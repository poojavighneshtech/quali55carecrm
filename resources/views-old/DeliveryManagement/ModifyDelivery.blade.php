<!DOCTYPE html>
<html lang="en">
    @extends('header_and_sidebar')
    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Delivery : Modify</title>
        @section('styles')
       
        
        @endsection
    </head>

        

<body id="page-top">	
		<!-- Page Wrapper -->
        @section('breadcrumb_item')
            <li class="breadcrumb-item active" aria-content="page">Modify Delivery</li>
        @endsection
            <div class="container">
                @section('content')
                    <div class="row">
                    <div class="col-md-1">
                    </div>
                    <div class="col-md-10">
                        <div class="card card-primary">
                            <div class="card-header text-center" style="background-color: #345bcc; color: white;">
                                <span><b>Modify Delivery</b></span>
                            </div>
                            <div class="card-body">
                                {{-- {{print_r($order_details)}} --}}
                                {{-- <form action="{{url('/')}}/filterRejectedOrders" method="post">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-2">
                                            
                                        </div>
                                        <div class="col-6 col-md-8">
                                            <div class="row">
                                                <label for="Filter"><strong>Filter :</strong></label>
                                                <div class="col-6 col-md-4">
                                                    <input type="date" class="form-control" name="start_date" id="start_date" value="@if(isset($start_date)){{$start_date}}@endif" required>
                                                </div>
                                                <strong>To</strong> 
                                                <div class="col-6 col-md-4">
                                                <input type="date" class="form-control" name="end_date" id="end_date" value="@if(isset($end_date)){{$end_date}}@endif" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="submit" class="btn btn-outline-primary btn-sm btn-block" name="btn_date_search" value="Search">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form> --}}
                                <form class="form" method="POST" action="<?php echo url('/');?>/ModifyDeliveryPost">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="order_id" value="{{$order_details[0]['order_id']}}">
                                    <input type="hidden" name="lead_id" value="{{$order_details[0]['lead_id']}}">
                                    <div class="row">
                                        
                                        <div class="col-md-10">
                                            <div class="row form-group">
                                                <div class=" col-md-4">
                                                    <label for="delivery_type">Delivery Type</label>
                                                </div>
                                                <div class=" col-md-8 text-left">
                                                    <input type="radio" name="delivery_type" id="delivery" value="Delivery" @if($order_details[0]['deliverypickup'] == "Delivery"){{"checked"}}@else{{"disabled"}}@endif> <label for="delivery">Delivery</label>
                                                    <input type="radio" name="delivery_type" id="pickup" value="Pickup" @if($order_details[0]['deliverypickup'] == "Pickup"){{"checked"}}@else{{"disabled"}}@endif> <label for="pickup">Pickup</label>
                                                    <input type="radio" name="delivery_type" id="collection" value="Collection" @if($order_details[0]['deliverypickup'] == "Collection"){{"checked"}}@else{{"disabled"}}@endif> <label for="collection">Collection</label>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class=" col-md-4">
                                                    <label for="status">Status</label>
                                                </div>
                                                <div class=" col-md-8">
                                                    <select name="del_status" class="selectpicker form-control" id="del_status">
                                                        <option value="Pending" @if($order_details[0]['status'] == "Pending"){{"selected"}}@else{{""}}@endif>Pending</option>
                                                        <option value="Assigned" @if($order_details[0]['status'] == "Assigned"){{"selected"}}@else{{""}}@endif>Assigned</option>
                                                        <option value="Accepted" @if($order_details[0]['status'] == "Accepted"){{"selected"}}@else{{""}}@endif>Accepted</option>
                                                        <option value="Delivered" @if($order_details[0]['status'] == "Delivered"){{"selected"}}@else{{""}}@endif>Delivered</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class=" col-md-4">
                                                    <label for="del_assigned_to">Assigned To</label>
                                                </div>
                                                <div class=" col-md-8">
                                                    <select name="del_assigned_to" class="selectpicker form-control" id="del_assigned_to">
                                                        <option value="Pending" selected>Pending</option>
                                                        <?php 
                                                            foreach($delboys as $delboy)
                                                            {
                                                        ?>
                                                                {{-- <option value="{{$delboy['username']}}"<?php //if (session('selected_delboy') == $delboy['username']){echo "selected";}?>>{{$delboy['username']}}</option> --}}
                                                                <option value="{{$delboy['username']}}" @if($order_details[0]['DelAssignedTo'] == $delboy['username']){{"selected"}}@endif>{{$delboy['username']}}</option>
                                                        <?php
                                                            } 
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class=" col-md-4">
                                                    <label for="del_assigned_to">Helpers</label>
                                                </div>
                                                <div class=" col-md-8">
                                                    <select class="selectpicker helpers form-control" name="helpers[]" id="helpers" data-live-search="true" multiple="multiple" required>
                                                        {{!$helpers = json_decode($order_details[0]['helpers'])}}
                                                        <option value="No Helper" @if(isset($helpers))@if($helpers[0]=='No Helper'){{"selected"}}@endif @else{{"selected"}}@endif>No Helper</option>
                                                            @foreach($delboys as $delboy)
                                                                <option value="{{$delboy['username']}}"@if(isset($helpers))@if(in_array($delboy['username'], $helpers)){{"selected"}}@endif @endif>{{$delboy['username']}}</option>
                                                            @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-6 col-md-4">
                                                    <label for="name">Name</label>
                                                </div>
                                                <div class="col-6 col-md-8 text-left ">
                                                    <input type="hidden" name="name" value="{{$order_details[0]['shipping_first_name']}}">
                                                    <span>{{$order_details[0]['shipping_first_name']}}</span>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-6 col-md-4">
                                                    <label for="contact_no">Mobile Number</label>
                                                </div>
                                                <div class="col-6 col-md-8 text-left">
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
                                                <div class="col-6 col-md-4">
                                                    <label for="cust_location">Customer Location</label>
                                                </div>
                                                <div class="col-6 col-md-8 text-left">
                                                    <span>{{$order_details[0]['location']}}</span>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-6 col-md-4">
                                                    <label for="pick_up_from">Pick up From</label>
                                                </div>
                                                <div class="col-6 col-md-8 text-left">
                                                    <span>See Table below</span>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-6 col-md-4">
                                                    <label for="pick_up_from_address">Address</label>
                                                </div>
                                                <div class="col-6 col-md-8 text-left">
                                                    <span>See Table Below</span>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-6 col-md-4">
                                                    <label for="Drop_at">Drop At</label>
                                                </div>
                                                <div class="col-6 col-md-8 text-left">
                                                    <span>Customer</span>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-6 col-md-4">
                                                    <label for="date">Date</label>
                                                </div>
                                                <div class="col-6 col-md-8 text-left">
                                                    {{-- <input class="form-control" type="date" onkeydown="return false" name="del_date" id="del_date" value="{{date('Y-m-d',strtotime($order_details[0]['DelDate']))}}"min="{{date('Y-m-d',strtotime(date('Y',strtotime($order_details[0]['created_at'])).'-'.date('m',strtotime($order_details[0]['created_at'])).'-'.'01'))}}"> --}}
                                                    <input class="form-control" type="date" onkeydown="return false" name="del_date" id="del_date" value="{{date('Y-m-d',strtotime($order_details[0]['DelDate']))}}">
                                                    {{-- <span>{{date('m-d-Y',strtotime($order_details[0]['DelDate']))}}</span> --}}
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-6 col-md-4">
                                                    <label for="amount">Amount</label>
                                                </div> 
                                                <div class="col-6 col-md-8 text-left">
                                                    <span>{{$order_details[0]['TotalAmt']}}</span>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-6 col-md-4">
                                                    <label for="amount_to_be">Amount To Be</label>
                                                </div>
                                                <div class="col-6 col-md-8 text-left">
                                                    <span>Collect</span>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="eqipments">Equipment Required*</label>
                                                </div>
                                            </div>
                                                <div class="row form-group table-responsive jim-table-responsive">
                                                    <table class="table table-bordered " id="equipment_table">
                                                        <thead>
                                                            <th>Sr. No.</th>
                                                            {{-- <th>Delivery Date</th> --}}
                                                            <th>Equipment Name</th>
                                                            <th>Qty</th>
                                                            <th>Deposite</th>
                                                            <th>Offered Rent</th>     
                                                            <th>Transport</th>
                                                        <th>Pick Up From</th>
                                                        <th>Warehouse Address</th>
                                                        </thead>
                                                        <tbody class="table-body">
                                                            <?php
                                                                $count = 0;
                                                                for($i=0; $i<count($product_details); $i++) 
                                                                {
                                                                    $count++;
                                                            ?>
                                                                    <tr id="{{$count}}">
                                                                        <td data-label="Sr.No.">{{$count}}</td>
                                                                        {{-- <td>{{$product_details[$i]['creation_date']}}</td> --}}
                                                                        <td data-label="Equipment">{{$product_details[$i]['product_name']}}</td>
                                                                    <input type="hidden" name="line_item_1[]" value="{{$product_details[$i]['product_name']}}">
                                                                        <td data-label="Qty">{{$product_details[$i]['product_qty']}}</td>
                                                                        <td data-label="Deposit">{{$product_details[$i]['product_deposite']}}</td>
                                                                        <td data-label="Rent">{{$product_details[$i]['product_rent']}}</td>
                                                                        <td data-label="Transport">{{$product_details[$i]['transport']}}</td>
                                                                    <td data-label="Pickup From">{{$product_details[$i]['vendor_name']}}</td>
                                                                    <td data-label="Warehouse">{{$product_details[$i]['warehouse_name'].', '.$product_details[$i]['warehouse_area'].', '.$product_details[$i]['warehouse_city']}}</td>
                                                                    </tr>
                                                            <?php   
                                                                } 
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            <div class="row form-group">
                                                <div class=" col-md-4">
                                                    <label for="invoice">Invoice</label>
                                                </div>
                                                <div class=" col-md-8 text-left">
                                                    <input type="radio" name="invoice_type" id="softcopy" value="Softcopy" checked="checked"> <label for="softcopy" class="form-label">Softcopy</label>
                                                    <input type="radio" name="invoice_type" id="hardcopy" value="Hardcopy"> <label for="hardcopy" class="form-label">Hardcopy</label>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class=" col-md-4">
                                                    <label for="payment_mode">Payment Mode</label>
                                                </div>
                                                <div class=" col-md-8 text-left">
                                                    <input type="radio" name="payment_mode" id="Online" value="Online" checked="checked"> <label for="Online" class="form-label">Online</label>
                                                    <input type="radio" name="payment_mode" id="cash" value="Cash"> <label for="cash" class="form-label">Cash</label>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class=" col-md-4">
                                                    <label for="travel">Travel</label>
                                                </div>
                                                <div class=" col-md-8 text-left">
                                                    <input type="radio" name="travel" id="local" value="Local" checked="checked"> <label for="local" class="form-label">Local</label>
                                                    <input type="radio" name="travel" id="rikshaw" value="Rikshaw"> <label for="rikshaw" class="form-label">Rikshaw</label>
                                                    <input type="radio" name="travel" id="bike" value="Bike"> <label for="bike" class="form-label">Bike</label>
                                                    <input type="radio" name="travel" id="tempo" value="Tempo"> <label for="tempo" class="form-label">Tempo</label>
                                                </div>
                                            </div>
                                            {{-- <div class="row container">
                                                <div class="col-md-10">
                                                    <div class="card card-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <label for="">Floor wise labour charges:</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="number" class="form-control form-control-sm labourCal" name="floor_wise_labour_charges" id="floor_wise_labour_charges" value="{{$order_details[0]['floor_wise_labour_charges']}}">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <label for="">Floor No :</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="number" class="form-control form-control-sm labourCal" name="floor_no" id="floor_no" value="{{$order_details[0]['floor_no']}}">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <label for="">Labour Charges :</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="number" class="form-control form-control-sm" name="labour_charges" id="labour_charges" value="{{$order_details[0]['labour_charges']}}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> --}}
                                            <br>
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
        <i class="fas fa-angle-up">dfd</i>
    </a>
    </div>	   
    @section('script')    
    <script>
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
        //     if(get_val!='No Helper')
        //     {
        //         $('#helpers option[value="No Helper"]').attr("selected",false);
        //         //$("option:selected").removeAttr("selected");
        //         $('#helpers').selectpicker('refresh');
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
        // $('.labourCal').on('input',function(){
        //     let floor_w_c = $('#floor_wise_labour_charges').val();
        //     let floor_no = $('#floor_no').val();
        //     let total = floor_w_c*floor_no;
        //     $('#labour_charges').val(total);
        // })
	</script>
    @endsection

    </body>
</html>