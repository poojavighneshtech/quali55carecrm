<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Order Information</title>
    @section('header')
    @endsection

</head>

<body id="page-top">	
<!-- Page Wrapper -->

@extends('header_and_sidebar')

    
    @section('content')
    <div class="container">
        <br>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="background-color: #337ab7; color: white;">
                        <center>
                            <b>Order Information</b>
                        </center>
                    </div>
                    <form class="form" id="assign_vendor" action="<?php echo url('/')?>/generate_order" method="post" >
                        {{ csrf_field() }}
                        <div class="card-body">
                            <h3> Lead Details </h3>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="customer_name">Customer Name :</label>
                                        </div>
                                        <div class="col-md-8 text-left">
                                            <span>{{$customer_info[0]['customer_name']}}</span>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="contacf_no">Customer Mobile No:</label>
                                        </div>
                                        <div class="col-md-8 text-left">
                                            <span>{{$customer_info[0]['primary_contact_no']}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="contacf_no">Customer Address:</label>
                                        </div>
                                        <div class="col-md-8 text-left">
                                            <span>{{$customer_info[0]['address_line_1'].', '.$customer_info[0]['address_line_2']}}<br>{{$customer_info[0]['landmark'].', '.$customer_info[0]['area'].', '.$customer_info[0]['city'].', '.$customer_info[0]['pincode'].', '.$customer_info[0]['state'].', '.$customer_info[0]['country']}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Assigned Vendor : <strong>{{$get_order_info[0]['vendor_name']}}</strong></h4>
                                </div>
                            </div>
                            
                            <hr>
                            <div>
                                <table id="records" class="table table-bordered table-responsive" style="width:100%; ">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>Product Name</th>
                                            <th>Quantity</th>
                                            <th>Rent/Sale Price</th>
                                            <th>Deposit</th>
                                            <th>Sale / Rental</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="tbody">
                                        @php($srno = 0)
                                        @foreach($get_order_info as $order_info)
                                        <tr>
                                            <td>{{++$srno}}</td>
                                            <td>{{$order_info['product_name']}}</td>
                                            <td>{{$order_info['product_quantity']}}</td>
                                            <td>{{$order_info['product_rent']}}</td>
                                            <td>{{$order_info['product_deposite']}}</td>
                                            <td>{{$order_info['sale_rental']}}</td>
                                            <td><span class="badge badge-danger">{{$order_info['status']}}</span></td>
                                            <td>
                                                @if($order_info['status']=='Rejected' OR $order_info['status']=='Pending')
                                                    <a href="{{url('/')}}/reassign_vendor/{{$order_info['order_details_id']}}" class="btn btn-primary">Reassign</a>
                                                @else
                                                    <center>-</center>
                                                @endif
                                        </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>   
    @endsection
</body>

    @section('script')

    @endsection
    
</html>