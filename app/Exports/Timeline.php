<?php

namespace App\Exports;

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

class Timeline implements FromView
{
    // use Exportable;
    // protected $details_count;
    // protected $count_array;
    // protected $amount_array;
    protected $orderTimeLine;

    function __construct($orderTimeLine) 
    {
        // $this->details_count = $details_count;
        // $this->count_array = $count_array;
        // $this->amount_array = $amount_array;
        $this->orderTimeLine = $orderTimeLine;
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
        $orderTimeLine = $this->orderTimeLine;
        
        return view('export_views.Timeline',compact('orderTimeLine'));
    }
    // public function array(): array
    // {
    //     return $this->get_all_orders;
    //     //print_r($this->get_all_orders);
    // }
}
?>