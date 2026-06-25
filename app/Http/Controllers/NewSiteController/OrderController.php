<?php

namespace App\Http\Controllers\NewSiteController;

//controller
use App\Http\Controllers\Controller;
//model
use App\Models\customer_detail;
use App\Models\lead;

//traits
use App\Http\Traits\ApiResponser;

//Other
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;


class OrderController extends Controller
{
    use ApiResponser;
    public function getOrder(Request $request){
        if(!$request->exists('data')){
            return $this->sendError('',"Request does not exists parameter data");
        }
        if(!$request->has('data') && $request->get('data')!=null){
            return $this->sendError('',"parameter data can not be null");
        }
        $data = json_decode($request->get('data'),true);
        //return $data;
        $validateData = Validator::make($data,[
            'id'=>'required',
            'customer.mobileno'=>'required',
            'customer.name'=>'required',
            'shippingaddress.addressline1'=>'required',
            'shippingaddress.cityname'=>'required',
            'shippingaddress.pincode'=>'required',
            'cart.handovermode'=>'required',
            'cart.lineitems.*.billingtype'=>'required',
            'cart.lineitems.*.quantity'=>'required',
            'cart.lineitems.*.productid'=>'required',
            'cart.lineitems.*.rentperiod'=>'required',
            'cart.lineitems.*.rate'=>'required',
            'cart.lineitems.*.depositrate'=>'required',
            
        ],
        [
            'id.required'=>'order id can not empty',
            'customer.name'=>'customer name no can not be empty',
            'customer.mobileno'=>'customer mobile no can not be empty',
            'shippingaddress.addressline1.required'=>'shipping address addressline1 can not be empty',
            'shippingaddress.cityname.required'=>'shipping address cityname can not be empty',
            'shippingaddress.pincode.required'=>'shipping address pincode can not be empty',
            'cart.handovermode.required'=>'handover mode can not be empty',
            'cart.lineitems.*.billingtype.required'=>'product billing type can not be empty',
            'cart.lineitems.*.productid'=>'product id can not be empty',
            'cart.lineitems.*.rentperiod'=>'product rent period can not be empty',
            'cart.lineitems.*.quantity'=>'product rate can not be empty',
            'cart.lineitems.*.rate'=>'product rate can not be empty',
            'cart.lineitems.*.depositrate'=>'product deposit rate can not be empty',
        ]);
        if($validateData->fails()){
            return $this->sendError('Validation Error.',$validateData->errors());       
        }

        // $productData = $data['cart'];
        // $validateProductId = Validator::make($productData,[
        //     'lineitems.*.productid'=>[
        //         'required',
        //         'exists:products,web_product_id',
        //     ]
        // ]);

        try {   
            DB::beginTransaction();
            //code...
            $today = Carbon::now()->toDateString();

            $data = json_decode($request->get('data'),true);
            $orderId = $data['id'];
            $billingAddress = $data['billingaddress'];
            $cart = $data['cart'];
            $products = $cart['lineitems'];
            $shippingAddress = $data['shippingaddress'];

            //customer update or insert
            $customer = $data['customer'];  
            $custName = $customer['name'];
            $custContact = $customer['mobileno'];
            $custEmail = $customer['email'];
            $custAddress = explode(',',$shippingAddress['addressline1']);

            $custLocation = $custAddress[3];
            $custArea = $custAddress[2];
            $custLandmark = $custAddress[1];
            $custAddressLine1 = $shippingAddress['addressline1'];
            $custPincode = $shippingAddress['pincode'];
            $custCity = $shippingAddress['cityname'];
            $custLatitude = $shippingAddress['latitude'];
            $custLongitude = $shippingAddress['longitude'];

           
            //Process data for lead insert
            $productIds = [];
            $productQuantitys = [];
            $productMonths = [];
            $productDelDate = [];
            $productSaleRental = [];
            $productOfferedRent = [];
            $productOfferedRentTotal = [];
            $productDeposit = [];
            $productDepositTotal = [];
            $productTransport = [];
            
            foreach ($products as $key => $product) {
                $webProductId = $product['productid'];
                $quantity = $product['quantity'];
                $months = $product['rentperiod'];
                $productRent = $product['rate'];
                $deposit = $product['depositrate'];
                $transport = $product['transportationcost'];

                //product id exists or not
                if(DB::table('products')->where('web_product_id',$webProductId)->where('flag','Active')->exists()){
                    $masterProduct = DB::table('products')->where('web_product_id',$webProductId)->get();
                    array_push($productIds,$masterProduct[0]->id);
                    array_push($productQuantitys,$quantity);
                    array_push($productMonths,$months);
                    array_push($productDelDate,$today);
                    
                    if($product['billingtype']=="RENT"){
                        array_push($productSaleRental,"Rental");
                    }else{
                        array_push($productSaleRental,"Sale");
                    }
    
                    array_push($productOfferedRent,$productRent);
                    $productRentTotal = $productRent*$quantity;
                    array_push($productOfferedRentTotal,$productRentTotal);
    
                    array_push($productDeposit,$deposit);
                    $depositTotal = $deposit*$quantity;
                    array_push($productDepositTotal,$depositTotal);
    
                    array_push($productTransport,$transport);
                
                }
                else
                {
                    $errorData = [
                        'webproductid'=>$webProductId,
                        'productname'=>$product['productname'],
                        'error'=>"web product id not found Or Product Inactive".$product['productname']." (".$webProductId.")",
                    ];

                    //send wp message to backend it team as error occourd
                    $error = "web product id not found Or Product Inactive - ".$product['productname']." (".$webProductId.")";
                    $templateData = [
                        'order_id'=>$orderId,
                        'date'=>Carbon::today()->format('j F Y'),
                        'customer_name'=>$custName,
                        'customer_contact'=>$custContact,
                        'error'=>$error,
                    ];
                    $backendTeamContacts = [
                        config('app.it_rahul'),
                        config('app.it_abhishek'),
                       // config('app.it_vivek'),
                    ];
                    $getContacts = DB::table('user')->whereIn('id',$backendTeamContacts)->get()->pluck('contact_no');
                    $contactNo = $getContacts;
                    //send error wp messaage backend IT team 
                    $this->errorWp($getContacts,$templateData);
                    
                    Log::channel('error_log')->error(json_encode($errorData));

                    return $this->sendError('',"Something went wrong");
                }

            }

            //get web user
            $webUser = config('app.web_lead_user') ;
            $getWebUser = DB::table('user')->where('id',$webUser)->get();
            
            $custUpdateData = [
                'customer_name'=>$custName,
                'location'=>$custLocation,
                'address_line_1'=>$custAddressLine1,
                'area'=>$custArea,
                'landmark'=>$custLandmark,
                'city'=>$custCity,
                'pincode'=>$custPincode,
                'state'=>"Maharastra",
                'country'=>"India",
                'cust_date'=>$today,
                'primary_contact_no'=>$custContact,
                'email_id'=>$custEmail,
                'addr_is_same'=>"No",
                'latitude'=>$custLatitude,
                'longitude'=>$custLongitude,
                'refered_by'=>"Web",
                'customer_type'=>"Individual",
                'cust_source'=>"Web",
                'cust_created_at'=>Carbon::now(),
                'cust_owner'=>$getWebUser[0]->id,
            ];

            $CustomerDetails =  customer_detail::updateOrCreate(
                                [
                                    'primary_contact_no'=>$custContact,
                                ],[
                                    'customer_name'=>$custName,
                                    'location'=>$custLocation,
                                    'address_line_1'=>$custAddressLine1,
                                    'area'=>$custArea,
                                    'landmark'=>$custLandmark,
                                    'city'=>$custCity,
                                    'pincode'=>$custPincode,
                                    'state'=>"Maharastra",
                                    'country'=>"India",
                                    'cust_date'=>$today,
                                    'primary_contact_no'=>$custContact,
                                    'email_id'=>$custEmail,
                                    'addr_is_same'=>"No",
                                    'latitude'=>$custLatitude,
                                    'longitude'=>$custLongitude,
                                    'refered_by'=>"Web",
                                    'customer_type'=>"Individual",
                                    'cust_source'=>"Web",
                                    'cust_created_at'=>Carbon::now(),
                                    'cust_owner'=>$getWebUser[0]->id,
                                ]);

            
            //lead insert in table
            $insertData = [
                'customer_id'=>$CustomerDetails['cust_id'],
                'creation_date'=>$today,
                'converted_at'=>Carbon::now(),
                'equipment_requirement'=>json_encode($productIds),
                'equipment_qty'=>json_encode($productQuantitys),
                'months'=>json_encode($productMonths),
                'del_date'=>json_encode($productDelDate),
                'sale_rental'=>json_encode($productSaleRental),
                'offered_rent'=>json_encode($productOfferedRent),
                'offered_rent_total'=>json_encode($productOfferedRentTotal),
                'deposite'=>json_encode($productDeposit),
                'deposite_total'=>json_encode($productDepositTotal),
                'transport'=>json_encode($productTransport),
                'lead_source'=>"New User Site",
                'lead_status'=>"Converted",
                'priority'=>0,
                'lead_value'=>$cart['netpayable'],
                'lead_owner'=> $getWebUser[0]->id,
                'payment_mode'=>$data['paymentlist'][0]['paymentmode'],
                'generated_from'=>"Web",
                'created_by'=> $getWebUser[0]->username,
            ];
            //$LeadDetails = lead::create($insertData);
            DB::commit();
            return $this->sendSuccess('Order Posted successfully','');
        } catch (\Throwable $th) {
            DB::rollBack();
            $errorTruncate = substr($th,0,500);
            $errorData = [
                'orderid'=>$orderId,
                'error'=>$errorTruncate,
            ];
            $apiData = json_decode($request->get('data'),true);
           
            $templateData = [
                'order_id'=>$apiData['id'],
                'date'=>Carbon::today()->format('j F Y'),
                'customer_name'=>$apiData['customer']['name'],
                'customer_contact'=>$apiData['customer']['mobileno'],
                'error'=>substr($th,0,100),
            ];
            $backendTeamContacts = [
                config('app.it_rahul'),
                config('app.it_abhishek'),
               // config('app.it_vivek'),
            ];
            $getContacts = DB::table('user')->whereIn('id',$backendTeamContacts)->get()->pluck('contact_no');
            $contactNo = $getContacts;

            //send error wp messaage backend IT team 
            $this->errorWp($getContacts,$templateData);
            
            Log::channel('error_log')->error(json_encode($errorData));
            return $this->sendError("somthing went wrong",'data');
        }
    }

    public function generateLead(Request $request){
        if(!$request->exists('data')){
            return $this->sendError('',"Request does not exists parameter data");
        }
        if(!$request->has('data') && $request->get('data')!=null){
            return $this->sendError('',"parameter data can not be null");
        }
        $data = json_decode($request->get('data'),true);
        //return $data;
        $validateData = Validator::make($data,[
            'id'=>'required',
            'customer.mobileno'=>'required',
            'customer.name'=>'required',
            'shippingaddress.addressline1'=>'required',
            'shippingaddress.cityname'=>'required',
            'shippingaddress.pincode'=>'required',
            'cart.handovermode'=>'required',
            'cart.lineitems.*.billingtype'=>'required',
            'cart.lineitems.*.quantity'=>'required',
            'cart.lineitems.*.productid'=>'required',
            'cart.lineitems.*.rentperiod'=>'required',
            'cart.lineitems.*.rate'=>'required',
            'cart.lineitems.*.depositrate'=>'required',
            
        ],
        [
            'id.required'=>'order id can not empty',
            'customer.name'=>'customer name no can not be empty',
            'customer.mobileno'=>'customer mobile no can not be empty',
            'shippingaddress.addressline1.required'=>'shipping address addressline1 can not be empty',
            'shippingaddress.cityname.required'=>'shipping address cityname can not be empty',
            'shippingaddress.pincode.required'=>'shipping address pincode can not be empty',
            'cart.handovermode.required'=>'handover mode can not be empty',
            'cart.lineitems.*.billingtype.required'=>'product billing type can not be empty',
            'cart.lineitems.*.productid'=>'product id can not be empty',
            'cart.lineitems.*.rentperiod'=>'product rent period can not be empty',
            'cart.lineitems.*.quantity'=>'product rate can not be empty',
            'cart.lineitems.*.rate'=>'product rate can not be empty',
            'cart.lineitems.*.depositrate'=>'product deposit rate can not be empty',
        ]);
        if($validateData->fails()){
            return $this->sendError('Validation Error.',$validateData->errors());       
        }

        // $productData = $data['cart'];
        // $validateProductId = Validator::make($productData,[
        //     'lineitems.*.productid'=>[
        //         'required',
        //         'exists:products,web_product_id',
        //     ]
        // ]);

        try {   
            DB::beginTransaction();
            //code...
            // $today = Carbon::now()->toDateString();
            $today = Carbon::parse($data['createdat'])->toDateString();

            $data = json_decode($request->get('data'),true);
            $orderId = $data['id'];
            $billingAddress = $data['billingaddress'];
            $cart = $data['cart'];
            $products = $cart['lineitems'];
            $shippingAddress = $data['shippingaddress'];

            //customer update or insert
            $customer = $data['customer'];  
            $custName = $customer['name'];
            $custContact = $customer['mobileno'];
            $custEmail = $customer['email'];
            $custAddress = explode(',',$shippingAddress['addressline1']);
            $custCity = $shippingAddress['cityname'];
            $custLocation = $shippingAddress['cityname'];
            $custArea = $shippingAddress['cityname'];
            $custLandmark = $shippingAddress['cityname'];
            // if(isset($custAddress[3]))
            // {
                
            //     $custLocation = $custAddress[3];
            // }
            // else
            // {
            //     $custLocation = "";
            // }
            // if(isset($custAddress[3]))
            // {
                
            //     $custArea = $custAddress[2];
            // }
            // else
            // {
            //     $custArea = "";
            // }
            // if(isset($custAddress[3]))
            // {
                
            //     $custLandmark = $custAddress[1];
            // }
            // else
            // {
            //     $custLandmark = "";
            // }
            $custAddressLine1 = $shippingAddress['addressline1'];
            $custPincode = $shippingAddress['pincode'];
            $custCity = $shippingAddress['cityname'];
            $custLatitude = $shippingAddress['latitude'];
            $custLongitude = $shippingAddress['longitude'];

           
            //Process data for lead insert
            $productIds = [];
            $productQuantitys = [];
            $productMonths = [];
            $productDelDate = [];
            $productSaleRental = [];
            $productOfferedRent = [];
            $productOfferedRentTotal = [];
            $productDeposit = [];
            $productDepositTotal = [];
            $productTransport = [];
            
            foreach ($products as $key => $product) {
                $webProductId = $product['productid'];
                $quantity = $product['quantity'];
                $months = $product['rentperiod'];
                $productRent = $product['rate'];
                $deposit = $product['depositrate'];
                $transport = $product['transportationcost'];

                //product id exists or not
                if(DB::table('products')->where('web_product_id',$webProductId)->where('flag','Active')->exists()){
                    $masterProduct = DB::table('products')->where('web_product_id',$webProductId)->get();
                    array_push($productIds,$masterProduct[0]->id);
                    array_push($productQuantitys,$quantity);
                    array_push($productMonths,$months);
                    array_push($productDelDate,$today);
                    
                    if($product['billingtype']=="RENT"){
                        array_push($productSaleRental,"Rental");
                    }else{
                        array_push($productSaleRental,"Sale");
                    }
    
                    array_push($productOfferedRent,$productRent);
                    $productRentTotal = $productRent*$quantity;
                    array_push($productOfferedRentTotal,$productRentTotal);
    
                    array_push($productDeposit,$deposit);
                    $depositTotal = $deposit*$quantity;
                    array_push($productDepositTotal,$depositTotal);
    
                    array_push($productTransport,$transport);
                
                }
                else
                {
                    $errorData = [
                        'webproductid'=>$webProductId,
                        'productname'=>$product['productname'],
                        'error'=>"web product id not found Or Product Inactive".$product['productname']." (".$webProductId.")",
                    ];

                    //send wp message to backend it team as error occourd
                    $error = "web product id not found Or Product Inactive - ".$product['productname']." (".$webProductId.")";
                    $templateData = [
                        'order_id'=>$orderId,
                        'date'=>Carbon::today()->format('j F Y'),
                        'customer_name'=>$custName,
                        'customer_contact'=>$custContact,
                        'error'=>$error,
                    ];
                    $backendTeamContacts = [
                        config('app.it_rahul'),
                        config('app.it_abhishek'),
                       // config('app.it_vivek'),
                    ];
                    $getContacts = DB::table('user')->whereIn('id',$backendTeamContacts)->get()->pluck('contact_no');
                    $contactNo = $getContacts;
                    //send error wp messaage backend IT team 
                    $this->errorWp($getContacts,$templateData);
                    
                    Log::channel('error_log')->error(json_encode($errorData));

                    return $this->sendError('',"Something went wrong");
                }

            }

            //get web user
            $webUser = config('app.web_lead_user') ;
            $getWebUser = DB::table('user')->where('id',$webUser)->get();
            
            $custUpdateData = [
                'customer_name'=>$custName,
                'location'=>$custLocation,
                'address_line_1'=>$custAddressLine1,
                'area'=>$custArea,
                'landmark'=>$custLandmark,
                'city'=>$custCity,
                'pincode'=>$custPincode,
                'state'=>"Maharastra",
                'country'=>"India",
                'cust_date'=>$today,
                'primary_contact_no'=>$custContact,
                'email_id'=>$custEmail,
                'addr_is_same'=>"No",
                'latitude'=>$custLatitude,
                'longitude'=>$custLongitude,
                'refered_by'=>"Web",
                'customer_type'=>"Individual",
                'cust_source'=>"New User Site",
                'cust_created_at'=>Carbon::now(),
                'cust_owner'=>$getWebUser[0]->id,
            ];

            $CustomerDetails =  customer_detail::updateOrCreate(
                                [
                                    'primary_contact_no'=>$custContact,
                                ],[
                                    'customer_name'=>$custName,
                                    'location'=>$custLocation,
                                    'address_line_1'=>$custAddressLine1,
                                    'area'=>$custArea,
                                    'landmark'=>$custLandmark,
                                    'city'=>$custCity,
                                    'pincode'=>$custPincode,
                                    'state'=>"Maharastra",
                                    'country'=>"India",
                                    'cust_date'=>$today,
                                    'primary_contact_no'=>$custContact,
                                    'email_id'=>$custEmail,
                                    'addr_is_same'=>"No",
                                    'latitude'=>$custLatitude,
                                    'longitude'=>$custLongitude,
                                    'refered_by'=>"Web",
                                    'customer_type'=>"Individual",
                                    'cust_source'=>"New User Site",
                                    'cust_created_at'=>Carbon::now(),
                                    'cust_owner'=>$getWebUser[0]->id,
                                ]);

            
            //lead insert in table
            $insertData = [
                'customer_id'=>$CustomerDetails['cust_id'],
                'web_order_id'=>$orderId,
                'creation_date'=>$today,
                'converted_at'=>Carbon::now(),
                'equipment_requirement'=>json_encode($productIds),
                'equipment_qty'=>json_encode($productQuantitys),
                'months'=>json_encode($productMonths),
                'del_date'=>json_encode($productDelDate),
                'sale_rental'=>json_encode($productSaleRental),
                'offered_rent'=>json_encode($productOfferedRent),
                'offered_rent_total'=>json_encode($productOfferedRentTotal),
                'deposite'=>json_encode($productDeposit),
                'deposite_total'=>json_encode($productDepositTotal),
                'transport'=>json_encode($productTransport),
                'lead_source'=>"New User Site",
                'lead_status'=>"Converted",
                'priority'=>0,
                'lead_value'=>$cart['netpayable'],
                'lead_owner'=> $getWebUser[0]->id,
                'payment_mode'=>$data['paymentlist'][0]['paymentmode'],
                'reference_id'=>$data['paymentlist'][0]['paymentreference'],
                'remark'=>$data['paymentlist'][0]['paymentremarks'],
                'generated_from'=>"Web",
                'flag'=>"Web",
                'created_by'=> $getWebUser[0]->username,
            ];
            $LeadDetails = lead::updateOrCreate(
                [
                    'web_order_id'=>$orderId,
                ],
                [
                    'customer_id'=>$CustomerDetails['cust_id'],
                    'web_order_id'=>$orderId,
                    'creation_date'=>$today,
                    'converted_at'=>Carbon::now(),
                    'equipment_requirement'=>json_encode($productIds),
                    'equipment_qty'=>json_encode($productQuantitys),
                    'months'=>json_encode($productMonths),
                    'del_date'=>json_encode($productDelDate),
                    'sale_rental'=>json_encode($productSaleRental),
                    'offered_rent'=>json_encode($productOfferedRent),
                    'offered_rent_total'=>json_encode($productOfferedRentTotal),
                    'deposite'=>json_encode($productDeposit),
                    'deposite_total'=>json_encode($productDepositTotal),
                    'transport'=>json_encode($productTransport),
                    'lead_source'=>"New User Site",
                    'lead_status'=>"Converted",
                    'priority'=>0,
                    'lead_value'=>$cart['netpayable'],
                    'lead_owner'=> $getWebUser[0]->id,
                    'payment_mode'=>$data['paymentlist'][0]['paymentmode'],
                    'reference_id'=>$data['paymentlist'][0]['paymentreference'],
                    'remark'=>$data['paymentlist'][0]['paymentremarks'],
                    'generated_from'=>"Web",
                    'flag'=>"Web",
                    'created_by'=> $getWebUser[0]->username,
                ]
            );
            DB::commit();
            return $this->sendSuccess('Order Posted successfully','');
        } catch (\Throwable $th) {
            DB::rollBack();
            $errorTruncate = substr($th,0,500);
            $errorData = [
                'orderid'=>$orderId,
                'error'=>$errorTruncate,
            ];
            $apiData = json_decode($request->get('data'),true);
           
            $templateData = [
                'order_id'=>$apiData['id'],
                'date'=>Carbon::today()->format('j F Y'),
                'customer_name'=>$apiData['customer']['name'],
                'customer_contact'=>$apiData['customer']['mobileno'],
                'error'=>substr($th,0,100),
            ];
            $backendTeamContacts = [
                config('app.it_rahul'),
                config('app.it_abhishek'),
                //config('app.it_vivek'),
            ];
            $getContacts = DB::table('user')->whereIn('id',$backendTeamContacts)->get()->pluck('contact_no');
            $contactNo = $getContacts;

            //send error wp messaage backend IT team 
            $this->errorWp($getContacts,$templateData);
            
            Log::channel('error_log')->error(json_encode($errorData));
            return $this->sendError("somthing went wrong",'data');
        }
    }

    public function generateLeadNew(Request $request){
        // return $request->getContent();
        if(!$request->getContent()){
            return $this->sendError('',"Request does not exists");
        }
        if(!$request->getContent() && $request->getContent()!=null){
            return $this->sendError('',"parameter can not be null");
        }
        Log::channel('error_log')->error('test123');
        $data = json_decode($request->getContent(),true);
        //return $data;
        $validateData = Validator::make($data,[
            'id'=>'required',
            'customermobile'=>'required',
            // 'customer.name'=>'required',
            'shippingaddress.addressline1'=>'required',
            // 'shippingaddress.cityname'=>'required',
            // 'shippingaddress.pincode'=>'required',
            // 'cart.handovermode'=>'required',
            'cart.lineitems.*.billingtype'=>'required',
            'cart.lineitems.*.quantity'=>'required',
            'cart.lineitems.*.productid'=>'required',
            // 'cart.lineitems.*.rentperiod'=>'required',
            'cart.lineitems.*.rate'=>'required',
            // 'cart.lineitems.*.depositrate'=>'required',
            
        ],
        [
            'id.required'=>'order id can not empty',
            // 'customer.name'=>'customer name no can not be empty',
            'customermobile.required'=>'customer mobile no can not be empty',
            'shippingaddress.addressline1.required'=>'shipping address addressline1 can not be empty',
            // 'shippingaddress.cityname.required'=>'shipping address cityname can not be empty',
            // 'shippingaddress.pincode.required'=>'shipping address pincode can not be empty',
            // 'cart.handovermode.required'=>'handover mode can not be empty',
            'cart.lineitems.*.billingtype.required'=>'product billing type can not be empty',
            'cart.lineitems.*.productid'=>'product id can not be empty',
            // 'cart.lineitems.*.rentperiod'=>'product rent period can not be empty',
            'cart.lineitems.*.quantity'=>'product rate can not be empty',
            'cart.lineitems.*.rate'=>'product rate can not be empty',
            // 'cart.lineitems.*.depositrate'=>'product deposit rate can not be empty',
        ]);
        if($validateData->fails()){
            return $this->sendError('Validation Error.',$validateData->errors());       
        }

        // $productData = $data['cart'];
        // $validateProductId = Validator::make($productData,[
        //     'lineitems.*.productid'=>[
        //         'required',
        //         'exists:products,web_product_id',
        //     ]
        // ]);

        try {   
            DB::beginTransaction();
            //code...
            // $today = Carbon::now()->toDateString();
            $today = Carbon::parse($data['createdat'])->toDateString();

            $data = json_decode($request->getContent(),true);
            if(DB::table('leads')->where('web_order_id',$data['id'])->whereNotIn('lead_status',['Work In Process','Converted'])->exists())
            {
                return $this->sendSuccess('Order Updated successfully', '');
            }
            // return $data['id'];
            $orderId = $data['id'];
            $billingAddress = $data['billingaddress'];
            $cart = $data['cart'];
            $products = $cart['lineitems'];
            if(isset($cart['handovermode']))
            {
                $handovermode = $cart['handovermode'];
            }
            else
            {
                $handovermode = null;
            }

            $shippingAddress = $data['shippingaddress'];

            //customer update or insert
            // $customer = $data['customer'];  
            if(isset($data['patientname']))
            {
                $patientName = $data['patientname'];
            }
            else
            {
                $patientName = null;
            }
            if(isset($data['patientage']))
            {
                $patientAge = $data['patientage'];
            }
            else
            {
                $patientAge = null;
            }
            if(isset($data['patientgender']))
            {
                $patientGender = $data['patientgender'];
            }
            else
            {
                $patientGender = null;
            }
            // $patientAge = $data['patientage'];
            // $patientGender = $data['patientgender'];
            
            $custContact = $data['customermobile'];
            
            $custAddress = explode(',',$shippingAddress['addressline1']);
            
            // if(isset($custAddress[3]))
            // {
                
            //     $custLocation = $custAddress[3];
            // }
            // else
            // {
            //     $custLocation = "";
            // }
            // if(isset($custAddress[3]))
            // {
                
            //     $custArea = $custAddress[2];
            // }
            // else
            // {
            //     $custArea = "";
            // }
            // if(isset($custAddress[3]))
            // {
                
            //     $custLandmark = $custAddress[1];
            // }
            // else
            // {
            //     $custLandmark = "";
            // }
            if(isset($shippingAddress['addressline1']))
            {
                $custAddressLine1 = $shippingAddress['addressline1'];
            }
            else
            {
                $custAddressLine1 = null;
            }

            if(isset($shippingAddress['addressline2']))
            {
                $custAddressLine2 = $shippingAddress['addressline2'];
            }
            else
            {
                $custAddressLine2 = null;
            }
            if(isset($shippingAddress['pincode']))
            {
                $custPincode = $shippingAddress['pincode'];
            }
            else
            {
                $custPincode = '999999';
            }
            // $custPincode = $shippingAddress['pincode'];
            if(isset($shippingAddress['cityname']))
            {
                $custCity = $shippingAddress['cityname'];
                $custLocation = null;
                $custArea = null;
                $custLandmark = null;
            }
            else
            {
                $custCity = 'Mumbai';
                $custLocation = null;
                $custArea = null;
                $custLandmark = null;
            }
            // $custCity = $shippingAddress['cityname'];
            if(isset($shippingAddress['statename']))
            {
                $custState = $shippingAddress['statename'];
            }
            else
            {
                $custState = 'Maharashtra';
            }
            if(isset($shippingAddress['maintext']))
            {
                $mainText = explode(',',$shippingAddress['maintext']);
                if(count($mainText) == 4)
                {
                    $custCity = $mainText[1];
                    $custLocation = $mainText[0];
                    $custArea = $mainText[0];
                    $custLandmark = $mainText[0];
                    if(!isset($shippingAddress['statename']))
                    {
                        $custState = $mainText[2];
                    }
                }
            }
            if(isset($shippingAddress['latitude']))
            {
                $custLatitude = $shippingAddress['latitude'];
            }
            else
            {
                $custLatitude = null;
            }
            if(isset($shippingAddress['latitude']))
            {
                $custLongitude = $shippingAddress['longitude'];
            }
            else
            {
                $custLongitude = null;
            }

            if(isset($shippingAddress['landmark']))
            {
                $custLandmark = $shippingAddress['landmark'];
            }
           
            //Process data for lead insert
            $productIds = [];
            $productQuantitys = [];
            $productMonths = [];
            $productBillingPeriod = [];
            $productBillingUnit = [];
            $productDelDate = [];
            $productSaleRental = [];
            $productOfferedRent = [];
            $productOfferedRentTotal = [];
            $productDeposit = [];
            $productDepositTotal = [];
            $productTransport = [];
            
            $transport = $cart['transportationcost'];
            foreach ($products as $key => $product) {
                $webProductId = $product['productid'];
                $quantity = $product['quantity'];
                if($product['billingtype']=="RENT"){
                    $months = $product['rentperiod'];
                    $billingPeriod = $product['rentperiod'];
                    $billingUnit = ucfirst($product['rentperiodunit']);
                    $deposit = $product['depositrate'];
                }
                else{
                    $months = 1;
                    $deposit = 0;
                    $billingPeriod = 1;
                    $billingUnit = "Month";
                }
                // return $months;
                // $productRent = $product['totalpayableamount'];
                // $productRent = $product['itemtaxableamount']/$product['quantity'];
                $productRent = round(($product['itemtaxableamount']/$product['quantity'])/$months);
                

                if(DB::table('productsmap')->where('webproductid',$webProductId)->exists()){
                // if(DB::table('products')->where('web_product_id',$webProductId)->where('flag','Active')->exists()){
                    // $masterProduct = DB::table('productsmap')->where('webproductid',$webProductId)
                    $masterProduct = DB::table('productsmap')->where('webproductid',$webProductId)->get();
                    array_push($productIds,$masterProduct[0]->oldproductid);
                    array_push($productQuantitys,$quantity);
                    array_push($productMonths,$months);
                    array_push($productBillingPeriod,$billingPeriod);
                    array_push($productBillingUnit,$billingUnit);
                    // return $productMonths;
                    array_push($productDelDate,$today);
                    
                    if($product['billingtype']=="RENT"){
                        array_push($productSaleRental,"Rental");
                    }else{
                        array_push($productSaleRental,"Sale");
                    }
    
                    array_push($productOfferedRent,$productRent);
                    $productRentTotal = $productRent*$quantity*$months;
                    array_push($productOfferedRentTotal,$productRentTotal);
    
                    array_push($productDeposit,$deposit);
                    $depositTotal = $deposit*$quantity;
                    array_push($productDepositTotal,$depositTotal);
    
                    // array_push($productTransport,$transport);
                    if($key==0)
                    {
                        array_push($productTransport,$transport);
                    }
                    else
                    {
                        array_push($productTransport,0);
                    }
                
                }
                else
                {
                    $apiData = json_decode($request->getContent(),true);
                    if($apiData['customertype'] == 'corporate')
                    {
                        $custName = $apiData['b2bcustomer']['orgname'];
                    }
                    else
                    {
                        $custName = $apiData['customer']['name'];
                    }
                    $errorData = [
                        'webproductid'=>$webProductId,
                        'productname'=>$product['productname'],
                        'error'=>"web product id not found Or Product Inactive".$product['productname']." (".$webProductId.")",
                    ];

                    //send wp message to backend it team as error occourd
                    $error = "web product id not found Or Product Inactive - ".$product['productname']." (".$webProductId.")";
                    $templateData = [
                        'order_id'=>$orderId,
                        'date'=>Carbon::today()->format('j F Y'),
                        'customer_name'=>$custName,
                        'customer_contact'=>$custContact,
                        'error'=>$error,
                    ];
                    $backendTeamContacts = [
                        config('app.it_rahul'),
                        config('app.it_abhishek'),
                        //config('app.it_vivek'),
                    ];
                    $getContacts = DB::table('user')->whereIn('id',$backendTeamContacts)->get()->pluck('contact_no');
                    $contactNo = $getContacts;
                    //send error wp messaage backend IT team 
                    $this->errorWp($getContacts,$templateData);
                    
                    Log::channel('error_log')->error(json_encode($errorData));

                    return $this->sendError('',"Something went wrong");
                }

            }

            //get web user
            $createdBy = $data['createdby'];
            if(DB::table('user')->whereIn('username',[$createdBy])->exists())
            {
                $lead_owner_id = DB::table('user')->whereIn('username',[$createdBy])->first()->id;
                $lead_owner_username =  DB::table('user')->whereIn('username',[$createdBy])->first()->username;
            }
            else{

                $webUser = config('app.web_lead_user') ;
                $getWebUser = DB::table('user')->where('id',$webUser)->get();
                $lead_owner_id = $getWebUser[0]->id;
                $lead_owner_username =  $getWebUser[0]->username;
            }
            $custType = $data['customertype'];
            $custTypeSys = null;
            $customerId = $data['customerid'];
            $corp_id = null;
            $leadtype = 'Individual';
            $b2bc_agent_id = null;
            if($custType != 'corporate')
            {
                $customer = $data['customer'];
            }
            if($custType == 'corporate')
            {
                // return $customerId;
                // $corp_id = DB::table('corp_master')->where('web_corp_id',$customerId)->first();
                $b2bc_agent_id = $customerId;
                $leadtype = 'corporate';
                if(DB::table('corp_master')->where('web_corp_id',$customerId)->exists())
                {
                    $corp_id = DB::table('corp_master')->where('web_corp_id',$customerId)->first()->id;
                    // return $corp_id[0]->id;
                }
                $custName = $data['b2bcustomer']['orgname'];
                $custEmail = $data['b2bcustomer']['email'];
                $custTypeSys = 'Corporate';
            }
            else if($custType == 'agent')
            {
                $b2bc_agent_id = $customerId;
                $leadtype = 'agent';
                $custName = $customer['name'];
                $custEmail = $data['b2bcustomer']['email'];
                $custTypeSys = 'Corporate';
            }
            else if($custType == 'retail')
            {
                $custName = $customer['name'];
                if(isset($customer['email']))
                {
                    $custEmail = $customer['email'];
                }
                else
                {
                    $custEmail = null;
                }
                $custTypeSys = 'Individual';
            }
            else
            {
                $custTypeSys = null;
            }
            $referredBy = null;
            if(isset($data['leadremarks']))
            {
                $leadremarks = $data['leadremarks'];
                if(str_contains($leadremarks, 'R:') || str_contains($leadremarks, 'r:')){
                    $referredBy = str_replace('R:','',$leadremarks);
                    $referredBy = str_replace('r:','',$referredBy);
                    $referredBy = substr($referredBy,0,25);
                    if($referredBy == "IndiaMart" || $referredBy == "Indiamart" || $referredBy == "indiamart" || $referredBy == "India Mart"){
                        $leadSource = "IndiaMart";
                    }
                }
            }
            else
            {
                $leadremarks = null;
            }
            // if($data['leadsource'] == 'Reference')
            // {
            //     $leadSource = 'Ref';
            // }
            // else
            // {
                $leadSource = $data['leadsource'];
            // }
            
            // New Code to check actual source of customer (First Lead Source)...
            $customerSource = "Offline";
            if(in_array($leadSource,config('app.online_source'))){
                $customerSource = "Online";
            }else{
                if($leadSource == 'Returning Cust')
                {
                    if(DB::table('customer_details')->where('primary_contact_no',$custContact)->exists()){
                        $customerSource = (in_array(DB::table('customer_details')->join('leads','leads.customer_id','=','customer_details.cust_id')->select('leads.lead_source')->where('customer_details.primary_contact_no',$custContact)->orderBy('leads.id','ASC')->first()->lead_source,config('app.online_source')))?"Online":"Offline";
                    }
                }
            }
            //-------

            if(isset($data['orderdate']))
            {
                $order_date = Carbon::parse($data['orderdate'])->toDateString()." ".date('H:i:s');
            }
            else{
                $order_date = Carbon::now()->toDateTimeString();
            }

            if(!in_array($custCity,['Mumbai','Pune']))
            {
                if(DB::table('citiesmap')->where('city', 'like', '%' . $custCity . '%')->exists())
                {
                    $cityGroup = DB::table('citiesmap')->where('city', 'like', '%' . $custCity . '%')->first()->citygroup;
                }
                else
                {
                    $cityGroup = "Other";
                }
            }
            else
            {
                $cityGroup = $custCity;
            }
            // return $cityGroup;
            if(($custType == 'corporate' || $leadSource == 'Agent') && !DB::table('leads')->where('web_order_id',$orderId)->exists())
            {
                $CustomerDetails =  customer_detail::insertGetId([
                        'customer_name'=>$custName,
                        'location'=>$custLandmark,
                        'address_line_1'=>$custAddressLine1,
                        'address_line_2'=>$custAddressLine2,
                        'area'=>$custLandmark,
                        'landmark'=>$custLandmark,
                        'city'=>$custCity,
                        'citygroup'=>$cityGroup,
                        'pincode'=>$custPincode,
                        'state'=>$custState,
                        'country'=>"India",
                        'cust_date'=>$today,
                        'primary_contact_no'=>$custContact,
                        'email_id'=>$custEmail,
                        'addr_is_same'=>"No",
                        'latitude'=>$custLatitude,
                        'longitude'=>$custLongitude,
                        'refered_by'=>"Web",
                        'customer_type'=>$custTypeSys,
                        'corp_master_id'=>$corp_id,
                        'cust_source'=>"New User Site",
                        'cust_created_at'=>Carbon::now(),
                        'cust_owner'=>$lead_owner_id,
                    ]);
                    $customer_id = $CustomerDetails;
            }
            else
            {
                $CustomerDetails =  customer_detail::updateOrCreate(
                    [
                        'primary_contact_no'=>$custContact,
                    ],[
                        'customer_name'=>$custName,
                        'location'=>$custLandmark,
                        'address_line_1'=>$custAddressLine1,
                        'address_line_2'=>$custAddressLine2,
                        'area'=>$custLandmark,
                        'landmark'=>$custLandmark,
                        'city'=>$custCity,
                        'citygroup'=>$cityGroup,
                        'pincode'=>$custPincode,
                        'state'=>$custState,
                        'country'=>"India",
                        'cust_date'=>$today,
                        'primary_contact_no'=>$custContact,
                        'email_id'=>$custEmail,
                        'addr_is_same'=>"No",
                        'latitude'=>$custLatitude,
                        'longitude'=>$custLongitude,
                        'refered_by'=>"Web",
                        'customer_type'=>$custTypeSys,
                        'corp_master_id'=>$corp_id,
                        'cust_source'=>"New User Site",
                        'cust_created_at'=>Carbon::now(),
                        'cust_owner'=>$lead_owner_id,
                    ]);   
                $customer_id = $CustomerDetails['cust_id'];
            }

            $LeadDetails = lead::updateOrCreate(
                [
                    'web_order_id'=>$orderId,
                ],
                [
                    'customer_id'=>$customer_id,
                    'web_order_id'=>$orderId,
                    'creation_date'=>$today,
                    'converted_at'=>$order_date,
                    'patient_name'=>$patientName,
                    'patient_age'=>$patientAge,
                    'patient_gender'=>$patientGender,
                    'equipment_requirement'=>json_encode($productIds),
                    'equipment_qty'=>json_encode($productQuantitys),
                    'months'=>json_encode($productMonths),
                    'billing_period'=>json_encode($productBillingPeriod),
                    'billing_unit'=>json_encode($productBillingUnit),
                    'del_date'=>json_encode($productDelDate),
                    'sale_rental'=>json_encode($productSaleRental),
                    'offered_rent'=>json_encode($productOfferedRent),
                    'offered_rent_total'=>json_encode($productOfferedRentTotal),
                    'deposite'=>json_encode($productDeposit),
                    'deposite_total'=>json_encode($productDepositTotal),
                    'transport'=>json_encode($productTransport),
                    'lead_source'=>$leadSource,
                    'source_id'=>$data['customerid'],
                    'lead_status'=>"Converted",
                    'priority'=>0,
                    'lead_value'=>$cart['netpayable'],
                    'lead_owner'=> $lead_owner_id,
                    'payment_mode'=>$data['paymentlist'][0]['paymentmode'],
                    'reference_id'=>$data['paymentlist'][0]['paymentreference'],
                    'remark'=>$data['paymentlist'][0]['paymentremarks'],
                    'generated_from'=>"Web",
                    'handovermode'=>$handovermode,
                    'leadtype'=>$leadtype,
                    'b2bc_agent_id'=>$b2bc_agent_id,
                    'comment'=>$leadremarks,
                    'referredby'=>$referredBy,
                    'customer_source'=>$customerSource,
                    'flag'=>"Web",
                    'created_at'=> $data['createdat'],
                    'created_by'=> $lead_owner_username,
                ]
            );
            DB::commit();
            $lead_id = DB::table('leads')->select('id')->where('web_order_id',$orderId)->first()->id;
            $resp = $this->sendwpDetails($lead_id);
            // return $resp;
            return $this->sendSuccess('Order Posted successfully','');
        } catch (\Exception $th) {
            DB::rollBack();
            $errorTruncate = substr($th,0,500);
            $errorData = [
                'orderid'=>$orderId,
                'error'=>$errorTruncate,
            ];
            $apiData = json_decode($request->getContent(),true);
            if($apiData['customertype'] == 'corporate')
            {
                $custName = $apiData['b2bcustomer']['orgname'];
            }
            else
            {
                $custName = $apiData['customer']['name'];
            }
            $templateData = [
                'order_id'=>$apiData['id'],
                'date'=>Carbon::today()->format('j F Y'),
                'customer_name'=>$custName,
                'customer_contact'=>$apiData['customermobile'],
                'error'=>substr($th,0,100),
            ];

            // $backendTeamContacts = [
            //     config('app.it_rahul'),
            //     config('app.it_abhishek')
            //     //, config('app.it_vivek'),
            // ];
            // $getContacts = DB::table('user')->whereIn('id',$backendTeamContacts)->get()->pluck('contact_no');
            // $contactNo = $getContacts;

            //send error wp messaage backend IT team 
            $getContacts = config('app.developer_contacts');
            $this->errorWp($getContacts,$templateData);
            
            Log::channel('error_log')->error(json_encode($errorData));
            return $this->sendError("somthing went wrong",'data');
        }
    }

    public function errorWp($contactNo,$templateData){
        foreach ($contactNo as $key => $contact) {
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
            $orderId = $templateData['order_id'];
            $date = $templateData['date'];
            $customerName = $templateData['customer_name'];
            $customerContact = $templateData['customer_contact'];
            $error = $templateData['error'];
            $data =[
                "portno"=>"11140",
                "namespace"=>"b9a23cb4_89ed_4fe2_b849_20775908ff5e",
                "countrycode"=> "91",
                "mobileno"=> "$contact",
                //"headerimageurl"=>"https://s3.ap-south-1.amazonaws.com/quali55care.com/assets/RESOURCES/logo_quli5care.png",
                "templatename" => "order_integration_error",
                "templateparams" => [
                    ["type"=> "text","text"=> "$orderId"],
                    ["type"=> "text","text"=> "$date"],
                    ["type"=> "text","text"=> "$customerName"],
                    ["type"=> "text","text"=> "$customerContact"],
                    ["type"=> "text","text"=> "$error"],
                ],
            ];
            //return $data;
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            $resp = curl_exec($curl);
            curl_close($curl);
        }

    }

    public function razorpayPaymentApi(Request $request)
    {
        // return $request;
        $ch = curl_init();
        $total_amount = $request->get('total_amount');
        // return $total_amount;
        $reference_id = $request->get('reference_id');
        $order_id = $request->get('order_id');
        $cust_name = $request->get('cust_name');
        $cust_email = $request->get('cust_email');
        $cust_mobile = $request->get('cust_mobile');
        $order_type = $request->get('order_type');
        $fields = 
        [
            "amount"=> (int)$total_amount,
            "currency"=> "INR",
            "expire_by"=> strtotime("+1 days"),
            "reference_id"=> "$reference_id",
            "description"=> "Payment for your order:#".$order_id,
            "customer"=> [
              "name"=> "$cust_name",
              "contact"=> "+91"."$cust_mobile",
              "email"=> "$cust_email"
            ],
                "notify"=> [
                "sms"=> true,
                "email"=> true
            ],
            "reminder_enable"=> true,
            "notes"=> [
                "order_type"=> "$order_type"
            ],
            "callback_url"=> "http://intra.quali55care.com/devweb/eflow/api/razorpay-callback-url?od=$order_id",
            // "callback_url"=> "https://example-callback-url.com/",
            "callback_method"=> "get"
        ];

        // return $fields;
        curl_setopt($ch, CURLOPT_URL, 'https://api.razorpay.com/v1/payment_links');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "rzp_live_E0qnCDa5w4nbH7:Wfg9dB1WT33ABO7TZ1vIdKSr");
        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $data = curl_exec($ch);
    
        if (empty($data) OR (curl_getinfo($ch, CURLINFO_HTTP_CODE != 200))) {
           $data = FALSE;
        } else {
            return json_decode($data, TRUE);
        }
        curl_close($ch);

    }
    public function razorpayPaymentApiRes(Request $request)
    {
        Log::info($request);
        $ch = curl_init();
        // return $fields;
        curl_setopt($ch, CURLOPT_URL, 'https://api.razorpay.com/v1/payment_links/'.$request->get('razorpay_payment_link_id'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($ch, CURLOPT_GETFIELDS, json_encode($fields));
        // curl_setopt($ch, CURLOPT_GET, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "rzp_live_E0qnCDa5w4nbH7:Wfg9dB1WT33ABO7TZ1vIdKSr");
        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $data = curl_exec($ch);
    
        if (empty($data) OR (curl_getinfo($ch, CURLINFO_HTTP_CODE != 200))) {
           $data = FALSE;
        } else {
            Log::info($request);
        }
        curl_close($ch);
        
        return redirect()->away('https://razorpay.com/payment-link/'.$request->get('razorpay_payment_link_id').'/live');
    }

    public function sendwpDetails($lead_id)
    {
        $lead = DB::table('leads')
                        ->join('customer_details','customer_details.cust_id','=','leads.customer_id')
                        ->where('leads.id',$lead_id)
                        ->first();
        $equipments = json_decode($lead->equipment_requirement);
        $customerName = $lead->customer_name;
        $customerAddress = $lead->address_line_1.', '.$lead->address_line_2.', '.$lead->landmark.', '.$lead->area.', '.$lead->city.'-'.$lead->pincode;
        $customerContactNO = $lead->primary_contact_no;
        $patientName = $lead->patient_name;
        $patientAge = $lead->patient_age;
        $getProducts = array();
        $productType = json_decode($lead->sale_rental);
        $productSaleRent = json_decode($lead->offered_rent_total);
        $productDeposit = json_decode($lead->deposite_total);
        $productQty = json_decode($lead->equipment_qty);
        $productTransport = json_decode($lead->transport);
        $paymentMode = $lead->payment_mode;
        $lead_source = $lead->lead_source;

        foreach ($equipments as $key=>$value){
            $temp_prod = DB::table('products')->where('id',$value)->get()->toArray();
           array_push($getProducts,$temp_prod[0]->product_name);
        }
        
        $totalAmt = array_sum($productSaleRent)+array_sum($productDeposit)+array_sum($productTransport);


        $msg ="Product Name : ";
        foreach ($getProducts as $key => $value) {
            $prdMsg ="*".$value."*, ".($productType[$key]=='Rental'?'Rent : ':'Sale : ')
                            .$productSaleRent[$key]
                            ." Deposit :"
                            .($productType[$key]=='Rental'?$productDeposit[$key]:0)
                            ." Qty :"
                            .$productQty[$key]
                            .", Transport : "
                            .$productTransport[$key]." | ";
            $msg .=" ".$prdMsg;
        }
        //$prodManagerNo = config('app.prod_manager_no');
        Log::channel('error_log')->error($lead_source);
        // if($lead_source == 'Wellness Forever' OR $lead_source == 'Web Call' OR $lead_source == 'Web Chat' OR $lead_source == 'Web Popup') 
        // {
        //     $business_head_ids = config('app.business_head_ids');
        //     $business_head_number = DB::table('user')->select('contact_no')->whereIn('id',$business_head_ids)->get();
        // } else
        // {
            // $business_head_id = config('app.business_head_id');
            // $business_head_number = DB::table('user')->select('contact_no')->where('id',$business_head_id)->get()->first();    
        // }
        $business_head_id = config('app.business_head_id');
        $business_head_number = DB::table('user')->select('contact_no')->where('id',$business_head_id)->get()->first();
        $business_head_number = $business_head_number->contact_no;
        $leadOwnerWpno = DB::table('user')->where('id',$lead->lead_owner)->first();
        $wpNumbers = array($business_head_number,$leadOwnerWpno->contact_no);
        
        $delivery_date = date('d-M-y',strtotime($lead->converted_at));
        
        if($patientName == null)
        {
            $patientName = "NA";
        }
        if($patientAge == null)
        {
            $patientAge = "NA";
        }
        $headerText = "";
        if(request()->get('flag') == "Edit")
        {
            $headerText = "(Edited): ".session('username').', Lead Owner: '.$leadOwnerWpno->username.', Lead Id: '.$lead_id;
        }
        else
        {
            $headerText = 'Lead Owner: '.$leadOwnerWpno->username.', Lead Id: '.$lead_id;
        }
        foreach ($wpNumbers as $key => $value) {
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
				$value = config('app.developer_contact');
			}
            $data =[
                "portno"=>"11140",
                "namespace"=>"b9a23cb4_89ed_4fe2_b849_20775908ff5e",
                "countrycode"=> "91",
                "mobileno"=> $value,
                "templatename" => "new_lead",
                "templateparams" => [
                    ["type"=> "text","text"=> $headerText],
                    ["type"=> "text","text"=> $customerName],
                    ["type"=> "text","text"=> "$customerContactNO"],
                    ["type"=> "text","text"=> $patientName],
                    ["type"=> "text","text"=> "$patientAge"],
                    ["type"=> "text","text"=> $msg],
                    ["type"=> "text","text"=> "$totalAmt"],
                    ["type"=> "text","text"=> $paymentMode],
                    ["type"=> "text","text"=>  $customerAddress],
                    ["type"=> "text","text"=> "$delivery_date"],
                    // ["type"=> "text","text"=> "<<Delivery>>],
                ],
            ];
            //   dd(json_encode($data));
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            
            $resp = curl_exec($curl);
            curl_close($curl);
            // dd($resp);
            // return $data;
        }
        return "Success";
    }
}
