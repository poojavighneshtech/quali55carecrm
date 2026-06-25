<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Renewal Pickup</title>
    {{-- <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script> --}}
    @section('styles')
        
    @endsection
</head>

<body id="page-top">	
    @extends('header_and_sidebar')
        
    @section('content')
        @if(session()->has('message') || session()->has('message_pop') )
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                {{ session()->get('message')}} <small><a class="" href="{{ session()->get('collection_url')}}">See Order Here</a></small>
                {{ session()->get('message_pop')}}
            </div>
        @endif
        @if(session()->has('message_delete'))
            <div class="alert alert-danger">
                {{ session()->get('message_delete') }}
            </div>
        @endif 
        @if(session()->has('error'))
            <div class="alert alert-danger">
                {{ session()->get('error') }}
            </div>
        @endif 
        <div class="card">
            <div class="card-header"  style="background-color: #4a6fdc; color: white;" >
                <center>Pickup Product</center>
            </div> 
            <div class="card-body">
                @if(!empty($incOrder))
                    <div class="alert alert-danger">
                        Delivery order is not completed for the order id <strong>{{implode(",",$incOrder)}}</strong>. Pickup order can't be generated.
                    </div>
                @endif
                <form class="form doublePost" method="post" action="{{url('/')}}/pickup_order">
                    {{csrf_field()}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-4">
                                    <label><b>Customer Name :</b></label>
                                </div>
                                <div class="col-md-8 text-left">
                                    {{$orderInfo[0]->customer_name}}
                                    
                                </div> 
                            </div>  
                            <div class="row">
                                <div class="col-md-4">
                                    <label><b>Contact No :</b></label>
                                </div>  
                                <div class="col-md-8 text-left">
                                    {{$orderInfo[0]->primary_contact_no}}
                                </div>  
                            </div>  
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-2">
                                    <label><b>Address:</b></label>
                                </div>
                                <div class="col-md-10 text-left">
                                    {{$orderInfo[0]->address_line_1}},{{$orderInfo[0]->address_line_2}},{{$orderInfo[0]->area}},{{$orderInfo[0]->location}},{{$orderInfo[0]->location}},{{$orderInfo[0]->city}},{{$orderInfo[0]->pincode}}
                                    {{!$cust_address = $orderInfo[0]->address_line_1.','.$orderInfo[0]->address_line_2.','.$orderInfo[0]->area.','.$orderInfo[0]->location.'-'.$orderInfo[0]->city.','.$orderInfo[0]->pincode}}
                                    {{--hidden customers details--}}
                                    <input type="hidden" name="customer_id" id="customer_id" value="{{$orderInfo[0]->cust_id}}">
                                    <input type="hidden" name="customer_name" id="customer_name" value="{{$orderInfo[0]->customer_name}}">
                                    <input type="hidden" name="customer_address" id="customer_address" value="{{$cust_address}}">
                                    <input type="hidden" name="customer_location"  value="{{$orderInfo[0]->location}}">
                                    <input type="hidden" name="customer_mobile" id="customer_address" value="{{$orderInfo[0]->primary_contact_no}}">
                                </div> 
                            </div>  
                        </div>
                    </div>
                    <hr>
                    <div class="row ">
                        <div class="col-md-1">
                            <label><b>Filter</b></label>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" required="true">
                                <input type="radio" class="form-group" id="individual" name="pickup_date_filter" data-id="individual" value="individual" > <label for="individual">Individual</label>
                                &emsp;
                                <input type="radio" class="form-group" id="today" name="pickup_date_filter" data-id="today" value="today" checked><label for="today">Today</label>
                                &emsp;
                                <input type="radio" class="form-group" id="tomorrow" name="pickup_date_filter" data-id="tomorrow" value="tomorrow" > <label for="tomorrow">Tomorrow</label>
                            </div>
                        </div>
                    </div>  
                    <div class="table table-responsive">
                        <table class="table table-bordered" id="records">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Pickup Date</th>
                                    <th>Deposit</th>
                                    <th>Product Rent</th>
                                    <th>Due Months</th>
                                    <th>Total Due Rent</th>
                                </tr>
                            </thead>
                            <tbody>
                                <input type="hidden" name="rowCount" id="rowCount" value="{{count($orderInfo)}}">
                                @foreach ($orderInfo as $key=>$product)
                                    <tr>
                                        <td>{{$product->product_name}}</td>
                                        <td>
                                            <input type="date" class="form-control" name="pickup_date[]" id="pickupDate{{$key}}" value={{date('Y-m-d')}}>
                                        </td>
                                        <td>{{$product->product_rent}}</td>
                                        <td>{{$product->product_deposite}}</td>
                                        <td>{{$dueMonthArr[$key]}}</td>
                                        <td>{{$totalProductRentArr[$key]}}</td>
                                    </tr>
                                    {{--Hidden Values--}}
                                    <input type="hidden" name="product_name[]" id="product_name" value="{{$product->product_name}}">
                                    <input type="hidden" name="order_id[]" id="order_id" value="{{$product->order_id}}">
                                    <input type="hidden" name="order_details_id[]" id="order_details_id" value="{{$product->order_details_id}}">
                                    <input type="hidden" name="product_rent[]" id="product_rent" value="{{$product->product_rent}}">
                                    <input type="hidden" name="product_deposit[]" id="product_deposit" value="{{$product->product_deposite}}">
                                    <input type="hidden" name="lead_id[]" id="lead_id" value="{{$product->lead_id}}">
                                    <input type="hidden" name="vendor_id[]" id="vendor_id" value="{{$product->vendor_id}}">
                                    <input type="hidden" name="product_id[]" id="product_id" value="{{$product->product_id}}">
                                    <input type="hidden" name="due_month_count[]" id="due_month_count" value="{{$dueMonthArr[$key]}}">
                                    <input type="hidden" name="total_due_month_rent[]" id="total_due_month_rent" value="{{$totalProductRentArr[$key]}}">
                                @endforeach
                                <tr>
                                   <td class="text-right" colspan="5">
                                        <strong>Total</strong>
                                   </td>
                                   <td>
                                       {{array_sum($totalProductRentArr)}}
                                       <input type="hidden" name="total_amount" id="total_amount" value="{{array_sum($totalProductRentArr)}}">
                                   </td>
                                </tr>
                            </tbody>
                        </table> 
                    </div>
                    <div class="row justify-content-center">
                        <button type="submit" class="btn btn-outline-primary" name="btn_state" value="stop_pickup" @if(!empty($incOrder)) disabled @endif>Pickup Order</button>
                    </div>
                </form>
            </div>
        </div>
    @endsection
</body>
@section('script')
<script>
    $(document).ready(function() {
        $('input[type=radio][name=pickup_date_filter]').change('click',function() {    
            var date_val = $(this).val();
            var rowCount =  $('#rowCount').val();
            if(date_val=='tomorrow')
            {
                for (var i=0; i<rowCount; i++) {
                    var date = '{{date('Y-m-d',strtotime("+1 days"))}}';
                    $('#pickupDate'+i).val(date);
                }
               
            }   
            if(date_val=='today')
            {
                for (var i=0; i<rowCount; i++) {
                    var date = '{{date('Y-m-d')}}';
                    $('#pickupDate'+i).val(date);
                }
               
            }   
            
        });
    });
</script>     
@endsection
</html>