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

    <title>Pickup Order</title>
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
                    Delivery order is not completed for the order id <strong>{{implode(",",$orderNotDelivered)}}</strong>. Pickup order can't be generated.
                </div>
            @endif
        </div>
        <div class="card">
            <div class="card-header border-primary" id="filter_card">
                <strong>Renew Product</strong>
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
            <form action="{{route('order-call')}}" method="post">
                @csrf
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
                            <th>Product Rent</th>
                            <th>Due Months</th>
                            <th>Total Due Rent</th>
                        </thead>
                        <tbody>
                            @forelse($productData as $key=>$data)
                                <tr>
                                    <input type="hidden" name="order_details_id[]" value="{{$data->order_details_id}}">
                                    <td data-label="Product Name" class="text-wrap">{{$data->product_name}}</td>
                                    <td class="text-nowrap" data-label="Pickup Date">
                                        <input type="date" class="form-control pickup-date" name="pickup_date[]" id="pickup_date{{$key}}" value="{{date('Y-m-d')}}">
                                    </td>
                                    <td data-label="Deposit">{{$data->product_deposite}}</td>
                                    <td data-label="Adjusted Deposit">{{$data->adjusted_deposit}}</td>
                                    <td data-label="Remaining Deposit">{{$data->product_deposite - $data->adjusted_deposit}}</td>
                                    <td data-label="Product Rent">
                                        {{$data->product_rent}}
                                    </td>
                                    <td data-label="Due Months">{{$orderMonthData[$key]['month_count']}}</td>
                                    <td data-label="Total Due Rent">
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
                                    <td colspan="2"></td>
                                    <td class="" data-label="Deposit">
                                        Total: {{$productData->pluck('product_deposite')->sum() - $productData->pluck('adjusted_deposit')->sum()}}
                                        <small>(return to customer)</small>
                                    </td>
                                    <td colspan="2"></td>
                                    <td data-label="Rent">
                                        Total: {{array_sum(array_column($orderMonthData,'total_rent'))}}
                                        <small>(Collect from customer)</small>
                                    </td>
                                </tr>
                                
                        </tbody>
                    </table>
                </div>
                
                <div class="conatiner content-justify-center mt-1">
                    <center><button type="submit" class="btn btn-outline-primary" name="submit" value="pickup" @if(!empty($orderNotDelivered)) disabled @endif>Pickup Order</button></center>
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

@endsection
</html>