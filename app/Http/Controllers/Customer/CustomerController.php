<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CustomerController extends Controller
{
    public function getBankDetails(Request $request,$link_id)
    {
        if(DB::table('link_cust_details')->where('link_id',$link_id)->where('link_status',0)->exists())
        {
            $getLinkDetails = DB::table('link_cust_details')->where('link_id',$link_id)->first();
            $linkRowId = $getLinkDetails->id;
            $linkStatus = $getLinkDetails->link_status;
            
            $orderIds = $getLinkDetails->order_ids;
            $cust_id = $getLinkDetails->customer_id;
            $customerName = $getLinkDetails->customer_name;
            $customerContact = $getLinkDetails->primary_contact_no;

            $createdAt = Carbon::parse($getLinkDetails->created_at);
            $currentTime = Carbon::now();
            $diifHour = $createdAt->diffInHours($currentTime);

            if($diifHour<48){
                if($request->method()=="GET"){
                    return view('CustomerView.customer_bank_details',compact('cust_id','link_id'));
                }
                elseif($request->method()=="POST"){
                    if($request->get('submit')=='bank_details'){
                        $validated = $request->validate([
                            'bank_name'=>'required',
                            'bank_branch_name'=>'required',
                            'bank_account_number'=>'required',
                            'bank_confirm_account_number'=>'required|same:bank_account_number',
                            'bank_ifsc_code'=>'required|min:11|max:11:|regex:/^[A-Za-z]{4}\d{7}$/',
                            'bank_account_holder_name'=>'required',
                            'bank_acount_type'=>'required',
                            'ifsc_status'=>'regex:(true)',
                        ],
                        [
                            'bank_ifsc_code.min'=>'IFSC Code must be 11 digit',
                            'bank_ifsc_code.max'=>'IFSC Code must be 11 digit',
                            'bank_ifsc_code.regex'=>'IFSC Code First 6 character followed by 5 digits',
                            'ifsc_status.regex'=>'IFSC code not found',
                            'bank_confirm_account_number.same'=>'Bank account number does not match',
                        ]);

                        $updateData = [
                            'bank_name'=>$request->get('bank_name'),
                            'account_holder_name'=>$request->get('bank_account_holder_name'),
                            'account_number'=>$request->get('bank_account_number'),
                            'branch_name'=>$request->get('bank_branch_name'),
                            'ifsc_code'=>$request->get('bank_ifsc_code'),
                            'account_type'=>$request->get('bank_acount_type'),
                        ];
                        DB::table('customer_details')->where('cust_id',$cust_id)->update($updateData);

                        $wpTemplateName = "cust_deposit_bank_details";
                    }
                    elseif($request->get('submit')=='cheque_details')
                    {  
                        $request->validate([
                            'capture_cheq_img' => 'required|image',
                        ],[
                            'capture_cheq_img.image'=>'file must be a image',
                        ]);

                        $cheqImg = $request->file('capture_cheq_img');
                        $cheqImgExt = $cheqImg->getClientOriginalExtension();
                        
                        $path = Storage::disk('public')->putFileAs('cust_bank_details',$request->file('capture_cheq_img'),$cust_id.'.'.$cheqImgExt);
                        $imgPath = "http://intra.quali55care.com/".config('app.app_env')."/eflow/public/storage/".$path;
                        $wpTemplateName = "cust_deposit_bank_image";

                    }

                    //send bank details to buisness head and staff
                    $buisnessHeadId = config('app.business_head_id');
                    $buisnessHeadDetails = DB::table('user')->where('id',$buisnessHeadId)->first();
                    $buisnessHeadContact = $buisnessHeadDetails->contact_no;
                    
                    $accountsId = config('app.accounts_id');
                    $accountsDetails = DB::table('user')->where('id',$accountsId)->first();
                    $accountsContact = $accountsDetails->contact_no;

                    $contactArr = [$buisnessHeadContact,$accountsContact];

                    foreach ($contactArr as $key => $contact) {
                        $url = "https://lj7724mli5.execute-api.ap-south-1.amazonaws.com/prod/sendwhatsappmsg";
                        $curl = curl_init();
                        curl_setopt($curl, CURLOPT_URL, $url);
                        curl_setopt($curl, CURLOPT_POST, true);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        
                        $headers = array(
                            "Accept: application/json",
                            "Content-Type: application/json",
                        );
                        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                        if(config('app.app_env') == 'devweb')
                        {
                            $contactNo = config('app.developer_contact');
                        }
                        if($request->get('submit')=="bank_details")
                        {
                            $templatePara = [   
                                ["type"=> "text","text"=> "$orderIds"],
                                ["type"=> "text","text"=> "$customerName"],
                                ["type"=> "text","text"=> "$customerContact"],
                                ["type"=> "text","text"=> '"'.$request->get('bank_name').'"'],
                                ["type"=> "text","text"=> '"'.$request->get('bank_branch_name').'"'],
                                ["type"=> "text","text"=> '"'.$request->get('bank_account_number').'"'],
                                ["type"=> "text","text"=> '"'.strtoupper($request->get('bank_ifsc_code')).'"'],
                                ["type"=> "text","text"=> '"'.$request->get('bank_acount_type').'"']
                            ];
                        }else{
                            $templatePara = [   
                                ["type"=> "text","text"=> "$orderIds"],
                                ["type"=> "text","text"=> "$customerName"],
                                ["type"=> "text","text"=> "$customerContact"],
                                ["type"=> "text","text"=> "$imgPath"],
                            ];
                        }
                        $data =[
                            "portno"=>"11140",
                            "namespace"=>"b9a23cb4_89ed_4fe2_b849_20775908ff5e",
                            "countrycode"=> "91",
                            "mobileno"=> "$contactNo",
                            //"headerimageurl"=>"https://s3.ap-south-1.amazonaws.com/quali55care.com/assets/RESOURCES/logo_quli5care.png",
                            "templatename" => "$wpTemplateName",
                            "templateparams" =>$templatePara,
                        ];
                        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                        $resp = curl_exec($curl);
                        curl_close($curl);
                    }
                    DB::table('link_cust_details')->where('id',$linkRowId)->update(['link_status'=>1]);
                    return view('Alert_Templates.success_template');
                }
            }
            else{
                DB::table('link_cust_details')->where('id',$linkRowId)->update(['link_status'=>1]);
                return view('Alert_Templates.failed_template');
            }
        }else{
            return view('Alert_Templates.failed_template');
        }
    }

        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $customers = DB::table('customer_details')
            ->when($request->get('filter_customer_name'),function($query)use($request){
                $query->where('customer_name','LIKE','%'.$request->get('filter_customer_name').'%');
            })  
            ->when($request->get('filter_contact_no'),function($query)use($request){
                $query->where('primary_contact_no',$request->get('filter_contact_no'));
            })
            ->when($request->get('filter_customer_id'),function($query)use($request){
                $query->where('cust_id',$request->get('filter_customer_id'));
            })
            ->when($request->get('filter_customer_type'),function($query)use($request){
                if($request->get('filter_customer_type') !="All"){
                    $query->where('customer_type',$request->get('filter_customer_type'));
                }
            })
            ->orderBy(DB::raw('DATE(created_at)'),'DESC')->paginate(10);
        return view('customers.index',compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
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
        $customer = DB::table('customer_details')->where('cust_id',$id)->first();
        return view("customers.view",compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        //
        $customer = DB::table('customer_details')->where('cust_id',$id)->first();
        return view("customers.edit",compact('customer'));
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
        DB::table('customer_details')->where('cust_id',$id)->update([
            'address_line_1'=>$request->get('address_line_1'),
            'address_line_2'=>$request->get('address_line_2'),
            'landmark'=>$request->get('landmark'),
            'area'=>$request->get('area'),
            'city'=>$request->get('city'),
            'pincode'=>$request->get('pincode'),
            'state'=>$request->get('state'),
            'country'=>$request->get('country'),
            'email_id'=>$request->get('email_id'),
            'secondary_contact_no'=>$request->get('secondary_contact_no'),
        ]);
        return redirect('customer-master')->with('message','Updated!');
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
