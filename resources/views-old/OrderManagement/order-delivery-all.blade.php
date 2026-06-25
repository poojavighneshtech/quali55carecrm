@extends('header_and_sidebar')
<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        
        <title>Pending Deliveries</title>
        {{-- <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script> --}}
        {{-- <link rel="stylesheet" href="{{url('/')}}/assets/dist/toast.min.css "> --}}
        <!-- Boostrap 4 CSS -->
   
        @section('styles')
        <style>
            .hiddenRow {
                padding: 0 !important;
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
        <div class="card">  
            <div class="card-header border-primary" id="filter_card">
                <div class="col text-primary" data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                    <strong>Converted Orders</strong>
                </div>
            </div> 
            <div class="card-body border-primary collapse" id="filter-collapse" aria-labelledby="headingTwo" data-parent="#filter_card">
                <form action="{{route('order-delivery-all')}}" method="GET" id="all_leads_form">
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
                                                size="5" autocomplete="off" value="{{request()->get('filter_customer_name')}}">
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
                                                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" maxlength="10"
                                                value="{{request()->get('filter_contact_no')}}">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-4 select">
                                            <label for="patient_name"><strong>Patient Name :</strong></label>
                                        </div>
                                        <div class="col-md-8 select">
                                            <input type="text" class="form-control" name="filter_patient_name"  id="txt_filter_patient_name" placeholder="Patient Name..."
                                                value="{{request()->get('filter_patient_name')}}">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-4 select">
                                            <label for="contact_no"><strong>Lead Status :</strong></label>
                                        </div>
                                        <div class="col-md-8 text-right">
                                            <select class="select form-control selectpicker" name="filter_lead_status" id="select_filter_lead_status" title="Lead Status">
                                                <option value="All" @if(request()->get('filter_lead_status')=='All') selected @endif>All</option>
                                                <option value="Converted" @if(request()->get('filter_lead_status')=='Converted') selected @endif>Pending Deliveries <small>(Converted)</small></option>
                                                <option value="Order Generated" @if(request()->get('filter_lead_status')=='Order Generated') selected @endif>Order Generated</option>
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
                                            <div class="input-group mb-3">
                                                <input type="date" name="filter_from_date" id="input_from_date" class="form-control" value="{{(request()->get('filter_from_date') ? request()->get('filter_from_date') : date('Y-m-d'))}}">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-danger" type="button" id="btn_clear_from_date"><i class="fas fa-times"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3 text-right">
                                            To
                                        </div>
                                        <div class="col-md-9">
                                            <input type="date" name="filter_end_date" id="input_end_date" class="form-control" value="{{(request()->get('filter_end_date') ? request()->get('filter_end_date') : date('Y-m-d'))}}">
                                        </div>
                                    </div>
                                    @if(session('role')=='superuser')
                                        <div class="row mt-2">
                                            <div class="col-md-3 text-right">
                                                Lead Owner
                                            </div>
                                            <div class="col-md-9">
                                                <select class="select form-control selectpicker border" name="filter_lead_owner" id="select_filter_lead_owner" title="Lead Owners"
                                                    data-live-search="true" data-size="5">
                                                    <option value="All" selected>All</option>
                                                    @foreach ($leadowners as $key => $lead_owners)
                                                        <option value="{{$lead_owners->user_id}}" @if(request()->get('filter_lead_owner')==$lead_owners->user_id) selected @endif>{{$lead_owners->lead_owner}}</option>
                                                    @endforeach
                                            </select>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="row mt-4">
                                        <div class="col-md-3 select">
                                            <strong>City :</strong>
                                        </div>
                                        <div class="col-md-9">
                                            <select class="form-control form-control-sm selectpicker border" name="filter_city" id="filter_city" data-size="5" data-live-search="true">
                                                <option value="All">All</option>
                                                @foreach ($cities as $key=>$city)
                                                    <option value="{{$city->city}}" @if(request()->get('filter_city')==$city->city) selected @endif>{{$city->city}}</option>
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
                                    <a href="{{route('order-delivery-all')}}" class="btn btn-sm btn-outline-secondary">Clear Filter</a>
                                    {{-- <button type="button" class="btn btn-outline-secondary btn-sm" id="btn_clear">Clear Filter</button> --}}
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col">
                                    <button type="submit" class="btn btn-outline-success btn-block" name="btn_submit" value="submit">Submit</button>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col">
                                    <button type="submit" class="btn btn-outline-success btn-block" name="btn_export" value="export">Export Excel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <ul class="list-group list-sm-group list-group-flush">
                <li class="list-group-item list-group-item-sm">
                    <div class="row justify-content-end">
                        <div class="col-sm-auto">
                            <div class="row">
                                <div class="col-sm-auto">
                                    Order Status : 
                                    <a  type="button" class="" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{$statustotal}}</a>
                                    <div class="dropdown-menu dropdown-primary">
                                        @foreach($orderstatus as $key=>$count)
                                            <a class="dropdown-item" href="{{request()->fullUrlWithQuery(['filter_order_status'=>$key])}}">{{$key}} &emsp;<strong>{{$count}}</strong></a>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-sm-auto">
                                    Customers : <strong>{{$customers}}</strong>
                                </div>
                                <div class="col-sm-auto">
                                    Products : <strong></strong>
                                    <a  type="button" class="" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{$productscount['total']}}</a>
                                    <div class="dropdown-menu dropdown-primary">
                                        <a class="dropdown-item" href="#">Rent &emsp;<strong>{{$productscount['rent']}}</strong></a>
                                        <a class="dropdown-item" href="#">Sale &emsp;<strong>{{$productscount['sale']}}</strong></a>
                                    </div>
                                </div>
                                <div class="col-sm-auto">
                                    Total Amount : 
                                    <a  type="button" class=""id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{$amountcount['total']}}</a>
                                    <div class="dropdown-menu dropdown-primary">
                                        <a class="dropdown-item" href="#">Rent &emsp;<strong>{{$amountcount['rent']}}</strong></a>
                                        <a class="dropdown-item" href="#">Sale &emsp;<strong>{{$amountcount['sale']}}</strong></a>
                                        <a class="dropdown-item" href="#">Deposit &emsp;<strong>{{$amountcount['deposit']}}</strong></a>
                                        <a class="dropdown-item" href="#">Transport &emsp;<strong>{{$amountcount['transport']}}</strong></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <div class="table table-responsive jim-table-responsive">
                <table class="table table-hover" id="tbl_view_all_leads" width="100%" >
                    <thead class="thead">
                        <tr class="text-nowrap">
                            <th>Timeline</th>
                            <th>creation date</th>
                            <th>customer name</th>
                            <th>Patient Name</th>
                            <th>Mobile Number</th>
                            <th>Equipment</th>
                            <th>Location</th>
                            <th>City</th>
                            <th>Status</th>
                            {{-- <th>Priority</th> --}}
                            <th>Lead Source</th>
                            <th>Lead Owner</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($get_all_leads as $key => $lead)
                            <tr class="text-wrap" data-toggle="collapse" data-target="#rowCollapse{{$key}}" class="accordion-toggle">
                                {{-- <td>{{$get_all_leads->firstItem()+$loop->index}}</td> --}}
                                <td data-label="Timeline">
                                    <button type="button" class="btn btn-outline-info btn-sm btn_timeline" data-id="{{$key}}" data-lead_id="{{$lead->lead_id}}"
                                        data-toggle="modal" data-target="#timelineModal">
                                        <i class="fas fa-history"> Timeline</i>
                                    </button>
                                </td>
                                <td data-label="Creation Date">{{date('d-M-Y',strtotime($lead->creation_date)).' '. date('h:i A',strtotime($lead->converted_at))}}</td>
                                <td data-label="Customer Name"><a href="{{url('/')}}/order_view_lead/{{$lead->customer_id}}/{{$lead->lead_id}}">{{$lead->customer_name}}</a></td>
                                <td data-label="Patient Name">{{$lead->patient_name}}</a></td>
                                <td data-label="Mobile Number"><a href="{{url('/')}}/order_view_lead/{{$lead->customer_id}}/{{$lead->lead_id}}">{{$lead->primary_contact_no}}</a></td>
                                <td data-label="Equipment">
                                    {{$lead->products_name}}
                                </td>
                                <td data-label="Location">{{$lead->location}}</td>
                                <td data-label="City">{{$lead->city}}</td>
                                <td data-label="Status">
                                    {{-- @if($lead->lead_status=='Order Generated')
                                        @if($getOrderStatuses[$lead->lead_id]['current_status']=='Pending')
                                            Del Boy not Assigned
                                        @else
                                            {{$getOrderStatuses[$lead->lead_id]['current_status']}}
                                        @endif

                                    @else
                                    @endif --}}
                                    {{$lead->lead_status}}
                                </td>
                                {{-- <td>{{$lead->priority}}</td> --}}
                                <td data-label="Source">{{$lead->lead_source}}</td>
                                <td data-label="Lead Owner">{{$lead->username}}</td>
                            </tr>
                            <tr>
                                @if($lead->orders!==null)
                                    <td colspan="12" class="hiddenRow">
                                        <div class="accordian-body collapse" id="rowCollapse{{$key}}"> 
                                            <table class="table table-striped table-sm table-light">
                                                <thead>
                                                    <tr class="thead-light">
                                                        <th>Order Id</th>
                                                        <th>Products</th>
                                                        <th>Vendor</th>
                                                        <th>Status</th>
                                                        <th>Assigned To</th>
                                                        <th>Helpers</th>
                                                        {{-- <th>Delivery State</th> --}}
                                                    </tr>
                                                </thead>	
                                                <tbody>
                                                    @foreach($lead->orders as $orderkey=>$order)
                                                        <tr>
                                                            <td>{{$order->order_id}}</td>
                                                            <td>{{$order->line_item_1}}</td>
                                                            <td>{{$order->vendor_name}}</td>
                                                            <td>{{$order->status}}</td>
                                                            <td>{{$order->DelAssignedTo}}</td>
                                                            <td>
                                                                @if(is_array(json_decode($order->helpers,true)))
                                                                    <span>{{implode(",",json_decode($order->helpers,true))}}</span>
                                                                @else
                                                                    <span>No Helper</span>
                                                                @endif
                                                            </td>
                                                            {{-- <td>{{$order->deliveredtimestatus}}</td> --}}
                                                        </tr>
                                                    @endforeach
                                            </tbody>
                                        </table>
                                        </div> 
                                    </td>
                                @else
                                    <td colspan="12" class="text-center hiddenRow accordian-body collapse" id="rowCollapse{{$key}}">No Orders</td>
                                @endif
                            </tr>
                            @endforeach
                    </tbody>
                    
                </table>
                {{$get_all_leads->withPath(url()->full())->links('Custom.Pagination.pagination')}}
            </div>
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
                </div>
            </div>
        </div>
    @endsection
    @section('script')
        <script>
            //-----date validateion----//
            $('#input_from_date').on('change',function(){
                let start_date = this.value;
                $('#input_end_date').attr('min',start_date);
                $('#input_end_date').attr('required',true);
            });
            $('#input_end_date').on('change',function(){
                let end_date = this.value;
                $('#input_from_date').attr('max',end_date);
                $('#input_from_date').attr('required',true);
            });
            //---btn clear filter all clear--..
            $('#btn_clear').on('click',function(){
                $.cookie("filter_collapse_js", "Yes");
                var url="<?php echo url('/');?>/order_mgmt_all_leads";
                window.location.href = url;
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
                let lead_id = $(this).data('lead_id');
                var dataString = ({_token:"{{ csrf_token() }}"});
                $.ajax({
                    type: "POST",
                    url: "{{url('/')}}/lead_timeline/"+lead_id,
                    data: dataString,
                    cache:false,
                    success: function (data)
                    {
                        console.log(data); 
                        let timeline = JSON.parse(data);
                        let row = "";
                        $('#append_div').empty();
                        $.each(timeline,function(key,value){
                            if(value.log_lead_status!='Vendor Assigned'){
                                row+='<div class="vertical-timeline-item vertical-timeline-element">';
                                    row+='<div> <span class="vertical-timeline-element-icon bounce-in"> <i class="badge badge-dot badge-dot-xl badge-primary"> </i> </span>';
                                        row+='<div class="vertical-timeline-element-content bounce-in">';
                                            row+='<h4 class="timeline-title text-success">'+value.log_lead_status+' <span class="text-dark">('+dateFormat(value.created_at)+')</span></h4>';
                                            if(value.log_order_type=='DO' && value.log_lead_status=='Order Generated'){
                                                row+='<strong class="timeline-title text-success">/ Vendor Assigned </span></strong>';
                                            }
                                            row+='<span class="vertical-timeline-element-date text-dark">'+timeFormat(value.created_at)+'</span>';
                                            row+='<p>-By '+value.updated_by+'</p>';
                                            // if(value.log_lead_status=='Converted' || (value.log_order_type=='DO' && value.log_lead_status=='Order Generated')){
                                            //     row+='<h4 class="timeline-title text-success">'+value.log_lead_status+' <span class="text-dark">('+dateFormat(value.log_order_lead_date)+')</span></h4>';
                                            // }else{
                                            //     row+='<h4 class="timeline-title text-success">'+value.log_lead_status+' <span class="text-dark">('+dateFormat(value.log_date+' '+value.log_time)+')</span></h4>';
                                            // }
                                            // if(value.log_order_type=='DO' && value.log_lead_status=='Order Generated'){
                                            //     row+='<strong class="timeline-title text-success">/ Vendor Assigned </span></strong>';
                                            // }
                                            // row+='<p>-By '+value.updated_by+'</p>';
                                            // if(value.log_lead_status=='Converted'){
                                            //     row+='<span class="vertical-timeline-element-date text-dark">'+timeFormat(value.log_order_lead_date)+'</span>';
                                            // }else{
                                            //     row+='<span class="vertical-timeline-element-date text-dark">'+timeFormat(value.created_at)+'</span>';
                                            // }
                                        row+='</div>';
                                    row+='</div>';
                                row+='</div>';
                            }
                        });
                        
                        $('#append_div').append(row);
                    }
                });
            });
        </script>
    @endsection
</body>
</html>
