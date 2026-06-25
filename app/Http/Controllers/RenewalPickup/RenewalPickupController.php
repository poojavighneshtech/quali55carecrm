<?php

namespace App\Http\Controllers\RenewalPickup;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\VendorRegister;
use App\Models\UserRegister;
use App\Models\DelOrders;
use App\Models\OrderDetails;
use App\Models\LinkCustDetails;
use App\Models\RenewalReminder;
use App\Models\customer_detail;
use App\Models\Renewal;
use App\Models\Pickup;
use App\Models\ShortUrl;
use App\Models\ActivityLog;
use App\Models\leads_log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\DeliveryManagement\DeliveryController;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use PDF;
use Mail;
use Session;
use DateTime;
use Carbon\Carbon;
use Log;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

//use other controler
use App\Http\Controllers\Leads\LeadController;
use App\Exports\RenewalPickupExportTest;

use Maatwebsite\Excel\Facades\Excel;
//use App\Imports\UsersImport;
use App\Exports\RenewalPickupExport;


class RenewalPickupController extends Controller
{
    public function isLoggedIn()
    {
        $data = session('isLoggedIn');
        return $data;
    }

    //paginate apply
    public function paginate($items, $perPage = 10, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function customer_products($customer_id)
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
            return redirect()->to($url);
        }
        $products = DB::select("SELECT 
            order_details.pickup_date as pickup_date,
            order_details.creation_date as creation_date, 
            order_details.product_rent as product_rent, 
            order_details.product_deposite as product_deposite, 
            order_details.order_id as order_id,
            order_details.product_id as product_id,
            order_details.id as order_details_id,
            order_details.customer_id as customer_id,
            products.product_name as product_name,
            customer_details.customer_name as customer_name,
            customer_details.citygroup as city
            FROM order_details,products,customer_details 
            where order_details.customer_id = $customer_id 
                AND customer_details.cust_id = $customer_id
                AND order_details.product_id = products.id
                AND order_details.sale_rental ='Rental'
                AND order_details.current_status ='Pending' ");
        $data['customer_id'] = $customer_id;
        $data['products'] = json_decode(json_encode($products),true);
        //$data['customer_name'] = $data['products'][0]['customer_name'];
        //print_r($data['products']);
        return view('RenewalPickup/customer_products',$data);
    }

    public function order_data()
    {  
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
         return redirect()->to($url);
        } 
        //print_r($_POST);
        // $customer_id = $_POST['customer_id'];
        // $cust_data= $_POST['customer_data'];
        // $data['customer_data'] = json_decode($cust_data);
        // $count = 0;
        // $action = "";
      
        // for ($i=0; $i < count($data['customer_data']); $i++) { 
                
        //     if(($data['customer_data'][$i][7])!=null)
        //     {
        //         if($data['customer_data'][$i][7]=="renewal")
        //         {
        //             $id = $data['customer_data'][$i][0];
        //             $pickup_date = $data['customer_data'][$i][4];
        //             $renewal_date = date('Y-m-d',strtotime('+30 days'));
        //             $product_name = $data['customer_data'][$i][3];
        //             $product_rent = $data['customer_data'][$i][5];
        //             $customer_info = DB::select("SELECT * FROM customer_details WHERE cust_id=$customer_id");
        //             $data['customer_info'] = json_decode(json_encode($customer_info),true);
        //             $customer_name = $data['customer_info'][0]['customer_name'];
        //             $contact_no = $data['customer_info'][0]['primary_contact_no'];
        //             $customer_email = $data['customer_info'][0]['email_id'];
        //             //echo $customer_email;
        //             $update_order = DB::update("UPDATE order_details SET pickup_date = '$renewal_date' where id=$id");
        //             $data_message = array(
        //                 'customer_email'=>"'".$customer_email."'",
        //                 'customer_name'=>"'".$customer_name."'",
        //                 'product_name'=>"'".$product_name."'",
        //                 'product_rent'=>"'".$product_rent."'",
        //                 'pickup_date'=>"'".$pickup_date."'",
        //                 'renewal_date'=>"'".$renewal_date."'");

        //            // Sending mail to customer about renewal of rental product....
        //             Mail::send('RenewalPickupMail/renewal_mail',$data_message, function($message) use ($customer_email)
        //             {     
                        
        //                 $message->to($customer_email, 'Product Renewal Mail')->subject('Product Renewal Mail');
        //                 $message->from('tempmailquali@gmail.com', 'Quali55Care');
        //             });
        //             //print_r($data_message);
        //             //echo "success";
        //         }
        //         elseif($data['customer_data'][$i][7]=="pickup")
        //         {
        //             $id = $data['customer_data'][$i][0];
        //             $pickup_date = $data['customer_data'][$i][4];
        //             $renewal_date = date('Y-m-d',strtotime('+30 days'));
        //             $product_name = $data['customer_data'][$i][3];
        //             $product_rent = $data['customer_data'][$i][5];
        //             $customer_info = DB::select("SELECT * FROM customer_details WHERE cust_id=$customer_id");
        //             $data['customer_info'] = json_decode(json_encode($customer_info),true);
        //             $customer_name = $data['customer_info'][0]['customer_name'];
        //             $contact_no = $data['customer_info'][0]['primary_contact_no'];
        //             $customer_email = $data['customer_info'][0]['email_id'];

        //             //print_r($data['customer_info']);
        //         }
               
        //     }
        //     else
        //     {
        //         echo "back";
        //         return Redirect::back();
        //     }
            
        //}
       
        
    }

    //-----add comment in customer table
    public function add_customer_comment($user_id,$customer_id,$desc)
    {
       $isLoggedIn = $this->isLoggedIn();
       if($isLoggedIn == 'false')
       {
           $url = url('/');
        return redirect()->to($url);
       }
       $customer_details = new customer_detail();
       //$leads->where('id',$id)->delete();
       $timestamp = date("d M, h:i A");
       $comment = "[".$timestamp."]".$desc."\n";
       $comment_status = [
          'comment' => $comment
       ];
       $cmt_check = DB::select("SELECT comment FROM customer_details WHERE cust_id = '$customer_id' ");
       $data['cmt_check'] = json_decode(json_encode($cmt_check), true);
    
       if(isset($data['cmt_check'][0]['comment']))
       {
            //$cmt_update = "UPDATE customer_details SET comment = CONCAT(comment, '$comment') WHERE cust_id = '$customer_id' ";
            $cmt_update = DB::update("UPDATE customer_details SET comment = CONCAT('$comment',comment) WHERE cust_id = '$customer_id' ");
         
            //$customer_details->where('cust_id',$customer_id)->update($comment_status);
       }
       else
       {
         //print_r($comment_status);
         $customer_details->where('cust_id',$customer_id)->update($comment_status);
       }
       //$cmt_update = "UPDATE customer_details SET comment = CONCAT(comment, '$comment') WHERE cust_id = '$customer_id' ";
       //return redirect('/view_all_inprocess_leads')->with('message', 'comment add Successfully');
       return redirect()->back()->with('message', 'comment add Successfully');
       //return $this->viewAllLeads();
    }

    public function send_perticular_reminder($cust_id,$product_name,$pickup_date,$product_rent)
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
         return redirect()->to($url);
        }
        $customer_details = new customer_detail();
        $get_email =  DB::select("SELECT email_id,customer_name FROM customer_details where cust_id = '$cust_id' ");
        $data['get_email'] = json_decode(json_encode($get_email),true);
        $email_id = $data['get_email'][0]['email_id'];
        $customer_name = $data['get_email'][0]['customer_name'];
            
            $data_message = array(
                    'customer_email'=>$email_id,
                    'customer_name'=>$customer_name,
                    'product_name'=>$product_name,
                    'product_rent'=>$product_rent,
                    'pickup_date'=>$pickup_date,);

                // Sending mail to customer about renewal of rental product....
            Mail::send('RenewalPickupMail/send_reminder',$data_message, function($message) use ($email_id)
            {     
                $message->to($email_id, 'Quali55Care -REMINDER')->subject('Quali55Care -REMINDER');
                $message->from('tempmailquali@gmail.com', 'Quali55Care');
            });

         //-----Comment added-----//
        $timestamp = date("d M, h:i A");
        $comment = "[".$timestamp."]Reminder sent for product : ".$product_name."\n";
        $comment_status = [
            'comment' => $comment
        ];
        $cmt_check = DB::select("SELECT comment FROM customer_details WHERE cust_id = '$cust_id' ");
        $data['cmt_check'] = json_decode(json_encode($cmt_check), true);
        
        if(isset($data['cmt_check'][0]['comment']))
        {
            //$cmt_update = "UPDATE customer_details SET comment = CONCAT(comment, '$comment') WHERE cust_id = '$cust_id' ";
            $cmt_update = DB::update("UPDATE customer_details SET comment = CONCAT('$comment',comment) WHERE cust_id = '$cust_id' ");
            
            //$customer_details->where('cust_id',$customer_id)->update($comment_status);
        }
        else
        {
            //print_r($comment_status);
            $customer_details->where('cust_id',$cust_id)->update($comment_status);
        }
            return redirect()->back()->with('message', 'Reminder Sent Successfully')
                                    ->with('pop_per_cust_name',$customer_name)
                                    ->with('pop_per_product_name',$product_name)
                                    ->with('pop_per_product_rent',$product_rent)
                                    ->with('pop_per_pickup_date',$pickup_date);
    }

    public function send_reminder()
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
         return redirect()->to($url);
        }
        //print_r($_POST);
        $customer_id = $_POST['pop_cust_id'];
        $customer_name = $_POST['pop_cust_name'];
        $product_name = $_POST['product_name'];
        $pickup_date = $_POST['pickup_date'];
        $product_rent = $_POST['product_rent'];
        $due_month_count = $_POST['due_month_count'];
        $total_due_month_rent = $_POST['total_due_month_rent'];
        $customer_details = new customer_detail();

        $mail_data = array();
        $total_rent = 0;
        for ($i=0; $i <count($product_name) ; $i++) 
        { 
            $mail_data['product_name'][$i] = $product_name[$i];
            $mail_data['pickup_date'][$i] = $pickup_date[$i];
            $mail_data['product_rent'][$i] = $product_rent[$i];
            $mail_data['due_months'][$i] = $due_month_count[$i];
            $mail_data['total_due_month_rent'][$i] = $total_due_month_rent[$i];
            $total_rent += $total_due_month_rent[$i];
        }
        //print_r($mail_data);

        $get_email =  DB::select("SELECT email_id FROM customer_details where cust_id = '$customer_id' ");
        $data['get_email'] = json_decode(json_encode($get_email),true);
        $email_id = $data['get_email'][0]['email_id'];
        // Sending mail to customer about renewal of rental product....
        //$mail_data = json_encode($mail_data);
        $data_message = array(
            'customer_email'=>$email_id,
            'customer_name'=>$customer_name,
            'total_rent'=>$total_rent,);
            $data_message['mail_data'] = $mail_data;
    
        Mail::send('RenewalPickupMail/send_product_reminder',$data_message, function($message) use ($email_id)
        {     
            $message->to($email_id, 'Quali55Care -REMINDER')->subject('Quali55Care -REMINDER');
            $message->from('tempmailquali@gmail.com', 'Quali55Care');
        });

        for ($i=0; $i <count($mail_data['product_name']) ; $i++) { 
            $timestamp = date("d M, h:i A");
            $p_data = $mail_data['product_name'][$i];
            $comment = "[".$timestamp."]Reminder sent for product : ".$p_data."\n";
            $comment_status = [
                'comment' => $comment
            ];
            $cmt_check = DB::select("SELECT comment FROM customer_details WHERE cust_id = '$customer_id' ");
            $data['cmt_check'] = json_decode(json_encode($cmt_check), true);
            
            if(isset($data['cmt_check'][0]['comment']))
            {
                //$cmt_update = "UPDATE customer_details SET comment = CONCAT(comment, '$comment') WHERE cust_id = '$cust_id' ";
                $cmt_update = DB::update("UPDATE customer_details SET comment = CONCAT('$comment',comment) WHERE cust_id = '$customer_id' ");
                
                //$customer_details->where('cust_id',$customer_id)->update($comment_status);
            }
            else
            {
                $customer_details->where('cust_id',$customer_id)->update($comment_status);
            }
        }
        return redirect()->back()->with('reminder_msg', 'Reminder Sent Successfully');
                                //->with('pop_cust_name',$customer_name)
                                //->with('pop_total_rent',$total_rent)
                                //->with(['pop_data'=>$mail_data]);
        
    }
 
    public function renewal_pickup_product(Request $request)
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
         return redirect()->to($url);
        }
        if(isset($_POST['check']))
        {   
            $customer_details = new customer_detail();
            $check = $_POST['check'];
            $customer_id = $_POST['customer_id'];
            $order_id = $_POST['order_id'];
            $order_details_id = $_POST['order_details_id'];
            $product_id = $_POST['product_id'];
            $product_name = $_POST['product_name'];
            $customer_name = $_POST['customer_name'];
            $pickup_date = $_POST['pickup_date'];
            $product_rent = $_POST['product_rent'];
            $product_deposite = $_POST['product_deposite'];
            $due_month_count = $_POST['due_month_count'];
            $total_due_month_rent = $_POST['total_due_month_rent'];
            $renewal_pickup_btn = $_POST['renewal_pickup_btn'];

            //print_r($_POST);
            
            //------send reminder from view products-> send reminder---------//
            if($_POST['renewal_pickup_btn']=='Send_Reminder')
            {   
                $mail_data = array();
                $total_rent = 0;
                for ($i=0; $i <count($check) ; $i++) { 
                    $temp = $check[$i];

                    $mail_data['product_name'][$i] = $product_name[$temp];
                    $mail_data['pickup_date'][$i] = $pickup_date[$temp];
                    $mail_data['product_rent'][$i] = $product_rent[$temp];
                    $total_rent += $product_rent[$temp];
                }

                $get_email =  DB::select("SELECT email_id FROM customer_details where cust_id = '$customer_id' ");
                $data['get_email'] = json_decode(json_encode($get_email),true);
                $email_id = $data['get_email'][0]['email_id'];
                // Sending mail to customer about renewal of rental product....
                //$mail_data = json_encode($mail_data);
                $data_message = array(
                    'customer_email'=>$email_id,
                    'customer_name'=>$customer_name,
                    'total_rent'=>$total_rent,);
                    $data_message['mail_data'] = $mail_data;
            
                Mail::send('RenewalPickupMail/send_product_reminder',$data_message, function($message) use ($email_id)
                {     
                    $message->to($email_id, 'Quali55Care -REMINDER')->subject('Quali55Care -REMINDER');
                    $message->from('tempmailquali@gmail.com', 'Quali55Care');
                });

                for ($i=0; $i <count($mail_data) ; $i++) { 
                    $timestamp = date("d M, h:i A");
                    $p_data = $mail_data['product_name'][$i];
                    $comment = "[".$timestamp."]Reminder sent for product : ".$p_data."\n";
                    $comment_status = [
                        'comment' => $comment
                    ];
                    $cmt_check = DB::select("SELECT comment FROM customer_details WHERE cust_id = '$customer_id' ");
                    $data['cmt_check'] = json_decode(json_encode($cmt_check), true);
                    
                    if(isset($data['cmt_check'][0]['comment']))
                    {
                        //$cmt_update = "UPDATE customer_details SET comment = CONCAT(comment, '$comment') WHERE cust_id = '$cust_id' ";
                        $cmt_update = DB::update("UPDATE customer_details SET comment = CONCAT('$comment',comment) WHERE cust_id = '$customer_id' ");
                        
                        //$customer_details->where('cust_id',$customer_id)->update($comment_status);
                    }
                    else
                    {
                        $customer_details->where('cust_id',$customer_id)->update($comment_status);
                    }
                }
                return redirect()->back()->with('message', 'Reminder Sent Successfully')
                                        ->with('pop_cust_name',$customer_name)
                                        ->with('pop_total_rent',$total_rent)
                                        ->with(['pop_data'=>$mail_data]);
            }

            //------renew button on click----------//
            elseif($_POST['renewal_pickup_btn']=='Renew')
            {
              
               $data['renew_info'] = array();
               $total_rent = 0;
               $total_deposit = 0;
               $total_due_rent = 0;
              // print_r($_POST['check']);
                
               $get_key = array_keys($check);
               $out_key = $get_key[0];
               
               //dd($_POST,$get_key,$out_key);
               $data['del_status_arr'] = array();
               // print_r($order_id);
               for ($i=0; $i <count($check[$out_key]) ; $i++) { 
                   $index = $check[$out_key][$i];

                   //get lead id by order_id from del_orders table
                   $temp_order_id= $order_id[$out_key][$index];
                   $get_status = DB::table('del_orders')->where('order_id',$temp_order_id)->get('status');
                   if($get_status[0]->status!='Delivered' && $get_status[0]->status!='Closed')
                   {
                       array_push($data['del_status_arr'],$temp_order_id);
                   }
                   $get_customer_id= $customer_id[$out_key][$index];
                   $temp_order_details_id= $order_details_id[$out_key][$index];
                   $get_lead_id = DB::select("SELECT lead_id,vendor_id FROM del_orders WHERE order_id='$temp_order_id' ");
                   $data['get_lead_id'] = json_decode(json_encode($get_lead_id),true);
                   $lead_id = $data['get_lead_id'][0]['lead_id'];
                   $get_vendor_id = DB::select("SELECT vendor_id FROM order_details WHERE order_id='$temp_order_id' ");
                   $data['get_vendor_id'] = json_decode(json_encode($get_vendor_id),true);
                   $vendor_id = $data['get_vendor_id'][0]['vendor_id'];

                   //product_id from order_details table 
                   $get_product_id = DB::select("SELECT product_id FROM order_details WHERE id='$temp_order_details_id' ");
                   $data['get_product_id'] = json_decode(json_encode($get_product_id),true);
                   $product_id = $data['get_product_id'][0]['product_id'];
                   $due_months = $due_month_count[$out_key][$index];

                   $data['renew_info'][$i]['product_name'] = $product_name[$out_key][$index];
                   $data['renew_info'][$i]['pickup_date'] = $pickup_date[$out_key][$index];
                   $data['renew_info'][$i]['renewal_date'] = date('Y-m-d',strtotime("+$due_months month",strtotime($pickup_date[$out_key][$index])));
                   $data['renew_info'][$i]['product_rent'] = $product_rent[$out_key][$index];
                   $data['renew_info'][$i]['deposit'] = $product_deposite[$out_key][$index];
                   $data['renew_info'][$i]['order_id'] = $order_id[$out_key][$index];
                   $data['renew_info'][$i]['order_details_id'] = $order_details_id[$out_key][$index];
                   $data['renew_info'][$i]['due_month_count'] = $due_month_count[$out_key][$index];
                   $data['renew_info'][$i]['total_due_month_rent'] = $total_due_month_rent[$out_key][$index];
                   $data['renew_info'][$i]['lead_id'] = $lead_id;
                   $data['renew_info'][$i]['vendor_id'] = $vendor_id;
                   $data['renew_info'][$i]['product_id'] = $product_id;
                   
                   $total_rent += $product_rent[$out_key][$i];
                   $total_deposit += $product_deposite[$out_key][$i];
                   $total_due_rent += $total_due_month_rent[$out_key][$index];
                }
               
            //    $customer_info =  DB::select("SELECT * FROM customer_details where cust_id = '$get_customer_id' ");
            //    $data['customer_info'] = json_decode(json_encode($customer_info),true);
                $order_id_addr = $data['renew_info'][0]['order_id'];
                $address_info = DB::select("SELECT del_orders.* FROM del_orders WHERE del_orders.order_id = $order_id_addr");
                $data['address_info'] = json_decode(json_encode($address_info),true);
                $customer_info =  DB::select("SELECT * FROM customer_details where cust_id = '$get_customer_id' ");
                $data['customer_info'] = json_decode(json_encode($customer_info),true);

               $data['total_rent'] = $total_rent;
               $data['total_deposit'] = $total_deposit;
               $data['total_due_rent'] = $total_due_rent;
                return view('RenewalPickup/renew_product',$data);
            }

            //--------pickup button ---------//
            elseif($_POST['renewal_pickup_btn']=='Pickup')
            {
                //print_r($_POST);
                $data['pickup_info'] = array();
                $total_rent = 0;
                $total_deposit = 0;
                $total_due_rent = 0;
                // print_r($_POST['check']);
                $get_key = array_keys($check);
                $out_key = $get_key[0];
                $data['del_status_arr'] = array();
                // print_r($order_id);
                for ($i=0; $i <count($check[$out_key]) ; $i++) { 
                    $index = $check[$out_key][$i];
                    //get lead id by order_id from del_orders table
                    $temp_order_id= $order_id[$out_key][$index];
                    $get_status = DB::table('del_orders')->where('order_id',$temp_order_id)->get('status');
                    if($get_status[0]->status!='Delivered' && $get_status[0]->status!='Closed')
                    {
                        array_push($data['del_status_arr'],$temp_order_id);
                    }
                    $get_customer_id= $customer_id[$out_key][$index];
                    $get_lead_id = DB::select("SELECT lead_id FROM del_orders WHERE order_id='$temp_order_id' ");
                    $data['get_lead_id'] = json_decode(json_encode($get_lead_id),true);
                    $lead_id = $data['get_lead_id'][0]['lead_id'];
                    $get_vendor_id = DB::select("SELECT vendor_id FROM order_details WHERE order_id='$temp_order_id' ");
                    $data['get_vendor_id'] = json_decode(json_encode($get_vendor_id),true);
                    $vendor_id = $data['get_vendor_id'][0]['vendor_id'];
                    // $vendor_id = $data['get_lead_id'][0]['vendor_id'];
                    //lead_id get close

                    //product_id from order_details table 
                    $temp_order_details_id= $order_details_id[$out_key][$index];
                    $get_product_id = DB::select("SELECT product_id FROM order_details WHERE id='$temp_order_details_id' ");
                    $data['get_product_id'] = json_decode(json_encode($get_product_id),true);
                    $product_id = $data['get_product_id'][0]['product_id'];

                    $data['pickup_info'][$i]['product_name'] = $product_name[$out_key][$index];
                    $data['pickup_info'][$i]['pickup_date'] = $pickup_date[$out_key][$index];
                    //$data['pickup_info'][$i]['renewal_date'] = date('Y-m-d',strtotime($pickup_date[$out_key][$index].'+30 day'));
                    $data['pickup_info'][$i]['product_rent'] = $product_rent[$out_key][$index];
                    $data['pickup_info'][$i]['deposit'] = $product_deposite[$out_key][$index];
                    $data['pickup_info'][$i]['order_id'] = $order_id[$out_key][$index];
                    $data['pickup_info'][$i]['order_details_id'] = $order_details_id[$out_key][$index];
                    $data['pickup_info'][$i]['due_month_count'] = $due_month_count[$out_key][$index];
                    $data['pickup_info'][$i]['total_due_month_rent'] = $total_due_month_rent[$out_key][$index];
                    $data['pickup_info'][$i]['lead_id'] = $lead_id;
                    $data['pickup_info'][$i]['vendor_id'] = $vendor_id;
                    $data['pickup_info'][$i]['product_id'] = $product_id;
                    
                    $total_rent += $product_rent[$out_key][$i];
                    $total_deposit += $product_deposite[$out_key][$i];
                    $total_due_rent += $total_due_month_rent[$out_key][$index];
                }
                
                $order_id_addr = $data['pickup_info'][0]['order_id'];
                $address_info = DB::select("SELECT del_orders.* FROM del_orders WHERE del_orders.order_id = $order_id_addr");
                $data['address_info'] = json_decode(json_encode($address_info),true);
                $customer_info =  DB::select("SELECT * FROM customer_details where cust_id = '$get_customer_id' ");
                $data['customer_info'] = json_decode(json_encode($customer_info),true);

                $data['total_rent'] = $total_rent;
                $data['total_deposit'] = $total_deposit;
                $data['total_due_rent'] = $total_due_rent;

                // print_r($data['pickup_info']);
                return view('RenewalPickup/pickup_product',$data);
                
            }
            //request for pickup
            elseif($_POST['renewal_pickup_btn']=='StopRequest'){
                $stopReason = $request->get('stop_request_reason');
                $get_key = array_keys($check);
                $out_key = $get_key[0];
                $checkStop = DB::table('order_details')->whereIn('id',$order_details_id[$out_key])->get('current_status')->pluck('current_status')->toArray();
                // if(in_array('CustStop',$checkStop)){
                //     Session::flash('message_delete', 'this product already stopped');
                //     return redirect('/renewal_pickup');
                // }
                
                for ($i=0; $i <count($check[$out_key]) ; $i++) { 
                    $index = $check[$out_key][$i];
                    $id = $order_details_id[$out_key][$index];
                    $updateData = [
                        'current_status'=>'CustStop',
                        'stop_requested_date'=>Carbon::now()->toDateTimeString(),
                        'stop_requested_by'=>session('user_id'),
                        'stop_requested_reason'=>$stopReason,
                        ];
                    $getStatus =DB::table('order_details')->where([['id','=',$id],['current_status','=','CustStop']])->exists();
                    if($getStatus==false){
                        DB::table('order_details')->where('id',$id)->update($updateData);
                    }
                }
                Session::flash('message', 'Stop Request added successfully');
                return redirect('/renewal_pickup');
 
            }

        }
        else {
            return redirect()->back()->with('error','Something went Wrong');
        }

    }


    public function pickup_order(Request $request)
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
         return redirect()->to($url);
        }
        
        //call main pickup function
        $pickupDate = $request->get('pickup_date');
        $orderDetailsId = $request->get('order_details_id');
        $pickupData = array();
        foreach ($orderDetailsId as $key => $orderId) {
            $pickupData[$key]['id']= $orderId;
            $pickupData[$key]['pickup_date']= $pickupDate[$key];
        }

        $orderStatus = $this->OrderPickup($pickupData);
        if(is_array($orderStatus)){
            $order_id = $orderStatus[0];
            $collection_url = url('/')."/assign_pickup_delboy/".$order_id;
            return redirect('/stop_requested')->with('message','Pickup Order Generated Successfuly')
                                            ->with('collection_url',$collection_url);
        }else{
            return redirect('/stop_requested')->with('message_delete','Something went wrong');
        }

        $customer_id = $_POST['customer_id'];
        $customer_name = $_POST['customer_name'];
        $customer_address = $_POST['customer_address'];
        $customer_location = $_POST['customer_location'];
        $customer_mobile = $_POST['customer_mobile'];
        $order_id = $_POST['order_id'];
        $lead_id = $_POST['lead_id'];
        $vendor_id = $_POST['vendor_id'];
        $product_id = $_POST['product_id'];
        $order_details_id = $_POST['order_details_id'];
        $product_name = $_POST['product_name'];
        $order_details_id = $_POST['order_details_id'];
        $product_rent = $_POST['product_rent'];
        $product_deposit = $_POST['product_deposit'];
        $due_month_count = $_POST['due_month_count'];
        $total_due_month_rent = $_POST['total_due_month_rent'];
        $pickup_date = $_POST['pickup_date'];
        $total_amt = $_POST['total_amount'];
        
        $temp_pickup_date = array();
        $pickup_data = array();

        $btn_state = $_POST['btn_state'];
        //date wise filter pikup data
        for($i=0; $i <count($pickup_date) ; $i++) { 
            $current_date = $pickup_date[$i];
            if(in_array($current_date,$temp_pickup_date))
            {
                for($j=0; $j<count($temp_pickup_date); $j++)
                {
                    if($pickup_data[$j]['pickup_date'] == $pickup_date[$i])
                    {
                        $pickup_details_count = count($pickup_data[$j]['pickup_details']);
                        $pickup_data[$j]['pickup_details'][$pickup_details_count]['order_id'] = $order_id[$i];
                        $pickup_data[$j]['pickup_details'][$pickup_details_count]['product_name'] = $product_name[$i];
                        $pickup_data[$j]['pickup_details'][$pickup_details_count]['order_details_id'] = $order_details_id[$i];
                        $pickup_data[$j]['pickup_details'][$pickup_details_count]['product_rent'] = $product_rent[$i];
                        $pickup_data[$j]['pickup_details'][$pickup_details_count]['product_deposit'] = $product_deposit[$i];
                        $pickup_data[$j]['pickup_details'][$pickup_details_count]['pickup_date'] = $pickup_date[$i];
                        $pickup_data[$j]['pickup_details'][$pickup_details_count]['lead_id'] = $lead_id[$i];
                        $pickup_data[$j]['pickup_details'][$pickup_details_count]['vendor_id'] = $vendor_id[$i];
                        $pickup_data[$j]['pickup_details'][$pickup_details_count]['product_id'] = $product_id[$i];
                        $pickup_data[$j]['pickup_details'][$pickup_details_count]['total_due_month_rent'] = $total_due_month_rent[$i];
                    }
                }
            }
            else
            {
                array_push($temp_pickup_date,$current_date);
                $count = count($pickup_data);
                $pickup_data[$count]['pickup_date'] = $current_date;
                $pickup_data[$count]['customer_id'] = $customer_id;
                $pickup_data[$count]['customer_name'] = $customer_name;
                $pickup_data[$count]['customer_mobile'] = $customer_mobile;
                $pickup_data[$count]['customer_location'] = $customer_location;
                $pickup_data[$count]['customer_address'] = $customer_address;
                $pickup_data[$count]['pickup_details'][0]['order_id'] = $order_id[$i];
                $pickup_data[$count]['pickup_details'][0]['product_name'] = $product_name[$i];
                $pickup_data[$count]['pickup_details'][0]['order_details_id'] = $order_details_id[$i];
                $pickup_data[$count]['pickup_details'][0]['product_rent'] = $product_rent[$i];
                $pickup_data[$count]['pickup_details'][0]['product_deposit'] = $product_deposit[$i];
                $pickup_data[$count]['pickup_details'][0]['pickup_date'] = $pickup_date[$i];
                $pickup_data[$count]['pickup_details'][0]['lead_id'] = $product_deposit[$i];
                $pickup_data[$count]['pickup_details'][0]['vendor_id'] = $vendor_id[$i];
                $pickup_data[$count]['pickup_details'][0]['product_id'] = $product_id[$i];
                $pickup_data[$count]['pickup_details'][0]['total_due_month_rent'] = $total_due_month_rent[$i];
            }
        }

        //insert data in delorders data;
        $DelOrder = new DelOrders();
        $Pickup = new Pickup();
        $get_delorder_id = 0;
        foreach ($pickup_data as $p_data)
        {
            //in del orders table for pikup order insert column DelDate value is pickup_data
            $temp_total_deposit = 0;
            $temp_product_name =array();
            $inserted_pickups_table_id = array();
            $fulldetails = $p_data['customer_name'].",".$p_data['customer_address'];
            foreach ($p_data['pickup_details'] as $p_details)
            {
                if($p_details!==null)
                {
                    $temp_total_deposit+=$p_details['product_deposit'];
                    array_push($temp_product_name,$p_details['product_name']);

                    //insert data in pickups
                    $insert_pickups = [
                        'del_order_id' => $p_details['order_id'],
                        'order_details_id' => $p_details['order_details_id'],
                        'lead_id' => $p_details['lead_id'],
                        'vendor_id' => $p_details['vendor_id'],
                        'product_id' => $p_details['product_id'],
                        //'product_id', => $p_details['order_id'],
                        'pickup_date' => $p_details['pickup_date'],
                        'cash_amount' => $p_details['total_due_month_rent'],
                        'created_at' =>date('Y-m-d h:i:s')
                    ];
                    $pickup_id = $Pickup->insertGetId($insert_pickups);
                    array_push($inserted_pickups_table_id, $pickup_id);
                    //order_details status change
                    $order_details_id = $p_details['order_details_id'];
                    $pickup_date = $p_details['pickup_date'];

                    //before update get old values
                    $getOldData = DB::table('order_details')->where('id',$order_details_id)->first('current_status');
                    DB::update("UPDATE order_details SET current_status = 'Pending Pickup' WHERE id ='$order_details_id' ");
                    $insertData = [
                        'order_type'=>'OD',
                        'key_id'=>$order_details_id,
                        'operation'=>'Pickup Order Generated',
                        'fields'=>'current_status',
                        'old_value'=>$getOldData->current_status,
                        'new_value'=>'Pending Pickup',
                        'updated_by'=>session('username')
                    ];
                    ActivityLog::insert($insertData);
                    $customer_id = $pickup_data[0]['customer_id'];
                    if(DB::table('customer_details')->select('email_id','customer_name')->where('cust_id',$customer_id)->where('cust_source','B2B')->exists())
                    {
                        $customer_details = DB::table('customer_details')->select('created_by','customer_name')->where('cust_id',$customer_id)->get()->toArray();
                        $user_details = DB::table('user')->select('id','username','email_id_user')->where('username',$customer_details[0]->created_by)->get()->toArray();
                        $user_email = $user_details[0]->email_id_user;
                        $user_name = $customer_details[0]->customer_name;
                        DelOrders::where('order_id',$pickup_id)->update(['order_owner'=>$user_details[0]->id]);
                        // $user_email = session('email_id');
                        $title = 'Pickup Order Generated for '.$user_name;
                        $message = ['message1'=>'Pickup Order Generated Successfully for '.$user_name.'.'];
                        DeliveryController::sendMailAlertUser($user_email,$user_name,$title,$message);
                        $admin_email = 'abhishekn@quali55care.com';
                        $title = 'Pickup Order Generated from '.session('username').'.';
                        $message = ['message1'=>'Check orders details page for more details about order.'];
                        DeliveryController::sendMailAlertAdmin($admin_email,$user_name,$title,$message);
                    }
                }
            }

            $temp_product_name = implode(",",$temp_product_name);
            //echo $p_data['pickup_date'];
            $pick_del_date = date('d-m-Y',strtotime($p_data['pickup_date']));
            $insert_delorder_data = [
                'status' => 'Pending',
                'deliverypickup' => 'Pick Up',
                'DelAssignedTo' =>'Pending',
                'shipping_first_name' => $p_data['customer_name'],
                'location' => $p_data['customer_location'],
                'mobileno' => $p_data['customer_mobile'],
                'line_item_1' => $temp_product_name,
                'DelDate' =>$pick_del_date,
                'Pickup_Date' => $p_data['pickup_date'],
                'TotalAmt' => $temp_total_deposit,
                'fulldetails'=> $fulldetails,
                'TravelMode' =>'Pending',
                'PickupLocation' =>'Customer',
                'order_approval_status' =>'Approved'
            ];
            $get_delorder_id = $DelOrder->insertGetId($insert_delorder_data);
            //update pickups inserted data with delorders id
            $inserted_pickups_table_id = implode(",",$inserted_pickups_table_id);
            DB::update("UPDATE pickups SET pickup_order_id = '$get_delorder_id' WHERE id IN($inserted_pickups_table_id) ");
            //insert date time log in lead log

            $logDate = Carbon::now()->toDateString();
            $logTime = Carbon::now()->toTimeString();
            DB::table('leads_log')->insert([
                'log_order_id'=>$get_delorder_id,
                'log_lead_status'=>'Order Generated',
                'log_order_type'=>'PO',
                'log_order_lead_date'=>$p_data['pickup_date'].' '.$logTime,
                'log_date'=>$logDate,
                'log_time'=>$logTime,
                'updated_by'=>session('username')
            ]);

        }
        $collection_url = url('/')."/assign_pickup_delboy/$get_delorder_id";
       //print_r($_POST);
        if(isset($btn_state) && $btn_state=='stop_pickup'){
            return redirect('/stop_requested')->with('message', 'Pickup Order Generated Successfully')
                                        ->with('collection_url',$collection_url);
        }else{
            return redirect('renewal_pickup')->with('message', 'Pickup Order Generated Successfully')
                                        ->with('collection_url',$collection_url);
        }
        
    }

    public function renew_order(Request $request)
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
         return redirect()->to($url);
        }
        $customer_id = $_POST['customer_id'];
        $customer_name = $_POST['customer_name'];
        $customer_address = $_POST['customer_address'];
        $customer_location = $_POST['customer_location'];
        $customer_mobile = $_POST['customer_mobile'];
        $order_id = $_POST['order_id'];
        $lead_id = $_POST['lead_id'];
        $product_id = $_POST['product_id'];
        $vendor_id = $_POST['vendor_id'];
        $order_details_id = $_POST['order_details_id'];
        $product_name = $_POST['product_name'];
        $order_details_id = $_POST['order_details_id'];
        $product_rent = $_POST['product_rent'];
        $total_amt = $_POST['total_amount'];
        $renew_date = $_POST['renew_date'];
        $next_renew_date = $_POST['next_renew_date'];
        $due_month_count = $_POST['due_month_count'];
        $new_due_month_count = $_POST['new_due_month_count'];
        $payment_mode = $_POST['payment_mode'];
        $offered_discount_input = $_POST['offered_discount'];
        $total_product_month_rent = $_POST['total_product_month_rent'];
        //dd($request->all());
        if($payment_mode=='Cash'){
            $cash_amount = $total_amt;
            $online_amount = 0;
            $collection_url = url('/')."/renew_request";
            // $collection_url = url('/')."/assign_collection_delboy/";
        }
        elseif($payment_mode=='Online'){
            $online_amount = $total_amt;
            $cash_amount = 0;
            $collection_url = url('/')."/pending_online_renew";
        }
        elseif($payment_mode=='Both'){
            $online_amount = $_POST['online_amount'];
            $cash_amount = $_POST['cash_amount'];
        }

        $Renew = new Renewal();
        $DelOrder = new DelOrders();
        $Order_Details = new OrderDetails();
        $temp_pickup_date = array();
        $pickup_data = array();
        //insert data in to del_order table for collection order
        $fulldetails = $customer_name.",".$customer_address;
        $temp_product_name = implode(", ",$product_name);
        $today =date('d-m-Y');
        $collection_date =date('Y-m-d');
        $insert_delorder_data = [
            'status' => 'Pending',
            'deliverypickup' => 'Collection',
            'DelAssignedTo' =>'Pending',
            'shipping_first_name' => $customer_name,
            'location' => $customer_location,
            'mobileno' => $customer_mobile,
            'line_item_1' => $temp_product_name,
            'DelDate' =>$today,
            'Collection_Date' =>$collection_date,
            'TotalAmt' => $total_amt,
            'fulldetails'=> $fulldetails,
            'TravelMode' =>'Pending',
            'PaymentMode'=>$payment_mode,
            'cash'=>$cash_amount,
            'online'=>$online_amount,
            'PickupLocation' =>'Customer',
            'order_approval_status' =>'Approved'
        ];
        $get_collection_order_id = $DelOrder->insertGetId($insert_delorder_data);
        
        if($payment_mode=='Cash'){
            $cash_amount = $total_amt;
            $online_amount = 0;
        }
        elseif($payment_mode=='Online'){
            $online_amount = $total_amt;
            $cash_amount = 0;
        }
        elseif($payment_mode=='Both'){
            $online_amount = $_POST['online_amount'];
            $cash_amount = $_POST['cash_amount'];
        }

        //insert data in renewal table for collection order 
        for ($i=0; $i <count($order_id); $i++) { 
            if($payment_mode=='Cash') {
                //$cash_collection_amt = $product_rent[$i];
                $total_product_rent = $product_rent[$i];
                if($offered_discount_input[$i]!=null){
                    $offered_discount = $offered_discount_input[$i];
                }else{
                    $offered_discount = 0;
                }
                $monthDiscount = $offered_discount/$new_due_month_count[$i];
                $cash_collection_amt = $total_product_rent-$monthDiscount;
                $online_collection_amt = 0;
            }
            if($payment_mode=='Online') {
                $total_product_rent = $product_rent[$i];
                if($offered_discount_input[$i]!=null){
                    $offered_discount = $offered_discount_input[$i];
                }else{
                    $offered_discount = 0;
                }
                $monthDiscount = $offered_discount/$new_due_month_count[$i];
                $online_collection_amt = $total_product_rent-$monthDiscount;
                $cash_collection_amt = 0;
            }
            $start_date = 0;
            for ($j=0; $j <$new_due_month_count[$i] ; $j++) { 
                if($j == 0)
                {
                    $start_date = $renew_date[$i];
                }
                $end_date = date('Y-m-d',strtotime("+1 month",strtotime($start_date)));
                $insert_collection_order_data= [
                    'collection_order_id'=>$get_collection_order_id,
                    'order_id'=>$order_id[$i],
                    'order_details_id'=>$order_details_id[$i],
                    'lead_id'=>$lead_id[$i],
                    'vendor_id'=>$vendor_id[$i],
                    'product_id'=>$product_id[$i],
                    'start_date'=>$start_date,
                    'end_date'=>$end_date,
                    'payment_mode'=>$payment_mode,
                    'cash_amount'=>$cash_collection_amt,
                    'online_amount'=>$online_collection_amt,
                    'discount_amt'=>$monthDiscount,
                    'total_amt'=>$total_product_rent,
                    //'online_method',
                    'status'=>'Pending',
                    'payment_status'=>'Pending',
                    'created_by'=>session('username'),
                    'created_at'=>date('Y-m-d H:i:s')
                ];
                $start_date = date('Y-m-d',strtotime("+1 month",strtotime($start_date)));
                $Renew->insert($insert_collection_order_data);
            }
            
            //update pickup date in order_details table
            $temp_pickup_date = date('Y-m-d',strtotime($next_renew_date[$i]));
            $update_order_details_data = [
                //'pickup_date'=>$temp_pickup_date,
                'collection_date'=>$collection_date,
                'current_status' =>'Pending Renew'
            ];
            foreach ($update_order_details_data as $key => $upData) 
            {
                $getOldData = DB::table('order_details')->where('id',$order_details_id[$i])->first($key);
                $insertData = [
                    'order_type'=>'OD',
                    'key_id'=>$order_details_id[$i],
                    'operation'=>'Collection Order Generated',
                    'fields'=>$key,
                    'old_value'=>$getOldData->$key,
                    'new_value'=>$upData,
                    'updated_by'=>session('username')
                ];
                ActivityLog::insert($insertData);
            }
            $Order_Details->where('id',$order_details_id[$i])->update($update_order_details_data);
        }

        //insert date time log in lead log
        $logDate = Carbon::now()->toDateString();
        $logTime = Carbon::now()->toTimeString();
        DB::table('leads_log')->insert([
            'log_order_id'=>$get_collection_order_id,
            'log_order_lead_date'=>Carbon::now()->toDateTimeString(),
            'log_lead_status'=>'Order Generated',
            'log_order_type'=>'CO',
            'log_date'=>$logDate,
            'log_time'=>$logTime,
            'updated_by'=>session('username')
        ]);
        
         return redirect('renewal_pickup')->with('message', 'Collection Order Generated Successfully')
                                         ->with('collection_url',$collection_url);
    }

    //testing for renewal auto jreminder
    public function RenewalAutoReminder()
    {
        $today = Carbon::today();
        $tomorrow = Carbon::now()->addDays(2)->toDateString();

        $orderTypeNotIn = config('app.order_type');
        $orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";

        // $tomorrow = Carbon::today()->toDateString();
        // $whereDate = "order_details.pickup_date = '$tommo'";
        $renewal_pickup_info = DB::select("SELECT 
                            order_details.*,
                            order_details.id as order_details_id,
                            customer_details.*,
                            products.product_name as product_name,
                            del_orders.order_id as order_id,
                            del_orders.DelDate as DelDate,
                            del_orders.isUpgraded as isUpgraded,
                            user.username as username,
                            user.contact_no as user_contact_no,
                            vendor_details.registered_name as vendor_name,
                            leads.lead_owner as lead_owner
                        FROM 
                            order_details,customer_details,del_orders,products,leads,user,vendor_details
                        where customer_details.cust_id = order_details.customer_id
                            AND order_details.order_id=del_orders.order_id
                            AND order_details.product_id=products.id
                            AND del_orders.lead_id = leads.id
                            AND del_orders.status != 'Cancel'
                            AND del_orders.status != 'Rejected'
                            AND del_orders.status != 'Cust Rejected'
                            AND order_details.vendor_id = vendor_details.id
                            AND order_details.sale_rental='Rental'
                            AND order_details.current_status!='CustStop'
                            AND leads.lead_owner = user.id
                            AND leads.lead_source != 'Agent'
                            AND(order_details.current_status='Pending'
                                -- OR order_details.current_status='Pending Renew'
                                OR order_details.current_status='Renewed' 
                                OR order_details.current_status='Renewed Online')
                            AND order_details.pickup_date = '$tomorrow'
                            AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)
                            ORDER BY order_details.pickup_date ASC");
        $data['renewal_pickup_info'] = json_decode(json_encode($renewal_pickup_info),true);
        $cust_id_array = array();
        $customer_products_details = array();
        
        foreach($data['renewal_pickup_info'] as $renewal_pickup_info)
        {
            $prod_name = $renewal_pickup_info['product_name'];
            if(in_array($renewal_pickup_info['customer_id'],$cust_id_array))
            {

                //print_r($customer_products_details);
                for($i=0; $i<count($customer_products_details); $i++)
                {
                    // echo "<br>customer_id".$customer_products_details[$i]['customer_id'];
                    // echo "<br>cust_id".$renewal_pickup_info['customer_id'];
                    if($customer_products_details[$i]['customer_id'] == $renewal_pickup_info['customer_id'])
                    {
                        $count = count($customer_products_details[$i]['product_details']);
                        $prod_name = $renewal_pickup_info['product_name'];

                        //monthly rent 
                        $temp_product_rent = $renewal_pickup_info['product_rent'];
                        $temp_today = date('Y-m-d');
                        $temp_pickup_date = $renewal_pickup_info['pickup_date'];
                        $temp_y1 = date('Y',strtotime($temp_today));
                        $temp_y2 = date('Y',strtotime($temp_pickup_date));
                        $temp_m1 = date('m',strtotime($temp_today));
                        $temp_m2 = date('m',strtotime($temp_pickup_date));
                        $month_count = abs((($temp_y2-$temp_y1)*12)+($temp_m2-$temp_m1));
                        if($month_count==0){
                            $month_count =1;
                        }

                        // //new month count
                        $d1 = new DateTime(date('Y-m-d H:i:s',));
                        $d2 = new DateTime($temp_pickup_date);
                        $interval = $d1->diff($d2);
                        $diffInSeconds = $interval->s; //45
                        $diffInMinutes = $interval->i; //23
                        $diffInHours   = $interval->h; //8
                        $diffInDays    = $interval->d; //21
                        $diffInMonths  = $interval->m; //4
                        $diffInYears   = $interval->y; //1
                        $month_count = $diffInMonths;
                        
                        if($diffInDays>0){
                            $month_count =$month_count+1;
                        }
                        if($month_count==0)
                        {
                            $month_count = 1;
                        }
                        $total_month_rent = $month_count*$temp_product_rent;

                        $customer_products_details[$i]['product_details'][$count]['product_name'] = $prod_name;
                        $customer_products_details[$i]['product_details'][$count]['vendor_name'] = $renewal_pickup_info['vendor_name'];
                        $customer_products_details[$i]['product_details'][$count]['pickup_date'] = $renewal_pickup_info['pickup_date'];
                        $customer_products_details[$i]['product_details'][$count]['order_details_id'] = $renewal_pickup_info['order_details_id'];
                        $customer_products_details[$i]['product_details'][$count]['vendor_id'] = $renewal_pickup_info['vendor_id'];
                        $customer_products_details[$i]['product_details'][$count]['vendor_product_id'] = $renewal_pickup_info['vendor_product_id'];
                        $customer_products_details[$i]['product_details'][$count]['product_qty'] = $renewal_pickup_info['product_qty'];
                        $customer_products_details[$i]['product_details'][$count]['product_rent'] = $renewal_pickup_info['product_rent'];
                        $customer_products_details[$i]['product_details'][$count]['product_deposite'] = $renewal_pickup_info['product_deposite'];
                        $customer_products_details[$i]['product_details'][$count]['transport'] = $renewal_pickup_info['transport'];
                        $customer_products_details[$i]['product_details'][$count]['product_id'] = $renewal_pickup_info['product_id'];
                        $customer_products_details[$i]['product_details'][$count]['order_id'] = $renewal_pickup_info['order_id'];
                        $customer_products_details[$i]['product_details'][$count]['DelDate'] = $renewal_pickup_info['DelDate'];
                        $customer_products_details[$i]['product_details'][$count]['lead_owner'] = $renewal_pickup_info['lead_owner'];
                        $customer_products_details[$i]['product_details'][$count]['user_contact_no'] = $renewal_pickup_info['user_contact_no'];
                        $customer_products_details[$i]['product_details'][$count]['current_status'] = $renewal_pickup_info['current_status'];
                        $customer_products_details[$i]['product_details'][$count]['month_count'] = $month_count;
                        $customer_products_details[$i]['product_details'][$count]['total_month_rent'] = $total_month_rent;
                        //product quantity wise show products
                        $temp_product_quantity = $renewal_pickup_info['product_qty'];
                        $quantity_product = array();
                        if($temp_product_quantity>1)
                        {
                            $temp_product_deposite = $renewal_pickup_info['product_deposite'];
                            $divided_product_rent = $temp_product_rent/$temp_product_quantity;
                            $divided_product_deposite = $temp_product_deposite/$temp_product_quantity;
                            
                            for ($j=0; $j <$temp_product_quantity; $j++) 
                            { 
                                $quantity_product[$j]['product_name'] = $prod_name;
                                $quantity_product[$j]['pickup_date'] = $renewal_pickup_info['pickup_date'];
                                $quantity_product[$j]['DelDate'] = $renewal_pickup_info['DelDate'];
                                $quantity_product[$j]['order_details_id'] = $renewal_pickup_info['pickup_date'];
                                $quantity_product[$j]['pickup_date'] = $renewal_pickup_info['order_details_id'];
                                $quantity_product[$j]['vendor_id'] = $renewal_pickup_info['vendor_id'];
                                $quantity_product[$j]['vendor_product_id'] = $renewal_pickup_info['vendor_product_id'];
                                $quantity_product[$j]['product_qty'] = 1;
                                $quantity_product[$j]['order_id'] = $renewal_pickup_info['order_id'];
                                $quantity_product[$j]['product_rent'] = $divided_product_rent;
                                $quantity_product[$j]['product_deposite'] = $divided_product_deposite;
                            }
                        }
                        $customer_products_details[$i]['product_details'][$count]['quantity_wise_products'] = $quantity_product;
                    }
                }
            }
            else
            {
                array_push($cust_id_array,$renewal_pickup_info['customer_id']);
                $count = count($customer_products_details);
                
                $customer_address = $renewal_pickup_info['address_line_1'].', '.$renewal_pickup_info['address_line_2'].', '.$renewal_pickup_info['area'].', '.$renewal_pickup_info['landmark'].', '.$renewal_pickup_info['location'].', '.$renewal_pickup_info['city'].', '.$renewal_pickup_info['pincode'];
                //monthly rent 
                $temp_product_rent = $renewal_pickup_info['product_rent'];
                $temp_today = date('Y-m-d');
                $temp_pickup_date = $renewal_pickup_info['pickup_date'];
                // $temp_y1 = date('Y',strtotime($temp_today));
                // $temp_y2 = date('Y',strtotime($temp_pickup_date));
                // $temp_m1 = date('m',strtotime($temp_today));
                // $temp_m2 = date('m',strtotime($temp_pickup_date));
                // $month_count = abs((($temp_y2-$temp_y1)*12)+($temp_m2-$temp_m1));
                // if($month_count==0){
                //     $month_count =1;
                // }

                //new month count
                $d1 = new DateTime(date('Y-m-d H:i:s',));
                $d2 = new DateTime($temp_pickup_date);
                $interval = $d1->diff($d2);
                $diffInSeconds = $interval->s; //45
                $diffInMinutes = $interval->i; //23
                $diffInHours   = $interval->h; //8
                $diffInDays    = $interval->d; //21
                $diffInMonths  = $interval->m; //4
                $diffInYears   = $interval->y; //1
                $month_count = $diffInMonths;
                if($diffInDays>0){
                    $month_count =$month_count+1;
                }
                if($month_count==0)
                {
                    $month_count = 1;
                }
                $total_month_rent = $month_count*$temp_product_rent;
                
                $customer_products_details[$count]['customer_id'] = $renewal_pickup_info['customer_id'];
                $customer_products_details[$count]['customer_name'] = $renewal_pickup_info['customer_name'];
                $customer_products_details[$count]['customer_type'] = $renewal_pickup_info['customer_type'];
                $customer_products_details[$count]['username'] = $renewal_pickup_info['username'];
                $customer_products_details[$count]['customer_contact_no'] = $renewal_pickup_info['primary_contact_no'];
                $customer_products_details[$count]['customer_log'] = $renewal_pickup_info['comment'];
                $customer_products_details[$count]['customer_address'] = $customer_address;
                $customer_products_details[$count]['lead_owner'] = $renewal_pickup_info['lead_owner'];
                $customer_products_details[$count]['user_contact_no'] = $renewal_pickup_info['user_contact_no'];
                $customer_products_details[$count]['customer_type'] = $renewal_pickup_info['customer_type'];

                $customer_products_details[$count]['product_details'][0]['vendor_name'] = $renewal_pickup_info['vendor_name'];
                $customer_products_details[$count]['product_details'][0]['product_name'] = $prod_name;
                $customer_products_details[$count]['product_details'][0]['pickup_date'] = $renewal_pickup_info['pickup_date'];
                $customer_products_details[$count]['product_details'][0]['order_details_id'] = $renewal_pickup_info['order_details_id'];
                $customer_products_details[$count]['product_details'][0]['vendor_id'] = $renewal_pickup_info['vendor_id'];
                $customer_products_details[$count]['product_details'][0]['vendor_product_id'] = $renewal_pickup_info['vendor_product_id'];
                $customer_products_details[$count]['product_details'][0]['product_qty'] = $renewal_pickup_info['product_qty'];
                $customer_products_details[$count]['product_details'][0]['product_rent'] = $renewal_pickup_info['product_rent'];
                $customer_products_details[$count]['product_details'][0]['product_deposite'] = $renewal_pickup_info['product_deposite'];
                $customer_products_details[$count]['product_details'][0]['transport'] = $renewal_pickup_info['transport'];
                $customer_products_details[$count]['product_details'][0]['product_id'] = $renewal_pickup_info['product_id'];
                $customer_products_details[$count]['product_details'][0]['order_id'] = $renewal_pickup_info['order_id'];
                $customer_products_details[$count]['product_details'][0]['DelDate'] = $renewal_pickup_info['DelDate'];
                $customer_products_details[$count]['product_details'][0]['lead_owner'] = $renewal_pickup_info['lead_owner'];
                $customer_products_details[$count]['product_details'][0]['user_contact_no'] = $renewal_pickup_info['user_contact_no'];
                $customer_products_details[$count]['product_details'][0]['current_status'] = $renewal_pickup_info['current_status'];
                $customer_products_details[$count]['product_details'][0]['month_count'] = $month_count;
                $customer_products_details[$count]['product_details'][0]['total_month_rent'] = $total_month_rent;

                //product quantity wise show products
                $temp_product_quantity = $renewal_pickup_info['product_qty'];
                $quantity_product = array();
                if($temp_product_quantity>1)
                {
                    $temp_product_deposite = $renewal_pickup_info['product_deposite'];
                    $divided_product_rent = $temp_product_rent/$temp_product_quantity;
                    $divided_product_deposite = $temp_product_deposite/$temp_product_quantity;
                    
                    for ($j=0; $j <$temp_product_quantity; $j++) 
                    { 
                        $quantity_product[$j]['product_name'] = $prod_name;
                        $quantity_product[$j]['DelDate'] = $renewal_pickup_info['DelDate'];
                        $quantity_product[$j]['pickup_date'] = $renewal_pickup_info['pickup_date'];
                        $quantity_product[$j]['order_details_id'] = $renewal_pickup_info['pickup_date'];
                        $quantity_product[$j]['pickup_date'] = $renewal_pickup_info['order_details_id'];
                        $quantity_product[$j]['vendor_id'] = $renewal_pickup_info['vendor_id'];
                        $quantity_product[$j]['vendor_product_id'] = $renewal_pickup_info['vendor_product_id'];
                        $quantity_product[$j]['product_qty'] = 1;
                        $quantity_product[$j]['order_id'] = $renewal_pickup_info['order_id'];
                        $quantity_product[$j]['product_rent'] = $divided_product_rent;
                        $quantity_product[$j]['product_deposite'] = $divided_product_deposite;
                    }
                }
                $customer_products_details[$count]['product_details'][0]['quantity_wise_products'] = $quantity_product;
            }
        }
        $data['customer_products_details'] = $customer_products_details;
        // Log::info($data['customer_products_details']);
        //get total all of need
        $total_equipment = 0;
        $total_due_amount = 0;
        
        for ($i=0; $i<count($customer_products_details); $i++) { 
            $total_equipment+=count($customer_products_details[$i]['product_details']);
            for ($j=0; $j<count($customer_products_details[$i]['product_details']); $j++)
            {
                $total_due_amount += $customer_products_details[$i]['product_details'][$j]['total_month_rent'];
            }
        }
        // $data['total_customer']=count($cust_id_array);
        // $data['total_equipment']=$total_equipment;
        // $data['total_due_amount']=$total_due_amount;
        // $total_customer=count($cust_id_array);
        // $total_equipment=$total_equipment;
        // $total_due_amount=$total_due_amount;
        if(!empty($data['customer_products_details']))
        {
            $LeadController = new LeadController();//controller file class
            $RenewalReminder = new RenewalReminder();//model file class
            $LinkCustDetails = new LinkCustDetails();//model file class
            foreach($data['customer_products_details'] as $key=>$customerOrderDetails)
            {
                $contact_no = $customerOrderDetails['customer_contact_no'];
                $customer_name = $customerOrderDetails['customer_name'];
                $link_owner = $customerOrderDetails['lead_owner'];
                $user_contact_no = $customerOrderDetails['user_contact_no'];
                $link_id = $LeadController->GenerateLinkid();
                if($customerOrderDetails['customer_type']=='Individual')
                {
                    //insert into link cust details
                    $insertLinkData = [
                        'primary_contact_no'=>$contact_no,
                        'customer_name'=>$customer_name,
                        'link_id'=>$link_id,
                        'link_type'=>'R',
                        'r_link_owner'=>$link_owner
                    ];
                    $link_tbl_id = $LinkCustDetails->insertGetId($insertLinkData);
                   
                    //insert data in renewal reminder
                    foreach($customerOrderDetails['product_details'] as $key1=>$productDetails)
                    {
                        $insertRenewalData = [
                            'link_id'=>$link_id,
                            'link_tbl_id'=>$link_tbl_id,
                            'customer_id'=>$customerOrderDetails['customer_id'],
                            'order_details_id'=>$productDetails['order_details_id'],
                            'due_month'=>$productDetails['month_count'],
                            'order_pickup_date'=>$productDetails['pickup_date']
                        ];  
                        $RenewalReminder->insert($insertRenewalData);
                    }
                    // //create short link to send customer
                    $app_env = config('app.app_env');
                    $full_url = "http://intra.quali55care.com/$app_env/eflow/customer_renewal_or_pickup_link/".$link_id;
                    
                    $short_url = "$app_env/eflow/0/".$link_id;
                    $whatsappLink = "http://intra.quali55care.com/".$short_url;
                    //send message link to customer

                    // $curl = curl_init();
                    // curl_setopt_array($curl, array(
                    //     CURLOPT_URL => "https://api.msg91.com/api/v5/flow/",
                    //     CURLOPT_RETURNTRANSFER => true,
                    //     CURLOPT_ENCODING => "",
                    //     CURLOPT_MAXREDIRS => 10,
                    //     CURLOPT_TIMEOUT => 30,
                    //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    //     CURLOPT_CUSTOMREQUEST => "POST",
                    //     //CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"60cc49120eeed16fcd62d103\",\n  \"sender\": \"QUALCR\",\n  \"mobiles\": \"919920361040\",\n  \"name\": \"testing\",\n  \"orderno\": \"8512457845\",\n  \"equpname\": \"Standard Walker\",\n  \"date\": \"18-06-2021\",\n  \"amount\": \"550\"}",
                    //     CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"6215d10a19121371cb019956\",\n  \"sender\": \"QUALCR\",\n  \"mobiles\": \"91$contact_no\",\n  \"cust_name\": \"$customer_name\",\n  \"link_url\": \"$short_url\"}",
                    //     CURLOPT_HTTPHEADER => array(
                    //     "authkey: 267641AmFwcnWjDS5e6b4757P1",
                    //     "content-type: application/JSON"
                    //     ),
                    // ));

                    // $response = curl_exec($curl);
                    // $err = curl_error($curl);

                    // curl_close($curl);

                    // if ($err) {
                    // echo "cURL Error #:" . $err;
                    // } else {
                    // echo $response;
                    // }
                    
                    //-----End-----//


                    //whatsapp message send
                    $ceoId = config('app.ceo_id');
                    $businessHeadId = config('app.business_head_id');
                    
                    $ceoContact = DB::table('user')->where('id',$ceoId)->first();
                    $businessHeadContact = DB::table('user')->where('id',$businessHeadId)->first();

                    // $callUs = $ceoContact->contact_no." / ".$businessHeadContact->contact_no;
                    $callUs = $user_contact_no." / ".$businessHeadContact->contact_no;
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
                    if(config('app.app_env') == 'devweb')
                    {
                        $contact_no = config('app.developer_contact');
                    }
                    $data =[
                        "portno"=>"11140",
                        "namespace"=>"b9a23cb4_89ed_4fe2_b849_20775908ff5e",
                        "countrycode"=> "91",
                        "mobileno"=> "$contact_no",
                        "templatename" => "orders_renewal_pickup",
                        "templateparams" => [
                            ["type"=> "text","text"=> $customer_name],
                            ["type"=> "text","text"=> $whatsappLink],
                            ["type"=> "text","text"=> $callUs],
                        ],
                    ];
                    
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                    
                    $resp = curl_exec($curl);
                    //dd($resp);
                    curl_close($curl);
                    
                    ShortUrl::insert(['url_link_id'=>$link_id,'full_url'=>$full_url]);
                }
                
            }
        }
    }
    public function RenewalReminderOverdue(){
        $today = Carbon::today();
        $overdueDetails = DB::table('order_details')
            ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
            ->join('del_orders','del_orders.order_id','=','order_details.order_id')
            ->join('products','products.id','=','order_details.product_id')
            ->join('leads','leads.id','=','del_orders.lead_id')
            ->join('user','leads.lead_owner','=','user.id')
            ->select('user.username','user.contact_no','del_orders.mobileno','del_orders.shipping_first_name','del_orders.patient_name','order_details.pickup_date','products.product_name','customer_details.customer_type')
            ->whereNotIn('del_orders.status',['Cancel','Rejected','Cust Rejected'])
            ->where('order_details.sale_rental','Rental')
            ->whereNotIn('leads.lead_source',['Agent'])
            ->whereNotIn('del_orders.mobileno',[7738017575,9820893329,9082612064,8291896466])
            ->whereIn('order_details.current_status',['Pending','Renewed','Renewed Online'])
            ->where('order_details.pickup_date','<',$today)
            ->orderBy('order_details.pickup_date','ASC')
            ->get();
        $overdueDetails = $overdueDetails->groupBy('mobileno');
        
        $razrpayLink = "RazorPay : https://rzp.io/l/2eDOVwr";
        $gpayLink = "https://bit.ly/3b5q776";
        $businessHeadId = config('app.business_head_id');
        $businessHeadContact = DB::table('user')->where('id',$businessHeadId)->first();
        
        foreach($overdueDetails as $key=>$value){
            $overdueDetails[$key][0]->equipments = substr(implode(',',array_unique($value->pluck('product_name')->toArray())),0,20)."...";
            $record = $overdueDetails[$key][0];
            if($value[0]->customer_type == 'Individual'){
                $callUs = $record->contact_no." / ".$businessHeadContact->contact_no;
                $template = "overdue_renewal_reminder";
                $params = [
                    ["type"=> "text","text"=> "$record->shipping_first_name"],
                    ["type"=> "text","text"=> "$razrpayLink"],
                    ["type"=> "text","text"=> "."],
                    ["type"=> "text","text"=> ": $gpayLink"],
                    ["type"=> "text","text"=> "$callUs"],
                ];
            }else{
                $pickup_date = date('d-m-Y',strtotime($record->pickup_date));
                $template = "corporate_renewal";
                $params = [
                    ["type"=> "text","text"=> "$record->patient_name"],
                    ["type"=> "text","text"=> "$record->equipments"],
                    ["type"=> "text","text"=> "$pickup_date"],
                    ["type"=> "text","text"=> "$record->contact_no"],
                    ["type"=> "text","text"=> "$businessHeadContact->contact_no"],
                ];
            }
            // return $params;
            return $this->sendWpMsg($key,$template,$params);

        }
        return $overdueDetails;
    }
    private function sendWpMsg($customerContact,$template,$params){

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
        if(config('app.app_env') == 'devweb')
        {
            $customerContact = config('app.developer_contact');
        }
        $data =[
            "portno"=>"11140",
            "namespace"=>"b9a23cb4_89ed_4fe2_b849_20775908ff5e",
            "countrycode"=> "91",
            "mobileno"=> "$customerContact",
            "templatename" => "$template",
            "templateparams" => $params,
        ];
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        $resp = curl_exec($curl);
        // dd($resp);
        curl_close($curl);
        return $resp;
    }
    public function RenewalAutoReminderOverdue()
    {
        $today = Carbon::today();
        $orderTypeNotIn = config('app.order_type');
        $orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";
        $renewal_pickup_info = DB::select("SELECT 
                            order_details.*,
                            order_details.id as order_details_id,
                            customer_details.*,
                            products.product_name as product_name,
                            del_orders.order_id as order_id,
                            del_orders.DelDate as DelDate,
                            del_orders.isUpgraded as isUpgraded,
                            user.username as username,
                            user.contact_no as user_contact_no,
                            vendor_details.registered_name as vendor_name,
                            leads.lead_owner as lead_owner,
                            leads.patient_name as patient_name
                        FROM 
                            order_details,customer_details,del_orders,products,leads,user,vendor_details
                        where customer_details.cust_id = order_details.customer_id
                            AND order_details.order_id=del_orders.order_id
                            AND order_details.product_id=products.id
                            AND del_orders.lead_id = leads.id
                            AND del_orders.status != 'Cancel'
                            AND del_orders.status != 'Rejected'
                            AND del_orders.status != 'Cust Rejected'
                            AND order_details.vendor_id = vendor_details.id
                            AND order_details.sale_rental='Rental'
                            AND leads.lead_owner = user.id
                            AND leads.lead_source != 'Agent'
                            AND(order_details.current_status='Pending'
                                -- OR order_details.current_status='Pending Renew'
                                OR order_details.current_status='Renewed' 
                                OR order_details.current_status='Renewed Online')
                            AND order_details.pickup_date < '$today'
                            AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)
                            ORDER BY order_details.pickup_date ASC");
        $data['renewal_pickup_info'] = json_decode(json_encode($renewal_pickup_info),true);
        $customer_nos = array();
        foreach($data['renewal_pickup_info'] as $key=>$value)
        {
            if($value['customer_type'] == 'Corporate')
            {
                //array_push($customer_nos,$value['contact_person_1_no']);
                $customer_nos[$key]['customer_no'] = $value['primary_contact_no'];
                $customer_nos[$key]['customer_name'] = $value['patient_name'];
                $customer_nos[$key]['user_contact_no'] = $value['user_contact_no'];
            }
            if($value['customer_type'] == 'Corporate' && $value['contact_person_1_no']!=null)
            {
                //array_push($customer_nos,$value['contact_person_1_no']);
                $customer_nos[$key]['customer_no'] = $value['contact_person_1_no'];
                $customer_nos[$key]['customer_name'] = $value['patient_name'];
                $customer_nos[$key]['user_contact_no'] = $value['user_contact_no'];
            }
            elseif($value['customer_type'] == 'Individual' && $value['primary_contact_no']!=null)
            {
                //array_push($customer_nos,$value['primary_contact_no']);
                $customer_nos[$key]['customer_no'] = $value['primary_contact_no'];
                $customer_nos[$key]['customer_name'] = $value['customer_name'];
                $customer_nos[$key]['user_contact_no'] = $value['user_contact_no'];
            }
        }
        $customer_nos = Collect($customer_nos)->groupBy('customer_no');
        // dd($customer_nos);
        foreach($customer_nos as $key=>$value)
        {
            $customer_contact = $key;
            $customer_name = $customer_nos[$key][0]['customer_name'];

            //send whatsapp message 
            $razrpayLink = "RazorPay : https://rzp.io/l/2eDOVwr";
            $gpayLink = "https://bit.ly/3b5q776";
            
            $ceoId = config('app.ceo_id');
            $businessHeadId = config('app.business_head_id');
            
            $ceoContact = DB::table('user')->where('id',$ceoId)->first();
            $businessHeadContact = DB::table('user')->where('id',$businessHeadId)->first();

            // $callUs = $ceoContact->contact_no." / ".$businessHeadContact->contact_no;
            $callUs = $customer_nos[$key][0]['user_contact_no']." / ".$businessHeadContact->contact_no;

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
            if(config('app.app_env') == 'devweb')
            {
                $customer_contact = config('app.developer_contact');
            }
            $data =[
                "portno"=>"11140",
                "namespace"=>"b9a23cb4_89ed_4fe2_b849_20775908ff5e",
                "countrycode"=> "91",
                "mobileno"=> "$customer_contact",
                "templatename" => "overdue_renewal_reminder",
                "templateparams" => [
                    ["type"=> "text","text"=> "$customer_name"],
                    ["type"=> "text","text"=> "$razrpayLink"],
                    ["type"=> "text","text"=> "."],
                    ["type"=> "text","text"=> ": $gpayLink"],
                    ["type"=> "text","text"=> "$callUs"],
                ],
            ];
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            $resp = curl_exec($curl);
            // dd($resp);
            curl_close($curl);
        }

    }

    //Renewal pickup link get data form customer side 
    public function CustomerRenewalLinkData(Request $request,$link)
    {
        if($request->isMethod('get') && RenewalReminder::where('link_id',$link)->exists() && LinkCustDetails::where([['link_id','=',$link],['link_type','=','R'],['link_status','=','0'],['admin_r_link_status','=','0']])->exists())
        {   
            $get_link_time = LinkCustDetails::where('link_id',$link)->get('created_at');
            $link_time = $get_link_time[0]->created_at;
            $now = Carbon::now();
            $hours = $link_time->diffInHours($now);
            if($hours<48){
                $get_renewal_data = RenewalReminder::join('order_details','order_details.id','=','order_details_id')
                                            ->join('products','order_details.product_id','=','products.id')
                                            ->select('renewal_reminder.*','order_details.product_rent as product_rent','products.product_name',DB::raw('order_details.product_rent * due_month as total_rent'))
                                            ->where('link_id',$link)
                                            ->get();
                return view('RenewalPickup/customer_link_view',compact('get_renewal_data','link'));
            }else{
                LinkCustDetails::where('link_id',$link)->update(['link_status'=>2]);
                header("Refresh:0");
            }
            
        }
        elseif($request->isMethod('post') && RenewalReminder::where('link_id',$link)->exists() && LinkCustDetails::where([['link_id','=',$link],['link_type','=','R'],['link_status','=','0'],['admin_r_link_status','=','0']])->exists())
        {
            $DelOrder = new DelOrders();
            $Renewals = new Renewal();
            $Pickups = new Pickup();
            $renewal_table_id=$request->get('renewal_table_id');
            $order_details_id=$request->get('order_details_id');
            $product_status=$request->get('product_status');
            $cust_pickup_date=$request->get('cust_pickup_date');
            // $cust_pickup_time=$request->get('cust_pickup_time');
            $cust_payment_mode=$request->get('payment_mode');
            $order_pickup_date = array();
            $order_renewal_date = array();
            $product_name_arr = array();
            $product_rent_arr = array();
            $product_online_amount = array();
            $product_cash_amount = array();
            foreach($renewal_table_id as $key=>$renew_reminder_id)
            {
                $get_order_details = DB::table('order_details')
                                        ->join('products','order_details.product_id','=','products.id')
                                        ->select('order_details.*','products.product_name as product_name')
                                        ->where('order_details.id',$order_details_id[$key])->get();
                $pickup_date = $get_order_details[0]->pickup_date;
                $next_renew_date = Carbon::parse($pickup_date)->addMonth()->toDateString();
                $order_pickup_date[$key] = $pickup_date;
                $order_renewal_date[$key]= $next_renew_date;
                $product_name_arr[$key] = $get_order_details[0]->product_name;
                $product_rent_arr[$key] = $get_order_details[0]->product_rent;
                
                if($product_status[$key]==0 && $cust_payment_mode[$key]==1){
                    array_push($product_online_amount,$get_order_details[0]->product_rent);
                }elseif($product_status[$key]==0 && $cust_payment_mode[$key]==0)
                {
                    array_push($product_cash_amount,$get_order_details[0]->product_rent);
                }
                $update_data = ['customer_reponse'=>$product_status[$key],
                                'cust_response_pickup_date'=>$cust_pickup_date[$key],
                                // 'cust_response_pickup_time'=>$cust_pickup_time[$key],
                                'cust_response_payment'=>$cust_payment_mode[$key]];
                RenewalReminder::where('id',$renew_reminder_id)->update($update_data);
            }
            $contact_no = 8792740050;
            $contact_no2 = 9820616550;
            $username = "Quali55Care";
            
            $compact = compact('renewal_table_id','order_details_id','product_status','cust_pickup_date','cust_payment_mode','order_pickup_date','order_renewal_date','product_name_arr','product_rent_arr','product_online_amount','product_cash_amount','contact_no','contact_no2','username');
            LinkCustDetails::where('link_id',$link)->update(['link_status'=>1]);
            //send whatsapp response to lead owner
            $orderDetailsId = $order_details_id[0];
            //get lead owner info
            $msgData = DB::table('order_details')->where('order_details.id',$orderDetailsId)
                            ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                            ->join('del_orders','order_details.order_id','=','del_orders.order_id')
                            ->join('leads','del_orders.lead_id','=','leads.id')
                            ->join('user','leads.lead_owner','=','user.id')
                            ->select('user.contact_no as lead_own_contact','del_orders.order_id','customer_details.*')
                            ->first();
            
            $leadOwnerContact = $msgData->lead_own_contact;
            $customerName = $msgData->customer_name;
            $customerContact = $msgData->primary_contact_no;
            $orderId = $msgData->order_id;
            $date = Carbon::now()->format('j F Y');
            $businessHeadId = config('app.business_head_id');
            $businessHeadContact = DB::table('user')->where('id',$businessHeadId)->first();
            $contactNos = [$businessHeadContact->contact_no,$leadOwnerContact];
            //dd($contactNos);
            foreach ($contactNos as $key => $contact) {
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
                if(config('app.app_env') == 'devweb')
                {
                    $contact = config('app.developer_contact');
                }
                $data =[
                    "portno"=>"11140",
                    "namespace"=>"b9a23cb4_89ed_4fe2_b849_20775908ff5e",
                    "countrycode"=> "91",
                    "mobileno"=> "$contact",
                    "templatename" => "cust_order_reply",
                    "templateparams" => [
                        ["type"=> "text","text"=> "$date"],
                        ["type"=> "text","text"=> "$customerName"],
                        ["type"=> "text","text"=> "$customerContact"],
                        ["type"=> "text","text"=> "$orderId"],
                    ],
                ];
                
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                
                $resp = curl_exec($curl);
                curl_close($curl);
            }

            return view('RenewalPickup/submit_autoreminder_success',$compact);
            
        }
        else{
            $contact_no = 8792740050;
            $contact_no2 = 9820616550;
            $username = "Quali55Care";
            return view('Alert_Templates/Failed_Message',compact('contact_no','username','contact_no2'));
        }
    }
    //Get all renewl links
    public function GetRenewalLinks(Request $request)
    {
        // $get_user_id = session('user_id');
        // $get_user_role = session('role');
        // if($get_user_role=='admin'||$get_user_role=='superuser')
        // {
        //     $whereClause = [['link_type','=','R']];
        // }
        // else
        // {
        //     $whereClause = [['link_type','=','R'],['r_link_owner','=',$get_user_id]];
        // }
        
        $cust_name = $request->get('cust_name');
        
        $start_date = Carbon::yesterday()->subDays(1)->toDateString();
        $end_date = Carbon::now()->toDateString();
        $f_start_date = $request->get('start_date');
        $f_end_date = $request->get('end_date');
        if(isset($f_start_date) && isset($f_end_date))
        {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $f_start_date = $request->get('start_date');
            $f_end_date = $request->get('end_date');
        }
        // $get_renewal_link = LinkCustDetails::where([['link_type','=','R']])
        //                     ->where([['customer_name','LIKE','%'.$cust_name.'%']])
        //                     ->whereBetween(DB::raw('DATE(created_at)'),[$start_date,$end_date])
        //                     ->orderBy('link_status','ASC')
        //                     ->orderBy('created_at','DESC')
        //                     ->paginate(10);
        $get_renewal_link = DB::table('link_cust_details')
                            ->join('renewal_reminder','renewal_reminder.link_tbl_id','=','link_cust_details.id')
                            ->join('customer_details','customer_details.cust_id','=','renewal_reminder.customer_id')
                            ->select('link_cust_details.*')
                            ->where([['link_cust_details.customer_name','LIKE','%'.$cust_name.'%']])
                            ->whereBetween(DB::raw('DATE(link_cust_details.created_at)'),[$start_date,$end_date])
                            ->when(session('city_based_access') =='1',function($query){
                                $query->where('customer_details.citygroup',session('user_city'));
                            })
                            ->orderBy('link_cust_details.link_status','ASC')
                            ->orderBy('link_cust_details.created_at','DESC')
                            ->paginate(10);
        $filter = ['cust_name'=>$cust_name,'start_date'=>$f_start_date,'end_date'=>$f_end_date];
        return view('RenewalPickup/renewal_links',compact('get_renewal_link','filter'));
    }

    public function AdminRenewalLinkData(Request $request,$link)
    {
        if($request->isMethod('get'))
        {
            $get_user_id = session('user_id');
            $get_renewal_data = RenewalReminder::join('order_details','order_details.id','=','order_details_id')
                                            ->join('products','order_details.product_id','=','products.id')
                                            ->join('link_cust_details','renewal_reminder.link_tbl_id','=','link_cust_details.id')
                                            ->select('renewal_reminder.*','order_details.order_id',
                                                        'order_details.product_rent as product_rent',
                                                        'order_details.current_status as current_status',
                                                        'order_details.pickup_date as pickup_date',
                                                        'products.product_name',
                                                        DB::raw('order_details.product_rent * due_month as total_rent'),
                                                        'renewal_reminder.order_pickup_date'
                                                    )
                                            ->where([['renewal_reminder.link_id','=',$link]])
                                            ->get();
            //dd($get_renewal_data);
            //check order is existed or not
            $order_existed = array();
            $status = array('Pending Pickup','Picked Up','Pending Renew');
            foreach($get_renewal_data as $key=>$Order){
                if($Order->order_pickup_date==$Order->pickup_date && in_array($Order->current_status,$status)){
                    $order_existed[$key] = "Y";
                }
                elseif(Carbon::parse($Order->pickup_date)->toDateString() > Carbon::parse($Order->order_pickup_date)->toDateString()){
                    $order_existed[$key] = "Y";
                }
                else{
                    $order_existed[$key] = "N";
                }
            }
            return view('RenewalPickup/admin_link_view',compact('get_renewal_data','link','order_existed'));
        }
        if($request->isMethod('post'))
        {
            $DelOrder = new DelOrders();
            $Renewals = new Renewal();
            $Pickups = new Pickup();

            $renewal_table_id=$request->get('renewal_table_id');
            $order_details_id=$request->get('order_details_id');
            $default_order_pickup_date=$request->get('default_order_pickup_date');
            $order_existed=$request->get('order_existed');

            if($request->get('btn_submit')=="show_submit"){
                $product_status=$request->get('default_product_status');
                $cust_pickup_date=$request->get('default_cust_pickup_date');
                // $cust_pickup_time=$request->get('default_cust_pickup_time');
                $cust_payment_mode=$request->get('default_payment_mode');
            }
            else {
                $product_status=$request->get('changed_product_status');
                $cust_pickup_date=$request->get('changed_cust_pickup_date');
                // $cust_pickup_time=$request->get('changed_cust_pickup_time');
                $cust_payment_mode=$request->get('changed_payment_mode');
            }
            if(in_array(2,$product_status)){
                return redirect()->back()->with('error','Please change the status of the Undecided order and then click on the submit button');
            }
            //dd($product_status,$cust_pickup_date,$cust_payment_mode);
            $ContinueOrders = array("renewal_table_id"=>array(),"order_details_id"=>array(),"cust_pickup_date"=>array(),"cust_pickup_time"=>array(),"cust_payment_mode"=>array());
            $PickupOrders = array("renewal_table_id"=>array(),"order_details_id"=>array(),"cust_pickup_date"=>array(),"cust_pickup_time"=>array(),"cust_payment_mode"=>array());
            $UndecidedOrders = array("renewal_table_id"=>array(),"order_details_id"=>array(),"cust_pickup_date"=>array(),"cust_pickup_time"=>array(),"cust_payment_mode"=>array());
            foreach($product_status as $key=>$status)
            {
                if($status==0 && $order_existed[$key]=="N")
                {
                    array_push($ContinueOrders['renewal_table_id'],$renewal_table_id[$key]);
                    array_push($ContinueOrders['order_details_id'],$order_details_id[$key]);
                    array_push($ContinueOrders['cust_pickup_date'], $cust_pickup_date[$key]);
                    // array_push($ContinueOrders['cust_pickup_time'], $cust_pickup_time[$key]);
                    array_push($ContinueOrders['cust_payment_mode'] ,$cust_payment_mode[$key]);
                }
                elseif($status==1 && $order_existed[$key]=="N")
                {
                    array_push($PickupOrders['renewal_table_id'],$renewal_table_id[$key]);
                    array_push($PickupOrders['order_details_id'],$order_details_id[$key]);
                    array_push($PickupOrders['cust_pickup_date'], $cust_pickup_date[$key]);
                    // array_push($PickupOrders['cust_pickup_time'], $cust_pickup_time[$key]);
                    //array_push($PickupOrders['cust_payment_mode'] ,$cust_payment_mode[$key]);
                }
                elseif($status==2 && $order_existed[$key]=="N")
                {
                    array_push($UndecidedOrders['renewal_table_id'],$renewal_table_id[$key]);
                    array_push($UndecidedOrders['order_details_id'],$order_details_id[$key]);
                    array_push($UndecidedOrders['cust_pickup_date'], $cust_pickup_date[$key]);
                    // array_push($UndecidedOrders['cust_pickup_time'], $cust_pickup_time[$key]);
                    array_push($UndecidedOrders['cust_payment_mode'] ,$cust_payment_mode[$key]);
                }
            }
            //for renewal by cash and online and insert in database
            $renewal_order = array();
            $temp_pay = array();
            if(!empty($ContinueOrders['renewal_table_id']))
            {
                foreach ($ContinueOrders['cust_payment_mode'] as $key => $mode) {
                    $key1 = array_search($mode,$temp_pay);
                    if(in_array($mode,$temp_pay))
                    {
                        $cnt = count($renewal_order[$key1]['data']['renew_reminder_id']);
                        $renewal_order[$key1]['data']['renew_reminder_id'][$cnt] = $ContinueOrders['renewal_table_id'][$key];
                        $renewal_order[$key1]['data']['order_details_id'][$cnt] = $ContinueOrders['order_details_id'][$key];
                        $renewal_order[$key1]['data']['cust_pickup_date'][$cnt] = $ContinueOrders['cust_pickup_date'][$key];
                        // $renewal_order[$key1]['data']['cust_pickup_time'][$cnt] = $ContinueOrders['cust_pickup_time'][$key];
                    }
                    else
                    {
                        array_push($temp_pay,$mode);
                        $rn_count = count($renewal_order);
                        $renewal_order[$rn_count]['payment_mode']=$mode;
                        $renewal_order[$rn_count]['data']['renew_reminder_id'][0] = $ContinueOrders['renewal_table_id'][$key];
                        $renewal_order[$rn_count]['data']['order_details_id'][0] = $ContinueOrders['order_details_id'][$key];
                        $renewal_order[$rn_count]['data']['cust_pickup_date'][0] = $ContinueOrders['cust_pickup_date'][$key];
                        // $renewal_order[$rn_count]['data']['cust_pickup_time'][0] = $ContinueOrders['cust_pickup_time'][$key];
                    }
                }
            }
            
            if(!empty($renewal_order))
            {
                foreach($renewal_order as $key=>$renewOrder)
                {
                    if($renewOrder['payment_mode']==0){
                        $payment = "Cash";
                    }
                    else{
                        $payment = "Online";
                    }
                    $order_details_id = $renewOrder['data']['order_details_id'][0];
                    
                    $get_customer_info = DB::table('order_details')
                                                ->join('del_orders', 'order_details.order_id','=','del_orders.order_id')
                                                ->where('order_details.id',$order_details_id)->get();
                    
                    $fulldetails = $get_customer_info[0]->fulldetails;
                    $insert_delorder_data = [
                        'status' => 'Pending',
                        'deliverypickup' => 'Collection',
                        'DelAssignedTo' =>'Pending',
                        'shipping_first_name' => $get_customer_info[0]->shipping_first_name,
                        'location' => $get_customer_info[0]->location,
                        'mobileno' => $get_customer_info[0]->mobileno,
                        //'line_item_1' => ,
                        'DelDate' =>date('d-m-Y',strtotime($renewOrder['data']['cust_pickup_date'][0])),
                        'Collection_Date' =>$renewOrder['data']['cust_pickup_date'][0],
                        'TotalAmt' => 0,
                        'fulldetails'=> $fulldetails,
                        'TravelMode' =>'Pending',
                        'PaymentMode'=>$payment,
                        //'cash'=>$cash_amount,
                        //'online'=>$online_amount,
                        'PickupLocation' =>'Customer',
                        'order_approval_status' =>'Approved'
                    ];
                    $get_collection_order_id = $DelOrder->insertGetId($insert_delorder_data);
                    $products_arr = array();
                    $product_rent_arr = array();
                    //get details and insert into renewal
                    for ($i=0; $i <count($renewal_order[$key]['data']['renew_reminder_id']) ; $i++) { 
                        $order_details_id = $renewal_order[$key]['data']['order_details_id'][$i];
                        $get_order_data = DB::table('order_details')
                                                ->join('del_orders','order_details.order_id','=','del_orders.order_id')
                                                ->join('products','order_details.product_id','=','products.id')
                                                ->select('order_details.*','products.product_name as product_name','del_orders.lead_id as lead_id')
                                                ->where('order_details.id',$order_details_id)->get();
                        array_push($products_arr,$get_order_data[0]->product_name);
                        $start_date = $get_order_data[0]->pickup_date;
                        $end_date = Carbon::parse($start_date)->addMonth()->toDateString();
                        //product rent 
                        array_push($product_rent_arr,$get_order_data[0]->product_rent);
                        //check
                        if($payment=="Online"){
                            $cash =0;
                            $online = $get_order_data[0]->product_rent;
                        }
                        if($payment=="Cash"){
                            $cash = $get_order_data[0]->product_rent;
                            $online = 0;
                        }
                        $insert_collection_order_data= [
                            'collection_order_id'=>$get_collection_order_id,
                            'order_id'=>$get_order_data[0]->order_id,
                            'order_details_id'=>$get_order_data[0]->id,
                            'lead_id'=>$get_order_data[0]->lead_id,
                            'vendor_id'=>$get_order_data[0]->vendor_id,
                            'product_id'=>$get_order_data[0]->product_id,
                            'start_date'=>$start_date,
                            'end_date'=>$end_date,
                            'payment_mode'=>$payment,
                            'cash_amount'=>$cash,
                            'online_amount'=>$online,
                            // 'online_method',
                            'status'=>'Pending',
                            'payment_status'=>'Pending',
                            'created_by'=>session('user_id'),
                            'created_at'=>Carbon::now()->toDateTimeString()
                        ];
                        $Renewals->insert($insert_collection_order_data);

                        //update in renewal reminder table
                        $get_oldData = RenewalReminder::where('id',$renewal_order[$key]['data']['renew_reminder_id'][$i])->get(); //for activity log

                        $update_data = ['customer_reponse'=>0,
                                        'cust_response_pickup_date'=>$renewal_order[$key]['data']['cust_pickup_date'][$i],
                                        // 'cust_response_pickup_time'=>$renewal_order[$key]['data']['cust_pickup_time'][$i],
                                        'cust_response_payment'=>$renewOrder['payment_mode'],
                                        'order_id'=>$get_collection_order_id,
                                        'admin_updated_at'=>Carbon::now()->toDateTimeString(),
                                        'updated_by'=>session('user_id')];
                        RenewalReminder::where('id',$renewal_order[$key]['data']['renew_reminder_id'][$i])->update($update_data);

                        //insert in activity log
                        foreach($update_data as $upKey=>$act_UpdatData)
                        {
                            if($act_UpdatData!=$get_oldData[0]->$upKey);
                            {
                                //if($get_oldData[0]->$upKey=>)
                                $insertData = [
                                    'order_type'=>'RR',
                                    'key_id'=>$renewal_order[$key]['data']['renew_reminder_id'][$i],
                                    'operation'=>'Updated Renewal Reminder',
                                    'fields'=>$upKey,
                                    'old_value'=>$get_oldData[0]->$upKey,
                                    'new_value'=>$act_UpdatData,
                                    'updated_by'=>session('username')
                                ];
                                ActivityLog::insert($insertData);
                            }
                            
                        }
                        
                        $get_oldData = DB::table('order_details')->select('current_status')->where('id',$order_details_id)->get();//for activity log
                        
                        DB::table('order_details')->where('id',$order_details_id)->update(['current_status'=>'Pending Renew']);
                        //insert in activity log
                        $insertData = [
                            'order_type'=>'OD',
                            'key_id'=>$order_details_id,
                            'operation'=>'Updated Current Status ',
                            'fields'=>'current_status',
                            'old_value'=>$get_oldData[0]->current_status,
                            'new_value'=>'Pending Renew',
                            'updated_by'=>session('username')
                        ];
                        ActivityLog::insert($insertData);
                    }
                    //update del orders table collection order id
                    $total_amt = array_sum($product_rent_arr);
                    $product_implode = implode(",",$products_arr);

                    $get_oldData = DB::table('del_orders')->where('order_id',$get_collection_order_id)->get();//for activity log
                    $updateData = ['line_item_1'=>$product_implode,'TotalAmt'=>$total_amt];
                    DelOrders::where('order_id',$get_collection_order_id)->update($updateData);

                    //insert in activity log
                    foreach($updateData as $actKey=>$act_UpdateData)
                    {
                        $insertData = [
                            'order_type'=>'DO',
                            'key_id'=>$get_collection_order_id,
                            'operation'=>'Update Del Orders',
                            'fields'=>$actKey,
                            'old_value'=>$get_oldData[0]->$actKey,
                            'new_value'=>$act_UpdateData,
                            'updated_by'=>session('username')
                        ];
                        ActivityLog::insert($insertData);
                    }
                }
            }

            //For pickups order insert in data 
            $temp_date = array();
            $order_pickup = array();
            
            if(!empty($PickupOrders['renewal_table_id']))
            {
                foreach($PickupOrders['cust_pickup_date'] as $key=>$PickupDate)
                {
                    if(in_array($PickupDate,$temp_date)){
                        $key1 = array_search($PickupDate,$temp_date);
                        $cnt = count($order_pickup[$key1]['data']['renew_reminder_id']);
                        $order_pickup[$key1]['data']['renew_reminder_id'][$cnt] = $PickupOrders['renewal_table_id'][$key];
                        $order_pickup[$key1]['data']['order_details_id'][$cnt] = $PickupOrders['order_details_id'][$key];
                        $order_pickup[$key1]['data']['cust_pickup_date'][$cnt] = $PickupOrders['cust_pickup_date'][$key];
                        //$order_pickup[$key1]['data']['cust_pickup_time'][$cnt] = $PickupOrders['cust_pickup_time'][$key];
                        //$order_pickup[$key1]['data']['payment_mode'][$cnt] = $PickupOrders['cust_payment_mode'][$key];
                    }
                    else{
                        array_push($temp_date,$PickupDate);
                       
                        $count_order_c = count($order_pickup);
                        $order_pickup[$count_order_c]['date']=$PickupDate;
                        
                        $order_pickup[$count_order_c]['data']['renew_reminder_id'][0] = $PickupOrders['renewal_table_id'][$key];
                        $order_pickup[$count_order_c]['data']['order_details_id'][0] = $PickupOrders['order_details_id'][$key];
                        $order_pickup[$count_order_c]['data']['cust_pickup_date'][0] = $PickupOrders['cust_pickup_date'][$key];
                        // $order_pickup[$count_order_c]['data']['cust_pickup_time'][0] = $PickupOrders['cust_pickup_time'][$key];
                        //$order_pickup[$count_order_c]['data']['payment_mode'][0] = $PickupOrders['cust_payment_mode'][$key];
                    }
                }
            }
            //dd(["Con"=>$ContinueOrders,"Pi"=>$PickupOrders,"Un"=>$UndecidedOrders,"ord_pick"=>$order_pickup]);
            if(!empty($order_pickup))
            {
                foreach($order_pickup as $key=>$pickup_data)
                {
                    $order_details_id = $pickup_data['data']['order_details_id'][0];
                    //dd($order_details_id);
                    $get_customer_info = DB::table('order_details')->join('del_orders', 'order_details.order_id','=','del_orders.order_id')
                                                ->where('order_details.id',$order_details_id)->get();
                    //dd($get_customer_info);
                    $fulldetails = $get_customer_info[0]->fulldetails;
                    $insert_delorder_data = [
                        'status' => 'Pending',
                        'deliverypickup' => 'Pick Up',
                        'DelAssignedTo' =>'Pending',
                        'shipping_first_name' =>$get_customer_info[0]->shipping_first_name,
                        'location' => $get_customer_info[0]->location,
                        'mobileno' => $get_customer_info[0]->mobileno,
                        //'line_item_1' => $temp_product_name,
                        'DelDate' =>date('d-m-Y',strtotime($pickup_data['date'])),
                        'Pickup_Date' =>$pickup_data['date'],
                        'TotalAmt' => 0,
                        'fulldetails'=> $fulldetails,
                        'TravelMode' =>'Pending',
                        'PickupLocation' =>'Customer',
                        'order_approval_status' =>'Approved'
                    ];
                    $get_pickup_order_id = $DelOrder->insertGetId($insert_delorder_data);
                    $products_arr = array();
                    $products_deposit_arr = array();
                    //get details and insert into renewal
                    for ($i=0; $i <count($pickup_data['data']['renew_reminder_id']) ; $i++) { 
                        $order_details_id = $pickup_data['data']['order_details_id'][$i];
                        $get_order_data = DB::table('order_details')
                                                ->join('del_orders','order_details.order_id','=','del_orders.order_id')
                                                ->join('products','order_details.product_id','=','products.id')
                                                ->select('order_details.*','products.product_name as product_name','del_orders.lead_id as lead_id')
                                                ->where('order_details.id',$order_details_id)->get();
                        array_push($products_arr,$get_order_data[0]->product_name);
                        $start_date = $get_order_data[0]->pickup_date;
                        $end_date = Carbon::parse($start_date)->addMonth()->toDateString();
                        //get product deposit 
                        array_push($products_deposit_arr,$get_order_data[0]->product_deposite);
                        $insert_pickups = [
                            'pickup_order_id'=>$get_pickup_order_id,
                            'del_order_id' => $get_order_data[0]->order_id,
                            'order_details_id' => $pickup_data['data']['order_details_id'][$i],
                            'lead_id' => $get_order_data[0]->lead_id,
                            'vendor_id' => $get_order_data[0]->vendor_id,
                            'product_id' => $get_order_data[0]->product_id,
                            'pickup_date' => $pickup_data['data']['cust_pickup_date'][$i],
                            'cash_amount' => $get_order_data[0]->product_deposite,
                            'created_at' =>Carbon::now()->toDateTimeString()
                        ];
                        $Pickups->insert($insert_pickups);
                        
                        $get_oldData =  RenewalReminder::where('id',$order_pickup[$key]['data']['renew_reminder_id'][$i])->get();
                        //renewal reminder update for pickup
                        $update_data = ['customer_reponse'=>1,
                                        'cust_response_pickup_date'=>$pickup_data['data']['cust_pickup_date'][$i],
                                        // 'cust_response_pickup_time'=>$pickup_data['data']['cust_pickup_time'][$i],
                                        'cust_response_payment'=>null,
                                        'order_id'=>$get_pickup_order_id,
                                        'admin_updated_at'=>Carbon::now()->toDateTimeString(),
                                        'updated_by'=>session('user_id')
                                    ];
                        RenewalReminder::where('id',$order_pickup[$key]['data']['renew_reminder_id'][$i])->update($update_data);
                        //insert in activity log
                        foreach($update_data as $upKey=>$act_UpdatData)
                        {
                            if($act_UpdatData!=$get_oldData[0]->$upKey){
                                $insertData = [
                                    'order_type'=>'RR',
                                    'key_id'=>$renewal_order[$key]['data']['renew_reminder_id'][$i],
                                    'operation'=>'Updated Renewal Reminder',
                                    'fields'=>$upKey,
                                    'old_value'=>$get_oldData[0]->$upKey,
                                    'new_value'=>$act_UpdatData,
                                    'updated_by'=>session('username')
                                ];
                                ActivityLog::insert($insertData);
                            }
                        }

                        $get_oldData = DB::table('order_details')->select('current_status')->where('id',$order_details_id)->get();//for activity log
                        DB::table('order_details')->where('id',$order_details_id)->update(['current_status'=>'Pending Pickup']);    
                        //insert in activity log
                        $insertData = [
                            'order_type'=>'OD',
                            'key_id'=>$order_details_id,
                            'operation'=>'Updated Current Status ',
                            'fields'=>'current_status',
                            'old_value'=>$get_oldData[0]->current_status,
                            'new_value'=>'Pending Pickup',
                            'updated_by'=>session('username')
                        ];
                        ActivityLog::insert($insertData);
                    }
                    //update del orders table collection order id
                    $total_amt = array_sum($products_deposit_arr);
                    $product_implode = implode(",",$products_arr);

                    $get_oldData = DB::table('del_orders')->where('order_id',$get_pickup_order_id)->get();//for activity log
                    $updateData = ['line_item_1'=>$product_implode,'TotalAmt'=>$total_amt];
                    DelOrders::where('order_id',$get_pickup_order_id)->update($updateData);

                    //insert in activity log
                    foreach($updateData as $actKey=>$act_UpdateData)
                    {
                        $insertData = [
                            'order_type'=>'DO',
                            'key_id'=>$get_pickup_order_id,
                            'operation'=>'Update Del Orders',
                            'fields'=>$actKey,
                            'old_value'=>$get_oldData[0]->$actKey,
                            'new_value'=>$act_UpdateData,
                            'updated_by'=>session('username')
                        ];
                        ActivityLog::insert($insertData);
                    }
                }
            }
            $get_oldData = LinkCustDetails::select('id','admin_r_link_status')->where('link_id',$link)->get();
            LinkCustDetails::where('link_id',$link)->update(['admin_r_link_status'=>1]);
            $insertData = [
                'order_type'=>'LCD',
                'key_id'=>$get_oldData[0]->id,
                'operation'=>'Updated link_cust_details',
                'fields'=>'admin_r_link_status',
                'old_value'=>$get_oldData[0]->admin_r_link_status,
                'new_value'=>'1',
                'updated_by'=>session('username')
            ];
            ActivityLog::insert($insertData);
            Session::flash('message','Order Generated Successfully');
            return redirect('/get_renewal_links');
        }
        
    }

    public function ShortUrl($id)
    {
        if(ShortUrl::where('url_link_id',$id)->exists() && RenewalReminder::where('link_id',$id)->exists() && LinkCustDetails::where([['link_id','=',$id],['link_type','=','R'],['link_status','=','0']])->exists())
        {
            $get_link = ShortUrl::where('url_link_id',$id)->first();
            return redirect($get_link->full_url);
        }
        else{
            return "Link Expired";
        }
    }

    //renewal pickup excel file export
    public function fileExport() 
    {
        $export_val = $_GET['export_val'];
        if($export_val!=null)
        {
            $start_date=$_GET['start_date'];
            $end_date=$_GET['end_date'];
            $text_val=$_GET['text_val'];
            ob_end_clean(); // this
            ob_start(); // and this
            return Excel::download(new RenewalPickupExport($export_val,$start_date,$end_date,$text_val), 'renewal.xls');
        }
        else{
            return redirect()->back();
        }
        
    }  

    //pickup requested orders
    public function StopRequested(Request $request)
    {
        $cities =  array();
        if(session('city_based_access') == '1')
        {
            $cities[0] = (object)(['city'=>session('user_city')]);
        }
        else{
            $cities = DB::table('customer_details')->distinct('citygroup')->whereNotNull('citygroup')->orderBy('citygroup','ASC')->get('citygroup');
        }
        $allProducts = DB::table('products')->where('flag','=','Active')->get();
        $textSearch = $request->get('text_search');
        $startDate = $request->get('start_date');
        $date_arr = [];
        $endDate = $request->get('end_date');
        if(isset($startDate) && isset($endDate)){
            array_push($date_arr,$startDate);
            array_push($date_arr,$endDate);
        }
        $selectedProductArr = [];
        $getSelectedProduct = $request->get('selected_product');
        if(isset($getSelectedProduct) && $getSelectedProduct!='All'){
            array_push($selectedProductArr,$getSelectedProduct);
        }
        $orderTypeNotIn = config('app.order_type');
        
        $get_stop_requested = DB::table('order_details')
                                ->select('customer_details.*',
                                        'order_details.*',
                                        'order_details.id as order_details_id',
                                        'vendor_details.registered_name as vendor_name',
                                        'del_orders.DelDate as DelDate',
                                        'leads.patient_name',
                                        'user.username as username','products.product_name as product_name')
                                ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                                ->join('products','order_details.product_id','=','products.id')
                                ->join('vendor_details','order_details.vendor_id','=','vendor_details.id')
                                ->join('del_orders','order_details.order_id','=','del_orders.order_id')
                                ->join('leads','del_orders.lead_id','=','leads.id')
                                ->join('user','leads.lead_owner','=','user.id')
                                ->where('order_details.current_status','=','CustStop')
                                ->Where(function($query)use($textSearch){
                                    $query->orWhere('customer_details.customer_name','LIKE','%'.$textSearch.'%')
                                        ->orWhere('customer_details.address_line_1','LIKE','%'.$textSearch.'%')
                                        ->orWhere('customer_details.address_line_2','LIKE','%'.$textSearch.'%')
                                        ->orWhere('customer_details.area','LIKE','%'.$textSearch.'%')
                                        ->orWhere('customer_details.location','LIKE','%'.$textSearch.'%')
                                        ->orWhere('leads.patient_name','LIKE','%'.$textSearch.'%')
                                        ->orWhere('customer_details.primary_contact_no','LIKE','%'.$textSearch.'%')
                                        ->orWhere('customer_details.secondary_contact_no','LIKE','%'.$textSearch.'%');
                                })
                                //->whereBetween(DB::raw("STR_TO_DATE(order_details.stop_requested_date,'%Y-%m-%d')"),[$minDate,$maxDate])
                                ->when($date_arr,function($query,$date_arr){
                                    $query->whereBetween(DB::raw("STR_TO_DATE(order_details.stop_requested_date,'%Y-%m-%d')"),$date_arr);
                                })
                                ->when($request->get('filter_city') && $request->get('filter_city')!='All',function($query)use($request){
                                    $query->where('customer_details.citygroup',$request->get('filter_city'));
                                })
                                ->when($selectedProductArr,function($query,$selectedProductArr){
                                    $query->whereIn('product_id',$selectedProductArr);
                                })
                                ->when(session('city_based_access')=='1',function($query){
                                    $query->where('customer_details.citygroup',session('user_city'));
                                })
                                ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                                ->orderBy('order_details.stop_requested_date','DESC')
                                ->get();
        foreach($get_stop_requested as $key=>$value)
        {
            if(DB::table('cr_dr_note')->where('order_details_id',$value->order_details_id)->exists())
            {
                $get_stop_requested[$key]->product_rent = $this->fetchCrDrData($value->order_details_id,'R');
                $get_stop_requested[$key]->product_deposite = $this->fetchCrDrData($value->order_details_id,'D');
                $get_stop_requested[$key]->transport = $this->fetchCrDrData($value->order_details_id,'T');
            }
        }
        $productCount = $get_stop_requested->count();
        $get_data = $get_stop_requested->groupBy('customer_id')->paginate(10);
        $getProductwiseData = $get_stop_requested->groupBy('product_id')->toArray();
        $filterArr = [
            "textSearch"=>$textSearch,
            "startDate"=>$startDate,
            "endDate"=>$endDate,
            "selectedProduct"=>$getSelectedProduct
        ];
        return view('RenewalPickup/Stop_Requested',compact('get_data','filterArr','productCount','getProductwiseData','allProducts','cities'));
    }

    public function StopPickupRequest(Request $request)
    {
        if($request->btn_submit=="pickup"){
            $newRequest = new Request();
            $newRequest->merge([
                'checked_product'=>$request->get('checkedProduct'),
                'submit'=>'pickup'
            ]);
            return $this->orderRequest($newRequest);

            $checkedProduct = $request->get('checkedProduct');
            $dueMonths = $request->get('dueMonths');
            $totalProductRent = $request->get('totalProductRent');
            $totalRent = $request->get('totalRent');
            if(isset($checkedProduct)){
                $dueMonthArr = [];
                $totalProductRentArr = [];
                $orderTypeNotIn = config('app.order_type');
                $orderInfo = DB::table('order_details')
                                    ->select('customer_details.*','order_details.id as order_details_id','order_details.*',
                                            'products.product_name as product_name','del_orders.lead_id as lead_id',
                                            'del_orders.status as delivery_status')
                                    ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                                    ->join('del_orders','order_details.order_id','=','del_orders.order_id')
                                    ->join('products','order_details.product_id','=','products.id')
                                    ->whereIn('order_details.id',$checkedProduct)
                                    ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                                    ->get();
                $deliveryOrderId = $orderInfo->pluck('order_id');
                $deliveryStatus = $orderInfo->pluck('delivery_status');
                $incOrder = [];
                foreach ($deliveryStatus as $key => $status) {
                    if($status!="Closed" && $status!="Delivered"){
                        array_push($incOrder,$deliveryOrderId[$key]);
                    }
                }
                foreach($checkedProduct as $key => $product){
                    array_push($dueMonthArr,$dueMonths[$key]);
                    array_push($totalProductRentArr,$totalProductRent[$key]);
                }
                return view('RenewalPickup/Stop_Pickup',compact('dueMonthArr','totalProductRentArr','orderInfo','incOrder'));
            }else{
                Session::flash('message_delete','please select product');
                return redirect()->back();
            }
        }
        else if($request->btn_submit=="renew")
        {
            $newRequest = new Request();
            $newRequest->merge([
                'checked_product'=>$request->get('checkedProduct'),
                'submit'=>'renew'
            ]);
            return $this->orderRequest($newRequest);

            $checkedProduct = $request->get('checkedProduct');
            $dueMonths = $request->get('dueMonths');
            $product_name = $request->get('product_name');
            $total_due_month_rent = $request->get('total_due_month_rent');
            //dd($request->all());
            $data['renew_info'] = array();
               $total_rent = 0;
               $total_deposit = 0;
               $total_due_rent = 0;
               
               //dd($_POST,$get_key,$out_key);
               $data['del_status_arr'] = array();
               // print_r($order_id);
               for ($i=0; $i <count($checkedProduct); $i++) { 
                   //get lead id by order_id from del_orders table
                   $getOrderData = DB::table('order_details')->where('id',$checkedProduct[$i])->first();

                   $get_status = DB::table('del_orders')->where('order_id',$getOrderData->order_id)->get('status');
                   if($get_status[0]->status!='Delivered' && $get_status[0]->status!='Closed')
                   {
                       array_push($data['del_status_arr'],$getOrderData->order_id);
                   }
                   $get_customer_id=$getOrderData->customer_id;
                   $temp_order_details_id= $checkedProduct[$i];
                   $get_lead_id = DB::select("SELECT lead_id,vendor_id FROM del_orders WHERE order_id=$getOrderData->order_id ");
                   $data['get_lead_id'] = json_decode(json_encode($get_lead_id),true);
                   $lead_id = $data['get_lead_id'][0]['lead_id'];
                   $get_vendor_id = DB::select("SELECT vendor_id FROM order_details WHERE id='$checkedProduct[$i]' ");
                   $data['get_vendor_id'] = json_decode(json_encode($get_vendor_id),true);
                   $vendor_id = $data['get_vendor_id'][0]['vendor_id'];

                   //product_id from order_details table 
                   $get_product_id = DB::select("SELECT product_id FROM order_details WHERE id='$checkedProduct[$i]' ");
                   $data['get_product_id'] = json_decode(json_encode($get_product_id),true);
                   $product_id = $data['get_product_id'][0]['product_id'];
                   $due_months =  $dueMonths[$i];

                   $data['renew_info'][$i]['product_name'] = $product_name[$i];
                   $data['renew_info'][$i]['pickup_date'] = $getOrderData->pickup_date;
                   $data['renew_info'][$i]['renewal_date'] = date('Y-m-d',strtotime("+$due_months month",strtotime($getOrderData->pickup_date)));
                   $data['renew_info'][$i]['product_rent'] = $getOrderData->product_rent;
                   $data['renew_info'][$i]['deposit'] = $getOrderData->product_deposite;
                   $data['renew_info'][$i]['order_id'] = $getOrderData->order_id;
                   $data['renew_info'][$i]['order_details_id'] = $checkedProduct[$i];
                   $data['renew_info'][$i]['due_month_count'] = $due_months;
                   $data['renew_info'][$i]['total_due_month_rent'] = $total_due_month_rent[$i];
                   $data['renew_info'][$i]['lead_id'] = $lead_id;
                   $data['renew_info'][$i]['vendor_id'] = $vendor_id;
                   $data['renew_info'][$i]['product_id'] = $product_id;
                   
                   $total_rent += $getOrderData->product_rent;
                   $total_deposit += $getOrderData->product_deposite;
                   $total_due_rent +=$total_due_month_rent[$i];
                }
       
                $order_id_addr = $data['renew_info'][0]['order_id'];
                $address_info = DB::select("SELECT del_orders.* FROM del_orders WHERE del_orders.order_id = $order_id_addr");
                $data['address_info'] = json_decode(json_encode($address_info),true);
                $customer_info =  DB::select("SELECT * FROM customer_details where cust_id = '$get_customer_id' ");
                $data['customer_info'] = json_decode(json_encode($customer_info),true);

               $data['total_rent'] = $total_rent;
               $data['total_deposit'] = $total_deposit;
               $data['total_due_rent'] = $total_due_rent;
                return view('RenewalPickup/renew_product',$data);
        }
        else if($request->btn_submit=="remove")
        {
            $checkedProduct = $request->get('checkedProduct');
            if(isset($checkedProduct)){
                $updateData = DB::table('order_details')->whereIn('id',$checkedProduct)->update(['current_status'=>'Pending']);
                //activity log
                foreach($checkedProduct as $key=>$product)
                {
                    $insertData = [
                        'order_type'=>'OD',
                        'key_id'=>$product,
                        'operation'=>'Update Current Status',
                        'fields'=>'current_status',
                        'old_value'=>'CustStop',
                        'new_value'=>'Pending',
                        'updated_by'=>session('username')
                    ];
                    ActivityLog::insert($insertData);
                }
                Session::flash('message','Product Removed from stop request');
                return redirect()->back();
            }else{
                Session::flash('message_delete','please select product');
                return redirect()->back();
            }
            
        }
    }


    // Whatsapp Msg Reminder

    public function renewalPickupTestWhatsApp(Request $request)
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
                    "phoneNumber": "'.$mobile_no.'",
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
                echo $response;
            }
            
        }
        return view('RenewalPickup.renewal_pickup_test1',compact('renewPickupData','leadUsers','dateFilter','totalRent','dateFilterVal','totalProducts','totalCustomers','totalRentHeador','products'),
                                                        compact('stoppedProducts','stoppedProductsCount','cities'));
    }

    //new renewal pickup test
    public function renewalPickupTest(Request $request)
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
        $dateFilterVal = $request->get('date_filter');
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
                                    $filter = $request->get('date_filter');
                                    if($filter=='Today'){
                                        $date = Carbon::today()->toDateString();
                                        $query->where('order_details.pickup_date',$date);
                                    }if($filter=='Tomorrow'){
                                        $date = Carbon::tomorrow()->toDateString();
                                        $query->where('order_details.pickup_date',$date);
                                    }else if($filter=='Overdue'){
                                        $date = Carbon::today()->toDateString();
                                        if($request->get('overdue_less_date')){
                                            $date = $request->get('overdue_less_date');
                                        }
                                        $query->where('order_details.pickup_date','<',$date);
                                    }else if($filter=='3 Days'){
                                        $date = Carbon::tomorrow()->toDateString();
                                        $dateArr = [$date,Carbon::today()->addDays(3)->toDateString()];
                                        $query->whereBetween('order_details.pickup_date',$dateArr);
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


        $today = Carbon::today()->toDateString();
$taskExists = DB::table('daily_task_log')
    ->where('task_name', '1')
    ->where('task_date', $today)
    ->exists();

$currentTime = Carbon::now()->format('H:i');

if (!$taskExists && $currentTime >= '10:00') {
    foreach ($renewPickupData as $key => $renewPickup) {
        if ($renewPickup[0]->current_status != 'CustStop') {
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
                    "phoneNumber": "'.$mobile_no.'",
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

    // Save task status
    DB::table('daily_task_log')->insert([
        'task_name' => '1',
        'task_date' => $today,
    ]);
}


        return view('RenewalPickup.renewal_pickup_test',compact('renewPickupData','leadUsers','dateFilter','totalRent','dateFilterVal','totalProducts','totalCustomers','totalRentHeador','products'),
                                                        compact('stoppedProducts','stoppedProductsCount','cities'));
    }

    public function getOrderData(Request $request)
    {
        $order_details_id = json_decode($request->get('order_details_id'));
        $orderTypeNotIn = config('app.order_type');
        $getOrderData = DB::table('order_details')
                        ->select('order_details.*','order_details.id as order_details_id','vendor_details.registered_name as vendor_name','del_orders.DelDate as DelDate','products.product_name','customer_details.customer_type')
                        ->join('del_orders','order_details.order_id','=','del_orders.order_id')
                        ->join('leads','del_orders.lead_id','=','leads.id')
                        ->join('products','order_details.product_id','=','products.id')
                        ->join('vendor_details','order_details.vendor_id','=','vendor_details.id')
                        ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                        ->whereIn('order_details.id',$order_details_id)
                        ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                        ->get()->toArray();
        $orderMonthData = [];
        foreach ($getOrderData as $key => $orderData) 
        {
            $today = Carbon::today()->toDateString();
            $getOrderData[$key]->product_rent = $this->fetchCrDrData($orderData->order_details_id,'R');
            $getOrderData[$key]->product_deposite = $this->fetchCrDrData($orderData->order_details_id,'D');
            $getOrderData[$key]->transport = $this->fetchCrDrData($orderData->order_details_id,'T');
            $monthCount = $this->getBillingPeriod($orderData->pickup_date,$orderData->billing_unit,$today);
            $productMonthRent = $monthCount*$orderData->product_rent;
            $orderMonthData[$key]['month_count'] = $monthCount;
            $orderMonthData[$key]['total_rent'] = $productMonthRent;
            $getOrderData[$key]->pickup_date = date('d-m-Y',strtotime($orderData->pickup_date));
        }
        return(['orderProducts'=>$getOrderData,'productMonthData'=>$orderMonthData]);
    }

    public function orderRequest(Request $request)
    {
        $orderId = $request->get('checked_product');
        if(!empty($orderId)){
            $orderTypeNotIn = config('app.order_type');
            $productData = DB::table('order_details')
                            ->select('order_details.*','order_details.id as order_details_id','customer_details.*','products.product_name','del_orders.status as delivery_status')
                            ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                            ->join('products','order_details.product_id','=','products.id')
                            ->join('del_orders','order_details.order_id','=','del_orders.order_id')
                            ->whereIn('order_details.id',$orderId)
                            ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                            ->get();
            $orderMonthData = [];          
            $orderNotDelivered = [];
            foreach ($productData as $key => $data) 
            {
                $productData[$key]->product_rent = $this->fetchCrDrData($data->order_details_id,'R');
                $productData[$key]->product_deposite = $this->fetchCrDrData($data->order_details_id,'D');
                $productData[$key]->transport = $this->fetchCrDrData($data->order_details_id,'T');
                
                $today = Carbon::today()->toDateString();
                
                // New Code
                $monthCount = 0;
                $monthCount = $this->getBillingPeriod($data->pickup_date,$data->billing_unit,$today);
                $productMonthRent = $monthCount*$data->product_rent;
                $orderMonthData[$key]['month_count'] = $monthCount;
                $orderMonthData[$key]['total_rent'] = $productMonthRent;
                // $orderMonthData[$key]['next_renew_date'] = Carbon::parse($data->pickup_date)->addMonths(1)->toDateString();
    
                //del status
                if($data->delivery_status!='Delivered' && $data->delivery_status!='Closed')
                {
                    array_push($orderNotDelivered,$data->order_id);
                }
                // dd($data->order_details_id);
                if(DB::table('adjustment_table')->where('order_details_id',$data->order_details_id)->where('fromtype','D')->where('adjustment_table.flag','A')->exists())
                {
                    $records = DB::table('adjustment_table')->select('adjusted_amount')->where('order_details_id',$data->order_details_id)->where('fromtype','D')->where('adjustment_table.flag','A')->get();
                    // dd($records);
                    $sum = $records->pluck('adjusted_amount')->sum();
                    // dd($sum);
                    $productData[$key]->adjusted_deposit = $sum;
                }
                else
                {
                    $productData[$key]->adjusted_deposit = 0;
                }
            }
            if($request->get('submit')=='renew'){
                return view('RenewalPickup.order-renew',compact('productData','orderMonthData','orderNotDelivered'));
            }
            else if($request->get('submit')=='pickup'){
                return view('RenewalPickup.order-pickup',compact('productData','orderMonthData','orderNotDelivered'));
            }
            elseif($request->get('submit')=='stop_product'){
                $orderIds = explode(",",$request->get('checked_product')[0]);
                $stopReason = $request->get('stop_reason');
                
                $getProducts = DB::table('order_details')->whereIn('id',$orderIds)->get();
                foreach ($getProducts as $key => $product) {
                    if($product->current_status!='CustStop'){
                        $orderUpdateData = [
                            'current_status'=>'CustStop',
                            'stop_requested_date'=>Carbon::now()->toDateTimeString(),
                            'stop_requested_by'=>session('user_id'),
                            'stop_requested_reason'=>$stopReason,
                        ];
                        $insertActivityLog = [
                            'order_type'=>'OD',
                            'key_id'=>$product->id,
                            'operation'=>'Stop Request Product',
                            'fields'=>'current_status',
                            'old_value'=>$product->current_status,
                            'new_value'=>'CustStop',
                            'updated_by'=>session('username')
                        ];
                        ActivityLog::insert($insertActivityLog);
                        DB::table('order_details')->where('id',$product->id)->update($orderUpdateData);

                        $orderDetails = DB::table('order_details')
                                        ->where('id', $product->id)
                                        ->first(['customer_id', 'order_id']);

                        $customerId = $orderDetails->customer_id; 
                        $orderId = $orderDetails->order_id;

                        $customerDetails = DB::table('customer_details')->where('cust_id', $customerId)->first();

                        $mobile = $customerDetails->primary_contact_no;

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
                            "phoneNumber": "'.$mobile.'",
                            "callbackData": "some text here",
                            "type": "Template",
                            "template": {
                                "name": "pickup_stoprequest",
                                "languageCode": "en",
                                "headerValues": [
                                    "header_variable_value"
                                ],
                                "bodyValues": [
                                    "'.$orderId.'"
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

                    }
                }
                Session::flash('message','Stop request added succesfully');
                return redirect()->back();                
            }
            else if($request->get('submit') == 'renew-and-pickup'){
                return view('RenewalPickup.order-renew-pickup',compact('productData','orderMonthData','orderNotDelivered'));
            }
        }else
        {
            return redirect()->back()->with('message_delete','Please select product');
        }
    }

    public function orderCall(Request $request){
        DB::beginTransaction();
        try{
            if($request->get('submit')=='renew'){
                //dd($request->all());
                $order_details_id = $request->get('order_details_id');
                $monthsRenew = $request->get('payment_months');
                $discount = $request->get('discount_offered');
                $depositAdjust = $request->get('deposit_adjust');
                $paymentMode = $request->get('payment_mode');
    
                $tempMonthsRenew = [];
                $tempDiscount = [];
                $tempDepositAdjust = [];
                foreach($order_details_id as $key=>$order){
                    $tempMonthsRenew[$order] = $monthsRenew[$key];
                    $tempDiscount[$order] = $discount[$key];
                    $tempDepositAdjust[$order] = $depositAdjust[$key];
                }
                //$collectionPeriod = $request->get('collection_of');
    
                //$status = $this->OrderRenew($order_details_id,$monthsRenew,$paymentMode,$discount,$depositAdjust);
                $status = $this->OrderRenew($order_details_id,$tempMonthsRenew,$paymentMode,$tempDiscount,$tempDepositAdjust);
                if($status=='success'){
                    if($paymentMode=='Cash'){
                        $redUrl =url('/')."/renew_request";
                    }else{
                        $redUrl =url('/')."/pending_online_renew";
                    }
                    DB::commit();
                    return redirect()->route('renewalpickup-test',['date_filter'=>'Today'])->with('message','Collection Order Generated Successfully')
                                                                ->with('collection_url',$redUrl);
                }else{
                    DB::rollback();
                    return redirect()->route('renewalpickup-test',['date_filter'=>'Today'])->with('message_delete','something went wrong');
                }
            }else if($request->get('submit')=='pickup'){
                $orderDetailsId = $request->get('order_details_id') ;
                $pickupDates = $request->get('pickup_date');
                $pickupData = [];
                foreach ($orderDetailsId as $key => $value) {
                    $pickupData[$key]['id']=$value;
                    $pickupData[$key]['pickup_date']=$pickupDates[$key];
                }
               $status = $this->OrderPickup($pickupData);
    
                if(is_array($status)){
                    $redUrl = url('/')."/pickup_request";
                    DB::commit();
                    return redirect()->route('renewalpickup-test',['date_filter'=>'Today'])->with('message','Pickup Order Generated Successfully')
                                                                ->with('collection_url',$redUrl);
                }else{
                    DB::rollback();
                    return redirect()->route('renewalpickup-test',['date_filter'=>'Today'])->with('message_delete','something went wrong');
                }
            }else if($request->get('submit')=='renew-and-pickup'){
                $order_details_id = $request->get('renewalOrderDetailsId');
                $monthsRenew = $request->get('payment_months');
                $discount = $request->get('discount_offered');
                $depositAdjust = $request->get('deposit_adjust');
                $paymentMode = $request->get('payment_mode');
                $tempMonthsRenew = [];
                $tempDiscount = [];
                $tempDepositAdjust = [];
                foreach($order_details_id as $key=>$order){
                    $tempMonthsRenew[$order] = $monthsRenew[$key];
                    $tempDiscount[$order] = $discount[$key];
                    $tempDepositAdjust[$order] = $depositAdjust[$key];
                }
                $status = $this->OrderRenew($order_details_id,$tempMonthsRenew,$paymentMode,$tempDiscount,$tempDepositAdjust);
                if($status=='success'){
                    $orderDetailsId = $request->get('pickupOrderDetailsId') ;
                    $pickupDates = $request->get('pickup_date');
                    $pickupData = [];
                    foreach ($orderDetailsId as $key => $value) {
                        $pickupData[$key]['id']=$value;
                        $pickupData[$key]['pickup_date']=$pickupDates[$key];
                    }
                    $status = $this->OrderPickup($pickupData);
                    DB::commit();
                    return redirect()->to('pending_payments')->with('message','Orders Generated Successfully');    
                }else{
                    DB::rollback();
                    return redirect()->back()->with('error','Something went wrong: '.$status);
                }
            }else{
                DB::rollback();
                return redirect()->back()->with('error','Something went wrong: No submit value!');
            }
        }catch(Exception $ex){
            DB::rollback();
            return redirect()->back()->with('error','Something went wrong: '.$ex->getMessage());
        }
    }

    // Renew Order
    public function OrderRenew($id,$monthsRenew,$paymentMode,$discount,$depositAdjust){
        DB::beginTransaction();
        try{
            $orderTypeNotIn = config('app.order_type');
            $getProductsData = DB::table('order_details')
                                ->select('order_details.*','customer_details.*','products.product_name','del_orders.lead_id')
                                ->join('products','order_details.product_id','=','products.id')
                                ->join('del_orders','order_details.order_id','=','del_orders.order_id')
                                ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                                ->whereIn('order_details.id',$id)
                                ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                                ->get();
            $leadId = $getProductsData[0]->lead_id;
            $getLeadsData = DB::table('leads')->where('id',$leadId)->first();
            
            //insert first Del order
            if($getProductsData->count()>1){
                $productName = $getProductsData->pluck('product_name')->implode(',');
            }else{
                $productName = $getProductsData[0]->product_name;
            }
            $customerDetails = $getProductsData[0]->customer_name.",".$getProductsData[0]->address_line_1.",".$getProductsData[0]->address_line_2.",".$getProductsData[0]->area.",".$getProductsData[0]->location.",".$getProductsData[0]->city.",".$getProductsData[0]->pincode;
            $invoice_no = DB::table('misc_table')->where('field','invoice_no')->first('value')->value;
            $insertDelOrder =  [
                'status' => 'Pending',
                'deliverypickup' => 'Collection',
                'DelAssignedTo' =>'Pending',
                'lead_id'=>$getLeadsData->id,
                'invoice_no'=>$invoice_no + 1,
                'patient_name'=>$getLeadsData->patient_name,
                'shipping_first_name' => $getProductsData[0]->customer_name,
                'location' => $getProductsData[0]->location,
                'mobileno' => $getProductsData[0]->primary_contact_no,
                'line_item_1' => $productName,
                'DelDate' =>date('d-m-Y',strtotime(Carbon::today()->toDateString())),
                'Collection_Date' =>Carbon::today()->toDateString(),
                'TotalAmt' => 0,
                'fulldetails'=> $customerDetails,
                'TravelMode' =>'Pending',
                'PaymentMode'=>$paymentMode,
                'cash'=>0,
                'online'=>0,
                'PickupLocation' =>'Customer',
                'order_approval_status' =>'Approved'
            ];
            DB::table('misc_table')->where('field','invoice_no')->update(['value'=>$invoice_no + 1]);
            $collectionOrderId = DB::table('del_orders')->insertGetId($insertDelOrder);
            
            $totalAmount = 0;
            foreach ($getProductsData as $key => $product) {
                $getProductsData[$key]->product_rent = $this->fetchCrDrData($product->id,'R');
                $getProductsData[$key]->product_deposite = $this->fetchCrDrData($product->id,'D');
                $getProductsData[$key]->transport = $this->fetchCrDrData($product->id,'T');
                $startDate = 0;
                for ($i=0; $i <$monthsRenew[$product->id] ; $i++) { 
                    if($i==0){
                        $startDate = $getProductsData[$key]->pickup_date;
                    }
                    if($product->billing_unit == 'Week'){
                        $endDate = Carbon::parse($startDate)->addWeeks(1)->toDateString();
                    }else if($product->billing_unit == 'Half Month'){
                        $product->billing_period = $product->billing_period * 2;
                        $endDate = Carbon::parse($startDate)->addWeeks(2)->toDateString();
                    }else if($product->billing_unit == 'Days'){
                        $endDate = Carbon::parse($startDate)->addDays(1)->toDateString();
                    }else{
                        $endDate = Carbon::parse($startDate)->addMonths(1)->toDateString();
                    }
                    //echo $collectionOrderId;
                    $insertCollectionData= [
                        'collection_order_id'=>$collectionOrderId,
                        'order_id'=>$getProductsData[$key]->order_id,
                        'order_details_id'=>$getProductsData[$key]->id,
                        'lead_id'=>$getProductsData[$key]->lead_id,
                        'vendor_id'=>$getProductsData[$key]->vendor_id,
                        'product_id'=>$getProductsData[$key]->product_id,
                        'start_date'=>$startDate,
                        'end_date'=> $endDate,
                        'payment_mode'=>$paymentMode,
                        'cash_amount'=>($paymentMode=='Cash')?$getProductsData[$key]->product_rent-($discount[$product->id]/$monthsRenew[$product->id]):0,
                        'online_amount'=>($paymentMode=='Online')?$getProductsData[$key]->product_rent-($discount[$product->id]/$monthsRenew[$product->id]):0,
                        'discount_amt'=>($discount[$product->id]/$monthsRenew[$product->id]),
                        'total_amt'=>$getProductsData[$key]->product_rent,
                        //'online_method',
                        'status'=>'Pending',
                        'payment_status'=>'Pending',
                        'created_by'=>session('username'),
                        'created_at'=>date('Y-m-d H:i:s')
                    ];
                    // $startDate = Carbon::parse($startDate)->addMonths(1)->toDateString();
                    if($product->billing_unit == 'Week'){
                        $startDate = Carbon::parse($startDate)->addWeeks(1)->toDateString();
                    }else if($product->billing_unit == 'Half Month'){
                        $product->billing_period = $product->billing_period * 2;
                        $startDate = Carbon::parse($startDate)->addWeeks(2)->toDateString();
                    }else if($product->billing_unit == 'Days'){
                        $startDate = Carbon::parse($startDate)->addDays(1)->toDateString();
                    }else{
                        $startDate = Carbon::parse($startDate)->addMonths(1)->toDateString();
                    }
                    $totalAmount += $getProductsData[$key]->product_rent-($discount[$product->id]/$monthsRenew[$product->id]);
                    $insertedRenewalId = DB::table('renewals')->insertGetId($insertCollectionData);
                    
                    //update deposit if adjusted 
                    if($depositAdjust[$product->id] > 0 && $i==0){
                        // $depositAmount = $product->product_deposite-$depositAdjust[$product->id];
                        // DB::table('order_details')->where('id',$product->id)->update(['product_deposite'=>$depositAmount]);
                        $insertRecord = [
                            'product_id'=>$getProductsData[$key]->product_id,
                            'order_id'=>$collectionOrderId,
                            'order_details_id'=>$getProductsData[$key]->id,
                            'adjusted_order_details_id'=>$insertedRenewalId,
                            'fromorderid'=>$getProductsData[$key]->order_id,
                            'adjusted_amount'=>$depositAdjust[$product->id],
                            'fromtype'=>'D',
                            'intype'=>'R',
                        ];
                        DB::table('adjustment_table')->insert($insertRecord);
                        $comment = "Rent adjusted against deposit : ".$depositAdjust[$product->id];
                        DB::table('renewals')->where('id',$insertedRenewalId)->update(['adjusted_deposit'=>$depositAdjust[$product->id],'comment'=>$comment]);
                    }
                }
    
                $updateOrderDetails = [
                    //'pickup_date'=>$temp_pickup_date,
                    'collection_date'=>Carbon::today()->toDateString(),
                    'current_status' =>'Pending Renew'
                ];
                foreach ($updateOrderDetails as $key1 => $upData) 
                {
                    $insertActivityData = [
                        'order_type'=>'OD',
                        'key_id'=>$getProductsData[$key]->id,
                        'operation'=>'Collection Order Generated',
                        'fields'=>$key1,
                        'old_value'=>$getProductsData[$key]->$key1,
                        'new_value'=>$upData,
                        'updated_by'=>session('username')
                    ];
                    ActivityLog::insert($insertActivityData);
                }
                DB::table('order_details')->where('id',$getProductsData[$key]->id)->update($updateOrderDetails);
            }
            DB::table('del_orders')->where('order_id',$collectionOrderId)->update(['TotalAmt'=>$totalAmount,'cash'=>($paymentMode=='Cash')?$totalAmount:0,'online'=>($paymentMode=='Online')?$totalAmount:0]);
            
            $logDate = Carbon::now()->toDateString();
            $logTime = Carbon::now()->toTimeString();
            DB::table('leads_log')->insert([
                'log_order_id'=>$collectionOrderId,
                'log_order_lead_date'=>Carbon::now()->toDateTimeString(),
                'log_lead_status'=>'Order Generated',
                'log_order_type'=>'CO',
                'log_date'=>$logDate,
                'log_time'=>$logTime,
                'updated_by'=>session('username')
            ]);
            DB::table('misc_table')->where('field','invlice_no')->update(['value'=>$invoice_no+1]);
            DB::commit();
            return "success";
        }catch(Exception $ex){
            DB::rollBack();
            return $ex->getMessage();
        }
    }

    //Pickup Order
    public function OrderPickup($pickupData){
        DB::beginTransaction();
        try{
            $data = Collect($pickupData);
            $whOrderDetailsId = $data->pluck('id');
            $data = $data->groupBy('pickup_date');
            $pickupOrderIdsArr = array();
            $orderTypeNotIn = config('app.order_type');
            foreach ($data as $key => $pickupData) {
                //insert in delorders
                $id = $pickupData->pluck('id');
                $getProductsData = DB::table('order_details')
                        ->select('order_details.*','customer_details.*','products.product_name','del_orders.lead_id')
                        ->join('products','order_details.product_id','=','products.id')
                        ->join('del_orders','order_details.order_id','=','del_orders.order_id')
                        ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                        ->whereIn('order_details.id',$id)
                        ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                        ->get();
    
                $leadId = $getProductsData[0]->lead_id;
                $getLeadsData = DB::table('leads')->where('id',$leadId)->first();
    
                //get total due rent or other 
                $orderMonthData = [];          
                $deposite_amount = 0;          
                foreach ($getProductsData as $key1 => $data) 
                {
                    $today = Carbon::today()->toDateString();
                    $monthCount = Carbon::parse($data->pickup_date)->diffInMonths($today);
                    $currentRenewDate = Carbon::parse($data->pickup_date)->addMonths($monthCount);
                    if(Carbon::parse($currentRenewDate)->diffInDays($today)>0){
                        $monthCount = $monthCount+1;
                    }
                    if(Carbon::parse($data->pickup_date)->diffInDays($today)==0)
                    {
                        $monthCount = 1;
                    }
                    $productMonthRent = $monthCount*$data->product_rent;
                    $orderMonthData[$key1]['month_count'] = $monthCount;
                    $orderMonthData[$key1]['total_rent'] = $productMonthRent;
                    $deposite_amount = $deposite_amount + $data->product_deposite;
                }
    
                //for insert in del orders
                if($getProductsData->count()>1){
                    $productName = $getProductsData->pluck('product_name')->implode(',');
                }else{
                    $productName = $getProductsData[0]->product_name;
                }
    
                $customerDetails = $getProductsData[0]->customer_name.",".$getProductsData[0]->address_line_1.",".$getProductsData[0]->address_line_2.",".$getProductsData[0]->area.",".$getProductsData[0]->location.",".$getProductsData[0]->city.",".$getProductsData[0]->pincode;
                $pickupDate = $pickupData[0]['pickup_date'];
                $insertDelData = [
                    'status' => 'Pending',
                    'deliverypickup' => 'Pick Up',
                    'DelAssignedTo' =>'Pending',
                    'lead_id'=>$getLeadsData->id,
                    'patient_name'=>$getLeadsData->patient_name,
                    'shipping_first_name' => $getProductsData[0]->customer_name,
                    'location' =>$getProductsData[0]->location,
                    'mobileno' => $getProductsData[0]->primary_contact_no,
                    'line_item_1' => $productName,
                    'DelDate' =>date('d-m-Y',strtotime($pickupDate)),
                    'Pickup_Date' => $pickupDate,
                    'TotalAmt' => 0,
                    'fulldetails'=> $customerDetails,
                    'TravelMode' =>'Pending',
                    'PickupLocation' =>'Customer',
                    'order_approval_status' =>'Approved'
                ];
                $pickupOrderId = DB::table('del_orders')->insertGetId($insertDelData);
                //push order ids in pickup order ids array
                array_push($pickupOrderIdsArr,$pickupOrderId);
    
                $totalRecDeposit = 0;
                $totalAdjDeposit = 0;
                
                foreach ($getProductsData as $key2 => $product) {
                    if(DB::table('adjustment_table')->where('order_details_id',$product->id)->where('fromtype','D')->where('adjustment_table.flag','A')->exists())
                    {
                        // dd("Exists");
                        $records = DB::table('adjustment_table')->select('adjusted_amount')->where('order_details_id',$product->id)->where('fromtype','D')->where('adjustment_table.flag','A')->get();
                        // dd($records);
                        $sum = $records->pluck('adjusted_amount')->sum();
                        // dd($sum);
                        // dd($product->product_deposite);
                        // $productData[$key]->adjusted_deposit = $sum;
                        $totalAdjDeposit = $totalAdjDeposit + $sum;
                        // dd($totalDeposit);
                    }
                    // $totalRecDeposit = $totalRecDeposit + $product->product_deposite;
                    $totalRecDeposit = $totalRecDeposit + $this->fetchCrDrData($product->id,'D');
                    $insertPickupsData = [
                        'pickup_order_id'=>$pickupOrderId,
                        'del_order_id' => $product->order_id,
                        'order_details_id' => $product->id,
                        'lead_id' => $product->lead_id,
                        'vendor_id' => $product->vendor_id,
                        'product_id' => $product->product_id,
                        'pickup_date' => $pickupDate,
                        'cash_amount' => $orderMonthData[$key2]['total_rent'],
                        'created_at' =>date('Y-m-d h:i:s')
                    ];
                    DB::table('pickups')->insert($insertPickupsData);
                    $insertActivityLog = [
                        'order_type'=>'OD',
                        'key_id'=>$product->id,
                        'operation'=>'Pickup Order Generated',
                        'fields'=>'current_status',
                        'old_value'=>$product->current_status,
                        'new_value'=>'Pending Pickup',
                        'updated_by'=>session('username')
                    ];
                    ActivityLog::insert($insertActivityLog);
                    DB::table('order_details')->where('id',$product->id)->update(['current_status'=>'Pending Pickup']);
    
                    // if(DB::table('customer_details')->select('email_id','customer_name')->where('cust_id',$product->customer_id)->where('cust_source','B2B')->exists())
                    // {
                    //     $customer_details = DB::table('customer_details')->select('created_by','customer_name')->where('cust_id',$customer_id)->get()->toArray();
                    //     $user_details = DB::table('user')->select('id','username','email_id_user')->where('username',$customer_details[0]->created_by)->get()->toArray();
                    //     $user_email = $user_details[0]->email_id_user;
                    //     $user_name = $customer_details[0]->customer_name;
                    //     DelOrders::where('order_id',$pickup_id)->update(['order_owner'=>$user_details[0]->id]);
                    //     // $user_email = session('email_id');
                    //     $title = 'Pickup Order Generated for '.$user_name;
                    //     $message = ['message1'=>'Pickup Order Generated Successfully for '.$user_name.'.'];
                    //     DeliveryController::sendMailAlertUser($user_email,$user_name,$title,$message);
                    //     $admin_email = 'abhishekn@quali55care.com';
                    //     $title = 'Pickup Order Generated from '.session('username').'.';
                    //     $message = ['message1'=>'Check orders details page for more details about order.'];
                    //     DeliveryController::sendMailAlertAdmin($admin_email,$user_name,$title,$message);
                    // }
                }
    
                //update total amt of pickup order id
                DB::table('del_orders')->where('order_id',$pickupOrderId)->update(['TotalAmt'=>$totalRecDeposit-$totalAdjDeposit]);
    
                $logDate = Carbon::now()->toDateString();
                $logTime = Carbon::now()->toTimeString();
                DB::table('leads_log')->insert([
                    'log_order_id'=>$pickupOrderId,
                    'log_lead_status'=>'Order Generated',
                    'log_order_type'=>'PO',
                    'log_order_lead_date'=>$pickupDate.' '.$logTime,
                    'log_date'=>$logDate,
                    'log_time'=>$logTime,
                    'updated_by'=>session('username')
                ]);
            }
    
            //send wp message
            $orderTypeNotIn = config('app.order_type');
            $pickupOrderIds = Collect($pickupOrderIdsArr)->implode(',');
            $whData = DB::table('order_details')
                        ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                        ->join('products','order_details.product_id','=','products.id')
                        ->join('del_orders','order_details.order_id','=','del_orders.order_id')
                        ->join('leads','del_orders.lead_id','=','leads.id')
                        ->join('user','leads.lead_owner','=','user.id')
                        ->select('customer_details.*','products.product_name','user.contact_no as lead_owner_contact')
                        ->whereIn('order_details.id',$whOrderDetailsId)
                        ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                        ->get();
            if($whData[0]->customer_type == 'Individual'){
                $productList = $whData->pluck('product_name')->implode(',');
                $custWhContact = $whData[0]->primary_contact_no;
                $customerName = $whData[0]->customer_name;
                $leadOwnerContact = $whData[0]->lead_owner_contact;
                $app_env = config('app.app_env');
                $LeadController = new LeadController();//controller file class
                $link_id = $LeadController->GenerateLinkid();
                $linkDetailsInsert = [
                    'primary_contact_no'=>$custWhContact,
                    'customer_name'=>$customerName,
                    'customer_id'=>$whData[0]->cust_id,
                    'link_id'=>$link_id,
                    'order_ids'=>$pickupOrderIds,
                    'link_type'=>'PN'
                ];
                DB::table('link_cust_details')->insert($linkDetailsInsert);
                $getBankLink = "http://intra.quali55care.com/$app_env/eflow/cust-bank/".$link_id;
    
                //send whatsapp message 
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
                $contactNo = $custWhContact; 
                if(config('app.app_env') == 'devweb')
                {
                    $contactNo = config('app.developer_contact');
                }
                $data =[
                    "portno"=>"11140",
                    "namespace"=>"b9a23cb4_89ed_4fe2_b849_20775908ff5e",
                    "countrycode"=> "91",
                    "mobileno"=> "$contactNo",
                    //"headerimageurl"=>"https://s3.ap-south-1.amazonaws.com/quali55care.com/assets/RESOURCES/logo_quli5care.png",
                    "templatename" => "customer_pickup_notification",
                    "templateparams" => [
                        ["type"=> "text","text"=> "$customerName"],
                        ["type"=> "text","text"=> "$productList"],
                        ["type"=> "text","text"=> "$getBankLink"],
                        ["type"=> "text","text"=> "$leadOwnerContact"],
                    ],
                ];
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                $resp = curl_exec($curl);
                curl_close($curl);
            }
            DB::commit();
            return $pickupOrderIdsArr;
        }catch(Exception $ex){
            DB::rollback();
            return $ex->getMessage();
        }

    }

    public static function fetchCrDrData($id,$intype)
    {
        $order_details = DB::table('order_details')->where('id',$id)->first();
        if(DB::table('cr_dr_note')->where('order_details_id',$order_details->id)->where('intype',$intype)->where('flag','A')->exists())
        {
            $cr_dr_notes = DB::table('cr_dr_note')->where('order_details_id',$order_details->id)->where('intype',$intype)->get()->groupBy('crdrtype');
            if(isset($cr_dr_notes['Cr']))
            {
                $creditnotes = $cr_dr_notes['Cr']->groupBy('intype');
                if($intype == 'R')
                {
                    $order_details->product_rent = $order_details->product_rent - array_sum($creditnotes['R']->pluck('amount')->toArray());
                }
                if($intype == 'D')
                {
                    $order_details->product_deposite = $order_details->product_deposite - array_sum($creditnotes['D']->pluck('amount')->toArray());
                }
                if($intype == 'T')
                {
                    $order_details->transport = $order_details->transport - array_sum($creditnotes['T']->pluck('amount')->toArray());
                }
            }
            if(isset($cr_dr_notes['Dr']))
            {
                $debitnotes = $cr_dr_notes['Dr']->groupBy('intype');
                if(isset($debitnotes['R']))
                {
                    $order_details->product_rent = $order_details->product_rent + array_sum($debitnotes['R']->pluck('amount')->toArray());
                }
                if(isset($debitnotes['D']))
                {
                    $order_details->product_deposite = $order_details->product_deposite + array_sum($debitnotes['D']->pluck('amount')->toArray());
                }
                if(isset($debitnotes['T']))
                {
                    $order_details->transport = $order_details->transport + array_sum($debitnotes['T']->pluck('amount')->toArray());
                }
            }
        }
        if($intype == 'R')
        {
            return $order_details->product_rent;
        }
        if($intype == 'D')
        {
            return $order_details->product_deposite;
        }
        if($intype == 'T')
        {
            return $order_details->transport;
        }
    }
    public function getOverduePeriod(Request $request){
        $order_details = DB::table('order_details')->where('id',$request->get('id'))->first();
        $months = array();
        for($date = date('Y-m-d',strtotime($order_details->pickup_date)); date('Y-m-d',strtotime($date))<date('Y-m-d'); $date = date('Y-m-d',strtotime("+1 months",strtotime($date)))){
            $count = count($months);
            $months[$count]['order_details_id'] = $order_details->id;
            $months[$count]['start_date'] = date('Y-m-d',strtotime($date));
            $months[$count]['end_date'] = date('Y-m-d',strtotime("+1 months",strtotime($date)));
            $months[$count]['period'] = date('d-M-y',strtotime($date))." - ".date('d-M-y',strtotime("+1 months",strtotime($date)));
            $months[$count]['amount'] = $order_details->product_rent;
            if(DB::table('corporate_renewal')->where('order_details_id',$request->get('id'))->where('start_date',$date)->where('end_date',date('Y-m-d',strtotime("+1 months",strtotime($date))))->exists()){
                $invoice_no = DB::table('corporate_renewal')->where('order_details_id',$request->get('id'))->where('start_date',$date)->where('end_date',date('Y-m-d',strtotime("+1 months",strtotime($date))))->first()->invoice_no;
                if($invoice_no != null){
                    $months[$count]['invoice_no'] = $invoice_no;
                }else{
                    $months[$count]['invoice_no'] = "";
                }
            }else{
                $months[$count]['invoice_no'] = "";
            }
        }
        return $months;
    }
    public function addInvoiceNos(Request $request){
        // dd($request->all());
        foreach($request->get('alldetails') as $key=>$value){
            $value= json_decode($value);
            DB::table('corporate_renewal')->updateOrInsert(
                [
                    'order_details_id'=>$value->order_details_id,
                    'start_date'=>$value->start_date,
                    'end_date'=>$value->end_date
                ],
                [
                    'amount'=>$value->amount,
                    'invoice_no'=>$request->get('update_invoice_ids')[$key],
                    'created_by'=>session('username'),
                    'updated_by'=>session('username')
                ]
            );
        }
        return redirect()->back()->with('message','Invoice Linked Successfully!');
    }

    public static function getBillingPeriod($pickup_date,$billingUnit,$end_date){
        $monthCount = 0;
        if($billingUnit == 'Days'){
            if(date('Y-m-d') > $pickup_date){
                $monthCount = Carbon::parse($pickup_date)->diffInDays($end_date);
            }else{
                $monthCount = 1;
            }
        }
        elseif($billingUnit == 'Week'){
            if(date('Y-m-d') > $pickup_date){
                $days = Carbon::parse($pickup_date)->diffInDays($end_date);
                $monthCount = (int)($days / 7);
                if(($days - (7*$monthCount)) >0){
                    $monthCount++;
                }
            }else{
                $monthCount = 1;
            }
        }else if($billingUnit == "Half Month"){
            if(date('Y-m-d') > $pickup_date){
                $days = Carbon::parse($pickup_date)->diffInDays($end_date);
                $monthCount = (int)($days / 14);
                if(($days - (14*$monthCount)) >0){
                    $monthCount++;
                }
            }else{
                $monthCount = 1;
            }
        }else{
            $monthCount = Carbon::parse($pickup_date)->diffInMonths($end_date);
            // return $monthCount;
            $currentRenewDate = Carbon::parse($pickup_date)->addMonths($monthCount);
            if(Carbon::parse($currentRenewDate)->diffInDays($end_date)>0){
                $monthCount++;
            }
            // if(Carbon::parse($pickup_date)->diffInDays($end_date)==0)
            else
            {
                $monthCount = 1;
            }
        }
        return $monthCount;
    }
}
?>