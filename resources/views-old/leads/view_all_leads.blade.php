@extends('header_and_sidebar')

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
        <br>
            <div class="card" id="filter_card">
                <div class="card-header border-primary" id="filter_card">
                    {{-- <button class="btn btn-link btn-sm collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        All Leads
                    </button> --}}
                    <div class="row">
                        <div class="col text-primary" data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                            <strong>All Leads</strong>
                        </div>
                        <div class="col-auto">
                            <a data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                                <i class="fa fa-chevron-down pull-right"></i>&emsp;<b>Filter</b>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body border-primary collapse" id="filter-collapse" aria-labelledby="headingTwo" data-parent="#filter_card">
                    <form action="{{url('/')}}/view_all_leads" method="GET" id="all_leads_form">
                        @csrf
                        <div class="row">
                            <div class="col-md-10">
                                <div class="row">
                                    <div class="col-md-7">
                                        <div class="row">
                                            <div class="col-md-4 ">
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
                                            <div class="col-md-4 ">
                                                <label for="contact_no"><strong>Contact No :</strong></label>
                                            </div>
                                            <div class="col-md-8 ">
                                                <input type="text" class="form-control" name="filter_contact_no"  id="txt_filter_contact_no" placeholder="Contact No..."
                                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" 
                                                    value="@if(isset($filter_arr['cust_no'])){{$filter_arr['cust_no']}}@endif">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-4 ">
                                                <label for="patient_name"><strong>Patient Name :</strong></label>
                                            </div>
                                            <div class="col-md-8 ">
                                                <input type="text" class="form-control form-control-sm" name="filter_patient_name"  id="txt_filter_patient_name" placeholder="Patient name..."
                                                    value="@if(isset($filter_arr['patient_name'])){{$filter_arr['patient_name']}}@endif">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="row">
                                            <div class="col-md-3 ">
                                                From
                                            </div>
                                            <div class="col-md-9">
                                                <input type="date" name="filter_from_date" id="input_from_date" class="form-control" value="@if(isset($filter_arr['from_date'])){{$filter_arr['from_date']}}@endif">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-3 ">
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
                                            <div class="col-md-4 ">
                                                <label for="lead_status"><strong>Status :</strong></label>
                                            </div>
                                            <div class="col-md-8 ">
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
                                            <div class="col-md-3 ">
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
                                            <div class="col-md-4 ">
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
                                            <div class="col-md-4 ">
                                                <label for="lead source"><strong>Source :</strong></label>
                                            </div>
                                            <div class="col-md-8 ">
                                                <select class="select form-control selectpicker border" name="filter_lead_source[]" id="select_filter_lead_source" title="Lead source" multiple="multiple"
                                                    data-live-search="true" data-size="5">
                                                    {{-- <option value="All" selected>All</option> --}}
                                                    @foreach($get_lead_sources as $key=>$lead_sources)
                                                        <option value="{{$lead_sources}}" @if(isset($filter_arr['lead_source']) && in_array($lead_sources,$filter_arr['lead_source'])) selected @endif>{{$lead_sources}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-4 ">
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
                                                <div class="col-md-4 ">
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
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-4 ">
                                                    <label for="contact_no"><strong>City :</strong></label>
                                                </div>
                                                <div class="col-md-8 ">
                                                    <select class="select form-control form-control-sm selectpicker border" name="filter_city" id="select_filter_city" title="Select City"
                                                    data-size="5" data-live-search="true">
                                                        <option value="All" @if(request()->get('filter_city')=='All') selected @endif>All</option>
                                                        @foreach ($cities as $key=>$city)
                                                            <option value="{{$city->citygroup}}" @if(request()->get('filter_city')==$city->citygroup) selected @endif>{{$city->citygroup}}</option>
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
                                        <button type="submit" class="btn btn-sm btn-outline-success btn-block" name="btn_submit" value="Submit">Submit</button>
                                    </div>
                                </div>
                                <br>
                                {{-- <div class="row">
                                    <div class="col">
                                        <a class="btn btn-sm btn-outline-primary btn-sm btn-block" href="{{url('/')}}/create_lead">Create New Lead</a>
                                    </div>
                                </div>
                                <br> --}}
                                <div class="row">
                                    <div class="col">
                                        <button type="submit" class="btn btn-sm btn-outline-success btn-block" name="btn_submit" value="Export">Export to Excel</button>
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
                <div class="table-responsive jim-table-responsive">

                    <table id="" class="table table-hover table-sm table-flush">
                        <thead class="thead thead-light text-dark border-primary">
                            <tr class="text-nowrap border-primary">
                                <th scope="col">
                                    @if(isset($filter_arr['sort_column']) && $filter_arr['sort_column']=='leads.creation_date' && $filter_arr['sort_val']=='DESC')
                                        <a class="text-dark" href="{{request()->fullUrlWithQuery(['sort_column'=>'leads.creation_date','sort_direction'=>'ASC'])}}">Cr Date&emsp;<i class="fas fa-sort-down"></i></a>
                                    @elseif(isset($filter_arr['sort_column']) && $filter_arr['sort_column']=='leads.creation_date' && $filter_arr['sort_val']=='ASC')
                                        <a class="text-dark" href="{{request()->fullUrlWithQuery(['sort_column'=>'leads.creation_date','sort_direction'=>'DESC'])}}">Cr Date&emsp;<i class="fas fa-sort-up"></i></a>
                                    @else
                                        <a class="text-dark" href="{{request()->fullUrlWithQuery(['sort_column'=>'leads.creation_date','sort_direction'=>'ASC'])}}">Cr Date&emsp;<i class="fas fa-sort"></i></a>
                                    @endif
                                </th>
                                <th>
                                    Converted Date
                                </th>
                                @if(isset($filter_arr['lead_status']) && $filter_arr['lead_status']=='Order Generated')
                                    <th scope="col">Order ID</th>
                                @endif
                                <th scope="col" class="col-auto">Action</th>
                                <th scope="col">
                                    @if(isset($filter_arr['sort_column']) && $filter_arr['sort_column']=='customer_details.customer_name' && $filter_arr['sort_val']=='DESC')
                                        <a class="text-dark" href="{{request()->fullUrlWithQuery(['sort_column'=>'customer_details.customer_name','sort_direction'=>'ASC'])}}">Customer Name&emsp;<i class="fas fa-sort-down"></i></a>
                                    @elseif(isset($filter_arr['sort_column']) && $filter_arr['sort_column']=='customer_details.customer_name' && $filter_arr['sort_val']=='ASC')
                                        <a class="text-dark" href="{{request()->fullUrlWithQuery(['sort_column'=>'customer_details.customer_name','sort_direction'=>'DESC'])}}">Customer Name&emsp;<i class="fas fa-sort-up"></i></a>
                                    @else
                                        <a class="text-dark" href="{{request()->fullUrlWithQuery(['sort_column'=>'customer_details.customer_name','sort_direction'=>'ASC'])}}">Customer Name&emsp;<i class="fas fa-sort"></i></a>
                                    @endif
                                </th>
                                <th>Patient Name</th>
                                <th scope="col">Mobile No</th>
                                <th scope="col">Products</th>
                                <th scope="col">
                                    @if(isset($filter_arr['sort_column']) && $filter_arr['sort_column']=='customer_details.location' && $filter_arr['sort_val']=='DESC')
                                        <a class="text-dark" href="{{request()->fullUrlWithQuery(['sort_column'=>'customer_details.location','sort_direction'=>'ASC'])}}">Location&emsp;<i class="fas fa-sort-down"></i></a>
                                    @elseif(isset($filter_arr['sort_column']) && $filter_arr['sort_column']=='customer_details.location' && $filter_arr['sort_val']=='ASC')
                                        <a class="text-dark" href="{{request()->fullUrlWithQuery(['sort_column'=>'customer_details.location','sort_direction'=>'DESC'])}}">Location&emsp;<i class="fas fa-sort-up"></i></a>
                                    @else
                                        <a class="text-dark" href="{{request()->fullUrlWithQuery(['sort_column'=>'customer_details.location','sort_direction'=>'ASC'])}}">Location&emsp;<i class="fas fa-sort"></i></a>
                                    @endif
                                </th>
                                <th scope="col">
                                    @if(isset($filter_arr['sort_column']) && $filter_arr['sort_column']=='leads.lead_status' && $filter_arr['sort_val']=='DESC')
                                        <a class="text-dark" href="{{request()->fullUrlWithQuery(['sort_column'=>'leads.lead_status','sort_direction'=>'ASC'])}}">Status&emsp;<i class="fas fa-sort-down"></i></a>
                                    @elseif(isset($filter_arr['sort_column']) && $filter_arr['sort_column']=='leads.lead_status' && $filter_arr['sort_val']=='ASC')
                                        <a class="text-dark" href="{{request()->fullUrlWithQuery(['sort_column'=>'leads.lead_status','sort_direction'=>'DESC'])}}">Status&emsp;<i class="fas fa-sort-up"></i></a>
                                    @else
                                        <a class="text-dark" href="{{request()->fullUrlWithQuery(['sort_column'=>'leads.lead_status','sort_direction'=>'ASC'])}}">Status&emsp;<i class="fas fa-sort"></i></a>
                                    @endif
                                </th>
                                <th scope="col">Lead Source</th>
                                <th scope="col">
                                    @if(isset($filter_arr['sort_column']) && $filter_arr['sort_column']=='user.username' && $filter_arr['sort_val']=='DESC')
                                        <a class="text-dark" href="{{request()->fullUrlWithQuery(['sort_column'=>'user.username','sort_direction'=>'ASC'])}}">Lead Owner&emsp;<i class="fas fa-sort-down"></i></a>
                                    @elseif(isset($filter_arr['sort_column']) && $filter_arr['sort_column']=='user.username' && $filter_arr['sort_val']=='ASC')
                                        <a class="text-dark" href="{{request()->fullUrlWithQuery(['sort_column'=>'user.username','sort_direction'=>'DESC'])}}">Lead Owner&emsp;<i class="fas fa-sort-up"></i></a>
                                    @else
                                        <a class="text-dark" href="{{request()->fullUrlWithQuery(['sort_column'=>'user.username','sort_direction'=>'ASC'])}}">Lead Owner&emsp;<i class="fas fa-sort"></i></a>
                                    @endif
                                </th>
                                
                                {{-- <th scope="col">Comment</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($get_all_leads as $key => $lead)
                                <tr class=" @if($lead->lead_status=='Work In Process')
                                                table-warning
                                            @elseif($lead->lead_status!='Converted' && $lead->lead_status!='Work In Process' && $lead->lead_status!='Order Generated')
                                                table-danger
                                            @elseif($lead->lead_status=='Converted')
                                                table-success
                                            @else
                                                table-primary
                                            @endif text-dark">
                                            {{-- #fdedbe; --}}
                                    {{-- <td>{{$get_all_leads->firstItem()+$loop->index}}</td> --}}
                                    <td data-label="Creation Date" class="text-nowrap">
                                        <small>{{date('d-M-y',strtotime($lead->creation_date))}} {{date('h:i A',strtotime($lead->created_at))}}</small>
                                    </td>
                                    <td data-label="Converted Date" class="text-nowrap">
                                        <small>{{date('d-M-y',strtotime($lead->converted_at))}} {{date('h:i A',strtotime($lead->converted_at))}}</small>
                                    </td>
                                    @if(isset($filter_arr['lead_status']) && $filter_arr['lead_status']=='Order Generated')
                                        <td><small>{{$lead->order_id}}</small></td>                                            
                                    @endif
                                    <td data-label="Action" scope="row" class="text-nowrap" >
                                        <button id="actionButton_list{{$key}}" type="button" class="btn btn-outline-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-tools"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="actionButton_list{{$key}}">
                                            <a class="dropdown-item" href="{{url('/')}}/leads_view_lead/{{$lead->customer_id}}/{{$lead->id}}">
                                                <i class="fa fa-eye " aria-hidden="true"></i>&emsp;View lead</a>
                                            {{-- @if($lead->lead_status=='Work In Process')
                                                <a class="dropdown-item" href="{{url('/')}}/convert_lead/{{$lead->cust_id}}/{{$lead->id}}">
                                                    <i class="far fa-check-circle"></i>&emsp;Convert Lead</a>
                                            @endif --}}
                                            @if($lead->lead_status=='Converted')
                                                <a class="dropdown-item" href="{{url('/')}}/edit_lead/{{$lead->cust_id}}/{{$lead->id}}">
                                                    <i class="fas fa-edit"></i>&emsp;Edit Lead</a>
                                            @endif
                                            @if($lead->lead_status=='Converted')
                                                <a class="dropdown-item update-status"id="updateStatus{{$lead->id}}" href="#" data-lead_id = "{{$lead->id}}" data-cust_id = "{{$lead->cust_id}}" data-web_lead_id = "{{$lead->web_order_id}}"><i class="far fa-edit"></i>&emsp;Update Status</a>
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
                                            {{-- @if($lead->lead_status != "Work In Process" && $lead->lead_status != "Converted" && $lead->lead_status != "Order Generated")
                                                <a class="dropdown-item" href="{{url('/')}}/convert_lead/{{$lead->cust_id}}/{{$lead->id}}">
                                                <i class="far fa-check-circle"></i>&emsp;Convert Lead</a>
                                            @endif --}}
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
                                                                <input type="text" class="form-control form-control-sm" aria-label="Default" aria-describedby="inputGroup-sizing-default" name="modal_email_id" value="{{$lead->email_id}}">
                                                            </div>
                                                            {{-- <embed src="{{url('/')}}/{{$lead->delivery_challan}}" id="modal_chllan_id" name="modal_chllan_id" frameborder="0" width="100%" height="400px"> --}}
                                                            <iframe src="{{url('/')}}/{{$lead->delivery_challan}}" id="frame_chllan{{$key}}" name="frame_chllan" frameborder="0" width="100%" height="400px"></iframe>
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
    Transport: {{$transports[$i]}}
    @elseif($sale_rental[$i] == 'Sale')
Sale: {{$offered_rents_total[$i]}}
    Transport: {{$transports[$i]}}
    @endif
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
                                                            <button type="button" class="btn btn-outline-success" onclick="sendOnWp({{$key}})">Share</button>    
                                                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
    
                                        {{--modal comment add view--}}
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
                                                            <textarea class="form-control form-control-sm" rows="5" name="comments" id="comments"></textarea>
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
    
                                        {{--modal closed lead view--}}
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
                                                            <select class="form-control form-control-sm" id="reasons" name="reason" required>
                                                                <option disabled selected>--Select Reason*--</option>
                                                                <option>Not interested</option>
                                                                <option>Ringing</option>
                                                                <option>Not required</option>
                                                                <option>Will Confirm Later</option>
                                                                <option>Mobile Off</option>
                                                            </select>
                                                            <label for="desc">Remark</label>
                                                            <textarea class="form-control form-control-sm" rows="5" name="desc" id="desc"></textarea>
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
                                    <td data-label="Customer Name"><small>{{$lead->customer_name}}</small></td>
                                    <td data-label="Patient Name"><small>{{$lead->patient_name}}</small></td>
                                    <td data-label="Contact No"><small>{{$lead->primary_contact_no}}</small></td>
                                    <td data-label="Products" class="text-nowrap">
                                        <small>
                                            {{$json_decode_all_leads['data'][$key]['first_product_name']}}<span class="btn btn-default btn-sm" href="#" data-tooltip="tooltip" data-placement="bottom" title="View Products" data-toggle="modal" data-target="#modal_view_products{{$key}}">...</span>
                                        </small>
                                        <div class="modal fade" id="modal_view_products{{$key}}" tabindex="-1" role="dialog" aria-labelledby="view_more_products" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="view_more_products">Products</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <pre>{{$json_decode_all_leads['data'][$key]['product_name']}}</pre>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    {{-- <td><small>{{$json_decode_all_leads['data'][$key]['product_name']}}</small></td> --}}
                                    <td data-label="Location"><small>{{$lead->location}}</small></td>
                                    <td data-label="Lead Status" class="text-nowrap">
                                        <small>{{$lead->lead_status}}</small><br>
                                        @if(isset($lead->lead_comment))
                                            <small>{{substr($lead->lead_comment,'0','10')}}<span class="btn btn-default" href="#" data-tooltip="tooltip" data-placement="bottom" title="View More" data-toggle="modal" data-target="#modal_view_more{{$key}}"> ...</span></small>
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
                                        @else
                                            {{-- <small>No Comments</small> --}}
                                        @endif
                                    </td>

                                    <td data-label="Lead Source"><small>{{$lead->lead_source}}</small>@if($lead->handovermode == 'pickup')<br><small class="text-danger">Self Pickup</small>@endif</td>
                                    <td data-label="Lead Owner"><small>{{$lead->lead_owner}}</small></td>
                                    {{-- <td data-label="Comment" scope="row" class="text-nowrap">
                                        @if(isset($lead->lead_comment))
                                            <small>{{substr($lead->lead_comment,'0','10')}}<span class="btn btn-default" href="#" data-tooltip="tooltip" data-placement="bottom" title="View More" data-toggle="modal" data-target="#modal_view_more{{$key}}"> ...</span></small>
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
                                        @else
                                            <small>No Comments</small>
                                        @endif
                                    </td> --}}
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
                    if(isset($filter_arr['patient_name'])){
                        $append_arr['filter_patient_name'] = $filter_arr['patient_name'];
                    }
                    if(isset($filter_arr['city'])){
                        $append_arr['filter_city'] = $filter_arr['city'];
                    }
                @endphp
                {{$get_all_leads->appends($append_arr)->links('Custom.Pagination.pagination')}}
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
                            <input type="hidden" name="update_web_order_id" id="update_web_order_id">
                            <center>
                                <select class="selectpicker border border-dark rounded select-lead-status" id="status" title="Select Status" placeholder="Select Status" required width="100%">
                                    <option value="Work In Process">In Pocess</option>
                                    <option value="Closed">Closed</option>
                                </select>
                                <select class="selectpicker border border-dark rounded select-lead-reason" style="display:none" id="reason" title="Select Reason" placeholder="Select Reason" required width="100%">
                                    @forelse ($lead_cancellation_reason as $item)
                                        <option value="{{$item}}">{{$item}}</option>
                                    @empty
                                        <option value="No" disabled>No Reasons here</option>
                                    @endforelse                           
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
        {{-- @if(request()->routeIs('view_all_leads') ? 'active' : '')
            <script>
                    $.removeCookie('filter_collapse_js');
            </script>
        @endif --}}
        <script>
            
            
            $('.update-status').on('click', function(){
                let lead_id = $(this).data("lead_id");
                let cust_id = $(this).data("cust_id");
                let web_lead_id = $(this).data("web_lead_id");
                console.log(web_lead_id);
                $(".select-lead-reason").hide();
                if(web_lead_id !=null && web_lead_id != "")
                {
                    // $(".select-lead-status")
                    //     .find("option")
                    //     .remove()
                    //     .end();
                    // $(".select-lead-status").append("<option value='Closed' selected>Closed</option>");
                    // $(".select-lead-status").selectpicker('refresh');
                }
                // modal_update_status
                // alert(lead_id+" :"+": "+cust_id);
                $('#modal_update_status').modal('show');
                $('#update_status_lead_id').val(lead_id);
                $('#update_status_cust_id').val(cust_id);
                $("#update_web_order_id").val(web_lead_id);
            });

            $(".select-lead-status").change(function(){
                if($(this).val() == 'Closed')
                {
                    $(".select-lead-reason").show();
                }
                else
                {
                    $(".select-lead-reason").hide();
                }
            });

            $('#submit_status').on('click', function(){
                let lead_id = $('#update_status_lead_id').val();
                let cust_id = $('#update_status_cust_id').val();
                let web_order_id = $("#update_web_order_id").val();
                let status = $('#status').val();
                let reason = $('#reason').val();
                let comment = $('#update_status_comment').val();
                // alert(lead_id);
                if(status.length <=0)
                {
                    $('#status').attr('style','borderColor : red');
                    $('#req').show();
                    // $('#update_status_comment').attr('style','border:dashed 2px red;');
                }
                else if(status == 'Closed' && reason.length<=0)
                {
                    $('#reason').attr('style','borderColor : red');
                    $('#req').show();
                }
                else if(comment.length <=15)
                {
                    $('#req').hide();
                    $('#update_status_comment').attr('style','border:dashed 2px red;');
                }
                else
                {
                    $('#status').attr('style','border:dashed 2px green;');
                    $('#update_status_comment').attr('style','border:dashed 2px green;');
                    var dataString = ({_token:"{{ csrf_token() }}",lead_id:""+lead_id,cust_id:""+cust_id,status:""+status,comment:""+comment,web_order_id:""+web_order_id,reason:""+reason});
                    $.ajax({
                        type: "POST",
                        url: "{{url('/')}}/updateStatus",
                        data: dataString,
                        cache:false,
                        success: function (data)
                        {
                            // console.log(data);
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

            function reloadFrame(id)
            {
                document.getElementById('frame_challan'+id).contentDocument.location.reload(true);
            }
        </script>
    @endsection
