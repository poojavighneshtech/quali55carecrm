<?php

namespace App\Http\Controllers\TestController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class TestingController extends Controller
{
    // public function sidebar()
    // {
    //     return view('new-sidebar');
    // }
    
    public function apiTest(Request $request){
        $product = [
            [
                'product_name'=>'Walker With Wheel',
                'rent'=>1500,
                'deposit'=>1000,
                'transport'=>100,
                'rental_sale' => 'Rent'
            ],
            [
                'product_name'=>'Standar Walker',
                'sale'=>1500,
                'deposit'=>1000,
                'transport'=>100,
                'rental_sale' => 'Sale'
            ]
        ];
        $msg = "Product name : ";
        foreach ($product as $key => $value) {
            $prdMsg ="*".$value['product_name']."*, ".($value['rental_sale']=='Rent'?'Rent : ':'Sale : ')
                            .($value['rental_sale']=='Rent'?$value['rent'].", ":$value['sale'].", ")
                            ." Deposit :"
                            .($value['rental_sale']=='Rent'?$value['deposit']:0)
                            .", Transport : "
                            .$value['transport']." | ";
            $msg .=" ".$prdMsg;
        }

        $whatsappLink = "http://intra.quali55care.com/devweb/eflow/0/1248df/";
        $razrpayLink = "RazorPay :  https://rzp.io/l/2eDOVwr";
        $gpayLink = "https://bit.ly/3b5q776";
        $ceoId = config('app.ceo_id');
        $businessHeadId = config('app.business_head_id');
        
        $ceoContact = DB::table('user')->where('id',$ceoId)->first();
        $businessHeadContact = DB::table('user')->where('id',$businessHeadId)->first();

        $callUs = $ceoContact->contact_no." / ".$businessHeadContact->contact_no;
        
        $url = "https://lj7724mli5.execute-api.ap-south-1.amazonaws.com/prod/sendwhatsappmsg";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        $headers = array(
            "Accept: application/json",
            "Content-Type: application/json",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $linkMsg = "<a href='http://intra.quali55care.com/devweb/eflow/cust-bank/2d0rGl'>http://intra.quali55care.com/devweb/eflow/cust-bank/2d0rGl";
        $data =[
            "portno"=>"11140",
            "namespace"=>"b9a23cb4_89ed_4fe2_b849_20775908ff5e",
            "countrycode"=> "91",
            "mobileno"=> "7709184142",
            "templatename" => "change_order_address_modified",
            "templateparams" => [
                ["type"=> "text","text"=> "d"],
                ["type"=> "text","text"=> "d"],
                ["type"=> "text","text"=> "d"],
                ["type"=> "text","text"=> "d"],
                ["type"=> "text","text"=> "d"],
                ["type"=> "text","text"=> "d"],
                ["type"=> "text","text"=> "d"],
                // ["type"=> "text","text"=> "d"],
                // ["type"=> "text","text"=> "d"],
                // ["type"=> "text","text"=> "d"],
                // ["type"=> "text","text"=> "Test Bank"],
                // ["type"=> "text","text"=> "Test Branch"],
                // ["type"=> "text","text"=> "IFSC0001"],
                // ["type"=> "text","text"=> "Saving"],
            ],
        ];

        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        $resp = curl_exec($curl);
        dd($resp);
        curl_close($curl);
        
    }

    public function bankDetails()
    {
        $today = date('d-m-Y');
        $orderTypeNotIn = config('app.order_type');
        $orderTypeNotIn = "'".implode("','",$orderTypeNotIn)."'";
        DB::enableQueryLog();
        $get_pickup_pending = DB::select("SELECT 
                                            order_details.*,
                                            customer_details.customer_name as customer_name,
                                            products.product_name as product_name,
                                            pickups.pickup_order_id as pickup_order_id,
                                            del_orders.DelDate as DelDate
                                        FROM
                                            del_orders,pickups,order_details,customer_details,products
                                        WHERE 
                                            order_details.order_id = pickups.del_order_id
                                            AND order_details.current_status = 'Pending Pickup'
                                            AND pickups.pickup_order_id = del_orders.order_id
                                            AND order_details.product_id = products.id
                                            AND order_details.customer_id = customer_details.cust_id
                                            AND del_orders.deliverypickup NOT IN ($orderTypeNotIn)
                                            AND del_orders.DelDate = $today
                                            ORDER BY del_orders.DelDate DESC ");
        dd(DB::getQueryLog());
        //$cust = DB::table('customer_details')->where()
    }
}