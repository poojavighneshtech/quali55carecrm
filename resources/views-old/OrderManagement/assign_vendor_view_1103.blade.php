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
                                <div class="col-md-2">
                                    <label for="customer_name">Customer Name : </label>
                                </div>
                                <div class="col-md-4">
                                    <span>{{$lead_details[0]['customer_name']}}</span>
                                    <input type="hidden" id="customer_name" name="customer_name" value="{{$lead_details[0]['customer_name']}}">
                                    <input type="hidden" id="customer_id" name="customer_id" value="{{$lead_details[0]['cust_id']}}">
                                    <input type="hidden" id="mobile_no" name="mobile_no" value="{{$lead_details[0]['primary_contact_no']}}">
                                    <input type="hidden" id="lead_id" name="lead_id" value="{{$lead_details[0]['id']}}">
                                    <input type="hidden" id="location" name="location" value="{{$lead_details[0]['location']}}">
                                </div>
                            
                                <div class="col-md-2">
                                    <label for="customer_address">Customer Address : </label>
                                </div>
                                <div class="col-md-4">
                                    <span>{{$lead_details[0]['address_line_1'].', '.$lead_details[0]['address_line_2']}}<br>{{$lead_details[0]['landmark'].', '.$lead_details[0]['area'].', '.$lead_details[0]['city'].', '.$lead_details[0]['pincode'].', '.$lead_details[0]['state'].', '.$lead_details[0]['country']}}</span>
                                    <input type="hidden" id="customer_address" name="customer_address" value="{{$lead_details[0]['address_line_1'].', '.$lead_details[0]['address_line_2'].','.$lead_details[0]['landmark'].', '.$lead_details[0]['area'].', '.$lead_details[0]['city'].', '.$lead_details[0]['pincode'].', '.$lead_details[0]['state'].', '.$lead_details[0]['country']}}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label for="payment_mode">Payment Mode :</label>
                                </div>
                                <div class="col-md-4">
                                    <span>{{$lead_details[0]['payment_mode']}}</span>
                                    <input type="hidden" name="payment_mode" id="payment_mode" value="{{$lead_details[0]['payment_mode']}}">
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
                                {{-- <div class="col-md-3">
                                    <button type="button" name="btnExplode" id="btnExplode" class="btn btn-primary" onclick="explode();">Explode</button>
                                    <button type="button" name="btnImplode" id="btnImplode" class="btn btn-primary" style="display: none;" onclick="implode();">Implode</button>
                                </div> --}}
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

                            <div class="row ">
                                <div class="col table_records">
                                    <table id="records" class="table table-bordered table-responsive nowrap" data-page-length='250' style="width:100%; ">
                                        <thead>
                                            <tr>
                                                <th>Sr.No.</th>
                                                {{-- <th><input type="checkbox" name="vendor_approval_all" id="vendor_approval_all"><label for="vendor_approval_all">Vendor Approval</label></th> --}}
                                                <th>Sale/Rental</th>
                                                <th>Del Date</th>
                                                <th>Required Equipment</th>
                                                <th>Quantity</th>
                                                <th>Select vendor</th>
                                                <th>Select Warehouse</th>
                                                <th>Select Brand</th>
                                                <th>Select Batch</th>
                                                <th>Select Inventory</th>
                                                <th>Vendor Product Rent/Price</th>
                                                <th>Additional Information</th>
                                                <th>Deposit</th>
                                                <th>Product Rent/Sale rate</th>
                                                <th>Offered Rent/Sale price</th>
                                                <th>Total Offered Rent/Sale price</th>
                                                <th>Profit Margin</th>
                                                <th>transport</th>
                                            </tr>
                                        </thead>
                                        <tbody class="tbody">
                                            <?php
                                                $products = json_decode($lead_details[0]['equipment_requirement']);
                                                $products_id = json_decode($lead_details[0]['equipment_id']);
                                                $del_date = json_decode($lead_details[0]['del_date']);
                                                $equipment_quantity = json_decode($lead_details[0]['equipment_qty']);
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
                                                    if($sale_rental[$i] == "Rental")
                                                    {
                                                        for($j=0; $j <$equipment_quantity[$i]; $j++)
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
                                                                <td>{{$sr_no}}</td>
                                                                {{-- <td><center><input type="checkbox" name="vendor_approval[]" id="vendor_approval{{$count}}"></center><input type="hidden" name="vendor_approval_hidden[]" id="vendor_approval_hidden{{$count}}" value="Pending"></td> --}}
                                                                <td>
                                                                    @if(isset($sale_rental))
                                                                        <span name="sale_rental[]" id="sale_rental{{$count}}">{{$sale_rental[$i]}}</span>
                                                                        <input type="hidden" name="sale_rental_hidden[]" id="sale_rental_hidden{{$count}}" value="{{$sale_rental[$i]}}">
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if(isset($del_date))
                                                                        <span name="del_date[]" id="del_date{{$count}}" value="{{$del_date[$i]}}">{{date('d-m-Y',strtotime($del_date[$i]))}}</span></td>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if(isset($products_id)||isset($products))
                                                                        <span id="req_eq{{$count}}" name="req_eq[]" value="{{$products_id[$i]}}">{{$products[$i]}}</span>
                                                                        <input type="hidden" name="req_eq_hidden[]" id="req_eq_hidden{{$count}}" value="{{$products_id[$i]}}">
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if(isset($equipment_quantity))
                                                                        <span name="eq_quantity[]" id="eq_quantity{{$count}}">{{1}}</span>
                                                                        <input type="hidden" name="eq_quantity_hidden[]" id="eq_quantity_hidden{{$count}}" value="{{1}}">
                                                                    @endif
                                                                </td>                                                
                                                                <td id='select_vendor{{$i}}'>
                                                                    <select class="selectpicker" width="fit" title="Select Vendor" name="vendors[]" id="vendors{{$count}}" data-live-search="true" width="100%" required="true">
                                                                        <option value="" disabled>No Vendors Found</option>
                                                                    </select>
                                                                </td>
                                                                <td id='select_warehouse{{$i}}'>
                                                                    <select class="selectpicker" width="fit" title="Select Warehouse" name="warehouses[]" id="warehouses{{$count}}" data-live-search="true" width="100%" required="true">
                                                                        {{-- <option value="" disabled>Select Vendor First</option> --}}
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
                                                                <td id='select_brand{{$i}}'>
                                                                    <select class="selectpicker" width="fit" title="Select Brand" name="brands[]" id="brands{{$count}}" data-live-search="true" width="100%" required="true">
                                                                        <option value="" disabled>Select Warehouse First</option>
                                                                    </select>
                                                                </td>
                                                                <td id='select_batch{{$i}}'>
                                                                    @if($sale_rental[$i] == "Rental")
                                                                    <select class="selectpicker" width="fit" title="Select Batch" name="batches[]" id="batches{{$count}}" data-live-search="true" width="100%" required="true">
                                                                        <option value="" disabled>Select Brand First</option>
                                                                    </select>
                                                                    @else
                                                                        <input type="hidden" name="batches[]" id="batches{{$count}}" value="0">
                                                                        <span><b> - </b></span>
                                                                    @endif
                                                                </td>
                                                                <td id='select_inventory{{$i}}'>
                                                                    @if($sale_rental[$i] == "Rental")
                                                                    <select class="selectpicker" width="fit" title="Select Inventory" name="inventories[]" id="inventories{{$count}}" data-live-search="true" width="100%" required="true">
                                                                        <option value="" disabled>Select Batch First</option>
                                                                    </select>
                                                                    @else
                                                                        <input type="hidden" name="inventories[]" id="inventories{{$count}}" value="0">
                                                                        <span><b> - </b></span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <span name="prod[]" id="prod{{$count}}">-</span>
                                                                    <input type="hidden" name="product_price[]" id="product_price" value="0">
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control" name="serial_numbers[]" id="serial_numbers{{$count}}" placeholder="Additional Info..." value="0">
                                                                </td>
                                                                <td>
                                                                    @if(isset($deposite))
                                                                        <span name="deposite[]" id="deposite{{$count}}>">{{$deposite[$i]}}</span>
                                                                        <input type="hidden" name="deposite[]" id="deposite" value="{{$deposite[$i]}}">
                                                                    @endif
                                                                    @if(isset($deposite_total))
                                                                        <span name="deposite_total[]" id="deposite_total{{$count}}>" style="display: none;">{{$deposit_text}}</span>
                                                                        <input type="hidden" name="deposite_total[]" id="deposite_total" value="{{$deposit_text}}">
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if(isset($product_rent))
                                                                        <span name="product_rent[]" id="product_rent{{$count}}">{{$product_rent[$i]}}</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if(isset($offered_rent[$i]))
                                                                        <span name="offered_rent[]" id="offered_rent1{{$count}}">{{$offered_rent[$i]}}</span>
                                                                        <input type="hidden" name="offered_rent[]" id="offered_rent{{$count}}" value="{{$offered_rent[$i]}}">
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if(isset($offered_rent_total[$i]))
                                                                        <span name="offered_rent_total[]" id="offered_rent1_total{{$count}}">{{$total_rent_text}}</span>
                                                                        <input type="hidden" name="offered_rent_total[]" id="offered_rent_total{{$count}}" value="{{$total_rent_text}}">
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <span name="profit[]" id="profit{{$count}}">0(0%)</span>
                                                                    <input type="hidden" name="profit_hidden[]" id="profit_hidden{{$count}}" value="0">
                                                                </td>
                                                                <td>
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
                                                    else if($sale_rental[$i] == "Sale")
                                                    {
                                                                $deposit_text = $deposite[$i];
                                                                $total_rent_text = $offered_rent_total[$i];
                                                                $transport_text = $transport[$i];
                                                            ?>
                                                            <tr class="rows @if($sale_rental[$i] == "Sale"){{'table-info'}}@elseif($sale_rental[$i] == "Rental"){{"table-success"}}@endif" data-count="{{$count}}" >
                                                                <td>{{$sr_no}}</td>
                                                                {{-- <td><center><input type="checkbox" name="vendor_approval[]" id="vendor_approval{{$count}}"></center><input type="hidden" name="vendor_approval_hidden[]" id="vendor_approval_hidden{{$count}}" value="Pending"></td> --}}
                                                                <td>
                                                                    @if(isset($sale_rental))
                                                                        <span name="sale_rental[]" id="sale_rental{{$count}}">{{$sale_rental[$i]}}</span>
                                                                        <input type="hidden" name="sale_rental_hidden[]" id="sale_rental_hidden{{$count}}" value="{{$sale_rental[$i]}}">
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if(isset($del_date))
                                                                        <span name="del_date[]" id="del_date{{$count}}" value="{{$del_date[$i]}}">{{date('d-m-Y',strtotime($del_date[$i]))}}</span></td>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if(isset($products_id)||isset($products))
                                                                        <span id="req_eq{{$count}}" name="req_eq[]" value="{{$products_id[$i]}}">{{$products[$i]}}</span>
                                                                        <input type="hidden" name="req_eq_hidden[]" id="req_eq_hidden{{$count}}" value="{{$products_id[$i]}}">
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if(isset($equipment_quantity))
                                                                        <span name="eq_quantity[]" id="eq_quantity{{$count}}">{{$equipment_quantity[$i]}}</span>
                                                                        <input type="hidden" name="eq_quantity_hidden[]" id="eq_quantity_hidden{{$count}}" value="{{$equipment_quantity[$i]}}">
                                                                    @endif
                                                                </td>                                                
                                                                <td id='select_vendor{{$i}}'>
                                                                    <select class="selectpicker" width="fit" title="Select Vendor" name="vendors[]" id="vendors{{$count}}" data-live-search="true" width="100%" required="true">
                                                                        <option value="" disabled>No Vendors Found</option>
                                                                    </select>
                                                                </td>
                                                                <td id='select_warehouse{{$i}}'>
                                                                    <select class="selectpicker" width="fit" title="Select Warehouse" name="warehouses[]" id="warehouses{{$count}}" data-live-search="true" width="100%" required="true">
                                                                        {{-- <option value="" disabled>Select Vendor First</option> --}}
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
                                                                <td id='select_brand{{$i}}'>
                                                                    <select class="selectpicker" width="fit" title="Select Brand" name="brands[]" id="brands{{$count}}" data-live-search="true" width="100%" required="true">
                                                                        <option value="" disabled>Select Warehouse First</option>
                                                                    </select>
                                                                </td>
                                                                <td id='select_batch{{$i}}'>
                                                                    @if($sale_rental[$i] == "Rental")
                                                                    <select class="selectpicker" width="fit" title="Select Batch" name="batches[]" id="batches{{$count}}" data-live-search="true" width="100%" required="true">
                                                                        <option value="" disabled>Select Brand First</option>
                                                                    </select>
                                                                    @else
                                                                        <input type="hidden" name="batches[]" id="batches{{$count}}" value="0">
                                                                        <span><b> - </b></span>
                                                                    @endif
                                                                </td>
                                                                <td id='select_inventory{{$i}}'>
                                                                    @if($sale_rental[$i] == "Rental")
                                                                    <select class="selectpicker" width="fit" title="Select Inventory" name="inventories[]" id="inventories{{$count}}" data-live-search="true" width="100%" required="true">
                                                                        <option value="" disabled>Select Batch First</option>
                                                                    </select>
                                                                    @else
                                                                        <input type="hidden" name="inventories[]" id="inventories{{$count}}" value="0">
                                                                        <span><b> - </b></span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <span name="prod[]" id="prod{{$count}}">-</span>
                                                                    <input type="hidden" name="product_price[]" id="product_price" value="0">
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control" name="serial_numbers[]" id="serial_numbers{{$count}}" placeholder="Additional Info..." value="0">
                                                                </td>
                                                                <td>
                                                                    @if(isset($deposite))
                                                                        <span name="deposite[]" id="deposite{{$count}}>">{{$deposite[$i]}}</span>
                                                                        <input type="hidden" name="deposite[]" id="deposite" value="{{$deposite[$i]}}">
                                                                    @endif
                                                                    @if(isset($deposite_total))
                                                                        <span name="deposite_total[]" id="deposite_total{{$count}}>" style="display: none;">{{$deposit_text}}</span>
                                                                        <input type="hidden" name="deposite_total[]" id="deposite_total" value="{{$deposit_text}}">
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if(isset($product_rent))
                                                                        <span name="product_rent[]" id="product_rent{{$count}}">{{$product_rent[$i]}}</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if(isset($offered_rent[$i]))
                                                                        <span name="offered_rent[]" id="offered_rent1{{$count}}">{{$offered_rent[$i]}}</span>
                                                                        <input type="hidden" name="offered_rent[]" id="offered_rent{{$count}}" value="{{$offered_rent[$i]}}">
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if(isset($offered_rent_total[$i]))
                                                                        <span name="offered_rent_total[]" id="offered_rent1_total{{$count}}">{{$total_rent_text}}</span>
                                                                        <input type="hidden" name="offered_rent_total[]" id="offered_rent_total{{$count}}" value="{{$total_rent_text}}">
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <span name="profit[]" id="profit{{$count}}">0(0%)</span>
                                                                    <input type="hidden" name="profit_hidden[]" id="profit_hidden{{$count}}" value="0">
                                                                </td>
                                                                <td>
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
                                            ?>
                                        </tbody>
                                    </table>
                                    <input type="hidden" name="row_ct" id="row_ct" value="<?php echo $count;?>">
                                    <center>
                                        <input class="submit btn btn-primary" type="submit" id="submit" name="submit" value="submit">
                                    </center>
                                </div>
                            </div>   
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>   
    @endsection
</body>

    @section('script')
        <script>
            $(document).ready(function() 
            {
                //\\*******Select Vendors on page load (Default Individual Selected)********//\\
                var count_eq = $('#row_ct').val();
                //alert(count_eq);
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
                                // cache:false,
                                success: function (data)
                                {
                                    var vendors = jQuery.parseJSON(data);
                                    // console.log(vendors);
                                    var vendorsLength = vendors.length;
                                    // var temp_var = i-2;
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
                                // cache:false,
                                success: function (data)
                                {
                                    var vendors = jQuery.parseJSON(data);
                                    // console.log(vendors);
                                    var vendorsLength = vendors.length;
                                    // var temp_var = i-2;
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
                //document.querySelector('input[name=assign][value=Individual]').checked = true;
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
                                    // cache:false,
                                    success: function (data)
                                    {
                                        var vendors = jQuery.parseJSON(data);
                                        // console.log(vendors);
                                        var vendorsLength = vendors.length;
                                        // var temp_var = i-2;
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
                    else if(this.value == "All")
                    {
                        var equipments = [];
                        for(var i = 1; i < count_eq; i++)
                        {
                            $("#vendors"+i).selectpicker('hide');
                            $("#vendors"+i).removeAttr('required');
                            $("#warehouses"+i).selectpicker('hide');
                            $("#warehouses"+i).removeAttr('required');
                        }
                        for(var i = 0; i < count_eq; i++)
                        {
                            var equipment = $('#req_eq_hidden'+i).val();
                            equipments.push(equipment);
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
                        $("#vendors"+0).find("option").remove().end();
                        var dataString_equipment = JSON.stringify(equipments);
                        var dataString = ({_token:"{{ csrf_token() }}",equipments:""+dataString_equipment});
                        //alert(dataString_equipment);
                        $.ajax({
                            type: "POST",
                            url: "<?php echo url('/');?>/all_vendor_batch",
                            data: dataString,
                            cache: false,
                            success: function(data)
                            {
                                var vendors = jQuery.parseJSON(data);
                                // console.log(vendors);
                                var vendorsLength = vendors.length;
                                // var temp_var = i-2;
                                $("#vendors"+0)
                                .find("option")
                                .remove()
                                .end();
                                for(var j = 0; j < vendorsLength; j++)
                                {
                                    $("#vendors"+0).append("<option value='"+vendors[j].vendor_id+"'>"+vendors[j].vendor_name+"</option>");
                                }
                                $('#vendors'+0).selectpicker('refresh');
                            }
                        });
                        $("#warehouses"+0).find("option").remove().end();
                        $("#warehouses"+0).append("<option value='Select Vendor First' disabled>Select Vendor First</option>");
                        $('#warehouses'+0).selectpicker('refresh');
                    }
                });
                //\\*******After Vendor Selection get vendors all warehouses where that product exists********//\\
                // $('.table tbody tr').click(function(){
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
                            //var equipment = $('table tr td').text();
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
                                        // console.log(data);
                                        var warehouses = jQuery.parseJSON(data);
                                        // console.log(warehouses);
                                        // var warehousesLength = warehouses.length;
                                        $("#warehouses"+count)
                                        .find('option')
                                        .remove()
                                        .end();
                                        // console.log(warehouses);
                                        if(typeof(warehouses[0][0]) != "undefined" && warehouses[0][0] !== null && warehouses[0][0] !== "")
                                        {
                                            // alert("Virtual");
                                            warehousesLength_viw = warehouses[0].length
                                            // $("#warehouses"+count)
                                            //     .find('option')
                                            //     .remove()
                                            //     .end();
                                            // $("#warehouses"+count).append("<optgroup label='Virtual Warehouse'>");
                                            // $("#warehouses"+count).find('optgroup[label="Virtual Warehouse"]').show();
                                            for(var j = 0; j < warehousesLength_viw; j++)
                                            {
                                                $("#warehouses"+count).find('optgroup[label="Virtual Warehouse"]').append("<option value='"+warehouses[0][j].warehouse_id+"'>"+warehouses[0][j].wh_name+","+warehouses[0][j].wh_area+","+warehouses[0][j].wh_city+"</option>");
                                            }
                                            $("#warehouses"+count).append("</optgroup>");
                                        }
                                        if(typeof(warehouses[1][0]) != "undefined" && warehouses[1][0] !== null  && warehouses[1][0] !== "")
                                        {
                                            // alert("Vendor");
                                            warehousesLength_viw = warehouses[1].length
                                            // $("#warehouses"+count).find('optgroup[label="Vendor Warehouse"]').show();
                                            // $("#warehouses"+count)
                                            //     .find('option')
                                            //     .remove()
                                            //     .end();
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
                                        //console.log(warehouses);
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
                            //var equipment = $('table tr td').text();
                            var equipment = $('#req_eq_hidden'+count).val();
                            var vendor_id = $("#vendors"+count).val();
                            var warehouse_id = $("#warehouses"+count).val();
                            var sale_rental = $('#sale_rental_hidden'+count).val();
                            if(sale_rental == "Rental")
                            {
                                $("#batches"+count)
                                    .find("option")
                                    .remove()
                                    .end();

                                $.ajax({
                                    type: "GET",
                                    url: "<?php echo url('/');?>/select_product_brand/"+equipment+"/"+vendor_id+"/"+warehouse_id,
                                    cache: false,
                                    success: function(data)
                                    {
                                        var brands = jQuery.parseJSON(data);
                                        // console.log(brands);
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
                                        //console.log(brands);
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
                            //var equipment = $('table tr td').text();
                            var equipment = $('#req_eq_hidden'+count).val();
                            var vendor_id = $("#vendors"+count).val();
                            var warehouse_id = $("#warehouses"+count).val();
                            var brand_id = $("#brands"+count).val();

                            $.ajax({
                                type: "GET",
                                url: "<?php echo url('/');?>/select_batch/"+equipment+"/"+vendor_id+"/"+warehouse_id+"/"+brand_id,
                                cache: false,
                                success: function(data)
                                {
                                    var batches = jQuery.parseJSON(data);
                                    //console.log(batches);
                                    var brandsLength = batches.length;
                                    $("#batches"+count)
                                    .find("option")
                                    .remove()
                                    .end();
                                    for(var j = 0; j < brandsLength; j++)
                                    {
                                        $("#batches"+count).append("<option value='"+batches[j].vendor_product_id+"'>"+batches[j].batch_name+" - "+batches[j].product_rent+"</option>");
                                    }                                
                                    $('#batches'+count).selectpicker('refresh');
                                }
                            });
                        });
                        $("#batches"+count).on("change",function()
                        {
                            var product_id = $("#batches"+count).val();
                            var warehouse_id = $("#warehouses"+count).val();

                            $.ajax({
                                type: "GET",
                                url: "<?php echo url('/');?>/select_inventory/"+product_id+"/"+warehouse_id,
                                cache: false,
                                success: function(data)
                                {
                                    var inventories = jQuery.parseJSON(data);
                                    //console.log(inventories);
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
                        });
                        $("#inventories"+count).on("change",function()
                        {
                            var product_id = $("#batches"+count).val();

                            $.ajax({
                                type: "GET",
                                url: "<?php echo url('/');?>/getDetails/"+product_id,
                                cache: false,
                                success: function(data)
                                {
                                    var details = jQuery.parseJSON(data);
                                    // console.log(details);
                                    $("#vendor_product_id"+count).val(details[0].vendor_product_id);
                                    $("#vendor_product_details_id"+count).val($("#inventories"+count).val());
                                    $("#prod"+count).text(details[0].product_rent);
                                    var prod_details_string = details[0].product_details;
                                    var short_str = prod_details_string.substring(0,50)+"...";
                                    //$("#product_details_span"+count).text(details[0].product_details);
                                    $("#product_details_span"+count).text(short_str);
                                }
                            });
                        });
                    }
                    else if(selected_type == "All")
                    {
                        $("#vendors"+0).on("change",function()
                        {
                            var vendor_id = $("#vendors"+0).val();
                            var equipments = [];
                            for(var i = 0; i < count_eq; i++)
                            {
                                var equipment = $('#req_eq_hidden'+i).val();
                                equipments.push(equipment);
                            }
                            var dataString_equipment = JSON.stringify(equipments);
                            var dataString = ({_token:"{{ csrf_token() }}",equipments:""+dataString_equipment,vendor_id:""+vendor_id});
                            $("#brands"+count).find("option").remove().end();
                            $('#brands'+count).selectpicker('refresh');
                            $("#batches"+count).find("option").remove().end();
                            $('#batches'+count).selectpicker('refresh');
                            $.ajax({
                                type: "POST",
                                url: "<?php echo url('/');?>/select_vendor_warehouses_all",
                                data: dataString,
                                cache: false,
                                success: function(data)
                                {
                                    var warehouses = jQuery.parseJSON(data);
                                    //console.log(warehouses);
                                    var warehousesLength = warehouses.length;
                                    $("#warehouses"+0).find("option").remove().end();
                                    for(var j = 0; j < warehousesLength; j++)
                                    {
                                        $("#warehouses"+0).append("<option value='"+warehouses[j].warehouse_id+"'>"+warehouses[j].wh_name+","+warehouses[j].wh_area+","+warehouses[j].wh_city+"</option>");
                                    }
                                    $('#warehouses'+0).selectpicker('refresh');
                                }
                            });
                        });
                        $("#warehouses"+0).on("change",function()
                        {
                            //var equipment = $('table tr td').text();
                            // var equipment = $('#req_eq_hidden'+count).val();
                            var vendor_id = $("#vendors"+0).val();
                            var warehouse_id = $("#warehouses"+0).val();
                            $("#batches"+count).find("option").remove().end();
                            // var vendor_id = $("#vendors"+0).val();
                            var equipments = [];
                            for(var i = 0; i < count_eq; i++)
                            {
                                var equipment = $('#req_eq_hidden'+i).val();
                                equipments.push(equipment);
                            }
                            var dataString_equipment = JSON.stringify(equipments);
                            var dataString = ({_token:"{{ csrf_token() }}",equipments:""+dataString_equipment,vendor_id:""+vendor_id,warehouse_id:""+warehouse_id});
                            $.ajax({
                                type: "POST",
                                url: "<?php echo url('/');?>/select_product_brand_all",
                                data: dataString,
                                cache: false,
                                success: function(data)
                                {
                                    var brands = jQuery.parseJSON(data);
                                    // console.log(brands);
                                    var brandsLength = brands.length;                                   
                                    for(var j = 0; j < brandsLength; j++)
                                    {
                                        $("#brands"+j).find("option").remove().end(); 
                                        var brandsLengthRow = brands[j].length;
                                        for(var k = 0; k < brandsLengthRow; k++)
                                        {
                                            $("#brands"+j).append("<option value='"+brands[j][k].brand_id+"'>"+brands[j][k].brand_name+"</option>");
                                        }
                                        $('#brands'+j).selectpicker('refresh');
                                    }
                                }
                            });
                        });
                        $("#brands"+count).on("change",function()
                        { 
                            //var equipment = $('table tr td').text();
                            var equipment = $('#req_eq_hidden'+count).val();
                            // var equipments = [];
                            // for(var i = 0; i < count_eq; i++)
                            // {
                            //     var equipment = $('#req_eq_hidden'+i).val();
                            //     equipments.push(equipment);
                            // }
                            var vendor_id = $("#vendors"+0).val();
                            var warehouse_id = $("#warehouses"+0).val();
                            var brand_id = $("#brands"+count).val();

                            $.ajax({
                                type: "GET",
                                url: "<?php echo url('/');?>/select_batch/"+equipment+"/"+vendor_id+"/"+warehouse_id+"/"+brand_id,
                                cache: false,
                                success: function(data)
                                {
                                    var batches = jQuery.parseJSON(data);
                                    //console.log(batches);
                                    var brandsLength = batches.length;
                                    $("#batches"+count)
                                    .find("option")
                                    .remove()
                                    .end();
                                    for(var j = 0; j < brandsLength; j++)
                                    {
                                        $("#batches"+count).append("<option value='"+batches[j].vendor_product_id+"'>"+batches[j].batch_name+" - "+batches[j].product_rent+"</option>");
                                    }                                
                                    $('#batches'+count).selectpicker('refresh');
                                }
                            });
                        });
                        $("#batches"+count).on("change",function()
                        { 
                            var product_id = $("#batches"+count).val();

                            $.ajax({
                                type: "GET",
                                url: "<?php echo url('/');?>/getDetails/"+product_id,
                                cache: false,
                                success: function(data)
                                {
                                    var details = jQuery.parseJSON(data);
                                    // console.log(details);
                                    $("#vendor_product_id"+count).val(details[0].vendor_product_id);
                                    $("#vendor_product_details_id"+count).val(details[0].vendor_product_details_id);
                                    $("#prod"+count).text(details[0].product_rent);
                                    $("#product_details_span"+count).text(details[0].product_details);
                                }
                            });
                        });
                    }
                });
            });
            function explode1()
            {
                // alert("Explode Table");
                var customer_id = $("#customer_id").val();
                var lead_id = $("#lead_id").val();
                var dataString = ({_token:"{{ csrf_token() }}",customer_id:""+customer_id,lead_id:""+lead_id});
                // $('#records tbody').empty();
                $.ajax({
                    type: "POST",
                    url: "<?php echo url('/'); ?>/assign_vendor_exp",
                    // cache:false,
                    data:dataString,
                    success: function (data)
                    {
                        var data_decoded = jQuery.parseJSON(data);
                        // console.log(data_decoded);
                        var products = jQuery.parseJSON(data_decoded.lead_details[0].equipment_requirement);
                        var products_id = jQuery.parseJSON(data_decoded.lead_details[0].equipment_id);
                        var del_date = jQuery.parseJSON(data_decoded.lead_details[0].del_date);
                        var equipment_quantity = jQuery.parseJSON(data_decoded.lead_details[0].equipment_qty);
                        var deposite = jQuery.parseJSON(data_decoded.lead_details[0].deposite);
                        var deposite_total = jQuery.parseJSON(data_decoded.lead_details[0].deposite_total);
                        var product_rent = jQuery.parseJSON(data_decoded.lead_details[0].equipments_rent);
                        var offered_rent = jQuery.parseJSON(data_decoded.lead_details[0].offered_rent);
                        var offered_rent_total = jQuery.parseJSON(data_decoded.lead_details[0].offered_rent_total);
                        var transport = jQuery.parseJSON(data_decoded.lead_details[0].transport);
                        var sale_rental = jQuery.parseJSON(data_decoded.lead_details[0].sale_rental);
                        var count =0;
                        var sr_no1 = 1;
                        var tr_array = [];
                        var dataSource = [];
                        for (var i=0; i <products.length; i++) 
                        {
                            for(var j=0; j <equipment_quantity[i]; j++)
                            {
                                var sale_rental_text = "";
                                var deposit_text = 0;
                                if(sale_rental[i] == 'Sale')
                                {
                                    sale_rental_text = 'table-info';
                                }
                                else if(sale_rental[i] == 'Rental')
                                {
                                    sale_rental_text = 'table-success';
                                }
                                if(j==0)
                                {
                                    deposit_text = deposite[i];
                                    total_rent_text = offered_rent_total[i];
                                    transport_text = transport[i];
                                }
                                else
                                {
                                    deposit_text = 0;
                                    total_rent_text = 0;
                                    transport_text = 0;
                                }
                                dataSource.push({sr_no: "'"+sr_no1+"'",sale_rental: '<span name="sale_rental[]" id="sale_rental'+count+'">'+sale_rental[i]+'</span><input type="hidden" name="sale_rental_hidden[]" id="sale_rental_hidden'+count+'" value="'+sale_rental+'">',del_date: '<span name="del_date[]" id="del_date'+count+'" value="'+del_date[i]+'">'+del_date[i]+'</span>',require_equip: '<span id="req_eq"'+count+'" name="req_eq[]" value="'+products_id[i]+'">'+products[i]+'</span><input type="hidden" name="req_eq_hidden[]" id="req_eq_hidden'+count+'" value="'+products_id[i]+'">',qty: '<span name="eq_quantity[]" id="eq_quantity'+count+'">'+1+'</span><input type="hidden" name="eq_quantity_hidden[]" id="eq_quantity_hidden'+count+'" value="'+1+'">',select_vendor: '<select class="selectpicker" width="fit" title="Select Vendor" name="vendors[]" id="vendors'+count+'" data-live-search="true" width="100%" required="true"><option value="" disabled>No Vendors Found</option></select>',select_warehouse: '<select class="selectpicker" width="fit" title="Select Warehouse" name="warehouses[]" id="warehouses'+count+'" data-live-search="true" width="100%" required="true"><option value="" disabled>Select Vendor First</option></select><input type="hidden" name="vendor_product_id[]" id="vendor_product_id'+count+'"><input type="hidden" name="vendor_product_details_id[]" id="vendor_product_details_id'+count+'">',select_brand: '<select class="selectpicker" width="fit" title="Select Brand" name="brands[]" id="brands'+count+'" data-live-search="true" width="100%" required="true"><option value="" disabled>Select Warehouse First</option></select>',select_batch: '<select class="selectpicker" width="fit" title="Select Batch" name="batches[]" id="batches'+count+'" data-live-search="true" width="100%" required="true"><option value="" disabled>Select Brand First</option></select><input type="hidden" name="batches[]" id="batches'+count+'" value="0"><span><b> - </b></span>',vdr_rent_price: '<span name="prod[]" id="prod'+count+'">-</span><input type="hidden" name="product_price[]" id="product_price" value="0">',additional_info: '<input type="text" class="form-control" name="serial_numbers[]" id="serial_numbers'+count+'" placeholder="Additional Info..." value="0">',deposit: '<span name="deposite[]" id="deposite'+count+'">'+deposit_text+'</span><input type="hidden" name="deposite[]" id="deposite" value="'+deposit_text+'"><span name="deposite_total[]" id="deposite_total'+count+'" style="display: none">'+deposite_total[i]+'</span><input type="hidden" name="deposite_total[]" id="deposite_total" value="'+deposite_total[i]+'">',prod_rent_sale: '<span name="product_rent[]" id="product_rent'+count+'">'+product_rent[i]+'</span>',offered_rent_sale: '<span name="offered_rent[]" id="offered_rent1'+count+'">'+offered_rent[i]+'</span><input type="hidden" name="offered_rent[]" id="offered_rent'+count+'" value="'+offered_rent[i]+'">',total_offered_rent_sale: '<span name="offered_rent_total[]" id="offered_rent1_total'+count+'">'+total_rent_text+'</span><input type="hidden" name="offered_rent_total[]" id="offered_rent_total'+count+'" value="'+total_rent_text+'">',profit_margin: '<span name="profit[]" id="profit'+count+'">0(0%)</span><input type="hidden" name="profit_hidden[]" id="profit_hidden'+count+'" value="0">',transport: '<span name="transport[]" id="transport'+count+'">'+transport_text+'</span><input type="hidden" name="transport[]" id="transport'+count+'" value="'+transport_text+'"></tr>'});
                                // var string_array = "{sr_no: '"+sr_no+"',sale_rental: '<span name='sale_rental[]' id='sale_rental"+count+"'>"+sale_rental[i]+"</span><input type='hidden' name='sale_rental_hidden[]' id='sale_rental_hidden"+count+"' value='"+sale_rental+"'>',del_date: '<span name='del_date[]' id='del_date"+count+"' value='"+del_date[i]+"'>"+del_date[i]+"</span>',require_equip: '<span id='req_eq"+count+"' name='req_eq[]' value='"+products_id[i]+"'>"+products[i]+"</span><input type='hidden' name='req_eq_hidden[]' id='req_eq_hidden"+count+"' value='"+products_id[i]+"'>',qty: '<span name='eq_quantity[]' id='eq_quantity"+count+"'>"+1+"</span><input type='hidden' name='eq_quantity_hidden[]' id='eq_quantity_hidden"+count+"' value='"+1+"'>',select_vendor: '<select class='selectpicker' width='fit' title='Select Vendor' name='vendors[]' id='vendors"+count+"' data-live-search='true' width='100%' required='true'><option value='' disabled>No Vendors Found</option></select>',select_warehouse: '<select class='selectpicker' width='fit' title='Select Warehouse' name='warehouses[]' id='warehouses"+count+"' data-live-search='true' width='100%' required='true'><option value='' disabled>Select Vendor First</option></select><input type='hidden' name='vendor_product_id[]' id='vendor_product_id"+count+"'>',select_brand: '<select class='selectpicker' width='fit' title='Select Brand' name='brands[]' id='brands"+count+"' data-live-search='true' width='100%' required='true'><option value='' disabled>Select Warehouse First</option></select>',select_batch: '<select class='selectpicker' width='fit' title='Select Batch' name='batches[]' id='batches"+count+"' data-live-search='true' width='100%' required='true'><option value=' disabled>Select Brand First</option></select><input type='hidden' name='batches[]' id='batches"+count+"' value='0'><span><b> - </b></span>',vdr_rent_price: '<span name='prod[]' id='prod"+count+"'>-</span><input type='hidden' name='product_price[]' id='product_price' value='0'>';additional_info: '<input type='text' class='form-control' name='serial_numbers[]' id='serial_numbers"+count+"' placeholder='Additional Info...' value='0'>';deposit: '<span name='deposite[]' id='deposite"+count+"'>"+deposit_text+"</span><input type='hidden' name='deposite[]' id='deposite' value='"+deposit_text+"'><span name='deposite_total[]' id='deposite_total"+count+">' style='display: none'>"+deposite_total[i]+"</span><input type='hidden' name='deposite_total[]' id='deposite_total' value='"+deposite_total[i]+"'>';prod_rent_sale: '<span name='product_rent[]' id='product_rent"+count+"'>"+product_rent[i]+"</span>';offered_rent_sale: '<span name='offered_rent[]' id='offered_rent1"+count+"'>"+offered_rent[i]+"</span><input type='hidden' name='offered_rent[]' id='offered_rent"+count+"' value='"+offered_rent[i]+"'>';total_offered_rent_sale: '<span name='offered_rent_total[]' id='offered_rent1_total"+count+"'>"+total_rent_text+"</span><input type='hidden' name='offered_rent_total[]' id='offered_rent_total"+count+"' value='"+total_rent_text+"'>';profit_margin: '<span name='profit[]' id='profit"+count+"'>0(0%)</span><input type='hidden' name='profit_hidden[]' id='profit_hidden"+count+"' value='0'>';transport: '<span name='transport[]' id='transport"+count+"'>"+transport_text+"</span><input type='hidden' name='transport[]' id='transport"+count+"' value='"+transport_text+"'></tr>'}";
                                // tr_array.push(string_array);
                                sr_no1 = sr_no1 + 1;
                                count =count+1;
                            }
                        }
                        
                        // dataSource.push(tr_array);
                        console.log(dataSource);
                        $('#records tbody').remove();
                        $('#records').DataTable().destroy();
                        $('#records').DataTable({  
                            dom: 'Bfrtip',  
                            data: dataSource,  
                            columns: [  
                                {  
                                    render: function (data, type, row, meta) {  
                                        return meta.row + meta.settings._iDisplayStart + 1;  
                                    }  
                                },
                                // { data: 'sr_no'},
                                { data: 'sale_rental'},
                                { data: 'del_date'},
                                { data: 'require_equip'},
                                { data: 'qty'},
                                { data: 'select_vendor'},
                                { data: 'select_warehouse'},
                                { data: 'select_brand'},
                                { data: 'select_batch'},
                                { data: 'vdr_rent_price'},
                                { data: 'additional_info'},
                                { data: 'deposit'},
                                { data: 'prod_rent_sale'},
                                { data: 'offered_rent_sale'},
                                { data: 'total_offered_rent_sale'},
                                { data: 'profit_margin'},
                                { data: 'transport'}
                            ],  
                            "paging": true,  
                            "info": true,  
                            "language": {  
                                "emptyTable": "No data available"  
                            },  
                            "fnRowCallback": function (nRow, aData, iDisplayIndex) {  
                                $("td:first", nRow).html(iDisplayIndex + 1);  
                                return nRow;  
                            },  
                        })
                        
                        $('.selectpicker').selectpicker('refresh');
                        $("#btnExplode").hide();
                        $("#btnImplode").show();
                        $('#row_ct').val(count);
                        select_vendor();
                        //\\*******Select Vendors on page load (Default Individual Selected)********//\\
                    }
                });
            }
            function explode()
            {
                // alert("Explode Table");
                var customer_id = $("#customer_id").val();
                var lead_id = $("#lead_id").val();
                var dataString = ({_token:"{{ csrf_token() }}",customer_id:""+customer_id,lead_id:""+lead_id});
                // $('#records tbody').empty();
                
                $.ajax({
                    type: "POST",
                    url: "<?php echo url('/'); ?>/assign_vendor_exp",
                    // cache:false,
                    data:dataString,
                    success: function (data)
                    {
                        var data_decoded = jQuery.parseJSON(data);
                        // console.log(data_decoded);
                        var products = jQuery.parseJSON(data_decoded.lead_details[0].equipment_requirement);
                        var products_id = jQuery.parseJSON(data_decoded.lead_details[0].equipment_id);
                        var del_date = jQuery.parseJSON(data_decoded.lead_details[0].del_date);
                        var equipment_quantity = jQuery.parseJSON(data_decoded.lead_details[0].equipment_qty);
                        var deposite = jQuery.parseJSON(data_decoded.lead_details[0].deposite);
                        var deposite_total = jQuery.parseJSON(data_decoded.lead_details[0].deposite_total);
                        var product_rent = jQuery.parseJSON(data_decoded.lead_details[0].equipments_rent);
                        var offered_rent = jQuery.parseJSON(data_decoded.lead_details[0].offered_rent);
                        var offered_rent_total = jQuery.parseJSON(data_decoded.lead_details[0].offered_rent_total);
                        var transport = jQuery.parseJSON(data_decoded.lead_details[0].transport);
                        var sale_rental = jQuery.parseJSON(data_decoded.lead_details[0].sale_rental);
                        var count =0;
                        var rows = "<tbody class='tbody'>";
                            var sr_no = 1;
                        for (var i=0; i <products.length; i++) 
                        {
                            for(var j=0; j <equipment_quantity[i]; j++)
                            {
                                var sale_rental_text = "";
                                var deposit_text = 0;
                                if(sale_rental[i] == 'Sale')
                                {
                                    sale_rental_text = 'table-info';
                                }
                                else if(sale_rental[i] == 'Rental')
                                {
                                    sale_rental_text = 'table-success';
                                }
                                if(j==0)
                                {
                                    deposit_text = deposite[i];
                                    total_rent_text = offered_rent_total[i];
                                    transport_text = transport[i];
                                }
                                else
                                {
                                    deposit_text = 0;
                                    total_rent_text = 0;
                                    transport_text = 0;
                                }
                                rows += "<tr class='rows "+sale_rental_text+"' data-count='"+count+"'>";
                                rows += "<td>"+sr_no+"</td>";
                                rows += "<td><center><input type='checkbox' name='vendor_approval[]' id='vendor_approval"+count+"'></center><input type='hidden' name='vendor_approval_hidden[]' id='vendor_approval_hidden"+count+"' value='Pending'></td>";
                                rows += "<td><span name='sale_rental[]' id='sale_rental"+count+"'>"+sale_rental[i]+"</span><input type='hidden' name='sale_rental_hidden[]' id='sale_rental_hidden"+count+"' value='"+sale_rental[i]+"'></td>";                            
                                rows += "<td><span name='del_date[]' id='del_date"+count+"' value='"+del_date[i]+"'>"+del_date[i]+"</span></td>";
                                rows += "<td><span id='req_eq"+count+"' name='req_eq[]' value='"+products_id[i]+"'>"+products[i]+"</span><input type='hidden' name='req_eq_hidden[]' id='req_eq_hidden"+count+"' value='"+products_id[i]+"'></td>";
                                rows += "<td><span name='eq_quantity[]' id='eq_quantity"+count+"'>"+1+"</span><input type='hidden' name='eq_quantity_hidden[]' id='eq_quantity_hidden"+count+"' value='"+1+"'></td>";
                                rows += "<td id='select_vendor"+j+"'><select class='selectpicker' width='fit' title='Select Vendor' name='vendors[]' id='vendors"+count+"' data-live-search='true' width='100%' required='true'><option value='' disabled>No Vendors Found</option></select></td>";
                                rows += "<td id='select_warehouse"+j+"'><select class='selectpicker' width='fit' title='Select Warehouse' name='warehouses[]' id='warehouses"+count+"' data-live-search='true' width='100%' required='true'><option value='' disabled>Select Vendor First</option></select><input type='hidden' name='vendor_product_id[]' id='vendor_product_id"+count+"'><input type='hidden' name='vendor_product_details_id[]' id='vendor_product_details_id"+count+"'></td>";
                                rows += "<td id='select_brand"+j+"'><select class='selectpicker' width='fit' title='Select Brand' name='brands[]' id='brands"+count+"' data-live-search='true' width='100%' required='true'><option value='' disabled>Select Warehouse First</option></select></td>";
                                rows += "<td id='select_batch"+j+"'><select class='selectpicker' width='fit' title='Select Batch' name='batches[]' id='batches"+count+"' data-live-search='true' width='100%' required='true'><option value=' disabled>Select Brand First</option></select><input type='hidden' name='batches[]' id='batches"+count+"' value='0'><span><b> - </b></span></td>";
                                rows += "<td><span name='prod[]' id='prod"+count+"'>-</span><input type='hidden' name='product_price[]' id='product_price' value='0'></td>";
                                rows += "<td><input type='text' class='form-control' name='serial_numbers[]' id='serial_numbers"+count+"' placeholder='Additional Info...' value='0'></td>";
                                rows += "<td><span name='deposite[]' id='deposite"+count+"'>"+deposit_text+"</span><input type='hidden' name='deposite[]' id='deposite' value='"+deposit_text+"'><span name='deposite_total[]' id='deposite_total"+count+">' style='display: none;'>"+deposite_total[i]+"</span><input type='hidden' name='deposite_total[]' id='deposite_total' value='"+deposite_total[i]+"'></td>";
                                rows += "<td><span name='product_rent[]' id='product_rent"+count+"'>"+product_rent[i]+"</span></td>";
                                rows += "<td><span name='offered_rent[]' id='offered_rent1"+count+"'>"+offered_rent[i]+"</span><input type='hidden' name='offered_rent[]' id='offered_rent"+count+"' value='"+offered_rent[i]+"'></td>";
                                rows += "<td><span name='offered_rent_total[]' id='offered_rent1_total"+count+"'>"+total_rent_text+"</span><input type='hidden' name='offered_rent_total[]' id='offered_rent_total"+count+"' value='"+total_rent_text+"'></td>";
                                rows += "<td><span name='profit[]' id='profit"+count+"'>0(0%)</span><input type='hidden' name='profit_hidden[]' id='profit_hidden"+count+"' value='0'></td>";
                                rows += "<td><span name='transport[]' id='transport"+count+"'>"+transport_text+"</span><input type='hidden' name='transport[]' id='transport"+count+"' value='"+transport_text+"'></td></tr>";
                                sr_no = sr_no + 1;
                                count =count+1;
                            }
                        }
                        rows += "</tbody>";
                        $('.table tbody').empty();
                        $('.table tbody').remove();
                        $('#records tbody').append(rows);
                        $('.table').append(rows);
                        $('.table').DataTable().draw();
                        $('.selectpicker').selectpicker('refresh');
                        $("#btnExplode").hide();
                        $("#btnImplode").show();
                        $('#row_ct').val(count);
                        $('#vendor_approval_all').prop('checked', false);
                        select_vendor();
                        //\\*******Select Vendors on page load (Default Individual Selected)********//\\
                    }
                });
            }
            function implode1()
            {
                // alert("Explode Table");
                var customer_id = $("#customer_id").val();
                var lead_id = $("#lead_id").val();
                var dataString = ({_token:"{{ csrf_token() }}",customer_id:""+customer_id,lead_id:""+lead_id});
                $('#records tbody').empty();
                $.ajax({
                    type: "POST",
                    url: "<?php echo url('/'); ?>/assign_vendor_exp",
                    // cache:false,
                    data:dataString,
                    success: function (data)
                    {
                        var data_decoded = jQuery.parseJSON(data);
                        // console.log(data_decoded);
                        var products = jQuery.parseJSON(data_decoded.lead_details[0].equipment_requirement);
                        var products_id = jQuery.parseJSON(data_decoded.lead_details[0].equipment_id);
                        var del_date = jQuery.parseJSON(data_decoded.lead_details[0].del_date);
                        var equipment_quantity = jQuery.parseJSON(data_decoded.lead_details[0].equipment_qty);
                        var deposite = jQuery.parseJSON(data_decoded.lead_details[0].deposite);
                        var deposite_total = jQuery.parseJSON(data_decoded.lead_details[0].deposite_total);
                        var product_rent = jQuery.parseJSON(data_decoded.lead_details[0].equipments_rent);
                        var offered_rent = jQuery.parseJSON(data_decoded.lead_details[0].offered_rent);
                        var offered_rent_total = jQuery.parseJSON(data_decoded.lead_details[0].offered_rent_total);
                        var transport = jQuery.parseJSON(data_decoded.lead_details[0].transport);
                        var sale_rental = jQuery.parseJSON(data_decoded.lead_details[0].sale_rental);
                        var count =0;
                        var rows = "<tbody>";
                        for (var i=0; i <products.length; i++) 
                        {
                            var sale_rental_text = "";
                            if(sale_rental[i] == 'Sale')
                            {
                                sale_rental_text = 'table-info';
                            }
                            else if(sale_rental[i] == 'Rental')
                            {
                                sale_rental_text = 'table-success';
                            }
                            rows += "<tr class='rows "+sale_rental_text+"' data-count='"+count+"'>";
                            rows += "<td>"+(i+1)+"</td>";
                            rows += "<td><center><input type='checkbox' name='vendor_approval' id='vendor_approval"+count+"'></center></td>";
                            rows += "<td><span name='sale_rental[]' id='sale_rental"+count+"'>"+sale_rental[i]+"</span><input type='hidden' name='sale_rental_hidden[]' id='sale_rental_hidden"+count+"' value='"+sale_rental+"'></td>";                            
                            rows += "<td><span name='del_date[]' id='del_date"+count+"' value='"+del_date[i]+"'>"+del_date[i]+"</span></td>";
                            rows += "<td><span id='req_eq"+count+"' name='req_eq[]' value='"+products_id[i]+"'>"+products[i]+"</span><input type='hidden' name='req_eq_hidden[]' id='req_eq_hidden"+count+"' value='"+products_id[i]+"'></td>";
                            rows += "<td><span name='eq_quantity[]' id='eq_quantity"+count+"'>"+equipment_quantity[i]+"</span><input type='hidden' name='eq_quantity_hidden[]' id='eq_quantity_hidden"+count+"' value='"+equipment_quantity[i]+"'></td>";
                            rows += "<td id='select_vendor"+i+"'><select class='selectpicker' width='fit' title='Select Vendor' name='vendors[]' id='vendors"+count+"' data-live-search='true' width='100%' required='true'><option value='' disabled>No Vendors Found</option></select></td>";
                            rows += "<td id='select_warehouse"+i+"'><select class='selectpicker' width='fit' title='Select Warehouse' name='warehouses[]' id='warehouses"+count+"' data-live-search='true' width='100%' required='true'><option value='' disabled>Select Vendor First</option></select><input type='hidden' name='vendor_product_id[]' id='vendor_product_id"+count+"'><input type='hidden' name='vendor_product_details_id[]' id='vendor_product_details_id"+count+"'></td>";
                            rows += "<td id='select_brand"+i+"'><select class='selectpicker' width='fit' title='Select Brand' name='brands[]' id='brands"+count+"' data-live-search='true' width='100%' required='true'><option value='' disabled>Select Warehouse First</option></select></td>";
                            rows += "<td id='select_batch"+i+"'><select class='selectpicker' width='fit' title='Select Batch' name='batches[]' id='batches"+count+"' data-live-search='true' width='100%' required='true'><option value=' disabled>Select Brand First</option></select><input type='hidden' name='batches[]' id='batches"+count+"' value='0'><span><b> - </b></span></td>";
                            rows += "<td><span name='prod[]' id='prod"+count+"'>-</span><input type='hidden' name='product_price[]' id='product_price' value='0'></td>";
                            rows += "<td><input type='text' class='form-control' name='serial_numbers[]' id='serial_numbers"+count+"' placeholder='Additional Info...' value='0'></td>";
                            rows += "<td><span name='deposite[]' id='deposite"+count+"'>"+deposite[i]+"</span><input type='hidden' name='deposite[]' id='deposite' value='"+deposite[i]+"'><span name='deposite_total[]' id='deposite_total"+count+">' style='display: none;'>"+deposite_total[i]+"</span><input type='hidden' name='deposite_total[]' id='deposite_total' value='"+deposite_total[i]+"'></td>";
                            rows += "<td><span name='product_rent[]' id='product_rent"+count+"'>"+product_rent[i]+"</span></td>";
                            rows += "<td><span name='offered_rent[]' id='offered_rent1"+count+"'>"+offered_rent[i]+"</span><input type='hidden' name='offered_rent[]' id='offered_rent"+count+"' value='"+offered_rent[i]+"'></td>";
                            rows += "<td><span name='offered_rent_total[]' id='offered_rent1_total"+count+"'>"+offered_rent_total[i]+"</span><input type='hidden' name='offered_rent_total[]' id='offered_rent_total"+count+"' value='"+offered_rent_total[i]+"'></td>";
                            rows += "<td><span name='profit[]' id='profit"+count+"'>0(0%)</span><input type='hidden' name='profit_hidden[]' id='profit_hidden"+count+"' value='0'></td>";
                            rows += "<td><span name='transport[]' id='transport"+count+"'>"+transport[i]+"</span><input type='hidden' name='transport[]' id='transport"+count+"' value='"+transport[i]+"'></td></tr>";
                            count =count+1;
                        }
                        rows += "</tbody>";
                        $('.table tbody').empty();
                        $('.table tbody').remove();
                        $('#records tbody').append(rows);
                        $('.table').append(rows);
                        $('.table').DataTable().draw();
                        // $('#records').append(rows);
                        $('.selectpicker').selectpicker('refresh');
                        $("#btnExplode").show();
                        $("#btnImplode").hide();
                        $('#row_ct').val(count);
                        select_vendor();
                    }
                });
            }
            function implode()
            {
                // alert("Explode Table");
                var customer_id = $("#customer_id").val();
                var lead_id = $("#lead_id").val();
                var dataString = ({_token:"{{ csrf_token() }}",customer_id:""+customer_id,lead_id:""+lead_id});
                $.ajax({
                    type: "POST",
                    url: "<?php echo url('/'); ?>/assign_vendor_exp",
                    // cache:false,
                    data:dataString,
                    success: function (data)
                    {
                        var data_decoded = jQuery.parseJSON(data);
                        // console.log(data_decoded);
                        var products = jQuery.parseJSON(data_decoded.lead_details[0].equipment_requirement);
                        var products_id = jQuery.parseJSON(data_decoded.lead_details[0].equipment_id);
                        var del_date = jQuery.parseJSON(data_decoded.lead_details[0].del_date);
                        var equipment_quantity = jQuery.parseJSON(data_decoded.lead_details[0].equipment_qty);
                        var deposite = jQuery.parseJSON(data_decoded.lead_details[0].deposite);
                        var deposite_total = jQuery.parseJSON(data_decoded.lead_details[0].deposite_total);
                        var product_rent = jQuery.parseJSON(data_decoded.lead_details[0].equipments_rent);
                        var offered_rent = jQuery.parseJSON(data_decoded.lead_details[0].offered_rent);
                        var offered_rent_total = jQuery.parseJSON(data_decoded.lead_details[0].offered_rent_total);
                        var transport = jQuery.parseJSON(data_decoded.lead_details[0].transport);
                        var sale_rental = jQuery.parseJSON(data_decoded.lead_details[0].sale_rental);
                        var count =0;
                        var rows = "<tbody class='tbody'>";
                            var sr_no = 1;
                        for (var i=0; i <products.length; i++) 
                        {
                            var sale_rental_text = "";
                            var deposit_text = 0;
                            if(sale_rental[i] == 'Sale')
                            {
                                sale_rental_text = 'table-info';
                            }
                            else if(sale_rental[i] == 'Rental')
                            {
                                sale_rental_text = 'table-success';
                            }
                            rows += "<tr class='rows "+sale_rental_text+"' data-count='"+count+"'>";
                            rows += "<td>"+sr_no+"</td>";
                            rows += "<td><center><input type='checkbox' name='vendor_approval[]' id='vendor_approval"+count+"'></center><input type='hidden' name='vendor_approval_hidden[]' id='vendor_approval_hidden"+count+"' value='Pending'></td>";
                            rows += "<td><span name='sale_rental[]' id='sale_rental"+count+"'>"+sale_rental[i]+"</span><input type='hidden' name='sale_rental_hidden[]' id='sale_rental_hidden"+count+"' value='"+sale_rental[i]+"'></td>";                            
                            rows += "<td><span name='del_date[]' id='del_date"+count+"' value='"+del_date[i]+"'>"+del_date[i]+"</span></td>";
                            rows += "<td><span id='req_eq"+count+"' name='req_eq[]' value='"+products_id[i]+"'>"+products[i]+"</span><input type='hidden' name='req_eq_hidden[]' id='req_eq_hidden"+count+"' value='"+products_id[i]+"'></td>";
                            rows += "<td><span name='eq_quantity[]' id='eq_quantity"+count+"'>"+equipment_quantity[i]+"</span><input type='hidden' name='eq_quantity_hidden[]' id='eq_quantity_hidden"+count+"' value='"+equipment_quantity[i]+"'></td>";
                            rows += "<td id='select_vendor"+i+"'><select class='selectpicker' width='fit' title='Select Vendor' name='vendors[]' id='vendors"+count+"' data-live-search='true' width='100%' required='true'><option value='' disabled>No Vendors Found</option></select></td>";
                            rows += "<td id='select_warehouse"+i+"'><select class='selectpicker' width='fit' title='Select Warehouse' name='warehouses[]' id='warehouses"+count+"' data-live-search='true' width='100%' required='true'><option value='' disabled>Select Vendor First</option></select><input type='hidden' name='vendor_product_id[]' id='vendor_product_id"+count+"'><input type='hidden' name='vendor_product_details_id[]' id='vendor_product_details_id"+count+"'></td>";
                            rows += "<td id='select_brand"+i+"'><select class='selectpicker' width='fit' title='Select Brand' name='brands[]' id='brands"+count+"' data-live-search='true' width='100%' required='true'><option value='' disabled>Select Warehouse First</option></select></td>";
                            rows += "<td id='select_batch"+i+"'><select class='selectpicker' width='fit' title='Select Batch' name='batches[]' id='batches"+count+"' data-live-search='true' width='100%' required='true'><option value=' disabled>Select Brand First</option></select><input type='hidden' name='batches[]' id='batches"+count+"' value='0'><span><b> - </b></span></td>";
                            rows += "<td><span name='prod[]' id='prod"+count+"'>-</span><input type='hidden' name='product_price[]' id='product_price' value='0'></td>";
                            rows += "<td><input type='text' class='form-control' name='serial_numbers[]' id='serial_numbers"+count+"' placeholder='Additional Info...' value='0'></td>";
                            rows += "<td><span name='deposite[]' id='deposite"+count+"'>"+deposit_text+"</span><input type='hidden' name='deposite[]' id='deposite' value='"+deposit_text+"'><span name='deposite_total[]' id='deposite_total"+count+">' style='display: none;'>"+deposite_total[i]+"</span><input type='hidden' name='deposite_total[]' id='deposite_total' value='"+deposite_total[i]+"'></td>";
                            rows += "<td><span name='product_rent[]' id='product_rent"+count+"'>"+product_rent[i]+"</span></td>";
                            rows += "<td><span name='offered_rent[]' id='offered_rent1"+count+"'>"+offered_rent[i]+"</span><input type='hidden' name='offered_rent[]' id='offered_rent"+count+"' value='"+offered_rent[i]+"'></td>";
                            rows += "<td><span name='offered_rent_total[]' id='offered_rent1_total"+count+"'>"+total_rent_text+"</span><input type='hidden' name='offered_rent_total[]' id='offered_rent_total"+count+"' value='"+total_rent_text+"'></td>";
                            rows += "<td><span name='profit[]' id='profit"+count+"'>0(0%)</span><input type='hidden' name='profit_hidden[]' id='profit_hidden"+count+"' value='0'></td>";
                            rows += "<td><span name='transport[]' id='transport"+count+"'>"+transport_text+"</span><input type='hidden' name='transport[]' id='transport"+count+"' value='"+transport_text+"'></td></tr>";
                            sr_no = sr_no + 1;
                            count =count+1;
                        }
                        rows += "</tbody>";
                        $('.table tbody').empty();
                        $('.table tbody').remove();
                        $('#records tbody').append(rows);
                        $('.table').append(rows);
                        $('.table').DataTable().draw();
                        $('.selectpicker').selectpicker('refresh');
                        $("#btnImplode").hide();
                        $("#btnExplode").show();
                        $('#row_ct').val(count);
                        $('#vendor_approval_all').prop('checked', false);
                        select_vendor();
                        //\\*******Select Vendors on page load (Default Individual Selected)********//\\
                    }
                });
            }
            function select_vendor()
            {
                var count_eq = $('#row_ct').val();
                //alert(count_eq);
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
                                // cache:false,
                                success: function (data)
                                {
                                    var vendors = jQuery.parseJSON(data);
                                    // console.log(vendors);
                                    var vendorsLength = vendors.length;
                                    // var temp_var = i-2;
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
                                // cache:false,
                                success: function (data)
                                {
                                    var vendors = jQuery.parseJSON(data);
                                    // console.log(vendors);
                                    var vendorsLength = vendors.length;
                                    // var temp_var = i-2;
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
            }
            // var countTable = 0;

            $('.table-responsive').on('show.bs.dropdown', function () {
            $('.table-responsive').css( "overflow", "inherit" );
            });

            $('.table-responsive').on('hide.bs.dropdown', function () {
                $('.table-responsive').css( "overflow", "auto" );
            });
        </script>                                                         

    @endsection
    
</html>
