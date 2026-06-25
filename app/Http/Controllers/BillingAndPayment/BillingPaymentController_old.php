<?php

namespace App\Http\Controllers\BillingAndPayment;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\VendorRegister;
use App\Models\UserRegister;
use App\Models\DelOrders;
use App\Models\OrderDetails;
use App\Models\customer_detail;
use App\Models\ActivityLog;
use App\Models\Renewal;
use App\Models\Pickup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exports\BillingPendingPayments;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Models\leads_log;
use App\Exports\PendingPayment;
use PDF;
use Mail;
use Session;
use DateTime;
use App\Http\Controllers\RenewalPickup\RenewalPickupController;
use App\Http\Controllers\AppApiV2\OrdersController;


class BillingPaymentController_old extends Controller
{
    public function isLoggedIn()
    {
        $data = session('isLoggedIn');
        return $data;
    }

    public function collection_report()
    {
        if($_SERVER['REQUEST_METHOD'] == 'GET')
        {
            $start_date = date('d-m-Y',strtotime('01-01-2018'));
            $end_date = date('d-m-Y');
            $orderTypeNotIn = config('app.order_type');
            $orderTypeNotIn = "'".implode("','",$orderTypeNotIn)."'";
            $order_details = DB::select("SELECT * FROM del_orders Where del_orders.deliverypickup =  = 'Collection' AND del_orders.status = 'Collected' AND STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) ORDER BY order_id DESC");
            $data['order_details'] = json_decode(json_encode($order_details),true);
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
                }
                $i++;
            }
            // print_r($data['collection_report']);
            return view('BillingAndPayment/collection_report',$data);
        }
        elseif($_SERVER['REQUEST_METHOD'] == 'POST')
        {

        }
    }

    public function pending_online_renew(Request $request)
    {
        // if($_SERVER['REQUEST_METHOD']=='GET')
        // { 
        //     $today = date('Y-m-d');
		// 	$collection_request = DB::select("SELECT 
        //                                         DISTINCT('collection_order_id'),
        //                                         del_orders.*,
        //                                         renewals.created_at as created_at
        //                                     FROM
        //                                         del_orders,renewals
        //                                     where 
        //                                         del_orders.order_id = renewals.collection_order_id AND del_orders.deliverypickup='Collection' AND del_orders.status='Pending' AND del_orders.PaymentMode='Online' ORDER BY STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') DESC ");
		// 	$data['collection_request'] = json_decode(json_encode($collection_request),true);
        //     return view('BillingAndPayment/pending_online_renew',$data);
        // }
        $orderTypeNotIn = config('app.order_type');
        $getCollectionOrder = DB::table('del_orders')
                            ->select('del_orders.*')
                            ->join('renewals','del_orders.order_id','collection_order_id')
                                ->join('order_details','order_details.id','=','renewals.order_details_id')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                            ->where('del_orders.deliverypickup','Collection')
                            ->where('del_orders.status','Pending')
                            ->where('del_orders.PaymentMode','Online')
                                ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                            ->when($request->get('date_filter') && $request->get('date_filter')!='All',function($query) use($request){
                                $filterVal = $request->get('date_filter');
                                if($filterVal=='Today'){
                                    $date = Carbon::today()->toDateString();
                                    $date = date('d-m-Y',strtotime($date));
                                    $query->where('DelDate',$date);
                                }elseif($filterVal=='Yesterday'){
                                    $date = Carbon::yesterday()->toDateString();
                                    $date = date('d-m-Y',strtotime($date));
                                    $query->where('DelDate',$date);
                                }elseif($filterVal=='Tomorrow'){
                                    $date = Carbon::tomorrow()->toDateString();
                                    $date = date('d-m-Y',strtotime($date));
                                    $query->where('DelDate',$date);
                                }elseif($filterVal=='Past_3_Days'){
                                    $date = Carbon::today();
                                    $past3days = $date->subDays(3)->toDateString();
                                    $query->whereBetween('DelDate',[date('d-m-Y',strtotime($past3days)),date('d-m-Y',strtotime($date))]);
                                }elseif($filterVal=='Week'){
                                    $startWeek = Carbon::now()->startOfWeek()->toDateString();
                                    $endWeek = Carbon::now()->endOfWeek()->toDateString();
                                    $query->whereBetween('DelDate',[date('d-m-Y',strtotime($startWeek)),date('d-m-Y',strtotime($endWeek))]);
                                }elseif($filterVal=='Month'){
                                    $startMonth = Carbon::now()->startOfMonth()->toDateString();
                                    $endMonth = Carbon::now()->endOfWeek()->toDateString();
                                    $query->whereBetween('DelDate',[date('d-m-Y',strtotime($startMonth)),date('d-m-Y',strtotime($endMonth))]);
                                }
                            })
                            ->when($request->get('customer_search'),function($query)use($request){
                                $query->where('shipping_first_name','LIKE','%'.$request->get('customer_search').'%');
                            })
                                ->when(session('city_based_access') == '1',function($query){
                                    $query->where('customer_details.citygroup',session('user_city'));
                                })
                            ->get();
                    $getCollectionOrder = $getCollectionOrder->groupBy('order_id')->paginate(10);
                    return view('BillingAndPayment/pending_online_renew',compact('getCollectionOrder'));
    }

    public function PaymentRecieved(Request $request,$order_id)
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
            return redirect()->to($url);
        }
        if($_SERVER['REQUEST_METHOD']=='GET')
        { 
            //get order_ all details 
            $get_order_details = DB::select("SELECT 
                                                order_details.id as order_details_id,
                                                order_details.*,
                                                order_details.collection_date as collected_at,
                                                renewals.*,
                                                products.product_name as product_name
                                            FROM 
                                                renewals,order_details,products 
                                            WHERE 
                                                renewals.collection_order_id='$order_id' 
                                            AND renewals.status!='Cancel'
                                            AND renewals.order_details_id=order_details.id 
                                            AND order_details.product_id=products.id");
            $get_order_details = json_decode(json_encode($get_order_details),true);
            // $data['collected_at'] = $get_order_details[0]['collected_at'];
            //get customer_details
            $get_customer_id = $get_order_details[0]['customer_id'];
            $get_customer_details = DB::select("SELECT * FROM customer_details WHERE cust_id ='$get_customer_id' ");
            $get_customer_details = json_decode(json_encode($get_customer_details),true);

            //order data
            $data['order_data']['customer_details'] = $get_customer_details;
            $data['order_data']['product_details'] = $get_order_details;
            $data['order_data']['collection_order_id'] = $order_id;
            $total_rent = 0;
            foreach($get_order_details as $ord_data)
            {
                if($ord_data['discount_amt']!=null){
                    $discount = $ord_data['discount_amt'];
                }else{
                    $discount = 0;
                }
                $total_rent += $ord_data['product_rent']-$discount;
            }
            $data['order_data']['total_rent']=$total_rent;
            // print_r( $data['order_data']);
            return view('BillingAndPayment/payment_recieved_page',$data);
        }
        if($_SERVER['REQUEST_METHOD']=='POST')
        { 
            $reference_id = $_POST['reference_id'];
            //$reference_image = $_FILES['reference_image'];
            $payment_mode = $_POST['payment_mode'];
            $comment = $_POST['comment'];
            $order_details_id = $_POST['order_details_id'];
            $renew_date = $_POST['renew_date'];
            $collected_at = date('d-m-Y',strtotime($_POST['collected_at']));
            $total_collected_amount = $_POST['total_collected_amount'];
            //-----------------------Image Upload -------------------------------//
            $filePath = "";
            if($_FILES['reference_image']['name']!=null)
            {
                $reference_image = $_FILES['reference_image']['name'];
                $image_name = $order_id."-".date('Y-m-d');
                //print_r($_FILES['shop_images']['name']);
                $targetDir = "assets/uploads/payment_images/";
                $fileName = basename($_FILES['reference_image']['name']);
                $targetFilePath = $targetDir . $fileName;
                $fileType =strtolower(pathinfo($targetFilePath,PATHINFO_EXTENSION));
                $new_file_name = $targetDir."".$image_name.".".$fileType;
                move_uploaded_file($_FILES["reference_image"]["tmp_name"], $new_file_name);    
                $filePath = "".$image_name.".".$fileType;
                // $filePath = "/devweb/eflow/assets/uploads/payment_images/".$reference_id.".".$fileType;                    
                //array_push($img_path,$filePath);
                //$reference_image_path= json_encode($img_path);
            }

            $order_details = new OrderDetails;
            // old code
            // for($i=0;$i<count($order_details_id);$i++)
            // {
            //     $update_data = [
            //         'pickup_date'=>date('Y-m-d',strtotime("+1 month",strtotime($renew_date[$i]))),
            //         'current_status' => 'Renewed'
            //     ];
            //    $order_details->where('id',$order_details_id[$i])->update($update_data);
            // }
            // new code
            // $order_details_ids = DB::table('renewals')->distinct('renewals.order_details_id')->select('renewals.order_details_id')->where('renewals.collection_order_id',$order_id)->whereNotIn('renewals.status',['Cancel'])->get();
            $collectionDetails = DB::table('renewals')->where('collection_order_id',$order_id)->whereNotIn('status',['Cancel'])->orderBy('id','ASC')->get();
            foreach($collectionDetails as $key=>$value){
                DB::table('order_details')->where('id',$value->order_details_id)->update(['pickup_date'=>$value->end_date,'current_status'=>'Renewed']);
            }
            $Renew = new Renewal();
            $update_renewal_data = [
                'online_method' => $payment_mode,
                'status' => 'Online Renewed',
                'payment_status' => 'Recieved',
                'reference_id'=>$reference_id,
                'image_path'=>$filePath,
                'comment'=>DB::raw('concat(comment,",","'.$comment.'")')
            ];
            
            $Renew->where('collection_order_id',$order_id)->update($update_renewal_data);
            DelOrders::where('order_id',$order_id)->update(['payment_image'=>$filePath,'reference_id'=>$reference_id,'DelAssignedTo'=>'Completed']);
            $Del_Orders = new DelOrders;
            $orderTypeNotIn = config('app.order_type');
            $Del_Orders->where('order_id',$order_id)->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)->update(['status'=>'Collected','DelDate'=>$collected_at]);
            // $email_id = DB::table('customer_details')->join('order_details','order_details.customer_id','=','customer_details.cust_id')->select('customer_details.email_id')->where('order_details.id',$order_details_id)->first('email_id');
            // $email_id = $email_id->email_id;
            // try {
            //     if($email_id != null){
            //         $data_message['message1'] = 'Your Product Renewed Successfully!';
            //         Mail::send('RenewalPickupMail/online_renew_payment_received',$data_message, function($message) use ($email_id)
            //         {     
            //             $message->to($email_id, 'Renewal Payment')->subject('Renewal Payment Received');
            //             $message->from('tempmailquali@gmail.com', 'Quali55Care');
            //         });
            //     }
            // } catch(Exception $ex){
            //     Log::channel('intraerrorlog')->info($e);
            // }
            //Product renewed message sent
            // $getCustDetails = DB::table('order_details')
            //                     ->select('customer_details.customer_name','customer_details.primary_contact_no','customer_details.customer_type')
            //                     ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
            //                     ->where('id',$order_details_id[0])
            //                     ->where('customer_details.customer_type','!=','Corporate')
            //                     ->first();
            // if(!empty($getCustDetails)){
            //     //$customerContactNo = $getCustDetails->primary_contact_no;
            //     if(config('app.app_env') == 'devweb')
            //     {
            //         $customerContactNo = config('app.developer_contact');
            //     }
            //     $customerName = $getCustDetails->customer_name;
            //     $curl = curl_init();
            //     // curl_setopt_array($curl, array(
            //     //     CURLOPT_URL => "https://api.msg91.com/api/v5/flow/",
            //     //     CURLOPT_RETURNTRANSFER => true,
            //     //     CURLOPT_ENCODING => "",
            //     //     CURLOPT_MAXREDIRS => 10,
            //     //     CURLOPT_TIMEOUT => 30,
            //     //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //     //     CURLOPT_CUSTOMREQUEST => "POST",
            //     //     //CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"60cc49120eeed16fcd62d103\",\n  \"sender\": \"QUALCR\",\n  \"mobiles\": \"919920361040\",\n  \"name\": \"testing\",\n  \"orderno\": \"8512457845\",\n  \"equpname\": \"Standard Walker\",\n  \"date\": \"18-06-2021\",\n  \"amount\": \"550\"}",
            //     //     CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"624159139aba0350012b25b6\",\n  \"sender\": \"QUALCR\",\n  \"mobiles\": \"91$customerContactNo\",\n  \"amount\": \"$total_collected_amount\",\n  \"order_id\": \"$order_id\"}",
            //     //     CURLOPT_HTTPHEADER => array(
            //     //     "authkey: 267641AmFwcnWjDS5e6b4757P1",
            //     //     "content-type: application/JSON"
            //     //     ),
            //     // ));

            //     $response = curl_exec($curl);
            //     $err = curl_error($curl);

            //     curl_close($curl);

            //     // if ($err) {
            //     // echo "cURL Error #:" . $err;
            //     // } else {
            //     // echo $response;
            //     // }

            // }
                //-----End-----//
            //insert date time log in lead log
            $logDate = Carbon::now()->toDateString();
            $logTime = Carbon::now()->toTimeString();
            DB::table('leads_log')->insert([
                'log_order_id'=>$order_id,
                'log_lead_status'=>'Order Collected',
                'log_order_type'=>'CO',
                'log_date'=>$logDate,
                'log_time'=>$logTime,
                'updated_by'=>session('username')
            ]);
            leads_log::updateOrCreate(
                [
                   'log_order_id' => $order_id,
                   'log_lead_status' => 'Order Generated',
                   'updated_by' => session('username')
                ],
                [
                   'log_order_lead_date' => date('Y-m-d',strtotime($collected_at)).' '.date('H:i:s'),
                   'log_date' => date('Y-m-d'),
                   'log_time' => date('H:i:s'),
                ]);
	         //send mail if changed something
            $today = Carbon::today()->toDateString();
            // if($request->get('collected_at')!=$today){
            //     $accountsEmail = config('app.accounts_email');
            //     $customer_name = DB::table('del_orders')->where('order_id',$order_id)->first();
            //     $customer_name = $customer_name->shipping_first_name;
            //     $orderType = 'Collection';
            //     $modifiedType = 'Date';
            //     $changedDate = ['from'=>$today,'to'=>$request->get('collected_at')];
            //     $modifiedBy = session('username');
            //     Mail::send('OrderModifyEmail/order-modifyEmail',compact('request','order_id','customer_name','orderType','modifiedType','modifiedBy','changedDate'), function($message) use($accountsEmail,$order_id)
            //     {  
            //         $message->to($accountsEmail, 'Accounts')->subject('Order Modified - '.$order_id);
            //         //$message->to($senderMail->email_id_user, 'Complaint Raised')->subject('Complaint Raised');
            //         $message->from('tempmailquali@gmail.com', 'Quali55Care');
            //     });
            // }
            $ordersController = new OrdersController();
            if(DB::table('del_orders')->join('leads','leads.id','=','del_orders.lead_id')->where('del_orders.order_id',$order_id)->exists()){
                $order = DB::table('del_orders')->join('leads','leads.id','=','del_orders.lead_id')->where('del_orders.order_id',$order_id)->first();
                if(!in_array($order->lead_source,['Corporate Booking','Agent']) && $order->msg_flag == 'n'){
                    $resp = $ordersController->orderCompletedWpMsg($order_id);
                    // dd($resp);
                    DB::table('del_orders')->where('order_id',$request->get('orderId'))->update(['msg_flag'=>'y']);
                }
            }
        }
        return redirect()->to('pending_payments_old')->with('message','Online Payment Done sucessfully');
        
    }


    public function renewal_online_payement_reminder($customer_id,$order_id)
    {
        $customer_details = new customer_detail();
        $get_info = DB::select("SELECT * FROM renewals WHERE order_id IN($order_id) AND (payment_mode='online' OR payment_mode='both') ");
        $data['get_info'] = json_decode(json_encode($get_info),true);
        $mail_data = array();
        $total_rent = 0;
        for($i=0; $i <count($data['get_info']) ; $i++)
        {
            $product_id = $data['get_info'][$i]['product_id'];
            $order_details_id = $data['get_info'][$i]['order_details_id'];
            $start_date = $data['get_info'][$i]['start_date'];
            $end_date = $data['get_info'][$i]['end_date'];
            $get_product_name = DB::select("SELECT product_name FROM products WHERE id ='$product_id' ");
            $data['get_product_name'] = json_decode(json_encode($get_product_name),true);
            $product_name = $data['get_product_name'][0]['product_name'];
            //get product rent
            $get_rent = DB::select("SELECT product_rent FROM order_details WHERE id='$order_details_id' ");
            $data['get_rent'] = json_decode(json_encode($get_rent),true);
            $product_rent = $data['get_rent'][0]['product_rent'];

            $mail_data['product_name'][$i] = $product_name;
            $mail_data['start_date'][$i] = $start_date;
            $mail_data['end_date'][$i] = $end_date;
            $mail_data['product_rent'][$i] = $product_rent;
            $total_rent += $product_rent;
            
        }      
       
            $get_email =  DB::select("SELECT email_id,customer_name FROM customer_details where cust_id = '$customer_id' ");
            $data['get_email'] = json_decode(json_encode($get_email),true);
            $email_id = $data['get_email'][0]['email_id'];
            $customer_name = $data['get_email'][0]['customer_name'];

            $data_message = array(
                'customer_email'=>$email_id,
                'customer_name'=>$customer_name,
                'total_rent'=>$total_rent,);
                // 'mail_data'=>"'".$mail_data."'");
                $data_message['mail_data'] = $mail_data;

               //    Sending mail to customer about renewal of rental product....
                Mail::send('RenewalPickupMail/online_renew_payment_reminder',$data_message, function($message) use ($email_id)
                {     
                    $message->to($email_id, 'Renew Payment Pending Reminder')->subject('Renew Payment Pending Reminder');
                    $message->from('tempmailquali@gmail.com', 'Quali55Care');
                });

            
            return redirect()->back()->with('pop_message',$email_id)->with('pop_cust_name',$customer_name);
    }

    public function ShowPendingPayments()
    {
        //get pending payments of pickups
        $today = date('d-m-Y');
        $orderTypeNotIn = config('app.order_type');
        $orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";
        // dd($orderTypeNotIn);
        $get_pickup_pending = DB::select("SELECT 
                                            order_details.*,
                                            customer_details.customer_name as customer_name,
                                            products.product_name as product_name,
                                            pickups.pickup_order_id as pickup_order_id,
                                            del_orders.DelDate as DelDate
                                        FROM
                                            del_orders,pickups,order_details,customer_details,products
                                        WHERE 
                                            order_details.order_id = pickups.del_order_id
                                            AND order_details.current_status = 'Pending Pickup'
                                            AND pickups.pickup_order_id = del_orders.order_id
                                            AND order_details.product_id = products.id
                                            AND order_details.customer_id = customer_details.cust_id
                                            AND del_orders.DelDate = $today
                                            AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)
                                            ORDER BY del_orders.DelDate DESC ");
        $data['get_pickup_pending'] = json_decode(json_encode($get_pickup_pending),true);
        //print_r($data['get_pickup_pending']);

        //get pending payments of renewals
        $get_renewal_pending = DB::select("SELECT 
                                            order_details.*,
                                            customer_details.customer_name as customer_name,
                                            products.product_name as product_name,
                                            renewals.collection_order_id as collection_order_id,
                                            del_orders.DelDate as DelDate
                                        FROM
                                            del_orders,renewals,order_details,customer_details,products
                                        WHERE 
                                            order_details.order_id = renewals.order_id
                                            AND order_details.current_status = 'Pending Renew'
                                            AND renewals.collection_order_id = del_orders.order_id
                                            AND order_details.product_id = products.id
                                            AND order_details.customer_id = customer_details.cust_id 
                                            AND del_orders.DelDate = $today
                                            AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)
                                            ORDER BY del_orders.DelDate DESC");
        $data['get_renewal_pending'] = json_decode(json_encode($get_renewal_pending),true);
        //print_r($data['get_renewal_pending']);
        $pending_data = array_merge($data['get_pickup_pending'],$data['get_renewal_pending']);
        //sort array by date
        usort($pending_data, function($a, $b) {
            return new DateTime($a['DelDate']) <=> new DateTime($b['DelDate']);
        });

        //$data['get_pending_payment_orders'] =$pending_data
        $product_rent = array_column($pending_data,'product_rent');
        $total_amt = array_sum($product_rent);
        $data['get_pending_payment_orders']=$pending_data;
        $data['total_amt'] =$total_amt;
        return view('BillingAndPayment/pending_payments_old',$data);
    }

    //filter apply
    public function PendigPaymentFilter($filter_val)
    {
        $orderTypeNotIn = config('app.order_type');        
        $orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";
        if($filter_val=='today')
        {
            $today = date('d-m-Y');
            $date_clause = "del_orders.DelDate ='$today' ";
        }
        else if($filter_val=='tomorrow')
        {
            $today = date('d-m-Y');
            $tomorrow = date('d-m-Y',strtotime("+1 day"));
            $date_clause = "del_orders.DelDate ='$tomorrow' ";
        }
        else if($filter_val=='overdue')
        {
            $today = date('d-m-Y');
            $date_clause = "del_orders.DelDate <'$today' ";
        }
        else if($filter_val=='3_days')
        {
            $today = date('d-m-Y');
            $three_days = date('d-m-Y',strtotime("+3 day"));
            $date_clause = "del_orders.DelDate BETWEEN '$today' AND '$three_days' ";
        }
        else if($filter_val=='all')
        {
            $get_pickup_pending = DB::select("SELECT 
                order_details.*,
                customer_details.customer_name as customer_name,
                products.product_name as product_name,
                pickups.pickup_order_id as pickup_order_id,
                del_orders.DelDate as DelDate
            FROM
                del_orders,pickups,order_details,customer_details,products
            WHERE 
                order_details.order_id = pickups.del_order_id
                AND order_details.current_status = 'Pending Pickup'
                AND pickups.pickup_order_id = del_orders.order_id
                AND order_details.product_id = products.id
                AND order_details.customer_id = customer_details.cust_id
                AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)
                ORDER BY del_orders.DelDate DESC ");
            $data['get_pickup_pending'] = json_decode(json_encode($get_pickup_pending),true);
            //print_r($data['get_pickup_pending']);

            //get pending payments of renewals
            $get_renewal_pending = DB::select("SELECT 
                    order_details.*,
                    customer_details.customer_name as customer_name,
                    products.product_name as product_name,
                    renewals.collection_order_id as collection_order_id,
                    del_orders.DelDate as DelDate
                FROM
                    del_orders,renewals,order_details,customer_details,products
                WHERE 
                    order_details.order_id = renewals.order_id
                    AND order_details.current_status = 'Pending Renew'
                    AND renewals.collection_order_id = del_orders.order_id
                    AND order_details.product_id = products.id
                    AND order_details.customer_id = customer_details.cust_id
                    AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) 
                    ORDER BY  del_orders.DelDate DESC");
            $data['get_renewal_pending'] = json_decode(json_encode($get_renewal_pending),true);
            $pending_data = array_merge($data['get_pickup_pending'],$data['get_renewal_pending']);
            //sort array by date
            usort($pending_data, function($a, $b) {
                return new DateTime($a['DelDate']) <=> new DateTime($b['DelDate']);
            });

            //$data['get_pending_payment_orders'] =$pending_data
            $product_rent = array_column($pending_data,'product_rent');
            $total_amt = array_sum($product_rent);
            $data['get_pending_payment_orders']=$pending_data;
            $data['total_amt'] =$total_amt;
            return view('BillingAndPayment/pending_payments_old',$data);

        }
        //get pending payments of pickups
        $get_pickup_pending = DB::select("SELECT 
            order_details.*,
            customer_details.customer_name as customer_name,
            products.product_name as product_name,
            pickups.pickup_order_id as pickup_order_id,
            del_orders.DelDate as DelDate
        FROM
            del_orders,pickups,order_details,customer_details,products
        WHERE 
            order_details.order_id = pickups.del_order_id
            AND order_details.current_status = 'Pending Pickup'
            AND pickups.pickup_order_id = del_orders.order_id
            AND order_details.product_id = products.id
            AND order_details.customer_id = customer_details.cust_id
            AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)
            AND $date_clause
            ORDER BY  del_orders.DelDate DESC ");
        $data['get_pickup_pending'] = json_decode(json_encode($get_pickup_pending),true);
        //get pending payments of renewals
        $get_renewal_pending = DB::select("SELECT 
                order_details.*,
                customer_details.customer_name as customer_name,
                products.product_name as product_name,
                renewals.collection_order_id as collection_order_id,
                del_orders.DelDate as DelDate
            FROM
                del_orders,renewals,order_details,customer_details,products
            WHERE 
                order_details.order_id = renewals.order_id
                AND order_details.current_status = 'Pending Renew'
                AND renewals.collection_order_id = del_orders.order_id
                AND order_details.product_id = products.id
                AND order_details.customer_id = customer_details.cust_id 
                AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)
                AND $date_clause
                ORDER BY  del_orders.DelDate DESC");
        $data['get_renewal_pending'] = json_decode(json_encode($get_renewal_pending),true);
        $pending_data = array_merge($data['get_pickup_pending'],$data['get_renewal_pending']);
        //sort array by date
        usort($pending_data, function($a, $b) {
            return new DateTime($a['DelDate']) <=> new DateTime($b['DelDate']);
        });

        //$data['get_pending_payment_orders'] =$pending_data
        $product_rent = array_column($pending_data,'product_rent');
        $total_amt = array_sum($product_rent);
        $data['get_pending_payment_orders']=$pending_data;
        $data['total_amt'] =$total_amt;
        //echo session('username');
        return view('BillingAndPayment/pending_payments_old',$data);
    }
    public function pendingPaymentOrder(Request $request)
    {
        $orderTypeNotIn = config('app.order_type');
        //3
        $form_min_date = date('d-m-Y',strtotime("-1 days"));
        //dd($form_min_date);
        //4
        $form_max_date = null;
        $from_date = null; 
        $end_date = null; 
        $whereCondition = [];
        $delivery_boy ="All";
        $cities =  array();
        if(session('city_based_access') == '1')
        {
            $cities[0] = (object)(['city'=>session('user_city')]);
        }
        else{
            $cities = DB::table('customer_details')->distinct('citygroup')->whereNotNull('citygroup')->orderBy('citygroup','ASC')->get('citygroup');
        }
        // $cities = DB::table('customer_details')->distinct('city')->whereNotNull('city')->get('city');
        // $get_min_date = DelOrders::first('DelDate');
        // //dd($get_min_date->DelDate);
        // $form_min_date = date('d-m-Y',strtotime($get_min_date->DelDate));
        // $get_max_date = Carbon::now()->toDateString();
        // $form_max_date = date('d-m-Y',strtotime($get_max_date));
        //get master products
        $get_master_products = DB::table('products')->where('flag','=','Active')->get();
        $get_lead_owner = DB::table('user')->where('role','=','user')->get();

        $customer_name = $request->get('filter_customer_name');
        if(isset($customer_name)){
            $whereCondition1 = ['del_orders.shipping_first_name','LIKE','%'.$customer_name.'%'];
            array_push($whereCondition,$whereCondition1);
        }
        $delivery_boy = $request->get('filter_delivery_boy');
        if(isset($delivery_boy) && $delivery_boy!="" && $delivery_boy !="All")
        {
                $whereCondition1 = ['del_orders.DelAssignedTo','LIKE','%'.$delivery_boy.'%'];
                array_push($whereCondition,$whereCondition1);
        }

        $customer_contact = $request->get('filter_contact_no');
        if(isset($customer_contact)) {
            $whereCondition2 = ['del_orders.mobileno','=',$customer_contact];
            array_push($whereCondition,$whereCondition2);
        }
        $order_type = $request->get('filter_order_type');
        if(isset($order_type) && $order_type!='All'){
            if($order_type == 'Replacement'){
                $whereCondition3 = ['del_orders.flag','=',$order_type];
                array_push($whereCondition,$whereCondition3);
            }else if($order_type == 'Live'){
                $whereCondition3 = ['del_orders.deliverypickup','=',"Delivery"];
                array_push($whereCondition,$whereCondition3);
            }
            else{
                $whereCondition3 = ['del_orders.deliverypickup','=',$order_type];
                array_push($whereCondition,$whereCondition3);
            }
        }
        $order_id = $request->get('filter_order_id');
        if(isset($order_id)){
            $whereCondition4 = ['del_orders.order_id','=',$order_id];
            array_push($whereCondition,$whereCondition4);
        }
        $delivery_status = $request->get('filter_delivery_status');
        if(isset($delivery_status) && $delivery_status!='All'){
            $whereCondition5 = ['del_orders.status','=',$delivery_status];
            array_push($whereCondition,$whereCondition5);
        }
        $order_state = $request->get('filter_order_state');
            if(isset($order_state))
            {
                if($order_state != "All")
                {
                    $whereCondition5 = ['del_orders.settlement_status','=',$order_state];
                    array_push($whereCondition,$whereCondition5);
                }
            }

        

        if(in_array(session('user_id'),config('app.accounts_id_array'))){
            if($request->get('filter_from_date') != null && $request->get('filter_end_date') != null)
            {
                $from_date = $request->get('filter_from_date');
                $end_date = $request->get('filter_end_date');
            }
        }else{
            if($request->get('filter_from_date') != null && $request->get('filter_end_date') != null)
            {
                $from_date = $request->get('filter_from_date');
                $end_date = $request->get('filter_end_date');
            }else{
                
                // $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
                // $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

                $currentDate = Carbon::now()->toDateString();

                $from_date = $currentDate; //date('Y-m-d', strtotime('-2 days')); // null; 
                $end_date = $currentDate; //date('Y-m-d'); //null;
                // $from_date = null; //date('Y-m-d', strtotime('-2 days')); // null; 
                // $end_date = null; //date('Y-m-d'); //null;
            }
        }

        if($from_date && $end_date){
            $form_min_date = date('d-m-Y',strtotime($from_date));
            $form_max_date = date('d-m-Y',strtotime($end_date));
        }

        $lead_owner = $request->get('filter_lead_owner');
        if(isset($lead_owner) && $lead_owner!="All"){
            $whereCondition6 = ['leads.lead_owner','=',$lead_owner];
            array_push($whereCondition,$whereCondition6);
        }
        // $allOrders = DB::table('del_orders')
        //                         ->leftJoin('leads',function($join){
        //                             $join->on('del_orders.lead_id','=','leads.id');
        //                         })
        //                         ->leftJoin('user',function($join){
        //                             $join->on('leads.lead_owner','=','user.id');
        //                         })
        //                         ->leftJoin('customer_details',function($join){
        //                             $join->on('leads.customer_id','=','customer_details.cust_id');
        //                         })
        //                         ->select(
        //                             'del_orders.order_id as order_id',
        //                             'del_orders.deliverypickup as order_type',
        //                             'del_orders.lead_id as lead_id',
        //                             'del_orders.invoice_no as invoice_no',
        //                             'del_orders.status as status',
        //                             'del_orders.shipping_first_name as customer_name',
        //                             'del_orders.mobileno as mobile_number',
        //                             'del_orders.location as location',
        //                             'del_orders.DelDate as date',
        //                             'del_orders.line_item_1 as equipments',
        //                             'del_orders.DelAssignedTo as assigned_to',
        //                             'del_orders.TotalAmt as assigned_total_amount',
        //                             'del_orders.PaymentMode as assigned_payment_mode',
        //                             'del_orders.cash as assigned_cash',
        //                             'del_orders.online as assigned_online',
        //                             'del_orders.del_payment_mode as received_payment_mode',
        //                             'del_orders.del_total_amount as received_total_amount',
        //                             'del_orders.del_cash_amount as received_cash',
        //                             'del_orders.del_online_amount as received_online',
        //                             'del_orders.payment_image as payment_image',
        //                             'del_orders.reference_id as reference_id',
        //                             'del_orders.del_receipt_image as receipt_image',
        //                             'del_orders.comment as comment',
        //                             'del_orders.settlement_status as settlement_status',
        //                             'del_orders.floor_no as floor_no',
		// 		                    'del_orders.labour_charges as labour_charges',
        //                             'del_orders.line_item_1 as order_products',
        //                             'del_orders.fulldetails as address',
        //                             'leads.reference_id as lead_reference_id',
        //                             'leads.lead_source as lead_source',
        //                             'user.username as username',
        //                             'del_orders.patient_name as patient_name',
        //                             'del_orders.created_at as order_created_at',
        //                             'del_orders.expense_amt',
        //                             'del_orders.expense_type',
        //                             'del_orders.ccadflag',
        //                             'customer_details.customer_type',
        //                             'del_orders.flag as flag',
        //                             'del_orders.order_expense',
        //                             'del_orders.vendor_charges'
        //                         )
        //                         ->selectRaw("0 as order_products_rent, 0 as order_products_sale, 0 as order_products_transport, 0 as order_products_deposite, 0 as order_vendor_cost")
        //                         ->where($whereCondition)
        //                         ->when($request->get('filter_patient_name'),function($query)use($request){
        //                             $query->where('del_orders.patient_name','LIKE','%'.$request->get('filter_patient_name').'%');
        //                         })
        //                         ->when($request->get('filter_city') && $request->get('filter_city')!='All',function($query)use($request){
        //                             $query->where('customer_details.citygroup',$request->get('filter_city'));
        //                         })
        //                         ->when($request->get('customer_type') && $request->get('customer_type')!='All',function($query)use($request){
        //                             $query->where('customer_details.customer_type',$request->get('customer_type'));
        //                         })
        //                         ->when(session('city_based_access'),function($query){
        //                             $query->where('customer_details.citygroup',session('user_city'));
        //                         })
        //                         ->when($request->get('filter_payment_mode'),function($query)use($request){
        //                             if($request->get('filter_payment_mode') == 'Online')
        //                             $query->where('del_orders.PaymentMode',$request->get('filter_payment_mode'));
        //                             else if($request->get('filter_payment_mode') == 'Cash')
        //                             $query->whereIn('del_orders.PaymentMode',[$request->get('filter_payment_mode'),'COD']);
        //                         })
        //                         ->when($from_date!=null && $end_date!=null,function($query)use($form_min_date,$form_max_date,$request){
        //                             if($request->get('onorderdate'))
        //                             {
        //                                 // $temp_from_min_date = date('d-m-Y',strtotime($form_min_date));
        //                                 // $temp_from_max_date = date('d-m-Y',strtotime($form_max_date));
        //                                 // $query->whereBetween(DB::raw("STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y')"),[DB::raw("STR_TO_DATE('$temp_from_min_date','%d-%m-%Y')"),DB::raw("STR_TO_DATE('$temp_from_max_date','%d-%m-%Y')")]);
        //                                 if($request->get('filter_order_type'))
        //                                 {
        //                                     // $temp_from_min_date = date('d-m-Y',strtotime($form_min_date));
        //                                     // $temp_from_max_date = date('d-m-Y',strtotime($form_max_date));
        //                                     // $query->whereBetween(DB::raw("STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y')"),[DB::raw("STR_TO_DATE('$temp_from_min_date','%d-%m-%Y')"),DB::raw("STR_TO_DATE('$temp_from_max_date','%d-%m-%Y')")]);
        //                                     if($request->get('filter_order_type') == 'Delivery')
        //                                     {
        //                                         $query->whereBetween(DB::raw('DATE(leads.creation_date)'),[date('Y-m-d',strtotime($form_min_date)),date('Y-m-d',strtotime($form_max_date))]);
        //                                     }
        //                                     else{
        //                                         $temp_from_min_date = date('d-m-Y',strtotime($form_min_date));
        //                                         $temp_from_max_date = date('d-m-Y',strtotime($form_max_date));
        //                                         $query->whereBetween(DB::raw("STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y')"),[DB::raw("STR_TO_DATE('$temp_from_min_date','%d-%m-%Y')"),DB::raw("STR_TO_DATE('$temp_from_max_date','%d-%m-%Y')")]);    
        //                                     }
        //                                 }
        //                                 else
        //                                 {
        //                                     $temp_from_min_date = date('d-m-Y',strtotime($form_min_date));
        //                                     $temp_from_max_date = date('d-m-Y',strtotime($form_max_date));
        //                                     $query->whereBetween(DB::raw("STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y')"),[DB::raw("STR_TO_DATE('$temp_from_min_date','%d-%m-%Y')"),DB::raw("STR_TO_DATE('$temp_from_max_date','%d-%m-%Y')")]);
        //                                 }
        //                             }
        //                             else
        //                             {
        //                                 $query->whereBetween(DB::raw('DATE(del_orders.created_at)'),[date('Y-m-d',strtotime($form_min_date)),date('Y-m-d',strtotime($form_max_date))]);
        //                             }
        //                         })
        //                         ->when($request->get('filterleadsource'),function($query)use($request){
        //                             $query->whereIn('leads.lead_source',$request->get('filterleadsource'));
        //                         })
        //                         ->orderBy('del_orders.order_id','Desc')
        //                         ->get();







        // DB::enableQueryLog();


    $allOrders = DB::table('del_orders')
    ->leftJoin('leads', 'del_orders.lead_id', '=', 'leads.id')
    ->leftJoin('user', 'leads.lead_owner', '=', 'user.id')
    ->leftJoin('customer_details', 'leads.customer_id', '=', 'customer_details.cust_id')
    ->select([
        'del_orders.order_id',
        'del_orders.deliverypickup as order_type',
        'del_orders.lead_id',
        'del_orders.invoice_no',
        'del_orders.status',
        'del_orders.shipping_first_name as customer_name',
        'del_orders.mobileno as mobile_number',
        'del_orders.location',
        'del_orders.DelDate as date',
        'del_orders.line_item_1 as equipments',
        'del_orders.DelAssignedTo as assigned_to',
        'del_orders.TotalAmt as assigned_total_amount',
        'del_orders.PaymentMode as assigned_payment_mode',
        'del_orders.cash as assigned_cash',
        'del_orders.online as assigned_online',
        'del_orders.del_payment_mode as received_payment_mode',
        'del_orders.del_total_amount as received_total_amount',
        'del_orders.del_cash_amount as received_cash',
        'del_orders.del_online_amount as received_online',
        'del_orders.payment_image',
        'del_orders.reference_id',
        'del_orders.del_receipt_image as receipt_image',
        'del_orders.comment',
        'del_orders.settlement_status',
        'del_orders.floor_no',
        'del_orders.labour_charges',
        'del_orders.line_item_1 as order_products',
        'del_orders.fulldetails as address',

        'del_orders.ccadflag',
        'del_orders.created_at as order_created_at',
        'customer_details.customer_type',
        'user.username',
        'leads.reference_id as lead_reference_id',
        'leads.lead_source',

        'del_orders.patient_name',

        'del_orders.expense_amt',
        'del_orders.expense_type',

        'del_orders.flag',
        'del_orders.order_expense',
        'del_orders.vendor_charges',

    ])

    ->selectRaw('0 as order_products_rent, 0 as order_products_sale, 0 as order_products_transport, 0 as order_products_deposite, 0 as order_vendor_cost')

    ->where($whereCondition)
    ->when($request->get('filter_patient_name'), function ($query) use ($request) {
        $query->where('del_orders.patient_name', 'LIKE', '%' . $request->get('filter_patient_name') . '%');
    })
    ->when($request->get('filter_city') && $request->get('filter_city') != 'All', function ($query) use ($request) {
        $query->where('customer_details.citygroup', $request->get('filter_city'));
    })
    ->when($request->get('customer_type') && $request->get('customer_type') != 'All', function ($query) use ($request) {
        $query->where('customer_details.customer_type', $request->get('customer_type'));
    })
    ->when(session('city_based_access'), function ($query) {
        $query->where('customer_details.citygroup', session('user_city'));
    })
    ->when($request->get('filter_payment_mode'), function ($query) use ($request) {
        if ($request->get('filter_payment_mode') == 'Online') {
            $query->where('del_orders.PaymentMode', $request->get('filter_payment_mode'));
        } elseif ($request->get('filter_payment_mode') == 'Cash') {
            $query->whereIn('del_orders.PaymentMode', [$request->get('filter_payment_mode'), 'COD']);
        }
    })
    ->when($from_date && $end_date, function ($query) use ($form_min_date, $form_max_date, $request) {
        if ($request->get('onorderdate')) {
            if ($request->get('filter_order_type') == 'Delivery') {
                $query->whereBetween(DB::raw('DATE(leads.creation_date)'), [date('Y-m-d', strtotime($form_min_date)), date('Y-m-d', strtotime($form_max_date))]);
            } else {
                $query->whereBetween(DB::raw('DATE(del_orders.DelDate)'), [date('Y-m-d', strtotime($form_min_date)), date('Y-m-d', strtotime($form_max_date))]);
            }
        } else {
            $query->whereBetween(DB::raw('DATE(del_orders.created_at)'), [date('Y-m-d', strtotime($form_min_date)), date('Y-m-d', strtotime($form_max_date))]);
        }
    })
    ->when($request->get('filterleadsource'), function ($query) use ($request) {
        $query->whereIn('leads.lead_source', $request->get('filterleadsource'));
    })
    ->orderBy('del_orders.order_id', 'Desc')
    ->get();


// $allOrders = DB::getQueryLog();
// print_r(end($allOrders));
// die;







        // dd("Stop");
        // dd($request->get('filtercategories'),$request->get('filterproducts'),$request->get('btn-submit'));
        if($request->get('filtercategories') || $request->get('filterproducts') || $request->get('filter_order_type') == 'Live' || $request->get('btn_submit') == 'export_excel')
        {
            // dd("Stop");
            foreach($allOrders as $key=>$value)
            {
                if($value->order_type == 'Delivery')
                {
                    if($request->get('filterproducts'))
                    {
                        if(!DB::table('order_details')->select('product_id')->where('order_details.order_id',$value->order_id)->whereNotIn('current_status',['Cancel'])->whereIn('product_id',$request->get('filterproducts'))->exists())
                        {
                            unset($allOrders[$key]);
                            continue;
                        }
                    }
                    if($request->get('filtercategories'))
                    {
                        if(!DB::table('order_details')->join('products','products.id','=','order_details.product_id')->select('order_details.product_id')->where('order_details.order_id',$value->order_id)->whereNotIn('order_details.current_status',['Cancel'])->whereIn('products.product_category',$request->get('filtercategories'))->exists())
                        {
                            unset($allOrders[$key]);
                            continue;
                        }
                    }
                    if($request->get('filter_order_type') == 'Live'){
                        if(!DB::table('order_details')->where('order_details.order_id',$value->order_id)->where('order_details.sale_rental','Rental')->whereNotIn('order_details.current_status',['Cancel','Picked Up','Pending Pickup','CustStop',''])->exists())
                        {
                            unset($allOrders[$key]);
                            continue;
                        }
                    }
                    $product_details = DB::table('order_details')
                                            ->join('products','products.id','=','order_details.product_id')
                                            ->select(
                                                'order_details.product_rent',
                                                'order_details.product_deposite',
                                                'order_details.sale_rental',
                                                'order_details.transport',
                                                'products.product_name','order_details.vendor_rent'
                                            )
                                            ->where('order_details.order_id',$value->order_id)
                                            ->get();
                    $products = "";

                    $tot_product_rent = 0;
                    $tot_product_sale = 0;
                    $tot_product_transport = 0;
                    $tot_product_deposite = 0;  
                    $tot_vendor_cost = 0;
                    foreach ($product_details as $k=> $v)
                    {
                        $products .= $v->product_name; 
                        if ($v->sale_rental=='Sale')
                        {
                            $tot_product_sale = $tot_product_sale + $v->product_rent;
                        } else {
                            $tot_product_rent = $tot_product_rent + $v->product_rent;
                        }
                        $tot_product_transport = $tot_product_transport + $v->transport;
                        $tot_product_deposite = $tot_product_deposite + $v->product_deposite;
                        $tot_vendor_cost = $tot_vendor_cost + $v->vendor_rent;
                    }
                    $allOrders[$key]->order_products = $products;
                    $allOrders[$key]->order_products_rent = $tot_product_rent;
                    $allOrders[$key]->order_products_sale = $tot_product_sale;
                    $allOrders[$key]->order_products_transport = $tot_product_transport;
                    $allOrders[$key]->order_products_deposite = $tot_product_deposite;
                    $allOrders[$key]->order_vendor_cost = $tot_vendor_cost;
                }
                elseif($value->order_type == 'Collection')
                {
                    if($request->get('filterproducts'))
                    {
                        if(!DB::table('renewals')->select('product_id')->where('renewals.collection_order_id',$value->order_id)->whereIn('product_id',$request->get('filterproducts'))->exists())
                        {
                            unset($allOrders[$key]);
                            continue;
                        }
                    }
		            if($request->get('filtercategories'))
                    {
                        if(!DB::table('renewals')->join('products','products.id','=','renewals.product_id')->select('renewals.product_id')->where('renewals.collection_order_id',$value->order_id)->whereIn('products.product_category',$request->get('filtercategories'))->exists())
                        {
                            unset($allOrders[$key]);
                            continue;
                        }
                    }
                    //Rahul fake collection
                    $allOrders[$key]->order_products_rent = $allOrders[$key]->assigned_total_amount;
                    

                    $product_details = DB::table('order_details')
                         //->join('renewals','renewals.order_id','=','order_details.order_id')
                         ->join('renewals', function($join)
                            {
                                $join->on('renewals.order_id', '=', 'order_details.order_id');
                                $join->on('renewals.product_id', '=', 'order_details.product_id');
                               // $join->on('renewals.collection_order_id', '=', $value->order_id);
                            })
                    // ->join('products','products.id','=','order_details.product_id')
                        ->select('order_details.vendor_rent' 
                        
                        //     'order_details.product_rent',
                        //     'order_details.product_deposite',
                        //     'order_details.sale_rental',
                        //     'order_details.transport',
                        //     'products.product_name'
                        )
                    ->selectRaw("TIMESTAMPDIFF(MONTH, renewals.start_date, renewals.end_date) as renewal_month")
                    ->where('renewals.collection_order_id',$value->order_id)
                    ->get();
                    // SELECT renewals.collection_order_id, renewals.order_id, order_details.product_id, order_details.vendor_rent, 
                    // TIMESTAMPDIFF(MONTH, renewals.start_date, renewals.end_date) as renewal_month  
                    // FROM order_details  join `renewals` ON renewals.order_id = order_details.order_id 
                    // and renewals.product_id = order_details.product_id and renewals.collection_order_id = 88765679 and renewals.order_id=88764603
                    //SELECT order_details.vendor_rent, order_details.order_id, TIMESTAMPDIFF(MONTH, renewals.start_date, renewals.end_date) as renewal_month  FROM order_details left join `renewals` ON renewals.order_id = order_details.order_id and renewals.collection_order_id = 88765679
                    //SELECT renewals.collection_order_id, renewals.order_id, order_details.product_id, order_details.vendor_rent, TIMESTAMPDIFF(MONTH, renewals.start_date, renewals.end_date) as renewal_month  FROM order_details  join `renewals` ON renewals.order_id = order_details.order_id and renewals.collection_order_id = 88765679 and renewals.order_id=88764603
                    //88783178	CCDA
                    // $products = "";
                    $tot_vendor_cost = 0;
                    foreach ($product_details as $k=> $v)
                    {
                       // $products .= $v->product_name.' Type:'.$v->sale_rental.' Rate:'.$v->product_rent.' Deposit:'.$v->product_deposite.' Transport:'.$v->transport;
                       $tot_vendor_cost = $tot_vendor_cost + ($v->vendor_rent * $v->renewal_month);
                    }
                    $allOrders[$key]->order_vendor_cost = $tot_vendor_cost;
                   // dd($products);
                }
                elseif($value->order_type == 'Pick Up')
                {
                    if($request->get('filterproducts'))
                    {
                        if(!DB::table('pickups')->select('product_id')->where('pickups.pickup_order_id',$value->order_id)->whereIn('product_id',$request->get('filterproducts'))->exists())
                        {
                            unset($allOrders[$key]);
                            continue;
                        }
                    }
		            if($request->get('filtercategories'))
                    {
                        if(!DB::table('pickups')->join('products','products.id','=','pickups.product_id')->select('pickups.product_id')->where('pickups.pickup_order_id',$value->order_id)->whereIn('products.product_category',$request->get('filtercategories'))->exists())
                        {
                            unset($allOrders[$key]);
                            continue;
                        }
                    }
                    // $product_details = DB::table('order_details')
                    // ->join('products','products.id','=','order_details.product_id')
                    // ->select(
                    //     'order_details.product_rent',
                    //     'order_details.product_deposite',
                    //     'order_details.sale_rental',
                    //     'order_details.transport',
                    //     'products.product_name'
                    // )
                    // ->where('order_details.order_id',$value->order_id)
                    // ->get();
                    // $products = "";
                    // foreach ($product_details as $k=> $v)
                    // {
                    // $products .= $v->product_name.' Type:'.$v->sale_rental.' Rate:'.$v->product_rent.' Deposit:'.$v->product_deposite.' Transport:'.$v->transport;
                    // }
                    // $allOrders[$key]->order_products = $products;
                }
                if(DB::table('order_expenses')->where('order_id',$value->order_id)->exists())
                {
                    $expenses = DB::table('order_expenses')->select('transport','labour','hardware_expenses')->where('order_id',$value->order_id)->get();
                    $allOrders[$key]->expenses = array_sum($expenses->pluck('transport')->toArray()) + array_sum($expenses->pluck('labour')->toArray()) + array_sum($expenses->pluck('hardware_expenses')->toArray());
                }
                else
                {
                    $allOrders[$key]->expenses = 0;
                }
            }
        }
        //2
        if($request->get('btn_submit')=='export_excel'){
            ob_end_clean(); // this
            ob_start(); // and this
            return Excel::download(new BillingPendingPayments($allOrders), 'pending_payments.xls');
        }
        $non_settled_orders  = $allOrders->paginate(10);
        // DB::enableQueryLog();
        $details_count = DB::table('del_orders')
                                ->leftJoin('leads',function($join){
                                    $join->on('del_orders.lead_id','=','leads.id');
                                })
                                ->leftJoin('user',function($join){
                                    $join->on('leads.lead_owner','=','user.id');
                                })
                                ->leftJoin('customer_details',function($join){
                                    $join->on('leads.customer_id','=','customer_details.cust_id');
                                })
                                ->select(
                                    'del_orders.order_id as order_id',
                                    'del_orders.deliverypickup as order_type',
                                    'del_orders.status as status',
                                    'del_orders.TotalAmt as assigned_total_amount',
                                    'del_orders.PaymentMode as assigned_payment_mode',
                                    'del_orders.cash as assigned_cash',
                                    'del_orders.online as assigned_online',
                                    'del_orders.del_payment_mode as received_payment_mode',
                                    'del_orders.del_total_amount as received_total_amount',
                                    'del_orders.del_cash_amount as received_cash',
                                    'del_orders.reference_id as reference_id',
                                    'del_orders.del_online_amount as received_online',
                                    'del_orders.settlement_status as settlement_status',
                                    'user.username as username',
                                    'del_orders.order_expense',
                                    'del_orders.vendor_charges'
                                )
                                ->where($whereCondition)
                                ->when($request->get('filter_patient_name'),function($query)use($request){
                                    $query->where('del_orders.patient_name','LIKE','%'.$request->get('filter_patient_name').'%');
                                })
                                ->when($request->get('filter_city') && $request->get('filter_city')!='All',function($query)use($request){
                                    $query->where('customer_details.citygroup',$request->get('filter_city'));
                                })
                                ->when($request->get('customer_type') && $request->get('customer_type')!='All',function($query)use($request){
                                    $query->where('customer_details.customer_type',$request->get('customer_type'));
                                })
                                ->when(session('city_based_access'),function($query){
                                    $query->where('customer_details.citygroup',session('user_city'));
                                })
                                ->when($request->get('filter_payment_mode'),function($query)use($request){
                                    if($request->get('filter_payment_mode') == 'Online')
                                    $query->where('del_orders.PaymentMode',$request->get('filter_payment_mode'));
                                    else if($request->get('filter_payment_mode') == 'Cash')
                                    $query->whereIn('del_orders.PaymentMode',[$request->get('filter_payment_mode'),'COD']);
                                })
                                ->when($from_date!=null && $end_date!=null,function($query)use($form_min_date,$form_max_date,$request){
                                    if($request->get('onorderdate'))
                                    {
                                        // $temp_from_min_date = date('d-m-Y',strtotime($form_min_date));
                                        // $temp_from_max_date = date('d-m-Y',strtotime($form_max_date));
                                        // $query->whereBetween(DB::raw("STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y')"),[DB::raw("STR_TO_DATE('$temp_from_min_date','%d-%m-%Y')"),DB::raw("STR_TO_DATE('$temp_from_max_date','%d-%m-%Y')")]);
                                        if($request->get('filter_order_type'))
                                        {
                                            // $temp_from_min_date = date('d-m-Y',strtotime($form_min_date));
                                            // $temp_from_max_date = date('d-m-Y',strtotime($form_max_date));
                                            // $query->whereBetween(DB::raw("STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y')"),[DB::raw("STR_TO_DATE('$temp_from_min_date','%d-%m-%Y')"),DB::raw("STR_TO_DATE('$temp_from_max_date','%d-%m-%Y')")]);
                                            if($request->get('filter_order_type') == 'Delivery')
                                            {
                                                $query->whereBetween(DB::raw('DATE(leads.creation_date)'),[date('Y-m-d',strtotime($form_min_date)),date('Y-m-d',strtotime($form_max_date))]);
                                            }
                                            else{
                                                $temp_from_min_date = date('d-m-Y',strtotime($form_min_date));
                                                $temp_from_max_date = date('d-m-Y',strtotime($form_max_date));
                                                $query->whereBetween(DB::raw("STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y')"),[DB::raw("STR_TO_DATE('$temp_from_min_date','%d-%m-%Y')"),DB::raw("STR_TO_DATE('$temp_from_max_date','%d-%m-%Y')")]);    
                                            }
                                        }
                                        else
                                        {
                                            $temp_from_min_date = date('d-m-Y',strtotime($form_min_date));
                                            $temp_from_max_date = date('d-m-Y',strtotime($form_max_date));
                                            $query->whereBetween(DB::raw("STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y')"),[DB::raw("STR_TO_DATE('$temp_from_min_date','%d-%m-%Y')"),DB::raw("STR_TO_DATE('$temp_from_max_date','%d-%m-%Y')")]);
                                        }
                                    }
                                    else
                                    {
                                        $query->whereBetween(DB::raw('DATE(del_orders.created_at)'),[date('Y-m-d',strtotime($form_min_date)),date('Y-m-d',strtotime($form_max_date))]);
                                    }
                                })
                                ->when($request->get('filterleadsource'),function($query)use($request){
                                    $query->whereIn('leads.lead_source',$request->get('filterleadsource'));
                                })
                                ->orderBy('del_orders.order_id','Desc')
                                ->get();
        // dd($details_count);
        $all_delivery_boys = DB::table('del_orders')->select('del_orders.DelAssignedTo')->distinct('del_orders.DelAssignedTo')->get()->toArray();
        $temp_delivery_not_settled_count = 0;
        $temp_delivery_settled_count = 0;
        $temp_pickup_not_settled_count = 0;
        $temp_pickup_settled_count = 0;
        $temp_collection_not_settled_count = 0;
        $temp_collection_settled_count = 0;
        $temp_maintenance_not_settled_count = 0;
        $temp_maintenance_settled_count = 0;

        $temp_delivery_online_amt = 0;
        $temp_delivery_cash_amt = 0;
        $temp_pickup_online_amt = 0;
        $temp_pickup_cash_amt = 0;
        $temp_collection_online_amt = 0;
        $temp_collection_cash_amt = 0;
        $temp_maintenance_online_amt = 0;
        $temp_maintenance_cash_amt = 0;
        $taxableamountrent = 0;
        $taxableamountrenew = 0;
        $count_array = array();
        $amount_array = array();
        $prodcountrent = 0;
        $prodcountrenewal = 0;
        if(count($request->all())>3)
        {
            foreach($details_count as $key => $value)
            {
                if($value->order_type == 'Delivery')
                {
                    if($request->get('filterproducts'))
                    {
                        $prodcountrent = $prodcountrent + count(DB::table('order_details')->select('product_id')->where('order_details.order_id',$value->order_id)->whereNotIn('current_status',['Cancel'])->whereIn('product_id',$request->get('filterproducts'))->get()->pluck('product_id')->toArray());
                    }
                    if($request->get('filtercategories'))
                    {
                        $prodcountrent = $prodcountrent + count(DB::table('order_details')->join('products','products.id','=','order_details.product_id')->select('order_details.product_id')->where('order_details.order_id',$value->order_id)->whereNotIn('order_details.current_status',['Cancel'])->whereIn('products.product_category',$request->get('filtercategories'))->get()->pluck('product_id')->toArray());
                    }
                    if($value->assigned_payment_mode == "Online")
                    {
                        $temp_delivery_online_amt = $temp_delivery_online_amt + $value->assigned_total_amount;
                    }
                    else if($value->assigned_payment_mode == "Cash")
                    {
                        $temp_delivery_cash_amt = $temp_delivery_cash_amt + $value->assigned_total_amount;   
                    }
                    else if($value->assigned_payment_mode == "Both")
                    {
                        $temp_delivery_cash_amt = $temp_delivery_cash_amt + $value->assigned_total_amount;
                        $temp_delivery_online_amt = $temp_delivery_online_amt + $value->assigned_total_amount;
                    }
                    if($value->settlement_status == 'N')
                    {
                        $temp_delivery_not_settled_count++;
                    }
                    elseif($value->settlement_status == 'Y')
                    {
                        $temp_delivery_settled_count++;
                    }
                    if($value->status != 'Cancel'){
                        $amounttotal = array_sum(DB::table('order_details')->select('product_rent')->where('order_details.order_id',$value->order_id)->whereNotIn('current_status',['Cancel'])->get()->pluck('product_rent')->toArray());
                        $crtotal = array_sum(DB::table('cr_dr_note')->where('order_id',$value->order_id)->where('intype','R')->where('crdrtype','Cr')->get('amount')->pluck('amount')->toArray());
                        $drtotal = array_sum(DB::table('cr_dr_note')->where('order_id',$value->order_id)->where('intype','R')->where('crdrtype','Dr')->get('amount')->pluck('amount')->toArray());
                        // dd($crtotal,$drtotal);
                        $amounttotal = $amounttotal - $crtotal + $drtotal;
                        // dd($amounttotal);
                        $taxableamountrent = $taxableamountrent + $amounttotal;
                        // $taxableamountrent = $taxableamountrent + array_sum(DB::table('order_details')->select('product_rent')->where('order_details.order_id',$value->order_id)->where('sale_rental','Rental')->whereNotIn('current_status',['Cancel'])->get()->pluck('product_rent')->toArray());
                        // dd('a');
                    }
                }
    
                if($value->order_type == 'Collection')
                {
                    // $temp_collection_cash_amt = $temp_collection_cash_amt + $value->assigned_cash;
                    // $temp_collection_online_amt = $temp_collection_online_amt + $value->assigned_online;
                    if($request->get('filterproducts'))
                    {
                        $prodcountrenewal = $prodcountrenewal + count(DB::table('renewals')->select('product_id')->where('renewals.collection_order_id',$value->order_id)->whereNotIn('status',['Cancel'])->whereIn('product_id',$request->get('filterproducts'))->get()->pluck('product_id')->toArray());
                    }
                    if($request->get('filtercategories'))
                    {
                        $prodcountrenewal = $prodcountrenewal + count(DB::table('renewals')->join('products','products.id','=','renewals.product_id')->select('renewals.product_id')->where('renewals.collection_order_id',$value->order_id)->whereIn('products.product_category',$request->get('filtercategories'))->get()->pluck('product_id')->toArray());
                    }
                    if($value->assigned_payment_mode == "Online")
                    {
                        $temp_collection_online_amt = $temp_collection_online_amt + $value->assigned_total_amount;
                    }
                    else if($value->assigned_payment_mode == "Cash")
                    {
                        $temp_collection_cash_amt = $temp_collection_cash_amt + $value->assigned_total_amount;   
                    }
                    else if($value->assigned_payment_mode == "Both")
                    {
                        $temp_collection_cash_amt = $temp_collection_cash_amt + $value->assigned_total_amount;
                        $temp_collection_online_amt = $temp_collection_online_amt + $value->assigned_total_amount;
                    }
                    if($value->settlement_status == 'N')
                    {
                        $temp_collection_not_settled_count++;
                    }
                    elseif($value->settlement_status == 'Y')
                    {
                        $temp_collection_settled_count++;
                    }
                    if($value->status != 'Cancel'){
                        $taxableamountrenew = $taxableamountrenew + $value->assigned_total_amount;
                    }
                }
                if($value->order_type == 'Pick Up')
                {
                    // $temp_pickup_cash_amt = $temp_pickup_cash_amt + $value->assigned_cash;
                    // $temp_pickup_online_amt = $temp_pickup_online_amt + $value->assigned_online;
                    if($value->assigned_payment_mode == "Online")
                    {
                        $temp_pickup_online_amt = $temp_pickup_online_amt + $value->assigned_total_amount;
                    }
                    else if($value->assigned_payment_mode == "Cash")
                    {
                        $temp_pickup_cash_amt = $temp_pickup_cash_amt + $value->assigned_total_amount;   
                    }
                    else if($value->assigned_payment_mode == "Both")
                    {
                        $temp_pickup_cash_amt = $temp_pickup_cash_amt + $value->assigned_total_amount;
                        $temp_pickup_online_amt = $temp_pickup_online_amt + $value->assigned_total_amount;
                    }
                    if($value->settlement_status == 'N')
                    {
                        $temp_pickup_not_settled_count++;
                    }
                    elseif($value->settlement_status == 'Y')
                    {
                        $temp_pickup_settled_count++;
                    }
                }
                if(in_array($value->order_type,$orderTypeNotIn))
                {
                    // $temp_pickup_cash_amt = $temp_pickup_cash_amt + $value->assigned_cash;
                    // $temp_pickup_online_amt = $temp_pickup_online_amt + $value->assigned_online;
                    if($value->assigned_payment_mode == "Online")
                    {
                        $temp_maintenance_online_amt = $temp_maintenance_online_amt + $value->assigned_total_amount;
                    }
                    else if($value->assigned_payment_mode == "Cash")
                    {
                        $temp_maintenance_cash_amt = $temp_maintenance_cash_amt + $value->assigned_total_amount;   
                    }
                    else if($value->assigned_payment_mode == "Both")
                    {
                        $temp_maintenance_cash_amt = $temp_maintenance_cash_amt + $value->assigned_total_amount;
                        $temp_maintenance_online_amt = $temp_maintenance_online_amt + $value->assigned_total_amount;
                    }
                    if($value->settlement_status == 'N')
                    {
                        $temp_maintenance_not_settled_count++;
                    }
                    elseif($value->settlement_status == 'Y')
                    {
                        $temp_maintenance_settled_count++;
                    }
                }
            }        
        }
        array_push($count_array,$temp_delivery_not_settled_count);
        array_push($count_array,$temp_delivery_settled_count);
        array_push($count_array,$temp_collection_not_settled_count);
        array_push($count_array,$temp_collection_settled_count);
        array_push($count_array,$temp_pickup_not_settled_count);
        array_push($count_array,$temp_pickup_settled_count);
        array_push($count_array,$temp_maintenance_not_settled_count);
        array_push($count_array,$temp_maintenance_settled_count);

        array_push($amount_array,$temp_delivery_online_amt);
        array_push($amount_array,$temp_delivery_cash_amt);
        array_push($amount_array,$temp_collection_online_amt);
        array_push($amount_array,$temp_collection_cash_amt);
        array_push($amount_array,$temp_pickup_online_amt);
        array_push($amount_array,$temp_pickup_cash_amt);
        array_push($amount_array,$temp_maintenance_online_amt);
        array_push($amount_array,$temp_maintenance_cash_amt);
        array_push($amount_array,$taxableamountrent);
        array_push($amount_array,$taxableamountrenew);
        
        $filter_arr = [
            "cust_name"=>$customer_name,
            "patient_name"=>$request->get('filter_patient_name'),
            "city"=>$request->get('filter_city'),
            "customer_type"=>$request->get('customer_type'),
            "filter_payment_mode"=>$request->get('filter_payment_mode'),
            "delivery_boy"=>$delivery_boy,
            "cust_no"=>$customer_contact,
            "order_type"=>$order_type,
            "order_id"=>$order_id,
            "order_state"=>$order_state,
            "delivery_status"=>$delivery_status,
            "from_date"=>$from_date,
            "end_date"=>$end_date,
            "lead_owner"=>$lead_owner,
            'filterleadsource'=>$request->get('filterleadsource'),
            'filterproducts'=>$request->get('filterproducts'),
            'filtercategories'=>$request->get('filtercategories'),
            'onorderdate'=>$request->get('onorderdate'),
        ];
                             // ->get();
        //customer bank details
        $bankCustomerDetails = array();
        foreach ($non_settled_orders as $key => $orderDetails) {
            // if($orderDetails->order_type == 'Pick Up'){
            //     $getPickupData = DB::table('pickups')->where('pickup_order_id',$orderDetails->order_id)->get();
            //     $orderDeatilsId = $getPickupData[0]->order_details_id;
            //     $getCustomerDetails = DB::table('order_details')
            //                             ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
            //                             ->where('order_details.id',$orderDeatilsId)
            //                             ->first('customer_details.*');
            //     $bankCustomerDetails[$key] = $getCustomerDetails;
            // }
            if(DB::table('activity_log')->where('key_id',$orderDetails->order_id)->where('fields','del_receipt_image')->exists()){
                $non_settled_orders[$key]->uploaded_at = date('d-m-Y',strtotime(DB::table('activity_log')->where('key_id',$orderDetails->order_id)->where('fields','del_receipt_image')->orderBy('id','DESC')->first()->updated_at));            
            }
            else{
                $non_settled_orders[$key]->uploaded_at = null;
            }
            $non_settled_orders[$key]->modified = false;
            if($orderDetails->order_type == 'Pick Up'){
                $getPickupData = DB::table('pickups')->join('leads','pickups.lead_id','=','leads.id')->join('user','user.id','leads.lead_owner')->where('pickups.pickup_order_id',$orderDetails->order_id)->get();
                if(isset($getPickupData[0])){
                    $orderDeatilsId = $getPickupData[0]->order_details_id;
                    $getCustomerDetails = DB::table('order_details')
                                            ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                                            ->where('order_details.id',$orderDeatilsId)
                                            ->first('customer_details.*');
                    $bankCustomerDetails[$key] = $getCustomerDetails;
                    $non_settled_orders[$key]->username = $getPickupData[0]->username;
                }
                if(DB::table('adjustment_table')->where('order_id',$orderDetails->order_id)->whereNotIn('adjusted_amount',['0'])->where('flag','A')->exists())
                {
                    $non_settled_orders[$key]->adjustedIn = 1;
                }
                else
                {
                    $non_settled_orders[$key]->adjustedIn = 0;
                }
                if(DB::table('adjustment_table')->where('fromorderid',$orderDetails->order_id)->whereNotIn('adjusted_amount',['0'])->where('flag','A')->exists())
                {
                    $non_settled_orders[$key]->adjustedFrom = 1;
                }
                else
                {
                    $non_settled_orders[$key]->adjustedFrom = 0;
                }
            }
            if($orderDetails->order_type == 'Collection'){
                $getCollectionData = DB::table('renewals')->join('leads','renewals.lead_id','=','leads.id')->join('user','user.id','leads.lead_owner')->where('renewals.collection_order_id',$orderDetails->order_id)->get();
                if(isset($getCollectionData[0])){
                    $non_settled_orders[$key]->username = $getCollectionData[0]->username;
                }
                if(DB::table('adjustment_table')->where('order_id',$orderDetails->order_id)->whereNotIn('adjusted_amount',['0'])->where('flag','A')->exists())
                {
                    $non_settled_orders[$key]->adjustedIn = 1;
                }
                else
                {
                    $non_settled_orders[$key]->adjustedIn = 0;
                }
                if(DB::table('adjustment_table')->where('fromorderid',$orderDetails->order_id)->whereNotIn('adjusted_amount',['0'])->where('flag','A')->exists())
                {
                    $non_settled_orders[$key]->adjustedFrom = 1;
                }
                else
                {
                    $non_settled_orders[$key]->adjustedFrom = 0;
                }
                if($orderDetails->ccadflag=='CCAD'){
                    $non_settled_orders[$key]->ccad_delivery_order_id = $getCollectionData[0]->order_id;
                }
            }
            if(DB::table('cr_dr_note')->where('order_id',$orderDetails->order_id)->where('flag','A')->exists())
            {
                $non_settled_orders[$key]->modified = true;
            }
            else
            {
                $non_settled_orders[$key]->modified = false;
            }
            if($orderDetails->order_type == 'Delivery')
            {
                if(DB::table('cr_dr_note')->where('order_id',$orderDetails->order_id)->where('flag','A')->exists())
                {
                    $non_settled_orders[$key]->modified = true;
                }
                else
                {
                    $non_settled_orders[$key]->modified = false;
                }
                if(DB::table('adjustment_table')->where('order_id',$orderDetails->order_id)->whereNotIn('adjusted_amount',['0'])->where('flag','A')->exists())
                {
                    $non_settled_orders[$key]->adjustedIn = 1;
                }
                else
                {
                    $non_settled_orders[$key]->adjustedIn = 0;
                }
                if(DB::table('adjustment_table')->where('fromorderid',$orderDetails->order_id)->whereNotIn('adjusted_amount',['0'])->where('flag','A')->exists())
                {
                    $non_settled_orders[$key]->adjustedFrom = 1;
                }
                else
                {
                    $non_settled_orders[$key]->adjustedFrom = 0;
                }
                if(DB::table('renewals')->where('order_id',$orderDetails->order_id)->where('flag','CCAD')->exists()){
                    $non_settled_orders[$key]->ccad_collection_order_id = DB::table('renewals')->where('order_id',$orderDetails->order_id)->where('flag','CCAD')->first()->collection_order_id;
                }
            }
            if(in_array($orderDetails->order_type,$orderTypeNotIn))
            {
                if(DB::table('adjustment_table')->where('order_id',$orderDetails->order_id)->whereNotIn('adjusted_amount',['0'])->where('flag','A')->exists())
                {
                    $non_settled_orders[$key]->adjustedIn = 1;
                }
                else
                {
                    $non_settled_orders[$key]->adjustedIn = 0;
                }
                if(DB::table('adjustment_table')->where('fromorderid',$orderDetails->order_id)->whereNotIn('adjusted_amount',['0'])->where('flag','A')->exists())
                {
                    $non_settled_orders[$key]->adjustedFrom = 1;
                }
                else
                {
                    $non_settled_orders[$key]->adjustedFrom = 0;
                }
            }
            if(DB::table('order_expenses')->where('order_id',$orderDetails->order_id)->exists())
            {
                $expenses = DB::table('order_expenses')->select('transport','labour','hardware_expenses')->where('order_id',$orderDetails->order_id)->get();
                $non_settled_orders[$key]->expenses = array_sum($expenses->pluck('transport')->toArray()) + array_sum($expenses->pluck('labour')->toArray()) + array_sum($expenses->pluck('hardware_expenses')->toArray());
            }
            else
            {
                $non_settled_orders[$key]->expenses = 0;
            }
        }
        $orderClosedReason = config('app.order_closed_reason');
        $leadsource = config('app.lead_source');
        $products = DB::table('products')->where('flag','Active')->get()->sortBy('product_name');
        $categories = DB::table('products')->distinct('product_category')->select('product_category')->where('flag','Active')->get()->sortBy('product_category');
        return view('BillingAndPayment/pending_payments_old',compact('non_settled_orders','filter_arr','count_array','amount_array','get_lead_owner','all_delivery_boys','bankCustomerDetails','cities','orderClosedReason','leadsource','products','prodcountrent','prodcountrenewal','categories'));
    }


    public function getOrderDetails(Request $request)
    {
        $orderTypeNotIn = config('app.order_type');
        if($request->get('request_type') == 'Delivery')
        {
            $orderTypeNotIn = config('app.order_type');
            $order_details = DB::table('order_details')
                                ->join('vendor_details','order_details.vendor_id','=','vendor_details.id')
                                ->join('products','order_details.product_id','=','products.id')
                                ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                                ->join('vendor_products','order_details.vendor_product_id','=','vendor_products.id')
                                ->join('vendor_warehouse','order_details.vendor_warehouse_id','=','vendor_warehouse.id')
                                ->join('del_orders','order_details.order_id','=','del_orders.order_id')
                                ->select('order_details.*',
                                'vendor_details.registered_name as vendor_name',
                                'customer_details.customer_name as customer_name',
                                'customer_details.primary_contact_no as primary_contact_no',
                                'products.product_name as product_name',
                                'vendor_products.product_rent_approved as vendor_rent',
                                'vendor_warehouse.wh_name as warehouse_name',
                                'vendor_warehouse.wh_area as warehouse_area',
                                'vendor_warehouse.wh_city as warehouse_city',
                                'vendor_warehouse.wh_pincode as warehouse_pincode',
                                'del_orders.TotalAmt as assigned_total_amount',
                                'del_orders.del_total_amount as received_total_amount',
                                'del_orders.DelDate as deldate',
                                'del_orders.*',
                                'customer_details.*'
                                )
                                ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                                ->where('order_details.order_id',$request->get('order_id'))
                                // ->whereNotIn('order_details.current_status',['Cancel'])
                                ->get();

            foreach($order_details as $key=>$value)
            {
                $order_details[$key]->offered_rent = $value->product_rent;
                $order_details[$key]->offered_deposite = $value->product_deposite;
                $order_details[$key]->offered_transport = $value->transport;
                $order_details[$key]->product_rent = RenewalPickupController::fetchCrDrData($value->id,'R');
                $order_details[$key]->product_deposite = RenewalPickupController::fetchCrDrData($value->id,'D');
                $order_details[$key]->transport = RenewalPickupController::fetchCrDrData($value->id,'T');
                if(DB::table('adjustment_table')->where('order_details_id',$value->id)->orWhere('adjusted_order_details_id',$value->id)->whereNotIn('adjusted_amount',['0'])->where('flag','A')->exists())
                {
                    // return "exists";
                    $adjusted_deposit = DB::table('adjustment_table')->select('adjusted_amount')->where('order_details_id',$value->id)->orWhere('adjusted_order_details_id',$value->id)->whereNotIn('adjusted_amount',['0'])->where('flag','A')->get();
                    $order_details[$key]->adjusted_deposit = array_sum($adjusted_deposit->pluck('adjusted_amount')->toArray());
                }
                else
                {
                    $order_details[$key]->adjusted_deposit = 0;
                }
                $order_details[$key]->creation_date = date('d-m-Y',strtotime($value->creation_date));
            }
            
            return $order_details;
                                // ->where('order_details.sale_rental','Rental')
                                // ->whereNotIn('order_details.current_status',$status)
                                // ->whereIn('order_details.product_id',$product_id)
                                // ->where($whereClause)
                                // ->whereBetween('order_details.creation_date',[$get_min_date,$get_max_date])
                                // ->paginate(10);

            
        }
        elseif($request->get('request_type') == 'Collection')
        {
            if(!DB::table('del_orders')->where('order_id',$request->get('order_id'))->where('ccadflag','CCAD')->exists()){
            $orderTypeNotIn = config('app.order_type');
            $order_details = DB::table('renewals')
                                ->join('order_details','renewals.order_details_id','=','order_details.id')
                                ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                                ->join('del_orders','renewals.collection_order_id','=','del_orders.order_id')
                                ->join('products','renewals.product_id','=','products.id')
                                ->join('vendor_details','order_details.vendor_id','=','vendor_details.id')
                                ->join('vendor_warehouse','order_details.vendor_warehouse_id','=','vendor_warehouse.id')
                                ->select(
                                    'del_orders.DelDate as date',
                                    'products.product_name as product_name',
                                    'vendor_details.registered_name as vendor_name',
                                    'vendor_warehouse.wh_name as warehouse_name',
                                    'vendor_warehouse.wh_area as warehouse_area',
                                    'vendor_warehouse.wh_city as warehouse_city',
                                    'vendor_warehouse.wh_pincode as warehouse_pincode',
                                    'order_details.product_rent as product_rent',
                                    'order_details.unique_id',
                                    'order_details.sale_rental',
                                    'renewals.start_date as start_date',
                                    'renewals.end_date as end_date',
                                    'renewals.adjusted_deposit',
                                    'renewals.discount_amt',
                                    'renewals.id as renewal_id',
                                    'del_orders.TotalAmt as total_amount',
                                    'del_orders.del_total_amount as received_total_amount',
                                    'del_orders.*',
                                    'customer_details.*'
                                )
                                ->whereNotIn('renewals.status',['Cancel'])
                                ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                                ->where('renewals.collection_order_id',$request->get('order_id'))
                                ->get();
                foreach($order_details as $key=>$value)
                {
                    $order_details[$key]->offered_rent = $value->product_rent;
                    $order_details[$key]->product_rent = $this->fetchCrDrDataRE($value->renewal_id,$value->PaymentMode);
                }
            return $order_details;
            }
            else{
                return false;
            }
        }
        elseif($request->get('request_type') == 'Pick Up' || $request->get('request_type') == 'Pickup')
        {
            DB::enableQueryLog();
            $orderTypeNotIn = config('app.order_type');
            $order_details = DB::table('pickups')
                                ->join('order_details','pickups.order_details_id','=','order_details.id')
                                ->join(\DB::raw('customer_details as c1'),'order_details.customer_id','=','c1.cust_id')
                                ->join('del_orders','pickups.pickup_order_id','=','del_orders.order_id')
                                ->join('products','pickups.product_id','=','products.id')
                                ->leftJoin('vendor_details','pickups.drop_vendor_id','=','vendor_details.id')
                                ->leftJoin('vendor_warehouse','pickups.drop_warehouse_id','=','vendor_warehouse.id')
                                ->leftJoin(\DB::raw('customer_details as c2'),'pickups.drop_warehouse_id','=','c2.cust_id')
                                ->select(
                                    'del_orders.DelDate as date',
                                    'products.product_name as product_name',
                                    'vendor_details.registered_name as vendor_name',
                                    // 'vendor_warehouse.wh_area as warehouse_area',
                                    // 'vendor_warehouse.wh_city as warehouse_city',
                                    // 'vendor_warehouse.wh_pincode as warehouse_pincode',
                                    'order_details.product_deposite as product_deposite',
                                    'order_details.unique_id',
                                    'order_details.sale_rental',
                                    'pickups.transport as transport',
                                    'del_orders.TotalAmt as total_amount',
                                    'del_orders.del_total_amount as paid_total_amount',
                                    'del_orders.*',
                                    'c1.*',
                                    'pickups.id as pickup_id',
                                    'pickups.drop_location'
                                )
                                ->selectRaw("concat_WS(',', vendor_warehouse.wh_name,  c2.customer_name,c2.address_line_1, c2.address_line_2)  as warehouse_name, 
                                concat_ws(',', vendor_warehouse.wh_area , c2.area)  as warehouse_area, concat_ws(',', vendor_warehouse.wh_city, c2.city) as warehouse_city,  
                                concat_WS(',', vendor_warehouse.wh_city, c2.city) as warehouse_city, concat_WS(',', vendor_warehouse.wh_pincode, c2.pincode) as warehouse_pincode ")
                                ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                                // ->whereNotIn('pickups.status',['Cancel'])
                                ->whereNull('pickups.status')
                                ->where('pickups.pickup_order_id',$request->get('order_id'))
                                //->toSql();
                                //Log::channel('single')->info($order_details);
                                //dd($order_details);
                                ->get();
            return $order_details;
        }
        elseif(in_array($request->get('request_type'),$orderTypeNotIn))
        {
            DB::enableQueryLog();
            // $orderTypeNotIn = config('app.order_type');
            $order_details = DB::table('maintenance_orders')
                                ->join('order_details','maintenance_orders.order_details_id','=','order_details.id')
                                ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                                ->join('del_orders','maintenance_orders.order_id','=','del_orders.order_id')
                                ->join('products','maintenance_orders.product_id','=','products.id')
                                ->join('vendor_details','order_details.vendor_id','=','vendor_details.id')
                                ->select(
                                    'del_orders.DelDate as date',
                                    'products.product_name as product_name',
                                    'vendor_details.registered_name as vendor_name',
                                    'del_orders.TotalAmt as total_amount',
                                    'del_orders.*',
                                    'customer_details.*'
                                )
                                // ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                                ->where('maintenance_orders.order_id',$request->get('order_id'))
                                ->get();

            return $order_details;
        }
        elseif($request->get('request_type') == 'update-status')
        {
            $orderTypeNotIn = config('app.order_type');
            DelOrders::where('order_id',$request->get('hidden_order_id_set'))->update(['comment'=>DB::raw('concat(comment,",","'.$request->get('update_order_comment').'")'),'settled_on'=>date('Y-m-d H:i:s'),'settled_by'=>session('username'),'settlement_status'=>'Y']);
            $insertData = [
                'order_type'=>'DO',
                'key_id'=>$request->get('hidden_order_id_set'),
                'operation'=>'Update Settlement Status',
                'fields'=>'comment',
                'old_value'=>null,
                'new_value'=>$request->get('update_order_comment'),
                'updated_by'=>session('username')
                ];
            ActivityLog::insert($insertData);
            $insertData = [
                'order_type'=>'DO',
                'key_id'=>$request->get('hidden_order_id_set'),
                'operation'=>'Update Settlement Status',
                'fields'=>'settlement_status',
                'old_value'=>'N',
                'new_value'=>'Y',
                'updated_by'=>session('username')
                ];
            ActivityLog::insert($insertData);
            return redirect()->back();
        }
        elseif($request->get('request_type') == 'update-pay-details')
        {
            $old_order_details = DB::table('del_orders')->select('del_orders.*')->where('order_id',$request->get('hidden_order_id'))->get()->toArray();
            $od_type = 'DO';
            if($old_order_details[0]->deliverypickup == 'Delivery')
            {
                $od_type = 'DO';
            }
            else if($old_order_details[0]->deliverypickup == 'Collection')
            {
                $od_type = 'CO';
            }
            else if($old_order_details[0]->deliverypickup == 'Pick Up')
            {
                $od_type = 'PO';
            }
            else
            {
                $od_type = 'OT';
            }
            if($request->get('payment_mode') !="" || $request->get('payment_mode') !=null)
            {
                $old_PaymentMode = $old_order_details[0]->PaymentMode;
                if($old_PaymentMode != $request->get('payment_mode'))
                {
                    $insertData = [
                        'order_type'=>$od_type,
                        'key_id'=>$request->get('hidden_order_id'),
                        'operation'=>'Update Order Payment Details',
                        'fields'=>'PaymentMode',
                        'old_value'=>$old_PaymentMode,
                        'new_value'=>$request->get('payment_mode'),
                        'updated_by'=>session('username')
                        ];
                    ActivityLog::insert($insertData);
    
                    DelOrders::where('order_id',$request->get('hidden_order_id'))->update(['PaymentMode'=>$request->get('payment_mode')]);
                    // dd($old_order_details[0]->deliverypickup);
                    if($old_order_details[0]->deliverypickup == 'Collection'){
                        $renewals = DB::table('renewals')->where('renewals.collection_order_id',$request->get('hidden_order_id'))->get();
                        foreach($renewals as $key=>$renewal){
                            $update_data = array();
                            if($request->get('payment_mode') == 'Cash'){
                                $update_data['payment_mode'] = "Cash";
                                $update_data['cash_amount'] = $renewal->total_amt;
                                $update_data['online_amount'] = 0;
                            }
                            if($request->get('payment_mode') == 'Online'){
                                $update_data['payment_mode'] = "Online";
                                $update_data['cash_amount'] = 0;
                                $update_data['online_amount'] = $renewal->total_amt;
                            }
                            // dd($update_data);
                            DB::table('renewals')->where('id',$renewal->id)->update($update_data);
                        }
                    }
                }

            }
            $comment = $request->get('comment');
            $id = $request->get('hidden_order_id');
            // DelOrders::where('order_id',$request->get('hidden_order_id'))->update(['comment'=>$comment]);
            $timestamp = date("d M, h:i A");
            $desc = "[".$timestamp."]".$comment."\n";
            // dd($id,$desc);
            //DB::update("UPDATE del_orders SET comment = CONCAT('$desc',comment) WHERE order_id = $id ");
            if(DB::table('del_orders')->whereNotNull('comment')->where('order_id',$id)->exists()){
                DB::table('del_orders')->where('order_id',$id)->update([
                    'del_orders.comment' => DB::raw("CONCAT(comment, '".$desc."')")
                ]);
            }elseif($comment!=null){
                DB::table('del_orders')->where('order_id',$id)->update([
                    'del_orders.comment' =>$desc
                ]);
            }
            $payment_file_filePath = "";
            if($_FILES['payment_file']['name']!=null)
            {
                $payment_file = $_FILES['payment_file']['name'];
                //print_r($_FILES['shop_images']['name']);
                $targetDir = "assets/uploads/payment_images/";
                $fileName = basename($_FILES['payment_file']['name']);
                $targetFilePath = $targetDir . $fileName;
                $fileType =strtolower(pathinfo($targetFilePath,PATHINFO_EXTENSION));
                $new_file_name = $targetDir."".$request->get('hidden_order_id').".".$fileType;
                move_uploaded_file($_FILES["payment_file"]["tmp_name"], $new_file_name);    
                $payment_file_filePath = $request->get('hidden_order_id').".".$fileType;
                $old_payment_image = $old_order_details[0]->payment_image;
                $insertData = [
                    'order_type'=>$od_type,
                    'key_id'=>$request->get('hidden_order_id'),
                    'operation'=>'Update Order Payment Details',
                    'fields'=>'payment_image',
                    'old_value'=>$old_payment_image,
                    'new_value'=>$payment_file_filePath,
                    'updated_by'=>session('username')
                    ];
                ActivityLog::insert($insertData);
                
                DelOrders::where('order_id',$request->get('hidden_order_id'))->update(['payment_image'=>$payment_file_filePath]);
            }
            $receipt_file_filePath = "";
            if($_FILES['receipt_file']['name']!=null)
            {
                $receipt_file = $_FILES['receipt_file']['name'];
                //print_r($_FILES['shop_images']['name']);
                $targetDir = "../../api/feedback/receipt_images/";
                // dd($targetDir);
                $fileName = basename($_FILES['receipt_file']['name']);
                $targetFilePath = $targetDir . $fileName;
                $fileType =strtolower(pathinfo($targetFilePath,PATHINFO_EXTENSION));
                $new_file_name = $targetDir."".$request->get('hidden_order_id').".".$fileType;
               // dd("fileType: ".$new_file_name);
                move_uploaded_file($_FILES["receipt_file"]["tmp_name"], $new_file_name);
                $receipt_file_filePath = "13.233.36.52/api/feedback/receipt_images/".$request->get('hidden_order_id').".".$fileType;
                // dd($receipt_file_filePath);
                $old_del_receipt_image = $old_order_details[0]->del_receipt_image;

                $insertData = [
                    'order_type'=>$od_type,
                    'key_id'=>$request->get('hidden_order_id'),
                    'operation'=>'Update Order Payment Details',
                    'fields'=>'del_receipt_image',
                    'old_value'=>$old_del_receipt_image,
                    'new_value'=>$receipt_file_filePath,
                    'updated_by'=>session('username')
                    ];
                ActivityLog::insert($insertData);

                DelOrders::where('order_id',$request->get('hidden_order_id'))->update(['del_receipt_image'=>$receipt_file_filePath]);
            }
            if($request->get('update_ref_id') !="" || $request->get('update_ref_id') !=null)
            {
                $old_reference_id = $old_order_details[0]->reference_id;
                $insertData = [
                    'order_type'=>$od_type,
                    'key_id'=>$request->get('hidden_order_id'),
                    'operation'=>'Update Order Payment Details',
                    'fields'=>'reference_id',
                    'old_value'=>$old_reference_id,
                    'new_value'=>$request->get('update_ref_id'),
                    'updated_by'=>session('username')
                    ];
                ActivityLog::insert($insertData);

                DelOrders::where('order_id',$request->get('hidden_order_id'))->update(['reference_id'=>$request->get('update_ref_id')]);
            }
            if(!empty($request->get('floor_no')) || !empty($request->get('labour_charges')))
            {
                $upLabourData = [
                    'floor_no'=>($request->get('floor_no'))?$request->get('floor_no'):0,
                    'labour_charges'=>($request->get('labour_charges'))?$request->get('labour_charges'):0
                ];
                // $orderTypeNotIn = config('app.order_type');
                //$oldLabour = DelOrders::where('order_id',$request->get('hidden_order_id'))->get(array_keys($upLabourData));
                // dd($upLabourData);
                $oldLabour = DelOrders::where('order_id',$request->get('hidden_order_id'))->get(array_keys($upLabourData));

                foreach ($upLabourData as $key => $value) {
                    $insertLogData = [
                        'order_type'=>$od_type,
                        'key_id'=>$request->get('hidden_order_id'),
                        'operation'=>'Labour Charges updated',
                        'fields'=>$key,
                        'old_value'=>$oldLabour[0]->$key,
                        'new_value'=>$value,
                        'updated_by'=>session('username')
                    ];
                    ActivityLog::insert($insertLogData);
                }
                // $orderTypeNotIn = config('app.order_type');
                //DelOrders::where('order_id',$request->get('hidden_order_id'))->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)->update($upLabourData);
                DelOrders::where('order_id',$request->get('hidden_order_id'))->update($upLabourData);
            }
            if($request->has('order_expense_pay') && $request->has('vendor_charges_pay')){
                $expense_update = [
                    "order_expense" => $request->get('order_expense_pay'),
                    "vendor_charges" => $request->get('vendor_charges_pay')
                ];
                Db::table('del_orders')->where('order_id',$request->get('hidden_order_id'))->update($expense_update);
            }
            if($request->get('submit') == 'submit-settle')
            {
                if(!empty($request->get('hidden_invoice_id'))){
                    DelOrders::where('order_id',$request->get('hidden_order_id'))->update(['invoice_no'=>$request->get('hidden_invoice_id')]);
                }
                $orderTypeNotIn = config('app.order_type');
                DelOrders::where('order_id',$request->get('hidden_order_id'))->update(['settled_on'=>date('Y-m-d H:i:s'),'settled_by'=>session('username'),'settlement_status'=>'Y']);
                $insertData = [
                    'order_type'=>'DO',
                    'key_id'=>$request->get('hidden_order_id'),
                    'operation'=>'Update Settlement Status',
                    'fields'=>'settlement_status',
                    'old_value'=>'N',
                    'new_value'=>'Y',
                    'updated_by'=>session('username')
                    ];
                ActivityLog::insert($insertData);
            }
            // DelOrders::where('order_id',$request->get('hidden_order_id'))->update(['comment'=>$request->get('update_order_comment'),'settlement_status'=>'Y']);
            // $insertData = [
            //     'order_type'=>'DO',
            //     'key_id'=>$request->get('hidden_order_id'),
            //     'operation'=>'Update Order Payment Details',
            //     'fields'=>'receipt_file',
            //     'old_value'=>'N',
            //     'new_value'=>'Y',
            //     'updated_by'=>session('username')
            //     ];
            // ActivityLog::insert($insertData);
            return redirect()->back();
        }
        if($request->get('request_type') == 'adjusted-data')
        {
            if($request->get('order_type') == 'Delivery')
            {
                if($request->get('adjust_state') == 'out')
                {
                    // return $request->all();
                    $order_details = DB::table('order_details')->join('products','products.id','=','order_details.product_id')->select('order_details.*','products.product_name')->where('order_details.order_id',$request->get('order_id'))->whereNotIn('order_details.current_status',['Cancel'])->get();
                    foreach($order_details as $key=>$value)
                    {
                        // return $value->id;
                        if(DB::table('adjustment_table')
                        ->join('del_orders','del_orders.order_id','=','adjustment_table.order_id')
                        ->where('adjustment_table.flag','A')
                        ->where('adjustment_table.order_details_id',$value->id)->exists())
                        {
                            
                            if(DB::table('adjustment_table')
                                ->join('del_orders','del_orders.order_id','=','adjustment_table.order_id')
                                ->where('adjustment_table.order_details_id',$value->id)
                                ->where('adjustment_table.flag','A')
                                ->first()->deliverypickup == 'Delivery')
                            {
                                $records = DB::table('adjustment_table')
                                ->join('order_details','order_details.id','=','adjustment_table.adjusted_order_details_id')
                                ->join('products','products.id','=','order_details.product_id')
                                ->select('order_details.*','adjustment_table.id as adjustment_id','adjustment_table.adjusted_amount','adjustment_table.fromtype','adjustment_table.intype','products.product_name')
                                ->where('adjustment_table.order_details_id',$value->id)
                                ->where('adjustment_table.flag','A')
                                ->get();
                                foreach($records as $k=>$v)
                                {
                                    $records[$k]->creation_date = date('d-M-Y',strtotime($v->creation_date));
                                }
                                // $order_details[$key]->creation_date = date('d-M-Y',strtotime($value->creation_date));
                                $order_details[$key]->adjustment = $records;
                            }
                            else if(DB::table('adjustment_table')
                            ->join('del_orders','del_orders.order_id','=','adjustment_table.order_id')
                            ->where('adjustment_table.order_details_id',$value->id)
                            ->where('adjustment_table.flag','A')
                            ->first()->deliverypickup == 'Repair')
                            {
                                $records = DB::table('adjustment_table')
                                ->join('maintenance_orders','maintenance_orders.id','=','adjustment_table.adjusted_order_details_id')
                                ->join('order_details','order_details.id','=','maintenance_orders.order_details_id')
                                ->join('products','products.id','=','order_details.product_id')
                                ->select('order_details.*','adjustment_table.id as adjustment_id','adjustment_table.adjusted_amount','adjustment_table.fromtype','adjustment_table.intype','products.product_name','maintenance_orders.order_id')
                                ->where('adjustment_table.order_details_id',$value->id)
                                ->where('adjustment_table.flag','A')
                                ->get();
                                foreach($records as $k=>$v)
                                {
                                    $records[$k]->creation_date = date('d-M-Y',strtotime($v->creation_date));
                                }
                                // $order_details[$key]->creation_date = date('d-M-Y',strtotime($value->creation_date));
                                $order_details[$key]->adjustment = $records;
                            }
                            else
                            {
                                $order_id_temp = DB::table('adjustment_table')->join('del_orders','del_orders.order_id','=','adjustment_table.order_id')->where('adjustment_table.order_details_id',$value->id)->where('adjustment_table.flag','A')->first()->order_id;
                                $order_details = DB::table('renewals')->join('order_details','order_details.id','=','renewals.order_details_id')->join('products','products.id','=','renewals.product_id')->select('renewals.*','order_details.unique_id','products.product_name')->where('renewals.collection_order_id',$order_id_temp)->get();
                                foreach($order_details as $key=>$value)
                                {
                                    $records = DB::table('adjustment_table')
                                                    ->join('renewals','renewals.id','=','adjustment_table.adjusted_order_details_id')
                                                    ->join('order_details','order_details.id','=','renewals.order_details_id')
                                                    ->join('products','products.id','=','renewals.product_id')
                                                    ->select('order_details.*','adjustment_table.id as adjustment_id','adjustment_table.adjusted_amount','adjustment_table.fromtype','adjustment_table.intype','products.product_name')
                                                    ->where('adjustment_table.adjusted_order_details_id',$value->id)
                                                    ->where('adjustment_table.flag','A')
                                                    ->get();
                                    foreach($records as $k=>$v)
                                    {
                                        $records[$k]->creation_date = date('d-M-Y',strtotime($v->creation_date));
                                    }
                                    // $order_details[$key]->creation_date = date('d-M-Y',strtotime($value->creation_date));
                                    $order_details[$key]->adjustment = $records;
                                }
                            }

                        }
                        else {
                            continue;
                        }
                    }
                }
                else
                {
                    $order_details = DB::table('order_details')->join('products','products.id','=','order_details.product_id')->select('order_details.*','products.product_name')->where('order_details.order_id',$request->get('order_id'))->get();
                    foreach($order_details as $key=>$value)
                    {
                        $records = DB::table('adjustment_table')
                                        ->join('order_details','order_details.id','=','adjustment_table.order_details_id')
                                        ->join('products','products.id','=','order_details.product_id')
                                        ->select('order_details.*','adjustment_table.id as adjustment_id','adjustment_table.adjusted_amount','adjustment_table.fromtype','adjustment_table.intype','products.product_name')
                                        ->where('adjustment_table.adjusted_order_details_id',$value->id)
                                        ->where('adjustment_table.flag','A')
                                        ->get();
                        foreach($records as $k=>$v)
                        {
                            $records[$k]->creation_date = date('d-M-Y',strtotime($v->creation_date));
                        }
                        // $order_details[$key]->creation_date = date('d-M-Y',strtotime($value->creation_date));
                        $order_details[$key]->adjustment = $records;
                    }
                }
                // $order_details = DB::table('order_details')->join('products','products.id','=','order_details.product_id')->select('order_details.*','products.product_name')->where('order_details.order_id',$request->get('order_id'))->get();
                // foreach($order_details as $key=>$value)
                // {
                //     $records = DB::table('adjustment_table')
                //                     ->join('order_details','order_details.id','=','adjustment_table.adjusted_order_details_id')
                //                     ->join('products','products.id','=','order_details.product_id')
                //                     ->select('order_details.*','adjustment_table.adjusted_amount','products.product_name')
                //                     ->where('adjustment_table.order_details_id',$value->id)
                //                     ->where('adjustment_table.flag','A')
                //                     ->get();
                //     foreach($records as $k=>$v)
                //     {
                //         $records[$k]->creation_date = date('d-M-Y',strtotime($v->creation_date));
                //     }
                //     // $order_details[$key]->creation_date = date('d-M-Y',strtotime($value->creation_date));
                //     $order_details[$key]->adjustment = $records;
                // }
            }
            else if($request->get('order_type') == 'Collection')
            {
                
                $order_details = DB::table('renewals')->join('order_details','order_details.id','=','renewals.order_details_id')->join('products','products.id','=','renewals.product_id')->select('renewals.*','order_details.unique_id','products.product_name')->where('renewals.collection_order_id',$request->get('order_id'))->get();
                foreach($order_details as $key=>$value)
                {
                    $records = DB::table('adjustment_table')
                                    ->join('renewals','renewals.id','=','adjustment_table.adjusted_order_details_id')
                                    ->join('order_details','order_details.id','=','renewals.order_details_id')
                                    ->join('products','products.id','=','adjustment_table.product_id')
                                    ->select('order_details.*','adjustment_table.id as adjustment_id','adjustment_table.adjusted_amount','adjustment_table.fromtype','adjustment_table.intype','products.product_name')
                                    ->where('adjustment_table.adjusted_order_details_id',$value->id)
                                    ->where('adjustment_table.flag','A')
                                    ->get();
                                    // return $records;
                    foreach($records as $k=>$v)
                    {
                        $records[$k]->creation_date = date('d-M-Y',strtotime($v->creation_date));
                    }
                    // $order_details[$key]->creation_date = date('d-M-Y',strtotime($value->creation_date));
                    $order_details[$key]->adjustment = $records;
                }
            }
            else if($request->get('order_type') == 'Pick Up')
            {
                $order_details = DB::table('pickups')->join('order_details','order_details.id','=','pickups.order_details_id')->join('products','products.id','=','renewals.product_id')->select('pickups.*','order_details.unique_id','products.product_name')->where('pickups.order_id',$request->get('order_id'))->get();
                foreach($order_details as $key=>$value)
                {
                    $records = DB::table('adjustment_table')
                                    ->join('pickups','pickups.id','=','adjustment_table.adjusted_order_details_id')
                                    ->join('order_details','order_details.id','=','pickups.order_details_id')
                                    ->join('products','products.id','=','pickups.product_id')
                                    ->select('order_details.*','adjustment_table.id as adjustment_id','adjustment_table.adjusted_amount','products.product_name')
                                    ->where('adjustment_table.order_details_id',$value->id)
                                    ->where('adjustment_table.flag','A')
                                    ->get();
                                    // return $records;
                    foreach($records as $k=>$v)
                    {
                        $records[$k]->creation_date = date('d-M-Y',strtotime($v->creation_date));
                    }
                    // $order_details[$key]->creation_date = date('d-M-Y',strtotime($value->creation_date));
                    $order_details[$key]->adjustment = $records;
                }
            }
            else if($request->get('order_type') == 'Repair')
            {
                
                $order_details = DB::table('maintenance_orders')->join('order_details','order_details.id','=','maintenance_orders.order_details_id')->join('products','products.id','=','maintenance_orders.product_id')->select('maintenance_orders.*','order_details.unique_id','products.product_name')->where('maintenance_orders.order_id',$request->get('order_id'))->get();
                foreach($order_details as $key=>$value)
                {
                    $records = DB::table('adjustment_table')
                                    ->join('maintenance_orders','maintenance_orders.id','=','adjustment_table.adjusted_order_details_id')
                                    ->join('order_details','order_details.id','=','maintenance_orders.order_details_id')
                                    ->join('products','products.id','=','adjustment_table.product_id')
                                    ->select('order_details.*','adjustment_table.id as adjustment_id','adjustment_table.adjusted_amount','adjustment_table.fromtype','adjustment_table.intype','products.product_name')
                                    ->where('adjustment_table.adjusted_order_details_id',$value->id)
                                    ->where('adjustment_table.flag','A')
                                    ->get();
                                    // return $records;
                    foreach($records as $k=>$v)
                    {
                        $records[$k]->creation_date = date('d-M-Y',strtotime($v->creation_date));
                    }
                    // $order_details[$key]->creation_date = date('d-M-Y',strtotime($value->creation_date));
                    $order_details[$key]->adjustment = $records;
                }
            }
            return $order_details;
        }
        if($request->get('request_type_inv') == 'update-invoice-id')
        {
            $order_details = DB::table('del_orders')->where('order_id',$request->get('hidden_order_id_inv'))->first();
            DB::table('del_orders')->where('order_id',$request->get('hidden_order_id_inv'))->update(['invoice_no'=>$request->get('invoice_id')]);
            $od_type = 'DO';
            if($order_details->deliverypickup == 'Delivery')
            {
                $od_type = 'DO';
            }
            else if($order_details->deliverypickup == 'Collection')
            {
                $od_type = 'CO';
            }
            else if($order_details->deliverypickup == 'Pick Up')
            {
                $od_type = 'PO';
            }
            else
            {
                $od_type = 'OT';
            }
            $insertData = [
                'order_type'=>$od_type,
                'key_id'=>$request->get('hidden_order_id_inv'),
                'operation'=>'Update Order Invoice ID',
                'fields'=>'invoice_no',
                'old_value'=>$order_details->invoice_no,
                'new_value'=>$request->get('invoice_id'),
                'updated_by'=>session('username')
                ];
            ActivityLog::insert($insertData);
            return redirect()->back();
        }
        if($request->get('request_type') == 'get-closed-order-details')
        {
            $details = DB::table('del_orders')->join('order_details','order_details.order_id','=','del_orders.order_id')->select('order_details.transport as total')->where('del_orders.order_id',$request->get('order_id'))->get();
            $total = $details->pluck('total')->sum();
            return $total;
        }
        if($request->get('request_type') == 'update-closed-order-transport')
        {
            // dd($request->all());
            $first_order_id = DB::table('order_details')->select('id','transport')->where('order_id',$request->get('update_closed_order_order_id'))->first();
            // dd($first_order_id);
            DB::table('order_details')->where('id',$first_order_id->id)->update(['transport'=>$request->get('transport_update_cost')]);
            $details = DB::table('del_orders')->join('order_details','order_details.order_id','=','del_orders.order_id')->select('order_details.transport as total')->where('del_orders.order_id',$request->get('update_closed_order_order_id'))->get();
            $total = $details->pluck('total')->sum();
            // dd($total);
            DB::table('del_orders')->where('order_id',$request->get('update_closed_order_order_id'))->update(['TotalAmt'=>$total]);
            DB::table('cr_dr_note')->insert([
                'order_id'=>$request->get('update_closed_order_order_id'),
                'order_details_id'=>$first_order_id->id,
                'crdrtype'=>'Dr',
                'intype'=>'T',
                'amount'=>($first_order_id->transport - $request->get('transport_update_cost')),
                'createdby'=>session('username')
            ]);
            return redirect()->back()->with('message','Order Updated Successfully!');
        }
        if($request->get('request_type') == 'fetchOrderDetails'){
            $orderDetails = DB::table('del_orders')->where('order_id',$request->get('order_id'))->first();
            $orderDetails->delBoys = DB::table('delusers')->where('role','user')->orderBy('username','ASC')->get();
            return json_encode($orderDetails);
        }
    }

    public function updatePickup(Request $request){
        // dd($request->all());
        DB::beginTransaction();
        try{
            foreach($request->get('pickupIds') as $key=>$value){
                DB::table('pickups')->where('id',$value)->update(['transport'=>$request->get('transportTaken')[$key]]);
            }
            $details = DB::table('del_orders')
            ->join('pickups','pickups.pickup_order_id','=','del_orders.order_id')
            ->join('order_details','order_details.id','=','pickups.order_details_id')
            ->select('order_details.id','order_details.product_deposite','pickups.transport')
            ->where('del_orders.order_id',$request->get('update_pickup_order_id'))
            ->get();
            foreach($details as $key=>$value){
                $details[$key]->product_deposite = RenewalPickupController::fetchCrDrData($value->id,'D');
            }
            // dd($details);
            $totalDeposit = $details->pluck('product_deposite')->sum();
            $totalTransport = $details->pluck('transport')->sum();
            // dd($totalDeposit,$totalTransport);
            Db::table('del_orders')->where('order_id',$request->get('update_pickup_order_id'))->update(['TotalAmt'=>$totalDeposit + $totalTransport]);
            DB::commit();
            return redirect()->back()->with('message','Pickup Updated Successfully!');
        }catch(Exception $ex){
            DB::rollback();
            return redirect()->back()->with('error',$ex->getMessage());
        }
    }

    public function settledUnsettledOrdersCron()
    {
        $today = Carbon::now();
        if($today->dayOfWeek == 1){
            $form_min_date = date('d-m-Y',strtotime("-9 days",strtotime($today)));
            $form_max_date = date('d-m-Y',strtotime("-3 days",strtotime($today)));
        }else{
            $form_min_date = date('d-m-Y',strtotime("-2 days"));
            $form_max_date = date('d-m-Y',strtotime("-1 days"));
        }
        // $form_min_date = date('d-m-Y',strtotime("-2 days"));
        // $form_max_date = date('d-m-Y',strtotime("-1 days"));
        
        // $from_date = date('Y-m-d',strtotime("-1 days")); 
        // $end_date = date('Y-m-d');
        // DB::enableQueryLog();
        $orderTypeNotIn = config('app.order_type');
        $details_count = DB::table('del_orders')
                                ->select(
                                    'del_orders.order_id as order_id',
                                    'del_orders.deliverypickup as order_type',
                                    'del_orders.status as status',
                                    'del_orders.shipping_first_name as customer_name',
                                    'del_orders.mobileno as mobile_number',
                                    'del_orders.location as location',
                                    'del_orders.DelDate as date',
                                    'del_orders.line_item_1 as equipments',
                                    'del_orders.DelAssignedTo as assigned_to',
                                    'del_orders.TotalAmt as assigned_total_amount',
                                    'del_orders.PaymentMode as assigned_payment_mode',
                                    'del_orders.cash as assigned_cash',
                                    'del_orders.online as assigned_online',
                                    'del_orders.del_payment_mode as received_payment_mode',
                                    'del_orders.del_total_amount as received_total_amount',
                                    'del_orders.del_cash_amount as received_cash',
                                    'del_orders.del_online_amount as received_online',
                                    'del_orders.payment_image as payment_image',
                                    'del_orders.reference_id as reference_id',
                                    'del_orders.del_receipt_image as receipt_image',
                                    'del_orders.comment as comment',
                                    'del_orders.settlement_status as settlement_status',
                                )
                                ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                                ->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$form_min_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$form_max_date','%d-%m-%Y'))")])
                                ->orderBy('del_orders.order_id','Asc')
                                ->get();
        // dd($details_count);

        $temp_delivery_not_settled_count = 0;
        $temp_delivery_settled_count = 0;
        $temp_pickup_not_settled_count = 0;
        $temp_pickup_settled_count = 0;
        $temp_collection_not_settled_count = 0;
        $temp_collection_settled_count = 0;

        $temp_delivery_online_amt = 0;
        $temp_delivery_cash_amt = 0;
        $temp_pickup_online_amt = 0;
        $temp_pickup_cash_amt = 0;
        $temp_collection_online_amt = 0;
        $temp_collection_cash_amt = 0;
        $count_array = array();
        $amount_array = array();
        foreach($details_count as $key => $value)
        {
            if($value->order_type == 'Delivery')
            {
                if($value->assigned_payment_mode == "Online")
                {
                    $temp_delivery_online_amt = $temp_delivery_online_amt + $value->assigned_total_amount;
                }
                else if($value->assigned_payment_mode == "Cash")
                {
                    $temp_delivery_cash_amt = $temp_delivery_cash_amt + $value->assigned_total_amount;   
                }
                else if($value->assigned_payment_mode == "Both")
                {
                    $temp_delivery_cash_amt = $temp_delivery_cash_amt + $value->assigned_total_amount;
                    $temp_delivery_online_amt = $temp_delivery_online_amt + $value->assigned_total_amount;
                }
                if($value->settlement_status == 'N')
                {
                    $temp_delivery_not_settled_count++;
                }
                elseif($value->settlement_status == 'Y')
                {
                    $temp_delivery_settled_count++;
                }
            }

            if($value->order_type == 'Collection')
            {
                // $temp_collection_cash_amt = $temp_collection_cash_amt + $value->assigned_cash;
                // $temp_collection_online_amt = $temp_collection_online_amt + $value->assigned_online;
                if($value->assigned_payment_mode == "Online")
                {
                    $temp_collection_online_amt = $temp_collection_online_amt + $value->assigned_total_amount;
                }
                else if($value->assigned_payment_mode == "Cash")
                {
                    $temp_collection_cash_amt = $temp_collection_cash_amt + $value->assigned_total_amount;   
                }
                else if($value->assigned_payment_mode == "Both")
                {
                    $temp_collection_cash_amt = $temp_collection_cash_amt + $value->assigned_total_amount;
                    $temp_collection_online_amt = $temp_collection_online_amt + $value->assigned_total_amount;
                }
                if($value->settlement_status == 'N')
                {
                    $temp_collection_not_settled_count++;
                }
                elseif($value->settlement_status == 'Y')
                {
                    $temp_collection_settled_count++;
                }
            }
            if($value->order_type == 'Pick Up')
            {
                // $temp_pickup_cash_amt = $temp_pickup_cash_amt + $value->assigned_cash;
                // $temp_pickup_online_amt = $temp_pickup_online_amt + $value->assigned_online;
                if($value->assigned_payment_mode == "Online")
                {
                    $temp_pickup_online_amt = $temp_pickup_online_amt + $value->assigned_total_amount;
                }
                else if($value->assigned_payment_mode == "Cash")
                {
                    $temp_pickup_cash_amt = $temp_pickup_cash_amt + $value->assigned_total_amount;   
                }
                else if($value->assigned_payment_mode == "Both")
                {
                    $temp_pickup_cash_amt = $temp_pickup_cash_amt + $value->assigned_total_amount;
                    $temp_pickup_online_amt = $temp_pickup_online_amt + $value->assigned_total_amount;
                }
                if($value->settlement_status == 'N')
                {
                    $temp_pickup_not_settled_count++;
                }
                elseif($value->settlement_status == 'Y')
                {
                    $temp_pickup_settled_count++;
                }
            }
        }        
        array_push($count_array,$temp_delivery_not_settled_count);
        array_push($count_array,$temp_delivery_settled_count);
        array_push($count_array,$temp_collection_not_settled_count);
        array_push($count_array,$temp_collection_settled_count);
        array_push($count_array,$temp_pickup_not_settled_count);
        array_push($count_array,$temp_pickup_settled_count);

        array_push($amount_array,$temp_delivery_online_amt);
        array_push($amount_array,$temp_delivery_cash_amt);
        array_push($amount_array,$temp_collection_online_amt);
        array_push($amount_array,$temp_collection_cash_amt);
        array_push($amount_array,$temp_pickup_online_amt);
        array_push($amount_array,$temp_pickup_cash_amt);
        // info($details_count);
        // info($count_array);
        // info($amount_array);
        $jsonDecoded = $details_count;
        $jsonDecoded1 = $count_array;
        $jsonDecoded2 = $amount_array;
        $data = array();
        $data = array('form_min_date'=>$form_min_date,
        'form_max_date'=>$form_max_date);
        // ob_end_clean(); // this
        // ob_start(); // and this
        // ob_end_clean(); // this
        // ob_start(); // and this
        // Excel::download(new PendingPayment($jsonDecoded,$jsonDecoded1,$jsonDecoded2), 'Pending Payments'.date('Y-m-d').'.xlsx');
        // // dd($file_path);

        // $path = '/var/www/html/devweb/eflow/storage/framework/cache/laravel-excel/Pending Payments'.date('Y-m-d').'.xlsx';
        Excel::store(new PendingPayment($jsonDecoded,$jsonDecoded1,$jsonDecoded2),'Pending Payments'.date('Y-m-d').'.xlsx','public',\Maatwebsite\Excel\Excel::XLSX);
        sleep(1);
        $xlsx_file = file_get_contents('storage/app/public/Pending Payments'.date('Y-m-d').'.xlsx');
        
        Mail::send('PendingOrders/pendingOrdersMail', $data, function($message)use($xlsx_file,$form_min_date,$form_max_date) 
        {                
            $email_id = config('app.ceo_email');
            $cto_email_id = config('app.cto_email');
            $from_email_id = config('app.default_from_email');
            // $email_id = 'rahulbhanushali@quali55care.com';
            $message->to($email_id, 'CEO')->subject('Not Settled Orders Report for ('.$form_min_date.' to '.$form_max_date.')');
            $message->to($cto_email_id, 'CTO')->subject('Not Settled Orders Report for ('.$form_min_date.' to '.$form_max_date.')');
            $message->from($from_email_id, 'Quali55Care');

            $message->attachData($xlsx_file,'Pending Payments'.date('Y-m-d').'.xlsx');
        });
        //print_r($jsonDecoded);
        // ob_end_clean(); // this
        // ob_start(); // and this
        // Excel::create(new MisExport($jsonDecoded,$jsonDecoded1,$jsonDecoded2), 'Pending Payments'.date('Y-m-d H:i:s').'.xlsx');

    }



    public function editRenewal(Request $request)
    {
        $delboys = DB::table('delusers')->where('status','Active')->where('role','user')->get();
        $orderId = $request->get('order_id');
        if(!empty($orderId)){
            $orderTypeNotIn = config('app.order_type');
            $productData = DB::table('renewals')
                            ->join('order_details','renewals.order_details_id','=','order_details.id')
                            // ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                            ->join('products','order_details.product_id','=','products.id')
                            ->join('vendor_details','order_details.vendor_id','=','vendor_details.id')
                            ->join('del_orders','renewals.collection_order_id','=','del_orders.order_id')
                            ->select('order_details.*','renewals.id as renewal_id','vendor_details.registered_name','del_orders.PaymentMode as payment_mode','del_orders.DelAssignedTo','del_orders.DelDate','renewals.start_date as ren_start_date','renewals.end_date as ren_end_date','order_details.id as order_details_id','products.product_name','del_orders.status as delivery_status','renewals.collection_order_id','renewals.discount_amt')
                            ->where('renewals.collection_order_id',$orderId)
                            ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                            ->whereNotIn('renewals.status',['Cancel'])
                            ->get();
            if(isset($productData[0]))
            {
                $customerDetails = DB::table('customer_details')->where('cust_id',$productData[0]->customer_id)->first();
            
                $orderMonthData = [];          
                $orderNotDelivered = [];
                $orderDetails = $productData->groupBy('id');
                // dd($productData);
                $rows = array();
                // dd($orderDetails);
                $raw_id = array();
                foreach($productData as $key=>$value)
                {
                    if(DB::table('adjustment_table')->where('adjusted_order_details_id',$value->renewal_id)->where('adjustment_table.flag','A')->exists())
                    {
                        $records = DB::table('adjustment_table')->select('adjusted_amount')->where('adjusted_order_details_id',$value->renewal_id)->where('fromtype','D')->where('adjustment_table.flag','A')->get();
                        // dd($records);
                        $sum = $records->pluck('adjusted_amount')->sum();
                        // dd($sum);
                        $productData[$key]->adjusted_deposit = $sum;
                    }
                    else
                    {
                        $productData[$key]->adjusted_deposit = 0;
                    }
                    // dd($key);
                    if(in_array($value->id,$raw_id))
                    {
                        $index = array_search($value->id,$raw_id);
                        // $rows[$index] = $value;
                        $rows[$index]->ren_end_date = $value->ren_end_date;
                        $orderMonthData[$index]['total_rent'] = $orderMonthData[$index]['total_rent'] + $value->product_rent - $value->discount_amt;
                        $orderMonthData[$index]['month_count'] = $orderMonthData[$index]['month_count']+1;
                        $orderMonthData[$index]['discount_amt'] = $orderMonthData[$index]['discount_amt']+ $value->discount_amt;
                        $orderMonthData[$index]['next_renew_date'] = Carbon::parse($value->ren_end_date)->toDateString();
                    }
                    else{
                        $index = count($rows);
                        array_push($raw_id,$value->id);
                        array_push($rows,$value);
    
                        $orderMonthData[$index]['month_count'] = 1;
                        $orderMonthData[$index]['total_rent'] = $value->product_rent - $value->discount_amt;
                        $orderMonthData[$index]['discount_amt'] = $value->discount_amt;
                        $orderMonthData[$index]['next_renew_date'] = Carbon::parse($value->ren_end_date)->toDateString();
                    }
                }
                return view('RenewalPickup.edit-order-renew',compact('customerDetails','rows','orderMonthData','orderNotDelivered','delboys'));
            }
            else
            {
                return redirect()->to('pending_online_renew');
            }
        }else
        {
            return redirect()->back()->with('message_delete','Please select order');
        }
    }


    public function updateRenewal(Request $request)
    {
        DB::beginTransaction();
        try{
            // dd($request->all());
            $order_id = $request->get('order_id');
            foreach($request->get('order_details_id') as $key=>$id)
            {
                $count = DB::table('renewals')->where('collection_order_id',$request->get('order_id'))->where('order_details_id',$id)->whereNotIn('renewals.status',['Cancel'])->get()->count();
                // dd($count);
                if($count<$request->get('payment_months')[$key])
                {
                    // Months Increased.
                    $increased_by = $request->get('payment_months')[$key] - $count;
                    $last_row = DB::table('renewals')->where('collection_order_id',$request->get('order_id'))->where('order_details_id',$id)->whereNotIn('renewals.status',['Cancel'])->orderBy('id','DESC')->first();
                    $start_date = $last_row->end_date;
                    // $end_date = date('Y-m-d',strtotime("+1 month",strtotime($start_date)));
                    $order_row_details = DB::table('order_details')->select('billing_period','billing_unit')->where('id',$id)->first();
                    if($order_row_details->billing_unit == 'Week'){
                        $end_date = Carbon::parse($start_date)->addWeeks(1)->toDateString();
                    }else if($order_row_details->billing_unit == 'Half Month'){
                        $order_row_details->billing_period = $order_row_details->billing_period * 2;
                        $end_date = Carbon::parse($start_date)->addWeeks(2)->toDateString();
                    }else if($order_row_details->billing_unit == 'Days'){
                        $end_date = Carbon::parse($start_date)->addDays(1)->toDateString();
                    }else{
                        $end_date = Carbon::parse($start_date)->addMonths(1)->toDateString();
                    }
                    $product_details = "";
                    for($i=0; $i<$increased_by; $i++)
                    {
                        $insertRow = [
                            'collection_order_id'=>$last_row->collection_order_id,
                            'order_id'=>$last_row->order_id,
                            'order_details_id'=>$last_row->order_details_id,
                            'lead_id'=>$last_row->lead_id,
                            'vendor_id'=>$last_row->vendor_id,
                            'product_id'=>$last_row->product_id,
                            'start_date'=>$start_date,
                            'end_date'=>$end_date,
                            'payment_mode'=>$last_row->payment_mode,
                            'cash_amount'=>$last_row->cash_amount,
                            'online_amount'=>$last_row->online_amount,
                            'discount_amt'=>$last_row->discount_amt,
                            'adjusted_deposit'=>$last_row->adjusted_deposit,
                            'total_amt'=>$last_row->total_amt,
                            'online_method'=>$last_row->online_method,
                            'status'=>$last_row->status,
                            'payment_status'=>$last_row->payment_status,
                            'reference_id'=>$last_row->reference_id,
                            'comment'=>$last_row->comment,
                            'image_path'=>$last_row->image_path,
                            'created_by'=>session('username')
                        ];
                        $insert_renewal_id = DB::table('renewals')->insertGetId($insertRow);
                        ActivityLog::insert([
                            'order_type'=>'CO',
                            'key_id'=>$insert_renewal_id,
                            'operation'=>'Update Collection Order period increased',
                            'fields'=>'Renewal Date',
                            'old_value'=>$start_date,
                            'new_value'=>$end_date,
                            'updated_by'=>session('username')
                        ]);
                        $product_details .= ' Period Increased of product from '.$start_date.' to '.$end_date;
                        $start_date = $end_date;
                        // $end_date = date('Y-m-d',strtotime("+1 month",strtotime($start_date)));
                        if($order_row_details->billing_unit == 'Week'){
                            $end_date = Carbon::parse($start_date)->addWeeks(1)->toDateString();
                        }else if($order_row_details->billing_unit == 'Half Month'){
                            $order_row_details->billing_period = $order_row_details->billing_period * 2;
                            $end_date = Carbon::parse($start_date)->addWeeks(2)->toDateString();
                        }else if($order_row_details->billing_unit == 'Days'){
                            $end_date = Carbon::parse($start_date)->addDays(1)->toDateString();
                        }else{
                            $end_date = Carbon::parse($start_date)->addMonths(1)->toDateString();
                        }
                    }
                    $collection_order_old_details = DB::table('del_orders')->where('order_id',$request->get('order_id'))->first();
                    $this->order_update_wp_alert('Collection Updated',$last_row->collection_order_id,$collection_order_old_details->shipping_first_name,$collection_order_old_details->DelDate,session('username'),$product_details);
                    // dd($key);
                }
                if($count>$request->get('payment_months')[$key])
                {
                    $product_details = "";
                    $last_row = DB::table('renewals')->where('collection_order_id',$request->get('order_id'))->where('order_details_id',$id)->whereNotIn('renewals.status',['Cancel'])->orderBy('id','DESC')->first();
                    // Month Decreased.
                    $decreased_by = $count - $request->get('payment_months')[$key];
                    $decreased_rows = DB::table('renewals')->where('collection_order_id',$request->get('order_id'))->where('order_details_id',$id)->whereNotIn('renewals.status',['Cancel'])->orderBy('id','DESC')->limit($decreased_by);
                    $decreased_id = $decreased_rows->pluck('id');
                    // dd($decreased_id);
                    DB::table('renewals')->whereIn('id',$decreased_id)->update(['status'=>'Cancel']);
                    foreach($decreased_id as $id){
                        ActivityLog::insert([
                            'order_type'=>'CO',
                            'key_id'=>$id,
                            'operation'=>'Update Collection Order period decreased',
                            'fields'=>'status',
                            'old_value'=>'Pending',
                            'new_value'=>'Cancel',
                            'updated_by'=>session('username')
                        ]);
                    }
                    $product_details .= ' Period Decreased of product by: '.$decreased_by.' Month(s)';
                    $collection_order_old_details = DB::table('del_orders')->where('order_id',$request->get('order_id'))->first();
                // $this->order_update_wp_alert('Collection Updated',$last_row->collection_order_id,$collection_order_old_details->shipping_first_name,$collection_order_old_details->DelDate,session('username'),$product_details);
                    // dd($key);
                }
                if($request->get('deposit_adjust')[$key] > 0){
                    $renewal_info = DB::table('renewals')->where('collection_order_id',$request->get('order_id'))->where('order_details_id',$id)->first();
                    // $depositAmount = $product->product_deposite-$depositAdjust[$product->id];
                    // DB::table('order_details')->where('id',$product->id)->update(['product_deposite'=>$depositAmount]);
                    $insertRecord = [
                        'product_id'=>$renewal_info->product_id,
                        'order_id'=>$request->get('order_id'),
                        'order_details_id'=>$id,
                        'adjusted_order_details_id'=>$renewal_info->id,
                        'fromorderid'=>$renewal_info->order_id,
                'fromtype'=>'D',
                        'intype'=>'R',
                        'adjusted_amount'=>$request->get('deposit_adjust')[$key],
                    ];
                    // dd($insertRecord);
                    DB::table('adjustment_table')->insert($insertRecord);
                    $comment = "Rent adjusted against deposit : ".$request->get('deposit_adjust')[$key];
                    DB::table('renewals')->where('id',$renewal_info->id)->update(['adjusted_deposit'=>$request->get('deposit_adjust')[$key],'comment'=>$comment]);
                }
                if(DB::table('pickups')->where('order_details_id',$id)->whereNull('status')->exists())
                {
                    $pickuporderid = DB::table('pickups')->where('order_details_id',$id)->whereNull('status')->first()->pickup_order_id;
                    $pickuporder = DB::table('pickups')->where('pickup_order_id',$pickuporderid)->whereNull('status')->get();
                    $orderdetailsarr = $pickuporder->pluck('order_details_id')->toArray();
                    $adjustedamount = 0;                        
                    foreach($pickuporder as $key=>$details)
                    {
                        if(DB::table('adjustment_table')->whereIn('order_details_id',$orderdetailsarr)->where('adjustment_table.flag','A')->exists())
                        {
                            $adjustedamount = array_sum(DB::table('adjustment_table')->whereIn('order_details_id',$orderdetailsarr)->where('fromtype','D')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->get()->pluck('adjusted_amount')->toArray());
                        }
                    }
                    $amounttoberefunded = DB::table('del_orders')->select('TotalAmt')->where('order_id',$pickuporderid)->first()->TotalAmt - $adjustedamount;
                    DB::table('del_orders')->where('order_id',$pickuporderid)->update(['TotalAmt'=>$amounttoberefunded]);
                }
            }
            if($request->get('renewal_id') != null)
            {
                if(count($request->get('renewal_id')) == DB::table('renewals')->select('order_details_id')->distinct('order_details_id')->where('collection_order_id',$request->get('order_id'))->whereNotIn('status',['Cancel'])->get()->count())
                {
                    DB::table('del_orders')->where('order_id',$request->get('order_id'))->update(['status'=>'Cancel']);
                    DB::table('order_details')->whereIn('id',$request->get('order_details_id'))->update(['current_status'=>'Pending']);
                    ActivityLog::insert([
                        'order_type'=>'CO',
                        'key_id'=>$request->get('order_id'),
                        'operation'=>'Collecton Order Cancelled',
                        'fields'=>'status',
                        'old_value'=>'Pending',
                        'new_value'=>'Cancel',
                        'updated_by'=>session('username')
                    ]);
                    $collection_order_old_details = DB::table('del_orders')->where('order_id',$request->get('order_id'))->first();
                // $this->order_update_wp_alert('Collection Cancelled',$last_row->collection_order_id,$collection_order_old_details->shipping_first_name,$collection_order_old_details->DelDate,session('username'),'Order Cancelled');
                }
                $collection_order_old_details = DB::table('del_orders')->where('order_id',$request->get('order_id'))->first();
                $last_row = DB::table('renewals')->where('collection_order_id',$request->get('order_id'))->where('order_details_id',$id)->whereNotIn('renewals.status',['Cancel'])->orderBy('id','DESC')->first();
                DB::table('renewals')->whereIn('order_details_id',$request->get('renewal_id'))->where('collection_order_id',$request->get('order_id'))->update(['status'=>'Cancel']);
                DB::table('order_details')->where('id',$request->get('renewal_id'))->update(['current_status'=>'Pending']);
            // $this->order_update_wp_alert('Collection Product Cancelled',$last_row->collection_order_id,$collection_order_old_details->shipping_first_name,$collection_order_old_details->DelDate,session('username'),'Order Product Cancelled');
            }
            $collection_order = DB::table('del_orders')->where('order_id',$request->get('order_id'))->first();
            // DD("NOT");
            if($collection_order->status!='Cancel')
            {
                // DD("NOT");
                if($request->get('payment_mode') == 'Cash')
                {
                    if($collection_order->status!="Pending")
                    {
                        DB::table('del_orders')->where('order_id',$request->get('order_id'))->update(['DelAssignedTo'=>$request->get('assign_delboy'),'PaymentMode'=>$request->get('payment_mode'),'cash'=>$request->get('TotalAmt')]);
                        // DB::table('renewals')->where('collection_order_id',$request->get('order_id'))->update(['DelAssignedTo'=>$request->get('assign_delboy'),'PaymentMode'=>$request->get('payment_mode'),'cash'=>$request->get('TotalAmt')]);
                    }
                    else
                    {
                        if($request->get('assign_delboy') != 'Pending')
                        {
                            DB::table('del_orders')->where('order_id',$request->get('order_id'))->update(['DelAssignedTo'=>$request->get('assign_delboy'),'status'=>'Assigned','PaymentMode'=>$request->get('payment_mode'),'cash'=>$request->get('TotalAmt')]);
                        }
                        else
                        {
                            DB::table('del_orders')->where('order_id',$request->get('order_id'))->update(['DelAssignedTo'=>$request->get('assign_delboy'),'status'=>'Pending','PaymentMode'=>$request->get('payment_mode'),'cash'=>$request->get('TotalAmt')]);
                        }
                    }
                    DB::update("UPDATE renewals SET renewals.cash_amount = renewals.total_amt,renewals.online_amount = 0, payment_mode = 'Cash' WHERE renewals.collection_order_id = $order_id");
                }
                else if($request->get('payment_mode') == 'Online')
                {
                    
                    DB::table('del_orders')->where('order_id',$request->get('order_id'))->update(['status'=>'Pending','PaymentMode'=>$request->get('payment_mode'),'online'=>$request->get('TotalAmt')]);
                    DB::update("UPDATE renewals SET renewals.online_amount = renewals.total_amt,renewals.cash_amount = 0, payment_mode = 'Online' WHERE renewals.collection_order_id = $order_id");
                    // dd("as");
                }
                if($request->get('discount_offered')[$key] != 0 && $request->get('discount_offered')[$key] != null)
                {
                    $renewal_info = DB::table('order_details')->where('id',$id)->first();
                    // dd($renewal_info);
                    DB::table('renewals')->where('collection_order_id',$request->get('order_id'))->where('order_details_id',$id)
                            ->update([
                                'cash_amount'=>($request->get('payment_mode') == 'Cash')?$renewal_info->product_rent-($request->get('discount_offered')[$key]/$request->get('payment_months')[$key]):0,
                                'online_amount'=>($request->get('payment_mode') == 'Online')?$renewal_info->product_rent-($request->get('discount_offered')[$key]/$request->get('payment_months')[$key]):0,
                                'discount_amt'=>($request->get('discount_offered')[$key]/$request->get('payment_months')[$key]),
                                'total_amt'=>$renewal_info->product_rent,
                            ]);
                    $renew_amount = DB::table('renewals')
                            // ->select(DB::raw("SUM('cash_amount') as cash_sum"))
                            ->where('collection_order_id',$request->get('order_id'))
                            ->whereNotIn('renewals.status',['Cancel'])
                            ->get();
                    // dd($renew_amount,$request->get('order_id'));
                    $total_sum = array_sum($renew_amount->pluck('online_amount')->toArray()) + array_sum($renew_amount->pluck('cash_amount')->toArray());
                    DB::table('del_orders')->where('order_id',$request->get('order_id'))->update([
                        'TotalAmt'=>$total_sum,
                    ]);
    
                }
    
                $order_data = DB::table('renewals')
                                ->join('order_details','renewals.order_details_id','=','order_details.id')                            
                                ->join('products','order_details.product_id','=','products.id')
                                ->join('del_orders','renewals.collection_order_id','=','del_orders.order_id')
                                ->select('order_details.*','renewals.id as renewal_id','del_orders.PaymentMode as payment_mode','del_orders.DelAssignedTo','renewals.start_date as ren_start_date','renewals.end_date as ren_end_date','order_details.id as order_details_id','products.product_name','del_orders.status as delivery_status','renewals.collection_order_id','renewals.discount_amt')
                                ->where('renewals.collection_order_id',$request->get('order_id'))
                                ->whereNotIn('renewals.status',['Cancel'])
                                ->get();
                
                $products = implode(',',$order_data->pluck('product_name')->unique()->toArray());
    
                $totalAmt = array_sum($order_data->pluck('product_rent')->toArray()) - array_sum($order_data->pluck('discount_amt')->toArray());
                if($order_data[0]->payment_mode == 'Cash')
                {
                    $update_pay = [
                        'line_item_1'=>$products,
                        'TotalAmt'=>$totalAmt,
                        'cash'=>$totalAmt,
                        'online'=>0
                    ];
                }
                else if($order_data[0]->payment_mode == 'Online'){
                    $update_pay = [
                        'line_item_1'=>$products,
                        'TotalAmt'=>$totalAmt,
                        'online'=>$totalAmt,
                        'cash'=>0
                    ];
                }
                DB::table('del_orders')->where('order_id',$request->get('order_id'))->update($update_pay);
                if($collection_order->PaymentMode != $request->get('payment_mode'))
                {
                    ActivityLog::insert([
                        'order_type'=>'CO',
                        'key_id'=>$request->get('order_id'),
                        'operation'=>'Collecton Order Updated',
                        'fields'=>'PaymentMode',
                        'old_value'=>$collection_order->PaymentMode,
                        'new_value'=>$request->get('payment_mode'),
                        'updated_by'=>session('username')
                    ]);
                    $details = 'Payment Mode Updated from:'.$collection_order->PaymentMode.' to '.$request->get('payment_mode');
                // $this->order_update_wp_alert('Collection Updated',$collection_order->order_id,$collection_order->shipping_first_name,$collection_order->DelDate,session('username'),$details);
                }
            }
            DB::table('del_orders')->where('order_id',$request->get('order_id'))->update([
                'DelDate'=>date('d-m-Y',strtotime($request->get('collectiondate'))),
            ]);
            DB::commit();
            return redirect()->back()->with('success','Order Updated Successfully!')->with('payreceived-url','payment_recieved');
        }catch(Exception $ex){
            DB::rollback();
            return redirect()->back()->with('success','Order Updated Successfully!')->with('error','Something went wrong: '.$ex->getMessage());
        }
    }

    public function order_update_wp_alert($changed,$order_id,$customer_name,$orderDate,$modifiedBy,$product_details)
    {
        $accounts_nos = config('app.accounts_staff_contacts');
        foreach($accounts_nos as $key=>$value)
        {
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
                "portno"=>"11140",
                "namespace"=>"b9a23cb4_89ed_4fe2_b849_20775908ff5e",
                "countrycode"=> "91",
                "mobileno"=> "$value",
                "templatename" => "change_order_product_added_removed",
                "templateparams" => [
                    ["type"=> "text","text"=> $changed],
                    ["type"=> "text","text"=> "$order_id"],
                    ["type"=> "text","text"=> $customer_name],
                    ["type"=> "text","text"=> $orderDate],
                    ["type"=> "text","text"=> $modifiedBy],
                    ["type"=> "text","text"=> $product_details]
                ],
            ];
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            
            $resp = curl_exec($curl);
            // dd($resp);
            curl_close($curl);
        }
    }

    public function unsettleOrder(Request $request)
    {
        $updated = DB::table('del_orders')->where('order_id',$request->get('order_id'))
                    ->update([
                        'unsettled_on'=>date('Y-m-d H:i:s'),
                        'unsettled_by'=>session('username'),
                        'settlement_status'=>'N'
                    ]);
        return redirect()->back()->with('message','Order Unsettled Successfully!');
    }
    public function addComment(Request $request)
    {
        if(DB::table('del_orders')->where('order_id',$request->get('addcomment_hiddenorderid'))->whereNotNull('comment')->exists())
        {
            DB::table('del_orders')->where('order_id',$request->get('addcomment_hiddenorderid'))->update([
                'comment'=>DB::raw('concat(comment,",","'.$request->get('updatedcomment').'")')
            ]);
        }
        else
        {
            DB::table('del_orders')->where('order_id',$request->get('addcomment_hiddenorderid'))->update([
                'comment'=>$request->get('updatedcomment')
            ]);
        }

        return redirect()->back()->with('message','Comment Added');
    }

    public function getOrderActivitylog(Request $request,$orderid){
        if($orderid){
            $orderdetailsids = [];
            if($request->get('order_type')!=null){
                if($request->get('order_type')=='Delivery'){
                    $orderdetailsids = DB::table('order_details')->where('order_id',$orderid)->get('id')->pluck('id');
                }
            }
            $getactivitydata = DB::table('activity_log')->where('key_id',$orderid)->get();
        }
    }

    public function adjustmentDetails(Request $request)
    {
        $url = $request->fullUrl();
        $method = $request->getMethod();
        $ip = $request->getClientIp();
        $reqResp = [
            'Ip'=>$ip,
            'Url'=>$url,
            'Method'=>$method,
            'username'=>session('username'),
            'Request' => $request->all(),
        ];
        Log::channel('intrarequestlog')->info(json_encode($reqResp));
        if($request->get('request_type') == 'order-product-details')
        {
            if($request->get('ordertype') == 'Delivery')
            {
                try{
                    $orderdetails = DB::table('order_details')
                        ->join('products','products.id','=','order_details.product_id')
                        ->select('order_details.*','products.product_name')
                        ->where('order_details.order_id',$request->get('orderid'))
                        ->whereNotIn('order_details.current_status',['Cancel'])
                        // ->where('order_details.sale_rental','Rental')
                        // ->whereNotIn('order_details.product_deposite',[0])
                        ->get();
                    if(count($orderdetails)>0)
                    {
                        $availableproducts = DB::table('order_details')
                            ->join('products','products.id','=','order_details.product_id')
                            ->select('order_details.*','products.product_name')
                            ->where('order_details.customer_id',$orderdetails[0]->customer_id)
                            ->whereNotIn('order_details.current_status',['Cancel'])
                            // ->whereNotIn('order_details.product_deposite',[0])
                            ->get();
                        foreach($availableproducts as $key=>$product)
                        {
                            if(in_array($product->current_status,['Picked Up','Pending Pickup']))
                            {
                                if(DB::table('pickups')->join('del_orders','pickups.del_order_id','=','del_orders.order_id')->whereNotIn('pickups.status',['Cancel'])->where('pickups.order_details_id',$product->id)->where('del_orders.settlement_status','Y')->exists())
                                {
                                    unset($availableproducts[$key]);
                                }
                            }
                        }
                        return ['status'=>'success','timestamp'=>Carbon::now(),'user'=>session('username'),'description'=>'Found','data'=>$orderdetails,'availableproducts'=>$availableproducts];    
                    }
                    else
                    {
                        return ['status'=>'error','timestamp'=>Carbon::now(),'user'=>session('username'),'description'=>'No Products!'];    
                    }
                }
                catch(Exception $e){
                    return ['status'=>'error','timestamp'=>Carbon::now(),'user'=>session('username'),'description'=>$e];
                    Log::channel('intraerrorlog')->info($e);
                }
            }
            elseif($request->get('ordertype') == 'Collection')
            {
                try{
                    $orderdetails = DB::table('renewals')
                        ->join('order_details','order_details.id','=','renewals.order_details_id')
                        ->join('products','products.id','=','order_details.product_id')
                        ->select('order_details.*','renewals.id as renewalid','renewals.start_date','renewals.end_date','products.product_name')
                        ->where('renewals.collection_order_id',$request->get('orderid'))
                        ->whereNotIn('renewals.status',['Cancel'])
                        ->where('order_details.sale_rental','Rental')
                        ->get();
                    if(count($orderdetails)>0)
                    {
                        $availableproducts = DB::table('order_details')
                            ->join('products','products.id','=','order_details.product_id')
                            ->select('order_details.*','products.product_name')
                            ->where('order_details.customer_id',$orderdetails[0]->customer_id)
                            ->whereNotIn('order_details.current_status',['Cancel'])
                            ->whereNotIn('order_details.product_deposite',[0])
                            ->get();
                        foreach($availableproducts as $key=>$product)
                        {
                            if(in_array($product->current_status,['Picked Up','Pending Pickup']))
                            {
                                // if(DB::table('pickups')->join('del_orders','pickups.del_order_id','=','del_orders.order_id')->whereNotIn('pickups.status',['Cancel'])->where('del_orders.settlement_status','Y')->exists())
                                if(DB::table('pickups')->join('del_orders','pickups.del_order_id','=','del_orders.order_id')->whereNotIn('pickups.status',['Cancel'])->where('pickups.order_details_id',$product->id)->where('del_orders.settlement_status','Y')->exists())
                                {
                                    unset($availableproducts,$key);
                                }
                            }
                        }
                        return ['status'=>'success','timestamp'=>Carbon::now(),'user'=>session('username'),'description'=>'Found','data'=>$orderdetails,'availableproducts'=>$availableproducts];    
                    }
                    else
                    {
                        return ['status'=>'error','timestamp'=>Carbon::now(),'user'=>session('username'),'description'=>'No Products!'];    
                    }
                }
                catch(Exception $e){
                    Log::channel('intraerrorlog')->info($e);
                    return ['status'=>'error','timestamp'=>Carbon::now(),'user'=>session('username'),'description'=>$e];
                }
            }
            elseif($request->get('ordertype') == 'Pick Up')
            {
                try{
                    $orderdetails = DB::table('pickups')
                        ->join('order_details','order_details.id','=','pickups.order_details_id')
                        ->join('products','products.id','=','order_details.product_id')
                        ->select('order_details.*','products.product_name')
                        ->where('pickups.pickup_order_id',$request->get('orderid'))
                        ->whereNotIn('order_details.current_status',['Cancel'])
                        ->where('order_details.sale_rental','Rental')
                        // ->whereNotIn('order_details.product_deposite',[0])
                        ->get();
                    if(count($orderdetails)>0)
                    {
                        $availableproducts = DB::table('pickups')
                            ->join('order_details','order_details.id','=','pickups.order_details_id')
                            ->join('products','products.id','=','order_details.product_id')
                            ->select('order_details.*','products.product_name')
                            ->where('pickups.pickup_order_id',$request->get('orderid'))
                            ->whereNotIn('order_details.current_status',['Cancel'])
                            ->where('order_details.sale_rental','Rental')
                            // ->whereNotIn('order_details.product_deposite',[0])
                            ->get();
                        // foreach($availableproducts as $key=>$product)
                        // {
                        //     if(in_array($product->current_status,['Picked Up','Pending Pickup']))
                        //     {
                        //         if(DB::table('pickups')->join('del_orders','pickups.del_order_id','=','del_orders.order_id')->whereNotIn('pickups.status',['Cancel'])->where('del_orders.settlement_status','Y')->exists())
                        //         {
                        //             unset($availableproducts[$key]);
                        //         }
                        //     }
                        // }
                        $availabledeposit = DB::table('del_orders')->where('order_id',$request->get('orderid'))->first()->TotalAmt;
                        return ['status'=>'success','timestamp'=>Carbon::now(),'user'=>session('username'),'description'=>'Found','data'=>$orderdetails,'availableproducts'=>$availableproducts,'availabledeposit'=>$availabledeposit];    
                    }
                    else
                    {
                        return ['status'=>'error','timestamp'=>Carbon::now(),'user'=>session('username'),'description'=>'No Products!'];    
                    }
                }
                catch(Exception $e){
                    return ['status'=>'error','timestamp'=>Carbon::now(),'user'=>session('username'),'description'=>$e];
                    Log::channel('intraerrorlog')->info($e);
                }
            }
            elseif($request->get('ordertype') == 'Repair'){
                try{
                    $orderdetails = DB::table('maintenance_orders')
                        ->join('products','products.id','=','maintenance_orders.product_id')
                        ->join('order_details','order_details.id','=','maintenance_orders.order_details_id')
                        ->select('maintenance_orders.id as maintenance_id','order_details.*','products.product_name')
                        ->where('maintenance_orders.order_id',$request->get('orderid'))
                        ->whereNotIn('order_details.current_status',['Cancel'])
                        // ->where('order_details.sale_rental','Rental')
                        // ->whereNotIn('order_details.product_deposite',[0])
                        ->get();
                        // return $orderdetails;
                    if(count($orderdetails)>0)
                    {
                        $availableproducts = DB::table('order_details')
                            ->join('products','products.id','=','order_details.product_id')
                            ->select('order_details.*','products.product_name')
                            ->where('order_details.customer_id',$orderdetails[0]->customer_id)
                            ->whereNotIn('order_details.current_status',['Cancel'])
                            // ->whereNotIn('order_details.product_deposite',[0])
                            ->get();
                        foreach($availableproducts as $key=>$product)
                        {
                            if(in_array($product->current_status,['Picked Up','Pending Pickup']))
                            {
                                // if(DB::table('pickups')->join('del_orders','pickups.del_order_id','=','del_orders.order_id')->whereNotIn('pickups.status',['Cancel'])->where('del_orders.settlement_status','Y')->exists())
                                if(DB::table('pickups')->join('del_orders','pickups.del_order_id','=','del_orders.order_id')->whereNotIn('pickups.status',['Cancel'])->where('pickups.order_details_id',$product->id)->where('del_orders.settlement_status','Y')->exists())
                                {
                                    unset($availableproducts[$key]);
                                }
                            }
                        }
                        return ['status'=>'success','timestamp'=>Carbon::now(),'user'=>session('username'),'description'=>'Found','data'=>$orderdetails,'availableproducts'=>$availableproducts];    
                    }
                    else
                    {
                        return ['status'=>'error','timestamp'=>Carbon::now(),'user'=>session('username'),'description'=>'No Products!'];    
                    }
                }
                catch(Exception $e){
                    return ['status'=>'error','timestamp'=>Carbon::now(),'user'=>session('username'),'description'=>$e];
                    Log::channel('intraerrorlog')->info($e);
                }
            }
        }
        else if($request->get('request_type') == 'adj-details')
        {
            if($request->get('ordertype') == 'Delivery')
            {
                try{
                    $inorderproduct = DB::table('order_details')
                        ->join('products','products.id','=','order_details.product_id')
                        ->select('order_details.*','products.product_name')
                        ->where('order_details.id',$request->get('inorderproduct'))
                        ->whereNotIn('order_details.current_status',['Cancel'])
                        ->first();
                    $fromorderproduct = DB::table('order_details')
                        ->join('products','products.id','=','order_details.product_id')
                        ->select('order_details.*','products.product_name')
                        ->where('order_details.id',$request->get('fromorderproduct'))
                        ->whereNotIn('order_details.current_status',['Cancel'])
                        ->first();
                    if($inorderproduct && $fromorderproduct)
                    {
                        $inorderproduct->product_deposite = RenewalPickupController::fetchCrDrData($inorderproduct->id,'D');
                        $fromorderproduct->product_deposite = RenewalPickupController::fetchCrDrData($fromorderproduct->id,'D');
                        if(DB::table('adjustment_table')->where('order_details_id',$inorderproduct->id)->where('fromtype','R')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->exists())
                        {
                            $inorderproduct->adjusted_rent = array_sum(DB::table('adjustment_table')->where('order_details_id',$inorderproduct->id)->where('fromtype','R')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->get()->pluck('adjusted_amount')->toArray());
                        }
                        else
                        {
                            $inorderproduct->adjusted_rent = 0;                
                        }
                        if(DB::table('adjustment_table')->where('order_details_id',$fromorderproduct->id)->where('fromtype','R')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->exists())
                        {
                            $fromorderproduct->adjusted_rent = array_sum(DB::table('adjustment_table')->where('order_details_id',$fromorderproduct->id)->where('fromtype','R')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->get()->pluck('adjusted_amount')->toArray());
                        }
                        else
                        {
                            $fromorderproduct->adjusted_rent = 0;                
                        }

                        if(DB::table('adjustment_table')->where('order_details_id',$inorderproduct->id)->where('fromtype','D')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->exists())
                        {
                            $inorderproduct->adjusted_deposit = array_sum(DB::table('adjustment_table')->where('order_details_id',$inorderproduct->id)->where('fromtype','D')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->get()->pluck('adjusted_amount')->toArray());
                        }
                        else
                        {
                            $inorderproduct->adjusted_deposit = 0;                
                        }
                        if(DB::table('adjustment_table')->where('order_details_id',$fromorderproduct->id)->where('fromtype','D')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->exists())
                        {
                            $fromorderproduct->adjusted_deposit = array_sum(DB::table('adjustment_table')->where('order_details_id',$fromorderproduct->id)->where('fromtype','D')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->get()->pluck('adjusted_amount')->toArray());
                        }
                        else
                        {
                            $fromorderproduct->adjusted_deposit = 0;                
                        }
                        if(DB::table('adjustment_table')->where('order_details_id',$inorderproduct->id)->where('fromtype','T')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->exists())
                        {
                            $inorderproduct->adjusted_transport = array_sum(DB::table('adjustment_table')->where('order_details_id',$inorderproduct->id)->where('fromtype','T')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->get()->pluck('adjusted_amount')->toArray());
                        }
                        else
                        {
                            $inorderproduct->adjusted_transport = 0;                
                        }
                        if(DB::table('adjustment_table')->where('order_details_id',$fromorderproduct->id)->where('fromtype','T')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->exists())
                        {
                            $fromorderproduct->adjusted_transport = array_sum(DB::table('adjustment_table')->where('order_details_id',$fromorderproduct->id)->where('fromtype','T')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->get()->pluck('adjusted_amount')->toArray());
                        }
                        else
                        {
                            $fromorderproduct->adjusted_transport = 0;                
                        }
                        return ['status'=>'success','timestamp'=>Carbon::now(),'user'=>session('username'),'description'=>'Found','inorderproduct'=>$inorderproduct,'fromorderproduct'=>$fromorderproduct];    
                    }
                    else
                    {
                        return ['status'=>'error','timestamp'=>Carbon::now(),'user'=>session('username'),'description'=>'No Details Found!'];    
                    }
                }
                catch(Exception $e){
                    Log::channel('intraerrorlog')->info($e);
                    return ['status'=>'error','timestamp'=>Carbon::now(),'user'=>session('username'),'description'=>$e];
                }
            }
            // Not Completed yet collection fetch data is pending.
            // users can edit collection order and adjust deposit against rent for specific month.            
            if($request->get('ordertype') == 'Collection')
            {
                try{
                    $inorderproduct = DB::table('renewals')
                        ->join('order_details','order_details.id','=','renewals.order_details_id')
                        ->join('products','products.id','=','order_details.product_id')
                        ->select('order_details.*','products.product_name')
                        ->where('renewals.id',$request->get('inorderproduct'))
                        ->whereNotIn('order_details.current_status',['Cancel'])
                        ->first();
                    $fromorderproduct = DB::table('order_details')
                        ->join('products','products.id','=','order_details.product_id')
                        ->select('order_details.*','products.product_name')
                        ->where('order_details.id',$request->get('fromorderproduct'))
                        ->whereNotIn('order_details.current_status',['Cancel'])
                        ->first();
                    if($inorderproduct && $fromorderproduct)
                    {
                        $inorderproduct->product_deposite = RenewalPickupController::fetchCrDrData($inorderproduct->id,'D');
                        $fromorderproduct->product_deposite = RenewalPickupController::fetchCrDrData($fromorderproduct->id,'D');
                        if(DB::table('adjustment_table')->where('order_details_id',$inorderproduct->id)->where('fromtype','R')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->exists())
                        {
                            $inorderproduct->adjusted_rent = array_sum(DB::table('adjustment_table')->where('order_details_id',$inorderproduct->id)->where('fromtype','R')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->get()->pluck('adjusted_amount')->toArray());
                        }
                        else
                        {
                            $inorderproduct->adjusted_rent = 0;                
                        }
                        if(DB::table('adjustment_table')->where('order_details_id',$fromorderproduct->id)->where('fromtype','R')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->exists())
                        {
                            $fromorderproduct->adjusted_rent = array_sum(DB::table('adjustment_table')->where('order_details_id',$fromorderproduct->id)->where('fromtype','R')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->get()->pluck('adjusted_amount')->toArray());
                        }
                        else
                        {
                            $fromorderproduct->adjusted_rent = 0;                
                        }
                        if(DB::table('adjustment_table')->where('order_details_id',$inorderproduct->id)->where('fromtype','D')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->exists())
                        {
                            $inorderproduct->adjusted_deposit = array_sum(DB::table('adjustment_table')->where('order_details_id',$inorderproduct->id)->where('fromtype','D')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->get()->pluck('adjusted_amount')->toArray());
                        }
                        else
                        {
                            $inorderproduct->adjusted_deposit = 0;                
                        }
                        if(DB::table('adjustment_table')->where('order_details_id',$fromorderproduct->id)->where('fromtype','D')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->exists())
                        {
                            $fromorderproduct->adjusted_deposit = array_sum(DB::table('adjustment_table')->where('order_details_id',$fromorderproduct->id)->where('fromtype','D')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->get()->pluck('adjusted_amount')->toArray());
                        }
                        else
                        {
                            $fromorderproduct->adjusted_deposit = 0;                
                        }
                        if(DB::table('adjustment_table')->where('order_details_id',$inorderproduct->id)->where('fromtype','T')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->exists())
                        {
                            $inorderproduct->adjusted_transport = array_sum(DB::table('adjustment_table')->where('order_details_id',$inorderproduct->id)->where('fromtype','T')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->get()->pluck('adjusted_amount')->toArray());
                        }
                        else
                        {
                            $inorderproduct->adjusted_transport = 0;                
                        }
                        if(DB::table('adjustment_table')->where('order_details_id',$fromorderproduct->id)->where('fromtype','T')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->exists())
                        {
                            $fromorderproduct->adjusted_transport = array_sum(DB::table('adjustment_table')->where('order_details_id',$fromorderproduct->id)->where('fromtype','T')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->get()->pluck('adjusted_amount')->toArray());
                        }
                        else
                        {
                            $fromorderproduct->adjusted_transport = 0;                
                        }
                        return ['status'=>'success','timestamp'=>Carbon::now(),'user'=>session('username'),'description'=>'Found','inorderproduct'=>$inorderproduct,'fromorderproduct'=>$fromorderproduct];    
                    }
                    else
                    {
                        return ['status'=>'error','timestamp'=>Carbon::now(),'user'=>session('username'),'description'=>'No Details Found!'];    
                    }
                }
                catch(Exception $e){
                    Log::channel('intraerrorlog')->info($e);
                    return ['status'=>'error','timestamp'=>Carbon::now(),'user'=>session('username'),'description'=>$e];
                }
            }
            if($request->get('ordertype') == 'Repair')
            {
                try{
                    $inorderproduct = DB::table('maintenance_orders')
                        ->join('products','products.id','=','maintenance_orders.product_id')
                        ->join('order_details','order_details.id','=','maintenance_orders.order_details_id')
                        ->select('maintenance_orders.id as maintenance_id','order_details.*','products.product_name')
                        ->where('maintenance_orders.id',$request->get('inorderproduct'))
                        ->whereNotIn('order_details.current_status',['Cancel'])
                        ->first();
                    $fromorderproduct = DB::table('order_details')
                        ->join('products','products.id','=','order_details.product_id')
                        ->select('order_details.*','products.product_name')
                        ->where('order_details.id',$request->get('fromorderproduct'))
                        ->whereNotIn('order_details.current_status',['Cancel'])
                        ->first();
                    if($inorderproduct && $fromorderproduct)
                    {
                        $inorderproduct->product_deposite = RenewalPickupController::fetchCrDrData($inorderproduct->id,'D');
                        $fromorderproduct->product_deposite = RenewalPickupController::fetchCrDrData($fromorderproduct->id,'D');
                        if(DB::table('adjustment_table')->where('order_details_id',$inorderproduct->id)->where('fromtype','R')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->exists())
                        {
                            $inorderproduct->adjusted_rent = array_sum(DB::table('adjustment_table')->where('order_details_id',$inorderproduct->id)->where('fromtype','R')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->get()->pluck('adjusted_amount')->toArray());
                        }
                        else
                        {
                            $inorderproduct->adjusted_rent = 0;                
                        }
                        if(DB::table('adjustment_table')->where('order_details_id',$fromorderproduct->id)->where('fromtype','R')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->exists())
                        {
                            $fromorderproduct->adjusted_rent = array_sum(DB::table('adjustment_table')->where('order_details_id',$fromorderproduct->id)->where('fromtype','R')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->get()->pluck('adjusted_amount')->toArray());
                        }
                        else
                        {
                            $fromorderproduct->adjusted_rent = 0;                
                        }

                        if(DB::table('adjustment_table')->where('order_details_id',$inorderproduct->id)->where('fromtype','D')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->exists())
                        {
                            $inorderproduct->adjusted_deposit = array_sum(DB::table('adjustment_table')->where('order_details_id',$inorderproduct->id)->where('fromtype','D')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->get()->pluck('adjusted_amount')->toArray());
                        }
                        else
                        {
                            $inorderproduct->adjusted_deposit = 0;                
                        }
                        if(DB::table('adjustment_table')->where('order_details_id',$fromorderproduct->id)->where('fromtype','D')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->exists())
                        {
                            $fromorderproduct->adjusted_deposit = array_sum(DB::table('adjustment_table')->where('order_details_id',$fromorderproduct->id)->where('fromtype','D')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->get()->pluck('adjusted_amount')->toArray());
                        }
                        else
                        {
                            $fromorderproduct->adjusted_deposit = 0;                
                        }
                        if(DB::table('adjustment_table')->where('order_details_id',$inorderproduct->id)->where('fromtype','T')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->exists())
                        {
                            $inorderproduct->adjusted_transport = array_sum(DB::table('adjustment_table')->where('order_details_id',$inorderproduct->id)->where('fromtype','T')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->get()->pluck('adjusted_amount')->toArray());
                        }
                        else
                        {
                            $inorderproduct->adjusted_transport = 0;                
                        }
                        if(DB::table('adjustment_table')->where('order_details_id',$fromorderproduct->id)->where('fromtype','T')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->exists())
                        {
                            $fromorderproduct->adjusted_transport = array_sum(DB::table('adjustment_table')->where('order_details_id',$fromorderproduct->id)->where('fromtype','T')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->get()->pluck('adjusted_amount')->toArray());
                        }
                        else
                        {
                            $fromorderproduct->adjusted_transport = 0;                
                        }
                        return ['status'=>'success','timestamp'=>Carbon::now(),'user'=>session('username'),'description'=>'Found','inorderproduct'=>$inorderproduct,'fromorderproduct'=>$fromorderproduct];    
                    }
                    else
                    {
                        return ['status'=>'error','timestamp'=>Carbon::now(),'user'=>session('username'),'description'=>'No Details Found!'];    
                    }
                }
                catch(Exception $e){
                    Log::channel('intraerrorlog')->info($e);
                    return ['status'=>'error','timestamp'=>Carbon::now(),'user'=>session('username'),'description'=>$e];
                }
            }
            // return $request->all();
        }
        else if($request->get('request_type') == 'adjust-deposit-details')
        {
            // dd($request->all());
            if($request->get('source') == 'Delivery')
            {
                try{
                    DB::beginTransaction();
                    $inorderproduct = DB::table('order_details')->where('id',$request->get('inorderproduct'))->first();
                    $fromorderproduct = DB::table('order_details')->where('id',$request->get('fromorderproduct'))->first();
                    DB::table('adjustment_table')->insert(
                        [
                            "product_id"=>$fromorderproduct->product_id,
                            "order_id"=>$inorderproduct->order_id,
                            "order_details_id"=>$fromorderproduct->id,
                            "adjusted_order_details_id"=>$inorderproduct->id,
                            "fromorderid"=>$fromorderproduct->order_id,
                            "fromtype"=>($request->get('fromadjust_rent_depo') == 'deposit'?'D':($request->get('fromadjust_rent_depo') == 'rent'?'R':'T')),
                            "intype"=>($request->get('inadjust_rent_depo') == 'deposit'?'D':($request->get('inadjust_rent_depo') == 'rent'?'R':'T')),
                            "adjusted_amount"=>$request->get('adjusteddeposit')
                        ]
                    );
                    foreach([
                        "product_id"=>$fromorderproduct->product_id,
                        "order_id"=>$inorderproduct->order_id,
                        "order_details_id"=>$fromorderproduct->id,
                        "adjusted_order_details_id"=>$inorderproduct->id,
                        "fromorderid"=>$fromorderproduct->order_id,
                        "fromtype"=>($request->get('fromadjust_rent_depo') == 'deposit'?'D':($request->get('fromadjust_rent_depo') == 'rent'?'R':'T')),
                        "intype"=>($request->get('inadjust_rent_depo') == 'deposit'?'D':($request->get('inadjust_rent_depo') == 'rent'?'R':'T')),
                        "adjusted_amount"=>$request->get('adjusteddeposit')
                    ] as $key=>$value)
                    {
                        ActivityLog::insert([
                            'order_type'=>'DO',
                            'key_id'=>$inorderproduct->id,
                            'operation'=>'Adjust Deposit',
                            'fields'=>$key,
                            'old_value'=>null,
                            'new_value'=>$value,
                            'updated_by'=>session('username')
                        ]);
                    }
                    if(DB::table('pickups')->where('order_details_id',$request->get('fromorderproduct'))->whereNull('status')->exists() && $request->get('fromadjust_rent_depo') =='deposit')
                    {
                        // dd("from");
                        $pickuporderid = DB::table('pickups')->where('order_details_id',$request->get('fromorderproduct'))->whereNull('status')->first()->pickup_order_id;
                        $pickuporder = DB::table('pickups')->where('pickup_order_id',$pickuporderid)->whereNull('status')->get();
                        $orderdetailsarr = $pickuporder->pluck('order_details_id')->toArray();
                        $adjustedamount = 0;                        
                        foreach($pickuporder as $key=>$details)
                        {
                            if(DB::table('adjustment_table')->whereIn('order_details_id',$orderdetailsarr)->where('adjustment_table.flag','A')->exists())
                            {
                                $adjustedamount = array_sum(DB::table('adjustment_table')->whereIn('order_details_id',$orderdetailsarr)->where('fromtype','D')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->get()->pluck('adjusted_amount')->toArray());
                            }
                        }
                        $orderdetailsrecords = DB::table('order_details')->select('id','product_deposite')->whereIn('id',$orderdetailsarr)->get();
                        foreach($orderdetailsrecords as $key=>$value){
                            $orderdetailsrecords[$key]->product_deposite = RenewalPickupController::fetchCrDrData($value->id,'D');
                        }
                        $totalAmt = array_sum($orderdetailsrecords->pluck('product_deposite')->toArray());
                        // $amounttoberefunded = DB::table('del_orders')->select('TotalAmt')->where('order_id',$pickuporderid)->first()->TotalAmt - $adjustedamount;
                        $amounttoberefunded = $totalAmt - $adjustedamount;
                        DB::table('del_orders')->where('order_id',$pickuporderid)->update(['TotalAmt'=>$amounttoberefunded]);
                    }
                    if(DB::table('pickups')->where('order_details_id',$request->get('inorderproduct'))->whereNull('status')->exists() && $request->get('fromadjust_rent_depo') =='deposit')
                    {
                        // dd("In");
                        $pickuporderid = DB::table('pickups')->where('order_details_id',$request->get('inorderproduct'))->whereNull('status')->first()->pickup_order_id;
                        $pickuporder = DB::table('pickups')->where('pickup_order_id',$pickuporderid)->whereNull('status')->get();
                        $orderdetailsarr = $pickuporder->pluck('order_details_id')->toArray();
                        $adjustedamount = 0;                        
                        foreach($pickuporder as $key=>$details)
                        {
                            if(DB::table('adjustment_table')->whereIn('order_details_id',$orderdetailsarr)->where('adjustment_table.flag','A')->exists())
                            {
                                $adjustedamount = array_sum(DB::table('adjustment_table')->whereIn('order_details_id',$orderdetailsarr)->where('intype','D')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->get()->pluck('adjusted_amount')->toArray());
                            }
                        }
                        $orderdetailsrecords = DB::table('order_details')->select('id','product_deposite')->whereIn('id',$orderdetailsarr)->get();
                        foreach($orderdetailsrecords as $key=>$value){
                            $orderdetailsrecords[$key]->product_deposite = RenewalPickupController::fetchCrDrData($value->id,'D');
                        }
                        // dd($orderdetailsrecords);
                        $totalAmt = array_sum($orderdetailsrecords->pluck('product_deposite')->toArray());

                        // $amounttoberefunded = DB::table('del_orders')->select('TotalAmt')->where('order_id',$pickuporderid)->first()->TotalAmt - $adjustedamount;
                        // dd($totalAmt,$adjustedamount);
                        $amounttoberefunded = $totalAmt - $adjustedamount;
                        DB::table('del_orders')->where('order_id',$pickuporderid)->update(['TotalAmt'=>$amounttoberefunded]);
                    }
                    // dd("Working");
                    DB::commit();
                    return redirect()->back()->with('message','Deposit Adjusted Successfully!');
                }
                catch(Exception $e){
                    DB::rollback();
                    Log::channel('intraerrorlog')->info($e);
                    return redirect()->back()->with('message','Something went wrong! Try Again!');
                }
            }
            if($request->get('source') == 'Collection')
            {
                try{
                    DB::beginTransaction();
                    $inorderproduct = DB::table('renewals')->where('id',$request->get('inorderproduct'))->first();
                    $fromorderproduct = DB::table('order_details')->where('id',$request->get('fromorderproduct'))->first();
                    DB::table('adjustment_table')->insert(
                        [
                            "product_id"=>$fromorderproduct->product_id,
                            "order_id"=>$inorderproduct->collection_order_id,
                            "order_details_id"=>$fromorderproduct->id,
                            "adjusted_order_details_id"=>$inorderproduct->id,
                            "fromorderid"=>$fromorderproduct->order_id,
                            // "fromtype"=>($request->get('fromadjust_rent_depo') == 'deposit')?'D':'R',
                            // "intype"=>($request->get('inadjust_rent_depo') == 'deposit')?'D':'R',
                            "fromtype"=>($request->get('fromadjust_rent_depo') == 'deposit'?'D':($request->get('fromadjust_rent_depo') == 'rent'?'R':'T')),
                            "intype"=>($request->get('inadjust_rent_depo') == 'deposit'?'D':($request->get('inadjust_rent_depo') == 'rent'?'R':'T')),
                            "adjusted_amount"=>$request->get('adjusteddeposit')
                        ]
                    );
                    foreach([
                        "product_id"=>$fromorderproduct->product_id,
                        "order_id"=>$inorderproduct->order_id,
                        "order_details_id"=>$fromorderproduct->id,
                        "adjusted_order_details_id"=>$inorderproduct->id,
                        "fromorderid"=>$fromorderproduct->order_id,
                        // "fromtype"=>($request->get('fromadjust_rent_depo') == 'deposit')?'D':'R',
                        // "intype"=>($request->get('inadjust_rent_depo') == 'deposit')?'D':'R',
                        "fromtype"=>($request->get('fromadjust_rent_depo') == 'deposit'?'D':($request->get('fromadjust_rent_depo') == 'rent'?'R':'T')),
                        "intype"=>($request->get('inadjust_rent_depo') == 'deposit'?'D':($request->get('inadjust_rent_depo') == 'rent'?'R':'T')),
                        "adjusted_amount"=>$request->get('adjusteddeposit')
                    ] as $key=>$value)
                    {
                        ActivityLog::insert([
                            'order_type'=>'DO',
                            'key_id'=>$inorderproduct->id,
                            'operation'=>'Adjust Deposit',
                            'fields'=>$key,
                            'old_value'=>null,
                            'new_value'=>$value,
                            'updated_by'=>session('username')
                        ]);
                    }
                    if(DB::table('pickups')->where('order_details_id',$request->get('fromorderproduct'))->whereNull('status')->exists() && $request->get('fromadjust_rent_depo') =='deposit')
                    {
                        // dd("from");
                        $pickuporderid = DB::table('pickups')->where('order_details_id',$request->get('fromorderproduct'))->whereNull('status')->first()->pickup_order_id;
                        $pickuporder = DB::table('pickups')->where('pickup_order_id',$pickuporderid)->whereNull('status')->get();
                        $orderdetailsarr = $pickuporder->pluck('order_details_id')->toArray();
                        $adjustedamount = 0;                        
                        foreach($pickuporder as $key=>$details)
                        {
                            if(DB::table('adjustment_table')->whereIn('order_details_id',$orderdetailsarr)->where('adjustment_table.flag','A')->exists())
                            {
                                $adjustedamount = array_sum(DB::table('adjustment_table')->whereIn('order_details_id',$orderdetailsarr)->where('fromtype','D')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->get()->pluck('adjusted_amount')->toArray());
                            }
                        }
                        $orderdetailsrecords = DB::table('order_details')->select('id','product_deposite')->whereIn('id',$orderdetailsarr)->get();
                        foreach($orderdetailsrecords as $key=>$value){
                            $orderdetailsrecords[$key]->product_deposite = RenewalPickupController::fetchCrDrData($value->id,'D');
                        }
                        $totalAmt = array_sum($orderdetailsrecords->pluck('product_deposite')->toArray());
                        // $amounttoberefunded = DB::table('del_orders')->select('TotalAmt')->where('order_id',$pickuporderid)->first()->TotalAmt - $adjustedamount;
                        $amounttoberefunded = $totalAmt - $adjustedamount;
                        DB::table('del_orders')->where('order_id',$pickuporderid)->update(['TotalAmt'=>$amounttoberefunded]);
                    }
                    if(DB::table('pickups')->where('order_details_id',$request->get('inorderproduct'))->whereNull('status')->exists() && $request->get('fromadjust_rent_depo') =='deposit')
                    {
                        // dd("In");
                        $pickuporderid = DB::table('pickups')->where('order_details_id',$request->get('inorderproduct'))->whereNull('status')->first()->pickup_order_id;
                        $pickuporder = DB::table('pickups')->where('pickup_order_id',$pickuporderid)->whereNull('status')->get();
                        $orderdetailsarr = $pickuporder->pluck('order_details_id')->toArray();
                        $adjustedamount = 0;                        
                        foreach($pickuporder as $key=>$details)
                        {
                            if(DB::table('adjustment_table')->whereIn('order_details_id',$orderdetailsarr)->where('adjustment_table.flag','A')->exists())
                            {
                                $adjustedamount = array_sum(DB::table('adjustment_table')->whereIn('order_details_id',$orderdetailsarr)->where('intype','D')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->get()->pluck('adjusted_amount')->toArray());
                            }
                        }
                        $orderdetailsrecords = DB::table('order_details')->select('id','product_deposite')->whereIn('id',$orderdetailsarr)->get();
                        foreach($orderdetailsrecords as $key=>$value){
                            $orderdetailsrecords[$key]->product_deposite = RenewalPickupController::fetchCrDrData($value->id,'D');
                        }
                        // dd($orderdetailsrecords);
                        $totalAmt = array_sum($orderdetailsrecords->pluck('product_deposite')->toArray());

                        // $amounttoberefunded = DB::table('del_orders')->select('TotalAmt')->where('order_id',$pickuporderid)->first()->TotalAmt - $adjustedamount;
                        // dd($totalAmt,$adjustedamount);
                        $amounttoberefunded = $totalAmt - $adjustedamount;
                        DB::table('del_orders')->where('order_id',$pickuporderid)->update(['TotalAmt'=>$amounttoberefunded]);
                    }
                    DB::commit();
                    return redirect()->back()->with('message','Deposit Adjusted Successfully!');
                }
                catch(Exception $e){
                    DB::rollback();
                    Log::channel('intraerrorlog')->info($e);
                    return redirect()->back()->with('message','Something went wrong! Try Again!');
                }
            }
            if($request->get('source') == 'Repair')
            {
                try{
                    DB::beginTransaction();
                    $inorderproduct = DB::table('maintenance_orders')->join('order_details','order_details.id','=','maintenance_orders.order_details_id')->select('maintenance_orders.*')->where('maintenance_orders.id',$request->get('inorderproduct'))->first();
                    $fromorderproduct = DB::table('order_details')->where('id',$request->get('fromorderproduct'))->first();
                    DB::table('adjustment_table')->insert(
                        [
                            "product_id"=>$fromorderproduct->product_id,
                            "order_id"=>$inorderproduct->order_id,
                            "order_details_id"=>$fromorderproduct->id,
                            "adjusted_order_details_id"=>$inorderproduct->id,
                            "fromorderid"=>$fromorderproduct->order_id,
                            "fromtype"=>($request->get('fromadjust_rent_depo') == 'deposit'?'D':($request->get('fromadjust_rent_depo') == 'rent'?'R':'T')),
                            "intype"=>($request->get('inadjust_rent_depo') == 'deposit'?'D':($request->get('inadjust_rent_depo') == 'rent'?'R':'T')),
                            "adjusted_amount"=>$request->get('adjusteddeposit')
                        ]
                    );
                    foreach([
                        "product_id"=>$fromorderproduct->product_id,
                        "order_id"=>$inorderproduct->order_id,
                        "order_details_id"=>$fromorderproduct->id,
                        "adjusted_order_details_id"=>$inorderproduct->id,
                        "fromorderid"=>$fromorderproduct->order_id,
                        "fromtype"=>($request->get('fromadjust_rent_depo') == 'deposit'?'D':($request->get('fromadjust_rent_depo') == 'rent'?'R':'T')),
                        "intype"=>($request->get('inadjust_rent_depo') == 'deposit'?'D':($request->get('inadjust_rent_depo') == 'rent'?'R':'T')),
                        "adjusted_amount"=>$request->get('adjusteddeposit')
                    ] as $key=>$value)
                    {
                        ActivityLog::insert([
                            'order_type'=>'DO',
                            'key_id'=>$inorderproduct->id,
                            'operation'=>'Adjust Deposit',
                            'fields'=>$key,
                            'old_value'=>null,
                            'new_value'=>$value,
                            'updated_by'=>session('username')
                        ]);
                    }
                    if(DB::table('pickups')->where('order_details_id',$request->get('fromorderproduct'))->whereNull('status')->exists() && $request->get('fromadjust_rent_depo') =='deposit')
                    {
                        // dd("from");
                        $pickuporderid = DB::table('pickups')->where('order_details_id',$request->get('fromorderproduct'))->whereNull('status')->first()->pickup_order_id;
                        $pickuporder = DB::table('pickups')->where('pickup_order_id',$pickuporderid)->whereNull('status')->get();
                        $orderdetailsarr = $pickuporder->pluck('order_details_id')->toArray();
                        $adjustedamount = 0;                        
                        foreach($pickuporder as $key=>$details)
                        {
                            if(DB::table('adjustment_table')->whereIn('order_details_id',$orderdetailsarr)->where('adjustment_table.flag','A')->exists())
                            {
                                $adjustedamount = array_sum(DB::table('adjustment_table')->whereIn('order_details_id',$orderdetailsarr)->where('fromtype','D')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->get()->pluck('adjusted_amount')->toArray());
                            }
                        }
                        $orderdetailsrecords = DB::table('order_details')->select('id','product_deposite')->whereIn('id',$orderdetailsarr)->get();
                        foreach($orderdetailsrecords as $key=>$value){
                            $orderdetailsrecords[$key]->product_deposite = RenewalPickupController::fetchCrDrData($value->id,'D');
                        }
                        $totalAmt = array_sum($orderdetailsrecords->pluck('product_deposite')->toArray());
                        // $amounttoberefunded = DB::table('del_orders')->select('TotalAmt')->where('order_id',$pickuporderid)->first()->TotalAmt - $adjustedamount;
                        $amounttoberefunded = $totalAmt - $adjustedamount;
                        DB::table('del_orders')->where('order_id',$pickuporderid)->update(['TotalAmt'=>$amounttoberefunded]);
                    }
                    if(DB::table('pickups')->where('order_details_id',$request->get('inorderproduct'))->whereNull('status')->exists() && $request->get('fromadjust_rent_depo') =='deposit')
                    {
                        // dd("In");
                        $pickuporderid = DB::table('pickups')->where('order_details_id',$request->get('inorderproduct'))->whereNull('status')->first()->pickup_order_id;
                        $pickuporder = DB::table('pickups')->where('pickup_order_id',$pickuporderid)->whereNull('status')->get();
                        $orderdetailsarr = $pickuporder->pluck('order_details_id')->toArray();
                        $adjustedamount = 0;                        
                        foreach($pickuporder as $key=>$details)
                        {
                            if(DB::table('adjustment_table')->whereIn('order_details_id',$orderdetailsarr)->where('adjustment_table.flag','A')->exists())
                            {
                                $adjustedamount = array_sum(DB::table('adjustment_table')->whereIn('order_details_id',$orderdetailsarr)->where('intype','D')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->get()->pluck('adjusted_amount')->toArray());
                            }
                        }
                        $orderdetailsrecords = DB::table('order_details')->select('id','product_deposite')->whereIn('id',$orderdetailsarr)->get();
                        foreach($orderdetailsrecords as $key=>$value){
                            $orderdetailsrecords[$key]->product_deposite = RenewalPickupController::fetchCrDrData($value->id,'D');
                        }
                        // dd($orderdetailsrecords);
                        $totalAmt = array_sum($orderdetailsrecords->pluck('product_deposite')->toArray());

                        // $amounttoberefunded = DB::table('del_orders')->select('TotalAmt')->where('order_id',$pickuporderid)->first()->TotalAmt - $adjustedamount;
                        // dd($totalAmt,$adjustedamount);
                        $amounttoberefunded = $totalAmt - $adjustedamount;
                        DB::table('del_orders')->where('order_id',$pickuporderid)->update(['TotalAmt'=>$amounttoberefunded]);
                    }
                    DB::commit();
                    return redirect()->back()->with('message','Deposit Adjusted Successfully!');
                }
                catch(Exception $e){
                    DB::rollback();
                    Log::channel('intraerrorlog')->info($e);
                    return redirect()->back()->with('message','Something went wrong! Try Again!');
                }
            }
        }
        else if($request->get('request_type') == 'adjust-againts-damage'){
            // dd($request->all());
            DB::table('cr_dr_note')->insert([
                'order_id'=>$request->get('dmsubmit'),
                'order_details_id'=>$request->get('inorderproductdm'),
                'crdrtype'=>'Dr',
                'intype'=>'DM',
                'amount'=>$request->get('adjusted_deposit_dm'),
                'createdby'=>session('username')
            ]);
            $amount = DB::table('del_orders')->where('order_id',$request->get('dmsubmit'))->first()->TotalAmt;
            DB::table('del_orders')->where('order_id',$request->get('dmsubmit'))->update(['TotalAmt'=>$amount - $request->get('adjusted_deposit_dm')]);
            return redirect()->back()->with('message','Deposit Adjusted Successfully!');
        }
    }

    public function orderImages(Request $request,$orderid){
        if(DB::table('del_orders')->where('order_id',$orderid)->whereNotNull('product_delivered')->exists())
        {
            $getImages = DB::table('del_orders')->where('order_id',$orderid)->get();        
            $getImages = json_decode($getImages[0]->product_delivered,true);
            return $getImages;
        }
        else
        {
            return false;
        }
        //88766747
    }

    public function orderOtherExpense(Request $request){
        if($request->method()=='POST'){
            try{
                DB::beginTransaction();
                $exp_type = $request->get('expense_type');
                if($request->get('expense_type')=='Other'){
                    $exp_type = $request->get('other_exp_type');
                }
                DB::table('del_orders')->where('order_id',$request->get('order_id'))->update(['expense_type'=>$exp_type,'expense_amt'=>$request->get('expense_amt')]);
                DB::commit();
                return redirect()->back()->with('message','Expense Uploaded Successfully!');
            }catch(Exception $e){
                DB::rollback();
                Log::channel('intraerrorlog')->info($e);
                return redirect()->back()->with('message','Something went wrong! Try Again!');
            }
        }else{
            $order = DB::table('del_orders')->where('order_id',$request->get('order_id'))->get(['expense_type','expense_amt']);
            return $order;
        }
        
    }
    public function cr_dr_data(Request $request){
        $details = DB::table('cr_dr_note')
                        ->join('del_orders','del_orders.order_id','=','cr_dr_note.order_id')
                        ->join('order_details','order_details.id','=','cr_dr_note.order_details_id')
                        ->join('products','products.id','=','order_details.product_id')
                        ->select(
                            'del_orders.order_id',
                            'del_orders.shipping_first_name',
                            'del_orders.patient_name',
                            'del_orders.DelDate',
                            'del_orders.mobileno',
                            'products.product_name',
                            'order_details.current_status',
                            'order_details.unique_id',
                            'order_details.product_rent',
                            'order_details.product_deposite',
                            'order_details.transport',
                            'order_details.remark',
                            'del_orders.comment',
                            'cr_dr_note.*'
                        )
                        ->where('cr_dr_note.flag','A')
                        ->when($request->get('orderid'),function($query)use($request){
                            $query->where('del_orders.order_id',$request->get('orderid'));
                        })
                        ->when($request->get('customername'),function($query)use($request){
                            $query->where('del_orders.shipping_first_name','LIKE','%'.$request->get('customername').'%');
                        })
                        ->when($request->get('patientname'),function($query)use($request){
                            $query->where('del_orders.patient_name','LIKE','%'.$request->get('patientname').'%');
                        })
                        ->when($request->get('contactno'),function($query)use($request){
                            $query->where('del_orders.mobileno','LIKE','%'.$request->get('contactno').'%');
                        })
                        ->when($request->get('orderstartdate') && $request->get('orderenddate'),function($query)use($request){
                            $temp_from_min_date = date('d-m-Y',strtotime($request->get('orderstartdate')));
                            $temp_from_max_date = date('d-m-Y',strtotime($request->get('orderenddate')));
                            $query->whereBetween(DB::raw("STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y')"),[DB::raw("STR_TO_DATE('$temp_from_min_date','%d-%m-%Y')"),DB::raw("STR_TO_DATE('$temp_from_max_date','%d-%m-%Y')")]);
                        })
                        ->orderBy('cr_dr_note.id','DESC')
                        ->get()
                        ->groupBy('order_id')
                        ->paginate(10);
        // dd($details);
        return view('Reports.cr-dr-report',compact('details'));
    }
    public function uploadImage(Request $request){
        if($_FILES['uploadimagecrdrnote']['name']!=null)
        {
            $uploadimagecrdrnote = $_FILES['uploadimagecrdrnote']['name'];
            //print_r($_FILES['shop_images']['name']);
            $targetDir = "assets/uploads/crdr_images/";
            $fileName = basename($_FILES['uploadimagecrdrnote']['name']);
            $targetFilePath = $targetDir . $fileName;
            $fileType =strtolower(pathinfo($targetFilePath,PATHINFO_EXTENSION));
            $new_file_name = $targetDir."".$request->get('hidden_order_id_crdr_img').".".$fileType;
            move_uploaded_file($_FILES["uploadimagecrdrnote"]["tmp_name"], $new_file_name);    
            $uploadimagecrdrnote_filePath = $request->get('hidden_order_id_crdr_img').".".$fileType;
            $insertData = [
                'order_type'=>"DE",
                'key_id'=>$request->get('hidden_order_id_crdr_img'),
                'operation'=>'Update Order CRDR Payment Details',
                'fields'=>'cr_dr_img',
                'old_value'=>null,
                'new_value'=>$uploadimagecrdrnote_filePath,
                'updated_by'=>session('username')
                ];
            ActivityLog::insert($insertData);
            
            DelOrders::where('order_id',$request->get('hidden_order_id_crdr_img'))->update(['cr_dr_img'=>$uploadimagecrdrnote_filePath]);
            return redirect()->back()->with('message','Image Uploaded Successfully!');
        }
    }

    public function corporateRenewal(Request $request){
        if($request->has('request_type')){
            try{
                if(DB::table('corporate_renewal')->where('invoice_no',$request->get('invoice_no'))->exists()){
                    $renewal_details = DB::table('corporate_renewal')
                        ->join('order_details','order_details.id','=','corporate_renewal.order_details_id')
                        ->join('del_orders','del_orders.order_id','=','order_details.order_id')
                        ->join('products','products.id','=','order_details.product_id')
                        ->select('corporate_renewal.*','products.product_name','del_orders.shipping_first_name','del_orders.patient_name','del_orders.mobileno','del_orders.fulldetails')
                        ->where('corporate_renewal.invoice_no',$request->get('invoice_no'))
                        ->get();
                    return ['status'=>'success','timestamp'=>Carbon::now(),'user'=>session('username'),'description'=>'Found','data'=>$renewal_details];    
                }else{
                    return ['status'=>'error','timestamp'=>Carbon::now(),'user'=>session('username'),'description'=>'No Records Found','data'=>'data'];
                }
            }
            catch(Exception $e){
                return ['status'=>'error','timestamp'=>Carbon::now(),'user'=>session('username'),'description'=>$e];
                Log::channel('intraerrorlog')->info($e);
            }
        }else{
            DB::beginTransaction();
            try{
                $renewal_details = DB::table('corporate_renewal')
                        ->join('order_details','order_details.id','=','corporate_renewal.order_details_id')
                        ->join('del_orders','del_orders.order_id','=','order_details.order_id')
                        ->join('products','products.id','=','order_details.product_id')
                        ->select('corporate_renewal.*','del_orders.lead_id','del_orders.patient_name','del_orders.shipping_first_name','del_orders.location','del_orders.mobileno','del_orders.fulldetails','products.product_name','order_details.order_id','order_details.id as order_details_id','order_details.product_id','order_details.vendor_id')
                        ->where('corporate_renewal.invoice_no',$request->get('corppay_invoice_no'))
                        ->get();
                // dd($request->all(),$renewal_details);
                $renewid = DB::table('del_orders')->insertGetId([
                    'status' => 'Collected',
                    'deliverypickup' => 'Collection',
                    'DelAssignedTo' =>'Completed',
                    'lead_id'=>$renewal_details[0]->lead_id,
                    'invoice_no'=>$renewal_details[0]->invoice_no,
                    'patient_name'=>$renewal_details[0]->patient_name,
                    'shipping_first_name' => $renewal_details[0]->shipping_first_name,
                    'location' => $renewal_details[0]->location,
                    'mobileno' => $renewal_details[0]->mobileno,
                    'line_item_1' =>implode(',',$renewal_details->pluck('product_name')->toArray()),
                    'DelDate' =>date('d-m-Y',strtotime($request->get('corpay_date'))),
                    'Collection_Date' =>date('Y-m-d',strtotime($request->get('corpay_date'))),
                    'TotalAmt' => array_sum($renewal_details->pluck('amount')->toArray()),
                    'fulldetails'=> $renewal_details[0]->fulldetails,
                    'TravelMode' =>'Pending',
                    'PaymentMode'=>'Online',
                    'cash'=>0,
                    'online'=>0,
                    'PickupLocation' =>'Customer',
                    'reference_id'=>$request->get('corpay_reference_id'),
                    'order_approval_status' =>'Approved'
                ]);
                $filePath = null;
                // dd($_FILES['corpay_payment_img']['name']);
                if($_FILES['corpay_payment_img']['name']!=null)
                {
                    $reference_image = $_FILES['corpay_payment_img']['name'];
                    $image_name = $renewid."-".date('Y-m-d');
                    //print_r($_FILES['shop_images']['name']);
                    $targetDir = "assets/uploads/payment_images/";
                    $fileName = basename($_FILES['corpay_payment_img']['name']);
                    $targetFilePath = $targetDir . $fileName;
                    $fileType =strtolower(pathinfo($targetFilePath,PATHINFO_EXTENSION));
                    $new_file_name = $targetDir."".$image_name.".".$fileType;
                    move_uploaded_file($_FILES["corpay_payment_img"]["tmp_name"], $new_file_name);    
                    $filePath = "".$image_name.".".$fileType;
                    // $filePath = "/devweb/eflow/assets/uploads/payment_images/".$reference_id.".".$fileType;                    
                    //array_push($img_path,$filePath);
                    //$reference_image_path= json_encode($img_path);
                    DB::table('del_orders')->where('order_id',$renewid)->update(['payment_image'=>$filePath]);
                }
                foreach($renewal_details as $key=>$renewal){
                    DB::table('renewals')->insert([
                        'collection_order_id'=>$renewid,
                        'order_id'=>$renewal->order_id,
                        'order_details_id'=>$renewal->order_details_id,
                        'lead_id'=>$renewal->lead_id,
                        'vendor_id'=>$renewal->vendor_id,
                        'product_id'=>$renewal->product_id,
                        'start_date'=>$renewal->start_date,
                        'end_date'=> $renewal->end_date,
                        'payment_mode'=>'Online',
                        'cash_amount'=>0,
                        'online_amount'=>$renewal->amount,
                        'discount_amt'=>0,
                        'total_amt'=>$renewal->amount,
                        'status'=>'Online Renewed',
                        'payment_status'=>'Recieved',
                        'image_path'=>$filePath,
                        'reference_id'=>$request->get('corpay_reference_id'),
                        'created_by'=>session('username')
                    ]);
                    DB::table('order_details')->where('id',$renewal->order_details_id)->update(['pickup_date'=>$renewal->end_date,'current_status'=>'Renewed']);
                }
                DB::commit();
                return redirect()->back()->with('message','Renewal Generated Successfully!');
            }
            catch(Exception $e){
                Log::channel('intraerrorlog')->info($e);
                DB::rollback();
                return redirect()->back()->with('error','Something went wrong!');
            }
        }
    }

    public function reverseAdjustment($id){
        DB::beginTransaction();
        try{
            DB::table('adjustment_table')->where('id',$id)->update(['flag'=>'I']);
            $rowDetails = DB::table('adjustment_table')->where('id',$id)->first();
            // dd($rowDetails);
            if(DB::table('pickups')->where('order_details_id',$rowDetails->order_details_id)->whereNull('status')->exists())
            {
                // dd("from");
                $pickuporderid = DB::table('pickups')->where('order_details_id',$rowDetails->order_details_id)->whereNull('status')->first()->pickup_order_id;
                $pickuporder = DB::table('pickups')->where('pickup_order_id',$pickuporderid)->whereNull('status')->get();
                $orderdetailsarr = $pickuporder->pluck('order_details_id')->toArray();
                $adjustedamount = 0;                        
                foreach($pickuporder as $key=>$details)
                {
                    if(DB::table('adjustment_table')->whereIn('order_details_id',$orderdetailsarr)->where('adjustment_table.flag','A')->exists())
                    {
                        $adjustedamount = array_sum(DB::table('adjustment_table')->whereIn('order_details_id',$orderdetailsarr)->where('fromtype','D')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->get()->pluck('adjusted_amount')->toArray());
                    }
                }
                $orderdetailsrecords = DB::table('order_details')->select('id','product_deposite')->whereIn('id',$orderdetailsarr)->get();
                foreach($orderdetailsrecords as $key=>$value){
                    $orderdetailsrecords[$key]->product_deposite = RenewalPickupController::fetchCrDrData($value->id,'D');
                }
                $totalAmt = array_sum($orderdetailsrecords->pluck('product_deposite')->toArray());
                // $amounttoberefunded = DB::table('del_orders')->select('TotalAmt')->where('order_id',$pickuporderid)->first()->TotalAmt - $adjustedamount;
                // dd($totalAmt,$adjustedamount);
                $amounttoberefunded = $totalAmt - $adjustedamount;
                DB::table('del_orders')->where('order_id',$pickuporderid)->update(['TotalAmt'=>$amounttoberefunded]);
            }
            if(DB::table('pickups')->where('order_details_id',$rowDetails->adjusted_order_details_id)->whereNull('status')->exists())
            {
                // dd("In");
                $pickuporderid = DB::table('pickups')->where('order_details_id',$rowDetails->adjusted_order_details_id)->whereNull('status')->first()->pickup_order_id;
                $pickuporder = DB::table('pickups')->where('pickup_order_id',$pickuporderid)->whereNull('status')->get();
                $orderdetailsarr = $pickuporder->pluck('order_details_id')->toArray();
                $adjustedamount = 0;                        
                foreach($pickuporder as $key=>$details)
                {
                    if(DB::table('adjustment_table')->whereIn('order_details_id',$orderdetailsarr)->where('adjustment_table.flag','A')->exists())
                    {
                        $adjustedamount = array_sum(DB::table('adjustment_table')->whereIn('order_details_id',$orderdetailsarr)->where('intype','D')->whereNotIn('adjusted_amount',[0])->where('adjustment_table.flag','A')->get()->pluck('adjusted_amount')->toArray());
                    }
                }
                $orderdetailsrecords = DB::table('order_details')->select('id','product_deposite')->whereIn('id',$orderdetailsarr)->get();
                foreach($orderdetailsrecords as $key=>$value){
                    $orderdetailsrecords[$key]->product_deposite = RenewalPickupController::fetchCrDrData($value->id,'D');
                }
                // dd($orderdetailsrecords);
                $totalAmt = array_sum($orderdetailsrecords->pluck('product_deposite')->toArray());
    
                // $amounttoberefunded = DB::table('del_orders')->select('TotalAmt')->where('order_id',$pickuporderid)->first()->TotalAmt - $adjustedamount;
                // dd($totalAmt,$adjustedamount);
                $amounttoberefunded = $totalAmt - $adjustedamount;
                DB::table('del_orders')->where('order_id',$pickuporderid)->update(['TotalAmt'=>$amounttoberefunded]);
            }

            DB::commit();
            return redirect()->back()->with('message','Adjustment Reversed!');
        }
        catch(Exception $ex){
            DB::rollback();
            return redirect()->back()->with('error',$ex->getMessage());
        }
    }
    public function editCollection(Request $request){
        $orderTypeNotIn = config('app.order_type');
        $order_details = DB::table('renewals')
                            ->join('order_details','renewals.order_details_id','=','order_details.id')
                            ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                            ->join('del_orders','renewals.collection_order_id','=','del_orders.order_id')
                            ->join('products','renewals.product_id','=','products.id')
                            ->join('vendor_details','order_details.vendor_id','=','vendor_details.id')
                            ->join('vendor_warehouse','order_details.vendor_warehouse_id','=','vendor_warehouse.id')
                            ->select(
                                'del_orders.DelDate as date',
                                'products.product_name as product_name',
                                'vendor_details.registered_name as vendor_name',
                                'vendor_warehouse.wh_name as warehouse_name',
                                'vendor_warehouse.wh_area as warehouse_area',
                                'vendor_warehouse.wh_city as warehouse_city',
                                'vendor_warehouse.wh_pincode as warehouse_pincode',
                                'order_details.product_rent as product_rent',
                                'order_details.unique_id',
                                'order_details.sale_rental',
                                'renewals.start_date as start_date',
                                'renewals.end_date as end_date',
                                'renewals.adjusted_deposit',
                                'renewals.discount_amt',
                                'renewals.id as renewal_id',
                                'renewals.cash_amount',
                                'renewals.online_amount',
                                'del_orders.TotalAmt as total_amount',
                                'del_orders.del_total_amount as received_total_amount',
                                'del_orders.*',
                                'customer_details.*'
                            )
                            ->whereNotIn('renewals.status',['Cancel'])
                            ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                            ->where('renewals.collection_order_id',$request->get('order_id'))
                            ->get();
        foreach($order_details as $key=>$order)
        {
            if($order->PaymentMode == "Cash"){
                $order_details[$key]->cash_amount = $this->fetchCrDrDataRE($order->renewal_id,"Cash");
            }  
            else{
                $order_details[$key]->online_amount = $this->fetchCrDrDataRE($order->renewal_id,"Online");
            }
        }
        return $order_details;
    }
    public function updateCollection(Request $request){
        $order_id = $request->get('order_id');
        $order = DB::table('del_orders')->where('del_orders.order_id',$request->get('order_id'))->first();
        $renewal_id = $request->get('renewal_id');
        $actual_rent = $request->get('actual_rent');
        $updated_rent = $request->get('updated_rent');
        foreach($renewal_id as $key=>$id){
            if($actual_rent[$key] != $updated_rent[$key]){
                DB::table('cr_dr_note')->insert([
                    'order_id' => $order_id,
                    'order_details_id' => $id,
                    'crdrtype' => ($actual_rent[$key] > $updated_rent[$key])?"Cr":"Dr",
                    'intype' => 'RE',
                    'amount' => ($actual_rent[$key] > $updated_rent[$key])?$actual_rent[$key] - $updated_rent[$key]:$updated_rent[$key] - $actual_rent[$key],
                    'createdby'=> session('username')
                ]);
            }
        }
        $total = array_sum($request->get('updated_rent'));
        DB::table('del_orders')->where('order_id',$order_id)->update(['TotalAmt'=>$total]);
        return redirect()->back()->with('message','Success!');
    }
    public static function fetchCrDrDataRE($id,$payment_mode)
    {
        $order_details = DB::table('renewals')->where('id',$id)->first();
        if(DB::table('cr_dr_note')->where('order_details_id',$order_details->id)->where('intype','RE')->where('flag','A')->exists())
        {
            $cr_dr_notes = DB::table('cr_dr_note')->where('order_details_id',$order_details->id)->where('intype','RE')->get()->groupBy('crdrtype');
            if(isset($cr_dr_notes['Cr']))
            {
                $creditnotes = $cr_dr_notes['Cr']->groupBy('intype');
                if($payment_mode == 'Cash'){
                    $order_details->cash_amount = $order_details->cash_amount - array_sum($creditnotes['RE']->pluck('amount')->toArray());
                }else{
                    $order_details->online_amount = $order_details->online_amount - array_sum($creditnotes['RE']->pluck('amount')->toArray());
                }
            }
            if(isset($cr_dr_notes['Dr']))
            {
                $debitnotes = $cr_dr_notes['Dr']->groupBy('intype');
                if($payment_mode == 'Cash'){
                    $order_details->cash_amount = $order_details->cash_amount + array_sum($debitnotes['RE']->pluck('amount')->toArray());
                }else{
                    $order_details->online_amount = $order_details->online_amount + array_sum($debitnotes['RE']->pluck('amount')->toArray());
                }
            }
        }
        if($payment_mode == 'Cash'){
            return $order_details->cash_amount;
        }else{
            return $order_details->online_amount;
        }
    }
    public function getActivityLog(Request $request){
        
        $order_type = ($request->get('order_type') == 'Delivery'?'DO':($request->get('order_type') == 'Collection'?'CO':($request->get('order_type') == 'Pick Up'?'PU':'COM')));
        $order_activity_logs = DB::table('activity_log')->where('key_id',$request->get('order_id'))->get();
        // return $order_activity_logs;
        if($order_type == 'DO'){
            $order_details_id = DB::table('order_details')->where('order_id',$request->get('order_id'))->get()->pluck('id');
            $order_details_al = DB::table('activity_log')->whereIn('order_type',['DO','OD'])->whereIn('key_id',$order_details_id)->get();

            foreach($order_details_al as $key=>$value){
                if($value->fields == 'vendor'){
                    $order_details_al[$key]->old_value = DB::table('vendor_details')->where('id',$value->old_value)->first()->registered_name;
                    $order_details_al[$key]->new_value = DB::table('vendor_details')->where('id',$value->new_value)->first()->registered_name;
                }elseif($value->fields == 'warehouse'){
                    $warehouse_old = DB::table('vendor_warehouse')->where('id',$value->old_value)->first();
                    $warehouse_new = DB::table('vendor_warehouse')->where('id',$value->new_value)->first();
                    $order_details_al[$key]->old_value = $warehouse_old->wh_name.", ".$warehouse_old->wh_area.", ".$warehouse_old->wh_city;
                    $order_details_al[$key]->new_value = $warehouse_new->wh_name.", ".$warehouse_new->wh_area.", ".$warehouse_new->wh_city;                    
                }elseif($value->fields == 'brand'){
                    $order_details_al[$key]->old_value = DB::table('product_brands')->where('id',$value->old_value)->first()->brand_name;
                    $order_details_al[$key]->new_value = DB::table('product_brands')->where('id',$value->new_value)->first()->brand_name;
                }else{
                    continue;
                }
            }

            return $order_activity_logs->merge($order_details_al);

            // $unsorted = $order_activity_logs->merge($order_details_al);
            // $col = "updated_at";
            // return $unsorted->sortBy(function($col) {
            //     return $col;
            // })->values()->all();
            // foreach($order_activity_logs)
        }else{
            return $order_activity_logs;
        }
        // elseif($order_type == 'CO'){
            
        // }else{

        // }
    }
}
?>