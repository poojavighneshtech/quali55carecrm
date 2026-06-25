@extends('header_and_sidebar')
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Renew Order</title>
    {{-- <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script> --}}
    @section('styles')
        
        <style>
        </style>
    @endsection
</head>

<body id="page-top">	
        
    @section('content')
        <div class="container-fluid">
            @if(session()->has('message') || session()->has('message_pop') )
                <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    {{ session()->get('message')}} @if(session()->has('collection_url'))<small><a class="" href="{{ session()->get('collection_url')}}">See Order Here</a></small>@endif
                    {{ session()->get('message_pop')}}
                </div>
            @endif
            @if(session()->has('reminder_msg'))
                <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    {{ session()->get('reminder_msg')}} 
                </div>
            @endif
            @if(session()->has('success'))
                <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    {{ session()->get('success')}} 
                    @if(session()->has('payreceived-url'))<small><a class="" href="{{url('/').'/'.session()->get('payreceived-url').'/'.$rows[0]->collection_order_id}}">Payment Received</a></small>@endif
                </div>
            @endif
            @if(session()->has('message_delete'))
                <div class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    {{ session()->get('message_delete') }}
                </div>
            @endif 
            @if(session()->has('error'))
                <div class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    {{ session()->get('error') }}
                </div>
            @endif 
            @if(!empty($orderNotDelivered))
                <div class="alert alert-danger">
                    Delivery order is not completed for the order id <strong>{{implode(",",$orderNotDelivered)}}</strong>. Renewal order can't be generated.
                </div>
            @endif
           
        </div>
        <div class="card">
            <div class="alert alert-danger" role="alert" id="total_amount_minus" style="display: none">
                The total amount cant be in negative..
            </div>

            <div class="card-header border-primary" id="filter_card">
                <strong>Renew Product</strong>
            </div>
            <div class="card-body border-primary" id="filter-collapse" aria-labelledby="headingTwo" data-parent="#filter_card">
                <div class="row">
                    <div class="col-sm-auto">
                        <Strong>Customer Name </Strong>: &emsp;{{$customerDetails->customer_name}}
                    </div>
                    <div class="col-sm-auto">
                        <Strong>Contact No </Strong>: &emsp;{{$customerDetails->primary_contact_no}}
                    </div>
                    <div class="col-sm-auto">
                        <Strong>Address </Strong>: &emsp;{{$customerDetails->address_line_1}},{{$customerDetails->address_line_2}},{{$customerDetails->area}},{{$customerDetails->location}},{{$customerDetails->city}},{{$customerDetails->pincode}}
                    </div>
                </div>
            </div>
            <form action="{{route('update-renewal')}}" method="post">
                @csrf
                <input type="hidden" name="order_id" value="{{$rows[0]->collection_order_id}}">
                <div class="table table-responsive jim-table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <th></th>
                            <th>Product Name</th>
                            <th>Vendor  Name</th>
                            <th>Renew Date</th>
                            <th>Next Renew Date</th>
                            <th>Rent</th>
                            <th>Act. Deposit</th>
                            <th>Adj. Deposit</th>
                            <th>Rem. Deposit</th>
                            <th>Due Months</th>
                            {{-- <th>Collection Of</th> --}}
                            <th>Rec Pay for month</th>
                            <th>Adj Deposit</th>
                            <th>Discount</th>
                            <th class="text-nowrap">Total Due Rent</th>
                        </thead>
                        <tbody>
                            @forelse($rows as $key=>$data)
                                <tr>
                                    <input type="hidden" name="order_details_id[]" value="{{$data->order_details_id}}">
                                    
                                    <td>
                                        <input type="checkbox" name="renewal_id[]" id="renewal_id{{$key}}" value="{{$data->order_details_id}}">
                                        <label for="renewal_id{{$key}}" class="text-danger"><i class="fas fa-trash"></i></label>
                                    </td>
                                    <td data-label="Product Name">{{$data->product_name}}</td>
                                    <td data-label="Vendor Name">{{$data->registered_name}}</td>
                                    <td class="text-nowrap" data-label="Renew Date">{{$data->ren_start_date}}</td>
                                    <td class="text-nowrap" data-label="Next Renew Date">{{$orderMonthData[$key]['next_renew_date']}}</td>
                                    <td data-label="Product Rent">
                                        {{$data->product_rent}}
                                        <input type="hidden" name="product_rent" id="product_rent{{$key}}" value="{{$data->product_rent}}">
                                    </td>
                                    <td data-label="Act. Deposit">
                                        {{$data->product_deposite}}
                                        <input type="hidden" name="act_product_deposit" id="act_product_deposit{{$key}}" value="{{$data->product_deposite}}">
                                    </td>
                                    <td data-label="Adj. Deposit">
                                        {{$data->adjusted_deposit}}
                                        <input type="hidden" name="adj_product_deposit" id="adj_product_deposit{{$key}}" value="{{$data->adjusted_deposit}}">
                                    </td>
                                    <td data-label="Rem. Deposit">
                                        {{$data->product_deposite - $data->adjusted_deposit}}
                                        <input type="hidden" name="rem_product_deposit" id="rem_product_deposit{{$key}}" value="{{$data->product_deposite - $data->adjusted_deposit}}">
                                    </td>
                                    <td data-label="Due Months">{{$orderMonthData[$key]['month_count']}}</td>
                                    {{-- <td>
                                        <div class="btn-group btn-group-toggle btn-sm" data-toggle="buttons" >
                                            <label class="btn btn-outline-primary btn-sm active">
                                                <input type="radio" class="collection-of" name="collection_of[]" id="btn_collection_of_month" autocomplete="off" value="month" checked>Month
                                            </label>
                                            <label class="btn btn-outline-primary btn-sm">
                                                <input type="radio" class="collection-of" name="collection_of[]" id="btn_collection_of_week" autocomplete="off" value="week">Week
                                            </label>
                                        </div>
                                    </td> --}}
                                    <td data-label="Recieved payment month">
                                        <input type="number" class="form-control form-control-sm payment-months" name="payment_months[]" id="payment_months{{$key}}" data-id={{$key}} 
                                            min="1"  value="{{$orderMonthData[$key]['month_count']}}">
                                    </td>
                                    <td data-label="Adjust Deposit">
                                        <input type="number" class="form-control form-control-sm deposit-adjust" name="deposit_adjust[]" data-order_details_id = "{{$data->id}}" data-actual_depo="{{$data->product_deposite - $data->adjusted_deposit}}" id="deposit_adjust{{$key}}" value="0">
                                        {{-- <div class="input-group input-group-sm mb-3">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="deposit_adjust_info" data-toggle="tooltip" data-placement="bottom" title="adjust rent in deposit">
                                                    <i class="fa fa-info-circle text-primary" aria-hidden="true"></i>
                                                </span>
                                            </div>
                                        </div> --}}
                                    </td>
                                    <td data-label="Discount" class="">
                                        <input type="number" class="form-control form-control-sm discount-offered" name="discount_offered[]" id="discount_offered{{$key}}" min="0" data-id={{$key}} value="{{$orderMonthData[$key]['discount_amt']}}">
                                    </td>
                                    <td data-label="Total Due Rent" class="text-right">
                                        <span id="product_due_rent_span{{$key}}">{{$orderMonthData[$key]['total_rent']}}</span>
                                        <input type="hidden" name="product_due_rent" id="product_due_rent{{$key}}" value="{{$orderMonthData[$key]['total_rent']}}">
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td class="text-center" colspan="*">No record</td> 
                                </tr>
                                @endforelse
                                <tr>
                                    {{-- <td colspan='2'><button type="button" name="remove_selected" id="remove_selected" class="btn btn-sm btn-outline-danger form-control"><i class="fas fa-trash"></i> Remove Selected</button></td> --}}
                                    <td colspan="13"></td>
                                    <td class="text-right">
                                        Total: &emsp;<strong><span id="total_due_rent_span">{{array_sum(array_column($orderMonthData,'total_rent'))}}</span></strong>
                                        <input type="hidden" name="total_due_rent" id="total_due_rent_hidden" value="{{array_sum(array_column($orderMonthData,'total_rent'))}}">
                                    </td>    
                                </tr>
                                <input type="hidden" name="total_rows" id="total_rows" value="{{count($rows)}}">
                        </tbody>
                    </table>
                </div>
                <div class="row form-group">
                    <div class="col-md-2">
                        <label><b>Payment Mode :</b></label>
                    </div>  
                    <div class="col-md-2 text-left">
                        <div class="btn-group btn-group-sm btn-group-toggle" id="payment_btn" data-toggle="buttons" >
                            <label class="btn btn-outline-success active">
                                <input type="radio" name="payment_mode" id="cash_radio" autocomplete="off" value="Cash" @if($rows[0]->payment_mode == 'Cash'){{'checked'}}@endif> Cash
                            </label>
                            <label class="btn btn-outline-success">
                                <input type="radio" name="payment_mode" id="online_radio" autocomplete="off" value="Online" @if($rows[0]->payment_mode == 'Online'){{'checked'}}@endif> Online
                            </label>
    
                            <label class="btn btn-outline-success disabled">
                                <input type="radio" name="payment_mode" id="both_radio" autocomplete="off" value="Both" disabled> Both
                            </label>
                        </div>
                    </div> 
                    <div class="col-md-8" id="div_delboy" style="@if($rows[0]->payment_mode == 'Online'){{'display:none'}}@endif">
                        <div class="row">
                            <div class="col-md-3">
                                <label><b>Delivery Boy :</b></label>
                            </div>  
                            <div class="col-md-3 text-left">
                                <select class="select selectpicker form-control form-control-sm border border-dark" title="Select Delboy" name="assign_delboy" data-live-search="true" data-size="5" id="assign_delboy" @if($rows[0]->payment_mode == 'Cash'){{'required'}}@endif>
                                    <option value="Pending" @if($rows[0]->DelAssignedTo == "Pending"){{'selected'}}@endif>Pending</option>
                                    @foreach($delboys as $key=>$delboy)
                                        <option value="{{$delboy->username}}" @if($rows[0]->DelAssignedTo == $delboy->username){{'selected'}}@endif>{{$delboy->username}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label><b>Collection Date :</b></label>
                            </div>  
                            <div class="col-md-3 text-left">
                                <input type="date" class="form-control form-control-sm" name="collectiondate" id="collectiondate" value="@if($rows[0]->DelAssignedTo == "Pending"){{date('Y-m-d')}}@else{{date('Y-m-d',strtotime($rows[0]->DelDate))}}@endif">
                            </div>  
                        </div>
                    </div>
                </div>
                <div class="conatiner content-justify-center mt-1">
                    <center><button type="submit" class="btn btn-outline-primary" name="submit" id="submit" value="renew" @if(!empty($orderNotDelivered)) disabled @endif>Submit</button></center>
                </div>
            </form>
            <br>
        </div>
        
       
    @endsection
</body>
@section('script')
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
    $('[name=payment_mode]').click(function(){
   		
   		if($('input[name="payment_mode"]:checked').val() == 'Cash')
        {
            $("#div_delboy").show();
            $("#assign_delboy").attr('required',true);
        }
        if($('input[name="payment_mode"]:checked').val() == 'Online')
        {
            $("#div_delboy").hide();
            $("#assign_delboy").attr('required',false);
        }
   	});
    
    $('.payment-months, .discount-offered, .deposit-adjust').on('input',function(){
        this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        let id = $(this).data('id');
        let rows = $('#total_rows').val();
        let totalAmount = 0;
       
        
        for(var i=0; i<rows; i++)
        {
            let productDueRent = $("#product_due_rent"+i).val();
            let productRent = $("#product_rent"+i).val();
            let productDeposit = $("#rem_product_deposit"+i).val();
            let paymentMonth = $('#payment_months'+i).val()
            let discount = $("#discount_offered"+i).val();
            
            let adjustDeposit = $('#deposit_adjust'+i).val();

            if(adjustDeposit==null || adjustDeposit==""){
                adjustDeposit = 0;
            }
            if(discount==null || discount==""){
                discount = 0;
            }
            if(parseInt(adjustDeposit)>parseInt(productDeposit)){
                alert("Adjust deposit amount can't be greater than  remaining product deposit");
                $("#deposit_adjust"+i).val(0);
                adjustDeposit = 0;
            }
            
            let totalProductDueRent = (paymentMonth*productRent)-discount;
                totalProductDueRent = totalProductDueRent-adjustDeposit;
            $('#product_due_rent_span'+i).text(totalProductDueRent);
            //$('#product_due_rent'+i).val(totalProductDueRent);
            totalAmount +=totalProductDueRent;
        }
        if(totalAmount<0){
            alert("total amount cant be less than 0");
            $('#submit').prop('disabled',true);
            $('#total_amount_minus').show();
        }else{
            $('#submit').prop('disabled',false);
            $('#total_amount_minus').hide();
        }
        $('#total_due_rent_span').text(totalAmount);
        // $('#total_due_rent').val(totalAmount);
    });
    
</script>     

@endsection
</html>