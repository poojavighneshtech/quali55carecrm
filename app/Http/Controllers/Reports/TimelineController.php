<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Lead\customer_detail;
use App\Models\Lead\lead;
use App\Models\leads_log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Timeline;
use App\Http\Controllers\Reports\MonthlyReportController;

class TimelineController extends Controller
{
    public function getTimelines_old(Request $request)
    {
        $getOrderTimeLine = DB::table('leads_log')
                                ->whereNotNull('log_order_id')
                                ->when($request->get('order_id'),function($query) use($request){
                                    $query->where('log_order_id','=',$request->get('order_id'));
                                })
                                ->when($request->get('start_date') && $request->get('end_date'),function($query) use($request){
                                    $query->whereBetween('log_date',[$request->get('start_date'),$request->get('end_date')]);
                                })
                                ->when($request->get('order_type')!='All' && $request->get('order_type')!=null,function($query) use($request){
                                    $query->where('log_order_type','=',$request->get('order_type'));
                                })
                                ->orderBy('log_order_id','DESC')
                                ->get()
                                ->groupBy('log_order_id')
                                ->paginate(10);
                                //->toSql();
        //dd($getOrderTimeLine);
        return view('Reports.timeline',compact('getOrderTimeLine'));
    }
    public function orderTimeLineOld(Request $request,$order_id){
        $orderTimeline = DB::table('leads_log')
                        ->where('log_order_id','=',$order_id)
                        ->get()
                        ->toJson();
        return $orderTimeline;
    }

    public function orderTimeLine(Request $request,$order_id){
        $orderTimeline = DB::table('leads_log')
                        ->where('log_order_id','=',$order_id)
                        ->get();
                        // ->toJson();
        $timeline = [];
        foreach($orderTimeline as $key=>$status){
            $index = count($timeline);
            $timeline[$index]['type'] = $status->log_order_type;
            $timeline[$index]['status'] = $status->log_lead_status;
            $timeline[$index]['datetime'] = $status->created_at;
            $timeline[$index]['user'] = $status->updated_by;
        }
        if(count($timeline)<=2){
            $activity_logs = DB::table('activity_log')->where('key_id',$order_id)->whereIn('operation',['Update Status','Order Completed'])->whereNotIn('fields',['delivered_by'])->get();
            foreach($activity_logs as $key=>$status){
                $index = count($timeline);
                if($timeline[$index-1]['status'] == 'Order Completed'){
                    $timeline[$index-1]['type'] = $timeline[0]['type'];
                    $timeline[$index-1]['datetime'] = $status->updated_at;
                    $timeline[$index-1]['user'] = $status->updated_by;    
                }else{
                    if($status->operation == 'Update Status'){
                        $timeline[$index]['status'] = "Order ".$status->new_value;
                    }else{
                        $timeline[$index]['status'] = "Order Completed";
                    }
                    $timeline[$index]['type'] = $timeline[0]['type'];
                    $timeline[$index]['datetime'] = $status->updated_at;
                    $timeline[$index]['user'] = $status->updated_by;
                }
            }
            // dd($activity_logs);
        }
        // dd($timeline);
        return json_encode($timeline);
    }

    public function leadTimeLine(Request $request,$lead_id){
        $leadTimeline = DB::table('leads_log')
                        ->where('log_lead_id','=',$lead_id)
                        ->get()
                        ->toJson();
        //$orderId = DB::table('leads_log')->where('log_lead_id','=',$lead_id)->whereNotNull('log_order_id')->first();
        // $orderTimeLine = DB::table('activity_log')
        //                     ->join('leads_log','activity_log.key_id','=','leads_log.log_order_id')
        //                     ->where('leads_log.log_lead_id',$lead_id)
        //                     //->whereNotNull('leads_log.log_order_id')
        //                     ->get();
                            
        return $leadTimeline;
    }
    public function fy_report(Request $request)
    {
        $fy_year_get = $request->get('fy_year');
        // $fymin = '2021-2022'
        $fy = '2022-2023';
        // if(isset($fy_year_get))
        // {
        //     $fy = $fy_year_get;
        // }
        $fy_year = DB::table('fy_report')->get();
        // $fy_record = DB::table('fy_report')->where('fy',$fy)->get();
        $fy_record = DB::table('fy_report')->get();
        // $settled_tasks = 0;
        // $unsettled_tasks = 0;
        // if($fy == '2022-2023')
        // {
        //     $form_min_date = '2022-04-01';
        //     $form_max_date = '2023-03-31';
        //     $settled_unsettled = DB::table('del_orders')
        //             ->join('pickups','del_orders.order_id','=','pickups.pickup_order_id')
        //             ->select(
        //                 'del_orders.order_id',
        //                 'del_orders.settlement_status',
        //                 'pickups.cash_amount',
        //             )
        //             ->where('del_orders.deliverypickup','Pick Up')
        //             ->where('pickups.status',null)
        //             ->whereBetween('pickups.pickup_date',[$form_min_date,$form_max_date])
        //             ->get();
        //     foreach($settled_unsettled as $key=>$value)
        //     {
        //         if($value->settlement_status == 'N')
        //         {
        //             $unsettled_tasks = $unsettled_tasks + $value->cash_amount;
        //         }
        //         else
        //         {
        //             $settled_tasks = $settled_tasks + $value->cash_amount;
        //         }
        //     }
        // }
        $chart_records = DB::table('fy_report')->whereIn('fy',['2020-2021','2021-2022','2022-2023','2023-2024'])->get();
        $Deposit_Collected = array();
        $Last_Year_Deposit = array();
        $Deposit_Returned = array();
        $Total_Rental = array();
        $Vendor_Payment = array();
        $Transport = array();
        $Sale = array();
        $Sale_Transport = array();
        $total = array();
        $temp_Deposit_Collected = 0;
        $temp_Last_Year_Deposit = 0;
        $temp_Deposit_Returned = 0;
        $temp_Total_Rental = 0;
        $temp_Vendor_Payment = 0;
        $temp_Transport = 0;
        $temp_Sale = 0;
        $temp_Sale_Transport = 0;
        $temp_Total = 0;
        // $abc = array();
        foreach($chart_records as $key=>$value)
        {
            if($value->fy == '2022-2023')
            {
                $monthlyReportController = new MonthlyReportController();
                for($start = date('Y-m-d',strtotime('2022-04-01')); date('Y-m-d',strtotime($start))<='2023-03-31'; $start = date('Y-m-d',strtotime("+1 month",strtotime($start))))
                {
                    $new_data = $monthlyReportController->new_orders_data('All',$start,date('Y-m-d',strtotime("-1 days",strtotime(date('Y-m-d',strtotime("+1 month",strtotime($start)))))));
                    $renewal_data = $monthlyReportController->renewal_data('All',$start,date('Y-m-d',strtotime("-1 days",strtotime(date('Y-m-d',strtotime("+1 month",strtotime($start)))))));
                    // dd($new_data);
                    $end = date('Y-m-d',strtotime("-1 days",strtotime(date('Y-m-d',strtotime("+1 month",strtotime($start))))));
                    $temp_start = date('d-m-Y',strtotime($start));
                    $temp_end = date('d-m-Y',strtotime($end));
                    $depo_returned_22 = DB::table('del_orders')->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$temp_start','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$temp_end','%d-%m-%Y'))")])->where('deliverypickup','Pick Up')->whereNotIn('status',['Closed'])->get();
                    // dd(array_sum($depo_returned_22->pluck('TotalAmt')->toArray()));
                    $temp_Deposit_Collected = $temp_Deposit_Collected + $new_data['new_rent_deposite'];
                    // $temp_Last_Year_Deposit = $temp_Last_Year_Deposit + 0;
                    $temp_Deposit_Returned = $temp_Deposit_Returned + array_sum($depo_returned_22->pluck('TotalAmt')->toArray());
                    $temp_Total_Rental = $temp_Total_Rental + $new_data['new_rent_collected'] + $renewal_data['renewal_rent_collected'];
                    $temp_Total = $temp_Total + $new_data['new_rent_collected'] + $renewal_data['renewal_rent_collected'] + $new_data['rental_transportation'] + $new_data['sales_value'] + $new_data['sales_transport'] + $renewal_data['due_rent'];
                    $temp_Vendor_Payment = $temp_Vendor_Payment + $new_data['vendor_payment_rent'];
                    $temp_Transport = $temp_Transport + $new_data['rental_transportation'];
                    $temp_Sale = $temp_Sale + $new_data['sales_value'];
                    $temp_Sale_Transport = $temp_Sale_Transport + $new_data['sales_transport'];
                    // array_push($abc,date('Y-m-d',strtotime("-1 days",strtotime(date('Y-m-d',strtotime("+1 month",strtotime($start)))))));
                    // array_push($abc,$new_data['new_rent_collected']);
                    // array_push($abc,$renewal_data['renewal_rent_collected']);
                    // array_push($abc,$new_data['rental_transportation']);
                    // array_push($abc,$new_data['sales_value']);
                    // array_push($abc,$new_data['sales_transport']);
                    // array_push($abc,$renewal_data['due_rent']);
                }
                // dd($abc);
                $fy_record[4]->total_depo_collected = $temp_Deposit_Collected;
                // $fy_record[4]->last_year_depo = $temp_Last_Year_Deposit;
                $fy_record[4]->depo_returned = $temp_Deposit_Returned;
                $fy_record[4]->total_rental = $temp_Total_Rental;
                $fy_record[4]->vdr_payment = $temp_Vendor_Payment;
                $fy_record[4]->transport = $temp_Transport;
                $fy_record[4]->sale = $temp_Sale;
                $fy_record[4]->sale_transport = $temp_Sale_Transport;
                $fy_record[4]->total = $temp_Total;
            }
            // elseif($value->fy == '2023-2024'){
            //     $monthlyReportController = new MonthlyReportController();
            //     for($start = date('Y-m-d',strtotime('2023-04-01')); date('Y-m-d',strtotime($start))<=date('Y-m-d'); $start = date('Y-m-d',strtotime("+1 month",strtotime($start))))
            //     {
            //         $new_data = $monthlyReportController->new_orders_data('All',$start,date('Y-m-d',strtotime("-1 days",strtotime(date('Y-m-d',strtotime("+1 month",strtotime($start)))))));
            //         $renewal_data = $monthlyReportController->renewal_data('All',$start,date('Y-m-d',strtotime("-1 days",strtotime(date('Y-m-d',strtotime("+1 month",strtotime($start)))))));
            //         // dd($new_data);
            //         $end = date('Y-m-d',strtotime("-1 days",strtotime(date('Y-m-d',strtotime("+1 month",strtotime($start))))));
            //         $temp_start = date('d-m-Y',strtotime($start));
            //         $temp_end = date('d-m-Y',strtotime($end));
            //         $depo_returned_22 = DB::table('del_orders')->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$temp_start','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$temp_end','%d-%m-%Y'))")])->where('deliverypickup','Pick Up')->whereNotIn('status',['Closed'])->get();
            //         // dd(array_sum($depo_returned_22->pluck('TotalAmt')->toArray()));
            //         $temp_Deposit_Collected = $temp_Deposit_Collected + $new_data['new_rent_deposite'];
            //         // $temp_Last_Year_Deposit = $temp_Last_Year_Deposit + 0;
            //         $temp_Deposit_Returned = $temp_Deposit_Returned + array_sum($depo_returned_22->pluck('TotalAmt')->toArray());
            //         $temp_Total_Rental = $temp_Total_Rental + $new_data['new_rent_collected'] + $renewal_data['renewal_rent_collected'];
            //         $temp_Total = $temp_Total + $new_data['new_rent_collected'] + $renewal_data['renewal_rent_collected'] + $new_data['rental_transportation'] + $new_data['sales_value'] + $new_data['sales_transport'] + $renewal_data['due_rent'];
            //         $temp_Vendor_Payment = $temp_Vendor_Payment + $new_data['vendor_payment_rent'];
            //         $temp_Transport = $temp_Transport + $new_data['rental_transportation'];
            //         $temp_Sale = $temp_Sale + $new_data['sales_value'];
            //         $temp_Sale_Transport = $temp_Sale_Transport + $new_data['sales_transport'];
            //         // array_push($abc,date('Y-m-d',strtotime("-1 days",strtotime(date('Y-m-d',strtotime("+1 month",strtotime($start)))))));
            //         // array_push($abc,$new_data['new_rent_collected']);
            //         // array_push($abc,$renewal_data['renewal_rent_collected']);
            //         // array_push($abc,$new_data['rental_transportation']);
            //         // array_push($abc,$new_data['sales_value']);
            //         // array_push($abc,$new_data['sales_transport']);
            //         // array_push($abc,$renewal_data['due_rent']);
            //     }
            //     // dd($abc);
            //     $fy_record[5]->total_depo_collected = $temp_Deposit_Collected;
            //     // $fy_record[5]->last_year_depo = $temp_Last_Year_Deposit;
            //     $fy_record[5]->depo_returned = $temp_Deposit_Returned;
            //     $fy_record[5]->total_rental = $temp_Total_Rental;
            //     $fy_record[5]->vdr_payment = $temp_Vendor_Payment;
            //     $fy_record[5]->transport = $temp_Transport;
            //     $fy_record[5]->sale = $temp_Sale;
            //     $fy_record[5]->sale_transport = $temp_Sale_Transport;
            //     $fy_record[5]->total = $temp_Total;
            // }
            else
            {
                array_push($Deposit_Collected,$value->total_depo_collected);
                array_push($Last_Year_Deposit,$value->last_year_depo);
                array_push($Deposit_Returned,$value->depo_returned);
                array_push($Total_Rental,$value->total_rental);
                array_push($Vendor_Payment,$value->vdr_payment);
                array_push($Transport,$value->transport);
                array_push($Sale,$value->sale);
                array_push($Sale_Transport,$value->sale_transport);
                array_push($total,$value->total_rental+$value->transport+$value->sale+$value->sale_transport);
            }
        }
        // dd($fy_record);
        return view('Reports.fy_reports',compact('fy_year','fy','fy_record','Deposit_Collected','Last_Year_Deposit','Deposit_Returned','Total_Rental','Vendor_Payment','Transport','Sale_Transport','Sale','total'));
    }

    public function getTimelines(Request $request)
    {
        $dateArr = array();
        if(($request->get('start_date')!=null && $request->get('start_date')!="") || ($request->get('end_date')!=null && $request->get('end_date')!=""))
        {
            array_push($dateArr,date('d-m-Y',strtotime($request->get('start_date'))));
            array_push($dateArr,date('d-m-Y',strtotime($request->get('end_date'))));
        }
        else{
            array_push($dateArr,date('d-m-Y',strtotime(Carbon::yesterday()->toDateString())));
            array_push($dateArr,date('d-m-Y',strtotime(Carbon::today()->toDateString())));
        }
        // dd($dateArr);
        $orderTimeLine = DB::table('leads_log')
                            ->join('del_orders','del_orders.order_id','=','leads_log.log_order_id')
                            ->join('leads','del_orders.lead_id','=','leads.id')
                            ->join('customer_details','customer_details.cust_id','=','leads.customer_id')
                            ->select(
                                'leads_log.*',
                                'del_orders.DelDate',
                                'del_orders.shipping_first_name',
                                'del_orders.mobileno as contact_no',
                                'del_orders.deliverypickup as type',
                                'del_orders.status as del_status',
                                'del_orders.updatedDateTime as completed_at',
                                'del_orders.updatedBy',
                                'del_orders.DelAssignedTo',
                                'del_orders.PaymentMode'
                            )
                            ->when($dateArr,function($query,$dateArr){
                                $query->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$dateArr[0]','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$dateArr[1]','%d-%m-%Y'))")]);
                            })
                            ->when($request->get('order_type'),function($query)use($request){
                                if(!in_array("All",$request->get('order_type')))
                                    $query->whereIn('del_orders.deliverypickup',$request->get('order_type'));
                            })
                            ->when($request->get('order_id'),function($query)use($request){
                                $query->where('del_orders.order_id',$request->get('order_id'));
                            })
                            ->when($request->get('order_status'),function($query)use($request){
                                if(!in_array("All",$request->get('order_status')) && !in_array("Completed",$request->get('order_status')))
                                    $query->where('del_orders.status',$request->get('order_status'));
                                elseif(in_array("Completed",$request->get('order_status')))
                                    $query->whereIn('del_orders.status',['Delivered','Collected','Picked Up']);
                            })
                            ->when($request->get('order_state'),function($query)use($request){
                                if($request->get('order_state')=="Pending")
                                $query->whereNotIn('del_orders.status',['Delivered','Collected','Picked Up']);
                                elseif($request->get('order_state')!="Pending" && $request->get('order_state')!="All")
                                $query->whereIn('del_orders.status',['Delivered','Collected','Picked Up']);
                            })
                            ->whereNotIn('del_orders.status',['Cancel'])
                            ->when(session('city_based_access') == '1',function($query){
                                $query->where('customer_details.citygroup',session('user_city'));
                            })
                            ->when($request->get('delivery_boy'),function($query)use($request){
                                if(!in_array("All",$request->get('delivery_boy')))
                                    $query->whereIn('del_orders.DelAssignedTo',$request->get('delivery_boy'));
                            })
                            // ->orderBy(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),'ASC')
                            ->get();
        $orderTimeLine = $orderTimeLine->groupBy('log_order_id');
        $detailed_report = array();
        $detailed_report[0]['order_type'] = "Delivery";
        $detailed_report[0]['pending_count'] = 0;
        $detailed_report[0]['delay_count'] = 0;
        $detailed_report[0]['on_time_count'] = 0;
        $detailed_report[0]['exce_count'] = 0;
        $detailed_report[0]['delay_perc'] = array();
        $detailed_report[0]['on_time_perc'] = array();
        $detailed_report[0]['exce_perc'] = array();

        $detailed_report[1]['order_type'] = "Collection";
        $detailed_report[1]['pending_count'] = 0;
        $detailed_report[1]['delay_count'] = 0;
        $detailed_report[1]['on_time_count'] = 0;
        $detailed_report[1]['exce_count'] = 0;
        $detailed_report[1]['delay_perc'] = array();
        $detailed_report[1]['on_time_perc'] = array();
        $detailed_report[1]['exce_perc'] = array();

        $detailed_report[2]['order_type'] = "Pick Up";
        $detailed_report[2]['pending_count'] = 0;
        $detailed_report[2]['delay_count'] = 0;
        $detailed_report[2]['on_time_count'] = 0;
        $detailed_report[2]['exce_count'] = 0;
        $detailed_report[2]['delay_perc'] = array();
        $detailed_report[2]['on_time_perc'] = array();
        $detailed_report[2]['exce_perc'] = array();
        // dd($orderTimeLine);
        if($request->get('order_state') == 'On Time' || $request->get('order_state') == "Delay" || $request->get('order_state') == "Exception")
        {
            foreach($orderTimeLine as $key=>$value)
            {
                if(isset($value[4]))
                {
                    $completed_date = Carbon::parse(date('d-M-y',strtotime($value[4]->log_date))." ".date('H:i:s',strtotime($value[4]->log_time)));
                    $assigned_date = Carbon::parse(date('d-M-y',strtotime($value[0]->log_date))." ".date('H:i:s',strtotime($value[0]->log_time)));
        
                    $diff = $completed_date->diffInSeconds($assigned_date);
                    $state = "On Time";
                    if(date('H:i:s',strtotime(gmdate('H:i:s', $diff))) >= date('H:i:s',strtotime('04:00:00')))
                    {
                        if($request->get('order_state') == 'On Time' || $request->get('order_state') =='Exception')
                        {
                            unset($orderTimeLine[$key]);
                        }
                    }
                    elseif(date('H:i:s',strtotime(gmdate('H:i:s', $diff))) <= date('H:i:s',strtotime('01:15:00')))
                    {
                        if($request->get('order_state') == 'Delay' || $request->get('order_state') == 'On Time')
                        {
                            unset($orderTimeLine[$key]);
                        }
                    }
                    else
                    {
                        if($request->get('order_state') == 'Delay' || $request->get('order_state') == 'Exception')
                        {
                            unset($orderTimeLine[$key]);
                        }
                    }
                }
                elseif($value[0]->del_status=='Picked up' || $value[0]->del_status=='Picked Up' || $value[0]->del_status=='Delivered' || $value[0]->del_status=='Collected')
                {
                    $completed_date = Carbon::parse(date('d-M-y',strtotime($value[0]->completed_at))." ".date('H:i:s',strtotime($value[0]->completed_at)));
                    $assigned_date = Carbon::parse(date('d-M-y',strtotime($value[0]->log_date))." ".date('H:i:s',strtotime($value[0]->log_time)));
        
                    $diff = $completed_date->diffInSeconds($assigned_date);
                    // $state = "On Time";
                    // if(date('H:i:s',strtotime(gmdate('H:i:s', $diff))) >= date('H:i:s',strtotime('04:00:00')))
                    // {
                    //     if($request->get('order_state') == 'On Time')
                    //     {
                    //         unset($orderTimeLine[$key]);
                    //     }
                    // }
                    // else
                    // {
                    //     if($request->get('order_state') == 'Delay')
                    //     {
                    //         unset($orderTimeLine[$key]);
                    //     }
                    // }
                    if(date('H:i:s',strtotime(gmdate('H:i:s', $diff))) >= date('H:i:s',strtotime('04:00:00')))
                    {
                        if($request->get('order_state') == 'On Time' || $request->get('order_state') == 'Exception')
                        {
                            unset($orderTimeLine[$key]);
                        }
                    }
                    elseif(date('H:i:s',strtotime(gmdate('H:i:s', $diff))) <= date('H:i:s',strtotime('01:15:00')))
                    {
                        if($request->get('order_state') == 'Delay' || $request->get('order_state') == 'On Time')
                        {
                            unset($orderTimeLine[$key]);
                        }
                    }
                    else
                    {
                        if($request->get('order_state') == 'Delay' || $request->get('order_state') == 'Exception')
                        {
                            unset($orderTimeLine[$key]);
                        }
                    }
                }
                else
                {
                    if($request->get('order_state') !=null && $request->get('order_state') != "All" && $request->get('order_state') != "Pending")
                    unset($orderTimeLine[$key]);
                }
            }
        }
        // dd($orderTimeLine);
        foreach($orderTimeLine as $key=>$value)
        {
            if($value[0]->type == 'Collection' && $value[0]->PaymentMode == 'Online')
            {
                unset($orderTimeLine[$key]);
            }
            // For percentage n all..
            if($value[0]->type == "Delivery")
            {
                if(isset($value[4]))
                {
                    $completed_date = Carbon::parse(date('d-M-y',strtotime($value[4]->log_date))." ".date('H:i:s',strtotime($value[4]->log_time)));
                    $assigned_date = Carbon::parse(date('d-M-y',strtotime($value[0]->log_date))." ".date('H:i:s',strtotime($value[0]->log_time)));
        
                    $diff = $completed_date->diffInSeconds($assigned_date);
                    $diff_min = $completed_date->diffInMinutes($assigned_date);
                    if(date('H:i:s',strtotime(gmdate('H:i:s', $diff))) >= date('H:i:s',strtotime('04:00:00')))
                    {
                        $detailed_report[0]['delay_count'] = $detailed_report[0]['delay_count'] + 1;
                        array_push($detailed_report[0]['delay_perc'],$diff_min);
                    }
                    elseif(date('H:i:s',strtotime(gmdate('H:i:s', $diff))) <= date('H:i:s',strtotime('01:15:00')))
                    {
                        $detailed_report[0]['exce_count'] = $detailed_report[0]['exce_count'] + 1;
                        array_push($detailed_report[0]['exce_perc'],$diff_min);
                    }
                    else
                    {
                        $detailed_report[0]['on_time_count'] = $detailed_report[0]['on_time_count'] + 1;
                        array_push($detailed_report[0]['on_time_perc'],$diff_min);
                    }
                    
                }
                elseif($value[0]->del_status=='Delivered')
                {
                    
                    $completed_date = Carbon::parse(date('d-M-y',strtotime($value[0]->completed_at))." ".date('H:i:s',strtotime($value[0]->completed_at)));
                    $assigned_date = Carbon::parse(date('d-M-y',strtotime($value[0]->log_date))." ".date('H:i:s',strtotime($value[0]->log_time)));
        
                    $diff = $completed_date->diffInSeconds($assigned_date);
                    $diff_min = $completed_date->diffInMinutes($assigned_date);
                    if(date('H:i:s',strtotime(gmdate('H:i:s', $diff))) >= date('H:i:s',strtotime('04:00:00')))
                    {
                        $detailed_report[0]['delay_count'] = $detailed_report[0]['delay_count'] + 1;
                        array_push($detailed_report[0]['delay_perc'],$diff_min);
                    }
                    elseif(date('H:i:s',strtotime(gmdate('H:i:s', $diff))) <= date('H:i:s',strtotime('01:15:00')))
                    {
                        $detailed_report[0]['exce_count'] = $detailed_report[0]['exce_count'] + 1;
                        array_push($detailed_report[0]['exce_perc'],$diff_min);
                    }
                    else
                    {
                        $detailed_report[0]['on_time_count'] = $detailed_report[0]['on_time_count'] + 1;
                        array_push($detailed_report[0]['on_time_perc'],$diff_min);
                    }
                }
                else
                {
                    $detailed_report[0]['pending_count'] = $detailed_report[0]['pending_count'] + 1;
                }
            }
            elseif($value[0]->type == "Collection")
            {
                if(isset($value[4]))
                {
                    $completed_date = Carbon::parse(date('d-M-y',strtotime($value[4]->log_date))." ".date('H:i:s',strtotime($value[4]->log_time)));
                    $assigned_date = Carbon::parse(date('d-M-y',strtotime($value[0]->log_date))." ".date('H:i:s',strtotime($value[0]->log_time)));
        
                    $diff = $completed_date->diffInSeconds($assigned_date);
                    $diff_min = $completed_date->diffInMinutes($assigned_date);
                    if(date('H:i:s',strtotime(gmdate('H:i:s', $diff))) >= date('H:i:s',strtotime('04:00:00')))
                    {
                        $detailed_report[1]['delay_count'] = $detailed_report[1]['delay_count'] + 1;
                        array_push($detailed_report[1]['delay_perc'],$diff_min);
                    }
                    elseif(date('H:i:s',strtotime(gmdate('H:i:s', $diff))) <= date('H:i:s',strtotime('01:15:00')))
                    {
                        $detailed_report[1]['exce_count'] = $detailed_report[1]['exce_count'] + 1;
                        array_push($detailed_report[1]['exce_perc'],$diff_min);
                    }
                    else
                    {
                        $detailed_report[1]['on_time_count'] = $detailed_report[1]['on_time_count'] + 1;
                        array_push($detailed_report[1]['on_time_perc'],$diff_min);
                    }
                }
                elseif($value[0]->del_status=='Collected')
                {
                    $completed_date = Carbon::parse(date('d-M-y',strtotime($value[0]->completed_at))." ".date('H:i:s',strtotime($value[0]->completed_at)));
                    $assigned_date = Carbon::parse(date('d-M-y',strtotime($value[0]->log_date))." ".date('H:i:s',strtotime($value[0]->log_time)));
        
                    $diff = $completed_date->diffInSeconds($assigned_date);
                    $diff_min = $completed_date->diffInMinutes($assigned_date);
                    if(date('H:i:s',strtotime(gmdate('H:i:s', $diff))) >= date('H:i:s',strtotime('04:00:00')))
                    {
                        $detailed_report[1]['delay_count'] = $detailed_report[1]['delay_count'] + 1;
                        array_push($detailed_report[1]['delay_perc'],$diff_min);
                    }
                    elseif(date('H:i:s',strtotime(gmdate('H:i:s', $diff))) <= date('H:i:s',strtotime('01:15:00')))
                    {
                        $detailed_report[1]['exce_count'] = $detailed_report[1]['exce_count'] + 1;
                        array_push($detailed_report[1]['exce_perc'],$diff_min);
                    }
                    else
                    {
                        $detailed_report[1]['on_time_count'] = $detailed_report[1]['on_time_count'] + 1;
                        array_push($detailed_report[1]['on_time_perc'],$diff_min);
                    }
                }
                else
                {
                    $detailed_report[1]['pending_count'] = $detailed_report[1]['pending_count'] + 1;
                    
                }
            }
            elseif($value[0]->type == "Pick Up")
            {
                // dd("Inside if.");
                if(isset($value[4]))
                {
                    $completed_date = Carbon::parse(date('d-M-y',strtotime($value[4]->log_date))." ".date('H:i:s',strtotime($value[4]->log_time)));
                    $assigned_date = Carbon::parse(date('d-M-y',strtotime($value[0]->log_date))." ".date('H:i:s',strtotime($value[0]->log_time)));
        
                    $diff = $completed_date->diffInSeconds($assigned_date);
                    $diff_min = $completed_date->diffInMinutes($assigned_date);
                    if(date('H:i:s',strtotime(gmdate('H:i:s', $diff))) >= date('H:i:s',strtotime('04:00:00')))
                    {
                        $detailed_report[2]['delay_count'] = $detailed_report[2]['delay_count'] + 1;
                        array_push($detailed_report[2]['delay_perc'],$diff_min);
                    }
                    elseif(date('H:i:s',strtotime(gmdate('H:i:s', $diff))) <= date('H:i:s',strtotime('01:15:00')))
                    {
                        $detailed_report[2]['exce_count'] = $detailed_report[2]['exce_count'] + 1;
                        array_push($detailed_report[2]['exce_perc'],$diff_min);
                    }
                    else
                    {
                        $detailed_report[2]['on_time_count'] = $detailed_report[2]['on_time_count'] + 1;
                        array_push($detailed_report[2]['on_time_perc'],$diff_min);
                    }
                }
                elseif($value[0]->del_status=='Picked up' || $value[0]->del_status=='Picked Up')
                {
                    $completed_date = Carbon::parse(date('d-M-y',strtotime($value[0]->completed_at))." ".date('H:i:s',strtotime($value[0]->completed_at)));
                    $assigned_date = Carbon::parse(date('d-M-y',strtotime($value[0]->log_date))." ".date('H:i:s',strtotime($value[0]->log_time)));
        
                    $diff = $completed_date->diffInSeconds($assigned_date);
                    $diff_min = $completed_date->diffInMinutes($assigned_date);
                    if(date('H:i:s',strtotime(gmdate('H:i:s', $diff))) >= date('H:i:s',strtotime('04:00:00')))
                    {
                        $detailed_report[2]['delay_count'] = $detailed_report[2]['delay_count'] + 1;
                        array_push($detailed_report[2]['delay_perc'],$diff_min);
                    }
                    elseif(date('H:i:s',strtotime(gmdate('H:i:s', $diff))) <= date('H:i:s',strtotime('01:15:00')))
                    {
                        $detailed_report[2]['exce_count'] = $detailed_report[2]['exce_count'] + 1;
                        array_push($detailed_report[2]['exce_perc'],$diff_min);
                    }
                    else
                    {
                        $detailed_report[2]['on_time_count'] = $detailed_report[2]['on_time_count'] + 1;
                        array_push($detailed_report[2]['on_time_perc'],$diff_min);
                    }
                }
                else
                {
                    $detailed_report[2]['pending_count'] = $detailed_report[2]['pending_count'] + 1;
                }
            }
        }
        $deliveryBoys = DB::table('delusers')->where('status','Active')->orderBy('username','ASC')->get();
        // dd($detailed_report);
        if($request->get('submitted') == "Export")
        {
            ob_end_clean(); // this
            ob_start(); // and this
            return Excel::download(new Timeline($orderTimeLine), 'Timeline.xlsx');
        }
        else{
            $orderTimeLine = $orderTimeLine->paginate(10);
            return view('Reports.timeline',compact('orderTimeLine','detailed_report','deliveryBoys'));
        }
        // dd($orderTimeLine);
    }
}
?>