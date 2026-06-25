<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Lead\customer_detail;
use App\Models\Lead\lead;
use App\Models\MonthlyRecord;
use App\Models\Lead\leads_log;
use App\Exports\MonthlyRecords;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\RenewalPickup\RenewalPickupController;
use App\Http\Controllers\BillingAndPayment\BillingPaymentController;


class MonthlyReportController extends Controller
{

    protected $online_arr, $offline_arr, $corporate_arr;

    public function __construct(){
        $this->online_arr = ["Google Ads","Web Chat","Web Popup","Web Order","Web - Call","Web - WhatsApp","Just Dial"];
        $this->offline_arr = ["Wellness Forever","Reference","Returning Cust","Other","Ref"];
        $this->corporate_arr = ["Agent","B2B Cust","Corporate Booking"];        
    }

    public function isLoggedIn()
    {
        $data = session('isLoggedIn');
        //print_r($data);      
        return $data;
    }

    public function monthly_report(Request $request)
    {
        if($_SERVER['REQUEST_METHOD'] == 'GET')
        {
            $data['month_count'] = date('m');
            $data['year_count'] = date('Y');
            $user_id = 'All';
            $whereClause = "";
            // if(session('role') == 'superuser')
            // {
            //     $user_id = 'All';
            //     $data['user_id'] = "All";
            //     $whereClause = "";
            // }
            // elseif(session('role') == 'user')
            // {
            //     $user_id = session('user_id');
            //     $data['user_id'] = session('user_id');
            //     $user_id = $data['user_id'];
            //     $whereClause="AND leads.lead_owner = $user_id";
            // }
            $month = date('m');
            $year = date('Y');
            $start_date =$year.'-'.$month.'-'.'01';
            // $end_date =$year.'-'.$month.'-'.'24';
            $start_date = Carbon::parse($start_date)->startOfMonth()->toDateString();
            //$end_date = Carbon::parse($start_date)->endOfMonth()->toDateString();
            $end_date = Carbon::now()->toDateString();
            if(session('role') == 'superuser')
            {
                $users=DB::select("SELECT * FROM user WHERE role = 'user' AND email_id_user IS NOT NULL");
                $data['users'] = json_decode(json_encode($users),true);
            }
        }
        elseif ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
             //after add urgenty date picker
             $start_date = $request->get('start_date');
             $end_date = $request->get('end_date');
            // $data['month_count'] = $_POST['month'];
            // $data['year_count'] = $_POST['year'];
            // $month = $_POST['month'];
            // $year = $_POST['year'];
            $user_id = 'All';
            $whereClause = "";
            // if(session('role') == 'superuser')
            // {
            //     $data['user_id'] = $_POST['user'];
            //     $users=DB::select("SELECT * FROM user WHERE role = 'user' AND email_id_user IS NOT NULL");
            //     $data['users'] = json_decode(json_encode($users),true);
            //     if($_POST['user'] == "All")
            //     {
            //         $user_id = 'All';
            //         $whereClause = "";
            //     }
            //     else
            //     {
            //         $user_id = $_POST['user'];
            //         $whereClause = "AND leads.lead_owner = $user_id";
            //     }
            // }
            // elseif(session('role') == 'user')
            // {
            //     $data['user_id'] = session('user_id');
            //     $user_id = $data['user_id'];
            //     $whereClause="AND leads.lead_owner = $user_id";
            // }

            // // $year = date('Y');
            // $start_date =$year.'-'.$month.'-'.'01';
            // // $end_date =$year.'-'.$month.'-'.'31';
            // $start_date = Carbon::parse($start_date)->startOfMonth()->toDateString();
            // $end_date = Carbon::parse($start_date)->endOfMonth()->toDateString();
        }
        if(isset($start_date) && isset($end_date))
        {
            // if(isset($_POST['user']))
            // {
            //     $whereClause = "AND "
            // }
            $data['monthReportDetails'] = array();
            $equipments = DB::select("SELECT * FROM products ORDER BY product_name ASC");
            $data['equipments'] = json_decode(json_encode($equipments),true);
            $orderTypeNotIn = config('app.order_type');
            $orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";
            foreach ($data['equipments'] as $equipment)
            {
                $equip_id = $equipment['id'];
                $temp_array = array();
                $renew_amount_corp = 0;
                $renew_amount_indon = 0;
                $renew_amount_indoff = 0;
                $renew_count_corp = 0;
                $renew_count_indon = 0;
                $renew_count_indoff = 0;
                $rent_amount_corp = 0;
                $rent_amount_indon = 0;
                $rent_amount_indoff = 0;
                $rent_count_corp = 0;
                $rent_count_indon = 0;
                $rent_count_indoff = 0;
                $sale_amount_corp = 0;
                $sale_amount_indon = 0;
                $sale_amount_indoff = 0;
                $sale_count_corp = 0;
                $sale_count_indon = 0;
                $sale_count_indoff = 0;
                $overdue_amount_corp = 0;
                $overdue_amount_indon = 0;
                $overdue_amount_indoff = 0;
                $overdue_count_corp = 0;
                $overdue_count_indon = 0;
                $overdue_count_indoff = 0;
                $orderTypeNotIn = config('app.order_type');
                $renewals = DB::table('renewals')
                                    ->join('order_details','renewals.order_details_id','=','order_details.id')
                                    ->join('vendor_products','order_details.vendor_product_id','=','vendor_products.id')
                                    ->join('del_orders','renewals.collection_order_id','=','del_orders.order_id')
                                    ->join('leads','renewals.lead_id','=','leads.id')
                                    ->join('customer_details','leads.customer_id','=','customer_details.cust_id')                            
                                    ->select(                                
                                        'del_orders.order_id as del_order_id',
                                        'del_orders.collection_date as collection_date',
                                        'renewals.collection_order_id as collection_order_id',
                                        'renewals.cash_amount as cash_amount',
                                        'renewals.online_amount as online_amount',
                                        'renewals.payment_mode as payment_mode',
                                        'leads.lead_owner as lead_owner',
                                        'leads.lead_source as lead_source',
                                        'customer_details.customer_type',
                                        'renewals.id as renewal_id'
                                    )
                                    ->whereBetween('renewals.start_date',[$start_date,$end_date])        
                                    ->where('renewals.product_id',$equip_id)                    
                                    ->when($user_id,function($query,$user_id){
                                        if($user_id != "All")
                                        {
                                            $query->where('leads.lead_owner',$user_id);
                                        }
                                    })
                                    ->whereNotIn('del_orders.status',['Cancel'])
                                    ->whereNotIn('renewals.status',['Cancel'])
                                    ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                                    ->orderBy('renewals.start_date','Asc')
                                    ->get();
                // $renewal_count_data = json_decode(json_encode($renewals),true);

                $online_leads = ['Just Dial','Google Ads','Web Popup','Old User Site','Tawk.to','Web Order','Web Chat','Web - Call','Web - WhatsApp'];
                foreach($renewals as $key=>$renewal)
                {
                    // if($renewal['payment_mode'] == "Cash")
                    // {
                    //     $renew_amount += $renewal['cash_amount'];
                    // }
                    // elseif($renewal['payment_mode'] == "Online")
                    // {
                    //     $renew_amount += $renewal['online_amount'];
                    // }
                        $billingPaymentController = new BillingPaymentController();
                        if($renewal->payment_mode == 'Cash'){
                            $renewals[$key]->cash_amount = $billingPaymentController->fetchCrDrDataRE($renewal->renewal_id,$renewal->payment_mode);
                        }else{
                            $renewals[$key]->online_amount = $billingPaymentController->fetchCrDrDataRE($renewal->renewal_id,$renewal->payment_mode);
                        }
                    if($renewal->customer_type == 'Corporate'){
                        $renew_amount_corp += $renewal->cash_amount + $renewal->online_amount;
                        $renew_count_corp++;
                    }else{
                        if(in_array($renewal->lead_source,$online_leads)){
                            $renew_amount_indon += $renewal->cash_amount + $renewal->online_amount;
                            $renew_count_indon++;
                        }else{
                            $renew_amount_indoff += $renewal->cash_amount + $renewal->online_amount;
                            $renew_count_indoff++;
                        }
                    }
                }
                $temp_array['product_name'] = $equipment['product_name'];
                $temp_array['renewal_count_corp'] = $renew_count_corp;
                $temp_array['renewal_amount_corp'] = $renew_amount_corp;
                $temp_array['renewal_count_indon'] = $renew_amount_indon;
                $temp_array['renewal_amount_indon'] = $renew_amount_indon;
                $temp_array['renewal_count_indoff'] = $renew_count_indoff;
                $temp_array['renewal_amount_indoff'] = $renew_amount_indoff;
                $temp_array['category'] = $equipment['product_use_category'];
                $orderTypeNotIn = config('app.order_type');
                $orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";

                $rental_count = DB::select("SELECT
                                                del_orders.order_id as del_order_id,
                                                order_details.id as order_details_id,
                                                order_details.creation_date as delivery_date,
                                                order_details.product_rent as product_rent,
                                                order_details.product_qty as product_qty,
                                                order_details.sale_rental as sale_rental,
                                                leads.lead_owner as lead_owner,
                                                leads.lead_source as lead_source,
                                                customer_details.customer_type
                                            FROM
                                                order_details,del_orders,leads,customer_details
                                            WHERE
                                                order_details.customer_id = customer_details.cust_id
                                                AND
                                                del_orders.order_id = order_details.order_id
                                                AND
                                                order_details.product_id = $equip_id
                                                AND
                                                order_details.creation_date BETWEEN '$start_date' AND '$end_date'
                                                AND
                                                order_details.sale_rental = 'Rental'
                                                AND
                                                order_details.current_status != 'Cancel'
                                                AND 
                                                del_orders.deliverypickup NOT IN ($orderTypeNotIn)
                                                AND
                                                del_orders.status != 'Cancel'
                                                AND
                                                del_orders.lead_id = leads.id $whereClause");
                $rental_count_data = json_decode(json_encode($rental_count), true);
                foreach($rental_count_data as $key => $rental_count)
                {
                    $rental_count_data[$key]['product_rent'] = RenewalPickupController::fetchCrDrData($rental_count['order_details_id'],'R');
                    // $rent_amount += $rental_count['product_rent'];
                    // $rent_count += $rental_count['product_qty'];
                    if($rental_count['customer_type'] == 'Corporate'){
                        $rent_amount_corp += $rental_count['product_rent'];
                        $rent_count_corp += $rental_count['product_qty'];
                    }else{
                        if(in_array($rental_count['lead_source'],$online_leads)){
                            $rent_amount_indon += $rental_count['product_rent'];
                            $rent_count_indon += $rental_count['product_qty'];
                        }else{
                            $rent_amount_indoff += $rental_count['product_rent'];
                            $rent_count_indoff += $rental_count['product_qty'];
                        }
                    }
                }
                $salela_count = DB::select("SELECT
                                                del_orders.order_id as del_order_id,
                                                order_details.id as order_details_id,
                                                order_details.creation_date as delivery_date,
                                                order_details.product_rent as product_rent,
                                                order_details.product_qty as product_qty,
                                                order_details.sale_rental as sale_rental,
                                                leads.lead_owner as lead_owner,
                                                leads.lead_source as lead_source,
                                                customer_details.customer_type
                                            FROM
                                                order_details,del_orders,leads,customer_details
                                            WHERE
                                                order_details.customer_id = customer_details.cust_id
                                                AND
                                                del_orders.order_id = order_details.order_id
                                                AND
                                                order_details.product_id = $equip_id
                                                AND
                                                order_details.creation_date BETWEEN '$start_date' AND '$end_date'
                                                AND
                                                order_details.sale_rental = 'Sale'
                                                AND
                                                order_details.current_status != 'Cancel'
                                                AND 
                                                del_orders.deliverypickup NOT IN ($orderTypeNotIn)
                                                AND
                                                del_orders.status != 'Cancel'
                                                AND
                                                del_orders.lead_id = leads.id $whereClause");
                $sale_count_data = json_decode(json_encode($salela_count), true);
                foreach($sale_count_data as $key => $sale)
                {
                    $sale_count_data[$key]['product_rent'] = RenewalPickupController::fetchCrDrData($sale['order_details_id'],'R');
                    // $sale_amount += $sale['product_rent'];
                    // $sale_count += $sale['product_qty'];
                    if($sale['customer_type'] == 'Corporate'){
                        $sale_amount_corp += $sale['product_rent'];
                        $sale_count_corp += $sale['product_qty'];
                    }else{
                        if(in_array($sale['lead_source'],$online_leads)){
                            $sale_amount_indon += $sale['product_rent'];
                            $sale_count_indon += $sale['product_qty'];
                        }else{
                            $sale_amount_indoff += $sale['product_rent'];
                            $sale_count_indoff += $sale['product_qty'];
                        }
                    }
                }
                $overdue_count_data = DB::select("SELECT
                                                del_orders.order_id as del_order_id,
                                                order_details.id as order_details_id,
                                                order_details.creation_date as delivery_date,
                                                order_details.product_rent as product_rent,
                                                order_details.product_qty as product_qty,
                                                order_details.sale_rental as sale_rental,
                                                leads.lead_source as lead_source,
                                                customer_details.customer_type
                                            FROM
                                                order_details,del_orders,customer_details,leads
                                            WHERE
                                                order_details.customer_id = customer_details.cust_id
                                                AND
                                                    del_orders.order_id = order_details.order_id
                                                AND
                                                    order_details.product_id = $equip_id
                                                AND
                                                    order_details.pickup_date BETWEEN '$start_date' AND '$end_date'
                                                AND
                                                    order_details.current_status NOT IN ('Picked UP','Picked Up','Pending Pickup','Cancel')
                                                AND 
                                                    del_orders.deliverypickup NOT IN ($orderTypeNotIn)
                                                AND
                                                    del_orders.status != 'Cancel'
                                                AND
                                                    order_details.sale_rental = 'Rental'AND
                                                    del_orders.lead_id = leads.id");
                $overdue_count_data = json_decode(json_encode($overdue_count_data), true);
                foreach($overdue_count_data as $key => $overdue_count_data_single)
                {
                    $overdue_count_data[$key]['product_rent'] = RenewalPickupController::fetchCrDrData($overdue_count_data_single['order_details_id'],'R');
                    // $overdue_amount += $overdue_count_data_single['product_rent'];
                    // $overdue_count += $overdue_count_data_single['product_qty'];
                    if($overdue_count_data_single['customer_type'] == 'Corporate'){
                        $overdue_amount_corp += $overdue_count_data_single['product_rent'];
                        $overdue_count_corp += $overdue_count_data_single['product_qty'];
                    }else{
                        if(in_array($overdue_count_data_single['lead_source'],$online_leads)){
                            $overdue_amount_indon += $overdue_count_data_single['product_rent'];
                            $overdue_count_indon += $overdue_count_data_single['product_qty'];
                        }else{
                            $overdue_amount_indoff += $overdue_count_data_single['product_rent'];
                            $overdue_count_indoff += $overdue_count_data_single['product_qty'];
                        }
                    }
                }
                $temp_array['rental_count_corp'] = $rent_count_corp;
                $temp_array['rental_amount_corp'] = $rent_amount_corp;
                $temp_array['rental_count_indon'] = $rent_count_indon;
                $temp_array['rental_amount_indon'] = $rent_amount_indon;
                $temp_array['rental_count_indoff'] = $rent_count_indoff;
                $temp_array['rental_amount_indoff'] = $rent_amount_indoff;
                $temp_array['sale_count_corp'] = $sale_count_corp;
                $temp_array['sale_amount_corp'] = $sale_amount_corp;
                $temp_array['sale_count_indon'] = $sale_count_indon;
                $temp_array['sale_amount_indon'] = $sale_amount_indon;
                $temp_array['sale_count_indoff'] = $sale_count_indoff;
                $temp_array['sale_amount_indoff'] = $sale_amount_indoff;
                // $temp_array['total_count'] = count($renewals->toArray()) + $rent_count;
                // $temp_array['total_amount'] = $rent_amount + $renew_amount;
                $temp_array['overdue_count_corp'] = $overdue_count_corp;
                $temp_array['overdue_amount_corp'] = $overdue_amount_corp;
                $temp_array['overdue_count_indon'] = $overdue_count_indon;
                $temp_array['overdue_amount_indon'] = $overdue_amount_indon;
                $temp_array['overdue_count_indoff'] = $overdue_count_indoff;
                $temp_array['overdue_amount_indoff'] = $overdue_amount_indoff;
                $temp_array['total_count_corp'] = $renew_count_corp+$rent_count_corp+$sale_count_corp+$overdue_count_corp;
                $temp_array['total_amount_corp'] = $renew_amount_corp+$rent_amount_corp+$sale_amount_corp+$overdue_amount_corp;
                $temp_array['total_count_indon'] = $renew_count_indon+$rent_count_indon+$sale_count_indon+$overdue_count_indon;
                $temp_array['total_amount_indon'] =$renew_amount_indon+$rent_amount_indoff+$sale_amount_indoff+$overdue_amount_indoff;
                $temp_array['total_count_indoff'] =$renew_count_indoff+$rent_count_indoff+$sale_count_indoff+$overdue_count_indoff;
                $temp_array['total_amount_indoff'] = $renew_amount_indoff+$rent_amount_indoff+$sale_amount_indoff+$overdue_amount_indoff;
                if($temp_array['total_count_corp']!=0 || $temp_array['total_count_indon']!=0 || $temp_array['total_count_indoff']!=0)
                {
                    array_push($data['monthReportDetails'],$temp_array);
                }
            }
            $temp_product_name = array();
            $report_details = array();
            foreach($data['monthReportDetails'] as $key=>$value)
            {
                if(in_array(strtolower($value['product_name']),$temp_product_name))
                {
                    $count = array_search(strtolower($value['product_name']),$temp_product_name);
                    $report_details[$count]['renewal_count_corp'] = $report_details[$count]['renewal_count_corp'] + $value['renewal_count_corp'];
                    $report_details[$count]['renewal_amount_corp'] = $report_details[$count]['renewal_amount_corp'] + $value['renewal_amount_corp'];
                    $report_details[$count]['renewal_count_indon'] = $report_details[$count]['renewal_count_indon'] + $value['renewal_count_indon'];
                    $report_details[$count]['renewal_amount_indon'] = $report_details[$count]['renewal_amount_indon'] + $value['renewal_amount_indon'];
                    $report_details[$count]['renewal_count_indoff'] = $report_details[$count]['renewal_count_indoff'] + $value['renewal_count_indoff'];
                    $report_details[$count]['renewal_amount_indoff'] = $report_details[$count]['renewal_amount_indoff'] + $value['renewal_amount_indoff'];

                    $report_details[$count]['rental_count_corp'] = $report_details[$count]['rental_count_corp'] + $value['rental_count_corp'];
                    $report_details[$count]['rental_amount_corp'] = $report_details[$count]['rental_amount_corp'] + $value['rental_amount_corp'];
                    $report_details[$count]['rental_count_indon'] = $report_details[$count]['rental_count_indon'] + $value['rental_count_indon'];
                    $report_details[$count]['rental_amount_indon'] = $report_details[$count]['rental_amount_indon'] + $value['rental_amount_indon'];
                    $report_details[$count]['rental_count_indoff'] = $report_details[$count]['rental_count_indoff'] + $value['rental_count_indoff'];
                    $report_details[$count]['rental_amount_indoff'] = $report_details[$count]['rental_amount_indoff'] + $value['rental_amount_indoff'];

                    $report_details[$count]['sale_count_corp'] = $report_details[$count]['sale_count_corp'] + $value['sale_count_corp'];
                    $report_details[$count]['sale_amount_corp'] = $report_details[$count]['sale_amount_corp'] + $value['sale_amount_corp'];
                    $report_details[$count]['sale_count_indon'] = $report_details[$count]['sale_count_indon'] + $value['sale_count_indon'];
                    $report_details[$count]['sale_amount_indon'] = $report_details[$count]['sale_amount_indon'] + $value['sale_amount_indon'];
                    $report_details[$count]['sale_count_indoff'] = $report_details[$count]['sale_count_indoff'] + $value['sale_count_indoff'];
                    $report_details[$count]['sale_amount_indoff'] = $report_details[$count]['sale_amount_indoff'] + $value['sale_amount_indoff'];

                    $report_details[$count]['overdue_count_corp'] = $report_details[$count]['overdue_count_corp'] + $value['overdue_count_corp'];
                    $report_details[$count]['overdue_amount_corp'] = $report_details[$count]['overdue_amount_corp'] + $value['overdue_amount_corp'];
                    $report_details[$count]['overdue_count_indon'] = $report_details[$count]['overdue_count_indon'] + $value['overdue_count_indon'];
                    $report_details[$count]['overdue_amount_indon'] = $report_details[$count]['overdue_amount_indon'] + $value['overdue_amount_indon'];
                    $report_details[$count]['overdue_count_indoff'] = $report_details[$count]['overdue_count_indoff'] + $value['overdue_count_indoff'];
                    $report_details[$count]['overdue_amount_indoff'] = $report_details[$count]['overdue_amount_indoff'] + $value['overdue_amount_indoff'];
                    
                    $report_details[$count]['total_count_corp'] = $report_details[$count]['total_count_corp'] + $value['total_count_corp'];
                    $report_details[$count]['total_amount_corp'] = $report_details[$count]['total_amount_corp'] + $value['total_amount_corp'];
                    $report_details[$count]['total_count_indon'] = $report_details[$count]['total_count_indon'] + $value['total_count_indon'];
                    $report_details[$count]['total_amount_indon'] = $report_details[$count]['total_amount_indon'] + $value['total_amount_indon'];
                    $report_details[$count]['total_count_indoff'] = $report_details[$count]['total_count_indoff'] + $value['total_count_indoff'];
                    $report_details[$count]['total_amount_indoff'] = $report_details[$count]['total_amount_indoff'] + $value['total_amount_indoff'];
                }
                else{
                    $count = count($temp_product_name);
                    $report_details[$count]['product_name'] = $value['product_name'];
                    $report_details[$count]['category'] = $value['category'];
                    $report_details[$count]['renewal_count_corp'] = $value['renewal_count_corp'];
                    $report_details[$count]['renewal_amount_corp'] = $value['renewal_amount_corp'];
                    $report_details[$count]['renewal_count_indon'] = $value['renewal_count_indon'];
                    $report_details[$count]['renewal_amount_indon'] = $value['renewal_amount_indon'];
                    $report_details[$count]['renewal_count_indoff'] = $value['renewal_count_indoff'];
                    $report_details[$count]['renewal_amount_indoff'] = $value['renewal_amount_indoff'];

                    $report_details[$count]['rental_count_corp'] = $value['rental_count_corp'];
                    $report_details[$count]['rental_amount_corp'] = $value['rental_amount_corp'];
                    $report_details[$count]['rental_count_indon'] = $value['rental_count_indon'];
                    $report_details[$count]['rental_amount_indon'] = $value['rental_amount_indon'];
                    $report_details[$count]['rental_count_indoff'] = $value['rental_count_indoff'];
                    $report_details[$count]['rental_amount_indoff'] = $value['rental_amount_indoff'];

                    $report_details[$count]['sale_count_corp'] = $value['sale_count_corp'];
                    $report_details[$count]['sale_amount_corp'] = $value['sale_amount_corp'];
                    $report_details[$count]['sale_count_indon'] = $value['sale_count_indon'];
                    $report_details[$count]['sale_amount_indon'] = $value['sale_amount_indon'];
                    $report_details[$count]['sale_count_indoff'] = $value['sale_count_indoff'];
                    $report_details[$count]['sale_amount_indoff'] = $value['sale_amount_indoff'];

                    $report_details[$count]['overdue_count_corp'] = $value['overdue_count_corp'];
                    $report_details[$count]['overdue_amount_corp'] = $value['overdue_amount_corp'];
                    $report_details[$count]['overdue_count_indon'] = $value['overdue_count_indon'];
                    $report_details[$count]['overdue_amount_indon'] = $value['overdue_amount_indon'];
                    $report_details[$count]['overdue_count_indoff'] = $value['overdue_count_indoff'];
                    $report_details[$count]['overdue_amount_indoff'] = $value['overdue_amount_indoff'];
                    $report_details[$count]['total_count_corp'] = $value['total_count_corp'];
                    $report_details[$count]['total_amount_corp'] = $value['total_amount_corp'];
                    $report_details[$count]['total_count_indon'] = $value['total_count_indon'];
                    $report_details[$count]['total_amount_indon'] = $value['total_amount_indon'];
                    $report_details[$count]['total_count_indoff'] = $value['total_count_indoff'];
                    $report_details[$count]['total_amount_indoff'] = $value['total_amount_indoff'];
                    array_push($temp_product_name,strtolower($value['product_name']));
                }
            }
            $data['monthReportDetails'] = $report_details;
            
            // print_r($data['monthReportDetails']);
             // print_r($data['monthReportDetails']);
             $data['start_date'] = ($start_date!=null) ? $start_date : null;
             $data['end_date'] = ($end_date!=null) ? $end_date : null;

            return view('Reports/monthly_report',$data);        
        }
    }

    public function monthly_records(Request $request)
    {
        $city = 'All';
        if($request->get('city'))
        {
            $city = $request->get('city');
        }

        $total_rental = 0;
        $due_rent = 0;
        $total_rent_collected = 0;
        $total_unit_rented = 0;
        $total_customer_served_rental = 0;
        $new_rent_collected = 0;
        $new_unit_rented = 0;
        $new_customer_rental = 0;
        $value_added_services = 0;
        $renewal_rent_collected = 0;
        $renewal_count_of_equipment = 0;
        $vdr_payment = 0;
        $vdr_payment_other_q5c = 0;
        $rental_transportation = 0;
        $transportation_expense = 0;
        $total_expense = 0;
        $google_spend = 0;
        $no_of_clicks = 0;
        $google_impr = 0;
        $justdial = 0;
        $offline_marketing = 0;
        $sales_value = 0;
        $purchase_value = 0;
        $sales_customer = 0;
        $sales_transport = 0;
        

        $period = $request->get('headfyear');
        if($period == '2022-2023' || $period == '2021-2022' || $period == '2023-2024' || $period == '2024-2025' || $period == '2025-2026')
        {
            // dd($request->all());
            $month_data = array();
            if($period == '2022-2023')
            {
                $month_count = array('2022-04-01','2022-05-01','2022-06-01','2022-07-01','2022-08-01','2022-09-01','2022-10-01','2022-11-01','2022-12-01','2023-01-01','2023-02-01','2023-03-01');
            }
            elseif($period == '2023-2024')
            {
                $month_count = array('2023-04-01','2023-05-01','2023-06-01','2023-07-01','2023-08-01','2023-09-01','2023-10-01','2023-11-01','2023-12-01','2024-01-01','2024-02-01','2024-03-01');
            }
            elseif($period == '2024-2025')
            {
                $month_count = array('2024-04-01','2024-05-01','2024-06-01','2024-07-01','2024-08-01','2024-09-01','2024-10-01','2024-11-01','2024-12-01','2025-01-01','2025-02-01','2025-03-01');
            }
            elseif($period == '2021-2022')
            {
                $month_count = array('2021-04-01','2021-05-01','2021-06-01','2021-07-01','2021-08-01','2021-09-01','2021-10-01','2021-11-01','2021-12-01','2022-01-01','2022-02-01','2022-03-01');
            }
            elseif ($period == '2025-2026') {
                $month_count = array('2025-04-01','2025-05-01','2025-06-01','2025-07-01','2025-08-01','2025-09-01','2025-10-01','2025-11-01','2025-12-01','2026-01-01','2026-02-01','2026-03-01');
            }
            // for($i=4; $i<=date('m');$i++)
            foreach($month_count as $key=>$value)
            {
                // dd($value);
                if(date('Y-m',strtotime($value)) > date('Y-m'))
                {
                    break;
                }
                $month_year = date('M-y',strtotime($value));
                $count = count($month_data);
                $month_data[$count]['month'] = $month_year;
                $month_data[$count]['city'] = $city;
                $month_data[$count]['headfyear'] = $period;                
                $month = date('m',strtotime($value));
                if(substr($month, 0, 1) == 0)
                {
                    $month = substr($month, 1, 1);
                }
                $year = date('Y',strtotime($value));
                $start_date = date('Y-m-d',strtotime($value));

                // $end_date = date('Y-m-d',strtotime('2022-'.$i.'-31'));
                $start_date = Carbon::parse($start_date)->startOfMonth()->toDateString();
                $end_date = Carbon::parse($start_date)->endOfMonth()->toDateString();
                if($end_date >Carbon::today()->toDateString())
                {
                    $end_date = Carbon::today()->toDateString();
                }
                
                // Renewal Data
                $month_data[$count]['nursing_income'] = $this->getNursingIncome($city,$start_date,$end_date);
                $renewal = $this->renewal_data($city,$start_date,$end_date);
                $month_data[$count]['due_rent'] = $renewal['due_rent'];
                $month_data[$count]['renewal_rent_collected'] = $renewal['renewal_rent_collected'];
                $month_data[$count]['renewal_rent_collected_online'] = $renewal['renewal_rent_collected_online'];
                $month_data[$count]['renewal_rent_collected_offline'] = $renewal['renewal_rent_collected_offline'];
                $month_data[$count]['renewal_rent_collected_corporate'] = $renewal['renewal_rent_collected_corporate'];
                $month_data[$count]['renewal_count_of_equipment'] = $renewal['renewal_count_of_equipment'];
                $month_data[$count]['vendor_payment_no_q5c_renewal'] = $renewal['vendor_payment_no_q5c_renewal'];
                $month_data[$count]['vendor_payment_renewal'] = $renewal['vendor_payment_renewal'];
                $month_data[$count]['vendor_equipment_renewal'] = $renewal['vendor_equipment_renewal'];
                
                // New Customer or Rental Data
                $new = $this->new_orders_data($city,$start_date,$end_date);
                $month_data[$count]['new_rent_collected'] = $new['new_rent_collected'];
                $month_data[$count]['new_rent_collected_online'] = $new['new_rent_collected_online'];
                $month_data[$count]['new_rent_collected_offline'] = $new['new_rent_collected_offline'];
                $month_data[$count]['new_rent_collected_corporate'] = $new['new_rent_collected_corporate'];
                $month_data[$count]['new_rent_deposite'] = $new['new_rent_deposite'];
                $month_data[$count]['new_unit_rented'] = $new['new_unit_rented'];
                $month_data[$count]['rental_transportation'] = $new['rental_transportation'];
                $month_data[$count]['sales_transport'] = $new['sales_transport'];
                $month_data[$count]['sales_value'] = $new['sales_value'];
                $month_data[$count]['vendor_payment_rent'] = $new['vendor_payment_rent'];
                $month_data[$count]['vendor_equipment_rent'] = $new['vendor_equipment_rent'];
                $month_data[$count]['vendor_payment_no_q5c_rent'] = $new['vendor_payment_no_q5c_rent'];

                // Customer_rented
                $customer_count = $this->monthly_customer_count($city,$start_date,$end_date);
                // dd($customer_count);
                $month_data[$count]['sales_customer'] = $customer_count['sales_customer'];
                $month_data[$count]['new_customer_rented'] = $customer_count['new_customer_rented'];
                $month_data[$count]['new_customer_rented_online'] = $customer_count['new_customer_rented_online'];
                $month_data[$count]['new_customer_rented_offline'] = $customer_count['new_customer_rented_offline'];
                $month_data[$count]['new_customer_rented_corporate'] = $customer_count['new_customer_rented_corporate'];
                $month_data[$count]['total_customer_served_rental'] = $customer_count['total_customer_rented'];
                $month_data[$count]['total_renewed_customer'] = $customer_count['total_renewed_customer'];
                
                // Monthly Static Data
                $monthly_data = $this->month_data($city,$month,$year);
                // dd($month_data);
                $month_data[$count]['transportation_expense'] = $monthly_data['transportation_expense'];
                $month_data[$count]['total_expense'] = $monthly_data['total_expense'];
                $month_data[$count]['google_spend_marketing'] = $monthly_data['google_spend_marketing'];
                $month_data[$count]['no_of_clicks'] = $monthly_data['no_of_clicks'];
                $month_data[$count]['impressions'] = $monthly_data['impressions'];
                $month_data[$count]['justdial'] = $monthly_data['justdial'];
                $month_data[$count]['offline_marketing'] = $monthly_data['offline_marketing'];
                $month_data[$count]['value_added_service'] = $monthly_data['value_added_service'];
                $month_data[$count]['purchase_value'] = $monthly_data['purchase_value'];

                // Maths Calculations
                // $month_data[$count]['total_rent_collected'] = $month_data[$count]['new_rent_collected'];
                // $month_data[$count]['total_rent_collected'] = $month_data[$count]['new_rent_collected'] + $month_data[$count]['renewal_rent_collected'];
                $month_data[$count]['total_rent_collected'] = $month_data[$count]['new_rent_collected'] + $month_data[$count]['renewal_rent_collected_online'] + $month_data[$count]['renewal_rent_collected_offline'] + $month_data[$count]['renewal_rent_collected_corporate'];
                $month_data[$count]['total_revenue'] = $month_data[$count]['total_rent_collected'] + $month_data[$count]['rental_transportation'] + $month_data[$count]['sales_value'] + $month_data[$count]['sales_transport']+ $month_data[$count]['due_rent'] + $month_data[$count]['nursing_income'];
                $month_data[$count]['gross_earning_total'] = $month_data[$count]['total_rent_collected'] + $month_data[$count]['rental_transportation'] + $month_data[$count]['sales_value'] + $month_data[$count]['sales_transport']+ $month_data[$count]['due_rent'];
                $month_data[$count]['total_rent'] = $month_data[$count]['total_rent_collected'] + $month_data[$count]['due_rent'];
                if($count !=0)
                    if($month_data[$count]['total_rent_collected'] != 0)
                        $month_data[$count]['total_growth_over_last_month'] = round((($month_data[$count]['total_rent_collected'] - $month_data[$count-1]['total_rent_collected'])/$month_data[$count]['total_rent_collected'])*100);
                    else
                        $month_data[$count]['total_growth_over_last_month'] = 0;
                else
                    $month_data[$count]['total_growth_over_last_month'] = 0;

                $month_data[$count]['total_unit_rented'] = $month_data[$count]['new_unit_rented'] + $month_data[$count]['renewal_count_of_equipment'];
                if($count !=0)
                    if($month_data[$count]['new_customer_rented'] != 0)
                        $month_data[$count]['new_growth_over_last_month'] = round((($month_data[$count]['new_customer_rented'] - $month_data[$count-1]['new_customer_rented'])/$month_data[$count]['new_customer_rented'])*100);
                    else
                        $month_data[$count]['new_growth_over_last_month'] = 0;
                else
                    $month_data[$count]['new_growth_over_last_month'] = 0;

                if($month_data[$count]['total_rent_collected'] != 0)
                    // $month_data[$count]['renewal_%'] = round((($month_data[$count]['renewal_rent_collected']/$month_data[$count]['total_rent_collected']))*100);
                    $month_data[$count]['renewal_%'] = round(((($month_data[$count]['renewal_rent_collected_online'] + $month_data[$count]['renewal_rent_collected_offline'] + $month_data[$count]['renewal_rent_collected_corporate'])/$month_data[$count]['total_rent_collected']))*100);
                else
                    $month_data[$count]['renewal_%'] = 0;

                $month_data[$count]['net_earning_from_vendor_equipment'] = 'NA';
                $month_data[$count]['%_of_vendor_net_rental_earning'] = 'NA';
                $month_data[$count]['net_rental_earning'] = $month_data[$count]['total_rent_collected'] - $month_data[$count]['vendor_payment_no_q5c_rent'];
                
                if($month_data[$count]['total_rent_collected'] != 0)
                    $month_data[$count]['gross_net_rental_earning'] = round((($month_data[$count]['total_rent_collected'] - $month_data[$count]['vendor_payment_no_q5c_rent'])/$month_data[$count]['total_rent_collected'])*100);
                else
                    $month_data[$count]['gross_net_rental_earning'] = 0;

            
                if($month_data[$count]['no_of_clicks'] != 0)
                    $month_data[$count]['conversion_ratio'] = round((($month_data[$count]['new_unit_rented']/$month_data[$count]['no_of_clicks']))*100);
                else
                    $month_data[$count]['conversion_ratio'] = 0;

                if($month_data[$count]['new_customer_rented_online'] != 0)
                    $month_data[$count]['new_customer_aquition_cost'] = round(($month_data[$count]['google_spend_marketing'] + $month_data[$count]['justdial'] + $month_data[$count]['offline_marketing'])/$month_data[$count]['new_customer_rented_online']);
                else
                    $month_data[$count]['new_customer_aquition_cost'] = 0;

                if($month_data[$count]['total_customer_served_rental'] != 0 && $month_data[$count]['total_renewed_customer'] != 0)
                    $month_data[$count]['all_customer_aquition_cost'] = round(($month_data[$count]['total_rent_collected']/($month_data[$count]['total_customer_served_rental'] + $month_data[$count]['total_renewed_customer'])));
                else
                    $month_data[$count]['all_customer_aquition_cost'] = 0;
            
                if($month_data[$count]['new_unit_rented'] != 0)
                    $month_data[$count]['avg_rental_per_customer_new'] = round(($month_data[$count]['new_rent_collected']/$month_data[$count]['total_customer_served_rental']));
                else
                    $month_data[$count]['avg_rental_per_customer_new'] = 0;
            
                if($month_data[$count]['total_unit_rented'] != 0)
                    $month_data[$count]['avg_rental_per_customer_rental'] = round(($month_data[$count]['total_rent_collected']/$month_data[$count]['total_unit_rented']));
                else
                    $month_data[$count]['avg_rental_per_customer_rental'] = 0;
            
                if($month_data[$count]['purchase_value'] != 0)
                    $month_data[$count]['sales_margin'] = round((($month_data[$count]['sales_value'] - $month_data[$count]['purchase_value'])/$month_data[$count]['purchase_value'])*100);
                else
                    $month_data[$count]['sales_margin'] = 0;
            
                if($month_data[$count]['sales_customer'] != 0)
                    $month_data[$count]['over_all_aquition_cost'] = round(($month_data[$count]['google_spend_marketing']/($month_data[$count]['sales_customer']+$month_data[$count]['new_unit_rented'])));
                else
                    $month_data[$count]['over_all_aquition_cost'] = 0;
            }            
            if($request->get('btn_submit') == 'export_excel')
            {
                ob_end_clean(); // this
                ob_start(); // and this
                return Excel::download(new MonthlyRecords($month_data), 'Monthly Records'.date('Y-m-d H:i:s').'.xlsx');
            }
            else
            {
                // dd($month_data);
                return view('Reports.monthly_records',compact('month_data'));
            }
        }
        else
        {
            // dd($period);
            $records = DB::table('monthly_records')->where('fyear',$period)->orderBy('id')->get()->toArray();
            $month_data = array();
            foreach($records as $key=>$value)
            {
                $count = count($month_data);
                $month_data[$count]['month'] = date('M-y',strtotime($value->year.'-'.$value->month.'-01'));
                $month_data[$count]['city'] = $value->city;
                $month_data[$count]['headfyear'] = $value->fyear;        
                $month_data[$count]['nursing_income'] = 0;        
                
                // Renewal Data
                $month_data[$count]['due_rent'] = $value->overdue_rent;
                $month_data[$count]['renewal_rent_collected'] = $value->renewal_rent_collected;
                $month_data[$count]['renewal_count_of_equipment'] = $value->renewal_count_of_equipment;
                $month_data[$count]['vendor_payment_no_q5c_renewal'] = $value->vdr_payment;
                // $month_data[$count]['vendor_payment_renewal'] = $value->vendor_payment_renewal;
                $month_data[$count]['vendor_equipment_renewal'] = $value->vdr_payment/2;
                
                // New Customer or Rental Data                
                $month_data[$count]['new_rent_collected'] = $value->new_rent_collected;
                $month_data[$count]['new_rent_deposite'] = 0;
                $month_data[$count]['new_unit_rented'] = $value->new_unit_rented;
                $month_data[$count]['rental_transportation'] = $value->rental_transportation;
                $month_data[$count]['sales_transport'] = $value->sales_transport;
                $month_data[$count]['sales_value'] = $value->sales_value;
                // $month_data[$count]['vendor_payment_rent'] = $value->vendor_payment_rent;
                $month_data[$count]['vendor_equipment_rent'] = $value->vdr_payment/2;
                $month_data[$count]['vendor_payment_no_q5c_rent'] = $value->vdr_payment;

                // Customer_rented
                $month_data[$count]['sales_customer'] = $value->sales_customer;
                $month_data[$count]['new_customer_rented'] = $value->new_customer_rental;
                $month_data[$count]['total_customer_served_rental'] = $value->total_customer_served_rental;
                $month_data[$count]['total_renewed_customer'] = 0;
                
                // Monthly Static Data
                $month_data[$count]['transportation_expense'] = $value->transportation_expense;
                $month_data[$count]['total_expense'] = $value->total_expense;
                $month_data[$count]['google_spend_marketing'] = $value->google_spend;
                $month_data[$count]['no_of_clicks'] = $value->no_of_clicks;
                $month_data[$count]['impressions'] = $value->google_impr;
                $month_data[$count]['justdial'] = $value->justdial;
                $month_data[$count]['offline_marketing'] = $value->offline_marketing;
                $month_data[$count]['value_added_service'] = $value->value_added_services;
                $month_data[$count]['purchase_value'] = $value->purchase_value;

                // Maths Calculations
                $month_data[$count]['total_rent_collected'] = $month_data[$count]['new_rent_collected'] + $month_data[$count]['renewal_rent_collected'];
                $month_data[$count]['total_revenue'] = $month_data[$count]['total_rent_collected'] + $month_data[$count]['rental_transportation'] + $month_data[$count]['sales_value'] + $month_data[$count]['sales_transport']+ $month_data[$count]['due_rent'];
                $month_data[$count]['gross_earning_total'] = $month_data[$count]['total_rent_collected'] + $month_data[$count]['rental_transportation'] + $month_data[$count]['sales_value'] + $month_data[$count]['sales_transport']+ $month_data[$count]['due_rent'];
                $month_data[$count]['total_rent'] = $month_data[$count]['total_rent_collected'] + $month_data[$count]['due_rent'];
                if($count !=0)
                    if($month_data[$count]['total_rent_collected'] != 0)
                        $month_data[$count]['total_growth_over_last_month'] = round((($month_data[$count]['total_rent_collected'] - $month_data[$count-1]['total_rent_collected'])/$month_data[$count]['total_rent_collected'])*100);
                    else
                        $month_data[$count]['total_growth_over_last_month'] = 0;
                else
                    $month_data[$count]['total_growth_over_last_month'] = 0;

                $month_data[$count]['total_unit_rented'] = $month_data[$count]['new_unit_rented'] + $month_data[$count]['renewal_count_of_equipment'];
                if($count !=0)
                    if($month_data[$count]['new_customer_rented'] != 0)
                        $month_data[$count]['new_growth_over_last_month'] = round((($month_data[$count]['new_customer_rented'] - $month_data[$count-1]['new_customer_rented'])/$month_data[$count]['new_customer_rented'])*100);
                    else
                        $month_data[$count]['new_growth_over_last_month'] = 0;
                else
                    $month_data[$count]['new_growth_over_last_month'] = 0;

                if($month_data[$count]['total_rent_collected'] != 0)
                    $month_data[$count]['renewal_%'] = round((($month_data[$count]['renewal_rent_collected']/$month_data[$count]['total_rent_collected']))*100);
                else
                    $month_data[$count]['renewal_%'] = 0;

                $month_data[$count]['net_earning_from_vendor_equipment'] = 'NA';
                $month_data[$count]['%_of_vendor_net_rental_earning'] = 'NA';
                $month_data[$count]['net_rental_earning'] = $month_data[$count]['total_rent_collected'] - $month_data[$count]['vendor_payment_no_q5c_renewal'];
                
                if($month_data[$count]['total_rent_collected'] != 0)
                    $month_data[$count]['gross_net_rental_earning'] = round((($month_data[$count]['total_rent_collected'] - $month_data[$count]['vendor_payment_no_q5c_renewal'])/$month_data[$count]['total_rent_collected'])*100);
                else
                    $month_data[$count]['gross_net_rental_earning'] = 0;

            
                if($month_data[$count]['no_of_clicks'] != 0)
                    $month_data[$count]['conversion_ratio'] = round((($month_data[$count]['new_unit_rented']/$month_data[$count]['no_of_clicks']))*100);
                else
                    $month_data[$count]['conversion_ratio'] = 0;
            
                if($month_data[$count]['total_customer_served_rental'] != 0)
                    $month_data[$count]['new_customer_aquition_cost'] = round(($month_data[$count]['new_rent_collected']/$month_data[$count]['total_customer_served_rental']));
                else
                    $month_data[$count]['new_customer_aquition_cost'] = 0;
                
                if($month_data[$count]['total_customer_served_rental'] != 0 && $month_data[$count]['total_renewed_customer'] != 0)
                    $month_data[$count]['all_customer_aquition_cost'] = round(($month_data[$count]['total_rent_collected']/($month_data[$count]['total_customer_served_rental'] + $month_data[$count]['total_renewed_customer'])));
                else
                    $month_data[$count]['all_customer_aquition_cost'] = 0;

                if($month_data[$count]['new_unit_rented'] != 0)
                    $month_data[$count]['avg_rental_per_customer_new'] = round(($month_data[$count]['new_rent_collected']/$month_data[$count]['total_customer_served_rental']));
                else
                    $month_data[$count]['avg_rental_per_customer_new'] = 0;
            
                if($month_data[$count]['total_unit_rented'] != 0)
                    $month_data[$count]['avg_rental_per_customer_rental'] = round(($month_data[$count]['total_rent_collected']/$month_data[$count]['total_unit_rented']));
                else
                    $month_data[$count]['avg_rental_per_customer_rental'] = 0;
            
                if($month_data[$count]['purchase_value'] != 0)
                    $month_data[$count]['sales_margin'] = round((($month_data[$count]['sales_value'] - $month_data[$count]['purchase_value'])/$month_data[$count]['purchase_value'])*100);
                else
                    $month_data[$count]['sales_margin'] = 0;
            
                if($month_data[$count]['sales_customer'] != 0)
                    $month_data[$count]['over_all_aquition_cost'] = round(($month_data[$count]['google_spend_marketing']/($month_data[$count]['sales_customer']+$month_data[$count]['new_unit_rented'])));
                else
                    $month_data[$count]['over_all_aquition_cost'] = 0;
            }
            if($request->get('btn_submit') == 'export_excel')
            {
                ob_end_clean(); // this
                ob_start(); // and this
                return Excel::download(new MonthlyRecords($month_data), 'Monthly Records'.date('Y-m-d H:i:s').'.xlsx');
            }
            else
            {
                return view('Reports.monthly_records',compact('month_data'));
            } 
        }
    }

    public function renewal_data($city,$start_date,$end_date)
    {
        
        $orderTypeNotIn = config('app.order_type');
        $renewed_count = DB::table('renewals')
                            ->join('order_details','renewals.order_details_id','=','order_details.id')
                            ->join('vendor_products','order_details.vendor_product_id','=','vendor_products.id')
                            ->join('del_orders','renewals.collection_order_id','=','del_orders.order_id')
                            ->join('leads','renewals.lead_id','=','leads.id')
                            ->join('customer_details','leads.customer_id','=','customer_details.cust_id')                            
                            ->select(                                
                                'renewals.cash_amount as cash_amount',
                                'renewals.online_amount as online_amount',
                                'vendor_products.product_rent_approved',
                                'leads.lead_source as lead_source',
                                'leads.customer_source as customer_source',
                                'renewals.id as renewal_id',
                                'renewals.payment_mode'
                            )
                            ->whereBetween('renewals.start_date',[$start_date,$end_date])                            
                            ->when($city,function($query,$city){
                                if($city != "All")
                                {
                                    $query->where('customer_details.citygroup',$city);
                                }
                            })
                            ->whereNotIn('del_orders.status',['Cancel'])
                            ->whereNotIn('renewals.status',['Cancel'])
                            ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                            ->orderBy('renewals.start_date','Asc')
                            ->get();

        $noq5c_vdr_count = DB::table('renewals')
                            ->join('order_details','renewals.order_details_id','=','order_details.id')
                            ->join('vendor_products','order_details.vendor_product_id','=','vendor_products.id')
                            ->join('del_orders','renewals.collection_order_id','=','del_orders.order_id')
                            ->join('leads','renewals.lead_id','=','leads.id')
                            ->join('customer_details','leads.customer_id','=','customer_details.cust_id')                            
                            ->select(     
                                'order_details.id',
                                'order_details.product_rent as product_rent',                        
                                'vendor_products.product_rent_approved'
                            )
                            ->whereBetween('renewals.start_date',[$start_date,$end_date])                            
                            ->when($city,function($query,$city){
                                if($city != "All")
                                {
                                    $query->where('customer_details.citygroup',$city);
                                }
                            })
                            ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                            ->whereNotIn('order_details.vendor_id',[17])
                            ->whereNotIn('del_orders.status',['Cancel'])
                            ->whereNotIn('renewals.status',['Cancel'])
                            ->orderBy('renewals.start_date','Asc')
                            ->get();
        foreach($noq5c_vdr_count as $key=>$value)
        {
            $noq5c_vdr_count[$key]->product_rent = RenewalPickupController::fetchCrDrData($value->id,'R');
        }
        $overdue_count = DB::table('order_details')
                            ->join('del_orders','order_details.order_id','=','del_orders.order_id')
                            ->join('leads','del_orders.lead_id','=','leads.id')
                            ->join('customer_details','leads.customer_id','=','customer_details.cust_id')
                            ->select(
                                'order_details.id',
                                'order_details.product_rent as product_rent_amount',
                                'order_details.pickup_date as pickup_date',
                                'order_details.billing_unit'
                            )
                            ->whereNotIn('del_orders.status',['Cancel','Cust Rejected','Rejected'])
                            ->where('order_details.sale_rental','Rental')
                            ->whereIn('order_details.current_status',['Pending','Renewed'])                            
                            ->when($start_date,function($query)use($start_date,$end_date){
                                $query->where('order_details.pickup_date','<=',$end_date);
                            })
                            ->when($city,function($query,$city){
                                if($city != "All")
                                {
                                    $query->where('customer_details.citygroup',$city);
                                }
                            })
                            ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                            ->get();
        foreach($overdue_count as $key=>$value)
        {
            $overdue_count[$key]->product_rent_amount = RenewalPickupController::fetchCrDrData($value->id,'R');
            $pickup_date = date('Y-m',strtotime($end_date))."-".date('d',strtotime($value->pickup_date));
            $monthCount = RenewalPickupController::getBillingPeriod($pickup_date,$value->billing_unit,$end_date);
            $overdue_count[$key]->product_rent_amount = $overdue_count[$key]->product_rent_amount * $monthCount;
        }
        $data['renewal_rent_collected_online'] = 0;
        $data['renewal_rent_collected_offline'] = 0;
        $data['renewal_rent_collected_corporate'] = 0;
        $data['renewal_rent_collected_other'] = 0;
        $billingPaymentController = new BillingPaymentController();
        foreach($renewed_count as $key=>$value){
            $renewed_count[$key]->cash_amount = $billingPaymentController->fetchCrDrDataRE($value->renewal_id,"Cash");
            $renewed_count[$key]->online_amount = $billingPaymentController->fetchCrDrDataRE($value->renewal_id,"Online");
            if(in_array($value->lead_source,$this->online_arr)){
                $data['renewal_rent_collected_online'] = $data['renewal_rent_collected_online'] + $renewed_count[$key]->cash_amount + $renewed_count[$key]->online_amount;
            }
            elseif(in_array($value->lead_source,$this->offline_arr)){
                if($value->customer_source == 'Online'){
                    $data['renewal_rent_collected_online'] = $data['renewal_rent_collected_online'] + $renewed_count[$key]->cash_amount + $renewed_count[$key]->online_amount;
                }else{
                    $data['renewal_rent_collected_offline'] = $data['renewal_rent_collected_offline'] + $renewed_count[$key]->cash_amount + $renewed_count[$key]->online_amount;
                }
            }
            elseif(in_array($value->lead_source,$this->corporate_arr)){
                $data['renewal_rent_collected_corporate'] = $data['renewal_rent_collected_corporate'] + $renewed_count[$key]->cash_amount + $renewed_count[$key]->online_amount;
            }
            else{                
                $data['renewal_rent_collected_other'] = $data['renewal_rent_collected_other'] + $renewed_count[$key]->cash_amount + $renewed_count[$key]->online_amount;
            }
        }
        $data['due_rent'] = $overdue_count->sum('product_rent_amount');
        $data['renewal_rent_collected'] = $renewed_count->sum('cash_amount') + $renewed_count->sum('online_amount');
        $data['renewal_count_of_equipment'] = count($renewed_count);
        $data['vendor_payment_no_q5c_renewal'] = $noq5c_vdr_count->sum('product_rent_approved');
        $data['vendor_equipment_renewal'] = $noq5c_vdr_count->sum('product_rent');
        $data['vendor_payment_renewal'] = $renewed_count->sum('product_rent_approved');
        return $data;
    }

    public function new_orders_data($city,$start_date,$end_date)
    {
        $data['new_rent_collected_online'] = 0;
        $data['new_rent_collected_offline'] = 0;
        $data['new_rent_collected_corporate'] = 0;
        $data['new_rent_collected_other'] = 0;
        $rental_order_details = DB::table('order_details')
                                ->join('del_orders','del_orders.order_id','=','order_details.order_id')                                
                                ->join('leads','leads.id','=','del_orders.lead_id')
                                ->join('vendor_details','order_details.vendor_id','=','vendor_details.id')                                
                                ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                                ->join('vendor_products','order_details.vendor_product_id','=','vendor_products.id')
                                ->select(
                                    'order_details.id',
                                    'order_details.product_rent',
                                    'order_details.product_deposite',
                                    'order_details.transport',
                                    'leads.lead_source',
                                    'vendor_products.product_rent_approved as vendor_rent',
                                    'leads.customer_source')
                                ->where('order_details.sale_rental','Rental')
                                ->whereNotIn('order_details.current_status',['Cancel'])
                                ->whereNotIn('del_orders.status',['Cancel'])
                                ->whereBetween('order_details.creation_date',[$start_date,$end_date])
                                ->when($city,function($query,$city){
                                    if($city != "All")
                                    {
                                        $query->where('customer_details.citygroup',$city);
                                    }
                                })
                                ->get();
        foreach($rental_order_details as $key=>$value)
        {
            $rental_order_details[$key]->product_rent = RenewalPickupController::fetchCrDrData($value->id,'R');
            $rental_order_details[$key]->product_deposite = RenewalPickupController::fetchCrDrData($value->id,'D');
            $rental_order_details[$key]->transport = RenewalPickupController::fetchCrDrData($value->id,'T');

            if(in_array($value->lead_source,$this->online_arr)){
                $data['new_rent_collected_online'] = $data['new_rent_collected_online'] + $rental_order_details[$key]->product_rent;
            }
            elseif(in_array($value->lead_source,$this->offline_arr)){
                if($value->customer_source == 'Online'){
                    $data['new_rent_collected_online'] = $data['new_rent_collected_online'] + $rental_order_details[$key]->product_rent;
                }else{
                    $data['new_rent_collected_offline'] = $data['new_rent_collected_offline'] + $rental_order_details[$key]->product_rent;
                }
            }
            elseif(in_array($value->lead_source,$this->corporate_arr)){
                $data['new_rent_collected_corporate'] = $data['new_rent_collected_corporate'] + $rental_order_details[$key]->product_rent;
            }
            else{                
                $data['new_rent_collected_other'] = $data['new_rent_collected_other'] + $rental_order_details[$key]->product_rent;
            }
        }
        $no_q5c_order_details = DB::table('order_details')
                                ->join('vendor_details','order_details.vendor_id','=','vendor_details.id')                                
                                ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                                ->join('vendor_products','order_details.vendor_product_id','=','vendor_products.id')
                                ->select(
                                    'order_details.id',
                                    'order_details.product_rent as product_rent',
                                    'vendor_products.product_rent_approved as vendor_rent')
                                ->where('order_details.sale_rental','Rental')
                                ->whereNotIn('order_details.current_status',['Cancel'])
                                ->whereBetween('order_details.creation_date',[$start_date,$end_date])
                                ->when($city,function($query,$city){
                                    if($city != "All")
                                    {
                                        $query->where('customer_details.citygroup',$city);
                                    }
                                })
                                ->whereNotIn('order_details.vendor_id',[17])
                                ->get();
        foreach($no_q5c_order_details as $key=>$value)
        {
            $no_q5c_order_details[$key]->product_rent = RenewalPickupController::fetchCrDrData($value->id,'R');
        }                                
        $sale_order_details = DB::table('order_details')                     
                                ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                                ->select(
                                    'order_details.product_rent',
                                    'order_details.product_deposite',
                                    'order_details.transport',)
                                ->where('order_details.sale_rental','Sale')
                                ->whereNotIn('order_details.current_status',['Cancel'])
                                ->whereBetween('order_details.creation_date',[$start_date,$end_date])
                                ->when($city,function($query,$city){
                                    if($city != "All")
                                    {
                                        $query->where('customer_details.citygroup',$city);
                                    }
                                })
                                ->get();
        $data['new_rent_collected'] = $rental_order_details->sum('product_rent');
        $data['new_unit_rented'] = count($rental_order_details);
        $data['rental_transportation'] = $rental_order_details->sum('transport');
        $data['sales_transport'] = $sale_order_details->sum('transport');
        $data['sales_value'] = $sale_order_details->sum('product_rent');
        $data['vendor_payment_rent'] = $rental_order_details->sum('vendor_rent');
        $data['vendor_payment_no_q5c_rent'] = $no_q5c_order_details->sum('vendor_rent');
        $data['vendor_equipment_rent'] = $no_q5c_order_details->sum('vendor_rent');
        $data['new_rent_deposite'] = $rental_order_details->sum('product_deposite');
        return $data;
    }
    public function month_data($city,$month,$year)
    {
        if($monthly_records = DB::table('monthly_records')
        ->select(
            'monthly_records.*'
        )
        ->where('month',$month)
        ->where('year',$year)
        ->when($city,function($query,$city){
            if($city != "All")
            {
                $query->where('city',$city);
            }
        })
        ->exists())
        {
            $monthly_records = DB::table('monthly_records')
                                    ->select(
                                        'monthly_records.*'
                                    )
                                    ->where('month',$month)
                                    ->where('year',$year)
                                    ->when($city,function($query,$city){
                                        if($city != "All")
                                        {
                                            $query->where('city',$city);
                                        }
                                    })
                                    ->get();
            $data['transportation_expense'] = $monthly_records->sum('transportation_expense');
            $data['total_expense'] = $monthly_records->sum('total_expense');
            $data['google_spend_marketing'] = $monthly_records->sum('google_spend');
            $data['no_of_clicks'] = $monthly_records->sum('no_of_clicks');
            $data['impressions'] = $monthly_records->sum('google_impr');
            $data['justdial'] = $monthly_records->sum('justdial');
            $data['offline_marketing'] = $monthly_records->sum('offline_marketing');
            $data['value_added_service'] = $monthly_records->sum('value_added_services');
            $data['purchase_value'] = $monthly_records->sum('purchase_value');
        }
        else
        {
            $data['transportation_expense'] = 0;
            $data['total_expense'] = 0;
            $data['google_spend_marketing'] = 0;
            $data['no_of_clicks'] = 0;
            $data['impressions'] = 0;
            $data['justdial'] = 0;
            $data['offline_marketing'] = 0;
            $data['value_added_service'] = 0;
            $data['purchase_value'] = 0;
        }
        return $data;
    }

    public function monthly_customer_count($city,$start_date,$end_date)
    {
        $orderTypeNotIn = config('app.order_type');
        $renewed_count = DB::table('renewals')
                            ->join('order_details','renewals.order_details_id','=','order_details.id')
                            ->join('vendor_products','order_details.vendor_product_id','=','vendor_products.id')
                            ->join('del_orders','renewals.collection_order_id','=','del_orders.order_id')
                            ->join('leads','renewals.lead_id','=','leads.id')
                            ->join('customer_details','leads.customer_id','=','customer_details.cust_id')                            
                            ->select('order_details.customer_id')
                            ->distinct('order_details.customer_id')
                            ->whereBetween('renewals.start_date',[$start_date,$end_date])                            
                            ->when($city,function($query,$city){
                                if($city != "All")
                                {
                                    $query->where('customer_details.citygroup',$city);
                                }
                            })
                            ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                            ->get();
        $sale_customers = DB::table('order_details')
                ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                ->select('order_details.customer_id')
                ->distinct('order_details.customer_id')
                ->where('order_details.sale_rental','Sale')
                //->whereNotIn('order_details.current_status',['Cancel'])
                ->whereBetween('order_details.creation_date',[$start_date,$end_date])
                ->when($city,function($query,$city){
                    if($city != "All")
                    {
                        $query->where('customer_details.citygroup',$city);
                    }
                })
                ->get();
                

        $rental_customers = DB::table('order_details')
                ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                ->join('del_orders','del_orders.order_id','=','order_details.order_id')
                ->join('leads','leads.id','=','del_orders.lead_id') 
                ->select('order_details.customer_id','leads.lead_source','leads.customer_source')
                // ->distinct('order_details.customer_id')
                ->where('order_details.sale_rental','Rental')
                //->whereNotIn('order_details.current_status',['Cancel'])
                ->whereBetween('order_details.creation_date',[$start_date,$end_date])
                ->when($city,function($query,$city){
                    if($city != "All")
                    {
                        $query->where('customer_details.citygroup',$city);
                    }
                })
                ->get();
        // dd($rental_customers->groupBy('customer_id'));
        $rental_customers = $rental_customers->groupBy('customer_id');
        // dd($rental_customers);
        $new_customer_count = 0;
        $new_customer_count_online = 0;
        $new_customer_count_offline = 0;
        $new_customer_count_corporate = 0;
        $new_customer_other_count = 0;
        $old_start_date = date('Y-m-d',strtotime("-1 Month",strtotime($start_date)));
        $old_end_date = date('Y-m-d',strtotime("-1 Month",strtotime($end_date)));
        
        foreach($rental_customers as $key=>$value)
        {
            // if(DB::table('order_details')->select('customer_id')->distinct('customer_id')->where('order_details.customer_id',$key)->where('order_details.sale_rental','Rental')->whereNotIn('order_details.current_status',['Cancel'])->whereBetween('order_details.creation_date',[$old_start_date,$old_end_date])->exists())
            // {
            //     continue;
            // }
            // else
            // {
                if(in_array($value[0]->lead_source,$this->online_arr)){
                    $new_customer_count_online++;
                }
                elseif(in_array($value[0]->lead_source,$this->offline_arr)){
                    if($value[0]->customer_source == 'Online'){
                        $new_customer_count_online++;
                    }else{
                        $new_customer_count_offline++;
                    }
                }
                elseif(in_array($value[0]->lead_source,$this->corporate_arr)){
                    $new_customer_count_corporate++;
                }
                else{
                    $new_customer_other_count++;
                }
                // $lead_source = DB::table('del_orders')
                //     ->join('order_details.')
                //     ->join('leads','leads.order_id','=','leads.id')
                //     ->where('order_details.id',)
                $new_customer_count++;
            // }
        }
        // dd($new_customer_count_online,
        // $new_customer_count_offline,
        // $new_customer_count_corporate,
        // $new_customer_other_count);
        $data['sales_customer'] = count($sale_customers);
        $data['new_customer_rented'] = $new_customer_count;
        $data['new_customer_rented_online'] = $new_customer_count_online;
        $data['new_customer_rented_offline'] = $new_customer_count_offline;
        $data['new_customer_rented_corporate'] = $new_customer_count_corporate;
        $data['total_customer_rented'] = count($rental_customers);
        $data['total_renewed_customer'] = count($renewed_count);
        return $data;
    }

    public function addMonthlyRecord(Request $request)
    {
        $monthYear = explode("-",$request->get('month_year'));
        $year = $monthYear[0];
        $month = ltrim($monthYear[1],"0");

        // $insertData = [
        //     'month'=>$month,
        //     'year'=>$year,
        //     'value_added_services'=>$request->get('value_added_service'),
        //     'transportation_expense'=>$request->get('transport_expense'),
        //     'total_expense'=>$request->get('total_expense'),
        //     'justdial'=>$request->get('justdial'),
        //     'offline_marketing'=>$request->get('offline_marketing'),
        //     'purchase_value'=>$request->get('purchase_value'),
        //     'created_by'=>session('user_id')
        // ];
        MonthlyRecord::updateOrCreate([
            'month'=>$month,
            'year'=>$year,
        ],[
            'month'=>$month,
            'year'=>$year,
            'city'=>$request->get('record_city'),
            'value_added_services'=>$request->get('value_added_service'),
            'transportation_expense'=>$request->get('transport_expense'),
            'total_expense'=>$request->get('total_expense'),
            'justdial'=>$request->get('justdial'),
            'offline_marketing'=>$request->get('offline_marketing'),
            'purchase_value'=>$request->get('purchase_value'),
            'created_by'=>session('user_id')
        ]);
        return redirect()->to('monthly_records')->with('message','Record added successfully');

    }
    public function getNursingIncome($city,$start_date,$end_date){

        
        return DB::table('nursing_care')->when($city,function($query)use($city){if($city != "All")$query->where('city',$city);})->whereBetween('lead_date',[$start_date,$end_date])->pluck('charges')->sum();
    }
}
