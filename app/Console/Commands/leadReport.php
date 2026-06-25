<?php

namespace App\Console\Commands;

use App\Models\UserRegister;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\ComplaintManagement\ComplaintController;
use App\Http\Controllers\Leads\LeadController;
use App\Http\Controllers\Expense\ExpenseController;

class leadReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leadreport:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a daily mail to users about leads information';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = DB::select("SELECT * FROM user WHERE role = 'user' AND email_id_user IS NOT NULL");
        $data['users'] = json_decode(json_encode($users), true);
        $admin_mail = array();
        $i=0;
        foreach ($data['users'] as $user)
        {
            $lead_owner = $user['id'];
            $lead_owner_name = $user['username'];
            $date = date('Y-m-d',strtotime('-1 days'));
            $lead_details = DB::select("SELECT * FROM leads WHERE lead_owner = $lead_owner AND DATE(leads.converted_at) = '$date'");
            $data['lead_details'] = json_decode(json_encode($lead_details),true);
            $temp_count_process = 0;
            $temp_count_close = 0;
            $temp_count_convert = 0;
            $total_leads = count($data['lead_details']);
            foreach($data['lead_details'] as $lead_details)
            {
                if($lead_details['lead_status'] == "Work In Process")
                {
                $temp_count_process = $temp_count_process + 1;
                }
                elseif($lead_details['lead_status'] == "Converted" OR $lead_details['lead_status'] == "Vendor Assigned" OR $lead_details['lead_status'] == "Delivery In Progress" OR $lead_details['lead_status'] == "Order Generated")                
                {
                $temp_count_convert = $temp_count_convert + 1;
                }
                elseif($lead_details['lead_status'] != "Work In Process" OR $lead_details['lead_status'] != "Converted" OR $lead_details['lead_status'] != "Vendor Assigned" OR $lead_details['lead_status'] != "Delivery In Progress" OR $lead_details['lead_status'] != "Mobile Generated")
                {
                $temp_count_close = $temp_count_close + 1;
                }
            }
            $report_data = array(
                'username'=>$lead_owner_name,
                'total'=>$total_leads,
                'in_process'=>$temp_count_process,
                'converted'=>$temp_count_convert,
                'closed'=>$temp_count_close,
                );
            $admin_mail[$i]['username'] = $lead_owner_name;
            $admin_mail[$i]['total'] = $total_leads;
            $admin_mail[$i]['in_process'] = $temp_count_process;
            $admin_mail[$i]['converted'] = $temp_count_convert;
            $admin_mail[$i]['closed'] = $temp_count_close;
                //$data_message['mail_data'] = $mail_data;
            $username = $user['username'];
            $user_email = $user['email_id_user'];
            $data['admin_mail']= $admin_mail;
            Mail::send('ReportMails/LeadReportUserMail',$report_data, function($message) use ($user_email,$username)
            {
                $message->to($user_email, $username)->subject('Quali55Care -Lead Report');
                $message->from('tempmailquali@gmail.com', 'Quali55Care');
            });
            $i++;
        }
        
        $complaint = new ComplaintController();
        $complaint->EscalationMail();
        $new_site_daily_orders = new LeadController();        
        $new_site_daily_orders->new_site_daily_orders1();
        //$expController = new ExpenseController();
        //$expController->expReportDaily();
        $leadController = new LeadController();
        $leadController->processLeads();
        $leadController->userLeadSummary(date('Y-m-d',strtotime('-1 days')));
        $leadController = new LeadController();
        $leadController->diapersAdvertisement();

    }
}
