<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Inquiry : Convert Lead</title>
        @section('styles')
       
        <style>
            .row_scroll {
                overflow-x: scroll;
                overflow-y: hidden;
                white-space:nowrap;
            }
        </style>
        @endsection
    </head>

<body id="page-top">	
		<!-- Page Wrapper -->
        @extends('header_and_sidebar')
            <div class="container">
                @section('content')
                <div class="row">
                    <div class="col-md-1">
                    </div>
                    <div class="col-md-10">
                        <div class="card">
                            <div class="card-header text-center" style="background-color: #337ab7; color: white;">
                                <span><b>Convert Lead</b></span>
                            </div>
                            <div class="card-body"><!--style="min-height: 0; max-height: 5; overflow-y: scroll;"-->
                                <form class="form doublePost" method="POST" action="<?php echo url('/');?>/update_lead" >
                                {{ csrf_field() }}
                                    <input type="hidden" name="customer_id" value="{{$lead_details[0]['cust_id']}}">
                                    <input type="hidden" name="lead_id" value="{{$lead_details[0]['id']}}">
                                    {{-- <div class="row form-group">
                                        <div class="col-md-6 text-right">
                                            <label for="cust_name">Sales / Rental</label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="radio" name="sale_rental" id="sale" value="Sale"><label for="sale">Sale</label>&emsp;
                                            <input type="radio" name="sale_rental" id="rental" checked value="Rental"><label for="rental">Rental</label>&emsp;
                                        </div>
                                    </div> --}}
                                    <div class="row form-group">
                                        <div class="col-md-4">                                            
                                            <label for="cust_name">Converted Date</label>
                                        </div>
                                        <div class="col-md-3">
                                            {{-- <input type="Date" class="form-control" name="converted_date" id="converted_date" value="{{date('Y-m-d',strtotime($lead_details[0]['converted_at']))}}"> --}}
                                            <input type="Date" class="form-control" name="converted_date" id="converted_date" value="@if(!isset($lead_details[0]['converted_at'])){{date('Y-m-d',strtotime($lead_details[0]['creation_date']))}}@else{{date('Y-m-d',strtotime($lead_details[0]['converted_at']))}}@endif">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">                                            
                                            <label for="cust_name">Customer Name*</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" value="{{$lead_details[0]['customer_name']}}" id="cust_name" name="cust_name" placeholder="Customer Name*" required="true">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">                                            
                                            <label for="patient_name">Patient Name</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" value="{{$lead_details[0]['patient_name']}}" name="patient_name" placeholder="Patient Name">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="patient_age">Patient Age</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="number" class="form-control" value="{{$lead_details[0]['patient_age']}}" name="patient_age" placeholder="Patient Age">
                                        </div>
                                    </div>                                    
                                    {{-- <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="location">Location*</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="location" <?php //echo"value='".$lead_details[0]['location']."'";?> name="location" placeholder="Location*" required="true">
                                        </div>
                                    </div> --}}
                                    {{--Convrted at dete--}} 
                                    <input type="hidden" name="converted_at" value="{{$lead_details[0]['converted_at']}}">
                                    {{--Convrted at dete--}} 
                                    
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="location">Location*</label>
                                        </div>
                                        <div class="col-md-8">
                                            <!-- <input type="text" class="form-control" id="location" <?php //echo "style='border-radius: 5px; border:#3292e4 1px solid;'"; echo"value='".$lead_details[0]['location']."'";?> name="location" placeholder="Location" required="true"> -->
                                            {{-- <select class="selectpicker form-control" id="location" title="Select Location" name="location" data-live-search="true" required>
                                                <?php 
                                                    // foreach ($cities as $city) 
                                                    // {
                                                ?>
                                                    <option value="<?php// echo $city['name']?>" <?php //if($city['name']==$lead_details[0]['location']){echo "selected";}?>><?php //echo $city['name']?></option>
                                                <?php
                                                    //}
                                                ?>    
                                                
                                            </select> --}}
                                            <input type="text" class="form-control" placeholder="Select Location" list="city" id="location" name="location" value="@if(isset($lead_details[0]['location'])){{$lead_details[0]['location']}}@endif" required>
                                            <datalist id="city">
                                                @foreach ($cities as $city) 
                                                    <option value="{{$city['name']}}">{{$city['name']}}</option>
                                                @endforeach
                                            </datalist>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="customer_type">Customer Type: </label>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="btn-group btn-group-toggle btn-block btn-sm" data-toggle="buttons">
                                                <label class="btn btn-outline-success btn-sm @if($lead_details[0]['customer_type']=='Individual'){{"active"}}@endif">
                                                    <input type="radio" name="customer_type" id="radio_individual" @if($lead_details[0]['customer_type']=='Individual'){{"checked"}}@endif value="Individual"> Individual
                                                </label>
                                                <label class="btn btn-outline-success btn-sm @if($lead_details[0]['customer_type']=='Corporate'){{"active"}}@endif">
                                                    <input type="radio" name="customer_type" id="radio_corporate" @if($lead_details[0]['customer_type']=='Corporate'){{"checked"}}@endif value="Corporate"> Corporate
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="priority">Priority</label>
                                        </div>
                                        <div class="col-md-8 radio-group">
                                            <input type="radio" name="priority_ratio" id="high" @if($lead_details[0]['priority']==0) {{"checked"}} @endif value="0"><label for="high">High</label>&emsp;
                                            <input type="radio" name="priority_ratio" id="normal" value="1"><label for="normal" @if($lead_details[0]['priority']==1) {{"checked"}} @endif >Normal</label>&emsp;
                                            <input type="radio" name="priority_ratio" id="tomorrow" value="2" @if($lead_details[0]['priority']==2) {{"checked"}} @endif ><label for="low">Low</label>&emsp;
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="cust_type">Customer GST Details</label>
                                        </div>
                                        <div class="col-md-8 radio-group">
                                            <input type="radio" name="cust_type" id="registered" value="registered" @if(isset($lead_details[0]['gst_no'])) {{"checked"}} @endif><label for="registered"> Registered</label>&emsp;
                                            <input type="radio" name="cust_type" id="not_registered" value="not_registered" @if(isset($lead_details[0]['gst_no'])) {{""}} @else {{"checked"}} @endif><label for="not_registered"> Not Registered</label>&emsp;
                                        </div>
                                    </div>
                                    <div class="row form-group" id="gst_no_row" style=" @if(isset($lead_details[0]['gst_no'])){{"display:block"}} @else {{"display:none"}} @endif">
                                        <div class="col-md-4">
                                            <label for="cust_type">GST Number</label>
                                        </div>
                                        <div class="col-md-8 radio-group">
                                            <input type="text" class="form-control text-uppercase" maxlength="15" name="gst_no" id="gst_no" value="@if(isset($lead_details[0]['gst_no'])) {{$lead_details[0]['gst_no']}} @endif">
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="location">Delivery Dates</label>
                                        </div>
                                        <div class="col-md-8 radio-group">
                                            <input type="radio" name="del_date_radio" id="individual"checked><label for="individual">Individual</label>&emsp;
                                            <input type="radio" name="del_date_radio" id="today"><label for="today">Today</label>&emsp;
                                            <input type="radio" name="del_date_radio" id="tomorrow"><label for="tomorrow">Tomorrow</label>&emsp;
                                            <input type="radio" name="del_date_radio" id="day_after_tomorrow"><label for="day_after_tomorrow">Day after Tomorrow</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <table class="table table-bordered table-responsive" id="equipment_table">
                                            <thead class="row_scroll">
                                                <th>Sr. No.</th>
                                                <th>Sales / Rental&emsp;&emsp;</th>
                                                <th width="-10%">Delivery Date</th>
                                                <th>Equipment Name&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</th>
                                                <th>Qty&emsp;</th>
                                                <th>Deposit(Rental Only)</th>
                                                <th><input type="checkbox" name="same_as_deposite" id="same_as_deposite">Offered Deposit<br>(Rental Only)</th>
                                                {{-- <th>Total Offered Deposit<br>(Rental Only)</th> --}}
                                                <th>Rent/Sale Price</th>
                                                <th><input type="checkbox" name="same_as_rent" id="same_as_rent">Offered Rent/Sale<br>Price</th>
                                                <th>Total Offered<br>Rent/Sale Price</th>
                                                <th>Transport</th>
                                                <th>Discount</th>
                                                <th>Action</th>
                                            </thead>
                                            <tbody class="table-body">
                                                <?php
                                                    $count = 0;
                                                    $i=0;
                                                    foreach ($products as $product) 
                                                    {   
                                                        $product_name = $product['id'];
                                                        $products = json_decode($lead_details[0]['equipment_requirement']);
                                                        if(isset($lead_details[0]['deposite']))
                                                        {
                                                            $deposites = json_decode($lead_details[0]['deposite']);
                                                        }
                                                        if(isset($lead_details[0]['offered_rent']))
                                                        {
                                                            $offered_rents = json_decode($lead_details[0]['offered_rent']);
                                                        }
                                                        if(isset($lead_details[0]['deposite_total']))
                                                        {
                                                            $deposites_total = json_decode($lead_details[0]['deposite_total']);
                                                        }
                                                        if(isset($lead_details[0]['offered_rent_total']))
                                                        {
                                                            $offered_rents_total = json_decode($lead_details[0]['offered_rent_total']);
                                                        }
                                                        if(isset($lead_details[0]['transport']))
                                                        {
                                                            $transports = json_decode($lead_details[0]['transport']);
                                                        }
                                                        if(isset($lead_details[0]['equipment_qty']))
                                                        {
                                                            $qtys = json_decode($lead_details[0]['equipment_qty']);
                                                        }
                                                        if(isset($lead_details[0]['del_date']))
                                                        {
                                                            $del_dates = json_decode($lead_details[0]['del_date']);
                                                        }
                                                        if(isset($lead_details[0]['sale_rental']))
                                                        {
                                                            $sale_rental = json_decode($lead_details[0]['sale_rental']);
                                                        }
                                                        $products_id = json_decode($lead_details[0]['equipment_id']);
                                                        foreach ($products_id as $prod)
                                                        {

                                                            if($product_name == $prod)
                                                            {
                                                            // if(in_array($product_name,$products_id))
                                                            // {
                                                                $count = $count + 1;
                                                                //echo $deposites[$i];
                                                        ?>
                                                                <tr id="{{$count}}">
                                                                    <td>{{$count}}</td>
                                                                    <td>
                                                                        <input type="radio" name="sale_rental[]{{$count}}" id="sale{{$count}}" value="Sale"  @if($sale_rental[$i]=="Sale"){{"Checked"}} @endif><label for="sale{{$count}}">Sale</label>&emsp;
                                                                        <input type="radio" name="sale_rental[]{{$count}}" id="rental{{$count}}" value="Rental" @if($sale_rental[$i]=="Rental"){{"Checked"}} @endif><label for="rental{{$count}}" >Rental</label>
                                                                    </td>
                                                                    <td width="-10%"><input type="date" class="form-control form-control-sm date" width="50px" name="DelDate[]" id="DelDate{{$count}}" value="<?php if (isset($del_dates)){echo $del_dates[$i];}else{echo date('Y-m-d');}?>"></td>
                                                                    <td>{{$product['product_name']}}<input type="hidden" id="equipments{{$count}}" name="equipments[]" value="{{$product['id']}}"></td>
                                                                    <td><input type="text" name="qty[]" id="qty{{$count}}" class="form-control form-control-sm" value="@if(isset($qtys)){{$qtys[$i]}}@else{{"1"}}@endif"></td>
                                                                    <td>
                                                                        <span id="deposite{{$count}}"><?php echo $product['product_deposite'];?></span><input type="hidden" name="prod_deposite[]" id="prod_deposite{{$count}}" value="{{$product['product_deposite']}}"></td><input type="hidden" name="min_depo[]" id="min_depo{{$count}}" value="{{$product['product_deposite']-($product['product_deposite']*$product['min_rent_percentage'])/100}}">
                                                                    </td>
                                                                    <td>
                                                                        <div class="row">
                                                                            <div class="col-md-2">
                                                                                <input type="checkbox" name="same_as_deposite[]" id="same_as_deposite{{$count}}" @if(isset($deposites[$i]))@if($deposites[$i] == $product['product_deposite']){{"checked"}}@endif @endif>
                                                                            </div>
                                                                            <div class="col-md-10">
                                                                                <input type="text" name="offered_deposite[]" id="offered_deposite{{$count}}" class="form-control form-control-sm" value="@if(isset($deposites)){{$deposites[$i]}}@else{{""}}@endif" required>
                                                                            </div>
                                                                        </div>
                                                                        <span id="offered_deposite_total_span{{$count}}" style="display: none;">0</span>
                                                                        <input type="hidden" name="offered_deposite_total[]" id="offered_deposite_total{{$count}}" class="form-control form-control-sm" value="@if(isset($deposites_total)){{$deposites_total[$i]}}@else{{"0"}}@endif">
                                                                    </td>
                                                                    <td><span id="rent_td{{$count}}">@if($sale_rental[$i]=='Rental'){{$product['product_rent']}}@elseif($sale_rental[$i]=='Sale'){{$product['product_sale_rate']}}@endif</span><input type="hidden" name="rent[]" id="rent{{$count}}" value="@if($sale_rental[$i]=='Rental'){{$product['product_rent']}}@elseif($sale_rental[$i]=='Sale'){{$product['product_sale_rate']}}@endif"></td>
                                                                    {{-- <td>{{$product['product_rent']-($product['product_rent']*$product['min_rent_percentage'])/100}}</td> --}}
                                                                    <td><div class="row"><div class="col-md-3"><input type="checkbox" name="same_as_rent[]" id="same_as_rent{{$count}}" @if(isset($offered_rents[$i])) @if($offered_rents[$i] == $product['product_rent']) {{"checked"}} @endif @endif></div><div class="col-md-9"><input type="text" class="form-control form-control-sm" id="offered_rent{{$count}}" name="offered_rent[]" value="@if(isset($offered_rents)){{$offered_rents[$i]}}@else{{""}}@endif" required></div></div><input type="hidden" name="min_rent[]" id="min_rent{{$count}}" value="{{$product['product_rent']-($product['product_rent']*$product['min_rent_percentage'])/100}}"></td>
                                                                    <td>
                                                                        <span id="offered_rent_total_span{{$count}}">0</span>
                                                                        <input type="hidden" class="form-control form-control-sm" id="offered_rent_total{{$count}}" name="offered_rent_total[]" value="@if(isset($offered_rent_total)){{$offered_rent_total[$i]}}@else{{"0"}}@endif">
                                                                    </td>
                                                                    <td><input type="text" class="form-control form-control-sm" id="transport{{$count}}" name="transport[]" value="@if(isset($transports)){{$transports[$i]}}@else{{""}}@endif" required></td>
                                                                    <td><span id="discount{{$count}}">0(0%)</span><input type="hidden" name="discount_hidden[]" id="discount_hidden{{$count}}"></td>
                                                                    <td><button type="button" class="btn btn-danger" id="{{$count}}" name="remove" onclick="remove(this.id);"><i class="fas fa-trash-alt"></i></button></td>
                                                                </tr>
                                                                <input type="hidden" name="count" id="count" value="{{$count}}">
                                                        <?php
                                                                $i++;
                                                            }
                                                        }
                                                    }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 text-right">
                                            <div class="row">
                                                <div class="col-md-11">
                                                    <span>Total Rent : </span>
                                                </div>
                                                <div class="col-md-1">
                                                    <span id="total_rent">0</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-11">
                                                    <span>Total Sale : </span>
                                                </div>
                                                <div class="col-md-1">
                                                    <span id="total_sale">0</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-11">
                                                    <span>Total Deposite : </span>
                                                </div>
                                                <div class="col-md-1">
                                                    <span id="total_deposite">0</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-11">
                                                    <span>Total Transport : </span>
                                                </div>
                                                <div class="col-md-1">
                                                    <span id="total_transport">0</span>                                                    
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-11">
                                                    <span>Total Discount : </span>
                                                </div>
                                                <div class="col-md-1">
                                                    <span id="total_discount">0</span>                                                    
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-11">
                                                    <span>Grand Total : </span>
                                                </div>
                                                <div class="col-md-1">                                                    
                                                    <span id="grand_total">0</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <center>
                                                <button type="button" class="btn btn-primary" name="add_new_product" onclick="add_product();" value="add_new_product">Add Equipment</button>
                                            </center>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="payment_mode">Payment Mode*</label>
                                        </div>
                                        <div class="col-md-8 radio-group">
                                            <input type="radio" name="payment_mode" value="Cash" id="cash" required @if($lead_details[0]['payment_mode']=="Cash") {{"checked"}} @else {{""}} @endif><label for="cash" >Cash</label>&emsp;
                                            <input type="radio" name="payment_mode" value="Online" id="online" @if($lead_details[0]['payment_mode']=="Online") {{"checked"}} @else {{""}} @endif><label for="online" >Online</label>&emsp;
                                        </div>
                                    </div>
                                    {{-- <div class="row form-group">
                                        <div class="col-md-4">                                            
                                            <label for="eqipments">Equipment Required*</label>
                                        </div>
                                        <div class="col-md-8">
                                            <select class="selectpicker form-control" title="Select Products From Dropdown" name="eqipments[]" multiple data-live-search="true" required>
                                                <?php 
                                                    /*foreach ($products as $product) 
                                                    {
                                                        $product_name = $product['id'];
                                                        $products = json_decode($lead_details[0]['equipment_requirement']);
                                                        $products_id = json_decode($lead_details[0]['equipment_id']);
                                                ?>
                                                    <option value="<?php echo $product['id']?>"<?php if(in_array($product_name,$products_id)){echo "selected";} ?>><?php echo $product['product_name']?></option>
                                                <?php
                                                    }*/
                                                ?>    
                                            </select>
                                        </div>
                                    </div> --}}
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="primary_contact_no">Mobile Number(Primary)*</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="primary_contact_no" <?php echo"value='".$lead_details[0]['primary_contact_no']."'";?> name="primary_contact_no" placeholder="Mobile Number (Primary)*" oninput="numberOnly(this.id);" maxlength="10" required="true">
                                        </div>
                                    </div> 
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="refered_by">Refered by*</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" <?php echo"value='".$lead_details[0]['refered_by']."'";?> name="refered_by" placeholder="Refered By*">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">                                        
                                            <label for="lead_source">Lead Source*</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="lead_source" <?php echo"value='".$lead_details[0]['lead_source']."'";?> name="lead_source" placeholder="Lead Source (Google, JustDial, Marketing)*" required="true">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">                                        
                                            <label for="lead_value">Lead Value*</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="lead_value" <?php echo"value='".$lead_details[0]['lead_value']."'";?> name="lead_value" required="true">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="lead_owner">Lead Owner*</label>
                                        </div>
                                        <div class="col-md-8">
                                            <select class="selectpicker form-control" id="lead_owner" title="Select Owner" name="lead_owner" data-live-search="true" required>
                                                <?php 
                                                    foreach ($users as $user) 
                                                    {
                                                ?>
                                                    <option value="<?php echo $user['id']?>" <?php if($user['username']==$lead_details[0]['username']){echo "selected";}?> ><?php echo $user['username']?></option>
                                                <?php
                                                    }
                                                ?>    
                                            </select>
                                        </div>
                                    </div>
                                    <hr>
                                    <center><b>Additional Information</b></center>
                                    <hr>
                                    <div class="row justify-content-center">
                                        {{-- Delivery add --}}
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header text-center">
                                                    <h4>Delivery Address<h4>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row form-group">
                                                        <div class="col-md-4">
                                                            <label for="address_line_1">Line 1</label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control" <?php echo"value='".$lead_details[0]['address_line_1']."'";?> name="address_line_1" id="address_line_1" placeholder="Line 1*"required="true">
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <div class="col-md-4">
                                                            <label for="address_line_2">Line 2</label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control" <?php echo"value='".$lead_details[0]['address_line_2']."'";?> name="address_line_2" id="address_line_2" placeholder="Line 2*">
                                                        </div>
                                                    </div>                                    
                                                    <div class="row form-group">
                                                        <div class="col-md-4">
                                                            <label for="landmark">Landmark</label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control" <?php echo"value='".$lead_details[0]['landmark']."'";?> name="landmark" id="landmark" placeholder="Landmark*"required="true">
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <div class="col-md-4">
                                                            <label for="area">Area</label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control" <?php echo"value='".$lead_details[0]['area']."'";?> name="area" id="area" placeholder="Area*">
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <div class="col-md-4">
                                                            <label for="city1">City</label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control"<?php echo"value='".$lead_details[0]['city']."'";?> name="city1" id="city1" placeholder="City*"required="true">
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <div class="col-md-4">
                                                            <label for="pincode">Pin Code</label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control" <?php echo"value='".$lead_details[0]['pincode']."'";?> name="pincode" id="pincode" placeholder="Pincode*" maxlength="6" required>
                                                        </div>
                                                    </div>
                                                   
                                                    <div class="row form-group">
                                                      
                                                        <div class="col-md-4">
                                                            <label for="state">State</label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <select class="selectpicker form-control" title="State*" name="state" id="state" data-live-search="true">
                                                                <?php 
                                                                    foreach ($states as $state) 
                                                                    {
                                                                ?>
                                                                    <option value="<?php echo $state['name']?>" <?php if($state['name'] == $lead_details[0]['state']){echo "selected";} ?>><?php echo $state['name']?></option>
                                                                <?php
                                                                    }
                                                                ?>    
                                                            </select>
                                                        </div>
                                                    </div>
                                                       
                                                    <div class="row form-group">
                                                        <div class="col-md-4">                                
                                                            <label for="country">Country</label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <select class="selectpicker form-control" title="Country*" name="country" id="country" data-live-search="true">
                                                                <?php 
                                                                    foreach ($countries as $country) 
                                                                    {
                                                                ?>
                                                                    <option value="<?php echo $country['name']?>" <?php if($country['name'] == $lead_details[0]['country']){echo "selected";} ?>><?php echo $country['name']?></option>
                                                                <?php
                                                                    }
                                                                ?>    
                                                            </select>
                                                        </div>                                    
                                                    </div>
                                                    
                
                                                    <div class="row form-group">
                                                        <div class="col-md-4">
                                                            <label for="email">Email Id</label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="email" class="form-control" <?php echo"value='".$lead_details[0]['email_id']."'";?> name="email" id="email" placeholder="Email*"required="true" autocomplete="nope"> 
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <div class="col-md-4">
                                                            <label for="eqipments">Mobile Number</label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control" <?php echo"value='".$lead_details[0]['secondary_contact_no']."'";?> name="secondary_contact_no" id="secondary_contact_no" max="9999999999" placeholder="Mobile Number (Secondary)*"  oninput="numberOnly(this.id);" maxlength="10">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- permanant add --}}
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <input type="checkbox" class="checkbox-lg"  id="check_del_address" name="check_del_address" @if($lead_details[0]['addr_is_same']=='Yes'){{'checked'}}@endif > <label for="check_del_address">is same?</label>
                                                    &emsp;
                                                    &emsp;
                                                    <label class="text-center h4">Permanant Address</label>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row form-group">
                                                        <div class="col-md-4">
                                                            <label for="prmt_address_line_1">Line 1</label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control" name="prmt_address_line_1" id="prmt_address_line_1" placeholder="Line 1*" value="{{$lead_details[0]['prmt_address_line_1']}}">
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <div class="col-md-4">
                                                            <label for="prmt_address_line_2">Line 2</label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control"  name="prmt_address_line_2" id="prmt_address_line_2" placeholder="Line 2*" value="{{$lead_details[0]['prmt_address_line_2']}}">
                                                        </div>
                                                    </div>                                    
                                                    <div class="row form-group">
                                                        <div class="col-md-4">
                                                            <label for="prmt_landmark">Landmark</label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control"  name="prmt_landmark" id="prmt_landmark" placeholder="Landmark*" value="{{$lead_details[0]['prmt_landmark']}}">
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <div class="col-md-4">
                                                            <label for="prmt_area">Area</label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control"  name="prmt_area" id="prmt_area" placeholder="Area*" value="{{$lead_details[0]['prmt_area']}}">
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <div class="col-md-4">
                                                            <label for="prmt_city">City</label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control" name="prmt_city" id="prmt_city" placeholder="City*" value="{{$lead_details[0]['prmt_city']}}">
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <div class="col-md-4">
                                                            <label for="prmt_pincode">Pin Code</label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control"  name="prmt_pincode" id="prmt_pincode" placeholder="Pincode*" maxlength="6" value="{{$lead_details[0]['prmt_pincode']}}">
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row form-group">
                                                       
                                                        <div class="col-md-4">
                                                            <label for="prmt_state">State</label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <select class="selectpicker form-control" title="State*" name="prmt_state" id="prmt_state" data-live-search="true">
                                                                @foreach ($states as $state)
                                                                    <option value="{{$state['name']}}"<?php if($state['name'] == $lead_details[0]['prmt_state']){echo "selected";} ?>>{{$state['name']}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                            
                                                    <div class="row form-group">
                                                        <div class="col-md-4">                                
                                                            <label for="prmt_country">Country</label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <select class="selectpicker form-control" title="Country*" name="prmt_country" id="prmt_country" data-live-search="true">
                                                                <?php 
                                                                    foreach ($countries as $country) 
                                                                    {
                                                                ?>
                                                                    <option value="<?php echo $country['name']?>"<?php if($country['name'] == $lead_details[0]['prmt_country']){echo "selected";} ?>><?php echo $country['name']?></option>
                                                                <?php
                                                                    }
                                                                ?>    
                                                            </select>
                                                        </div>
                                                    </div>
                
                                                    <div class="row form-group">
                                                        <div class="col-md-4">
                                                            <label for="prmt_email">Email Id</label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="email" class="form-control" name="prmt_email" id="prmt_email" placeholder="Email*" value="{{$lead_details[0]['prmt_email_id']}}" autocomplete="nope">
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <div class="col-md-4">
                                                            <label for="prmt_eqipments">Mobile Number</label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control" name="prmt_secondary_contact_no" id="prmt_secondary_contact_no" max="9999999999" placeholder="Mobile Number (Secondary)*" oninput="numberOnly(this.id);" maxlength="10" value="{{$lead_details[0]['prmt_secondary_contact_no']}}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row form-group">
                                        <div class="col-md-12">
                                            <center><input type="checkbox" name="same_address" id="same_address" required checked> <label for="same_address" style="color:tomato">is address verified?</label></center>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="doctor_name">Doctor Name</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name="doctor_name" placeholder="Doctor Name" value="{{$lead_details[0]['doctor_name']}}">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="hospital_name">Hospital Name</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control"  name="hospital_name" placeholder="Hospital Name" value="{{$lead_details[0]['hospital_name']}}">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="therapeutic_requirement">Therapeutic Requirement</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name="therapeutic_requirement" placeholder="Therapeutic Requirement" value="{{$lead_details[0]['therapeutic_requirement']}}">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="comment">Comment</label>
                                        </div>
                                        <div class="col-md-8">
                                            <textarea class="form-control" name="comment" id="comment" value="{{$lead_details[0]['comment']}}"></textarea>
                                        </div>
                                    </div>
                                    <center>
                                        <button class="btn btn-primary" type="submit" name="submit" value="convert">Convert</button>
                                        <button class="btn btn-default" type="reset" name="submit">Clear</button>
                                    </center>
                                    <br>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endsection
            </div>
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>   
    @section('script')
    <script>
        $('#registered').on('click',function()
        {
           $('#gst_no_row').show(); 
        });
        $('#not_registered').on('click',function()
        {
           $('#gst_no_row').hide(); 
        });
        $('#same_address').on('change',function()
        {
            if(this.checked)
            {
                if(confirm("Are you are sure the address is verified with customer?"))
                {
                }
                else
                {
                    $('#same_address').removeAttr('checked');
                }
            }
        });
        $('#check_del_address').on('change',function()
        {
            if(this.checked)
            {
                var line1 = $('#address_line_1').val();
                var line2 = $('#address_line_2').val();
                var landmark = $('#landmark').val();
                var area = $('#area').val();
                var city = $('#city1').val();
                var pincode = $('#pincode').val();
                var state = $('#state').val();
                var country = $('#country').val();
                var email = $('#email').val();
                var mobile_no = $('#secondary_contact_no').val();

                $('#prmt_address_line_1').val(line1);
                $('#prmt_address_line_2').val(line2);
                $('#prmt_landmark').val(landmark);
                $('#prmt_area').val(area);
                $('#prmt_city').val(city);
                $('#prmt_pincode').val(pincode);
                $('#prmt_state').selectpicker('val',state);
                $('#prmt_country').selectpicker('val',country);
                $('#prmt_email').val(email);
                $('#prmt_secondary_contact_no').val(mobile_no);
            }
            else
            {
                $('#prmt_address_line_1').val(null);
                $('#prmt_address_line_2').val(null);
                $('#prmt_landmark').val(null);
                $('#prmt_area').val(null);
                $('#prmt_city').val(null);
                $('#prmt_pincode').val(null);
                $('#prmt_state').selectpicker('val',null);
                $('#prmt_country').selectpicker('val',null);
                $('#prmt_email').val(null);
                $('#prmt_secondary_contact_no').val(null);
            }
        });
        $('#today').on('click',function()
        {
            var count = parseInt($('#equipment_table tr:last').attr('id'));
            for(var i=1; i<=count; i++)
            {
                //alert(i);
                var date = '{{date('Y-m-d')}}';
                $('#DelDate'+i).val(date);
            }
        });
        $('#tomorrow').on('click',function()
        {
            var count = parseInt($('#equipment_table tr:last').attr('id'));
            for(var i=1; i<=count; i++)
            {
                //alert(i);
                var date = '{{date('Y-m-d',strtotime("+1 days"))}}';
                $('#DelDate'+i).val(date);
            }
        });
        $('#day_after_tomorrow').on('click',function()
        {
            var count = parseInt($('#equipment_table tr:last').attr('id'));
            for(var i=1; i<=count; i++)
            {
                //alert(i);
                var date = '{{date('Y-m-d',strtotime("+2 days"))}}';
                $('#DelDate'+i).val(date);
            }
        });
        function remove(id)
        {
            $('table#equipment_table tr#'+id).remove();
        }
        function add_product()
        {
            var count = parseInt($('#equipment_table tr:last').attr('id'));
            if(isNaN(count))
            {
                count = 1;
            }
            var count = count + 1;
            var row = "<tr id='"+count+"'>";
                row += "<td>"+count+"</td>";
                row += '<td><input type="radio" name="sale_rental[]'+count+'" id="sale'+count+'" value="Sale"><label for="sale'+count+'">Sale</label>&emsp;<input type="radio" name="sale_rental[]'+count+'" id="rental'+count+'" checked value="Rental"><label for="rental'+count+'">Rental</label></td>';
                row += '<td><input type="date" class="form-control form-control-sm date" name="DelDate[]" id="DelDate'+count+'" value="{{date('Y-m-d')}}"></td>';
                row += '<td><select class="selectpicker form-control" data-width="fit" id="equipments'+count+'" title="Select Products From Dropdown" name="equipments[]" data-live-search="true" required><?php foreach($products_details as $product){ echo "<option value=".$product['id'].">".$product['product_name']."</option>";}?></select></td>';
                row += '<td><input type="text" name="qty[]" id="qty'+count+'" class="form-control form-control-sm" value="1"></td>';
                row += '<td><span id="deposite'+count+'"></span><input type="hidden" name="prod_deposite[]" id="prod_deposite'+count+'"><input type="hidden" name="min_depo[]" id="min_depo'+count+'"></td>';
                row += "<td><div class='row'><div class='col-md-2'><input type='checkbox' name='same_as_deposite[]' id='same_as_deposite"+count+"'></div><div class='col-md-10'><input type='text' name='offered_deposite[]' class='form-control' id='offered_deposite"+count+"' required></div></div><span id='offered_deposite_total_span"+count+"' style='display:none;'>0</span><input type='hidden' name='offered_deposite_total[]' class='form-control' id='offered_deposite_total"+count+"' value='0'></td>";
                row += "<td><span id='rent_td"+count+"'></span><input type='hidden' name='rent[]' id='rent"+count+"'></td>";
                // row += "<td id='min_rent"+count+"'></td>";
                row += "<td><div class='row'><div class='col-md-3'><input type='checkbox' name='same_as_rent[]' id='same_as_rent"+count+"'></div><div class='col-md-9'><input type='text' name='offered_rent[]' class='form-control' id='offered_rent"+count+"' required><input type='hidden' name='min_rent[]' id='min_rent"+count+"'></div></div></td>";
                row += "<td><span id='offered_rent_total_span"+count+"'>0</span><input type='hidden' name='offered_rent_total[]' class='form-control' id='offered_rent_total"+count+"' value='0'></td>";
                row += "<td><input type='text' name='transport[]' class='form-control' id='transport"+count+"' value='0' required></td>";
                row += '<td><span id="discount'+count+'">0(0%)</span><input type="hidden" name="discount_hidden[]" id="discount_hidden'+count+'"></td>';
                row += '<td><button type="button" class="btn btn-danger" id="'+count+'" name="remove" onclick="remove(this.id);"><i class="fas fa-trash-alt"></i></button></td>';
                row += "</tr>";
            // alert(row);
            $("#equipment_table tbody").append(row);
            //$("#equipments").selectpicker().render();
            $("#equipments"+count).selectpicker();
        }
        $('#same_as_deposite').on('change',function()
        {
            var count = parseInt($('#equipment_table tr:last').attr('id'));
            if(this.checked)
            {
                for(var i=1; i<=count; i++)
                {                    
                    $('#offered_deposite'+i).removeAttr('style');
                    var value = $('#prod_deposite'+i).val();
                    $('#offered_deposite'+i).val(value);
                    $('#same_as_deposite'+i).attr('checked','checked');
                }
            }
            else
            {
                for(var i=1; i<=count; i++)
                {
                    // var value = $('#rent'+i).val();
                    $('#offered_deposite'+i).val(0);
                    $('#same_as_deposite'+i).removeAttr('checked');
                }
            }
        });

        $('#same_as_rent').on('change',function()
        {
            var count = parseInt($('#equipment_table tr:last').attr('id'));
            if(this.checked)
            {
                for(var i=1; i<=count; i++)
                {
                    $('#offered_rent'+i).removeAttr('style');
                    //document.getElementById('discount'+i).innerHTML="0(0%)";
                    document.getElementById('discount'+i).innerHTML="0(0%)";
                    var span = document.getElementById('discount'+i);
                    span.style.color="green";
                    var value = $('#rent'+i).val();
                    $('#offered_rent'+i).val(value);
                    $('#same_as_rent'+i).attr('checked','checked');
                }
            }
            else
            {
                for(var i=1; i<=count; i++)
                {
                    // var value = $('#rent'+i).val();
                    $('#offered_rent'+i).val(0);
                    $('#same_as_rent'+i).removeAttr('checked');
                }
            }
        });

        $(".table-body").on('click',"tr",function()
        {
            var count = this.id;     
            $('#qty'+count).on('change',function()
            {
                var qty = parseInt($('#qty'+count).val());
                var rent = parseInt($('#offered_rent'+count).val());
                var deposite = parseInt($('#offered_deposite'+count).val());
                if(!rent)
                {
                    rent = 0;
                }
                if(!deposite)
                {
                    deposite = 0;
                }
                var total_rent = rent*qty;
                var total_deposite = deposite*qty;
                $('#offered_rent_total'+count).val(total_rent);
                document.getElementById('offered_rent_total_span'+count).innerHTML = total_rent;
                document.getElementById('offered_deposite_total_span'+count).innerHTML = deposite;
            });        
            $('#sale'+count).on('change',function()
            {
                var product_id = $('#equipments'+count).val();
                $.ajax({  //create an ajax request to display.php
                type: "GET",
                url: "<?php echo url('/'); ?>/fetch_product_details_sales/"+product_id,
                success: function (data)
                {
                    //alert(data);
                    var obj = jQuery.parseJSON(data);
                    //document.getElementById('deposite'+count).innerHTML = obj.product_deposite;
                    document.getElementById('deposite'+count).innerHTML = "-";
                    $('#deposite'+count).val("-");
                    document.getElementById('rent_td'+count).innerHTML = obj.product_sale_rate;
                    var min_rent = parseInt(obj.product_sale_rate)-(parseInt(obj.product_sale_rate)*parseInt(obj.min_rent_percentage)/100);
                    //document.getElementById('min_rent'+count).innerHTML = min_rent;
                    //$('#min_rent'+count).val(null);
                    $('#offered_deposite'+count).removeAttr('required');
                    $('#offered_deposite'+count).val(0);
                    $('#offered_rent'+count).val(0);
                    $('#min_rent'+count).val(min_rent);
                    $('#min_depo'+count).val(0);
                    $('#rent'+count).val(obj.product_sale_rate);
                    $('#prod_deposite'+count).val(0);
                }
                });
                //alert(equipment_id);

            });
            $('#rental'+count).on('change',function()
            {
                var product_id = $('#equipments'+count).val();
                $.ajax({  //create an ajax request to display.php
                type: "GET",
                url: "<?php echo url('/'); ?>/fetch_product_details/"+product_id,
                success: function (data)
                {
                    var obj = jQuery.parseJSON(data);
                    document.getElementById('deposite'+count).innerHTML = obj.product_deposite;
                    $('#deposite'+count).val(obj.product_deposite);
                    document.getElementById('rent_td'+count).innerHTML = obj.product_rent;
                    var min_rent = parseInt(obj.product_rent)-(parseInt(obj.product_rent)*parseInt(obj.min_rent_percentage)/100);
                    var min_depo = parseInt(obj.product_deposite)-(parseInt(obj.product_deposite)*parseInt(obj.min_rent_percentage)/100);
                    $('#offered_deposite'+count).attr('required',true);
                    $('#offered_rent'+count).attr('required',true);
                    $('#min_rent'+count).val(min_rent);
                    $('#min_depo'+count).val(min_depo);
                    $('#rent'+count).val(obj.product_rent);
                    $('#prod_deposite'+count).val(obj.product_deposite);
                }
                });
                //alert(equipment_id);
            });
            $('#same_as_deposite'+count).on('change',function(){
                //alert(count);
                if(this.checked)
                {
                    $('#offered_deposite'+count).removeAttr('style');
                    var value = $('#prod_deposite'+count).val();
                    $('#offered_deposite'+count).val(value);
                }
                else
                {
                    $('#offered_deposite'+count).val(0);                 
                }
            });
            $('#same_as_rent'+count).on('change',function(){
                //alert(count);
                if(this.checked)
                {
                    $('#offered_rent'+count).removeAttr('style');
                    document.getElementById('discount'+count).innerHTML="0(0%)";
                    var span = document.getElementById('discount'+count);
                    span.style.color="green";
                    var value = $('#rent'+count).val();
                    $('#offered_rent'+count).val(value);
                }
                else
                {
                    $('#offered_rent'+count).val(0);                 
                }
            });
            $("#offered_rent"+count).on('input',function(e){
                var min_rent = $('#min_rent'+count).val();
                    min_rent = parseInt(min_rent);
                var rent = $('#rent'+count).val();
                    rent = parseInt(rent);
                var entered_rent = $('#offered_rent'+count).val();
                    entered_rent = parseInt(entered_rent);
                if(!entered_rent)
                {
                    entered_rent = 0;
                    rent = 0;
                }
                var discount = rent - entered_rent;
                if(discount != 0)
                {
                    var discount_percentage = discount/rent*100;
                    discount_percentage = parseInt(discount_percentage);    
                }
                else
                {
                    var discount_percentage = 0;
                    discount_percentage = parseInt(discount_percentage);
                }
                document.getElementById('discount'+count).innerHTML=discount+"("+discount_percentage+"%)";
                $('#discount_hidden'+count).val(discount);
                var span = document.getElementById('discount'+count);
                if(discount_percentage<25)
                {
                    span.style.color="green";
                }
                else if(discount_percentage>25 && discount_percentage<=50)
                {
                    span.style.color="#FFBE00";
                }
                else if(discount_percentage>50)
                {
                    span.style.color="red";
                }
                $('#same_as_rent'+count).removeAttr('checked');
                if(entered_rent < min_rent)
                {
                    $('#offered_rent'+count).attr('style', "border-radius: 5px; border:#FF0000 1px solid;");
                }
                else
                {
                    $('#offered_rent'+count).removeAttr('style');
                }
                
            })
            $("#offered_deposite"+count).on('input',function(e){
                var min_depo = $('#min_depo'+count).val();
                    min_depo = parseInt(min_depo)
                var entered_rent = $('#offered_deposite'+count).val();
                    entered_rent = parseInt(entered_rent)
                
                if(entered_rent < min_depo)
                {
                    $('#offered_deposite'+count).attr('style', "border-radius: 5px; border:#FF0000 1px solid;");
                }
                else
                {

                    $('#offered_deposite'+count).removeAttr('style');
                }
                
            })
            
            $("#equipments"+count).on('change',function(){
                var product_id = $("#equipments"+count).val();
                //alert(product_id);
                var sale_rental = $('input[name="sale_rental[]'+count+'"]:checked').val();
                var url_link = null;
                if(sale_rental == 'Rental')
                {
                    url_link = "<?php echo url('/'); ?>/fetch_product_details/";
                }
                else if(sale_rental == 'Sale')
                {
                    url_link = "<?php echo url('/'); ?>/fetch_product_details_sales/";
                }
                $.ajax({  //create an ajax request to display.php
                type: "GET",
                url: url_link+product_id,
                success: function (data)
                {
                    var obj = jQuery.parseJSON(data);
                    if(sale_rental == 'Rental')
                    {
                        //document.getElementById('deposite'+count).innerHTML = obj.product_deposite;
                        document.getElementById('deposite'+count).innerHTML = obj.product_deposite;
                        $('#deposite'+count).val(obj.product_deposite);
                        document.getElementById('rent_td'+count).innerHTML = obj.product_rent;
                        var min_rent = parseInt(obj.product_rent)-(parseInt(obj.product_rent)*parseInt(obj.min_rent_percentage)/100);
                        var min_depo = parseInt(obj.product_deposite)-(parseInt(obj.product_deposite)*parseInt(obj.min_rent_percentage)/100);
                        //document.getElementById('min_rent'+count).innerHTML = min_rent;
                        $('#min_rent'+count).val(min_rent);
                        $('#min_depo'+count).val(min_depo);
                        $('#rent'+count).val(obj.product_rent);
                        $('#prod_deposite'+count).val(obj.product_deposite);   
                    }
                    else if(sale_rental == 'Sale')
                    {
                        document.getElementById('deposite'+count).innerHTML = "-";
                        $('#deposite'+count).val("-");
                        document.getElementById('rent_td'+count).innerHTML = obj.product_sale_rate;
                        var min_rent = parseInt(obj.product_sale_rate)-(parseInt(obj.product_sale_rate)*parseInt(obj.min_rent_percentage)/100);
                        //document.getElementById('min_rent'+count).innerHTML = min_rent;
                        //$('#min_rent'+count).val(null);
                        $('#offered_deposite'+count).removeAttr('required');
                        $('#offered_deposite'+count).val(0);
                        $('#offered_rent'+count).val(0);
                        $('#min_rent'+count).val(min_rent);
                        $('#min_depo'+count).val(0);
                        $('#rent'+count).val(obj.product_sale_rate);
                        $('#prod_deposite'+count).val(0);
                    }
                }
                });
            });
            var count1 = parseInt($('#equipment_table tr:last').attr('id'));
            if(isNaN(count1))
            {
                count1 = 1;
            }
            var total_rent = 0;
            var total_sale = 0;
            var total_deposite = 0;
            var total_transport = 0;
            var total_discount = 0;
            for(var i = 1; i <= count1; i++)
            {
                //var sale_rental = $('#sale'+i).val();
                var qty = $('#qty'+i).val();
                var sale_rental = $('input[name="sale_rental[]'+i+'"]:checked').val();
                var offered_rent = $('#offered_rent'+i).val();
                var offered_sale = $('#offered_rent'+i).val();
                var offered_deposite = $('#offered_deposite'+i).val();
                var transport = $('#transport'+i).val();
                var discount = $('#discount_hidden'+i).val();
                
                //alert(sale_rental);
                if(!offered_rent)
                {
                    offered_rent = 0;
                }
                if(!offered_sale)
                {
                    offered_sale = 0;
                }
                if(sale_rental == "Rental")
                {
                    offered_sale = 0;
                }
                else if(sale_rental == "Sale")
                {
                    offered_rent = 0;
                }
                if(!offered_deposite)
                {
                    offered_deposite = 0;
                }
                if(!transport)
                {
                    transport = 0;
                }
                if(!discount)
                {
                    discount = 0;
                }
                if(sale_rental == "Rental")
                {
                    var total_offered_rent_pre = parseInt(qty)*parseInt(offered_rent);
                }
                else if(sale_rental == "Sale")
                {
                    var total_offered_rent_pre = parseInt(qty)*parseInt(offered_sale);
                }
                var total_offered_deposit_pre = parseInt(qty)*parseInt(offered_deposite);
                $('#offered_rent_total'+i).val(total_offered_rent_pre);
                $('#offered_deposite_total'+i).val(offered_deposite);
                document.getElementById('offered_rent_total_span'+i).innerHTML = total_offered_rent_pre;
                // document.getElementById('offered_deposite_total_span'+i).innerHTML = offered_deposite;
                var offered_rent_total = $('#offered_rent_total'+i).val();
                var offered_sale_total = $('#offered_rent_total'+i).val();
                var offered_deposite_total = $('#offered_deposite_total'+i).val();
                if(!offered_deposite_total)
                {
                    offered_deposite_total = 0;
                }
                if(!offered_rent_total)
                {
                    offered_rent_total = 0;
                }
                if(!offered_sale_total)
                {
                    offered_sale_total = 0;
                }
                if(sale_rental == "Rental")
                {
                    offered_sale_total = 0;
                }
                else if(sale_rental == "Sale")
                {
                    offered_rent_total = 0;
                }
                total_rent=total_rent +parseInt(offered_rent_total);
                total_sale=total_sale +parseInt(offered_sale_total);
                total_deposite=total_deposite +parseInt(offered_deposite_total);
                total_transport=total_transport +parseInt(transport);
                total_discount=total_discount +parseInt(discount);
            }
            var grand_total = total_rent + total_sale + total_deposite + total_transport;
            document.getElementById('total_rent').innerHTML = total_rent;
            document.getElementById('total_sale').innerHTML = total_sale;
            document.getElementById('total_deposite').innerHTML = total_deposite;
            document.getElementById('total_transport').innerHTML = total_transport;
            document.getElementById('total_discount').innerHTML = total_discount;
            document.getElementById('grand_total').innerHTML = grand_total;
            $('#lead_value').val(grand_total);

        });
        
        $('.table-responsive').on('show.bs.dropdown', function(){
            $('.table-responsive').css("overflow","inherit");
        });
        $('.table-responsive').on('hide.bs.dropdown', function(){
            $('.table-responsive').css("overflow","auto");
        });
        function numberOnly(id) 
        {
            var element = document.getElementById(id);
            element.value = element.value.replace(/[^0-9]/gi, "");
        }
    </script>
    @endsection

</body>

</html>