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
                        <div class="card card-primary">
                            <div class="card-header text-center" style="background-color: #345bcc; color: white;">
                                <span><b>Assign Delivery Boy</b></span>
                            </div>
                            <div class="card-body">
                                <form class="form" method="POST" action="<?php echo url('/');?>/assign_deliveryBoy">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="order_id" value="{{$order_details[0]['order_id']}}">
                                    <input type="hidden" name="lead_id" value="{{$order_details[0]['lead_id']}}">
                                    <input type="hidden" name="name" value="{{$order_details[0]['shipping_first_name']}}">
                                    <input type="hidden" name="mobileno" value="{{$order_details[0]['mobileno']}}">
                                    <input type="hidden" name="del_date" value="{{$order_details[0]['DelDate']}}">
                                    <div class="row">
                                        
                                        <div class="col-md-10">
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="delivery_type">Delivery Type</label>
                                                </div>
                                                <div class="col-md-8 text-left">
                                                    <input type="radio" name="delivery_type" id="delivery" value="Delivery" checked="checked"> <label for="delivery">Delivery</label>
                                                    <input type="radio" name="delivery_type" id="pickup" value="Pickup" disabled> <label for="pickup">Pickup</label>
                                                    <input type="radio" name="delivery_type" id="collection" value="Collection" disabled> <label for="collection">Collection</label>
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
                                                    <label for="del_assigned_to">Assigned To</label><br>
                                                    <div class="form-group">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="self_pick" id="self_pick" @if($self_pickup == 'pickup'){{'checked'}}@endif>
                                                            <label class="form-check-label" for="self_pick">
                                                                Customer self pickup
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <select name="del_assigned_to" title="Select Delboy" class="selectpicker form-control" data-size="5" id="del_assigned_to" @if($self_pickup == 'pickup'){{'disabled'}}@endif required>
                                                        {{-- <option value="Pending" selected>Pending</option> --}}                                                        
                                                            @foreach($delboys as $delboy)
                                                                <option value="{{$delboy['username']}}"@if (session('selected_delboy') == $delboy['username']){{"selected"}}@endif>{{$delboy['username']}}</option>
                                                            @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="del_assigned_to">Helpers</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <select class="selectpicker helpers form-control" data-size="5" name="helpers[]" id="helpers" data-live-search="true" multiple="multiple" required @if($self_pickup == 'pickup'){{'disabled'}}@endif required>
                                                        <option value="No Helper" selected>No Helper</option>
                                                        {{!$helpers = json_decode($order_details[0]['helpers'])}}
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
                                            {{-- <div class="row form-group">
                                                <div class="col-6 col-md-4">
                                                    <label for="pick_up_from">Pick up From</label>
                                                </div>
                                                <div class="col-6 col-md-8 text-left">
                                                    <span>Vendors Here</span>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-6 col-md-4">
                                                    <label for="pick_up_from_address">Address</label>
                                                </div>
                                                <div class="col-6 col-md-8 text-left">
                                                    <span>Warehouse Address Here</span>
                                                </div>
                                            </div> --}}
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
                                                    <span>{{date('Y-m-d')}}</span>
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
                                                <div class="col-6 col-md-4">
                                                    <label for="eqipments">Equipment Required*</label>
                                                </div>
                                            </div>
                                            <div class="row table-responsive jim-table-responsive">
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
                                            <div class="row container">
                                                <div class="col-md-10">
                                                    <div class="card card-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <label for="">Floor wise labour charges:</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="number" class="form-control form-control-sm labourCal" name="floor_wise_labour_charges" id="floor_wise_labour_charges">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <label for="">Floor No :</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="number" class="form-control form-control-sm labourCal" name="floor_no" id="floor_no">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <label for="">Labour Charges :</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="number" class="form-control form-control-sm" name="labour_charges" id="labour_charges">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-12">
                                                    <center>
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" id="sendSms" name="sendSms" value='send' checked>
                                                            <label class="custom-control-label" for="sendSms">Send Delivery Sms</label>
                                                        </div>
                                                    </center>
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
        $('#del_assigned_to').on('change',function()
        {
            var del_boy = $(this).val();
            $('#helpers option').attr("disabled",false);
            $("#helpers option[value='"+del_boy+"']").prop("selected",false);
            $("#helpers option[value='"+del_boy+"']").attr("disabled",true);
            $('#helpers').selectpicker('refresh');
        });
        //var helperBoys = $('#helpers').find(":selected").val();
        $(document).ready(function(){
           
        });

        //var selected_nohelp = $('#helpers').val();
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
        //             $("#helpers option[value='"+get_val[i]+"']").prop("selected",false);
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
                $(this).selectpicker('refresh');
            }
        });
        
	</script>
    @endsection

    </body>
</html>