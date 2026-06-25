<?php

namespace App\Http\Controllers\NurseController;

use App\Http\Controllers\Controller;
use App\Models\customer_detail;
use App\Models\lead;
use App\Models\JDLead;
use App\Models\Nurses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\User\UserController;

class NurseController extends Controller
{
   public function isLoggedIn()
   {
      $data = session('isLoggedIn');
      //print_r($data);      
      return $data;
   }

   // Add Nurse 
   public function add_nurse()
   {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
        return redirect()->to($url);
        }
        
        if($_SERVER['REQUEST_METHOD']=='GET')
        {
            $cities = DB::select("SELECT * FROM cities ");
            $data['cities'] = json_decode(json_encode($cities), true);      
            return view('Nurses/Add_nurse',$data);
        }
        if($_SERVER['REQUEST_METHOD']=='POST')
        {
            $NursesData = new Nurses();
            $nurse_name = $_POST['nurse_name'];
            $nurses_info = [
                'name' =>request()->get('nurse_name'),
                'primary_contact' =>request()->get('primary_contact'),
                'secondary_contact' =>request()->get('secondary_contact'),
                'gender' =>request()->get('gender'),
                'city' =>request()->get('city'),
                'profession' =>request()->get('profession')
            ];
            $NursesData->insert($nurses_info);
            return redirect()->back()->with('message',$nurse_name.' nurse added successfully');
        }

   }

   // ****view all Nurses ****
   public function view_all_nurse()
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
       return redirect()->to($url);
      }
      $data['user_id'] = session('user_id');
      $nurses_data = DB::select("SELECT * FROM nurses_data where status='new' ");
      $data['nurses_data'] = json_decode(json_encode($nurses_data), true);
      //echo "<script>localStorage['filtered']='today';</script>";
      return view('Nurses/view_all_nurse',$data);
   }
   //---------view reffered nurse----///
   public function view_referred_nurse()
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
        return redirect()->to($url);
      }
      $data['user_id'] = session('user_id');
      if(session('role') == 'admin' || session('role') == 'superuser')
      {
        $nurses_data = DB::select("SELECT * FROM nurses_data where status='App Installed'");
      }
      elseif(session('role') == 'user')
      {
          $user_id = session('user_id');
        $nurses_data = DB::select("SELECT * FROM nurses_data where status='App Installed' AND referrer_owner = $user_id");
      }
      $data['nurses_data'] = json_decode(json_encode($nurses_data), true);
    for ($i=0; $i < count($data['nurses_data']); $i++) 
    {
          $user_id = $data['nurses_data'][$i]['referrer_owner'];
          $user_name = DB::select("SELECT username FROM user where id='$user_id' ");
          //print_r($user_id);
          $data['referrer_owner_details'] = json_decode(json_encode($user_name), true);
          if(isset($data['nurses_data'][$i]['username']))
          {
            $data['nurses_data'][$i]['username']=$data['referrer_owner_details'][0]['username'];
          }
          //print_r($data);
    }
      //echo "<script>localStorage['filtered']='today';</script>";
      return view('Nurses/view_referred_nurse',$data);
   }

   //--inprogress --
   public function view_inprogress_nurse()
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
       return redirect()->to($url);
      }
      if(session('role') == 'admin' || session('role') == 'superuser')
      {
        $nurses_data = DB::select("SELECT * FROM nurses_data where status='In Progress'");
      }
      elseif(session('role') == 'user')
      {
          $user_id = session('user_id');
        $nurses_data = DB::select("SELECT * FROM nurses_data where status='In Progress' AND referrer_owner = $user_id");
      }
      //$data['user_id'] = session('user_id');
      //$nurses_data = DB::select("SELECT * FROM nurses_data where status='In Progress'");
      $data['nurses_data'] = json_decode(json_encode($nurses_data), true);
      for ($i=0; $i < count($data['nurses_data']); $i++) 
      {
            $user_id = $data['nurses_data'][$i]['referrer_owner'];
            $user_name = DB::select("SELECT username FROM user where id='$user_id' ");
            //print_r($user_id);
            $data['referrer_owner_details'] = json_decode(json_encode($user_name), true);
            $data['nurses_data'][$i]['username']=$data['referrer_owner_details'][0]['username'];
            //print_r($data);
      }
      //print_r($data);
      //echo "<script>localStorage['filtered']='today';</script>";
      return view('Nurses/view_inprogress_nurse',$data);
   }

   //----------view closed nurse -------------//
   public function view_closed_nurse()
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
       return redirect()->to($url);
      }
      $data['user_id'] = session('user_id');
      if(session('role') == 'admin' || session('role') == 'superuser')
      {
        $nurses_data = DB::select("SELECT * FROM nurses_data where status!='In Progress' AND status!='App Installed' AND status!='new'");
      }
      elseif(session('role') == 'user')
      {
          $user_id = session('user_id');
        $nurses_data = DB::select("SELECT * FROM nurses_data where status!='In Progress' AND status!='App Installed' AND status!='new' AND referrer_owner = $user_id");
      }
    //   $nurses_data = DB::select("SELECT * FROM nurses_data where status!='In Progress' AND status!='App Installed' AND status!='new' ");
      $data['nurses_data'] = json_decode(json_encode($nurses_data), true);
      for ($i=0; $i < count($data['nurses_data']); $i++) 
      {
            $user_id = $data['nurses_data'][$i]['referrer_owner'];
            $user_name = DB::select("SELECT username FROM user where id='$user_id' ");
            //print_r($user_id);
            $data['referrer_owner_details'] = json_decode(json_encode($user_name), true);
            $data['nurses_data'][$i]['username']=$data['referrer_owner_details'][0]['username'];
            //print_r($data);
      }
      //echo "<script>localStorage['filtered']='today';</script>";
      return view('Nurses/view_closed_nurse',$data);
   }

   //view nurse information
   public function view_nurse_details($nurse_id)
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
       return redirect()->to($url);
      }
      $data['user_id'] = session('user_id');
      $nurses_data = DB::select("SELECT * FROM nurses_data where id='$nurse_id' ");
      $data['nurses_data'] = json_decode(json_encode($nurses_data), true);
      //echo "<script>localStorage['filtered']='today';</script>";
      return view('Nurses/view_nurse_details',$data);
   }

   //----------In process status-------//
   public function in_progress($nurse_id, $user_id)
   {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
        return redirect()->to($url);
        }
        $NurseData = new Nurses();
        $nurse_status = [
            'status' => "In Progress",
            'referrer_owner' => $user_id
        ];
        $NurseData->where('id',$nurse_id)->update($nurse_status);
        return redirect('view_all_nurse')->with('message','In Progress status Successfully');
   }
    

   //-----------App installed status -----//
    public function referral()
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
        return redirect()->to($url);
        }
        $nurse_id = $_POST['nurse_id'];
        $user_id  = session('user_id');
        $NurseData = new Nurses();
        $nurse_status = [
            'status' => "App Installed",
            'referrer_owner' => $user_id
        ];
        $NurseData->where('id',$nurse_id)->update($nurse_status);
        return redirect('view_referred_nurse')->with('message','Refferred Successfully');
    }

    //--------------Closed Status update------//
    public function close($nurse_id, $user_id)
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
        return redirect()->to($url);
        }
        $NurseData = new Nurses();
        $nurse_status = [
            'status' => "Closed",
            'referrer_owner' => $user_id
        ];
        $NurseData->where('id',$nurse_id)->update($nurse_status);
        return redirect('view_all_nurse')->with('message','Closed status Successfully');
    }

    //-----------add ccomment post method----//
    public function add_nurse_comment($user_id,$nurse_id,$desc)
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
        return redirect()->to($url);
        }
        $NurseData = new Nurses();
        //$leads->where('id',$id)->delete();
        $timestamp = date("d M, h:i A");
        $comment = "[".$timestamp."]".$desc."\n";
        $nurse_status = [
            'comment' => $comment
        ];
        $cmt_check = DB::select("SELECT comment FROM nurses_data WHERE id = '$nurse_id' ");
        $data['cmt_check'] = json_decode(json_encode($cmt_check), true);

        if(isset($data['cmt_check'][0]['comment']))
        {
            //$cmt_update = "UPDATE nurses_data SET comment = CONCAT(comment, '$comment') WHERE id = '$nurse_id' ";
            $cmt_update = DB::update("UPDATE nurses_data SET comment = CONCAT('$comment',comment) WHERE id = '$nurse_id' ");

            //$nurses_data->where('id',$nurse_id)->update($nurse_status);
        }
        else
        {
            //print_r($nurse_status);
            $NurseData->where('id',$nurse_id)->update($nurse_status);
        }
        return redirect()->back()->with('message', 'comment add Successfully');
   }

   public function close_nurse($user_id,$nurse_id,$reason,$desc)
   {
      $isLoggedIn = $this->isLoggedIn();
      if($isLoggedIn == 'false')
      {
          $url = url('/');
       return redirect()->to($url);
      }
      $NurseData = new Nurses();
      //$leads->where('id',$id)->delete();
      $nurses_status = [
         'status' => $reason,
         'remark' => $desc
      ];
      $NurseData->where('id',$nurse_id)->update($nurses_status);
      return redirect('/view_inprogress_nurse')->with('message', 'nurse status Closed Successfully');
      //return $this->viewAllLeads();
   }

    public function logout()
   {
       $request = new Request();
       //session()->destroy();
       session(['isLoggedIn' => 'false']);
       //$data = session()->all();
       //print_r($data);      
       return view('Admin/admin_login');
   }
}
