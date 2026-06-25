<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;

use App\Models\Lead\customer_detail;
use App\Models\Lead\lead;
use App\Models\DelOrders;
use App\Models\OrderDetails;
use App\Models\Renewal;
use App\Models\Pickup;
use App\Models\VendorRegister;
use App\Models\MasterProduct;
use App\Models\VendorWarehouse;
use App\Models\VendorProducts;
use App\Exports\MisExport;
use App\Models\Lead\leads_log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\RenewalPickup\RenewalPickupController;

class MISReportController extends Controller
{
   public function isLoggedIn()
   {
      $data = session('isLoggedIn');
      return $data;
   }
   public function mis_reports(Request $request)
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
         $url = url('/');
         return redirect()->to($url);
      }
      $whereClause = array();
      $whereClauseM = array();
      
      // $get_min_date = OrderDetails::min('creation_date');
      // $get_max_date = OrderDetails::max('creation_date');
      $get_max_date = date('Y-m-d');
      $get_min_date = date('Y-m-d',strtotime("-1 years"));
      $filter_data['filter_from_date'] = $get_min_date;
      $filter_data['filter_end_date'] = $get_max_date;
      $filter_data['filter_customer_name'] = null;
      $filter_data['filter_contact_no'] = null;
      // dd($get_min_date, $get_max_date);
      $customer_name = $request->get('filter_customer_name');
		if(isset($customer_name)){
			$whereCondition1 = ['customer_details.customer_name','LIKE','%'.$customer_name.'%'];
			array_push($whereClause,$whereCondition1);
         $whereConditionM1 = ['mis_records.patient_name','LIKE','%'.$customer_name.'%'];
			array_push($whereClauseM,$whereConditionM1);
         $filter_data['filter_customer_name'] = $customer_name;
		}

		$customer_contact = $request->get('filter_contact_no');
		if(isset($customer_contact)) {
			$whereCondition2 = ['customer_details.primary_contact_no','=',$customer_contact];
			array_push($whereClause,$whereCondition2);
         $whereConditionM2 = ['mis_records.contact_no','=',$customer_contact];
			array_push($whereClauseM,$whereConditionM2);
         $filter_data['filter_contact_no'] = $customer_contact;
		}

      $from_date = $request->get('filter_from_date');
		$end_date = $request->get('filter_end_date');
      if(isset($from_date) && isset($end_date)){
         $get_min_date = $from_date;
         $get_max_date = $end_date;
         
         $filter_data['filter_from_date'] = $get_min_date;
         $filter_data['filter_end_date'] = $get_max_date;
      }
      
      // DB::enableQueryLog();
      $orderTypeNotIn = config('app.order_type');
      $mis_report_details = DB::table('order_details')
                                 ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                                 ->join('del_orders','order_details.order_id','=','del_orders.order_id')
                                 ->join('leads','del_orders.lead_id','=','leads.id')
                                 ->join('user','leads.lead_owner','=','user.id')
                                 ->join('vendor_details','order_details.vendor_id','=','vendor_details.id')
                                 ->join('products','order_details.product_id','=','products.id')
                                 ->join('vendor_warehouse','order_details.vendor_warehouse_id','=','vendor_warehouse.id')
                                 ->join('vendor_products','order_details.vendor_product_id','=','vendor_products.id')
                                 ->select('customer_details.citygroup as city',
                                          'del_orders.DelDate as date',
                                          'customer_details.customer_name as customer_name',
                                          'customer_details.primary_contact_no as contact_number',
                                          'products.product_name as product_name',
                                          'order_details.id as order_details_id',
                                          'order_details.product_qty as product_qty',
                                          'order_details.creation_date as start_date',
                                          'order_details.pickup_date as renewal_date',
                                          // 'pickups.pickup_date as stop_date',
                                          'order_details.current_status as status',
                                          'order_details.product_rent as rent_per_unit',
                                          'order_details.product_deposite as deposit_taken',
                                          // 'pickups.cash_amount as pickup_cash_amount',
                                          // 'pickups.online_amount as pickup_online_amount',
                                          'order_details.transport as transport',
                                          'vendor_products.product_rent_approved as vendor_rent',
                                          'user.username as owner',
                                          'customer_details.address_line_1 as address_line_1',
                                          'customer_details.address_line_2 as address_line_2',
                                          'customer_details.area as area',
                                          'customer_details.landmark as landmark',
                                          'customer_details.pincode as pincode',
                                          'customer_details.location as location',
                                          'leads.lead_source as source',
                                          'vendor_details.registered_name as vendor_name')
                                 ->where($whereClause)
                                 ->when(session('city_based_access') == '1',function($query){
                                    $query->where('customer_details.citygroup',session('user_city'));
                                 })
                                 ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                                 ->whereBetween('order_details.creation_date',[$get_min_date,$get_max_date])
                                 ->orderBy('order_details.creation_date','DESC')
                                 ->get();
      foreach($mis_report_details as $key=>$value)
      {
         $mis_report_details[$key]->rent_per_unit = RenewalPickupController::fetchCrDrData($value->order_details_id,'R');
         $mis_report_details[$key]->deposit_taken = RenewalPickupController::fetchCrDrData($value->order_details_id,'D');
         $mis_report_details[$key]->transport = RenewalPickupController::fetchCrDrData($value->order_details_id,'T');
      }                                 
                                 // ->paginate(10);
      $old_mis_report_details = DB::table('mis_records')
                                    ->join('products','mis_records.equipment_taken','=','products.id')
                                    ->join('vendor_details','mis_records.vendor_id','=','vendor_details.id')
                                    ->join('user','mis_records.lead_owner','=','user.id')
                                    ->select(
                                       'mis_records.city as city',
                                       'mis_records.date as date',
                                       'mis_records.patient_name as customer_name',
                                       'mis_records.contact_no as contact_number',
                                       'products.product_name as product_name',
                                       'mis_records.qty as product_qty',
                                       'mis_records.id as order_details_id',
                                       'mis_records.start_date as start_date',
                                       'mis_records.renewal_date as renewal_date',
                                       'mis_records.stop_date as stop_date',
                                       'mis_records.status as status',
                                       'mis_records.rent_per_unit as rent_per_unit',
                                       'mis_records.deposit_taken as deposit_taken',
                                       'mis_records.deposit_return as deposit_return',
                                       'mis_records.deposit_outstanding as deposit_outstanding',
                                       'mis_records.transport as transport',
                                       'mis_records.total as total',
                                       'mis_records.paid as paid',
                                       'mis_records.outstanding as outstanding',
                                       'mis_records.outstanding_last_year as outstanding_last_year',
                                       'mis_records.payment_received as payment_received',
                                       'mis_records.net_outstanding as net_outstanding',
                                       'mis_records.how_many_months as how_many_months',
                                       'mis_records.apr as apr',
                                       'mis_records.may as may',
                                       'mis_records.jun as jun',
                                       'mis_records.july as july',
                                       'mis_records.aug as aug',
                                       'mis_records.sep as sep',
                                       'mis_records.oct as oct',
                                       'mis_records.nov as nov',
                                       'mis_records.dece as dece',
                                       'mis_records.jan as jan',
                                       'mis_records.feb as feb',
                                       'mis_records.march as march',
                                       'mis_records.no_of_month as no_of_month',
                                       'mis_records.rental as rental',
                                       'mis_records.rental_collected as rental_collected',
                                       'mis_records.vendor as vendor_rent',
                                       'mis_records.net_rental as net_rental',
                                       'mis_records.net_rental_outstanding as net_rental_outstanding',
                                       'user.username as lead_owner',
                                       'mis_records.address as address',
                                       'mis_records.location as location',
                                       'mis_records.lead_source as lead_source',
                                       'vendor_details.registered_name as vendor_name',
                                       'mis_records.payment_mode as payment_mode',
                                    )
                                    ->where($whereClauseM)
                                    ->whereBetween('mis_records.date',[$get_min_date,$get_max_date])
                                    ->when(session('city_based_access') == '1',function($query){
                                       $query->where('mis_records.city',session('user_city'));
                                    })
                                    ->orderBy('mis_records.date','DESC')
                                    ->get();
               
      if($request->get('btn_submit') == 'submit')
      {
         
         // $mis_report_details = $mis_report_details->union($old_mis_report_details)->paginate(10);
         $mis_report_details = $mis_report_details->merge($old_mis_report_details)->paginate(10);
         
      }
      else if($request->get('btn_submit') == 'export_excel')
      {
         // $mis_report_details = $mis_report_details->get();
         $mis_report_details = $mis_report_details->merge($old_mis_report_details)->paginate(10000000000);
      }
      else
      {
         $mis_report_details = $mis_report_details->merge($old_mis_report_details)->paginate(10);
      }

      // dd($mis_report_details);
      // dd(DB::getQueryLog());
      $decoded_data = json_decode(json_encode(json_decode(json_encode($mis_report_details),true)),true);
      // dd($decoded_data);
      $pickup_data = array();
      $renewal_data = array();
      foreach ($decoded_data['data'] as $key => $val)
      {
         if($val['status'] == "Pending Pickup" || $val['status'] == "Picked up")
         {
            // echo "in if";
            $temp_array = array();
            $temp_array['deposite_return'] = $val['deposit_taken'];
            $temp_array['deposite_outstanding'] = 0;
            $pickup_details = DB::table('order_details')
                                 ->join('pickups','order_details.id','=','pickups.order_details_id')
                                 ->join('del_orders','pickups.del_order_id','=','del_orders.order_id')
                                 ->select('del_orders.DelDate as stop_date')
                                 ->where('order_details.id',$val['order_details_id'])
                                 ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                                 ->get()
                                 ->toArray();
            if(isset($pickup_details[0]))
            {
               $temp_array['stop_date'] = $pickup_details[0]->stop_date;
            }
            else
            {
               $temp_array['stop_date'] = "Deleted Records";
            }
            $temp_array['status'] = "Stop";
            $pickup_data[$key] = $temp_array;
         }
         else
         {
            $temp_array = array();
            $temp_array['deposite_return'] = 0;
            $temp_array['deposite_outstanding'] = $val['deposit_taken'];
            $temp_array['stop_date'] = "-";
            $temp_array['status'] = "Live";
            $pickup_data[$key] = $temp_array;
         }
         $temp_date = date('Y',strtotime('-1 years')).'-04-01';
         $start_date = date('Y-m-d',strtotime($temp_date));
         // echo $start_date;
         // DB::enableQueryLog();
         $temp_array_renew = array();
         $renewal_details = Renewal::where([['order_details_id','=',$val['order_details_id']],['start_date','>=',$temp_date]])->get()->toArray();
         if(isset($renewal_details[0]))
         {
            foreach($renewal_details as $key1 => $value)
            {
                $key1 = date('m',strtotime($value['start_date']));
                $temp_array_renew[$key1] = "1";
            }            
         }
         $renewal_data[$key] = $temp_array_renew;
         // dd(DB::getQueryLog());
         // select * from `renewals` where (`order_details_id` = 526 and `start_date` >= '2021-04-01')
        
      }
      if($request->get('btn_submit') == 'export_excel')
      {
         // $mis_report_details = $mis_report_details->get();
         // $mis_report_details = $mis_report_details->toArray();
         // $jsonDecoded = json_decode(json_encode($mis_report_details),true);
         $jsonDecoded = $mis_report_details;
         $jsonDecoded1 = $renewal_data;
         $jsonDecoded2 = $pickup_data;
         //print_r($jsonDecoded);
         ob_end_clean(); // this
         ob_start(); // and this
         return Excel::download(new MisExport($jsonDecoded,$jsonDecoded1,$jsonDecoded2), 'MIS'.date('Y-m-d H:i:s').'.xlsx');
      }
      // dd($renewal_data);
      // dd($pickup_data);
      // dd($mis_report_details);
      // dd($mis_report_details,$pickup_data,$renewal_data,$filter_data);
      return view('Reports/mis_reports',compact('mis_report_details','pickup_data','renewal_data','filter_data'));
   }
   // public function mis_reports(Request $request)
   // {
   //    $isLoggedIn = $this->isLoggedIn();
   //    if($isLoggedIn == 'false')
   //    {
   //       $url = url('/');
   //       return redirect()->to($url);
   //    }
   //    $whereClause = array();
      
   //    // $get_min_date = OrderDetails::min('creation_date');
   //    // $get_max_date = OrderDetails::max('creation_date');
   //    $get_max_date = date('Y-m-d');
   //    $get_min_date = date('Y-m-d',strtotime("-1 years"));
   //    $filter_data['filter_from_date'] = $get_min_date;
   //    $filter_data['filter_end_date'] = $get_max_date;
   //    $filter_data['filter_customer_name'] = null;
   //    $filter_data['filter_contact_no'] = null;
   //    // dd($get_min_date, $get_max_date);
   //    $customer_name = $request->get('filter_customer_name');
	// 	if(isset($customer_name)){
	// 		$whereCondition1 = ['customer_details.customer_name','LIKE','%'.$customer_name.'%'];
	// 		array_push($whereClause,$whereCondition1);
   //       $filter_data['filter_customer_name'] = $customer_name;
	// 	}

	// 	$customer_contact = $request->get('filter_contact_no');
	// 	if(isset($customer_contact)) {
	// 		$whereCondition2 = ['customer_details.primary_contact_no','=',$customer_contact];
	// 		array_push($whereClause,$whereCondition2);
   //       $filter_data['filter_contact_no'] = $customer_contact;
	// 	}

   //    $from_date = $request->get('filter_from_date');
	// 	$end_date = $request->get('filter_end_date');
   //    if(isset($from_date) && isset($end_date)){
   //       $get_min_date = $from_date;
   //       $get_max_date = $end_date;
         
   //       $filter_data['filter_from_date'] = $get_min_date;
   //       $filter_data['filter_end_date'] = $get_max_date;
   //    }
      
   //    // DB::enableQueryLog();
   //    $mis_report_details = DB::table('order_details')
   //                               ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
   //                               ->join('del_orders','order_details.order_id','=','del_orders.order_id')
   //                               ->join('leads','del_orders.lead_id','=','leads.id')
   //                               ->join('user','leads.lead_owner','=','user.id')
   //                               ->join('vendor_details','order_details.vendor_id','=','vendor_details.id')
   //                               ->join('products','order_details.product_id','=','products.id')
   //                               ->join('vendor_warehouse','order_details.vendor_warehouse_id','=','vendor_warehouse.id')
   //                               ->join('vendor_products','order_details.vendor_product_id','=','vendor_products.id')
   //                               ->select('customer_details.citygroup as city',
   //                                        'del_orders.DelDate as date',
   //                                        'customer_details.customer_name as customer_name',
   //                                        'customer_details.primary_contact_no as contact_number',
   //                                        'products.product_name as product_name',
   //                                        'order_details.id as order_details_id',
   //                                        'order_details.creation_date as start_date',
   //                                        'order_details.pickup_date as renewal_date',
   //                                        // 'pickups.pickup_date as stop_date',
   //                                        'order_details.current_status as status',
   //                                        'order_details.product_rent as rent_per_unit',
   //                                        'order_details.product_deposite as deposit_taken',
   //                                        // 'pickups.cash_amount as pickup_cash_amount',
   //                                        // 'pickups.online_amount as pickup_online_amount',
   //                                        'order_details.transport as transport',
   //                                        'vendor_products.product_rent_approved as vendor_rent',
   //                                        'user.username as owner',
   //                                        'customer_details.address_line_1 as address_line_1',
   //                                        'customer_details.address_line_2 as address_line_2',
   //                                        'customer_details.area as area',
   //                                        'customer_details.landmark as landmark',
   //                                        'customer_details.pincode as pincode',
   //                                        'customer_details.location as location',
   //                                        'leads.lead_source as source',
   //                                        'vendor_details.registered_name as vendor_name')
   //                               ->where($whereClause)
   //                               ->whereBetween('order_details.creation_date',[$get_min_date,$get_max_date])
   //                               ->orderBy('order_details.creation_date','DESC');
   //                               // ->paginate(10);
   //    if($request->get('btn_submit') == 'submit')
   //    {
   //       $mis_report_details = $mis_report_details->paginate(10);
   //    }
   //    else if($request->get('btn_submit') == 'export_excel')
   //    {
   //       // $mis_report_details = $mis_report_details->get();
   //       $mis_report_details = $mis_report_details->paginate(10000000000);
   //    }
   //    else
   //    {
   //       $mis_report_details = $mis_report_details->paginate(10);
   //    }

   //    // dd($mis_report_details);
   //    // dd(DB::getQueryLog());
   //    $decoded_data = json_decode(json_encode(json_decode(json_encode($mis_report_details),true)),true);
   //    // dd($decoded_data);
   //    $pickup_data = array();
   //    $renewal_data = array();
   //    foreach ($decoded_data['data'] as $key => $val)
   //    {
   //       if($val['status'] == "Pending Pickup" || $val['status'] == "Picked up")
   //       {
   //          // echo "in if";
   //          $temp_array = array();
   //          $temp_array['deposite_return'] = $val['deposit_taken'];
   //          $temp_array['deposite_outstanding'] = 0;
   //          $pickup_details = DB::table('order_details')
   //                               ->join('pickups','order_details.id','=','pickups.order_details_id')
   //                               ->join('del_orders','pickups.del_order_id','=','del_orders.order_id')
   //                               ->select('del_orders.DelDate as stop_date')
   //                               ->where('order_details.id',$val['order_details_id'])
   //                               ->get()
   //                               ->toArray();
   //          if(isset($pickup_details[0]))
   //          {
   //             $temp_array['stop_date'] = $pickup_details[0]->stop_date;
   //          }
   //          else
   //          {
   //             $temp_array['stop_date'] = "Deleted Records";
   //          }
   //          $temp_array['status'] = "Stop";
   //          array_push($pickup_data,$temp_array);
   //       }
   //       else
   //       {
   //          $temp_array = array();
   //          $temp_array['deposite_return'] = 0;
   //          $temp_array['deposite_outstanding'] = $val['deposit_taken'];
   //          $temp_array['stop_date'] = "-";
   //          $temp_array['status'] = "Live";
   //          array_push($pickup_data,$temp_array);
   //       }
   //       $temp_date = date('Y',strtotime('-1 years')).'-04-01';
   //       $start_date = date('Y-m-d',strtotime($temp_date));
   //       // echo $start_date;
   //       // DB::enableQueryLog();
   //       $temp_array_renew = array();
   //       $renewal_details = Renewal::where([['order_details_id','=',$val['order_details_id']],['start_date','>=',$temp_date]])->get()->toArray();
   //       if(isset($renewal_details[0]))
   //       {
   //          foreach($renewal_details as $key => $value)
   //          {
   //              $key = date('m',strtotime($value['start_date']));
   //              $temp_array_renew[$key] = "1";
   //          }            
   //       }
   //       array_push($renewal_data,$temp_array_renew);
   //       // dd(DB::getQueryLog());
   //       // select * from `renewals` where (`order_details_id` = 526 and `start_date` >= '2021-04-01')
        
   //    }
   //    if($request->get('btn_submit') == 'export_excel')
   //    {
   //       // $mis_report_details = $mis_report_details->get();
   //       // $mis_report_details = $mis_report_details->toArray();
   //       // $jsonDecoded = json_decode(json_encode($mis_report_details),true);
   //       $jsonDecoded = $mis_report_details;
   //       $jsonDecoded1 = $renewal_data;
   //       $jsonDecoded2 = $pickup_data;
   //       //print_r($jsonDecoded);
   //       ob_end_clean(); // this
   //       ob_start(); // and this
   //       return Excel::download(new MisExport($jsonDecoded,$jsonDecoded1,$jsonDecoded2), 'MIS'.date('Y-m-d H:i:s').'.xlsx');
   //    }
   //    // dd($renewal_data);
   //    // dd($pickup_data);
   //    // dd($mis_report_details);
   //    return view('Reports/mis_reports',compact('mis_report_details','pickup_data','renewal_data','filter_data'));
   // }
   // public function misReportExport()
   // { 
   //    ob_end_clean(); // this
   //    ob_start(); // and this
   //    return Excel::download(new MisExport, 'invoices.xlsx');
   // }
}
