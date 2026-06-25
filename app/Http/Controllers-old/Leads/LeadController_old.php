<?php

namespace App\Http\Controllers\Leads;

use App\Http\Controllers\Controller;
use App\Models\Lead\customer_detail;
use App\Models\LinkCustDetails;
use App\Models\Lead\lead;
use App\Models\Lead\leads_log;
use App\Models\LeadsQueryLog;
// use App\Models\LinkCustDetails;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use PDF;
use Mail;
use File;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Str;
use Carbon\Carbon;
//use Storage;

class LeadController extends Controller
{
   public function isLoggedIn()
   {
      $data = session('isLoggedIn');
      //print_r($data);      
      return $data;
   }
   // ****Create New Lead****
   public function create_lead()
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
         $url = url('/');
         return redirect()->to($url);
      }
      ini_set('display_errors', 1);
      if($_SERVER['REQUEST_METHOD']=='POST')
      {
         //print_r($_POST);
         $customer_details = new customer_detail();
         $leads = new lead();
         $leads_log = new leads_log();
         $equipments = request()->get('eqipments');
         $equipments_requirements = json_encode($equipments);
         //print_r($equipments_requirements);
         if($_POST['submit']=='submit')
         {
            
            try {
               DB::enableQueryLog();
               $current_time = new DateTime("now", new DateTimeZone('Asia/Kolkata') );
               $current_time = $current_time->format('H:i:s');
               $creation_date = request()->get('creation_date')." ".$current_time;
               $cust_date = date('Y-m-d');
               $cutomer_details_insertData = [
                  'customer_name' => request()->get('cust_name'),
                  'cust_date' => request()->get('creation_date'),
                  'address_line_1' => request()->get('address_line_1'),
                  'address_line_2' => request()->get('address_line_2'),
                  'landmark' => request()->get('landmark'),
                  'area' => request()->get('area'),
                  'city' => request()->get('city'),
                  'pincode' => request()->get('pincode'),
                  'state' => request()->get('state'),
                  'country' => request()->get('country'),
                  'location' => request()->get('location'),
                  'email_id' => request()->get('email'),
                  'primary_contact_no' => request()->get('primary_contact_no'),
                  'secondary_contact_no' => request()->get('secondary_contact_no'),
                  'refered_by' => request()->get('refered_by'),
                  'customer_type' => request()->get('customer_type'),
                  'created_at'=>$creation_date,
                  'created_by' => session('username')
               ];
               $customer_id = $customer_details->insertGetId($cutomer_details_insertData);

               $leads_insertData = [
                  'customer_id' => $customer_id,
                  'creation_date' => request()->get('creation_date'), 
                  'patient_name' => request()->get('patient_name'),
                  'patient_age' => request()->get('patient_age'),
                  'doctor_name' => request()->get('doctor_name'),
                  'hospital_name' => request()->get('hospital_name'),
                  'therapeutic_requirement' => request()->get('therapeutic_requirement'),
                  'equipment_requirement' => $equipments_requirements,
                  'lead_source' => request()->get('lead_source'),
                  'lead_value' => request()->get('lead_value'),
                  'lead_status' => 'Work In Process',
                  'lead_owner' => request()->get('lead_owner'),
                  'created_at'=>$creation_date,
                  'created_by' => session('username')
               ];
               $lead_id = $leads->insertGetId($leads_insertData);
               
               //query log insert
               $queries = DB::getQueryLog();
               $query_msg = $queries[0]['query'];
               $query_col = json_encode($queries[0]['bindings']);
               $LeadsQueryLog = new LeadsQueryLog();
               $insertQueryLog = [
                  'user_id'=>session('user_id'),
                  'operation'=>'Create Lead-submit button',
                  'query'=>$query_msg,
                  'col'=>$query_col,
                  'created_by'=>session('username')
               ];
               $LeadsQueryLog->insert($insertQueryLog);

            } catch (\Illuminate\Database\QueryException $ex) {
               $msg = $ex->getMessage();
               $LeadsQueryLog = new LeadsQueryLog();
               $insertQueryLog = [
                  'user_id'=>session('user_id'),
                  'operation'=>'Create Lead-submit button',
                  'query'=>$msg,
                  'created_by'=>session('username')
               ];
               $LeadsQueryLog->insert($insertQueryLog);
               //return redirect()->back();
            }
            
         }
         if($_POST['submit']=='check')
         {
            try {
               DB::enableQueryLog();
               $current_time = new DateTime("now", new DateTimeZone('Asia/Kolkata') );
               $current_time = $current_time->format('H:i:s');
               $creation_date = request()->get('creation_date')." ".$current_time;

               $customer_id = request()->get('customer_id');
               $cust_date = date('Y-m-d');
               $leads_insertData = [
                  'customer_id' => $customer_id,
                  'creation_date' => request()->get('creation_date'), 
                  'patient_name' => request()->get('patient_name'),
                  'patient_age' => request()->get('patient_age'),
                  'doctor_name' => request()->get('doctor_name'),
                  'hospital_name' => request()->get('hospital_name'),
                  'therapeutic_requirement' => request()->get('therapeutic_requirement'),
                  'equipment_requirement' => $equipments_requirements,
                  'lead_source' => request()->get('lead_source'),
                  'lead_value' => request()->get('lead_value'),
                  'lead_status' => 'Work In Process',
                  'lead_owner' => request()->get('lead_owner'),
                  'created_at'=>$creation_date,
                  'created_by' => session('username')
               ];
               $lead_id = $leads->insertGetId($leads_insertData);
               $queries = \DB::getQueryLog();
               $query_msg = $queries[0]['query'];
               $query_col = json_encode($queries[0]['bindings']);
               $LeadsQueryLog = new LeadsQueryLog();
               $insertQueryLog = [
                  'user_id'=>session('user_id'),
                  'operation'=>'Create Lead-check button',
                  'query'=>$query_msg,
                  'col'=>$query_col,
                  'created_by'=>session('username')
               ];
               $LeadsQueryLog->insert($insertQueryLog);
            } catch (\Illuminate\Database\QueryException $ex) {
               $msg = $ex->getMessage();
               $LeadsQueryLog = new LeadsQueryLog();
               $insertQueryLog = [
                  'user_id'=>session('user_id'),
                  'operation'=>'Create Lead-check button',
                  'query'=>$msg,
                  'created_by'=>session('username')
               ];
               $LeadsQueryLog->insert($insertQueryLog);
               //return false;
            }
            
         }
         $leads_log_data = [
            'log_lead_id' => $lead_id,
            'log_lead_status' => 'Work In Process',
            'log_date' => date('Y-m-d'),
            'log_time' => date('H:i:s'),
            'updated_by' => session('username')
         ];
         $leads_log->insert($leads_log_data);
         //----Send Message to Lead Owner about new lead-----------------------//
         $product = json_decode($equipments_requirements);
         //print_r($product);
         $equipement_details = array();
         for ($j=0; $j <count($product); $j++) 
         { 
            $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
            $product_details = json_decode(json_encode($product_details), true);
            array_push($equipement_details,$product_details[0]['product_name']);
         }
         //$equipements = json_encode($equipement_details);
         $lead_owner = request()->get('lead_owner');
         $equipments = implode(", ",$equipement_details);
         $lead_owner_details = DB::select("SELECT * FROM user WHERE id=$lead_owner");
         $data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
         $user_name = $data['lead_owner_details'][0]['username'];
         $user_contact_no = $data['lead_owner_details'][0]['contact_no'];
         //print_r($equipments);
         $mobile = request()->get('primary_contact_no');

        //-----Send Lead Creation Message to Customer-----//
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.msg91.com/api/v5/flow/",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        //CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"60cc49120eeed16fcd62d103\",\n  \"sender\": \"QUALCR\",\n  \"mobiles\": \"919920361040\",\n  \"name\": \"testing\",\n  \"orderno\": \"8512457845\",\n  \"equpname\": \"Standard Walker\",\n  \"date\": \"18-06-2021\",\n  \"amount\": \"550\"}",
        CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"60d05c8924235536765b61d4\",\n  \"sender\": \"qulcar\",\n  \"mobiles\": \"91$mobile\",\n  \"username\": \"$user_name\",\n  \"contact\": \"$user_contact_no\",\n  \"equipment\": \"$equipments\"}",
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
        
        //-----End-----//
        
         // $message = "Thank you for your inquiry at Quali55Care.com for $equipments . We assure you quick delivery and provide you with everything that you might need with the urgency and care, please contact ".$data['lead_owner_details'][0]['username']." at ".$data['lead_owner_details'][0]['contact_no']." if need any further advise/assitance";
         //$this->sendsms($mobile,$message);
         //echo $message;
         //return $this->viewAllLeads();
         //$previous_url = request()->get('previous_url');
         return redirect('view_all_leads')->with('message', 'Lead Generated Successfully');
      }
      else
      {
         $products = DB::select("SELECT * FROM products WHERE flag = 'Active'");
         $data['products'] = \json_decode(\json_encode($products), true);
         $cities = DB::select("SELECT * FROM cities");
         $data['cities'] = \json_decode(\json_encode($cities), true);
         $states = DB::select("SELECT * FROM states");
         $data['states'] = \json_decode(\json_encode($states), true);
         $countries = DB::select("SELECT * FROM countries");
         $data['countries'] = \json_decode(\json_encode($countries), true);
         $users = DB::select("SELECT * FROM user WHERE role!='vendor'");
         $data['users'] = \json_decode(\json_encode($users), true);
         return view('leads/create_lead',$data); 
      }
   }
   // ****View All Leads****
   public function viewAllLeads()
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
       return redirect()->to($url);
      }

      $past_three_days = date('Y-m-d',strtotime("-2 days"));
      // $today = date('Y-m-d');
      if(session('role') == "superuser")
      {
         $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.creation_date >= '$past_three_days' ORDER BY leads.creation_date DESC");
      }
      elseif(session('role') == "admin")
      {
         $user_city = session('user_city');
         $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.creation_date >= '$past_three_days' AND customer_details.citygroup='$user_city' ORDER BY leads.creation_date DESC");
      }
      elseif(session('role') == "user")
      {
         $lead_owner = session('user_id');
         $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.creation_date >= '$past_three_days' AND leads.lead_owner = $lead_owner ORDER BY leads.creation_date DESC");
      }
      $data['lead_details'] = json_decode(json_encode($leads), true);
      for ($i=0; $i < count($data['lead_details']); $i++) 
      { 
         $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
         $user_id = $data['lead_details'][$i]['lead_owner'];
         $lead_owner_details = DB::select("SELECT * FROM user WHERE id=$user_id");
         $data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
         $data['lead_details'][$i]['username'] =  $data['lead_owner_details'][0]['username'];
         $data['lead_details'][$i]['user_id'] =  $data['lead_owner_details'][0]['id'];
         //print_r($product);
         $equipement_details = array();
         for ($j=0; $j <count($product); $j++) 
         { 
            $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
            $product_details = json_decode(json_encode($product_details), true);
            array_push($equipement_details,$product_details[0]['product_name']);
         }
         $equipements = json_encode($equipement_details);
         $data['lead_details'][$i]['equipment_requirement'] = $equipements;
      }
      echo "<script>localStorage['filtered']='past_3_days';</script>";
      return view('leads/viewAllLeads',$data);
   }

   //new page view all leads with filter
   public function ViewAllLeads_new(Request $request)
	{
      $get_min_date = lead::min('creation_date');
      $get_max_date = lead::max('creation_date');

		$get_lead_sources = DB::table('leads')->select('lead_source')->distinct('lead_source')->get();
		$lead_sources_arr = array_column($get_lead_sources->toArray(),'lead_source');
		
		$get_customer_location = DB::table('customer_details')->select('location')->distinct('location')->get();
		$customer_location_arr = array_column($get_customer_location->toArray(),'location');
		
		$get_lead_owners = DB::table('user')
                        ->select('id as user_id','username as lead_owner')
                        ->where('role','=','user')
                        ->whereNotNull('email_id_user')
                        ->orderBy('username')
								->get();
                        
		$lead_owners_arr = array_column($get_lead_owners->toArray(),'user_id');
      
		$whereCondition = [];
      
		$customer_name = $request->get('filter_customer_name');
		if(isset($customer_name)){
			$whereCondition1 = ['customer_details.customer_name','LIKE','%'.$customer_name.'%'];
			array_push($whereCondition,$whereCondition1);
		}
		$customer_contact = $request->get('filter_contact_no');
		if(isset($customer_contact)) {
			$whereCondition2 = ['customer_details.primary_contact_no','=',$customer_contact];
			array_push($whereCondition,$whereCondition2);
		}
		$lead_status = $request->get('filter_lead_status');
		if(isset($lead_status) && ($lead_status=='Work In Process' || $lead_status=='Converted' || $lead_status=='Closed' )){
         if($lead_status=='Closed'){
            $whereCondition3_1 = ['leads.lead_status','!=','Order Generated'];
            $whereCondition3_2 = ['leads.lead_status','!=','Converted'];
            $whereCondition3_3 = ['leads.lead_status','!=','Work In Process'];
            array_push($whereCondition,$whereCondition3_1);
            array_push($whereCondition,$whereCondition3_2);
            array_push($whereCondition,$whereCondition3_3);
         }
         else{
            $whereCondition3 = ['leads.lead_status','=',$lead_status];
            array_push($whereCondition,$whereCondition3);
         }
		}
		$location = $request->get('filter_customer_location');
		if(isset($location) && in_array($location,$customer_location_arr)){
			$whereCondition4 = ['customer_details.location','=',$location];
			array_push($whereCondition,$whereCondition4);
		}
		$lead_source = $request->get('filter_lead_source');
		if(isset($lead_source) && in_array($lead_source,$lead_sources_arr)){
			$whereCondition6 = ['leads.lead_source','=',$lead_source];
			array_push($whereCondition,$whereCondition6);
		}
		$lead_owner = $request->get('filter_lead_owner');
		if(isset($lead_owner) && in_array($lead_owner,$lead_owners_arr)){
			$whereCondition7 = ['leads.lead_owner','=',$lead_owner];
			array_push($whereCondition,$whereCondition7);
		}
      if(session('role')=='user')
      {
         $whereCondition8 = ['leads.lead_owner','=',session('user_id')];
			array_push($whereCondition,$whereCondition8);
      }
      $order_id = $request->get('filter_order_id');
		if(isset($order_id)){
			$whereCondition9 = ['del_orders.order_id','=',$order_id];
			array_push($whereCondition,$whereCondition9);
		}
      $customer_type = $request->get('filter_customer_type');
		if(isset($customer_type) && $customer_type=='Individual' || $customer_type=='Corporate'){
			$whereCondition10 = ['customer_details.customer_type','=',$customer_type];
			array_push($whereCondition,$whereCondition10);
		}
      
		$from_date = $request->get('filter_from_date');
		$end_date = $request->get('filter_end_date');
      if(isset($from_date) && isset($end_date)){
         $get_min_date = $from_date;
         $get_max_date = $end_date;
      }
      $sort_colmun = $request->get('sort_column');
      $sort_val = $request->get('sort_direction');
      $column = 'leads.id';
      $direction = 'DESC';
      if(isset($sort_colmun) && isset($sort_val)){
         $column = $sort_colmun;
         $direction = $sort_val;
      }

      if(isset($lead_status) && $lead_status=='Order Generated'){
         $get_all_leads = DB::table('leads')
               ->join('customer_details','leads.customer_id','=','customer_details.cust_id')
               ->join('user','leads.lead_owner','=','user.id')
               ->join('del_orders','leads.id','=','del_orders.lead_id')
               ->select('leads.*','customer_details.*','del_orders.order_id as order_id','user.username as lead_owner','leads.comment as lead_comment')
               ->where($whereCondition)
               ->whereBetween('leads.creation_date',[$get_min_date,$get_max_date])
               ->where('leads.lead_status','!=','Mobile Generated')
               ->orderBy($column,$direction)
               ->paginate(10);
      }
      else
      {
         $get_all_leads = DB::table('leads')
               ->join('customer_details','leads.customer_id','=','customer_details.cust_id')
               ->join('user','leads.lead_owner','=','user.id')
               ->select('leads.*','customer_details.*','user.username as lead_owner','leads.comment as lead_comment')
               ->where($whereCondition)
               ->where('leads.lead_status','!=','Mobile Generated')
               ->whereBetween('leads.creation_date',[$get_min_date,$get_max_date])
               ->orderBy($column,$direction)
               ->paginate(10);
      }
      $json_decode_all_leads = json_decode(json_encode($get_all_leads->toArray()),true);
      foreach($json_decode_all_leads['data'] as $key=>$lead)
		{
		   $get_product_name = DB::table('products')->select('product_name')->whereIn('id',json_decode($lead['equipment_requirement']))->get()->toArray();
         $json_decode_all_leads['data'][$key]['product_name_arr'] = json_decode(json_encode($get_product_name),true);
			$get_product_name = implode(",",array_column(json_decode(json_encode($get_product_name),true),'product_name'));
		 	$json_decode_all_leads['data'][$key]['product_name']=$get_product_name;
		}

		$filter_collapse_cookie = null;
      if(isset($_COOKIE['filter_collapse_js']) && $_COOKIE['filter_collapse_js'] =='Yes')
      {
         $filter_collapse_cookie = 1;
      }
      $filter_arr = ["cust_name"=>$customer_name,
                     "cust_no"=>$customer_contact,
                     "lead_status"=>$lead_status,
                     "location"=>$location,
                     "lead_source"=>$lead_source,
                     "lead_owner"=>$lead_owner,
                     "from_date"=>$from_date,
                     "end_date"=>$end_date,
                     "order_id"=>$order_id,
                     "customer_type"=>$customer_type,
                     "sort_column"=>$sort_colmun,
                     "sort_val"=>$sort_val,
                     "filter_collapse_cookie"=>$filter_collapse_cookie];
      // dd($json_decode_all_leads);              
	   return view('leads/view_all_leads',compact('get_all_leads','json_decode_all_leads','get_lead_sources','get_customer_location','get_lead_owners','filter_arr','filter_collapse_cookie'));
	}
   
   //***** View All Converted Leads *******//
   public function viewConvertedLeads()
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
       return redirect()->to($url);
      }
      $past_three_days = date('Y-m-d',strtotime("-2 days"));
      // $date = date('Y-m-d');
      if($_SERVER['REQUEST_METHOD']=='GET')
      {
         if(session('role') == "superuser")
         {
            $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.creation_date >= '$past_three_days' AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress') ORDER BY leads.creation_date DESC");
            //echo "SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress') ORDER BY leads.priority ASC";
         }
         elseif(session('role') == "admin")
         {
            $user_city = session('user_city');
            $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.creation_date >= '$past_three_days' AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress') AND customer_details.citygroup = '$user_city' ORDER BY leads.creation_date DESC");
         }
         elseif(session('role') == "user")
         {
            $lead_owner = session('user_id');
            $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.creation_date >= '$past_three_days' AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress') AND leads.lead_owner = $lead_owner ORDER BY leads.creation_date DESC");
         }
         $data['lead_details'] = json_decode(json_encode($leads), true);
         for ($i=0; $i < count($data['lead_details']); $i++) 
         { 
            $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
            $user_id = $data['lead_details'][$i]['lead_owner'];
            $lead_owner_details = DB::select("SELECT * FROM user WHERE id=$user_id");
            $data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
            $data['lead_details'][$i]['username'] =  $data['lead_owner_details'][0]['username'];
            $data['lead_details'][$i]['user_id'] =  $data['lead_owner_details'][0]['id'];
            //print_r($product);
            $equipement_details = array();
            for ($j=0; $j <count($product); $j++) 
            { 
               $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
               $product_details = json_decode(json_encode($product_details), true);
               array_push($equipement_details,$product_details[0]['product_name']);
            }
            $equipements = json_encode($equipement_details);
            $data['lead_details'][$i]['equipment_requirement'] = $equipements;
         }
         echo "<script>localStorage['filtered']='past_3_days';</script>";
         return view('/leads/viewConvertedLeads',$data);
      }
      else
      {
         //$past_three_days = date('Y-m-d',strtotime("-2 days"));
         $start_date = $_POST['start_date'];
         $last_date = $_POST['last_date'];
         if(session('role') == "superuser")
         {
            $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND (leads.creation_date BETWEEN '$start_date' AND '$last_date' AND leads.lead_status = 'Converted' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress') ORDER BY leads.creation_date DESC");
         }
         elseif(session('role') == "admin")
         {
            $user_city = session('user_city');
            $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND (leads.creation_date BETWEEN '$start_date' AND '$last_date' AND leads.lead_status = 'Converted' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress') AND customer_details.citygroup = '$user_city' ORDER BY leads.creation_date DESC");
         }
         elseif(session('role') == "user")
         {
            $lead_owner = session('user_id');
            $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND (leads.creation_date BETWEEN '$start_date' AND '$last_date' AND leads.lead_status = 'Converted' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress') AND leads.lead_owner = $lead_owner ORDER BY leads.creation_date DESC");
         }
         $data['lead_details'] = json_decode(json_encode($leads), true);
         for ($i=0; $i < count($data['lead_details']); $i++) 
         { 
            $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
            $user_id = $data['lead_details'][$i]['lead_owner'];
            $lead_owner_details = DB::select("SELECT * FROM user WHERE id=$user_id");
            $data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
            $data['lead_details'][$i]['username'] =  $data['lead_owner_details'][0]['username'];
            $data['lead_details'][$i]['user_id'] =  $data['lead_owner_details'][0]['id'];
            //print_r($product);
            $equipement_details = array();
            for ($j=0; $j <count($product); $j++) 
            { 
               $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
               $product_details = json_decode(json_encode($product_details), true);
               array_push($equipement_details,$product_details[0]['product_name']);
            }
            $equipements = json_encode($equipement_details);
           $data['lead_details'][$i]['equipment_requirement'] = $equipements;
         }
         $data['lead_details'][0]['start_date'] = $start_date;
         $data['lead_details'][0]['last_date'] = $last_date;
         if(isset($equipements))
         {
            echo "<script>localStorage['filtered']='past_3_days';</script>";
            return view('leads/viewConvertedLeads',$data);
         }
         else
         {

            return redirect('/viewConvertedLeads')->with('message_search','no data available');
         }
      }
   }
   public function viewInProcessLeads()
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
       return redirect()->to($url);
      }
      // $date = date('Y-m-d');
      $past_three_days = date('Y-m-d',strtotime("-2 days"));
      if($_SERVER['REQUEST_METHOD']=='GET')
      {
         if(session('role') == "superuser")
         {
            $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.creation_date >= '$past_three_days' AND leads.lead_status = 'Work In Process' ORDER BY leads.creation_date DESC");
         }
         elseif(session('role') == "admin")
         {
            $user_city = session('user_city');
            $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.creation_date >= '$past_three_days' AND leads.lead_status = 'Work In Process' AND customer_details.citygroup = '$user_city' ORDER BY leads.creation_date DESC");
         }
         elseif(session('role') == "user")
         {
            $lead_owner = session('user_id');
            $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.creation_date >= '$past_three_days' AND leads.lead_status = 'Work In Process' AND leads.lead_owner = $lead_owner ORDER BY leads.creation_date DESC");
         }
         $data['lead_details'] = json_decode(json_encode($leads), true);
         for ($i=0; $i < count($data['lead_details']); $i++) 
         { 
            $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
            $user_id = $data['lead_details'][$i]['lead_owner'];
            $lead_owner_details = DB::select("SELECT * FROM user WHERE id=$user_id");
            $data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
            $data['lead_details'][$i]['username'] =  $data['lead_owner_details'][0]['username'];
            $data['lead_details'][$i]['user_id'] =  $data['lead_owner_details'][0]['id'];
            //print_r($product);
            $equipement_details = array();
            for ($j=0; $j <count($product); $j++) 
            { 
               $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
               $product_details = json_decode(json_encode($product_details), true);
               array_push($equipement_details,$product_details[0]['product_name']);
            }
            $equipements = json_encode($equipement_details);
            $data['lead_details'][$i]['equipment_requirement'] = $equipements;
         }
         echo "<script>localStorage['filtered']='past_3_days';</script>";
         return view('leads/viewInProcessLeads',$data);
      }
      else
      {
         //$past_three_days = date('Y-m-d',strtotime("-2 days"));
         $start_date = $_POST['start_date'];
         $last_date = $_POST['last_date'];
         if(session('role') == "superuser")
         {
            $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.creation_date BETWEEN '$start_date' AND '$last_date' AND leads.lead_status = 'Work In Process' ORDER BY leads.creation_date DESC");
         }
         elseif(session('role') == "admin")
         {
            $user_city = session('user_city');
            $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.creation_date BETWEEN '$start_date' AND '$last_date' AND leads.lead_status = 'Work In Process' AND customer_details.citygroup = $user_city ORDER BY leads.creation_date DESC");
         }
         elseif(session('role') == "user")
         {
            $lead_owner = session('user_id');
            $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.creation_date BETWEEN '$start_date' AND '$last_date' AND leads.lead_status = 'Work In Process' AND leads.lead_owner = $lead_owner ORDER BY leads.creation_date DESC");
         }
         $data['lead_details'] = json_decode(json_encode($leads), true);
         for ($i=0; $i < count($data['lead_details']); $i++) 
         { 
            $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
            $user_id = $data['lead_details'][$i]['lead_owner'];
            $lead_owner_details = DB::select("SELECT * FROM user WHERE id=$user_id");
            $data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
            $data['lead_details'][$i]['username'] =  $data['lead_owner_details'][0]['username'];
            $data['lead_details'][$i]['user_id'] =  $data['lead_owner_details'][0]['id'];
            //print_r($product);
            $equipement_details = array();
            for ($j=0; $j <count($product); $j++) 
            { 
               $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
               $product_details = json_decode(json_encode($product_details), true);
               array_push($equipement_details,$product_details[0]['product_name']);
            }
            $equipements = json_encode($equipement_details);
           $data['lead_details'][$i]['equipment_requirement'] = $equipements;
         }
         $data['lead_details'][0]['start_date'] = $start_date;
         $data['lead_details'][0]['last_date'] = $last_date;
         if(isset($equipements))
         {
            echo "<script>localStorage['filtered']='today';</script>";
            return view('leads/viewInProcessLeads',$data);
         }
         else
         {
            echo "<script>localStorage['filtered']='today';</script>";
            return redirect('/viewInProcessLeads')->with('message_search','no data available');
         }
         
      }
   }
   public function viewClosedLeads()
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
       return redirect()->to($url);
      }
      if($_SERVER['REQUEST_METHOD']=='GET')
      {
         // $date = date('Y-m-d');
         $past_three_days = date('Y-m-d',strtotime("-2 days"));
         if(session('role') == "superuser")
         {
            $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status != 'Converted' AND leads.lead_status != 'Work In Process' AND leads.lead_status != 'Quali5Care' AND leads.lead_status != 'Mobile Generated' AND leads.lead_status != 'Order Generated' AND leads.lead_status != 'Delivery In Progress' AND leads.lead_status != 'Vendor Assigned' AND leads.creation_date = '$past_three_days' ORDER BY leads.creation_date DESC");
         }
         elseif(session('role') == "admin")
         {
            $user_city = session('user_city');
            $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status != 'Converted' AND leads.lead_status != 'Work In Process' AND leads.lead_status != 'Quali5Care' AND leads.lead_status != 'Mobile Generated' AND leads.lead_status != 'Order Generated' AND leads.lead_status != 'Delivery In Progress' AND leads.lead_status != 'Vendor Assigned' AND leads.creation_date = '$past_three_days' AND customer_details.citygroup='$user_city' ORDER BY leads.creation_date DESC");
         }
         elseif(session('role') == "user")
         {
            $lead_owner = session('user_id');
            $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status != 'Converted' AND leads.lead_status != 'Work In Process' AND leads.lead_status != 'Quali5Care' AND leads.lead_status != 'Mobile Generated' AND leads.lead_status != 'Order Generated' AND leads.lead_status != 'Delivery In Progress' AND leads.lead_status != 'Vendor Assigned' AND leads.creation_date = '$past_three_days' AND leads.lead_owner = $lead_owner ORDER BY leads.creation_date DESC");
         }
         $data['lead_details'] = json_decode(json_encode($leads), true);
         for ($i=0; $i < count($data['lead_details']); $i++) 
         {
            $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
            $user_id = $data['lead_details'][$i]['lead_owner'];
            $lead_owner_details = DB::select("SELECT * FROM user WHERE id=$user_id");
            $data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
            $data['lead_details'][$i]['username'] =  $data['lead_owner_details'][0]['username'];
            $data['lead_details'][$i]['user_id'] =  $data['lead_owner_details'][0]['id'];
            //print_r($product);
            $equipement_details = array();
            for ($j=0; $j <count($product); $j++) 
            {
               $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
               $product_details = json_decode(json_encode($product_details), true);
               array_push($equipement_details,$product_details[0]['product_name']);
            }
            $equipements = json_encode($equipement_details);
            $data['lead_details'][$i]['equipment_requirement'] = $equipements;
         }
         echo "<script>localStorage['filtered']='past_3_days';</script>";
         return view('/leads/viewClosedLeads',$data);
      }
      else
      {
         //$past_three_days = date('Y-m-d',strtotime("-2 days"));
         $start_date = $_POST['start_date'];
         $last_date = $_POST['last_date'];
         if(session('role') == "superuser")
         {
            $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.creation_date BETWEEN '$start_date' AND '$last_date' AND leads.lead_status = 'Converted' AND leads.lead_status != 'Work In Process' ORDER BY leads.creation_date DESC");
         }
         elseif(session('role') == "admin")
         {
            $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.creation_date BETWEEN '$start_date' AND '$last_date' AND leads.lead_status = 'Converted' AND leads.lead_status != 'Work In Process' AND customer_details.citygroup = $user_city ORDER BY leads.creation_date DESC");
         }
         elseif(session('role') == "user")
         {
            $lead_owner = session('user_id');
            $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.creation_date BETWEEN '$start_date' AND '$last_date' AND leads.lead_status = 'Converted' AND leads.lead_status != 'Work In Process' AND leads.lead_owner = $lead_owner ORDER BY leads.creation_date DESC");
         }
         $data['lead_details'] = json_decode(json_encode($leads), true);
         for ($i=0; $i < count($data['lead_details']); $i++) 
         { 
            $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
            $user_id = $data['lead_details'][$i]['lead_owner'];
            $lead_owner_details = DB::select("SELECT * FROM user WHERE id=$user_id");
            $data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
            $data['lead_details'][$i]['username'] =  $data['lead_owner_details'][0]['username'];
            $data['lead_details'][$i]['user_id'] =  $data['lead_owner_details'][0]['id'];
            //print_r($product);
            $equipement_details = array();
            for ($j=0; $j <count($product); $j++) 
            { 
               $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
               $product_details = json_decode(json_encode($product_details), true);
               array_push($equipement_details,$product_details[0]['product_name']);
            }
            $equipements = json_encode($equipement_details);
           $data['lead_details'][$i]['equipment_requirement'] = $equipements;
         }
         $data['lead_details'][0]['start_date'] = $start_date;
         $data['lead_details'][0]['last_date'] = $last_date;
         if(isset($equipements))
         {
            echo "<script>localStorage['filtered']='today';</script>";
            return view('/leads/viewClosedLeads',$data);
         }
         else
         {
            echo "<script>localStorage['filtered']='today';</script>";
            return redirect('/viewClosedLeads')->with('message_search','no data available');
         }
         
      }
   }
   //***** View All Leads In Dashboard only for last three days******/
   public function viewAllLeads_d()
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
       return redirect()->to($url);
      }
      $leads = DB::select('SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = "Work In Process" ORDER BY leads.creation_date DESC');
      $data['lead_details'] = json_decode(json_encode($leads), true);
      for ($i=0; $i < count($data['lead_details']); $i++) 
      { 
         $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
         
         $user_id = $data['lead_details'][$i]['lead_owner'];
         $lead_owner_details = DB::select("SELECT * FROM user WHERE id=$user_id");
         $data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
         $data['lead_details'][$i]['username'] =  $data['lead_owner_details'][0]['username'];
         $data['lead_details'][$i]['user_id'] =  $data['lead_owner_details'][0]['id'];
         //print_r($product);
         $equipement_details = array();
         for ($j=0; $j <count($product); $j++) 
         { 
            $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
            $product_details = json_decode(json_encode($product_details), true);
            array_push($equipement_details,$product_details[0]['product_name']);
         }
         $equipements = json_encode($equipement_details);
         $data['lead_details'][$i]['equipment_requirement'] = $equipements;
      }
      echo "<script>localStorage['filtered']='today';</script>";
      return view('leads/viewAllLeads',$data);
   }
   // ****View Single Lead****
   public function leads_view_lead($customer_id,$id)
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
         $url = url('/');
         return redirect()->to($url);
      }
      $lead_details = DB::select("SELECT * FROM leads,customer_details WHERE leads.id=$id AND customer_details.cust_id=$customer_id");
      $data['lead_details'] = json_decode(json_encode($lead_details), true);
      $user_id = $data['lead_details'][0]['lead_owner'];
      $lead_owner_details = DB::select("SELECT * FROM user WHERE id=$user_id");
      $data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
      $data['lead_details'][0]['username'] =  $data['lead_owner_details'][0]['username'];
      $data['lead_details'][0]['user_id'] =  $data['lead_owner_details'][0]['id'];
      $product = json_decode($data['lead_details'][0]['equipment_requirement']);
         //print_r($product);
         $equipement_details = array();
         $user_id = $data['lead_details'][0]['lead_owner'];
         $lead_owner_details = DB::select("SELECT * FROM user WHERE id=$user_id");
         $data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
         $data['lead_details'][0]['username'] =  $data['lead_owner_details'][0]['username'];
         $data['lead_details'][0]['user_id'] =  $data['lead_owner_details'][0]['id'];
         for ($j=0; $j <count($product); $j++) 
         { 
            $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
            $product_details = json_decode(json_encode($product_details), true);
            array_push($equipement_details,$product_details[0]['product_name']);
         }
         $equipements = json_encode($equipement_details);
         $data['lead_details'][0]['equipment_requirement'] = $equipements;

      //print_r($data);
      return view('leads/view_lead',$data);
   }
   // Edit lead get method to display lead info
   public function edit_lead($customer_id,$id)
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
       return redirect()->to($url);
      }
      $products = DB::select("SELECT * FROM products WHERE flag = 'Active'");
      $data['products'] = \json_decode(\json_encode($products), true);
      $cities = DB::select("SELECT * FROM cities");
      $data['cities'] = \json_decode(\json_encode($cities), true);
      $states = DB::select("SELECT * FROM states");
      $data['states'] = \json_decode(\json_encode($states), true);
      $countries = DB::select("SELECT * FROM countries");
      $data['countries'] = \json_decode(\json_encode($countries), true);
      $users = DB::select("SELECT * FROM user WHERE role!='vendor'");
      $data['users'] = \json_decode(\json_encode($users), true);
      $lead_details = DB::select("SELECT * FROM leads,customer_details WHERE leads.id=$id AND customer_details.cust_id=$customer_id");
      $data['lead_details'] = json_decode(json_encode($lead_details), true);

      if ($data['lead_details'][0]['lead_status']=='Work In Process')
      {
         $user_id = $data['lead_details'][0]['lead_owner'];
         $lead_owner_details = DB::select("SELECT * FROM user WHERE id=$user_id");
         $data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
         $data['lead_details'][0]['username'] =  $data['lead_owner_details'][0]['username'];
         $data['lead_details'][0]['user_id'] =  $data['lead_owner_details'][0]['id'];
         $product = json_decode($data['lead_details'][0]['equipment_requirement']);
            $equipment_id = $data['lead_details'][0]['equipment_requirement'];
            //print_r($product);
            $equipement_details = array();
            for ($j=0; $j <count($product); $j++) 
            { 
               $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
               $product_details = json_decode(json_encode($product_details), true);
               array_push($equipement_details,$product_details[0]['product_name']);
            }
            $equipements = json_encode($equipement_details);
            $data['lead_details'][0]['equipment_requirement'] = $equipements;
            $data['lead_details'][0]['equipment_id'] = $equipment_id;
         return view('leads/edit_lead',$data);
      }
      elseif ($data['lead_details'][0]['lead_status']=='Converted')
      {
         $products = DB::select("SELECT * FROM products WHERE flag = 'Active'");
         $data['products_details'] = json_decode(json_encode($products), true);
         $users = DB::select("SELECT * FROM user WHERE role!='vendor'");
         $data['users'] = \json_decode(\json_encode($users), true);
         $user_id = $data['lead_details'][0]['lead_owner'];
         $lead_owner_details = DB::select("SELECT * FROM user WHERE id=$user_id");
         $data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
         $data['lead_details'][0]['username'] =  $data['lead_owner_details'][0]['username'];
         $data['lead_details'][0]['user_id'] =  $data['lead_owner_details'][0]['id'];
         $equipment_id = $data['lead_details'][0]['equipment_requirement'];
         $product = json_decode($data['lead_details'][0]['equipment_requirement']);
            $equipment_id = $data['lead_details'][0]['equipment_requirement'];
            //print_r($product);
            $equipement_details = array();
            for ($j=0; $j <count($product); $j++) 
            { 
               $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
               $product_details = json_decode(json_encode($product_details), true);
               array_push($equipement_details,$product_details[0]['product_name']);
            }
            $equipements = json_encode($equipement_details);
            $data['lead_details'][0]['equipment_requirement'] = $equipements;
            $data['lead_details'][0]['equipment_id'] = $equipment_id;
         //print_r($data['lead_details']);
         return view('leads/converted_lead_edit',$data);
      }
     
   }
   // to update lead details
   public function update_lead()
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
       return redirect()->to($url);
      }
      if($_SERVER['REQUEST_METHOD']=='POST')
      {
         $customer_details = new customer_detail();
         $leads = new lead();
         $leads_log =new leads_log();
         $current_time = new DateTime("now", new DateTimeZone('Asia/Kolkata') );
         $current_time = $current_time->format('H:i:s');
         $converted_date = request()->get('converted_date')." ".$current_time;
         if($_POST['submit']=='update')
         {
            //print_r($_POST);
            $customer_id = request()->get('customer_id');
            $lead_id = request()->get('lead_id');
            $lead_status = 'Work In Process';
            $equipments = request()->get('eqipments');
            $equipments_requirements = json_encode($equipments);

            $cutomer_details_updateData = [
               'customer_name' => request()->get('cust_name'),
               'address_line_1' => request()->get('address_line_1'),
               'address_line_2' => request()->get('address_line_2'),
               'landmark' => request()->get('landmark'),
               'area' => request()->get('area'),
               'city' => request()->get('city'),
               'pincode' => request()->get('pincode'),
               'state' => 'Maharashtra',//request()->get('state'),
               'country' => 'India',//request()->get('country'),
               'location' => request()->get('location'),
               'email_id' => request()->get('email'),
               'primary_contact_no' => request()->get('primary_contact_no'),
               'secondary_contact_no' => request()->get('secondary_contact_no'),
               'refered_by' => request()->get('refered_by'),
               'customer_type' => request()->get('customer_type'),
               'updated_by' => session('username')
            ];
            //echo $customer_id;
            //DB::table('customer_details')->where('cust_id',$customer_id)->update($cutomer_details_updateData);
            //print_r($cutomer_details_updateData);
            $customer_details->where('cust_id',$customer_id)->update($cutomer_details_updateData);
            $lead_details = DB::table('leads')->select('equipment_requirement')->where('id',$lead_id)->get();
            $lead_equipments = $lead_details[0]->equipment_requirement;
            $leads_updateData = [
               //'converted_at'=>$converted_date,
               'creation_date' => request()->get('creation_date'),
               'patient_name' => request()->get('patient_name'),
               'patient_age' => request()->get('patient_age'),
               'doctor_name' => request()->get('doctor_name'),
               'hospital_name' => request()->get('hospital_name'),
               'therapeutic_requirement' => request()->get('therapeutic_requirement'),
               'equipment_requirement' => $equipments_requirements,
               'lead_source' => request()->get('lead_source'),
               'lead_value' => request()->get('lead_value'),
               'lead_status' => $lead_status,
               'lead_owner' => request()->get('lead_owner'),
               'updated_by' => session('username')
            ];
            $leads->where('id',$lead_id)->update($leads_updateData);

            if($lead_equipments != $equipments_requirements)
            {
               $insertData = [
                  'order_type'=>'LD',
                  'key_id'=>$lead_id,
                  'operation'=>'Update Lead Equip.',
                  'fields'=>'equipment_requirement',
                  'old_value'=>$lead_equipments,
                  'new_value'=>$equipments_requirements,
                  'updated_by'=>session('username')
               ];
               ActivityLog::insert($insertData);
            }
            //print_r($leads_updateData);
         }
         if($_POST['submit']=='convert')
         {
            $customer_id = request()->get('customer_id');
            $lead_id = request()->get('lead_id');
            $equipments = request()->get('equipments');
            $deposite = json_encode(request()->get('offered_deposite'));
            $offered_rent = json_encode(request()->get('offered_rent'));
            $deposite_total = json_encode(request()->get('offered_deposite_total'));
            $offered_rent_total = json_encode(request()->get('offered_rent_total'));
            $transport = json_encode(request()->get('transport'));
            $del_date = json_encode(request()->get('DelDate'));
            $priority = request()->get('priority_ratio');
            $qty = json_encode(request()->get('qty'));
            $gst_no = request()->get('gst_no');
            $lead_value = request()->get('lead_value');
            $equipments_requirements = json_encode($equipments);
            $equipment = json_decode($equipments_requirements);
            $sale_rental = json_encode(request()->get('sale_rental'));
            $same_address = request()->get('check_del_address');
            if($same_address == True){
               $is_same = "Yes";
            }
            else
            {
               $is_same = "No";
            }
               // $sale_rental = array();
               // for($i=1; $i<=count($equipment); $i++)
               // {
               //    $sale_rental_temp = request()->get('sale_rental'.$i);
               //    array_push($sale_rental,$sale_rental_temp);
               // }
               // $sale_rental = json_encode($sale_rental);
            $cutomer_details_updateData = 
            [
               'customer_name' => request()->get('cust_name'),
               'address_line_1' => request()->get('address_line_1'),
               'address_line_2' => request()->get('address_line_2'),
               'landmark' => request()->get('landmark'),
               'area' => request()->get('area'),
               'city' => request()->get('city1'),
               'pincode' => request()->get('pincode'),
               'state' => request()->get('state'),
               'country' => request()->get('country'),
               'location' => request()->get('location'),
               'email_id' => request()->get('email'),
               'primary_contact_no' => request()->get('primary_contact_no'),
               'secondary_contact_no' => request()->get('secondary_contact_no'),
               'prmt_address_line_1' => request()->get('prmt_address_line_1'),
               'prmt_address_line_2' => request()->get('prmt_address_line_2'),
               'prmt_landmark' => request()->get('prmt_landmark'),
               'prmt_area' => request()->get('prmt_area'),
               'prmt_city' => request()->get('prmt_city'),
               'prmt_pincode' => request()->get('prmt_pincode'),
               'prmt_state' => request()->get('state'),
               'prmt_country' =>request()->get('country'),
               'prmt_email_id' => request()->get('prmt_email'),
               'prmt_secondary_contact_no' => request()->get('prmt_secondary_contact_no'),
               'addr_is_same' => $is_same,
               'gst_no' => $gst_no,
               'refered_by' => request()->get('refered_by'),
               'customer_type' => request()->get('customer_type'),
               'updated_by' => session('username')
            ];
            $customer_details->where('cust_id',$customer_id)->update($cutomer_details_updateData);
            if(isset($equipments))
            {
               $equipment = json_decode($equipments_requirements);
               $sale_rental = $_POST['sale_rental'];
               $sale_rental = json_encode($sale_rental);
               // $sale_rental = array();
               // for($i=1; $i<=count($equipment); $i++)
               // {
               //    $sale_rental_temp = request()->get('sale_rental'.$i);
               //    array_push($sale_rental,$sale_rental_temp);
               // }
               // $sale_rental = json_encode($sale_rental);

               //for converted_at date 
               $converted_at = $_POST['converted_at'];
               if($converted_at!=null)
               {
                  $converted_at = $_POST['converted_at'];
                  $creation_date1 = $_POST['converted_at'];
               }
               else
               {
                  $converted_at = date('Y-m-d');
                  $creation_date1 = date('Y-m-d');
               }
               $lead_old_data = DB::table('leads')
                  ->select(
                     'converted_at',
                     'creation_date',
                     'patient_name',
                     'patient_age',
                     'doctor_name',
                     'hospital_name',
                     'therapeutic_requirement',
                     'equipment_requirement',
                     'equipment_qty',
                     'del_date',
                     'sale_rental',
                     'deposite',
                     'offered_rent',
                     'deposite_total',
                     'offered_rent_total',
                     'transport',
                     'lead_source',
                     'lead_value',
                     'lead_status',
                     'priority',
                     'lead_owner',
                     'comment',
                     'payment_mode',
                     'updated_by')
                  ->where('id',$lead_id)
                  ->get();
               $leads_updateData = 
               [
                  'converted_at'=>$converted_date,
                  'creation_date'=>$converted_date,
                  'patient_name' => request()->get('patient_name'),
                  'patient_age' => request()->get('patient_age'),
                  'doctor_name' => request()->get('doctor_name'),
                  'hospital_name' => request()->get('hospital_name'),
                  'therapeutic_requirement' => request()->get('therapeutic_requirement'),
                  'equipment_requirement' => $equipments_requirements,
                  'equipment_qty' => $qty,
                  'del_date' => $del_date,
                  'sale_rental' => $sale_rental,
                  'deposite' => $deposite,
                  'offered_rent' => $offered_rent,
                  'deposite_total' => $deposite_total,
                  'offered_rent_total' => $offered_rent_total,
                  'transport' => $transport,
                  'lead_source' => request()->get('lead_source'),
                  'lead_value' => request()->get('lead_value'),
                  'lead_status' => 'Converted',
                  'priority' => $priority,
                  'lead_owner' => request()->get('lead_owner'),
                  'comment' => request()->get('comment'),
                  'payment_mode' => request()->get('payment_mode'),
                  'updated_by' => session('username')
               ];
            }
            $leads->where('id',$lead_id)->update($leads_updateData);
            $activity_log = array();
            foreach ($leads_updateData as $key => $value)
            {
               if($value != $lead_old_data[0]->$key)
               {
                  $insertData = [
                     'order_type'=>'LD',
                     'key_id'=>$lead_id,
                     'operation'=>'Update Lead Conv.',
                     'fields'=>$key,
                     'old_value'=>$lead_old_data[0]->$key,
                     'new_value'=>$value,
                     'updated_by'=>session('username')
                  ];
                  ActivityLog::insert($insertData);
               }
            }

            $leads_log_data = 
            [
               'log_lead_id' => $lead_id,
               'log_lead_status' => 'Converted',
               'log_date' => date('Y-m-d'),
               'log_time' => date('H:i:s'),
               'updated_by' => session('username')
            ];
            $leads_log->insert($leads_log_data);
            // $equipment = json_decode($equipments_requirements);
            // $equip_qty = json_decode($qty);
            // $equip_deposit = json_decode($deposit);
            // $eqip_rent = json_decode($offered_rent);
            // $transport = json_decode($transport);
            // $sale_rental = json_decode($sale_rental);
            // $cdata = array();
            // $temp_transport = 0;
            // $temp_total = 0;
            // $i = 0;
            // foreach ($equipment as $equipment_details) 
            // {
            //    $product_details = DB::select("SELECT * FROM products WHERE id = $equipement_details");
            //    $data['product_details'] = json_decode(json_encode($product_details), true);
            //    $cdata['equipment_name'.$i] = $data['product_details']['product_name'];
            //    $cdata['equipment_qty'.$i] = $equip_qty[$i];
            //    if($sale_rental[$i] == 'Rental') 
            //    {
            //       $cdata['equipment_rent'.$i] = $equip_rent[$i];
            //       $cdata['equipment_deposit'.$i] = $equip_deposit[$i];
            //       $temp_transport = $temp_transport + $transport[$i];
            //       $temp_total = $temp_total + $equip_rent[$i];
            //       $temp_total = $temp_total + $equip_deposit[$i];
            //    }
            //    if($sale_rental == 'Sale')
            //    {
            //       $cdata['equipment_sale_rate'.$i] = $equip_rent[$i];
            //       $temp_total = $temp_total + $equip_rent[$i];
            //       $temp_transport = $temp_transport + $transport[$i];
            //    } 
            //    $i = $i + 1;
            // }
            // $cdata['transport'] = $temp_transport;
            // $cdata['total'] = $temp_total;
            // generate_challan($cdata);

            //Generate Delivery Challan Information
               $customer_name = $_POST['cust_name'];
               $address = $_POST['address_line_1'].",".$_POST['address_line_2'].",".$_POST['area'].",".$_POST['city1'].",".$_POST['pincode'];

               $get_cust_contact = DB::select("SELECT primary_contact_no from customer_details WHERE cust_id ='$customer_id' ");
               $data['get_cust_contact'] = json_decode(json_encode($get_cust_contact),true);
               $contact = $data['get_cust_contact'][0]['primary_contact_no'];
               $customer_products = $_POST['equipments'];
               $total_amt=$lead_value;                
               $total_transport=0;
               $challan_data = array();
               // print_r($_POST['offered_rent']);
               // print_r($_POST['offered_deposite']);
               // print_r($_POST['transport']);
               for ($i=0; $i <count($customer_products) ; $i++) { 
                  $product_id = $customer_products[$i];
                  $offered_rent = (int)$_POST['offered_rent_total'][$i];   
                  $offered_deposit =(int)$_POST['offered_deposite'][$i];
                  $offered_transport = (int)$_POST['transport'][$i];
                  $sale_rental = $_POST['sale_rental'][$i];
                  $product_quantity = $_POST['qty'][$i];

                  //$total_amt+=$offered_rent+$offered_deposit+$offered_transport;
                  $total_transport+=$offered_transport;

                  $get_product_name = DB::select("SELECT product_name FROM products Where id ='$product_id' ");
                  $data['get_product_name'] = json_decode(json_encode($get_product_name),true);
                  $product_name = $data['get_product_name'][0]['product_name'];

                  $challan_data['product_name'][$i] = $product_name;  
                  $challan_data['offered_rent'][$i] = $offered_rent;
                  $challan_data['offered_deposit'][$i] = $offered_deposit;
                  $challan_data['offered_transport'][$i] = $offered_transport;
                  $challan_data['sale_rental'][$i] = $sale_rental;
                  $challan_data['product_quantity'][$i] = $product_quantity;

               }
               
               $data_pdf = array(
                  'lead_id'=>$lead_id,
                  'customer_name'=>$customer_name,
                  'address' =>$address,
                  'converted_date'=>date('d-m-Y',strtotime($converted_date)),
                  'contact_no' =>$contact,
                  'total_transport'=>$total_transport,
                  'total_amt'=>$total_amt,);
                  // 'mail_data'=>"'".$mail_data."'");
                  $data_pdf['challan_data'] = $challan_data;

                  //print_r($data_message);
               $url = url('/');
               $pdf = PDF::loadView('del_challan_temp', $data_pdf);

               file_put_contents("/var/www/html/prodweb/eflow/assets/uploads/challan/".$_POST['cust_name'].$lead_id.".pdf", $pdf->output()); 
               $file_path = "/assets/uploads/challan/".$_POST['cust_name'].$lead_id.".pdf";

			   //Storage::disk('local')->put(''.'/'.$_POST['cust_name'].$lead_id.".pdf", $pdf, 'public');

			//    if(!Storage::disk('public_uploads')->put($path, $file_content)) {
			// 	return false;
			// }
               
               $update_challanpath = DB::update("UPDATE leads SET delivery_challan='$file_path' WHERE id ='$lead_id' ");

            return redirect('/view_all_leads')->with('message', 'Lead Converted Successfully');
         }
         return redirect('/view_all_leads')->with('message', 'Lead Updated Successfully');
         
      }
   }
   //****Convert Lead View***//
   public function fetch_product_details($product_id)
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
         $url = url('/');
         return redirect()->to($url);
      }
      $products = DB::select("SELECT * FROM products WHERE id = $product_id");
      $data['products_details'] = json_decode(json_encode($products), true);
      $product_details = array('product_deposite' => $data['products_details'][0]['product_deposite'], 'product_rent' => $data['products_details'][0]['product_rent'], 'min_rent_percentage' => $data['products_details'][0]['min_rent_percentage']);
      $jsonstring = json_encode($product_details);
      echo $jsonstring;
   }
   public function fetch_product_details_sales($product_id)
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
         $url = url('/');
         return redirect()->to($url);
      }
      $products = DB::select("SELECT * FROM products WHERE id = $product_id");
      $data['products_details'] = json_decode(json_encode($products), true);
      $product_details = array('product_sale_rate' => $data['products_details'][0]['product_sale_rate'], 'min_rent_percentage' => $data['products_details'][0]['min_rent_percentage']);
      $jsonstring = json_encode($product_details);
      echo $jsonstring;
   }
   public function convert_lead($customer_id,$id)
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
         $url = url('/');
         return redirect()->to($url);
      }
      $products = DB::select("SELECT * FROM products WHERE flag = 'Active'");
      $data['products'] = json_decode(json_encode($products), true);
      $products = DB::select("SELECT * FROM products WHERE flag = 'Active'");
      $data['products_details'] = json_decode(json_encode($products), true);
      $cities = DB::select("SELECT * FROM cities");
      $data['cities'] = \json_decode(\json_encode($cities), true);
      $states = DB::select("SELECT * FROM states");
      $data['states'] = \json_decode(\json_encode($states), true);
      $countries = DB::select("SELECT * FROM countries");
      $data['countries'] = \json_decode(\json_encode($countries), true);
      $users = DB::select("SELECT * FROM user WHERE role!='vendor'");
      $data['users'] = \json_decode(\json_encode($users), true);
      $lead_details = DB::select("SELECT * FROM leads,customer_details WHERE leads.id=$id AND customer_details.cust_id=$customer_id");
      //$lead_details = DB::select("SELECT * FROM leads,customer_details,user WHERE leads.id=$id AND customer_details.cust_id=$customer_id AND user.id = leads.lead_owner");
      $data['lead_details'] = json_decode(json_encode($lead_details), true);
      $user_id = $data['lead_details'][0]['lead_owner'];
      $lead_owner_details = DB::select("SELECT * FROM user WHERE id=$user_id");
      $data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
      $data['lead_details'][0]['username'] =  $data['lead_owner_details'][0]['username'];
      $data['lead_details'][0]['user_id'] =  $data['lead_owner_details'][0]['id'];
      $equipment_id = $data['lead_details'][0]['equipment_requirement'];
      $product = json_decode($data['lead_details'][0]['equipment_requirement']);
         $equipment_id = $data['lead_details'][0]['equipment_requirement'];
         //print_r($product);
         $equipement_details = array();
         for ($j=0; $j <count($product); $j++) 
         { 
            $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
            $product_details = json_decode(json_encode($product_details), true);
            array_push($equipement_details,$product_details[0]['product_name']);
         }
         $equipements = json_encode($equipement_details);
         $data['lead_details'][0]['equipment_requirement'] = $equipements;
         $data['lead_details'][0]['equipment_id'] = $equipment_id;
      return view('leads/convert_lead',$data);
   }
   public function delete_lead($id)
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
       return redirect()->to($url);
      }
      $leads = new lead();
      $leads->where('id',$id)->delete();
      return redirect('/viewAllLeads')->with('message_delete', 'Lead Deleted Successfully');
      //return $this->viewAllLeads();
   }
   public function close_lead($customer_id,$id)
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
       return redirect()->to($url);
      }
      $leads = new lead();
      //$leads->where('id',$id)->delete();
      $lead_old_status = DB::table('leads')->select('lead_status')->where('id',$id)->get();

      $old_status = $lead_old_status[0]->lead_status;

      $lead_status = [
         'lead_status' => 'Closed'
      ];
      $leads->where('id',$id)->update($lead_status);

      $insertData = [
         'order_type'=>'LD',
         'key_id'=>$id,
         'operation'=>'Lead Closed',
         'fields'=>'lead_status',
         'old_value'=>$old_status,
         'new_value'=>'Closed',
         'updated_by'=>session('username')
      ];
      ActivityLog::insert($insertData);

      return redirect('/viewAllLeads')->with('message', 'Lead Closed Successfully');
      //return $this->viewAllLeads();
   }
   public function close_lead_with_reason(Request $request,$lead_id)
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
       return redirect()->to($url);
      }
      $leads = new lead();
      //$leads->where('id',$id)->delete();
      $reason = $request->get('reason');
      $desc = $request->get('desc');
      // $previous_url = $request->get('previous_url');
      $lead_status = [
         'lead_status' => $reason,
         'remark' => $desc
      ];
      $leads->where('id',$lead_id)->update($lead_status);
      
      $insertData = [
         'order_type'=>'LD',
         'key_id'=>$lead_id,
         'operation'=>'Lead Closed Reason',
         'fields'=>'lead_status',
         'old_value'=>'Work In Process',
         'new_value'=>$reason,
         'updated_by'=>session('username')
      ];
      ActivityLog::insert($insertData);

      $insertData1 = [
         'order_type'=>'LD',
         'key_id'=>$lead_id,
         'operation'=>'Lead Closed Desc',
         'fields'=>'remark',
         'old_value'=>'',
         'new_value'=>$desc,
         'updated_by'=>session('username')
      ];
      ActivityLog::insert($insertData1);

      // return redirect($previous_url)->with('message', 'Lead Closed Successfully');
      return redirect()->back()->with('message_delete','Lead Closed Successfully');
   }
   public function check_customer()
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
       return redirect()->to($url);
      }
      if($_SERVER['REQUEST_METHOD']=='POST')
      {
         $products = DB::select("SELECT * FROM products WHERE flag = 'Active'");
         $data['products'] = \json_decode(\json_encode($products), true);
         $cities = DB::select("SELECT * FROM cities");
         $data['cities'] = \json_decode(\json_encode($cities), true);
         $states = DB::select("SELECT * FROM states");
         $data['states'] = \json_decode(\json_encode($states), true);
         $countries = DB::select("SELECT * FROM countries");
         $data['countries'] = \json_decode(\json_encode($countries), true);
         $users = DB::select("SELECT * FROM user WHERE role!='vendor'");
         $data['users'] = \json_decode(\json_encode($users), true);
         $primary_contact_no = request()->get('primary_contact_no');
         $customer_details = DB::select("SELECT * FROM customer_details WHERE primary_contact_no = $primary_contact_no");
         $data['customer_details'] = json_decode(json_encode($customer_details), true);
         return view('leads/create_lead',$data);
      }
      else
      {
         return view('leads/check_customer'); 
      }
   }
   public function findCustomer($primary_contact_no)
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
       return redirect()->to($url);
      }
      ini_set('display_errors', 1);
      $customer_details = DB::select("SELECT * FROM customer_details WHERE primary_contact_no = '$primary_contact_no'");
      $data['customer_details'] = \json_decode(\json_encode($customer_details), true);
      // print_r(null['cust_id']);
      //$customer_details = array();
      //array_push($customer_details['cust_id'],$data['customer_details'][0]['cust_id']);
      //print_r($customer_details);
      if(isset($data['customer_details'][0]['cust_id'])){
         $json= array('customer_id' => $data['customer_details'][0]['cust_id'] , 'customer_name' => $data['customer_details'][0]['customer_name'] , 'location' => $data['customer_details'][0]['location'] , 'address_line_1' => $data['customer_details'][0]['address_line_1'] , 'address_line_2' => $data['customer_details'][0]['address_line_2'] , 'area' => $data['customer_details'][0]['area'] , 'landmark' => $data['customer_details'][0]['landmark'] , 'city' => $data['customer_details'][0]['city'] , 'pincode' => $data['customer_details'][0]['pincode'] , 'state' => $data['customer_details'][0]['state'] , 'country' => $data['customer_details'][0]['country'] , 'secondary_contact_no' => $data['customer_details'][0]['secondary_contact_no'] , 'email_id' => $data['customer_details'][0]['email_id'] , 'refered_by' => $data['customer_details'][0]['refered_by']);	
         $jsonstring = json_encode($json);
         echo $jsonstring;
      }
      else
      {
         $json= array('customer_id' => null , 'customer_name' => null, 'location' => null, 'address_line_1' => null, 'address_line_2' => null, 'area' => null, 'landmark' => null, 'city' => null, 'pincode' => null, 'state' => null, 'country' => null, 'secondary_contact_no' => null, 'email_id' => null, 'refered_by' => null);	
         $jsonstring = json_encode($json);
         echo $jsonstring;
      }
      
   }
//view all leads of the same customer//
   public function view_cust_leads($customer_id)
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
       return redirect()->to($url);
      }
      $lead_details = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id=$customer_id AND customer_details.cust_id=$customer_id");
      $data['lead_details'] = \json_decode(\json_encode($lead_details), true);
      for ($i=0; $i < count($data['lead_details']); $i++) 
      { 
         $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
         $user_id = $data['lead_details'][$i]['lead_owner'];
         $lead_owner_details = DB::select("SELECT * FROM user WHERE id=$user_id");
         $data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
         $data['lead_details'][$i]['username'] =  $data['lead_owner_details'][0]['username'];
         $data['lead_details'][$i]['user_id'] =  $data['lead_owner_details'][0]['id'];
         //print_r($product);
         $equipement_details = array();
         for ($j=0; $j <count($product); $j++) 
         { 
            $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
            $product_details = json_decode(json_encode($product_details), true);
            array_push($equipement_details,$product_details[0]['product_name']);
         }
         $equipements = json_encode($equipement_details);
         $data['lead_details'][$i]['equipment_requirement'] = $equipements;
      }
      //print_r($data['lead_details']);
      return view('leads/view_cust_leads',$data);
   } 
   //filter Manually
   public function filterLeadsViewAll($filter_by)
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
          return redirect()->to($url);
      }
      if($filter_by =='today')
      {
         $date = date('Y-m-d');
         $whereClause = "leads.creation_date = '$date'";
         echo "<script>localStorage['filtered']='today';</script>";
      }
      elseif($filter_by =='yesterday')
      {
         $prevDate = date('Y-m-d',strtotime("-1 days"));
         $whereClause = "leads.creation_date = '$prevDate'";
         echo "<script>localStorage['filtered']='yesterday';</script>";
      }
      elseif($filter_by =='past_3_days')
      {
         $past_three_days = date('Y-m-d',strtotime("-2 days"));
         $whereClause = "leads.creation_date >= '$past_three_days'";
         echo "<script>localStorage['filtered']='past_3_days';</script>";
      }
      elseif($filter_by =='week')
      {
         $past_three_days = date('Y-m-d',strtotime("-6 days"));
         $whereClause = "leads.creation_date >= '$past_three_days'";
         echo "<script>localStorage['filtered']='week';</script>";
      }
      elseif($filter_by =='month')
      {
         $month = date('m-Y');
         $start_date_temp = '01-'.$month;
         $start_date = date('Y-m-d',strtotime($start_date_temp));
         $end_date_temp = '31-'.$month;
         $end_date = date('Y-m-d',strtotime($end_date_temp));
         $whereClause = "leads.creation_date BETWEEN '$start_date' AND '$end_date'";
      }
      elseif($filter_by == 'all')
      {
         if(session('role')=='superuser')
         {
            $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id ORDER BY leads.creation_date DESC");
         }
         elseif(session('role')=='admin')
         {
            $user_city = session('user_city');
            $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND customer_details.citygroup='$user_city' ORDER BY leads.creation_date DESC");
         }
         elseif(session('role')=='user')
         {
            $lead_owner = session('user_id');
            $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_owner = $lead_owner ORDER BY leads.creation_date DESC");
         }
         $data['lead_details'] = json_decode(json_encode($leads), true);
         for ($i=0; $i < count($data['lead_details']); $i++) 
         { 
            $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
            $user_id = $data['lead_details'][$i]['lead_owner'];
            $lead_owner_details = DB::select("SELECT * FROM user WHERE id=$user_id");
            $data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
            $data['lead_details'][$i]['username'] =  $data['lead_owner_details'][0]['username'];
            $data['lead_details'][$i]['user_id'] =  $data['lead_owner_details'][0]['id'];
            //print_r($product);
            $equipement_details = array();
            for ($j=0; $j <count($product); $j++) 
            { 
               $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
               $product_details = json_decode(json_encode($product_details), true);
               array_push($equipement_details,$product_details[0]['product_name']);
            }
            $equipements = json_encode($equipement_details);
            $data['lead_details'][$i]['equipment_requirement'] = $equipements;
         }
         echo "<script>localStorage['filtered']='all';</script>";
         return view('leads/viewAllLeads',$data);
      }
      if(session('role')=='superuser')
      {
         $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND $whereClause ORDER BY leads.creation_date DESC");
      }
      elseif(session('role')=='admin')
      {
         $user_city = session('user_city');
         //$leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND customer_details.citygroup ORDER BY leads.creation_date DESC");
         $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND customer_details.citygroup='$user_city' AND $whereClause ORDER BY leads.creation_date DESC");
      }
      elseif(session('role')=='user')
      {
         $lead_owner = session('user_id');
         $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_owner='$lead_owner' AND $whereClause ORDER BY leads.creation_date DESC");
      }
      
      $data['lead_details'] = json_decode(json_encode($leads), true);
      for ($i=0; $i < count($data['lead_details']); $i++) 
      { 
         $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
         $user_id = $data['lead_details'][$i]['lead_owner'];
         $lead_owner_details = DB::select("SELECT * FROM user WHERE id=$user_id");
         $data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
         $data['lead_details'][$i]['username'] =  $data['lead_owner_details'][0]['username'];
         $data['lead_details'][$i]['user_id'] =  $data['lead_owner_details'][0]['id'];
         //print_r($product);
         $equipement_details = array();
         for ($j=0; $j <count($product); $j++) 
         { 
            $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
            $product_details = json_decode(json_encode($product_details), true);
            array_push($equipement_details,$product_details[0]['product_name']);
         }
         $equipements = json_encode($equipement_details);
         $data['lead_details'][$i]['equipment_requirement'] = $equipements;
      }
      return view('leads/viewAllLeads',$data);
   }
   //inprocess Leads
   public function filterInprocessLeads($filter_by)
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
          return redirect()->to($url);
      }
      if($filter_by =='today')
      {
         $date = date('Y-m-d');
         $whereClause = "leads.creation_date = '$date'";
         echo "<script>localStorage['filtered']='today';</script>";
      }
      elseif($filter_by =='yesterday')
      {
         $prevDate = date('Y-m-d',strtotime("-1 days"));
         $whereClause = "leads.creation_date = '$prevDate'";
         echo "<script>localStorage['filtered']='yesterday';</script>";
      }
      elseif($filter_by =='past_3_days')
      {
         $past_three_days = date('Y-m-d',strtotime("-2 days"));
         $whereClause = "leads.creation_date >= '$past_three_days'";
         echo "<script>localStorage['filtered']='past_3_days';</script>";
      }
      elseif($filter_by =='week')
      {
         $past_three_days = date('Y-m-d',strtotime("-6 days"));
         $whereClause = "leads.creation_date >= '$past_three_days'";
         echo "<script>localStorage['filtered']='week';</script>";
      }
      elseif($filter_by =='month')
      {
         $month = date('m-Y');
         $start_date_temp = '01-'.$month;
         $start_date = date('Y-m-d',strtotime($start_date_temp));
         $end_date_temp = '31-'.$month;
         $end_date = date('Y-m-d',strtotime($end_date_temp));
         $whereClause = "leads.creation_date BETWEEN '$start_date' AND '$end_date'";
      }
      elseif($filter_by == 'all')
      {
         if(session('role')=='superuser')
         {
            $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Work In Process' ORDER BY leads.creation_date DESC");
         }
         elseif(session('role')=='admin')
         {
            $user_city = session('user_city');
            $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Work In Process' AND customer_details.citygroup='$user_city' ORDER BY leads.creation_date DESC");
         }
         elseif(session('role')=='user')
         {
            $lead_owner = session('user_id');
            $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Work In Process' AND leads.lead_owner='$lead_owner' ORDER BY leads.creation_date DESC");
         }
         
         $data['lead_details'] = json_decode(json_encode($leads), true);
         for ($i=0; $i < count($data['lead_details']); $i++) 
         { 
            $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
            $user_id = $data['lead_details'][$i]['lead_owner'];
            $lead_owner_details = DB::select("SELECT * FROM user WHERE id=$user_id");
            $data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
            $data['lead_details'][$i]['username'] =  $data['lead_owner_details'][0]['username'];
            $data['lead_details'][$i]['user_id'] =  $data['lead_owner_details'][0]['id'];
            //print_r($product);
            $equipement_details = array();
            for ($j=0; $j <count($product); $j++) 
            { 
               $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
               $product_details = json_decode(json_encode($product_details), true);
               array_push($equipement_details,$product_details[0]['product_name']);
            }
            $equipements = json_encode($equipement_details);
            $data['lead_details'][$i]['equipment_requirement'] = $equipements;
         }
         echo "<script>localStorage['filtered']='all';</script>";
         return view('leads/viewInProcessLeads',$data);
      }

      if(session('role')=='superuser')
      {
         //$leads = DB::select("SELECT * FROM leads,customer_details WHERE $whereClause ORDER BY leads.creation_date DESC");
         $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Work In Process' AND $whereClause ORDER BY leads.creation_date DESC");
      }
      elseif(session('role')=='admin')
      {
         $user_city = session('user_city');
         $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Work In Process' AND customer_details.citygroup='$user_city' AND $whereClause ORDER BY leads.creation_date DESC");
         //$leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND customer_details.citygroup='$user_city' AND $whereClause ORDER BY leads.creation_date DESC");
      }
      elseif(session('role')=='user')
      {
         $lead_owner = session('user_id');
         //$leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_owner='$lead_owner' AND $whereClause ORDER BY leads.creation_date DESC");
         $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Work In Process' AND leads.lead_owner='$lead_owner' AND $whereClause ORDER BY leads.creation_date DESC");
      }
      
      //echo "SELECT * FROM leads,customer_details WHERE leads.lead_status = 'Work In Process' AND $whereClause AND ORDER BY leads.creation_date DESC";
      $data['lead_details'] = json_decode(json_encode($leads), true);
      if(isset($data['lead_details']))
      {
         for ($i=0; $i < count($data['lead_details']); $i++) 
         { 
            $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
            $user_id = $data['lead_details'][$i]['lead_owner'];
            $lead_owner_details = DB::select("SELECT * FROM user WHERE id=$user_id");
            $data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
            $data['lead_details'][$i]['username'] =  $data['lead_owner_details'][0]['username'];
            $data['lead_details'][$i]['user_id'] =  $data['lead_owner_details'][0]['id'];
            //print_r($product);
            $equipement_details = array();
            for ($j=0; $j <count($product); $j++) 
            { 
               $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
               $product_details = json_decode(json_encode($product_details), true);
               array_push($equipement_details,$product_details[0]['product_name']);
            }
            $equipements = json_encode($equipement_details);
            $data['lead_details'][$i]['equipment_requirement'] = $equipements;
         }
      }
      return view('leads/viewInProcessLeads',$data);
   }
   //Converted Lead
   public function filterConvertedLeads($filter_by)
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
          return redirect()->to($url);
      }
      if($filter_by =='today')
      {
         $date = date('Y-m-d');
         $whereClause = "leads.creation_date = '$date'";
         echo "<script>localStorage['filtered']='today';</script>";
      }
      elseif($filter_by =='yesterday')
      {
         $prevDate = date('Y-m-d',strtotime("-1 days"));
         $whereClause = "leads.creation_date = '$prevDate'";
         echo "<script>localStorage['filtered']='yesterday';</script>";
      }
      elseif($filter_by =='past_3_days')
      {
         $past_three_days = date('Y-m-d',strtotime("-2 days"));
         $whereClause = "leads.creation_date >= '$past_three_days'";
         echo "<script>localStorage['filtered']='past_3_days';</script>";
      }
      elseif($filter_by =='week')
      {
         $past_three_days = date('Y-m-d',strtotime("-6 days"));
         $whereClause = "leads.creation_date >= '$past_three_days'";
         echo "<script>localStorage['filtered']='week';</script>";
      }
      elseif($filter_by =='month')
      {
         $month = date('m-Y');
         $start_date_temp = '01-'.$month;
         $start_date = date('Y-m-d',strtotime($start_date_temp));
         $end_date_temp = '31-'.$month;
         $end_date = date('Y-m-d',strtotime($end_date_temp));
         $whereClause = "leads.creation_date BETWEEN '$start_date' AND '$end_date'";
      }
      elseif($filter_by == 'all')
      {  
         if(session('role')=='superuser')
         {
            //$leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Work In Process' ORDER BY leads.creation_date DESC");
            $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Delivery In Progress' OR leads.lead_status = 'Vendor Assigned') ORDER BY leads.creation_date DESC");
         }
         elseif(session('role')=='admin')
         {
            $user_city = session('user_city');
            //$leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Work In Process' AND customer_details.citygroup='$user_city' ORDER BY leads.creation_date DESC");
            $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Delivery In Progress' OR leads.lead_status = 'Vendor Assigned') AND customer_details.citygroup='$user_city' ORDER BY leads.creation_date DESC");
         }
         elseif(session('role')=='user')
         {
            $lead_owner = session('user_id');
            //$leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Work In Process' AND leads.lead_owner='$lead_owner' ORDER BY leads.creation_date DESC");
            $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Delivery In Progress' OR leads.lead_status = 'Vendor Assigned') AND leads.lead_owner='$lead_owner' ORDER BY leads.creation_date DESC");
         }
         $data['lead_details'] = json_decode(json_encode($leads), true);
         for ($i=0; $i < count($data['lead_details']); $i++) 
         { 
            $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
            $user_id = $data['lead_details'][$i]['lead_owner'];
            $lead_owner_details = DB::select("SELECT * FROM user WHERE id=$user_id");
            $data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
            $data['lead_details'][$i]['username'] =  $data['lead_owner_details'][0]['username'];
            $data['lead_details'][$i]['user_id'] =  $data['lead_owner_details'][0]['id'];
            //print_r($product);
            $equipement_details = array();
            for ($j=0; $j <count($product); $j++) 
            { 
               $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
               $product_details = json_decode(json_encode($product_details), true);
               array_push($equipement_details,$product_details[0]['product_name']);
            }
            $equipements = json_encode($equipement_details);
            $data['lead_details'][$i]['equipment_requirement'] = $equipements;
         }
         echo "<script>localStorage['filtered']='all';</script>";
         return view('/leads/viewConvertedLeads',$data);
      }
      if(session('role')=='superuser')
      {
         //$leads = DB::select("SELECT * FROM leads,customer_details WHERE $whereClause ORDER BY leads.creation_date DESC");
         $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Delivery In Progress' OR leads.lead_status = 'Vendor Assigned') AND $whereClause ORDER BY leads.creation_date DESC");
      }
      elseif(session('role')=='admin')
      {
         $user_city = session('user_city');
         //$leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.lead_status = 'Work In Process' AND customer_details.citygroup='$user_city' AND $whereClause ORDER BY leads.creation_date DESC");
         $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Delivery In Progress' OR leads.lead_status = 'Vendor Assigned') AND customer_details.citygroup='$user_city' AND $whereClause ORDER BY leads.creation_date DESC");
      }
      elseif(session('role')=='user')
      {
         $lead_owner = session('user_id');
         //$leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.lead_status = 'Work In Process' AND leads.lead_owner='$lead_owner' AND $whereClause ORDER BY leads.creation_date DESC");
         $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Delivery In Progress' OR leads.lead_status = 'Vendor Assigned') AND leads.lead_owner='$lead_owner' AND $whereClause ORDER BY leads.creation_date DESC");
      }

      
      //echo "SELECT * FROM leads,customer_details WHERE leads.lead_status = 'Work In Process' AND $whereClause AND ORDER BY leads.creation_date DESC";
      $data['lead_details'] = json_decode(json_encode($leads), true);
      for ($i=0; $i < count($data['lead_details']); $i++) 
      { 
         $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
         $user_id = $data['lead_details'][$i]['lead_owner'];
         $lead_owner_details = DB::select("SELECT * FROM user WHERE id=$user_id");
         $data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
         $data['lead_details'][$i]['username'] =  $data['lead_owner_details'][0]['username'];
         $data['lead_details'][$i]['user_id'] =  $data['lead_owner_details'][0]['id'];
         //print_r($product);
         $equipement_details = array();
         for ($j=0; $j <count($product); $j++) 
         { 
            $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
            $product_details = json_decode(json_encode($product_details), true);
            array_push($equipement_details,$product_details[0]['product_name']);
         }
         $equipements = json_encode($equipement_details);
         $data['lead_details'][$i]['equipment_requirement'] = $equipements;
      }
      return view('/leads/viewConvertedLeads',$data);
   }
   //closed LEad
   public function filterClosedLeads($filter_by)
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
          return redirect()->to($url);
      }
      if($filter_by =='today')
      {
         $date = date('Y-m-d');
         $whereClause = "leads.creation_date = '$date'";
         echo "<script>localStorage['filtered']='today';</script>";
      }
      elseif($filter_by =='yesterday')
      {
         $prevDate = date('Y-m-d',strtotime("-1 days"));
         $whereClause = "leads.creation_date = '$prevDate'";
         echo "<script>localStorage['filtered']='yesterday';</script>";
      }
      elseif($filter_by =='past_3_days')
      {
         $past_three_days = date('Y-m-d',strtotime("-2 days"));
         $whereClause = "leads.creation_date >= '$past_three_days'";
         echo "<script>localStorage['filtered']='past_3_days';</script>";
      }
      elseif($filter_by =='week')
      {
         $past_three_days = date('Y-m-d',strtotime("-6 days"));
         $whereClause = "leads.creation_date >= '$past_three_days'";
         echo "<script>localStorage['filtered']='week';</script>";
      }
      elseif($filter_by =='month')
      {
         $month = date('m-Y');
         $start_date_temp = '01-'.$month;
         $start_date = date('Y-m-d',strtotime($start_date_temp));
         $end_date_temp = '31-'.$month;
         $end_date = date('Y-m-d',strtotime($end_date_temp));
         $whereClause = "leads.creation_date BETWEEN '$start_date' AND '$end_date'";
      }
      elseif($filter_by == 'all')
      {
         if(session('role')=='superuser')
         {
            $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status != 'Converted' AND leads.lead_status != 'Work In Process' AND leads.lead_status != 'Quali5Care' AND leads.lead_status != 'Mobile Generated' AND leads.lead_status != 'Order Generated' AND leads.lead_status != 'Delivery In Progress' AND leads.lead_status != 'Vendor Assigned') ORDER BY leads.creation_date DESC");
         }
         elseif(session('role')=='admin')
         {
            $user_city = session('user_city');
            //$leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Delivery In Progress' OR leads.lead_status = 'Vendor Assigned') AND customer_details.citygroup='$user_city' ORDER BY leads.creation_date DESC");
            $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status != 'Converted' AND leads.lead_status != 'Work In Process' AND leads.lead_status != 'Quali5Care' AND leads.lead_status != 'Mobile Generated' AND leads.lead_status != 'Order Generated' AND leads.lead_status != 'Delivery In Progress' AND leads.lead_status != 'Vendor Assigned') AND customer_details.citygroup='$user_city' ORDER BY leads.creation_date DESC");
         }
         elseif(session('role')=='user')
         {
            $lead_owner = session('user_id');
            //$leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Delivery In Progress' OR leads.lead_status = 'Vendor Assigned') AND leads.lead_owner='$lead_owner' ORDER BY leads.creation_date DESC");
            $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status != 'Converted' AND leads.lead_status != 'Work In Process' AND leads.lead_status != 'Quali5Care' AND leads.lead_status != 'Mobile Generated' AND leads.lead_status != 'Order Generated' AND leads.lead_status != 'Delivery In Progress' AND leads.lead_status != 'Vendor Assigned') AND leads.lead_owner='$lead_owner' ORDER BY leads.creation_date DESC");
         }

         //$leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status != 'Converted' AND leads.lead_status != 'Work In Process' AND leads.lead_status != 'Quali5Care' AND leads.lead_status != 'Mobile Generated' AND leads.lead_status != 'Order Generated' AND leads.lead_status != 'Delivery In Progress' AND leads.lead_status != 'Vendor Assigned' ORDER BY leads.creation_date DESC");
         $data['lead_details'] = json_decode(json_encode($leads), true);
         for ($i=0; $i < count($data['lead_details']); $i++) 
         { 
            $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
            $user_id = $data['lead_details'][$i]['lead_owner'];
            $lead_owner_details = DB::select("SELECT * FROM user WHERE id=$user_id");
            $data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
            $data['lead_details'][$i]['username'] =  $data['lead_owner_details'][0]['username'];
            $data['lead_details'][$i]['user_id'] =  $data['lead_owner_details'][0]['id'];
            //print_r($product);
            $equipement_details = array();
            for ($j=0; $j <count($product); $j++) 
            { 
               $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
               $product_details = json_decode(json_encode($product_details), true);
               array_push($equipement_details,$product_details[0]['product_name']);
            }
            $equipements = json_encode($equipement_details);
            $data['lead_details'][$i]['equipment_requirement'] = $equipements;
         }
         echo "<script>localStorage['filtered']='all';</script>";
         return view('/leads/viewClosedLeads',$data);
      }
      if(session('role')=='superuser')
      {
         $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status != 'Converted' AND leads.lead_status != 'Work In Process' AND leads.lead_status != 'Quali5Care' AND leads.lead_status != 'Mobile Generated' AND leads.lead_status != 'Order Generated' AND leads.lead_status != 'Delivery In Progress' AND leads.lead_status != 'Vendor Assigned') AND $whereClause ORDER BY leads.creation_date DESC");
      }
      elseif(session('role')=='admin')
      {
         $user_city = session('user_city');
         $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status != 'Converted' AND leads.lead_status != 'Work In Process' AND leads.lead_status != 'Quali5Care' AND leads.lead_status != 'Mobile Generated' AND leads.lead_status != 'Order Generated' AND leads.lead_status != 'Delivery In Progress' AND leads.lead_status != 'Vendor Assigned') AND customer_details.citygroup='$user_city' AND $whereClause ORDER BY leads.creation_date DESC");
      }
      elseif(session('role')=='user')
      {
         $lead_owner = session('user_id');
         
         $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status != 'Converted' AND leads.lead_status != 'Work In Process' AND leads.lead_status != 'Quali5Care' AND leads.lead_status != 'Mobile Generated' AND leads.lead_status != 'Order Generated' AND leads.lead_status != 'Delivery In Progress' AND leads.lead_status != 'Vendor Assigned') AND leads.lead_owner='$lead_owner' AND $whereClause ORDER BY leads.creation_date DESC");
      }
      
      //echo "SELECT * FROM leads,customer_details WHERE leads.lead_status = 'Work In Process' AND $whereClause AND ORDER BY leads.creation_date DESC";
      $data['lead_details'] = json_decode(json_encode($leads), true);
      for ($i=0; $i < count($data['lead_details']); $i++) 
      { 
         $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
         $user_id = $data['lead_details'][$i]['lead_owner'];
         $lead_owner_details = DB::select("SELECT * FROM user WHERE id=$user_id");
         $data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
         $data['lead_details'][$i]['username'] =  $data['lead_owner_details'][0]['username'];
         $data['lead_details'][$i]['user_id'] =  $data['lead_owner_details'][0]['id'];
         //print_r($product);
         $equipement_details = array();
         for ($j=0; $j <count($product); $j++) 
         { 
            $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
            $product_details = json_decode(json_encode($product_details), true);
            array_push($equipement_details,$product_details[0]['product_name']);
         }
         $equipements = json_encode($equipement_details);
         $data['lead_details'][$i]['equipment_requirement'] = $equipements;
      }
      return view('/leads/viewClosedLeads',$data);

         
   }
   function sendsms($mobile, $message)
   {	
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
         $url = url('/');
         return redirect()->to($url);
      }
      $curl = curl_init();
         // Route is 4 for transactional and 1 for promootional messages
         $url ="https://api.msg91.com/api/sendhttp.php?mobiles=".$mobile."&authkey=267641A4vHkf04R35d7644d0&route=4&sender=QUALCR&message=".$message ."&country=91";
      curl_setopt_array($curl, array(
         //CURLOPT_URL =>"https://api.msg91.com/api/sendhttp.php?mobiles=8369364948&authkey=267641A4vHkf04R35d7644d0&route=4&sender=QUALCR&message=Hello!Finally working test message&country=91",
         CURLOPT_URL => $url,
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_ENCODING => "",
         CURLOPT_MAXREDIRS => 10,
         CURLOPT_TIMEOUT => 30,
         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
         CURLOPT_CUSTOMREQUEST => "GET",
         CURLOPT_SSL_VERIFYHOST => 0,
         CURLOPT_SSL_VERIFYPEER => 0,
      ));
      // change this line commented for testing
      $response = curl_exec($curl);
      $err = curl_error($curl);
      curl_close($curl);
   }
   public function add_lead_comment($user_id,$lead_id,$desc)
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
       return redirect()->to($url);
      }
      $Lead = new lead();
      //$leads->where('id',$id)->delete();
      $timestamp = date("d M, h:i A");
      $comment = "[".$timestamp."]".$desc."\n";
      $lead_status = [
         'comment' => $comment
      ];
      $cmt_check = DB::select("SELECT comment FROM leads WHERE id = '$lead_id' ");
      $data['cmt_check'] = json_decode(json_encode($cmt_check), true);
   
      if(isset($data['cmt_check'][0]['comment']))
      {
		//$cmt_update = "UPDATE Lead SET comment = CONCAT(comment, '$comment') WHERE Lead_id = '$lead_id' ";
		$cmt_update = DB::update("UPDATE leads SET comment = CONCAT('$comment',comment) WHERE id = '$lead_id' ");
		
		//$Lead->where('Lead_id',$lead_id)->update($lead_status);
	  }
	  else
	  {
		//print_r($lead_status);
		$Lead->where('id',$lead_id)->update($lead_status);
	  }
	  //$cmt_update = "UPDATE Lead SET comment = CONCAT(comment, '$comment') WHERE Lead_id = '$lead_id' ";
      //return redirect('/view_all_inprocess_leads')->with('message', 'comment add Successfully');
      return redirect()->back()->with('message', 'comment add Successfully');
      //return $this->viewAllLeads();
   }
   public function generate_challan()
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
          return redirect()->to($url);
      }
      $data = 
         [
            'to' => 'Test Customer', 
            'address' => 'Shop no 6, Surya House, Vidyavihar Station Road, Vidyavihar(E), Mumbai - 400077', 
            'contact_no' => '9370738471 / 7559316562', 
            'order_number' => '85642157', 
            'date_sent' => '21-07-2021', 
            'challan_no' => '125', 
            'rent_equipment_name1' => 'Standard Walker', 
            'rent_equipment_qty1' => '1', 
            'equipment_rent1' => '850',
            'equipment_deposit1' => '2000', 
            'sale_equipment_name1' => 'Walker with Wheel', 
            'sale_equipment_qty1' => '1', 
            'equipment_sale_rate1' => '2500', 
            'transport' => '250',
            'total' => '4350'
         ];
      //$data = $cdata;
      $url = url('/');
      $pdf = PDF::loadView('delivery_challan', $data);
      file_put_contents("/var/www/html/prodweb/eflow/assets/uploads/challan/".'Testing'.".pdf", $pdf->output()); 
      $file_path = "/assets/uploads/challan/".'Testing'.".pdf";
      print_r($data);
      echo $file_path;
      // return $file_path;
   }

   public function sent_challan()
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
          return redirect()->to($url);
      }
	   //print_r($_POST);
		$lead_id = $_POST['modal_lead_id'];
		$cust_email = $_POST['modal_email_id'];
		$customer_id = $_POST['modal_cust_id'];
      $get_cust_detail = DB::select("SELECT * FROM leads,customer_details Where leads.id = '$lead_id' AND customer_details.cust_id='$customer_id'");
      $data['get_cust_detail'] = json_decode(json_encode($get_cust_detail),true);

	   $cust_name = $data['get_cust_detail'][0]['customer_name'];
      //$cust_email =  $data['get_cust_detail'][0]['email_id'];
	   $challan_file = $data['get_cust_detail'][0]['delivery_challan'];
	   //   echo $challan_file;
	   $pdf_file = file_get_contents("/var/www/html/prodweb/eflow/".$challan_file);
	  //echo $fil;
		
      $data_message = array(
         'customer_email'=>$cust_email,
         'customer_name'=>$cust_name,
         );
         //$data_message['mail_data'] = $mail_data;

      Mail::send('leads_email/delivery_challan',$data_message, function($message) use ($cust_email,$pdf_file)
      {     
         	$message->to($cust_email, 'Quali55Care -Prdouct Challan')->subject('Quali55Care -Product Challan');
         	$message->from('tempmailquali@gmail.com', 'Quali55Care');
			$message->attachData($pdf_file,'Challan.pdf');
	// 			//$message->attach(asset($challan_file), ['mime' => 'application/pdf']);
      });
	  
	  return redirect()->back()->with('email_sent','Email sent successfully');
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

   //date search filter 
   public function LeadsDateSearch()
   {
      $start_date = $_POST['start_date'];
      $end_date = $_POST['end_date'];
      if(session('role') == "superuser")
      {
         $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.creation_date BETWEEN '$start_date' AND '$end_date'  ORDER BY leads.creation_date DESC");
      }
      elseif(session('role') == "admin")
      {
         $user_city = session('user_city');
         $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.creation_date BETWEEN '$start_date' AND '$end_date' AND customer_details.citygroup='$user_city' ORDER BY leads.creation_date DESC");
      }
      elseif(session('role') == "user")
      {
         $lead_owner = session('user_id');
         $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.creation_date BETWEEN '$start_date' AND '$end_date' leads.lead_owner = $lead_owner ORDER BY leads.creation_date DESC");
      }
      $data['lead_details'] = json_decode(json_encode($leads), true);
      for ($i=0; $i < count($data['lead_details']); $i++) 
      { 
         $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
         $user_id = $data['lead_details'][$i]['lead_owner'];
         $lead_owner_details = DB::select("SELECT * FROM user WHERE id=$user_id");
         $data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
         $data['lead_details'][$i]['username'] =  $data['lead_owner_details'][0]['username'];
         $data['lead_details'][$i]['user_id'] =  $data['lead_owner_details'][0]['id'];
         //print_r($product);
         $equipement_details = array();
         for ($j=0; $j <count($product); $j++) 
         { 
            $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
            $product_details = json_decode(json_encode($product_details), true);
            array_push($equipement_details,$product_details[0]['product_name']);
         }
         $equipements = json_encode($equipement_details);
         $data['lead_details'][$i]['equipment_requirement'] = $equipements;
      }
      echo "<script>localStorage['filtered']='past_3_days';</script>";
      return view('leads/viewAllLeads',$data);
   }

   public function AddComment(Request $request,$lead_id)
   {
      $Lead = new lead();
      //$leads->where('id',$id)->delete();
      $comment = $request->get('comments');
      echo $comment;
      $timestamp = date("d M, h:i A");
      $desc = "[".$timestamp."]".$comment."\n";
      $lead_status = [
         'comment' => $desc
      ];
      $cmt_check = DB::select("SELECT comment FROM leads WHERE id = '$lead_id' ");
      $data['cmt_check'] = json_decode(json_encode($cmt_check), true);
   
      if(isset($data['cmt_check'][0]['comment']))
      {
		   $cmt_update = DB::update("UPDATE leads SET comment = CONCAT('$desc',comment) WHERE id = '$lead_id' ");
      }
      else
      {
         $Lead->where('id',$lead_id)->update($lead_status);
      }
      return redirect()->back()->with('message', 'comment add Successfully');
   }
   
   //lead by link
   public function GenerateLinkid()
   {
      $link_id = Str::random(6);
      if(LinkCustDetails::where('link_id',$link_id)->doesntExist())
      {
         return $link_id;
      }
      else
      {
         $this->GenerateLinkid();
      }
   }

   // public function SendLinkLead(Request $request)
   // {
   //    $LinkCustDetails = new LinkCustDetails();
   //    $CustomerDetails = new customer_detail();
   //    if($request->isMethod('get'))
   //    {
   //       $get_products = DB::table('products')->where('flag','Active')->get();
   //       $today = Carbon::today();
   //       $yesterday = Carbon::yesterday();
   //       $whereClause = [];
   //       $role='superuser';
   //       if(session('role') == "user")
   //       {
   //          $user_id = session('user_id');
   //          $whereClause1 = ['created_by','=',$user_id];
   //          array_push($whereClause,$whereClause1);
   //       }
   //       $get_all_links = DB::table('link_cust_details')
   //                            ->where('link_type','=','L')
   //                            ->where($whereClause)
   //                            ->WhereBetween(DB::raw('DATE(created_at)'), [$yesterday, $today])
   //                            ->orderBy('id','DESC')
   //                            ->get();
         
   //       $products_name_arr = json_decode(json_encode($get_all_links->toArray()),true);
   //       foreach($get_all_links as $key=>$all_links)
   //       {
   //          $products = json_decode($all_links->products);
   //          $product_name = DB::table('products')
   //                               ->select('product_name')
   //                               ->whereIn('id',$products)
   //                               ->get('product_name')->pluck('product_name')->toArray();
   //          $imp_products = implode(',',$product_name);
   //          $products_name_arr['data'][$key]['product_name']=$imp_products;
   //       }
   //       return view('leads.send_link_lead',compact('get_products','get_all_links','products_name_arr'));
   //    }
   //    if($request->isMethod('post'))
   //    {
   //       $request->validate(
   //          [
   //             'primary_contact_no' => 'required|digits_between:10,10',
   //          ]
   //       );
         
   //       $contact_no = $request->get('primary_contact_no');
   //       $get_update_link = $request->get('update_link_id');
   //       $update_flag = $request->get('update_link_flag');
   //       $btn_gen = $request->get('btn_gen_link');
        
   //       //$status = LinkCustDetails::where('primary_contact_no',$contact_no)->where(array(['link_status','=','0']))->whereRaw('created_at > now() - interval 24 hour')->exists();
   //       $status = LinkCustDetails::where('primary_contact_no',$contact_no)->where(array(['link_status','=','0']))->where("created_at",">",Carbon::now()->subDay())->where("created_at","<",Carbon::now())->exists();
   //       if($btn_gen=='Generate_Link' && $status=='true')
   //       {
   //          //$get_link = LinkCustDetails::where('primary_contact_no',$contact_no)->where(array(['link_status','=','0']))->whereRaw('created_at > now() - interval 24 hour')->get();
   //          $get_link = LinkCustDetails::where('primary_contact_no',$contact_no)->where(array(['link_status','=','0']))->where("created_at",">",Carbon::now()->subDay())->where("created_at","<",Carbon::now())->get();
   //          $link_id = $get_link[0]->link_id;
   //          $customer_name = $get_link[0]->customer_name;
   //          $products = $get_link[0]->products;
   //          $contact_no = $get_link[0]->primary_contact_no;
            
   //          $url = url('/')."/create_lead_link/".$get_update_link;
   //          return redirect()->back()->with(['CustExist'=>'Link already existed for '.$contact_no.' and status is live',
   //                                              'session_link_id'=>$link_id,
   //                                              'session_customer_name'=>$customer_name,
   //                                              'session_contact_no'=>$contact_no,
   //                                              'session_products'=>$products
   //          ]);
           
   //       }
   //       if($btn_gen=='Update_Link' && $status=='true' && LinkCustDetails::where(array(['link_status','=','0'],['link_id','=',$get_update_link],['primary_contact_no','=',$contact_no]))->where("created_at",">",Carbon::now()->subDay())->where("created_at","<",Carbon::now())->exists()=='true')
   //       {
               
   //                $update_data = [
   //                'primary_contact_no'=> $request->get('primary_contact_no'),
   //                'customer_name'=>$request->get('customer_name'),
   //                'products'=>json_encode($request->get('product_required')),
   //                //'link_id'=>$get_update_link,
   //                'created_by'=>session('user_id')
   //             ];
   //             $LinkCustDetails->where('link_id',$get_update_link)->update($update_data);
   //             $url = url('/')."/create_lead_link/".$get_update_link;
   //             return redirect()->back()->with('link',$url);
   //       }
   //       else
   //       {
   //          if($btn_gen=='Update_Link' && $status=='true')
   //          {
   //             $get_link = LinkCustDetails::where('primary_contact_no',$contact_no)->where(array(['link_status','=','0']))->where("created_at",">",Carbon::now()->subDay())->where("created_at","<",Carbon::now())->get();
   //             $link_id = $get_link[0]->link_id;
   //             $customer_name = $get_link[0]->customer_name;
   //             $products = $get_link[0]->products;
   //             $contact_no = $get_link[0]->primary_contact_no;
               
   //             $url = url('/')."/create_lead_link/".$get_update_link;
   //             return redirect()->back()->with(['CustExist'=>'Link already existed for '.$contact_no.' and status is live',
   //                                                 'session_link_id'=>$link_id,
   //                                                 'session_customer_name'=>$customer_name,
   //                                                 'session_contact_no'=>$contact_no,
   //                                                 'session_products'=>$products
   //             ]);
   //          }
   //          else
   //          {
   //             $link_id = $this->GenerateLinkid();
   //             $url = url('/')."/create_lead_link/".$link_id;
   //             $insert_data = [
   //                'primary_contact_no'=> $request->get('primary_contact_no'),
   //                'customer_name'=>$request->get('customer_name'),
   //                'products'=>json_encode($request->get('product_required')),
   //                'link_id'=>$link_id,
   //                'created_by'=>session('user_id')
   //             ];
   //             $LinkCustDetails->insert($insert_data);
   //             return redirect()->back()->with('link',$url);
   //          }
            
   //       }
       
   //    }   
   // }

   //changes code 

   public function SendLinkLead(Request $request)
   {
      $LinkCustDetails = new LinkCustDetails();
      $CustomerDetails = new customer_detail();
      if($request->isMethod('get'))
      {
         $get_products = DB::table('products')->where('flag','Active')->get();
         $today = Carbon::today();
         $yesterday = Carbon::yesterday();
         $whereClause = [];
         $role='superuser';
         if(session('role') == "user")
         {
            $user_id = session('user_id');
            $whereClause1 = ['created_by','=',$user_id];
            array_push($whereClause,$whereClause1);
         }
         $get_all_links = DB::table('link_cust_details')
                              ->where('link_type','=','L')
                              ->where($whereClause)
                              ->WhereBetween(DB::raw('DATE(created_at)'), [$yesterday, $today])
                              ->orderBy('id','DESC')
                              ->get();
         
         $products_name_arr = json_decode(json_encode($get_all_links->toArray()),true);
         foreach($get_all_links as $key=>$all_links)
         {
            $products = json_decode($all_links->products);
            $product_name = DB::table('products')
                                 ->select('product_name')
                                 ->whereIn('id',$products)
                                 ->get('product_name')->pluck('product_name')->toArray();
            $imp_products = implode(',',$product_name);
            $products_name_arr['data'][$key]['product_name']=$imp_products;
         }
         return view('leads.send_link_lead',compact('get_products','get_all_links','products_name_arr'));
      }
      if($request->isMethod('post'))
      {
         $request->validate(
            [
               'primary_contact_no' => 'required|digits_between:10,10',
               'lead_source' => 'required',
            ],
            [
               'primary_contact_no.required' => 'Customer No is required',
               'primary_contact_no.digits_between' => 'Customer Number should be minimum 10 digit',
               'lead_source.required' => 'Lead source is required',
            ]);
         
         $contact_no = $request->get('primary_contact_no');
         $get_update_link = $request->get('update_link_id');
         $update_flag = $request->get('update_link_flag');
         $btn_gen = $request->get('btn_gen_link');
        
         //$status = LinkCustDetails::where('primary_contact_no',$contact_no)->where(array(['link_status','=','0']))->whereRaw('created_at > now() - interval 24 hour')->exists();
         $status = LinkCustDetails::where('primary_contact_no',$contact_no)->where(array(['link_status','=','0']))->where("created_at",">",Carbon::now()->subDay())->where("created_at","<",Carbon::now())->exists();
         if($btn_gen=='Generate_Link' && $status=='true')
         {
            //$get_link = LinkCustDetails::where('primary_contact_no',$contact_no)->where(array(['link_status','=','0']))->whereRaw('created_at > now() - interval 24 hour')->get();
            $get_link = LinkCustDetails::where('primary_contact_no',$contact_no)->where(array(['link_status','=','0']))->where("created_at",">",Carbon::now()->subDay())->where("created_at","<",Carbon::now())->get();
            $link_id = $get_link[0]->link_id;
            $customer_name = $get_link[0]->customer_name;
            $products = $get_link[0]->products;
            $contact_no = $get_link[0]->primary_contact_no;
            
            $url = url('/')."/create_lead_link/".$get_update_link;
            return redirect()->back()->with(['CustExist'=>'Link already existed for '.$contact_no.' and status is live',
                                                'session_link_id'=>$link_id,
                                                'session_customer_name'=>$customer_name,
                                                'session_contact_no'=>$contact_no,
                                                'session_products'=>$products
            ]);
           
         }
         if($btn_gen=='Update_Link' && $status=='true' && LinkCustDetails::where(array(['link_status','=','0'],['link_id','=',$get_update_link],['primary_contact_no','=',$contact_no]))->where("created_at",">",Carbon::now()->subDay())->where("created_at","<",Carbon::now())->exists()=='true')
         {
               
                  $update_data = [
                  'primary_contact_no'=> $request->get('primary_contact_no'),
                  'customer_name'=>$request->get('customer_name'),
                  'products'=>json_encode($request->get('product_required')),
                  //'link_id'=>$get_update_link,
                  'created_by'=>session('user_id')
               ];
               $LinkCustDetails->where('link_id',$get_update_link)->update($update_data);
               $url = url('/')."/create_lead_link/".$get_update_link;
               return redirect()->back()->with('link',$url);
         }
         else
         {
            if($btn_gen=='Update_Link' && $status=='true')
            {
               $get_link = LinkCustDetails::where('primary_contact_no',$contact_no)->where(array(['link_status','=','0']))->where("created_at",">",Carbon::now()->subDay())->where("created_at","<",Carbon::now())->get();
               $link_id = $get_link[0]->link_id;
               $lead_source = $get_link[0]->lead_source;
               $customer_name = $get_link[0]->customer_name;
               $products = $get_link[0]->products;
               $contact_no = $get_link[0]->primary_contact_no;
               
               $url = url('/')."/create_lead_link/".$get_update_link;
               return redirect()->back()->with(['CustExist'=>'Link already existed for '.$contact_no.' and status is live',
                                                   'session_link_id'=>$link_id,
                                                   'session_customer_name'=>$customer_name,
                                                   'session_contact_no'=>$contact_no,
                                                   'session_products'=>$products,
                                                   'session_lead_source'=>$lead_source]);
            }
            else
            {
               $link_id = $this->GenerateLinkid();
               $url = url('/')."/create_lead_link/".$link_id;
               $insert_data = [
                  'primary_contact_no'=> $request->get('primary_contact_no'),
                  'customer_name'=>$request->get('customer_name'),
                  'products'=>json_encode($request->get('product_required')),
                  'link_id'=>$link_id,
                  'lead_source'=>$request->get('lead_source'),
                  'created_by'=>session('user_id')
               ];
               $LinkCustDetails->insert($insert_data);
               return redirect()->back()->with('link',$url);
            }
            
         }
       
      }   
   }

   // public function CreateLeadLink(Request $request,$link_id)
   // {
   //    $LinkCustDetails = new LinkCustDetails();
   //    $CustomerDetails = new customer_detail();
   //    $Lead = new lead();
   //    if($request->isMethod('get'))
   //    {
   //       if(LinkCustDetails::where(array(['link_id','=',$link_id],['link_status','=','0']))->exists())
   //       {
   //          $get_customer = LinkCustDetails::select('primary_contact_no','customer_name','products','created_by','created_at')->where('link_id',$link_id)->get();
   //          $link_created_at = $get_customer[0]->created_at;
   //          $start_date =new Carbon($link_created_at);
   //          $end_date =Carbon::now();
   //          $hourseDiff = $end_date->diffInHours($start_date);
   //          if($hourseDiff<24)
   //          {
   //             $products = DB::table('products')->where('flag','Active')->get();
   //             $cities = DB::table('cities')->get();
   //             $states = DB::table('states')->get();
   //             $countries = DB::table('countries')->get();
   //             $users = DB::table('user')->where('role','!=','vendor')->get();
               
   //             $customer_no = $get_customer[0]->primary_contact_no;
   //             $customer_name = $get_customer[0]->customer_name;
   //             $def_products = json_decode($get_customer[0]->products,true);
   //             $link_created_by = $get_customer[0]->created_by;
   //             //lead_owner info
   //             $get_lead_onwer = DB::table('user')->select('username','contact_no')->where('id',$link_created_by)->get();
   //             $username = $get_lead_onwer[0]->username;
   //             $lead_own_contact = $get_lead_onwer[0]->contact_no;
   
   //             $default_products = DB::table('products')->select('product_name')->whereIn('id',$def_products)->get()->pluck('product_name')->toArray();
   //             $default_products = implode(',',$default_products);
   
   //             $customer_details = null;
   //             if(customer_detail::where('primary_contact_no',$customer_no)->exists())
   //             {
   //                $customer_details = DB::table('customer_details')->where('primary_contact_no',$customer_no)->get()->toArray();
   //             }
   //             return view('leads/create_lead_send_link',compact('link_id','customer_no','customer_name','link_created_by','customer_details','products','cities','states','countries','users','default_products','username','lead_own_contact'));
   //          }
   //          else
   //          {
   //             $update_link_status = LinkCustDetails::where('link_id',$link_id)->update(['link_status'=>1]);
   //             $get_lead_owner_no = DB::table('link_cust_details')
   //                                  ->join('user','link_cust_details.created_by','=','user.id')
   //                                  ->select('contact_no','username')
   //                                  ->where('link_cust_details.link_id',$link_id)
   //                                  ->get();
   //             $contact_no = $get_lead_owner_no[0]->contact_no;
   //             $username = $get_lead_owner_no[0]->username;
   //             return view('Alert_Templates.Failed_Message',compact('contact_no','username'));
   //          }

           
   //       }
   //       elseif(LinkCustDetails::where('link_id',$link_id)->doesntExist())
   //       {
   //          echo "This Link not exist";
   //       }
   //       else
   //       {
   //          $get_lead_owner_no = DB::table('link_cust_details')
   //                                     ->join('user','link_cust_details.created_by','=','user.id')
   //                                     ->select('contact_no','username')
   //                                     ->where('link_cust_details.link_id',$link_id)
   //                                     ->get();
   //          $contact_no = $get_lead_owner_no[0]->contact_no;
   //          $username = $get_lead_owner_no[0]->username;
   //          return view('Alert_Templates.Failed_Message',compact('contact_no','username'));
   //       }
         
   //    }

   //    if($request->isMethod('post'))
   //    {
   //       if(LinkCustDetails::where(array(['link_id','=',$link_id],['link_status','=','0']))->exists())
   //       {
   //          $validatedData = $request->validate([
   //             'customer_no' => 'required|numeric|digits_between:10,10',
   //             'patient_age' => 'nullable|numeric|digits_between:1,3',
   //             'pincode' => 'required|numeric|digits_between:6,6',
   //             'email_id' => 'nullable|email',
   //             'secondary_contact_no'=>'nullable|numeric|digits_between:10,10',
   //             'terms_and_condition'=>'accepted'
   //             ], [
   //                   'customer_no.required' => 'Customer No is required',
   //                   'customer_no.digits_between' => 'Customer Number should be minimum 10 digit',
   //                   'patient_age.numeric' => 'Patient age should be numeric and maximum 3 digit',
   //                   'patient_age.digits_between' => 'Patient age should be numeric and maximum 3 digit',
   //                   'pincode.required' => 'Pincode Must be numeric and 6 didgit',
   //                   'pincode.numeric' => 'Pincode Must be numeric and 6 didgit',
   //                   'pincode.digits_between' => 'Pincode Must be numeric and 6 didgit',
   //                   'email_id.email'=>'Email is incorrect',
   //                   'secondary_contact_no.numeric'=>'Alternate number should be numeric',
   //                   'secondary_contact_no.digits_between'=>'Alternate number should be 10 digit',
   //             ]);
   //          $update_arr['customer_type']='Individual';
   //          if(!empty($request->get('customer_name')))
   //          {
   //             $update_arr['customer_name']=$request->get('customer_name');
   //          }
   //          // if(!empty($request->get('customer_no')))
   //          // {
   //          //    $update_arr['primary_contact_no']=$request->get('customer_no');
   //          // }
   //          if(!empty($request->get('location')))
   //          {
   //             $update_arr['location']=$request->get('location');
   //          }
   //          if(!empty($request->get('city')))
   //          {
   //             $update_arr['city']=$request->get('city');
   //          }
   //          if(!empty($request->get('state')))
   //          {
   //             $update_arr['state']=$request->get('state');
   //          }
   //          if(!empty($request->get('address_line_1')))
   //          {
   //             $update_arr['address_line_1']=$request->get('address_line_1');
   //          }
   //          if(!empty($request->get('address_line_2')))
   //          {
   //             $update_arr['address_line_2']=$request->get('address_line_2');
   //          }
   //          if(!empty($request->get('landmark')))
   //          {
   //             $update_arr['landmark']=$request->get('landmark');
   //          }
   //          if(!empty($request->get('area')))
   //          {
   //             $update_arr['area']=$request->get('area');
   //          }
   //          if(!empty($request->get('pincode')))
   //          {
   //             $update_arr['pincode']=$request->get('pincode');
   //          }
   //          if(!empty($request->get('email_id')))
   //          {
   //             $update_arr['email_id']=$request->get('email_id');
   //          }
   //          if(!empty($request->get('secondary_contact_no')))
   //          {
   //             $update_arr['secondary_contact_no']=$request->get('secondary_contact_no');
   //          }
   //          if(!empty($request->get('refered_by')))
   //          {
   //             $update_arr['refered_by']=$request->get('refered_by');
   //          }
   //          if(!empty($request->get('hospital_name')))
   //          {
   //             $update_arr['hospital_name']=$request->get('hospital_name');
   //          }

   //          //for deafult product or selected products
   //          $get_default_product = LinkCustDetails::select('products')->where('link_id',$link_id)->get()->pluck('products')->toArray();
   //          $default_products = json_decode($get_default_product[0],true);
   //          $cust_products = null;
   //          if(isset($default_products) && !empty($request->get('additional_equipments')))
   //          {
   //             $additional_products =$request->get('additional_equipments');
   //             $cust_products = json_encode(array_unique(array_merge($default_products,$additional_products)));
   //          }
   //          else{
   //             $cust_products = $get_default_product[0];
   //          }

   //          $get_lead_owner_no = DB::table('user')->select('contact_no','username')->where('id',$request->get('link_created_by'))->get();
   //          $contact_no = $get_lead_owner_no[0]->contact_no;
   //          $username = $get_lead_owner_no[0]->username;
   //          $cust_db_contact = LinkCustDetails::select('primary_contact_no')->where('link_id',$link_id)->get();
   //          $cust_contact = $cust_db_contact[0]->primary_contact_no;
   //          $update_arr['primary_contact_no']=$cust_contact;
   //          //update country by default
   //          $update_arr['country']="India";
   //          $customer = customer_detail::updateOrCreate(['primary_contact_no'=>$cust_contact],$update_arr);
   //          $cust_id = $customer->cust_id;
   //          $insertLead = [
   //             'customer_id'=>$cust_id,
   //             'creation_date'=>date('Y-m-d'),
   //             'patient_name' =>$request->get('patient_name'),
   //             'patient_age'=>$request->get('patient_age'),
   //             'doctor_name' =>$request->get('doctor_name'),
   //             'hospital_name'=>$request->get('hospital_name'),
   //             'equipment_requirement'=>$cust_products,
   //             'lead_status'=>'Work In Process',
   //             'lead_owner'=>$request->get('link_created_by'),
   //             'created_by'=>'By Link',
   //             'lead_source'=>'By Link'
   //          ];
   //          $Lead->insert($insertLead);
   //          $update_link_status = LinkCustDetails::where('link_id',$link_id)->update(['link_status'=>1,'terms_condition'=>1]);
   //          return view('Alert_Templates.Success_Message',compact('contact_no','username'));
   //       }
   //       elseif(LinkCustDetails::where('link_id',$link_id)->doesntExist())
   //       {
   //          echo "This Link not exist";
   //       }
   //       else
   //       {
   //          $get_lead_owner_no = DB::table('link_cust_details')
   //                               ->join('user','link_cust_details.created_by','=','user.id')
   //                               ->select('contact_no','username')
   //                               ->where('link_cust_details.link_id',$link_id)
   //                               ->get();
   //          $contact_no = $get_lead_owner_no[0]->contact_no;    
   //          $username = $get_lead_owner_no[0]->username;            
   //          return view('Alert_Templates.Failed_Message',compact('contact_no','username'));
   //       }
   //    }
   // }

   //changes code for up function

   public function CreateLeadLink(Request $request,$link_id)
   {
      $LinkCustDetails = new LinkCustDetails();
      $CustomerDetails = new customer_detail();
      $Lead = new lead();
      if($request->isMethod('get'))
      {
         if(LinkCustDetails::where(array(['link_id','=',$link_id],['link_status','=','0']))->exists())
         {
            $get_customer = LinkCustDetails::select('primary_contact_no','customer_name','products','created_by','created_at')->where('link_id',$link_id)->get();
            $link_created_at = $get_customer[0]->created_at;
            $start_date =new Carbon($link_created_at);
            $end_date =Carbon::now();
            $hourseDiff = $end_date->diffInHours($start_date);
            if($hourseDiff<24)
            {
               $products = DB::table('products')->where('flag','Active')->get();
               $cities = DB::table('cities')->get();
               $states = DB::table('states')->get();
               $countries = DB::table('countries')->get();
               $users = DB::table('user')->where('role','!=','vendor')->get();
               
               $customer_no = $get_customer[0]->primary_contact_no;
               $customer_name = $get_customer[0]->customer_name;
               $def_products = json_decode($get_customer[0]->products,true);
               $link_created_by = $get_customer[0]->created_by;
               //lead_owner info
               $get_lead_onwer = DB::table('user')->select('username','contact_no')->where('id',$link_created_by)->get();
               $username = $get_lead_onwer[0]->username;
               $lead_own_contact = $get_lead_onwer[0]->contact_no;
   
               $default_products = DB::table('products')->select('product_name')->whereIn('id',$def_products)->get()->pluck('product_name')->toArray();
               $default_products = implode(',',$default_products);
   
               $customer_details = null;
               if(customer_detail::where('primary_contact_no',$customer_no)->exists())
               {
                  $customer_details = DB::table('customer_details')->where('primary_contact_no',$customer_no)->get()->toArray();
               }
               return view('leads/create_lead_send_link',compact('link_id','customer_no','customer_name','link_created_by','customer_details','products','cities','states','countries','users','default_products','username','lead_own_contact'));
            }
            else
            {
               $update_link_status = LinkCustDetails::where('link_id',$link_id)->update(['link_status'=>2]);
               $get_lead_owner_no = DB::table('link_cust_details')
                                    ->join('user','link_cust_details.created_by','=','user.id')
                                    ->select('contact_no','username')
                                    ->where('link_cust_details.link_id',$link_id)
                                    ->get();
               $contact_no = $get_lead_owner_no[0]->contact_no;
               $username = $get_lead_owner_no[0]->username;
               return view('Alert_Templates.Failed_Message',compact('contact_no','username'));
            }

           
         }
         elseif(LinkCustDetails::where('link_id',$link_id)->doesntExist())
         {
            echo "This Link not exist";
         }
         else
         {
            $get_lead_owner_no = DB::table('link_cust_details')
                                       ->join('user','link_cust_details.created_by','=','user.id')
                                       ->select('contact_no','username')
                                       ->where('link_cust_details.link_id',$link_id)
                                       ->get();
            $contact_no = $get_lead_owner_no[0]->contact_no;
            $username = $get_lead_owner_no[0]->username;
            return view('Alert_Templates.Failed_Message',compact('contact_no','username'));
         }
         
      }

      if($request->isMethod('post'))
      {
         if(LinkCustDetails::where(array(['link_id','=',$link_id],['link_status','=','0']))->exists())
         {
            $validatedData = $request->validate([
               'customer_no' => 'required|numeric|digits_between:10,10',
               'patient_age' => 'nullable|numeric|digits_between:1,3',
               'pincode' => 'required|numeric|digits_between:6,6',
               'email_id' => 'nullable|email',
               'secondary_contact_no'=>'nullable|numeric|digits_between:10,10',
               'terms_and_condition'=>'accepted'
               ], [
                     'customer_no.required' => 'Customer No is required',
                     'customer_no.digits_between' => 'Customer Number should be minimum 10 digit',
                     'patient_age.numeric' => 'Patient age should be numeric and maximum 3 digit',
                     'patient_age.digits_between' => 'Patient age should be numeric and maximum 3 digit',
                     'pincode.required' => 'Pincode Must be numeric and 6 didgit',
                     'pincode.numeric' => 'Pincode Must be numeric and 6 didgit',
                     'pincode.digits_between' => 'Pincode Must be numeric and 6 didgit',
                     'email_id.email'=>'Email is incorrect',
                     'secondary_contact_no.numeric'=>'Alternate number should be numeric',
                     'secondary_contact_no.digits_between'=>'Alternate number should be 10 digit',
               ]);
            $update_arr['customer_type']='Individual';
            if(!empty($request->get('customer_name')))
            {
               $update_arr['customer_name']=$request->get('customer_name');
            }
            // if(!empty($request->get('customer_no')))
            // {
            //    $update_arr['primary_contact_no']=$request->get('customer_no');
            // }
            if(!empty($request->get('location')))
            {
               $update_arr['location']=$request->get('location');
            }
            if(!empty($request->get('city')))
            {
               $update_arr['city']=$request->get('city');
            }
            if(!empty($request->get('state')))
            {
               $update_arr['state']=$request->get('state');
            }
            if(!empty($request->get('address_line_1')))
            {
               $update_arr['address_line_1']=$request->get('address_line_1');
            }
            if(!empty($request->get('address_line_2')))
            {
               $update_arr['address_line_2']=$request->get('address_line_2');
            }
            if(!empty($request->get('landmark')))
            {
               $update_arr['landmark']=$request->get('landmark');
            }
            if(!empty($request->get('area')))
            {
               $update_arr['area']=$request->get('area');
            }
            if(!empty($request->get('pincode')))
            {
               $update_arr['pincode']=$request->get('pincode');
            }
            if(!empty($request->get('email_id')))
            {
               $update_arr['email_id']=$request->get('email_id');
            }
            if(!empty($request->get('secondary_contact_no')))
            {
               $update_arr['secondary_contact_no']=$request->get('secondary_contact_no');
            }
            if(!empty($request->get('refered_by')))
            {
               $update_arr['refered_by']=$request->get('refered_by');
            }
            if(!empty($request->get('hospital_name')))
            {
               $update_arr['hospital_name']=$request->get('hospital_name');
            }

            //for deafult product or selected products
            $get_default_product = LinkCustDetails::select('products')->where('link_id',$link_id)->get()->pluck('products')->toArray();
            $default_products = json_decode($get_default_product[0],true);
            $cust_products = null;
            if(isset($default_products) && !empty($request->get('additional_equipments')))
            {
               $additional_products =$request->get('additional_equipments');
               $cust_products = json_encode(array_unique(array_merge($default_products,$additional_products)));
            }
            else{
               $cust_products = $get_default_product[0];
            }

            $get_lead_owner_no = DB::table('user')->select('contact_no','username')->where('id',$request->get('link_created_by'))->get();
            $contact_no = $get_lead_owner_no[0]->contact_no;
            $username = $get_lead_owner_no[0]->username;
            $cust_db_contact = LinkCustDetails::select('primary_contact_no','lead_source')->where('link_id',$link_id)->get();
            $cust_contact = $cust_db_contact[0]->primary_contact_no;
            $lead_source = $cust_db_contact[0]->lead_source;
            $update_arr['primary_contact_no']=$cust_contact;
            //update country by default
            $update_arr['country']="India";
            $customer = customer_detail::updateOrCreate(['primary_contact_no'=>$cust_contact],$update_arr);
            $cust_id = $customer->cust_id;
            $insertLead = [
               'customer_id'=>$cust_id,
               'creation_date'=>date('Y-m-d'),
               'patient_name' =>$request->get('patient_name'),
               'patient_age'=>$request->get('patient_age'),
               'doctor_name' =>$request->get('doctor_name'),
               'hospital_name'=>$request->get('hospital_name'),
               'equipment_requirement'=>$cust_products,
               'lead_status'=>'Work In Process',
               'lead_owner'=>$request->get('link_created_by'),
               'created_by'=>'By Link',
               'lead_source'=>$lead_source,
               'generated_from'=>'Link'
            ];
            $Lead->insert($insertLead);
            $update_link_status = LinkCustDetails::where('link_id',$link_id)->update(['link_status'=>1,'terms_condition'=>1]);
            return view('Alert_Templates.Success_Message',compact('contact_no','username'));
         }
         elseif(LinkCustDetails::where('link_id',$link_id)->doesntExist())
         {
            echo "This Link not exist";
         }
         else
         {
            $get_lead_owner_no = DB::table('link_cust_details')
                                 ->join('user','link_cust_details.created_by','=','user.id')
                                 ->select('contact_no','username')
                                 ->where('link_cust_details.link_id',$link_id)
                                 ->get();
            $contact_no = $get_lead_owner_no[0]->contact_no;    
            $username = $get_lead_owner_no[0]->username;            
            return view('Alert_Templates.Failed_Message',compact('contact_no','username'));
         }
      }
   }

   public function ESC(Request $request)
   {
      if($request->isMethod('get'))
      {
         // $cust = customer_detail::where('cust_id',214)->get();

         // return view('testing.test',compact('cust'));
         $get_renewal_pickup_info = DB::table('order_details')
                                            ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                                            ->join('del_orders','order_details.order_id','=','del_orders.order_id')
                                            ->join('products','order_details.product_id','=','products.id')
                                            ->join('leads','del_orders.lead_id','=','leads.id')
                                            ->join('vendor_details','order_details.vendor_id','=','vendor_details.id')
                                            ->join('user','leads.lead_owner','=','user.id')
                                            ->select('order_details.id as order_details_id',
                                                    'order_details.pickup_date as pickup_date',
                                                    'order_details.vendor_id as vendor_id', 
                                                    'order_details.vendor_product_id as vendor_product_id', 
                                                    'order_details.sale_rental as sale_rental',
                                                    'order_details.product_qty as product_qty',
                                                    'order_details.product_rent as product_rent',
                                                    'order_details.product_deposite as product_deposite',
                                                    'order_details.transport as transport',
                                                    'order_details.pickup_date as pickup_date',
                                                    'order_details.customer_id as customer_id',
                                                    'order_details.product_id as product_id',
                                                    'order_details.current_status as current_status',
                                                    'customer_details.*',
                                                    'products.product_name as product_name',
                                                    'del_orders.order_id as order_id',
                                                    'del_orders.DelDate as DelDate',
                                                    'user.username as username',
                                                    'vendor_details.registered_name as vendor_name')
                                                ->where('order_details.sale_rental','=','Rental')
                                                ->whereIn('order_details.current_status',['Pending','Pending Renew','Renewed','Renewed Online'])
                                                ->groupBy('order_details.customer_id','')
                                                //->where('order_details.pickup_date','=',$today)
                                                //->orderBy('order_details.pickup_date','ASC')
                                                ->get();
         dd($get_renewal_pickup_info);
      }
      if($request->isMethod('post'))
      {
         $prod[0]['sale'] = utf8_encode("\n\nSale :"."hdjfhj");
         $prod[0]['name'] = utf8_encode('OÂdómetro');
         //$json = array('tag' => 'OÂdómetro'); 
         //$json = array_map('utf8_encode', $prod);
         echo $json = "{'data':".json_encode($prod)."}";
         //print_r($prod);
         //print_r($json = json_decode($json));
         //echo $json->{'tag'};
         //echo utf8_decode($json->{'tag'});
         //customer_detail::insert(['customer_name'=>$request->get('customer_name')]);
      }
   }


   public function updateStatus(Request $request)
   {
      $lead_id = $request->get('lead_id');
      $cust_id = $request->get('cust_id');
      $status = $request->get('status');
      $comment = $request->get('comment');
      $cmt_check = DB::select("SELECT comment FROM leads WHERE id = '$lead_id' ");
      $data['cmt_check'] = json_decode(json_encode($cmt_check), true);
      $old_status = "Converted";
      if(isset($data['cmt_check'][0]['comment']))
      {
         // $old_status = $data['cmt_check'][0]['comment'];
		   // $cmt_update = DB::update("UPDATE leads SET comment = CONCAT('$desc',comment) WHERE id = '$lead_id' ");
         $update =  DB::update("UPDATE leads SET lead_status = '$status', comment = CONCAT('$comment',comment) WHERE id = '$lead_id'");
      }
      else
      {
         lead::where('id',$lead_id)->update(['lead_status'=>$status,'comment'=>$comment]);
      }
      $insertData = [
         'order_type'=>'LD',
         'key_id'=>$lead_id,
         'operation'=>'Status Update',
         'fields'=>'lead_status',
         'old_value'=>$old_status,
         'new_value'=>$status,
         'updated_by'=>session('username')
      ];
      ActivityLog::insert($insertData);
      // $insert = lead::where('id',$lead_id)->update([''=>'$status','comment'=>'comment']);
      // echo"UPDATE leads SET lead_status = '$status', comment = CONCAT('$comment',comment) WHERE id = '$lead_id'";
      return "Success";
   }
}
