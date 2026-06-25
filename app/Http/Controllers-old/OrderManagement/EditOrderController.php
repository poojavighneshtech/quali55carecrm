<?php

namespace App\Http\Controllers\OrderManagement;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\VendorRegister;
use App\Models\UserRegister;
use App\Models\DelOrders;
use App\Models\lead;
use App\Models\OrderDetails;
use App\Models\VendorProducts;
use App\Models\leads_log;
use App\Models\VendorProductDetails;
use App\Models\sale_vendor_products;
use App\Models\MasterProduct;
use App\Models\VendorRentedProducts;
use App\Models\Renewal;
use App\Models\Pickup;
use App\Models\ActivityLog;
use App\Http\Controllers\OrderManagement\OrderController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Mail;
use App\Exports\OrdersExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\RenewalPickup\RenewalPickupController;

class EditOrderController extends Controller
{
    public function isLoggedIn()
    {
        $data = session('isLoggedIn');
        return $data;
    }

    public function editOrder(Request $request,$order_id,$order_type)
    {
        if($order_type == "Delivery")
        {
            $products = DB::table('products')->select('products.*')->where('flag','Active')->get();
            $response = $this->editDelivery($order_id);
            $cities = DB::select("SELECT * FROM cities");
            $cities = \json_decode(\json_encode($cities), true);
            $states = DB::select("SELECT * FROM states");
            $states = \json_decode(\json_encode($states), true);
            $countries = DB::select("SELECT * FROM countries");
            $countries = \json_decode(\json_encode($countries), true);
            return view('OrderManagement.edit_del_order',compact('response','products','cities','states','countries'));
        }
        else if($order_type == "Collection")
        {
            $response = $this->editCollection($order_id);
        }
        else if($order_type == "Pick Up")
        {
            $response = $this->editPickup($order_id);
        }
    }

    public function editDelivery($order_id)
    {
        $order_details = DB::table('order_details')
                            ->join('vendor_details','order_details.vendor_id','=','vendor_details.id')
                            ->join('vendor_warehouse','order_details.vendor_warehouse_id','=','vendor_warehouse.id')
                            // ->join('vendor_products','order_details.vendor_product_id','=','vendor_products.id')
                            ->join('products','order_details.product_id','=','products.id')
                            ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                            ->join('product_brands','order_details.product_brand','=','product_brands.id')
                            ->join('del_orders','order_details.order_id','=','del_orders.order_id')
                            ->join('leads','leads.id','=','del_orders.lead_id')
                            ->select(
                                'customer_details.*',
                                'products.product_name as product_name',
                                'order_details.product_rent as product_rent',
                                'order_details.product_deposite as product_deposite',
                                'order_details.transport as transport',
                                'order_details.sale_rental as sale_rental',
                                'order_details.upgraded as upgraded',
                                'vendor_details.registered_name as vendor_name',
                                'vendor_warehouse.wh_name as warehouse_name',
                                'vendor_warehouse.wh_area as warehouse_area',
                                'vendor_warehouse.wh_city as warehouse_city',
                                'product_brands.brand_name as brand_name',
                                'order_details.product_batch as product_batch',
                                'order_details.unique_id as inventory_id',
                                'del_orders.lead_id as lead_id',
                                'del_orders.status as order_status',
                                'del_orders.DelDate as Delivery_date',
                                'leads.lead_source as lead_source',
                                'leads.patient_name as patient_name',
                                'leads.id as lead_id',
                                'order_details.order_id as order_id',
                                'order_details.id as order_details_id',
                                'order_details.product_id as product_id',
                                'order_details.product_qty as product_qty',
                                'order_details.creation_date as creation_date',
                                'order_details.current_status as status',
                                'order_details.remark'
                                )
                            ->where('order_details.order_id',$order_id)
                            ->get();
            foreach ($order_details as $index=>$detail)
            {
                $order_details[$index]->offered_rent = $order_details[$index]->product_rent;
                $order_details[$index]->offered_deposit = $order_details[$index]->product_deposite;
                $order_details[$index]->offered_transport = $order_details[$index]->transport;
                if(DB::table('cr_dr_note')->where('order_details_id',$detail->order_details_id)->exists())
                {
                    $cr_dr_data = DB::table('cr_dr_note')->where('order_details_id',$detail->order_details_id)->where('flag','A')->get();
                    foreach($cr_dr_data as $key=>$data)
                    {
                        if($data->intype == 'R')
                        {
                            if($data->crdrtype == 'Dr')
                            {
                                $order_details[$index]->product_rent = $order_details[$index]->product_rent + $data->amount;
                            }
                            else
                            {
                                $order_details[$index]->product_rent = $order_details[$index]->product_rent - $data->amount;
                            }
                        }
                        elseif($data->intype == 'D')
                        {
                            if($data->crdrtype == 'Dr')
                            {
                                $order_details[$index]->product_deposite = $order_details[$index]->product_deposite + $data->amount;
                            }
                            else
                            {
                                $order_details[$index]->product_deposite = $order_details[$index]->product_deposite - $data->amount;
                            }
                        }
                        else
                        {
                            if($data->crdrtype == 'Dr')
                            {
                                $order_details[$index]->transport = $order_details[$index]->transport + $data->amount;
                            }
                            else
                            {
                                $order_details[$index]->transport = $order_details[$index]->transport - $data->amount;
                            }
                        }
                    }
                }
            }
        // dd($order_details); 
        return $order_details;
        // 
    }


    public function editCollection($order_id)
    {
        
    }

    public function editPickup($order_id)
    {
        
    }
    public function getOrderDelivery($order_details_id)
    {
        $order_details = DB::table('order_details')
                            ->join('vendor_details','order_details.vendor_id','=','vendor_details.id')
                            ->join('vendor_warehouse','order_details.vendor_warehouse_id','=','vendor_warehouse.id')
                            // ->join('vendor_products','order_details.vendor_product_id','=','vendor_products.id')
                            ->join('products','order_details.product_id','=','products.id')
                            ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                            ->join('product_brands','order_details.product_brand','=','product_brands.id')
                            ->join('del_orders','order_details.order_id','=','del_orders.order_id')
                            ->select(
                                'customer_details.*',
                                'products.product_name as product_name',
                                'order_details.product_rent as product_rent',
                                'order_details.product_deposite as product_deposite',
                                'order_details.transport as transport',
                                'order_details.sale_rental as sale_rental',
                                'vendor_details.registered_name as vendor_name',
                                'vendor_warehouse.wh_name as warehouse_name',
                                'vendor_warehouse.wh_area as warehouse_area',
                                'vendor_warehouse.wh_city as warehouse_city',
                                'product_brands.brand_name as brand_name',
                                'order_details.product_batch as product_batch',
                                // 'vendor_products.batch as batch',
                                'order_details.unique_id as inventory_id',
                                'del_orders.lead_id as lead_id',
                                'del_orders.DelDate as Delivery_date',
                                'order_details.id as order_details_id',
                                'order_details.product_id as product_id',
                                'order_details.product_qty as product_qty',
                                'order_details.creation_date as creation_date',
                                'order_details.remark'
                                )
                            ->where('order_details.id',$order_details_id)
                            ->get();
        // dd($order_details); 
        return $order_details;
        // 
    }

    public function updateOrderProduct(Request $request)
    {
        try{
            if($request->get("request_type") == "fetch-order-product")
            {
                $order_details_id = $request->get("order_details_id");
                
                $response = $this->fetchOrderProductExists($order_details_id);
                return $response;
            }
            else if ($request->get("request_type") == "fetch-order-product-new")
            {
                $product_id = $request->get("product_id");
                $product_qty = $request->get("product_qty");
                $product_type = $request->get("product_type");
    
                $vendor_details = $this->getVendor($product_id,$product_qty,$product_type);
                $product_details = $this->getProduct($product_id);
                $data['product_details'] = $product_details;
                $data['vendor_details'] = $vendor_details;
                return $data;
            }
            else if($request->get("request_type") == "fetch-warehouse")
            {
                $product_id = $request->get("product_id");
                $vendor_id = $request->get("vendor_id");
                $product_type = $request->get("product_type");
                $data['warehouse_details'] = $this->getWarehouse($product_id,$vendor_id,$product_type);
                $data['virtual_warehouse_details'] = $this->getVirtualWarehouse($product_id,$vendor_id,$product_type);
                return $data;
            }
            else if($request->get("request_type") == "fetch-brand")
            {
                $product_id = $request->get("product_id");
                $vendor_id = $request->get("vendor_id");
                $warehouse_id = $request->get("warehouse_id");
                $product_type = $request->get("product_type");
                $data['brand_details'] = $this->getBrand($product_id,$vendor_id,$warehouse_id,$product_type);
                return $data;
            }
            else if($request->get("request_type") == "fetch-batch")
            {
                $product_id = $request->get("product_id");
                $vendor_id = $request->get("vendor_id");
                $brand_id = $request->get("brand_id");
                $warehouse_id = $request->get("warehouse_id");
                $product_type = $request->get("product_type");
                $data['batch_details'] = $this->getBatch($product_id,$vendor_id,$warehouse_id,$brand_id,$product_type);
                return $data;
            }
            else if($request->get("request_type") == "fetch-inventory")
            {
                $vendor_product_id = $request->get("brand_id");
                $warehouse_id = $request->get("warehouse_id");
                $product_type = $request->get("product_type");

                $data['inventory_details'] = $this->getInventory($request->get("vendor_id"),$request->get("warehouse_id"),$request->get("brand_id"),$request->get("product_id"));
                return $data;
            }
            else if($request->get("request_type") == "update-data-order")
            {
                DB::beginTransaction();
                $order_details_id = $request->get('order_details_id');
                $product_rent = $request->get('product_rent');
                $product_type = $request->get('product_type');
                $product_deposite = $request->get('product_deposite');
                $transport = $request->get('transport');
                $vendor = $request->get('vendor');
                $warehouse = $request->get('warehouse');
                $brand = $request->get('brand');
                // $batch = $request->get('batch');
                $inventory = $request->get('inventory');
                $remark = $request->get('remark');
                $sale_serial_no = $request->get('sale_serial_no');
                $sale_warranty = $request->get('sale_warranty');
                $remark = $request->get('remark');
                $order_details = DB::table('order_details')
                                ->join('products','order_details.product_id','=','products.id')
                                ->join('vendor_details','order_details.vendor_id','=','vendor_details.id')
                                ->select('order_details.*','products.product_name as product_name','vendor_details.registered_name as vendor_name')
                                ->where('order_details.id',$order_details_id)
                                ->get();
                if(DB::table('cr_dr_note')->where('order_details_id',$order_details_id)->exists())
                {
                    $cr_dr_data = DB::table('cr_dr_note')->where('order_details_id',$order_details_id)->where('flag','A')->get();
                    foreach($cr_dr_data as $key=>$data)
                    {
                        if($data->intype == 'R')
                        {
                            if($data->crdrtype == 'Dr')
                            {
                                $order_details[0]->product_rent = $order_details[0]->product_rent + $data->amount;
                            }
                            else
                            {
                                $order_details[0]->product_rent = $order_details[0]->product_rent - $data->amount;
                            }
                        }
                        elseif($data->intype == 'D')
                        {
                            if($data->crdrtype == 'Dr')
                            {
                                $order_details[0]->product_deposite = $order_details[0]->product_deposite + $data->amount;
                            }
                            else
                            {
                                $order_details[0]->product_deposite = $order_details[0]->product_deposite - $data->amount;
                            }
                        }
                        else
                        {
                            if($data->crdrtype == 'Dr')
                            {
                                $order_details[0]->transport = $order_details[0]->transport + $data->amount;
                            }
                            else
                            {
                                $order_details[0]->transport = $order_details[0]->transport - $data->amount;
                            }
                        }
                    }
                }
    
                $productOldData = $order_details->toArray();
                // if(Db::table('cr_dr_note')->where('order_details_id',$order_details_id)->where('flag','A')->exists())
                // {
                //     $globexists = true;
                // }
                // else{
                //     $globexists = false;
                // }
                $updateData = array();            
                $updatedDataActivityLog['key'] = array();
                $updatedDataActivityLog['old_value'] = array();
                $updatedDataActivityLog['new_value'] = array();
                if($order_details[0]->product_rent != $product_rent)
                {
                    if($order_details[0]->product_rent < $product_rent)
                    {
                        // Credited
                        DB::table('cr_dr_note')->insert([
                            'order_id'=>$order_details[0]->order_id,
                            'order_details_id'=>$order_details[0]->id,
                            'crdrtype'=>'Dr',
                            'intype'=>'R',
                            'amount'=>($product_rent - $order_details[0]->product_rent),
                            'createdby'=>session('username')
                        ]);
                    }
                    else
                    {
                        // Debited
                        Db::table('cr_dr_note')->insert([
                            'order_id'=>$order_details[0]->order_id,
                            'order_details_id'=>$order_details[0]->id,
                            'crdrtype'=>'Cr',
                            'intype'=>'R',
                            'amount'=>($order_details[0]->product_rent - $product_rent),
                            'createdby'=>session('username')
                        ]);
                    }
                    // $updateData['product_rent'] = $product_rent;
                    array_push($updatedDataActivityLog['key'],'Product Rent');
                    array_push($updatedDataActivityLog['old_value'],$order_details[0]->product_rent);
                    array_push($updatedDataActivityLog['new_value'],$product_rent);
                }
                if($order_details[0]->product_deposite != $product_deposite)
                {
                    if($order_details[0]->product_deposite < $product_deposite)
                    {
                        // Credited
                        Db::table('cr_dr_note')->insert([
                            'order_id'=>$order_details[0]->order_id,
                            'order_details_id'=>$order_details[0]->id,
                            'crdrtype'=>'Dr',
                            'intype'=>'D',
                            'amount'=>($product_deposite - $order_details[0]->product_deposite),
                            'createdby'=>session('username')
                        ]);
                    }
                    else
                    {
                        // Debited
                        Db::table('cr_dr_note')->insert([
                            'order_id'=>$order_details[0]->order_id,
                            'order_details_id'=>$order_details[0]->id,
                            'crdrtype'=>'Cr',
                            'intype'=>'D',
                            'amount'=>($order_details[0]->product_deposite - $product_deposite),
                            'createdby'=>session('username')
                        ]);
                    }
                    // $updateData['product_deposite'] = $product_deposite;
                    array_push($updatedDataActivityLog['key'],'Product Deposite');
                    array_push($updatedDataActivityLog['old_value'],$order_details[0]->product_deposite);
                    array_push($updatedDataActivityLog['new_value'],$product_deposite);
                }
                if($order_details[0]->transport != $transport)
                {
                    if($order_details[0]->transport < $transport)
                    {
                        // Credited
                        Db::table('cr_dr_note')->insert([
                            'order_id'=>$order_details[0]->order_id,
                            'order_details_id'=>$order_details[0]->id,
                            'crdrtype'=>'Dr',
                            'intype'=>'T',
                            'amount'=>($transport - $order_details[0]->transport),
                            'createdby'=>session('username')
                        ]);
                    }
                    else
                    {
                        // Debited
                        Db::table('cr_dr_note')->insert([
                            'order_id'=>$order_details[0]->order_id,
                            'order_details_id'=>$order_details[0]->id,
                            'crdrtype'=>'Cr',
                            'intype'=>'T',
                            'amount'=>($order_details[0]->transport - $transport),
                            'createdby'=>session('username')
                        ]);
                    }
                    // $updateData['transport'] = $transport;
                    array_push($updatedDataActivityLog['key'],'transport');
                    array_push($updatedDataActivityLog['old_value'],$order_details[0]->transport);
                    array_push($updatedDataActivityLog['new_value'],$transport);
                }
                if($order_details[0]->sale_rental != $product_type)
                {
                    $updateData['sale_rental'] = $product_type;
                    array_push($updatedDataActivityLog['key'],'sale_rental');
                    array_push($updatedDataActivityLog['old_value'],$order_details[0]->sale_rental);
                    array_push($updatedDataActivityLog['new_value'],$product_type);
                }
                if($order_details[0]->vendor_id != $vendor)
                {
                    $updateData['vendor_id'] = $vendor;
                    array_push($updatedDataActivityLog['key'],'vendor');
                    array_push($updatedDataActivityLog['old_value'],$order_details[0]->vendor_id);
                    array_push($updatedDataActivityLog['new_value'],$vendor);
                }
                if($order_details[0]->vendor_warehouse_id != $warehouse)
                {
                    $updateData['vendor_warehouse_id'] = $warehouse;
                    array_push($updatedDataActivityLog['key'],'warehouse');
                    array_push($updatedDataActivityLog['old_value'],$order_details[0]->vendor_warehouse_id);
                    array_push($updatedDataActivityLog['new_value'],$warehouse);
                }
                if($order_details[0]->product_brand != $brand)
                {
                    $updateData['product_brand'] = $brand;
                    array_push($updatedDataActivityLog['key'],'brand');
                    array_push($updatedDataActivityLog['old_value'],$order_details[0]->product_brand);
                    array_push($updatedDataActivityLog['new_value'],$brand);
                }
                if($product_type == "Rental")
                {
                    // if($order_details[0]->product_batch != $batch)
                    // {
                    //     $updateData['product_batch'] = $batch;
                    //     $updateData['vendor_product_id'] = $batch;
                    //     array_push($updatedDataActivityLog['key'],'batch');
                    //     array_push($updatedDataActivityLog['old_value'],$order_details[0]->product_batch);
                    //     array_push($updatedDataActivityLog['new_value'],$batch);
                    // }

                    if($order_details[0]->vendor_product_details_id != $inventory){
                        $order_detail = DB::table('customer_details')->join('order_details','order_details.customer_id','=','customer_details.cust_id')->select('customer_details.city','order_details.*')->where('order_details.id',$request->get('order_details_id'))->first();
                        $series = 1;
                        if($inventory == 'AG'){
                            $brand_id = $brand;
                            // echo $vendor." ".$warehouses[$key]." ".$brands[$key];
                            $vendor_product_id = DB::table('vendor_products')->where('vendor_id',$vendor)->where('warehouse_id',$warehouse)->where('product_brand',$brand)->first()->id;
                            if(DB::table('vendor_product_details')->join('vendor_products','vendor_products.id','=','vendor_product_details.vendor_products_id')->join('vendor_details','vendor_details.id','=','vendor_products.vendor_id')->select('series')->where('vendor_products.vendor_id',$vendor)){
                                $series = DB::table('vendor_product_details')->join('vendor_products','vendor_products.id','=','vendor_product_details.vendor_products_id')->join('vendor_details','vendor_details.id','=','vendor_products.vendor_id')->select('vendor_product_details.series')->where('vendor_products.vendor_id',$vendor)->orderBy('vendor_product_details.id','DESC')->first()->series;
                            }
                            $batchid = substr($order_detail->city,0,1).DB::table('vendor_details')->where('id',$vendor)->first()->vendor_code.DB::table('products')->where('id',$order_detail->product_id)->first()->product_code.date('my',strtotime($order_detail->creation_date)).($series+1);
                            $vendor_product_details_id = DB::table('vendor_product_details')->insertGetId(
                                [
                                    'vendor_products_id'=>$vendor_product_id,
                                    'availability_status'=>1,
                                    'inventory_id'=>$batchid,
                                    'inventory_type'=>0,
                                    'current_location'=>0,
                                    'warehouse_id'=>$warehouse,
                                    'additional_dateils'=>null,
                                    'series'=>$series++,
                                    'created_by'=>session('username')
                                ]
                            );
                        }else{
                            $vendor_product_details = DB::table('vendor_product_details')->where('id',$inventory)->first();
                            $vendor_product_id = $vendor_product_details->vendor_products_id;
                            $vendor_product_details_id = $vendor_product_details->id;
                            // $series = 0;
                            $batchid = $vendor_product_details->inventory_id;
                            $brand_id = $brand;
                        }
                        if($order_details[0]->status == "Accepted")
                        {
                            // echo $inventory;
                            // $inventory_id = VendorProductDetails::where('id',$inventory)->first('inventory_id');
                            $inventory_id = DB::table('vendor_product_details')->select('inventory_id')->where('id',$vendor_product_details_id)->get();
                            // if($order_details[0]->product_batch != $batch)
                            // {
                                $updateData['unique_id'] = $inventory_id[0]->inventory_id;
                                VendorRentedProducts::where('id',$order_details[0]->rented_product_id)->update(['unique_id'=>$inventory_id[0]->inventory_id, 'vendor_product_id'=>$vendor_product_id]);
                                array_push($updatedDataActivityLog['key'],'rented_product_inventory');
                                array_push($updatedDataActivityLog['old_value'],$order_details[0]->vendor_product_details_id);
                                array_push($updatedDataActivityLog['new_value'],$inventory_id[0]->inventory_id);
                            // }
                        }
                        VendorProductDetails::where('id',$order_details[0]->vendor_product_details_id)->update(['availability_status'=>0,'current_location'=>2]);
                        VendorProductDetails::where('id',$vendor_product_details_id)->update(['availability_status'=>1,'current_location'=>0]);
                        
                        $updateData['product_batch'] = $vendor_product_id;
                        $updateData['vendor_product_id'] = $vendor_product_id;
                        array_push($updatedDataActivityLog['key'],'batch');
                        array_push($updatedDataActivityLog['old_value'],$order_details[0]->product_batch);
                        array_push($updatedDataActivityLog['new_value'],$vendor_product_id);
                        
                        $updateData['vendor_product_details_id'] = $vendor_product_details_id;
                        array_push($updatedDataActivityLog['key'],'inventory');
                        array_push($updatedDataActivityLog['old_value'],$order_details[0]->vendor_product_details_id);
                        array_push($updatedDataActivityLog['new_value'],$vendor_product_details_id);
                        DB::table('vendor_rented_inventory')->where('order_details_id',$request->get('order_details_id'))->where('type','Delivery')->update(['flag'=>'Inactive']);
                        DB::table('vendor_rented_inventory')->insert(
                            [
                                "vendor_id" => $vendor,
                                "order_id" => $order_detail->order_id,
                                "order_details_id" => $request->get('order_details_id'),
                                "inventory_id" => $vendor_product_details_id,
                                "vendor_product_id" => $vendor_product_id,
                                "rented_date" => date('Y-m-d',strtotime($order_detail->creation_date)),
                                "due_date" => date('Y-m-d',strtotime("+1 months",strtotime($order_detail->creation_date))),
                                "status" => 'live',
                                'type' => 'Delivery',
                                'created_by' => session('username')
                            ]
                        );
                    }
                }
                else if($product_type == "Sale")
                {
                    $updateData['product_batch'] = 0;
                    $updateData['vendor_product_id'] = 0;
                    $updateData['vendor_product_details_id'] = 0;
                }
                $updateData['remark'] = $order_details[0]->remark.' ['.date("d-M-y h:i:sa").'] '.$remark;
                array_push($updatedDataActivityLog['key'],'remark');
                array_push($updatedDataActivityLog['old_value'],$order_details[0]->remark);
                array_push($updatedDataActivityLog['new_value'],$remark);
                OrderDetails::where('id',$order_details_id)->update($updateData);
                
                
                foreach($updatedDataActivityLog['key'] as $key =>$value)
                {
                    ActivityLog::insert([
                        'order_type'=>'DO',
                        'key_id'=>$order_details_id,
                        'operation'=>'Update Order Product Details',
                        'fields'=>$value,
                        'old_value'=>$updatedDataActivityLog['old_value'][$key],
                        'new_value'=>$updatedDataActivityLog['new_value'][$key],
                        'updated_by'=>session('username')
                    ]);
                }
                $order_id = DB::select("SELECT order_id FROM order_details WHERE id = $order_details_id");
                $order_id = json_decode(json_encode($order_id), true);
                $order_id = $order_id[0]['order_id'];
                // $total_amount = DB::select("SELECT sum(product_rent + product_deposite + transport) as total_amount FROM order_details WHERE order_id = $order_id");
                // $total_amount = json_decode(json_encode($total_amount), true);
                // $total_amount = $total_amount[0]['total_amount'];
                // // $total_amount = 0;
                // if(DB::table('cr_dr_note')->where('order_id',$order_id)->where('flag','A')->exists())
                // {
                //     $crdrdata = DB::table('cr_dr_note')->where('order_id',$order_id)->where('flag','A')->get();
                //     foreach($crdrdata as $k=>$data)
                //     {
                //         if($data->crdrtype == 'Dr')
                //         {
                //             $total_amount = $total_amount + $data->amount;
                //         }
                //         else
                //         {
                //             $total_amount = $total_amount - $data->amount;
                //         }
                //     }
                // }
                // DelOrders::where('order_id', $order_id)->update(['TotalAmt'=>$total_amount]);

                // ----------New Code Starts Here.....
                $remaining_products = DB::table('order_details')->select('id','product_rent','product_deposite','transport')->where('order_id',$order_id)->whereNotIn('current_status',['Cancel'])->get();
                // $orderDetailsIds = $remaining_products->pluck('id')->toArray();
                foreach($remaining_products as $key=>$remainingProduct){
                    $remaining_products[$key]->product_rent =  RenewalPickupController::fetchCrDrData($remainingProduct->id,'R');
                    $remaining_products[$key]->product_deposite =  RenewalPickupController::fetchCrDrData($remainingProduct->id,'D');
                    $remaining_products[$key]->transport =  RenewalPickupController::fetchCrDrData($remainingProduct->id,'T');
                }
                $actualTotalAmt = $remaining_products->pluck('product_rent')->sum() + $remaining_products->pluck('product_deposite')->sum() + $remaining_products->pluck('transport')->sum();
                DB::table('del_orders')->where('order_id',$order_id)->update(['TotalAmt'=>$actualTotalAmt]);

                $order_details = $this->getOrderDelivery($order_details_id);
    
                //send mail to account product updated 
                $orderDate1 = DB::table('del_orders')->where('order_id',$order_id)->select('DelDate','lead_id')->first();
                $orderDate = $orderDate1->DelDate;
                $lead_id = $orderDate1->lead_id;
                $order_id = $productOldData[0]->order_id;
                $customer_name = DB::table('customer_details')->where('cust_id',$productOldData[0]->customer_id)->first();
                $customer_name = $customer_name->customer_name;
                $vendor_name = DB::table('vendor_details')->where('id',$request->get('vendor'))->first();
                $vendor_name = $vendor_name->registered_name;
                $accountsEmail = config('app.accounts_email');
                //$accountsEmail = 'viveks@quali55care.com';
                $this->matchLeadProducts($lead_id);
                $orderType = 'Delivery';
                $modifiedType = 'Content';
                $change = 'Product-update';
                $modifiedBy = session('username');
                if($productOldData[0]->sale_rental == 'Rental'){
                    $this->updateVendorInOutInventory($order_details_id,'in');
                }

                // Mail::send('OrderModifyEmail/order-modifyEmail',compact('request','order_id','customer_name','orderDate','orderType','modifiedType','modifiedBy','change','productOldData','updatedDataActivityLog','vendor_name'), function($message) use($accountsEmail,$order_id)
                // {  
                //     $message->to($accountsEmail, 'Accounts')->subject('Order Modified - '.$order_id);
                //     //$message->to($senderMail->email_id_user, 'Complaint Raised')->subject('Complaint Raised');
                //     $message->from('tempmailquali@gmail.com', 'Quali55Care');
                // });
                
                $old_product = "";
                $old_product .="Product Name: ".$productOldData[0]->product_name;
                $old_product .=" | Rent/Sale: ".$productOldData[0]->product_rent;
                $old_product .=" | Deposit: ".$productOldData[0]->product_deposite;
                $old_product .=" | Transport: ".$productOldData[0]->transport;
                $old_product .=" | Type: ".$productOldData[0]->sale_rental;
                $old_product .=" | Vendor: ".$productOldData[0]->vendor_name;
                
                //return json_encode($old_product);
                $new_product = "";
                $new_product .= "Product Name: ".$productOldData[0]->product_name;
                $new_product .= " | Rent/Sale: ".$request->get('product_rent');
                $new_product .= " | Deposit: ".$request->get('product_deposite');
                $new_product .= " | Transport: ".$request->get('transport');
                $new_product .= " | Type: ".$productOldData[0]->sale_rental;
                $new_product .= " | Vendor: ".$vendor_name;
                $changes_done = "";
                
                foreach ($updatedDataActivityLog['key'] as $key=>$data)
                {
                    $changes_done .= "Change: ".$data;
                    $changes_done .= " Old: ".$updatedDataActivityLog['old_value'][$key];
                    $changes_done .= " New: ".$updatedDataActivityLog['new_value'][$key];
                }
    
                $accounts_nos = config('app.accounts_staff_contacts');
                // foreach($accounts_nos as $key=>$value)
                // {
                //     $url = "https://lj7724mli5.execute-api.ap-south-1.amazonaws.com/prod/sendwhatsappmsg";
                //     $curl = curl_init();
                //     curl_setopt($curl, CURLOPT_URL, $url);
                //     curl_setopt($curl, CURLOPT_POST, true);
                //     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    
                //     $headers = array(
                //         "Accept: application/json",
                //         "Content-Type: application/json",
                //     );
    
                //     curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                //     $data =[
                //         "portno"=>"11140",
                //         "namespace"=>"b9a23cb4_89ed_4fe2_b849_20775908ff5e",
                //         "countrycode"=> "91",
                //         "mobileno"=> "$value",
                //         "templatename" => "change_order_rent_update",
                //         "templateparams" => [
                //             ["type"=> "text","text"=> "$order_id"],
                //             ["type"=> "text","text"=> "$orderType"],
                //             ["type"=> "text","text"=> "$customer_name"],
                //             ["type"=> "text","text"=> "$orderDate"],
                //             ["type"=> "text","text"=> "$modifiedBy"],
                //             ["type"=> "text","text"=> "$old_product"],
                //             ["type"=> "text","text"=> "$new_product"],
                //             ["type"=> "text","text"=> "$changes_done"]
                //         ],
                //     ];
                //     curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                    
                //     $resp = curl_exec($curl);
    
                //     // return $resp;
                //     curl_close($curl);
                // }
                DB::commit();
                return $order_details;
            }
            else if($request->get("request_type") == "remove-product")
            {
                DB::beginTransaction();
                $order_details_id = $request->get("order_details_id");
                $order_details = DB::table('order_details')->select('order_details.*','products.product_name','vendor_details.registered_name as vendor_name')
                                    ->join('products','order_details.product_id','=','products.id')
                                    ->join('vendor_details','order_details.vendor_id','=','vendor_details.id')
                                    ->where('order_details.id',$order_details_id)->first();
                $vendor_product_details_id = $order_details->vendor_product_details_id;
                VendorProductDetails::where('id',$vendor_product_details_id)->update(["availability_status" => 0, "current_location" => 2]);
                
                
                $order_id = DB::table('order_details')->where('id',$order_details_id)->first('order_id');
                $order_id = $order_id->order_id;
                // $order_id = json_decode(json_encode($order_id), true);
                // $order_id = $order_id[0]['order_id'];            
                // OrderDetails::where('id',$order_details_id)->delete();
                OrderDetails::where('id',$order_details_id)->update(['current_status'=>'Cancel']);
                DB::table('vendor_inventory_mgmt')->where('details_id',$order_details_id)->where('state','in')->update(['flag'=>'Inactive']);
                // $total_amount = DB::select("SELECT sum(product_rent + product_deposite + transport) as total_amount FROM order_details WHERE order_id = $order_id AND current_status != 'Cancel'");
                // $total_amount = json_decode(json_encode($total_amount), true);
                // $total_amount = $total_amount[0]['total_amount'];
                // DelOrders::where('order_id', $order_id)->update(['TotalAmt'=>$total_amount]);
                $remaining_products = DB::table('order_details')->select('id','product_rent','product_deposite','transport')->where('order_id',$order_id)->whereNotIn('current_status',['Cancel'])->get();
                // $orderDetailsIds = $remaining_products->pluck('id')->toArray();
                foreach($remaining_products as $key=>$remainingProduct){
                    $remaining_products[$key]->product_rent =  RenewalPickupController::fetchCrDrData($remainingProduct->id,'R');
                    $remaining_products[$key]->product_deposite =  RenewalPickupController::fetchCrDrData($remainingProduct->id,'D');
                    $remaining_products[$key]->transport =  RenewalPickupController::fetchCrDrData($remainingProduct->id,'T');
                }
                $actualTotalAmt = $remaining_products->pluck('product_rent')->sum() + $remaining_products->pluck('product_deposite')->sum() + $remaining_products->pluck('transport')->sum();
                DB::table('del_orders')->where('order_id',$order_id)->update(['TotalAmt'=>$actualTotalAmt]);
                // $actual
                ActivityLog::insert([
                    'order_type'=>'DO',
                    'key_id'=>$order_details_id,
                    'operation'=>'Remove Product',
                    'fields'=>'Product_id',
                    'old_value'=>$order_details->product_id,
                    'new_value'=>'-',
                    'updated_by'=>session('username')
                ]);
    
                //sent mail product removed
                $order_id = $order_details->order_id;
                
                // Recalculate rent deposit transport

                $order_details->product_rent =  RenewalPickupController::fetchCrDrData($order_details->id,'R');
                $order_details->product_deposite =  RenewalPickupController::fetchCrDrData($order_details->id,'D');
                $order_details->transport =  RenewalPickupController::fetchCrDrData($order_details->id,'T');

                $orderDate1 = DB::table('del_orders')->where('order_id',$order_id)->first();
                $orderDate = $orderDate1->DelDate;
                $this->matchLeadProducts($orderDate1->lead_id);
                $customer_name = DB::table('customer_details')->where('cust_id',$order_details->customer_id)->first();
                $customer_name = $customer_name->customer_name;
                $accountsEmail = config('app.accounts_email');
                //$accountsEmail = 'viveks@quali55care.com';
                $orderType = 'Delivery';
                $modifiedType = 'Content';
                $change = 'Product-remove';
                $changed = 'Removed';
                $modifiedBy = session('username');
                $product_details = "";
                $product_details .= "Product Name: ".$order_details->product_name;
                $product_details .= " | Rent/Sale: ".$order_details->product_rent;
                $product_details .= " | Deposit: ".$order_details->product_deposite;
                $product_details .= " | Transport: ".$order_details->transport;
                $product_details .= " | Type: ".$order_details->sale_rental;
                $product_details .= " | Vendor: ".$order_details->vendor_name;
                if($order_details->product_rent !=0)
                {
                    DB::table('cr_dr_note')->insert([
                        'order_id'=>$order_details->order_id,
                        'order_details_id'=>$order_details->id,
                        'crdrtype'=>'Cr',
                        'intype'=>'R',
                        'amount'=>($order_details->product_rent),
                        'createdby'=>session('username')
                    ]);
                }
                if($order_details->product_deposite !=0)
                {
                    DB::table('cr_dr_note')->insert([
                        'order_id'=>$order_details->order_id,
                        'order_details_id'=>$order_details->id,
                        'crdrtype'=>'Cr',
                        'intype'=>'D',
                        'amount'=>($order_details->product_deposite),
                        'createdby'=>session('username')
                    ]);
                }
                if($order_details->transport !=0)
                {
                    DB::table('cr_dr_note')->insert([
                        'order_id'=>$order_details->order_id,
                        'order_details_id'=>$order_details->id,
                        'crdrtype'=>'Cr',
                        'intype'=>'T',
                        'amount'=>($order_details->transport),
                        'createdby'=>session('username')
                    ]);
                }
                // Mail::send('OrderModifyEmail/order-modifyEmail',compact('request','order_id','customer_name','orderDate','orderType','modifiedType','modifiedBy','change','order_details'), function($message) use($accountsEmail,$order_id)
                // {  
                //     $message->to($accountsEmail, 'Accounts')->subject('Order Modified - '.$order_id);
                //     //$message->to($senderMail->email_id_user, 'Complaint Raised')->subject('Complaint Raised');
                //     $message->from('tempmailquali@gmail.com', 'Quali55Care');
                // });
    
                $accounts_nos = config('app.accounts_staff_contacts');
                // foreach($accounts_nos as $key=>$value)
                // {
                //     $url = "https://lj7724mli5.execute-api.ap-south-1.amazonaws.com/prod/sendwhatsappmsg";
                //     $curl = curl_init();
                //     curl_setopt($curl, CURLOPT_URL, $url);
                //     curl_setopt($curl, CURLOPT_POST, true);
                //     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    
                //     $headers = array(
                //         "Accept: application/json",
                //         "Content-Type: application/json",
                //     );
                //     curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                //     $data =[
                //         "portno"=>"11140",
                //         "namespace"=>"b9a23cb4_89ed_4fe2_b849_20775908ff5e",
                //         "countrycode"=> "91",
                //         "mobileno"=> "$value",
                //         "templatename" => "change_order_product_added_removed",
                //         "templateparams" => [
                //             ["type"=> "text","text"=> $changed],
                //             ["type"=> "text","text"=> "$order_id"],
                //             ["type"=> "text","text"=> $customer_name],
                //             ["type"=> "text","text"=> $orderDate],
                //             ["type"=> "text","text"=> $modifiedBy],
                //             ["type"=> "text","text"=> $product_details]
                //         ],
                //     ];
                //     curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                    
                //     $resp = curl_exec($curl);
                //     // return $resp;
                //     curl_close($curl);
                // }
                DB::commit();
                return true;
            }
            else if($request->get("request_type") == "edit_address")
            {
                $customer_id = $request->get('hidden_customer_id');
                $customer_name = $request->get('hidden_customer_name');
                $full_details = $customer_name.' '.$request->get('address_line_1').', '.$request->get('address_line_2').', '.$request->get('landmark').', '.$request->get('area').', '.$request->get('city').', '.$request->get('Pincode').', '.$request->get('state').', '.$request->get('country');
                $order_id = $request->get('hidden_order_id');
                //get old address
                $oldAddress = DB::table('del_orders')->where('order_id',$order_id)->first('fulldetails');
                $update_array = [
                    'address_line_1'=>$request->get('address_line_1'),
                    'address_line_2'=>$request->get('address_line_2'),
                    'landmark'=>$request->get('landmark'),
                    'area'=>$request->get('area'),
                    'city'=>$request->get('city1'),
                    'state'=>$request->get('state'),
                    'country'=>$request->get('country'),
                    'location'=>$request->get('location'),
                    'pincode'=>$request->get('pincode')
                ];
                DB::table('customer_details')->where('cust_id',$customer_id)->update($update_array);
                DB::table('del_orders')->where('order_id',$order_id)->update(['fulldetails'=>$full_details]);
                //activity log
                $insertData = [
                    'order_type'=>'DO',
                    'key_id'=>$order_id,
                    'operation'=>'Address Changed',
                    'fields'=>'fulldetails',
                    'old_value'=>$oldAddress->fulldetails,
                    'new_value'=>$full_details,
                    'updated_by'=>session('username')
                ];
                ActivityLog::insert($insertData); //insert into activity log
                //order modify mail to accounts
                $orderDate = DB::table('del_orders')->where('order_id',$order_id)->select('DelDate')->first();
                $orderDate = $orderDate->DelDate;
                $customer_name = DB::table('customer_details')->where('cust_id',$customer_id)->first();
                $customer_name = $customer_name->customer_name;
                $accountsEmail = config('app.accounts_email');
                //$accountsEmail = 'viveks@quali55care.com';
                $orderType = 'Delivery';
                $modifiedType = 'Content';
                $change = 'address-changed';
                $old_address = $oldAddress->fulldetails;
                $new_address = $full_details;
                $modifiedBy = session('username');
                // Mail::send('OrderModifyEmail/order-modifyEmail',compact('request','order_id','customer_name','orderDate','orderType','modifiedType','modifiedBy','change','old_address','new_address'), function($message) use($accountsEmail,$order_id)
                // {  
                //     $message->to($accountsEmail, 'Accounts')->subject('Order Modified - '.$order_id);
                //     //$message->to($senderMail->email_id_user, 'Complaint Raised')->subject('Complaint Raised');
                //     $message->from('tempmailquali@gmail.com', 'Quali55Care');
                // });
    
                $accounts_nos = config('app.accounts_staff_contacts');
                // foreach($accounts_nos as $key=>$value)
                // {
                //     $url = "https://lj7724mli5.execute-api.ap-south-1.amazonaws.com/prod/sendwhatsappmsg";
                //     $curl = curl_init();
                //     curl_setopt($curl, CURLOPT_URL, $url);
                //     curl_setopt($curl, CURLOPT_POST, true);
                //     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    
                //     $headers = array(
                //         "Accept: application/json",
                //         "Content-Type: application/json",
                //     );
                //     curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                //     $data =[
                //         "portno"=>"11140",
                //         "namespace"=>"b9a23cb4_89ed_4fe2_b849_20775908ff5e",
                //         "countrycode"=> "91",
                //         "mobileno"=> "$value",
                //         "templatename" => "change_order_address_modified",
                //         "templateparams" => [
                //             ["type"=> "text","text"=> $order_id],
                //             ["type"=> "text","text"=> $orderType],
                //             ["type"=> "text","text"=> $customer_name],
                //             ["type"=> "text","text"=> $orderDate],
                //             ["type"=> "text","text"=> $modifiedBy],
                //             ["type"=> "text","text"=> $old_address],
                //             ["type"=> "text","text"=> $new_address]
                //         ],
                //     ];
                //     //dd($data);
                //     curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                    
                //     $resp = curl_exec($curl);
                //     // dd($resp);
                //     curl_close($curl);
                // }
    
                return redirect()->back()->with('message','Address Updated Successfully!');
            }
            else if($request->get("request_type") == "edit_patient_name")
            {
                $lead_id = $request->get('modal_lead_id');
                $patient_name = $request->get('modal_patient_name');
                DB::table('del_orders')->where('lead_id',$lead_id)->update(['patient_name'=>$patient_name]);
                DB::table('leads')->where('id',$lead_id)->update(['patient_name'=>$patient_name]);
                return redirect()->back()->with('message','Patient Name Updated Successfully!');
            }
            else if($request->get("request_type") == "fetch-order-product-details")
            {
                $data['details'] = DB::table('order_details')->join('products','products.id','=','order_details.product_id')->select('order_details.*','products.product_name')->where('order_details.id',$request->get('order_details_id'))->get();
    
                $deposit_available = DB::table('order_details')->join('products','products.id','=','order_details.product_id')->select('order_details.*','products.product_name')->where('order_details.customer_id',$request->get('customer_id'))->whereNotIn('order_details.id',[$request->get('order_details_id')])->whereNotIn('order_details.current_status',['Picked up','Cancel'])->where('sale_rental','Rental')->get();
    
                foreach($deposit_available as $key=>$value)
                {
                    if(DB::table('adjustment_table')->where('order_details_id',$request->get('order_details_id'))->where('adjusted_order_details_id',$value->id)->where('adjustment_table.flag','A')->exists())
                    {
                        $adjusted_record = DB::table('adjustment_table')->where('order_details_id',$request->get('order_details_id'))->where('adjusted_order_details_id',$value->id)->where('adjustment_table.flag','A')->first();
                        $deposit_available[$key]->adjusted_deposit = $adjusted_record->adjusted_amount;
                    }
                    else
                    {
                        $deposit_available[$key]->adjusted_deposit = 0;
                    }
                }
                $data['deposit_available'] = $deposit_available;
    
                return $data;
            }
            else if($request->get("request_type") == "adjust-deposit")
            {
                // dd($request->all());
                $act_order_details = DB::table('order_details')->where('id',$request->get('act_order_details_id'))->first();
                foreach($request->get('adjusted_deposit') as $key=>$value)
                {
                    $adjusted_order_details = DB::table('order_details')->where('id',$request->get('hidden_adjusted_order_details_id')[$key])->first();
                    
                    if(DB::table('adjustment_table')->where('order_details_id',$request->get('act_order_details_id'))->where('adjusted_order_details_id',$request->get('hidden_adjusted_order_details_id')[$key])->where('adjustment_table.flag','A')->exists())
                    {
                        DB::table('adjustment_table')->where('order_details_id',$request->get('act_order_details_id'))->where('product_id',$act_order_details->product_id)->where('adjusted_order_details_id',$request->get('hidden_adjusted_order_details_id')[$key])->update(['adjusted_amount'=>$value]);
                    }
                    else
                    {
                        if($value !=0 && $value !="" && $value != null)
                        {
                            $insertData = [
                                'product_id'=>$act_order_details->product_id,
                                'order_id'=>$adjusted_order_details->order_id,
                                'order_details_id'=>$request->get('act_order_details_id'),
                                'adjusted_order_details_id'=>$request->get('hidden_adjusted_order_details_id')[$key],
                                'fromorderid'=>$act_order_details->order_id,
                                'adjusted_amount'=>$value,
                                'fromtype'=>'D',
                                'intype'=>'R',
                            ];
                            DB::table('adjustment_table')->insert($insertData);
                        }
                    }
                }
                return redirect()->back()->with('message','Deposit Adjusted Successfully!');
            }
            else if($request->get("request_type") == "fetch-record")
            {
                $order_details = DB::table('order_details')
                    ->join('products','products.id','=','order_details.product_id')
                    ->select('order_details.*','products.product_name')
                    ->where('order_details.id',$request->get('order_details_id'))
                    ->first();
                $order_details->product_rent =  RenewalPickupController::fetchCrDrData($request->get('order_details_id'),'R');
                $order_details->product_deposite =  RenewalPickupController::fetchCrDrData($request->get('order_details_id'),'D');
                $order_details->transport =  RenewalPickupController::fetchCrDrData($request->get('order_details_id'),'T');
                return json_encode($order_details);
            }
            else if($request->get("request_type") == 'convert-rent-to-sale'){
                try{
                    DB::beginTransaction();
                    $order_details = DB::table('order_details')->join('del_orders','del_orders.order_id','=','order_details.order_id')->where('order_details.id',$request->get('rts_order_details_id'))->first();
                    
                    $order_details->product_rent =  RenewalPickupController::fetchCrDrData($request->get('rts_order_details_id'),'R');
                    $order_details->product_deposite =  RenewalPickupController::fetchCrDrData($request->get('rts_order_details_id'),'D');
                    $order_details->transport =  RenewalPickupController::fetchCrDrData($request->get('rts_order_details_id'),'T');
                    
                    dd($request->all());
                    // Create Pick Up order
                    DB::commit();
                }catch(Exception $exc){
                    DB::rollback();
                    return redirect()->back()->with('error',$exc->getMessage());
                }
                

            }
            else{
                return "Else";
            }
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function fetchOrderProductExists($order_details_id)
    {
        // $order_details = OrderDetails::where('id',$order_details_id)->get();
        $order_details = DB::table('order_details')
                            ->join('products','order_details.product_id','=','products.id')
                            ->join('vendor_details','order_details.vendor_id','=','vendor_details.id')
                            ->select('order_details.*','products.product_name as product_name','vendor_details.registered_name as vendor_name')
                            ->where('order_details.id',$order_details_id)
                            ->get();
        // return $order_details;
        foreach ($order_details as $index=>$detail)
        {
            if(DB::table('cr_dr_note')->where('order_details_id',$detail->id)->exists())
            {
                $cr_dr_data = DB::table('cr_dr_note')->where('order_details_id',$detail->id)->where('flag','A')->get();
                foreach($cr_dr_data as $key=>$crdr)
                {
                    if($crdr->intype == 'R')
                    {
                        // return "c";
                        if($crdr->crdrtype == 'Dr')
                        {
                            $order_details[$index]->product_rent = ($order_details[$index]->product_rent + $crdr->amount);
                        }
                        else
                        {
                            $order_details[$index]->product_rent = ($order_details[$index]->product_rent - $crdr->amount);
                        }
                    }
                    elseif($crdr->intype == 'D')
                    {
                        // return "a";
                        if($crdr->crdrtype == 'Dr')
                        {
                            $order_details[$index]->product_deposite = ($order_details[$index]->product_deposite + $crdr->amount);
                        }
                        else
                        {
                            $order_details[$index]->product_deposite = ($order_details[$index]->product_deposite - $crdr->amount);
                        }
                    }
                    else
                    {
                        // return "b";
                        if($crdr->crdrtype == 'Dr')
                        {
                            $order_details[$index]->transport = ($order_details[$index]->transport + $crdr->amount);
                        }
                        else
                        {
                            $order_details[$index]->transport = ($order_details[$index]->transport - $crdr->amount);
                        }
                    }
                }
            }
        }
        // return $order_details;
        // echo $order_details[0]->product_id,$order_details[0]->product_qty;
        $vendor_details = $this->getVendor($order_details[0]->product_id,$order_details[0]->product_qty,$order_details[0]->sale_rental);
        $warehouse_details = $this->getWarehouse($order_details[0]->product_id,$order_details[0]->vendor_id,$order_details[0]->sale_rental);
        $virtual_warehouse_details = $this->getVirtualWarehouse($order_details[0]->product_id,$order_details[0]->vendor_id,$order_details[0]->sale_rental);
        $brand_details = $this->getBrand($order_details[0]->product_id,$order_details[0]->vendor_id,$order_details[0]->vendor_warehouse_id,$order_details[0]->sale_rental);
        $batch_details = $this->getBatch($order_details[0]->product_id,$order_details[0]->vendor_id,$order_details[0]->vendor_warehouse_id,$order_details[0]->product_brand,$order_details[0]->sale_rental);
        $inventory_details = $this->getInventory($order_details[0]->vendor_id,$order_details[0]->vendor_warehouse_id,$order_details[0]->product_brand,$order_details[0]->product_id);

        $data['order_details'] = $order_details;
        $data['vendor_details'] = $vendor_details;
        $data['warehouse_details'] = $warehouse_details;
        $data['virtual_warehouse_details'] = $virtual_warehouse_details;
        $data['brand_details'] = $brand_details;
        $data['batch_details'] = $batch_details;
        $data['inventory_details'] = $inventory_details;

        return $data;
    }

    public function getVendor($product_id,$product_qty,$product_type)
    {
        if($product_type == 'Rental')
        {
            $vendors = DB::select("SELECT DISTINCT vendor_details.id as vendor_id, vendor_details.registered_name as vendor_name FROM vendor_details, vendor_products,vendor_product_details WHERE vendor_products.product_id = $product_id AND vendor_products.vendor_id = vendor_details.id AND vendor_products.product_quantity >= $product_qty AND (vendor_products.status != 'Pending' OR vendor_products.status != 'Rejected') AND (vendor_product_details.vendor_products_id = vendor_products.id)");
            return $vendors;
        }
        else if($product_type == 'Sale')
        {
            $vendors = DB::select("SELECT DISTINCT vendor_details.id as vendor_id, vendor_details.registered_name as vendor_name FROM vendor_details, vendor_products WHERE vendor_products.vendor_id = vendor_details.id AND (vendor_products.status != 'Pending' OR vendor_products.status != 'Rejected')");
            return $vendors;
        }
    }

    public function getWarehouse($product_id,$vendor_id,$product_type)
    {
        if($product_type == "Rental")
        {
            $warehouses = DB::select("SELECT DISTINCT vendor_warehouse.id as warehouse_id, vendor_warehouse.wh_name as wh_name, vendor_warehouse.wh_area as wh_area, vendor_warehouse.wh_city as wh_city FROM vendor_products,vendor_warehouse,vendor_product_details WHERE vendor_products.vendor_id = $vendor_id AND vendor_products.id = vendor_product_details.vendor_products_id AND vendor_product_details.warehouse_id = vendor_warehouse.id");
            return $warehouses;
        }
        else if($product_type == "Sale")
        {
            $warehouses = DB::select("SELECT DISTINCT vendor_warehouse.id as warehouse_id, vendor_warehouse.wh_name as wh_name, vendor_warehouse.wh_area as wh_area, vendor_warehouse.wh_city as wh_city FROM vendor_warehouse,vendor_products WHERE vendor_products.vendor_id = $vendor_id AND vendor_products.warehouse_id = vendor_warehouse.id ");
            return $warehouses;
        }
    }

    public function getVirtualWarehouse($product_id,$vendor_id,$product_type)
    {
        if($product_type == "Rental")
        {
            $warehouses = DB::select("SELECT DISTINCT vendor_warehouse.id as warehouse_id, vendor_warehouse.wh_name as wh_name, vendor_warehouse.wh_area as wh_area, vendor_warehouse.wh_city as wh_city FROM vendor_products,vendor_warehouse,vendor_product_details WHERE vendor_products.product_id = $product_id AND vendor_products.vendor_id = $vendor_id AND vendor_products.id = vendor_product_details.vendor_products_id AND vendor_product_details.warehouse_id = vendor_warehouse.id AND vendor_product_details.current_location IN(1)");
            return $warehouses;
        }
        else if($product_type == "Sale")
        {
            $warehouses = DB::select("SELECT DISTINCT vendor_warehouse.id as warehouse_id, vendor_warehouse.wh_name as wh_name, vendor_warehouse.wh_area as wh_area, vendor_warehouse.wh_city as wh_city FROM vendor_warehouse,vendor_products WHERE vendor_products.vendor_id = $vendor_id AND vendor_products.warehouse_id = vendor_warehouse.id ");
            return $warehouses;
        }
    }

    public function getBrand($product_id,$vendor_id,$warehouse_id,$product_type) 
    { 
        if($product_type == "Rental")
        {
            $brands = DB::select("SELECT DISTINCT product_brands.id as brand_id, product_brands.brand_name as brand_name FROM product_brands,vendor_products,vendor_product_details WHERE vendor_products.product_id = $product_id AND vendor_products.vendor_id = $vendor_id AND vendor_product_details.vendor_products_id = vendor_products.id AND vendor_product_details.warehouse_id = $warehouse_id AND product_brands.id = vendor_products.product_brand");
            return $brands;
        }
        else if($product_type == "Sale")
        {
            $brands = DB::select("SELECT DISTINCT product_brands.id as brand_id, product_brands.brand_name as brand_name FROM product_brands WHERE product_brands.product_id = $product_id ");
            return $brands;
        }
    }

    public function getBatch($product_id,$vendor_id,$warehouse_id,$brand_id,$product_type) 
    { 
        if($product_type == "Rental")
        {
            $batches = DB::select("SELECT DISTINCT vendor_products.id as vendor_product_id, vendor_products.batch as batch_name, vendor_products.product_rent_approved as product_rent FROM vendor_products,vendor_product_details WHERE vendor_products.product_id = $product_id AND vendor_products.vendor_id = $vendor_id AND vendor_product_details.warehouse_id = $warehouse_id AND vendor_product_details.vendor_products_id = vendor_products.id AND vendor_products.product_brand = $brand_id");
            return $batches;
        }
        else if($product_type == "Sale")
        {
            return "-";
            // $brands = DB::select("SELECT DISTINCT product_brands.id as brand_id, product_brands.brand_name as brand_name FROM product_brands WHERE product_brands.product_id = $product_id ");
            // return $brands;
        }
    }

    public function getInventory($vendor_id,$warehouse_id,$brand_id,$product_id)
    {
        $inventory = DB::select("SELECT vendor_product_details.id as vendor_product_details_id, vendor_product_details.inventory_id as inventory_id FROM vendor_product_details,vendor_products WHERE vendor_products.id = vendor_product_details.vendor_products_id AND vendor_products.vendor_id = $vendor_id AND vendor_products.product_id = $product_id AND vendor_products.product_brand = $brand_id AND vendor_product_details.warehouse_id = $warehouse_id AND vendor_product_details.availability_status=0 AND vendor_product_details.current_location != 0");
        return $inventory;
    }

    public function getProduct($product_id)
    {
        $product_details = DB::table('products')
                                ->select(
                                    'products.id as product_id',
                                    'products.product_deposite as product_deposite',
                                    'products.product_rent as product_rent',
                                    'products.product_sale_rate as product_sale_rate',
                                    'product_transport_cost')
                                ->where('products.id', $product_id)
                                ->get();
        return $product_details;
    }

    public function addOrderProduct(Request $request)
    {
        if($request->get("request_type") == "fetch-product-details")
        {
            $product_id = $request->get("product_id");
            $product_type = $request->get("product_type");
            $response['product_details'] = DB::table('products')->select('products.*')->where('id',$product_id)->get();
            $response['vendor_details'] = $this->getVendor($product_id,1,$product_type);
            return $response;
        }
        // Code not in use as we are not allowing users to add new product in order.....
        if($request->get("request_type") == "add-product")
        {
            $del_orders = new DelOrders();
            $order_details = new OrderDetails();
            $sale_vendor_products = new sale_vendor_products();
            $leads_log = new leads_log();

            $creation_date = $request->get("creation_date");
            $order_id = $request->get("order_id");
            $order_details_id = $request->get("order_details_id");
            $customer_id = $request->get("customer_id");
            $new_product_name = $request->get("new_product_name");
            $new_product_type = $request->get("new_product_type");
            $new_select_vendor = $request->get("new_select_vendor");
            $new_select_warehouse = $request->get("new_select_warehouse");
            $new_select_brand = $request->get("new_select_brand");
            $new_select_batch = $request->get("new_select_batch");
            $new_select_inventory = $request->get("new_select_inventory");
            $new_product_rent = $request->get("new_product_rent");
            $new_product_deposite = $request->get("new_product_deposite");
            $new_transport = $request->get("new_transport");
            $new_select_brand = $request->get("new_select_brand");
            $new_product_qty = 1;

            if($new_product_name == "Sale")
            {
                $status = "Pending";
                $insertData = 
                [
                    'order_id'=> $order_id,
                    'vendor_id' => $new_select_vendor,
                    'product_id' => $new_product_name,
                    'sale_price' => $new_product_rent,
                    'vendor_sale_price' => 0,
                    'vendor_warehouse_id' => $new_select_warehouse,
                    'created_by' => session('username')
                ];
                $inserted = $sale_vendor_products->insert($insertData);
            }
            else
            {
                $status = "Pending";
            }
            if($new_product_type == "Sale")
            {
                $new_select_batch = 0;
            }
            if($new_product_type == 'Rental')
            {
                DB::enableQueryLog();
                $product_details = DB::select("SELECT * FROM vendor_product_details WHERE id = $new_select_inventory");
                $product_details = json_decode(json_encode($product_details), true);

                $vendor_product_details_id = $product_details[0]['id'];
                $inventory_id = $product_details[0]['inventory_id'];
            }
            else
            {
                $new_select_inventory = 0;
                $inventory_id = 0;
            }
            $insert_order = [
                'order_id'=> $order_id,
                'customer_id'=>$customer_id,
                'product_id'=>$new_product_name,
                'vendor_product_id'=>$new_select_batch,
                'vendor_id'=>$new_select_vendor,
                'vendor_warehouse_id'=>$new_select_warehouse,
                'product_brand'=>$new_select_brand,
                'product_batch'=>$new_select_batch,
                'product_qty'=>1,
                'product_rent'=>$new_product_rent,
                'product_deposite'=>$new_product_deposite,
                'transport'=>$new_transport,
                'sale_rental' =>$new_product_type,
                'vendor_product_details_id' => $new_select_inventory,
                'unique_id'=> $inventory_id,
                'product_serial_nos' =>$inventory_id,
                'creation_date'=>$creation_date,
                'pickup_date'=>date('Y-m-d',strtotime("+1 month",strtotime($creation_date))),
                'status'=>"Accepted",
                'created_at'=>date('Y-m-d H:i:s'),
                'created_by'=>session('username'),  
            ];
            $update_inventory_status = DB::update("UPDATE vendor_product_details SET availability_status = 1, current_location = 0 WHERE id = $new_select_inventory");            
            $added_order_details_id = $order_details->insertGetId($insert_order);
            $loopdetails = ['R'=>$new_product_rent,'D'=>$new_product_deposite,'T'=>$new_transport];
            foreach($loopdetails as $key=>$field)
            {
                DB::table('cr_dr_note')->insert([
                    'order_id'=>$order_id,
                    'order_details_id'=>$added_order_details_id,
                    'crdrtype'=>'Cr',
                    'intype'=>$key,
                    'amount'=>$field,
                    'createdby'=>session('username')
                ]);
            }
            // $update_qty = DB::update("UPDATE vendor_products SET product_quantity = product_quantity-1 WHERE id=$new_select_batch");
            // $update = DB::update("UPDATE leads SET lead_status = 'Vendor Assigned' WHERE id = $lead_id");

            //get new product details for mailsent
                $delData = DB::table('del_orders')->where('order_id',$order_id)->first();
                $customer_name = $delData->shipping_first_name;
                $orderDate = $delData->DelDate;
                $this->matchLeadProducts($delData->lead_id);
                $getNewProductName = DB::table('products')->select('product_name')->where('id',$insert_order['product_id'])->first();
                $getNewProductName = $getNewProductName->product_name;
                $getNewProductVendor = DB::table('vendor_details')->where('id',$insert_order['vendor_id'])->first('registered_name');
                $getNewProductVendor = $getNewProductVendor->registered_name;
                $insert_order['product_name'] = $getNewProductName;
                $insert_order['vendor_name'] = $getNewProductVendor;
                $accountsEmail = config('app.accounts_email');
                // /$accountsEmail = 'viveks@quali55care.com';
                $orderType = 'Delivery';
                $modifiedType = 'Content';
                $change = 'Product-add';
                $changed = 'Added';
                $modifiedBy = session('username');
                $product_details = "";

                $product_details .= "Product Name: ".$insert_order['product_name'];
                $product_details .= " | Rent/Sale: ".$insert_order['product_rent'];
                $product_details .= " | Deposit: ".$insert_order['product_deposite'];
                $product_details .= " | Transport: ".$insert_order['transport'];
                $product_details .= " | Type: ".$insert_order['sale_rental'];
                $product_details .= " | Vendor: ".$insert_order['vendor_name'];

                // Mail::send('OrderModifyEmail/order-modifyEmail',compact('request','order_id','customer_name','orderDate','orderType','modifiedType','modifiedBy','change','insert_order'), function($message) use($accountsEmail,$order_id)
                // {  
                //     $message->to($accountsEmail, 'Accounts')->subject('Order Modified - '.$order_id);
                //     //$message->to($senderMail->email_id_user, 'Complaint Raised')->subject('Complaint Raised');
                //     $message->from('tempmailquali@gmail.com', 'Quali55Care');
                // });

                $accounts_nos = config('app.accounts_staff_contacts');
                // foreach($accounts_nos as $key=>$value)
                // {
                //     $url = "https://lj7724mli5.execute-api.ap-south-1.amazonaws.com/prod/sendwhatsappmsg";
                //     $curl = curl_init();
                //     curl_setopt($curl, CURLOPT_URL, $url);
                //     curl_setopt($curl, CURLOPT_POST, true);
                //     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    
                //     $headers = array(
                //         "Accept: application/json",
                //         "Content-Type: application/json",
                //     );
                //     curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                //     $data =[
                //         "portno"=>"11140",
                //         "namespace"=>"b9a23cb4_89ed_4fe2_b849_20775908ff5e",
                //         "countrycode"=> "91",
                //         "mobileno"=> $value,
                //         "templatename" => "change_order_product_added_removed",
                //         "templateparams" => [
                //             ["type"=> "text","text"=> $changed],
                //             ["type"=> "text","text"=> $order_id],
                //             ["type"=> "text","text"=> $customer_name],
                //             ["type"=> "text","text"=> $orderDate],
                //             ["type"=> "text","text"=> $modifiedBy],
                //             ["type"=> "text","text"=> $product_details]
                //         ],
                //     ];
                //     curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                    
                //     $resp = curl_exec($curl);
                //     curl_close($curl);
                // }

            return $insert_order;
            
            //return $order_id;
        }
        // Code ends Here.....
    }

    public function editOrderUpdate(Request $request,$order_id){
        if(DelOrders::where('order_id',$order_id)->where('deliverypickup','Delivery')->exists()){
            $getOldOrder = DelOrders::where('order_id',$order_id)->get();
            
            $updateLabourCharges = [
                'floor_wise_labour_charges'=>($request->get('floor_wise_labour_charges'))?$request->get('floor_wise_labour_charges'):0,
                'floor_no'=>($request->get('floor_no'))?$request->get('floor_no'):0,
                'labour_charges'=>($request->get('labour_charges'))?$request->get('labour_charges'):0
            ];
            
            $getOldOrder = DelOrders::select(array_keys($updateLabourCharges))->where('order_id',$order_id)->get(array_keys($updateLabourCharges));
            
            foreach ($updateLabourCharges as $key => $upData)
            {
                $insertData = [
                    'order_type'=>'DO',
                    'key_id'=>$order_id,
                    'operation'=>'labour charges',
                    'fields'=>$key,
                    'old_value'=>$getOldOrder[0]->$key,
                    'new_value'=>$upData,
                    'updated_by'=>session('username')
                ];
                ActivityLog::insert($insertData); //insert into activity log
            }
            DelOrders::where('order_id',$order_id)->update($updateLabourCharges);
            
            return redirect()->back()->with('message','Labour charges updated successfully');
        }else{
            return redirect()->back()->with('message_delete','order not exist or order type is not delivery');
        }
    }
    public function matchLeadProducts($lead_id){
        $orderTypeNotIn = config('app.order_type');
        $order_details = DB::table('del_orders')
                            ->join('order_details','order_details.order_id','=','del_orders.order_id')
                            ->select('order_details.*')
                            ->where('del_orders.lead_id',$lead_id)
                            ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                            ->whereNotIn('order_details.current_status',['Cancel'])
                            ->orderBy('order_details.product_id','ASC')
                            ->get()
                            ->groupBy('sale_rental');
        // dd($order_details);
        $equip_req_rent = array();
        $equip_qty_rent = array();
        $months_rent = array();
        $del_date_rent = array();
        $sale_rental_rent = array();
        $offered_rent_rent = array();
        $offered_rent_total_rent = array();
        $deposite_rent = array();
        $deposite_total_rent = array();
        $transport_rent = array();
    
        $equip_req_sale = array();
        $equip_qty_sale = array();
        $months_sale = array();
        $del_date_sale = array();
        $sale_rental_sale = array();
        $offered_rent_sale = array();
        $offered_rent_total_sale = array();
        $deposite_sale = array();
        $deposite_total_sale = array();
        $transport_sale = array();
    
        foreach($order_details as $key=>$value)
        {
            if($key == 'Rental'){
                foreach($value as $k=>$val){
                    $value[$k]->product_rent =  RenewalPickupController::fetchCrDrData($val->id,'R');
                    $value[$k]->product_deposite =  RenewalPickupController::fetchCrDrData($val->id,'D');
                    $value[$k]->transport =  RenewalPickupController::fetchCrDrData($val->id,'T');
                    if(in_array($val->product_id,$equip_req_rent)){
                        $index = array_search($val->product_id,$equip_req_rent);
                        // $equip_req_rent[$index] = $equip_req_rent[$index] + $val->product_id;
                        $equip_qty_rent[$index] = $equip_qty_rent[$index] + $val->product_qty;                       
                        $offered_rent_total_rent[$index] = $offered_rent_total_rent[$index] + $val->product_rent;
                        // $deposite_total_rent[$index] = $deposite_total_rent[$index] + $val->product_deposite;
                        $transport_rent[$index] = $transport_rent[$index] + $val->transport;
                    }
                    else
                    {
                        array_push($equip_req_rent,$val->product_id);
                        array_push($equip_qty_rent,$val->product_qty);
                        array_push($months_rent,$val->months);
                        array_push($del_date_rent,$val->creation_date);
                        array_push($sale_rental_rent,$val->sale_rental);
                        array_push($offered_rent_rent,$val->product_rent);
                        array_push($offered_rent_total_rent,$val->product_rent);
                        array_push($deposite_rent,$val->product_deposite);
                        array_push($deposite_total_rent,$val->product_deposite);
                        array_push($transport_rent,$val->transport);
                    }
                }
            }
            else if($key == 'Sale'){
                foreach($value as $k=>$val){
                    $value[$k]->product_rent =  RenewalPickupController::fetchCrDrData($val->id,'R');
                    $value[$k]->product_deposite =  RenewalPickupController::fetchCrDrData($val->id,'D');
                    $value[$k]->transport =  RenewalPickupController::fetchCrDrData($val->id,'T');
                    if(in_array($val->product_id,$equip_req_sale)){
                        $index = array_search($val->product_id,$equip_req_sale);
                        // $equip_req_sale[$index] = $equip_req_sale[$index] + $val->product_id;
                        $equip_qty_sale[$index] = $equip_qty_sale[$index] + $val->product_qty;                       
                        $offered_rent_total_sale[$index] = $offered_rent_total_sale[$index] + $val->product_rent;
                        // $deposite_total_sale[$index] = $deposite_total_sale[$index] + $val->product_deposite;
                        $transport_sale[$index] = $transport_sale[$index] + $val->transport;
                    }
                    else
                    {
                        array_push($equip_req_sale,$val->product_id);
                        array_push($equip_qty_sale,$val->product_qty);
                        array_push($months_sale,$val->months);
                        array_push($del_date_sale,$val->creation_date);
                        array_push($sale_rental_sale,$val->sale_rental);
                        array_push($offered_rent_sale,$val->product_rent);
                        array_push($offered_rent_total_sale,$val->product_rent);
                        array_push($deposite_sale,$val->product_deposite);
                        array_push($deposite_total_sale,$val->product_deposite);
                        array_push($transport_sale,$val->transport);
                    }
                }
            }
            else{
                dd("Something Went Wrong!");
            }
        }
        $equip_req_rent = array_merge($equip_req_rent,$equip_req_sale);
        $equip_qty_rent = array_merge($equip_qty_rent,$equip_qty_sale);
        $months_rent = array_merge($months_rent,$months_sale);
        $del_date_rent = array_merge($del_date_rent,$del_date_sale);
        $sale_rental_rent = array_merge($sale_rental_rent,$sale_rental_sale);
        $offered_rent_rent = array_merge($offered_rent_rent,$offered_rent_sale);
        $offered_rent_total_rent = array_merge($offered_rent_total_rent,$offered_rent_total_sale);
        $deposite_rent = array_merge($deposite_rent,$deposite_sale);
        $deposite_total_rent = array_merge($deposite_total_rent,$deposite_total_sale);
        $transport_rent = array_merge($transport_rent,$transport_sale);
        
        // dd($equip_req_rent,
        //     $equip_qty_rent,
        //     $months_rent,
        //     $del_date_rent,
        //     $sale_rental_rent,
        //     $offered_rent_rent,
        //     $offered_rent_total_rent,
        //     $deposite_rent,
        //     $deposite_total_rent,
        //     $transport_rent
        // );
        $update_data = [
            'equipment_requirement'=>json_encode($equip_req_rent),
            'equipment_qty'=>json_encode($equip_qty_rent),
            'months'=>json_encode($months_rent),
            'del_date'=>json_encode($del_date_rent),
            'sale_rental'=>json_encode($sale_rental_rent),
            'offered_rent'=>json_encode($offered_rent_rent),
            'offered_rent_total'=>json_encode($offered_rent_total_rent),
            'deposite'=>json_encode($deposite_rent),
            'deposite_total'=>json_encode($deposite_total_rent),
            'transport'=>json_encode($transport_rent),
            'lead_value'=>array_sum($offered_rent_total_rent) + array_sum($deposite_total_rent) + array_sum($transport_rent)
        ];
        DB::table('leads')->where('id',$lead_id)->update($update_data);
        // dd("Check");
        // dd(array_sum($offered_rent_total_rent) + array_sum($deposite_total_rent) + array_sum($transport_rent));
    }

    public function crdrdata(Request $request)
    {
        $ordertype = DB::table('del_orders')->where('order_id',$request->get('order_id'))->first()->deliverypickup;
        if($ordertype == 'Collection'){
            $crdrdata = DB::table('cr_dr_note')
            ->join('del_orders','del_orders.order_id','=','cr_dr_note.order_id')
            ->join('renewals','cr_dr_note.order_details_id','=','renewals.id')
            ->join('order_details','renewals.order_details_id','=','order_details.id')
            ->join('products','products.id','=','order_details.product_id')
            ->select(
                'products.product_name',
                'order_details.unique_id',
                'order_details.current_status',
                'order_details.remark',
                'del_orders.cr_dr_img',
                'del_orders.comment',
                'del_orders.order_id',
                'cr_dr_note.order_details_id',
                'cr_dr_note.crdrtype',
                'cr_dr_note.intype',
                'cr_dr_note.amount',
                'cr_dr_note.createdat',
                'cr_dr_note.createdby'
            )
            ->where('cr_dr_note.order_id',$request->get('order_id'))
            ->get()
            ->groupBy('order_details_id');    
        }else{
            $crdrdata = DB::table('cr_dr_note')
                            ->join('del_orders','del_orders.order_id','=','cr_dr_note.order_id')
                            ->join('order_details','cr_dr_note.order_details_id','=','order_details.id')
                            ->join('products','products.id','=','order_details.product_id')
                            ->select(
                                'products.product_name',
                                'order_details.unique_id',
                                'order_details.current_status',
                                'order_details.remark',
                                'del_orders.cr_dr_img',
                                'del_orders.comment',
                                'del_orders.order_id',
                                'cr_dr_note.order_details_id',
                                'cr_dr_note.crdrtype',
                                'cr_dr_note.intype',
                                'cr_dr_note.amount',
                                'cr_dr_note.createdat',
                                'cr_dr_note.createdby'
                            )
                            ->where('cr_dr_note.order_id',$request->get('order_id'))
                            ->get()
                            ->groupBy('order_details_id');
        }
        $crdrnotes = array();
        foreach($crdrdata as $id=>$data)
        {
            $crdrnotes[$id]['data'] = $data->groupBy('crdrtype');
            $crdrnotes[$id]['product_name'] = $data[0]->product_name;
            $crdrnotes[$id]['status'] = $data[0]->current_status;
            $crdrnotes[$id]['unique_id'] = $data[0]->unique_id;
            $crdrnotes[$id]['remark'] = $data[0]->remark;
            $crdrnotes[$id]['comment'] = $data[0]->comment;
            $crdrnotes[$id]['cr_dr_img'] = $data[0]->cr_dr_img;
            $crdrnotes[$id]['order_id'] = $data[0]->order_id;
            // return $crdrnotes[$id];

        }
        return $crdrnotes;
    }

    // Auto entry in vendor_inventory_mgmt (In/Out) Products.....
    public static function updateVendorInOutInventory($details_id,$state){
        if($state == "in"){
            $details = DB::table('order_details')
            ->join('vendor_warehouse','vendor_warehouse.id','=','order_details.vendor_warehouse_id')
            ->join('del_orders','del_orders.order_id','=','order_details.order_id')
            ->select('order_details.id','order_details.order_id','order_details.product_id','order_details.vendor_id','del_orders.DelDate as date','vendor_warehouse.wh_name','vendor_warehouse.wh_area','vendor_warehouse.wh_city','del_orders.fulldetails','del_orders.DelAssignedTo','order_details.unique_id as inventory_id')
            ->where('order_details.id',$details_id)->first();
        }elseif($state == "out"){
            $details = DB::table('pickups')
            ->join('order_details','order_details.id','=','pickups.order_details_id')
            ->join('vendor_warehouse','vendor_warehouse.id','=','pickups.drop_warehouse_id')
            ->join('del_orders','del_orders.order_id','=','pickups.pickup_order_id')
            ->select('pickups.id','pickups.pickup_order_id','pickups.product_id','pickups.drop_vendor_id as vendor_id','del_orders.DelDate as date','vendor_warehouse.wh_name','vendor_warehouse.wh_area','vendor_warehouse.wh_city','del_orders.fulldetails','del_orders.DelAssignedTo','order_details.unique_id as inventory_id')
            ->where('pickups.id',$details_id)->first();
        }
        $details->warehouse = $details->wh_name.", ".$details->wh_area.", ".$details->wh_city;
        DB::table('vendor_inventory_mgmt')->updateOrInsert(
            [
                'details_id'=>$details_id,
                'state'=>$state,
                'flag'=>'Active'
            ],
            [
                'date'=>date('Y-m-d',strtotime($details->date)),
                'vendor'=>$details->vendor_id,
                'equipment'=>$details->product_id,
                'inventory_id'=>$details->inventory_id,
                'quantity'=>1,
                'inventory_pickup_date'=>date('Y-m-d',strtotime($details->date)),
                'pickup_address'=>($state == 'in')?$details->warehouse:$details->fulldetails,
                'drop_address'=>($state == 'out')?$details->warehouse:$details->fulldetails,
                'assigned_to'=>$details->DelAssignedTo
            ]
        );
    }

}


?>