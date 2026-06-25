<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\VendorRegister;
use App\Models\UserRegister;
use App\Models\UserLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\VendorManagement\VendorController;
use App\Http\Controllers\UserManagement\UserController;
use Mail;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function isLoggedIn()
    {
        $data = session('isLoggedIn');
        //print_r($data);      
        return $data;
    }
    public function dashboard()
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
            return redirect()->to($url);
        }

        $end_date_glob = date('Y-m-d');
        $start_date_glob = date('Y-m-d',strtotime("-7 days", strtotime($end_date_glob)));

        $city = 'All';
        if(request()->get('select_avg_city') == null || request()->get('select_avg_city') == ""){
            $city = 'All';
        }
        else{
            $city = request()->get('select_avg_city');
        }
        $data = $this->ordersCount('Week');
        $data += $this->leadsCount('Week',$start_date_glob,$end_date_glob,"All","stateWeek");
        $data += $this->q5cEquipmentCount('Week');
        $data += $this->vdrEquipmentCount('Week');
        $data += $this->leadOwner();
        $data += $this->avgAnualReport($city);
        // dd($data);
        return view('dashboard',$data);
    }
    public function avgAnualReport($city)
    {
        $today = date('d-m-Y');
        $year_rental_total_new = DB::table('order_details')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                ->select('order_details.product_rent')
                                ->whereBetween('order_details.creation_date',['2022-04-01',date('Y-m-d')])
                                ->whereNotIn('order_details.current_status',['Cancel'])
                                ->where('order_details.sale_rental','Rental')
                                ->when($city,function($query)use($city){
                                    if($city!="All"){
                                        $query->where('city',$city);
                                    }
                                })
                                ->get();
        $year_equip_count = count($year_rental_total_new->pluck('product_rent')->toArray());
        $year_rental_total_new = array_sum($year_rental_total_new->pluck('product_rent')->toArray());

        $year_rental_total_renew = DB::table('renewals')
                                ->join('order_details','order_details.id','=','renewals.order_details_id')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                ->select('renewals.cash_amount','online_amount')
                                ->whereBetween('renewals.start_date',['2022-04-01',date('Y-m-d')])
                                ->whereNotIn('renewals.status',['Cancel'])
                                ->when($city,function($query)use($city){
                                    if($city!="All"){
                                        $query->where('city',$city);
                                    }
                                })
                                ->get();
        $year_equip_count = $year_equip_count + count($year_rental_total_renew->toArray());
        $year_rental_total_renew = array_sum($year_rental_total_renew->pluck('cash_amount')->toArray()) +  array_sum($year_rental_total_renew->pluck('online_amount')->toArray());
        
        $year_rental_total = $year_rental_total_new + $year_rental_total_renew;
        
        $year_sale_total = DB::table('order_details')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                ->select('order_details.product_rent')
                                ->whereBetween('order_details.creation_date',['2022-04-01',date('Y-m-d')])
                                ->whereNotIn('order_details.current_status',['Cancel'])
                                ->where('order_details.sale_rental','Sale')
                                ->when($city,function($query)use($city){
                                    if($city!="All"){
                                        $query->where('city',$city);
                                    }
                                })
                                ->get();
        $year_sale_total = array_sum($year_sale_total->pluck('product_rent')->toArray());
        
        $year_transport_total = DB::table('order_details')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                ->select('order_details.transport')
                                ->whereBetween('order_details.creation_date',['2022-04-01',date('Y-m-d')])
                                //->whereNotIn('order_details.current_status',['Cancel'])
                                ->when($city,function($query)use($city){
                                    if($city!="All"){
                                        $query->where('city',$city);
                                    }
                                })
                                ->get();
        $year_transport_total = array_sum($year_transport_total->pluck('transport')->toArray());

        $year_rental_customer_count = DB::table('order_details')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                ->distinct('order_details.customer_id')
                                ->select('order_details.customer_id')
                                ->whereBetween('order_details.creation_date',['2022-04-01',date('Y-m-d')])
                                ->whereNotIn('order_details.current_status',['Cancel'])
                                ->where('order_details.sale_rental','Rental')
                                ->when($city,function($query)use($city){
                                    if($city!="All"){
                                        $query->where('city',$city);
                                    }
                                })
                                ->get();
        $year_rental_customer_count = count($year_rental_customer_count->toArray());

        $year_rental_customer_count_renew = DB::table('renewals')
                                ->join('order_details','order_details.id','=','renewals.order_details_id')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                ->distinct('order_details.customer_id')
                                ->select('order_details.customer_id')
                                ->whereBetween('renewals.start_date',['2022-04-01',date('Y-m-d')])
                                ->whereNotin('renewals.status',['Cancel'])
                                ->when($city,function($query)use($city){
                                    if($city!="All"){
                                        $query->where('city',$city);
                                    }
                                })
                                ->get();
        $year_rental_customer_count_renew = count($year_rental_customer_count_renew->toArray());
        $year_rental_customer_count = $year_rental_customer_count + $year_rental_customer_count_renew;

        $year_sale_customer_count = DB::table('order_details')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                ->distinct('order_details.customer_id')
                                ->select('order_details.customer_id')
                                ->whereBetween('order_details.creation_date',['2022-04-01',date('Y-m-d')])
                                ->whereNotIn('order_details.current_status',['Cancel'])
                                ->where('order_details.sale_rental','Sale')
                                ->when($city,function($query)use($city){
                                    if($city!="All"){
                                        $query->where('city',$city);
                                    }
                                })
                                ->get();
        $year_sale_customer_count = count($year_sale_customer_count->toArray());

        // $year_stop_requests = DB::table('del_orders')->select('order_id')->where('deliverypickup','Pick Up')->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('01-04-2022','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$today','%d-%m-%Y'))")])->whereNotIn('status',['Cancel'])->get();        
        // $year_stop_requests = count($year_stop_requests->toArray());

        // $year_new_order = DB::table('del_orders')->select('order_id')->where('deliverypickup','Delivery')->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('01-04-2022','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$today','%d-%m-%Y'))")])->whereNotIn('status',['Cancel'])->get();        
        // $year_new_order = count($year_new_order->toArray());

        $year_stop_requests = DB::table('pickups')
                                ->join('order_details','order_details.id','=','pickups.order_details_id')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                ->distinct('order_details.customer_id')
                                ->select('order_details.customer_id')
                                ->whereBetween('pickups.pickup_date',['2022-04-01',date('Y-m-d')])
                                ->whereNull('pickups.status')
                                ->when($city,function($query)use($city){
                                    if($city!="All"){
                                        $query->where('city',$city);
                                    }
                                })
                                ->get();
        $year_stop_requests = count($year_stop_requests->toArray());

        $year_new_order = DB::table('order_details')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                ->distinct('order_details.customer_id')
                                ->select('order_details.customer_id')
                                ->whereBetween('order_details.creation_date',['2022-04-01',date('Y-m-d')])
                                ->whereNotIn('order_details.current_status',['Cancel'])
                                ->where('order_details.sale_rental','Rental')
                                ->when($city,function($query)use($city){
                                    if($city!="All"){
                                        $query->where('city',$city);
                                    }
                                })
                                ->get();
        $year_new_order = count($year_new_order->toArray());

        $year_new_equip = DB::table('order_details')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                // ->distinct('order_details.product_id')
                                ->select('order_details.product_id')
                                ->whereBetween('order_details.creation_date',['2022-04-01',date('Y-m-d')])
                                ->whereNotIn('order_details.current_status',['Cancel'])
                                ->where('order_details.sale_rental','Rental')
                                ->when($city,function($query)use($city){
                                    if($city!="All"){
                                        $query->where('city',$city);
                                    }
                                })
                                ->get();
        $year_new_equip = count($year_new_equip->toArray());


        // Month
        $first_date = '01-'.date('m-Y');
        $first_date = date('Y-m-d',strtotime($first_date));
        $month_date = date('Y-m-d',strtotime($first_date));
        $first_date_order = date('d-m-Y',strtotime($first_date));

        $month_rental_total_new = DB::table('order_details')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                ->select('order_details.product_rent')
                                ->whereBetween('order_details.creation_date',[$first_date,date('Y-m-d')])
                                ->whereNotIn('order_details.current_status',['Cancel'])
                                ->where('order_details.sale_rental','Rental')
                                ->when($city,function($query)use($city){
                                    if($city!="All"){
                                        $query->where('city',$city);
                                    }
                                })
                                ->get();
        $month_equip_count = count($month_rental_total_new->pluck('product_rent')->toArray());
        $month_rental_total_new = array_sum($month_rental_total_new->pluck('product_rent')->toArray());

        $month_rental_total_renew = DB::table('renewals')
                                ->join('order_details','order_details.id','=','renewals.order_details_id')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                ->select('renewals.cash_amount','online_amount')
                                ->whereBetween('renewals.start_date',[$first_date,date('Y-m-d')])
                                ->whereNotIn('renewals.status',['Cancel'])
                                ->when($city,function($query)use($city){
                                    if($city!="All"){
                                        $query->where('city',$city);
                                    }
                                })
                                ->get();
        $month_equip_count = $month_equip_count + count($month_rental_total_renew->toArray());
        $month_rental_total_renew = array_sum($month_rental_total_renew->pluck('cash_amount')->toArray()) +  array_sum($month_rental_total_renew->pluck('online_amount')->toArray());
        
        $month_rental_total = $month_rental_total_new + $month_rental_total_renew;
        
        $month_sale_total = DB::table('order_details')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                ->select('order_details.product_rent')
                                ->whereBetween('order_details.creation_date',[$first_date,date('Y-m-d')])
                                ->whereNotIn('order_details.current_status',['Cancel'])
                                ->where('order_details.sale_rental','Sale')
                                ->when($city,function($query)use($city){
                                    if($city!="All"){
                                        $query->where('city',$city);
                                    }
                                })
                                ->get();
        $month_sale_total = array_sum($month_sale_total->pluck('product_rent')->toArray());
        
        $month_transport_total = DB::table('order_details')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                ->select('order_details.transport')
                                ->whereBetween('order_details.creation_date',[$first_date,date('Y-m-d')])
                                //->whereNotIn('order_details.current_status',['Cancel'])
                                ->when($city,function($query)use($city){
                                    if($city!="All"){
                                        $query->where('city',$city);
                                    }
                                })
                                ->get();
        $month_transport_total = array_sum($month_transport_total->pluck('transport')->toArray());

        $month_rental_customer_count = DB::table('order_details')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                ->distinct('order_details.customer_id')
                                ->select('order_details.customer_id')
                                ->whereBetween('order_details.creation_date',[$first_date,date('Y-m-d')])
                                ->whereNotIn('order_details.current_status',['Cancel'])
                                ->where('order_details.sale_rental','Rental')
                                ->when($city,function($query)use($city){
                                    if($city!="All"){
                                        $query->where('city',$city);
                                    }
                                })
                                ->get();
        $month_rental_customer_count = count($month_rental_customer_count->toArray());

        $month_rental_customer_count_renew = DB::table('renewals')
                                ->join('order_details','order_details.id','=','renewals.order_details_id')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                ->distinct('order_details.customer_id')
                                ->select('order_details.customer_id')
                                ->whereBetween('renewals.start_date',[$first_date,date('Y-m-d')])
                                ->whereNotin('renewals.status',['Cancel'])
                                ->when($city,function($query)use($city){
                                    if($city!="All"){
                                        $query->where('city',$city);
                                    }
                                })
                                ->get();
        $month_rental_customer_count_renew = count($month_rental_customer_count_renew->toArray());
        $month_rental_customer_count = $month_rental_customer_count + $month_rental_customer_count_renew;

        $month_sale_customer_count = DB::table('order_details')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                ->distinct('order_details.customer_id')
                                ->select('order_details.customer_id')
                                ->whereBetween('order_details.creation_date',[$first_date,date('Y-m-d')])
                                ->whereNotIn('order_details.current_status',['Cancel'])
                                ->where('order_details.sale_rental','Sale')
                                ->when($city,function($query)use($city){
                                    if($city!="All"){
                                        $query->where('city',$city);
                                    }
                                })
                                ->get();
        $month_sale_customer_count = count($month_sale_customer_count->toArray());

        // $month_stop_requests = DB::table('del_orders')->select('order_id')->where('deliverypickup','Pick Up')->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$first_date_order','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$today','%d-%m-%Y'))")])->whereNotIn('status',['Cancel'])->get();        
        // $month_stop_requests = count($month_stop_requests->toArray());

        // $month_new_order = DB::table('del_orders')->select('order_id')->where('deliverypickup','Delivery')->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$first_date_order','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$today','%d-%m-%Y'))")])->whereNotIn('status',['Cancel'])->get();        
        // $month_new_order = count($month_new_order->toArray());

        $month_stop_requests = DB::table('pickups')
                                ->join('order_details','order_details.id','=','pickups.order_details_id')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                ->distinct('order_details.customer_id')
                                ->select('order_details.customer_id')
                                ->whereBetween('pickups.pickup_date',[$first_date,date('Y-m-d')])
                                ->whereNull('pickups.status')
                                ->when($city,function($query)use($city){
                                    if($city!="All"){
                                        $query->where('city',$city);
                                    }
                                })
                                ->get();
        // dd($month_stop_requests);
        $month_stop_requests = count($month_stop_requests->toArray());

        $month_new_order = DB::table('order_details')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                ->distinct('order_details.customer_id')
                                ->select('order_details.customer_id')
                                ->whereBetween('order_details.creation_date',[$first_date,date('Y-m-d')])
                                ->whereNotIn('order_details.current_status',['Cancel'])
                                ->where('order_details.sale_rental','Rental')
                                ->when($city,function($query)use($city){
                                    if($city!="All"){
                                        $query->where('city',$city);
                                    }
                                })
                                ->get();
        $month_new_order = count($month_new_order->toArray());

        $month_new_equip = DB::table('order_details')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                // ->distinct('order_details.product_id')
                                ->select('order_details.product_id')
                                ->whereBetween('order_details.creation_date',[$first_date,date('Y-m-d')])
                                ->whereNotIn('order_details.current_status',['Cancel'])
                                ->where('order_details.sale_rental','Rental')
                                ->when($city,function($query)use($city){
                                    if($city!="All"){
                                        $query->where('city',$city);
                                    }
                                })
                                ->get();
        $month_new_equip = count($month_new_equip->toArray());

        // day
        $first_date = date('Y-m-d');
        $first_date_order = date('d-m-Y');

        $day_rental_total_new = DB::table('order_details')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                ->select('order_details.product_rent')
                                ->whereBetween('order_details.creation_date',[$first_date,date('Y-m-d')])
                                ->whereNotIn('order_details.current_status',['Cancel'])
                                ->where('order_details.sale_rental','Rental')
                                ->when($city,function($query)use($city){
                                    if($city!="All"){
                                        $query->where('city',$city);
                                    }
                                })
                                ->get();
        $day_equip_count = count($day_rental_total_new->pluck('product_rent')->toArray());
        $day_rental_total_new = array_sum($day_rental_total_new->pluck('product_rent')->toArray());

        $day_rental_total_renew = DB::table('renewals')
                                ->join('order_details','order_details.id','=','renewals.order_details_id')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                ->select('renewals.cash_amount','online_amount')
                                ->whereBetween('renewals.start_date',[$first_date,date('Y-m-d')])
                                ->whereNotIn('renewals.status',['Cancel'])
                                ->when($city,function($query)use($city){
                                    if($city!="All"){
                                        $query->where('city',$city);
                                    }
                                })
                                ->get();
        $day_equip_count = $day_equip_count + count($day_rental_total_renew->toArray());
        $day_rental_total_renew = array_sum($day_rental_total_renew->pluck('cash_amount')->toArray()) +  array_sum($day_rental_total_renew->pluck('online_amount')->toArray());
        
        $day_rental_total = $day_rental_total_new + $day_rental_total_renew;
        
        $day_sale_total = DB::table('order_details')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                ->select('order_details.product_rent')
                                ->whereBetween('order_details.creation_date',[$first_date,date('Y-m-d')])
                                ->whereNotIn('order_details.current_status',['Cancel'])
                                ->where('order_details.sale_rental','Sale')
                                ->when($city,function($query)use($city){
                                    if($city!="All"){
                                        $query->where('city',$city);
                                    }
                                })
                                ->get();
        $day_sale_total = array_sum($day_sale_total->pluck('product_rent')->toArray());
        
        $day_transport_total = DB::table('order_details')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                ->select('order_details.transport')
                                ->whereBetween('order_details.creation_date',[$first_date,date('Y-m-d')])
                                //->whereNotIn('order_details.current_status',['Cancel'])
                                ->when($city,function($query)use($city){
                                    if($city!="All"){
                                        $query->where('city',$city);
                                    }
                                })
                                ->get();
        $day_transport_total = array_sum($day_transport_total->pluck('transport')->toArray());

        $day_rental_customer_count = DB::table('order_details')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                ->distinct('order_details.customer_id')
                                ->select('order_details.customer_id')
                                ->whereBetween('order_details.creation_date',[$first_date,date('Y-m-d')])
                                ->whereNotIn('order_details.current_status',['Cancel'])
                                ->where('order_details.sale_rental','Rental')
                                ->when($city,function($query)use($city){
                                    if($city!="All"){
                                        $query->where('city',$city);
                                    }
                                })
                                ->get();
        $day_rental_customer_count = count($day_rental_customer_count->toArray());

        // $day_rental_customer_count_renew = DB::table('renewals')->join('order_details','order_details.id','=','renewals.order_details_id')->distinct('order_details.customer_id')->select('order_details.customer_id')->whereBetween('renewals.start_date',[$first_date,date('Y-m-d')])->whereNotin('renewals.status',['Cancel'])->get();
        // $day_rental_customer_count_renew = count($day_rental_customer_count_renew->toArray());
        // $day_rental_customer_count = $day_rental_customer_count + $day_rental_customer_count_renew;
	
	    $day_rental_customer_count_renew = DB::table('renewals')
                                ->join('order_details','order_details.id','=','renewals.order_details_id')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                ->distinct('order_details.customer_id')
                                ->select('order_details.customer_id')
                                ->whereBetween('renewals.start_date',[$first_date,date('Y-m-d')])
                                ->whereNotin('renewals.status',['Cancel'])
                                ->when($city,function($query)use($city){
                                    if($city!="All"){
                                        $query->where('city',$city);
                                    }
                                })
                                ->get();
        $day_rental_customer_count_renew = count($day_rental_customer_count_renew->toArray());
        $day_rental_customer_count = $day_rental_customer_count + $day_rental_customer_count_renew;

        $day_sale_customer_count = DB::table('order_details')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                ->distinct('order_details.customer_id')
                                ->select('order_details.customer_id')
                                ->whereBetween('order_details.creation_date',[$first_date,date('Y-m-d')])
                                ->whereNotIn('order_details.current_status',['Cancel'])
                                ->where('order_details.sale_rental','Sale')
                                ->when($city,function($query)use($city){
                                    if($city!="All"){
                                        $query->where('city',$city);
                                    }
                                })
                                ->get();
        $day_sale_customer_count = count($day_sale_customer_count->toArray());

        // $day_stop_requests = DB::table('del_orders')->select('order_id')->where('deliverypickup','Pick Up')->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$first_date_order','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$today','%d-%m-%Y'))")])->whereNotIn('status',['Cancel'])->get();        
        // $day_stop_requests = count($day_stop_requests->toArray());

        // $day_new_order = DB::table('del_orders')->select('order_id')->where('deliverypickup','Delivery')->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$first_date_order','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$today','%d-%m-%Y'))")])->whereNotIn('status',['Cancel'])->get();        
        // $day_new_order = count($day_new_order->toArray());

        $day_stop_requests = DB::table('pickups')
                                ->join('order_details','order_details.id','=','pickups.order_details_id')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                ->distinct('order_details.customer_id')
                                ->select('order_details.customer_id')
                                ->whereBetween('pickups.pickup_date',[$first_date,date('Y-m-d')])
                                ->whereNull('pickups.status')
                                ->when($city,function($query)use($city){
                                    if($city!="All"){
                                        $query->where('city',$city);
                                    }
                                })
                                ->get();
        $day_stop_requests = count($day_stop_requests->toArray());

        $day_new_order = DB::table('order_details')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                ->distinct('order_details.customer_id')
                                ->select('order_details.customer_id')
                                ->whereBetween('order_details.creation_date',[$first_date,date('Y-m-d')])
                                ->whereNotIn('order_details.current_status',['Cancel'])
                                ->where('order_details.sale_rental','Rental')
                                ->when($city,function($query)use($city){
                                    if($city!="All"){
                                        $query->where('city',$city);
                                    }
                                })
                                ->get();
        $day_new_order = count($day_new_order->toArray());

        $day_new_equip = DB::table('order_details')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                // ->distinct('order_details.product_id')
                                ->select('order_details.product_id')
                                ->whereBetween('order_details.creation_date',[$first_date,date('Y-m-d')])
                                ->whereNotIn('order_details.current_status',['Cancel'])
                                ->where('order_details.sale_rental','Rental')
                                ->when($city,function($query)use($city){
                                    if($city!="All"){
                                        $query->where('city',$city);
                                    }
                                })
                                ->get();
        $day_new_equip = count($day_new_equip->toArray());

        $year_days = Carbon::parse('2022-04-01');
        $now = Carbon::now();
        $year_days = $year_days->diffInDays($now);
        $year_days ++;
        $month_days = Carbon::parse($month_date);
        $now = Carbon::now();
        $month_days = $month_days->diffInDays($now);
        $month_days ++;
        // dd($month_days);

        $avgAnualReport['ytd_rental_total'] = $year_rental_total / $year_days;
        $avgAnualReport['ytd_rental_total_new'] = $year_rental_total_new / $year_days;
        $avgAnualReport['ytd_rental_total_renew'] = $year_rental_total_renew / $year_days;
        $avgAnualReport['ytd_sale_total'] = $year_sale_total / $year_days;
        $avgAnualReport['ytd_trans_collected'] = $year_transport_total / $year_days;
        $avgAnualReport['ytd_total_no_of_equip'] = $year_equip_count / $year_days;
        $avgAnualReport['ytd_rental_cust_count'] = $year_rental_customer_count / $year_days;
        $avgAnualReport['ytd_sale_cust_count'] = $year_sale_customer_count / $year_days;
        $avgAnualReport['ytd_stop_req'] = $year_stop_requests / $year_days;
        $avgAnualReport['ytd_new_rent'] = $year_new_order / $year_days;
        $avgAnualReport['ytd_new_equip'] = $year_new_equip / $year_days;

        $avgAnualReport['mtd_rental_total'] = $month_rental_total / $month_days;
        $avgAnualReport['mtd_rental_total_new'] = $month_rental_total_new / $month_days;
        $avgAnualReport['mtd_rental_total_renew'] = $month_rental_total_renew / $month_days;
        $avgAnualReport['mtd_sale_total'] = $month_sale_total / $month_days;
        $avgAnualReport['mtd_trans_collected'] = $month_transport_total / $month_days;
        $avgAnualReport['mtd_total_no_of_equip'] = $month_equip_count / $month_days;
        $avgAnualReport['mtd_rental_cust_count'] = $month_rental_customer_count / $month_days;
        $avgAnualReport['mtd_sale_cust_count'] = $month_sale_customer_count / $month_days;
        $avgAnualReport['mtd_stop_req'] = $month_stop_requests / $month_days;
        $avgAnualReport['mtd_new_rent'] = $month_new_order / $month_days;
        $avgAnualReport['mtd_new_equip'] = $month_new_equip / $month_days;

        $avgAnualReport['td_rental_total'] = $day_rental_total;
        $avgAnualReport['td_rental_total_new'] = $day_rental_total_new;
        $avgAnualReport['td_rental_total_renew'] = $day_rental_total_renew;
        $avgAnualReport['td_sale_total'] = $day_sale_total;
        $avgAnualReport['td_trans_collected'] = $day_transport_total;
        $avgAnualReport['td_total_no_of_equip'] = $day_equip_count;
        $avgAnualReport['td_rental_cust_count'] = $day_rental_customer_count;
        $avgAnualReport['td_sale_cust_count'] = $day_sale_customer_count;
        $avgAnualReport['td_stop_req'] = $day_stop_requests;
        $avgAnualReport['td_new_rent'] = $day_new_order;
        $avgAnualReport['td_new_equip'] = $day_new_equip;
        // dd($avgAnualReport);
        return $avgAnualReport;
    }
    // public function avgAnualReport()
    // {
    //     $today = date('d-m-Y');
    //     $year_rental_total_new = DB::table('order_details')->select('product_rent')->whereBetween('creation_date',['2022-04-01',date('Y-m-d')])->whereNotIn('current_status',['Cancel'])->where('sale_rental','Rental')->get();
    //     $year_equip_count = count($year_rental_total_new->pluck('product_rent')->toArray());
    //     $year_rental_total_new = array_sum($year_rental_total_new->pluck('product_rent')->toArray());

    //     $year_rental_total_renew = DB::table('renewals')->select('cash_amount','online_amount')->whereBetween('start_date',['2022-04-01',date('Y-m-d')])->whereNotIn('status',['Cancel'])->get();
    //     $year_equip_count = $year_equip_count + count($year_rental_total_renew->toArray());
    //     $year_rental_total_renew = array_sum($year_rental_total_renew->pluck('cash_amount')->toArray()) +  array_sum($year_rental_total_renew->pluck('online_amount')->toArray());
        
    //     $year_rental_total = $year_rental_total_new + $year_rental_total_renew;
        
    //     $year_sale_total = DB::table('order_details')->select('product_rent')->whereBetween('creation_date',['2022-04-01',date('Y-m-d')])->whereNotIn('current_status',['Cancel'])->where('sale_rental','Sale')->get();
    //     $year_sale_total = array_sum($year_sale_total->pluck('product_rent')->toArray());
        
    //     $year_transport_total = DB::table('order_details')->select('transport')->whereBetween('creation_date',['2022-04-01',date('Y-m-d')])->whereNotIn('current_status',['Cancel'])->get();
    //     $year_transport_total = array_sum($year_transport_total->pluck('transport')->toArray());

    //     $year_rental_customer_count = DB::table('order_details')->distinct('customer_id')->select('customer_id')->whereBetween('creation_date',['2022-04-01',date('Y-m-d')])->whereNotIn('current_status',['Cancel'])->where('sale_rental','Rental')->get();
    //     $year_rental_customer_count = count($year_rental_customer_count->toArray());

    //     $year_sale_customer_count = DB::table('order_details')->distinct('customer_id')->select('customer_id')->whereBetween('creation_date',['2022-04-01',date('Y-m-d')])->whereNotIn('current_status',['Cancel'])->where('sale_rental','Sale')->get();
    //     $year_sale_customer_count = count($year_sale_customer_count->toArray());

    //     $year_stop_requests = DB::table('del_orders')->select('order_id')->where('deliverypickup','Pick Up')->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('01-04-2022','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$today','%d-%m-%Y'))")])->whereNotIn('status',['Cancel'])->get();        
    //     $year_stop_requests = count($year_stop_requests->toArray());

    //     $year_new_order = DB::table('del_orders')->select('order_id')->where('deliverypickup','Delivery')->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('01-04-2022','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$today','%d-%m-%Y'))")])->whereNotIn('status',['Cancel'])->get();        
    //     $year_new_order = count($year_new_order->toArray());


    //     // Month
    //     $first_date = '01-'.date('m-Y');
    //     $first_date = date('Y-m-d',strtotime($first_date));
    //     $month_date = date('Y-m-d',strtotime($first_date));
    //     $first_date_order = date('d-m-Y',strtotime($first_date));

    //     $month_rental_total_new = DB::table('order_details')->select('product_rent')->whereBetween('creation_date',[$first_date,date('Y-m-d')])->whereNotIn('current_status',['Cancel'])->where('sale_rental','Rental')->get();
    //     $month_equip_count = count($month_rental_total_new->pluck('product_rent')->toArray());
    //     $month_rental_total_new = array_sum($month_rental_total_new->pluck('product_rent')->toArray());

    //     $month_rental_total_renew = DB::table('renewals')->select('cash_amount','online_amount')->whereBetween('start_date',[$first_date,date('Y-m-d')])->whereNotIn('status',['Cancel'])->get();
    //     $month_equip_count = $month_equip_count + count($month_rental_total_renew->toArray());
    //     $month_rental_total_renew = array_sum($month_rental_total_renew->pluck('cash_amount')->toArray()) +  array_sum($month_rental_total_renew->pluck('online_amount')->toArray());
        
    //     $month_rental_total = $month_rental_total_new + $month_rental_total_renew;
        
    //     $month_sale_total = DB::table('order_details')->select('product_rent')->whereBetween('creation_date',[$first_date,date('Y-m-d')])->whereNotIn('current_status',['Cancel'])->where('sale_rental','Sale')->get();
    //     $month_sale_total = array_sum($month_sale_total->pluck('product_rent')->toArray());
        
    //     $month_transport_total = DB::table('order_details')->select('transport')->whereBetween('creation_date',[$first_date,date('Y-m-d')])->whereNotIn('current_status',['Cancel'])->get();
    //     $month_transport_total = array_sum($month_transport_total->pluck('transport')->toArray());

    //     $month_rental_customer_count = DB::table('order_details')->distinct('customer_id')->select('customer_id')->whereBetween('creation_date',[$first_date,date('Y-m-d')])->whereNotIn('current_status',['Cancel'])->where('sale_rental','Rental')->get();
    //     $month_rental_customer_count = count($month_rental_customer_count->toArray());

    //     $month_sale_customer_count = DB::table('order_details')->distinct('customer_id')->select('customer_id')->whereBetween('creation_date',[$first_date,date('Y-m-d')])->whereNotIn('current_status',['Cancel'])->where('sale_rental','Sale')->get();
    //     $month_sale_customer_count = count($month_sale_customer_count->toArray());

    //     $month_stop_requests = DB::table('del_orders')->select('order_id')->where('deliverypickup','Pick Up')->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$first_date_order','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$today','%d-%m-%Y'))")])->whereNotIn('status',['Cancel'])->get();        
    //     $month_stop_requests = count($month_stop_requests->toArray());

    //     $month_new_order = DB::table('del_orders')->select('order_id')->where('deliverypickup','Delivery')->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$first_date_order','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$today','%d-%m-%Y'))")])->whereNotIn('status',['Cancel'])->get();        
    //     $month_new_order = count($month_new_order->toArray());
        

    //     // day
    //     $first_date = date('Y-m-d');
    //     $first_date_order = date('d-m-Y');

    //     $day_rental_total_new = DB::table('order_details')->select('product_rent')->whereBetween('creation_date',[$first_date,date('Y-m-d')])->whereNotIn('current_status',['Cancel'])->where('sale_rental','Rental')->get();
    //     $day_equip_count = count($day_rental_total_new->pluck('product_rent')->toArray());
    //     $day_rental_total_new = array_sum($day_rental_total_new->pluck('product_rent')->toArray());

    //     $day_rental_total_renew = DB::table('renewals')->select('cash_amount','online_amount')->whereBetween('start_date',[$first_date,date('Y-m-d')])->whereNotIn('status',['Cancel'])->get();
    //     $day_equip_count = $day_equip_count + count($day_rental_total_renew->toArray());
    //     $day_rental_total_renew = array_sum($day_rental_total_renew->pluck('cash_amount')->toArray()) +  array_sum($day_rental_total_renew->pluck('online_amount')->toArray());
        
    //     $day_rental_total = $day_rental_total_new + $day_rental_total_renew;
        
    //     $day_sale_total = DB::table('order_details')->select('product_rent')->whereBetween('creation_date',[$first_date,date('Y-m-d')])->whereNotIn('current_status',['Cancel'])->where('sale_rental','Sale')->get();
    //     $day_sale_total = array_sum($day_sale_total->pluck('product_rent')->toArray());
        
    //     $day_transport_total = DB::table('order_details')->select('transport')->whereBetween('creation_date',[$first_date,date('Y-m-d')])->whereNotIn('current_status',['Cancel'])->get();
    //     $day_transport_total = array_sum($day_transport_total->pluck('transport')->toArray());

    //     $day_rental_customer_count = DB::table('order_details')->distinct('customer_id')->select('customer_id')->whereBetween('creation_date',[$first_date,date('Y-m-d')])->whereNotIn('current_status',['Cancel'])->where('sale_rental','Rental')->get();
    //     $day_rental_customer_count = count($day_rental_customer_count->toArray());

    //     $day_sale_customer_count = DB::table('order_details')->distinct('customer_id')->select('customer_id')->whereBetween('creation_date',[$first_date,date('Y-m-d')])->whereNotIn('current_status',['Cancel'])->where('sale_rental','Sale')->get();
    //     $day_sale_customer_count = count($day_sale_customer_count->toArray());

    //     $day_stop_requests = DB::table('del_orders')->select('order_id')->where('deliverypickup','Pick Up')->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$first_date_order','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$today','%d-%m-%Y'))")])->whereNotIn('status',['Cancel'])->get();        
    //     $day_stop_requests = count($day_stop_requests->toArray());

    //     $day_new_order = DB::table('del_orders')->select('order_id')->where('deliverypickup','Delivery')->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$first_date_order','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$today','%d-%m-%Y'))")])->whereNotIn('status',['Cancel'])->get();        
    //     $day_new_order = count($day_new_order->toArray());

    //     $year_days = Carbon::parse('2022-04-01');
    //     $now = Carbon::now();
    //     $year_days = $year_days->diffInDays($now);
    //     $month_days = Carbon::parse($month_date);
    //     $now = Carbon::now();
    //     $month_days = $month_days->diffInDays($now);
    //     // dd($month_days);

    //     $avgAnualReport['ytd_rental_total'] = $year_rental_total / $year_days;
    //     $avgAnualReport['ytd_sale_total'] = $year_sale_total / $year_days;
    //     $avgAnualReport['ytd_trans_collected'] = $year_transport_total / $year_days;
    //     $avgAnualReport['ytd_total_no_of_equip'] = $year_equip_count / $year_days;
    //     $avgAnualReport['ytd_rental_cust_count'] = $year_rental_customer_count / $year_days;
    //     $avgAnualReport['ytd_sale_cust_count'] = $year_sale_customer_count / $year_days;
    //     $avgAnualReport['ytd_stop_req'] = $year_stop_requests / $year_days;
    //     $avgAnualReport['ytd_new_rent'] = $year_new_order / $year_days;

    //     $avgAnualReport['mtd_rental_total'] = $month_rental_total / $month_days;
    //     $avgAnualReport['mtd_sale_total'] = $month_sale_total / $month_days;
    //     $avgAnualReport['mtd_trans_collected'] = $month_transport_total / $month_days;
    //     $avgAnualReport['mtd_total_no_of_equip'] = $month_equip_count / $month_days;
    //     $avgAnualReport['mtd_rental_cust_count'] = $month_rental_customer_count / $month_days;
    //     $avgAnualReport['mtd_sale_cust_count'] = $month_sale_customer_count / $month_days;
    //     $avgAnualReport['mtd_stop_req'] = $month_stop_requests / $month_days;
    //     $avgAnualReport['mtd_new_rent'] = $month_new_order / $month_days;

    //     $avgAnualReport['td_rental_total'] = $day_rental_total;
    //     $avgAnualReport['td_sale_total'] = $day_sale_total;
    //     $avgAnualReport['td_trans_collected'] = $day_transport_total;
    //     $avgAnualReport['td_total_no_of_equip'] = $day_equip_count;
    //     $avgAnualReport['td_rental_cust_count'] = $day_rental_customer_count;
    //     $avgAnualReport['td_sale_cust_count'] = $day_sale_customer_count;
    //     $avgAnualReport['td_stop_req'] = $day_stop_requests;
    //     $avgAnualReport['td_new_rent'] = $day_new_order;
    //     return $avgAnualReport;
    // }
    public function ordersCount($filter)
    {
        $orderTypeNotIn = config('app.order_type');        
        $orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";

        $data['order_filter'] = $filter;
        $data['lead_filter'] = $filter;
        if($filter == 'All')
        {
            $data['xAxis'] = 'Year';
            if(session('role') == 'superuser')
            {
                $orders = DB::select("SELECT * FROM del_orders WHERE status != 'Closed' AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) ORDER BY order_id ASC");
            }
            elseif(session('role') == 'user')
            {
                $user_id = session('user_id');
                $orders = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE del_orders.status != 'Closed' AND leads.id = del_orders.lead_id AND leads.lead_owner = $user_id AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) ORDER BY del_orders.order_id ASC");
            }

            $data['orders'] = json_decode(json_encode($orders),true);
            if(isset($data['orders'][0]))
            {
                $data['start_year'] = date('Y',strtotime($data['orders'][0]['DelDate']));
                $data['end_year'] = date('Y');
                $data['period_orders'] = array();
                $data['incomplete'] = array();
                $data['completed'] = array();
                for($year=$data['start_year']; $year<=$data['end_year']; $year++)
                {
                    $start_date = '01-01-'.$year;
                    $end_date = '31-12-'.$year;
                    array_push($data['period_orders'],$year);
                    if(session('role') == 'superuser')
                    {
                        $foryearin = DB::select("SELECT DISTINCT del_orders.* FROM del_orders WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) AND del_orders.status IN ('Assigned','Pending','Accepted','InProgress')");
                        $foryearcom = DB::select("SELECT DISTINCT del_orders.* FROM del_orders WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) AND del_orders.status IN ('Picked up','Collected','Delivered')");
                    }
                    elseif(session('role') == 'user')
                    {
                        $user_id = session('user_id');
                        $foryearin = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) AND del_orders.status IN ('Assigned','Pending','Accepted','InProgress') AND del_orders.lead_id = leads.id AND leads.lead_owner = $user_id");
                        $foryearcom = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) AND del_orders.status IN ('Picked up','Collected','Delivered') AND del_orders.lead_id = leads.id AND leads.lead_owner = $user_id");
                    }
                    $foryears = json_decode(json_encode($foryearin), true);
                    if(isset($foryears[0]))
                    {
                        array_push($data['incomplete'],count($foryears));
                    }
                    else
                    {
                        array_push($data['incomplete'],0);
                    }
    
                    $foryearscom = json_decode(json_encode($foryearcom), true);
                    if(isset($foryearscom[0]))
                    {
                        array_push($data['completed'],count($foryearscom));
                    }
                    else
                    {
                        array_push($data['completed'],0);
                    }
                    //array_push($data['completed'],count($foryearscom));
                }
            }
            else
            {
                $data['period_orders'] = array();
                $data['incomplete'] = array();
                $data['completed'] = array();
                array_push($data['period_orders'],0);
                array_push($data['incomplete'],0);
                array_push($data['completed'],0);
            }
        }
        elseif($filter == 'Year')
        {
            $year = date('Y');
            $data['xAxis'] = 'Month of '.$year;
            $start_date = '01-01-'.$year;
            $end_date = '31-12-'.$year;
            if(session('role') == 'superuser')
            {
                $orders = DB::select("SELECT * FROM del_orders WHERE status != 'Closed' AND STR_TO_DATE(DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) ORDER BY order_id ASC");
            }
            elseif(session('role') == 'user')
            {
                $user_id = session('user_id');
                $orders = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE del_orders.status != 'Closed' AND STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) AND leads.id = del_orders.lead_id AND leads.lead_owner = $user_id ORDER BY del_orders.order_id ASC");
            }

            $data['orders'] = json_decode(json_encode($orders),true);
            if(isset($data['orders'][0]))
            {
                // $data['start_year'] = date('Y',strtotime($data['orders'][0]['DelDate']));
                // $data['end_year'] = date('Y');
                $data['start_month'] = $start_date;
                $data['end_month'] = $end_date;
                $data['period_orders'] = array();
                $data['incomplete'] = array();
                $data['completed'] = array();
                for($month=1; $month<=12; $month++)
                {
                    $start_date = '01-'.$month.'-'.$year;
                    $end_date = '31-'.$month.'-'.$year;
                    $monthName = date('F',mktime(0, 0, 0, $month, 10));
                    array_push($data['period_orders'],$month);
                    if(session('role') == 'superuser')
                    {
                        $foryearin = DB::select("SELECT DISTINCT del_orders.* FROM del_orders WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) AND del_orders.status IN ('Assigned','Pending','Accepted','InProgress')");
                        $foryearcom = DB::select("SELECT DISTINCT del_orders.* FROM del_orders WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) AND del_orders.status IN ('Picked up','Collected','Delivered')");
                    }
                    elseif(session('role') == 'user')
                    {
                        $user_id = session('user_id');
                        $foryearin = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) AND del_orders.status IN ('Assigned','Pending','Accepted','InProgress') AND del_orders.lead_id = leads.id AND leads.lead_owner = $user_id");
                        $foryearcom = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) AND del_orders.status IN ('Picked up','Collected','Delivered') AND del_orders.lead_id = leads.id AND leads.lead_owner = $user_id");
                    }
                    $foryears = json_decode(json_encode($foryearin), true);
                    if(isset($foryears[0]))
                    {
                        array_push($data['incomplete'],count($foryears));
                    }
                    else
                    {
                        array_push($data['incomplete'],0);
                    }
    
                    $foryearscom = json_decode(json_encode($foryearcom), true);
                    if(isset($foryearscom[0]))
                    {
                        array_push($data['completed'],count($foryearscom));
                    }
                    else
                    {
                        array_push($data['completed'],0);
                    }
                    //array_push($data['completed'],count($foryearscom));
                }
            }
            else
            {
                $data['period_orders'] = array();
                $data['incomplete'] = array();
                $data['completed'] = array();
                array_push($data['period_orders'],0);
                array_push($data['incomplete'],0);
                array_push($data['completed'],0);
            }
        }
        elseif($filter == 'Month')
        {
            $data['xAxis'] = 'Days of '.date('M');
            $year = date('Y');
            $month = date('m');
            $last_day = date('t');
            $start_date = '01-'.$month.'-'.$year;
            $end_date = $last_day.'-'.$month.'-'.$year;
            if(session('role') == 'superuser')
            {
                $orders = DB::select("SELECT * FROM del_orders WHERE status != 'Closed' AND STR_TO_DATE(DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) ORDER BY order_id ASC");
            }
            elseif(session('role') == 'user')
            {
                $user_id = session('user_id');
                $orders = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE del_orders.status != 'Closed' AND STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) AND leads.id = del_orders.lead_id AND leads.lead_owner = $user_id ORDER BY del_orders.order_id ASC");
            }

            $data['orders'] = json_decode(json_encode($orders),true);
            if(isset($data['orders'][0]))
            {
                $data['start_date'] = $start_date;
                $data['end_date'] = $end_date;
                $data['period_orders'] = array();
                $data['incomplete'] = array();
                $data['completed'] = array();
                for($day=1; $day<=$last_day; $day++)
                {
                    $start_date = $day.'-'.$month.'-'.$year;
                    $end_date = $day.'-'.$month.'-'.$year;
                    array_push($data['period_orders'],$day);
                    if(session('role') == 'superuser')
                    {
                        $foryearin = DB::select("SELECT DISTINCT del_orders.* FROM del_orders WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) AND del_orders.status IN ('Assigned','Pending','Accepted','InProgress')");
                        $foryearcom = DB::select("SELECT DISTINCT del_orders.* FROM del_orders WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) AND del_orders.status IN ('Picked up','Collected','Delivered')");
                    }
                    elseif(session('role') == 'user')
                    {
                        $user_id = session('user_id');
                        $foryearin = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) AND del_orders.status IN ('Assigned','Pending','Accepted','InProgress') AND del_orders.lead_id = leads.id AND leads.lead_owner = $user_id");
                        $foryearcom = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) AND del_orders.status IN ('Picked up','Collected','Delivered') AND del_orders.lead_id = leads.id AND leads.lead_owner = $user_id");
                    }
                    $foryears = json_decode(json_encode($foryearin), true);
                    if(isset($foryears[0]))
                    {
                        array_push($data['incomplete'],count($foryears));
                    }
                    else
                    {
                        array_push($data['incomplete'],0);
                    }
    
                    $foryearscom = json_decode(json_encode($foryearcom), true);
                    if(isset($foryearscom[0]))
                    {
                        array_push($data['completed'],count($foryearscom));
                    }
                    else
                    {
                        array_push($data['completed'],0);
                    }
                    //array_push($data['completed'],count($foryearscom));
                }
            }
            else
            {
                $data['period_orders'] = array();
                $data['incomplete'] = array();
                $data['completed'] = array();
                array_push($data['period_orders'],0);
                array_push($data['incomplete'],0);
                array_push($data['completed'],0);
            }
        }
        elseif($filter == 'Week')
        {
            $year = date('Y');
            $month = date('m');
            $data['xAxis'] = 'Days';
            // $last_day = date('t');
            // $start_date = '01-'.$month.'-'.$year;
            // $end_date = $last_day.'-'.$month.'-'.$year;
            $end_date_glob = date('d-m-Y');
            $start_date_glob = date('d-m-Y',strtotime("-7 days", strtotime($end_date_glob)));
            $end_day = date('d');
            $start_day = date('d',strtotime("-7 days", strtotime($end_date_glob)));
            if(session('role') == 'superuser')
            {
                $orders = DB::select("SELECT * FROM del_orders WHERE status != 'Closed' AND STR_TO_DATE(DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date_glob','%d-%m-%Y') AND STR_TO_DATE('$end_date_glob','%d-%m-%Y') AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) ORDER BY order_id ASC");
            }
            elseif(session('role') == 'user')
            {
                $user_id = session('user_id');
                $orders = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE del_orders.status != 'Closed' AND STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date_glob','%d-%m-%Y') AND STR_TO_DATE('$end_date_glob','%d-%m-%Y') AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) AND leads.id = del_orders.lead_id AND leads.lead_owner = $user_id ORDER BY del_orders.order_id ASC");
            }

            $data['orders'] = json_decode(json_encode($orders),true);
            if(isset($data['orders'][0]))
            {
                $data['start_date'] = $start_date_glob;
                $data['end_date'] = $end_date_glob;
                $data['period_orders'] = array();
                $data['period_date'] = array();
                $data['incomplete'] = array();
                $data['completed'] = array();
                // for($day=$start_day; $day<=$end_day; $day++)
                // {
                //     $start_date = $day.'-'.$month.'-'.$year;
                //     $end_date = $day.'-'.$month.'-'.$year;
                // echo "End Date: ".$end_date_glob;
                for($date = date('Y-m-d',strtotime($start_date_glob)); date('Y-m-d',strtotime($date)) <= date('Y-m-d',strtotime($end_date_glob)); $date = date('Y-m-d',strtotime("+1 days", strtotime($date))))
                { 
                    // echo "Date: ".$date;
                    // $start_date = $year.'-'.$month.'-'.$day;
                    $start_date = date('d-m-Y',strtotime($date));
                    $end_date = date('d-m-Y',strtotime($date));
                    $day = date('d',strtotime($start_date));
                    array_push($data['period_orders'],$day);
                    array_push($data['period_date'],$date);
                    if(session('role') == 'superuser')
                    {
                        $foryearin = DB::select("SELECT DISTINCT del_orders.* FROM del_orders WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) AND del_orders.status IN ('Assigned','Pending','Accepted','InProgress')");
                        $foryearcom = DB::select("SELECT DISTINCT del_orders.* FROM del_orders WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) AND del_orders.status IN ('Picked up','Collected','Delivered')");
                    }
                    elseif(session('role') == 'user')
                    {
                        $user_id = session('user_id');
                        $foryearin = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) AND del_orders.status IN ('Assigned','Pending','Accepted','InProgress') AND del_orders.lead_id = leads.id AND leads.lead_owner = $user_id");
                        $foryearcom = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) AND del_orders.status IN ('Picked up','Collected','Delivered') AND del_orders.lead_id = leads.id AND leads.lead_owner = $user_id");
                    }
                    $foryears = json_decode(json_encode($foryearin), true);
                    if(isset($foryears[0]))
                    {
                        array_push($data['incomplete'],count($foryears));
                    }
                    else
                    {
                        array_push($data['incomplete'],0);
                    }
    
                    $foryearscom = json_decode(json_encode($foryearcom), true);
                    if(isset($foryearscom[0]))
                    {
                        array_push($data['completed'],count($foryearscom));
                    }
                    else
                    {
                        array_push($data['completed'],0);
                    }
                    //array_push($data['completed'],count($foryearscom));
                }
            }
            else
            {
                $data['period_orders'] = array();
                $data['incomplete'] = array();
                $data['completed'] = array();
                array_push($data['period_orders'],0);
                array_push($data['incomplete'],0);
                array_push($data['completed'],0);
            }
        }
        $data['total_orders'] = count($data['orders']);
        
        $all_pickups = 0;
        $all_deliveries = 0;
        $all_collections = 0;

        $completed_pickups = 0;
        $completed_deliveries = 0;
        $completed_collections = 0;

        $incompleted_pending = 0;
        $incompleted_assigned = 0;
        $incompleted_accepted = 0;
        $incompleted_in_progress = 0;

        if(isset($data['orders'][0]))
        {
            foreach($data['orders'] as $order)
            {
                if($order['deliverypickup'] == 'Delivery')
                {
                    $all_deliveries++;
                    if($order['status'] == 'Delivered')
                    {
                        $completed_deliveries++;
                    }
                    else
                    {
                        if($order['status'] == 'Pending')
                        {
                            $incompleted_pending++;
                        }
                        elseif($order['status'] == 'Assigned')
                        {
                            $incompleted_assigned++;
                        }
                        elseif($order['status'] == 'Accepted')
                        {
                            $incompleted_accepted++;
                        }
                        elseif($order['status'] == 'InProgress')
                        {
                            $incompleted_in_progress++;
                        }
                        else
                        {
                            continue;
                        }
                    }
                }
                elseif($order['deliverypickup'] == 'Pick Up' || $order['deliverypickup'] == 'Pickup' || $order['deliverypickup'] == 'PickUp')
                {
                    $all_pickups++;
                    if($order['status'] == 'Picked up')
                    {
                        $completed_pickups++;
                    }
                    else
                    {
                        if($order['status'] == 'Pending')
                        {
                            $incompleted_pending++;
                        }
                        elseif($order['status'] == 'Assigned')
                        {
                            $incompleted_assigned++;
                        }
                        elseif($order['status'] == 'Accepted')
                        {
                            $incompleted_accepted++;
                        }
                        elseif($order['status'] == 'InProgress')
                        {
                            $incompleted_in_progress++;
                        }
                        else
                        {
                            continue;
                        }
                    }
                }
                elseif($order['deliverypickup'] == 'Collection')
                {
                    $all_collections++;
                    if($order['status'] == 'Collected')
                    {
                        $completed_collections++;
                    }
                    else
                    {
                        if($order['status'] == 'Pending')
                        {
                            $incompleted_pending++;
                        }
                        elseif($order['status'] == 'Assigned')
                        {
                            $incompleted_assigned++;
                        }
                        elseif($order['status'] == 'Accepted')
                        {
                            $incompleted_accepted++;
                        }
                        elseif($order['status'] == 'InProgress')
                        {
                            $incompleted_in_progress++;
                        }
                        else
                        {
                            continue;
                        }
                    }
                }
                else
                {
                    continue;
                }
            }
        }
        $data['all_pickups'] = $all_pickups;
        $data['all_deliveries'] = $all_deliveries;
        $data['all_collections'] = $all_collections;

        $data['completed_pickups'] = $completed_pickups;
        $data['completed_deliveries'] = $completed_deliveries;
        $data['completed_collections'] = $completed_collections;

        $data['incompleted_pending'] = $incompleted_pending;
        $data['incompleted_accepted'] = $incompleted_accepted;
        $data['incompleted_assigned'] = $incompleted_assigned;
        $data['incompleted_in_progress'] = $incompleted_in_progress;

        $data['completed_total'] = $completed_pickups + $completed_deliveries + $completed_collections;
        $data['incompleted_total'] = $incompleted_pending + $incompleted_accepted + $incompleted_assigned + $incompleted_in_progress;
        return $data;
    }
    public function leadsCount($filter,$start_date11,$end_date11,$lead_owner,$state)
    {
        // dd($start_date11,$end_date11);
        $diff = 0;
        $state_glob = 0;
        if($state !="stateWeek" && ($start_date11 != "start" && $end_date11 != "end"))
        {
            $state_glob = 1;
            $start_date = $start_date11;
            $end_date = $end_date11;
            $date = Carbon::parse($start_date);
            $now = Carbon::parse($end_date);

            $diff = $date->diffInDays($now);
            // dd($diff);
        }
        if($filter == "All" || $diff > 366)
        {
            // if()
            $data['xAxis'] = 'Year';
            if(session('role') == 'superuser')
            {
                // $lead = DB::select("SELECT * FROM leads");
                if($lead_owner == "All")
                {
                    $lead = DB::select("SELECT * FROM leads");
                }
                else
                {
                    $lead = DB::select("SELECT * FROM leads WHERE lead_owner = $lead_owner");
                }
            }
            elseif(session('role') == 'user')
            {
                $user_id = session('user_id');
                $lead = DB::select("SELECT * FROM leads WHERE leads.lead_owner = $user_id");
            }
            $leads = json_decode(json_encode($lead), true);
            if(isset($leads[0]))
            {
                if($state_glob == 1)
                {
                    $data['start_year'] = date('Y',strtotime($start_date11));
                    $data['end_year'] = date('Y',strtotime($end_date11));
                    // echo "dates".$data['start_year'],$data['end_year'];
                }
                else
                {
                    $data['start_year'] = date('Y',strtotime($leads[0]['creation_date']));
                    $data['end_year'] = date('Y');
                }
                $data['period_leads'] = array();
                $data['in_process_lead'] = array();
                $data['closed_lead'] = array();
                $data['converted_lead'] = array();
                for($year=$data['start_year']; $year<=$data['end_year']; $year++)
                {
                    $start_date = $year.'-01-01';
                    $end_date = $year.'-12-31';
                    array_push($data['period_leads'],$year);
                    if(session('role') == 'superuser')
                    {
                        // $foryear = DB::select("SELECT * FROM leads WHERE lead_status = 'Work In Process' AND creation_date BETWEEN '$start_date' AND '$end_date'");
                        if($lead_owner == "All")
                        {
                            // $lead = DB::select("SELECT * FROM leads WHERE creation_date BETWEEN '$start_date' AND '$end_date'");
                            $foryear = DB::select("SELECT * FROM leads WHERE lead_status = 'Work In Process' AND creation_date BETWEEN '$start_date' AND '$end_date'");
                        }
                        else
                        {
                            // $lead = DB::select("SELECT * FROM leads WHERE creation_date BETWEEN '$start_date' AND '$end_date' AND lead_owner = $lead_owner");
                            $foryear = DB::select("SELECT * FROM leads WHERE lead_status = 'Work In Process' AND creation_date BETWEEN '$start_date' AND '$end_date' AND lead_owner = $lead_owner");
                        }
                    }
                    elseif(session('role') == 'user')
                    {
                        $user_id = session('user_id');
                        $foryear = DB::select("SELECT * FROM leads WHERE lead_status = 'Work In Process' AND creation_date BETWEEN '$start_date' AND '$end_date' AND leads.lead_owner = $user_id");
                    }
                    $foryears = json_decode(json_encode($foryear), true);
                    array_push($data['in_process_lead'],count($foryears));
                    if(session('role') == 'superuser')
                    {
                        $foryear = DB::select("SELECT * FROM leads WHERE lead_status NOT IN ('Work In Process','Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date'");
                    }
                    elseif(session('role') == 'user')
                    {
                        $user_id = session('user_id');
                        $foryear = DB::select("SELECT * FROM leads WHERE lead_status NOT IN ('Work In Process','Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date' AND leads.lead_owner = $user_id");
                    }
                    // $foryear = DB::select("SELECT * FROM leads WHERE lead_status NOT IN ('Work In Process','Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date'");
                    $foryears = json_decode(json_encode($foryear), true);
                    array_push($data['closed_lead'],count($foryears));
                    if(session('role') == 'superuser')
                    {
                        $foryear = DB::select("SELECT * FROM leads WHERE lead_status IN ('Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date'");
                    }
                    elseif(session('role') == 'user')
                    {
                        $user_id = session('user_id');
                        $foryear = DB::select("SELECT * FROM leads WHERE lead_status IN ('Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date' AND leads.lead_owner = $user_id");
                    }
                    // $foryear = DB::select("SELECT * FROM leads WHERE lead_status IN ('Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date'");
                    $foryears = json_decode(json_encode($foryear), true);
                    array_push($data['converted_lead'],count($foryears));
    
                }
            }
            else
            {
                $data['period_leads'] = array();
                $data['in_process_lead'] = array();
                $data['closed_lead'] = array();
                $data['converted_lead'] = array();

                array_push($data['period_leads'],0);
                array_push($data['in_process_lead'],0);
                array_push($data['closed_lead'],0);
                array_push($data['converted_lead'],0);
            }
            
        }
        elseif($filter == "Year" || ($diff <= 366 && $diff >31))
        {
            if($state_glob == 1)
            {
                $start_year = date('Y',strtotime($start_date11));
                $end_year = date('Y',strtotime($end_date11));
                if($start_year != $end_year)
                {
                    $data['xAxis'] = 'Month of '.$start_year.'-'.$end_year;
                }
                else
                {
                    $data['xAxis'] = 'Month of '.$start_year;
                }
                $start_date = $start_date11;
                $end_date = $end_date11;
            }
            else
            {
                $year = date('Y');
                $data['xAxis'] = 'Month of '.$year;
                $start_date = $year.'-01-01';
                $end_date = $year.'-12-31';
            }
           
            if(session('role') == 'superuser')
            {
                // $lead = DB::select("SELECT * FROM leads WHERE creation_date BETWEEN '$start_date' AND '$end_date'");
                if($lead_owner == "All")
                {
                    $lead = DB::select("SELECT * FROM leads WHERE creation_date BETWEEN '$start_date' AND '$end_date'");
                }
                else
                {
                    $lead = DB::select("SELECT * FROM leads WHERE creation_date BETWEEN '$start_date' AND '$end_date' AND lead_owner = $lead_owner");
                }
            }
            elseif(session('role') == 'user')
            {
                $user_id = session('user_id');
                $lead = DB::select("SELECT * FROM leads WHERE leads.lead_owner = $user_id AND creation_date BETWEEN '$start_date' AND '$end_date'");
            }
            $leads = json_decode(json_encode($lead), true);
            if(isset($leads[0]))
            {
                if($state_glob == 1)
                {
                    $data['start_year'] = date('Y',strtotime($start_date11));
                    $data['end_year'] = date('Y',strtotime($end_date11));
                    
                    $data['start_month'] = date('Y-m-d',strtotime($start_date11));
                    $data['end_month'] = date('Y-m-d',strtotime($end_date11));
                    // echo "dates".$data['start_year'],$data['end_year'];
                }
                else
                {
                    $data['start_year'] = date('Y',strtotime($leads[0]['creation_date']));
                    $data['end_year'] = date('Y');

                    $data['start_month'] = date('Y-m-d',strtotime($leads[0]['creation_date']));
                    $data['end_month'] = date('Y-m-d');
                }
                // $data['start_year'] = date('Y',strtotime($leads[0]['creation_date']));
                // $data['end_year'] = date('Y');
                $data['period_leads'] = array();
                $data['in_process_lead'] = array();
                $data['closed_lead'] = array();
                $data['converted_lead'] = array();
                $start_month = date('Y-m',strtotime($data['start_month']));
                for($month = date('Y-m',strtotime($start_month)); $month<=date('Y-m',strtotime($data['end_month'])); $month = date('Y-m',strtotime("+1 month",strtotime($month))))
                {
                    $month_loc = date('Y-m',strtotime($month));
                    $start_date = $month.'-01';
                    $end_date = $month.'-31';
                    array_push($data['period_leads'],$month);
                    if(session('role') == 'superuser')
                    {
                        $foryear = DB::select("SELECT * FROM leads WHERE lead_status = 'Work In Process' AND creation_date BETWEEN '$start_date' AND '$end_date'");
                    }
                    elseif(session('role') == 'user')
                    {
                        $user_id = session('user_id');
                        $foryear = DB::select("SELECT * FROM leads WHERE lead_status = 'Work In Process' AND creation_date BETWEEN '$start_date' AND '$end_date' AND leads.lead_owner = $user_id");
                    }
                    $foryears = json_decode(json_encode($foryear), true);
                    array_push($data['in_process_lead'],count($foryears));
                    if(session('role') == 'superuser')
                    {
                        $foryear = DB::select("SELECT * FROM leads WHERE lead_status NOT IN ('Work In Process','Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date'");
                    }
                    elseif(session('role') == 'user')
                    {
                        $user_id = session('user_id');
                        $foryear = DB::select("SELECT * FROM leads WHERE lead_status NOT IN ('Work In Process','Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date' AND leads.lead_owner = $user_id");
                    }
                    // $foryear = DB::select("SELECT * FROM leads WHERE lead_status NOT IN ('Work In Process','Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date'");
                    $foryears = json_decode(json_encode($foryear), true);
                    array_push($data['closed_lead'],count($foryears));
                    if(session('role') == 'superuser')
                    {
                        $foryear = DB::select("SELECT * FROM leads WHERE lead_status IN ('Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date'");
                    }
                    elseif(session('role') == 'user')
                    {
                        $user_id = session('user_id');
                        $foryear = DB::select("SELECT * FROM leads WHERE lead_status IN ('Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date' AND leads.lead_owner = $user_id");
                    }
                    // $foryear = DB::select("SELECT * FROM leads WHERE lead_status IN ('Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date'");
                    $foryears = json_decode(json_encode($foryear), true);
                    array_push($data['converted_lead'],count($foryears));
    
                }
            }
            else
            {
                $data['period_leads'] = array();
                $data['in_process_lead'] = array();
                $data['closed_lead'] = array();
                $data['converted_lead'] = array();

                array_push($data['period_leads'],0);
                array_push($data['in_process_lead'],0);
                array_push($data['closed_lead'],0);
                array_push($data['converted_lead'],0);
            }
            
        }
        elseif($filter == "Month" || ($diff <= 31 && $diff > 7))
        {
            $start_date = 0;
            $end_date = 0;
            if($state_glob == 1)
            {
                $year = date('Y');
                $data['xAxis'] = 'Days of '.date('M',strtotime($start_date11)).'-'.date('M',strtotime($end_date11));
                $month = date('m');
                $last_day = date('t');
                $start_date = date('Y-m-d',strtotime($start_date11));
                $end_date = date('Y-m-d',strtotime($end_date11));
            }
            else
            {
                $year = date('Y');
                $data['xAxis'] = 'Days of '.date('M');
                $month = date('m');
                $last_day = date('t');
                $start_date = $year.'-'.$month.'-01';
                $end_date = $year.'-'.$month.'-'.$last_day;
            }
            
            if(session('role') == 'superuser')
            {
                // $lead = DB::select("SELECT * FROM leads WHERE creation_date BETWEEN '$start_date' AND '$end_date'");
                if($lead_owner == "All")
                {
                    $lead = DB::select("SELECT * FROM leads WHERE creation_date BETWEEN '$start_date' AND '$end_date'");
                }
                else
                {
                    $lead = DB::select("SELECT * FROM leads WHERE creation_date BETWEEN '$start_date' AND '$end_date' AND lead_owner = $lead_owner");
                }
            }
            elseif(session('role') == 'user')
            {
                $user_id = session('user_id');
                $lead = DB::select("SELECT * FROM leads WHERE leads.lead_owner = $user_id AND creation_date BETWEEN '$start_date' AND '$end_date'");
            }
            $leads = json_decode(json_encode($lead), true);
            if(isset($leads[0]))
            {
                // $data['start_year'] = date('Y',strtotime($leads[0]['creation_date']));
                // $data['end_year'] = date('Y');
                if($state_glob == 1)
                {
                    $data['start_year'] = date('Y',strtotime($start_date11));
                    $data['end_year'] = date('Y',strtotime($end_date11));
                    
                    $data['start_month'] = date('Y-m-d',strtotime($start_date11));
                    $data['end_month'] = date('Y-m-d',strtotime($end_date11));
                    // echo "dates".$data['start_year'],$data['end_year'];
                }
                else
                {
                    $data['start_year'] = date('Y',strtotime($leads[0]['creation_date']));
                    $data['end_year'] = date('Y');

                    $data['start_month'] = date('Y-m-d',strtotime($leads[0]['creation_date']));
                    $data['end_month'] = date('Y-m-d');
                }
                $data['period_leads'] = array();
                $data['in_process_lead'] = array();
                $data['closed_lead'] = array();
                $data['converted_lead'] = array();
                // $data['Dates']=$start_date."-".$end_date;
                for($day = date('Y-m-d',strtotime($start_date)); $day <= date('Y-m-d',strtotime($end_date)); $day = date('Y-m-d',strtotime("+1 days",strtotime($day))))
                {
                    $start_date_loc = $day;
                    $end_date_loc = $day;
                    array_push($data['period_leads'],date('d-M',strtotime($day)));
                    if(session('role') == 'superuser')
                    {
                        $foryear = DB::select("SELECT * FROM leads WHERE lead_status = 'Work In Process' AND creation_date BETWEEN '$start_date_loc' AND '$end_date_loc'");
                    }
                    elseif(session('role') == 'user')
                    {
                        $user_id = session('user_id');
                        $foryear = DB::select("SELECT * FROM leads WHERE lead_status = 'Work In Process' AND creation_date BETWEEN '$start_date_loc' AND '$end_date_loc' AND leads.lead_owner = $user_id");
                    }
                    $foryears = json_decode(json_encode($foryear), true);
                    array_push($data['in_process_lead'],count($foryears));
                    if(session('role') == 'superuser')
                    {
                        $foryear = DB::select("SELECT * FROM leads WHERE lead_status NOT IN ('Work In Process','Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date_loc' AND '$end_date_loc'");
                    }
                    elseif(session('role') == 'user')
                    {
                        $user_id = session('user_id');
                        $foryear = DB::select("SELECT * FROM leads WHERE lead_status NOT IN ('Work In Process','Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date_loc' AND '$end_date_loc' AND leads.lead_owner = $user_id");
                    }
                    // $foryear = DB::select("SELECT * FROM leads WHERE lead_status NOT IN ('Work In Process','Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date_loc' AND '$end_date_loc'");
                    $foryears = json_decode(json_encode($foryear), true);
                    array_push($data['closed_lead'],count($foryears));
                    if(session('role') == 'superuser')
                    {
                        $foryear = DB::select("SELECT * FROM leads WHERE lead_status IN ('Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date_loc' AND '$end_date_loc'");
                    }
                    elseif(session('role') == 'user')
                    {
                        $user_id = session('user_id');
                        $foryear = DB::select("SELECT * FROM leads WHERE lead_status IN ('Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date_loc' AND '$end_date_loc' AND leads.lead_owner = $user_id");
                    }
                    // $foryear = DB::select("SELECT * FROM leads WHERE lead_status IN ('Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date_loc' AND '$end_date_loc'");
                    $foryears = json_decode(json_encode($foryear), true);
                    array_push($data['converted_lead'],count($foryears));
    
                }
            }
            else
            {
                $data['period_leads'] = array();
                $data['in_process_lead'] = array();
                $data['closed_lead'] = array();
                $data['converted_lead'] = array();

                array_push($data['period_leads'],0);
                array_push($data['in_process_lead'],0);
                array_push($data['closed_lead'],0);
                array_push($data['converted_lead'],0);
            }
            
        }
        elseif($filter == "Week" || ($diff <=7))
        {
            if($state_glob == 1)
            {
                $year = date('Y');
                $month = date('m');
                $data['xAxis'] = 'Days';
                $end_date_glob = date('Y-m-d',strtotime($end_date11));
                $start_date_glob = date('Y-m-d',strtotime($start_date11));
                $end_day = date('d',strtotime($end_date11));
                $start_day = date('d',strtotime($start_date11));
            }
            else
            {
                $year = date('Y');
                $month = date('m');
                $data['xAxis'] = 'Days';
                $end_date_glob = date('Y-m-d');
                $start_date_glob = date('Y-m-d',strtotime("-7 days", strtotime($end_date_glob)));
                $end_day = date('d');
                $start_day = date('d',strtotime("-7 days", strtotime($end_date_glob)));
            }
            
            if(session('role') == 'superuser')
            {
                if($lead_owner == "All")
                {
                    $lead = DB::select("SELECT * FROM leads WHERE creation_date BETWEEN '$start_date_glob' AND '$end_date_glob'");
                }
                else
                {
                    $lead = DB::select("SELECT * FROM leads WHERE creation_date BETWEEN '$start_date_glob' AND '$end_date_glob' AND lead_owner = $lead_owner");
                }
            }
            elseif(session('role') == 'user')
            {
                $user_id = session('user_id');
                $lead = DB::select("SELECT * FROM leads WHERE leads.lead_owner = $user_id AND creation_date BETWEEN '$start_date_glob' AND '$end_date_glob'");
            }
            $leads = json_decode(json_encode($lead), true);
            if(isset($leads[0]))
            {
                if($state_glob == 1)
                {
                    $data['start_year'] = date('Y',strtotime($start_date11));
                    $data['end_year'] = date('Y',strtotime($end_date11));
                }
                else
                {
                    $data['start_year'] = date('Y',strtotime($leads[0]['creation_date']));
                    $data['end_year'] = date('Y');
                }
                $data['period_leads'] = array();
                $data['in_process_lead'] = array();
                $data['closed_lead'] = array();
                $data['converted_lead'] = array();
                // echo "End Date: ".$end_date_glob;
                for($date = date('Y-m-d',strtotime($start_date_glob)); date('Y-m-d',strtotime($date)) <= date('Y-m-d',strtotime($end_date_glob)); $date = date('Y-m-d',strtotime("+1 days", strtotime($date))))
                { 
                    // echo "Date: ".$date;
                    // $start_date = $year.'-'.$month.'-'.$day;
                    $start_date = $date;
                    $end_date = $date;
                    $day = date('d',strtotime($start_date));
                    // $end_date = $year.'-'.$month.'-'.$day;
                    array_push($data['period_leads'],$day);
                    if(session('role') == 'superuser')
                    {
                        $foryear = DB::select("SELECT * FROM leads WHERE lead_status = 'Work In Process' AND creation_date BETWEEN '$start_date' AND '$end_date'");
                    }
                    elseif(session('role') == 'user')
                    {
                        $user_id = session('user_id');
                        $foryear = DB::select("SELECT * FROM leads WHERE lead_status = 'Work In Process' AND creation_date BETWEEN '$start_date' AND '$end_date' AND leads.lead_owner = $user_id");
                    }
                    $foryears = json_decode(json_encode($foryear), true);
                    array_push($data['in_process_lead'],count($foryears));
                    if(session('role') == 'superuser')
                    {
                        $foryear = DB::select("SELECT * FROM leads WHERE lead_status NOT IN ('Work In Process','Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date'");
                    }
                    elseif(session('role') == 'user')
                    {
                        $user_id = session('user_id');
                        $foryear = DB::select("SELECT * FROM leads WHERE lead_status NOT IN ('Work In Process','Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date' AND leads.lead_owner = $user_id");
                    }
                    // $foryear = DB::select("SELECT * FROM leads WHERE lead_status NOT IN ('Work In Process','Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date'");
                    $foryears = json_decode(json_encode($foryear), true);
                    array_push($data['closed_lead'],count($foryears));
                    if(session('role') == 'superuser')
                    {
                        $foryear = DB::select("SELECT * FROM leads WHERE lead_status IN ('Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date'");
                    }
                    elseif(session('role') == 'user')
                    {
                        $user_id = session('user_id');
                        $foryear = DB::select("SELECT * FROM leads WHERE lead_status IN ('Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date' AND leads.lead_owner = $user_id");
                    }
                    // $foryear = DB::select("SELECT * FROM leads WHERE lead_status IN ('Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date'");
                    $foryears = json_decode(json_encode($foryear), true);
                    array_push($data['converted_lead'],count($foryears));
    
                }
            }
            else
            {
                $data['period_leads'] = array();
                $data['in_process_lead'] = array();
                $data['closed_lead'] = array();
                $data['converted_lead'] = array();

                array_push($data['period_leads'],0);
                array_push($data['in_process_lead'],0);
                array_push($data['closed_lead'],0);
                array_push($data['converted_lead'],0);
            }
            
        }
        // elseif($filter == "DateSearch")
        // {
        //     // $year = date('Y');
        //     $data['xAxis'] = 'Date Search';
        //     // $month = date('m');
        //     // $last_day = date('t');
        //     // $start_date = $year.'-'.$month.'-01';
        //     // $end_date = $year.'-'.$month.'-'.$last_day;
        //     $start_date = $start_date11;
        //     $end_date = $end_date11;
        //     if(session('role') == 'superuser')
        //     {
        //         if($lead_owner == "All")
        //         {
        //             $lead = DB::select("SELECT * FROM leads WHERE creation_date BETWEEN '$start_date' AND '$end_date'");
        //         }
        //         else
        //         {
        //             $lead = DB::select("SELECT * FROM leads WHERE creation_date BETWEEN '$start_date' AND '$end_date' AND lead_owner = $lead_owner");
        //         }
        //     }
        //     elseif(session('role') == 'user')
        //     {
        //         $user_id = session('user_id');
        //         $lead = DB::select("SELECT * FROM leads WHERE leads.lead_owner = $user_id AND creation_date BETWEEN '$start_date' AND '$end_date'");
        //     }
        //     $leads = json_decode(json_encode($lead), true);
        //     if(isset($leads[0]))
        //     {
        //         $data['start_year'] = date('Y',strtotime($leads[0]['creation_date']));
        //         $data['end_year'] = date('Y');
        //         $data['period_leads'] = array();
        //         $data['in_process_lead'] = array();
        //         $data['closed_lead'] = array();
        //         $data['converted_lead'] = array();
        //     }
        //     else
        //     {
        //         $data['period_leads'] = array();
        //         $data['in_process_lead'] = array();
        //         $data['closed_lead'] = array();
        //         $data['converted_lead'] = array();

        //         array_push($data['period_leads'],0);
        //         array_push($data['in_process_lead'],0);
        //         array_push($data['closed_lead'],0);
        //         array_push($data['converted_lead'],0);
        //     }
            
        // }
        $data['total_leads'] = count($leads);
        $inprocess_count = 0;
        $closed_count = 0;
        $converted_count = 0;
        foreach ($leads as $lead)
        {
            if($lead['lead_status'] == "Converted" || $lead['lead_status'] == "Order Generated" || $lead['lead_status'] == "DelBoy Assigned")
            {
                $converted_count++;
            }
            elseif($lead['lead_status'] == "Work In Process")
            {
                $inprocess_count++;
            }
            else
            {
                $closed_count++;
            }
        }
        $data['inprocess_count'] = $inprocess_count;
        $data['closed_count'] = $closed_count;
        $data['converted_count'] = $converted_count;
        
        return $data;
    }

    public function vdrEquipmentCount($filter)
    {
        $data['vdr_rent_equip_filter'] = $filter;
        $data['q5c_rent_equip_filter'] = $filter;
        $equipments = DB::select("SELECT SUM(vendor_products.product_quantity) as available_count FROM vendor_details,vendor_products WHERE vendor_details.of_primary_contact_1 != '9820616550' AND vendor_details.id=vendor_products.vendor_id");
        $data['available_count'] = json_decode(json_encode($equipments), true);

        $equipments = DB::select("SELECT count(*) as rented_count FROM vendor_details,vendor_rented_products WHERE vendor_details.of_primary_contact_1 != '9820616550' AND vendor_details.id=vendor_rented_products.vendor_id AND vendor_rented_products.status = 'On Rent'");
        $data['rented_count'] = json_decode(json_encode($equipments), true);

        $equipments = DB::select("SELECT vendor_rented_products.rental_date as rental_date FROM vendor_details,vendor_rented_products WHERE vendor_details.of_primary_contact_1 != '9820616550' AND vendor_details.id=vendor_rented_products.vendor_id AND vendor_rented_products.status = 'On Rent'");
        $data['rented_date_count'] = json_decode(json_encode($equipments), true);
        
        if($filter=="Week")
        {
            $year = date('Y');
            $month = date('m');
            $front_data['xAxis'] = 'Days';
            
            $end_date_glob = date('Y-m-d');
            $start_date_glob = date('Y-m-d',strtotime("-7 days", strtotime($end_date_glob)));
            $end_day = date('d');
            $start_day = date('d',strtotime("-7 days", strtotime($end_date_glob)));
            $front_data['rented_arr'] = array();
            $front_data['period_vdr_equip'] = array();
            
            for($date = date('Y-m-d',strtotime($start_date_glob)); date('Y-m-d',strtotime($date)) <= date('Y-m-d',strtotime($end_date_glob)); $date = date('Y-m-d',strtotime("+1 days", strtotime($date))))
            {
                $start_date = $date;
                $end_date = $date;
                $day = date('d',strtotime($start_date));
                // $end_date = $year.'-'.$month.'-'.$day;
                array_push($front_data['period_vdr_equip'],$day);
                $equipments = DB::select("SELECT count(*) as rented_count FROM vendor_details,vendor_rented_products WHERE vendor_details.of_primary_contact_1 != '9820616550' AND vendor_details.id=vendor_rented_products.vendor_id AND vendor_rented_products.status = 'On Rent' AND vendor_rented_products.rental_date BETWEEN $start_date AND $end_date");
                $data['rented_count'] = json_decode(json_encode($equipments), true);
                array_push($front_data['rented_arr'],$data['rented_count'][0]['rented_count']);
            } 
            
        }
        if($filter=="Month")
        {
            $year = date('Y');
            $front_data['xAxis'] = 'Days of '.date('M');
            $month = date('m');
            $last_day = date('t');
            $front_data['period_vdr_equip'] = array();
            $front_data['rented_arr'] = array();
            for($day=1; $day<=$last_day; $day++)
            {
                $start_date = $year.'-'.$month.'-'.$day;
                $end_date = $year.'-'.$month.'-'.$day;
                // $end_date = $year.'-'.$month.'-'.$day;
                array_push($front_data['period_vdr_equip'],$day);
                $equipments = DB::select("SELECT count(*) as rented_count FROM vendor_details,vendor_rented_products WHERE vendor_details.of_primary_contact_1 != '9820616550' AND vendor_details.id=vendor_rented_products.vendor_id AND vendor_rented_products.status = 'On Rent' AND vendor_rented_products.rental_date BETWEEN $start_date AND $end_date");
                $data['rented_count'] = json_decode(json_encode($equipments), true);
                array_push($front_data['rented_arr'],$data['rented_count'][0]['rented_count']);
            } 
            
        }
        if($filter=="Year")
        {
            $year = date('Y');
            $front_data['xAxis'] = 'Month of '.$year;
            $start_date = $year.'-01-01';
            $end_date = $year.'-12-31';

            $front_data['rented_arr'] = array();
            $front_data['period_vdr_equip'] = array();
            
            for($month=1; $month<=12; $month++)
            {
                $start_date = $year.'-'.$month.'-01';
                $end_date = $year.'-'.$month.'-31';
                // $end_date = $year.'-'.$month.'-'.$day;
                array_push($front_data['period_vdr_equip'],$month);
                $equipments = DB::select("SELECT count(*) as rented_count FROM vendor_details,vendor_rented_products WHERE vendor_details.of_primary_contact_1 != '9820616550' AND vendor_details.id=vendor_rented_products.vendor_id AND vendor_rented_products.status = 'On Rent' AND vendor_rented_products.rental_date BETWEEN $start_date AND $end_date");
                $data['rented_count'] = json_decode(json_encode($equipments), true);
                array_push($front_data['rented_arr'],$data['rented_count'][0]['rented_count']);
            } 
            
        }
        if($filter=="All")
        {
            $front_data['xAxis'] = 'Year';

            $front_data['rented_arr'] = array();
            $front_data['period_vdr_equip'] = array();
            if(isset($data['rented_date_count'][0]['rental_date']))
            {
                $data['start_year'] = date('Y',strtotime($data['rented_date_count'][0]['rental_date']));
            }
            else
            {
                $data['start_year'] = date('Y');    
            }

            $data['end_year'] = date('Y');

            for($year=$data['start_year']; $year<=$data['end_year']; $year++)
            {
                $start_date = $year.'-01-01';
                $end_date = $year.'-12-31';
                array_push($front_data['period_vdr_equip'],$year);
                $equipments = DB::select("SELECT count(*) as rented_count FROM vendor_details,vendor_rented_products WHERE vendor_details.of_primary_contact_1 != '9820616550' AND vendor_details.id=vendor_rented_products.vendor_id AND vendor_rented_products.status = 'On Rent' AND vendor_rented_products.rental_date BETWEEN $start_date AND $end_date");
                $data['rented_count'] = json_decode(json_encode($equipments), true);
                array_push($front_data['rented_arr'],$data['rented_count'][0]['rented_count']);
            } 
            
        }

        $front_data['vdr_available_equip'] = $data['available_count'][0]['available_count'];
        $front_data['vdr_rented_equip'] = $data['rented_count'][0]['rented_count'];
        $front_data['vdr_total_equip'] = $front_data['vdr_available_equip'] + $front_data['vdr_rented_equip'];
        // print_r($front_data);
        return $front_data;
    }

    public function q5cEquipmentCount($filter)
    {
        $data['q5c_rent_equip_filter'] = $filter;
        $equipments = DB::select("SELECT SUM(vendor_products.product_quantity) as available_count FROM vendor_details,vendor_products WHERE vendor_details.of_primary_contact_1 = '9820616550' AND vendor_details.id=vendor_products.vendor_id");
        $data['available_count'] = json_decode(json_encode($equipments), true);

        $equipments = DB::select("SELECT count(*) as rented_count FROM vendor_details,vendor_rented_products WHERE vendor_details.of_primary_contact_1 = '9820616550' AND vendor_details.id=vendor_rented_products.vendor_id");
        $data['rented_count'] = json_decode(json_encode($equipments), true);
        

        $front_data['available_equip'] = $data['available_count'][0]['available_count'];
        $front_data['rented_equip'] = $data['rented_count'][0]['rented_count'];
        $front_data['total_equip'] = $front_data['available_equip'] + $front_data['rented_equip'];
        // print_r($front_data);
        return $front_data;
    }

    public function mailLeadReport()
    {
        $filter="Week";
        if($filter == "Week")
        {
            $year = date('Y');
            $month = date('m');
            $data['xAxis'] = 'Days';
            
            // $last_day = date('t');
            // $start_date = '01-'.$month.'-'.$year;
            // $end_date = $last_day.'-'.$month.'-'.$year;
            $end_date_glob = date('Y-m-d');
            $start_date_glob = date('Y-m-d',strtotime("-7 days", strtotime($end_date_glob)));
            $end_day = date('d');
            $start_day = date('d',strtotime("-7 days", strtotime($end_date_glob)));
            $users = DB::select("SELECT * FROM user WHERE role = 'user' AND email_id_user IS NOT NULL");
            $users = json_decode(json_encode($users), true);
            $data['users'] = array();
            $data['period_leads'] = array();
            for($date = date('Y-m-d',strtotime($start_date_glob)); date('Y-m-d',strtotime($date)) <= date('Y-m-d',strtotime($end_date_glob)); $date = date('Y-m-d',strtotime("+1 days", strtotime($date))))
            {
                $start_date = $date;
                $end_date = $date;
                $day = date('d-m-Y',strtotime($start_date));
                array_push($data['period_leads'],$day);
            } 
            foreach($users as $key=>$user)
            {
                $user_id = $user['id'];
                $lead = DB::select("SELECT * FROM leads,user WHERE leads.lead_owner = user.id AND leads.lead_owner = $user_id AND leads.creation_date BETWEEN '$start_date_glob' AND '$end_date_glob' ");
                $leads = json_decode(json_encode($lead), true);
                
                $data['in_process_lead'] = array();
                $data['closed_lead'] = array();
                $data['converted_lead'] = array();
                
                if(isset($leads[0]))
                {
                    $data['start_year'] = date('Y',strtotime($leads[0]['creation_date']));
                    $data['end_year'] = date('Y');
                
                    // echo "End Date: ".$end_date_glob;
                    for($date = date('Y-m-d',strtotime($start_date_glob)); date('Y-m-d',strtotime($date)) <= date('Y-m-d',strtotime($end_date_glob)); $date = date('Y-m-d',strtotime("+1 days", strtotime($date))))
                    { 
                        // echo "Date: ".$date;
                        // $start_date = $year.'-'.$month.'-'.$day;
                        $start_date = $date;
                        $end_date = $date;
                        // $day = date('d',strtotime($start_date));
                        // // $end_date = $year.'-'.$month.'-'.$day;
                        // array_push($data['period_leads'],$date);
                        
                        $foryear = DB::select("SELECT * FROM leads,user WHERE leads.lead_owner = user.id AND leads.lead_owner = $user_id AND lead_status = 'Work In Process' AND leads.creation_date BETWEEN '$start_date' AND '$end_date'");
                        
                        $foryears = json_decode(json_encode($foryear), true);
                        array_push($data['in_process_lead'],count($foryears));
                        $foryear = DB::select("SELECT * FROM leads,user WHERE leads.lead_owner = user.id AND leads.lead_owner = $user_id AND lead_status NOT IN ('Work In Process','Converted','Order Generated','DelBoy Assigned') AND leads.creation_date BETWEEN '$start_date' AND '$end_date'");
                        // $foryear = DB::select("SELECT * FROM leads WHERE lead_status NOT IN ('Work In Process','Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date'");
                        $foryears = json_decode(json_encode($foryear), true);
                        array_push($data['closed_lead'],count($foryears));
                            
                        $foryear = DB::select("SELECT * FROM leads ,user WHERE leads.lead_owner = user.id AND leads.lead_owner = $user_id AND lead_status IN ('Converted','Order Generated','DelBoy Assigned') AND leads.creation_date BETWEEN '$start_date' AND '$end_date'");
                    
                        // $foryear = DB::select("SELECT * FROM leads WHERE lead_status IN ('Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date'");
                        $foryears = json_decode(json_encode($foryear), true);
                        array_push($data['converted_lead'],count($foryears));
        
                    }
                }
                else
                {
                    //array_push($data['period_leads'],0);
                    array_push($data['in_process_lead'],0);
                    array_push($data['closed_lead'],0);
                    array_push($data['converted_lead'],0);
                }
                $temp = array();
                
                $temp['username']=$user['username'];
                $temp['in_process_lead'] = $data['in_process_lead'];
                $temp['closed_lead'] = $data['closed_lead'];
                $temp['converted_lead'] = $data['converted_lead'];
                array_push($data['users'],$temp);
            }
        }
        //print_r($data);
        //send mail
        $report_data = array(
            'user_data'=>$data['users'],
            'date_count'=>$data['period_leads'],
        );
        $username = "Harddik";
        $user_email = "viveks@quali55care.com";
        //$data['admin_mail']= $admin_mail;
        Mail::send('ReportMails/LeadReportWeeklyMail',$report_data, function($message) use ($user_email,$username)
        {
            $message->to($user_email, $username)->subject('Quali55Care -Lead Report');
            $message->from('tempmailquali@gmail.com', 'Quali55Care');
        });

    }

    public function mailOrderReport()
    {
        $orderTypeNotIn = config('app.order_type');        
        $orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";

        $filter = "Week";
        if($filter == 'Week')
        {
            $year = date('Y');
            $month = date('m');
            $data['xAxis'] = 'Days';
            // $last_day = date('t');
            // $start_date = '01-'.$month.'-'.$year;
            // $end_date = $last_day.'-'.$month.'-'.$year;
            $end_date_glob = date('d-m-Y');
            $start_date_glob = date('d-m-Y',strtotime("-7 days", strtotime($end_date_glob)));
            $end_day = date('d');
            $start_day = date('d',strtotime("-7 days", strtotime($end_date_glob)));
            
            $orders = DB::select("SELECT * FROM del_orders WHERE status != 'Closed' AND STR_TO_DATE(DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date_glob','%d-%m-%Y') AND STR_TO_DATE('$end_date_glob','%d-%m-%Y') AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) ORDER BY order_id ASC");

            $data['orders'] = json_decode(json_encode($orders),true);
            if(isset($data['orders'][0]))
            {
                $data['start_date'] = $start_date_glob;
                $data['end_date'] = $end_date_glob;
                $data['period_orders'] = array();
                $data['period_date'] = array();
                $data['incomplete'] = array();
                $data['completed'] = array();
                // for($day=$start_day; $day<=$end_day; $day++)
                // {
                //     $start_date = $day.'-'.$month.'-'.$year;
                //     $end_date = $day.'-'.$month.'-'.$year;
                // echo "End Date: ".$end_date_glob;
                for($date = date('Y-m-d',strtotime($start_date_glob)); date('Y-m-d',strtotime($date)) <= date('Y-m-d',strtotime($end_date_glob)); $date = date('Y-m-d',strtotime("+1 days", strtotime($date))))
                { 
                    // echo "Date: ".$date;
                    // $start_date = $year.'-'.$month.'-'.$day;
                    $start_date = date('d-m-Y',strtotime($date));
                    $end_date = date('d-m-Y',strtotime($date));
                    $day = date('d',strtotime($start_date));
                    array_push($data['period_orders'],$day);
                    array_push($data['period_date'],$date);
                    
                    $foryearin = DB::select("SELECT DISTINCT del_orders.* FROM del_orders WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) AND del_orders.status IN ('Assigned','Pending','Accepted','InProgress')");
                    $foryearcom = DB::select("SELECT DISTINCT del_orders.* FROM del_orders WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) AND del_orders.status IN ('Picked up','Collected','Delivered')");
                   
                    $foryears = json_decode(json_encode($foryearin), true);
                    if(isset($foryears[0]))
                    {
                        array_push($data['incomplete'],count($foryears));
                    }
                    else
                    {
                        array_push($data['incomplete'],0);
                    }
    
                    $foryearscom = json_decode(json_encode($foryearcom), true);
                    if(isset($foryearscom[0]))
                    {
                        array_push($data['completed'],count($foryearscom));
                    }
                    else
                    {
                        array_push($data['completed'],0);
                    }
                    //array_push($data['completed'],count($foryearscom));
                }
            }
            else
            {
                $data['period_orders'] = array();
                $data['incomplete'] = array();
                $data['completed'] = array();
                array_push($data['period_orders'],0);
                array_push($data['incomplete'],0);
                array_push($data['completed'],0);
            }
        }
        $data['total_orders'] = count($data['orders']);
        
        $all_pickups = 0;
        $all_deliveries = 0;
        $all_collections = 0;

        $completed_pickups = 0;
        $completed_deliveries = 0;
        $completed_collections = 0;

        $incompleted_pending = 0;
        $incompleted_assigned = 0;
        $incompleted_accepted = 0;
        $incompleted_in_progress = 0;

        if(isset($data['orders'][0]))
        {
            foreach($data['orders'] as $order)
            {
                if($order['deliverypickup'] == 'Delivery')
                {
                    $all_deliveries++;
                    if($order['status'] == 'Delivered')
                    {
                        $completed_deliveries++;
                    }
                    else
                    {
                        if($order['status'] == 'Pending')
                        {
                            $incompleted_pending++;
                        }
                        elseif($order['status'] == 'Assigned')
                        {
                            $incompleted_assigned++;
                        }
                        elseif($order['status'] == 'Accepted')
                        {
                            $incompleted_accepted++;
                        }
                        elseif($order['status'] == 'InProgress')
                        {
                            $incompleted_in_progress++;
                        }
                        else
                        {
                            continue;
                        }
                    }
                }
                elseif($order['deliverypickup'] == 'Pick Up' || $order['deliverypickup'] == 'Pickup' || $order['deliverypickup'] == 'PickUp')
                {
                    $all_pickups++;
                    if($order['status'] == 'Picked up')
                    {
                        $completed_pickups++;
                    }
                    else
                    {
                        if($order['status'] == 'Pending')
                        {
                            $incompleted_pending++;
                        }
                        elseif($order['status'] == 'Assigned')
                        {
                            $incompleted_assigned++;
                        }
                        elseif($order['status'] == 'Accepted')
                        {
                            $incompleted_accepted++;
                        }
                        elseif($order['status'] == 'InProgress')
                        {
                            $incompleted_in_progress++;
                        }
                        else
                        {
                            continue;
                        }
                    }
                }
                elseif($order['deliverypickup'] == 'Collection')
                {
                    $all_collections++;
                    if($order['status'] == 'Collected')
                    {
                        $completed_collections++;
                    }
                    else
                    {
                        if($order['status'] == 'Pending')
                        {
                            $incompleted_pending++;
                        }
                        elseif($order['status'] == 'Assigned')
                        {
                            $incompleted_assigned++;
                        }
                        elseif($order['status'] == 'Accepted')
                        {
                            $incompleted_accepted++;
                        }
                        elseif($order['status'] == 'InProgress')
                        {
                            $incompleted_in_progress++;
                        }
                        else
                        {
                            continue;
                        }
                    }
                }
                else
                {
                    continue;
                }
            }
        }
        $data['all_pickups'] = $all_pickups;
        $data['all_deliveries'] = $all_deliveries;
        $data['all_collections'] = $all_collections;

        $data['completed_pickups'] = $completed_pickups;
        $data['completed_deliveries'] = $completed_deliveries;
        $data['completed_collections'] = $completed_collections;

        $data['incompleted_pending'] = $incompleted_pending;
        $data['incompleted_accepted'] = $incompleted_accepted;
        $data['incompleted_assigned'] = $incompleted_assigned;
        $data['incompleted_in_progress'] = $incompleted_in_progress;

        $data['completed_total'] = $completed_pickups + $completed_deliveries + $completed_collections;
        $data['incompleted_total'] = $incompleted_pending + $incompleted_accepted + $incompleted_assigned + $incompleted_in_progress;

        $orderData = json_decode(json_encode($data),true);
        //day by total
        $day_by_total = array();
        foreach($orderData['incomplete'] as $key=>$inc_data)
        {
            $total = $inc_data + $orderData['completed'][$key];
            array_push($day_by_total,$total);
        }
        $orderData['day_by_total'] = $day_by_total;
        //sendmail
        $username = "Harddik";
        $user_email = "viveks@quali55care.com";
        //$data['admin_mail']= $admin_mail;
        Mail::send('ReportMails/OrderReportWeeklyMail',$orderData, function($message) use ($user_email,$username)
        {
            $message->to($user_email, $username)->subject('Quali55Care -Lead Report');
            $message->from('tempmailquali@gmail.com', 'Quali55Care');
        });
    }
    public function leadOwner()
    {
        $get_lead_owners = DB::table('user')
                        ->select('id as user_id','username as lead_owner')
                        ->where('role','=','user')
                        ->whereNotNull('email_id_user')
                        ->orderBy('username')
								->get()->toArray();
        $data['lead_owner'] = $get_lead_owners;
        return $data;
    }
}
?>