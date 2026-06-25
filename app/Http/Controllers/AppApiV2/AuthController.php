<?php

namespace App\Http\Controllers\AppApiV2;

use DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    //
    public function userExists($username,$password){
        return DB::table('delusers')->where('username',$username)->where('password',$password)->where('status','Active')->exists();
    }
    public function validateUser(Request $request){
        // return json_encode($request->all());
        if($this->userExists($request->get('username'),$request->get('password'))){
            DB::table('delusers')->where('username',$request->get('username'))->where('password',$request->get('password'))->update(['usertoken'=>Str::random(60)]);
            $delstaff = DB::table('delusers')->where('username',$request->get('username'))->where('password',$request->get('password'))->where('status','Active')->first();
            return json_encode(['status'=>'success','description'=>'found','resp'=>$delstaff]);
        }else{
            return json_encode(['status'=>'success','description'=>'notfound','resp'=>'Username/Password Incorrect!']);
        }
    }
    public function fetchProfile(Request $request){    
        $delstaff = DB::table('delusers')->where('username',$request->get('username'))->where('usertoken',$request->get('token'))->first();
        if($delstaff->profile == null || $delstaff->profile == ""){            
            $delstaff->profile ="http://intra.quali55care.com/prodweb/eflow/assets/uploads/userProfile/Default.png";
        }
        return json_encode(['status'=>'success','description'=>'found','resp'=>$delstaff]);
    }
}
