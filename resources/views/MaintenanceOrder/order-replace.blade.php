@extends('header_and_sidebar')
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Order Replace</title>
    @section('styles')
        <style>
        </style>
    @endsection
</head>

<body id="page-top">	
        
    @section('content')
        
        @if(session()->has('message') || session()->has('message_pop') )
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                {{ session()->get('message')}} @if(session()->has('collection_url'))<small><a class="" href="{{ session()->get('collection_url')}}">See Order Here</a></small>@endif
                {{ session()->get('message_pop')}}
            </div>
        @endif
        @if(session()->has('reminder_msg'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                {{ session()->get('reminder_msg')}} 
            </div>
        @endif
        @if(session()->has('message_delete'))
            <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                {{ session()->get('message_delete') }}
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

        <div class="card">
            <div class="card-header">
                <strong>Customer Details</strong>
            </div>
            <div class="card-body container-fluid">
                <div class="row">
                    <h5 class="card-title"><strong> Customer Name : </strong></h5>{{$orderData->customer_details->customer_name}}
                </div>
                <div class="row">
                    <strong>Contact No : </strong>{{$orderData->customer_details->primary_contact_no}}
                </div>
                <div class="row my-2">
                    <strong>Patient Name : </strong>{{$orderData->patient_name}}
                </div>
                <div class="row">
                    <strong>Address : </strong> <Address>{{$orderData->customer_details->address_line_1}}, {{$orderData->customer_details->address_line_2}}, {{$orderData->customer_details->area}}, {{$orderData->customer_details->landmark}}, {{$orderData->customer_details->location}}, {{$orderData->customer_details->city}}, {{$orderData->customer_details->pincode}}, {{$orderData->customer_details->state}}, {{$orderData->customer_details->country}}</Address>
                </div>
            </div>
        </div>

        <div class="card my-2">
            <div class="card-header">
                <strong>Products</strong>
            </div>
            <form action="{{route('order-replace',[$orderData->order_id])}}" method="post">
                @csrf
                <div class="table table-responsive">
                    <table class="table">
                        <thead>
                            <th>Action</th>
                            <th>Product Name</th>
                            <th>Rent</th>
                            <th>Deposit</th>
                            <th>Vendor Name</th>
                        </thead>
                        <tbody>
                            @foreach ($orderData->order_details as $key => $product)
                            <tr>
                                <td>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="selected_product[]" id="selected_product{{$key}}" value="{{$product->order_details_id}}"
                                            @if(!empty($selectedData) && in_array($product->order_details_id,request()->get('selected_product'))) checked @endif>
                                        <label class="custom-control-label" for="selected_product{{$key}}">Select</label>
                                    </div>
                                </td>
                                <td>{{$product->product_name}}</td>
                                <td>{{$product->product_rent}}</td>
                                <td>{{$product->product_deposite}}</td>
                                <td>{{$product->vendor_name}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="row justify-content-center mb-2">
                    <button  type="submit" class="btn btn-outline-primary">Replace</button>
                </div>
            </form>
            {{-- <div class="card-body">
            </div> --}}
        </div>

        @if($selectedData)
            <div class="card">
                <div class="card-header">
                    Replace Product
                </div>
                <form action="{{route('order-replace-generate')}}" method="post">
                    @csrf
                    <input type="hidden" name="customer_id" value="{{$orderData->customer_details->cust_id}}">
                    <ul class="list-group list-group-flush">
                        @foreach ($selectedData as $key=>$product)
                            <li class="list-group-item border border-dark" id="li_element{{$key}}">
                                <div class="row">
                                    <div class="col-auto mr-auto">
                                        <strong>{{$product->product_name}}</strong><small>({{$product->unique_id}})</small>
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-sm btn-outline-danger fa fa-window-close btn-product-close" data-order_details_id={{$product->order_details_id}} value="{{$key}}"></button>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-2">
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="old_product_rent{{$key}}"><strong>Rent</strong></span>
                                            </div>
                                            <input type="number" class="form-control form-control-sm" name="replaced_data[{{$product->order_details_id}}][old][product_rent]" id="" value="{{$product->product_rent}}" readonly>
                                            <input type="hidden" name="replaced_data[{{$product->order_details_id}}][old][product_id]" value="{{$product->product_id}}">
                                            <input type="hidden" name="replaced_data[{{$product->order_details_id}}][old][order_details_id]" value="{{$product->order_details_id}}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="old_product_deposit{{$key}}"><strong>Deposit</strong></span>
                                            </div>
                                            <input type="number" class="form-control form-control-sm" name="replaced_data[{{$product->order_details_id}}][old][product_deposit]" id="" value="{{$product->product_deposite}}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="old_product_transport{{$key}}"><strong>Transport</strong></span>
                                            </div>
                                            <input type="number" class="form-control form-control-sm" name="replaced_data[{{$product->order_details_id}}][old][transport]" id="" value="{{$product->transport}}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="old_product_vendor{{$key}}"><strong>Vendor</strong></span>
                                            </div>
                                            <input type="text" class="form-control form-control-sm" name="replaced_data[{{$product->order_details_id}}][old][vendor]" id="" value="{{$product->vendor_name}}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="old_product_warehouse{{$key}}"><strong>Wrehouse</strong></span>
                                            </div>
                                            <input type="text" class="form-control form-control-sm" name="replaced_data[{{$product->order_details_id}}][old][warehouse]" id="" value="{{$product->wh_name}}" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-2">
                                        <strong>Adjust Amount :</strong>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="old_product_rent{{$key}}"><strong>Rent</strong></span>
                                            </div>
                                                <input type="number" class="form-control form-control-sm" name="replaced_data[{{$product->order_details_id}}][adjust][rent]" id="" value="0" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="old_product_deposit{{$key}}"><strong>Deposit</strong></span>
                                            </div>
                                            <input type="number" class="form-control form-control-sm" name="replaced_data[{{$product->order_details_id}}][adjust][deposit]" id="" value="0" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group input-group-sm mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="old_product_transport{{$key}}"><strong>Transport</strong></span>
                                            </div>
                                            <input type="number" class="form-control form-control-sm" name="replaced_data[{{$product->order_details_id}}][adjust][transport]" id="" value="0" disabled>
                                        </div>
                                    </div>
                                </div>
    
                                <div class="dropdown-divider"></div>
    
                                <div class="row mb-2">
                                    <div class="col-md-12">
                                        <strong>New Product Details:</strong>
                                    </div>
                                    <div class="col-md-12 mt-2">
                                        <div class="row">
                                            <div class="col-md-4 mt-4">
                                                <select class="selectpicker form-control form-control-sm border border-dark product-new-select" name="replaced_data[{{$product->order_details_id}}][new][product_id]" id="new_product{{$key}}"
                                                    title="Select Product" data-size="5" data-live-search="true" data-id={{$key}} data-sale_rental="{{$product->sale_rental}}" required>
                                                    @foreach ($productList as $keyP=>$productL)
                                                        <option value="{{$productL->id}}">{{$productL->product_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="input-group input-group-sm mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="new_product_rent{{$key}}"><strong>Rent</strong></span>
                                                            </div>
                                                                <input type="number" class="form-control form-control-sm" name="replaced_data[{{$product->order_details_id}}][new][rent]" id="" value="0" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="input-group input-group-sm mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="new_product_deposit{{$key}}"><strong>Deposit</strong></span>
                                                            </div>
                                                            <input type="number" class="form-control form-control-sm" name="replaced_data[{{$product->order_details_id}}][new][deposit]" id="" value="0" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="input-group input-group-sm mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="new_product_transport{{$key}}"><strong>Transport</strong></span>
                                                            </div>
                                                            <input type="number" class="form-control form-control-sm" name="replaced_data[{{$product->order_details_id}}][new][transport]" id="" value="0" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <select class="selectpicker form-control form-control-sm border border-dark product-new-vendor" name="replaced_data[{{$product->order_details_id}}][new][vendor]" id="new_vendor{{$key}}"
                                                            title="Select Vendor" data-size="5" data-live-search="true" data-id={{$key}} required>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <select class="selectpicker form-control form-control-sm border border-dark product-new-warehouse" name="replaced_data[{{$product->order_details_id}}][new][warehouse]" id="new_warehouse{{$key}}"
                                                            title="Select Warehouse" data-size="5" data-live-search="true" data-id={{$key}} required>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <select class="selectpicker form-control form-control-sm border border-dark product-new-brand" name="replaced_data[{{$product->order_details_id}}][new][brand]" id="new_brand{{$key}}"
                                                            title="Select Brand" data-size="5" data-live-search="true" data-id={{$key}} required>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <select class="selectpicker form-control form-control-sm border border-dark product-new-batch" name="replaced_data[{{$product->order_details_id}}][new][batch]" id="new_batch{{$key}}"
                                                            title="Select Batch" data-size="5" data-live-search="true" data-id={{$key}} required>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <select class="selectpicker form-control form-control-sm border border-dark product-new-inventoryid" name="replaced_data[{{$product->order_details_id}}][new][inventory_id]" id="new_inventoryid{{$key}}"
                                                            title="Select Inventory Id" data-size="5" data-live-search="true" data-id={{$key}} required>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                        <li class="list-group-item border border-dark" id="li_element">
                            <div class="row">
                                <div class="col-md-2">
                                   <label for=""><strong>Delivery Payment Mode :</strong></label>
                                </div>
                                <div class="col-md-2">
                                    <div class="btn-group btn-group-toggle btn-group-sm col" data-toggle="buttons">
                                        <label class="btn btn-outline-primary in">
                                            <input type="radio" name="delivery_product_type" id="radio_cash" value="Cash" required> Cash
                                        </label>
                                        <label class="btn btn-outline-primary out">
                                            <input type="radio" name="delivery_product_type" id="radio_online" value="Online" required> Online
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label for=""><strong>Next Renewal Date :</strong></label>
                                </div>
                                <div class="col-md-2">
                                    <input type="date" class="form-control form-control-sm" name="next_renewal_date" id="next_renewal_date" required>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div class="row justify-content-center my-2">
                        <button type="submit" class="btn btn-sm btn-outline-success">Generate order</button>
                    </div>
                </form>
            </div>
        @endif  
        <div class="modal fade" id="remove_product_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Close Product</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Are you sure remove this product ?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" >Yes</button>
                    </div>
                </div>
            </div>
        </div>

       
    @endsection
</body>
@section('script')
   <script>
        $('.product-new-select').on('change',function(){
            let productId = $(this).val();
            let dataId = $(this).data('id');
            let productType = $(this).data('sale_rental');
            fetchVendor(productId,productType,dataId);
        })

        $('.product-new-vendor').on('change',function(){
            let dataId = $(this).data('id');
            let productId = $('#new_product'+dataId).val();
            let vendorId = $(this).val();
            let productType = $('#new_product'+dataId).data('sale_rental');
            fetchWarehouse(productId,vendorId,productType,dataId)
        });

        $('.product-new-warehouse').on('change',function(){
            let dataId = $(this).data('id');
            let productId = $('#new_product'+dataId).val();
            let vendorId = $('#new_vendor'+dataId).val();
            let warehouseId = $(this).val();
            let productType = $('#new_product'+dataId).data('sale_rental');
            fetchBrand(productId,vendorId,warehouseId,productType,dataId)
        });

        $('.product-new-brand').on('change',function(){
            let dataId = $(this).data('id');
            let productId = $('#new_product'+dataId).val();
            let vendorId = $('#new_vendor'+dataId).val();
            let warehouseId = $('#new_warehouse'+dataId).val();
            let brandId = $(this).val();
            let productType = $('#new_product'+dataId).data('sale_rental');
            fetchBatch(productId,vendorId,warehouseId,brandId,productType,dataId)
        });
        $('.product-new-batch').on('change',function(){
            let dataId = $(this).data('id');
            let productId = $('#new_product'+dataId).val();
            let vendorId = $('#new_vendor'+dataId).val();
            let warehouseId = $('#new_warehouse'+dataId).val();
            let brandId = $('#new_brand'+dataId).val();
            let batchId = $(this).val();
            let productType = $('#new_product'+dataId).data('sale_rental');
            fetchInventoryId(productId,vendorId,warehouseId,brandId,batchId,productType,dataId)
        });

        $('.product-new-inventoryid').on('change',function(){
            let invId = $(this).val();
            let dataId = $(this).data('id');
            //$('.product-new-inventory option').attr("disabled",false);
            $('.product-new-inventoryid option[value='+invId+']').prop("selected",false);
            $('.product-new-inventoryid option[value='+invId+']').attr("disabled",true);
            $('.product-new-inventoryid').selectpicker('render');
            $('.product-new-inventoryid').selectpicker('refresh');
            
            $('#new_inventoryid'+dataId+' option[value='+invId+']').attr("disabled",false);
            $('#new_inventoryid'+dataId+' option[value='+invId+']').prop("selected",true);
            $('#new_inventoryid'+dataId).selectpicker('render');
            $('#new_inventoryid'+dataId).selectpicker('refresh');
        });

        $('.btn-product-close').on('click',function() {
            let id = $(this).val();
            let ordDetId = $(this).data('order_details_id');
            //$('#remove_product_modal').modal('show');
            $('#li_element'+id).remove();

        })



        function fetchVendor(productId,productType,dataId){
            let dataString = ({_token:"{{ csrf_token() }}",product_id:""+productId,product_type:""+productType,request_type:"fetch_vendor"});
            $.ajax({
                type: "POST",
                url: "{{url('/')}}/order-replace-fetch-details",
                data: dataString,
                cache:false,
                success: function (data)
                {
                    $("#new_vendor"+dataId)
                        .find("option")
                        .remove()
                        .end();

                    //console.log(data[0].id);
                    Object.keys(data).forEach(function(key){
                        $('#new_vendor'+dataId).append($("<option></option>")
                            .attr("value", data[key].id)
                            .text(data[key].registered_name));
                    });
                    $('#new_vendor'+dataId).selectpicker('refresh');
                }
            });
        }

        function fetchWarehouse(productId,vendorId,productType,dataId){
            let dataString = ({_token:"{{ csrf_token() }}",product_id:""+productId,vendor_id:""+vendorId,product_type:""+productType,request_type:"fetch_warehouse"});
            $.ajax({
                type: "POST",
                url: "{{url('/')}}/order-replace-fetch-details",
                data: dataString,
                cache:false,
                success: function (data)
                {
                    console.log(data);
                    $("#new_warehouse"+dataId)
                        .find("option")
                        .remove()
                        .end();

                    Object.keys(data).forEach(function(key){
                        $('#new_warehouse'+dataId).append($("<option></option>")
                            .attr("value", data[key].id)
                            .text(data[key].wh_name+","+data[key].wh_area+", "+data[key].wh_city));
                    });
                    $('#new_warehouse'+dataId).selectpicker('refresh');
                }
            });
        }

        function fetchBrand(productId,vendorId,warehouseId,productType,dataId){
            let dataString = ({_token:"{{ csrf_token() }}",product_id:""+productId,vendor_id:""+vendorId,warehouse_id:""+warehouseId,product_type:""+productType,request_type:"fetch_brand"});
            $.ajax({
                type: "POST",
                url: "{{url('/')}}/order-replace-fetch-details",
                data: dataString,
                cache:false,
                success: function (data)
                {
                    console.log(data);
                    $("#new_brand"+dataId)
                        .find("option")
                        .remove()
                        .end();

                    Object.keys(data).forEach(function(key){
                        $('#new_brand'+dataId).append($("<option></option>")
                            .attr("value", data[key].id)
                            .text(data[key].brand_name));
                    });
                    $('#new_brand'+dataId).selectpicker('refresh');
                }
            });
        }

        function fetchBatch(productId,vendorId,warehouseId,brandId,productType,dataId){
            let dataString = ({_token:"{{ csrf_token() }}",product_id:""+productId,vendor_id:""+vendorId,warehouse_id:""+warehouseId,brand_id:""+brandId,product_type:""+productType,request_type:"fetch_batch"});
            $.ajax({
                type: "POST",
                url: "{{url('/')}}/order-replace-fetch-details",
                data: dataString,
                cache:false,
                success: function (data)
                {
                    console.log(data);
                    $("#new_batch"+dataId)
                        .find("option")
                        .remove()
                        .end();

                    Object.keys(data).forEach(function(key){
                        $('#new_batch'+dataId).append($("<option></option>")
                            .attr("value", data[key].id)
                            .text(data[key].batch));
                    });
                    $('#new_batch'+dataId).selectpicker('refresh');
                }
            });
        }

        function fetchInventoryId(productId,vendorId,warehouseId,brandId,batchId,productType,dataId){
            let dataString = ({_token:"{{ csrf_token() }}",product_id:""+productId,vendor_id:""+vendorId,warehouse_id:""+warehouseId,brand_id:""+brandId,batch_id:""+batchId,product_type:""+productType,request_type:"fetch_inventory_id"});            
            $.ajax({
                type: "POST",
                url: "{{url('/')}}/order-replace-fetch-details",
                data: dataString,
                cache:false,
                success: function (data)
                {
                    $("#new_inventoryid"+dataId)
                        .find("option")
                        .remove()
                        .end();

                    Object.keys(data).forEach(function(key){
                        $('#new_inventoryid'+dataId).append($("<option></option>")
                            .attr("value", data[key].id)
                            .text(data[key].inventory_id));
                    });
                    $('#new_inventoryid'+dataId).selectpicker('refresh');
                }
            });
        }
        
   </script>
@endsection
</html>