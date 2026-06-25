<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Lead\customer_detail;
use App\Models\Lead\lead;
use App\Models\Lead\leads_log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EquipmentReportExport;


class EquipmentReportController extends Controller
{
   public function isLoggedIn()
   {
      $data = session('isLoggedIn');
      //print_r($data);      
      return $data;
   }
   public function equipment_report()
   {    
        // $equipment_report = DB::select("SELECT 
        //                                     del_orders.order_id as order_id,
        //                                     del_orders.lead_id as lead_id,
        //                                     del_orders.status as status,
        //                                     del_orders.DelDate as Del_Date,
        //                                     order_details.product_id as product_id,
        //                                     order_details.id as order_details_id,
        //                                     order_details.sale_rental as sale_rental,
        //                                     products.product_name as product_name
        //                                 FROM del_orders,order_details,products 
        //                                 where del_orders.order_id = order_details.order_id AND del_orders.status ='Delivered' AND order_details.product_id = products.id
        //                                         AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) ");
        $orderTypeNotIn = config('app.order_type');
        $equipment_report = DB::table('order_details')
                                ->join('del_orders','del_orders.order_id','=','order_details.order_id')        
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                ->join('products','products.id','=','order_details.product_id')
                                ->select(
                                    'del_orders.order_id as order_id',
                                    'del_orders.lead_id as lead_id',
                                    'del_orders.status as status',
                                    'del_orders.DelDate as Del_Date',
                                    'order_details.product_id as product_id',
                                    'order_details.id as order_details_id',
                                    'order_details.sale_rental as sale_rental',
                                    'products.product_name as product_name'
                                )
                                ->where('del_orders.status','Delivered')
                                ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                                ->when(session('city_based_access') == '1',function($query){
                                    $query->where('customer_details.citygroup',session('user_city'));
                                })
                                ->get();

        $data['equipment_report'] = json_decode(json_encode($equipment_report),true);
        
        $product_id = array();
        for ($i=0; $i <count($equipment_report) ; $i++) { 
            $prod_id = $data['equipment_report'][$i]['product_name'];
            array_push($product_id,$prod_id);
        }
        $data['product_count'] = array();
        for ($i=0; $i <count($equipment_report); $i++) 
        { 
            $temp = array();
            $products = array();
            $prod_id = $data['equipment_report'][$i]['product_name'];
            //$product_name = DB::select("SELECT product_name FROM products where id = $prod_id ");
            //$data['product_name'] = json_decode(json_encode($product_name),true);
            

            if(in_array($prod_id,array_column($data['product_count'],'prod_id')))
            {
                $prod_count_key = array_search($prod_id, array_column($data['product_count'], 'prod_id'));
                $data['product_count'][$prod_count_key]['count'] = $data['product_count'][$prod_count_key]['count'] + 1;
                if($data['equipment_report'][$i]['sale_rental']=='Sale')
                {
                    $data['product_count'][$prod_count_key]['sale_count'] = $data['product_count'][$prod_count_key]['sale_count'] + 1;
                }
                // elseif($data['equipment_report'][$i]['sale_rental']=='Rental')
                else
                {
                    //echo $data['product_count'][$prod_count_key]['rental_count'];
                    $data['product_count'][$prod_count_key]['rental_count'] = $data['product_count'][$prod_count_key]['rental_count'] + 1;
                }
                //$data['product_count'][$prod_count_key]['product_name'] = $data['product_count'][$prod_count_key]['producy'];
            }
            else
            {
                $temp['prod_id'] = $prod_id;
                $temp['count'] = 1;
                $temp['sale_count'] = 0;
                $temp['rental_count'] = 0;
                if ($data['equipment_report'][$i]['sale_rental']=='Sale') 
                {                    
                   // $data['product_count'][$prod_count_key]['sale_count'] = $data['product_count'][$prod_count_key]['sale_count'] + 1;
                   $temp['sale_count'] = 1;
                }
                elseif($data['equipment_report'][$i]['sale_rental']=='Rental')
                {
                    //$data['product_count'][$prod_count_key]['rental_count'] = $data['product_count'][$prod_count_key]['rental_count'] + 1;
                    $temp['rental_count'] = 1;
                }
                array_push($data['product_count'],$temp);
            }

        }
        //print_r($data);
        echo "<script>localStorage['filtered']='all';</script>";
        return view('Reports/equipment_reports',$data);
   }

   public function equipmentReportNew(Request $request)
   {
        $equipment_report = DB::table('order_details')
                                ->join('del_orders','del_orders.order_id','=','order_details.order_id')        
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                ->join('products','products.id','=','order_details.product_id')
                                ->select(
                                    'del_orders.order_id as order_id',
                                    'del_orders.lead_id as lead_id',
                                    'del_orders.status as status',
                                    'del_orders.DelDate as Del_Date',
                                    'order_details.product_id as product_id',
                                    'order_details.id as order_details_id',
                                    'order_details.sale_rental as sale_rental',
                                    'order_details.current_status as current_status',
                                    'products.product_name as product_name'
                                )
                                // ->where('del_orders.status','Delivered')
                                ->where('del_orders.deliverypickup','Delivery')
                                ->whereNotIn('del_orders.status',['Cancel'])
                                ->when($request->get('start_date')&&$request->get('end_date'),function($query)use($request){
                                    $query->whereBetween('order_details.creation_date',[$request->get('start_date'),$request->get('end_date')]);
                                })
                                ->when(session('city_based_access') == '1',function($query){
                                    $query->where('customer_details.citygroup',session('user_city'));
                                })
                                ->get(10);
        $equipment_report = $equipment_report->groupBy('product_id');
        foreach($equipment_report as $key=>$equipments)
        {
            $sale_rental = $equipments->groupBy('sale_rental');
            $equipment_report[$key]->name = $equipments[0]->product_name;
            // dd($sale_rental);
            if(isset($sale_rental['Rental']))
            {
                $equipment_report[$key]->rental = count($sale_rental['Rental']);
                $equipment_report[$key]->live = 0;
                $equipment_report[$key]->stop = 0;
                $live = array('Pending','Pending Renew','Renewed');
                $stop = array('CustStop','Pending Pickup','Pending PickUp','Picked Up','Picked UP');
                foreach($sale_rental['Rental'] as $k=>$v)
                {
                    if(in_array($v->current_status,$live))
                    {
                        $equipment_report[$key]->live++;
                    }
                    else if(in_array($v->current_status,$stop))
                    {
                        $equipment_report[$key]->stop++;
                    }
                }
            }
            else
            {
                $equipment_report[$key]->rental = 0;
                $equipment_report[$key]->live = 0;
                $equipment_report[$key]->stop = 0;
            }
            if(isset($sale_rental['Sale']))
            {
                $equipment_report[$key]->sale = count($sale_rental['Sale']);
            }
            else
            {
                $equipment_report[$key]->sale = 0;
            }
        }
        if($request->get('btn_submit') == 'Export')
        {
            ob_end_clean();
            ob_start();
            return Excel::download(new EquipmentReportExport($equipment_report), 'EquipmentReport.xlsx');
        }
        else
        {
            $equipment_report = $equipment_report->paginate(10);
            return view('Reports.equipment_report_new',compact('equipment_report'));
        }

   }

   public function filterEquipmentReport($day)
   {
      //--------leads management----------//
      $data['leads_report'] = array();
      $user_details = DB::select("SELECT * FROM user WHERE role = 'user'");
      $data['user_details'] = json_decode(json_encode($user_details),true);
      $i=0;
      $whereClause = "";
        if($day =='today')
		{
			$date = date('d-m-Y');
			// $whereClause = "DelDate = '$date'";
			$whereClause = "AND STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') = STR_TO_DATE('$date','%d-%m-%Y')";
			echo "<script>localStorage['filtered']='today';</script>";
		}
		elseif($day =='yesterday')
		{
			$prevDate = date('d-m-Y',strtotime("-1 days"));
			// $whereClause = "DelDate = '$prevDate'";
			$whereClause = "AND STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') = STR_TO_DATE('$prevDate','%d-%m-%Y')";
			echo "<script>localStorage['filtered']='yesterday';</script>";
		}
		elseif($day =='past_3_days')
		{
			$today = date('d-m-Y');
			$past_three_days = date('d-m-Y',strtotime("-2 days"));
			// $whereClause = "DelDate >= '$past_three_days'";
			$whereClause = "AND STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$past_three_days','%d-%m-%Y') AND STR_TO_DATE('$today','%d-%m-%Y')";
			echo "<script>localStorage['filtered']='past_3_days';</script>";
		}
		 elseif($day =='week')
		{
			$today = date('d-m-Y');
			$past_three_days = date('d-m-Y',strtotime("-6 days"));
			// $whereClause = "DelDate >= '$past_three_days'";
			$whereClause = "AND STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$past_three_days','%d-%m-%Y') AND STR_TO_DATE('$today','%d-%m-%Y')";
			echo "<script>localStorage['filtered']='week';</script>";
		}
		elseif($day =='month')
		{
			$month = date('m-Y');
			$start_date_temp = '01-'.$month;
			$start_date = date('d-m-Y',strtotime($start_date_temp));
			$end_date_temp = '31-'.$month;
			$end_date = date('d-m-Y',strtotime($end_date_temp));
			$whereClause = "AND STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y')";
		}
      elseif($day == "all")
      {         
         $whereClause = "";
         echo "<script>localStorage['filteredReport']='all';</script>";
      }

        // $orderTypeNotIn = "'".implode("','",$orderTypeNotIn)."'";
        // $equipment_report = DB::select("SELECT 
        //                                     del_orders.order_id as order_id,
        //                                     del_orders.lead_id as lead_id,
        //                                     del_orders.status as status,
        //                                     del_orders.DelDate as DelDate,
        //                                     order_details.product_id as product_id,
        //                                     order_details.id as order_details_id,
        //                                     order_details.sale_rental as sale_rental,
        //                                     products.product_name as product_name
        //                                 FROM del_orders,order_details,products 
        //                                 where del_orders.order_id = order_details.order_id AND del_orders.status ='Delivered' 
        //                                         AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)
        //                                         AND order_details.product_id = products.id $whereClause ");    
        $orderTypeNotIn = config('app.order_type');
        $equipment_report = DB::table('order_details')
                                ->join('del_orders','del_orders.order_id','=','order_details.order_id')        
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                ->join('products','products.id','=','order_details.product_id')
                                ->select(
                                    'del_orders.order_id as order_id',
                                    'del_orders.lead_id as lead_id',
                                    'del_orders.status as status',
                                    'del_orders.DelDate as Del_Date',
                                    'order_details.product_id as product_id',
                                    'order_details.id as order_details_id',
                                    'order_details.sale_rental as sale_rental',
                                    'products.product_name as product_name'
                                )
                                ->where('del_orders.status','Delivered')

                                ->when($day == 'today',function($query){
									$query->where('del_orders.DelDate',date('d-m-Y'));
								})
								->when($day == 'yesterday',function($query){
									$query->where('del_orders.DelDate',date('d-m-Y',strtotime("-1 days")));
								})
								->when($day == 'past_3_days',function($query){
									$start_date = date('d-m-Y',strtotime("-2 days"));
									$end_date = date('d-m-Y');
									$query->whereBetween(DB::raw("(STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$start_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$end_date','%d-%m-%Y'))")]);
								})
								->when($day == 'week',function($query){
									$start_date = date('d-m-Y',strtotime("-7 days"));
									$end_date = date('d-m-Y');
									$query->whereBetween(DB::raw("(STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$start_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$end_date','%d-%m-%Y'))")]);
								})
								->when($day == 'month',function($query){
									$month = date('m-Y');
									$start_date_temp = '01-'.$month;
									$start_date = date('d-m-Y',strtotime($start_date_temp));
									$end_date_temp = '31-'.$month;
									$end_date = date('d-m-Y',strtotime($end_date_temp));
									$query->whereBetween(DB::raw("(STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$start_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$end_date','%d-%m-%Y'))")]);
								})
								->when(session('city_based_access') == '1',function($query){
									$query->where('customer_details.citygroup',session('user_city'));
								})
                                ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                                ->get();

        $data['equipment_report'] = json_decode(json_encode($equipment_report),true);
        
        $product_id = array();
        for ($i=0; $i <count($equipment_report) ; $i++) { 
            $prod_id = $data['equipment_report'][$i]['product_name'];
            array_push($product_id,$prod_id);
        }
        $data['product_count'] = array();
        for ($i=0; $i <count($equipment_report); $i++) 
        { 
            $temp = array();
            $products = array();
            $prod_id = $data['equipment_report'][$i]['product_name'];
            //$product_name = DB::select("SELECT product_name FROM products where id = $prod_id ");
            //$data['product_name'] = json_decode(json_encode($product_name),true);
            if(in_array($prod_id,array_column($data['product_count'],'prod_id')))
            {
                $prod_count_key = array_search($prod_id, array_column($data['product_count'], 'prod_id'));
                $data['product_count'][$prod_count_key]['count'] = $data['product_count'][$prod_count_key]['count'] + 1;
                //$data['product_count'][$prod_count_key]['product_name'] = $data['product_count'][$prod_count_key]['producy'];
                if($data['equipment_report'][$i]['sale_rental']=='Sale')
                {
                    $data['product_count'][$prod_count_key]['sale_count'] = $data['product_count'][$prod_count_key]['sale_count'] + 1;
                }
                // elseif($data['equipment_report'][$i]['sale_rental']=='Rental')
                else
                {
                    //echo $data['product_count'][$prod_count_key]['rental_count'];
                    $data['product_count'][$prod_count_key]['rental_count'] = $data['product_count'][$prod_count_key]['rental_count'] + 1;
                }
            }
            else
            {
                $temp['prod_id'] = $prod_id;
                $temp['count'] = 1;
                $temp['sale_count'] = 0;
                $temp['rental_count'] = 0;
                if ($data['equipment_report'][$i]['sale_rental']=='Sale') 
                {                    
                    //$data['product_count'][$prod_count_key]['sale_count'] = $data['product_count'][$prod_count_key]['sale_count'] + 1;
                   $temp['sale_count'] = 1;
                }
                elseif($data['equipment_report'][$i]['sale_rental']=='Rental')
                {
                    //$data['product_count'][$prod_count_key]['rental_count'] = $data['product_count'][$prod_count_key]['rental_count'] + 1;
                    $temp['rental_count'] = 1;
                }
                array_push($data['product_count'],$temp);
            }

        }

        return view('Reports/equipment_reports',$data);
      
   }
}
