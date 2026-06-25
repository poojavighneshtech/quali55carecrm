<?php

namespace App\Http\Controllers\JDLeadController;

use App\Http\Controllers\Controller;
use App\Models\customer_detail;
use App\Models\lead;
use App\Models\JDLead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\User\UserController;

class JDLeadController extends Controller
{
   public function isLoggedIn()
   {
      $data = session('isLoggedIn');
      //print_r($data);      
      return $data;
   }
   // ****view all jd lead****

   public function viewAllJDLeads()
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
       return redirect()->to($url);
      }
      $data['user_id'] = session('user_id');
      $date = date('Y-m-d');
      $jd_leads = DB::select("SELECT * FROM jd_leads WHERE status = 'New' AND jd_leads.date = '$date' ORDER BY jd_leads.jd_leads_id DESC");
      $data['jd_lead_details'] = json_decode(json_encode($jd_leads), true);
      echo "<script>localStorage['filtered']='today';</script>";
      return view('JDLead/view_all_jd_lead',$data);
   }
   //----------------------close-----------//

   // ****view all jd lead****

   public function viewAllInProcessLeads()
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
       return redirect()->to($url);
      }
      $data['user_id'] = session('user_id');
      $date = date('Y-m-d');
      if(session('role') == "admin")
      {
         $jd_leads = DB::select("SELECT * FROM jd_leads where status='In Process' AND jd_leads.date = '$date' ORDER BY jd_leads.jd_leads_id DESC");
      }
      elseif(session('role') == 'user')
      {
         $session_user_id = session('user_id');
         $jd_leads = DB::select("SELECT * FROM jd_leads where status='In Process' AND jd_leads.date = '$date' AND lead_owner = $session_user_id ORDER BY jd_leads.jd_leads_id DESC");
      }
      $data['jd_lead_details'] = json_decode(json_encode($jd_leads), true);
      for ($i=0; $i <count($data['jd_lead_details']) ; $i++) { 
         $user_id = $data['jd_lead_details'][$i]['lead_owner'];
         $user_name = DB::select("SELECT username FROM user where id='$user_id' ");
         $data['lead_owner'] = json_decode(json_encode($user_name), true);
         $data['jd_lead_details'][$i]['username']=$data['lead_owner'][0]['username'];
      }
      //print_r($data);
      echo "<script>localStorage['filtered']='today';</script>";
      return view('JDLead/view_all_inprocess_lead',$data);
   }
   //----------------------close-----------//

   // ****view all jd Converted lead****

   public function viewAllConvertedLeads()
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
       return redirect()->to($url);
      }
      $data['user_id'] = session('user_id');
      $date = date('Y-m-d');
      if(session('role') == "admin")
      {
         $jd_leads = DB::select("SELECT * FROM jd_leads where status='Converted' ORDER BY jd_leads.jd_leads_id DESC");
      }
      elseif(session('role') == 'user')
      {
         $session_user_id = session('user_id');
         $jd_leads = DB::select("SELECT * FROM jd_leads where status='Converted' AND lead_owner = $session_user_id ORDER BY jd_leads.jd_leads_id DESC");
      }
      $data['jd_lead_details'] = json_decode(json_encode($jd_leads), true);
      for ($i=0; $i <count($data['jd_lead_details']) ; $i++) { 
         $user_id = $data['jd_lead_details'][$i]['lead_owner'];
         $user_name = DB::select("SELECT username FROM user where id='$user_id' ");
         $data['lead_owner'] = json_decode(json_encode($user_name), true);
         $data['jd_lead_details'][$i]['username']=$data['lead_owner'][0]['username'];
      }
      //print_r($data);
      //return view('JDLead/view_all_converted_leads',$data);
   }
   //----------------------close-----------//
   
   public function viewAllClosedLeads()
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
       return redirect()->to($url);
      }
      $data['user_id'] = session('user_id');
      $date = date('Y-m-d');
      if(session('role') == "admin")
      {
         $jd_leads = DB::select("SELECT * FROM jd_leads where (status!='New' AND status!='In Process' AND status!='Converted') AND jd_leads.date = '$date' ORDER BY jd_leads.jd_leads_id DESC");
      }
      elseif(session('role') == 'user')
      {
         $session_user_id = session('user_id');
         $jd_leads = DB::select("SELECT * FROM jd_leads where (status!='New' AND status!='In Process' AND status!='Converted') AND jd_leads.date = '$date'AND lead_owner = $session_user_id ORDER BY jd_leads.jd_leads_id DESC");
      }
      $data['jd_lead_details'] = json_decode(json_encode($jd_leads), true);
      for ($i=0; $i <count($data['jd_lead_details']) ; $i++) { 
         $user_id = $data['jd_lead_details'][$i]['lead_owner'];
         $user_name = DB::select("SELECT username FROM user where id='$user_id' ");
         $data['lead_owner'] = json_decode(json_encode($user_name), true);
         $data['jd_lead_details'][$i]['username']=$data['lead_owner'][0]['username'];
      }
      //print_r($data);
      echo "<script>localStorage['filtered']='today';</script>";
      return view('JDLead/view_all_closed_leads',$data);
   }

   //-----------update Inprocess_lead-------//
   public function in_process($jd_lead_id, $user_id)
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
       return redirect()->to($url);
      }
      $JD_Leads = new JDLead();
      $lead_status = [
         'status' => "In Process",
         'lead_owner' => $user_id
      ];
      $JD_Leads->where('jd_leads_id',$jd_lead_id)->update($lead_status);
      //$Update_lead = "UPDATE jd_leads SET status='In Process',lead_owner = '$user_id' WHERE jd_leads_id='$jd_lead_id' ";
      //echo "UPDATE jd_leads SET status='In Process',lead_owner = '$user_id' WHERE jd_leads_id='$jd_lead_id' ";
      return redirect('view_all_jd_leads')->with('message','Lead Updated to In Process status Successfully');
   }

   //--------------CLose Lead --------------------------//
   public function close_jd_lead($user_id, $lead_id)
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
       return redirect()->to($url);
      }
      $JD_Leads = new JDLead();
      //$leads->where('id',$id)->delete();
      $lead_status = [
         'status' => 'Closed'
      ];
      $JD_Leads->where('jd_leads_id',$lead_id)->update($lead_status);
      return redirect('/view_all_inprocess_leads')->with('message', 'Lead Closed Successfully');
   }
   public function close_jd_lead_with_reason($user_id,$lead_id,$reason,$desc)
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
       return redirect()->to($url);
      }
      $JD_Leads = new JDLead();
      //$leads->where('id',$id)->delete();
      $lead_status = [
         'status' => $reason,
         'remark' => $desc
      ];
      $JD_Leads->where('jd_leads_id',$lead_id)->update($lead_status);
      return redirect('/view_all_inprocess_leads')->with('message', 'Lead Closed Successfully');
      //return $this->viewAllLeads();
   }

   //---------------COnvert to create lead_---------------//
   public function create_jd_lead($lead_id)
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
       return redirect()->to($url);
      }
      $JD_Leads = new JDLead();
      $customer_details = new customer_detail();
      $leads = new lead();
      $equipments = request()->get('eqipments');
      $equipments_requirements = json_encode($equipments);
      
      if($_SERVER['REQUEST_METHOD']=='GET')
      {
         $products = DB::select("SELECT * FROM products");
         $data['products'] = \json_decode(\json_encode($products), true);
         $cities = DB::select("SELECT * FROM cities");
         $data['cities'] = \json_decode(\json_encode($cities), true);
         $states = DB::select("SELECT * FROM states");
         $data['states'] = \json_decode(\json_encode($states), true);
         $countries = DB::select("SELECT * FROM countries");
         $data['countries'] = \json_decode(\json_encode($countries), true);
         $users = DB::select("SELECT * FROM user WHERE role!='vendor'");
         $data['users'] = \json_decode(\json_encode($users), true);

         $lead_info = DB::select("SELECT * FROM jd_leads WHERE jd_leads_id ='$lead_id' ");
         $data['jd_lead_details'] = json_decode(json_encode($lead_info), true);
         
         //print_r($data['jd_lead_details']);
         return view('JDLead/create_jd_lead',$data);
      }
      if($_SERVER['REQUEST_METHOD']=='POST')
      {
         if($_POST['submit']=='submit1')
         {
            $cust_date = date('Y-m-d');
            $cutomer_details_insertData = [
               'customer_name' => request()->get('cust_name'),
               'cust_date' => $cust_date,
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
               'lead_status' => 'Quali5Care',
               'lead_owner' => request()->get('lead_owner'),
               'created_by' => session('username')
            ];
            //-------insert in lead table --------------//
            $leads->insert($leads_insertData);

            //--------update jd lead status ---------//
            $lead_status = [
               'status' => 'Converted'
            ];
            $JD_Leads->where('jd_leads_id',$lead_id)->update($lead_status);
            return redirect('view_all_inprocess_leads')->with('message', 'Lead Converted Successfully');
            
         }
         if($_POST['submit']=='submit2')
         {
            $cutomer_details_insertData = [
               'customer_name' => request()->get('cust_name'),
               'cust_date' => $cust_date,
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
               'lead_status' => 'Quali5Care',
               'lead_owner' => request()->get('lead_owner'),
               'created_by' => session('username')
            ];
             //-------insert in lead table --------------//
             $leads->insert($leads_insertData);

             //--------update jd lead status ---------//
             $lead_status = [
                'status' => 'Converted'
             ];
             $JD_Leads->where('jd_leads_id',$lead_id)->update($lead_status);
             return redirect('view_all_inprocess_leads')->with('message', 'Lead Converted Successfully');
             
         }
      }
   }

   //----------Q5c leads ------//
   public function viewAllQ5CLeads()
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
       return redirect()->to($url);
      }
      $date = date('Y-m-d');
      if(session('role') == "admin")
      {
         $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Quali5Care' AND leads.creation_date = '$date' ORDER BY leads.creation_date DESC");
      }
      elseif(session('role') == "user")
      {
         $session_user_id = session('user_id');
         $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Quali5Care' AND leads.creation_date = '$date' AND leads.lead_owner = $session_user_id ORDER BY leads.creation_date DESC");
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
      //return view('/leads/viewConvertedLeads',$data);
      echo "<script>localStorage['filtered']='today';</script>";
      return view('JDLead/view_all_q5c_leads',$data);
   }

   //-----------add ccomment post method----//
   public function add_comment($user_id,$lead_id,$desc)
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
       return redirect()->to($url);
      }
      $JD_Leads = new JDLead();
      //$leads->where('id',$id)->delete();
      $timestamp = date("d M, h:i A");
      $comment = "[".$timestamp."]".$desc."\n";
      $lead_status = [
         'remark' => $comment
      ];
      $cmt_check = DB::select("SELECT remark FROM jd_leads WHERE jd_leads_id = '$lead_id' ");
      $data['cmt_check'] = json_decode(json_encode($cmt_check), true);
   
      if(isset($data['cmt_check'][0]['remark']))
      {
		//$cmt_update = "UPDATE jd_leads SET remark = CONCAT(remark, '$comment') WHERE jd_leads_id = '$lead_id' ";
		$cmt_update = DB::update("UPDATE jd_leads SET remark = CONCAT('$comment',remark) WHERE jd_leads_id = '$lead_id' ");
		
		//$JD_Leads->where('jd_leads_id',$lead_id)->update($lead_status);
	  }
	  else
	  {
		//print_r($lead_status);
		$JD_Leads->where('jd_leads_id',$lead_id)->update($lead_status);
	  }
	  //$cmt_update = "UPDATE jd_leads SET remark = CONCAT(remark, '$comment') WHERE jd_leads_id = '$lead_id' ";
      //return redirect('/view_all_inprocess_leads')->with('message', 'comment add Successfully');
      return redirect()->back()->with('message', 'comment add Successfully');
      //return $this->viewAllLeads();
   }

   public function add_converted_comment($user_id,$lead_id,$desc)
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
       return redirect()->to($url);
      }
      $Leads = new lead();
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
		//$cmt_update = "UPDATE jd_leads SET remark = CONCAT(remark, '$comment') WHERE jd_leads_id = '$lead_id' ";
		$cmt_update = DB::update("UPDATE leads SET comment = CONCAT('$comment',comment) WHERE id = '$lead_id' ");
		
		//$JD_Leads->where('jd_leads_id',$lead_id)->update($lead_status);
	  }
	  else
	  {
		//print_r($lead_status);
		$Leads->where('id',$lead_id)->update($lead_status);
	  }
	  //$cmt_update = "UPDATE jd_leads SET remark = CONCAT(remark, '$comment') WHERE jd_leads_id = '$lead_id' ";
      //return redirect('/view_all_inprocess_leads')->with('message', 'comment add Successfully');
      return redirect()->back()->with('message', 'comment add Successfully');
      //return $this->viewAllLeads();
   }

   public function filterJDLeadsViewAll($filter_by)
   {
      if($filter_by =='today')
      {
         $date = date('Y-m-d');
         $whereClause = "jd_leads.date = '$date'";
         echo "<script>localStorage['filtered']='today';</script>";
      }
      elseif($filter_by =='yesterday')
      {
         $prevDate = date('Y-m-d',strtotime("-1 days"));
         $whereClause = "jd_leads.date = '$prevDate'";
         echo "<script>localStorage['filtered']='yesterday';</script>";
      }
      elseif($filter_by =='past_3_days')
      {
         $past_three_days = date('Y-m-d',strtotime("-2 days"));
         $whereClause = "jd_leads.date >= '$past_three_days'";
         echo "<script>localStorage['filtered']='past_3_days';</script>";
      }
      elseif($filter_by =='week')
      {
         $past_three_days = date('Y-m-d',strtotime("-6 days"));
         $whereClause = "jd_leads.date >= '$past_three_days'";
         echo "<script>localStorage['filtered']='week';</script>";
      }
      elseif($filter_by =='month')
      {
         $month = date('m-Y');
         $start_date_temp = '01-'.$month;
         $start_date = date('Y-m-d',strtotime($start_date_temp));
         $end_date_temp = '31-'.$month;
         $end_date = date('Y-m-d',strtotime($end_date_temp));
         $whereClause = "jd_leads.date BETWEEN '$start_date' AND '$end_date'";
      }
      elseif($filter_by == 'all')
      {
         $jd_leads = DB::select("SELECT * FROM jd_leads WHERE status = 'New' ORDER BY jd_leads.jd_leads_id DESC");
         $data['jd_lead_details'] = json_decode(json_encode($jd_leads), true);
         echo "<script>localStorage['filtered']='all';</script>";
         return view('JDLead/view_all_jd_lead',$data);
      }
      $jd_leads = DB::select("SELECT * FROM jd_leads WHERE $whereClause ORDER BY jd_leads.jd_leads_id DESC");
      $data['jd_lead_details'] = json_decode(json_encode($jd_leads), true);
      return view('JDLead/view_all_jd_lead',$data);
   }
   
   public function filterJDLeadsInProgress($filter_by)
   {
      if($filter_by =='today')
      {
         $date = date('Y-m-d');
         $whereClause = "jd_leads.date = '$date'";
         echo "<script>localStorage['filtered']='today';</script>";
      }
      elseif($filter_by =='yesterday')
      {
         $prevDate = date('Y-m-d',strtotime("-1 days"));
         $whereClause = "jd_leads.date = '$prevDate'";
         echo "<script>localStorage['filtered']='yesterday';</script>";
      }
      elseif($filter_by =='past_3_days')
      {
         $past_three_days = date('Y-m-d',strtotime("-2 days"));
         $whereClause = "jd_leads.date >= '$past_three_days'";
         echo "<script>localStorage['filtered']='past_3_days';</script>";
      }
      elseif($filter_by =='week')
      {
         $past_three_days = date('Y-m-d',strtotime("-6 days"));
         $whereClause = "jd_leads.date >= '$past_three_days'";
         echo "<script>localStorage['filtered']='week';</script>";
      }
      elseif($filter_by =='month')
      {
         $month = date('m-Y');
         $start_date_temp = '01-'.$month;
         $start_date = date('Y-m-d',strtotime($start_date_temp));
         $end_date_temp = '31-'.$month;
         $end_date = date('Y-m-d',strtotime($end_date_temp));
         $whereClause = "jd_leads.date BETWEEN '$start_date' AND '$end_date'";
      }
      elseif($filter_by =='all')
      {
         $isLoggedIn = $this->isLoggedIn();
         if($isLoggedIn == 'false')
         {
             $url = url('/');
          return redirect()->to($url);
         }
         $data['user_id'] = session('user_id');
         if(session('role') == "admin")
         {
            $jd_leads = DB::select("SELECT * FROM jd_leads where status='In Process'ORDER BY jd_leads.jd_leads_id DESC");
         }
         elseif(session('role') == 'user')
         {
            $session_user_id = session('user_id');
            $jd_leads = DB::select("SELECT * FROM jd_leads where status='In Process' AND lead_owner = $session_user_id ORDER BY jd_leads.jd_leads_id DESC");
         }
         $data['jd_lead_details'] = json_decode(json_encode($jd_leads), true);
         for ($i=0; $i <count($data['jd_lead_details']) ; $i++) { 
            $user_id = $data['jd_lead_details'][$i]['lead_owner'];
            $user_name = DB::select("SELECT username FROM user where id='$user_id' ");
            $data['lead_owner'] = json_decode(json_encode($user_name), true);
            $data['jd_lead_details'][$i]['username']=$data['lead_owner'][0]['username'];
         }
         echo "<script>localStorage['filtered']='all';</script>";
         return view('JDLead/view_all_inprocess_lead',$data);
      }
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
            $url = url('/');
         return redirect()->to($url);
      }
      $data['user_id'] = session('user_id');
      if(session('role') == "admin")
      {
         $jd_leads = DB::select("SELECT * FROM jd_leads where status='In Process' AND $whereClause ORDER BY jd_leads.jd_leads_id DESC");
      }
      elseif(session('role') == 'user')
      {
         $session_user_id = session('user_id');
         $jd_leads = DB::select("SELECT * FROM jd_leads where status='In Process' AND $whereClause AND lead_owner = $session_user_id ORDER BY jd_leads.jd_leads_id DESC");
      }
      $data['jd_lead_details'] = json_decode(json_encode($jd_leads), true);
      for ($i=0; $i <count($data['jd_lead_details']) ; $i++) { 
         $user_id = $data['jd_lead_details'][$i]['lead_owner'];
         $user_name = DB::select("SELECT username FROM user where id='$user_id' ");
         $data['lead_owner'] = json_decode(json_encode($user_name), true);
         $data['jd_lead_details'][$i]['username']=$data['lead_owner'][0]['username'];
      }
      return view('JDLead/view_all_inprocess_lead',$data);
   }

   public function filterJDLeadsConverted($filter_by)
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
      elseif($filter_by =='all')
      {
         if(session('role') == "admin")
         {
            $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Quali5Care'ORDER BY leads.creation_date DESC");
         }
         elseif(session('role') == "user")
         {
            $session_user_id = session('user_id');
            $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Quali5Care' AND leads.lead_owner = $session_user_id ORDER BY leads.creation_date DESC");
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
         return view('JDLead/view_all_q5c_leads',$data);
      }
      if(session('role') == "admin")
         {
            $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Quali5Care' AND $whereClause ORDER BY leads.creation_date DESC");
         }
         elseif(session('role') == "user")
         {
            $session_user_id = session('user_id');
            $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Quali5Care' AND $whereClause AND leads.lead_owner = $session_user_id ORDER BY leads.creation_date DESC");
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
         return view('JDLead/view_all_q5c_leads',$data);
   }
   public function filterJDLeadsClosed($filter_by)
   {
      if($filter_by =='today')
      {
         $date = date('Y-m-d');
         $whereClause = "jd_leads.date = '$date'";
         echo "<script>localStorage['filtered']='today';</script>";
      }
      elseif($filter_by =='yesterday')
      {
         $prevDate = date('Y-m-d',strtotime("-1 days"));
         $whereClause = "jd_leads.date = '$prevDate'";
         echo "<script>localStorage['filtered']='yesterday';</script>";
      }
      elseif($filter_by =='past_3_days')
      {
         $past_three_days = date('Y-m-d',strtotime("-2 days"));
         $whereClause = "jd_leads.date >= '$past_three_days'";
         echo "<script>localStorage['filtered']='past_3_days';</script>";
      }
      elseif($filter_by =='week')
      {
         $past_three_days = date('Y-m-d',strtotime("-6 days"));
         $whereClause = "jd_leads.date >= '$past_three_days'";
         echo "<script>localStorage['filtered']='week';</script>";
      }
      elseif($filter_by =='month')
      {
         $month = date('m-Y');
         $start_date_temp = '01-'.$month;
         $start_date = date('Y-m-d',strtotime($start_date_temp));
         $end_date_temp = '31-'.$month;
         $end_date = date('Y-m-d',strtotime($end_date_temp));
         $whereClause = "jd_leads.date BETWEEN '$start_date' AND '$end_date'";
      }
      elseif($filter_by =='all')
      {
         $isLoggedIn = $this->isLoggedIn();
         if($isLoggedIn == 'false')
         {
             $url = url('/');
          return redirect()->to($url);
         }
         $data['user_id'] = session('user_id');
         if(session('role') == "admin")
         {
            $jd_leads = DB::select("SELECT * FROM jd_leads where (status!='New' AND status!='In Process' AND status!='Converted') ORDER BY jd_leads.jd_leads_id DESC");
         }
         elseif(session('role') == 'user')
         {
            $session_user_id = session('user_id');
            $jd_leads = DB::select("SELECT * FROM jd_leads where (status!='New' AND status!='In Process' AND status!='Converted') AND lead_owner = $session_user_id ORDER BY jd_leads.jd_leads_id DESC");
         }
         $data['jd_lead_details'] = json_decode(json_encode($jd_leads), true);
         for ($i=0; $i <count($data['jd_lead_details']) ; $i++) { 
            $user_id = $data['jd_lead_details'][$i]['lead_owner'];
            $user_name = DB::select("SELECT username FROM user where id='$user_id' ");
            $data['lead_owner'] = json_decode(json_encode($user_name), true);
            $data['jd_lead_details'][$i]['username']=$data['lead_owner'][0]['username'];
         }
         echo "<script>localStorage['filtered']='all';</script>";
         return view('JDLead/view_all_closed_leads',$data);
      }
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
            $url = url('/');
         return redirect()->to($url);
      }
      $data['user_id'] = session('user_id');
      if(session('role') == "admin")
      {
         $jd_leads = DB::select("SELECT * FROM jd_leads where (status!='New' AND status!='In Process' AND status!='Converted') AND $whereClause ORDER BY jd_leads.jd_leads_id DESC");
      }
      elseif(session('role') == 'user')
      {
         $session_user_id = session('user_id');
         $jd_leads = DB::select("SELECT * FROM jd_leads where (status!='New' AND status!='In Process' AND status!='Converted') AND $whereClause AND lead_owner = $session_user_id ORDER BY jd_leads.jd_leads_id DESC");
      }
      $data['jd_lead_details'] = json_decode(json_encode($jd_leads), true);
      for ($i=0; $i <count($data['jd_lead_details']) ; $i++) { 
         $user_id = $data['jd_lead_details'][$i]['lead_owner'];
         $user_name = DB::select("SELECT username FROM user where id='$user_id' ");
         $data['lead_owner'] = json_decode(json_encode($user_name), true);
         $data['jd_lead_details'][$i]['username']=$data['lead_owner'][0]['username'];
      }
      return view('JDLead/view_all_closed_leads',$data);
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
}
