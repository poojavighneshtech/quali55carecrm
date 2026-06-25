<?php

namespace App\Http\Controllers\VendorInventoryManagement;

use File;
use Mail;
use Carbon\Carbon;
use App\Models\ActivityLog;
use Illuminate\Support\Str;
use App\Models\UserRegister;
use Illuminate\Http\Request;
use App\Models\VendorRegister;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\VendorProductDetails;
use App\Models\VendorRentedProducts;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VendorReturnInventory;
use App\Models\VirtualVdrInventoryMgmt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class VendorInventoryController extends Controller
{
    public function isLoggedIn()
    {
        $data = session('isLoggedIn');
        return $data;
    }
    public function getVendorInventory()
    {
        
    }

    public function virtual_wh_inventory(Request $request)
    {
        if($request->method() == "GET")
        {
            $from_date = $request->get('filter_from_date');
            $end_date = $request->get('filter_end_date');
            $date_arr = array();
            if(isset($from_date) && isset($end_date))
            {
                array_push($date_arr,$from_date);
                array_push($date_arr,$end_date);
            }
            $vendor_name = $request->get('filter_vendor_name');
            $product_id = $request->get('products');
            $status = $request->get('filter_status');
            $del_boy_id = $request->get('del_boys');
            
            $records = DB::table('virtual_wh_inventory_mgmt')
                            ->join('vendor_details','virtual_wh_inventory_mgmt.vdr_id','=','vendor_details.id')
                            ->join('vendor_warehouse','virtual_wh_inventory_mgmt.vir_wh_id','=','vendor_warehouse.id')
                            ->join('products','virtual_wh_inventory_mgmt.prod_id','=','products.id')
                            ->leftJoin('delusers','virtual_wh_inventory_mgmt.del_boy','=','delusers.id')
                            ->select(
                                'products.product_name as product_name',
                                'vendor_details.registered_name as vendor_name',
                                'vendor_warehouse.wh_name as wh_name',
                                'vendor_warehouse.wh_area as wh_area',
                                'vendor_warehouse.wh_city as wh_city',
                                'virtual_wh_inventory_mgmt.status as status',
                                'delusers.username as del_boy',
                                'virtual_wh_inventory_mgmt.in_time as in_time',
                                'virtual_wh_inventory_mgmt.id as id',
                                'virtual_wh_inventory_mgmt.vdr_id as vdr_id'
                            )
                            ->when($date_arr,function($query,$date_arr){
                                $query->whereBetween(DB::raw("STR_TO_DATE(virtual_wh_inventory_mgmt.in_time,'%Y-%m-%d')"),$date_arr);
                            })
                            ->when($vendor_name,function($query,$vendor_name){
                                $query->where('vendor_details.registered_name',$vendor_name);
                            })
                            ->when($product_id,function($query,$product_id){
                                $query->whereIn('virtual_wh_inventory_mgmt.prod_id',$product_id);
                            })
                            ->when($status,function($query,$status){
                                if($status != "All")
                                {
                                    $query->where('virtual_wh_inventory_mgmt.status',$status);
                                }
                            })
                            ->when($del_boy_id,function($query,$del_boy_id){
                                $query->where('virtual_wh_inventory_mgmt.del_boy',$del_boy_id);
                            })
                            ->when(session('city_based_access') == '1',function($query){
                                $query->where('vendor_warehouse.wh_city',session('user_city'));
                            })
                            ->whereNotIn('virtual_wh_inventory_mgmt.status',['2'])
                            ->paginate(10);                            

            $products = DB::table('products')
                            ->select('products.id as product_id','products.product_name as product_name')
                            ->where('flag','Active')
                            ->get()
                            ->toArray();
            
            $del_boys = DB::table('delusers')
                            ->select('delusers.id as user_id','delusers.username as username')
                            ->where('role','user')
                            ->get()
                            ->toArray();
            $filter_arr['filter_from_date'] = $from_date;
            $filter_arr['filter_end_date'] = $end_date;
            $filter_arr['filter_vendor_name'] = $vendor_name;
            $filter_arr['products'] = $product_id;
            $filter_arr['filter_status'] = $status;
            $filter_arr['del_boys'] = $del_boy_id;
            // dd($filter_arr);
            return view('VendorInventoryManagement.virtual_wh_inventory',compact('products','del_boys','records','filter_arr'));
        }
        else if($request->method() == "POST")
        {

        }
    }
    public function getVendorWarehouse(Request $request)
    {
        $warehouses = DB::table('vendor_warehouse')
                            ->select(
                                'id as id',
                                'wh_name as wh_name',
                                'wh_area as wh_area',
                                'wh_city as wh_city'
                            )
                            ->where('vendor_id',$request->get('vendor_id'))
                            ->get()
                            ->toArray();
        return($warehouses);
    }

    public function update_vir_state(Request $request)
    {
        // dd($request);
        if($request->get('request_type') == 'update-state')
        {
            $out_time = str_replace('T',' ',$request->get('out_time'));
            $out_time = $out_time.":00";
            VirtualVdrInventoryMgmt::where('id',$request->get('hidden_out_process_id'))->update([
                'drop_wh_id'=>$request->get('warehouse'),
                'del_boy'=>$request->get('del_boy_select'),
                'out_time'=>$out_time,
                'status'=>1,
                'remark'=>$request->get('remark'),
                'updated_at'=>date('Y-m-d H:i:m'),
                'updated_by'=>session('username')
            ]);
            $updated_data = [
                'drop_wh_id'=>$request->get('warehouse'),
                'del_boy'=>$request->get('del_boy_select'),
                'out_time'=>$request->get('out_time'),
                'status'=>1,
                'remark'=>$request->get('remark'),
                'updated_at'=>date('Y-m-d H:i:m'),
                'updated_by'=>session('username')
            ];
            $old_data = [
                'drop_wh_id'=>null,
                'del_boy'=>null,
                'out_time'=>null,
                'status'=>0,
                'remark'=>null,
                'updated_at'=>null,
                'updated_by'=>null
            ];
            $record = DB::table('virtual_wh_inventory_mgmt')
                            ->join('order_details','virtual_wh_inventory_mgmt.order_details_id','=','order_details.id')
                            ->join('vendor_product_details','order_details.vendor_product_details_id','=','vendor_product_details.id')
                            ->join('products','order_details.product_id','=','products.id')
                            ->join('vendor_details','order_details.vendor_id','=','vendor_details.id')
                            ->select(
                                'order_details.id as order_details_id',
                                'order_details.rented_product_id as rented_product_id',
                                'order_details.vendor_product_details_id as vendor_product_details_id',
                                'virtual_wh_inventory_mgmt.drop_wh_id as drop_wh_id',
                                'vendor_product_details.warehouse_id as warehouse_id',
                                'products.product_name as product_name',
                                'vendor_details.registered_name as vendor_name',
                                'vendor_details.of_email as email_id',
                                'vendor_details.of_primary_contact_1 as contact_no'
                            )
                            ->where('virtual_wh_inventory_mgmt.id',$request->get('hidden_out_process_id'))
                            ->get()
                            ->toArray();
            VendorProductDetails::where('id',$record[0]->vendor_product_details_id)->update(['availability_status' => 0,'current_location' => 2,'warehouse_id' => $request->get('warehouse')]);
            $update_inventory = [
                'availability_status' => 0,
                'current_location' => 2,
                'warehouse_id' => $request->get('warehouse')
            ];
            $old_inventory = [
                'availability_status' => 0,
                'current_location' => 2,
                'warehouse_id' => $record[0]->warehouse_id
            ];
            foreach($update_inventory as $key=>$value)
            {
                ActivityLog::insert([
                    'order_type'=>'OD',
                    'key_id'=>$record[0]->vendor_product_details_id,
                    'operation'=>'Inventory State Update',
                    'fields'=>$key,
                    'old_value'=>$old_inventory[$key],
                    'new_value'=>$value,
                    'updated_by'=>session('username')
                ]);
            }
            
            $update_rented_prod = VendorRentedProducts::where('id',$record[0]->rented_product_id)->update(['status'=>'Released']);
            ActivityLog::insert([
                'order_type'=>'VRP',
                'key_id'=>$record[0]->rented_product_id,
                'operation'=>'Release Rented Product',
                'fields'=>'status',
                'old_value'=>'On Rent',
                'new_value'=>'Released',
                'updated_by'=>session('username')
            ]);
            foreach($updated_data as $key=>$value)
            {
                ActivityLog::insert([
                    'order_type'=>'VWU',
                    'key_id'=>$request->get('hidden_out_process_id'),
                    'operation'=>'Update State to Out Process',
                    'fields'=>$key,
                    'old_value'=>$old_data[$key],
                    'new_value'=>$value,
                    'updated_by'=>session('username')
                ]);
            }
            $data_message = array(
                'vendor_name'=>$record[0]->vendor_name,
                'product_name'=>$record[0]->product_name
            );
            $mail_id = $record[0]->email_id;
            $mail_id = 'abhishekn@quali55care.com';
            $contact_no = $record[0]->contact_no;
            if(config('app.app_env') == 'devweb'){
                $contact_no = config('app.developer_contact');
            }
            $email_check = $request->get('email_check');
            if(isset($email_check))
            {
                if($request->get('email_check') == 'on')
                {
                    // dd("In Email");
                    $warehouse_details = DB::table('vendor_warehouse')->select('wh_name','wh_area','wh_city')->where('id',$request->get('warehouse'))->get()->toArray();
                    $warehouse_address = $warehouse_details[0]->wh_name.", ".$warehouse_details[0]->wh_area.", ".$warehouse_details[0]->wh_city;
                    $delivery_time = $out_time;
                    $data_message['warehouse_address'] = $warehouse_address;
                    $data_message['delivery_time'] = $delivery_time;
                    $data_message['remark'] = $request->get('remark');
                    $this->sendEmail($mail_id,$data_message);
                }
                if($request->get('wp_check') == 'on')
                {
                    $warehouse_details = DB::table('vendor_warehouse')->select('wh_name','wh_area','wh_city')->where('id',$request->get('warehouse'))->get()->toArray();
                    $warehouse_address = $warehouse_details[0]->wh_name.", ".$warehouse_details[0]->wh_area.", ".$warehouse_details[0]->wh_city;
                    $delivery_time = $out_time;
                    $data_message['warehouse_address'] = $warehouse_address;
                    $data_message['delivery_time'] = $delivery_time;
                    $data_message['remark'] = $request->get('remark');
                    // $this->sendWp($contact_no,$data_message);
                }
            }
            return redirect()->back()->with('message','State updated Successfully!');
        }
        if($request->get('request_type') == "update-state-out")
        {
            VirtualVdrInventoryMgmt::where('id',$request->get('id'))->update([
                'status'=>3,
                'updated_at'=>date('Y-m-d H:i:m'),
                'updated_by'=>session('username')
            ]);
            ActivityLog::insert([
                'order_type'=>'VWU',
                'key_id'=>$request->get('id'),
                'operation'=>'Update State to Out Process',
                'fields'=>'status',
                'old_value'=>1,
                'new_value'=>3,
                'updated_by'=>session('username')
            ]);    
        }
    }
    public function sendEmail($mail_id,$data_message)
    {
        Mail::send('vir_wh_mail/out_process_mail',$data_message, function($message) use ($mail_id)
        {     
            $message->to($mail_id, 'Quali55Care -Inventory Update')->subject('Quali55Care -Inventory Update');
            $message->from('tempmailquali@gmail.com', 'Quali55Care');
        });
    }
    public function sendWp($contact_no,$message)
    {
        $product_name = "*'".$message['product_name']."'*";
        $date = "*".date('d-m-Y H:i',strtotime($message['delivery_time']))."*";
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
               $data =[
                  "portno"=>"11140",
                  "namespace"=>"b9a23cb4_89ed_4fe2_b849_20775908ff5e",
                  "countrycode"=> "91",
                  "mobileno"=> $contact_no,
                  "templatename" => "vendor_products_return_conf",
                  "templateparams" => [
                        ["type"=> "text","text"=> $message['vendor_name']],
                        ["type"=> "text","text"=> $product_name],
                        ["type"=> "text","text"=> $message['warehouse_address']],
                        ["type"=> "text","text"=> $date],
                        ["type"=> "text","text"=> $message['remark']],
                      // ["type"=> "text","text"=> "<<Delivery>>],
                  ],
              ];
              curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
              
              $resp = curl_exec($curl);
              curl_close($curl);
              dd($resp);
    }
    public function sedMessage($contact_no,$message)
    {

    }

    public function inventoryVendorMail($order_ids){
        // $order_ids = json_encode([1666,1671,1672,652]);
        $mail_id = 'abhishekn@quali55care.com';
        $order_ids = json_decode($order_ids);
        // dd($order_ids);
        
        $records = DB::table('order_details')
                        ->join('vendor_details','order_details.vendor_id','=','vendor_details.id')
                        ->join('products','order_details.product_id','=','products.id')
                        ->join('pickups','order_details.id','=','pickups.order_details_id')
                        ->join('vendor_warehouse','pickups.drop_warehouse_id','=','vendor_warehouse.id')
                        ->select(
                            'vendor_details.id as vendor_details_id',
                            'vendor_warehouse.vendor_id as warehouse_vendor_id',
                            'products.product_name as product_name',
                            'vendor_details.registered_name as vendor_name',
                            'vendor_warehouse.wh_name as wh_name',
                            'vendor_warehouse.wh_area as wh_area',
                            'vendor_warehouse.wh_city as wh_city'
                        )
                        ->whereIn('order_details.id',$order_ids)
                        // ->where('vendor_details.id','vendor_warehouse.vendor_id')
                        ->get();
        $records = $records->groupBy('vendor_details_id')->toArray();
        // dd($records);
        $vendor_records = array();
        foreach($records as $key=>$value)
        {
            $i = 0;
            foreach($value as $key_i=>$val)
            {
                if($val->vendor_details_id == $val->warehouse_vendor_id)
                {
                    $vendor_records[$key][$i] = $val;
                    $i++;
                }
            }            
        }
        // dd($vendor_records);
        // $data_message['vendor_products'] = $vendor_records;
        foreach($vendor_records as $key=>$value)
        {
            $data_message['vendor_products'] = $value;
            Mail::send('vir_wh_mail/inventory_pickup_mail_api',$data_message, function($message) use ($mail_id)
            {   
                $message->to($mail_id, 'Quali55Care -Inventory Update')->subject('Quali55Care -Inventory Update');
                $message->from('tempmailquali@gmail.com', 'Quali55Care');
            });
        }
    }
    public function vendorReturnInventory(Request $request){
        // dd($request->all());
        $masterProducts = DB::table('products')->where('flag','Active')->get();
        $vendors = DB::table('vendor_details')->where('authentication_status','Approved')->get();
        $cities = array();
        if(session('city_based_access') == '1')
        {
            $cities[0] = (object)(['city'=>session('user_city')]);
        }
        else{

            $cities = DB::table('vendor_details')->distinct('of_city')->select('of_city as city')->get();
        }
        $deliveryStaff = DB::table('delusers')->where('role','user')->get();
        // dd($request->all());
        $getInventory = DB::table('vendor_inventory_mgmt')
                        ->select('vendor_inventory_mgmt.*','vendor_details.registered_name as vendor_name','products.product_name')
                        ->join('vendor_details','vendor_inventory_mgmt.vendor','=','vendor_details.id')
                        ->join('products','vendor_inventory_mgmt.equipment','=','products.id')
                        ->when($request->get('return_vendor'),function($query)use($request){
                            $query->whereIn('vendor_inventory_mgmt.vendor',$request->get('return_vendor'));
                        })
                        ->when($request->get('prod_name'),function($query)use($request){
                            $query->where('vendor_inventory_mgmt.equipment',$request->get('prod_name'));
                        })
                        ->when($request->get('from_date'),function($query)use($request){
                            if($request->get('from_date')!=null && $request->get('end_date')!=null)
                            {
                                $query->whereBetween('date',[$request->get('from_date'),$request->get('end_date')]);
                            }
                        })
                        ->when($request->get('filter_state'),function($query)use($request){
                            if($request->get('filter_state')!="All"){
                                $query->where('vendor_inventory_mgmt.state',$request->get('filter_state'));
                            }
                        })
                        ->when($request->get('filter_return_assigned_to'),function($query)use($request){
                            $query->where('assigned_to',$request->get('filter_return_assigned_to'));
                        })
                        ->when($request->get('city'),function($query)use($request){
                            if($request->get('city')!='All'){
                                $query->where('vendor_details.of_city',$request->get('city'));
                            }
                        })
                        ->when(session('city_based_access') == '1',function($query){
                            $query->where('vendor_details.of_city',session('user_city'));
                        })
                        ->where('vendor_inventory_mgmt.flag','Active')
                        ->orderBy('vendor_inventory_mgmt.date','DESC')
                        ->get();
        $inventory = $getInventory->paginate(10);
        $virtual_warehouses = DB::table('vendor_warehouse')->select('vendor_warehouse.id','vendor_warehouse.wh_name','vendor_warehouse.wh_area','vendor_warehouse.wh_city')->where('vendor_id',17)->whereIn('id',[19,175])->where('flag','active')->orderBy('wh_name','ASC')->get();
        if($request->get('btn_submit')=='export_excel')
        {
            ob_end_clean(); // this
            ob_start(); // and this
            return Excel::download(new VendorReturnInventory($getInventory), 'vendor_inevntory.xls');
        }
        return view('VendorInventoryManagement.vendor-return-inventory',compact('vendors','virtual_warehouses','masterProducts','inventory','cities','deliveryStaff'));
    }
    public function vendorReturnInventoryCreate(Request $request){

        if($request->get('type')=='img_update'){
            $productImage = $request->file('img_return_product_image');
            $filePath=array();
            $insertData = array();
            if($productImage){
                foreach($productImage as $key=>$image)
                {
                    $record = DB::table('vendor_inventory_mgmt')->where('id',$request->get('row_id'))->first();
                    $fileName = $record->date."-".$record->equipment.date("Y-m-d h:i:s")."-".$key.'.'.$image->getClientOriginalExtension();
                    array_push($filePath,$request->file('img_return_product_image')[$key]->storeAs('public/vendor_return_inventory',$fileName));
                }
                $insertData['product_img']= implode(',',$filePath);
            };
            if($request->get('img_pasted_image')){
                $image = $request->get('img_pasted_image');  // your base64 encoded
                $image = str_replace('data:image/png;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageName = Str::random(10).'.'.'png';
                File::put(storage_path(). '/app/public/vendor_return_inventory/' . $imageName, base64_decode($image));
                array_push($filePath,'public/vendor_return_inventory/'.$imageName);
                $insertData['product_img']= implode(',',$filePath);
            }
            if(count($insertData)>0){
                DB::table('vendor_inventory_mgmt')->where('id',$request->get('row_id'))->update($insertData);
            }
            return redirect()->back()->with('message','Success');
        }
        $productImage = $request->file('return_product_image');
        // dd($productImage);
        // $fileName = time().'.'.$request->file->extension();  
        // $request->file->move(public_path('uploads'), $fileName);
        $insertData = [
            'date'=>$request->get('return_date'),
            'vendor'=>$request->get('return_vendor'),
            'equipment'=>$request->get('return_product'),
            'inventory_id'=>$request->get('return_inventory_id'),
            'inventory_pickup_date'=>$request->get('return_pickup_date'),
            'state'=>$request->get('return_type'),
            'quantity'=>$request->get('return_inventory_quantity'),
            'pickup_address'=>$request->get('return_pickup_address'),
            'drop_address'=>$request->get('return_drop_address'),
            'assigned_to'=>$request->get('return_assigned_to'),
            'comment'=>$request->get('comment')
        ];

        $filePath=array();
        if($productImage){
            foreach($productImage as $key=>$image)
            {
                $fileName = $request->get('return_date')."-".$request->get('return_product').date("Y-m-d h:i:s")."-".$key.'.'.$image->getClientOriginalExtension();
                array_push($filePath,$request->file('return_product_image')[$key]->storeAs('public/vendor_return_inventory',$fileName));
            }
            $insertData['product_img']= implode(',',$filePath);
        };
        if($request->get('pasted_image')){
            $image = $request->get('pasted_image');  // your base64 encoded
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = Str::random(10).'.'.'png';
            File::put(storage_path(). '/app/public/vendor_return_inventory/' . $imageName, base64_decode($image));
            array_push($filePath,'public/vendor_return_inventory/'.$imageName);
            $insertData['product_img']= implode(',',$filePath);
        }
        // dd($insertData);
        $vendor = DB::table('vendor_details')->where('id',$request->get('return_vendor'))->first();
        $vendor_name = $vendor->registered_name;
        $vdr_contact = $vendor->of_primary_contact_1;
        $state = ($request->get('return_type') == 'in')?"Picked":"Returned";
        $product_name = DB::table('products')->where('id',$request->get('return_product'))->first()->product_name;
        $inventory_id = ($request->get('return_inventory_id') == 0)?"Not Available":$request->get('return_inventory_id');
        
        $business_head_id = config('app.business_head_id');
        $business_head_contact = DB::table('user')->select('contact_no')->where('id',$business_head_id)->get()->first()->contact_no;
        if($request->get('submit') == "update")
        {
            $previous_data = DB::table('vendor_inventory_mgmt')->where('id',$request->get('hide_record_id'))->first();
            if($previous_data->inventory_pickup_date){
                $previous_date = date('d-M-y',strtotime($previous_data->inventory_pickup_date));
            }else{
                $previous_date = date('d-M-y',strtotime($previous_data->date));
            }

            // $is_edited = "Your product pickup/return information has changed of *$previous_date*";
            $is_edited = "Your product pickup/return information sent on *$previous_date* has changed";
            DB::table('vendor_inventory_mgmt')->where('id',$request->get('hide_record_id'))->update($insertData);
            if($previous_data->is_verified == 'yes' && $vendor->notify_flag == 'yes'){
                $this->sendVdrInvWpMsg(strval($vdr_contact),$is_edited,$vendor_name,$state,date('d-M-y',strtotime($request->get('return_pickup_date'))),$product_name,$request->get('return_inventory_quantity'),$inventory_id,$business_head_contact);
                $this->sendVdrInvMail($vdr_contact,str_replace('*', '', $is_edited),$vendor_name,$state,date('d-M-y',strtotime($request->get('return_pickup_date'))),$product_name,$request->get('return_inventory_quantity'),$inventory_id,$business_head_contact);
            }
            return redirect()->back()->with('message','Product Updated Successfully...');
        }
        else
        {
            $insertData['is_verified'] = ($request->get('isVerified'))?"yes":"no";
            $inserted = DB::table('vendor_inventory_mgmt')->insert($insertData);
            if($request->get('isVerified') && $vendor->notify_flag == 'yes'){
                $this->sendVdrInvWpMsg(strval($vdr_contact)," ",$vendor_name,$state,date('d-M-y',strtotime($request->get('return_pickup_date'))),$product_name,$request->get('return_inventory_quantity'),$inventory_id,$business_head_contact);
                $this->sendVdrInvMail($vdr_contact," ",$vendor_name,$state,date('d-M-y',strtotime($request->get('return_pickup_date'))),$product_name,$request->get('return_inventory_quantity'),$inventory_id,$business_head_contact);
            }
            return redirect()->back()->with('message','Product Added Successfully...');
        }
        
        //return view('VendorInventoryManagement.vendor-return-inventory',compact('vendors'));
    }

    public function vendorReturnInventoryGet(Request $request){
        $inventory = DB::table('vendor_inventory_mgmt')
                        ->select('vendor_inventory_mgmt.*','vendor_details.registered_name as vendor_name','products.product_name')
                        ->join('vendor_details','vendor_inventory_mgmt.vendor','=','vendor_details.id')
                        ->join('products','vendor_inventory_mgmt.equipment','=','products.id')
                        ->when($request->get('record_id'),function($query)use($request){
                            $query->where('vendor_inventory_mgmt.id',$request->get('record_id'));
                        })
                        ->get();
        return $inventory;
    }
    public function vendorReturnInventoryDelete(Request $request){
        DB::table('vendor_inventory_mgmt')->where('id',$request->get('record_id'))->update(['flag'=>'Inactive','updated_by'=>session('username')]);
    }

    // public function vdrinvreport($date){
    //     $vdrinv = DB::table('order_details')->join('del_orders','del_orders.order_id','=','order_details.order_id')->select('del_orders.mobileno','order_details.*')->where('order_details.creation_date','>=',$date)->whereNotIn('order_details.current_status',['Cancel'])->get();
    //     foreach($vdrinv as $key=>$value){
    //         $pickedup = null;
    //         if(DB::table('pickups')->where('order_details_id',$value->id)->whereNull('status')->exists()){
    //             $pickedup = DB::table('pickups')->where('order_details_id',$value->id)->whereNull('status')->first()->pickup_date;
    //         }
    //         DB::table('vdr_inv_mgmt')->updateOrInsert([
    //             'order_id'=>$value->order_id,
    //             'order_details_id'=>$value->id,
    //             'vendor_id'=>$value->vendor_id,
    //         ],[
    //             'product_id'=>$value->product_id,
    //             'contact_no'=>str_replace(" ","",$value->mobileno),
    //             'rented_on'=>$value->creation_date,
    //             'picked_up_on'=>$pickedup
    //         ]);
    //     }
    // }
    public function vdrinvreport($date){
        $deldate = date('d-m-Y',strtotime($date));
        // $vdrinv = DB::table('order_details')->join('del_orders','del_orders.order_id','=','order_details.order_id')->select('del_orders.mobileno','order_details.*')->where('order_details.creation_date','>=',$date)->whereNotIn('order_details.current_status',['Cancel'])->get();
        // foreach($vdrinv as $key=>$value){
        //     $pickedup = null;
        //     if(DB::table('pickups')->where('order_details_id',$value->id)->whereNull('status')->exists()){
        //         $pickedup = DB::table('pickups')->where('order_details_id',$value->id)->whereNull('status')->first()->pickup_date;
        //     }
        //     DB::table('vdr_inv_mgmt')->updateOrInsert([
        //         'order_id'=>$value->order_id,
        //         'order_details_id'=>$value->id,
        //     ],[
        //         'vendor_id'=>$value->vendor_id,
        //         'product_id'=>$value->product_id,
        //         'contact_no'=>str_replace(" ","",$value->mobileno),
        //         'rented_on'=>$value->creation_date,
        //         'picked_up_on'=>$pickedup
        //     ]);
        // }
        // Delivery
        $vdrinvdelivery = DB::table('order_details')->join('del_orders','del_orders.order_id','=','order_details.order_id')->select('del_orders.mobileno','del_orders.status','order_details.*')->where(DB::raw("STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y')"),">=", DB::raw("STR_TO_DATE('$deldate','%d-%m-%Y')"))->whereNotIn('order_details.current_status',['Cancel'])->where('del_orders.status','Delivered')->get();
        $vdrinvrenewal = DB::table('renewals')->join('del_orders','del_orders.order_id','=','renewals.collection_order_id')->join('order_details','order_details.id','=','renewals.order_details_id')->select('del_orders.mobileno','order_details.*','renewals.start_date','renewals.id as renewal_id')->where(DB::raw("STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y')"),">=", DB::raw("STR_TO_DATE('$deldate','%d-%m-%Y')"))->whereNotIn('renewals.status',['Cancel'])->get();
        $vdrinvpickup = DB::table('pickups')->join('del_orders','del_orders.order_id','=','pickups.pickup_order_id')->join('order_details','order_details.id','=','pickups.order_details_id')->select('del_orders.mobileno','order_details.*','pickups.pickup_date')->where(DB::raw("STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y')"),">=", DB::raw("STR_TO_DATE('$deldate','%d-%m-%Y')"))->whereNull('pickups.status')->get();
        foreach($vdrinvdelivery as $key=>$value){
            DB::table('vdr_inv_mgmt')->updateOrInsert([
                'order_id'=>$value->order_id,
                'order_details_id'=>$value->id,
                'type'=>"D"
            ],[
                'vendor_id'=>$value->vendor_id,
                'product_id'=>$value->product_id,
                'contact_no'=>str_replace(" ","",$value->mobileno),
                'rented_on'=>$value->creation_date,
            ]);
        }
        foreach($vdrinvrenewal as $key=>$value){
            DB::table('vdr_inv_mgmt')->updateOrInsert([
                'order_id'=>$value->order_id,
                'order_details_id'=>$value->id,
                'renewal_order_details_id'=>$value->renewal_id,
                'type'=>"R"
            ],[
                'vendor_id'=>$value->vendor_id,
                'product_id'=>$value->product_id,
                'contact_no'=>str_replace(" ","",$value->mobileno),
                'rented_on'=>$value->start_date,
            ]);
        }
        foreach($vdrinvpickup as $key=>$value){
            DB::table('vdr_inv_mgmt')->where([
                'order_id'=>$value->order_id,
                'order_details_id'=>$value->id,
            ])->update([
                'picked_up_on'=>$value->pickup_date
            ]);
        }
    }
    public function vendorRentedInventory(){
        $today = Carbon::now()->toDateString();
        $vendor_inventories = DB::table('order_details')->where('pickup_date',$today)->whereNotIn('current_status',['Picked Up','Cancel'])->where('sale_rental','Rental')->get();
        foreach($vendor_inventories as $key=>$value){
            if(DB::table('vendor_rented_inventory')->where('order_details_id',$value->id)->where('flag','Active')->exists()){
                $rented_date = DB::table('vendor_rented_inventory')->where('order_details_id',$value->id)->where('flag','Active')->orderBy('id','DESC')->first()->due_date;
                DB::table('vendor_rented_inventory')->insert(
                    [
                        "vendor_id" => $value->vendor_id,
                        "order_id" => $value->order_id,
                        "order_details_id" => $value->id,
                        "inventory_id" => $value->vendor_product_details_id,
                        "vendor_product_id" => $value->vendor_product_id,
                        "rented_date" => date('Y-m-d',strtotime($rented_date)),
                        "due_date" => date('Y-m-d',strtotime("+1 months",strtotime($rented_date))),
                        "status" => 'live',
                        'type' => 'Renewal',
                        'created_by' => 'CronJob'
                    ]
                );
            }
        }
    }
    public function vendorInventoryVerify($id){
        DB::beginTransaction();
        try{
            $business_head_id = config('app.business_head_id');
            $business_head_contact = DB::table('user')->select('contact_no')->where('id',$business_head_id)->get()->first()->contact_no;
            $record = DB::table('vendor_inventory_mgmt')
                ->join('vendor_details','vendor_inventory_mgmt.vendor','=','vendor_details.id')
                ->join('products','vendor_inventory_mgmt.equipment','=','products.id')
                ->select('vendor_inventory_mgmt.*','vendor_details.registered_name as vendor_name','products.product_name','vendor_details.of_primary_contact_1','vendor_details.notify_flag')
                ->where('vendor_inventory_mgmt.id',$id)->first();

            $inventory_id = ($record->inventory_id == "0" || $record->inventory_id == 0)?"Not Available":$record->inventory_id;
            $state = ($record->state == 'in')?"Picked":"Returned";

            DB::table('vendor_inventory_mgmt')->where('id',$id)->update(['is_verified'=>'yes']);
            Db::commit();
            if($record->notify_flag == 'Yes'){
                $this->sendVdrInvWpMsg(strval($record->of_primary_contact_1)," ",$record->vendor_name,$state,date('d-M-y',strtotime($record->inventory_pickup_date)),$record->product_name,strval($record->quantity),$record->inventory_id,$business_head_contact);
                $this->sendVdrInvMail($record->of_primary_contact_1," ",$record->vendor_name,$state,date('d-M-y',strtotime($record->inventory_pickup_date)),$record->product_name,strval($record->quantity),$record->inventory_id,$business_head_contact);
            }
            return redirect()->back()->with('message','Record Verified!');
        }catch(Exception $ex){
            DB::rollback();
            return redirect()->back()->with('error','Something went wrong!: '.$ex->getMessage());
        }
    }
    public function sendVdrInvWpMsg($vdr_contact,$is_edited,$vendor_name,$state,$date,$product_name,$qty,$inventory_id,$business_head_contact){
        if(config('app.app_env') == 'devweb'){
            $vdr_contact = config('app.developer_contact');
        }
        // dd($vdr_contact,$is_edited,$vendor_name,$state,$date,$product_name,$qty,$inventory_id,$business_head_contact);
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
        $data =[
           "portno"=>"11140",
           "namespace"=>"b9a23cb4_89ed_4fe2_b849_20775908ff5e",
           "countrycode"=> "91",
           "mobileno"=> $vdr_contact,
           "templatename" => "vendor_products_return_conf",
           "templateparams" => [
                 ["type"=> "text","text"=> $is_edited],
                 ["type"=> "text","text"=> $vendor_name],
                 ["type"=> "text","text"=> "*".$state."*"],
                 ["type"=> "text","text"=> $date],
                 ["type"=> "text","text"=> $product_name],
                 ["type"=> "text","text"=> $qty],
                 ["type"=> "text","text"=> $inventory_id],
                 ["type"=> "text","text"=> $business_head_contact],
           ],
       ];
       curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
       
       $resp = curl_exec($curl);
       curl_close($curl);
       return $resp;
        // dd($resp);
    }

    public function sendVdrInvMail($vdr_contact,$is_edited,$vendor_name,$state,$date,$product_name,$qty,$inventory_id,$business_head_contact){
        $accountsEmail = config('app.accounts_email');
        Mail::send('Mail/vendor-inventory-msg-report',compact('vdr_contact','is_edited','vendor_name','state','date','product_name','qty','inventory_id','business_head_contact'), function($message) use($accountsEmail,$vendor_name)
        {  
            $message->to($accountsEmail, 'Accounts')->subject("Vendor Inventory Picked/Returned [$vendor_name]");
            //$message->to($senderMail->email_id_user, 'Complaint Raised')->subject('Complaint Raised');
            $message->from('tempmailquali@gmail.com', 'Quali55Care');
        });
    }
    public function vendorBilling(Request $request){
        $vendors = DB::table('vendor_details')->select('id','registered_name as vendor_name')->get();
        $orderIds = DB::table('del_orders')->select('order_id')->get()->pluck('order_id');
        $vendorBilling = DB::table('vendor_billing')
            ->join('vendor_details','vendor_details.id','=','vendor_billing.vendor_id')
            ->select('vendor_billing.*','vendor_details.registered_name','vendor_details.of_primary_contact_1')
            ->when($request->get('filtervendorid'),function($query)use($request){
                $query->where('vendor_billing.vendor_id',$request->get('filtervendorid'));
            })
            ->when($request->get('filterinvoiceno'),function($query)use($request){
                $query->where('vendor_billing.vendor_invoice_no','LIKE','%'.$request->get('filterinvoiceno').'%');
            })
            ->when($request->get('filterinvoicedatefrom') && $request->get('filterinvoicedateto'),function($query)use($request){
                $query->whereBetween('vendor_billing.vendor_invoice_date',[$request->get('filterinvoicedatefrom'),$request->get('filterinvoicedateto')]);
            })
            ->when($request->get('filterinvoicestatus'),function($query)use($request){
                $query->where('vendor_billing.invoice_status',$request->get('filterinvoicestatus'));
            })
            ->when($request->get('filterpaymentstate'),function($query)use($request){
                $query->where('vendor_billing.payment_state',$request->get('filterpaymentstate'));
            })
            ->paginate(10);
        foreach($vendorBilling as $key=>$invoice){
            if(DB::table('vendor_billing_details')->where('vendor_billing_id',$invoice->id)->where('flag','active')->exists()){
                $vendorBilling[$key]->orderid = DB::table('vendor_billing_details')->where('vendor_billing_id',$invoice->id)->where('flag','active')->get()->pluck('order_id')->toArray();
            }else{
                $vendorBilling[$key]->orderid = [];
            }
        }
        return view('VendorInventoryManagement.vendor-billing',compact('vendors','orderIds','vendorBilling'));
    }
    public function vendorBillingCrud(Request $request){
        // dd($request->all());
        DB::beginTransaction();
        try{
            $updatearr = [
                'vendor_invoice_date'=>$request->get('invoicedate'),
                "vendor_id"=>$request->get('vendorid'),
                "vendor_invoice_no"=>$request->get('invoiceno'),
                "invoice_status"=>$request->get('invoice_status'),
                "product_inventory_no"=>$request->get('inventoryid'),
                "vendor_invoice_rate"=>$request->get('vdrrate'),
                "payment_state"=>$request->get('payment_state'),
                "vendor_serial_no"=>$request->get('vdrserialno'),
                'vendor_pickup_date'=>$request->get('vendor_pickup_date'),
                'vendor_return_date'=>$request->get('vendor_returned_date'),
                'rent_unit'=>$request->get('vendor_rent_unit'),
                'period'=>$request->get('vendor_rent_period'),
                "created_by" => session('username')
            ];
            if($request->get('invoice_status') == 1){
                $updatearr["verified_at"] = date('Y-m-d H:i:s');
                $updatearr["verified_by"] = session('username');
            }
            $str = rand();
            $result = md5($str);
            if(!empty($request->hasFile('paymentimg'))){
                
                $paymentimg = $_FILES['paymentimg']['name'];
                //print_r($_FILES['shop_images']['name']);
                $targetDir = "assets/uploads/vdr_pay_img/";
                $fileName = basename($_FILES['paymentimg']['name']);
                $targetFilePath = $targetDir . $fileName;
                $fileType =strtolower(pathinfo($targetFilePath,PATHINFO_EXTENSION));
                $new_file_name = $targetDir."N".$result.".".$fileType;
                move_uploaded_file($_FILES["paymentimg"]["tmp_name"], $new_file_name);    
                $paymentimg_filePath = "N".$result.".".$fileType;
                $updatearr['payment_image'] = $paymentimg_filePath;
            }

            if(!empty($request->hasFile('invoiceimg'))){
                $invoiceimg = $_FILES['invoiceimg']['name'];
                //print_r($_FILES['shop_images']['name']);
                $targetDir = "assets/uploads/vdr_invoice_img/";
                $fileName = basename($_FILES['invoiceimg']['name']);
                $targetFilePath = $targetDir . $fileName;
                $fileType =strtolower(pathinfo($targetFilePath,PATHINFO_EXTENSION));
                $new_file_name = $targetDir."N".$result.".".$fileType;
                move_uploaded_file($_FILES["invoiceimg"]["tmp_name"], $new_file_name);    
                $updatepaymentimg_filePath = "N".$result.".".$fileType;
                $updatearr['vendor_invoice_image'] = $updatepaymentimg_filePath;
            }
            if($request->get('updateid')){
                unset($updatearr['created_by']);
                $updatearr['updated_by'] = session('username');
                if($request->get('comment')){
                    $comment = DB::table('vendor_billing')->select('comment')->where('id',$request->get('updateid'))->first()->comment;
                    $updatearr['comment'] = $comment.' ['.date("d-M-y h:i:sa").'] '.$request->get('comment');
                }
                DB::table('vendor_billing')->where('id',$request->get('updateid'))->update($updatearr);
                if($request->get('orderid')){
                    DB::table('vendor_billing_details')->where('vendor_billing_id',$request->get('updateid'))->update(['flag'=>'inactive']);
                    foreach($request->get('orderid') as $orderid){
                        DB::table('vendor_billing_details')->updateOrInsert([
                            'vendor_billing_id'=>$request->get('updateid'),
                            'order_id'=>$orderid,
                        ],[
                            'flag' => 'active',
                            'created_by' => session('username'),
                            'updated_by' => session('username')
                        ]);                        
                    }
                }
            }else{
                if($request->get('comment')){
                    $updatearr['comment'] = ' ['.date("d-M-y h:i:sa").'] '.$request->get('comment');
                }
                $insertedId = DB::table('vendor_billing')->insertGetId($updatearr);
                if($request->get('orderid')){
                    foreach($request->get('orderid') as $orderid){
                        DB::table('vendor_billing_details')->insert(['vendor_billing_id'=>$insertedId,'order_id'=>$orderid,'created_by'=>session('username')]);
                    }
                }
            }
            DB::commit();
            return redirect()->back()->with('message','Details Updated!');
        }catch(Exception $ex){
            DB::rollback();
            return redirect()->back()->with('error',$ex->getMessage());
        }
    }

    // public function getOrderid(Request $request){
    //     $orderIds = DB::table('del_orders')->join('')
    // }
    
    /*--- Working function but not releasing for now as one bug is there at the time of selecting value requires 2 clicks after search...*/
    // public function getOrderid(Request $request){
    //     if($request->get('selectedid')){
    //         $selectedIds = $request->get('selectedid');
    //     }else{
    //         $selectedIds = [];
    //     }
    //     $rawId = $request->get('rawid');
    //     // return $selectedIds;
    //     if(DB::table('del_orders')->select('order_id')->where('del_orders.order_id','LIKE','%'.$rawId.'%')->exists()){
    //         $orderIds = DB::table('del_orders')->select('order_id')->where('del_orders.order_id','LIKE','%'.$rawId.'%')->limit(5)->get()->pluck('order_id')->toArray();
    //     }else{
    //         $orderIds = [];
    //     }
    //     $orderIds = array_values(array_unique(array_merge($selectedIds,$orderIds)));
    //     return str_replace('"', '', $orderIds);
    // }
}
?>