<?php

namespace App\Http\Controllers\AppApiV2;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AppApi\AuthController;
use App\Http\Controllers\Leads\LeadController;
use App\Models\ShortUrl;

class OrdersController extends Controller
{
    //
    protected $startDate,$endDate,$incompleteOrderStatus,$completedOrderStatus,$cancelOrderStatus,$orderType,$receiptImagePath,$productImagePath,$ownLocations,$transportMediums;

    public function __construct(){
        // $this->startDate = Carbon::now()->subMonth()->toDateString();
        // $this->startDate = Carbon::createFromFormat('Y-m-d', Carbon::now()->subDays('120')->toDateString())
        // ->format('d-m-Y');
        $this->startDate = Carbon::createFromFormat('Y-m-d', Carbon::now()->subMonth()->toDateString())
        ->format('d-m-Y');
        // $this->endDate = Carbon::now()->toDateString();
        $this->endDate = Carbon::createFromFormat('Y-m-d', Carbon::now()->toDateString())
        ->format('d-m-Y');
        $this->incompleteOrderStatus = ['Assigned','Accepted','InProgress'];
        $this->completedOrderStatus = ['Delivered','Collected','Picked up','Completed','Closed'];
        $this->cancelOrderStatus = ['Cancel','Cust Rejected','Rejected'];
        $this->orderType = ['Delivery','Pick Up','Collection','Repair','Shifting'];
        $this->receiptImagePath = "http://intra.quali55care.com/".config('app.app_env')."/eflow/storage/receiptImages/";
        $this->productImagePath = "http://intra.quali55care.com/".config('app.app_env')."/eflow/storage/deliveredProductImages/";
        $this->customerSignatureImagePath = "http://intra.quali55care.com/".config('app.app_env')."/eflow/storage/customerSignatures/";
        $this->ownLocation = ['Office-Mumbai','Office-Pune','Office-Gurgaon','Office-Delhi'];
        $this->transportMediums = (object)["Bike", "Boat", "Metro", "OLA", "Porter", "Rickshaw", "Taxi", "Tempo", "Train"];

    }

    public function dashboard(Request $request){

        $dashboardData = DB::table('del_orders')
            ->select('deliverypickup','status','DelDate','shipping_first_name','DelAssignedTo','order_id','mobileno','TotalAmt','line_item_1')
            ->whereBetween(DB::raw("STR_TO_DATE(DelDate,'%d-%m-%Y')"),[DB::raw("STR_TO_DATE('$this->startDate','%d-%m-%Y')"),DB::raw("STR_TO_DATE('$this->endDate','%d-%m-%Y')")])
            ->when($request->get('defrole') == 'user',function($query)use($request){
                $query->where('DelAssignedTo',$request->get('defusername'));
            })
            ->orderBy(DB::raw("STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y')"),'DESC')
            ->get();

        $today = Carbon::createFromFormat('Y-m-d', Carbon::today()->toDateString())
        ->format('d-m-Y');
        $yesterday = Carbon::createFromFormat('Y-m-d', Carbon::yesterday()->toDateString())
        ->format('d-m-Y');
        $b4yday = Carbon::createFromFormat('Y-m-d', Carbon::now()->subDays(2)->toDateString())
        ->format('d-m-Y');
        $week = Carbon::createFromFormat('Y-m-d', Carbon::now()->subDays(7)->toDateString())
        ->format('d-m-Y');
        $response['defrole'] = $request->get('defrole');
        $response['defusername'] = $request->get('defusername');
        $response['today'] = [
            'incomplete'=>DB::table('del_orders')
            ->select('deliverypickup','status','DelDate','shipping_first_name','DelAssignedTo','order_id','mobileno','TotalAmt','line_item_1')
            ->whereBetween(DB::raw("STR_TO_DATE(DelDate,'%d-%m-%Y')"),[DB::raw("STR_TO_DATE('$this->startDate','%d-%m-%Y')"),DB::raw("STR_TO_DATE('$this->endDate','%d-%m-%Y')")])
            ->when($request->get('defrole') == 'user',function($query)use($request){
                $query->where('DelAssignedTo',$request->get('defusername'));
            })->where(DB::raw("STR_TO_DATE(DelDate,'%d-%m-%Y')"),DB::raw("STR_TO_DATE('$today','%d-%m-%Y')"))->whereIn('status',$this->incompleteOrderStatus)->count(),

            'total'=>DB::table('del_orders')
            ->select('deliverypickup','status','DelDate','shipping_first_name','DelAssignedTo','order_id','mobileno','TotalAmt','line_item_1')
            ->whereBetween(DB::raw("STR_TO_DATE(DelDate,'%d-%m-%Y')"),[DB::raw("STR_TO_DATE('$this->startDate','%d-%m-%Y')"),DB::raw("STR_TO_DATE('$this->endDate','%d-%m-%Y')")])
            ->when($request->get('defrole') == 'user',function($query)use($request){
                $query->where('DelAssignedTo',$request->get('defusername'));
            })->where(DB::raw("STR_TO_DATE(DelDate,'%d-%m-%Y')"),DB::raw("STR_TO_DATE('$today','%d-%m-%Y')"))->whereNotIn('status',$this->cancelOrderStatus)->count(),
        ];
        $response['yesterday'] = [
            'incomplete'=>DB::table('del_orders')
            ->select('deliverypickup','status','DelDate','shipping_first_name','DelAssignedTo','order_id','mobileno','TotalAmt','line_item_1')
            ->whereBetween(DB::raw("STR_TO_DATE(DelDate,'%d-%m-%Y')"),[DB::raw("STR_TO_DATE('$this->startDate','%d-%m-%Y')"),DB::raw("STR_TO_DATE('$this->endDate','%d-%m-%Y')")])
            ->when($request->get('defrole') == 'user',function($query)use($request){
                $query->where('DelAssignedTo',$request->get('defusername'));
            })->where(DB::raw("STR_TO_DATE(DelDate,'%d-%m-%Y')"),DB::raw("STR_TO_DATE('$yesterday','%d-%m-%Y')"))->whereIn('status',$this->incompleteOrderStatus)->count(),

            'total'=>DB::table('del_orders')
            ->select('deliverypickup','status','DelDate','shipping_first_name','DelAssignedTo','order_id','mobileno','TotalAmt','line_item_1')
            ->whereBetween(DB::raw("STR_TO_DATE(DelDate,'%d-%m-%Y')"),[DB::raw("STR_TO_DATE('$this->startDate','%d-%m-%Y')"),DB::raw("STR_TO_DATE('$this->endDate','%d-%m-%Y')")])
            ->when($request->get('defrole') == 'user',function($query)use($request){
                $query->where('DelAssignedTo',$request->get('defusername'));
            })->where(DB::raw("STR_TO_DATE(DelDate,'%d-%m-%Y')"),DB::raw("STR_TO_DATE('$yesterday','%d-%m-%Y')"))->whereNotIn('status',$this->cancelOrderStatus)->count(),
        ];
        $response['b4yday'] = [
            'incomplete'=>DB::table('del_orders')
            ->select('deliverypickup','status','DelDate','shipping_first_name','DelAssignedTo','order_id','mobileno','TotalAmt','line_item_1')
            ->whereBetween(DB::raw("STR_TO_DATE(DelDate,'%d-%m-%Y')"),[DB::raw("STR_TO_DATE('$this->startDate','%d-%m-%Y')"),DB::raw("STR_TO_DATE('$this->endDate','%d-%m-%Y')")])
            ->when($request->get('defrole') == 'user',function($query)use($request){
                $query->where('DelAssignedTo',$request->get('defusername'));
            })->where(DB::raw("STR_TO_DATE(DelDate,'%d-%m-%Y')"),DB::raw("STR_TO_DATE('$b4yday','%d-%m-%Y')"))->whereIn('status',$this->incompleteOrderStatus)->count(),

            'total'=>DB::table('del_orders')
            ->select('deliverypickup','status','DelDate','shipping_first_name','DelAssignedTo','order_id','mobileno','TotalAmt','line_item_1')
            ->whereBetween(DB::raw("STR_TO_DATE(DelDate,'%d-%m-%Y')"),[DB::raw("STR_TO_DATE('$this->startDate','%d-%m-%Y')"),DB::raw("STR_TO_DATE('$this->endDate','%d-%m-%Y')")])
            ->when($request->get('defrole') == 'user',function($query)use($request){
                $query->where('DelAssignedTo',$request->get('defusername'));
            })->where(DB::raw("STR_TO_DATE(DelDate,'%d-%m-%Y')"),DB::raw("STR_TO_DATE('$b4yday','%d-%m-%Y')"))->whereNotIn('status',$this->cancelOrderStatus)->count(),
        ];
        $response['week'] = [
            'incomplete'=>DB::table('del_orders')
            ->select('deliverypickup','status','DelDate','shipping_first_name','DelAssignedTo','order_id','mobileno','TotalAmt','line_item_1')
            ->whereBetween(DB::raw("STR_TO_DATE(DelDate,'%d-%m-%Y')"),[DB::raw("STR_TO_DATE('$this->startDate','%d-%m-%Y')"),DB::raw("STR_TO_DATE('$this->endDate','%d-%m-%Y')")])
            ->when($request->get('defrole') == 'user',function($query)use($request){
                $query->where('DelAssignedTo',$request->get('defusername'));
            })->whereBetween(DB::raw("STR_TO_DATE(DelDate,'%d-%m-%Y')"),[DB::raw("STR_TO_DATE('$week','%d-%m-%Y')"),DB::raw("STR_TO_DATE('$today','%d-%m-%Y')")])->whereIn('status',$this->incompleteOrderStatus)->count(),
            
            'total'=>DB::table('del_orders')
            ->select('deliverypickup','status','DelDate','shipping_first_name','DelAssignedTo','order_id','mobileno','TotalAmt','line_item_1')
            ->whereBetween(DB::raw("STR_TO_DATE(DelDate,'%d-%m-%Y')"),[DB::raw("STR_TO_DATE('$this->startDate','%d-%m-%Y')"),DB::raw("STR_TO_DATE('$this->endDate','%d-%m-%Y')")])
            ->when($request->get('defrole') == 'user',function($query)use($request){
                $query->where('DelAssignedTo',$request->get('defusername'));
            })->whereBetween(DB::raw("STR_TO_DATE(DelDate,'%d-%m-%Y')"),[DB::raw("STR_TO_DATE('$week','%d-%m-%Y')"),DB::raw("STR_TO_DATE('$today','%d-%m-%Y')")])->whereNotIn('status',$this->cancelOrderStatus)->count(),
        ];

        $response['assigned'] = ['count'=>$dashboardData->where('status','Assigned')->count()];
        $response['accepted'] = ['count'=>$dashboardData->where('status','Accepted')->count()];
        $response['inprogress'] = ['count'=>$dashboardData->where('status','InProgress')->count()];
        $response['pending'] = ['count'=>$dashboardData->where('status','Pending')->count()];
        $response['cancelled'] = ['count'=>$dashboardData->whereIn('status',$this->cancelOrderStatus)->count()];
        $response['completed'] = ['count'=>$dashboardData->whereIn('status',$this->completedOrderStatus)->count()];
        $response['pendingOrders'] = array_values($dashboardData->whereNotIn('status',$this->cancelOrderStatus)->whereNotIn('status',$this->completedOrderStatus)->toArray());
        // return $dashboardData->whereNotIn('status',$this->cancelOrderStatus)->whereNotIn('status',$this->completedOrderStatus)->toArray();
        // return $dashboardData;
        return json_encode(['status'=>'success','description'=>'found','resp'=>$response]);
    }

    public function fetchOrders(Request $request){
        if($request->get('flag') != "summary")
        {
            $response['orders'] = DB::table('del_orders')
            ->select('deliverypickup','status','DelDate','shipping_first_name','DelAssignedTo','order_id','mobileno','TotalAmt','line_item_1')
            ->whereBetween(DB::raw("STR_TO_DATE(DelDate,'%d-%m-%Y')"),[DB::raw("STR_TO_DATE('$this->startDate','%d-%m-%Y')"),DB::raw("STR_TO_DATE('$this->endDate','%d-%m-%Y')")])
            ->when($request->get('defrole') == 'user',function($query)use($request){
                $query->where(function($q)use($request){
                    $q->where('DelAssignedTo',$request->get('defusername'));
                    $q->orWhere('del_orders.helpers','LIKE','%'.$request->get('defusername').'%');
                });
            })
            ->when($request->get('status'),function($query)use($request){
                if($request->get('status') == 'Cancelled' || in_array($request->get('status'),$this->cancelOrderStatus)){
                    $query->whereIn('status',$this->cancelOrderStatus);
                }elseif($request->get('status') == 'Completed' || in_array($request->get('status'),$this->completedOrderStatus)){
                    $query->whereIn('status',$this->completedOrderStatus);
                }
                else{
                    $query->where("status",$request->get('status'));
                }
            })
            ->orderBy(DB::raw("STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y')"),'DESC')
            ->paginate(10);    
        }else{
            if($request->get('type') == 'Pending'){
                $response['orders'] = DB::table('del_orders')->select('order_id')
                ->whereNotIn('status',$this->cancelOrderStatus)
                ->whereNotIn('status',$this->completedOrderStatus)
                ->select('deliverypickup','status','DelDate','shipping_first_name','DelAssignedTo','order_id','mobileno','TotalAmt','line_item_1')
                ->when($request->get('period'), function($query)use($request){
                    if($request->get('period') == 'Today'){
                        $today = date('d-m-Y');
                        $query->where(DB::raw("STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y')"),DB::raw("STR_TO_DATE('$today','%d-%m-%Y')"));
                    }else if($request->get('period') == 'Week'){
                        $today = date('d-m-Y');
                        $last7days = date('d-m-Y',strtotime("-7 days"));
                        $query->whereBetween (DB::raw("STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y')"),[DB::raw("STR_TO_DATE('$last7days','%d-%m-%Y')"),DB::raw("STR_TO_DATE('$today','%d-%m-%Y')")]);
                    }else if($request->get('period') == 'Month'){
                        $today = date('d-m-Y');
                        $month1stday = "01-".date('m-Y');
                        $query->whereBetween (DB::raw("STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y')"),[DB::raw("STR_TO_DATE('$month1stday','%d-%m-%Y')"),DB::raw("STR_TO_DATE('$today','%d-%m-%Y')")]);
                    }else if($request->get('period') == 'Custom Range'){
                        $fromDate = date('d-m-Y',($request->get('fromDate') / 1000));
                        $toDate = date('d-m-Y',($request->get('toDate') / 1000));
                        $query->whereBetween (DB::raw("STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y')"),[DB::raw("STR_TO_DATE('$fromDate','%d-%m-%Y')"),DB::raw("STR_TO_DATE('$toDate','%d-%m-%Y')")]);
                    }else{

                    }
                })
                ->when($request->get('staff'), function($query)use($request){
                    if($request->get('staff') != "All"){
                        $query->where('del_orders.DelAssignedTo',$request->get('staff'));
                    }
                })
                ->orderBy(DB::raw("STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y')"),'DESC')
                ->paginate(10);
            }else{
                $summaryReport = DB::table('del_orders')
                    ->join('activity_log','activity_log.key_id','=','del_orders.order_id')
                    ->select('del_orders.order_id','activity_log.fields','activity_log.new_value')
                    ->whereIn('del_orders.status',$this->completedOrderStatus)
                    ->whereIn('activity_log.operation',['Order Assigned','Order Completed'])
                    ->whereIn('activity_log.fields',['assign_at','delivered_at'])
                    ->when($request->get('period'), function($query)use($request){
                        if($request->get('period') == 'Today'){
                            $today = date('d-m-Y');
                            $query->where(DB::raw("STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y')"),DB::raw("STR_TO_DATE('$today','%d-%m-%Y')"));
                        }else if($request->get('period') == 'Week'){
                            $today = date('d-m-Y');
                            $last7days = date('d-m-Y',strtotime("-7 days"));
                            $query->whereBetween (DB::raw("STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y')"),[DB::raw("STR_TO_DATE('$last7days','%d-%m-%Y')"),DB::raw("STR_TO_DATE('$today','%d-%m-%Y')")]);
                        }else if($request->get('period') == 'Month'){
                            $today = date('d-m-Y');
                            $month1stday = "01-".date('m-Y');
                            $query->whereBetween (DB::raw("STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y')"),[DB::raw("STR_TO_DATE('$month1stday','%d-%m-%Y')"),DB::raw("STR_TO_DATE('$today','%d-%m-%Y')")]);
                        }else if($request->get('period') == 'Custom Range'){
                            $fromDate = date('d-m-Y',($request->get('fromDate') / 1000));
                            $toDate = date('d-m-Y',($request->get('toDate') / 1000));
                            $query->whereBetween (DB::raw("STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y')"),[DB::raw("STR_TO_DATE('$fromDate','%d-%m-%Y')"),DB::raw("STR_TO_DATE('$toDate','%d-%m-%Y')")]);
                        }else{

                        }
                    })
                    ->when($request->get('staff'), function($query)use($request){
                        if($request->get('staff') != "All"){
                            $query->where('del_orders.DelAssignedTo',$request->get('staff'));
                        }
                    })
                    ->get();
                // return $summaryReport;
                // return $summaryReport->groupBy('order_id');
                $ordersList = array();
                if($request->get('type') != "Total"){
                    $summaryReport = $summaryReport->groupBy('order_id');
                    foreach($summaryReport as $key=>$summary){
                        // return $summary;
                        $completedAt = Carbon::parse(date('d-M-y H:i:s',strtotime($summary[count($summary)-1]->new_value)));
                        $assignedAt = Carbon::parse(date('d-M-y H:i:s',strtotime($summary[0]->new_value)));
        
                        $diff = $completedAt->diffInSeconds($assignedAt);
                        if(date('H:i:s',strtotime(gmdate('H:i:s', $diff))) >= date('H:i:s',strtotime('04:00:00')))
                        {
                            if($request->get('type') != 'Delayed'){
                                unset($summaryReport[$key]);
                            }
                            // $resp['delay'] = $resp['delay'] + 1;
                        }
                        elseif(date('H:i:s',strtotime(gmdate('H:i:s', $diff))) <= date('H:i:s',strtotime('01:15:00')))
                        {
                            // $resp['exception'] = $resp['exception'] + 1;
                            if($request->get('type') != 'Exception'){
                                unset($summaryReport[$key]);
                            }
                        }
                        else
                        {
                            if($request->get('type') != 'OnTime'){
                                unset($summaryReport[$key]);
                            }
                            // $resp['ontime'] = $resp['ontime'] + 1;
                        }
                        // return $diff;
                    }
                    $ordersList = array_keys($summaryReport->toArray());
                }else{
                    $ordersList = $summaryReport->pluck('order_id')->toArray();
                }
                $response['orders'] = DB::table('del_orders')
                ->select('deliverypickup','status','DelDate','shipping_first_name','DelAssignedTo','order_id','mobileno','TotalAmt','line_item_1')
                ->whereIn('del_orders.order_id',$ordersList)
                ->orderBy(DB::raw("STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y')"),'DESC')
                ->paginate(10);
            }
        }
        return json_encode(['status'=>'success','description'=>'found','resp'=>$response]);
    }
    public function fetchOrderDetails(Request $request){
        $response['basicDetails'] = DB::table('del_orders')->where('del_orders.order_id',$request->get('orderId'))->first();
        if($response['basicDetails']->deliverypickup == 'Delivery'){
            $response['details'] = DB::table('order_details')
                ->join('products','products.id','=','order_details.product_id')
                ->select('order_details.*','products.product_name')
                ->where('order_details.order_id',$request->get('orderId'))
                ->whereNotIn('current_status',['Cancel'])
                ->get();
        }
        elseif($response['basicDetails']->deliverypickup == 'Collection'){
            $response['details'] = DB::table('renewals')
                ->join('order_details','order_details.id','=','renewals.order_details_id')
                ->join('products','products.id','=','renewals.product_id')
                ->select('order_details.*','products.product_name')
                ->where('renewals.collection_order_id',$request->get('orderId'))
                ->whereNotIn('renewals.status',['Cancel'])
                ->get();
        }
        elseif($response['basicDetails']->deliverypickup == 'Pick Up'){
            $response['details'] = DB::table('pickups')
                ->join('order_details','order_details.id','=','pickups.order_details_id')
                ->join('products','products.id','=','pickups.product_id')
                ->select('order_details.*','products.product_name')
                ->where('pickups.pickup_order_id',$request->get('orderId'))
                // ->whereNotNull('pickups.status')
                ->get();
        }
        else{
            $response['details'] = DB::table('maintenance_orders')
            ->join('order_details','order_details.id','=','maintenance_orders.order_details_id')
            ->join('products','products.id','=','maintenance_orders.product_id')
            ->select('order_details.*','products.product_name')
            ->where('maintenance_orders.order_id',$request->get('orderId'))
            ->where('maintenance_orders.flag','Active')
            ->get();
        }
        if($response['basicDetails']->helpers != '[No helper]'){
            $response['basicDetails']->helpers = implode(', ',json_decode($response['basicDetails']->helpers));
        }else{
            $response['basicDetails']->helpers = "No Helper";
        }
        $productDelivered = json_decode($response['basicDetails']->product_delivered);
        $response['productDelivered1'] = null;
        $response['productDelivered2'] = null;
        $response['productDelivered3'] = null;
        if($productDelivered){
            $response['productDelivered1'] = $productDelivered[0];
            $response['productDelivered2'] = $productDelivered[1];
            $response['productDelivered3'] = $productDelivered[2];
        }
        return json_encode(['status'=>'success','description'=>'found','resp'=>$response]);
    }

    public function updateStatus(Request $request){
        Db::beginTransaction();
        try{
            $oldStatus = DB::table('del_orders')->where('order_id',$request->get('orderId'))->first()->status;
            DB::table('activity_log')->insert([
                'order_type' => 'AL',
                'key_id' => $request->get('orderId'),
                'operation'=>"Update Status",
                'fields'=>'status',
                'old_value'=>$oldStatus,
                'new_value'=>$request->get('status'),
                'updated_by'=>$request->get('username')
            ]);
            DB::table('del_orders')->where('order_id',$request->get('orderId'))->update(['status'=>$request->get('status')]);
            DB::commit();
            return json_encode(['status'=>'success','description'=>'found','resp'=>"success"]);
        }catch(Exception $e){
            DB::rollback();
            return json_encode(['status'=>'error','description'=>'not found','resp'=>$e->getMessage()]);
        }
    }

    public function uploadImage(Request $request){
        return json_encode(['status'=>'success','description'=>'found','resp'=>"success"]);
    }
    public function updatePayment(Request $request){
        DB::beginTransaction();
        try{
            $orderDetails = DB::table('del_orders')->where('order_id',$request->get('orderId'))->first();
            $updateData = array();
            if($request->get('receiptImage')){
                $image = $request->get('receiptImage');  // your base64 encoded
                $image = str_replace('data:image/jpg;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageName = $request->get('orderId').'Receipt.'.'jpg';
                \File::put(storage_path(). '/receiptImages/' . $imageName, base64_decode($image));
                $updateData['del_receipt_image'] = $this->receiptImagePath. $imageName;
            }
            $updateData['del_payment_mode'] = $request->get('paymentMode');
            $updateData['del_cash_amount'] = $request->get('cashAmount')?$request->get('cashAmount'):0;
            $updateData['del_online_amount'] = $request->get('onlineAmount')?$request->get('onlineAmount'):0;
            $updateData['del_total_amount'] = ($request->get('cashAmount')?$request->get('cashAmount'):0) + (($request->get('onlineAmount'))?$request->get('onlineAmount'):0);
            // ******* Order Completion status ******* //
            //                                         //
            //  $updateData['status'] = ($orderDetails->status == 'Delivery'?'Delivered':($orderDetails->status == 'Collection'?'Collected':($orderDetails->status == 'Pick Up'?'Picked up':'Completed')));
            //                                         //
            // ******* Order Completion status ******* //
            if($orderDetails->deliverypickup == 'Collection' && $orderDetails->ccadflag !="CCAD"){
                $collectionDetails = DB::table('renewals')->where('collection_order_id',$request->get('orderId'))->whereNotIn('status',['Cancel'])->orderBy('id','ASC')->get();
                foreach($collectionDetails as $key=>$value){
                    DB::table('order_details')->where('id',$value->order_details_id)->update(['pickup_date'=>$value->end_date,'current_status'=>'Renewed']);
                }
                $updateData['status'] = 'Collected';
            }
            if($orderDetails->deliverypickup == 'Collection' || $orderDetails->deliverypickup == 'Repair' || $orderDetails->deliverypickup == 'Install' || $orderDetails->deliverypickup == 'Shifting'){
                if($orderDetails->deliverypickup == 'Collection'){
                    $updateData['status'] = 'Collected';
                    $type = "CO";
                }else{
                    $updateData['status'] = 'Completed';
                    $type = "OT";
                }
                DB::table('activity_log')->insert(
                    [
                        'order_type'=>$type,
                        'key_id'=>$orderDetails->order_id,
                        'operation'=>"Order Completed",
                        'fields'=>'delivered_at',
                        'old_value'=>'',
                        'new_value'=>date('Y-m-d H:i:m'),
                        'updated_by'=>$request->get('defusername')
                    ]
                    );
                DB::table('activity_log')->insert(
                    [
                        'order_type'=>$type,
                        'key_id'=>$orderDetails->order_id,
                        'operation'=>"Order Completed",
                        'fields'=>'delivered_by',
                        'old_value'=>'',
                        'new_value'=>$request->get('defusername'),
                        'updated_by'=>$request->get('defusername')
                    ]
                    );
            }
            DB::table('del_orders')->where('order_id',$request->get('orderId'))->update($updateData);
            if($orderDetails->deliverypickup == 'Collection' && $orderDetails->msg_flag == 'n'){
                $this->orderCompletedWpMsg($request->get('orderId'));
                DB::table('del_orders')->where('order_id',$request->get('orderId'))->update(['msg_flag'=>'y']);
            }
            DB::commit();
            return json_encode(['status'=>'success','description'=>'found','resp'=>"Payment Details Updated!"]);
        }catch(Exception $ex){
            DB::rollback();
            return json_encode(['status'=>'error','description'=>'not found','resp'=>$e->getMessage()]);
        }
    }

    public function updateFeedback(Request $request){
        DB::beginTransaction();
        try{
            $orderDetails = DB::table('del_orders')->where('order_id',$request->get('orderId'))->first();
            $updateData = array();
            $images = array();
            if($request->get('productImage1')){
                $image = $request->get('productImage1');  // your base64 encoded
                $image = str_replace('data:image/jpg;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageName = $request->get('orderId').'ProductImage1.'.'jpg';
                \File::put(storage_path(). '/deliveredProductImages/' . $imageName, base64_decode($image));
                array_push($images,$this->productImagePath. $imageName);
            }
            if($request->get('productImage2')){
                $image = $request->get('productImage2');  // your base64 encoded
                $image = str_replace('data:image/jpg;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageName = $request->get('orderId').'ProductImage2.'.'jpg';
                \File::put(storage_path(). '/deliveredProductImages/' . $imageName, base64_decode($image));
                array_push($images,$this->productImagePath. $imageName);
            }
            if($request->get('productImage3')){
                $image = $request->get('productImage3');  // your base64 encoded
                $image = str_replace('data:image/jpg;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageName = $request->get('orderId').'ProductImage3.'.'jpg';
                \File::put(storage_path(). '/deliveredProductImages/' . $imageName, base64_decode($image));
                array_push($images,$this->productImagePath. $imageName);
            }
            if($request->get('signature')){
                $image = $request->get('signature');  // your base64 encoded
                $image = str_replace('data:image/jpg;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageName = $request->get('orderId').'Signature.'.'jpg';
                \File::put(storage_path(). '/customerSignatures/' . $imageName, base64_decode($image));
                $updateData['cust_sign'] = $this->customerSignatureImagePath . $imageName;
            }
            $updateData['product_delivered'] = json_encode($images);
            $updateData['custcomments'] = $request->get('customerFeedback');

            if($orderDetails->deliverypickup == 'Pick Up'){
                $pickedup_order_ids = DB::table('pickups')->where('pickup_order_id',$request->get('orderId'))->whereNull('status')->get()->pluck('order_details_id');
                DB::table('order_details')->whereIn('id',$pickedup_order_ids)->update(['current_status'=>'Picked Up']);
            }
            
            // ******* Order Completion status ******* //
            //                                         //
             $updateData['status'] = ($orderDetails->deliverypickup == 'Delivery'?'Delivered':($orderDetails->deliverypickup == 'Collection'?'Collected':($orderDetails->deliverypickup == 'Pick Up'?'Picked up':'Completed')));
            //                                         //
            // ******* Order Completion status ******* //
            DB::table('activity_log')->insert(
                [
                    'order_type'=>($orderDetails->deliverypickup == 'Delivery'?'DO':($orderDetails->deliverypickup == 'Collection'?'CO':($orderDetails->deliverypickup == 'Pick Up'?'PU':'COM'))),
                    'key_id'=>$orderDetails->order_id,
                    'operation'=>"Order Completed",
                    'fields'=>'delivered_at',
                    'old_value'=>'',
                    'new_value'=>date('Y-m-d H:i:m'),
                    'updated_by'=>$request->get('defusername')
                ]
                );
            DB::table('activity_log')->insert(
                [
                    'order_type'=>($orderDetails->deliverypickup == 'Delivery'?'DO':($orderDetails->deliverypickup == 'Collection'?'CO':($orderDetails->deliverypickup == 'Pick Up'?'PU':'COM'))),
                    'key_id'=>$orderDetails->order_id,
                    'operation'=>"Order Completed",
                    'fields'=>'delivered_by',
                    'old_value'=>'',
                    'new_value'=>$request->get('defusername'),
                    'updated_by'=>$request->get('defusername')
                ]
                );
            DB::table('del_orders')->where('order_id',$request->get('orderId'))->update($updateData);
            DB::commit();
            if(DB::table('del_orders')->join('leads','leads.id','=','del_orders.lead_id')->where('del_orders.order_id',$request->get('orderId'))->exists()){
                $order = DB::table('del_orders')->join('leads','leads.id','=','del_orders.lead_id')->where('del_orders.order_id',$request->get('orderId'))->first();                
                if(!in_array($order->lead_source,['Corporate Booking','Agent']) && $order->msg_flag =='n'){
                    $this->orderCompletedWpMsg($request->get('orderId'));
                    DB::table('del_orders')->where('order_id',$request->get('orderId'))->update(['msg_flag'=>'y']);
                }
            }

            return json_encode(['status'=>'success','description'=>'found','resp'=>"Order Updated Successfully!"]);
        }catch(Exception $ex){
            DB::rollback();
            return json_encode(['status'=>'error','description'=>'not found','resp'=>$e->getMessage()]);
        }
    }
    public function orderExpenseDetails(Request $request){
        if($request->get('type') == 'fetch'){
            try{
                $response['basicDetails'] = DB::table('del_orders')->where('del_orders.order_id',$request->get('orderId'))->first();
                array_push($this->ownLocation,trim($response['basicDetails']->location));
                $orderDate = $response['basicDetails']->DelDate;
                $fromLocations = DB::table('del_orders')->where(DB::raw("STR_TO_DATE(DelDate,'%d-%m-%Y')"),DB::raw("STR_TO_DATE('$orderDate','%d-%m-%Y')"))->when($request->get('defrole') == 'user',function($query)use($request){
                    $query->where(function($q)use($request){
                        $q->where('DelAssignedTo',$request->get('defusername'));
                        $q->orWhere('del_orders.helpers','LIKE','%'.$request->get('defusername').'%');
                    });
                })->get()->pluck('location')->toArray();

                if(DB::table('order_expenses')->where('order_id',$request->get('orderId'))->where('username',$request->get('defusername'))->exists()){
                    $response['orderExpenses'] = DB::table('order_expenses')->where('order_id',$request->get('orderId'))->where('username',$request->get('defusername'))->first();
                    $transport_medium = $response['orderExpenses']->transport_medium;
                    $transport_medium = str_replace('[', '', $transport_medium);
                    $transport_medium = str_replace(']', '', $transport_medium);
                    $transport_medium = str_replace(' ', '', $transport_medium);
                    $response['orderExpenses']->transport_medium = explode(",",$transport_medium);
                }else{
                    $response['orderExpenses'] = "nf";
                }
                foreach($fromLocations as $key=>$value){
                    $fromLocations[$key] = trim($value);
                }
                $response['transportMediums'] = $this->transportMediums;
                $response['fromLocation'] = (object)array_values(array_unique(array_merge($this->ownLocation,$fromLocations)));
                $response['toLocation'] = (object)array_values(array_unique($this->ownLocation));
                                
                

                return json_encode(['status'=>'success','description'=>'found','resp'=>$response]);
            }catch(Exception $ex){
                return json_encode(['status'=>'error','description'=>'not found','resp'=>$e->getMessage()]);
            }
        }
        else if($request->get('type') == 'update'){
            DB::beginTransaction();
            try{
                $basicDetails = DB::table('del_orders')->where('order_id',$request->get('orderId'))->first();
                DB::table('order_expenses')->updateOrInsert(
                    [
                        'username'=>$request->get('defusername'),'order_id'=>$request->get('orderId')
                    ],[
                        "exp_date" => date("Y-m-d",strtotime($basicDetails->DelDate)),
                        "cash_rec_from_cust" => $request->get("cashFromCust"),
                        "depo_returned" => $request->get("depositReturned"),
                        "labour" => $request->get("labourCharges"),
                        "transport" => $request->get("transport"),
                        "transport_medium" => ($request->get("transMedium") == "[]")?null:$request->get("transMedium"),
                        "fromlocation" => ($request->get("fromLocation") == "null")?null:$request->get("fromLocation"),
                        "tolocation" => ($request->get("toLocation") == "null")?null:$request->get("toLocation"),
                        "hardware_expenses" => $request->get("hardwareExpenses"),
                        "created_by" => $request->get("defusername")
                    ]
                    );
                
                DB::commit();
                return json_encode(['status'=>'success','description'=>'found','resp'=>"Order Expense Updated Successfully!"]);
            }catch(Exception $ex){
                DB::rollback();
                return json_encode(['status'=>'error','description'=>'not found','resp'=>$e->getMessage()]);
            }
        }
        else{
            return json_encode(['status'=>'error','description'=>'Unauthorised','resp'=>'Unauthorised Request!']);
        }
    }

    public function aboutUs(){
        try{
            $response = DB::table('misc_table')->where('field','about-us')->first()->value;
            return json_encode(['status'=>'success','description'=>'found','resp'=>$response]);
        }catch(Exception $ex){
            return json_encode(['status'=>'error','description'=>'not found','resp'=>$e->getMessage()]);
        }
    }

    public function orderCompletedWpMsg($order_id){
        $order_details = DB::table('del_orders')->where('order_id',$order_id)->first();
        if($order_details){
            $order_status = ($order_details->status == 'Collected')?"Renewed":$order_details->status;
            $customer_name = $order_details->shipping_first_name;
            $order_state = ($order_details->deliverypickup == 'Delivery'?'for placing your order':($order_details->deliverypickup == 'Collection'?'for renewing product':($order_details->deliverypickup == 'Pick Up'?'to allowing us to serve you':'to allowing us to serve you')));
            $product_name = substr($order_details->line_item_1,0,20).(strlen($order_details->line_item_1)>20)?'...':'';
            $order_date = date('d-M-y',strtotime($order_details->DelDate));
            if($order_details->deliverypickup != 'Pick Up'){
                $note_msg = config('app.note_msg');
                $terms_conditions = "*Terms & Conditions:-*";
            }else{
                $note_msg = " ";
                $terms_conditions = " ";
            }
            // $link = route('consumables-form',[$order_id,$order_details->mobileno]);
            $link = $this->createLink($order_id,$order_details->mobileno);

            $virtual_no = config('app.virtual_no');
            if(config('app.app_env') == 'devweb'){
                $contact = config('app.developer_contact');
            }else{
                $contact = $order_details->mobileno;
            }
            // Curl send wp message
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
                "mobileno"=> "$contact",
                "templatename" => "order_completion_and_consumable_link",
                "templateparams" => [
                    ["type"=> "text","text"=> "$order_status"],
                    ["type"=> "text","text"=> "$customer_name"],
                    ["type"=> "text","text"=> "$order_state"],
                    ["type"=> "text","text"=> "$product_name"],
                    // ["type"=> "text","text"=> "$order_status"],
                    ["type"=> "text","text"=> "$order_date"],
                    ["type"=> "text","text"=> "$terms_conditions"],
                    ["type"=> "text","text"=> "$note_msg"],
                    ["type"=> "text","text"=> "$link"],
                    ["type"=> "text","text"=> "$virtual_no"],
                ],
            ];
            // return $data;
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            $resp = curl_exec($curl);
            curl_close($curl);
            return $resp;
        }else{
            return false;
        }

    }

    public function createLink($order_id,$contact_no){
        DB::beginTransaction();
        try{
            $app_env = config('app.app_env');
            $leadController = new LeadController();
            $link_id = $leadController->GenerateLinkid();
            $encrypted_id = $this->encryptData($order_id,"AES-128-CTR","consumableQuali55care");
            $encrypted_no = $this->encryptData($contact_no,"AES-128-CTR","consumableQuali55care");
            $full_url = "http://intra.quali55care.com/$app_env/eflow/consumables-form/$encrypted_id/$encrypted_no";
            
            $short_url = "$app_env/eflow/cons/".$link_id;
            $whatsappLink = "http://intra.quali55care.com/".$short_url;            
            ShortUrl::insert(['url_link_id'=>$link_id,'full_url'=>$full_url]);
            DB::commit();
            return $whatsappLink;
        }catch(Exception $ex){
            DB::rollback();
            return $ex;
        }
    }

    public function encryptData($original_string,$ciphering_value,$encryption_key){
        return openssl_encrypt($original_string,$ciphering_value,$encryption_key,0,8000000000000000);
        
    }
    public function decryptData($original_string,$ciphering_value,$decryption_key){
        return openssl_decrypt($original_string,$ciphering_value,$decryption_key,0,8000000000000000);
    }
}
