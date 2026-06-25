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

class PendingPayment implements FromView
{
    // use Exportable;
    protected $details_count;
    protected $count_array;
    protected $amount_array;
    

    function __construct($details_count,$count_array,$amount_array) 
    {
        $this->details_count = $details_count;
        $this->count_array = $count_array;
        $this->amount_array = $amount_array;
    }
    // public function headings(): array
    // {
    //     return ["Date", "Order Id", "Customer Name","Contact No","Order Type","Delivery Status"];
    // }
    public function view(): View
    {
        $details_count = $this->details_count;
        $count_array = $this->count_array;
        $amount_array = $this->amount_array;
        return view('export_views.export_unsettled_orders',compact('details_count','amount_array','count_array'));
    }
    // public function array(): array
    // {
    //     return $this->get_all_orders;
    //     //print_r($this->get_all_orders);
    // }
}
?>