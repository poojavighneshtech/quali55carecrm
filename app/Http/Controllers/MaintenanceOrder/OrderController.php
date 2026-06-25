<?php

namespace App\Http\Controllers\MaintenanceOrder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\AddressTable;

use App\Http\Controllers\RenewalPickup\RenewalPickupController;

use Carbon\Carbon;

class OrderController extends Controller
{
    public function createOrder(Request $request)
    {
        if($request->method()=='GET')
        {
            $deliveryStaff = DB::table('delusers')->where('role','user')->get();
            $states = DB::table('states')->get();
            $countries = DB::table('countries')->get();
            $cities = DB::table('cities')->get();
            $maintenanceOrdersArr = ['Repair','Install','Shifting','Replace'];
            $delMaintenanceOrders = DB::table('del_orders')
                                ->select('del_orders.*')
                                ->distinct('del_orders.order_id')
                                ->whereIn('del_orders.deliverypickup',$maintenanceOrdersArr)
                                ->when($request->get('search_customer'),function($query)use($request){
                                    $query->where(function($q)use($request) {
                                        $q->where('del_orders.shipping_first_name','LIKE','%'.$request->get('search_customer').'%');
                                        $q->orWhere('del_orders.mobileno',$request->get('search_customer'));
                                        $q->orWhere('del_orders.patient_name',$request->get('search_customer'));
                                    });
                                })
                                ->when($request->get('search_order_id'),function($query)use($request){
                                    $query->where('del_orders.order_id',$request->get('search_order_id'));
                                })
                                ->when($request->get('search_type') && $request->get('search_type')!='All',function($query)use($request){
                                    $query->where('del_orders.deliverypickup',$request->get('search_type'));
                                })
                                ->when($request->get('search_from_date') && $request->get('search_to_date'),function($query)use($request){
                                    $fromDate = date('d-m-Y',strtotime($request->get('search_from_date')));
                                    $toDate = date('d-m-Y',strtotime($request->get('search_to_date')));
                                    $query->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$fromDate','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$toDate','%d-%m-%Y'))")]);
                                })
                                ->orderBy(DB::raw('STR_TO_DATE(del_orders.DelDate,"%d-%m-%Y")'),'DESC')
                                ->paginate(10);
            $orderClosedReason = config('app.order_closed_reason');
            return view('MaintenanceOrder.create-order',compact('deliveryStaff','states','countries','cities','delMaintenanceOrders','orderClosedReason'));
        }

        if($request->method()=='POST')
        {
            dd($request->all());
        }

    }
    public function searchCustomer(Request $request){
        $searchCustomer = $request->get('search_customer');
        // $getCustomers = DB::table('customer_details')
        //                     ->when($request->get('search_customer')!=null,function($query)use($request){
        //                         $query->where(function($where) use($request){
        //                             $where->orWhere('customer_details.customer_name','LIKE','%'.$request->get('search_customer').'%')
        //                                 ->orWhere('customer_details.address_line_1','LIKE','%'.$request->get('search_customer').'%')
        //                                 ->orWhere('customer_details.address_line_2','LIKE','%'.$request->get('search_customer').'%')
        //                                 ->orWhere('customer_details.area','LIKE','%'.$request->get('search_customer').'%')
        //                                 ->orWhere('customer_details.location','LIKE','%'.$request->get('search_customer').'%')
        //                                 ->orWhere('customer_details.primary_contact_no','LIKE','%'.$request->get('search_customer').'%')
        //                                 ->orWhere('customer_details.secondary_contact_no','LIKE','%'.$request->get('search_customer').'%');
        //                         });
        //                     })->get();
        $getCustomers = DB::table('del_orders')->join('leads','leads.id','=','del_orders.lead_id')
            ->select('del_orders.order_id','del_orders.shipping_first_name','del_orders.patient_name','del_orders.fulldetails','del_orders.mobileno','del_orders.DelDate','del_orders.lead_id','leads.customer_id')
            ->whereNotIn('del_orders.status',['Cancel'])
            ->where('del_orders.deliverypickup','Delivery')
            ->when($request->get('search_customer')!=null,function($query)use($request){
                $query->where(function($where) use($request){
                    $where->orWhere('del_orders.shipping_first_name','LIKE','%'.$request->get('search_customer').'%')
                    ->orWhere('del_orders.mobileno','LIKE','%'.$request->get('search_customer').'%')
                    ->orWhere('del_orders.patient_name','LIKE','%'.$request->get('search_customer').'%');
                });
            })->get()->groupBy('lead_id');
                            
        return $getCustomers;
    }

    public function customerProducts(Request $request){
        $lead_id = $request->get('lead_id');
        // $customerInfo = DB::table('customer_details')->where('cust_id',$lead_id)->first();
        // $productsData = DB::table('order_details')
        //                 ->select('order_details.*','products.product_name','vendor_details.registered_name as vendor_name')
        //                 ->join('vendor_details','order_details.vendor_id','vendor_details.id')
        //                 ->join('products','order_details.product_id','=','products.id')
        //                 ->where('customer_id',$lead_id)
        //                 ->where('sale_rental','Rental')
        //                 ->whereNotIn('current_status',['Cancel','Picked Up','Pending Pickup'])
        //                 ->get();
        $productsData = DB::table('del_orders')->join('leads','del_orders.lead_id','=','leads.id')
                            ->join('order_details','order_details.order_id','=','del_orders.order_id')
                            ->join('vendor_details','order_details.vendor_id','vendor_details.id')
                            ->join('products','order_details.product_id','=','products.id')
                            ->join('customer_details','leads.customer_id','=','customer_details.cust_id')
                            ->select('del_orders.*','order_details.*','vendor_details.*','products.*','order_details.id as ord_dt_id','customer_details.*')
                            ->where('del_orders.lead_id',$lead_id)
                            // ->where('order_details.sale_rental','Rental')
                            ->where('del_orders.deliverypickup','Delivery')
                            ->whereNotIn('order_details.current_status',['Cancel','Picked Up','Pending Pickup'])
                            ->get();
        // $customerInfo = DB::table('customer_details')->where('cust_id',$customerId)->first();
        return $productsData;
    }

    public function generateOrder(Request $request){ 
        // dd($request->all());
        $orderType = $request->get('order_type');
        $customerId = $request->get('customer_id');
        $orderCost = $request->get('order_cost');
        $orderDate = $request->get('order_date');
        $assignDelboy = $request->get('assign_order');
        $submit = $request->get('submit_order');
        $assignHelper = json_encode($request->get('assign_helper'));
        $products = json_decode($request->get('products'));//order_details_id
        $orderAddress = $request->get('order_address');

        //for update order
        $hidDelOrderId = $request->get('del_order_id');
        $hidMaintenanceIds = json_decode($request->get('maintenance_ids'));
        $hidAddressId = $request->get('address_id');
        $hidDropAddressId = $request->get('drop_address_id');
        $getDelDetails = DB::table('del_orders')->where('order_id',$hidDelOrderId)->get();
        try {
            DB::beginTransaction();
            //get customer details
            $getCustomer = DB::table('customer_details')->where('cust_id',$customerId)->first();
            //product details
            $productDetails = DB::table('order_details')
                                    ->join('del_orders','del_orders.order_id','=','order_details.order_id')
                                    ->join('products','order_details.product_id','=','products.id')
                                    ->whereIn('order_details.id',$products)
                                    ->select('order_details.*','del_orders.lead_id','del_orders.patient_name','products.product_name')
                                    ->get();

            if($submit=='update_order'){
                $productDetails = DB::table('maintenance_orders')
                                    ->join('products','maintenance_orders.product_id','=','products.id')
                                    ->join('order_details','maintenance_orders.order_details_id','=','order_details.id')
                                    ->whereIn('maintenance_orders.id',$hidMaintenanceIds)
                                    //->whereIn('order_details.id',$products)
                                    ->select('order_details.*','products.product_name','maintenance_orders.id as mmaintenance_id')
                                    ->get();
                //dd($productDetails);
            }
            $productNames = $productDetails->pluck('product_name')->implode(',');

            $mobileNo = null;
            
            if($orderType!='Shifting'){
                $mobileNo = ($orderAddress['address']['address_contact']!=null)?$orderAddress['address']['address_contact']:$getCustomer->primary_contact_no;
            }else{
                $mobileNo = ($orderAddress['drop']['address_contact']!=null)?$orderAddress['drop']['address_contact']:$getCustomer->primary_contact_no;
            }

            //create first order from del_orders
            //dd($getCustomer);
            $customerFullDeails = $getCustomer->customer_name.", ".$orderAddress['address']['address_line_1'].", ".$orderAddress['address']['address_line_2'].", ".$orderAddress['address']['address_location'].", ".$orderAddress['address']['address_area'].", ".$orderAddress['address']['address_landmark'].", ".$orderAddress['address']['address_city'].", ".$orderAddress['address']['address_pincode'];
            $insertOrderData =[
                'lead_id'=>$getDelDetails[0]->lead_id,
                'patient_name'=>$getDelDetails[0]->patient_name,
                'status'=>($assignDelboy!=null)?'Assigned':'Pending',
                'deliverypickup'=>$orderType,
                'shipping_first_name'=>$getCustomer->customer_name,
                'cust_gender'=>$getCustomer->cust_gender,
                'mobileno'=>$mobileNo,
                'DelDate'=>date('d-m-Y',strtotime($orderDate)),
                'line_item_1'=>$productNames,
                'DelAssignedTo'=>($assignDelboy!=null)?$assignDelboy:'Pending',
                'helpers'=>($assignHelper)?$assignHelper:'[No helper]',
                'TotalAmt'=>$orderCost,
                'PaymentMode'=>'Cash',
                'TravelMode'=>'Pending',
                'fulldetails'=>$customerFullDeails,
                'location'=>$orderAddress['address']['address_location'],
                'order_approval_status'=>'Approved',
            ];
            if($submit=='update_order'){
                DB::table('del_orders')->where('order_id',$hidDelOrderId)->update($insertOrderData);
            }else{
                $getOrderId = DB::table('del_orders')->insertGetId($insertOrderData);
            }

            //before update check product deleted or not if yes change flag of data is deleted
            if($submit=='update_order'){
                $getOldData = DB::table('maintenance_orders')->where('order_id',$hidDelOrderId)->where('customer_id',$customerId)->get();
                $maintenanceIds = $getOldData->whereNotIn('id',$hidMaintenanceIds);
                if($maintenanceIds->isNotEmpty()){
                    $ids = $maintenanceIds->pluck('id');
                    $updateDelete = DB::table('maintenance_orders')->whereIn('id',$ids)->update(['flag'=>'Deleted']);
                }
                $getOrderId = $hidDelOrderId;
            }

            if($orderType=='Repair' || $orderType=='Install' || $orderType=='Shifting' || $orderType=='Replace'){
                foreach ($productDetails as $key => $product) {
                    $insertRepairData = [
                        'order_id'=>$getOrderId,
                        'order_details_id'=>$product->id,
                        'customer_id'=>$customerId,
                        'del_order_id'=>$product->order_id,
                        'product_id'=>$product->product_id,
                        'order_amount'=>$orderCost,
                    ];
                    if($submit=='update_order'){
                        $updateMaintenanceOrder = DB::table('maintenance_orders')->where('id',$product->mmaintenance_id)->update($insertRepairData);
                    }
                    else{
                        $inertMaintenanceOrder = DB::table('maintenance_orders')->insert($insertRepairData);
                    }
                }
            }
           
            $addressType = ['address','drop'];
            
            if($orderType!='Shifting'){
                $addressType = ['address'];
            }

            foreach ($addressType as $key => $type) {
                $adType = $type;
                if($orderType=='Shifting' && $type=='address'){
                    $adType = 'pickup';
                }
                
                $insertAddress = [
                    'order_id' => $getOrderId,
                    'type'=>$adType,
                    'address_line_1'=>$orderAddress[$type]['address_line_1'],
                    'address_line_2'=>$orderAddress[$type]['address_line_2'],
                    'landmark'=>$orderAddress[$type]['address_landmark'],
                    'area'=>$orderAddress[$type]['address_area'],
                    'city'=>$orderAddress[$type]['address_city'],
                    'pincode'=>$orderAddress[$type]['address_pincode'],
                    'state'=>$orderAddress[$type]['address_state'],
                    'country'=>$orderAddress[$type]['address_country'],
                    'email'=>$orderAddress[$type]['address_email'],
                    'contact_no'=>$orderAddress[$type]['address_contact'],
                    'created_by'=>session('username')
                ];
                $addCreateOrdUpdate = AddressTable::updateOrCreate(['order_id'=>$getOrderId,'type'=>$adType],$insertAddress);
                // if($submit=='update_order'){
                //     if($type=='address'){
                //         $updateAddress = DB::table('address_table')->where('id',$hidAddressId)->update($insertAddress);
                //     }elseif($type=='pickup'){
                //         $updateAddress = DB::table('address_table')->where('id',$hidAddressId)->update($insertAddress);
                //     }elseif($type=='drop'){
                //         $updateAddress = DB::table('address_table')->where('id',$hidDropAddressId)->update($insertAddress);
                //     }
                // }else{
                //     $insertAddress = DB::table('address_table')->insert($insertAddress);
                // }
            }

            DB::commit();
            return redirect()->back();

        } catch (\Throwable $th) {
            DB::rollback();
            return $th;
        }
    }   

    public function getOrderData($orderId){
        $orderData = DB::table('del_orders')
                        ->select('maintenance_orders.*','del_orders.*','products.product_name','order_details.unique_id','order_details.id as order_details_id','vendor_details.registered_name as vendor_name')
                        ->join('maintenance_orders','del_orders.order_id','=','maintenance_orders.order_id')
                        ->join('order_details','maintenance_orders.order_details_id','=','order_details.id')
                        ->join('vendor_details','order_details.vendor_id','=','vendor_details.id')
                        ->join('products','maintenance_orders.product_id','=','products.id')
                        ->where('del_orders.order_id',$orderId)
                        ->whereNotIn('maintenance_orders.flag',['Deleted','Cancel'])->get();
        $customerDetails = DB::table('maintenance_orders')
                        ->select('customer_details.*')
                        ->distinct('maintenance_orders.customer_id')
                        ->join('customer_details','maintenance_orders.customer_id','=','customer_details.cust_id')
                        ->where('maintenance_orders.order_id',$orderId)
                        ->first();
        $address = DB::table('address_table')->where('order_id',$orderId)->get()->groupBy('type');
        $customerDetails->order_address = $address;
        return compact('customerDetails','orderData');
        //return compact('orderData');
        //customer details

        
    }

    public function cancelOrder(Request $request){
        try {
            //cancel order del_orders
            $order_id = $request->get('cancel_order_id');
            $reason = $request->get('cancel_reason');
            $comment = $request->get('cancel_comment');
            DB::table('del_orders')->where('order_id',$order_id)->update(['status'=>'Cancel','cancellation_reason'=>$reason,'comment'=>$comment]);
            //maintencence table cancel
            DB::table('maintenance_orders')->where('order_id',$order_id)->whereNotIn('flag',['Deleted'])->update(['flag'=>'Cancel']);
            return redirect()->back()->with('message','order closed successfully');
        } catch (\Throwable $th) {
            return $th;
            //throw $th;
        }
    }

    public function updateOrderData(Request $request,$orderId){
        return $this->getOrderData($orderId);
    }
   

    public function replaceOrder(Request $request,$orderId){
        //validate data check order is delivery or not
        $order = DB::table('del_orders')->where('order_id',$orderId)->where('deliverypickup','Delivery');
        $isDelivery = $order->exists();
        if(!$isDelivery){
            return redirect()->back()->with('message_delete','Please select delivery order');
        }
        $orderData = $order->first();
        //patient name 
        $patientName = $order->join('leads','del_orders.lead_id','=','leads.id');
        if($patientName->exists()){
            $orderData->patient_name = $patientName->select('leads.patient_name')->first('patient_name')->patient_name;
        }else{
            $orderData->patient_name = null;
        }
        $customerDetails = DB::table('order_details')
                            ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                            ->distinct('order_details.customer_id')
                            ->where('order_details.order_id',$orderId)->first('customer_details.*');

        $orderDetails = DB::table('order_details')
                                ->join('products','order_details.product_id','=','products.id')
                                ->join('vendor_details','order_details.vendor_id','=','vendor_details.id')
                                ->join('vendor_warehouse','order_details.vendor_warehouse_id','=','vendor_warehouse.id')
                                ->where('order_details.order_id',$orderId)
                                ->whereNotIn('order_details.current_status',['Picked Up','CustStop','Cancel'])
                                ->get(['order_details.*','order_details.id as order_details_id','products.product_name','vendor_details.registered_name as vendor_name','vendor_warehouse.*']);
        $orderData->customer_details = $customerDetails;
        $orderData->order_details = $orderDetails;
        
        //other required data
        $productList = DB::table('products')->where('flag','Active')->get();

        $selectedData = null;
        if($request->has('selected_product')){
            $selectedProduct = $request->get('selected_product');
            $selectedData = $orderDetails->whereIn('order_details_id',$selectedProduct);
            //dd($vendorDetails = $this->getVendor($selectedProduct));
        }

        return view('MaintenanceOrder.order-replace',compact('orderData','selectedData','productList'));
    }

    public function fetchDetails(Request $request){
        if($request->get('request_type')=='fetch_vendor'){
                $getVendor = DB::table('vendor_products')
                                ->join('vendor_details','vendor_products.vendor_id','=','vendor_details.id')
                                ->where('vendor_products.product_quantity','>=',1)
                                ->when($request->get('product_type')=="Rental",function($query)use($request){
                                    $query->where('vendor_products.product_id',$request->get('product_id'));
                                })  
                                ->whereNotIn('vendor_products.status',['Pending','Rejected'])
                                ->get(['vendor_details.*']);
            
            return $getVendor->unique('id');
        }
        elseif($request->get('request_type')=='fetch_warehouse'){
            if($request->get('product_type')=="Rental"){
                $getWarehouse = DB::table('vendor_warehouse')
                                    ->join('vendor_product_details','vendor_warehouse.id','=','vendor_product_details.warehouse_id')
                                    ->join('vendor_products','vendor_products.id','=','vendor_product_details.vendor_products_id')
                                    ->where('vendor_products.product_id',$request->get('product_id'))
                                    ->where('vendor_products.vendor_id',$request->get('vendor_id'))
                                    ->whereIn('vendor_product_details.current_location',[1,2])
                                    ->get(['vendor_warehouse.*']);
            }else{
                $getWarehouse = DB::table('vendor_warehouse')
                                    ->join('vendor_products','vendor_products.warehouse_id','=','vendor_warehouse.id')
                                    ->where('vendor_products.product_id',$request->get('product_id'))
                                    ->where('vendor_products.vendor_id',$request->get('vendor_id'))
                                    ->get(['vendor_warehouse.*']);
            }
            return $getWarehouse->unique('id');
        }
        elseif($request->get('request_type')=='fetch_brand'){
            $getBrand = DB::table('product_brands')
                            ->join('vendor_products','product_brands.id','=','vendor_products.product_brand')
                            ->join('vendor_product_details','vendor_product_details.vendor_products_id','=','vendor_products.id')
                            ->where('vendor_products.product_id',$request->get('product_id'))
                            ->where('vendor_products.vendor_id',$request->get('vendor_id'))
                            ->where('vendor_product_details.warehouse_id',$request->get('warehouse_id'))
                            ->get(['product_brands.*']);

            return $getBrand->unique('id');
        }
        elseif($request->get('request_type')=='fetch_batch'){
            $getBatch = DB::table('vendor_products')
                            ->join('vendor_product_details','vendor_product_details.vendor_products_id','=','vendor_products.id')
                            ->where('vendor_products.product_id',$request->get('product_id'))
                            ->where('vendor_products.vendor_id',$request->get('vendor_id'))
                            ->where('vendor_product_details.warehouse_id',$request->get('warehouse_id'))
                            ->where('vendor_products.product_brand',$request->get('brand_id'))
                            ->get(['vendor_products.*']);
            return $getBatch->unique('id');
        }
        elseif($request->get('request_type')=='fetch_inventory_id'){
            //return 1;
            $getInventoryId= DB::table('vendor_product_details')
                            ->where('vendor_products_id',$request->get('batch_id'))
                            ->where('warehouse_id',$request->get('warehouse_id'))
                            ->get(['vendor_product_details.*']);
            return $getInventoryId;
        }

    }

    public function generateReplaceOrder(Request $request){
        $request->validate([
            'replaced_data'=>'required',
        ]);
        try {
            $customerId = $request->get('customer_id');
            $replacedData = Collect($request->get('replaced_data'));

            //create pickup Order
            $replacedDataKeys = $replacedData->keys();
            $pickupData = array();
            foreach ($replacedDataKeys as $key => $id) {
                $pickupData[$key]['id'] = $id;
                $pickupData[$key]['pickup_date'] = Carbon::today()->toDateString();
            }
            $RenewalPickupController = new RenewalPickupController();
            $pickupOrderId = $RenewalPickupController->OrderPickup($pickupData);

            //Delivery Order
            $newProductData = Collect();
            $oldProductData = Collect();
            foreach ($replacedData as $key => $orderData) {
                $newData = Collect($replacedData[$key]['new'])->put('order_details_id', $key);
                $newProductData->push($newData);

                $oldData = Collect($replacedData[$key]['old']);
                $oldProductData->push($oldData);
            }
            $this->orderDelivery($newProductData,$customerId,'replace');
            return "success";
        } catch (Exception $th) {
            dd($th);
        }

    }


    public static function orderDelivery($data,$customerId,$argType=null){
        DB::beginTransaction();
        try {
            $customerDetails = DB::table('customer_details')->where('cust_id',$customerId)->first();
            $customerFullDeails = $customerDetails->customer_name.', '.$customerDetails->address_line_1.', '.$customerDetails->address_line_2.', '.$customerDetails->location.', '.$customerDetails->area.', '.$customerDetails->landmark.', '.$customerDetails->city.' - '.$customerDetails->pincode.', '.$customerDetails->state.', '.$customerDetails->country;
            $vendorWiseData = $data->groupBy('vendor');
            
            $deliveryOrderIdArray = array();
            foreach ($vendorWiseData as $vendorId => $product) {
                $leadId = DB::table('order_details')
                                ->join('del_orders','order_details.order_id','=','del_orders.order_id')
                                ->where('order_details.id',$product[0]['order_details_id'])
                                ->first('del_orders.lead_id')->lead_id;

                $productIds = $product->pluck('product_id');
                $productNames = DB::table('products')->whereIn('id',$productIds)->get('product_name')->pluck('product_name')->implode(',');
                            
                //total amount
                $rentSum = $product->pluck('rent')->sum();
                $depositSum = $product->pluck('deposit')->sum();
                $transportSum = $product->pluck('transport')->sum();
                $totalAmount = $rentSum+$depositSum+$transportSum;
                
                $today = Carbon::today()->toDateString();
                $DelDate = date('d-m-Y',strtotime($today));
                
                $insertDelivery = [
                    'status'=>'Pending',
                    'lead_id'=>$leadId,
                    'vendor_id'=>$vendorId,
                    'deliverypickup'=>'Delivery',
                    'DelDate'=>$DelDate,
                    'location'=>$customerDetails->location,
                    'shipping_first_name'=>$customerDetails->customer_name,
                    'cust_gender'=>$customerDetails->cust_gender,
                    'TotalAmt'=>$totalAmount,
                    'PaymentMode'=>'Cash',
                    'mobileno'=>$customerDetails->primary_contact_no,
                    'DelAssignedTo'=>'Pending',
                    'TravelMode'=>'Null',
                    'order_approval_status'=>'Pending',
                    'fulldetails'=>$customerFullDeails,
                    'line_item_1'=>$productNames,
                ];
                $deliveryOrderId = DB::table('del_orders')->insertGetId($insertDelivery);
                array_push($deliveryOrderIdArray,$deliveryOrderId);

                $leadsLoginsert = [
                    'log_lead_id' => $leadId,
                    'log_order_id' => $deliveryOrderId,
                    'log_order_lead_date' => date('Y-m-d').' '.date('H:i:s'),
                    'log_order_type' => 'DO',
                    'log_lead_status' => 'Order Generated',
                    'log_date' => date('Y-m-d'),
                    'log_time' => date('H:i:s'),
                    'updated_by' => session('username'),
                    'PARAM' => $vendorId
                ];
                DB::table('leads_log')->insert($leadsLoginsert);

                //insert into order details table
                foreach ($product as $key => $pr) {
                    //dd($pr);
                    $inventoryId = DB::table('vendor_product_details')->where('id',$pr['inventory_id'])->first()->inventory_id;
                    $insertOrderDetailsData = [
                        'order_id'=> $deliveryOrderId,
                        'customer_id'=>$customerId,
                        'product_id'=>$pr['product_id'],
                        'vendor_product_id'=>$pr['batch'],
                        'vendor_id'=>$vendorId,
                        'vendor_warehouse_id'=>$pr['warehouse'],
                        'vendor_product_details_id' => $pr['inventory_id'],
                        'product_brand'=>$pr['brand'],
                        'product_batch'=>$pr['batch'],
                        'product_qty'=>1,
                        'months'=>1,
                        'product_rent'=>$pr['rent'],
                        'product_deposite'=>$pr['deposit'],
                        'transport'=>$pr['transport'],
                        'sale_rental' =>'Rental',
                        'unique_id'=>$inventoryId,
                        'product_serial_nos' =>null,
                        'vendor_rent'=>null,
                        'creation_date'=>$today,
                        'pickup_date'=>$today,
                        'status'=>'Pending',
                        'upgraded'=>null,
                        'created_at'=>Carbon::now()->toDateTimeString(),
                        'created_by'=>session('username'),  
                    ];
                    
                    $getOrderDetailsId = DB::table('order_details')->insertGetId($insertOrderDetailsData);
                    
                    //insert into sale_vendor_products table
                    $insertSaleVendorProduct = [
                        'order_id'=>$deliveryOrderId,
                        'vendor_id' =>$vendorId,
                        'product_id' =>$pr['product_id'],
                        'sale_price' =>0,
                        'vendor_sale_price' => 0,
                        'vendor_warehouse_id' =>$pr['warehouse'],
                        'created_by' => session('username')
                    ];
                    $insertSaleVP = DB::table('sale_vendor_products')->insert($insertSaleVendorProduct);
                    
                    //update status
                    $updateVendorProductDetailsData = [
                        'availability_status'=>1,
                        'current_location'=>0,
                    ];
                    $updateVendorProductDetails = DB::table('vendor_product_details')->where('id',$pr['inventory_id'])->update($updateVendorProductDetailsData);

                    //if replacement order
                    if($argType!=null && $argType=='replace'){
                        $lastOrderDetailsData = DB::table('order_details')->where('id',$pr['order_details_id'])->first();
                        $insertMaintenanceTable = [
                            'order_id'=>$deliveryOrderId,
                            'order_details_id'=>$getOrderDetailsId,
                            'customer_id'=>$customerId,
                            'del_order_id'=>$lastOrderDetailsData->order_id,
                            'product_id'=>$pr['product_id'],
                            'old_product_id'=>$lastOrderDetailsData->product_id,
                            'new_product_id'=>$pr['product_id'],
                            'prev_order_details_id'=>$pr['order_details_id'],
                            'current_order_details_id'=>$getOrderDetailsId,
                            'order_amount'=>$totalAmount
                        ];
                        $maintenanceInsert = DB::table('maintenance_orders')->insert($insertMaintenanceTable);
                    }

                }
                
                //update lead log
                $leadStatus = ['Vendor Assigned'=>null,'Order Generated'=>$deliveryOrderId];
                foreach ($leadStatus as $key => $value) {
                    $updateLeadLog = [
                        'log_lead_id' =>$leadId,
                        'log_lead_status' => $key,
                        'log_order_lead_date' => date('Y-m-d').' '.date('H:i:s'),
                        'log_order_id' =>$value,
                        'log_order_type' =>'DO',
                        'log_date' => date('Y-m-d'),
                        'log_time' => date('H:i:s'),
                        'updated_by' => session('username')
                    ];
                    DB::table('leads_log')->insert($updateLeadLog);
                    // DB::table('leads')->where('id',$leadId)->update(['lead_status'=>$key]);
                }
            }
            DB::commit();
            return $deliveryOrderIdArray;
        } catch (Exception $th) {
            DB::rollBack();
            dd($th);
        }
    }
}
