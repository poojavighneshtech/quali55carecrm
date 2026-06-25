<?php

namespace App\Exports;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use App\Models\Renewal;
use App\Models\UserLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromView;
use DateTime;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ReminderOverdueMail implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $data;
    protected $type;
    function __construct($data,$type) {
        $this->data = $data;
        $this->type = $type;
    }
    // public function headings():array{
    //     return[
    //         'Due Date',
    //         'Customer Name',
    //         'Address',
    //         'Contact Number',
    //         'Products',
    //         'Lead Owner',
    //         'Total Amount',
    //         'Sr. No.',
    //         'Order ID',	
    //         'Product Name',	
    //         'Vendor Name',
    //         'Quantity',
    //         'Rent',
    //         'Deposite',
    //         'Due Months',
    //         'Total Due Rent'
    //     ];
    // } 
    // public function collection()
    // {
    
    // }

    public function view(): View
    {
        $data = $this->data;
        $dataType = $this->type;
        if($dataType=='converted_lead' || $dataType=='inprocess_lead'){
            //dd($data);
            return view('export_views.reminder-lead-converted-overdue-email',compact('data','dataType'));
        }   
        elseif($dataType=='pending_delivery'){
            return view('export_views.reminder-order-pending-delivery-email',compact('data'));
        }
    }
}