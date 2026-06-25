<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Aproved Orders</title>
    <style>
        .row_scroll {
                
                white-space:nowrap;
                /* line-break: auto; */
            }
    </style>
</head>

<body id="page-top">	
		<!-- Page Wrapper -->
    
    @extends('header_and_sidebar')
       
    @section('content')
        <div class="container leads">
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
                    <center>Approved Orders</center>
                </div> 
                <div class="card-body">
                    <form action="{{url('/')}}/filterApprovedOrders" method="post">
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
                    {{-- <div class="row" id="datewise_search" style="@if(isset($start_date)){{'display: inline;'}}@else{{'display: none;'}}@endif">                                
                        <div class="col-md-6">
                            <form action="{{url('/')}}/approvedOrders_datewise_search" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="date" class="form-control" name="start_date" id="start_date" value="@if(isset($start_date)){{$start_date}}@else{{date('Y-m-d')}}@endif" required>
                                    </div>
                                    <div class="col-md-1">
                                        <strong>To</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="date" class="form-control" name="end_date" id="end_date" value="@if(isset($end_date)){{$end_date}}@else{{date('Y-m-d')}}@endif" required>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="form-control btn btn-outline-primary" id="btn_date_search_customer" name="btn_search" value="date_search">Search</button>
                                    </div>
                                </div>    
                            </form>
                        </div>
                    </div> --}}
                    <div class="table">
                        <form action="" name="lead" method="post">
                            {{-- <center><a class="btn btn-primary" href="<?php echo url('/')?>/create_lead">Create New Lead</a></center> --}}
                            <table id="records" class="table table-bordered " width="100%" >
                                <thead>
                                    <tr>
                                        <th>Sr.No</th>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Customer Name</th>
                                        <th>Mobile Number</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php($srno =0)
                                    @foreach($get_approved_orders as $approved_order)
                                    <tr>
                                        <td>{{++$srno}}</td>
                                        <td>{{$approved_order['order_id']}}</td>
                                        <td>{{date('d-m-Y',strtotime($approved_order['DelDate']))}}</td>
                                        <td>{{$approved_order['customer_name']}}</td>
                                        <td>{{$approved_order['contact_no']}}</td>
                                        <td> <span class="badge badge-success">{{$approved_order['status']}}</span></td>
                                        <td>
                                            <a href="{{url('/')}}/approved_order_info/{{$approved_order['order_id']}}" class="btn btn-sm btn-outline-primary">View Order</a>
                                            @if($approved_order['del_status']!='Delivered' AND $approved_order['del_status']!='Closed')
                                            {{-- <a href="{{url('/')}}/close_delivery/{{$approved_order['order_id']}}" class="btn btn-sm btn-outline-secondary">Close Order</a> --}}
                                            <a href="#" class="btn btn-sm btn-outline-secondary" id="{{$approved_order['order_id']}}" onclick="close_order(this.id)" data-toggle="modal" data-target="#closeOrderModal">Close Order</a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="closeOrderModal" tabindex="-1" role="dialog" aria-labelledby="closeOrderModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <form action="{{url('/')}}/close_delivery" method="post" enctype="multipart/form-data">                        
                        {{ csrf_field() }}
                        <div class="modal-header">
                        <h5 class="modal-title" id="closeOrderModalLabel">Close Order</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="order_id" id="order_id" value="">
                            <label for="cancellation_reason">Cancellation Reason</label>
                        <textarea class="form-control" name="cancellation_reason" id="cancellation_reason" cols="30" rows="2"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" value="CloseOrder">Close Order</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                  </div>
                </div>
            </div>
        </div>
    @endsection
</body>
    @section('script')
        <script>
        function close_order(id)
        {
            $('#order_id').val(id);
        }
        $(document).ready(function()
        {  
            $('#records_filter').append("<label>Filter: <select class='form-control-sm' id='filter' name='filter'><option disabled selected>-----Select-----</option><option value='today'>Today</option><option value='yesterday'>Yesterday</option><<option value='past_3_days'>Past 3 Days</option><option value='week'>1 Week</option><option value='month'>Current Month</option><option value='all'>All</option><option value='datewise'>Datewise</option></select></label>"); 
            if(localStorage['filtered'] != null)
            {
                $('#filter').val(localStorage['filtered']);
            }
            $('#filter').on("change",function(){
                var filter_by = $('#filter').val();
                var section = "All_Leads";
                localStorage['filtered'] = filter_by;
                //alert(filter_by);
                if(filter_by != 'datewise')
                {
                    $('#datewise_search').hide();
                    // $('#break_datewise').hide();
                    var dataString = (filter_by);
                    var url = "<?php echo url('/');?>/filterApprovedOrders/"+dataString;
                    window.location.assign(url);
                }
                else if(filter_by == 'datewise')
                {
                    // $('#break_datewise').show();
                    $('#datewise_search').show();
                }
            });  
        });
        </script>
    @endsection
</html>