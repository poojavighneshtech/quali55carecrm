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
    use App\Models\ShortUrl;
    use App\Http\Controllers\AppApiV2\OrdersController;
    use Mail;

    class OtherLeadController extends Controller
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
                $get_cities = DB::select("SELECT * FROM cities");
                $data['get_cities'] = json_decode(json_encode($get_cities),true);
                return view('OtherLeadViews.create_lead',$data);
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
                if($_POST['submit']=='lab_test_data')
                {
                    $email_id = $_POST['email_id'];
                    $visit_date = $_POST['visit_date'];
                    $visit_time = $_POST['visit_time'];
                    $test_name = $_POST['test_name'];
                    $lab_name = $_POST['lab_name'];
                    $blood_collection_address = $_POST['blood_collection_address'];
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
                            $filePath = "/devweb/eflow/assets/uploads/payment_images/".$new_file_name;
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
                    $insert_Lab = [
                        'name'=>$name,
                        'contact_no'=>$contact_no,
                        'email_id'=>$email_id,
                        'location'=>$location,
                        'created_date'=>date('Y-m-d'),
                        'visit_date'=>$visit_date,
                        'visit_time'=>$visit_time,
                        'test_id'=>$test_name,
                        'prescription_image'=>$img_path_json,
                        'lab_name'=>$lab_name,
                        'blood_collection_address'=>$blood_collection_address,
                        'customer_price'=>$price,
                        'comments'=>$comments,
                        'lead_platform'=>'Web',
                        'created_by'=>session('user_id')
                    ];
                    $LabTestLeads->insert($insert_Lab);
                    return redirect()->back()->with('message','Lead Inserted Successfully');
                    
                }
                elseif($_POST['submit']=='ambulance_data')
                {
                    $pickup_location = $_POST['pickup_location'];
                    $drop_location = $_POST['drop_location'];
                    $ambulance_type = $_POST['ambulance_type'];
                    $insert_Amb = [
                        'patient_name'=>$name,
                        'contact_no'=>$contact_no,
                        'service_from'=>$pickup_location,
                        'service_to'=>$drop_location,
                        'ambulance_type'=>$ambulance_type,
                        'created_by'=>session('user_id'),
                        'lead_platform'=>'Web'
                    ];
                    $AmbulanceLeads->insert($insert_Amb);
                    return redirect()->back()->with('message','Lead Inserted Successfully');
                }
                elseif($_POST['submit']=='nursing_data')
                {   
                    $service_rquirement = $_POST['service_rquirement'];
                    $address = $_POST['address'];
                    $email_id = $_POST['email_id'];
                    $insert_Nursing=[
                        'name'=>$name,
                        'contact_no'=>$contact_no,
                        'email_id'=>$email_id,
                        'location'=>$location,
                        'service_required'=>$service_rquirement,
                        'address'=>$address,
                        'lead_platform'=>'Web',
                        'created_date'=>date('Y-m-d'),
                        'created_by'=>session('user_id')
                    ];
                    $NursingCareLeads->insert($insert_Nursing);
                    return redirect()->back()->with('message','Lead Inserted Successfully');
                }
            }
        }

        //view all lab test lead
        public function ViewAllLabTestLeads() {
            $get_all_leads = DB::select("SELECT 
                                                lab_test_leads.*,
                                                lab_test_list.test_name as test_name,
                                                user.username as username
                                            FROM 
                                                lab_test_leads,
                                                lab_test_list,
                                                user
                                            WHERE 
                                                lab_test_leads.test_id = lab_test_list.id
                                                AND lab_test_leads.created_by = user.id ");
            $data['get_all_leads'] = json_decode(json_encode($get_all_leads),true);
            return view('OtherLeadViews.view_all_lab_test_leads',$data);
        }
        //view all lab test lead
        public function ViewAmbulanceLeads() {
            $get_all_leads = DB::select("SELECT 
                                                amb_user_data.*,
                                                user.username as username
                                            FROM 
                                                amb_user_data,
                                                user
                                            WHERE 
                                                amb_user_data.created_by = user.id ");
            $data['get_all_leads'] = json_decode(json_encode($get_all_leads),true);
            return view('OtherLeadViews.view_all_ambulance_leads',$data);
        }
        //view Nursing care lead
        public function ViewNursingCareLeads() {
            $get_all_leads = DB::select("SELECT 
                                                nursing_care_leads.*,
                                                user.username as username
                                            FROM 
                                                nursing_care_leads,
                                                user
                                            WHERE 
                                                nursing_care_leads.created_by = user.id ");
            $data['get_all_leads'] = json_decode(json_encode($get_all_leads),true);
            return view('OtherLeadViews.view_all_nursing_care_leads',$data);
        }
        
        // Consumables and patient support products
        public function consumableForm($order,$contact){
            // dd($order,$contact);
            $products = [
                'Diapers',
                'Hand gloves',
                'Bed wipes',
                'Rubber sheets',
                'Urine pot',
                'Urine bag',
                'Under pads',
                'Orto product tynor',
                'Oxygen mask',
                'Oxygen cannula',
                'Bipap mask',
                'Sentizer',
                'Room freshner',
                'Commode chair',
            ];
            $link = route('consumables-form',[$order,$contact]);
            if(DB::table('dump_data_advertise_form')->where('link',$link)->exists()){
                $virtual_no = config('app.virtual_no');
                return view('OtherLeadViews.consumable-form-already-submitted',compact('virtual_no'));
            }else{
                return view('OtherLeadViews.consumable-form',compact('order','contact','products'));
            }
        }

        public function consumableFormSubmit(Request $request){
            // dd($request->all());
            DB::beginTransaction();
            try{
                $ordersController = new OrdersController();
                $order =$ordersController->decryptData($request->get('orderid'),"AES-128-CTR","consumableQuali55care"); 
                $contact =$ordersController->decryptData($request->get('contactno'),"AES-128-CTR","consumableQuali55care"); 
                if($request->get('products')){
                    $id = DB::table('dump_data_advertise_form')->insertGetId([
                        "order_id"=>$order,
                        "customer_contact"=>$contact,
                        "source"=>"consumables-form",
                        "link"=>$request->get('prevurl'),
                        "data_json"=>json_encode($request->get('products')),
                    ]);
                    $this->sendConsFormSubWpMsg($id);
                    DB::commit();
                    return redirect()->back()->with('message','Data Submited');
                }else{
                    return redirect()->back()->with('error','Select atlest one product');
                }
            }catch(Exception $ex){
                DB::rollback();
                return redirect()->back()->with('error','Something went wrong, Try again!');
            }
        }
        public function sendConsFormSubWpMsg($id){
            $record = DB::table('dump_data_advertise_form')->where('id',$id)->first();
            $order_record = DB::table('del_orders')->where('order_id',$record->order_id)->first();
            $submission_date = date('d-M-y',strtotime($record->created_at));
            $product_list = implode(', ',json_decode($record->data_json));
            $product_count = count(json_decode($record->data_json));
            $contacts = array();
            if(config('app.app_env') == 'devweb'){
                array_push($contacts,config('app.developer_contact'));
            }else{
                $contacts = config('app.developer_contacts');
                array_push($contacts,config('app.business_head_contact'));

            }
            foreach($contacts as $contact){
                // Curl send wp message
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
                    "templatename" => "usersite_orders",
                    "templateparams" => [
                        ["type"=> "text","text"=> "Consumables"],
                        ["type"=> "text","text"=> "$submission_date"],
                        ["type"=> "text","text"=> "-"],
                        ["type"=> "text","text"=> "$order_record->shipping_first_name"],
                        ["type"=> "text","text"=> "$order_record->mobileno"],
                        ["type"=> "text","text"=> "$order_record->fulldetails"],
                        ["type"=> "text","text"=> "$product_list"],
                        ["type"=> "text","text"=> "$product_count"],
                        ["type"=> "text","text"=> "-"],
                        ["type"=> "text","text"=> "-"],
                    ],
                ];
                //return $data;
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                $resp = curl_exec($curl);
                curl_close($curl);
            }
            
        }
        public function ShortUrl($id)
        {
            if(ShortUrl::where('url_link_id',$id)->exists())
            {
                $get_link = ShortUrl::where('url_link_id',$id)->first();
                return redirect($get_link->full_url);
            }
            else{
                return "Link Expired";
            }
        }
    }
?>