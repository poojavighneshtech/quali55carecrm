<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Pending Order Details</title>
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
                {{-- <form action="{{url('/')}}/status_change/{{$order_details[0]['order_id']}}/{{$order_details[0]['vendor_id']}}/{{$order_details[0]['vendor_product_id']}}" method="GET"> --}}
                    <form action="{{url('/')}}/status_change" method="POST">
                        {{csrf_field()}}
                    <div class="card">
                        <div class="card-header"  style="background-color: #4a6fdc; color: white;" >
                            <center>Order Details</center>
                        </div> 
                        <div class="card-body">
                           
                            
                            
                            <div class="table table-responsive jim-table-responsive">
                                <table class="table table-bordered" id="records">
                                    <thead>
                                        <th>Sr.No</th>
                                        <th>Date&emsp;&emsp;&emsp;</th>
                                        <th>Order Id</th>
                                        <th>Vendor Name</th>
                                        <th>Equipment</th>
                                        <th>Warehouse</th>
                                        <th>Status</th> 
                                        <th>Unique ID</th>                                   
                                        <th>Action</th>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $i=0;
                                            $srno=1;
                                            foreach($order_details as $order_detail)
                                            {
                                        ?>
                                            <tr>
                                                {{-- hidden for post value --}}
                                                <input type="hidden" name="order_details_id[]" id="order_details_id{{$i}}" value="{{$order_detail['order_details_id']}}">
                                                <input type="hidden" name="order_id[]" id="order_id{{$i}}" value="{{$order_detail['order_id']}}">
                                                <input type="hidden" name="customer_id[]" id="customer_id{{$i}}" value="{{$order_detail['customer_id']}}">
                                                <input type="hidden" name="vendor_product_id[]" id="vendor_product_id{{$i}}" value="{{$order_detail['vendor_product_id']}}">
                                                <input type="hidden" name="vendor_id[]" id="vendor_id{{$i}}" value="{{$order_detail['vendor_id']}}">
                                                <input type="hidden" name="vendor_warehouse_id[]" id="vendor_warehouse_id{{$i}}" value="{{$order_detail['vendor_warehouse_id']}}">
                                                <input type="hidden" name="product_qty[]" id="product_qty{{$i}}" value="{{$order_detail['product_qty']}}">      
                                                <input type="hidden" name="creation_date[]" id="creation_date{{$i}}" value="{{$order_detail['creation_date']}}">      
                                                <td data-label="Sr.No.">{{$srno}}</td>
                                                <td data-label="Date">
                                                    {{date('d-m-Y',strtotime($order_detail['creation_date']))}}
                                                </td>
                                                <td data-label="Order Id">{{$order_detail['order_id']}}</td>
                                                <td data-label="Vendor">{{$order_detail['registered_name']}}</td>
                                                <td data-label="Equipment">
                                                    {{$order_detail['product_name']}}
                                                </td>
                                                <td data-label="Warehouse">
                                                    {{$order_detail['warehouse_name'].', '.$order_detail['warehouse_area'].', '.$order_detail['warehouse_city']}}
                                                </td>
                                                <td data-label="Status">
                                                    @if ($order_detail['status']=="Pending")
                                                        {{date('H:i:s',strtotime($order_details[0]['created_at']))}}    
                                                        <span class="badge badge-warning">{{$order_detail['status']}}</span>
                                                    @endif
                                                    @if ($order_detail['status']=="Rejected")
                                                        <span class="badge badge-danger">{{$order_detail['status']}}</span>
                                                    @endif
                                                    @if ($order_detail['status']=="Accepted")
                                                        <span class="badge badge-success">{{$order_detail['status']}}</span>
                                                    @endif
                                                </td>
                                                <td data-label="Inventory Id" class="text-center">
                                                    @if(isset($order_detail['unique_id']))
                                                        {{$order_detail['unique_id']}}
                                                    @else
                                                        {{"-"}}
                                                    @endif

                                                </td>
                                                <td data-label="Action">
                                                    @if($order_detail['status']=='Rejected' OR $order_detail['status']=='Pending')
                                                        -
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        <?php
                                                $i++;
                                                $srno++;
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    {{-- <center><input type="submit" class="btn btn-primary" name="status_change" id="status_change" value="Approve"></center> --}}
                                    <a class="btn btn-outline-primary btn-sm" href="{{ url()->previous() }}"><i class="fas fa-arrow-left"></i> Back</a>
                                </div>
                                {{-- <div class="col-md-3 text-right">
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endsection
</body>
@section('script')
    <script>
    </script>
    @endsection
</html>