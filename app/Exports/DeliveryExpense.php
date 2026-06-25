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
use Carbon\Carbon;


use Illuminate\Contracts\View\View;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;

class DeliveryExpense implements FromView
{
    // use Exportable;
    protected $get_all_expenses;
    protected $totalTransport;
    protected $totalExpense;
    protected $totalLabour;
   
    function __construct($get_all_expenses,$totalTransport,$totalExpense,$totalLabour) 
    {
        $this->get_all_expenses = $get_all_expenses;
        $this->totalTransport = $totalTransport;
        $this->totalExpense = $totalExpense;
        $this->totalLabour = $totalLabour;
    }
    public function view(): View
    {
        $get_all_expenses = $this->get_all_expenses;
        $totalTransport = $this->totalTransport;
        $totalExpense = $this->totalExpense;
        $totalLabour = $this->totalLabour;
        return view('export_views.DeliveryExpense',compact('get_all_expenses','totalExpense','totalTransport','totalLabour'));
    }
}
?>