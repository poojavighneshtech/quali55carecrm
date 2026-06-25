<?php

namespace App\Exports;

use App\Models\OrderDetails;
use App\Models\VendorRegister;
use App\Models\UserRegister;
use App\Models\DelOrders;
use App\Models\lead;
use App\Models\VendorProducts;
use App\Models\leads_log;
use App\Models\sale_vendor_products;
use App\Models\VendorRentedProducts;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;

class OrdersExport implements FromArray,WithHeadings
{
    use Exportable;
    protected $get_all_orders;

    function __construct(array $get_all_orders) 
    {
        $this->get_all_orders = $get_all_orders;
    }
    public function headings(): array
    {
        return ["Date", "Order Id", "Customer Name","Contact No","Order Type","Delivery Status"];
    }
    public function array(): array
    {
        return $this->get_all_orders;
        //print_r($this->get_all_orders);
    }
}
?>