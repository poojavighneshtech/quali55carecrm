<?php

namespace App\Http\Controllers\CustomerView;

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
use DateTime;
use App\Http\Controllers\RenewalPickup\RenewalPickupController;

class CustomerViewController extends Controller
{
    public function isLoggedIn()
    {
        $data = session('isLoggedIn');
        return $data;
    }
    //populate customer list 
    public function GetCustomerListToPopulate($get_keyword)
    {
        if($get_keyword!=null)
        {
            $get_customers_list = DB::select("SELECT customer_name FROM customer_details WHERE customer_name LIKE '%$get_keyword%' ");
            $get_customers_list = json_decode(json_encode($get_customers_list),true);
            return json_encode($get_customers_list);
        }
        else{
            return false;
        }
        
    }

    public function CustomerSingleViewGet()
    {
        $orderTypeNotIn = config('app.order_type');
        $get_customers_by_orders = DB::table('customer_details')
                                            ->join('order_details','customer_details.cust_id','=','order_details.customer_id')
                                            ->join('del_orders','order_details.order_id','=','del_orders.order_id')
                                            ->when(session('city_based_access') == '1',function($query){
                                                $query->where('customer_details.citygroup',session('user_city'));
                                            })
                                            ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                                            ->orderBy('del_orders.order_id','DESC')
                                            ->distinct('cust_id')
                                            ->paginate(10);

        //dd($get_customers_by_orders);
        return view('CustomerView.customer_single_view',compact('get_customers_by_orders'));
    }

    //for remove special characters
    function clean($string) {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
        return preg_replace('/-+/', ' ', $string); // Replaces multiple hyphens with single one.
    }
    public function GetCustomers(Request $request)
    {
        $customer_value = $_POST['customer_value'];
        $data['customer_value'] = $customer_value;
        $customer_value = $this->clean($customer_value);
        // $get_customers = DB::select("SELECT
        //                                     * 
        //                                 FROM
        //                                     customer_details,leads,del_orders
        //                                 WHERE 
        //                                     (customer_details.customer_name LIKE '%$customer_value%'
        //                                     OR customer_details.primary_contact_no LIKE '%$customer_value%'
        //                                     OR leads.patient_name LIKE '%$customer_value%' )
        //                                     AND leads.customer_id = customer_details.cust_id
        //                                     AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)
        //                                     AND del_orders.lead_id = leads.id");
        $get_customers = DB::table('customer_details')
                            ->join('leads','leads.customer_id','=','customer_details.cust_id')
                            ->join('del_orders','del_orders.lead_id','=','leads.id')                            
                            //->select('customer_details.*','leads.*')
                            //->distinct('customer_details.cust_id')
                            // ->where('del_orders.deliverypickup','Delivery')
                            //->where('leads.lead_status','Order Generated')
                            // ->where([['customer_details.customer_name','LIKE','%'.$customer_value.'%']])
                            // ->orWhere([['customer_details.primary_contact_no','LIKE','%'.$customer_value.'%']])
                            // ->orWhere([['leads.patient_name','LIKE','%'.$customer_value.'%']])
                            // ->orWhere('del_orders.order_id',$customer_value)
                            ->where('del_orders.deliverypickup','Delivery')
                            ->where(function($q)use($customer_value) {
                                $q->where('customer_details.customer_name','LIKE','%'.$customer_value.'%');
                                $q->orWhere('customer_details.primary_contact_no','LIKE','%'.$customer_value.'%');
                                $q->orWhere('leads.patient_name','LIKE','%'.$customer_value.'%');
                                $q->orWhere('del_orders.order_id',$customer_value);
                            })
                            ->when(session('city_based_access') == '1',function($query){
                                $query->where('customer_details.citygroup',session('user_city'));
                            })
                            ->when(session('role') == 'user',function($query)use($request){
                                $query->where('leads.lead_owner',session('user_id'));
                            })
                            ->get()->groupBy('cust_id');
        // $data['get_customers']  = json_decode(json_encode($get_customers),true);
        // return view('CustomerView.customer_single_view',$data);
        // dd($get_customers);
        return view('CustomerView.customer_single_view',compact('get_customers','customer_value'));
    }
    public function GetCustomerLeads($customer_id)
    {
        $orderTypeNotIn = config('app.order_type');        
        $orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";
        //cucstomer detail
        $get_customer = DB::select("SELECT
                                            * 
                                        FROM
                                            customer_details
                                        WHERE 
                                            cust_id = $customer_id");
        $data['get_customer']  = json_decode(json_encode($get_customer),true);
        //lead details of customer
        $get_leads = DB::select("SELECT 
                                        leads.*,
                                        user.username as username
                                    FROM
                                        leads,user
                                    WHERE
                                        leads.customer_id=$customer_id
                                        AND leads.lead_status IN('Order Generated','Vendor Assigned','Delivery In Progress' )
                                        AND leads.lead_owner = user.id"); 
        $data['lead_details']= json_decode(json_encode($get_leads),true);
        foreach($data['lead_details'] as $key=>$lead)
        {
            //search in del_orders
            $lead_id = $lead['id'];
            $product_data = DB::select("SELECT 
                                            del_orders.*,
                                            order_details.*,
                                            order_details.id as order_details_id,
                                            concat(order_details.product_rent + order_details.transport + order_details.product_deposite) as total_amt,
                                            products.product_name as product_name,
                                            vendor_details.registered_name as vendor_name
                                        FROM 
                                            del_orders,order_details,products,vendor_details
                                        WHERE
                                            del_orders.lead_id = $lead_id
                                            AND del_orders.order_id = order_details.order_id
                                            AND order_details.product_id = products.id
                                            AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)
                                            AND order_details.vendor_id = vendor_details.id");
            $data['get_product_data'] = json_decode(json_encode($product_data),true);
            
            foreach($data['get_product_data'] as $key1=> $product)
            {
                $data['get_product_data'][$key1]['product_rent'] = RenewalPickupController::fetchCrDrData($product['id'],'R');
                $data['get_product_data'][$key1]['product_deposite'] = RenewalPickupController::fetchCrDrData($product['id'],'D');
                $data['get_product_data'][$key1]['transport'] = RenewalPickupController::fetchCrDrData($product['id'],'T');
                $data['get_product_data'][$key1]['total_amt'] = $data['get_product_data'][$key1]['product_rent'] + $data['get_product_data'][$key1]['product_deposite'] + $data['get_product_data'][$key1]['transport'];
                $d1 = new DateTime($product['creation_date']);
                $d2 = new DateTime($product['pickup_date']);
                $interval = $d1->diff($d2);
                $diffInMonths  = $interval->m; //4
                $data['get_product_data'][$key1]['renewal_counts'] =$diffInMonths;
                $data['get_product_data'][$key1]['total_rent'] =$diffInMonths*$product['product_rent'];
            }
          
            $data['lead_details'][$key]['product_details'] = $data['get_product_data'];
            
        }

        //get customer complaints
        $get_complaints = DB::select("SELECT 
                                            complaints.*,
                                            vendor_details.registered_name as vendor_name,
                                            products.product_name as product_name
                                        FROM 
                                            complaints,vendor_details,products
                                        WHERE 
                                            complaints.customer_id = $customer_id
                                            AND products.id = complaints.product_id
                                            AND vendor_details.id = complaints.vendor_id");
        $data['get_complaints'] = json_decode(json_encode($get_complaints),true);
        return view('CustomerView.customer_single_view',$data);
    }
    
    //get product wise data
    public function GetProductsData($order_details_id,$name) 
    {
        $orderTypeNotIn = config('app.order_type');        
        $orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";
        // if($name=='delivery_modal')
        // {
             $get_del_orders = DB::select("SELECT 
                                                del_orders.*,
                                                order_details.*,
                                                order_details.id as order_details_id,
                                                products.product_name,
                                                vendor_details.registered_name as vendor_name,
                                                vendor_warehouse.wh_name as warehouse_name

                                            FROM 
                                                del_orders,order_details,products,vendor_details,vendor_warehouse
                                            WHERE 
                                                order_details.id = $order_details_id
                                                AND order_details.order_id = del_orders.order_id
                                                AND del_orders.deliverypickup = 'Delivery'
                                                AND products.id =order_details.product_id
                                                AND vendor_details.id = order_details.vendor_id
                                                AND order_details.vendor_warehouse_id = vendor_warehouse.id");
            $data['get_del_order_data'] = json_decode(json_encode($get_del_orders),true);
            //return json_encode($get_del_order_data);
        // }
        // elseif($name=='renewal_modal')
        // {
            $get_renewal_orders = DB::select("SELECT 
                                                renewals.*,
                                                del_orders.*,
                                                renewals.order_id as delivery_order_id,
                                                products.product_name as product_name
                                            FROM 
                                                renewals,del_orders,products
                                            WHERE 
                                                renewals.order_details_id = $order_details_id
                                                AND products.id = renewals.product_id
                                                AND renewals.collection_order_id = del_orders.order_id");
            $data['get_renewal_orders_data'] = json_decode(json_encode($get_renewal_orders),true);
            //return json_encode($'get_renewal_orders_data']);
        // }
        // elseif($name=='pickup_modal')
        // {
            $get_pickup_order = DB::select("SELECT 
                                                del_orders.*,
                                                pickups.del_order_id as delivery_order_id,
                                                pickups.pickup_order_id as pickup_order_id,
                                                vendor_details.registered_name as vendor_name,
                                                vendor_warehouse.wh_name as warehouse_name,
                                                products.product_name as product_name
                                            FROM
                                                pickups,del_orders,vendor_details,vendor_warehouse,products
                                            WHERE
                                                pickups.order_details_id = $order_details_id
                                                AND del_orders.order_id = pickups.pickup_order_id
                                                AND pickups.drop_vendor_id = vendor_details.id
                                                AND pickups.product_id = products.id
                                                AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)
                                                AND pickups.drop_warehouse_id = vendor_warehouse.id");
            $data['get_pickup_order_data'] = json_decode(json_encode($get_pickup_order),true);
            
            //return json_encode($get_pickup_order_data);
        // }
        return json_encode($data);
        
    }

    public function GetAllLeadData($customer_id)
    {
        $orderTypeNotIn = config('app.order_type');        
        $orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";

        $get_all_leads = DB::select("SELECT 
                                        del_orders.*
                                    FROM 
                                        del_orders,leads
                                    WHERE 
                                        leads.customer_id = $customer_id 
                                        AND leads.lead_status= 'Order Generated' 
                                        AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)
                                        AND del_orders.lead_id = leads.id");
        $data['get_all_leads'] = json_decode(json_encode($get_all_leads),true);
        
        foreach($data['get_all_leads'] as $key=>$leads)
        {
            $del_order_id = $leads['order_id'];
            //get delivery data
            // $get_delivery_data = DB::select("SELECT 
            //                                         del_orders.*,
            //                                         order_details.*
            //                                     FROM 
            //                                         del_orders,order_details 
            //                                     WHERE 
            //                                         del_orders.order_id = $del_order_id 
            //                                         AND del_orders.deliverypickup = 'Delivery' 
            //                                         AND del_orders.order_id = order_details.order_id");
            // $get_delivery_data = json_decode(json_encode($get_delivery_data),true);
            // $data['all_delivery_data'] = $get_delivery_data;

            //get pickup data
            $get_pickup_data = DB::select("SELECT 
                                                del_orders.*,
                                                pickups.*
                                            FROM 
                                                del_orders,pickups
                                            WHERE 
                                                del_orders.order_id = $del_order_id 
                                                AND del_orders.deliverypickup = 'Pick Up' 
                                                AND pickups.pickup_order_id = del_orders.order_id");
            $get_pickup_data = json_decode(json_encode($get_pickup_data),true);
            $data['all_pickup_data'] = $get_pickup_data;

            // //get renewal data 
            // $get_collection_data = DB::select("SELECT 
            //                                     del_orders.*,
            //                                     renewals.*
            //                                 FROM 
            //                                     del_orders,renewals
            //                                 WHERE 
            //                                     del_ordes.order_id = $del_order_id 
            //                                     AND del_orders.deliverypickup = 'Collection' 
            //                                     AND renewals.collection_order_id = del_orders.order_id");
            // $get_collection_data = json_decode(json_encode($get_collection_data),true);
            // $data['all_renewals_data'] = $get_collection_data;
        }
        // print_r($data);
        
    }

    public function CustomerSingleView(Request $request)
    {
        $orderTypeNotIn = config('app.order_type');        
        $orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";

        if($_SERVER['REQUEST_METHOD']=='GET')
        {
            return view('CustomerView.customer_single_view');
        }
        if($_SERVER['REQUEST_METHOD']=='POST')
        {
            $customer_id = $_POST['customer_id'];
            //get lead details
            $gett_lead_details = DB::select("SELECT 
                                                    * 
                                                FROM
                                                    customer_details,leads
                                                WHERE
                                                    customer_details.cust_id = $customer_id
                                                    AND leads.customer_id = customer_details.cust_id");
            $data['lead_details'] = json_decode(json_encode($gett_lead_details),true);
            //print_r($data['lead_details']);

            //get transactional report
            $delivery_report = array();
            foreach($data['lead_details'] as $key => $lead)
            {
                $lead_id = $lead['id'];
                if($lead['lead_status']=='Order Generated')
                {
                    $get_del_orders = DB::select("SELECT 
                                                        del_orders.*,
                                                        order_details.*,
                                                        order_details.order_id as order_id,
                                                        order_details.id as order_details_id
                                                    FROM 
                                                        del_orders,order_details
                                                    WHERE
                                                        del_orders.lead_id = '$lead_id'
                                                        AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)
                                                        AND del_orders.order_id = order_details.order_id");
                    $get_del_orders_data = json_decode(json_encode($get_del_orders),true);
                    //print_r($get_del_orders);
                    $de_rep_count = count($delivery_report);
                    $delivery_report[$de_rep_count] = $get_del_orders_data;

                    //get pickuped data
                    for ($i=0; $i < count($get_del_orders); $i++) { 
                        $order_details_id =  $get_del_orders[$i]['order_details_id'];
                        if($get_del_orders[$i]['current_status']=='Picked Up' || $get_del_orders[$i]['current_status']=='Picked UP')
                        {
                            //check is renewal present or not
                            $get_renewals = DB::select("SELECT 
                                                            * 
                                                        FROM 
                                                            renewals 
                                                        WHERE
                                                            order_details_id = $order_details_id");
                            $get_renewals_data = json_decode(json_encode($get_renewals),true);
                            if($get_renewals_data[0]!=null)
                            {
                                
                            }
                        }
                    }
                }
                
            }
            

        }
    }

    public function transaction_history(Request $request){
        $customer_details = DB::table('customer_details')->where('cust_id',$request->get('cust_id'))->first();
        $orders = DB::table('del_orders')->join('leads','leads.id','=','del_orders.lead_id')->join('order_details','order_details.order_id','=','del_orders.order_id')->distinct('del_orders.order_id')->select('del_orders.order_id','del_orders.DelDate','del_orders.TotalAmt','leads.patient_name','del_orders.deliverypickup')->where('order_details.customer_id',$request->get('cust_id'))->get();
        if($request->has('order_id') && $request->get('order_id')!=null){
            $orders = DB::table('del_orders')->join('leads','leads.id','=','del_orders.lead_id')
                    ->join('order_details','order_details.order_id','=','del_orders.order_id')
                    ->distinct('del_orders.order_id')
                    ->select('del_orders.order_id','del_orders.DelDate','del_orders.TotalAmt','leads.patient_name','leads.customer_id','del_orders.deliverypickup')
                    //->where('order_details.customer_id',$request->get('cust_id'))
                    ->where('del_orders.order_id',$request->get('order_id'))
                    ->get();
            $customer_details = DB::table('customer_details')->where('cust_id',$orders[0]->customer_id)->first();
        }
        
        foreach($orders as $key=>$value)
        {
            if($value->deliverypickup=='Delivery'){
                $checkcrdr = DB::table('cr_dr_note')->where('order_id',$value->order_id)->get();
                $orders[$key]->crnote = $checkcrdr->where('crdrtype','Cr')->values();
                $orders[$key]->drnote = $checkcrdr->where('crdrtype','Dr')->values();
                //dd($checkcrdr->where('crdrtype','Dr')->values(),$checkcrdr->where('crdrtype','Cr')->values());
            }
            $renewals = DB::table('renewals')->join('del_orders','renewals.collection_order_id','=','del_orders.order_id')
                            ->distinct('del_orders.order_id')
                            ->select('del_orders.order_id','del_orders.DelDate','del_orders.TotalAmt','renewals.start_date','renewals.end_date')
                            ->where('renewals.order_id',$value->order_id)->whereNotIn('del_orders.status',['Cancel'])->get();
            $renewals = $renewals->groupBy('order_id');
            $pickups = DB::table('pickups')->join('del_orders','pickups.pickup_order_id','=','del_orders.order_id')->distinct('del_orders.order_id')->select('del_orders.order_id','del_orders.DelDate','del_orders.TotalAmt')->where('pickups.del_order_id',$value->order_id)->whereNotIn('del_orders.status',['Cancel'])->get();
            $orders[$key]->renewals = $renewals;
            $orders[$key]->pickups = $pickups;
        }
        // dd($orders);
        return view('CustomerView.transaction_history',compact('customer_details','orders'));

    }

}
