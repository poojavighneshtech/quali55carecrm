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
        
        <style>
            .morecontent span {
                display: none;
            }
            .morelink {
                display: block;
            }
        </style>
    @endsection
</head>

<body id="page-top">	
		<!-- Page Wrapper -->
    
    @extends('header_and_sidebar')
        
    @section('content')
        @if(session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
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
                @if(!empty($del_status_arr))
                    <div class="alert alert-danger">
                            Delivery order is not completed for the order id <strong>{{implode(",",$del_status_arr)}}</strong>. Pickup order can't be generated.
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
                                    <label>{{$address_info[0]['shipping_first_name']}}</label>
                                    <input type="hidden" name="customer_id" id="customer_id" value="{{$customer_info[0]['cust_id']}}">
                                    <input type="hidden" name="customer_name" id="customer_name" value="{{$address_info[0]['shipping_first_name']}}">
                                </div> 
                            </div>  
                            <div class="row">
                                <div class="col-md-4">
                                    <label><b>Contact No :</b></label>
                                </div>  
                                <div class="col-md-8 text-left">
                                    <label>{{$address_info[0]['mobileno']}}</label>
                                </div>  
                            </div>  
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-2">
                                    <label><b>Address:</b></label>
                                </div>
                                <div class="col-md-10 text-left">                                    
                                    <label>{{$address_info[0]['fulldetails']}}</label>
                                    {{!$cust_address = $address_info[0]['fulldetails']}}
                                    <input type="hidden" name="customer_address" id="customer_address" value="{{$cust_address}}">
                                    <input type="hidden" name="customer_location" id="customer_location" value="{{$address_info[0]['location']}}">
                                    <input type="hidden" name="customer_mobile" id="customer_mobile" value="{{$address_info[0]['mobileno']}}">
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
                    <div class="table">
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
                                @php
                                    $i =0;
                                @endphp
                                @foreach ($pickup_info as $R_Info)
                                    <tr data-id="{{$i}}">
                                        <td>
                                            {{$R_Info['product_name']}}
                                            <input type="hidden" name="product_name[]" id="product_name" value="{{$R_Info['product_name']}}">
                                            <input type="hidden" name="order_id[]" id="order_id" value="{{$R_Info['order_id']}}">
                                            <input type="hidden" name="order_details_id[]" id="order_details_id" value="{{$R_Info['order_details_id']}}">
                                            <input type="hidden" name="lead_id[]" id="lead_id" value="{{$R_Info['lead_id']}}">
                                            <input type="hidden" name="vendor_id[]" id="vendor_id" value="{{$R_Info['vendor_id']}}">
                                            <input type="hidden" name="product_id[]" id="product_id" value="{{$R_Info['product_id']}}">
                                            <input type="hidden" name="due_month_count[]" id="due_month_count" value="{{$R_Info['due_month_count']}}">
                                            <input type="hidden" name="total_due_month_rent[]" id="total_due_month_rent" value="{{$R_Info['total_due_month_rent']}}">
                                        </td>
                                        <td>
                                            {{-- {{date('d-m-Y',strtotime($R_Info['pickup_date']))}} --}}
                                            {{-- <input type="hidden" name="pickup_date[]" id="pickup_date" value="{{$R_Info['pickup_date']}}"> --}}
                                            <input class="form-control" type="date" name="pickup_date[]" id="pickup_date{{$i}}" value="{{date('Y-m-d')}}" required>
                                        </td>
                                        <td>
                                            {{$R_Info['product_rent']}}
                                            <input type="hidden" name="product_rent[]" id="product_rent" value="{{$R_Info['product_rent']}}">
                                        </td>
                                        <td>
                                            {{$R_Info['deposit']}}
                                            <input type="hidden" name="product_deposit[]" id="product_deposit" value="{{$R_Info['deposit']}}">
                                        </td>
                                        <td>{{$R_Info['due_month_count']}}</td>
                                        <td>{{$R_Info['total_due_month_rent']}}</td>
                                    </tr>
                                    @php
                                        $i++;
                                    @endphp
                                @endforeach
                                <tr>
                                    <td colspan="3" class="text-right">Total</td>    
                                    <td>{{$total_deposit}} <small class="text-muted">(return to customer)</small></td>
                                    <td colspan="" class="text-right">Total</td>    
                                    <td>
                                        {{$total_due_rent}}<small class="text-muted">(collect from customer)</small>
                                        <input type="hidden" name="total_amount" id="total_amount" value="{{$total_due_rent}}">
                                    </td>
                                </tr>
                            </tbody>
                        </table> 
                        {{-- <input type="hidden" name="row_ct" id="row_ct" value="{{$count}}"> --}}
                    </div>
                    <input type="hidden" name="row_count" id="row_count" value="{{$i}}">
                    {{-- <div class="row">
                        <div class="col-md-2">
                            <label><b>Payment Mode :</b></label>
                        </div>  
                        <div class="col-md-10 text-left">
                            <div class="btn-group btn-group-toggle" id="payment_btn" data-toggle="buttons" >
                                <label class="btn btn-outline-success active">
                                    <input type="radio" name="payment_mode" id="cash" autocomplete="off" value="cash" checked> Cash
                                </label>
                                <label class="btn btn-outline-success">
                                    <input type="radio" name="payment_mode" id="online" autocomplete="off" value="online"> Online
                                </label>

                                <label class="btn btn-outline-success">
                                    <input type="radio" name="payment_mode" id="both" autocomplete="off" value="both"> Both
                                </label>
                            </div>
                        </div>  
                    </div>
                    <div class="row cash_online_div" style="display: none">
                        <br>
                        <div class="col-md-2">
                            
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="cash">Cash</span>
                                </div>
                                <input type="text" class="form-control" name="cash_amount" id="cash_amount" placeholder="Cash Amount">
                            </div>
                        </div>
                        <br>
                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="online">Online</span>
                                </div>
                                <input type="text" class="form-control" id="online_amount" name="online_amount" placeholder="Online Amount">
                            </div>
                        </div>
                    </div> --}}
                    <br>
                        <center><button type="submit" name="btn_state" value="pickup" class="btn btn-primary" @if(!empty($del_status_arr)) disabled @endif >Pickup Order</button></center>
                </form>
            </div>
        </div>
    @endsection
</body>
@section('script')
<script>
    $(document).ready(function() {
        $('input:radio[name=payment_mode]').change(function() {
            if (this.value == 'both') {
                $('.cash_online_div').css("display", "block");
            }
            else{
                $('.cash_online_div').css("display", "none");
            }
        });

        $('input[type=radio][name=pickup_date_filter]').change('click',function() {     
            var date_val = $(this).val();
            var row_count =  $('#row_count').val();
            if(date_val=='tomorrow')
            {
                for (var i=0; i<row_count; i++) {
                    var date = '{{date('Y-m-d',strtotime("+1 days"))}}';
                    $('#pickup_date'+i).val(date);
                }
               
            }   
            if(date_val=='today')
            {
                for (var i=0; i<row_count; i++) {
                    var date = '{{date('Y-m-d')}}';
                    $('#pickup_date'+i).val(date);
                }
               
            }   
            
        });
    });
</script>     
@endsection
</html>