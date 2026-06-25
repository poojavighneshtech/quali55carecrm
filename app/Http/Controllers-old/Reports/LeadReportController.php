<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Lead\customer_detail;
use App\Models\Lead\lead;
use App\Models\Lead\leads_log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class LeadReportController extends Controller
{
   public function isLoggedIn()
   {
      $data = session('isLoggedIn');
      //print_r($data);      
      return $data;
   }
   public function lead_reports()
   {
      //--------leads management----------//
      $data['leads_report'] = array();
      // $user_details = DB::select("SELECT * FROM user WHERE role = 'user'");
      $user_details = DB::table('user')
                        ->when(session('city_based_access') =='1',function($query){
                           $query->where('user_city',session('user_city'));
                        })
                        ->get();

      $data['user_details'] = json_decode(json_encode($user_details),true);
      //print_r($data['user_details']);
      $i=0;
      foreach ($data['user_details'] as $user_details)
      {
         $lead_owner = $user_details['id'];
         $lead_owner_name = $user_details['username'];
         $lead_details = DB::select("SELECT * FROM leads WHERE lead_owner = $lead_owner");
         $data['lead_details'] = json_decode(json_encode($lead_details),true);
         $temp_count_process = 0;
         $temp_count_close = 0;
         $temp_count_convert = 0;
         $total_leads = count($data['lead_details']);
         foreach($data['lead_details'] as $lead_details)
         {
            if($lead_details['lead_status'] == "Work In Process")
            {
               $temp_count_process = $temp_count_process + 1;
            }
            elseif($lead_details['lead_status'] == "Converted" OR $lead_details['lead_status'] == "Vendor Assigned" OR $lead_details['lead_status'] == "Delivery In Progress" OR $lead_details['lead_status'] == "Order Generated")
            //elseif($lead_details['lead_status'] != "Work In Process" OR $lead_details['lead_status'] != "Converted" OR $lead_details['lead_status'] != "Vendor Assigned" OR $lead_details['lead_status'] != "Delivery In Progress" OR $lead_details['lead_status'] != "Mobile Generated")
            {
               $temp_count_convert = $temp_count_convert + 1;
            }
            elseif($lead_details['lead_status'] != "Work In Process" OR $lead_details['lead_status'] != "Converted" OR $lead_details['lead_status'] != "Vendor Assigned" OR $lead_details['lead_status'] != "Delivery In Progress" OR $lead_details['lead_status'] != "Mobile Generated")
            // elseif($lead_details['lead_status'] == "Converted" OR $lead_details['lead_status'] == "Vendor Assigned" OR $lead_details['lead_status'] == "Delivery In Progress" OR $lead_details['lead_status'] == "Order Generated")
            {
               $temp_count_close = $temp_count_close + 1;
            }
         }
         $data['leads_report'][$i]['username'] = $lead_owner_name;
         $data['leads_report'][$i]['InProcess'] = $temp_count_process;
         $data['leads_report'][$i]['convert'] = $temp_count_convert;
         $data['leads_report'][$i]['close'] = $temp_count_close;
         $data['leads_report'][$i]['total'] = $total_leads;
         $i = $i + 1;
      }
      //------JD leads management---------------//
      $data['jd_leads_report'] = array();
      // $user_details = DB::select("SELECT * FROM user WHERE role = 'user'");
      $user_details = DB::table('user')
                        ->when(session('city_based_access') =='1',function($query){
                           $query->where('user_city',session('user_city'));
                        })
                        ->get();
      $data['user_details'] = json_decode(json_encode($user_details),true);
      //print_r($data['user_details']);
      $i=0;
      foreach ($data['user_details'] as $user_details)
      {
         $lead_owner = $user_details['id'];
         $lead_owner_name = $user_details['username'];
         $lead_details = DB::select("SELECT * FROM jd_leads WHERE lead_owner = $lead_owner");
         $data['lead_details'] = json_decode(json_encode($lead_details),true);
         $temp_count_process = 0;
         $temp_count_close = 0;
         $temp_count_convert = 0;
         $total_leads = count($data['lead_details']);
         foreach($data['lead_details'] as $lead_details)
         {
            if($lead_details['status'] == "In Process")
            {
               $temp_count_process = $temp_count_process + 1;
            }
            elseif($lead_details['status'] == "Converted")
            {
               $temp_count_convert = $temp_count_convert + 1;
            }
            elseif($lead_details['status'] != "In Process" OR $lead_details['status'] != "Converted" OR $lead_details['status'] != "New")
            {
               $temp_count_close = $temp_count_close + 1;
            }
         }
         $data['jd_leads_report'][$i]['username'] = $lead_owner_name;
         $data['jd_leads_report'][$i]['InProcess'] = $temp_count_process;
         $data['jd_leads_report'][$i]['convert'] = $temp_count_convert;
         $data['jd_leads_report'][$i]['close'] = $temp_count_close;
         $data['jd_leads_report'][$i]['total'] = $total_leads;
         $i = $i + 1;
      }
      echo "<script>localStorage['filteredReport']='all';</script>";
      return view('Reports/lead_reports',$data);
   }
   public function filterReport($day)
   {
      //--------leads management----------//
      $data['leads_report'] = array();
      $user_details = DB::select("SELECT * FROM user WHERE role = 'user'");
      $data['user_details'] = json_decode(json_encode($user_details),true);
      $i=0;
      $whereClause = "";
      if($day =='today')
      {
         $date = date('Y-m-d');
         $whereClause = "leads.creation_date = '$date' AND";
         echo "<script>localStorage['filteredReport']='today';</script>";
      }
      elseif($day =='yesterday')
      {
         $prevDate = date('Y-m-d',strtotime("-1 days"));
         $whereClause = "leads.creation_date = '$prevDate' AND";
         echo "<script>localStorage['filteredReport']='yesterday';</script>";
      }
      elseif($day =='past_3_days')
      {
         $past_three_days = date('Y-m-d',strtotime("-2 days"));
         $whereClause = "leads.creation_date >= '$past_three_days' AND";
         echo "<script>localStorage['filteredReport']='past_3_days';</script>";
      }
      elseif($day =='week')
      {
         $past_three_days = date('Y-m-d',strtotime("-6 days"));
         $whereClause = "leads.creation_date >= '$past_three_days' AND";
         echo "<script>localStorage['filteredReport']='week';</script>";
      }
      elseif($day =='month')
      {
         $month = date('m-Y');
         $start_date_temp = '01-'.$month;
         $start_date = date('Y-m-d',strtotime($start_date_temp));
         $end_date_temp = '31-'.$month;
         $end_date = date('Y-m-d',strtotime($end_date_temp));
         $whereClause = "leads.creation_date BETWEEN '$start_date' AND '$end_date' AND";
         echo "<script>localStorage['filteredReport']='month';</script>";
      }
      elseif($day == "all")
      {         
         $whereClause = "";
         echo "<script>localStorage['filteredReport']='all';</script>";
      }
      foreach ($data['user_details'] as $user_details)
      {
         $lead_owner = $user_details['id'];
         $lead_owner_name = $user_details['username'];
         $lead_details = DB::select("SELECT * FROM leads WHERE $whereClause lead_owner = $lead_owner");
         $data['lead_details'] = json_decode(json_encode($lead_details),true);
         $temp_count_process = 0;
         $temp_count_close = 0;
         $temp_count_convert = 0;
         $total_leads = count($data['lead_details']);
         foreach($data['lead_details'] as $lead_details)
         {
            if($lead_details['lead_status'] == "Work In Process")
            {
               $temp_count_process = $temp_count_process + 1;
            }
            elseif($lead_details['lead_status'] == "Converted" OR $lead_details['lead_status'] == "Vendor Assigned" OR $lead_details['lead_status'] == "Delivery In Progress" OR $lead_details['lead_status'] == "Order Generated")
            {
               $temp_count_convert = $temp_count_convert + 1;
            }
            elseif($lead_details['lead_status'] != "Work In Process" OR $lead_details['lead_status'] != "Converted" OR $lead_details['lead_status'] != "Vendor Assigned" OR $lead_details['lead_status'] != "Delivery In Progress" OR $lead_details['lead_status'] != "Mobile Generated")
            {
               $temp_count_close = $temp_count_close + 1;
            }
         }
         $data['leads_report'][$i]['username'] = $lead_owner_name;
         $data['leads_report'][$i]['InProcess'] = $temp_count_process;
         $data['leads_report'][$i]['convert'] = $temp_count_convert;
         $data['leads_report'][$i]['close'] = $temp_count_close;
         $data['leads_report'][$i]['total'] = $total_leads;
         $i = $i + 1;
      }
      //------JD leads management---------------//
      $data['jd_leads_report'] = array();
      $user_details = DB::select("SELECT * FROM user WHERE role = 'user'");
      $data['user_details'] = json_decode(json_encode($user_details),true);
      $i=0;
      $whereClause = "";
      if($day =='today')
      {
         $date = date('Y-m-d');
         $whereClause = "jd_leads.date = '$date' AND";
         echo "<script>localStorage['filteredReport']='today';</script>";
      }
      elseif($day =='yesterday')
      {
         $prevDate = date('Y-m-d',strtotime("-1 days"));
         $whereClause = "jd_leads.date = '$prevDate' AND";
         echo "<script>localStorage['filteredReport']='yesterday';</script>";
      }
      elseif($day =='past_3_days')
      {
         $past_three_days = date('Y-m-d',strtotime("-2 days"));
         $whereClause = "jd_leads.date >= '$past_three_days' AND";
         echo "<script>localStorage['filteredReport']='past_3_days';</script>";
      }
      elseif($day =='week')
      {
         $past_three_days = date('Y-m-d',strtotime("-6 days"));
         $whereClause = "jd_leads.date >= '$past_three_days' AND";
         echo "<script>localStorage['filteredReport']='week';</script>";
      }
      elseif($day =='month')
      {
         $month = date('m-Y');
         $start_date_temp = '01-'.$month;
         $start_date = date('Y-m-d',strtotime($start_date_temp));
         $end_date_temp = '31-'.$month;
         $end_date = date('Y-m-d',strtotime($end_date_temp));
         $whereClause = "jd_leads.date BETWEEN '$start_date' AND '$end_date' AND";
         echo "<script>localStorage['filteredReport']='month';</script>";
      }
      elseif($day == "all")
      {         
         $whereClause = "";
         echo "<script>localStorage['filteredReport']='all';</script>";
      }
      foreach ($data['user_details'] as $user_details)
      {
         $lead_owner = $user_details['id'];
         $lead_owner_name = $user_details['username'];
         $lead_details = DB::select("SELECT * FROM jd_leads WHERE $whereClause lead_owner = $lead_owner");
         $data['lead_details'] = json_decode(json_encode($lead_details),true);
         $temp_count_process = 0;
         $temp_count_close = 0;
         $temp_count_convert = 0;
         $total_leads = count($data['lead_details']);
         foreach($data['lead_details'] as $lead_details)
         {
            if($lead_details['status'] == "In Process")
            {
               $temp_count_process = $temp_count_process + 1;
            }
            elseif($lead_details['status'] == "Converted")
            {
               $temp_count_convert = $temp_count_convert + 1;
            }
            elseif($lead_details['status'] != "In Process" OR $lead_details['status'] != "Converted" OR $lead_details['status'] != "New")
            {
               $temp_count_close = $temp_count_close + 1;
            }
         }
         $data['jd_leads_report'][$i]['username'] = $lead_owner_name;
         $data['jd_leads_report'][$i]['InProcess'] = $temp_count_process;
         $data['jd_leads_report'][$i]['convert'] = $temp_count_convert;
         $data['jd_leads_report'][$i]['close'] = $temp_count_close;
         $data['jd_leads_report'][$i]['total'] = $total_leads;
         $i = $i + 1;
      }
      return view('Reports/lead_reports',$data);
   }
   public function leadReportScript()
   {
      $yesterday = date('Y-m-d',strtotime('-1 day'));
      $today = date('Y-m-d');
      $start_date = date('Y-m-d',strtotime('2021-12-01'));
      $end_date = date('Y-m-d',strtotime('2021-12-06'));
      // leads.converted_at BETWEEN '$start_date' AND '$end_date'
      // STR_TO_DATE(leads.creation_date,'%Y-%m-%d') = '$yesterday'
      $leads = DB::select("SELECT 
                              leads.id as id,
                              leads.offered_rent_total as rent_total,
                              leads.deposite_total as deposite_total,
                              leads.transport as transport,
                              leads.sale_rental as sale_rental,
                              leads.lead_owner as lead_owner,
                              leads.converted_at as converted_at,
                              customer_details.customer_name as customer_name,
                              user.username as username
                           FROM 
                              leads,customer_details,user
                           WHERE
                           leads.customer_id = customer_details.cust_id
                           AND
                           leads.lead_owner = user.id
                           AND
                           STR_TO_DATE(leads.creation_date,'%Y-%m-%d') BETWEEN '2022-02-11' AND '2022-02-11'
                           -- STR_TO_DATE(leads.converted_at,'%Y-%m-%d') BETWEEN STR_TO_DATE('$start_date','%Y-%m-%d') AND STR_TO_DATE('$end_date','%Y-%m-%d')
                           AND
                           lead_status IN ('Converted', 'Order Generated')");
      $data['leads'] = json_decode(json_encode($leads),true);
      // print_r($data['leads']);
      $user_1 = array();
      $user_2 = array();
      $user_3 = array();
      $user_4 = array();
      $user_total_amount = array();
      $user_1[0]['customer_count'] = 0;
      $user_1[0]['rent_amount'] = 0;
      $user_2[0]['customer_count'] = 0;
      $user_2[0]['rent_amount'] = 0;
      $user_3[0]['customer_count'] = 0;
      $user_3[0]['rent_amount'] = 0;
      $user_4[0]['customer_count'] = 0;
      $user_4[0]['rent_amount'] = 0;
      $user_5[0]['customer_count'] = 0;
      $user_5[0]['rent_amount'] = 0;
      $user_6[0]['customer_count'] = 0;
      $user_6[0]['rent_amount'] = 0;
      $user_total_amount_rent[0]['total_amount_rent'] = 0;
      $user_total_amount_deposite[0]['total_amount_deposite'] = 0;
      $user_total_amount_transport[0]['total_amount_transport'] = 0;
      foreach ($data['leads'] as $lead)
      {
         if($lead['lead_owner']==24)
         {
            $temp_array = $user_1;
            $tp = "user";
         }
         elseif($lead['lead_owner']==26)
         {
            $temp_array = $user_2;
            $tp = "user";
         }
         elseif($lead['lead_owner']==27)
         {
            $temp_array = $user_3;
            $tp = "user";
         }
         elseif($lead['lead_owner']==15)
         {
            $temp_array = $user_4;
            $tp = "user";
         }
         elseif($lead['lead_owner']==14)
         {
            $temp_array = $user_5;
            $tp = "user";
         }
         else
         {
            $temp_array = $user_5;
            continue;
         }
            
            $deposite_total = json_decode($lead['deposite_total']);
            $offered_rent = json_decode($lead['rent_total']);
            $transport = json_decode($lead['transport']);
            $sale_rental = json_decode($lead['sale_rental']);
            $temp_array[0]['customer_count'] = $temp_array[0]['customer_count'] + 1;
            for($i=0;$i<count($offered_rent);$i++)
            {
               $temp_array[0]['lead_owner'] = $lead['username'];
               if($sale_rental[$i] == 'Rental')
               {
                  $user_total_amount_rent[0]['total_amount_rent'] = $user_total_amount_rent[0]['total_amount_rent'] + $offered_rent[$i];
                  $user_total_amount_deposite[0]['total_amount_deposite'] = $user_total_amount_deposite[0]['total_amount_deposite'] + $deposite_total[$i];
                  $user_total_amount_transport[0]['total_amount_transport'] = $user_total_amount_transport[0]['total_amount_transport'] + $transport[$i];
                  $temp_array[0]['rent_amount'] = $temp_array[0]['rent_amount'] + $offered_rent[$i];
               }
               else
               {
                  // $user_total_amount_transport[0]['total_amount_transport'] = $user_total_amount_transport[0]['total_amount_transport'] + $transport[$i];
               }

            }

         if($lead['lead_owner']==24)
         {
            $user_1 = $temp_array;
         }
         elseif($lead['lead_owner']==26)
         {
            $user_2 = $temp_array;
         }
         elseif($lead['lead_owner']==27)
         {
            $user_3 = $temp_array;
         }
         elseif($lead['lead_owner']==15)
         {
            $user_4 = $temp_array;
         }
         elseif($lead['lead_owner']==14)
         {
            $user_5 = $temp_array;
         }
      }
      print_r($user_1);
      print_r($user_2);
      print_r($user_3);
      print_r($user_4);
      print_r($user_5);
      print_r($user_total_amount_rent);
      print_r($user_total_amount_deposite);
      print_r($user_total_amount_transport);
   }
   public function orderReportTemp()
   {
      $orderTypeNotIn = config('app.order_type');
      $orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";
      for($i = 1; $i <= 31; $i++)
      {
         $query = DB::select("SELECT 
                                 del_orders.order_id as order_id,
                                 order_details.product_rent,
                                 order_details.product_deposite,
                                 order_details.transport,
                                 order_details.creation_date as date,
                                 customer_details.customer_name,
                                 leads.lead_status
                              FROM
                                 del_orders,order_details,customer_details,leads
                              WHERE
                                 del_orders.order_id = order_details.order_id
                                 AND
                                 order_details.sale_rental = 'Rental'
                                 AND
                                 order_details.creation_date BETWEEN '2021-12-$i' AND '2021-12-$i'
                                 AND
                                 order_details.customer_id = customer_details.cust_id
                                 AND 
                                 del_orders.deliverypickup NOT IN ($orderTypeNotIn)
                                 AND 
                                 del_orders.lead_id = leads.id"
                           );
         $data['details'] = json_decode(json_encode($query),true);
         $d['rent'] = 0;
         $d['deposit'] = 0;
         $d['transport'] = 0;

         foreach ($data['details'] as $details)
         {
         // echo "Customer Name: ".$details['customer_name'];
         // echo "Rent: ".$details['product_rent'];
         $d['rent'] = $d['rent'] + $details['product_rent'];
         $d['deposit'] = $d['deposit'] + $details['product_deposite'];
         $d['transport'] = $d['transport'] + $details['transport'];
         }
         print_r($d);
      }
      
   }
}
