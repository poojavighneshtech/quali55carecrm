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

class ConvertedOrdersReport implements FromView
{
    // use Exportable;
    // protected $details_count;
    // protected $count_array;
    // protected $amount_array;
    protected $get_all_leads;
    protected $all_leads_products;
    protected $filter_arr;
    protected $get_lead_owners;
    protected $cust_or_pay_status;
    protected $cities;
    protected $getOrderStatuses;
    protected $ordersStateCount;
    protected $orderConvertedCount;
    protected $totalOrderCount;

    function __construct($get_all_leads,$all_leads_products,$filter_arr,$get_lead_owners,$cust_or_pay_status,$cities,$getOrderStatuses,$ordersStateCount,$orderConvertedCount,$totalOrderCount) 
    {
        // $this->details_count = $details_count;
        // $this->count_array = $count_array;
        // $this->amount_array = $amount_array;
        $this->get_all_leads = $get_all_leads;
        $this->all_leads_products = $all_leads_products;
        $this->filter_arr = $filter_arr;
        $this->get_lead_owners = $get_lead_owners;
        $this->cust_or_pay_status = $cust_or_pay_status;
        $this->cities = $cities;
        $this->getOrderStatuses = $getOrderStatuses;
        $this->ordersStateCount = $ordersStateCount;
        $this->orderConvertedCount = $orderConvertedCount;
        $this->totalOrderCount = $totalOrderCount;
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
        $get_all_leads = $this->get_all_leads;
        $all_leads_products = $this->all_leads_products;
        $filter_arr = $this->filter_arr;
        $get_lead_owners = $this->get_lead_owners;
        $cust_or_pay_status = $this->cust_or_pay_status;
        $cities = $this->cities;
        $getOrderStatuses = $this->getOrderStatuses;
        $ordersStateCount = $this->ordersStateCount;
        $orderConvertedCount = $this->orderConvertedCount;
        $totalOrderCount = $this->totalOrderCount;
        return view('export_views.export_converted_orders_report',compact('get_all_leads','all_leads_products','filter_arr','get_lead_owners','cust_or_pay_status','cities','getOrderStatuses','ordersStateCount','orderConvertedCount','totalOrderCount'));
    }
    // public function array(): array
    // {
    //     return $this->get_all_orders;
    //     //print_r($this->get_all_orders);
    // }
}
?>