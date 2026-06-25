<?php

namespace App\Http\Controllers\Expense;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\VendorRegister;
use App\Models\UserRegister;
use App\Models\DelOrders;
use App\Models\OrderDetails;
use App\Models\LinkCustDetails;
use App\Models\RenewalReminder;
use App\Models\customer_detail;
use App\Models\Renewal;
use App\Models\Pickup;
use App\Models\ShortUrl;
use App\Models\DailyExpense;
use App\Models\ActivityLog;
use App\Models\opening_closing_balances;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use PDF;
use Mail;
use Session;
use DateTime;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Response;
//use other controler
use App\Http\Controllers\Leads\LeadController;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DeliveryExpense;


class ExpenseController extends Controller
{
    public function isLoggedIn()
    {
        $data = session('isLoggedIn');
        return $data;
    }
        
    //show all expenses
    public function AllExpenses(Request $request)
    {
        //delivery staff
        if(session('city_based_access') == '1')
        {
            $get_delivery_staff = DB::table('delusers')->where('role','=','user')->where('city',session('user_city'))->get();
        }
        else
        {
            $get_delivery_staff = DB::table('delusers')->where('role','=','user')->get();
        }

        $start_date = Carbon::yesterday()->toDateString();
        $end_date = Carbon::now()->toDateString();
        if($request->get('bulk_settle') == "bulk-settle")
        {
            $start_date = date('Y-m-d',strtotime($request->get('start_date_bulk')));
            $username = $request->get('del_boy');
            $end_date = date('Y-m-d',strtotime("-1 days"));
            DailyExpense::whereBetween('exp_date',[$start_date,$end_date])
                        ->when($username,function($query,$username){
                            if($username != "All")
                            {
                                $query->where('user_name',$username);
                            }
                        })                        
                        ->whereNotIn('status',['Settled'])
                        ->update([
                            'received_cash'=>DB::raw('daily_expenses.balance_cash'),
                            'balance_cash'=>0,
                            'verified_by'=>session('username'),
                            'verified_at'=>Carbon::now()->toDateTimeString(),
                            'status'=>'Settled',
                            'settled_by'=>session('username'),
                            'settled_at'=>Carbon::now()->toDateTimeString(),
                            'comment'=>'Bulk Settled'
                        ]);
                        $new_value = [
                            'from_date'=>$start_date,
                            'username'=>$username,
                            'end_date'=>$end_date,
                            'received_cash'=>'Expense_cash',
                            'balance_cash'=>0,
                            'verified_by'=>session('username'),
                            'verified_at'=>Carbon::now()->toDateTimeString(),
                            'status'=>'Settled',
                            'settled_by'=>session('username'),
                            'settled_at'=>Carbon::now()->toDateTimeString(),
                            'comment'=>'Bulk Settled'
                        ];
            $insertData = [
                'order_type'=>'DEXP',
                'key_id'=>0,
                'operation'=>'Bulk Settled',
                'fields'=>'received_cash,balance_cash,verified_by,verified_at,settled_by,settled_at,comment',
                'old_value'=>null,
                'new_value'=>json_encode($new_value),
                'updated_by'=>session('username')
            ];
            ActivityLog::insert($insertData);
        }
        $status = $request->get('status');
        $status_arr = ['Pending','Verified','Settled'];
        if($status==null || $status=='All'){
            $status_arr = ['Pending','Verified','Settled'];
        }else{
            $status_arr = [$status];
        }
        if($request->get('start_date')!=null && $request->get('end_date')!=null){
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
        }
        $del_username = $request->get('del_username');
        // $getDelUsers = DailyExpense::distinct('user_name')
        //                         ->whereBetween('exp_date',[$start_date,$end_date])
        //                         ->get('user_name');
        $getDelUsers = DB::table('daily_expenses')
                            ->join('delusers','delusers.username','=','daily_expenses.user_name')
                            ->distinct('daily_expenses.user_name')
                            ->select('daily_expenses.user_name')
                            ->whereBetween('daily_expenses.exp_date',[$start_date,$end_date])
                            ->when(session('city_based_access') == '1',function($query){
                                $query->where('delusers.city',session('user_city'));
                            })
                            ->get();
        
        // $get_all_expenses_all = DailyExpense::whereBetween('exp_date',[$start_date,$end_date])
        $get_all_expenses_all = DB::table('daily_expenses')
                                        ->join('delusers','delusers.username','=','daily_expenses.user_name')
                                        ->whereBetween('daily_expenses.exp_date',[$start_date,$end_date])
                                        ->when($request->get('selected_user')!="All",function($query) use($request){
                                            $query->where([['daily_expenses.user_name','LIKE','%'.$request->get('selected_user').'%']]);
                                        })
                                        ->when($request->get('status') && $request->get('status')!="All",function($query) use($request){
                                            $query->where('daily_expenses.status','=',$request->get('status'));
                                        })
                                        ->when(session('city_based_access') == '1',function($query){
                                            $query->where('delusers.city',session('user_city'));
                                        })
                                        ->orderBy('daily_expenses.exp_date','DESC')
                                        ->orderBy('daily_expenses.id','DESC')
                                        ->get('daily_expenses.*');
        $totalTransport = $get_all_expenses_all->pluck('transport')->sum();
        $totalExpense = $get_all_expenses_all->pluck('expenses')->sum();
        $totalLabour = $get_all_expenses_all->pluck('labour')->sum();
        $get_all_expenses = $get_all_expenses_all->paginate(10);
        foreach($get_all_expenses as $key=>$expense){
            // $get_all_expenses[$key]->previous_balance = DB::table('daily_expenses')->where('user_name',$expense->user_name)->where('exp_date',date('Y-m-d',strtotime("-1 days",strtotime($expense->exp_date))))->get()->sum('balance_cash');
            if(DB::table('daily_expenses')->where('user_name',$expense->user_name)->where('exp_date',date('Y-m-d',strtotime("-1 days",strtotime($expense->exp_date))))->exists()){
                $get_all_expenses[$key]->previous_balance = DB::table('daily_expenses')->where('user_name',$expense->user_name)->where('exp_date',date('Y-m-d',strtotime("-1 days",strtotime($expense->exp_date))))->get()->sum('balance_cash');
                $get_all_expenses[$key]->previous_settled = DB::table('daily_expenses')->where('user_name',$expense->user_name)->where('exp_date',date('Y-m-d',strtotime("-1 days",strtotime($expense->exp_date))))->first()->status;
            }else{
                $get_all_expenses[$key]->previous_balance = 0;
                $get_all_expenses[$key]->previous_settled = 'Settled';
            }
            // dd(DB::table('del_expenses')->where('user_name',$expense->user_name)->where('exp_date',date('Y-m-d',strtotime("-1 days",strtotime($expense->exp_date))))->toSql(),$expense->user_name,$expense->exp_date);
        }
        $filter = [
            'start_date'=>$start_date,
            'end_date'=>$end_date,
        ];
        if(!empty($request->get('submit')) && $request->get('submit')=='exportExcel')
        {
            ob_end_clean(); // this
            ob_start(); // and this
            return Excel::download(new DeliveryExpense($get_all_expenses_all,$totalTransport,$totalExpense,$totalLabour), 'DeliveryExpense.xlsx');            
        }
        return view('ExpensesViews/all_expenses',compact('get_all_expenses','filter','get_delivery_staff','getDelUsers','totalTransport','totalExpense','totalLabour'));
    }

    //settle expense from account side
    public function SettleExpense(Request $request)
    {
        $received_bal = $request->get('received_bal');
        $actual_bal = $request->get('actual_bal');
        $id = $request->get('id');
        
        $narration = $request->get('narration');
        $timestamp = date("d M, h:i A");
        $comment = "[".$timestamp."]".$narration."-Received Amount :$received_bal \n";
        if($request->get('isin_negative')=='Y'){
            $comment = "[".$timestamp."]".$narration."-Paid Amount :$received_bal \n";
            $received_bal = -($received_bal);
        }
        $cmtUpdate = [
            'comment' => $comment
        ];
        //dd($request->all(),$comment,$received_bal);
        $cmt_check = DB::table('daily_expenses')->where('id',$id)->get('comment');
        if(isset($cmt_check[0]->comment))
        {
            $cmt_update = DB::update("UPDATE daily_expenses SET comment = CONCAT('$comment',comment) WHERE id = '$id' ");
        }
        else
        {
            DailyExpense::where('id',$id)->update($cmtUpdate);
        }
        
        $getData = DailyExpense::where('id',$id)->get();//for activity log

        $updateData=[
            'balance_cash'=>$actual_bal,
            'received_cash'=>$received_bal,
            'status'=>'Settled',
            'settled_by'=>session('username'),
            'settled_at'=>Carbon::now()->toDateTimeString(),
        ];
        DailyExpense::where('id',$id)->update($updateData);
        //insert into acitivty log
        foreach($updateData as $key=>$data){
            $insertData = [
                'order_type'=>'DEXP',
                'key_id'=>$id,
                'operation'=>'Settled Expense',
                'fields'=>$key,
                'old_value'=>$getData[0]->$key,
                'new_value'=>$data,
                'updated_by'=>session('username')
            ];
            ActivityLog::insert($insertData);
        }
        Session::flash('message','Expense settled successfully');
        return redirect()->back();
    }
    public function verify_expense($id)
    {
        $updateData=[
            'status'=>'Verified',
            'verified_by'=>session('username'),
            'verified_at'=>Carbon::now()->toDateTimeString(),
        ];
        DailyExpense::where('id',$id)->update($updateData);
        $update_status = DailyExpense::where('id',$id)->update($updateData);
        $data = DB::table('daily_expenses')->select('user_name','exp_date')->where('id',$id)->first();
        DB::table('del_orders')->where('DelAssignedTo',$data->user_name)->where('DelDate',date('d-m-Y',strtotime($data->exp_date)))->update(['del_expenses_status'=>'Verified']);
        DB::table('order_expenses')->where('username',$data->user_name)->where('exp_date',date('Y-m-d',strtotime($data->exp_date)))->update(['exp_status'=>'Verified']);
    }

    public function UpdateNarration(Request $request){
        $id = $request->get('narr_dexp_id');
        $narration = $request->get('narration');
        $timestamp = date("d M, h:i A");
        $comment = "[".$timestamp."]".$narration."\n";
        $cmtUpdate = [
            'comment' => $comment
        ];
        $cmt_check = DB::table('daily_expenses')->where('id',$id)->get('comment');
        if(isset($cmt_check[0]->comment))
        {
            $cmt_update = DB::update("UPDATE daily_expenses SET comment = CONCAT('$comment',comment) WHERE id = '$id' ");
        }
        else
        {
            DailyExpense::where('id',$id)->update($cmtUpdate);
        }
        Session::flash('message','Comment Updated Successfully');
        return redirect()->back();
    }

    //Add cash
    public function AddCash(Request $request){
        $date = $request->get('add_cash_date');
        $del_boy = $request->get('add_del_boy');
        $cash = $request->get('add_cash_amount');
        
        $insertData = [
            'user_name'=>$del_boy,
            'deposite_paid'=>0,
            'transport'=>0,
            'expenses'=>0,
            'balance_cash'=>$cash,
            'exp_date'=>$date,
            'cash_from_office'=>$cash,
        ];
        $inserDailExpenses = DB::table('daily_expenses')->insert($insertData);
        Session::flash('message','Cash Added Successfully');
        return redirect()->back();
    }

    public function UpdateCash(Request $request)
    {
        $id = $request->get('id');
        $edited_cash = $request->get('edited_cash');
        $get_bal = DailyExpense::where('id',$id)->get(['cash_from_office','cash_received_from_customer','deposite_paid','transport','labour','expenses','balance_cash']);
        $cash_rec_frm_cust = $get_bal[0]->cash_received_from_customer;
        $depo = $get_bal[0]->deposite_paid;
        $transp = $get_bal[0]->transport;
        $exp = $get_bal[0]->expenses;
        $labour = $get_bal[0]->labour;
        $total_cash = $edited_cash+$cash_rec_frm_cust;
        $total_rem = $total_cash-$depo-$transp-$exp-$labour;
        
        $updateData = ['cash_from_office'=>$edited_cash,'balance_cash'=>$total_rem];
        DailyExpense::where('id',$id)->update($updateData);
        //for activity log
        foreach ($updateData as $key => $upData) {
            
            $insertData = [
                'order_type'=>'DEXP',
                'key_id'=>$id,
                'operation'=>'Update Cash From Off',
                'fields'=>$key,
                'old_value'=>$get_bal[0]->$key,
                'new_value'=>$upData,
                'updated_by'=>session('username')
            ];
            ActivityLog::insert($insertData);
        }
    }

    //upload expenses 
    public function UploadExpenses(Request $request)
    {
        if(session('city_based_access') == '1')
        {
            $getDelUsers = DB::table('delusers')->where('status','=','Active')->where('city',session('user_city'))->get();
        }
        else
        {
            $getDelUsers = DB::table('delusers')->where('status','=','Active')->get();
        }
        $yesterday = Carbon::yesterday()->toDateString();
        return view('ExpensesViews/upload_expenses',compact('getDelUsers','yesterday'));
    }
    
    public function checkPreviousBalance(Request $request)
    {
        $date = $request->get('date');
        $prevDate = Carbon::parse($date)->subDay()->toDateString();
        $delUser = $request->get('del_user');

        $checkBal = DB::table('daily_expenses')->where([['user_name','=',$delUser],['exp_date','=',$prevDate],['balance_cash','!=','0']])->get('balance_cash');
        if($checkBal->isNotEmpty()){
                return Response::json(['status'=>$checkBal]);
        }else{
            return Response::json(['status'=>"Not Found"]);
        }
    }

    public function InsertExpense(Request $request){
        
        $delUser = $request->get('del_user_name');
        $date = $request->get('selected_date');
        $cashFrmOff = $request->get('cash_received_from_office');
        $cashFrmCust = $request->get('cash_received_from_customer');
        $transport = $request->get('transport');
        $actDepoRet = $request->get('actual_deposit_returned');
        $expense = $request->get('expense');
        $labourCharges = $request->get('labour_charges');
        $recNo = $request->get('receipt_no');
        $balCash = $request->get('balance_cash');

        //upload image file
        $expense_image_filePath = null;
        if($_FILES['expense_image']['name']!=null)
        {
            $expense_image = $_FILES['expense_image']['name'];
            //print_r($_FILES['shop_images']['name']);
            $targetDir = "../../testapi/expenses_demo/expense_receipt/";
            $fileName = basename($_FILES['expense_image']['name']);
            $newFileName = $delUser."_".$date;
            $targetFilePath = $targetDir . $fileName;
            $fileType =strtolower(pathinfo($targetFilePath,PATHINFO_EXTENSION));
            $new_file_name = $targetDir."".$newFileName.".".$fileType;
            move_uploaded_file($_FILES["expense_image"]["tmp_name"], $new_file_name);
            $expense_image_filePath = "13.233.36.52/testapi/expenses_demo/expense_receipt/".$newFileName.".".$fileType;
        }
        if($request->get('hidden_receipt_image')!=null){
            $expense_image_filePath = $request->get('hidden_receipt_image');
        }

        $holiday = $request->get('holiday');
        if(isset($holiday) && $holiday=="on"){
            $insertData = [
                'user_name'=>$delUser,
                'deposite_paid'=>0,
                'transport'=>0,
                'expenses'=>0,
                'labour'=>0,
                'balance_cash'=>0,
                'exp_date'=>$date,
                'cash_from_office'=>0,
                'cash_received_from_customer'=>0,
                'holiday'=>"Yes"
            ];
            if($request->get('voucher_id')!=null){
                $insertDailExpenses = DB::table('daily_expenses')->where('id',$request->get('voucher_id'))->update($insertData);    
            }else{
                $insertDailExpenses = DB::table('daily_expenses')->insert($insertData);
            }
            Session::flash('message',$delUser.' holiday Added Successfully');
            return redirect()->back();
        }
        $insertData = [
            'user_name'=>$delUser,
            'deposite_paid'=>$actDepoRet,
            'transport'=>$transport,
            'expenses'=>$expense,
            'labour'=>$labourCharges,
            'balance_cash'=>$balCash,
            'exp_date'=>$date,
            'cash_from_office'=>$cashFrmOff,
            'img_url'=>$expense_image_filePath,
            'cash_received_from_customer'=>$cashFrmCust,
        ];
        if($request->get('voucher_id')!=null){
            $insertDailExpenses = DB::table('daily_expenses')->where('id',$request->get('voucher_id'))->update($insertData);
        }else{
            $insertDailExpenses = DB::table('daily_expenses')->insert($insertData);
        }
        Session::flash('message',$delUser.' Expense Added Successfully');
        return redirect()->back();
    }

    public function GetExpenseData(Request $request)
    {
        $delUserName = $request->get('username');
        $expenseDate = $request->get('expense_date');
        $getExpenseDetails = DB::table('daily_expenses')->where([['user_name','=',$delUserName],['exp_date','=',$expenseDate]])->first();
        return Response::json($getExpenseDetails);
    }

    public function UpdateExpense(Request $request)
    {
        $expId = $request->get('expense_id');
        $delUser = $request->get('del_user_name');
        $date = $request->get('expense_date');
        $cashFrmOff = $request->get('cash_received_from_office');
        $cashFrmCust = $request->get('cash_received_from_customer');
        $transport = $request->get('transport');
        $actDepoRet = $request->get('actual_deposit_returned');
        $expense = $request->get('expense');
        $labourCharges = $request->get('labour_charges');
        $lunch_dinner = $request->get('lunch_dinner');
        $month_pass = $request->get('monthly_pass');
        $office_expenses = $request->get('office_expenses');
        $fuel_expenses = $request->get('fuel_expenses');
        $recNo = $request->get('receipt_no');
        $balCash = $request->get('balance_cash');
        
        //upload image file
        $expense_image_filePath = null;
        if($_FILES['expense_image']['name']!=null)
        {
            $expense_image = $_FILES['expense_image']['name'];
            //print_r($_FILES['shop_images']['name']);
            $targetDir = "../../api/expenses_demo/expense_receipt/";
            $fileName = basename($_FILES['expense_image']['name']);
            $newFileName = $delUser."_".$date;
            $targetFilePath = $targetDir . $fileName;
            $fileType =strtolower(pathinfo($targetFilePath,PATHINFO_EXTENSION));
            $new_file_name = $targetDir."".$newFileName.".".$fileType;
            move_uploaded_file($_FILES["expense_image"]["tmp_name"], $new_file_name);
            $expense_image_filePath = "intra.quali55care.com/api/expenses_demo/expense_receipt/".$newFileName.".".$fileType;
        }
        $holiday = $request->get('holiday');
        if(isset($holiday) && $holiday=="on"){
            $updateData = [
                'user_name'=>$delUser,
                'deposite_paid'=>0,
                'transport'=>0,
                'expenses'=>0,
                'labour'=>0,
                'balance_cash'=>0,
                'exp_date'=>$date,
                'cash_from_office'=>0,
                'cash_received_from_customer'=>0,
                'monthly_pass'=>0,
                'lunch_dinner'=>0,
                'office_expenses'=>0,
                'fuel_expenses'=>0,
                //'receipt_no'=>$recNo,
                'holiday'=>"Yes"
            ];
            $insertDailExpenses = DB::table('daily_expenses')->where('id',$expId)->update($updateData);
            Session::flash('message',$delUser.' holiday Added Successfully');
            return redirect()->back();
        }
        $updateData = [
            'user_name'=>$delUser,
            'deposite_paid'=>$actDepoRet,
            'transport'=>$transport,
            'expenses'=>$expense,
            'labour'=>$labourCharges,
            'balance_cash'=>$balCash,
            'exp_date'=>$date,
            'cash_from_office'=>$cashFrmOff,
            'lunch_dinner'=>$lunch_dinner,
            'monthly_pass'=>$month_pass,
            'office_expenses'=>$office_expenses,
            'fuel_expenses'=>$fuel_expenses,
            'receipt_no'=>$recNo,
            'img_url'=>$expense_image_filePath,
            'cash_received_from_customer'=>$cashFrmCust,
        ];
        $insertDailExpenses = DB::table('daily_expenses')->where('id',$expId)->update($updateData);
        Session::flash('message',$delUser.' Expense Added Successfully');
        return redirect()->back();
    }
    
    public function UnverifyExpense(Request $request)
    {
        //($request->all());
        $id = $request->get('unverify_exp_id');
        $narration = $request->get('unverify_comment');

        //update comment and status
        $timestamp = date("d M, h:i A");
        $comment = "[".$timestamp."] Unverify: ".$narration."\n";
        $cmtUpdate = [
            'status'=>'Pending',
            'comment' => $comment
        ];
        //get old values
        $getOldValues = DB::table('daily_expenses')->where('id',$id)->first(array_keys($cmtUpdate));
        $cmt_check = DB::table('daily_expenses')->where('id',$id)->get('comment');
        if(isset($cmt_check[0]->comment))
        {
            $cmt_update = DB::update("UPDATE daily_expenses SET comment = CONCAT('$comment',comment),status = 'Pending' WHERE id = '$id' ");
        }
        else
        {
            DailyExpense::where('id',$id)->update($cmtUpdate);
        }
        $getValues = DB::table('daily_expenses')->where('id',$id)->first();
        $data = DB::table('daily_expenses')->select('user_name','exp_date')->where('id',$id)->first();
        DB::table('del_orders')->where('DelAssignedTo',$data->user_name)->where('DelDate',date('d-m-Y',strtotime($data->exp_date)))->update(['del_expenses_status'=>'Pending']);
        DB::table('order_expenses')->where('username',$data->user_name)->where('exp_date',date('Y-m-d',strtotime($data->exp_date)))->update(['exp_status'=>'Pending']);
        // dd($getValues->id,$getValues->exp_date,$getValues->user_name,$getValues->cash_received_from_customer,$getValues->cash_from_office,$getValues->transport,$getValues->deposite_paid,$getValues->expenses,$getValues->labour,$getValues->balance_cash,$getValues->img_url);
        $user = '- [Unverified] '.$getValues->user_name;
        $this->send_wp_msg($getValues->id,$getValues->exp_date,$user,$getValues->cash_received_from_customer,$getValues->cash_from_office,$getValues->transport,$getValues->deposite_paid,$getValues->expenses,$getValues->labour,$getValues->balance_cash,$getValues->img_url);
        //insert in activity log
        foreach ($cmtUpdate as $key => $value) {
            $insertData = [
                'order_type'=>'DEXP',
                'key_id'=>$id,
                'operation'=>'Clicked on Unverify',
                'fields'=>$key,
                'old_value'=>$getOldValues->$key,
                'new_value'=>$value,
                'updated_by'=>session('username')
            ];
            ActivityLog::insert($insertData);
        }
       
        Session::flash('message','Comment Updated Successfully');
        return redirect()->back();
    }
    public function order_expenses(Request $request)
    {
        if($request->method() == "POST")
        {
            // dd($request->all());
            $update_data = [
                'cash_rec_from_cust'=>$request->get('cash_received_from_customer'),
                'depo_returned'=>$request->get('actual_deposit_returned'),
                'transport'=>$request->get('transport'),
                'hardware_expenses'=>$request->get('expense'),
                'labour'=>$request->get('labour_charges')
            ];
            DB::table('order_expenses')->where('id',$request->get('order_exp_id'))->update($update_data);
            $order_id = DB::table('order_expenses')->select('order_id','exp_date','username')->where('id',$request->get('order_exp_id'))->get();
            // dd($order_id);
            $expense = DB::table('order_expenses')
                            ->select(
                                DB::raw("SUM(cash_rec_from_cust) as cash_rec_from_cust"),
                                DB::raw("SUM(depo_returned) as depo_returned"),
                                DB::raw("SUM(transport) as transport"),
                                DB::raw("SUM(hardware_expenses) as hardware_expenses"),
                                DB::raw("SUM(labour) as labour")
                                )
                            ->where('order_id',$order_id[0]->order_id)
                            ->get();
            $update_del_orders = [
                'del_cash_rec_from_cust'=>$expense[0]->cash_rec_from_cust,
                'del_depo_returned'=>$expense[0]->depo_returned,
                'del_transport'=>$expense[0]->transport,
                'del_expenses'=>$expense[0]->hardware_expenses,
                'del_labour_charges'=>$expense[0]->labour
            ];
            DB::table('del_orders')->where('order_id',$order_id[0]->order_id)->update($update_del_orders);

            $expense = DB::table('order_expenses')
                            ->select(
                                DB::raw("SUM(cash_rec_from_cust) as cash_rec_from_cust"),
                                DB::raw("SUM(depo_returned) as depo_returned"),
                                DB::raw("SUM(transport) as transport"),
                                DB::raw("SUM(hardware_expenses) as hardware_expenses"),
                                DB::raw("SUM(labour) as labour")
                                )
                            ->where('exp_date',$order_id[0]->exp_date)
                            ->where('username',$order_id[0]->username)
                            ->get();
            // dd($expense);
            $update_daily_expenses = [
                'cash_received_from_customer'=>$expense[0]->cash_rec_from_cust,
                'deposite_paid'=>$expense[0]->depo_returned,
                'transport'=>$expense[0]->transport,
                'expenses'=>$expense[0]->hardware_expenses,
                'labour'=>$expense[0]->labour
            ];
            DB::table('daily_expenses')->where('exp_date',$order_id[0]->exp_date)->where('user_name',$order_id[0]->username)->update($update_daily_expenses);
            $expense_id = DB::table('daily_expenses')->where('exp_date',$order_id[0]->exp_date)->where('user_name',$order_id[0]->username)->first('id');
            // dd($expense_id);
            $request = new Request();
            $request->merge(['id'=>$expense_id->id,'username'=>$order_id[0]->username,'exp_date'=>$order_id[0]->exp_date]);

            // dd($request->all());
            $this->RecalculateExpense($request);

            return redirect()->back();
        }
        else
        {
            // $today = Carbon::now()->toDateString();
            // dd($request->all());
            // $today = Carbon::parse('2022-08-06')->toDateString();
            $start_date = Carbon::yesterday()->toDateString();
            $end_date = Carbon::yesterday()->toDateString();
            if($request->get('filter_start_date') && $request->get('filter_end_date'))
            {
                $start_date = $request->get('filter_start_date');
                $end_date = $request->get('filter_end_date');
            }
            $order_expenses = DB::table('order_expenses')
                                ->join('del_orders','del_orders.order_id','=','order_expenses.order_id')
                                ->join('delusers','del_orders.DelAssignedTo','=','delusers.username')
                                ->select(
                                    'del_orders.shipping_first_name',
                                    'del_orders.order_id',
                                    'del_orders.DelDate',
                                    'del_orders.deliverypickup',
                                    'del_orders.line_item_1',
                                    'order_expenses.*'
                                    )
                                ->when($request->get('filter_customer_name'),function($query) use($request){
                                    $query->where([['del_orders.shipping_first_name','LIKE','%'.$request->get('filter_customer_name').'%']]);
                                })
                                // ->when($request->get('filter_start_date'),function($query) use($request){
                                //     $query->whereBetween('order_expenses.exp_date',[$request->get('filter_start_date'),$request->get('filter_end_date')]);
                                // })
                                ->when($request->get('filter_order_id'),function($query) use($request){
                                    $query->where('order_expenses.order_id',$request->get('filter_order_id'));
                                })
                                ->when($request->get('filter_del_boy'),function($query) use($request){
                                    if($request->get('filter_del_boy')!='All')
                                    {
                                        $query->where('order_expenses.username',$request->get('filter_del_boy'));
                                    }
                                })
                                ->when($request->get('filter_order_type'),function($query) use($request){
                                    $query->where('del_orders.deliverypickup',$request->get('filter_order_type'));
                                })
                                ->when(session('city_based_access'),function($query){
                                    $query->where('delusers.city',session('user_city'));
                                })
                                ->whereBetween('order_expenses.exp_date',[$start_date,$end_date])
                                // ->toSql();
                                // ->where('exp_date',$today)
                                ->paginate();
            // dd($order_expenses);
            $del_boys = DB::table('delusers')->select('username')->where('status','Active')->where('role','user')->orderBy('username','ASC')->get();
            return view('ExpensesViews.order-expenses',compact('order_expenses','del_boys'));
        }
    }
    public function get_order_expense(Request $request)
    {
        $order_expense = DB::table('order_expenses')->where('id',$request->get('exp_id'))->get();
        return $order_expense;
    }

    public function RecalculateExpense(Request $request)
    {
        $id = $request->get('id');
        $username = $request->get('username');
        $expenseDate = $request->get('exp_date');

        $prevDate = Carbon::parse($expenseDate)->subDay()->toDateString();
        $getCurrentExp = DB::table('daily_expenses')->where('id',$id)->first();
        // $checkBal = DB::table('daily_expenses')->where([['user_name','=',$username],['exp_date','<=',$prevDate],['balance_cash','!=','0'],['status','!=','Settled']])->get('balance_cash');
        $checkBal = DB::table('daily_expenses')->where([['user_name','=',$username],['exp_date','=',$prevDate],['balance_cash','!=','0'],['status','=','Settled']])->get('balance_cash');
        $previousBalance = $checkBal->sum('balance_cash');
        // dd($previousBalance);
        //echo $previousBalance;
        //update data
        $cashFromOff = $getCurrentExp->cash_from_office;
        $cashFromCust = $getCurrentExp->cash_received_from_customer;
        $depositePaid = $getCurrentExp->deposite_paid;
        $transport = $getCurrentExp->transport;
        $expense = $getCurrentExp->expenses;
        $labour = $getCurrentExp->labour;
        $lunch_dinner = $getCurrentExp->lunch_dinner;
        $month_pass = $getCurrentExp->monthly_pass;
        $office_expenses = $getCurrentExp->office_expenses;
        $fuel_expenses = $getCurrentExp->fuel_expenses;
        $balanceCash = $getCurrentExp->balance_cash;
        $receivedCash = $getCurrentExp->received_cash;

        $totalBal = $previousBalance+$cashFromOff+$cashFromCust+$receivedCash-$depositePaid-$transport-$expense-$labour-$lunch_dinner-$month_pass-$office_expenses-$fuel_expenses;
        $updateData = [
            'balance_cash'=>$totalBal
        ];
        $updateExpense = DB::table('daily_expenses')->where('id',$id)->update($updateData);
        $insertData = [
            'order_type'=>'DEXP',
            'key_id'=>$id,
            'operation'=>'Clicked on Recalculate',
            'fields'=>'balance_cash',
            'old_value'=>$getCurrentExp->balance_cash,
            'new_value'=>$totalBal,
            'updated_by'=>session('username')
        ];
        ActivityLog::insert($insertData);
        
    }

    public function checkVoucherAvailable (Request $request){
        if(DB::table('daily_expenses')->where('user_name',$request->get('del_user'))->where('exp_date',$request->get('date'))->exists())
        {
            $voucher = DB::table('daily_expenses')->where('user_name',$request->get('del_user'))->where('exp_date',$request->get('date'))->get()->toArray();
            return response()->json($voucher);
        }else{
            return response()->json('failed');
        }
    }
    public function expReportDaily()
    {
        $settled_count = DB::table('daily_expenses')->where('exp_date',date('Y-m-d',strtotime('-1 days')))->where('status','Settled')->get();
        $verified_count = DB::table('daily_expenses')->where('exp_date',date('Y-m-d',strtotime('-1 days')))->where('status','Verified')->get();
        $not_verified_count = DB::table('daily_expenses')->where('exp_date',date('Y-m-d',strtotime('-1 days')))->where('status','Pending')->get();

        // dd($settled_count);
        $data = [
                    'settled'=>$settled_count,
                    'verified'=>$verified_count,
                    'not_verified'=>$not_verified_count
                ];
        Mail::send('PendingOrders/exp_data', $data, function($message) 
        {
            $email_id = config('app.ceo_email');
            // $email_id = 'abhishekn@quali55care.com';
            // $email_id = 'rahulbhanushali@quali55care.com';
            $message->to($email_id, 'CEO')->subject('Delivery Boy Expense Upload Report');
            $message->from('tempmailquali@gmail.com', 'Quali55Care');            
        });
    }
    public function send_wp_msg($exp_id,$date , $del_boy, $cash_rec_from_cust, $cash_rec_from_off, $transport, $depo_returned, $expenses, $labour_charges, $balance_cash, $receipt)
	{
		$business_head_id = config('app.business_head_id');
		$business_head_number = DB::table('user')->select('contact_no')->where('id',$business_head_id)->get()->first();
		$business_head_number = $business_head_number->contact_no;
		$date = date('d-m-Y',strtotime($date));
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
			"mobileno"=> $business_head_number,
			"templatename" => "expense_verification",
			"templateparams" => [
				["type"=> "text","text"=> "$del_boy"],
				["type"=> "text","text"=> "$date"],
				["type"=> "text","text"=> "$cash_rec_from_off"],
				["type"=> "text","text"=> "$cash_rec_from_cust"],
				["type"=> "text","text"=> "$transport"],
				["type"=> "text","text"=> "$depo_returned"],
				["type"=> "text","text"=> "$expenses"],
				["type"=> "text","text"=> "$labour_charges"],
				["type"=> "text","text"=> "$balance_cash"],
				["type"=> "text","text"=> "$receipt"]
			],
		];
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
		
		$resp = curl_exec($curl);
		curl_close($curl);
		// dd($resp);
	}


    public function getOrderExp(Request $request){
        // return $request;
        // $details = DB::table('del_orders')->where('DelAssignedTo',$request->get('username'))->where('DelDate',date('d-m-Y',strtotime($request->get('exp_date'))))->get();
        $details = DB::table('del_orders')
            ->join('order_expenses','order_expenses.order_id','=','del_orders.order_id')
            ->where('order_expenses.username',$request->get('username'))
            ->where('order_expenses.exp_date',date('Y-m-d',strtotime($request->get('exp_date'))))->get();
        foreach ($details as $key => $order) {
            $patient_name = null;
            if($order->deliverypickup=='Delivery'){
                $patient_name = DB::table('del_orders')
                                    ->select('leads.patient_name')
                                    ->join('leads','del_orders.lead_id','=','leads.id')
                                    ->where('order_id',$order->order_id)->get();
                $patient_name = $patient_name[0]->patient_name;
            }
            elseif($order->deliverypickup=='Collection'){
                $patient_name = DB::table('renewals')
                                    ->select('leads.patient_name')
                                    ->join('del_orders','del_orders.order_id','=','renewals.collection_order_id')
                                    ->join('leads','renewals.lead_id','=','leads.id')
                                    ->where('renewals.collection_order_id',$order->order_id)->get();
                $patient_name = $patient_name[0]->patient_name;
            }
            elseif($order->deliverypickup=='Pick Up'){
                $patient_name = DB::table('pickups')
                                    ->select('leads.patient_name')
                                    ->join('del_orders','del_orders.order_id','=','pickups.del_order_id')
                                    ->join('leads','del_orders.lead_id','=','leads.id')
                                    ->where('pickups.pickup_order_id',$order->order_id)->get();
                $patient_name = $patient_name[0]->patient_name;
            }

            $details[$key]->patient_name = $patient_name;
            $transport_medium = $order->transport_medium;
            $transport_medium = str_replace('[', '', $transport_medium);
            $transport_medium = str_replace(']', '', $transport_medium);
            $details[$key]->transport_medium = json_encode(explode(",",$transport_medium));
        }
        return $details;
    }

    public function updateTransMode(Request $request){
        // return $request->get('transmodes');
        $transmodes = str_replace('"','',$request->get('transmodes'));
        $transmodes ="[".$transmodes."]";
        DB::table('order_expenses')->where('id',$request->get('order_id'))->update(['transport_medium'=>$transmodes]);
        return true;
    }


    public function generateExpenseVoucher(Request $request)
    {
        $exp_details = DB::table('order_expenses')->join('delusers','delusers.username','=','order_expenses.username')->join('del_orders','del_orders.order_id','=','order_expenses.order_id')->select('order_expenses.*','delusers.city','del_orders.line_item_1','del_orders.shipping_first_name','del_orders.location','del_orders.deliverypickup')->where('order_expenses.exp_date',$request->get('exp_date'))->where('order_expenses.username',$request->get('user'))->get();
        $day_expense = DB::table('daily_expenses')->where('exp_date',$request->get('exp_date'))->where('user_name',$request->get('user'))->first();
        // $voucher_id = DB::table('misc_table')->where('field','voucher_id')->first()->value;
        $voucher_id = $day_expense->receipt_no;
        $user = DB::table('delusers')->where('username',$day_expense->user_name)->first();
        if(count($exp_details)==0){
            $exp_details = collect([(object)[
                "id"=>null,
                "order_id"=>null,
                "exp_date"=>$day_expense->exp_date,
                "username"=>$day_expense->user_name,
                "cash_rec_from_cust"=>0,
                "depo_returned"=>0,
                "labour"=>0,
                "transport"=>0,
                "transport_medium"=>0,
                "fromlocation"=>"-",
                "tolocation"=>"-",
                "hardware_expenses"=>0,
                "user_type"=>null,
                "exp_status"=>null,
                "flag"=>null,
                "created_at"=>null,
                "created_by"=>null,
                "updated_at"=>null,
                "updated_by"=>null,
                "city"=>$user->city,
                "line_item_1"=>"-",
                "shipping_first_name"=>"-",
                "location"=>"-",
                "deliverypickup"=>"-",
            ]]);
        }
        return view('ExpensesViews.generate-expense-voucher',compact('exp_details','voucher_id','day_expense'));
    }

    public function cashReport(Request $request)
    {
        if($request->get('type') == 'Lock')
        {
            // DB::table('opening_closing_balances')->where('date',$startdate)
            opening_closing_balances::updateOrCreate(
                [
                    "date"=>$request->get('date'),
                ],
                [
                    "opening_balance_ptcash"=> $request->get('opening_bal_pt_cash'),
                    "closing_balance_ptcash"=> $request->get('closing_bal_pt_cash'),
                    "opening_balance_cust_cash"=> $request->get('opening_bal_cust_cash'),
                    "closing_balance_cust_cash"=> $request->get('closing_bal_cust_cash'),
                    "locking_state"=> "Locked",
                    "created_by"=>session('username'),
                    "updated_by"=>session('username')
                ]
            );
            // return $request;
            return "Locked";
        }
        if($request->get('type') == 'Open')
        {
            DB::table('opening_closing_balances')->whereDate('date','>=',$request->get('date'))->update(['locking_state'=>'Open']);
            return "Open";
        }
        if($request->get('type') == 'getrecord')
        {
            $record = DB::table('dailycashreport')->where('id',$request->get('id'))->first();
            return json_encode($record);
        }
        if($request->get('btntype'))
        {
            if($request->get('btntype') == 'addrecord')
            {
                // dd($request->all());
                // dd($request->all(),(($request->get('recordusertype') == 'Office Staff')?1:($request->get('recordusertype') == 'DelBoy'))?2:3);
                if($request->get('recordusertype') == 'DelBoy')
                {
                    $person = $request->get('recorduserdb');
                }
                else if($request->get('recordusertype') == 'Office Staff')
                {
                    $person = $request->get('recorduseros');
                }
                else
                {
                    $person = $request->get('recorduseroth');
                }
                $inserted = DB::table('dailycashreport')->insert([
                    'date'=>$request->get('recorddate'),
                    // 'person'=>($request->get('recordusertype') == 'DelBoy')?$request->get('recorduserdb'):($request->get('recordusertype') == 'Office Staff')?$request->get('recorduseros'):$request->get('recorduseroth'),
                    'person'=>$person,
                    'usertype'=>$request->get('recordusertype'),
                    'amount'=>$request->get('recordamount'),
                    'rec_paid'=>$request->get('recordcashmode'),
                    'purpose'=>$request->get('recordpurpose'),
                    'remark'=>$request->get('recordremark'),
                    'created_by'=>session('username')
                ]);
                return redirect()->back()->with('message','Record Added Successfully!');
            }
            elseif($request->get('btntype') == 'editrecord')
            {
                // dd($request->all());
                DB::table('dailycashreport')->where('id',$request->get('editid'))->update([
                    'amount'=>$request->get('recordamountedit'),
                    'purpose'=>$request->get('recordpurposeedit'),
                    'remark'=>$request->get('recordremarkedit'),
                ]);
                return redirect()->back()->with('message','Record Updated Successfully!');
            }
        }
        if($request->get('filterdate'))
        {
            $enddate = $request->get('filterdate');
        }
        else
        {
            $enddate = Carbon::now()->toDateString();
        }
        if(Carbon::parse($enddate)->format('l') == 'Monday')
        {
            $startdate = Carbon::parse($enddate)->subdays(2)->toDateString();
        }
        else
        {
            $startdate = Carbon::parse($enddate)->subdays(1)->toDateString();
        }
        $lastdate = Carbon::parse($enddate)->subdays(1)->toDateString();
        // dd($startdate,$enddate);
        $officestaff = DB::table('user')->where('flag','Active')->whereIn('role',['user','admin','superuser'])->get();
        $delboys = DB::table('delusers')->where('status','Active')->whereNotNull('password')->where('role','User')->get();
        $received_delboys = DB::table('daily_expenses')->select('user_name','received_cash','cash_received_from_customer')->whereBetween('exp_date',[$startdate,$lastdate])->whereNotNull('received_cash')->whereNotIn('received_cash',['0',0,''])->get();
        $received_customers = DB::table('order_expenses')->join('del_orders','order_expenses.order_id','=','del_orders.order_id')->select('del_orders.shipping_first_name','order_expenses.cash_rec_from_cust','order_expenses.username')->whereNotNull('order_expenses.cash_rec_from_cust')->whereNotIn('order_expenses.cash_rec_from_cust',[0,'','0'])->whereBetween('exp_date',[$startdate,$lastdate])->get();
        $paid_delboys = DB::table('dailycashreport')->where('date',$enddate)->where('rec_paid','Paid')->whereNotIn('usertype',['Other'])->get();
        $paid_others = DB::table('dailycashreport')->where('date',$enddate)->where('rec_paid','Paid')->where('usertype','Other')->get();
        // dd($paid_others);
        $opening_closing_balance = DB::table('opening_closing_balances')->where('date',$startdate)->first();
        if(DB::table('opening_closing_balances')->where('date',$enddate)->exists())
        {
            $locking_state = DB::table('opening_closing_balances')->where('date',$enddate)->first()->locking_state;
        }
        else
        {
            $locking_state = "Open";
        }
            

        // dd($opening_closing_balance);
        if(!$opening_closing_balance){
            $opening_closing_balance = (object)([
                "id"=> 1,
                "date"=> $enddate,
                "opening_balance_ptcash"=> 0,
                "closing_balance_ptcash"=> 0,
                "opening_balance_cust_cash"=> 0,
                "closing_balance_cust_cash"=> 0,
                "locking_state"=> "Open",
            ]);
        }
        $count = max(count($received_delboys),count($received_customers),count($paid_delboys),count($paid_others));
        // dd(count($received_delboys),count($received_customers),count($paid_delboys),count($paid_others));
        // dd($opening_closing_balance);
        return view('ExpensesViews.cash-report',compact('received_delboys','delboys','officestaff','received_customers','paid_delboys','paid_others','count','opening_closing_balance','enddate','startdate','locking_state'));
    }
    
    public function xmlExport(Request $request){
        if($request->has('expids')){
            $expids = json_decode($request->get('expids'),true);
            $getexpenses = DB::table('daily_expenses')->whereIn('id',$expids)->get()->groupBy('exp_date');
            //update xml date and 
            DB::table('daily_expenses')->whereIn('id',$expids)->update(['xml'=>'Y','xml_generated_at'=>Carbon::now()->toDateTimeString()]);
            return view('ExpensesViews.xmlTemplate',compact('getexpenses'));
            // return response(view('ExpensesViews.xmlTemplate')->with(compact('getexpenses')), 200, [
            //     'Content-Type' => 'application/xml', // use your required mime type
            //     'Content-Disposition' => 'attachment; filename="filename.xml"',
            // ]);
        }
    }
}
?>