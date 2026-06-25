<?php

namespace App\Http\Controllers\B2BController;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Models\ActivityLog;
use App\Models\B2BProdRate;
use App\Models\UserDetails;
use App\Models\UserRegister;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use PDF;
use Mail;
use Session;
use DateTime;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Response;
use File;
use Storage;

//use other controler
use App\Http\Controllers\Leads\LeadController;


class B2BController extends Controller
{
    public function add(Request $request)
    {
        if($request->method() == 'GET')
        {
            $cities = DB::table('cities')->get()->toArray();
            $states = DB::table('states')->get()->toArray();
            $countries = DB::table('countries')->get()->toArray();
            return view('B2BCust.add-update-user',compact('cities','states','countries'));
        }
        elseif($request->method() == 'POST')
        {
            $validatedData = $request->validate([
                'contact_no' => 'required|unique:user_details,contact_no|numeric|digits_between:10,10',
                'secondary_contact_no' => 'required|numeric|digits_between:10,10',
                'pincode' => 'required|numeric|digits_between:6,6',
                'email' => 'required|unique:user_details,email|email',
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
                    'b2b_cust_img', 'public'
                );
                // array_push($certificates,$gst_path);
            }
            if($request->file('gst_certificate') != null)
            {
                $gst_path = $request->file('gst_certificate')->store(
                    'b2b_cust_gst', 'public'
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
                    'b2b_pan_card', 'public'
                );
                array_push($certificates,$pan_path);
            }
            else
            {
                array_push($certificates,null);
            }
            $userDetailsId = UserDetails::insertGetId(
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
                    'type' => 'b2buser',
                    'created_by' => session('username'),
                ]
            );
            $password = substr($request->get('name'),0,3).'25q5c';
            $userId = UserRegister::insertGetId(
                [                                        
                    'username' => $request->get('contact_no'),
                    'password' => $password,
                    'role' => 'b2buser',
                    'email_id_user' => $request->get('email'),
                    'contact_no' => $request->get('contact_no'),
                    'location_user' => $request->get('area'),
                    'user_city' => $request->get('city'),
                ]
            );
            UserDetails::where('id',$userDetailsId)->update(['user_id'=>$userId]);

            //create pdf view
            $pdf = PDF::loadView('PdfViews.user-details', compact('request','password'));

            //sent mail
            $message1 = 'Thank You!. You can login using this site http://intra.quali55care.com/prodweb/b2bcrm';
            Mail::send('B2BCust/userMail',compact('message1'),function($message) use($pdf,$request)
            {     
                $message->to($request->get('email'), 'B2B User')->subject('Registered Successfully');
                $message->attachData($pdf->output(),'UserDetails.pdf');
                $message->from('tempmailquali@gmail.com', 'Quali55Care');
            });
            return redirect('b2b-user-view-all');
        }
    }    

    public function viewAll(Request $request)
    {
        $flagState = DB::table('user_details')->select('flag')->distinct('flag')->get();
        // dd($flagState);
        $users = DB::table('user_details')
                    ->where('type','b2buser')
                    // ->where('flag','Active')
                    ->when($request->get('search_customer'),function($query) use($request){
                        $query->where(function($q)use($request) {
                            $q->where('user_details.name','LIKE','%'.$request->get('search_customer').'%');
                            $q->orWhere('user_details.contact_no','LIKE','%'.$request->get('search_customer').'%');
                            $q->orWhere('user_details.email','LIKE','%'.$request->get('search_customer').'%');
                        });
                    })
                    ->when($request->get('search_flag') && $request->get('search_flag')!='All',function($query) use($request){
                        $query->where('user_details.flag',$request->get('search_flag'));
                    })
                    ->get()->paginate(10);
        return view('B2BCust.view-all-users',compact('users','flagState'));
    }

    public function view(Request $request)
    {
        $user = DB::table('user_details')->where('type','b2buser')->where('flag','Active')->where('id',$request->get('id'))->get()->toArray();
        $cities = DB::table('cities')->get()->toArray();
        $states = DB::table('states')->get()->toArray();
        $countries = DB::table('countries')->get()->toArray();
        return view('B2BCust.view-user',compact('user','cities','states','countries'));
    }

    public function update(Request $request)
    {
        $certificates = array();
        // $profil_img = null;
        // dd($request->file('profile_img'));
        if($request->file('profile_img') != null)
        {
            $profil_img = $request->file('profile_img')->store(
                'b2b_cust_img', 'public'
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
                'b2b_cust_gst', 'public'
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
                'b2b_pan_card', 'public'
            );
            array_push($certificates,$pan_path);
        }
        else
        {
            array_push($certificates,$request->get('old_pan_card'));
        }

        UserDetails::where('id',$request->get('id'))->update(
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
        return redirect('b2b-user-view-all');
    }

    public function delete(Request $request)
    {
        UserDetails::where('id',$request->get('id'))->update(['flag'=>'Inactive']);
        return redirect('b2b-user-view-all');
    }

    public function active(Request $request){
        $id = $request->get('user_id');
        if(isset($id)){
            $getUser = DB::table('user_details')->where('id',$id)->first();
            UserDetails::where('id',$id)->update(['flag'=>'Active']);
            return redirect()->to('b2b-user-view-all')->with('message',$getUser->name.' activated..');
        }
    }


    public function passwordChange(Request $request){
        $loggedUserId = session('user_id');
        if(DB::table('user')->where('id',$loggedUserId)->where('password',$request->get('loggedin_password'))->exists())
        {
            if(DB::table('user_details')->where('id',$request->get('user_id'))->exists() && DB::table('user_details')->join('user','user_details.user_id','=','user.id')->where('user_details.id',$request->get('user_id'))->exists())
            {
                $password = $request->get('change_user_password');
                $userTable = DB::table('user_details')->where('id',$request->get('user_id'))->first();
                DB::table('user')->where('id',$userTable->user_id)->update(['password'=>$password]);
                DB::table('user_details')->where('id',$request->get('user_id'))->update(['forgot_pass_req'=>2]);
                $data = [
                    'userData'=>$userTable,
                    'password'=>$password
                ];
                Mail::send('B2BMail/password-change',compact('data'), function($message) use($userTable)
                {     
                    $message->to($userTable->email, 'B2B User')->subject('Password Changed Successfully');
                    $message->from('tempmailquali@gmail.com', 'Quali55Care');
                });
                $request->session()->flash('message', 'Password changed successfully');
                return redirect()->back();
            }else{
                $request->session()->flash('error', 'User Not Found');
                return redirect()->back();
            }
            
        }else{
            return redirect()->back()->withErrors(['loggedin_password'=>'Logged in user password wrong'])->withInput();
        }
    }



    public function userRates(Request $request)
    {
        $getB2bUser = DB::table('user')
                            ->where('role','=','b2buser')
                            ->get();
        $productList = DB::table('products')
                            ->where('flag','=','Active')
                            ->get();
        $b2bProductRate = DB::table('b2b_prod_rates')
                            ->select('b2b_prod_rates.*','products.product_name','user.username')
                            ->join('products','b2b_prod_rates.product_id','=','products.id')
                            ->join('user','b2b_prod_rates.b2b_user_id','=','user.id')
                            ->when($request->get('search_product'),function($query) use($request){
                                $query->where('products.product_name','LIKE','%'.$request->get('search_product').'%');
                            })
                            ->when($request->get('search_b2buser'),function($query) use($request){
                                $query->where('b2b_prod_rates.b2b_user_id','=',$request->get('search_b2buser'));
                            })
                            ->orderBy('id','DESC')
                            ->paginate(10);
        return view('B2BCust.userRates',compact('getB2bUser','productList','b2bProductRate'));
    }

    public function addProductRate(Request $request){
        DB::beginTransaction();
        try
        {
            
            $selectedProduct = $request->get('selected_product');
            $insertData = [];
            for ($i=0; $i <count($selectedProduct['id']) ; $i++) { 
                $sale_rate = 0;
                if($selectedProduct['sale_rate'][$i]!=null){
                    $sale_rate = $selectedProduct['sale_rate'][$i];
                }
                $insertData[] = [
                    'product_id'=>$selectedProduct['id'][$i],
                    'b2b_user_id'=>$request->get('selected_b2buser'),
                    'rate'=>$selectedProduct['rate'][$i],
                    'sale_rate'=>$sale_rate,
                    'created_by'=>session('user_id'),
                    'updated_by'=>session('user_id'),
                ];
            }
            B2BProdRate::insert($insertData);
            DB::commit();
            return redirect()->to('b2b-user-rate')->with('message','B2B Use product rate added successfully');
        }
        catch (Exception $ex) 
        {
            DB::rollBack();
            $file = fopen(public_path().'/tempLogfile'.date('Y-m-d').'.txt','a');
            fwrite($file,date('Y-m-d')."Exception: ".$ex);
            fwrite($file,"request_data".$request_dump);
            fclose($file);
            return redirect()->back()->with('error','Something Went Wrong! Please Try Again or Contact Administrator.');
        }
    }

    public function editProductRate(Request $request)
    {
        $request->validate(
        [
            'edit_id' => 'required|numeric',
        ],
        [
            'edit_id.required' => 'Something Went Wrong?',
            'edit_id.numeric' => 'Something Went Wrong?',
        ]);

        $id = $request->get('edit_id');
        $updateData = [
            'product_id'=>$request->get('edit_product'),
            'b2b_user_id'=>$request->get('edit_b2b_user'),
            'rate'=>$request->get('edit_rate'),
            'sale_rate'=>$request->get('edit_sale_rate'),
            'updated_by'=>session('user_id'),
        ];
        B2BProdRate::where('id',$id)->update($updateData);
        return redirect()->to('b2b-user-rate')->with('message','Product Updated successfully');
    }

    public function reomveProductRate(Request $request)
    {
        $request->validate(
            [
               'remove_id' => 'required|numeric',
            ],
            [
               'remove_id.required' => 'Something Went Wrong?',
               'remove_id.numeric' => 'Something Went Wrong?',
            ]);

        $id = $request->get('remove_id');
        B2BProdRate::where('id',$id)->delete();
        return redirect()->to('b2b-user-rate')->with('message','Product deleted successfully');
    }

}
?>