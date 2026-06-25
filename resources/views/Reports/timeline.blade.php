<?php use Carbon\Carbon; ?>
@extends('header_and_sidebar')

@section('styles')
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

@section('content')
    <div class="my-3">
        <div class="card" id="filter_card">
            <div class="card-header border-primary" id="filter_card">
                <div class="row">
                    <div class="col text-primary" data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                        <strong>Timelines</strong>
                    </div>
                    <div class="col-auto">
                        <a data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                            <i class="fa fa-chevron-down pull-right"></i>&emsp;<b>Filter</b>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body border-primary collapse" id="filter-collapse" aria-labelledby="headingTwo" data-parent="#filter_card">
                <form action="{{url('/')}}/timeline" method="get">
                    @csrf
                    <div class="row form-group">
                        <div class="col-md-2">
                            <input type="text" class="form-control form-control-sm" name="order_id" id="" placeholder="Order id..." value="{{request()->get('order_id')}}">
                        </div>
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="date" class="form-control form-control-sm" name="start_date" id="" value="{{request()->get('start_date')}}">
                                </div> 
                                <div class="col-md-6">
                                    <input type="date" class="form-control form-control-sm" name="end_date" id="" value="{{request()->get('end_date')}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control form-control-sm selectpicker border" name="order_type[]" id="" title="Order Type" multiple="true" >
                                <option value="All" @if(request()->get('order_type'))@if(in_array("All",request()->get('order_type'))){{"selected"}}@endif @endif>All</option>
                                <option value="Delivery" @if(request()->get('order_type'))@if(in_array("Delivery",request()->get('order_type'))){{"selected"}}@endif @endif>Delivery</option>
                                <option value="Collection" @if(request()->get('order_type'))@if(in_array("Collection",request()->get('order_type'))){{"selected"}}@endif @endif>Collection</option>
                                <option value="Pick Up" @if(request()->get('order_type'))@if(in_array("Pick Up",request()->get('order_type'))){{"selected"}}@endif @endif>Pickup</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control form-control-sm selectpicker border" name="order_status[]" id="" title="Order Status" multiple="true" >
                                <option value="All" @if(request()->get('order_status'))@if(in_array("All",request()->get('order_status'))){{"selected"}}@endif @endif>All</option>
                                <option value="Assigned" @if(request()->get('order_status'))@if(in_array("Assigned",request()->get('order_status'))){{"selected"}}@endif @endif>Assigned</option>
                                <option value="Accepted" @if(request()->get('order_status'))@if(in_array("Accepted",request()->get('order_status'))){{"selected"}}@endif @endif>Accepted</option>
                                <option value="InProgress" @if(request()->get('order_status'))@if(in_array("In-Progress",request()->get('order_status'))){{"selected"}}@endif @endif>In-Progress</option>
                                <option value="Completed" @if(request()->get('order_status'))@if(in_array("Completed",request()->get('order_status'))){{"selected"}}@endif @endif>Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="row form-group justify-content-center">
                        <div class="col-md-2">
                            <select class="form-control form-control-sm selectpicker border" name="order_state" id="" title="Order State">
                                <option value="All" @if(request()->get('order_state')=='All')selected @endif>All</option>
                                <option value="Pending" @if(request()->get('order_state')=='Pending')selected @endif>Pending</option>
                                <option value="Exception" @if(request()->get('order_state')=='Exception')selected @endif>Exception</option>
                                <option value="On Time" @if(request()->get('order_state')=='On Time')selected @endif>On Time</option>
                                <option value="Delay" @if(request()->get('order_state')=='Delay')selected @endif>Delay</option>                                
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control form-control-sm selectpicker border" data-live-search="true" multiple="true" data-size="5" name="delivery_boy[]" id="delivery_boy" title="Delivery Boy">
                                    <option value="All">All</option>
                                @forelse($deliveryBoys as $key=>$delboy)
                                    <option value="{{$delboy->username}}" @if(request()->get('delivery_boy'))@if(in_array($delboy->username,request()->get('delivery_boy'))){{"selected"}}@endif @endif>{{$delboy->username}}</option>
                                @empty
                                    <option value="No" disabled></option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-outline-primary btn-sm btn-block">Submit</button>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-outline-success btn-sm btn-block" name="submitted" value="Export">Export</button>
                        </div>                                
                        <div class="col-md-2">
                            <a href="{{url('/')}}/timeline" class="btn btn-outline-secondary btn-sm btn-block">Clear</a>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-warning btn-sm btn-block" name="details" data-toggle="modal" data-target="#detailed_report" value="Details">Detailed Report</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="table table-responsive my-2">
            <table class="table table-hover table-stripped table-sm">
                <thead>
                    <tr>
                        <th>Date</th>
                        {{-- <th>Order Id</th> --}}
                        <th>Type</th>
                        <th>DelBoy</th>
                        <th>Customer Name</th>
                        {{-- <th>Contact No</th> --}}
                        <th>Generated</th>
                        <th>Assigned</th>
                        <th>Accepted</th>
                        <th>In-Progress</th>
                        <th>Completed</th>
                        <th>Total Time</th>
                        {{-- <th>State</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orderTimeLine as $key=> $timeLine)
                        <tr class="text-nowrap">
                            {{-- <td>{{}}</td> --}}
                            <td class="text-nowrap">{{date('d-M-y',strtotime($timeLine[0]->DelDate))}}</td>
                            {{-- <td>{{$key}}</td> --}}
                            <td>{{$timeLine[0]->type}}</td>
                            <td>{{$timeLine[0]->DelAssignedTo}}</td>
                            <td class="text-nowrap">{{$timeLine[0]->shipping_first_name}}<br>{{$timeLine[0]->contact_no}}</td>
                            <td>{{date('d-M-y',strtotime($timeLine[0]->log_date))." ".date('H:i:s',strtotime($timeLine[0]->log_time))}}<br>-by {{$timeLine[0]->updated_by}}</td>
                            <td>@if(isset($timeLine[1]))
                                    {{date('d-M-y',strtotime($timeLine[1]->log_date))." ".date('H:i:s',strtotime($timeLine[1]->log_time))}}<br>-by {{$timeLine[1]->updated_by}}
                                @else
                                    {{"-"}}
                                @endif
                            </td>
                            <td>@if(isset($timeLine[2]))
                                {{date('d-M-y',strtotime($timeLine[2]->log_date))." ".date('H:i:s',strtotime($timeLine[2]->log_time))}}<br>-by {{$timeLine[2]->updated_by}}
                                @else
                                    {{"-"}}
                                @endif
                            </td>
                            <td>@if(isset($timeLine[3]))
                                    {{date('d-M-y',strtotime($timeLine[3]->log_date))." ".date('H:i:s',strtotime($timeLine[3]->log_time))}}<br>-by {{$timeLine[3]->updated_by}}
                                @else
                                    {{"-"}}
                                @endif
                            </td>
                            <td>@if(isset($timeLine[4]))
                                    {{date('d-M-y',strtotime($timeLine[4]->log_date))." ".date('H:i:s',strtotime($timeLine[4]->log_time))}}<br>-by {{$timeLine[4]->updated_by}}
                                @elseif($timeLine[0]->del_status == "Picked up" || $timeLine[0]->del_status == "Collected" || $timeLine[0]->del_status == "Delivered")
                                    {{date('d-M-y',strtotime($timeLine[0]->completed_at))." ".date('H:i:s',strtotime($timeLine[0]->completed_at))}}<br>-by {{$timeLine[0]->updatedBy}}
                                @else
                                    {{"-"}}
                                @endif
                            </td>
                            <td>
                                @if(isset($timeLine[4]))
                                    @php
                                        $completed_date = Carbon::parse(date('d-M-y',strtotime($timeLine[4]->log_date))." ".date('H:i:s',strtotime($timeLine[4]->log_time)));
                                        $assigned_date = Carbon::parse(date('d-M-y',strtotime($timeLine[0]->log_date))." ".date('H:i:s',strtotime($timeLine[0]->log_time)));

                                        $diff = $completed_date->diffInSeconds($assigned_date);
                                    @endphp
                                    {{gmdate('H:i:s', $diff)}}
                                @else
                                    @if($timeLine[0]->del_status == "Picked up" || $timeLine[0]->del_status == "Collected" || $timeLine[0]->del_status == "Delivered")
                                    @php
                                        $completed_date = Carbon::parse(date('d-M-y',strtotime($timeLine[0]->completed_at))." ".date('H:i:s',strtotime($timeLine[0]->completed_at)));
                                        $assigned_date = Carbon::parse(date('d-M-y',strtotime($timeLine[0]->log_date))." ".date('H:i:s',strtotime($timeLine[0]->log_time)));

                                        $diff = $completed_date->diffInSeconds($assigned_date);
                                    @endphp
                                    {{gmdate('H:i:s', $diff)}}
                                    @else
                                        <b>{{"Not Completed"}}</b>
                                    @endif
                                @endif
                                <br>
                                @if(isset($timeLine[4]))
                                    @php
                                        $completed_date = Carbon::parse(date('d-M-y',strtotime($timeLine[4]->log_date))." ".date('H:i:s',strtotime($timeLine[4]->log_time)));
                                        $assigned_date = Carbon::parse(date('d-M-y',strtotime($timeLine[0]->log_date))." ".date('H:i:s',strtotime($timeLine[0]->log_time)));

                                        $diff = $completed_date->diffInSeconds($assigned_date);
                                        $state = "On Time";
                                        if(date('H:i:s',strtotime(gmdate('H:i:s', $diff))) >= date('H:i:s',strtotime('04:00:00')))
                                        {
                                            $state = "Delayed";
                                        }
                                        else if(date('H:i:s',strtotime(gmdate('H:i:s', $diff))) <= date('H:i:s',strtotime('01:15:00')))
                                        {
                                            $state = "Exception";
                                        }
                                        else
                                        {
                                            $state = "On Time";
                                        }
                                    @endphp
                                    {{$state}}
                                @else
                                    @if($timeLine[0]->del_status == "Picked up" || $timeLine[0]->del_status == "Collected" || $timeLine[0]->del_status == "Delivered")
                                    @php
                                        $completed_date = Carbon::parse(date('d-M-y',strtotime($timeLine[0]->completed_at))." ".date('H:i:s',strtotime($timeLine[0]->completed_at)));
                                        $assigned_date = Carbon::parse(date('d-M-y',strtotime($timeLine[0]->log_date))." ".date('H:i:s',strtotime($timeLine[0]->log_time)));

                                        $diff = $completed_date->diffInSeconds($assigned_date);
                                        $state = "On Time";
                                        if(date('H:i:s',strtotime(gmdate('H:i:s', $diff))) >= date('H:i:s',strtotime('04:00:00')))
                                        {
                                            $state = "Delayed";
                                        }
                                        else if(date('H:i:s',strtotime(gmdate('H:i:s', $diff))) <= date('H:i:s',strtotime('01:15:00')))
                                        {
                                            $state = "Exception";
                                        }
                                        else
                                        {
                                            $state = "On Time";
                                        }
                                    @endphp
                                    {{$state}}
                                    @else
                                        <b>{{"Pending"}}</b>
                                    @endif
                                @endif
                            </td>
                            {{-- <td>
                                @if(isset($timeLine[4]))
                                    @php
                                        $completed_date = Carbon::parse(date('d-M-y',strtotime($timeLine[4]->log_date))." ".date('H:i:s',strtotime($timeLine[4]->log_time)));
                                        $assigned_date = Carbon::parse(date('d-M-y',strtotime($timeLine[0]->log_date))." ".date('H:i:s',strtotime($timeLine[0]->log_time)));

                                        $diff = $completed_date->diffInSeconds($assigned_date);
                                        $state = "On Time";
                                        if(date('H:i:s',strtotime(gmdate('H:i:s', $diff))) >= date('H:i:s',strtotime('04:00:00')))
                                        {
                                            $state = "Delayed";
                                        }
                                        else if(date('H:i:s',strtotime(gmdate('H:i:s', $diff))) <= date('H:i:s',strtotime('01:15:00')))
                                        {
                                            $state = "Exception";
                                        }
                                        else
                                        {
                                            $state = "On Time";
                                        }
                                    @endphp
                                    {{$state}}
                                @else
                                    @if($timeLine[0]->del_status == "Picked up" || $timeLine[0]->del_status == "Collected" || $timeLine[0]->del_status == "Delivered")
                                    @php
                                        $completed_date = Carbon::parse(date('d-M-y',strtotime($timeLine[0]->completed_at))." ".date('H:i:s',strtotime($timeLine[0]->completed_at)));
                                        $assigned_date = Carbon::parse(date('d-M-y',strtotime($timeLine[0]->log_date))." ".date('H:i:s',strtotime($timeLine[0]->log_time)));

                                        $diff = $completed_date->diffInSeconds($assigned_date);
                                        $state = "On Time";
                                        if(date('H:i:s',strtotime(gmdate('H:i:s', $diff))) >= date('H:i:s',strtotime('04:00:00')))
                                        {
                                            $state = "Delayed";
                                        }
                                        else if(date('H:i:s',strtotime(gmdate('H:i:s', $diff))) <= date('H:i:s',strtotime('01:15:00')))
                                        {
                                            $state = "Exception";
                                        }
                                        else
                                        {
                                            $state = "On Time";
                                        }
                                    @endphp
                                    {{$state}}
                                    @else
                                        <b>{{"Pending"}}</b>
                                    @endif
                                @endif
                            </td> --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @php
                    $append_arr = array();
                    // if((request()->get('cust_name')!='' && request()->get('cust_name')!=null)){
                    //     $append_arr['customer_name'] = request()->get('cust_name');
                    // }
                    // if((request()->get('cust_no')!='' && request()->get('cust_no')!=null)){
                    //     $append_arr['contact_no'] = request()->get('cust_no');
                    // }
                    if((request()->get('start_date')!='' && request()->get('start_date')!=null)){
                        $append_arr['start_date'] = request()->get('start_date');
                    }
                    if((request()->get('end_date')!='' && request()->get('end_date')!=null)){
                        $append_arr['end_date'] = request()->get('end_date');
                    }
                    if((request()->get('order_id')!='' && request()->get('order_id')!=null)){
                        $append_arr['order_id'] = request()->get('order_id');
                    }
                    if((request()->get('order_status')!='' && request()->get('order_status')!=null)){
                        $append_arr['order_status'] = request()->get('order_status');
                    }
                    if((request()->get('order_state')!='' && request()->get('order_state')!=null)){
                        $append_arr['order_state'] = request()->get('order_state');
                    }
                    if((request()->get('order_type')!='' && request()->get('order_type')!=null)){
                        $append_arr['order_type'] = request()->get('order_type');
                    }
                @endphp
                {{$orderTimeLine->appends($append_arr)->links('Custom.Pagination.pagination')}}
        </div>
        <!-- Modal -->
    <div class="modal fade" id="detailed_report" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="detailed_report" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailed_report">Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Order Type</th>
                                <th>Pending</th>
                                <th>Delay</th>
                                <th>On Time</th>
                                <th>Exception</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($detailed_report as $key=>$value)
                                <tr>
                                    <th>{{$value['order_type']}}</th>
                                    <td>{{$value['pending_count']}}</td>
                                    <td>{{$value['delay_count']}}</td>
                                    <td>{{$value['on_time_count']}}</td>
                                    <td>{{$value['exce_count']}}</td>
                                    <td>{{$value['pending_count'] + $value['delay_count'] + $value['on_time_count'] + $value['exce_count']}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <table class="table table-bordered my-3">
                        <thead>
                            <tr>
                                <th>Order Type</th>
                                <th colspan="2">Delay</th>
                                <th colspan="2">On Time</th>
                                <th colspan="2">Exception</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($detailed_report as $key=>$value)
                                <tr>
                                    <th>{{$value['order_type']}}</th>
                                    <td>{{$value['delay_count']}}</td>
                                    <td>                                        
                                        @php
                                            // print_r($value['delay_perc']);
                                            $a = $value['delay_perc'];
                                            if(count($a)) {
                                                // $average = array_sum($a)/count($a);
                                                // echo $average;
                                                $minutes = array_sum($a) / count($a);
                                                echo date('H:i:s',strtotime(intdiv($minutes, 60).':'. ($minutes % 60)));
                                            }
                                        @endphp
                                    </td>
                                    <td>{{$value['on_time_count']}}</td>
                                    <td>
                                        @php
                                            $a = $value['on_time_perc'];
                                            if(count($a)) {
                                                // $average = array_sum($a)/count($a);                                                
                                                // echo date('H:i:s', array_sum(array_map('strtotime', $a)) / count($a));
                                                $minutes = array_sum($a) / count($a);
                                                echo date('H:i:s',strtotime(intdiv($minutes, 60).':'. ($minutes % 60)));
                                            }
                                        @endphp
                                    </td>
                                    <td>{{$value['exce_count']}}</td>
                                    <td>
                                        @php
                                            $a = $value['exce_perc'];
                                            if(count($a)) {
                                                // $average = array_sum($a)/count($a);
                                                // echo date('H:i:s', array_sum(array_map('strtotime', $a)) / count($a));
                                                $minutes = array_sum($a) / count($a);
                                                echo date('H:i:s',strtotime(intdiv($minutes, 60).':'. ($minutes % 60)));
                                            }
                                        @endphp
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
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
                                row+='<h4 class="timeline-title text-success">'+value.log_lead_status+' <span class="text-dark">('+dateFormat(value.log_date+' '+value.log_time)+')</span></h4>';
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
</script>
@endsection