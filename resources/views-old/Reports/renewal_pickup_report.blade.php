<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Renewal Pickup Report</title>
    <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css"/>
    <style>                    
    </style>
</head>

<body id="page-top">	
<!-- Page Wrapper -->

{{-- @extends('header_and_sidebar') --}}
@extends('new-sidebar')

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
    <div class="container">
        <br>
        <div class="card">
            <div class="card-header"  style="background-color: #4a6fdc; color: white;" >
                <center>Renewal Report</center>
            </div> 
            <div class="card-body">
                <div class="row">
                    <div class="col-md-9">
                    </div>
                    <div class="col-md-3">
                        <label>
                            Filter: 
                            <select name="filter" id="filter" onchange="location = this.value;">
                                <option value='{{url('/')}}/renewal_report/today' id="today" name="today">Today</option>
                                <option value='{{url('/')}}/renewal_report/tomorrow' id="tomorrow" name="tomorrow">Tomorrow</option>
                                <option value='{{url('/')}}/renewal_report/overdue' id="overdue" name="overdue">Overdue</option>
                                <option value='{{url('/')}}/renewal_report/3_days' id="3_days" name="3_days">After 3 Days</option>
                                {{-- <option value='{{url('/')}}/renewal_report/month' name="month">Current Month</option> --}}
                                <option value='{{url('/')}}/renewal_report/all' name="all">All</option>
                                {{-- <option value="Home.php">Home</option>
                                <option value="Contact.php">Contact</option>
                                <option value="Sitemap.php">Sitemap</option> --}}
                            </select>
                        </label>
                    </div>
                </div>
                <div class="table">
                    <table id="records" class="display" style="width:100%">
                        <thead>
                            <th>Sr. No</th>
                            <th>User / Lead Owner</th>
                            <th>Total Customers</th>
                            <th>Total Equipments</th>
                            <th>Total Due Amount</th>                            
                        </thead>
                        <tbody>
                            @php
                                $sr_no = 1;
                            @endphp
                             @foreach($renewal_pickup_report as $report)
                             <tr>
                                 <td>{{$sr_no}}</td>
                                 <td>{{$report['username']}}</td>
                                 <td>{{$report['customer_count']}}</td>
                                 <td>{{$report['product_count']}}</td>
                                 <td>{{$report['total_due_amount']}}</td>
                             </tr>
                             @php
                                $sr_no += 1;
                            @endphp
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
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            // $('#records_filter').append("<label>Filter: <select class='form-control-sm' id='filter' name='filter'><option disabled selected>-----Select-----</option><option value='today'>Today</option><option value='yesterday'>Yesterday</option><<option value='past_3_days'>Past 3 Days</option><option value='week'>1 Week</option><option value='month'>Current Month</option><option value='all'>All</option></select></label>"); 
            if(localStorage['filtered'] != null)
            {
                var filtered = localStorage['filtered'];   
                // alert(filtered);
                $('#'+filtered).prop('selected', true);
            }
            $('table.display').DataTable();
        });
    </script>
    @endsection
</html>