<?php

namespace App\Http\Controllers\BillingAndPayment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class CashCollectionAgaintsDelivery extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        // $delboys = DB::select("SELECT * FROM delusersWHERE role='user' AND status='Active' ORDER BY username ASC");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
        $totalamt = DB::table('del_orders')->select('TotalAmt')->where('lead_id',$request->get('leadid'))->where('deliverypickup','Delivery')->get()->sum('TotalAmt');
        $delboys = DB::table('delusers')->select('username')->where('role','user')->where('status','Active')->orderBy('username','ASC')->get();
        $custdetails = DB::table('del_orders')->where('lead_id',$request->get('leadid'))->where('deliverypickup','Delivery')->first();
        return compact('totalamt','delboys','custdetails');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        //dd($request->all());
        $delorderdetails = DB::table('del_orders')->where('order_id',$request->get('ccadorderid'))->first();
        $orderdetailsrecord = DB::table('order_details')->where('order_id',$request->get('ccadorderid'))->first();
        // dd($orderdetailsrecord);
        $insertDelOrder =  [
            'status' => 'Assigned',
            'deliverypickup' => 'Collection',
            'DelAssignedTo' =>$request->get('ccaddelassigned'),
            'lead_id'=>$request->get('btnsubmit'),
            'invoice_no'=>'0',
            'patient_name'=>$delorderdetails->patient_name,
            'shipping_first_name' => $delorderdetails->shipping_first_name,
            'location' => $delorderdetails->location,
            'mobileno' => $delorderdetails->mobileno,
            'line_item_1' => $delorderdetails->line_item_1,
            'DelDate' =>date('d-m-Y',strtotime($request->get('ccaddate'))),
            'Collection_Date' =>$request->get('ccaddate'),
            'TotalAmt' => $request->get('ccadamounttocollect'),
            'fulldetails'=> $delorderdetails->fulldetails,
            'TravelMode' =>'Pending',
            'PaymentMode'=>'Cash',
            'cash'=>$request->get('ccadamounttocollect'),
            'online'=>0,
            'PickupLocation' =>'Customer',
            'order_approval_status' =>'Approved',
            'ccadflag' => 'CCAD'
        ];
        $collectionOrderId = DB::table('del_orders')->insertGetId($insertDelOrder);
        $insertCollectionData= [
            'collection_order_id'=>$collectionOrderId,
            'order_id'=>$orderdetailsrecord->order_id,
            'order_details_id'=>$orderdetailsrecord->id,
            'lead_id'=>$request->get('btnsubmit'),
            'vendor_id'=>$orderdetailsrecord->vendor_id,
            'product_id'=>$orderdetailsrecord->product_id,
            'start_date'=>$orderdetailsrecord->creation_date,
            'end_date'=> $orderdetailsrecord->pickup_date,
            'payment_mode'=>'Cash',
            'cash_amount'=>0,
            'online_amount'=>0,
            'discount_amt'=>0,
            'total_amt'=>0,
            //'online_method',
            'status'=>'Pending',
            'payment_status'=>'Pending',
            'created_by'=>session('username'),
            'created_at'=>date('Y-m-d H:i:s'),
            'flag' => 'CCAD'
        ];
        $insertedRenewalId = DB::table('renewals')->insertGetId($insertCollectionData);
        return redirect()->back()->with('message','Cash Collection Generated Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        //
        // dd($id);
        $totalamt = DB::table('del_orders')->select('TotalAmt')->where('lead_id',$request->get('leadid'))->where('deliverypickup','Delivery')->get()->sum('TotalAmt');
        $delboys = DB::table('delusers')->select('username')->where('role','user')->where('status','Active')->orderBy('username','ASC')->get();
        $custdetails = DB::table('del_orders')->where('order_id',$request->get('orderid'))->first();
        $custdetails->DelDate = date('Y-m-d',strtotime($custdetails->DelDate));
        return compact('totalamt','delboys','custdetails');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //dd($request->all());

        DB::table('del_orders')->where('order_id',$id)->update(['DelAssignedTo'=>$request->get('ccaddelassigned'),'TotalAmt'=>$request->get('ccadamounttocollect'),'DelDate'=>date('d-m-Y',strtotime($request->get('ccaddate')))]);
        return redirect()->back()->with('message','Cash Collection Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
