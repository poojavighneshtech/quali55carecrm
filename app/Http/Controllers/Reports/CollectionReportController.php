<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Lead\customer_detail;
use App\Models\Lead\lead;
use App\Models\Lead\leads_log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CollectionReportExport;
use App\Http\Controllers\RenewalPickup\RenewalPickupController;

class CollectionReportController extends Controller
{
    public function collectionReport(Request $request)
    {
        // dd("Inside It");
        // $start_date = date('Y-m-d',strtotime("-1 days"));
        // $end_date = date('Y-m-d');
        $start_date = Carbon::now()->startOfMonth()->toDateString();
        $end_date = Carbon::now()->toDateString();
        $whereCondition = [];
        $customer_name = $request->get('filter_customer_name');
        if(isset($customer_name)){
            $whereCondition1 = ['del_orders.shipping_first_name','LIKE','%'.$customer_name.'%'];
            array_push($whereCondition,$whereCondition1);
        }
        $customer_contact = $request->get('filter_contact_no');
        if(isset($customer_contact)){
            $whereCondition1 = ['del_orders.mobileno','LIKE','%'.$customer_contact.'%'];
            array_push($whereCondition,$whereCondition1);
        }
        $start_date1 = $request->get('filter_from_date');
		$end_date1 = $request->get('filter_end_date');
        if(isset($start_date1) && isset($end_date1)){
            $start_date = $start_date1;
            $end_date = $end_date1;
        }
        $page_no = 1;
        $page = $request->get('page');
        if(isset($page) && $page>1){
            $page_no = $page;
        }
        else{
            $page_no = 1;
        }
        // if($end_date >Carbon::today()->toDateString())
        //         {
        //             $end_date = Carbon::today()->toDateString();
        //         }
        $total_renewed_count_total = 0;
        $orderTypeNotIn = config('app.order_type');
        $renewed_count = DB::table('renewals')
                            ->join('del_orders','renewals.collection_order_id','=','del_orders.order_id')
                            ->join('leads','renewals.lead_id','=','leads.id')
                            ->join('customer_details','leads.customer_id','=','customer_details.cust_id')
                            ->join('user','leads.lead_owner','=','user.id')
                            ->join('products','renewals.product_id','=','products.id')
                            ->select(
                                'del_orders.shipping_first_name as customer_name',
                                'del_orders.mobileno as contact_no',
                                'del_orders.order_id',
                                'products.product_name as product_name',
                                'renewals.payment_mode as payment_mode',
                                'renewals.cash_amount as cash_amount',
                                'renewals.online_amount as online_amount',
                                'renewals.start_date as date',
                                'renewals.created_at as created_at',
                                'user.username as lead_owner'
                            )
                            ->where($whereCondition)
                            ->whereBetween('renewals.start_date',[$start_date,$end_date])                            
                            ->when(session('city_based_access') == '1',function($query){
                                $query->where('customer_details.citygroup',session('user_city'));
                            })
                            ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                            ->orderBy('renewals.start_date','Asc')
                            ->get();
        if(DB::table('cr_dr_note')->whereIn('order_id',$renewed_count->pluck('order_id')->toArray())->where('intype','RE')->where('crdrtype','Cr')->exists()){
            $totalcreditrecords = DB::table('cr_dr_note')->whereIn('order_id',$renewed_count->pluck('order_id')->toArray())->where('intype','RE')->where('crdrtype','Cr')->get()->sum('amount');
        }else{
            $totalcreditrecords = 0;
        }
        if(DB::table('cr_dr_note')->whereIn('order_id',$renewed_count->pluck('order_id')->toArray())->where('intype','RE')->where('crdrtype','Dr')->exists()){
            $totaldebitrecords = DB::table('cr_dr_note')->whereIn('order_id',$renewed_count->pluck('order_id')->toArray())->where('intype','RE')->where('crdrtype','Dr')->get()->sum('amount');
        }else{
            $totaldebitrecords = 0;
        }
        $total_renewed_count_total = $total_renewed_count_total + $renewed_count->sum('cash_amount') + $renewed_count->sum('online_amount') - $totalcreditrecords + $totaldebitrecords;
        
        $overdue_count = DB::table('order_details')
                            ->join('del_orders','order_details.order_id','=','del_orders.order_id')
                            ->join('leads','del_orders.lead_id','=','leads.id')
                            ->join('customer_details','leads.customer_id','=','customer_details.cust_id')
                            ->join('user','leads.lead_owner','=','user.id')
                            ->join('products','order_details.product_id','=','products.id')
                            ->select(
                                'del_orders.shipping_first_name as customer_name',
                                'del_orders.mobileno as contact_no',
                                'products.product_name as product_name',
                                'order_details.product_rent as product_rent_amount',
                                'order_details.pickup_date as date',
                                'order_details.created_at as created_at',
                                'user.username as lead_owner',
                                'order_details.id as order_details_id'
                            )
                            ->where($whereCondition)
                            ->whereNotIn('del_orders.status',['Cancel','Cust Rejected','Rejected'])
                            ->where('order_details.sale_rental','Rental')
                            ->whereIn('order_details.current_status',['Pending','Renewed'])
                            // ->whereBetween('order_details.pickup_date',[$start_date,$end_date])
                            ->when($start_date,function($query)use($start_date,$end_date){
                                // $query->where('order_details.pickup_date','<',$start_date);
                                $query->where('order_details.pickup_date','<=',$end_date);
                            })
			                ->when(session('city_based_access') == '1',function($query){
                                $query->where('customer_details.citygroup',session('user_city'));
                            })
                            
                            ->orderBy('order_details.pickup_date','Asc')  
                            ->get();
        foreach($overdue_count as $key=>$value)
        {
            $overdue_count[$key]->product_rent_amount = RenewalPickupController::fetchCrDrData($value->order_details_id,'R');
        }

        $total_overdue_count_total = $overdue_count->sum('product_rent_amount');
        
        $count_array = collect($renewed_count->merge($overdue_count))->sortBy('date');

        // dd($final_array);
        if($request->get('btn_submit') != null || $request->get('btn_submit') != "" )
        {
            if($request->get('btn_submit') == "Search")
            {
                $count_array = $count_array->paginate(10,null,$page_no);
                $filter_arr = 
                    [
                        "cust_name"=>$customer_name,
                        "cust_no"=>$customer_contact,
                        "from_date"=>$start_date,
                        "end_date"=>$end_date,
                    ];
                    // dd($start_date,$end_date);
                return view('Reports/collection-report',compact('count_array','total_renewed_count_total','total_overdue_count_total','filter_arr'));
            }
            elseif($request->get('btn_submit') == "Export")
            {
                // dd("Export");
                // dd($count_array);
                $count_array = $count_array->toArray();
                ob_end_clean(); // this
                ob_start(); // and this
                return Excel::download(new CollectionReportExport($count_array,$total_renewed_count_total,$total_overdue_count_total), 'Collection Report.xlsx');
            }
            else
            {
                return redirect()->back()->with('message','Something Went Wrong');
            }
        }
        else
        {
            $count_array = $count_array->paginate(10,null,$page_no);
            $filter_arr = 
                [
                    "cust_name"=>$customer_name,
                    "cust_no"=>$customer_contact,
                    "from_date"=>$start_date,
                    "end_date"=>$end_date,
                ];
                // dd($start_date,$end_date);
            return view('Reports/collection-report',compact('count_array','total_renewed_count_total','total_overdue_count_total','filter_arr'));
        }
    }
}


?>