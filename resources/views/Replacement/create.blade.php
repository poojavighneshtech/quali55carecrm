@extends('header_and_sidebar')

@section('styles')
    <title>Replacement</title>
    <style>
        .glowing-border {
            border: 1px solid #5052b8;
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
                <div class="col text-primary" id="heading-filter" class="d-block">
                    <strong>Create Order</strong>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form action="{{route('replace-order.store')}}" method="POST">
                @csrf
                <div class="customer-heading">
                    <h5>Customer Details</h5>
                </div>
                <div class="customer-details">
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="customername">Customer Name: </label>
                            <span id="customername">{{$customer->shipping_first_name}}</span>
                            <input type="hidden" name="baseorderid" id="baceorderid" value="{{$customer->order_id}}">
                        </div>
                        <div class="col-md-4">
                            <label for="patientname">Patient Name: </label>
                            <span id="patientname">{{$customer->patient_name}}</span>
                        </div>
                        <div class="col-md-4">
                            <label for="contactno">Contact Number: </label>
                            <span id="contactno">{{$customer->mobileno}}</span>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-12">
                            <label for="contactno">Address: </label>
                            <span id="contactno">{{$customer->fulldetails}}</span>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row form-group">
                    <div class="col-md-4">
                        <label for="orderdate">Date</label>
                        <input type="date" name="orderdate" id="orderdate" class="form-control form-control-sm" value="{{date('Y-m-d')}}" required>
                    </div>
                    <div class="col-md-4">
                        <label for="orderassignedto">Order Assigned To</label>
                        <select name="orderassignedto" id="orderassignedto" class="form-control form-control-sm select selectpicker" title="Select Delboy" data-live-search="true" data-size="5" required>
                            @foreach($delboys as $delboy)
                                <option value="{{$delboy->username}}">{{$delboy->username}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="orderhelpers">Helpers</label>
                        <select name="orderhelpers[]" id="orderhelpers" class="form-control form-control-sm select selectpicker" data-live-search="true" data-size="5" multiple="multiple" required>
                            <option value="No Helper" selected>No Helper</option>
                            @foreach($delboys as $delboy)
                                <option value="{{$delboy->username}}">{{$delboy->username}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <hr>
                <div class="products">
                    @foreach($products as $key=>$product)
                        <div class="card card-body my-2 glowing-border">
                            <div class="row form-group">
                                <div class="col-md-12">
                                    {{-- <h5>{{$product->product_name}} - ({{$product->unique_id}})</h5> --}}
                                    <span>{{$product->product_name}} - ({{$product->unique_id}})</span>                                    
                                    <span><b>Rent:</b> {{$product->product_rent}}</span>
                                    <span><b>Deposit:</b> {{$product->product_deposite}}</span>
                                    {{-- <div class="float-right">
                                        <span>
                                            <input type="checkbox" name="products[{{$product->id}}][newproductadjustedrent]" id="newproductadjustedrent{{$key}}"> <label for="newproductadjustedrent{{$key}}">Adjust Rent</label>
                                        </span>
                                        <span>
                                            <input type="checkbox" name="products[{{$product->id}}][newproductadjusted]" id="newproductadjusted{{$key}}"> <label for="newproductadjusted{{$key}}">Adjust Deposit</label>
                                        </span>
                                    </div> --}}
                                </div>
                            </div>
                            <div class="row form-group text-dark">
                                <b>Drop Location</b>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-3">
                                    <input type="hidden" name="products[{{$product->id}}][dropwarehousetype]" id="group_name{{$key}}" value="">
                                    <select name="products[{{$product->id}}][dropwarehouseid]" data-id="{{$key}}" id="dropwarehouseid{{$key}}" data-rcount="{{$key}}" class="select selectpicker form-control form-control-sm Drop_at" data-size="5" data-live-search="true" title="Select Warehouse" required>
                                        <optgroup label="Virtual Warehouse">
                                            @foreach($product->q5c_warehouse_details as $q5c_warehouse_detail)
                                                <option value="{{$q5c_warehouse_detail->id}}">{{$q5c_warehouse_detail->wh_name}}-{{$q5c_warehouse_detail->wh_area}},{{$q5c_warehouse_detail->wh_city}}</option>
                                            @endforeach
                                        </optgroup>
                                        <optgroup label="Vendor Warehouse">
                                            @foreach($product->vendor_warehouse_details as $vendor_warehouse_detail)
                                                <option value="{{$vendor_warehouse_detail->id}}">{{$vendor_warehouse_detail->wh_name}}-{{$vendor_warehouse_detail->wh_area}},{{$vendor_warehouse_detail->wh_city}}</option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                            <div class="row form-group text-dark">
                                <b>Replaced Equipment</b>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-3">
                                    <select name="products[{{$product->id}}][newproductid]" id="newproductid{{$key}}" data-rcount="{{$key}}" class="select selectpicker form-control form-control-sm product" data-size="5" data-live-search="true" title="Select Product" required>
                                        @foreach($masterproducts as $masterproduct)
                                            <option value="{{$masterproduct->id}}">{{$masterproduct->product_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    {{-- <input type="number" name="products[{{$product->id}}][newproductrent]" id="newproductrent{{$key}}" class="form-control form-control-sm" placeholder="Rent" required> --}}
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <input type="checkbox" data-toggle="tooltip" data-placement="top" title="Adjust From Rent" aria-label="Adjust From Rent" class="mr-2"name="products[{{$product->id}}][newproductadjustedrent0]" id="newproductadjustedrent0{{$key}}">
                                                <input type="checkbox" data-toggle="tooltip" data-placement="top" title="Adjust From Deposit" aria-label="Adjust From Deposit" name="products[{{$product->id}}][newproductadjusteddeposit0]" id="newproductadjusteddeposit0{{$key}}">
                                            </div>
                                        </div>
                                        <input class="form-control form-control-sm" type="number" name="products[{{$product->id}}][newproductrent]" id="newproductrent{{$key}}" placeholder="Rent" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    {{-- <input type="number" name="products[{{$product->id}}][newproductdeposit]" id="newproductdeposit{{$key}}" class="form-control form-control-sm" placeholder="Deposit" required> --}}
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <input type="checkbox" data-toggle="tooltip" data-placement="top" title="Adjust From Rent" aria-label="Adjust From Rent" class="mr-2"name="products[{{$product->id}}][newproductadjustedrent1]" id="newproductadjustedrent1{{$key}}">
                                                <input type="checkbox" data-toggle="tooltip" data-placement="top" title="Adjust From Deposit" aria-label="Adjust From Deposit" name="products[{{$product->id}}][newproductadjusteddeposit1]" id="newproductadjusteddeposit1{{$key}}">
                                            </div>
                                        </div>
                                        <input type="number" name="products[{{$product->id}}][newproductdeposit]" id="newproductdeposit{{$key}}" class="form-control form-control-sm" placeholder="Deposit" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    {{-- <input type="number" name="products[{{$product->id}}][newproducttransport]" id="newproducttransport{{$key}}" class="form-control form-control-sm" placeholder="Transport" required> --}}
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <input type="checkbox" data-toggle="tooltip" data-placement="top" title="Adjust From Rent" aria-label="Adjust From Rent" class="mr-2"name="products[{{$product->id}}][newproductadjustedrent2]" id="newproductadjustedrent2{{$key}}">
                                                <input type="checkbox" data-toggle="tooltip" data-placement="top" title="Adjust From Deposit" aria-label="Adjust From Deposit" name="products[{{$product->id}}][newproductadjusteddeposit2]" id="newproductadjusteddeposit2{{$key}}">
                                            </div>
                                        </div>
                                        <input type="number" name="products[{{$product->id}}][newproducttransport]" id="newproducttransport{{$key}}" class="form-control form-control-sm" placeholder="Transport" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-3">
                                    <select name="products[{{$product->id}}][newvendorid]" id="newvendorid{{$key}}" data-rcount="{{$key}}" class="select selectpicker form-control form-control-sm vendor" data-size="5" data-live-search="true" title="Select Vendor" required>
                                        <option value="false" disabled>Select Product First</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="products[{{$product->id}}][newwarehouseid]" id="newwarehouseid{{$key}}" data-rcount="{{$key}}" class="select selectpicker form-control form-control-sm warehouse" data-size="5" data-live-search="true" title="Select Warehouse" required>
                                        <option value="false" disabled>Select Vendor First</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="products[{{$product->id}}][newbrandid]" id="newbrandid{{$key}}" data-rcount="{{$key}}" class="select selectpicker form-control form-control-sm brand" data-size="5" data-live-search="true" title="Select Brand" required>
                                        <option value="false" disabled>Select Warehouse First</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="products[{{$product->id}}][newbatchid]" id="newbatchid{{$key}}" data-rcount="{{$key}}" class="select selectpicker form-control form-control-sm batch" data-size="5" data-live-search="true" title="Select Batch" required>
                                        <option value="false" disabled>Select Brand First</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="products[{{$product->id}}][newinventoryid]" id="newinventoryid{{$key}}" data-rcount="{{$key}}" class="select selectpicker form-control form-control-sm inventory" data-size="5" data-live-search="true" title="Select Inventory" required>
                                        <option value="false" disabled>Select Batch First</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="submit text-center">
                    <button type="submit" name="submit" value="" class="btn btn-sm btn-outline-success">Generate Order</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(".Drop_at").change(function(){
            var id = $(this).data("id");
            // alert(id);
            var s = $(this).find(":selected").closest("optgroup");
            if($(s).attr("label") == 'Virtual Warehouse')
            {
                $("#group_name"+id).val('Virtual Warehouse');
            }
            else if($(s).attr("label") == 'Vendor Warehouse')
            {
                $("#group_name"+id).val('Vendor Warehouse');
            }
            else if($(s).attr("label") == 'Customer Location')
            {
                $("#group_name"+id).val('Customer Location');
            }
            $("#group_name"+id).val($(s).attr("label"));
            // alert($(s).attr("label"));
        });
        // Fetch Vendors //
        $(".product").change(function(){
            let rcount = $(this).data('rcount');
            $.ajax({
                type:"GET",
                url:"{{url('/')}}/get-inventory/Vendor/"+$(this).val()+"/"+$(this).val(),
                cache:false,
                success:function(res){
                    console.log(res);
                    $("#newvendorid"+rcount).find('option').remove().end();
                    $.each(res,function(key,vendor){
                        $("#newvendorid"+rcount).append('<option value="'+vendor.id+'">'+vendor.registered_name+'</option>');
                    });
                    $("#newvendorid"+rcount).selectpicker('refresh');
                },
                error:function(err){

                }
            });
        });

        // Fetch Warehouses //
        $(".vendor").change(function(){
            let rcount = $(this).data('rcount');
            $.ajax({
                type:"GET",
                url:"{{url('/')}}/get-inventory/Warehouse/"+$("#newproductid"+rcount).val()+"/"+$(this).val(),
                cache:false,
                success:function(res){
                    console.log(res);
                    $("#newwarehouseid"+rcount).find('option').remove().end();
                    $.each(res,function(key,warehouse){
                        $("#newwarehouseid"+rcount).append('<option value="'+warehouse.id+'">'+warehouse.wh_name+', '+warehouse.wh_area+', '+warehouse.wh_city+'</option>');
                    });
                    $("#newwarehouseid"+rcount).selectpicker('refresh');
                },
                error:function(err){

                }
            });
        });

        // Fetch Brand //
        $(".warehouse").change(function(){
            let rcount = $(this).data('rcount');
            $.ajax({
                type:"GET",
                url:"{{url('/')}}/get-inventory/Brand/"+$("#newproductid"+rcount).val()+"/"+$(this).val(),
                cache:false,
                success:function(res){
                    console.log(res);
                    $("#newbrandid"+rcount).find('option').remove().end();
                    $.each(res,function(key,brand){
                        $("#newbrandid"+rcount).append('<option value="'+brand.id+'">'+brand.brand_name+'</option>');
                    });
                    $("#newbrandid"+rcount).selectpicker('refresh');
                },
                error:function(err){

                }
            });
        });

        // Fetch Batch //
        $(".brand").change(function(){
            let rcount = $(this).data('rcount');
            $.ajax({
                type:"GET",
                url:"{{url('/')}}/get-inventory/Batch/"+$("#newproductid"+rcount).val()+"/"+$(this).val()+"/"+$("#newwarehouseid"+rcount).val(),
                cache:false,
                success:function(res){
                    console.log(res);
                    $("#newbatchid"+rcount).find('option').remove().end();
                    $.each(res,function(key,batch){
                        $("#newbatchid"+rcount).append('<option value="'+batch.id+'">'+batch.batch+'</option>');
                    });
                    $("#newbatchid"+rcount).selectpicker('refresh');
                },
                error:function(err){

                }
            });
        });

        // Fetch Inventory //
        $(".batch").change(function(){
            let rcount = $(this).data('rcount');
            $.ajax({
                type:"GET",
                url:"{{url('/')}}/get-inventory/Inventory/"+$("#newproductid"+rcount).val()+"/"+$(this).val(),
                cache:false,
                success:function(res){
                    console.log(res);
                    $("#newinventoryid"+rcount).find('option').remove().end();
                    $.each(res,function(key,inventory){
                        $("#newinventoryid"+rcount).append('<option value="'+inventory.id+'">'+inventory.inventory_id+'</option>');
                    });
                    $("#newinventoryid"+rcount).selectpicker('refresh');
                },
                error:function(err){

                }
            });
        });
    </script>
@endsection