<?php

    namespace App\Http\Controllers\ComplaintManagement;

    use Illuminate\Support\Facades\Validator;
    use Illuminate\Support\Facades\DB;
    use App\Models\VendorRegister;
    use App\Models\UserRegister;
    use App\Models\DelOrders;
    use App\Models\lead;
    use App\Models\OrderDetails;
    use App\Models\customer_detail;
    use App\Models\VendorProducts;
    use App\Models\leads_log;
    use App\Models\sale_vendor_products;
    use App\Models\VendorRentedProducts;
    use App\Models\Complaints;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;
    use App\Http\Controllers\Controller;
    use Mail;

    class ComplaintController extends Controller
    {
        public function isLoggedIn()
        {
            $data = session('isLoggedIn');
            return $data;
        }
        public function check_generated_id()
        {
            $complaint_id = random_int(100000, 999999);
            $check = DB::select("SELECT generated_complaint_id FROM complaints WHERE generated_complaint_id=$complaint_id");
            $check = json_decode(json_encode($check), true);
            if(isset($check[0]['generated_complaint_id']))
            {
                $this->check_generated_id();
            }
            else
            {
                return $complaint_id;
            }
        }
        public function RaiseComplaint()
        {
            if($_SERVER['REQUEST_METHOD'] == 'GET')
            {
                if(session('role')=='user')
                {
                    $get_all_complaints = Complaints::join('customer_details','complaints.customer_id','=','customer_details.cust_id')
                                            ->join('user','complaints.created_by_id','=','user.id')
                                            ->select('complaints.*','customer_details.*','user.username')
                                            ->where('complaints.created_by_id','=',session('user_id'))
                                            ->orderBy('complaint_date','DESC')
                                            ->paginate(10);
                }
                else{
                    $get_all_complaints = Complaints::join('customer_details','complaints.customer_id','=','customer_details.cust_id')
                                                ->join('user','complaints.created_by_id','=','user.id')
                                                ->select('complaints.*','customer_details.*','user.username')
                                                ->when(session('city_based_access') == '1',function($query){
                                                    $query->where('customer_details.citygroup',session('user_city'));
                                                })
                                                ->orderBy('complaint_date','DESC')
                                                ->paginate(10);
                }
               //dd($get_all_complaints);
                return view('ComplaintManagement/Complaint_Mgmt',compact('get_all_complaints'));
            }
        }
        //complaint filter
        public function ComplaintFilter(Request $request)
        {
            $match_clause = array();
            $whereCondition = [];
            $customer_name = $request->get('filter_customer_name');
            if(isset($customer_name)){
                $whereCondition1 = ['customer_details.customer_name','LIKE','%'.$customer_name.'%'];
                array_push($whereCondition,$whereCondition1);
            }
            $complaint_id = $request->get('filter_complaint_id');
            if(isset($complaint_id)){
                $whereCondition2 = ['complaints.generated_complaint_id','=',$complaint_id];
                array_push($whereCondition,$whereCondition2);
            }
            $complaint_status = $request->get('filter_complaint_status');
            if(isset($complaint_status) && ($complaint_status=='Open' || $complaint_status=='Closed')){
                $whereCondition3 = ['complaints.status','=',$complaint_status];
                array_push($whereCondition,$whereCondition3);
            }
            if(session('role')=='user')
            {
                $whereCondition4 = ['complaints.created_by_id','=',session('user_id')];
                array_push($whereCondition,$whereCondition4);
            }
            $from_date = $request->get('filter_from_date');
            $end_date = $request->get('filter_end_date');
            if(isset($from_date) && isset($end_date)){
                $get_all_complaints =DB::table('complaints')
                                    ->join('customer_details','complaints.customer_id','=','customer_details.cust_id')
                                    ->join('user','complaints.created_by_id','=','user.id')
                                    ->select('complaints.*','customer_details.*','user.username')
                                    ->where($whereCondition)
                                    ->when(session('city_based_access') == '1',function($query){
                                        $query->where('customer_details.citygroup',session('user_city'));
                                    })
                                    // ->Where('complaints.generated_complaint_id','',$complaint_id)
                                    // ->Where('complaints.status','=',$complaint_status)
                                    ->WhereBetween('complaints.complaint_date',[$from_date,$end_date])
                                    ->orderBy('complaint_date','DESC')
                                    ->paginate(10);
            }
            else
            {
                $get_all_complaints =DB::table('complaints')
                                    ->join('customer_details','complaints.customer_id','=','customer_details.cust_id')
                                    ->join('user','complaints.created_by_id','=','user.id')
                                    ->select('complaints.*','customer_details.*','user.username')
                                    ->where($whereCondition)
                                    ->when(session('city_based_access') == '1',function($query){
                                        $query->where('customer_details.citygroup',session('user_city'));
                                    })
                                    // ->Where('complaints.generated_complaint_id','=',$complaint_id)
                                    // ->Where('complaints.status','=',$complaint_status)
                                    ->orderBy('complaint_date','DESC')
                                    ->paginate(10);
            }
            $filter_arr = ["cust_name"=>$customer_name,"cmp_id"=>$complaint_id,"cmp_status"=>$complaint_status,"from_date"=>$from_date,"end_date"=>$end_date];
            
            return view('ComplaintManagement/Complaint_Mgmt',compact('get_all_complaints'),compact('filter_arr'));
        }

        //customer popoulate
        public function CustomerPopulate(Request $request)
        {
            // if($customer_val!=null)
            // {
            //     $get_customers_list = DB::select("SELECT customer_name FROM customer_details WHERE customer_name LIKE '%$customer_val%' ");
            //     $get_customers_list = json_decode(json_encode($get_customers_list),true);
            //     return json_encode($get_customers_list);
            // }
            // else{
            //     return false;
            // }
            $query = $request->get('query');
            $filterResult = DB::select("SELECT customer_name FROM customer_details WHERE customer_name LIKE '%$query%' ");
            $filterResult = json_decode(json_encode($filterResult),true);
            $data = array();
            foreach($filterResult as $key => $result)
            {
                $data[] = $result['customer_name'];
            }
            return response()->json($data);
        }

        //delete complaint
        public function DeleteComplaint(Request $request)
        {
            $id = $request->get('complaint_tbl_id');
            $Complaints = new Complaints();
            $Complaints->where('id',$id)->delete();
            return redirect()->back()->with('message_delete','complaint_deleted successfully');
        }
        //create a new complaint 
        public function CreateComplaint(Request $request,Response $response)
        {   
            if($_SERVER['REQUEST_METHOD']=='GET')
            {
                return view('ComplaintManagement/create_complaint');
            }
            elseif($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $Complaints = new Complaints();
                $order_details_id = $_POST['order_details_id'];
                $customer_id = $_POST['customer_id'];
                $product_id = $_POST['product_id'];
                $vendor_id = $_POST['vendor_id'];
                $lead_id = $_POST['lead_id'];
                $delivered_by = $_POST['DelAssignedTo'];
                $lead_owner = $_POST['username'];
                $remarks = $_POST['remarks'];
                $complaint_id = $this->check_generated_id();

                //-----------------------Image Upload update by id -------------------------------//
                $complaint_image_path = null;
                if($_FILES['complaint_img']['name'][0]!=NULL)
                {
                    $complaint_img = $_FILES['complaint_img']['name'];
                    //print_r($_FILES['shop_images']['name']);
                    $img_path = array();
                    $count = count($complaint_img);
                    for ($i=0; $i <$count; $i++) { 
                        // echo $i;
                        $targetDir = "assets/uploads/complaint_images/";
                        $fileName = basename($_FILES['complaint_img']['name'][$i]);
                        $targetFilePath = $targetDir . $fileName;
                        $fileType =strtolower(pathinfo($targetFilePath,PATHINFO_EXTENSION));
                        $new_file_name = $targetDir."".$complaint_id."-".$i.".".$fileType;
                        move_uploaded_file($_FILES["complaint_img"]["tmp_name"][$i], $new_file_name);    
                        $filePath = url('/')."/assets/uploads/complaint_images/".$complaint_id."-".$i.".".$fileType;                    
                        array_push($img_path,$filePath);
                    } 
                    $complaint_image_path= json_encode($img_path);
                }
                
                //customer_details
                $get_cust_data = DB::SELECT("SELECT * FROM customer_details where cust_id='$customer_id' ");
                $get_cust_data = json_decode(json_encode($get_cust_data),true);
                $customer_name = $get_cust_data[0]['customer_name'];
                $customer_contact_no = $get_cust_data[0]['primary_contact_no'];
                $email_data = array();
                for ($i=0; $i <count($order_details_id); $i++) 
                { 
                    $complaint_data = [
                        'generated_complaint_id'=>$complaint_id,
                        'image'=>$complaint_image_path,
                        'customer_id'=>$customer_id,
                        'order_details_id'=>$order_details_id[$i],
                        'product_id'=>$product_id[$i],
                        'vendor_id'=>$vendor_id[$i],
                        'lead_id'=>$lead_id[$i],
                        'delivered_by'=>$delivered_by[$i],
                        'lead_owner'=>$lead_owner[$i],
                        'lead_owner_id'=>session('user_id'),
                        'remarks'=>$remarks,
                        'status'=>'Open',
                        'complaint_date'=>date('Y-m-d'),
                        'created_by'=>session('username'),
                        'created_by_id'=>session('user_id')
                    ];
                    $Complaints->insert($complaint_data);
                    //get product name
                    $p_id = $product_id[$i];
                    $get_product_name = DB::SELECT("SELECT product_name FROM products WHERE id=$p_id");
                    $get_product_name = json_decode(json_encode($get_product_name),true);
                    $product_name = $get_product_name[0]['product_name'];
                    //vendor_name
                    $v_id = $vendor_id[$i];
                    $get_vendor_name = DB::SELECT("SELECT registered_name FROM vendor_details WHERE id=$v_id");
                    $get_vendor_name = json_decode(json_encode($get_vendor_name),true);
                    $vendor_name = $get_vendor_name[0]['registered_name'];

                    //getOrder id
                    $orderId = DB::table('order_details')->select('order_id')->where('id',$order_details_id[$i])->first('order_id');
                    //delivered  by
                    $del_boy = $delivered_by[$i];
                    //lead owner
                    $lead_owner = $lead_owner[$i];
                    //email data
                    $email_data['order_id'][$i] = $orderId->order_id;
                    $email_data['product_name'][$i] = $product_name;
                    $email_data['vendor_name'][$i] = $vendor_name;
                    $email_data['delivered_by'][$i] = $del_boy;
                    $email_data['complaint_id'][$i] = $complaint_id;
                    $email_data['lead_owner'][$i] = $lead_owner;
                }
                
                //send mail get emails
                //  $user_id = session('user_id');
                //  $get_sender_email = DB::select("SELECT email_id_user FROM user WHERE id=$user_id");
                //  $get_sender_email = json_decode(json_encode($get_sender_email),true);
                //  $sender_mail = $get_sender_email[0]['email_id_user'];


                //  //reciever mail id
                //  $get_reciever_email = DB::select("SELECT email_id_user FROM user WHERE username='abhishek' OR username='admin' ");
                //  $get_reciever_email = json_decode(json_encode($get_reciever_email),true);
                //  $reciever_email = array_column($get_reciever_email,'email_id_user');
                //  dd($reciever_email);
                
                //sender_mail 
                $senderMail = DB::table('user')->select('username','email_id_user','contact_no')->where('id',session('user_id'))->first('username','email_id_user','contact_no');
                //get customer email
                $customerMail = DB::table('customer_details')->where('cust_id', $customer_id)->select('email_id')->first('email_id');
                //get ceo mail and business head mail
                $ceoMail = config('app.ceo_email');
                $businessHeadMail = config('app.business_head_email');

                $data_message = array(
                    'customer_name'=>$customer_name,
                    'customer_contact_no'=>$customer_contact_no,
                    'complaint_date'=>date('Y-m-d'),
                    'remarks'=>$remarks,
                    'raised_by'=>session('username'),
                    'raised_contact_no'=>$senderMail->contact_no
                 );
                // 'mail_data'=>"'".$mail_data."'");
                $data_message['mail_data'] = $email_data;

                //Send mail to internal
                Mail::send('ComplaintManagement/send_complaint_mail',$data_message, function($message) use ($senderMail,$ceoMail,$businessHeadMail,$customerMail)
                {     
                    // dd($senderMail);
                    //$message->to($ceoMail, 'Complaint Raised')->subject('Complaint Raised');
                    $message->to($businessHeadMail, 'Complaint Raised')->subject('Complaint Raised');
                    $message->to($senderMail->email_id_user, 'Complaint Raised')->subject('Complaint Raised');
                    $message->from('tempmailquali@gmail.com', 'Quali55Care');
                });

                //send mail to customer
                if(isset($customerMail->email_id)){
                    
                    Mail::send('ComplaintManagement/send_customer_mail',$data_message, function($message) use ($customerMail)
                    {     
                        //dd($customerMail);
                        $message->to($customerMail->email_id, 'Complaint Raised')->subject('Complaint Raised');
                        $message->from('tempmailquali@gmail.com', 'Quali55Care');
                    });
                }
                return redirect()->back()->with('message','Complaint add successfully');
            }
        }
        //get customers list
        public function GetCustOrComplaint(Request $request)
        {
           
            $cust_val = $request->get('name');
            // $get_customers_list = DB::select("SELECT
            //                                         customer_details.*,leads.patient_name as patient_name 
            //                                     FROM 
            //                                         customer_details,leads
            //                                     WHERE
            //                                     customer_details.cust_id = leads.customer_id AND
            //                                         (customer_details.customer_name LIKE '$cust_val%'
            //                                         OR customer_details.primary_contact_no LIKE '$cust_val%') OR  leads.patient_name LIKE '$cust_val%' AND (leads.lead_status='Converted' OR leads.lead_status='Order Generated')");

            $get_customers_list = DB::table('customer_details')
                                        ->join('leads','customer_details.cust_id','=','leads.customer_id')
                                        ->select('customer_details.*','leads.patient_name')
                                        // ->where([['customer_details.customer_name','LIKE','%'.$cust_val.'%'],['customer_details.primary_contact_no','LIKE','%'.$cust_val.'%'],['leads.patient_name','LIKE','%'.$cust_val.'%']])
                                        ->where([['customer_details.customer_name','LIKE','%'.$cust_val.'%']])
                                        ->orWhere([['customer_details.primary_contact_no','LIKE',$cust_val.'%']])
                                        ->orWhere([['leads.patient_name','LIKE',$cust_val.'%']])
                                        ->when(session('city_based_access') == '1',function($query){
                                            $query->where('customer_details.citygroup',session('user_city'));
                                        })
                                        ->get()
                                        ->paginate(10);
            // dd($get_customers_list);
            return view('ComplaintManagement/create_complaint',compact('get_customers_list'));
        }

        //selected customer details
        public function GetCustomer($customer_id)
        {
            $orderTypeNotIn = config('app.order_type');        
            $orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";

            $get_customer_details = Db::select("SELECT * FROM customer_details WHERE cust_id = $customer_id");
            $data['customer_details'] = json_decode(json_encode($get_customer_details),true);

            $get_live_products = DB::select("SELECT del_orders.*,
                                                order_details.*,
                                                order_details.id as  order_details_id,
                                                products.product_name as product_name,
                                                vendor_details.registered_name as vendor_name
                                            FROM 
                                                customer_details,del_orders,order_details,products,vendor_details
                                            WHERE 
                                                customer_details.cust_id = $customer_id
                                                AND del_orders.mobileno = customer_details.primary_contact_no 
                                                AND del_orders.order_id = order_details.order_id
                                                AND order_details.product_id = products.id
                                                AND order_details.current_status !='Picked Up'
                                                AND order_details.current_status !='Pending Pickup' 
                                                -- AND order_details.current_status !='Pending Renew' 
                                                AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)
                                                AND order_details.vendor_id = vendor_details.id");

            $data['get_live_products'] = json_decode(json_encode($get_live_products),true);
            return view('ComplaintManagement/create_complaint',$data);
            //print_r($data);
                //get customer_id
                // //search complaint ids in complaints
                // $get_complaint_ids = DB::select("SELECT generated_complaint_id,complaint_date FROM complaints where customer_id=$get_cust_id");
                // $get_complaint_ids = json_decode(json_encode($get_complaint_ids),true);
                // $data = array();
                // array_push($data,$get_cust_data);
                // array_push($data,$get_complaint_ids);
                // $data = json_encode($data);
          
        }

        public function GetComplaintData($btn_val,$contact_no) 
        {
            $orderTypeNotIn = config('app.order_type');        
            $orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";
            if($btn_val=='btn_customer')
            {
                $get_cust_data = DB::select("SELECT customer_details.*,     
                                                del_orders.order_id as order_id,
                                                del_orders.DelAssignedTo as DelAssignedTo,
                                                del_orders.helpers as helpers,
                                                order_details.*,
                                                products.product_name as product_name,
                                                vendor_details.registered_name as vendor_name
                                        FROM 
                                            customer_details,del_orders,order_details,products,vendor_details
                                        WHERE 
                                            (customer_details.primary_contact_no='$contact_no'
                                            OR customer_details.customer_name='$contact_no')
                                            AND del_orders.mobileno = customer_details.primary_contact_no 
                                            AND del_orders.order_id = order_details.order_id
                                            AND order_details.product_id = products.id
                                            AND order_details.current_status !='Picked Up'
                                            AND order_details.current_status !='Pending Pickup' 
                                            AND order_details.current_status !='Pending Renew' 
                                            AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)
                                            AND order_details.vendor_id = vendor_details.id");
                $get_cust_data = json_decode(json_encode($get_cust_data),true);
                //get customer_id
                $get_cust_id = $get_cust_data[0]['cust_id'];
                //search complaint ids in complaints
                $get_complaint_ids = DB::select("SELECT generated_complaint_id,complaint_date FROM complaints where customer_id=$get_cust_id");
                $get_complaint_ids = json_decode(json_encode($get_complaint_ids),true);
                $data = array();
                array_push($data,$get_cust_data);
                array_push($data,$get_complaint_ids);
                $data = json_encode($data);
                return $data;
               
            }
            elseif($btn_val=='btn_complaint_id') 
            {
                $get_cust_data = DB::select("SELECT 
                                                customer_details.*,
                                                complaints.*,
                                                products.product_name as product_name,
                                                del_orders.DelDate as DelDate,
                                                vendor_details.registered_name as vendor_name
                                            FROM 
                                                complaints,customer_details,order_details,products,vendor_details,del_orders
                                            WHERE 
                                                complaints.generated_complaint_id=$contact_no
                                                AND complaints.customer_id = customer_details.cust_id
                                                AND complaints.product_id = products.id
                                                AND order_details.id = complaints.order_details_id
                                                AND vendor_details.id = complaints.vendor_id
                                                AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)
                                                AND del_orders.order_id = order_details.order_id");
                $jsonstring = json_encode($get_cust_data);
                return $jsonstring;
            }
            
        }

        public function ProductDetails()
        {
            $products = json_decode($_POST['product']);
            
            for ($i=0; $i <count($products); $i++) { 
                $explode_product = explode(".", $products[$i]);
                $product_id = $explode_product[0];
                $order_details_id = $explode_product[1];

                $get_product_details = DB::select("SELECT
                                                        order_details.*,
                                                        del_orders.DelDate as DelDate,
                                                        vendor_details.registered_name as vendor_name,
                                                        vendor_details.id as vendor_id,
                                                        del_orders.DelAssignedTo as DelAssignedTo,
                                                        del_orders.helpers as helpers,
                                                        del_orders.lead_id as lead_id,
                                                        user.username as username,
                                                        products.id as product_id,
                                                        products.product_name as product_name
                                                    FROM 
                                                        order_details,leads,del_orders,user,vendor_details,products
                                                    WHERE 
                                                        order_details.id = $order_details_id
                                                    AND del_orders.order_id=order_details.order_id
                                                    AND del_orders.lead_id = leads.id
                                                    AND leads.lead_owner = user.id
                                                    AND order_details.product_id = products.id
                                                    AND order_details.vendor_id = vendor_details.id");
                $get_product_details= json_decode(json_encode($get_product_details),true);
                $product_data[$i]= $get_product_details[0];
            }

            print_r(json_encode($product_data));
        }

        //open complaints
        public function OpenComplaints() 
        {
            $get_complaints = DB::select("SELECT 
                                            complaints.*,
                                            customer_details.customer_name as customer_name
                                        FROM 
                                            complaints,products,customer_details
                                        WHERE 
                                            complaints.status='Open' 
                                        AND complaints.product_id=products.id
                                        AND complaints.customer_id=customer_details.cust_id 
                                        ORDER BY complaints.complaint_date DESC ");
            $data['get_complaints'] = json_decode(json_encode($get_complaints),true);
            //return view('ComplaintManagement/Open_Complaints',$data);
            //print_r($data['get_complaints']);
            $customer_id = array();
            $temp_complaint_date = array();
            $customer_complaints = array();
            $cust_data = array();
            //$unique_complaint_date = array_unique(array_map(function($elem){return $elem['complaint_date'];}, $data['get_complaints']));
            //$unique_customer_id = array_unique(array_map(function($elem){return $elem['customer_id'];}, $data['get_complaints']));
            
            foreach($data['get_complaints'] as $key => $get_complaint)
            {
                if(in_array($get_complaint['complaint_date'],$temp_complaint_date))
                {
                    for ($i=0; $i <count($customer_complaints); $i++) 
                    { 
                        if($customer_complaints[$i]['complaint_date'] == $get_complaint['complaint_date'])
                        {
                            $count = count($customer_complaints[$i]['customer_ids']);
                            $customer_complaints[$i]['customer_ids'][$count] = $get_complaint['customer_id'];
                            $customer_complaints[$i]['customer_names'][$count] = $get_complaint['customer_name'];
                        }
                    }
                }
                else
                {
                    array_push($temp_complaint_date,$get_complaint['complaint_date']);
                    $count = count($customer_complaints);
                    $customer_complaints[$count]['complaint_date'] = $get_complaint['complaint_date'];
                    $customer_complaints[$count]['customer_ids'][0] = $get_complaint['customer_id'];
                    $customer_complaints[$count]['customer_names'][0] = $get_complaint['customer_name'];
                    
                }
            }
            $open_complaints = array();
            for ($i=0; $i <count($customer_complaints);$i++)
            {
                $open_complaints[$i]['complaint_date']=$customer_complaints[$i]['complaint_date'];
                //array uniques
                $unique_customer_ids = array_unique($customer_complaints[$i]['customer_ids']);
                $unique_customer_names = array_unique($customer_complaints[$i]['customer_names']);
                //put arrays
                $open_complaints[$i]['customer_ids'] = $unique_customer_ids;
                $open_complaints[$i]['customer_names'] = $unique_customer_names;
                //array replace 
                //$customer_ids = array_replace($unique_customer_ids,$customer_complaints[$i]['customer_ids']);
                //print_r($unique_customer_ids);
            }
            
            $data['open_complaints'] = $open_complaints;
            return view('ComplaintManagement/Open_Complaints',$data);
           
        }

        //view datewised complaint of customer
        public function ViewOpenComplaint($customer_id,$complaint_date)
        {
            $get_complaint_detail = DB::select("SELECT 
                                                        complaints.*,
                                                        customer_details.*,
                                                        vendor_details.registered_name as vendor_name,
                                                        products.product_name as product_name            
                                                FROM 
                                                    complaints,customer_details,products,vendor_details
                                                WHERE 
                                                    complaints.customer_id='$customer_id' 
                                                    AND complaints.complaint_date='$complaint_date'
                                                    AND customer_details.cust_id=complaints.customer_id
                                                    AND vendor_details.id=complaints.vendor_id
                                                    AND products.id = complaints.product_id
                                                    AND complaints.status ='Open' ");
            $data['get_complaint_detail'] = json_decode(json_encode($get_complaint_detail),true);
            $image = array();
            foreach($data['get_complaint_detail'] as $get_img)
            {
                if(isset($get_img['image']))
                {
                    $img = json_decode($get_img['image'],true);
                    for ($i=0; $i<count($img); $i++)
                    {
                        array_push($image,$img[$i]);
                    }

                }
            }
            $data['image']= $image;
            return view('ComplaintManagement/View_Open_Complaint',$data);
        }

        //Close complaint 
        public function closeComplaint()
        {
            $Complaints = new Complaints();
            $tbl_cmp_id  = $_POST['tbl_cmp_id'];
            $solution = $_POST['solution'];
            $solution_date = $_POST['solution_date'];
            $repaired_by = $_POST['repaired_by'];
            $repaired_by = json_encode($repaired_by);
            for ($i=0; $i <count($tbl_cmp_id); $i++) { 
                $Complaints->where('id',$tbl_cmp_id[$i])->update(['status'=>'Closed','solution'=>$solution,'closed_date'=>date('Y-m-d'),'solution_date'=>$solution_date,'repaired_by'=>$repaired_by,'closed_by_id'=>session('user_id')]);
            }
            //send mail
            // $tb_id = $tbl_cmp_id[0];
            // $get_reciever_email = DB::SELECT("SELECT 
            //                                         customer_details.*,
            //                                         complaints.remarks as remarks,
            //                                         user.email_id_user as email_id
            //                                     FROM
            //                                         complaints,user,customer_details
            //                                     WHERE
            //                                         complaints.id = $tb_id
            //                                         AND complaints.lead_owner_id = user.id
            //                                         AND complaints.customer_id = customer_details.cust_id");
            // $get_reciever_email = json_decode(json_encode($get_reciever_email),true);
            // $customer_name = $get_reciever_email[0]['customer_name'];   
            // $reciever_email = $get_reciever_email[0]['email_id'];
            // $remarks = $get_reciever_email[0]['remarks'];
            // //sender mail id
            // $sender_id = session('user_id');
            // $get_sender_email = DB::select("SELECT email_id_user,username FROM user WHERE id=$sender_id");
            // $get_sender_email = json_decode(json_encode($get_sender_email),true);
            // $sender_name = $get_sender_email[0]['username'];
            // $sender_mail = $get_sender_email[0]['email_id_user'];
            // $data_message = array(
            //     'customer_name'=>$customer_name,
            //     'sender_name'=>$sender_name,
            //     'remarks'=>$remarks,
            //     'solution'=>$solution
            // );
            //$data_message['mail_data'] = $email_data;

            //Sending mail to customer about renewal of rental product....
            // Mail::send('ComplaintManagement/send_complaint_close_mail',$data_message, function($message) use ($reciever_email,$sender_mail)
            // {     
            //     $message->to($reciever_email, 'complaint Closed')->subject('complaint Closed');
            //     $message->from($sender_mail, 'Quali55Care');
            // });
            return redirect()->back()->with('message','complaint closed successfully');
        }
        //show Closed Compplaints
        public function ShowClosedComplaints()
        {
            $get_closed_complaints = DB::select("SELECT 
                                            complaints.*,
                                            customer_details.customer_name as customer_name
                                        FROM 
                                            complaints,customer_details
                                        WHERE 
                                            complaints.status='Closed' 
                                        AND complaints.customer_id=customer_details.cust_id 
                                        ORDER BY complaints.closed_date DESC ");
            $data['get_closed_complaints'] = json_decode(json_encode($get_closed_complaints),true);
            $customer_id = array();
            $temp_complaint_date = array();
            $customer_complaints = array();
            $cust_data = array();
            //$unique_complaint_date = array_unique(array_map(function($elem){return $elem['complaint_date'];}, $data['get_closed_complaints']));
            //$unique_customer_id = array_unique(array_map(function($elem){return $elem['customer_id'];}, $data['get_closed_complaints']));
            
            foreach($data['get_closed_complaints'] as $key => $get_complaint)
            {
                if(in_array($get_complaint['closed_date'],$temp_complaint_date))
                {
                    for ($i=0; $i <count($customer_complaints); $i++) 
                    { 
                        if($customer_complaints[$i]['closed_date'] == $get_complaint['closed_date'])
                        {
                            $count = count($customer_complaints[$i]['customer_ids']);
                            $customer_complaints[$i]['customer_ids'][$count] = $get_complaint['customer_id'];
                            $customer_complaints[$i]['customer_names'][$count] = $get_complaint['customer_name'];
                        }
                    }
                }
                else
                {
                    array_push($temp_complaint_date,$get_complaint['closed_date']);
                    $count = count($customer_complaints);
                    $customer_complaints[$count]['closed_date'] = $get_complaint['closed_date'];
                    $customer_complaints[$count]['customer_ids'][0] = $get_complaint['customer_id'];
                    $customer_complaints[$count]['customer_names'][0] = $get_complaint['customer_name'];
                    
                }
            }

            $closed_complaints = array();
            for ($i=0; $i <count($customer_complaints);$i++)
            {
                $closed_complaints[$i]['closed_date']=$customer_complaints[$i]['closed_date'];
                //array uniques
                $unique_customer_ids = array_unique($customer_complaints[$i]['customer_ids']);
                $unique_customer_names = array_unique($customer_complaints[$i]['customer_names']);
                //put arrays
                $closed_complaints[$i]['customer_ids'] = $unique_customer_ids;
                $closed_complaints[$i]['customer_names'] = $unique_customer_names;
            }
            
            $data['closed_complaints'] = $closed_complaints;
            return view('ComplaintManagement/Closed_Complaints',$data);
        }

        //view datewised complaint of customer
        public function ViewClosedComplaint($customer_id,$closed_date)
        {
            $get_complaint_detail = DB::select("SELECT 
                                                        complaints.*,
                                                        customer_details.*,
                                                        vendor_details.registered_name as vendor_name,
                                                        products.product_name as product_name            
                                                FROM 
                                                    complaints,customer_details,products,vendor_details
                                                WHERE 
                                                    complaints.customer_id='$customer_id' 
                                                    AND complaints.closed_date='$closed_date'
                                                    AND customer_details.cust_id=complaints.customer_id
                                                    AND vendor_details.id=complaints.vendor_id
                                                    AND products.id = complaints.product_id
                                                    AND complaints.status = 'Closed' ");
            $data['get_complaint_detail'] = json_decode(json_encode($get_complaint_detail),true);
            $image = array();
            foreach($data['get_complaint_detail'] as $get_img)
            {
                if(isset($get_img['image']))
                {
                    $img = json_decode($get_img['image'],true);
                    for ($i=0; $i<count($img); $i++)
                    {
                        array_push($image,$img[$i]);
                    }

                }
            }
            $data['image']= $image;
            return view('ComplaintManagement/View_Closed_Complaint',$data);
        }

        //get complaint detaqils by complaint id
        public function GetComplaintDetails($complaint_id)
        {
            $get_complaint_detail = DB::select("SELECT 
                                                    complaints.*,
                                                    products.product_name,
                                                    vendor_details.registered_name as vendor_name
                                                FROM 
                                                    complaints,products,vendor_details 
                                                WHERE 
                                                    complaints.generated_complaint_id ='$complaint_id' 
                                                    AND complaints.product_id = products.id
                                                    AND complaints.vendor_id = vendor_details.id");
            $data['get_complaint_detail'] = json_decode(json_encode($get_complaint_detail),true);
            if(isset($data['get_complaint_detail'][0]['repaired_by']))
            {
                $repaired_boys = $data['get_complaint_detail'][0]['repaired_by'];
                $repaired_by = json_decode($repaired_boys,true);
                $repaired_by_name = array();
                foreach($repaired_by as $key=>$repaired)
                {
                    $id=$repaired;
                    $get_del_users = DB::select("SELECT username FROM delusers WHERE id=$id ");        
                    $get_del_users = json_decode(json_encode($get_del_users),true);
                    array_push($repaired_by_name,$get_del_users[0]['username']);
                }
                $data['get_complaint_detail'][0]['repaired_by_name'] = implode(',',$repaired_by_name);
            }
            $image = json_decode($data['get_complaint_detail'][0]['image']);
            $data['image']= $image;
            //print_r($image);
            //get del users
            $get_del_users = DB::select("SELECT * FROM delusers where role='user' and status='Active' ");
            $data['delusers'] = json_decode(json_encode($get_del_users),true);
            return view('ComplaintManagement/Show_Complaint_Details',$data);
        }

        //date filter for open and closed complains
        public function DateFilter($type)
        {
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            if($type=='open')
            {
                $get_complaints = DB::select("SELECT 
                                            complaints.*,
                                            customer_details.customer_name as customer_name
                                        FROM 
                                            complaints,products,customer_details
                                        WHERE 
                                            complaints.status='Open' 
                                        AND complaints.product_id=products.id
                                        AND complaints.customer_id=customer_details.cust_id 
                                        AND complaints.complaint_date BETWEEN '$start_date' AND '$end_date' 
                                        ORDER BY complaints.complaint_date ASC");
                $data['get_complaints'] = json_decode(json_encode($get_complaints),true);
                $customer_id = array();
                $temp_complaint_date = array();
                $customer_complaints = array();
                $cust_data = array();
                foreach($data['get_complaints'] as $key => $get_complaint)
                {
                    if(in_array($get_complaint['complaint_date'],$temp_complaint_date))
                    {
                        for ($i=0; $i <count($customer_complaints); $i++) 
                        { 
                            if($customer_complaints[$i]['complaint_date'] == $get_complaint['complaint_date'])
                            {
                                $count = count($customer_complaints[$i]['customer_ids']);
                                $customer_complaints[$i]['customer_ids'][$count] = $get_complaint['customer_id'];
                                $customer_complaints[$i]['customer_names'][$count] = $get_complaint['customer_name'];
                            }
                        }
                    }
                    else
                    {
                        array_push($temp_complaint_date,$get_complaint['complaint_date']);
                        $count = count($customer_complaints);
                        $customer_complaints[$count]['complaint_date'] = $get_complaint['complaint_date'];
                        $customer_complaints[$count]['customer_ids'][0] = $get_complaint['customer_id'];
                        $customer_complaints[$count]['customer_names'][0] = $get_complaint['customer_name'];
                        
                    }
                }
                $open_complaints = array();
                for ($i=0; $i <count($customer_complaints);$i++)
                {
                    $open_complaints[$i]['complaint_date']=$customer_complaints[$i]['complaint_date'];
                    //array uniques
                    $unique_customer_ids = array_unique($customer_complaints[$i]['customer_ids']);
                    $unique_customer_names = array_unique($customer_complaints[$i]['customer_names']);
                    //put arrays
                    $open_complaints[$i]['customer_ids'] = $unique_customer_ids;
                    $open_complaints[$i]['customer_names'] = $unique_customer_names;
                    //array replace 
                    //$customer_ids = array_replace($unique_customer_ids,$customer_complaints[$i]['customer_ids']);
                    //print_r($unique_customer_ids);
                }
                
                $data['open_complaints'] = $open_complaints;
                return view('ComplaintManagement/Open_Complaints',$data);
            }
            elseif($type=='closed')
            {
                $get_closed_complaints = DB::select("SELECT 
                                            complaints.*,
                                            customer_details.customer_name as customer_name
                                        FROM 
                                            complaints,customer_details
                                        WHERE 
                                            complaints.status='Closed' 
                                        AND complaints.customer_id=customer_details.cust_id 
                                        AND complaints.closed_date BETWEEN '$start_date' AND '$end_date' 
                                        ORDER BY complaints.closed_date ASC");
                $data['get_closed_complaints'] = json_decode(json_encode($get_closed_complaints),true);
                $customer_id = array();
                $temp_complaint_date = array();
                $customer_complaints = array();
                $cust_data = array();
                foreach($data['get_closed_complaints'] as $key => $get_complaint)
                {
                    if(in_array($get_complaint['closed_date'],$temp_complaint_date))
                    {
                        for ($i=0; $i <count($customer_complaints); $i++) 
                        { 
                            if($customer_complaints[$i]['closed_date'] == $get_complaint['closed_date'])
                            {
                                $count = count($customer_complaints[$i]['customer_ids']);
                                $customer_complaints[$i]['customer_ids'][$count] = $get_complaint['customer_id'];
                                $customer_complaints[$i]['customer_names'][$count] = $get_complaint['customer_name'];
                            }
                        }
                    }
                    else
                    {
                        array_push($temp_complaint_date,$get_complaint['closed_date']);
                        $count = count($customer_complaints);
                        $customer_complaints[$count]['closed_date'] = $get_complaint['closed_date'];
                        $customer_complaints[$count]['customer_ids'][0] = $get_complaint['customer_id'];
                        $customer_complaints[$count]['customer_names'][0] = $get_complaint['customer_name'];
                        
                    }
                }

                $closed_complaints = array();
                for ($i=0; $i <count($customer_complaints);$i++)
                {
                    $closed_complaints[$i]['closed_date']=$customer_complaints[$i]['closed_date'];
                    //array uniques
                    $unique_customer_ids = array_unique($customer_complaints[$i]['customer_ids']);
                    $unique_customer_names = array_unique($customer_complaints[$i]['customer_names']);
                    //put arrays
                    $closed_complaints[$i]['customer_ids'] = $unique_customer_ids;
                    $closed_complaints[$i]['customer_names'] = $unique_customer_names;
                }
                
                $data['closed_complaints'] = $closed_complaints;
                return view('ComplaintManagement/Closed_Complaints',$data);
            }
                
        }

        //12 hrs complaint raised escalation mail
        
        public function EscalationMail(){
            //check complaints pending 12 hrs
            $getComplaints = DB::table('complaints')
                    ->where('created_at','<=',date('Y-m-d H:i:s',strtotime("-12 hours")))
                    ->where('status','Open')
                    ->get();
            $getComplaints = $getComplaints->groupBy('generated_complaint_id');
            $ceoMail = config('app.ceo_email');
            $businessHeadMail = config('app.business_head_email');
            foreach ($getComplaints as $key => $complaint) {
            $getProductDetails = DB::table('order_details')
                        ->select('order_details.*','customer_details.*','products.product_name','vendor_details.registered_name as vendor_name')
                        ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                        ->join('products','order_details.product_id','=','products.id')
                        ->join('vendor_details','order_details.vendor_id','=','vendor_details.id')
                        ->whereIn('order_details.id',$complaint->pluck('order_details_id'))->get();

            //send escalatin mail

                Mail::send('ComplaintManagement/escalation_mail',compact('getProductDetails','complaint'), function($message) use ($ceoMail,$businessHeadMail)
                {     
                    $message->to($ceoMail, 'Escalation - Complaint not attended')->subject('Escalation - Complaint not attended');
                    //$message->to($senderMail->email_id_user, 'Complaint Raised')->subject('Complaint Raised');
                    $message->from('tempmailquali@gmail.com', 'Quali55Care');
                });

            }
        }
       
       
    }

    
?>