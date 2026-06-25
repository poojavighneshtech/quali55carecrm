<!DOCTYPE html>
<html lang="en">
    @extends('header_and_sidebar')
    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Delivery : Assign DelBoy</title>
        @section('styles')
       
            <style>
            </style>
        @endsection
    </head>

        

<body id="page-top">	
		<!-- Page Wrapper -->
        @section('breadcrumb_item')
           
            <li class="breadcrumb-item active" aria-content="page">Add New Delivery</li>
        @endsection
            <div class="container">                
                @section('content')
                <div class="row">
                    <div class="col-md-1">
                    </div>
                    <div class="col-md-10">
                        <div class="container">  
                            @if(session()->has('message'))
                            <div class="alert alert-success">
                                {{ session()->get('message') }}
                            </div>
                        @endif
                        @if(session()->has('message_delete'))
                            <div class="alert alert-danger">
                                {{ session()->get('message_delete') }}
                            </div>
                        @endif 
                        @if(session()->has('error'))
                            <div class="alert alert-danger">
                                {{ session()->get('error') }}
                            </div>
                        @endif 
                        <div class="card card-primary">
                            <div class="card-header text-center" style="background-color: #345bcc; color: white;">
                                <span><b>Assign Collection Boy</b></span>
                            </div>
                            <div class="card-body">
                                <form class="form" method="POST" action="<?php echo url('/');?>/assign_collection_delboy_post/{{$order_details[0]['order_id']}}">
                                {{ csrf_field() }}
                                {{-- <input type="hidden" name="order_id" value="{{$order_details[0]['order_id']}}"> --}}
                                {{-- <input type="hidden" name="lead_id" value="{{$order_details[0]['lead_id']}}"> --}}
                                    <div class="row">
                                        
                                        <div class="col-md-10 text-right">
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="delivery_type">Delivery Type</label>
                                                </div>
                                                <div class="col-md-8 text-left">
                                                    <input type="radio" name="delivery_type" id="delivery" value="Delivery" disabled> <label for="delivery">Delivery</label>
                                                    <input type="radio" name="delivery_type" id="pickup" value="Pick Up" disabled> <label for="pickup">Pickup</label>
                                                    <input type="radio" name="delivery_type" id="collection" value="Collection" checked="checked"> <label for="collection">Collection</label>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="status">Status</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <select name="del_status" class="selectpicker form-control" id="del_status">
                                                        <option value="Pending">Pending</option>
                                                        <option value="Assigned" selected>Assigned</option>
                                                        <option value="Accepted">Accepted</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="del_assigned_to">Assigned To</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <select name="del_assigned_to" class="selectpicker form-control" id="del_assigned_to">
                                                        <option value="Pending" selected>Pending</option>
                                                        <?php 
                                                            foreach($delboys as $delboy)
                                                            {
                                                        ?>
                                                                <option value="{{$delboy['username']}}"<?php if (session('selected_delboy') == $delboy['username']){echo "selected";}?>>{{$delboy['username']}}</option>
                                                        <?php
                                                            } 
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="del_assigned_to">Helpers</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <select class="selectpicker form-control" name="helpers[]" id="helpers" data-live-search="true" multiple="multiple" required>
                                                        {{!$helpers = json_decode($order_details[0]['helpers'])}}
                                                        <option value="No Helper" selected>No Helper</option>
                                                            @foreach($delboys as $delboy)
                                                                <option value="{{$delboy['username']}}"@if(isset($helpers))@if(in_array($delboy['username'], $helpers)){{"selected"}}@endif @endif>{{$delboy['username']}}</option>
                                                            @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            {{-- <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="del_assigned_to">Helpers</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <select class="selectpicker form-control" name="helpers" id="helpers" data-live-search="true" multiple="multiple">
                                                            @foreach($delboys as $delboy)
                                                                <option value="{{$delboy['username']}}"<?php if (session('selected_delboy') == $delboy['username']){echo "selected";}?>>{{$delboy['username']}}</option>
                                                            @endforeach
                                                    </select>
                                                </div>
                                            </div> --}}
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="name">Name</label>
                                                </div>
                                                <div class="col-md-8 text-left ">
                                                    <input type="hidden" name="name" value="{{$order_details[0]['shipping_first_name']}}">
                                                    <span>{{$order_details[0]['shipping_first_name']}}</span>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="contact_no">Mobile Number</label>
                                                </div>
                                                <div class="col-md-8 text-left">
                                                    {{-- <input type="hidden" name="name" value="{{$order_details[0]['shipping_first_name']}}"> --}}
                                                    <span>{{$order_details[0]['mobileno']}}</span>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="address">Address</label>
                                                </div>
                                                <div class="col-md-8 text-left">
                                                    {{-- <input type="hidden" name="name" value="{{$order_details[0]['shipping_first_name']}}"> --}}
                                                    <span>{{$order_details[0]['fulldetails']}}</span>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="cust_location">Customer Location</label>
                                                </div>
                                                <div class="col-md-8 text-left">
                                                    <span>{{$order_details[0]['location']}}</span>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="pick_up_from">Collection From</label>
                                                </div>
                                                <div class="col-md-8 text-left">
                                                    <span>Customer</span>
                                                </div>
                                            </div>
                                            {{-- <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="pick_up_from_address">Address</label>
                                                </div>
                                                <div class="col-md-8 text-left">
                                                    <span>Warehouse Address Here</span>
                                                </div>
                                            </div> --}}
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="Drop_at">Drop At</label>
                                                </div>
                                                <div class="col-md-8 text-left">
                                                    <span>Quali55Care</span>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="date">Date</label>
                                                </div>
                                                <div class="col-md-8 text-left">
                                                    <span>{{date('Y-m-d')}}</span>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="amount">Amount</label>
                                                </div> 
                                                <div class="col-md-8 text-left">
                                                    <span>{{$order_details[0]['TotalAmt']}}</span>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="amount_to_be">Amount To Be</label>
                                                </div>
                                                <div class="col-md-8 text-left">
                                                    <span>Collect</span>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="eqipments">Equipment Required*</label>
                                                </div>
                                            </div>
                                            <input type="hidden" name="collection_order_id" value="{{$order_details[0]['order_id']}}">
                                            <div class="row form-group">
                                                <div class="col-md-12 text-left">
                                                    <table id="DataTable" class="table table-bordered" width="100%">
                                                        <thead>
                                                            <th>Sr. No.</th>
                                                            <th>Equipment Name</th>
                                                            <th>Qty</th>
                                                            {{-- <th>Deposit</th> --}}
                                                            <th>Collect Rent</th>
                                                            {{-- <th>Transport</th> --}}
                                                        </thead>
                                                        <tbody class="table-body">
                                                                @php
                                                                    $count = 0;
                                                                    $srno=1;
                                                                @endphp
                                                                @foreach ($product_details as $product_inf) 
                                                                    <tr id="{{$count}}">
                                                                        <td>{{$srno++}}</td>
                                                                        {{-- <td>{{$product_details[$i]['creation_date']}}</td> --}}
                                                                        <td>
                                                                            {{$product_inf['product_name']}}
                                                                            <input type="hidden" name="order_details_id[]" id="order_details_id" value="{{$product_inf['id']}}">
                                                                            <input type="hidden" name="order_id[]" id="order_id" value="{{$product_inf['order_id']}}">
                                                                            <input type="hidden" name="renewal_main_id[]" id="renewal_main_id" value="{{$product_inf['renewal_main_id']}}">
                                                                            {{-- <input type="hidden" name="lead_id[]" id="lead_id" value="{{$product_inf['lead_id']}}"> --}}
                                                                        </td>
                                                                        <td>{{$product_inf['product_qty']}}</td>
                                                                        {{-- <td>{{$product_inf['product_deposite']}}</td> --}}
                                                                        <td>{{$product_inf['product_rent']}}</td>
                                                                        {{-- <td>
                                                                            <select class="selectpicker show-tick Drop_at" name="Drop_at[{{$count}}][id]" id="Drop_at{{$count}}" title="Drop Location" data-live-search="true" required>
                                                                                <optgroup  label="Pools">
                                                                                    @foreach($pool_details as $pool)
                                                                                        <option value="{{$pool['id']}}">{{$pool['registered_name']}}-{{$pool['wh_name']}},{{$pool['wh_city']}}</option>
                                                                                    @endforeach
                                                                                </optgroup>
                                                                                <optgroup label="Vendor" >
                                                                                    <option value="{{$product_inf['vendor_id']}}">{{$product_inf['vendor_name']}}</option>
                                                                                </optgroup>
                                                                            </select>
                                                                            <input type="hidden" name="Drop_at[{{$count}}][type]" id="drop_type{{$count}}">
                                                                            <input type="hidden" name="Drop_at[{{$count}}][product_id]" id="drop_type{{$count}}" value="{{$product_inf['product_id']}}">
                                                                        </td> --}}
                                                                    </tr>
                                                                    @php($count++)
                                                                @endforeach
                                                                <tr>
                                                                    <td colspan="3" class="text-right">Total</td>
                                                                    {{-- <td>
                                                                        {{$total_deposit}}
                                                                        <input type="hidden" name="total_deposit" value="{{$total_deposit}}">
                                                                    </td> --}}
                                                                    <td>
                                                                        {{$total_rent}} <small>(collect from customer)</small>
                                                                        <input type="hidden" name="total_rent" value="{{$total_rent}}">
                                                                    </td>
                                                                </tr>
                                                        </tbody>
                                                    </table>
                                                    <input type="hidden" name="row_ct" id="row_ct" value="{{$count}}">
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="invoice">Invoice</label>
                                                </div>
                                                <div class="col-md-8 text-left">
                                                    <input type="radio" name="invoice_type" id="softcopy" value="Softcopy" checked="checked"> <label for="softcopy" class="form-label">Softcopy</label>
                                                    <input type="radio" name="invoice_type" id="hardcopy" value="Hardcopy"> <label for="hardcopy" class="form-label">Hardcopy</label>
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="amount">Amount</label>
                                                </div>
                                                <div class="col-md-8 text-left">
                                                    <span>{{$order_details[0]['TotalAmt']}}</span>
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="payment_mode">Payment Mode</label>
                                                </div>
                                                <div class="col-md-8 text-left">
                                                    <input type="radio" name="payment_mode" id="Online" value="Online" @if($order_details[0]['PaymentMode']=='Online'){{"checked"}}@endif> <label for="Online" class="form-label">Online</label>
                                                    <input type="radio" name="payment_mode" id="cash" value="Cash" @if($order_details[0]['PaymentMode']=='Cash'){{"checked"}}@endif> <label for="cash" class="form-label">Cash</label>
                                                    <input type="radio" name="payment_mode" id="both" value="Both" disabled> <label for="both" class="form-label">Both</label>
                                                </div>
                                            </div>

                                            <div class="row form-group cash_online_div" style="display: none">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="cash">Cash</span>
                                                                </div>
                                                                <input type="text" class="form-control" name="cash_amount" id="cash_amount" placeholder="Cash Amount" >
                                                            </div>
                                                            
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="online">Online</span>
                                                                </div>
                                                                <input type="text" class="form-control" id="online_amount" name="online_amount" placeholder="Online Amount">
                                                            </div>
                                                        </div>
                                                    </div>
                                                
                                            </div>

                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="travel">Travel</label>
                                                </div>
                                                <div class="col-md-8 text-left">
                                                    <input type="radio" name="travel" id="local" value="Local" checked="checked"> <label for="local" class="form-label">Local</label>
                                                    <input type="radio" name="travel" id="rikshaw" value="Rikshaw"> <label for="rikshaw" class="form-label">Rikshaw</label>
                                                    <input type="radio" name="travel" id="bike" value="Bike"> <label for="bike" class="form-label">Bike</label>
                                                    <input type="radio" name="travel" id="tempo" value="Tempo"> <label for="tempo" class="form-label">Tempo</label>
                                                </div>
                                            </div>


                                            <div class="row form-group">
                                                <div class="col-md-12">
                                                    <center>
                                                        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                                                        <button type="reset" name="reset" class="btn btn-secondary">Clear</button>
                                                    </center>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
    </div>	   
    @section('script')    
    <script>
        $('select').on('change',function(){
            var value = $(this).val();
            if(value == 'Multiple')
            {
                $('#multiple_products').show();
            }
            else
            {
                $('#multiple_products').hide();
            }
        });


        function numberOnly(id) {
            var element = document.getElementById(id);
            element.value = element.value.replace(/[^0-9]/gi, "");
        }

        var count = $('#row_ct').val();
        $('#DataTable tr').click(function()
        {
            var id= this.id;
            $('#Drop_at'+id).selectpicker('render');
            $('#Drop_at'+id).on('change', function(e){
                var picker_id=e.target.id;
                var selected_option = $(this).find(":selected"); // get selected option for the changed select only
                var selected_value = selected_option.val();
                var optgroup = selected_option.parent().attr('label');
                //alert("select");
                $('#drop_type'+id).val(optgroup);    
            }); 
        });

        $('.table-responsive').on('show.bs.dropdown', function () {
        $('.table-responsive').css( "overflow", "inherit" );
        });

        $('.table-responsive').on('hide.bs.dropdown', function () {
            $('.table-responsive').css( "overflow", "auto" );
        })
        

        // $('.Drop_at').selectpicker('render');
        // $('.Drop_at').on('change', function(e){
        //     var picker_id=e.target.id;
        //     var selected_option = $(this).find(":selected"); // get selected option for the changed select only
        //     var selected_value = selected_option.val();
        //     var optgroup = selected_option.parent().attr('label');
        //     $('#drop_type').val(optgroup);
        // }); 

        $(document).ready(function() {
            $('input:radio[name=payment_mode]').change(function() {
                if (this.value == 'Both') {
                    $('.cash_online_div').css("display", "inline-block");
                }
                else{
                    $('.cash_online_div').css("display", "none");
                }
            });
        }); 
        $('#del_assigned_to').on('change',function()
        {
            var del_boy = $(this).val();
            $('#helpers option').attr("disabled",false);
            $('#helpers option[value='+del_boy+']').prop("selected",false);
            $('#helpers option[value='+del_boy+']').attr("disabled",true);
            $('#helpers').selectpicker('refresh');
        });
        //var helperBoys = $('#helpers').find(":selected").val();
        $('#helpers').on('change',function()
        {   
            var get_val = $(this).val();
            
            if(get_val[0]=='No Helper')
            {
                $('#helpers option[value="No Helper"]').attr("selected",false);
                //$("option:selected").removeAttr("selected");
                $('#helpers').selectpicker('refresh');
            }
            // selected_helpers.push($(this).val());
            if(get_val[0]=='No Helper')
            {
                for(var i=1; i<get_val.length; i++)
                {
                    $('#helpers option[value='+get_val[i]+']').prop("selected",false);
                }
                $(this).selectpicker('refresh');
            }
            
        });
	</script>
    @endsection

    </body>
</html>