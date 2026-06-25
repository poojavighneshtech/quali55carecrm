<!DOCTYPE html>
<html lang="en">
    @extends('header_and_sidebar')
    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Delivery : Modify</title>
        @section('styles')
       
        
        @endsection
    </head>

        

<body id="page-top">	
		<!-- Page Wrapper -->
        @section('breadcrumb_item')
           
            <li class="breadcrumb-item active" aria-content="page">Modify Delivery</li>
        @endsection
            <div class="container">                
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
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-primary">
                            <div class="card-header text-center" style="background-color: #345bcc; color: white;">
                                <span><b>Modify Delivery</b></span>
                            </div>
                            <div class="card-body">
                                <form action="{{url('/')}}/modifyDeliveryFilter" method="post">
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
                                <div class="table-responsive jim-table-responsive">
                                    <table id="records" class="table table-bordered">
                                        <thead>
                                            <th>Sr.No.</th>
                                            <th>Order Id</th>
                                            <th>Customer Name</th>
                                            <th>Contact Number</th>
                                            <th>Equipment</th>
                                            <th>Action</th>
                                        </thead>
                                        <tbody>
                                            {{!$Srno = 1}}
                                            @foreach ($orders as $order)
                                                <tr>
                                                    <td data-label="Sr.No.">{{$Srno}}</td>
                                                    <td data-label="Order Id">{{$order['order_id']}}</td>
                                                    <td data-label="Customer Name">{{$order['shipping_first_name']}}</td>
                                                    <td data-label="Contact Number">{{$order['mobileno']}}</td>
                                                    {{-- <td>{{$order['line_item_1']}}</td> --}}
                                                    <td data-label="Equipment"><center>---</center></td>
                                                    <td data-label="Action">
                                                        <a class="btn btn-primary" href="{{url('/')}}/ModifyDelivery/{{$order['order_id']}}">Modify</a>
                                                    </td>
                                                </tr>
                                                {{!$Srno++}}
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endsection
            </div>
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    </div>	   
    @section('script')    
    <script>
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
                    var url = "<?php echo url('/');?>/modifyDeliveryFilter/"+dataString;
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

    </body>
</html>