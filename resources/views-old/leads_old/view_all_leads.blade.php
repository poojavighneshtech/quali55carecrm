@extends('header_and_sidebar')
<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        
        <title>All Leads</title>
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
            .card-header .fa {
            transition: .3s transform ease-in-out;
            }
            .card-header .collapsed .fa {
            transform: rotate(90deg);
            }
            /* .table tbody tr td, 
            .table thead tr th { */
                white-space: nowrap;
                width: 1%;
            /* } */
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
        <div class="col-md-12">
            
            <div class="card">  
                <div class="card-header"  style="background-color: #4a6fdc; color: white;" >
                    <center>All Leads</center>
                </div> 
                <div class="card-body">
                    <div class="card" id="filter_card">
                       {{-- <h6 id="h6_filter"><span class="border border-dark rounded bg-primary text-white">&emsp;Filter&emsp;</span></h6> --}}
                        <div class="card-header border border-primary" >
                            <div class="row">
                                <div class="col" data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                                    <strong >Filter</strong>
                                </div>
                                <div class="col-auto">
                                    <a data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                                        <i class="fa fa-chevron-down pull-right"></i>
                                    </a>
                                </div>
                              </div>
                        </div>
                        <div class="card-body collapse @if(!array_filter($filter_arr)) hide @else show @endif" id="filter-collapse" aria-labelledby="heading-filter">
                            <form action="{{url('/')}}/view_all_leads" method="GET" id="all_leads_form">
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
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4 text-right">
                                                        <label for="lead_status"><strong>Status :</strong></label>
                                                    </div>
                                                    <div class="col-md-8 text-right">
                                                        <select class="select form-control selectpicker border" name="filter_lead_status" id="select_filter_lead_status" title="Lead Status">
                                                            <option value="All" selected>All</option>
                                                            <option value="Work In Process" @if(isset($filter_arr['lead_status']) && $filter_arr['lead_status']=='Work In Process'){{"selected"}}@endif>In Process</option>
                                                            <option value="Converted" @if(isset($filter_arr['lead_status']) && $filter_arr['lead_status']=='Converted'){{"selected"}}@endif>Converted</option>
                                                            <option value="Order Generated" @if(isset($filter_arr['lead_status']) && $filter_arr['lead_status']=='Order Generated'){{"selected"}}@endif>Order Generated</option>
                                                            <option value="Closed" @if(isset($filter_arr['lead_status']) && $filter_arr['lead_status']=='Closed'){{"selected"}}@endif>Closed</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-3 text-right">
                                                        <strong>Location</strong>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <select class="select form-control selectpicker border" name="filter_customer_location" id="select_filter_customer_location" title="Customer Location"
                                                            data-live-search="true" data-size="5">
                                                            <option value="All" selected>All</option>
                                                            @foreach ($get_customer_location as $key => $locations)
                                                                <option value="{{$locations->location}}" @if(isset($filter_arr['location']) && $locations->location == $filter_arr['location'])selected @endif>{{$locations->location}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row form-group" id="div_order_id" style="@if(isset($filter_arr['lead_status']) && $filter_arr['lead_status']=='Order Generated') display:block @else display:none @endif">
                                            <div class="col-md-6">
                                                
                                                <div class="row">
                                                    <div class="col-md-4 text-right">
                                                        <label for="order_id"><strong>Order Id :</strong></label>
                                                    </div>
                                                    <div class="col-md-8 text-left">
                                                        <input type="text" class="form-control form-group" name="filter_order_id"  id="txt_filter_order_id" placeholder="Order ID..."
                                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" 
                                                            value="@if(isset($filter_arr['order_id'])){{$filter_arr['order_id']}}@endif">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4 text-right">
                                                        <label for="lead source"><strong>Source :</strong></label>
                                                    </div>
                                                    <div class="col-md-8 text-right">
                                                        <select class="select form-control selectpicker border" name="filter_lead_source" id="select_filter_lead_source" title="Lead source"
                                                            data-live-search="true" data-size="3">
                                                            <option value="All" selected>All</option>
                                                            @foreach($get_lead_sources as $key=>$lead_sources)
                                                                <option value="{{$lead_sources->lead_source}}" @if(isset($filter_arr['lead_source']) && $lead_sources->lead_source==$filter_arr['lead_source']) selected @endif>{{$lead_sources->lead_source}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4 text-right">
                                                        <strong>Customer Type</strong>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <select class="select form-control selectpicker border" name="filter_customer_type" id="select_filter_customer_type" title="Customer Type" 
                                                            data-live-search="true" data-size="5">
                                                            <option value="All" selected>All</option>
                                                            <option value="Individual" @if(isset($filter_arr) && $filter_arr['customer_type'] == 'Individual') selected @endif>Individual</option>
                                                            <option value="Corporate" @if(isset($filter_arr) && $filter_arr['customer_type'] == 'Corporate') selected @endif>Corporate</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @if(session('role')=='superuser')
                                            <br>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-4 text-right">
                                                            <strong>Lead Owner</strong>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <select class="select form-control selectpicker border" name="filter_lead_owner" id="select_filter_lead_owner" title="Lead Owners"
                                                                data-live-search="true" data-size="5">
                                                                <option value="All" selected>All</option>
                                                                @foreach ($get_lead_owners as $key => $lead_owners)
                                                                    <option value="{{$lead_owners->user_id}}" @if(isset($filter_arr['lead_owner']) && $lead_owners->user_id==$filter_arr['lead_owner']) selected @endif>{{$lead_owners->lead_owner}}</option>
                                                                @endforeach
                                                        </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-2">
                                        <div class="row">
                                            <div class="col">
                                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btn_clear">Clear Filter</button>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col">
                                                <button type="submit" class="btn btn-outline-success btn-block">Submit</button>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col">
                                                <a class="btn btn-outline-primary btn-sm btn-block" href="{{url('/')}}/create_lead">Create New Lead</a>
                                            </div>
                                        </div>
                                        <br>
                                        {{-- <div class="row">
                                            Customer Type:
                                            <div class="col-auto">
                                                <select class="select form-control" name="filter_customer_type" id="select_filter_customer_type">
                                                    <option value="All" >All</option>
                                                    <option value="Individual" @if(isset($filter_arr) && $filter_arr['customer_type'] == 'Individual') selected @endif>Individual</option>
                                                    <option value="Corporate" @if(isset($filter_arr) && $filter_arr['customer_type'] == 'Corporate') selected @endif>Corporate</option>
                                                </select>
                                            </div>
                                        </div> --}}
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    {{-- <div class="card" >
                        <div class="card-body"> --}}
                            <table class="table table-hover table-responsive" id="tbl_view_all_leads" width="100%" >
                                <thead class="thead">
                                    <tr class="text-nowrap">
                                        <th scope="col">
                                            @if(isset($filter_arr['sort_column']) && $filter_arr['sort_column']=='leads.creation_date' && $filter_arr['sort_val']=='DESC')
                                                <a class="text-white" href="{{request()->fullUrlWithQuery(['sort_column'=>'leads.creation_date','sort_direction'=>'ASC'])}}">Creation Date&emsp;<i class="fas fa-sort-down"></i></a>
                                            @elseif(isset($filter_arr['sort_column']) && $filter_arr['sort_column']=='leads.creation_date' && $filter_arr['sort_val']=='ASC')
                                                <a class="text-white" href="{{request()->fullUrlWithQuery(['sort_column'=>'leads.creation_date','sort_direction'=>'DESC'])}}">Creation Date&emsp;<i class="fas fa-sort-up"></i></a>
                                            @else
                                                <a class="text-white" href="{{request()->fullUrlWithQuery(['sort_column'=>'leads.creation_date','sort_direction'=>'ASC'])}}">Creation Date&emsp;<i class="fas fa-sort"></i></a>
                                            @endif
                                        </th>
                                        @if(isset($filter_arr['lead_status']) && $filter_arr['lead_status']=='Order Generated')
                                            <th scope="col">Order ID</th>
                                        @endif
                                        <th scope="col" class="col-auto">Action</th>
                                        <th scope="col">
                                            @if(isset($filter_arr['sort_column']) && $filter_arr['sort_column']=='customer_details.customer_name' && $filter_arr['sort_val']=='DESC')
                                                <a class="text-white" href="{{request()->fullUrlWithQuery(['sort_column'=>'customer_details.customer_name','sort_direction'=>'ASC'])}}">Customer Name&emsp;<i class="fas fa-sort-down"></i></a>
                                            @elseif(isset($filter_arr['sort_column']) && $filter_arr['sort_column']=='customer_details.customer_name' && $filter_arr['sort_val']=='ASC')
                                                <a class="text-white" href="{{request()->fullUrlWithQuery(['sort_column'=>'customer_details.customer_name','sort_direction'=>'DESC'])}}">Customer Name&emsp;<i class="fas fa-sort-up"></i></a>
                                            @else
                                                <a class="text-white" href="{{request()->fullUrlWithQuery(['sort_column'=>'customer_details.customer_name','sort_direction'=>'ASC'])}}">Customer Name&emsp;<i class="fas fa-sort"></i></a>
                                            @endif
                                        </th>
                                        <th scope="col">Mobile No</th>
                                        <th scope="col">Products</th>
                                        <th scope="col">
                                            @if(isset($filter_arr['sort_column']) && $filter_arr['sort_column']=='customer_details.location' && $filter_arr['sort_val']=='DESC')
                                                <a class="text-white" href="{{request()->fullUrlWithQuery(['sort_column'=>'customer_details.location','sort_direction'=>'ASC'])}}">Location&emsp;<i class="fas fa-sort-down"></i></a>
                                            @elseif(isset($filter_arr['sort_column']) && $filter_arr['sort_column']=='customer_details.location' && $filter_arr['sort_val']=='ASC')
                                                <a class="text-white" href="{{request()->fullUrlWithQuery(['sort_column'=>'customer_details.location','sort_direction'=>'DESC'])}}">Location&emsp;<i class="fas fa-sort-up"></i></a>
                                            @else
                                                <a class="text-white" href="{{request()->fullUrlWithQuery(['sort_column'=>'customer_details.location','sort_direction'=>'ASC'])}}">Location&emsp;<i class="fas fa-sort"></i></a>
                                            @endif
                                        </th>
                                        <th scope="col">
                                            @if(isset($filter_arr['sort_column']) && $filter_arr['sort_column']=='leads.lead_status' && $filter_arr['sort_val']=='DESC')
                                                <a class="text-white" href="{{request()->fullUrlWithQuery(['sort_column'=>'leads.lead_status','sort_direction'=>'ASC'])}}">Status&emsp;<i class="fas fa-sort-down"></i></a>
                                            @elseif(isset($filter_arr['sort_column']) && $filter_arr['sort_column']=='leads.lead_status' && $filter_arr['sort_val']=='ASC')
                                                <a class="text-white" href="{{request()->fullUrlWithQuery(['sort_column'=>'leads.lead_status','sort_direction'=>'DESC'])}}">Status&emsp;<i class="fas fa-sort-up"></i></a>
                                            @else
                                                <a class="text-white" href="{{request()->fullUrlWithQuery(['sort_column'=>'leads.lead_status','sort_direction'=>'ASC'])}}">Status&emsp;<i class="fas fa-sort"></i></a>
                                            @endif
                                        </th>
                                        <th scope="col">Lead Source</th>
                                        <th scope="col">
                                            @if(isset($filter_arr['sort_column']) && $filter_arr['sort_column']=='user.username' && $filter_arr['sort_val']=='DESC')
                                                <a class="text-white" href="{{request()->fullUrlWithQuery(['sort_column'=>'user.username','sort_direction'=>'ASC'])}}">Lead Owner&emsp;<i class="fas fa-sort-down"></i></a>
                                            @elseif(isset($filter_arr['sort_column']) && $filter_arr['sort_column']=='user.username' && $filter_arr['sort_val']=='ASC')
                                                <a class="text-white" href="{{request()->fullUrlWithQuery(['sort_column'=>'user.username','sort_direction'=>'DESC'])}}">Lead Owner&emsp;<i class="fas fa-sort-up"></i></a>
                                            @else
                                                <a class="text-white" href="{{request()->fullUrlWithQuery(['sort_column'=>'user.username','sort_direction'=>'ASC'])}}">Lead Owner&emsp;<i class="fas fa-sort"></i></a>
                                            @endif
                                        </th>
                                        
                                        <th scope="col">Comment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($get_all_leads as $key => $lead)
                                        <tr style=" @if($lead->lead_status=='Work In Process')
                                                        background-color:#8fffa8;
                                                    @elseif($lead->lead_status!='Converted' && $lead->lead_status!='Work In Process' && $lead->lead_status!='Order Generated')
                                                        background-color:#fdedbe;
                                                    @elseif($lead->lead_status=='Converted')
                                                        background-color:#adfffb;
                                                    @else
                                                        background-color:#fdedbe;
                                                    @endif ">
                                            {{-- <td>{{$get_all_leads->firstItem()+$loop->index}}</td> --}}
                                            <td>
                                                {{date('d-M-Y',strtotime($lead->creation_date))}}
                                            </td>
                                                @if(isset($filter_arr['lead_status']) && $filter_arr['lead_status']=='Order Generated')
                                                <td>{{$lead->order_id}}</td>                                            
                                            @endif
                                            <td scope="row" class="text-nowrap" >
                                                <button id="actionButton_list{{$key}}" type="button" class="btn btn-outline-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-tools"></i>
                                                  </button>
                                                <div class="dropdown-menu" aria-labelledby="actionButton_list{{$key}}">
                                                    <a class="dropdown-item" href="{{url('/')}}/leads_view_lead/{{$lead->customer_id}}/{{$lead->id}}">
                                                        <i class="fa fa-eye " aria-hidden="true"></i>&emsp;View lead</a>
                                                    @if($lead->lead_status=='Work In Process')
                                                        <a class="dropdown-item" href="{{url('/')}}/convert_lead/{{$lead->cust_id}}/{{$lead->id}}">
                                                            <i class="far fa-check-circle"></i>&emsp;Convert Lead</a>
                                                    @endif
                                                    @if($lead->lead_status=='Work In Process' || $lead->lead_status=='Converted')
                                                        <a class="dropdown-item" href="{{url('/')}}/edit_lead/{{$lead->cust_id}}/{{$lead->id}}">
                                                            <i class="fas fa-edit"></i>&emsp;Edit Lead</a>
                                                    @endif
                                                    @if($lead->lead_status=='Converted')
                                                        <a class="dropdown-item update-status"id="updateStatus{{$lead->id}}" href="#" data-lead_id = "{{$lead->id}}" data-cust_id = "{{$lead->cust_id}}"><i class="far fa-edit"></i>&emsp;Update Status</a>
                                                    @endif
                                                    @if($lead->lead_status=='Converted' || $lead->lead_status=='Order Generated')
                                                    {{-- <a class="btn btn-outline-info btn-sm" data-tooltip="tooltip" data-id="{{$lead->id}}" data-placement="bottom" title="Copy Details"><i class="fas fa-copy"></i></a> --}}
                                                        <a class="dropdown-item" data-toggle="modal" id="{{$lead->id}}" data-target="#modal_copy_details{{$key}}" href="#modal_copy_details{{$key}}">
                                                        <i class="far fa-copy"></i>&emsp;Copy Details</a>
                                                        <a class="dropdown-item" data-toggle="modal" data-target="#modal_sent_challan{{$key}}" href="#modal_sent_challan{{$key}}" onClick="reloadFrame({{$key}});">
                                                            <i class="far fa-envelope"></i>&emsp;Sent Challan</a>
                                                    @endif
                                                    @if($lead->lead_status=='Work In Process')
                                                        <a class="dropdown-item" data-toggle="modal" data-target="#modal_add_comment{{$key}}" href="#modal_add_comment{{$key}}">
                                                            <i class="far fa-comments"></i>&emsp;Add Comment</a>
                                                        <a class="dropdown-item" data-toggle="modal" data-target="#modal_close_lead{{$key}}" href="#modal_close_lead{{$key}}">
                                                            <i class="far fa-window-close"></i>&emsp;Close Lead</a>
                                                    @endif
                                                    @if($lead->lead_status != "Work In Process" && $lead->lead_status != "Converted" && $lead->lead_status != "Order Generated")
                                                        <a class="dropdown-item" href="{{url('/')}}/convert_lead/{{$lead->cust_id}}/{{$lead->id}}">
                                                        <i class="far fa-check-circle"></i>&emsp;Convert Lead</a>
                                                    @endif
                                                </div>

                                                {{--modal popup for challan send and view--}}
                                                <div class="modal fade" id="modal_sent_challan{{$key}}" tabindex="-1" role="dialog" aria-labelledby="sent_challlan" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                            <h5 class="modal-title" id="sent_challlan">Delivery Challan</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <form class="form" method="post" action="{{url('/')}}/sent_challan">
                                                                {{csrf_field()}}
                                                                <div class="modal-body">
                                                                    <div class="input-group mb-3">
                                                                        <div class="input-group-prepend">
                                                                            <span class="input-group-text" id="inputGroup-sizing-default">Email ID</span>
                                                                        </div>
                                                                        <input type="text" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default" name="modal_email_id" value="{{$lead->email_id}}">
                                                                    </div>
                                                                    {{-- <embed src="{{url('/')}}/{{$lead->delivery_challan}}" id="modal_chllan_id" name="modal_chllan_id" frameborder="0" width="100%" height="400px"> --}}
                                                                    <iframe src="{{url('/')}}/{{$lead->delivery_challan}}" id="frame_challan{{$key}}" name="frame_challan" frameborder="0" width="100%" height="400px"></iframe>
                                                                    <input type="hidden" name="modal_lead_id" value="{{$lead->id}}">
                                                                    <input type="hidden" name="modal_cust_id" value="{{$lead->cust_id}}">
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="submit" class="btn btn-outline-success">Send</button>    
                                                                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{--modal popup for Copy Details and view--}}
                                                <div class="modal fade" id="modal_copy_details{{$key}}" tabindex="-1" role="dialog" aria-labelledby="copy_details" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                            <h5 class="modal-title" id="copy_details">Lead Details</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="modal_lead_id" value="{{$lead->id}}">                                                                  <input type="hidden" name="modal_cust_id" value="{{$lead->cust_id}}">
<pre class="" rows="15" cols="5" id="copy_text{{$key}}">
@if($lead->lead_status=='Converted' || $lead->lead_status=='Order Generated')
Customer Name : {{$lead->customer_name}}
Mobile No: {{$lead->primary_contact_no}}
Patient Name: {{$lead->patient_name}}
Patient Age: {{$lead->patient_age}}
@php
    $products = $json_decode_all_leads['data'][$key]['product_name_arr'];
    // $del_dates = json_decode($lead->del_date);
    // $qtys = json_decode($lead->equipment_qty);
    // $deposites = json_decode($lead->deposite);
    // $offered_rents = json_decode($lead->offered_rent);
    // $deposites_total = json_decode($lead->deposite_total);
    // $offered_rents_total = json_decode($lead->offered_rent_total);
    // $transports = json_decode($lead->transport);
    // $sale_rental = json_decode($lead->sale_rental);
    $total = 0;
if(isset($lead->del_date)){
    $del_dates = json_decode($lead->del_date);
}else{
    $del_dates = 0;
}
if(isset($lead->equipment_qty)){
    $qtys = json_decode($lead->equipment_qty);    
}else{
    $qtys = [0];
}
if(isset($lead->deposite)){
    $deposites = json_decode($lead->deposite);
}else{
    $deposites = array();
}
if(isset($lead->offered_rent)){
    $offered_rents = json_decode($lead->offered_rent);
}else{
    $offered_rents = 0;
}
if(isset($lead->deposite_total)){
    $deposites_total = json_decode($lead->deposite_total);
}else{
    $deposites_total = [0];
}if(isset($lead->offered_rent_total)){
    $offered_rents_total = json_decode($lead->offered_rent_total);
}else{
    $offered_rents_total = [0];    
}
if(isset($lead->transport)){
    $transports = json_decode($lead->transport);
}else{
    $transports = [0];
}
if(isset($lead->sale_rental)){
    $sale_rental = json_decode($lead->sale_rental);
}else{
    $sale_rental = [null] ;
}
    // print_r($products);
@endphp
@for($i=0; $i <count($products); $i++)
{{!$total = $total + $deposites_total[$i] + $offered_rents_total[$i] + $transports[$i]}}
Product Name: {{$products[$i]['product_name']}}
Qty: {{$qtys[$i]}}
@if($sale_rental[$i] == 'Rental')
Deposit: {{$deposites_total[$i]}}
Rent: {{$offered_rents_total[$i]}}
@elseif($sale_rental[$i] == 'Sale')
Sale: {{$offered_rents_total[$i]}}
@endif
Transport: {{$transports[$i]}}
@endfor
-----------------
Total: {{$total}}
Payment Mode : {{$lead->payment_mode}}

Address: {{$lead->address_line_1.", ".$lead->address_line_2.", ".$lead->area.", ".$lead->landmark.", ".$lead->city."-".$lead->pincode}}
Email: {{$lead->email_id}}

Delivery: {{date('d-m-Y',strtotime($lead->creation_date))}}
@endif
</pre>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-outline-success btn_copy" data-clipboard-target="#copy_text{{$key}}">Copy</button>    
                                                                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{--modal popup for challan send and view--}}
                                                <div class="modal fade" id="modal_add_comment{{$key}}" tabindex="-1" role="dialog" aria-labelledby="add_comment" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="add_comment">Add Comment</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <form class="form" method="post" action="{{url('/')}}/leads_add_comment/{{$lead->id}}">
                                                                @csrf
                                                                <div class="modal-body">
                                                                    <textarea class="form-control" rows="5" name="comments" id="comments"></textarea>
                                                                    <input type="hidden" name="previous_url" value="{{url()->previous()}}">
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="submit" class="btn btn-success" id="add_comment" title="add _comment">Submit</button>
                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{--modal popup for challan send and view--}}
                                                <div class="modal fade" id="modal_close_lead{{$key}}" tabindex="-1" role="dialog" aria-labelledby="close_lead" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="close_lead">Close Lead</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <form class="form" method="post" action="{{url('/')}}/close_lead/{{$lead->id}}">
                                                                @csrf
                                                                <div class="modal-body">
                                                                    <select class="form-control" id="reasons" name="reason" required>
                                                                        <option disabled selected>--Select Reason*--</option>
                                                                        <option>Not interested</option>
                                                                        <option>Ringing</option>
                                                                        <option>Not required</option>
                                                                        <option>Will Confirm Later</option>
                                                                        <option>Mobile Off</option>
                                                                    </select>
                                                                    <label for="desc">Remark</label>
                                                                    <textarea class="form-control" rows="5" name="desc" id="desc"></textarea>
                                                                    <input type="hidden" name="previous_url" value="{{url()->previous()}}">
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="submit" class="btn btn-secondary" id="close_lead" title="Close"><i class="fas fa-window-close"></i> Close lead</button>
                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{$lead->customer_name}}</td>
                                            <td>{{$lead->primary_contact_no}}</td>
                                            <td>{{$json_decode_all_leads['data'][$key]['product_name']}}</td>
                                            <td>{{$lead->location}}</td>
                                            <td>{{$lead->lead_status}}</td>
                                            <td>{{$lead->lead_source}}</td>
                                            <td>{{$lead->lead_owner}}</td>
                                            
                                            
                                            <td scope="row" class="text-nowrap">
                                                @if(isset($lead->lead_comment))
                                                    {{substr($lead->lead_comment,'0','10')}}<span class="btn btn-default" href="#" data-tooltip="tooltip" data-placement="bottom" title="View More" data-toggle="modal" data-target="#modal_view_more{{$key}}"> ...</span>
                                                    {{--modal popup for View more comment--}}
                                                    <div class="modal fade" id="modal_view_more{{$key}}" tabindex="-1" role="dialog" aria-labelledby="view_more_comment" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="view_more_comment">Comments</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <pre>{{$lead->lead_comment}}</pre>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @php
                                $append_arr = array();
                                if(isset($filter_arr['cust_name'])){
                                    $append_arr['filter_customer_name'] = $filter_arr['cust_name'];
                                }
                                if(isset($filter_arr['cust_no'])){
                                    $append_arr['filter_contact_no'] = $filter_arr['cust_no'];
                                }
                                if(isset($filter_arr['lead_status'])){
                                    $append_arr['filter_lead_status'] = $filter_arr['lead_status'];
                                }
                                if(isset($filter_arr['lead_source'])){
                                    $append_arr['filter_lead_source'] = $filter_arr['lead_source'];
                                }
                                if(isset($filter_arr['location'])){
                                    $append_arr['filter_customer_location'] = $filter_arr['location'];
                                }
                                if(isset($filter_arr['lead_owner'])){
                                    $append_arr['filter_lead_owner'] = $filter_arr['lead_owner'];
                                }
                                if(isset($filter_arr['from_date'])){
                                    $append_arr['filter_from_date'] = $filter_arr['from_date'];
                                }
                                if(isset($filter_arr['end_date'])){
                                    $append_arr['filter_end_date'] = $filter_arr['end_date'];
                                }
                                if(isset($filter_arr['sort_column']) && isset($filter_arr['sort_val'])){
                                    $append_arr['sort_column'] = $filter_arr['sort_column'];
                                    $append_arr['sort_direction'] = $filter_arr['sort_val'];
                                }
                            @endphp
                            {{$get_all_leads->appends($append_arr)->links('Custom.Pagination.pagination')}}
                        {{-- </div>
                    </div> --}}
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal_update_status" tabindex="-1" role="dialog" aria-labelledby="update_status" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="update_status">Update Lead Status</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    {{-- <form class="form" method="post" action="{{url('/')}}/updateStatus">
                        {{csrf_field()}} --}}
                        <div class="modal-body">
                            <input type="hidden" name="update_status_lead_id" id="update_status_lead_id">
                            <input type="hidden" name="update_status_cust_id" id="update_status_cust_id">
                            <center>
                                <select class="selectpicker border border-dark rounded" id="status" title="Select Status" placeholder="Select Status" required width="100%">
                                    <option value="Work In Process">In Pocess</option>
                                    <option value="Closed">Closed</option>
                                </select>
                                <span id="req" style="color:red; display:none;">*Required</span>
                            </center>
                            </br>
                            <label for="update_status_comment">Comment</label>
                            <textarea class="form-control" name="update_status_comment" id="update_status_comment" cols="30" rows="10" required></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" id="submit_status" class="btn btn-outline-success">Update</button>    
                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                        </div>
                    {{-- </form> --}}
                </div>
            </div>
        </div>

    @endsection
    @section('script')
        @if(request()->routeIs('view_all_leads') ? 'active' : '')
            <script>
                    $.removeCookie('filter_collapse_js');
            </script>
        @endif
        <script>

            $('.update-status').on('click', function(){
                let lead_id = $(this).data("lead_id");
                let cust_id = $(this).data("cust_id");

                // alert(lead_id+" :"+": "+cust_id);
                $('#modal_update_status').modal('show');
                $('#update_status_lead_id').val(lead_id);
                $('#update_status_cust_id').val(cust_id);
            });

            $('#submit_status').on('click', function(){
                let lead_id = $('#update_status_lead_id').val();
                let cust_id = $('#update_status_cust_id').val();
                let status = $('#status').val();
                let comment = $('#update_status_comment').val();
                // alert(lead_id);
                if(status.length <=0)
                {
                    $('#status').attr('style','borderColor : red');
                    $('#req').show();
                    // $('#update_status_comment').attr('style','border:dashed 2px red;');
                }
                else if(comment.length <=0)
                {
                    $('#req').hide();
                    $('#update_status_comment').attr('style','border:dashed 2px red;');
                }
                else
                {
                    $('#status').attr('style','border:dashed 2px green;');
                    $('#update_status_comment').attr('style','border:dashed 2px green;');
                    var dataString = ({_token:"{{ csrf_token() }}",lead_id:""+lead_id,cust_id:""+cust_id,status:""+status,comment:""+comment});
                    $.ajax({
                        type: "POST",
                        url: "{{url('/')}}/updateStatus",
                        data: dataString,
                        cache:false,
                        success: function (data)
                        {
                            console.log(data);
                            location.reload();
                        }
                    });
                }
            });
            $(function () {
                $('[data-tooltip="tooltip"]').tooltip()
            });
            //-----date validateion----//
            $('#input_from_date').on('change',function(){
                let start_date = this.value;
                $('#input_end_date').attr('min',start_date);
            });
            $('#input_end_date').on('change',function(){
                let end_date = this.value;
                $('#input_from_date').attr('max',end_date);
            });
            //---btn clear filter all clear--..
            $('#btn_clear').on('click',function(){
                $.cookie("filter_collapse_js", "Yes");
                var url="<?php echo url('/');?>/view_all_leads";
                window.location.href = url;
            });
            //----destroy cookie----//
            $('#heading-filter').on('click',function(){
                $.removeCookie('filter_collapse_js');
            });

            //---pop up customer --//
            var route = "{{ url('complaint_customers_populate') }}";
            $('#txt_filter_customer_name').typeahead({ 
                source: function (query, process) {
                    return $.get(route, {
                        query: query
                    }, function (data) {
                        return process(data);
                    });
                }
            });

            //visible order id
            $('#select_filter_lead_status').on('change', function(){
                if(this.value=='Order Generated')
                {
                    $('#div_order_id').css('display', 'block');
                }
                else
                {
                    $('#div_order_id').css('display', 'none');
                }
            });
            
            $('#select_filter_all_prodcuts').select2({
                placeholder: 'Select Products',
                allowClear: true,
            });

            $('.btn_copy').tooltip({
                trigger: 'click',
                placement: 'bottom'
            });

            var clipboard = new ClipboardJS('.btn_copy');
            clipboard.on('success', function(e) {
                // console.info('Action:', e.action);
                // console.info('Text:', e.text);
                // console.info('Trigger:', e.trigger);
                
                setTooltip('Copied!');
                hideTooltip();
                //e.clearSelection();
            });

            clipboard.on('error', function(e) {
                // console.error('Action:', e.action);
                // console.error('Trigger:', e.trigger);
                setTooltip('Failed!');
                hideTooltip();      
            });

            // $(".btn_copy").click(function(){
            //     let id = $(this).data("id");
            //     console.log(id);
            //     const copyText = document.getElementById("copy_text"+id).textContent;
            //     console.log(copyText);
            //     const textArea = document.createElement('textarea');
            //     textArea.textContent = copyText;
            //     document.body.append(textArea);
            //     textArea.select();
            //     document.execCommand("copy");
            //     textArea.remove();
            // });
            function reloadFrame(id)
            {
                document.getElementById('frame_challan'+id).contentDocument.location.reload(true);
            }
        </script>
    @endsection
</body>
</html>
