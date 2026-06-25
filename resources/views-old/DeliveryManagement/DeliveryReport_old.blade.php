<!DOCTYPE html>
<html lang="en">
    @extends('header_and_sidebar')
    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Delivery : Completed Deliveries</title>
        @section('styles')
       
        <style>
            table td[class*=col-], table th[class*=col-] {
            position: static;
            display: table-cell;
            float: none;
        }
        .row_scroll {
            overflow-x: scroll;
            overflow-y: hidden;
            white-space:nowrap;
        }
        </style>
        @endsection
    </head>

   

<body id="page-top">	
		<!-- Page Wrapper -->
            @section('breadcrumb_item')
                <li class="breadcrumb-item active" aria-content="page">Order Report</li>
            @endsection
            <div class="container">
                @section('content')
                <div class="row">
                    <div class="col-md-12">
                        <center><button type="button" id="export" class="btn btn-primary">Export To Excel</button></center>
                        <hr>
                        <center>
                            <select class="selectpicker" name="deliverypickup" id="deliverypickup">
                                <option value="Delivery" @if($deliverypickup=="Delivery") {{'selected'}} @endif>Delivery</option>
                                <option value="Collection" @if($deliverypickup=="Collection") {{'selected'}} @endif>Collection</option>
                                <option value="Pick Up" @if($deliverypickup=="Pick Up") {{'selected'}} @endif>Pick Up</option>
                                <option value="All" @if($deliverypickup=="All") {{'selected'}} @endif>All</option>
                            </select>
                            <br>
                            <br>
                            <div class="row">
                                <div class="col-md-7">
                                    <form action="{{url('/')}}/searchCustomerDelReport" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-4">
                                                <input type="date" class="form-control" name="start_date" id="start_date" value="@if(isset($start_date)){{$start_date}}@endif" required>
                                            </div>
                                            <div class="col-md-1">
                                                <strong>To</strong>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="date" class="form-control" name="end_date" id="end_date" value="@if(isset($end_date)){{$end_date}}@endif" required>
                                            </div>
                                            <div class="col-md-3">
                                                <button type="submit" class="form-control btn btn-outline-primary" id="btn_date_search_customer" name="submit" value="Datewise">Search</button>
                                            </div>
                                        </div>    
                                    </form>
                                </div>
                                <div class="col-md-5">
                                    <form action="{{url('/')}}/searchCustomerDelReport" method="post">
                                            @csrf
                                        <div class="row">
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" name="input_text" id="text_search_customer" placeholder="Search by customer name or number" value="@if(isset($text_customer)){{$text_customer}}@endif" required>
                                            </div>
                                            <div class="col-md-3">
                                                <button type="submit" class="form-control btn btn-outline-primary" id="btn_search_customer" name="submit" value="Search">Search</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </center>
                        <table id="records" class="table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th>Sr.No.</th>
                                    <th>Order id</th>
                                    <th>Status</th>
                                    <th>Delivery Type</th>
                                    <th>Customer Name</th>
                                    <th>Location</th>
                                    <th>Mobile Number</th>
                                    <th>Delivery Date</th>
                                    <th>Equipment</th>
                                    <th>Delivery Assigned To</th>
                                    <th>Receipt to be Carried</th>
                                    <th>Total Amount</th>
                                    <th>Payment Mode</th>
                                    <th>Online Method</th>
                                    <th>Payment Status</th>
                                    <th>Reference Id</th>
                                    <th>Comment</th>
                                    <th>Travel Mode</th>
                                    <th>Pickup Location</th>
                                    <th>Product Address</th>
                                    <th>Drop Location</th>
                                    <th>Customer Star Ratings</th>
                                    <th>Customer Comments</th>
                                    <th>Customer Desclaimer</th>
                                    <th>Updated DateTime</th>
                                    <th>Updated By</th>
                                    <th>Customer Signature</th>
                                    <th>Product Delivered</th>
                                    <th>Address&emsp;&emsp;&emsp;&emsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(isset($order_details[0]['order_id'])){?>
                                    <tr class="row_scroll" style="display: none">
                                        <th>1</th>
                                        <td>1</td>
                                        <td>Status</td>
                                        <td>Delivery Type</td>
                                        <td>Customer Name</td>
                                        <td>Location</td>
                                        <td>Mobile Number</td>
                                        <td>Delivery Date</td>
                                        <td>Equipment</td>
                                        <td>Delivery Assigned To</td>
                                        <td>Receipt to be Carried</td>
                                        <td>Total Amount</td>
                                        <td>Payment Mode</td>
                                        <td>Online Method</td>
                                        <td>Payment Status</td>
                                        <td>Reference Id</td>
                                        <td>Comment</td>
                                        <td>Travel Mode</td>
                                        <td>Pickup Location</td>
                                        <td>Product Address</td>
                                        <td>Drop Location</td>
                                        <td>Customer Star Ratings</td>
                                        <td>Customer Comments</td>
                                        <td>Customer Desclaimer</td>
                                        <td>Updated DateTime</td>
                                        <td>Updated By</td>
                                        <td>Customer Signature</td>
                                        <td>Product Delivered</td>
                                        <td>Address</td>
                                    </tr>
                                    <?php } ?>
                                <?php
                                    $i = 1;
                                    foreach ($order_details as $order_detail)
                                    {
                                ?>
                                        <tr class="row_scroll <?php if($order_detail['deliverypickup'] == 'Delivery'){echo "table-success";}elseif($order_detail['deliverypickup'] == 'Pick Up'){echo "table-info";}elseif($order_detail['deliverypickup'] == 'Collection'){echo "table-warning";}?>">
                                            <td>{{$i}}</td>
                                            <td>{{ $order_detail['order_id']}}</td>
                                            <td>{{ $order_detail['status']}}</td>
                                            <td>{{ $order_detail['deliverypickup']}}</td>
                                            <td>{{ $order_detail['shipping_first_name']}}</td>
                                            <td>{{ $order_detail['location']}}</td>
                                            <td>{{ $order_detail['mobileno']}}</td>
                                            <td>{{ $order_detail['DelDate']}}</td>
                                            <td>{{ $order_detail['line_item_1']}}</td>
                                            <td>{{ $order_detail['DelAssignedTo']}}</td>
                                            <td>{{ $order_detail['ReceiptToBeCarried']}}</td>
                                            <td>{{ $order_detail['TotalAmt']}}</td>
                                            <td>{{ $order_detail['PaymentMode']}}</td>
                                            <td>{{ $order_detail['online_method']}}</td>
                                            <td>{{ $order_detail['payment_status']}}</td>
                                            <td>{{ $order_detail['reference_id']}}</td>
                                            <td>{{ $order_detail['comment']}}</td>
                                            <td>{{ $order_detail['TravelMode']}}</td>
                                            <td>{{ $order_detail['PickupLocation']}}</td>
                                            <td>{{ $order_detail['itemAddress']}}</td>
                                            <td>{{ $order_detail['DropLocation']}}</td>
                                            <td>{{ $order_detail['custstarrating']}}</td>
                                            <td>{{ $order_detail['custcomments']}}</td>
                                            <td>{{ $order_detail['custdisclaimer']}}</td>
                                            <td>{{ $order_detail['updatedDateTime']}}</td>
                                            <td>{{ $order_detail['updatedBy']}}</td>
                                            <td>{{ $order_detail['cust_sign']}}</td>
                                            <td>{{ $order_detail['product_delivered']}}</td>
                                            <td>{{ $order_detail['fulldetails']}}</td>
                                        </tr>
                                <?php
                                        $i++;
                                    }
                                ?>
                            </tbody>
                        </table>
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
        $(document).ready(function(){

            $('#records_filter').append("<label>Filter: <select class='form-control-sm' id='filter' name='filter'><option disabled selected>-----Select-----</option><option value='today'>Today</option><option value='yesterday'>Yesterday</option><<option value='past_3_days'>Past 3 Days</option><option value='week'>1 Week</option><option value='month'>Current Month</option><option value='all'>All</option><option value='datewise'>Datewise</option></select></label>"); 
            if(localStorage['filtered'] != null)
            {
                $('#filter').val(localStorage['filtered']);
            }
            $("#deliverypickup").on('change', function(){
                var filter_by = $('#filter').val();
                localStorage['filtered'] = filter_by;
                var dataString = (filter_by);
                    var deliverypickup = $("#deliverypickup").val();
                    var url = "<?php echo url('/');?>/deliveryReportFilter/"+dataString+"/"+deliverypickup;
                    window.location.assign(url);
            });
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
                    var deliverypickup = $("#deliverypickup").val();
                    var url = "<?php echo url('/');?>/deliveryReportFilter/"+dataString+"/"+deliverypickup;
                    window.location.assign(url);
                }
                else if(filter_by == 'datewise')
                {
                    // $('#break_datewise').show();
                    $('#datewise_search').show();
                }
            });  
            var table = $('#records').DataTable();
            
            $('#export').on('click', function(){
                $('<table>').append(table.$('tr').clone()).table2excel({
                    //exclude: ".excludeThisClass",
                    //name: "abc",
                    filename: "CompletedOrders-{{date('d-m-Y')}}"
                });
            });      
        })
        // function exportToExcel()
        // {
        //     $('#recordds').table2excel({
        //         filename : "Orders {{date('d-m-Y')}}.xls"
        //         //exclude: ".noExl",
        //         //name: "Orders",
        //     });
        // }
        $('select').on('change',function(){
            var value = $(this).val();
            if(value == 'Multiple')
            {
                $('#multiple_products').show();
            }
            else
            {
                $('#multiple_products').hide();
            }
        });
	</script>
    @endsection

    </body>
</html>