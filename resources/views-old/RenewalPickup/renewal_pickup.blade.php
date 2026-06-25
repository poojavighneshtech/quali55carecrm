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
            /* .morecontent span {
                display: none;
            }
            .morelink {
                display: block;
            } */
            #records tbody td{
                padding: 0.08rem;
            }
        </style>
    @endsection
</head>

<body id="page-top">	
		<!-- Page Wrapper -->
    @extends('header_and_sidebar')
        
    @section('content')
        <div class="leads">
            
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
                {{-- <form action="{{url('/')}}/renewal_pickup_product" method="post"> --}}
                {{-- {{csrf_field()}} --}}
                    <div class="card">
                        <div class="card-header"  style="background-color: #4a6fdc; color: white;" >
                            <center>Renewal Pickup</center>
                        </div> 
                        <div class="card-body">
                            <div class="row ">
                                <div class="col-md-6">
                                    <form action="{{url('/')}}/renewal_pickup" method="GET">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-4">
                                                <input type="date" class="form-control form-control-sm" name="filter_start_date" id="start_date"
                                                    value="@if(!empty(request()->get('filter_start_date'))){{request()->get('filter_start_date')}}@endif" required>
                                            </div>
                                            <div class="col-md-1">
                                                <strong>To</strong>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="date" class="form-control form-control-sm" name="filter_end_date" id="end_date"
                                                    value="@if(!empty(request()->get('filter_end_date'))){{request()->get('filter_end_date')}}@endif"  required>
                                            </div>
                                            <div class="col-md-3">
                                                <button type="submit" class="form-control form-control-sm btn btn-outline-primary btn-sm" id="btn_date_search_customer" name="btn_search" value="date_search">Search</button>
                                            </div>
                                        </div>    
                                    </form>
                                </div>
                                <div class="col-md-6 text-left">
                                    <div class="row">
                                        <div class="col-md-6">
                                            Total Customer <span class="badge badge-light">{{$total_customer}}</span>
                                            Equipment <span class="badge badge-light">{{$total_equipment}}</span>
                                            Amount <span class="badge badge-light">{{$total_due_amount}}</span>
                                        </div>
                                        <div class="col-md-3">
                                            <select class="form-control form-control-sm" name="user_filter" id="user_filter">
                                                <option value="{{url('/')}}/renewal_pickup?date_filter={{request()->get('date_filter')}}&user_filter=all" @if(request()->get('user_filter')=='all')selected @endif>All</option>
                                                <option value="{{url('/')}}/renewal_pickup?date_filter={{request()->get('date_filter')}}&user_filter=14" @if(request()->get('user_filter')=='14')selected @endif>Harddik</option>
                                                <option value="{{url('/')}}/renewal_pickup?date_filter={{request()->get('date_filter')}}&user_filter=15" @if(request()->get('user_filter')=='15')selected @endif>Shraddha</option>
                                                <option value="{{url('/')}}/renewal_pickup?date_filter={{request()->get('date_filter')}}&user_filter=24" @if(request()->get('user_filter')=='24')selected @endif>Reshama</option>
                                                <option value="{{url('/')}}/renewal_pickup?date_filter={{request()->get('date_filter')}}&user_filter=26" @if(request()->get('user_filter')=='26')selected @endif>Namrata</option>
                                                <option value="{{url('/')}}/renewal_pickup?date_filter={{request()->get('date_filter')}}&user_filter=27" @if(request()->get('user_filter')=='27')selected @endif>Sheetal</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select class="form-control form-control-sm" name="date_filter" id="date_filter">
                                                <option value="{{url('/')}}/renewal_pickup?date_filter=today&user_filter={{request()->get('user_filter')}}" @if(request()->get('date_filter')=='today')selected @endif>Today</option>
                                                <option value="{{url('/')}}/renewal_pickup?date_filter=tommorow&user_filter={{request()->get('user_filter')}}" @if(request()->get('date_filter')=='tommorow')selected @endif>Tomorrow</option>
                                                <option value="{{url('/')}}/renewal_pickup?date_filter=overdue&user_filter={{request()->get('user_filter')}}" @if(request()->get('date_filter')=='overdue')selected @endif>Overdue</option>
                                                <option value="{{url('/')}}/renewal_pickup?date_filter=3_days&user_filter={{request()->get('user_filter')}}" @if(request()->get('date_filter')=='3_days')selected @endif>3 Days</option>
                                                <option value="{{url('/')}}/renewal_pickup?date_filter=all&user_filter={{request()->get('user_filter')}}" @if(request()->get('date_filter')=='all')selected @endif>All</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>   
                            <div class="row mt-1">
                                <div class="col-md-6">
                                    <form action="{{url('/')}}/renewal_pickup_search" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-9">
                                                <input type="text" class="form-control form-control-sm" name="text_search_customer" id="text_search_customer" placeholder="Name/Contact no/address/patient name .." value="@if(isset($cust_val)){{$cust_val}}@endif" required>
                                            </div>
                                            <div class="col-md-3">
                                                <button type="submit" class="form-control form-control-sm btn btn-outline-primary btn-sm" id="btn_search_customer" name="btn_search" value="customer_search">Search</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-9">
                                            <form action="{{url('/')}}/renewal_pickup_search" method="post">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="input-group mb-1">
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text">
                                                                    <input type="checkbox" name="shows_only_stops" id="check_shows_only_stops" data-toggle="tooltip" data-placement="bottom" title="Show only stop orders" 
                                                                    required @if($exp_current_value=="CustStop")checked @endif>
                                                                </div>
                                                            </div>
                                                            <div class="input-group-append">
                                                                {{-- <button type="button" class="btn btn-outline-secondary btn-sm btn-block" id="btn_stop_order_status" data-toggle="tooltip" data-placement="bottom" title="Show only stop orders">
                                                                    <span class="badge badge-primary">{{count($getStops)}}</span> Stop Orders
                                                                </button> --}}
                                                                <button type="button" class="btn btn-outline-secondary btn-sm btn-block" data-toggle="modal" data-target=".viewStopProductModal">
                                                                    <span class="badge badge-primary">{{count($getStops)}}</span> Stop Orders
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-3">
                                                        <button type="submit" class="form-control form-control-sm btn btn-outline-primary btn-sm" id="btn_stops_order_search" name="btn_search" value="stop_order_search">Search</button>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <button type="button" class="form-control form-control-sm btn btn-outline-secondary btn-sm"  id="btn_clear" >Clear</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <input type="hidden" name="export_val" id="export_val" value="@if(isset($exp_current_value)){{$exp_current_value}}@endif">
                                            <button type="button" class="btn btn-outline-success btn-sm btn-block" id="export_excel">Export Excel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- </form> --}}
                            <form action="{{url('/')}}/renewal_pickup_product" method="post">
                                @csrf
                                <table class="table table-bordered table-hover" id="records" width="100%">
                                    <thead>
                                        <tr>
                                            <th scope="col">Sr.No.</th>
                                            <th scope="col">Due Date&emsp;</th>
                                            <th scope="col">Customer Name</th>
                                            <th scope="col">Contact No</th>
                                            <th scope="col">Address</th>
                                            <th scope="col">Products</th>
                                            <th scope="col">Lead Owner</th>
                                            {{-- <th scope="col">Comment</th> --}}
                                            <th scope="col">Total due Rent</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $i =0;
                                            $srno=1;
                                        @endphp
                                        @if($customer_products_details !=null)
                                            @foreach ($customer_products_details as $customer_products_detail) 
                                                <tr scope="row" class="data-toggle @if($customer_products_detail['customer_type']=='Corporate') bg-info text-dark @elseif($customer_products_detail['product_details'][0]['current_status']=='CustStop')  bg-danger text-white @endif" data-toggle="collapse"
                                                    data-target="#demo{{$i}}" data-id="{{$i}}">
                                                    <td>{{$customer_products_details->firstItem()+$loop->index}} </td>
                                                    <td>{{date('d-m-Y',strtotime($customer_products_detail['product_details'][0]['pickup_date']))}}</td>
                                                    <td>{{$customer_products_detail['customer_name']}}</td>
                                                    <td>{{$customer_products_detail['customer_contact_no']}}</td>
                                                    <td>{{$customer_products_detail['customer_address']}}</td>
                                                    <td>{{count($customer_products_detail['product_details'])}}</td>
                                                    <td>{{$customer_products_detail['username']}}</td>
                                                    {{-- <td>
                                                        {{substr($customer_products_detail['customer_log'],0,60)}}
                                                        <a class="btn btn-sm" href="" data-toggle="popover" title="Customer Log" data-content="{{$customer_products_detail['customer_log']}}"
                                                            data-book-id="{{$customer_products_detail['customer_log']}}">...</a>
                                                    </td> --}}
                                                    <td>
                                                        @php
                                                            $prduct_rent_col = array_column($customer_products_detail['product_details'],'total_month_rent');
                                                            $product_rent_sum = array_sum($prduct_rent_col);
                                                        @endphp
                                                        {{$product_rent_sum}}
                                                    </td>
                                                </tr>
                                                <tr data-id="{{$i}}" scope="row">
                                                    <td colspan="12" class="hiddenRow">
                                                        <div class="collapse" id="demo{{$i}}">
                                                            
                                                            {{-- hidden values --}}
                                                            <input type="hidden" name="cust_id" value="{{$customer_products_detail['customer_id']}}">
                                                            <input type="hidden" name="customer_id[]" id="customer_id{{$i}}" value="{{$customer_products_detail['customer_id']}}">
                                                            <input type="hidden" name="customer_name" id="customer_name{{$i}}" value="{{$customer_products_detail['customer_name']}}">
                                                            <input type="hidden" name="r_count" id="r_count{{$i}}" value="{{count($customer_products_detail['product_details'])}}">
                                                            <div class="table table-responsive">
                                                                <table class="table table-bordered table-sm table-responsive " id="InTable{{$i}}" width="100%">
                                                                    <thead class="thead-light" style="background-color: #476dda; color:white;">
                                                                        <tr>
                                                                            <th class="nosort">
                                                                                &emsp;&emsp;<input type="checkbox" class="form-check-input" name="check_all[]" id="check_all{{$i}}" value="{{$i}}" data-r_count="{{count($customer_products_detail['product_details'])}}">
                                                                                Sr.No
                                                                            </th>
                                                                            <th>Start Date</th>
                                                                            <th>Due Date</th>
                                                                            <th>Order ID</th>
                                                                            <th>Inventory ID</th>
                                                                            <th>Product Name</th>
                                                                            <th>Vendor Name</th>
                                                                            <th>Quantity</th>
                                                                            <th>Rent</th>
                                                                            <th>Deposit</th>
                                                                            <th>Due Months</th>
                                                                            <th>Total Due Rent</th>                                                                            
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @php
                                                                            $in_sr=1;
                                                                        @endphp
                                                                        @for($j = 0; $j <count($customer_products_detail['product_details']); $j++)
                                                                                {{!$p_date = date('Y-m-d',strtotime($customer_products_detail['product_details'][$j]['pickup_date']))}}
                                                                                {{!$today = date('Y-m-d')}}
                                                                                @php
                                                                                    $quantity_count = count($customer_products_detail['product_details'][$j]['quantity_wise_products']);
                                                                                @endphp
                                                                            <tr class=" @if($p_date<$today || $customer_products_detail['product_details'][$j]['current_status']=='CustStop'){{"table-danger text-black"}} @else{{"table-light"}}@endif" data-in_row_id ="{{$j}}" 
                                                                                scope="row" data-toggle="@if($quantity_count>1){{"collapse"}}@endif" data-target="#quantity_product_table{{$j}}" data-quantity_id="{{$j}}">
                                                                                <td>
                                                                                    &emsp;&emsp;<input type="checkbox" class="form-check-input single_check{{$i}}" name="check[{{$i}}][]" id="single_check{{$i.$j}}" value="{{$j}}" @if($customer_products_detail['product_details'][$j]['current_status']=='Pending Renew'){{"disabled"}}@endif>
                                                                                    <input type="hidden" name="current_status{{$i.$j}}" id="current_status{{$i.$j}}"value="{{$customer_products_detail['product_details'][$j]['current_status']}}">
                                                                                    {{$in_sr}} 
                                                                                </td>
                                                                                <td class="text-nowrap">
                                                                                    {{$customer_products_detail['product_details'][$j]['DelDate']}}
                                                                                </td>
                                                                                <td class="text-nowrap">
                                                                                    {{date('d-m-Y',strtotime($customer_products_detail['product_details'][$j]['pickup_date']))}}
                                                                                </td>
                                                                                <td>{{$customer_products_detail['product_details'][$j]['order_id']}}</td>
                                                                                <td>{{$customer_products_detail['product_details'][$j]['unique_id']}}</td>
                                                                                <td>{{$customer_products_detail['product_details'][$j]['product_name']}}<br><span><small><b>{{$customer_products_detail['product_details'][$j]['upgraded']}}</b></small></span></td>
                                                                                <td>{{$customer_products_detail['product_details'][$j]['vendor_name']}}</td>
                                                                                <td>{{$customer_products_detail['product_details'][$j]['product_qty']}}</td>
                                                                                <td>{{$customer_products_detail['product_details'][$j]['product_rent']}}</td>
                                                                                <td>{{$customer_products_detail['product_details'][$j]['product_deposite']}}</td>
                                                                                <td>{{$customer_products_detail['product_details'][$j]['month_count']}}</td>
                                                                                <td>{{$customer_products_detail['product_details'][$j]['total_month_rent']}}</td>
                                                                                {{-- hidden values --}}
                                                                                <input type="hidden" name="customer_id[{{$i}}][]" id="customer_id{{$j}}" value="{{$customer_products_detail['customer_id']}}">
                                                                                <input type="hidden" name="order_id[{{$i}}][]" id="order_id{{$j}}" value="{{$customer_products_detail['product_details'][$j]['order_id']}}">
                                                                                <input type="hidden" name="order_details_id[{{$i}}][]" value="{{$customer_products_detail['product_details'][$j]['order_details_id']}}">
                                                                                <input type="hidden" name="product_id[{{$i}}][]"  value="{{$customer_products_detail['product_details'][$j]['product_id']}}">
                                                                                <input type="hidden" name="product_name[{{$i}}][]" id="product_name{{$j}}" value="{{$customer_products_detail['product_details'][$j]['product_name']}}">
                                                                                <input type="hidden" name="pickup_date[{{$i}}][]" id="pickup_date{{$j}}" value="{{$customer_products_detail['product_details'][$j]['pickup_date']}}">
                                                                                <input type="hidden" name="product_rent[{{$i}}][]" id="product_rent{{$j}}"  value="{{$customer_products_detail['product_details'][$j]['product_rent']}}">
                                                                                <input type="hidden" name="product_deposite[{{$i}}][]" id="product_deposite{{$j}}" value="{{$customer_products_detail['product_details'][$j]['product_deposite']}}">
                                                                                <input type="hidden" name="due_month_count[{{$i}}][]" id="due_month_count{{$j}}" value="{{$customer_products_detail['product_details'][$j]['month_count']}}">
                                                                                <input type="hidden" name="total_due_month_rent[{{$i}}][]" id="total_due_month_rent{{$j}}" value="{{$customer_products_detail['product_details'][$j]['total_month_rent']}}">
                                                                                {{-- hidden values close--}}
                                                                            </tr>
    
                                                                            {{--Quantity Wise Products--}}
                                                                            @if(isset($customer_products_detail['product_details'][$j]['quantity_wise_products']))
                                                                                <tr data-quantity_id="{{$j}}" scope="row">
                                                                                    <td colspan="12" class="hiddenRow">
                                                                                        <div class="collapse" id="quantity_product_table{{$j}}">
                                                                                            <table  class="table table-bordered table-sm " id="QuantityTable{{$j}}" width="100%">
                                                                                                <thead>
                                                                                                    <tr>
                                                                                                        <th>Sr NO</th>
                                                                                                        <th>Start Date</th>
                                                                                                        <th>Due Date</th>
                                                                                                        <th>Order ID</th>
                                                                                                        <th>Inventory Id</th>
                                                                                                        <th>Product Name</th>
                                                                                                        <th>Quantity</th>
                                                                                                        <th>Rent</th>
                                                                                                        {{-- <th>Deposit</th> --}}
                                                                                                    </tr>
                                                                                                </thead>
                                                                                                <tbody>
                                                                                                    
                                                                                                    {{!$quantity_table_no=1}}
                                                                                                    @for($k= 0; $k<count($customer_products_detail['product_details'][$j]['quantity_wise_products']); $k++)
                                                                                                        <tr>
                                                                                                            <td>{{$quantity_table_no}}</td>
                                                                                                            <td><td>{{$customer_products_detail['product_details'][$j]['quantity_wise_products'][$k]['DelDate']}}</td></td>
                                                                                                            <td>{{$customer_products_detail['product_details'][$j]['quantity_wise_products'][$k]['pickup_date']}}</td>
                                                                                                            <td>{{$customer_products_detail['product_details'][$j]['quantity_wise_products'][$k]['order_id']}}</td>
                                                                                                            <td>{{$customer_products_detail['product_details'][$j]['quantity_wise_products'][$k]['unique_id']}}</td>
                                                                                                            <td>{{$customer_products_detail['product_details'][$j]['quantity_wise_products'][$k]['product_name']}}<br><span><small>{{$customer_products_detail['product_details'][$j]['quantity_wise_products'][$k]['upgraded']}}</small></span></td>
                                                                                                            <td>{{$customer_products_detail['product_details'][$j]['quantity_wise_products'][$k]['product_qty']}}</td>
                                                                                                            <td>{{$customer_products_detail['product_details'][$j]['quantity_wise_products'][$k]['product_rent']}}</td>
                                                                                                            {{-- <td>{{$customer_products_detail['product_details'][$j]['quantity_wise_products'][$k]['product_deposite']}}</td> --}}
                                                                                                        </tr>
                                                                                                        {{!$quantity_table_no++}}
                                                                                                    @endfor
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                            @endif
                                                                            {{--Quantity Wise Products close--}}
                                                                            
                                                                            {{!$in_sr++}}
                                                                        @endfor
                                                                    
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <input type="hidden" name="in_row_ct" id="in_row_ct" value="{{$j}}">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <center>
                                                                        <button type="submit" class="btn btn-primary" id="renewal_btn{{$i}}" name="renewal_pickup_btn" value="Renew" disabled>Renew</button>
                                                                        <button type="submit" class="btn btn-primary" id="pickup_btn{{$i}}" name="renewal_pickup_btn" value="Pickup" disabled>Pickup</button>
                                                                        <button type="button" class="btn btn-outline-primary reason_popup" id="stop_request{{$i}}" name="renewal_pickup_btn" value="StopRequest" disabled>Stop Request</button>
                                                                        <button type="button" class="btn btn-outline-warning" id="send_reminder{{$i}}" name="renewal_pickup_btn" value="Send_Reminder" disabled>Send Reminder</button>
                                                                        {{-- <input type="submit" class="btn btn-primary" id="renewal" name="renewal" value="Renewal">    
                                                                        <input type="submit" class="btn btn-primary" id="pickup" name="pickup" value="Pickup">    
                                                                        <input type="submit" class="btn btn-outline-warning" id="send_reminder" name="send_reminder" value="Send Reminder">--}}
                                                                    </center>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                    </td>
                                                </tr>
                                                @php
                                                    $srno++;
                                                    $i++;
                                                @endphp
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="12"><center><h3>No records found</h3></center></td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                                @php
                                    $upd_arr = array();
                                    if(!empty(request()->get('date_filter')))
                                    {
                                        $upd_arr['date_filter'] = request()->get('date_filter');
                                    }
                                    if(!empty(request()->get('user_filter')))
                                    {
                                        $upd_arr['user_filter'] = request()->get('user_filter');
                                    }
                                    if(!empty($cust_val))
                                    {
                                        $upd_arr['text_search'] = $cust_val;
                                    }
                                @endphp
                                {{$customer_products_details->withPath(url()->current())->appends($upd_arr)->links('Custom.Pagination.pagination')}}

                                {{-- reason modal --}}
                                <div class="modal fade" id="stopRequestModal" tabindex="-1" role="dialog" aria-labelledby="stopRequestModalTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="stopRequestModalTitle">Stop Reason</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <textarea class="form-control" name="stop_request_reason" id="stop_request_reason" cols="30" rows="6" placeholder="Stop requested reason..."></textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary" id="stop_request_reason_modal_btn" name="renewal_pickup_btn" value="StopRequest" disabled>Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <input type="hidden" name="out_row_ct" id="out_row_ct" value="{{$i}}">        
                        </div>

                        

                        <!-- Modal: modalAbandonedCart-->
                        <div class="modal fade" id="pop_msg_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <form action="{{url('/')}}/send_reminder" method="post">
                                        @csrf
                                        <div class="modal-header bg-success text-white">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            {{-- <p>reminder send to <b><span id="pop_cust_name"></span></b></p> --}}
                                            <input type="hidden" name="pop_cust_id" id="pop_cust_id" value="">
                                            <input type="hidden" name="pop_cust_name" id="pop_cust_name" value="">
                                            <p>Send Reminder to Customer</p>
                                            <p>reminder send for product renew or pickup</p>
                                            <div id="div_pop_table"></div>
                                            <input type="hidden" name="hid" value="df">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="Submit" class="btn btn-outline-primary" id="send_reminder" name="renewal_pickup_btn" value="Send_Reminder">Send Reminder</button>
                                            {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Modal: modalAbandonedCart-->
                    </div>
                {{-- </form> --}}
                 {{--view stop Products cash--}}
                 <div class="modal fade viewStopProductModal" tabindex="-1" role="dialog" id="viewStopProducts" aria-labelledby="viewStopProducts" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                      <div class="modal-content">
                          <div class="modal-body">
                              <div class="table table-responsive">
                                  <table class="table" id="stopProductsTable">
                                      <thead>
                                          <th>Sr No</th>
                                          <th>Product Name</th>
                                          <th>Product Count</th>
                                          <th>Action</th>
                                      </thead>
                                      <tbody>
                                          @php                                            
                                              $srno = 1;
                                          @endphp
                                          @foreach($getProductwiseData as $key =>$productData)
                                              <tr>
                                                  <td>{{$srno++}}</td>
                                                  <td>{{$productData[0]->product_name}}</td>
                                                  <td>{{count($productData)}}</td>
                                                  <td>
                                                      {{-- <a class="btn btn-outline-primary btn-sm" href="{{request()->fullUrlWithQuery(['product_id'=>$key,'btn_search'=>'stop_product_search'])}}">Search</a> --}}
                                                      <a class="btn btn-outline-primary btn-sm" href="{{route('renewal_pickup_search_get',['product_id'=>$key,'btn_search'=>'stop_product_search'])}}">Search</a>
                                                  </td>
                                              </tr>
                                          @endforeach
                                      </tbody>
                                  </table>
                              </div>
                          </div>
                      </div>
                    </div>
                  </div>
        </div>
      
    @endsection
</body>
@section('script')
    {{-- @if(session()->has('message_pop') || session()->has('pop_per_cust_name'))
        <script>
            $(function() {
                $('#pop_msg_modal').modal('show');
            });
        </script>
    @endif --}}
    
<script>
    
//  $(document).ready(function(){ $('#records').DataTable(); });
    $('document').ready(function() {
        var count = $('#out_row_ct').val();
        // for(i=0;i<count;i++)
        // {
        //     $('#InTable'+i).dataTable({
        //         "bPaginate": true,
        //         "bJQueryUI": true, 
        //         "bLengthChange": true,
        //         "bFilter": true,
        //         "bSort": false,
        //         "bInfo": true,
        //         "bAutoWidth": true,
        //         "bProcessing": true,
        //         "iDisplayLength": 25,
        //         });
        //         //$('#InTable'+i).DataTable();
        // }
        //document.querySelector('input[name=date_filter][id=overdue]').checked = true;
        if(localStorage['filtered']=="today")
        {
            $('#today').attr('checked','checked');
        }
        else if(localStorage['filtered']=="all")
        {
            $('#all').attr('checked','checked');
        }
        else if(localStorage['filtered']=="tommorow")
        {
            $('#tomorrow').attr('checked','checked');
        }
        else if(localStorage['filtered']=="overdue")
        {
            $('#overdue').attr('checked','checked');
        }
        else if(localStorage['filtered']=="3_days")
        {
            $('#3_days').attr('checked','checked');
        }

    });

    $('#records tr').click(function() {    
        var count = this.dataset.count;
           
        var user_id = $('#user_id'+count).val(); 
        var customer_id = $('#customer_id'+count).val();
        //document.getElementById('add_customer_comment').href ="<?php echo url('/');?>/add_customer_comment/"+user_id+"/"+customer_id;
        // window.location.assign(url);
        var in_count = this.dataset.id;
        var r_count = $('#r_count'+in_count).val();
        $('#check_all'+in_count).click(function() 
        {   
            //var inRowCount = $('#InTable'+in_count+' tr').length;
            //alert(inRowCount);
            var r_count = this.dataset.r_count;
            if($(this).is(':checked'))
            {
                for(j=0;j<r_count;j++)
                {
                    if($('#single_check'+in_count+j).is(':disabled'))
                    {
                        $('#single_check'+in_count+j).prop('checked',false);
                    }
                    else
                    {
                        $('#single_check'+in_count+j).prop('checked',true);
                    }
                    if($('#current_status'+in_count+j).val()=='CustStop'){
                        $('#stop_request'+in_count).attr('disabled',true);
                        $('#send_reminder'+in_count).attr('disabled',true);
                    }else{
                        $('#stop_request'+in_count).attr('disabled',false);
                        $('#send_reminder'+in_count).attr('disabled',false);
                    }
                }
                $('#renewal_btn'+in_count).attr('disabled',false);
                $('#pickup_btn'+in_count).attr('disabled',false);
                $('#send_reminder'+in_count).attr('disabled',false);
                $('#stop_request'+in_count).attr('disabled',false);
            }
            else
            {
                for(j=0;j<r_count;j++)
                {
                    $('#single_check'+in_count+j).prop('checked',false);
                }
                $('#renewal_btn'+in_count).attr('disabled',true);
                $('#pickup_btn'+in_count).attr('disabled',true);
                $('#send_reminder'+in_count).attr('disabled',true);
                $('#stop_request'+in_count).attr('disabled',true);
            }
        });
        
        for(j=0;j<r_count;j++)
        {
            $('#single_check'+in_count+j).click(function(){
                let nj = j-1;
                if($(this).is(':checked'))
                {
                    $('#renewal_btn'+in_count).attr('disabled',false);
                    $('#pickup_btn'+in_count).attr('disabled',false);
                    //$('#send_reminder'+in_count).attr('disabled',false);
                    if($('#current_status'+in_count+nj).val()=='CustStop'){
                        $('#stop_request'+in_count).attr('disabled',true);
                        $('#send_reminder'+in_count).attr('disabled',true);
                    }else{
                        $('#stop_request'+in_count).attr('disabled',false);
                        $('#send_reminder'+in_count).attr('disabled',false);
                    }
                  
                }
                else
                {
                    var checked_count = $('.single_check'+in_count+':checked').length;
                    if(checked_count==0)
                    {
                        $('#renewal_btn'+in_count).attr('disabled',true);
                        $('#pickup_btn'+in_count).attr('disabled',true);
                        $('#send_reminder'+in_count).attr('disabled',true);
                        $('#stop_request'+in_count).attr('disabled',true);
                    }
                    
                }
            });
        }
        $('#send_reminder'+in_count).click(function(){
            var product_name = [];
            var pickup_date = [];
            var product_rent = [];
            var due_month_count = [];
            var total_due_month_rent = [];
            for(j=0;j<r_count;j++)
            {
                if($('#single_check'+in_count+j).is(':checked'))
                {
                    var check_val = $('#single_check'+in_count+j).val();
                    product_name.push($('#product_name'+check_val).val());
                    pickup_date.push($('#pickup_date'+check_val).val());
                    product_rent.push($('#product_rent'+check_val).val());
                    due_month_count.push($('#due_month_count'+check_val).val());
                    total_due_month_rent.push($('#total_due_month_rent'+check_val).val());
                }
            }
            
            var get_cust_name = $('#customer_name'+in_count).val();
            var get_cust_id = $('#customer_id'+in_count).val();
            $('#pop_cust_name').val(get_cust_name);
            $('#pop_cust_id').val(get_cust_id);
            $('#div_pop_table').empty();
            $('#div_pop_table').append('<table id="pop_table" class="table table-bordered"><thead><tr><th>Product Name</th> <th>Pickup Date</th> <th>Product Rent</th><th>Due Months</th><th>Total Due Rent</th></tr><thead><tbody></tbody></table>');
            for(var i = 0; i < product_name.length; i++)
            {

                $('#pop_table tbody').append('<tr><td>'+product_name[i]+'<input type="hidden" name="product_name[]" id="product_name" value="'+product_name[i]+'"> </td> <td>'+pickup_date[i]+'<input type="hidden" name="pickup_date[]" id="pickup_date" value="'+pickup_date[i]+'"> </td> <td>'+product_rent[i]+' <input type="hidden" name="product_rent[]" id="product_rent" value="'+product_rent[i]+'"> </td> <td>'+due_month_count[i]+' <input type="hidden" name="due_month_count[]" id="due_month_count" value="'+due_month_count[i]+'"> </td> <td>'+total_due_month_rent[i]+' <input type="hidden" name="total_due_month_rent[]" id="total_due_month_rent" value="'+total_due_month_rent[i]+'"> </td></tr>');
                
            }
                $('#pop_msg_modal').modal('show');
        });

        var r_count = $('#r_count'+in_count).val();
       
    });


    // $('input[type=radio][name=date_filter]').change('click',function() {                
    //     window.location.assign(this.value);
    // });

    $('#date_filter').on('change',function(){
        window.location.assign(this.value);
    });
    $('#user_filter').on('change',function(){
        window.location.assign(this.value);
    });
   

    $("#search_customer").on("change",function()
    {
        var cust_id = $(this).val();
        var filter_val = localStorage['filtered'];
        var url = "<?php echo url('/');?>/renewal_pickup/"+filter_val+"/"+cust_id;
        window.location.assign(url);

    });

    $(document).ready(function() {
        $('[data-toggle="popover"]').popover({
            placement: 'left',
            trigger: 'hover'
        });
    });

    $('#export_excel').on('click',function(){
        let export_val = $('#export_val').val();
        let start_date = $('#start_date').val();
        let end_date = $('#end_date').val();
        let text_val = $('#text_search_customer').val();
        let filter_arr = ['Overdue','Today','3_Days','Tomorrow','All'];
        //var dataString = ({_token:"{{ csrf_token() }}",export_val:""+export_val,start_date:""+start_date,end_date:""+end_date,text_val:""+text_val});
        var query = {
            export_val:export_val,
            start_date:start_date,
            end_date:end_date,
            text_val:text_val,
        }
        var url = "{{URL::to('excel_exp')}}?" + $.param(query);
        window.location = url;
        
    });

    // $('#btn_stop_order_status').on('click',function(){
        
    //     // if($('#check_shows_only_stops').is(':checked')){
    //     //     $('#check_shows_only_stops').prop('checked',false);
    //     // }else{
    //     //     $('#check_shows_only_stops').prop('checked',true);
    //     // }
    //     $("#viewStopProducts").modal('show');
    // });
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
    $('#btn_clear').on('click',function(){
        var url="<?php echo url('/');?>/renewal_pickup";
        window.location.href = url;
    });
    $('#stopProductsTable').DataTable();
    
    $('.reason_popup').on('click',function(){
        $('#stopRequestModal').modal('show');
    });
    $('#stop_request_reason').on('input',function(){
        if($(this).val().length >0){
            $('#stop_request_reason_modal_btn').attr('disabled',false);
        }else{
            $('#stop_request_reason_modal_btn').attr('disabled',true);
        }
    });
</script>     

@endsection
</html>