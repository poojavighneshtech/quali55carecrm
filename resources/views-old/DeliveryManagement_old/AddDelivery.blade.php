<!DOCTYPE html>
<html lang="en">
    @extends('header_and_sidebar')
    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Delivery : Add New Delivery</title>
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
                                <span><b>Add New Delivery</b></span>
                            </div>
                            <div class="card-body"><!--style="min-height: 0; max-height: 5; overflow-y: scroll;"-->
                                <form class="form" method="POST" action="<?php echo url('/');?>/AddDelivery">
                                {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-md-2">
                                        </div>
                                        <div class="col-md-8">
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="delivery_type">Delivery Type</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="radio" name="delivery_type" id="delivery" value="Delivery" checked="checked"> <label for="delivery">Delivery</label>
                                                    <input type="radio" name="delivery_type" id="pickup" value="Pickup"> <label for="pickup">Pickup</label>
                                                    <input type="radio" name="delivery_type" id="collection" value="Collection"> <label for="collection">Collection</label>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="status">Status</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <select name="del_status" class="form-control" id="del_status">
                                                        <option value="Pending">Pending</option>
                                                        <option value="Assigned">Assigned</option>
                                                        <option value="Accepted">Accepted</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="del_assigned_to">Assigned To</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <select name="del_assigned_to" class="form-control" id="del_assigned_to">
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
                                                    <label for="name">Name</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" name="name" id="name">
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="contact_no">Mobile Number</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="text" oninput="numberOnly(this.id);" maxlength="10" class="form-control" name="contact_no" id="contact_no">
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="address">Address</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <textarea name="address" id="address" class="form-control"></textarea>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="cust_location">Customer Location</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" name="cust_location" id="cust_location">
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="pick_up_from">Pick up From</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <select name="pick_up_from" class="form-control" id="pick_up_from">
                                                        <option value="Quali55Care-Shop" selected>Quali55Care-Shop</option>
                                                        <option value="Customer">Customer</option>
                                                        <option value="Ronak Internationa">Ronak Internationa</option>
                                                        <option value="Aarya Internationa">Aarya Internationa</option>
                                                        <option value="Jupiter">Jupiter</option>
                                                        <option value="Misri Medical">Misri Medical</option>
                                                        <option value="Balaji Healthcare">Balaji Healthcare</option>
                                                        <option value="Fine Healthcare">Fine Healthcare</option>
                                                        <option value="Jayprakash">Jayprakash</option>
                                                        <option value="Maheshbhai">Maheshbhai</option>
                                                        <option value="Lifecare">Lifecare</option>
                                                        <option value="Oxylife">Oxylife</option>
                                                        <option value="Nidek">Nidek</option>
                                                        <option value="Jeegar Surgical">Jeegar Surgical</option>
                                                        <option value="Pritesh Surgical">Pritesh Surgical</option>
                                                        <option value="Vendor">Vendor</option>
                                                        <option value="Easy Care Masjid">Easy Care Masjid</option>
                                                        <option value="Other">Other</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="pick_up_from_address">Address</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <textarea name="pick_up_from_address" id="pick_up_from_address" class="form-control"></textarea>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="Drop_at">Drop At</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <select name="drop_at" class="form-control" id="drop_at">
                                                        <option value="Quali55Care-Shop">Quali55Care-Shop</option>
                                                        <option value="Customer" selected>Customer</option>
                                                        <option value="Ronak Internationa">Ronak Internationa</option>
                                                        <option value="Aarya Internationa">Aarya Internationa</option>
                                                        <option value="Jupiter">Jupiter</option>
                                                        <option value="Misri Medical">Misri Medical</option>
                                                        <option value="Balaji Healthcare">Balaji Healthcare</option>
                                                        <option value="Fine Healthcare">Fine Healthcare</option>
                                                        <option value="Jayprakash">Jayprakash</option>
                                                        <option value="Maheshbhai">Maheshbhai</option>
                                                        <option value="Lifecare">Lifecare</option>
                                                        <option value="Oxylife">Oxylife</option>
                                                        <option value="Nidek">Nidek</option>
                                                        <option value="Jeegar Surgical">Jeegar Surgical</option>
                                                        <option value="Pritesh Surgical">Pritesh Surgical</option>
                                                        <option value="Vendor">Vendor</option>
                                                        <option value="Easy Care Masjid">Easy Care Masjid</option>
                                                        <option value="Other">Other</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="date">Date</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="date" class="form-control" name="date" id="date" value="{{date('Y-m-d')}}">
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="amount">Amount</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="number" class="form-control" name="amount" id="amount">
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="radio" name="amount_to_be" id="collect" value="Collect" checked="checked"> <label for="collect" class="form-label">Collect</label>
                                                    <input type="radio" name="amount_to_be" id="pay" value="Pay"> <label for="pay" class="form-label">Pay</label>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="eqipments">Equipment Required*</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <select class="selectpicker form-control" id="equipments" title="Select Product" name="equipments" data-live-search="true" required>
                                                        <?php 
                                                            foreach ($products as $product) 
                                                            {
                                                        ?>
                                                            <option value="<?php echo $product['product_name']?>"><?php echo $product['product_name']?></option>
                                                        <?php
                                                            }
                                                        ?>    
                                                        <option value="Multiple">Multiple</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    
                                                </div>
                                                <div class="col-md-8">
                                                    <textarea class="form-control" name="multiple_products" id="multiple_products" style="display: none;"></textarea>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="invoice">Invoice</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="radio" name="invoice_type" id="softcopy" value="Softcopy" checked="checked"> <label for="softcopy" class="form-label">Softcopy</label>
                                                    <input type="radio" name="invoice_type" id="hardcopy" value="Hardcopy"> <label for="hardcopy" class="form-label">Hardcopy</label>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="payment_mode">Payment Mode</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="radio" name="payment_mode" id="Online" value="Online" checked="checked"> <label for="Online" class="form-label">Online</label>
                                                    <input type="radio" name="payment_mode" id="cash" value="Cash"> <label for="cash" class="form-label">Cash</label>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="travel">Travel</label>
                                                </div>
                                                <div class="col-md-8">
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
	</script>
    @endsection

    </body>
</html>