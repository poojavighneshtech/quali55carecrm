<?php

namespace App\Exports;

use App\Models\Lead\customer_detail;
use App\Models\Lead\lead;
use App\Models\DelOrders;
use App\Models\OrderDetails;
use App\Models\Renewal;
use App\Models\Pickup;
use App\Models\VendorRegister;
use App\Models\MasterProduct;
use App\Models\VendorWarehouse;
use App\Models\VendorProducts;

use App\Models\Lead\leads_log;

use Illuminate\Contracts\View\View;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;

class MisExport implements FromView
{
    // use Exportable;
    protected $mis_report_details;
    protected $renewal_data;
    protected $pickup_data;

    function __construct($mis_report_details,$renewal_data,$pickup_data) 
    {
        $this->mis_report_details = $mis_report_details;
        $this->renewal_data = $renewal_data;
        $this->pickup_data = $pickup_data;
    }
    // public function headings(): array
    // {
    //     return ["Date", "Order Id", "Customer Name","Contact No","Order Type","Delivery Status"];
    // }
    public function view(): View
    {
        $mis_report_details = $this->mis_report_details;
        $renewal_data = $this->renewal_data;
        $pickup_data = $this->pickup_data;
        return view('export_views.export_mis',compact('mis_report_details','pickup_data','renewal_data'));
    }
    // public function array(): array
    // {
    //     return $this->get_all_orders;
    //     //print_r($this->get_all_orders);
    // }
}
?>