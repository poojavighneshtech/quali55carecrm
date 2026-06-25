@extends('header_and_sidebar')
@section('title')
    Admin: Product Details
@endsection
@section('content')
    <form action="<?php echo url('/');?>/edit_master_product/{{$product_details[0]['id']}}" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="container-fluid">
            <!-- DataTales Example -->
            <div class="row">
                <div class="col-md-2">
                </div>
                <div class="col-md-8">
                    @if(session()->has('message'))
                        <div class="alert alert-success">
                            {{ session()->get('message') }}
                        </div>
                    @endif
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Product Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 text-right">
                                    <label for="product_name">Product Name :</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="hidden" name="product_id" value="{{$product_details[0]['id']}}">
                                    {{-- <input type="text" class="form-control" name="product_name" id="product_name" placeholder="Product Name" value="{{$product_details[0]['product_name']}}" required> --}}
                                    <span>{{$product_details[0]['product_name']}}</span>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4 text-right">
                                    <label for="product_details">Product Details :</label>
                                </div>
                                <div class="col-md-8">
                                    {{-- <input type="text" class="form-control" name="product_details" id="product_details" placeholder="Product Details" value="{{$product_details[0]['product_details']}}" required> --}}
                                    <span>{{$product_details[0]['product_details']}}</span>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4 text-right">
                                    <label for="product_details">Product Video :</label>
                                </div>
                                <div class="col-md-8">
                                    <video width="320" height="240" controls>
                                        <source src="{{$product_details[0]['video_url']}}" type="video/mp4">
                                      </video>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4 text-right">
                                    <label for="product_type">Product Type :</label>
                                </div>
                                <div class="col-md-8">
                                    {{-- <input type="text" class="form-control" name="product_type" id="product_type" placeholder="Product Type" value="{{$product_details[0]['product_type']}}" required> --}}
                                    <span>{{$product_details[0]['product_type']}}</span>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4 text-right">
                                    <label for="product_qty">Product quantity :</label>
                                </div>
                                <div class="col-md-8">
                                    {{-- <input type="text" class="form-control" name="product_qty" id="product_qty" placeholder="Product Quantity" value="{{$product_details[0]['product_qty']}}" required> --}}
                                    <span>{{$product_details[0]['product_qty']}}</span>
                                </div>
                            </div>
                            <br>
                            {{-- <div class="row container">
                                <div class="form-group col-md-4 text-right">
                                    <label for="product_image" class="form-label">Product Image :</label>
                                </div>
                                <div class="col-md-8" >  
                                    <div>
                                        <input type="file" id="product_image" class="" value="{{$product_details[0]['product_img_url']}}" name="product_image" accept="image/png, image/jpeg, image/jpg," onclick="fileType(this.id);">
                                    </div>                                    
                                </div>
                            </div> --}}
                            <div class="row" style="display : block" id="product_image_show">
                                <div class="col-md-4 text-right">
                                    <label for="product_image_show">Image :</label>
                                </div>
                                <div class="col-md-8 text-right">
                                    <img src="{{$product_details[0]['product_img_url']}}" class="rounded " id="output"  alt="" width="200" />
                                </div>
                                <br>
                            </div>
                            <div class="row">
                                <div class="col-md-4 text-right">
                                    <label for="product_sale_price">Sale Price :</label>
                                </div>
                                <div class="col-md-8">
                                    {{-- <input type="text" class="form-control" name="product_sale_price" id="product_sale_price" placeholder="Product Sale Price" value="{{$product_details[0]['product_sale_rate']}}" required> --}}
                                    <span>{{$product_details[0]['product_sale_rate']}}</span>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4 text-right">
                                    <label for="product_deposit">Product Deposit :</label>
                                </div>
                                <div class="col-md-8">
                                    {{-- <input type="text" class="form-control" name="product_deposite" id="product_deposite" placeholder="Product Deposit" value="{{$product_details[0]['product_deposite']}}" required> --}}
                                    <span>{{$product_details[0]['product_deposite']}}</span>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4 text-right">
                                    <label for="product_rent">Product Rent :</label>
                                </div>
                                <div class="col-md-8">
                                    {{-- <input type="text" class="form-control" name="product_rent" id="product_rent" placeholder="Product Rent" value="{{$product_details[0]['product_rent']}}" required> --}}
                                    <span>{{$product_details[0]['product_rent']}}</span>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4 text-right">
                                    <label for="product_transport_cost">Product Transport Cost :</label>
                                </div>
                                <div class="col-md-8">
                                    {{-- <input type="text" class="form-control" name="product_transport_cost" id="product_transport_cost" value="{{$product_details[0]['product_transport_cost']}}" placeholder="Transport Cost" required> --}}
                                    <span>{{$product_details[0]['product_transport_cost']}}</span>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4 text-right">
                                    <label for="min_rent_percentage">Min. Rent Percentage (In %) :</label>
                                </div>
                                <div class="col-md-8">
                                    {{-- <input type="text" class="form-control" name="min_rent_percentage" id="min_rent_percentage" placeholder="Minimum Rent Percentage" value="{{$product_details[0]['min_rent_percentage']}}" required> --}}
                                    <span>{{$product_details[0]['min_rent_percentage']}}</span>
                                </div>
                            </div>
                            <hr>
                            {{-- <center><button class="btn btn-primary" type="submit" name="update" value="update">Update Product</button></center> --}}
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                </div>
            </div>	                  
        </div>
        <!-- /.container-fluid -->
    </form>
@endsection

@section('script')
<script>
    $('#product_image').on('change',function(e){
        $('#product_image_show').css('display' , 'block');
        var image = document.getElementById('output');
        image.src = URL.createObjectURL(event.target.files[0]);
    });
</script>

@endsection