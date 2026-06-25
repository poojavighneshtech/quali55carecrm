<!DOCTYPE html>
<html lang="en">
    @extends('header_and_sidebar')
    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Delivery : Archived Deliveries</title>
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
            <li class="breadcrumb-item active" aria-content="page">Archived Reports</li>
        @endsection
            <div class="container">
                @section('content')
                <div class="row">
                    <div class="col-md-12">
                        <center><button type="button" id="export" class="btn btn-primary">Export To Excel</button></center>
                        <hr>
                        <table id="records" class="table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th>Order id</th>
                                    <th>Status</th>
                                    <th>Delivery Type</th>
                                    <th>Customer Name</th>
                                    <th>Location</th>
                                    <th>Mobile Number</th>
                                    <th>Delivery Date</th>
                                    <th>Delivery Assigned To</th>
                                    <th>Receipt to be Carried</th>
                                    <th>Total Amount</th>
                                    <th>Payment Mode</th>
                                    <th>Travel Mode</th>
                                    <th>Address</th>
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
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(isset($order_details[0]['order_id'])){?>
                                <tr class="row_scroll" style="display: none">
                                    <td>1</td>
                                    <td>Status</td>
                                    <td>Delivery Type</td>
                                    <td>Customer Name</td>
                                    <td>Location</td>
                                    <td>Mobile Number</td>
                                    <td>Delivery Date</td>
                                    <td>Delivery Assigned To</td>
                                    <td>Receipt to be Carried</td>
                                    <td>Total Amount</td>
                                    <td>Payment Mode</td>
                                    <td>Travel Mode</td>
                                    <td>Address</td>
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
                                </tr>
                                <?php } ?>
                                <?php
                                    foreach ($order_details as $order_detail)
                                    {
                                ?>
                                        <tr class="row_scroll <?php if($order_detail['status'] == 'Delivered'){echo "table-success";}elseif($order_detail['status'] == 'Picked Up'){echo "table-info";}elseif($order_detail['status'] == 'Collected'){echo "table-warning";}?>">
                                            <td>{{ $order_detail['order_id']}}</td>
                                            <td>{{ $order_detail['status']}}</td>
                                            <td>{{ $order_detail['deliverypickup']}}</td>
                                            <td>{{ $order_detail['shipping_first_name']}}</td>
                                            <td>{{ $order_detail['location']}}</td>
                                            <td>{{ $order_detail['mobileno']}}</td>
                                            <td>{{ $order_detail['DelDate']}}</td>
                                            <td>{{ $order_detail['DelAssignedTo']}}</td>
                                            <td>{{ $order_detail['ReceiptToBeCarried']}}</td>
                                            <td>{{ $order_detail['TotalAmt']}}</td>
                                            <td>{{ $order_detail['PaymentMode']}}</td>
                                            <td>{{ $order_detail['TravelMode']}}</td>
                                            <td>{{ $order_detail['fulldetails']}}</td>
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
                                        </tr>
                                <?php
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