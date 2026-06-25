<?php

namespace App\Http\Controllers\MaintenanceOrder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Exception;
use App\Http\Controllers\RenewalPickup\RenewalPickupController;
use App\Http\Controllers\OrderManagement\EditOrderController;

class ReplacementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        // $orders = DB::table('del_orders')->where('del_orders.deliverypickup','Replace')->orderBy(DB::raw("STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y')",'DESC'))->get();
        $orders = DB::table('replacement_table')
            ->join('del_orders','replacement_table.deliveryorderid','=','del_orders.order_id')
            ->when($request->get('filtercustomername'),function($query)use($request){
                $query->where('del_orders.shipping_first_name','LIKE','%'.$request->get('filtercustomername').'%');
            })
            ->when($request->get('filterpatientname'),function($query)use($request){
                $query->where('del_orders.patient_name','LIKE','%'.$request->get('filterpatientname').'%');
            })
            ->when($request->get('filterstartdate') && $request->get('filterenddate'),function($query)use($request){
                $temp_from_min_date = date('d-m-Y',strtotime($request->get('filterstartdate')));
                $temp_from_max_date = date('d-m-Y',strtotime($request->get('filterenddate')));
                $query->whereBetween(DB::raw("STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y')"),[DB::raw("STR_TO_DATE('$temp_from_min_date','%d-%m-%Y')"),DB::raw("STR_TO_DATE('$temp_from_max_date','%d-%m-%Y')")]);
            })
            ->orderBy(DB::raw("STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y')",'DESC'))
            ->get()
            ->paginate(10);
        return view('Replacement.view',compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
        if($request->get('reqtype') == 'getcustomers'){
            $customers = DB::table('del_orders')
                ->join('order_details','order_details.order_id','=','del_orders.order_id')
                ->select('del_orders.lead_id','del_orders.DelDate','del_orders.shipping_first_name','del_orders.patient_name','del_orders.line_item_1','del_orders.fulldetails')
                ->when($request->get('searchcustomertxt'),function($query)use($request){
                    $query->orWhere('del_orders.shipping_first_name','LIKE','%'.$request->get('searchcustomertxt').'%')
                        ->orWhere('del_orders.mobileno','LIKE','%'.$request->get('searchcustomertxt').'%')
                        ->orWhere('del_orders.patient_name','LIKE','%'.$request->get('searchcustomertxt').'%');
                })
                ->where('del_orders.deliverypickup','Delivery')
                ->where('order_details.sale_rental','Rental')
                ->whereNotIn('order_details.current_status',['CustStop','Cancel','Pending Pickup','Picked Up'])
                ->get()
                ->groupBy('lead_id');
            foreach($customers as $key=>$value){
                $customers[$key][0]->line_item_1 = implode(', ',$value->pluck('line_item_1')->toArray());
            }
            return $customers;
        }
        elseif($request->get('reqtype') == 'productdetails'){
            $products = DB::table('del_orders')
                ->join('order_details','order_details.order_id','=','del_orders.order_id')
                ->join('vendor_details','order_details.vendor_id','=','vendor_details.id')
                ->join('vendor_warehouse','vendor_warehouse.id','=','order_details.vendor_warehouse_id')
                ->join('products','order_details.product_id','=','products.id')
                ->select('order_details.id','vendor_details.registered_name','vendor_warehouse.wh_name','vendor_warehouse.wh_area','vendor_warehouse.wh_city','products.product_name','order_details.creation_date')
                ->when($request->get('leadid'),function($query)use($request){
                    $query->where('del_orders.lead_id',$request->get('leadid'));
                })
                ->where('del_orders.deliverypickup','Delivery')
                ->where('order_details.sale_rental','Rental')
                ->whereNotIn('order_details.current_status',['CustStop','Cancel','Pending Pickup','Picked Up'])
                ->get();
            foreach($products as $key=>$product)
            {
                $products[$key]->creation_date = date('d-M-y',strtotime($product->creation_date));
            }
            return $products;
        }
        else{
            throw new Exception('Server Error');
        }
    }

    public function createOrder(Request $request){   
        if($request->get('checkedproducts')){
            // ***** Customer Details ***** //
            $customer = DB::table('del_orders')
                ->join('order_details','order_details.order_id','=','del_orders.order_id')
                ->select('del_orders.*')
                ->when($request->get('checkedproducts'),function($query)use($request){
                    $query->where('order_details.id',$request->get('checkedproducts'));
                })
                ->first();

            // ***** Products ***** //
            $products = DB::table('del_orders')
                ->join('order_details','order_details.order_id','=','del_orders.order_id')
                ->join('product_brands','product_brands.id','=','order_details.product_brand')
                ->join('vendor_details','order_details.vendor_id','=','vendor_details.id')
                ->join('vendor_products','vendor_products.id','=','order_details.product_batch')
                ->join('vendor_warehouse','vendor_warehouse.id','=','order_details.vendor_warehouse_id')
                ->join('products','order_details.product_id','=','products.id')
                ->select('order_details.id','order_details.vendor_id','order_details.product_id','products.product_name','order_details.unique_id','order_details.product_rent','order_details.product_deposite')
                ->when($request->get('checkedproducts'),function($query)use($request){
                    $query->whereIn('order_details.id',$request->get('checkedproducts'));
                })
                ->where('del_orders.deliverypickup','Delivery')
                ->where('order_details.sale_rental','Rental')
                ->whereNotIn('order_details.current_status',['CustStop','Cancel','Pending Pickup','Picked Up'])
                ->get();
            
            foreach($products as $key=>$product){
                $products[$key]->product_rent = RenewalPickupController::fetchCrDrData($product->id,'R');
                $products[$key]->product_deposite = RenewalPickupController::fetchCrDrData($product->id,'D');
                $products[$key]->transport = RenewalPickupController::fetchCrDrData($product->id,'T');
                $get_warehouse_details = DB::table('vendor_warehouse')->where('vendor_id',17)->whereIn('id',[19,175])->get();
                $products[$key]->q5c_warehouse_details = $get_warehouse_details;
                $vendor_warehouse_details = DB::table('vendor_warehouse')->where('vendor_id',$product->vendor_id)->get();
                $products[$key]->vendor_warehouse_details = $vendor_warehouse_details;
            }

            // If Product Sold not replaced
            if($request->get('generateorder') == 'sale'){
                DB::beginTransaction();
                try{
                    return view('Replacement.sale',compact('products','customer'));
                }catch(Exception $ex){
                    DB::rollback();
                    return redirect()->back()->with('error',$ex->getMessage());
                }
            }
                // ***** Del Boys ***** //
            $delboys = DB::table('delusers')->select('username')->where('role','user')->where('status','Active')->orderBy('username','ASC')->get();

            // // ***** Vendors ***** //
            // $vendors = array();
            // foreach($request->get('checkedproducts') as $key=>$value){
            //     $productid = DB::table('order_details')->select('product_id')->where('order_details.id',$value)->first()->product_id;
            //     $vendors[$value] = $this->filterInventory('Vendor',$productid);
            // }

            // ***** Master Products ***** //
            $masterproducts = DB::table('products')->select('id','product_name')->where('flag','Active')->get();
            // ***** Return Statement ***** //
            // dd($products,$customer,$delboys,$vendors);
            return view('Replacement.create',compact('masterproducts','products','customer','delboys'));
        }
        else{
            return redirect()->back()->with('error','Select atleast one product!');
        }
    }

    public function sale_product(Request $request){
        DB::beginTransaction();
        try{
            // Get basic order details and customer details from del orders table..
            $order_details = DB::table('del_orders')->where('order_id',$request->get('baseorderid'))->first();
            $pickups = array();
            $deliveries = array();
            // Insert pickup order record in del_orders to get pickup order id assigned to customer as pickup not executed but for record auto generated
            $pickupid = DB::table('del_orders')->insertGetId([
                'status'=>'Picked up',
                'lead_id'=>$order_details->lead_id,
                'web_order_id'=>$order_details->web_order_id,
                'patient_name'=>$order_details->patient_name,
                'vendor_id'=>$order_details->vendor_id,
                'deliverypickup'=>'Pick Up',
                'DelDate'=>date('d-m-Y',strtotime($request->get('orderdate'))),
                'location'=>$order_details->location,
                'shipping_first_name'=>$order_details->shipping_first_name,
                'cust_gender'=>$order_details->cust_gender,
                'TotalAmt'=>0,
                'PaymentMode'=>$order_details->PaymentMode,
                'mobileno'=>$order_details->mobileno,
                'DelAssignedTo'=>'Customer',
                'TravelMode'=>'Null',
                'order_approval_status'=>'Approved',
                'comment'=>'Same Product Sold',
                'fulldetails'=>$order_details->fulldetails,
                'line_item_1'=>"0",
            ]);
            // Insert delivery order record in del_orders to get delivery order id assigned to customer as delivery not executed but for record auto generated
            $deliveryid = DB::table('del_orders')->insertGetId([
                'status'=>'Delivered',
                'lead_id'=>$order_details->lead_id,
                'web_order_id'=>$order_details->web_order_id,
                'patient_name'=>$order_details->patient_name,
                'vendor_id'=>$order_details->vendor_id,
                'deliverypickup'=>'Delivery',
                'DelDate'=>date('d-m-Y',strtotime($request->get('orderdate'))),
                'location'=>$order_details->location,
                'shipping_first_name'=>$order_details->shipping_first_name,
                'cust_gender'=>$order_details->cust_gender,
                'TotalAmt'=>array_sum($request->get('rts_selling_rate')),
                'PaymentMode'=>$order_details->PaymentMode,
                'mobileno'=>$order_details->mobileno,
                'DelAssignedTo'=>'Customer',
                'TravelMode'=>'Null',
                'order_approval_status'=>'Approved',
                'comment'=>'Same Product Sold',
                'fulldetails'=>$order_details->fulldetails,
                'line_item_1'=>'0',
            ]);
            // calculate pickup amount based on the deposit adjusted or check if deposit is taken or not..
            $pickupamount = array_sum(DB::table('order_details')->select('product_deposite')->whereIn('id',$request->get('rts_order_details_id'))->get()->pluck('product_deposite')->toArray());
            $products = array();
            foreach($request->get('rts_order_details_id') as $key=>$value){
                // get order product details from order details and products table..
                $product_details = DB::table('order_details')
                    ->join('products','products.id','=','order_details.product_id')
                    ->select('order_details.*','products.product_name')
                    ->where('order_details.id',$value)
                    ->first();
                
                // temporarly store product names in array to add in del_orders table..
                array_push($products,$product_details->product_name);


                // insert pickup record in pickups table
                DB::table('pickups')->insert([
                    'pickup_order_id'=>$pickupid,
                    'del_order_id' => $product_details->order_id,
                    'order_details_id' => $product_details->id,
                    'lead_id' => $order_details->lead_id,
                    'vendor_id' => $product_details->vendor_id,
                    'product_id' => $product_details->product_id,
                    'pickup_date' => $request->get('orderdate'),
                    'cash_amount' => 0,
                    'created_by' => session('username')
                ]);
                // insert delivery sale product record in order details table
                $order_details_id = DB::table('order_details')->insertGetId([
                    'order_id'=> $deliveryid,
                    'customer_id'=>$product_details->customer_id,
                    'product_id'=>$product_details->product_id,
                    'vendor_product_id'=>0,
                    'vendor_id'=>$product_details->vendor_id,
                    'vendor_warehouse_id'=>$product_details->vendor_warehouse_id,
                    'product_brand'=>$product_details->product_brand,
                    'product_batch'=>0,
                    'product_qty'=>$product_details->product_qty,
                    'months'=>$product_details->months,
                    'product_rent'=>$request->get('rts_selling_rate')[$key],
                    'product_deposite'=>0,
                    'transport'=>0,
                    'sale_rental'=>'Sale',
                    'vendor_product_details_id'=>0,
                    'unique_id'=>0,
                    'product_serial_nos'=>$product_details->product_serial_nos,
                    'creation_date'=>$request->get('orderdate'),
                    'pickup_date'=>date('Y-m-d',strtotime("+1 months",strtotime($request->get('orderdate')))),
                    'status'=>'Accepted',
                    'created_by'=>session('username'),
                ]);
                // if adjusted in rent then add crdr record in adjustment_table
                if($request->get('rts_adjust_rent')[$key] != 0 || $request->get('rts_adjust_rent')[$key] != null){
                    DB::table('adjustment_table')->insert(
                        [
                            "product_id"=>$product_details->product_id,
                            "order_id"=>$deliveryid,
                            "order_details_id"=>$product_details->id,
                            "adjusted_order_details_id"=>$order_details_id,
                            "fromorderid"=>$product_details->order_id,
                            "fromtype"=>'R',
                            "intype"=>'R',
                            "adjusted_amount"=>$request->get('rts_adjust_rent')[$key]
                        ]
                    );
                }
                // if adjusted in deposit then add crdr record in adjustment_table and also sub. amoount of pickup.
                if($request->get('rts_adjust_deposit')[$key] != 0 || $request->get('rts_adjust_deposit')[$key] != null){
                    DB::table('adjustment_table')->insert(
                        [
                            "product_id"=>$product_details->product_id,
                            "order_id"=>$deliveryid,
                            "order_details_id"=>$product_details->id,
                            "adjusted_order_details_id"=>$order_details_id,
                            "fromorderid"=>$product_details->order_id,
                            "fromtype"=>'D',
                            "intype"=>'D',
                            "adjusted_amount"=>$request->get('rts_adjust_deposit')[$key]
                        ]
                    );
                    $pickupamount = $pickupamount - $request->get('rts_adjust_deposit')[$key];
                }
            }
            // dd($products);
            // update product names in del_orders table..
            DB::table('del_orders')->whereIn('order_id',[$deliveryid,$pickupid])->update(['line_item_1'=>implode(', ',$products)]);
            // Update old orders product details..
            DB::table('order_details')->whereIn('id',$request->get('rts_order_details_id'))->update(['current_status'=>'Picked Up']);
            // commit all changes 
            DB::commit();
            // redirect to pending payments view tith message with updated records
            return redirect()->to('pending_payments')->with('message','Order Generated Successfully!');
        }catch(Exception $ex){
            // catch the exception and rollback all db crud operations
            DB::rollback();
            dd($ex);
            // return redirect back to view page to display error message..
            return redirect()->back()->with('error',$ex->getMessage());
        }
    }

    public function filterInventory($stack,$prodid,$id,$wareid=null){
        $availablevendor = DB::table('vendor_details')
            ->join('vendor_products','vendor_products.vendor_id','vendor_details.id')
            ->join('product_brands','product_brands.id','=','vendor_products.product_brand')
            ->join('vendor_warehouse','vendor_products.warehouse_id','vendor_warehouse.id')
            ->join('vendor_product_details','vendor_products.id','=','vendor_product_details.vendor_products_id')
            ->when($stack=='Vendor',function($query)use($id){
                $query->select('vendor_details.registered_name','vendor_details.id');
                $query->distinct('vendor_details.id');
            })
            ->when($stack=='Warehouse',function($query)use($id){
                $query->select('vendor_warehouse.id','vendor_warehouse.wh_name','vendor_warehouse.wh_area','vendor_warehouse.wh_city');
                $query->distinct('vendor_warehouse.id');
                $query->where('vendor_warehouse.vendor_id',$id);
            })
            ->when($stack=='Brand',function($query)use($id){
                $query->select('product_brands.brand_name','product_brands.id');
                $query->distinct('vendor_products.product_brand');
                $query->where('vendor_products.warehouse_id',$id);
            })
            ->when($stack=='Batch',function($query)use($id,$wareid){
                $query->select('vendor_products.batch','vendor_products.id');
                $query->distinct('vendor_products.batch');
                $query->where('vendor_products.product_brand',$id);
                $query->where('vendor_products.warehouse_id',$wareid);
            })
            ->when($stack=='Inventory',function($query)use($id){
                $query->select('vendor_product_details.inventory_id','vendor_product_details.id');
                $query->distinct('vendor_product_details.id');
                $query->where('vendor_product_details.vendor_products_id',$id);
            })
            ->where('vendor_products.product_id',$prodid)
            ->where('vendor_product_details.availability_status',0)
            ->get();
        return $availablevendor;
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
        // dd($request->all());
        DB::beginTransaction();
        try{
            $replacementdata = [
                'replacementdate'=>$request->get('orderdate'),
                'createdby'=>session('username')
            ];
            $basicorderdata = DB::table('del_orders')->where('del_orders.order_id',$request->get('baseorderid'))->first();
            $pickuporderid = DB::table('del_orders')->insertGetId([
                'status' => 'Assigned',
                'deliverypickup' => 'Pick Up',
                'DelAssignedTo' =>$request->get('orderassignedto'),
                'helpers' => json_encode($request->get('orderhelpers')),
                'lead_id'=>$basicorderdata->lead_id,
                'patient_name'=>$basicorderdata->patient_name,
                'shipping_first_name' => $basicorderdata->shipping_first_name,
                'location' =>$basicorderdata->location,
                'mobileno' => $basicorderdata->mobileno,
                'line_item_1' => 'Products',
                'DelDate' =>date('d-m-Y',strtotime($request->get('orderdate'))),
                'Pickup_Date' => $request->get('orderdate'),
                'TotalAmt' => 0,
                'flag'=>'Replacement',
                'PaymentMode'=>'Online',
                'fulldetails'=> $basicorderdata->fulldetails,
                'TravelMode' =>'Pending',
                'PickupLocation' =>'Customer',
                'order_approval_status' =>'Approved',
                'comment'=>'Replacement Order',
            ]);

            $deliveryorderid = DB::table('del_orders')->insertGetId([
                'status'=>'Assigned',
                'lead_id'=>$basicorderdata->lead_id,
                'web_order_id'=>$basicorderdata->web_order_id,
                'patient_name'=>$basicorderdata->patient_name,
                'vendor_id'=>$basicorderdata->vendor_id,
                'deliverypickup'=>'Delivery',
                'DelDate'=>date('d-m-Y',strtotime($request->get('orderdate'))),
                'location'=>$basicorderdata->location,
                'shipping_first_name'=>$basicorderdata->shipping_first_name,
                'cust_gender'=>$basicorderdata->cust_gender,
                'TotalAmt'=>0,
                'flag'=>'Replacement',
                'PaymentMode'=>'Online',
                'mobileno'=>$basicorderdata->mobileno,
                'DelAssignedTo'=>$request->get('orderassignedto'),
                'helpers' => json_encode($request->get('orderhelpers')),
                'TravelMode'=>'Pending',
                'order_approval_status'=>'Approved',
                'comment'=>'Replacement Order',
                'fulldetails'=>$basicorderdata->fulldetails,
                'line_item_1'=>'Products',
            ]);
            $replacementid = DB::table('replacement_table')->insertGetId([
                'replacementdate'=>$request->get('orderdate'),
                'pickuporderid'=>$pickuporderid,
                'deliveryorderid'=>$deliveryorderid,
                'createdby'=>session('username')
            ]);
            $pickuptotalamount = DB::table('order_details')->join('del_orders','del_orders.order_id','=','order_details.order_id')->whereIn('order_details.id',array_keys($request->get('products')))->sum('product_deposite');
            $deliverytotalamount = 0;

            foreach($request->get('products') as $orderdetailsid=>$newproductdetails){
                $orderproductdetails = DB::table('order_details')->join('del_orders','del_orders.order_id','=','order_details.order_id')->where('order_details.id',$orderdetailsid)->first();
                
                // Check for credit and debit notes amounts....
                $orderproductdetails->product_rent = RenewalPickupController::fetchCrDrData($orderproductdetails->id,'R');
                $orderproductdetails->product_deposite = RenewalPickupController::fetchCrDrData($orderproductdetails->id,'D');
                $orderproductdetails->transport = RenewalPickupController::fetchCrDrData($orderproductdetails->id,'T');

                // Adjusted amounts....
                $orderproductdetails->product_rent = $orderproductdetails->product_rent - DB::table('adjustment_table')->where('order_details_id',$orderdetailsid)->where('fromtype','R')->where('adjustment_table.flag','A')->sum('adjusted_amount');
                $orderproductdetails->product_deposite = $orderproductdetails->product_deposite - DB::table('adjustment_table')->where('order_details_id',$orderdetailsid)->where('fromtype','D')->where('adjustment_table.flag','A')->sum('adjusted_amount');

                $pickuprecord = DB::table('pickups')->insertGetId([
                    'pickup_order_id'=>$pickuporderid,
                    'del_order_id' => $orderproductdetails->order_id,
                    'order_details_id' => $orderproductdetails->id,
                    'lead_id' => $orderproductdetails->lead_id,
                    'vendor_id' => $orderproductdetails->vendor_id,
                    'product_id' => $orderproductdetails->product_id,
                    'pickup_date' => $request->get('orderdate'),
                    'cash_amount' =>0,
                    'online_amount' =>0,
                    'drop_vendor_id' =>$orderproductdetails->vendor_id,
                    'drop_warehouse_id'=>$newproductdetails['dropwarehouseid'],
                    'drop_vendor_product_id'=>0,
                    'drop_location'=>($newproductdetails['dropwarehousetype'] == 'Vendor Warehouse')?'Vendor':'Q5C',
                    'created_by' =>session('username')
                ]);
                if($newproductdetails['dropwarehousetype'] == 'Vendor Warehouse'){
                    EditOrderController::updateVendorInOutInventory($pickuprecord,'out');
                }
                $deliveryrecord = DB::table('order_details')->insertGetId([
                    'order_id'=> $deliveryorderid,
                    'customer_id'=>$orderproductdetails->customer_id,
                    'product_id'=>$newproductdetails['newproductid'],
                    'vendor_product_id'=>$newproductdetails['newbatchid'],
                    'vendor_id'=>$newproductdetails['newvendorid'],
                    'vendor_warehouse_id'=>$newproductdetails['newwarehouseid'],
                    'product_brand'=>$newproductdetails['newbrandid'],
                    'product_batch'=>$newproductdetails['newbatchid'],
                    'product_qty'=>1,
                    'months'=>1,
                    'product_rent'=>$newproductdetails['newproductrent'],
                    'product_deposite'=>$newproductdetails['newproductdeposit'],
                    'transport'=>$newproductdetails['newproducttransport'],
                    'sale_rental' =>'Rental',
                    'vendor_product_details_id' => $newproductdetails['newinventoryid'],
                    'unique_id'=> DB::table('vendor_product_details')->where('id',$newproductdetails['newinventoryid'])->first()->inventory_id,
                    'product_serial_nos' =>null,
                    'creation_date'=>$orderproductdetails->creation_date,
                    'pickup_date'=>$orderproductdetails->pickup_date,
                    'status'=>'Accepted',
                    'created_by'=>session('username'),
                ]);
                EditOrderController::updateVendorInOutInventory($deliveryrecord,'in');
                $deliverytotalamount += ($newproductdetails['newproductrent'] + $newproductdetails['newproductdeposit'] + $newproductdetails['newproducttransport']);

                // *** // Adjustment Section Here...... // *** //

                // ------------ Notations.... ------------- //
                /*
                    newproductadjustedrent0 : Adjusted Rent in Rent.
                    newproductadjusteddeposit0 : Adjusted Deposit in Rent.
                    newproductadjustedrent1 : Adjusted Rent in Deposit.
                    newproductadjusteddeposit1 : Adjusted Deposit in Deposit.
                    newproductadjustedrent2 : Adjusted Rent in Transport.
                    newproductadjusteddeposit2 : Adjusted Deposit in Transport.
                */

                if(isset($newproductdetails['newproductadjustedrent0']) && $orderproductdetails->product_rent !=0){
                    if($orderproductdetails->product_rent > $newproductdetails['newproductrent']){
                        // $pickuptotalamount -= $newproductdetails['newproductrent'];
                        $adjustedrent = $newproductdetails['newproductrent'];
                        $orderproductdetails->product_rent = $orderproductdetails->product_rent - $newproductdetails['newproductrent'];
                        $newproductdetails['newproductrent'] = 0;
                    }elseif($orderproductdetails->product_rent < $newproductdetails['newproductrent']){
                        // $deliverytotalamount += (($newproductdetails['newproductrent'] - $orderproductdetails->product_rent) + $newproductdetails['newproductrent'] + $newproductdetails['newproducttransport']);
                        // $pickuptotalamount -= $orderproductdetails->product_rent;
                        $adjustedrent = $orderproductdetails->product_rent;
                        $newproductdetails['newproductrent'] = $newproductdetails['newproductrent'] - $orderproductdetails->product_rent;
                        $orderproductdetails->product_rent = 0;
                    }
                    else{
                        // $pickuptotalamount -= $newproductdetails['newproductrent'];
                        $adjustedrent = $newproductdetails['newproductrent'];
                        $orderproductdetails->product_rent = 0;
                        $newproductdetails['newproductrent'] = 0;
                    }
                    // dd($adjustedrent);
                    // Adjustment Details......
                    DB::table('adjustment_table')->insert(
                        [
                            "product_id"=>$orderproductdetails->product_id,
                            "order_id"=>$deliveryorderid,
                            "order_details_id"=>$orderproductdetails->id,
                            "adjusted_order_details_id"=>$deliveryrecord,
                            "fromorderid"=>$orderproductdetails->order_id,
                            "adjusted_amount"=>$adjustedrent,
                            "fromtype"=>'R',
                            "intype"=>'R'
                        ]
                    );
                }
                if(isset($newproductdetails['newproductadjusteddeposit0']) && $orderproductdetails->product_deposite !=0){
                    if($orderproductdetails->product_deposite > $newproductdetails['newproductrent']){
                        // $pickuptotalamount -= $newproductdetails['newproductrent'];
                        $adjustedrent = $newproductdetails['newproductrent'];
                        $orderproductdetails->product_deposite = $orderproductdetails->product_deposite - $newproductdetails['newproductrent'];
                        $newproductdetails['newproductrent'] = 0;
                    }elseif($orderproductdetails->product_deposite < $newproductdetails['newproductrent']){
                        // $deliverytotalamount += (($newproductdetails['newproductrent'] - $orderproductdetails->product_deposite) + $newproductdetails['newproductrent'] + $newproductdetails['newproducttransport']);
                        $pickuptotalamount -= $orderproductdetails->product_deposite;
                        $adjustedrent = $orderproductdetails->product_deposite;
                        $newproductdetails['newproductrent'] = $newproductdetails['newproductrent'] - $orderproductdetails->product_deposite;
                        $orderproductdetails->product_deposite = 0;
                    }
                    else{
                        $pickuptotalamount -= $newproductdetails['newproductrent'];
                        $adjustedrent = $newproductdetails['newproductrent'];
                        $orderproductdetails->product_deposite = 0;
                        $newproductdetails['newproductrent'] = 0;
                    }
                    // Adjustment Details......
                    DB::table('adjustment_table')->insert(
                        [
                            "product_id"=>$orderproductdetails->product_id,
                            "order_id"=>$deliveryorderid,
                            "order_details_id"=>$orderproductdetails->id,
                            "adjusted_order_details_id"=>$deliveryrecord,
                            "fromorderid"=>$orderproductdetails->order_id,
                            "adjusted_amount"=>$adjustedrent,
                            "fromtype"=>'D',
                            "intype"=>'R'
                        ]
                    );
                }
                if(isset($newproductdetails['newproductadjustedrent1']) && $orderproductdetails->product_rent !=0){
                    if($orderproductdetails->product_rent > $newproductdetails['newproductdeposit']){
                        // $pickuptotalamount -= $newproductdetails['newproductdeposit'];
                        $adjusteddeposit = $newproductdetails['newproductdeposit'];
                        $orderproductdetails->product_rent = $orderproductdetails->product_rent - $newproductdetails['newproductdeposit'];
                        $newproductdetails['newproductdeposit'] = 0;
                    }elseif($orderproductdetails->product_rent < $newproductdetails['newproductdeposit']){
                        // $deliverytotalamount += (($newproductdetails['newproductdeposit'] - $orderproductdetails->product_rent) + $newproductdetails['newproductdeposit'] + $newproductdetails['newproducttransport']);
                        // $pickuptotalamount -= $orderproductdetails->product_rent;
                        $adjusteddeposit = $orderproductdetails->product_rent;
                        $newproductdetails['newproductdeposit'] = $newproductdetails['newproductdeposit'] - $orderproductdetails->product_rent;
                        $orderproductdetails->product_rent = 0;
                    }
                    else{
                        // $pickuptotalamount -= $newproductdetails['newproductdeposit'];
                        $adjusteddeposit = $newproductdetails['newproductdeposit'];
                        $newproductdetails['newproductdeposit'] = 0;
                        $orderproductdetails->product_rent = 0;
                    }
                    // Adjustment Details......
                    DB::table('adjustment_table')->insert(
                        [
                            "product_id"=>$orderproductdetails->product_id,
                            "order_id"=>$deliveryorderid,
                            "order_details_id"=>$orderproductdetails->id,
                            "adjusted_order_details_id"=>$deliveryrecord,
                            "fromorderid"=>$orderproductdetails->order_id,
                            "adjusted_amount"=>$adjusteddeposit,
                            "fromtype"=>'R',
                            "intype"=>'D'
                        ]
                    );
                }
                if(isset($newproductdetails['newproductadjusteddeposit1']) && $orderproductdetails->product_deposite !=0){
                    if($orderproductdetails->product_deposite > $newproductdetails['newproductdeposit']){
                        $pickuptotalamount -= $newproductdetails['newproductdeposit'];
                        $adjusteddeposit = $newproductdetails['newproductdeposit'];
                        $orderproductdetails->product_deposite = $orderproductdetails->product_deposite - $newproductdetails['newproductdeposit'];
                        $newproductdetails['newproductdeposit'] = 0;
                    }elseif($orderproductdetails->product_deposite < $newproductdetails['newproductdeposit']){
                        // $deliverytotalamount += (($newproductdetails['newproductdeposit'] - $orderproductdetails->product_deposite) + $newproductdetails['newproductdeposit'] + $newproductdetails['newproducttransport']);
                        $pickuptotalamount -= $orderproductdetails->product_deposite;
                        $adjusteddeposit = $orderproductdetails->product_deposite;
                        $newproductdetails['newproductdeposit'] = $newproductdetails['newproductdeposit'] - $orderproductdetails->product_deposite;
                        $orderproductdetails->product_deposite = 0;
                    }
                    else{
                        $pickuptotalamount -= $newproductdetails['newproductdeposit'];
                        $adjusteddeposit = $newproductdetails['newproductdeposit'];
                        $orderproductdetails->product_deposite = 0;
                        $newproductdetails['newproductdeposit'] = 0;
                    }
                    // Adjustment Details......
                    DB::table('adjustment_table')->insert(
                        [
                            "product_id"=>$orderproductdetails->product_id,
                            "order_id"=>$deliveryorderid,
                            "order_details_id"=>$orderproductdetails->id,
                            "adjusted_order_details_id"=>$deliveryrecord,
                            "fromorderid"=>$orderproductdetails->order_id,
                            "adjusted_amount"=>$adjusteddeposit,
                            "fromtype"=>'D',
                            "intype"=>'D'
                        ]
                    );
                }
                if(isset($newproductdetails['newproductadjustedrent2']) && $orderproductdetails->product_rent !=0){
                    if($orderproductdetails->product_rent > $newproductdetails['newproducttransport']){
                        // $pickuptotalamount -= $newproductdetails['newproducttransport'];
                        $adjustedtransport = $newproductdetails['newproducttransport'];
                        $orderproductdetails->product_rent = $orderproductdetails->product_rent - $newproductdetails['newproducttransport'];
                        $newproductdetails['newproducttransport'] = 0;
                    }elseif($orderproductdetails->product_rent < $newproductdetails['newproducttransport']){
                        // $deliverytotalamount += (($newproductdetails['newproducttransport'] - $orderproductdetails->product_rent) + $newproductdetails['newproducttransport'] + $newproductdetails['newproducttransport']);
                        // $pickuptotalamount -= $orderproductdetails->product_rent;
                        $adjustedtransport = $orderproductdetails->product_rent;
                        $newproductdetails['newproducttransport'] = $newproductdetails['newproducttransport'] - $orderproductdetails->product_rent;
                        $orderproductdetails->product_rent = 0;
                    }
                    else{
                        // $pickuptotalamount -= $newproductdetails['newproducttransport'];
                        $adjustedtransport = $newproductdetails['newproducttransport'];
                        $orderproductdetails->product_rent = 0;
                        $newproductdetails['newproducttransport'] = 0;
                    }
                    // Adjustment Details......
                    DB::table('adjustment_table')->insert(
                        [
                            "product_id"=>$orderproductdetails->product_id,
                            "order_id"=>$deliveryorderid,
                            "order_details_id"=>$orderproductdetails->id,
                            "adjusted_order_details_id"=>$deliveryrecord,
                            "fromorderid"=>$orderproductdetails->order_id,
                            "adjusted_amount"=>$adjustedtransport,
                            "fromtype"=>'R',
                            "intype"=>'T'
                        ]
                    );
                }
                if(isset($newproductdetails['newproductadjusteddeposit2']) && $orderproductdetails->product_deposite !=0){
                    if($orderproductdetails->product_deposite > $newproductdetails['newproducttransport']){
                        $pickuptotalamount -= $newproductdetails['newproducttransport'];
                        $adjustedtransport = $newproductdetails['newproducttransport'];
                        $orderproductdetails->product_deposite = $orderproductdetails->product_deposite - $newproductdetails['newproducttransport'];
                        $newproductdetails['newproducttransport'] = 0;
                    }elseif($orderproductdetails->product_deposite < $newproductdetails['newproducttransport']){
                        // $deliverytotalamount += (($newproductdetails['newproducttransport'] - $orderproductdetails->product_deposite) + $newproductdetails['newproducttransport'] + $newproductdetails['newproducttransport']);
                        $pickuptotalamount -= $orderproductdetails->product_deposite;
                        $adjustedtransport = $orderproductdetails->product_deposite;
                        $newproductdetails['newproducttransport'] = $newproductdetails['newproducttransport'] - $orderproductdetails->product_deposite;
                        $orderproductdetails->product_deposite = 0;
                    }
                    else{
                        $pickuptotalamount -= $newproductdetails['newproducttransport'];
                        $adjustedtransport = $newproductdetails['newproducttransport'];
                        $orderproductdetails->product_deposite = 0;
                        $newproductdetails['newproducttransport'] = 0;
                    }
                    // Adjustment Details......
                    DB::table('adjustment_table')->insert(
                        [
                            "product_id"=>$orderproductdetails->product_id,
                            "order_id"=>$deliveryorderid,
                            "order_details_id"=>$orderproductdetails->id,
                            "adjusted_order_details_id"=>$deliveryrecord,
                            "fromorderid"=>$orderproductdetails->order_id,
                            "adjusted_amount"=>$adjustedtransport,
                            "fromtype"=>'D',
                            "intype"=>'T'
                        ]
                    );
                }
            }
            DB::table('del_orders')->where('order_id',$deliveryorderid)->update(['TotalAmt'=>$deliverytotalamount]);
            DB::table('del_orders')->where('order_id',$pickuporderid)->update(['TotalAmt'=>$pickuptotalamount]);
            $staffContact = config('app.it_head_contact');
            $orderid = $deliveryorderid.', '.$pickuporderid;
            $date = date('d-M-y',strtotime($request->get('orderdate')));
            $customername = $basicorderdata->shipping_first_name;
            $contactno = $basicorderdata->mobileno;
            // dd("No Error");
            $this->sendWpSms($staffContact,$orderid,$date,$customername,$contactno);
            DB::commit();
            return redirect()->route('pendingPaymentOrder',['filter_customer_name'=>$basicorderdata->shipping_first_name,'filter_from_date'=>$request->get('orderdate'),'filter_end_date'=>$request->get('orderdate')])->with('message','Replacement Order Generated!');
        }catch(Exception $e){
            DB::rollback();
            // return redirect()->back()->with('error',$e->getMessage());
            dd($e);
        }
    }
    function sendWpSms($staffContact,$orderid,$date,$customername,$contactno){
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
        print_r($data =[
            "portno"=>"11140",
            "namespace"=>"b9a23cb4_89ed_4fe2_b849_20775908ff5e",
            "countrycode"=> "91",
            "mobileno"=> "$staffContact",            
            "templatename" => "order_integration_error",
            "templateparams" => [
                ["type"=> "text","text"=> "Replacement Order Generated($orderid)"],
                ["type"=> "text","text"=> "$date"],
                ["type"=> "text","text"=> "$customername"],
                ["type"=> "text","text"=> "$contactno"],
                ["type"=> "text","text"=> "-"],
            ],
        ]);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        
        $resp = curl_exec($curl);
        // dd($resp);
        print_r($resp);
        curl_close($curl);
        return null;
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
