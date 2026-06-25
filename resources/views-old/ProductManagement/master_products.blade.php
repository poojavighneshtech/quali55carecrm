@extends('header_and_sidebar')
@section('title')
    Master Products 
@endsection
@section('content')        
        <div class="container-fluid">
            <!-- DataTales Example -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Products List</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Sr.No.</th>
                                    <th>Product Name</th>
                                    <th>Product Details</th>
                                    <th>Product type</th>
                                    <th>Product Deposit</th>
                                    <th>Product Rent</th>
                                    <th>Min Rent Percentage</th>
                                </tr>
                            </thead>
                                
                            <tbody>
                                {{!$count=1}}
                                @foreach($product_details as $product_detail)
                                    <tr style="white-space:nowrap;">
                                        <td>{{$count}}</td>
                                        <td>{{$product_detail['product_name']}}</td>
                                        <td>{{$product_detail['product_details']}}</td>
                                        <td>{{$product_detail['product_type']}}</td>
                                        <td>{{$product_detail['product_deposite']}}</td>
                                        <td>{{$product_detail['product_rent']}}</td>
                                        <td>{{$product_detail['min_rent_percentage']}}</td>
                                    </tr>
                                    {{!$count=$count+1}}
                                @endforeach                           
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>	                    
        </div>
@endsection

@section('script')
@endsection