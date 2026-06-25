<?php

namespace App\Http\Controllers\TestController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReminderOverdueMail;

class TestController extends Controller
{
    public function runCronJob(Request $request)
    {
        // ---------- In Process Leads ---------- //
        $today = Carbon::today()->toDateString();
        $dayBefore = Carbon::now()->subDay("2")->toDateString();

        // ---------- Converted Pending Assign ----------- //
        $converted_leads = DB::table('leads')->where('lead_status','Converted')->where('creation_date',$today)->orderBy('id','DESC')->get();
        // dd($converted_leads);
        $lead_30_count = 0;
        $lead_30_data = Collect();

        $lead_60_count = 0;
        $lead_60_data = Collect();

        $lead_90_count = 0;
        $lead_90_data = Collect();

        $lead_a90_count = 0;
        $lead_a90_data = Collect();
        foreach($converted_leads as $key=>$lead)
        {
            $finishTime = Carbon::now();
            $totalDuration = $finishTime->diffInMinutes($lead->converted_at);
            // dd($totalDuration);
            if($totalDuration >= 30 && $totalDuration < 60 && $lead->reminder_state == 0)
            {
                $lead_30_count ++;
                DB::table('leads')->where('id',$lead->id)->update(['reminder_state'=>1]);
                $getLeadData = $this->getLeadData($lead->id);
                $lead_30_data->push($getLeadData);
            }
            else if($totalDuration >= 60 && $totalDuration < 90 && $lead->reminder_state == 1)
            {
                $lead_60_count ++;
                DB::table('leads')->where('id',$lead->id)->update(['reminder_state'=>2]);
                $getLeadData = $this->getLeadData($lead->id);
                $lead_60_data->push($getLeadData);
            }
            else if($totalDuration >= 90 && $lead->reminder_state == 2)
            {
                $lead_90_count ++;
                DB::table('leads')->where('id',$lead->id)->update(['reminder_state'=>3,'reminder_time'=>Carbon::now()]);
                $getLeadData = $this->getLeadData($lead->id);
                $lead_90_data->push($getLeadData);
            }
            else if($lead->reminder_state == 3 && $finishTime->diffInMinutes($lead->reminder_time) >=60)
            {
                $lead_a90_count++;
                DB::table('leads')->where('id',$lead->id)->update(['reminder_state'=>3,'reminder_time'=>Carbon::now()]);
                $getLeadData = $this->getLeadData($lead->id);
                $lead_a90_data->push($getLeadData);
            }
        }
        
        // dd($lead_30_count,$lead_60_count,$lead_90_count,$lead_a90_count);
        
        if($lead_30_count != 0)
        {
            $this->sendMail(config('app.cron_job_mail'),'abhishekn@quali55care.com','Attention : '.$lead_30_count.' Order(s) pending for vendor assignment', ['link'=>null,'message1'=>'Check Pending Assignment View'],$lead_30_data,'converted_lead'); 
        }
        else if($lead_60_count != 0)
        {
            $this->sendMail(config('app.cron_job_mail'),'abhishekn@quali55care.com','Escalation : '.$lead_60_count.' Order(s) pending for vendor assignment', ['link'=>null,'message1'=>'Check Pending Assignment View'],$lead_60_data,'converted_lead'); 
        }
        else if($lead_90_count != 0)
        {
            $this->sendMail(config('app.cron_job_mail'),'abhishekn@quali55care.com','Immediate action : '.$lead_90_count.' Order(s) pending for vendor assignment', ['link'=>null,'message1'=>'Check Pending Assignment View'],$lead_90_data,'converted_lead'); 
        }
        else if($lead_a90_count != 0)
        {
            $this->sendMail(config('app.cron_job_mail'),'abhishekn@quali55care.com','Immediate action : '.$lead_a90_count.' Order(s) pending for vendor assignment', ['link'=>null,'message1'=>'Check Pending Assignment View'],$lead_a90_data,'converted_lead'); 
        }

        // $this->sendMail(config('app.cron_job_mail'),'abhishekn@quali55care.com','Attention : '..'Order(s) pending for vendor assignment', ['link'=>null,'message1'=>'Check Pending Assignment View']);


        // --------------pending for delivery assignment-------------- //
        $del_today = date('d-m-Y');
        $orders = DB::table('del_orders')->where('deliverypickup','Delivery')->where('status','Pending')->where('DelDate',$del_today)->get();
        $order_30_count = 0;
        $order_30_data = Collect();

        $order_60_count = 0;
        $order_60_data = Collect();

        $order_90_count = 0;
        $order_90_data = Collect();

        $order_a90_count = 0;
        $order_a90_data = Collect();
        foreach($orders as $key=>$order)
        {
            $finishTime = Carbon::now();
            $totalDuration = $finishTime->diffInMinutes($order->created_at);
            // dd($totalDuration);
            if($totalDuration >= 30 && $totalDuration < 60 && $order->reminder_state == 0)
            {
                $order_30_count ++;
                DB::table('del_orders')->where('order_id',$order->order_id)->update(['reminder_state'=>1]);
                $getOrderData = $this->getOrderData($order->order_id);
                $order_30_data->push($getOrderData);
            }
            else if($totalDuration >= 60 && $totalDuration < 90 && $order->reminder_state == 1)
            {
                $order_60_count ++;
                DB::table('del_orders')->where('order_id',$order->order_id)->update(['reminder_state'=>2]);
                $getOrderData = $this->getOrderData($order->order_id);
                $order_60_data->push($getOrderData);
            }
            else if($totalDuration >= 90 && $order->reminder_state == 2)
            {
                $order_90_count ++;
                DB::table('del_orders')->where('order_id',$order->order_id)->update(['reminder_state'=>3,'reminder_time'=>Carbon::now()]);
                $getOrderData = $this->getOrderData($order->order_id);
                $order_90_data->push($getOrderData);
            }
            else if($order->reminder_state == 3 && $finishTime->diffInMinutes($order->reminder_time) >=60)
            {
                $order_a90_count++;
                DB::table('del_orders')->where('order_id',$order->order_id)->update(['reminder_state'=>3,'reminder_time'=>Carbon::now()]);
                $getOrderData = $this->getOrderData($order->order_id);
                $order_a90_data->push($getOrderData);
            }
        }
        
        // dd($order_30_count,$order_60_count,$order_90_count,$order_a90_count);
        
        if($order_30_count != 0)
        {
            $this->sendMail(config('app.cron_job_mail'),'abhishekn@quali55care.com','Attention : '.$order_30_count.' Order(s) pending for delivery assignment', ['link'=>null,'message1'=>'Check Pending Assignment View'],$order_30_data,'pending_delivery'); 
        }
        else if($order_60_count != 0)
        {
            $this->sendMail(config('app.cron_job_mail'),'abhishekn@quali55care.com','Escalation : '.$order_60_count.' Order(s) pending for delivery assignment', ['link'=>null,'message1'=>'Check Pending Assignment View'],$order_60_data,'pending_delivery'); 
        }
        else if($order_90_count != 0)
        {
            $this->sendMail(config('app.cron_job_mail'),'abhishekn@quali55care.com','Immediate action : '.$order_90_count.' Order(s) pending for delivery assignment', ['link'=>null,'message1'=>'Check Pending Assignment View'],$order_90_data,'pending_delivery'); 
        }
        else if($order_a90_count != 0)
        {
            $this->sendMail(config('app.cron_job_mail'),'abhishekn@quali55care.com','Immediate action : '.$order_a90_count.' Order(s) pending for delivery assignment', ['link'=>null,'message1'=>'Check Pending Assignment View'],$order_a90_data,'pending_delivery'); 
        }

        $assigned_orders = DB::table('del_orders')
                                ->join('activity_log','activity_log.key_id','=','del_orders.order_id')
                                ->select(
                                    'del_orders.*',
                                    'activity_log.updated_at as activity_updated_at'
                                )
                                ->whereNotIn('del_orders.status',['Pending','Collected','Picked up','Delivered'])
                                ->where('activity_log.operation','Order Assigned')
                                ->where('activity_log.fields','DelAssignedTo')
                                ->where('DelDate',$del_today)
                                ->get();
        // dd($assigned_orders);
        $assigned_order_30_count = 0;
        $assigned_order_30_data = Collect();

        $assigned_order_60_count = 0;
        $assigned_order_60_data = Collect();

        $assigned_order_90_count = 0;
        $assigned_order_90_data = Collect();

        $assigned_order_a90_count = 0;
        $assigned_order_a90_data = Collect();

        foreach($assigned_orders as $key=>$order)
        {
            $finishTime = Carbon::now();
            $totalDuration = $finishTime->diffInMinutes($order->activity_updated_at);
            // dd($totalDuration);
            if($totalDuration >= 30 && $totalDuration < 60 && $order->reminder_state == 0)
            {
                $assigned_order_30_count ++;
                DB::table('del_orders')->where('order_id',$order->order_id)->update(['reminder_state'=>1]);
                $getOrderData = $this->getOrderData($order->order_id);
                $assigned_order_30_data->push($getOrderData);
            }
            else if($totalDuration >= 60 && $totalDuration < 90 && $order->reminder_state == 1)
            {
                $assigned_order_60_count ++;
                DB::table('del_orders')->where('order_id',$order->order_id)->update(['reminder_state'=>2]);
                $getOrderData = $this->getOrderData($order->order_id);
                $assigned_order_60_data->push($getOrderData);
            }
            else if($totalDuration >= 90 && $order->reminder_state == 2)
            {
                $assigned_order_90_count ++;
                DB::table('del_orders')->where('order_id',$order->order_id)->update(['reminder_state'=>3,'reminder_time'=>Carbon::now()]);
                $getOrderData = $this->getOrderData($order->order_id);
                $assigned_order_90_data->push($getOrderData);
            }
            else if($order->reminder_state == 3 && $finishTime->diffInMinutes($order->reminder_time) >=60)
            {
                $assigned_order_a90_count++;
                DB::table('del_orders')->where('order_id',$order->order_id)->update(['reminder_state'=>3,'reminder_time'=>Carbon::now()]);
                $getOrderData = $this->getOrderData($order->order_id);
                $assigned_order_a90_data->push($getOrderData);
            }
        }
        
        // dd($assigned_order_30_count,$assigned_order_60_count,$assigned_order_90_count,$assigned_order_a90_count);
        
        if($assigned_order_30_count != 0)
        {
            $this->sendMail(config('app.cron_job_mail'),'abhishekn@quali55care.com','Attention : '.$assigned_order_30_count.' Delivery/pickup/collection not completed.', ['link'=>null,'message1'=>'Check Pending Assignment View'],$assigned_order_30_data,'pending_delivery'); 
        }
        else if($assigned_order_60_count != 0)
        {
            $this->sendMail(config('app.cron_job_mail'),'abhishekn@quali55care.com','Escalation : '.$assigned_order_60_count.' Delivery/pickup/collection not completed.', ['link'=>null,'message1'=>'Check Pending Assignment View'],$assigned_order_60_data,'pending_delivery'); 
        }
        else if($assigned_order_90_count != 0)
        {
            $this->sendMail(config('app.cron_job_mail'),'abhishekn@quali55care.com','Immediate action : '.$assigned_order_90_count.' Delivery/pickup/collection not completed.', ['link'=>null,'message1'=>'Check Pending Assignment View'],$assigned_order_90_data,'pending_delivery'); 
        }
        else if($assigned_order_a90_count != 0)
        {
            $this->sendMail(config('app.cron_job_mail'),'abhishekn@quali55care.com','Immediate action : '.$assigned_order_a90_count.' Delivery/pickup/collection not completed.', ['link'=>null,'message1'=>'Check Pending Assignment View'],$assigned_order_a90_data,'pending_delivery'); 
        }
        
        $assigned_orders = DB::table('del_orders')
                                ->join('order_details','order_details.order_id','=','del_orders.order_id')
                                ->join('activity_log','activity_log.key_id','=','order_details.id')
                                ->select(
                                    'del_orders.*',
                                    'activity_log.updated_at as activity_updated_at'
                                )
                                // ->whereNotIn('del_orders.status',['Pending','Collected','Picked up','Delivered'])
                                ->where('activity_log.operation','Stop Request Product')
                                ->where('activity_log.fields','current_status')
                                ->where('del_orders.DelDate',$del_today)
                                ->get();
        // dd($assigned_orders);
        $assigned_order_30_count = 0;
        $assigned_order_30_data = Collect();

        $assigned_order_60_count = 0;
        $assigned_order_60_data = Collect();

        $assigned_order_90_count = 0;
        $assigned_order_90_data = Collect();

        $assigned_order_a90_count = 0;
        $assigned_order_a90_data = Collect();

        foreach($assigned_orders as $key=>$order)
        {
            $finishTime = Carbon::now();
            $totalDuration = $finishTime->diffInMinutes($order->activity_updated_at);
            // dd($totalDuration);
            if($totalDuration >= 1440 && $totalDuration < 2880 && $order->reminder_state == 0)
            {
                $assigned_order_30_count ++;
                DB::table('del_orders')->where('order_id',$order->order_id)->update(['reminder_state'=>1]);
                $getOrderData = $this->getOrderData($order->order_id);
                $assigned_order_30_data->push($getOrderData);
            }
            else if($totalDuration >= 2880 && $totalDuration < 4320 && $order->reminder_state == 1)
            {
                $assigned_order_60_count ++;
                DB::table('del_orders')->where('order_id',$order->order_id)->update(['reminder_state'=>2]);
                $getOrderData = $this->getOrderData($order->order_id);
                $assigned_order_60_data->push($getOrderData);
            }
            else if($totalDuration >= 4320 && $order->reminder_state == 2)
            {
                $assigned_order_90_count ++;
                DB::table('del_orders')->where('order_id',$order->order_id)->update(['reminder_state'=>3,'reminder_time'=>Carbon::now()]);
                $getOrderData = $this->getOrderData($order->order_id);
                $assigned_order_90_data->push($getOrderData);
            }
            else if($order->reminder_state == 3 && $finishTime->diffInMinutes($order->reminder_time) >=2880)
            {
                $assigned_order_a90_count++;
                DB::table('del_orders')->where('order_id',$order->order_id)->update(['reminder_state'=>3,'reminder_time'=>Carbon::now()]);
                $getOrderData = $this->getOrderData($order->order_id);
                $assigned_order_a90_data->push($getOrderData);
            }
        }
        // dd($assigned_order_30_count,$assigned_order_60_count,$assigned_order_90_count,$assigned_order_a90_count);
        
        if($assigned_order_30_count != 0)
        {
            $this->sendMail(config('app.cron_job_mail'),'abhishekn@quali55care.com','Attention : '.$assigned_order_30_count.' Delivery/pickup/collection not completed.', ['link'=>null,'message1'=>'Check Pending Assignment View'],$assigned_order_30_data,'pending_delivery'); 
        }
        else if($assigned_order_60_count != 0)
        {
            $this->sendMail(config('app.cron_job_mail'),'abhishekn@quali55care.com','Escalation : '.$assigned_order_60_count.' Delivery/pickup/collection not completed.', ['link'=>null,'message1'=>'Check Pending Assignment View'],$assigned_order_60_data,'pending_delivery'); 
        }
        else if($assigned_order_90_count != 0)
        {
            $this->sendMail(config('app.cron_job_mail'),'abhishekn@quali55care.com','Immediate action : '.$assigned_order_90_count.' Delivery/pickup/collection not completed.', ['link'=>null,'message1'=>'Check Pending Assignment View'],$assigned_order_90_data,'pending_delivery'); 
        }
        else if($assigned_order_a90_count != 0)
        {
            $this->sendMail(config('app.cron_job_mail'),'abhishekn@quali55care.com','Immediate action : '.$assigned_order_a90_count.' Delivery/pickup/collection not completed.', ['link'=>null,'message1'=>'Check Pending Assignment View'],$assigned_order_a90_data,'pending_delivery'); 
        }

        return "Yes";


    }

    public function sendMail($recipients,$cc, $subject, $body,$excelData,$excelType)
    {
        // dd($body);
        // $body = ['message'=>$body];
        Mail::send('Mail.reminder-mail',$body, function($message) use ($recipients,$subject,$cc,$excelData,$excelType)
        {    
            foreach($recipients as $key=>$mail)
            {
                $message->to($mail,$mail)->subject($subject);
            }
            // $message->cc($cc,$cc)->subject($subject);
            $message->from('tempmailquali@gmail.com', 'Quali55Care');
            if(!in_array($excelType,['overdue_individual_all','overdue_individual_leadowner','overdue_corporate_all','overdue_corporate_leadowner'])){
                $excelFile = Excel::raw(new ReminderOverdueMail($excelData,$excelType),\Maatwebsite\Excel\Excel::XLSX);
                $message->attachData($excelFile,'reminder.xls');
            }
            //$message->attachData($pdf_file,'Challan.pdf');
            //$message->attach(asset($challan_file), ['mime' => 'application/pdf']);
        });
    }
    // public function sendMail1($recipients,$cc, $subject, $body,$excelData,$excelType)
    // {
    //     // dd($body);
    //     // $body = ['message'=>$body];
    //     Mail::send('Mail.reminder-mail',$body, function($message) use ($recipients,$subject,$cc,$excelData,$excelType)
    //     {    
    //         foreach($recipients as $key=>$mail)
    //         {
    //             $message->to($mail,$mail)->subject($subject);
    //         }
    //         $message->cc($cc,$cc)->subject($subject);
    //         $message->from('tempmailquali@gmail.com', 'Quali55Care');
    //         if(!in_array($excelType,['overdue_individual_all','overdue_individual_leadowner','overdue_corporate_all','overdue_corporate_leadowner'])){
    //             $excelFile = Excel::raw(new ReminderOverdueMail($excelData,$excelType),\Maatwebsite\Excel\Excel::XLSX);
    //             $message->attachData($excelFile,'reminder.xls');
    //         }
    //         //$message->attachData($pdf_file,'Challan.pdf');
    //         //$message->attach(asset($challan_file), ['mime' => 'application/pdf']);
    //     });
    // }


    public function overdueIndividual()
    {
        $overdue_period = Carbon::now()->subDay("5")->toDateString();
        // $overdue_period = Carbon::now()->toDateString();
        $overdue_data = DB::table('order_details')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                ->join('del_orders','order_details.order_id','=','del_orders.order_id')
                                ->join('leads','leads.id','=','del_orders.lead_id')
                                ->select(
                                    'order_details.customer_id',
                                    'order_details.product_id',
                                    'order_details.pickup_date',
                                    'customer_details.customer_type',
                                    'leads.lead_owner'
                                )
                                ->whereNotIn('del_orders.status',['Cancel'])
                                // ->where('customer_details.customer_name','LIKE','%'.'apollo'.'%')
                                ->where('order_details.sale_rental','Rental')
                                ->where('order_details.pickup_date','<',$overdue_period)
                                ->where('customer_details.customer_type','Individual')
                                // ->where('customer_details.customer_type','Corporate')
                                ->whereNotIn('order_details.current_status',['Pending Pickup','Picked Up','Cancel','CustStop'])
                                ->get();
        // dd($overdue_data);
        $cust_count = $overdue_data->groupBy('customer_id');
        if(count($cust_count) !=0)
        {
            // $link = route('renewalpickup-test',['date_filter'=>'Overdue','customer_type'=>'Individual','overdue_less_date'=>$overdue_period]);
            $link = "http://intra.quali55care.com/".config('app.app_env')."/eflow/renewalpickup-test?date_filter=Overdue&customer_type=Individual&overdue_less_date=$overdue_period";
            $this->sendMail(config('app.cron_job_mail'),'abhishekn@quali55care.com','Reminder : '.count($cust_count).' Customer(s) Payment over due more than 5 days.', ['message1'=>'View Renewal Pickup View','link'=>$link],null,'overdue_individual_all'); 
        }
        $owner_data = $overdue_data->groupBy('lead_owner');
        
        foreach($owner_data as $key=>$value)
        {
            $owner_cust_count = $value->groupBy('customer_id');
            // dd(count($owner_cust_count));
            $owner_email = DB::table('user')->where('id',$key)->first('email_id_user')->email_id_user;
            // $link = route('renewalpickup-test',['date_filter'=>'Overdue','customer_type'=>'Individual','overdue_less_date'=>$overdue_period,'lead_user'=>$key]);
            $link = "http://intra.quali55care.com/".config('app.app_env')."/eflow/renewalpickup-test?date_filter=Overdue&customer_type=Individual&overdue_less_date=$overdue_period&lead_user=$key";
            $this->sendMail([$owner_email],'abhishekn@quali55care.com','Reminder : '.count($owner_cust_count).' Customer(s) Payment over due more than 5 days.', ['message1'=>'View Renewal Pickup View','link'=>$link],null,'overdue_individual_leadowner'); 
        }

    }

    public function overdueCorpoprate()
    {
        $overdue_period = Carbon::now()->subDay("30")->toDateString();
        // $overdue_period = Carbon::now()->toDateString();
        $overdue_data = DB::table('order_details')
                                ->join('customer_details','customer_details.cust_id','=','order_details.customer_id')
                                ->join('del_orders','order_details.order_id','=','del_orders.order_id')
                                ->join('leads','leads.id','=','del_orders.lead_id')
                                ->select(
                                    'order_details.customer_id',
                                    'order_details.product_id',
                                    'order_details.pickup_date',
                                    'customer_details.customer_type',
                                    'leads.lead_owner'
                                )
                                ->whereNotIn('del_orders.status',['Cancel'])
                                // ->where('customer_details.customer_name','LIKE','%'.'apollo'.'%')
                                ->where('order_details.sale_rental','Rental')
                                ->where('order_details.pickup_date','<',$overdue_period)
                                ->where('customer_details.customer_type','Corporate')
                                // ->where('customer_details.customer_type','Corporate')
                                ->whereNotIn('order_details.current_status',['Pending Pickup','Picked Up','Cancel','CustStop'])
                                ->get();
        // dd($overdue_data);
        $cust_count = $overdue_data->groupBy('customer_id');
        if(count($cust_count) !=0)
        {
            // $link = route('renewalpickup-test',['date_filter'=>'Overdue','customer_type'=>'Corporate','overdue_less_date'=>$overdue_period]);
            $link = "http://intra.quali55care.com/".config('app.app_env')."/eflow/renewalpickup-test?date_filter=Overdue&customer_type=Corporate&overdue_less_date=$overdue_period";
            $this->sendMail(config('app.cron_job_mail'),'abhishekn@quali55care.com','Reminder : '.count($cust_count).' Customer(s) Payment not received for more than 30 days.', ['message1'=>'View Renewal Pickup View','link'=>$link],null,'overdue_corporate_all'); 
        }
        $owner_data = $overdue_data->groupBy('lead_owner');
        
        foreach($owner_data as $key=>$value)
        {
            $owner_cust_count = $value->groupBy('customer_id');
            // dd(count($owner_cust_count));
            $owner_email = DB::table('user')->where('id',$key)->first('email_id_user')->email_id_user;
            // $link = route('renewalpickup-test',['date_filter'=>'Overdue','customer_type'=>'Corporate','overdue_less_date'=>$overdue_period,'lead_user'=>$key]);
            $link = "http://intra.quali55care.com/".config('app.app_env')."/eflow/renewalpickup-test?date_filter=Overdue&customer_type=Corporate&overdue_less_date=$overdue_period&lead_user=$key";

            $this->sendMail([$owner_email],'abhishekn@quali55care.com','Reminder : '.count($owner_cust_count).' Customer(s) Payment not received for more than 30 days.', ['message1'=>'View Renewal Pickup View','link'=>$link],null,'overdue_corporate_leadowner'); 
        }
    }

    public function getLeadData($leadId)
    {
        $getLeadData = DB::table('leads')
            ->join('customer_details','leads.customer_id','=','customer_details.cust_id')
            ->select('leads.*','customer_details.customer_name','customer_details.primary_contact_no')
            ->where('id',$leadId)->first();
        $productIds = json_decode($getLeadData->equipment_requirement);
        $productName = DB::table('products')->whereIn('id',$productIds)->get('product_name')->pluck('product_name');
        $getLeadData->product_name = $productName;
        return $getLeadData;
    }
    public function getOrderData($orderId)
    {
        $getOrderData = DB::table('del_orders')->where('order_id',$orderId)->first();
        return $getOrderData;
    }

    public function Test(){
        $link = "http://intra.quali55care.com/".config('app.app_env')."/eflow/renewalpickup-test?date_filter=Overdue&customer_type=Corporate";
        dd($link);
    }
}
