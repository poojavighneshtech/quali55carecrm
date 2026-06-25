<?php

namespace App\Http\Controllers\Leads;

use App\Http\Controllers\Controller;
use App\Models\Lead\customer_detail;
use App\Models\LinkCustDetails;
use App\Models\Lead\lead;
use App\Models\leads_log;
use App\Models\LeadsQueryLog;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use App\Models\GoogleCampaignReport;
use PDF;
use Mail;
use File;
use DateTime;
use DateTimeZone;
use DatePeriod;
use DateInterval;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DailyNewSiteOrders;
use App\Exports\ReminderOverdueMail;
use App\Exports\AllLeadsReportExport;
use App\Http\Controllers\RenewalPickup\RenewalPickupController;
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
   public function create_lead(Request $request)
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
         $customer_details = new customer_detail();
         $leads = new lead();
         $leads_log = new leads_log();
         $equipments = request()->get('equipment_id');
         $equipments_requirements = json_encode($equipments);
         //print_r($equipments_requirements);
         if($_POST['submit']=='submit')
         {
            
            // try {
               DB::enableQueryLog();
               $current_time = new DateTime("now", new DateTimeZone('Asia/Kolkata') );
               $current_time = $current_time->format('H:i:s');
               $creation_date = request()->get('creation_date')." ".$current_time;
               $cust_date = date('Y-m-d');
               $corporate_cust_id = null;
               
               $contact_person_1_name = null;
               $contact_person_1_no = null;
               $contact_person_2_name = null;
               $contact_person_2_no = null;
               
               if(request()->get('customer_type') == "Corporate")
               {
                  $corporate_cust_id = request()->get('corporate_cust_id');
                  $contact_person_1_name = request()->get('contact_person_1_name');
                  $contact_person_1_no = request()->get('contact_person_1_no');
                  $contact_person_2_name = request()->get('contact_person_2_name');
                  $contact_person_2_no = request()->get('contact_person_2_no');
               }
               $cutomer_details_insertData = [
                  'customer_name' => request()->get('cust_name'),
                  'cust_date' => request()->get('creation_date'),
                  'address_line_1' => request()->get('address_line_1'),
                  'address_line_2' => request()->get('address_line_2'),
                  'cust_gender' => request()->get('customer_gender'),
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
                  'corporate_cust_id' => $corporate_cust_id,
                  'contact_person_1_name' => $contact_person_1_name,
                  'contact_person_1_no' => $contact_person_1_no,
                  'contact_person_2_name' => $contact_person_2_name,
                  'contact_person_2_no' => $contact_person_2_no,
                  'created_at'=>$creation_date,
                  'created_by' => session('username')
               ];
               $customer_id = $customer_details->insertGetId($cutomer_details_insertData);

               $leads_insertData = [
                  'customer_id' => $customer_id,
                  'creation_date' => request()->get('creation_date'), 
                  'patient_name' => request()->get('patient_name'),
                  'patient_age' => request()->get('patient_age'),
                  'patient_gender' => request()->get('patient_gender'),
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

            // } catch (\Illuminate\Database\QueryException $ex) {
            //    $msg = $ex->getMessage();
            //    $LeadsQueryLog = new LeadsQueryLog();
            //    $insertQueryLog = [
            //       'user_id'=>session('user_id'),
            //       'operation'=>'Create Lead-submit button',
            //       'query'=>$msg,
            //       'created_by'=>session('username')
            //    ];
            //    $LeadsQueryLog->insert($insertQueryLog);
            //    //return redirect()->back();
            // }
            
         }
         if($_POST['submit']=='check')
         {
            // try {
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
                  'patient_gender' => request()->get('patient_gender'),
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
            // } catch (\Illuminate\Database\QueryException $ex) {
            //    $msg = $ex->getMessage();
            //    $LeadsQueryLog = new LeadsQueryLog();
            //    $insertQueryLog = [
            //       'user_id'=>session('user_id'),
            //       'operation'=>'Create Lead-check button',
            //       'query'=>$msg,
            //       'created_by'=>session('username')
            //    ];
            //    $LeadsQueryLog->insert($insertQueryLog);
            //    //return false;
            // }
            
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

         if(request()->get('hidden_def_patient_id') != "No" && request()->get('hidden_def_patient_id') != null){
            DB::table('lookup_table')->update(['patient_id'=>request()->get('hidden_def_patient_id')+1]);
         }
        //-----Send Lead Creation Message to Customer-----//
        $curl = curl_init();
        if(config('app.app_env') == 'devweb')
        {
            $mobile = config('app.developer_contact');
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
         $patient_id = DB::table('lookup_table')->get('patient_id');
         $data['patient_id'] = $patient_id[0]->patient_id;
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
         $data['lead_source'] = config('app.lead_source');
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
   //------view all leads with filter new code------//
	public function ViewAllLeads_new(Request $request)
	{
      // $this->new_site_daily_orders();
      $cities = DB::table('customer_details')->distinct('citygroup')->whereNotNull('citygroup')->orderBy('citygroup','ASC')->get('citygroup');
		// $get_lead_sources = DB::table('leads')->select('lead_source')->distinct('lead_source')->get();
		$get_lead_sources = config('app.lead_source');
		$lead_sources_arr = $get_lead_sources;
		$get_customer_location = DB::table('customer_details')->select('location')->distinct('location')->get();
		$customer_location_arr = array_column($get_customer_location->toArray(),'location');
		$lead_cancellation_reason = config('app.lead_cancellation');
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
      // dd(session('city_based_access'));
      if(session('city_based_access') == "1")
      {
         // dd("a");
         $whereCondition4 = ['customer_details.citygroup','=',session('user_city')];
			array_push($whereCondition,$whereCondition4);
		}
		$lead_source = $request->get('filter_lead_source');
		// if(isset($lead_source)){
		// 	$whereCondition6 = ['leads.lead_source','=',$lead_source];
		// 	array_push($whereCondition,$whereCondition6);
		// }
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

      $patient_name = $request->get('filter_patient_name');
		if(isset($patient_name)){
			$whereCondition11 = ['leads.patient_name','LIKE','%'.$patient_name.'%'];
			array_push($whereCondition,$whereCondition11);
		}
      $city = $request->get('filter_city');
		if(isset($city) && $city!='All'){
			$whereCondition12 = ['customer_details.citygroup','=',$city];
			array_push($whereCondition,$whereCondition12);
		}

     // dd($whereCondition);
		$from_date = $request->get('filter_from_date');
		$end_date = $request->get('filter_end_date');
      $dateArr = [];
      if(isset($from_date) && isset($end_date)){
         array_push($dateArr,$from_date);
         array_push($dateArr,$end_date);
      }
      else{
         if(count($whereCondition)==0)
         {
            if($lead_source == null){
               array_push($dateArr,date('Y-m-d'));
               array_push($dateArr,date('Y-m-d'));
            }
         }
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
         $orderTypeNotIn = config('app.order_type');        
         // $orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";
         $get_all_leads = DB::table('leads')
               ->join('customer_details','leads.customer_id','=','customer_details.cust_id')
               ->join('user','leads.lead_owner','=','user.id')
               ->join('del_orders','leads.id','=','del_orders.lead_id')
               ->select('leads.*','customer_details.*','del_orders.order_id as order_id','user.username as lead_owner','leads.comment as lead_comment','customer_details.comment as cust_comment')
               ->where($whereCondition)
               ->whereNotIn('del_orders.status',['Cancel'])
               ->when($request->get('filter_lead_source'),function(){
                  $query->whereIn('leads.lead_source',$request->get('filter_lead_source'));
               })
               //->whereBetween('leads.creation_date',[$get_min_date,$get_max_date])
               ->when($dateArr,function($query,$dateArr){
                  // $query->whereBetween('leads.creation_date',$dateArr);
                  $query->wherebetween(DB::raw("(STR_TO_DATE(leads.converted_at,'%Y-%m-%d'))"),$dateArr);
               })
               ->where('leads.lead_status','!=','Mobile Generated')
               ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
               ->orderBy($column,$direction)
               ->get();
               // ->groupBy('leads.id')
               // ->paginate(10);
      }
      else
      {
         $get_all_leads = DB::table('leads')
               ->join('customer_details','leads.customer_id','=','customer_details.cust_id')
               ->join('user','leads.lead_owner','=','user.id')
               ->select('leads.*','customer_details.*','user.username as lead_owner','leads.comment as lead_comment')
               ->where($whereCondition)
               ->when($request->get('filter_lead_source'),function($query)use($request){
                  $query->whereIn('leads.lead_source',$request->get('filter_lead_source'));
               })
               ->where('leads.lead_status','!=','Mobile Generated')
               //->whereBetween('leads.creation_date',[$get_min_date,$get_max_date])
               ->when($dateArr,function($query,$dateArr){
                  // $query->whereBetween('leads.creation_date',$dateArr);
                  $query->wherebetween(DB::raw("(STR_TO_DATE(leads.converted_at,'%Y-%m-%d'))"),$dateArr);
               })
               ->orderBy($column,$direction)
               ->get();
               // ->paginate(10);
      }
      if($request->get('btn_submit') != 'Export')
      {
         $get_all_leads = $get_all_leads->paginate(10);
      }
      else
      {
         $get_all_leads  = $get_all_leads->paginate(10000);
      }
      // $json_decode_all_leads = json_decode(json_encode($get_all_leads->toArray()),true);
      // foreach($json_decode_all_leads['data'] as $key=>$lead)
		// {
      //    $prod = array();
      //    foreach(json_decode($lead['equipment_requirement']) as $ke=>$val)
      //    {
      //       $get_product_name = DB::table('products')->select('product_name')->where('id',json_decode($val))->get()->toArray();
      //        $products_names = json_decode(json_encode($get_product_name),true);
      //       if($ke == 0)
      //       {
      //          $first_product_name = $products_names[0]['product_name'];
      //          $json_decode_all_leads['data'][$key]['first_product_name']=$first_product_name;
      //       }    
      //       $prod[$ke]['product_name'] = $products_names[0]['product_name'];
      //    } 
      //    $json_decode_all_leads['data'][$key]['product_name'] = $get_product_name = implode(",",array_column($prod,'product_name'));;
      //    $json_decode_all_leads['data'][$key]['product_name_arr'] = $prod;
      //    // dd($json_decode_all_leads['data'][$key]['product_name_arr']);
		// }

      $json_decode_all_leads = json_decode(json_encode($get_all_leads->toArray()), true);

      foreach ($json_decode_all_leads['data'] as $key => $lead) {
         $prod = [];
         $equipment_requirements = json_decode($lead['equipment_requirement']);

         // Set a default value to avoid undefined index error
         $json_decode_all_leads['data'][$key]['first_product_name'] = "No Product";

         // Validate equipment requirement
         if (is_array($equipment_requirements) && !empty($equipment_requirements)) {
            foreach ($equipment_requirements as $ke => $val) {
                  $get_product_name = DB::table('products')
                     ->select('product_name')
                     ->where('id', json_decode($val))
                     ->get()
                     ->toArray();

                  // Check if product name exists
                  if (!empty($get_product_name)) {
                     $products_names = json_decode(json_encode($get_product_name), true);
                     $product_name = $products_names[0]['product_name'];

                     // Assign first product name
                     if ($ke == 0) {
                        $json_decode_all_leads['data'][$key]['first_product_name'] = $product_name;
                     }

                     // Store product names
                     $prod[$ke]['product_name'] = $product_name;
                  } else {
                     // Handle missing product gracefully
                     $prod[$ke]['product_name'] = "Unknown Product";
                  }
            }

            // Set product name array and string
            $json_decode_all_leads['data'][$key]['product_name'] = implode(",", array_column($prod, 'product_name'));
            $json_decode_all_leads['data'][$key]['product_name_arr'] = $prod;
         } else {
            // Handle empty equipment requirement
            $json_decode_all_leads['data'][$key]['product_name'] = "No Equipment";
            $json_decode_all_leads['data'][$key]['product_name_arr'] = [];
         }
      }
      // dd($json_decode_all_leads['data']);

		$filter_collapse_cookie = null;
      if(isset($_COOKIE['filter_collapse_js']) && $_COOKIE['filter_collapse_js'] =='Yes')
      {
         $filter_collapse_cookie = 1;
      }
      $filter_arr = ["cust_name"=>$customer_name,
                     "cust_no"=>$customer_contact,
                     "patient_name"=>$patient_name,
                     "city"=>$city,
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

      if($request->get('btn_submit') == 'Export')
      {
         ob_end_clean();
         ob_start();
         return Excel::download(new AllLeadsReportExport($get_all_leads,$json_decode_all_leads), 'Leads.xlsx');
      }
      else
      {
         // dd($json_decode_all_leads,$get_lead_sources,$get_customer_location,$get_lead_owners,$filter_arr,$filter_collapse_cookie,$cities,$lead_cancellation_reason,$get_all_leads);
         // die;
         return view('leads/view_all_leads',compact('json_decode_all_leads','get_lead_sources','get_customer_location','get_lead_owners','filter_arr','filter_collapse_cookie','cities','lead_cancellation_reason','get_all_leads'));
      }
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
      $lead_details = DB::select("SELECT leads.*,customer_details.*,leads.comment FROM leads,customer_details WHERE leads.id=$id AND customer_details.cust_id=$customer_id");
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
      $patient_id = DB::table('lookup_table')->get('patient_id');
      $data['patient_id'] = $patient_id[0]->patient_id;
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
      $lead_details = DB::select("SELECT leads.*,customer_details.*,leads.comment FROM leads,customer_details WHERE leads.id=$id AND customer_details.cust_id=$customer_id");
      $data['lead_details'] = json_decode(json_encode($lead_details), true);
      $data['lead_source'] = config('app.lead_source');

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
         $corp_masters = DB::select("SELECT * FROM corp_master WHERE flag = 'Active'");
         $data['corp_masters'] = json_decode(json_encode($corp_masters), true);
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
         $data['patient_documents'] = config('app.patient_documents');
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
            $equipments = request()->get('equipment_id');
            $equipments_requirements = json_encode($equipments);

            $contact_person_1_name = null;
            $contact_person_1_no = null;
            $contact_person_2_name = null;
            $contact_person_2_no = null;
            
            if(request()->get('customer_type') == "Corporate")
            {
               // $corporate_cust_id = request()->get('corporate_cust_id');
               // dd(request()->get('contact_person_1_name'));
               $contact_person_1_name = request()->get('contact_person_1_name');
               $contact_person_1_no = request()->get('contact_person_1_no');
               $contact_person_2_name = request()->get('contact_person_2_name');
               $contact_person_2_no = request()->get('contact_person_2_no');
            }
            $cutomer_details_updateData = [
               'customer_name' => request()->get('cust_name'),
               'address_line_1' => request()->get('address_line_1'),
               'address_line_2' => request()->get('address_line_2'),
               'cust_gender'=>request()->get('customer_gender'),
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
               // 'refered_by' => request()->get('refered_by'),
               'customer_type' => request()->get('customer_type'),
               'contact_person_1_name' => $contact_person_1_name,
               'contact_person_1_no' => $contact_person_1_no,
               'contact_person_2_name' => $contact_person_2_name,
               'contact_person_2_no' => $contact_person_2_no,
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
               'patient_gender' => request()->get('patient_gender'),
               'patient_gender' => request()->get('patient_gender'),
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
            // dd(request()->get('hidden_def_patient_id'));
            if(request()->get('hidden_def_patient_id') != "No" && request()->get('hidden_def_patient_id') != null){
               DB::table('lookup_table')->update(['patient_id'=>request()->get('hidden_def_patient_id')+1]);
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
            if(request()->get('handovermode') == "on")
            {
               $handovermode = 'pickup';
            }
            else
            {
               $handovermode = 'delivery';
            }
            if($same_address == True){
               $is_same = "Yes";
            }
            else
            {
               $is_same = "No";
            }

            $fileName = $lead_id.'.jpeg';
            $filePath = null;
            if(request()->file('doc_id_file') != null)
            {
               $filePath = request()->file('doc_id_file')->storeAs('public/patient_document',$fileName);
            }
            else if(request()->get('doc_id_file_hidden') != null){
               $filePath = request()->get('doc_id_file_hidden');
            }
               // $sale_rental = array();
               // for($i=1; $i<=count($equipment); $i++)
               // {
               //    $sale_rental_temp = request()->get('sale_rental'.$i);
               //    array_push($sale_rental,$sale_rental_temp);
               // }
               // $sale_rental = json_encode($sale_rental);
               $contact_person_1_name = null;
               $contact_person_1_no = null;
               $contact_person_2_name = null;
               $contact_person_2_no = null;
               $corp_master = null;
               
               if(request()->get('customer_type') == "Corporate")
               {
                  // $corporate_cust_id = request()->get('corporate_cust_id');
                  $contact_person_1_name = request()->get('contact_person_1_name');
                  $contact_person_1_no = request()->get('contact_person_1_no');
                  $contact_person_2_name = request()->get('contact_person_2_name');
                  $contact_person_2_no = request()->get('contact_person_2_no');
                  if(request()->get('corp_master') == 'Other')
                  {
                     $corp_master = null;
                  }
                  else
                  {
                     $corp_master = request()->get('corp_master');
                  }
               }
            $cutomer_details_updateData = 
            [
               'customer_name' => request()->get('cust_name'),
               'address_line_1' => request()->get('address_line_1'),
               'address_line_2' => request()->get('address_line_2'),
               'cust_gender' => request()->get('customer_gender'),
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
               'corp_master_id' => $corp_master,
               'prmt_secondary_contact_no' => request()->get('prmt_secondary_contact_no'),
               'addr_is_same' => $is_same,
               'gst_no' => $gst_no,
               // 'refered_by' => request()->get('refered_by'),
               'customer_type' => request()->get('customer_type'),
               'contact_person_1_name' => $contact_person_1_name,
               'contact_person_1_no' => $contact_person_1_no,
               'contact_person_2_name' => $contact_person_2_name,
               'contact_person_2_no' => $contact_person_2_no,
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
                     'patient_gender',
                     'doctor_name',
                     'hospital_name',
                     'therapeutic_requirement',
                     'equipment_requirement',
                     'equipment_qty',
                     'months',
                     'billing_period',
                     'billing_unit',
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
                     'referredby',
                     'payment_mode',
                     'handovermode',
                     'patient_document_type',
                     'patient_document_no',
                     'patient_document_image',
                     'updated_by')
                  ->where('id',$lead_id)
                  ->get();
               $leads_updateData = 
               [
                  'converted_at'=>$converted_date,
                  // 'creation_date'=>$converted_date,
                  'patient_name' => request()->get('patient_name'),
                  'patient_age' => request()->get('patient_age'),
                  'patient_gender' => request()->get('patient_gender'),
                  'doctor_name' => request()->get('doctor_name'),
                  'hospital_name' => request()->get('hospital_name'),
                  'therapeutic_requirement' => request()->get('therapeutic_requirement'),
                  'equipment_requirement' => $equipments_requirements,
                  'equipment_qty' => $qty,
                  'months'=>json_encode(request()->get('billingPeriod')),
                  'billing_period'=>json_encode(request()->get('billingPeriod')),
                  'billing_unit'=>json_encode(request()->get('billingUnit')),
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
                  'referredby'=> request()->get('refered_by'),
                  'payment_mode' => request()->get('payment_mode'),
                  'handovermode'=> $handovermode,
                  'patient_document_type'=>request()->get('patient_document'),
                  'patient_document_no'=>request()->get('patient_doc_id_no'),
                  'patient_document_image'=>$filePath,
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

            leads_log::updateOrCreate(
               [
                  'log_lead_id' => $lead_id,
                  'log_lead_status' => 'Converted',
                  'updated_by' => session('username')
               ],
               [
                  'log_order_lead_date' => $converted_date,
                  'log_date' => date('Y-m-d'),
                  'log_time' => date('H:i:s'),
               ]);
            // $leads_log_data = 
            // [
            //    'log_lead_id' => $lead_id,
            //    'log_lead_status' => 'Converted',
            //    'log_date' => date('Y-m-d'),
            //    'log_time' => date('H:i:s'),
            //    'updated_by' => session('username')
            // ];
            // $leads_log->insert($leads_log_data);
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
               $address = $_POST['address_line_1'].", ".$_POST['address_line_2'].", ".$_POST['landmark'].", ".$_POST['area'].", ".$_POST['city1'].", ".$_POST['pincode'];

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
            if(request()->get('hidden_def_patient_id') != "No" && request()->get('hidden_def_patient_id') != null){
               DB::table('lookup_table')->update(['patient_id'=>request()->get('hidden_def_patient_id')+1]);
            }

            //send lead msg to internal staff

            $customerName = request()->get('cust_name');
            $customerAddress = $address;
            $customerContactNO = request()->get('primary_contact_no');
            $patientName = request()->get('patient_name');
            $patientAge = request()->get('patient_age');
            $getProducts = array();
            foreach (request()->get('equipments') as $key=>$value){
                $temp_prod = DB::table('products')->where('id',$value)->get()->toArray();
               array_push($getProducts,$temp_prod[0]->product_name);
            }
            $productType = request()->get('sale_rental');
            $productSaleRent = request()->get('offered_rent_total');
            $productDeposit = request()->get('offered_deposite_total');
            $productQty = request()->get('qty');
            $productTransport = request()->get('transport');
            $paymentMode = request()->get('payment_mode');
            $totalAmt = array_sum($productSaleRent)+array_sum($productDeposit)+array_sum($productTransport);
            
            $msg ="Product Name : ";
            foreach ($getProducts as $key => $value) {
               $prdMsg ="*".$value."*, ".($productType[$key]=='Rental'?'Rent : ':'Sale : ')
                              .$productSaleRent[$key]
                              ." Deposit :"
                              .($productType[$key]=='Rental'?$productDeposit[$key]:0)
                              ." Qty :"
                              .$productQty[$key]
                              .", Transport : "
                              .$productTransport[$key]." | ";
               $msg .=" ".$prdMsg;
            }
            //$prodManagerNo = config('app.prod_manager_no');
            $business_head_id = config('app.business_head_id');
			   $business_head_number = DB::table('user')->select('contact_no')->where('id',$business_head_id)->get()->first();
			   $business_head_number = $business_head_number->contact_no;
            $leadOwnerWpno = DB::table('user')->where('id',request()->get('lead_owner'))->first();
            $wpNumbers = array($business_head_number,$leadOwnerWpno->contact_no);
            // $wpNumbers = array($leadOwnerWpno->contact_no);            
            
            $delivery_date = date('d-M-y',strtotime($converted_date));
            //dd($wpNumbers,$getProducts,$msg,$customerAddress);
            if($patientName == null)
            {
               $patientName = "NA";
            }
            if($patientAge == null)
            {
               $patientAge = "NA";
            }
            $headerText = "";
            if(request()->get('flag') == "Edit")
            {
               $headerText = "(Edited): ".session('username').', Lead Owner: '.$leadOwnerWpno->username.', Lead Id: '.$lead_id;
            }
            else
            {
               $headerText = 'Lead Owner: '.$leadOwnerWpno->username.', Lead Id: '.$lead_id;
            }
            foreach ($wpNumbers as $key => $value) {
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
                  "portno"=>"11140",
                  "namespace"=>"b9a23cb4_89ed_4fe2_b849_20775908ff5e",
                  "countrycode"=> "91",
                  "mobileno"=> $value,
                  "templatename" => "new_lead",
                  "templateparams" => [
                        ["type"=> "text","text"=> $headerText],
                        ["type"=> "text","text"=> $customerName],
                        ["type"=> "text","text"=> "$customerContactNO"],
                        ["type"=> "text","text"=> $patientName],
                        ["type"=> "text","text"=> "$patientAge"],
                        ["type"=> "text","text"=> $msg],
                        ["type"=> "text","text"=> "$totalAmt"],
                        ["type"=> "text","text"=> $paymentMode],
                        ["type"=> "text","text"=>  $customerAddress],
                        ["type"=> "text","text"=> "$delivery_date"],
                      // ["type"=> "text","text"=> "<<Delivery>>],
                  ],
              ];
            //   dd(json_encode($data));
              curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
              
               $resp = curl_exec($curl);
               curl_close($curl);
               // dd($resp);
            }

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
      $patient_id = DB::table('lookup_table')->get('patient_id');
      $data['patient_id'] = $patient_id[0]->patient_id;
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
      $corp_masters = DB::select("SELECT * FROM corp_master WHERE flag = 'Active'");
      $data['corp_masters'] = json_decode(json_encode($corp_masters), true);
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
         $data['lead_source'] = config('app.lead_source');
         $data['patient_documents'] = config('app.patient_documents');
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
         $data['lead_source'] = config('app.lead_source');
         return view('leads/create_lead',$data);
      }
      else
      {
         return view('leads/check_customer'); 
      }
   }
   public function findCustomer(Request $request)
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
       return redirect()->to($url);
      }
      $primary_contact_no = $request->get('primary_contact_no');
      $customer_details = DB::select("SELECT * FROM customer_details WHERE primary_contact_no = '$primary_contact_no' order by cust_id DESC");
      $data['customer_details'] = \json_decode(\json_encode($customer_details), true);
      // print_r(null['cust_id']);
      //$customer_details = array();
      //array_push($customer_details['cust_id'],$data['customer_details'][0]['cust_id']);
      //print_r($customer_details);
      if(isset($data['customer_details'][0]['cust_id'])){
         $json= array('customer_id' => $data['customer_details'][0]['cust_id'],'contact_person_1_name' => $data['customer_details'][0]['contact_person_1_name'],'contact_person_1_no' => $data['customer_details'][0]['contact_person_1_no'],'contact_person_2_name' => $data['customer_details'][0]['contact_person_2_name'],'contact_person_2_no' => $data['customer_details'][0]['contact_person_2_no'], 'customer_name' => $data['customer_details'][0]['customer_name'] , 'location' => $data['customer_details'][0]['location'] , 'address_line_1' => $data['customer_details'][0]['address_line_1'] , 'address_line_2' => $data['customer_details'][0]['address_line_2'] , 'area' => $data['customer_details'][0]['area'] , 'landmark' => $data['customer_details'][0]['landmark'] , 'city' => $data['customer_details'][0]['city'] , 'pincode' => $data['customer_details'][0]['pincode'] , 'state' => $data['customer_details'][0]['state'] , 'country' => $data['customer_details'][0]['country'] , 'secondary_contact_no' => $data['customer_details'][0]['secondary_contact_no'] , 'email_id' => $data['customer_details'][0]['email_id'] , 'refered_by' => $data['customer_details'][0]['refered_by'], 'cust_gender'=>$data['customer_details'][0]['cust_gender'], 'cust_type'=>$data['customer_details'][0]['customer_type'],'cust_migrate'=>false);	
         $jsonstring = json_encode($json);
         echo $jsonstring;
      }
      else
      {
         // DB::enableQueryLog();
         // echo "ahshg".$primary_contact_no;
         $cust_details = DB::table('mis_records')
                              ->select(
                                 'patient_name as customer_name',
                                 'location as location',
                                 'address as address',
                                 'lead_source as refered_by',
                                 'city as city')
                              ->where('contact_no',$primary_contact_no)
                              ->get()
                              ->toArray();
         
         // print_r($cust_details);
         if(isset($cust_details[0])){
            $address = $cust_details[0]->address;
            $address =  explode(',',$address);
            $address_line_1 = null;
            $address_line_2 = null;
            $landmark = null;
            $area = null;
            $pincode = null;
            // print_r($address);
            // $area_keywords = ['east','west','East','West','(e)','(w)'];
            foreach($address as $key=>$addr)
            {
               if($key == 0)
               {
                  $address_line_1 .= ' '.$addr;
               }
               if($key == 1)
               {
                  $address_line_2 .= ' '.$addr;
               }
               if((strpos($addr,'near')!== false)||(strpos($addr,'opposite')!== false)||(strpos($addr,'opp')!== false)||(strpos($addr,'behind')!== false)||(strpos($addr,'before')!== false))
               {
                  $landmark .=' '.$addr;
               }
               // if((strpos($addr,'east')!== false)||(strpos($addr,'East')!= false)||(strpos($addr,'west')!= false)||(strpos($addr,'West')!= false)||(strpos($addr,'(e)')!= false)||(strpos($addr,'(w)')!= false)(strpos($addr,'(e)')!= false)||(strpos($addr,'(w)')!= false))
               // {
               //    $area .= $addr;
               // }
               if(is_numeric($addr) && (strlen($addr) == 6))
               {
                  $pincode .= $addr;
               }
            }
            $json= array(
               'customer_id' => null,
               'contact_person_1_name' => null,
               'contact_person_1_no' => null,
               'contact_person_2_name' => null,
               'contact_person_2_no' => null,
               'customer_name' => $cust_details[0]->customer_name,
               'location' => $cust_details[0]->location,
               'address_line_1' => $address_line_1,
               'address_line_2' => $address_line_2,
               'area' => $cust_details[0]->location,
               'landmark' => $landmark,
               'city' => $cust_details[0]->city,
               'pincode' => $pincode,
               'state' => 'Maharashtra',
               'country' => 'India',
               'secondary_contact_no' => null,
               'email_id' => null,
               'refered_by' => $cust_details[0]->refered_by,
               'cust_gender'=>null,
               'cust_type'=>null,
               'cust_migrate'=>true
            );	
         }
         else{

            $json= array(
               'customer_id' => null,
               'contact_person_1_name' => null,
               'contact_person_1_no' => null,
               'contact_person_2_name' => null,
               'contact_person_2_no' => null,
               'customer_name' => null,
               'location' => null,
               'address_line_1' => null,
               'address_line_2' => null,
               'area' => null,
               'landmark' => null,
               'city' => null,
               'pincode' => null,
               'state' => null,
               'country' => null,
               'secondary_contact_no' => null,
               'email_id' => null,
               'refered_by' => null,
               'cust_gender'=>null,
               'cust_type'=>null,
               'cust_migrate'=>false
            );	
         }
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
      if(config('app.app_env') == 'devweb')
      {
            $mobile = config('app.developer_contact');
      }
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
            'contact_no' => config('app.developer_contact'), 
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
         $lead_source = config('app.lead_source');
         return view('leads.send_link_lead',compact('get_products','get_all_links','products_name_arr','lead_source'));
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
                  'terms_and_condition'=>'accepted',
                  'customer_gender'=>'required'
               ],[
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
                  'customer_gender.required'=>'customer gender required'
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
            if(!empty($request->get('customer_gender')))
            {
               $update_arr['cust_gender']=$request->get('customer_gender');
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
               'patient_gender'=>$request->get('patient_gender'),
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
         $orderTypeNotIn = config('app.order_type');
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
                                                ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
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
      $web_order_id = $request->get('web_order_id');
      $status = $request->get('status');
      $reason = $request->get('reason');
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
      if($request->get('web_order_id')!=null && $request->get('web_order_id') != "")
      {
         // return "gg";
         $postdelivery = false;
         if(DB::table('del_orders')->where('web_order_id',$web_order_id)->where('status','Cancel')->exists())
         {
            $postdelivery = true;
         }
         if($status == 'Closed')
         {
            $url = "https://lj7724mli5.execute-api.ap-south-1.amazonaws.com/prod/q5careordercancellation";
            $data =[
               "orderid"=>$web_order_id,
               "cancelreason"=>$reason,
               "cancelremarks"=>$comment,
               "postdelivery"=>$postdelivery
            ];
         }
         else if($status == 'Work In Process')
         {
            // $lead_data = DB::table('leads')->where('id',$request->get('lead_id'))->get()->toArray();
            // DB::table('deleted_records')->insert(['type'=>'lead','record'=>json_encode($lead_data)]);
            $leadowner = strtolower(DB::table('leads')->join('user','user.id','=','leads.lead_owner')->select('user.username')->where('leads.id',$request->get('lead_id'))->first()->username);
            $url = "https://lj7724mli5.execute-api.ap-south-1.amazonaws.com/prod/q5careorderupdate";
            $data =[
               "orderid"=>$web_order_id,
               "orderstatus"=>'Hot',
               "createdby"=>$leadowner,
            ];
         }

         // return json_encode($data);
      //    return json_encode([
      //       "orderid"=>$web_order_id,
      //       "cancelreason"=>$reason,
      //       "cancelremarks"=>$comment,
      //       "postdelivery"=>$postdelivery
      //   ]);

         // return json_encode($data =[
         //    "orderstatus"=>'Hot',
         //    "createdby"=>$leadowner,
         // ]);
         $curl = curl_init();
         curl_setopt($curl, CURLOPT_URL, $url);
         curl_setopt($curl, CURLOPT_POST, true);
         curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
         
         $headers = array(
            "Accept: application/json",
            "Content-Type: application/json",
         );
         curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
         curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        
        $resp = curl_exec($curl);
      //   dd($resp);
      curl_close($curl);
      return $resp;
      }
      else
      {
         return "Success";
      }
      // $insert = lead::where('id',$lead_id)->update([''=>'$status','comment'=>'comment']);
      // echo"UPDATE leads SET lead_status = '$status', comment = CONCAT('$comment',comment) WHERE id = '$lead_id'";
      // return "Success";
   }
   public function fetchProductDetailsLead(Request $request)
   {
      $product_details = DB::table('products')->where('id',$request->get('id'))->get()->toArray();
      $product_details['related_products'] = DB::table('products')->whereIn('id',explode(',',$product_details[0]->related_products))->get()->toArray();
      return $product_details;
   }

   public function leadReportDetails(Request $request)
   {
      $date_report = array();
      $begin = new DateTime('2022-06-01');
      $end = new DateTime('2022-06-31');

      $interval = DateInterval::createFromDateString('1 day');
      $period = new DatePeriod($begin, $interval, $end);

      foreach ($period as $dt) 
      {
         $date = $dt->format("Y-m-d");
         // lead_details
         $lead_details = DB::table('leads')->select('*')->where('creation_date',$date)->whereIn('lead_status',["Converted","Order Generated","Vendor Assigned","Delivery In Progress"])->get()->toArray();
         // dd($date,$lead_details);
         $total_rent = 0;
         $total_deposite = 0;
         $total_sale = 0;
         $total_transport = 0;
         foreach($lead_details as $key=>$value)
         {
            $deposite = json_decode($value->deposite_total);
            $rent = json_decode($value->offered_rent_total);
            $transport = json_decode($value->transport);
            $sale_rental = json_decode($value->sale_rental);                  
            foreach($rent as $key=>$value)
            {
               if($sale_rental[$key] == 'Rental')
               {
                  $total_deposite = $total_deposite + $deposite[$key];
                  $total_rent = $total_rent + $rent[$key];
                  $total_transport = $total_transport + $transport[$key];
               }
               else if($sale_rental[$key] == 'Sale')
               {
                  $total_sale = $total_sale + $rent[$key];
                  $total_transport = $total_transport + $transport[$key];
               }
            }
         }
         $temp['date'] = $date;
         $temp['deposite'] = $total_deposite;
         $temp['rent'] = $total_rent;
         $temp['sale'] = $total_sale;
         $temp['transport'] = $total_transport;
         $temp['type'] = 'Lead';
         array_push($date_report,$temp);
         $orderTypeNotIn = config('app.order_type');
         $order_details = DB::table('order_details')
                                 ->join('del_orders','del_orders.order_id','=','order_details.order_id')
                                 ->join('leads','leads.id','=','del_orders.lead_id')
                                 ->select('order_details.*')->where('leads.creation_date',$date)
                                 ->whereNotIn('order_details.current_status',['Cancel','Closed'])
                                 ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)->get()->toArray();
         $total_rent = 0;
         $total_deposite = 0;
         $total_sale = 0;
         $total_transport = 0;
         foreach($order_details as $key=>$value)
         {
            $order_details[$key]->product_rent = RenewalPickupController::fetchCrDrData($value->id,'R');
            $order_details[$key]->product_deposite = RenewalPickupController::fetchCrDrData($value->id,'D');
            $order_details[$key]->transport = RenewalPickupController::fetchCrDrData($value->id,'T');
            if($value->sale_rental == 'Rental')
            {
               if(!in_array($value->current_status,['Cancel','Closed'])){
                  $total_rent = $total_rent + $value->product_rent;
                  $total_deposite = $total_deposite + $value->product_deposite;
                  $total_transport = $total_transport + $value->transport;
               }else{
                  $total_transport = $total_transport + $value->transport;
               }
            }
            else if($value->sale_rental == 'Sale')
            {
               if(!in_array($value->current_status,['Cancel','Closed'])){
                  $total_sale = $total_sale + $value->product_rent;
                  $total_transport = $total_transport + $value->transport;
               }else{
                  $total_transport = $total_transport + $value->transport;
               }
               
            }
            
         }
         $temp['date'] = $date;
         $temp['deposite'] = $total_deposite;
         $temp['rent'] = $total_rent;
         $temp['sale'] = $total_sale;
         $temp['transport'] = $total_transport;
         $temp['type'] = 'Order';
         array_push($date_report,$temp);
      }
      return view('OrderManagement.daily-report-view',compact('date_report'));
   }

   // public function new_site_daily_orders(){
   //    $orderTypeNotIn = config('app.order_type');
   //    $details = DB::table('leads')
   //                   ->join('customer_details','customer_details.cust_id','=','leads.customer_id')
   //                   ->join('del_orders','leads.id','=','del_orders.lead_id')
   //                   ->join('order_details','order_details.order_id','=','del_orders.order_id')
   //                   ->join('products','order_details.product_id','=','products.id')
   //                   ->select(
   //                      'products.product_name',
   //                      'order_details.sale_rental',
   //                      'del_orders.DelDate',
   //                      'order_details.product_rent',
   //                      'order_details.product_deposite',
   //                      'products.id',
   //                      'customer_details.citygroup'
   //                   )
   //                   ->where('leads.lead_source','New User Site')
   //                   ->where('order_details.creation_date',date('Y-m-d',strtotime("-1 days")))
   //                   ->whereNotIn('order_details.current_status',['Cancel'])
   //                   ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
   //                   ->get();
   //                   $details = $details->groupBy('sale_rental')->toArray();         
   //                // $details = $details->toArray();
   //                $details_val_rent = array();
   //                $details_val_sale = array();
   //                $temp_ids_rent = array();
   //                $temp_ids_sale = array();
   //                foreach($details as $key=>$value){
            
   //                   foreach($value as $ke=>$val){
   //                      // dd($ke);
   //                      if($key == 'Rental'){
   //                         if(in_array($val->id,$temp_ids_rent)){
   //                            $index = array_search($val->id,$temp_ids_rent);
   //                            $details_val_rent[$index]['qty'] = $details_val_rent[$index]['qty'] + 1;
   //                            $details_val_rent[$index]['product_rent'] = $details_val_rent[$index]['product_rent'] + $val->product_rent;
   //                            $details_val_rent[$index]['product_deposite'] = $details_val_rent[$index]['product_deposite'] + $val->product_deposite;
   //                            array_push($details_val_rent[$index]['cities'],$val->city);
   //                         }
   //                         else{
   //                            array_push($temp_ids_rent,$val->id);
   //                            $index = count($details_val_rent);
   //                            $details_val_rent[$index]['qty'] = 1;
   //                            $details_val_rent[$index]['product_name'] = $val->product_name;
   //                            $details_val_rent[$index]['product_rent'] = $val->product_rent;
   //                            $details_val_rent[$index]['product_deposite'] = $val->product_deposite;
   //                            $details_val_rent[$index]['sale_rental'] = $val->sale_rental;
   //                            $details_val_rent[$index]['DelDate'] = $val->DelDate;
   //                            $details_val_rent[$index]['cities'][0] = $val->city;
   //                         }
   //                      }
   //                      else{
   //                         if(in_array($val->id,$temp_ids_sale)){
   //                            $index = array_search($val->id,$temp_ids_sale);
   //                            $details_val_sale[$index]['qty'] = $details_val_sale[$index]['qty'] + 1;
   //                            $details_val_sale[$index]['product_rent'] = $details_val_sale[$index]['product_rent'] + $val->product_rent;
   //                            $details_val_sale[$index]['product_deposite'] = $details_val_sale[$index]['product_deposite'] + $val->product_deposite;
   //                            array_push($details_val_sale[$index]['cities'],$val->city);
   //                         }
   //                         else{
   //                            array_push($temp_ids_sale,$val->id);
   //                            $index = count($details_val_sale);
   //                            $details_val_sale[$index]['qty'] = 1;
   //                            $details_val_sale[$index]['product_name'] = $val->product_name;
   //                            $details_val_sale[$index]['product_rent'] = $val->product_rent;
   //                            $details_val_sale[$index]['product_deposite'] = $val->product_deposite;
   //                            $details_val_sale[$index]['sale_rental'] = $val->sale_rental;
   //                            $details_val_sale[$index]['DelDate'] = $val->DelDate;
   //                            $details_val_sale[$index]['cities'][0] = $val->city;
   //                         }
   //                      }
   //                   }
   //                }
   //                $details = array_merge($details_val_rent,$details_val_sale);
   //    // dd($details);
   //    Excel::store(new DailyNewSiteOrders($details),'Daily New Site Orders'.date('Y-m-d').'.xlsx','public',\Maatwebsite\Excel\Excel::XLSX);
   //    $xlsx_file = file_get_contents('storage/app/public/Daily New Site Orders'.date('Y-m-d').'.xlsx');
   //    $data = ['data'=>'data'];
   //    Mail::send('PendingOrders/new_site_data', $data, function($message)use($xlsx_file) 
   //    {
   //        $email_id = config('app.cto_email');
   //        // $email_id = 'rahulbhanushali@quali55care.com';
   //        $message->to($email_id, 'CEO')->subject('Daily New Site Data Report');
   //        $message->to('abhishekn@quali55care.com', 'Abhishek Nate')->subject('Daily New Site Data Report');
   //        $message->from('tempmailquali@gmail.com', 'Quali55Care');

   //        $message->attachData($xlsx_file,'Daily New Site Orders'.date('Y-m-d').'.xlsx');
   //    });

   // }
   public function new_site_daily_orders(){
      $date = date('d-M-y',strtotime("-1 days"));
      $orderTypeNotIn = config('app.order_type');
      $cities = DB::table('leads')
                     ->join('customer_details','customer_details.cust_id','=','leads.customer_id')
                     ->join('del_orders','leads.id','=','del_orders.lead_id')
                     ->join('order_details','order_details.order_id','=','del_orders.order_id')
                     ->join('products','order_details.product_id','=','products.id')
                     ->select(
                        'customer_details.citygroup as city'
                     )
                     ->distinct('customer_details.citygroup')
                     ->whereIn('leads.lead_source',['New User Site','Web Chat','Web Popup','Web Order','Web - Call','Web - WhatsApp'])
                     ->where('order_details.creation_date',date('Y-m-d',strtotime("-1 days")))
                     ->whereNotIn('order_details.current_status',['Cancel'])
                     ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                     ->get();
      // dd($cities);
      $final_data = array();
      // foreach($cities as $key=>$value)
      // {
         $details = DB::table('leads')
                        ->join('customer_details','customer_details.cust_id','=','leads.customer_id')
                        ->join('del_orders','leads.id','=','del_orders.lead_id')
                        ->join('order_details','order_details.order_id','=','del_orders.order_id')
                        ->join('products','order_details.product_id','=','products.id')
                        ->select(
                           'products.product_name',
                           'order_details.sale_rental',
                           'del_orders.DelDate',
                           'order_details.product_rent',
                           'order_details.product_deposite',
                           'products.id',
                           'customer_details.citygroup as city'
                        )
                        // ->whereIn('leads.lead_source',['New User Site','Web Chat','Web Popup'])
                        ->where('order_details.creation_date',date('Y-m-d',strtotime("-1 days")))
                        // ->whereIn('customer_details.citygroup',[$value->city])
                        ->whereIn('leads.lead_source',['New User Site','Web Chat','Web Popup','Web Order','Web - Call','Web - WhatsApp'])
                        ->whereNotIn('order_details.current_status',['Cancel'])
                        ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                        ->get();
            $details = $details->groupBy('sale_rental')->toArray();         
            // dd($details);
         // $details = $details->toArray();
         $details_val_rent = array();
         $details_val_sale = array();
         $temp_ids_rent = array();
         $temp_ids_sale = array();
         foreach($details as $key=>$value)
         {
   
            foreach($value as $ke=>$val)
            {
               // dd($ke);
               if($key == 'Rental')
               {
                  
                  if(in_array($val->id,$temp_ids_rent))
                  {
                     $index = array_search($val->id,$temp_ids_rent);
                     $details_val_rent[$index]['qty'] = $details_val_rent[$index]['qty'] + 1;
                     $details_val_rent[$index]['product_rent'] = $details_val_rent[$index]['product_rent'] + $val->product_rent;
                     $details_val_rent[$index]['product_deposite'] = $details_val_rent[$index]['product_deposite'] + $val->product_deposite;
                  }
                  else
                  {
                     array_push($temp_ids_rent,$val->id);
                     $index = count($details_val_rent);
                     $details_val_rent[$index]['qty'] = 1;
                     $details_val_rent[$index]['product_name'] = $val->product_name;
                     $details_val_rent[$index]['product_rent'] = $val->product_rent;
                     $details_val_rent[$index]['product_deposite'] = $val->product_deposite;
                     $details_val_rent[$index]['sale_rental'] = $val->sale_rental;
                     $details_val_rent[$index]['DelDate'] = $val->DelDate;
                     $details_val_rent[$index]['cities'] = $val->city;
                  }
               }
               else
               {
                  
                  if(in_array($val->id,$temp_ids_sale))
                  {
                     $index = array_search($val->id,$temp_ids_sale);
                     $details_val_sale[$index]['qty'] = $details_val_sale[$index]['qty'] + 1;
                     $details_val_sale[$index]['product_rent'] = $details_val_sale[$index]['product_rent'] + $val->product_rent;
                     $details_val_sale[$index]['product_deposite'] = $details_val_sale[$index]['product_deposite'] + $val->product_deposite;
                     // array_push($details_val_sale[$index]['cities'],$val->city);
                  }
                  else
                  {
                     array_push($temp_ids_sale,$val->id);
                     $index = count($details_val_sale);
                     $details_val_sale[$index]['qty'] = 1;
                     $details_val_sale[$index]['product_name'] = $val->product_name;
                     $details_val_sale[$index]['product_rent'] = $val->product_rent;
                     $details_val_sale[$index]['product_deposite'] = $val->product_deposite;
                     $details_val_sale[$index]['sale_rental'] = $val->sale_rental;
                     $details_val_sale[$index]['DelDate'] = $val->DelDate;
                     $details_val_sale[$index]['cities'] = $val->city;
                  }
               }
            }
         }
         $details1 = array_merge($details_val_rent,$details_val_sale);
         $index = count($final_data);
         // $final_data[$index] = $details1;
      // }
      dd($details1);
      $pune_products = null;
      $mumbai_products = null;
      foreach($details1 as $key=>$citywise)
      {

         if($citywise['cities'] == "Pune" || $citywise['cities'] == "pune")
         {
            // $pune_products .= '*'.trim($citywise['product_name']).'* Qty:'.$citywise['qty'].' Type:'.$citywise['sale_rental'].' Rent:'.$citywise['product_rent'].' Deposit:'.$citywise['product_deposite'].' || ';
            $pune_products .= '*'.trim($citywise['product_name']).'* Qty:'.$citywise['qty'].' Type:'.$citywise['sale_rental'].' || ';
         }
         else if($citywise['cities'] == "Thane" ||$citywise['cities'] == "Mumbai" || $citywise['cities'] == "mumbai")
         {
            // $mumbai_products .= '*'.trim($citywise['product_name']).'* Qty:'.$citywise['qty'].' Type:'.$citywise['sale_rental'].' Rent:'.$citywise['product_rent'].' Deposit:'.$citywise['product_deposite'].' || ';
            $mumbai_products .= '*'.trim($citywise['product_name']).'* Qty:'.$citywise['qty'].' Type:'.$citywise['sale_rental'].' || ';
         }
      }
      // dd($pune_products,$mumbai_products);
      $wpNumbers = config('app.new_site_wp_no');
      if($mumbai_products == null)
      {
         $mumbai_products = "*No Orders for Mumbai*";
      }
      if($pune_products == null)
      {
         $pune_products = "*No Orders for Pune*";
      }
      foreach ($wpNumbers as $key => $value) {
         if(config('app.app_env') == 'devweb')
         {
               $value = config('app.developer_contact');
         }
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
            "portno"=>"11140",
            "namespace"=>"b9a23cb4_89ed_4fe2_b849_20775908ff5e",
            "countrycode"=> "91",
            "mobileno"=> $value,
            "templatename" => "daily_product_rentsale_summary",
            "templateparams" => [
                  ["type"=> "text","text"=> $date],
                  ["type"=> "text","text"=> $mumbai_products],
                  ["type"=> "text","text"=> $pune_products],
            ],
        ];
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        
        $resp = curl_exec($curl);
      //   dd($resp);
        curl_close($curl);
        
      }
      // Excel::store(new DailyNewSiteOrders($details),'Daily New Site Orders'.date('Y-m-d').'.xlsx','public',\Maatwebsite\Excel\Excel::XLSX);
      // $xlsx_file = file_get_contents('storage/app/public/Daily New Site Orders'.date('Y-m-d').'.xlsx');
      // $data = ['data'=>'data'];
      // Mail::send('PendingOrders/new_site_data', $data, function($message)use($xlsx_file) 
      // {
      //     $email_id = config('app.ceo_email');
      //     // $email_id = 'rahulbhanushali@quali55care.com';
      //     $message->to($email_id, 'CEO')->subject('Daily New Site Data Report');
      //     $message->to('abhishekn@quali55care.com', 'Abhishek Nate')->subject('Daily New Site Data Report');
      //     $message->from('tempmailquali@gmail.com', 'Quali55Care');

      //     $message->attachData($xlsx_file,'Daily New Site Orders'.date('Y-m-d').'.xlsx');
      // });

   }

   public function new_site_daily_orders1(){
      $date = date('d-M-y',strtotime("-1 days"));
      $yesterday = date('Y-m-d',strtotime("-1 days"));
      $orderTypeNotIn = config('app.order_type');
      $cities = DB::table('leads')
                     ->join('customer_details','customer_details.cust_id','=','leads.customer_id')
                     ->join('del_orders','leads.id','=','del_orders.lead_id')
                     ->join('order_details','order_details.order_id','=','del_orders.order_id')
                     ->join('products','order_details.product_id','=','products.id')
                     ->select(
                        'customer_details.citygroup as city'
                     )
                     ->distinct('customer_details.citygroup')
                     ->whereIn('leads.lead_source',['New User Site','Web Chat','Web Popup','Web Order','Web - Call','Web - WhatsApp','Google Ads'])
                     ->where('order_details.creation_date',date('Y-m-d',strtotime("-1 days")))
                     // ->where(DB::raw("STR_TO_DATE(leads.converted_at,'%Y-%m-%d')"),DB::raw("STR_TO_DATE('$yesterday','%Y-%m-%d')"))
                     ->whereNotIn('order_details.current_status',['Cancel'])
                     ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                     ->get();
      $final_data = array();
      $details = DB::table('leads')
                     ->join('customer_details','customer_details.cust_id','=','leads.customer_id')
                     ->join('del_orders','leads.id','=','del_orders.lead_id')
                     ->join('order_details','order_details.order_id','=','del_orders.order_id')
                     ->join('products','order_details.product_id','=','products.id')
                     ->select(
                        'products.product_name',
                        'products.campaign',
                        'order_details.sale_rental',
                        'del_orders.DelDate',
                        'order_details.product_rent',
                        'order_details.product_deposite',
                        'products.id',
                        'customer_details.citygroup as city'
                     )
                     // ->whereIn('leads.lead_source',['New User Site','Web Chat','Web Popup'])
                     ->where('order_details.creation_date',date('Y-m-d',strtotime("-1 days")))
                     // ->where(DB::raw("STR_TO_DATE(leads.converted_at,'%Y-%m-%d')"),DB::raw("STR_TO_DATE('$yesterday','%Y-%m-%d')"))
                     // ->whereIn('customer_details.citygroup',[$value->city])
                     ->whereIn('leads.lead_source',['New User Site','Web Chat','Web Popup','Web Order','Web - Call','Web - WhatsApp','Google Ads'])
                     ->whereNotIn('order_details.current_status',['Cancel'])
                     ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                     ->get();
      $details = $details->groupBy('city');         
      // dd($details);
      $dumpdatacitywise = array();
      foreach($details as $key=>$value)
      {
         $typewise = $value->groupBy('sale_rental');
         // dd($typewise);
         $dumpdatacitywise[$key] = array();
         foreach($typewise as $k=>$record)
         {
            $temp_ids = array();
            // dd($record);
            foreach($record as $r=>$row)
            {
               if(in_array($row->id,$temp_ids))
               {
                  $index = array_search($row->id,$temp_ids);
                  $dumpdatacitywise[$key][$index]->qty = $dumpdatacitywise[$key][$index]->qty + 1;
                  $dumpdatacitywise[$key][$index]->product_rent = $dumpdatacitywise[$key][$index]->product_rent + $row->product_rent;
                  $dumpdatacitywise[$key][$index]->product_deposite = $dumpdatacitywise[$key][$index]->product_deposite + $row->product_deposite;
               }
               else
               {
                  array_push($temp_ids,$row->id);
                  $index = count($dumpdatacitywise[$key]);
                  $dumpdatacitywise[$key][$index] = $row;
                  $dumpdatacitywise[$key][$index]->qty = 1;
               }
            }
         }         
      }
      // dd($dumpdatacitywise);
      $pune_products = null;
      $mumbai_products = null;
      foreach($dumpdatacitywise as $key=>$data)
      {
         // dd($data);
         foreach($data as $k=>$d)
         {
            if($d->city == "Pune" || $d->city == "pune")
            {
               $city = 'Pune';
               if(DB::table('campaign_mapping')->select('campaign')->where('cat_code',$d->campaign.'P')->exists()){
                  $campaign = DB::table('campaign_mapping')->select('campaign')->where('cat_code',$d->campaign.'P')->first()->campaign;
               }
               else{
                  $campaign = 'Other';
               }
               // dd($campaign,$date);
               if(DB::table('google_campain_report')->select('conversions')->where('campaign',$campaign)->where('date',$yesterday)->exists())
               {
                  // dd('a');
                  $conversions = DB::table('google_campain_report')->select('conversions')->where('campaign',$campaign)->where('date',$yesterday)->first()->conversions + $d->qty;
                  $total_rate = DB::table('google_campain_report')->select('total_rate')->where('campaign',$campaign)->where('date',$yesterday)->first()->total_rate + $d->product_rent;
               }
               else
               {
                  $conversions = $d->qty;
                  $total_rate = $d->product_rent;
               }
               $pune_products .= '*'.trim($d->product_name).'* Qty:'.$d->qty.' Type:'.$d->sale_rental.' || ';
            }
            else if($d->city == "Thane" ||$d->city == "Mumbai" || $d->city == "mumbai")
            {
               $city = 'Mumbai';
               if(DB::table('campaign_mapping')->select('campaign')->where('cat_code',$d->campaign.'P')->exists()){
                  $campaign = DB::table('campaign_mapping')->select('campaign')->where('cat_code',$d->campaign)->first()->campaign;
               }
               else{
                  $campaign = 'Other';
               }
               if(DB::table('google_campain_report')->select('conversions')->where('campaign',$campaign)->where('date',$yesterday)->exists())
               {
                  $conversions = DB::table('google_campain_report')->select('conversions')->where('campaign',$campaign)->where('date',$yesterday)->first()->conversions + $d->qty;
                  $total_rate = DB::table('google_campain_report')->select('total_rate')->where('campaign',$campaign)->where('date',$yesterday)->first()->total_rate + $d->product_rent;
               }
               else
               {
                  $conversions = $d->qty;
                  $total_rate = $d->product_rent;
               }
               $mumbai_products .= '*'.trim($d->product_name).'* Qty:'.$d->qty.' Type:'.$d->sale_rental.' || ';;
            }
            GoogleCampaignReport::updateOrCreate([
               'campaign'=>$campaign,
               'date'=>date('Y-m-d',strtotime($date)),
            ],
            [
                  'total_rate'=>$total_rate,
                  'conversions'=>$conversions,
                  'city'=>$city,
                  'campaign_state'=>'Enabled',
                  'created_by'=>'Cronjob'
            ]);
         }
      }
      // dd($pune_products,$mumbai_products);
      $wpNumbers = config('app.new_site_wp_no');
      if($mumbai_products == null)
      {
         $mumbai_products = "*No Orders for Mumbai*";
      }
      if($pune_products == null)
      {
         $pune_products = "*No Orders for Pune*";
      }
      foreach ($wpNumbers as $key => $value) {
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
            "portno"=>"11140",
            "namespace"=>"b9a23cb4_89ed_4fe2_b849_20775908ff5e",
            "countrycode"=> "91",
            "mobileno"=> $value,
            "templatename" => "daily_product_rentsale_summary",
            "templateparams" => [
                  ["type"=> "text","text"=> $date],
                  ["type"=> "text","text"=> $mumbai_products],
                  ["type"=> "text","text"=> $pune_products],
            ],
        ];
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        
        $resp = curl_exec($curl);
      //   dd($resp);
        curl_close($curl);
        
      }
   }

   public function webProcessLeads()
   {
      $process_leads = DB::table('leads')
         ->join('customer_details','customer_details.cust_id','=','leads.customer_id')
         ->where('lead_status','Work In Process')
         ->whereIn('leads.lead_source',['New User Site','Web Chat','Web Popup'])
         ->where('leads.creation_date',date('Y-m-d',strtotime("-1 days")))
         ->get();
      $details_val_rent = array();
      $temp_ids_rent = array();
      foreach($process_leads as $key=>$lead)
      {
      foreach(json_decode($lead->equipment_requirement) as $key=>$equipment)
      {
      if(in_array($equipment,$temp_ids_rent))
      {
      $index = array_search($equipment,$temp_ids_rent);
      $details_val_rent[$index]['qty'] = $details_val_rent[$index]['qty'] + 1;
      }
      else
      {
      array_push($temp_ids_rent,$equipment);
      $index = count($details_val_rent);
      $details_val_rent[$index]['qty'] = 1;
      $details_val_rent[$index]['product_name'] = DB::table('products')->select('product_name')->where('id',$equipment)->first()->product_name;
      $details_val_rent[$index]['cities'] = $lead->city;
      }
      }
      }
      // dd($details_val_rent);
      $pune_products = null;
      $mumbai_products = null;
      foreach($details_val_rent as $key=>$citywise)
      {

      if($citywise['cities'] == "Pune" || $citywise['cities'] == "pune")
      {
      // $pune_products .= '*'.trim($citywise['product_name']).'* Qty:'.$citywise['qty'].' Type:'.$citywise['sale_rental'].' Rent:'.$citywise['product_rent'].' Deposit:'.$citywise['product_deposite'].' || ';
      $pune_products .= '*'.trim($citywise['product_name']).'* Qty:'.$citywise['qty'].' || ';
      }
      else if($citywise['cities'] == "Thane" ||$citywise['cities'] == "Mumbai" || $citywise['cities'] == "mumbai")
      {
      // $mumbai_products .= '*'.trim($citywise['product_name']).'* Qty:'.$citywise['qty'].' Type:'.$citywise['sale_rental'].' Rent:'.$citywise['product_rent'].' Deposit:'.$citywise['product_deposite'].' || ';
      $mumbai_products .= '*'.trim($citywise['product_name']).'* Qty:'.$citywise['qty'].' || ';
      }
      }
      // dd($pune_products,$mumbai_products);
      $wpNumbers = config('app.new_site_wp_no');
      if($mumbai_products == null)
      {
      $mumbai_products = "*No Orders for Mumbai*";
      }
      if($pune_products == null)
      {
      $pune_products = "*No Orders for Pune*";
      }
      $date = $date." Follow up Leads";
      foreach ($wpNumbers as $key => $value) {
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
      "portno"=>"11140",
      "namespace"=>"b9a23cb4_89ed_4fe2_b849_20775908ff5e",
      "countrycode"=> "91",
      "mobileno"=> $value,
      "templatename" => "daily_product_rentsale_summary",
      "templateparams" => [
      ["type"=> "text","text"=> $date],
      ["type"=> "text","text"=> $mumbai_products],
      ["type"=> "text","text"=> $pune_products],
      ],
      ];
      curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

      $resp = curl_exec($curl);
      //   dd($resp);
      curl_close($curl);

      }
   }

   public function processLeads()
   {
      $dayBefore = Carbon::now()->subDay("2")->toDateString();
      $inprocessLeads = DB::table('leads')
                           ->where('lead_status','Work In Process')
                           ->where([['creation_date','<=',$dayBefore]])
                           ->orderBy('creation_date','DESC')
                           ->get();
      $excelData = Collect();
      foreach ($inprocessLeads as $key => $lead) {
         $getLeadData = $this->getLeadData($lead->id);
         $excelData->push($getLeadData);
      }
      //dd($excelData);
      //dd($excelData[0]->product_name->count());
      if(count($inprocessLeads) !=0)
      {
         $this->sendMail([config('app.cron_job_mail')],'abhishekn@quali55care.com','Attention : '.count($inprocessLeads).' leads are work in progress for more than 48 hrs.', ['link'=>null,'message1'=>'View All Leads View'],$excelData,'inprocess_lead'); 
      }

      $inprocessLeadsByOwner = $inprocessLeads->groupBy('lead_owner');

      // dd($inprocessLeadsByOwner);
      foreach($inprocessLeadsByOwner as $key=>$value)
      {
         $leadOwnExcelData = Collect();
         $owner_email = DB::table('user')->where('id',$key)->first('email_id_user')->email_id_user;
         foreach ($value as $leadKey => $leadData) {
            $getLeadData = $this->getLeadData($leadData->id);
            $leadOwnExcelData->push($getLeadData);
         }
         $this->sendMail([$owner_email],'abhishekn@quali55care.com','Attention : '.count($value).' leads are work in progress for more than 48 hrs.', ['link'=>null,'message1'=>'View All Leads View'],$leadOwnExcelData,'inprocess_lead'); 
      }
   }

   public function sendMail($recipients,$cc, $subject, $body,$excelData,$excelType)
   {
      // dd($body);
      // $body = ['link'=>null,'message'=>$body];
      Mail::send('Mail.reminder-mail',$body, function($message) use ($recipients,$subject,$cc,$excelData,$excelType)
      {     
         foreach($recipients as $key=>$mail)
         {
               $message->to($mail,$mail)->subject($subject);
         }
         // $message->cc($cc,$cc)->subject($subject);
         $message->from('tempmailquali@gmail.com', 'Quali55Care');
         if(!in_array($excelType,['overdue_individual_all','overdue_individual_leadowner','overdue_corporate_all','overdue_corporate_leadowner'])){
            $excelFile = Excel::raw(new ReminderOverdueMail($excelData,$excelType),\Maatwebsite\Excel\Excel::XLSX);
            $message->attachData($excelFile,'reminder.xls');
         }
         //$message->attachData($pdf_file,'Challan.pdf');
         //$message->attach(asset($challan_file), ['mime' => 'application/pdf']);
      });
   }
   public function getLeadData($leadId)
    {
        $getLeadData = DB::table('leads')
            ->join('customer_details','leads.customer_id','=','customer_details.cust_id')
            ->select('leads.*','customer_details.customer_name','customer_details.primary_contact_no')
            ->where('id',$leadId)->first();
        $productIds = json_decode($getLeadData->equipment_requirement);
        $productName = DB::table('products')->whereIn('id',$productIds)->get('product_name')->pluck('product_name');
        $getLeadData->product_name = $productName;
        return $getLeadData;
    }
    public function assignLeadUser(Request $request)
    {
       // dd($request->getMethod());
       if($request->getMethod() == 'GET')
       {
          // dd(config('app.web_lead_user'));
          $leads = DB::table('leads')->join('customer_details','customer_details.cust_id','=','leads.customer_id')->where('leads.lead_owner',config('app.web_lead_user'))->get();
          $lead_owners = DB::table('user')->where('role','user')->get();
          return view('leads.assign-lead-user',compact('leads','lead_owners'));
       }
       else if($request->getMethod() == 'POST')
       {
          // dd($request->all());
          DB::table('leads')->where('id',$request->get('lead_id'))->update(['lead_owner'=>$request->get('lead_owner')]);
         $customer_details = DB::table('leads')->join('customer_details','customer_details.cust_id','=','leads.customer_id')->select('customer_details.customer_name','customer_details.primary_contact_no')->where('leads.id',$request->get('lead_id'))->first();
          $orderid = DB::table('leads')->where('id',$request->get('lead_id'))->first()->web_order_id;
          $leadowner = strtolower(DB::table('user')->where('id',$request->get('lead_owner'))->first()->username);
         $user_contact = DB::table('user')->where('id',$request->get('lead_owner'))->first()->contact_no;
         $customer_name = $customer_details->customer_name;
         $contact_no = $customer_details->primary_contact_no;
         $assigned_by = session('username');
          // dd(json_encode($data =[
          //    "orderid"=>$orderid,
          //    "createdby"=>$leadowner,
          // ]));
          $url = "https://lj7724mli5.execute-api.ap-south-1.amazonaws.com/prod/q5careorderupdate";
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
             "orderid"=>$orderid,
             "createdby"=>$leadowner,
          ];
          curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
          
          $resp = curl_exec($curl);
         curl_close($curl);
         // if($resp)
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
               $user_contact = config('app.developer_contact');
         }
         $data =[
            "portno"=>"11140",
            "namespace"=>"b9a23cb4_89ed_4fe2_b849_20775908ff5e",
            "countrycode"=> "91",
            "mobileno"=> "$user_contact",
            "templatename" => "converted_leadowner_assign",
            "templateparams" => [
                  ["type"=> "text","text"=> $customer_name],
                  ["type"=> "text","text"=> "$contact_no"],
                  ["type"=> "text","text"=> $assigned_by],
            ],
        ];
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        
        $resp = curl_exec($curl);
          //dd($resp);
          curl_close($curl);
          return redirect()->back()->with('message','Assigned Successfully!');
       }
    }
   public function diapersAdvertisement()
   {
      // array of contacts....
      $contacts = [];
      // dd($contacts);
      foreach($contacts as $mobile)
      {
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
               $mobile = config('app.developer_contact');
         }
         $data =[
            "portno"=>"11140",
            "namespace"=>"b9a23cb4_89ed_4fe2_b849_20775908ff5e",
            "countrycode"=> "91",
            "mobileno"=> "$mobile",
            "templatename" => "diaper_promotion",
            // "templateparams" => [
            //       ["type"=> "text","text"=> "Abhishek"],
            //       ["type"=> "text","text"=> "Abhishek"],
            //       ["type"=> "text","text"=> "Abhishek"],
            // ],
         ];
         curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
         
         $resp = curl_exec($curl);
         // dd($resp);
         curl_close($curl);
      }
   }

   public function userLeadSummary($date){
      $users = DB::table('user')->where('role','user')->where('flag','Active')->get();

      foreach($users as $user){
         if(DB::table('leads')->where('lead_owner',$user->id)->where(DB::raw("STR_TO_DATE(leads.converted_at,'%Y-%m-%d')"),$date)->whereIn('lead_status',['Converted','Order Generated'])->exists()){
            $leads = DB::table('leads')->where('lead_owner',$user->id)->where(DB::raw("STR_TO_DATE(leads.converted_at,'%Y-%m-%d')"),$date)->whereIn('lead_status',['Converted','Order Generated'])->get();

            $total_leads = $leads->count();
            $total_rental_count = 0;
            $total_rental_amount = 0;
            $total_sale_count = 0;
            $total_sale_amount = 0;
            
            foreach($leads as $key=>$lead){
               foreach(json_decode($lead->equipment_qty) as $k=>$qty){
                  if(json_decode($lead->sale_rental)[$k] == 'Rental'){
                     $total_rental_count = $total_rental_count + $qty;
                     $total_rental_amount = $total_rental_amount + json_decode($lead->offered_rent_total)[$k];
                  }else{
                     $total_sale_count = $total_sale_count + $qty;
                     $total_sale_amount = $total_sale_amount + json_decode($lead->offered_rent_total)[$k];
                  }
               }
            }
            $this->sendUserLeadSummaryWp($user->username,$user->contact_no,date('d-M-y',strtotime($date)),$total_leads,$total_rental_count,$total_rental_amount,$total_sale_count,$total_sale_amount,$total_rental_amount + $total_sale_amount);
         }else{
            $this->sendUserLeadSummaryWp($user->username,$user->contact_no,date('d-M-y',strtotime($date)),0,0,0,0,0,0);
         }
      }
   }

   public function sendUserLeadSummaryWp($username,$contact,$date,$total_leads,$total_rental_count,$total_rental_amount,$total_sale_count,$total_sale_amount,$total_amount){
      if(config('app.app_env') == 'devweb'){
         $contact = config('app.developer_contact');
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
         "mobileno"=> "$contact",
         "templatename" => "user_lead_daily_summary",
         "templateparams" => [
            ["type"=> "text","text"=> "*$username*"],
            ["type"=> "text","text"=> "*$date*"],
            ["type"=> "text","text"=> "$total_leads"],
            ["type"=> "text","text"=> "$total_rental_count"],
            ["type"=> "text","text"=> "$total_rental_amount"],
            ["type"=> "text","text"=> "$total_sale_count"],
            ["type"=> "text","text"=> "$total_sale_amount"],
            ["type"=> "text","text"=> "*$total_amount*"],
         ],
      ];
      curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
      
      $resp = curl_exec($curl);
      // dd($resp);
      curl_close($curl);
      return $resp;
   }
}
