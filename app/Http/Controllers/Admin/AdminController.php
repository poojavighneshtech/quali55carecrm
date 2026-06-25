<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\VendorRegister;
use App\Models\UserRegister;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Vendor_Management\VendorController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Leads\LeadController;
use Mail;


class AdminController extends Controller
{

    public function under_construction()
    {
        return view('Admin/under_construction');
    }
    //---FOr session------------//
    public function isLoggedIn()
    {
        $data = session('isLoggedIn');
        //print_r($data);      
        return $data;
    }

    //---------validate login with role based---------//
    public function validate_login(Request $request)
    {
        ini_set('display_errors', 1);
        if($_SERVER['REQUEST_METHOD']=='POST')
        {
            $vendor_details = new UserRegister();
            $leadController = new LeadController();
            $vendorController = new VendorController();
            ini_set('display_errors', 1);
           if ($_POST['submit']=='Login') 
           {
                $input = $request->all();

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
               $username = $_POST['username'];
               $password = $_POST['password'];
               $vendor_details = DB::select("SELECT *  FROM user where username = '$username' AND password = '$password'");
               $data['vendor_details'] = json_decode(json_encode($vendor_details), true);
                //print_r($data['vendor_details']);
                if(isset($data['vendor_details'][0]))
                {
                    if ($data['vendor_details'][0]['role']=='admin') 
                    {
                        if(!empty($_POST["remember"])){
                            //echo "remember me block";
                            $sha1_value = $username.$data['vendor_details'][0]['role'];
                            $remember_token = sha1($sha1_value);
                            $id= $data['vendor_details'][0]['id'];
                            DB::update("UPDATE user SET remember_token = '$remember_token' WHERE id = $id");
                            //echo $sha1_value;
                            //echo $remember_token;
                            setcookie ("remember_token",$remember_token,time()+ 1440);
                        }
                        session(['user_id' => $data['vendor_details'][0]['id']]);
                        session(['username' => $data['vendor_details'][0]['username']]);
                        session(['role' => $data['vendor_details'][0]['role']]);
                        session(['isLoggedIn' => 'true']);
                        //$this->setUserSession($user);
                        //return $this->pending_vendors();
                        //return redirect()->action('LeadController@viewAllLeads');
                        //return $leadController->viewAllLeads();
                        return redirect('viewAllLeads');
                    }
                    if ($data['vendor_details'][0]['role']=='vendor') 
                    {
                        $vendor_details = DB::select("SELECT * FROM vendor_details WHERE of_primary_contact_1=$username");
                        $data['vendor_details'] = json_decode(json_encode($vendor_details), true);
                        session(['id' => $data['vendor_details'][0]['id']]);
                        session(['username' => $data['vendor_details'][0]['registered_name']]);
                        session(['vendor_aggrement' => $data['vendor_details'][0]['vendor_aggreement']]);
                        session(['role' => 'vendor']);
                        session(['isLoggedIn' => 'true']);
                        //print_r($data['vendor_details']);
                        //return view('Vendor_Management/vendor_registered_info',$data);
                        return $vendorController->vendor_registered_info($data);
                    }
                    if ($data['vendor_details'][0]['role']=='user') 
                    {
                        session(['id' => $data['vendor_details'][0]['id']]);
                        session(['username' => $data['vendor_details'][0]['username']]);
                        session(['role' => $data['vendor_details'][0]['role']]);
                        session(['isLoggedIn' => 'true']);
                        //return $leadController->viewAllLeads();
                        return redirect('viewAllLeads');
                    }
                }
                else
                {
                    // echo "<script>alert('You have entered wrong credentials....')</script>";
                    // return view('Admin/view');
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

    //---------Forgot Passwprd-------------------//
    public function forgot_password()
    {
        ini_set('display_errors', 1);
        if($_SERVER['REQUEST_METHOD']=='GET')
        {
            return view('Admin/forgot_password');
        }

        ini_set('display_errors', 1);
        if($_SERVER['REQUEST_METHOD']=='POST')
        {
            $email = $_POST['email'];
            
            $error = "";
            //echo $email;
            
            $vendor_id = DB::select("SELECT id FROM vendor_details WHERE of_email='$email' AND authentication_status='Approved'");
            $data['vendor_id'] = json_decode(json_encode($vendor_id), true);
            if($data['vendor_id']!=null)
            {
                //print_r($data['vendor_id'][0]['id']);
                $vendor_id  = $data['vendor_id'][0]['id'];
                session(['vendor_id' => $vendor_id]);
                session(['email' => $email]);
                $generator = "1357902468"; 
                $OTP = ""; 
                for ($i = 1; $i <= 6; $i++) 
                { 
                    $OTP .= substr($generator, (rand()%(strlen($generator))), 1); 
                }
                $insert_OTP = DB::update("UPDATE user SET otp=$OTP WHERE vendor_id = '$vendor_id'");
                
                $data = array('otp'=>"'".$OTP."'");
                //Sending mail to vendor about OTP forgot Password / or changing password ....
                //$email = $data['vendor_details'][0]['of_email'];
                Mail::send('vendorMail/otpRequestMail', $data, function($otp) 
                {
                    //$email_id = request()->get('vdr_email');
                    $email_id = session('email');
                    $otp->to($email_id, 'OTP Sent Mail')->subject('OTP Update Mail');
                    $otp->from('tempmailquali@gmail.com', 'Quali55Care');
                });
                //return view('Admin/enter_otp');
                return redirect('/enter_otp')->with('message', 'OTP has been sent on your Mail Address');
            }
            else
            {
                // /Session::flash('message', 'This is a message!'); 
                //echo "<script>alert('Enterd Wrong Email or Not registerd '); </script>";
                return redirect('/forgot_password')->with('message', 'Entered Wrong Email or Email Address Not registred');
            }
           
        }
        
    }
    
//-------------submit otp and check ---//
    public function submit_otp(Request $request)
    {
        ini_set('display_errors', 1);
        if($_SERVER['REQUEST_METHOD']=='POST')
        {
            $input = $request->all();

            $rules = $request->validate([
                'Entered_OTP' =>'required|min:6|numeric',
            ]);
            $vendor_id= session('vendor_id');
            $OTP = $_POST['Entered_OTP'];
            $otp_check = DB::select("SELECT otp FROM user WHERE vendor_id =$vendor_id AND otp=$OTP ");
            $data['otp_check'] = json_decode(json_encode($otp_check), true);
            //print_r($data['otp_check'][0]['otp']);
            if($data['otp_check']!=null)
            {
                return redirect('/password_reset')->with('opt_sucess','Your OTP is correct..Now you can change your password');
            }
            else
            {   
                echo"<script>alert('wrong otp');</script>";
                // return view('Admin/enter_otp');
                return redirect('/enter_otp')->with('otp_wrong','Entered OTP is Wrong Please check your Email or click on resend ');
            }
            //print_r($_POST);
        }
    }

    //-----on resend click send otp----------------/
    public function resend_otp()
    {
        ini_set('display_errors', 1);
        if($_SERVER['REQUEST_METHOD']=='GET')
        {
            $email = session('email');
            $vendor_id = session('vendor_id');
            $generator = "1357902468"; 
            
            $OTP = ""; 
            for ($i = 1; $i <= 6; $i++) 
            { 
                $OTP .= substr($generator, (rand()%(strlen($generator))), 1); 
            }
            
            $data = array('otp'=>"'".$OTP."'");
            //Sending mail to vendor about OTP forgot Password / or changing password ....
            //$email = $data['vendor_details'][0]['of_email'];

            Mail::send('vendorMail/otpRequestMail', $data, function($resend_otp) 
            {
                //$email_id = request()->get('vdr_email');
                $email_id = session('email');
                $resend_otp->to($email_id, 'OTP Sent Mail')->subject('OTP Update Mail');
                $resend_otp->from('tempmailquali@gmail.com', 'Quali55Care');
            });
            
            $resend_otp = DB::update("UPDATE user SET otp=$OTP WHERE vendor_id = '$vendor_id'");

            //echo "<script>alert('otp has been sent on your email address');</script>";
            return redirect('/enter_otp')->with('resend_message', 'Your new OTP is sent on your Email address');
        }

    }
//------------for changing password---------------//
    public function password_reset(Request $request)
    {
       
        ini_set('display_errors', 1);
        if($_SERVER['REQUEST_METHOD']=='GET')
        {
            return view('Admin/password_reset');
        }
        
        if($_SERVER['REQUEST_METHOD']=='POST')
        {
            $input = $request->all();

            $rules = $request->validate([
                'password' =>'required|min:6|required_with:confirm_password|same:confirm_password',
                'confirm_password' =>'min:6'
            ],
            [
                'confirm_password.same' => 'Password Confirmation should match the Password',
            ]);

            // $messages = [
            //     'confirm_password.same' => 'Password Confirmation should match the Password',
            // ];
            // $validator = Validator::make($input, $rules, $messages);

            // if ($validator->fails()) {
            //     return back()->withInput()->withErrors($validator->messages());
            // }


            $vendor_id= session('vendor_id');
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            
            if ($password ==$confirm_password)
            {
                $update_password = DB::update("UPDATE user SET password=$confirm_password WHERE vendor_id = '$vendor_id'");
                //$update_otp = DB::update("UPDATE user SET otp=$otp WHERE vendor_id = '$vendor_id'");
                $update_password = DB::update("UPDATE user SET otp = NULL WHERE vendor_id = '$vendor_id'");
                //echo "<script>alert('password updated successfully')</script>";
                return redirect('/')->with('message','Your Password has been updated successfully Now you can login with updated password..');
                //session_abort();
            }
            else
            {
                //echo "<script>alert('password mismatched')</script>";
                return redirect('/password_reset')->with('message','Entered Password Missmatched');
            }
        }
    }  

    public function admin_login(){
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
                    session(['isLoggedIn' => 'true']);
                    return redirect('admin/pending');
                }
                else
                {
                    return view('Admin/admin_login');
                }
            }
            else
            {
                return view('Admin/admin_login');
            }
        }
        
    }

//---------------show  pending vendors--------------//
public function pending_vendors()
{
    $isLoggedIn = $this->isLoggedIn();
    if($isLoggedIn == 'false')
    {
        $url = url('/');
    return redirect()->to($url);
    }
    ini_set('display_errors', 1);
    $vendor_details = DB::select("SELECT * FROM vendor_details where authentication_status = 'Pending' ");
    $data['vendor_details'] = json_decode(json_encode($vendor_details), true);
    $data['shop_img'] = json_decode($data['vendor_details'][0]['shop_image']);
    
    //print_r($data['shop_img']);
    //print_r($data['vendor_details']);
    return view('Admin/pending_vendors',$data);
}
//------------------rejected vendor-------//
   public function rejected_vendors()
   {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
        return redirect()->to($url);
        }
       ini_set('display_errors', 1);
       $vendor_details = DB::select("SELECT * FROM vendor_details where authentication_status = 'Rejected' ");
        $data['vendor_details'] = json_decode(json_encode($vendor_details), true);
        //print_r($data['vendor_details']);
        return view('Admin/rejected_vendors',$data);
   }
//------------show requested vendors----------------//
   public function requested_vendors()
   {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
        return redirect()->to($url);
        }
       ini_set('display_errors', 1);
        $vendor_details = DB::select("SELECT * FROM vendor_details where authentication_status = 'Requested' ");
        $data['vendor_details'] = json_decode(json_encode($vendor_details), true);
        //print_r($data['vendor_details']);
        return view('Admin/requested_vendors',$data);
   }
//-------------------show approved vendors---------------//
   public function approved_vendors()
    {   
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
        return redirect()->to($url);
        }
       ini_set('display_errors', 1);
       $vendor_details = DB::select("SELECT * FROM vendor_details where authentication_status = 'Approved' ");
        $data['vendor_details'] = json_decode(json_encode($vendor_details), true);
        //print_r($data['vendor_details']);
        return view('Admin/approved_vendors',$data);
    }
//-----------------show vendor details------------------//
   public function vendor_details($id)
   {
        //echo $id;
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
        return redirect()->to($url);
        }
        $vendor_details = DB::select("SELECT * FROM vendor_details where id = $id");
        $data['vendor_details'] = json_decode(json_encode($vendor_details), true);
        $data['shop_img'] = json_decode($data['vendor_details'][0]['shop_image']);
        $data['cheque_img'] = json_decode($data['vendor_details'][0]['cheque_image']);
        //print_r($data);

        return view('Admin/vendor_details',$data);
   }
//------------check vendor details by admin and approved rejet actio---------------//
   public function share_info()
   {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
            return redirect()->to($url);
        }
        ini_set('display_errors', 1);
        if ($_POST['submit']=='Revise') 
        {

            $vendor_details_model  = new VendorRegister();

            //print_r($_POST);
            $id = $_POST['vendor_id'];
            $vendor_details = DB::select("SELECT * FROM vendor_details Where id = $id");
            $data['vendor_details'] = json_decode(json_encode($vendor_details), true);
            //print_r($data);
            $reg_name = $data['vendor_details'][0]['registered_name'];
            $primary_contact_no = $data['vendor_details'][0]['of_primary_contact_1'];
            $email = $data['vendor_details'][0]['of_email'];
            session(['email'=>$email]);
            $comment = $_POST['comment'];
            //echo $id;

            $gst_certificate = "Rejected";
            $shop_certificate = "Rejected";
            $vendor_pancard = "Rejected";
            $aggreement_certificate = "Rejected";
            $authentication_status = "Rejected";

            $check_certificate = request()->get('check_certificate');

            if (isset($check_certificate)) 
            {
                if (in_array("gst_certificate",$check_certificate)) {
                    $gst_certificate = "Accepted";
                }
                
                if (in_array("shop_certificate",$check_certificate)) {
                    $shop_certificate = "Accepted";
                }
        
                if (in_array("vendor_pancard",$check_certificate)) {
                    $vendor_pancard = "Accepted";
                }
        
                if (in_array("aggreement_certificate",$check_certificate)) {
                    $aggreement_certificate = "Accepted";
                }
            }
            $vendor_details_updateData = [
                'gst_status' => $gst_certificate,
                'shop_establishment_status' => $shop_certificate,
                'vendor_pan_card_status' => $vendor_pancard,
                'vendor_aggreement_status' => $aggreement_certificate,
                'authentication_status' => $authentication_status,
                'comment' => $comment

            ];
            $vendor_details_model-> where('id',$id)->update($vendor_details_updateData);
            $data = array('name'=>"'".$reg_name."'",'primary_contact_no'=>"'".$primary_contact_no."'");
            //Sending mail to vendor about updated registration request....
            //$email = $data['vendor_details'][0]['of_email'];

            Mail::send('vendorMail/registrationStatusUpdateMail', $data, function($message) 
            {
                //$email_id = request()->get('vdr_email');
                $email_id = session('email');
                $message->to($email_id, 'Registration Update Mail')->subject('Registration Update Mail');
                $message->from('tempmailquali@gmail.com', 'Quali55Care');
            });
            //return view('Admin/pending_vendors');
            return $this->pending_vendors();


        }

        if ($_POST['submit']=='Approve') 
        {
            $id = $_POST['vendor_id'];

            $vendor_details = DB::select("SELECT * FROM vendor_details where id = $id");
            $data['vendor_details'] = json_decode(json_encode($vendor_details), true);
            
            $primary_contact_no = $data['vendor_details'][0]['of_primary_contact_1'];
            $reg_name = $data['vendor_details'][0]['registered_name'];
            $email_id = $data['vendor_details'][0]['of_email'];
            session(['email'=>$email_id]);
            //echo $primary_contact_no;
            

            $vendor_register  = new UserRegister();
            $vendor_details  = new VendorRegister();

            function generate_password($len = 8){
                $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                $password = substr( str_shuffle( $chars ), 0, $len );
                return $password;
            }

            $password = generate_password();
            $date = date('Y-m-d H:i:m');
            $role = "vendor";
            //echo $password;
            $vendor_details_insertUser = [
                'vendor_id' => $id,
                'username' => $primary_contact_no,
                'password' => $password,
                'created_at'=> $date,
                'role' =>$role
            ];
            $vendor_register->insert($vendor_details_insertUser);


            $vendor_details_updateData = [
                'gst_status' => 'Accepted',
                'shop_establishment_status' => 'Accepted',
                'vendor_pan_card_status' => 'Accepted',
                'vendor_aggreement_status' => 'Accepted',
                'authentication_status' => 'Approved'

            ];
            $vendor_details-> where('id',$id)->update($vendor_details_updateData);
            $data = array('name'=>"'".$reg_name."'",'primary_contact_no'=>"'".$primary_contact_no."'",'username'=>"'".$primary_contact_no."'",'password'=>"'".$password."'");
            //Sending mail to vendor about Registration Approval..
            Mail::send('vendorMail/registrationApprovedMail', $data, function($message) 
            {
                $email_id = session('email');
                $message->to($email_id, 'Registration Approval Mail')->subject('Registration Approval Mail');
                $message->from('tempmailquali@gmail.com', 'Quali55Care');
            });
            //return view('Admin/pending_vendors');
            return $this->approved_vendors();

        }        
   }
   
   //Pending Products------------
   public function product_request()
   {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
            return redirect()->to($url);
        }
        $vendor_details = DB::select("SELECT vendor_details.registered_name as registered_name, vendor_products.id as id, vendor_products.product_id as product_id, vendor_products.product_brand as product_details, vendor_products.product_rent_requested as product_rent_requested FROM vendor_products,vendor_details WHERE vendor_products.vendor_id = vendor_details.id AND vendor_products.status = 'Pending'");
        $data['vendor_details'] = json_decode(json_encode($vendor_details), true);
        //print_r($data['vendor_details']);

        $data['vendor_product_details'] = array();
        $data['vendor_product_counts'] = array();
        for ($i=0; $i <count($data['vendor_details']); $i++) 
        {
            $temp = array();
            $products = array();
            $vendor_name = $data['vendor_details'][$i]['registered_name'];
            if(in_array($vendor_name, array_column($data['vendor_product_counts'], 'vendor_name')))
            {
                $vendor_count_key = array_search($vendor_name, array_column($data['vendor_product_counts'], 'vendor_name'));
                $data['vendor_product_counts'][$vendor_count_key]['count'] = $data['vendor_product_counts'][$vendor_count_key]['count']+1;
                $product_count_key = array_search($vendor_name, array_column($data['vendor_product_counts'][$vendor_count_key]['products'], 'product_name'));
                $product_count_key = $product_count_key+1;
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_id'] = $data['vendor_details'][$i]['id'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_name'] = $data['vendor_details'][$i]['product_id'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_details'] = $data['vendor_details'][$i]['product_details'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_rent_requested'] = $data['vendor_details'][$i]['product_rent_requested'];
            }
            else
            {
                $temp['vendor_name'] = $vendor_name;
                $temp['count'] = 1;
                $products[0]['product_id'] = $data['vendor_details'][$i]['id'];
                $products[0]['product_name'] = $data['vendor_details'][$i]['product_id'];
                $products[0]['product_details'] = $data['vendor_details'][$i]['product_details'];
                $products[0]['product_rent_requested'] = $data['vendor_details'][$i]['product_rent_requested'];
                $temp['products'] = $products;
                array_push($data['vendor_product_counts'],$temp);
                //print_r($product);
                
            }            
        }
        //print_r($data['vendor_product_counts']);
        return view('Admin/product_request',$data);
   }
   //Approved Products----------------
   public function product_approved_rent()
   {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
            return redirect()->to($url);
        }
        $vendor_details = DB::select("SELECT vendor_details.registered_name as registered_name, vendor_products.id as id, vendor_products.product_id as product_id, vendor_products.product_brand as product_details, vendor_products.product_rent_approved as product_rent_approved FROM vendor_products,vendor_details WHERE vendor_products.vendor_id = vendor_details.id AND vendor_products.status = 'Approved'");
        $data['vendor_details'] = json_decode(json_encode($vendor_details), true);
        //print_r($data['vendor_details']);

        $data['vendor_product_details'] = array();
        $data['vendor_product_counts'] = array();
        for ($i=0; $i <count($data['vendor_details']); $i++) 
        {
            $temp = array();
            $products = array();
            $vendor_name = $data['vendor_details'][$i]['registered_name'];
            if(in_array($vendor_name, array_column($data['vendor_product_counts'], 'vendor_name')))
            {
                $vendor_count_key = array_search($vendor_name, array_column($data['vendor_product_counts'], 'vendor_name'));
                $data['vendor_product_counts'][$vendor_count_key]['count'] = $data['vendor_product_counts'][$vendor_count_key]['count']+1;
                $product_count_key = array_search($vendor_name, array_column($data['vendor_product_counts'][$vendor_count_key]['products'], 'product_name'));
                $product_count_key = $product_count_key+1;
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_id'] = $data['vendor_details'][$i]['id'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_name'] = $data['vendor_details'][$i]['product_id'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_details'] = $data['vendor_details'][$i]['product_details'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_rent_approved'] = $data['vendor_details'][$i]['product_rent_approved'];
            }
            else
            {
                $temp['vendor_name'] = $vendor_name;
                $temp['count'] = 1;
                $products[0]['product_id'] = $data['vendor_details'][$i]['id'];
                $products[0]['product_name'] = $data['vendor_details'][$i]['product_id'];
                $products[0]['product_details'] = $data['vendor_details'][$i]['product_details'];
                $products[0]['product_rent_approved'] = $data['vendor_details'][$i]['product_rent_approved'];
                $temp['products'] = $products;
                array_push($data['vendor_product_counts'],$temp);
                //print_r($product);
                
            }            
        }
        //print_r($data['vendor_product_counts']);
        return view('Admin/product_approved_rent',$data);
   }
   //Rejected Products----------------
   public function product_rejected_rent()
   {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
            return redirect()->to($url);
        }
        $vendor_details = DB::select("SELECT vendor_details.registered_name as registered_name, vendor_products.id as id, vendor_products.product_id as product_id, vendor_products.product_brand as product_details, vendor_products.product_rent_requested as product_rent_rejected FROM vendor_products,vendor_details WHERE vendor_products.vendor_id = vendor_details.id AND vendor_products.status = 'Rejected'");
        $data['vendor_details'] = json_decode(json_encode($vendor_details), true);
        //print_r($data['vendor_details']);

        $data['vendor_product_details'] = array();
        $data['vendor_product_counts'] = array();
        for ($i=0; $i <count($data['vendor_details']); $i++) 
        {
            $temp = array();
            $products = array();
            $vendor_name = $data['vendor_details'][$i]['registered_name'];
            if(in_array($vendor_name, array_column($data['vendor_product_counts'], 'vendor_name')))
            {
                $vendor_count_key = array_search($vendor_name, array_column($data['vendor_product_counts'], 'vendor_name'));
                $data['vendor_product_counts'][$vendor_count_key]['count'] = $data['vendor_product_counts'][$vendor_count_key]['count']+1;
                $product_count_key = array_search($vendor_name, array_column($data['vendor_product_counts'][$vendor_count_key]['products'], 'product_name'));
                $product_count_key = $product_count_key+1;
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_id'] = $data['vendor_details'][$i]['id'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_name'] = $data['vendor_details'][$i]['product_id'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_details'] = $data['vendor_details'][$i]['product_details'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_rent_rejected'] = $data['vendor_details'][$i]['product_rent_rejected'];
            }
            else
            {
                $temp['vendor_name'] = $vendor_name;
                $temp['count'] = 1;
                $products[0]['product_id'] = $data['vendor_details'][$i]['id'];
                $products[0]['product_name'] = $data['vendor_details'][$i]['product_id'];
                $products[0]['product_details'] = $data['vendor_details'][$i]['product_details'];
                $products[0]['product_rent_rejected'] = $data['vendor_details'][$i]['product_rent_rejected'];
                $temp['products'] = $products;
                array_push($data['vendor_product_counts'],$temp);
                //print_r($product);
                
            }            
        }
        //print_r($data['vendor_product_counts']);
        return view('Admin/product_rejected_rent',$data);
   }
   //Rejected Products----------------
   public function product_requested_rent()
   {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
            return redirect()->to($url);
        }
        $vendor_details = DB::select("SELECT vendor_details.registered_name as registered_name, vendor_products.id as id, vendor_products.product_id as product_id, vendor_products.product_brand as product_details, vendor_products.product_rent_requested as product_rent_requested FROM vendor_products,vendor_details WHERE vendor_products.vendor_id = vendor_details.id AND vendor_products.status = 'Requested'");
        $data['vendor_details'] = json_decode(json_encode($vendor_details), true);
        //print_r($data['vendor_details']);

        $data['vendor_product_details'] = array();
        $data['vendor_product_counts'] = array();
        for ($i=0; $i <count($data['vendor_details']); $i++) 
        {
            $temp = array();
            $products = array();
            $vendor_name = $data['vendor_details'][$i]['registered_name'];
            if(in_array($vendor_name, array_column($data['vendor_product_counts'], 'vendor_name')))
            {
                $vendor_count_key = array_search($vendor_name, array_column($data['vendor_product_counts'], 'vendor_name'));
                $data['vendor_product_counts'][$vendor_count_key]['count'] = $data['vendor_product_counts'][$vendor_count_key]['count']+1;
                $product_count_key = array_search($vendor_name, array_column($data['vendor_product_counts'][$vendor_count_key]['products'], 'product_name'));
                $product_count_key = $product_count_key+1;
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_id'] = $data['vendor_details'][$i]['id'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_name'] = $data['vendor_details'][$i]['product_id'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_details'] = $data['vendor_details'][$i]['product_details'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_rent_requested'] = $data['vendor_details'][$i]['product_rent_requested'];
            }
            else
            {
                $temp['vendor_name'] = $vendor_name;
                $temp['count'] = 1;
                $products[0]['product_id'] = $data['vendor_details'][$i]['id'];
                $products[0]['product_name'] = $data['vendor_details'][$i]['product_id'];
                $products[0]['product_details'] = $data['vendor_details'][$i]['product_details'];
                $products[0]['product_rent_requested'] = $data['vendor_details'][$i]['product_rent_requested'];
                $temp['products'] = $products;
                array_push($data['vendor_product_counts'],$temp);
                //print_r($product);
                
            }            
        }
        //print_r($data['vendor_product_counts']);
        return view('Admin/product_requested_rent',$data);
   }
   public function update_product_status()
   {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
            return redirect()->to($url);
        }
       if($_SERVER['REQUEST_METHOD']=='POST')
       {
            if($_POST['submit']=='submit')
            {
                //print_r($_POST);
                $ids = json_decode(request()->get('info'));
                foreach ($ids as $id)
                {
                    $action = request()->get('action'.$id);
                    $vendor_product_id = request()->get('vendor_product_id'.$id);
                    if((request()->get('comment'.$id))!==null)
                    {
                            $comment = request()->get('comment'.$id);
                    }
                    else
                    {
                        $comment = 'null';
                    }
                    if($action == 'Approve')
                    {
                        DB::update("UPDATE vendor_products SET status='Approved', product_rent_approved=vendor_products.product_rent_requested, comment='$comment' WHERE id = $vendor_product_id");
                    }
                    else
                    {
                            DB::update("UPDATE vendor_products SET status='Rejected', comment='$comment' WHERE id = $vendor_product_id");
                    }
                }
                return $this->product_request();
            }
            else
            {
                $ids = json_decode(request()->get('info'));
                foreach ($ids as $id)
                {
                    $action = request()->get('action'.$id);
                    $vendor_product_id = request()->get('vendor_product_id'.$id);
                    if((request()->get('comment'.$id))!==null)
                    {
                            $comment = request()->get('comment'.$id);
                    }
                    else
                    {
                        $comment = 'null';
                    }
                    if($action == 'Approve')
                    {
                        DB::update("UPDATE vendor_products SET status='Approved', product_rent_approved=vendor_products.product_rent_requested, comment='$comment' WHERE id = $vendor_product_id");
                    }
                    else
                    {
                        DB::update("UPDATE vendor_products SET status='Rejected', comment='$comment' WHERE id = $vendor_product_id");
                    }
                }
                return $this->product_approved_rent();
            }
        }
   }
    public function add_new_product()
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
            return redirect()->to($url);
        }
        if($_SERVER['REQUEST_METHOD'] == 'GET')
        {
            return view('Admin/add_new_product');
        }
        else
        {
            //print_r($_POST);
            $product_name = $_POST['product_name'];
            $product_details = $_POST['product_details'];
            $product_type = $_POST['product_type'];
            DB::insert("INSERT INTO products (product_name,product_details,product_type) values('$product_name','$product_details','$product_type')");
            return redirect('/add_new_product')->with('message','New Product Successfully added');
        }
    }
   public function detailed_rent_list()
   {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
            return redirect()->to($url);
        }
       $vendor_names = DB::select("SELECT * FROM vendor_details");
       $data['vendor_names'] = json_decode(json_encode($vendor_names),true);
       return view('Admin/detailed_rent_list',$data);
   }
   public function fetch_all_vendor_details($vendor_id)
   {
        //echo $vendor_id;
        $vendor_details = DB::select(
            "SELECT
                vendor_details.registered_name,
                vendor_products.product_brand,
                vendor_products.product_rent_approved,
                vendor_products.product_rent_requested,
                vendor_products.status,
                products.product_name,
                products.product_details,
                vendor_warehouse.wh_city,
                vendor_warehouse.wh_pincode,
                vendor_warehouse.wh_primary_contact_1
            FROM
                vendor_details,
                vendor_products,
                products,
                vendor_warehouse 
            WHERE
                vendor_details.id=$vendor_id 
                AND
                vendor_products.vendor_id =$vendor_id 
                AND
                vendor_products.product_id=products.id 
                AND
                vendor_products.warehouse_id=vendor_warehouse.id"
        );
        $data['vendor_details'] = json_decode(json_encode($vendor_details),true);
        //$json= array('product_name' => $data['vendor_details'][0]['pro'] , 'customer_name' => $data['vendor_details'][0]['customer_name'] , 'location' => $data['vendor_details'][0]['location'] , 'address_line_1' => $data['vendor_details'][0]['address_line_1'] , 'address_line_2' => $data['vendor_details'][0]['address_line_2'] , 'area' => $data['vendor_details'][0]['area'] , 'landmark' => $data['vendor_details'][0]['landmark'] , 'city' => $data['vendor_details'][0]['city'] , 'pincode' => $data['vendor_details'][0]['pincode'] , 'state' => $data['vendor_details'][0]['state'] , 'country' => $data['vendor_details'][0]['country'] , 'secondary_contact_no' => $data['vendor_details'][0]['secondary_contact_no'] , 'email_id' => $data['vendor_details'][0]['email_id'] , 'refered_by' => $data['vendor_details'][0]['refered_by']);	
        $jsonstring = json_encode($data['vendor_details']);
        echo $jsonstring;
        //print_r($data['vendor_details']);
   }

   //-------------add new user---------//
   public function add_user()
   {
       if($_SERVER['REQUEST_METHOD'] == 'GET')
       {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            return view('Admin/add_user');
       }
       
       if($_SERVER['REQUEST_METHOD'] == 'POST')
       {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            $newUser = new UserRegister();     
            //$username = $_POST['username'];
            // $password = $_POST['password'];
            //print_r($_POST);
            $date = date('Y-m-d H:i:m');
            $insert_user = [
                'username'=>request()->get('username'),
                'password'=>request()->get('password'),
                'role'=>'user',
                'created_at' => $date
            ];
            $newUser->insert($insert_user);
            return redirect('/add_user')->with('message','New user Added successfully');
       }
   }

   //----------view all user-----------//
   public function view_all_user()
   {
        if($_SERVER['REQUEST_METHOD'] == 'GET')
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            $all_user = DB::select("SELECT * FROM user WHERE role='user'");
            $data['all_user'] = json_decode(json_encode($all_user),true);
            return view('Admin/view_all_user',$data);
        } 
   }

   public function logout()
   {
        $request = new Request();
        session(['id' => null]);
        session(['username' => null]);
        session(['role' => null]);
        session(['isLoggedIn' => 'false']);
        //return view('Admin/admin_login');
        return redirect('/');
   }
}