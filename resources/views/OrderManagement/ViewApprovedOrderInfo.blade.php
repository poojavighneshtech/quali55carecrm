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
<br>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            @if(session()->has('delete') || session()->has('product_name') )
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong> {{ session()->get('product_name')}}</strong> {{ session()->get('delete')}}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="card-header" style="background-color: #337ab7; color: white;">
                <center>
                    <b>Order Information</b>
                </center>
            </div>
            <form class="form" id="assign_vendor" action="<?php echo url('/')?>/generate_order" method="post" >
                {{ csrf_field() }}
                <div class="card-body">
                    <h3> Order Details </h3>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row form-group">
                                <div class="col-6 col-md-4">
                                    <label for="customer_name">Customer Name :</label>
                                </div>
                                <div class="col-6 col-md-8 text-left">
                                    <span>{{$customer_info[0]['customer_name']}}</span>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-6 col-md-4">
                                    <label for="contacf_no">Customer Mobile No:</label>
                                </div>
                                <div class="col-6 col-md-8 text-left">
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
                    <div class="table  table-responsive jim-table-responsive">
                        <table id="records" class="table table-bordered" style="width:100%; ">
                            <thead>
                                <tr>
                                    <th>Sr.No.</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Rent/Sale Price</th>
                                    <th>Deposit</th>
                                    <th>Sale / Rental</th>
                                    <th>Unique Id</th>
                                    <th>Warehouse</th>
                                    <th>Status</th>
                                    {{-- <th>Action &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</th> --}}
                                </tr>
                            </thead>
                            <tbody class="tbody">
                                @php($srno = 0)
                                @foreach($get_order_info as $order_info)
                                <tr>
                                    <td data-label="Sr.No.">{{++$srno}}</td>
                                    <td data-label="Equipment">{{$order_info['product_name']}}<br><span><small>{{$order_info['upgraded']}}</small></span></td>
                                    <td data-label="Qty">{{$order_info['product_quantity']}}</td>
                                    <td data-label="Rent/Sale Price">{{$order_info['product_rent']}}</td>
                                    <td data-label="Deposit">{{$order_info['product_deposite']}}</td>
                                    <td data-label="Sale/Rental">{{$order_info['sale_rental']}}</td>
                                    <td data-label="Inventory Id">{{$order_info['unique_id']}}</td>
                                    <td data-label="Warehouse">{{$order_info['warehouse_name'].', '.$order_info['warehouse_area'].', '.$order_info['warehouse_city']}}</td>
                                    <td data-label="Status">{{$order_info['status']}}</td>
                                    {{-- <td>
                                         @if($order_info['delivery_status']!='Delivered' AND $order_info['delivery_status']!='Closed')
                                            <a href="{{url('/')}}/reassign_vendor/{{$order_info['order_details_id']}}" class="btn btn-primary">Edit Vendor</a>
                                            <a href="{{url('/')}}/delete_order_product/{{$order_info['order_details_id']}}/{{$order_info['product_id']}}" class="btn btn-danger btn-circle"><i class="fas fa-trash"></i></a>

                                        @else
                                            <center>-</center>
                                        @endif
                                    </td> --}}
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <br>
                        <div class="row">
                            <div class="col-auto mr-auto"></div>
                            <div class="col-auto">
                                {{-- <a class="btn btn-outline-primary btn-sm" href="{{ url()->previous() }}">Cancel</a> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            {{-- product images --}}
            <div id="accordion">
                <div class="card">
                    <div class="card-header" id="headingOne"  data-toggle="collapse" data-target="#product-images" aria-expanded="true" aria-controls="product-images">
                        <h5 class="mb-0">
                            <span class="btn btn-outline-primary">Show Images</span>
                        </h5>
                    </div>
              
                    <div id="product-images" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            {{!$productImages = json_decode($get_order_info[0]['product_image'])}}
                            @if(!empty($productImages))
                            <div class="text-center">
                                @foreach ($productImages as $key=>$img)
                                    <img src="http://{{$img}}" class="img-fluid img-thumbnail view-image" alt="Responsive image">
                                @endforeach
                            </div>
                            @else
                                <span>No Images</span>
                            @endif
                        </div>
                    </div>
                </div>
        </div>
        <br>
    </div>
</div>
<div class="modal fade" id="modalImage" tabindex="-1" role="dialog" aria-labelledby="modalImageTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Image</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
        </div>
            <img class="img-fluid img-thumbnail" src="" alt="" id="modal_image" height="300" width="300">
      </div>
    </div>
  </div>

@endsection
</body>

    @section('script')
    <script>
        $('.view-image').on('click',function(){
            console.log($(this).attr('src'));
            $('#modal_image').attr('src',$(this).attr('src'));
            $('#modalImage').modal('show');
        })
    </script>
    @endsection
    
</html>