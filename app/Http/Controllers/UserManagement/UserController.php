<?php

namespace App\Http\Controllers\UserManagement;

use Mail;
use App\Models\UserRegister;
use Illuminate\Http\Request;
use App\Models\VendorRegister;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Validator;

class UserController extends Controller
{
    public function isLoggedIn()
    {
        $data = session('isLoggedIn');
        return $data;
    }
    public function add_user(Request $request)
    {
        if($_SERVER['REQUEST_METHOD'] == 'GET')
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            return view('UserManagement/add_user');
        }
       
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            $input = $request->all();
            $request->validate([
                'password' => ['required', 
                'min:6', 
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/',
                ],
            ],
            [
                'password' => 'English uppercase characters A – Z',
                // 'confirm_password' => 'English lowercase characters a – z',
                // 'confirm_password' => 'Base 10 digits 0 – 9',
                // 'confirm_password' => 'Non-alphanumeric For example: !, $, #, or %'
            ]);
            $newUser = new UserRegister();     
            //$username = $_POST['username'];
            // $password = $_POST['password'];
            //print_r($_POST);
            $date = date('Y-m-d H:i:m');
            $role_access = json_encode(request()->get('role_access'));
            $insert_user = [
                'username' => request()->get('username'),
                'password' => request()->get('password'),
                'email_id_user' => request()->get('email_id_user'),
                'contact_no' => request()->get('contact_no'),
                'location_user' => request()->get('location_user'),
                'user_city' => request()->get('user_city'),
                'role'=> request()->get('role'),
                'role_access'=>$role_access,
                'created_at' => $date
            ];
            
            $newUser->insert($insert_user);
            return redirect('/add_user')->with('message','New user Added successfully');
        }
    }

    //-----------Edit user----------//
    // function edit_user($uid)
    // {
    //         $isLoggedIn = $this->isLoggedIn();
    //         if($isLoggedIn == 'false')
    //         {
    //             $url = url('/');
    //             return redirect()->to($url);
    //         }
    //         //$user_id = session('user_id');
    //         //echo $user_id;
    //         $user_details = DB::select("SELECT * FROM user WHERE id = $uid");
    //         $data['user_details'] = json_decode(json_encode($user_details),true);
    //         return view('UserManagement/edit_user');
    // }
    public function edit_user(Request $request,$uid)
    {
        if($_SERVER['REQUEST_METHOD'] == 'GET')
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            //$user_id = session('user_id');
            //echo $user_id;
            $user_details = DB::select("SELECT * FROM user WHERE id = $uid");
            $data['user_details'] = json_decode(json_encode($user_details),true);
            // /print_r($data);
            return view('UserManagement/user_edit',$data);
        }
       
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            $input = $request->all();
            $request->validate([
                'password' => ['required', 
                'min:6', 
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/',
                ],
            ],
            [
                'password' => 'English uppercase characters A – Z',
                // 'confirm_password' => 'English lowercase characters a – z',
                // 'confirm_password' => 'Base 10 digits 0 – 9',
                // 'confirm_password' => 'Non-alphanumeric For example: !, $, #, or %'
            ]);
            $newUser = new UserRegister();     
            //$username = $_POST['username'];
            // $password = $_POST['password'];
            //print_r($_POST);
            $date = date('Y-m-d H:i:m');
            $role_access = json_encode(request()->get('role_access'));
            $update_user = [
                'username' => request()->get('username'),
                'password' => request()->get('password'),
                'email_id_user' => request()->get('email_id_user'),
                'contact_no' => request()->get('contact_no'),
                'location_user' => request()->get('location_user'),
                'user_city' => request()->get('user_city'),
                'role'=> request()->get('role'),
                'role_access'=>$role_access,
                //'created_at' => $date
            ];
            
            $newUser->where('id',$uid)->update($update_user);
            return redirect('/view_all_user')->with('message','User updated successfully');
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
            
            if(session('role')=='superuser')
            {
                $all_user = DB::select("SELECT * FROM user WHERE role !='vendor' AND user_city!='Global'  ");

                $all_user = DB::table('user')
                                    ->whereNotIn('role',['vendor'])
                                    ->whereNotIn('user_city',['Global'])
                                    ->when(session('city_based_access') == '1',function($query){
                                        $query->where('user_city',session('user_city'));
                                    })
                                    ->get();
            }
            elseif(session('role')=='admin')
            {
                $user_city = session('user_city');
                $user_id = session('user_id');
                $all_user = DB::select("SELECT * FROM user WHERE  role !='vendor' AND id!='$user_id' AND user_city!='Global' AND user_city='$user_city' ");
            }
            //echo "SELECT * FROM user WHERE role !='vendor' AND user_city='$user_city' ";
            $data['all_user'] = json_decode(json_encode($all_user),true);
            return view('UserManagement/view_all_user',$data);
        } 
    }
    
    public function view_profile()
    {
       $user_id = session('user_id');
       //echo $user_id;
       $user_details = DB::select("SELECT * FROM user WHERE id = $user_id");
       $data['user_details'] = json_decode(json_encode($user_details),true);
       return view('/UserManagement/user_profile',$data);
    }

    public function deliveryStaffs(Request $request){
        $staff = DB::table('delusers')->where('status','Active')->where('role','user')->orderBy('username','ASC')->paginate(10);
        return view('UserManagement.delivery-staff',compact('staff'));
    }

    public function saveStaff(Request $request){
        DB::beginTransaction();
        try{
            // dd($request->all());
            // $validated = $request->validate([
            //    'staffUsername' => 'required',
            //    'staffContactNo' => 'required|min:10|max:10',
            //    'staffCity' => 'required',
            //    'staffRole' => 'required',
            //    'staffIsInHouse' => 'required',
            //    'staffPassword' => 'required',
            //    'staffConfirmPassword' => 'required|same:staffPassword'
            // ]);
    
            // $rules = [
            //     'staffUsername' => 'required',
            //     'staffContactNo' => 'required|min:10|max:10',
            //     'staffCity' => 'required',
            //     'staffRole' => 'required',
            //     'staffIsInHouse' => 'required',
            //     'staffPassword' => 'required',
            //     'staffConfirmPassword' => 'required|same:staffPassword'
            // ];
    
            // $messages = [
            //     'staffConfirmPassword.same' => 'Password not matched!'
            // ];
            $insertData = [
                'username' => $request->get('staffUsername'),
                'contact_no' => $request->get('staffContactNo'),
                'city' => $request->get('staffCity'),
                'role' => $request->get('staffRole'),
                'inhousestaff' => $request->get('staffIsInHouse'),
                'password' => $request->get('staffPassword'),            
                'status' => 'Active'
            ];
            if($request->hasFile('staffProfile')){
                $staffProfile = $_FILES['staffProfile']['name'];
                //print_r($_FILES['shop_images']['name']);
                $targetDir = "assets/uploads/userProfile/";
                $fileName = basename($_FILES['staffProfile']['name']);
                $targetFilePath = $targetDir . $fileName;
                $fileType =strtolower(pathinfo($targetFilePath,PATHINFO_EXTENSION));
                $new_file_name = $targetDir."".$request->get('staffUsername').".".$fileType;
                move_uploaded_file($_FILES["staffProfile"]["tmp_name"], $new_file_name);    
                $staffProfile_filePath = $request->get('staffUsername').".".$fileType;
                $insertData['profile'] = "http://intra.quali55care.com/devweb/eflow/".$new_file_name;
            }
            DB::table('delusers')->insert($insertData);
            DB::commit();
            return redirect()->back()->with('message','Inserted Successfully...!');
        }catch(Exception $ex){
            DB::rollback();
            return redirect()->back()->with('error','Something went wrong!');
        }
        
    }
}
?>