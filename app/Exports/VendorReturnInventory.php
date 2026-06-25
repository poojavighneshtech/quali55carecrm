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

class VendorReturnInventory implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $data;
    function __construct($data) {
        $this->data = $data;
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
        return view('export_views.vendor-inventory-return-export',compact('data'));
    }
}