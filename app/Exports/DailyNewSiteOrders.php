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

class DailyNewSiteOrders implements FromView
{
    // use Exportable;
    protected $details_count;

    

    function __construct($details_count) 
    {
        $this->details_count = $details_count;

    }
    // public function headings(): array
    // {
    //     return ["Date", "Order Id", "Customer Name","Contact No","Order Type","Delivery Status"];
    // }
    public function view(): View
    {
        $details_count = $this->details_count;
        return view('export_views.export_daily_new_site_orders',compact('details_count'));
    }
    // public function array(): array
    // {
    //     return $this->get_all_orders;
    //     //print_r($this->get_all_orders);
    // }
}
?>