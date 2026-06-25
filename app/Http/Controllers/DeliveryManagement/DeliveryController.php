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
use App\Models\VendorProductDetails;
use App\Models\VendorRentedProducts;
use App\Models\ActivityLog;
use App\Models\VirtualVdrInventoryMgmt;
use App\Models\TempCustDetails;
use Illuminate\Http\Request;
use App\Models\leads_log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\User\UserController;
use PDF;
use Mail;
use Session;
use Carbon\Carbon;
use App\Http\Controllers\RenewalPickup\RenewalPickupController;
use App\Http\Controllers\OrderManagement\EditOrderController;


class DeliveryController extends Controller
{

	public function rejectedOrders(Request $request){
		$orders = DB::table('del_orders')
			->join('leads','leads.id','=','del_orders.lead_id')
			->join('user','user.id','=','leads.lead_owner')
			->select('del_orders.*','leads.lead_owner','user.username','del_orders.created_at as order_created_at')
			->whereNotIn('del_orders.status',['Pending','Cancel'])
			->when($request->get('filterCustomerNameNumber'),function($query)use($request){
				$query->where(function($q)use($request) {
					$q->where('del_orders.shipping_first_name','LIKE','%'.$request->get('filterCustomerNameNumber').'%');
					$q->orWhere('del_orders.mobileno','LIKE','%'.$request->get('filterCustomerNameNumber').'%');
				});
			})
			->when($request->get('filterOrderId'),function($query)use($request){
				$query->where('del_orders.order_id',$request->get('filterOrderId'));
			})
			->when($request->get('filterStartDate') && $request->get('filterEndDate'),function($query)use($request){
				$startDate = date('d-m-Y',strtotime($request->get('filterStartDate')));
				$endDate = date('d-m-Y',strtotime($request->get('filterEndDate')));
				$query->whereBetween(DB::raw("STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y')"),[DB::raw("STR_TO_DATE('$startDate','%d-%m-%Y')"),DB::raw("STR_TO_DATE('$endDate','%d-%m-%Y')")]);
			})
			->orderBy('del_orders.order_id','Desc')
			->paginate(10);
		return view('DeliveryManagement.rejected-orders',compact('orders'));
	}

	public function rejectedOrdersUpdate(Request $request){
		DB::beginTransaction();
		try{
			$status = $request->get('statusUpdate');
			if($request->get('statusUpdate') == 'Completed'){
				$orderDetails = DB::table('del_orders')->where('order_id',$request->get('updateorderid'))->first();
				$status = ($orderDetails->deliverypickup == 'Delivery'?'Delivered':($orderDetails->deliverypickup == 'Collection'?'Collected':($orderDetails->deliverypickup == 'Pick Up'?'Picked up':'Completed')));
				if($orderDetails->deliverypickup == 'Pick Up'){
					$pickedup_order_ids = DB::table('pickups')->where('pickup_order_id',$request->get('updateorderid'))->whereNull('status')->get()->pluck('order_details_id');
					DB::table('order_details')->whereIn('id',$pickedup_order_ids)->update(['current_status'=>'Picked Up']);
				}
				if($orderDetails->deliverypickup  == 'Collection' && $orderDetails->ccadflag !="CCAD"){
					$collectionDetails = DB::table('renewals')->where('collection_order_id',$request->get('updateorderid'))->whereNotIn('status',['Cancel'])->orderBy('id','ASC')->get();
					foreach($collectionDetails as $key=>$value){
						DB::table('order_details')->where('id',$value->order_details_id)->update(['pickup_date'=>$value->end_date,'current_status'=>'Renewed']);
					}
					$updateData['status'] = 'Collected';
				}
			}
			DB::table('del_orders')->where('order_id',$request->get('updateorderid'))->update(['DelAssignedTo'=>$request->get('taskAssignedTo'),'status'=>$status]);
			DB::commit();
			return redirect()->back()->with('message','Order Assigned Successfully!');
		}catch(Exception $ex){
			DB::rollback();
			return redirect()->back()->with('error',$ex->getMessage);
		}
	}

   public function isLoggedIn()
   {
	  $data = session('isLoggedIn');
	  //print_r($data);      
	  return $data;
   }
   public function AddDelivery()
   {
		$isLoggedIn = $this->isLoggedIn();
		if($isLoggedIn == 'false')
		{
			$url = url('/');
			return redirect()->to($url);
		}
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
    //   $orders = DB::select("SELECT * FROM del_orders WHERE lead_id IS NOT NULL AND order_approval_status !='Pending' AND status!='Pending' order by order_id DESC");
    //   $data['orders'] = json_decode(json_encode($orders),true);
    //   return view('/DeliveryManagement/ModifyDeliveryView',$data);
	return redirect('modifyDeliveryFilter/today');
   }
   public function modifyDeliveryFilter($filter_by)
   {
		$orderTypeNotIn = config('app.order_type');        
		// $orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";
		if($filter_by =='today')
		{
			$date = date('d-m-Y');
			$data['start_date'] = date('Y-m-d');
			$data['end_date'] = date('Y-m-d');
				// $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') = STR_TO_DATE('$date','%d-%m-%Y')";
			echo "<script>localStorage['filtered']='today';</script>";
		}
		elseif($filter_by =='yesterday')
		{
			$prevDate = date('d-m-Y',strtotime("-1 days"));
			$data['start_date'] = date('Y-m-d',strtotime("-1 days"));
			$data['end_date'] = date('Y-m-d',strtotime("-1 days"));
				// $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') = STR_TO_DATE('$prevDate','%d-%m-%Y')";
			echo "<script>localStorage['filtered']='yesterday';</script>";
		}
		elseif($filter_by =='past_3_days')
		{
			$past_three_days = date('d-m-Y',strtotime("-2 days"));
			$data['start_date'] = date('Y-m-d',strtotime("-2 days"));
			$data['end_date'] = date('Y-m-d');
				// $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') >= STR_TO_DATE('$past_three_days','%d-%m-%Y')";
			echo "<script>localStorage['filtered']='past_3_days';</script>";
		}
		elseif($filter_by =='week')
		{
			$past_three_days = date('d-m-Y',strtotime("-7 days"));
			$data['start_date'] = date('Y-m-d',strtotime("-7 days"));
			$data['end_date'] = date('Y-m-d');
				// $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') >= STR_TO_DATE('$past_three_days','%d-%m-%Y')";
			echo "<script>localStorage['filtered']='week';</script>";
		}
		elseif($filter_by =='month')
		{
			$month = date('m-Y');
			$start_date_temp = '01-'.$month;
			$start_date = date('d-m-Y',strtotime($start_date_temp));
			$end_date_temp = '31-'.$month;
			$end_date = date('d-m-Y',strtotime($end_date_temp));
			$data['start_date'] = date('Y-m-d',strtotime($start_date_temp));
			$data['end_date'] = date('Y-m-d',strtotime($end_date_temp));
				// $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y')";
			echo "<script>localStorage['filtered']='month';</script>";
		}
		elseif($filter_by == 'all')
		{
				// $orderTypeNotIn = config('app.order_type');
				// $orderTypeNotIn = "'".implode("','",$orderTypeNotIn)."'";

				// $orders = DB::select("SELECT * FROM del_orders WHERE lead_id IS NOT NULL AND order_approval_status !='Pending' AND status NOT IN ('Pending','Cancel') AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) order by order_id DESC");
				// $data['orders'] = json_decode(json_encode($orders),true);
				// return view('/DeliveryManagement/ModifyDeliveryView',$data);
		}
			$orders = DB::table('del_orders')
									->join('order_details','order_details.order_id','=','del_orders.order_id')
									->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
									->distinct('order_details.order_id')
									->select(
										'del_orders.*',
										'order_details.created_at as created_at'
									)
									->where('del_orders.order_approval_status','Approved')
									// ->where('del_orders.DelAssignedTo','Pending')
									->where('del_orders.deliverypickup','Delivery')
									->whereNotIn('del_orders.status',['Pending','Cancel'])
									->when($filter_by == 'today',function($query){
										$query->where('del_orders.DelDate',date('d-m-Y'));
									})
									->when($filter_by == 'yesterday',function($query){
										$query->where('del_orders.DelDate',date('d-m-Y',strtotime("-1 days")));
									})
									->when($filter_by == 'past_3_days',function($query){
										$start_date = date('d-m-Y',strtotime("-2 days"));
										$end_date = date('d-m-Y');
										$query->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$start_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$end_date','%d-%m-%Y'))")]);
									})
									->when($filter_by == 'week',function($query){
										$start_date = date('d-m-Y',strtotime("-7 days"));
										$end_date = date('d-m-Y');
										$query->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$start_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$end_date','%d-%m-%Y'))")]);
									})
									->when($filter_by == 'month',function($query){
										$month = date('m-Y');
										$start_date_temp = '01-'.$month;
										$start_date = date('d-m-Y',strtotime($start_date_temp));
										$end_date_temp = '31-'.$month;
										$end_date = date('d-m-Y',strtotime($end_date_temp));
										$query->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$start_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$end_date','%d-%m-%Y'))")]);
									})
									->when(session('city_based_access') == '1',function($query){
										$query->where('customer_details.citygroup',session('user_city'));
									})
									->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
									->get();
		// $orders = DB::select("SELECT * FROM del_orders WHERE lead_id IS NOT NULL AND order_approval_status !='Pending' AND status NOT IN ('Pending','Cancel') AND $whereClause order by order_id DESC");
		$data['orders'] = json_decode(json_encode($orders),true);
		return view('/DeliveryManagement/ModifyDeliveryView',$data);
   }

   public function modifyDeliveryFilterDWS()
   {
		$orderTypeNotIn = config('app.order_type');
		// $orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";	
		$start_date = date('d-m-Y',strtotime($_POST['start_date']));		
		$end_date = date('d-m-Y',strtotime($_POST['end_date']));
		$data['start_date'] = date('Y-m-d',strtotime($_POST['start_date']));
		$data['end_date'] = date('Y-m-d',strtotime($_POST['end_date']));
			// $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y')";
			// $orders = DB::select("SELECT * FROM del_orders WHERE lead_id IS NOT NULL AND order_approval_status !='Pending' AND status NOT IN ('Pending','Cancel') AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) AND $whereClause order by order_id DESC");
			$orders = DB::table('del_orders')
									->join('order_details','order_details.order_id','=','del_orders.order_id')
									->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
									->distinct('order_details.order_id')
									->select(
										'del_orders.*',
										'order_details.created_at as created_at'
									)
									->where('del_orders.order_approval_status','Approved')
									// ->where('del_orders.DelAssignedTo','Pending')
									->where('del_orders.deliverypickup','Delivery')
									->whereNotIn('del_orders.status',['Pending','Cancel'])

									->when($start_date,function($query)use($start_date,$end_date){
										$query->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$start_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$end_date','%d-%m-%Y'))")]);
									})
									->when(session('city_based_access') == '1',function($query){
										$query->where('customer_details.citygroup',session('user_city'));
									})
									->get();
		$data['orders'] = json_decode(json_encode($orders),true);
		return view('/DeliveryManagement/ModifyDeliveryView',$data);
   }

   public function ModifyDelivery($order_id)
   {
	  $order_details = DB::select("SELECT * FROM del_orders WHERE order_id = $order_id");
	  $data['order_details'] = json_decode(json_encode($order_details),true);
	//   $delboys = DB::select("SELECT *FROM delusers WHERE role='user'");
	  $delboys = DB::select("SELECT * FROM delusers WHERE role='user' AND status='Active'");
	  $data['delboys'] = json_decode(json_encode($delboys), true);
	  $products = DB::select("SELECT * FROM products");
	  $data['products'] = \json_decode(\json_encode($products), true);
	  //$product_details = DB::select("SELECT order_details. FROM order_details,products,vendor_details WHERE order_id = $order_id AND order_details.product_id=products.id AND order_details.vendor_id = vendor_details.id");
	  $product_details = DB::select("SELECT 
											products.product_name as product_name,
											order_details.id as order_details_id,
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
		// FROM order_details,products,vendor_details 
		// WHERE order_details.order_id = $order_id 
		// AND order_details.product_id=products.id 
		// AND order_details.vendor_id = vendor_details.id");
		if(DB::table('cr_dr_note')->where('order_id',$order_id)->exists())
		{
			foreach($product_details as $key=>$details)
			{
				$product_details[$key]->product_rent = RenewalPickupController::fetchCrDrData($details->order_details_id,'R');
				$product_details[$key]->product_deposite = RenewalPickupController::fetchCrDrData($details->order_details_id,'D');
				$product_details[$key]->transport = RenewalPickupController::fetchCrDrData($details->order_details_id,'T');
			}
		}
	  $data['product_details'] = json_decode(json_encode($product_details),true);
	  return view('/DeliveryManagement/ModifyDelivery',$data);
   }
   public function ModifyDeliveryPost(Request $request)
   {
	$isLoggedIn = $this->isLoggedIn();
	if($isLoggedIn == 'false')
	{
		$url = url('/');
		return redirect()->to($url);
	}
      //print_r($_POST);
      $order_id = $_POST['order_id'];
      $lead_id = $_POST['lead_id'];
      $del_status=$_POST['del_status'];
	  $del_date=date('d-m-Y',strtotime($_POST['del_date']));
	  //get Order Data for modify send email
	  	$oldOrderData = DB::table('del_orders')->where('order_id',$order_id)->first();
		$delDate = date('d-m-Y',strtotime($oldOrderData->DelDate));
		$delDate = date('Y-m-d',strtotime($delDate));
		if(date('Y-m-d',strtotime($delDate))!=$request->get('del_date')){
			$customer_name = $oldOrderData->shipping_first_name;
			$accountsEmail = config('app.accounts_email');
			//$accountsEmail = 'viveks@quali55care.com';
			$orderType = 'Delivery';
			$modifiedType = 'Date';
			$changedDate = ['from'=>$delDate,'to'=>$request->get('del_date')];
			$modifiedBy = session('username');
			DB::table('vendor_rented_inventory')->where('order_id',$order_id)->where('type','Delivery')->where('flag','Active')->update(['rented_date'=>$delDate,'due_date'=>date('Y-m-d',strtotime("+1 months",strtotime($delDate)))]);
			// Changed Start by Rahul on 28 July 2023
			// Mail::send('OrderModifyEmail/order-modifyEmail',compact('request','order_id','customer_name','orderType','modifiedType','modifiedBy','changedDate'), function($message) use($accountsEmail,$order_id)
			// {  
			// 	$message->to($accountsEmail, 'Accounts')->subject('Order Modified - '.$order_id);
			// 	$message->from('tempmailquali@gmail.com', 'Quali55Care');
			// });
			// Changed End by Rahul on 28 July 2023
			$changed_date = $request->get('del_date');
			$accounts_nos = config('app.accounts_staff_contacts');
			// Changed Start by Rahul on 28 July 2023
			// foreach($accounts_nos as $key=>$value)
			// {
			// 	$url = "https://lj7724mli5.execute-api.ap-south-1.amazonaws.com/prod/sendwhatsappmsg";
			// 	$curl = curl_init();
			// 	curl_setopt($curl, CURLOPT_URL, $url);
			// 	curl_setopt($curl, CURLOPT_POST, true);
			// 	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				
			// 	$headers = array(
			// 		"Accept: application/json",
			// 		"Content-Type: application/json",
			// 	);
			// 	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			// if(config('app.app_env') == 'devweb')
			// {
			// 	$value = config('app.developer_contact');
			// }
			// 	$data =[
			// 		"portno"=>"11140",
			// 		"namespace"=>"b9a23cb4_89ed_4fe2_b849_20775908ff5e",
			// 		"countrycode"=> "91",
			// 		"mobileno"=> $value,
			// 		"templatename" => "change_order_datemodified",
			// 		"templateparams" => [
			// 			["type"=> "text","text"=> $order_id],
			// 			["type"=> "text","text"=> $orderType],
			// 			["type"=> "text","text"=> $customer_name],
			// 			["type"=> "text","text"=> $delDate],
			// 			["type"=> "text","text"=> $changed_date],
			// 			["type"=> "text","text"=> $modifiedBy]
			// 		],
			// 	];
			// 	curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
				
			// 	$resp = curl_exec($curl);
			// 	curl_close($curl);
			// }
			// Changed END by Rahul on 28 July 2023
		}

	  leads_log::updateOrCreate(
		[
		   'log_order_id' => $order_id,
		   'log_lead_status' => 'Order Generated',
		   'updated_by' => session('username')
		],
		[
		   'log_order_lead_date' => date('Y-m-d',strtotime($del_date)).' '.date('H:i:s'),
		   'log_date' => date('Y-m-d'),
		   'log_time' => date('H:i:s'),
		]);
	  $pickup_date = date('Y-m-d',strtotime("+1 month",strtotime($del_date)));
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
	  $old_del_order = DB::table('del_orders')->select(
		'status',
		'helpers',
		'DelDate',
		'deliverypickup',
		'UpdatedBy',
		'DelAssignedTo',
		'ReceiptToBeCarried',
		'PaymentMode',
		'TravelMode',
		// 'created_at',
		'order_approval_status'
	  )->where('order_id',$order_id)->get();

      $update_data = [
         'status' => $del_status,
         'helpers' => $helpers,
		 'DelDate' => $del_date,
         'deliverypickup' => $delivery_type,
         'UpdatedBy' => $UpdatedBy,
         'DelAssignedTo' => $del_assigned_to,
         'ReceiptToBeCarried' => $invoice_type,
         'PaymentMode' => $payment_mode,
         'TravelMode' => $travel,
		//  'created_at'=>date('Y-m-d',strtotime($del_date)).' '.date('H:i:s'),
         'order_approval_status' => 'Approved',
      ];
      //print_r($update_data);
      $del_order = new DelOrders();
	  $order_details = new OrderDetails();
      $del_order->where('order_id', $order_id)->update($update_data);
	  $order_details_ids = DB::table('order_details')->where('order_id',$order_id)->whereNotIn('current_status',['Cancel'])->where('sale_rental','Rental')->whereNotIn('vendor_warehouse_id',[19,217])->get();
	  if($order_details_ids->count() != 0){
		$order_details_ids = $order_details_ids->pluck('id');
		foreach($order_details_ids as $key=>$id){
  
			EditOrderController::updateVendorInOutInventory($id,'in');
		}
	  }
	  foreach($update_data as $key => $value)
	  {
		  if($value != $old_del_order[0]->$key)
		  {
			$insertData = [
				'order_type'=>'DO',
				'key_id'=>$order_id,
				'operation'=>'Update Delivery.',
				'fields'=>$key,
				'old_value'=>$old_del_order[0]->$key,
				'new_value'=>$value,
				'updated_by'=>session('username')
			 ];
			 ActivityLog::insert($insertData);
		  }
	  }
		$assignArr = [
			'assign_at'=>Carbon::now()->toDateTimeString(),
			'assign_by'=>session('username'),
			];
		foreach ($assignArr as $key => $assignData) 
		{
			$assignInsert = [
				'order_type'=>'DO',
				'key_id'=>$order_id,
				'operation'=>'Order Modified',
				'fields'=>$key,
				'old_value'=>null,
				'new_value'=>$assignData,
				'updated_by'=>session('username'),
				
			];
			ActivityLog::insert($assignInsert);
		}
		$old_order_details = DB::table('order_details')->select('id','creation_date','months','pickup_date','billing_period','billing_unit')->where('order_id', $order_id)->get();

		foreach($old_order_details as $key=>$value)
		{
			$del_date=date('Y-m-d',strtotime($_POST['del_date']));
			$dt = Carbon::parse($del_date);
			if($value->billing_unit == 'Week'){
				$pickup_date = date('Y-m-d',strtotime("+$value->billing_period $value->billing_unit",strtotime($del_date)));
			}else if($value->billing_unit == 'Half Month'){
				$value->billing_period = $value->billing_period * 2;
				$pickup_date = date('Y-m-d',strtotime("+$value->billing_period Week",strtotime($del_date)));
			}else if($value->billing_unit == 'Days'){
				$pickup_date = date('Y-m-d',strtotime("+$value->billing_period Days",strtotime($del_date)));
			}else{
				$pickup_date = date('Y-m-d',strtotime("+$value->billing_period months",strtotime($del_date)));
			}
        	// $pickup_date = $dt->addMonths($value->months);
			$update_order_details = [
				'id'=>$value->id,
				'months'=>$value->months,
				'creation_date'=>$del_date,
				'pickup_date'=>$pickup_date,
				'billing_period'=>$value->billing_period,
				'billing_unit'=>$value->billing_unit,
			];
			$order_details->where('id', $value->id)->update($update_order_details);
			foreach($update_order_details as $key => $value)
			{
				if($value != $old_order_details[0]->$key)
				{
					$insertData = [
						'order_type'=>'OD',
						'key_id'=>$order_id,
						'operation'=>'Update Order Details',
						'fields'=>$key,
						'old_value'=>$old_order_details[0]->$key,
						'new_value'=>$value,
						'updated_by'=>session('username')
					];
					ActivityLog::insert($insertData);
				}
			}
	  	}
	//   $old_order_details = DB::table('order_details')->select('creation_date','pickup_date')->where('order_id', $order_id)->get();

	//   $del_date=date('Y-m-d',strtotime($_POST['del_date']));
    //   $update_order_details = [
	// 	  'creation_date'=>$del_date,
	// 	  'pickup_date'=>$pickup_date
	//   ];
	//   $order_details->where('order_id', $order_id)->update($update_order_details);

	// 	foreach($update_order_details as $key => $value)
	// 	{
	// 		if($value != $old_order_details[0]->$key)
	// 		{
	// 			$insertData = [
	// 				'order_type'=>'OD',
	// 				'key_id'=>$order_id,
	// 				'operation'=>'Update Order Details',
	// 				'fields'=>$key,
	// 				'old_value'=>$old_order_details[0]->$key,
	// 				'new_value'=>$value,
	// 				'updated_by'=>session('username')
	// 			];
	// 			ActivityLog::insert($insertData);
	// 		}
	// 	}

    //   $leads_log = new leads_log();
    //   $leads_log_data = [
    //      'log_lead_id' => $lead_id,
    //      'log_lead_status' => 'Order Generated',
    //      'log_date' => date('Y-m-d'),
    //      'log_time' => date('H:i:s'),
    //      'updated_by' => session('username')
    //  ];
    //  $leads_log->insert($leads_log_data);
      return redirect('/confirmed_delivery')->with('Message','Order Modified Successfully');
   }
   public function AllDeliveries() 
   {
		$orderTypeNotIn = config('app.order_type');        
		$orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";
		$start_date = '01-04-2022';

      $order_details = DB::select("SELECT * FROM del_orders Where status NOT IN ('Closed')  AND STR_TO_DATE(DelDate,'%d-%m-%Y') >= STR_TO_DATE('$start_date','%d-%m-%Y') AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) ORDER BY order_id DESC");
      $data['order_details'] = json_decode(json_encode($order_details),true);
	  $i = 0;
      foreach($data['order_details'] as $order_details1)
      {
		if($order_details1['lead_id']!="")
		{
            $order_id = $order_details1['order_id'];
			$patient_name = DB::table('leads')->select('patient_name')->where('id',$order_details1['lead_id'])->first();
			$data['order_details'][$i]['patient_name'] = $patient_name->patient_name;
            $equipment_details = DB::select("SELECT * FROM products,order_details WHERE order_details.order_id = $order_id AND order_details.product_id = products.id");
            $data['equipment_details'] = json_decode(json_encode($equipment_details),true);
            $equipment_name = array();
            $fulldetails = $order_details1['fulldetails'];
            foreach ($data['equipment_details'] as $equipment_details)
            {
               $equip_name = $equipment_details['product_name'];
               array_push($equipment_name,$equipment_details['product_name']);
               if($equipment_details['sale_rental'] == 'Rental')
               {
                  $fulldetails .= "\n\n".$equipment_details['product_name'].
                              "\nDeposite : ".$equipment_details['product_deposite'].
                              "\nRent : ".$equipment_details['product_rent'].
                              "\nTransport : ".$equipment_details['transport'];
               }
               elseif($equipment_details['sale_rental'] == 'Sale')
               {
                  $fulldetails .= "\n\n".$equipment_details['product_name'].
                              "\nSale : ".$equipment_details['product_rent'].
                              "\nTransport : ".$equipment_details['transport'];
               }
            }
            $data['order_details'][$i]['fulldetails'] = $fulldetails;
            $equipment_name = implode(',',$equipment_name);
            $data['order_details'][$i]['line_item_1'] = $equipment_name;
		}
		else{
			$data['order_details'][$i]['patient_name'] = null;
		}
		$i++;
      }
      return view('/DeliveryManagement/AllDeliveries',$data);
   }
   public function CompletedDeliveries() 
   {
      $order_details = DB::select("SELECT * FROM del_orders Where status IN ('Delivered','Picked Up', 'Collected') ORDER BY order_id DESC");
      $data['order_details'] = json_decode(json_encode($order_details),true);
      return view('/DeliveryManagement/CompletedDeliveries',$data);
   }
   public function deliveryReport() 
   {
	return redirect('deliveryReportFilter/today/All');
   }
//    public function deliveryReportFilter($filter_by,$deliverypickup)
//    {	   
// 		if($deliverypickup == 'Delivery')
// 		{
// 			$data['deliverypickup'] = 'Delivery';
// 			$deliverypickup = "('Delivery')";
// 		}
// 		elseif($deliverypickup == 'Pick Up')
// 		{
// 			$data['deliverypickup'] = 'Pick Up';
// 			$deliverypickup = "('Pick Up')";
// 		}
// 		elseif($deliverypickup == 'Collection')
// 		{
// 			$data['deliverypickup'] = 'Collection';
// 			$deliverypickup = "('Collection')";
// 		}
// 		else
// 		{
// 			$data['deliverypickup'] = 'All';
// 			$deliverypickup = "('Delivery','Pick Up', 'Collection')";
// 		}
// 		if($filter_by =='today')
// 		{
// 			$date = date('d-m-Y');
// 			$whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') = STR_TO_DATE('$date','%d-%m-%Y')";
// 			echo "<script>localStorage['filtered']='today';</script>";
// 		}
// 		elseif($filter_by =='yesterday')
// 		{
// 			$prevDate = date('d-m-Y',strtotime("-1 days"));
// 			$whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') = STR_TO_DATE('$prevDate','%d-%m-%Y')";
// 			echo "<script>localStorage['filtered']='yesterday';</script>";
// 		}
// 		elseif($filter_by =='past_3_days')
// 		{
// 			$past_three_days = date('d-m-Y',strtotime("-2 days"));
// 			$whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') >= STR_TO_DATE('$past_three_days','%d-%m-%Y')";
// 			echo "<script>localStorage['filtered']='past_3_days';</script>";
// 		}
// 		elseif($filter_by =='week')
// 		{
// 			$past_three_days = date('d-m-Y',strtotime("-7 days"));
// 			$whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') >= STR_TO_DATE('$past_three_days','%d-%m-%Y')";
// 			echo "<script>localStorage['filtered']='week';</script>";
// 		}
// 		elseif($filter_by =='month')
// 		{
// 			$month = date('m-Y');
// 			$start_date_temp = '01-'.$month;
// 			$start_date = date('d-m-Y',strtotime($start_date_temp));
// 			$end_date_temp = '31-'.$month;
// 			$end_date = date('d-m-Y',strtotime($end_date_temp));
// 			$whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y')";
// 			echo "<script>localStorage['filtered']='month';</script>";
// 		}
// 		elseif($filter_by == 'all')
// 		{
// 			$order_details = DB::select("SELECT * FROM del_orders Where del_orders.deliverypickup IN $deliverypickup ORDER BY order_id DESC");
//       		$data['order_details'] = json_decode(json_encode($order_details),true);
//       		return view('/DeliveryManagement/DeliveryReport',$data);
// 		}
// 		$order_details = DB::select("SELECT * FROM del_orders Where del_orders.deliverypickup IN $deliverypickup AND $whereClause ORDER BY order_id DESC");
// 		$data['order_details'] = json_decode(json_encode($order_details),true);
// 		return view('/DeliveryManagement/DeliveryReport',$data);
//    }
// public function searchCustomerDelReport()
//    {
// 	   if($_SERVER['REQUEST_METHOD']=='POST')
// 	   {
// 		   if($_POST['submit']=="Search")
// 		   {
// 				$data['deliverypickup'] = 'All';
// 				$input_text = $_POST['input_text'];
// 				$whereClause = "del_orders.shipping_first_name LIKE '%$input_text%' OR del_orders.mobileno LIKE '%$input_text%'";
// 				$order_details = DB::select("SELECT * FROM del_orders Where $whereClause ORDER BY order_id DESC");
// 				$data['order_details'] = json_decode(json_encode($order_details),true);
// 				return view('/DeliveryManagement/DeliveryReport',$data);
// 		   }
// 		   elseif($_POST['submit']=="Datewise")
// 		   {
// 				$data['deliverypickup'] = 'All';
// 				$start_date = $_POST['start_date'];
// 				$start_date = date('d-m-Y',strtotime($start_date));
// 				$end_date = $_POST['end_date'];
// 				$end_date = date('d-m-Y',strtotime($end_date));
// 				$whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y')";
// 				$order_details = DB::select("SELECT * FROM del_orders Where $whereClause ORDER BY order_id DESC");
// 				$data['order_details'] = json_decode(json_encode($order_details),true);
// 				$data['start_date'] = date("Y-m-d",strtotime($start_date));
// 				$data['end_date'] = date("Y-m-d",strtotime($end_date));
// 				// print_r($data['order_details']);
// 				return view('/DeliveryManagement/DeliveryReport',$data);
// 		   }
// 	   }
//    }
public function deliveryReportFilter($filter_by,$deliverypickup)
{	   
 if($deliverypickup == 'Delivery')
 {
	 $data['deliverypickup'] = 'Delivery';
	 $deliverypickup = ['Delivery'];
 }
 elseif($deliverypickup == 'Pick Up')
 {
	 $data['deliverypickup'] = 'Pick Up';
	 $deliverypickup = ['Pick Up'];
 }
 elseif($deliverypickup == 'Collection')
 {
	 $data['deliverypickup'] = 'Collection';
	 $deliverypickup = ['Collection'];
 }
 else
 {
	 $data['deliverypickup'] = 'All';
	 $deliverypickup = ['Delivery','Pick Up', 'Collection'];
 }
 if($filter_by =='today')
 {
	 $data['start_date'] = date('Y-m-d');
	 $data['end_date'] = date('Y-m-d');
	 $date = date('d-m-Y');
	 $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') = STR_TO_DATE('$date','%d-%m-%Y')";
	 echo "<script>localStorage['filtered']='today';</script>";
 }
 elseif($filter_by =='yesterday')
 {
	 $prevDate = date('d-m-Y',strtotime("-1 days"));
	 $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') = STR_TO_DATE('$prevDate','%d-%m-%Y')";
	 echo "<script>localStorage['filtered']='yesterday';</script>";
 }
 elseif($filter_by =='past_3_days')
 {
	 $past_three_days = date('d-m-Y',strtotime("-2 days"));
	 $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') >= STR_TO_DATE('$past_three_days','%d-%m-%Y')";
	 echo "<script>localStorage['filtered']='past_3_days';</script>";
 }
 elseif($filter_by =='week')
 {
	 $past_three_days = date('d-m-Y',strtotime("-7 days"));
	 $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') >= STR_TO_DATE('$past_three_days','%d-%m-%Y')";
	 echo "<script>localStorage['filtered']='week';</script>";
 }
 elseif($filter_by =='month')
 {
	 $month = date('m-Y');
	 $start_date_temp = '01-'.$month;
	 $start_date = date('d-m-Y',strtotime($start_date_temp));
	 $end_date_temp = '31-'.$month;
	 $end_date = date('d-m-Y',strtotime($end_date_temp));
	 $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y')";
	 echo "<script>localStorage['filtered']='month';</script>";
 }
 // elseif($filter_by == 'all')
 // {
 // 	$orderTypeNotIn = config('app.order_type');
 // 	$orderTypeNotIn = "'".implode("','",$orderTypeNotIn)."'";

 // 	$order_details = DB::select("SELECT * FROM del_orders Where del_orders.deliverypickup IN $deliverypickup  AND status NOT IN ('Cancel') AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) ORDER BY order_id DESC");
 // 	$data['order_details'] = json_decode(json_encode($order_details),true);
 // 	$i=0;
 // 	foreach($data['order_details'] as $order_details)
 // 	{
 // 		$order_id = $order_details['order_id'];
 // 		if($order_details['lead_id']!="")
 // 		{	
 // 			$equipment_details = DB::select("SELECT * FROM products,order_details WHERE order_details.order_id = $order_id AND order_details.product_id = products.id");
 // 			$data['equipment_details'] = json_decode(json_encode($equipment_details),true);
 // 			$equipment_name = array();
 // 			$fulldetails = $order_details['fulldetails'];
 // 			foreach ($data['equipment_details'] as $equipment_details)
 // 			{
 // 			$equip_name = $equipment_details['product_name'];
 // 			array_push($equipment_name,$equipment_details['product_name']);
 // 			if($equipment_details['sale_rental'] == 'Rental')
 // 			{
 // 				$fulldetails .= "\n\n".$equipment_details['product_name'].
 // 							"\nDeposite : ".$equipment_details['product_deposite'].
 // 							"\nRent : ".$equipment_details['product_rent'].
 // 							"\nTransport : ".$equipment_details['transport'];
 // 			}
 // 			elseif($equipment_details['sale_rental'] == 'Sale')
 // 			{
 // 				$fulldetails .= "\n\n".$equipment_details['product_name'].
 // 							"\nSale : ".$equipment_details['product_rent'].
 // 							"\nTransport : ".$equipment_details['transport'];
 // 			}
 // 			}
 // 			$data['order_details'][$i]['fulldetails'] = $fulldetails;
 // 			$equipment_name = implode(',',$equipment_name);
 // 			$data['order_details'][$i]['line_item_1'] = $equipment_name;
 // 		}
 // 		$data['order_details'][$i]['online_method'] = "-";
 // 		$data['order_details'][$i]['payment_status'] = "-";
 // 		$data['order_details'][$i]['reference_id'] = "-";
 // 		$data['order_details'][$i]['comment'] = "-";
 // 		$renewal_data = DB::select("SELECT * FROM renewals WHERE collection_order_id = $order_id");
 // 		$renewal_data = json_decode(json_encode($renewal_data),true);
 // 		if(isset($renewal_data[0]))
 // 		{
 // 			$data['order_details'][$i]['online_method'] = $renewal_data[0]['online_method'];
 // 			$data['order_details'][$i]['payment_status'] = $renewal_data[0]['payment_status'];
 // 			$data['order_details'][$i]['reference_id'] = $renewal_data[0]['reference_id'];
 // 			$data['order_details'][$i]['comment'] = $renewal_data[0]['comment'];
 // 		}
 // 		$i++;
 // 	}
   // 	return view('/DeliveryManagement/DeliveryReport',$data);
 // }
 $orderTypeNotIn = config('app.order_type');
 // $orderTypeNotIn = "'".implode("','",$orderTypeNotIn)."'";

 // $order_details = DB::select("SELECT * FROM del_orders Where del_orders.deliverypickup IN $deliverypickup AND status NOT IN ('Cancel') AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) AND $whereClause ORDER BY order_id DESC");
 $order_details = DB::table('del_orders')
						 ->join('leads','del_orders.lead_id','=','leads.id')
						 ->join('customer_details','customer_details.cust_id','=','leads.customer_id')
						 ->select('del_orders.*')
						 ->whereIn('del_orders.deliverypickup',$deliverypickup)
						 ->whereNotIn('del_orders.status',['Cancel'])
						 ->when($filter_by == 'today',function($query){
							 $query->where('del_orders.DelDate',date('d-m-Y'));
						 })
						 ->when(session('city_based_access') == '1',function($query){
							 $query->where('customer_details.citygroup',session('user_city'));
						 })
						 ->get();
 $data['order_details'] = json_decode(json_encode($order_details),true);
 $i=0;
 foreach($data['order_details'] as $order_details)
 {
	 $order_id = $order_details['order_id'];
	 if($order_details['lead_id']!="" && $order_details['deliverypickup'] == 'Delivery')
	 {
		 $equipment_details = DB::select("SELECT * FROM products,order_details WHERE order_details.order_id = $order_id AND order_details.product_id = products.id");
		 $data['equipment_details'] = json_decode(json_encode($equipment_details),true);
		 $equipment_name = array();
		 $fulldetails = $order_details['fulldetails'];
		 foreach ($data['equipment_details'] as $equipment_details)
		 {
			 $equip_name = $equipment_details['product_name'];
			 array_push($equipment_name,$equipment_details['product_name']);
			 if($equipment_details['sale_rental'] == 'Rental')
			 {
				 $fulldetails .= "\n\n".$equipment_details['product_name'].
							 "\nDeposite : ".$equipment_details['product_deposite'].
							 "\nRent : ".$equipment_details['product_rent'].
							 "\nTransport : ".$equipment_details['transport'];
			 }
			 elseif($equipment_details['sale_rental'] == 'Sale')
			 {
				 $fulldetails .= "\n\n".$equipment_details['product_name'].
							 "\nSale : ".$equipment_details['product_rent'].
							 "\nTransport : ".$equipment_details['transport'];
			 }
		 }
		 $data['order_details'][$i]['fulldetails'] = $fulldetails;
		 $equipment_name = implode(',',$equipment_name);
		 $data['order_details'][$i]['line_item_1'] = $equipment_name;
	 }
	 $data['order_details'][$i]['online_method'] = "-";
	 $data['order_details'][$i]['payment_status'] = "-";
	 $data['order_details'][$i]['reference_id'] = "-";
	 $data['order_details'][$i]['comment'] = "-";
	 $data['order_details'][$i]['reference_image'] = "-";
	 $data['order_details'][$i]['period'] = "-";
	 // $renewal_data = Renewal::where('collection_order_id',$order_id)->get()->toArray();
	 $renewal_data = DB::select("SELECT * FROM renewals WHERE collection_order_id = $order_id");
	 $renewal_data = json_decode(json_encode($renewal_data),true);
	 // dd($renewal_data);
	 if(isset($renewal_data[0]))
	 {
		 $data['order_details'][$i]['online_method'] = $renewal_data[0]['online_method'];
		 $data['order_details'][$i]['payment_status'] = $renewal_data[0]['payment_status'];
		 $data['order_details'][$i]['reference_id'] = $renewal_data[0]['reference_id'];
		 $data['order_details'][$i]['comment'] = $renewal_data[0]['comment'];
		 $data['order_details'][$i]['reference_image'] = $renewal_data[0]['image_path'];
		 $data['order_details'][$i]['period'] = date('d-M-Y',strtotime($renewal_data[0]['start_date']))." - ".date('d-M-Y',strtotime($renewal_data[0]['end_date']));
	 }
	 $i++;
 }
 return view('/DeliveryManagement/DeliveryReport',$data);
}
public function searchCustomerDelReport()
{
	$orderTypeNotIn = config('app.order_type');        
	// $orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";
	if($_SERVER['REQUEST_METHOD']=='POST')
	{
		if($_POST['submit']=="Search")
		{
			$whereClause = "order_id IS NOT NULL ";
				$start_date = null;
			if(!empty($_POST['start_date']) && !empty($_POST['end_date']))
			{
				$data['start_date'] = $_POST['start_date'];
				$data['end_date'] = $_POST['end_date'];

				$start_date = date('d-m-Y',strtotime($_POST['start_date']));
				$end_date = date('d-m-Y',strtotime($_POST['end_date']));

					// $whereClause .="AND STR_TO_DATE(DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') ";
				}
				$input_text = null;
			if(!empty($_POST['input_text']))
			{
				$input_text = $_POST['input_text'];
				$data['text_customer'] = $input_text;
					// $whereClause .="AND del_orders.shipping_first_name LIKE '%$input_text%' OR del_orders.mobileno LIKE '%$input_text%' ";
			}
			if(!empty($_POST['deliverypickup']))
			{
				if($_POST['deliverypickup'] == "All")
				{
					$data['deliverypickup'] = "All";
						$deliverypickup = ['Delivery','Pick Up', 'Collection'];
				}
				else
				{
					$deliverypickup = $_POST['deliverypickup'];
					$data['deliverypickup'] = $deliverypickup;
						$deliverypickup = [$deliverypickup];
				}
					// $whereClause .= "AND del_orders.deliverypickup IN $deliverypickup";
			}
			// $data['deliverypickup'] = 'All';
			// $input_text = $_POST['input_text'];
			// $whereClause = "del_orders.shipping_first_name LIKE '%$input_text%' OR del_orders.mobileno LIKE '%$input_text%'";
			// DB::enableQueryLog();
				// $order_details = DB::select("SELECT * FROM del_orders Where $whereClause AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) ORDER BY order_id DESC");
				$order_details = DB::table('del_orders')
							->join('leads','del_orders.lead_id','=','leads.id')
							->join('customer_details','customer_details.cust_id','=','leads.customer_id')
							->select('del_orders.*')
							->whereIn('del_orders.deliverypickup',$deliverypickup)
							->whereNotIn('del_orders.status',['Cancel'])

							->when($start_date != null,function($query)use($start_date,$end_date){
								$query->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$start_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$end_date','%d-%m-%Y'))")]);
							})
							->when($input_text != null,function($query)use($input_text){
								$query->where([['del_orders.shipping_first_name','LIKE','%'.$input_text.'%']]);
							})
							->when(session('city_based_access') == '1',function($query){
								$query->where('customer_details.citygroup',session('user_city'));
							})
							->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
							->get();
			// dd(DB::getQueryLog());

			$data['order_details'] = json_decode(json_encode($order_details),true);
			$i=0;
			$data['total_collection'] = 0;
			$data['cash_collection'] = 0;
			$data['online_collection'] = 0;
			foreach($data['order_details'] as $order_details)
			{
				$order_id = $order_details['order_id'];
				if($order_details['lead_id']!="")
				{
					
					$equipment_details = DB::select("SELECT * FROM products,order_details WHERE order_details.order_id = $order_id AND order_details.product_id = products.id");
					$data['equipment_details'] = json_decode(json_encode($equipment_details),true);
					$equipment_name = array();
					$fulldetails = $order_details['fulldetails'];
					foreach ($data['equipment_details'] as $equipment_details)
					{
						$equip_name = $equipment_details['product_name'];
						array_push($equipment_name,$equipment_details['product_name']);
						if($equipment_details['sale_rental'] == 'Rental')
						{
							$fulldetails .= "\n\n".$equipment_details['product_name'].
										"\nDeposite : ".$equipment_details['product_deposite'].
										"\nRent : ".$equipment_details['product_rent'].
										"\nTransport : ".$equipment_details['transport'];
						}
						elseif($equipment_details['sale_rental'] == 'Sale')
						{
							$fulldetails .= "\n\n".$equipment_details['product_name'].
										"\nSale : ".$equipment_details['product_rent'].
										"\nTransport : ".$equipment_details['transport'];
						}
					}
					$data['order_details'][$i]['fulldetails'] = $fulldetails;
					$equipment_name = implode(',',$equipment_name);
					$data['order_details'][$i]['line_item_1'] = $equipment_name;
				}
				$data['order_details'][$i]['online_method'] = "-";
				$data['order_details'][$i]['payment_status'] = "-";
				$data['order_details'][$i]['reference_id'] = "-";
				$data['order_details'][$i]['comment'] = "-";
				$data['order_details'][$i]['reference_image'] = "-";
				$data['order_details'][$i]['period'] = "-";
				$renewal_data = DB::select("SELECT * FROM renewals WHERE collection_order_id = $order_id");
				$renewal_data = json_decode(json_encode($renewal_data),true);
				if(isset($renewal_data[0]))
				{
					$data['order_details'][$i]['online_method'] = $renewal_data[0]['online_method'];
					$data['order_details'][$i]['payment_status'] = $renewal_data[0]['payment_status'];
					$data['order_details'][$i]['reference_id'] = $renewal_data[0]['reference_id'];
					$data['order_details'][$i]['comment'] = $renewal_data[0]['comment'];
					$data['order_details'][$i]['reference_image'] = $renewal_data[0]['image_path'];
					$data['order_details'][$i]['period'] = date('d-M-Y',strtotime($renewal_data[0]['start_date']))." - ".date('d-M-Y',strtotime($renewal_data[0]['end_date']));
				}
				if($data['order_details'][$i]['deliverypickup'] == 'Collection')
				{
					if($data['order_details'][$i]['PaymentMode'] == 'Online')
					{
						$data['online_collection'] = $data['online_collection']+$data['order_details'][$i]['TotalAmt'];
					}
					elseif($data['order_details'][$i]['PaymentMode'] == 'Cash')
					{
						$data['cash_collection'] = $data['cash_collection']+$data['order_details'][$i]['TotalAmt'];
					}
					$data['total_collection'] = $data['total_collection']+$data['order_details'][$i]['TotalAmt'];
				}
				$i++;
			}
			// dd($data);
			return view('/DeliveryManagement/DeliveryReport',$data);
		}
		elseif($_POST['submit']=="Datewise")
		{
			$orderTypeNotIn = config('app.order_type');        
			$orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";
			$data['deliverypickup'] = 'All';
			$start_date = $_POST['start_date'];
			$start_date = date('d-m-Y',strtotime($start_date));
			$end_date = $_POST['end_date'];
			$end_date = date('d-m-Y',strtotime($end_date));
			$whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y')";
			$order_details = DB::select("SELECT * FROM del_orders Where $whereClause AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) ORDER BY order_id DESC");
			$data['order_details'] = json_decode(json_encode($order_details),true);
			$data['start_date'] = date("Y-m-d",strtotime($start_date));
			$data['end_date'] = date("Y-m-d",strtotime($end_date));
			$i=0;
			foreach($data['order_details'] as $order_details)
			{
				$order_id = $order_details['order_id'];
				if($order_details['lead_id']!="")
				{
					
					$equipment_details = DB::select("SELECT * FROM products,order_details WHERE order_details.order_id = $order_id AND order_details.product_id = products.id");
					$data['equipment_details'] = json_decode(json_encode($equipment_details),true);
					$equipment_name = array();
					$fulldetails = $order_details['fulldetails'];
					foreach ($data['equipment_details'] as $equipment_details)
					{
					$equip_name = $equipment_details['product_name'];
					array_push($equipment_name,$equipment_details['product_name']);
					if($equipment_details['sale_rental'] == 'Rental')
					{
						$fulldetails .= "\n\n".$equipment_details['product_name'].
									"\nDeposite : ".$equipment_details['product_deposite'].
									"\nRent : ".$equipment_details['product_rent'].
									"\nTransport : ".$equipment_details['transport'];
					}
					elseif($equipment_details['sale_rental'] == 'Sale')
					{
						$fulldetails .= "\n\n".$equipment_details['product_name'].
									"\nSale : ".$equipment_details['product_rent'].
									"\nTransport : ".$equipment_details['transport'];
					}
					}
					$data['order_details'][$i]['fulldetails'] = $fulldetails;
					$equipment_name = implode(',',$equipment_name);
					$data['order_details'][$i]['line_item_1'] = $equipment_name;
				}
				$data['order_details'][$i]['online_method'] = "-";
				$data['order_details'][$i]['payment_status'] = "-";
				$data['order_details'][$i]['reference_id'] = "-";
				$data['order_details'][$i]['comment'] = "-";
				$data['order_details'][$i]['reference_image'] = "-";
				$data['order_details'][$i]['period'] = "-";
				$renewal_data = DB::select("SELECT * FROM renewals WHERE collection_order_id = $order_id");
				$renewal_data = json_decode(json_encode($renewal_data),true);
				if(isset($renewal_data[0]))
				{
					$data['order_details'][$i]['online_method'] = $renewal_data[0]['online_method'];
					$data['order_details'][$i]['payment_status'] = $renewal_data[0]['payment_status'];
					$data['order_details'][$i]['reference_id'] = $renewal_data[0]['reference_id'];
					$data['order_details'][$i]['comment'] = $renewal_data[0]['comment'];
					$data['order_details'][$i]['reference_image'] = $renewal_data[0]['image_path'];
					$data['order_details'][$i]['period'] = date('d-M-Y',strtotime($renewal_data[0]['start_date']))." - ".date('d-M-Y',strtotime($renewal_data[0]['end_date']));
				}
				$i++;
			}
			// print_r($data['order_details']);
			return view('/DeliveryManagement/DeliveryReport',$data);
		}
	}
}
   
//    public function searchCustomerDelReport()
//    {
// 	   if($_SERVER['REQUEST_METHOD']=='POST')
// 	   {
// 		   if($_POST['submit']=="Search")
// 		   {
// 				$data['deliverypickup'] = 'All';
// 				$input_text = $_POST['input_text'];
// 				$whereClause = "del_orders.shipping_first_name LIKE '%$input_text%' OR del_orders.mobileno LIKE '%$input_text%'";
// 				$order_details = DB::select("SELECT * FROM del_orders Where $whereClause ORDER BY order_id DESC");
// 				$data['order_details'] = json_decode(json_encode($order_details),true);
// 				$i=0;
// 				foreach($data['order_details'] as $order_details)
// 				{
// 					if($order_details['lead_id']!="")
// 					{
// 						$order_id = $order_details['order_id'];
// 						$equipment_details = DB::select("SELECT * FROM products,order_details WHERE order_details.order_id = $order_id AND order_details.product_id = products.id");
// 						$data['equipment_details'] = json_decode(json_encode($equipment_details),true);
// 						$equipment_name = array();
// 						$fulldetails = $order_details['fulldetails'];
// 						foreach ($data['equipment_details'] as $equipment_details)
// 						{
// 						$equip_name = $equipment_details['product_name'];
// 						array_push($equipment_name,$equipment_details['product_name']);
// 						if($equipment_details['sale_rental'] == 'Rental')
// 						{
// 							$fulldetails .= "\n\n".$equipment_details['product_name'].
// 										"\nDeposite : ".$equipment_details['product_deposite'].
// 										"\nRent : ".$equipment_details['product_rent'].
// 										"\nTransport : ".$equipment_details['transport'];
// 						}
// 						elseif($equipment_details['sale_rental'] == 'Sale')
// 						{
// 							$fulldetails .= "\n\n".$equipment_details['product_name'].
// 										"\nSale : ".$equipment_details['product_rent'].
// 										"\nTransport : ".$equipment_details['transport'];
// 						}
// 						}
// 						$data['order_details'][$i]['fulldetails'] = $fulldetails;
// 						$equipment_name = implode(',',$equipment_name);
// 						$data['order_details'][$i]['line_item_1'] = $equipment_name;
// 					}
// 					$i++;
// 				}
// 				return view('/DeliveryManagement/DeliveryReport',$data);
// 		   }
// 		   elseif($_POST['submit']=="Datewise")
// 		   {
// 				$data['deliverypickup'] = 'All';
// 				$start_date = $_POST['start_date'];
// 				$start_date = date('d-m-Y',strtotime($start_date));
// 				$end_date = $_POST['end_date'];
// 				$end_date = date('d-m-Y',strtotime($end_date));
// 				$whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y')";
// 				$order_details = DB::select("SELECT * FROM del_orders Where $whereClause ORDER BY order_id DESC");
// 				$data['order_details'] = json_decode(json_encode($order_details),true);
// 				$data['start_date'] = date("Y-m-d",strtotime($start_date));
// 				$data['end_date'] = date("Y-m-d",strtotime($end_date));
// 				$i=0;
// 				foreach($data['order_details'] as $order_details)
// 				{
// 					if($order_details['lead_id']!="")
// 					{
// 						$order_id = $order_details['order_id'];
// 						$equipment_details = DB::select("SELECT * FROM products,order_details WHERE order_details.order_id = $order_id AND order_details.product_id = products.id");
// 						$data['equipment_details'] = json_decode(json_encode($equipment_details),true);
// 						$equipment_name = array();
// 						$fulldetails = $order_details['fulldetails'];
// 						foreach ($data['equipment_details'] as $equipment_details)
// 						{
// 						$equip_name = $equipment_details['product_name'];
// 						array_push($equipment_name,$equipment_details['product_name']);
// 						if($equipment_details['sale_rental'] == 'Rental')
// 						{
// 							$fulldetails .= "\n\n".$equipment_details['product_name'].
// 										"\nDeposite : ".$equipment_details['product_deposite'].
// 										"\nRent : ".$equipment_details['product_rent'].
// 										"\nTransport : ".$equipment_details['transport'];
// 						}
// 						elseif($equipment_details['sale_rental'] == 'Sale')
// 						{
// 							$fulldetails .= "\n\n".$equipment_details['product_name'].
// 										"\nSale : ".$equipment_details['product_rent'].
// 										"\nTransport : ".$equipment_details['transport'];
// 						}
// 						}
// 						$data['order_details'][$i]['fulldetails'] = $fulldetails;
// 						$equipment_name = implode(',',$equipment_name);
// 						$data['order_details'][$i]['line_item_1'] = $equipment_name;
// 					}
// 					$i++;
// 				}
// 				// print_r($data['order_details']);
// 				return view('/DeliveryManagement/DeliveryReport',$data);
// 		   }
// 	   }
//    }
   
   public function ArchivedDeliveries() 
   {
		$orderTypeNotIn = config('app.order_type');        
		$orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";
		$order_details = DB::select("SELECT * FROM del_orders Where status = 'Closed' AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) ORDER BY order_id DESC");
		$data['order_details'] = json_decode(json_encode($order_details),true);
		return view('/DeliveryManagement/ArchivedDeliveries',$data);
   }

   public function MonthlyDeliveryReport()
   {
	$isLoggedIn = $this->isLoggedIn();
	if($isLoggedIn == 'false')
	{
		$url = url('/');
		return redirect()->to($url);
	}
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
			$orderTypeNotIn = config('app.order_type');        
			$orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";
		 $order_details = DB::select("SELECT * FROM del_orders WHERE STR_TO_DATE(DelDate,'%d-%m-%Y') = STR_TO_DATE('$start_date','%d-%m-%Y') AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)");         
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
	$isLoggedIn = $this->isLoggedIn();
	if($isLoggedIn == 'false')
	{
		$url = url('/');
		return redirect()->to($url);
	}
      //$order_details = DB::select("SELECT del_orders.order_id as order_id, del_orders.lead_id as lead_id, del_orders.DelDate as DelDate, del_orders.shipping_first_name as shipping_first_name, del_orders.mobileno as mobileno, order_details.status as status FROM del_orders,order_details Where del_orders.order_id=order_details.order_id AND del_orders.order_approval_status='Approved'");
      $date = date('d-m-Y');
	  $data['start_date'] = date('Y-m-d');
	  $data['end_date'] = date('Y-m-d');
		// $order_details = DB::select("SELECT 
		// 									DISTINCT('order_details.order_id'),del_orders.*,order_details.created_at as created_at
		// 								FROM 
		// 								del_orders,order_details 
		// 								WHERE 
		// 									del_orders.order_id = order_details.order_id
		// 									AND del_orders.order_approval_status = 'Approved' 
		// 									AND del_orders.DelAssignedTo ='Pending' 
		// 									AND del_orders.deliverypickup = 'Delivery' 
		// 									AND del_orders.status NOT IN ('Cancel') 
		// 									AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)
		// 									AND del_orders.DelDate='$date'");
		$order_details = DB::table('del_orders')
									->join('order_details','order_details.order_id','=','del_orders.order_id')
									->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
									->distinct('order_details.order_id')
									->select(
										'del_orders.*',
										'order_details.created_at as created_at'
									)
									->where('del_orders.order_approval_status','Approved')
									->where('del_orders.DelAssignedTo','Pending')
									->where('del_orders.deliverypickup','Delivery')
									->whereNotIn('del_orders.status',['Cancel'])

									->where('del_orders.DelDate',$date)
									->when(session('city_based_access') == '1',function($query){
										$query->where('customer_details.citygroup',session('user_city'));
									})
									->get();
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
	$isLoggedIn = $this->isLoggedIn();
	if($isLoggedIn == 'false')
	{
		$url = url('/');
		return redirect()->to($url);
	}
		$order_details = DB::select("SELECT * FROM del_orders WHERE order_id = $order_id");
		$data['order_details'] = json_decode(json_encode($order_details),true);
		//print_r($data['order_details']);
		$delboys = DB::select("SELECT * FROM delusers WHERE role='user' AND status='Active'");
		$data['delboys'] = json_decode(json_encode($delboys), true);
		$products = DB::select("SELECT * FROM products");
		$data['products'] = \json_decode(\json_encode($products), true);
		//$product_details = DB::select("SELECT order_details. FROM order_details,products,vendor_details WHERE order_id = $order_id AND order_details.product_id=products.id AND order_details.vendor_id = vendor_details.id");
		$product_details = DB::select("SELECT 
											products.product_name as product_name,
											order_details.id as order_details_id,
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
		if(DB::table('cr_dr_note')->where('order_id',$order_id)->exists())
		{
			foreach($product_details as $key=>$value)
			{
				$product_details[$key]->product_rent = RenewalPickupController::fetchCrDrData($value->order_details_id,'R');
				$product_details[$key]->product_deposite = RenewalPickupController::fetchCrDrData($value->order_details_id,'D');
				$product_details[$key]->transport = RenewalPickupController::fetchCrDrData($value->order_details_id,'T');
			}
		}
		$data['product_details'] = json_decode(json_encode($product_details),true);
		$lead_id = $data['order_details'][0]['lead_id'];
		$data['self_pickup'] = DB::table('leads')->where('id',$lead_id)->first()->handovermode;
		return view('/DeliveryManagement/AssignDelBoy',$data);
		//print_r($data['product_details']);
   }
   	public function assign_deliveryBoy_post(Request $request)
   	{
		
		$isLoggedIn = $this->isLoggedIn();
		if($isLoggedIn == 'false')
		{
			$url = url('/');
			return redirect()->to($url);
		}
      
		$order_id = $_POST['order_id'];
		$lead_id = $_POST['lead_id'];
		$del_status='';
		$name=$_POST['name'];
		$del_date=$_POST['del_date'];
		$mobileno=$_POST['mobileno'];
		$generator = "1357902468";

		$otp = "";

		for ($i = 1; $i <= 6; $i++)
		{
			$otp .= substr($generator, (rand()%(strlen($generator))), 1);
		}

		$delivery_type=$_POST['delivery_type'];
		
		$invoice_type=$_POST['invoice_type'];
		
		$travel=$_POST['travel'];
		$payment_mode=$_POST['payment_mode'];
		$line_item = $_POST['line_item_1'];
		$equipment = $_POST['line_item_1'][0];
		$line_item = implode(",",$line_item);
		$UpdatedBy=session('username');
		$del_assigned_to = '';
		$helpers = '';
		$helpers_temp = "No";
		$misc_orders = config('app.misc_orders');
		if(isset($_POST['self_pick']))
		{
			$del_assigned_to = 'Customer';
			$del_status = 'Delivered';
			$helpers = "[No helper]";
			$helpers_temp = "No";
		}
		elseif(in_array($equipment,$misc_orders))
		{
			$del_assigned_to=$_POST['del_assigned_to'];
			$del_status = $_POST['del_status'];
			if($_POST['helpers'][0] != "No Helper")
			{
				$helpers=$_POST['helpers'];
				$helpers = json_encode($helpers, true);
				$helpers_temp = "Yes";
			}
			else
			{
				$helpers = "[No helper]";
				$helpers_temp = "No";
			}
		}
		else
		{
			$del_assigned_to=$_POST['del_assigned_to'];
			$del_status = $_POST['del_status'];
			if($_POST['helpers'][0] != "No Helper")
			{
				$helpers=$_POST['helpers'];
				$helpers = json_encode($helpers, true);
				$helpers_temp = "Yes";
			}
			else
			{
				$helpers = "[No helper]";
				$helpers_temp = "No";
			}
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
			// 'delivery_otp' =>$otp,
			'order_approval_status'=>'Approved',
			'reminder_state'=>0
		];       
		$del_order = new DelOrders();

		//for activty log
		
		$getOldData = DB::table('del_orders')->where('order_id',$order_id)->first(array_keys($update_data));//before update
		
		$del_order->where('order_id', $order_id)->update($update_data);//updated
		$del_order->where('order_id', $order_id)->update($update_data);
		$order_details_ids = DB::table('order_details')->where('order_id',$order_id)->whereNotIn('current_status',['Cancel'])->where('sale_rental','Rental')->whereNotIn('vendor_warehouse_id',[19,217])->get();
		if($order_details_ids->count() != 0){
			$order_details_ids = $order_details_ids->pluck('id');
		  foreach($order_details_ids as $key=>$id){
	
			  EditOrderController::updateVendorInOutInventory($id,'in');
		  }
		}
		foreach ($update_data as $key => $upData)
		{
			$insertData = [
                'order_type'=>'DO',
                'key_id'=>$order_id,
                'operation'=>'Order Assigned',
                'fields'=>$key,
                'old_value'=>$getOldData->$key,
                'new_value'=>$upData,
                'updated_by'=>session('username')
            ];
            ActivityLog::insert($insertData); //insert into activity log
		}
		$assignArr = [
			'assign_at'=>Carbon::now()->toDateTimeString(),
			'assign_by'=>session('username'),
		];
		foreach ($assignArr as $key => $assignData) 
		{
			$assignInsert = [
				'order_type'=>'DO',
				'key_id'=>$order_id,
				'operation'=>'Order Assigned',
				'fields'=>$key,
				'old_value'=>null,
				'new_value'=>$assignData,
				'updated_by'=>session('username'),
				
			];
			ActivityLog::insert($assignInsert);
		}

		DB::UPDATE("UPDATE leads SET lead_status = 'Order Generated' WHERE id = $lead_id");
		$leads_log = new leads_log();
		$leads_log_data = [
			'log_lead_id' =>$lead_id,
			'log_order_id'=>$order_id,
			'log_lead_status'=>'Order Assigned',
			'log_order_type'=>'DO',
			'log_date' => date('Y-m-d'),
			'log_time' => date('H:i:s'),
			'updated_by' => session('username')
		];
		$leads_log->insert($leads_log_data);

		// Send Whats app Message to delivery boy and helpers and operation manager
		$numbers = array();
		if(isset($_POST['self_pick']))
		{
			$business_head_id = config('app.business_head_id');
			$business_head_number = DB::table('user')->select('contact_no')->where('id',$business_head_id)->get()->first();
			$business_head_number = $business_head_number->contact_no;
			array_push($numbers,$business_head_number);
			$accounts_id = config('app.accounts_id');
			$accounts_contact_no = DB::table('user')->select('contact_no')->where('id',$accounts_id)->get()->first();
			$accounts_contact_no = $accounts_contact_no->contact_no;
			array_push($numbers,$accounts_contact_no);
		}
		else{
			$business_head_id = config('app.business_head_id');
			$business_head_number = DB::table('user')->select('contact_no')->where('id',$business_head_id)->get()->first();
			$business_head_number = $business_head_number->contact_no;
			$assigned_number = DB::table('delusers')->select('contact_no')->where('username',$del_assigned_to)->get()->first();
			$assigned_number = $assigned_number->contact_no;
			array_push($numbers,$business_head_number);
			array_push($numbers,$assigned_number);
			$accounts_id = config('app.accounts_id');
			$accounts_contact_no = DB::table('user')->select('contact_no')->where('id',$accounts_id)->get()->first();
			$accounts_contact_no = $accounts_contact_no->contact_no;
			array_push($numbers,$accounts_contact_no);
			// dd($helpers);
			if($helpers_temp != "No")
			{
				$helpers_number = DB::table('delusers')->select('contact_no')->whereIn('username',json_decode($helpers))->get()->toArray();
				foreach($helpers_number as $key=>$value)
				{
					array_push($numbers,$value->contact_no);
				}
			}

			// dd($numbers);
		}
		$orderTypeNotIn = config('app.order_type');        
		// $orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";
		$order_all_details = DB::table('del_orders')
							->join('order_details','order_details.order_id','=','del_orders.order_id')
							->join('products','products.id','order_details.product_id')
							->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
							->select('del_orders.*','order_details.*','products.product_name','customer_details.customer_type')
							->where('del_orders.order_id',$order_id)
							->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
							->get()
							->toArray();		
		$customer_name = $order_all_details[0]->shipping_first_name;
		$mobile_no = $order_all_details[0]->mobileno;
		$address = $order_all_details[0]->fulldetails;
		$payment_mode = $order_all_details[0]->PaymentMode;
		$amount = $order_all_details[0]->TotalAmt;
		if($order_all_details[0]->customer_type == 'Corporate')
		{
			$amount = 0;
		}		
		$product_names = array();
		$total_qty = 0;
		foreach ($order_all_details as $key=>$value)
		{
			$total_qty++;
			if($value->sale_rental == 'Rental')
			{

				$temp_prod_name = $value->product_name.' (Id: '.$value->unique_id.')';
				array_push($product_names,$temp_prod_name);
			}
			else{

				array_push($product_names,$value->product_name);
			}
		}
		$product_names = implode(', ',$product_names);
		// foreach ($numbers as $key => $value) {


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
    "phoneNumber": "'.$mobile_no.'",
    "type": "Template",
    "template": {
        "name": "assigndelivery",
        "languageCode": "en",
        "headerValues": [
            "header_variable_value"
        ],
        "bodyValues": [
            "'.$customer_name.'",
            "'.$order_id.'",
            "'.$amount.'"
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
echo $response;



		// 	$url = "https://lj7724mli5.execute-api.ap-south-1.amazonaws.com/prod/sendwhatsappmsg";
		// 	$curl = curl_init();
		// 	curl_setopt($curl, CURLOPT_URL, $url);
		// 	curl_setopt($curl, CURLOPT_POST, true);
		// 	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			
		// 	$headers = array(
		// 	   "Accept: application/json",
		// 	   "Content-Type: application/json",
		// 	);
		// 	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		// 	if(config('app.app_env') == 'devweb')
		// 	{
		// 		$value = config('app.developer_contact');
		// 	}
		// 	$data =[
		// 		"portno"=> "11140",
		// 		"namespace"=> "b9a23cb4_89ed_4fe2_b849_20775908ff5e",
		// 		"countrycode"=> "91",
		// 		"mobileno" => "$value",
		// 		"templatename" => "order_assignment_delboy",
		// 		"templateparams" => [ 
		// 			["type"=> "text","text"=> "Delivery *Order Id: $order_id*"],
		// 			["type"=> "text","text"=> "$customer_name"],
		// 			["type"=> "text","text"=> "$mobile_no"],
		// 			["type"=> "text","text"=> "$address"],
		// 			["type"=> "text","text"=> "$product_names"],
		// 			["type"=> "text","text"=> "$total_qty"],
		// 			["type"=> "text","text"=> "$amount"],
		// 			["type"=> "text","text"=> "$payment_mode"],
		// 	],
		//    ];
		//    //dd($data);
		//    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
		   
		//    $resp = curl_exec($curl);
		//    curl_close($curl);
			// print_r($resp);
		   
			// dd($resp);
		// }

		//-----Send Lead Creation Message to Customer-----//
		
		if(isset($_POST['sendSms']) && $_POST['sendSms'] == 'send')
		{
			$curl = curl_init();
			if(config('app.app_env') == 'devweb')
			{
				$mobileno = config('app.developer_contact');
			}
			curl_setopt_array($curl, array(
				CURLOPT_URL => "https://api.msg91.com/api/v5/flow/",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"60cc49120eeed16fcd62d103\",\n  \"sender\": \"QUALCR\",\n  \"mobiles\": \"91$mobileno\",\n  \"name\": \"$name\",\n  \"orderno\": \"$order_id\",\n  \"equpname\": \"$equipment...\",\n  \"date\": \"$del_date\",\n  \"amount\": \"amount\"}",
				// CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"61cc110117fc7d756f0d91d2\",\n  \"sender\": \"qualcr\",\n  \"mobiles\": \"91$mobileno\",\n  \"name\": \"$name\",\n  \"orderno\": \"$order_id\",\n  \"equipment\": \"$equipment...\",\n  \"date\": \"$del_date\",\n  \"amount\": \"amount\",\n  \"otp\": \"$otp\"}",
				CURLOPT_HTTPHEADER => array(
				"authkey: 267641AmFwcnWjDS5e6b4757P1",
				"content-type: application/JSON"
				),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
				echo("cURL Error #:" . $err);
			} else {
				echo($response);
			}
		}
		
		//labour charges add
		


		return redirect('/confirmed_delivery')->with('Message','Order Assigned Successfully');
		
		// return redirect($_POST['previous_url']);
   	}
   public function logout()
   {
	$isLoggedIn = $this->isLoggedIn();
	if($isLoggedIn == 'false')
	{
		$url = url('/');
		return redirect()->to($url);
	}
	   $request = new Request();
	   //session()->destroy();
	   session(['isLoggedIn' => 'false']);
	   //$data = session()->all();
	   //print_r($data);      
	   return view('Admin/admin_login');
   }

   public function filterDeliveryOrder($filter_by)
   {

		$order_details = DB::table('del_orders')
									->join('order_details','order_details.order_id','=','del_orders.order_id')
									->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
									->distinct('order_details.order_id')
									->select(
										'del_orders.*',
										'order_details.created_at as created_at'
									)
									->where('del_orders.order_approval_status','Approved')
									->where('del_orders.DelAssignedTo','Pending')
									->where('del_orders.deliverypickup','Delivery')
									->whereNotIn('del_orders.status',['Cancel'])
									->when($filter_by == 'today',function($query){
										$query->where('del_orders.DelDate',date('d-m-Y'));
									})
									->when($filter_by == 'yesterday',function($query){
										$query->where('del_orders.DelDate',date('d-m-Y',strtotime("-1 days")));
									})
									->when($filter_by == 'past_3_days',function($query){
										$start_date = date('d-m-Y',strtotime("-2 days"));
										$end_date = date('d-m-Y');
										$query->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$start_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$end_date','%d-%m-%Y'))")]);
									})
									->when($filter_by == 'week',function($query){
										$start_date = date('d-m-Y',strtotime("-7 days"));
										$end_date = date('d-m-Y');
										$query->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$start_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$end_date','%d-%m-%Y'))")]);
									})
									->when($filter_by == 'month',function($query){
										$month = date('m-Y');
										$start_date_temp = '01-'.$month;
										$start_date = date('d-m-Y',strtotime($start_date_temp));
										$end_date_temp = '31-'.$month;
										$end_date = date('d-m-Y',strtotime($end_date_temp));
										$query->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$start_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$end_date','%d-%m-%Y'))")]);
									})
									->when(session('city_based_access') == '1',function($query){
										$query->where('customer_details.citygroup',session('user_city'));
									})
									->get();
		if($filter_by =='today')
		{
			$date = date('d-m-Y');
			$data['start_date'] = date('Y-m-d');
			$data['end_date'] = date('Y-m-d');
				// $whereClause = "DelDate = '$date'";
			echo "<script>localStorage['filtered']='today';</script>";
		}
		elseif($filter_by =='yesterday')
		{
			$prevDate = date('d-m-Y',strtotime("-1 days"));
			$data['start_date'] = date('Y-m-d',strtotime("-1 days"));
			$data['end_date'] = date('Y-m-d');
				// $whereClause = "DelDate = '$prevDate'";
			echo "<script>localStorage['filtered']='yesterday';</script>";
		}
		elseif($filter_by =='past_3_days')
		{
			$past_three_days = date('d-m-Y',strtotime("-2 days"));
			$data['start_date'] = date('Y-m-d',strtotime("-2 days"));
			$data['end_date'] = date('Y-m-d');
				// $whereClause = "DelDate >= '$past_three_days'";
			echo "<script>localStorage['filtered']='past_3_days';</script>";
		}
		 elseif($filter_by =='week')
		{
			$past_three_days = date('d-m-Y',strtotime("-7 days"));
			$data['start_date'] = date('Y-m-d',strtotime("-7 days"));
			$data['end_date'] = date('Y-m-d');
				// $whereClause = "DelDate >= '$past_three_days'";
			echo "<script>localStorage['filtered']='week';</script>";
		}
		elseif($filter_by =='month')
		{
			$month = date('m-Y');
			$start_date_temp = '01-'.$month;
			$start_date = date('d-m-Y',strtotime($start_date_temp));
			$end_date_temp = '31-'.$month;
			$end_date = date('d-m-Y',strtotime($end_date_temp));
			$data['start_date'] = date('Y-m-d',strtotime($start_date_temp));
			$data['end_date'] = date('Y-m-d',strtotime($end_date_temp));
				// $whereClause = "DelDate BETWEEN '$start_date' AND '$end_date'";
		}
		elseif($filter_by == 'all')
		{   
			//$order_details = DB::select("SELECT * FROM del_orders WHERE order_approval_status = 'Approved' order by order_id DESC");
				// $orderTypeNotIn = config('app.order_type');
				// $orderTypeNotIn = "'".implode("','",$orderTypeNotIn)."'";
				// $order_details = DB::select("SELECT 
				// 							DISTINCT('order_details.order_id'),del_orders.*,order_details.created_at as created_at
				// 						FROM 
				// 						del_orders,order_details 
				// 						WHERE 
				// 							del_orders.order_id = order_details.order_id
				// 							AND del_orders.order_approval_status = 'Approved' 
				// 							AND del_orders.DelAssignedTo ='Pending' 
				// 							AND del_orders.deliverypickup = 'Delivery' 
				// 							AND del_orders.status NOT IN ('Cancel')
				// 							AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)
				// 							order by order_id DESC ");

				// $data['order_details'] = json_decode(json_encode($order_details),true);
				// //$data['order_details'] = json_decode(json_encode($order_details),true);
				
				echo "<script>localStorage['filtered']='all';</script>";
				// return view('/DeliveryManagement/confirmed_delivery',$data);
			}
		
			//$order_details = DB::select("SELECT * FROM del_orders WHERE order_approval_status = 'Approved' AND $whereClause  order by order_id DESC");
			// $orderTypeNotIn = config('app.order_type');
			// $orderTypeNotIn = "'".implode("','",$orderTypeNotIn)."'";
			// $order_details = DB::select("SELECT 
			// 			DISTINCT('order_details.order_id'),del_orders.*,order_details.created_at as created_at
			// 		FROM 
			// 		del_orders,order_details 
			// 	WHERE 
			// 		del_orders.order_id = order_details.order_id
			// 		AND del_orders.order_approval_status = 'Approved' 
			// 		AND del_orders.DelAssignedTo ='Pending' 
			// 		AND del_orders.deliverypickup = 'Delivery' 
			// 		AND del_orders.status NOT IN ('Cancel')
			// 		AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)
			// 		AND $whereClause
			// 		order by order_id DESC ");
			$data['order_details'] = json_decode(json_encode($order_details),true);
			
			return view('/DeliveryManagement/confirmed_delivery',$data);
	}

	public function filterDeliveryOrderDWS()
	{
		$data['start_date'] = $_POST['start_date'];
		$data['end_date'] = $_POST['end_date'];
		$start_date = date('d-m-Y',strtotime($_POST['start_date']));
		$end_date = date('d-m-Y',strtotime($_POST['end_date']));
		// $whereClause = "STR_TO_DATE(DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y')";
		// $order_details = DB::select("SELECT * FROM del_orders WHERE order_approval_status = 'Approved' AND $whereClause  order by order_id DESC");
		$order_details = DB::table('del_orders')
									->join('order_details','order_details.order_id','=','del_orders.order_id')
									->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
									->distinct('order_details.order_id')
									->select(
										'del_orders.*',
										'order_details.created_at as created_at'
									)
									->where('del_orders.order_approval_status','Approved')
									->where('del_orders.DelAssignedTo','Pending')
									->where('del_orders.deliverypickup','Delivery')
									->whereNotIn('del_orders.status',['Cancel'])

									->when($start_date,function($query)use($start_date,$end_date){
										$query->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$start_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$end_date','%d-%m-%Y'))")]);
									})
									->when(session('city_based_access') == '1',function($query){
										$query->where('customer_details.citygroup',session('user_city'));
									})
									->get();
		$data['order_details'] = json_decode(json_encode($order_details),true);
		
		return view('/DeliveryManagement/confirmed_delivery',$data);
	}

   //pickup requested orders

   public function pickup_request()
   {
	$isLoggedIn = $this->isLoggedIn();
	if($isLoggedIn == 'false')
	{
		$url = url('/');
		return redirect()->to($url);
	}
       if($_SERVER['REQUEST_METHOD']=='GET')
       { 
            $today = date('d-m-Y');
			$past_three_days = date('d-m-Y',strtotime("-2 days"));
			$data['start_date'] = date('Y-m-d',strtotime("-2 days"));
			$data['end_date'] = date('Y-m-d');
				// $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$past_three_days','%d-%m-%Y') AND STR_TO_DATE('$today','%d-%m-%Y')";
				//$pickup_request = DB::select("SELECT * FROM pickups,order_details where order_details.id=pickups.order_details_id AND order_details.order_id=pickups.order_id AND (order_details.current_status='Pending Pickup' OR order_details.current_status='Pickuped')");
				// $pickup_request = DB::select("SELECT 
				// 									DISTINCT('pickups.pickup_order_id'),
				// 									del_orders.*,
				// 									pickups.created_at as created_at
				// 								FROM del_orders,pickups 
				// 								where
				// 									del_orders.order_id = pickups.pickup_order_id 
				// 									AND del_orders.deliverypickup='Pick Up' 
				// 									AND $whereClause AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) ORDER BY STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') DESC ");

				$pickup_request = DB::table('pickups')
										->join('del_orders','del_orders.order_id','=','pickups.pickup_order_id')
										->join('order_details','order_details.id','=','pickups.order_details_id')
										->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
										->distinct('pickups.pickup_order_id')
										->select('del_orders.*','pickups.created_at as created_at')
										->where('del_orders.deliverypickup','Pick Up')
										->whereNotIn('del_orders.status',['Cancel'])
										->whereBetween(DB::raw("(STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$past_three_days','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$today','%d-%m-%Y'))")])
										->when(session('city_based_access'),function($query){
											$query->where('customer_details.citygroup',session('user_city'));
										})
										// ->orderBy('del_orders.DelDate','DESC')
										->orderBy('del_orders.order_id','DESC')
										->get();
            $data['pickup_request'] = json_decode(json_encode($pickup_request),true);
			echo "<script>localStorage['filtered']='past_3_days';</script>";
            return view('/DeliveryManagement/PickupRequest',$data);
       }
   }
   // Filter Pickup Request
   public function filterPickupOrder($filter_by)
   {
	$isLoggedIn = $this->isLoggedIn();
	if($isLoggedIn == 'false')
	{
		$url = url('/');
		return redirect()->to($url);
	}
		if($filter_by =='today')
		{
			$date = date('d-m-Y');
			$data['start_date'] = date('Y-m-d');
			$data['end_date'] = date('Y-m-d');
			// $whereClause = "DelDate = '$date'";
			// $whereClause = "STR_TO_DATE(DelDate,'%d-%m-%Y') = STR_TO_DATE('$date','%d-%m-%Y')";
			echo "<script>localStorage['filtered']='today';</script>";
		}
		elseif($filter_by =='yesterday')
		{
			$prevDate = date('d-m-Y',strtotime("-1 days"));
			$data['start_date'] = date('Y-m-d',strtotime("-1 days"));
			$data['end_date'] = date('Y-m-d',strtotime("-1 days"));
			// $whereClause = "DelDate = '$prevDate'";
			// $whereClause = "STR_TO_DATE(DelDate,'%d-%m-%Y') = STR_TO_DATE('$prevDate','%d-%m-%Y')";
			echo "<script>localStorage['filtered']='yesterday';</script>";
		}
		elseif($filter_by =='past_3_days')
		{
			$today = date('d-m-Y');
			$past_three_days = date('d-m-Y',strtotime("-2 days"));
			$data['start_date'] = date('Y-m-d',strtotime("-2 days"));
			$data['end_date'] = date('Y-m-d');
			// $whereClause = "DelDate >= '$past_three_days'";
			// $whereClause = "STR_TO_DATE(DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$past_three_days','%d-%m-%Y') AND STR_TO_DATE('$today','%d-%m-%Y')";
			echo "<script>localStorage['filtered']='past_3_days';</script>";
		}
		 elseif($filter_by =='week')
		{
			$today = date('d-m-Y');
			$past_three_days = date('d-m-Y',strtotime("-7 days"));
			$data['start_date'] = date('Y-m-d',strtotime("-7 days"));
			$data['end_date'] = date('Y-m-d');
			// $whereClause = "DelDate >= '$past_three_days'";
			// $whereClause = "STR_TO_DATE(DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$past_three_days','%d-%m-%Y') AND STR_TO_DATE('$today','%d-%m-%Y')";
			echo "<script>localStorage['filtered']='week';</script>";
		}
		elseif($filter_by =='month')
		{
			$month = date('m-Y');
			$start_date_temp = '01-'.$month;
			$start_date = date('d-m-Y',strtotime($start_date_temp));
			$end_date_temp = '31-'.$month;
			$end_date = date('d-m-Y',strtotime($end_date_temp));
			
			$data['start_date'] = date('Y-m-d',strtotime($start_date_temp));
			$data['end_date'] = date('Y-m-d',strtotime($end_date_temp));

			// $whereClause = "STR_TO_DATE(DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y')";
		}
		elseif($filter_by == 'all')
		{
			// $orderTypeNotIn = config('app.order_type');
			// $orderTypeNotIn = "'".implode("','",$orderTypeNotIn)."'";

			// $pickup_request = DB::select("SELECT * FROM del_orders where deliverypickup='Pick Up' AND (status='Pending' OR status='Assigned' OR status='Picked up') AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) ORDER BY STR_TO_DATE(DelDate,'%d-%m-%Y') DESC ");

			// $data['pickup_request'] = json_decode(json_encode($pickup_request),true);
			echo "<script>localStorage['filtered']='all';</script>";
			// return view('/DeliveryManagement/PickupRequest',$data);
		}
		$orderTypeNotIn = config('app.order_type');
		// $orderTypeNotIn = "'".implode("','",$orderTypeNotIn)."'";
	   
		// $pickup_request = DB::select("SELECT * FROM del_orders where deliverypickup='Pick Up' AND $whereClause AND (status='Pending' OR status='Assigned' OR status='Picked up') AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) ORDER BY STR_TO_DATE(DelDate,'%d-%m-%Y') DESC ");
		$pickup_request = DB::table('pickups')
								->join('del_orders','del_orders.order_id','=','pickups.pickup_order_id')
								->join('order_details','order_details.id','=','pickups.order_details_id')
								->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
								->distinct('pickups.pickup_order_id')
								->select('del_orders.*','pickups.created_at as created_at')
								->where('del_orders.order_approval_status','Approved')
								// ->where('del_orders.DelAssignedTo','Pending')
								->where('del_orders.deliverypickup','Pick Up')
								->whereNotIn('del_orders.status',['Cancel'])

								->when($filter_by == 'today',function($query){
									$query->where('del_orders.DelDate',date('d-m-Y'));
								})
								->when($filter_by == 'yesterday',function($query){
									$query->where('del_orders.DelDate',date('d-m-Y',strtotime("-1 days")));
								})
								->when($filter_by == 'past_3_days',function($query){
									$start_date = date('d-m-Y',strtotime("-12 days"));
									$end_date = date('d-m-Y');
									$query->whereBetween(DB::raw("(STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$start_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$end_date','%d-%m-%Y'))")]);
								})
								->when($filter_by == 'week',function($query){
									$start_date = date('d-m-Y',strtotime("-7 days"));
									$end_date = date('d-m-Y');
									$query->whereBetween(DB::raw("(STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$start_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$end_date','%d-%m-%Y'))")]);
								})
								->when($filter_by == 'month',function($query){
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
								->get();
        $data['pickup_request'] = json_decode(json_encode($pickup_request),true);
		
		return view('/DeliveryManagement/PickupRequest',$data);
   }
   public function filterPickupOrderDWS()
   {
		$start_date = date('d-m-Y',strtotime($_POST['start_date']));
		$end_date = date('d-m-Y',strtotime($_POST['end_date']));
		
		$data['start_date'] = date('Y-m-d',strtotime($_POST['start_date']));
		$data['end_date'] = date('Y-m-d',strtotime($_POST['end_date']));

			$pickup_request = DB::table('pickups')
								->join('del_orders','del_orders.order_id','=','pickups.pickup_order_id')
								->join('order_details','order_details.id','=','pickups.order_details_id')
								->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
								->distinct('pickups.pickup_order_id')
								->select('del_orders.*','pickups.created_at as created_at')
								->where('del_orders.order_approval_status','Approved')
								->where('del_orders.DelAssignedTo','Pending')
								->where('del_orders.deliverypickup','Pick Up')
								->whereNotIn('del_orders.status',['Cancel'])
								->when($start_date,function($query)use($start_date,$end_date){
									$query->whereBetween(DB::raw("(STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$start_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$end_date','%d-%m-%Y'))")]);
								})
								->when(session('city_based_access') == '1',function($query){
									$query->where('customer_details.citygroup',session('user_city'));
								})
								->get();
        $data['pickup_request'] = json_decode(json_encode($pickup_request),true);
		
		return view('/DeliveryManagement/PickupRequest',$data);
   }
   	//pickup orders assign del boy
	public function assign_pickup_delboy_old($order_id)
	{    
		$isLoggedIn = $this->isLoggedIn();
		if($isLoggedIn == 'false')
		{
			$url = url('/');
			return redirect()->to($url);
		}
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
										pickups.pickup_date as pickup_date,
										pickups.cash_amount as cash_amount,
										vendor_products.product_rent_approved as vendor_rent
									FROM
										pickups,products,order_details,vendor_products
									Where
										pickups.pickup_order_id = '$order_id'
										AND order_details.id = pickups.order_details_id
										AND order_details.product_id = products.id
										AND order_details.vendor_product_id = vendor_products.id");
		$data['product_details'] = json_decode(json_encode($product_details),true);

		$total_rent = 0;
		$total_deposit = 0;
		$vendor_details=array();
		foreach($data['product_details'] as $p_details)
		{
			$total_rent += $p_details['cash_amount'];
			$total_deposit += $p_details['product_deposite'];
		}
		$data['total_rent'] = $total_rent;
		$data['total_deposit'] = $total_deposit;
		$pickup_date = $data['order_details'][0]['DelDate'];
		//vendor warehouse details
		for($i=0; $i<count($data['product_details']); $i++)
		{
			$get_vendor_id = $data['product_details'][$i]['vendor_id'];
			$get_warehouse_details = DB::select("SELECT * FROM vendor_warehouse WHERE vendor_id = $get_vendor_id");
			$get_warehouse_details =json_decode(json_encode($get_warehouse_details), true);
			$data['product_details'][$i]['vendor_warehouse_details'] = $get_warehouse_details;
			$get_warehouse_details = DB::select("SELECT * FROM vendor_warehouse WHERE vendor_id = 17 AND id IN (19,217)");
			$get_warehouse_details =json_decode(json_encode($get_warehouse_details), true);
			$data['product_details'][$i]['q5c_warehouse_details'] = $get_warehouse_details;
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
	public function assign_pickup_delboy($order_id)
	{
		$order_details = DB::table('del_orders')->where('order_id',$order_id)->get()->toArray();
		$delboys = DB::table('delusers')->where('role','user')->where('status','Active')->get()->toArray();
		// $delboys = DB::select("SELECT * FROM delusers WHERE role='user' AND status='Active'");
		$product_details = DB::table('order_details')
							->join('products','order_details.product_id','=','products.id')
							->join('pickups','order_details.id','=','pickups.order_details_id')
							->join('vendor_products','order_details.vendor_product_id','=','vendor_products.id')
							->join('vendor_details','order_details.vendor_id','=','vendor_details.id')
							->join('vendor_warehouse','order_details.vendor_warehouse_id','=','vendor_warehouse.id')
							->select(
								'order_details.*',
								'products.product_name as product_name',
								'pickups.id as pickup_main_id',
								'pickups.order_details_id as order_details_id',
								'pickups.pickup_date as pickup_date',
								'pickups.cash_amount as cash_amount',
								'vendor_products.product_rent_approved as vendor_rent',
								'vendor_details.registered_name',
								'vendor_warehouse.wh_name',
								'vendor_warehouse.wh_area',
								'vendor_warehouse.wh_city'
							)
							->where('pickups.pickup_order_id',$order_id)
							->get()
							->toArray();		

		$total_rent = 0;
		$total_deposit = 0;
		$vendor_details=array();
		foreach($product_details as $key=>$product_detail)
		{
			if(DB::table('cr_dr_note')->where('order_id',$product_detail->order_id)->exists())
			{
				$product_details[$key]->product_rent = RenewalPickupController::fetchCrDrData($product_detail->order_details_id,'R');
				$product_details[$key]->product_deposite = RenewalPickupController::fetchCrDrData($product_detail->order_details_id,'D');
				$product_details[$key]->transport = RenewalPickupController::fetchCrDrData($product_detail->order_details_id,'T');
			}
			$total_rent += $product_detail->cash_amount;
			$total_deposit += $product_detail->product_deposite;
		}
		$total_rent = $total_rent;
		$total_deposit = $total_deposit;
		$pickup_date = $order_details[0]->DelDate;
		//vendor warehouse details
		$get_customer_details = DB::table('leads')
										->join('customer_details','leads.customer_id','=','customer_details.cust_id')
										->select('customer_details.*','leads.id as lead_id','leads.equipment_requirement')
										->where(DB::raw("(STR_TO_DATE(leads.converted_at,'%Y-%m-%d'))"),date('Y-m-d',strtotime($pickup_date)))
										->where('leads.lead_status','Converted')
										->get()
										->toArray();
		// dd($get_customer_details);

		foreach ($product_details as $key1=>$value1)
		{			
			$get_warehouse_details = DB::table('vendor_warehouse')->where('vendor_id',$value1->vendor_id)->get()->toArray();
			$product_details[$key1]->vendor_warehouse_details = $get_warehouse_details;
			$get_warehouse_details = DB::table('vendor_warehouse')->where('vendor_id',17)->whereIn('id',[19,217])->get()->toArray();
			$product_details[$key1]->q5c_warehouse_details = $get_warehouse_details;	
			$customer_address = array();
			foreach ($get_customer_details as $key=>$value)
			{
				$products = json_decode($value->equipment_requirement);
				if(in_array($value1->product_id,$products))
				{
					$count = count($customer_address);
					$customer_address[$count] = $value;
				}
			}
			$product_details[$key1]->customer_address = $customer_address;
			if(DB::table('adjustment_table')->where('adjusted_order_details_id',$value1->id)->where('adjustment_table.flag','A')->exists())
			{
				// dd("Exists");
				$records = DB::table('adjustment_table')->select('adjusted_amount')->where('adjusted_order_details_id',$value1->id)->where('fromtype','D')->where('adjustment_table.flag','A')->get();
				// dd($records);
				$sum = $records->pluck('adjusted_amount')->sum();
				// dd($sum);
				// dd($product->product_deposite);
				// $productData[$key]->adjusted_deposit = $sum;
				// $totalAdjDeposit = $totalAdjDeposit + $sum;
				$product_details[$key1]->product_deposite = $product_details[$key1]->product_deposite - $sum;
				// dd($totalDeposit);
			}
		}
		// dd($product_details);
		return view('DeliveryManagement.AssignPickup',compact('product_details','total_rent','total_deposit','order_details','delboys'));
	}
	public function getConvCustomers($order_id,$pickup_date)
	{
		$order_details = DB::table('del_orders')->where('order_id',$order_id)->get()->toArray();
		$delboys = DB::table('delusers')->where('role','user')->get()->toArray();
		$product_details = DB::table('order_details')
							->join('products','order_details.product_id','=','products.id')
							->join('pickups','order_details.id','=','pickups.order_details_id')
							->join('vendor_products','order_details.vendor_product_id','=','vendor_products.id')
							->select(
								'order_details.*',
								'products.product_name as product_name',
								'pickups.id as pickup_main_id',
								'pickups.order_details_id as order_details_id',
								'pickups.pickup_date as pickup_date',
								'pickups.cash_amount as cash_amount',
								'vendor_products.product_rent_approved as vendor_rent'
							)
							->where('pickups.pickup_order_id',$order_id)
							->get()
							->toArray();		

		$total_rent = 0;
		$total_deposit = 0;
		$vendor_details=array();
		foreach($product_details as $key=>$product_detail)
		{
			$total_rent += $product_detail->cash_amount;
			$total_deposit += $product_detail->product_deposite;
		}
		$total_rent = $total_rent;
		$total_deposit = $total_deposit;
		// $pickup_date = $order_details[0]->DelDate;
		//vendor warehouse details
		$get_customer_details = DB::table('leads')
										->join('customer_details','leads.customer_id','=','customer_details.cust_id')
										->select('customer_details.*','leads.id as lead_id','leads.equipment_requirement')
										->where(DB::raw("(STR_TO_DATE(leads.converted_at,'%Y-%m-%d'))"),date('Y-m-d',strtotime($pickup_date)))
										->where('leads.lead_status','Converted')
										->get()
										->toArray();
		// dd($get_customer_details);
		$customer_details = array();
		$vendor_warehouse_details = array();
		$q5c_warehouse_details = array();
		foreach ($product_details as $key1=>$value1)
		{
			$get_warehouse_details = DB::table('vendor_warehouse')->where('vendor_id',$value1->vendor_id)->get()->toArray();
			$vendor_warehouse_details[$key1] = $get_warehouse_details;
			$get_warehouse_details = DB::table('vendor_warehouse')->where('vendor_id',17)->whereIn('id',[19,217])->get()->toArray();
			$q5c_warehouse_details[$key1] = $get_warehouse_details;	
			$customer_address = array();
			foreach ($get_customer_details as $key=>$value)
			{
				$products = json_decode($value->equipment_requirement);
				if(in_array($value1->product_id,$products))
				{
					$count = count($customer_address);
					$customer_address[$count] = $value;
				}
			}
			$customer_details[$key1] = $customer_address;
		}
		$data['customer_details'] = $customer_details;
		$data['vendor_warehouse_details'] = $vendor_warehouse_details;
		$data['q5c_warehouse_details'] = $q5c_warehouse_details;
		return $data;
	}

    //assign del boy pickup order post
    public function assign_pickup_delboy_post(Request $request)
    {
		// dd($request);
        $pickup_order_id = $request->get('pickup_order_id');
        $order_details_id = $request->get('order_details_id');
		$pickup_main_id = $request->get('pickup_main_id');
        $order_id = $request->get('order_id');
		$prod_name = $request->get('prod_name');
		$name=$request->get('name');
		$del_date=$request->get('del_date');
		$transport = $request->get('transport');
		$curr_deposit = $request->get('deposit');
		$act_deposit = $request->get('hidden_deposit');
		$comment = $request->get('comment');
		leads_log::updateOrCreate(
			[
			   'log_order_id' => $pickup_order_id,
			   'log_lead_status' => 'Order Generated',
			   'updated_by' => session('username')
			],
			[
			   'log_order_lead_date' => date('Y-m-d',strtotime($del_date)).' '.date('H:i:s'),
			   'log_date' => date('Y-m-d'),
			   'log_time' => date('H:i:s'),
			]);
		$mobileno=$request->get('mobileno');
		$pickup_date = $request->get('pickup_date');
		$vendor_warehouse_id = $request->get('vendor_warehouse_id');
        //$lead_id = $request->get('lead_id');
        $del_status="Assigned";
        $delivery_type='Pick Up';
        $del_assigned_to='';
        $invoice_type=$request->get('invoice_type');
        $travel=$request->get('travel');
		$helpers='';
		$helpers_temp = "No";
        $payment_mode=$request->get('payment_mode');
        $total_deposit=$request->get('total_deposit');
        $total_rent=$request->get('total_rent');
        $cash_amount = $request->get('cash_amount');
        $online_amount = $request->get('online_amount');
        $UpdatedBy=session('username');
        $today = date('Y-m-d');
		$vendor_rent = $request->get('vendor_product_rent');
		$warehouse_type = $request->get('group_name');
        $vendor_warehouse_id = $request->get('vendor_warehouse_id');
		$vendor_rented_product_id = $request->get('vendor_rented_product_id');
            //print_r($drop_at);
		// dd($_POST);
        if($payment_mode =='Both')
        {
            $cash_amount = $request->get('cash_amount');
            $online_amount = $request->get('online_amount');
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
			if($request->get('self_pick') == 'on')
			{
				$del_assigned_to = 'Customer';
				$helpers = "[No helper]";
				$helpers_temp = "No";
				$del_status = 'Picked up';
				$pickups = DB::table('pickups')->select('order_details_id')->where('pickup_order_id',$pickup_order_id)->get()->toArray();
				foreach($pickups as $key=>$value)
				{
					OrderDetails::where('id',$value->order_details_id)->update(['current_status'=>'Picked Up']);
				}
			}
			else
			{
				$del_assigned_to=$request->get('del_assigned_to');
				$del_status = "Assigned";
				if($request->get('helpers')[0] != "No Helper")
				{
					$helpers=$request->get('helpers');
					$helpers = json_encode($helpers, true);
					$helpers_temp = "Yes";
				}
				else
				{
					$helpers = "[No helper]";
					$helpers_temp = "No";
				}
			}
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
				// 'created_at'=>date('Y-m-d',strtotime($del_date)).' '.date('H:i:s'),
                'order_approval_status'=>'Approved',
				'reminder_state'=>0
             ];       
             //print_r($update_data);
            $del_order = new DelOrders();     
			$Vendor_Products = new VendorProducts();
			$Pickup = new Pickup();

			//for activity log
			$getOldData = DB::table('del_orders')->where('order_id',$pickup_order_id)->first(array_keys($update_pickup_data));//get old data before update
			
            $del_order->where('order_id', $pickup_order_id)->update($update_pickup_data);//update

			foreach ($update_pickup_data as $key => $upData) 
			{
				$insertData = [
					'order_type'=>'PO',
					'key_id'=>$pickup_order_id,
					'operation'=>'Order Assigned',
					'fields'=>$key,
					'old_value'=>$getOldData->$key,
					'new_value'=>$upData,
					'updated_by'=>session('username')
				];
				ActivityLog::insert($insertData);	
			}
			$assignArr = [
				'assign_at'=>Carbon::now()->toDateTimeString(),
				'assign_by'=>session('username'),
			];
			foreach ($assignArr as $key => $assignData) 
			{
				$assignInsert = [
					'order_type'=>'PO',
					'key_id'=>$pickup_order_id,
					'operation'=>'Order Assigned',
					'fields'=>$key,
					'old_value'=>null,
					'new_value'=>$assignData,
					'updated_by'=>session('username'),
					
				];
				ActivityLog::insert($assignInsert);
			}

			for ($i=0; $i <count($order_details_id) ; $i++) 
			{ 
				$get_order_details_id = $order_details_id[$i];
				$get_pickup_id = $pickup_main_id[$i];
				$get_order_details_data = DB::select("SELECT * FROM order_details WHERE id ='$get_order_details_id' ");
				$get_order_details_data = json_decode(json_encode($get_order_details_data),true);

				$get_vendor_id = $get_order_details_data[0]['vendor_id'];
				$get_warehouse_id = $vendor_warehouse_id[$i];
				$get_warehouse_type = $warehouse_type[$i];
				$get_vendor_rent = $vendor_rent[$i];
				$get_transport = $transport[$i];
				$get_deposit = $curr_deposit[$i];
				$get_act_deposit = $act_deposit[$i];
				$get_comment = $comment[$i];
				// $get_vendor_rented_prod_id = $vendor_rented_product_id[$i];
				$get_vendor_rented_prod_id = $get_order_details_data[0]['rented_product_id'];
				$get_product_id = $get_order_details_data[0]['product_id'];
				$get_product_qty = $get_order_details_data[0]['product_qty'];
				$get_product_brand = $get_order_details_data[0]['product_brand'];
				$get_product_rent = $get_order_details_data[0]['product_rent'];
				$get_product_deposit = $get_order_details_data[0]['product_deposite'];
				$get_vendor_product_details_id = $get_order_details_data[0]['vendor_product_details_id'];
				$get_unique_id = $get_order_details_data[0]['unique_id'];
				$vendor_prod_ids = $get_order_details_data[0]['vendor_product_id'];
				$vdr_actual_wh_id = $get_order_details_data[0]['vendor_warehouse_id'];
				$batch_id = date('Y-m-d')." - ".$get_product_id;
				$check_exist = DB::select("SELECT * FROM vendor_products where id = $vendor_prod_ids ");
				$check_exist = json_decode(json_encode($check_exist),true);

				if($check_exist!=null)
				{
					$drop_location = 'Vendor';
					$vdr_warehouse_type = 0;
					$vdr_prod_details_warehouse_type = 0;
					if($get_warehouse_type =='Virtual Warehouse')
					{
						$drop_location = 'Q5C';
						$vdr_warehouse_type = 0;
						$vdr_prod_details_warehouse_type = 1;
						VirtualVdrInventoryMgmt::insert([
							'order_details_id'=>$get_order_details_id,
							'vdr_prod_details_id'=>$get_vendor_product_details_id,
							'prod_id'=>$get_product_id,
							'vdr_id'=>$get_vendor_id,
							'vdr_wh_id'=>$vdr_actual_wh_id,
							'vir_wh_id'=>$get_warehouse_id,
							'inventory_id'=>$get_unique_id,
							'prod_qty'=>$get_product_qty,
							'status'=>'0',
							'in_time'=>date('Y-m-d H:m:i'),
							'created_by'=>session('username')
						]);
					}
					elseif($get_warehouse_type =='Vendor Warehouse')
					{
						$vdr_warehouse_type = 1;
						$vdr_prod_details_warehouse_type = 2;
						$update_rented_prod = VendorRentedProducts::where('id',$get_vendor_rented_prod_id)->update(['status'=>'Released']);
						
						
					}
					elseif($get_warehouse_type == 'Customer Location')
					{
						$drop_location = 'Customer';
						$vdr_prod_details_warehouse_type = 0;
						TempCustDetails::insert([
							'pickup_id' => $get_pickup_id,
							'cust_id' => $get_warehouse_id,
							'product_id' => $get_product_id,							
							'vendor_product_id' => $vendor_prod_ids,
							'vendor_product_details_id' => $get_vendor_product_details_id,
							'status' => 'Reserved',
							'date' => $pickup_date[0]
						]);
					}

					$get_vendor_product_id = $check_exist[0]['id'];
					$final_qty = $check_exist[0]['product_quantity']+$get_product_qty;
					$update_quantity = [
						'product_quantity' =>$final_qty,
						'virtual_warehouse' => $vdr_warehouse_type
					];
					$Vendor_Products->where('id',$get_vendor_product_id)->update($update_quantity);

					VendorProductDetails::where('id',$get_vendor_product_details_id)->update(['availability_status' => 0,'current_location' => $vdr_prod_details_warehouse_type,'warehouse_id' => $get_warehouse_id]);
					if($vdr_prod_details_warehouse_type == 2){
						DB::table('vendor_rented_inventory')->insert(
							[
								"vendor_id" => $get_vendor_id,
								"order_id" => $pickup_order_id,
								"order_details_id" => $get_order_details_id,
								"inventory_id" => $get_vendor_product_details_id,
								"vendor_product_id" => $vendor_prod_ids,
								"rented_date" => date('Y-m-d'),
								"due_date" => date('Y-m-d'),
								"status" => 'Stop',
								'type' => 'Pickup',
								'created_by'=>session('username')
							]
						);
					}
					//update in pickups table
					$update_pickups = [
						'drop_location' => $drop_location,
						'drop_vendor_id' =>$get_vendor_id,
						'drop_warehouse_id'=>$get_warehouse_id,
						'drop_vendor_product_id'=>$get_vendor_product_id,
						'transport'=>$get_transport
					];
					$Pickup->where('id',$get_pickup_id)->update($update_pickups);
					if($get_act_deposit != $get_deposit){
						DB::table('order_details')->where('id',$get_order_details_id)->update(['product_deposite'=>$get_deposit]);
					}
					if($vdr_warehouse_type == 1){
						EditOrderController::updateVendorInOutInventory($get_pickup_id,'out');
					}
				}
				else
				{
					$vdr_warehouse_type = 0;
					if($get_warehouse_type =='Virtual Warehouse')
					{
						$vdr_warehouse_type = 0;
						VirtualVdrInventoryMgmt::insert([
							'order_details_id'=>$get_order_details_id,
							'vdr_prod_details_id'=>$get_vendor_product_details_id,
							'prod_id'=>$get_product_id,
							'vdr_id'=>$get_vendor_id,
							'vdr_wh_id'=>$vdr_actual_wh_id,
							'vir_wh_id'=>$get_warehouse_id,
							'inventory_id'=>$get_unique_id,
							'prod_qty'=>$get_product_qty,
							'status'=>'0',
							'in_time'=>date('Y-m-d H:m:i'),
							'created_by'=>session('username')
						]);
					}
					elseif($get_warehouse_type =='Vendor Warehouse')
					{
						$vdr_warehouse_type = 1;
						$update_rented_prod = VendorRentedProducts::where('id',$get_vendor_rented_prod_id)->update('status','Released');
						// EditOrderController::updateVendorInOutInventory($get_pickup_id,'out');
					}
					$insert_record = [
						'vendor_id' => $get_vendor_id,
						'product_id' =>$get_product_id,
						'product_quantity'=>$get_product_qty,
						'product_brand'=>$get_product_brand,
						'product_rent_approved'=>$get_vendor_rent,
						'product_deposite'=>$get_product_deposit,
						'warehouse_id'=>$get_warehouse_id,
						'status'=>'Approved',
						'virtual_id'=>$get_unique_id,
						'batch'=>$batch_id,
						'virtual_warehouse'=>$vdr_warehouse_type,
						// 'created_at'=>date('Y-m-d H:i:s')
					];	
					$get_vendor_product_id = $Vendor_Products->insertGetId($insert_record);
					//update in pickups table
					$update_pickups = [
						'drop_vendor_id' =>$get_vendor_id,
						'drop_warehouse_id'=>$get_warehouse_id,
						'drop_vendor_product_id'=>$get_vendor_product_id,
						'updated_at' =>date('Y-m-d H:i:s'),
						'transport'=>$get_transport,
						'comment'=>$get_comment
					];
					$Pickup->where('id',$get_pickup_id)->update($update_pickups);
					if($vdr_warehouse_type == 1){
						EditOrderController::updateVendorInOutInventory($get_pickup_id,'out');
					}
				}
				
			}

			$pickup_data = DB::table('pickups')->join('order_details','order_details.id','=','pickups.order_details_id')->select('order_details.product_deposite')->where('pickups.pickup_order_id',$pickup_order_id)->get();

			$pickup_sum = $pickup_data->pluck('product_deposite')->toArray();

			$pickup_sum = array_sum($pickup_sum);

			DB::table('del_orders')->where('order_id',$pickup_order_id)->update(['comment'=>implode(',',$comment)]);

			$numbers = array();
			if($request->get('self_pick') == 'on')
			{
				$business_head_id = config('app.business_head_id');
				$business_head_number = DB::table('user')->select('contact_no')->where('id',$business_head_id)->get()->first();
				$business_head_number = $business_head_number->contact_no;
				array_push($numbers,$business_head_number);
				$accounts_id = config('app.accounts_id');
				$accounts_contact_no = DB::table('user')->select('contact_no')->where('id',$accounts_id)->get()->first();
				$accounts_contact_no = $accounts_contact_no->contact_no;
				array_push($numbers,$accounts_contact_no);
			}
			else{
				$business_head_id = config('app.business_head_id');
				$business_head_number = DB::table('user')->select('contact_no')->where('id',$business_head_id)->get()->first();
				$business_head_number = $business_head_number->contact_no;
				$assigned_number = DB::table('delusers')->select('contact_no')->where('username',$del_assigned_to)->get()->first();
				$assigned_number = $assigned_number->contact_no;
				array_push($numbers,$business_head_number);
				array_push($numbers,$assigned_number);
				$accounts_id = config('app.accounts_id');
				$accounts_contact_no = DB::table('user')->select('contact_no')->where('id',$accounts_id)->get()->first();
				$accounts_contact_no = $accounts_contact_no->contact_no;
				array_push($numbers,$accounts_contact_no);
				if($helpers_temp != "No")
				{
					$helpers_number = DB::table('delusers')->select('contact_no')->whereIn('username',json_decode($helpers))->get()->toArray();
					foreach($helpers_number as $key=>$value)
					{
						array_push($numbers,$value->contact_no);
					}
				}

				// dd($numbers);
			}

			$order_all_details = DB::table('del_orders')
								->join('pickups','pickups.pickup_order_id','=','del_orders.order_id')
								->join('order_details','order_details.id','=','pickups.order_details_id')
								->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
								->join('products','products.id','pickups.product_id')
								->select('del_orders.*','pickups.*','products.product_name','customer_details.customer_type')
								->where('del_orders.order_id',$pickup_order_id)
								->get()
								->toArray();
			$customer_name = $order_all_details[0]->shipping_first_name;
			$mobile_no = $order_all_details[0]->mobileno;
			$address = $order_all_details[0]->fulldetails;
			// $payment_mode = $order_all_details[0]->PaymentMode;
			$amount = $order_all_details[0]->TotalAmt;
			if($order_all_details[0]->customer_type == 'Corporate')
			{
				$amount = 0;
			}		
			$product_names = array();
			$total_qty = 0;
			foreach ($order_all_details as $key=>$value)
			{
				$total_qty++;
				array_push($product_names,$value->product_name);
			}
			// dd($numbers);
			$product_names = implode(',',$product_names);
			// dd($product_names);
			
			foreach ($numbers as $key => $value) {

				if(config('app.app_env') == 'devweb')
				{
					$value = config('app.developer_contact');
				}
				
				$url = "https://lj7724mli5.execute-api.ap-south-1.amazonaws.com/prod/sendwhatsappmsg";
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, $url);
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				
				$headers = array(
				"Accept: application/json",
				"Content-Type: application/json",
				);
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
				$data =[
					"portno"=> "11140",
					"namespace"=> "b9a23cb4_89ed_4fe2_b849_20775908ff5e",
					"countrycode"=> "91",
					"mobileno" => "$value",
					"templatename" => "order_assignment_delboy",
					"templateparams" => [ 
						["type"=> "text","text"=> "Pick Up *Order Id: $pickup_order_id*"],
						["type"=> "text","text"=> "$customer_name"],
						["type"=> "text","text"=> "$mobile_no"],
						["type"=> "text","text"=> "$address"],
						["type"=> "text","text"=> "$product_names"],
						["type"=> "text","text"=> "$total_qty"],
						["type"=> "text","text"=> "$amount"],
						["type"=> "text","text"=> "$payment_mode"],
				],
			];
			//dd($data);
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
			
			$resp = curl_exec($curl);
			curl_close($curl);
			//    print_r($resp);
			
				// dd($resp);
			}
            
        }
		$curl = curl_init();
		if(config('app.app_env') == 'devweb')
		{
			$mobileno = config('app.developer_contact');
		}
		curl_setopt_array($curl, array(
		CURLOPT_URL => "https://api.msg91.com/api/v5/flow/",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		//CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"60cc49120eeed16fcd62d103\",\n  \"sender\": \"QUALCR\",\n  \"mobiles\": \"919920361040\",\n  \"name\": \"testing\",\n  \"orderno\": \"8512457845\",\n  \"equpname\": \"Standard Walker\",\n  \"date\": \"18-06-2021\",\n  \"amount\": \"550\"}",
		CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"60d1b14773d364743d1eec37\",\n  \"sender\": \"qualcr\",\n  \"mobiles\": \"91$mobileno\",\n  \"customer_name\": \"$name\",\n  \"order_no\": \"$pickup_order_id\",\n  \"equipment\": \"$prod_name...\",\n  \"date\": \"$del_date\",\n  \"amount\": \"amount\"}",
		CURLOPT_HTTPHEADER => array(
			"authkey: 267641AmFwcnWjDS5e6b4757P1",
			"content-type: application/JSON"
		),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		echo "cURL Error #:" . $err;
		} else {
		echo $response;
		}
		$logDate = Carbon::now()->toDateString();
		$logTime = Carbon::now()->toTimeString();
		DB::table('leads_log')->insert([
			'log_order_id'=>$pickup_order_id,
			'log_lead_status'=>'Order Assigned',
			'log_order_type'=>'PO',
			'log_date'=>$logDate,
			'log_time'=>$logTime,
			'updated_by'=>session('username')
		]);
		
        return redirect('/pickup_request')->with('message','Pickup Order Generated sucessfully');
    }

	//Modify Pickup Request
	public function ModifyPickup(Request $request,$order_id)
	{
		$isLoggedIn = $this->isLoggedIn();
		if($isLoggedIn == 'false')
		{
			$url = url('/');
			return redirect()->to($url);
		}
		if($_SERVER['REQUEST_METHOD']=='GET')
		{
			$order_details = DB::select("SELECT * FROM del_orders WHERE order_id = $order_id ");
			$data['order_details'] = json_decode(json_encode($order_details),true);
			//print_r($data['order_details']);
			// $delboys = DB::select("SELECT *FROM delusers WHERE role='user'");
			$delboys = DB::select("SELECT * FROM delusers WHERE role='user' AND status='Active'");
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
											pickups.drop_vendor_product_id as pickup_vendor_product_id,
											pickups.cash_amount as cash_amount,
											vendor_details.registered_name,
											vendor_warehouse.wh_name,
											vendor_warehouse.wh_area,
											vendor_warehouse.wh_city
										FROM
											pickups,products,order_details,vendor_details,vendor_warehouse
										Where
											pickups.pickup_order_id = '$order_id'
											AND order_details.id = pickups.order_details_id
											AND order_details.product_id = products.id
											AND order_details.vendor_id = vendor_details.id
											AND order_details.vendor_warehouse_id = vendor_warehouse.id");
			$data['product_details'] = json_decode(json_encode($product_details),true);
	
			$total_rent = 0;
			$total_deposit = 0;
			$vendor_details=array();
			foreach($data['product_details'] as $key=>$p_details)
			{
				if(DB::table('cr_dr_note')->where('order_id',$p_details['order_id'])->exists())
				{
					$data['product_details'][$key]['product_rent'] = RenewalPickupController::fetchCrDrData($p_details['order_details_id'],'R');
					$data['product_details'][$key]['product_deposite'] = RenewalPickupController::fetchCrDrData($p_details['order_details_id'],'D');
					$data['product_details'][$key]['transport'] = RenewalPickupController::fetchCrDrData($p_details['order_details_id'],'T');
				}
				$total_rent += $p_details['cash_amount'];
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
				

				$get_warehouse_details = DB::table('vendor_warehouse')->where('vendor_id',$get_vendor_id)->get()->toArray();
				$data['product_details'][$i]['vendor_warehouse_details'] = $get_warehouse_details;
				$get_warehouse_details = DB::table('vendor_warehouse')->where('vendor_id',17)->whereIn('id',[19,217])->get()->toArray();
				$data['product_details'][$i]['q5c_warehouse_details'] = $get_warehouse_details;	
				$customer_address = array();
				// foreach ($get_customer_details as $key=>$value)
				// {
				// 	$products = json_decode($value->equipment_requirement);
				// 	if(in_array($value1->product_id,$products))
				// 	{
				// 		$count = count($customer_address);
				// 		$customer_address[$count] = $value;
				// 	}
				// }
				$data['product_details'][$i]['customer_address'] = $customer_address;
				// if(DB::table('adjustment_table')->where('adjusted_order_details_id',$value1->id)->where('adjustment_table.flag','A')->exists())
				// {
				// 	// dd("Exists");
				// 	$records = DB::table('adjustment_table')->select('adjusted_amount')->where('adjusted_order_details_id',$value1->id)->where('fromtype','D')->where('adjustment_table.flag','A')->get();
				// 	// dd($records);
				// 	$sum = $records->pluck('adjusted_amount')->sum();
				// 	// dd($sum);
				// 	// dd($product->product_deposite);
				// 	// $productData[$key]->adjusted_deposit = $sum;
				// 	// $totalAdjDeposit = $totalAdjDeposit + $sum;
				// 	$data['product_details'][$i]['product_deposite'] = $data['product_details'][$i]['product_deposite'] - $sum;
				// 	// dd($totalDeposit);
				// }
			}
			return view('/DeliveryManagement/ModifyPickup',$data);
		}

		if($_SERVER['REQUEST_METHOD']=='POST')
		{
			//print_r($_POST);
			$pickup_order_id = $_POST['pickup_order_id'];
			$order_id = $request->get('pickup_order_id');
			$oldOrderData = DB::table('del_orders')->where('order_id',$pickup_order_id)->first();
			$delDate = date('d-m-Y',strtotime($oldOrderData->DelDate));
			$delDate = date('Y-m-d',strtotime($delDate));
			if(date('Y-m-d',strtotime($delDate))!=$request->get('del_date')){
				$customer_name = $oldOrderData->shipping_first_name;
				$accountsEmail = config('app.accounts_email');
				//$accountsEmail = 'viveks@quali55care.com';
				$orderType = 'Pickup';
				$modifiedType = 'Date';
				$changedDate = ['from'=>$delDate,'to'=>$request->get('del_date')];
				$modifiedBy = session('username');
				$changed_date = $request->get('del_date');
				DB::table('vendor_rented_inventory')->where('order_id',$order_id)->where('type','Pickup')->where('flag','Active')->update(['rented_date'=>$delDate,'due_date'=>$delDate]);
				// Mail::send('OrderModifyEmail/order-modifyEmail',compact('request','order_id','customer_name','orderType','modifiedType','modifiedBy','changedDate'), function($message) use($accountsEmail,$order_id)
				// {  
				// 	$message->to($accountsEmail, 'Accounts')->subject('Order Modified - '.$order_id);
				// 	$message->from('tempmailquali@gmail.com', 'Quali55Care');
				// });
				// $accounts_nos = config('app.accounts_staff_contacts');
				// foreach($accounts_nos as $key=>$value)
				// {
				// 	$url = "https://lj7724mli5.execute-api.ap-south-1.amazonaws.com/prod/sendwhatsappmsg";
				// 	$curl = curl_init();
				// 	if(config('app.app_env') == 'devweb')
				// 	{
				// 		$value = config('app.developer_contact');
				// 	}
				// 	curl_setopt($curl, CURLOPT_URL, $url);
				// 	curl_setopt($curl, CURLOPT_POST, true);
				// 	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
					
				// 	$headers = array(
				// 		"Accept: application/json",
				// 		"Content-Type: application/json",
				// 	);
				// 	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
				// 	$data =[
				// 		"portno"=>"11140",
				// 		"namespace"=>"b9a23cb4_89ed_4fe2_b849_20775908ff5e",
				// 		"countrycode"=> "91",
				// 		"mobileno"=> $value,
				// 		"templatename" => "change_order_datemodified",
				// 		"templateparams" => [
				// 			["type"=> "text","text"=> $order_id],
				// 			["type"=> "text","text"=> $orderType],
				// 			["type"=> "text","text"=> $customer_name],
				// 			["type"=> "text","text"=> $delDate],
				// 			["type"=> "text","text"=> $changed_date],
				// 			["type"=> "text","text"=> $modifiedBy]
				// 		],
				// 	];
				// 	curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
					
				// 	$resp = curl_exec($curl);
				// 	curl_close($curl);
				// }
			}
			
			$order_details_id = $_POST['order_details_id'];
			$del_date=date('d-m-Y',strtotime($_POST['del_date']));
			leads_log::updateOrCreate(
				[
				   'log_order_id' => $order_id,
				   'log_lead_status' => 'Order Generated',
				   'updated_by' => session('username')
				],
				[
				   'log_order_lead_date' => date('Y-m-d',strtotime($del_date)).' '.date('H:i:s'),
				   'log_date' => date('Y-m-d'),
				   'log_time' => date('H:i:s'),
				]);
	  		$pickup_date = date('Y-m-d',strtotime($del_date));
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
			$warehouse_type = $request->get('group_name');
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
				$old_pickup_order = DB::table('del_orders')
					->select(
						'status',
						'helpers',
						'deliverypickup',
						'UpdatedBy',
						'DelDate',
						'Pickup_Date',
						'DelAssignedTo',
						'ReceiptToBeCarried',
						'PaymentMode',
						'cash',
						'online',
						'TravelMode',
						// 'created_at',
						'order_approval_status')
					->where('order_id', $order_id)->get();

				$update_pickup_data = [
					'status' => $del_status,
					'helpers' => $helpers,
					'deliverypickup' => $delivery_type,
					'UpdatedBy' => $UpdatedBy,
					'DelDate' => $del_date,
					'Pickup_Date' => $pickup_date,
					'DelAssignedTo' => $del_assigned_to,
					'ReceiptToBeCarried' => $invoice_type,
					'PaymentMode' => $payment_mode,
					'cash' => $cash_amount,
					'online' => $online_amount,
					'TravelMode' => $travel,
					// 'created_at'=>date('Y-m-d',strtotime($del_date)).' '.date('H:i:s'),
					'order_approval_status' => 'Approved',
				 ];
				 //print_r($update_data);
				$del_order = new DelOrders();     
				$Vendor_Products = new VendorProducts();
				$Pickup = new Pickup();
				$del_order->where('order_id', $pickup_order_id)->update($update_pickup_data);

				foreach ($update_pickup_data as $key => $value)
				{
					if($value != $old_pickup_order[0]->$key)
					{
						$insertData = [
							'order_type'=>'PO',
							'key_id'=>$pickup_order_id,
							'operation'=>'Update Pickup Order',
							'fields'=>$key,
							'old_value'=>$old_pickup_order[0]->$key,
							'new_value'=>$value,
							'updated_by'=>session('username')
							];
							ActivityLog::insert($insertData);
					}
				}
				$assignArr = [
					'assign_at'=>Carbon::now()->toDateTimeString(),
					'assign_by'=>session('username'),
				];
				foreach ($assignArr as $key => $assignData) 
				{
					$assignInsert = [
						'order_type'=>'PO',
						'key_id'=>$pickup_order_id,
						'operation'=>'Order Modified',
						'fields'=>$key,
						'old_value'=>null,
						'new_value'=>$assignData,
						'updated_by'=>session('username'),
					];
					ActivityLog::insert($assignInsert);
				}

				$del_date=date('Y-m-d',strtotime($_POST['del_date']));
				$update_pickup_details = [
					'pickup_date'=>$del_date,
				];
				$old_pickup_order_details = DB::table('pickups')->select('pickup_date')->where('pickup_order_id',$pickup_order_id)->get();
				if($old_pickup_order_details[0]->pickup_date != $update_pickup_details['pickup_date'])
				{
					$insertData = [
						'order_type'=>'PO',
						'key_id'=>$pickup_order_id,
						'operation'=>'Update Pickup Order Details',
						'fields'=>'pickup_date',
						'old_value'=>$old_pickup_order_details[0]->pickup_date,
						'new_value'=>$update_pickup_details['pickup_date'],
						'updated_by'=>session('username')
						];
						ActivityLog::insert($insertData);
				}
				$Pickup->where('pickup_order_id', $pickup_order_id)->update($update_pickup_details);
				// $Pickup = DB::update("UPDATE pickups SET pickup_date=$del_date WHERE pickup_order_id=$order_id");
	
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
					$get_warehouse_type = $warehouse_type[$i];
					$batch_id = date('Y-m-d')." - ".$get_product_id;
					// dd($get_warehouse_id,$pickup_warehouse_id[$i]);
					$temp_vdr_warehouse_type = 0;
					$temp_vdr_prod_details_warehouse_type = 0;
					if($get_warehouse_type =='Virtual Warehouse')
					{
						$drop_location = 'Q5C';
						$temp_vdr_warehouse_type = 0;
						$temp_vdr_prod_details_warehouse_type = 1;
					}
					elseif($get_warehouse_type =='Vendor Warehouse')
					{
						$temp_vdr_warehouse_type = 1;
						$temp_vdr_prod_details_warehouse_type = 2;
						// $update_rented_prod = VendorRentedProducts::where('id',$get_vendor_rented_prod_id)->update(['status'=>'Released']);
					}
					if($temp_vdr_warehouse_type == 1){
						EditOrderController::updateVendorInOutInventory($get_pickup_id,'out');
					}else{
						DB::table('vendor_inventory_mgmt')->where('details_id',$get_pickup_id)->where('state','out')->update(['flag'=>'Inactive']);
					}
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
							$drop_location = 'Vendor';
							$vdr_warehouse_type = 0;
							$vdr_prod_details_warehouse_type = 0;
							if($get_warehouse_type =='Virtual Warehouse')
							{
								$drop_location = 'Q5C';
								$vdr_warehouse_type = 0;
								$vdr_prod_details_warehouse_type = 1;
							}
							elseif($get_warehouse_type =='Vendor Warehouse')
							{
								$vdr_warehouse_type = 1;
								$vdr_prod_details_warehouse_type = 2;
								// $update_rented_prod = VendorRentedProducts::where('id',$get_vendor_rented_prod_id)->update(['status'=>'Released']);
							}
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
							// dd($vdr_warehouse_type);
							// if($vdr_warehouse_type == 1){
							// 	EditOrderController::updateVendorInOutInventory($get_pickup_id,'out');
							// }else{
							// 	DB::table('vendor_inventory_mgmt')->where('details_id',$get_pickup_id)->where('state','out')->update(['flag'=>'Inactive']);
							// }
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
								// 'created_at'=>date('Y-m-d H:i:s')
							];	
							$get_vendor_product_id = $Vendor_Products->insertGetId($insert_record);
							$drop_location = 'Vendor';
							$vdr_warehouse_type = 0;
							$vdr_prod_details_warehouse_type = 0;
							if($get_warehouse_type =='Virtual Warehouse')
							{
								$drop_location = 'Q5C';
								$vdr_warehouse_type = 0;
								$vdr_prod_details_warehouse_type = 1;
							}
							elseif($get_warehouse_type =='Vendor Warehouse')
							{
								$vdr_warehouse_type = 1;
								$vdr_prod_details_warehouse_type = 2;
								// $update_rented_prod = VendorRentedProducts::where('id',$get_vendor_rented_prod_id)->update(['status'=>'Released']);
							}
							//update in pickups table
							$update_pickups = [
								'drop_vendor_id' =>$get_vendor_id,
								'drop_warehouse_id'=>$get_warehouse_id,
								'drop_vendor_product_id'=>$get_vendor_product_id,
								'updated_at' =>date('Y-m-d H:i:s')
							];
							$Pickup->where('id',$get_pickup_id)->update($update_pickups);
							// dd($vdr_warehouse_type);


							// if($vdr_warehouse_type == 1){
							// 	EditOrderController::updateVendorInOutInventory($get_pickup_id,'out');
							// }else{
							// 	DB::table('vendor_inventory_mgmt')->where('details_id',$get_pickup_id)->where('state','out')->update(['flag'=>'Inactive']);
							// }
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
		$isLoggedIn = $this->isLoggedIn();
		if($isLoggedIn == 'false')
		{
			$url = url('/');
			return redirect()->to($url);
		}
		if($_SERVER['REQUEST_METHOD']=='GET')
		{
			$today = date('d-m-Y');
			$past_three_days = date('d-m-Y',strtotime("-3 days"));

			$data['start_date'] = date('Y-m-d',strtotime("-3 days"));
			$data['end_date'] = date('Y-m-d');

			// $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$past_three_days','%d-%m-%Y') AND STR_TO_DATE('$today','%d-%m-%Y')";
			// $collection_request = DB::select("SELECT 
			// 										DISTINCT('renewals.collection_order_id'),
			// 										del_orders.*,
			// 										renewals.created_at as created_at
			// 									FROM del_orders,renewals 
			// 									where 
			// 										del_orders.order_id = renewals.collection_order_id
			// 										AND del_orders.deliverypickup='Collection' 
			// 										AND $whereClause 
			// 										AND (del_orders.status='Pending' OR del_orders.status='Assigned') 
			// 										AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)
			// 										AND del_orders.PaymentMode='Cash' ORDER BY STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') DESC ");

			// DB::enableQueryLog();

			$collection_request = DB::table('renewals')
										->join('del_orders','del_orders.order_id','=','renewals.collection_order_id')
										->join('order_details','order_details.id','=','renewals.order_details_id')
										->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
										->distinct('renewals.collection_order_id')
										->select('del_orders.*','renewals.created_at as created_at')
										->where('del_orders.deliverypickup','Collection')
										->whereNotIn('del_orders.PaymentMode',['Online'])
										->whereNotIn('del_orders.status',['Cancel'])
										->whereBetween(DB::raw("(STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$past_three_days','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$today','%d-%m-%Y'))")])
										->when(session('city_based_access'),function($query){
											$query->where('customer_details.citygroup',session('user_city'));
										})
										// ->orderBy(DB::raw("(STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y'))",'DESC'))
										->orderBy('del_orders.order_id','DESC')
										->get();


// 										$collection_request = DB::getQueryLog();
// print_r(end($collection_request));
// die;

			$data['collection_request'] = json_decode(json_encode($collection_request),true);
			echo "<script>localStorage['filtered']='past_3_days';</script>";
			return view('/DeliveryManagement/RenewRequest',$data);
		}
	}
	public function filterCollectionOrder($filter_by)
	{
		$isLoggedIn = $this->isLoggedIn();
		if($isLoggedIn == 'false')
		{
			$url = url('/');
			return redirect()->to($url);
		}
			if($filter_by =='today')
			{
				$date = date('d-m-Y');
			$data['start_date'] = date('Y-m-d');
			$data['end_date'] = date('Y-m-d');
			// $whereClause = "DelDate = '$date'";
			$whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') = STR_TO_DATE('$date','%d-%m-%Y')";
			echo "<script>localStorage['filtered']='today';</script>";
		}
		elseif($filter_by =='yesterday')
		{
			$prevDate = date('d-m-Y',strtotime("-1 days"));

			$data['start_date'] = date('Y-m-d',strtotime("-1 days"));
			$data['end_date'] = date('Y-m-d',strtotime("-1 days"));
			// $whereClause = "DelDate = '$prevDate'";
			$whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') = STR_TO_DATE('$prevDate','%d-%m-%Y')";
			echo "<script>localStorage['filtered']='yesterday';</script>";
		}
		elseif($filter_by =='past_3_days')
		{
			$today = date('d-m-Y');
			$past_three_days = date('d-m-Y',strtotime("-2 days"));
			$data['start_date'] = date('Y-m-d',strtotime("-2 days"));
			$data['end_date'] = date('Y-m-d');
			// $whereClause = "DelDate >= '$past_three_days'";
			$whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$past_three_days','%d-%m-%Y') AND STR_TO_DATE('$today','%d-%m-%Y')";
			echo "<script>localStorage['filtered']='past_3_days';</script>";
		}
		 elseif($filter_by =='week')
		{
			$today = date('d-m-Y');
			$past_three_days = date('d-m-Y',strtotime("-7 days"));
			$data['start_date'] = date('Y-m-d',strtotime("-7 days"));
			$data['end_date'] = date('Y-m-d');
			// $whereClause = "DelDate >= '$past_three_days'";
			$whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$past_three_days','%d-%m-%Y') AND STR_TO_DATE('$today','%d-%m-%Y')";
			echo "<script>localStorage['filtered']='week';</script>";
		}
		elseif($filter_by =='month')
		{
			$month = date('m-Y');
			$start_date_temp = '01-'.$month;
			$start_date = date('d-m-Y',strtotime($start_date_temp));
			$end_date_temp = '31-'.$month;
			$end_date = date('d-m-Y',strtotime($end_date_temp));
			$data['start_date'] = date('Y-m-d',strtotime($start_date_temp));
			$data['end_date'] = date('Y-m-d',strtotime($end_date_temp));
			$whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y')";
		}
		elseif($filter_by == 'all')
		{
			// $orderTypeNotIn = config('app.order_type');
			// $orderTypeNotIn = "'".implode("','",$orderTypeNotIn)."'";
			// $collection_request = DB::select("SELECT 
			// 										DISTINCT('renewals.collection_order_id'),
			// 										del_orders.*,
			// 										renewals.created_at as created_at
			// 									FROM del_orders,renewals
			// 									where 
			// 										 del_orders.deliverypickup='Collection'
			// 										 AND renewals.collection_order_id = del_orders.order_id
			// 										 AND (del_orders.status!='Collected')
			// 										 AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)
			// 										AND del_orders.PaymentMode='Cash' ORDER BY STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') DESC ");
        	// $data['collection_request'] = json_decode(json_encode($collection_request),true);
			echo "<script>localStorage['filtered']='all';</script>";
			//return view('/DeliveryManagement/RenewRequest',$data);
		}
	   
		// $collection_request = DB::select("SELECT 
		// 									DISTINCT('renewals.collection_order_id'),
		// 										del_orders.*,
		// 										renewals.created_at as created_at
		// 									FROM del_orders,renewals
		// 									where 
		// 										 del_orders.deliverypickup='Collection' 
		// 										 AND renewals.collection_order_id = del_orders.order_id
		// 										AND $whereClause 
		// 										AND (del_orders.status!='Picked up')
		// 										AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)
		// 										AND del_orders.PaymentMode='Cash' ORDER BY STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') DESC ");
		$collection_request = DB::table('renewals')
								->join('del_orders','del_orders.order_id','=','renewals.collection_order_id')
								->join('order_details','order_details.id','=','renewals.order_details_id')
								->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
								->distinct('renewals.collection_order_id')
								->select('del_orders.*','renewals.created_at as created_at')
								->where('del_orders.order_approval_status','Approved')
								// ->where('del_orders.DelAssignedTo','Pending')
								->where('del_orders.deliverypickup','Collection')
								->whereNotIn('del_orders.status',['Cancel'])

								->when($filter_by == 'today',function($query){
									$query->where('del_orders.DelDate',date('d-m-Y'));
								})
								->when($filter_by == 'yesterday',function($query){
									$query->where('del_orders.DelDate',date('d-m-Y',strtotime("-1 days")));
								})
								->when($filter_by == 'past_3_days',function($query){
									$start_date = date('d-m-Y',strtotime("-2 days"));
									$end_date = date('d-m-Y');
									$query->whereBetween(DB::raw("(STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$start_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$end_date','%d-%m-%Y'))")]);
								})
								->when($filter_by == 'week',function($query){
									$start_date = date('d-m-Y',strtotime("-7 days"));
									$end_date = date('d-m-Y');
									$query->whereBetween(DB::raw("(STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$start_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$end_date','%d-%m-%Y'))")]);
								})
								->when($filter_by == 'month',function($query){
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
								->get();
        $data['collection_request'] = json_decode(json_encode($collection_request),true);
		
		return view('/DeliveryManagement/RenewRequest',$data);
   	}
   	public function filterCollectionOrderDWS()
   	{
		$start_date = date('d-m-Y',strtotime($_POST['start_date']));
		$end_date = date('d-m-Y',strtotime($_POST['end_date']));

		$data['start_date'] = date('Y-m-d',strtotime($_POST['start_date']));
		$data['end_date'] = date('Y-m-d',strtotime($_POST['end_date']));
		// $whereClause = "STR_TO_DATE(DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y')";

		$orderTypeNotIn = config('app.order_type');
		$orderTypeNotIn = "'".implode("','",$orderTypeNotIn)."'";
		
		$collection_request = DB::table('renewals')
								->join('del_orders','del_orders.order_id','=','renewals.collection_order_id')
								->join('order_details','order_details.id','=','renewals.order_details_id')
								->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
								->distinct('renewals.collection_order_id')
								->select('del_orders.*','renewals.created_at as created_at')
								->where('del_orders.order_approval_status','Approved')
								// ->where('del_orders.DelAssignedTo','Pending')
								->where('del_orders.deliverypickup','Collection')
								->whereNotIn('del_orders.status',['Cancel'])

								->when($start_date,function($query)use($start_date,$end_date){
									$query->whereBetween(DB::raw("(STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$start_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$end_date','%d-%m-%Y'))")]);
								})
								->when(session('city_based_access') == '1',function($query){
									$query->where('customer_details.citygroup',session('user_city'));
								})
								->get();
		// $collection_request = DB::select("SELECT * FROM del_orders where deliverypickup='Collection' AND $whereClause AND (status='Pending' OR status='Assigned') AND PaymentMode='Cash' AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) ORDER BY STR_TO_DATE(DelDate,'%d-%m-%Y') DESC ");
        $data['collection_request'] = json_decode(json_encode($collection_request),true);
		
		return view('/DeliveryManagement/RenewRequest',$data);
   	}


   public function send_del_reminder($customer_id,$order_id)
   {
	$isLoggedIn = $this->isLoggedIn();
	if($isLoggedIn == 'false')
	{
		$url = url('/');
		return redirect()->to($url);
	}
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
	   $isLoggedIn = $this->isLoggedIn();
	   if($isLoggedIn == 'false')
	   {
		   $url = url('/');
		   return redirect()->to($url);
	   } 
	   $order_details = DB::select("SELECT * FROM del_orders WHERE order_id = $order_id ");
	   $data['order_details'] = json_decode(json_encode($order_details),true);
	   //print_r($data['order_details']);
	//    $delboys = DB::select("SELECT *FROM delusers WHERE role='user'");
	   $delboys = DB::select("SELECT * FROM delusers WHERE role='user' AND status='Active'");
	   $data['delboys'] = json_decode(json_encode($delboys), true);
	   $products = DB::select("SELECT * FROM products");
	   $data['products'] = \json_decode(\json_encode($products), true);
	   $product_details = DB::select("SELECT 
									   order_details.*,
									   products.product_name as product_name,
									   renewals.id as renewal_main_id,
									   renewals.order_details_id as order_details_id,
									   renewals.*
								   FROM
									   renewals,products,order_details
								   Where
									   renewals.collection_order_id = '$order_id'
									   AND order_details.id = renewals.order_details_id
									   AND renewals.status NOT IN ('Cancel')
									   AND order_details.product_id = products.id");
	   $data['product_details'] = json_decode(json_encode($product_details),true);
	   $total_rent = 0;
	   $total_deposit = 0;
	   $vendor_details=array();
		foreach($data['product_details'] as $key=>$p_details)
	   {
			if(DB::table('cr_dr_note')->where('order_id',$p_details['order_id'])->exists())
			{
				$data['product_details'][$key]['product_rent'] = RenewalPickupController::fetchCrDrData($p_details['order_details_id'],'R');
				$data['product_details'][$key]['product_deposite'] = RenewalPickupController::fetchCrDrData($p_details['order_details_id'],'D');
			}
		   if($p_details['discount_amt']!=null){
			   $discount = $p_details['discount_amt'];
		   }else{
			   $discount =0;
		   }
		   $total_rent += $p_details['product_rent']-$discount;
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
		$del_status='';
		$delivery_type=$_POST['delivery_type'];
		$del_assigned_to='';
		$invoice_type=$_POST['invoice_type'];
		$travel=$_POST['travel'];
		$helpers='';
		// $start_date = $_POST['start_date'];
		// $end_date = $_POST['end_date'];
		// $helpers = json_encode($helpers);
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
			if(isset($_POST['self_pick']))
			{
				$del_assigned_to = 'Customer';
				$helpers = "[No helper]";
				$helpers_temp = "No";
				$del_status="Collected";
				$renewals = DB::table('renewals')->select('order_details_id')->where('collection_order_id',$collection_order_id)->get()->toArray();
				foreach($renewals as $key=>$value)
				{
					// DB::enableQueryLog();
					OrderDetails::where('id',$value->order_details_id)->update(['current_status'=>'Renewed','pickup_date'=>DB::raw('DATE_ADD(pickup_date, INTERVAL 1 MONTH)')]);
					// dd(DB::getQueryLog());
				}
			}
			else
			{
				$del_assigned_to=$_POST['del_assigned_to'];
				$del_status=$_POST['del_status'];
				if($_POST['helpers'][0] != "No Helper")
				{
					$helpers=$_POST['helpers'];
					$helpers = json_encode($helpers, true);
					$helpers_temp = "Yes";
				}
				else
				{
					$helpers = "[No helper]";
					$helpers_temp = "No";
				}
			}
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
				'reminder_state'=>0
			 ];       
			 //print_r($update_data);
			$Renew = new Renewal();
			$DelOrder = new DelOrders();
			$Order_Details = new OrderDetails();  
			//for activity log
			$getOldData = DB::table('del_orders')->where('order_id',$collection_order_id)->first(array_keys($update_collection_data));//before order id

			$DelOrder->where('order_id', $collection_order_id)->update($update_collection_data);//updated

			foreach ($update_collection_data as $key => $upData) 
			{
				$insertData = [
					'order_type'=>'CO',
					'key_id'=>$collection_order_id,
					'operation'=>'Order Assigned',
					'fields'=>$key,
					'old_value'=>$getOldData->$key,
					'new_value'=>$upData,
					'updated_by'=>session('username')
				];
				ActivityLog::insert($insertData);
			}
			$assignArr = [
				'assign_at'=>Carbon::now()->toDateTimeString(),
				'assign_by'=>session('username'),
			];
			foreach ($assignArr as $key => $assignData) 
			{
				$assignInsert = [
					'order_type'=>'CO',
					'key_id'=>$collection_order_id,
					'operation'=>'Order Assigned',
					'fields'=>$key,
					'old_value'=>null,
					'new_value'=>$assignData,
					'updated_by'=>session('username'),
					
				];
				ActivityLog::insert($assignInsert);
			}

			//update in order_details table and renewals table data
			for ($i=0; $i <count($order_details_id); $i++) 
			{ 
				// $update_order_details_data = [
				// 	'collection_date' =>$today,
				// 	'pickup_date'=>$end_date[$i],
				// 	'current_status' =>'Renewed'
				// ];
				// $Order_Details->where('id',$order_details_id[$i])->update($update_order_details_data);
				$update_renewals_data = [
					'status' =>'DelBoy Assigned',
					'payment_status' =>'Pending'
				];
				$Renew->where('id',$renewal_main_id[$i])->update($update_renewals_data);
			}

			$numbers = array();
			if(isset($_POST['self_pick']))
			{
				$business_head_id = config('app.business_head_id');
				$business_head_number = DB::table('user')->select('contact_no')->where('id',$business_head_id)->get()->first();
				$business_head_number = $business_head_number->contact_no;
				array_push($numbers,$business_head_number);
				$accounts_id = config('app.accounts_id');
				$accounts_contact_no = DB::table('user')->select('contact_no')->where('id',$accounts_id)->get()->first();
				$accounts_contact_no = $accounts_contact_no->contact_no;
				array_push($numbers,$accounts_contact_no);
			}
			else{
				$business_head_id = config('app.business_head_id');
				$business_head_number = DB::table('user')->select('contact_no')->where('id',$business_head_id)->get()->first();
				$business_head_number = $business_head_number->contact_no;
				$assigned_number = DB::table('delusers')->select('contact_no')->where('username',$del_assigned_to)->get()->first();
				$assigned_number = $assigned_number->contact_no;
				array_push($numbers,$business_head_number);
				array_push($numbers,$assigned_number);
				$accounts_id = config('app.accounts_id');
				$accounts_contact_no = DB::table('user')->select('contact_no')->where('id',$accounts_id)->get()->first();
				$accounts_contact_no = $accounts_contact_no->contact_no;
				array_push($numbers,$accounts_contact_no);
				if($helpers_temp != "No")
				{
					$helpers_number = DB::table('delusers')->select('contact_no')->whereIn('username',json_decode($helpers))->get()->toArray();
					foreach($helpers_number as $key=>$value)
					{
						array_push($numbers,$value->contact_no);
					}
				}

				// dd($numbers);
			}

			$order_all_details = DB::table('del_orders')
								->join('renewals','renewals.collection_order_id','=','del_orders.order_id')
								->join('products','products.id','renewals.product_id')
								->join('order_details','order_details.id','=','renewals.order_details_id')
								->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
								->select('del_orders.*','renewals.*','products.product_name','customer_details.customer_type')
								->where('del_orders.order_id',$collection_order_id)
								->get()
								->toArray();
			$customer_name = $order_all_details[0]->shipping_first_name;
			$mobile_no = $order_all_details[0]->mobileno;
			$address = $order_all_details[0]->fulldetails;
			// $payment_mode = $order_all_details[0]->PaymentMode;
			$amount = $order_all_details[0]->TotalAmt;
			if($order_all_details[0]->customer_type == 'Corporate')
			{
				$amount = 0;
			}
			$product_names = array();
			$total_qty = 0;
			foreach ($order_all_details as $key=>$value)
			{
				$total_qty++;
				array_push($product_names,$value->product_name);
			}
			// dd($numbers);
			$product_names = implode(',',$product_names);
			// dd($product_names);
			
			foreach ($numbers as $key => $value) {

				$url = "https://lj7724mli5.execute-api.ap-south-1.amazonaws.com/prod/sendwhatsappmsg";
				$curl = curl_init();
				if(config('app.app_env') == 'devweb')
				{
					$value = config('app.developer_contact');
				}
				curl_setopt($curl, CURLOPT_URL, $url);
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				
				$headers = array(
				"Accept: application/json",
				"Content-Type: application/json",
				);
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
				$data =[
					"portno"=> "11140",
					"namespace"=> "b9a23cb4_89ed_4fe2_b849_20775908ff5e",
					"countrycode"=> "91",
					"mobileno" => "$value",
					"templatename" => "order_assignment_delboy",
					"templateparams" => [ 
						["type"=> "text","text"=> "Collection *Order Id: $collection_order_id*"],
						["type"=> "text","text"=> "$customer_name"],
						["type"=> "text","text"=> "$mobile_no"],
						["type"=> "text","text"=> "$address"],
						["type"=> "text","text"=> "$product_names"],
						["type"=> "text","text"=> "$total_qty"],
						["type"=> "text","text"=> "$amount"],
						["type"=> "text","text"=> "$payment_mode"],
				],
			];
			//dd($data);
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
			
			$resp = curl_exec($curl);
			curl_close($curl);
			//    print_r($resp);
			
				// dd($resp);
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
			$logDate = Carbon::now()->toDateString();
			$logTime = Carbon::now()->toTimeString();
			DB::table('leads_log')->insert([
				'log_order_id'=>$collection_order_id,
				'log_lead_status'=>'Order Assigned',
				'log_order_type'=>'CO',
				'log_date'=>$logDate,
				'log_time'=>$logTime,
				'updated_by'=>session('username')
			]);
			
		}
		return redirect('/renew_request')->with('message','Collection Order Generated sucessfully');
	}

	//Modify Collection Request
	public function ModifyCollection(Request $request,$order_id)
	{
		if($_SERVER['REQUEST_METHOD']=='GET')
		{
			$order_details = DB::select("SELECT * FROM del_orders WHERE order_id = $order_id ");
			$data['order_details'] = json_decode(json_encode($order_details),true);
			//print_r($data['order_details']);
			// $delboys = DB::select("SELECT *FROM delusers WHERE role='user'");
			$delboys = DB::select("SELECT * FROM delusers WHERE role='user' AND status='Active'");
			$data['delboys'] = json_decode(json_encode($delboys), true);
			$products = DB::select("SELECT * FROM products");
			$data['products'] = \json_decode(\json_encode($products), true);
			$product_details = DB::select("SELECT 
											order_details.*,
											products.product_name as product_name,
											renewals.order_details_id as order_details_id,
											renewals.id as renewal_main_id,
											renewals.*
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
			foreach($data['product_details'] as $key =>$p_details)
			{
				if(DB::table('cr_dr_note')->where('order_id',$p_details['order_id'])->exists())
				{
					$data['product_details'][$key]['product_rent'] = RenewalPickupController::fetchCrDrData($p_details['order_details_id'],'R');
					$data['product_details'][$key]['product_deposite'] = RenewalPickupController::fetchCrDrData($p_details['order_details_id'],'D');
					// $data['product_details'][$key]['product_rent'] = RenewalPickupController::fetchCrDrData($p_details['order_details_id'],'R');
					// $data['product_details'][$key]['product_deposite'] = RenewalPickupController::fetchCrDrData($p_details['order_details_id'],'D');
				}
				if($p_details['discount_amt']!=null){
					$discount = $p_details['discount_amt'];
				}else{
					$discount =0;
				}
				$total_rent += $p_details['product_rent']-$discount;
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
			$del_date=date('d-m-Y',strtotime($_POST['del_date']));
	  		$collection_date = date('Y-m-d',strtotime($del_date));
			//$lead_id = $_POST['lead_id'];
			$delivery_type=$_POST['delivery_type'];
			// $del_assigned_to=$_POST['del_assigned_to'];
			if(isset($_POST['self_pick']))
			{
				$del_assigned_to = 'Customer';
				$helpers = "[No helper]";
				$helpers_temp = "No";
				$del_status="Collected";
			}
			else
			{
				// $del_status=$_POST['del_status'];
				$del_assigned_to=$_POST['del_assigned_to'];
				$del_status=$_POST['del_status'];
				if($_POST['helpers'][0] != "No Helper")
				{
					$helpers=$_POST['helpers'];
					$helpers = json_encode($helpers, true);
					$helpers_temp = "Yes";
				}
				else
				{
					$helpers = "[No helper]";
					$helpers_temp = "No";
				}
			}
			$invoice_type=$_POST['invoice_type'];
			$travel=$_POST['travel'];
			// $helpers=$_POST['helpers'];
			// $helpers = json_encode($helpers);
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
					'DelDate'=>$del_date,
					'Collection_Date'=>$collection_date,
					'DelAssignedTo' => $del_assigned_to,
					'ReceiptToBeCarried' => $invoice_type,
					'PaymentMode' => $payment_mode,
					'cash' =>$cash_amount,
					'online'=>$online_amount,
					'TravelMode' => $travel,
					// 'created_at'=>date('Y-m-d',strtotime($del_date)).' '.date('H:i:s'),
					'order_approval_status'=>'Approved',
				 ];
				 leads_log::updateOrCreate(
					[
					   'log_order_id' => $collection_order_id,
					   'log_lead_status' => 'Order Generated',
					   'updated_by' => session('username')
					],
					[
					   'log_order_lead_date' => date('Y-m-d',strtotime($del_date)).' '.date('H:i:s'),
					   'log_date' => date('Y-m-d'),
					   'log_time' => date('H:i:s'),
					]);

				$old_collection_order_details = DB::table('del_orders')
					->select(
						'status',
						'helpers',
						'deliverypickup',
						'UpdatedBy',
						'DelDate',
						'Collection_Date',
						'DelAssignedTo',
						'ReceiptToBeCarried',
						'PaymentMode',
						'cash',
						'online',
						'TravelMode',
						// 'created_at',
						'order_approval_status'
					)
					->where('order_id',$collection_order_id)->get();
				$old_collection_order_details_ship = DB::table('del_orders')
					->select(
						'status',
						'helpers',
						'deliverypickup',
						'shipping_first_name',
						'UpdatedBy',
						'DelDate',
						'Collection_Date',
						'DelAssignedTo',
						'ReceiptToBeCarried',
						'PaymentMode',
						'cash',
						'online',
						'TravelMode',
						// 'created_at',
						'order_approval_status'
					)
					->where('order_id',$collection_order_id)->get();
				$delDate = $old_collection_order_details[0]->DelDate;
				if(date('Y-m-d',strtotime($delDate))!=$_POST['del_date']){
					$customer_name = $old_collection_order_details_ship[0]->shipping_first_name;
					$accountsEmail = config('app.accounts_email');
					//$accountsEmail = 'viveks@quali55care.com';
					$orderType = 'Collection';
					$modifiedType = 'Date';
					$changedDate = ['from'=>$delDate,'to'=>$_POST['del_date']];
					$modifiedBy = session('username');
					$changed_date = $_POST['del_date'];
					// Mail::send('OrderModifyEmail/order-modifyEmail',compact('request','collection_order_id','customer_name','orderType','modifiedType','modifiedBy','changedDate'), function($message) use($accountsEmail,$collection_order_id)
					// {  
					// 	$message->to($accountsEmail, 'Accounts')->subject('Order Modified - '.$collection_order_id);
					// 	$message->from('tempmailquali@gmail.com', 'Quali55Care');
					// });
					// $accounts_nos = config('app.accounts_staff_contacts');
					// foreach($accounts_nos as $key=>$value)
					// {
					// 	$url = "https://lj7724mli5.execute-api.ap-south-1.amazonaws.com/prod/sendwhatsappmsg";
					// 	$curl = curl_init();
					// 	if(config('app.app_env') == 'devweb')
					// 	{
					// 		$value = config('app.developer_contact');
					// 	}
					// 	curl_setopt($curl, CURLOPT_URL, $url);
					// 	curl_setopt($curl, CURLOPT_POST, true);
					// 	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
						
					// 	$headers = array(
					// 		"Accept: application/json",
					// 		"Content-Type: application/json",
					// 	);
					// 	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
					// 	$data =[
					// 		"portno"=>"11140",
					// 		"namespace"=>"b9a23cb4_89ed_4fe2_b849_20775908ff5e",
					// 		"countrycode"=> "91",
					// 		"mobileno"=> $value,
					// 		"templatename" => "change_order_datemodified",
					// 		"templateparams" => [
					// 			["type"=> "text","text"=> $order_id],
					// 			["type"=> "text","text"=> $orderType],
					// 			["type"=> "text","text"=> $customer_name],
					// 			["type"=> "text","text"=> $delDate],
					// 			["type"=> "text","text"=> $changed_date],
					// 			["type"=> "text","text"=> $modifiedBy]
					// 		],
					// 	];
					// 	curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
						
					// 	$resp = curl_exec($curl);
					// 	curl_close($curl);
					// }
				}
				foreach($update_collection_data as $key => $value)
				{
					if($value != $old_collection_order_details[0]->$key)
					{
						$insertData = [
							'order_type'=>'CO',
							'key_id'=>$collection_order_id,
							'operation'=>'Update Collection Order',
							'fields'=>$key,
							'old_value'=>$old_collection_order_details[0]->$key,
							'new_value'=>$value,
							'updated_by'=>session('username')
						];
						ActivityLog::insert($insertData);
					}
				}
				$assignArr = [
					'assign_at'=>Carbon::now()->toDateTimeString(),
					'assign_by'=>session('username'),
				];
				foreach ($assignArr as $key => $assignData) 
				{
					$assignInsert = [
						'order_type'=>'CO',
						'key_id'=>$collection_order_id,
						'operation'=>'Order Modified',
						'fields'=>$key,
						'old_value'=>null,
						'new_value'=>$assignData,
						'updated_by'=>session('username'),
						
					];
					ActivityLog::insert($assignInsert);
				}
				
				
				 //print_r($update_data);
				$Renew = new Renewal();
				$DelOrder = new DelOrders();
				$Order_Details = new OrderDetails();  
				$DelOrder->where('order_id', $collection_order_id)->update($update_collection_data);
				 //update in order_details table and renewals table data
				for ($i=0; $i <count($order_details_id); $i++) 
				{ 
					// $update_order_details_data = [
					// 	//'collection_date' =>$today,
					// 	'current_status' =>'Renewed'
					// ];
					// $Order_Details->where('id',$order_details_id[$i])->update($update_order_details_data);
					$update_renewals_data = [
						'status' =>'DelBoy Assigned',
						'payment_status' =>'Pending'
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

	public function delivery_upload_image(Request $request)
	{
		$order_id = $request->get('hidden_order_id');
		$filePath = "";
		if($_FILES['image_file']['name']!=null)
		{
			$image_file = $_FILES['image_file']['name'];
			//print_r($_FILES['shop_images']['name']);
			$targetDir = "assets/uploads/payment_images/";
			$fileName = basename($_FILES['image_file']['name']);
			$targetFilePath = $targetDir . $fileName;
			$fileType =strtolower(pathinfo($targetFilePath,PATHINFO_EXTENSION));
			$new_file_name = $targetDir."".$order_id.".".$fileType;
			move_uploaded_file($_FILES["image_file"]["tmp_name"], $new_file_name);    
			$filePath = $order_id.".".$fileType;
		}
		// dd($order_id);
		DelOrders::where('order_id',$order_id)->update(['payment_image'=>$filePath]);
		return redirect()->back();
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
			$past_three_days = date('d-m-Y',strtotime("-7 days"));
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
	public static function sendMailAlertUser($email,$user,$title,$message)
	{
		Mail::send('B2BCust/userMail',$message, function($mail) use ($email,$user,$title)
		{     
			$mail->to($email, $user)->subject($title);
			$mail->from('tempmailquali@gmail.com', 'Quali55Care');
		});
	}
	public static function sendMailAlertAdmin($email,$user,$title,$message)
	{
		Mail::send('B2BCust/adminMail',$message, function($mail) use ($email,$user,$title)
		{     
			$mail->to($email, $user)->subject($title);
			$mail->from('tempmailquali@gmail.com', 'Quali55Care');
		});
	}
}
