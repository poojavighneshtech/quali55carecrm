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
        <div class="leads">
            <div class="container">  
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
                        <center>Renew Product</center>
                    </div> 
                    <div class="card-body">
                        <form class="form" method="post" action="{{url('/')}}/renew_order">
                            {{csrf_field()}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label><b>Customer Name :</b></label>
                                        </div>
                                        <div class="col-md-8 text-left">
                                            <label>{{$customer_info[0]['customer_name']}}</label>
                                            <input type="hidden" name="customer_id" id="customer_id" value="{{$customer_info[0]['cust_id']}}">
                                            <input type="hidden" name="customer_name" id="customer_name" value="{{$customer_info[0]['customer_name']}}">
                                        </div> 
                                    </div>  
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label><b>Contact No :</b></label>
                                        </div>  
                                        <div class="col-md-8 text-left">
                                            <label>{{$customer_info[0]['primary_contact_no']}}</label>
                                        </div>  
                                    </div>  
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label><b>Address:</b></label>
                                        </div>
                                        <div class="col-md-10 text-left">
                                            <label>{{$customer_info[0]['address_line_1']}},{{$customer_info[0]['address_line_2']}},{{$customer_info[0]['area']}},{{$customer_info[0]['location']}}-{{$customer_info[0]['city']}},{{$customer_info[0]['pincode']}}</label>
                                            {{!$cust_address = $customer_info[0]['address_line_1'].','.$customer_info[0]['address_line_2'].','.$customer_info[0]['area'].','.$customer_info[0]['location'].'-'.$customer_info[0]['city'].','.$customer_info[0]['pincode']}}
                                            {{-- <input type="hidden" name="customer_id" id="customer_id" value="{{$customer_info[0]['cust_id']}}"> --}}
                                            <input type="hidden" name="customer_address" id="customer_address" value="{{$cust_address}}">
                                            <input type="hidden" name="customer_location" id="customer_address" value="{{$customer_info[0]['location']}}">
                                            <input type="hidden" name="customer_mobile" id="customer_address" value="{{$customer_info[0]['primary_contact_no']}}">
                                        </div> 
                                    </div>  
                                </div>
                            </div>

                            <div class="table">
                                <table class="table table-bordered" id="records">
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Renew Date</th>
                                            <th>Next Renew Date</th>
                                            <th>Product Rent</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($renew_info as $R_Info)
                                            <tr>
                                                <td>
                                                    {{$R_Info['product_name']}}
                                                    <input type="hidden" name="product_name[]" id="product_name" value="{{$R_Info['product_name']}}">
                                                    <input type="hidden" name="order_id[]" id="order_id" value="{{$R_Info['order_id']}}">
                                                    <input type="hidden" name="order_details_id[]" id="order_details_id" value="{{$R_Info['order_details_id']}}">
                                                    <input type="hidden" name="lead_id[]" id="lead_id" value="{{$R_Info['lead_id']}}">
                                                    <input type="hidden" name="vendor_id[]" id="vendor_id" value="{{$R_Info['vendor_id']}}">
                                                    <input type="hidden" name="product_id[]" id="product_id" value="{{$R_Info['product_id']}}">
                                                </td>
                                                <td>{{date('d-m-Y',strtotime($R_Info['pickup_date']))}}</td>
                                                <input type="hidden" name="renew_date[]" id="renew_date" value="{{date('Y-m-d',strtotime($R_Info['pickup_date']))}}">
                                                <td>{{date('d-m-Y',strtotime($R_Info['renewal_date']))}}</td>
                                                <input type="hidden" name="next_renew_date[]" id="next_renew_date" value="{{date('Y-m-d',strtotime($R_Info['renewal_date']))}}">
                                                <td>
                                                    {{$R_Info['product_rent']}}
                                                    <input type="hidden" name="product_rent[]" id="product_rent" value="{{$R_Info['product_rent']}}">
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td colspan="3" class="text-right">Total Amount</td>    
                                            <td>
                                                {{$total_rent}}
                                                <input type="hidden" name="total_amount" id="total_amount" value="{{$total_rent}}">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                {{-- <input type="hidden" name="row_ct" id="row_ct" value="{{$count}}"> --}}
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label><b>Payment Mode :</b></label>
                                </div>  
                                <div class="col-md-10 text-left">
                                    <div class="btn-group btn-group-toggle" id="payment_btn" data-toggle="buttons" >
                                        <label class="btn btn-outline-success active">
                                            <input type="radio" name="payment_mode" id="cash_radio" autocomplete="off" value="Cash" checked> Cash
                                        </label>
                                        <label class="btn btn-outline-success">
                                            <input type="radio" name="payment_mode" id="online_radio" autocomplete="off" value="Online"> Online
                                        </label>

                                        <label class="btn btn-outline-success disabled">
                                            <input type="radio" name="payment_mode" id="both_radio" autocomplete="off" value="Both" disabled> Both
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
                            </div>
                            <br>
                                <center><button type="submit" class="btn btn-primary">Renew Order</button></center>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endsection
</body>
    @section('script')
        <script>
            $(document).ready(function() {
                $('input:radio[name=payment_mode]').change(function() {
                    if (this.value == 'Both') {
                        $('.cash_online_div').css("display", "block");
                    }
                    else{
                        $('.cash_online_div').css("display", "none");
                    }
                });
            });
        </script>     
    @endsection
</html>