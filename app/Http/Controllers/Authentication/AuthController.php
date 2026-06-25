<?php

namespace App\Http\Controllers\Authentication;

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

class AuthController extends Controller
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
        $data = $this->ordersCount('Week');
        $data += $this->leadsCount('Week');
        $data += $this->q5cEquipmentCount('Week');
        $data += $this->vdrEquipmentCount('Week');
        
        
        return view('dashboard',$data);
    }
    public function ordersCount($filter)
    {
        $data['order_filter'] = $filter;
        $data['lead_filter'] = $filter;
        if($filter == 'All')
        {
            $data['xAxis'] = 'Year';
            if(session('role') == 'superuser')
            {
                $orders = DB::select("SELECT * FROM del_orders WHERE status != 'Closed' ORDER BY order_id ASC");
            }
            elseif(session('role') == 'user')
            {
                $user_id = session('user_id');
                $orders = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE del_orders.status != 'Closed' AND leads.id = del_orders.lead_id AND leads.lead_owner = $user_id ORDER BY del_orders.order_id ASC");
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
                        $foryearin = DB::select("SELECT DISTINCT del_orders.* FROM del_orders WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.status IN ('Assigned','Pending','Accepted','InProgress')");
                        $foryearcom = DB::select("SELECT DISTINCT del_orders.* FROM del_orders WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.status IN ('Picked up','Collected','Delivered')");
                    }
                    elseif(session('role') == 'user')
                    {
                        $user_id = session('user_id');
                        $foryearin = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.status IN ('Assigned','Pending','Accepted','InProgress') AND del_orders.lead_id = leads.id AND leads.lead_owner = $user_id");
                        $foryearcom = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.status IN ('Picked up','Collected','Delivered') AND del_orders.lead_id = leads.id AND leads.lead_owner = $user_id");
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
                $orders = DB::select("SELECT * FROM del_orders WHERE status != 'Closed' AND STR_TO_DATE(DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') ORDER BY order_id ASC");
            }
            elseif(session('role') == 'user')
            {
                $user_id = session('user_id');
                $orders = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE del_orders.status != 'Closed' AND STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND leads.id = del_orders.lead_id AND leads.lead_owner = $user_id ORDER BY del_orders.order_id ASC");
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
                        $foryearin = DB::select("SELECT DISTINCT del_orders.* FROM del_orders WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.status IN ('Assigned','Pending','Accepted','InProgress')");
                        $foryearcom = DB::select("SELECT DISTINCT del_orders.* FROM del_orders WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.status IN ('Picked up','Collected','Delivered')");
                    }
                    elseif(session('role') == 'user')
                    {
                        $user_id = session('user_id');
                        $foryearin = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.status IN ('Assigned','Pending','Accepted','InProgress') AND del_orders.lead_id = leads.id AND leads.lead_owner = $user_id");
                        $foryearcom = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.status IN ('Picked up','Collected','Delivered') AND del_orders.lead_id = leads.id AND leads.lead_owner = $user_id");
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
                $orders = DB::select("SELECT * FROM del_orders WHERE status != 'Closed' AND STR_TO_DATE(DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') ORDER BY order_id ASC");
            }
            elseif(session('role') == 'user')
            {
                $user_id = session('user_id');
                $orders = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE del_orders.status != 'Closed' AND STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND leads.id = del_orders.lead_id AND leads.lead_owner = $user_id ORDER BY del_orders.order_id ASC");
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
                        $foryearin = DB::select("SELECT DISTINCT del_orders.* FROM del_orders WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.status IN ('Assigned','Pending','Accepted','InProgress')");
                        $foryearcom = DB::select("SELECT DISTINCT del_orders.* FROM del_orders WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.status IN ('Picked up','Collected','Delivered')");
                    }
                    elseif(session('role') == 'user')
                    {
                        $user_id = session('user_id');
                        $foryearin = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.status IN ('Assigned','Pending','Accepted','InProgress') AND del_orders.lead_id = leads.id AND leads.lead_owner = $user_id");
                        $foryearcom = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.status IN ('Picked up','Collected','Delivered') AND del_orders.lead_id = leads.id AND leads.lead_owner = $user_id");
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
                $orders = DB::select("SELECT * FROM del_orders WHERE status != 'Closed' AND STR_TO_DATE(DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date_glob','%d-%m-%Y') AND STR_TO_DATE('$end_date_glob','%d-%m-%Y') ORDER BY order_id ASC");
            }
            elseif(session('role') == 'user')
            {
                $user_id = session('user_id');
                $orders = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE del_orders.status != 'Closed' AND STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date_glob','%d-%m-%Y') AND STR_TO_DATE('$end_date_glob','%d-%m-%Y') AND leads.id = del_orders.lead_id AND leads.lead_owner = $user_id ORDER BY del_orders.order_id ASC");
            }

            $data['orders'] = json_decode(json_encode($orders),true);
            if(isset($data['orders'][0]))
            {
                $data['start_date'] = $start_date_glob;
                $data['end_date'] = $end_date_glob;
                $data['period_orders'] = array();
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
                    if(session('role') == 'superuser')
                    {
                        $foryearin = DB::select("SELECT DISTINCT del_orders.* FROM del_orders WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.status IN ('Assigned','Pending','Accepted','InProgress')");
                        $foryearcom = DB::select("SELECT DISTINCT del_orders.* FROM del_orders WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.status IN ('Picked up','Collected','Delivered')");
                    }
                    elseif(session('role') == 'user')
                    {
                        $user_id = session('user_id');
                        $foryearin = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.status IN ('Assigned','Pending','Accepted','InProgress') AND del_orders.lead_id = leads.id AND leads.lead_owner = $user_id");
                        $foryearcom = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.status IN ('Picked up','Collected','Delivered') AND del_orders.lead_id = leads.id AND leads.lead_owner = $user_id");
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
    public function leadsCount($filter)
    {
        if($filter == "All")
        {
            $data['xAxis'] = 'Year';
            if(session('role') == 'superuser')
            {
                $lead = DB::select("SELECT * FROM leads");
            }
            elseif(session('role') == 'user')
            {
                $user_id = session('user_id');
                $lead = DB::select("SELECT * FROM leads WHERE leads.lead_owner = $user_id");
            }
            $leads = json_decode(json_encode($lead), true);
            if(isset($leads[0]))
            {
                $data['start_year'] = date('Y',strtotime($leads[0]['creation_date']));
                $data['end_year'] = date('Y');
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
        elseif($filter == "Year")
        {
            $year = date('Y');
            $data['xAxis'] = 'Month of '.$year;
            $start_date = $year.'-01-01';
            $end_date = $year.'-12-31';
            if(session('role') == 'superuser')
            {
                $lead = DB::select("SELECT * FROM leads WHERE creation_date BETWEEN '$start_date' AND '$end_date'");
            }
            elseif(session('role') == 'user')
            {
                $user_id = session('user_id');
                $lead = DB::select("SELECT * FROM leads WHERE leads.lead_owner = $user_id AND creation_date BETWEEN '$start_date' AND '$end_date'");
            }
            $leads = json_decode(json_encode($lead), true);
            if(isset($leads[0]))
            {
                $data['start_year'] = date('Y',strtotime($leads[0]['creation_date']));
                $data['end_year'] = date('Y');
                $data['period_leads'] = array();
                $data['in_process_lead'] = array();
                $data['closed_lead'] = array();
                $data['converted_lead'] = array();
                for($month=1; $month<=12; $month++)
                {
                    $start_date = $year.'-'.$month.'-01';
                    $end_date = $year.'-'.$month.'-31';
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
        elseif($filter == "Month")
        {
            $year = date('Y');
            $data['xAxis'] = 'Days of '.date('M');
            $month = date('m');
            $last_day = date('t');
            $start_date = $year.'-'.$month.'-01';
            $end_date = $year.'-'.$month.'-'.$last_day;
            if(session('role') == 'superuser')
            {
                $lead = DB::select("SELECT * FROM leads WHERE creation_date BETWEEN '$start_date' AND '$end_date'");
            }
            elseif(session('role') == 'user')
            {
                $user_id = session('user_id');
                $lead = DB::select("SELECT * FROM leads WHERE leads.lead_owner = $user_id AND creation_date BETWEEN '$start_date' AND '$end_date'");
            }
            $leads = json_decode(json_encode($lead), true);
            if(isset($leads[0]))
            {
                $data['start_year'] = date('Y',strtotime($leads[0]['creation_date']));
                $data['end_year'] = date('Y');
                $data['period_leads'] = array();
                $data['in_process_lead'] = array();
                $data['closed_lead'] = array();
                $data['converted_lead'] = array();
                for($day=1; $day<=$last_day; $day++)
                {
                    $start_date = $year.'-'.$month.'-'.$day;
                    $end_date = $year.'-'.$month.'-'.$day;
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
        elseif($filter == "Week")
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
            if(session('role') == 'superuser')
            {
                $lead = DB::select("SELECT * FROM leads WHERE creation_date BETWEEN '$start_date_glob' AND '$end_date_glob'");
            }
            elseif(session('role') == 'user')
            {
                $user_id = session('user_id');
                $lead = DB::select("SELECT * FROM leads WHERE leads.lead_owner = $user_id AND creation_date BETWEEN '$start_date_glob' AND '$end_date_glob'");
            }
            $leads = json_decode(json_encode($lead), true);
            if(isset($leads[0]))
            {
                $data['start_year'] = date('Y',strtotime($leads[0]['creation_date']));
                $data['end_year'] = date('Y');
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

    // public function dashboard()
    // {
    //     $isLoggedIn = $this->isLoggedIn();
    //     if($isLoggedIn == 'false')
    //     {
    //         $url = url('/');
    //         return redirect()->to($url);
    //     }
    //     $data = $this->ordersCount('Week');
    //     $data += $this->leadsCount('Week');
        
    //     return view('dashboard',$data);
    // }
    // public function ordersCount($filter)
    // {
    //     $data['order_filter'] = $filter;
    //     if($filter == 'All')
    //     {
    //         if(session('role') == 'superuser')
    //         {
    //             $orders = DB::select("SELECT * FROM del_orders WHERE status != 'Closed' ORDER BY order_id ASC");
    //         }
    //         elseif(session('role') == 'user')
    //         {
    //             $user_id = session('user_id');
    //             $orders = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE del_orders.status != 'Closed' AND leads.id = del_orders.lead_id AND leads.lead_owner = $user_id ORDER BY del_orders.order_id ASC");
    //         }
    //         $data['orders'] = json_decode(json_encode($orders),true);
    //         if(isset($data['orders'][0]))
    //         {
    //             $data['start_year'] = date('Y',strtotime($data['orders'][0]['DelDate']));
    //             $data['end_year'] = date('Y');
    //             $data['period_orders'] = array();
    //             $data['incomplete'] = array();
    //             $data['completed'] = array();
    //             for($year=$data['start_year']; $year<=$data['end_year']; $year++)
    //             {
    //                 $start_date = '01-01-'.$year;
    //                 $end_date = '31-12-'.$year;
    //                 array_push($data['period_orders'],$year);
    //                 if(session('role') == 'superuser')
    //                 {
    //                     $foryearin = DB::select("SELECT DISTINCT del_orders.* FROM del_orders WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.status IN ('Assigned','Pending','Accepted','InProgress')");
    //                     $foryearcom = DB::select("SELECT DISTINCT del_orders.* FROM del_orders WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.status IN ('Picked up','Collected','Delivered')");
    //                 }
    //                 elseif(session('role') == 'user')
    //                 {
    //                     $user_id = session('user_id');
    //                     $foryearin = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.status IN ('Assigned','Pending','Accepted','InProgress') AND del_orders.lead_id = leads.id AND leads.lead_owner = $user_id");
    //                     $foryearcom = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.status IN ('Picked up','Collected','Delivered') AND del_orders.lead_id = leads.id AND leads.lead_owner = $user_id");
    //                 }
    //                 $foryears = json_decode(json_encode($foryearin), true);
    //                 if(isset($foryears[0]))
    //                 {
    //                     array_push($data['incomplete'],count($foryears));
    //                 }
    //                 else
    //                 {
    //                     array_push($data['incomplete'],0);
    //                 }
    
    //                 $foryearscom = json_decode(json_encode($foryearcom), true);
    //                 if(isset($foryearscom[0]))
    //                 {
    //                     array_push($data['completed'],count($foryearscom));
    //                 }
    //                 else
    //                 {
    //                     array_push($data['completed'],0);
    //                 }
    //                 //array_push($data['completed'],count($foryearscom));
    //             }
    //         }
    //         else
    //         {
    //             $data['period_orders'] = array();
    //             $data['incomplete'] = array();
    //             $data['completed'] = array();
    //             array_push($data['period_orders'],0);
    //             array_push($data['incomplete'],0);
    //             array_push($data['completed'],0);
    //         }
    //     }
    //     elseif($filter == 'Year')
    //     {
    //         $year = date('Y');
    //         $start_date = '01-01-'.$year;
    //         $end_date = '31-12-'.$year;
    //         if(session('role') == 'superuser')
    //         {
    //             $orders = DB::select("SELECT * FROM del_orders WHERE status != 'Closed' AND STR_TO_DATE(DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') ORDER BY order_id ASC");
    //         }
    //         elseif(session('role') == 'user')
    //         {
    //             $user_id = session('user_id');
    //             $orders = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE del_orders.status != 'Closed' AND STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND leads.id = del_orders.lead_id AND leads.lead_owner = $user_id ORDER BY del_orders.order_id ASC");
    //         }

    //         $data['orders'] = json_decode(json_encode($orders),true);
    //         if(isset($data['orders'][0]))
    //         {
    //             // $data['start_year'] = date('Y',strtotime($data['orders'][0]['DelDate']));
    //             // $data['end_year'] = date('Y');
    //             $data['start_month'] = $start_date;
    //             $data['end_month'] = $end_date;
    //             $data['period_orders'] = array();
    //             $data['incomplete'] = array();
    //             $data['completed'] = array();
    //             for($month=1; $month<=12; $month++)
    //             {
    //                 $start_date = '01-'.$month.'-'.$year;
    //                 $end_date = '31-'.$month.'-'.$year;
    //                 $monthName = date('F',mktime(0, 0, 0, $month, 10));
    //                 array_push($data['period_orders'],$month);
    //                 if(session('role') == 'superuser')
    //                 {
    //                     $foryearin = DB::select("SELECT DISTINCT del_orders.* FROM del_orders WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.status IN ('Assigned','Pending','Accepted','InProgress')");
    //                     $foryearcom = DB::select("SELECT DISTINCT del_orders.* FROM del_orders WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.status IN ('Picked up','Collected','Delivered')");
    //                 }
    //                 elseif(session('role') == 'user')
    //                 {
    //                     $user_id = session('user_id');
    //                     $foryearin = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.status IN ('Assigned','Pending','Accepted','InProgress') AND del_orders.lead_id = leads.id AND leads.lead_owner = $user_id");
    //                     $foryearcom = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.status IN ('Picked up','Collected','Delivered') AND del_orders.lead_id = leads.id AND leads.lead_owner = $user_id");
    //                 }
    //                 $foryears = json_decode(json_encode($foryearin), true);
    //                 if(isset($foryears[0]))
    //                 {
    //                     array_push($data['incomplete'],count($foryears));
    //                 }
    //                 else
    //                 {
    //                     array_push($data['incomplete'],0);
    //                 }
    
    //                 $foryearscom = json_decode(json_encode($foryearcom), true);
    //                 if(isset($foryearscom[0]))
    //                 {
    //                     array_push($data['completed'],count($foryearscom));
    //                 }
    //                 else
    //                 {
    //                     array_push($data['completed'],0);
    //                 }
    //                 //array_push($data['completed'],count($foryearscom));
    //             }
    //         }
    //         else
    //         {
    //             $data['period_orders'] = array();
    //             $data['incomplete'] = array();
    //             $data['completed'] = array();
    //             array_push($data['period_orders'],0);
    //             array_push($data['incomplete'],0);
    //             array_push($data['completed'],0);
    //         }
    //     }
    //     elseif($filter == 'Month')
    //     {
    //         $year = date('Y');
    //         $month = date('m');
    //         $last_day = date('t');
    //         $start_date = '01-'.$month.'-'.$year;
    //         $end_date = $last_day.'-'.$month.'-'.$year;
    //         if(session('role') == 'superuser')
    //         {
    //             $orders = DB::select("SELECT * FROM del_orders WHERE status != 'Closed' AND STR_TO_DATE(DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') ORDER BY order_id ASC");
    //         }
    //         elseif(session('role') == 'user')
    //         {
    //             $user_id = session('user_id');
    //             $orders = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE del_orders.status != 'Closed' AND STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND leads.id = del_orders.lead_id AND leads.lead_owner = $user_id ORDER BY del_orders.order_id ASC");
    //         }

    //         $data['orders'] = json_decode(json_encode($orders),true);
    //         if(isset($data['orders'][0]))
    //         {
    //             $data['start_date'] = $start_date;
    //             $data['end_date'] = $end_date;
    //             $data['period_orders'] = array();
    //             $data['incomplete'] = array();
    //             $data['completed'] = array();
    //             for($day=1; $day<=$last_day; $day++)
    //             {
    //                 $start_date = $day.'-'.$month.'-'.$year;
    //                 $end_date = $day.'-'.$month.'-'.$year;
    //                 array_push($data['period_orders'],$day);
    //                 if(session('role') == 'superuser')
    //                 {
    //                     $foryearin = DB::select("SELECT DISTINCT del_orders.* FROM del_orders WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.status IN ('Assigned','Pending','Accepted','InProgress')");
    //                     $foryearcom = DB::select("SELECT DISTINCT del_orders.* FROM del_orders WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.status IN ('Picked up','Collected','Delivered')");
    //                 }
    //                 elseif(session('role') == 'user')
    //                 {
    //                     $user_id = session('user_id');
    //                     $foryearin = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.status IN ('Assigned','Pending','Accepted','InProgress') AND del_orders.lead_id = leads.id AND leads.lead_owner = $user_id");
    //                     $foryearcom = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.status IN ('Picked up','Collected','Delivered') AND del_orders.lead_id = leads.id AND leads.lead_owner = $user_id");
    //                 }
    //                 $foryears = json_decode(json_encode($foryearin), true);
    //                 if(isset($foryears[0]))
    //                 {
    //                     array_push($data['incomplete'],count($foryears));
    //                 }
    //                 else
    //                 {
    //                     array_push($data['incomplete'],0);
    //                 }
    
    //                 $foryearscom = json_decode(json_encode($foryearcom), true);
    //                 if(isset($foryearscom[0]))
    //                 {
    //                     array_push($data['completed'],count($foryearscom));
    //                 }
    //                 else
    //                 {
    //                     array_push($data['completed'],0);
    //                 }
    //                 //array_push($data['completed'],count($foryearscom));
    //             }
    //         }
    //         else
    //         {
    //             $data['period_orders'] = array();
    //             $data['incomplete'] = array();
    //             $data['completed'] = array();
    //             array_push($data['period_orders'],0);
    //             array_push($data['incomplete'],0);
    //             array_push($data['completed'],0);
    //         }
    //     }
    //     elseif($filter == 'Week')
    //     {
    //         $year = date('Y');
    //         $month = date('m');
    //         // $last_day = date('t');
    //         // $start_date = '01-'.$month.'-'.$year;
    //         // $end_date = $last_day.'-'.$month.'-'.$year;
    //         $end_date = date('d-m-Y');
    //         $start_date = date('d-m-Y',strtotime("-7 days", strtotime($end_date)));
    //         $end_day = date('d');
    //         $start_day = date('d',strtotime("-7 days", strtotime($end_date)));
    //         if(session('role') == 'superuser')
    //         {
    //             $orders = DB::select("SELECT * FROM del_orders WHERE status != 'Closed' AND STR_TO_DATE(DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') ORDER BY order_id ASC");
    //         }
    //         elseif(session('role') == 'user')
    //         {
    //             $user_id = session('user_id');
    //             $orders = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE del_orders.status != 'Closed' AND STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND leads.id = del_orders.lead_id AND leads.lead_owner = $user_id ORDER BY del_orders.order_id ASC");
    //         }

    //         $data['orders'] = json_decode(json_encode($orders),true);
    //         if(isset($data['orders'][0]))
    //         {
    //             $data['start_date'] = $start_date;
    //             $data['end_date'] = $end_date;
    //             $data['period_orders'] = array();
    //             $data['incomplete'] = array();
    //             $data['completed'] = array();
    //             for($day=$start_day; $day<=$end_day; $day++)
    //             {
    //                 $start_date = $day.'-'.$month.'-'.$year;
    //                 $end_date = $day.'-'.$month.'-'.$year;
    //                 array_push($data['period_orders'],$day);
    //                 if(session('role') == 'superuser')
    //                 {
    //                     $foryearin = DB::select("SELECT DISTINCT del_orders.* FROM del_orders WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.status IN ('Assigned','Pending','Accepted','InProgress')");
    //                     $foryearcom = DB::select("SELECT DISTINCT del_orders.* FROM del_orders WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.status IN ('Picked up','Collected','Delivered')");
    //                 }
    //                 elseif(session('role') == 'user')
    //                 {
    //                     $user_id = session('user_id');
    //                     $foryearin = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.status IN ('Assigned','Pending','Accepted','InProgress') AND del_orders.lead_id = leads.id AND leads.lead_owner = $user_id");
    //                     $foryearcom = DB::select("SELECT DISTINCT del_orders.* FROM del_orders,leads WHERE STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y') AND del_orders.status IN ('Picked up','Collected','Delivered') AND del_orders.lead_id = leads.id AND leads.lead_owner = $user_id");
    //                 }
    //                 $foryears = json_decode(json_encode($foryearin), true);
    //                 if(isset($foryears[0]))
    //                 {
    //                     array_push($data['incomplete'],count($foryears));
    //                 }
    //                 else
    //                 {
    //                     array_push($data['incomplete'],0);
    //                 }
    
    //                 $foryearscom = json_decode(json_encode($foryearcom), true);
    //                 if(isset($foryearscom[0]))
    //                 {
    //                     array_push($data['completed'],count($foryearscom));
    //                 }
    //                 else
    //                 {
    //                     array_push($data['completed'],0);
    //                 }
    //                 //array_push($data['completed'],count($foryearscom));
    //             }
    //         }
    //         else
    //         {
    //             $data['period_orders'] = array();
    //             $data['incomplete'] = array();
    //             $data['completed'] = array();
    //             array_push($data['period_orders'],0);
    //             array_push($data['incomplete'],0);
    //             array_push($data['completed'],0);
    //         }
    //     }
    //     $data['total_orders'] = count($data['orders']);
        
    //     $all_pickups = 0;
    //     $all_deliveries = 0;
    //     $all_collections = 0;

    //     $completed_pickups = 0;
    //     $completed_deliveries = 0;
    //     $completed_collections = 0;

    //     $incompleted_pending = 0;
    //     $incompleted_assigned = 0;
    //     $incompleted_accepted = 0;
    //     $incompleted_in_progress = 0;

    //     if(isset($data['orders'][0]))
    //     {
    //         foreach($data['orders'] as $order)
    //         {
    //             if($order['deliverypickup'] == 'Delivery')
    //             {
    //                 $all_deliveries++;
    //                 if($order['status'] == 'Delivered')
    //                 {
    //                     $completed_deliveries++;
    //                 }
    //                 else
    //                 {
    //                     if($order['status'] == 'Pending')
    //                     {
    //                         $incompleted_pending++;
    //                     }
    //                     elseif($order['status'] == 'Assigned')
    //                     {
    //                         $incompleted_assigned++;
    //                     }
    //                     elseif($order['status'] == 'Accepted')
    //                     {
    //                         $incompleted_accepted++;
    //                     }
    //                     elseif($order['status'] == 'InProgress')
    //                     {
    //                         $incompleted_in_progress++;
    //                     }
    //                     else
    //                     {
    //                         continue;
    //                     }
    //                 }
    //             }
    //             elseif($order['deliverypickup'] == 'Pick Up' || $order['deliverypickup'] == 'Pickup' || $order['deliverypickup'] == 'PickUp')
    //             {
    //                 $all_pickups++;
    //                 if($order['status'] == 'Picked up')
    //                 {
    //                     $completed_pickups++;
    //                 }
    //                 else
    //                 {
    //                     if($order['status'] == 'Pending')
    //                     {
    //                         $incompleted_pending++;
    //                     }
    //                     elseif($order['status'] == 'Assigned')
    //                     {
    //                         $incompleted_assigned++;
    //                     }
    //                     elseif($order['status'] == 'Accepted')
    //                     {
    //                         $incompleted_accepted++;
    //                     }
    //                     elseif($order['status'] == 'InProgress')
    //                     {
    //                         $incompleted_in_progress++;
    //                     }
    //                     else
    //                     {
    //                         continue;
    //                     }
    //                 }
    //             }
    //             elseif($order['deliverypickup'] == 'Collection')
    //             {
    //                 $all_collections++;
    //                 if($order['status'] == 'Collected')
    //                 {
    //                     $completed_collections++;
    //                 }
    //                 else
    //                 {
    //                     if($order['status'] == 'Pending')
    //                     {
    //                         $incompleted_pending++;
    //                     }
    //                     elseif($order['status'] == 'Assigned')
    //                     {
    //                         $incompleted_assigned++;
    //                     }
    //                     elseif($order['status'] == 'Accepted')
    //                     {
    //                         $incompleted_accepted++;
    //                     }
    //                     elseif($order['status'] == 'InProgress')
    //                     {
    //                         $incompleted_in_progress++;
    //                     }
    //                     else
    //                     {
    //                         continue;
    //                     }
    //                 }
    //             }
    //             else
    //             {
    //                 continue;
    //             }
    //         }
    //     }
    //     $data['all_pickups'] = $all_pickups;
    //     $data['all_deliveries'] = $all_deliveries;
    //     $data['all_collections'] = $all_collections;

    //     $data['completed_pickups'] = $completed_pickups;
    //     $data['completed_deliveries'] = $completed_deliveries;
    //     $data['completed_collections'] = $completed_collections;

    //     $data['incompleted_pending'] = $incompleted_pending;
    //     $data['incompleted_accepted'] = $incompleted_accepted;
    //     $data['incompleted_assigned'] = $incompleted_assigned;
    //     $data['incompleted_in_progress'] = $incompleted_in_progress;

    //     $data['completed_total'] = $completed_pickups + $completed_deliveries + $completed_collections;
    //     $data['incompleted_total'] = $incompleted_pending + $incompleted_accepted + $incompleted_assigned + $incompleted_in_progress;
    //     return $data;
    // }
    // public function leadsCount($filter)
    // {
    //     $data['lead_filter'] = $filter;
    //     if($filter == "All")
    //     {
    //         if(session('role') == 'superuser')
    //         {
    //             $lead = DB::select("SELECT * FROM leads");
    //         }
    //         elseif(session('role') == 'user')
    //         {
    //             $user_id = session('user_id');
    //             $lead = DB::select("SELECT * FROM leads WHERE leads.lead_owner = $user_id");
    //         }
    //         $leads = json_decode(json_encode($lead), true);
    //         if(isset($leads[0]))
    //         {
    //             $data['start_year'] = date('Y',strtotime($leads[0]['creation_date']));
    //             $data['end_year'] = date('Y');
    //             $data['period_leads'] = array();
    //             $data['in_process_lead'] = array();
    //             $data['closed_lead'] = array();
    //             $data['converted_lead'] = array();
    //             for($year=$data['start_year']; $year<=$data['end_year']; $year++)
    //             {
    //                 $start_date = $year.'-01-01';
    //                 $end_date = $year.'-12-31';
    //                 array_push($data['period_leads'],$year);
    //                 if(session('role') == 'superuser')
    //                 {
    //                     $foryear = DB::select("SELECT * FROM leads WHERE lead_status = 'Work In Process' AND creation_date BETWEEN '$start_date' AND '$end_date'");
    //                 }
    //                 elseif(session('role') == 'user')
    //                 {
    //                     $user_id = session('user_id');
    //                     $foryear = DB::select("SELECT * FROM leads WHERE lead_status = 'Work In Process' AND creation_date BETWEEN '$start_date' AND '$end_date' AND leads.lead_owner = $user_id");
    //                 }
    //                 $foryears = json_decode(json_encode($foryear), true);
    //                 array_push($data['in_process_lead'],count($foryears));
    //                 if(session('role') == 'superuser')
    //                 {
    //                     $foryear = DB::select("SELECT * FROM leads WHERE lead_status NOT IN ('Work In Process','Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date'");
    //                 }
    //                 elseif(session('role') == 'user')
    //                 {
    //                     $user_id = session('user_id');
    //                     $foryear = DB::select("SELECT * FROM leads WHERE lead_status NOT IN ('Work In Process','Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date' AND leads.lead_owner = $user_id");
    //                 }
    //                 // $foryear = DB::select("SELECT * FROM leads WHERE lead_status NOT IN ('Work In Process','Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date'");
    //                 $foryears = json_decode(json_encode($foryear), true);
    //                 array_push($data['closed_lead'],count($foryears));
    //                 if(session('role') == 'superuser')
    //                 {
    //                     $foryear = DB::select("SELECT * FROM leads WHERE lead_status IN ('Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date'");
    //                 }
    //                 elseif(session('role') == 'user')
    //                 {
    //                     $user_id = session('user_id');
    //                     $foryear = DB::select("SELECT * FROM leads WHERE lead_status IN ('Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date' AND leads.lead_owner = $user_id");
    //                 }
    //                 // $foryear = DB::select("SELECT * FROM leads WHERE lead_status IN ('Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date'");
    //                 $foryears = json_decode(json_encode($foryear), true);
    //                 array_push($data['converted_lead'],count($foryears));
    
    //             }
    //         }
    //         else
    //         {
    //             $data['period_leads'] = array();
    //             $data['in_process_lead'] = array();
    //             $data['closed_lead'] = array();
    //             $data['converted_lead'] = array();

    //             array_push($data['period_leads'],0);
    //             array_push($data['in_process_lead'],0);
    //             array_push($data['closed_lead'],0);
    //             array_push($data['converted_lead'],0);
    //         }
            
    //     }
    //     elseif($filter == "Year")
    //     {
    //         $year = date('Y');
    //         $start_date = $year.'-01-01';
    //         $end_date = $year.'-12-31';
    //         if(session('role') == 'superuser')
    //         {
    //             $lead = DB::select("SELECT * FROM leads WHERE creation_date BETWEEN '$start_date' AND '$end_date'");
    //         }
    //         elseif(session('role') == 'user')
    //         {
    //             $user_id = session('user_id');
    //             $lead = DB::select("SELECT * FROM leads WHERE leads.lead_owner = $user_id AND creation_date BETWEEN '$start_date' AND '$end_date'");
    //         }
    //         $leads = json_decode(json_encode($lead), true);
    //         if(isset($leads[0]))
    //         {
    //             $data['start_year'] = date('Y',strtotime($leads[0]['creation_date']));
    //             $data['end_year'] = date('Y');
    //             $data['period_leads'] = array();
    //             $data['in_process_lead'] = array();
    //             $data['closed_lead'] = array();
    //             $data['converted_lead'] = array();
    //             for($month=1; $month<=12; $month++)
    //             {
    //                 $start_date = $year.'-'.$month.'-01';
    //                 $end_date = $year.'-'.$month.'-31';
    //                 array_push($data['period_leads'],$month);
    //                 if(session('role') == 'superuser')
    //                 {
    //                     $foryear = DB::select("SELECT * FROM leads WHERE lead_status = 'Work In Process' AND creation_date BETWEEN '$start_date' AND '$end_date'");
    //                 }
    //                 elseif(session('role') == 'user')
    //                 {
    //                     $user_id = session('user_id');
    //                     $foryear = DB::select("SELECT * FROM leads WHERE lead_status = 'Work In Process' AND creation_date BETWEEN '$start_date' AND '$end_date' AND leads.lead_owner = $user_id");
    //                 }
    //                 $foryears = json_decode(json_encode($foryear), true);
    //                 array_push($data['in_process_lead'],count($foryears));
    //                 if(session('role') == 'superuser')
    //                 {
    //                     $foryear = DB::select("SELECT * FROM leads WHERE lead_status NOT IN ('Work In Process','Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date'");
    //                 }
    //                 elseif(session('role') == 'user')
    //                 {
    //                     $user_id = session('user_id');
    //                     $foryear = DB::select("SELECT * FROM leads WHERE lead_status NOT IN ('Work In Process','Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date' AND leads.lead_owner = $user_id");
    //                 }
    //                 // $foryear = DB::select("SELECT * FROM leads WHERE lead_status NOT IN ('Work In Process','Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date'");
    //                 $foryears = json_decode(json_encode($foryear), true);
    //                 array_push($data['closed_lead'],count($foryears));
    //                 if(session('role') == 'superuser')
    //                 {
    //                     $foryear = DB::select("SELECT * FROM leads WHERE lead_status IN ('Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date'");
    //                 }
    //                 elseif(session('role') == 'user')
    //                 {
    //                     $user_id = session('user_id');
    //                     $foryear = DB::select("SELECT * FROM leads WHERE lead_status IN ('Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date' AND leads.lead_owner = $user_id");
    //                 }
    //                 // $foryear = DB::select("SELECT * FROM leads WHERE lead_status IN ('Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date'");
    //                 $foryears = json_decode(json_encode($foryear), true);
    //                 array_push($data['converted_lead'],count($foryears));
    
    //             }
    //         }
    //         else
    //         {
    //             $data['period_leads'] = array();
    //             $data['in_process_lead'] = array();
    //             $data['closed_lead'] = array();
    //             $data['converted_lead'] = array();

    //             array_push($data['period_leads'],0);
    //             array_push($data['in_process_lead'],0);
    //             array_push($data['closed_lead'],0);
    //             array_push($data['converted_lead'],0);
    //         }
            
    //     }
    //     elseif($filter == "Month")
    //     {
    //         $year = date('Y');
    //         $month = date('m');
    //         $last_day = date('t');
    //         $start_date = $year.'-'.$month.'-01';
    //         $end_date = $year.'-'.$month.'-'.$last_day;
    //         if(session('role') == 'superuser')
    //         {
    //             $lead = DB::select("SELECT * FROM leads WHERE creation_date BETWEEN '$start_date' AND '$end_date'");
    //         }
    //         elseif(session('role') == 'user')
    //         {
    //             $user_id = session('user_id');
    //             $lead = DB::select("SELECT * FROM leads WHERE leads.lead_owner = $user_id AND creation_date BETWEEN '$start_date' AND '$end_date'");
    //         }
    //         $leads = json_decode(json_encode($lead), true);
    //         if(isset($leads[0]))
    //         {
    //             $data['start_year'] = date('Y',strtotime($leads[0]['creation_date']));
    //             $data['end_year'] = date('Y');
    //             $data['period_leads'] = array();
    //             $data['in_process_lead'] = array();
    //             $data['closed_lead'] = array();
    //             $data['converted_lead'] = array();
    //             for($day=1; $day<=$last_day; $day++)
    //             {
    //                 $start_date = $year.'-'.$month.'-'.$day;
    //                 $end_date = $year.'-'.$month.'-'.$day;
    //                 array_push($data['period_leads'],$day);
    //                 if(session('role') == 'superuser')
    //                 {
    //                     $foryear = DB::select("SELECT * FROM leads WHERE lead_status = 'Work In Process' AND creation_date BETWEEN '$start_date' AND '$end_date'");
    //                 }
    //                 elseif(session('role') == 'user')
    //                 {
    //                     $user_id = session('user_id');
    //                     $foryear = DB::select("SELECT * FROM leads WHERE lead_status = 'Work In Process' AND creation_date BETWEEN '$start_date' AND '$end_date' AND leads.lead_owner = $user_id");
    //                 }
    //                 $foryears = json_decode(json_encode($foryear), true);
    //                 array_push($data['in_process_lead'],count($foryears));
    //                 if(session('role') == 'superuser')
    //                 {
    //                     $foryear = DB::select("SELECT * FROM leads WHERE lead_status NOT IN ('Work In Process','Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date'");
    //                 }
    //                 elseif(session('role') == 'user')
    //                 {
    //                     $user_id = session('user_id');
    //                     $foryear = DB::select("SELECT * FROM leads WHERE lead_status NOT IN ('Work In Process','Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date' AND leads.lead_owner = $user_id");
    //                 }
    //                 // $foryear = DB::select("SELECT * FROM leads WHERE lead_status NOT IN ('Work In Process','Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date'");
    //                 $foryears = json_decode(json_encode($foryear), true);
    //                 array_push($data['closed_lead'],count($foryears));
    //                 if(session('role') == 'superuser')
    //                 {
    //                     $foryear = DB::select("SELECT * FROM leads WHERE lead_status IN ('Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date'");
    //                 }
    //                 elseif(session('role') == 'user')
    //                 {
    //                     $user_id = session('user_id');
    //                     $foryear = DB::select("SELECT * FROM leads WHERE lead_status IN ('Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date' AND leads.lead_owner = $user_id");
    //                 }
    //                 // $foryear = DB::select("SELECT * FROM leads WHERE lead_status IN ('Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date'");
    //                 $foryears = json_decode(json_encode($foryear), true);
    //                 array_push($data['converted_lead'],count($foryears));
    
    //             }
    //         }
    //         else
    //         {
    //             $data['period_leads'] = array();
    //             $data['in_process_lead'] = array();
    //             $data['closed_lead'] = array();
    //             $data['converted_lead'] = array();

    //             array_push($data['period_leads'],0);
    //             array_push($data['in_process_lead'],0);
    //             array_push($data['closed_lead'],0);
    //             array_push($data['converted_lead'],0);
    //         }
            
    //     }
    //     elseif($filter == "Week")
    //     {
    //         $year = date('Y');
    //         $month = date('m');
    //         // $last_day = date('t');
    //         // $start_date = '01-'.$month.'-'.$year;
    //         // $end_date = $last_day.'-'.$month.'-'.$year;
    //         $end_date = date('Y-m-d');
    //         $start_date = date('Y-m-d',strtotime("-7 days", strtotime($end_date)));
    //         $end_day = date('d');
    //         $start_day = date('d',strtotime("-7 days", strtotime($end_date)));
    //         if(session('role') == 'superuser')
    //         {
    //             $lead = DB::select("SELECT * FROM leads WHERE creation_date BETWEEN '$start_date' AND '$end_date'");
    //         }
    //         elseif(session('role') == 'user')
    //         {
    //             $user_id = session('user_id');
    //             $lead = DB::select("SELECT * FROM leads WHERE leads.lead_owner = $user_id AND creation_date BETWEEN '$start_date' AND '$end_date'");
    //         }
    //         $leads = json_decode(json_encode($lead), true);
    //         if(isset($leads[0]))
    //         {
    //             $data['start_year'] = date('Y',strtotime($leads[0]['creation_date']));
    //             $data['end_year'] = date('Y');
    //             $data['period_leads'] = array();
    //             $data['in_process_lead'] = array();
    //             $data['closed_lead'] = array();
    //             $data['converted_lead'] = array();
    //             for($day=$start_day; $day<=$end_day; $day++)
    //             {
    //                 $start_date = $year.'-'.$month.'-'.$day;
    //                 $end_date = $year.'-'.$month.'-'.$day;
    //                 array_push($data['period_leads'],$day);
    //                 if(session('role') == 'superuser')
    //                 {
    //                     $foryear = DB::select("SELECT * FROM leads WHERE lead_status = 'Work In Process' AND creation_date BETWEEN '$start_date' AND '$end_date'");
    //                 }
    //                 elseif(session('role') == 'user')
    //                 {
    //                     $user_id = session('user_id');
    //                     $foryear = DB::select("SELECT * FROM leads WHERE lead_status = 'Work In Process' AND creation_date BETWEEN '$start_date' AND '$end_date' AND leads.lead_owner = $user_id");
    //                 }
    //                 $foryears = json_decode(json_encode($foryear), true);
    //                 array_push($data['in_process_lead'],count($foryears));
    //                 if(session('role') == 'superuser')
    //                 {
    //                     $foryear = DB::select("SELECT * FROM leads WHERE lead_status NOT IN ('Work In Process','Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date'");
    //                 }
    //                 elseif(session('role') == 'user')
    //                 {
    //                     $user_id = session('user_id');
    //                     $foryear = DB::select("SELECT * FROM leads WHERE lead_status NOT IN ('Work In Process','Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date' AND leads.lead_owner = $user_id");
    //                 }
    //                 // $foryear = DB::select("SELECT * FROM leads WHERE lead_status NOT IN ('Work In Process','Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date'");
    //                 $foryears = json_decode(json_encode($foryear), true);
    //                 array_push($data['closed_lead'],count($foryears));
    //                 if(session('role') == 'superuser')
    //                 {
    //                     $foryear = DB::select("SELECT * FROM leads WHERE lead_status IN ('Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date'");
    //                 }
    //                 elseif(session('role') == 'user')
    //                 {
    //                     $user_id = session('user_id');
    //                     $foryear = DB::select("SELECT * FROM leads WHERE lead_status IN ('Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date' AND leads.lead_owner = $user_id");
    //                 }
    //                 // $foryear = DB::select("SELECT * FROM leads WHERE lead_status IN ('Converted','Order Generated','DelBoy Assigned') AND creation_date BETWEEN '$start_date' AND '$end_date'");
    //                 $foryears = json_decode(json_encode($foryear), true);
    //                 array_push($data['converted_lead'],count($foryears));
    
    //             }
    //         }
    //         else
    //         {
    //             $data['period_leads'] = array();
    //             $data['in_process_lead'] = array();
    //             $data['closed_lead'] = array();
    //             $data['converted_lead'] = array();

    //             array_push($data['period_leads'],0);
    //             array_push($data['in_process_lead'],0);
    //             array_push($data['closed_lead'],0);
    //             array_push($data['converted_lead'],0);
    //         }
            
    //     }
    //     $data['total_leads'] = count($leads);
    //     $inprocess_count = 0;
    //     $closed_count = 0;
    //     $converted_count = 0;
    //     foreach ($leads as $lead)
    //     {
    //         if($lead['lead_status'] == "Converted" || $lead['lead_status'] == "Order Generated" || $lead['lead_status'] == "DelBoy Assigned")
    //         {
    //             $converted_count++;
    //         }
    //         elseif($lead['lead_status'] == "Work In Process")
    //         {
    //             $inprocess_count++;
    //         }
    //         else
    //         {
    //             $closed_count++;
    //         }
    //     }
    //     $data['inprocess_count'] = $inprocess_count;
    //     $data['closed_count'] = $closed_count;
    //     $data['converted_count'] = $converted_count;
    //     return $data;
    // }
    public static function get_client_ip() 
    {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
           $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
    public function validate_login(Request $request)
    {
        ini_set('display_errors', 1);
        if($_SERVER['REQUEST_METHOD']=='POST')
        {
            $user_details = new UserRegister();
            ini_set('display_errors', 1);
            if ($_POST['submit']=='Login') 
            {
                $input = $request->all();

                $ip_address = $this->get_client_ip();
                $ip_address = str_replace(".", "", $ip_address);
                $ip_address_captcha_high = config('app.ip_address_captcha_high');
                $ip_address_captcha_high = str_replace(".", "", $ip_address_captcha_high);
                // echo "High".$ip_address_captcha_high;
                $ip_address_captcha_low = config('app.ip_address_captcha_low');
                $ip_address_captcha_low = str_replace(".", "", $ip_address_captcha_low);
                // echo "Low".$ip_address_captcha_low;
                if($ip_address <= $ip_address_captcha_high && $ip_address >= $ip_address_captcha_low)
                {
                    $rules = $request->validate([
                        'username' =>'required',
                        'password' =>'required'
                        // 'captcha' => 'required|captcha'
                    ],
                    [
                        'username.required'=>'Enter your username',
                        'password.required'=>'Enter your password',
                        // 'captcha.required' => 'Enter Captcha code'
                        // 'captcha.captcha' => 'Captcha code is not same'
                    ]);
                }
                else
                {
                    $rules = $request->validate([
                        'username' =>'required',
                        'password' =>'required',
                        'captcha' => 'required|captcha'
                    ],
                    [
                        'username.required'=>'Enter your username',
                        'password.required'=>'Enter your password',
                        'captcha.required' => 'Enter Captcha code',
                        'captcha.captcha' => 'Captcha code is not same'
                    ]);
                }
                $username = $_POST['username'];
                $password = $_POST['password'];
                $user_details = DB::select("SELECT *  FROM user where username = '$username' AND password = '$password' AND flag = 'Active'");
                $data['user_details'] = json_decode(json_encode($user_details), true);
                if(isset($data['user_details'][0]))
                {
                    if ($data['user_details'][0]['role']=='admin' || $data['user_details'][0]['role']=='superuser') 
                    {
                        if(!empty($_POST["remember"]))
                        {                            
                            $sha1_value = $username.$data['user_details'][0]['role'];
                            $remember_token = sha1($sha1_value);
                            $id= $data['user_details'][0]['id'];
                            DB::update("UPDATE user SET remember_token = '$remember_token' WHERE id = $id");
                            setcookie ("remember_token",$remember_token,time()+ 1440);
                        }
                        session(['user_id' => $data['user_details'][0]['id']]);
                        session(['username' => $data['user_details'][0]['username']]);
                        session(['role' => $data['user_details'][0]['role']]);
                        session(['user_city' => $data['user_details'][0]['user_city']]);
                        session(['city_based_access' => $data['user_details'][0]['city_based_access']]);
                        session(['role_access' => $data['user_details'][0]['role_access']]);
                        session(['isLoggedIn' => 'true']);

                        //user log
                        $UserLog = new UserLog();
                        $UserLog->insert(['ip_address'=>$this->get_client_ip(),'username'=>$username,'user_role'=>$data['user_details'][0]['role']]);

                        return redirect('dashboard');
                    }
                    if ($data['user_details'][0]['role']=='user') 
                    {
                        if(!empty($_POST["remember"]))
                        {
                            $sha1_value = $username.$data['user_details'][0]['role'];
                            $remember_token = sha1($sha1_value);
                            $id= $data['user_details'][0]['id'];
                            DB::update("UPDATE user SET remember_token = '$remember_token' WHERE id = $id");                            
                            setcookie ("remember_token",$remember_token,time()+ 1440);
                        }
                        session(['user_id' => $data['user_details'][0]['id']]);
                        session(['id' => $data['user_details'][0]['id']]);
                        session(['username' => $data['user_details'][0]['username']]);
                        session(['role' => $data['user_details'][0]['role']]);
                        session(['user_city' => $data['user_details'][0]['user_city']]);
                        session(['city_based_access' => $data['user_details'][0]['city_based_access']]);
                        session(['role_access' => $data['user_details'][0]['role_access']]);
                        session(['isLoggedIn' => 'true']);
                        //userlog
                        $UserLog = new UserLog();
                        $UserLog->insert(['ip_address'=>$this->get_client_ip(),'username'=>$username,'user_role'=>$data['user_details'][0]['role']]);
                        return redirect('dashboard');
                    }
                }
                else
                {
                    return redirect('/')->with('error_login','Username and password wrong');                    
                }
            }
        }   
    }
    public function reloadCaptcha()
    {
        return response()->json(['captcha'=>captcha_img('mini')]);
    }
    //---Enter otp template for redirect---------//
    public function enter_otp()
    {
        return view('Admin/enter_otp');
    }
    public function admin_login()
    {
        ini_set('display_errors', 1);
        if($_SERVER['REQUEST_METHOD']=='GET')
        {
            if(isset($_COOKIE["remember_token"]))
            {
                $remember_token = $_COOKIE["remember_token"];
                $user_details = DB::select("SELECT * FROM user WHERE remember_token = '$remember_token'");
                $data['user_details'] = json_decode(json_encode($user_details), true);
                if(!empty($data['user_details']))
                {
                    session(['user_id' => $data['user_details'][0]['id']]);
                    session(['username' => $data['user_details'][0]['username']]);
                    session(['role' => $data['user_details'][0]['role']]);
                    session(['user_city' => $data['user_details'][0]['user_city']]);
                    session(['city_based_access' => $data['user_details'][0]['city_based_access']]);
                    session(['role_access' => $data['user_details'][0]['role_access']]);
                    session(['isLoggedIn' => 'true']);
                    return redirect('dashboard');
                }
                else
                {
                    return view('Authentication/admin_login');
                }
            }
            else
            {
                return view('Authentication/admin_login');
            }
        }
        
    }
    
    public function dashboard_fullfillment()
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
            return redirect()->to($url);
        }
        return view('Dashboard/dashboard_fullfillment');
    }
    public function dashboard_presales()
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
            return redirect()->to($url);
        }
        return view('Dashboard/dashboard_presales');
    }
    public function logout()
    {
         $request = new Request();
         session(['id' => null]);
         session(['username' => null]);
         session(['role' => null]);
         session(['role_access' => null]);
         session(['user_city' => null]);
         session(['city_based_access' => null]);
         session(['isLoggedIn' => 'false']);
         //return view('Admin/admin_login');
         return redirect('/');
    }
    public function getCount()
    {
        $leads_details = DB::select("SELECT * FROM hot_leads WHERE hot_leads_status = 'Pending'");
        $data['lead_details'] = json_decode(json_encode($leads_details),true);
        $count = count($data['lead_details']);
        echo $count;
    }
    public function notification($count) 
    {
        $date = date('Y-m-d');
        $leads_details = DB::select("SELECT * FROM hot_leads WHERE hot_leads_status = 'Pending' AND hot_leads_created_at = '$date'");
        $data['lead_details'] = json_decode(json_encode($leads_details),true);
        $newCount = count($data['lead_details']);    
        if($newCount > $count)
        {
            echo $newCount - $count;
        }
        else
        {
            echo '0';
        }
    }
}
?>