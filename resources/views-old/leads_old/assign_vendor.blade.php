<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Inquiry :  Assign Vendor</title>
    <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script>
    <style>                    
            .bootstrap-select > .dropdown-toggle[title='Select vendor'],
            .bootstrap-select > .dropdown-toggle[title='Select vendor']:hover,
            .bootstrap-select > .dropdown-toggle[title='Select vendor']:focus,
            .bootstrap-select > .dropdown-toggle[title='Select vendor']:active { color: red; }
    </style>
</head>

<body id="page-top">	
<!-- Page Wrapper -->

@extends('header_and_sidebar')
    <?php if (session('role') == 'admin'){?>
        @section('Admin')
            @parent
        @endsection
        @section('side_users')
            @parent
        @endsection

    <?php } else{?>
        @section('Admin')
            @stop
    
        @section('side_users')
            @stop
    
    <?php } ?>
    
    @section('content')
    <div class="container">
        <br>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="background-color: #337ab7; color: white;">
                        <center>
                            <b>Assign Vendor</b>
                        </center>
                    </div>
                    <form class="form" action="<?php echo url('/')?>/generate_order" method="post">
                    {{ csrf_field() }}
                        <div class="card-body">
                            <h3> Lead Details </h3>
                            <hr>
                            <div class="row">
                                <div class="col-md-2">
                                    <label for="customer_name">Customer Name : </label>
                                </div>
                                <div class="col-md-4">
                                    <span><?php echo $lead_details[0]['customer_name'];?></span>
                                </div>
                                <div class="col-md-2">
                                    <label for="customer_name">Customer Address : </label>
                                </div>
                                <div class="col-md-4">
                                    <span><?php echo $lead_details[0]['address_line_1'].', '.$lead_details[0]['address_line_2'].',<br> '.$lead_details[0]['landmark'].', '.$lead_details[0]['area'].', '.$lead_details[0]['city'].', '.$lead_details[0]['pincode'].', '.$lead_details[0]['state'].', '.$lead_details[0]['country'];?></span>
                                </div>
                            </div>
                            <hr>
                            <br>
                            <center>
                                <h3> Vendor Selection </h3>
                            </center>

                            <div class="row">
                                &emsp;
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="assign"
                                        id="All"
                                        value="All"
                                    />
                                    <label class="form-check-label"  for="All" id="All"> <strong>All</strong> </label>
                                </div>
                                &emsp;
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="assign"
                                        id="Individual"
                                        value="Individual"
                                        checked
                                    />
                                    <label class="form-check-label" for="Individual" id="Individual"> <strong>Individual</strong> </label>
                                </div>
                            </div>
                            <hr>


                            <!-- <div class="row" >
                                <div class="col">
                                    <table id="records" class="table table-bordered table-responsive" style="width:100%;">    
                                    </table>
                                </div>
                            </div> -->
                            

                            <div class="row ">
                                <div class="col">
                                    <table id="records" class="table table-bordered table-responsive" style="width:100%; ">
                                        <thead>
                                            <tr>
                                                <th>Require Equipment &emsp; &emsp;&emsp;&emsp;&emsp;</th>
                                                <th>Select vendor </th>
                                                <th>Product Price</th>
                                                <th>Product Details</th>
                                                <th>Warehouse Details</th>
                                            </tr>
                                        </thead>
                                        <tbody class="tbody">
                                            <?php
                                                $products = json_decode($lead_details[0]['equipment_requirement']);
                                                $products_id = json_decode($lead_details[0]['equipment_id']);
                                                $count =0;
                                                for ($i=0; $i <count($products) ; $i++) 
                                                { 
                                                    
                                                    echo "<tr class='rows' data-count=".$count.">";
                                                        echo "<td>";?> <label for="" id="req_eq<?php echo $count;?>" name="req_eq[]" value="<?php echo $products_id[$i];?>"><?php echo $products[$i];?></label> <input type="hidden" name="req_eq_hidden" id="req_eq_hidden<?php echo $count;?>" value="<?php echo $products_id[$i];?>"> <?php echo"</td>";
                                                        echo "<td>";
                                                        ?>
                                                            <select class="selectpicker form-control" id="vendor<?php echo $count;?>" data-count="<?php echo $count;?>" title="Select vendor" name="vendor[]" data-live-search="true" required>
                                                        <?php
                                                            foreach ($vendor_details as $vendor) 
                                                            {
                                                                ?>                                                                  
                                                                    <option  value="<?php echo $vendor['id'];?>"><?php echo $vendor['registered_name'];?></option>
                                                                <?php
                                                            }
                                                        ?>
                                                            </select>
                                                        <?php
                                                        echo "</td>";

                                                        echo "<td>";?> <input type="text" class="form-control" name="product_price[]" id="product_price<?php echo $count;?>" placeholder="Product Price:  "  readonly> <?php echo "</td>";
                                                        echo "<td>";?> <input type="text" class="form-control" name="product_details[]" id="product_details<?php echo $count;?>" placeholder="Product Details:  "  readonly> <?php echo "</td>";
                                                        echo "<td>";?> <input type="text" class="form-control" name="warehouse_details[]" id="warehouse_details<?php echo $count;?>" placeholder="Warehouse Details:  "  readonly> <?php echo "</td>";
                                                    echo "</tr>"; 
                                                    $count =$count+1;
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                    <input type="hidden" name="row_ct" id="row_ct" value="<?php echo $count;?>">
                                    <center>
                                        <input class="submit btn btn-primary" type="submit" id="submit" name="submit" value="submit">
                                        <input type="reset" class="btn btn-secondary" id="reset" name="reset" value="Reset">
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
            $(document).ready(function() {
                $("input[name='assign']").click(function() {
                    if ($("#All").is(":checked")) {

                        var count = $('#row_ct').val();
                        //alert(count);
                    
                        for(var i=count; i>0; i--)
                        {
                            $("#vendor"+i).attr('style',"display:none;");
                        }
                        // for(var i=0; i<=count; i++)
                        // {
                        //     var equipment = $('#req_eq'+i)
                        //     equipments.push(equipment);
                        //     var equipment_NAME = $('#req_eq'+i).text();
                        //     alert(equipment_NAME);
                        // };
                    
                        $('#records tr').click(function() {  
                            
                            var count = this.dataset.count;
                            //alert(count);
                            $("#vendor"+count).on("change",function() {
                                var count_eq = $('#row_ct').val();
                                var equipments = [];
                                for(var i=0; i<=count_eq; i++)
                                {
                                    //var equipment = $('#req_eq'+i).val();
                                    var equipment = $('#req_eq_hidden'+i).val();
                                    //alert()
                                    equipments.push(equipment);
                                }; 
                                //alert(equipments);
                                var slct_vdr_id = $("#vendor"+count).val();
                                //alert(count);
                                var vendor_id = (slct_vdr_id);
                                //alert(JSON.stringify(equipments));
                                var dataString_equipment = JSON.stringify(equipments);
                                //alert(equipments);
                                //alert(dataString);
                            
                                var dataString = ({_token:"{{ csrf_token() }}", vendor_id:""+vendor_id, equipments:""+dataString_equipment});
                                $.ajax({  //create an ajax request to display.php
                                    type: "POST",
                                    url: "<?php echo url('/'); ?>/select_vendor_all",       
                                    data: dataString,
                                    cache:false,
                                    success: function (data)
                                    {
                                        //alert(data);
                                        //alert('hisfg');
                                        
                                        var obj = jQuery.parseJSON(data);
                                        //alert(obj.product_rent);
                                        for(var i=0; i<=count_eq; i++)
                                        {
                                            $('#product_price'+i).val(obj.product_rent[i]); 
                                            $('#product_details'+i).val(obj.product_details[i]);
                                            $('#warehouse_details'+i).val(obj.warehouse_details[i]);
                                        }; 
                                        // var i=0;
                                        // //alert(obj.product_details);
                                        // $('#product_price'+count).val(obj.product_price); 
                                        // $('#product_details'+count).val(obj.product_details);
                                    }
                                });
                            });
                        });
                    }   

                    if ($("#Individual").is(":checked")) {
                        $(function() {
                            var count = $('#row_ct').val();
                            //  alert(count);
                            for(var i=count; i>0; i--)
                            {
                                $("#vendor"+i).attr('style',"display:show;");
                            }
                            $('#records tr').click(function() {    
                                var count = this.dataset.count;
                                
                                //alert(count);
                                $("#vendor"+count).on("change",function() { 
                                    //var equipment = $('table tr td').text();
                                    var equipment = $('#req_eq_hidden'+count).val();
                                    //alert(equipment);
                                    var slct_vdr_id = $("#vendor"+count).val();
                                    //alert(count);
                                    var dataString = (slct_vdr_id);
                                    var dataString_equipment = (equipment);
                                    //alert(dataString);
                                    //alert(dataString);
                                    $.ajax({  //create an ajax request to display.php
                                        type: "GET",
                                        url: "<?php echo url('/'); ?>/select_vendor/"+dataString+"/"+dataString_equipment,       
                                        success: function (data)
                                        {
                                            //alert(data);
                                            //alert('hisfg');
                                            
                                            var obj = jQuery.parseJSON(data);
                                            //alert(obj.product_details);
                                            var i=0;
                                            //alert(obj.product_details);
                                            $('#product_price'+count).val(obj.product_price); 
                                            $('#product_details'+count).val(obj.product_details);
                                            $('#warehouse_details'+count).val(obj.warehouse_details);
                                        }
                                    });
                                });
                            });
                        });
                    } 
                });
            });

        </script>                                                         

    @endsection
    
</html>