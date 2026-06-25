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

class OrderDetailsReportExport implements FromView
{
    // use Exportable;
    // protected $details_count;
    // protected $count_array;
    // protected $amount_array;
    protected $products;
    protected $order_details;
    protected $filter_data;
    protected $count;

    function __construct($products,$order_details,$filter_data,$count) 
    {
        // $this->details_count = $details_count;
        // $this->count_array = $count_array;
        // $this->amount_array = $amount_array;
        $this->products = $products;
        $this->order_details = $order_details;
        $this->filter_data = $filter_data;
        $this->count = $count;
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
        $products = $this->products;
        $order_details = $this->order_details;
        $filter_data = $this->filter_data;
        $count = $this->count;
        return view('export_views.export_order_details_report',compact('products','order_details','filter_data','count'));
    }
    // public function array(): array
    // {
    //     return $this->get_all_orders;
    //     //print_r($this->get_all_orders);
    // }
}
?>