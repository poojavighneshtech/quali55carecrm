<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Inquiry :  Assign Vendor</title>
    @section('header')
    @endsection

</head>

<body id="page-top">	
<!-- Page Wrapper -->

@extends('header_and_sidebar')
    @section('content')
    <div class="">
        <br>
        @if(session()->has('message'))
            <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                {{ session()->get('message') }}
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
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="background-color: #337ab7; color: white;">
                        <center>
                            <b>Assign Vendor</b>
                        </center>
                    </div>
                    <form class="form doublePost" id="assign_vendor" action="<?php echo url('/')?>/generate_order" method="post" >
                        {{ csrf_field() }}
                        <div class="card-body">
                            <h3> Lead Details </h3>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-6 col-md-4 ">
                                            <label for="customer_name">Customer Name : </label>
                                        </div>
                                        <div class="col-6 col-md-8">
                                            <span>{{$lead_details[0]['customer_name']}}</span>
                                            <input type="hidden" id="customer_id" name="customer_id" value="{{$lead_details[0]['cust_id']}}">
                                            <input type="hidden" id="lead_id" name="lead_id" value="{{$lead_details[0]['id']}}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 col-md-4 ">
                                            <label for="cust_gender">Gender :</label>
                                        </div>
                                        <div class="col-6 col-md-8">
                                            <span>{{$lead_details[0]['cust_gender']}}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 col-md-4 ">
                                            <label for="payment_mode">Payment Mode :</label>
                                        </div>
                                        <div class="col-6 col-md-8">
                                            <span>{{$lead_details[0]['payment_mode']}}</span>
                                        </div>
                                    </div>
                                   
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="customer_address">Customer Address : </label>
                                        </div>
                                        <div class="col-md-8">
                                            <span>{{$lead_details[0]['address_line_1'].', '.$lead_details[0]['address_line_2']}}<br>{{$lead_details[0]['landmark'].', '.$lead_details[0]['area'].', '.$lead_details[0]['city'].', '.$lead_details[0]['pincode'].', '.$lead_details[0]['state'].', '.$lead_details[0]['country']}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <br>
                            <center>
                                <h3> Vendor Selection </h3>
                            </center>
                            @php
                                $sale_rental = json_decode($lead_details[0]['sale_rental']);
                            @endphp
                            <div class="row">
                                &emsp;
                                <div class="form-check">
                                    <input class="form-check-input assign" type="radio" name="assign" id="All" value="All" @if(in_array("Sale",$sale_rental)){{"disabled"}}@endif/>
                                    <label class="form-check-label"  for="All"> <strong>All</strong> </label>
                                </div>
                                &emsp;
                                <div class="form-check">
                                    <input class="form-check-input assign" type="radio" name="assign" id="Individual" value="Individual" checked/>
                                    <label class="form-check-label" for="Individual"> <strong>Individual</strong></label>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-2">
                                    <b>Delivery Date :</b>
                                </div>
                                <div class="col-md-3">
                                    <input type="date" class="form-control" id="delivery_date" name="delivery_date" value="{{date('Y-m-d',strtotime($lead_details[0]['converted_at']))}}">
                                </div>
                            </div>
                            <hr>
                            @if ($errors->any())
                                @foreach ($errors->all() as $error)
                                    <div class="alert alert-danger">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        {{$error}}
                                    </div>
                                @endforeach
                            @endif
                            <input type="hidden" name="hide" id="hide" value="hide">
                        </div>
                        <div class="row ">
                            <div class="col table_records table-responsive jim-table-responsive">
                                <table id="records" class="table table-bordered nowrap" style="width:100%; " data-page-length='250'>
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>Sale/Rental</th>
                                            <th>Required Equipment</th>
                                            <th>Quantity</th>
                                            <th>Addons</th>
                                            <th>Select vendor</th>
                                            <th>Select Warehouse</th>
                                            <th>Select Brand</th>
                                            <th>Inventory</th>
                                            <th>Additional Information</th>
                                            <th>Serial Number</th>
                                            <th>Warranty</th>
                                            <th>Deposit</th>
                                            <th>Product Rent/Sale rate</th>
                                            <th>Offered Rent/Sale price</th>
                                            <th>Total Offered Rent/Sale price</th>
                                            <th>transport</th>
                                        </tr>
                                    </thead>
                                    <tbody class="tbody">
                                        <?php
                                            $products = json_decode($lead_details[0]['equipment_requirement']);
                                            $products_id = json_decode($lead_details[0]['equipment_id']);
                                            $del_date = json_decode($lead_details[0]['del_date']);
                                            $equipment_quantity = json_decode($lead_details[0]['equipment_qty']);
                                            $months = json_decode($lead_details[0]['months']);
                                            $deposite = json_decode($lead_details[0]['deposite']);
                                            $deposite_total = json_decode($lead_details[0]['deposite_total']);
                                            $product_rent = json_decode($lead_details[0]['equipments_rent']);
                                            $offered_rent = json_decode($lead_details[0]['offered_rent']);
                                            $offered_rent_total = json_decode($lead_details[0]['offered_rent_total']);
                                            $transport = json_decode($lead_details[0]['transport']);
                                            $sale_rental = json_decode($lead_details[0]['sale_rental']);
                                            $count =0;
                                            $sr_no = 1;
                                            for ($i=0; $i <count($products) ; $i++) 
                                            {
                                                for($j=0; $j <$equipment_quantity[$i]; $j++)
                                                {
                                                    if($sale_rental[$i] == "Rental")
                                                    {
                                                            if($j==0)
                                                            {
                                                                $deposit_text = $deposite[$i];
                                                                $total_rent_text = $offered_rent_total[$i];
                                                                $transport_text = $transport[$i];
                                                            }
                                                            else
                                                            {
                                                                $deposit_text = 0;
                                                                $total_rent_text = 0;
                                                                $transport_text = 0;
                                                            }
                                                            ?>
                                                            <tr class="rows @if($sale_rental[$i] == "Sale"){{'table-info'}}@elseif($sale_rental[$i] == "Rental"){{"table-success"}}@endif" data-count="{{$count}}" >
                                                                <td data-label="Sr.No.">{{$sr_no}}</td>
                                                                <td data-label="Sale/Rental">
                                                                    @if(isset($sale_rental))
                                                                        <span name="sale_rental[]" id="sale_rental{{$count}}">{{$sale_rental[$i]}}</span>
                                                                        <input type="hidden" name="sale_rental_hidden[]" id="sale_rental_hidden{{$count}}" value="{{$sale_rental[$i]}}">
                                                                    @endif
                                                                </td>
                                                                <td data-label="Equipment">
                                                                    @if(isset($products_id)||isset($products))
                                                                        <span id="req_eq{{$count}}" name="req_eq[]" value="{{$products_id[$i]}}">{{$products[$i]}}</span>
                                                                        <input type="hidden" name="req_eq_hidden[]" id="req_eq_hidden{{$count}}" value="{{$products_id[$i]}}">
                                                                    @endif
                                                                </td>
                                                                <td data-label="Qty">
                                                                    @if(isset($equipment_quantity))
                                                                        <span name="eq_quantity[]" id="eq_quantity{{$count}}">{{1}}</span>
                                                                        <input type="hidden" name="eq_quantity_hidden[]" id="eq_quantity_hidden{{$count}}" value="{{1}}">
                                                                        <input type="hidden" name="months[]" id="months{{$count}}" value="@if(isset($months)){{$months[$i]}}@else{{1}}@endif">
                                                                    @endif
                                                                </td>                                                
                                                                <td data-label="IsUpgraded">
                                                                    <input type="hidden" name="upgraded_hidden[]" id="upgraded_hidden{{$count}}" value="Off">
                                                                    <input type="checkbox" class="upgraded" name="upgraded_check[]" id="upgraded_check{{$count}}" data-id = {{$count}}>
                                                                    <input type="text" name="upgraded_text[]" id="upgraded_text{{$count}}" class="form-control" style="display:none;">
                                                                </td>
                                                                <td id='select_vendor{{$i}}' class="text-wrap" data-label="Vendor">
                                                                    @if($default_vdr_details[$i]!="Not Found")
                                                                        <select class="select selectpicker form-control" data-dropup-auto="false" data-size="3" width="fit" title="Select Vendor" name="vendors[]" id="def_vendors{{$count}}" data-live-search="true" width="100%" required="true">
                                                                            <option value="{{$default_vdr_details[$i][0]->vendor_id}}" selected>{{$default_vdr_details[$i][0]->vendor_name}}</option>
                                                                        </select>
                                                                    @else
                                                                        <select class="select selectpicker form-control" data-dropup-auto="false" data-size="3" width="fit" title="Select Vendor" name="vendors[]" id="vendors{{$count}}" data-live-search="true" width="100%" required="true">
                                                                            <option value="" disabled>No Vendors Found</option>
                                                                        </select>
                                                                    @endif
                                                                </td>
                                                                <td id='select_warehouse{{$i}}' class="text-wrap" data-label="Warehouse">
                                                                    @if($default_vdr_details[$i]!="Not Found")
                                                                        <select class="select selectpicker form-control" width="fit" title="Select Warehouse" name="warehouses[]" id="def_warehouses{{$count}}" data-live-search="true" width="100%" required="true">
                                                                            <optgroup label="Vendor Warehouse">
                                                                                <option value="{{$default_vdr_details[$i][0]->warehouse_id}}" selected>{{$default_vdr_details[$i][0]->wh_name}}, {{$default_vdr_details[$i][0]->wh_area}}, {{$default_vdr_details[$i][0]->wh_city}}</option>
                                                                            </optgroup>
                                                                        </select>

                                                                        <input type="hidden" name="vendor_product_id[]" id="def_vendor_product_id{{$count}}" value="{{$default_vdr_details[$i][0]->vendor_product_id}}">
                                                                        <input type="hidden" name="vendor_product_details_id[]" id="def_vendor_product_details_id{{$count}}" value="{{$default_vdr_details[$i][0]->vendor_product_details_id}}">
                                                                    @else
                                                                        <select class="select selectpicker form-control" width="fit" title="Select Warehouse" name="warehouses[]" id="warehouses{{$count}}" data-live-search="true" width="100%" required="true" data-product_id = "0" data-vendor_id = "0" data-count = "{{$count}}" onchange='addWarehouseBrand($(this).data("count"),$(this).val(),"warehouse")'>
                                                                            <optgroup label="Virtual Warehouse">
                                                                                <option value="" disabled>No Warehouse Found</option>
                                                                            </optgroup>
                                                                            <optgroup label="Vendor Warehouse">
                                                                                <option value="" disabled>No Warehouse Found</option>
                                                                            </optgroup>
                                                                        </select>
                                                                        <input type="hidden" name="vendor_product_id[]" id="vendor_product_id{{$count}}">
                                                                        <input type="hidden" name="vendor_product_details_id[]" id="vendor_product_details_id{{$count}}">
                                                                    @endif
                                                                </td>
                                                                <td id='select_brand{{$i}}' class="text-wrap" data-label="Brand">
                                                                    @if($default_vdr_details[$i]!="Not Found")
                                                                        <select class="select selectpicker form-control" width="fit" title="Select Brand" name="brands[]" id="def_brands{{$count}}" data-live-search="true" width="100%" required="true">
                                                                            <option value="{{$default_vdr_details[$i][0]->brand_id}}" selected>{{$default_vdr_details[$i][0]->brand_name}}</option>
                                                                        </select>
                                                                    @else
                                                                        <select class="select selectpicker form-control" width="fit" title="Select Brand" name="brands[]" id="brands{{$count}}" data-live-search="true" width="100%" required="true" data-product_id = "0" data-vendor_id = "0" data-warehouse_id = "0" data-count = "{{$count}}" onchange='addWarehouseBrand($(this).data("count"),$(this).val(),"brand")'>
                                                                            <option value="" disabled>Select Warehouse First</option>
                                                                        </select>
                                                                    @endif
                                                                    
                                                                </td>
                                                                <td id='select_inventory{{$i}}' class="text-wrap" data-label="Inventory">
                                                                    @if($default_vdr_details[$i]!="Not Found")
                                                                        <select class="select selectpicker form-control" width="fit" title="Select Inventory" name="inventories[]" id="def_inventories{{$count}}" data-live-search="true" width="100%" required="true">
                                                                            <option value="{{$default_vdr_details[$i][0]->vendor_product_details_id}}" selected>{{$default_vdr_details[$i][0]->inventory_id}}</option>
                                                                        </select>
                                                                    @else
                                                                        <select class="select selectpicker form-control" width="fit" name="inventories[]" id="inventories{{$count}}" data-live-search="true" width="100%" required="true" data-product_id = "0" data-vendor_id = "0" data-warehouse_id = "0" data-count = "{{$count}}" required>
                                                                            <option value="AG" selected>Auto Generated</option>
                                                                        </select>
                                                                    @endif
                                                                </td>
                                                                <td data-label="Additional Info.">
                                                                    <input type="text" class="form-control" name="serial_numbers[]" id="serial_numbers{{$count}}" placeholder="Additional Info..." value="0">
                                                                </td>
                                                                <td>
                                                                    -
                                                                </td>
                                                                <td>
                                                                    -
                                                                </td>
                                                                <td data-label="Deposit">
                                                                    @if(isset($deposite))
                                                                        <span name="deposite[]" id="deposite{{$count}}>">{{$deposite[$i]}}</span>
                                                                        <input type="hidden" name="deposite[]" id="deposite" value="{{$deposite[$i]}}">
                                                                    @endif
                                                                    @if(isset($deposite_total))
                                                                        <span name="deposite_total[]" id="deposite_total{{$count}}>" style="display: none;">{{$deposit_text}}</span>
                                                                        <input type="hidden" name="deposite_total[]" id="deposite_total" value="{{$deposit_text}}">
                                                                    @endif
                                                                </td>
                                                                <td data-label="Product Rent/Sale rate">
                                                                    @if(isset($product_rent))
                                                                        <span name="product_rent[]" id="product_rent{{$count}}">{{$product_rent[$i]}}</span>
                                                                    @endif
                                                                </td>
                                                                <td data-label="Offered Rent/Sale rate">
                                                                    @if(isset($offered_rent[$i]))
                                                                        <span name="offered_rent[]" id="offered_rent1{{$count}}">{{$offered_rent[$i]}}</span>
                                                                        <input type="hidden" name="offered_rent[]" id="offered_rent{{$count}}" value="{{$offered_rent[$i]}}">
                                                                    @endif
                                                                </td>
                                                                <td data-label="Total Offered Rent/Sale rate" >
                                                                    @if(isset($offered_rent_total[$i]))
                                                                        <span name="offered_rent_total[]" id="offered_rent1_total{{$count}}">{{$total_rent_text}}</span>
                                                                        <input type="hidden" name="offered_rent_total[]" id="offered_rent_total{{$count}}" value="{{$total_rent_text}}">
                                                                    @endif
                                                                </td>
                                                                <td data-label="Transport">
                                                                    @if(isset($transport))
                                                                        <span name="transport[]" id="transport{{$count}}">{{$transport_text}}</span>
                                                                        <input type="hidden" name="transport[]" id="transport{{$count}}" value="{{$transport_text}}">
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <?php
                                                                    $count =$count+1;   
                                                                    $sr_no++;
                                                        
                                                    }
                                                    else if($sale_rental[$i] == "Sale")
                                                    {
                                                                
                                                            if($j==0)
                                                            {
                                                                $deposit_text = $deposite[$i];
                                                                $total_rent_text = $offered_rent_total[$i];
                                                                $transport_text = $transport[$i];
                                                            }
                                                            else
                                                            {
                                                                $deposit_text = 0;
                                                                $total_rent_text = 0;
                                                                $transport_text = 0;
                                                            }
                                                            ?>
                                                            <tr class="rows @if($sale_rental[$i] == "Sale"){{'table-info'}}@elseif($sale_rental[$i] == "Rental"){{"table-success"}}@endif" data-count="{{$count}}" >
                                                                <td data-label="Sr.No">{{$sr_no}}</td>
                                                                <td data-label="Sale/Rental">
                                                                    @if(isset($sale_rental))
                                                                        <span name="sale_rental[]" id="sale_rental{{$count}}">{{$sale_rental[$i]}}</span>
                                                                        <input type="hidden" name="sale_rental_hidden[]" id="sale_rental_hidden{{$count}}" value="{{$sale_rental[$i]}}">
                                                                    @endif
                                                                </td>
                                                                <td data-label="Equipment">
                                                                    @if(isset($products_id)||isset($products))
                                                                        <span id="req_eq{{$count}}" name="req_eq[]" value="{{$products_id[$i]}}">{{$products[$i]}}</span>
                                                                        <input type="hidden" name="req_eq_hidden[]" id="req_eq_hidden{{$count}}" value="{{$products_id[$i]}}">
                                                                    @endif
                                                                </td>
                                                                <td data-label="Qty">
                                                                    @if(isset($equipment_quantity))
                                                                        <span name="eq_quantity[]" id="eq_quantity{{$count}}">{{1}}</span>
                                                                        <input type="hidden" name="eq_quantity_hidden[]" id="eq_quantity_hidden{{$count}}" value="{{1}}">
                                                                        <input type="hidden" name="months[]" id="months{{$count}}" value="@if(isset($months)){{$months[$i]}}@else{{1}}@endif">
                                                                    @endif
                                                                </td>                                                
                                                                <td data-label="IsUpgraded">
                                                                        <input type="hidden" name="upgraded_hidden[]" id="upgraded_hidden{{$count}}" value="Off">
                                                                        <input type="hidden" name="upgraded_text[]" id="upgraded_text{{$count}}" class="form-control" style="display:none;">
                                                                </td>
                                                                <td data-label="Vendor" class="text-wrap" id='select_vendor{{$i}}'>
                                                                    <select class="select selectpicker form-control" width="fit" title="Select Vendor" name="vendors[]" id="vendors{{$count}}" data-live-search="true" data-size="5" width="100%" required="true">
                                                                        <option value="" disabled>No Vendors Found</option>
                                                                    </select>
                                                                </td>
                                                                <td data-label="Warehouse" class="text-wrap" id='select_warehouse{{$i}}'>
                                                                    <select class="select selectpicker form-control" width="fit" title="Select Warehouse" name="warehouses[]" id="warehouses{{$count}}" data-live-search="true" data-size="5" width="100%" required="true">
                                                                        <optgroup label="Virtual Warehouse">
                                                                            <option value="" disabled>No Warehouse Found</option>
                                                                        </optgroup>
                                                                        <optgroup label="Vendor Warehouse">
                                                                            <option value="" disabled>No Warehouse Found</option>
                                                                        </optgroup>
                                                                    </select>
                                                                    <input type="hidden" name="vendor_product_id[]" id="vendor_product_id{{$count}}">
                                                                    <input type="hidden" name="vendor_product_details_id[]" id="vendor_product_details_id{{$count}}">
                                                                </td>
                                                                <td data-label="Brand" class="text-wrap" id='select_brand{{$i}}'>
                                                                    <select class="select selectpicker form-control" width="fit" title="Select Brand" name="brands[]" id="brands{{$count}}" data-size="5" data-live-search="true" width="100%" required="true">
                                                                        <option value="" disabled>Select Warehouse First</option>
                                                                    </select>
                                                                </td>
                                                                <td id='select_inventory{{$i}}' class="text-wrap" data-label="Inventory">                                                                
                                                                    <input type="hidden" name="inventories[]" id="inventories{{$count}}">
                                                                </td>
                                                                <td data-label="Additional Info">
                                                                    <input type="text" class="form-control" name="serial_numbers[]" id="serial_numbers{{$count}}" placeholder="Additional Info..." value="0">
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control" name="sale_serial_no[]" id="sale_serial_no{{$count}}" placeholder="Serial no..." value="">
                                                                </td>
                                                                <td>
                                                                    <input type="number" class="form-control" name="sale_warranty[]" id="sale_warranty{{$count}}" placeholder="Warranty..." value="0">
                                                                </td>
                                                                <td data-label="Deposit">
                                                                    @if(isset($deposite))
                                                                        <span name="deposite[]" id="deposite{{$count}}>">{{$deposite[$i]}}</span>
                                                                        <input type="hidden" name="deposite[]" id="deposite" value="{{$deposite[$i]}}">
                                                                    @endif
                                                                    @if(isset($deposite_total))
                                                                        <span name="deposite_total[]" id="deposite_total{{$count}}>" style="display: none;">{{$deposit_text}}</span>
                                                                        <input type="hidden" name="deposite_total[]" id="deposite_total" value="{{$deposit_text}}">
                                                                    @endif
                                                                </td>
                                                                <td data-label="Product Rent/Sale rate">
                                                                    @if(isset($product_rent))
                                                                        <span name="product_rent[]" id="product_rent{{$count}}">{{$product_rent[$i]}}</span>
                                                                    @endif
                                                                </td>
                                                                <td data-label="Offered Rent/Sale rate">
                                                                    @if(isset($offered_rent[$i]))
                                                                        <span name="offered_rent[]" id="offered_rent1{{$count}}">{{$offered_rent[$i]}}</span>
                                                                        <input type="hidden" name="offered_rent[]" id="offered_rent{{$count}}" value="{{$offered_rent[$i]}}">
                                                                    @endif
                                                                </td>
                                                                <td data-label="Total Offered Rent/Sale rate">
                                                                    @if(isset($offered_rent_total[$i]))
                                                                        <span name="offered_rent_total[]" id="offered_rent1_total{{$count}}">{{$total_rent_text}}</span>
                                                                        <input type="hidden" name="offered_rent_total[]" id="offered_rent_total{{$count}}" value="{{$total_rent_text}}">
                                                                    @endif
                                                                </td>
                                                                <td data-label="Transport">
                                                                    @if(isset($transport))
                                                                        <span name="transport[]" id="transport{{$count}}">{{$transport_text}}</span>
                                                                        <input type="hidden" name="transport[]" id="transport{{$count}}" value="{{$transport_text}}">
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <?php
                                                                    $count =$count+1;   
                                                                    $sr_no++;
                                                        
                                                    }
                                                }
                                            }
                                        ?>
                                    </tbody>
                                </table>
                                <input type="hidden" name="row_ct" id="row_ct" value="<?php echo $count;?>">
                                
                            </div>
                        </div> 
                        <center>
                            <input class="submit btn btn-primary" type="submit" id="submit" name="submit" value="submit">
                        </center>
                    </form>
                </div>
            </div>
        </div>
    </div>   
    <div id="addwarehousebrand" class="modal modal-fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span id="modal_title">Add Warehouse / Batch</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body container-fluid">
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="awb_vendorname">Vendor</label>
                            <input type="text" name="awb_vendorname" id="awb_vendorname" class="form-control form-control-sm" disabled>
                            <input type="hidden" name="awb_vendorname_hidden" id="awb_vendorname_hidden" class="form-control form-control-sm">
                            <input type="hidden" name="awb_productid_hidden" id="awb_productid_hidden" class="form-control form-control-sm">
                            <input type="hidden" name="awb_count_hidden" id="awb_count_hidden" class="form-control form-control-sm">
                            <input type="hidden" name="awb_type_hidden" id="awb_type_hidden" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label for="awb_warehouse">Warehouse</label>
                            <select name="awb_warehouse" id="awb_warehouse" class="select selectpicker form-control form-control-sm" title="Warehouse" data-live-search="true" data-size="5">
                                <option value="0" disabled>No Warehouse Found</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="awb_brand">Brand</label>
                            <select name="awb_brand" id="awb_brand" class="select selectpicker form-control form-control-sm" title="Brand" data-live-search="true" data-size="5">
                                <option value="0" disabled>No Brand Found</option>
                            </select>
                        </div>
                    </div>
                    {{-- <div class="row form-group">
                        <div class="col text-center">
                            <button type="submit" name="submitform" id="submitform" onclick="addVdrProduct();" class="btn btn-sm btn-outline-success">Add</button>
                        </div>
                    </div> --}}
                    <div class="row alert-div">
                        <span id="erroralert" class="text-danger"></span>
                        <span id="successalert" class="text-success"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="submitform" id="submitform" onclick="addVdrProduct();" class="btn btn-sm btn-outline-success">Add</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div id="addbrand" class="modal modal-fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span id="modal_title">Add Warehouse / Batch</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body container-fluid">
                    <div class="row form-group">
                        <div class="col">
                            <label for="awb_add_brand">Brand</label>
                            <input type="text" name="awb_add_brand" id="awb_add_brand" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col text-center">
                            <button type="submit" name="submitformbrand" id="submitformbrand" class="btn btn-sm btn-outline-success">Add</button>
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
</body>

    @section('script')
        <script>
            function addVdrProduct(){
                let warehouse_id = $("#awb_warehouse").val();
                let brand_id = $("#awb_brand").val();
                let vendor_id = $("#awb_vendorname_hidden").val();
                let product_id = $("#awb_productid_hidden").val();
                let count = $("#awb_count_hidden").val();
                console.log("In function");
                if(warehouse_id == null || warehouse_id == ""){                    
                    $("#awb_warehouse").selectpicker('setStyle', 'btn-danger',"add"); 
                }
                else if(brand_id == null || brand_id == "")
                {
                    $("#awb_brand").selectpicker('setStyle', 'btn-danger',"add");
                }
                else{
                    $("#awb_warehouse").selectpicker('setStyle', 'btn-danger',"remove"); 
                    let dataString = ({_token:"{{ csrf_token() }}",vendorid:""+vendor_id,productid:""+product_id,warehouseid:""+warehouse_id,brandid:""+brand_id});
                    $.ajax({
                        type:"POST",
                        url:"{{route('add-warehouse-brand')}}",
                        data:dataString,
                        cache:false,
                        success:function(resp){
                            console.log(resp);
                            resp = jQuery.parseJSON(resp);
                            if(resp['status'] == 'exists'){
                                $("#erroralert").text(resp['description']);

                            }else{
                                $("#successalert").text(resp['description']);
                                $("#vendors"+count).change();
                                setTimeout( function()
                                {
                                    $("#warehouses"+count).selectpicker("refresh");
                                    $("#warehouses"+count).val(warehouse_id);
                                    $("#warehouses"+count).selectpicker("refresh");
                                    $("#warehouses"+count).change();
                                    setTimeout( function()
                                    {
                                        $("#brands"+count).selectpicker("refresh");
                                        $("#brands"+count).val(brand_id);
                                        $("#brands"+count).selectpicker("refresh");
                                        $("#brands"+count).change();
                                        $("#addwarehousebrand").modal("hide");
                                    },1000);
                                },1000);
                            }
                        },error:function(err){
                            $("#erroralert").text(err.getMessage());
                            alert("Something went wrong contact administartion!");
                        }
                    });
                }
            }
            function addWarehouseBrand(count,value,type){
                if(value == 'Add'){
                    $("#erroralert").text(null);
                    $("#successalert").text(null);
                    let productid = $("#req_eq_hidden"+count).val();
                    let vendorid = $("#vendors"+count).val();
                    if(type == 'warehouse' || type == 'brand'){
                        let dataString = ({vendorid:""+vendorid,productid:""+productid,requesttype:type});
                        $.ajax({
                            type: "GET",
                            url: "{{route('add-warehouse-brand')}}",
                            data: dataString,
                            cache:false,
                            success: function (data)
                            {
                                console.log
                                var data = jQuery.parseJSON(data);
                                let warehouses = data['warehouse_details'];
                                $("#awb_vendorname").val($("#vendors"+count+" option:selected").text());
                                $("#awb_vendorname_hidden").val($("#vendors"+count).val());
                                $("#awb_productid_hidden").val($("#req_eq_hidden"+count).val());
                                $("#awb_count_hidden").val(count);
                                $("#awb_type_hidden").val(type);
                                $("#awb_warehouse")
                                .find("option")
                                .remove()
                                .end();
                                for(var j = 0; j < warehouses.length; j++)
                                {
                                    $("#awb_warehouse").append("<option value='"+warehouses[j].id+"'>"+warehouses[j].wh_name+', '+warehouses[j].wh_area+', '+warehouses[j].wh_city+"</option>");
                                }
                                $('#awb_warehouse').selectpicker('refresh');
                                if(type=="brand")
                                {
                                    $('#awb_warehouse').val($("#warehouses"+count).val());
                                    $('#awb_warehouse').attr("disabled",true);
                                }
                                else{
                                    $('#awb_warehouse').removeAttr("disabled");
                                }
                                $('#awb_warehouse').selectpicker('refresh');
                                let brands = data['product_brands'];
                                $("#awb_brand")
                                .find("option")
                                .remove()
                                .end();
                                $("#awb_brand").append("<option value='Add'>Add New</option>");
                                for(var j = 0; j < brands.length; j++)
                                {
                                    $("#awb_brand").append("<option value='"+brands[j].id+"'>"+brands[j].brand_name+"</option>");
                                }
                                $('#awb_brand').selectpicker('refresh');
                            }
                        });
                    }else{

                    }
                    $("#addwarehousebrand").modal("show");
                }
            }
            $("#awb_brand").change(function(){
                if($("#awb_brand").val() == 'Add'){
                    $("#addbrand").modal("show");
                }
            });
            $("#submitformbrand").click(function(){
                if($("#awb_add_brand").val() == null || $("#awb_add_brand").val() == ""){
                    $("#awb_add_brand").attr('style','border:dashed 2px red;');
                }
                else{
                    let dataString = ({brandname:""+$("#awb_add_brand").val(),productid:""+$("#awb_productid_hidden").val(),requesttype:"addnewbrand"});
                    let warehouse_id = $("#awb_warehouse").val();
                    $.ajax({
                        type: "GET",
                        url: "{{route('add-warehouse-brand')}}",
                        data: dataString,
                        cache:false,
                        success: function (data)
                        {
                            let res = jQuery.parseJSON(data);
                            console.log(res["id"]);
                            addWarehouseBrand($("#awb_count_hidden").val(),'Add',$("#awb_type_hidden").val());
                            setTimeout( function()
                            {
                                $("#awb_brand").selectpicker("refresh");
                                $("#awb_brand").val(res["id"]);
                                $("#awb_brand").selectpicker("refresh");
                                $("#awb_warehouse").selectpicker("refresh");
                                $("#awb_warehouse").val(warehouse_id);
                                $("#awb_warehouse").selectpicker("refresh");
                                
                            },1000);
                            $("#addbrand").modal("hide");
                        }
                    })
                }
            });
        </script>
        <script>
            $(".upgraded").change(function(){
                let id = $(this).data("id");
                if(this.checked)
                {
                    $("#upgraded_text"+id).show();
                    $("#upgraded_hidden"+id).val("On");
                }
                else
                {
                    $("#upgraded_text"+id).hide();
                    $("#upgraded_hidden"+id).val("Off");
                }
            });
            $(document).ready(function() 
            {
                //\\*******Select Vendors on page load (Default Individual Selected)********//\\
                var count_eq = $('#row_ct').val();
                for(var i = 0; i < count_eq; i++)
                {
                    var sale_rental = $('#sale_rental_hidden'+i).val();
                    var equipment = $('#req_eq_hidden'+i).val();
                    var eq_quantity = $('#eq_quantity_hidden'+i).val();
                    var dataString_equipment = (equipment);
                    if(sale_rental == "Rental")
                    {
                        (function(index)
                        {
                            $.ajax({
                                type: "GET",
                                url: "<?php echo url('/'); ?>/individual_vendor_batch/"+dataString_equipment+"/"+eq_quantity,                                
                                success: function (data)
                                {
                                    var vendors = jQuery.parseJSON(data);
                                    var vendorsLength = vendors.length;
                                    $("#vendors"+index)
                                    .find("option")
                                    .remove()
                                    .end();
                                    for(var j = 0; j < vendorsLength; j++)
                                    {
                                        $("#vendors"+index).append("<option value='"+vendors[j].vendor_id+"'>"+vendors[j].vendor_name+"</option>");
                                    }
                                    $('#vendors'+index).selectpicker('refresh');
                                }
                            });
                        })(i);
                    }
                    else
                    {
                        (function(index)
                        {
                            $.ajax({
                                type: "GET",
                                url: "<?php echo url('/'); ?>/individual_vendor_batch_sale",
                                success: function (data)
                                {
                                    var vendors = jQuery.parseJSON(data);                                    
                                    var vendorsLength = vendors.length;
                                    $("#vendors"+index)
                                    .find("option")
                                    .remove()
                                    .end();
                                    for(var j = 0; j < vendorsLength; j++)
                                    {
                                        $("#vendors"+index).append("<option value='"+vendors[j].vendor_id+"'>"+vendors[j].vendor_name+"</option>");
                                    }
                                    $('#vendors'+index).selectpicker('refresh');
                                }
                            });
                        })(i);
                    }
                }                
                $('input[type=radio][name=assign]').change('click',function() 
                {
                    if(this.value == "Individual")
                    {
                        for(var i = 0; i < count_eq; i++)
                        {
                            $("#vendors"+i).selectpicker('show');
                            $("#vendors"+i).find("option").remove().end();
                            $('#vendors'+i).selectpicker('refresh');
                            $("#warehouses"+i).selectpicker('show');
                            $("#warehouses"+i).find("option").remove().end();
                            $("#warehouses"+i).append("<option value='Select Vendor First' disabled>Select Vendor First</option>");
                            $('#warehouses'+i).selectpicker('refresh');
                        }
                        for(var i = 0; i < count_eq; i++)
                        {
                            $("#brands"+i).find("option").remove().end();
                            $("#brands"+i).append("<option value='Select Warehouse First' disabled>Select Warehouse First</option>");
                            $('#brands'+i).selectpicker('refresh');
                            $("#batches"+i).find("option").remove().end();
                            $("#batches"+i).append("<option value='Select Brand First' disabled>Select Warehouse First</option>");
                            $('#batches'+i).selectpicker('refresh');
                            $("#vendor_product_id"+i).val(null);
                            $("#vendor_product_details_id"+i).val(null);
                            $("#prod"+i).text("-");
                            $("#product_details_span"+i).text("-");
                        }
                        for(var i = 0; i < count_eq; i++)
                        {
                            var equipment = $('#req_eq_hidden'+i).val();
                            var eq_quantity = $('#eq_quantity_hidden'+i).val();
                            var dataString_equipment = (equipment);
                            (function(index)
                            {
                                $.ajax({
                                    type: "GET",
                                    url: "<?php echo url('/'); ?>/individual_vendor_batch/"+dataString_equipment+"/"+eq_quantity,
                                    success: function (data)
                                    {
                                        var vendors = jQuery.parseJSON(data);
                                        var vendorsLength = vendors.length;
                                        $("#vendors"+index)
                                        .find("option")
                                        .remove()
                                        .end();
                                        for(var j = 0; j < vendorsLength; j++)
                                        {
                                            $("#vendors"+index).append("<option value='"+vendors[j].vendor_id+"'>"+vendors[j].vendor_name+"</option>");
                                        }
                                        $('#vendors'+index).selectpicker('refresh');
                                    }
                                });
                            })(i);
                        }
                    }
                });
                //\\*******After Vendor Selection get vendors all warehouses where that product exists********//\\
                $('#records').on('click', 'tr', function () {
                    //------For Individual Vendors------//
                    var count_eq_ex = $('#row_ct').val();
                    var count = this.dataset.count;
                    $('#vendor_approval_all').on("change",function(){
                        if(this.checked)
                        {
                            for(var i = 0; i < count_eq_ex; i++)
                            {
                                $('#vendor_approval'+i).prop('checked',true);
                                $('#vendor_approval_hidden'+i).val('Approved');
                            }
                        }
                        else
                        {
                            for(var i = 0; i < count_eq_ex; i++)
                            {
                                $('#vendor_approval'+i).prop('checked',false);
                                $('#vendor_approval_hidden'+i).val('Pending');
                            }
                        }
                    });
                    $('#vendor_approval'+count).on("change",function(){
                        if(this.checked)
                        {
                            
                        }
                        else
                        {
                            $('#vendor_approval_all').prop('checked', false);
                            $('#vendor_approval_hidden'+count).val('Pending');
                        }
                    });
                    var selected_type = $("input[name='assign']:checked").val();
                    if(selected_type == "Individual")
                    {
                        $("#vendors"+count).on("change",function()
                        {
                            var equipment = $('#req_eq_hidden'+count).val();
                            var vendor_id = $("#vendors"+count).val();
                            var sale_rental = $('#sale_rental_hidden'+count).val();
                            if(sale_rental == "Rental")
                            {
                                $("#brands"+count)
                                    .find("option")
                                    .remove()
                                    .end();
                                $('#brands'+count).selectpicker('refresh');
                                $("#batches"+count)
                                        .find("option")
                                        .remove()
                                        .end();
                                $('#batches'+count).selectpicker('refresh');
                                $.ajax({
                                    type: "GET",
                                    url: "<?php echo url('/');?>/select_vendor_warehouses/"+equipment+"/"+vendor_id,
                                    cache: false,
                                    success: function(data)
                                    {
                                        var warehouses = jQuery.parseJSON(data);
                                        $("#warehouses"+count)
                                        .find('option')
                                        .remove()
                                        .end();
                                        if(typeof(warehouses[0][0]) != "undefined" && warehouses[0][0] !== null && warehouses[0][0] !== "")
                                        {                                            
                                            warehousesLength_viw = warehouses[0].length
                                            for(var j = 0; j < warehousesLength_viw; j++)
                                            {
                                                $("#warehouses"+count).find('optgroup[label="Virtual Warehouse"]').append("<option value='"+warehouses[0][j].warehouse_id+"'>"+warehouses[0][j].wh_name+","+warehouses[0][j].wh_area+","+warehouses[0][j].wh_city+"</option>");
                                            }
                                            $("#warehouses"+count).append("</optgroup>");
                                        }
                                        $("#warehouses"+count).find('optgroup[label="Vendor Warehouse"]').append("<option value='Add' >Add New</option>");
                                        if(typeof(warehouses[1][0]) != "undefined" && warehouses[1][0] !== null  && warehouses[1][0] !== "")
                                        {
                                            warehousesLength_viw = warehouses[1].length
                                            for(var j = 0; j < warehousesLength_viw; j++)
                                            {
                                                $("#warehouses"+count).find('optgroup[label="Vendor Warehouse"]').append("<option value='"+warehouses[1][j].warehouse_id+"'>"+warehouses[1][j].wh_name+","+warehouses[1][j].wh_area+","+warehouses[1][j].wh_city+"</option>");
                                            }
                                        }
                                        $('#warehouses'+count).selectpicker('refresh');
                                    }
                                });
                            }
                            else
                            {
                                $("#brands"+count)
                                    .find("option")
                                    .remove()
                                    .end();
                                $('#brands'+count).selectpicker('refresh');
                                $("#batches"+count)
                                        .find("option")
                                        .remove()
                                        .end();
                                $('#batches'+count).selectpicker('refresh');
                                $.ajax({
                                    type: "GET",
                                    url: "<?php echo url('/');?>/select_vendor_warehouses_sale/"+vendor_id,
                                    cache: false,
                                    success: function(data)
                                    {
                                        var warehouses = jQuery.parseJSON(data);
                                        var warehousesLength = warehouses.length;
                                        $("#warehouses"+count)
                                        .find("option")
                                        .remove()
                                        .end();
                                        for(var j = 0; j < warehousesLength; j++)
                                        {
                                            $("#warehouses"+count).find('optgroup[label="Vendor Warehouse"]').append("<option value='"+warehouses[j].warehouse_id+"'>"+warehouses[j].wh_name+","+warehouses[j].wh_area+","+warehouses[j].wh_city+"</option>");
                                        }
                                        $('#warehouses'+count).selectpicker('refresh');
                                    }
                                });
                            }
                        });
                        $("#warehouses"+count).on("change",function()
                        { 
                                var equipment = $('#req_eq_hidden'+count).val();
                                var vendor_id = $("#vendors"+count).val();
                                var warehouse_id = $("#warehouses"+count).val();
                                var sale_rental = $('#sale_rental_hidden'+count).val();
                                // let optgroup = $('option:selected', $("#warehouses"+count)).parent('optgroup').prop('label');
                                
                                // console.log(optgroup);
                                if(sale_rental == "Rental")
                                {
                                        $("#brands"+count)
                                        .find("option")
                                        .remove()
                                        .end();
                                        $('#brands'+count).selectpicker('refresh');
                                    if($("#warehouses"+count).val() != 'Add'){
                                        $.ajax({
                                            type: "GET",
                                            url: "<?php echo url('/');?>/select_product_brand/"+equipment+"/"+vendor_id+"/"+warehouse_id,
                                            cache: false,
                                            success: function(data)
                                            {
                                                $("#brands"+count)
                                                .find("option")
                                                .remove()
                                                .end();
                                                $("#brands"+count).append("<option value='Add'>Add New</option>");
                                                var brands = jQuery.parseJSON(data);
                                                var brandsLength = brands.length;
                                                for(var j = 0; j < brandsLength; j++)
                                                {
                                                    $("#brands"+count).append("<option value='"+brands[j].brand_id+"'>"+brands[j].brand_name+"</option>");
                                                }
                                                $("#brands"+count).append("<option value='unknown'>Other</option>");
                                                $('#brands'+count).selectpicker('refresh');
                                                
                                            }
                                        });
                                    }
                                }
                                else
                                {
                                    $("#batches"+count)
                                        .find("option")
                                        .remove()
                                        .end();

                                    $.ajax({
                                        type: "GET",
                                        url: "<?php echo url('/');?>/select_product_brand_sale/"+equipment,
                                        cache: false,
                                        success: function(data)
                                        {
                                            var brands = jQuery.parseJSON(data);
                                            var brandsLength = brands.length;
                                            $("#brands"+count)
                                            .find("option")
                                            .remove()
                                            .end();
                                            for(var j = 0; j < brandsLength; j++)
                                            {
                                                $("#brands"+count).append("<option value='"+brands[j].brand_id+"'>"+brands[j].brand_name+"</option>");
                                            }
                                            $('#brands'+count).selectpicker('refresh');
                                        }
                                    });
                                }
                            
                        });
                        $("#brands"+count).on("change",function()
                        {
                            var equipment = $('#req_eq_hidden'+count).val();
                            var vendor_id = $("#vendors"+count).val();
                            var warehouse_id = $("#warehouses"+count).val();
                            var brand_id = $("#brands"+count).val();
                            $("#inventories"+count).find("option").remove().end();
                            if($("#vendors"+count).val() == 17 || $('option:selected', $("#warehouses"+count)).parent('optgroup').prop('label') == 'Virtual Warehouse'){
                                if(brand_id != 'unknown' && brand_id != 'Add'){
                                    $.ajax({
                                        type: "GET",
                                        url: "<?php echo url('/');?>/select_inventory/"+vendor_id+"/"+warehouse_id+"/"+brand_id+"/"+equipment,
                                        cache: false,
                                        success: function(data)
                                        {
                                            var inventories = jQuery.parseJSON(data);                                            
                                            var brandsLength = inventories.length;
                                            $("#inventories"+count)
                                            .find("option")
                                            .remove()
                                            .end();
                                            for(var j = 0; j < brandsLength; j++)
                                            {
                                                $("#inventories"+count).append("<option value='"+inventories[j].vendor_product_details_id+"'>"+inventories[j].inventory_id+"</option>");
                                            }                                
                                            $('#inventories'+count).selectpicker('refresh');
                                        }
                                    });
                                }
                            }else{
                                $("#inventories"+count).append("<option value='AG'>Auto Generated</option>");
                            }
                            $("#inventories"+count).selectpicker("refresh");
                        });
                    }
                });
            });
        </script>                                                         

    @endsection
    
</html>
