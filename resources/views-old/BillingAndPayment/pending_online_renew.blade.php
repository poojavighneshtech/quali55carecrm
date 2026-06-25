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
    
    {{-- @extends('header_and_sidebar') --}}
    @extends('header_and_sidebar')

       
    @section('content')
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
                <center>Collection Request</center>
            </div> 
            <div class="card-body">
                <form action="{{url('/')}}/pending_online_renew" method="get">
                    @csrf
                    <div class="row justify-content-between">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-8">
                                    <input type="text" class="form-control form-control-sm" name="customer_search" id="" placeholder="Customer name..."
                                    value="{{request()->get('customer_search')}}">
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-outline-success btn-sm btn-block">Submit</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-8"></div>
                                <div class="col-md-4">
                                    <select class="form-control form-control-sm date_filter"  name="date_filter" id="date_filter">
                                        <option value="Today" @if(request()->get('date_filter')=='Today') selected @endif>Today</option>
                                        <option value="Tomorrow" @if(request()->get('date_filter')=='Tomorrow') selected @endif>Tomorrow</option>
                                        <option value="Yesterday" @if(request()->get('date_filter')=='Yesterday') selected @endif>Yesterday</option>
                                        <option value="Past_3_Days" @if(request()->get('date_filter')=='Past_3_Days') selected @endif>Past 3 Days</option>
                                        <option value="Week" @if(request()->get('date_filter')=='Week') selected @endif>Week</option>
                                        <option value="Month" @if(request()->get('date_filter')=='Month') selected @endif>Month</option>
                                        <option value="All" @if(request()->get('date_filter')=='All') selected @endif>All</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="table table-responsive jim-table-responsive table-sm">
                <table class="table table-bordered">
                    <thead>
                        {{-- <th>Sr.No</th> --}}
                        <th>Collection Date</th>
                        <th>Created On</th>
                        <th>Order Id</th>
                        <th>Customer Name</th>
                        <th>Mobile Number</th>
                        <th>Products</th>
                        <th class="text-nowrap">Action</th>
                    </thead>
                    <tbody>
                        {{-- @foreach($collection_request as $collection_order)
                            <tr>
                                <td class="text-nowrap">{{date('d-M-y',strtotime($collection_order['DelDate']))}}</td>
                                <td class="text-nowrap">{{date('d-M-y h:i',strtotime($collection_order['created_at']))}}</td>
                                <td>{{$collection_order['order_id']}}</td>
                                <td>{{$collection_order['shipping_first_name']}}</td>
                                <td>{{$collection_order['mobileno']}}</td>
                                <td>{{$collection_order['line_item_1']}}</td>
                                <td class="text-nowrap">
                                    <a href="#" class="btn btn-outline-warning waves-effect btn-sm"><i class="fas fa-bell"></i></a>
                                    <a href="{{url('/')}}/payment_recieved/{{$collection_order['order_id']}}" class="btn btn-outline-success waves-effect btn-sm">Payment Recieved</a>
                                </td>
                            </tr>
                        @endforeach --}}
                        @foreach($getCollectionOrder as $key =>$order)
                            <tr>
                                <td data-label="Collection Date">{{$order[0]->DelDate}}</td>
                                <td data-label="Crated On">{{$order[0]->created_at}}</td>
                                <td data-label="Order Id">{{$order[0]->order_id}}</td>
                                <td data-label="Customer Name">{{$order[0]->shipping_first_name}}</td>
                                <td data-label="Mobile No">{{$order[0]->mobileno}}</td>
                                <td data-label="Products">{{$order[0]->line_item_1}}</td>
                                <td class="text-nowrap" data-label="Action">
                                    <a href="{{route('edit-renewal')}}?order_id={{$order[0]->order_id}}" class="btn btn-outline-primary waves-effect btn-sm"><i class="fas fa-edit"></i></a>
                                    <a href="#" class="btn btn-outline-warning waves-effect btn-sm"><i class="fas fa-bell"></i></a>
                                    <a href="{{url('/')}}/payment_recieved/{{$order[0]->order_id}}" class="btn btn-outline-success waves-effect btn-sm">Payment Recieved</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    {{$getCollectionOrder->withPath(url()->full())->links('Custom.Pagination.pagination')}}
                </table>
            </div>
        </div>
    @endsection
</body>
@section('script')
    <script>
        $(document).ready(function()
        {    
            // $('#records_filter').append("<label>Filter: <select class='form-control-sm' id='filter' name='filter'><option disabled selected>-----Select-----</option><option value='today'>Today</option><option value='yesterday'>Yesterday</option><<option value='past_3_days'>Past 3 Days</option><option value='week'>1 Week</option><option value='month'>Current Month</option><option value='all'>All</option></select></label>"); 
            // if(localStorage['filtered'] != null)
            // {
            //     $('#filter').val(localStorage['filtered']);
            // }
            // $('#filter').on("change",function(){
            //     var filter_by = $('#filter').val();
            //     var section = "All_Leads";
            //     localStorage['filtered'] = filter_by;
            //     //alert(filter_by);
            //     var dataString = (filter_by);
            //     var url = "<?php echo url('/');?>/filterDeliveryOrder/"+dataString;
            //     window.location.assign(url);
            // });

            $('#date_filter').on("change",function(){
                var filter_val = $(this).val();
                var url = "<?php echo url('/');?>/pending_online_renew/?date_filter="+filter_val;
                window.location.assign(url);
            });
        });
    </script>
    @endsection
</html>