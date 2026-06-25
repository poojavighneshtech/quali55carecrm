<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Confirm Deliveries</title>
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
                    {{ session()->get('message') }}
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
                <div class="card-header"  style="background-color: #4a6fdc; color: white;" >
                    <center>Pickups Request</center>
                </div> 
                <div class="card-body">
                    <form action="{{url('/')}}/filterPickupOrder" method="post">
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
                    <div class="table table-responsive jim-table-responsive">
                        <table class="table table-bordered" id="records">
                            <thead>
                                <th>Sr.No</th>
                                <th>Order Date</th>
                                <th>Created On</th>
                                <th>Order Id</th>
                                <th>Customer Name</th>
                                <th>Mobile Number</th>
                                <th>Products</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                                <?php $srno=1;?>
                                @foreach($pickup_request as $pickup_order)
                                    <tr>
                                        <td>{{$srno}}</td>
                                        <td data-label="Order Date">{{date('d-m-Y',strtotime($pickup_order['DelDate']))}}</td>
                                        <td data-label="Created Pn">{{date('d-M-y h:i',strtotime($pickup_order['created_at']))}}</td>
                                        <td data-label="Order Id">{{$pickup_order['order_id']}}</td>
                                        <td data-label="Customer Name">{{$pickup_order['shipping_first_name']}}</td>
                                        <td data-label="Mobile No">{{$pickup_order['mobileno']}}</td>
                                        <td data-label="Products">{{$pickup_order['line_item_1']}}</td>
                                        <td data-label="Action">
                                            @if($pickup_order['DelAssignedTo'] != 'Pending')
                                                <a class="btn btn-primary" href="{{url('/')}}/ModifyPickup/{{$pickup_order['order_id']}}">Modify</a>
                                            @else
                                                <a class="btn btn-primary" href="{{url('/')}}/assign_pickup_delboy/{{$pickup_order['order_id']}}">Assign DelBoy</a>
                                            @endif
                                    </tr>
                                    <?php $srno++;?>
                                @endforeach
                            </tbody>
                        </table>
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
                var url = "<?php echo url('/');?>/filterPickupOrder/"+dataString;
                window.location.assign(url);
            });
        });
    </script>
    @endsection
</html>