<?php

namespace App\Http\Controllers\AppApi;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AppApi\AuthController;

class ExpensesController extends Controller
{
    //
    protected $timeperiod,$startDate,$endDate,$receiptImagePath;
    public function __construct(){
        $this->timeperiod = "1 month";
        $this->startDate = Carbon::now()->subMonth()->toDateString();
        $this->endDate = Carbon::now()->toDateString();
        $this->receiptImagePath = "http://intra.quali55care.com/prodweb/eflow/storage/expenseReceipt/";
    }

    public function expDateList(Request $request){
        $dateRange = array();
        $i = 0;
        for($currentDate = date('Y-m-d',strtotime($this->endDate)); date('Y-m-d',strtotime($currentDate)) >= date('Y-m-d',strtotime($this->startDate)); $currentDate = date('Y-m-d',strtotime("-1 days",strtotime($currentDate))) ){
            $dateRange[$i]['date'] = date('d-M, D',strtotime($currentDate));
            $dateRange[$i]['orgDate'] = date('Y-m-d',strtotime($currentDate));
            $dateRange[$i]['displayDate'] = date('d-m-Y',strtotime($currentDate));
            $dateRange[$i]['isSunday'] = (date('D',strtotime($currentDate)) == 'Sun')?"true":"false";
            $dateRange[$i]['isHoliday'] = (DB::table('daily_expenses')->where('user_name',$request->get('defusername'))->where('exp_date',$currentDate)->where('holiday','Yes')->exists())?"true":"false";
            $dateRange[$i]['hasExpense'] = (DB::table('daily_expenses')->where('user_name',$request->get('defusername'))->where('exp_date',$currentDate)->exists())?"true":"false";
            $i++;
        }
        return  json_encode(['status'=>'success','description'=>'found','resp'=>(Object)$dateRange]);
    }

    public function expenseDetails(Request $request){
        try{
            $expDate = date('d-m-Y',strtotime($request->get('expDate')));
            if(DB::table('daily_expenses')->where('user_name',$request->get('defusername'))->where('exp_date',$request->get('expDate'))->exists()){
                $response = DB::table('daily_expenses')->where('user_name',$request->get('defusername'))->where('exp_date',$request->get('expDate'))->first();
                $response->cashCarriedForward = (DB::table('daily_expenses')->where('user_name',$request->get('defusername'))->where('exp_date',date('Y-m-d',strtotime("-1 days",strtotime($request->get('expDate')))))->exists())?DB::table('daily_expenses')->where('user_name',$request->get('defusername'))->where('exp_date',date('Y-m-d',strtotime("-1 days",strtotime($request->get('expDate')))))->first()->balance_cash:0;
                $response->cashExpected = (DB::table('del_orders')->where(DB::raw("STR_TO_DATE(DelDate,'%d-%m-%Y')"),DB::raw("STR_TO_DATE('$expDate','%d-%m-%Y')"))->whereIn('deliverypickup',['Delivery','Collection'])->where('DelAssignedTo',$request->get('defusername'))->where('PaymentMode','Cash')->exists())?DB::table('del_orders')->where(DB::raw("STR_TO_DATE(DelDate,'%d-%m-%Y')"),DB::raw("STR_TO_DATE('$expDate','%d-%m-%Y')"))->whereIn('deliverypickup',['Delivery','Collection'])->where('DelAssignedTo',$request->get('defusername'))->where('PaymentMode','Cash')->get()->pluck('TotalAmt')->sum():0;
                $response->depositReturned = (DB::table('del_orders')->where(DB::raw("STR_TO_DATE(DelDate,'%d-%m-%Y')"),DB::raw("STR_TO_DATE('$expDate','%d-%m-%Y')"))->where('DelAssignedTo',$request->get('defusername'))->where('PaymentMode','Cash')->exists())?DB::table('del_orders')->where(DB::raw("STR_TO_DATE(DelDate,'%d-%m-%Y')"),DB::raw("STR_TO_DATE('$expDate','%d-%m-%Y')"))->where('deliverypickup','Pick Up')->where('DelAssignedTo',$request->get('defusername'))->where('PaymentMode','Cash')->get()->pluck('TotalAmt')->sum():0;
                return json_encode(['status'=>'success','description'=>'found','resp'=>$response]);
            }else{
                $response = (object)array();
                $response->cashCarriedForward = (DB::table('daily_expenses')->where('user_name',$request->get('defusername'))->where('exp_date',date('Y-m-d',strtotime("-1 days",strtotime($request->get('expDate')))))->exists())?DB::table('daily_expenses')->where('user_name',$request->get('defusername'))->where('exp_date',date('Y-m-d',strtotime("-1 days",strtotime($request->get('expDate')))))->first()->balance_cash:0;
                $response->cashExpected = (DB::table('del_orders')->where(DB::raw("STR_TO_DATE(DelDate,'%d-%m-%Y')"),DB::raw("STR_TO_DATE('$expDate','%d-%m-%Y')"))->whereIn('deliverypickup',['Delivery','Collection'])->where('DelAssignedTo',$request->get('defusername'))->where('PaymentMode','Cash')->exists())?DB::table('del_orders')->where(DB::raw("STR_TO_DATE(DelDate,'%d-%m-%Y')"),DB::raw("STR_TO_DATE('$expDate','%d-%m-%Y')"))->whereIn('deliverypickup',['Delivery','Collection'])->where('DelAssignedTo',$request->get('defusername'))->where('PaymentMode','Cash')->get()->pluck('TotalAmt')->sum():0;
                $response->depositReturned = (DB::table('del_orders')->where(DB::raw("STR_TO_DATE(DelDate,'%d-%m-%Y')"),DB::raw("STR_TO_DATE('$expDate','%d-%m-%Y')"))->where('DelAssignedTo',$request->get('defusername'))->where('PaymentMode','Cash')->exists())?DB::table('del_orders')->where(DB::raw("STR_TO_DATE(DelDate,'%d-%m-%Y')"),DB::raw("STR_TO_DATE('$expDate','%d-%m-%Y')"))->where('deliverypickup','Pick Up')->where('DelAssignedTo',$request->get('defusername'))->where('PaymentMode','Cash')->get()->pluck('TotalAmt')->sum():0;
                $response->deposite_paid = 0;
                $response->transport = 0;
                $response->expenses = 0;
                $response->labour = 0;
                $response->cash_from_office = 0;
                $response->lunch_dinner = 0;
                $response->monthly_pass = 0;
                $response->office_expenses = 0;
                $response->fuel_expenses = 0;
                $response->cash_received_from_customer = 0;
                $response->receipt_no = null;
                $response->holiday = null;
                $response->img_url = null;
                if(DB::table('order_expenses')->where('username',$request->get('defusername'))->where('exp_date',$request->get('expDate'))->exists()){
                    $order_expenses = DB::table('order_expenses')->where('username',$request->get('defusername'))->where('exp_date',$request->get('expDate'))->get();
                    $response->deposite_paid = $order_expenses->pluck('depo_returned')->sum();
                    $response->transport = $order_expenses->pluck('transport')->sum();
                    $response->expenses = $order_expenses->pluck('hardware_expenses')->sum();
                    $response->labour = $order_expenses->pluck('labour')->sum();
                    $response->cash_received_from_customer = $order_expenses->pluck('cash_rec_from_cust')->sum();
                }
                return json_encode(['status'=>'success','description'=>'nf','resp'=>$response]);
            }
        }catch(Exception $e){
            return json_encode(['status'=>'error','description'=>'not found','resp'=>$e->getMessage()]);
        }
    }

    public function updateExpense(Request $request){
        DB::beginTransaction();
        try{
            $updateData = [
                "deposite_paid" => $request->get('depositReturned'),
                "transport" => $request->get('transport'),
                "expenses" => $request->get('expenses'),
                "labour" => $request->get('labourCharges'),
                "balance_cash" => $request->get('balanceCash'),
                "cash_from_office" => $request->get('cashRecFromOffice'),
                "received_cash" => 0,
                "lunch_dinner" => $request->get('lunchDinner'),
                "monthly_pass" => $request->get('trainMonthlyPass'),
                "office_expenses" => $request->get('officeExpenses'),
                "fuel_expenses" => $request->get('petrol'),
                "cash_received_from_customer" => $request->get('cashRecFromCustomer'),
                "receipt_no" => $request->get('receiptNo'),
                "holiday" => ($request->get('isHoliday') == 'Yes')?"Yes":null
            ];
            if($request->get('receiptImage')){
                $image = $request->get('receiptImage');  // your base64 encoded
                $image = str_replace('data:image/jpg;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageName = $request->get('defusername').'_'.$request->get('expDate').'.'.'jpg';
                \File::put(storage_path(). '/expenseReceipt/' . $imageName, base64_decode($image));
                $updateData['img_url'] = $this->receiptImagePath . $imageName;
            }
            
            DB::table('daily_expenses')->updateOrInsert(
                [
                    "user_name" => $request->get('defusername'),
                    "exp_date" => $request->get('expDate')
                ],$updateData
                );
            DB::commit();
            return json_encode(['status'=>'success','description'=>'found','resp'=>"Expence Updated Successfully!..."]);
        }catch(Exception $ex){
            DB::rollback();
            return json_encode(['status'=>'error','description'=>'not found','resp'=>$e->getMessage()]);
        }
    }
}
