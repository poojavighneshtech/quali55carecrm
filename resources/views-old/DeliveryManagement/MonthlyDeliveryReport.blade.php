<!DOCTYPE html>
<html lang="en">
    @extends('header_and_sidebar')
    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Delivery : Monthly Report</title>
        @section('styles')
        
        <style>
            table td[class*=col-], table th[class*=col-] {
            position: static;
            display: table-cell;
            float: none;
        }
        tbody.collapse.in {
            display: table-row-group;
        }
        </style>
        @endsection
    </head>

    

<body id="page-top">	
		<!-- Page Wrapper -->
        @section('breadcrumb_item')
            <li class="breadcrumb-item active" aria-content="page">Monthly Delivery Report</li>
        @endsection
            <div class="container">
               
                @section('content')
                <div class="row">
                    <div class="col-md-2">
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header text-center">
                                <b>Delivery Report</b>
                            </div>
                            <div class="card-body justify-content-center">
                                <div id="select_report">
                                    <center>
                                        <input type="radio" id="monthly_report_select" name="report" value="Monthly Report" onclick="monthly_report()"><label for="monthly_report_select"> Monthly Report</label>
                                        <input type="radio" id="datewise_report_select" name="report" value="Datewise Report" onclick="datewise_report()"><label for="datewise_report_select"> Datewise Report</label>
                                    </center>
                                </div>
                                <div id="monthly_report" style="<?php if($_SERVER['REQUEST_METHOD'] === 'POST'){ if($_POST['search']=='search_monthly'){ echo 'display: block';} else{ echo 'display: none'; }}else{ echo 'display: none'; }?>">
                                    <form class="form" action="<?php echo url('/');?>/MonthlyDeliveryReport" method="POST">
                                        {{csrf_field()}}
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <center>
                                                        <span><b>Monthly Report</b></span><br>
                                                        <label for="del_month">Select Month</label>
                                                    <input id="del_month" type="month" name="month" value="<?php if($_SERVER['REQUEST_METHOD'] === 'POST'){ if($_POST['search']=='search_monthly'){ echo $_POST['month'];}}?>" Required>
                                                    <button class="btn btn-primary" type="submit" name="search" value="search_monthly">Search</button>
                                                    </center>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
            
                                <div id="datewise_report"  style="<?php if($_SERVER['REQUEST_METHOD'] === 'POST'){ if($_POST['search']=='search_datewise'){ echo 'display: block';} else{ echo 'display: none'; }}else{ echo 'display: none'; }?>">
                                    <form action="<?php echo url('/');?>/MonthlyDeliveryReport" method="POST">
                                        {{csrf_field()}}
                                        <div class="row">
                                            <div class="col-md-2">
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <center>
                                                        <span><b>Datewise Report</b></span><br>
                                                        <label for="start_date">Select Start Date</label>
                                                        <input id="start_date" type="date" name="start_date" value="<?php if($_SERVER['REQUEST_METHOD'] === 'POST'){ if($_POST['search']=='search_datewise'){ echo $_POST['start_date'];}}?>" required>
                                                        <label for="last_date">Select Last Date</label>
                                                        <input id="last_date" type="date" name="last_date" value="<?php if($_SERVER['REQUEST_METHOD'] === 'POST'){ if($_POST['search']=='search_datewise'){ echo $_POST['last_date'];}}?>" required>
                                                        <button class="btn btn-primary" type="submit" name="search" value="search_datewise">Search</button>
                                                    </center>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>    
                                    @if(session()->has('message'))
                                        <div class="alert alert-danger">
                                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                            {{ session()->get('message') }}
                                        </div>
                                    @endif                    
                                    <table id="records" width="100%" class="table table-bordered ">
                                        <thead>
                                            <th>Date</th>
                                            <th>Delivery</th>
                                            <th>Pickup</th>
                                            <th>Collection</th>
                                            <th>Total</th>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                $count = 0;
                                                $total_amt=array();
                                                //print_r($order_details_data);
                                                foreach($order_details_data as $order_data)
                                                {
                                            ?>
                                                    <tr>
                                                        <td>{{$order_data['del_date']}}<br><button class="btn btn-sm btn-primary btn-xs" type="submit" id="{{$count}}" data-id="{{$count}}" onclick="viewDetails(this.id)">+</button><button class="btn  btn-sm btn-primary btn-xs" type="submit" style="display :none;" id="hide{{$count}}" data-id="{{$count}}" onclick="hideDetails(this.id)">-</button></td>
                                                        <td>{{$order_data['total_del_orders']}}</td>
                                                        <td>{{$order_data['total_pic_orders']}}</td>
                                                        <td>{{$order_data['total_col_orders']}}</td>
                                                        <td>{{$order_data['total_del_orders']+$order_data['total_pic_orders']+$order_data['total_col_orders']}}</td>
                                                    </tr>                                                    
                                                    <tr>
                                                        <td colspan="5" style="display:none"; id="td{{$count}}">
                                                        <table class="table">
                                                            <thead id="head{{$count}}" style="background-color: #3a61d0; color: white;">
                                                                <th>Customer Name</th>
                                                                <th colspan="2">Order Type</th>
                                                                <th colspan="2">Amount</th>
                                                            </thead>
                                                            <tbody id="body{{$count}}">
                                                <?php                                                    
                                                    for ($i=7; $i < count($order_data); $i++)
                                                    {         
                                                        $index = $i - 7;
                                                ?>
                                                        <tr class="<?php if($order_data[$index]['deliverypickup']=='Delivery'){echo "table-success";}elseif($order_data[$index]['deliverypickup']=='Pick Up'){echo "table-info";}else{echo "table-warning";}?>">
                                                            <td>{{$order_data[$index]['shipping_first_name']}}</td>
                                                            <td colspan="2">{{$order_data[$index]['deliverypickup']}}</td>
                                                            <td colspan="2">{{$order_data[$index]['TotalAmt']}}</td>
                                                        </tr>
                                                <?php
                                                    }
                                                    $count++;
                                                ?>
                                                            </tbody>
                                                        </table>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Total:</b></td>
                                                        <td>{{$order_data['total_del_amount']}}</td>
                                                        <td>{{$order_data['total_pic_amount']}}</td>
                                                        <td>{{$order_data['total_col_amount']}}</td>
                                                        <td>{{$total = $order_data['total_del_amount']+$order_data['total_pic_amount']+$order_data['total_col_amount']}}</td> 
                                                    </tr>
                                                    @php
                                                        array_push($total_amt,$total);
                                                    @endphp
                                                    
                                                    
                                            <?php 
                                                }
                                                $total = array_sum($total_amt);
                                            ?>
                                             <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td><b>Total:</b></td>
                                                <td>{{$total}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
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
        function viewDetails(clicked_id)
        {
                //alert(clicked_id);
                //$("."+clicked_id).toggle();
                document.getElementById("td"+clicked_id).style.display = "block";
                //document.getElementById("body"+clicked_id).style.display = "block";
                document.getElementById(clicked_id).style.display = "none";
                document.getElementById("hide"+clicked_id).style.display = "block";
            
        }
        function hideDetails(clicked_id)
        {
                //$("."+clicked_id).toggle();
                //alert(clicked_id);
                var id = document.getElementById(clicked_id);
                var dataID = id.getAttribute('data-id');
                //alert(dataID);
                document.getElementById("td"+dataID).style.display = "none";
                //document.getElementById("body"+dataID).style.display = "none";
                document.getElementById(dataID).style.display = "block";
                document.getElementById("hide"+dataID).style.display = "none";
            
        }
        function monthly_report() {
          document.getElementById("monthly_report").style.display = "block";
          document.getElementById("datewise_report").style.display = "none";
        }
        function datewise_report() {
          document.getElementById("datewise_report").style.display = "block";
          document.getElementById("monthly_report").style.display = "none";
        }
    </script>
    @endsection

    </body>
</html>