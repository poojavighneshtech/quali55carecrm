@extends('header_and_sidebar')
<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        
        <title>Edit Delivery Order</title>
        {{-- <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script> --}}
        <link rel="stylesheet" href="{{url('/')}}/assets/dist/toast.min.css ">
        <!-- Boostrap 4 CSS -->
   
        @section('styles')
        <style>
            #filter_card{
                position: relative;
            }
            #h6_filter{
                position: absolute;
                right: 50%;
                top: -0.5rem;
                /* z-index: -100; */
            }
            .overlay-card {
                pointer-events: none;
                opacity: 0.4;
            }
        </style>
        @endsection
    </head>

<body id="page-top">	
        <!-- Page Wrapper -->
        
    @section('content')
        @if(session()->has('message_delete'))
            <div class="alert alert-danger">
                {{ session()->get('message_delete') }}
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
        <br>
        <div class="col-md-12">
            
            <div class="card">
                <div class="card-header text-center">
                    <strong>Customer Details</strong>
                </div>
                <div class="card-body">
                    <h5 class="card-title"><strong>Customer Name :</strong> {{$response[0]->customer_name}}</h5>
                    <h6 class="card-title"><strong>Patient Name :</strong> {{$response[0]->patient_name}}
                        @if($response[0]->lead_source != 'B2B Cust' && $response[0]->order_status !='Delivered')
                            <button class="btn btn-sm" id="edit_patient_name" data-toggle="tooltip" data-placement="bottom" title="Edit Addess">
                                        <i class="fa fa-edit" aria-hidden="true"></i>
                            </button>
                        @endif</h6>
                    <h6 class="card-subtitle mb-2"><strong>Contact No : </strong>{{$response[0]->primary_contact_no}}</h6>
                    <address class="card-text">
                        <strong>Address : </strong>{{$response[0]->address_line_1.", ".$response[0]->address_line_2.", ".$response[0]->landmark.", ".$response[0]->area.", ".$response[0]->city."- ".$response[0]->pincode}}
                        @if($response[0]->lead_source != 'B2B Cust' && $response[0]->order_status !='Delivered')
                            <button class="btn btn-sm" id="edit_address" data-toggle="tooltip" data-placement="bottom" title="Edit Addess">
                                        <i class="fa fa-edit" aria-hidden="true"></i>
                            </button>
                        @endif
                    </address>
                </div>
            </div>
            <br>
            <div class="card flex-column align-start">
                <div class="card-header d-flex w-100 justify-content-between">
                    <p class="mb-1"><strong>Product Details</strong></p>
                            {{-- hidden values --}}
                            <input type="hidden" name="order_id" id="order_id" value={{$response[0]->order_id}}>
                            <input type="hidden" name="customer_id" id="customer_id" value={{$response[0]->cust_id}}>
                            <input type="hidden" name="creation_date" id="creation_date" value={{$response[0]->creation_date}}>
                            <button id="add_equipment" name="add_equipment" class="btn btn-outline-primary btn-sm">Add Product</button>
                        </div>
                        <div class="list-group list-group-flush">
                            @foreach($response as $key => $value)
                        <div class="list-group-item list-group-item-action flex-column align-items-start @if($value->status == 'Cancel'){{'overlay-card'}}@endif">
                                    {{--hidden values--}}
                                    <input type="hidden" name="hide_prod_name" id="hide_prod_name{{$key}}" value="{{$value->product_name}}">
                                    
                                    <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1 @if($value->status == 'Cancel'){{'text-danger'}}@endif">
                                    @if($value->status == 'Cancel')
                                        <span class="text-danger">Removed -</span>
                                    @endif
                                    {{$value->product_name}} - <span><small>{{$value->upgraded}}</small></span><small>({{$value->inventory_id}})</small>
                                </h5>
                                <div class="text-nowrap" role="group">
                                            <button class="btn btn-outline-secondary btn-sm adjust-deposit" id="adjustDeposit{{$key}}" value="Adjust-Deposit" data-id="{{$key}}" data-customer_id="{{$response[0]->cust_id}}" data-order_details_id="{{$value->order_details_id}}" data-product_type = "{{$value->sale_rental}}" 
                                                data-product_id = {{$value->product_id}} data-product_qty = {{$value->product_qty}}
                                                data-toggle="tooltip" data-placement="bottom" title="Adjust Deposit">
                                                <i class="fas fa-exchange-alt"></i>
                                            </button>
                                    <button class="btn btn-outline-primary btn-sm edit-product" id="editProduct{{$key}}" value="Edit" data-id="{{$key}}" data-order_details_id="{{$value->order_details_id}}" data-product_type = "{{$value->sale_rental}}" 
                                            data-product_id = {{$value->product_id}} data-product_qty = {{$value->product_qty}}
                                            data-toggle="tooltip" data-placement="bottom" title="Edit Product">
                                            <i class="fa fa-edit" aria-hidden="true"></i>
                                    </button>
                                    @if(count($response)>1)
                                        <button class="btn btn-outline-danger btn-sm delete-product" id="deleteProduct{{$key}}" value="Delete" data-id="{{$key}}" 
                                                data-order_details_id="{{$value->order_details_id}}"
                                                data-toggle="tooltip" data-placement="bottom" title="Delete Product">
                                                <i class="fa fa-window-close" aria-hidden="true"></i>
                                        </button>
                                    @else
                                        <button class="btn btn-outline-danger btn-sm cant-delete" id="cantDeleteProduct{{$key}}" value="Cant Delete" data-id="{{$key}}" 
                                                data-order_details_id="{{$value->order_details_id}}"
                                                data-toggle="tooltip" data-placement="bottom" title="Cant Delete">
                                                <i class="fa fa-window-close" aria-hidden="true"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-auto">
                                    <b><span>Type : </span></b><span id="product_type{{$key}}">{{$value->sale_rental}}</span>
                                </div>
                                @if($value->offered_rent != $value->product_rent)
                                    <div class="col-auto">
                                        <b><span>Offered Rent : </span></b><span id="offered_rent{{$key}}">{{$value->offered_rent}}</span>
                                    </div>
                                @endif
                                <div class="col-auto">
                                    <b><span>Rent : </span></b><span id="product_rent{{$key}}">{{$value->product_rent}}</span>
                                </div>
                                @if($value->offered_deposit != $value->product_deposite)
                                    <div class="col-auto">
                                        <b><span>Offered Deposit : </span></b><span id="offered_deposit{{$key}}">{{$value->offered_deposit}}</span>
                                    </div>
                                @endif
                                <div class="col-auto">
                                    <b><span>Deposit : </span></b><span id="product_deposite{{$key}}">{{$value->product_deposite}}</span>
                                </div>
                                @if($value->offered_transport != $value->transport)
                                    <div class="col-auto">
                                        <b><span>Offered Transport : </span></b><span id="offered_transport{{$key}}">{{$value->offered_transport}}</span>
                                    </div>
                                @endif
                                <div class="col-auto">
                                    <b><span>Transport : </span></b><span id="transport{{$key}}">{{$value->transport}}</span>
                                </div>
                                <div class="col-auto">
                                    <b><span>Vendor : </span></b><span id="vendor_name{{$key}}">{{$value->vendor_name}}</span>
                                </div>
                                <div class="col-auto">
                                    <b><span>Warehouse : </span></b><span id="warehouse_address{{$key}}">{{$value->warehouse_name.", ".$value->warehouse_area.", ".$value->warehouse_city}}</span>
                                </div>
                                <div class="col-auto">
                                    <b><span >Product Brand : </span></b><span id="product_brand{{$key}}">{{$value->brand_name}}</span>
                                </div>
                                <div class="col-auto">
                                    <b><span >Product Batch : </span></b><span id="product_batch{{$key}}">{{$value->product_batch}}</span>
                                </div>
                                <div class="col-auto">
                                    <b><span >Inventory Id : </span></b><span id="inventory_id{{$key}}">{{$value->inventory_id}}</span>
                                </div>
                            </div>
                            @if($value->remark)
                                <div class="row">
                                    <div class="col-auto text-success">
                                        <b><span >Remark : </span></b><span id="remark{{$key}}">{{$value->remark}}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal_update_product_details" tabindex="-1" role="dialog" aria-labelledby="update_product_details" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="update_product_details">Update Product Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    {{-- <form class="form" method="post" action="{{url('/')}}/updateStatus">
                        {{csrf_field()}} --}}
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <span >Product Name : </span>
                                    <input type="hidden" name="hidden_product_id" id="hidden_product_id">
                                    <input type="hidden" name="hidden_order_details_id" id="hidden_order_details_id">
                                    <input type="hidden" name="hidden_product_qty" id="hidden_product_qty">
                                    <input type="hidden" name="hidden_key" id="hidden_key">
                                    <input type="hidden" name="hidden_vendor_id" id="hidden_vendor_id">
                                    <input type="hidden" name="hidden_warehouse_id" id="hidden_warehouse_id">
                                    <input type="hidden" name="hidden_brand_id" id="hidden_brand_id">
                                    <input type="hidden" name="hidden_batch_id" id="hidden_batch_id">
                                    <input type="hidden" name="hidden_inventory_id" id="hidden_inventory_id">
                                    <input type="hidden" name="hidden_edit_vendor" id="hidden_edit_vendor" value="false">
                                    {{-- <select class="selectpicker outline-primary form-control" id="product_name" width="100%" readonly="true">
                                        @foreach($products as $key => $value)
                                            <option value="{{$value->id}}">{{$value->product_name}}</option>
                                        @endforeach
                                    </select> --}}
                                    <span class="form-control" id="product_name">Standard Walker</span>
                                </div>
                                <div class="col-md-6">
                                    <span >Product Type : </span><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input btn_product_type" type="radio" name="update_product_type"id="rental" value="Rental" required>
                                        <label class="form-check-label" for="rental">Rental</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input btn_product_type" type="radio" name="update_product_type"id="sale" value="Sale" required>
                                        <label class="form-check-label" for="sale">Sale</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <span>Product Rent/Sale : </span><input type="number" class="form-control" name="update_product_rent" id="update_product_rent" @if(!in_array(session('user_id'),[14,97,19])){{"disabled"}}@endif>
                                </div>
                                <div class="col-md-6">
                                    <span>Product Deposite : </span><input type="number" class="form-control" name="update_product_deposite" id="update_product_deposite" @if(!in_array(session('user_id'),[14,97,19])){{"disabled"}}@endif>
                                </div>
                                <div class="col-md-6">
                                    <span>Transport : </span><input type="number" class="form-control" name="update_transport" id="update_transport" @if(!in_array(session('user_id'),[14,97,19])){{"disabled"}}@endif>
                                </div>
                                <div class="col-md-3">
                                    <span >Edit Vendor : </span>
                                    <button type="submit" class="btn btn-primary form-control" id="edit_vendor">Edit Vendor</button>
                                    <button type="submit" class="btn btn-primary form-control" id="undo_changes" style="display: none;">Reset</button>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <span >Vendor : </span>
                                    <select class="selectpicker outline-primary form-control" title="Select Vendor" data-live-search="true" name="update_select_vendor" id="update_select_vendor" width="100%">
                                        <option value="No">No Vendor Found</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <span >Warehouse : </span>
                                    <select class="selectpicker outline-primary form-control" title="Select Warehouse" data-live-search="true" name="update_select_warehouse" id="update_select_warehouse" width="100%">
                                        <option value="No">No Warehouse Found</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <span >Product Brand : </span>
                                    <select class="selectpicker outline-primary form-control" title="Select Brand" data-live-search="true" name="update_select_brand" id="update_select_brand" width="100%">
                                        <option value="No">Select Warehouse First</option>
                                    </select>
                                </div>
                                <div class="col-md-6" id="div_batch_rental">
                                    <span >Product Batch : </span>
                                    <select class="selectpicker outline-primary form-control" title="Select Batch" data-live-search="true" name="update_select_batch" id="update_select_batch" width="100%">
                                        <option value="No">Select Brand First</option>
                                    </select>
                                </div>
                                <div class="col-md-6" id="div_batch_sale">
                                    <span >Product Batch : </span>
                                    <span class="form-control" id="update_batch">No Batch</span>
                                </div>
                                <div class="col-md-6" id="div_inventory_sale">
                                    <span >Inventory Id : </span>
                                    <span class="form-control" id="update_inventory_id">No Invenory Id</span>
                                </div>
                                <div class="col-md-6" id="div_inventory_rental">
                                    <span >Inventory Id : </span>
                                    <select class="selectpicker outline-primary form-control" title="Select Inventory" data-live-search="true" name="update_select_inventory" id="update_select_inventory" width="100%">
                                        <option value="No">Select Batch First</option>
                                    </select>                                    
                                </div>
                                <div class="col-md-6" style="display: none;">
                                    <span >Vendor Approved : </span><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input btn_vendor_approved" type="radio" name="update_approval_status"id="btn_yes" value="Yes" required>
                                        <label class="form-check-label" for="btn_yes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input btn_vendor_approved" type="radio" name="update_approval_status"id="brn_no" value="No" required>
                                        <label class="form-check-label" for="brn_no">No</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <span>Remark</span>
                                    <textarea class="form-control form-control-sm" name="update_remark" id="update_remark" cols="5" rows="5"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" id="update_product" class="btn btn-outline-success">Update</button>
                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                        </div>
                    {{-- </form> --}}
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal_edit_address" tabindex="-1" role="dialog" aria-labelledby="edit_address_modal" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="edit_address_modal">Address</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-header text-center">
                                <h4>Delivery Address<h4>
                            </div>
                            <div class="card-body">
                                <form action="{{route('edit-order-addr')}}" method="POST">
                                    @csrf
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="address_line_1">Line 1</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="hidden" name="hidden_customer_id" value="{{$response[0]->cust_id}}">
                                            <input type="hidden" name="hidden_customer_name" value="{{$response[0]->customer_name}}">
                                            <input type="hidden" name="hidden_order_id" value="{{$response[0]->order_id}}">
                                            <input type="hidden" name="request_type" value="edit_address">
                                            <input type="text" class="form-control" name="address_line_1" value="{{$response[0]->address_line_1}}" id="address_line_1" placeholder="Line 1*" required>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="address_line_2">Line 2</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" value="{{$response[0]->address_line_2}}" name="address_line_2" id="address_line_2" placeholder="Line 2*" required>
                                        </div>
                                    </div>                                    
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="landmark">Landmark</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" value="{{$response[0]->landmark}}" name="landmark" id="landmark" placeholder="Landmark*"required>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="area">Area</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" value="{{$response[0]->area}}" name="area" id="area" placeholder="Area*">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="city1">City</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" value="{{$response[0]->city}}" name="city1" id="city1" placeholder="City*"required>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="pincode">Pin Code</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" value="{{$response[0]->pincode}}" name="pincode" id="pincode" placeholder="Pincode*" maxlength="6" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row form-group">
                                        
                                        <div class="col-md-4">
                                            <label for="state">State</label>
                                        </div>
                                        <div class="col-md-8">
                                            <select class="selectpicker form-control" title="State*" name="state" id="state" data-live-search="true" required>                                                
                                                @foreach ($states as $state) 
                                                    <option value="{{$state['name']}}" @if($response[0]->state == $state['name']){{'selected'}}@endif>{{$state['name']}}</option>
                                                @endforeach 
                                            </select>
                                        </div>
                                    </div>
                                        
                                    <div class="row form-group">
                                        <div class="col-md-4">                                
                                            <label for="country">Country</label>
                                        </div>
                                        <div class="col-md-8">
                                            <select class="selectpicker form-control" title="Country*" name="country" id="country" data-live-search="true" required>
                                                @foreach ($countries as $country)
                                                    <option value="{{$country['name']}}" @if($response[0]->country == $country['name']){{'selected'}}@endif>{{$country['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>                                    
                                    </div>

                                    <div class="row form-group text-center">
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-sm btn-outline-success" name="update_addr" id="update_addr">Update</button>
                                        </div>                     
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal_add_product_details" tabindex="-1" role="dialog" aria-labelledby="add_product_details" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="add_product_details">Add Product Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form class="form" id="add_product_form">                        
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <span >Product Name : </span>
                                    <select class="selectpicker outline-primary form-control" title="Select Product" data-live-search="true" name="new_product_name" id="new_product_name" width="100%">
                                        @foreach($products as $key => $value)
                                            <option value="{{$value->id}}">{{$value->product_name}}</option>
                                        @endforeach
                                    </select>                                    
                                </div>
                                <div class="col-md-6">
                                    <span >Product Type : </span><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input btn_product_type" type="radio" name="new_product_type"id="new_rental" value="Rental" checked required>
                                        <label class="form-check-label" for="new_rental">Rental</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input btn_product_type" type="radio" name="new_product_type"id="new_sale" value="Sale" required>
                                        <label class="form-check-label" for="new_sale">Sale</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <span >Product Rent/Sale : </span><input type="number" class="form-control" name="new_product_rent" id="new_product_rent">
                                </div>
                                <div class="col-md-6">
                                    <span >Product Deposite : </span><input type="number" class="form-control" name="new_product_deposite" id="new_product_deposite">
                                </div>
                                <div class="col-md-6">
                                    <span >Transport : </span><input type="number" class="form-control" name="new_transport" id="new_transport">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <span >Vendor : </span>
                                    <select class="selectpicker outline-primary form-control" title="Select Vendor" data-live-search="true" name="new_select_vendor" id="new_select_vendor" width="100%">
                                        <option value="No" disabled>No Vendor Found</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <span >Warehouse : </span>
                                    <select class="selectpicker outline-primary form-control" title="Select Warehouse" data-live-search="true" name="new_select_warehouse" id="new_select_warehouse" width="100%">
                                        <option value="No" disabled>No Vendor Found</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <span >Product Brand : </span>
                                    <select class="selectpicker outline-primary form-control" title="Select Brand" data-live-search="true" name="new_select_brand" id="new_select_brand" width="100%">
                                        <option value="No" disabled>No Brand Found</option>
                                    </select>
                                </div>
                                {{-- <div class="col-md-6">
                                    <span >Product Batch : </span>
                                    <select class="selectpicker outline-primary form-control" title="Select Batch" data-live-search="true" name="new_select_batch" id="new_select_batch" width="100%">
                                        <option value="No" disabled>No Batch Found</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <span >Inventory Id : </span>
                                    <select class="selectpicker outline-primary form-control" title="Select Inventory" data-live-search="true" name="new_select_inventory" id="new_select_inventory" width="100%">
                                        <option value="No" disabled>No Inventory Found</option>
                                    </select>
                                </div> --}}
                                <div class="col-md-6" id="new_div_batch_rental">
                                    <span >Product Batch : </span>
                                    <select class="selectpicker outline-primary form-control" title="Select Batch" data-live-search="true" name="new_select_batch" id="new_select_batch" width="100%">
                                        <option value="No">Select Brand First</option>
                                    </select>
                                </div>
                                <div class="col-md-6" id="new_div_batch_sale" style="display: none;">
                                    <span >Product Batch : </span>
                                    <span class="form-control" id="new_batch">No Batch</span>
                                </div>
                                <div class="col-md-6" id="new_div_inventory_sale" style="display: none;">
                                    <span >Inventory Id : </span>
                                    <span class="form-control" id="new_inventory_id">No Invenory Id</span>
                                </div>
                                <div class="col-md-6" id="new_div_inventory_rental">
                                    <span >Inventory Id : </span>
                                    <select class="selectpicker outline-primary form-control" title="Select Inventory" data-live-search="true" name="new_select_inventory" id="new_select_inventory" width="100%">
                                        <option value="No">Select Batch First</option>
                                    </select>                                    
                                </div>
                                <div class="col-md-6">
                                    <span >Vendor Approved : </span><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input btn_vendor_approved" type="radio" name="new_approval_status"id="new_btn_yes" value="Yes" checked required>
                                        <label class="form-check-label" for="new_btn_yes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input btn_vendor_approved" type="radio" name="new_approval_status"id="new_btn_no" value="No" required disabled>
                                        <label class="form-check-label" for="new_btn_no">No</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="btn_add_product" id="btn_add_product" class="btn btn-outline-success">Submit</button>    
                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal_edit_patient_name" tabindex="-1" role="dialog" aria-labelledby="edit_patient_name_modal" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="edit_patient_name_modal">Address</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('edit-patient-name')}}" method="POST">
                            @csrf
                            <div class="card card-body">
                                <div class="row form-group">
                                    <label for="modal_patient_name">Patient Name</label>
                                </div>
                                <div class="row form-group">
                                    <input type="text" name="modal_patient_name" id="modal_patient_name" class="form-control form-control-sm">
                                    <input type="hidden" name="request_type" value="edit_patient_name">
                                    <input type="hidden" name="modal_lead_id" value="{{$response[0]->lead_id}}">
                                </div>
                                <div class="row form-group">
                                    <button class="btn btn-outline-success" name="update_patient_name" id="update_patient_name">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="adjustDeposit" tabindex="-1" role="dialog" aria-labelledby="adjustDepositLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form action="{{url('/')}}/updateOrderProduct" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="adjustDepositLabel">Deposit Adjustment</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item flex-column align-items-start">
                                    {{--hidden values--}}
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1" id="depo_product_name"></h5>
                                        <input type="hidden" name="act_order_details_id" id="act_order_details_id">
                                        <input type="hidden" name="request_type" id="request_type" value="adjust-deposit">
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-auto">
                                            <b><span>Type : </span></b><span id="depo_product_type"></span>
                                        </div>
                                        <div class="col-auto">
                                            <b><span>Rent : </span></b><span id="depo_product_rent"></span>
                                        </div>
                                        <div class="col-auto">
                                            <input type="hidden" name="hidden_depo_product_deposite" id="hidden_depo_product_deposite">
                                            <b><span>Deposit : </span></b><span id="depo_product_deposite"></span>
                                        </div>
                                        <div class="col-auto">
                                            <b><span>Transport : </span></b><span id="depo_transport"></span>
                                        </div>
                                        {{-- <input type='number' class='form-control form-control-sm adjusted_deposit' name='adjusted_deposit[]' id='adjusted_deposit' value='0'> --}}
                                    </div>
                                    <div class="row mb-1" id="live_products">
                                        
                                    </div>
                                </div>                            
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
       
    @endsection
    @section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.0/js/bootstrap-datepicker.min.js"></script>
    {{-- alert on screeen popup Script--}}
    <script src="{{url('/')}}/assets/dist/toast.min.js"></script>
    <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
    <script>

        function calculate(index){
            let total = 0;
            for(let i=0; i<$("#row_count").val(); i++)
            {
                if(parseInt($("#adjusted_deposit"+i).val())>parseInt($("#hidden_adjusted_deposit"+i).val())){
                    alert("Amount Should be less than or equal to: "+$("#hidden_adjusted_deposit"+i).val());
                    $("#adjusted_deposit"+i).val(parseInt($("#hidden_adjusted_deposit"+i).val()));
                }
                else{
                    
                }
                total = total + parseInt($("#adjusted_deposit"+i).val());
            }            
            if(total > $("#hidden_depo_product_deposite").val())
            {   total = total - parseInt($("#adjusted_deposit"+index).val());
                alert("Adjusted deposit amount should be less than or equal to: "+$("#hidden_depo_product_deposite").val());
                
                $("#adjusted_deposit"+index).val(parseInt($("#hidden_depo_product_deposite").val()) - parseInt(total));
            }
            
        }

        // $(".adjusted_deposit").on("input",function(){
        //     console.log(".");
        //     console.log($(this).val());
        // });
        // $(".checked1").click(function(){
        //     console.log($(this).val());
        // });

        $(".adjust-deposit").click(function(){
            // alert($(this).data("order_details_id"));
            // $("#adjustDeposit").modal("show");
            let dataString = ({_token:"{{ csrf_token() }}",order_details_id:""+$(this).data("order_details_id"),customer_id:""+$(this).data("customer_id"),request_type:"fetch-order-product-details"});
            // console.log(dataString);
            $.ajax({
                type: "POST",
                url: "{{url('/')}}/updateOrderProduct",
                data: dataString,
                cache:false,
                success: function (data)
                {
                    $("#live_products").empty();
                    console.log(data);
                    if(data.details.length>=1)
                    {
                        $("#depo_product_name").text("For: "+data.details[0].product_name);
                        $("#depo_updated").text(data.details[0].upgraded);
                        $("#depo_product_type").text(data.details[0].sale_rental);
                        $("#depo_product_rent").text(data.details[0].product_rent);
                        $("#hidden_depo_product_deposite").val(data.details[0].product_deposite);
                        $("#depo_product_deposite").text(data.details[0].product_deposite);
                        $("#depo_transport").text(data.details[0].transport);
                        $("#act_order_details_id").val(data.details[0].id);

                        // Live Products
                        if(data.deposit_available.length>=1){
                            let div = "<div class='d-flex w-100 justify-content-between'><h5 class='mb-1'>From Live Products</h5></div>";
                            div += "<div class='table table-responsive'>";
                            div += "<table class='table' id='live_products_table'>";
                                div += "<thead>";
                                    div += "<tr>";
                                        div += "<th>SrNo</th>";
                                        div += "<th>Order Id</th>";
                                        div += "<th>Product Name</th>";
                                        div += "<th>Type</th>";
                                        div += "<th>Rent</th>";
                                        div += "<th>Deposit</th>";
                                        div += "<th>Adjust</th>";
                                    div += "</tr>";
                                div += "</thead>";
                                div += "<tbody>";
                                let row_count = 0;
                                for(let i=0;i<data.deposit_available.length;i++)
                                {
                                    div += "<tr>";
                                        div += "<td>"+parseInt(i+1)+"</td>";
                                        div += "<td>"+data.deposit_available[i].order_id+"</td>";
                                        div += "<td>"+data.deposit_available[i].product_name+"</td>";
                                        div += "<td>"+data.deposit_available[i].sale_rental+"</td>";
                                        div += "<td>"+data.deposit_available[i].product_rent+"</td>";
                                        div += "<td><input type='hidden' class='form-control form-control-sm hidden_adjusted_order_details_id' name='hidden_adjusted_order_details_id[]' id='hidden_adjusted_order_details_id"+i+"' value='"+data.deposit_available[i].id+"'><input type='hidden' class='form-control form-control-sm hidden_adjusted_deposit' name='hidden_adjusted_deposit[]' id='hidden_adjusted_deposit"+i+"' value='"+data.deposit_available[i].product_deposite+"'>"+data.deposit_available[i].product_deposite+"</td>";
                                        div += "<td><input type='number' class='form-control form-control-sm adjusted_deposit' name='adjusted_deposit[]' onInput='calculate("+i+");' id='adjusted_deposit"+i+"'  value='"+data.deposit_available[i].adjusted_deposit+"' required></td>";
                                    div += "</tr>";
                                    row_count++;                                        
                                }
                                div += "</tbody>";
                            div += "</table>";
                            div += "<input type='hidden' name='row_count' id='row_count' value='"+row_count+"'>"
                            div += "</div>";
                            $("#live_products").append(div);
                            $("#live_products_table").dataTable();
                        }
                        else{
                            $("#live_products").append("<div class='d-flex w-100 justify-content-between'><h5 class='mb-1'>Live Products</h5></div><div class='row mb-1'>No Live Products</div>");
                        }

                        $("#adjustDeposit").modal("show");
                    }
                    else{
                        alert("Something Went Wrong!");
                    }
                }
            });
        });

        $("#edit_patient_name").click(function(){
            $("#modal_edit_patient_name").modal("show");
        });

        $("#edit_address").click(function(){
            $("#modal_edit_address").modal('show');
        });

        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        });

        $("#edit_vendor").click(function(){
            $("#hidden_edit_vendor").val("true");
            $("#update_select_vendor").removeAttr("disabled");
            $("#update_select_warehouse").removeAttr("disabled");
            $("#update_select_brand").removeAttr("disabled");
            $("#update_select_batch").removeAttr("disabled");
            $("#update_select_inventory").removeAttr("disabled");
            $(".selectpicker").selectpicker("refresh");
            $(this).hide();
            $("#undo_changes").show();
        });

        $("#undo_changes").click(function(){
            $("#hidden_edit_vendor").val("false");
            let key_id = $("#hidden_key").val();
            editProduct_fetch_detailsExists(key_id);
            $(this).hide();
            $("#edit_vendor").show();
        });

        $(document).ready(function(){
            // $("#modal_update_product_details").on('click', function(){
            $('#update_select_vendor').on('change', function(){
                let hidden_vendor_id = $("#hidden_vendor_id").val();
                let hidden_warehouse_id = $("#hidden_warehouse_id").val();
                // let hidden_brand_id = $("#hidden_brand_id").val();
                // let hidden_batch_id = $("#hidden_batch_id").val();
                // let hidden_inventory_id = $("#hidden_inventory_id").val();

                let vendor_id = $('#update_select_vendor').val();
                let warehouse_id = $('#update_select_warehouse').val();
                // let brand_id = $('#update_select_brand').val();
                // let batch_id = $('#update_select_batch').val();
                // let inventory_id = $('#update_select_inventory').val();

                // let vendor_id = $(this).val();

                if($("#hidden_edit_vendor").val() == "true")
                {
                    let product_id = $("#hidden_product_id").val();
                    let product_type = $('input[name="update_product_type"]:checked').val();                    

                    let dataString = ({_token:"{{ csrf_token() }}",vendor_id:""+vendor_id,product_id:""+product_id,product_type:""+product_type,request_type:"fetch-warehouse"});
                    $.ajax({
                        type: "POST",
                        url: "{{url('/')}}/updateOrderProduct",
                        data: dataString,
                        cache:false,
                        success: function (data)
                        {
                            let warehouse_details = data["warehouse_details"];
                            //------- Warehouse Selection --------//
                            $("#update_select_warehouse")
                                .find("option")
                                .remove()
                                .end();
                            for(var j = 0; j < warehouse_details.length; j++)
                            {
                                // console.log(warehouse_details[j].warehouse_id);
                                $("#update_select_warehouse").append("<option value='"+warehouse_details[j].warehouse_id+"'>"+warehouse_details[j].wh_name+", "+warehouse_details[j].wh_area+", "+warehouse_details[j].wh_city+"</option>");
                            }
                            $('#update_select_warehouse').selectpicker('refresh');
                            // $("#update_select_brand")
                            //     .find("option")
                            //     .remove()
                            //     .end();
                            // $("#update_select_batch")
                            //     .find("option")
                            //     .remove()
                            //     .end();
                            // $("#update_select_batch")
                            //     .find("option")
                            //     .remove()
                            //     .end();
                        }
                    });
                }
            });

            $('#update_select_warehouse').on('change', function(){
                let vendor_id = $("#update_select_vendor").val();
                let warehouse_id = $(this).val();
                let product_id = $("#hidden_product_id").val();
                let product_type = $('input[name="update_product_type"]:checked').val();
                let hidden_warehouse_id = $("#hidden_warehouse_id").val();
                // let warehouse_id = $('#update_select_warehouse').val();
                if($("#hidden_edit_vendor").val() == "true")
                {
                    let dataString = ({_token:"{{ csrf_token() }}",vendor_id:""+vendor_id,warehouse_id:""+warehouse_id,product_id:""+product_id,product_type:""+product_type,request_type:"fetch-brand"});
                    $.ajax({
                        type: "POST",
                        url: "{{url('/')}}/updateOrderProduct",
                        data: dataString,
                        cache:false,
                        success: function (data)
                        {
                            let brand_details = data["brand_details"];
                            //------- Warehouse Selection --------//
                            $("#update_select_brand")
                                .find("option")
                                .remove()
                                .end();
                            for(var j = 0; j < brand_details.length; j++)
                            {
                                // console.log(warehouse_details[j].warehouse_id);
                                $("#update_select_brand").append("<option value='"+brand_details[j].brand_id+"'>"+brand_details[j].brand_name+"</option>");
                            }
                            $('#update_select_brand').selectpicker('refresh');                    
                        }
                    });
                }

            });

            $('#update_select_brand').on('change', function(){
                let vendor_id = $("#update_select_vendor").val();
                let warehouse_id = $("#update_select_warehouse").val();
                let brand_id = $(this).val();
                let product_id = $("#hidden_product_id").val();
                let product_type = $('input[name="update_product_type"]:checked').val();
                let hidden_brand_id = $("#hidden_brand_id").val();
                // let brand_id = $('#update_select_brand').val();
                if($("#hidden_edit_vendor").val() == "true")
                {
                    let dataString = ({_token:"{{ csrf_token() }}",vendor_id:""+vendor_id,warehouse_id:""+warehouse_id,brand_id:""+brand_id,product_id:""+product_id,product_type:""+product_type,request_type:"fetch-batch"});
                    $.ajax({
                        type: "POST",
                        url: "{{url('/')}}/updateOrderProduct",
                        data: dataString,
                        cache:false,
                        success: function (data)
                        {
                            let batch_details = data["batch_details"];
                            //------- Batch Selection --------//
                            $("#update_select_batch")
                                .find("option")
                                .remove()
                                .end();
                            for(var j = 0; j < batch_details.length; j++)
                            {
                                // console.log(brand_details[j].warehouse_id);
                                $("#update_select_batch").append("<option value='"+batch_details[j].vendor_product_id+"'>"+batch_details[j].batch_name+"</option>");
                            }
                            $('#update_select_batch').selectpicker('refresh');
                        }
                    });
                }
            });

            $('#update_select_batch').on('change', function(){
                // let vendor_id = $("#update_select_vendor").val();
                let warehouse_id = $("#update_select_warehouse").val();
                let batch_id = $(this).val();
                let hidden_batch_id = $("#hidden_batch_id").val();
                // let product_id = $("#hidden_product_id").val();
                let product_type = $('input[name="update_product_type"]:checked').val();
                if($("#hidden_edit_vendor").val() == "true")
                {
                    let dataString = ({_token:"{{ csrf_token() }}",warehouse_id:""+warehouse_id,batch_id:""+batch_id,product_type:""+product_type,request_type:"fetch-inventory"});
                    $.ajax({
                        type: "POST",
                        url: "{{url('/')}}/updateOrderProduct",
                        data: dataString,
                        cache:false,
                        success: function (data)
                        {
                            let inventory_details = data["inventory_details"];
                            //------- Inventory Selection --------//
                            $("#update_select_inventory")
                                .find("option")
                                .remove()
                                .end();
                            for(var j = 0; j < inventory_details.length; j++)
                            {
                                // console.log(brand_details[j].warehouse_id);
                                $("#update_select_inventory").append("<option value='"+inventory_details[j].vendor_product_details_id+"'>"+inventory_details[j].inventory_id+"</option>");
                            }
                            $('#update_select_inventory').selectpicker('refresh');
                        }
                    });
                }
            });

            $('#new_select_vendor').on('change', function(){                
                let product_id = $("#new_product_name").val();
                let product_type = $('input[name="new_product_type"]:checked').val();
                let vendor_id = $(this).val();

                let dataString = ({_token:"{{ csrf_token() }}",vendor_id:""+vendor_id,product_id:""+product_id,product_type:""+product_type,request_type:"fetch-warehouse"});
                $.ajax({
                    type: "POST",
                    url: "{{url('/')}}/updateOrderProduct",
                    data: dataString,
                    cache:false,
                    success: function (data)
                    {
                        let warehouse_details = data["warehouse_details"];
                        //------- Warehouse Selection --------//
                        $("#new_select_warehouse")
                            .find("option")
                            .remove()
                            .end();
                        for(var j = 0; j < warehouse_details.length; j++)
                        {
                            // console.log(warehouse_details[j].warehouse_id);
                            $("#new_select_warehouse").append("<option value='"+warehouse_details[j].warehouse_id+"'>"+warehouse_details[j].wh_name+", "+warehouse_details[j].wh_area+", "+warehouse_details[j].wh_city+"</option>");
                        }
                        $('#new_select_warehouse').selectpicker('refresh');
                        // $("#update_select_brand")
                        //     .find("option")
                        //     .remove()
                        //     .end();
                        // $("#update_select_batch")
                        //     .find("option")
                        //     .remove()
                        //     .end();
                        // $("#update_select_batch")
                        //     .find("option")
                        //     .remove()
                        //     .end();
                    }
                });
                
            });

            $('#new_select_warehouse').on('change', function(){
                let vendor_id = $("#new_select_vendor").val();
                let warehouse_id = $(this).val();
                let product_id = $("#new_product_name").val();
                let product_type = $('input[name="new_product_type"]:checked').val();

                let dataString = ({_token:"{{ csrf_token() }}",vendor_id:""+vendor_id,warehouse_id:""+warehouse_id,product_id:""+product_id,product_type:""+product_type,request_type:"fetch-brand"});
                $.ajax({
                    type: "POST",
                    url: "{{url('/')}}/updateOrderProduct",
                    data: dataString,
                    cache:false,
                    success: function (data)
                    {
                        let brand_details = data["brand_details"];
                        //------- Warehouse Selection --------//
                        $("#new_select_brand")
                            .find("option")
                            .remove()
                            .end();
                        for(var j = 0; j < brand_details.length; j++)
                        {
                            // console.log(warehouse_details[j].warehouse_id);
                            $("#new_select_brand").append("<option value='"+brand_details[j].brand_id+"'>"+brand_details[j].brand_name+"</option>");
                        }
                        $('#new_select_brand').selectpicker('refresh');                    
                    }
                });
            
            });

            $('#new_select_brand').on('change', function(){
                let vendor_id = $("#new_select_vendor").val();
                let warehouse_id = $("#new_select_warehouse").val();
                let brand_id = $(this).val();                
                let product_id = $("#new_product_name").val();
                let product_type = $('input[name="new_product_type"]:checked').val();
                // let brand_id = $('#new_select_brand').val();

                let dataString = ({_token:"{{ csrf_token() }}",vendor_id:""+vendor_id,warehouse_id:""+warehouse_id,brand_id:""+brand_id,product_id:""+product_id,product_type:""+product_type,request_type:"fetch-batch"});
                $.ajax({
                    type: "POST",
                    url: "{{url('/')}}/updateOrderProduct",
                    data: dataString,
                    cache:false,
                    success: function (data)
                    {
                        let batch_details = data["batch_details"];
                        //------- Batch Selection --------//
                        $("#new_select_batch")
                            .find("option")
                            .remove()
                            .end();
                        for(var j = 0; j < batch_details.length; j++)
                        {
                            // console.log(brand_details[j].warehouse_id);
                            $("#new_select_batch").append("<option value='"+batch_details[j].vendor_product_id+"'>"+batch_details[j].batch_name+"</option>");
                        }
                        $('#new_select_batch').selectpicker('refresh');
                    }
                });

            });

            $('#new_select_batch').on('change', function(){                
                let warehouse_id = $("#new_select_warehouse").val();
                let batch_id = $(this).val();                
                let product_type = $('input[name="new_product_type"]:checked').val();
                
                let dataString = ({_token:"{{ csrf_token() }}",warehouse_id:""+warehouse_id,batch_id:""+batch_id,product_type:""+product_type,request_type:"fetch-inventory"});
                $.ajax({
                    type: "POST",
                    url: "{{url('/')}}/updateOrderProduct",
                    data: dataString,
                    cache:false,
                    success: function (data)
                    {
                        let inventory_details = data["inventory_details"];
                        //------- Inventory Selection --------//
                        $("#new_select_inventory")
                            .find("option")
                            .remove()
                            .end();
                        for(var j = 0; j < inventory_details.length; j++)
                        {
                            // console.log(brand_details[j].warehouse_id);
                            $("#new_select_inventory").append("<option value='"+inventory_details[j].vendor_product_details_id+"'>"+inventory_details[j].inventory_id+"</option>");
                        }
                        $('#new_select_inventory').selectpicker('refresh');
                    }
                });

            });
            
        // });
        });

        let request = "Not Exists";
        $(".edit-product").on("click",function(){
            // alert($(this).data("order_details_id"));
            let id = $(this).data("id");
            $("#hidden_product_id").val($("#editProduct"+id).data("product_id"));
            $("#hidden_product_qty").val($("#editProduct"+id).data("product_qty"));
            $("#hidden_key").val(id);
            editProduct_fetch_detailsExists(id);
            $("#modal_update_product_details").modal("show");
        });

        // Crud Operations and fetch data from current actions....
        $('input[name="update_product_type"]').on('change', function(){
            let product_type = $(this).val();
            let product_id = $("#hidden_product_id").val();
            let product_qty = $("#hidden_product_qty").val();
            let key_id = $("#hidden_key").val();
            if(product_type == $("#editProduct"+key_id).data("product_type"))
            {
                editProduct_fetch_detailsExists(key_id);
                $("#hidden_edit_vendor").val("false");
                $("#undo_changes").hide();
                $("#edit_vendor").show();
                // request = "Exists";
            }
            else
            {
                editProduct_fetch_detailsNew(product_id,product_qty,product_type);
                // request = "Not Exists";
            }
        });

        function editProduct_fetch_detailsExists(id) 
        {
            $("#hidden_edit_vendor").val("false");
            let order_details_id = $("#editProduct"+id).data("order_details_id");
            $("#hidden_order_details_id").val(order_details_id);
            let product_type = $("#editProduct"+id).data("product_type");
            let dataString = ({_token:"{{ csrf_token() }}",order_details_id:""+order_details_id,product_type:""+product_type,request_type:"fetch-order-product"});
            $.ajax({
                type: "POST",
                url: "{{url('/')}}/updateOrderProduct",
                data: dataString,
                cache:false,
                success: function (data)
                {
                    $("#hidden_product_id").val()
                    let vendor_details = data['vendor_details'];
                    let order_details = data['order_details'];
                    let warehouse_details = data['warehouse_details'];
                    let brand_details = data['brand_details'];
                    let batch_details = data['batch_details'];
                    let inventory_details = data['inventory_details'];
                    let product_type = order_details[0].sale_rental;
                    if(product_type == "Rental")
                    {
                        // $('radio[name=update_product_type]').val("Rental");
                        $("#rental").prop('checked', true);
                    }
                    else
                    {
                        $("#sale").prop('checked', true);
                    }

                    $("#hidden_vendor_id").val(order_details[0].vendor_id);
                    $("#hidden_warehouse_id").val(order_details[0].vendor_warehouse_id);
                    $("#hidden_brand_id").val(order_details[0].product_brand);
                    $("#hidden_batch_id").val(order_details[0].vendor_product_id);
                    $("#hidden_inventory_id").val(order_details[0].vendor_product_details_id);

                    $("#product_name").text(order_details[0].product_name);
                    $("#update_product_rent").val(order_details[0].product_rent);
                    $("#update_product_deposite").val(order_details[0].product_deposite);
                    $("#update_transport").val(order_details[0].transport);
                    //------- Vendor Selection --------//
                    $("#update_select_vendor")
                        .find("option")
                        .remove()
                        .end();
                    for(var j = 0; j < vendor_details.length; j++)
                    {
                        
                        $("#update_select_vendor").append("<option value='"+vendor_details[j].vendor_id+"'>"+vendor_details[j].vendor_name+"</option>");
                    }
                    $('#update_select_vendor').selectpicker('refresh');
                    $('#update_select_vendor').selectpicker('val', order_details[0].vendor_id);
                    $('#update_select_vendor').attr('disabled',true);
                    $('#update_select_vendor').selectpicker('refresh');

                    //------- Warehouse Selection --------//
                    $("#update_select_warehouse")
                        .find("option")
                        .remove()
                        .end();
                    for(var j = 0; j < warehouse_details.length; j++)
                    {
                        // console.log(warehouse_details[j].warehouse_id);
                        $("#update_select_warehouse").append("<option value='"+warehouse_details[j].warehouse_id+"'>"+warehouse_details[j].wh_name+", "+warehouse_details[j].wh_area+", "+warehouse_details[j].wh_city+"</option>");
                    }
                    $('#update_select_warehouse').selectpicker('refresh');
                    $('#update_select_warehouse').selectpicker('val', order_details[0].vendor_warehouse_id);
                    $('#update_select_warehouse').attr('disabled',true);
                    $('#update_select_warehouse').selectpicker('refresh');

                    //------- Brand Selection --------//
                    $("#update_select_brand")
                        .find("option")
                        .remove()
                        .end();
                    for(var j = 0; j < brand_details.length; j++)
                    {
                        // console.log(brand_details[j].warehouse_id);
                        $("#update_select_brand").append("<option value='"+brand_details[j].brand_id+"'>"+brand_details[j].brand_name+"</option>");
                    }
                    $('#update_select_brand').selectpicker('refresh');
                    $('#update_select_brand').selectpicker('val', order_details[0].product_brand);
                    $('#update_select_brand').attr('disabled',true);
                    $('#update_select_brand').selectpicker('refresh');
                    if(product_type == "Rental")
                    {
                        $('#div_inventory_rental').show();
                        $('#div_batch_rental').show();

                        $('#div_inventory_sale').hide();
                        $('#div_batch_sale').hide();
                        //------- Batch Selection --------//
                        $("#update_select_batch")
                            .find("option")
                            .remove()
                            .end();
                        for(var j = 0; j < batch_details.length; j++)
                        {
                            // console.log(brand_details[j].warehouse_id);
                            $("#update_select_batch").append("<option value='"+batch_details[j].vendor_product_id+"'>"+batch_details[j].batch_name+"</option>");
                        }
                        $('#update_select_batch').selectpicker('refresh');
                        $('#update_select_batch').selectpicker('val', order_details[0].vendor_product_id);
                        $('#update_select_batch').attr('disabled',true);
                        $('#update_select_batch').selectpicker('refresh');
                        
                        //------- Inventory Selection --------//
                        $("#update_select_inventory")
                            .find("option")
                            .remove()
                            .end();
                        for(var j = 0; j < inventory_details.length; j++)
                        {
                            // console.log(brand_details[j].warehouse_id);
                            $("#update_select_inventory").append("<option value='"+inventory_details[j].vendor_product_details_id+"'>"+inventory_details[j].inventory_id+"</option>");
                        }
                        $("#update_select_inventory").append("<option value='"+order_details[0].vendor_product_details_id+"'>"+order_details[0].unique_id+"</option>");
                        $('#update_select_inventory').selectpicker('refresh');
                        $('#update_select_inventory').selectpicker('val', order_details[0].vendor_product_details_id);
                        $('#update_select_inventory').attr('disabled',true);
                        $('#update_select_inventory').selectpicker('refresh');
                    }
                    else if(product_type == "Sale")
                    {
                        $('#div_inventory_rental').hide();
                        $('#div_batch_rental').hide();

                        $('#div_inventory_sale').show();
                        $('#div_batch_sale').show();
                    }
                }
            });
        }

        function editProduct_fetch_detailsNew(product_id,product_qty,product_type)
        {
            // let order_details_id = $(".edit-product").data("order_details_id");
            // let product_type = $(".edit-product").data("product_type");

            let dataString = ({_token:"{{ csrf_token() }}",product_id:""+product_id,product_qty:""+product_qty,product_type:""+product_type,request_type:"fetch-order-product-new"});
            $.ajax({
                type: "POST",
                url: "{{url('/')}}/updateOrderProduct",
                data: dataString,
                cache:false,
                success: function (data)
                {
                    console.log(data);
                    let vendor_details = data['vendor_details'];
                    let product_details = data['product_details'];
                    // let product_type = order_details[0].sale_rental;
                    if(product_type == "Rental")
                    {
                        // $('radio[name=update_product_type]').val("Rental");
                        $("#rental").prop('checked', true);
                        $("#update_product_rent").val(product_details[0].product_rent);
                    }
                    else
                    {
                        $("#sale").prop('checked', true);
                        $("#update_product_rent").val(product_details[0].product_sale_rate);
                    }
                    
                    $("#update_product_deposite").val(product_details[0].product_deposite);
                    $("#update_transport").val(product_details[0].transport);
                    //------- Vendor Selection --------//
                    $("#update_select_vendor")
                        .find("option")
                        .remove()
                        .end();
                    for(var j = 0; j < vendor_details.length; j++)
                    {
                        
                        $("#update_select_vendor").append("<option value='"+vendor_details[j].vendor_id+"'>"+vendor_details[j].vendor_name+"</option>");
                    }
                    $('#update_select_vendor').selectpicker('refresh');
                    // $('#update_select_vendor').selectpicker('val', order_details[0].vendor_id);

                    //------- Warehouse Selection --------//
                    $("#update_select_warehouse")
                        .find("option")
                        .remove()
                        .end();
                        $("#update_select_warehouse").append("<option value='No' disabled>Select Vendor First</option>");
                    // for(var j = 0; j < warehouse_details.length; j++)
                    // {
                    //     // console.log(warehouse_details[j].warehouse_id);
                    //     $("#update_select_warehouse").append("<option value='"+warehouse_details[j].warehouse_id+"'>"+warehouse_details[j].wh_name+", "+warehouse_details[j].wh_area+", "+warehouse_details[j].wh_city+"</option>");
                    // }
                    $('#update_select_warehouse').selectpicker('refresh');
                    // $('#update_select_warehouse').selectpicker('val', order_details[0].vendor_warehouse_id);

                    //------- Brand Selection --------//
                    $("#update_select_brand")
                        .find("option")
                        .remove()
                        .end()
                        .append("<option value='No' disabled>Select Warehouse First</option>");
                    // for(var j = 0; j < brand_details.length; j++)
                    // {
                    //     // console.log(brand_details[j].warehouse_id);
                    //     $("#update_select_brand").append("<option value='"+brand_details[j].brand_id+"'>"+brand_details[j].brand_name+"</option>");
                    // }
                    $('#update_select_brand').selectpicker('refresh');
                    // $('#update_select_brand').selectpicker('val', order_details[0].product_brand);
                    if(product_type == "Rental")
                    {
                        $('#div_inventory_rental').show();
                        $('#div_batch_rental').show();

                        $('#div_inventory_sale').hide();
                        $('#div_batch_sale').hide();
                        //------- Batch Selection --------//
                        $("#update_select_batch")
                            .find("option")
                            .remove()
                            .end()
                            .append("<option value='No' disabled>Select Brand First</option>");
                        // for(var j = 0; j < batch_details.length; j++)
                        // {
                        //     // console.log(brand_details[j].warehouse_id);
                        //     $("#update_select_batch").append("<option value='"+batch_details[j].vendor_product_id+"'>"+batch_details[j].batch_name+"</option>");
                        // }
                        $('#update_select_batch').selectpicker('refresh');
                        // $('#update_select_batch').selectpicker('val', order_details[0].vendor_product_id);

                        //------- Inventory Selection --------//
                        $("#update_select_inventory")
                            .find("option")
                            .remove()
                            .end()
                            .append("<option value='No' disabled>Select Batch First</option>");
                        // for(var j = 0; j < batch_details.length; j++)
                        // {
                        //     // console.log(brand_details[j].warehouse_id);
                        //     $("#update_select_inventory").append("<option value='"+batch_details[j].vendor_product_id+"'>"+batch_details[j].batch_name+"</option>");
                        // }
                        $('#update_select_inventory').selectpicker('refresh');
                        // $('#update_select_inventory').selectpicker('val', order_details[0].vendor_product_id);
                    }
                    else if(product_type == "Sale")
                    {
                        $('#div_inventory_rental').hide();
                        $('#div_batch_rental').hide();

                        $('#div_inventory_sale').show();
                        $('#div_batch_sale').show();
                    }
                }
            });
        }

        $("#update_product").click(function(){
            let order_details_id = $("#hidden_order_details_id").val();
            let product_rent = $("#update_product_rent").val();
            let product_type = $('input[name="update_product_type"]:checked').val();
            let product_deposite = $("#update_product_deposite").val();
            let transport = $("#update_transport").val();
            
            let vendor = $("#update_select_vendor").val();
            let warehouse = $("#update_select_warehouse").val();
            let brand = $("#update_select_brand").val();
            let batch = $("#update_select_batch").val();
            let inventory = $("#update_select_inventory").val();
            let remark = $("#update_remark").val();

            let dataString = ({_token:"{{ csrf_token()}}",
                                order_details_id:""+order_details_id,
                                product_rent:""+product_rent,
                                product_type:""+product_type,
                                product_deposite:""+product_deposite,
                                transport:""+transport,
                                vendor:""+vendor,
                                warehouse:""+warehouse,
                                brand:""+brand,
                                batch:""+batch,
                                inventory:""+inventory,
                                remark:""+remark,
                                request_type:"update-data-order",
                            });
            if(vendor.length>0){
                $.ajax({
                    type: "POST",
                    url: "{{url('/')}}/updateOrderProduct",
                    data: dataString,
                    cache: false,
                    success: function (data)
                    {
                        // console.log(data);
                        let id = $("#hidden_key").val();
                        editProduct_fetch_detailsExists(id);
                        $("#undo_changes").hide();
                        $("#edit_vendor").show();
                        $("#product_name"+id).text(data[0].product_name);
                        $("#product_type"+id).text(data[0].sale_rental);
                        $("#product_rent"+id).text(data[0].product_rent);
                        $("#product_deposite"+id).text(data[0].product_deposite);
                        $("#transport"+id).text(data[0].transport);
                        $("#vendor_name"+id).text(data[0].vendor_name);
                        $("#warehouse_address"+id).text(data[0].warehouse_name+", "+data[0].warehouse_area+", "+data[0].warehouse_city);
                        $("#product_brand"+id).text(data[0].brand_name);
                        $("#product_batch"+id).text(data[0].product_batch);
                        $("#inventory_id"+id).text(data[0].inventory_id);
                        // alert(data);
                        window.location.reload();
                    }
                });
            }else{
                alert('please select vendor using edit vendor button');
            }
        });

        $("#add_equipment").on("click",function(){
            $("#modal_add_product_details").modal("show");
        });

        $("#new_product_name").change(function(){
            let product_id = $(this).val();
            let product_type = $('input[name="new_product_type"]:checked').val();
            new_product_details(product_id, product_type);
        });

        // Crud Operations and fetch data from current actions....
        $('input[name="new_product_type"]').on('change', function(){
            let product_type = $(this).val();
            let product_id = $("#new_product_name").val();
            if(product_type == "Rental")
            {
                $('#new_div_inventory_rental').show();
                $('#new_div_batch_rental').show();

                $('#new_div_inventory_sale').hide();
                $('#new_div_batch_sale').hide();
            }
            else if(product_type == "Sale")
            {
                $('#new_div_inventory_rental').hide();
                $('#new_div_batch_rental').hide();

                $('#new_div_inventory_sale').show();
                $('#new_div_batch_sale').show();
            }
            // alert(product_id);
            if(product_id != "" && product_id != null)
            {
                new_product_details(product_id, product_type);
            }
        });

        function  new_product_details(product_id,product_type)
        {
            let dataString = ({_token:"{{ csrf_token() }}",product_id:""+product_id,product_type:""+product_type,request_type:"fetch-product-details"});
            $.ajax({
                type: "POST",
                url: "{{url('/')}}/addOrderProduct",
                data: dataString,
                cache: false,
                success: function (data)
                {
                    // console.log(data);
                    let product_details = data['product_details'];
                    let vendor_details = data['vendor_details'];

                    if(product_type == "Rental")
                    {
                        $("#new_product_rent").val(product_details[0].product_rent);
                        $("#new_product_deposite").val(product_details[0].product_deposite);
                    }
                    else if(product_type == "Sale")
                    {
                        $("#new_product_rent").val(product_details[0].product_sale_rate);
                        $("#new_product_deposite").val(0);
                    }
                    $("#new_transport").val(product_details[0].product_transport_cost);

                    //------- Vendor Selection --------//
                    $("#new_select_vendor")
                        .find("option")
                        .remove()
                        .end();
                    for(var j = 0; j < vendor_details.length; j++)
                    {
                        
                        $("#new_select_vendor").append("<option value='"+vendor_details[j].vendor_id+"'>"+vendor_details[j].vendor_name+"</option>");
                    }
                    $('#new_select_vendor').selectpicker('refresh');
                }
            });
        }

        $("#add_product_form").validate({
            rules: {
                new_product_name:{
                    required: true,
                },
                new_select_vendor: {
                    required: true,
                },
                new_select_warehouse: {
                    required: true,
                },
                new_select_brand: {
                    required: true,
                },
                new_select_batch: {
                    required: true,
                },
                new_select_inventory: {
                    required: true,
                },
                new_product_rent: {
                    required: true,
                    minlength:1,
                    number: true
                },
                new_product_deposite: {
                    required: true,
                    minlength:1,
                    number: true
                },
                new_transport: {
                    required: true,
                    minlength:1,
                    number: true
                },
                new_select_brand: {
                    required: true,
                }
            },
            messages: {
                new_product_name: {
                    required: "please select product",
                },
                new_select_vendor: {
                    required: "please select vendor",
                },
                new_select_warehouse: {
                    required: "please select warehouse",
                },
                new_select_brand: {
                    required: "please select brand",
                },
                new_select_batch: {
                    required: "please select batch",
                },
                new_select_inventory: {
                    required: "please select inventory",
                },
            },
            submitHandler: function (form, e) {
                e.preventDefault();
                $.ajax({
                    url: "{{url('/')}}/addOrderProduct",
                    type: "post",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "order_id":$("#order_id").val(),
                        "order_details_id":$("#order_details_id").val(),
                        "customer_id":$("#customer_id").val(),
                        "creation_date":$("#creation_date").val(),
                        "new_product_type":$('input[name="new_product_type"]:checked').val(),
                        "new_product_name":$("#new_product_name").val(),
                        "new_select_vendor":$("#new_select_vendor").val(),
                        "new_select_warehouse":$("#new_select_warehouse").val(),
                        "new_select_brand": $("#new_select_brand").val(),
                        "new_select_batch": $("#new_select_batch").val(),
                        "new_select_inventory":$("#new_select_inventory").val(),
                        "new_product_rent":$("#new_product_rent").val(),
                        "new_product_deposite":$("#new_product_deposite").val(),
                        "new_transport":$("#new_transport").val(),
                        "new_select_brand":$("#new_select_brand").val(),
                        "request_type":"add-product"
                    },
                    success: function (data) {
                        //console.log(data);
                        window.location.reload();
                    },
                    error: function (error) {
                        console.warning(error);
                    }
                });
            }
        });

        $(".delete-product").click(function(){
            let order_details_id = $(this).data('order_details_id');
            let id = $(this).data('id');
            let product_name = $("#hide_prod_name"+id).val();
            
            let text = "Are you sure to remove "+product_name+" from this order.";
                if (confirm(text) == true)
                {
                    var dataString = ({_token:"{{ csrf_token() }}",order_details_id:""+order_details_id,request_type:"remove-product"});
                    $.ajax({
                        type: "POST",
                        url: "{{url('/')}}/updateOrderProduct",
                        data: dataString,
                        cache:false,
                        success: function (data)
                        {
                            console.log(data);
                            window.location.reload();
                        }
                    });
                }
                else
                {
                    console.log('No');
                }
        });
        $(".cant-delete").click(function(){
            alert("Can't delete this product! \n\n Please delete this order completely.");
        });
    </script>
    @endsection
</body>
</html>
