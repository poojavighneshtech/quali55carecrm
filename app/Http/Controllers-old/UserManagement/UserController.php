<?php

namespace App\Http\Controllers\UserManagement;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\VendorRegister;
use App\Models\UserRegister;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Mail;

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
}
?>