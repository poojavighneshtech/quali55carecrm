<?php

namespace App\Exports;

use App\Models\VendorRegister;
use App\Models\UserRegister;
use App\Models\DelOrders;
use App\Models\OrderDetails;
use App\Models\customer_detail;
use App\Models\ActivityLog;
use App\Models\Renewal;
use App\Models\Pickup;

use PDF;
use Mail;
use Session;
use DateTime;


use Illuminate\Contracts\View\View;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;

class GoogleAdsCampaign implements FromView
{
    // use Exportable;
    
    protected $google_campaign_report;
    protected $filter_data;
    protected $count;
    protected $campaign_names;


    function __construct($google_campaign_report,$filter_data,$count,$campaign_names) 
    {
        $this->google_campaign_report = $google_campaign_report;
        $this->filter_data = $filter_data;
        $this->count = $count;
        $this->campaign_names = $campaign_names;
    }
    // public function headings(): array
    // {
    //     return ["Date", "Order Id", "Customer Name","Contact No","Order Type","Delivery Status"];
    // }
    public function view(): View
    {
        // $details_count = $this->details_count;
        // $count_array = $this->count_array;
        // $amount_array = $this->amount_array;
        $google_campaign_report = $this->google_campaign_report;
        $filter_data = $this->filter_data;
        $count = $this->count;
        $campaign_names = $this->campaign_names;
        return view('export_views.exportgoogleadscampaign',compact('google_campaign_report','filter_data','count','campaign_names'));
    }
    // public function array(): array
    // {
    //     return $this->get_all_orders;
    //     //print_r($this->get_all_orders);
    // }
}
?>