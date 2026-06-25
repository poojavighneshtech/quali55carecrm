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

class RenewalPickupExportTest implements FromView
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
        $orderData = $this->data;
        $orderData = $orderData->groupBy('customer_id');
        // dd($orderData);
        $orderMonthData = [];
        foreach ($orderData as $key => $order) 
        {
            foreach ($order as $key1 => $product) {
                $today = Carbon::today()->toDateString();
                $monthCount = Carbon::parse($product->pickup_date)->diffInMonths($today);
                //echo $monthCount;
                $currentRenewDate = Carbon::parse($product->pickup_date)->addMonths($monthCount);
                if(Carbon::parse($currentRenewDate)->diffInDays($today)>0){
                    $monthCount = $monthCount+1;
                }
                if(Carbon::parse($product->pickup_date)->diffInDays($today)==0)
                {
                    $monthCount = 1;
                }
                $productMonthRent = $monthCount*$product->product_rent;
                $orderMonthData[$key][$key1]['month_count'] = $monthCount;
                $orderMonthData[$key][$key1]['total_rent'] = $productMonthRent;
            }
        }
        return view('export_views.renewal_pickup_excel_test',compact('orderData','orderMonthData'));
    }
}