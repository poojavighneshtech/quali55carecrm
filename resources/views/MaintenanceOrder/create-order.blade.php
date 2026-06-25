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

    <title>Order Maintenance</title>
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

        <div class="card">
            <div class="card-header border-primary" id="filter_card">
                <div class="row">
                    <div class="col text-primary" data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                        <strong>Maintenance Orders</strong>
                    </div>
                    <div class="col-auto">
                        <a data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                            <i class="fa fa-chevron-down pull-right"></i>&emsp;<b>Filter</b>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body collapse" id="filter-collapse" aria-labelledby="headingTwo" data-parent="#filter_card">
                <form action="{{route('order-maintenance')}}" method="GET">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="input-group input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="cust_name_label"><strong>Customer</strong></span>
                                        </div>
                                        <input type="text" class="form-control form-control-sm" style="" name="search_customer" id="search_customer" 
                                            size="5" autocomplete="off"  placeholder="Customer Name / Number"
                                            aria-label="Customer Name:" aria-describedby="cust_name_label" value="{{request()->get('search_customer')}}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="input-group input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="search_order_id_label"><strong>Order ID</strong></span>
                                        </div>
                                        <input type="text" class="form-control form-control-sm" style="" name="search_order_id" id="search_order_id" 
                                            size="5" autocomplete="off"  placeholder="Order id.."
                                            aria-label="Order Id.." aria-describedby="search_order_id_label" value="{{request()->get('search_order_id')}}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                   <select class="selectpicker form-control form-control-sm border border-dark" name="search_type" id="search_type" title="Order Type">
                                        <option value="All" @if(request()->get('search_type')=='All') selected @endif>All</option>
                                        <option value="Repair" @if(request()->get('search_type')=='Repair') selected @endif>Repair</option>
                                        {{-- <option value="Replace" @if(request()->get('search_type')=='Replace') selected @endif>Replacement</option> --}}
                                        <option value="Install" @if(request()->get('search_type')=='Install') selected @endif>Installation</option>
                                        <option value="Shifting" @if(request()->get('search_type')=='Shifting') selected @endif>Shifting</option>
                                   </select>
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-group input-group-sm mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="search_from_date_label"><strong>From</strong></span>
                                                </div>
                                                <input type="date" class="form-control form-control-sm search-date" style="" name="search_from_date" id="search_from_date" 
                                                    aria-label="Customer Name:" aria-describedby="search_from_date_label" value="{{request()->get('search_from_date')}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-group input-group-sm mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="search_to_date_label"><strong>To</strong></span>
                                                </div>
                                                <input type="date" class="form-control form-control-sm search-date" style="" name="search_to_date" id="search_to_date" 
                                                    aria-label="Customer Name:" aria-describedby="search_to_date_label" value="{{request()->get('search_to_date')}}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <a class="btn btn-outline-danger btn-sm btn-block" href="{{route('order-maintenance')}}" id="clear_filter">Clear</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-primary btn-sm btn-block" id="create_order">Create Order</button>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-outline-success btn-sm btn-block" id="search_submit">Search</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="table table-responsive">
                <table class="table">
                    <thead>
                        <th>Order Date</th>
                        <th>Order Id</th>
                        <th>Customer Name</th>
                        <th>Patient Name</th>
                        <th>Products</th>
                        <th>Contact No</th>
                        <th>Type</th>
                        <th>Del Boy</th>
                        <th>Cost</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        @foreach ($delMaintenanceOrders as $key=>$order)
                            <tr>
                                <td>{{$order->DelDate}}</td>
                                <td>{{$order->order_id}}</td>
                                <td>{{$order->shipping_first_name}}</td>
                                <td>{{$order->patient_name}}</td>
                                <td>{{$order->line_item_1}}</td>
                                <td>{{$order->mobileno}}</td>
                                <td>{{$order->deliverypickup}}</td>
                                <td>{{$order->DelAssignedTo}}</td>
                                <td>{{$order->TotalAmt}}</td>
                                <td>
                                    @if($order->status!='Cancel')
                                        <div class="dropdown dropleft">
                                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="action_menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>

                                            <div class="dropdown-menu dropdown-menu-lg-right" aria-labelledby="action_menu">
                                                <button class="dropdown-item view-order" type="button" data-order_id = {{$order->order_id}}>View</button>
                                                <button class="dropdown-item update-order" type="button" data-order_id = {{$order->order_id}}>Update Order</button>
                                                <button class="dropdown-item cancel-order" type="button" data-order_id = {{$order->order_id}}>Cancel Order</button>
                                            </div>
                                        </div>
                                    @else
                                        <span class="badge badge-danger">Closed</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{$delMaintenanceOrders->links('Custom.Pagination.pagination')}}
            </div>
        </div>

        <div class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="create_order_modal" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <strong>Create Order</strong>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" class="form-control form-control-sm" name="search_customer" id="txt_search_customer" placeholder="search customer name,contact no,address...."> 
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-outline-primary btn-sm btn-block" id="btn_search_customer">Submit</button>
                            </div>
                        </div>
                        <div class="card my-2" id="customer_list_div" style="display: none">
                            <div class="card-header">
                                <strong>Customer List</strong>
                                <button type="button" class="close" aria-label="Close" value="customer_list_div">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="table table-responsive my-2" >
                                <table class="table" id="customer_list_table">
                                    <thead>
                                        <th>Date</th>
                                        <th>Customer Name</th>
                                        <th>Patient Name</th>
                                        <th>Contact No</th>
                                        <th>Address</th>
                                        <th>Action</th>
                                    </thead>
                                    <tbody id="customer_table_body"></tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card my-2"  id="customer_live_products" style="display: none">
                            <div class="card-header">
                                <strong>Live Products</strong>
                                <button type="button" class="close" aria-label="Close" value="customer_live_products">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            
                            <div class="card-body mt-2" id="customer_details_div">
                                <div class="row">
                                    <div class="col-md-auto">
                                        <strong>Customer Name :</strong>
                                    </div>
                                    <div class="col-md-auto">
                                        <span id="customer_name"></span>
                                    </div>
                                    <div class="col-md-auto">
                                        <strong>Contact No :</strong>
                                    </div>
                                    <div class="col-md-auto">
                                        <span id="customer_contact"></span>
                                    </div>
                                    <div class="col-md-auto">
                                        <strong>Address :</strong>
                                    </div>
                                    <div class="col-md-auto">
                                        <span id="customer_address"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="table table-responsive mt-2">
                                <table class="table" id="customer_live_products_table">
                                    <thead>
                                        <th>Action</th>
                                        <th>Product Name</th>
                                        <th>Inventory Id</th>
                                        <th>Vendor Name</th>
                                    </thead>
                                    <tbody id="customer_live_products_tbody"></tbody>
                                </table>
                            </div>
                            <div class="row container container-fluid justify-content-center my-3">
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong>Order type :</strong>
                                        </div>
                                        <div class="col-md-8">
                                            <select class="form-control form-control-sm selectpicker border border-dark" name="order_type" id="order_type">
                                                <option selected disabled >--Select Order Type--</option>
                                                <option value="Repair">Repair/Checking</option>
                                                {{-- <option value="Replace">Replace</option> --}}
                                                <option value="Install">Installation</option>
                                                <option value="Shifting">Shifting</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card my-2" id="order_details_div" style="display: none">
                            <div class="card-header">
                                <strong>Order Details</strong>
                                <button type="button" class="close" aria-label="Close" value="order_details_div">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="card-body">
                                <form action="{{route('order-maintenance-create')}}" method="post">
                                    @csrf
                                    {{-- customer data hidden --}}
                                    <input type="hidden" name="customer_id" id="customer_id_hid">
                                    <input type="hidden" name="products" id="products_hid">
                                    <input type="hidden" name="order_type" id="order_type_hid">

                                    {{-- order ids hidden store --}}
                                    <input type="hidden" name="del_order_id" id="del_order_id_hid">
                                    <input type="hidden" name="maintenance_ids" id="maintenance_ids_hid">

                                    <div class="row" id="ord_details_repair_install_div" style="display: none">
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <div class="row form-row ">
                                                        <div class="col-md-6">
                                                            <Strong>Address</Strong>
                                                        </div>
                                                        <div class="col-md-6 ml-auto">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input" name="customer_address_check" id="customer_address_check">
                                                                <label class="custom-control-label" for="customer_address_check">Same as last address</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    {{-- address id hidden --}}
                                                    <input type="hidden" name="address_id" id="address_id_hid">
                                                    <div class="row form-row">
                                                        <div class="col-md-3 text-right">
                                                            <strong>Line 1 :</strong>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input type="text" class="form-control form-control-sm" name="order_address[address][address_line_1]" id="ord_address_line_1" required>
                                                            <div class="invalid-feedback ord_address_line_1_er">
                                                                Please Enter Address Line 1 .
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row form-row  my-2">
                                                        <div class="col-md-3 text-right">
                                                            <strong>Line 2 :</strong>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input type="text" class="form-control form-control-sm" name="order_address[address][address_line_2]" id="ord_address_line_2" required>
                                                            <div class="invalid-feedback ord_address_line_2_er">
                                                                Please Enter Address Line 2 .
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row form-row ">
                                                        <div class="col-md-3 text-right">
                                                            <strong>Landmark :</strong>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input type="text" class="form-control form-control-sm" name="order_address[address][address_landmark]" id="ord_address_landmark" required>
                                                            <div class="invalid-feedback ord_address_landmark_er">
                                                                Please Enter Landmark.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row form-row mt-2">
                                                        <div class="col-md-3 text-right">
                                                            <strong>Location :</strong>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input type="text" class="form-control form-control-sm" name="order_address[address][address_location]" id="ord_address_location" required>
                                                            <div class="invalid-feedback ord_address_location_er">
                                                                Please Enter Location.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row form-row  my-2">
                                                        <div class="col-md-3 text-right">
                                                            <strong>Area :</strong>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input type="text" class="form-control form-control-sm" name="order_address[address][address_area]" id="ord_address_area" required>
                                                            <div class="invalid-feedback ord_address_area_er">
                                                                Please Enter Area.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row form-row ">
                                                        <div class="col-md-3 text-right">
                                                            <strong>City :</strong>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input type="text" class="form-control form-control-sm" name="order_address[address][address_city]" id="ord_address_city" required>
                                                            <div class="invalid-feedback ord_address_city_er">
                                                                Please Enter City.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row form-row  my-2">
                                                        <div class="col-md-3 text-right">
                                                            <strong>Pin Code :</strong>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input type="number" class="form-control form-control-sm" name="order_address[address][address_pincode]" id="ord_address_pincode"
                                                                maxlength="6" required>
                                                            <div class="invalid-feedback ord_address_pincode_er">
                                                                Please Enter pincode.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row form-row ">
                                                        <div class="col-md-3 text-right">
                                                            <strong>State :</strong>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <select class="form-control form-control-sm selectpicker border" name="order_address[address][address_state]" id="ord_address_state"
                                                                    data-size="5" data-live-search="true" required>
                                                                @foreach ($states as $key=>$state)
                                                                    <option value="{{$state->name}}" @if($state->name=='Maharashtra')selected @endif>{{$state->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row form-row  my-2">
                                                        <div class="col-md-3 text-right">
                                                            <strong>Country :</strong>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <select class="form-control form-control-sm selectpicker border" name="order_address[address][address_country]" id="ord_address_country" 
                                                                data-size="5" data-live-search="true" required>
                                                                @foreach ($countries as $key=>$country)
                                                                    <option value="{{$country->name}}" @if($country->name=='India')selected @endif>{{$country->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row form-row ">
                                                        <div class="col-md-3 text-right">
                                                            <strong>Email Id :</strong>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input type="email" class="form-control form-control-sm" name="order_address[address][address_email]" id="ord_address_email">
                                                        </div>
                                                    </div>
                                                    <div class="row form-row  my-2">
                                                        <div class="col-md-3 text-right">
                                                            <strong>Mobile No :</strong>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input type="number" class="form-control form-control-sm" name="order_address[address][address_contact]" id="ord_address_contact"
                                                                maxlength="10" required>
                                                        </div>
                                                    </div>
                                                    <div class="row form-row  my-2">
                                                        <div class="col-md-12">
                                                            <center><button type="button" class="btn btn-sm btn-outline-secondary" id="clear_addr">Clear</button></center>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6" id="drop_address_div" style="display: none" >
                                            <div class="card">
                                                <div class="card-header">
                                                    <div class="row form-row ">
                                                        <div class="col-md-6">
                                                            <Strong>Drop Addrress</Strong>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    {{-- drop addres id hidden --}}
                                                    <input type="hidden" name="drop_address_id" id="drop_address_id_hid">

                                                    <div class="row form-row  ">
                                                        <div class="col-md-3 text-right">
                                                            <strong>Line 1 :</strong>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input type="text" class="form-control form-control-sm" name="order_address[drop][address_line_1]" id="drop_address_line_1" required>
                                                            <div class="invalid-feedback drop_address_line_1_er">
                                                                Please Enter Address Line 1 .
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row form-row  my-2">
                                                        <div class="col-md-3 text-right">
                                                            <strong>Line 2 :</strong>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input type="text" class="form-control form-control-sm" name="order_address[drop][address_line_2]" id="drop_address_line_2" required>
                                                            <div class="invalid-feedback drop_address_line_2_er">
                                                                Please Enter Address Line 2 .
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row form-row ">
                                                        <div class="col-md-3 text-right">
                                                            <strong>Landmark :</strong>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input type="text" class="form-control form-control-sm" name="order_address[drop][address_landmark]" id="drop_address_landmark" required>
                                                            <div class="invalid-feedback drop_address_landmark_er">
                                                                Please Enter Landmark.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row form-row mt-2">
                                                        <div class="col-md-3 text-right">
                                                            <strong>Location :</strong>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input type="text" class="form-control form-control-sm" name="order_address[drop][address_location]" id="drop_address_location" required>
                                                            <div class="invalid-feedback drop_address_location_er">
                                                                Please Enter Location.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row form-row  my-2">
                                                        <div class="col-md-3 text-right">
                                                            <strong>Area :</strong>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input type="text" class="form-control form-control-sm" name="order_address[drop][address_area]" id="drop_address_area" required>
                                                            <div class="invalid-feedback drop_address_area_er">
                                                                Please Enter area.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row form-row ">
                                                        <div class="col-md-3 text-right">
                                                            <strong>City :</strong>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input type="text" class="form-control form-control-sm" name="order_address[drop][address_city]" id="drop_address_city" required>
                                                            <div class="invalid-feedback drop_address_city_er">
                                                                Please Enter City.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row form-row  my-2">
                                                        <div class="col-md-3 text-right">
                                                            <strong>Pin Code :</strong>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input type="number" class="form-control form-control-sm" name="order_address[drop][address_pincode]" id="drop_address_pincode" 
                                                                maxlength="6" required>
                                                            <div class="invalid-feedback drop_address_pincode_er">
                                                                Please Enter Pincode.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row form-row ">
                                                        <div class="col-md-3 text-right">
                                                            <strong>State :</strong>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <select class="form-control form-control-sm selectpicker border" name="order_address[drop][address_state]" id="drop_address_state"
                                                                    data-size="5" data-live-search="true" required>
                                                                @foreach ($states as $key=>$state)
                                                                    <option value="{{$state->name}}" @if($state->name=='Maharashtra')selected @endif>{{$state->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row form-row  my-2">
                                                        <div class="col-md-3 text-right">
                                                            <strong>Country :</strong>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <select class="form-control form-control-sm selectpicker border" name="order_address[drop][address_country]" id="drop_address_country" 
                                                                data-size="5" data-live-search="true" required>
                                                                @foreach ($countries as $key=>$country)
                                                                    <option value="{{$country->name}}" @if($country->name=='India')selected @endif>{{$country->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row form-row ">
                                                        <div class="col-md-3 text-right">
                                                            <strong>Email Id :</strong>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input type="email" class="form-control form-control-sm" name="order_address[drop][address_email]" id="drop_address_email">
                                                        </div>
                                                    </div>
                                                    <div class="row form-row  my-2">
                                                        <div class="col-md-3 text-right">
                                                            <strong>Mobile No :</strong>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input type="number" class="form-control form-control-sm" name="order_address[drop][address_contact]" id="drop_address_contact"
                                                                maxlength="10" required>
                                                            <div class="invalid-feedback drop_address_contact_er">
                                                                Please Enter Contact no.
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="row form-row mt-3">
                                                <div class="col-md-4 text-right">
                                                    <strong>Order Date :</strong>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="date" class="form-control form-control-sm" name="order_date" id="order_date" value="{{date('Y-m-d')}}" required>
                                                </div>
                                            </div>
                                            <div class="row form-row my-2">
                                                <div class="col-md-4 text-right">
                                                    <strong>Charges :</strong>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="number" class="form-control form-control-sm" name="order_cost" id="order_cost" required>
                                                    <div class="invalid-feedback order_cost_er">
                                                        Please enter cost.
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row form-row">
                                                <div class="col-md-4 text-right">
                                                    <strong>Assign Order:</strong>
                                                </div>
                                                <div class="col-md-8">
                                                    <select class="form-control form-control-sm selectpicker border" name="assign_order" id="assign_order"
                                                        data-size="5" data-live-search="true" title="Select Delboy" required>
                                                        @foreach ($deliveryStaff as $key=>$delboy)
                                                            <option value="{{$delboy->username}}">{{$delboy->username}}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="invalid-feedback assign_order_er">
                                                        Please select delivery stafff.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row form-row my-2">
                                                <div class="col-md-4 text-right">
                                                    <strong>Assign Helpers:</strong>
                                                </div>
                                                <div class="col-md-8">
                                                    <select class="form-control form-control-sm selectpicker border" name="assign_helper[]" id="assign_helper"
                                                        data-size="5" data-live-search="true" title="Select Helpers" multiple>
                                                        <option value="No Helper">No Helper</option>
                                                        @foreach ($deliveryStaff as $key=>$delboy)
                                                            <option value="{{$delboy->username}}">{{$delboy->username}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row my-2 justify-content-center">
                                        <div class="col-md-auto">
                                            <button type="submit" class="btn btn-outline-success" name="submit_order" id="submit_order" value="generate_order">Generate Order</button>
                                            {{-- <button type="submit" class="btn btn-outline-success" name="update_selected_order" id="submit_order" value="update_selected_order">Update Order</button> --}}
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="view_order_modal" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <strong>View Order</strong>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-auto">
                                <strong>Customer Name :</strong>
                            </div>
                            <div class="col-auto">
                                <span id="view_order_customer_name"></span>
                            </div>
                            <div class="col-auto">
                                <strong>Contact No :</strong>
                            </div>
                            <div class="col-auto">
                                <span id="view_order_customer_contact"></span>
                            </div>
                            <div class="col-auto">
                                <strong>Email :</strong>
                            </div>
                            <div class="col-auto">
                                <span id="view_order_customer_email"></span>
                            </div>
                        </div>
                        <div class="row mt-2" >
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-auto">
                                        <strong><span>Address :</span></strong>
                                    </div>
                                    <div class="col-auto">
                                        <span id="view_order_address"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6" id="view_order_drop_address_div" style="display: none">
                                <div class="row">
                                    <div class="col-auto">
                                        <strong><span>Drop Address :</span></strong>
                                    </div>
                                    <div class="col-auto">
                                        <span id="view_order_drop_address"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="border border-0">
                        <ul class="list-group list-group-flush">
                           
                        </ul>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-auto mr-auto">
                                    <div class="row">
                                        <div class="col-auto">
                                            <strong>Order Assign To : </strong><span id="view_order_del_assign_to_span"></span>
                                        </div>
                                        <div class="col-auto">
                                            <strong>Order Helpers : </strong><span id="view_order_helpers_span"></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-auto">
                                    <strong>Total Amt : </strong><span id="view_order_total_amt_span"></span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item"><Strong>Products</Strong></li>
                      </ul>
                    <div class="table table-responsive">
                        <table class="table">
                            <thead>
                                <th>Product name</th>
                                <th>Inventory ID</th>
                                <th>Vendor Name</th>
                            </thead>
                            <tbody id="view_order_table_body"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cancel Order --}}
        <div class="modal fade" id="order_cancel_modal" tabindex="-1" role="dialog" aria-labelledby="order_cancel_modalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{route('order-maintenance-cancel')}}" method="post">
                        @csrf
                        <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Cancel Order - <span id="order_cancel_order_id_span"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        </div>
                        <div class="modal-body container-fluid">
                            <input type="hidden" name="cancel_order_id" id="cancel_order_id">
                            <div class="row">
                                <label for=""><strong>Reason</strong></label>
                                <select class="form-control form-control-sm selectpicker border border-dark order-close-reason" name="cancel_reason" id="cancel_reason" 
                                    title="Select Reason"  required>
                                    @foreach ($orderClosedReason as $key=>$reason)
                                        <option value="{{$key}}" data-reason="{{$reason}}">{{$reason}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row">
                                <label for=""><strong>comment</strong></label>
                                <textarea class="form-control form-control-sm" name="cancel_comment" id="cancel_comment" cols="30" rows="5"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    @endsection
</body>
@section('script')
    <script>
        $('#clear_addr').click(function(){
            $('#ord_address_line_1').val(null);
            $('#ord_address_line_2').val(null);
            $('#ord_address_landmark').val(null);
            $('#ord_address_location').val(null);
            $('#ord_address_area').val(null);
            $('#ord_address_city').val(null);
            $('#ord_address_pincode').val(null);
            $('#ord_address_state').val(null);
            $('#ord_address_country').val(null);
            $('#ord_address_email').val(null);
            $('#ord_address_contact').val(null); 
        });
        $('#create_order').on('click',function(){
            $('#submit_order').val('generate_order');
            $('#order_details_div').hide();
            $('#ord_details_repair_install_div').hide();
            $('#customer_list_div').hide();
            $('#customer_live_products').hide();
            $('#create_order_modal').modal({
                backdrop: 'static',
                keyboard: false
            });
        });

        $('#btn_search_customer').on('click',function(){
            let search_val = $('#txt_search_customer').val();
            if(search_val!=null && search_val!=''){
                var dataString = ({_token:"{{ csrf_token() }}",search_customer:""+search_val});
                $.ajax({
                    type: "POST",
                    url: "{{route('order-maintenance-customers')}}",
                    data: dataString,
                    success: function (data) {
                        console.log(data);
                        let customerList = data;
                        $('#customer_table_body').empty();
                        let row="";
                        Object.keys(customerList).forEach(function(key){
                            // let customerAddress = customerList[key].address_line_1+','+customerList[key].address_line_2+','+customerList[key].area+','+customerList[key].landmark+','+customerList[key].location+','+customerList[key].city;
                            // row += "<tr id='trParent'>";
                            //     row+="<td class='text-nowrap' data-label='Customer name'>"+customerList[key].customer_name+"</td>";
                            //     row+="<td class='text-nowrap' data-label='Contact No'>"+customerList[key].primary_contact_no+"</td>";
                            //     row+="<td class='' data-label='Address'>"+customerAddress+"</td>";
                            //     row+="<td class='text-nowrap' data-label='Action'><button type='button' class='btn btn-outline-primary btn-sm' onclick ='selectCustomer("+customerList[key].cust_id+")'>Select</button></td>";
                            // row+="</tr>";
                            let customerAddress = customerList[key][0].fulldetails;
                            row += "<tr id='trParent'>";
                                row+="<td class='text-nowrap' data-label='Customer name'>"+customerList[key][0].DelDate+"</td>";
                                row+="<td class='text-nowrap' data-label='Customer name'>"+customerList[key][0].shipping_first_name+"</td>";
                                row+="<td class='text-nowrap' data-label='Customer name'>"+customerList[key][0].patient_name+"</td>";
                                row+="<td class='text-nowrap' data-label='Contact No'>"+customerList[key][0].mobileno+"</td>";
                                row+="<td class='' data-label='Address'>"+customerAddress+"</td>";
                                row+="<td class='text-nowrap' data-label='Action'><button type='button' class='btn btn-outline-primary btn-sm' onclick ='selectCustomer("+key+")'>Select</button></td>";
                            row+="</tr>";
                        });
                        //$('#customer_table_body').append(row);
                        $('#customer_list_table').DataTable().destroy();
                        $('#customer_list_table').find('tbody').empty();
                        $('#customer_list_table').find('tbody').append(row);
                        $('#customer_list_table').DataTable().draw();

                        $('#customer_list_div').show();
                        $('#customer_live_products').hide();
                        $('#order_details_div').hide();
                    }
                });
            }else{
                alert('please enter customer name or number');
            }
        });

        let customerData = null;

        function selectCustomer(lead_id){
            var dataString = ({_token:"{{ csrf_token() }}",lead_id:""+lead_id});
            $.ajax({
                type: "GET",
                url: "{{route('order-customer-live-products')}}",
                data: dataString,
                success: function (data) {
                    console.log(data);
                    $('#customer_list_div').hide();
                    $('#customer_live_products').show();
                    let customerDetails = data[0];
                    customerData = data[0];
                    //customer details show

                    $('#customer_name').text(customerDetails.shipping_first_name);
                    $('#customer_contact').text(customerDetails.mobileno);

                    let customerAddress = customerDetails.fulldetails;
                    $('#customer_address').text(customerAddress);
                    
                    let productsData = data;

                    $('#customer_live_products_tbody').empty();
                    let row="";
                    if(productsData.length!=0){
                        Object.keys(productsData).forEach(function(key){
                        row += "<tr id='trParent"+key+"'>";
                            row+="<td class='text-nowrap' data-label='Action'>";
                                row+="<div class='custom-control custom-checkbox'>";
                                    row+="<input type='checkbox' class='custom-control-input selected-products' name='selected_product[]' id='product_check"+key+"' value="+productsData[key].ord_dt_id+" onClick='selectProduct();'>";
                                    row+="<label class='custom-control-label' for='product_check"+key+"'>Select</label>";
                                row+="</div>";
                            row+="</td>";
                                
                            row+="<td class='text-nowrap' data-label='Customer name'>"+productsData[key].product_name+"</td>";
                            row+="<td class='text-nowrap' data-label='Customer name'>"+productsData[key].unique_id+"</td>";
                            row+="<td class='text-nowrap' data-label='Contact No'>"+productsData[key].registered_name+"</td>";
                        row+="</tr>";
                    });
                    }else{
                        row += "<tr>";
                            row += "<td colspan='*'><center>No Data Found</td></center>";
                        row += "</tr>";
                    }   

                    // $('#ord_address_line_1').val(customerData.address_line_1);
                    // $('#ord_address_line_2').val(customerData.address_line_2);
                    // $('#ord_address_landmark').val(customerData.landmark);
                    // $('#ord_address_location').val(customerData.location);
                    // $('#ord_address_area').val(customerData.area);
                    // $('#ord_address_city').val(customerData.city);
                    // $('#ord_address_pincode').val(customerData.pincode);
                    // $('#ord_address_state').val(customerData.state);
                    // $('#ord_address_country').val(customerData.country);
                    // $('#ord_address_email').val(customerData.email_id);
                    // $('#ord_address_contact').val(customerData.primary_contact_no);
                    
                    $('#customer_live_products_tbody').append(row);
                    $('#customer_details_div').show();

                    //assign hidden
                    $('#customer_id_hid').val(customerDetails.customer_id);
                    $('#del_order_id_hid').val(productsData[0].order_id);
                }
            });
        }

        let productsArr = null;

        $('#order_type').on('change',function(){
            var checked = $(".selected-products:checked").length;
            
            validateData($(this).val());
            if(checked>0){
                var arr = [];
                var maintenance_ids = [];
                $.each($(".selected-products:checked"), function(){
                  arr.push($(this).val());
                  let key = $(this).data('key');
                    console.log($(this).data('key'));
                    maintenance_ids.push($('#maintenance_id'+key).val());
                });
                console.log(maintenance_ids);
                let orderType = $(this).val();

                producatsArr = arr;
                if(orderType=='Repair' || orderType=='Install' || orderType=='Replace'){
                    $('#order_details_div').show();
                    $('#ord_details_repair_install_div').show();
                    $('#drop_address_div').hide();
                }else if(orderType=='Shifting'){
                    $('#order_details_div').show();
                    $('#ord_details_repair_install_div').show();
                    $('#drop_address_div').show();
                }else{
                    $('#order_details_div').hide();
                }
                //hidden assign
                $('#products_hid').val(JSON.stringify(arr));
                $('#order_type_hid').val(orderType);
                $('#maintenance_ids_hid').val(JSON.stringify(maintenance_ids))

                //order details field null nulll
                // $('#ord_address_line_1').val(null);
                // $('#ord_address_line_2').val(null);
                // $('#ord_address_landmark').val(null);
                // $('#ord_address_location').val(null);
                // $('#ord_address_area').val(null);
                // $('#ord_address_city').val(null);
                // $('#ord_address_pincode').val(null);
                // $('#ord_address_state').val(null);
                // $('#ord_address_country').val(null);
                // $('#ord_address_email').val(null);
                // $('#ord_address_contact').val(null);
                $('#ord_address_line_1').val(customerData.address_line_1);
                $('#ord_address_line_2').val(customerData.address_line_2);
                $('#ord_address_landmark').val(customerData.landmark);
                $('#ord_address_location').val(customerData.location);
                $('#ord_address_area').val(customerData.area);
                $('#ord_address_city').val(customerData.city);
                $('#ord_address_pincode').val(customerData.pincode);
                $('#ord_address_state').val(customerData.state);
                $('#ord_address_country').val(customerData.country);
                $('#ord_address_email').val(customerData.email_id);
                $('#ord_address_contact').val(customerData.primary_contact_no);

                $('#customer_address_check').prop('checked',false);
                $('#order_cost').val(null);
                $('#assign_order').val(null);
                $('#assign_order').selectpicker('val',null);
                $('#assign_helper').selectpicker('val',null);
                //another fields set null

            }else{
                $(this).val(null);
                alert('please select product');
            }
        });

        function selectProduct(){
            $('#order_details_div').hide();
            $('#order_type').selectpicker('val',null);
        };
       
        $('#customer_address_check').on('click',function(){
            
            if($(this).is(":checked")){
                $('#ord_address_line_1').val(customerData.address_line_1);
                $('#ord_address_line_2').val(customerData.address_line_2);
                $('#ord_address_landmark').val(customerData.landmark);
                $('#ord_address_location').val(customerData.location);
                $('#ord_address_area').val(customerData.area);
                $('#ord_address_city').val(customerData.city);
                $('#ord_address_pincode').val(customerData.pincode);
                $('#ord_address_state').val(customerData.state);
                $('#ord_address_country').val(customerData.country);
                $('#ord_address_email').val(customerData.email_id);
                $('#ord_address_contact').val(customerData.primary_contact_no);
            }else{
                $('#ord_address_line_1').val(null);
                $('#ord_address_line_2').val(null);
                $('#ord_address_landmark').val(null);
                $('#ord_address_location').val(null);
                $('#ord_address_area').val(null);
                $('#ord_address_city').val(null);
                $('#ord_address_pincode').val(null);
                $('#ord_address_state').val(null);
                $('#ord_address_country').val(null);
                $('#ord_address_email').val(null);
                $('#ord_address_contact').val(null);
            }
        });

        //close cards 
        $('.close').on('click',function(){
            let val = $(this).val();
            if(val=='order_details_div'){
                $('#order_details_div').hide();
            }else if(val=='customer_live_products'){
                $('#customer_live_products').hide();
                $('#order_details_div').hide();
                $('#customer_list_div').show();
            }else if(val=='customer_list_div'){
                $('#customer_list_div').hide();
                $('#customer_live_products').hide();
                $('#order_details_div').hide();
            }
        });


        $('.view-order').on('click',function(){
            let orderId = $(this).data('order_id');
            //console.log("{{url('/')}}/order-maintenance-view/"+orderId);
            $.ajax({
                type: "GET",
                url: "{{url('/')}}/order-maintenance-view/"+orderId,
                //data: dataString,
                success: function (data) {
                    console.log(data);
                    let productsData = data.orderData;
                    let customerDetails = data.customerDetails;
                    
                    //customer details
                    $('#view_order_customer_name').text(customerDetails.customer_name);
                    $('#view_order_customer_contact').text(customerDetails.primary_contact_no);
                    $('#view_order_customer_email').text(customerDetails.email_id);
                    
                    //$('#view_order_address').text(orderAddress);
                    if(productsData[0].deliverypickup=="Shifting"){
                        let pickupAddress = customerDetails.order_address.pickup[0].address_line_1+", "+customerDetails.order_address.pickup[0].address_line_2+", "+customerDetails.order_address.pickup[0].area+", "+customerDetails.order_address.pickup[0].landmark+", "+customerDetails.order_address.pickup[0].city+", "+customerDetails.order_address.pickup[0].state+", "+customerDetails.order_address.pickup[0].country+", "+customerDetails.order_address.pickup[0].pincode;
                        $('#view_order_address').text(pickupAddress);
                        let dropAddress = customerDetails.order_address.drop[0].address_line_1+", "+customerDetails.order_address.drop[0].address_line_2+", "+customerDetails.order_address.drop[0].area+", "+customerDetails.order_address.drop[0].landmark+", "+customerDetails.order_address.drop[0].city+", "+customerDetails.order_address.drop[0].state+", "+customerDetails.order_address.drop[0].country+", "+customerDetails.order_address.drop[0].pincode;
                        $('#view_order_drop_address').text(dropAddress);
                        $('#view_order_drop_address_div').show();
                    }else{
                        let pickupAddress = customerDetails.order_address.address[0].address_line_1+", "+customerDetails.order_address.address[0].address_line_2+", "+customerDetails.order_address.address[0].area+", "+customerDetails.order_address.address[0].landmark+", "+customerDetails.order_address.address[0].city+", "+customerDetails.order_address.address[0].state+", "+customerDetails.order_address.address[0].country+", "+customerDetails.order_address.address[0].pincode;
                        $('#view_order_address').text(pickupAddress);
                        $('#view_order_drop_address_div').hide();
                    }

                    $('#view_order_del_assign_to_span').text(productsData[0].DelAssignedTo);
                    let helpers = productsData[0].helpers;
                    if(helpers=='[No helper]' || helpers=='null' || helpers.length==0){
                        helpers = 'No Helper';
                    }else{  
                        helpers = JSON.parse(productsData[0].helpers).join(', ');
                        
                    }
                    console.log(helpers);
                    $('#view_order_helpers_span').text(helpers);
                    $('#view_order_total_amt_span').text(productsData[0].TotalAmt);

                    $('#view_order_table_body').empty();
                    let row="";
                    Object.keys(productsData).forEach(function(key){
                        row += "<tr id='trParent"+key+"'>";
                            row+="<td class='text-nowrap' data-label='Customer name'>"+productsData[key].product_name+"</td>";
                            row+="<td class='text-nowrap' data-label='Inventory Id'>"+productsData[key].unique_id+"</td>";
                            row+="<td class='text-nowrap' data-label='Contact No'>"+productsData[key].vendor_name+"</td>";
                        row+="</tr>";
                    });
                    $('#view_order_table_body').append(row);
                    $('#view_order_modal').modal('show');
                }
            });
            
        });

        $('.update-order').on('click',function(){
            
            let orderId = $(this).data('order_id');
            
            $.ajax({
                type: "GET",
                url: "{{url('/')}}/order-maintenance-update/"+orderId,
                //data: dataString,
                success: function (data) {
                    //console.log(data);
                    $('#create_order_modal').modal({
                        backdrop: 'static',
                        keyboard: false
                    });

                    let productsData = data.orderData;
                    let customerDetails = data.customerDetails;
                    customerData = customerDetails;
                    let orderType = productsData[0].deliverypickup;
                    console.log(productsData);

                    $('#customer_live_products').show();

                    //set  customer details
                    $('#customer_name').text(customerDetails.customer_name);
                    $('#customer_contact').text(customerDetails.primary_contact_no);
                    let customerAddress = customerDetails.address_line_1+", "+customerDetails.address_line_2+", "+customerDetails.area+", "+customerDetails.landmark+", "+customerDetails.location+", "+customerDetails.city+", "+customerDetails.state+", "+customerDetails.country+", "+customerDetails.pincode;
                    $('#customer_address').text(customerAddress);

                    //let productsData = data.orderData;

                    $('#customer_live_products_tbody').empty();
                    let row="";
                    if(productsData.length!=0){
                        Object.keys(productsData).forEach(function(key){
                        row += "<tr id='trParent"+key+"'>";
                            row+="<td class='text-nowrap' data-label='Action'>";
                                row+="<div class='custom-control custom-checkbox'>";
                                    row+="<input type='checkbox' class='custom-control-input selected-products' name='selected_product[]' id='product_check"+key+"' data-key="+key+" value="+productsData[key].order_details_id+" onClick='selectProduct();' checked>";
                                    row+="<input type='hidden' name='maintenance_id[]' id='maintenance_id"+key+"' value="+productsData[key].id+">"; 
                                    row+="<label class='custom-control-label' for='product_check"+key+"'>Select</label>";
                                row+="</div>";
                            row+="</td>";
                            row+="<td class='text-nowrap' data-label='Customer name'>"+productsData[key].product_name+"</td>";
                            row+="<td class='text-nowrap' data-label='Customer name'>"+productsData[key].unique_id+"</td>";
                            row+="<td class='text-nowrap' data-label='Contact No'>"+productsData[key].vendor_name+"</td>";
                        row+="</tr>";
                    });
                    }else{
                        row += "<tr>";
                            row += "<td colspan='*'><center>No Data Found</td></center>";
                        row += "</tr>";
                    }   
                    $('#customer_live_products_tbody').append(row);

                    //set order type
                    $('#order_type').selectpicker('val',orderType);
                    
                    //set addresses order address and drop address
                    if(orderType!="Shifting"){
                        $('#address_id_hid').val(customerDetails.order_address.address[0].id);

                        $('#ord_address_line_1').val(customerDetails.order_address.address[0].address_line_1);
                        $('#ord_address_line_2').val(customerDetails.order_address.address[0].address_line_2);
                        $('#ord_address_area').val(customerDetails.order_address.address[0].area);
                        $('#ord_address_landmark').val(customerDetails.order_address.address[0].landmark);
                        $('#ord_address_location').val(customerDetails.order_address.address[0].location);
                        $('#ord_address_city').val(customerDetails.order_address.address[0].city);
                        $('#ord_address_pincode').val(customerDetails.order_address.address[0].pincode);
                        $('#ord_address_email').val(customerDetails.order_address.address[0].email);
                        $('#ord_address_contact').val(customerDetails.order_address.address[0].contact_no);
                    }else{
                        //pickup
                        $('#address_id_hid').val(customerDetails.order_address.pickup[0].id);

                        $('#ord_address_line_1').val(customerDetails.order_address.pickup[0].address_line_1);
                        $('#ord_address_line_2').val(customerDetails.order_address.pickup[0].address_line_2);
                        $('#ord_address_area').val(customerDetails.order_address.pickup[0].area);
                        $('#ord_address_landmark').val(customerDetails.order_address.pickup[0].landmark);
                        $('#ord_address_location').val(customerDetails.order_address.pickup[0].location);
                        $('#ord_address_city').val(customerDetails.order_address.pickup[0].city);
                        $('#ord_address_pincode').val(customerDetails.order_address.pickup[0].pincode);
                        $('#ord_address_email').val(customerDetails.order_address.pickup[0].email);
                        $('#ord_address_contact').val(customerDetails.order_address.pickup[0].contact_no);
                        //drop
                        $('#drop_address_id_hid').val(customerDetails.order_address.drop[0].id);
                        
                        $('#drop_address_line_1').val(customerDetails.order_address.drop[0].address_line_1);
                        $('#drop_address_line_2').val(customerDetails.order_address.drop[0].address_line_2);
                        $('#drop_address_area').val(customerDetails.order_address.drop[0].area);
                        $('#drop_address_landmark').val(customerDetails.order_address.drop[0].landmark);
                        $('#drop_address_location').val(customerDetails.order_address.drop[0].location);
                        $('#drop_address_city').val(customerDetails.order_address.drop[0].city);
                        $('#drop_address_pincode').val(customerDetails.order_address.drop[0].pincode);
                        $('#drop_address_email').val(customerDetails.order_address.drop[0].email);
                        $('#drop_address_contact').val(customerDetails.order_address.drop[0].contact_no);
                    }

                    //set date cost assign del and helper
                    let delDate = productsData[0].DelDate;
                    let dateArr = delDate.split('-');
                    let dateConverted = dateArr[2]+"-"+dateArr[1]+"-"+dateArr[0];
                    $('#order_date').val(dateConverted);

                    let orderCost = productsData[0].TotalAmt;
                    $('#order_cost').val(orderCost);
                    
                    let assignOrder = productsData[0].DelAssignedTo;
                    $('#assign_order').selectpicker('val',assignOrder);

                    let assignHelper = productsData[0].helpers;
                    $('#assign_helper').selectpicker('val',JSON.parse(assignHelper));
                    $('#assign_helper').selectpicker('refresh');

                    //set hidden values
                    $('#customer_id_hid').val(customerDetails.cust_id);
                    $('#order_type_hid').val(orderType);
                    console.log(productsData[0].order_id);
                    $('#del_order_id_hid').val(productsData[0].order_id);

                    var arr = [];
                    var maintenance_ids = [];
                    $.each($(".selected-products:checked"), function(){
                        arr.push($(this).val());
                        let key = $(this).data('key');
                        console.log($(this).data('key'));
                        maintenance_ids.push($('#maintenance_id'+key).val());
                    });
                    //producatsArr = arr;
                    console.log(arr);
                    console.log(maintenance_ids);
                    $('#products_hid').val(JSON.stringify(arr));
                    $('#maintenance_ids_hid').val(JSON.stringify(maintenance_ids))

                    //set btn submit to update
                    $('#submit_order').val('update_order')

                    $('#order_details_div').show();
                    $('#ord_details_repair_install_div').show();
                    if(orderType=='Repair' || orderType=='Install'){
                        $('#drop_address_div').hide();
                    }else if(orderType=='Shifting'){
                        $('#drop_address_div').show();
                    }


                }
            });
        });

        $('.cancel-order').on('click',function(){
            orderId = $(this).data('order_id');
            $('#order_cancel_order_id_span').text(orderId);
            $('#cancel_order_id').val(orderId);
            $('#order_cancel_modal').modal('show');
        });
        $('#cancel_reason').on('change',function(){
            let value = $(this).find(':selected').attr('data-reason');
            if(value=='Others'){
                $('#cancel_comment').prop('required',true);
            }else{
                $('#cancel_comment').prop('required',false);
            }
        })


        function validateData(orderType){
            if(orderType=='Shifting'){
                $('#drop_address_line_1').prop('required',true)
                $('#drop_address_lina').prop('required',true)
                $('#drop_address_landmark').prop('required',true)
                $('#drop_address_location').prop('required',true)
                $('#drop_address_area').prop('required',true)
                $('#drop_address_city').prop('required',true)
                $('#drop_address_pincode').prop('required',true)
                $('#drop_address_state').prop('required',true)
                $('#drop_address_country').prop('required',true)
                $('#drop_address_email').prop('required',true)
                $('#drop_address_contact').prop('required',true)
            }else{
                $('#drop_address_line_1').prop('required',false)
                $('#drop_address_line_2').prop('required',false)
                $('#drop_address_landmark').prop('required',false)
                $('#drop_address_location').prop('required',false)
                $('#drop_address_area').prop('required',false)
                $('#drop_address_city').prop('required',false)
                $('#drop_address_pincode').prop('required',false)
                $('#drop_address_state').prop('required',false)
                $('#drop_address_country').prop('required',false)
                $('#drop_address_email').prop('required',false)
                $('#drop_address_contact').prop('required',false)
            }
        }

        function formatDate(date) {
            var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;

            return [year, month, day].join('-');
        }

        $('#assign_order').on('change',function(){
            let val = $(this).val();
            $('#assign_helper option').attr("disabled",false);
            $('#assign_helper option[value='+val+']').prop("selected",false);
            $('#assign_helper option[value='+val+']').attr("disabled",true);
            $('#assign_helper').selectpicker('refresh');
        });

        $('#search_from_date').on('change',function(){
            let dateVal = $(this).val();
            if(dateVal){
                $('#search_to_date').prop('required',true);
            }
            else{
                $('#search_to_date').prop('required',false);
            }
            
        })
        $('#search_to_date').on('change',function(){
            let dateVal = $(this).val();
            if(dateVal){
                $('#search_from_date').prop('required',true);
            }else{
                $('#search_from_date').prop('required',false);
            }
        })
    </script>
@endsection
</html>