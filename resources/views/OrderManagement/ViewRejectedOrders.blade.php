<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Rejected Orders</title>
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
                    <center>Rejected Orders</center>
                </div> 
                <div class="card-body">
                    <form action="{{url('/')}}/filterRejectedOrders" method="post">
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
                    <div class="table">
                        <form action="" name="lead" method="post">
                            {{-- <center><a class="btn btn-primary" href="<?php echo url('/')?>/create_lead">Create New Lead</a></center> --}}
                            <table id="records" class="table table-bordered" width="100%" >
                                <thead>
                                    <tr>
                                        <th>Sr.No</th>
                                        <th>Order ID</th>
                                        <th>Customer Name</th>
                                        <th>Mobile Number</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php($srno =0)
                                    @foreach($get_rejected_orders as $rejected_orders) 
                                    <tr>
                                        <td>{{++$srno}}</td>
                                        <td>{{$rejected_orders['order_id']}}</td>
                                        <td>{{$rejected_orders['customer_name']}}</td>
                                        <td>{{$rejected_orders['contact_no']}}</td>
                                        <td> <span class="badge badge-danger">{{$rejected_orders['status']}}</span></td>
                                        <td><a href="{{url('/')}}/rejected_order_info/{{$rejected_orders['order_id']}}" class="btn btn-outline-primary">View Order</a></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            <div>
            
                
        </div>
    @endsection
</body>
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
                    var dataString = (filter_by);
                    var url = "<?php echo url('/');?>/filterRejectedOrders/"+dataString;
                    window.location.assign(url);
                }
                else if(filter_by == 'datewise')
                {
                    $('#datewise_search').show();
                }
            });   
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
            //     var url = "<?php echo url('/');?>/filterOrderLeads/"+dataString;
            //     window.location.assign(url);
            // });

            
            // $('#records tr').click("input",function() {    
            //     var count = this.dataset.count;
            //     // var cust_no = $('#cust_no'+count).text(); 
            //     //alert(count);   
            //     var customer_id = $('#customer_id'+count).val();
            //     var lead_id = $('#lead_id'+count).val();
            //     document.getElementById('close_lead').href ="<?php echo url('/');?>/close_lead/"+customer_id+"/"+lead_id;
            //     // var url = "<?php echo url('/'); ?>/assign_vendor_byscript/"+customer_id+"/"+lead_id;
            //     // window.location.assign(url);
            // });
            // $('select').on('change', function(){
            //     var reason = $(this).val();
            //     //alert(reason);
            //     document.getElementById('close_lead').href += '/'+reason;
            // })
        });
        </script>
    @endsection
</html>