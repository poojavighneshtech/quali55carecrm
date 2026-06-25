<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Reassign Vendor</title>
    @section('header')
    @endsection

</head>

<body id="page-top">	
<!-- Page Wrapper -->

@extends('header_and_sidebar')
    
    @section('content')
    <div class="container">
        <br>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="background-color: #337ab7; color: white;">
                        <center>
                            <b>Reassign Vendor</b>
                        </center>
                    </div>
                    <form class="form" id="assign_vendor" action="<?php echo url('/')?>/reassign_vendor_post" method="post" >
                    {{ csrf_field() }}
                        <div class="card-body">
                            <div class="row ">
                                <div class="col">
                                    <table id="records" class="table table-bordered table-responsive" style="width:100%; ">
                                        <thead>
                                            <tr>
                                                <th>Sr.No.</th>
                                                <th>Sale/Rental</th>
                                                <th>Del Date</th>
                                                <th>Require Equipment</th>
                                                <th>Quantity</th>
                                                <th>Select vendor</th>
                                                <th>Select Warehouse</th>
                                                <th>Select Brand</th>
                                                <th>Select Batch</th>
                                                <th>Vendor Product Rent/Price</th>
                                                {{-- <th>Product Details</th> --}}
                                                <th>Deposit</th>
                                                <th>Product Rent/Sale rate</th>
                                                <th>Offered Rent/Sale price</th>
                                                <th>Profit Margin</th>
                                                <th>transport</th>
                                            </tr>
                                        </thead>
                                        <tbody class="tbody">
                                            @php($count =0)
                                            @php($i = 0)
                                            @foreach($product_details as $product_detail)
                                                <tr class="rows"  data-count="{{$count}}">
                                                    <td>{{$i+1}}</td>
                                                    <td>
                                                        @if(isset($product_detail['sale_rental']))
                                                            <span name="sale_rental[]" id="sale_rental{{$count}}">{{$product_detail['sale_rental']}}</span>
                                                            <input type="hidden" name="sale_rental_hidden[]" id="sale_rental_hidden{{$count}}" value="{{$product_detail['sale_rental']}}">
                                                            <input type="hidden" name="order_id" id="order_id" value="{{$product_detail['order_id']}}">
                                                            <input type="hidden" name="order_details_id" id="order_details_id" value="{{$product_detail['id']}}">
                                                            <input type="hidden" name="lead_id" id="lead_id" value="{{$product_detail['lead_id']}}">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(isset($product_detail['DelDate']))
                                                            <span name="del_date[]" id="del_date{{$count}}" value="{{$product_detail['DelDate']}}">{{$product_detail['DelDate']}}</span></td>
                                                            <input type="hidden" name="del_date" id="del_date" value="{{$product_detail['DelDate']}}">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span id="req_eq{{$count}}" name="req_eq[]" value="{{$product_detail['product_id']}}">{{$product_detail['product_name']}}</span>
                                                        <input type="hidden" name="req_eq_hidden[]" id="req_eq_hidden{{$count}}" value="{{$product_detail['product_id']}}">
                                                    </td>
                                                    <td>
                                                        @if(isset($product_detail['product_qty']))
                                                            <span name="eq_quantity[]" id="eq_quantity{{$count}}">{{$product_detail['product_qty']}}</span>
                                                            <input type="hidden" name="eq_quantity_hidden[]" id="eq_quantity_hidden{{$count}}" value="{{$product_detail['product_qty']}}">
                                                        @endif
                                                    </td>                                                
                                                    <td id='select_vendor{{$i}}'>
                                                        <select class="selectpicker" width="fit" title="Select Vendor" name="vendors[]" id="vendors{{$count}}" data-live-search="true" width="100%" required="true">
                                                            @foreach($vendor_details as $vendor) 
                                                            <option value="{{$vendor['id']}}">{{$vendor['registered_name']}}</option>
                                                                {{-- <option value="" disabled>No Vendors Found</option> --}}
                                                            @endforeach
                                                            
                                                        </select>
                                                    </td>
                                                    <td id='select_warehouse{{$i}}'>
                                                        <select class="selectpicker" width="fit" title="Select Warehouse" name="warehouses[]" id="warehouses{{$count}}" data-live-search="true" width="100%" required="true">
                                                            <option value="" disabled>Select Vendor First</option>
                                                        </select>
                                                        <input type="hidden" name="vendor_product_id[]" id="vendor_product_id{{$count}}">
                                                    </td>
                                                    <td id='select_brand{{$i}}'>
                                                        <select class="selectpicker" width="fit" title="Select Brand" name="brands[]" id="brands{{$count}}" data-live-search="true" width="100%" required="true">
                                                            <option value="" disabled>Select Warehouse First</option>
                                                        </select>
                                                    </td>
                                                    <td id='select_batch{{$i}}'>
                                                        @if($product_detail['sale_rental'] == "Rental")
                                                        <select class="selectpicker" width="fit" title="Select Batch" name="batches[]" id="batches{{$count}}" data-live-search="true" width="100%" required="true">
                                                            <option value="" disabled>Select Brand First</option>
                                                        </select>
                                                        @else
                                                            <span><b> - </b></span>
                                                            <input type="hidden" name="batches[]" value="-">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span name="prod[]" id="prod{{$count}}">-</span>
                                                        {{-- <span name="product_price_span[]" id="product_price_span{{$count}}>">-</span> --}}
                                                        <input type="hidden" name="product_price[]" id="product_price" value="0">
                                                        {{-- <input type="text" class="form-control" name="product_price[]" id="product_price{{$count}}" placeholder="Vendor Rent:" readonly> --}}
                                                    </td>
                                                    {{-- <td>
                                                        <input type="hidden" class="form-control" name="product_details[]" id="product_details{{$count}}" placeholder="Product Details:" readonly>
                                                        <span name="product_details_span[]" id="product_details_span{{$count}}">-</span>
                                                    </td> --}}
                                                    <td>
                                                        @if(isset($product_detail['product_deposite']))
                                                            <span name="deposite[]" id="deposite{{$count}}>">{{$product_detail['product_deposite']}}</span>
                                                            <input type="hidden" name="deposite[]" id="deposite" value="{{$product_detail['product_deposite']}}">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(isset($product_detail['actual_product_rent']))
                                                            <span name="product_rent[]" id="product_rent{{$count}}">{{$product_detail['actual_product_rent']}}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(isset($product_detail['product_rent']))
                                                            <span name="offered_rent[]" id="offered_rent1{{$count}}">{{$product_detail['product_rent']}}</span>
                                                            <input type="hidden" name="offered_rent[]" id="offered_rent{{$count}}" value="{{$product_detail['product_rent']}}">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span name="profit[]" id="profit{{$count}}">0(0%)</span>
                                                        <input type="hidden" name="profit_hidden[]" id="profit_hidden{{$count}}" value="0">
                                                    </td>
                                                    <td>
                                                        @if(isset($product_detail['transport']))
                                                            <span name="transport[]" id="transport{{$count}}">{{$product_detail['transport']}}</span>
                                                            <input type="hidden" name="transport[]" id="transport{{$count}}" value="{{$product_detail['transport']}}">
                                                        @endif  
                                                    </td>
                                                </tr>
                                                {{!$count++}}
                                            @endforeach
                                           
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
            $(document).ready(function(){
                // $(".selectpicker").on("change",function() {                                 
                //     var equipment = $('#req_eq_hidden').val();
                //     var slct_vdr_id = $(this).val();
                //     var dataString = (slct_vdr_id);
                //     var dataString_equipment = (equipment);
                //     $.ajax({
                //         type: "GET",
                //         url: "<?php echo url('/'); ?>/select_vendor/"+dataString+"/"+dataString_equipment,       
                //         success: function (data)
                //         {
                //             //alert(data);
                //             var obj = jQuery.parseJSON(data);                     
                //             var i=0;                        
                //             // $('#product_price').val(obj.product_price); 
                //             document.getElementById('product_price').innerHTML = obj.product_price;
                //             // $('#product_details').val(obj.product_details);
                //             document.getElementById('product_details').innerHTML = obj.product_details;
                //             // $('#warehouse_details').val(obj.warehouse_details);
                //             document.getElementById('warehouse_details').innerHTML = obj.warehouse_details;
                //             $('#warehouse_id').val(obj.warehouse_id);
                //         }
                //     });
                // });
                $('#records tr').click(function()
                {
                    var count = this.dataset.count;
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
                                        var warehouses = jQuery.parseJSON(data);
                                        //console.log(warehouses);
                                        var warehousesLength = warehouses.length;
                                        $("#warehouses"+count)
                                        .find("option")
                                        .remove()
                                        .end();
                                        for(var j = 0; j < warehousesLength; j++)
                                        {
                                            $("#warehouses"+count).append("<option value='"+warehouses[j].warehouse_id+"'>"+warehouses[j].wh_name+","+warehouses[j].wh_area+","+warehouses[j].wh_city+"</option>");
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
                                            $("#warehouses"+count).append("<option value='"+warehouses[j].warehouse_id+"'>"+warehouses[j].wh_name+","+warehouses[j].wh_area+","+warehouses[j].wh_city+"</option>");
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

                            $.ajax({
                                type: "GET",
                                url: "<?php echo url('/');?>/getDetails/"+product_id,
                                cache: false,
                                success: function(data)
                                {
                                    var details = jQuery.parseJSON(data);
                                    console.log(details);
                                    $("#vendor_product_id"+count).val(details[0].vendor_product_id);
                                    $("#prod"+count).text(details[0].product_rent);
                                    var prod_details_string = details[0].product_details;
                                    var short_str = prod_details_string.substring(0,50)+"...";
                                    //$("#product_details_span"+count).text(details[0].product_details);
                                    $("#product_details_span"+count).text(short_str);
                                }
                            });
                        });
                });
            });

            $('.table-responsive').on('show.bs.dropdown', function () {
            $('.table-responsive').css( "overflow", "inherit" );
            });

            $('.table-responsive').on('hide.bs.dropdown', function () {
                $('.table-responsive').css( "overflow", "auto" );
            })
        </script>                                                         
    @endsection
    
</html>