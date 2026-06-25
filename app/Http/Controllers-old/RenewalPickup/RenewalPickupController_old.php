<?php

namespace App\Http\Controllers\RenewalPickup;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\VendorRegister;
use App\Models\UserRegister;
use App\Models\DelOrders;
use App\Models\OrderDetails;
use App\Models\customer_detail;
use App\Models\Renewal;
use App\Models\Pickup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PDF;
use Mail;
use Session;

class RenewalPickupController extends Controller
{
    public function isLoggedIn()
    {
        $data = session('isLoggedIn');
        return $data;
    }

    //default show info renewal pickup
    public function renewal_pickup()
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
         return redirect()->to($url);
        }
        if($_SERVER['REQUEST_METHOD']=='GET')
        {
            $today = date('Y-m-d');
            // /echo $today;
            //$renewal_pickup_info = DB::select("SELECT * FROM order_details,customer_details,del_orders where customer_details.cust_id = order_details.customer_id AND order_details.order_id=del_orders.order_id ");
            $renewal_pickup_info = DB::select("SELECT 
                                                    order_details.id as order_details_id,
                                                    order_details.pickup_date as pickup_date,
                                                    order_details.vendor_id as vendor_id, 
                                                    order_details.vendor_product_id as vendor_product_id, 
                                                    order_details.sale_rental as sale_rental,
                                                    order_details.product_qty as product_qty,
                                                    order_details.product_rent as product_rent,
                                                    order_details.product_deposite as product_deposite,
                                                    order_details.transport as transport,
                                                    order_details.pickup_date as pickup_date,
                                                    order_details.customer_id as customer_id,
                                                    order_details.product_id as product_id,
                                                    customer_details.*,
                                                    products.product_name as product_name,
                                                    del_orders.order_id as order_id
                                                FROM order_details,customer_details,del_orders,products
                                                where customer_details.cust_id = order_details.customer_id 
                                                    AND order_details.order_id=del_orders.order_id
                                                    AND order_details.product_id=products.id
                                                    AND order_details.sale_rental='Rental'
                                                    AND(order_details.current_status='Pending'
                                                    OR order_details.current_status='Renewed' OR order_details.current_status='Renewed Online')
                                                    AND order_details.pickup_date<'$today' 
                                                    ORDER BY order_details.pickup_date ASC");
      
            $data['renewal_pickup_info'] = json_decode(json_encode($renewal_pickup_info),true);
            // print_r($data['renewal_pickup_info']);
            $cust_id_array = array();
            $customer_products_details = array();
            foreach($data['renewal_pickup_info'] as $renewal_pickup_info)
            {
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
                            //print_r($customer_products_details[$i]['product_details']);
                            //echo $count;
                            $prod_name = $renewal_pickup_info['product_name'];
                            //echo $prod_name;
                            $customer_products_details[$i]['product_details'][$count]['product_name'] = $prod_name;
                            $customer_products_details[$i]['product_details'][$count]['pickup_date'] = $renewal_pickup_info['pickup_date'];
                            $customer_products_details[$i]['product_details'][$count]['order_details_id'] = $renewal_pickup_info['order_details_id'];
                            $customer_products_details[$i]['product_details'][$count]['vendor_id'] = $renewal_pickup_info['vendor_id'];
                            $customer_products_details[$i]['product_details'][$count]['vendor_product_id'] = $renewal_pickup_info['vendor_product_id'];
                            $customer_products_details[$i]['product_details'][$count]['product_qty'] = $renewal_pickup_info['product_qty'];
                            $customer_products_details[$i]['product_details'][$count]['order_id'] = $renewal_pickup_info['order_id'];
                            $customer_products_details[$i]['product_details'][$count]['product_rent'] = $renewal_pickup_info['product_rent'];
                            $customer_products_details[$i]['product_details'][$count]['product_deposite'] = $renewal_pickup_info['product_deposite'];
                            $customer_products_details[$i]['product_details'][$count]['transport'] = $renewal_pickup_info['transport'];
                            $customer_products_details[$i]['product_details'][$count]['product_id'] = $renewal_pickup_info['product_id'];
                            $customer_products_details[$i]['product_details'][$count]['order_id'] = $renewal_pickup_info['order_id'];
                            // $customer_products_detail['product_details'][$count] = $renewal_pickup_info['product_name'];
                        }
                    }
                }
                else
                {
                    array_push($cust_id_array,$renewal_pickup_info['customer_id']);
                    $count = count($customer_products_details);
                    
                    $customer_address = $renewal_pickup_info['address_line_1'].', '.$renewal_pickup_info['address_line_2'].', '.$renewal_pickup_info['area'].', '.$renewal_pickup_info['landmark'].', '.$renewal_pickup_info['location'].', '.$renewal_pickup_info['city'].', '.$renewal_pickup_info['pincode'];
                    
                    $customer_products_details[$count]['customer_id'] = $renewal_pickup_info['customer_id'];
                    $customer_products_details[$count]['customer_name'] = $renewal_pickup_info['customer_name'];
                    $customer_products_details[$count]['customer_log'] = $renewal_pickup_info['comment'];
                    $customer_products_details[$count]['customer_address'] = $customer_address;
                    $customer_products_details[$count]['product_details'][0]['product_name'] = $renewal_pickup_info['product_name'];
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
                }
            }
            $data['customer_products_details'] = $customer_products_details;
            //print_r($customer_products_details);
            // $customer_id = array();
            // for ($i=0; $i <count($renewal_pickup_info) ; $i++) { 
            //     $cust_id = $data['renewal_pickup_info'][$i]['customer_id'];
            //     array_push($customer_id,$cust_id);
            // }

            echo "<script>localStorage['filtered']='overdue';</script>";
            return view('RenewalPickup/renewal_pickup',$data);
        }
        
    }

    //filter applied info show
    public function renewal_pickup_filter($filter,$cust_id)
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
         return redirect()->to($url);
        }
       //$date = $filter;
        if($filter=="today")
        {
            $date = date('Y-m-d');
            if($cust_id!="All")
            {
                $whereClause = "order_details.customer_id = $cust_id AND order_details.customer_id = customer_details.cust_id AND order_details.pickup_date ='$date' ";
            }
            else{
                echo "<script>localStorage['filtered'] = 'Nothing'</script>";
                $whereClause = "order_details.customer_id = customer_details.cust_id AND order_details.pickup_date ='$date' ";
            }
            
            //return $this->renewal_pickup();
        }
        if($filter=="tomorrow")
        {
            $date = date('Y-m-d',strtotime('+1 days'));
            //$query = "where order_details.pickup_date <'$date' AND customer_details.cust_id = order_details.customer_id AND order_details.order_id=del_orders.order_id";
            if($cust_id!="All")
            {
                $whereClause = "order_details.customer_id = $cust_id AND order_details.customer_id = customer_details.cust_id AND order_details.pickup_date ='$date' ";
            }
            else{

                $whereClause = "order_details.customer_id = customer_details.cust_id AND order_details.pickup_date ='$date' ";
            }
            
        }
        if($filter=="overdue")
        {
            $date = date('Y-m-d');
            //$query = "where order_details.pickup_date <'$date' AND customer_details.cust_id = order_details.customer_id AND order_details.order_id=del_orders.order_id";
            if($cust_id!="All")
            {
                $whereClause = "order_details.customer_id = $cust_id AND order_details.customer_id = customer_details.cust_id AND order_details.pickup_date <'$date' ";
                echo "<script>localStorage['filtered'] = 'Nothing'</script>";
            }
            else{

                $whereClause = "order_details.customer_id = customer_details.cust_id AND order_details.pickup_date <'$date' ";
            }
        }
        elseif($filter=="3_days")
        {
            $date = date('Y-m-d',strtotime("+1 days"));
            $end_date = date('Y-m-d',strtotime("+3 days"));
            //$query = "where order_details.pickup_date BETWEEN'$date' AND '$end_date' AND customer_details.cust_id = order_details.customer_id AND order_details.order_id=del_orders.order_id";
            if($cust_id!="All")
            {
                $whereClause = " order_details.customer_id = $cust_id AND order_details.customer_id = customer_details.cust_id AND order_details.pickup_date BETWEEN'$date' AND '$end_date' ";
                echo "<script>localStorage['filtered'] = 'Nothing'</script>";
            }
            else{

                $whereClause = "order_details.customer_id = customer_details.cust_id AND order_details.pickup_date BETWEEN'$date' AND '$end_date' ";
            }
        }
        elseif($filter == "all")
        {
            if($cust_id!="All"){
                $renewal_pickup_info = DB::select("SELECT 
                                                    order_details.id as order_details_id,
                                                    order_details.pickup_date as pickup_date,
                                                    order_details.vendor_id as vendor_id, 
                                                    order_details.vendor_product_id as vendor_product_id, 
                                                    order_details.sale_rental as sale_rental,
                                                    order_details.product_qty as product_qty,
                                                    order_details.product_rent as product_rent,
                                                    order_details.product_deposite as product_deposite,
                                                    order_details.transport as transport,
                                                    order_details.pickup_date as pickup_date,
                                                    order_details.customer_id as customer_id,
                                                    order_details.product_id as product_id,
                                                    customer_details.*,
                                                    products.product_name as product_name,
                                                    del_orders.order_id as order_id
                                                FROM order_details,customer_details,del_orders,products
                                                where order_details.customer_id = $cust_id AND order_details.customer_id = customer_details.cust_id
                                                    AND order_details.order_id=del_orders.order_id
                                                    AND order_details.product_id=products.id
                                                    AND order_details.sale_rental='Rental'
                                                    AND (order_details.current_status='Pending'
                                                    OR order_details.current_status='Renewed' OR order_details.current_status='Renewed Online')
                                                    ORDER BY order_details.pickup_date ASC");
                echo "<script>localStorage['filtered'] = 'Nothing'</script>";
            }
            else{
                $renewal_pickup_info = DB::select("SELECT 
                                                    order_details.id as order_details_id,
                                                    order_details.pickup_date as pickup_date,
                                                    order_details.vendor_id as vendor_id, 
                                                    order_details.vendor_product_id as vendor_product_id, 
                                                    order_details.sale_rental as sale_rental,
                                                    order_details.product_qty as product_qty,
                                                    order_details.product_rent as product_rent,
                                                    order_details.product_deposite as product_deposite,
                                                    order_details.transport as transport,
                                                    order_details.pickup_date as pickup_date,
                                                    order_details.customer_id as customer_id,
                                                    order_details.product_id as product_id,
                                                    customer_details.*,
                                                    products.product_name as product_name,
                                                    del_orders.order_id as order_id
                                                FROM order_details,customer_details,del_orders,products
                                                where customer_details.cust_id = order_details.customer_id 
                                                    AND order_details.order_id=del_orders.order_id
                                                    AND order_details.product_id=products.id
                                                    AND order_details.sale_rental='Rental'
                                                    AND (order_details.current_status='Pending'
                                                    OR order_details.current_status='Renewed' OR order_details.current_status='Renewed Online')
                                                    ORDER BY order_details.pickup_date ASC");
            }

            $data['renewal_pickup_info'] = json_decode(json_encode($renewal_pickup_info),true);
            // print_r($data['renewal_pickup_info']);
            $cust_id_array = array();
            $customer_products_details = array();
            foreach($data['renewal_pickup_info'] as $renewal_pickup_info)
            {
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
                            //print_r($customer_products_details[$i]['product_details']);
                            //echo $count;
                            $prod_name = $renewal_pickup_info['product_name'];
                            //echo $prod_name;
                            $customer_products_details[$i]['product_details'][$count]['product_name'] = $prod_name;
                            $customer_products_details[$i]['product_details'][$count]['pickup_date'] = $renewal_pickup_info['pickup_date'];
                            $customer_products_details[$i]['product_details'][$count]['order_details_id'] = $renewal_pickup_info['order_details_id'];
                            $customer_products_details[$i]['product_details'][$count]['vendor_id'] = $renewal_pickup_info['vendor_id'];
                            $customer_products_details[$i]['product_details'][$count]['vendor_product_id'] = $renewal_pickup_info['vendor_product_id'];
                            $customer_products_details[$i]['product_details'][$count]['product_qty'] = $renewal_pickup_info['product_qty'];
                            $customer_products_details[$i]['product_details'][$count]['order_id'] = $renewal_pickup_info['order_id'];
                            $customer_products_details[$i]['product_details'][$count]['product_rent'] = $renewal_pickup_info['product_rent'];
                            $customer_products_details[$i]['product_details'][$count]['product_deposite'] = $renewal_pickup_info['product_deposite'];
                            $customer_products_details[$i]['product_details'][$count]['transport'] = $renewal_pickup_info['transport'];
                            $customer_products_details[$i]['product_details'][$count]['product_id'] = $renewal_pickup_info['product_id'];
                            $customer_products_details[$i]['product_details'][$count]['order_id'] = $renewal_pickup_info['order_id'];
                            // $customer_products_detail['product_details'][$count] = $renewal_pickup_info['product_name'];
                        }
                    }
                }
                else
                {
                    array_push($cust_id_array,$renewal_pickup_info['customer_id']);
                    $count = count($customer_products_details);

                    // $customer_address = $renewal_pickup_info['address_line_1'].','.$renewal_pickup_info['address_line_2'].','.$renewal_pickup_info['area'].','.$renewal_pickup_info['landmark'].','.$renewal_pickup_info['location'].','.$renewal_pickup_info['city'].','.$renewal_pickup_info['pincode'];
                    $customer_address = $renewal_pickup_info['address_line_1'].', '.$renewal_pickup_info['address_line_2'].', '.$renewal_pickup_info['area'].', '.$renewal_pickup_info['landmark'].', '.$renewal_pickup_info['location'].', '.$renewal_pickup_info['city'].', '.$renewal_pickup_info['pincode'];
                    
                    $customer_products_details[$count]['customer_id'] = $renewal_pickup_info['customer_id'];
                    $customer_products_details[$count]['customer_name'] = $renewal_pickup_info['customer_name'];
                    $customer_products_details[$count]['customer_log'] = $renewal_pickup_info['comment'];
                    $customer_products_details[$count]['customer_address'] = $customer_address;
                    $customer_products_details[$count]['product_details'][0]['product_name'] = $renewal_pickup_info['product_name'];
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
                }
            }
            $data['customer_products_details'] = $customer_products_details;
            return view('RenewalPickup/renewal_pickup',$data);
        }
        
        //$today = date('Y-m-d');
        // /echo $today;
        $renewal_pickup_info = DB::select("SELECT 
                                                    order_details.id as order_details_id,
                                                    order_details.pickup_date as pickup_date,
                                                    order_details.vendor_id as vendor_id, 
                                                    order_details.vendor_product_id as vendor_product_id, 
                                                    order_details.sale_rental as sale_rental,
                                                    order_details.product_qty as product_qty,
                                                    order_details.product_rent as product_rent,
                                                    order_details.product_deposite as product_deposite,
                                                    order_details.transport as transport,
                                                    order_details.pickup_date as pickup_date,
                                                    order_details.customer_id as customer_id,
                                                    order_details.product_id as product_id,
                                                    customer_details.*,
                                                    products.product_name as product_name,
                                                    del_orders.order_id as order_id
                                                FROM order_details,customer_details,del_orders,products
                                                where order_details.order_id=del_orders.order_id
                                                    AND order_details.product_id=products.id
                                                    AND order_details.sale_rental='Rental'
                                                    AND (order_details.current_status='Pending'
                                                    OR order_details.current_status='Renewed Online' OR order_details.current_status='Renewed')
                                                    AND $whereClause ORDER BY order_details.pickup_date ASC");
            $data['renewal_pickup_info'] = json_decode(json_encode($renewal_pickup_info),true);
            // print_r($data['renewal_pickup_info']);
            $cust_id_array = array();
            $customer_products_details = array();
            foreach($data['renewal_pickup_info'] as $renewal_pickup_info)
            {
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
                            //print_r($customer_products_details[$i]['product_details']);
                            //echo $count;
                            $prod_name = $renewal_pickup_info['product_name'];
                            //echo $prod_name;
                            $customer_products_details[$i]['product_details'][$count]['product_name'] = $prod_name;
                            $customer_products_details[$i]['product_details'][$count]['pickup_date'] = $renewal_pickup_info['pickup_date'];
                            $customer_products_details[$i]['product_details'][$count]['order_details_id'] = $renewal_pickup_info['order_details_id'];
                            $customer_products_details[$i]['product_details'][$count]['vendor_id'] = $renewal_pickup_info['vendor_id'];
                            $customer_products_details[$i]['product_details'][$count]['vendor_product_id'] = $renewal_pickup_info['vendor_product_id'];
                            $customer_products_details[$i]['product_details'][$count]['product_qty'] = $renewal_pickup_info['product_qty'];
                            $customer_products_details[$i]['product_details'][$count]['order_id'] = $renewal_pickup_info['order_id'];
                            $customer_products_details[$i]['product_details'][$count]['product_rent'] = $renewal_pickup_info['product_rent'];
                            $customer_products_details[$i]['product_details'][$count]['product_deposite'] = $renewal_pickup_info['product_deposite'];
                            $customer_products_details[$i]['product_details'][$count]['transport'] = $renewal_pickup_info['transport'];
                            $customer_products_details[$i]['product_details'][$count]['product_id'] = $renewal_pickup_info['product_id'];
                            $customer_products_details[$i]['product_details'][$count]['order_id'] = $renewal_pickup_info['order_id'];
                            // $customer_products_detail['product_details'][$count] = $renewal_pickup_info['product_name'];
                        }
                    }
                }
                else
                {
                    array_push($cust_id_array,$renewal_pickup_info['customer_id']);
                    $count = count($customer_products_details);

                    //$customer_address = $renewal_pickup_info['address_line_1'].','.$renewal_pickup_info['address_line_2'].','.$renewal_pickup_info['area'].','.$renewal_pickup_info['landmark'].','.$renewal_pickup_info['location'].','.$renewal_pickup_info['city'].','.$renewal_pickup_info['pincode'];
                    $customer_address = $renewal_pickup_info['address_line_1'].', '.$renewal_pickup_info['address_line_2'].', '.$renewal_pickup_info['area'].', '.$renewal_pickup_info['landmark'].', '.$renewal_pickup_info['location'].', '.$renewal_pickup_info['city'].', '.$renewal_pickup_info['pincode'];
                    
                    $customer_products_details[$count]['customer_id'] = $renewal_pickup_info['customer_id'];
                    $customer_products_details[$count]['customer_name'] = $renewal_pickup_info['customer_name'];
                    $customer_products_details[$count]['customer_log'] = $renewal_pickup_info['comment'];
                    $customer_products_details[$count]['customer_address'] = $customer_address;
                    $customer_products_details[$count]['product_details'][0]['product_name'] = $renewal_pickup_info['product_name'];
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
                }
            }
            $data['customer_products_details'] = $customer_products_details;
            return view('RenewalPickup/renewal_pickup',$data);
    }

    public function renewal_pickup_search()
    {
        
        if($_SERVER['REQUEST_METHOD']=='GET')
        {
            return view('RenewalPickup/search_customer_order');
        }
        if($_SERVER['REQUEST_METHOD']=='POST')
        {
            $search_input = $_POST['search_input'];
            if(is_numeric($search_input))
            {
                $renewal_pickup_info = DB::select("SELECT * FROM order_details,customer_details,del_orders Where order_details.order_id LIKE '%$search_input' AND customer_details.cust_id = order_details.customer_id AND order_details.order_id=del_orders.order_id");
            }   
            elseif(is_string($search_input))
            {
                $renewal_pickup_info = DB::select("SELECT * FROM order_details,customer_details,del_orders Where customer_details.customer_name LIKE '%$search_input' AND customer_details.cust_id = order_details.customer_id AND order_details.order_id=del_orders.order_id");
            }
            // $renewal_pickup_info = DB::select("SELECT * FROM order_details,customer_details,del_orders where order_details.order_id = LIKE '%$filter' AND customer_details.cust_id = order_details.customer_id AND order_details.order_id=del_orders.order_id");
            
            $data['renewal_pickup_info'] = json_decode(json_encode($renewal_pickup_info), true);
            //print_r($data['renewal_pickup_info']);
            $customer_id = array();
            for ($i=0; $i <count($renewal_pickup_info) ; $i++) { 
                $cust_id = $data['renewal_pickup_info'][$i]['customer_id'];
                array_push($customer_id,$cust_id);
            }
            //$cust_id= array_values(array_unique($customer_id));
            $data['search_input'] = $search_input;
            $data['details'] = array();
            $data['product_count'] = array();
            for ($i=0; $i <count($renewal_pickup_info); $i++) 
            { 
                $temp = array();
                $products = array();
                $cust_id = $data['renewal_pickup_info'][$i]['customer_id'];
                //echo $cust_id;
                if(in_array($cust_id,array_column($data['product_count'],'cust_id')))
                {
                    $cust_count_key = array_search($cust_id, array_column($data['product_count'], 'cust_id'));
                    $data['product_count'][$cust_count_key]['count'] = $data['product_count'][$cust_count_key]['count'] + 1;
                    $data['product_count'][$cust_count_key]['customer_name'] = $data['product_count'][$cust_count_key]['customer_name'];
                    $data['product_count'][$cust_count_key]['total_amount'] = $data['product_count'][$cust_count_key]['total_amount'] + $data['renewal_pickup_info'][$i]['product_rent'];
                }
                else
                {
                    $temp['cust_id'] = $cust_id;
                    $temp['count'] = 1;
                    $temp['total_amount'] = $data['renewal_pickup_info'][$i]['product_rent'];
                    $temp['customer_name'] = $data['renewal_pickup_info'][$i]['customer_name'];
                    array_push($data['product_count'],$temp);
                }
            }
            //print_r($data['product_count']);
            return view('RenewalPickup/search_customer_order',$data);
        }
    
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
        print_r($_POST);
        $customer_id = $_POST['pop_cust_id'];
        $customer_name = $_POST['pop_cust_name'];
        $product_name = $_POST['product_name'];
        $pickup_date = $_POST['pickup_date'];
        $product_rent = $_POST['product_rent'];
        $customer_details = new customer_detail();

        $mail_data = array();
        $total_rent = 0;
        for ($i=0; $i <count($product_name) ; $i++) 
        { 
            $mail_data['product_name'][$i] = $product_name[$i];
            $mail_data['pickup_date'][$i] = $pickup_date[$i];
            $mail_data['product_rent'][$i] = $product_rent[$i];
            $total_rent += $product_rent[$i];
        }
        print_r($mail_data);

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
 
    public function renewal_pickup_product()
    {
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
            if($_POST['renewal_pickup_btn']=='Renew')
            {
               //print_r($_POST);
               $data['renew_info'] = array();
               $total_rent = 0;
               $total_deposit = 0;
               // print_r($_POST['check']);
               $get_key = array_keys($check);
               $out_key = $get_key[0];
               // print_r($order_id);
               for ($i=0; $i <count($check[$out_key]) ; $i++) { 
                   $index = $check[$out_key][$i];

                   //get lead id by order_id from del_orders table
                   $temp_order_id= $order_id[$out_key][$index];
                   $temp_order_details_id= $order_details_id[$out_key][$index];
                   $get_lead_id = DB::select("SELECT lead_id,vendor_id FROM del_orders WHERE order_id='$temp_order_id' ");
                   $data['get_lead_id'] = json_decode(json_encode($get_lead_id),true);
                   $lead_id = $data['get_lead_id'][0]['lead_id'];
                   $vendor_id = $data['get_lead_id'][0]['vendor_id'];

                   //product_id from order_details table 
                   $get_product_id = DB::select("SELECT product_id FROM order_details WHERE id='$temp_order_details_id' ");
                   $data['get_product_id'] = json_decode(json_encode($get_product_id),true);
                   $product_id = $data['get_product_id'][0]['product_id'];

                   $data['renew_info'][$i]['product_name'] = $product_name[$out_key][$index];
                   $data['renew_info'][$i]['pickup_date'] = $pickup_date[$out_key][$index];
                   $data['renew_info'][$i]['renewal_date'] = date('Y-m-d',strtotime($pickup_date[$out_key][$index].'+30 day'));
                   $data['renew_info'][$i]['product_rent'] = $product_rent[$out_key][$index];
                   $data['renew_info'][$i]['deposit'] = $product_deposite[$out_key][$index];
                   $data['renew_info'][$i]['order_id'] = $order_id[$out_key][$index];
                   $data['renew_info'][$i]['order_details_id'] = $order_details_id[$out_key][$i];
                   $data['renew_info'][$i]['lead_id'] = $lead_id;
                   $data['renew_info'][$i]['vendor_id'] = $vendor_id;
                   $data['renew_info'][$i]['product_id'] = $product_id;
                   
                   $total_rent += $product_rent[$out_key][$i];
                   $total_deposit += $product_deposite[$out_key][$i];
                }
               
               $customer_info =  DB::select("SELECT * FROM customer_details where cust_id = '$customer_id' ");
               $data['customer_info'] = json_decode(json_encode($customer_info),true);

               $data['total_rent'] = $total_rent;
               $data['total_deposit'] = $total_deposit;

               return view('RenewalPickup/renew_product',$data);
            }

            //--------pickup button ---------//
            if($_POST['renewal_pickup_btn']=='Pickup')
            {
                //print_r($_POST);
                $data['pickup_info'] = array();
                $total_rent = 0;
                $total_deposit = 0;
                // print_r($_POST['check']);
                $get_key = array_keys($check);
                $out_key = $get_key[0];
                // print_r($order_id);
                for ($i=0; $i <count($check[$out_key]) ; $i++) { 
                    $index = $check[$out_key][$i];
                    //get lead id by order_id from del_orders table
                    $temp_order_id= $order_id[$out_key][$index];
                    $get_lead_id = DB::select("SELECT lead_id,vendor_id FROM del_orders WHERE order_id='$temp_order_id' ");
                    $data['get_lead_id'] = json_decode(json_encode($get_lead_id),true);
                    $lead_id = $data['get_lead_id'][0]['lead_id'];
                    $vendor_id = $data['get_lead_id'][0]['vendor_id'];
                    //lead_id get close

                    $data['pickup_info'][$i]['product_name'] = $product_name[$out_key][$index];
                    $data['pickup_info'][$i]['pickup_date'] = $pickup_date[$out_key][$index];
                    //$data['pickup_info'][$i]['renewal_date'] = date('Y-m-d',strtotime($pickup_date[$out_key][$index].'+30 day'));
                    $data['pickup_info'][$i]['product_rent'] = $product_rent[$out_key][$index];
                    $data['pickup_info'][$i]['deposit'] = $product_deposite[$out_key][$index];
                    $data['pickup_info'][$i]['order_id'] = $order_id[$out_key][$index];
                    $data['pickup_info'][$i]['order_details_id'] = $order_details_id[$out_key][$i];
                    $data['pickup_info'][$i]['lead_id'] = $lead_id;
                    $data['pickup_info'][$i]['vendor_id'] = $vendor_id;
                    
                    $total_rent += $product_rent[$out_key][$i];
                    $total_deposit += $product_deposite[$out_key][$i];
                }
                
                $customer_info =  DB::select("SELECT * FROM customer_details where cust_id = '$customer_id' ");
                $data['customer_info'] = json_decode(json_encode($customer_info),true);

                $data['total_rent'] = $total_rent;
                $data['total_deposit'] = $total_deposit;

                // print_r($data['pickup_info']);
                return view('RenewalPickup/pickup_product',$data);
                
            }
        }
        else {
            return redirect()->back()->with('error','Something went Wrong');
        }

    }


    public function pickup_order()
    {
        $customer_id = $_POST['customer_id'];
        $customer_name = $_POST['customer_name'];
        $customer_address = $_POST['customer_address'];
        $customer_location = $_POST['customer_location'];
        $customer_mobile = $_POST['customer_mobile'];
        $order_id = $_POST['order_id'];
        $lead_id = $_POST['lead_id'];
        $vendor_id = $_POST['vendor_id'];
        $order_details_id = $_POST['order_details_id'];
        $product_name = $_POST['product_name'];
        $order_details_id = $_POST['order_details_id'];
        $product_rent = $_POST['product_rent'];
        $product_deposit = $_POST['product_deposit'];
        $pickup_date = $_POST['pickup_date'];
        $total_amt = $_POST['total_amount'];
        
       
        $temp_pickup_date = array();
        $pickup_data = array();

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
                        //'product_id', => $p_details['order_id'],
                        'pickup_date' => $p_details['pickup_date'],
                        'created_at' =>date('Y-m-d h:i:s')
                    ];
                    $pickup_id = $Pickup->insertGetId($insert_pickups);
                    array_push($inserted_pickups_table_id, $pickup_id);
                    //order_details status change
                    $order_details_id = $p_details['order_details_id'];
                    $pickup_date = $p_details['pickup_date'];
                   DB::update("UPDATE order_details SET current_status = 'Pending Pickup', pickup_date='$pickup_date' WHERE id ='$order_details_id' ");
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
        }
        $collection_url = url('/')."/assign_pickup_delboy/$get_delorder_id";
       //print_r($_POST);
        return redirect('renewal_pickup')->with('message', 'Pickup Order Generated Successfully')
                                        ->with('collection_url',$collection_url);
    }

    public function renew_order()
    {
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
        //$product_deposit = $_POST['product_deposit'];
        //$pickup_date = $_POST['pickup_date'];
        $total_amt = $_POST['total_amount'];
        $renew_date = $_POST['renew_date'];
        $next_renew_date = $_POST['next_renew_date'];
        $payment_mode = $_POST['payment_mode'];
        //$cash_amount = $_POST['cash_amount'];
                    if($payment_mode=='Cash'){
                        $cash_amount = $total_amt;
                        $online_amount = 0;
                        // $collection_url = url('/')."/renew_request";
                        // $collection_url = url('/')."/assign_collection_delboy/";
                    }
                    elseif($payment_mode=='Online'){
                        $online_amount = $total_amt;
                        $cash_amount = 0;
                        // $collection_url = url('/')."/pending_online_renew";
                    }
                    elseif($payment_mode=='Both'){
                        $online_amount = $_POST['online_amount'];
                        $cash_amount = $_POST['cash_amount'];
                    }
        //$online_amount = $_POST['online_amount'];

        //print_r($_POST);
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
            // $collection_url = url('/')."/renew_request";
            $collection_url = url('/')."/assign_collection_delboy/$get_collection_order_id";            
        }
        elseif($payment_mode=='Online'){
            $online_amount = $total_amt;
            $cash_amount = 0;
            $collection_url = url('/')."/payment_recieved/$get_collection_order_id";
        }
        elseif($payment_mode=='Both'){
            $online_amount = $_POST['online_amount'];
            $cash_amount = $_POST['cash_amount'];
        }
        //insert data in renewal table for collection order 

        for ($i=0; $i <count($order_id); $i++) { 
            if($payment_mode=='Cash') {
                $cash_collection_amt = $product_rent[$i];
                $online_collection_amt = 0;
            }
            if($payment_mode=='Online') {
                $online_collection_amt = $product_rent[$i];
                $cash_collection_amt = 0;
            }
            $insert_collection_order_data= [
                'collection_order_id'=>$get_collection_order_id,
                'order_id'=>$order_id[$i],
                'order_details_id'=>$order_details_id[$i],
                'lead_id'=>$lead_id[$i],
                'vendor_id'=>$vendor_id[$i],
                'product_id'=>$product_id[$i],
                'start_date'=>$renew_date[$i],
                'end_date'=>$next_renew_date[$i],
                'payment_mode'=>$payment_mode,
                'cash_amount'=>$cash_collection_amt,
                'online_amount'=>$online_collection_amt,
                //'online_method',
                'status'=>'Pending',
                'payment_status'=>'Pending',
                'created_at'=>date('Y-m-d H:i:s')
            ];
            $Renew->insert($insert_collection_order_data);

            //update pickup date in order_details table
            $temp_pickup_date = date('Y-m-d',strtotime($next_renew_date[$i]));
            $update_order_details_data = [
                'pickup_date'=>$temp_pickup_date,
                'collection_date'=>$collection_date,
                'current_status' =>'Pending Renew'
            ];
            $Order_Details->where('id',$order_details_id[$i])->update($update_order_details_data);
        }
        
        return redirect('renewal_pickup')->with('message', 'Collection Order Generated Successfully')
                                        ->with('collection_url',$collection_url);
    }
    
}
?>