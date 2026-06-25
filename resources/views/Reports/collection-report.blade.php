@extends('header_and_sidebar')
@section('title')
   Collection Report
@endsection
    @section('header')
       
    @endsection

    @section('content')
        <div class="container-fluid">
            <div class="card my-5">
                <div class="card" id="filter_card">
                    <div class="card-header border-primary" id="filter_card">
                        {{-- <button class="btn btn-link btn-sm collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            All Leads
                        </button> --}}
                        <div class="row">
                            <div class="col text-primary" data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                                <strong>Collection Report</strong>
                            </div>
                            <div class="col-auto">
                                <a data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                                    <i class="fa fa-chevron-down pull-right"></i>&emsp;<b>Filter</b>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body border-primary collapse" id="filter-collapse" aria-labelledby="headingTwo" data-parent="#filter_card">
                        <form action="{{url('/')}}/collectionReport" method="GET" id="all_leads_form">
                            @csrf
                            <div class="row">
                                <div class="col-md-9">
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
                                </div>
                                <div class="col-md-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btn_clear">Clear Filter</button>
                                        </div>
                                        <div class="col-md-6">
                                            <button type="submit" class="btn btn-outline-success btn-block" name="btn_submit" value="Export">Export</button>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col">
                                            <button type="submit" class="btn btn-outline-primary btn-block" name="btn_submit" value="Search">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="revenue-count">
                    <div class="row">
                        <div class="col-auto">
                            <div class="row">
                                <div class="col-auto">Total Revenue</div>
                                <div class="col-auto"><a  type="button" class="count" id="orders_count"><span class="badge badge-primary">{{$total_renewed_count_total + $total_overdue_count_total}}</span></a></div>
                                <div class="col-auto">Collected Revenue</div>
                                <div class="col-auto"><a  type="button" class="count" id="vendor_rent_count"><span class="badge badge-primary">{{$total_renewed_count_total}}</span></a></div>
                                <div class="col-auto">Overdue Revenue</div>
                                <div class="col-auto"><a  type="button" class="count" id="order_rent_count"><span class="badge badge-primary">{{$total_overdue_count_total}}</span></a></div>
                            </div>
                        </div>
                        <div class="col-auto mr-auto"></div>
                    </div>
                </div>
                <div class="table table-responsive table-sm">
                    <table class="table table-stripped table-hover">
                        <thead>
                            <th>Sr No</th>
                            <th>Date</th>
                            <th>Created On</th>
                            <th>Customer Name</th>
                            <th>Contact Number</th>
                            <th>Equipment</th>
                            <th>Amount</th>                                
                            <th>State</th>
                            <th>Lead Owner</th>
                        </thead>
                        <tbody>
                            @foreach($count_array as $key => $value)
                                <tr>
                                    <td>{{$count_array->firstItem()+$loop->index}}</td>
                                    <td class="text-nowrap">
                                        {{date('d-M-Y',strtotime($value->date))}}
                                    </td>
                                    <td class="text-nowrap">
                                        {{date('d-M-y h:i A',strtotime($value->created_at))}}
                                    </td>
                                    <td>{{$value->customer_name}}</td>
                                    <td>{{$value->contact_no}}</td>
                                    <td>{{$value->product_name}}</td>
                                    <td>
                                        @if(isset($value->product_rent_amount))
                                            {{$value->product_rent_amount}}
                                        @elseif(isset($value->payment_mode))
                                            @if($value->payment_mode == "Cash")
                                                {{$value->cash_amount}}
                                            @elseif($value->payment_mode == "Online")
                                                {{$value->online_amount}}
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($value->product_rent_amount))
                                            <span class="badge badge-danger">Overdue</span>
                                        @elseif(isset($value->payment_mode))
                                            <span class="badge badge-success">Renewed</span>
                                        @endif
                                    </td>
                                    <td>{{$value->lead_owner}}</td>
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
                        if(isset($filter_arr['from_date'])){
                            $append_arr['filter_from_date'] = $filter_arr['from_date'];
                        }
                        if(isset($filter_arr['end_date'])){
                            $append_arr['filter_end_date'] = $filter_arr['end_date'];
                        }
                    @endphp
                    {{$count_array->appends($append_arr)->links('Custom.Pagination.pagination')}}
                </div>
            </div>
        </div>
    @endsection
    @section('script')
    <script>
        $('#btn_clear').on('click',function(){
            $.cookie("filter_collapse_js", "Yes");
            var url="<?php echo url('/');?>/collectionReport";
            window.location.href = url;
        });
    </script>
    @endsection