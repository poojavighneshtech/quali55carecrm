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
        <br>
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
                @if(!empty($del_status_arr))
                    <div class="alert alert-danger">
                            Delivery order is not completed for the order id <strong>{{implode(",",$del_status_arr)}}</strong>. Renewal order can't be generated.
                    </div>
                @endif
                <form class="form doublePost" method="post" action="{{url('/')}}/renew_order">
                    {{csrf_field()}}
                    {{-- <div class="row">
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
                                    <input type="hidden" name="customer_address" id="customer_address" value="{{$cust_address}}">
                                    <input type="hidden" name="customer_location" id="customer_address" value="{{$customer_info[0]['location']}}">
                                    <input type="hidden" name="customer_mobile" id="customer_address" value="{{$customer_info[0]['primary_contact_no']}}">
                                </div> 
                            </div>  
                        </div>
                    </div> --}}
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

                    <div class="table table-responsive">
                        <table class="table-bordered" id="records">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Renew Date</th>
                                    <th>Next Renew Date</th>
                                    <th>Product Rent</th>
                                    <th>Due Months</th>
                                    <th>Recieved Payment for month</th>
                                    <th>Discount</th>
                                    <th>Total Due Rent</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $count = 0;
                                @endphp
                                @foreach ($renew_info as $key=>$R_Info)
                                    <tr>
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
                                        <td>{{date('d-m-Y',strtotime($R_Info['pickup_date']))}}</td>
                                        <input type="hidden" name="renew_date[]" id="renew_date" value="{{date('Y-m-d',strtotime($R_Info['pickup_date']))}}">
                                        <td>{{date('d-m-Y',strtotime($R_Info['renewal_date']))}}</td>
                                        <input type="hidden" name="next_renew_date[]" id="next_renew_date" value="{{date('Y-m-d',strtotime($R_Info['renewal_date']))}}">
                                        <td>
                                            {{$R_Info['product_rent']}}
                                            <input type="hidden" name="product_rent[]" id="product_rent" value="{{$R_Info['product_rent']}}">
                                        </td>
                                        <td>{{$R_Info['due_month_count']}}</td>
                                        <td>
                                            <input type="number" class="form-control monthCount" name="new_due_month_count[]" id="new_due_month_count{{$key}}" min="1" max="{{$R_Info['due_month_count']}}" value="{{$R_Info['due_month_count']}}" data-id="{{$key}}">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control offeredDiscount" name="offered_discount[]" id="offeredDiscount{{$key}}" min="0" data-id="{{$key}}">
                                        </td>
                                        <td>
                                            <span id="totalProductRentSpan{{$key}}">{{$R_Info['total_due_month_rent']}}</span>
                                            <input type="hidden" name="total_product_month_rent[]" id="total_product_month_rent{{$key}}" value="{{$R_Info['total_due_month_rent']}}">
                                            <input type="hidden" name="total_product_rent[]" id="totalProductRentInp{{$key}}" value="{{$R_Info['product_rent']}}">
                                        </td>
                                    </tr>
                                    @php $count ++; @endphp
                                @endforeach
                                <input type="hidden" name="row_count" id="row_count" value="{{$count}}">
                                <tr>
                                    <td colspan="7" class="text-right">Total Amount</td>   
                                    <td>
                                        <span id="totalAmountSpan">{{$total_due_rent}}</span>
                                        <input type="hidden" name="total_amount" id="total_amount" value="{{$total_due_rent}}">
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
                        <div class="col-md-2"></div>
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
                    <center><button type="submit" class="btn btn-primary" @if(!empty($del_status_arr)) disabled @endif >Renew Order</button></center>
                </form>
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

            $('.offeredDiscount, .monthCount').on('input',function(){
                let data_id = $(this).data('id');
                let total_rows = $("#row_count").val();
                let total_amount = 0;
                for(var i=0; i<total_rows; i++)
                {
                    let total_rent = $("#totalProductRentInp"+i).val();
                    let total_month_count = $('#new_due_month_count'+i).val()
                    let discount = $("#offeredDiscount"+i).val();
                    let total_month_rent = (total_month_count*total_rent)-discount
                    $('#totalProductRentSpan'+i).text(total_month_rent);
                    $('#total_product_month_rent'+i).val(total_month_rent);
                    total_amount +=total_month_rent;

                }
                $('#totalAmountSpan').text(total_amount);
                $('#total_amount').val(total_amount);
            });
        </script>     
    @endsection
</html>