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
                        <div class="card-body">
                            {{-- <div class="row">
                                <div class="col-auto">
                                    <a class="btn btn-outline-primary btn-sm" href="{{ url()->previous() }}">Previous Page</a>
                                </div>
                            </div> --}}
                            <h3> order Details </h3>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="customer_name">Customer Name :</label>
                                        </div>
                                        <div class="col-md-8 text-left">
                                            <span>{{$get_order_info[0]->shipping_first_name}}</span>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="contacf_no">Customer Mobile No:</label>
                                        </div>
                                        <div class="col-md-8 text-left">
                                            <span>{{$get_order_info[0]->mobileno}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="contacf_no">Customer Address:</label>
                                        </div>
                                        <div class="col-md-8 text-left">
                                            <span>{{$get_order_info[0]->fulldetails}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="table table-responsive">
                                <table id="records" class="table table-bordered " style="width:100%; ">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>Del Order Id</th>
                                            <th>Product Name</th>
                                            <th>Quantity</th>
                                            <th>Rent/Sale Price</th>
                                            <th>Deposit</th>
                                            <th>Unique Id</th>
                                            {{-- <th>Drop Vendor</th> --}}
                                            <th>Drop Warehouse</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="tbody">
                                        @foreach($get_order_info as $key=>$value)
                                            <tr>
                                                <td>{{$key+1}}</td>
                                                <td>
                                                    <a type="button" class="btn btn-outline-success"
                                                    href="{{url('/')}}/approved_order_info/{{$value->order_id}}/Delivery"><i class="fa fa-eye" aria-hidden="true"></i>&emsp;{{$value->order_id}}</a>
                                                </td>
                                                <td>{{$value->product_name}}</td>
                                                <td>{{1}}</td>
                                                <td>{{$value->product_rent}}</td>                                                
                                                <td>{{$value->product_deposite}}</td>
                                                <td>{{$value->unique_id}}</td>
                                                {{-- <td>{{$value->registered_name}}</td> --}}
                                                <td>{{$value->wh_name.', '.$value->wh_area.', '.$value->wh_city}}</td>
                                                <td><a class="btn btn-sm btn-outline-danger" href="{{route('remove-pickup-prod')}}?order_id={{$pickup_order_id}}&prod_id={{$value->pickups_prod_id}}"><i class="fas fa-trash"></i></a></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
                                    {{!$productImages = json_decode($get_order_info[0]->product_image)}}
                                    @if(!empty($productImages))
                                    <div class="text-center">
                                        @foreach ($productImages as $key=>$img)
                                            <img src="http://{{$img}}" class="img-fluid img-thumbnail view-image" alt="Responsive image" height="300" width="300">
                                        @endforeach
                                    </div>
                                    @else
                                        <span>No Images</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
            </div>
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
                <img class="img-fluid img-thumbnail" src="" alt="" id="modal_image">
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