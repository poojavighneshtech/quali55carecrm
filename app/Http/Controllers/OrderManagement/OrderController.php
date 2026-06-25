<?php

    namespace App\Http\Controllers\OrderManagement;

    use Illuminate\Support\Facades\Validator;
    use Illuminate\Support\Facades\DB;
    //use Illuminate\Support\Collection;
    use App\Support\Collection;
    use App\Models\VendorRegister;
    use App\Models\UserRegister;
    use App\Models\DelOrders;
    use App\Models\lead;
    use App\Models\OrderDetails;
    use App\Models\VendorProducts;
    use App\Models\leads_log;
    use App\Models\ActivityLog;
    use App\Models\VendorProductDetails;
    use App\Models\sale_vendor_products;
    use App\Models\VendorRentedProducts;
    use App\Models\Renewal;
    use App\Models\Pickup;
    use App\Models\WebExceptionLog;
    use App\Models\VirtualVdrInventoryMgmt;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    use App\Http\Controllers\DeliveryManagement\DeliveryController;
    use Mail;
    use App\Exports\OrdersExport;
    use Maatwebsite\Excel\Facades\Excel;
    use Carbon\Carbon;
    use Exception;
    use Illuminate\Support\Arr;
    use App\Exports\GenerateInvoice;
    use App\Exports\ConvertedOrdersReport;
    use App\Exports\OrderDeliveryAllReport;
    use App\Http\Controllers\RenewalPickup\RenewalPickupController;
    use App\Http\Controllers\OrderManagement\EditOrderController;

    class OrderController extends Controller
    {
        public function isLoggedIn()
        {
            $data = session('isLoggedIn');
            return $data;
        }
        public function vendorAssignment(Request $request){
            $details = DB::table('leads')->join('customer_details','customer_details.cust_id','=','leads.customer_id')->where('leads.id',$request->get('lead_id'))->first();
            // dd($details);
            $equipments = json_decode($details->equipment_requirement);
            $equipment_names = array();
            foreach($equipments as $key=>$equip){
                array_push($equipment_names, DB::table('products')->where('id',$equip)->first()->product_name);
            }
            $details->equipment_names = json_encode($equipment_names);
            // dd($details);
            return view('OrderManagement.vendor-assignment',compact('details'));
        }
        public function fetchInventoryDetails(Request $request){
            if($request->get('type') == 'vdr'){
                $vendor = DB::table('vendor_details')->select('vendor_details.id','vendor_details.registered_name')->orderBy('registered_name','ASC')->get();
                return $vendor;
            }else if($request->get('type') == 'war'){
                $warehouse['vendor_warehouse'] = DB::table('vendor_warehouse')->select('vendor_warehouse.id','vendor_warehouse.wh_name','vendor_warehouse.wh_area','vendor_warehouse.wh_city')->where('vendor_id',$request->get('fieldid'))->where('flag','active')->orderBy('wh_name','ASC')->get();
                $warehouse['virtual_warehouse'] = DB::table('vendor_warehouse')->select('vendor_warehouse.id','vendor_warehouse.wh_name','vendor_warehouse.wh_area','vendor_warehouse.wh_city')->where('vendor_id',17)->whereIn('id',[19,175])->where('flag','active')->orderBy('wh_name','ASC')->get();
                return $warehouse;
            }else if($request->get('type') == 'bra'){
                $brand = DB::table('product_brands')->select('product_brands.id','product_brands.brand_name')->where('product_id',$request->get('fieldid'))->orderBy('brand_name','ASC')->get();
                return $brand;
            }else{
                return false;
            }
        }
        public function orderGenerate(Request $request){
            // dd($request->all());
            DB::beginTransaction();
            try{
                $details = $request->get('details');
                $leadDetails = DB::table('leads')->join('customer_details','leads.customer_id','=','customer_details.cust_id')->where('id',$request->get('leadid'))->first();
                $arrInsertedOrderId = array();
                $addVendorInventory = array();
                foreach(json_decode($leadDetails->equipment_requirement) as $key=>$value){
                    $productRecord = $details[$key];
                    for($i = 0; $i < json_decode($leadDetails->equipment_qty)[$key]; $i++){
                        if($productRecord['selectionType'] == 'All'){
                            $productRecord['inventory'][0]['product'] = $value;
                            $funResponse = $this->getInventory($productRecord['inventory'][0]);                            
                            $prodRecord = $productRecord['inventory'][0];
                        }else{
                            $productRecord['inventory'][$i]['product'] = $value;
                            $funResponse = $this->getInventory($productRecord['inventory'][$i]);
                            $prodRecord = $productRecord['inventory'][$i];
                        }
                        // dd($funResponse);
                        $month = json_decode($leadDetails->months)[$key];
                        $billingPeriod = json_decode($leadDetails->billing_period)[$key];
                        $billingUnit = json_decode($leadDetails->billing_unit)[$key];
                        $pickupDate = date('Y-m-d');
                        
                        if($billingUnit == 'Week'){
                            $pickupDate = date('Y-m-d',strtotime("+$billingPeriod $billingUnit",strtotime($leadDetails->converted_at)));
                        }else if($billingUnit == 'Half Month'){
                            $billingPeriod = $billingPeriod * 2;
                            $pickupDate = date('Y-m-d',strtotime("+$billingPeriod Week",strtotime($leadDetails->converted_at)));
                        }else if($billingUnit == 'Days'){
                            $pickupDate = date('Y-m-d',strtotime("+$billingPeriod Days",strtotime($leadDetails->converted_at)));
                        }else{
                            $pickupDate = date('Y-m-d',strtotime("+$billingPeriod months",strtotime($leadDetails->converted_at)));
                        }

                        $insertedOrderId = DB::table('order_details')->insertGetId([
                            'order_id'=> 0,
                            'customer_id'=>$leadDetails->cust_id,
                            'product_id'=>$value,
                            'vendor_product_id'=>$funResponse->vendor_products_id,
                            'vendor_id'=>$prodRecord['vendor'],
                            'vendor_warehouse_id'=>$prodRecord['warehouse'],
                            'product_brand'=>$prodRecord['brand'],
                            'product_batch'=>$funResponse->vendor_products_id,
                            'product_qty'=>1,
                            'months'=>json_decode($leadDetails->billing_period)[$key],
                            'billing_period'=>json_decode($leadDetails->billing_period)[$key],
                            'billing_unit'=>json_decode($leadDetails->billing_unit)[$key],
                            'product_rent'=>json_decode($leadDetails->offered_rent)[$key],
                            'product_deposite'=>($i == 0)?json_decode($leadDetails->deposite)[$key]:0,
                            'transport'=>($i == 0)?json_decode($leadDetails->transport)[$key]:0,
                            'sale_rental' =>json_decode($leadDetails->sale_rental)[$key],
                            'vendor_product_details_id' =>$funResponse->id,
                            'unique_id'=>$funResponse->inventory_id,                            
                            'creation_date'=>date('Y-m-d',strtotime($leadDetails->converted_at)),
                            // 'pickup_date'=>date('Y-m-d',strtotime("+$month months",strtotime($leadDetails->converted_at))),
                            'pickup_date'=>$pickupDate,
                            'status'=>'Approved',
                            'upgraded'=>null,
                            'created_by'=>session('username'),
                        ]);
                        if($prodRecord['warehousetype'] == 'Vendor Warehouse' && json_decode($leadDetails->sale_rental)[$key] == 'Rental'){
                            array_push($addVendorInventory,$insertedOrderId);
                        }
                        DB::table('vendor_product_details')->where('id',$funResponse->id)->update(['current_location'=>0]);
                        array_push($arrInsertedOrderId,$insertedOrderId);
                    }
                }
                $groupedOrders = DB::table('order_details')->whereIn('id',$arrInsertedOrderId)->get()->groupBy('vendor_id');
                $order_details_id_array = array();
                foreach($groupedOrders as $key=>$value){
                    // $total = $value->pluck('product_rent')->toArray()
                    foreach($value as $key=>$detail){
                        $value[$key]->product_rent = $value[$key]->product_rent * $value[$key]->billing_period;
                    }
                    $total = array_sum($value->pluck('product_rent')->toArray())+array_sum($value->pluck('product_deposite')->toArray())+array_sum($value->pluck('transport')->toArray());
                    $fulldetails = $leadDetails->customer_name." ".$leadDetails->address_line_1." ".$leadDetails->address_line_2." ".$leadDetails->landmark." ".$leadDetails->area." ".$leadDetails->city."-".$leadDetails->pincode;
                    // dd($value->pluck('product_id')->toArray());
                    $equipments = implode(', ',DB::table('products')->whereIn('id',$value->pluck('product_id')->toArray())->get()->pluck('product_name')->toArray());

                    $generatedOrderId = DB::table('del_orders')->insertGetId([
                        'status'=>'Pending',
                        'lead_id'=>$leadDetails->id,
                        'web_order_id'=>$leadDetails->web_order_id,
                        'patient_name'=>$leadDetails->patient_name,
                        'vendor_id'=>$key,
                        'deliverypickup'=>'Delivery',
                        'DelDate'=>date('d-m-Y',strtotime($leadDetails->converted_at)),
                        'location'=>$leadDetails->location,
                        'shipping_first_name'=>$leadDetails->customer_name,
                        'cust_gender'=>$leadDetails->cust_gender,
                        'TotalAmt'=>$total,
                        'PaymentMode'=>$leadDetails->payment_mode,
                        'mobileno'=>$leadDetails->primary_contact_no,
                        'DelAssignedTo'=>'Pending',
                        'TravelMode'=>'Null',
                        'order_approval_status'=>'Pending',
                        'comment'=>$leadDetails->remark,
                        'fulldetails'=>$fulldetails,
                        'line_item_1'=>$equipments,
                        'order_approval_status'=>'Approved'
                    ]);
                    DB::table('order_details')->whereIn('id',$value->pluck('id')->toArray())->update(['order_id'=>$generatedOrderId]);
                    DB::table('vendor_rented_inventory')->whereIn('order_details_id',$value->pluck('id')->toArray())->update(['order_id'=>$generatedOrderId]);
                    
                    $leads_log_data = [
                        'log_lead_id' => $request->get('lead_id'),
                        'log_order_id' => $generatedOrderId,
                        'log_order_lead_date' => date('Y-m-d').' '.date('H:i:s'),
                        'log_order_type' => 'DO',
                        'log_lead_status' => 'Order Generated',
                        'log_date' => date('Y-m-d'),
                        'log_time' => date('H:i:s'),
                        'updated_by' => session('username')
                        ,'PARAM' => $key
                    ];
                    $order_details_id_array = array_merge($value->pluck('id')->toArray(),$order_details_id_array);
                }
                // dd("Done");
                $invoice_no = DB::table('misc_table')->where('field','invoice_no')->first('value')->value;
                DB::table('leads')->where('id',$request->get('leadid'))->update(['invoice_no'=>$invoice_no + 1]);

                DB::table('del_orders')->where('lead_id',$request->get('leadid'))->update(['invoice_no'=>$invoice_no + 1]);

                DB::table('misc_table')->where('field','invoice_no')->update(['value'=>$invoice_no + 1]);
                DB::table('leads')->where('leads.id',$request->get('leadid'))->update(['leads.lead_status'=>'Order Generated']);
                foreach($addVendorInventory as $id){
                    EditOrderController::updateVendorInOutInventory($id,'in');
                }
                DB::commit();
                return redirect('confirmed_delivery')->with('message','Order Assign Succesfully');
            }catch(Exception $ex){
                DB::rollback();
                // dd($ex);
                return redirect()->back()->with('error',$ex->getMessage());
            }
            
        }

        private function getInventory($productRecord){
            // dd($productRecord);
            if(DB::table('vendor_products')->where('vendor_id',$productRecord['vendor'])->where('warehouse_id',$productRecord['warehouse'])->where('product_brand',$productRecord['brand'])->where('product_id',$productRecord['product'])->exists()){
                $vendorProducts = DB::table('vendor_products')->where('vendor_id',$productRecord['vendor'])->where('warehouse_id',$productRecord['warehouse'])->where('product_brand',$productRecord['brand'])->where('product_id',$productRecord['product'])->first();
                if(DB::table('vendor_product_details')->where('vendor_products_id',$vendorProducts->id)->where('current_location',2)->exists()){
                    return DB::table('vendor_product_details')->where('vendor_products_id',$vendorProducts->id)->where('current_location',2)->first();
                }
            }else{
                $batchid = substr(DB::table('vendor_warehouse')->where('id',$productRecord['warehouse'])->first()->wh_city,0,1).DB::table('vendor_details')->where('id',$productRecord['vendor'])->first()->vendor_code.DB::table('products')->where('id',$productRecord['product'])->first()->product_code.date('my');
                $vendorProductsId = DB::table('vendor_products')->insertGetId([
                    'vendor_id'=>$productRecord['vendor'],
                    'product_id'=>$productRecord['product'],
                    'product_quantity'=>1,
                    'product_brand'=>$productRecord['brand'],
                    'product_rent_approved'=>0,
                    'product_rent_requested'=>0,
                    'product_deposite'=>0,
                    'warehouse_id'=>$productRecord['warehouse'],
                    'status'=>'Approved',
                    'batch'=>$batchid,
                ]);
                $vendorProducts = DB::table('vendor_products')->where('id',$vendorProductsId)->first();
            }
            $series = 0;
            if(DB::table('vendor_product_details')->join('vendor_products','vendor_products.id','=','vendor_product_details.vendor_products_id')->join('vendor_details','vendor_details.id','=','vendor_products.vendor_id')->select('vendor_product_details.series')->where('vendor_products.vendor_id',$productRecord['vendor'])->exists()){
                $series = DB::table('vendor_product_details')->join('vendor_products','vendor_products.id','=','vendor_product_details.vendor_products_id')->join('vendor_details','vendor_details.id','=','vendor_products.vendor_id')->select('vendor_product_details.series')->where('vendor_products.vendor_id',$productRecord['vendor'])->orderBy('vendor_product_details.id','DESC')->first()->series;                        
            }
            $series = $series + 1;

            $batchid = substr(DB::table('vendor_warehouse')->where('id',$productRecord['warehouse'])->first()->wh_city,0,1).DB::table('vendor_details')->where('id',$productRecord['vendor'])->first()->vendor_code.DB::table('products')->where('id',$productRecord['product'])->first()->product_code.date('my').($series+1);
            $inventoryID = DB::table('vendor_product_details')->insertGetId([
                'vendor_products_id'=>$vendorProducts->id,
                'availability_status'=>1,
                'inventory_id'=>$batchid,
                'inventory_type'=>0,
                'current_location'=>2,
                'warehouse_id'=>$productRecord['warehouse'],
                'additional_dateils'=>null,
                'series'=>$series,
                'created_by'=>session('username')
            ]);
            return DB::table('vendor_product_details')->where('id',$inventoryID)->where('current_location',2)->first();
        }

        public function ViewAllLeads_new(Request $request)
        {
            $btn_submit = $request->get('btn_submit');
            //$get_min_date = lead::get('creation_date')->first();
            $get_min_date = Carbon::now()->toDateString();
            $get_max_date = Carbon::now()->toDateString();
            // $date_changed = "False";
            // if(isset($btn_submit))
            // {
            //     $date_changed = "True";
            //     $get_min_date = "2021-08-01";
            //     $get_max_date = lead::max('creation_date');
            // }
            // else
            // {
            //     $get_min_date = date('Y-m-d');//lead::min('creation_date');
            //     $get_max_date = date('Y-m-d');//lead::max('creation_date');
            // }
            
            $get_lead_owners = DB::table('user')
                                ->select('id as user_id','username as lead_owner')
                                ->whereIn('role',["user","admin","superuser"])
								->get();
            $cities = DB::table('customer_details')->distinct('citygroup')->whereNotNull('citygroup')->orderBy('citygroup','ASC')->get('citygroup');
		    $lead_owners_arr = array_column($get_lead_owners->toArray(),'user_id');

            $whereCondition = [];
            $customer_name = $request->get('filter_customer_name');
            if(isset($customer_name)){
                $whereCondition1 = ['customer_details.customer_name','LIKE','%'.$customer_name.'%'];
                array_push($whereCondition,$whereCondition1);
            }
            $customer_contact = $request->get('filter_contact_no');
            if(isset($customer_contact)) {
                $whereCondition2 = ['customer_details.primary_contact_no','=',$customer_contact];
                array_push($whereCondition,$whereCondition2);
            }
            $lead_status = $request->get('filter_lead_status');
            $status_arr = ["Converted","Order Generated","Vendor Assigned","Delivery In Progress"];
            if(isset($lead_status) && in_array($lead_status,$status_arr)){
                $whereCondition3 = ['leads.lead_status','=',$lead_status];
                array_push($whereCondition,$whereCondition3);
            }
            $lead_owner = $request->get('filter_lead_owner');
            if(isset($lead_owner) && in_array($lead_owner,$lead_owners_arr)){
                $whereCondition7 = ['leads.lead_owner','=',$lead_owner];
                array_push($whereCondition,$whereCondition7);
            }
            if(session('role')=='user')
            {
                $whereCondition8 = ['leads.lead_owner','=',session('user_id')];
                array_push($whereCondition,$whereCondition8);
            }

            $patient_name = $request->get('filter_patient_name');
            if(isset($patient_name)){
                $whereCondition8 = ['leads.patient_name','LIKE','%'.$patient_name.'%'];
                array_push($whereCondition,$whereCondition8);
            }

            $city = $request->get('filter_city');
            if(isset($city) && $city!='All'){
                $whereCondition9 = ['customer_details.citygroup','=',$city];
                array_push($whereCondition,$whereCondition9);
            }

            $dateArr = [];
            $startDate = $request->get('filter_from_date');
            $endDate = $request->get('filter_end_date');
            if(isset($startDate) && isset($endDate)){
                array_push($dateArr,$startDate);
                array_push($dateArr,$endDate);
            }else{
                // if(session('user_id')!='15')
                // {
                    array_push($dateArr,Carbon::now()->toDateString());
                    array_push($dateArr,Carbon::now()->toDateString());
                // }
            }
            
            $sort_colmun = $request->get('sort_column');
            $sort_val = $request->get('sort_direction');
            $column = 'leads.id';
            $direction = 'DESC';
            if(isset($sort_colmun) && isset($sort_val)){
                $column = $sort_colmun;
                $direction = $sort_val;
            }
            $get_all_leads = DB::table('leads')
                                    ->join('customer_details','leads.customer_id','=','customer_details.cust_id')
                                    ->join('user','leads.lead_owner','=','user.id')
                                    ->select('leads.*','customer_details.*','leads.id as lead_id','user.*')
                                    ->whereIn('leads.lead_status',$status_arr)
                                    //->wherebetween('leads.creation_date',[$get_min_date,$get_max_date])
                                    ->when($dateArr,function($query,$dateArr){
                                        // $query->wherebetween('leads.creation_date',$dateArr);
                                        $query->wherebetween(DB::raw("(STR_TO_DATE(leads.converted_at,'%Y-%m-%d'))"),$dateArr);
                                    })
                                    ->when(session('city_based_access') == '1',function($query){
                                        $query->where('customer_details.citygroup',session('user_city'));
                                    })
                                    ->where($whereCondition)
                                    ->orderBy($column,$direction)
                                    ->get();
            $leadIdsForcount = $get_all_leads->where('lead_status','Order Generated')->pluck('lead_id');
            $orderConvertedCount = $get_all_leads->where('lead_status','Converted')->count();
            $getOrdersCountStatuswise = DB::table('del_orders')->whereIn('lead_id',$leadIdsForcount)->where('deliverypickup','Delivery')->whereNotNull('status')->get(['lead_id','order_id','status']);
            $totalOrderCount = $getOrdersCountStatuswise->count() + $orderConvertedCount;
            $ordersStateCount = $getOrdersCountStatuswise->groupBy('status');
            if($request->get('btn_export'))
            {
                $get_all_leads = $get_all_leads->paginate(1000000);
            }
            else
            {
                $get_all_leads = $get_all_leads->paginate(10);
            }
            $orderGeneratedIds = $get_all_leads->where('lead_status','Order Generated')->pluck('lead_id');
            $getOrderStatuses = DB::table('del_orders')->whereIn('lead_id',$orderGeneratedIds)->where('deliverypickup','Delivery')->get(['lead_id','order_id','status'])->groupBy('lead_id');
            $delStatuses = ['Pending','InProgress','Assigned','Accepted','Delivered','Completed','Closed','Rejected','Cust Rejected'];
            foreach ($getOrderStatuses as $lead => $leadOrders) {
                $delstate = null;
                $statusCount = Collect();
                if($leadOrders->pluck('status')->count() > 1){
                    foreach ($leadOrders->pluck('status') as $key => $status) {
                        if(in_array($status,$delStatuses)){
                            $delstateindex = array_search($status,$delStatuses);
                            $statusCount->push($delstateindex);
                        }
                    }
                    $delstate = $delStatuses[$statusCount->min()];
                }else{
                    $delstate = $leadOrders[0]->status;
                }
                $getOrderStatuses[$lead]->put('current_status',$delstate);
            }
            $all_leads_products = json_decode(json_encode($get_all_leads->toArray()),true);
            foreach($all_leads_products['data'] as $key=>$lead)
            {
                $get_product_name = DB::table('products')->select('product_name')->whereIn('id',json_decode($lead['equipment_requirement']))->get()->toArray();
                $get_product_name = implode(",",array_column(json_decode(json_encode($get_product_name),true),'product_name'));
                $all_leads_products['data'][$key]['product_name']=$get_product_name;
            }
            $all_leads_payment_status = DB::table('leads')
                    ->join('customer_details','leads.customer_id','=','customer_details.cust_id')
                    ->join('user','leads.lead_owner','=','user.id')
                    ->whereIn('leads.lead_status',$status_arr)
                    //->wherebetween('leads.creation_date',[$get_min_date,$get_max_date])
                    ->when($dateArr,function($query,$dateArr){
                        // $query->wherebetween('leads.creation_date',$dateArr);
                        $query->wherebetween(DB::raw("(STR_TO_DATE(leads.converted_at,'%Y-%m-%d'))"),$dateArr);
                    })
                    ->when(session('city_based_access') == '1',function($query){
                        $query->where('customer_details.citygroup',session('user_city'));
                    })
                    ->where($whereCondition)
                    ->orderBy('leads.id','DESC')
                    ->get();
            
            $all_leads_payment_status = json_decode(json_encode($all_leads_payment_status->toArray()),true);
            $unq_cust = array();
            $total_products = array();

            foreach($all_leads_payment_status as $key => $lead_info) 
            {
                $customer_id = $lead_info['customer_id'];
                if(in_array($customer_id,$unq_cust))
                {
                    for($i=0; $i<count($total_products); $i++)
                    {
                        if($total_products[$i]['customer_id'] == $lead_info['customer_id'])
                        {
                            $count = count($total_products[$i]['product_details']);
                            $total_products[$i]['product_details'][$count]['quantity'] = json_decode($lead_info['equipment_qty']);
                            $total_products[$i]['product_details'][$count]['offered_rent'] = json_decode($lead_info['offered_rent']);
                            $total_products[$i]['product_details'][$count]['offered_rent_total'] = json_decode($lead_info['offered_rent_total']);
                            $total_products[$i]['product_details'][$count]['deposit'] = json_decode($lead_info['deposite']);
                            $total_products[$i]['product_details'][$count]['deposit_total'] = json_decode($lead_info['deposite_total']);
                            $total_products[$i]['product_details'][$count]['sale_rental'] = json_decode($lead_info['sale_rental']);
                            $total_products[$i]['product_details'][$count]['transport'] = json_decode($lead_info['transport']);
                        }
                    }
                }
                else
                {
                    array_push($unq_cust,$customer_id);
                    $count = count($total_products);
                    $total_products[$count]['customer_id'] = $lead_info['customer_id'];
                    $total_products[$count]['product_details'][0]['quantity'] = json_decode($lead_info['equipment_qty']);
                    $total_products[$count]['product_details'][0]['offered_rent'] = json_decode($lead_info['offered_rent']);
                    $total_products[$count]['product_details'][0]['offered_rent_total'] = json_decode($lead_info['offered_rent_total']);
                    $total_products[$count]['product_details'][0]['deposit'] = json_decode($lead_info['deposite']);
                    $total_products[$count]['product_details'][0]['deposit_total'] = json_decode($lead_info['deposite_total']);
                    $total_products[$count]['product_details'][0]['transport'] = json_decode($lead_info['transport']);
                    $total_products[$count]['product_details'][0]['sale_rental'] = json_decode($lead_info['sale_rental']);
                }
            }
            $product_count = 0;
            $total_rent_product = 0;
            $total_sale_product = 0;
            $total_rent_amt = 0;
            $total_sale_amt = 0;
            $total_deposit = 0;
            $total_transport = 0;
            foreach ($total_products as $total_p)
            {
                $product_details = $total_p['product_details'];
                for ($i=0; $i <count($product_details) ; $i++) { 
                    for ($j=0; $j <count($product_details[$i]['quantity']) ; $j++) { 
                        
                        if($product_details[$i]['sale_rental'][$j]=='Rental')
                        {                            
                            $total_rent_amt += $product_details[$i]['offered_rent_total'][$j];    
                            $total_rent_product += $product_details[$i]['quantity'][$j];    
                        }
                        elseif($product_details[$i]['sale_rental'][$j]=='Sale')
                        {
                            $total_sale_amt += $product_details[$i]['offered_rent_total'][$j];    
                            $total_sale_product += $product_details[$i]['quantity'][$j];   
                        }

                        $total_transport += $product_details[$i]['transport'][$j];
                        $total_deposit += $product_details[$i]['deposit_total'][$j];
                        $product_count += $product_details[$i]['quantity'][$j];
                    }
                }
            }
            $cust_or_pay_status['product_count'] = $product_count;
            $cust_or_pay_status['total_customer'] = count($unq_cust);
            $cust_or_pay_status['total_amount'] = $total_rent_amt+$total_sale_amt+$total_transport+$total_deposit;
            $cust_or_pay_status['total_rent_product'] = $total_rent_product;
            $cust_or_pay_status['total_sale_product'] = $total_sale_product;
            $cust_or_pay_status['total_sale_amt'] = $total_sale_amt;
            $cust_or_pay_status['total_rent_amt'] = $total_rent_amt;
            $cust_or_pay_status['total_deposit'] = $total_deposit;
            $cust_or_pay_status['total_transport'] = $total_transport;            
            $filter_collapse_cookie = null;
            if(isset($_COOKIE['filter_collapse_js']) && $_COOKIE['filter_collapse_js'] =='Yes')
            {
                $filter_collapse_cookie = 1;
            }
            $filter_arr = ["cust_name"=>$customer_name,
                            "cust_no"=>$customer_contact,
                            "patient_name"=>$patient_name,
                            "city"=>$city,
                            "lead_status"=>$lead_status,
                            "lead_owner"=>$lead_owner,
                            "get_min_date"=>$startDate,
                            "get_max_date"=>$endDate,
                            "btn_submit"=>$btn_submit,
                            "sort_column"=>$sort_colmun,
                            "sort_val"=>$sort_val,
                            "filter_collapse_cookie"=>$filter_collapse_cookie];
            if($request->get('btn_export'))
            {
                ob_end_clean(); // this
                ob_start(); // and this
                return Excel::download(new ConvertedOrdersReport($get_all_leads,$all_leads_products,$filter_arr,$get_lead_owners,$cust_or_pay_status,$cities,$getOrderStatuses,$ordersStateCount,$orderConvertedCount,$totalOrderCount), 'converted_orders.xls');
            }
            return view('OrderManagement/view_all_leads',compact('get_all_leads','all_leads_products','filter_arr','get_lead_owners','cust_or_pay_status','cities','getOrderStatuses','ordersStateCount','orderConvertedCount','totalOrderCount'));            
        }
        public function viewAllLeads(Request $request)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
            return redirect()->to($url);
            }
            $date = date('Y-m-d');
            $data['start_date'] = $date;
            $data['end_date'] = $date;
            $user = DB::select("SELECT id,username FROM user WHERE role!='vendor' ");
            $data['users'] = json_decode(json_encode($user),true);
            if(session('role') == "superuser")
            {
                if(!empty($request->get('user_id')))
                {
                    $user_id=$request->get('user_id');
                    $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress' ) AND leads.creation_date='$date' AND leads.lead_owner = $user_id ORDER BY leads.creation_date DESC");
                }
                else
                {
                    $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress' ) AND leads.creation_date='$date' AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
                }
                
            }
            elseif(session('role') == "admin")
            {
                $user_city = session('user_city');
                $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated') AND customer_details.citygroup='$user_city' AND leads.creation_date='$date' AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
            }
            elseif(session('role') == "user")
            {
                $user_city = session('user_city');
                $user_id = session('user_id');
                $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress' ) AND leads.creation_date='$date' AND leads.lead_owner = $user_id AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
            }

            //$leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated') ORDER BY leads.creation_date DESC");
            //echo "SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated') ORDER BY leads.creation_date DESC";
            $data['lead_details'] = json_decode(json_encode($leads), true);
            for ($i=0; $i < count($data['lead_details']); $i++) 
            { 
                $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
                $equipement_details = array();
                for ($j=0; $j <count($product); $j++) 
                { 
                    $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
                    $product_details = json_decode(json_encode($product_details), true);
                    array_push($equipement_details,$product_details[0]['product_name']);
                }
                $equipements = json_encode($equipement_details);
                $data['lead_details'][$i]['equipment_requirement'] = $equipements;
            }

            //print_r($data['lead_details']);
            $unq_cust = array();
            $total_products = array();
            foreach($data['lead_details'] as $key => $lead_info) 
            {
                $customer_id = $lead_info['customer_id'];
                if(in_array($customer_id,$unq_cust))
                {
                    for($i=0; $i<count($total_products); $i++)
                    {
                        if($total_products[$i]['customer_id'] == $lead_info['customer_id'])
                        {
                            $count = count($total_products[$i]['product_details']);
                            $total_products[$i]['product_details'][$count]['quantity'] = json_decode($lead_info['equipment_qty']);
                            $total_products[$i]['product_details'][$count]['offered_rent'] = json_decode($lead_info['offered_rent']);
                            $total_products[$i]['product_details'][$count]['offered_rent_total'] = json_decode($lead_info['offered_rent_total']);
                            $total_products[$i]['product_details'][$count]['deposit'] = json_decode($lead_info['deposite']);
                            $total_products[$i]['product_details'][$count]['deposit_total'] = json_decode($lead_info['deposite_total']);
                            $total_products[$i]['product_details'][$count]['sale_rental'] = json_decode($lead_info['sale_rental']);
                            $total_products[$i]['product_details'][$count]['transport'] = json_decode($lead_info['transport']);
                        }
                    }
                }
                else
                {
                    array_push($unq_cust,$customer_id);
                    $count = count($total_products);
                    $total_products[$count]['customer_id'] = $lead_info['customer_id'];
                    $total_products[$count]['product_details'][0]['quantity'] = json_decode($lead_info['equipment_qty']);
                    $total_products[$count]['product_details'][0]['offered_rent'] = json_decode($lead_info['offered_rent']);
                    $total_products[$count]['product_details'][0]['offered_rent_total'] = json_decode($lead_info['offered_rent_total']);
                    $total_products[$count]['product_details'][0]['deposit'] = json_decode($lead_info['deposite']);
                    $total_products[$count]['product_details'][0]['deposit_total'] = json_decode($lead_info['deposite_total']);
                    $total_products[$count]['product_details'][0]['transport'] = json_decode($lead_info['transport']);
                    $total_products[$count]['product_details'][0]['sale_rental'] = json_decode($lead_info['sale_rental']);
                }
            }
            //print_r($total_products);
            $product_count = 0;
            $total_rent_product = 0;
            $total_sale_product = 0;
            $total_rent_amt = 0;
            $total_sale_amt = 0;
            $total_deposit = 0;
            $total_transport = 0;
            foreach ($total_products as $total_p)
            {
                $product_details = $total_p['product_details'];
                for ($i=0; $i <count($product_details) ; $i++) { 
                    for ($j=0; $j <count($product_details[$i]['quantity']) ; $j++) { 
                        
                        if($product_details[$i]['sale_rental'][$j]=='Rental')
                        {
                            $total_rent_amt += $product_details[$i]['offered_rent_total'][$j];    
                            $total_rent_product += $product_details[$i]['quantity'][$j];    
                        }
                        elseif($product_details[$i]['sale_rental'][$j]=='Sale')
                        {
                            $total_sale_amt += $product_details[$i]['offered_rent_total'][$j];    
                            $total_sale_product += $product_details[$i]['quantity'][$j];   
                        }
                        $total_transport += $product_details[$i]['transport'][$j];
                        $total_deposit += $product_details[$i]['deposit_total'][$j];
                        $product_count += $product_details[$i]['quantity'][$j];
                    }
                }
            }
            $data['product_count'] = $product_count;
            $data['total_customer'] = count($unq_cust);
            $data['total_amount'] = $total_rent_amt+$total_sale_amt+$total_transport+$total_deposit;
            $data['total_rent_product'] = $total_rent_product;
            $data['total_sale_product'] = $total_sale_product;
            $data['total_sale_amt'] = $total_sale_amt;
            $data['total_rent_amt'] = $total_rent_amt;
            $data['total_deposit'] = $total_deposit;
            $data['total_transport'] = $total_transport;
            echo "<script>localStorage['filtered']='today';</script>";
            $user = DB::select("SELECT id,username FROM user WHERE role!='vendor' ");
            $data['users'] = json_decode(json_encode($user),true);
            if(!empty($request->get('user_id')))
            {
                $data['user_id']=$request->get('user_id');
            }
            return view('OrderManagement/viewAllLeads',$data);
        }
        public function pendingAssignment()
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
            return redirect()->to($url);
            }
            $date = date('Y-m-d');
            $data['start_date'] = $date;
            $data['end_date'] = $date;
            if(session('role') == "superuser")
            {
                // $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' AND leads.creation_date='$date' AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
                $leads = DB::table('leads')
                                ->join('customer_details','customer_details.cust_id','=','leads.customer_id')
                                ->join('user','user.id','=','leads.lead_owner')
                                ->select(
                                    'leads.*',
                                    'customer_details.*',
                                    'user.username'
                                )
                                ->where('leads.lead_status','Converted')
                                ->where('leads.creation_date',$date)
                                ->when(session('city_based_access'),function($query){
                                    $query->where('customer_details.citygroup',session('user_city'));
                                })
                                ->orderBy('leads.creation_date','DESC')
                                ->get();
            }
            elseif(session('role') == "admin")
            {
                $user_city = session('user_city');
                $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' AND customer_details.citygroup='$user_city' AND leads.creation_date='$date' AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
            }
            elseif(session('role') == "user")
            {
                $user_city = session('user_city');
                $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' AND customer_details.citygroup='$user_city' AND leads.creation_date='$date' AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
            }

            //$leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated') ORDER BY leads.creation_date DESC");
            //echo "SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated') ORDER BY leads.creation_date DESC";
            $data['lead_details'] = json_decode(json_encode($leads), true);
            for ($i=0; $i < count($data['lead_details']); $i++) 
            { 
                $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
                $equipement_details = array();
                for ($j=0; $j <count($product); $j++) 
                { 
                    $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
                    $product_details = json_decode(json_encode($product_details), true);
                    array_push($equipement_details,$product_details[0]['product_name']);
                }
                $equipements = json_encode($equipement_details);
                $data['lead_details'][$i]['equipment_requirement'] = $equipements;
            }
            //print_r($data['lead_details']);
            $Countlead = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' ORDER BY leads.creation_date DESC");
            $count1 = count(json_decode(json_encode($Countlead),true));
            $data['count1'] = $count1;
            echo "<script>localStorage['filtered']='today';</script>";
            return view('OrderManagement/pendingAssignment',$data);
        }
        public function order_view_lead($customer_id,$id)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
            return redirect()->to($url);
            }
           
            $lead_details = DB::select("SELECT * FROM leads,customer_details WHERE leads.id=$id AND customer_details.cust_id=$customer_id");

             

            $data['lead_details'] = json_decode(json_encode($lead_details), true);
            $user_id = $data['lead_details'][0]['lead_owner'];
            $lead_owner_details = DB::select("SELECT * FROM user WHERE id=$user_id");
            $data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
            $data['lead_details'][0]['username'] =  $data['lead_owner_details'][0]['username'];
            $data['lead_details'][0]['user_id'] =  $data['lead_owner_details'][0]['id'];
            $product = json_decode($data['lead_details'][0]['equipment_requirement']);         
            $equipement_details = array();
           
            $user_id = $data['lead_details'][0]['lead_owner'];
            $lead_owner_details = DB::select("SELECT * FROM user WHERE id=$user_id");
            $data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
            $data['lead_details'][0]['username'] =  $data['lead_owner_details'][0]['username'];
            $data['lead_details'][0]['user_id'] =  $data['lead_owner_details'][0]['id'];
            // for ($j=0; $j <count($product); $j++) 
            // { 
            // $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
            // $product_details = json_decode(json_encode($product_details), true);
            // array_push($equipement_details,$product_details[0]['product_name']);
            // }
            // Check if $product is a valid array and not empty
            if (isset($product) && is_array($product) && !empty($product)) {
                for ($j = 0; $j < count($product); $j++) {
                    $product_id = $product[$j] ?? 0; // Avoid undefined index error

                    // Use query bindings for security
                    $product_details = DB::select("SELECT product_name FROM products WHERE id = ?", [$product_id]);
                    $product_details = json_decode(json_encode($product_details), true);

                    // Check if product_details has data before accessing
                    if (!empty($product_details) && isset($product_details[0]['product_name'])) {
                        array_push($equipement_details, $product_details[0]['product_name']);
                    } else {
                        array_push($equipement_details, "Unknown Product"); // Graceful handling
                    }
                }
            }
 
            $equipements = json_encode($equipement_details);
            $data['lead_details'][0]['equipment_requirement'] = $equipements;
            
            $leads_log_data = DB::select("SELECT * FROM leads_log WHERE log_lead_id = $id");
            $data['leads_log_data'] = json_decode(json_encode($leads_log_data), true);
            $data['log_lead_status'] = array();
            $data['log_lead_date'] = array();
            $data['log_lead_time'] = array();
            
            foreach($data['leads_log_data'] as $leads)
            {
                array_push($data['log_lead_status'], $leads['log_lead_status']);
                array_push($data['log_lead_date'], $leads['log_date']);
                array_push($data['log_lead_time'], $leads['log_time']);
            }
           
            return view('OrderManagement/view_lead',$data);
        }
        //-----filter order lead-------//
        public function filterOrderLeads($filter_by)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
             return redirect()->to($url);
            }
            $user = DB::select("SELECT id,username FROM user WHERE role!='vendor' ");
            $data['users'] = json_decode(json_encode($user),true);
            if($filter_by =='today')
            {
                $date = date('Y-m-d');
                $data['start_date'] = $date;
                $data['end_date'] = $date;
                $whereClause = "leads.creation_date = '$date'";
                echo "<script>localStorage['filtered']='today';</script>";
            }
            elseif($filter_by =='yesterday')
            {
                $prevDate = date('Y-m-d',strtotime("-1 days"));
                $data['start_date'] = $prevDate;
                $data['end_date'] = date('Y-m-d');
                $whereClause = "leads.creation_date = '$prevDate'";
                echo "<script>localStorage['filtered']='yesterday';</script>";
            }
            elseif($filter_by =='past_3_days')
            {
                $past_three_days = date('Y-m-d',strtotime("-2 days"));
                $data['start_date'] = $past_three_days;
                $data['end_date'] = date('Y-m-d');
                $whereClause = "leads.creation_date >= '$past_three_days'";
                echo "<script>localStorage['filtered']='past_3_days';</script>";
            }
            elseif($filter_by =='week')
            {
                $past_three_days = date('Y-m-d',strtotime("-7 days"));
                $data['start_date'] = $past_three_days;
                $data['end_date'] = date('Y-m-d');
                $whereClause = "leads.creation_date >= '$past_three_days'";
                echo "<script>localStorage['filtered']='week';</script>";
            }
            elseif($filter_by =='month')
            {
                $month = date('m-Y');
                $start_date_temp = '01-'.$month;
                $start_date = date('Y-m-d',strtotime($start_date_temp));
                $end_date_temp = '31-'.$month;
                $end_date = date('Y-m-d',strtotime($end_date_temp));
                $data['start_date'] = $start_date;
                $data['end_date'] = $end_date;
                $whereClause = "leads.creation_date BETWEEN '$start_date' AND '$end_date'";
            }
            elseif($filter_by == 'all')

            {   if(session('role') == "superuser")
                {
                    $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress' ) AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
                }
                elseif(session('role') == "admin")
                {
                    $user_city = session('user_city');
                    $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated') AND customer_details.citygroup='$user_city' AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
                }
                elseif(session('role') == "user")
                {
                    $user_city = session('user_city');
                    $user_id = session('user_id');
                    // $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress' ) AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
                    $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress' ) AND leads.creation_date='$date' AND leads.lead_owner = $user_id AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
                }
                
        
                    $data['lead_details'] = json_decode(json_encode($leads), true);
                    for ($i=0; $i < count($data['lead_details']); $i++) 
                    { 
                        $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
                        $equipement_details = array();
                        for ($j=0; $j <count($product); $j++) 
                        { 
                            $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
                            $product_details = json_decode(json_encode($product_details), true);
                            array_push($equipement_details,$product_details[0]['product_name']);
                        }
                        $equipements = json_encode($equipement_details);
                        $data['lead_details'][$i]['equipment_requirement'] = $equipements;
                    }
                //total status
                $unq_cust = array();
                $total_products = array();
                foreach($data['lead_details'] as $key => $lead_info) 
                {
                    $customer_id = $lead_info['customer_id'];
                    if(in_array($customer_id,$unq_cust))
                    {
                        for($i=0; $i<count($total_products); $i++)
                        {
                            if($total_products[$i]['customer_id'] == $lead_info['customer_id'])
                            {
                                $count = count($total_products[$i]['product_details']);
                                $total_products[$i]['product_details'][$count]['quantity'] = json_decode($lead_info['equipment_qty']);
                                $total_products[$i]['product_details'][$count]['offered_rent'] = json_decode($lead_info['offered_rent']);
                                $total_products[$i]['product_details'][$count]['offered_rent_total'] = json_decode($lead_info['offered_rent_total']);
                                $total_products[$i]['product_details'][$count]['deposit'] = json_decode($lead_info['deposite']);
                                $total_products[$i]['product_details'][$count]['deposit_total'] = json_decode($lead_info['deposite_total']);
                                $total_products[$i]['product_details'][$count]['sale_rental'] = json_decode($lead_info['sale_rental']);
                                $total_products[$i]['product_details'][$count]['transport'] = json_decode($lead_info['transport']);
                            }
                        }
                    }
                    else
                    {
                        array_push($unq_cust,$customer_id);
                        $count = count($total_products);
                        $total_products[$count]['customer_id'] = $lead_info['customer_id'];
                        $total_products[$count]['product_details'][0]['quantity'] = json_decode($lead_info['equipment_qty']);
                        $total_products[$count]['product_details'][0]['offered_rent'] = json_decode($lead_info['offered_rent']);
                        $total_products[$count]['product_details'][0]['offered_rent_total'] = json_decode($lead_info['offered_rent_total']);
                        $total_products[$count]['product_details'][0]['deposit'] = json_decode($lead_info['deposite']);
                        $total_products[$count]['product_details'][0]['deposit_total'] = json_decode($lead_info['deposite_total']);
                        $total_products[$count]['product_details'][0]['transport'] = json_decode($lead_info['transport']);
                        $total_products[$count]['product_details'][0]['sale_rental'] = json_decode($lead_info['sale_rental']);
                    }
                }
                //print_r($total_products);
                $product_count = 0;
                $total_rent_product = 0;
                $total_sale_product = 0;
                $total_rent_amt = 0;
                $total_sale_amt = 0;
                $total_deposit = 0;
                $total_transport = 0;
                foreach ($total_products as $total_p)
                {
                    $product_details = $total_p['product_details'];
                    for ($i=0; $i <count($product_details) ; $i++) { 
                        for ($j=0; $j <count($product_details[$i]['quantity']) ; $j++) { 
                            
                            if($product_details[$i]['sale_rental'][$j]=='Rental')
                            {
                                $total_rent_amt += $product_details[$i]['offered_rent_total'][$j];    
                                $total_rent_product += $product_details[$i]['quantity'][$j];    
                            }
                            elseif($product_details[$i]['sale_rental'][$j]=='Sale')
                            {
                                $total_sale_amt += $product_details[$i]['offered_rent_total'][$j];    
                                $total_sale_product += $product_details[$i]['quantity'][$j];   
                            }
                            $total_transport += $product_details[$i]['transport'][$j];
                            $product_count += $product_details[$i]['quantity'][$j];
                        }
                    }
                }
                $data['product_count'] = $product_count;
                $data['total_customer'] = count($unq_cust);
                $data['total_amount'] = $total_rent_amt+$total_sale_amt+$total_transport;
                $data['total_rent_product'] = $total_rent_product;
                $data['total_sale_product'] = $total_sale_product;
                $data['total_sale_amt'] = $total_sale_amt;
                $data['total_rent_amt'] = $total_rent_amt;
                $data['total_deposit'] = $total_deposit;
                $data['total_transport'] = $total_transport;
                echo "<script>localStorage['filtered']='all';</script>";
                return view('OrderManagement/viewAllLeads',$data);
            }
            

            if(session('role') == "superuser")
            {
                $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress' ) AND $whereClause AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
            }
            elseif(session('role') == "admin")
            {
                $user_city = session('user_city');
                $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated')  AND customer_details.citygroup='$user_city' AND $whereClause AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
            }
            elseif(session('role') == "user")
            {
                $user_city = session('user_city');
                // $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress' ) AND $whereClause AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
                $user_id = session('user_id');
                // $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress' ) AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
                $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress' ) AND $whereClause AND leads.lead_owner = $user_id AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
            }
            //echo "SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.lead_status = 'Work In Process' AND $whereClause AND ORDER BY leads.creation_date DESC";
                $data['lead_details'] = json_decode(json_encode($leads), true);
                for ($i=0; $i < count($data['lead_details']); $i++) 
                { 
                $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
                $equipement_details = array();
                for ($j=0; $j <count($product); $j++) 
                { 
                    $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
                    $product_details = json_decode(json_encode($product_details), true);
                    array_push($equipement_details,$product_details[0]['product_name']);
                }
                $equipements = json_encode($equipement_details);
                $data['lead_details'][$i]['equipment_requirement'] = $equipements;
                }

                //total status
                $unq_cust = array();
                $total_products = array();
                foreach($data['lead_details'] as $key => $lead_info) 
                {
                    $customer_id = $lead_info['customer_id'];
                    if(in_array($customer_id,$unq_cust))
                    {
                        for($i=0; $i<count($total_products); $i++)
                        {
                            if($total_products[$i]['customer_id'] == $lead_info['customer_id'])
                            {
                                $count = count($total_products[$i]['product_details']);
                                $total_products[$i]['product_details'][$count]['quantity'] = json_decode($lead_info['equipment_qty']);
                                $total_products[$i]['product_details'][$count]['offered_rent'] = json_decode($lead_info['offered_rent']);
                                $total_products[$i]['product_details'][$count]['offered_rent_total'] = json_decode($lead_info['offered_rent_total']);
                                $total_products[$i]['product_details'][$count]['deposit'] = json_decode($lead_info['deposite']);
                                $total_products[$i]['product_details'][$count]['deposit_total'] = json_decode($lead_info['deposite_total']);
                                $total_products[$i]['product_details'][$count]['sale_rental'] = json_decode($lead_info['sale_rental']);
                                $total_products[$i]['product_details'][$count]['transport'] = json_decode($lead_info['transport']);
                            }
                        }
                    }
                    else
                    {
                        array_push($unq_cust,$customer_id);
                        $count = count($total_products);
                        $total_products[$count]['customer_id'] = $lead_info['customer_id'];
                        $total_products[$count]['product_details'][0]['quantity'] = json_decode($lead_info['equipment_qty']);
                        $total_products[$count]['product_details'][0]['offered_rent'] = json_decode($lead_info['offered_rent']);
                        $total_products[$count]['product_details'][0]['offered_rent_total'] = json_decode($lead_info['offered_rent_total']);
                        $total_products[$count]['product_details'][0]['deposit'] = json_decode($lead_info['deposite']);
                        $total_products[$count]['product_details'][0]['deposit_total'] = json_decode($lead_info['deposite_total']);
                        $total_products[$count]['product_details'][0]['transport'] = json_decode($lead_info['transport']);
                        $total_products[$count]['product_details'][0]['sale_rental'] = json_decode($lead_info['sale_rental']);
                    }
                }
                //print_r($total_products);
                $product_count = 0;
                $total_rent_product = 0;
                $total_sale_product = 0;
                $total_rent_amt = 0;
                $total_sale_amt = 0;
                $total_deposit = 0;
                $total_transport = 0;
                foreach ($total_products as $total_p)
                {
                    $product_details = $total_p['product_details'];
                    for ($i=0; $i <count($product_details) ; $i++) { 
                        for ($j=0; $j <count($product_details[$i]['quantity']) ; $j++) { 
                            
                            if($product_details[$i]['sale_rental'][$j]=='Rental')
                            {
                                $total_rent_amt += $product_details[$i]['offered_rent_total'][$j];    
                                $total_rent_product += $product_details[$i]['quantity'][$j];    
                            }
                            elseif($product_details[$i]['sale_rental'][$j]=='Sale')
                            {
                                $total_sale_amt += $product_details[$i]['offered_rent_total'][$j];    
                                $total_sale_product += $product_details[$i]['quantity'][$j];   
                            }
                            $total_transport += $product_details[$i]['transport'][$j];
                            $product_count += $product_details[$i]['quantity'][$j];
                        }
                    }
                }
                $data['product_count'] = $product_count;
                $data['total_customer'] = count($unq_cust);
                $data['total_amount'] = $total_rent_amt+$total_sale_amt+$total_transport;
                $data['total_rent_product'] = $total_rent_product;
                $data['total_sale_product'] = $total_sale_product;
                $data['total_sale_amt'] = $total_sale_amt;
                $data['total_rent_amt'] = $total_rent_amt;
                $data['total_deposit'] = $total_deposit;
                $data['total_transport'] = $total_transport;
                return view('OrderManagement/viewAllLeads',$data);
        }
        public function filterOrderLeadsDWS()
        {
            if($_SERVER['REQUEST_METHOD']=='POST')
            {
                
                
                $start_date = date('Y-m-d',strtotime($_POST['start_date']));
                $end_date = date('Y-m-d',strtotime($_POST['end_date']));
                $data['start_date'] = $start_date;
                $data['end_date'] = $end_date;
                $whereClause = "leads.creation_date BETWEEN '$start_date' AND '$end_date'";

                if(session('role') == "superuser")
                {
                    $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress' ) AND $whereClause AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
                }
                elseif(session('role') == "admin")
                {
                    $user_city = session('user_city');
                    $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated')  AND customer_details.citygroup='$user_city' AND $whereClause AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
                }
                elseif(session('role') == "user")
                {
                    $user_city = session('user_city');
                    $user_id = session('user_id');
                    $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress' ) AND $whereClause AND leads.lead_owner = $user_id AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
                }
                //echo "SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.lead_status = 'Work In Process' AND $whereClause AND ORDER BY leads.creation_date DESC";
                    $data['lead_details'] = json_decode(json_encode($leads), true);
                    for ($i=0; $i < count($data['lead_details']); $i++) 
                    { 
                    $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
                    $equipement_details = array();
                    for ($j=0; $j <count($product); $j++) 
                    { 
                        $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
                        $product_details = json_decode(json_encode($product_details), true);
                        array_push($equipement_details,$product_details[0]['product_name']);
                    }
                    $equipements = json_encode($equipement_details);
                    $data['lead_details'][$i]['equipment_requirement'] = $equipements;
                    }
                    $unq_cust = array();
                $total_products = array();
                foreach($data['lead_details'] as $key => $lead_info) 
                {
                    $customer_id = $lead_info['customer_id'];
                    if(in_array($customer_id,$unq_cust))
                    {
                        for($i=0; $i<count($total_products); $i++)
                        {
                            if($total_products[$i]['customer_id'] == $lead_info['customer_id'])
                            {
                                $count = count($total_products[$i]['product_details']);
                                $total_products[$i]['product_details'][$count]['quantity'] = json_decode($lead_info['equipment_qty']);
                                $total_products[$i]['product_details'][$count]['offered_rent'] = json_decode($lead_info['offered_rent']);
                                $total_products[$i]['product_details'][$count]['offered_rent_total'] = json_decode($lead_info['offered_rent_total']);
                                $total_products[$i]['product_details'][$count]['deposit'] = json_decode($lead_info['deposite']);
                                $total_products[$i]['product_details'][$count]['deposit_total'] = json_decode($lead_info['deposite_total']);
                                $total_products[$i]['product_details'][$count]['sale_rental'] = json_decode($lead_info['sale_rental']);
                                $total_products[$i]['product_details'][$count]['transport'] = json_decode($lead_info['transport']);
                            }
                        }
                    }
                    else
                    {
                        array_push($unq_cust,$customer_id);
                        $count = count($total_products);
                        $total_products[$count]['customer_id'] = $lead_info['customer_id'];
                        $total_products[$count]['product_details'][0]['quantity'] = json_decode($lead_info['equipment_qty']);
                        $total_products[$count]['product_details'][0]['offered_rent'] = json_decode($lead_info['offered_rent']);
                        $total_products[$count]['product_details'][0]['offered_rent_total'] = json_decode($lead_info['offered_rent_total']);
                        $total_products[$count]['product_details'][0]['deposit'] = json_decode($lead_info['deposite']);
                        $total_products[$count]['product_details'][0]['deposit_total'] = json_decode($lead_info['deposite_total']);
                        $total_products[$count]['product_details'][0]['transport'] = json_decode($lead_info['transport']);
                        $total_products[$count]['product_details'][0]['sale_rental'] = json_decode($lead_info['sale_rental']);
                    }
                }
                //print_r($total_products);
                $product_count = 0;
                $total_rent_product = 0;
                $total_sale_product = 0;
                $total_rent_amt = 0;
                $total_sale_amt = 0;
                $total_deposit = 0;
                $total_transport = 0;
                // foreach ($total_products as $total_p)
                // {
                //     $product_details = $total_p['product_details'];
                //     for ($i=0; $i <count($product_details) ; $i++) { 
                //         for ($j=0; $j <count($product_details[$i]['quantity']) ; $j++) { 
                            
                //             if($product_details[$i]['sale_rental'][$j]=='Rental')
                //             {
                //                 $total_rent_amt += $product_details[$i]['offered_rent_total'][$j];    
                //                 $total_rent_product += $product_details[$i]['quantity'][$j];    
                //             }
                //             elseif($product_details[$i]['sale_rental'][$j]=='Sale')
                //             {
                //                 $total_sale_amt += $product_details[$i]['offered_rent_total'][$j];    
                //                 $total_sale_product += $product_details[$i]['quantity'][$j];   
                //             }
                //             $total_transport += $product_details[$i]['transport'][$j];
                //             $total_deposit += $product_details[$i]['deposit_total'][$j];
                //             $product_count += $product_details[$i]['quantity'][$j];
                //         }
                //     }
                // }
                foreach($data['lead_details'] as $leads)
                {
                    $qty = json_decode($leads['equipment_qty']);
                    $sale_rental = json_decode($leads['sale_rental']);
                    $rent_total = json_decode($leads['offered_rent_total']);
                    $deposit_total = json_decode($leads['deposite_total']);
                    $transport = json_decode($leads['transport']);
                    for ($j=0; $j <count($qty) ; $j++) 
                    {
                            
                        if($sale_rental[$j]=='Rental')
                        {
                            $total_rent_amt += $rent_total[$j];    
                            $total_rent_product += $qty[$j];    
                            $total_deposit += $deposit_total[$j];
                        }
                        elseif($sale_rental[$j]=='Sale')
                        {
                            $total_sale_amt += $rent_total[$j];    
                            $total_sale_product += $qty[$j];    
                        }
                        $total_transport += $transport[$j];
                        $product_count += $qty[$j];
                    }
                }
                $data['product_count'] = $product_count;
                $data['total_customer'] = count($unq_cust);
                $data['total_amount'] = $total_rent_amt+$total_sale_amt+$total_transport+$total_deposit;
                $data['total_rent_product'] = $total_rent_product;
                $data['total_sale_product'] = $total_sale_product;
                $data['total_sale_amt'] = $total_sale_amt;
                $data['total_rent_amt'] = $total_rent_amt;
                $data['total_deposit'] = $total_deposit;
                $data['total_transport'] = $total_transport;
                return view('OrderManagement/viewAllLeads',$data);
            }
        }
        //-----filter order lead-------//
        public function filterPendingAssignment($filter_by)
        {
            if($filter_by =='today')
            {
                $date = date('Y-m-d');
                $data['start_date'] = $date;
                $data['end_date'] = $date;
                $whereClause = "leads.creation_date = '$date'";
                echo "<script>localStorage['filtered']='today';</script>";
            }
            elseif($filter_by =='yesterday')
            {
                $prevDate = date('Y-m-d',strtotime("-1 days"));
                $data['start_date'] = $prevDate;
                $data['end_date'] = date('Y-m-d');
                $whereClause = "leads.creation_date = '$prevDate'";
                echo "<script>localStorage['filtered']='yesterday';</script>";
            }
            elseif($filter_by =='past_3_days')
            {
                $past_three_days = date('Y-m-d',strtotime("-2 days"));
                $data['start_date'] = $past_three_days;
                $data['end_date'] = date('Y-m-d');
                $whereClause = "leads.creation_date >= '$past_three_days'";
                echo "<script>localStorage['filtered']='past_3_days';</script>";
            }
            elseif($filter_by =='week')
            {
                $past_three_days = date('Y-m-d',strtotime("-7 days"));
                $data['start_date'] = $past_three_days;
                $data['end_date'] = date('Y-m-d');
                $whereClause = "leads.creation_date >= '$past_three_days'";
                echo "<script>localStorage['filtered']='week';</script>";
            }
            elseif($filter_by =='month')
            {
                $month = date('m-Y');
                $start_date_temp = '01-'.$month;
                $start_date = date('Y-m-d',strtotime($start_date_temp));
                $end_date_temp = '31-'.$month;
                $end_date = date('Y-m-d',strtotime($end_date_temp));
                $data['start_date'] = $start_date;
                $data['end_date'] = $end_date;
                $whereClause = "leads.creation_date BETWEEN '$start_date' AND '$end_date'";
            }
            elseif($filter_by == 'all')
            {   if(session('role') == "superuser")
                {
                    $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
                }
                elseif(session('role') == "admin")
                {
                    $user_city = session('user_city');
                    $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' AND customer_details.citygroup='$user_city' AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
                }
                elseif(session('role') == "user")
                {
                    $user_city = session('user_city');
                    $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' AND customer_details.citygroup='$user_city' AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
                }
                
        
                    $data['lead_details'] = json_decode(json_encode($leads), true);
                    for ($i=0; $i < count($data['lead_details']); $i++) 
                    { 
                        $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
                        $equipement_details = array();
                        for ($j=0; $j <count($product); $j++) 
                        { 
                            $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
                            $product_details = json_decode(json_encode($product_details), true);
                            array_push($equipement_details,$product_details[0]['product_name']);
                        }
                        $equipements = json_encode($equipement_details);
                        $data['lead_details'][$i]['equipment_requirement'] = $equipements;
                    }
                
                echo "<script>localStorage['filtered']='all';</script>";
                $Countlead = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' ORDER BY leads.creation_date DESC");
                $count1 = count(json_decode(json_encode($Countlead),true));
                $data['count1'] = $count1;
                return view('OrderManagement/pendingAssignment',$data);
            }
            

            if(session('role') == "superuser")
            {
                $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' AND $whereClause ORDER BY leads.creation_date DESC");
            }
            elseif(session('role') == "admin")
            {
                $user_city = session('user_city');
                $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' AND customer_details.citygroup='$user_city' AND $whereClause ORDER BY leads.creation_date DESC");
            }
            elseif(session('role') == "user")
            {
                $user_city = session('user_city');
                $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' AND customer_details.citygroup='$user_city' AND $whereClause ORDER BY leads.creation_date DESC");
            }
            //echo "SELECT * FROM leads,customer_details WHERE leads.lead_status = 'Work In Process' AND $whereClause AND ORDER BY leads.creation_date DESC";
                $data['lead_details'] = json_decode(json_encode($leads), true);
                for ($i=0; $i < count($data['lead_details']); $i++) 
                { 
                $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
                $equipement_details = array();
                for ($j=0; $j <count($product); $j++) 
                { 
                    $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
                    $product_details = json_decode(json_encode($product_details), true);
                    array_push($equipement_details,$product_details[0]['product_name']);
                }
                $equipements = json_encode($equipement_details);
                $data['lead_details'][$i]['equipment_requirement'] = $equipements;
                }
                $Countlead = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' ORDER BY leads.creation_date DESC");
                $count1 = count(json_decode(json_encode($Countlead),true));
                $data['count1'] = $count1;
                return view('OrderManagement/pendingAssignment',$data);
        }
        public function filterPendingAssignmentDWS()
        {
            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $start_date = $_POST['start_date'];
                $end_date = $_POST['end_date'];
                $data['start_date'] = $start_date;
                $data['end_date'] = $end_date;
                $whereClause = "leads.creation_date BETWEEN '$start_date' AND '$end_date'";

                if(session('role') == "superuser")
                {
                    $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' AND $whereClause ORDER BY leads.creation_date DESC");
                }
                elseif(session('role') == "admin")
                {
                    $user_city = session('user_city');
                    $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' AND customer_details.citygroup='$user_city' AND $whereClause ORDER BY leads.creation_date DESC");
                }
                elseif(session('role') == "user")
                {
                    $user_city = session('user_city');
                    $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' AND customer_details.citygroup='$user_city' AND $whereClause ORDER BY leads.creation_date DESC");
                }
                //echo "SELECT * FROM leads,customer_details WHERE leads.lead_status = 'Work In Process' AND $whereClause AND ORDER BY leads.creation_date DESC";
                $data['lead_details'] = json_decode(json_encode($leads), true);
                for ($i=0; $i < count($data['lead_details']); $i++) 
                { 
                $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
                $equipement_details = array();
                for ($j=0; $j <count($product); $j++) 
                { 
                    $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
                    $product_details = json_decode(json_encode($product_details), true);
                    array_push($equipement_details,$product_details[0]['product_name']);
                }
                $equipements = json_encode($equipement_details);
                $data['lead_details'][$i]['equipment_requirement'] = $equipements;
                }
                $Countlead = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' ORDER BY leads.creation_date DESC");
                $count1 = count(json_decode(json_encode($Countlead),true));
                $data['count1'] = $count1;
                return view('OrderManagement/pendingAssignment',$data);
            }
        }
        public function pendingAssignmentsNotify()
        {
            // $date = date('Y-m-d',strtotime('-7 days'));
            // $today = date('Y-m-d');
            // if(session('role') == "superuser")
            // {
            //     $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' AND leads.creation_date BETWEEN '$date' AND '$today' ORDER BY leads.creation_date DESC");
            //     // $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' ORDER BY leads.creation_date DESC");
            // }
            // elseif(session('role') == "admin")
            // {
            //     $user_city = session('user_city');
            //     $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' AND customer_details.citygroup='$user_city' AND leads.creation_date BETWEEN '$date' AND '$today' ORDER BY leads.creation_date DESC");
            //     // $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' AND customer_details.citygroup='$user_city' ORDER BY leads.creation_date DESC");
            // }
            // $data['leads'] = json_decode(json_encode($leads), true);
            // $leads = count($data['leads']);
            // echo $leads;
            $date = Carbon::now()->subDays(7)->toDateString();
            $today = Carbon::now()->toDateString();
            $user_city = session('user_city');
            $leads = DB::table('leads')
                            ->join('customer_details','customer_details.cust_id','=','leads.customer_id')
                            ->select(DB::raw("count('leads.id') as count"))
                            ->when(session('role') == 'admin',function($query)use($user_city){
                                $query->where('cutomer_details.citygroup',$user_city);
                            })
                            ->whereBetween(DB::raw("STR_TO_DATE(leads.converted_at,'%Y-%m-%d')"),[DB::raw("STR_TO_DATE('$date','%Y-%m-%d')"),DB::raw("STR_TO_DATE('$today','%Y-%m-%d')")])
                            ->where('leads.lead_status','Converted')
                            ->get();
                            return $leads[0]->count;
        }
        /*Assign Vendor*/
        public function assign_vendor()
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
            if($_SERVER['REQUEST_METHOD']=='GET')
            {
                $id = $_GET['lead_id'];
                $cust_id = $_GET['customer_id'];
                DB::enableQueryLog();
                $lead_details = DB::select("SELECT * FROM leads,customer_details WHERE leads.id=$id AND customer_details.cust_id=$cust_id");
                $data['lead_details'] = json_decode(json_encode($lead_details), true);

                $product = json_decode($data['lead_details'][0]['equipment_requirement']);
                $equipment_id = $data['lead_details'][0]['equipment_requirement'];
                
                $equipement_details = array();
                $equipement_rent = array();
                $default_vdr_details = array();
                for ($j=0; $j <count($product); $j++) 
                {
                    $date = date('Y-m-d',strtotime($data['lead_details'][0]['converted_at']));
                    if(DB::table('temp_cust_drop_location')->where('cust_id',$cust_id)->where('product_id',$product[$j])->where('date',$date)->exists())
                    {
                        $details = DB::table('temp_cust_drop_location')->where('cust_id',$cust_id)->where('product_id',$product[$j])->where('date',$date)->get()->toArray();
                        $pickups_id = $details[0]->pickup_id;
                        $default_vendor_details = DB::table('pickups')
                                                ->join('order_details','pickups.order_details_id','=','order_details.id')
                                                ->join('vendor_details','order_details.vendor_id','=','vendor_details.id')
                                                ->join('vendor_warehouse','order_details.vendor_warehouse_id','=','vendor_warehouse.id')
                                                ->join('vendor_products','order_details.vendor_product_id','=','vendor_products.id')
                                                ->join('vendor_product_details','order_details.vendor_product_details_id','=','vendor_product_details.id')
                                                ->join('product_brands','vendor_products.product_brand','=','product_brands.id')                                                
                                                ->select(
                                                    'vendor_details.id as vendor_id',
                                                    'vendor_details.registered_name as vendor_name',
                                                    'vendor_warehouse.id as warehouse_id',
                                                    'vendor_warehouse.wh_name as wh_name',
                                                    'vendor_warehouse.wh_area as wh_area',
                                                    'vendor_warehouse.wh_city as wh_city',
                                                    'product_brands.id as brand_id',
                                                    'product_brands.brand_name as brand_name',
                                                    'vendor_products.id as vendor_product_id',
                                                    'vendor_products.batch as batch',
                                                    'vendor_products.product_rent_approved as product_rent',
                                                    'vendor_product_details.id as vendor_product_details_id',
                                                    'vendor_product_details.inventory_id as inventory_id'
                                                    )
                                                ->where('pickups.id',$pickups_id)
                                                ->get()
                                                ->toArray();
                        // dd($default_vendor_details);
                        $default_vdr_details[$j] = $default_vendor_details;
                    }
                    else
                    {
                        $default_vdr_details[$j] = "Not Found";
                    }
                    $product_details = DB::select("SELECT product_name,product_rent FROM products WHERE id = $product[$j]");
                    $product_details = json_decode(json_encode($product_details), true);
                    array_push($equipement_details,$product_details[0]['product_name']);
                    array_push($equipement_rent,$product_details[0]['product_rent']);
                }
                $equipements = json_encode($equipement_details);
                $equipements_rent = json_encode($equipement_rent);
                $data['lead_details'][0]['equipment_requirement'] = $equipements;
                $data['lead_details'][0]['equipment_id'] = $equipment_id;
                $data['lead_details'][0]['equipments_rent'] = $equipements_rent;
                $vendor_details = DB::select("SELECT * FROM vendor_details");
                $data['vendor_details'] = json_decode(json_encode($vendor_details), true);
                // dd(DB::getQueryLog());
                // dd($default_vdr_details);
                $data['default_vdr_details'] = $default_vdr_details;
                // dd($data['default_vdr_details']);
                return view('OrderManagement/assign_vendor_view',$data);
            }
        }
        public function assign_vendor_exp()
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
            if($_SERVER['REQUEST_METHOD']=='POST')
            {
                $lead_id = $_POST['lead_id'];
                $customer_id = $_POST['customer_id'];
                
                $lead_details = DB::select("SELECT * FROM leads,customer_details WHERE leads.id=$lead_id AND leads.customer_id = customer_details.cust_id AND customer_details.cust_id=$customer_id");
                $data['lead_details'] = json_decode(json_encode($lead_details), true);

                $product = json_decode($data['lead_details'][0]['equipment_requirement']);
                $equipment_id = $data['lead_details'][0]['equipment_requirement'];
                
                $equipement_details = array();
                $equipement_rent = array();
                for ($j=0; $j <count($product); $j++) 
                { 
                    $product_details = DB::select("SELECT product_name,product_rent FROM products WHERE id = $product[$j]");
                    $product_details = json_decode(json_encode($product_details), true);
                    array_push($equipement_details,$product_details[0]['product_name']);
                    array_push($equipement_rent,$product_details[0]['product_rent']);
                }
                $equipements = json_encode($equipement_details);
                $equipements_rent = json_encode($equipement_rent);
                $data['lead_details'][0]['equipment_requirement'] = $equipements;
                $data['lead_details'][0]['equipment_id'] = $equipment_id;
                $data['lead_details'][0]['equipments_rent'] = $equipements_rent;
                $vendor_details = DB::select("SELECT * FROM vendor_details"  );
                $data['vendor_details'] = json_decode(json_encode($vendor_details), true);
                echo json_encode($data);
                // return view('OrderManagement/assign_vendor_qty',$data);
            }
        }
        public function assign_vendor_byscript($customer_id,$lead_id)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
                $id = $lead_id;
                $cust_id = $customer_id;
                
                $lead_details = DB::select("SELECT * FROM leads,customer_details WHERE leads.id=$id AND customer_details.cust_id=$cust_id");
                $data['lead_details'] = json_decode(json_encode($lead_details), true);

                $product = json_decode($data['lead_details'][0]['equipment_requirement']);
                $equipment_id = $data['lead_details'][0]['equipment_requirement'];
                $equipement_details = array();
                for ($j=0; $j <count($product); $j++) 
                { 
                    $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
                    $product_details = json_decode(json_encode($product_details), true);
                    array_push($equipement_details,$product_details[0]['product_name']);
                }
                $equipements = json_encode($equipement_details);
                $data['lead_details'][0]['equipment_requirement'] = $equipements;
                $data['lead_details'][0]['equipment_id'] = $equipment_id;

                $vendor_details = DB::select("SELECT * FROM vendor_details"  );
                $data['vendor_details'] = json_decode(json_encode($vendor_details), true);
                return view('OrderManagement/assign_vendor',$data);      
        }
        //------Individual vendor----//
        public function individual_vendor($equipment)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
            if($_SERVER['REQUEST_METHOD']=='GET')
            {
                $vendor_products = DB::select("SELECT * FROM vendor_products,vendor_details WHERE vendor_products.product_id= '$equipment' AND vendor_details.id = vendor_products.vendor_id ");
                $data['vendor_products'] = json_decode(json_encode($vendor_products), true);   
                $data['vendor_products'][0]['sale_rental']=$sale_rental;
                
                if(isset($data['vendor_products']) && $data['vendor_products'] != NULL)
                {
                    $json = $data['vendor_products'];
                    
                    //$json= array('vendor_id' => $data['vendor_products'][0]['vendor_id'], 'registered_name'=>$data['vendor_products'][0]['registered_name'] ,'warehouse_id'=>$data['vendor_warehouse'][0]['id']);	
                }
                else
                {
                    $json= array('vendor_id' => null, 'registered_name'=>null ,'warehouse_id'=>null);	
                }            
                $jsonstring = json_encode($json);
                    echo $jsonstring;
            } 
        }
        public function select_vendor($slct_vdr_id,$equipment)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
            if($_SERVER['REQUEST_METHOD']=='GET')
            {
                $vendor_warehouse = DB::select("SELECT * FROM vendor_products,vendor_warehouse WHERE vendor_products.product_id = $equipment AND vendor_products.vendor_id AND vendor_products.warehouse_id = vendor_warehouse.id");
                $data['vendor_warehouse'] = json_decode(json_encode($vendor_warehouse),true);
                $jsonstring = json_encode($data['vendor_warehouse']);
                echo $jsonstring;
            }
            // if($_SERVER['REQUEST_METHOD']=='GET')
            // {
            //     //echo $equipment;
            //     $vendor_products = DB::select("SELECT * FROM vendor_products WHERE vendor_id = '$slct_vdr_id' and product_id ='$equipment'");
            //     $data['vendor_products'] = json_decode(json_encode($vendor_products), true);            
            //     // print_r($data['vendor_products']);
            //     if(isset($data['vendor_products']) && $data['vendor_products'] != NULL)
            //     {
            //         $warehouse_id = $data['vendor_products'][0]['warehouse_id'];                
            //         $vendor_warehouse = DB::select("SELECT * FROM vendor_warehouse WHERE id = $warehouse_id");
            //         $data['vendor_warehouse'] = json_decode(json_encode($vendor_warehouse), true);
                    
            //         $json= array('id' => $data['vendor_products'][0]['id'] , 'vendor_id' => $data['vendor_products'][0]['vendor_id'], 'product_id'=>$data['vendor_products'][0]['product_id'] , 'product_details' => $data['vendor_products'][0]['product_brand'] , 'product_price' => $data['vendor_products'][0]['product_rent_approved'], 'warehouse_details' => $data['vendor_warehouse'][0]['wh_landmark'].','.$data['vendor_warehouse'][0]['wh_city'],'warehouse_id'=>$data['vendor_warehouse'][0]['id'], 'virtual_id' => $data['vendor_products'][0]['virtual_id']);	
            //     }
            //     else
            //     {
            //         $json= array('id' => null , 'vendor_id' => null, 'product_id'=>null , 'product_details' => null , 'product_price' => null, 'warehouse_details' => null,'warehouse_id'=>null, 'virtual_id' => null);	
            //     }            
            //     $jsonstring = json_encode($json);
            //     echo $jsonstring;
            // } 
        }
        //-------------------added new select vendor controller----------------//
        public function get_vendor()
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
            
            $eq_id =  json_decode($_POST['equipments']);
            $assign = $_POST['assign'];
            $count = 0;
            $data_count = count($eq_id);
            $data_count = $data_count-1;
            $vdr = array();
            for ($i=0; $i <$data_count ; $i++) 
            { 
                $equip = $eq_id[$i];
                $vendor_id = DB::select("SELECT vendor_id FROM vendor_products WHERE product_id=$equip");
                $data['vendor'.$i] = json_decode(json_encode($vendor_id), true); 
                $count = count($data['vendor'.$i]);
                $temp_array = $data['vendor'.$i];
                for($j=0; $j<$count; $j++)
                {
                    $data['vendor_dummy'][$i][$j] = $temp_array[$j]['vendor_id'];
                }
                array_push($vdr,$data['vendor_dummy'][$i]);
            }
            $temp_array_1 =array();
            for($i=0; $i<count($vdr); $i++)
            {
                $temp_array_1= array_merge($temp_array_1,$vdr[$i]);
            }
            $result_values = array_count_values($temp_array_1);
            $result_keys = array_keys($result_values,"$data_count");
            $vdr_details = DB::select('SELECT id,registered_name FROM vendor_details WHERE id IN (' . implode(',', array_map('intval', $result_keys)) . ') ' );
            $jsonstring = json_encode($vdr_details);
            echo $jsonstring;
        }
        public function select_vendor_all()
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
            $equipments = request()->get('equipments');
            $vendor_id = request()->get('vendor_id');
            $equipments = json_decode($equipments);
            $count = count($equipments);
            $equipment_details = array();
            $equipment_price = array();
            $equipment_warehouse = array();
            $vendor_warehouse_id = array();
            for($i=0; $i<$count-1; $i++)
            {
                $product = $equipments[$i];
                $vendor_products = DB::select("SELECT * FROM vendor_products WHERE vendor_id = $vendor_id AND product_id= '$product' ");
                $data['vendor_products'] = json_decode(json_encode($vendor_products), true);
                $warehouse_id = $data['vendor_products'][0]['warehouse_id'];
                $vendor_warehouse = DB::select("SELECT * FROM vendor_warehouse WHERE id = $warehouse_id");
                $data['vendor_warehouse'] = json_decode(json_encode($vendor_warehouse), true);
                $vendor_product_id[$i] = $data['vendor_products'][0]['id'];
                $equipment_price[$i] = $data['vendor_products'][0]['product_rent_approved'];
                $equipment_details[$i] = $data['vendor_products'][0]['product_brand'];
                $equipment_warehouse[$i] = $data['vendor_warehouse'][0]['wh_landmark'].','.$data['vendor_warehouse'][0]['wh_city'];
                $vendor_warehouse_id[$i] = $data['vendor_warehouse'][0]['id'];
            }
            $data['vendor_product_details']['vendor_product_id']=$vendor_product_id;
            $data['vendor_product_details']['product_rent']=$equipment_price;
            $data['vendor_product_details']['product_details']=$equipment_details;
            $data['vendor_product_details']['warehouse_details']=$equipment_warehouse;
            $data['vendor_product_details']['warehouse_id']=$vendor_warehouse_id;
            $jsonstring = json_encode($data['vendor_product_details']);
            echo $jsonstring;
        }
        // Code not in use now from this function ------->
        public function generate_order(Request $request){
            DB::beginTransaction();
            try{
                $customer_details = DB::table('customer_details')->where('cust_id',$request->get('customer_id'))->first();
                $leads = DB::table('leads')->where('id',$request->get('lead_id'))->first();
                $total_amt=array_sum($request->get('offered_rent'))+array_Sum($request->get('deposite'))+array_Sum($request->get('transport'));$product_rent = $_POST['offered_rent'];
                $product_rent_total = $request->get('offered_rent_total');
                $product_deposite = $request->get('deposite');
                $transport = $request->get('transport');
                $products = $request->get('req_eq_hidden');
                $qty = $request->get('eq_quantity_hidden');
                $vendors = $request->get('vendors');
                $warehouses = $request->get('warehouses');
                $brands = $request->get('brands');
                $inventories = $request->get('inventories');
                $months = $request->get('months');
                $serial_numbers = $request->get('serial_numbers');
                $sale_rental_hidden = $request->get('sale_rental_hidden');
                $created_at = date('Y-m-d H:i:s', strtotime('now'));
                $pickup_date = date('Y-m-d',strtotime("+1 month"));
                $username = session('username');
                $is_upgraded = $request->get('upgraded_hidden');
                $text_upgraded = $request->get('upgraded_text');
                $sale_serial_no = $request->get('sale_serial_no');
                $sale_warranty = $request->get('sale_warranty');
                $order_details_ids = array();
                foreach($vendors as $key=>$vendor){
                    $series = 1;
                    if($request->get('sale_rental_hidden')[$key] == 'Rental'){
                        if($inventories[$key] == 'AG'){
                            if($brands[$key] == 'unknown'){
                                if(DB::table('product_brands')->where('product_id',$products[$key])->where('brand_name',$brands[$key])->exists()){
                                    $brand_id = DB::table('product_brands')->where('product_id',$products[$key])->where('brand_name',$brands[$key])->first()->id;
                                }
                                else{
                                    $brand_id = DB::table('product_brands')->insertGetId(['product_id'=>$products[$key],'brand_name'=>$brands[$key]]);
                                }
                                if(DB::table('vendor_products')->where('vendor_id',$vendor)->where('warehouse_id',$warehouses[$key])->where('product_brand',$brand_id)->exists()){
                                    $vendor_product_id = DB::table('vendor_products')->where('vendor_id',$vendor)->where('warehouse_id',$warehouses[$key])->where('product_brand',$brand_id)->first()->id;
                                }else{
                                    $vendor_details = DB::table('vendor_details')->where('id',$vendor)->first();
                                    $batchid = substr($vendor_details->of_city,0,1).$vendor_details->vendor_code.DB::table('products')->where('id',$products[$key])->first()->product_code.date('my');
                                    $vendor_product_id = DB::table('vendor_products')->insertGetId(
                                        [
                                            'vendor_id'=>$vendor,
                                            'product_id'=>$products[$key],
                                            'product_quantity'=>1,
                                            'product_brand'=>$brand_id,
                                            'product_rent_approved'=>0,
                                            'product_rent_requested'=>0,
                                            'product_deposite'=>0,
                                            'warehouse_id'=>$warehouses[$key],
                                            'status'=>'Approved',
                                            'batch'=>$batchid,
                                        ]
                                    );
                                    $batchid = $batchid."0";
                                    $insertedid = DB::table('vendor_product_details')->insertGetId(
                                        [
                                            'vendor_products_id'=>$vendor_product_id,
                                            'availability_status'=>0,
                                            'inventory_id'=>$batchid,
                                            'inventory_type'=>0,
                                            'current_location'=>2,
                                            'warehouse_id'=>$warehouses[$key],
                                            'additional_dateils'=>'Initial Product',
                                            'created_by'=>session('username')
                                        ]
                                    );
                                }
                            }
                            else{
                                $brand_id = $brands[$key];
                                // echo $vendor." ".$warehouses[$key]." ".$brands[$key];
                                // dd($vendor,$warehouses[$key],$brands[$key]);
                                $vendor_product_id = DB::table('vendor_products')->where('vendor_id',$vendor)->where('warehouse_id',$warehouses[$key])->where('product_brand',$brands[$key])->first()->id;
                            }
                            // dd(DB::table('vendor_product_details')->join('vendor_products','vendor_products.id','=','vendor_product_details.vendor_products_id')->join('vendor_details','vendor_details.id','=','vendor_products.vendor_id')->select('vendor_product_details.series')->where('vendor_products.vendor_id',$vendor)->toSql(),$vendor);
                            if(DB::table('vendor_product_details')->join('vendor_products','vendor_products.id','=','vendor_product_details.vendor_products_id')->join('vendor_details','vendor_details.id','=','vendor_products.vendor_id')->select('vendor_product_details.series')->where('vendor_products.vendor_id',$vendor)->exists()){
                                $series = DB::table('vendor_product_details')->join('vendor_products','vendor_products.id','=','vendor_product_details.vendor_products_id')->join('vendor_details','vendor_details.id','=','vendor_products.vendor_id')->select('vendor_product_details.series')->where('vendor_products.vendor_id',$vendor)->orderBy('vendor_product_details.id','DESC')->first()->series;
                                // dd($series);
                            }
                            $series = $series + 1;
                            // dd($series);

                            $batchid = substr($customer_details->city,0,1).DB::table('vendor_details')->where('id',$vendor)->first()->vendor_code.DB::table('products')->where('id',$products[$key])->first()->product_code.date('my',strtotime($leads->converted_at)).($series+1);
                            $vendor_product_details_id = DB::table('vendor_product_details')->insertGetId(
                                [
                                    'vendor_products_id'=>$vendor_product_id,
                                    'availability_status'=>1,
                                    'inventory_id'=>$batchid,
                                    'inventory_type'=>0,
                                    'current_location'=>0,
                                    'warehouse_id'=>$warehouses[$key],
                                    'additional_dateils'=>null,
                                    'series'=>$series,
                                    'created_by'=>session('username')
                                ]
                            );
                        }else{
                            $vendor_product_details = DB::table('vendor_product_details')->where('id',$request->get('inventories')[$key])->first();
                            $vendor_product_id = $vendor_product_details->vendor_products_id;
                            $vendor_product_details_id = $vendor_product_details->id;
                            // $series = 0;
                            $batchid = $vendor_product_details->inventory_id;
                            $brand_id = $request->get('brands')[$key];
                        }

                    }
                    else{
                        $brand_id = $request->get('brands')[$key];
                        $vendor_product_id = 0;
                        $vendor_product_details_id = 0;
                        $series = 0;
                        $batchid = 0;
                    }
                    // dd($batchid);
                    $month = $months[$key];
                    $order_details_id = DB::table('order_details')->insertGetId([
                        'order_id'=> 0,
                        'customer_id'=>$request->get('customer_id'),
                        'product_id'=>$request->get('req_eq_hidden')[$key],
                        'vendor_product_id'=>$vendor_product_id,
                        'vendor_id'=>$vendor,
                        'vendor_warehouse_id'=>$request->get('warehouses')[$key],
                        'product_brand'=>$brand_id,
                        'product_batch'=>$vendor_product_id,
                        'product_qty'=>1,
                        'months'=>$months[$key],
                        'product_rent'=>$request->get('offered_rent')[$key],
                        'product_deposite'=>$request->get('deposite')[$key],
                        'transport'=>$request->get('transport')[$key],
                        'sale_rental' =>$request->get('sale_rental_hidden')[$key],
                        'vendor_product_details_id' =>$vendor_product_details_id,
                        'unique_id'=>$batchid,
                        'product_serial_nos' =>$request->get('serial_numbers')[$key],
                        'creation_date'=>date('Y-m-d',strtotime($leads->converted_at)),
                        'pickup_date'=>date('Y-m-d',strtotime("+$month months",strtotime($leads->converted_at))),
                        'status'=>'Pending',
                        'upgraded'=>($request->get('upgraded_hidden')[$key]=='On')?'Yes':null,
                        'created_by'=>session('username'),
                    ]);
                    array_push($order_details_ids,$order_details_id);
                    DB::table('vendor_rented_inventory')->insert(
                        [
                            "vendor_id" => $vendor,
                            "order_id" => 0,
                            "order_details_id" => $order_details_id,
                            "inventory_id" => $vendor_product_details_id,
                            "vendor_product_id" => $vendor_product_id,
                            "rented_date" => date('Y-m-d',strtotime($leads->converted_at)),
                            "due_date" => date('Y-m-d',strtotime("+1 months",strtotime($leads->converted_at))),
                            "status" => 'live',
                            'type' => 'Delivery',
                            'created_by' => session('username')
                        ]
                    );
                }
                $groupedOrders = DB::table('order_details')->whereIn('id',$order_details_ids)->get()->groupBy('vendor_id');
                // dd($groupedOrders);
                foreach($groupedOrders as $key=>$order){
                    $total = array_sum($order->pluck('product_rent')->toArray())+array_sum($order->pluck('product_deposite')->toArray())+array_sum($order->pluck('transport')->toArray());
                    $fulldetails = $customer_details->customer_name." ".$customer_details->address_line_1." ".$customer_details->address_line_2." ".$customer_details->landmark." ".$customer_details->area." ".$customer_details->city."-".$customer_details->pincode;
                    // dd($order->pluck('product_id')->toArray());
                    $equipments = implode(', ',DB::table('products')->whereIn('id',$order->pluck('product_id')->toArray())->get()->pluck('product_name')->toArray());
                    // dd($equipments);
                    
                    $generatedOrderId = DB::table('del_orders')->insertGetId([
                        'status'=>'Pending',
                        'lead_id'=>$request->get('lead_id'),
                        'web_order_id'=>$leads->web_order_id,
                        'patient_name'=>$leads->patient_name,
                        'vendor_id'=>$key,
                        'deliverypickup'=>'Delivery',
                        'DelDate'=>date('d-m-Y',strtotime($leads->converted_at)),
                        'location'=>$customer_details->location,
                        'shipping_first_name'=>$customer_details->customer_name,
                        'cust_gender'=>$customer_details->cust_gender,
                        'TotalAmt'=>$total,
                        'PaymentMode'=>$leads->payment_mode,
                        'mobileno'=>$customer_details->primary_contact_no,
                        'DelAssignedTo'=>'Pending',
                        'TravelMode'=>'Null',
                        'order_approval_status'=>'Pending',
                        'comment'=>$leads->remark,
                        'fulldetails'=>$fulldetails,
                        'line_item_1'=>$equipments,
                    ]);
                    DB::table('order_details')->whereIn('id',$order->pluck('id')->toArray())->update(['order_id'=>$generatedOrderId]);
                    DB::table('vendor_rented_inventory')->whereIn('order_details_id',$order->pluck('id')->toArray())->update(['order_id'=>$generatedOrderId]);
                    
                    $leads_log_data = [
                        'log_lead_id' => $request->get('lead_id'),
                        'log_order_id' => $generatedOrderId,
                        'log_order_lead_date' => date('Y-m-d').' '.date('H:i:s'),
                        'log_order_type' => 'DO',
                        'log_lead_status' => 'Order Generated',
                        'log_date' => date('Y-m-d'),
                        'log_time' => date('H:i:s'),
                        'updated_by' => session('username')
                        ,'PARAM' => $key
                    ];
                }
                $invoice_no = DB::table('misc_table')->where('field','invoice_no')->first('value')->value;
                DB::table('leads')->where('id',$request->get('lead_id'))->update(['invoice_no'=>$invoice_no + 1]);

                DB::table('del_orders')->where('lead_id',$request->get('lead_id'))->update(['invoice_no'=>$invoice_no + 1]);

                DB::table('misc_table')->where('field','invoice_no')->update(['value'=>$invoice_no + 1]);
                DB::table('leads')->where('id',$request->get('lead_id'))->update(['lead_status'=>'Order Generated']);
                // dd("Commiting Stopped");
                DB::commit();
                return redirect('pending_for_vendor_approval')->with('message','Order Assign Succesfully')->with('approvevendor','pending_for_vendor_approval');
            }catch(Exception $ex){
                DB::rollBack();
                // dd($data);
                // dd($ex);
                $file = fopen(public_path().'/tempLogfile'.date('Y-m-d').'.txt','a');
                fwrite($file,date('Y-m-d')."Exception: ".$ex);
                // fwrite($file,"request_data".$request_dump);
                fclose($file);
                return redirect()->back()->with('error',$ex->getMessage());
            }
            // dd($request->all());
        }
        public function generate_orderOLD(Request $request)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
            if($_SERVER['REQUEST_METHOD']=='POST')
            {
                $request_dump = "";
                DB::beginTransaction();
                try
                {
                    $order_id_array = array();
                        //print_r($_POST);
                        if ($_POST['submit']=='submit') 
                        {
                            // $order_id_array = DB::transaction(function () 
                            // {
                                $del_orders = new DelOrders();
                                $order_details = new OrderDetails();
                                $sale_vendor_products = new sale_vendor_products();
                                $leads_log = new leads_log();

                                $total_amt=array_sum($_POST['offered_rent'])+array_Sum($_POST['deposite'])+array_Sum($_POST['transport']);
                                $customer_name = $_POST['customer_name'];
                                $customer_gender = $_POST['customer_gender'];
                                $customer_address = $_POST['customer_address'];
                                $customer_id = $_POST['customer_id'];
                                $lead_id = $_POST['lead_id'];
                                $getPatientName = DB::table('leads')->where('id',$lead_id)->first();
                                $location = $_POST['location'];
                                // $vendor_id = $_POST['vendor'];
                                $product_rent = $_POST['offered_rent'];
                                $product_rent_total = $_POST['offered_rent_total'];
                                $product_deposite = $_POST['deposite'];
                                $transport = $_POST['transport'];
                                $fulldetails = $customer_name.','.$customer_address;
                                $date = date('Y-m-d');
                                $DelDate= date('d-m-Y');
                                $temp_order_details = array();
                                $temp_array =array();
                                
                                $vendor_product_id = $_POST['vendor_product_id'];
                                // $vendor_product_details_id = $_POST['vendor_product_details_id'];
                                $vendor_product_details_id = $_POST['vendor_product_details_id'];
                                $equipment_id = $_POST['req_eq_hidden'];
                                $equipment_qty = $_POST['eq_quantity_hidden'];
                                $vendor_id = $_POST['vendors'];
                                $warehouse_id = $_POST['warehouses'];
                                $brand_id = $_POST['brands'];
                                $batch_id = $_POST['batches'];
                                $months = $_POST['months'];
                                $serial_numbers = $_POST['serial_numbers'];
                                $sale_rental_hidden = $_POST['sale_rental_hidden'];
                                $created_at = date('Y-m-d H:i:s', strtotime('now'));
                                $pickup_date = date('Y-m-d',strtotime("+1 month"));
                                $username = session('username');
                                $temp_vendor_id = $vendor_id[0];
                                $order_id_array = array();
                                $is_upgraded = $request->get('upgraded_hidden');
                                $text_upgraded = $request->get('upgraded_text');
                                if($_POST['assign']=='Individual')
                                {
                                    $prod_id_arr = array();
                                    for($i=0; $i<count($vendor_id); $i++)
                                    {
                                        
                                        for($l=0; $l<$equipment_qty[$i]; $l++)
                                        {
                                            if(in_array($equipment_id[$i],$prod_id_arr))
                                            {
                                                $temp_deposite = 0;
                                                $temp_transport = 0;
                                            }
                                            else
                                            {
                                                array_push($prod_id_arr,$equipment_id[$i]);
                                                $temp_transport = $transport[$i];
                                                $temp_deposite = $product_deposite[$i];
                                            }
                                            // if($l==0)
                                            // {
                                            //     $temp_transport = $transport[$i];
                                            //     $temp_deposite = $product_deposite[$i];
                                            // }
                                            // else
                                            // {
                                            //     $temp_deposite = 0;
                                            //     $temp_transport = 0;
                                            // }
                                            //$count = count($temp_order_details);
                                            if(in_array($vendor_id[$i], $temp_array, TRUE))
                                            {
                                                $same_vendor_index_i = 0;
                                                $same_vendor_index_j = 0;
                                                for ($j=0; $j<count($temp_order_details); $j++)
                                                {
                                                    for ($k=0; $k<count($temp_order_details[$j]); $k++)
                                                    {
                                                        if($temp_order_details[$j][$k]['vendor_id'] == $vendor_id[$i])
                                                        {
                                                            $same_vendor_index_j = $j;
                                                            $same_vendor_index_k = $k+1;
                                                        }
                                                    }
                                                }
                                                // $count_first = count($temp_order_details);
                                                // $count_minus = $count_first-1;
                                                // $count = count($temp_order_details[$count_minus]);
                                                //$count = 0;
                                                $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['vendor_id'] = $vendor_id[$i];
                                                $temp_total_amt = $temp_order_details[$same_vendor_index_j][$same_vendor_index_k-1]['total_amt'];
                                                $temp_total_amt=(int)$temp_total_amt+(int)($product_rent[$i]*$months[$i])+(int)$temp_deposite+(int)$temp_transport;
                                                // $temp_total_amt=(int)$temp_total_amt+(int)$product_rent_total[$i]+(int)$product_deposite[$i]+(int)$transport[$i];
                                                $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['total_amt'] = $temp_total_amt;
                                                $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['product_id'] = $equipment_id[$i];
                                                $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['vendor_product_id'] = $vendor_product_id[$i];
                                                $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['vendor_product_details_id'] = $vendor_product_details_id[$i];
                                                $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['brand_id'] = $brand_id[$i];
                                                $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['months'] = $months[$i];
                                                if($sale_rental_hidden[$i] == "Sale")
                                                {
                                                    $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['batch_id'] = "0";
                                                    $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['upgraded'] = null;
                                                }
                                                else
                                                {
                                                    if($is_upgraded[$i] == "On")
                                                    {
                                                        $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['upgraded'] = $text_upgraded[$i];
                                                        // if($temp_order_details[$same_vendor_index_j][0]['isUpgraded'] == false)
                                                        // {
                                                        //     $temp_order_details[$same_vendor_index_j][0]['isUpgraded'] = true;
                                                        // }
                                                    }
                                                    else
                                                    {
                                                        $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['upgraded'] = null;
                                                        // if($temp_order_details[$same_vendor_index_j][0]['isUpgraded'] != true)
                                                        // {
                                                        //     $temp_order_details[$same_vendor_index_j][0]['isUpgraded'] = false;
                                                        // }
                                                    }
                                                    $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['batch_id'] = $batch_id[$i];
                                                }
                                                // $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['batch_id'] = $batch_id[$i];
                                                $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['vendor_warehouse_id'] = $warehouse_id[$i];
                                                // $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['product_qty'] = $equipment_qty[$i];
                                                $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['product_qty'] = 1;
                                                $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['product_rent'] = $product_rent[$i];
                                                // $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['product_rent'] = $product_rent_total[$i];
                                                $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['product_deposite'] = $temp_deposite;
                                                $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['serial_numbers'] = $serial_numbers[$i];
                                                $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['transport'] = $temp_transport;
                                                $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['sale_rental_hidden'] = $sale_rental_hidden[$i];
                                            }
                                            else
                                            {
                                                $temp_total_amt = 0;
                                                array_push($temp_array,$vendor_id[$i]);
                                                //array_push($temp_order_details, $vendor_id[$i]);
                                                $temp_vendor_id = $vendor_id[$i];
                                                if(isset($temp_order_details[$temp_vendor_id]))
                                                {
                                                    $count = count($temp_order_details[$temp_vendor_id]);
                                                }
                                                else
                                                {
                                                    $count = 0;
                                                }
                                                $count_first = count($temp_order_details);
                                                $temp_order_details[$count_first][$count]['vendor_id'] = $vendor_id[$i];
                                                // $temp_total_amt=(int)$product_rent_total[$i]+(int)$product_deposite[$i]+(int)$transport[$i];
                                                $temp_total_amt=(int)($product_rent[$i]*$months[$i])+(int)$temp_deposite+(int)$temp_transport;
                                                $temp_order_details[$count_first][$count]['total_amt'] = $temp_total_amt;
                                                $temp_order_details[$count_first][$count]['product_id'] = $equipment_id[$i];
                                                $temp_order_details[$count_first][$count]['vendor_product_id'] = $vendor_product_id[$i];
                                                $temp_order_details[$count_first][$count]['vendor_product_details_id'] = $vendor_product_details_id[$i];
                                                $temp_order_details[$count_first][$count]['brand_id'] = $brand_id[$i];
                                                $temp_order_details[$count_first][$count]['months'] = $months[$i];
                                                if($sale_rental_hidden[$i] == "Sale")
                                                {
                                                    $temp_order_details[$count_first][$count]['batch_id'] = "0";
                                                    $temp_order_details[$count_first][$count]['upgraded'] = null;
                                                }
                                                else
                                                {
                                                    if($is_upgraded[$i] == "On")
                                                    {
                                                        $temp_order_details[$count_first][$count]['upgraded'] = $text_upgraded[$i];
                                                        // $temp_order_details[$count_first][0]['isUpgraded'] = true;
                                                        // if($temp_order_details[$count_first][0]['isUpgraded'] == false)
                                                        // {
                                                        //     $temp_order_details[$count_first][0]['isUpgraded'] = true;
                                                        // }
                                                    }
                                                    else
                                                    {
                                                        $temp_order_details[$count_first][$count]['upgraded'] = null;
                                                        // $temp_order_details[$count_first][0]['isUpgraded'] = false;
                                                    }
                                                    $temp_order_details[$count_first][$count]['batch_id'] = $batch_id[$i];
                                                }
                                                // $temp_order_details[$count_first][$count]['batch_id'] = $batch_id[$i];
                                                $temp_order_details[$count_first][$count]['vendor_warehouse_id'] = $warehouse_id[$i];
                                                // $temp_order_details[$count_first][2]['vendor_product_details_id'] = null;
                                                // $temp_order_details[$count_first][$count]['product_qty'] = $equipment_qty[$i];
                                                $temp_order_details[$count_first][$count]['product_qty'] = 1;
                                                $temp_order_details[$count_first][$count]['product_rent'] = $product_rent[$i];
                                                // $temp_order_details[$count_first][$count]['product_rent'] = $product_rent_total[$i];
                                                $temp_order_details[$count_first][$count]['product_deposite'] = $temp_deposite;
                                                $temp_order_details[$count_first][$count]['serial_numbers'] = $serial_numbers[$i];
                                                $temp_order_details[$count_first][$count]['transport'] = $temp_transport;
                                                $temp_order_details[$count_first][$count]['sale_rental_hidden'] = $sale_rental_hidden[$i];
                                            }
                                        }
                                    }
                                    // dd($temp_order_details);
                                    //print_r($temp_order_details);
                                    $leads_log_data = [
                                        'log_lead_id' => $lead_id,
                                        'log_lead_status' => 'Vendor Assigned',
                                        'log_date' => date('Y-m-d'),
                                        'log_time' => date('H:i:s'),
                                        'updated_by' => session('username')
                                    ];
                                    /**/ $leads_log->insert($leads_log_data);
                                    foreach ($temp_order_details as $key=>$temp_order_detail)
                                    {
                                        $equipment_names = array();
                                        foreach($temp_order_detail as $order_detail)
                                        {
                                            $product_id = $order_detail['product_id'];
                                            $equipment_name = DB::select("SELECT product_name FROM products WHERE id = $product_id");
                                            $data['equipment_name'] = json_decode(json_encode($equipment_name), true);
                                            array_push($equipment_names,$data['equipment_name'][0]['product_name']);
                                        }
                                        $count = count($temp_order_detail);
                                        $equip_name = implode(', ',$equipment_names);
                                        $generate_order = [
                                            'status'=>'Pending',
                                            'lead_id'=>$lead_id,
                                            'web_order_id'=>$getPatientName->web_order_id,
                                            'patient_name'=>$getPatientName->patient_name,
                                            'vendor_id'=>$temp_order_detail[0]['vendor_id'],
                                            'deliverypickup'=>'Delivery',
                                            'DelDate'=>$DelDate,
                                            'location'=>$location,
                                            'shipping_first_name'=>$customer_name,
                                            'cust_gender'=>$customer_gender,
                                            'TotalAmt'=>$temp_order_detail[$count-1]['total_amt'],
                                            'PaymentMode'=>request()->get('payment_mode'),
                                            'mobileno'=>request()->get('mobile_no'),
                                            'DelAssignedTo'=>'Pending',
                                            'TravelMode'=>'Null',
                                            'order_approval_status'=>'Pending',
                                            'comment'=>$getPatientName->remark,
                                            'fulldetails'=>$fulldetails,
                                            'line_item_1'=>$equip_name,
                                            // 'isUpgraded'=>$temp_order_detail[0]['isUpgraded']
                                        ];
                                        //print_r($generate_order); 
                                        /**/ $order_id = $del_orders->insertGetId($generate_order);
                                        $leads_log_data = [
                                            'log_lead_id' => $lead_id,
                                            'log_order_id' => $order_id,
                                            'log_order_lead_date' => date('Y-m-d').' '.date('H:i:s'),
                                            'log_order_type' => 'DO',
                                            'log_lead_status' => 'Order Generated',
                                            'log_date' => date('Y-m-d'),
                                            'log_time' => date('H:i:s'),
                                            'updated_by' => session('username')
                                            ,'PARAM' => $temp_order_detail[0]['vendor_id']
                                        ];
                                        $leads_log->insert($leads_log_data);
                                        if(DB::table('customer_details')->select('email_id','customer_name')->where('cust_id',$customer_id)->where('cust_source','B2B')->exists())
                                        {
                                            // $customer_details = DB::table('customer_details')->select('created_by','customer_name')->where('cust_id',$customer_id)->get()->toArray();
                                            // // dd($customer_details);
                                            // $user_details = DB::table('user')->select('id','username','email_id_user')->where('username',$customer_details[0]->created_by)->get()->toArray();
                                            $customer_details = DB::table('customer_details')->select('cust_owner','customer_name')->where('cust_id',$customer_id)->get()->toArray();
                                            $user_details = DB::table('user')->select('id','username','email_id_user')->where('id',$customer_details[0]->cust_owner)->get()->toArray();
                                            // dd($user_details);
                                            $orderTypeNotIn = config('app.order_type');
                                            DelOrders::where('order_id',$order_id)->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)->update(['order_owner'=>$user_details[0]->id]);
                                            $user_email = $user_details[0]->email_id_user;
                                            $user_name = $customer_details[0]->customer_name;
                                            // $user_email = session('email_id');
                                            $title = 'Order in process for '.$user_name;
                                            $message = ['message1'=>'Order processed Successfully for '.$user_name.'.'];
                                            DeliveryController::sendMailAlertUser($user_email,$user_name,$title,$message);
                                            $admin_email = 'abhishekn@quali55care.com';
                                            $title = 'Order in process from '.session('username').'.';
                                            $message = ['message1'=>'Check orders details page for more details about order.'];
                                            DeliveryController::sendMailAlertAdmin($admin_email,$user_name,$title,$message);
                                        }
                                        // /**/ $del_orders->insert($generate_order);
                                        // $order_id = DB::table('del_orders')->select('del_orders.order_id')->orderBy('del_orders.order_id','DESC')->first();
                                        // $order_id = $order_id->order_id;
                                        // $order_id = DB::table('del_orders')->insertGetId($generate_order);
                                        // $order_id = 0;
                                        
                                        for ($i=0; $i <count($temp_order_detail) ; $i++)
                                        {
                                            $temp_vendor_product_id = $temp_order_detail[$i]['vendor_product_id'];
                                            $temp_vendor_product_details_id = $temp_order_detail[$i]['vendor_product_details_id'];
                                            $temp_eq_id = $temp_order_detail[$i]['product_id'];
                                            $temp_eq_qty = $temp_order_detail[$i]['product_qty'];
                                            $temp_vendor_id = $temp_order_detail[$i]['vendor_id'];
                                            $temp_warehouse_id = $temp_order_detail[$i]['vendor_warehouse_id'];
                                            $temp_product_rent = $temp_order_detail[$i]['product_rent'];
                                            $temp_product_brand = $temp_order_detail[$i]['brand_id'];
                                            $temp_product_batch = $temp_order_detail[$i]['batch_id'];
                                            $temp_months = $temp_order_detail[$i]['months'];
                                            $temp_product_deposite = $temp_order_detail[$i]['product_deposite'];
                                            $temp_serial_numbers = $temp_order_detail[$i]['serial_numbers'];
                                            $temp_transport = $temp_order_detail[$i]['transport'];
                                            $temp_sale_rental_hidden = $temp_order_detail[$i]['sale_rental_hidden'];
                                            $upgraded = $temp_order_detail[$i]['upgraded'];
                                            $request_dump = "temp_vendor_product_id: ".$temp_vendor_product_id.
                                                "\n temp_vendor_product_details_id: ".$temp_vendor_product_details_id.
                                                "\n temp_eq_id: ".$temp_eq_id.
                                                "\n temp_eq_qty: ".$temp_eq_qty.
                                                "\n temp_vendor_id: ".$temp_vendor_id.
                                                "\n temp_warehouse_id: ".$temp_warehouse_id.
                                                "\n temp_product_rent: ".$temp_product_rent.
                                                "\n temp_product_brand: ".$temp_product_brand.
                                                "\n temp_product_batch: ".$temp_product_batch.
                                                "\n temp_product_deposite: ".$temp_product_deposite.
                                                "\n temp_serial_numbers: ".$temp_serial_numbers.
                                                "\n temp_transport: ".$temp_transport.
                                                "\n temp_sale_rental_hidden: ".$temp_sale_rental_hidden;
                                            if(in_array($order_id,$order_id_array))
                                            {
            
                                            }
                                            else
                                            {
                                                array_push($order_id_array,$order_id);
                                            }
                                            if($temp_sale_rental_hidden == "Sale")
                                            {
                                                $status = "Pending";
                                                $insertData = 
                                                [
                                                    'order_id'=> $order_id,
                                                    'vendor_id' => $temp_vendor_id,
                                                    'product_id' => $temp_eq_id,
                                                    'sale_price' => $temp_product_rent,
                                                    'vendor_sale_price' => 0,
                                                    'vendor_warehouse_id' => $temp_warehouse_id,
                                                    'created_by' => session('username')
                                                ];
                                                $inserted = $sale_vendor_products->insert($insertData);
                                            }
                                            else
                                            {
                                                $status = "Pending";
                                            }
                                            if($temp_sale_rental_hidden == "Sale")
                                            {
                                                $temp_vendor_product_id = 0;
                                                
                                            }
                                            if($temp_sale_rental_hidden == 'Rental')
                                            {
                                                $insertData = 
                                                [
                                                    'order_id'=> $order_id,
                                                    'vendor_id' => $temp_vendor_id,
                                                    'product_id' => $temp_eq_id,
                                                    'sale_price' => $temp_product_rent,
                                                    'vendor_sale_price' => 0,
                                                    'vendor_warehouse_id' => $temp_warehouse_id,
                                                    'created_by' => session('username')
                                                ];
                                                $inserted = $sale_vendor_products->insert($insertData);
                                                // DB::enableQueryLog();
                                                // $temp_vendor_product_details_id = null;
                                                $product_details = DB::select("SELECT * FROM vendor_product_details WHERE id = $temp_vendor_product_details_id");
                                                // dd(DB::getQueryLog());
                                                $product_details = json_decode(json_encode($product_details), true);
                                                // print_r($product_details);
            
                                                $vendor_product_details_id = $product_details[0]['id'];
                                                $inventory_id = $product_details[0]['inventory_id'];
                                            }
                                            else
                                            {
                                                $temp_vendor_product_details_id = 0;
                                                $vendor_product_details_id = 0;
                                                $inventory_id = 0;
                                            }
                                            $dt = Carbon::now();
                                            $pickup_date = $dt->addMonths($temp_months);
                                            $product_rent_ins = $temp_product_rent;
                                            $insert_order = [
                                                'order_id'=> $order_id,
                                                'customer_id'=>$customer_id,
                                                'product_id'=>$temp_eq_id,
                                                'vendor_product_id'=>$temp_vendor_product_id,
                                                'vendor_id'=>$temp_vendor_id,
                                                'vendor_warehouse_id'=>$temp_warehouse_id,
                                                'product_brand'=>$temp_product_brand,
                                                'product_batch'=>$temp_product_batch,
                                                'product_qty'=>$temp_eq_qty,
                                                'months'=>$temp_months,
                                                'product_rent'=>$product_rent_ins,
                                                'product_deposite'=>$temp_product_deposite,
                                                'transport'=>$temp_transport,
                                                'sale_rental' =>$temp_sale_rental_hidden,
                                                'vendor_product_details_id' => $temp_vendor_product_details_id,
                                                'unique_id'=> $inventory_id,
                                                'product_serial_nos' =>$temp_serial_numbers,
                                                'creation_date'=>$date,
                                                'pickup_date'=>$pickup_date,
                                                'status'=>$status,
                                                'upgraded'=>$upgraded,
                                                'created_at'=>$created_at,
                                                'created_by'=>$username,  
                                            ];
                                            $update_inventory_status = DB::update("UPDATE vendor_product_details SET availability_status = 1, current_location = 0 WHERE id = $vendor_product_details_id");
                                            //print_r($insert_order);
                                            /**/ $order_details->insert($insert_order);
                                            
                                            // **********$update_qty = DB::update("UPDATE vendor_products SET product_quantity = product_quantity-$temp_eq_qty WHERE id=$temp_vendor_product_id");
                                            
                                            /**/ $update = DB::update("UPDATE leads SET lead_status = 'Vendor Assigned' WHERE id = $lead_id");
                                            // $leads_log = new leads_log();
                                            // $leads_log_data = [
                                            //     'log_lead_id' => $lead_id,
                                            //     'log_lead_status' => 'Vendor Assigned',
                                            //     'log_date' => date('Y-m-d'),
                                            //     'log_time' => date('H:i:s'),
                                            //     'updated_by' => session('username')
                                            // ];
                                            // $leads_log->insert($leads_log_data);
                                            // $temp_vendor_id = null;
                                            // if($key == 1)
                                            // {
                                            //     $temp_vendor_id = null;
                                            // }
                                            $vendor_details = DB::select("SELECT * FROM vendor_details WHERE id=$temp_vendor_id");
                                            $data['vendor_details'] = json_decode(json_encode($vendor_details),true);
                                            $products = DB::select("SELECT * FROM products WHERE id=$temp_eq_id");
                                            $data['products'] = json_decode(json_encode($products),true);
                                            // session(['email' => $data['vendor_details'][0]['of_email']]);
                                            // $data = array('name'=>"'".$data['vendor_details'][0]['registered_name']."'",'product_name'=>"'".$data['products'][0]['product_name']."'");
                                            // Mail::send('vendorMail/pendingRentedProductMail', $data, function($message) 
                                            // {                
                                            //     $email_id = session('email');
                                            //     $message->to($email_id, 'Product Rental Request')->subject('Product Rental Request');
                                            //     $message->from('tempmailquali@gmail.com', 'Quali55Care');
                                            // });
                                        }
                                    }
                                }
                                else
                                {
                                    $generate_order = [
                                        'status'=>'Pending',
                                        'lead_id'=>$lead_id,
                                        'patient_name'=>$getPatientName->patient_name,
                                        'vendor_id'=>$vendor_id[0],
                                        'deliverypickup'=>'Delivery',
                                        'DelDate'=>$DelDate,
                                        'shipping_first_name'=>$customer_name,
                                        'TotalAmt'=>$total_amt,
                                        'PaymentMode'=>request()->get('payment_mode'),
                                        'mobileno'=>request()->get('mobile_no'),
                                        'DelAssignedTo'=>'Pending',
                                        'TravelMode'=>'Null',
                                        'order_approval_status'=>'Pending',
                                        'fulldetails'=>$fulldetails,
                                    ]; 
                                    //print_r($generate_order); 
                                    $order_id = $del_orders->insertGetId($generate_order);
                                    
                                    array_push($order_id_array,$order_id);
                                    $update = DB::update("UPDATE leads SET lead_status = 'Vendor Assigned' WHERE id = $lead_id");
                                    $leads_log = new leads_log();
                                    $leads_log_data = [
                                        'log_lead_id' => $lead_id,
                                        'log_lead_status' => 'Vendor Assignedxxxx',
                                        'log_date' => date('Y-m-d'),
                                        'log_time' => date('H:i:s'),
                                        'updated_by' => session('username')
                                       // ,'PARAM' => 'abcd'
                                    ];
                                    $leads_log->insert($leads_log_data);
                                    $leads_log_data = [
                                        'log_lead_id' => $lead_id,
                                        'log_lead_status' => 'Order Generated',
                                        'log_order_lead_date' => date('Y-m-d').' '.date('H:i:s'),
                                        'log_order_id' => $order_id,
                                        'log_order_type' => 'DO',
                                        'log_date' => date('Y-m-d'),
                                        'log_time' => date('H:i:s'),
                                        'updated_by' => session('username')
                                    ];
                                    $leads_log->insert($leads_log_data);
                                    if(DB::table('customer_details')->select('email_id','customer_name')->where('cust_id',$customer_id)->where('cust_source','B2B')->exists())
                                    {
                                        $customer_details = DB::table('customer_details')->select('created_by','customer_name')->where('cust_id',$customer_id)->get()->toArray();
                                        $user_details = DB::table('user')->select('username','email_id_user')->where('username',$customer_details[0]->created_by)->get()->toArray();
                                        $user_email = $user_details[0]->email_id_user;
                                        $user_name = $customer_details[0]->customer_name;
                                        $orderTypeNotIn = config('app.order_type');
                                        DelOrders::where('order_id',$order_id)->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)->update(['order_owner'=>$user_details[0]->id]);
                                        // $user_email = session('email_id');
                                        $title = 'Pickup Order Generated for '.$user_name;
                                        $message = ['message1'=>'Pickup Order Generated Successfully for '.$user_name.'.'];
                                        DeliveryController::sendMailAlertUser($user_email,$user_name,$title,$message);
                                        $admin_email = 'abhishekn@quali55care.com';
                                        $title = 'Pickup Order Generated from '.session('username').'.';
                                        $message = ['message1'=>'Check orders details page for more details about order.'];
                                        DeliveryController::sendMailAlertAdmin($admin_email,$user_name,$title,$message);
                                    }
                                    $vendor_product_id = $_POST['vendor_product_id'];
                                    $equipment_id = $_POST['req_eq_hidden'];
                                    $equipment_qty = $_POST['eq_quantity_hidden'];
                                    $vendor_id = $_POST['vendors'];
                                    $serial_numbers = $_POST['serial_numbers'];
                                    $warehouse_id = $_POST['warehouses'];
                                    $brand_id = $_POST['brands'];
                                    $batch_id = $_POST['batches'];
                                    $sale_rental_hidden = $_POST['sale_rental_hidden'];
                                    //echo "asdas".$_POST['deposite'];
                                    $date = date('Y-m-d H:i:m');
                                    $created_at = date( 'Y-m-d H:i:s', strtotime( 'now' ) );
                                    $pickup_date = date('Y-m-d',strtotime("+1 month"));
                                    $username = session('username');
                                    $temp_vendor_id = $vendor_id[0];
                                    $temp_warehouse_id = $warehouse_id[0];
                                    for ($i=0; $i <count($equipment_id) ; $i++)
                                    {
                                        $temp_vendor_product_id = $vendor_product_id[$i];
                                        $temp_eq_id = $equipment_id[$i];
                                        $temp_eq_qty = $equipment_qty[$i];
                                        $temp_vendor_id = $vendor_id[0];
                                        $temp_warehouse_id = $warehouse_id[0];
                                        if($_POST['assign']=='Individual')
                                        {
                                            $temp_vendor_id = $vendor_id[$i];
                                            $temp_warehouse_id = $warehouse_id[$i];
                                        }
                                        // $temp_warehouse_id = $warehouse_id[$i];
                                        $temp_product_rent = $product_rent[$i];
                                        $temp_product_brand = $brand_id[$i];
                                        $temp_product_batch = $batch_id[$i];
                                        $temp_product_deposite = $product_deposite[$i];
                                        $temp_serial_numbers = $serial_numbers[$i];
                                        $temp_transport = $transport[$i];
                                        $temp_sale_rental_hidden = $sale_rental_hidden[$i];
                                        $insert_order = [
                                            'order_id'=> $order_id,
                                            'customer_id'=>$customer_id,
                                            'product_id'=>$temp_eq_id,
                                            'vendor_product_id'=>$temp_vendor_product_id,
                                            'vendor_id'=>$temp_vendor_id,
                                            'vendor_warehouse_id'=>$temp_warehouse_id,
                                            'product_qty'=>$temp_eq_qty,
                                            'product_rent'=>$temp_product_rent,
                                            'product_brand'=>$temp_product_brand,
                                            'product_batch'=>$temp_product_batch,
                                            'product_deposite'=>$temp_product_deposite,
                                            'product_serial_nos'=>$temp_serial_numbers,
                                            'transport'=>$temp_transport,
                                            'sale_rental' => $temp_sale_rental_hidden,
                                            'creation_date'=>$date,
                                            'pickup_date'=>$pickup_date,
                                            'status'=>'Pending',
                                            'created_at'=>$created_at,
                                            'created_by'=>$username,  
                                        ];
                                        $order_details->insert($insert_order);
                                        $vendor_details = DB::select("SELECT * FROM vendor_details WHERE id=$temp_vendor_id");
                                        $data['vendor_details'] = json_decode(json_encode($vendor_details),true);
                                        $products = DB::select("SELECT * FROM products WHERE id=$temp_eq_id");
                                        $data['products'] = json_decode(json_encode($products),true);
                                        // session(['email' => $data['vendor_details'][0]['of_email']]);
                                        // $data = array('name'=>"'".$data['vendor_details'][0]['registered_name']."'",'product_name'=>"'".$data['products'][0]['product_name']."'");
                                        // Mail::send('vendorMail/pendingRentedProductMail', $data, function($message) 
                                        // {
                                        //     $email_id = session('email');
                                        //     $message->to($email_id, 'Product Rental Request')->subject('Product Rental Request');
                                        //     $message->from('tempmailquali@gmail.com', 'Quali55Care');
                                        // });
                                    }
                                }
                                //print_r($order_id_array);
                                //-------change lead status to order_generated---//
                                $lead_status = DB::update("UPDATE leads SET lead_status='Order Generated' WHERE id=$lead_id");
                                // $leads_log_data = [
                                //     'log_lead_id' => $lead_id,
                                //     'log_lead_status' => 'Order Generated',
                                //     'log_date' => date('Y-m-d'),
                                //     'log_time' => date('H:i:s'),
                                //     'updated_by' => session('username')
                                // ];
                                // $leads_log->insert($leads_log_data);
                            //     return $order_id_array;
                            // });
                            // dd($order_id_array);
                            //session(['order_id' => $order_id_array]);   
                            $order_id = json_encode($order_id_array);
                            $order_id = base64_encode($order_id);
                            DB::commit();
                            $invoice_no = DB::table('misc_table')->where('field','invoice_no')->first('value')->value;
                            DB::table('leads')->where('id',$lead_id)->update(['invoice_no'=>$invoice_no + 1]);

                            DB::table('del_orders')->where('lead_id',$lead_id)->update(['invoice_no'=>$invoice_no + 1]);

                            DB::table('misc_table')->where('field','invoice_no')->update(['value'=>$invoice_no + 1]);
                            // $this->generate_delivery_invoice($lead_id);
                            /**/ return redirect('/order_details/'.$order_id)->with('message','Order Assign Succesfully')->with('approvevendor','pending_for_vendor_approval');
                        }
                }
                catch (Exception $ex) 
                {
                    // $msg = $ex->getMessage();
                    // WebExceptionLog::insert([
                    //     'function'=>'generate_order',
                    //     'controller'=>'OrderController',
                    //     'exception'=>$msg,
                    //     'user'=>session('username')
                    // ]);
                    
                    DB::rollBack();
                    $file = fopen(public_path().'/tempLogfile'.date('Y-m-d').'.txt','a');
                    fwrite($file,date('Y-m-d')."Exception: ".$ex);
                    fwrite($file,"request_data".$request_dump);
                    fclose($file);
                    return redirect()->back()->with('message','Something Went Wrong! Please Try Again or Contact Administrator.');
                }
            }
        }
        public function generate_order1(Request $request)
        {
            if($_SERVER['REQUEST_METHOD']=='POST')
            {
                $request_dump = "";
                DB::beginTransaction();
                try
                {
                    $order_id_array = array();
                    if ($_POST['submit']=='submit') 
                    {
                        $del_orders = new DelOrders();
                        $order_details = new OrderDetails();
                        $sale_vendor_products = new sale_vendor_products();
                        $leads_log = new leads_log();

                        $total_amt=array_sum($_POST['offered_rent'])+array_Sum($_POST['deposite'])+array_Sum($_POST['transport']);
                        $customer_name = $_POST['customer_name'];
                        $customer_gender = $_POST['customer_gender'];
                        $customer_address = $_POST['customer_address'];
                        $customer_id = $_POST['customer_id'];
                        $lead_id = $_POST['lead_id'];
                        $getPatientName = DB::table('leads')->where('id',$lead_id)->first();
                        $location = $_POST['location'];
                        $product_rent = $_POST['offered_rent'];
                        $product_rent_total = $_POST['offered_rent_total'];
                        $product_deposite = $_POST['deposite'];
                        $transport = $_POST['transport'];
                        $fulldetails = $customer_name.','.$customer_address;
                        $date = date('Y-m-d');
                        $DelDate= date('d-m-Y');
                        $temp_order_details = array();
                        $temp_array =array();
                        
                        $vendor_product_id = $_POST['vendor_product_id'];
                        $vendor_product_details_id = $_POST['vendor_product_details_id'];
                        $equipment_id = $_POST['req_eq_hidden'];
                        $equipment_qty = $_POST['eq_quantity_hidden'];
                        $vendor_id = $_POST['vendors'];
                        $warehouse_id = $_POST['warehouses'];
                        $brand_id = $_POST['brands'];
                        $batch_id = $_POST['batches'];
                        $months = $_POST['months'];
                        $serial_numbers = $_POST['serial_numbers'];
                        $sale_rental_hidden = $_POST['sale_rental_hidden'];
                        $created_at = date('Y-m-d H:i:s', strtotime('now'));
                        $pickup_date = date('Y-m-d',strtotime("+1 month"));
                        $username = session('username');
                        $temp_vendor_id = $vendor_id[0];
                        $order_id_array = array();
                        $is_upgraded = $request->get('upgraded_hidden');
                        $text_upgraded = $request->get('upgraded_text');
                        $sale_serial_no = $request->get('sale_serial_no');
                        $sale_warranty = $request->get('sale_warranty');
                        if($_POST['assign']=='Individual')
                        {
                            $prod_id_arr = array();
                            for($i=0; $i<count($vendor_id); $i++)
                            {
                                for($l=0; $l<$equipment_qty[$i]; $l++)
                                {
                                    if(in_array($equipment_id[$i],$prod_id_arr))
                                    {
                                        $temp_deposite = 0;
                                        $temp_transport = 0;
                                    }
                                    else
                                    {
                                        array_push($prod_id_arr,$equipment_id[$i]);
                                        $temp_transport = $transport[$i];
                                        $temp_deposite = $product_deposite[$i];
                                    }
                                    if(in_array($vendor_id[$i], $temp_array, TRUE))
                                    {
                                        $same_vendor_index_i = 0;
                                        $same_vendor_index_j = 0;
                                        for ($j=0; $j<count($temp_order_details); $j++)
                                        {
                                            for ($k=0; $k<count($temp_order_details[$j]); $k++)
                                            {
                                                if($temp_order_details[$j][$k]['vendor_id'] == $vendor_id[$i])
                                                {
                                                    $same_vendor_index_j = $j;
                                                    $same_vendor_index_k = $k+1;
                                                }
                                            }
                                        }
                                        $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['vendor_id'] = $vendor_id[$i];
                                        $temp_total_amt = $temp_order_details[$same_vendor_index_j][$same_vendor_index_k-1]['total_amt'];
                                        $temp_total_amt=(int)$temp_total_amt+(int)$product_rent[$i]+(int)$temp_deposite+(int)$temp_transport;                                        
                                        $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['total_amt'] = $temp_total_amt;
                                        $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['product_id'] = $equipment_id[$i];
                                        $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['vendor_product_id'] = $vendor_product_id[$i];
                                        $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['vendor_product_details_id'] = $vendor_product_details_id[$i];
                                        $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['brand_id'] = $brand_id[$i];
                                        $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['months'] = $months[$i];
                                        if($sale_rental_hidden[$i] == "Sale")
                                        {
                                            $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['batch_id'] = "-";
                                            $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['upgraded'] = null;
                                        }
                                        else
                                        {
                                            if($is_upgraded[$i] == "On")
                                            {
                                                $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['upgraded'] = $text_upgraded[$i];
                                            }
                                            else
                                            {
                                                $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['upgraded'] = null;
                                            }
                                            $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['batch_id'] = $batch_id[$i];
                                        }                                        
                                        $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['vendor_warehouse_id'] = $warehouse_id[$i];                                        
                                        $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['product_qty'] = 1;
                                        $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['product_rent'] = $product_rent[$i];                                        
                                        $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['product_deposite'] = $temp_deposite;
                                        $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['serial_numbers'] = $serial_numbers[$i];
                                        $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['transport'] = $temp_transport;
                                        $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['sale_rental_hidden'] = $sale_rental_hidden[$i];
                                    }
                                    else
                                    {
                                        $temp_total_amt = 0;
                                        array_push($temp_array,$vendor_id[$i]);                                        
                                        $temp_vendor_id = $vendor_id[$i];
                                        if(isset($temp_order_details[$temp_vendor_id]))
                                        {
                                            $count = count($temp_order_details[$temp_vendor_id]);
                                        }
                                        else
                                        {
                                            $count = 0;
                                        }
                                        $count_first = count($temp_order_details);
                                        $temp_order_details[$count_first][$count]['vendor_id'] = $vendor_id[$i];                                        
                                        $temp_total_amt=(int)$product_rent[$i]+(int)$temp_deposite+(int)$temp_transport;
                                        $temp_order_details[$count_first][$count]['total_amt'] = $temp_total_amt;
                                        $temp_order_details[$count_first][$count]['product_id'] = $equipment_id[$i];
                                        $temp_order_details[$count_first][$count]['vendor_product_id'] = $vendor_product_id[$i];
                                        $temp_order_details[$count_first][$count]['vendor_product_details_id'] = $vendor_product_details_id[$i];
                                        $temp_order_details[$count_first][$count]['brand_id'] = $brand_id[$i];
                                        $temp_order_details[$count_first][$count]['months'] = $months[$i];
                                        if($sale_rental_hidden[$i] == "Sale")
                                        {
                                            $temp_order_details[$count_first][$count]['batch_id'] = "-";
                                            $temp_order_details[$count_first][$count]['upgraded'] = null;
                                        }
                                        else
                                        {
                                            if($is_upgraded[$i] == "On")
                                            {
                                                $temp_order_details[$count_first][$count]['upgraded'] = $text_upgraded[$i];                                                
                                            }
                                            else
                                            {
                                                $temp_order_details[$count_first][$count]['upgraded'] = null;                                                
                                            }
                                            $temp_order_details[$count_first][$count]['batch_id'] = $batch_id[$i];
                                        }                                        
                                        $temp_order_details[$count_first][$count]['vendor_warehouse_id'] = $warehouse_id[$i];                                        
                                        $temp_order_details[$count_first][$count]['product_qty'] = 1;
                                        $temp_order_details[$count_first][$count]['product_rent'] = $product_rent[$i];                                        
                                        $temp_order_details[$count_first][$count]['product_deposite'] = $temp_deposite;
                                        $temp_order_details[$count_first][$count]['serial_numbers'] = $serial_numbers[$i];
                                        $temp_order_details[$count_first][$count]['transport'] = $temp_transport;
                                        $temp_order_details[$count_first][$count]['sale_rental_hidden'] = $sale_rental_hidden[$i];
                                    }
                                }
                            }
                            $leads_log_data = [
                                'log_lead_id' => $lead_id,
                                'log_lead_status' => 'Vendor Assigned',
                                'log_date' => date('Y-m-d'),
                                'log_time' => date('H:i:s'),
                                'updated_by' => session('username')
                            ];
                            /**/ $leads_log->insert($leads_log_data);
                            foreach ($temp_order_details as $key=>$temp_order_detail)
                            {
                                $equipment_names = array();
                                foreach($temp_order_detail as $order_detail)
                                {
                                    $product_id = $order_detail['product_id'];
                                    $equipment_name = DB::select("SELECT product_name FROM products WHERE id = $product_id");
                                    $data['equipment_name'] = json_decode(json_encode($equipment_name), true);
                                    array_push($equipment_names,$data['equipment_name'][0]['product_name']);
                                }
                                $count = count($temp_order_detail);
                                $equip_name = implode(', ',$equipment_names);
                                $generate_order = [
                                    'status'=>'Pending',
                                    'lead_id'=>$lead_id,
                                    'web_order_id'=>$getPatientName->web_order_id,
                                    'patient_name'=>$getPatientName->patient_name,
                                    'vendor_id'=>$temp_order_detail[0]['vendor_id'],
                                    'deliverypickup'=>'Delivery',
                                    'DelDate'=>$DelDate,
                                    'location'=>$location,
                                    'shipping_first_name'=>$customer_name,
                                    'cust_gender'=>$customer_gender,
                                    'TotalAmt'=>$temp_order_detail[$count-1]['total_amt'],
                                    'PaymentMode'=>request()->get('payment_mode'),
                                    'mobileno'=>request()->get('mobile_no'),
                                    'DelAssignedTo'=>'Pending',
                                    'TravelMode'=>'Null',
                                    'order_approval_status'=>'Pending',
                                    'comment'=>$getPatientName->remark,
                                    'fulldetails'=>$fulldetails,
                                    'line_item_1'=>$equip_name,
                                ];
                                /**/ $order_id = $del_orders->insertGetId($generate_order);
                                $leads_log_data = [
                                    'log_lead_id' => $lead_id,
                                    'log_order_id' => $order_id,
                                    'log_order_lead_date' => date('Y-m-d').' '.date('H:i:s'),
                                    'log_order_type' => 'DO',
                                    'log_lead_status' => 'Order Generated',
                                    'log_date' => date('Y-m-d'),
                                    'log_time' => date('H:i:s'),
                                    'updated_by' => session('username')
                                    ,'PARAM' => $temp_order_detail[0]['vendor_id']
                                ];
                                $leads_log->insert($leads_log_data);
                                if(DB::table('customer_details')->select('email_id','customer_name')->where('cust_id',$customer_id)->where('cust_source','B2B')->exists())
                                {                                    
                                    $customer_details = DB::table('customer_details')->select('cust_owner','customer_name')->where('cust_id',$customer_id)->get()->toArray();
                                    $user_details = DB::table('user')->select('id','username','email_id_user')->where('id',$customer_details[0]->cust_owner)->get()->toArray();                                    
                                    $orderTypeNotIn = config('app.order_type');
                                    DelOrders::where('order_id',$order_id)->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)->update(['order_owner'=>$user_details[0]->id]);
                                    $user_email = $user_details[0]->email_id_user;
                                    $user_name = $customer_details[0]->customer_name;                                    
                                    $title = 'Order in process for '.$user_name;
                                    $message = ['message1'=>'Order processed Successfully for '.$user_name.'.'];
                                    DeliveryController::sendMailAlertUser($user_email,$user_name,$title,$message);
                                    $admin_email = 'abhishekn@quali55care.com';
                                    $title = 'Order in process from '.session('username').'.';
                                    $message = ['message1'=>'Check orders details page for more details about order.'];
                                    DeliveryController::sendMailAlertAdmin($admin_email,$user_name,$title,$message);
                                }
                                
                                for ($i=0; $i <count($temp_order_detail) ; $i++)
                                {
                                    $temp_vendor_product_id = $temp_order_detail[$i]['vendor_product_id'];
                                    $temp_vendor_product_details_id = $temp_order_detail[$i]['vendor_product_details_id'];
                                    $temp_eq_id = $temp_order_detail[$i]['product_id'];
                                    $temp_eq_qty = $temp_order_detail[$i]['product_qty'];
                                    $temp_vendor_id = $temp_order_detail[$i]['vendor_id'];
                                    $temp_warehouse_id = $temp_order_detail[$i]['vendor_warehouse_id'];
                                    $temp_product_rent = $temp_order_detail[$i]['product_rent'];
                                    $temp_product_brand = $temp_order_detail[$i]['brand_id'];
                                    $temp_product_batch = $temp_order_detail[$i]['batch_id'];
                                    $temp_months = $temp_order_detail[$i]['months'];
                                    $temp_product_deposite = $temp_order_detail[$i]['product_deposite'];
                                    $temp_serial_numbers = $temp_order_detail[$i]['serial_numbers'];
                                    $temp_transport = $temp_order_detail[$i]['transport'];
                                    $temp_sale_rental_hidden = $temp_order_detail[$i]['sale_rental_hidden'];
                                    $upgraded = $temp_order_detail[$i]['upgraded'];
                                    $request_dump = "temp_vendor_product_id: ".$temp_vendor_product_id.
                                        "\n temp_vendor_product_details_id: ".$temp_vendor_product_details_id.
                                        "\n temp_eq_id: ".$temp_eq_id.
                                        "\n temp_eq_qty: ".$temp_eq_qty.
                                        "\n temp_vendor_id: ".$temp_vendor_id.
                                        "\n temp_warehouse_id: ".$temp_warehouse_id.
                                        "\n temp_product_rent: ".$temp_product_rent.
                                        "\n temp_product_brand: ".$temp_product_brand.
                                        "\n temp_product_batch: ".$temp_product_batch.
                                        "\n temp_product_deposite: ".$temp_product_deposite.
                                        "\n temp_serial_numbers: ".$temp_serial_numbers.
                                        "\n temp_transport: ".$temp_transport.
                                        "\n temp_sale_rental_hidden: ".$temp_sale_rental_hidden;
                                    if(in_array($order_id,$order_id_array))
                                    {
    
                                    }
                                    else
                                    {
                                        array_push($order_id_array,$order_id);
                                    }
                                    if($temp_sale_rental_hidden == "Sale")
                                    {
                                        $status = "Pending";
                                        $insertData = 
                                        [
                                            'order_id'=> $order_id,
                                            'vendor_id' => $temp_vendor_id,
                                            'product_id' => $temp_eq_id,
                                            'sale_price' => $temp_product_rent,
                                            'vendor_sale_price' => 0,
                                            'vendor_warehouse_id' => $temp_warehouse_id,
                                            'created_by' => session('username')
                                        ];
                                        $inserted = $sale_vendor_products->insert($insertData);
                                    }
                                    else
                                    {
                                        $status = "Pending";
                                    }
                                    if($temp_sale_rental_hidden == "Sale")
                                    {
                                        $temp_vendor_product_id = 0;
                                        
                                    }
                                    if($temp_sale_rental_hidden == 'Rental')
                                    {
                                        $insertData = 
                                        [
                                            'order_id'=> $order_id,
                                            'vendor_id' => $temp_vendor_id,
                                            'product_id' => $temp_eq_id,
                                            'sale_price' => $temp_product_rent,
                                            'vendor_sale_price' => 0,
                                            'vendor_warehouse_id' => $temp_warehouse_id,
                                            'created_by' => session('username')
                                        ];
                                        $inserted = $sale_vendor_products->insert($insertData);
                                        $product_details = DB::select("SELECT * FROM vendor_product_details WHERE id = $temp_vendor_product_details_id");
                                        $product_details = json_decode(json_encode($product_details), true);
    
                                        $vendor_product_details_id = $product_details[0]['id'];
                                        $inventory_id = $product_details[0]['inventory_id'];
                                    }
                                    else
                                    {
                                        $temp_vendor_product_details_id = 0;
                                        $vendor_product_details_id = 0;
                                        $inventory_id = 0;
                                    }
                                    $dt = Carbon::now();
                                    $pickup_date = $dt->addMonths($temp_months);
                                    $product_rent_ins = $temp_product_rent * $temp_months;
                                    $insert_order = [
                                        'order_id'=> $order_id,
                                        'customer_id'=>$customer_id,
                                        'product_id'=>$temp_eq_id,
                                        'vendor_product_id'=>$temp_vendor_product_id,
                                        'vendor_id'=>$temp_vendor_id,
                                        'vendor_warehouse_id'=>$temp_warehouse_id,
                                        'product_brand'=>$temp_product_brand,
                                        'product_batch'=>$temp_product_batch,
                                        'product_qty'=>$temp_eq_qty,
                                        'months'=>$temp_months,
                                        'product_rent'=>$product_rent_ins,
                                        'product_deposite'=>$temp_product_deposite,
                                        'transport'=>$temp_transport,
                                        'sale_rental' =>$temp_sale_rental_hidden,
                                        'vendor_product_details_id' => $temp_vendor_product_details_id,
                                        'unique_id'=> $inventory_id,
                                        'product_serial_nos' =>$temp_serial_numbers,
                                        'creation_date'=>$date,
                                        'pickup_date'=>$pickup_date,
                                        'status'=>$status,
                                        'upgraded'=>$upgraded,
                                        'created_at'=>$created_at,
                                        'created_by'=>$username,  
                                    ];
                                    $update_inventory_status = DB::update("UPDATE vendor_product_details SET availability_status = 1, current_location = 0 WHERE id = $vendor_product_details_id");
                                    /**/ $order_details->insert($insert_order);
                                    
                                    /**/ $update = DB::update("UPDATE leads SET lead_status = 'Vendor Assigned' WHERE id = $lead_id");
                                    $vendor_details = DB::select("SELECT * FROM vendor_details WHERE id=$temp_vendor_id");
                                    $data['vendor_details'] = json_decode(json_encode($vendor_details),true);
                                    $products = DB::select("SELECT * FROM products WHERE id=$temp_eq_id");
                                    $data['products'] = json_decode(json_encode($products),true);
                                }
                            }
                        }
                        //-------change lead status to order_generated---//
                        $lead_status = DB::update("UPDATE leads SET lead_status='Order Generated' WHERE id=$lead_id");  
                        $order_id = json_encode($order_id_array);
                        $order_id = base64_encode($order_id);
                        DB::commit();
                        $invoice_no = DB::table('misc_table')->where('field','invoice_no')->first('value')->value;
                        DB::table('leads')->where('id',$lead_id)->update(['invoice_no'=>$invoice_no + 1]);

                        DB::table('del_orders')->where('lead_id',$lead_id)->update(['invoice_no'=>$invoice_no + 1]);

                        DB::table('misc_table')->where('field','invoice_no')->update(['value'=>$invoice_no + 1]);
                        /**/ return redirect('/order_details/'.$order_id)->with('message','Order Assign Succesfully')->with('approvevendor','pending_for_vendor_approval');
                    }
                }
                catch (Exception $ex) 
                {   
                    DB::rollBack();
                    $file = fopen(public_path().'/tempLogfile'.date('Y-m-d').'.txt','a');
                    fwrite($file,date('Y-m-d')."Exception: ".$ex);
                    fwrite($file,"request_data".$request_dump);
                    fclose($file);
                    return redirect()->back()->with('message','Something Went Wrong! Please Try Again or Contact Administrator.');
                }
            }
        }
        // <------- Till this function.....
        public function order_details($order_id)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            $order_id = base64_decode($order_id);
            $order_id = json_decode($order_id, true);
            $temp_array = array();
            $temp_order_details = array();
            $orderTypeNotIn = config('app.order_type');
            $orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";
            
            for($i=0; $i<count($order_id); $i++)
            {
                //$count = count($temp_order_details);
                $order_details = DB::select("SELECT order_details.*,
                                                    customer_details.*,
                                                    del_orders.*,
                                                    vendor_details.registered_name as vendor_name,
                                                    vendor_warehouse.wh_name as wh_name,
                                                    vendor_warehouse.wh_landmark as wh_landmark,
                                                    vendor_warehouse.wh_city as wh_city,
                                                    products.product_name as product_name
                                                    FROM order_details,del_orders,customer_details,products,vendor_details,vendor_warehouse
                                                    WHERE del_orders.order_id =$order_id[$i] 
                                                    AND del_orders.order_id = order_details.order_id 
                                                    AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)
                                                    AND products.id = order_details.product_id 
                                                    AND vendor_details.id = order_details.vendor_id 
                                                    AND vendor_warehouse.id = order_details.vendor_warehouse_id
                                                    AND order_details.customer_id = customer_details.cust_id");
                $data['order_details'] = json_decode(json_encode($order_details), true);    

                
                $temp_order_details[$i] = $data['order_details'];
            }
            $data['order_details'] = $temp_order_details;
            //print_r($data['order_details']);
            return view('OrderManagement/order_details',$data);
            
        }
        public function pending_for_vendor_approval()
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
             return redirect()->to($url);
            }
            $today = date('d-m-Y');
            $data['start_date'] = date('Y-m-d');
            $data['end_date'] = date('Y-m-d');
            $orderTypeNotIn = config('app.order_type');
            // $temp_order_details = DB::select("SELECT * FROM del_orders,order_details WHERE del_orders.order_id=order_details.order_id AND del_orders.order_approval_status='Pending' AND del_orders.status !='Cancel' AND del_orders.DelDate='$today' AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) ");
            $temp_order_details = DB::table('del_orders')
                                        ->join('order_details','del_orders.order_id','=','order_details.order_id')
                                        ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                        ->select(
                                            'del_orders.*',
                                            'order_details.*'
                                        )
                                        ->where('del_orders.order_approval_status','Pending')
                                        ->where('del_orders.DelDate',$today)
                                        ->whereNotIn('del_orders.status',['Cancel'])
                                        ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                                        
                                        ->when(session('city_based_access') == '1',function($query){
                                            $query->where('customer_details.citygroup',session('user_city'));
                                        })
                                        ->get();
            $data['temp_order_details'] = json_decode(json_encode($temp_order_details),true);
            //print_r($data['temp_order_details']);
            $prev_order_id = 0;
            $count_status = 1;
            $status = null;
            $data['final_array'] = array();
            $order_details_array = array();
            foreach($data['temp_order_details'] as $order_detail)
            {
                if($order_detail['order_id'] == $prev_order_id)
                {
                        if($status=='Accepted')
                        {
                                $count_status = $count_status + 1;
                                $temp_data['DelDate'] = $order_detail['DelDate'];
                                $temp_data['order_id'] = $order_detail['order_id'];
                                $temp_data['lead_id'] = $order_detail['lead_id'];
                                $temp_data['shipping_first_name'] = $order_detail['shipping_first_name'];
                                $temp_data['mobileno'] = $order_detail['mobileno'];
                                $temp_data['status'] = $order_detail['status'];         
                                $count = count($order_details_array[$prev_order_id]);
                                $order_details_array[$prev_order_id][$count] = $temp_data;            
                                $order_details_array[$prev_order_id]['count'] = $count_status;
                        }
                        $status = $order_detail['status'];
                }
                else
                {
                    $count_status = 1;
                    if($status=='Accepted')
                    {
                        $prev_order_id = $order_detail['order_id'];
                        $temp_data['DelDate'] = $order_detail['DelDate'];
                        $temp_data['order_id'] = $order_detail['order_id'];
                        $temp_data['lead_id'] = $order_detail['lead_id'];
                        $temp_data['shipping_first_name'] = $order_detail['shipping_first_name'];
                        $temp_data['mobileno'] = $order_detail['mobileno'];
                        $temp_data['status'] = $order_detail['status'];                        
                        $order_details_array[$prev_order_id][0] = $temp_data;
                        $order_details_array[$prev_order_id]['count'] = $count_status;
                        $count_status = $count_status + 1;
                    }
                    $status = $order_detail['status'];
                }
            }
            //print_r($order_details_array);
            foreach($order_details_array as $order_detail)
            {
            $count=count($order_detail);
            //$count=$count-1;
            //echo $count;
            if($order_detail['count'] == $count)
            {
                //echo "a";
                //$final_array = $order_detail;
                $lead_id = $order_detail[0]['lead_id'];
                $order_id = $order_detail[0]['order_id'];
                DB::UPDATE("UPDATE del_orders SET order_approval_status = 'Approved' WHERE order_id = $order_id");
                //   $leads_log = new leads_log();
                //   $leads_log_data = [
                //      'log_lead_id' => $lead_id,
                //      'log_lead_status' => 'Delivery In Progress',
                //      'log_date' => date('Y-m-d'),
                //      'log_time' => date('H:i:s'),
                //      'updated_by' => session('username')
                //   ];
                //   $leads_log->insert($leads_log_data);
                //array_push($data['final_array'],$order_detail);
            }
            }
            // $order_details = DB::select("SELECT DISTINCT('order_id'),del_orders.*,order_details.created_at as created_at FROM del_orders,order_details WHERE del_orders.order_id=order_details.order_id AND del_orders.order_approval_status='Pending' AND del_orders.status !='Cancel' AND del_orders.DelDate ='$today' AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) ");
            $orderTypeNotIn = config('app.order_type');
            $order_details = DB::table('del_orders')
                                        ->join('order_details','del_orders.order_id','=','order_details.order_id')
                                        ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                        ->distinct('del_orders.order_id')
                                        ->select(
                                            'del_orders.*',
                                            'order_details.created_at as created_at'
                                        )
                                        ->where('del_orders.order_approval_status','Pending')
                                        ->where('del_orders.DelDate',$today)
                                        ->whereNotIn('del_orders.status',['Cancel'])
                                        ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                                        ->when(session('city_based_access') == '1',function($query){
                                            $query->where('customer_details.citygroup',session('user_city'));
                                        })
                                        ->get();
            $data['order_details'] = json_decode(json_encode($order_details),true);
            echo "<script>localStorage['filtered']='today';</script>";        
            return view('/OrderManagement/pending_for_vendor_approval',$data);
        }
        public function view_pending_order_details($order_id)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
             return redirect()->to($url);
            }
            $order_details = DB::select("SELECT 
                                            order_details.id as order_details_id,
                                            order_details.creation_date as creation_date,
                                            order_details.order_id as order_id,
                                            order_details.status as status,
                                            order_details.unique_id as unique_id,
                                            order_details.customer_id as customer_id,
                                            order_details.vendor_product_id as vendor_product_id,
                                            order_details.vendor_id as vendor_id,
                                            order_details.vendor_warehouse_id as vendor_warehouse_id,
                                            order_details.created_at as created_at,
                                            order_details.product_qty as product_qty,
                                            vendor_details.registered_name as registered_name,
                                            products.product_name as product_name,
                                            customer_details.customer_name as customer_name,
                                            vendor_warehouse.wh_name as warehouse_name,
                                            vendor_warehouse.wh_area as warehouse_area,
                                            vendor_warehouse.wh_city as warehouse_city
                                        FROM 
                                            order_details,products,vendor_details,customer_details,vendor_warehouse
                                        WHERE 
                                            order_details.order_id = $order_id 
                                            AND 
                                            order_details.vendor_id = vendor_details.id 
                                            AND 
                                            order_details.product_id = products.id 
                                            AND 
                                            order_details.customer_id = customer_details.cust_id
                                            AND
                                            order_details.vendor_warehouse_id = vendor_warehouse.id");
            $data['order_details'] = json_decode(json_encode($order_details),true);
            //print_r($data['order_details']);
            return view('/OrderManagement/view_pending_order_details',$data);
        }
        public function reassign_vendor($order_id)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
             return redirect()->to($url);
            }
            $orderTypeNotIn = config('app.order_type');
            $orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";
            $product_details = DB::select("SELECT del_orders.lead_id as lead_id,
                                                    del_orders.DelDate as DelDate,
                                                    order_details.*,
                                                    products.product_rent as actual_product_rent,
                                                    products.product_name as product_name
                                            FROM products,order_details,del_orders WHERE order_details.id=$order_id 
                                            AND order_details.product_id=products.id 
                                            AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)
                                            AND order_details.order_id=del_orders.order_id ");
            $data['product_details'] = json_decode(json_encode($product_details),true);
            $vendor_details = DB::select("SELECT DISTINCT vendor_details.*,order_details.id as order_details_id FROM vendor_details,order_details,vendor_products WHERE order_details.id=$order_id AND order_details.product_id = vendor_products.product_id AND vendor_products.vendor_id = vendor_details.id");
            $data['vendor_details'] = json_decode(json_encode($vendor_details), true);

            //print_r($data['product_details']);
            return view('/OrderManagement/reassign_vendor',$data);
        }
        public function reassign_vendor_post()
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
             return redirect()->to($url);
            }
            //print_r($_POST);
            $lead_id = $_POST['lead_id'];
            $order_id = $_POST['order_id'];
            $order_details_id = $_POST['order_details_id'];
            $DelDate = $_POST['del_date'];
            $vendor_id = $_POST['vendors'][0];
            $warehouse_id = $_POST['warehouses'][0];
            $product_id= $_POST['req_eq_hidden'][0];
            $vendor_product_id= $_POST['vendor_product_id'][0];
            $product_qty= $_POST['eq_quantity_hidden'][0];
            $product_rent= $_POST['offered_rent'][0];
            $product_deposite= $_POST['deposite'][0];
            $product_transport= $_POST['transport'][0];
            $brand = $_POST['brands'][0];
            $batch = $_POST['batches'][0];
            //$update_order = DB::update("UPDATE order_details SET vendor_id=$vendor_id, vendor_warehouse_id=$warehouse_id, status='Pending' WHERE id=$order_id");
            $del_orders = new DelOrders();
            $order_details = new OrderDetails();
            $update_del_orders = [
                'status'=>'Pending',
                'lead_id'=>$lead_id,
                'vendor_id'=>$vendor_id,
                'deliverypickup'=>'Delivery',
                'DelDate'=>$DelDate,
                // 'location'=>$location,
                // 'shipping_first_name'=>$customer_name,
                // 'TotalAmt'=>$temp_order_detail[$count-1]['total_amt'],
                // 'PaymentMode'=>request()->get('payment_mode'),
                // 'mobileno'=>request()->get('mobile_no'),
                'DelAssignedTo'=>'Pending',
                'TravelMode'=>'Null',
                'order_approval_status'=>'Pending',
                // 'fulldetails'=>$fulldetails,
            ];
            //print_r($generate_order); 
                $del_orders->where('order_id',$order_id)->update($update_del_orders);
                $update_order_details = [
                    'order_id'=> $order_id,
                    //'customer_id'=>$customer_id,
                    'product_id'=>$product_id,
                    'vendor_product_id'=>$vendor_product_id,
                    'vendor_id'=>$vendor_id,
                    'vendor_warehouse_id'=>$warehouse_id,
                    'product_brand'=>$brand,
                    'product_batch'=>$batch,
                    'product_qty'=>$product_qty,
                    'product_rent'=>$product_rent,
                    'product_deposite'=>$product_deposite,
                    'transport'=>$product_transport,
                    //'sale_rental' =>$temp_sale_rental_hidden,
                    //'creation_date'=>$date,
                    //'pickup_date'=>$pickup_date,
                    'status'=>'Pending',
                    //'created_at'=>$created_at,
                    //'created_by'=>$username,  
                ];
            $order_details->where('id',$order_details_id)->update($update_order_details);

            $vendor_details = DB::select("SELECT * FROM vendor_details WHERE id=$vendor_id");
            $data['vendor_details'] = json_decode(json_encode($vendor_details),true);
            
            //session(['email' => $data['vendor_details'][0]['of_email']]);
            //$data = array('name'=>"'".$data['vendor_details'][0]['registered_name']."'",'product_name'=>"'".$data['products'][0]['product_name']."'");
            // Mail::send('vendorMail/pendingRentedProductMail', $data, function($message) 
            // {                
            //     $email_id = session('email');
            //     $message->to($email_id, 'Product Rental Request')->subject('Product Rental Request');
            //     $message->from('tempmailquali@gmail.com', 'Quali55Care');
            // });
            
            return redirect('/view_pending_order_details/'.$order_id)->with('message','Product Assign Succesfully');
        }
        public function mobile_app_leads()
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
            return redirect()->to($url);
            }
            $leads = DB::select('SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = "Mobile Generated" ORDER BY leads.creation_date DESC');
            $data['lead_details'] = json_decode(json_encode($leads), true);
            for ($i=0; $i < count($data['lead_details']); $i++) 
            { 
                $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
                $equipement_details = array();
                for ($j=0; $j <count($product); $j++) 
                { 
                $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
                $product_details = json_decode(json_encode($product_details), true);
                array_push($equipement_details,$product_details[0]['product_name']);
                }
                $equipements = json_encode($equipement_details);
                $data['lead_details'][$i]['equipment_requirement'] = $equipements;
            }
            return view('OrderManagement/mobileAppLeads',$data);
        }
        public function status_change()
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
            return redirect()->to($url);
            }
            
            $order_details_id = $_POST['order_details_id'];
            $order_id = $_POST['order_id'];
            $vendor_product_id = $_POST['vendor_product_id'];
            $vendor_id = $_POST['vendor_id'];
            $vendor_warehouse_id = $_POST['vendor_warehouse_id'];
            $product_qty = $_POST['product_qty'];
            $creation_date = $_POST['creation_date'];
            $vendor_rented_products = new VendorRentedProducts();
            $OrderDetails = new OrderDetails();
            // $date = date('dm');  
            for ($i=0; $i < count($vendor_warehouse_id); $i++) 
            {
                $max_id = DB::select("SELECT max(id) as id FROM vendor_rented_products");
                $data['max_id'] = json_decode(json_encode($max_id),true);
                $max_id = $data['max_id'][0]['id']+1;
                $unique_id = dechex($vendor_id[$i]."".$date."".$vendor_warehouse_id[$i]."".$vendor_product_id[$i]."".$max_id); 
                // order_details status changing to accpted
                $temp_order_id = $order_id[$i];
                $status_order_details = DB::update("UPDATE order_details status = 'Accepted' WHERE order_id IN($temp_order_id)");
                for($j=0; $j<$product_qty[$i]; $j++)
                {
                    $insert_order =[
                        'vendor_id'=>$vendor_id[$i],
                        'vendor_product_id'=> $vendor_product_id[$i],
                        'unique_id'=>$unique_id,
                        'rental_date'=>$creation_date[$i],
                        'pickup_date'=>date('Y-m-d',strtotime("+1 month",strtotime($creation_date[$i]))),
                        'status' => 'On Rent',
                        'created_by'=>session('username')
                    ];
                   $get_insert_id = $vendor_rented_products->insertGetId($insert_order);
                   $OrderDetails->where('id',$order_details_id[$i])->update(['rented_product_id'=>$get_insert_id]);
                }
                // Del_orders status changed to Approved 
                $status_del_orders = DB::update("UPDATE del_orders SET order_approval_status = 'Approved' WHERE order_id IN($temp_order_id)");
            }
            return redirect()->back()->with('message','Approved Successfully');
        }
        public function filterPendingVendorApproval($filter_by)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
             return redirect()->to($url);
            }
            if($filter_by =='today')
            {
                $date = date('d-m-Y');
                $data['start_date'] = date('Y-m-d');
                $data['end_date'] = date('Y-m-d');
                $whereClause = "del_orders.DelDate = '$date'";
                echo "<script>localStorage['filtered']='today';</script>";
            }
            elseif($filter_by =='yesterday')
            {
                $prevDate = date('d-m-Y',strtotime("-1 days"));
                $data['start_date'] = date('Y-m-d',strtotime($prevDate));
                $data['end_date'] = date('Y-m-d');
                $whereClause = "del_orders.DelDate = '$prevDate'";
                echo "<script>localStorage['filtered']='yesterday';</script>";
            }
            elseif($filter_by =='past_3_days')
            {
                $past_three_days = date('d-m-Y',strtotime("-2 days"));
                $data['start_date'] = date('Y-m-d',strtotime($past_three_days));
                $data['end_date'] = date('Y-m-d');
                $whereClause = "del_orders.DelDate >= '$past_three_days'";
                echo "<script>localStorage['filtered']='past_3_days';</script>";
            }
            elseif($filter_by =='week')
            {
                $past_three_days = date('d-m-Y',strtotime("-7 days"));
                $data['start_date'] = date('Y-m-d',strtotime($past_three_days));
                $data['end_date'] = date('Y-m-d');
                $whereClause = "del_orders.DelDate >= '$past_three_days'";
                echo "<script>localStorage['filtered']='week';</script>";
            }
            elseif($filter_by =='month')
            {
                $month = date('m-Y');
                $start_date_temp = '01-'.$month;
                $start_date = date('d-m-Y',strtotime($start_date_temp));
                $end_date_temp = '31-'.$month;
                $end_date = date('d-m-Y',strtotime($end_date_temp));
                $data['start_date'] = date('Y-m-d',strtotime($start_date));
                $data['end_date'] = date('Y-m-d',strtotime($end_date));
                $whereClause = "del_orders.DelDate BETWEEN '$start_date' AND '$end_date'";
            }
            // elseif($filter_by =='all')
            // {
            //     $isLoggedIn = $this->isLoggedIn();
            //     if($isLoggedIn == 'false')
            //     {
            //         $url = url('/');
            //         return redirect()->to($url);
            //     }
            //     $orderTypeNotIn = config('app.order_type');
            //     $orderTypeNotIn = "'".implode("','",$orderTypeNotIn)."'";

            //     // $temp_order_details = DB::select("SELECT * FROM del_orders,order_details WHERE del_orders.order_id=order_details.order_id AND del_orders.order_approval_status='Pending'  AND del_orders.status !='Cancel' AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)");
            //     $data['temp_order_details'] = json_decode(json_encode($temp_order_details),true);
            //     $prev_order_id = 0;
            //     $count_status = 1;
            //     $status = null;
            //     $data['final_array'] = array();
            //     $order_details_array = array();
            //     foreach($data['temp_order_details'] as $order_detail)
            //     {
            //     if($order_detail['order_id'] == $prev_order_id)
            //     {
            //             if($status=='Accepted')
            //             {
            //                 $count_status = $count_status + 1;
            //                 $temp_data['DelDate'] = $order_detail['DelDate'];
            //                 $temp_data['order_id'] = $order_detail['order_id'];
            //                 $temp_data['lead_id'] = $order_detail['lead_id'];
            //                 $temp_data['shipping_first_name'] = $order_detail['shipping_first_name'];
            //                 $temp_data['mobileno'] = $order_detail['mobileno'];
            //                 $temp_data['status'] = $order_detail['status'];         
            //                 $count = count($order_details_array[$prev_order_id]);
            //                 $order_details_array[$prev_order_id][$count] = $temp_data;            
            //                 $order_details_array[$prev_order_id]['count'] = $count_status;
            //             }
            //             $status = $order_detail['status'];
            //     }
            //     else
            //     {
            //         $count_status = 1;
            //         if($status=='Accepted')
            //         {
            //             $prev_order_id = $order_detail['order_id'];
            //             $temp_data['DelDate'] = $order_detail['DelDate'];
            //             $temp_data['order_id'] = $order_detail['order_id'];
            //             $temp_data['lead_id'] = $order_detail['lead_id'];
            //             $temp_data['shipping_first_name'] = $order_detail['shipping_first_name'];
            //             $temp_data['mobileno'] = $order_detail['mobileno'];
            //             $temp_data['status'] = $order_detail['status'];                        
            //             $order_details_array[$prev_order_id][0] = $temp_data;
            //             $order_details_array[$prev_order_id]['count'] = $count_status;
            //             $count_status = $count_status + 1;
            //         }
            //         $status = $order_detail['status'];
            //     }
            //     }
            //     //print_r($order_details_array);
            //     foreach($order_details_array as $order_detail)
            //     {
            //     $count=count($order_detail);
            //     //$count=$count-1;
            //     //echo $count;
            //     if($order_detail['count'] == $count)
            //     {
            //         //echo "a";
            //         //$final_array = $order_detail;
            //         $lead_id = $order_detail[0]['lead_id'];
            //         $order_id = $order_detail[0]['order_id'];
            //         DB::UPDATE("UPDATE del_orders SET order_approval_status = 'Approved' WHERE order_id = $order_id");
            //         //   $leads_log = new leads_log();
            //         //   $leads_log_data = [
            //         //      'log_lead_id' => $lead_id,
            //         //      'log_lead_status' => 'Delivery In Progress',
            //         //      'log_date' => date('Y-m-d'),
            //         //      'log_time' => date('H:i:s'),
            //         //      'updated_by' => session('username')
            //         //   ];
            //         //   $leads_log->insert($leads_log_data);
            //         //array_push($data['final_array'],$order_detail);
            //     }
            //     }
            //     $orderTypeNotIn = config('app.order_type');
            //     $orderTypeNotIn = "'".implode("','",$orderTypeNotIn)."'";

            //     $order_details = DB::select("SELECT * FROM del_orders WHERE  del_orders.order_approval_status='Pending'  AND del_orders.status !='Cancel' AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)");
            //     //echo "SELECT * FROM del_orders,order_details WHERE del_orders.order_id=order_details.order_id AND del_orders.order_approval_status";
            //     $data['order_details'] = json_decode(json_encode($order_details),true);        
            //     return view('/OrderManagement/pending_for_vendor_approval',$data);
            // }
                $isLoggedIn = $this->isLoggedIn();
                if($isLoggedIn == 'false')
                {
                    $url = url('/');
                    return redirect()->to($url);
                }

            // $temp_order_details = DB::select("SELECT * FROM del_orders,order_details WHERE del_orders.order_id=order_details.order_id AND del_orders.order_approval_status='Pending'  AND del_orders.status !='Cancel' AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) AND $whereClause ");
            $orderTypeNotIn = config('app.order_type');
            $temp_order_details = DB::table('del_orders')
                                        ->join('order_details','del_orders.order_id','=','order_details.order_id')
                                        ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                        ->select(
                                            'del_orders.*',
                                            'order_details.*'
                                        )
                                        ->where('del_orders.order_approval_status','Pending')
                                        ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                                        // ->where('del_orders.DelDate',$today)
                                        ->whereNotIn('del_orders.status',['Cancel'])

                                        ->when($filter_by == 'today',function($query){
                                            $query->where('del_orders.DelDate',date('d-m-Y'));
                                        })
                                        ->when($filter_by == 'yesterday',function($query){
                                            $query->where('del_orders.DelDate',date('d-m-Y',strtotime("-1 days")));
                                        })
                                        ->when($filter_by == 'past_3_days',function($query){
                                            $start_date = date('d-m-Y',strtotime("-2 days"));
                                            $end_date = date('d-m-Y');
                                            $query->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$start_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$end_date','%d-%m-%Y'))")]);
                                        })
                                        ->when($filter_by == 'week',function($query){
                                            $start_date = date('d-m-Y',strtotime("-7 days"));
                                            $end_date = date('d-m-Y');
                                            $query->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$start_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$end_date','%d-%m-%Y'))")]);
                                        })
                                        ->when($filter_by == 'month',function($query){
                                            $month = date('m-Y');
                                            $start_date_temp = '01-'.$month;
                                            $start_date = date('d-m-Y',strtotime($start_date_temp));
                                            $end_date_temp = '31-'.$month;
                                            $end_date = date('d-m-Y',strtotime($end_date_temp));
                                            $query->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$start_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$end_date','%d-%m-%Y'))")]);
                                        })
                                        ->when(session('city_based_access') == '1',function($query){
                                            $query->where('customer_details.citygroup',session('user_city'));
                                        })
                                        ->orderBy('del_orders.order_id')
                                        ->get();
                $data['temp_order_details'] = json_decode(json_encode($temp_order_details),true);
                $prev_order_id = 0;
                $count_status = 1;
                $status = null;
                $data['final_array'] = array();
                $order_details_array = array();
                foreach($data['temp_order_details'] as $order_detail)
                {
                if($order_detail['order_id'] == $prev_order_id)
                {
                        if($status=='Accepted')
                        {
                            $count_status = $count_status + 1;
                            $temp_data['DelDate'] = $order_detail['DelDate'];
                            $temp_data['order_id'] = $order_detail['order_id'];
                            $temp_data['lead_id'] = $order_detail['lead_id'];
                            $temp_data['shipping_first_name'] = $order_detail['shipping_first_name'];
                            $temp_data['mobileno'] = $order_detail['mobileno'];
                            $temp_data['status'] = $order_detail['status'];         
                            $count = count($order_details_array[$prev_order_id]);
                            $order_details_array[$prev_order_id][$count] = $temp_data;            
                            $order_details_array[$prev_order_id]['count'] = $count_status;
                        }
                        $status = $order_detail['status'];
                }
                else
                {
                    $count_status = 1;
                    if($status=='Accepted')
                    {
                        $prev_order_id = $order_detail['order_id'];
                        $temp_data['DelDate'] = $order_detail['DelDate'];
                        $temp_data['order_id'] = $order_detail['order_id'];
                        $temp_data['lead_id'] = $order_detail['lead_id'];
                        $temp_data['shipping_first_name'] = $order_detail['shipping_first_name'];
                        $temp_data['mobileno'] = $order_detail['mobileno'];
                        $temp_data['status'] = $order_detail['status'];                        
                        $order_details_array[$prev_order_id][0] = $temp_data;
                        $order_details_array[$prev_order_id]['count'] = $count_status;
                        $count_status = $count_status + 1;
                    }
                    $status = $order_detail['status'];
                }
                }
                //print_r($order_details_array);
                foreach($order_details_array as $order_detail)
                {
                $count=count($order_detail);
                //$count=$count-1;
                //echo $count;
                if($order_detail['count'] == $count)
                {
                    //echo "a";
                    //$final_array = $order_detail;
                    $lead_id = $order_detail[0]['lead_id'];
                    $order_id = $order_detail[0]['order_id'];
                    DB::UPDATE("UPDATE del_orders SET order_approval_status = 'Approved' WHERE order_id = $order_id");
                    //   $leads_log = new leads_log();
                    //   $leads_log_data = [
                    //      'log_lead_id' => $lead_id,
                    //      'log_lead_status' => 'Delivery In Progress',
                    //      'log_date' => date('Y-m-d'),
                    //      'log_time' => date('H:i:s'),
                    //      'updated_by' => session('username')
                    //   ];
                    //   $leads_log->insert($leads_log_data);
                    //array_push($data['final_array'],$order_detail);
                }
                }
            // $order_details = DB::select("SELECT * FROM del_orders WHERE  del_orders.order_approval_status='Pending'  AND del_orders.status !='Cancel' AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) AND $whereClause ");
            $orderTypeNotIn = config('app.order_type');
            $order_details = DB::table('del_orders')
                                        ->join('order_details','del_orders.order_id','=','order_details.order_id')
                                        ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                        ->select(
                                            'del_orders.*',
                                            'order_details.*'
                                        )
                                        ->where('del_orders.order_approval_status','Pending')
                                        // ->where('del_orders.DelDate',$today)
                                        ->whereNotIn('del_orders.status',['Cancel'])
                                        ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                                        ->when($filter_by == 'today',function($query){
                                            $query->where('del_orders.DelDate',date('d-m-Y'));
                                        })
                                        ->when($filter_by == 'yesterday',function($query){
                                            $query->where('del_orders.DelDate',date('d-m-Y',strtotime("-1 days")));
                                        })
                                        ->when($filter_by == 'past_3_days',function($query){
                                            $start_date = date('d-m-Y',strtotime("-2 days"));
                                            $end_date = date('d-m-Y');
                                            $query->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$start_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$end_date','%d-%m-%Y'))")]);
                                        })
                                        ->when($filter_by == 'week',function($query){
                                            $start_date = date('d-m-Y',strtotime("-7 days"));
                                            $end_date = date('d-m-Y');
                                            $query->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$start_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$end_date','%d-%m-%Y'))")]);
                                        })
                                        ->when($filter_by == 'month',function($query){
                                            $month = date('m-Y');
                                            $start_date_temp = '01-'.$month;
                                            $start_date = date('d-m-Y',strtotime($start_date_temp));
                                            $end_date_temp = '31-'.$month;
                                            $end_date = date('d-m-Y',strtotime($end_date_temp));
                                            $query->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$start_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$end_date','%d-%m-%Y'))")]);
                                        })
                                        ->when(session('city_based_access') == '1',function($query){
                                            $query->where('customer_details.citygroup',session('user_city'));
                                        })
                                        ->get();
            //echo "SELECT * FROM del_orders,order_details WHERE del_orders.order_id=order_details.order_id AND del_orders.order_approval_status";
            $data['order_details'] = json_decode(json_encode($order_details),true);        
            return view('/OrderManagement/pending_for_vendor_approval',$data);     
        }
        public function filterPendingVendorApprovalDWS()
        {
            $start_date = date('d-m-Y',strtotime($_POST['start_date']));   
            $end_date = date('d-m-Y',strtotime($_POST['end_date']));
            $data['start_date'] = $_POST['start_date'];   
            $data['end_date'] = $_POST['end_date'];
            $whereClause = "del_orders.DelDate BETWEEN '$start_date' AND '$end_date'";

            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                    $url = url('/');
                return redirect()->to($url);
            }
            // $temp_order_details = DB::select("SELECT * FROM del_orders,order_details WHERE del_orders.order_id=order_details.order_id AND del_orders.order_approval_status='Pending'  AND del_orders.status !='Cancel' AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) AND $whereClause ");
            $orderTypeNotIn = config('app.order_type');
            $temp_order_details = DB::table('del_orders')
                                        ->join('order_details','del_orders.order_id','=','order_details.order_id')
                                        ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                        ->select(
                                            'del_orders.*',
                                            'order_details.*'
                                        )
                                        ->where('del_orders.order_approval_status','Pending')
                                        // ->where('del_orders.DelDate',$today)
                                        ->whereNotIn('del_orders.status',['Cancel'])
                                        ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                                        ->when($start_date,function($query)use($start_date,$end_date){
                                            $query->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$start_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$end_date','%d-%m-%Y'))")]);
                                        })
                                        ->when(session('city_based_access') == '1',function($query){
                                            $query->where('customer_details.citygroup',session('user_city'));
                                        })
                                        ->get();
            $data['temp_order_details'] = json_decode(json_encode($temp_order_details),true);
            $prev_order_id = 0;
            $count_status = 1;
            $status = null;
            $data['final_array'] = array();
            $order_details_array = array();
            foreach($data['temp_order_details'] as $order_detail)
            {
                if($order_detail['order_id'] == $prev_order_id)
                {
                    if($status=='Accepted')
                    {
                            $count_status = $count_status + 1;
                            $temp_data['DelDate'] = $order_detail['DelDate'];
                            $temp_data['order_id'] = $order_detail['order_id'];
                            $temp_data['lead_id'] = $order_detail['lead_id'];
                            $temp_data['shipping_first_name'] = $order_detail['shipping_first_name'];
                            $temp_data['mobileno'] = $order_detail['mobileno'];
                            $temp_data['status'] = $order_detail['status'];         
                            $count = count($order_details_array[$prev_order_id]);
                            $order_details_array[$prev_order_id][$count] = $temp_data;            
                            $order_details_array[$prev_order_id]['count'] = $count_status;
                    }
                    $status = $order_detail['status'];
                }
                else
                {
                    $count_status = 1;
                    if($status=='Accepted')
                    {
                        $prev_order_id = $order_detail['order_id'];
                        $temp_data['DelDate'] = $order_detail['DelDate'];
                        $temp_data['order_id'] = $order_detail['order_id'];
                        $temp_data['lead_id'] = $order_detail['lead_id'];
                        $temp_data['shipping_first_name'] = $order_detail['shipping_first_name'];
                        $temp_data['mobileno'] = $order_detail['mobileno'];
                        $temp_data['status'] = $order_detail['status'];                        
                        $order_details_array[$prev_order_id][0] = $temp_data;
                        $order_details_array[$prev_order_id]['count'] = $count_status;
                        $count_status = $count_status + 1;
                    }
                    $status = $order_detail['status'];
                }
            }
            //print_r($order_details_array);
            foreach($order_details_array as $order_detail)
            {
                $count=count($order_detail);
                //$count=$count-1;
                //echo $count;
                if($order_detail['count'] == $count)
                {
                    //echo "a";
                    //$final_array = $order_detail;
                    $lead_id = $order_detail[0]['lead_id'];
                    $order_id = $order_detail[0]['order_id'];
                    DB::UPDATE("UPDATE del_orders SET order_approval_status = 'Approved' WHERE order_id = $order_id ");
                //   $leads_log = new leads_log();
                //   $leads_log_data = [
                //      'log_lead_id' => $lead_id,
                //      'log_lead_status' => 'Delivery In Progress',
                //      'log_date' => date('Y-m-d'),
                //      'log_time' => date('H:i:s'),
                //      'updated_by' => session('username')
                //   ];
                //   $leads_log->insert($leads_log_data);
                    //array_push($data['final_array'],$order_detail);
                }
            }
            $orderTypeNotIn = config('app.order_type');
            // $orderTypeNotIn = "'".implode("','",$orderTypeNotIn)."'";

            // $order_details = DB::select("SELECT * FROM del_orders WHERE  del_orders.order_approval_status='Pending'  AND del_orders.status !='Cancel' AND del_orders.deliverypickup NOT IN ($orderTypeNotIn) AND $whereClause ");
            $orderTypeNotIn = config('app.order_type');
            $order_details = DB::table('del_orders')
                                        ->join('order_details','del_orders.order_id','=','order_details.order_id')
                                        ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                        ->select(
                                            'del_orders.*',
                                            'order_details.*'
                                        )
                                        ->where('del_orders.order_approval_status','Pending')
                                        // ->where('del_orders.DelDate',$today)
                                        ->whereNotIn('del_orders.status',['Cancel'])
                                        ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                                        ->when($start_date,function($query)use($start_date,$end_date){
                                            $query->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$start_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$end_date','%d-%m-%Y'))")]);
                                        })
                                        ->when(session('city_based_access') == '1',function($query){
                                            $query->where('customer_details.citygroup',session('user_city'));
                                        })
                                        ->get();
            //echo "SELECT * FROM del_orders,order_details WHERE del_orders.order_id=order_details.order_id AND del_orders.order_approval_status";
            $data['order_details'] = json_decode(json_encode($order_details),true);        
            return view('/OrderManagement/pending_for_vendor_approval',$data);     
        }
        public function approve_orders()
        {
            $order_checked = $_POST['order_checked'];
            $vendor_rented_products = new VendorRentedProducts();
            $OrderDetails =new OrderDetails();
            for ($i=0; $i <count($order_checked) ; $i++)
            {
                
                //$order_id = implode(",",$_POST['order_id']);
                $order_id = $order_checked[$i];
                $order_details = DB::select("SELECT * FROM order_details WHERE order_id ='$order_id' ");
                $data['order_details'] = json_decode(json_encode($order_details),true);  
                for ($j=0; $j <count($data['order_details']) ; $j++) 
                {
                    $order_details_id = $data['order_details'][$j]['id'];
                    $order_id = $data['order_details'][$j]['order_id'];
                    $vendor_id = $data['order_details'][$j]['vendor_id'];
                    $vendor_warehouse_id = $data['order_details'][$j]['vendor_warehouse_id'];
                    $vendor_product_id = $data['order_details'][$j]['vendor_product_id'];
                    $product_qty = $data['order_details'][$j]['product_qty'];
                    $creation_date = $data['order_details'][$j]['creation_date'];
                    $unique_id = $data['order_details'][$j]['unique_id'];
                    // $date = date('dm');    
                    // $max_id = DB::select("SELECT max(id) as id FROM vendor_rented_products");
                    // $data['max_id'] = json_decode(json_encode($max_id),true);
                    // $max_id = $data['max_id'][0]['id']+1;
                    // $unique_id = dechex($vendor_id."".$date."".$vendor_warehouse_id."".$vendor_product_id."".$max_id);
                    //order_details status changing to accpted
                    $status_order_details = DB::update("UPDATE order_details SET status = 'Accepted' WHERE order_id ='$order_id' AND id='$order_details_id' ");
                    for($k=0; $k<$product_qty; $k++)
                    {
                        $insert_order =[
                            'vendor_id'=>$vendor_id,
                            'vendor_product_id'=> $vendor_product_id,
                            'unique_id'=>$unique_id,
                            'rental_date'=>$creation_date,
                            'pickup_date'=>date('Y-m-d',strtotime('+1 month',strtotime($creation_date))),
                            'status' => 'On Rent',
                            'created_by'=>session('username')
                        ];
                        //print_r($insert_order);
                        $get_insert_id = $vendor_rented_products->insertGetId($insert_order);
                        $OrderDetails->where('id',$order_details_id)->update(['rented_product_id'=>$get_insert_id]);
                    }
                    //Del_orders status changed to Approved 
                    $status_del_orders = DB::update("UPDATE del_orders SET order_approval_status = 'Approved' WHERE order_id='$order_id' ");
                }
            }
            return redirect()->back()->with('message','Order Approved Successfully')->with('assigndelboy','confirmed_delivery');

        }
        //New Logics Based on Batches
        // --Select Vendor based on equipments-- //
        public static function individual_vendor_batch($equipment,$eq_quantity)
        {
            if($_SERVER['REQUEST_METHOD']=='GET')
            {
                $vendors = DB::select("SELECT DISTINCT vendor_details.id as vendor_id, vendor_details.registered_name as vendor_name FROM vendor_details, vendor_products,vendor_product_details WHERE vendor_products.product_id = $equipment AND vendor_products.vendor_id = vendor_details.id AND vendor_products.product_quantity >= $eq_quantity AND (vendor_products.status != 'Pending' OR vendor_products.status != 'Rejected') AND (vendor_product_details.vendor_products_id = vendor_products.id AND vendor_product_details.availability_status = '0' AND vendor_product_details.current_location != 0)");
                $data['vendors'] = json_decode(json_encode($vendors), true);
                $jsonstring = json_encode($data['vendors']);
                echo $jsonstring;
            } 
        }
        //for sale
        public function individual_vendor_batch_sale()
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
             return redirect()->to($url);
            }
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
            if($_SERVER['REQUEST_METHOD']=='GET')
            {
                $vendors = DB::select("SELECT DISTINCT vendor_details.id as vendor_id, vendor_details.registered_name as vendor_name FROM vendor_details, vendor_products WHERE vendor_products.vendor_id = vendor_details.id AND (vendor_products.status != 'Pending' OR vendor_products.status != 'Rejected')");
                $data['vendors'] = json_decode(json_encode($vendors), true);
                $jsonstring = json_encode($data['vendors']);
                echo $jsonstring;
            } 
        }
        //ware for sale
        public function select_vendor_warehouses_sale($vendor_id)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
            if($_SERVER['REQUEST_METHOD']=='GET')
            {
                $warehouses = DB::select("SELECT DISTINCT vendor_warehouse.id as warehouse_id, vendor_warehouse.wh_name as wh_name, vendor_warehouse.wh_area as wh_area, vendor_warehouse.wh_city as wh_city FROM vendor_warehouse,vendor_products WHERE vendor_products.vendor_id = $vendor_id AND vendor_products.warehouse_id = vendor_warehouse.id ");
                $data['warehouses'] = json_decode(json_encode($warehouses), true);
                $jsonstring = json_encode($data['warehouses']);
                echo $jsonstring;
            }
        }
        //close sale
        public function select_vendor_warehouses($equipment,$vendor_id)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
            if($_SERVER['REQUEST_METHOD']=='GET')
            {
                // $warehouses = DB::select("SELECT DISTINCT vendor_warehouse.id as warehouse_id, vendor_warehouse.wh_name as wh_name, vendor_warehouse.wh_area as wh_area, vendor_warehouse.wh_city as wh_city FROM vendor_warehouse,vendor_products WHERE vendor_products.product_id = $equipment AND vendor_products.vendor_id = $vendor_id AND vendor_products.warehouse_id = vendor_warehouse.id ");
                // $data['warehouses'] = json_decode(json_encode($warehouses), true);
                // $jsonstring = json_encode($data['warehouses']);
                // echo $jsonstring;
                //-- query to fetch warehouse based on vendor id only --//
                // $warehouses = DB::select("SELECT DISTINCT vendor_warehouse.id as warehouse_id, vendor_warehouse.wh_name as wh_name, vendor_warehouse.wh_area as wh_area, vendor_warehouse.wh_city as wh_city,vendor_product_details.current_location as current_location FROM vendor_products,vendor_warehouse,vendor_product_details WHERE vendor_products.vendor_id = $vendor_id AND vendor_products.id = vendor_product_details.vendor_products_id AND vendor_product_details.warehouse_id = vendor_warehouse.id AND vendor_product_details.current_location IN(1,2)");
                //-- Old query to fetch warehouse based on equipment id and vendor id   --//
                $warehouses = DB::select("SELECT DISTINCT vendor_warehouse.id as warehouse_id, vendor_warehouse.wh_name as wh_name, vendor_warehouse.wh_area as wh_area, vendor_warehouse.wh_city as wh_city,vendor_product_details.current_location as current_location FROM vendor_products,vendor_warehouse,vendor_product_details WHERE vendor_products.product_id = $equipment AND vendor_products.vendor_id = $vendor_id AND vendor_products.id = vendor_product_details.vendor_products_id AND vendor_product_details.warehouse_id = vendor_warehouse.id AND vendor_product_details.current_location IN(0,1,2)");
                
                
                
                // echo "SELECT DISTINCT vendor_warehouse.id as warehouse_id, vendor_warehouse.wh_name as wh_name, vendor_warehouse.wh_area as wh_area, vendor_warehouse.wh_city as wh_city,vendor_product_details.current_location as current_location FROM vendor_products,vendor_warehouse,vendor_product_details WHERE vendor_products.product_id = $equipment AND vendor_products.vendor_id = $vendor_id AND vendor_products.id = vendor_product_details.vendor_products_id AND vendor_product_details.warehouse_id = vendor_warehouse.id AND vendor_product_details.current_location IN(1,2)";
                $data['warehouses'] = json_decode(json_encode($warehouses), true);
                $virtual_warehouse = array();
                $vendor_warehouse = array();
                foreach($data['warehouses'] as $key=>$warehouse)
                {
                    if($warehouse['current_location']==1)
                    {
                        array_push($virtual_warehouse,$warehouse);
                    }
                    elseif($warehouse['current_location']==2)
                    {
                        array_push($vendor_warehouse,$warehouse);
                    }
                }
                $pata['warehouses'] = array();
                array_push($pata['warehouses'],$virtual_warehouse);
                array_push($pata['warehouses'],$vendor_warehouse);
                $jsonstring = json_encode($pata['warehouses']);
                echo $jsonstring;
                // return $pata;
            }
        }
        public function select_product_brand($equipment,$vendor_id,$warehouse_id)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
            if($_SERVER['REQUEST_METHOD']=='GET')
            {
                $brands = DB::select("SELECT DISTINCT product_brands.id as brand_id, product_brands.brand_name as brand_name FROM product_brands,vendor_products,vendor_product_details WHERE vendor_products.product_id = $equipment AND vendor_products.vendor_id = $vendor_id AND vendor_product_details.vendor_products_id = vendor_products.id AND vendor_product_details.warehouse_id = $warehouse_id AND product_brands.id = vendor_products.product_brand");
                $data['brands'] = json_decode(json_encode($brands), true);
                $jsonstring = json_encode($data['brands']);
                echo $jsonstring;
            }
        }
        //for sale
        public function select_product_brand_sale($equipment)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
            if($_SERVER['REQUEST_METHOD']=='GET')
            {
                $brands = DB::select("SELECT DISTINCT product_brands.id as brand_id, product_brands.brand_name as brand_name FROM product_brands WHERE product_brands.product_id = $equipment ");
                $data['brands'] = json_decode(json_encode($brands), true);
                $jsonstring = json_encode($data['brands']);
                echo $jsonstring;
            }
        }
        public function select_batch($equipment,$vendor_id,$warehouse_id,$brand_id)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
            if($_SERVER['REQUEST_METHOD']=='GET')
            {
                $brands = DB::select("SELECT DISTINCT vendor_products.id as vendor_product_id, vendor_products.batch as batch_name, vendor_products.product_rent_approved as product_rent FROM vendor_products,vendor_product_details WHERE vendor_products.product_id = $equipment AND vendor_products.vendor_id = $vendor_id AND vendor_product_details.warehouse_id = $warehouse_id AND vendor_product_details.vendor_products_id = vendor_products.id AND vendor_products.product_brand = $brand_id");
                $data['brands'] = json_decode(json_encode($brands), true);
                $jsonstring = json_encode($data['brands']);
                echo $jsonstring;
            }
        }
        public function select_inventory($vendor_id,$warehouse_id,$brand_id,$product_id)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            if($_SERVER['REQUEST_METHOD']=='GET')
            {
                $inventory = DB::select("SELECT vendor_product_details.id as vendor_product_details_id, vendor_product_details.inventory_id as inventory_id FROM vendor_product_details,vendor_products WHERE vendor_products.id = vendor_product_details.vendor_products_id AND vendor_products.vendor_id = $vendor_id AND vendor_products.product_id = $product_id AND vendor_products.product_brand = $brand_id AND vendor_product_details.warehouse_id = $warehouse_id AND vendor_product_details.availability_status=0 AND vendor_product_details.current_location != 0");
                $data['inventory'] = json_decode(json_encode($inventory), true);
                $jsonstring = json_encode($data['inventory']);
                echo $jsonstring;
            }
        }
        public function select_inventoryOLD($product_id,$warehouse_id)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            if($_SERVER['REQUEST_METHOD']=='GET')
            {
                $inventory = DB::select("SELECT vendor_product_details.id as vendor_product_details_id, vendor_product_details.inventory_id as inventory_id FROM vendor_product_details WHERE vendor_products_id = $product_id AND warehouse_id = $warehouse_id AND availability_status=0 AND current_location != 0");
                $data['inventory'] = json_decode(json_encode($inventory), true);
                $jsonstring = json_encode($data['inventory']);
                echo $jsonstring;
            }
        }
        public function getDetails($product_id)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
            if($_SERVER['REQUEST_METHOD']=='GET')
            {
                $details = DB::select("SELECT vendor_products.product_rent_approved as product_rent, vendor_products.id as vendor_product_id, products.product_details as product_details FROM vendor_products,products WHERE vendor_products.id = $product_id AND vendor_products.product_id=products.id");
                $data['details'] = json_decode(json_encode($details), true);
                $jsonstring = json_encode($data['details']);
                echo $jsonstring;
            }
        }
        public function all_vendor_batch()
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
            if($_SERVER['REQUEST_METHOD']=='POST')
            {
                $equipment = json_decode($_POST['equipments']);
                $count = 0;
                $data_count = count($equipment);
                $data_count = $data_count-1;
                $vdr = array();
                for ($i=0; $i <$data_count ; $i++) 
                { 
                    $equip = $equipment[$i];
                    $vendor_id = DB::select("SELECT DISTINCT vendor_id FROM vendor_products WHERE product_id=$equip AND vendor_products.status != 'Pending' AND vendor_products.status != 'Rejected'");
                    $data['vendor'.$i] = json_decode(json_encode($vendor_id), true); 
                    $count = count($data['vendor'.$i]);
                    $temp_array = $data['vendor'.$i];
                    for($j=0; $j<$count; $j++)
                    {
                        $data['vendor_dummy'][$i][$j] = $temp_array[$j]['vendor_id'];
                    }
                    array_push($vdr,$data['vendor_dummy'][$i]);
                }
                $temp_array_1 =array();
                for($i=0; $i<count($vdr); $i++)
                {
                    $temp_array_1= array_merge($temp_array_1,$vdr[$i]);
                }
                $result_values = array_count_values($temp_array_1);
                $result_keys = array_keys($result_values,"$data_count");
                $vendors = DB::select('SELECT DISTINCT id as vendor_id,registered_name as vendor_name FROM vendor_details WHERE id IN (' . implode(',', array_map('intval', $result_keys)) . ')');
                $data['vendors'] = json_decode(json_encode($vendors), true);
                $jsonstring = json_encode($data['vendors']);
                echo $jsonstring;
            }
        }
        public function select_vendor_warehouses_all()
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
            if($_SERVER['REQUEST_METHOD']=='POST')
            {
                $equipment = json_decode($_POST['equipments']);
                $vendor_id = $_POST['vendor_id'];                
                $count = 0;
                $data_count = count($equipment);
                $data_count = $data_count-1;
                $vdr = array();
                for ($i=0; $i <$data_count ; $i++) 
                {
                    $data['vendor_dummy'] = array();
                    $equip = $equipment[$i];
                    $vendor_ids = DB::select("SELECT DISTINCT warehouse_id FROM vendor_products WHERE product_id = $equip AND vendor_id = $vendor_id");
                    $data['vendor'.$i] = json_decode(json_encode($vendor_ids), true); 
                    $count = count($data['vendor'.$i]);
                    $temp_array = $data['vendor'.$i];
                    for($j=0; $j<$count; $j++)
                    {
                        $data['vendor_dummy'][$i][$j] = $temp_array[$j]['warehouse_id'];
                    }
                    array_push($vdr,$data['vendor_dummy'][$i]);
                }
                $temp_array_1 =array();
                for($i=0; $i<count($vdr); $i++)
                {
                    $temp_array_1= array_merge($temp_array_1,$vdr[$i]);
                }
                $result_values = array_count_values($temp_array_1);
                $result_keys = array_keys($result_values,"$data_count");
                $vendors = DB::select('SELECT DISTINCT id as warehouse_id,wh_name as wh_name, wh_area as wh_area, wh_city as wh_city FROM vendor_warehouse WHERE id IN (' . implode(',', array_map('intval', $result_keys)) . ')');
                $data['vendors'] = json_decode(json_encode($vendors), true);
                $jsonstring = json_encode($data['vendors']);
                echo $jsonstring;
            }
        }
        public function select_product_brand_all()
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
            if($_SERVER['REQUEST_METHOD']=='POST')
            {
                $equipment = json_decode($_POST['equipments']);
                $vendor_id = $_POST['vendor_id'];
                $warehouse_id = $_POST['warehouse_id'];
                $data['brands']=array();
                foreach($equipment as $equip)
                {
                    $brands = DB::select("SELECT DISTINCT product_brands.id as brand_id, product_brands.brand_name as brand_name FROM product_brands,vendor_products WHERE vendor_products.product_id = $equip AND vendor_products.vendor_id = $vendor_id AND vendor_products.warehouse_id = $warehouse_id AND product_brands.id = vendor_products.product_brand");
                    $brands = json_decode(json_encode($brands), true);
                    array_push($data['brands'],$brands);
                }
                $jsonstring = json_encode($data['brands']);
                echo $jsonstring;
            }
        }
        //view all approved orders by vendor side or quali55care approved
        public function viewApprovedOrders()
        {
            return redirect('filterApprovedOrders/today');
        }
        public function filterApprovedOrders($filter_by)
        {
            if($filter_by =='today')
            {
                $date = date('d-m-Y');
                $data['start_date'] = date('Y-m-d');
                $data['end_date'] = date('Y-m-d');

                $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') = STR_TO_DATE('$date','%d-%m-%Y')";
                echo "<script>localStorage['filtered']='today';</script>";
            }
            elseif($filter_by =='yesterday')
            {
                $prevDate = date('d-m-Y',strtotime("-1 days"));
                $data['start_date'] = date('Y-m-d',strtotime("-1 days"));
                $data['end_date'] = date('Y-m-d',strtotime("-1 days"));
                $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') = STR_TO_DATE('$prevDate','%d-%m-%Y')";
                echo "<script>localStorage['filtered']='yesterday';</script>";
            }
            elseif($filter_by =='past_3_days')
            {
                $past_three_days = date('d-m-Y',strtotime("-2 days"));
                $data['start_date'] = date('Y-m-d',strtotime("-2 days"));
                $data['end_date'] = date('Y-m-d');
                $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') >= STR_TO_DATE('$past_three_days','%d-%m-%Y')";
                echo "<script>localStorage['filtered']='past_3_days';</script>";
            }
            elseif($filter_by =='week')
            {
                $past_three_days = date('d-m-Y',strtotime("-7 days"));
                $data['start_date'] = date('Y-m-d',strtotime("-7 days"));
                $data['end_date'] = date('Y-m-d');
                $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') >= STR_TO_DATE('$past_three_days','%d-%m-%Y')";
                echo "<script>localStorage['filtered']='week';</script>";
            }
            elseif($filter_by =='month')
            {
                $month = date('m-Y');
                $start_date_temp = '01-'.$month;
                $start_date = date('d-m-Y',strtotime($start_date_temp));
                $data['start_date'] = date('Y-m-d',strtotime($start_date));
                $end_date_temp = '31-'.$month;
                $end_date = date('d-m-Y',strtotime($end_date_temp));
                $data['end_date'] = date('Y-m-d',strtotime($end_date));
                $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y')";
                echo "<script>localStorage['filtered']='month';</script>";
            }
            elseif($filter_by == 'all')
            {
                $orderTypeNotIn = config('app.order_type');
                $orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";
                echo "<script>localStorage['filtered']='all';</script>";
                $get_approved_orders = DB::select("SELECT 
                                                    DISTINCT 
                                                    customer_details.customer_name as customer_name,
                                                    customer_details.primary_contact_no as contact_no,
                                                    del_orders.order_id as order_id,
                                                    del_orders.DelDate as DelDate,
                                                    del_orders.status as del_status,
                                                    order_details.status as status
                                                FROM 
                                                    order_details,del_orders,customer_details
                                                where 
                                                    order_details.status='Accepted'
                                                AND 
                                                    del_orders.order_approval_status='Approved'
                                                AND 
                                                    del_orders.status != 'Closed'
                                                AND 
                                                    order_details.order_id=del_orders.order_id
                                                AND     
                                                    order_details.customer_id = customer_details.cust_id 
                                                AND 
                                                    del_orders.deliverypickup NOT IN ($orderTypeNotIn)
                                                ORDER BY 
                                                    del_orders.order_id DESC");
                $data['get_approved_orders'] = json_decode(json_encode($get_approved_orders), true);
                //print_r($data);
                return view('OrderManagement/ViewApprovedOrders',$data);
            }
            $orderTypeNotIn = config('app.order_type');
            $orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";
            $get_approved_orders = DB::select("SELECT 
                                                    DISTINCT 
                                                    customer_details.customer_name as customer_name,
                                                    customer_details.primary_contact_no as contact_no,
                                                    del_orders.order_id as order_id,
                                                    del_orders.DelDate as DelDate,
                                                    del_orders.status as del_status,
                                                    order_details.status as status
                                                FROM 
                                                    order_details,del_orders,customer_details
                                                where 
                                                    order_details.status='Accepted'
                                                AND 
                                                    del_orders.order_approval_status='Approved'
                                                AND 
                                                    del_orders.status != 'Closed'
                                                AND 
                                                    order_details.order_id=del_orders.order_id
                                                AND     
                                                    order_details.customer_id = customer_details.cust_id 
                                                AND 
                                                    del_orders.deliverypickup NOT IN ($orderTypeNotIn)
                                                AND 
                                                    $whereClause
                                                ORDER BY 
                                                    del_orders.order_id DESC");
            $data['get_approved_orders'] = json_decode(json_encode($get_approved_orders), true);
            //print_r($data);
            return view('OrderManagement/ViewApprovedOrders',$data);
        }

        public function filterApprovedOrdersDWS()
        {
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];

            $data['start_date'] = $_POST['start_date'];
            $data['end_date'] = $_POST['end_date'];
            $start_date_d = date('d-m-Y',strtotime($start_date));
            $end_date_d = date('d-m-Y',strtotime($end_date));
            $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date_d','%d-%m-%Y') AND STR_TO_DATE('$end_date_d','%d-%m-%Y')";
            $orderTypeNotIn = config('app.order_type');
            $orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";
            $get_approved_orders = DB::select("SELECT 
                                                    DISTINCT 
                                                    customer_details.customer_name as customer_name,
                                                    customer_details.primary_contact_no as contact_no,
                                                    del_orders.order_id as order_id,
                                                    del_orders.DelDate as DelDate,
                                                    del_orders.status as del_status,
                                                    order_details.status as status
                                                FROM 
                                                    order_details,del_orders,customer_details
                                                where 
                                                    order_details.status='Accepted'
                                                AND 
                                                    del_orders.order_approval_status='Approved'
                                                AND 
                                                    del_orders.status != 'Closed'
                                                AND 
                                                    order_details.order_id=del_orders.order_id
                                                AND     
                                                    order_details.customer_id = customer_details.cust_id 
                                                AND 
                                                    del_orders.deliverypickup NOT IN ($orderTypeNotIn)
                                                AND 
                                                    $whereClause
                                                ORDER BY 
                                                    del_orders.order_id DESC");
            $data['get_approved_orders'] = json_decode(json_encode($get_approved_orders), true);
            //print_r($data);
            return view('OrderManagement/ViewApprovedOrders',$data);
        }
        //View All rejected orders 
        public function viewRejectedOrders()
        {
            return redirect('filterRejectedOrders/today');
        }
        public function filterRejectedOrders($filter_by)
        {
            if($filter_by =='today')
            {
                $date = date('d-m-Y');
                $data['start_date'] = date('Y-m-d');
                $data['end_date'] = date('Y-m-d');
                $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') = STR_TO_DATE('$date','%d-%m-%Y')";
                echo "<script>localStorage['filtered']='today';</script>";
            }
            elseif($filter_by =='yesterday')
            {
                $prevDate = date('d-m-Y',strtotime("-1 days"));
                $data['start_date'] = date('Y-m-d',strtotime("-1 days"));
                $data['end_date'] = date('Y-m-d');
                $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') = STR_TO_DATE('$prevDate','%d-%m-%Y')";
                echo "<script>localStorage['filtered']='yesterday';</script>";
            }
            elseif($filter_by =='past_3_days')
            {
                $past_three_days = date('d-m-Y',strtotime("-2 days"));
                $data['start_date'] = date('Y-m-d',strtotime("-2 days"));
                $data['end_date'] = date('Y-m-d');
                $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') >= STR_TO_DATE('$past_three_days','%d-%m-%Y')";
                echo "<script>localStorage['filtered']='past_3_days';</script>";
            }
            elseif($filter_by =='week')
            {
                $past_three_days = date('d-m-Y',strtotime("-7 days"));
                $data['start_date'] = date('Y-m-d',strtotime("-7 days"));
                $data['end_date'] = date('Y-m-d');
                $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') >= STR_TO_DATE('$past_three_days','%d-%m-%Y')";
                echo "<script>localStorage['filtered']='week';</script>";
            }
            elseif($filter_by =='month')
            {
                $month = date('m-Y');
                $start_date_temp = '01-'.$month;
                $start_date = date('d-m-Y',strtotime($start_date_temp));
                $end_date_temp = '31-'.$month;
                $end_date = date('d-m-Y',strtotime($end_date_temp));
                $data['start_date'] = date('Y-m-d',strtotime($start_date_temp));
                $data['end_date'] = date('Y-m-d',strtotime($end_date_temp));
                $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y')";
                echo "<script>localStorage['filtered']='month';</script>";
            }
            elseif($filter_by == 'all')
            {
                $orderTypeNotIn = config('app.order_type');
                $orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";
                $get_rejected_orders = DB::select("SELECT DISTINCT customer_details.customer_name as customer_name,
                customer_details.primary_contact_no as contact_no,
                del_orders.order_id as order_id,
                order_details.status as status
                FROM order_details,del_orders,customer_details 
                where order_details.status='Rejected' 
                AND del_orders.order_approval_status='Pending' 
                AND order_details.order_id=del_orders.order_id
                AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)
                AND order_details.customer_id = customer_details.cust_id ");
                $data['get_rejected_orders'] = json_decode(json_encode($get_rejected_orders), true);
                //print_r($data);
                return view('OrderManagement/ViewRejectedOrders',$data);
            }
            $orderTypeNotIn = config('app.order_type');
            $orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";
            $get_rejected_orders = DB::select("SELECT DISTINCT customer_details.customer_name as customer_name,
                customer_details.primary_contact_no as contact_no,
                del_orders.order_id as order_id,
                order_details.status as status
                FROM order_details,del_orders,customer_details 
                where order_details.status='Rejected' 
                AND del_orders.order_approval_status='Pending' 
                AND order_details.order_id=del_orders.order_id
                AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)
                AND order_details.customer_id = customer_details.cust_id AND $whereClause ");
                $data['get_rejected_orders'] = json_decode(json_encode($get_rejected_orders), true);
                //print_r($data);
                return view('OrderManagement/ViewRejectedOrders',$data);
        }

        public function filterRejectedOrdersDWS()
        {
            $data['start_date'] = $_POST['start_date'];
            $data['end_date'] = $_POST['end_date'];
            $start_date = date('d-m-Y',strtotime($_POST['start_date']));
            $end_date = date('d-m-Y',strtotime($_POST['end_date']));
            $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y')";
            $orderTypeNotIn = config('app.order_type');
            $orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";
            $get_rejected_orders = DB::select("SELECT DISTINCT customer_details.customer_name as customer_name,
                customer_details.primary_contact_no as contact_no,
                del_orders.order_id as order_id,
                order_details.status as status
                FROM order_details,del_orders,customer_details 
                where order_details.status='Rejected' 
                AND del_orders.order_approval_status='Pending' 
                AND order_details.order_id=del_orders.order_id
                AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)
                AND order_details.customer_id = customer_details.cust_id AND $whereClause ");
                $data['get_rejected_orders'] = json_decode(json_encode($get_rejected_orders), true);
                //print_r($data);
                return view('OrderManagement/ViewRejectedOrders',$data);
        }
        //view order information on click of view details
        public function viewApprovedOrderInfo($order_id,$order_type)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
             return redirect()->to($url);
            }
            if($order_type == 'Delivery')
            {
                $orderTypeNotIn = config('app.order_type');
                $orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";
                $get_order_info = DB::select("SELECT products.product_name as product_name,
                                                    order_details.product_qty as product_quantity,
                                                    order_details.product_rent as product_rent,
                                                    order_details.product_deposite as product_deposite,
                                                    order_details.sale_rental as sale_rental,
                                                    order_details.unique_id as unique_id,
                                                    order_details.status as status,
                                                    order_details.customer_id as customer_id,
                                                    order_details.id as order_details_id,
                                                    order_details.vendor_warehouse_id as warehouse_id,
                                                    order_details.upgraded as upgraded,
                                                    vendor_details.registered_name as vendor_name,
                                                    vendor_warehouse.wh_name as warehouse_name,
                                                    vendor_warehouse.wh_area as warehouse_area,
                                                    vendor_warehouse.wh_city as warehouse_city,
                                                    del_orders.status as delivery_status,
                                                    products.id as product_id,
                                                    del_orders.product_delivered as product_image
                                            FROM order_details,del_orders,products,vendor_details,vendor_warehouse
                                            where order_details.order_id= $order_id 
                                            -- AND order_details.status = 'Accepted'
                                            AND del_orders.order_id = order_details.order_id 
                                            AND order_details.product_id = products.id 
                                            AND order_details.vendor_id = vendor_details.id
                                            AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)
                                            AND order_details.vendor_warehouse_id = vendor_warehouse.id");
                $data['get_order_info'] = json_decode(json_encode($get_order_info),true);
                $customer_id = $data['get_order_info'][0]['customer_id']; 
                //get all customer information
                $get_customer_info = DB::select("SELECT * FROM customer_details WHERE cust_id ='$customer_id' ");
                $data['customer_info'] = json_decode(json_encode($get_customer_info),true);
                //get activity log information for assign at and assign by
                $data['getActitvityInfo'] = DB::table('activity_log')
                                            ->where([['key_id','=',$order_id],['order_type','=','DO'],['operation','=','Order Assigned']])
                                            ->whereIn('fields',['assign_at','assign_by'])
                                            ->get()->toArray();
                return view('OrderManagement/ViewApprovedOrderInfo',$data);
            }
            else if($order_type == 'Pick Up')
            {
                $pickup_order_id = $order_id;
                $orderTypeNotIn = config('app.order_type');
                $get_order_info = DB::table('pickups')
                                        ->join('vendor_details','pickups.drop_vendor_id','=','vendor_details.id')
                                        ->join('vendor_warehouse','pickups.drop_warehouse_id','=','vendor_warehouse.id')
                                        ->join('products','pickups.product_id','=','products.id')
                                        ->join('order_details','pickups.order_details_id','=','order_details.id')
                                        ->join('del_orders','pickups.pickup_order_id','=','del_orders.order_id')
                                        ->select(
                                            'del_orders.*',
                                            'del_orders.product_delivered as product_image',
                                            'products.product_name',
                                            'order_details.product_id',
                                            'order_details.order_id',
                                            'order_details.product_rent',
                                            'order_details.product_deposite',
                                            'order_details.unique_id',
                                            'vendor_warehouse.wh_name',
                                            'vendor_warehouse.wh_area',
                                            'vendor_warehouse.wh_city',
                                            'vendor_details.registered_name',
                                            'pickups.id as pickups_prod_id'
                                        )
                                        ->where('pickups.pickup_order_id',$order_id)
                                        ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                                        ->get()
                                        ->toArray();
                return view('OrderManagement/ViewApprovedOrderInfoPU',compact('get_order_info','pickup_order_id'));
            }
            
        }
        public function viewRejectedOrderInfo($order_id)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
             return redirect()->to($url);
            }
            $orderTypeNotIn = config('app.order_type');
            $orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";
            $get_order_info = DB::select("SELECT products.product_name as product_name,
                                                order_details.product_qty as product_quantity,
                                                order_details.product_rent as product_rent,
                                                order_details.product_deposite as product_deposite,
                                                order_details.sale_rental as sale_rental,
                                                order_details.unique_id as unique_id,
                                                order_details.status as status,
                                                order_details.customer_id as customer_id,
                                                order_details.id as order_details_id,
                                                vendor_details.registered_name as vendor_name

                                        FROM order_details,del_orders,products,vendor_details 
                                        where order_details.order_id= $order_id 
                                        AND order_details.status = 'Rejected'
                                        AND del_orders.order_id = order_details.order_id 
                                        AND order_details.product_id = products.id 
                                        AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)
                                        AND order_details.vendor_id = vendor_details.id");
            $data['get_order_info'] = json_decode(json_encode($get_order_info),true);
            $customer_id = $data['get_order_info'][0]['customer_id']; 
            //get all customer information
            $get_customer_info = DB::select("SELECT * FROM customer_details WHERE cust_id ='$customer_id' ");
            $data['customer_info'] = json_decode(json_encode($get_customer_info),true);
            //print_r($data['get_order_info']);
            return view('OrderManagement/ViewRejectedOrderInfo',$data);
        }
        /* Using Get Method */
        public function close_order(Request $request)
        {
            $orderDetailsId = $request->get('order_details_id');
            $orderProductRent = $request->get('product_rent');
            $orderProductDeposit = $request->get('product_deposit');
            $orderProductTransport = $request->get('product_transport');
            

            $reasons = config('app.order_closed_reason');
            $order_id = $request->get('order_id');
            $shipping_first_name = DB::table('del_orders')->select('shipping_first_name')->where('order_id',$order_id)->get()->toArray();
            $shipping_first_name = $shipping_first_name[0]->shipping_first_name;
            $closeReason = $request->get('close_reason');
            $closeRemark = $request->get('close_remark');
            $reason = $reasons[$closeReason];
            // $this->order_closed_mail($order_id,$reason,$closeRemark,$shipping_first_name);

            $del_orders = DelOrders::where('order_id',$order_id)->get()->toArray();
            
            $timestamp = date("d M, h:i A");
            $comment = "[".$timestamp."][".session('username')."]:".$closeRemark;
            if(isset($del_orders[0]['comment'])){
                $comment = $del_orders[0]['comment']."\n".$comment;
            }
            
            // print_r($del_orders);
            if($del_orders[0]['deliverypickup'] == 'Delivery')
            {
                $orderTotal = array_sum($orderProductRent) + array_sum($orderProductDeposit) + array_sum($orderProductTransport);
                $lead_id = $del_orders[0]['lead_id'];
                
                $order_details = OrderDetails::where('order_id',$order_id)->get()->toArray();
                foreach($order_details as $order_detail)
                {
                    $vendor_product_id = $order_detail['vendor_product_id'];
                    $vendor_product_details_id = $order_detail['vendor_product_details_id'];
                    $vendor_rented_products_id = $order_detail['rented_product_id'];
                    $qty = $order_detail['product_qty'];

                    VendorProductDetails::where('id',$vendor_product_details_id)->update(['availability_status'=>0,'current_location'=>2]);
                    VendorRentedProducts::where('id',$vendor_rented_products_id)->delete();

                    $update = DB::update("UPDATE vendor_products SET vendor_products.product_quantity = vendor_products.product_quantity+$qty WHERE id=$vendor_product_id");
                }
                //update product rent and other
                foreach ($orderDetailsId as $key => $id) {
                    // $updateData = [
                    //     'product_rent'=>$orderProductRent[$key],
                    //     'product_deposite'=>$orderProductDeposit[$key],
                    //     'transport'=>$orderProductTransport[$key]
                    // ];
                    // DB::table('order_details')->where('id',$id)->update($updateData);
                    $details = DB::table('order_details')->select('id','order_id','product_rent','product_deposite','transport')->where('id',$id)->first();
                    if($details->product_rent != $orderProductRent[$key]){
                        // Rental
                        if($details->product_rent > $orderProductRent[$key]){
                            DB::table('cr_dr_note')->insert([
                                'order_id'=>$details->order_id,
                                'order_details_id'=>$details->id,
                                'crdrtype'=>'Cr',
                                'intype'=>'R',
                                'amount'=>($details->product_rent - $orderProductRent[$key]),
                                'createdby'=>session('username')
                            ]);
                        }
                        else if($details->product_rent < $orderProductRent[$key]){
                            DB::table('cr_dr_note')->insert([
                                'order_id'=>$details->order_id,
                                'order_details_id'=>$details->id,
                                'crdrtype'=>'Dr',
                                'intype'=>'R',
                                'amount'=>($orderProductRent[$key] - $details->product_rent),
                                'createdby'=>session('username')
                            ]);
                        }
                        else
                        {
                            continue;
                        }
                        // Deposit
                        if($details->product_deposite > $orderProductDeposit[$key]){
                            DB::table('cr_dr_note')->insert([
                                'order_id'=>$details->order_id,
                                'order_details_id'=>$details->id,
                                'crdrtype'=>'Cr',
                                'intype'=>'D',
                                'amount'=>($details->product_deposite - $orderProductDeposit[$key]),
                                'createdby'=>session('username')
                            ]);
                        }
                        else if($orderProductDeposit[$key] < $details->product_deposite){
                            DB::table('cr_dr_note')->insert([
                                'order_id'=>$details->order_id,
                                'order_details_id'=>$details->id,
                                'crdrtype'=>'Dr',
                                'intype'=>'D',
                                'amount'=>($product_rent - $details->product_rent),
                                'createdby'=>session('username')
                            ]);
                        }
                        else
                        {
                            continue;
                        }
                        // Transport
                        if($details->transport > $orderProductTransport[$key]){
                            DB::table('cr_dr_note')->insert([
                                'order_id'=>$details->order_id,
                                'order_details_id'=>$details->id,
                                'crdrtype'=>'Cr',
                                'intype'=>'T',
                                'amount'=>($details->transport - $orderProductTransport[$key]),
                                'createdby'=>session('username')
                            ]);
                        }
                        else if($details->transport < $orderProductTransport[$key]){
                            DB::table('cr_dr_note')->insert([
                                'order_id'=>$details->order_id,
                                'order_details_id'=>$details->id,
                                'crdrtype'=>'Dr',
                                'intype'=>'T',
                                'amount'=>($orderProductTransport[$key] - $details->transport),
                                'createdby'=>session('username')
                            ]);
                        }
                        else
                        {
                            continue;
                        }
                    }
                }
                OrderDetails::where('order_id',$order_id)->update(['current_status'=>'Cancel']);
                DelOrders::where('order_id',$order_id)->update(['status'=>'Cancel','cancellation_reason'=>$closeReason,'comment'=>$comment, 'TotalAmt'=>$orderTotal]);
                if(!DB::table('del_orders')->where('lead_id',$lead_id)->where('deliverypickup','Delivery')->whereNotIn('status',['Cancel'])->exists())
                {
                    lead::where('id',$lead_id)->update(['lead_status'=>'Converted']);
                }
                $insertLogData = [
                    'order_type'=>'DO',
                    'key_id'=>$order_id,
                    'operation'=>'Order Cancelled',
                    'fields'=>'status',
                    'old_value'=>$del_orders[0]['status'],
                    'new_value'=>'Cancel',
                    'updated_by'=>session('username')
                ];
                ActivityLog::insert($insertLogData);
                // OrderDetails::where('order_id',$order_id)->delete();
                // DelOrders::where('order_id',$order_id)->delete();
                // return "Deleted";
                $order_details_ids = DB::table('order_details')->where('order_id',$order_id)->get()->pluck('id');
                foreach($order_details_ids as $id){
                    DB::table('vendor_inventory_mgmt')->where('details_id',$id)->where('state','in')->update(['flag'=>'Inactive']);
                }
            }
            elseif($del_orders[0]['deliverypickup'] == 'Pick Up')
            {
                $pickup_details = Pickup::where('pickup_order_id',$order_id)->get()->toArray();
                foreach($pickup_details as $pickup_detail)
                {
                    $order_details_id = $pickup_detail['order_details_id'];
                    OrderDetails::where('id',$order_details_id)->update(['current_status'=>'Pending']);
                    VirtualVdrInventoryMgmt::where('order_details_id',$order_details_id)->update(['status'=>'2','updated_by'=>session('username')]);
                    $order_detail = OrderDetails::where('id',$order_details_id)->get()->toArray();
                    $qty = $order_detail[0]['product_qty'];
                    $inventory_id = $order_detail[0]['unique_id'];
                    $vendor_product_id = $order_detail[0]['vendor_product_id'];
                    $update = DB::update("UPDATE vendor_products SET vendor_products.product_quantity = vendor_products.product_quantity-$qty WHERE id=$vendor_product_id");
                    $vendor_product_details_id = $order_detail[0]['vendor_product_details_id'];
                    if(VendorRentedProducts::where('unique_id',$inventory_id)->where('status','On Rent')->exists())
                    {
                        $rented_prod_details = VendorRentedProducts::where('unique_id',$inventory_id)->where('status','On Rent')->get()->toArray();
                        $rented_prod_id =  $rented_prod_details[0]['id'];
                        $order_details_del = OrderDetails::where('rented_product_id',$rented_prod_id)->get()->toArray();
                        if(isset($order_details_del[0]))
                        {
                            $del_order_id = $order_details_del[0]['order_id'];
                            // $response = $this->close_deliverey_fun($del_order_id);
                        }
                    }
                    VendorProductDetails::where('id',$vendor_product_details_id)->update(['availability_status'=>0,'current_location'=>0]);
                    $rented_product_id = $order_detail[0]['rented_product_id'];
                    VendorRentedProducts::where('id',$rented_product_id)->update(['status'=>'On Rent']);
                }
                // DelOrders::where('order_id',$order_id)->delete();
                // Pickup::where('pickup_order_id',$order_id)->delete();
                DelOrders::where('order_id',$order_id)->update(['status'=>'Cancel','TotalAmt'=>'0','cancellation_reason'=>$closeReason,'comment'=>$comment]);
                Pickup::where('pickup_order_id',$order_id)->update(['status'=>'Cancel']);
                $insertLogData = [
                    'order_type'=>'PO',
                    'key_id'=>$order_id,
                    'operation'=>'Order Cancelled',
                    'fields'=>'status',
                    'old_value'=>$del_orders[0]['status'],
                    'new_value'=>'Cancel',
                    'updated_by'=>session('username')
                ];
                ActivityLog::insert($insertLogData);
                $order_details_ids = Pickup::where('pickup_order_id',$order_id)->get()->pluck('id');
                foreach($order_details_ids as $id){
                    DB::table('vendor_inventory_mgmt')->where('details_id',$id)->where('state','out')->update(['flag'=>'Inactive']);
                }
            }
            elseif($del_orders[0]['deliverypickup'] == 'Collection')
            {
                $renewal_details = Renewal::where('collection_order_id',$order_id)->get()->toArray();
                foreach($renewal_details as $renewal_detail)
                {
                    // if($del_orders[0]['status'] == 'Collected')
                    // {

                    //     $pickup_date = DB::table('renewals')->select('renewals.start_date')->where('order_details_id',$order_details_id)->first()->start_date;
                    //     OrderDetails::where('id',$order_details_id)->update(['current_status'=>'Pending','pickup_date'=>$pickup_date]);
                    // }
                    // else
                    // {
                    //     OrderDetails::where('id',$order_details_id)->update(['current_status'=>'Pending']);
                    // }
                    $order_details_id = $renewal_detail['order_details_id'];
                    OrderDetails::where('id',$order_details_id)->update(['current_status'=>'Pending']);
                }
                // DelOrders::where('order_id',$order_id)->delete();
                // Renewal::where('collection_order_id',$order_id)->delete();
                DelOrders::where('order_id',$order_id)->update(['status'=>'Cancel','TotalAmt'=>'0','cancellation_reason'=>$closeReason,'comment'=>$comment]);
                Renewal::where('collection_order_id',$order_id)->update(['status'=>'Cancel']);
                $insertLogData = [
                    'order_type'=>'CO',
                    'key_id'=>$order_id,
                    'operation'=>'Order Cancelled',
                    'fields'=>'status',
                    'old_value'=>$del_orders[0]['status'],
                    'new_value'=>'Cancel',
                    'updated_by'=>session('username')
                ];
                //ActivityLog::insert($insertLogData);
                DB::table('activity_log')->insert($insertLogData);
                // dd($insertLogData);
            }
            $request->session()->flash('message', 'Order Closed successfully');
            return redirect()->back();
            // return $order_details;
        }
        public function close_deliverey_fun($order_id)
        {
            // $order_id = $_POST['order_id'];/
            $orderTypeNotIn = config('app.order_type');
            $del_orders = DelOrders::where('order_id',$order_id)->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)->get()->toArray();
            $lead_id = $del_orders[0]['lead_id'];
            lead::where('id',$lead_id)->update(['lead_status'=>'Converted']);
            $order_details = OrderDetails::where('order_id',$order_id)->get()->toArray();
            foreach($order_details as $order_detail)
            {
                $vendor_product_id = $order_detail['vendor_product_id'];
                $vendor_product_details_id = $order_detail['vendor_product_details_id'];
                $vendor_rented_products_id = $order_detail['rented_product_id'];
                $qty = $order_detail['product_qty'];

                VendorProductDetails::where('id',$vendor_product_details_id)->update(['availability_status'=>0,'current_location'=>2]);
                VendorRentedProducts::where('id',$vendor_rented_products_id)->delete();

                $update = DB::update("UPDATE vendor_products SET vendor_products.product_quantity = vendor_products.product_quantity+$qty WHERE id=$vendor_product_id");

            }
            OrderDetails::where('order_id',$order_id)->update(['current_status'=>'Cancel']);
            DelOrders::where('order_id',$order_id)->update(['status'=>'Cancel']);
            // return "Deleted";
        }
        public function close_delivery()
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
             return redirect()->to($url);
            }
            $order_id = $_POST['order_id']; 
            $reason = $_POST['cancellation_reason'];
            $update_data = [
                'status'=>'Closed',
                'cancellation_reason'=>$reason
            ];
            $delOrders = new DelOrders();
            $update_query = $delOrders->where('order_id',$order_id)->update($update_data);
            $lead_ids = DB::select("SELECT lead_id FROM del_orders WHERE order_id = $order_id");
            $lead_details = json_decode(json_encode($lead_ids), true);
            $update_data = [
                'lead_status'=>'Closed',
                'comment'=>$reason
            ];
            $leads = new Lead();
            $update_query = $leads->where('id',$lead_details[0]['lead_id'])->update($update_data);
            return redirect('/approved_orders')->with('message','Order Closed Succesfully');
        }
        //Delete product from order
        public function DeleteOrderProduct($order_details_id,$product_id)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
             return redirect()->to($url);
            }
            //get product name
            $orderTypeNotIn = config('app.order_type');
            $orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";
            $del_orders = new DelOrders();
            $lead_table = new lead();
            $VendorProducts = new VendorProducts();
            $OrderDetails = new OrderDetails();
            $VendorRentedProducts = new VendorRentedProducts();
            $get_product_data = DB::select("SELECT 
                                            products.product_name as product_name,
                                            del_orders.line_item_1 as line_item_1,
                                            order_details.order_id as order_id,
                                            order_details.vendor_product_id as vendor_product_id,
                                            order_details.product_qty as order_product_quantity,
                                            order_details.rented_product_id as rented_product_id,
                                            del_orders.lead_id as lead_id,
                                            vendor_products.product_quantity as vendor_product_quantity
                                        FROM 
                                            products,del_orders,order_details,vendor_products
                                        where 
                                            products.id=$product_id 
                                        AND del_orders.order_id=order_details.order_id
                                        AND order_details.id = $order_details_id
                                        AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)
                                        AND order_details.vendor_product_id=vendor_products.id ");
            $get_product_data = json_decode(json_encode($get_product_data), true);
            $product_name = $get_product_data[0]['product_name'];
            $del_orders_products = $get_product_data[0]['line_item_1']; //existed products in del_orders table
            $order_id = $get_product_data[0]['order_id'];
            $lead_id = $get_product_data[0]['lead_id'];
            $vendor_product_id = $get_product_data[0]['vendor_product_id'];
            $rented_product_id = $get_product_data[0]['rented_product_id'];
            $order_product_quantity = $get_product_data[0]['order_product_quantity'];
            $vendor_product_quantity = $get_product_data[0]['vendor_product_quantity'];

            //update in del_orders table 
            
            $updated_products = str_replace($product_name,"",$del_orders_products);
            $update_del_orders=[
                'line_item_1' => $updated_products
            ];
            $del_orders->where('order_id',$order_id)->update($update_del_orders);
            
            //delete from leads table
            $get_lead_data = DB::select("SELECT * FROM leads where id=$lead_id");
            $get_lead_data = json_decode(json_encode($get_lead_data), true);
            $lead_value = $get_lead_data[0]['lead_value'];
            $equipment_requirement = json_decode($get_lead_data[0]['equipment_requirement']);
            $equipment_qty = json_decode($get_lead_data[0]['equipment_qty']);
            $del_date = json_decode($get_lead_data[0]['del_date']);
            $sale_rental = json_decode($get_lead_data[0]['sale_rental']);
            $offered_rent = json_decode($get_lead_data[0]['offered_rent']);
            $offered_rent_total = json_decode($get_lead_data[0]['offered_rent_total']);
            $deposite = json_decode($get_lead_data[0]['deposite']);
            $deposite_total = json_decode($get_lead_data[0]['deposite_total']);
            $transport = json_decode($get_lead_data[0]['transport']);
            
            $get_key = null;
            $temp_equipment_requirement=$equipment_requirement;
            $temp_equipment_qty = $equipment_qty;
            $temp_del_date = $del_date;
            $temp_sale_rental = $sale_rental;
            $temp_offered_rent = $offered_rent;
            $temp_offered_rent_total = $offered_rent_total;
            $temp_deposite = $deposite;
            $temp_deposite_total = $deposite_total;
            $temp_transport = $transport;
            foreach($equipment_requirement as $key => $value)
            {
                if($product_id==$value)
                {
                    $get_key=$key;
                    unset($temp_equipment_requirement[$key]);
                }
            }
            $total_product_amt = $offered_rent[$get_key]+$deposite[$get_key]+$transport[$get_key];
            $temp_lead_value = $lead_value-$total_product_amt;
            unset($temp_equipment_qty[$key]);
            unset($temp_del_date[$key]);
            unset($temp_del_date[$key]);
            unset($temp_sale_rental[$key]);
            unset($temp_offered_rent[$key]);
            unset($temp_offered_rent_total[$key]);
            unset($temp_deposite[$key]);
            unset($temp_deposite_total[$key]);
            unset($temp_transport[$key]);
            
            $update_lead_data=[
                'equipment_requirement' => json_encode(array_values($temp_equipment_requirement)),
                'equipment_qty' => json_encode(array_values($temp_equipment_qty)),
                'del_date' => json_encode(array_values($temp_del_date)),
                'offered_rent' => json_encode(array_values($temp_offered_rent)),
                'sale_rental' => json_encode(array_values($temp_sale_rental)),
                'offered_rent_total' => json_encode(array_values($temp_offered_rent_total)),
                'deposite' => json_encode(array_values($temp_deposite)),
                'deposite_total' => json_encode(array_values($temp_deposite_total)),
                'transport' => json_encode(array_values($temp_transport)),
                'lead_value' => $temp_lead_value
            ];
            $lead_table->where('id',$lead_id)->update($update_lead_data);

            //update vendor product quantity
            $final_qty = $vendor_product_quantity+$order_product_quantity;
            $update_vendor_products = [
                'product_quantity' => $final_qty
            ];
            $VendorProducts->where('id',$vendor_product_id)->update($update_vendor_products);

            //remove rented id from rented table
            if(isset($rented_product_id))
            {
                $VendorRentedProducts->where('id',$rented_product_id)->delete();
            }

            //Delete or update order_details table row
            // $OrderDetails->where('id',$order_details_id)->delete();
            $OrderDetails->where('id',$order_details_id)->update(['current_status'=>'Cancel']);
            
            return redirect()->back()->with('delete','deleted successfully.')
                                    ->with('product_name',$product_name);

        }
        //date filter search for order mgmt
        public function DateFilter($type)
        {
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            if($type=='orders')
            {
                $isLoggedIn = $this->isLoggedIn();
                if($isLoggedIn == 'false')
                {
                    $url = url('/');
                    return redirect()->to($url);
                }
                $date = date('Y-m-d');
                if(session('role') == "superuser")
                {
                    $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress' ) AND leads.lead_owner = user.id AND leads.creation_date BETWEEN '$start_date' AND '$end_date' ORDER BY leads.creation_date DESC");
                }
                elseif(session('role') == "admin")
                {
                    $user_city = session('user_city');
                    $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated') AND customer_details.citygroup='$user_city' AND leads.lead_owner = user.id AND leads.creation_date BETWEEN '$start_date' AND '$end_date' ORDER BY leads.creation_date DESC");
                }
                elseif(session('role') == "user")
                {
                    $user_city = session('user_city');
                    $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated') AND customer_details.citygroup='$user_city' AND leads.lead_owner = user.id AND leads.creation_date BETWEEN '$start_date' AND '$end_date' ORDER BY leads.creation_date DESC");
                }

                //$leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated') ORDER BY leads.creation_date DESC");
                //echo "SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated') ORDER BY leads.creation_date DESC";
                $data['lead_details'] = json_decode(json_encode($leads), true);
                for ($i=0; $i < count($data['lead_details']); $i++) 
                { 
                    $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
                    $equipement_details = array();
                    for ($j=0; $j <count($product); $j++) 
                    { 
                        $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
                        $product_details = json_decode(json_encode($product_details), true);
                        array_push($equipement_details,$product_details[0]['product_name']);
                    }
                    $equipements = json_encode($equipement_details);
                    $data['lead_details'][$i]['equipment_requirement'] = $equipements;
                }
                //print_r($data['lead_details']);
                echo "<script>localStorage['filtered']='today';</script>";
                return view('OrderManagement/viewAllLeads',$data);
            }
            elseif($type=='pending_asgmt')
            {
                $isLoggedIn = $this->isLoggedIn();
                if($isLoggedIn == 'false')
                {
                    $url = url('/');
                return redirect()->to($url);
                }
                $date = date('Y-m-d');
                if(session('role') == "superuser")
                {
                    $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted'  AND leads.lead_owner = user.id AND leads.creation_date BETWEEN '$start_date'AND '$end_date' ORDER BY leads.creation_date DESC");
                }
                elseif(session('role') == "admin")
                {
                    $user_city = session('user_city');
                    $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' AND customer_details.citygroup='$user_city'  AND leads.lead_owner = user.id AND leads.creation_date BETWEEN '$start_date'AND '$end_date'  ORDER BY leads.creation_date DESC");
                }
                elseif(session('role') == "user")
                {
                    $user_city = session('user_city');
                    $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' AND customer_details.citygroup='$user_city' AND leads.lead_owner = user.id AND leads.creation_date BETWEEN '$start_date'AND '$end_date'  ORDER BY leads.creation_date DESC");
                }

                //$leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated') ORDER BY leads.creation_date DESC");
                //echo "SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated') ORDER BY leads.creation_date DESC";
                $data['lead_details'] = json_decode(json_encode($leads), true);
                for ($i=0; $i < count($data['lead_details']); $i++) 
                { 
                    $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
                    $equipement_details = array();
                    for ($j=0; $j <count($product); $j++) 
                    { 
                        $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
                        $product_details = json_decode(json_encode($product_details), true);
                        array_push($equipement_details,$product_details[0]['product_name']);
                    }
                    $equipements = json_encode($equipement_details);
                    $data['lead_details'][$i]['equipment_requirement'] = $equipements;
                }
                //print_r($data['lead_details']);
                echo "<script>localStorage['filtered']='today';</script>";
                $Countlead = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' ORDER BY leads.creation_date DESC");
                $count1 = count(json_decode(json_encode($Countlead),true));
                $data['count1'] = $count1;
                return view('OrderManagement/pendingAssignment',$data);
            }
            elseif($type=='vendor_pending_approval')
            {
                $isLoggedIn = $this->isLoggedIn();
                if($isLoggedIn == 'false')
                {
                    $url = url('/');
                return redirect()->to($url);
                }
                $today = date('d-m-Y');
                $start_date = date('d-m-Y',strtotime($start_date));
                $end_date = date('d-m-Y',strtotime($end_date));
                $orderTypeNotIn = config('app.order_type');
                $orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";
                $temp_order_details = DB::select("SELECT * FROM del_orders,order_details WHERE del_orders.order_id=order_details.order_id AND del_orders.order_approval_status='Pending' AND del_orders.status != 'Cancel' AND del_orders.DelDate BETWEEN '$start_date' AND '$end_date' AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)");
                //echo "SELECT * FROM del_orders,order_details WHERE del_orders.order_id=order_details.order_id AND del_orders.order_approval_status='Pending' AND del_orders.DelDate BETWEEN '$start_date' AND '$end_date'"; 
                $data['temp_order_details'] = json_decode(json_encode($temp_order_details),true);
                $prev_order_id = 0;
                $count_status = 1;
                $status = null;
                $data['final_array'] = array();
                $order_details_array = array();
                foreach($data['temp_order_details'] as $order_detail)
                {
                    if($order_detail['order_id'] == $prev_order_id)
                    {
                            if($status=='Accepted')
                            {
                                    $count_status = $count_status + 1;
                                    $temp_data['DelDate'] = $order_detail['DelDate'];
                                    $temp_data['order_id'] = $order_detail['order_id'];
                                    $temp_data['lead_id'] = $order_detail['lead_id'];
                                    $temp_data['shipping_first_name'] = $order_detail['shipping_first_name'];
                                    $temp_data['mobileno'] = $order_detail['mobileno'];
                                    $temp_data['status'] = $order_detail['status'];         
                                    $count = count($order_details_array[$prev_order_id]);
                                    $order_details_array[$prev_order_id][$count] = $temp_data;            
                                    $order_details_array[$prev_order_id]['count'] = $count_status;
                            }
                            $status = $order_detail['status'];
                    }
                    else
                    {
                        $count_status = 1;
                        if($status=='Accepted')
                        {
                            $prev_order_id = $order_detail['order_id'];
                            $temp_data['DelDate'] = $order_detail['DelDate'];
                            $temp_data['order_id'] = $order_detail['order_id'];
                            $temp_data['lead_id'] = $order_detail['lead_id'];
                            $temp_data['shipping_first_name'] = $order_detail['shipping_first_name'];
                            $temp_data['mobileno'] = $order_detail['mobileno'];
                            $temp_data['status'] = $order_detail['status'];                        
                            $order_details_array[$prev_order_id][0] = $temp_data;
                            $order_details_array[$prev_order_id]['count'] = $count_status;
                            $count_status = $count_status + 1;
                        }
                        $status = $order_detail['status'];
                    }
                }
                foreach($order_details_array as $order_detail)
                {
                    $count=count($order_detail);
                    if($order_detail['count'] == $count)
                    {
                        $lead_id = $order_detail[0]['lead_id'];
                        $order_id = $order_detail[0]['order_id'];
                        DB::UPDATE("UPDATE del_orders SET order_approval_status = 'Approved' WHERE order_id = $order_id");
                    }
                }
                $orderTypeNotIn = config('app.order_type');
                $orderTypeNotIn ="'".implode("','",$orderTypeNotIn)."'";
                $order_details = DB::select("SELECT * FROM del_orders WHERE  del_orders.order_approval_status='Pending' AND del_orders.status != 'Cancel' AND del_orders.DelDate BETWEEN '$start_date' AND '$end_date' AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)");
                $data['order_details'] = json_decode(json_encode($order_details),true);
                echo "<script>localStorage['filtered']='today';</script>";        
                return view('/OrderManagement/pending_for_vendor_approval',$data);
            }
            // elseif($type=='approved_orders')
            // {
            //     $isLoggedIn = $this->isLoggedIn();
            // if($isLoggedIn == 'false')
            // {
            //     $url = url('/');
            //  return redirect()->to($url);
            // }
            // $get_approved_orders = DB::select("SELECT DISTINCT customer_details.customer_name as customer_name,
            //                                         customer_details.primary_contact_no as contact_no,
            //                                         del_orders.order_id as order_id,
            //                                         del_orders.status as del_status,
            //                                         order_details.status as status
            //                                     FROM order_details,del_orders,customer_details
            //                                     where order_details.status='Accepted'
            //                                     AND del_orders.order_approval_status='Approved'
            //                                     AND del_orders.status != 'Closed'
            //                                     AND order_details.order_id=del_orders.order_id
            //                                     AND order_details.customer_id = customer_details.cust_id ORDER BY del_orders.order_id DESC");
            // $data['get_approved_orders'] = json_decode(json_encode($get_approved_orders), true);
            // //print_r($data);
            // return view('OrderManagement/ViewApprovedOrders',$data);
            // }
        }
        //Name FIlter apply
        public function NameFilter(Request $request)
        {
            $date = date('Y-m-d');
            $name = $_POST['name'];
            if(session('role') == "superuser")
            {
                $leads = DB::select("SELECT 
                                        leads.*,
                                        customer_details.*,
                                        user.username as lead_owner 
                                    FROM 
                                        leads,
                                        customer_details,
                                        user 
                                    WHERE 
                                        leads.customer_id = customer_details.cust_id 
                                        AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress' )
                                        AND customer_details.customer_name LIKE '%$name%'
                                        AND leads.lead_owner = user.id 
                                        ORDER BY leads.creation_date DESC");
            }
            elseif(session('role') == "admin")
            {
                $user_city = session('user_city');
                $leads = DB::select("SELECT 
                                        leads.*,
                                        customer_details.*,
                                        user.username as lead_owner 
                                    FROM 
                                        leads,
                                        customer_details,
                                        user 
                                    WHERE 
                                        leads.customer_id = customer_details.cust_id 
                                        AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated') 
                                        AND customer_details.citygroup='$user_city' 
                                        AND customer_details.customer_name LIKE '%$name%'
                                        AND leads.lead_owner = user.id 
                                        ORDER BY leads.creation_date DESC");
            }
            elseif(session('role') == "user")
            {
                $user_city = session('user_city');
                $leads = DB::select("SELECT 
                                        leads.*,
                                        customer_details.*,
                                        user.username as lead_owner 
                                    FROM 
                                        leads,
                                        customer_details,
                                        user 
                                    WHERE 
                                        leads.customer_id = customer_details.cust_id 
                                        AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated') 
                                        AND customer_details.citygroup='$user_city' 
                                        AND customer_details.customer_name LIKE '%$name%'
                                        AND leads.lead_owner = user.id 
                                        ORDER BY leads.creation_date DESC");
            }
            $data['lead_details'] = json_decode(json_encode($leads), true);
            for ($i=0; $i < count($data['lead_details']); $i++) 
            { 
                $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
                $equipement_details = array();
                for ($j=0; $j <count($product); $j++) 
                { 
                    $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
                    $product_details = json_decode(json_encode($product_details), true);
                    array_push($equipement_details,$product_details[0]['product_name']);
                }
                $equipements = json_encode($equipement_details);
                $data['lead_details'][$i]['equipment_requirement'] = $equipements;
            }
            //print_r($data['lead_details']);
            $unq_cust = array();
            $total_products = array();
            foreach($data['lead_details'] as $key => $lead_info) 
            {
                $customer_id = $lead_info['customer_id'];
                if(in_array($customer_id,$unq_cust))
                {
                    for($i=0; $i<count($total_products); $i++)
                    {
                        if($total_products[$i]['customer_id'] == $lead_info['customer_id'])
                        {
                            $count = count($total_products[$i]['product_details']);
                            $total_products[$i]['product_details'][$count]['quantity'] = json_decode($lead_info['equipment_qty']);
                            $total_products[$i]['product_details'][$count]['offered_rent'] = json_decode($lead_info['offered_rent']);
                            $total_products[$i]['product_details'][$count]['offered_rent_total'] = json_decode($lead_info['offered_rent_total']);
                            $total_products[$i]['product_details'][$count]['deposit'] = json_decode($lead_info['deposite']);
                            $total_products[$i]['product_details'][$count]['deposit_total'] = json_decode($lead_info['deposite_total']);
                            $total_products[$i]['product_details'][$count]['sale_rental'] = json_decode($lead_info['sale_rental']);
                            $total_products[$i]['product_details'][$count]['transport'] = json_decode($lead_info['transport']);
                        }
                    }
                }
                else
                {
                    array_push($unq_cust,$customer_id);
                    $count = count($total_products);
                    $total_products[$count]['customer_id'] = $lead_info['customer_id'];
                    $total_products[$count]['product_details'][0]['quantity'] = json_decode($lead_info['equipment_qty']);
                    $total_products[$count]['product_details'][0]['offered_rent'] = json_decode($lead_info['offered_rent']);
                    $total_products[$count]['product_details'][0]['offered_rent_total'] = json_decode($lead_info['offered_rent_total']);
                    $total_products[$count]['product_details'][0]['deposit'] = json_decode($lead_info['deposite']);
                    $total_products[$count]['product_details'][0]['deposit_total'] = json_decode($lead_info['deposite_total']);
                    $total_products[$count]['product_details'][0]['transport'] = json_decode($lead_info['transport']);
                    $total_products[$count]['product_details'][0]['sale_rental'] = json_decode($lead_info['sale_rental']);
                }
            }
            //print_r($total_products);
            $product_count = 0;
            $total_rent_product = 0;
            $total_sale_product = 0;
            $total_rent_amt = 0;
            $total_sale_amt = 0;
            $total_deposit = 0;
            $total_transport = 0;
            foreach ($total_products as $total_p)
            {
                $product_details = $total_p['product_details'];
                for ($i=0; $i <count($product_details) ; $i++) { 
                    for ($j=0; $j <count($product_details[$i]['quantity']) ; $j++) { 
                        
                        if($product_details[$i]['sale_rental'][$j]=='Rental')
                        {
                            $total_rent_amt += $product_details[$i]['offered_rent_total'][$j];    
                            $total_rent_product += $product_details[$i]['quantity'][$j];    
                        }
                        elseif($product_details[$i]['sale_rental'][$j]=='Sale')
                        {
                            $total_sale_amt += $product_details[$i]['offered_rent_total'][$j];    
                            $total_sale_product += $product_details[$i]['quantity'][$j];   
                        }
                        $total_transport += $product_details[$i]['transport'][$j];
                        $total_deposit += $product_details[$i]['deposit_total'][$j];
                        $product_count += $product_details[$i]['quantity'][$j];
                    }
                }
            }
            $data['product_count'] = $product_count;
            $data['total_customer'] = count($unq_cust);
            $data['total_amount'] = $total_rent_amt+$total_sale_amt+$total_transport;
            $data['total_rent_product'] = $total_rent_product;
            $data['total_sale_product'] = $total_sale_product;
            $data['total_sale_amt'] = $total_sale_amt;
            $data['total_rent_amt'] = $total_rent_amt;
            $data['total_deposit'] = $total_deposit;
            $data['total_transport'] = $total_transport;
            echo "<script>localStorage['filtered']='today';</script>";
            $data['post_name'] = $name;
            return view('OrderManagement/viewAllLeads',$data);
        }
        //view all filter
        public function ViewAllOrdersFilter(Request $request)
        {
            $match_clause = array();
            $whereCondition = [];
            $whereConditionDB = [];
            // dd($request);
            $collapsible = false;
            $collapsible_main = false;
            $del_boys = DB::table('delusers')
                                ->select('username')
                                ->where('role','user')
                                ->where('status','Active')
                                ->when(session('city_based_access') == '1',function($query){
                                    $query->where('city',session('user_city'));
                                })
                                ->get();
            $cities =  array();
            if(session('city_based_access') == '1')
            {
                $cities[0] = (object)(['city'=>session('user_city')]);
            }
            else{
                $cities = DB::table('customer_details')->distinct('citygroup')->whereNotNull('citygroup')->orderBy('citygroup','ASC')->get('citygroup');
            }

            $state = $request->get('del_boy_state');
            $date = date('Y-m-d');
            $time = $request->get('availability_time');
            $dateArr = [];
            if(isset($time))
            {
                $time = $time;
                // $startDate = date('d-m-Y');
                // $endDate = date('d-m-Y');
                // array_push($dateArr,$startDate);
                // array_push($dateArr,$endDate);
            }
            else{
                $time = date('H:i');
            }
            $del_boy_in = array();
            if($state != null)
            {
                $collapsible = true;
                $collapsible_main = true;
                $time = $request->get('availability_time');
                $date_time = date('Y-m-d').' '.$time.':00';
                
                $orderTypeNotIn = config('app.order_type');
                $del_boys = DB::table('del_orders')
                                    ->join('delusers','del_orders.DelAssignedTo','=','delusers.username')
                                    ->select('del_orders.DelAssignedTo as username')
                                    ->distinct('del_orders.DelAssignedTo')
                                    ->where([[DB::raw('DATE_ADD(del_orders.created_at, INTERVAL 4 HOUR)'),'>',$date_time]])
                                    ->where([['created_at','>',(date('Y-m-d').' '.'00:00:00')]])
                                    ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                                    ->when(session('city_based_access') == '1',function($query){
                                        $query->where('delusers.city',session('user_city'));
                                    })
                                    ->get()
                                    ->toArray();
                if($state == "Occupied")
                {
                    // $date_time = date('Y-m-d H:i:s',strtotime('+4 hours'));
                    // $time = $request->get('availability_time');
                    // $del_boys = DB::table('del_orders')->select('DelAssignedTo')->whereBetween('created_at',[date('Y-m-d').' '.$time,$date_time])->get();
                }
                elseif($state == "Available")
                {
                    $del_boys = json_decode(json_encode($del_boys),true);
                    $del_boys = DB::table('delusers')->select('*')->whereNotIn('username',$del_boys)->where('role','user')->where('status','Active')->get();
                }
            }
            // dd('ssss'.$del_boys);
            $del_boys_get = $request->get('del_boys');
            if($del_boys_get != null)
            {
                $collapsible = true;
                $collapsible_main = true;
                // dd($del_boys_get);
                $del_boy_in[] = $del_boys_get;
                // if($state == null){
                //     $del_boy_in[] = $del_boys_get;
                // }
                // $wherendra = ['DelAssignedTo','=',$del_boys_get];
                // array_push($whereConditionDB,$wherendra);
            }
            else
            {
                // $del = json_decode(json_encode($del_boys),true);
                // $del_boy_in = $del;
                // $wherendra = ['DelAssignedTo',$del];
                // array_push($whereConditionDB,$wherendra);
            }
            // $get_min_date = DelOrders::first('DelDate');
            // //dd($get_min_date->DelDate);
            // $form_min_date = date('d-m-Y',strtotime($get_min_date->DelDate));
            // $get_max_date = Carbon::now()->toDateString();
            // $form_max_date = date('d-m-Y',strtotime($get_max_date));
            // //get master products
            // $from_date = date('Y-m-d',strtotime($form_min_date));
            // $end_date = date('Y-m-d',strtotime($form_max_date));
            $get_master_products = DB::table('products')->where('flag','=','Active')->get();

            $from_date = $request->get('filter_from_date');
            $end_date = $request->get('filter_end_date');
            
            if(isset($from_date) && isset($end_date)){
                $collapsible_main = true;
                $startDate = date('d-m-Y',strtotime($from_date));
                $endDate = date('d-m-Y',strtotime($end_date));
                array_push($dateArr,$startDate);
                array_push($dateArr,$endDate);
                // $form_min_date = date('d-m-Y',strtotime($from_date));
                // $form_max_date = date('d-m-Y',strtotime($end_date));
            }

            $customer_name = $request->get('filter_customer_name');
            if(isset($customer_name)){
                $collapsible_main = true;
                $whereCondition1 = ['del_orders.shipping_first_name','LIKE','%'.$customer_name.'%'];
                array_push($whereCondition,$whereCondition1);
               
            }
            $location = $request->get('filter_location');
            if(isset($location)){
                $collapsible_main = true;
                $whereCondition1 = ['del_orders.location','LIKE','%'.$location.'%'];
                array_push($whereCondition,$whereCondition1);
            }
            $customer_contact = $request->get('filter_contact_no');
            if(isset($customer_contact)) {
                $collapsible_main = true;
                $whereCondition2 = ['del_orders.mobileno','=',$customer_contact];
                array_push($whereCondition,$whereCondition2);
            }
            $order_type = $request->get('filter_order_type');
            if(isset($order_type) && ($order_type=='Delivery' || $order_type=='Collection' || $order_type=='Pick Up')){
                $collapsible_main = true;
                $whereCondition3 = ['del_orders.deliverypickup','=',$order_type];
                array_push($whereCondition,$whereCondition3);
            }
            $order_id = $request->get('filter_order_id');
            if(isset($order_id)){
                $collapsible_main = true;
                $whereCondition4 = ['del_orders.order_id','=',$order_id];
                array_push($whereCondition,$whereCondition4);
            }
            $delivery_status = $request->get('filter_delivery_status');
            if(isset($delivery_status) && ($delivery_status=='Pending' || $delivery_status=='Assigned' || $delivery_status=='Accepted' || $delivery_status=='InProgress' || $delivery_status=='Delivered')){
                $collapsible_main = true;
                $whereCondition5 = ['del_orders.status','=',$delivery_status];
                array_push($whereCondition,$whereCondition5);
            }

            
            $sort_colmun = $request->get('sort_column');
            $sort_val = $request->get('sort_direction');
            $column = DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))");
            $direction = 'DESC';
            if(isset($sort_colmun) && isset($sort_val)){
                if($sort_colmun=='DelDate')
                {
                    $column = DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))");
                    $direction = $sort_val;
                }
                else{
                    $column = $sort_colmun;
                    $direction = $sort_val;
                }
                
            }
            $page_no = 1;
            $page = $request->get('page');
            if(isset($page) && $page>1){
                $page_no = $page;
            }
            else{
                $page_no = 1;
            }
            $master_product = $request->get('master_product');
            
            if(isset($master_product) && $master_product!='All')
            {
                // dd($master_product);
                $collapsible_main = true;
                $orderTypeNotIn = config('app.order_type');
                $get_del_orders = DB::table('del_orders')
                                    ->join('order_details','del_orders.order_id','=','order_details.order_id')
                                    ->join('leads','del_orders.lead_id','=','leads.id')
                                    ->join('customer_details','leads.customer_id','customer_details.cust_id')
                                    //->select('del_orders.*','leads.patient_name','order_details.created_at')
                                    ->select('del_orders.*','order_details.created_at')
                                    //->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$form_min_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$form_max_date','%d-%m-%Y'))")])
                                    ->when($dateArr,function($query,$dateArr){
                                        $query->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$dateArr[0]','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$dateArr[1]','%d-%m-%Y'))")]);
                                    })
                                    ->where($whereCondition)
                                    ->where($whereConditionDB)
                                    ->when($del_boy_in,function($query,$del_boy_in){
                                        $query->whereIn('del_orders.DelAssignedTo',$del_boy_in);
                                    })
                                    ->when($request->get('filter_patient_name'),function($query)use($request){
                                        $query->where('del_orders.patient_name','LIKE','%'.$request->get('filter_patient_name').'%');
                                    })
                                    ->when($request->get('filter_city') && $request->get('filter_city')!='All',function($query)use($request){
                                        $query->where('customer_details.citygroup',$request->get('filter_city'));
                                    })
                                    ->where('del_orders.deliverypickup','=','Delivery')
                                    ->whereIn('order_details.product_id',$master_product)
                                    ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                                    ->orderBy($column,$direction)
                                    ->get();
                
                $get_pickup_orders = DB::table('del_orders')
                                    ->join('pickups','del_orders.order_id','=','pickups.pickup_order_id')
                                    ->join('leads','del_orders.lead_id','=','leads.id')
                                    ->join('customer_details','leads.customer_id','customer_details.cust_id')
                                    // ->select('del_orders.*','leads.patient_name','pickups.created_at')
                                    ->select('del_orders.*','pickups.created_at')
                                    //->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$form_min_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$form_max_date','%d-%m-%Y'))")])
                                    ->when($dateArr,function($query,$dateArr){
                                        $query->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$dateArr[0]','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$dateArr[1]','%d-%m-%Y'))")]);
                                    })
                                    ->where($whereCondition)
                                    ->where($whereConditionDB)
                                    ->when($del_boy_in,function($query,$del_boy_in){
                                        $query->whereIn('del_orders.DelAssignedTo',$del_boy_in);
                                    })
                                    ->when($request->get('filter_patient_name'),function($query)use($request){
                                        $query->where('del_orders.patient_name','LIKE','%'.$request->get('filter_patient_name').'%');
                                    })
                                    ->when($request->get('filter_city') && $request->get('filter_city')!='All',function($query)use($request){
                                        $query->where('customer_details.citygroup',$request->get('filter_city'));
                                    })
                                    ->where('del_orders.deliverypickup','=','Pick Up')
                                    ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                                    ->whereIn('pickups.product_id',$master_product)
                                    ->orderBy($column,$direction)
                                    ->get();
                $get_collection_orders = DB::table('del_orders')
                                    ->join('renewals','del_orders.order_id','=','renewals.order_id')
                                    ->join('leads','del_orders.lead_id','=','leads.id')
                                    ->join('customer_details','leads.customer_id','customer_details.cust_id')
                                    //->select('del_orders.*','leads.patient_name','renewals.created_at')
                                    ->select('del_orders.*','renewals.created_at')
                                    //->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$form_min_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$form_max_date','%d-%m-%Y'))")])
                                    ->when($dateArr,function($query,$dateArr){
                                        $query->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$dateArr[0]','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$dateArr[1]','%d-%m-%Y'))")]);
                                    })
                                    ->where($whereCondition)
                                    ->where($whereConditionDB)
                                    ->when($del_boy_in,function($query,$del_boy_in){
                                        $query->whereIn('del_orders.DelAssignedTo',$del_boy_in);
                                    })
                                    ->when($request->get('filter_patient_name'),function($query)use($request){
                                        $query->where('del_orders.patient_name','LIKE','%'.$request->get('filter_patient_name').'%');
                                    })
                                    ->when($request->get('filter_city') && $request->get('filter_city')!='All',function($query)use($request){
                                        $query->where('customer_details.citygroup',$request->get('filter_city'));
                                    })
                                    ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                                    ->where('del_orders.deliverypickup','=','Collection')
                                    ->whereIn('renewals.product_id',$master_product)
                                    ->orderBy($column,$direction)
                                    ->get();

                $get_maintenance_orders = DB::table('del_orders')
                                            //->join('renewals','del_orders.order_id','=','renewals.order_id')
                                            ->join('leads','del_orders.lead_id','=','leads.id')
                                            ->join('customer_details','leads.customer_id','customer_details.cust_id')
                                            ->join('maintenance_orders','del_orders.order_id','=','maintenance_orders.order_id')
                                            ->select('del_orders.*','maintenance_orders.created_at')
                                            ->when($dateArr,function($query,$dateArr){
                                                $query->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$dateArr[0]','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$dateArr[1]','%d-%m-%Y'))")]);
                                            })
                                            ->where($whereCondition)
                                            ->where($whereConditionDB)
                                            ->when($del_boy_in,function($query,$del_boy_in){
                                                $query->whereIn('del_orders.DelAssignedTo',$del_boy_in);
                                            })
                                            ->when($request->get('filter_patient_name'),function($query)use($request){
                                                $query->where('del_orders.patient_name','LIKE','%'.$request->get('filter_patient_name').'%');
                                            })
                                            ->when($request->get('filter_city'),function($query)use($request){
                                                $query->where('customer_details.citygroup',$request->get('filter_city'));
                                            })
                                            ->whereIn('del_orders.deliverypickup',$orderTypeNotIn)
                                            ->whereIn('maintenance_orders.product_id',$master_product)
                                            ->orderBy($column,$direction)
                                            ->get();
                
                $get_all_orders = collect($get_del_orders->merge($get_pickup_orders)->merge($get_collection_orders))->sortByDesc('order_id');
                // dd($get_all_orders);
                //print_r($get_all_orders->toArray());
                $get_all_orders = $get_all_orders->paginate(10,null,$page_no);
                $get_products = json_decode(json_encode($get_all_orders), true);
                $products_arr = array();
                // $key=0;
                foreach($get_all_orders as $key=>$order)
                {
                    $get_order_id = $order->order_id;
                    $del_status = $order->deliverypickup;
                    if($del_status=='Delivery')
                    {
                        if(OrderDetails::where('order_id',$get_order_id)->exists()){
                            $get_product_details = DB::table('order_details')
                                            ->join('products', 'order_details.product_id','=','products.id')
                                            ->join('customer_details','order_details.customer_id','customer_details.cust_id')
                                            
                                            ->select('products.product_name','customer_details.customer_type','order_details.created_at')
                                            ->where('order_details.order_id',$get_order_id)->get();
                            $product_name_imp = implode(',',$get_product_details->pluck('product_name')->toArray());
                            $products_arr[$key]['products'] = $product_name_imp;
                            $products_arr[$key]['customer_type'] = $get_product_details[0]->customer_type;
                            $products_arr[$key]['created_at'] = $get_product_details[0]->created_at;

                        }else{
                            $products_arr[$key]['products'] = null;
                            $products_arr[$key]['customer_type'] = null;
                            $products_arr[$key]['created_at'] =null;
                            
                        }
                    }
                    elseif($del_status=='Pick Up' || $del_status=='Pick UP')
                    {
                        if(Pickup::where('pickup_order_id',$get_order_id)->exists()){
                            $get_product_details = DB::table('pickups')
                                            ->join('products', 'pickups.product_id','=','products.id')
                                            ->join('order_details','pickups.order_details_id','=','order_details.id')
                                            ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                                            ->select('products.product_name','customer_details.customer_type','pickups.created_at')
                                            ->where('pickups.pickup_order_id',$get_order_id)
                                            ->get();
                            $product_name_imp = implode(',',$get_product_details->pluck('product_name')->toArray());
                            $products_arr[$key]['products'] = $product_name_imp;
                            $products_arr[$key]['customer_type'] = $get_product_details[0]->customer_type;
                            $products_arr[$key]['created_at'] = $get_product_details[0]->created_at;
                        }else{
                            $products_arr[$key]['products'] = null;
                            $products_arr[$key]['customer_type'] = null;
                            $products_arr[$key]['created_at'] = null;
                        }
                    }
                    elseif($del_status=='Collection')
                    {
                        if(Renewal::where('collection_order_id',$get_order_id)->exists()){
                            $get_product_details = DB::table('renewals')
                                            ->join('products', 'renewals.product_id','=','products.id')
                                            ->join('order_details','renewals.order_details_id','=','order_details.id')
                                            ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                                            ->select('products.product_name','customer_details.customer_type','renewals.created_at')
                                            ->where('renewals.collection_order_id',$get_order_id)
                                            ->get();
                            $product_name_imp = implode(',',$get_product_details->pluck('product_name')->toArray());
                            $products_arr[$key]['products'] = $product_name_imp;
                            $products_arr[$key]['customer_type'] = $get_product_details[0]->customer_type;
                            $products_arr[$key]['created_at'] = $get_product_details[0]->created_at;
                            
                        }else{
                            $products_arr[$key]['products'] = null;
                            $products_arr[$key]['customer_type'] = null;
                            $products_arr[$key]['created_at'] = null;
                        }
                    }
                    elseif(in_array($del_status,$orderTypeNotIn))
                    {
                        if(DB::table('maintenace_orders')->where('order_id',$get_order_id)->exists()){
                            $get_product_details = DB::table('maintenace_orders')
                                            ->join('products', 'maintenace_orders.product_id','=','products.id')
                                            ->join('order_details','maintenace_orders.order_details_id','=','order_details.id')
                                            ->join('customer_details','maintenace_orders.customer_id','=','customer_details.cust_id')
                                            ->select('products.product_name','customer_details.customer_type','maintenace_orders.created_at')
                                            ->where('maintenace_orders.order_id',$get_order_id)
                                            ->get();
                            $product_name_imp = implode(',',$get_product_details->pluck('product_name')->toArray());
                            $products_arr[$key]['products'] = $product_name_imp;
                            $products_arr[$key]['customer_type'] = $get_product_details[0]->customer_type;
                            $products_arr[$key]['created_at'] = $get_product_details[0]->created_at;
                        }else{
                            $products_arr[$key]['products'] = null;
                            $products_arr[$key]['customer_type'] = NULL;
                            $products_arr[$key]['created_at'] = null;
                        }
                    }
                    // $product_name_imp = implode(',',$get_product_details);
                    // $products_arr[$key][''] = $product_name_imp;
                    // $key++;
                }
                // dd($products_arr);
            }
            else
            {
                // DB::enableQueryLog();
                $orderTypeNotIn = config('app.order_type');
                $get_all_orders = DB::table('del_orders')
                                    ->join('leads','del_orders.lead_id','=','leads.id')
                                    ->join('customer_details','leads.customer_id','customer_details.cust_id')
                                    //->join('order_details','del_orders.order_id','=','order_details.order_id')
                                    // ->join('leads','del_orders.lead_id','=','leads.id')
                                    // ->join('customer_details','leads.customer_id','customer_details.cust_id')
                                    ->select('del_orders.*')
                                    ->when($dateArr,function($query,$dateArr){
                                        $query->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$dateArr[0]','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$dateArr[1]','%d-%m-%Y'))")]);
                                    })
                                    ->when($request->get('filter_patient_name'),function($query)use($request){
                                        $query->where('del_orders.patient_name','LIKE','%'.$request->get('filter_patient_name').'%');
                                    })
                                    ->when($request->get('filter_city') && $request->get('filter_city')!='All',function($query)use($request){
                                        $query->where('customer_details.citygroup',$request->get('filter_city'));
                                    })
                    ->where($whereCondition)
                    ->where($whereConditionDB)
                    ->when($del_boy_in,function($query,$del_boy_in){
                        $query->whereIn('del_orders.DelAssignedTo',$del_boy_in);
                    })
                                    ->when(session('city_based_access') == '1',function($query){
                                        $query->where('customer_details.citygroup',session('user_city'));
                                    })
                                    // ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                    //->orderBy('order_id','DESC')
                    ->orderBy($column,$direction)
                    ->paginate(10);
                    //->groupBy('order_id');
                    // dd(DB::getQueryLog());
                $get_products = json_decode(json_encode($get_all_orders), true);
                $products_arr = array();
                foreach($get_all_orders as $key=>$order)
                {
                    $get_order_id = $order->order_id;
                    $del_status = $order->deliverypickup;
                    if($del_status=='Delivery')
                    {
                        if(OrderDetails::where('order_id',$get_order_id)->exists())
                        {
                            $get_product_details = DB::table('order_details')
                                            ->join('products', 'order_details.product_id','=','products.id')
                                            ->join('customer_details','order_details.customer_id','customer_details.cust_id')
                                            ->select('products.product_name','customer_details.customer_type','order_details.created_at')
                                            ->where('order_id',$get_order_id)
                                            ->get();
                            $product_name_imp = implode(',',$get_product_details->pluck('product_name')->toArray());
                            $products_arr[$key]['products'] = $product_name_imp;
                            $products_arr[$key]['customer_type'] = $get_product_details[0]->customer_type;
                            $products_arr[$key]['created_at'] = $get_product_details[0]->created_at;
                        }
                        else
                        {
                            $products_arr[$key]['products'] = null;
                            $products_arr[$key]['customer_type'] = null;
                            $products_arr[$key]['created_at'] = null;
                        }
                        
                    }
                    elseif($del_status=='Pick Up')
                    {
                        if(Pickup::where('pickup_order_id',$get_order_id)->exists())
                        {
                            $get_product_details = DB::table('pickups')
                                            ->join('products', 'pickups.product_id','=','products.id')
                                            ->join('order_details','pickups.order_details_id','=','order_details.id')
                                            ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                                            ->select('products.product_name','customer_details.customer_type','pickups.created_at')
                                            ->where('pickup_order_id',$get_order_id)
                                            ->get();
                            $product_name_imp = implode(',',$get_product_details->pluck('product_name')->toArray());
                            $products_arr[$key]['products'] = $product_name_imp;
                            $products_arr[$key]['customer_type'] = $get_product_details[0]->customer_type;
                            $products_arr[$key]['created_at'] = $get_product_details[0]->created_at;
                        }
                        else{
                            $products_arr[$key]['products'] = null;
                            $products_arr[$key]['customer_type'] = null;
                            $products_arr[$key]['created_at'] = null;
                        }
                            
                    }
                    elseif($del_status=='Collection')
                    {
                        if(Renewal::where('collection_order_id',$get_order_id)->exists())
                        {
                            $get_product_details = DB::table('renewals')
                                            ->join('products', 'renewals.product_id','=','products.id')
                                            ->join('order_details','renewals.order_details_id','=','order_details.id')
                                            ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                                            ->select('products.product_name','customer_details.customer_type','renewals.created_at')
                                            ->where('collection_order_id',$get_order_id)
                                            ->get();
                            $product_name_imp = implode(',',$get_product_details->pluck('product_name')->toArray());
                            $products_arr[$key]['products'] = $product_name_imp;
                            $products_arr[$key]['customer_type'] = $get_product_details[0]->customer_type;
                            $products_arr[$key]['created_at'] = $get_product_details[0]->created_at;
                        }else{
                            $products_arr[$key]['products'] = null;
                            $products_arr[$key]['customer_type'] = null;
                            $products_arr[$key]['created_at'] = null;
                        }
                    }
                    elseif(in_array($del_status,$orderTypeNotIn))
                    {
                        // dd("a");
                        if(DB::table('maintenance_orders')->where('order_id',$get_order_id)->exists())
                        {
                            $get_product_details = DB::table('maintenance_orders')
                                            ->join('products', 'maintenance_orders.product_id','=','products.id')
                                            ->join('order_details','maintenance_orders.order_details_id','=','order_details.id')
                                            ->join('customer_details','maintenance_orders.customer_id','=','customer_details.cust_id')
                                            ->where('maintenance_orders.order_id',$get_order_id)
                                            ->select('products.product_name','customer_details.customer_type','maintenance_orders.created_at')
                                            ->get();
                            $product_name_imp = implode(',',$get_product_details->pluck('product_name')->toArray());
                            $products_arr[$key]['products'] = $product_name_imp;
                            $products_arr[$key]['customer_type'] = $get_product_details[0]->customer_type;
                            $products_arr[$key]['created_at'] = $get_product_details[0]->created_at;
                        }else{
                            $products_arr[$key]['products'] = null;
                            $products_arr[$key]['customer_type'] = null;
                            $products_arr[$key]['created_at'] = null;
                            
                        }
                    }
                    //dd($get_product_details);
                    // $product_name_imp = implode(',',$get_product_details->pluck('product_name')->toArray());
                    // $products_arr[$key]['products'] = $product_name_imp;
                    // $products_arr[$key]['customer_type'] = $get_product_details[0]->customer_type;
                    // $products_arr[$key]= $product_name_imp;
                    
                }
            }

            
            //dd($get_all_orders,$products_arr);
            
            $filter_arr = [ "cust_name"=>$customer_name,
                            "location"=>$location,
                            "patient_name"=>$request->get('filter_patient_name'),
                            "cust_no"=>$customer_contact,
                            "city"=>$request->get('filter_city'),
                            "order_type"=>$order_type,
                            "order_id"=>$order_id,
                            "master_product"=>$master_product,
                            "delivery_status"=>$delivery_status,
                            "from_date"=>$from_date,
                            "end_date"=>$end_date,
                            "sort_column"=>$sort_colmun,
                            "sort_val"=>$sort_val,
                            "del_boys"=>$del_boys_get,
                            "del_boy_state"=>$state,
                            "availability_time"=>$time,
                            "collapsible"=>$collapsible,
                            "collapsible_main"=>$collapsible_main];

            //get labour charges
            $totalLabour = DelOrders::select(DB::raw("SUM(labour_charges) as total_labour_charges"))
                                ->when($dateArr,function($query,$dateArr){
                                    $query->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$dateArr[0]','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$dateArr[1]','%d-%m-%Y'))")]);
                                })->get();
            $submit = $request->get('btn_submit');
            if($submit=="export_excel")
            {
                $orderTypeNotIn = config('app.order_type');
                $get_all_orders = DB::table('del_orders')
                                    ->select('DelDate','order_id','shipping_first_name','mobileno','deliverypickup','status')
                                    //->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$form_min_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$form_max_date','%d-%m-%Y'))")])
                                    ->when($dateArr,function($query,$dateArr){
                                        $query->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$dateArr[0]','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$dateArr[1]','%d-%m-%Y'))")]);
                                    })
                                    ->where($whereCondition)
                                    //->orderBy('order_id','DESC')
                                    ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                                    ->orderBy($column,$direction)
                                    ->get()
                                    ->toArray();
                $jsonDecoded = json_decode(json_encode($get_all_orders),true);
                //print_r($jsonDecoded);
                ob_end_clean(); // this
                ob_start(); // and this
                return Excel::download(new OrdersExport($jsonDecoded), 'AllOrders.xlsx');
            }
            else
            {
                //dd($get_all_orders,$products_arr,$get_master_products);
                $orderClosedReason = config('app.order_closed_reason');
                return view('OrderManagement/view_all_orders',compact('get_all_orders','products_arr','get_master_products','del_boys'),
                                                            compact('filter_arr','totalLabour','orderClosedReason','cities'));
            }
            
        }


        function array_sort_by_column(&$array, $column, $direction = SORT_ASC) {
            $reference_array = array();
        
            foreach($array as $key => $row) {
                $reference_array[$key] = $row[$column];
            }
        
            array_multisort($reference_array, $direction, $array);
        }
        public function ViewAllOrdersExport()
        { 
            ob_end_clean(); // this
            ob_start(); // and this
            return Excel::download(new OrdersExport, 'invoices.xlsx');
        }

        public function location_populate(Request $request)
        {
            $query = $request->get('query');
            $filterResult = DB::select("SELECT DISTINCT location FROM del_orders WHERE location LIKE '%$query%' ");
            $filterResult = json_decode(json_encode($filterResult),true);
            $data = array();
            foreach($filterResult as $key => $result)
            {
                $data[] = $result['location'];
            }
            return response()->json($data);
        }

        public function addLabourCharges(Request $request)
        {
            $upLabourData = [
                'floor_no'=>($request->get('floor_no'))?$request->get('floor_no'):0,
                'labour_charges'=>($request->get('labour_charges'))?$request->get('labour_charges'):0
            ];
            
            $oldLabour = DelOrders::where('order_id',$request->get('labour_order_id'))->get(array_keys($upLabourData));
            foreach ($upLabourData as $key => $value) {
                $insertLogData = [
                    'order_type'=>'DO',
                    'key_id'=>$request->get('labour_order_id'),
                    'operation'=>'Labour Charges updated',
                    'fields'=>$key,
                    'old_value'=>$oldLabour[0]->$key,
                    'new_value'=>$value,
                    'updated_by'=>session('username')
                ];
                ActivityLog::insert($insertLogData);
            }
            DelOrders::where('order_id',$request->get('labour_order_id'))->update($upLabourData);
            return redirect()->back()->with('message','Labour charges added successfully');
        }

        public function orderCancelReason(Request $request){
            $order_id = $request->get('order_id');
            $getReason = DB::table('del_orders')->select('cancellation_reason','comment')->where('order_id',$order_id)->get()->toArray();
            return $getReason;
        }
        public function order_closed_mail($order_id,$closeReason,$closeRemark,$shipping_first_name)
        {
            $data = array('order_id'=>"'".$order_id."'",'shipping_first_name'=>"'".$shipping_first_name."'",'close_reason'=>"'".$closeReason."'",'close_remark'=>"'".$closeRemark."'");
            Mail::send('OrderManagement/closeOrderMail', $data, function($message) 
            {                
                $email_id = 'accounts@quali55care.com';
                $message->to($email_id, 'Accounts')->subject('Order Cancelled');
                $message->from('tempmailquali@gmail.com', 'Quali55Care');
            });
        }

        public function removePickupProd(Request $request)
        {
            $pickup_details = Pickup::where('id',$request->get('prod_id'))->get()->toArray();
            foreach($pickup_details as $pickup_detail)
            {
                $order_details_id = $pickup_detail['order_details_id'];
                OrderDetails::where('id',$order_details_id)->update(['current_status'=>'Pending']);
                VirtualVdrInventoryMgmt::where('order_details_id',$order_details_id)->update(['status'=>'2','updated_by'=>session('username')]);
                $order_detail = OrderDetails::where('id',$order_details_id)->get()->toArray();
                $qty = $order_detail[0]['product_qty'];
                $inventory_id = $order_detail[0]['unique_id'];
                $vendor_product_id = $order_detail[0]['vendor_product_id'];
                $update = DB::update("UPDATE vendor_products SET vendor_products.product_quantity = vendor_products.product_quantity-$qty WHERE id=$vendor_product_id");
                $vendor_product_details_id = $order_detail[0]['vendor_product_details_id'];
                if(VendorRentedProducts::where('unique_id',$inventory_id)->where('status','On Rent')->exists())
                {
                    $rented_prod_details = VendorRentedProducts::where('unique_id',$inventory_id)->where('status','On Rent')->get()->toArray();
                    $rented_prod_id =  $rented_prod_details[0]['id'];
                    //dd($rented_prod_id);
                    $order_details_del = OrderDetails::where('rented_product_id',$rented_prod_id)->get()->toArray();
                    if(isset($order_details_del[0]))
                    {
                        $del_order_id = $order_details_del[0]['order_id'];
                        $response = $this->close_deliverey_fun($del_order_id);
                    }
                }
                VendorProductDetails::where('id',$vendor_product_details_id)->update(['availability_status'=>0,'current_location'=>0]);
                $rented_product_id = $order_detail[0]['rented_product_id'];
                VendorRentedProducts::where('id',$rented_product_id)->update(['status'=>'On Rent']);
                // Pickup::where('id',$request->get('prod_id'))->delete();
                Pickup::where('id',$request->get('prod_id'))->update(['status'=>'Cancel']);
            }
            // DelOrders::where('order_id',$order_id)->delete();
            // Pickup::where('pickup_order_id',$order_id)->delete();
            // DelOrders::where('order_id',$order_id)->update(['status'=>'Cancel','cancellation_reason'=>$closeReason,'comment'=>$comment]);
            // Pickup::where('pickup_order_id',$order_id)->update(['status'=>'Cancel']);
            $insertLogData = [
                'order_type'=>'PO',
                'key_id'=>$request->get('prod_id'),
                'operation'=>'Order Product Cancelled',
                'fields'=>'status',
                'old_value'=>'Pending',
                'new_value'=>'Cancel',
                'updated_by'=>session('username')
            ];
            ActivityLog::insert($insertLogData);
            return redirect()->back()->with('message','Product Removed Successfully');
        }
        
        public function orderData(Request $request){
            $orderId = $request->get('order_id');
            $getOrderData = DB::table('order_details')
                                ->select('order_details.*','products.product_name')
                                ->join('products','order_details.product_id','=','products.id')
                                ->where('order_id',$orderId)
                                ->get();
            return $getOrderData;
        }
        public function ordersearch(Request $request){
            if($request->method() == "GET"){
                return view('OrderManagement.order-search');
            }else{
                $request->get('order_id');
                // dd($request->get('order_id'));
                $orderTypeNotIn = config('app.order_type');
                $order_details = DB::table('del_orders')
                                    ->join('order_details','order_details.order_id','=','del_orders.order_id')                                    
                                    // ->join('pickups','pickups.del_order_id','=','del_orders.order_id')
                                    // ->join('renewals','renewals.order_id','=','del_orders.order_id')
                                    ->select('del_orders.*')
                                    ->where('del_orders.order_id',$request->get('order_id'))
                                    ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                                    ->get();
                if(DB::table('renewals')->where('renewals.order_id',$request->get('order_id'))->exists())
                {
                    // dd("Exists");
                    $collection_order_id = DB::table('renewals')->distinct('collection_order_id')->where('renewals.order_id',$request->get('order_id'))->get('collection_order_id')->pluck('collection_order_id');
                    // dd($collection_order_id);
                    $orders = DB::table('del_orders')->whereIn('order_id',$collection_order_id)->get();
                    $order_details = $order_details->merge($orders);
                    // dd($order_details);
                    
                }
                // else{
                //     dd("Not Exists");
                // }
                if(DB::table('pickups')->where('pickups.del_order_id',$request->get('order_id'))->exists()){
                    // dd("Exists");
                    $pickup_order_id = DB::table('pickups')->distinct('pickup_order_id')->where('pickups.del_order_id',$request->get('order_id'))->get('pickup_order_id')->pluck('pickup_order_id');
                    // dd($pickup_order_id);
                    $orders = DB::table('del_orders')->whereIn('order_id',$pickup_order_id)->get();
                    $order_details = $order_details->merge($orders);
                }
                // dd($order_details);
                $order_id = $request->get('order_id');
                return view('OrderManagement.order-search',compact('order_details','order_id'));
            }
        }
        public function generateInvoice(Request $request)
        {
            return view('generate-invoice');
        }

        public function generate_delivery_invoice(Request $request)
        {
            $invoice_type = 'Delivery';
            // dd($request->get('lead_id'));
            // $misc_details = DB::table('misc_table')->get();
            $orderTypeNotIn = config('app.order_type');
            $order_data = DB::table('del_orders')->join('leads','leads.id','=','del_orders.lead_id')->select('leads.patient_name','del_orders.*')->where('lead_id',$request->get('lead_id'))->whereNotIn('status',['Closed','Cancel'])->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)->get();
            $order_id = $order_data->pluck('order_id')->toArray();
            $data['order_date'] = $order_data[0]->DelDate;
            // dd($order_id);
            $order_details = DB::table('order_details')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                ->join('products','products.id','=','order_details.product_id')
                                ->select(
                                    'order_details.*',
                                    'products.product_name',
                                    'products.gst_rent',
                                    'products.gst_sale',
                                    'products.hsn_sac_rent',
                                    'customer_details.*',
                                    'products.hsn_sac_sale')
                                ->whereIn('order_details.order_id',$order_id)
                                ->whereNotIn('order_details.current_status',['Cancel'])
                                ->get();
            $customer_details = $order_details;
            $order_details = $order_details->groupBy('sale_rental');
            // dd($order_details);
            $product_details_rent = array();
            $product_details_sale = array();
            $transport_cost = 0;
            $total_rent = 0;
            $gst_percents = array();
            foreach($order_details as $key=>$value)
            {
                $temp_ids = array();
                // dd($key);
                foreach($value as $k=>$v)
                {
                    $v->product_rent = RenewalPickupController::fetchCrDrData($v->id,'R');
                    $v->product_deposit = RenewalPickupController::fetchCrDrData($v->id,'D');
                    $v->transport = RenewalPickupController::fetchCrDrData($v->id,'T');
                    if($v->billing_unit == 'Week'){
                        $pickupDate = date('Y-m-d',strtotime("+$v->billing_period $v->billing_unit",strtotime($v->creation_date)));
                    }else if($v->billing_unit == 'Half Month'){
                        $v->billing_period = $v->billing_period * 2;
                        $pickupDate = date('Y-m-d',strtotime("+$v->billing_period Week",strtotime($v->creation_date)));
                    }else if($v->billing_unit == 'Days'){
                        $pickupDate = date('Y-m-d',strtotime("+$v->billing_period Days",strtotime($v->creation_date)));
                    }else{
                        $pickupDate = date('Y-m-d',strtotime("+$v->billing_period months",strtotime($v->creation_date)));
                    }
                    if($key == 'Rental')
                    {
                        if(in_array($v->product_id,$temp_ids))
                        {
                            $index = array_search($v->product_id,$temp_ids);
                            $product_details_rent[$index]['product_qty'] = $product_details_rent[$index]['product_qty'] + $v->product_qty;
                            $product_details_rent[$index]['product_rent'] = $product_details_rent[$index]['product_rent'] + $v->product_rent;
                            $product_details_rent[$index]['product_deposit'] = $product_details_rent[$index]['product_deposit'] + $v->product_deposite;
                            $total_rent = $total_rent + $v->product_rent + $v->product_deposite;
                            $transport_cost = $transport_cost + $v->transport;
                        }
                        else
                        {
                            $index = count($product_details_rent);
                            $product_details_rent[$index]['product_name'] = $v->product_name;
                            $product_details_rent[$index]['unit'] = $v->billing_unit;
                            $product_details_rent[$index]['inventory_id'] = $v->unique_id;
                            $product_details_rent[$index]['order_id'] = $v->order_id;
                            $product_details_rent[$index]['product_qty'] = $v->product_qty;
                            $product_details_rent[$index]['product_rent'] = ($v->product_rent * $v->months);
                            $product_details_rent[$index]['months'] = $v->months;
                            $product_details_rent[$index]['product_deposit'] = $v->product_deposite;
                            $product_details_rent[$index]['creation_date'] = $v->creation_date;
                            $product_details_rent[$index]['pickup_date'] = $pickupDate;
                            $product_details_rent[$index]['sale_rental'] = $v->sale_rental;
                            $product_details_rent[$index]['gst_rent'] = $v->gst_rent;
                            $product_details_rent[$index]['gst_sale'] = $v->gst_sale;
                            $product_details_rent[$index]['hsn_sac_rent'] = $v->hsn_sac_rent;
                            $product_details_rent[$index]['hsn_sac_sale'] = $v->hsn_sac_sale;
                            $transport_cost = $transport_cost + $v->transport;
                            $total_rent = $total_rent +  ($v->product_rent * $v->months) + $v->product_deposite;
                            array_push($temp_ids,$v->product_id);
                        }
                        array_push($gst_percents,$v->gst_rent);
                    }
                    else
                    {
                        if(in_array($v->product_id,$temp_ids))
                        {
                            $index = array_search($v->product_id,$temp_ids);
                            $product_details_sale[$index]['product_qty'] = $product_details_sale[$index]['product_qty'] + $v->product_qty;
                            $product_details_sale[$index]['product_rent'] = $product_details_sale[$index]['product_rent'] + $v->product_rent;
                            $product_details_sale[$index]['product_deposit'] = $product_details_sale[$index]['product_deposit'] + $v->product_deposite;
                            $transport_cost = $transport_cost + $v->transport;
                            $total_rent = $total_rent + $v->product_rent + $v->product_deposite;
                        }
                        else
                        {
                            $index = count($product_details_sale);
                            $product_details_sale[$index]['product_name'] = $v->product_name;
                            $product_details_sale[$index]['unit'] = "-";
                            $product_details_sale[$index]['inventory_id'] = $v->unique_id;
                            $product_details_sale[$index]['order_id'] = $v->order_id;
                            $product_details_sale[$index]['product_qty'] = $v->product_qty;
                            $product_details_sale[$index]['product_rent'] = $v->product_rent;
                            $product_details_sale[$index]['product_deposit'] = $v->product_deposite;
                            $product_details_sale[$index]['creation_date'] = $v->creation_date;
                            $product_details_sale[$index]['pickup_date'] = $pickupDate;
                            $product_details_sale[$index]['sale_rental'] = $v->sale_rental;
                            $product_details_sale[$index]['gst_rent'] = $v->gst_rent;
                            $product_details_sale[$index]['gst_sale'] = $v->gst_sale;
                            $product_details_sale[$index]['hsn_sac_rent'] = $v->hsn_sac_rent;
                            $product_details_sale[$index]['hsn_sac_sale'] = $v->hsn_sac_sale;
                            $transport_cost = $transport_cost + $v->transport;
                            $total_rent = $total_rent + $v->product_rent + $v->product_deposite;
                            array_push($temp_ids,$v->product_id);
                        }
                        array_push($gst_percents,$v->gst_sale);
                    }
                }
            }
            $product_details = array_merge($product_details_sale,$product_details_rent);

            $temp_hsn_code = array();
            // $temp_hsn_code_sale = array();
            $hsn_code_details = array();
            // $hsn_code_details_sale = array();
            // dd($product_details);
            $rental_exists = false;
            $sale_exists = false;
            foreach($product_details as $key=>$value)
            {
                if($value['sale_rental'] == 'Rental')
                {
                    $rental_exists = true;
                    $product_details[$key]['product_rate'] = ((($value['product_rent']/$value['product_qty'])/($value['gst_rent']+100))*100);
                    $product_details[$key]['amount'] = ((($value['product_rent'])/($value['gst_rent']+100))*100);
                    $product_details[$key]['amount_cal'] = ((($value['product_rent'])/($value['gst_rent']+100))*100);
                    if(in_array($value['hsn_sac_rent'],$temp_hsn_code))
                    {
                        $index = array_search($value['hsn_sac_rent'],array_reverse($temp_hsn_code,true));
                        // echo $index.' - '.$hsn_code_details[$index]['gst'].' - '.$value['gst_rent'].' - '.json_encode(array_reverse($temp_hsn_code));
                        if($hsn_code_details[$index]['gst'] == $value['gst_rent'])
                        {
                            $hsn_code_details[$index]['taxable_value'] = $hsn_code_details[$index]['taxable_value'] + $product_details[$key]['amount'];
                            // $hsn_code_details[$index]['ct_rate'] = $value['gst_rent']/2;
                            $hsn_code_details[$index]['ct_amount'] = $hsn_code_details[$index]['taxable_value']*(($value['gst_rent']/2)/100);
                            $hsn_code_details[$index]['st_amount'] = $hsn_code_details[$index]['taxable_value']*(($value['gst_rent']/2)/100);
                            $hsn_code_details[$index]['i_amount'] = $hsn_code_details[$index]['taxable_value']*(($value['gst_rent'])/100);
                        }
                        else{
                            $index = count($hsn_code_details);
                            $hsn_code_details[$index]['hsn_sac'] = $value['hsn_sac_rent'];
                            $hsn_code_details[$index]['gst'] = $value['gst_rent'];
                            $hsn_code_details[$index]['taxable_value'] = $product_details[$key]['amount'];
                            $hsn_code_details[$index]['ct_rate'] = $value['gst_rent']/2;
                            $hsn_code_details[$index]['ct_amount'] = ($product_details[$key]['product_rate'])*(($value['gst_rent']/2)/100);    
                            $hsn_code_details[$index]['st_rate'] = $value['gst_rent']/2;
                            $hsn_code_details[$index]['st_amount'] = ($product_details[$key]['product_rate'])*(($value['gst_rent']/2)/100);    
                            $hsn_code_details[$index]['i_rate'] = $value['gst_rent'];
                            $hsn_code_details[$index]['i_amount'] = ($product_details[$key]['product_rate'])*(($value['gst_rent'])/100);    
                            array_push($temp_hsn_code,$value['hsn_sac_rent']);
                        }
                    }
                    else
                    {
                        $index = count($hsn_code_details);
                        $hsn_code_details[$index]['hsn_sac'] = $value['hsn_sac_rent'];
                        $hsn_code_details[$index]['gst'] = $value['gst_rent'];
                        $hsn_code_details[$index]['taxable_value'] = $product_details[$key]['amount'];
                        $hsn_code_details[$index]['ct_rate'] = $value['gst_rent']/2;
                        $hsn_code_details[$index]['ct_amount'] = ($product_details[$key]['product_rate'])*(($value['gst_rent']/2)/100);
                        $hsn_code_details[$index]['st_rate'] = $value['gst_rent']/2;
                        $hsn_code_details[$index]['st_amount'] = ($product_details[$key]['product_rate'])*(($value['gst_rent']/2)/100);
                        $hsn_code_details[$index]['i_rate'] = $value['gst_rent'];
                        $hsn_code_details[$index]['i_amount'] = ($product_details[$key]['product_rate'])*(($value['gst_rent'])/100);
                        array_push($temp_hsn_code,$value['hsn_sac_rent']);
                    }
                    $product_details[$key]['product_rate'] = number_format(((($value['product_rent']/$value['product_qty'])/($value['gst_rent']+100))*100),2);
                    $product_details[$key]['amount'] = number_format(((($value['product_rent'])/($value['gst_rent']+100))*100),2);
                }
                else
                {
                    $sale_exists = true;
                    $product_details[$key]['product_rate'] = ((($value['product_rent']/$value['product_qty'])/($value['gst_sale']+100))*100);
                    $product_details[$key]['amount'] = ((($value['product_rent'])/($value['gst_sale']+100))*100);
                    $product_details[$key]['amount_cal'] = ((($value['product_rent'])/($value['gst_sale']+100))*100);
                    if(in_array($value['hsn_sac_sale'],$temp_hsn_code))
                    {
                        $index = array_search($value['hsn_sac_sale'],array_reverse($temp_hsn_code,true));
                        if($hsn_code_details[$index]['gst'] == $value['gst_sale'])
                        {
                            $hsn_code_details[$index]['taxable_value'] = $hsn_code_details[$index]['taxable_value'] + $product_details[$key]['amount'];
                            // $hsn_code_details[$index]['ct_rate'] = $value['gst_sale']/2;
                            $hsn_code_details[$index]['ct_amount'] = $hsn_code_details[$index]['taxable_value']*($value['gst_sale']/100);
                            $hsn_code_details[$index]['st_amount'] = $hsn_code_details[$index]['taxable_value']*($value['gst_sale']/100);
                            $hsn_code_details[$index]['i_amount'] = $hsn_code_details[$index]['taxable_value']*($value['gst_sale']/100);
                        }
                        else{
                            $index = count($hsn_code_details);
                            $hsn_code_details[$index]['hsn_sac'] = $value['hsn_sac_sale'];
                            $hsn_code_details[$index]['gst'] = $value['gst_sale'];
                            $hsn_code_details[$index]['taxable_value'] = $product_details[$key]['amount'];
                            $hsn_code_details[$index]['ct_rate'] = $value['gst_sale']/2;
                            $hsn_code_details[$index]['ct_amount'] = ($product_details[$key]['product_rate'])*(($value['gst_sale']/2)/100);
                            $hsn_code_details[$index]['st_rate'] = $value['gst_sale']/2;
                            $hsn_code_details[$index]['st_amount'] = ($product_details[$key]['product_rate'])*(($value['gst_sale']/2)/100);
                            $hsn_code_details[$index]['i_rate'] = $value['gst_sale'];
                            $hsn_code_details[$index]['i_amount'] = ($product_details[$key]['product_rate'])*(($value['gst_sale'])/100);
                            array_push($temp_hsn_code,$value['hsn_sac_sale']);
                        }
                    }
                    else
                    {
                        $index = count($hsn_code_details);
                        $hsn_code_details[$index]['hsn_sac'] = $value['hsn_sac_sale'];
                        $hsn_code_details[$index]['gst'] = $value['gst_sale'];
                        $hsn_code_details[$index]['taxable_value'] = $product_details[$key]['amount'];
                        $hsn_code_details[$index]['ct_rate'] = $value['gst_sale']/2;
                        $hsn_code_details[$index]['ct_amount'] = ($product_details[$key]['product_rate'])*(($value['gst_sale']/2)/100);
                        $hsn_code_details[$index]['st_rate'] = $value['gst_sale']/2;
                        $hsn_code_details[$index]['st_amount'] = ($product_details[$key]['product_rate'])*(($value['gst_sale']/2)/100);
                        $hsn_code_details[$index]['i_rate'] = $value['gst_sale'];
                        $hsn_code_details[$index]['i_amount'] = ($product_details[$key]['product_rate'])*(($value['gst_sale'])/100);
                        array_push($temp_hsn_code,$value['hsn_sac_sale']);
                    }
                    $product_details[$key]['product_rate'] = number_format(((($value['product_rent']/$value['product_qty'])/($value['gst_sale']+100))*100),2);
                    $product_details[$key]['amount'] = number_format(((($value['product_rent'])/($value['gst_sale']+100))*100),2);
                }

            }

            
            foreach($product_details as $key=>$value)
            {
                if($value['sale_rental'] == 'Rental')
                {
                    $product_details[$key]['ct_rate'] = $value['gst_rent']/2;
                    $product_details[$key]['ct_amount'] = ($product_details[$key]['amount_cal'])*(($value['gst_rent']/2)/100);
                    $product_details[$key]['st_rate'] = $value['gst_rent']/2;
                    $product_details[$key]['st_amount'] = ($product_details[$key]['amount_cal'])*(($value['gst_rent']/2)/100);
                    $product_details[$key]['i_rate'] = $value['gst_rent'];
                    $product_details[$key]['i_amount'] = ($product_details[$key]['amount_cal'])*(($value['gst_rent'])/100);
                }
                else
                {
                    $product_details[$key]['ct_rate'] = $value['gst_sale']/2;
                    $product_details[$key]['ct_amount'] = ($product_details[$key]['amount_cal'])*(($value['gst_sale']/2)/100);
                    $product_details[$key]['st_rate'] = $value['gst_sale']/2;
                    $product_details[$key]['st_amount'] = ($product_details[$key]['amount_cal'])*(($value['gst_sale']/2)/100);
                    $product_details[$key]['i_rate'] = $value['gst_sale'];
                    $product_details[$key]['i_amount'] = ($product_details[$key]['amount_cal'])*(($value['gst_sale'])/100);
                }
            }

            // dd($hsn_code_details);
            // dd($product_details,max($gst_percents),$transport_cost);
            $orderTypeNotIn = config('app.order_type');
            $data['office_address'] = DB::table('misc_table')->where('field','office_address')->first('value')->value;
            $data['invoice_no_format'] = DB::table('misc_table')->where('field','invoice_no_format')->first('value')->value;
            $data['invoice_no'] = DB::table('del_orders')->where('lead_id',$request->get('lead_id'))->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)->first('invoice_no')->invoice_no;
            $data['pan_no'] = DB::table('misc_table')->where('field','pan_no')->first('value')->value;
            
            // if(in_array($customer_details[0]->citygroup,['Delhi','Gurgaon'])){
            //     $data['bank_name'] = DB::table('misc_table')->where('field','bank_name_del')->first('value')->value;
            //     $data['account_no'] = DB::table('misc_table')->where('field','account_no_del')->first('value')->value;
            //     $data['branch'] = DB::table('misc_table')->where('field','branch_del')->first('value')->value;
            //     $data['ifsc_code'] = DB::table('misc_table')->where('field','ifsc_code_del')->first('value')->value;
            // }else{
            //     $data['bank_name'] = DB::table('misc_table')->where('field','bank_name')->first('value')->value;
            //     $data['account_no'] = DB::table('misc_table')->where('field','account_no')->first('value')->value;
            //     $data['branch'] = DB::table('misc_table')->where('field','branch')->first('value')->value;
            //     $data['ifsc_code'] = DB::table('misc_table')->where('field','ifsc_code')->first('value')->value;
            // }
            if(in_array($customer_details[0]->citygroup,['Delhi','Gurgaon'])){
                $data['bank_name'] = DB::table('misc_table')->where('field','bank_name_del')->first('value')->value;
                $data['account_no'] = DB::table('misc_table')->where('field','account_no_del')->first('value')->value;
                $data['branch'] = DB::table('misc_table')->where('field','branch_del')->first('value')->value;
                $data['ifsc_code'] = DB::table('misc_table')->where('field','ifsc_code_del')->first('value')->value;
                $data['company_name'] = DB::table('misc_table')->where('field','comp_name_del')->first('value')->value;
                $data['company_addr_1'] = DB::table('misc_table')->where('field','comp_addr_1_del')->first('value')->value;
                $data['company_addr_2'] = DB::table('misc_table')->where('field','comp_addr_2_del')->first('value')->value;
                $data['company_gst'] = DB::table('misc_table')->where('field','comp_gst_del')->first('value')->value;
                $data['company_state'] = DB::table('misc_table')->where('field','comp_state_del')->first('value')->value;
                $data['company_state_code'] = DB::table('misc_table')->where('field','comp_state_code_del')->first('value')->value;
            }else{
                $data['bank_name'] = DB::table('misc_table')->where('field','bank_name')->first('value')->value;
                $data['account_no'] = DB::table('misc_table')->where('field','account_no')->first('value')->value;
                $data['branch'] = DB::table('misc_table')->where('field','branch')->first('value')->value;
                $data['ifsc_code'] = DB::table('misc_table')->where('field','ifsc_code')->first('value')->value;
                $data['company_name'] = DB::table('misc_table')->where('field','comp_name_mum')->first('value')->value;
                $data['company_addr_1'] = DB::table('misc_table')->where('field','comp_addr_1_mum')->first('value')->value;
                $data['company_addr_2'] = DB::table('misc_table')->where('field','comp_addr_2_mum')->first('value')->value;
                $data['company_gst'] = DB::table('misc_table')->where('field','comp_gst_mum')->first('value')->value;
                $data['company_state'] = DB::table('misc_table')->where('field','comp_state_mum')->first('value')->value;
                $data['company_state_code'] = DB::table('misc_table')->where('field','comp_state_code_mum')->first('value')->value;
            }

            if($customer_details[0]->customer_type == 'Corporate' && ($customer_details[0]->gst_no != null && $customer_details[0]->gst_no != ""))
            {
                if($rental_exists == true && $sale_exists == true)
                {
                    $data['invoice_no_format'] = DB::table('misc_table')->where('field','invoice_no_comp')->first('value')->value.'/SRG/'.$this->getFinancialYear(date('Y-m-d',strtotime($data['order_date']))).'/';
                }
                else if($rental_exists == true && $sale_exists == false)
                {
                    $data['invoice_no_format'] = DB::table('misc_table')->where('field','invoice_no_comp')->first('value')->value.'/SRG/'.$this->getFinancialYear(date('Y-m-d',strtotime($data['order_date']))).'/';
                }
                else if($rental_exists == false && $sale_exists == true)
                {
                    $data['invoice_no_format'] = DB::table('misc_table')->where('field','invoice_no_comp')->first('value')->value.'/SG/'.$this->getFinancialYear(date('Y-m-d',strtotime($data['order_date']))).'/';
                }
            }
            else{
                $data['invoice_no_format'] = DB::table('misc_table')->where('field','invoice_no_comp')->first('value')->value.'/SG/'.$this->getFinancialYear(date('Y-m-d',strtotime($data['order_date']))).'/';
            }
            if($customer_details[0]->customer_type == 'Corporate' && $order_data[0]->patient_name != null && $order_data[0]->patient_name != "")
            {
                $data['consignee_name'] = $order_data[0]->patient_name;    
            }
            else
            {
                $data['consignee_name'] = $order_data[0]->shipping_first_name;
            }
            $address = "";
            if($customer_details[0]->address_line_1 != null && $customer_details[0]->address_line_1 != "" )
            {
                $address .= $customer_details[0]->address_line_1.',';
            }
            if($customer_details[0]->address_line_2 != null && $customer_details[0]->address_line_2 != "" )
            {
                $address .= $customer_details[0]->address_line_2.',';
            }
            if($customer_details[0]->landmark != null && $customer_details[0]->landmark != "" )
            {
                $address .= $customer_details[0]->landmark.',';
            }
            if($customer_details[0]->area != null && $customer_details[0]->area != "" )
            {
                $address .= $customer_details[0]->area.',';
            }
            if($customer_details[0]->city != null && $customer_details[0]->city != "" )
            {
                $address .= $customer_details[0]->city;
            }
            if($customer_details[0]->pincode != null && $customer_details[0]->pincode != "" )
            {
                $address .= ' - '.$customer_details[0]->pincode;
            }
            $data['gst_no'] = null;
            if($customer_details[0]->gst_no != null && $customer_details[0]->gst_no != "")
            {
                $data['gst_no'] = $customer_details[0]->gst_no;
            }
            $data['buyer_state'] = $customer_details[0]->state;
            $data['consignee'] = $address;
            $data['consignee_state'] = $customer_details[0]->state;
            // $data['consignee'] = $order_data[0]->fulldetails;
            $data['buyer_name'] = $order_data[0]->shipping_first_name;
            $data['buyer'] = $address;
            $data['state_code'] = 27;
            if($customer_details[0]->corp_master_id != null)
            {
                // dd("not");
                $corp_cust_details = DB::table('corp_master')->where('id',$customer_details[0]->corp_master_id)->first();
                $buyer_address = $corp_cust_details->addr_line_1.', '.$corp_cust_details->addr_line_2.', '.$corp_cust_details->landmark.', '.$corp_cust_details->area.', '.$corp_cust_details->city.'-'.$corp_cust_details->pincode;
                $data['buyer_name'] = $corp_cust_details->corp_name;
                $data['gst_no'] = $corp_cust_details->gst_no;
                $data['buyer_state'] = $corp_cust_details->state;
                $data['buyer'] = $buyer_address;
                if($corp_cust_details->state_code != 27)
                {
                    $data['state_code'] = $corp_cust_details->state_code;
                }
                if($corp_cust_details->gst_no != null)
                {
                    if($rental_exists == true && $sale_exists == true)
                    {
                        $data['invoice_no_format'] = DB::table('misc_table')->where('field','invoice_no_comp')->first('value')->value.'/SRG/'.$this->getFinancialYear(date('Y-m-d',strtotime($data['order_date']))).'/';
                    }
                    else if($rental_exists == true && $sale_exists == false)
                    {
                        $data['invoice_no_format'] = DB::table('misc_table')->where('field','invoice_no_comp')->first('value')->value.'/SRG/'.$this->getFinancialYear(date('Y-m-d',strtotime($data['order_date']))).'/';
                    }
                    else if($rental_exists == false && $sale_exists == true)
                    {
                        $data['invoice_no_format'] = DB::table('misc_table')->where('field','invoice_no_comp')->first('value')->value.'/SG/'.$this->getFinancialYear(date('Y-m-d',strtotime($data['order_date']))).'/';
                    }
                }
            }
            
            
            // $data['buyer'] = $order_data[0]->fulldetails;
            
            $data['max_gst'] = max($gst_percents);
            // dd($data['max_gst']);
            $data['transport_hsn'] = DB::table('misc_table')->where('field','trans'.max($gst_percents))->first('value')->value;
            // dd($data['transport_hsn']);
            $data['total_amount'] = $this->getIndianCurrency($total_rent+$transport_cost+array_sum($order_data->pluck('labour_charges')->toArray()));
            $data['total_amount_no'] = $total_rent+$transport_cost+array_sum($order_data->pluck('labour_charges')->toArray());
            $exists = false;
            $data['total_taxable_value'] = 0;
            $data['total_central_tax'] = 0;
            $data['total_state_tax'] = 0;
            $data['total_i_tax'] = 0;
            $data['total_tax_amount'] = 0;
            $data['transport_cost'] = ((($transport_cost)/($data['max_gst']+100))*100);
            $data['labour'] = array_sum($order_data->pluck('labour_charges')->toArray());
            if($transport_cost != 0)
            {
                $data['transport_cal'] = array();
                $data['transport_cal']['ct_amount'] = (($data['transport_cost']*(($data['max_gst']/2)/100)));
                $data['transport_cal']['st_amount'] = (($data['transport_cost']*(($data['max_gst']/2)/100)));
            	$data['transport_cal']['i_amount'] = (($data['transport_cost']*(($data['max_gst'])/100)));
                $data['transport_cal']['taxable_value'] = $data['transport_cost'];
                foreach($hsn_code_details as $k=>$v)
                {
                    if($v['hsn_sac'] == $data['transport_hsn'] && $v['gst'] == $data['max_gst'])
                    {
                        // dd(";");
                        // dd((($transport_cost*(($data['max_gst']/2)/100))));
                        $exists = true;
                        $hsn_code_details[$k]['ct_amount'] = $hsn_code_details[$k]['ct_amount'] + (($data['transport_cost']*(($data['max_gst']/2)/100)));
                        $hsn_code_details[$k]['st_amount'] = $hsn_code_details[$k]['st_amount'] + (($data['transport_cost']*(($data['max_gst']/2)/100)));
                    	$hsn_code_details[$k]['i_amount'] = $hsn_code_details[$k]['i_amount'] + (($data['transport_cost']*(($data['max_gst'])/100)));
                        $hsn_code_details[$k]['taxable_value'] = $hsn_code_details[$k]['taxable_value'] + $data['transport_cost'];
                    }
                }
                if($exists == false)
                {
                    $index = count($hsn_code_details);
                    $hsn_code_details[$index]['hsn_sac'] = $data['transport_hsn'];
                    $hsn_code_details[$index]['gst'] = $data['max_gst'];
                    $hsn_code_details[$index]['taxable_value'] = $data['transport_cost'];
                    $hsn_code_details[$index]['ct_rate'] = $data['max_gst']/2;
                    $hsn_code_details[$index]['ct_amount'] = ($data['transport_cost'])*(($data['max_gst']/2)/100);
                    
                    $hsn_code_details[$index]['st_rate'] = $data['max_gst']/2;
                    $hsn_code_details[$index]['st_amount'] = ($data['transport_cost'])*(($data['max_gst']/2)/100);
                $hsn_code_details[$index]['i_rate'] = $data['max_gst'];
                $hsn_code_details[$index]['i_amount'] = ($data['transport_cost'])*(($data['max_gst'])/100);
                }
            }
            // dd($hsn_code_details);
            foreach($hsn_code_details as $k=>$v)
            {
                $data['total_taxable_value'] = $data['total_taxable_value'] + $v['taxable_value'];
                $data['total_central_tax'] = $data['total_central_tax'] + $v['ct_amount'];
                $data['total_state_tax'] = $data['total_state_tax'] + $v['st_amount'];
                $data['total_i_tax'] = $data['total_i_tax'] + $v['i_amount'];
                $data['total_tax_amount'] = $data['total_tax_amount'] + ($v['ct_amount'] + $v['st_amount']);
            }
            $data['total_tax_amount_word'] = $this->getIndianCurrency($data['total_tax_amount']);
            return view('generate-invoice',compact('data','product_details','hsn_code_details','invoice_type'));
            // $pdf_data['data'] = $data;
            // $pdf_data['product_details'] = $product_details;
            // $pdf_data['hsn_code_details'] = $hsn_code_details;
            // $pdf = PDF::loadView('generate-invoice', $pdf_data);
            // file_put_contents("/var/www/html/devweb/eflow/assets/uploads/invoices/".$request->get('lead_id').".pdf", $pdf->output()); 
            // $file_path = "/assets/uploads/invoices/".$request->get('lead_id').".pdf";
            // return $file_path;
        }
        public function generate_renewal_invoice(Request $request)
        {
            $invoice_type = 'Renewal';
            $order_data = DB::table('del_orders')->join('leads','leads.id','=','del_orders.lead_id')->select('leads.patient_name','del_orders.*')->where('order_id',$request->get('order_id'))->whereNotIn('status',['Closed','Cancel'])->get();
            $order_id = $order_data->pluck('order_id')->toArray();
            $data['order_date'] = $order_data[0]->DelDate;
            $order_details = DB::table('renewals')
                                ->join('order_details','order_details.id','=','renewals.order_details_id')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                ->join('products','products.id','=','renewals.product_id')
                                ->select(
                                    'renewals.*',
                                    'products.product_name',
                                    'products.gst_rent',
                                    'products.gst_sale',
                                    'products.hsn_sac_rent',
                                    'order_details.*',
                                    'customer_details.*',
                                    'products.hsn_sac_sale')
                                ->whereIn('renewals.collection_order_id',$order_id)
                                ->whereNotIn('renewals.status',['Cancel'])
                                ->get();
            $customer_details = $order_details;
            // $order_details = $order_details->groupBy('order_details_id');
            // dd($order_details);
            $product_details_rent = array();
            // $product_details_sale = array();
            $transport_cost = 0;
            $total_rent = 0;
            $gst_percents = array();
            $temp_ids = array();
            foreach($order_details as $key=>$value)
            {
                if(in_array($value->order_details_id,$temp_ids))
                {
                    $index = array_search($value->order_details_id,$temp_ids);
                    $product_details_rent[$index]['product_qty'] = $product_details_rent[$index]['product_qty'] + $value->product_qty;
                    $product_details_rent[$index]['product_rent'] = $product_details_rent[$index]['product_rent'] + ($value->product_rent - $value->discount_amt);
                    $product_details_rent[$index]['product_deposit'] = 0;
                    $product_details_rent[$index]['end_date'] = $value->end_date;
                    $total_rent = $total_rent + ($value->product_rent - $value->discount_amt);
                    $transport_cost = $transport_cost + $value->transport;
                }
                else
                {
                    $index = count($product_details_rent);
                    $product_details_rent[$index]['product_name'] = $value->product_name;
                    $product_details_rent[$index]['unit'] = $value->billing_unit;
                    $product_details_rent[$index]['inventory_id'] = $value->unique_id;
                    $product_details_rent[$index]['order_id'] = $value->collection_order_id;
                    $product_details_rent[$index]['product_qty'] = $value->product_qty;
                    $product_details_rent[$index]['product_rent'] = ($value->product_rent - $value->discount_amt);
                    $product_details_rent[$index]['product_deposit'] = 0;
                    $product_details_rent[$index]['creation_date'] = $value->creation_date;
                    $product_details_rent[$index]['pickup_date'] = $value->pickup_date;
                    $product_details_rent[$index]['start_date'] = $value->start_date;
                    $product_details_rent[$index]['end_date'] = $value->end_date;
                    $product_details_rent[$index]['sale_rental'] = $value->sale_rental;
                    $product_details_rent[$index]['gst_rent'] = $value->gst_rent;
                    $product_details_rent[$index]['gst_sale'] = $value->gst_sale;
                    $product_details_rent[$index]['hsn_sac_rent'] = $value->hsn_sac_rent;
                    $product_details_rent[$index]['hsn_sac_sale'] = $value->hsn_sac_sale;
                    $transport_cost = $transport_cost + $value->transport;
                    $total_rent = $total_rent + ($value->product_rent - $value->discount_amt);
                    array_push($temp_ids,$value->order_details_id);
                }
                array_push($gst_percents,$value->gst_rent);
            }
            $product_details = $product_details_rent;

            // dd($product_details);

            $temp_hsn_code = array();
            // $temp_hsn_code_sale = array();
            $hsn_code_details = array();
            // $hsn_code_details_sale = array();
            // dd($product_details);
            foreach($product_details as $key=>$value)
            {
                $product_details[$key]['product_rate'] = ((($value['product_rent']/$value['product_qty'])/($value['gst_rent']+100))*100);
                $product_details[$key]['amount'] = ((($value['product_rent'])/($value['gst_rent']+100))*100);
                $product_details[$key]['amount_cal'] = ((($value['product_rent'])/($value['gst_rent']+100))*100);
                if(in_array($value['hsn_sac_rent'],$temp_hsn_code))
                {
                    $index = array_search($value['hsn_sac_rent'],array_reverse($temp_hsn_code,true));
                    // echo $index.' - '.$hsn_code_details[$index]['gst'].' - '.$value['gst_rent'].' - '.json_encode(array_reverse($temp_hsn_code));
                    if($hsn_code_details[$index]['gst'] == $value['gst_rent'])
                    {
                        $hsn_code_details[$index]['taxable_value'] = $hsn_code_details[$index]['taxable_value'] + $product_details[$key]['amount'];
                        // $hsn_code_details[$index]['ct_rate'] = $value['gst_rent']/2;
                        $hsn_code_details[$index]['ct_amount'] = $hsn_code_details[$index]['taxable_value']*(($value['gst_rent']/2)/100);
                        $hsn_code_details[$index]['st_amount'] = $hsn_code_details[$index]['taxable_value']*(($value['gst_rent']/2)/100);
                            $hsn_code_details[$index]['i_amount'] = $hsn_code_details[$index]['taxable_value']*(($value['gst_rent'])/100);
                    }
                    else{
                        $index = count($hsn_code_details);
                        $hsn_code_details[$index]['hsn_sac'] = $value['hsn_sac_rent'];
                        $hsn_code_details[$index]['gst'] = $value['gst_rent'];
                        $hsn_code_details[$index]['taxable_value'] = $product_details[$key]['amount'];
                        $hsn_code_details[$index]['ct_rate'] = $value['gst_rent']/2;
                        $hsn_code_details[$index]['ct_amount'] = ($product_details[$key]['product_rate'])*(($value['gst_rent']/2)/100);    
                        $hsn_code_details[$index]['st_rate'] = $value['gst_rent']/2;
                        $hsn_code_details[$index]['st_amount'] = ($product_details[$key]['product_rate'])*(($value['gst_rent']/2)/100);    
                            $hsn_code_details[$index]['i_rate'] = $value['gst_rent'];
                            $hsn_code_details[$index]['i_amount'] = ($product_details[$key]['product_rate'])*(($value['gst_rent'])/100);    
                        array_push($temp_hsn_code,$value['hsn_sac_rent']);
                    }
                }
                else
                {
                    $index = count($hsn_code_details);
                    $hsn_code_details[$index]['hsn_sac'] = $value['hsn_sac_rent'];
                    $hsn_code_details[$index]['gst'] = $value['gst_rent'];
                    $hsn_code_details[$index]['taxable_value'] = $product_details[$key]['amount'];
                    $hsn_code_details[$index]['ct_rate'] = $value['gst_rent']/2;
                    $hsn_code_details[$index]['ct_amount'] = ($product_details[$key]['product_rate'])*(($value['gst_rent']/2)/100);
                    $hsn_code_details[$index]['st_rate'] = $value['gst_rent']/2;
                    $hsn_code_details[$index]['st_amount'] = ($product_details[$key]['product_rate'])*(($value['gst_rent']/2)/100);
                        $hsn_code_details[$index]['i_rate'] = $value['gst_rent'];
                        $hsn_code_details[$index]['i_amount'] = ($product_details[$key]['product_rate'])*(($value['gst_rent'])/100);
                    array_push($temp_hsn_code,$value['hsn_sac_rent']);
                }
                $product_details[$key]['product_rate'] = number_format(((($value['product_rent']/$value['product_qty'])/($value['gst_rent']+100))*100),2);
                $product_details[$key]['amount'] = number_format(((($value['product_rent'])/($value['gst_rent']+100))*100),2);

            }
            foreach($product_details as $key=>$value)
            {
                if($value['sale_rental'] == 'Rental')
                {
                    $product_details[$key]['ct_rate'] = $value['gst_rent']/2;
                    $product_details[$key]['ct_amount'] = ($product_details[$key]['amount_cal'])*(($value['gst_rent']/2)/100);
                    $product_details[$key]['st_rate'] = $value['gst_rent']/2;
                    $product_details[$key]['st_amount'] = ($product_details[$key]['amount_cal'])*(($value['gst_rent']/2)/100);
                    $product_details[$key]['i_rate'] = $value['gst_rent'];
                    $product_details[$key]['i_amount'] = ($product_details[$key]['amount_cal'])*(($value['gst_rent'])/100);
                }
                else
                {
                    $product_details[$key]['ct_rate'] = $value['gst_sale']/2;
                    $product_details[$key]['ct_amount'] = ($product_details[$key]['amount_cal'])*(($value['gst_sale']/2)/100);
                    $product_details[$key]['st_rate'] = $value['gst_sale']/2;
                    $product_details[$key]['st_amount'] = ($product_details[$key]['amount_cal'])*(($value['gst_sale']/2)/100);
                    $product_details[$key]['i_rate'] = $value['gst_sale'];
                    $product_details[$key]['i_amount'] = ($product_details[$key]['amount_cal'])*(($value['gst_sale'])/100);
                }
            }
            


            // dd($hsn_code_details);
            // dd($product_details,max($gst_percents),$transport_cost);

            $data['office_address'] = DB::table('misc_table')->where('field','office_address')->first('value')->value;
            $data['invoice_no_format'] = DB::table('misc_table')->where('field','invoice_no_format')->first('value')->value;
            $data['invoice_no'] = DB::table('del_orders')->where('order_id',$request->get('order_id'))->first('invoice_no')->invoice_no;
            $data['pan_no'] = DB::table('misc_table')->where('field','pan_no')->first('value')->value;
            // $data['bank_name'] = DB::table('misc_table')->where('field','bank_name')->first('value')->value;
            // $data['account_no'] = DB::table('misc_table')->where('field','account_no')->first('value')->value;
            // $data['branch'] = DB::table('misc_table')->where('field','branch')->first('value')->value;
            // $data['ifsc_code'] = DB::table('misc_table')->where('field','ifsc_code')->first('value')->value;
            if(in_array($customer_details[0]->citygroup,['Delhi','Gurgaon'])){
                $data['bank_name'] = DB::table('misc_table')->where('field','bank_name_del')->first('value')->value;
                $data['account_no'] = DB::table('misc_table')->where('field','account_no_del')->first('value')->value;
                $data['branch'] = DB::table('misc_table')->where('field','branch_del')->first('value')->value;
                $data['ifsc_code'] = DB::table('misc_table')->where('field','ifsc_code_del')->first('value')->value;
                $data['company_name'] = DB::table('misc_table')->where('field','comp_name_del')->first('value')->value;
                $data['company_addr_1'] = DB::table('misc_table')->where('field','comp_addr_1_del')->first('value')->value;
                $data['company_addr_2'] = DB::table('misc_table')->where('field','comp_addr_2_del')->first('value')->value;
                $data['company_gst'] = DB::table('misc_table')->where('field','comp_gst_del')->first('value')->value;
                $data['company_state'] = DB::table('misc_table')->where('field','comp_state_del')->first('value')->value;
                $data['company_state_code'] = DB::table('misc_table')->where('field','comp_state_code_del')->first('value')->value;
            }else{
                $data['bank_name'] = DB::table('misc_table')->where('field','bank_name')->first('value')->value;
                $data['account_no'] = DB::table('misc_table')->where('field','account_no')->first('value')->value;
                $data['branch'] = DB::table('misc_table')->where('field','branch')->first('value')->value;
                $data['ifsc_code'] = DB::table('misc_table')->where('field','ifsc_code')->first('value')->value;
                $data['company_name'] = DB::table('misc_table')->where('field','comp_name_mum')->first('value')->value;
                $data['company_addr_1'] = DB::table('misc_table')->where('field','comp_addr_1_mum')->first('value')->value;
                $data['company_addr_2'] = DB::table('misc_table')->where('field','comp_addr_2_mum')->first('value')->value;
                $data['company_gst'] = DB::table('misc_table')->where('field','comp_gst_mum')->first('value')->value;
                $data['company_state'] = DB::table('misc_table')->where('field','comp_state_mum')->first('value')->value;
                $data['company_state_code'] = DB::table('misc_table')->where('field','comp_state_code_mum')->first('value')->value;
            }
            if($customer_details[0]->customer_type == 'Corporate' && ($customer_details[0]->gst_no != null && $customer_details[0]->gst_no != ""))
            {
                $data['invoice_no_format'] = DB::table('misc_table')->where('field','invoice_no_comp')->first('value')->value.'/SRG/'.$this->getFinancialYear(date('Y-m-d',strtotime($data['order_date']))).'/';
            }
            if($customer_details[0]->customer_type == 'Corporate' && $order_data[0]->patient_name != null && $order_data[0]->patient_name != "")
            {
                $data['consignee_name'] = $order_data[0]->patient_name;    
            }
            else
            {
                $data['consignee_name'] = $order_data[0]->shipping_first_name;
            }
            $address = "";
            if($customer_details[0]->address_line_1 != null && $customer_details[0]->address_line_1 != "" )
            {
                $address .= $customer_details[0]->address_line_1.',';
            }
            if($customer_details[0]->address_line_2 != null && $customer_details[0]->address_line_2 != "" )
            {
                $address .= $customer_details[0]->address_line_2.',';
            }
            if($customer_details[0]->landmark != null && $customer_details[0]->landmark != "" )
            {
                $address .= $customer_details[0]->landmark.',';
            }
            if($customer_details[0]->area != null && $customer_details[0]->area != "" )
            {
                $address .= $customer_details[0]->area.',';
            }
            if($customer_details[0]->city != null && $customer_details[0]->city != "" )
            {
                $address .= $customer_details[0]->city;
            }
            if($customer_details[0]->pincode != null && $customer_details[0]->pincode != "" )
            {
                $address .= ' - '.$customer_details[0]->pincode;
            }
            $data['gst_no'] = null;
            if($customer_details[0]->gst_no != null && $customer_details[0]->gst_no != "")
            {
                $data['gst_no'] = $customer_details[0]->gst_no;
            }
            $data['buyer_state'] = $customer_details[0]->state;
            $data['consignee'] = $address;
            $data['consignee_state'] = $customer_details[0]->state;
            // $data['consignee'] = $order_data[0]->fulldetails;
            $data['buyer_name'] = $order_data[0]->shipping_first_name;
            $data['buyer'] = $address;
            $data['state_code'] = 27;
            if($customer_details[0]->corp_master_id != null)
            {
                // dd("not");
                $corp_cust_details = DB::table('corp_master')->where('id',$customer_details[0]->corp_master_id)->first();
                $buyer_address = $corp_cust_details->addr_line_1.', '.$corp_cust_details->addr_line_2.', '.$corp_cust_details->landmark.', '.$corp_cust_details->area.', '.$corp_cust_details->city.'-'.$corp_cust_details->pincode;
                $data['buyer_name'] = $corp_cust_details->corp_name;
                $data['gst_no'] = $corp_cust_details->gst_no;
                $data['buyer_state'] = $corp_cust_details->state;
                $data['buyer'] = $buyer_address;
                if($corp_cust_details->state_code != 27)
                {
                    $data['state_code'] = $corp_cust_details->state_code;
                }
                if($corp_cust_details->gst_no != null)
                {
                    $data['invoice_no_format'] = DB::table('misc_table')->where('field','invoice_no_comp')->first('value')->value.'/SRG/'.$this->getFinancialYear(date('Y-m-d',strtotime($data['order_date']))).'/';
                }
            }
            
            
            // $data['buyer'] = $order_data[0]->fulldetails;
            
            $data['max_gst'] = max($gst_percents);
            // dd($data['max_gst']);
            $data['transport_hsn'] = DB::table('misc_table')->where('field','trans'.max($gst_percents))->first('value')->value;
            // dd($data['transport_hsn']);
            $data['transport_cost'] = ((($transport_cost)/($data['max_gst']+100))*100);
            $data['total_amount'] = $this->getIndianCurrency($total_rent);
            $data['total_amount_no'] = $total_rent;
            // dd($data['total_amount_no']);
            $exists = false;
            $data['total_taxable_value'] = 0;
            $data['total_central_tax'] = 0;
            $data['total_state_tax'] = 0;
            $data['total_tax_amount'] = 0;
            $data['total_i_tax'] = 0;
            foreach($hsn_code_details as $k=>$v)
            {
                $data['total_taxable_value'] = $data['total_taxable_value'] + $v['taxable_value'];
                $data['total_central_tax'] = $data['total_central_tax'] + $v['ct_amount'];
                $data['total_state_tax'] = $data['total_state_tax'] + $v['st_amount'];
                $data['total_i_tax'] = $data['total_i_tax'] + $v['i_amount'];
                $data['total_tax_amount'] = $data['total_tax_amount'] + ($v['ct_amount'] + $v['st_amount']);
            }
            $data['total_tax_amount_word'] = $this->getIndianCurrency($data['total_tax_amount']);
            return view('generate-invoice',compact('data','product_details','hsn_code_details','invoice_type'));
            $pdf_data['data'] = $data;
            $pdf_data['product_details'] = $product_details;
            $pdf_data['hsn_code_details'] = $hsn_code_details;
            $pdf = PDF::loadView('generate-invoice', $pdf_data);
            file_put_contents("/var/www/html/devweb/eflow/assets/uploads/invoices/".$request->get('lead_id').".pdf", $pdf->output()); 
            $file_path = "/assets/uploads/invoices/".$request->get('lead_id').".pdf";
            // return $file_path;
        }
        function getIndianCurrency(float $number)
        {
            $decimal = round($number - ($no = floor($number)), 2) * 100;
            $hundred = null;
            $digits_length = strlen($no);
            $i = 0;
            $str = array();
            $words = array(0 => '', 1 => 'One', 2 => 'Two',
                3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
                7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
                10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
                13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
                16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
                19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
                40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
                70 => 'Seventy', 80 => 'Eeighty', 90 => 'Ninety');
            $digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
            while( $i < $digits_length ) {
                $divider = ($i == 2) ? 10 : 100;
                $number = floor($no % $divider);
                $no = floor($no / $divider);
                $i += $divider == 10 ? 1 : 2;
                if ($number) {
                    $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                    $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                    $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
                } else $str[] = null;
            }
            $Rupees = implode('', array_reverse($str));
            $paise = ($decimal > 0) ? "and " . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise only' : 'only';
            return ($Rupees ? $Rupees . '' : '') . $paise;
        }

        function getOrderSatusCount(Request $request){
            $today = Carbon::today()->toDateString();
            $status_arr = ["Converted","Order Generated","Vendor Assigned","Delivery In Progress"];
            $get_all_leads = DB::table('leads')
                                    ->select('id','lead_status')
                                    ->whereIn('leads.lead_status',$status_arr)
                                    //->where('leads.creation_date',$today)
                                    ->where(DB::raw("(STR_TO_DATE(leads.converted_at,'%Y-%m-%d'))"),$today)
                                    ->get();
            $leadIdsForcount = $get_all_leads->where('lead_status','Order Generated')->pluck('id');
            
            $orderConvertedCount = $get_all_leads->where('lead_status','Converted')->count();
            $getOrdersCountStatuswise = DB::table('del_orders')->whereIn('lead_id',$leadIdsForcount)->where('deliverypickup','Delivery')->whereNotNull('status')->get(['lead_id','order_id','status']);
            $totalOrderCount = $getOrdersCountStatuswise->count() + $orderConvertedCount;
            $ordersStateCount = $getOrdersCountStatuswise->groupBy('status');
            return compact('ordersStateCount','orderConvertedCount','totalOrderCount');
            
        }
        
        function delveryOrdersAll(Request $request){
            $leadowners = DB::table('user')
                        ->select('id as user_id','username as lead_owner')
                        ->whereIn('role',["user","admin","superuser"])
                        ->get();
            $cities = DB::table('customer_details')->distinct('city')->whereNotNull('city')->orderBy('city','ASC')->get('city');
    
            $today = Carbon::now()->toDateString();
            $fromdate = $today;
            $enddate = $today;
            if($request->get('filter_from_date')!=null && $request->get('filter_end_date')!=null){
                $fromdate = $request->get('filter_from_date');
                $enddate = $request->get('filter_end_date');
            }
    
            $leadstatus = $request->get('filter_lead_status');
            if($request->get('filter_order_status')!=null){
                $leadstatus = null;
            }
            if($request->get('filter_order_status')=='Converted'){
                $leadstatus = 'Converted';
            }
            $sessionuserrole = session('role');
            
            $get_all_leads = DB::table('leads')
                ->join('customer_details','leads.customer_id','=','customer_details.cust_id')
                ->join('user','leads.lead_owner','=','user.id')
                ->select('leads.*','customer_details.*','leads.id as lead_id','user.*')
                ->when($request->get('filter_order_status')!=null && $request->get('filter_order_status')!='Converted',function($query)use($request){
                    $query->join('del_orders',function($join)use($request){
                        $join->on('del_orders.lead_id','=','leads.id')
                            ->where('del_orders.status',$request->get('filter_order_status'))
                            ->where('deliverypickup','Delivery')
                            ->whereIn('lead_status',["Order Generated","Vendor Assigned","Delivery In Progress"]);
                    });
                })
                ->when($request->get('filter_customer_name'),function($query)use($request){
                    $query->where('customer_details.customer_name','LIKE','%'.$request->get('filter_customer_name').'%');
                })
                ->when($request->get('filter_contact_no'),function($query)use($request){
                    $query->where('customer_details.primary_contact_no','=',$request->get('filter_contact_no'));
                })
                ->when($request->get('filter_patient_name'),function($query)use($request){
                    $query->where('leads.patient_name','LIKE','%'.$request->get('filter_patient_name').'%');
                })
                // ->whereIn('lead_status',["Converted","Order Generated","Vendor Assigned","Delivery In Progress"])
                ->when($leadstatus!='All' && $leadstatus!=null,function($query)use($request,$leadstatus){
                    $query->where('leads.lead_status','=',$leadstatus);
                })
                ->when($request->get('filter_lead_owner')!='All' && $sessionuserrole,function($query)use($request,$sessionuserrole){
                    if($sessionuserrole=='user'){
                        $query->where('leads.lead_owner','=',session('user_id'));
                    }
                    if($request->get('filter_lead_owner')!=null){
                        $query->where('leads.lead_owner','=',$request->get('filter_lead_owner'));
                    }
                })
                ->when($request->get('filter_city')!='All' && $request->get('filter_city')!=null,function($query)use($request){
                    $query->where('customer_details.city','=',$request->get('filter_city'));
                })
                //->whereBetween('leads.creation_date',[$fromdate,$enddate])
                ->wherebetween(DB::raw("(STR_TO_DATE(leads.converted_at,'%Y-%m-%d'))"),[$fromdate,$enddate])
                ->get()
                ->unique();
                // echo '<pre>';print_r($get_all_leads);die;
            $customers = $get_all_leads->pluck('customer_id')->unique()->count();
            $productscount = Collect(['sale'=>0,'rent'=>0,'total'=>0]);
            $amountcount = Collect(['rent'=>0,'sale'=>0,'deposit'=>0,'transport'=>0,'total'=>0]);
    
            $generatedordersid = $get_all_leads->where('lead_status','Order Generated')->pluck('lead_id');
            $getorders = DB::table('del_orders')
                ->join('vendor_details','del_orders.vendor_id','=','vendor_details.id')
                ->whereIn('lead_id',$generatedordersid)
                ->where('deliverypickup','Delivery')
                ->when($request->get('filter_order_status')!='Converted' && $request->get('filter_order_status')!=null,function($query)use($request){
                    $query->where('status',$request->get('filter_order_status'));
                })
                ->select('del_orders.*','vendor_details.registered_name as vendor_name')->get()->groupBy('lead_id');
            $getleadlog = DB::table('leads_log')->whereIn('log_lead_id',$generatedordersid)->where('log_order_type','DO')->get()->groupBy('log_order_id');
            //dd($getleadlog);
            foreach ($getorders as $getorderskey => $orders) {
                foreach ($orders as $orderkey => $order) {
                    $gentime = null;
                    $comptime = null;
                    if($getleadlog->has($order->order_id)){
                        if($getleadlog[$order->order_id]->contains('log_lead_status','Order Generated')){
                            $gentime = $getleadlog[$order->order_id]->where('log_lead_status','Order Generated')->first()->created_at;
                        }
                        if($getleadlog[$order->order_id]->contains('log_lead_status','Order Assigned')){
                            $gentime = $getleadlog[$order->order_id]->where('log_lead_status','Order Assigned')->first()->created_at;
                        }
                        if($getleadlog[$order->order_id]->contains('log_lead_status','Order Delivered')){
                            $comptime = $getleadlog[$order->order_id]->where('log_lead_status','Order Delivered')->first()->created_at;
                        }
                        $deliveredtime = 'Pending';
                        if($gentime!=null && $comptime!=null){
                            $starttime = Carbon::parse($gentime);
                            $endtime = Carbon::parse($comptime);
                            $diff = $starttime->diffInMinutes($endtime);
                            if($diff>90 && $diff<=240 ){
                                $deliveredtime = 'On Time';
                            }
                            elseif($diff<90){
                                $deliveredtime = 'Exception';
                            }
                            // else if($diff>240){
                            else{
                                $deliveredtime = 'Delay';
                            }
                        }
                        $getorders[$getorderskey][$orderkey]->deliveredtimestatus = $deliveredtime;
                    }
                    else{
                        $getorders[$getorderskey][$orderkey]->deliveredtimestatus = null;
                    }
                }
            }
            //dd($getleadlog);
            //dd($getleadlog[8819]->where('log_lead_status','Order Generated')->pluck('log_order_id'));
            $orderstatus = Collect();
            $statustotal = 0;
            foreach($get_all_leads as $leadkey=>$lead){
                //add products name
                $productids = json_decode($lead->equipment_requirement,true);
                $product_names = DB::table('products')->whereIn('id',$productids)->get('product_name')->pluck('product_name')->implode(',');
                $get_all_leads[$leadkey]->products_name = $product_names;
    
                //product and amount count
                $salerental = json_decode($lead->sale_rental,true);
                $totalrent = json_decode($lead->offered_rent_total,true);
                $totaldeposit = json_decode($lead->deposite_total,true);
                $totaltransport = json_decode($lead->transport,true);
                //dd($totalrent);
                foreach ($productids as $prdidkey => $prdid) {
                    if($salerental[$prdidkey]=='Sale'){
                        $productscount['sale']+=1;
                        $amountcount['sale']+=$totalrent[$prdidkey];
                    }else{
                        $productscount['rent']+=1;
                        $amountcount['rent']+=$totalrent[$prdidkey];
                    }
                    $amountcount['deposit']+=$totaldeposit[$prdidkey];
                    $amountcount['transport']+=$totaltransport[$prdidkey];
                    $amountcount['total']+=$totaltransport[$prdidkey]+$totalrent[$prdidkey]+$totaldeposit[$prdidkey];;
                    $productscount['total']+=1;
    
                }
                if($getorders->has($lead->lead_id)){
                    $get_all_leads[$leadkey]->orders = $getorders[$lead->lead_id];
                    $orderstatus = $orderstatus->merge($getorders[$lead->lead_id]->pluck('status'));
                }
                else{
                    $get_all_leads[$leadkey]->orders = null;
                }
            }
            $statustotal = $orderstatus->count()+$get_all_leads->where('lead_status','Converted')->count();
            $orderstatus = array_count_values($orderstatus->toArray());
            $orderstatus['Converted'] = $get_all_leads->where('lead_status','Converted')->count();
            if($request->get('btn_export'))
            {
                ob_end_clean(); // this
                ob_start(); // and this
                return Excel::download(new OrderDeliveryAllReport($get_all_leads,$amountcount,$productscount,$customers,$statustotal,$orderstatus), 'converted_orders.xls');
            }
            $get_all_leads = $get_all_leads->paginate(10);
            return view('OrderManagement.order-delivery-all',compact('get_all_leads','amountcount','productscount','customers','statustotal','orderstatus','leadowners','cities'));
        }
        public function renewalnotice(){
            $order_details = DB::table('del_orders')->join('order_details','order_details.order_id','=','del_orders.order_id')->join('customer_details','order_details.customer_id','=','customer_details.cust_id')->select('del_orders.mobileno')->distinct('del_orders.mobileno')->whereNotIn('customer_details.customer_type',['Corporate'])->where('order_details.sale_rental','Rental')->whereIn('order_details.current_status',['Pending','Pending Renew','Renewed','Renewed Online','CustStop'])->whereNotIn('del_orders.status',['Cancel','Rejected','Cust Rejected'])->get();
            $order_details = $order_details->pluck('mobileno')->toArray();
            foreach($order_details as $key=>$value){
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
                    "mobileno"=> "$value",
                    "templatename" => "renewal_notice",
                    "templateparams" => [
                        ["type"=> "text","text"=> "9643503583"],
                    ],
                ];
                //return $data;
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                $resp = curl_exec($curl);
                curl_close($curl);
                // dd($resp);
            }
        }
        public function getFinancialYear($date){
            if (date('m',strtotime($date)) > 3) {
                $year = date('y',strtotime($date))."-".(date('y',strtotime($date)) +1);
            }
            else {
                $year = (date('y',strtotime($date))-1)."-".date('y',strtotime($date));
            }
            // echo $year; // 2015-2016
            return $year;
        }
        public function addWarehouseBrand(Request $request){
            if($request->method() == 'GET'){
                try{
                    if($request->get('requesttype') == 'warehouse' || $request->get('requesttype') == 'brand'){
                        $warehouse_details = DB::table('vendor_warehouse')->select('id','wh_name','wh_area','wh_city')->where('vendor_id',$request->get('vendorid'))->get();
                        $product_brands = DB::table('product_brands')->where('product_id',$request->get('productid'))->get();
                        $data['warehouse_details'] = $warehouse_details;
                        $data['product_brands'] = $product_brands;
                        return json_encode($data);
                    }
                    elseif($request->get('requesttype') == 'addnewbrand'){
                        DB::beginTransaction();
                        try{
                            $id = DB::table('product_brands')->insertGetId(['product_id'=>$request->get('productid'),'brand_name'=>$request->get('brandname')]);
                            DB::commit();
                            return json_encode(["id"=>$id]);
                        }catch(Exception $e){
                            DB::rollback();
                            return false;
                        }
                    }
                    else{
                        return false;
                    }
                }catch(Exception $e){
                    return $e->getMessage();
                }
            }
            elseif($request->method() == 'POST'){
                DB::beginTransaction();
                if(DB::table('vendor_products')->where('vendor_id',$request->get('vendorid'))->where('warehouse_id',$request->get('warehouseid'))->where('product_id',$request->get('productid'))->where('product_brand',$request->get('brandid'))->exists()){
                    return json_encode(['status'=>'exists','statuscode'=>'200','description'=>'Record already exists!']);
                }else{
                    $vendor = DB::table('vendor_details')->where('id',$request->get('vendorid'))->first();
                    // $batchid = substr($vendor->of_city,0,1).$vendor->vendor_code.DB::table('products')->where('id',$request->get('productid'))->first()->product_code.date('my');
                    // VendorProducts::where('id',$vendor_products_id)->update(['batch'=>$batch_id]);
                    $insertid = DB::table('vendor_products')->insertGetId(
                        [
                            'vendor_id'=>$request->get('vendorid'),
                            'product_id'=>$request->get('productid'),
                            'product_quantity'=>1,
                            'product_brand'=>$request->get('brandid'),
                            'product_rent_approved'=>0,
                            'product_rent_requested'=>0,
                            'product_deposite'=>0,
                            'warehouse_id'=>$request->get('warehouseid'),
                            'status'=>'Approved',
                            'batch'=>null,
                        ]
                    );
                    $batchid = "".$insertid."-".$request->get('productid')."-".$request->get('vendorid')."-".$request->get('brandid');
                    DB::table('vendor_products')->where('id',$insertid)->update(['batch'=>$batchid]);
                    // $batchid = $batchid."0";
                    for($i=0;$i<10;$i++){
                        $insertedid = DB::table('vendor_product_details')->insertGetId(
                            [
                                'vendor_products_id'=>$insertid,
                                'availability_status'=>0,
                                'inventory_id'=>$batchid."-".($i+1),
                                'inventory_type'=>0,
                                'current_location'=>2,
                                'warehouse_id'=>$request->get('warehouseid'),
                                'additional_dateils'=>'Initial Product',
                                'created_by'=>session('username')
                            ]
                        );
                    }
                    DB::commit();
                    return json_encode(['status'=>'success','statuscode'=>'200','description'=>'Record inserted!','data'=>$insertid]);
                }
                return json_encode($request->all());
            }
            else{
                return false;
            }
        } 
        public function getOrdersCount(){
            $today = date('d-m-Y');
            $orders = DB::table('del_orders')->select('del_orders.order_id','del_orders.deliverypickup')->where(DB::raw("STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y')"),DB::raw("STR_TO_DATE('$today','%d-%m-%Y')"))->whereNotIn('status',['Cancel'])->get();
            $data['total'] = $orders->count();
            $data['Delivery'] = $orders->where('deliverypickup','Delivery')->count();
            $data['Pick Up'] = $orders->where('deliverypickup','Pick Up')->count();
            $data['Collection'] = $orders->where('deliverypickup','Collection')->count();
            return $data;
        }

    }
?>