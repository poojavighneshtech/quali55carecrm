<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>MIS Report</title>
    {{-- <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css"/> --}}
    <style>                    
            /* .bootstrap-select > .dropdown-toggle[title='Select vendor'],
            .bootstrap-select > .dropdown-toggle[title='Select vendor']:hover,
            .bootstrap-select > .dropdown-toggle[title='Select vendor']:focus,
            .bootstrap-select > .dropdown-toggle[title='Select vendor']:active { color: red; }
            div.dataTables_wrapper {
                margin-bottom: 3em;
            } */
            /* table td[class*=col-], table th[class*=col-] {
            position: static;
            display: table-cell;
            float: none;
        } */
        .row_scroll {
            overflow-x: scroll;
            overflow-y: hidden;
            white-space:nowrap;
        }        
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
</head>

<body id="page-top">	
<!-- Page Wrapper -->

@extends('header_and_sidebar')
    <?php if (session('role') == 'admin'){?>
        @section('Admin')
            @parent
        @endsection
        @section('side_users')
            @parent
        @endsection

    <?php } else{?>
        @section('Admin')
            @stop
    
        @section('side_users')
            @stop
    
    <?php } ?>
    
    @section('content')
        <br>
        <div class="card">
            <div class="card-header"  style="background-color: #4a6fdc; color: white;" >
                <center>MIS Reports</center>
            </div> 
            <div class="card-body">
                <div class="card" id="filter_card">
                    <h6 id="h6_filter"><span class="border border-dark rounded bg-primary text-white">&emsp;Filter&emsp;</span></h6>
                    <div class="card-body">
                        <form action="{{url('/')}}/mis_reports" method="GET" id="all_order_form">
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
                                                        size="5" autocomplete="off" value="@if(isset($filter_data['filter_customer_name'])){{$filter_data['filter_customer_name']}}@endif">
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
                                                        value="@if(isset($filter_data['filter_contact_no'])){{$filter_data['filter_contact_no']}}@endif">
                                                </div>
                                            </div>
                                           
                                        </div>
                                        <div class="col-md-5">
                                            <div class="row">
                                                <div class="col-md-3 text-right">
                                                    From
                                                </div>
                                                <div class="col-md-9">
                                                    <input type="date" name="filter_from_date" id="input_from_date" class="form-control" value="@if(isset($filter_data['filter_from_date'])){{$filter_data['filter_from_date']}}@endif">
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-3 text-right">
                                                    To
                                                </div>
                                                <div class="col-md-9">
                                                    <input type="date" name="filter_end_date" id="input_end_date" class="form-control" value="@if(isset($filter_data['filter_end_date'])){{$filter_data['filter_end_date']}}@endif">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <button type="reset" class="btn btn-outline-secondary btn-sm btn-block" id="btn_clear">Clear Filter</button>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-outline-primary btn-block" name="btn_submit" value="submit">Submit</button>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-outline-success btn-sm btn-block" name="btn_submit" value="export_excel">Export Excel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <br>
                <div class="table table-responsive">
                    <table id="mis_records" class="table table-stripped" style="width:100%">
                        <thead>
                            <tr class = "row_scroll">
                                <th>Sr No</th>
                                <th>City</th>
                                <th>Date</th>
                                <th>Patient Name</th>
                                <th>Contact No</th>
                                <th>Equipment Taken</th>
                                <th>Qty</th>
                                <th>Start Date</th>
                                <th>Renewal Date</th>
                                <th>Stop Date</th>
                                <th>Status</th>
                                <th>Rent per unit</th>
                                <th>Deposit Taken</th>
                                <th>Deposit Return</th>
                                <th>Deposit Outstanding</th>
                                <th>Transport</th>
                                <th>Total</th>
                                <th>Paid</th>
                                <th>Outstanding</th>
                                <th>Outstanding last year</th>
                                <th>Payment Received</th>
                                <th>Net Outstanding</th>
                                <th>How many months</th>
                                <th>Apr</th>
                                <th>May</th>
                                <th>Jun</th>
                                <th>July</th>
                                <th>Aug</th>
                                <th>Sep</th>
                                <th>Oct</th>
                                <th>Nov</th>
                                <th>Dec</th>
                                <th>Jan</th>
                                <th>Feb</th>
                                <th>Mar</th>
                                <th>No of Month</th>
                                <th>Rental</th>
                                <th>Rental Collected</th>
                                <th>Vendor</th>
                                <th>Net Rental</th>
                                <th>Net Rental Outstanding</th>
                                <th>Owner</th>
                                <th>Address</th>
                                <th>Location</th>
                                <th>Lead Source</th>
                                <th>Vendor Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mis_report_details as $key=>$mis_report_detail)
                                @if(isset($mis_report_detail->payment_mode))
                                    <tr class = "row_scroll">
                                        <td>{{$mis_report_details->firstItem()+$loop->index}}</td>
                                        {{-- <td></td> --}}
                                        <td>{{$mis_report_detail->city}}</td>
                                        <td>{{date('d-m-Y',strtotime($mis_report_detail->date))}}</td>
                                        <td>{{$mis_report_detail->customer_name}}</td>
                                        <td>{{$mis_report_detail->contact_number}}</td>
                                        <td>{{$mis_report_detail->product_name}}</td>
                                        <td>{{$mis_report_detail->product_qty}}</td>
                                        <td>{{date('d-m-Y',strtotime($mis_report_detail->start_date))}}</td>
                                        <td>{{date('d-m-Y',strtotime($mis_report_detail->renewal_date))}}</td>
                                        <td>{{date('d-m-Y',strtotime($mis_report_detail->stop_date))}}</td>
                                        <td>{{$mis_report_detail->status}}</td>
                                        <td>{{$mis_report_detail->rent_per_unit}}</td>
                                        <td>{{$mis_report_detail->deposit_taken}}</td>
                                        <td>{{$pickup_data[$key]['deposite_return']}}</td>
                                        <td>{{$mis_report_detail->deposit_taken}}</td>
                                        <td>{{$mis_report_detail->transport}}</td>
                                        <td>{{$mis_report_detail->total}}</td>
                                        <td>{{$mis_report_detail->paid}}</td>
                                        <td>{{$mis_report_detail->outstanding}}</td>
                                        <td>{{$mis_report_detail->outstanding_last_year}}</td>
                                        <td>{{$mis_report_detail->payment_received}}</td>
                                        <td>{{$mis_report_detail->net_outstanding}}</td>
                                        <td>{{$mis_report_detail->how_many_months}}</td>
                                        <td>{{$mis_report_detail->apr}}</td>
                                        <td>{{$mis_report_detail->may}}</td>
                                        <td>{{$mis_report_detail->jun}}</td>
                                        <td>{{$mis_report_detail->july}}</td>
                                        <td>{{$mis_report_detail->aug}}</td>
                                        <td>{{$mis_report_detail->sep}}</td>
                                        <td>{{$mis_report_detail->oct}}</td>
                                        <td>{{$mis_report_detail->nov}}</td>
                                        <td>{{$mis_report_detail->dece}}</td>
                                        <td>{{$mis_report_detail->jan}}</td>
                                        <td>{{$mis_report_detail->feb}}</td>
                                        <td>{{$mis_report_detail->march}}</td>
                                        <td>{{$mis_report_detail->no_of_month}}</td>
                                        <td>{{$mis_report_detail->rental}}</td>
                                        <td>{{$mis_report_detail->rental_collected}}</td>
                                        <td>{{$mis_report_detail->vendor_rent}}</td>
                                        <td>{{$mis_report_detail->net_rental}}</td>
                                        <td>{{$mis_report_detail->net_rental_outstanding}}</td>
                                        {{-- <td>-0</td> --}}
                                        <td>{{$mis_report_detail->lead_owner}}</td>
                                        <td>{{$mis_report_detail->address}}</td>
                                        <td>{{$mis_report_detail->location}}</td>
                                        <td>{{$mis_report_detail->lead_source}}</td>
                                        <td>{{$mis_report_detail->vendor_name}}</td>
                                    </tr>
                                @else
                                    <tr class = "row_scroll">
                                        <td>{{$mis_report_details->firstItem()+$loop->index}}</td>
                                        {{-- <td></td> --}}
                                        <td>{{$mis_report_detail->city}}</td>
                                        <td>{{date('d-m-Y',strtotime($mis_report_detail->date))}}</td>
                                        <td>{{$mis_report_detail->customer_name}}</td>
                                        <td>{{$mis_report_detail->contact_number}}</td>
                                        <td>{{$mis_report_detail->product_name}}</td>
                                        <td>{{$mis_report_detail->product_qty}}</td>
                                        <td>{{date('d-m-Y',strtotime($mis_report_detail->start_date))}}</td>
                                        <td>{{date('d-m-Y',strtotime($mis_report_detail->renewal_date))}}</td>
                                        <td>{{$pickup_data[$key]['stop_date']}}</td>
                                        <td>{{$pickup_data[$key]['status']}}</td>
                                        <td>{{$mis_report_detail->rent_per_unit}}</td>
                                        <td>{{$mis_report_detail->deposit_taken}}</td>
                                        <td>{{$pickup_data[$key]['deposite_return']}}</td>
                                        <td>{{$mis_report_detail->deposit_taken}}</td>
                                        <td>{{$mis_report_detail->transport}}</td>
                                        <td>{{$mis_report_detail->rent_per_unit+$mis_report_detail->deposit_taken+$mis_report_detail->transport}}</td>
                                        <td>{{$mis_report_detail->rent_per_unit+$mis_report_detail->deposit_taken+$mis_report_detail->transport}}</td>
                                        <td>NA</td>
                                        <td>NA</td>
                                        <td>NA</td>
                                        <td>NA</td>
                                        <td>NA</td>
                                        <td>@if(isset($renewal_data[$key]['04'])){{"1"}}@else{{"0"}}@endif</td>
                                        <td>@if(isset($renewal_data[$key]['05'])){{"1"}}@else{{"0"}}@endif</td>
                                        <td>@if(isset($renewal_data[$key]['06'])){{"1"}}@else{{"0"}}@endif</td>
                                        <td>@if(isset($renewal_data[$key]['07'])){{"1"}}@else{{"0"}}@endif</td>
                                        <td>@if(isset($renewal_data[$key]['08'])){{"1"}}@else{{"0"}}@endif</td>
                                        <td>@if(isset($renewal_data[$key]['09'])){{"1"}}@else{{"0"}}@endif</td>
                                        <td>@if(isset($renewal_data[$key]['10'])){{"1"}}@else{{"0"}}@endif</td>
                                        <td>@if(isset($renewal_data[$key]['11'])){{"1"}}@else{{"0"}}@endif</td>
                                        <td>@if(isset($renewal_data[$key]['12'])){{"1"}}@else{{"0"}}@endif</td>
                                        <td>@if(isset($renewal_data[$key]['01'])){{"1"}}@else{{"0"}}@endif</td>
                                        <td>@if(isset($renewal_data[$key]['02'])){{"1"}}@else{{"0"}}@endif</td>
                                        <td>@if(isset($renewal_data[$key]['03'])){{"1"}}@else{{"0"}}@endif</td>
                                        <td>{{count($renewal_data[$key])}}</td>
                                        <td>
                                            @php
                                                $date1 = date('Y-m-d',strtotime($mis_report_detail->start_date));
                                                $date2 = date('Y-m-d');

                                                $ts1 = strtotime($date1);
                                                $ts2 = strtotime($date2);

                                                $year1 = date('Y', $ts1);
                                                $year2 = date('Y', $ts2);

                                                $month1 = date('m', $ts1);
                                                $month2 = date('m', $ts2);

                                                $diff = (($year2 - $year1) * 12) + ($month2 - $month1);
                                                echo $diff * $mis_report_detail->rent_per_unit;
                                            @endphp
                                        </td>
                                        <td>{{($mis_report_detail->rent_per_unit * count($renewal_data[$key]))}}</td>
                                        <td>{{($diff * $mis_report_detail->vendor_rent)}}</td>
                                        <td>{{(count($renewal_data[$key]) * $mis_report_detail->rent_per_unit) - (count($renewal_data[$key]) * $mis_report_detail->vendor_rent)}}</td>
                                        <td>{{($diff * $mis_report_detail->rent_per_unit) - ($diff * $mis_report_detail->vendor_rent)}}</td>
                                        {{-- <td>-0</td> --}}
                                        <td>{{$mis_report_detail->owner}}</td>
                                        <td>{{$mis_report_detail->address_line_1.', '.$mis_report_detail->address_line_2.', '.$mis_report_detail->area.', '.$mis_report_detail->landmark.', '.$mis_report_detail->city.'- '.$mis_report_detail->pincode}}</td>
                                        <td>{{$mis_report_detail->location}}</td>
                                        <td>{{$mis_report_detail->source}}</td>
                                        <td>{{$mis_report_detail->vendor_name}}</td>
                                    </tr>
                                @endif

                            @endforeach
                        </tbody>
                    </table>
                    @php
                        $append_arr = array();
                        if(isset($filter_data['filter_customer_name'])){
                            $append_arr['filter_customer_name'] = $filter_data['filter_customer_name'];
                        }
                        if(isset($filter_data['filter_contact_no'])){
                            $append_arr['filter_contact_no'] = $filter_data['filter_contact_no'];
                        }
                        if(isset($filter_data['filter_from_date'])){
                            $append_arr['filter_from_date'] = $filter_data['filter_from_date'];
                        }
                        if(isset($filter_data['filter_end_date'])){
                            $append_arr['filter_end_date'] = $filter_data['filter_end_date'];
                        }
                    @endphp
                    {{$mis_report_details->appends($append_arr)->links('Custom.Pagination.pagination')}}
                </div>
            </div>
        </div>
    @endsection
</body>

    @section('script')
    {{-- <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script> --}}
    <script>
        $(document).ready(function() {
            // $('table.display').DataTable();
            // $('#mis_records').DataTable();
            $('#mis_records').DataTable( {
                "paging":false
                // "ordering": false,
                // "info":     false
            } );
        });
    </script>                                                       

    @endsection
    
</html>