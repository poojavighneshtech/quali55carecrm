<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Pending for vendor Approval</title>
    {{-- <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script> --}}
    @section('styles')
    @endsection
</head>

<body id="page-top">	
		<!-- Page Wrapper -->
    
    @extends('header_and_sidebar')
        
    @section('content')
        <div class="leads">
            @if(session()->has('message'))
                <div class="alert alert-success">
                    {{ session()->get('message') }} @if(session()->has('assigndelboy'))<small><a class="" href="{{ route(session()->get('assigndelboy')) }}">Assign Delivery Boy</a></small>@endif
                </div>
            @endif
            @if(session()->has('message_delete'))
                <div class="alert alert-danger">
                    {{ session()->get('message_delete') }}
                </div>
            @endif 
            @if(session()->has('message_search'))
            <div class="alert alert-danger">
                {{ session()->get('message_search') }}
            </div>
            @endif 
                <div class="card">
                    <div class="card-header" style="background-color: #4a6fdc; color: white;" >
                        <center>Pending For Vendor Approval</center>
                    </div> 
                    <div class="card-body">
                        <form action="{{url('/')}}/filterPendingVendorApproval" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-2">
                                    
                                </div>
                                <div class="col-md-8">
                                    <div class="row">
                                        <label for="Filter"><strong>Filter :</strong></label>
                                        <div class="col-md-4">
                                            <input type="date" class="form-control" name="start_date" id="start_date" value="@if(isset($start_date)){{$start_date}}@endif" required>
                                        </div>
                                        <strong>To</strong> 
                                        <div class="col-md-4">
                                        <input type="date" class="form-control" name="end_date" id="end_date" value="@if(isset($end_date)){{$end_date}}@endif" required>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="submit" class="btn btn-outline-primary btn-sm btn-block" name="btn_date_search" value="Search">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <form action="{{url('/')}}/approve_orders" method="post">
                            {{ csrf_field()}}
                            <div class="table table-responsive jim-table-responsive">
                                <table class="table table-bordered" id="records">
                                    <thead>
                                        <th>Sr. No</th>
                                        <th>Order Date</th>
                                        <th>Created On</th>
                                        <th>Order Id</th>
                                        <th>Customer Name</th>
                                        <th>Mobile Number</th>                                    
                                        <th>Status</th>
                                        <th>Action</th>
                                    </thead>
                                    <tbody>
                                            @php($i=0)
                                            @foreach($order_details as $key=>$order_detail)
                                                <tr>
                                                    <td data-label="Sr.No." class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" name="order_checked[]" id="order_check{{$i}}" value="{{$order_detail['order_id']}}">
                                                            <label class="custom-control-label" for="order_check{{$i}}">{{$key+1}}</label>
                                                        </div>
                                                    <td data-label="Order Date">{{date('d-M-y',strtotime($order_detail['DelDate']))}}</td>
                                                    <td data-label="Created On"> {{\Carbon\Carbon::parse($order_detail['created_at'])->format('j F y g:i A')}}</td>
                                                    <td data-label="Order Id">
                                                        {{$order_detail['order_id']}}
                                                        <input type="hidden" name="order_id[]" id="order_id" value="{{$order_detail['order_id']}}">
                                                    </td>
                                                    <td data-label="Customer Name">{{$order_detail['shipping_first_name']}}</td>
                                                    <td data-label="Mobile Number">{{$order_detail['mobileno']}}</td>                                                
                                                    <td data-label="Status">{{$order_detail['order_approval_status']}}</td>
                                                    <td data-label="Action"><a href="{{url('/')}}/view_pending_order_details/{{$order_detail['order_id']}}" class="btn btn-primary">View Details</a></td>
                                                </tr>
                                                @php($i=$i+1)
                                            @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <center>
                                <button type="submit" class="btn btn-primary" @if(count($order_details)<1){{"disabled"}}@endif>Approve</button>
                            </center>
                        </form>
                       
                    </div>
                </div>
            </div>
        </div>
    @endsection
</body>
@section('script')
    <script>
        $(document).ready(function()
        {    
            $('#records_filter').append("<label>Filter: <select class='form-control-sm' id='filter' name='filter'><option disabled selected>-----Select-----</option><option value='today'>Today</option><option value='yesterday'>Yesterday</option><<option value='past_3_days'>Past 3 Days</option><option value='week'>1 Week</option><option value='month'>Current Month</option><option value='all'>All</option></select></label>"); 
            if(localStorage['filtered'] != null)
            {
                $('#filter').val(localStorage['filtered']);
            }
            $('#filter').on("change",function(){
                var filter_by = $('#filter').val();
                var section = "All_Leads";
                localStorage['filtered'] = filter_by;
                //alert(filter_by);
                var dataString = (filter_by);
                var url = "<?php echo url('/');?>/filterPendingVendorApproval/"+dataString;
                window.location.assign(url);
            });
            
        });
    </script>
@endsection
</html>