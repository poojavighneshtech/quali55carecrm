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
    use App\Models\LabTestCustomers;
    use App\Models\NursingCareLeads;
    use App\Models\Labs;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    use Mail;

    class LabController extends Controller
    {
        public function isLoggedIn()
        {
            $data = session('isLoggedIn');
            return $data;
        }

        public function Registerlab(Request $request)
        {
            if($_SERVER['REQUEST_METHOD']=='GET')
            {
                $get_countries = DB::select("SELECT * FROM countries");
                $data['get_countries'] = json_decode(json_encode($get_countries),true);
                $get_states = DB::select("SELECT * FROM states");
                $data['get_states'] = json_decode(json_encode($get_states),true);
                $get_cities = DB::select("SELECT * FROM cities");
                $data['get_cities'] = json_decode(json_encode($get_cities),true);
                return view('LabTestViews.lab_registration',$data);
            }
            if($_SERVER['REQUEST_METHOD']=='POST')
            {
                //print_r($_POST);
                $Lab = new Labs();
                $lab_name = $_POST['lab_name'];
                if(isset($_POST['other_contact_persons']))
                {
                    $other_contact_persons = json_encode($_POST['other_contact_persons']);
                }
                else{ $other_contact_persons =null; }
                //certificate aggreement
                if($_FILES['aggreement']['name'][0]!=null)
                {
                    $reference_image = $_FILES['aggreement']['name'];    
                    $targetDir = "assets/uploads/lab_certificates/";
                    $fileName = basename($_FILES['aggreement']['name']);
                    $targetFilePath = $targetDir . $fileName;
                    $fileType =strtolower(pathinfo($targetFilePath,PATHINFO_EXTENSION));
                    $new_file_name = $lab_name."-aggreement".".".$fileType;
                    move_uploaded_file($_FILES["aggreement"]["tmp_name"], $targetDir.$new_file_name);        
                    $aggreement_filePath = url('/')."/assets/uploads/lab_certificates/".$new_file_name;
                    //array_push($img_path,$filePath);
                }
                
                //other certificates
                $other_certificates_path = array();
                if(isset($_POST['other_certificate']))
                {
                    $other_certificates = $_POST['other_certificate'];
                    for ($i=0; $i <count($other_certificates) ; $i++) { 
                        $name = $_POST['other_certificate'][$i];
                        //certifiactes
                        $reference_image = $_FILES['other_certificate']['name'][$i] ;    
                        $targetDir = "assets/uploads/lab_certificates/";
                        $fileName = basename($_FILES['other_certificate']['name'][$i]);
                        $targetFilePath = $targetDir . $fileName;
                        $fileType =strtolower(pathinfo($targetFilePath,PATHINFO_EXTENSION));
                        $new_file_name = $lab_name."-".$name.".".$fileType;
                        move_uploaded_file($_FILES["other_certificate"]["tmp_name"][$i], $targetDir.$new_file_name);        
                        $filePath = url('/')."/assets/uploads/lab_certificates/".$new_file_name;
                        $other_certificates_path[$i]['name'] = $name;
                        $other_certificates_path[$i]['path'] = $filePath;
                    }
                    $other_cer_path = json_encode($other_certificates_path);
                }
                else
                {
                    $other_cer_path = null;
                }
                
                $insert_Lab = [
                    'lab_name'=>$request->get('lab_name'),
                    'line_1'=>$request->get('line_1'),
                    'line_2'=>$request->get('line_2'),
                    'landmark'=>$request->get('landmark'),
                    'area'=>$request->get('area'),
                    'location'=>$request->get('location'),
                    'city'=>$request->get('city'),
                    'pincode'=>$request->get('pincode'),
                    'lab_email'=>$request->get('email'),
                    'state'=>$request->get('state'),
                    'country'=>$request->get('country'),
                    'aggreement'=>json_encode($aggreement_filePath),
                    'other_certificates'=>$other_cer_path,
                    'person1_name'=>$request->get('person1_name'),
                    'person1_contact'=>$request->get('person1_contact'),
                    'person1_email'=>$request->get('person1_email'),
                    'other_contact_persons'=>$other_contact_persons,
                    'created_by'=>session('username')
                ];
                $Lab->insert($insert_Lab);
                return redirect('/view_all_labs')->with('message','Lab Registered Successfully');

            }
        }

        public function ViewAllLabs()
        {
            $get_labs = DB::SELECT("SELECT * FROM labs");
            $data['get_labs'] = json_decode(json_encode($get_labs),true);
            //print_r($data);
            return view('LabTestViews.view_all_labs',$data);
        }
        
        public function ViewLab($id)
        {
            $get_countries = DB::select("SELECT * FROM countries");
            $data['get_countries'] = json_decode(json_encode($get_countries),true);
            $get_states = DB::select("SELECT * FROM states");
            $data['get_states'] = json_decode(json_encode($get_states),true);
            $get_cities = DB::select("SELECT * FROM cities");
            $data['get_cities'] = json_decode(json_encode($get_cities),true);

            $get_lab = DB::select("SELECT * FROM labs WHERE id=$id");
            $data['get_lab'] = json_decode(json_encode($get_lab),true);
            return view('LabTestViews.view_lab',$data);
        }
        public function UpdateRegisterlab(Request $request)
        {
            //print_r($_POST);
            $Lab = new Labs();
            $id = $_POST['lab_id'];
            $lab_name = $_POST['lab_name'];
            $old_lab_name =$_POST['old_lab_name'];
            $old_certificates_name = $_POST['old_certificates_name'];
            $old_certificates_status = $_POST['old_certificates_status'];   
            $old_certificates_path = $_POST['old_certificates_path'];  
            if(isset($_POST['other_contact_persons']))
            {
                $other_contact_persons = json_encode($_POST['other_contact_persons']);
            }
            else{ $other_contact_persons =null; }

            if(isset($_POST['other_certificate_name']))
            {
                $other_certificate_name = $_POST['other_certificate_name'];
            }
            else
            {
                $other_certificate_name = array();
            }
            if(isset($_POST['old_other_certificate_name']))
            {
                $old_other_certificates_name = $_POST['old_other_certificate_name'];
            }
            else
            {
                $old_other_certificates_name = array();
            }
            $other_file_path = array();

            //deleted  update certificates
            for ($i=0; $i <count($old_certificates_status); $i++) 
            { 
                $count = count($other_file_path);
                if($old_certificates_status[$i]=='Deleted' && file_exists("/var/www/html".$old_certificates_path[$i]))
                {
                    //$path = ltrim($old_certificates_path[$i], '/');
                    unlink("/var/www/html".$old_certificates_path[$i]);
                }
                elseif($old_certificates_status[$i]=='Updated' && file_exists("/var/www/html".$old_certificates_path[$i]))
                {
                    //unlink("/var/www/html".$old_certificates_path[$i]);
                    $name = $_POST['old_other_certificate_name'][$i][0];
                    //certifiactes
                    if($aggreement_file = $_FILES['old_other_certificate_file']['name'][$i][0]!=null)
                    {
                        $targetDir = "assets/uploads/lab_certificates/";
                        $fileName = basename($_FILES['old_other_certificate_file']['name'][$i][0]);
                        $targetFilePath = $targetDir . $fileName;
                        $fileType =strtolower(pathinfo($targetFilePath,PATHINFO_EXTENSION));
                        $new_file_name = $lab_name."-".$name.".".$fileType;
                        move_uploaded_file($_FILES["old_other_certificate_file"]["tmp_name"][$i], $targetDir.$new_file_name);        
                        $filePath = url('/')."/assets/uploads/lab_certificates/".$new_file_name;
                        //check count of array
                        $other_file_path[$count]['name'] = $name;
                        $other_file_path[$count]['path'] = $filePath;
                    }
                }
                else
                {
                    $other_file_path[$count]['name'] = $old_certificates_name[$i];
                    $other_file_path[$count]['path'] = $old_certificates_path[$i];
                    $count++;
                }
            }

            //certificate aggreement
            $aggreement_filePath = null;
            if($_FILES['aggreement']['name']!=null)
            {
                if(file_exists("/var/www/html".$_POST['old_aggreement_path']))
                {
                    unlink("/var/www/html".$_POST['old_aggreement_path']);
                }
                $aggreement_file = $_FILES['aggreement']['name'];    
                $targetDir = "assets/uploads/lab_certificates/";
                $fileName = basename($_FILES['aggreement']['name']);
                $targetFilePath = $targetDir . $fileName;
                $fileType =strtolower(pathinfo($targetFilePath,PATHINFO_EXTENSION));
                $new_file_name = $lab_name."-aggreement".".".$fileType;
                move_uploaded_file($_FILES["aggreement"]["tmp_name"], $targetDir.$new_file_name);        
                $aggreement_filePath = url('/')."/assets/uploads/lab_certificates/".$new_file_name;
                //array_push($img_path,$aggreement_filePath);
            }
           
            if($other_file_path!=null)
            {
                $filePath = json_encode($other_file_path);
            }
            else
            {
                $filePath = json_encode(array());
            }

            //other certificate files
            if(isset($_POST['other_certificate_name']))
            {
                $other_certificates = $_POST['other_certificate_name'];
                for ($i=0; $i <count($other_certificates); $i++) 
                { 
                    $count = count($other_file_path);
                    $name = $_POST['other_certificate_name'][$i];
                    //certifiactes
                    $reference_image = $_FILES['other_certificate_file']['name'][$i] ;    
                    $targetDir = "assets/uploads/lab_certificates/";
                    $fileName = basename($_FILES['other_certificate_file']['name'][$i]);
                    $targetFilePath = $targetDir . $fileName;
                    $fileType =strtolower(pathinfo($targetFilePath,PATHINFO_EXTENSION));
                    $new_file_name = $lab_name."-".$name.".".$fileType;
                    move_uploaded_file($_FILES["other_certificate_file"]["tmp_name"][$i], $targetDir.$new_file_name);        
                    $filePath = url('/')."/assets/uploads/lab_certificates/".$new_file_name;
                    $other_file_path[$count]['name'] = $name;
                    $other_file_path[$count]['path'] = $filePath;
                }
            }
            $other_file_path_json = json_encode($other_file_path);

            $update_Lab = [
                'lab_name'=>$request->get('lab_name'),
                'line_1'=>$request->get('line_1'),
                'line_2'=>$request->get('line_2'),
                'landmark'=>$request->get('landmark'),
                'area'=>$request->get('area'),
                'location'=>$request->get('location'),
                'pincode'=>$request->get('pincode'),
                'lab_email'=>$request->get('email'),
                'state'=>$request->get('state'),
                'country'=>$request->get('country'),
                'aggreement'=>json_encode($aggreement_filePath),
                'other_certificates'=>$other_file_path_json,
                'person1_name'=>$request->get('person1_name'),
                'person1_contact'=>$request->get('person1_contact'),
                'person1_email'=>$request->get('person1_email'),
                'other_contact_persons'=>$other_contact_persons,
                'created_by'=>session('username')
            ];
            $Lab->where('id',$id)->update($update_Lab);
            return redirect()->back()->with('message','Lab updated Successfully');


        }

        //delete lab_name
        public function Deletelab($id)
        {
            $Lab = new Labs();
            $Customers = new LabTestCustomers();
            $Lab->where('id',$id)->delete();
            return redirect()->back()->with('message_delete','Lab deleted Successfully');
        }

        public function CreateLead(Request $request)
        {
            if($_SERVER['REQUEST_METHOD']=='GET')
            {
                $get_lab_test = DB::select("SELECT * FROM lab_test_list");
                $data['get_lab_test'] = json_decode(json_encode($get_lab_test),true);
                $get_labs = DB::select("SELECT id,lab_name FROM labs");
                $data['get_labs'] = json_decode(json_encode($get_labs),true);
                $get_countries = DB::select("SELECT * FROM countries");
                $data['get_countries'] = json_decode(json_encode($get_countries),true);
                $get_states = DB::select("SELECT * FROM states");
                $data['get_states'] = json_decode(json_encode($get_states),true);
                $get_cities = DB::select("SELECT * FROM cities");
                $data['get_cities'] = json_decode(json_encode($get_cities),true);
                return view('LabTestViews.create_lead',$data);
            }
            if($_SERVER['REQUEST_METHOD']=='POST')
            {
                //print_r($_POST);
                $contact_no = $_POST['contact_no'];
                $name =$_POST['patient_name'];
                $location = $_POST['location'];
                $AmbulanceLeads = new AmbulanceLeads();
                $LabTestLeads = new LabTestLeads();
                $NursingCareLeads = new NursingCareLeads();
                $LabCustomers = new LabTestCustomers();
               
                $email_id = $_POST['email'];
                $visit_date = $_POST['visit_date'];
                $visit_time = $_POST['visit_time'];
                $test_name = $_POST['test_name'];
                $lab_name = $_POST['lab_name'];
                //prescription_image
                $img_path =array();
                if($_FILES['prescription_img']['name'][0]!=null)
                {
                    $count = count($_FILES['prescription_img']['name']);
                    for ($i=0; $i <$count ; $i++) { 
                        $reference_image = $_FILES['prescription_img']['name'][$i];    
                        $targetDir = "assets/uploads/prescription_images/";
                        $fileName = basename($_FILES['prescription_img']['name'][$i]);
                        $targetFilePath = $targetDir . $fileName;
                        $fileType =strtolower(pathinfo($targetFilePath,PATHINFO_EXTENSION));
                        $new_file_name = $targetDir."".$contact_no."-".date("Y-m-d")."-$i".".".$fileType;
                        move_uploaded_file($_FILES["prescription_img"]["tmp_name"][$i], $new_file_name);        
                        $filePath = url('/')."/assets/uploads/payment_images/".$new_file_name;
                        array_push($img_path,$filePath);
                    }
                }
                if($img_path!=null)
                {
                    $img_path_json = json_encode($img_path);
                }
                else
                {
                    $img_path_json = null;
                }
                
                $price = $_POST['customer_price'];
                $comments = $_POST['comment'];
                //insert in cutsomer of lab
                $insert_customer = [
                    'customer_name'=>$name,
                    'contact_no'=>$contact_no,
                    'line_1'=>$request->get('line_1'),
                    'line_2'=>$request->get('line_2'),
                    'landmark'=>$request->get('landmark'),
                    'area'=>$request->get('area'),
                    'location'=>$request->get('location'),
                    'city'=>$request->get('city'),
                    'pincode'=>$request->get('pincode'),
                    'email'=>$request->get('email'),
                    'state'=>$request->get('state'),
                    'country'=>$request->get('country'),
                    'created_by'=>session('username')
                ];
                $customer_id = $LabCustomers->insertGetId($insert_customer);
                //insert in lab
                $insert_Lab = [
                    'cust_id'=>$customer_id,
                    'created_date'=>date('Y-m-d'),
                    'visit_date'=>$visit_date,
                    'visit_time'=>$visit_time,
                    'test_id'=>json_encode($test_name),
                    'prescription_image'=>$img_path_json,
                    'lab_name'=>$lab_name,
                    'customer_price'=>$price,
                    'comments'=>$comments,
                    'lead_platform'=>'Web',
                    'lead_source'=>$request->get('lead_source'),
                    'reffered_by'=>$request->get('reffered_by'),
                    'created_by'=>session('user_id')
                ];
                $LabTestLeads->insert($insert_Lab);
                return redirect('/view_all_lab_test_leads')->with('message','Lead Inserted Successfully');
            }
        }


        //view all lab test lead
        public function ViewAllLabTestLeads(Request $request) {

            $get_min_date = LabTestLeads::min('created_date');
            $get_max_date = LabTestLeads::max('created_date');
            //all labs name
            $get_lab_names = DB::table('labs')->get(['id','lab_name'])->toArray();
            //get all user
            $get_all_users = DB::table('user')->where('role','=','user')->get(['id','username'])->toArray();
            //get customer location
            $get_all_location = DB::table('lab_test_customers')->select('location')->distinct('location')->get()->toArray();
            $match_clause = array();
            $whereCondition = [];
            $customer_name = $request->get('filter_customer_name');
            if(isset($customer_name)){
                $whereCondition1 = ['lab_test_customers.customer_name','LIKE','%'.$customer_name.'%'];
                array_push($whereCondition,$whereCondition1);
            }
            $customer_contact = $request->get('filter_contact_no');
            if(isset($customer_contact)) {
                $whereCondition2 = ['lab_test_customers.contact_no','=',$customer_contact];
                array_push($whereCondition,$whereCondition2);
            }
            $lead_status = $request->get('filter_lead_status');
            if(isset($lead_status) && ($lead_status=='Work In Process' || $lead_status=='Converted' || $lead_status=='Closed' )){
                $whereCondition3 = ['lab_test_leads.status','=',$lead_status];
                array_push($whereCondition,$whereCondition3);
            }
            $location = $request->get('filter_customer_location');
            if(isset($location) && in_array($location,array_column($get_all_location,'location'))){
                $whereCondition4 = ['lab_test_customers.location','=',$location];
                array_push($whereCondition,$whereCondition4);
            }
            $lab_id = $request->get('filter_lab_name');
            if(isset($lab_id) && in_array($lab_id,array_column($get_lab_names,'id'))){
                $whereCondition5 = ['lab_test_leads.lab_name','=',$lab_id];
                array_push($whereCondition,$whereCondition5);
            }
            $lead_owner = $request->get('filter_lead_owner');
            if(isset($lead_owner) && in_array($lead_owner,$get_all_users)){
                $whereCondition7 = ['lab_test_leads.lead_owner','=',$lead_owner];
                array_push($whereCondition,$whereCondition7);
            }
            if(session('role')=='user')
            {
                $whereCondition8 = ['leads.lead_owner','=',session('user_id')];
                array_push($whereCondition,$whereCondition8);
            }
           
            $from_date = $request->get('filter_from_date');
            $end_date = $request->get('filter_end_date');
            if(isset($from_date) && isset($end_date)){
                $get_min_date = $from_date;
                $get_max_date = $end_date;
            }
            
            $get_all_leads = DB::table('lab_test_leads')
                                ->join('lab_test_customers','lab_test_leads.cust_id','=','lab_test_customers.id')
                                ->join('labs','lab_test_leads.lab_name','=','labs.id')
                                ->join('user','lab_test_leads.created_by','=','user.id')
                                ->select('lab_test_leads.*','lab_test_leads.id as lead_id','lab_test_customers.*','user.username as lead_owner_name','labs.lab_name as l_name')
                                ->where($whereCondition)
                                ->whereBetween('lab_test_leads.created_date',[$get_min_date,$get_max_date])
                                ->orderBy('lab_test_leads.id','DESC')
                                ->orderBy('lab_test_leads.created_date','DESC')
                                ->paginate(10);
            $all_leads_arr = json_decode(json_encode($get_all_leads->toArray()),true);
            foreach($all_leads_arr['data'] as $key=> $lead)            
            {
                $test_id = json_decode($lead['test_id'],true);
                $test_id_impl = implode(',',$test_id);
                $get_test_names = DB::select("SELECT test_name FROM lab_test_list WHERE id IN($test_id_impl)");
                $get_test_names = implode(",",array_column(json_decode(json_encode($get_test_names),true),'test_name'));
                $all_leads_arr['data'][$key]['test_names']=$get_test_names;
            }
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
                "lab_id"=>$lab_id,
                "lead_owner"=>$lead_owner,
                "from_date"=>$from_date,
                "end_date"=>$end_date,
                "filter_collapse_cookie"=>$filter_collapse_cookie];
            return view('LabTestViews.view_all_leads',compact('get_all_leads','get_lab_names','get_all_location','get_all_users','all_leads_arr','filter_arr'));
        }
        public function ConvertLead(Request $request)
        {
            $LabLead = new LabTestLeads();
            $lead_id = $request->get('lead_id');
            $LabLead->where('id',$lead_id)->update(['status'=>'Converted']);
            return redirect()->back();
        }
        public function CloseLead(Request $request)
        {
            $LabLead = new LabTestLeads();
            $lead_id = $request->get('lead_id');
            $reason = $request->get('reason');
            $LabLead->where('id',$lead_id)->update(['status'=>'Closed','comment'=>$reason]);
            return redirect()->back();
        }

        //customer populate of labs lead
        public function CustomerPopulate(Request $request)
        {
            $query = $request->get('query');
            $filterResult = DB::select("SELECT customer_name FROM lab_test_customers WHERE customer_name LIKE '%$query%' ");
            $filterResult = json_decode(json_encode($filterResult),true);
            $data = array();
            foreach($filterResult as $key => $result)
            {
                $data[] = $result['customer_name'];
            }
            return response()->json($data);
        }
    }
?>