<?php

namespace App\Exports;

use App\Models\Lead\leads_log;

use Illuminate\Contracts\View\View;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;

class MonthlyRecords implements FromView
{
    // use Exportable;
    protected $records;
    // protected $renewal_data;
    // protected $pickup_data;

    function __construct($records) 
    {
        $this->records = $records;
        // $this->renewal_data = $renewal_data;
        // $this->pickup_data = $pickup_data;
    }
    // public function headings(): array
    // {
    //     return ["Date", "Order Id", "Customer Name","Contact No","Order Type","Delivery Status"];
    // }
    public function view(): View
    {
        $month_data = $this->records;
        // $renewal_data = $this->renewal_data;
        // $pickup_data = $this->pickup_data;
        return view('export_views.export_monthly_records',compact('month_data'));
    }
    // public function array(): array
    // {
    //     return $this->get_all_orders;
    //     //print_r($this->get_all_orders);
    // }
}
?>