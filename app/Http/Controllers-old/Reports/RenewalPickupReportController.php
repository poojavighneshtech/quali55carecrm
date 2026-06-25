<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Lead\customer_detail;
use App\Models\Lead\lead;
use App\Models\Lead\leads_log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;


class RenewalPickupReportController extends Controller
{
    public function isLoggedIn()
    {
        $data = session('isLoggedIn');
        //print_r($data);      
        return $data;
    }
        public function renewalPickupReport1()
        {
            $today = date('Y-m-d');
            $orderTypeNotIn = config('app.order_type');
            $orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";
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
                            order_details.current_status as current_status,
                            customer_details.*,
                            products.product_name as product_name,
                            del_orders.order_id as order_id,
                            user.username as username,
                            vendor_details.registered_name as vendor_name,
                            leads.lead_owner as lead_owner
                    FROM 
                            order_details,customer_details,del_orders,products,leads,user,vendor_details
                    where customer_details.cust_id = order_details.customer_id
                            AND order_details.order_id=del_orders.order_id
                            AND order_details.product_id=products.id
                            AND del_orders.lead_id = leads.id
                            AND leads.lead_owner = user.id
                            AND order_details.vendor_id = vendor_details.id
                            AND order_details.sale_rental='Rental'
                            AND(order_details.current_status='Pending'
                            OR order_details.current_status='Pending Renew'
                            OR order_details.current_status='Renewed' 
                            OR order_details.current_status='Renewed Online')
                            AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)
                            ORDER BY order_details.pickup_date ASC");
            $data['renewal_pickup_info'] = json_decode(json_encode($renewal_pickup_info),true);
            $data['renewal_pickup_report'] = array();
            $temp_lead_owner = array();
            foreach($data['renewal_pickup_info'] as $renewal_pickup_info)
            {
                if(in_array($renewal_pickup_info['lead_owner'],$temp_lead_owner))
                {
                    $index = array_search($renewal_pickup_info['lead_owner'],$temp_lead_owner);
                    
                }
                else
                {
                    array_push($temp_lead_owner,$renewal_pickup_info['lead_owner']);
                }
            }
            //print_r($temp_lead_owner);
            //return view('Reports/renewal_pickup_report',$data);
    }
    public function renewalPickupReport($filter)
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
            return redirect()->to($url);
        }
        if($_SERVER['REQUEST_METHOD']=='GET')
        {
            //    $today = date('Y-m-d');
                if($filter=="today")
                {
                    $date = date('Y-m-d');
                    echo "<script>localStorage['filtered']='today';</script>";
                        $whereClause = "order_details.customer_id = customer_details.cust_id AND order_details.pickup_date ='$date' ";
                    //return $this->renewal_pickup();
                }
                elseif($filter=="tomorrow")
                {
                    $date = date('Y-m-d',strtotime('+1 days'));
                    echo "<script>localStorage['filtered']='tomorrow';</script>";
                    //$query = "where order_details.pickup_date <'$date' AND customer_details.cust_id = order_details.customer_id AND order_details.order_id=del_orders.order_id";
                        $whereClause = "order_details.customer_id = customer_details.cust_id AND order_details.pickup_date ='$date' ";
                }
                elseif($filter=="overdue")
                {
                    $date = date('Y-m-d');
                    //$query = "where order_details.pickup_date <'$date' AND customer_details.cust_id = order_details.customer_id AND order_details.order_id=del_orders.order_id";
                    echo "<script>localStorage['filtered']='overdue';</script>";
                        $whereClause = "order_details.customer_id = customer_details.cust_id AND order_details.pickup_date <'$date' ";
                }
                elseif($filter=="3_days")
                {
                    $date = date('Y-m-d',strtotime("+1 days"));
                    $end_date = date('Y-m-d',strtotime("+3 days"));
                    //$query = "where order_details.pickup_date BETWEEN'$date' AND '$end_date' AND customer_details.cust_id = order_details.customer_id AND order_details.order_id=del_orders.order_id";
                    echo "<script>localStorage['filtered']='3_days';</script>";
                        $whereClause = "order_details.customer_id = customer_details.cust_id AND order_details.pickup_date BETWEEN'$date' AND '$end_date' ";
                }
                elseif($filter == "all")
                {
                    echo "<script>localStorage['filtered']='all';</script>";
                    $whereClause = "order_details.customer_id = customer_details.cust_id";
                }
            //echo $today;
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
                            order_details.current_status as current_status,
                            customer_details.*,
                            products.product_name as product_name,
                            del_orders.order_id as order_id,
                            user.username as username,
                            user.id as userid,
                            vendor_details.registered_name as vendor_name
                        FROM 
                            order_details,customer_details,del_orders,products,leads,user,vendor_details
                        where customer_details.cust_id = order_details.customer_id
                            AND order_details.order_id=del_orders.order_id
                            AND order_details.product_id=products.id
                            AND del_orders.lead_id = leads.id
                            AND leads.lead_owner = user.id
                            AND order_details.vendor_id = vendor_details.id
                            AND order_details.sale_rental='Rental'
                            AND(order_details.current_status='Pending'
                                OR order_details.current_status='Pending Renew'
                                OR order_details.current_status='Renewed' 
                                OR order_details.current_status='Renewed Online')                            
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
                    $prod_name = $renewal_pickup_info['product_name'];
                    
                    $customer_address = $renewal_pickup_info['address_line_1'].', '.$renewal_pickup_info['address_line_2'].', '.$renewal_pickup_info['area'].', '.$renewal_pickup_info['landmark'].', '.$renewal_pickup_info['location'].', '.$renewal_pickup_info['city'].', '.$renewal_pickup_info['pincode'];

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
                    $total_month_rent = $month_count*$temp_product_rent;
                    
                    $customer_products_details[$count]['customer_id'] = $renewal_pickup_info['customer_id'];
                    $customer_products_details[$count]['customer_name'] = $renewal_pickup_info['customer_name'];
                    $customer_products_details[$count]['username'] = $renewal_pickup_info['username'];
                    $customer_products_details[$count]['lead_owner'] = $renewal_pickup_info['userid'];
                    $customer_products_details[$count]['customer_contact_no'] = $renewal_pickup_info['primary_contact_no'];
                    $customer_products_details[$count]['customer_log'] = $renewal_pickup_info['comment'];
                    $customer_products_details[$count]['customer_address'] = $customer_address;
                    $customer_products_details[$count]['product_details'][0]['vendor_name'] = $renewal_pickup_info['vendor_name'];
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
            $data['total_customer']=count($cust_id_array);
            $data['total_equipment']=$total_equipment;
            $data['total_due_amount']=$total_due_amount;
            //    print_r($data['total_due_amount']);
                // print_r($customer_products_details);
                $data['renewal_pickup_report'] = array();
                $temp_lead_owner = array();
                foreach($customer_products_details as $customer_product)
                {
                    if(in_array($customer_product['username'],$temp_lead_owner))
                    {
                        $index = array_search($customer_product['username'],$temp_lead_owner);
                        $data['renewal_pickup_report'][$index]['customer_count'] = $data['renewal_pickup_report'][$index]['customer_count'] + 1;
                        foreach($customer_product['product_details'] as $product_details)
                        {
                            $data['renewal_pickup_report'][$index]['product_count'] = $data['renewal_pickup_report'][$index]['product_count'] + $product_details['product_qty'];
                            $data['renewal_pickup_report'][$index]['total_due_amount'] = $data['renewal_pickup_report'][$index]['total_due_amount'] + $product_details['total_month_rent'];
                        }
                    }
                    else
                    {
                        array_push($temp_lead_owner,$customer_product['username']);
                        $count = count($data['renewal_pickup_report']);
                        $data['renewal_pickup_report'][$count]['customer_count'] = 1;
                        $data['renewal_pickup_report'][$count]['product_count'] = 0;
                        $data['renewal_pickup_report'][$count]['total_due_amount'] = 0;
                        foreach($customer_product['product_details'] as $product_details)
                        {
                            $data['renewal_pickup_report'][$count]['product_count'] = $data['renewal_pickup_report'][$count]['product_count'] + $product_details['product_qty'];
                            $data['renewal_pickup_report'][$count]['total_due_amount'] = $data['renewal_pickup_report'][$count]['total_due_amount'] + $product_details['total_month_rent'];
                        }
                    }
                }
                for($i = 0; $i < count($data['renewal_pickup_report']); $i++)
                {
                    $data['renewal_pickup_report'][$i]['username'] = $temp_lead_owner[$i];
                }
                // print_r($temp_lead_owner);
                // print_r($data['renewal_pickup_report']);
                echo "<script>localStorage['filtered']='all';</script>";
                return view('Reports/renewal_pickup_report',$data);
        }
        
    }

    public function pickupProducts(Request $request){
        $picked_up_products = DB::table('del_orders')
            ->join('pickups','pickups.pickup_order_id','=','del_orders.order_id')
            ->join('order_details','order_details.id','=','pickups.order_details_id')
            ->join('products','pickups.product_id','=','products.id')
            ->join('leads','leads.id','=','del_orders.lead_id')
            ->select('del_orders.*','order_details.product_rent','products.product_name','order_details.product_deposite','order_details.id as order_details_id','order_details.creation_date')
            ->whereNull('pickups.status')
            ->when($request->get('filter_customer_name'),function($query)use($request){
                $query->where('del_orders.shipping_first_name','LIKE','%'.$request->get('filter_customer_name').'%');
            })
            ->when($request->get('filter_contact_no'),function($query)use($request){
                $query->where('del_orders.mobileno',$request->get('filter_contact_no'));
            })
            ->when($request->get('filter_order_id'),function($query)use($request){
                $query->where('del_orders.order_id',$request->get('filter_order_id'));
            })
            ->when(!$request->get('filter_start_date') && !$request->get('filter_stop_date') && !$request->get('filter_customer_name') && !$request->get('filter_contact_no') && !$request->get('filter_order_id') && !$request->get('filter_master_products'),function($query)use($request){
                $fromDate = date('d-m-Y');
                $toDate = date('d-m-Y');
                $query->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$fromDate','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$toDate','%d-%m-%Y'))")]);
            })
            ->when($request->get('filter_start_date') && $request->get('filter_stop_date'),function($query)use($request){
                $fromDate = date('d-m-Y',strtotime($request->get('filter_start_date')));
                $toDate = date('d-m-Y',strtotime($request->get('filter_stop_date')));
                $query->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$fromDate','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$toDate','%d-%m-%Y'))")]);
            })
            ->when($request->get('filter_master_products'),function($query)use($request){
                $query->whereIn('pickups.product_id',$request->get('filter_master_products'));
            })
            ->when(session('role') == 'user',function($query){
                $query->where('leads.lead_owner',session('user_id'));
            })
            ->orderBy(DB::raw('STR_TO_DATE(del_orders.DelDate,"%d-%m-%Y")'),'DESC')
            ->get();
        $order_details_ids = $picked_up_products->pluck('order_details_id');
        $total_rent = $picked_up_products->pluck('product_rent')->sum() - 
        DB::table('cr_dr_note')->whereIn('order_details_id',$order_details_ids)->where('intype','R')->where('crdrtype','Cr')->get()->pluck('amount')->sum() + 
        DB::table('cr_dr_note')->whereIn('order_details_id',$order_details_ids)->where('intype','R')->where('crdrtype','Dr')->get()->pluck('amount')->sum();

        $total_deposit = $picked_up_products->pluck('product_deposite')->sum() - 
        DB::table('cr_dr_note')->whereIn('order_details_id',$order_details_ids)->where('intype','D')->where('crdrtype','Cr')->get()->pluck('amount')->sum() + 
        DB::table('cr_dr_note')->whereIn('order_details_id',$order_details_ids)->where('intype','D')->where('crdrtype','Dr')->get()->pluck('amount')->sum();
        $kept_product_days = array();
        foreach($picked_up_products as $product){
            array_push($kept_product_days,Carbon::parse($product->creation_date)->diffInDays(date('Y-m-d',strtotime($product->DelDate))));  
        }
        // dd($kept_product_days);
        $avg_date = collect($kept_product_days)->avg();
        // dd($avg_date);
        // calculate avg kept period from avg date....

        $years = ($avg_date / 365) ; // days / 365 days
        $years = floor($years); // Remove all decimals

        $month = ($avg_date % 365) / 30.5; // I choose 30.5 for Month (30,31) ;)
        $month = floor($month); // Remove all decimals

        $avg_date = ($avg_date % 365) % 30.5; // the rest of days
        if($years!=0){
            $avg_kept_period = $years.' years - '.$month.' month - '.$avg_date.' days';
        }else{
            $avg_kept_period = $month.' month - '.$avg_date.' days';
        }

        $picked_up_products = $picked_up_products->paginate(10);
        $master_products = DB::table('products')->where('flag','Active')->get();
        return view('Reports.pickup-products',compact('picked_up_products','master_products','total_rent','total_deposit','avg_kept_period'));
    }
}
