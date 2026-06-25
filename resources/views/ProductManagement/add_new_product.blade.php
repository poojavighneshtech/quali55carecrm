@extends('header_and_sidebar')
@section('title')
    Admin: Add New Product
@endsection
@section('content')
    <form action="<?php echo url('/');?>/add_new_product" method="POST">
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
                                    <input type="text" class="form-control" name="product_type" id="product_type" placeholder="Product Type" required>
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
                                    <label for="min_rent_percentage">Min. Rent Percentage (In %)</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="number" class="form-control" name="min_rent_percentage" id="min_rent_percentage" placeholder="Minimum Rent Percentage" required>
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

</script>

@endsection