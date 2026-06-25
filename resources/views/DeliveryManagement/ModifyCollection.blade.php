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
                                <form class="form" method="POST" action="<?php echo url('/');?>/ModifyCollection/{{$order_details[0]['order_id']}}">
                                {{ csrf_field() }}
                                {{-- <input type="hidden" name="order_id" value="{{$order_details[0]['order_id']}}"> --}}
                                {{-- <input type="hidden" name="lead_id" value="{{$order_details[0]['lead_id']}}"> --}}
                                    <div class="row">
                                        
                                        <div class="col-md-10">
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
                                                    <select name="del_status" class="selectpicker form-control" id="del_status" @if($order_details[0]['status']=="Collected"){{"disabled"}} @endif>
                                                        <option value="Assigned" @if($order_details[0]['status']=="Assigned"){{"selected"}} @endif>Assigned</option>
                                                        <option value="Accepted" @if($order_details[0]['status']=="Accepted"){{"selected"}} @endif>Accepted</option>
                                                        <option value="InProgress" @if($order_details[0]['status']=="InProgress"){{"selected"}} @endif>InProgress</option>
                                                        <option value="Collected" @if($order_details[0]['status']=="Collected"){{"selected"}} @endif>Collected</option>
                                                    </select>
                                                    @if($order_details[0]['status'] == "Collected")
                                                        <input type="hidden" name="del_status" value="Collected">
                                                    @endif

                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="del_assigned_to">Assigned To</label>
                                                    <div class="form-group">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="self_pick" id="self_pick" @if($order_details[0]['DelAssignedTo']=="Customer"){{"checked"}} @endif>
                                                            <label class="form-check-label" for="self_pick">
                                                                Customer self pickup
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <select name="del_assigned_to" class="selectpicker form-control" data-live-search="true" title="select delboy" id="del_assigned_to" @if($order_details[0]['DelAssignedTo']=="Customer"){{"disabled"}} @endif>
                                                        {{-- <option value="Pending" selected>Pending</option> --}}
                                                        {{-- <option value="No Helper" >No Helper</option> --}}
                                                            @foreach($delboys as $delboy)
                                                                <option value="{{$delboy['username']}}" @if($order_details[0]['DelAssignedTo']==$delboy['username']){{"selected"}} @endif>{{$delboy['username']}}</option>
                                                            @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="del_assigned_to">Helpers</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <select class="selectpicker helpers form-control" name="helpers[]" id="helpers" data-live-search="true" multiple="multiple" @if($order_details[0]['DelAssignedTo']=="Customer"){{"disabled"}} @endif>
                                                        {{!$helpers = json_decode($order_details[0]['helpers'])}}
                                                        <option value="No Helper" @if(isset($helpers))@if($helpers[0]=='No Helper'){{"selected"}}@endif @else{{"selected"}}@endif>No Helper</option>
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
                                                    <label for="pick_up_from">Pick up From</label>
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
                                            {{-- <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="date">Date</label>
                                                </div>
                                                <div class="col-md-8 text-left">
                                                    <span>{{date('Y-m-d')}}</span>
                                                </div>
                                            </div> --}}
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="date">Date</label>
                                                </div>
                                                <div class="col-md-8 text-left">
                                                    <input class="form-control" type="date" onkeydown="return false" name="del_date" id="del_date" value="{{date('Y-m-d',strtotime($order_details[0]['DelDate']))}}">
                                                    {{-- <input class="form-control" type="date" onkeydown="return false" name="del_date" id="del_date" value="{{date('Y-m-d',strtotime($order_details[0]['DelDate']))}}" min="{{date('Y-m-d',strtotime(date('Y',strtotime($order_details[0]['created_at'])).'-'.date('m',strtotime($order_details[0]['created_at'])).'-'.'01'))}}"> --}}
                                                    {{-- <span>{{date('m-d-Y',strtotime($order_details[0]['DelDate']))}}</span> --}}
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
                                                <div class="table table-responsive jim-table-responsive">
                                                    <table id="DataTable" class="table table-bordered" width="100%">
                                                        <thead>
                                                            <th>Sr. No.</th>
                                                            {{-- <th>Delivery Date</th> --}}
                                                            <th>Equipment Name</th>
                                                            <th>Qty</th>
                                                            <th>Start Date</th>
                                                            <th>End Date</th>
                                                            {{-- <th>Deposit</th> --}}
                                                            <th>Product Rent</th>
                                                            <th>Collect Rent</th>
                                                            <th>Discount</th>
                                                            {{-- <th>Drop Location</th>      --}}
                                                            {{-- <th>Transport</th> --}}
                                                        </thead>
                                                        <tbody class="table-body">
                                                                @php
                                                                    $count = 0;
                                                                    $srno=1;
                                                                @endphp
                                                                @foreach ($product_details as $product_inf) 
                                                                    <tr id="{{$count}}">
                                                                        <td data-label="Srno">{{$srno++}}</td>
                                                                        {{-- <td>{{$product_details[$i]['creation_date']}}</td> --}}
                                                                        <td data-label="Product Name">
                                                                            {{$product_inf['product_name']}}
                                                                            <input type="hidden" name="order_details_id[]" id="order_details_id" value="{{$product_inf['order_details_id']}}">
                                                                            <input type="hidden" name="order_id[]" id="order_id" value="{{$product_inf['order_id']}}">
                                                                            <input type="hidden" name="renewal_main_id[]" id="renewal_main_id" value="{{$product_inf['renewal_main_id']}}">
                                                                            {{-- <input type="hidden" name="lead_id[]" id="lead_id" value="{{$product_inf['lead_id']}}"> --}}
                                                                        </td>
                                                                        <td data-label="Quantity">{{$product_inf['product_qty']}}</td>
                                                                        <td data-label="Start Date">{{date('d-m-Y',strtotime($product_inf['start_date']))}}</td>
                                                                        <td data-label="End Date">{{date('d-m-Y',strtotime($product_inf['end_date']))}}</td>
                                                                        {{-- <td data-label="">{{$product_inf['product_deposite']}}</td> --}}
                                                                        <td data-label="Rent">{{$product_inf['product_rent']}}</td>
                                                                        <td data-label="Collect Rent">{{$product_inf['cash_amount']}}</td>
                                                                        <td data-label="Discount">{{$product_inf['discount_amt']}}</td>
                                                                    </tr>
                                                                    @php($count++)
                                                                @endforeach
                                                                <tr>
                                                                    <td colspan="7" class="text-right">Total: 
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
                                                    <input type="radio" name="invoice_type" id="softcopy" value="NO" @if($order_details[0]['ReceiptToBeCarried']=='NO'){{"checked"}} @endif> <label for="softcopy" class="form-label">Softcopy</label>
                                                    <input type="radio" name="invoice_type" id="hardcopy" value="YES" @if($order_details[0]['ReceiptToBeCarried']=='YES'){{"checked"}} @endif> <label for="hardcopy" class="form-label">Hardcopy</label>
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
                                                    <input type="radio" name="payment_mode" id="Online" value="Online" @if($order_details[0]['PaymentMode']=='Online'){{"checked"}} @endif> <label for="Online" class="form-label">Online</label>
                                                    <input type="radio" name="payment_mode" id="cash" value="Cash"  @if($order_details[0]['PaymentMode']=='Cash'){{"checked"}} @endif> <label for="cash" class="form-label">Cash</label>
                                                    <input type="radio" name="payment_mode" id="both" value="Both"  @if($order_details[0]['PaymentMode']=='Both'){{"checked"}} @endif disabled> <label for="both" class="form-label">Both</label>
                                                </div>
                                            </div>

                                            <div class="row form-group cash_online_div" style="@if($order_details[0]['PaymentMode']=='Both'){{"display:block"}} @else {{"display:none"}} @endif">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="cash">Cash</span>
                                                            </div>
                                                            <input type="text" class="form-control" name="cash_amount" id="cash_amount" placeholder="Cash Amount" value="@if(isset($order_details[0]['cash'])){{$order_details[0]['cash']}}@endif">
                                                        </div>
                                                        
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="online">Online</span>
                                                            </div>
                                                            <input type="text" class="form-control" id="online_amount" name="online_amount" placeholder="Online Amount" value="@if(isset($order_details[0]['online'])){{$order_details[0]['online']}}@endif">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="travel">Travel</label>
                                                </div>
                                                <div class="col-md-8 text-left">
                                                    <input type="radio" name="travel" id="pendingtravel" value="Pending" @if($order_details[0]['TravelMode']=='Pending'){{"checked"}} @endif style="display:none;">
                                                    <input type="radio" name="travel" id="local" value="Local" @if($order_details[0]['TravelMode']=='Local'){{"checked"}} @endif> <label for="local" class="form-label">Local</label>
                                                    <input type="radio" name="travel" id="rikshaw" value="Rikshaw" @if($order_details[0]['TravelMode']=='Rikshaw'){{"checked"}} @endif> <label for="rikshaw" class="form-label">Rikshaw</label>
                                                    <input type="radio" name="travel" id="bike" value="Bike" @if($order_details[0]['TravelMode']=='Bike'){{"checked"}} @endif> <label for="bike" class="form-label">Bike</label>
                                                    <input type="radio" name="travel" id="tempo" value="Tempo" @if($order_details[0]['TravelMode']=='Tempo'){{"checked"}} @endif> <label for="tempo" class="form-label">Tempo</label>
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
        $("#self_pick").click(function(){
            if($(this).prop('checked') == true)
            {
                // alert('hehehe');
                $("#del_assigned_to").attr('disabled',true);
                $("#helpers").attr('disabled',true);
                $('.selectpicker').selectpicker('refresh');
            }
            else
            {
                $("#del_assigned_to").attr('disabled',false);
                $("#helpers").attr('disabled',false);
                $('.selectpicker').selectpicker('refresh');
            }
        });
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

        $(document).ready(function() {
            var selected_boy = $('#del_assigned_to').val();
            if(selected_boy!=NaN)
            {
                //$('#helpers option').attr("disabled",false);
                $('#helpers option[value='+selected_boy+']').attr("disabled",true);
                $('#helpers').selectpicker('render');
            }
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
        // $('#helpers').on('change',function()
        // {   
        //     var get_val = $(this).val();
            
        //     if(get_val[0]=='No Helper')
        //     {
        //         $('#helpers option[value="No Helper"]').attr("selected",false);
        //         //$("option:selected").removeAttr("selected");
        //         $('#helpers').selectpicker('refresh');
        //     }
        //     // selected_helpers.push($(this).val());
        //     if(get_val[0]=='No Helper')
        //     {
        //         for(var i=1; i<get_val.length; i++)
        //         {
        //             $('#helpers option[value='+get_val[i]+']').prop("selected",false);
        //         }
        //         $(this).selectpicker('refresh');
        //     }
            
        // });
        $(".helpers").on('show.bs.select',function(){
            // console.log("Saving value " + $(this).val());
            $(this).data('val', $(this).val());
        });

        $(".helpers").change(function(){
            // console.log($(this).val());
            let values = $(this).val();
            let oldvalues = $(this).data('val');
            // console.log("Old Value");
            // console.log($(this).data('val'));
            // console.log("New Value");
            // console.log($(this).val());
            // const filteredArray = values.filter(value => oldvalues.includes(value));
            const filteredArray = values.filter(function(obj) { return oldvalues.indexOf(obj) == -1; });
            // console.log("Filtered");
            // console.log(filteredArray);
            let isnohelper = false;
            if(values.length>0)
            {
                if(filteredArray)
                {
                    $.each(filteredArray,function($key,$value)
                    {
                        if($value == 'No Helper')
                        {
                            isnohelper = true;
                        }
                        // console.log($value);
                    });
                    if(isnohelper)
                    {
                        $.each(values,function($key,$value)
                        {
                            $(".helpers option[value='"+$value+"']").prop("selected",false); 
                            // console.log($value);
                        });
                        $(".helpers option[value='No Helper']").prop("selected",true);
                    }
                    else
                    {
                        $(".helpers option[value='No Helper']").prop("selected",false);
                    }
                }
                else{
                    $(".helpers option[value='No Helper']").prop("selected",false);
                }
            }
            $(".helpers").selectpicker("refresh");
            $(".helpers").selectpicker("toggle");
            $(".helpers").selectpicker("toggle");
        });
	</script>
    @endsection

    </body>
</html>