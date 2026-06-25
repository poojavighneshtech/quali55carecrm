<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead\customer_detail;
use App\Models\Lead\lead;
use App\Models\Lead\leads_log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function dashbordView(Request $request)
    {
        return view('Reports.dashboard-view');
    }

    public function NewCustomerCount()
    {
        $customers_mumbai = DB::table('order_details')
                ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                ->select('order_details.customer_id')
                ->distinct('order_details.customer_id')
                ->where('order_details.sale_rental','Rental')
                ->whereNotIn('order_details.current_status',['Cancel'])
                ->whereBetween('order_details.creation_date',['2022-04-01','2022-04-31'])
                ->where('customer_details.citygroup','Mumbai')
                ->get();
                // dd($customers);
        $customer_count_mumbai = 0;
        foreach($customers_mumbai as $key=>$value)
        {
            if(DB::table('order_details')->select('customer_id')->distinct('customer_id')->where('order_details.customer_id',$value->customer_id)->where('order_details.sale_rental','Rental')->whereNotIn('order_details.current_status',['Cancel'])->where('order_details.creation_date','<','2022-05-01')->where('order_details.creation_date','>','2022-04-01')->exists())
            {

            }
            else
            {
                $customer_count_mumbai++;
            }
        }
        DB::table('monthly_records')->where('month','4')->where('year','2022')->where('city','Mumbai')->update(['new_customer_rental'=>$customer_count_mumbai]);
        $customers_pune = DB::table('order_details')
                ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                ->select('order_details.customer_id')
                ->distinct('order_details.customer_id')
                ->where('order_details.sale_rental','Rental')
                ->whereNotIn('order_details.current_status',['Cancel'])
                ->whereBetween('order_details.creation_date',['2022-04-01','2022-04-31'])
                ->where('customer_details.citygroup','Pune')
                ->get();
                // dd($customers);
        $customer_count_pune = 0;
        foreach($customers_pune as $key=>$value)
        {
            if(DB::table('order_details')->select('customer_id')->distinct('customer_id')->where('order_details.customer_id',$value->customer_id)->where('order_details.sale_rental','Rental')->whereNotIn('order_details.current_status',['Cancel'])->where('order_details.creation_date','<','2022-05-01')->where('order_details.creation_date','>','2022-04-01')->exists())
            {

            }
            else
            {
                $customer_count_pune++;
            }
        }
        DB::table('monthly_records')->where('month','4')->where('year','2022')->where('city','Pune')->update(['new_customer_rental'=>$customer_count_pune]);
    }
}
