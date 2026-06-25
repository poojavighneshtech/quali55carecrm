<?php

    namespace App\Http\Controllers\OtherServices;

    use Illuminate\Support\Facades\Validator;
    use Illuminate\Support\Facades\DB;
    use App\Models\VendorRegister;
    use App\Models\UserRegister;
    use App\Models\DelOrders;
    use App\Models\lead;
    use App\Models\OrderDetails;
    use App\Models\VendorProducts;
    use App\Models\leads_log;
    use App\Models\sale_vendor_products;
    use App\Models\VendorRentedProducts;
    use App\Models\AmbulanceLeads;
    use App\Models\LabTestLeads;
    use App\Models\NursingCareLeads;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    use Mail;

    class AmbulanceController extends Controller
    {
        public function isLoggedIn()
        {
            $data = session('isLoggedIn');
            return $data;
        }

        public function CreateLead(Request $request)
        {
            if($_SERVER['REQUEST_METHOD']=='GET')
            {
                $get_lab_test = DB::select("SELECT * FROM lab_test_list");
                $data['get_lab_test'] = json_decode(json_encode($get_lab_test),true);
                $get_countries = DB::select("SELECT * FROM countries");
                $data['get_countries'] = json_decode(json_encode($get_countries),true);
                $get_states = DB::select("SELECT * FROM states");
                $data['get_states'] = json_decode(json_encode($get_states),true);
                $get_cities = DB::select("SELECT * FROM cities");
                $data['get_cities'] = json_decode(json_encode($get_cities),true);
                return view('AmbulanceViews.create_lead',$data);
            }
            if($_SERVER['REQUEST_METHOD']=='POST')
            {
                $contact_no = $_POST['contact_no'];
                $name =$_POST['patient_name'];
                $AmbulanceLeads = new AmbulanceLeads();
                $LabTestLeads = new LabTestLeads();
                $NursingCareLeads = new NursingCareLeads();
                
                $pickup_location = $_POST['pickup_location'];
                $drop_location = $_POST['drop_location'];
                $ambulance_type = $_POST['ambulance_type'];
                $insert_Amb = [
                    'patient_name'=>$name,
                    'contact_no'=>$contact_no,
                    'date'=>$request->get('date'),
                    'waiting_time'=>$request->get('waiting_time'),
                    'price'=>$request->get('price'),
                    'service_from'=>$request->get('pickup_location'),
                    'service_to'=>$request->get('drop_location'),
                    'pickup_line_1'=>$request->get('pickup_line_1'),
                    'pickup_line_2'=>$request->get('pickup_line_2'),
                    'pickup_landmark'=>$request->get('pickup_landmark'),
                    'pickup_area'=>$request->get('pickup_area'),
                    'pickup_location'=>$request->get('pickup_location'),
                    'pickup_city'=>$request->get('pickup_city'),
                    'pickup_pincode'=>$request->get('pickup_pincode'),
                    'pickup_email'=>$request->get('pickup_email'),
                    'pickup_state'=>$request->get('pickup_state'),
                    'pickup_country'=>$request->get('pickup_country'),
                    'drop_line_1'=>$request->get('drop_line_1'),
                    'drop_line_2'=>$request->get('drop_line_2'),
                    'drop_landmark'=>$request->get('drop_landmark'),
                    'drop_area'=>$request->get('drop_area'),
                    'drop_location'=>$request->get('drop_location'),
                    'drop_city'=>$request->get('drop_city'),
                    'drop_pincode'=>$request->get('drop_pincode'),
                    'drop_email'=>$request->get('drop_email'),
                    'drop_state'=>$request->get('drop_state'),
                    'drop_country'=>$request->get('drop_country'),
                    'ambulance_type'=>$ambulance_type,
                    'lead_platform'=>'Web',
                    'lead_source'=>$request->get('lead_source'),
                    'reffered_by'=>$request->get('reffered_by'),
                    'customer_name'=>$request->get('customer_name'),
                    'customer_type'=>$request->get('customer_type'),
                    'created_by'=>session('user_id')
                ];
                $AmbulanceLeads->insert($insert_Amb);
                //return redirect()->back()->with('message ','Lead Inserted Successfully');
                return redirect('view_all_ambulance_leads')->with('message ','Lead Inserted Successfully');
            }
        }

        public function ViewAmbulanceLeads(Request $request) {

            $get_min_date = AmbulanceLeads::min('date');
            $get_max_date = AmbulanceLeads::max('date');
            //get all user
            $get_all_users = DB::table('user')->where('role','=','user')->get(['id','username'])->toArray();
            //get customer location
            $get_pickup_locations = DB::table('amb_user_data')->distinct('pickup_location')->get('pickup_location')->toArray();
            //dd($get_pickup_locations);
            $get_drop_locations = DB::table('amb_user_data')->distinct('drop_location')->get('drop_location')->toArray();
            $whereCondition = [];
            $customer_name = $request->get('filter_customer_name');
            if(isset($customer_name)){
                $whereCondition1 = ['amb_user_data.patient_name','LIKE','%'.$customer_name.'%'];
                array_push($whereCondition,$whereCondition1);
            }
            $customer_contact = $request->get('filter_contact_no');
            if(isset($customer_contact)) {
                $whereCondition2 = ['amb_user_data.contact_no','=',$customer_contact];
                array_push($whereCondition,$whereCondition2);
            }
            $lead_status = $request->get('filter_lead_status');
            if(isset($lead_status) && ($lead_status=='In Process' || $lead_status=='Converted' || $lead_status=='Closed' )){
                $whereCondition3 = ['amb_user_data.status','=',$lead_status];
                array_push($whereCondition,$whereCondition3);
            }
            $pickup_location = $request->get('filter_customer_pickup_location');
            if(isset($pickup_location) && in_array($pickup_location,array_column($get_pickup_locations,'pickup_location'))){
                $whereCondition4 = ['amb_user_data.pickup_location','=',$pickup_location];
                array_push($whereCondition,$whereCondition4);
            }
            $drop_location = $request->get('filter_customer_drop_location');
            if(isset($drop_location) && in_array($drop_location,array_column($get_drop_locations,'drop_location'))){
                $whereCondition5 = ['amb_user_data.drop_location','=',$drop_location];
                array_push($whereCondition,$whereCondition5);
            }
            $ambulance_type = $request->get('filter_ambulance_type');
            $amb_arr = array("Cardic","Non Cardiac","Covid 2019","Ac","Non-Ac");
            if(isset($ambulance_type) && in_array($ambulance_type,$amb_arr)){
                $whereCondition6 = ['amb_user_data.ambulance_type','=',$ambulance_type];
                array_push($whereCondition,$whereCondition6);
            }
            $lead_owner = $request->get('filter_lead_owner');
            if(isset($lead_owner) && in_array($lead_owner,array_column($get_all_users,'id'))){
                $whereCondition7 = ['amb_user_data.created_by','=',$lead_owner];
                array_push($whereCondition,$whereCondition7);
            }
            if(session('role')=='user')
            {
                $whereCondition8 = ['amb_user_data.created_by','=',session('user_id')];
                array_push($whereCondition,$whereCondition8);
            }
           
            $from_date = $request->get('filter_from_date');
            $end_date = $request->get('filter_end_date');
            if(isset($from_date) && isset($end_date)){
                $get_min_date = $from_date;
                $get_max_date = $end_date;
            }
            
            $get_all_leads = DB::table('amb_user_data')
                                ->join('user','amb_user_data.created_by','=','user.id')
                                ->select('amb_user_data.*','user.username as lead_owner_name')
                                ->where($whereCondition)
                                ->whereBetween('amb_user_data.date',[$get_min_date,$get_max_date])
                                ->orderBy('amb_user_data.amb_id','DESC')
                                ->orderBy('amb_user_data.date','DESC')
                                ->paginate(10);
            //fiilter collapseable 
            $filter_collapse_cookie = null;
            if(isset($_COOKIE['filter_collapse_js']) && $_COOKIE['filter_collapse_js'] =='Yes')
            {
                $filter_collapse_cookie = 1;
            }
            //flter set 
            $filter_arr = ["cust_name"=>$customer_name,
                "cust_no"=>$customer_contact,
                "lead_status"=>$lead_status,
                "pickup_location"=>$pickup_location,
                "drop_location"=>$drop_location,
                "ambulance_type"=>$ambulance_type,
                "lead_owner"=>$lead_owner,
                "from_date"=>$from_date,
                "end_date"=>$end_date,
                "filter_collapse_cookie"=>$filter_collapse_cookie];
            return view('AmbulanceViews.view_all_leads',compact('get_all_leads','get_pickup_locations','get_drop_locations','filter_arr','get_all_users'));
        }
        public function ConvertLead(Request $request)
        {
            $AmbulanceLeads = new AmbulanceLeads();
            $lead_id = $request->get('lead_id');
            //echo $lead_id;
            $AmbulanceLeads->where('amb_id',$lead_id)->update(['status'=>'Converted']);
            return redirect()->back();
        }
        public function CloseLead(Request $request)
        {
            $AmbulanceLeads = new AmbulanceLeads();
            $lead_id = $request->get('lead_id');
            $reason = $request->get('reason');
            $AmbulanceLeads->where('amb_id',$lead_id)->update(['status'=>'Closed','comment'=>$reason]);
            return redirect()->back();
        }
        
    }
?>