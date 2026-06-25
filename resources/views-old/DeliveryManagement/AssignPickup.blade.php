@extends('header_and_sidebar')
{{-- @extends('new-sidebar') --}}


@section('styles')
    <style>
        .card-body
        {
            color: black;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-header text-center" style="background-color: #345bcc; color: white;">
            <h5>Pickup Order</h5>
        </div>
        <div class="card-body">
            <form class="form" method="POST" action="<?php echo url('/');?>/assign_pickup_delboy_post">
                {{ csrf_field() }}
                <input type="hidden" name="pickup_order_id" id="pickup_order_id" value="{{$order_details[0]->order_id}}">
                <input type="hidden" name="name" value="{{$order_details[0]->shipping_first_name}}">
                <input type="hidden" name="mobileno" value="{{$order_details[0]->mobileno}}">
                <input type="hidden" name="del_date" value="{{$order_details[0]->DelDate}}">
                <input type="hidden" name="prod_name" value="{{$product_details[0]->product_name}}">
                <h5 class="text-center">Basic Details</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <span class="heading"><b>Name: </b></span><span>{{$order_details[0]->shipping_first_name}}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <span class="heading"><b>Contact: </b></span><span>{{$order_details[0]->mobileno}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <span class="heading"><b>Address: </b></span><span>{{$order_details[0]->fulldetails}}</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <span class="heading"><b>Location: </b></span><span>{{$order_details[0]->location}}</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <span class="heading"><b>Amount: </b></span><span>{{$order_details[0]->TotalAmt}}</span>
                    </div>
                    <div class="col-md-6">
                        <span class="heading"><b>Amount To be: </b></span><span>{{"Collect"}}</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <span class="heading"><b>Payment Mode: </b></span> 
                        <input type="radio" name="payment_mode" id="Online" value="Online" checked="checked"> <label for="Online" class="form-label">Online</label>
                        <input type="radio" name="payment_mode" id="cash" value="Cash"> <label for="cash" class="form-label">Cash</label>
                        <input type="radio" name="payment_mode" id="both" value="Both"> <label for="both" class="form-label">Both</label>
                    </div>
                    <div class="col-md-6">
                        <div class="row cash_online_div fade">
                            <div class="input-group input-group-sm col-md-6">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="cash">Cash</span>
                                </div>
                                <input type="text" class="form-control form-control-sm" name="cash_amount" id="cash_amount" placeholder="Cash Amount">
                            </div>
                            <div class="input-group input-group-sm col-md-6">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="online">Online</span>
                                </div>
                                <input type="text" class="form-control form-control-sm" id="online_amount" name="online_amount" placeholder="Online Amount">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <span class="heading"><b>Invoice: </b></span> 
                        <input type="radio" name="invoice_type" id="softcopy" value="Softcopy" checked="checked"> <label for="softcopy" class="form-label">Softcopy</label>
                        <input type="radio" name="invoice_type" id="hardcopy" value="Hardcopy"> <label for="hardcopy" class="form-label">Hardcopy</label>
                    </div>
                    <div class="col-md-6">
                        <span class="heading"><b>Travel: </b></span> 
                        <input type="radio" name="travel" id="local" value="Local" checked="checked"> <label for="local" class="form-label">Local</label>
                        <input type="radio" name="travel" id="rikshaw" value="Rikshaw"> <label for="rikshaw" class="form-label">Rikshaw</label>
                        <input type="radio" name="travel" id="bike" value="Bike"> <label for="bike" class="form-label">Bike</label>
                        <input type="radio" name="travel" id="tempo" value="Tempo"> <label for="tempo" class="form-label">Tempo</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="pickup_date">Date</label>
                        <input type="date" class="form-control" name="pickup_date" id="pickup_date" value="{{date('Y-m-d')}}">
                    </div>
                </div>
                <hr>
                <h5 class="text-center">Assign Delivery Boy</h5>
                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="self_pick" id="self_pick">
                        <label class="form-check-label" for="self_pick">
                            Customer self Droped
                        </label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="del_assigned_to" class="form-label">Assigned To</label>
                    <select class="selectpicker" width="100%" title="Select Delboy" name="del_assigned_to" data-size="5" data-live-search="true" id="del_assigned_to" required>
                            @foreach($delboys as $delboy)
                                <option value="{{$delboy->username}}">{{$delboy->username}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="helpers" class="form-label">Helpers</label>
                    <select class="selectpicker helpers" width="100%" title="Select Helpers" multiple="multiple" data-size="5" data-live-search="true" name="helpers[]" id="helpers" required>
                            <option value="No Helper">No Helper</option>
                            @foreach($delboys as $delboy)
                                <option value="{{$delboy->username}}">{{$delboy->username}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <hr>
                <h5 class="text-center">Equipment Details</h5>
                <div class="table table-responsive jim-table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <th>Sr. No.</th>
                            <th>Equipment Name</th>
                            <th>Qty</th>
                            <th>Vendor</th>
                            <th>Warehouse</th>
                            <th>Collect Rent</th>
                            <th>Deposit</th>
                            <th>Transport</th>
                            <th>Drop Location</th>
                            <th>Comment</th>
                        </thead>
                        <tbody class="tbody">
                            @foreach($product_details as $key=>$product_detail)
                                <tr>
                                    <input type="hidden" name="order_details_id[]" id="order_details_id" value="{{$product_detail->id}}">
                                    <input type="hidden" name="order_id[]" id="order_id" value="{{$product_detail->order_id}}">
                                    <input type="hidden" name="pickup_main_id[]" id="pickup_main_id" value="{{$product_detail->pickup_main_id}}">
                                    <input type="hidden" name="vendor_id[]" id="vendor_id" value="{{$product_detail->vendor_id}}">
                                    <input type="hidden" name="vendor_product_rent[]" id="vendor_product_rent" value="{{$product_detail->vendor_rent}}">
                                    <input type="hidden" name="vendor_rented_product_id[]" id="vendor_rented_product_id" value="{{$product_detail->rented_product_id}}">
                                    <input type="hidden" name="product_id[]" id="product_id" value="{{$product_detail->product_id}}">
                                    <input type="hidden" name="product_qty[]" id="product_qty" value="{{$product_detail->product_qty}}">
                                    <input type="hidden" name="pickup_date[]" id="pickup_date" value="{{$product_detail->pickup_date}}">
                                    <td data-label="srno">{{$key+1}}</td>
                                    <td data-label="Product Name">{{$product_detail->product_name}}</td>
                                    <td data-label="Quantity">{{$product_detail->product_qty}}</td>
                                    <td data-label="Vendor">{{$product_detail->registered_name}}</td>
                                    <td data-label="Warehouse">{{$product_detail->wh_name.', '.$product_detail->wh_area.', '.$product_detail->wh_city}}</td>
                                <td data-label="Collect Rent"><input type="text" name="collect_rent" id="collect_rent" class="form-control form-control-sm" value="{{$product_detail->cash_amount}}" disabled></td>
                                {{-- <td data-label="Deposit">{{$product_detail->product_deposite}}</td> --}}
                                <td>
                                    <input type="hidden" name="hidden_deposit[]" id="hidden_deposit{{$key}}" class="form-control form-control-sm" value="{{$product_detail->product_deposite}}">
                                    <input type="text" name="deposit[]" id="deposit{{$key}}" class="form-control form-control-sm deposit" data-id="{{$key}}" value="{{$product_detail->product_deposite}}" required readonly>
                                </td>
                                {{-- <td data-label="Collect Rent">{{$product_detail->cash_amount}}</td> --}}
                                <td data-label="Transport"><input type="text" class="form-control form-control-sm" name="transport[]" id="transport{{$key}}" value="0" required></td>
                                <td data-label=" Drop Location">
                                    <input type="hidden" name="group_name[]" id="group_name{{$key}}" value="">
                                    <select class="form-control form-control-sm selectpicker show-tick Drop_at" data-size="5" name="vendor_warehouse_id[]" data-id="{{$key}}" id="vendor_warehouse_id{{$key}}" title="Drop Location" data-live-search="true" required
                                        data-width="">
                                            <optgroup label="Customer Location">
                                                @foreach($product_detail->customer_address as $cust_addr)
                                                    <option value="{{$cust_addr->cust_id}}">{{$cust_addr->customer_name}}-{{$cust_addr->address_line_1}}, {{$cust_addr->landmark}}, {{$cust_addr->area}}, {{$cust_addr->city}}</option>
                                                @endforeach
                                            </optgroup>
                                            <optgroup label="Virtual Warehouse">
                                                @foreach($product_detail->q5c_warehouse_details as $q5c_warehouse_detail)
                                                    <option value="{{$q5c_warehouse_detail->id}}">{{$q5c_warehouse_detail->wh_name}}-{{$q5c_warehouse_detail->wh_area}},{{$q5c_warehouse_detail->wh_city}}</option>
                                                @endforeach
                                            </optgroup>
                                            <optgroup label="Vendor Warehouse">
                                                @foreach($product_detail->vendor_warehouse_details as $vendor_warehouse_detail)
                                                    <option value="{{$vendor_warehouse_detail->id}}">{{$vendor_warehouse_detail->wh_name}}-{{$vendor_warehouse_detail->wh_area}},{{$vendor_warehouse_detail->wh_city}}</option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                    </td>
                                <td><textarea class="form-control form-control-sm" name="comment[]" id="comment{{$key}}"></textarea></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <center>
                    <button type="submit" class="btn btn-outline-success" id="submit-form" name="submit-form" value="submit">Submit</button>
                    <button type="reset" class="btn btn-outline-secondary" id="reset-form" name="reset-form" value="reset">Reset</button>
                </center>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>

        $(".deposit").on("input",function(){
            let id = $(this).data("id");
            let act_deposit = $("#hidden_deposit"+id).val();
            let curr_deposit = $(this).val();
            if(parseInt(curr_deposit)>parseInt(act_deposit)){
                alert("Amount should be less than or equal to:"+act_deposit);
                $(this).val(act_deposit);
            }
        });

        $("#pickup_date").change(function(){
            let pickup_date = $(this).val();
            let order_id = $("#pickup_order_id").val();
            $.ajax({
                type:"GET",
                url:"{{url('/')}}/getConvCustomers/"+order_id+'/'+pickup_date,
                cache:false,
                success:function(data){
                    // console.log(data.customer_details.length);
                    for(let i = 0; i<data.customer_details.length; i++)
                    {

                        // console.log(i);
                        $("#vendor_warehouse_id"+i)
                        .find('option')
                        .remove()
                        .end();                        
                        if(data.customer_details[i].length != 0)
                        {                   
                            customer_detail = data.customer_details[i].length
                            for(var j = 0; j < customer_detail; j++)
                            {
                                $("#vendor_warehouse_id"+i).find('optgroup[label="Customer Location"]').append("<option value='"+data.customer_details[i][j].cust_id+"'>"+data.customer_details[i][j].customer_name+"-"+data.customer_details[i][j].address_line_1+","+data.customer_details[i][j].landmark+","+data.customer_details[i][j].area+','+data.customer_details[i][j].city+"</option>");
                            }
                            $("#vendor_warehouse_id"+i).append("</optgroup>");
                        }
                        if(data.q5c_warehouse_details[i].length != 0)
                        {            
                            q5c_warehouse_detail = data.q5c_warehouse_details[i].length
                            for(var j = 0; j < q5c_warehouse_detail; j++)
                            {
                                $("#vendor_warehouse_id"+i).find('optgroup[label="Virtual Warehouse"]').append("<option value='"+data.q5c_warehouse_details[i][j].id+"'>"+data.q5c_warehouse_details[i][j].wh_name+"-"+data.q5c_warehouse_details[i][j].wh_area+","+data.q5c_warehouse_details[i][j].wh_city+"</option>");
                            }
                            $("#vendor_warehouse_id"+i).append("</optgroup>");
                        }
                        if(data.vendor_warehouse_details[i].length != 0)
                        {
                            vendor_warehouse_detail = data.vendor_warehouse_details[i].length
                            for(var j = 0; j < vendor_warehouse_detail; j++)
                            {
                                $("#vendor_warehouse_id"+i).find('optgroup[label="Vendor Warehouse"]').append("<option value='"+data.vendor_warehouse_details[i][j].id+"'>"+data.vendor_warehouse_details[i][j].wh_name+"-"+data.vendor_warehouse_details[i][j].wh_area+","+data.vendor_warehouse_details[i][j].wh_city+"</option>");
                            }
                            $("#vendor_warehouse_id"+i).append("</optgroup>");
                        }
                        $('#vendor_warehouse_id'+i).selectpicker('refresh');
                    }
                }
            });
        });
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
        $(document).ready(function(){
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
            $('input:radio[name=payment_mode]').change(function() {
                if (this.value == 'Both') {
                    $('.cash_online_div').removeClass('fade');
                }
                else{
                    $('.cash_online_div').addClass("fade");
                }
            });
        });
        $('.table-responsive').on('show.bs.dropdown', function () {
        $('.table-responsive').css( "overflow", "inherit" );
        });

        $('.table-responsive').on('hide.bs.dropdown', function () {
            $('.table-responsive').css( "overflow", "auto" );
        })
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