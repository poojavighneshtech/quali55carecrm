@extends('header_and_sidebar')
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
    
        <title>Customer View</title>
        {{-- <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script> --}}
        @section('styles')
            
        @endsection
    </head>
<body id="page-top">	
		<!-- Page Wrapper -->
    @section('content')
        <div class="leads">
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
            <div class="card">
                <div class="card-header text-center">
                    Customer View
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="{{url('/')}}/get_customers" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" placeholder="Search Customer Name / Contact No / Patient Name / Order id.." name="customer_value" id="txt_customer_value" value="@if(isset($customer_value)){{$customer_value}}@endif" list="datalist_customers" required>
                                        <datalist id="datalist_customers"></datalist>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="submit" class="btn btn-success"value="Submit">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <br>
                    {{-- @if(isset($get_customers_by_orders))
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <table class="table table-hover">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Sr.No</th>
                                                    <th>Customer Name</th>
                                                    <th>Contact No</th>
                                                    <th>Location</th>
                                                    <th>Address</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($get_customers_by_orders as $key=>$customer)
                                                    <tr>
                                                        <th>{{$loop->index+1}}</th>
                                                        <td>{{$customer->customer_name}}</td>
                                                        <td>{{$customer->primary_contact_no}}</td>
                                                        <td>{{$customer->location}}</td>
                                                        <td>{{$customer->address_line_1}},{{$customer->address_line_2}},{{$customer->area}},{{$customer->landmark}},{{$customer->city}}-{{$customer->pincode}}</td>
                                                        <td><a href="{{url('/')}}/customer_leads/{{$customer->cust_id}}" class="btn btn-outline-primary">view</a></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        
                                        {{ $get_customers_by_orders->links('Custom.Pagination.pagination') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif --}}
                    
                    @if(isset($get_customers))
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table table-responsive">
                                            <table class="table table-hover" id="records">
                                                <thead class="thead-light">
                                                    <tr>
                                                        {{-- <th>Sr.No</th> --}}
                                                        <th>Customer Name</th>
                                                        <th>Patient Name</th>
                                                        <th>Contact No</th>
                                                        <th>Location</th>
                                                        <th>Address</th>
                                                        <th>Orders</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {{-- @foreach($get_customers as $key=>$customer)
                                                        <tr>
                                                            <th>{{$key+1}}</th>
                                                            <td>{{$customer['customer_name']}}</td>
                                                            <td>{{$customer['patient_name']}}</td>
                                                            <td>{{$customer['primary_contact_no']}}</td>
                                                            <td>{{$customer['location']}}</td>
                                                            <td>{{$customer['address_line_1']}},{{$customer['address_line_2']}},{{$customer['area']}},{{$customer['landmark']}},{{$customer['city']}}-{{$customer['pincode']}}</td>
                                                            <td class="text-nowrap"><a href="{{url('/')}}/customer_leads/{{$customer['cust_id']}}" class="btn btn-outline-primary">view</a>
                                                            <a href="{{url('/')}}/transaction_history?cust_id={{$customer['cust_id']}}" class="btn btn-sm btn-outline-secondary">Transaction</a>
                                                        </td>
                                                        </tr>
                                                    @endforeach --}}
                                                    @foreach($get_customers as $custidkey=>$customer)
                                                        <tr>
                                                            {{-- <th>{{$key+1}}</th> --}}
                                                            <td>{{$customer[0]->customer_name}}</td>
                                                            <td>{{$customer[0]->patient_name}}</td>
                                                            <td>{{$customer[0]->primary_contact_no}}</td>
                                                            <td>{{$customer[0]->location}}</td>
                                                            <td>{{$customer[0]->address_line_1}},{{$customer[0]->address_line_2}},{{$customer[0]->area}},{{$customer[0]->landmark}},{{$customer[0]->city}}-{{$customer[0]->pincode}}</td>
                                                            <td>{{$customer->pluck('order_id')->join(', ')}}</td>
                                                            <td class="text-nowrap">
                                                                <a href="{{url('/')}}/customer_leads/{{$custidkey}}" class="btn btn-sm btn-outline-primary">view</a>
                                                                <a href="{{url('/')}}/transaction_history?cust_id={{$custidkey}}" class="btn btn-sm btn-outline-secondary">Transaction</a>
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
                    @endif
                    @if(isset($get_customer))
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <strong>Customer Details</strong>
                                            </div>
                                            <div class="col-md-8 text-right">
                                                {{-- <button type="button" class="btn btn-outline-primary btn-sm btn-rounded" id="btn_all_leads">Raise Complaint</button> --}}
                                                <a href="{{url('/')}}/complaint_customer_view/{{$get_customer[0]['cust_id']}}" class="btn btn-outline-primary btn-sm">Raise Complaint</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <strong>Name :</strong>
                                                    </div>
                                                    <div class="col-md-9">
                                                        {{$get_customer[0]['customer_name']}}
                                                        <input type="hidden" name="hidden_customer_id" id="hidden_customer_id" value="{{$get_customer[0]['cust_id']}}">
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <strong>Contact :</strong>
                                                    </div>
                                                    <div class="col-md-9">
                                                        {{$get_customer[0]['primary_contact_no']}}
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <strong>Email :</strong>
                                                    </div>
                                                    <div class="col-md-9">
                                                        {{$get_customer[0]['email_id']}}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-7">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <strong>Address :</strong>
                                                    </div>
                                                    <div class="col-md-9">
                                                        {{$get_customer[0]['address_line_1']}},{{$get_customer[0]['address_line_2']}},{{$get_customer[0]['area']}},{{$get_customer[0]['landmark']}},{{$get_customer[0]['city']}}-{{$get_customer[0]['pincode']}},
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <strong>Location :</strong>
                                                    </div>
                                                    <div class="col-md-9">
                                                        {{$get_customer[0]['location']}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <br>
                    @if(isset($lead_details))
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>Lead Details</strong>
                                            </div>
                                            <div class="col-md-9 text-right">
                                                {{-- <button type="button" class="btn btn-outline-primary btn-sm btn-rounded" id="btn_all_leads">All</button> --}}
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-responsive lead_table" id="records" width="100%">
                                            <thead>
                                                <tr class="thead-light">
                                                    <th>Sr No</th>
                                                    <th>Date&emsp;&emsp;&emsp;</th>
                                                    <th>Lead ID</th>
                                                    {{-- <th>No</th> --}}
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                    <th>Product Name &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</th>
                                                    <th>Quantity</th>
                                                    <th>Rental/Sale</th>
                                                    <th>Vendor Name</th>
                                                    <th>Rent</th>
                                                    <th>Deposit Taken</th>
                                                    <th>Deposit Return</th>
                                                    <th>Deposit Outstanding</th>
                                                    <th>Transport</th>
                                                    <th>Paid</th>
                                                    <th>Total</th>
                                                    <th>No Of Months</th>
                                                    <th>Total Rent</th>
                                                    <th>Lead Status</th>
                                                    <th>Lead Owner</th>
                                                    <th>Lead Source</th>
                                                    <th>Comment</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $srno=1;
                                                    $no = 2;
                                                @endphp
                                                @foreach($lead_details as $key=>$lead)
                                                    <tr>
                                                        <td>{{$srno}}</td>
                                                        <td>{{date('d-m-Y',strtotime($lead['creation_date']))}}</td>
                                                        <td>{{$lead['id']}}</td>
                                                        {{-- <td>1</td> --}}
                                                        <td class="text-center">
                                                            @if($lead['product_details'][0]['current_status']=='Pending' && $lead['product_details'][0]['sale_rental']=='Sale')
                                                                <span class="badge badge-secondary">Sold</span>
                                                            @elseif(($lead['product_details'][0]['current_status']==='Pending Pickup' || ($lead['product_details'][0]['current_status']==='Picked up' || $lead['product_details'][0]['current_status']==='Picked UP' || $lead['product_details'][0]['current_status']==='Picked Up')) && $lead['product_details'][0]['sale_rental']==='Rental')
                                                                <span class="badge badge-danger">Picked Up</span>
                                                            @elseif(($lead['product_details'][0]['current_status']=='Pending' || $lead['product_details'][0]['current_status']=='Renewed')  && $lead['product_details'][0]['sale_rental']=='Rental')
                                                                <span class="badge badge-success">Live</span>
                                                            @elseif(($lead['product_details'][0]['current_status'] == 'Cancel'))
                                                                <span class="badge badge-warning">Cancel</span>
                                                            @elseif(($lead['product_details'][0]['current_status'] == 'CustStop'))
                                                                <span class="badge badge-primary">Stop</span>
                                                            @else
                                                                <span class="badge badge-primary">Undefined</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="dropdown">
                                                                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="actionDropDown{{$key}}1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                    Action
                                                                </button>
                                                                <div class="dropdown-menu" aria-labelledby="actionDropDown{{$key}}1">
                                                                    <button class="dropdown-item" data-toggle="modal" data-target="#history{{$key}}1" id="{{$key}}1" name="history" onclick="btnReportClick(this.name,this.id);">View</button>
                                                                    @if(($lead['product_details'][0]['current_status']=='Pending' || $lead['product_details'][0]['current_status']=='Renewed')  && $lead['product_details'][0]['sale_rental']=='Rental')
                                                                        <a class="dropdown-item" href="{{route('order-request',['submit'=>'renew','checked_product'=>array($lead['product_details'][0]['order_details_id'])])}}">Renew</a>
                                                                        <a class="dropdown-item" href="{{route('order-request',['submit'=>'pickup','checked_product'=>array($lead['product_details'][0]['order_details_id'])])}}">Pickup</a>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            {{-- <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#history{{$key}}1" id="{{$key}}1" name="history" onclick="btnReportClick(this.name,this.id);"><i class="fa fa-history" aria-hidden="true"></i></button> --}}
                                                            <div class="modal fade bd-example-modal-xl" id="history{{$key}}1" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                                                <div class="modal-dialog modal-xl">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <div class="col-md-12">
                                                                                <div class="row">
                                                                                    <div class="col-md-4">
                                                                                        <h4>Report</h4>
                                                                                    </div>
                                                                                    <div class="col-md-8">
                                                                                        <div class="text-right">
                                                                                            <select class="selectpicker" name="select" id="{{$key}}1" onchange="btnSelect(this.value,this.id)">
                                                                                                <option value="all" selected>All</option>
                                                                                                <option value="delivery_report">Delivery Report</option>
                                                                                                <option value="renewal_report">Renewal Report</option>
                                                                                                <option value="pickup_report">Pickup Report</option>
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <div class="card border-primary" id="div_content_delivery_card{{$key}}1" style="display: none">
                                                                                <div class="card-header text-primary border-primary">
                                                                                    <strong>Delivery Report</strong>
                                                                                </div>
                                                                                <div class="card-body" id="div_content_delivery_modal{{$key}}1"></div>
                                                                            </div>
                                                                            <br>
                                                                            <div class="card border-success" id="div_content_renewal_card{{$key}}1" style="display: none">
                                                                                <div class="card-header text-success border-success">
                                                                                    <strong>Renewal Report</strong>
                                                                                </div>
                                                                                <div class="card-body" id="div_content_renewal_modal{{$key}}1"></div>
                                                                            </div>
                                                                            <br>
                                                                            <div class="card border-danger" id="div_content_pickup_card{{$key}}1" style="display: none">
                                                                                <div class="card-header text-danger border-danger">
                                                                                    <strong>Pickup Report</strong>
                                                                                </div>
                                                                                <div class="card-body" id="div_content_pickup_modal{{$key}}1"></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {{-- @if($lead['product_details'][0]['current_status']=='Pending' && $lead['product_details'][0]['sale_rental']=='Sale')
                                                                <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#delivery_modal{{$key}}1" id="{{$key}}1" name="delivery_modal" onclick="btnReportClick(this.name,this.id);"><i class="fa fa-eye" aria-hidden="true"></i> Delivery</button>
                                                                <div class="modal fade bd-example-modal-xl" id="delivery_modal{{$key}}1" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog modal-xl">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <strong>Delivery Report</strong>
                                                                            </div>
                                                                            <div class="modal-body" id="div_content_delivery_modal{{$key}}1"></div>
                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @elseif(($lead['product_details'][0]['current_status']=='Pending' || $lead['product_details'][0]['current_status']=='Picked Up') && $lead['product_details'][0]['sale_rental']=='Rental')
                                                                <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#delivery_modal{{$key}}1" id="{{$key}}1" name="delivery_modal" onclick="btnReportClick(this.name,this.id);"><i class="fa fa-eye" aria-hidden="true"></i> Delivery</button>
                                                                <div class="modal fade bd-example-modal-xl" id="delivery_modal{{$key}}1" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog modal-xl">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <strong>Delivery Report</strong>
                                                                            </div>
                                                                            <div class="modal-body" id="div_content_delivery_modal{{$key}}1"></div>
                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                 <button type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target="#renewal_modal{{$key}}1" id="{{$key}}1" name="renewal_modal" onclick="btnReportClick(this.name,this.id);"><i class="fa fa-eye" aria-hidden="true"></i> Renewals</button>
                                                                 <div class="modal fade bd-example-modal-xl" id="renewal_modal{{$key}}1" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                                                     <div class="modal-dialog modal-xl">
                                                                         <div class="modal-content" >
                                                                             <div class="modal-header">
                                                                                 <strong>Renewal Report</strong>
                                                                             </div>
                                                                             <div class="modal-body" id="div_content_renewal_modal{{$key}}1"></div>
                                                                             <div class="modal-footer">
                                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                            </div>
                                                                         </div>
                                                                     </div>
                                                                 </div>

                                                                <button type="button" class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#pickup_modal{{$key}}1" id="{{$key}}1" name="pickup_modal" onclick="btnReportClick(this.name,this.id);"><i class="fa fa-eye" aria-hidden="true"></i> Pickup</button>
                                                                <div class="modal fade bd-example-modal-xl" id="pickup_modal{{$key}}1" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog modal-xl">
                                                                        <div class="modal-content" >
                                                                            <div class="modal-header">
                                                                                <strong>Pickup Report</strong>
                                                                            </div>
                                                                            <div class="modal-body" id="div_content_pickup_modal{{$key}}1"></div>
                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @elseif(($lead['product_details'][0]['current_status']=='Pending' || $lead['product_details'][0]['current_status']=='Renewed')  && $lead['product_details'][0]['sale_rental']=='Rental')

                                                                <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#delivery_modal{{$key}}1" id="{{$key}}1" name="delivery_modal" onclick="btnReportClick(this.name,this.id);"><i class="fa fa-eye" aria-hidden="true"></i> Delivery</button>
                                                                <div class="modal fade bd-example-modal-xl" id="delivery_modal{{$key}}1" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog modal-xl">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <strong>Delivery Report</strong>
                                                                            </div>
                                                                            <div class="modal-body" id="div_content_delivery_modal{{$key}}1"></div>
                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <button type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target="#renewal_modal{{$key}}1" id="{{$key}}1" name="renewal_modal" onclick="btnReportClick(this.name,this.id);"><i class="fa fa-eye" aria-hidden="true"></i> Renewals</button>
                                                                <div class="modal fade bd-example-modal-xl" id="renewal_modal{{$key}}1" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog modal-xl">
                                                                        <div class="modal-content" >
                                                                            <div class="modal-header">
                                                                                <strong>Renewal Report</strong>
                                                                            </div>
                                                                            <div class="modal-body" id="div_content_renewal_modal{{$key}}1">   </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif --}}
                                                        </td>
                                                       
                                                        <td>
                                                            <input type="hidden" name="lead_id" id="hid_lead_id{{$key}}1" value="{{$lead['id']}}">
                                                            <input type="hidden" name="product_id" id="hid_product_id{{$key}}1" value="{{$lead['product_details'][0]['product_id']}}">
                                                            <input type="hidden" name="order_details_id" id="hid_order_details_id{{$key}}1" value="{{$lead['product_details'][0]['order_details_id']}}">
                                                            <span id="{{$key}}1" data-count="{{$key}}1" onclick="ProductNameClick(this.id);">{{$lead['product_details'][0]['product_name']}}</span>
                                                        </td>
                                                        <td>
                                                            {{$lead['product_details'][0]['product_qty']}}
                                                        </td>
                                                        <td>
                                                            {{$lead['product_details'][0]['sale_rental']}}
                                                        </td>
                                                        <td>{{$lead['product_details'][0]['vendor_name']}}</td>
                                                        <td>
                                                            {{$lead['product_details'][0]['product_rent']}}
                                                        </td>
                                                        <td>
                                                            {{$lead['product_details'][0]['product_deposite']}}
                                                        </td>
                                                        <td>
                                                            @if(($lead['product_details'][0]['current_status']=='Pending' || $lead['product_details'][0]['current_status']=='Picked Up') && $lead['product_details'][0]['sale_rental']=='Rental')
                                                                {{$lead['product_details'][0]['product_deposite']}}
                                                            @else
                                                                0
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if(($lead['product_details'][0]['current_status']=='Pending' || $lead['product_details'][0]['current_status']=='Renewed')  && $lead['product_details'][0]['sale_rental']=='Rental')
                                                                {{$lead['product_details'][0]['product_deposite']}}
                                                            @else
                                                                0
                                                            @endif
                                                        </td>
                                                        <td> 
                                                            {{$lead['product_details'][0]['transport']}}
                                                        </td>
                                                        <td>{{$lead['product_details'][0]['total_amt']}}</td>
                                                        <td>{{$lead['product_details'][0]['total_amt']}}</td>
                                                        <td>
                                                            @if($lead['product_details'][0]['current_status']=='Pending' && $lead['product_details'][0]['sale_rental']=='Sale')
                                                                -
                                                            @elseif(($lead['product_details'][0]['current_status']=='Pending' || $lead['product_details'][0]['current_status']=='Picked Up') && $lead['product_details'][0]['sale_rental']=='Rental' )
                                                                {{$lead['product_details'][0]['renewal_counts']}}
                                                            @elseif(($lead['product_details'][0]['current_status']=='Pending' || $lead['product_details'][0]['current_status']=='Renewed')  && $lead['product_details'][0]['sale_rental']=='Rental')
                                                                {{$lead['product_details'][0]['renewal_counts']}}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($lead['product_details'][0]['current_status']=='Pending' && $lead['product_details'][0]['sale_rental']=='Sale')
                                                                -
                                                            @elseif(($lead['product_details'][0]['current_status']=='Pending' || $lead['product_details'][0]['current_status']=='Picked Up') && $lead['product_details'][0]['sale_rental']=='Rental' )
                                                                {{$lead['product_details'][0]['total_rent']}}
                                                            @elseif(($lead['product_details'][0]['current_status']=='Pending' || $lead['product_details'][0]['current_status']=='Renewed')  && $lead['product_details'][0]['sale_rental']=='Rental')
                                                                {{$lead['product_details'][0]['total_rent']}}
                                                            @endif
                                                        </td>
                                                        <td>{{$lead['lead_status']}}</td>
                                                        <td>{{$lead['username']}}</td>
                                                        <td>{{$lead['lead_source']}}</td>
                                                        <td>{{$lead['comment']}}</td>
                                                        
                                                    </tr>
                                                    @if(count($lead['product_details'])>1)
                                                        @for ($i=1; $i<count($lead['product_details']); $i++)
                                                            <tr>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                {{-- <td>{{$no}}</td> --}}
                                                                <td class="text-center">
                                                                    {{-- @if($lead['product_details'][$i]['current_status']=='Pending' && $lead['product_details'][$i]['sale_rental']=='Sale')
                                                                        <span class="badge badge-secondary">Sold</span>
                                                                    @elseif(($lead['product_details'][$i]['current_status']=='Pending Pickup' || $lead['product_details'][$i]['current_status']=='Picked Up') && $lead['product_details'][$i]['sale_rental']=='Rental' )
                                                                        <span class="badge badge-danger">Stop</span>
                                                                    @elseif(($lead['product_details'][$i]['current_status']=='Pending' || $lead['product_details'][$i]['current_status']=='Renewed')  && $lead['product_details'][$i]['sale_rental']=='Rental')
                                                                        <span class="badge badge-success">Live</span>
                                                                    @endif --}}
                                                                    @if($lead['product_details'][$i]['current_status']=='Pending' && $lead['product_details'][$i]['sale_rental']=='Sale')
                                                                        <span class="badge badge-secondary">Sold</span>
                                                                    @elseif(($lead['product_details'][$i]['current_status']==='Pending Pickup' || ($lead['product_details'][$i]['current_status']==='Picked up' || $lead['product_details'][$i]['current_status']==='Picked UP' || $lead['product_details'][$i]['current_status']==='Picked Up')) && $lead['product_details'][$i]['sale_rental']==='Rental')
                                                                        <span class="badge badge-danger">Picked Up</span>
                                                                    @elseif(($lead['product_details'][$i]['current_status']=='Pending' || $lead['product_details'][$i]['current_status']=='Renewed')  && $lead['product_details'][$i]['sale_rental']=='Rental')
                                                                        <span class="badge badge-success">Live</span>
                                                                    @elseif(($lead['product_details'][$i]['current_status'] == 'Cancel'))
                                                                        <span class="badge badge-warning">Cancel</span>
                                                                    @elseif(($lead['product_details'][$i]['current_status'] == 'CustStop'))
                                                                        <span class="badge badge-primary">Stop</span>
                                                                    @else
                                                                        <span class="badge badge-primary">Undefined</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <div class="dropdown">
                                                                        <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="actionDropDown{{$key}}{{$no}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                            Action
                                                                        </button>
                                                                        <div class="dropdown-menu" aria-labelledby="actionDropDown{{$key}}{{$no}}">
                                                                            <button class="dropdown-item" data-toggle="modal" data-target="#history{{$key}}{{$no}}" id="{{$key}}{{$no}}" name="history" onclick="btnReportClick(this.name,this.id);">View</button>
                                                                            @if(($lead['product_details'][$i]['current_status']=='Pending' || $lead['product_details'][$i]['current_status']=='Renewed')  && $lead['product_details'][$i]['sale_rental']=='Rental')
                                                                                <a class="dropdown-item" href="{{route('order-request',['submit'=>'renew','checked_product'=>array($lead['product_details'][$i]['order_details_id'])])}}">Renew</a>
                                                                                <a class="dropdown-item" href="{{route('order-request',['submit'=>'pickup','checked_product'=>array($lead['product_details'][$i]['order_details_id'])])}}">Pickup</a>
                                                                            @endif
                                                                        </div>
                                                                    </div>

                                                                    {{-- <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#history{{$key}}{{$no}}" id="{{$key}}{{$no}}" name="history" onclick="btnReportClick(this.name,this.id);"><i class="fa fa-history" aria-hidden="true"></i></button> --}}
                                                                    <div class="modal fade bd-example-modal-xl" id="history{{$key}}{{$no}}" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                                                        <div class="modal-dialog modal-xl">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <div class="col-md-12">
                                                                                        <div class="row">
                                                                                            <div class="col-md-4">
                                                                                                <strong>Report</strong>
                                                                                            </div>
                                                                                            <div class="col-md-8">
                                                                                                <div class="text-right">
                                                                                                    <select class="selectpicker" name="select" id="{{$key}}{{$no}}" onchange="btnSelect(this.value,this.id)">
                                                                                                        <option value="all" selected>All</option>
                                                                                                        <option value="delivery_report">Delivery Report</option>
                                                                                                        <option value="renewal_report">Renewal Report</option>
                                                                                                        <option value="pickup_report">Pickup Report</option>
                                                                                                    </select>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <div class="card border-primary" id="div_content_delivery_card{{$key}}{{$no}}" style="display: none">
                                                                                        <div class="card-header text-primary border-primary">
                                                                                            <strong>Delivery Report</strong>
                                                                                        </div>
                                                                                        <div class="card-body" id="div_content_delivery_modal{{$key}}{{$no}}"></div>
                                                                                    </div>
                                                                                    <br>
                                                                                    <div class="card border-success" id="div_content_renewal_card{{$key}}{{$no}}" style="display: none">
                                                                                        <div class="card-header text-success border-success">
                                                                                            <strong>Renewal Report</strong>
                                                                                        </div>
                                                                                        <div class="card-body" id="div_content_renewal_modal{{$key}}{{$no}}"></div>
                                                                                    </div>
                                                                                    <br>
                                                                                    <div class="card border-danger" id="div_content_pickup_card{{$key}}{{$no}}" style="display: none">
                                                                                        <div class="card-header text-danger border-danger">
                                                                                            <strong>Pickup Report</strong>
                                                                                        </div>
                                                                                        <div class="card-body" id="div_content_pickup_modal{{$key}}{{$no}}"></div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
        
                                                                    {{-- @if($lead['product_details'][0]['current_status']=='Pending' && $lead['product_details'][0]['sale_rental']=='Sale')
                                                                        <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#delivery_modal{{$key}}1" id="{{$key}}1" name="delivery_modal" onclick="btnReportClick(this.name,this.id);"><i class="fa fa-eye" aria-hidden="true"></i> Delivery</button>
                                                                        <div class="modal fade bd-example-modal-xl" id="delivery_modal{{$key}}1" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                                                            <div class="modal-dialog modal-xl">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <strong>Delivery Report</strong>
                                                                                    </div>
                                                                                    <div class="modal-body" id="div_content_delivery_modal{{$key}}1"></div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @elseif(($lead['product_details'][0]['current_status']=='Pending' || $lead['product_details'][0]['current_status']=='Picked Up') && $lead['product_details'][0]['sale_rental']=='Rental')
                                                                        <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#delivery_modal{{$key}}1" id="{{$key}}1" name="delivery_modal" onclick="btnReportClick(this.name,this.id);"><i class="fa fa-eye" aria-hidden="true"></i> Delivery</button>
                                                                        <div class="modal fade bd-example-modal-xl" id="delivery_modal{{$key}}1" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                                                            <div class="modal-dialog modal-xl">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <strong>Delivery Report</strong>
                                                                                    </div>
                                                                                    <div class="modal-body" id="div_content_delivery_modal{{$key}}1"></div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                         <button type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target="#renewal_modal{{$key}}1" id="{{$key}}1" name="renewal_modal" onclick="btnReportClick(this.name,this.id);"><i class="fa fa-eye" aria-hidden="true"></i> Renewals</button>
                                                                         <div class="modal fade bd-example-modal-xl" id="renewal_modal{{$key}}1" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                                                             <div class="modal-dialog modal-xl">
                                                                                 <div class="modal-content" >
                                                                                     <div class="modal-header">
                                                                                         <strong>Renewal Report</strong>
                                                                                     </div>
                                                                                     <div class="modal-body" id="div_content_renewal_modal{{$key}}1"></div>
                                                                                     <div class="modal-footer">
                                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                                    </div>
                                                                                 </div>
                                                                             </div>
                                                                         </div>
        
                                                                        <button type="button" class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#pickup_modal{{$key}}1" id="{{$key}}1" name="pickup_modal" onclick="btnReportClick(this.name,this.id);"><i class="fa fa-eye" aria-hidden="true"></i> Pickup</button>
                                                                        <div class="modal fade bd-example-modal-xl" id="pickup_modal{{$key}}1" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                                                            <div class="modal-dialog modal-xl">
                                                                                <div class="modal-content" >
                                                                                    <div class="modal-header">
                                                                                        <strong>Pickup Report</strong>
                                                                                    </div>
                                                                                    <div class="modal-body" id="div_content_pickup_modal{{$key}}1"></div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @elseif(($lead['product_details'][0]['current_status']=='Pending' || $lead['product_details'][0]['current_status']=='Renewed')  && $lead['product_details'][0]['sale_rental']=='Rental')
        
                                                                        <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#delivery_modal{{$key}}1" id="{{$key}}1" name="delivery_modal" onclick="btnReportClick(this.name,this.id);"><i class="fa fa-eye" aria-hidden="true"></i> Delivery</button>
                                                                        <div class="modal fade bd-example-modal-xl" id="delivery_modal{{$key}}1" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                                                            <div class="modal-dialog modal-xl">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <strong>Delivery Report</strong>
                                                                                    </div>
                                                                                    <div class="modal-body" id="div_content_delivery_modal{{$key}}1"></div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <button type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target="#renewal_modal{{$key}}1" id="{{$key}}1" name="renewal_modal" onclick="btnReportClick(this.name,this.id);"><i class="fa fa-eye" aria-hidden="true"></i> Renewals</button>
                                                                        <div class="modal fade bd-example-modal-xl" id="renewal_modal{{$key}}1" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                                                            <div class="modal-dialog modal-xl">
                                                                                <div class="modal-content" >
                                                                                    <div class="modal-header">
                                                                                        <strong>Renewal Report</strong>
                                                                                    </div>
                                                                                    <div class="modal-body" id="div_content_renewal_modal{{$key}}1">   </div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endif --}}
                                                                </td>
                                                                
                                                                <td>
                                                                    <input type="hidden" name="lead_id" id="hid_lead_id{{$key}}{{$no}}" value="{{$lead['id']}}">
                                                                    <input type="hidden" name="product_id" id="hid_product_id{{$key}}{{$no}}" value="{{$lead['product_details'][$i]['product_id']}}">
                                                                    <input type="hidden" name="order_details_id" id="hid_order_details_id{{$key}}{{$no}}" value="{{$lead['product_details'][$i]['order_details_id']}}">
                                                                    <span id="{{$key}}1" data-count="{{$key}}1" onclick="ProductNameClick(this.id);">{{$lead['product_details'][$i]['product_name']}}</span>
                                                                </td>
                                                                <td>
                                                                    {{$lead['product_details'][$i]['product_qty']}}
                                                                </td>
                                                                <td>
                                                                    {{$lead['product_details'][$i]['sale_rental']}}
                                                                </td>
                                                                <td>
                                                                    {{$lead['product_details'][$i]['vendor_name']}}
                                                                </td>
                                                                <td>
                                                                    {{$lead['product_details'][$i]['product_rent']}}
                                                                </td>
                                                                <td>
                                                                    {{$lead['product_details'][$i]['product_deposite']}}
                                                                </td>
                                                                <td>
                                                                    @if(($lead['product_details'][$i]['current_status']=='Pending' || $lead['product_details'][$i]['current_status']=='Picked Up') && $lead['product_details'][$i]['sale_rental']=='Rental')
                                                                        {{$lead['product_details'][$i]['product_deposite']}}
                                                                    @else
                                                                        0
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if(($lead['product_details'][$i]['current_status']=='Pending' || $lead['product_details'][$i]['current_status']=='Renewed')  && $lead['product_details'][$i]['sale_rental']=='Rental')
                                                                        {{$lead['product_details'][$i]['product_deposite']}}
                                                                    @else
                                                                        0
                                                                    @endif
                                                                </td>
                                                                <td> 
                                                                    {{$lead['product_details'][$i]['transport']}}
                                                                </td>
                                                                <td>{{$lead['product_details'][$i]['total_amt']}}</td>
                                                                <td>{{$lead['product_details'][$i]['total_amt']}}</td>
                                                                <td>
                                                                    @if($lead['product_details'][$i]['current_status']=='Pending' && $lead['product_details'][$i]['sale_rental']=='Sale')
                                                                        -
                                                                    @elseif(($lead['product_details'][$i]['current_status']=='Pending' || $lead['product_details'][$i]['current_status']=='Picked Up') && $lead['product_details'][$i]['sale_rental']=='Rental' )
                                                                        {{$lead['product_details'][$i]['renewal_counts']}}
                                                                    @elseif(($lead['product_details'][$i]['current_status']=='Pending' || $lead['product_details'][$i]['current_status']=='Renewed')  && $lead['product_details'][$i]['sale_rental']=='Rental')
                                                                        {{$lead['product_details'][$i]['renewal_counts']}}
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($lead['product_details'][$i]['current_status']=='Pending' && $lead['product_details'][$i]['sale_rental']=='Sale')
                                                                        -
                                                                    @elseif(($lead['product_details'][$i]['current_status']=='Pending' || $lead['product_details'][$i]['current_status']=='Picked Up') && $lead['product_details'][$i]['sale_rental']=='Rental' )
                                                                        {{$lead['product_details'][$i]['total_rent']}}
                                                                    @elseif(($lead['product_details'][$i]['current_status']=='Pending' || $lead['product_details'][$i]['current_status']=='Renewed')  && $lead['product_details'][$i]['sale_rental']=='Rental')
                                                                        {{$lead['product_details'][$i]['total_rent']}}
                                                                    @endif
                                                                </td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                               
                                                            </tr>
                                                            @php
                                                                $no++;
                                                            @endphp
                                                        @endfor
                                                    @endif
                                                    @php
                                                        $srno++;
                                                    @endphp
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    {{--Customer Complaints--}}
                    @if(isset($get_complaints))
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <strong>Complaints</strong>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-striped table-responsive" id="complaint_table" width="100%">
                                            <thead>
                                                <tr>
                                                    <td>Sr.No</td>
                                                    <td>Complaint Date</td>
                                                    <td>Complaint ID</td>
                                                    <td>Product Name</td>
                                                    <td>Vendor Name</td>
                                                    <td>Delivered By</td>
                                                    <td>Lead Owner</td>
                                                    <td>Status</td>
                                                    <td>Action</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $srno =1;
                                                @endphp
                                                @foreach($get_complaints as $key => $complaint)
                                                    <tr>
                                                        <td>{{$srno}}</td>
                                                        <td>{{date('d-m-Y',strtotime($complaint['complaint_date']))}}</td>
                                                        <td>
                                                            <a href="{{url('/')}}/complaint_details/{{$complaint['generated_complaint_id']}}">{{$complaint['generated_complaint_id']}}</a>
                                                        </td>
                                                        <td>{{$complaint['product_name']}}</td>
                                                        <td>{{$complaint['vendor_name']}}</td>
                                                        <td>{{$complaint['delivered_by']}}</td>
                                                        <td>{{$complaint['lead_owner']}}</td>
                                                        <td>{{$complaint['status']}}</td>
                                                        <td>
                                                            @if($complaint['status']=='Open')
                                                                <a href="{{url('/')}}/complaint_details/{{$complaint['generated_complaint_id']}}" class="btn btn-outline-danger">Close</a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>

                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
    @endsection
</body>
@section('script')
<script>
    $('#complaint_table').DataTable({});
    $('.lead_table').DataTable({
        "order": []
    });
    
    function btnReportClick(name,id)
    {
        let lead_id = $('#hid_lead_id'+id).val();
        let product_id = $('#hid_product_id'+id).val();
        let order_details_id = $('#hid_order_details_id'+id).val();
        $.ajax({
            type: "GET",
            url: "<?php echo url('/');?>/get_customer_product_data/"+order_details_id+"/"+name,
            //data: dataString,
            cache: false,
            //dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            success: function(data)
            {
                var obj = jQuery.parseJSON(data);
                console.log(data);
                if(obj['get_del_order_data'].length>0)
                {
                    let cash = "";
                    let online = "";
                    if(obj['get_del_order_data'].cash!=null)
                    {
                        cash = obj['get_del_order_data'].cash;    
                    }
                    else
                    {
                        cash="-";
                    }
                    if(obj['get_del_order_data'].online!=null)
                    {
                        online = obj['get_del_order_data'].online;    
                    }
                    else
                    {
                        online="-";
                    }
                    $('#div_content_delivery_card'+id).css('display','block');
                    $('#no_data').remove();
                    $('#table_delivery_report').remove();
                    var rows="<table class='table table-striped table-bordered table-responsive' id='table_delivery_report' width='100%'>";
                            rows+="<thead>";
                                rows+="<tr>";
                                    rows+="<td>Date &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</td>";
                                    rows+="<td>Order Id</td>";
                                    rows+="<td>Lead Id</td>";
                                    rows+="<td>Product Name</td>";
                                    rows+="<td>Vendor Name</td>";
                                    rows+="<td>Warehouse Name</td>";
                                    rows+="<td>Del Assigned To</td>";
                                    rows+="<td>Helpers</td>";
                                    rows+="<td>Amount</td>";
                                    rows+="<td>Payment Mode</td>";
                                    rows+="<td>Cash</td>";
                                    rows+="<td>Online</td>";
                                rows+="</tr>";
                            rows+="</thead>";
                            rows+="<tbody id='tbody_del_report'>";
                                rows+="<tr>";
                                    rows+="<td>"+obj['get_del_order_data'][0].DelDate+"</td>";
                                    rows+="<td>"+obj['get_del_order_data'][0].order_id+"</td>";
                                    rows+="<td>"+obj['get_del_order_data'][0].lead_id+"</td>";
                                    rows+="<td>"+obj['get_del_order_data'][0].product_name+" </td>";
                                    rows+="<td>"+obj['get_del_order_data'][0].vendor_name+" </td>";
                                    rows+="<td>"+obj['get_del_order_data'][0].warehouse_name+" </td>";
                                    rows+="<td>"+obj['get_del_order_data'][0].DelAssignedTo+" </td>";
                                    rows+="<td>"+obj['get_del_order_data'][0].helpers+"</td>";
                                    rows+="<td>"+obj['get_del_order_data'][0].TotalAmt+"</td>";
                                    rows+="<td>"+obj['get_del_order_data'][0].PaymentMode+"</td>";
                                    rows+="<td>"+cash+"</td>";
                                    rows+="<td>"+online+"</td>";
                                rows+="</tr>";
                            rows+="</tbody>";
                        rows+="</table>";
                    $('#div_content_delivery_modal'+id).append(rows);
                    //$('#div_product_delivery').css('display','block');
                }
                else
                {
                    $('#div_content_delivery_card'+id).css('display','none');
                    // $('#no_data').remove();
                    // var span='<span id="no_data">No data available</span>' ;
                    // $('#div_content_delivery_modal'+id).append(span);
                }

                //renewal report
                if(obj['get_renewal_orders_data'].length>0)
                {
                    $('#div_content_renewal_card'+id).css('display','block');
                    $('#no_data').remove();
                    //var renewal_length = obj.length;
                    $('#table_renewal_report').remove();

                    
                    var rows2="<table class='table table-striped table-bordered table-responsive' id='table_renewal_report' width='100%'>";
                            rows2+="<thead>";
                                rows2+="<tr>";
                                    rows2+="<td>Date &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</td>";
                                    rows2+="<td>Collection Order Id</td>";
                                    rows2+="<td>Delivered Order Id</td>";
                                    rows2+="<td>Product Name</td>";
                                    rows2+="<td>Start Date &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</td>";
                                    rows2+="<td>End Date &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</td>";
                                    rows2+="<td>Collection Date &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</td>";
                                    rows2+="<td>Payment Mode</td>";
                                    rows2+="<td>Cash</td>";
                                    rows2+="<td>Online</td>";
                                    rows2+="<td>Reference Id</td>";
                                    rows2+="<td>Online Method</td>";
                                    rows2+="<td>Collected By</td>";
                                    rows2+="<td>Comment</td>";
                                    rows2+="<td>Status</td>";
                                rows2+="</tr>";
                            rows2+="</thead>";
                            rows2+="<tbody id='tbody_pickup_report'>";
                                for (let i = 0; i<obj['get_renewal_orders_data'].length; i++) {
                                    let cash = "";
                                    let online = "";
                                    let comment = "";
                                    let reference_id = "";
                                    let online_method = "";
                                    if(obj['get_renewal_orders_data'][i].cash!=null){
                                        cash = obj['get_renewal_orders_data'][i].cash;    
                                    }else{
                                        cash="-";
                                    }
                                    if(obj['get_renewal_orders_data'][i].online!=null){
                                        online = obj['get_renewal_orders_data'][i].online;    
                                    }else{
                                        online="-";
                                    }
                                    if(obj['get_renewal_orders_data'][i].comment!=null){
                                        comment = obj['get_renewal_orders_data'][i].comment;    
                                    }else{
                                        comment="-";
                                    }
                                    if(obj['get_renewal_orders_data'][i].reference_id!=null){
                                        reference_id = obj['get_renewal_orders_data'][i].reference_id;    
                                    }else{
                                        reference_id="-";
                                    }
                                    if(obj['get_renewal_orders_data'][i].online_method!=null){
                                        online_method = obj['get_renewal_orders_data'][i].online_method;    
                                    }else{
                                        online_method="-";
                                    }
                                    rows2+="<tr>";
                                        rows2+="<td>"+obj['get_renewal_orders_data'][i].DelDate+"</td>";
                                        rows2+="<td>"+obj['get_renewal_orders_data'][i].collection_order_id+"</td>";
                                        rows2+="<td>"+obj['get_renewal_orders_data'][i].order_id+"</td>";
                                        rows2+="<td>"+obj['get_renewal_orders_data'][i].product_name+" </td>";
                                        rows2+="<td>"+obj['get_renewal_orders_data'][i].start_date+" </td>";
                                        rows2+="<td>"+obj['get_renewal_orders_data'][i].end_date+" </td>";
                                        rows2+="<td>"+obj['get_renewal_orders_data'][i].Collection_Date+" </td>";
                                        rows2+="<td>"+obj['get_renewal_orders_data'][i].payment_mode+"</td>";
                                        rows2+="<td>"+cash+"</td>";
                                        rows2+="<td>"+online+"</td>";
                                        rows2+="<td>"+reference_id+"</td>";
                                        rows2+="<td>"+online_method+"</td>";
                                        rows2+="<td>"+obj['get_renewal_orders_data'][i].DelAssignedTo+"</td>";
                                        rows2+="<td>"+comment+"</td>";
                                        rows2+="<td>"+obj['get_renewal_orders_data'][i].status+"</td>";
                                    rows2+="</tr>";
                                }
                            rows2+="</tbody>";
                        rows2+="</table>";
                        $('#div_content_renewal_modal'+id).append(rows2);
                }
                else
                {
                    $('#div_content_renewal_card'+id).css('display','none');
                    // $('#no_data').remove();
                    // var span='<span id="no_data">No data available</span>' ;
                    // $('#div_content_'+name+id).append(span);
                }

                //pickup report
                if(obj['get_pickup_order_data'].length>0)
                {
                    $('#div_content_pickup_card'+id).css('display','block');
                    $('#no_data').remove();
                    $('#table_pickup_report').remove();
                    var rows1="<table class='table table-striped table-bordered table-responsive' id='table_pickup_report' width='100%'>";
                            rows1+="<thead>";
                                rows1+="<tr>";
                                    rows1+="<td>Date</td>";
                                    rows1+="<td>Pickup Order Id</td>";
                                    rows1+="<td>Delivered Order Id</td>";
                                    rows1+="<td>Product Name</td>";
                                    rows1+="<td>Drop Vendor Name</td>";
                                    rows1+="<td>Drop Warehouse Name</td>";
                                    rows1+="<td>Pickup Assigned To</td>";
                                    rows1+="<td>Helpers</td>";
                                    rows1+="<td>Refund Amount</td>";
                                    rows1+="<td>Payment Mode</td>";
                                    rows1+="<td>Cash</td>";
                                    rows1+="<td>Online</td>";
                                rows1+="</tr>";
                            rows1+="</thead>";
                            rows1+="<tbody id='tbody_pickup_report'>";
                                rows1+="<tr>";
                                    rows1+="<td>"+obj['get_pickup_order_data'][0].DelDate+"</td>";
                                    rows1+="<td>"+obj['get_pickup_order_data'][0].pickup_order_id+"</td>";
                                    rows1+="<td>"+obj['get_pickup_order_data'][0].delivery_order_id+"</td>";
                                    rows1+="<td>"+obj['get_pickup_order_data'][0].product_name+" </td>";
                                    rows1+="<td>"+obj['get_pickup_order_data'][0].vendor_name+" </td>";
                                    rows1+="<td>"+obj['get_pickup_order_data'][0].warehouse_name+" </td>";
                                    rows1+="<td>"+obj['get_pickup_order_data'][0].DelAssignedTo+" </td>";
                                    rows1+="<td>"+obj['get_pickup_order_data'][0].helpers+"</td>";
                                    rows1+="<td>"+obj['get_pickup_order_data'][0].TotalAmt+"</td>";
                                    rows1+="<td>"+obj['get_pickup_order_data'][0].PaymentMode+"</td>";
                                    rows1+="<td>"+obj['get_pickup_order_data'][0].cash+"</td>";
                                    rows1+="<td>"+obj['get_pickup_order_data'][0].online+"</td>";
                                rows1+="</tr>";
                            rows1+="</tbody>";
                        rows1+="</table>";
                        $('#div_content_pickup_modal'+id).append(rows1);
                    //$('#div_product_delivery').css('display','block');
                }
                else
                {
                    $('#div_content_pickup_card'+id).css('display','none');
                    // $('#no_data').remove();
                    // var span='<span id="no_data">No data available</span>' ;
                    // $('#div_content_pickup_modal'+id).append(rows2);
                }

                
            },
            error: function(xhr, status, error){
                var errorMessage = xhr.status + ': ' + xhr.statusText
                alert(errorMessage);
                // $.toast({
                //     type: 'error',
                //    // title: 'Customer Not Found',
                //     subtitle: '11 mins ago',
                //     content: 'Something Wennt Wrong',
                //     delay: 5000
                // });
            }
        });
    }

    //populate customer name in text field
    // $('#txt_customer_value').on('keyup',function(){
    //     let key_val = $(this).val();
    //     if(key_val.length!=0)
    //     {
    //         $.ajax({
    //             type: "GET",
    //             url: "<?php echo url('/');?>/cust_single_populate_customers/"+key_val,
    //             //data: dataString,
    //             cache: false,
    //             //dataType: 'json',
    //             contentType: 'application/json; charset=utf-8',
    //             success: function(data)
    //             {
    //                 //console.log(data);
    //                 var obj = jQuery.parseJSON(data);
    //                 //console.log(obj);
    //                 $('#datalist_customers').empty();
    //                 $.each(obj, function(i, item) {
    //                     //$("#datalist_customers").append($("<option>").attr('value', i).text(obj[i]['customer_name']));
    //                     $("#datalist_customers").append($("<option>").text(obj[i]['customer_name']));
    //                 });
    //             },
    //             error: function(xhr, status, error)
    //             {
    //                 var errorMessage = xhr.status + ': ' + xhr.statusText
    //                 alert(errorMessage);
    //                 // $.toast({
    //                 //     type: 'error',
    //                 //    // title: 'Customer Not Found',
    //                 //     subtitle: '11 mins ago',
    //                 //     content: 'Something Wennt Wrong',
    //                 //     delay: 5000
    //                 // });
    //             }

    //         });
    //     }
    // });
    function btnSelect(val,id) 
    {
        let product_id = $('#hid_product_id'+id).val();
        let order_details_id = $('#hid_order_details_id'+id).val();
        if(val=='delivery_report')
        {
            $('#div_content_delivery_card'+id).css('display','block');
            $('#div_content_renewal_card'+id).css('display','none');
            $('#div_content_pickup_card'+id).css('display','none');
        }
        else if(val=='renewal_report')
        {
            $('#div_content_delivery_card'+id).css('display','none');
            $('#div_content_renewal_card'+id).css('display','block');
            $('#div_content_pickup_card'+id).css('display','none');
        }
        else if(val=='pickup_report')
        {
            $('#div_content_delivery_card'+id).css('display','none');
            $('#div_content_renewal_card'+id).css('display','none');
            $('#div_content_pickup_card'+id).css('display','block');
        }
        else if(val=='all')
        {
            $('#div_content_delivery_card'+id).css('display','block');
            $('#div_content_renewal_card'+id).css('display','block');
            $('#div_content_pickup_card'+id).css('display','block');
        }

    }
</script>
   
@endsection
</html>  