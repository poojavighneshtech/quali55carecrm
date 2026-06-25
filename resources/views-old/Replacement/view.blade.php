@extends('header_and_sidebar')

@section('styles')
    <title>Replacement</title>
    <style>
        .glowing-border {
            border: 2px solid #d60f0f;
            border-radius: 12px;
        }
    </style>
@endsection

@section('content')
    @if(session()->has('message'))
        <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            {{ session()->get('message')}} 
        </div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            {{ session()->get('error') }}
        </div>
    @endif 
    <div class="card my-3">
        <div class="card-header border-primary" id="filter_card">
            <div class="row">
                <div class="col text-primary" data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                    <strong>Replacement Orders</strong>
                </div>
                <div class="col-auto">
                    <a data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                        <i class="fa fa-chevron-down pull-right"></i>&emsp;<b>Filter</b>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body collapse" id="filter-collapse" aria-labelledby="headingTwo" data-parent="#filter_card">
            <form action="{{route('replace-order.index')}}" method="GET">
                <div class="row form-group">
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="filtercustomername">Customer Name</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" name="filtercustomername" id="filtercustomername" class="form-control form-control-sm" value="{{request()->get('filtercustomername')}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="filterpatientname">Patient Name</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" name="filterpatientname" id="filterpatientname" class="form-control form-control-sm" value="{{request()->get('filterpatientname')}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="date" name="filterstartdate" id="filterstartdate" class="form-control form-control-sm" value="{{request()->get('filterstartdate')}}">
                            </div>
                            <div class="col-md-4">
                                <input type="date" name="filterenddate" id="filterenddate" class="form-control form-control-sm" value="{{request()->get('filterenddate')}}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-primary" name="create" id="create" data-toggle="modal" data-target="#createReplacement">Replace Product</button>
                    <button type="submit" class="btn btn-sm btn-outline-success">Submit</button>
                    <a href="{{route('replace-order.index')}}" class="btn btn-sm btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    
        <div class="table table-responsive jim-table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Delivery Order Id</th>
                        <th>Pickup Order Id</th>
                        <th>Customer Name</th>
                        <th>Patient Name</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $key=>$order)
                        <tr>
                            <td data-label="Date">{{$order->DelDate}}</td>
                            <td data-label="Delivery Order Id">{{$order->deliveryorderid}}</td>
                            <td data-label="Pickup Order Id">{{$order->pickuporderid}}</td>
                            <td data-label="Customer Name">{{$order->shipping_first_name}}</td>
                            <td data-label="Patient Name">{{$order->patient_name}}</td>
                            <td data-label="Total Amount">{{$order->TotalAmt}}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7">No Orders Found</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{$orders->withPath(url()->full())->links('Custom.Pagination.pagination')}}
        </div>
    </div>
    <div class="modal fade" id="createReplacement" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Search Customer / Patient</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row form-group">
                        <div class="col-md-6">
                            <label for="searchcustomertxt">Customer/Patient Name, Mobile No.</label>
                            <input type="text" name="searchcustomertxt" id="searchcustomertxt" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button class="btn btn-sm btn-outline-primary" id="search-customer" name="search-customer"><i class="fas fa-search"></i> Search</button>
                        </div>
                    </div>
                    <div class="accordian" id="replacementAccordian">
                        <div class="card d-none customer-details-card">
                            <div class="card-header" id="headingCustpatientDetails">
                                <h2 class="mb-0">
                                <button class="btn btn-link btn-block text-left collapsed" id="customer-details-heading" type="button" data-toggle="collapse" data-target="#collapseCustpatientDetails" aria-expanded="false" aria-controls="collapseCustpatientDetails">
                                    Customer/Patient Details
                                </button>
                                </h2>
                            </div>
                            <div id="collapseCustpatientDetails" class="collapse" aria-labelledby="headingCustpatientDetails" data-parent="#replacementAccordian">
                                <div class="card-body">
                                    <div class="customer-details">
                                        <div class="table table-responsive jim-table-responsive">
                                            <table class="table" id="customer-details-table">
                                                <thead>
                                                    <tr>
                                                        <th>Order Date</th>
                                                        <th>Customer Name</th>
                                                        <th>Patient Name</th>
                                                        <th>Address</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="customer-table-records">
                
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card d-none product-details-card">
                            <div class="card-header" id="headingProductDetails">
                                <h2 class="mb-0">
                                <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseProductDetails" aria-expanded="false" aria-controls="collapseProductDetails">
                                    Product Details
                                </button>
                                </h2>
                            </div>
                            <div id="collapseProductDetails" class="collapse" aria-labelledby="headingProductDetails" data-parent="#replacementAccordian">
                                <div class="card-body">
                                    <form action="{{route('replace-order-create')}}" method="POST">
                                        @csrf
                                        <div class="product-details">
                                            <div class="table table-responsive jim-table-responsive">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            
                                                            <th></th>
                                                            <th>Delivery Date</th>
                                                            <th>Product Name</th>
                                                            <th>Vendor</th>
                                                            <th>Warehouse</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="product-table-records">
                    
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="button-div d-flex justify-content-center">
                                                <button type="submit" class="btn btn-sm btn-outline-success" name="generatereplacement" id="generatereplacement">Generate Replacement</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $("#search-customer").click(function(){
            if($("#searchcustomertxt").val().length == 0){
                $("#searchcustomertxt").addClass('glowing-border');
            }
            else{
                $("#searchcustomertxt").removeClass('glowing-border');
                let dataString = ({searchcustomertxt:$("#searchcustomertxt").val(),reqtype:"getcustomers"});
                $("#customer-table-records").empty();
                $(".customer-details-card").addClass('d-none');
                $.ajax({
                    type:"GET",
                    url:"{{route('replace-order.create')}}",
                    cache:false,
                    data:dataString,
                    success:function(res){
                        console.log(res);
                        var tr = null;
                        $.each(res,function(key,value){
                            tr += '<tr>';
                                tr += '<td data-label="Order Date">'+value[0].DelDate+'</td>';
                                tr += '<td data-label="Customer Name">'+value[0].shipping_first_name+'</td>';
                                tr += '<td data-label="Patient Name">'+value[0].patient_name+'</td>';
                                tr += '<td data-label="Address">'+value[0].fulldetails+'</td>';
                                // tr += '<td><a href="'+"{{route('replace-order-create')}}?id="+key+'" class="btn btn-sm btn-outline-primary">View</a></td>';
                                tr += '<td><button type="button" class="btn btn-sm btn-outline-primary viewproducts" onclick="fetchProducts('+key+')" id="'+key+'">View</button></td>';
                            tr += '</tr>';
                        });
                        $("#customer-table-records").append(tr);
                        $("#customer-details-table").dataTable('refresh');
                        $(".customer-details-card").removeClass('d-none');
                    },
                    error:function(err){
                        console.log(err);
                        $(".customer-details-card").addClass('d-none');
                    }
                });
            }
        });
        function fetchProducts(id){
            console.log("Products");
            $("#product-table-records").empty();
            let dataString = ({leadid:id,reqtype:"productdetails"});
            $.ajax({
                type:"GET",
                url:"{{route('replace-order.create')}}",
                data:dataString,
                cache:false,
                success:function(res){
                    console.log(res);
                    var tr = null;
                    $.each(res,function(key,value){
                        tr += '<tr>';
                            tr += '<td><input type="checkbox" name="checkedproducts[]" id="'+key+'" value="'+value.id+'"></td>';
                            tr += '<td data-label="Delivery Date">'+value.creation_date+'</td>';
                            tr += '<td data-label="Product Name">'+value.product_name+'</td>';
                            tr += '<td data-label="Patient Name">'+value.registered_name+'</td>';
                            tr += '<td data-label="Address">'+value.wh_name+', '+value.wh_area+', '+value.wh_city+'</td>';
                        tr += '</tr>';
                    });
                    $("#product-table-records").append(tr);
                    $("#product-details-table").dataTable('refresh');
                    $(".product-details-card").removeClass('d-none');
                },
                error:function(err){
                    console.log(err);
                }
            });
        }
    </script>
@endsection