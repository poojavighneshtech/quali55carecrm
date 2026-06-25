<?php

namespace App\Http\Controllers\AppApi;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ReportsController extends Controller
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
        $this->receiptImagePath = "http://intra.quali55care.com/devweb/eflow/storage/receiptImages/";
        $this->productImagePath = "http://intra.quali55care.com/devweb/eflow/storage/deliveredProductImages/";
        $this->customerSignatureImagePath = "http://intra.quali55care.com/devweb/eflow/storage/customerSignatures/";
        $this->ownLocation = ['Office-Mumbai','Office-Pune','Office-Gurgaon','Office-Delhi'];
        $this->transportMediums = (object)["Bike", "Boat", "Metro", "OLA", "Porter", "Rickshaw", "Taxi", "Tempo", "Train"];

    }
    public function orderSummary(Request $request){
        DB::beginTransaction();
            try{
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
                DB::commit();
                // return $summaryReport->groupBy('order_id');
                $summaryReport = $summaryReport->groupBy('order_id');
                $resp['total'] = $summaryReport->count();
                $resp['delay'] = 0;
                $resp['exception'] = 0;
                $resp['ontime'] = 0;
                $resp['staff'] = DB::table('delusers')->select('username')->where('status','Active')->where('role','user')->where('inhousestaff','Yes')->orderBy('username','ASC')->get();
                $resp['pending'] = DB::table('del_orders')->select('order_id')
                    ->whereNotIn('status',$this->cancelOrderStatus)
                    ->whereNotIn('status',$this->completedOrderStatus)
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
                    ->count();
                foreach($summaryReport as $key=>$summary){
                    // return $summary;
                    $completedAt = Carbon::parse(date('d-M-y H:i:s',strtotime($summary[count($summary)-1]->new_value)));
                    $assignedAt = Carbon::parse(date('d-M-y H:i:s',strtotime($summary[0]->new_value)));

                    $diff = $completedAt->diffInSeconds($assignedAt);
                    if(date('H:i:s',strtotime(gmdate('H:i:s', $diff))) >= date('H:i:s',strtotime('04:00:00')))
                    {
                        $resp['delay'] = $resp['delay'] + 1;
                    }
                    elseif(date('H:i:s',strtotime(gmdate('H:i:s', $diff))) <= date('H:i:s',strtotime('01:15:00')))
                    {
                        $resp['exception'] = $resp['exception'] + 1;
                    }
                    else
                    {
                        $resp['ontime'] = $resp['ontime'] + 1;
                    }
                    // return $diff;
                }
                return json_encode(['status'=>'success','description'=>'found','resp'=>$resp]);
            }catch(Exception $ex){
                DB::rollback();
                return json_encode(['status'=>'error','description'=>'not found','resp'=>$e->getMessage()]);
            }
    }
}
