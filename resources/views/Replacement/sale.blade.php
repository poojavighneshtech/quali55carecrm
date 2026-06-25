@extends('header_and_sidebar')

@section('styles')
    <title>Sale Order</title>
    <style>
        .glowing-border {
            border: 1px solid #5052b8;
            border-radius: 12px;
        }
    </style>
@endsection

@section('content')
    @if(session()->has('message'))
        <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            {{ session()->get('message')}} 
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
    <div class="card my-3">
        <div class="card-header border-primary" id="filter_card">
            <div class="row">
                <div class="col text-primary" id="heading-filter" class="d-block">
                    <strong>Create Order</strong>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form action="{{route('sale-product')}}" method="POST">
                @csrf
                <div class="customer-heading">
                    <h5>Customer Details</h5>
                </div>
                <div class="customer-details">
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="customername">Customer Name: </label>
                            <span id="customername">{{$customer->shipping_first_name}}</span>
                            <input type="hidden" name="baseorderid" id="baceorderid" value="{{$customer->order_id}}">
                        </div>
                        <div class="col-md-4">
                            <label for="patientname">Patient Name: </label>
                            <span id="patientname">{{$customer->patient_name}}</span>
                        </div>
                        <div class="col-md-4">
                            <label for="contactno">Contact Number: </label>
                            <span id="contactno">{{$customer->mobileno}}</span>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-12">
                            <label for="contactno">Address: </label>
                            <span id="contactno">{{$customer->fulldetails}}</span>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row form-group">
                    <div class="col-md-4">
                        <label for="orderdate">Date</label>
                        <input type="date" name="orderdate" id="orderdate" class="form-control form-control-sm" value="{{date('Y-m-d')}}" required>
                    </div>
                </div>
                <hr>
                <div class="products">
                    @foreach($products as $key=>$product)
                        <div class="card card-body my-2 glowing-border">
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <span>{{$product->product_name}} - ({{$product->unique_id}})</span>                                    
                                    <span><b>Rent:</b> </span><span id="rts_adjust_rent_txt{{$key}}">{{$product->product_rent}}</span>
                                    <span><b>Deposit:</b> </span><span id="rts_adjust_deposit_txt{{$key}}">{{$product->product_deposite}}</span>
                                    <input type="hidden" name="rts_order_details_id[]" id="rts_order_details_id{{$key}}" value="{{$product->id}}">
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="rts_selling_rate">Selling Rate</label>
                                            <input type="number" name="rts_selling_rate[]" id="rts_selling_rate{{$key}}" class="form-control form-control-sm rts_selling_rate" value="0" min="0">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="rts_adjust_rent">Adjust Rent</label>
                                            <input type="number" name="rts_adjust_rent[]" id="rts_adjust_rent{{$key}}" class="form-control form-control-sm rts_adjust_rent" data-count="{{$key}}" value="0" min="0">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="rts_adjust_deposit">Adjust Deposit</label>
                                            <input type="number" name="rts_adjust_deposit[]" id="rts_adjust_deposit{{$key}}" class="form-control form-control-sm rts_adjust_deposit" data-count="{{$key}}" value="0" min="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="submit text-center">
                    <button type="submit" name="submit" value="" class="btn btn-sm btn-outline-success">Generate Order</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(".rts_adjust_rent").on("input",function(){
            let count = $(this).data("count");
            if(parseInt($(this).val()) > parseInt($("#rts_adjust_rent_txt"+count).text())){
                alert("Amount Should be less than: "+parseInt($("#rts_adjust_rent_txt"+count).text()));
                $(this).val($("#rts_adjust_rent_txt"+count).text());
            }
        });
        $(".rts_adjust_deposit").on("input",function(){
            let count = $(this).data("count");
            if(parseInt($(this).val()) > parseInt($("#rts_adjust_deposit_txt"+count).text())){
                alert("Amount Should be less than: "+parseInt($("#rts_adjust_deposit_txt"+count).text()));
                $(this).val($("#rts_adjust_deposit_txt"+count).text());
            }
        });
    </script>
@endsection