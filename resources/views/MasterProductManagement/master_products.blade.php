
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
                    <div class="table-responsive jim-table-responsive">
                        <table class="table table-bordered" id="records" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Sr.No.</th>
                                    <th>Product Name</th>
                                    <th>Image</th>
                                    <th>Product Deposit</th>
                                    <th>Product Rent</th>
                                    <th>Min Rent Percentage</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                                
                            <tbody>
                                {{!$count=1}}
                                @foreach($product_details as $product_detail)
                                    <tr style="white-space:nowrap;">
                                        <td data-label="Sr.No.">{{$count}}</td>
                                        <td data-label="Product Name" class="text-wrap"><a href="{{url('/')}}/view_product_details/{{$product_detail['id']}}">{{$product_detail['product_name']}}</a></td>
                                        <td data-label="Image"><img src="{{$product_detail['product_img_url']}}" class="rounded " id="output"  alt="Not Found&emsp;" width="80" height="80" /></td>                                        
                                        <td data-label="Deposit">{{$product_detail['product_deposite']}}</td>
                                        <td data-label="Rent">{{$product_detail['product_rent']}}</td>
                                        <td data-label="Min Rent %">{{$product_detail['min_rent_percentage']}}</td>
                                        <td data-label="Action">
                                            <a href="{{url('/')}}/edit_master_product/{{$product_detail['id']}}" class="btn btn-success btn-sm rounded-0" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></a>
                                        </td>
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