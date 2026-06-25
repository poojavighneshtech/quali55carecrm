<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Order Details</title>

</head>

<body id="page-top">	
<!-- Page Wrapper -->

@extends('header_and_sidebar')
    
@section('content')
<br>
@if(session()->has('message'))
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        {{ session()->get('message') }} @if(session()->has('approvevendor'))<small><a class="" href="{{ route(session()->get('approvevendor')) }}">Approve Vendor</a></small>@endif
    </div>
@endif
<form class="form" action="<?php echo url('/')?>/" method="post" >
    {{ csrf_field() }}
    <div class="card">
        <div class="card-header" style="background-color: #337ab7; color: white;">
            <center>
                <b>Order Details</b>
            </center>
        </div>
        @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                {{$error}}
            </div>
        @endforeach
        @endif
        <div class="card-body">
            <h3> Order Details </h3>
            <hr>
            <div class="row">
                <div class="col-6 col-md-2">
                    <label for="customer_name">Customer Mobile : </label>
                </div>
                <div class="col-6 col-md-4">
                   <span>{{$order_details[0][0]['mobileno']}}</span>
                </div>
                <div class="col-md-2">
                    <label for="customer_address">Customer Address : </label>
                </div>
                <div class="col-md-4">
                    <span>{{$order_details[0][0]['fulldetails']}}</span>
                </div>
            </div>
            <div class="row">
                <div class="col-6 col-md-2">
                    <label for="customer_name">Customer Email ID : </label>
                </div>
                <div class="col-6 col-md-4">
                   <span>{{$order_details[0][0]['email_id']}}</span>
                </div>
            </div>
            <div class="row">
                <div class="col-6 col-md-2">
                    <label for="payment_mode">Payment Mode : </label>
                </div>
                <div class="col-6 col-md-2">
                    <span>{{$order_details[0][0]['PaymentMode']}}</span>
                </div>
            </div>
            <hr>    
            <h3> Order List </h3>
            <hr>
            <div class="row table-responsive jim-table-responsive">
                <table id="records" class="table table-bordered" style="width:100%; ">
                    <thead>
                        <tr>
                            <th>Sr.No.</th>
                            <th>Order ID</th>
                            <th>Require Equipment </th>
                            <th>Selected vendor </th>                                        
                            <th>Product Qty</th>
                            <th>Warehouse Details</th>
                            <th>Status</th>
                            <th>Product Deposit</th>
                            <th>Offered Rent</th>
                            <th>Transport</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody class="tbody">
                            @php
                                $count = 0;
                                $temp_order_id = array();
                                $sr_no = 1;
                            @endphp                                        
                            @foreach($order_details as $order_detail) 
                                @foreach ($order_detail as $OD)
                                    <tr class='rows' data-count="{{$count}}">
                                        <td data-label="Sr.No.">{{$sr_no}}</td>
                                        <td data-label="Order ID">
                                            @if(in_array($OD['order_id'],$temp_order_id))
                                                -
                                            @else
                                                {{$OD['order_id']}}
                                            @endif
                                        </td>
                                        <td data-label="Equipment">{{$OD['product_name']}}<br><span><small>{{$OD['upgraded']}}</small></span></td>
                                        <td data-label="Vendor">{{$OD['vendor_name']}}</td>
                                        <td data-label="Qty">{{$OD['product_qty']}}</td>
                                        <td data-label="Warehouse">{{$OD['wh_name'].", ".$OD['wh_landmark'].", ".$OD['wh_city']}}</td>
                                        <td data-label="Status">{{$OD['sale_rental']}}</td>
                                        <td data-label="Deposit">{{$OD['product_deposite']}}</td>
                                        <td data-label="Rent">{{$OD['product_rent']}}</td>
                                        <td data-label="Transport">{{$OD['transport']}}</td>
                                        <td data-label="Total">
                                            @if(in_array($OD['order_id'],$temp_order_id))
                                                <center><span>-</span></center>
                                                <input type="hidden" name="total_amt{{$count}}" id="total_amt{{$count}}" value="0">
                                            @else 
                                                {{$OD['TotalAmt']}}   
                                                <input type="hidden" name="total_amt{{$count}}" id="total_amt{{$count}}" value="{{$OD['TotalAmt']}}">
                                                {{!array_push($temp_order_id,$OD['order_id'])}}
                                            @endif   
                                            
                                        </td>
                                    </tr>
                                    {{!$sr_no++}}
                                @endforeach
                                
                                @php
                                    $count =$count+1; 
                                @endphp
                                {{-- {{!$count =$count+1}} --}}
                                
                            @endforeach
                            <tr>
                                <td>{{$sr_no}}</td>
                                <td colspan="8">Grand Total</td>
                                <td><span id="final_total_amt"></span></td>
                            </tr>
                    </tbody>
                </table>
                <input type="hidden" name="row_ct" id="row_ct" value="{{$count}}">
            </div>   
        </div>
    </div>
</form>
@endsection
</body>

    @section('script')
        <script>
            var count = $('#row_ct').val();
            //var amt_array = []; 
            var final_amt = 0
            for (var i = 0; i <count; i++)
            {
                var amt_val = $('#total_amt'+i).val();
                //amt_array.push(parseInt(amt_val));
                final_amt += parseInt(amt_val);
            }
            //var final_amt = amt_array.reduce((a,b) => a + b,0);
            $('#final_total_amt').text(final_amt);
            
        </script>                                                         

    @endsection
    
</html>