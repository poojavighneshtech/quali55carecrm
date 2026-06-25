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
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    use App\Exports\OrderDetailsReportExport;
    use App\Exports\VendorLiveInventoryExport;
    use Mail;
    use App\Exports\OrdersExport;
    use Maatwebsite\Excel\Facades\Excel;
    use Carbon\Carbon;
    use App\Http\Controllers\RenewalPickup\RenewalPickupController;
    use Illuminate\Support\Collection;

    class TestController extends Controller
    {
        public function isLoggedIn()
        {
            $data = session('isLoggedIn');
            return $data;
        }
        public function vendor_product_details(Request $request)
        {
            $products = MasterProduct::where('flag','Active')->get();

            $whereClause = array();
            $whereInClause = "";
            // $get_min_date = OrderDetails::min('creation_date');
            // $get_max_date = OrderDetails::max('creation_date');
            // $filter_data['from_date'] =$get_min_date;
            // $filter_data['end_date'] = $get_max_date;
            $filter_data['from_date'] =null;
            $filter_data['end_date'] = null;
            $filter_data['cust_name'] = null;
            $filter_data['cust_no'] = null;
            $filter_data['prod_name'] = null;
            $filter_data['vdr_name'] = null;
            $filter_data['order_type'] = null;
            $filter_data['patient_name'] = null;
            $filter_data['equip_type'] = null;
            // dd($get_min_date, $get_max_date);
            $customer_name = $request->get('cust_name');
            if(isset($customer_name)){
                $whereCondition1 = ['customer_details.customer_name','LIKE','%'.$customer_name.'%'];
                array_push($whereClause,$whereCondition1);
                $filter_data['cust_name'] = $customer_name;
            }

            $customer_contact = $request->get('cust_no');
            if(isset($customer_contact)) {
                $whereCondition2 = ['customer_details.primary_contact_no','=',$customer_contact];
                array_push($whereClause,$whereCondition2);
                $filter_data['cust_no'] = $customer_contact;
            }
            $dateArr = [];
            $from_date = $request->get('from_date');
            $end_date = $request->get('end_date');
            if(isset($from_date) && isset($end_date)){
                array_push($dateArr,$from_date);
                array_push($dateArr,$end_date);
                $filter_data['from_date'] = $from_date;
                $filter_data['end_date'] = $end_date;
            }

            $vendor_name = $request->get('vdr_name');
            if(isset($vendor_name)){
                $whereCondition1 = ['vendor_details.registered_name','LIKE','%'.$vendor_name.'%'];
                array_push($whereClause,$whereCondition1);
                $filter_data['vdr_name'] = $vendor_name;
            }

            $patient_name = $request->get('patient_name');
            if(isset($patient_name)){
                $whereCondition2 = ['leads.patient_name','LIKE','%'.$patient_name.'%'];
                array_push($whereClause,$whereCondition2);
                $filter_data['patient_name'] = $patient_name;
            }

            $product_id = $request->get('prod_name');
            // dd($product_id);
            if(isset($product_id)) {
                // $whereInClause = "'order_details.product_id',$product_id";
                // dd('ac');
                // $whereCondition2 = ['customer_details.primary_contact_no','=',$customer_contact];
                // array_push($whereClause,$whereCondition2);
                $filter_data['prod_name'] = $product_id;
            }
            else
            {
                $product_id = array();
                $dist_product_id = DB::table('order_details')->distinct('product_id')->select('product_id')->get()->toArray();
                // $dist_product_id = json_decode(json_encode($dist_product_id), true);
                foreach ($dist_product_id as $key => $value)
                {
                    array_push($product_id,$value->product_id);
                }
                // $product_id = 
                // $whereInClause = ['order_details.status',[$product_id]];
            }

            $order_type = $request->get('order_type');
            if(isset($order_type)){
                if($order_type == 'Pick Up')
                {
                    $status = ['Pending Renew','Renewed','Pending','Renewed Online','Cancel'];
                    $filter_data['order_type'] = 'Pick Up';
                }
                elseif($order_type == 'Delivery')
                {
                    $status = ['Pending Pickup','Picked Up' ,'Picked UP','CustStop' ,'Pickuped','Cancel'];
                    $filter_data['order_type'] = 'Delivery';
                }
                else
                {
                    $status = ['Cancel'];
                    $filter_data['order_type'] = 'All';
                }
            }
            else
            {
                $status = ['null',null];
            }
            $equip_type = $request->get('equip_type');
            if(isset($equip_type)){
                if($equip_type == 'Live')
                {
                    $filter_data['equip_type'] = 'Live';
                }
                elseif($equip_type == 'Sold')
                {
                    $filter_data['equip_type'] = 'Sold';
                }
                else
                {                    
                    $filter_data['equip_type'] = 'All';
                }
            }
            $city = $request->get('city');
            if(isset($city)){
                $filter_data['city'] = $city;
            }else{
                $filter_data['city'] = "All";
            }
            $product_type = $request->get('product_type');
            if(isset($product_type)){
                if($product_type == 'Rental')
                {
                    $product_type = 'Rental';
                    $filter_data['product_type'] = 'Rental';
                }
                elseif($product_type == 'Sale')
                {
                    $product_type = 'Sale';
                    $filter_data['product_type'] = 'Sale';
                }
                else
                {
                    $product_type = 'All';
                    $filter_data['product_type'] = 'All';
                }
            }
            else
            {
                $product_type = 'All';
            }
            $orderTypeNotIn = config('app.order_type');
            // $order_details = OrderDetails::whereNotIn('status',['Pending Pickup','Picked up'])
            $order_details = DB::table('order_details')
                                ->join('vendor_details','order_details.vendor_id','=','vendor_details.id')
                                ->join('products','order_details.product_id','=','products.id')
                                ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                                ->join('vendor_products','order_details.vendor_product_id','=','vendor_products.id')
                                ->join('del_orders','order_details.order_id','=','del_orders.order_id')
                                ->join('leads','del_orders.lead_id','=','leads.id')
                                ->select('order_details.*',
                                'del_orders.patient_name',
                                'del_orders.fulldetails',
                                'del_orders.product_delivered',
                                'vendor_details.registered_name as vendor_name',
                                'customer_details.customer_name as customer_name',
                                'customer_details.primary_contact_no as primary_contact_no',
                                'products.product_name as product_name',
                                //'vendor_products.product_rent_approved as vendor_rent',
                                'customer_details.citygroup as city',
                                'customer_details.cust_id as customer_id',
                                'leads.patient_name','leads.lead_source','leads.leadtype')
                                // ->where('order_details.sale_rental','Rental')
                                ->whereNotIn('order_details.current_status',$status)
                                ->whereIn('order_details.product_id',$product_id)
                                ->where($whereClause)
                                ->when($request->get('equip_type'), function($query)use($request){
                                    if($request->get('equip_type') == 'Live'){
                                        $query->whereNotIn('order_details.current_status',['Pending Pickup','Picked Up' ,'Picked UP','Pickuped','Cancel']);
                                        $query->where('order_details.sale_rental','Rental');
                                    }
                                    elseif($request->get('equip_type') == 'Sold'){
                                        $query->whereNotIn('order_details.current_status',['Pending Pickup','Picked Up' ,'Picked UP','Pickuped','Cancel']);
                                        $query->where('order_details.sale_rental','Sale');
                                    }
                                })
                                ->when($product_type,function($query,$product_type){
                                    if($product_type != 'All')
                                    {
                                        $query->where('order_details.sale_rental',$product_type);
                                    }
                                })
                                //->whereBetween('order_details.creation_date',[$get_min_date,$get_max_date])
                                ->when($dateArr,function($query,$dateArr){
                                    $query->whereBetween('order_details.creation_date',$dateArr);
                                })
                                ->when($request->get('city'),function($query)use($request){
                                    if($request->get('city') != 'All'){
                                        $query->where('customer_details.citygroup',$request->get('city'));
                                    }
                                })
                                ->when(session('city_based_access') == '1',function($query){
                                    $query->where('customer_details.citygroup',session('user_city'));
                                })
                                ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                                ->get();

            if($request->get('btn_submit') == 'submit')
            {
                $order_details = $order_details->paginate(10);
            }
            elseif($request->get('btn_submit') == 'export_excel')
            {
                $order_details = $order_details->paginate(10000000000);
            }
            else
            {
                $order_details = $order_details->paginate(10);
            }
            foreach($order_details as $key=>$temp)
            {
                $order_details[$key]->pickup_date_stopped = "-";
                $order_details[$key]->days_count = "-";
                $order_details[$key]->months_count = 0;
                $days_count = 0;
                if($temp->current_status == 'Pending Pickup' || $temp->current_status == 'Picked Up' || $temp->current_status == 'Picked UP' || $temp->current_status == 'Pickuped')
                {
                    // dd("");
                    if(DB::table('pickups')->select('pickup_date')->where('order_details_id',$temp->id)->exists()){
                        $order_details[$key]->pickup_date_stopped = DB::table('pickups')->select('pickup_date')->where('order_details_id',$temp->id)->first()->pickup_date;
                        $days_count = Carbon::parse($temp->creation_date)->diffInDays($order_details[$key]->pickup_date_stopped);
                        //$days_count = 0;
                        $order_details[$key]->days_count = $days_count;

                        $order_details[$key]->months_count = Carbon::parse($temp->creation_date)->DiffInMonths($order_details[$key]->pickup_date_stopped);
                    }else{
                        $order_details[$key]->pickup_date_stopped = "-";
                        $order_details[$key]->days_count = "-";
                       // $order_details[$key]->months_count = 0;
                    }
                }
                else
                {
                    $days_count = Carbon::parse($temp->creation_date)->diffInDays(Carbon::now()->toDateString());
                    //$days_count = 0;
                    $order_details[$key]->days_count = $days_count;
                   // $order_details[$key]->months_count = Carbon::parse($temp->creation_date)->DiffInMonths(Carbon::now()->toDateString());
                }
                if ($days_count <=34) {
                    $order_details[$key]->months_count = 0;
                } elseif ($days_count >=35 && $days_count <=64) {
                    $order_details[$key]->months_count = 1;
                } elseif ($days_count >=65 && $days_count <=96) {
                    $order_details[$key]->months_count = 2;
                } elseif ($days_count >=97 && $days_count <=125) {
                    $order_details[$key]->months_count = 3;
                } elseif ($days_count >=126 && $days_count <=156) {
                    $order_details[$key]->months_count = 4;
                } elseif ($days_count >=157 && $days_count <=186) {
                    $order_details[$key]->months_count = 5;
                } elseif ($days_count >=187 && $days_count <=215) {
                    $order_details[$key]->months_count = 6;
                } elseif ($days_count >=216 && $days_count <=246) {
                    $order_details[$key]->months_count = 7;
                } elseif ($days_count >=247 && $days_count <=275) {
                    $order_details[$key]->months_count = 8;
                } elseif ($days_count >=276 && $days_count <=305) {
                    $order_details[$key]->months_count = 9;
                } elseif ($days_count >=306 && $days_count <=335) {
                    $order_details[$key]->months_count = 10;
                } elseif ($days_count >=336 && $days_count <=366) {
                    $order_details[$key]->months_count = 11;
                } elseif ($days_count >=367 && $days_count <=395) {
                    $order_details[$key]->months_count = 12;
                } else {
                    $order_details[$key]->months_count = Carbon::parse($temp->creation_date)->DiffInMonths(Carbon::now()->toDateString());
                }

                // if ($order_details[$key]->months_count >= -1) {
                //     $order_details[$key]->months_count = 0;
                // }
                if ($order_details[$key]->lead_source == 'Corporate Booking')
                {
                    if ($order_details[$key]->sale_rental=='Rental')
                        $order_details[$key]->leadtype = 'RC';
                    else {
                        $order_details[$key]->leadtype = 'SC';
                    }
                } else {
                    if ($order_details[$key]->sale_rental=='Rental')
                        $order_details[$key]->leadtype = 'RR';
                    else {
                        $order_details[$key]->leadtype = 'SR';
                    }
                }
            }
            $orderTypeNotIn = config('app.order_type');
            $order_details_all = DB::table('order_details')
                                ->join('vendor_details','order_details.vendor_id','=','vendor_details.id')
                                ->join('products','order_details.product_id','=','products.id')
                                ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                                ->join('vendor_products','order_details.vendor_product_id','=','vendor_products.id')
                                ->join('del_orders','order_details.order_id','=','del_orders.order_id')
                                ->join('leads','del_orders.lead_id','=','leads.id')
                                ->select('order_details.*',
                                'del_orders.patient_name',
                                'del_orders.fulldetails',
                                'vendor_details.registered_name as vendor_name',
                                'customer_details.customer_name as customer_name',
                                'customer_details.primary_contact_no as primary_contact_no',
                                'products.product_name as product_name',
                               // 'vendor_products.product_rent_approved as vendor_rent',
                               // 'order_details.vendor_rent',
                                'customer_details.cust_id as customer_id',
                                'leads.patient_name')
                                // ->where('order_details.sale_rental','Rental')
                                ->when($product_type,function($query,$product_type){
                                    if($product_type != 'All')
                                    {
                                        $query->where('order_details.sale_rental',$product_type);
                                    }
                                })
                                ->whereNotIn('order_details.current_status',$status)
                                ->whereIn('order_details.product_id',$product_id)
                                ->where($whereClause)
                                //->whereBetween('order_details.creation_date',[$get_min_date,$get_max_date])
                                ->when($dateArr,function($query,$dateArr){
                                    $query->whereBetween('order_details.creation_date',$dateArr);
                                })
                                ->when($request->get('equip_type'), function($query)use($request){
                                    if($request->get('equip_type') == 'Live'){
                                        $query->whereNotIn('order_details.current_status',['Pending Pickup','Picked Up' ,'Picked UP','Pickuped','Cancel']);
                                        $query->where('order_details.sale_rental','Rental');
                                    }
                                    elseif($request->get('equip_type') == 'Sold'){
                                        $query->whereNotIn('order_details.current_status',['Pending Pickup','Picked Up' ,'Picked UP','Pickuped','Cancel']);
                                        $query->where('order_details.sale_rental','Sale');
                                    }
                                })
                                ->when($request->get('city'),function($query)use($request){
                                    if($request->get('city') != 'All'){
                                        $query->where('customer_details.citygroup',$request->get('city'));
                                    }
                                })
                                ->when(session('city_based_access') == '1',function($query){
                                    $query->where('customer_details.citygroup',session('user_city'));
                                })
                                ->whereNotIn('del_orders.deliverypickup',$orderTypeNotIn)
                                ->get()
                                ->toArray();

            // dd($order_details_all);
            $order_details_all = json_decode(json_encode($order_details_all),true);
            $orders = array();
            $total_vendor_rent = 0;
            $total_order_rent = 0;
            $total_order_sale = 0;
            $equipment_count = 0;
            $total_order_deposite = 0;
            $total_order_transport = 0;
            foreach($order_details_all as $key => $order_detail)
            {
                if(DB::table('cr_dr_note')->where('order_details_id',$order_detail['id'])->exists())
                {
                    $order_details_all[$key]['product_rent'] = $this->fetchCrDrData($order_detail['id'],'R');
                    $order_details_all[$key]['product_deposite'] = $this->fetchCrDrData($order_detail['id'],'D');
                    $order_details_all[$key]['transport'] = $this->fetchCrDrData($order_detail['id'],'T');
                }
                
                $total_vendor_rent = $total_vendor_rent + ($order_detail['vendor_rent'] * $order_detail['product_qty']);

                if($order_detail['sale_rental'] == 'Rental')
                {
                    $total_order_rent = $total_order_rent + $order_detail['product_rent'];
                }
                else if($order_detail['sale_rental'] == 'Sale')
                {
                    $total_order_sale = $total_order_sale + $order_detail['product_rent'];
                }
                $total_order_deposite = $total_order_deposite + $order_detail['product_deposite'];
                $total_order_transport = $total_order_transport + $order_detail['transport'];
                $equipment_count = $equipment_count + $order_detail['product_qty'];
                array_push($orders,$order_detail['order_id']);
            }
            //dd($total_vendor_rent);
            $orders = array_unique($orders);
            $orders_count = count($orders);
            $count['orders_count'] = $orders_count;
            $count['vendor_rent_count'] = $total_vendor_rent;
            $count['order_sale_count'] = $total_order_sale;
            $count['order_rent_count'] = $total_order_rent;
            $count['order_deposite_count'] = $total_order_deposite;
            $count['order_transport_count'] = $total_order_transport;
            $count['equipment_count'] = $equipment_count;
            if($request->get('btn_submit') == 'export_excel')
            {                
                ob_end_clean(); // this
                ob_start(); // and this
                return Excel::download(new OrderDetailsReportExport($products,$order_details,$filter_data,$count), 'Product Report'.date('Y-m-d H:i:s').'.xlsx');
            }
            return view('OrderManagement.vendor_product_details',compact('products','order_details','filter_data','count'));
        }

        public function vendor_live_inventory(Request $request)
        {
            if($request->method() == "GET")
            {
                $products = MasterProduct::where('flag','Active')->get();
                $vendor_rent_flag = $request->get('vendor_rent_flag');
                
                $dateArr = [];
                $from_date = $request->get('from_date');
                $end_date = $request->get('end_date');
                if(isset($from_date) && isset($end_date)){
                    array_push($dateArr,$from_date);
                    array_push($dateArr,$end_date);
                }
    
                $order_details = DB::table('order_details')
                                    ->join('del_orders','del_orders.order_id','=','order_details.order_id')
                                    ->join('vendor_details','order_details.vendor_id','=','vendor_details.id')
                                    ->join('products','order_details.product_id','=','products.id')
                                    ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                                    // ->join('vendor_products','order_details.vendor_product_id','=','vendor_products.id')
                                    ->select('order_details.*',
                                    'vendor_details.registered_name as vendor_name',
                                    'customer_details.customer_name as customer_name',
                                    'customer_details.primary_contact_no as primary_contact_no',
                                    'products.product_name as product_name')
                                    // 'vendor_products.product_rent_approved as vendor_rent')
                                    ->where('order_details.sale_rental','Rental')
                                   // ->whereNotIn('order_details.current_status',['Pending Pickup','Picked Up' ,'Picked UP','Pickuped','Cancel'])
                                   ->whereNotIn('order_details.current_status',['Cancel'])
                                    ->when($request->get('cust_name'),function($query)use($request){
                                        $query->where('customer_details.customer_name','LIKE','%'.$request->get('cust_name').'%');
                                    })
                                    ->when($request->get('cust_no'),function($query)use($request){
                                        $query->where('customer_details.primary_contact_no',$request->get('cust_no'));
                                    })
                                    ->when($request->get('prod_name'),function($query)use($request){
                                        $query->whereIn('order_details.product_id',$request->get('prod_name'));
                                    })
                                    ->when($dateArr,function($query,$dateArr){
                                        $query->whereBetween('order_details.creation_date',$dateArr);
                                    })
                                    ->when($request->get('vdr_name'),function($query)use($request){
                                        $query->where('vendor_details.registered_name','LIKE','%'.$request->get('vdr_name').'%');
                                    })
                                    ->when(session('city_based_access') == '1',function($query){
                                        $query->where('customer_details.citygroup',session('user_city'));
                                    })
                                    ->when($vendor_rent_flag == '0',function($query){
                                        $query->whereNull('order_details.vendor_rent');
                                    })
                                    ->when($vendor_rent_flag == '1',function($query){
                                        $query->whereNotNull('order_details.vendor_rent');
                                    })
                                    ->orderBy('del_orders.order_id','Desc')
                                    ->get();
                
                if($request->get("btn_submit") == 'export'){

                    foreach ($order_details as $key => $value) {
                        $order_details[$key]->product_rent = RenewalPickupController::fetchCrDrData($value->id,'R');
                    }
                    ob_end_clean(); // this
                    ob_start(); // and this
                    return Excel::download(new VendorLiveInventoryExport($products, $order_details), 'Live Inventory'.date('Y-m-d H:i:s').'.xlsx');
                }else{
                    foreach ($order_details as $key => $value) {
                        $order_details[$key]->product_rent = RenewalPickupController::fetchCrDrData($value->id,'R');
                    }
                    $order_details = $order_details->paginate(10);
                    return view('VendorInventoryManagement.vendor-live-inventory',compact('products','order_details'));
                }
            }
            else if($request->method() == "POST")
            {
                // dd($request->all());
                $vendor_rents = $request->get('vendor_rent');
                $inventoryIds = $request->get('inventory_id');
                foreach($request->get('order_details_id') as $key=>$value)
                {
                    DB::table('order_details')->where('id',$value)->update(['vendor_rent'=>$vendor_rents[$key],"unique_id"=>$inventoryIds[$key]]);
                }
                return redirect()->back()->with('message','Rent Updated Successfully');
            }
        }

        public function order_details_count(Request $request)
        {
            // $products = MasterProduct::where('flag','Active')->get();

            $whereClause = array();
            $whereInClause = "";
            $get_min_date = OrderDetails::min('creation_date');
            $get_max_date = OrderDetails::max('creation_date');
            $filter_data['from_date'] = $get_min_date;
            $filter_data['end_date'] = $get_max_date;
            $filter_data['cust_name'] = null;
            $filter_data['cust_no'] = null;
            $filter_data['prod_name'] = null;
            $filter_data['vdr_name'] = null;
            $filter_data['order_type'] = null;
            $status = null;
            // dd($get_min_date, $get_max_date);
            $customer_name = $request->get('cust_name');
            if(isset($customer_name)){
                $whereCondition1 = ['customer_details.customer_name','LIKE','%'.$customer_name.'%'];
                array_push($whereClause,$whereCondition1);
                $filter_data['cust_name'] = $customer_name;
            }

            $customer_contact = $request->get('cust_no');
            if(isset($customer_contact)) {
                $whereCondition2 = ['customer_details.primary_contact_no','=',$customer_contact];
                array_push($whereClause,$whereCondition2);
                $filter_data['cust_no'] = $customer_contact;
            }

            $from_date = $request->get('from_date');
            $end_date = $request->get('end_date');
            if(isset($from_date) && isset($end_date)){
                $get_min_date = $from_date;
                $get_max_date = $end_date;
                
                $filter_data['from_date'] = $get_min_date;
                $filter_data['end_date'] = $get_max_date;
            }

            $vendor_name = $request->get('vdr_name');
            if(isset($vendor_name)){
                $whereCondition1 = ['vendor_details.registered_name','LIKE','%'.$vendor_name.'%'];
                array_push($whereClause,$whereCondition1);
                $filter_data['vdr_name'] = $vendor_name;
            }

            $product_id = $request->get('prod_name');
            
            if(isset($product_id)) {
                // $whereInClause = "'order_details.product_id',$product_id";
                // dd('ac');
                // $whereCondition2 = ['customer_details.primary_contact_no','=',$customer_contact];
                // array_push($whereClause,$whereCondition2);
                $filter_data['prod_name'] = $product_id;
            }
            else
            {
                $product_id = array();
                $dist_product_id = DB::table('order_details')->distinct('product_id')->select('product_id')->get()->toArray();
                // $dist_product_id = json_decode(json_encode($dist_product_id), true);
                foreach ($dist_product_id as $key => $value)
                {
                    array_push($product_id,$value->product_id);
                }
                // $product_id = 
                // $whereInClause = ['order_details.status',[$product_id]];
            }
            $order_type = $request->get('order_type');
            if(isset($order_type)){
                if($order_type == 'Pick Up')
                {
                    $status = ['Pending Renew','Renewed','Pending','Renewed Online'];
                    $filter_data['order_type'] = 'Pick Up';
                }
                elseif($order_type == 'Delivery')
                {
                    $status = ['Pending Pickup','Picked Up', 'Picked UP','Pickuped'];
                    $filter_data['order_type'] = 'Delivery';
                }
                else
                {
                    $status = ['null','null'];
                    $filter_data['order_type'] = 'All';
                }
            }
            else
            {
                $status = ['null','null'];
            }
            // dd($status);
            // $order_details = OrderDetails::whereNotIn('status',['Pending Pickup','Picked up'])
            $return_data = array();
            $order_details = DB::table('order_details')
                                ->join('vendor_details','order_details.vendor_id','=','vendor_details.id')
                                ->join('products','order_details.product_id','=','products.id')
                                ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                                ->join('vendor_products','order_details.vendor_product_id','=','vendor_products.id')
                                ->select('order_details.*',
                                'vendor_details.registered_name as vendor_name',
                                'customer_details.customer_name as customer_name',
                                'customer_details.primary_contact_no as primary_contact_no',
                                'products.product_name as product_name',
                                'vendor_products.product_rent_approved as vendor_rent')
                                ->where('order_details.sale_rental','Rental')
                                ->whereNotIn('order_details.current_status',$status)
                                ->whereIn('order_details.product_id',$product_id)
                                ->where($whereClause)
                                ->whereBetween('order_details.creation_date',[$get_min_date,$get_max_date])
                                ->get()
                                ->toArray();
            $order_details = json_decode(json_encode($order_details), true);
            if($request->get('section') == "vendor_rent_count")
            {
                // echo "abc";
                $vendor_rent_count = array();
                $temp_vendor = array();
                foreach ($order_details as $key => $order_detail)
                {
                    if(in_array($order_detail['vendor_id'],$temp_vendor))
                    {
                        $key = array_search ($order_detail['vendor_id'], $temp_vendor);
                        // $vendor_rent_count[$key]['vendor_name'] = $order_detail['vendor_name'];
                        if($order_detail['current_status'] == 'Pending Pickup' || $order_detail['current_status'] == 'Picked Up' || $order_detail['current_status'] == 'Picked UP' || $order_detail['current_status'] == 'Pickuped')
                        {
                            $vendor_rent_count[$key]['product_stop_qty'] = $vendor_rent_count[$key]['product_stop_qty'] + $order_detail['product_qty'];
                            $vendor_rent_count[$key]['total_stop_rent'] = $vendor_rent_count[$key]['total_stop_rent'] + ($order_detail['vendor_rent'] * $order_detail['product_qty']);
                        }
                        else
                        {
                            $vendor_rent_count[$key]['product_live_qty'] = $vendor_rent_count[$key]['product_live_qty'] + $order_detail['product_qty'];
                            $vendor_rent_count[$key]['total_live_rent'] = $vendor_rent_count[$key]['total_live_rent'] + ($order_detail['vendor_rent'] * $order_detail['product_qty']);
                        }
                    }
                    else
                    {
                        $key = count($vendor_rent_count);
                        $vendor_rent_count[$key]['vendor_name'] = $order_detail['vendor_name'];
                        // $vendor_rent_count[$key]['product_qty'] = $order_detail['product_qty']; 
                        if($order_detail['current_status'] == 'Pending Pickup' || $order_detail['current_status'] == 'Picked Up' || $order_detail['current_status'] == 'Picked UP' || $order_detail['current_status'] == 'Pickuped')
                        {
                            $vendor_rent_count[$key]['product_stop_qty'] = $order_detail['product_qty'];
                            $vendor_rent_count[$key]['total_stop_rent'] = $order_detail['vendor_rent'] * $order_detail['product_qty'];
                            $vendor_rent_count[$key]['product_live_qty'] = 0;
                            $vendor_rent_count[$key]['total_live_rent'] = 0;
                        }
                        else
                        {
                            $vendor_rent_count[$key]['product_stop_qty'] = 0;
                            $vendor_rent_count[$key]['total_stop_rent'] = 0;
                            $vendor_rent_count[$key]['product_live_qty'] = $order_detail['product_qty'];
                            $vendor_rent_count[$key]['total_live_rent'] = $order_detail['vendor_rent'] * $order_detail['product_qty'];
                        }
                        array_push($temp_vendor,$order_detail['vendor_id']);
                    }
                }
                // dd($vendor_rent_count);
                return $vendor_rent_count;
            }
            else if($request->get('section') == 'order_rent_count')
            {
                $order_rent_count = array();
                $temp_order_count = array();
                foreach ($order_details as $key => $order_detail)
                {
                    if($order_detail['sale_rental'] == 'Rental')
                    {
                        if(in_array($order_detail['product_id'],$temp_order_count))
                        {
                            $key = array_search ($order_detail['product_id'], $temp_order_count);
                            // $vendor_rent_count[$key]['vendor_name'] = $order_detail['vendor_name'];
                            if($order_detail['current_status'] == 'Pending Pickup' || $order_detail['current_status'] == 'Picked Up' || $order_detail['current_status'] == 'Picked UP' || $order_detail['current_status'] == 'Pickuped')
                            {
                                $order_rent_count[$key]['product_stop_qty'] = $order_rent_count[$key]['product_stop_qty'] + $order_detail['product_qty'];
                                $order_rent_count[$key]['total_stop_rent'] = $order_rent_count[$key]['total_stop_rent'] + ($order_detail['product_rent'] * $order_detail['product_qty']);
                            }
                            else
                            {
                                $order_rent_count[$key]['product_live_qty'] = $order_rent_count[$key]['product_live_qty'] + $order_detail['product_qty'];
                                $order_rent_count[$key]['total_live_rent'] = $order_rent_count[$key]['total_live_rent'] + ($order_detail['product_rent'] * $order_detail['product_qty']);
                            }
                        }
                        else
                        {
                            $key = count($order_rent_count);
                            $order_rent_count[$key]['product_name'] = $order_detail['product_name'];
                            // $order_rent_count[$key]['product_qty'] = $order_detail['product_qty'];
                            // $order_rent_count[$key]['total_rent'] = ($order_detail['product_rent'] * $order_detail['product_qty']);
                            if($order_detail['current_status'] == 'Pending Pickup' || $order_detail['current_status'] == 'Picked Up' || $order_detail['current_status'] == 'Picked UP' || $order_detail['current_status'] == 'Pickuped')
                            {
                                $order_rent_count[$key]['product_stop_qty'] = $order_detail['product_qty'];
                                $order_rent_count[$key]['total_stop_rent'] = $order_detail['product_rent'] * $order_detail['product_qty'];
                                $order_rent_count[$key]['product_live_qty'] = 0;
                                $order_rent_count[$key]['total_live_rent'] = 0;
                            }
                            else
                            {
                                $order_rent_count[$key]['product_live_qty'] = $order_detail['product_qty'];
                                $order_rent_count[$key]['total_live_rent'] = $order_detail['product_rent'] * $order_detail['product_qty'];
                                $order_rent_count[$key]['product_stop_qty'] = 0;
                                $order_rent_count[$key]['total_stop_rent'] = 0;
                            }
                            array_push($temp_order_count,$order_detail['product_id']);
                        }   
                    }
                }
                // dd($vendor_rent_count);
                return $order_rent_count;
            }
            else if($request->get('section') == 'order_sale_count')
            {
                $order_rent_count = array();
                $temp_order_count = array();
                foreach ($order_details as $key => $order_detail)
                {
                    if($order_detail['sale_rental'] == 'Sale')
                    {
                        if(in_array($order_detail['product_id'],$temp_order_count))
                        {
                            $key = array_search ($order_detail['product_id'], $temp_order_count);
                            // $vendor_rent_count[$key]['vendor_name'] = $order_detail['vendor_name'];
                            if($order_detail['current_status'] == 'Pending Pickup' || $order_detail['current_status'] == 'Picked Up' || $order_detail['current_status'] == 'Picked UP' || $order_detail['current_status'] == 'Pickuped')
                            {
                                $order_rent_count[$key]['product_stop_qty'] = $order_rent_count[$key]['product_stop_qty'] + $order_detail['product_qty'];
                                $order_rent_count[$key]['total_stop_rent'] = $order_rent_count[$key]['total_stop_rent'] + ($order_detail['product_rent'] * $order_detail['product_qty']);
                            }
                            else
                            {
                                $order_rent_count[$key]['product_live_qty'] = $order_rent_count[$key]['product_live_qty'] + $order_detail['product_qty'];
                                $order_rent_count[$key]['total_live_rent'] = $order_rent_count[$key]['total_live_rent'] + ($order_detail['product_rent'] * $order_detail['product_qty']);
                            }
                        }
                        else
                        {
                            $key = count($order_rent_count);
                            $order_rent_count[$key]['product_name'] = $order_detail['product_name'];
                            // $order_rent_count[$key]['product_qty'] = $order_detail['product_qty'];
                            // $order_rent_count[$key]['total_rent'] = ($order_detail['product_rent'] * $order_detail['product_qty']);
                            if($order_detail['current_status'] == 'Pending Pickup' || $order_detail['current_status'] == 'Picked Up' || $order_detail['current_status'] == 'Picked UP' || $order_detail['current_status'] == 'Pickuped')
                            {
                                $order_rent_count[$key]['product_stop_qty'] = $order_detail['product_qty'];
                                $order_rent_count[$key]['total_stop_rent'] = $order_detail['product_rent'] * $order_detail['product_qty'];
                                $order_rent_count[$key]['product_live_qty'] = 0;
                                $order_rent_count[$key]['total_live_rent'] = 0;
                            }
                            else
                            {
                                $order_rent_count[$key]['product_live_qty'] = $order_detail['product_qty'];
                                $order_rent_count[$key]['total_live_rent'] = $order_detail['product_rent'] * $order_detail['product_qty'];
                                $order_rent_count[$key]['product_stop_qty'] = 0;
                                $order_rent_count[$key]['total_stop_rent'] = 0;
                            }
                            array_push($temp_order_count,$order_detail['product_id']);
                        }   
                    }
                }
                // dd($vendor_rent_count);
                return $order_rent_count;
            }
            else if($request->get('section') == 'order_deposite_count')
            {
                $order_deposite_count = array();
                $temp_order_count = array();
                foreach ($order_details as $key => $order_detail)
                {
                    if(in_array($order_detail['product_id'],$temp_order_count))
                    {
                        $key = array_search ($order_detail['product_id'], $temp_order_count);
                        // $vendor_rent_count[$key]['vendor_name'] = $order_detail['vendor_name'];
                        // $order_deposite_count[$key]['product_qty'] = $order_deposite_count[$key]['product_qty'] + $order_detail['product_qty'];
                        // $order_deposite_count[$key]['total_deposite'] = $order_deposite_count[$key]['total_deposite'] + $order_detail['product_deposite'];
                        if($order_detail['current_status'] == 'Pending Pickup' || $order_detail['current_status'] == 'Picked Up' || $order_detail['current_status'] == 'Picked UP' || $order_detail['current_status'] == 'Pickuped')
                        {
                            $order_deposite_count[$key]['product_stop_qty'] = $order_deposite_count[$key]['product_stop_qty'] + $order_detail['product_qty'];
                            $order_deposite_count[$key]['total_stop_deposite'] = $order_deposite_count[$key]['total_stop_deposite'] + $order_detail['product_deposite'];
                        }
                        else
                        {
                            $order_deposite_count[$key]['product_live_qty'] = $order_deposite_count[$key]['product_live_qty'] + $order_detail['product_qty'];
                            $order_deposite_count[$key]['total_live_deposite'] = $order_deposite_count[$key]['total_live_deposite'] + $order_detail['product_deposite'];
                        }
                    }
                    else
                    {
                        $key = count($order_deposite_count);
                        $order_deposite_count[$key]['product_name'] = $order_detail['product_name'];
                        // $order_deposite_count[$key]['product_qty'] = $order_detail['product_qty'];
                        // $order_deposite_count[$key]['total_stop_deposite'] = $order_detail['product_deposite'];
                        // $order_deposite_count[$key]['total_live_deposite'] = $order_detail['product_deposite'];
                        if($order_detail['current_status'] == 'Pending Pickup' || $order_detail['current_status'] == 'Picked Up' || $order_detail['current_status'] == 'Picked UP' || $order_detail['current_status'] == 'Pickuped')
                        {
                            $order_deposite_count[$key]['product_stop_qty'] = $order_detail['product_qty'];
                            $order_deposite_count[$key]['total_stop_deposite'] = $order_detail['product_deposite'];
                            $order_deposite_count[$key]['product_live_qty'] = 0;
                            $order_deposite_count[$key]['total_live_deposite'] = 0;
                        }
                        else
                        {
                            $order_deposite_count[$key]['product_stop_qty'] = 0;
                            $order_deposite_count[$key]['total_stop_deposite'] = 0;
                            $order_deposite_count[$key]['product_live_qty'] = $order_detail['product_qty'];
                            $order_deposite_count[$key]['total_live_deposite'] = $order_detail['product_deposite'];
                        }
                        array_push($temp_order_count,$order_detail['product_id']);
                    }
                }
                // dd($vendor_rent_count);
                return $order_deposite_count;
            }
            else if($request->get('section') == 'equipment_count')
            {
                $order_equipment_count = array();
                $temp_order_count = array();
                foreach ($order_details as $key => $order_detail)
                {
                    if(in_array($order_detail['product_id'],$temp_order_count))
                    {
                        $key = array_search ($order_detail['product_id'], $temp_order_count);
                        // $vendor_rent_count[$key]['vendor_name'] = $order_detail['vendor_name'];
                        // $order_equipment_count[$key]['product_qty'] = $order_equipment_count[$key]['product_qty'] + $order_detail['product_qty'];
                        // $order_equipment_count[$key]['total_deposite'] = $order_equipment_count[$key]['total_deposite'] + $order_detail['product_deposite'];
                        if($order_detail['current_status'] == 'Pending Pickup' || $order_detail['current_status'] == 'Picked Up' || $order_detail['current_status'] == 'Picked UP' || $order_detail['current_status'] == 'Pickuped')
                        {
                            $order_equipment_count[$key]['product_stop_qty'] = $order_equipment_count[$key]['product_stop_qty'] + $order_detail['product_qty'];
                        }
                        else
                        {
                            $order_equipment_count[$key]['product_live_qty'] = $order_equipment_count[$key]['product_live_qty'] + $order_detail['product_qty'];
                        }
                    }
                    else
                    {
                        $key = count($order_equipment_count);
                        $order_equipment_count[$key]['product_name'] = $order_detail['product_name'];
                        // $order_equipment_count[$key]['product_qty'] = $order_detail['product_qty'];
                        // $order_equipment_count[$key]['total_stop_deposite'] = $order_detail['product_deposite'];
                        // $order_equipment_count[$key]['total_live_deposite'] = $order_detail['product_deposite'];
                        if($order_detail['current_status'] == 'Pending Pickup' || $order_detail['current_status'] == 'Picked Up' || $order_detail['current_status'] == 'Picked UP' || $order_detail['current_status'] == 'Pickuped')
                        {
                            $order_equipment_count[$key]['product_stop_qty'] = $order_detail['product_qty'];
                            $order_equipment_count[$key]['product_live_qty'] = 0;
                        }
                        else
                        {
                            $order_equipment_count[$key]['product_stop_qty'] = 0;
                            $order_equipment_count[$key]['product_live_qty'] = $order_detail['product_qty'];
                        }
                        array_push($temp_order_count,$order_detail['product_id']);
                    }
                }
                // dd($vendor_rent_count);
                return $order_equipment_count;
            }
            // return view('OrderManagement.vendor_product_details',compact('products','order_details','filter_data'));
        }

        public function VendorPopulate(Request $request)
        {
            // if($customer_val!=null)
            // {
            //     $get_customers_list = DB::select("SELECT customer_name FROM customer_details WHERE customer_name LIKE '%$customer_val%' ");
            //     $get_customers_list = json_decode(json_encode($get_customers_list),true);
            //     return json_encode($get_customers_list);
            // }
            // else{
            //     return false;
            // }
            $query = $request->get('query');
            $filterResult = DB::select("SELECT registered_name FROM vendor_details WHERE registered_name LIKE '%$query%' ");
            $filterResult = json_decode(json_encode($filterResult),true);
            $data = array();
            foreach($filterResult as $key => $result)
            {
                $data[] = $result['registered_name'];
            }
            return response()->json($data);
        }

        // public function inventoryUpdateScript()
        // {
        //     $vendor_product_details = DB::table('order_details')
        //          ->select('vendor_product_id', DB::raw('count(*) as count'))
        //          ->groupBy('vendor_product_id')
        //          ->whereNotIn('order_details.current_status',['Pending Pickup','Picked Up', 'Picked UP','Pickuped'])
        //          ->get()
        //          ->toArray();
        //     dd(json_decode(json_encode($vendor_product_details),true));
        //     $vendor_product_details = json_decode(json_encode($vendor_product_details),true);
        //     foreach ($vendor_product_details as $key => $value)
        //     {
        //         $vendor_products = VendorProducts::where('id',$value['vendor_product_id'])->get()->toArray();
        //         if(isset($vendor_products[0]))
        //         {
        //             for ($i = 0; $i < $value['count']; $i++)
        //             {
        //                 $inventory_id = "".$vendor_products[0]['id']."-".$vendor_products[0]['product_id']."-".$vendor_products[0]['vendor_id']."-".$vendor_products[0]['product_brand']."-".$i."";
        //                 $insertData = [
        //                     'vendor_products_id'=>$vendor_products[0]['vendor_product_id'],
        //                     'availability_status'=>'0',
        //                     'inventory_id'=>$inventory_id,
        //                     'inventory_type'=>'0',
        //                     'current_location'=>'2',
        //                     'warehouse_id'=>$vendor_products[0]['warehouse_id'],
        //                     'created_by'=>session('username')
        //                 ];
        //                 VendorProductDetails::insert($insertData);
        //             }
        //         }
        //     }
        //     // dd($vendor_product_details);
        // }
        public function inventoryUpdateScript()
        {
            $vendor_product_details = DB::table('order_details')
                 ->select('vendor_product_id', DB::raw('count(*) as count'))
                 ->groupBy('vendor_product_id')
                 ->whereNotIn('order_details.current_status',['Pending Pickup','Picked Up', 'Picked UP','Pickuped'])
                 ->where('order_details.sale_rental','Rental')
                 ->get()
                 ->toArray();
            // dd(json_decode(json_encode($vendor_product_details),true));
            $vendor_product_details = json_decode(json_encode($vendor_product_details),true);
            // print_r($vendor_product_details);
            foreach ($vendor_product_details as $key => $value)
            {
                $vendor_products = VendorProducts::where('id',$value['vendor_product_id'])->get()->toArray();
                if(isset($vendor_products[0]))
                {
                    for ($i = 0; $i < $value['count']; $i++)
                    {
                        $inventory_id = "".$vendor_products[0]['id']."-".$vendor_products[0]['product_id']."-".$vendor_products[0]['vendor_id']."-".$vendor_products[0]['product_brand']."-".$i."";
                        $insertData = [
                            'vendor_products_id'=>$vendor_products[0]['id'],
                            'availability_status'=>'0',
                            'inventory_id'=>$inventory_id,
                            'inventory_type'=>'0',
                            'current_location'=>'2',
                            'warehouse_id'=>$vendor_products[0]['warehouse_id'],
                            'created_by'=>session('username')
                        ];
                        VendorProductDetails::insert($insertData);
                    }
                    VendorProducts::where('id',$vendor_products[0]['id'])->update(['product_quantity'=>$value['count']]);
                }
            }
            $order_details = DB::table('order_details')
                                ->select('order_details.id as order_details_id',
                                        'order_details.vendor_product_id as vendor_product_id',
                                        'order_details.creation_date as rental_date',
                                        'order_details.pickup_date as pickup_date',
                                        'order_details.vendor_id as vendor_id')
                                ->whereNotIn('order_details.current_status',['Pending Pickup','Picked Up', 'Picked UP','Pickuped'])
                                ->where('order_details.sale_rental','Rental')
                                ->get()
                                ->toArray();
            
            $order_details = json_decode(json_encode($order_details),true);

            foreach ($order_details as $order_detail)
            {
                $vendor_product_details = DB::table('vendor_product_details')
                                            ->select('vendor_product_details.id as vendor_product_details_id','vendor_product_details.inventory_id as inventory_id')
                                            ->where(['vendor_products_id'=>$order_detail['vendor_product_id'],'availability_status'=>0])
                                            ->whereIn('current_location',[1,2])
                                            ->get()
                                            ->toArray();
                $vendor_product_details = json_decode(json_encode($vendor_product_details),true);
                $rented_product_id = VendorRentedProducts::insertGetId(
                                    [
                                        'vendor_id'=>$order_detail['vendor_id'],
                                        'vendor_product_id'=>$order_detail['vendor_product_id'],
                                        'unique_id'=>$vendor_product_details[0]['inventory_id'],
                                        'rental_date'=>$order_detail['rental_date'],
                                        'pickup_date'=>$order_detail['pickup_date'],
                                        'status'=>'On Rent',
                                        'created_by'=>'Script'
                                    ]);
                OrderDetails::where('id',$order_detail['order_details_id'])->update(['vendor_product_details_id'=>$vendor_product_details[0]['vendor_product_details_id'],'unique_id'=>$vendor_product_details[0]['inventory_id'],'rented_product_id'=>$rented_product_id]);
                VendorProductDetails::where('id',$vendor_product_details[0]['vendor_product_details_id'])->update(['availability_status'=>'1','current_location'=>'0']);
            }
            // dd($vendor_product_details);
        }
        public static function fetchCrDrData($id,$intype)
        {
            $order_details = DB::table('order_details')->where('id',$id)->first();
            if(DB::table('cr_dr_note')->where('order_details_id',$order_details->id)->where('intype',$intype)->where('flag','A')->exists())
            {
                $cr_dr_notes = DB::table('cr_dr_note')->where('order_details_id',$order_details->id)->where('intype',$intype)->get()->groupBy('crdrtype');
                if(isset($cr_dr_notes['Cr']))
                {
                    $creditnotes = $cr_dr_notes['Cr']->groupBy('intype');
                    if($intype == 'R')
                    {
                        $order_details->product_rent = $order_details->product_rent - array_sum($creditnotes['R']->pluck('amount')->toArray());
                    }
                    if($intype == 'D')
                    {
                        $order_details->product_deposite = $order_details->product_deposite - array_sum($creditnotes['D']->pluck('amount')->toArray());
                    }
                    if($intype == 'T')
                    {
                        $order_details->transport = $order_details->transport - array_sum($creditnotes['T']->pluck('amount')->toArray());
                    }
                }
                if(isset($cr_dr_notes['Dr']))
                {
                    $debitnotes = $cr_dr_notes['Dr']->groupBy('intype');
                    if(isset($debitnotes['R']))
                    {
                        $order_details->product_rent = $order_details->product_rent + array_sum($debitnotes['R']->pluck('amount')->toArray());
                    }
                    if(isset($debitnotes['D']))
                    {
                        $order_details->product_deposite = $order_details->product_deposite + array_sum($debitnotes['D']->pluck('amount')->toArray());
                    }
                    if(isset($debitnotes['T']))
                    {
                        $order_details->transport = $order_details->transport + array_sum($debitnotes['T']->pluck('amount')->toArray());
                    }
                }
            }
            if($intype == 'R')
            {
                return $order_details->product_rent;
            }
            if($intype == 'D')
            {
                return $order_details->product_deposite;
            }
            if($intype == 'T')
            {
                return $order_details->transport;
            }
        }
        public function vendor_inventory_auto(Request $request){
            if($request->method() == "POST"){
                // dd($request->all());
                DB::beginTransaction();
                try{
                    $updatearr = [
                        "inventory_no"=>$request->get('updateinventoryid'),
                        "invoice_no"=>$request->get('updateinvoiceno'),
                        "invoice_status"=>$request->get('invoice_status'),
                        "payment_state"=>$request->get('payment_state'),
                        "vdr_serial_no"=>$request->get('updatevdrserialno'),
                        "vdr_rent"=>$request->get('updatevdrrate')
                    ];
                    if(!DB::table('vendor_inventory_auto')->where('order_details_id',$request->get('updateid'))->where('order_type',strtolower($request->get('updatetype')))->where('flag',1)->exists()){
                        $updatearr["created_by"] = session('username');
                    }
                    if($request->get('invoice_status') == 1){
                        $updatearr["verified_at"] = date('Y-m-d H:i:s');
                        $updatearr["verified_by"] = session('username');
                    }
                    if(!empty($request->hasFile('updatepaymentimg'))){
                        $updatepaymentimg = $_FILES['updatepaymentimg']['name'];
                        //print_r($_FILES['shop_images']['name']);
                        $targetDir = "assets/uploads/vdr_pay_img/";
                        $fileName = basename($_FILES['updatepaymentimg']['name']);
                        $targetFilePath = $targetDir . $fileName;
                        $fileType =strtolower(pathinfo($targetFilePath,PATHINFO_EXTENSION));
                        $new_file_name = $targetDir."".$request->get('updateid')."".$request->get('updatetype').".".$fileType;
                        move_uploaded_file($_FILES["updatepaymentimg"]["tmp_name"], $new_file_name);    
                        $updatepaymentimg_filePath = $request->get('updateid')."".$request->get('updatetype').".".$fileType;
                        $updatearr['payment_image'] = $updatepaymentimg_filePath;
                    }

                    if(!empty($request->hasFile('updateinvoiceimg'))){
                        $updateinvoiceimg = $_FILES['updateinvoiceimg']['name'];
                        //print_r($_FILES['shop_images']['name']);
                        $targetDir = "assets/uploads/vdr_invoice_img/";
                        $fileName = basename($_FILES['updateinvoiceimg']['name']);
                        $targetFilePath = $targetDir . $fileName;
                        $fileType =strtolower(pathinfo($targetFilePath,PATHINFO_EXTENSION));
                        $new_file_name = $targetDir."".$request->get('updateid')."".$request->get('updatetype').".".$fileType;
                        move_uploaded_file($_FILES["updateinvoiceimg"]["tmp_name"], $new_file_name);    
                        $updatepaymentimg_filePath = $request->get('updateid')."".$request->get('updatetype').".".$fileType;
                        $updatearr['vendor_invoice_img'] = $updatepaymentimg_filePath;
                    }
                    
                    DB::table('vendor_inventory_auto')->updateOrInsert(
                        [
                            "order_details_id"=>$request->get('updateid'),
                            "order_type"=>strtolower($request->get('updatetype')),
                        ],$updatearr);
                    DB::commit();
                    return redirect()->back()->with('message','Details Updated!');
                }catch(Exception $ex){
                    DB::rollback();
                    return redirect()->back()->with('error',$ex->getMessage());
                }
            }
            // dd($request->all());
            $product_details = DB::table('order_details')
                ->join('del_orders','del_orders.order_id','=','order_details.order_id')
                ->join('products','products.id','=','order_details.product_id')
                ->join('vendor_details','vendor_details.id','=','order_details.vendor_id')
                ->join('vendor_warehouse','vendor_warehouse.id','=','order_details.vendor_warehouse_id')
                ->select('del_orders.DelDate','order_details.unique_id','order_details.sale_rental',
                        'order_details.current_status','vendor_details.registered_name','vendor_warehouse.wh_name',
                        'vendor_warehouse.wh_area','vendor_warehouse.wh_city','products.product_name',
                        'order_details.id','order_details.creation_date as start_date','order_details.pickup_date as end_date',
                        'del_orders.deliverypickup',
                        'del_orders.status as delstatus','order_details.product_rent as prod_rent')
                ->whereNotIn('del_orders.status',['Cancel'])
                ->whereNotIn('order_details.status',['Cancel'])
                ->when((empty($request->get('filterfromdate')) || empty($request->get('filtertodate'))) && empty($request->get('filtervdrname')),function($query){
                    $start_date = date('d-m-Y',strtotime("-1 Months"));
                    $end_date = date('d-m-Y');
                    $query->whereBetween(DB::raw("STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y')"),[DB::raw("STR_TO_DATE('$start_date','%d-%m-%Y')"),DB::raw("STR_TO_DATE('$end_date','%d-%m-%Y')")]);
                })
                ->when($request->get('filterfromdate') && $request->get('filtertodate'),function($query)use($request){
                    $start_date = date('d-m-Y',strtotime($request->get('filterfromdate')));
                    $end_date = date('d-m-Y',strtotime($request->get('filtertodate')));
                    $query->whereBetween(DB::raw("STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y')"),[DB::raw("STR_TO_DATE('$start_date','%d-%m-%Y')"),DB::raw("STR_TO_DATE('$end_date','%d-%m-%Y')")]);
                })
                ->when($request->get('filtervdrname'),function($query)use($request){
                    $query->where('vendor_details.registered_name','LIKE','%'.$request->get('filtervdrname').'%');
                })
                ->when($request->get('filterproducts'),function($query)use($request){
                    $query->whereIn('order_details.product_id',$request->get('filterproducts'));
                })
                ->when($request->get('filterproductstatus'),function($query)use($request){
                    if($request->get('filterproductstatus') == 'live'){
                        $query->whereNotIn('order_details.current_status',['Picked Up','Picked UP']);
                    }elseif($request->get('filterproductstatus') == 'stop'){
                        $query->whereIn('order_details.current_status',['Picked Up','Picked UP']);
                    }
                })
                ->orderBy('order_details.id','DESC')
                ->get();
                $inventories = new Collection;
                foreach($product_details as $key => $product){
                    $product->stop_date = null;
                    if(DB::table('vendor_inventory_auto')->where('order_details_id',$product->id)->where('order_type','delivery')->where('flag',1)
                    ->when($request->get('filtervdrinvoiceno'),function($query)use($request){
                        $query->where('invoice_no','LIKE','%'.$request->get('filtervdrinvoiceno').'%');
                    })
                    ->when($request->get('filterinvoicestatus'),function($query)use($request){
                        if($request->get('filterinvoicestatus') == 'pending' || $request->get('filterinvoicestatus') == 'unverified'){
                            $query->where('invoice_status','unverified');
                        }
                        elseif($request->get('filterinvoicestatus') == 'verified'){
                            $query->where('invoice_status','verified');
                        }
                    })
                    ->when($request->get('filterpaymentstatus'),function($query)use($request){
                        if($request->get('filterpaymentstatus') !='all'){
                            $query->where('payment_state',$request->get('filterpaymentstatus'));
                        }
                    })
                    ->exists()){
                        $vdrinvdetails = DB::table('vendor_inventory_auto')->where('order_details_id',$product->id)->where('order_type','delivery')->where('flag',1)
                        ->when($request->get('filtervdrinvoiceno'),function($query)use($request){
                            $query->where('invoice_no','LIKE','%'.$request->get('filtervdrinvoiceno').'%');
                        })
                        ->when($request->get('filterinvoicestatus'),function($query)use($request){
                            if($request->get('filterinvoicestatus') == 'pending' || $request->get('filterinvoicestatus') == 'unverified'){
                                $query->where('invoice_status','unverified');
                            }
                            elseif($request->get('filterinvoicestatus') == 'verified'){
                                $query->where('invoice_status','verified');
                            }
                        })
                        ->when($request->get('filterpaymentstatus'),function($query)use($request){
                            if($request->get('filterpaymentstatus') !='all'){
                                $query->where('payment_state',$request->get('filterpaymentstatus'));
                            }
                        })
                        ->first();
                        // dd($vdrinvdetails);
                        $product->unique_id = $vdrinvdetails->inventory_no;
                        $product->invoice_no = $vdrinvdetails->invoice_no;
                        $product->vdr_serial_no = $vdrinvdetails->vdr_serial_no;
                        $product->invoice_status = $vdrinvdetails->invoice_status;
                        $product->verified_at = $vdrinvdetails->verified_at;
                        $product->verified_by = $vdrinvdetails->verified_by;
                        $product->payment_state = $vdrinvdetails->payment_state;
                        $product->payment_img = $vdrinvdetails->payment_image;
                        $product->vendor_invoice_img = $vdrinvdetails->vendor_invoice_img;
                        $product->vdr_rent = $vdrinvdetails->vdr_rent;
                    }
                    else{
                        if($request->get('filtervdrinvoiceno') || (!empty($request->get('filterinvoicestatus')) && $request->get('filterinvoicestatus')!='all') || (!empty($request->get('filterpaymentstatus') && $request->get('filterpaymentstatus')!='all'))){
                            $product_details->forget($key);
                            continue;
                        }
                        $product->invoice_no = null;
                        $product->vdr_serial_no = null;
                        $product->invoice_status = null;
                        $product->verified_at = null;
                        $product->verified_by = null;
                        $product->payment_state = null;
                        $product->payment_img = null;
                        $product->vendor_invoice_img = null;
                        $product->vdr_rent = 0;
                    }
                    if(DB::table('pickups')->where('order_details_id',$product->id)->whereNull('status')->exists()){
                        $product->stop_date = DB::table('pickups')->where('order_details_id',$product->id)->whereNull('status')->first()->pickup_date;
                    }
                    $inventories->push($product);
                    // if(DB::table('renewals')->where('order_details_id',$product->id)->whereNotIn('status',['Cancel'])->exists()){
                    //     // dd("a");
                    //     // dd($key);
                    //     $renewals = DB::table('renewals')->join('del_orders','del_orders.order_id','=','renewals.collection_order_id')->select('del_orders.order_id','del_orders.DelDate','renewals.start_date','renewals.end_date','del_orders.deliverypickup','renewals.id')->where('renewals.order_details_id',$product->id)->whereNotIn('renewals.status',['Cancel'])->get();
                    //     $tempobj = $product;
                    //     foreach($renewals as $renewal){
                    //         $tempobj->DelDate = $renewal->DelDate;
                    //         $tempobj->deliverypickup = $renewal->deliverypickup;
                    //         $tempobj->start_date = $renewal->start_date;
                    //         $tempobj->end_date = $renewal->end_date;
                    //         $tempobj->stop_date = null;
                    //         $tempobj->id = $renewal->id;
                    //         if(DB::table('vendor_inventory_auto')->where('order_details_id',$tempobj->id)->where('order_type',1)->where('flag',1)->exists()){
                    //             $vdrinvdetails = DB::table('vendor_inventory_auto')->where('order_details_id',$tempobj->id)->where('order_type',1)->where('flag',1)->first();
                    //             $tempobj->unique_id = $vdrinvdetails->inventory_id;
                    //             $tempobj->invoice_no = $vdrinvdetails->invoice_no;
                    //             $tempobj->invoice_status = $vdrinvdetails->invoice_status;
                    //             $tempobj->verified_at = $vdrinvdetails->verified_at;
                    //             $tempobj->verified_by = $vdrinvdetails->verified_by;
                    //             $tempobj->payment_state = $vdrinvdetails->payment_state;
                    //             $tempobj->payment_img = $vdrinvdetails->payment_image;
                    //         }
                    //         else{
                    //             $tempobj->invoice_no = null;
                    //             $tempobj->invoice_status = null;
                    //             $tempobj->verified_at = null;
                    //             $tempobj->verified_by = null;
                    //             $tempobj->payment_state = null;
                    //             $tempobj->payment_img = null;
                    //         }
                    //         $inventories->push($tempobj);
                    //     }
                    // }
                }
                $renewals = DB::table('renewals')
                    ->join('del_orders','del_orders.order_id','=','renewals.collection_order_id')
                    ->join('order_details','order_details.id','=','renewals.order_details_id')
                    ->join('products','products.id','=','order_details.product_id')
                    ->join('vendor_details','vendor_details.id','=','order_details.vendor_id')
                    ->join('vendor_warehouse','vendor_warehouse.id','=','order_details.vendor_warehouse_id')
                    ->select('del_orders.order_id','del_orders.DelDate','order_details.unique_id','order_details.sale_rental','renewals.id as renewid',
                            'order_details.current_status','vendor_details.registered_name','vendor_warehouse.wh_name',
                            'vendor_warehouse.wh_area','vendor_warehouse.wh_city','products.product_name',
                            'order_details.id','order_details.creation_date as start_date','order_details.pickup_date as end_date',
                            'del_orders.deliverypickup',
                            'del_orders.status as delstatus','renewals.start_date','renewals.end_date','del_orders.deliverypickup','renewals.id','order_details.product_rent as prod_rent')
                    ->whereNotIn('del_orders.status',['Cancel'])
                    ->whereNotIn('order_details.status',['Cancel'])
                    ->whereNotIn('renewals.status',['Cancel'])
                    ->when((empty($request->get('filterfromdate')) || empty($request->get('filtertodate'))) && empty($request->get('filtervdrname')),function($query){
                        $start_date = date('d-m-Y',strtotime("-1 Months"));
                        $end_date = date('d-m-Y');
                        $query->whereBetween(DB::raw("STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y')"),[DB::raw("STR_TO_DATE('$start_date','%d-%m-%Y')"),DB::raw("STR_TO_DATE('$end_date','%d-%m-%Y')")]);
                    })
                    ->when($request->get('filterfromdate') && $request->get('filtertodate'),function($query)use($request){
                        $start_date = date('d-m-Y',strtotime($request->get('filterfromdate')));
                        $end_date = date('d-m-Y',strtotime($request->get('filtertodate')));
                        $query->whereBetween(DB::raw("STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y')"),[DB::raw("STR_TO_DATE('$start_date','%d-%m-%Y')"),DB::raw("STR_TO_DATE('$end_date','%d-%m-%Y')")]);
                    })
                    ->when($request->get('filtervdrname'),function($query)use($request){
                        $query->where('vendor_details.registered_name','LIKE','%'.$request->get('filtervdrname').'%');
                    })
                    ->when($request->get('filterproducts'),function($query)use($request){
                        $query->whereIn('order_details.product_id',$request->get('filterproducts'));
                    })
                    ->when($request->get('filterproductstatus'),function($query)use($request){
                        if($request->get('filterproductstatus') == 'live'){
                            $query->whereNotIn('order_details.current_status',['Picked Up','Picked UP']);
                        }elseif($request->get('filterproductstatus') == 'stop'){
                            $query->whereIn('order_details.current_status',['Picked Up','Picked UP']);
                        }
                    })
                    ->orderBy('order_details.id','DESC')
                    // ->toSql();
                    // ->paginate(100);
                    ->get();
                    // dd($renewals);
                    // dd($request->all());
                foreach($renewals as $renewal){
                    $renewal->DelDate = $renewal->DelDate;
                    $renewal->deliverypickup = $renewal->deliverypickup;
                    $renewal->start_date = $renewal->start_date;
                    $renewal->end_date = $renewal->end_date;
                    $renewal->stop_date = null;
                    $renewal->id = $renewal->renewid;
                    if(DB::table('vendor_inventory_auto')->where('order_details_id',$renewal->id)->where('order_type','collection')->where('flag',1)
                    ->when($request->get('filtervdrinvoiceno'),function($query)use($request){
                        $query->where('invoice_no','LIKE','%'.$request->get('filtervdrinvoiceno').'%');
                    })
                    ->when($request->get('filterinvoicestatus'),function($query)use($request){
                        if($request->get('filterinvoicestatus') == 'pending' || $request->get('filterinvoicestatus') == 'unverified'){
                            $query->where('invoice_status','unverified');
                        }
                        elseif($request->get('filterinvoicestatus') == 'verified'){
                            $query->where('invoice_status','verified');
                        }
                    })
                    ->when($request->get('filterpaymentstatus'),function($query)use($request){
                        // $query->where('payment_state',$request->get('filterpaymentstatus'));
                        if($request->get('filterpaymentstatus') !='all'){
                            $query->where('payment_state',$request->get('filterpaymentstatus'));
                        }
                    })
                    ->exists()){
                        $vdrinvdetails = DB::table('vendor_inventory_auto')->where('order_details_id',$renewal->id)->where('order_type','collection')->where('flag',1)
                        ->when($request->get('filtervdrinvoiceno'),function($query)use($request){
                            $query->where('invoice_no','LIKE','%'.$request->get('filtervdrinvoiceno').'%');
                        })
                        ->when($request->get('filterinvoicestatus'),function($query)use($request){
                            if($request->get('filterinvoicestatus') == 'pending' || $request->get('filterinvoicestatus') == 'unverified'){
                                $query->where('invoice_status','unverified');
                            }
                            elseif($request->get('filterinvoicestatus') == 'verified'){
                                $query->where('invoice_status','verified');
                            }
                        })
                        ->when($request->get('filterpaymentstatus'),function($query)use($request){
                            // $query->where('payment_state',$request->get('filterpaymentstatus'));
                            if($request->get('filterpaymentstatus') !='all'){
                                $query->where('payment_state',$request->get('filterpaymentstatus'));
                            }
                        })
                        ->first();
                        $renewal->unique_id = $vdrinvdetails->inventory_no;
                        $renewal->invoice_no = $vdrinvdetails->invoice_no;
                        $renewal->vdr_serial_no = $vdrinvdetails->vdr_serial_no;
                        $renewal->invoice_status = $vdrinvdetails->invoice_status;
                        $renewal->verified_at = $vdrinvdetails->verified_at;
                        $renewal->verified_by = $vdrinvdetails->verified_by;
                        $renewal->payment_state = $vdrinvdetails->payment_state;
                        $renewal->payment_img = $vdrinvdetails->payment_image;
                        $renewal->vendor_invoice_img = $vdrinvdetails->vendor_invoice_img;
                        $renewal->vdr_rent = $vdrinvdetails->vdr_rent;
                    }
                    else{
                        if($request->get('filtervdrinvoiceno') || (!empty($request->get('filterinvoicestatus')) && $request->get('filterinvoicestatus')!='all') || (!empty($request->get('filterpaymentstatus') && $request->get('filterpaymentstatus')!='all'))){
                            $product_details->forget($key);
                            continue;
                        }
                        $renewal->invoice_no = null;
                        $renewal->vdr_serial_no = null;
                        $renewal->invoice_status = null;
                        $renewal->verified_at = null;
                        $renewal->verified_by = null;
                        $renewal->payment_state = null;
                        $renewal->payment_img = null;
                        $renewal->vendor_invoice_img = null;
                        $renewal->vdr_rent = 0;
                    }
                    $inventories->push($renewal);
                }
                $inventories = $inventories->sortByDesc('id')->paginate(10);
            // dd($inventories);
            $products = DB::table('products')->where('flag','Active')->get();
            return view('VendorInventoryManagement.vendor-inventory-automated',compact('inventories','products'));

        }
    }
?>