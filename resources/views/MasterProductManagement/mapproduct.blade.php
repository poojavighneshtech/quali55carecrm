@extends('header_and_sidebar')

@section('styles')
@endsection

@section('content')
    <div class="container">
        @if(session()->has('error'))
            <div class="alert alert-danger">
                {{ session()->get('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if(session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <form action="{{route('map-product')}}" method="GET">
            <div class="card my-3">
                <div class="card-header text-white text-center bg-primary">
                    Map Products
                </div>
                <div class="card-body">
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="filterwebproductid">Web Product Id</label>
                            <input type="text" class="form-control form-control-sm" name="filterwebproductid" id="filterwebproductid" value="{{request()->get('filterwebproductid')}}">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-sm btn-outline-success">Search</button>
                            <a href="{{route('map-product')}}" class="btn btn-sm btn-outline-secondary ml-2">Clear</a>
                        </div>
                        <div class="col-md-4 d-flex justify-content-end">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="mapnewid" name="mapnewid">Map New Product</button>
                        </div>
                    </div>
                </div>
                <div class="table table-responsive jim-table responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Sr.No.</th>
                                <th>Web Product Id</th>
                                <th>Product Name</th>
                                <th>createdat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mappedproducts as $index=>$product)
                            <tr>
                                <td>{{$mappedproducts->firstItem()+$loop->index}}</td>
                                <td>{{$product->webproductid}}</td>
                                <td>{{$product->product_name}}</td>
                                <td>{{date('d-M-y H:i:s',strtotime($product->createdat))}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{$mappedproducts->links('Custom.Pagination.pagination')}}
                </div>
            </div>
        </form>
    </div>
    <div class="modal fade" id="mapnewmodal" tabindex="-1" aria-labelledby="mapnewmodalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('map-product')}}" method="GET">
                    <input type="hidden" name="request-type" id="request-type" value="Insert">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Map New Product</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row form-group">
                            <div class="col-md-12">
                                <label for="web_product_id">Web Product Id</label>
                                <input type="number" name="web_product_id" id="web_product_id" class="form-control form-control-sm" required>
                                <span id="existsspan" class="text-danger"></span>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-12">
                                <label for="master_product_id">Master Products</label>
                                <select class="select selectpicker form-control form-control-sm border border-dark" data-size="5" data-live-search="true" title="Select Master Products" name="master_product_id" id="master_product_id" required>
                                    @foreach($masterproducts as $index=>$product)
                                        <option value="{{$product->id}}">{{$product->product_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row form-group my-2">
                            <div class="col-md-12 text-center">
                                <button class="btn btn-sm btn-outline-success">Submit</button>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div> --}}
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>

        $("#mapnewid").click(function(){
            $("#existsspan").hide();
            $("#mapnewmodal").modal("show");
        })

        $("#web_product_id").on("input",function(){
            if($(this).val() && $(this).val().length == 13){
                let dataString = ({webproductid:$(this).val(),'state':"Check"});
                $.ajax({
                    type:"GET",
                    url:"{{route('map-product')}}",
                    data:dataString,
                    cache:false,
                    success:function(response){
                        console.log(response);
                        if(response.status == "Exists")
                        {
                            $("#existsspan").text("* Id already Exists in Product map table");
                            $("#existsspan").show();
                        }
                        else
                        {
                            $("#existsspan").text("");
                            $("#existsspan").hide();
                        }
                    },
                    error:function(err){
                        console.log(err);
                    }
                })
            }
            else
            {
                $("#existsspan").text("* Id should be 13 digits!");
                $("#existsspan").show();
            }
        });
    </script>
@endsection