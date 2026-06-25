{{-- @extends('new-sidebar') --}}
@extends('header_and_sidebar')
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Renew & Pickup Order</title>
    {{-- <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script> --}}
    @section('styles')
        
        <style>
        </style>
    @endsection
</head>

<body id="page-top">	
        
    @section('content')
        
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

        <div class="container-fluid">
            @if(!empty($orderNotDelivered))
                <div class="alert alert-danger">
                    Delivery order is not completed for the order id <strong>{{implode(",",$orderNotDelivered)}}</strong>. Renewal and Pickup order can't be generated.
                </div>
            @endif
        </div>
        <form action="{{route('order-call')}}" method="post">
            @csrf
            <div class="card">
                <div class="alert alert-danger" role="alert" id="total_amount_minus" style="display: none">
                    The total amount cant be in negative..
                </div>

                <div class="card-header border-primary" id="filter_card">
                    <strong>Customer Details</strong>
                </div>
                <div class="card-body border-primary" id="filter-collapse" aria-labelledby="headingTwo" data-parent="#filter_card">
                    <div class="row">
                        <div class="col-sm-auto">
                            <Strong>Customer Name </Strong>: &emsp;{{$productData[0]->customer_name}}
                        </div>
                        <div class="col-sm-auto">
                            <Strong>Contact No </Strong>: &emsp;{{$productData[0]->primary_contact_no}}
                        </div>
                        <div class="col-sm-auto">
                            <Strong>Address </Strong>: &emsp;{{$productData[0]->address_line_1}},{{$productData[0]->address_line_2}},{{$productData[0]->area}},{{$productData[0]->location}},{{$productData[0]->city}},{{$productData[0]->pincode}}
                        </div>
                    </div>
                </div>
            
                <div class="alert alert-danger" role="alert" id="total_amount_minus" style="display: none">
                    The total amount cant be in negative..
                </div>

                <div class="card-header border-primary" id="filter_card">
                    <strong>Renew Product</strong>
                </div>
                    <div class="table table-responsive jim-table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <th>Product Name</th>
                                <th>Renew Date</th>
                                <th>Next Renew Date</th>
                                <th>Rent</th>
                                <th>Act. Deposit</th>
                                <th>Adj. Deposit</th>
                                <th>Rem. Deposit</th>
                                <th>Due Months</th>
                                {{-- <th>Collection Of</th> --}}
                                <th>Recieved Payment for month</th>
                                <th>Adjust Deposit</th>
                                <th>Discount</th>
                                <th>Total Due Rent</th>
                            </thead>
                            <tbody>
                                @forelse($productData as $key=>$data)
                                    <tr>
                                        <input type="hidden" name="renewalOrderDetailsId[]" value="{{$data->order_details_id}}">
                                        <td data-label="Product Name">{{$data->product_name}}</td>
                                        <td class="text-nowrap" data-label="Renew Date">{{date('d-m-Y',strtotime($data->pickup_date))}}</td>
                                        <td class="text-nowrap" data-label="Next Renew Date">{{date('d-m-Y',strtotime($orderMonthData[$key]['next_renew_date']))}}</td>
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
                                        <td data-label="Recieved payment month">
                                            <input type="number" class="form-control form-control-sm payment-months" name="payment_months[]" id="payment_months{{$key}}" data-id={{$key}} min="1"  value="{{$orderMonthData[$key]['month_count']}}">
                                        </td>
                                        <td data-label="Adjust Deposit">
                                            <div class="input-group input-group-sm mb-3">
                                                <input type="number" class="form-control form-control-sm deposit-adjust" name="deposit_adjust[]" id="deposit_adjust{{$key}}" value="0">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="deposit_adjust_info" data-toggle="tooltip" data-placement="bottom" title="adjust rent in deposit">
                                                        <i class="fa fa-info-circle text-primary" aria-hidden="true"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-label="Discount" class="">
                                            <input type="number" class="form-control form-control-sm discount-offered" name="discount_offered[]" id="discount_offered{{$key}}" min="0" value="0" data-id={{$key}}>
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
                                        <td colspan="11"></td>
                                        <td class="text-nowrap" class="text-right">
                                            Total: &emsp;<strong><span id="total_due_rent_span">{{array_sum(array_column($orderMonthData,'total_rent'))}}</span></strong>
                                            <input type="hidden" name="total_due_rent" id="total_due_rent_hidden" value="{{array_sum(array_column($orderMonthData,'total_rent'))}}">
                                        </td>    
                                    </tr>
                                    <input type="hidden" name="total_rows" id="total_rows" value="{{$productData->count()}}">
                            </tbody>
                        </table>
                    </div>
                    <div class="row container">
                        <div class="col-md-2">
                            <label><b>Payment Mode :</b></label>
                        </div>  
                        <div class="col-md-4 text-left">
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
                <br>
            
                <div class="card-header border-primary" id="filter_card">
                    <strong>Pickup Product</strong>
                </div>
                    <ul class="list-group list-sm-group list-group-flush ">
                        <li class="list-group-item list-group-item-sm border">
                            <div class="row justify-content-between">
                                <div class="col-sm-auto">
                                    <div class="row">
                                        <div class="col-auto">
                                            <strong>Filter : </strong>
                                        </div>
                                        <div class="col-auto">
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="Indivdual" name="filter" value="individual" class="custom-control-input">
                                                <label class="custom-control-label text-dark" for="Indivdual">Indivdual</label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="Today" name="filter" value="today" checked class="custom-control-input">
                                                <label class="custom-control-label text-dark" for="Today">Today</label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="Tomorrow" name="filter" value="tomorrow" class="custom-control-input">
                                                <label class="custom-control-label text-dark" for="Tomorrow">Tomorrow</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                    
                    <div class="table table-responsive jim-table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <th>Product Name</th>
                                <th>Pickup Date</th>
                                <th>Deposit</th>
                                <th>Adjusted Deposit</th>
                                <th>Remaining Deposit</th>
                            </thead>
                            <tbody>
                                @forelse($productData as $key=>$data)
                                    <tr>
                                        <input type="hidden" name="pickupOrderDetailsId[]" value="{{$data->order_details_id}}">
                                        <td data-label="Product Name" class="text-wrap">{{$data->product_name}}</td>
                                        <td class="text-nowrap" data-label="Pickup Date">
                                            <input type="date" class="form-control pickup-date" name="pickup_date[]" id="pickup_date{{$key}}" value="{{date('Y-m-d')}}">
                                        </td>
                                        <td data-label="Deposit">{{$data->product_deposite}}</td>
                                        <td data-label="Adjusted Deposit">{{$data->adjusted_deposit}}</td>
                                        <td data-label="Remaining Deposit">{{$data->product_deposite - $data->adjusted_deposit}}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td class="text-center" colspan="*">No record</td> 
                                    </tr>
                                    @endforelse
                                    <tr>
                                        <td colspan="4"></td>
                                        <td class="" data-label="Deposit">
                                            Total: {{$productData->pluck('product_deposite')->sum() - $productData->pluck('adjusted_deposit')->sum()}}
                                            <small>(return to customer)</small>
                                        </td>
                                    </tr>
                                    
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="conatiner content-justify-center mt-1">
                        <center><button type="submit" class="btn btn-outline-primary" name="submit" value="renew-and-pickup" @if(!empty($orderNotDelivered)) disabled @endif>Generate Order</button></center>
                    </div>
                
                <br>
            </div>
        </form>
    @endsection
</body>
@section('script')
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
    $('input[type=radio][name=filter]').change('click',function() {     
        var date_val = $(this).val();
        if(date_val=='tomorrow')
        {
            var date = '{{date('Y-m-d',strtotime("+1 days"))}}';
            $('.pickup-date').val(date);
        }   
        if(date_val=='today')
        {
            var date = '{{date('Y-m-d')}}';
            $('.pickup-date').val(date);
        }   
        
    });
</script>    

<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
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