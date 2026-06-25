<?php

namespace App\Http\Controllers\ReferralController;
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


class ReferralController extends Controller
{

    public function referralsCount(){
        $referrals = DB::table('referralitems')->where('referralStatus','Received')->count();
        return $referrals;
    }
   //----------view all user-----------//
   public function viewAllReferrals(Request $request)
    {
        $referrals = DB::table('referralitems')
            ->when($request->get('filterpatientname'),function($query)use($request){
                $query->where('referralName',$request->get('filterpatientname'));
            })
            ->when($request->get('filterpatientcontact'),function($query)use($request){
                $query->where('mobileNo',$request->get('filterpatientcontact'));
            })
            ->when($request->get('filterfromdate') && $request->get('filterlastdate'),function($query)use($request){
                $query->whereBetween(DB::raw('date(insert_date)'),[$request->get('filterfromdate'),$request->get('filterlastdate')]);
            })
            ->when($request->get('filterlocations'),function($query)use($request){
                $query->whereIn('location',$request->get('filterlocations'));
            })
            ->when($request->get('filterreferredby'),function($query)use($request){
                $query->whereIn('referredBy',$request->get('filterreferredby'));
            })
            ->orderBy('id','DESC')->get()->paginate(10);
        $locations = DB::table('referralitems')->distinct('location')->select('location')->get();
        $referredby = DB::table('referralitems')->distinct('referredBy')->select('referredBy')->get();
        return view('ReferralManagement/viewAllReferrals',compact('referrals','locations','referredby'));
    }
    public function searchReferrals()
    {
        $start_date = $_POST['start_date'];
        session(['start_date'=>$start_date]);
        $last_date = $_POST['last_date'];
        session(['last_date'=>$last_date]);
        $referrals = DB::select("SELECT *FROM referralitems WHERE insert_date BETWEEN '$start_date' AND '$last_date' ORDER BY id DESC");
        $data['referrals'] = json_decode(json_encode($referrals), true);
        return view('ReferralManagement/viewAllReferrals',$data);
    }
    public function view_details($id)
    {
        $referral_details = DB::select("SELECT *FROM referralitems WHERE id =$id ORDER BY id DESC");
        $data['referral_details'] = json_decode(json_encode($referral_details),true);
        return view('ReferralManagement/view_referral_details',$data);
    }
    public function update_status()
    {
        $ref_id = $_POST['ref_id'];
        $status = $_POST['referralStatus'];
        $comment = $_POST['comment'];
        $update_data = DB::update("UPDATE referralitems SET referralstatus ='$status', comment ='$comment' WHERE id =$ref_id");
        return redirect('viewAllReferrals');
    }

    public function view_profile()
    {
        $user_id = session('user_id');
        //echo $user_id;
        $user_details = DB::select("SELECT * FROM user WHERE id = $user_id");
        $data['user_details'] = json_decode(json_encode($user_details),true);
        return view('admin/user_profile',$data);
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