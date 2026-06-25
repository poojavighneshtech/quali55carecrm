<?php

namespace App\Http\Controllers\B2BController;

use App\Models\ActivityLog;
use App\Models\B2BProdRate;
use App\Models\agentmaster;
use App\Models\UserRegister;
use PDF;
use Mail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
class AgentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //View All Agents
        $agents = DB::table('agent_master')->where('flag','Active')->paginate(10);
        return view('B2BCust.agents',compact('agents'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //Add New Agent View Here
        $cities = DB::table('cities')->get()->toArray();
        $states = DB::table('states')->get()->toArray();
        $countries = DB::table('countries')->get()->toArray();
        return view('B2BCust.add-update-agent',compact('cities','states','countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $validatedData = $request->validate([
            'contact_no' => 'required|unique:agent_master,contact_no|numeric|digits_between:10,10',
            'secondary_contact_no' => 'required|numeric|digits_between:10,10',
            'pincode' => 'required|numeric|digits_between:6,6',
            'email' => 'required|unique:agent_master,email|email',
            'area' => 'required',
            'city' => 'required',
         ],[
            'contact_no.required' => 'Contact No is required',
            'secondary_contact_no.required' => 'Secondary Contact No is required',
            'contact_no.unique' => 'Contact No is already exist',
            'contact_no.digits_between' => 'Contact Number should be minimum 10 digit',
            'secondary_contact_no.digits_between' => 'Contact Number should be minimum 10 digit',
            'pincode.required' => 'Pincode Must be numeric and 6 didgit',
            'pincode.numeric' => 'Pincode Must be numeric and 6 didgit',
            'pincode.digits_between' => 'Pincode Must be numeric and 6 didgit',
            'email.email'=>'Email is incorrect',
            'email.unique'=>'Email already exist',
            'city.required' => 'City No is required',
            'area.required' => 'Area No is required'
        ]);

        $certificates = array();
        $profile_img = null;
        if($request->file('profile_img') != null)
        {
            $profile_img = $request->file('profile_img')->store(
                'agent_img', 'public'
            );
            // array_push($certificates,$gst_path);
        }
        if($request->file('gst_certificate') != null)
        {
            $gst_path = $request->file('gst_certificate')->store(
                'agent_gst', 'public'
            );
            array_push($certificates,$gst_path);
        }
        else
        {
            array_push($certificates,null);
        }
        if($request->file('pan_card') != null)
        {
            $pan_path = $request->file('pan_card')->store(
                'agent_pan_card', 'public'
            );
            array_push($certificates,$pan_path);
        }
        else
        {
            array_push($certificates,null);
        }
        $userDetailsId = agentmaster::insertGetId(
            [
                'name' => $request->get('name'),
                'contact_no' => $request->get('contact_no'),
                'secondary_contact_no' => $request->get('secondary_contact_no'),
                'whats_app_1'=>$request->get('is_whats_app_1'),
                'whats_app_2'=>$request->get('is_whats_app_2'),
                'addr_line_1' => $request->get('addr_line_1'),
                'addr_line_2' => $request->get('addr_line_2'),
                'landmark' => $request->get('landmark'),
                'area' => $request->get('area'),
                'city' => $request->get('city'),
                'state' => $request->get('state'),
                'country' => $request->get('country'),
                'pincode' => $request->get('pincode'),
                'email' => $request->get('email'),
                'second_email' => $request->get('second_email'),
                'profile_img' =>$profile_img,
                'company_type' => $request->get('company_type'),
                'gst_no' => $request->get('gst_no'),
                'certificates' => implode(',',$certificates),
                'flag' => 'Active',
                'type' => 'agent',
                'created_by' => session('username'),
            ]
        );
        $password = substr($request->get('name'),0,3).'25q5c';
        $userId = UserRegister::insertGetId(
            [                                        
                'username' => $request->get('contact_no'),
                'password' => $password,
                'role' => 'agent',
                'email_id_user' => $request->get('email'),
                'contact_no' => $request->get('contact_no'),
                'location_user' => $request->get('area'),
                'user_city' => $request->get('city'),
            ]
        );
        agentmaster::where('id',$userDetailsId)->update(['user_id'=>$userId]);

        //create pdf view
        $pdf = PDF::loadView('PdfViews.user-details', compact('request','password'));

        //sent mail
        $message1 = 'Thank You!. You can login using this site http://intra.quali55care.com/devweb/b2bcrm';
        Mail::send('B2BCust/userMail',compact('message1'),function($message) use($pdf,$request)
        {     
            $message->to($request->get('email'), 'Agent')->subject('Registered Successfully');
            $message->attachData($pdf->output(),'agentmaster.pdf');
            $message->from('tempmailquali@gmail.com', 'Quali55Care');
        });
        return redirect('agents');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $user = DB::table('agent_master')->where('type','agent')->where('flag','Active')->where('id',$id)->get()->toArray();
        $cities = DB::table('cities')->get()->toArray();
        $states = DB::table('states')->get()->toArray();
        $countries = DB::table('countries')->get()->toArray();
        return view('B2BCust.view-agent',compact('user','cities','states','countries'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $user = DB::table('agent_master')->where('type','agent')->where('flag','Active')->where('id',$id)->get()->toArray();
        $cities = DB::table('cities')->get()->toArray();
        $states = DB::table('states')->get()->toArray();
        $countries = DB::table('countries')->get()->toArray();
        return view('B2BCust.view-agent',compact('user','cities','states','countries'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        // dd($request->all());
        $certificates = array();
        // $profil_img = null;
        // dd($request->file('profile_img'));
        if($request->file('profile_img') != null)
        {
            $profil_img = $request->file('profile_img')->store(
                'agent_img', 'public'
            );
            // array_push($certificates,$gst_path);
        }
        else
        {
            $profil_img = $request->get('old_profile_img');
        }
        // $request->file('gst_certificate')->path();
        if($request->file('gst_certificate') != null)
        {
            $gst_path = $request->file('gst_certificate')->store(
                'agent_gst', 'public'
            );
            array_push($certificates,$gst_path);
        }
        else
        {
            array_push($certificates,$request->get('old_gst_certificate'));
        }
        if($request->file('pan_card') != null)
        {
            $pan_path = $request->file('pan_card')->store(
                'agent_pan_card', 'public'
            );
            array_push($certificates,$pan_path);
        }
        else
        {
            array_push($certificates,$request->get('old_pan_card'));
        }

        agentmaster::where('id',$id)->update(
            [
                'name' => $request->get('name'),
                'contact_no' => $request->get('contact_no'),
                'secondary_contact_no' => $request->get('secondary_contact_no'),
                'whats_app_1'=>$request->get('is_whats_app_1'),
                'whats_app_2'=>$request->get('is_whats_app_2'),
                'addr_line_1' => $request->get('addr_line_1'),
                'addr_line_2' => $request->get('addr_line_2'),
                'landmark' => $request->get('landmark'),
                'area' => $request->get('area'),
                'city' => $request->get('city'),
                'state' => $request->get('state'),
                'country' => $request->get('country'),
                'pincode' => $request->get('pincode'),
                'email' => $request->get('email'),
                'second_email' => $request->get('second_email'),
                'company_type' => $request->get('company_type'),
                'gst_no' => $request->get('gst_no'),
                'profile_img' => $profil_img,
                'certificates' => implode(',',$certificates),
                'created_by' => session('username'),
            ]
        );
        return redirect('agents');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
