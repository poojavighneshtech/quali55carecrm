@extends('header_and_sidebar')
<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
    
        <title>All Lab Test Leads</title>
        {{-- <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script> --}}
        @section('styles')
        <style>
            .select2 {
                width:100%!important;
            }    
            .card-header .fa {
            transition: .3s transform ease-in-out;
            }
            .card-header .collapsed .fa {
            transform: rotate(90deg);
            }
        </style>
        @endsection
    </head>

<body id="page-top">	
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
                <div class="card-header"  style="background-color: #4a6fdc; color: white;" >
                    <center>Nursing Care Leads</center>
                </div> 
                <div class="card-body">
                    <div class="card" id="filter_card">
                        {{-- <h6 id="h6_filter"><span class="border border-dark rounded bg-primary text-white">&emsp;Filter&emsp;</span></h6> --}}
                        <div class="card-header border border-primary">
                            <div class="row">
                                <div class="col" data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                                    <strong>Filter</strong>
                                </div>
                                <div class="col-auto">
                                    <a data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                                        <i class="fa fa-chevron-down pull-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body collapse @if(isset($filter_arr) && !array_filter($filter_arr)) hide @else show @endif" id="filter-collapse" aria-labelledby="heading-filter">
                            <form action="{{url('/')}}/view_all_nursing_care_leads" method="GET" id="all_lab_leads_form">
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
                                                        <label for="contact_no"><strong>Lead Status :</strong></label>
                                                    </div>
                                                    <div class="col-md-8 text-right">
                                                        <select class="select form-control selectpicker" name="filter_lead_status" id="select_filter_lead_status" title="Lead Status">
                                                            <option value="All" selected>All</option>
                                                            <option value="In Process" @if(isset($filter_arr['lead_status']) && $filter_arr['lead_status']=='In Process'){{"selected"}}@endif>In Process</option>
                                                            <option value="Converted" @if(isset($filter_arr['lead_status']) && $filter_arr['lead_status']=='Converted'){{"selected"}}@endif>Converted</option>
                                                            <option value="Closed" @if(isset($filter_arr['lead_status']) && $filter_arr['lead_status']=='Closed'){{"selected"}}@endif>Closed</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="sorting_diff" id="hid_sorting_diff" value="@if(isset($filter_arr['lead_status']) && $filter_arr['lead_status']=='Order Generated') 1 @else 0 @endif">
                                                {{-- <br>
                                                <div class="row">
                                                    <div class="col-md-4 text-right">
                                                        <label for="lead source"><strong>Ambulance Type :</strong></label>
                                                    </div>
                                                    <div class="col-md-8 text-right">
                                                        <select class="select form-control selectpicker" name="filter_ambulance_type" id="select_filter_ambulance_type" title="Ambulance type" data-size="4">
                                                            <option value="All" selected>All</option>
                                                            <option value="Cardic" @if(isset($filter_arr['ambulance_type']) && $filter_arr['ambulance_type']=='Cardic'){{"selected"}}@endif>Cardic</option>
                                                            <option value="Non Cardic" @if(isset($filter_arr['ambulance_type']) && $filter_arr['ambulance_type']=='Non Cardic'){{"selected"}}@endif>Non Cardic</option>
                                                            <option value="Covid 2019" @if(isset($filter_arr['ambulance_type']) && $filter_arr['ambulance_type']=='Covid 2019'){{"selected"}}@endif>Covid 2019</option>
                                                            <option value="Ac" @if(isset($filter_arr['ambulance_type']) && $filter_arr['ambulance_type']=='Ac'){{"selected"}}@endif>Ac</option>
                                                            <option value="Non-Ac" @if(isset($filter_arr['ambulance_type']) && $filter_arr['ambulance_type']=='Non-Ac'){{"selected"}}@endif>Non-Ac</option>
                                                           
                                                        </select>
                                                    </div>
                                                </div> --}}
                                                @if(session('role')=='superuser')
                                                    <br>
                                                    <div class="row">
                                                        <div class="col-md-4 text-right">
                                                            <strong>Lead Owner</strong>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <select class="select form-control selectpicker" name="filter_lead_owner" id="select_filter_lead_owner" title="Lead Owners"
                                                                data-live-search="true" data-size="5">
                                                                <option value="All" selected>All</option>
                                                                @foreach($get_all_users as $user)
                                                                    <option value="{{$user->id}}" @if(isset($filter_arr['lead_owner']) && $filter_arr['lead_owner']==$user->id) selected @endif>{{$user->username}}</option>
                                                                @endforeach
                                                        </select>
                                                        </div>
                                                    </div>
                                                @endif
                                               
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
                                                        Location
                                                    </div>
                                                    <div class="col-md-9">
                                                        {{-- <input type="date" name="filter_end_date" id="input_end_date" class="form-control" value="@if(isset($filter_arr['end_date'])){{$filter_arr['end_date']}}@endif"> --}}
                                                        <select class="select form-control selectpicker" name="filter_customer_location" id="select_filter_customer_location" title="Customer Location"
                                                            data-live-search="true" data-size="5">
                                                            <option value="All" selected>All</option>
                                                            @foreach($get_locations as $location)
                                                                <option value="{{$location->location}}" @if(isset($filter_arr['location']) && $location->location==$filter_arr['location'])selected @endif>{{$location->location}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                               
                                            </div>
                                        </div>
                                    
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
                                                <a class="btn btn-outline-primary btn-sm btn-block" href="{{url('/')}}/create_lead_lab">Create New Lead</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <br>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="table">
                                    <table class="table table-hover " width="100%">
                                        <thead>
                                            <tr>
                                                <th>Sr No</th>
                                                <th>Date</th>
                                                <th>Name</th>
                                                <th>Contact Name</th>
                                                <th>Location</th>
                                                <th>Service Required</th>
                                                <th>Lead Owner</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($get_all_leads as $key=> $lead) 
                                                <tr>
                                                    <td>{{$key+1}}</td>
                                                    <td>{{$lead->created_date}}</td>
                                                    <td>{{$lead->name}}</td>
                                                    <td>{{$lead->contact_no}}</td>
                                                    <td>{{$lead->location}}</td>
                                                    <td>{{$lead->service_required}}</td>
                                                    <td>{{$lead->lead_owner_name}}</td>
                                                    <td>
                                                        <span class="badge 
                                                            @if($lead->status=='Converted')
                                                                {{"badge-success"}} 
                                                            @elseif($lead->status=='Closed')
                                                                {{"badge-danger"}}
                                                            @else
                                                                {{"badge-primary"}}
                                                            @endif">
                                                            {{$lead->status}}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($lead->status=='In Process')
                                                            <button type="button" class="btn btn-outline-success" data-toggle="modal" data-target="#convert_modal">
                                                                Convert
                                                            </button>
                                                            {{--modal convert--}}
                                                            <form action="{{url('/')}}/nur_convert_lead" method="post">
                                                                @csrf
                                                                <div class="modal fade" id="convert_modal" tabindex="-1" role="dialog" aria-labelledby="convert_modalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                        <h5 class="modal-title" id="convert_modalLabel">Convert</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            Are you sure want to convert this lead
                                                                            <input type="hidden" name="lead_id" id="modal_txt_lead_id" value="{{$lead->id}}">
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                        <button type="submit" class="btn btn-outline-success">submit</button>
                                                                        </div>
                                                                    </div>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                            <button type="button" class="btn btn-outline-danger" data-toggle="modal" data-target="#close_modal">
                                                                Close
                                                            </button>
                                                            <form action="{{url('/')}}/nur_close_lead" method="post">
                                                                @csrf
                                                                <div class="modal fade" id="close_modal" tabindex="-1" role="dialog" aria-labelledby="close_modalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                        <h5 class="modal-title" id="close_modalLabel">Closed Lead</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <div class="row">
                                                                                <label for="reason"><strong>Reason :</strong></label>
                                                                                <textarea class="form-control" id="reason" name="reason" cols="30" rows="6" required></textarea>
                                                                            </div>
                                                                            <input type="hidden" name="lead_id" id="modal_txt_lead_id" value="{{$lead->id}}">
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                        <button type="submit" class="btn btn-outline-success">submit</button>
                                                                        </div>
                                                                    </div>
                                                                    </div>
                                                                </div>
                                                            </form>
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
                                    @endphp
                                    {{$get_all_leads->appends($append_arr)->links('Custom.Pagination.pagination')}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
</body>
@section('script')
    @if(request()->routeIs('view_all_nursing_care_leads') ? 'active' : '')
        <script>
            $.removeCookie('filter_collapse_js');
        </script>
    @endif
    <script>
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
            var url="<?php echo url('/');?>/view_all_nursing_care_leads ";
            window.location.href = url;
        });
        //----destroy cookie----//
        $('#heading-filter').on('click',function(){
            $.removeCookie('filter_collapse_js');
        });
        //---pop up customer --//
        var route = "{{ url('lab_test_customers_populate') }}";
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
        //sorting apply on table
        let sort_val = $("input[name=sorting_diff]").val();
        $('#tbl_view_all_leads').DataTable({
            "paging":   false,
            // "ordering": false,
            // "info":     false
            //"bSearching": false
            "bFilter": false,
            "bInfo": false,
            'columnDefs': [ {
                'targets': [3,4,5,7,11,12], /* column index */
                'orderable': false, /* true or false */
            }]
        });
    </script>
@endsection
</html>