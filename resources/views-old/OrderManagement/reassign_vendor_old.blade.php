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
                                                <th>Del Date &emsp;&emsp;&emsp;&emsp;&emsp;</th>
                                                <th>Require Equipment</th>
                                                <th>Quantity</th>
                                                <th>Select vendor </th>
                                                <th>Vendor Product Rent</th>
                                                <th>Product Details</th>
                                                <th>Warehouse Details</th>
                                                <th>Deposite</th>
                                                <th>Product Rent</th>
                                            </tr>
                                        </thead>
                                        <tbody class="tbody">                                          
                                            <tr>
                                                <td>{{$product_details[0]['creation_date']}}<input type="hidden" name="order_id" value="{{$product_details[0]['id']}}"></td>
                                                <td>{{$product_details[0]['product_name']}}<input type="hidden" name="req_eq_hidden" id="req_eq_hidden" value="{{$product_details[0]['product_id']}}"></td>
                                                <td>{{$product_details[0]['product_qty']}}</td>
                                                <td>
                                                    <select class="selectpicker form-control" id="vendor" title="Select vendor" name="vendor" data-width="fit" data-live-search="true" required="true">
                                                        <option selected disabled>--Select-Vendor--</option>
                                                        <?php
                                                            foreach ($vendor_details as $vendor) 
                                                            {
                                                        ?>    
                                                            <option value="{{$vendor['id']}}">{{$vendor['registered_name']}}</option>
                                                        <?php
                                                            }
                                                        ?>
                                                    </select>
                                                </td>
                                                <td><span id="product_price">0</span></td>
                                                <td><span id="product_details">-</span></td>
                                                <td><span id="warehouse_details">-</span><input type="hidden" id="warehouse_id" name="warehouse_id"></td>
                                                <td><span id="deposite">{{$product_details[0]['product_deposite']}}</span></td>
                                                <td><span id="rent">{{$product_details[0]['product_rent']}}</span></td>
                                            </tr>
                                        </tbody>
                                    </table>                                    
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
                $(".selectpicker").on("change",function() {                                 
                    var equipment = $('#req_eq_hidden').val();
                    var slct_vdr_id = $(this).val();
                    var dataString = (slct_vdr_id);
                    var dataString_equipment = (equipment);
                    $.ajax({
                        type: "GET",
                        url: "<?php echo url('/'); ?>/select_vendor/"+dataString+"/"+dataString_equipment,       
                        success: function (data)
                        {
                            //alert(data);
                            var obj = jQuery.parseJSON(data);                     
                            var i=0;                        
                            // $('#product_price').val(obj.product_price); 
                            document.getElementById('product_price').innerHTML = obj.product_price;
                            // $('#product_details').val(obj.product_details);
                            document.getElementById('product_details').innerHTML = obj.product_details;
                            // $('#warehouse_details').val(obj.warehouse_details);
                            document.getElementById('warehouse_details').innerHTML = obj.warehouse_details;
                            $('#warehouse_id').val(obj.warehouse_id);
                        }
                    });
                });
            });
        </script>                                                         
    @endsection
    
</html>