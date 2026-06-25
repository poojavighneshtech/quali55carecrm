<?php

namespace App\Http\Controllers\DeliveryManagement;

use App\Http\Controllers\Controller;
use App\Models\DelOrders;
use App\Models\OrderDetails;
use App\Models\customer_detail;
use App\Models\Renewal;
use App\Models\Pickup;
use App\Models\VendorProducts;
use App\Models\q5c_pools_products;
use Illuminate\Http\Request;
use App\Models\leads_log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\User\UserController;
use PDF;
use Mail;
use Session;


class DeliveryController extends Controller
{
   public function isLoggedIn()
   {
	  $data = session('isLoggedIn');
	  //print_r($data);      
	  return $data;
   }
   public function AddDelivery()
   {
      $del_order = new DelOrders();
      if($_SERVER['REQUEST_METHOD']=='GET')
      {
         $delboys = DB::select("SELECT *FROM delusers WHERE role='user'");
         $data['delboys'] = json_decode(json_encode($delboys), true);
         $products = DB::select("SELECT * FROM products");
         $data['products'] = \json_decode(\json_encode($products), true);
         return view('DeliveryManagement/AddDelivery',$data);
      }
      else
      {
         //print_r($_POST);
         $name=$_POST['name'];
            $contact_no=$_POST['contact_no'];
            $del_status=$_POST['del_status'];
            $delivery_type=$_POST['delivery_type'];
            $equipments=$_POST['equipments'];
            $other_equipments=$_POST['multiple_products'];
            if ($equipments =='Multiple') {
                $equipments=$other_equipments;
            } 
            $del_assigned_to=$_POST['del_assigned_to'];
            $invoice_type=$_POST['invoice_type'];
            $date = $_POST['date'];
            $date = date('d-m-Y',strtotime($date));
            $travel=$_POST['travel'];
            $payment_mode=$_POST['payment_mode'];
            $pick_up_from_address=$_POST['pick_up_from_address'];
            $amount=$_POST['amount'];
            $address=$_POST['address'];
            $cust_location=$_POST['cust_location'];
            $pick_up_from=$_POST['pick_up_from'];
            
            $drop_at=$_POST['drop_at'];
            $amount_to_be=$_POST['amount_to_be'];
            if ($amount_to_be == "Pay") {
                $amount="-".trim($amount);
            }
         $UpdatedBy="webadmin";
         $insert_data = [
            'status' => $del_status,
            'deliverypickup' => $delivery_type,
            'shipping_first_name' => $name,
            'mobileno' => $contact_no,
            'PickupLocation' => $pick_up_from,
            'DropLocation' => $drop_at,
            'UpdatedBy' => $UpdatedBy,
            'Location' => $cust_location, 
            'line_item_1' => $equipments,
            'DelDate' => $date,
            'DelAssignedTo' => $del_assigned_to,
            'ReceiptToBeCarried' => $invoice_type,
            'PaymentMode' => $payment_mode,
            'itemAddress' => $pick_up_from_address,
            'TotalAmt' => $amount,
            'TravelMode' => $travel,
            'fulldetails'=> $address,
         ];       
         //print_r($insert_data);
         $del_order->insert($insert_data);
         return redirect('AllDeliveries');
      }
   }
   public function ModifyDeliveryView()
   {
      $orders = DB::select("SELECT * FROM del_orders WHERE lead_id IS NOT NULL AND order_approval_status !='Pending' AND status!='Pending' order by order_id DESC");
      $data['orders'] = json_decode(json_encode($orders),true);
      return view('/DeliveryManagement/ModifyDeliveryView',$data);
   }
   public function ModifyDelivery($order_id)
   {
      $order_details = DB::select("SELECT * FROM del_orders WHERE order_id = $order_id");
      $data['order_details'] = json_decode(json_encode($order_details),true);
      $delboys = DB::select("SELECT *FROM delusers WHERE role='user'");
      $data['delboys'] = json_decode(json_encode($delboys), true);
      $products = DB::select("SELECT * FROM products");
      $data['products'] = \json_decode(\json_encode($products), true);
      //$product_details = DB::select("SELECT order_details. FROM order_details,products,vendor_details WHERE order_id = $order_id AND order_details.product_id=products.id AND order_details.vendor_id = vendor_details.id");
      $product_details = DB::select("SELECT 
                                          products.product_name as product_name,
                                          order_details.product_qty as product_qty,
                                          order_details.product_deposite as product_deposite,
                                          order_details.product_rent as product_rent,
                                          order_details.transport as transport
                                       FROM order_details,products,vendor_details 
                                       WHERE order_details.order_id = $order_id 
                                       AND order_details.product_id=products.id 
                                       AND order_details.vendor_id = vendor_details.id");
      $data['product_details'] = json_decode(json_encode($product_details),true);
      return view('/DeliveryManagement/ModifyDelivery',$data);
   }
   public function ModifyDeliveryPost()
   {
      print_r($_POST);
      $order_id = $_POST['order_id'];
      $lead_id = $_POST['lead_id'];
      $del_status=$_POST['del_status'];
      $delivery_type=$_POST['delivery_type'];
      $del_assigned_to=$_POST['del_assigned_to'];
      if($_POST['helpers'][0] != "No Helper")
      {
         $helpers=$_POST['helpers'];
         $helpers = json_encode($helpers, true);
      }
      else
      {
         $helpers = "[No helper]";
      }
      $invoice_type=$_POST['invoice_type'];
      $travel=$_POST['travel'];
      $payment_mode=$_POST['payment_mode'];
      $UpdatedBy=session('username');
      $update_data = [
         'status' => $del_status,
         'helpers' => $helpers,
         'deliverypickup' => $delivery_type,
         'UpdatedBy' => $UpdatedBy,
         'DelAssignedTo' => $del_assigned_to,
         'ReceiptToBeCarried' => $invoice_type,
         'PaymentMode' => $payment_mode,
         'TravelMode' => $travel,
         'order_approval_status'=>'Approved',
      ];       
      //print_r($update_data);
      $del_order = new DelOrders();      
      $del_order->where('order_id', $order_id)->update($update_data);
      
      $leads_log = new leads_log();
      $leads_log_data = [
         'log_lead_id' => $lead_id,
         'log_lead_status' => 'Order Generated',
         'log_date' => date('Y-m-d'),
         'log_time' => date('H:i:s'),
         'updated_by' => session('username')
     ];
     $leads_log->insert($leads_log_data);
      return redirect('/confirmed_delivery')->with('Message','Order Modified Successfully');
   }
   public function AllDeliveries() 
   {
      $order_details = DB::select("SELECT * FROM del_orders Where status != 'Closed' ORDER BY order_id DESC");
      $data['order_details'] = json_decode(json_encode($order_details),true);
      return view('/DeliveryManagement/AllDeliveries',$data);
   }
   public function CompletedDeliveries() 
   {
      $order_details = DB::select("SELECT * FROM del_orders Where status IN ('Delivered','Picked Up', 'Collected') ORDER BY order_id DESC");
      $data['order_details'] = json_decode(json_encode($order_details),true);
      return view('/DeliveryManagement/CompletedDeliveries',$data);
   }
   public function ArchivedDeliveries() 
   {
      $order_details = DB::select("SELECT * FROM del_orders Where status = 'Closed' ORDER BY order_id DESC");
      $data['order_details'] = json_decode(json_encode($order_details),true);
      return view('/DeliveryManagement/ArchivedDeliveries',$data);
   }
   public function MonthlyDeliveryReport()
   {
      if($_SERVER['REQUEST_METHOD'] == 'GET')
      {
         $month = date('m-Y');
         $start_date = '01-'.$month;
         //echo $start_date;
         $end_date = '31-'.$month;
         //echo $end_date;
      }
      else
      {
         if($_POST['search']=='search_monthly')
         {
            $date = $_POST['month'];
            $month = date('m-Y',strtotime($date));
            $start_date = '01-'.$month;
            $end_date = '31-'.$month;
         }
         elseif($_POST['search']=='search_datewise')
         {
            $start_date = $_POST['start_date'];
            $end_date = $_POST['last_date'];
         }
      }
      $j = 0;
      
      while (strtotime($start_date) <= strtotime($end_date))
      {
         $order_details = DB::select("SELECT * FROM del_orders WHERE STR_TO_DATE(DelDate,'%d-%m-%Y') = STR_TO_DATE('$start_date','%d-%m-%Y')");         
         if($order_details !=null)
         {
            $data['order_details'] = json_decode(json_encode($order_details),true);
            $count = count($data['order_details']);
            $total_del_amount = 0;
            $total_del_orders = 0;
            $total_pic_amount = 0;
            $total_pic_orders = 0;
            $total_col_amount = 0;
            $total_col_orders = 0;
            for ($i = 0; $i < $count; $i++)
            {
               $data['order_details_data'][$j][$i] = $data['order_details'][$i];
               if($data['order_details'][$i]['deliverypickup']=='Delivery')
               {
                  $total_del_orders = $total_del_orders+1;
                  $total_del_amount = $total_del_amount + $data['order_details'][$i]['TotalAmt'];
               }
               elseif($data['order_details'][$i]['deliverypickup']=='Pick Up')
               {
                  $total_pic_orders = $total_pic_orders+1;
                  $total_pic_amount = $total_pic_amount + $data['order_details'][$i]['TotalAmt'];	
               }
               elseif($data['order_details'][$i]['deliverypickup']=='Collection')
               {
                  $total_col_orders = $total_col_orders+1;
                  $total_col_amount = $total_col_amount + $data['order_details'][$i]['TotalAmt'];	
               }
            }
            $data['order_details_data'][$j]['del_date'] = $start_date.",".date('l', strtotime($start_date));
            $data['order_details_data'][$j]['total_del_orders'] = $total_del_orders;
            $data['order_details_data'][$j]['total_del_amount'] = $total_del_amount;
            $data['order_details_data'][$j]['total_pic_orders'] = $total_pic_orders;
            $data['order_details_data'][$j]['total_pic_amount'] = $total_pic_amount;
            $data['order_details_data'][$j]['total_col_orders'] = $total_col_orders;
            $data['order_details_data'][$j]['total_col_amount'] = $total_col_amount;
            $j++;
         }
         $start_date = date ("d-m-Y", strtotime("+1 day", strtotime($start_date)));
      }
      //print_r($data['order_details_data']);
      if(isset($data))
      {
         return view('/DeliveryManagement/MonthlyDeliveryReport',$data);
      }
      else
      {
         return redirect('/MonthlyDeliveryReport')->with('message','No Data Available');
      }
   }
   public function confirmed_delivery()
   {
      //$order_details = DB::select("SELECT del_orders.order_id as order_id, del_orders.lead_id as lead_id, del_orders.DelDate as DelDate, del_orders.shipping_first_name as shipping_first_name, del_orders.mobileno as mobileno, order_details.status as status FROM del_orders,order_details Where del_orders.order_id=order_details.order_id AND del_orders.order_approval_status='Approved'");
      $date = date('d-m-Y');
      $order_details = DB::select("SELECT * FROM del_orders WHERE order_approval_status = 'Approved' AND DelAssignedTo ='Pending' AND DelDate='$date' ");
      $data['order_details'] = json_decode(json_encode($order_details),true);
      echo "<script>localStorage['filtered']='today';</script>";
      //print_r($data['order_details']);
      // $prev_order_id = 0;
      // $count_status = 1;
      // $status = null;
      // $data['final_array'] = array();
      // $order_details_array = array();
      // foreach($data['order_details'] as $order_detail)
      // {
      //    if($order_detail['order_id'] == $prev_order_id)
      //    {
      //       if($status=='Accepted')
      //       {
      //          $count_status = $count_status + 1;
      //       }
      //       $temp_data['DelDate'] = $order_detail['DelDate'];
      //       $temp_data['order_id'] = $order_detail['order_id'];
      //       $temp_data['lead_id'] = $order_detail['lead_id'];
      //       $temp_data['shipping_first_name'] = $order_detail['shipping_first_name'];
      //       $temp_data['mobileno'] = $order_detail['mobileno'];
      //       $temp_data['status'] = $order_detail['status'];         
      //       $count = count($order_details_array[$prev_order_id]);
      //       $order_details_array[$prev_order_id][$count] = $temp_data;            
      //       $order_details_array[$prev_order_id]['count'] = $count_status;
      //       $status = $order_detail['status'];
      //    }
      //    else
      //    {
      //       $count_status = 1;
      //       $prev_order_id = $order_detail['order_id'];
      //       $temp_data['DelDate'] = $order_detail['DelDate'];
      //       $temp_data['order_id'] = $order_detail['order_id'];
      //       $temp_data['lead_id'] = $order_detail['lead_id'];
      //       $temp_data['shipping_first_name'] = $order_detail['shipping_first_name'];
      //       $temp_data['mobileno'] = $order_detail['mobileno'];
      //       $temp_data['status'] = $order_detail['status'];                        
      //       $order_details_array[$prev_order_id][0] = $temp_data;
      //       $order_details_array[$prev_order_id]['count'] = $count_status;
      //       $status = $order_detail['status'];
      //       if($status=='Accepted')
      //       {
      //          $count_status = $count_status + 1;
      //       }
      //    }
      // }
      // //print_r($order_details_array);
      // foreach($order_details_array as $order_detail)
      // {
      //    $count=count($order_detail);
      //    $count=$count-1;
      //    //echo $count;
      //    if($order_detail['count'] == $count)
      //    {
      //       //echo "a";
      //       //$final_array = $order_detail;
      //       $lead_id = $order_detail[0]['lead_id'];
      //       DB::UPDATE("UPDATE leads SET lead_status = 'Delivery In Progress' WHERE id = $lead_id");
      //       $leads_log = new leads_log();
      //       $leads_log_data = [
      //          'log_lead_id' => $lead_id,
      //          'log_lead_status' => 'Delivery In Progress',
      //          'log_date' => date('Y-m-d'),
      //          'log_time' => date('H:i:s'),
      //          'updated_by' => session('username')
      //       ];
      //       $leads_log->insert($leads_log_data);
      //       array_push($data['final_array'],$order_detail);
      //    }
      // }
      //print_r($data['final_array']);
      return view('/DeliveryManagement/confirmed_delivery',$data);
   }
   public function assign_deliveryBoy($order_id)
   {
         $order_details = DB::select("SELECT * FROM del_orders WHERE order_id = $order_id");
         $data['order_details'] = json_decode(json_encode($order_details),true);
         //print_r($data['order_details']);
         $delboys = DB::select("SELECT *FROM delusers WHERE role='user'");
         $data['delboys'] = json_decode(json_encode($delboys), true);
         $products = DB::select("SELECT * FROM products");
         $data['products'] = \json_decode(\json_encode($products), true);
         //$product_details = DB::select("SELECT order_details. FROM order_details,products,vendor_details WHERE order_id = $order_id AND order_details.product_id=products.id AND order_details.vendor_id = vendor_details.id");
         $product_details = DB::select("SELECT 
                                             products.product_name as product_name,
                                             order_details.product_qty as product_qty,
                                             order_details.product_deposite as product_deposite,
                                             order_details.product_rent as product_rent,
                                             order_details.transport as transport,
											 vendor_details.registered_name as vendor_name,
											 vendor_warehouse.wh_name as warehouse_name,
											 vendor_warehouse.wh_area as warehouse_area,
											 vendor_warehouse.wh_city as warehouse_city
                                        FROM order_details,products,vendor_details,vendor_warehouse
                                        WHERE order_details.order_id = $order_id 
                                        AND order_details.product_id=products.id 
                                        AND order_details.vendor_id = vendor_details.id
										AND order_details.vendor_warehouse_id = vendor_warehouse.id");
         $data['product_details'] = json_decode(json_encode($product_details),true);
         return view('/DeliveryManagement/AssignDelBoy',$data);
         //print_r($data['product_details']);
   }
   public function assign_deliveryBoy_post()
   {
      
      $order_id = $_POST['order_id'];
      $lead_id = $_POST['lead_id'];
      $del_status=$_POST['del_status'];
      $delivery_type=$_POST['delivery_type'];
      $del_assigned_to=$_POST['del_assigned_to'];
      $invoice_type=$_POST['invoice_type'];
      
      $travel=$_POST['travel'];
      $payment_mode=$_POST['payment_mode'];
      $line_item = $_POST['line_item_1'];
      $line_item = implode(",",$line_item);
      $UpdatedBy=session('username');
      if($_POST['helpers'][0] != "No Helper")
      {
         $helpers=$_POST['helpers'];
         $helpers = json_encode($helpers, true);
      }
      else
      {
         $helpers = "[No helper]";
      }
      $update_data = [
         'status' => $del_status,
         'helpers' => $helpers,
         'line_item_1' => $line_item,
         'deliverypickup' => $delivery_type,
         'UpdatedBy' => $UpdatedBy,
         'DelAssignedTo' => $del_assigned_to,
         'ReceiptToBeCarried' => $invoice_type,
         'PaymentMode' => $payment_mode,
         'TravelMode' => $travel,
         'order_approval_status'=>'Approved',
      ];       
      //print_r($update_data);
      $del_order = new DelOrders();      
      $del_order->where('order_id', $order_id)->update($update_data);
      DB::UPDATE("UPDATE leads SET lead_status = 'Order Generated' WHERE id = $lead_id");
      $leads_log = new leads_log();
      $leads_log_data = [
         'log_lead_id' => $lead_id,
         'log_lead_status' => 'Order Generated',
         'log_date' => date('Y-m-d'),
         'log_time' => date('H:i:s'),
         'updated_by' => session('username')
     ];
     $leads_log->insert($leads_log_data);
      return redirect('/confirmed_delivery')->with('Message','Order Assigned Successfully');
   }
   public function logout()
   {
	   $request = new Request();
	   //session()->destroy();
	   session(['isLoggedIn' => 'false']);
	   //$data = session()->all();
	   //print_r($data);      
	   return view('Admin/admin_login');
   }

   public function filterDeliveryOrder($filter_by)
   {
		if($filter_by =='today')
		{
			$date = date('d-m-Y');
			$whereClause = "DelDate = '$date'";
			echo "<script>localStorage['filtered']='today';</script>";
		}
		elseif($filter_by =='yesterday')
		{
			$prevDate = date('d-m-Y',strtotime("-1 days"));
			$whereClause = "DelDate = '$prevDate'";
			echo "<script>localStorage['filtered']='yesterday';</script>";
		}
		elseif($filter_by =='past_3_days')
		{
			$past_three_days = date('d-m-Y',strtotime("-2 days"));
			$whereClause = "DelDate >= '$past_three_days'";
			echo "<script>localStorage['filtered']='past_3_days';</script>";
		}
		 elseif($filter_by =='week')
		{
			$past_three_days = date('d-m-Y',strtotime("-6 days"));
			$whereClause = "DelDate >= '$past_three_days'";
			echo "<script>localStorage['filtered']='week';</script>";
		}
		elseif($filter_by =='month')
		{
			$month = date('m-Y');
			$start_date_temp = '01-'.$month;
			$start_date = date('d-m-Y',strtotime($start_date_temp));
			$end_date_temp = '31-'.$month;
			$end_date = date('d-m-Y',strtotime($end_date_temp));
			$whereClause = "DelDate BETWEEN '$start_date' AND '$end_date'";
		}
		elseif($filter_by == 'all')
		{   
			$order_details = DB::select("SELECT * FROM del_orders WHERE order_approval_status = 'Approved' order by order_id DESC");
			$data['order_details'] = json_decode(json_encode($order_details),true);
			
			echo "<script>localStorage['filtered']='all';</script>";
			return view('/DeliveryManagement/confirmed_delivery',$data);
		}
	   
		$order_details = DB::select("SELECT * FROM del_orders WHERE order_approval_status = 'Approved' AND $whereClause  order by order_id DESC");
		$data['order_details'] = json_decode(json_encode($order_details),true);
		
		return view('/DeliveryManagement/confirmed_delivery',$data);
   }

   //pickup requested orders

   public function pickup_request()
   {
       if($_SERVER['REQUEST_METHOD']=='GET')
       { 
            $today = date('d-m-Y');
			$past_three_days = date('d-m-Y',strtotime("-2 days"));
			$whereClause = "STR_TO_DATE(DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$past_three_days','%d-%m-%Y') AND STR_TO_DATE('$today','%d-%m-%Y')";
            //$pickup_request = DB::select("SELECT * FROM pickups,order_details where order_details.id=pickups.order_details_id AND order_details.order_id=pickups.order_id AND (order_details.current_status='Pending Pickup' OR order_details.current_status='Pickuped')");
            $pickup_request = DB::select("SELECT * FROM del_orders where deliverypickup='Pick Up' AND $whereClause AND (status='Pending' OR status='Assigned') ORDER BY STR_TO_DATE(DelDate,'%d-%m-%Y') DESC ");
            $data['pickup_request'] = json_decode(json_encode($pickup_request),true);
			echo "<script>localStorage['filtered']='past_3_days';</script>";
            return view('/DeliveryManagement/PickupRequest',$data);
       }
   }

   // Filter Pickup Request
   public function filterPickupOrder($filter_by)
   {
		if($filter_by =='today')
		{
			$date = date('d-m-Y');
			// $whereClause = "DelDate = '$date'";
			$whereClause = "STR_TO_DATE(DelDate,'%d-%m-%Y') = STR_TO_DATE('$date','%d-%m-%Y')";
			echo "<script>localStorage['filtered']='today';</script>";
		}
		elseif($filter_by =='yesterday')
		{
			$prevDate = date('d-m-Y',strtotime("-1 days"));
			// $whereClause = "DelDate = '$prevDate'";
			$whereClause = "STR_TO_DATE(DelDate,'%d-%m-%Y') = STR_TO_DATE('$prevDate','%d-%m-%Y')";
			echo "<script>localStorage['filtered']='yesterday';</script>";
		}
		elseif($filter_by =='past_3_days')
		{
			$today = date('d-m-Y');
			$past_three_days = date('d-m-Y',strtotime("-2 days"));
			// $whereClause = "DelDate >= '$past_three_days'";
			$whereClause = "STR_TO_DATE(DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$past_three_days','%d-%m-%Y') AND STR_TO_DATE('$today','%d-%m-%Y')";
			echo "<script>localStorage['filtered']='past_3_days';</script>";
		}
		 elseif($filter_by =='week')
		{
			$today = date('d-m-Y');
			$past_three_days = date('d-m-Y',strtotime("-6 days"));
			// $whereClause = "DelDate >= '$past_three_days'";
			$whereClause = "STR_TO_DATE(DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$past_three_days','%d-%m-%Y') AND STR_TO_DATE('$today','%d-%m-%Y')";
			echo "<script>localStorage['filtered']='week';</script>";
		}
		elseif($filter_by =='month')
		{
			$month = date('m-Y');
			$start_date_temp = '01-'.$month;
			$start_date = date('d-m-Y',strtotime($start_date_temp));
			$end_date_temp = '31-'.$month;
			$end_date = date('d-m-Y',strtotime($end_date_temp));
			$whereClause = "STR_TO_DATE(DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y')";
		}
		elseif($filter_by == 'all')
		{
			$pickup_request = DB::select("SELECT * FROM del_orders where deliverypickup='Pick Up' AND (status='Pending' OR status='Assigned') ORDER BY STR_TO_DATE(DelDate,'%d-%m-%Y') DESC ");
            $data['pickup_request'] = json_decode(json_encode($pickup_request),true);
			echo "<script>localStorage['filtered']='all';</script>";
			return view('/DeliveryManagement/PickupRequest',$data);
		}
	   
		$pickup_request = DB::select("SELECT * FROM del_orders where deliverypickup='Pick Up' AND $whereClause AND (status='Pending' OR status='Assigned') ORDER BY STR_TO_DATE(DelDate,'%d-%m-%Y') DESC ");
        $data['pickup_request'] = json_decode(json_encode($pickup_request),true);
		
		return view('/DeliveryManagement/PickupRequest',$data);
   }

   //pickup orders assign del boy
	public function assign_pickup_delboy($order_id)
	{    
		$order_details = DB::select("SELECT * FROM del_orders WHERE order_id = $order_id ");
		$data['order_details'] = json_decode(json_encode($order_details),true);
		//print_r($data['order_details']);
		$delboys = DB::select("SELECT *FROM delusers WHERE role='user'");
		$data['delboys'] = json_decode(json_encode($delboys), true);
		$products = DB::select("SELECT * FROM products");
		$data['products'] = \json_decode(\json_encode($products), true);
		$product_details = DB::select("SELECT 
										order_details.*,
										products.product_name as product_name,
										pickups.id as pickup_main_id,
										pickups.order_details_id as order_details_id
									FROM
										pickups,products,order_details
									Where
										pickups.pickup_order_id = '$order_id'
										AND order_details.id = pickups.order_details_id
										AND order_details.product_id = products.id");
		$data['product_details'] = json_decode(json_encode($product_details),true);

		$total_rent = 0;
		$total_deposit = 0;
		$vendor_details=array();
		foreach($data['product_details'] as $p_details)
		{
			$total_rent += $p_details['product_rent'];
			$total_deposit += $p_details['product_deposite'];
		}
		$data['total_rent'] = $total_rent;
		$data['total_deposit'] = $total_deposit;

		//vendor warehouse details
		for($i=0; $i<count($data['product_details']); $i++)
		{
			$get_vendor_id = $data['product_details'][$i]['vendor_id'];
			$get_warehouse_details = DB::select("SELECT * FROM vendor_warehouse WHERE vendor_id = $get_vendor_id");
			$get_warehouse_details =json_decode(json_encode($get_warehouse_details), true);
			$data['product_details'][$i]['vendor_warehouse_details'] = $get_warehouse_details;
		}
		
		
		//poool details
		// $pool_details = DB::select("SELECT vendor_details.registered_name as registered_name,
		// 								vendor_warehouse.wh_name as wh_name,
		// 								vendor_warehouse.wh_city as wh_city,
		// 								vendor_warehouse.id as id
		// 							FROM
		// 								vendor_details,vendor_warehouse
		// 							Where
		// 								vendor_warehouse.vendor_id=17 
		// 								AND vendor_details.id=vendor_warehouse.vendor_id");
		// $data['pool_details'] = json_decode(json_encode($pool_details),true); 
		
		//dd($data['product_details']);
		//dd($data['pool_details']);
		return view('/DeliveryManagement/AssignPickupDelBoy',$data);
	}

    //assign del boy pickup order post
    public function assign_pickup_delboy_post()
    {
        //print_r($_POST);
        $pickup_order_id = $_POST['pickup_order_id'];
        $order_details_id = $_POST['order_details_id'];
		$pickup_main_id = $_POST['pickup_main_id'];
        $order_id = $_POST['order_id'];
		$vendor_warehouse_id = $_POST['vendor_warehouse_id'];
        //$lead_id = $_POST['lead_id'];
        $del_status=$_POST['del_status'];
        $delivery_type=$_POST['delivery_type'];
        $del_assigned_to=$_POST['del_assigned_to'];
        $invoice_type=$_POST['invoice_type'];
        $travel=$_POST['travel'];
		$helpers=$_POST['helpers'];
        $helpers = json_encode($helpers);
        $payment_mode=$_POST['payment_mode'];
        $total_deposit=$_POST['total_deposit'];
        $total_rent=$_POST['total_rent'];
        $cash_amount = $_POST['cash_amount'];
        $online_amount = $_POST['online_amount'];
        $UpdatedBy=session('username');
        $today = date('Y-m-d');
        $vendor_warehouse_id = $_POST['vendor_warehouse_id'];
            //print_r($drop_at);
        if($payment_mode =='Both')
        {
            $cash_amount = $_POST['cash_amount'];
            $online_amount = $_POST['online_amount'];
        }
        elseif($payment_mode =='Online')
        {
            $cash_amount =0;
            $online_amount =$total_rent;
        }
        elseif($payment_mode =='Cash')
        {
            $cash_amount = $total_rent;
            $online_amount =0;
        }
        if($pickup_order_id!=null)
        {
            $update_pickup_data = [
                'status' => $del_status,
                'helpers' => $helpers,
                'deliverypickup' => $delivery_type,
                'UpdatedBy' => $UpdatedBy,
                'DelAssignedTo' => $del_assigned_to,
                'ReceiptToBeCarried' => $invoice_type,
                'PaymentMode' => $payment_mode,
                'cash' =>$cash_amount,
                'online'=>$online_amount,
                'TravelMode' => $travel,
                'order_approval_status'=>'Approved',
             ];       
             //print_r($update_data);
            $del_order = new DelOrders();     
			$Vendor_Products = new VendorProducts();
			$Pickup = new Pickup();
            $del_order->where('order_id', $pickup_order_id)->update($update_pickup_data);

			for ($i=0; $i <count($order_details_id) ; $i++) { 
				$get_order_details_id = $order_details_id[$i];
				$get_pickup_id = $pickup_main_id[$i];
				$get_order_details_data = DB::select("SELECT * FROM order_details WHERE id ='$get_order_details_id' ");
				$get_order_details_data = json_decode(json_encode($get_order_details_data),true);

				$get_vendor_id = $get_order_details_data[0]['vendor_id'];
				$get_warehouse_id = $vendor_warehouse_id[$i];
				$get_product_id = $get_order_details_data[0]['product_id'];
				$get_product_qty = $get_order_details_data[0]['product_qty'];
				$get_product_brand = $get_order_details_data[0]['product_brand'];
				$get_product_rent = $get_order_details_data[0]['product_rent'];
				$get_product_deposit = $get_order_details_data[0]['product_deposite'];
				$get_unique_id = $get_order_details_data[0]['unique_id'];
				$batch_id = date('Y-m-d')." - ".$get_product_id;
				$check_exist = DB::select("SELECT * FROM vendor_products where vendor_id='$get_vendor_id' AND product_id='$get_product_id' AND warehouse_id='$get_warehouse_id' ");
				$check_exist = json_decode(json_encode($check_exist),true);
				if($check_exist!=null)
				{
					$get_vendor_product_id = $check_exist[0]['id'];
					$final_qty = $check_exist[0]['product_quantity']+$get_product_qty;
					$update_quantity = [
						'product_quantity' =>$final_qty
					];
					$Vendor_Products->where('id',$get_vendor_product_id)->update($update_quantity);
					//update in pickups table
					$update_pickups = [
						'drop_vendor_id' =>$get_vendor_id,
						'drop_warehouse_id'=>$get_warehouse_id,
						'drop_vendor_product_id'=>$get_vendor_product_id
					];
					$Pickup->where('id',$get_pickup_id)->update($update_pickups);

				}
				else
				{
					$insert_record = [
						'vendor_id' => $get_vendor_id,
						'product_id' =>$get_product_id,
						'product_quantity'=>$get_product_qty,
						'product_brand'=>$get_product_brand,
						'product_rent_approved'=>$get_product_rent,
						'product_deposite'=>$get_product_deposit,
						'warehouse_id'=>$get_warehouse_id,
						'status'=>'Approved',
						'virtual_id'=>$get_unique_id,
						'batch'=>$batch_id,
						'created_at'=>date('Y-m-d H:i:s')
					];	
					$get_vendor_product_id = $Vendor_Products->insertGetId($insert_record);
					//update in pickups table
					$update_pickups = [
						'drop_vendor_id' =>$get_vendor_id,
						'drop_warehouse_id'=>$get_warehouse_id,
						'drop_vendor_product_id'=>$get_vendor_product_id,
						'updated_at' =>date('Y-m-d H:i:s')
					];
					$Pickup->where('id',$get_pickup_id)->update($update_pickups);
				}

			}
            // for ($i=0; $i <count($drop_at) ; $i++) 
            // { 
			// 	if($drop_at[$i]['type']=='Vendor')
			// 	{
			// 		//vendor inverntory management
			// 		$get_vendor_id = $drop_at[$i]['id'];
			// 		$vendor_details = DB::select("SELECT vendor_id,vendor_product_id,vendor_warehouse_id,product_qty,rented_product_id FROM order_details WHERE order_id='$order_id[$i]' AND id='$order_details_id[$i]' AND vendor_id = '$get_vendor_id' ");
			// 		$data['vendor_details'] = json_decode(json_encode($vendor_details),true);
			// 		//print_r($data['vendor_details']);
			// 		$vendor_product_id = $data['vendor_details'][0]['vendor_product_id'];
			// 		$vendor_warehouse_id = $data['vendor_details'][0]['vendor_warehouse_id'];
			// 		$rented_product_id = $data['vendor_details'][0]['rented_product_id'];
			// 		$product_qty = $data['vendor_details'][0]['product_qty'];

			// 		// get vendor inverntory
			// 		$get_product_qty = DB::select("SELECT product_quantity FROM vendor_products WHERE vendor_id ='$get_vendor_id' AND id = '$vendor_product_id' ");
			// 		$data['get_product_qty'] = json_decode(json_encode($get_product_qty),true);
			// 		$final_producty_qty = $data['get_product_qty'][0]['product_quantity'] + $product_qty;
			// 		//update vendor inverntory
			// 		$get_product_qty = DB::update("UPDATE vendor_products SET product_quantity='$final_producty_qty' WHERE vendor_id ='$get_vendor_id' AND id = '$vendor_product_id' ");
						
			// 		//vendor rented product status change
			// 		DB::UPDATE("UPDATE vendor_rented_products SET status='Released' WHERE  id = '$rented_product_id' ");
			// 		//DB::UPDATE("UPDATE vendor_rented_products SET status='Released' WHERE vendor_id = '$get_vendor_id' AND id = '$rented_product_id' ");
			// 		//update in pickups table for drop_at_id and drop_location
			// 		DB::UPDATE("UPDATE pickups SET drop_at_id='$get_vendor_id',drop_at_location='Vendor' WHERE id='$pickup_main_id[$i]' ");
			// 	}
			// 	elseif($drop_at[$i]['type']=='Pools')
			// 	{
			// 		$q5c_pools = new q5c_pools_products();
			// 		$virtual_poolwarehouse_id = $drop_at[$i]['id'];
			// 		$get_vendor_id = $order_details_vendor_id[$i];
			// 		$get_vendor_details = DB::select("SELECT rented_product_id,vendor_product_id,product_id	 FROM order_details WHERE order_id='$order_id[$i]' AND id='$order_details_id[$i]' AND vendor_id = '$get_vendor_id' ");
			// 		$data['get_vendor_details'] = json_decode(json_encode($get_vendor_details),true);
			// 		//print_r($data['get_vendor_details']);	
			// 		$rented_product_id = $data['get_vendor_details'][0]['rented_product_id'];
			// 		$vendor_product_id = $data['get_vendor_details'][0]['vendor_product_id'];
			// 		$virtual_product_id = $drop_at[$i]['product_id'];
			// 		$pool_data = [
			// 			'pickup_order_id'=>$pickup_order_id,
			// 			'vendor_id' => $get_vendor_id,
			// 			'product_id'=>$virtual_product_id,
			// 			'virtual_pool_id' =>$virtual_poolwarehouse_id,
			// 			'vendor_product_id' => $vendor_product_id,
			// 			'vendor_rented_product_id' => $rented_product_id
			// 		];
					
			// 		/*virtual pool id mean quali55care warehouse id*/ 
			// 		$get_pool_id = $q5c_pools->insertGetId($pool_data); 

			// 		//update in pickups table for drop_at_id and drop_location
			// 		DB::UPDATE("UPDATE pickups SET drop_at_id='$get_pool_id',drop_at_location='Pools' WHERE id='$pickup_main_id[$i]' ");
			// 	}
			// 	else
			// 	{
			// 		return redirect()->back()->with('error','smoething went wrong');
			// 	}

			// 	//order_details table change status pickuped
			// 	DB::UPDATE("UPDATE order_details SET current_status='Pickuped' WHERE id = '$order_details_id[$i]' ");
				
			// 	//update in pickups table for drop_at_id and drop_location
			// 	DB::UPDATE("UPDATE pickups SET status='DelBoy Assigned' WHERE id='$pickup_main_id[$i]' ");
            // }


            //  DB::UPDATE("UPDATE leads SET lead_status = 'Order Generated' WHERE id = $lead_id");
            //  $leads_log = new leads_log();
            //  $leads_log_data = [
            //     'log_lead_id' => $lead_id,
            //     'log_lead_status' => 'Order Generated',
            //     'log_date' => date('Y-m-d'),
            //     'log_time' => date('H:i:s'),
            //     'updated_by' => session('username')
            // ];
        }
        return redirect('/pickup_request')->with('message','Pickup Order Generated sucessfully');
    }

	//Modify Pickup Request
	public function ModifyPickup($order_id)
	{
		if($_SERVER['REQUEST_METHOD']=='GET')
		{
			$order_details = DB::select("SELECT * FROM del_orders WHERE order_id = $order_id ");
			$data['order_details'] = json_decode(json_encode($order_details),true);
			//print_r($data['order_details']);
			$delboys = DB::select("SELECT *FROM delusers WHERE role='user'");
			$data['delboys'] = json_decode(json_encode($delboys), true);
			$products = DB::select("SELECT * FROM products");
			$data['products'] = \json_decode(\json_encode($products), true);
			$product_details = DB::select("SELECT 
											order_details.*,
											products.product_name as product_name,
											pickups.id as pickup_main_id,
											pickups.order_details_id as order_details_id,
											pickups.drop_vendor_id as pickup_vendor_id,
											pickups.drop_warehouse_id as pickup_warehouse_id,
											pickups.drop_vendor_product_id as pickup_vendor_product_id
										FROM
											pickups,products,order_details
										Where
											pickups.pickup_order_id = '$order_id'
											AND order_details.id = pickups.order_details_id
											AND order_details.product_id = products.id");
			$data['product_details'] = json_decode(json_encode($product_details),true);
	
			$total_rent = 0;
			$total_deposit = 0;
			$vendor_details=array();
			foreach($data['product_details'] as $p_details)
			{
				$total_rent += $p_details['product_rent'];
				$total_deposit += $p_details['product_deposite'];
			}
			$data['total_rent'] = $total_rent;
			$data['total_deposit'] = $total_deposit;
	
			//vendor warehouse details
			for($i=0; $i<count($data['product_details']); $i++)
			{
				$get_vendor_id = $data['product_details'][$i]['vendor_id'];
				$get_warehouse_details = DB::select("SELECT * FROM vendor_warehouse WHERE vendor_id = $get_vendor_id");
				$get_warehouse_details =json_decode(json_encode($get_warehouse_details), true);
				$data['product_details'][$i]['vendor_warehouse_details'] = $get_warehouse_details;
			}
			return view('/DeliveryManagement/ModifyPickup',$data);
		}

		if($_SERVER['REQUEST_METHOD']=='POST')
		{
			//print_r($_POST);
			$pickup_order_id = $_POST['pickup_order_id'];
			$order_details_id = $_POST['order_details_id'];
			$pickup_main_id = $_POST['pickup_main_id'];
			$order_id = $_POST['order_id'];
			$vendor_warehouse_id = $_POST['vendor_warehouse_id'];
			$pickup_vendor_id = $_POST['pickup_vendor_id'];
			$pickup_warehouse_id = $_POST['pickup_warehouse_id'];
			$pickup_vendor_product_id = $_POST['pickup_vendor_product_id'];
			//$lead_id = $_POST['lead_id'];
			$del_status=$_POST['del_status'];
			$delivery_type=$_POST['delivery_type'];
			$del_assigned_to=$_POST['del_assigned_to'];
			$invoice_type=$_POST['invoice_type'];
			$travel=$_POST['travel'];
			$helpers=$_POST['helpers'];
			$helpers = json_encode($helpers);
			$payment_mode=$_POST['payment_mode'];
			$total_deposit=$_POST['total_deposit'];
			$total_rent=$_POST['total_rent'];
			$cash_amount = $_POST['cash_amount'];
			$online_amount = $_POST['online_amount'];
			$UpdatedBy=session('username');
			$today = date('Y-m-d');
			$vendor_warehouse_id = $_POST['vendor_warehouse_id'];
				//print_r($drop_at);
			if($payment_mode =='Both')
			{
				$cash_amount = $_POST['cash_amount'];
				$online_amount = $_POST['online_amount'];
			}
			elseif($payment_mode =='Online')
			{
				$cash_amount =0;
				$online_amount =$total_rent;
			}
			elseif($payment_mode =='Cash')
			{
				$cash_amount = $total_rent;
				$online_amount =0;
			}
			if($pickup_order_id!=null)
			{
				$update_pickup_data = [
					'status' => $del_status,
					'helpers' => $helpers,
					'deliverypickup' => $delivery_type,
					'UpdatedBy' => $UpdatedBy,
					'DelAssignedTo' => $del_assigned_to,
					'ReceiptToBeCarried' => $invoice_type,
					'PaymentMode' => $payment_mode,
					'cash' =>$cash_amount,
					'online'=>$online_amount,
					'TravelMode' => $travel,
					'order_approval_status'=>'Approved',
				 ];       
				 //print_r($update_data);
				$del_order = new DelOrders();     
				$Vendor_Products = new VendorProducts();
				$Pickup = new Pickup();
				$del_order->where('order_id', $pickup_order_id)->update($update_pickup_data);
	
				for ($i=0; $i <count($order_details_id) ; $i++) { 
					$get_order_details_id = $order_details_id[$i];
					$get_pickup_id = $pickup_main_id[$i];
					$get_order_details_data = DB::select("SELECT * FROM order_details WHERE id ='$get_order_details_id' ");
					$get_order_details_data = json_decode(json_encode($get_order_details_data),true);
	
					$get_vendor_id = $get_order_details_data[0]['vendor_id'];
					$get_warehouse_id = $vendor_warehouse_id[$i];
					$get_product_id = $get_order_details_data[0]['product_id'];
					$get_product_qty = $get_order_details_data[0]['product_qty'];
					$get_product_brand = $get_order_details_data[0]['product_brand'];
					$get_product_rent = $get_order_details_data[0]['product_rent'];
					$get_product_deposit = $get_order_details_data[0]['product_deposite'];
					$get_unique_id = $get_order_details_data[0]['unique_id'];
					$batch_id = date('Y-m-d')." - ".$get_product_id;
					
					if($get_warehouse_id!=$pickup_warehouse_id[$i])
					{
						//decrease inventory from previous data
						$temp_pick_vid = $pickup_vendor_id[$i];
						$temp_pick_wid = $pickup_warehouse_id[$i];
						$temp_pick_vpid = $pickup_vendor_product_id[$i];
						$get_qty = DB::select("SELECT product_quantity FROM vendor_products where id='$temp_pick_vpid' ");
						$get_qty = json_decode(json_encode($get_qty),true);
						
						$final_qty = $get_qty[0]['product_quantity']-$get_product_qty;
						$update_prev_quantity = [
							'product_quantity' =>$final_qty
						];
						$Vendor_Products->where('id',$temp_pick_vpid)->update($update_prev_quantity);
						//clode deceres inventory

						$check_exist = DB::select("SELECT * FROM vendor_products where vendor_id='$get_vendor_id' AND product_id='$get_product_id' AND warehouse_id='$get_warehouse_id' ");
						$check_exist = json_decode(json_encode($check_exist),true);
						if($check_exist!=null)
						{
							$get_vendor_product_id = $check_exist[0]['id'];
							$final_qty = $check_exist[0]['product_quantity']+$get_product_qty;
							$update_quantity = [
								'product_quantity' =>$final_qty
							];
							$Vendor_Products->where('id',$get_vendor_product_id)->update($update_quantity);
							//update in pickups table
							$update_pickups = [
								'drop_vendor_id' =>$get_vendor_id,
								'drop_warehouse_id'=>$get_warehouse_id,
								'drop_vendor_product_id'=>$get_vendor_product_id
							];
							$Pickup->where('id',$get_pickup_id)->update($update_pickups);
		
						}
						else
						{
							$insert_record = [
								'vendor_id' => $get_vendor_id,
								'product_id' =>$get_product_id,
								'product_quantity'=>$get_product_qty,
								'product_brand'=>$get_product_brand,
								'product_rent_approved'=>$get_product_rent,
								'product_deposite'=>$get_product_deposit,
								'warehouse_id'=>$get_warehouse_id,
								'status'=>'Approved',
								'virtual_id'=>$get_unique_id,
								'batch'=>$batch_id,
								'created_at'=>date('Y-m-d H:i:s')
							];	
							$get_vendor_product_id = $Vendor_Products->insertGetId($insert_record);
							//update in pickups table
							$update_pickups = [
								'drop_vendor_id' =>$get_vendor_id,
								'drop_warehouse_id'=>$get_warehouse_id,
								'drop_vendor_product_id'=>$get_vendor_product_id,
								'updated_at' =>date('Y-m-d H:i:s')
							];
							$Pickup->where('id',$get_pickup_id)->update($update_pickups);
						}
					}
					// else
					// {
					// 	$temp_pick_vid = $pickup_vendor_id[$i];
					// 	$temp_pick_wid = $pickup_warehouse_id[$i];
					// 	$temp_pick_vpid = $pickup_vendor_product_id[$i];
					// 	$check_exist = DB::select("SELECT * FROM vendor_products where vendor_id='$temp_pick_vid' AND product_id='$get_product_id' AND warehouse_id='$temp_pick_wid' ");
					// 	$check_exist = json_decode(json_encode($check_exist),true);
					// 	if($check_exist!=null)
					// 	{
					// 		$get_vendor_product_id = $check_exist[0]['id'];
					// 		$final_qty = $check_exist[0]['product_quantity']+$get_product_qty;
					// 		$update_quantity = [
					// 			'product_quantity' =>$final_qty
					// 		];
					// 		$Vendor_Products->where('id',$get_vendor_product_id)->update($update_quantity);
					// 		//update in pickups table
					// 		$update_pickups = [
					// 			'drop_vendor_id' =>$get_vendor_id,
					// 			'drop_warehouse_id'=>$get_warehouse_id,
					// 			'drop_vendor_product_id'=>$get_vendor_product_id
					// 		];
					// 		$Pickup->where('id',$get_pickup_id)->update($update_pickups);
		
					// 	}
					// }
	
				}
				
			}
			return redirect('/pickup_request')->with('message','Pickup Order Modified sucessfully');
		}
	   
	}

	//renew requested orders

	public function renew_request()
	{
		if($_SERVER['REQUEST_METHOD']=='GET')
		{
			$today = date('d-m-Y');
			$past_three_days = date('d-m-Y',strtotime("-2 days"));
			$whereClause = "STR_TO_DATE(DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$past_three_days','%d-%m-%Y') AND STR_TO_DATE('$today','%d-%m-%Y')";
			$collection_request = DB::select("SELECT * FROM del_orders where deliverypickup='Collection' AND $whereClause AND (status='Pending' OR status='Assigned') AND PaymentMode='Cash' ORDER BY STR_TO_DATE(DelDate,'%d-%m-%Y') DESC ");
			$data['collection_request'] = json_decode(json_encode($collection_request),true);
			echo "<script>localStorage['filtered']='past_3_days';</script>";
			return view('/DeliveryManagement/RenewRequest',$data);
		}
	}
	public function filterCollectionOrder($filter_by)
   {
		if($filter_by =='today')
		{
			$date = date('d-m-Y');
			// $whereClause = "DelDate = '$date'";
			$whereClause = "STR_TO_DATE(DelDate,'%d-%m-%Y') = STR_TO_DATE('$date','%d-%m-%Y')";
			echo "<script>localStorage['filtered']='today';</script>";
		}
		elseif($filter_by =='yesterday')
		{
			$prevDate = date('d-m-Y',strtotime("-1 days"));
			// $whereClause = "DelDate = '$prevDate'";
			$whereClause = "STR_TO_DATE(DelDate,'%d-%m-%Y') = STR_TO_DATE('$prevDate','%d-%m-%Y')";
			echo "<script>localStorage['filtered']='yesterday';</script>";
		}
		elseif($filter_by =='past_3_days')
		{
			$today = date('d-m-Y');
			$past_three_days = date('d-m-Y',strtotime("-2 days"));
			// $whereClause = "DelDate >= '$past_three_days'";
			$whereClause = "STR_TO_DATE(DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$past_three_days','%d-%m-%Y') AND STR_TO_DATE('$today','%d-%m-%Y')";
			echo "<script>localStorage['filtered']='past_3_days';</script>";
		}
		 elseif($filter_by =='week')
		{
			$today = date('d-m-Y');
			$past_three_days = date('d-m-Y',strtotime("-6 days"));
			// $whereClause = "DelDate >= '$past_three_days'";
			$whereClause = "STR_TO_DATE(DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$past_three_days','%d-%m-%Y') AND STR_TO_DATE('$today','%d-%m-%Y')";
			echo "<script>localStorage['filtered']='week';</script>";
		}
		elseif($filter_by =='month')
		{
			$month = date('m-Y');
			$start_date_temp = '01-'.$month;
			$start_date = date('d-m-Y',strtotime($start_date_temp));
			$end_date_temp = '31-'.$month;
			$end_date = date('d-m-Y',strtotime($end_date_temp));
			$whereClause = "STR_TO_DATE(DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y')";
		}
		elseif($filter_by == 'all')
		{
			$collection_request = DB::select("SELECT * FROM del_orders where deliverypickup='Collection' AND (status='Pending' OR status='Assigned') AND PaymentMode='Cash' ORDER BY STR_TO_DATE(DelDate,'%d-%m-%Y') DESC ");
        $data['collection_request'] = json_decode(json_encode($collection_request),true);
			echo "<script>localStorage['filtered']='all';</script>";
			return view('/DeliveryManagement/RenewRequest',$data);
		}
	   
		$collection_request = DB::select("SELECT * FROM del_orders where deliverypickup='Collection' AND $whereClause AND (status='Pending' OR status='Assigned') AND PaymentMode='Cash' ORDER BY STR_TO_DATE(DelDate,'%d-%m-%Y') DESC ");
        $data['collection_request'] = json_decode(json_encode($collection_request),true);
		
		return view('/DeliveryManagement/RenewRequest',$data);
   }


   public function send_del_reminder($customer_id,$order_id)
   {
		$customer_details = new customer_detail();
		$get_email =  DB::select("SELECT email_id,customer_name FROM customer_details where cust_id = '$customer_id' ");
		$data['get_email'] = json_decode(json_encode($get_email),true);
		$email_id = $data['get_email'][0]['email_id'];
		$customer_name = $data['get_email'][0]['customer_name'];
			
			$data_message = array(
					'customer_email'=>$email_id,
					'customer_name'=>$customer_name,
					);

			//    Sending mail to customer about renewal of rental product....
			Mail::send('DeliveryManagementMail/del_reminder_mail',$data_message, function($message) use ($email_id)
			{     
				$message->to($email_id, 'Renew Payment Pending Reminder')->subject('Renew Payment Pending Reminder');
				$message->from('tempmailquali@gmail.com', 'Quali55Care');
			});

		 // //-----Comment added-----//
		 // $timestamp = date("d M, h:i A");
		 // $comment = "[".$timestamp."]Reminder sent for product : ".$product_name."\n";
		 // $comment_status = [
		 //       'comment' => $comment
		 // ];
		 // $cmt_check = DB::select("SELECT comment FROM customer_details WHERE cust_id = '$cust_id' ");
		 // $data['cmt_check'] = json_decode(json_encode($cmt_check), true);
		 
		 // if(isset($data['cmt_check'][0]['comment']))
		 // {
		 //       //$cmt_update = "UPDATE customer_details SET comment = CONCAT(comment, '$comment') WHERE cust_id = '$cust_id' ";
		 //       $cmt_update = DB::update("UPDATE customer_details SET comment = CONCAT('$comment',comment) WHERE cust_id = '$cust_id' ");
			   
		 //       //$customer_details->where('cust_id',$customer_id)->update($comment_status);
		 // }
		 // else
		 // {
		 //       //print_r($comment_status);
		 //       $customer_details->where('cust_id',$cust_id)->update($comment_status);
		 // }
		 return redirect()->back()->with('pop_message',$email_id)->with('pop_cust_name',$customer_name);
   }

   //pickup orders assign del boy
   public function assign_collection_delboy($order_id)
   {    
		$order_details = DB::select("SELECT * FROM del_orders WHERE order_id = $order_id ");
		$data['order_details'] = json_decode(json_encode($order_details),true);
		//print_r($data['order_details']);
		$delboys = DB::select("SELECT *FROM delusers WHERE role='user'");
		$data['delboys'] = json_decode(json_encode($delboys), true);
		$products = DB::select("SELECT * FROM products");
		$data['products'] = \json_decode(\json_encode($products), true);
		$product_details = DB::select("SELECT 
										order_details.*,
										products.product_name as product_name,
										renewals.id as renewal_main_id,
										renewals.order_details_id as order_details_id
									FROM
										renewals,products,order_details
									Where
										renewals.collection_order_id = '$order_id'
										AND order_details.id = renewals.order_details_id
										AND order_details.product_id = products.id");
		$data['product_details'] = json_decode(json_encode($product_details),true);
		$total_rent = 0;
		$total_deposit = 0;
		$vendor_details=array();
		foreach($data['product_details'] as $p_details)
		{
			$total_rent += $p_details['product_rent'];
			$total_deposit += $p_details['product_deposite'];
			$vendor_id = $p_details['vendor_id'];
		}
		
		$data['total_rent'] = $total_rent;
		$data['total_deposit'] = $total_deposit;
		// print_r( $data['product_details']);
		return view('/DeliveryManagement/AssignCollectionDelBoy',$data);
	}

	//assign del boy collection order post
	public function assign_collection_delboy_post($order_id)
	{
		$collection_order_id = $_POST['collection_order_id'];
		$order_details_id = $_POST['order_details_id'];
		$renewal_main_id = $_POST['renewal_main_id'];
		$order_id = $_POST['order_id'];
		//$lead_id = $_POST['lead_id'];
		$del_status=$_POST['del_status'];
		$delivery_type=$_POST['delivery_type'];
		$del_assigned_to=$_POST['del_assigned_to'];
		$invoice_type=$_POST['invoice_type'];
		$travel=$_POST['travel'];
		$helpers=$_POST['helpers'];
		$helpers = json_encode($helpers);
		$payment_mode=$_POST['payment_mode'];
		//$total_deposit=$_POST['total_deposit'];
		$total_rent=$_POST['total_rent'];
		$cash_amount = $_POST['cash_amount'];
		$online_amount = $_POST['online_amount'];
		$UpdatedBy=session('username');
		$today = date('Y-m-d');
		//$drop_at = $_POST['Drop_at'];
			//print_r($drop_at);
		if($payment_mode =='Both')
		{
			$cash_amount = $_POST['cash_amount'];
			$online_amount = $_POST['online_amount'];
		}
		elseif($payment_mode =='Online')
		{
			$cash_amount =0;
			$online_amount =$total_rent;
		}
		elseif($payment_mode =='Cash')
		{
			$cash_amount = $total_rent;
			$online_amount =0;
		}
		if($collection_order_id!=null)
		{
			$update_collection_data = [
				'status' => $del_status,
				'helpers' => $helpers,
				'deliverypickup' => $delivery_type,
				'UpdatedBy' => $UpdatedBy,
				'DelAssignedTo' => $del_assigned_to,
				'ReceiptToBeCarried' => $invoice_type,
				'PaymentMode' => $payment_mode,
				'cash' =>$cash_amount,
				'online'=>$online_amount,
				'TravelMode' => $travel,
				'order_approval_status'=>'Approved',
			 ];       
			 //print_r($update_data);
			$Renew = new Renewal();
			$DelOrder = new DelOrders();
			$Order_Details = new OrderDetails();  
			$DelOrder->where('order_id', $collection_order_id)->update($update_collection_data);
			 //update in order_details table and renewals table data
			for ($i=0; $i <count($order_details_id); $i++) 
			{ 
				$update_order_details_data = [
					'collection_date' =>$today,
					'current_status' =>'Renewed'
				];
				$Order_Details->where('id',$order_details_id[$i])->update($update_order_details_data);
				$update_renewals_data = [
					'status' =>'DelBoy Assigned',
					'payment_status' =>'Recieved'
				];
				$Renew->where('id',$renewal_main_id[$i])->update($update_renewals_data);
			}


			//  DB::UPDATE("UPDATE leads SET lead_status = 'Order Generated' WHERE id = $lead_id");
			//  $leads_log = new leads_log();
			//  $leads_log_data = [
			//     'log_lead_id' => $lead_id,
			//     'log_lead_status' => 'Order Generated',
			//     'log_date' => date('Y-m-d'),
			//     'log_time' => date('H:i:s'),
			//     'updated_by' => session('username')
			// ];
		}
		return redirect('/renew_request')->with('message','Collection Order Generated sucessfully');
	}

	//Modify Collection Request
	public function ModifyCollection($order_id)
	{
		if($_SERVER['REQUEST_METHOD']=='GET')
		{
			$order_details = DB::select("SELECT * FROM del_orders WHERE order_id = $order_id ");
			$data['order_details'] = json_decode(json_encode($order_details),true);
			//print_r($data['order_details']);
			$delboys = DB::select("SELECT *FROM delusers WHERE role='user'");
			$data['delboys'] = json_decode(json_encode($delboys), true);
			$products = DB::select("SELECT * FROM products");
			$data['products'] = \json_decode(\json_encode($products), true);
			$product_details = DB::select("SELECT 
											order_details.*,
											products.product_name as product_name,
											renewals.order_details_id as order_details_id,
											renewals.id as renewal_main_id
										FROM
											renewals,products,order_details
										Where
											renewals.collection_order_id = '$order_id'
											AND order_details.id = renewals.order_details_id
											AND order_details.product_id = products.id");
			$data['product_details'] = json_decode(json_encode($product_details),true);
			$total_rent = 0;
			$total_deposit = 0;
			$vendor_details=array();
			foreach($data['product_details'] as $p_details)
			{
				$total_rent += $p_details['product_rent'];
				$total_deposit += $p_details['product_deposite'];
			}
			
			//pool set already
			$data['total_rent'] = $total_rent;
			$data['total_deposit'] = $total_deposit;
			//print_r($data['product_details']);
			return view('/DeliveryManagement/ModifyCollection',$data);
		}

		if($_SERVER['REQUEST_METHOD']=='POST')
		{
			$collection_order_id = $_POST['collection_order_id'];
			$order_details_id = $_POST['order_details_id'];
			$renewal_main_id = $_POST['renewal_main_id'];
			$order_id = $_POST['order_id'];
			//$lead_id = $_POST['lead_id'];
			$del_status=$_POST['del_status'];
			$delivery_type=$_POST['delivery_type'];
			$del_assigned_to=$_POST['del_assigned_to'];
			$invoice_type=$_POST['invoice_type'];
			$travel=$_POST['travel'];
			$helpers=$_POST['helpers'];
			$helpers = json_encode($helpers);
			$payment_mode=$_POST['payment_mode'];
			//$total_deposit=$_POST['total_deposit'];
			$total_rent=$_POST['total_rent'];
			$cash_amount = $_POST['cash_amount'];
			$online_amount = $_POST['online_amount'];
			$UpdatedBy=session('username');
			$today = date('Y-m-d');
			//$drop_at = $_POST['Drop_at'];
				//print_r($drop_at);
			if($payment_mode =='Both')
			{
				$cash_amount = $_POST['cash_amount'];
				$online_amount = $_POST['online_amount'];
			}
			elseif($payment_mode =='Online')
			{
				$cash_amount =0;
				$online_amount =$total_rent;
			}
			elseif($payment_mode =='Cash')
			{
				$cash_amount = $total_rent;
				$online_amount =0;
			}
			if($collection_order_id!=null)
			{
				$update_collection_data = [
					'status' => $del_status,
					'helpers' => $helpers,
					'deliverypickup' => $delivery_type,
					'UpdatedBy' => $UpdatedBy,
					'DelAssignedTo' => $del_assigned_to,
					'ReceiptToBeCarried' => $invoice_type,
					'PaymentMode' => $payment_mode,
					'cash' =>$cash_amount,
					'online'=>$online_amount,
					'TravelMode' => $travel,
					'order_approval_status'=>'Approved',
				 ];       
				 //print_r($update_data);
				$Renew = new Renewal();
				$DelOrder = new DelOrders();
				$Order_Details = new OrderDetails();  
				$DelOrder->where('order_id', $collection_order_id)->update($update_collection_data);
				 //update in order_details table and renewals table data
				for ($i=0; $i <count($order_details_id); $i++) 
				{ 
					$update_order_details_data = [
						'collection_date' =>$today,
						'current_status' =>'Renewed'
					];
					$Order_Details->where('id',$order_details_id[$i])->update($update_order_details_data);
					$update_renewals_data = [
						'status' =>'DelBoy Assigned',
						'payment_status' =>'Recieved'
					];
					$Renew->where('id',$renewal_main_id[$i])->update($update_renewals_data);
				}
	
	
				//  DB::UPDATE("UPDATE leads SET lead_status = 'Order Generated' WHERE id = $lead_id");
				//  $leads_log = new leads_log();
				//  $leads_log_data = [
				//     'log_lead_id' => $lead_id,
				//     'log_lead_status' => 'Order Generated',
				//     'log_date' => date('Y-m-d'),
				//     'log_time' => date('H:i:s'),
				//     'updated_by' => session('username')
				// ];
			}
			return redirect('/renew_request')->with('message','Collection Order Updated sucessfully');
		}
	   
	}

	public function order_feedback()
	{
		$today = date('d-m-Y');
		$feedback_info = DB::select("SELECT * FROM del_orders WHERE cust_sign IS NOT NULL AND DelDate = '$today' ");
			$data['feedback_info'] = json_decode(json_encode($feedback_info),true);
		echo "<script>localStorage['filtered']='today';</script>";
		return view('/DeliveryManagement/OrderFeedback',$data);
	}

	  public function perticular_feedback($order_id)
	  {
		 $perticular_feedback = DB::select("SELECT * FROM del_orders WHERE order_id = '$order_id' ");
			$data['perticular_feedback'] = json_decode(json_encode($perticular_feedback),true);
		 //print_r($data['perticular_feedback']);
		 return view('/DeliveryManagement/PerticularFeedback',$data);
	  }

		 public function filterFeedback($filter_by)
		{
			if($filter_by =='today')
			{
				$date = date('d-m-Y');
				$whereClause = "DelDate = '$date'";
				echo "<script>localStorage['filtered']='today';</script>";
			}
			elseif($filter_by =='yesterday')
			{
				$prevDate = date('d-m-Y',strtotime("-1 days"));
				$whereClause = "DelDate = '$prevDate'";
				echo "<script>localStorage['filtered']='yesterday';</script>";
			}
			elseif($filter_by =='past_3_days')
			{
				$past_three_days = date('d-m-Y',strtotime("-2 days"));
				$whereClause = "DelDate >= '$past_three_days'";
				echo "<script>localStorage['filtered']='past_3_days';</script>";
			}
			elseif($filter_by =='week')
			{
				$past_three_days = date('d-m-Y',strtotime("-6 days"));
				$whereClause = "DelDate >= '$past_three_days'";
				echo "<script>localStorage['filtered']='week';</script>";
			}
			elseif($filter_by =='month')
			{
				$month = date('m-Y');
				$start_date_temp = '01-'.$month;
				$start_date = date('d-m-Y',strtotime($start_date_temp));
				$end_date_temp = '31-'.$month;
				$end_date = date('d-m-Y',strtotime($end_date_temp));
				$whereClause = "DelDate BETWEEN '$start_date' AND '$end_date'";
			}
			elseif($filter_by == 'all')
			{   
				$feedback_info = DB::select("SELECT * FROM del_orders WHERE cust_sign IS NOT NULL");
				$data['feedback_info'] = json_decode(json_encode($feedback_info),true);
				echo "<script>localStorage['filtered']='all';</script>";
				return view('/DeliveryManagement/OrderFeedback',$data);
			}
		
			$feedback_info = DB::select("SELECT * FROM del_orders WHERE cust_sign IS NOT NULL AND $whereClause ");
			//echo "SELECT * FROM del_orders WHERE cust_sign IS NOT NULL AND $whereClause ";
			$data['feedback_info'] = json_decode(json_encode($feedback_info),true);
			
			return view('/DeliveryManagement/OrderFeedback',$data);
		}


}
