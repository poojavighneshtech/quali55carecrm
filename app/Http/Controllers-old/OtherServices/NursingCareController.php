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
    use App\Models\NursingCareLead;

    class NursingCareController extends Controller
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
                $get_countries = DB::select("SELECT * FROM countries");
                $data['get_countries'] = json_decode(json_encode($get_countries),true);
                $get_states = DB::select("SELECT * FROM states");
                $data['get_states'] = json_decode(json_encode($get_states),true);
                $get_cities = DB::select("SELECT * FROM cities");
                $data['get_cities'] = json_decode(json_encode($get_cities),true);
                return view('NursingCareViews.create_lead',$data);
                //return view('OtherLeadViews.view_all_leads',$data);
            }
            if($_SERVER['REQUEST_METHOD']=='POST')
            {
                $contact_no = $_POST['contact_no'];
                $name =$_POST['patient_name'];
                $location = $_POST['location'];
                $AmbulanceLeads = new AmbulanceLeads();
                $LabTestLeads = new LabTestLeads();
                $NursingCareLeads = new NursingCareLeads();
                
                $service_rquirement = $_POST['service_rquirement'];
                $insert_Nursing=[
                    'name'=>$name,
                    'contact_no'=>$contact_no,
                    'line_1'=>$request->get('line_1'),
                    'line_2'=>$request->get('line_2'),
                    'landmark'=>$request->get('landmark'),
                    'area'=>$request->get('area'),
                    'location'=>$request->get('location'),
                    'pincode'=>$request->get('pincode'),
                    'email_id'=>$request->get('email'),
                    'state'=>$request->get('state'),
                    'country'=>$request->get('country'),
                    'service_required'=>$service_rquirement,
                    'date' => $request->get('date'),
                    'price' => $request->get('price'),
                    'therapeutic_rqrmt' => $request->get('therapeutic_rqrmt'),
                    'nurses_type' => $request->get('nurses_type'),
                    'dutie_hr' => $request->get('dutie_hour'),
                    'lead_platform'=>'Web',
                    'lead_source' => $request->get('lead_source'),
                    'reffered_by'=>$request->get('reffered_by'),
                    'created_date'=>date('Y-m-d'),
                    'created_by'=>session('user_id')
                ];
                $NursingCareLeads->insert($insert_Nursing);
                //return redirect()->back()->with('message','Lead Inserted Successfully');
                return redirect('view_all_nursing_care_leads')->with('message','Lead Inserted Successfully');
            }
        }

        //view Nursing care lead
        public function ViewNursingCareLeads(Request $request) {
            
            $get_min_date = NursingCareLeads::min('created_date');
            $get_max_date = NursingCareLeads::max('created_date');
            //get all user
            $get_all_users = DB::table('user')->where('role','=','user')->get(['id','username'])->toArray();
            //get customer location
            $get_locations = DB::table('nursing_care_leads')->distinct('location')->get('location')->toArray();
            $whereCondition = [];
            $customer_name = $request->get('filter_customer_name');
            if(isset($customer_name)){
                $whereCondition1 = ['nursing_care_leads.patient_name','LIKE','%'.$customer_name.'%'];
                array_push($whereCondition,$whereCondition1);
            }
            $customer_contact = $request->get('filter_contact_no');
            if(isset($customer_contact)) {
                $whereCondition2 = ['nursing_care_leads.contact_no','=',$customer_contact];
                array_push($whereCondition,$whereCondition2);
            }
            $lead_status = $request->get('filter_lead_status');
            if(isset($lead_status) && ($lead_status=='In Process' || $lead_status=='Converted' || $lead_status=='Closed' )){
                $whereCondition3 = ['nursing_care_leads.status','=',$lead_status];
                array_push($whereCondition,$whereCondition3);
            }
            $location = $request->get('filter_customer_location');
            if(isset($location) && in_array($location,array_column($get_locations,'location'))){
                $whereCondition4 = ['nursing_care_leads.location','=',$location];
                array_push($whereCondition,$whereCondition4);
            }
            $lead_owner = $request->get('filter_lead_owner');
            if(isset($lead_owner) && in_array($lead_owner,array_column($get_all_users,'id'))){
                $whereCondition7 = ['nursing_care_leads.created_by','=',$lead_owner];
                array_push($whereCondition,$whereCondition7);
            }
            if(session('role')=='user')
            {
                $whereCondition8 = ['nursing_care_leads.created_by','=',session('user_id')];
                array_push($whereCondition,$whereCondition8);
            }
           
            $from_date = $request->get('filter_from_date');
            $end_date = $request->get('filter_end_date');
            if(isset($from_date) && isset($end_date)){
                $get_min_date = $from_date;
                $get_max_date = $end_date;
            }
            
            $get_all_leads = DB::table('nursing_care_leads')
                                ->join('user','nursing_care_leads.created_by','=','user.id')
                                ->select('nursing_care_leads.*','user.username as lead_owner_name')
                                ->where($whereCondition)
                                ->whereBetween('nursing_care_leads.created_date',[$get_min_date,$get_max_date])
                                ->orderBy('nursing_care_leads.id','DESC')
                                ->orderBy('nursing_care_leads.created_date','DESC')
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
                "location"=>$location,
                "lead_owner"=>$lead_owner,
                "from_date"=>$from_date,
                "end_date"=>$end_date,
                "filter_collapse_cookie"=>$filter_collapse_cookie];
            return view('NursingCareViews.view_all_leads',compact('get_all_leads','location','filter_arr','get_all_users','get_locations'));
        }

        public function ConvertLead(Request $request)
        {
            $NurseLead = new NursingCareLeads();
            $lead_id = $request->get('lead_id');
            $NurseLead->where('id',$lead_id)->update(['status'=>'Converted']);
            return redirect()->back();
        }
        public function CloseLead(Request $request)
        {
            $NurseLead = new NursingCareLeads();
            $lead_id = $request->get('lead_id');
            $reason = $request->get('reason');
            $NurseLead->where('id',$lead_id)->update(['status'=>'Closed','comment'=>$reason]);
            return redirect()->back();
        }

        public function index(Request $request){
            $user_id = session('user_id');
            $users = DB::table('user')->where('role','user')->where('flag','Active')->get();
            $leads = NursingCareLead::join('user','user.id','=','nursing_care.lead_owner')
                ->select('nursing_care.*','user.username')
                ->when($request->get('filter_customer_name'),function($query)use($request){
                    $query->where('nursing_care.customer_name','LIKE','%'.$request->get('filter_customer_name').'%');
                })
                ->when($request->get('filter_contact_no'),function($query)use($request){
                    $query->where('nursing_care.contact_no','LIKE','%'.$request->get('filter_contact_no').'%');
                })
                ->when($request->get('filter_start_date') && $request->get('filter_stop_date'),function($query)use($request){
                    $query->whereBetween('nursing_care.start_date',[$request->get('filter_start_date'),$request->get('filter_stop_date')]);
                })
                // ->when($request->get('filter_stop_date'),function($query)use($request){
                //     $query->where('nursing_care.stop_date',$request->get('filter_stop_date'));
                // })
                ->when($request->get('filter_service'),function($query)use($request){
                    $query->where('nursing_care.service_type',$request->get('filter_service'));
                })
                ->when($request->get('filter_status'),function($query)use($request){
                    $query->where('nursing_care.status',$request->get('filter_status'));
                })
                ->when($request->get('filter_lead_owner'),function($query)use($request){
                    $query->where('nursing_care.lead_owner',$request->get('filter_lead_owner'));
                })
                ->when(session('role') == 'user',function($query)use($user_id){
                    $query->where('nursing_care.lead_owner',$user_id);
                })
                ->paginate(10);
            return view('NursingCareViews.index',compact('leads','users'));
        }

        public function create(){
            $states = DB::table('states')->get();
            return view('NursingCareViews.create',compact('states'));
        }

        public function store(Request $request){
            $rules = [
                'lead_date'=>'required',
                'customer_name'=>'required|min:3',
                'contact_no'=>'required|digits:10|min:10|max:10',
                'address_line_1'=>'required|min:3|max:30',
                // 'landmark'=>'required|min:3|max:30',
                'area'=>'required|min:3|max:30',
                'city'=>'required|min:3|max:30',
                'state'=>'required|min:3|max:30',
                // 'pincode'=>'required|digits:6|min:6|max:6',
                'duty_type'=>'required',
                'duty_hours'=>'required',
                'service_type'=>'required',
                'gender'=>'required',
                // 'start_date'=>'required',
                // 'charges'=>'required',
                'status'=>'required',
            ];
            if($request->get('patient_name')!=null){
                $rules['patient_name'] = 'min:3';
            }
            if($request->get('alt_contact_no')!=null){
                $rules['alt_contact_no'] = 'digits:10|min:10|max:10';
            }
            if($request->get('address_line_2')!=null){
                $rules['address_line_2'] = 'min:3';
            }
            if($request->get('stop_date')!=null){
                $rules['stop_date'] = 'gte:start_date';
            }
            // dd($rules);
            $validated = $request->validate($rules);
            // dd($request->all());
            $request->request->remove('_token');
            $insert_data = $request->all();
            $insert_data['lead_owner'] = session('user_id');
            $insert_data['created_by'] = session('username');
            NursingCareLead::insert($insert_data);
            return redirect('nursing-care')->with('message','Lead Inserted!');
        }

        public function view($id){
            $lead = NursingCareLead::join('user','user.id','=','nursing_care.lead_owner')->select('nursing_care.*','user.username')->where('nursing_care.id',$id)->first();
            return view('NursingCareViews.view',compact('lead'));
        }

        public function edit($id){
            $lead = NursingCareLead::join('user','user.id','=','nursing_care.lead_owner')->select('nursing_care.*','user.username')->where('nursing_care.id',$id)->first();
            $states = DB::table('states')->get();
            return view('NursingCareViews.edit',compact('lead','states'));
        }

        public function update(Request $request,$id){
            $rules = [
                'lead_date'=>'required',
                'customer_name'=>'required|min:3',
                'contact_no'=>'required|digits:10|min:10|max:10',
                'address_line_1'=>'required|min:3|max:30',
                // 'landmark'=>'required|min:3|max:30',
                'area'=>'required|min:3|max:30',
                'city'=>'required|min:3|max:30',
                'state'=>'required|min:3|max:30',
                // 'pincode'=>'required|digits:6|min:6|max:6',
                // 'duty_type'=>'required',
                // 'duty_hours'=>'required',
                'service_type'=>'required',
                'gender'=>'required',
                // 'start_date'=>'required',
                // 'charges'=>'required',
                'status'=>'required',
            ];
            if($request->get('patient_name')!=null){
                $rules['patient_name'] = 'min:3';
            }
            if($request->get('alt_contact_no')!=null){
                $rules['alt_contact_no'] = 'digits:10|min:10|max:10';
            }
            if($request->get('address_line_2')!=null){
                $rules['address_line_2'] = 'min:3';
            }
            if($request->get('stop_date')!=null){
                $rules['stop_date'] = 'required|date|after:start_date';
            }
            // dd($rules);
            $validated = $request->validate($rules);
            // dd($request->all());
            $request->request->remove('_token');
            $update_data = $request->all();            
            $update_data['updated_by'] = session('username');
            NursingCareLead::where('id',$id)->update($update_data);
            return redirect('nursing-care')->with('message','Updated!');
        }

        public function cancel($id){
            
        }
        public function statusUpdate(Request $request,$id){
            $status_array = ['Process','Live','Stopped','Cancelled'];
            $timestamp = date("d M, h:i A");
            $status = $status_array[($request->get('status_updated')-1)];
            if($request->get('comment_updated')){
                $desc = "[".$timestamp."]"." (Status Updated to ".$status.") ".$request->get('comment_updated')."\n";
            }else
            {
                $desc = "[".$timestamp."]"." (Status Updated to ".$status.")\n";
            }
            if(!DB::table('nursing_care')->where('id',$id)->where('status',$request->get('status_updated'))->exists()){
                DB::table('nursing_care')->where('id',$id)->update(['status'=>$request->get('status_updated')]);
                if(DB::table('nursing_care')->where('id',$id)->whereNotNull('remark')->exists()){
                    DB::table('nursing_care')->where('id',$id)->update([
                        'nursing_care.remark' => DB::raw("CONCAT(remark, '".$desc."')")
                    ]);
                }else{
                    DB::table('nursing_care')->where('id',$id)->update([
                        'nursing_care.remark' =>$desc
                    ]);
                }
                return redirect()->back()->with('message','Updated!');
            }else{
                return redirect()->back()->with('info','Nothing to update!');
            }
        }
    }
?>