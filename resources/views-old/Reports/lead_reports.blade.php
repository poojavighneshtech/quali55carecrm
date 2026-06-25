<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Inquiry :  Assign Vendor</title>
    <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css"/>
    <style>                    
            .bootstrap-select > .dropdown-toggle[title='Select vendor'],
            .bootstrap-select > .dropdown-toggle[title='Select vendor']:hover,
            .bootstrap-select > .dropdown-toggle[title='Select vendor']:focus,
            .bootstrap-select > .dropdown-toggle[title='Select vendor']:active { color: red; }
            div.dataTables_wrapper {
                margin-bottom: 3em;
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
    <div class="container">
        <br>
        <div class="card">
            <div class="card-header"  style="background-color: #4a6fdc; color: white;" >
                <center>Leads Management</center>
            </div> 
            <div class="card-body">
                <div class="row">
                    <div class="col-md-9">
                    </div>
                    <div class="col-md-3">
                        <label>
                            Filter: 
                            <select name="filter" id="filter" onchange="location = this.value;">
                                <option disabled selected>-----Select-----</option>
                                <option value='{{url('/')}}/filterReport/today' name="today">Today</option>
                                <option value='{{url('/')}}/filterReport/yesterday' name="yesterday">Yesterday</option>
                                <option value='{{url('/')}}/filterReport/past_3_days' name="past_3_days">Past 3 Days</option>
                                <option value='{{url('/')}}/filterReport/week' name="week">1 Week</option>
                                <option value='{{url('/')}}/filterReport/month' name="month">Current Month</option>
                                <option value='{{url('/')}}/filterReport/all' name="all">All</option>
                                {{-- <option value="Home.php">Home</option>
                                <option value="Contact.php">Contact</option>
                                <option value="Sitemap.php">Sitemap</option> --}}
                            </select>
                        </label>
                    </div>
                </div>
                <div class="table">
                    <table id="records_filter" class="display" style="width:100%">
                        <thead>
                            <th>Sr. No</th>
                            <th>User / Lead Owner</th>
                            <th>Total Leads</th>
                            <th>In Process Leads</th>
                            <th>Converted Leads</th>
                            <th>Closed Leads</th>
                        </thead>
                        <tbody>
                            {{!$i=1}}
                            @foreach($leads_report as $reports)
                            <tr>
                                <td>{{$i}}</td>
                                <td>{{$reports['username']}}</td>
                                <td>{{$reports['total']}}</td>
                                <td>{{$reports['InProcess']}}</td>
                                <td>{{$reports['convert']}}</td>
                                <td>{{$reports['close']}}</td>
                            </tr>
                            {{!$i=$i+1}}
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <hr><hr>
        <div class="card">
            <div class="card-header"  style="background-color: #4a6fdc; color: white;" >
                <center>JD Leads Management</center>
            </div> 
            <div class="card-body">
                <div class="table">
                    <table id="" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>Sr. No</th>
                                <th>User / Lead Owner</th>
                                <th>Total Leads</th>
                                <th>In Process Leads</th>
                                <th>Converted Leads</th>
                                <th>Closed Leads</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{!$i=1}}
                            @foreach($jd_leads_report as $reports)
                            <tr>
                                <td>{{$i}}</td>
                                <td>{{$reports['username']}}</td>
                                <td>{{$reports['total']}}</td>
                                <td>{{$reports['InProcess']}}</td>
                                <td>{{$reports['convert']}}</td>
                                <td>{{$reports['close']}}</td>
                            </tr>
                            {{!$i=$i+1}}
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
            if(localStorage['filteredReport'] != null)
            {
                $('#filter').val(localStorage['filteredReport']);
            }
            $('table.display').DataTable();
        } );        
    </script>                                                       

    @endsection
    
</html>