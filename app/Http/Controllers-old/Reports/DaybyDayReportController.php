<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Lead\customer_detail;
use App\Models\Lead\lead;
use App\Models\Lead\leads_log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\RenewalPickup\RenewalPickupController;


class DaybyDayReportController extends Controller
{
   public function isLoggedIn()
   {
      $data = session('isLoggedIn');
      //print_r($data);      
      return $data;
   }
   public function daybyday_report(Request $request)
   {
      $report_d = DB::table('del_orders')
                  ->join('order_details','del_orders.order_id','=','order_details.order_id')
                  ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                  ->join('products','order_details.product_id','=','products.id')
                  ->whereIn('del_orders.deliverypickup',['Delivery'])
                  ->whereNotNull('del_orders.DelDate')
                  ->when($request->get('start_date') && $request->get('end_date'),function($query)use($request){
                     $startDate = date('d-m-Y',strtotime($request->get('start_date')));
                     $endDate = date('d-m-Y',strtotime($request->get('end_date')));
                     $query->whereBetween(DB::raw("(STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$startDate','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$endDate','%d-%m-%Y'))")]);
                  })
                  ->orderBy( DB::raw("(STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y'))"),'DESC')
                  ->select('del_orders.DelDate','order_details.*','customer_details.customer_name','customer_details.primary_contact_no','products.product_name')
                  ->get()
                  ->groupBy('DelDate')
                  ->paginate(10);
         
      foreach ($report_d as $key => $orders) {
         // dd($orders);
         foreach($orders as $k=>$v)
         {
            $report_d[$key][$k]->product_rent = RenewalPickupController::fetchCrDrData($v->id,'R');
            $report_d[$key][$k]->product_deposite = RenewalPickupController::fetchCrDrData($v->id,'D');
            $report_d[$key][$k]->transport = RenewalPickupController::fetchCrDrData($v->id,'T');
         }
         $custData = $orders->groupBy('customer_id');
         $report_d[$key] = $custData;
      }
         
      return view('Reports/DaybyDay_report',compact('report_d'));
   }
}
