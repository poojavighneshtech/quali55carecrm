@extends('header_and_sidebar')
<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        
        <title>Orders</title>
        {{-- <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script> --}}
        <link rel="stylesheet" href="{{url('/')}}/assets/dist/toast.min.css ">
        <!-- Boostrap 4 CSS -->
   
        @section('styles')
        <style>
            #filter_card{
                position: relative;
            }
            #h6_filter{
                position: absolute;
                right: 50%;
                top: -0.5rem;
                /* z-index: -100; */
            }
        </style>
           <style>
            body {
                background-color: #eee
            }
        
            .mt-70 {
                margin-top: 70px
            }
        
            .mb-70 {
                margin-bottom: 70px
            }
        
            .card {
                box-shadow: 0 0.46875rem 2.1875rem rgba(4, 9, 20, 0.03), 0 0.9375rem 1.40625rem rgba(4, 9, 20, 0.03), 0 0.25rem 0.53125rem rgba(4, 9, 20, 0.05), 0 0.125rem 0.1875rem rgba(4, 9, 20, 0.03);
                border-width: 0;
                transition: all .2s
            }
        
            .card {
                position: relative;
                display: flex;
                flex-direction: column;
                min-width: 0;
                word-wrap: break-word;
                background-color: #fff;
                background-clip: border-box;
                border: 1px solid rgba(26, 54, 126, 0.125);
                border-radius: .25rem
            }
        
            /* .card-body {
                flex: 1 1 auto;
                padding: 1.25rem
            } */
        
            .vertical-timeline {
                width: 100%;
                position: relative;
                padding: 1.5rem 0 1rem
            }
        
            .vertical-timeline::before {
                content: '';
                position: absolute;
                top: 0;
                left: 67px;
                height: 100%;
                width: 4px;
                background: #e9ecef;
                border-radius: .25rem
            }
        
            .vertical-timeline-element {
                position: relative;
                margin: 0 0 1rem
            }
        
            .vertical-timeline--animate .vertical-timeline-element-icon.bounce-in {
                visibility: visible;
                animation: cd-bounce-1 .8s
            }
        
            .vertical-timeline-element-icon {
                position: absolute;
                top: 0;
                left: 60px
            }
        
            .vertical-timeline-element-icon .badge-dot-xl {
                box-shadow: 0 0 0 5px #fff
            }
        
            .badge-dot-xl {
                width: 18px;
                height: 18px;
                position: relative
            }
        
            .badge:empty {
                display: none
            }
        
            .badge-dot-xl::before {
                content: '';
                width: 10px;
                height: 10px;
                border-radius: .25rem;
                position: absolute;
                left: 50%;
                top: 50%;
                margin: -5px 0 0 -5px;
                background: #fff
            }
        
            .vertical-timeline-element-content {
                position: relative;
                margin-left: 90px;
                font-size: .8rem
            }
        
            .vertical-timeline-element-content .timeline-title {
                font-size: .8rem;
                text-transform: uppercase;
                margin: 0 0 .5rem;
                padding: 2px 0 0;
                font-weight: bold
            }
        
            .vertical-timeline-element-content .vertical-timeline-element-date {
                display: block;
                position: absolute;
                left: -90px;
                top: 0;
                padding-right: 10px;
                text-align: right;
                color: #adb5bd;
                font-size: .7619rem;
                white-space: nowrap
            }
            /* .vertical-timeline-element-content .vertical-timeline-element-dateonly {
                display: block;
                position: absolute;
                left: -90px;
                top: 20px;
                padding-right: 10px;
                text-align: right;
                color: #adb5bd;
                font-size: .7619rem;
                white-space: nowrap
            } */
        
            .vertical-timeline-element-content:after {
                content: "";
                display: table;
                clear: both
            }
           
        </style>
        @endsection
    </head>

<body id="page-top">	
        <!-- Page Wrapper -->
        
    @section('content')
        @if(session()->has('message_delete'))
            <div class="alert alert-danger">
                {{ session()->get('message_delete') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if(session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        
        
            <div class="alert alert-danger fade out" id="date_alert" style="display:none">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Date Range</strong> TO date should be greator than start date
            </div>
            <div class="card" id="filter_card">
                <div class="card-header border-primary" id="filter_card">
                    <div class="row">
                        <div class="col text-primary" data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                            <strong>Orders</strong>
                        </div>
                        <div class="col-auto">
                            <a data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                                <i class="fa fa-chevron-down pull-right"></i>&emsp;<b>Filter</b>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body border-primary @if($filter_arr['collapsible_main'] == true){{''}}@else{{'collapse'}}@endif" id="filter-collapse" aria-labelledby="headingTwo" data-parent="#filter_card">
                    <form action="{{url('/')}}/viewall_order_mgmt_filter" method="GET" id="all_order_form">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="cust_name_label"><strong>Name:</strong></span>
                                    </div>
                                    <input type="text" class="form-control" style="" name="filter_customer_name" id="txt_filter_customer_name" 
                                        size="5" autocomplete="off" value="@if(isset($filter_arr['cust_name'])){{$filter_arr['cust_name']}}@endif" placeholder="Customer Name..."
                                        aria-label="Customer Name:" aria-describedby="cust_name_label">
                                    <datalist id="datalist_customers"></datalist>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="contact_no_label"><strong>Contact No:</strong></span>
                                    </div>
                                    <input type="text" class="form-control" name="filter_contact_no"  id="txt_filter_contact_no" placeholder="Contact No..."
                                        oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" 
                                        value="@if(isset($filter_arr['cust_no'])){{$filter_arr['cust_no']}}@endif"
                                        aria-label="Contact No:" aria-describedby="contact_no_label">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="from_date_label"><strong>From:</strong></span>
                                    </div>
                                    <input type="date" name="filter_from_date" id="input_from_date" class="form-control" value="@if(isset($filter_arr['from_date'])){{$filter_arr['from_date']}}@endif"
                                    aria-label="From:" aria-describedby="from_date_label">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="end_date_label"><strong>To:</strong></span>
                                    </div>
                                    <input type="date" name="filter_end_date" id="input_end_date" class="form-control" value="@if(isset($filter_arr['end_date'])){{$filter_arr['end_date']}}@endif"
                                    aria-label="To:" aria-describedby="end_date_label">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="location_label"><strong>Location:</strong></span>
                                    </div>
                                    <input type="text" class="form-control" name="filter_location" id="txt_filter_location"  placeholder="Location..." 
                                        size="5" autocomplete="off" value="@if(isset($filter_arr['location'])){{$filter_arr['location']}}@endif" aria-label="Location:" aria-describedby="location_label">
                                    <datalist id="datalist_location"></datalist>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="order_id_label"><strong>Order Id:</strong></span>
                                    </div>
                                    <input type="text" name="filter_order_id" id="input_order_id" class="form-control" placeholder="Order Id.."
                                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" 
                                                            value="@if(isset($filter_arr['order_id'])){{$filter_arr['order_id']}}@endif"
                                                            aria-label="Order Id:" aria-describedby="order_id_label">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="order_type_label"><strong>Order type:</strong></span>
                                    </div>
                                    <select class="form-control form-control-sm selectpicker"  name="filter_order_type" id="select_filter_order_type"  title="Order Type" aria-label="Order type:" aria-describedby="order_type_label">
                                        <option value="All" @if(isset($filter_arr['order_type']) && $filter_arr['order_type']=='All'){{"selected"}}@endif>All</option>
                                        <option value="Delivery" @if(isset($filter_arr['order_type']) && $filter_arr['order_type']=='Delivery'){{"selected"}}@endif>Delivery</option>
                                        <option value="Collection" @if(isset($filter_arr['order_type']) && $filter_arr['order_type']=='Collection'){{"selected"}}@endif>Collection</option>
                                        <option value="Pick Up" @if(isset($filter_arr['order_type']) && $filter_arr['order_type']=='Pick Up'){{"selected"}}@endif>Pick Up</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="del_status_label"><strong>Del Status:</strong></span>
                                    </div>
                                    <select class="selectpicker form-control form-control-sm" name="filter_delivery_status" id="select_filter_delivery_status" aria-label="Del Status:" aria-describedby="del_status_label">
                                        <option value="All" @if(isset($filter_arr['delivery_status']) && $filter_arr['delivery_status']=='All'){{"selected"}}@endif>All</option>
                                        <option value="Pending" @if(isset($filter_arr['delivery_status']) && $filter_arr['delivery_status']=='Pending')selected @endif>Pending</option>
                                        <option value="Assigned" @if(isset($filter_arr['delivery_status']) && $filter_arr['delivery_status']=='Assigned')selected @endif>Assigned</option>
                                        <option value="Accepted" @if(isset($filter_arr['delivery_status']) && $filter_arr['delivery_status']=='Accepted')selected @endif>Rejected</option>
                                        <option value="InProgress" @if(isset($filter_arr['delivery_status']) && $filter_arr['delivery_status']=='InProgress')selected @endif>In Progress</option>
                                        <option value="Delivered" @if(isset($filter_arr['delivery_status']) && $filter_arr['delivery_status']=='Delivered')selected @endif>Delivered</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="input_patient_name"><strong>Patient Name:</strong></span>
                                    </div>
                                    <input type="text" name="filter_patient_name" id="input_patient_name" class="form-control" placeholder="Patient Name.."
                                            value="{{request()->get('filter_patient_name')}}"
                                            aria-label="Patient Name:" aria-describedby="input_patient_name">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="products_label"><strong>Products:</strong></span>
                                    </div>
                                    <select class="form-control form-control-sm selectpicker" name="master_product[]" id="select_master_product" data-size="5" title="Products" multiple data-live-search="true"  aria-label="Products:" aria-describedby="products_label">
                                        @foreach($get_master_products as $key=>$product)
                                            <option value="{{$product->id}}" @if(isset($filter_arr['master_product']) && in_array($product->id,$filter_arr['master_product']))selected @endif>{{$product->product_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="select city"><strong>City :</strong></span>
                                    </div>
                                    <select class="form-control form-control-sm selectpicker" name="filter_city" id="select_filter_city" data-size="5" title="Select City" data-live-search="true"  aria-label="Select City:" aria-describedby="Select City">
                                        <option value="All" @if(request()->get('filter_city')=='All')selected @endif>All</option>
                                        @foreach($cities as $key=>$city)
                                            <option value="{{$city->citygroup}}" @if(request()->get('filter_city')==$city->citygroup) selected @endif>{{$city->citygroup}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-md-auto">
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btn_clear">Clear Filter</button>
                            </div>
                            <div class="col-md-auto">
                                <button type="submit" class="btn btn-outline-primary btn-sm" name="btn_submit" value="submit">Submit</button>
                            </div>
                            <div class="col-md-auto">
                                <button type="submit" class="btn btn-outline-success btn-sm" name="btn_submit" value="export_excel">Export Excel</button>
                            </div>  
                                <a href="{{url('/')}}/order-search" class="btn btn-sm btn-outline-primary">Order Search</a>
                        </div>
                        {{-- <div class="row ">
                            
                        </div> --}}
                    </form>
                    <div class="card my-2" id="filter_card_del">
                        <div class="card-header border-primary" id="filter_card_del">
                            <div class="row">
                                <div class="col text-primary" data-toggle="collapse" href="#filter-collapse_del" aria-expanded="true" aria-controls="filter-collapse_del" id="heading-filter" class="d-block">
                                    <strong>Delivery Boys</strong>
                                </div>
                                <div class="col-auto">
                                    <a data-toggle="collapse" href="#filter-collapse_del" aria-expanded="true" aria-controls="filter-collapse_del" id="heading-filter" class="d-block">
                                        <i class="fa fa-chevron-down pull-right"></i>&emsp;<b>Filter</b>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body border-primary @if($filter_arr['collapsible'] == true){{''}}@else{{'collapse'}}@endif" id="filter-collapse_del" aria-labelledby="headingTwo" data-parent="#filter_card_del">
                            <form action="{{url('/')}}/viewall_order_mgmt_filter" method="GET" id="all_order_form">
                                @csrf
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="del_boys_label">
                                                    <strong>
                                                        @if($filter_arr['del_boy_state'] == 'Occupied')
                                                            {{'Occupied Del Boys:'}}
                                                        @elseif($filter_arr['del_boy_state'] == 'Available')
                                                            {{'Available Del Boys:'}}
                                                            @else
                                                            {{"Del Boys:"}}
                                                        @endif
                                                    </strong>
                                                </span>
                                            </div>
                                            <select class="form-control form-control-sm selectpicker" name="del_boys" id="select_del_boy" data-size="5" title="Del Boys" aria-label="Del Boys:" aria-describedby="del_boys_label">
                                                @foreach($del_boys as $key=>$del_boy)
                                                    {{-- {{print_r($del_boy->username)}} --}}
                                                    @if($del_boy->username != "Pending")
                                                        <option value="{{$del_boy->username}}" @if(isset($filter_arr['del_boys']) && ($del_boy->username == $filter_arr['del_boys']))selected @endif>{{$del_boy->username}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="del_boy_state_label"><strong>State:</strong></span>
                                            </div>
                                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                <label class="btn btn-sm btn-outline-primary active">
                                                    <input type="radio" name="del_boy_state" id="occupied" value="Occupied" autocomplete="off" @if($filter_arr['del_boy_state'] == 'Occupied'){{'Checked'}}@endif> Occupied
                                                </label>
                                                <label class="btn btn-sm btn-outline-primary">
                                                    <input type="radio" name="del_boy_state" id="available" value="Available" autocomplete="off" @if($filter_arr['del_boy_state'] == 'Available'){{'Checked'}}@endif> Available
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="availability_time_label"><strong>Time:</strong></span>
                                            </div>
                                            <input type="time" class="form-control form-control-sm" name="availability_time" id="availability_time" aria-label="Time:" aria-describedby="availability_time_label" value="{{$filter_arr['availability_time']}}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-outline-primary btn-sm " name="btn_submit" value="submit">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="card">
                <div class="card-header"  style="background-color: #4a6fdc; color: white;" >
                    <center>All Orders</center>
                </div> 
                <div class="card-body">                    
                    <div class="card" id="filter_card">
                        <h6 id="h6_filter"><span class="border border-dark rounded bg-primary text-white">&emsp;Filter&emsp;</span></h6>
                        <div class="card-body">
                            <form action="{{url('/')}}/viewall_order_mgmt_filter" method="GET" id="all_order_form">
                                @csrf
                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="row">
                                            <div class="col-md-7">
                                                <div class="row">
                                                    <div class="col-md-4 text-right">
                                                        <label for="customer_name"><strong>Customer Name:</strong></label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" name="filter_customer_name" id="txt_filter_customer_name"  placeholder="Customer Name.." 
                                                            size="5" autocomplete="off" value="@if(isset($filter_arr['cust_name'])){{$filter_arr['cust_name']}}@endif">
                                                        <datalist id="datalist_customers"></datalist>
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="row">
                                                    <div class="col-md-4 text-right">
                                                        <label for="contact_no"><strong>Contact No :</strong></label>
                                                    </div>
                                                    <div class="col-md-8 text-right">
                                                        <input type="text" class="form-control" name="filter_contact_no"  id="txt_filter_contact_no" placeholder="Contact No..."
                                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" 
                                                            value="@if(isset($filter_arr['cust_no'])){{$filter_arr['cust_no']}}@endif">
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="row">
                                                    <div class="col-md-4 text-right">
                                                        <label for="customer_name"><strong>Location:</strong></label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" name="filter_location" id="txt_filter_location"  placeholder="Location..." 
                                                            size="5" autocomplete="off" value="@if(isset($filter_arr['location'])){{$filter_arr['location']}}@endif">
                                                        <datalist id="datalist_location"></datalist>
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="row">
                                                    <div class="col-md-4 text-right">
                                                        <label for="order_type"><strong>Order Type :</strong></label>
                                                    </div>
                                                    <div class="col-md-8 text-right">
                                                        <select class="form-control selectpicker" name="filter_order_type" id="select_filter_order_type" title="Order Type">
                                                            <option value="All" @if(isset($filter_arr['order_type']) && $filter_arr['order_type']=='All'){{"selected"}}@endif>All</option>
                                                            <option value="Delivery" @if(isset($filter_arr['order_type']) && $filter_arr['order_type']=='Delivery'){{"selected"}}@endif>Delivery</option>
                                                            <option value="Collection" @if(isset($filter_arr['order_type']) && $filter_arr['order_type']=='Collection'){{"selected"}}@endif>Collection</option>
                                                            <option value="Pick Up" @if(isset($filter_arr['order_type']) && $filter_arr['order_type']=='Pick Up'){{"selected"}}@endif>Pick Up</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="row">
                                                    <div class="col-md-4 text-right">
                                                        <label for="order_type"><strong>Products :</strong></label>
                                                    </div>
                                                    <div class="col-md-8 text-right">
                                                        <select class="form-control selectpicker" name="master_product[]" id="select_master_product" data-size="5" title="Products" data-width="100%" multiple data-live-search="true">                                                            
                                                            @foreach($get_master_products as $key=>$product)
                                                                <option value="{{$product->id}}" @if(isset($filter_arr['master_product']) && in_array($product->id,$filter_arr['master_product']))selected @endif>{{$product->product_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="row">
                                                    <div class="col-md-3 text-right">
                                                        From
                                                    </div>
                                                    <div class="col-md-9">
                                                        <input type="date" name="filter_from_date" id="input_from_date" class="form-control" value="@if(isset($filter_arr['from_date'])){{$filter_arr['from_date']}}@endif">
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="row">
                                                    <div class="col-md-3 text-right">
                                                        To
                                                    </div>
                                                    <div class="col-md-9">
                                                        <input type="date" name="filter_end_date" id="input_end_date" class="form-control" value="@if(isset($filter_arr['end_date'])){{$filter_arr['end_date']}}@endif">
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="row">
                                                    <div class="col-md-3 text-right">
                                                        Order Id
                                                    </div>
                                                    <div class="col-md-9">
                                                        <input type="text" name="filter_order_id" id="input_order_id" class="form-control" placeholder="Order Id.."
                                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" 
                                                            value="@if(isset($filter_arr['order_id'])){{$filter_arr['order_id']}}@endif"> 
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="row">
                                                    <div class="col-md-3 text-right">
                                                        Del Status
                                                    </div>
                                                    <div class="col-md-9">
                                                        <select class="selectpicker form-control " name="filter_delivery_status" id="select_filter_delivery_status">
                                                            <option value="All" @if(isset($filter_arr['delivery_status']) && $filter_arr['delivery_status']=='All'){{"selected"}}@endif>All</option>
                                                            <option value="Pending" @if(isset($filter_arr['delivery_status']) && $filter_arr['delivery_status']=='Pending')selected @endif>Pending</option>
                                                            <option value="Assigned" @if(isset($filter_arr['delivery_status']) && $filter_arr['delivery_status']=='Assigned')selected @endif>Assigned</option>
                                                            <option value="Accepted" @if(isset($filter_arr['delivery_status']) && $filter_arr['delivery_status']=='Accepted')selected @endif>Rejected</option>
                                                            <option value="InProgress" @if(isset($filter_arr['delivery_status']) && $filter_arr['delivery_status']=='InProgress')selected @endif>In Progress</option>
                                                            <option value="Delivered" @if(isset($filter_arr['delivery_status']) && $filter_arr['delivery_status']=='Delivered')selected @endif>Delivered</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-secondary btn-sm btn-block" id="btn_clear">Clear Filter</button>
                                        <br>
                                        <button type="submit" class="btn btn-outline-primary btn-block" name="btn_submit" value="submit">Submit</button>
                                        <br>
                                        <button type="submit" class="btn btn-outline-success btn-sm btn-block" name="btn_submit" value="export_excel">Export Excel</button>
                                        <br>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <br> --}}
            <div class="card my-2 border-primary overflow-auto" >
                {{-- <ul class="list-group list-sm-group list-group-flush">
                    <li class="list-group-item list-group-item-sm">
                        <div class="row justify-content-between">
                            <div class="col-sm-auto">
                                <div class="row">
                                    <div class="col-sm-auto">
                                        Total Labour : <strong>{{$totalLabour[0]->total_labour_charges}}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul> --}}
                <div class="table table-responsive table-sm jim-table-responsive">
                    <table class="table table-sm table-hover" id="tbl_all_order">
                        <thead class="thead-light text-nowrap table-sm">
                            <th>Timeline</th>
                            <th>
                                @if(isset($filter_arr['sort_column']) && $filter_arr['sort_column']=='DelDate' && $filter_arr['sort_val']=='DESC')
                                    <a class="text-dark" href="{{request()->fullUrlWithQuery(['sort_column'=>'DelDate','sort_direction'=>'ASC'])}}">Order Date&emsp;<i class="fas fa-sort-down"></i></a>
                                @elseif(isset($filter_arr['sort_column']) && $filter_arr['sort_column']=='DelDate' && $filter_arr['sort_val']=='ASC')
                                    <a class="text-dark" href="{{request()->fullUrlWithQuery(['sort_column'=>'DelDate','sort_direction'=>'DESC'])}}">Order Date&emsp;<i class="fas fa-sort-up"></i></a>
                                @else
                                    <a class="text-dark" href="{{request()->fullUrlWithQuery(['sort_column'=>'DelDate','sort_direction'=>'ASC'])}}">Order Date&emsp;<i class="fas fa-sort"></i></a>
                                @endif
                            </th>
                            <th>Created On</th>
                            <th>
                                @if(isset($filter_arr['sort_column']) && $filter_arr['sort_column']=='order_id' && $filter_arr['sort_val']=='DESC')
                                    <a class="text-dark" href="{{request()->fullUrlWithQuery(['sort_column'=>'order_id','sort_direction'=>'ASC'])}}">Order ID&emsp;<i class="fas fa-sort-down"></i></a>
                                @elseif(isset($filter_arr['sort_column']) && $filter_arr['sort_column']=='order_id' && $filter_arr['sort_val']=='ASC')
                                    <a class="text-dark" href="{{request()->fullUrlWithQuery(['sort_column'=>'order_id','sort_direction'=>'DESC'])}}">Order ID&emsp;<i class="fas fa-sort-up"></i></a>
                                @else
                                    <a class="text-dark" href="{{request()->fullUrlWithQuery(['sort_column'=>'order_id','sort_direction'=>'ASC'])}}">Order ID&emsp;<i class="fas fa-sort"></i></a>
                                @endif
                            </th>
                            <th>
                                @if(isset($filter_arr['sort_column']) && $filter_arr['sort_column']=='shipping_first_name' && $filter_arr['sort_val']=='DESC')
                                    <a class="text-dark" href="{{request()->fullUrlWithQuery(['sort_column'=>'shipping_first_name','sort_direction'=>'ASC'])}}">Customer Name&emsp;<i class="fas fa-sort-down"></i></a>
                                @elseif(isset($filter_arr['sort_column']) && $filter_arr['sort_column']=='shipping_first_name' && $filter_arr['sort_val']=='ASC')
                                    <a class="text-dark" href="{{request()->fullUrlWithQuery(['sort_column'=>'shipping_first_name','sort_direction'=>'DESC'])}}">Customer Name&emsp;<i class="fas fa-sort-up"></i></a>
                                @else
                                    <a class="text-dark" href="{{request()->fullUrlWithQuery(['sort_column'=>'shipping_first_name','sort_direction'=>'ASC'])}}">Customer Name&emsp;<i class="fas fa-sort"></i></a>
                                @endif
                            </th>
                            <th>Patient Name</th>
                            <th>
                                Products
                            </th>
                            <th>Contact No</th>
                            <th>Type/St</th>
                            {{-- <th>
                                @if(isset($filter_arr['sort_column']) && $filter_arr['sort_column']=='deliverypickup' && $filter_arr['sort_val']=='DESC')
                                    <a class="text-dark" href="{{request()->fullUrlWithQuery(['sort_column'=>'deliverypickup','sort_direction'=>'ASC'])}}">Order Type&emsp;<i class="fas fa-sort-down"></i></a>
                                @elseif(isset($filter_arr['sort_column']) && $filter_arr['sort_column']=='deliverypickup' && $filter_arr['sort_val']=='ASC')
                                    <a class="text-dark" href="{{request()->fullUrlWithQuery(['sort_column'=>'deliverypickup','sort_direction'=>'DESC'])}}">Order Type&emsp;<i class="fas fa-sort-up"></i></a>
                                @else
                                    <a class="text-dark" href="{{request()->fullUrlWithQuery(['sort_column'=>'deliverypickup','sort_direction'=>'ASC'])}}">Order Type&emsp;<i class="fas fa-sort"></i></a>
                                @endif
                            </th>
                            <th>
                                @if(isset($filter_arr['sort_column']) && $filter_arr['sort_column']=='status' && $filter_arr['sort_val']=='DESC')
                                    <a class="text-dark" href="{{request()->fullUrlWithQuery(['sort_column'=>'status','sort_direction'=>'ASC'])}}">Del status&emsp;<i class="fas fa-sort-down"></i></a>
                                @elseif(isset($filter_arr['sort_column']) && $filter_arr['sort_column']=='status' && $filter_arr['sort_val']=='ASC')
                                    <a class="text-dark" href="{{request()->fullUrlWithQuery(['sort_column'=>'status','sort_direction'=>'DESC'])}}">Del status&emsp;<i class="fas fa-sort-up"></i></a>
                                @else
                                    <a class="text-dark" href="{{request()->fullUrlWithQuery(['sort_column'=>'status','sort_direction'=>'ASC'])}}">Del status&emsp;<i class="fas fa-sort"></i></a>
                                @endif --}}
                            </th>
                            <th>Del Boy</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach ($get_all_orders as $key=> $order)
                                <tr>
                                    <td data-label="Timeline">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn_timeline" data-id="{{$key}}" data-order_id="{{$order->order_id}}"
                                            data-toggle="modal" data-target="#timelineModal">
                                            <i class="fas fa-history"> Timeline</i>
                                        </button>
                                    </td>
                                    <td data-label="Order Date" class="text-nowrap">
                                        @if($products_arr[$key]['customer_type']=='Corporate')
                                            <span class="h5 badge badge-success" style="font-family:Arial">C</span>
                                        @elseif($products_arr[$key]['customer_type']=='Individual')
                                            <span class="h5 text-Capitaized badge badge-primary" style="font-family:Times New Roman">I</span>
                                        @endif
                                        @if($order->isUpgraded)
                                        |
                                        <span class="h5 text-Capitaized badge badge-primary" style="font-family:Times New Roman">U</span>
                                        @endif
                                        |
                                        <span class="h6">{{date('d-M-Y',strtotime($order->DelDate))}}</span>
                                    </td>
                                    <td data-label="Created On" class="">@if($products_arr[$key]['created_at']!=null){{date('d-M-y h:i',strtotime($products_arr[$key]['created_at']))}}@else - @endif</td>
                                    <td data-label="Order Id" class="text-nowrap">{{$order->order_id}}</td>
                                    <td data-label="Customer Name">{{$order->shipping_first_name}}</td>
                                    <td data-label="Patient Name">{{$order->patient_name}}</td>
                                    <td data-label="Products" class="text-wrap">{{$products_arr[$key]['products']}}</td>
                                    <td data-label="Contact No">{{$order->mobileno}}</td>
                                    <td data-label="Type/St">
                                        @if($order->deliverypickup == 'Delivery')
                                            <span class="badge badge-success">
                                                D
                                            </span>
                                            @if($order->flag=='Replacement')
                                                <span class="badge badge-warning">
                                                    Replace
                                                </span>
                                            @endif
                                        @elseif($order->deliverypickup == 'Collection')
                                            <span class="badge badge-warning">
                                                C
                                            </span>
                                        @elseif($order->deliverypickup == 'Pick Up')
                                            <span class="badge badge-danger">
                                                P
                                            </span>
                                        @else
                                            <span class="badge badge-primary">
                                                {{$order->deliverypickup}}
                                            </span>
                                        @endif
                                        {{"/"}}
                                        @if($order->status == 'Pending')
                                        <span class="badge badge-danger">
                                            {{"PE"}}
                                        </span>
                                        @elseif($order->status == 'Accepted')
                                            <span class="badge badge-secondary">
                                                {{"AC"}}
                                            </span>
                                        @elseif($order->status == 'Assigned')
                                            <span class="badge badge-warning">
                                                {{"AS"}}
                                            </span>
                                        @elseif($order->status == 'InProgress')
                                            <span class="badge badge-primary">
                                                {{"IP"}}
                                            </span>
                                        @elseif($order->status == 'Cancel')
                                            <span class="badge badge-danger">
                                                {{"CA"}}
                                            </span>
                                        @elseif($order->status == 'Collected')
                                            <span class="badge badge-success">
                                                {{"CO"}}
                                            </span>
                                        @elseif($order->status == 'Delivered')
                                            <span class="badge badge-success">
                                                {{"DE"}}
                                            </span>
                                        @elseif($order->status == 'Picked up')
                                            <span class="badge badge-success">
                                                {{"PU"}}
                                            </span>
                                        @endif
                                    </td>
                                    {{-- <td>
                                        <span class="badge @if($order->deliverypickup=='Delivery') badge-primary 
                                                            @elseif($order->deliverypickup=='Pick Up') badge-danger
                                                            @else badge-success @endif">
                                            {{$order->deliverypickup}}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge @if($order->status=='Pending') 
                                                                badge-danger 
                                                            @elseif($order->status=='Assigned') 
                                                                badge-primary  
                                                            @elseif($order->status=='Accepted') 
                                                                badge-info 
                                                            @elseif($order->status=='InProgress') 
                                                                badge-warning  
                                                            @elseif($order->status=='Cancel')
                                                                badge-secondary 
                                                            @else badge-success @endif">
                                            {{$order->status}}
                                        </span>
                                    </td> --}}
                                    <td data-label="Del Boy">{{$order->DelAssignedTo}}</td>
                                    <td data-label="Action" class="text-nowrap">
                                        {{-- @if($order->order_approval_status=='Approved' && $order->deliverypickup=='Delivery')
                                        <a type="button" class="btn btn-outline-primary btn-sm" 
                                            href="{{url('/')}}/approved_order_info/{{$order->order_id}}"
                                            data-toggle="tooltip" data-placement="bottom" title="View Order"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                        @endif
                                        @if($order->deliverypickup=='Delivery' && ($order->status != 'Delivered' && $order->status != 'Cancel'))
                                            <a type="button" class="btn btn-outline-primary btn-sm" 
                                                href="{{url('/')}}/editOrder/{{$order->order_id}}/{{$order->deliverypickup}}"
                                                data-toggle="tooltip" data-placement="bottom" title="Update Order"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                        @endif

                                        @if($order->status != 'Cancel')
                                            <a type="button" class="btn btn-outline-primary btn-sm upload_image"
                                            data-toggle="tooltip" data-placement="bottom" data-id="{{$order->order_id}}" title="Upload Image"><i class="fa fa-upload" aria-hidden="true"></i></a>
                                        @endif

                                        @if($order->status!='Delivered' || $order->status!='Picked Up' || $order->status!='Collected')
                                            @if($order->status!='Cancel')
                                                <a type="button" class="btn btn-outline-danger btn-sm close_order"  
                                                data-toggle="tooltip" data-id="{{$order->order_id}}" data-placement="bottom" title="Close Order"><i class="fas fa-window-close"></i></a>
                                            @endif
                                        @endif
                                        @if($order->status=='Accepted' && $order->status=='Pending')
                                            <a type="button" class="btn btn-outline-success btn-sm" href="{{url('/')}}/assign_deliveryBoy/{{$order->order_id}}" 
                                                data-toggle="tooltip" data-placement="bottom" title="Assign Delivery"><i class="fas fa-shipping-fast"></i></a>
                                        @endif --}}
                                        
                                        <button id="actionButton_list{{$key}}" type="button" class="btn btn-outline-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-tools"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="actionButton_list{{$key}}">
                                            @if(($order->deliverypickup == 'Pick Up' && $order->status=='Picked up') || ($order->deliverypickup == 'Delivery' && $order->status=="Delivered"))
                                                <a id="addLaboour{{$key}}" type="button" class="btn form-control btn-outline-primary btn-sm btn-outline-info addLabour" 
                                                data-order_id={{$order->order_id}} data-floor_no={{$order->floor_no}} data-labour_charges = {{$order->labour_charges}}>
                                                Labour
                                                </a>
                                            @endif
                                            @if($order->deliverypickup == 'Pick Up' || ($order->order_approval_status=='Approved' && $order->deliverypickup=='Delivery'))
                                                <a type="button" class="dropdown-item" 
                                                    href="{{url('/')}}/approved_order_info/{{$order->order_id}}/{{$order->deliverypickup}}"
                                                    data-toggle="tooltip" data-placement="bottom" title="View Order"><i class="fa fa-eye" aria-hidden="true"></i>&emsp;View Order
                                                </a>
                                            @endif

                                            @if(in_array($order->deliverypickup,['Install','Shifting','Repair']))
                                                <a type="button" class="dropdown-item" 
                                                    href="{{url('/')}}/order-maintenance?search_order_id={{$order->order_id}}"
                                                    data-toggle="tooltip" data-placement="bottom" title="View Order"><i class="fa fa-eye" aria-hidden="true"></i>&emsp;View Order
                                                </a>
                                            @endif
                                            
                                            @if($order->deliverypickup=='Delivery' && $order->status != 'Cancel' && $order->settlement_status != 'Y')
                                                <a type="button" class="dropdown-item" 
                                                    href="{{url('/')}}/editOrder/{{$order->order_id}}/{{$order->deliverypickup}}"
                                                    data-toggle="tooltip" data-placement="bottom" title="Update Order"><i class="fa fa-edit" aria-hidden="true"></i>&emsp;Update Order
                                                </a>
                                            @endif

                                            @if($order->status != 'Cancel')
                                                <a type="button" class="dropdown-item upload_image"
                                                    data-toggle="tooltip" data-placement="bottom" data-id="{{$order->order_id}}" title="Upload Image"><i class="fa fa-upload" aria-hidden="true"></i>&emsp;Upload Image
                                                </a>
                                            @endif

                                            @if($order->status!='Delivered' || $order->status!='Picked Up' || $order->status!='Collected')
                                                @if($order->status!='Cancel')
                                                    <a type="button" class="dropdown-item close_order"  
                                                        data-toggle="tooltip" data-id="{{$order->order_id}}" data-placement="bottom" title="Close Order" data-order_type ="{{$order->deliverypickup}}" ><i class="fas fa-window-close"></i>&emsp; Close Order
                                                    </a>
                                                @endif
                                            @endif
                                            @if($order->status=='Accepted' && $order->status=='Pending')
                                                <a type="button" class="dropdown-item" href="{{url('/')}}/assign_deliveryBoy/{{$order->order_id}}" 
                                                    data-toggle="tooltip" data-placement="bottom" title="Assign Delivery"><i class="fas fa-shipping-fast"></i> &emsp;Assign Delivery
                                                </a>
                                            @endif
                                            @if($order->status === 'Cancel')
                                                <a type="button" class="dropdown-item cancel_reason_show"  
                                                    data-toggle="tooltip" data-id="{{$order->order_id}}" data-placement="bottom" title="Cancel Reason"><i class="fa fa-eye" aria-hidden="true"></i>&emsp;View Cancel Reason
                                                </a>
                                            @endif

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @php
                    $append_arr = array();
                    if(isset($filter_arr['cust_name'])){
                        $append_arr['filter_customer_name'] = $filter_arr['cust_name'];
                    }
                    if(isset($filter_arr['cust_no'])){
                        $append_arr['filter_contact_no'] = $filter_arr['cust_no'];
                    }
                    if(isset($filter_arr['from_date'])){
                        $append_arr['filter_from_date'] = $filter_arr['from_date'];
                    }
                    if(isset($filter_arr['end_date'])){
                        $append_arr['filter_end_date'] = $filter_arr['end_date'];
                    }
                    if(isset($filter_arr['order_id'])){
                        $append_arr['filter_order_id'] = $filter_arr['order_id'];
                    }
                    if(isset($filter_arr['sort_column']) && isset($filter_arr['sort_val'])){
                        $append_arr['sort_column'] = $filter_arr['sort_column'];
                        $append_arr['sort_direction'] = $filter_arr['sort_val'];
                    }
                    if(isset($filter_arr['delivery_status'])){
                        $append_arr['filter_delivery_status'] = $filter_arr['delivery_status'];
                    }
                    if(isset($filter_arr['order_type'])){
                        $append_arr['filter_order_type'] = $filter_arr['order_type'];
                    }
                    if(isset($filter_arr['city'])){
                        $append_arr['filter_city'] = $filter_arr['city'];
                    }
                    if(isset($filter_arr['patient_name'])){
                        $append_arr['filter_patient_name'] = $filter_arr['patient_name'];
                    }
                    if(isset($filter_arr['master_product'])){
                        $append_arr['master_product'] = $filter_arr['master_product'];
                    }
                @endphp
                {{$get_all_orders->appends($append_arr)->links('Custom.Pagination.pagination')}}
            </div>
            {{-- time line modal --}}
            <div class="modal fade" id="timelineModal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
                <div class="modal-dialog " role="document">
                    <div class="row d-flex justify-content-center">
                        <div class="main-card card">
                            <div class="card-body">
                                <h5 class="card-title">Order Timeline</h5>
                                <div class="vertical-timeline vertical-timeline--animate vertical-timeline--one-column" id="append_div">
                                </div>
                            </div>
                        </div>
                        {{-- <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        </div> --}}
                    </div>
                </div>
            </div>
                {{-- </div>
            </div> --}}
        </div>
        <div class="modal fade" id="modal_upload_image" tabindex="-1" role="dialog" aria-labelledby="upload_image" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="upload_image">Upload Image for <span id="span_order_id">0000000</span></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form class="form" method="post" action="{{url('/')}}/delivery_upload_image" enctype='multipart/form-data'>
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="hidden_order_id" id="hidden_order_id">
                            <label for="image_file">Upload Image..</label><input type="file" name="image_file" id="image_file">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success" id="btn_upload_image" title="Upload Image">Submit</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Labour charges modal--}}
        <div class="modal fade" id="addLabourModal" tabindex="-1" role="dialog" aria-labelledby="addLabourModal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <strong>Add Labour Charges</strong>
                    </div>
                    <form class="form" method="post" action="{{route('addLabourCharges')}}" enctype='multipart/form-data'>
                        @csrf
                        {{-- hidden --}}
                        <input type="hidden" name="labour_order_id" id="labour_order_id">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-4 text-right">
                                    <label for="">Floor No:</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="number" class="form-control form-control-sm" name="floor_no" id="floor_no">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 text-right">
                                    <label for="">Labour Charges:</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="number" class="form-control form-control-sm" name="labour_charges" id="labour_charges">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-outline-success btn-sm" id="btn_upload_image" title="Upload Image">Submit</button>
                            <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- closed order reason modal --}}
        <div class="modal fade" id="orderClosedModal" tabindex="-1" role="dialog" aria-labelledby="orderClosedModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="orderClosedModal">Close Order - <strong>"<span id="close_orderid_span"></span>"</strong></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{route('order-close')}}" method="post">
                        @csrf
                        <div class="modal-body container-fluid">
                            <div class="table table-responsive" id="closedOrderProductTableDiv">
                                <table class="table">
                                    <thead>
                                        <th>Product name</th>
                                        <th>Sale/Rent</th>
                                        <th>Rent/price</th>
                                        <th>Deposit</th>
                                        <th>Transport</th>
                                    </thead>
                                    <tbody  id="closedOrderProductTable"></tbody>
                                </table>
                            </div>
                            <div class="row">
                                <input type="hidden" name="order_id" id="order_close_orde_id">
                                <select class="form-control form-control-sm selectpicker border border-dark order-close-reason" name="close_reason" id="close_reason" 
                                title="Select Reason"  required>
                                    @foreach ($orderClosedReason as $key=>$reason)
                                        <option value="{{$key}}">{{$reason}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row mt-2">
                                <textarea class="form-control form-control-sm " name="close_remark" id="close_remark" placeholder="Remark..." cols="30" rows="5" ></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-outline-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- closed order reason modal --}}
        <div class="modal fade" id="orderCancelReasonShow" tabindex="-1" role="dialog" aria-labelledby="orderCancelReasonShow" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body container-fluid">
                        <strong>Reason : </strong><span id="order-cancel-reason-span"></span>
                        <br>
                        <strong>Comment : </strong> <span id="order-cancel-comment-span"></span>
                    </div>
                </div>
            </div>
        </div>
          

       
    @endsection
    @section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.0/js/bootstrap-datepicker.min.js"></script>
    {{-- alert on screeen popup Script--}}
    <script src="{{url('/')}}/assets/dist/toast.min.js"></script>

        <script>
            $(".upload_image").on('click', function(){
                let order_id = $(this).data("id");
                $("#hidden_order_id").val(order_id);
                $("#span_order_id").text(order_id);
                $('#modal_upload_image').modal('show');
            });
            $(".close_order").click(function(){
                var order_id = $(this).data("id");
                $('#order_close_orde_id').val(order_id);
                $('#close_orderid_span').text(order_id);
                var dataString = ({_token:"{{ csrf_token() }}",order_id:""+order_id});
                    $.ajax({
                            type: "POST",
                            url: "{{route('order-data')}}",
                            data: dataString,
                            cache:false,
                            success: function (data)
                        {
                            let orderData = data;
                            $('#closedOrderProductTable').empty();
                            let row="";
                            Object.keys(orderData).forEach(function(key){
                                // let disabeldStatus = '';
                                // if(data.orderProducts[key].current_status=='Pending Renew'){
                                //     disabeldStatus = 'disabled=="true"';
                                // };
                                row += "<tr id='trParent'>";
                                    row+="<td class='text-nowrap' data-label='product name'>"+orderData[key].product_name+" <input type='hidden' name='order_details_id[]' value="+orderData[key].id+"></td>";
                                    row+="<td class='text-nowrap' data-label='sale rental'>"+orderData[key].sale_rental+"</td>";
                                    row+="<td class='text-nowrap' data-label='Due Date'><input type='number' class='form-control form-control-sm' name='product_rent[]' value='0' required></td>";
                                    row+="<td data-label='Order Id'><input type='number' class='form-control form-control-sm' name='product_deposit[]' value='0' required></td>";
                                    row+="<td data-label='Inventory Id'> <input type='number' class='form-control form-control-sm' name='product_transport[]' value="+orderData[key].transport+" required></td>";
                                row+="</tr>";
                            });
                            $('#closedOrderProductTable').append(row);
                            //location.reload();
                        }
                    });
                    if($(this).data('order_type')=='Delivery'){
                        $('#closedOrderProductTableDiv').show();
                    }else{
                        $('#closedOrderProductTableDiv').hide();
                    }
                    $('#orderClosedModal').modal('show');
                });

            $(".cancel_reason_show").click(function(){
                let closedReasonArr = @json($orderClosedReason);
                console.log(closedReasonArr);
                var order_id = $(this).data("id");
                // $('#order_close_orde_id').val(order_id);
                // $('#close_orderid_span').text(order_id);
                // // alert(order_id);
                // $('#orderClosedModal').modal('show');
                var dataString = ({_token:"{{ csrf_token() }}",order_id:""+order_id});
                $.ajax({
                    type: "get",
                    url: "{{url('/')}}/order-cancel-show-reason",
                    data: dataString,
                    cache:false,
                    success: function (data)
                    {
                        //console.log(data);
                        let = cancel_key = data[0].cancellation_reason;
                        let reason = closedReasonArr[cancel_key];
                        $('#order-cancel-reason-span').text(reason);
                        $('#order-cancel-comment-span').text(data[0].comment);
                        $('#orderCancelReasonShow').modal('show');
                    }
                });
               
            });


            const TYPES = ['info', 'warning', 'success', 'error'],
                TITLES = {
                    'info': 'Notice!',
                    'success': 'Awesome!',
                    'warning': 'Watch Out!',
                    'error': 'Doh!'
                },
                CONTENT = {
                    'info': 'Hello, world! This is a toast message.',
                    'success': 'The action has been completed.',
                    'warning': 'It\'s all about to go wrong',
                    'error': 'It all went wrong.'
                },
                POSITION = ['top-right', 'top-left', 'top-center', 'bottom-right', 'bottom-left', 'bottom-center'];

            $.toastDefaults.position = 'top-right';
            $.toastDefaults.dismissible = true;
            $.toastDefaults.stackable = true;
            $.toastDefaults.pauseDelayOnHover = true;

            $('.snack').click(function () {
                var type = TYPES[Math.floor(Math.random() * TYPES.length)],
                    content = CONTENT[type];

                $.snack(type, content);
            });

            $('.toast-btn').click(function () {
                var rng = Math.floor(Math.random() * 2) + 1,
                    type = TYPES[Math.floor(Math.random() * TYPES.length)],
                    title = TITLES[type],
                    content = CONTENT[type];

                if (rng === 1) {
                    $.toast({
                        type: type,
                        title: title,
                        subtitle: '11 mins ago',
                        content: content,
                        delay: 5000
                    });
                } else {
                    $.toast({
                        type: type,
                        title: title,
                        subtitle: '11 mins ago',
                        content: content,
                        delay: 5000,
                        img: {
                            src: 'https://via.placeholder.com/20',
                            alt: 'Image'
                        }
                    });
                }
            });
        </script>
    {{-- alert on screeen popup Script close--}}
        <script>
            // $('#tbl_all_order').DataTable({
            //     "paging":   false,
            //     // "ordering": false,
            //     // "info":     false
            //     //"bSearching": false
            //     "bFilter": false,
            //     "bInfo": false,
            //     'columnDefs': [ {
            //         'targets': [3,6], /* column index */
            //         'orderable': false, /* true or false */
            //     }]
            // });

            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            });
            $('#btn_clear').on('click',function(){
                // $('#txt_filter_complaint_name').val('');
                // $('#txt_filter_complaint_id').val('');
                // $('#select_filter_complaint_status').selectpicker('val','All');
                // $('#input_from_date').val('');
                // $('#input_end_date').val('');
                var url="<?php echo url('/');?>/viewall_order_mgmt_filter";
                window.location.href = url;
            });
            var route = "{{ url('complaint_customers_populate') }}";
            $('#txt_filter_customer_name').typeahead({ 
                source: function (query, process) {
                    return $.get(route, {
                        query: query
                    }, function (data) {
                        //var obj = jQuery.parseJSON(data);
                        //console.log(data);
                        return process(data);
                    });
                }
            });
            // var route = "{{ url('location_populate') }}";
            // $('#txt_filter_location').typeahead({ 
            //     source: function (query, process) {
            //         return $.get(route, {
            //             query: query
            //         }, function (data) {
            //             //var obj = jQuery.parseJSON(data);
            //             //console.log(data);
            //             return process(data);
            //         });
            //     }
            // });
            
            $('#input_from_date').on('change',function(){
                let start_date = this.value;
                $('#input_end_date').attr('min',start_date);
            });
            $('#input_end_date').on('change',function(){
                let end_date = this.value;
                $('#input_from_date').attr('max',end_date);
            });
            
        </script>
          <script type="text/javascript">
            function timeFormat(input){
                var d = new Date(Date.parse(input.replace(/-/g, "/")));
                var time = d.toLocaleTimeString().toUpperCase().replace(/([\d]+:[\d]+):[\d]+(\s\w+)/g, "$1$2");
                return (time);  
            }
            function dateFormat(input){
                var d = new Date(Date.parse(input.replace(/-/g, "/")));
                var month = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                var date = d.getDate() + " " + month[d.getMonth()] + ", " + d.getFullYear();
                return (date);  
            }
        
            $('.btn_timeline').on('click',function(){
                let order_id = $(this).data('order_id');
                var dataString = ({_token:"{{ csrf_token() }}"});
                $.ajax({
                    type: "POST",
                    url: "{{url('/')}}/order_timeline/"+order_id,
                    data: dataString,
                    cache:false,
                    success: function (data)
                    {
                        let timeline = JSON.parse(data);
                        console.log(timeline);
                        let row = "";
                        $('#append_div').empty();
                        $.each(timeline,function(key,value){
                            row+='<div class="vertical-timeline-item vertical-timeline-element">';
                                row+='<div> <span class="vertical-timeline-element-icon bounce-in"> <i class="badge badge-dot badge-dot-xl badge-primary"> </i> </span>';
                                    row+='<div class="vertical-timeline-element-content bounce-in">';
                                        row+='<h4 class="timeline-title text-success">'+value.log_lead_status+' <span class="text-dark">('+dateFormat(value.created_at)+')</span></h4>';
                                        if(value.log_order_type=='DO' && value.log_lead_status=='Order Generated'){
                                            row+='<strong class="timeline-title text-success">/ Vendor Assigned </span></strong>';
                                        }
                                        row+='<p>-By '+value.updated_by+'</p>';
                                        row+='<span class="vertical-timeline-element-date text-dark">'+timeFormat(value.created_at)+'</span>';
                                    row+='</div>';
                                row+='</div>';
                            row+='</div>';
                        });
                        
                        $('#append_div').append(row);
                    }
                });
            });

            $('.addLabour').on('click',function(){
                $('#labour_order_id').val($(this).data('order_id'));
                $('#floor_no').val($(this).data('floor_no'));
                $('#labour_charges').val($(this).data('labour_charges'));
                $('#addLabourModal').modal('show');
            })

            $(".order-close-reason").on('change',function(){
                if($(this).val()==99){
                    $('#close_remark').prop('required',true);
                }else{
                    $('#close_remark').prop('required',false);
                }
            })

        </script>
    @endsection
</body>
</html>
