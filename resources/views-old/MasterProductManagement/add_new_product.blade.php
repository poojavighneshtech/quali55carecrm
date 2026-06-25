@extends('header_and_sidebar')
@section('title')
    Admin: Add New Product
@endsection
@section('content')
    <form action="<?php echo url('/');?>/add_new_product" method="POST" enctype="multipart/form-data">
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
                            <h6 class="m-0 font-weight-bold text-primary">Add New Product</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 text-right">
                                    <label for="product_name">Product Name</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="product_name" id="product_name" placeholder="Product Name" required>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4 text-right">
                                    <label for="product_details">Product Details</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="product_details" id="product_details" placeholder="Product Details" required>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4 text-right">
                                    <label for="product_type">Product Type</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="product_type" id="product_type" placeholder="Product Type" required list="product_type_list">
                                    <datalist id="product_type_list">
                                        <option value="Walker">
                                        <option value="Wheel Chair">
                                        <option value="Walking Stick">
                                      </datalist>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4 text-right">
                                    <label for="product_qty">Product quantity</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="product_qty" id="product_qty" placeholder="Product Quantity" value="" required>
                                </div>
                            </div>
                            <br>
                            <div class="row container">
                                <div class="form-group col-md-4 text-right">
                                    <label for="product_image" class="form-label">Product Image</label>
                                </div>
                                <div class="col-md-8" >  
                                    <div>
                                        <input type="file" id="product_image" class="" name="product_image" required="true" value="{{old('product_image')}}" accept="image/png, image/jpeg, image/jpg," onclick="fileType(this.id);" >
                                    </div>                                    
                                </div>
                                {{-- <div class="text-right">
                                    <img src="" class="rounded float-right" id="output"  alt="" width="200" />
                                </div>    --}}
                            </div>
                            <div class="row" style="display: none" id="product_image_show">
                                <div class="col-md-8 text-right">
                                    <img src="" class="rounded " id="output"  alt="" width="200" />
                                </div>
                                <br>
                            </div>
                            <div class="row">
                                <div class="col-md-4 text-right">
                                    <label for="product_sale_price">Sale Price</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="product_sale_price" id="product_sale_price" placeholder="Product Sale Price" required>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4 text-right">
                                    <label for="product_deposit">Product Deposit</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="product_deposite" id="product_deposite" placeholder="Product Deposit" required>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4 text-right">
                                    <label for="product_rent">Product Rent</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="product_rent" id="product_rent" placeholder="Product Rent" required>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4 text-right">
                                    <label for="product_transport_cost">Product Transport Cost</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="product_transport_cost" id="product_transport_cost" placeholder="Transport Cost" required>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4 text-right">
                                    <label for="min_rent_percentage">Max discount %age Sale/Rent</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="number" class="form-control" name="min_rent_percentage" id="min_rent_percentage" placeholder="Max discount %age Sale/Rent" required>
                                </div>
                            </div>
                            <hr>
                            <center><button class="btn btn-primary" type="submit" name="submit" value="submit">Add New Product</button></center>
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