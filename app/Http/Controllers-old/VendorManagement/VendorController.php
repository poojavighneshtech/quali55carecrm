<?php

namespace App\Http\Controllers\VendorManagement;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\VendorRegister;
use App\Models\UserRegister;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Mail;

class VendorController extends Controller
{
    public function isLoggedIn()
    {
        $data = session('isLoggedIn');
        return $data;
    }
    public function pending_vendors()
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
        return redirect()->to($url);
        }
        ini_set('display_errors', 1);
        if(session('role')=='superuser')
        {
            // $vendor_details = DB::select("SELECT * FROM vendor_details where authentication_status = 'Pending' ");    
            $vendor_details = DB::table('vendor_details')
                                    ->where('authentication_status','Pending')
                                    ->when(session('city_based_access') == '1', function($query){
                                        $query->where('vendor_details.of_city',session('user_city'));
                                    })
                                    ->get()
                                    ->toArray();
        }
        elseif(session('role')=='admin')
        {
            $user_city = session('user_city');
            $vendor_details = DB::select("SELECT * FROM vendor_details where authentication_status = 'Pending' AND of_city = '$user_city' ");    
        }
        elseif(session('role')=='user')
        {
            $user_city = session('user_city');
            $vendor_details = DB::select("SELECT * FROM vendor_details where authentication_status = 'Pending' AND of_city = '$user_city' ");    
        }
        //$vendor_details = DB::select("SELECT * FROM vendor_details where authentication_status = 'Pending' ");
        $data['vendor_details'] = json_decode(json_encode($vendor_details), true);
        // $data['shop_img'] = json_decode($data['vendor_details'][0]['shop_image']);        
        return view('VendorManagement/pending_vendors',$data);
    }
    public function vendor_details($id)
    {        
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
        return redirect()->to($url);
        }
        $vendor_details = DB::select("SELECT * FROM vendor_details where id = $id");
        $data['vendor_details'] = json_decode(json_encode($vendor_details), true);
        // $data['shop_img'] = json_decode($data['vendor_details'][0]['shop_image']);
        $data['cheque_img'] = json_decode($data['vendor_details'][0]['cheque_image']);        
        return view('VendorManagement/vendor_details',$data);
    }
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
            $id = $_POST['vendor_id'];
            $vendor_details = DB::select("SELECT * FROM vendor_details Where id = $id");
            $data['vendor_details'] = json_decode(json_encode($vendor_details), true);            
            $reg_name = $data['vendor_details'][0]['registered_name'];
            $primary_contact_no = $data['vendor_details'][0]['of_primary_contact_1'];
            $email = $data['vendor_details'][0]['of_email'];
            session(['email'=>$email]);
            $comment = $_POST['comment'];
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
            $data = array('name'=>"'".$reg_name."'",'primary_contact_no'=>"'".$primary_contact_no."'",'comment'=>"'".$comment."'");

            Mail::send('vendorMail/registrationStatusUpdateMail', $data, function($message) 
            {                
                $email_id = session('email');
                $message->to($email_id, 'Registration Update Mail')->subject('Registration Update Mail');
                $message->from('tempmailquali@gmail.com', 'Quali55Care');
            });            
            return redirect('/pending_vendors');
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

            Mail::send('vendorMail/registrationApprovedMail', $data, function($message) 
            {
                $email_id = session('email');
                $message->to($email_id, 'Registration Approval Mail')->subject('Registration Approval Mail');
                $message->from('tempmailquali@gmail.com', 'Quali55Care');
            });
            return redirect('/approved_vendors');

        }        
    }
    public function approved_vendors()
    {   
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
        return redirect()->to($url);
        }
        ini_set('display_errors', 1);
        if(session('role')=='superuser')
        {
            // $vendor_details = DB::select("SELECT * FROM vendor_details where authentication_status = 'Approved' ");
            $vendor_details = DB::table('vendor_details')
                                    ->where('authentication_status','Approved')
                                    ->when(session('city_based_access') == '1', function($query){
                                        $query->where('vendor_details.of_city',session('user_city'));
                                    })
                                    ->get()
                                    ->toArray();
        }
        elseif(session('role')=='admin')
        {
            $user_city = session('user_city');
            $vendor_details = DB::select("SELECT * FROM vendor_details where authentication_status = 'Approved' AND of_city = '$user_city' ");
        }
        elseif(session('role')=='user')
        {
            $user_city = session('user_city');
            $vendor_details = DB::select("SELECT * FROM vendor_details where authentication_status = 'Approved' AND of_city = '$user_city' ");   
        }
        //$vendor_details = DB::select("SELECT * FROM vendor_details where authentication_status = 'Approved' ");
        $data['vendor_details'] = json_decode(json_encode($vendor_details), true);
        //print_r($data['vendor_details']);
        return view('VendorManagement/approved_vendors',$data);
    }
    public function rejected_vendors()
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
        return redirect()->to($url);
        }
        ini_set('display_errors', 1);
        if(session('role')=='superuser')
        {
            // $vendor_details = DB::select("SELECT * FROM vendor_details where authentication_status = 'Rejected' ");
            $vendor_details = DB::table('vendor_details')
                                    ->where('authentication_status','Rejected')
                                    ->when(session('city_based_access') == '1', function($query){
                                        $query->where('vendor_details.of_city',session('user_city'));
                                    })
                                    ->get()
                                    ->toArray();
        }
        elseif(session('role')=='admin')
        {
            $user_city = session('user_city');
            $vendor_details = DB::select("SELECT * FROM vendor_details where authentication_status = 'Rejected' AND of_city = '$user_city' ");
        }
        elseif(session('role')=='user')
        {
            $user_city = session('user_city');
            $vendor_details = DB::select("SELECT * FROM vendor_details where authentication_status = 'Rejected' AND of_city = '$user_city' ");   
        }
        //$vendor_details = DB::select("SELECT * FROM vendor_details where authentication_status = 'Rejected' ");
        $data['vendor_details'] = json_decode(json_encode($vendor_details), true);
        //print_r($data['vendor_details']);
        return view('VendorManagement/rejected_vendors',$data);
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
        if(session('role')=='superuser')
        {
            // $vendor_details = DB::select("SELECT * FROM vendor_details where authentication_status = 'Requested' ");
            $vendor_details = DB::table('vendor_details')
                                    ->where('authentication_status','Requested')
                                    ->when(session('city_based_access') == '1', function($query){
                                        $query->where('vendor_details.of_city',session('user_city'));
                                    })
                                    ->get()
                                    ->toArray();
        }
        elseif(session('role')=='admin')
        {
            $user_city = session('user_city');
            $vendor_details = DB::select("SELECT * FROM vendor_details where authentication_status = 'Requested' AND of_city = '$user_city' ");
        }
        elseif(session('role')=='user')
        {
            $user_city = session('user_city');
            $vendor_details = DB::select("SELECT * FROM vendor_details where authentication_status = 'Requested' AND of_city = '$user_city' ");   
        }
        //$vendor_details = DB::select("SELECT * FROM vendor_details where authentication_status = 'Requested' ");
        $data['vendor_details'] = json_decode(json_encode($vendor_details), true);
        //print_r($data['vendor_details']);
        return view('VendorManagement/requested_vendors',$data);
    }
}
?>