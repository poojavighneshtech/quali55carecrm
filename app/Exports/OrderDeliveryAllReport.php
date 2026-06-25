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

class OrderDeliveryAllReport implements FromView
{

    /**
    * @return \Illuminate\Support\Collection
    */
    
    protected $get_all_leads;
    protected $amountcount;
    protected $productscount;
    protected $customers;
    protected $statustotal;
    protected $orderstatus;

    function __construct($get_all_leads,$amountcount,$productscount,$customers,$statustotal,$orderstatus) 
    {
        $this->get_all_leads = $get_all_leads;
        $this->amountcount = $amountcount;
        $this->productscount = $productscount;
        $this->customers = $customers;
        $this->statustotal = $statustotal;
        $this->orderstatus = $orderstatus;
    }
    public function view(): View
    {
        $get_all_leads = $this->get_all_leads;
        $amountcount = $this->amountcount;
        $productscount = $this->productscount;
        $customers = $this->customers;
        $statustotal = $this->statustotal;
        $orderstatus = $this->orderstatus;

        return view('export_views.order-delivery-all-export',compact('get_all_leads','amountcount','productscount','customers','statustotal','orderstatus'));
    }
}
?>