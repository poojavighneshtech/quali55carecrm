<?php

namespace App\Console\Commands;

use App\Models\UserRegister;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\ComplaintManagement\ComplaintController;
use App\Http\Controllers\Leads\LeadController;
use App\Http\Controllers\Expense\ExpenseController;

class whatsappRenewal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapprenewal:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a daily mail to users about leads information';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $cities =  array();
        if(session('city_based_access') == '1')
        {
            $cities[0] = (object)(['city'=>session('user_city')]);
        }
        else{
            $cities = DB::table('customer_details')->distinct('citygroup')->whereNotNull('citygroup')->orderBy('citygroup','ASC')->get('citygroup');
        }

        $leadUsers = DB::table('user')->whereIn('role',['user','admin','superuser'])->orderBy('username')->get();
        $products = DB::table('products')->where('flag','Active')->get();
        $dateFilter = ['Today','Tomorrow','Overdue','3 Days','All'];
        $startDate = Carbon::today()->toDateString();
        $endDate = Carbon::today()->toDateString();
        $dateFilterVal = "Today";
        if(!empty($request->get('start_date')) && !empty($request->get('end_date')) || $request->get('shows_only_stops')=='on' || !empty($request->get('customer_search'))){
            $dateFilterVal ='All';
        }
        $orderTypeNotIn = config('app.order_type');
        $renewPickupData = DB::table('order_details')
                                ->select('customer_details.*','order_details.*','user.username','order_details.id as order_details_id','vendor_details.registered_name as vendor_name','del_orders.DelDate','products.product_name','leads.patient_name')
                                ->join('del_orders','order_details.order_id','=','del_orders.order_id')
                                ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                                ->join('products','order_details.product_id','=','products.id')
                                ->join('vendor_details','order_details.vendor_id','=','vendor_details.id')
                                ->join('leads','del_orders.lead_id','=','leads.id')
                                ->join('user','leads.lead_owner','=','user.id')
                                ->when($request->get('customer_search'),function($query) use($request){
                                    $query->where(function($q)use($request) {
                                        $q->where('customer_details.customer_name','LIKE','%'.$request->get('customer_search').'%');
                                        $q->orWhere('customer_details.primary_contact_no','LIKE','%'.$request->get('customer_search').'%');
                                        $q->orWhere('customer_details.address_line_1','LIKE','%'.$request->get('customer_search').'%');
                                        $q->orWhere('customer_details.address_line_2','LIKE','%'.$request->get('customer_search').'%');
                                        $q->orWhere('customer_details.area','LIKE','%'.$request->get('customer_search').'%');
                                        $q->orWhere('customer_details.location','LIKE','%'.$request->get('customer_search').'%');
                                        $q->orWhere('leads.patient_name','LIKE','%'.$request->get('customer_search').'%');
                                    });
                                })
                                ->when($request->get('city_filter') && $request->get('city_filter')!='All',function($query) use($request){
                                    $query->where('customer_details.citygroup','LIKE','%'.$request->get('city_filter').'%');
                                })
                                ->when(session('city_based_access')=='1', function($query){
                                    $query->where('customer_details.citygroup',session('user_city'));
                                })
                                ->when($request->get('customer_type') && $request->get('customer_type')!='All',function($query) use($request){
                                    $query->where('customer_details.customer_type',$request->get('customer_type'));
                                })
                                ->when($request->get('order_id'),function($query)use($request){
                                    $query->where('del_orders.order_id',$request->get('order_id'));
                                })
                                ->when($request->get('lead_user') && $request->get('lead_user')!="All",function($query) use($request){
                                    $query->where('leads.lead_owner',$request->get('lead_user'));
                                })
                                ->when($dateFilterVal && $dateFilterVal!="All" , function($query)use($request){
                                    $filter = "Today";
                                    if($filter=='Today'){
                                        $date = Carbon::today()->toDateString();
                                        $query->where('order_details.pickup_date',$date);
                                    }
                                })
                                ->when($request->get('start_date') && $request->get('end_date'),function($query) use($request){
                                    $query->whereBetween('order_details.pickup_date',[$request->get('start_date'),$request->get('end_date')]);
                                })
                                ->when($request->get('stopped_product_id'),function($query) use($request){
                                    $query->where('order_details.product_id',$request->get('stopped_product_id'))
                                    ->where('order_details.current_status','CustStop');
                                })
                                ->when($request->get('shows_only_stops')=='on',function($query) use($request){
                                    $query->where('order_details.current_status','CustStop');
                                })
                                ->when($request->get('order_product_type')=='Live',function($query) use($request){
                                    $query->whereNotIn('order_details.current_status',['CustStop']);
                                })
                                ->when($request->get('order_product_type')=='Stop',function($query) use($request){
                                    $query->where('order_details.current_status','CustStop');
                                })
                                ->when($request->get('filter_product'),function($query)use($request){
                                    $query->whereIn('order_details.product_id',$request->get('filter_product'));
                                })
                                ->where('order_details.sale_rental','Rental')
                                ->whereNotIn('del_orders.status',['Cancel','Rejected','Cust Rejected'])
                                ->whereIn('order_details.current_status',['Pending','Pending Renew','Renewed','Renewed Online','CustStop'])
                                ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                                ->orderBy('order_details.pickup_date','ASC')
                                ->get();
                                // ->groupBy('customer_id')
                                // ->paginate(10);

        foreach($renewPickupData as $key=>$data)
        {
            if(DB::table('cr_dr_note')->where('order_details_id',$data->order_details_id)->exists())
            {
                $renewPickupData[$key]->product_rent = $this->fetchCrDrData($data->order_details_id,'R');
                $renewPickupData[$key]->product_deposite = $this->fetchCrDrData($data->order_details_id,'D');
                $renewPickupData[$key]->transport = $this->fetchCrDrData($data->order_details_id,'T');
            }
            
        }
        
        if($request->get('submit')=='export_excel')
        {
            ob_end_clean(); // this
            ob_start(); // and this
            return Excel::download(new RenewalPickupExportTest($renewPickupData), 'renewal.xls');
        }
        
        $totalProducts =  $renewPickupData->count();
        $totalCustomers = $renewPickupData->groupBy('customer_id')->count();
        $renewPickupData = $renewPickupData->groupBy('customer_id');
        //dd($renewPickupData);
        //product to month wise data
        $totalRent = [];
        $totalRentHeador = 0;
        foreach ($renewPickupData as $key => $orderData) 
        {
            foreach ($orderData as $key1 => $productData) 
            {
                $today = Carbon::today()->toDateString();
                $monthCount = $this->getBillingPeriod($productData->pickup_date,$productData->billing_unit,$today);
                // $monthCount = Carbon::parse($productData->pickup_date)->diffInMonths($today);
                // $currentRenewDate = Carbon::parse($productData->pickup_date)->addMonths($monthCount);
                // if(Carbon::parse($currentRenewDate)->diffInDays($today)>0){
                //     $monthCount = $monthCount+1;
                // }
                // if(Carbon::parse($productData->pickup_date)->diffInDays($today)==0)
                // {
                //     $monthCount = 1;
                // }
                $productMonthRent = $monthCount*$productData->product_rent;
                $totalRent[$key][$key1]['month_count'] = $monthCount;
                $totalRent[$key][$key1]['total_rent'] = $productMonthRent;
                $totalRentHeador+=$productMonthRent;
            }
        }
        $renewPickupData = $renewPickupData->paginate(10);
        
        $stoppedProducts = DB::table('order_details')
                        ->join('products','order_details.product_id','=','products.id')
                        ->where('current_status','CustStop')
                        ->get();
        $stoppedProductsCount = $stoppedProducts->count();
        $stoppedProducts = $stoppedProducts->groupBy('product_id');
        
        foreach ($renewPickupData as $key => $renewPickup) {
            if($renewPickup[0]->current_status!='CustStop')
            {
                $customer_name = $renewPickup[0]->customer_name;
                $mobile_no = $renewPickup[0]->primary_contact_no;
                $curl = curl_init();
                
                curl_setopt_array($curl, array(
                  CURLOPT_URL => 'https://api.interakt.ai/v1/public/message/',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS =>'{
                    "countryCode": "+91",
                    "phoneNumber": "8552944963",
                    "callbackData": "some text here",
                    "type": "Template",
                    "template": {
                        "name": "copy_renewals_demo",
                        "languageCode": "en",
                        "headerValues": [
                            "header_variable_value"
                        ],
                        "bodyValues": [
                            "'.$customer_name.'"
                        ]
                    }
                }',
                  CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic {{RmpZU0VkRTQ2ZVdrUXBERWd3b0VyMlUyYTB2T0VJaWlvUTg4VUt2Z2FnRTo=}}',
                    'Content-Type: application/json'
                  ),
                ));
                
                $response = curl_exec($curl);
                
                curl_close($curl);
                // echo $response;
            }
            
        }
        return view('RenewalPickup.renewal_pickup_test1',compact('renewPickupData','leadUsers','dateFilter','totalRent','dateFilterVal','totalProducts','totalCustomers','totalRentHeador','products'),
                                                        compact('stoppedProducts','stoppedProductsCount','cities'));

    }
}
