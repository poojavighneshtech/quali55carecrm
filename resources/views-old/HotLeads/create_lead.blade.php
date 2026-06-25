<!DOCTYPE html>
<html lang="en">
    @extends('header_and_sidebar')
    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Create Hot Lead</title>
        @section('styles')
        {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.2/css/bootstrap.min.css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.min.css"/>
        <!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script> -->
        <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script> --}}
        
        @endsection
    </head>

   

<body id="page-top">	
		<!-- Page Wrapper -->
    
            <div class="container">
                @section('content')
                <div class="row">
                    <div class="col-md-2">
                    </div>
                    <div class="col-md-8 ">
                        <div class="panel panel-primary">
                            <div class="panel-heading text-center">
                                <span><b>New Inquiry</b></span>
                            </div>
                            <div class="panel-body"><!--style="min-height: 0; max-height: 5; overflow-y: scroll;"-->
                                <form class="form" method="POST" action="<?php echo url('/');?>/create_hot_lead">
                                {{ csrf_field() }}
                                    <input type="hidden" name="hot_lead_id" id = "hot_lead_id"<?php if(isset($hot_lead_details[0]['hot_lead_id'])){ echo"value='".$hot_lead_details[0]['hot_lead_id']."'";}?>>
                                    <input type="hidden" name="customer_id" id = "customer_id"<?php if(isset($customer_details[0]['cust_id'])){ echo"value='".$customer_details[0]['cust_id']."'";}?>>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="creation_date">Date</label>
                                        </div>
                                        <div class="col-md-3">
                                            <input class="form-control" type="date" name="creation_date" id="creation_date" value="{{date('Y-m-d')}}" required>
                                        </div>
                                        <div class="col-md-3">
                                            <center>
                                                <span class="badge bg-danger text-light" style="display: <?php if(isset($customer_details[0]['customer_name'])){echo"block";}else{echo "none";}?>">Existing user</span>
                                            </center>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="primary_contact_no">Mobile Number(Primary)*</label>
                                        </div>
                                        <div id="primary_contact" class="col-md-7">
                                            <input type="number" class="form-control" id="primary_contact_no" <?php if(isset($hot_lead_details[0]['hot_leads_contact_no'])){echo"value='".$hot_lead_details[0]['hot_leads_contact_no']."'";}?> name="primary_contact_no" placeholder="Mobile Number (Primary)" max="9999999999" required="true">
                                        </div>
                                        <div id="buttons" class="col-md-1">
                                            <button class="btn btn-secondary" type="reset" id="reset1" name="reset">Clear</button>
                                            <a href="#" id="prev_leads" class="btn btn-primary" style="display:none;">Previous Leads</a>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="cust_name">Customer Name*</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" <?php if(isset($customer_details[0]['customer_name'])){echo"value='".$customer_details[0]['customer_name']."'";}?> id="cust_name" name="cust_name" placeholder="Customer Name" required="true">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="patient_name">Patient Name</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" <?php if(isset($customer_details[0]['patient_name'])){ echo"value='".$customer_details[0]['patient_name']."'";}?> name="patient_name" placeholder="Patient Name">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="patient_age">Patient Age</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="number" class="form-control" <?php if(isset($customer_details[0]['patient_age'])){ echo"value='".$customer_details[0]['patient_age']."'";}?> name="patient_age" placeholder="Patient Age">
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <div class="col-md-4">
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="register_type" id="corporate" value="corporate">
                                                <label class="form-check-label" for="corporate">Corporate</label>
                                              </div>
                                              <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="register_type" id="individual" value="indivual">
                                                <label class="form-check-label" for="individual">Individual</label>
                                            </div>

                                        </div>
                                    </div>
                                    
                                    <div class="row form-group">
                                        <div class="col-md-4" >
                                            <label for="corporation_name" id="corporation_name_label" style="display: none">Corporation Name</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control"  name="corporation_name" id="corporation_name" placeholder="Corporation Name" value="" style="display: none">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="gst_no" id="gst_no_label" style="display: none">GST NO</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control text-uppercase" id="gst_no" name="gst_no" placeholder="GST No." maxlength="15" value="" style="display: none">
                                        </div>
                                    </div>
                                    
                                    
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="location">Location*</label>
                                        </div>
                                        <div class="col-md-8">
                                            <!-- <select class="selectpicker form-control" id="location" title="Select Location" name="location" data-live-search="true" required>
                                                <?php 
                                                    // foreach ($cities as $city) 
                                                    // {
                                                ?>
                                                    <option value="<?php //echo $city['name']?>"><?php //echo $city['name']?></option>
                                                <?php
                                                    //}
                                                ?>    
                                            </select> -->

                                            <input class="form-control" list="city_list" id="location" title="Select Location" name="location"  required>
                                            <datalist id="city_list">
                                                @foreach ($cities as $city)
                                                    <option value="{{$city['name']}}">    
                                                @endforeach
                                            </datalist>
                                        </div>
                                    </div>
									
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="eqipments">Equipment Required*</label>
                                        </div>
                                        <div class="col-md-8">
                                            <select class="selectpicker form-control" id="equipments" title="Select Products From Dropdown" name="eqipments[]" multiple data-live-search="true" required>
                                                <?php 
                                                    foreach ($products as $product) 
                                                    {
                                                ?>
                                                    <option value="<?php echo $product['id']?>"><?php echo $product['product_name']?></option>
                                                <?php
                                                   }
                                                ?>    
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="refered_by">Refered by</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="refered_by" <?php if(isset($customer_details[0]['refered_by'])){ echo"value='".$customer_details[0]['refered_by']."'";}?> name="refered_by" placeholder="Refered By">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="lead_source">Lead Source*</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="lead_source" <?php if(isset($customer_details[0]['lead_source'])){ echo"value='".$customer_details[0]['lead_source']."'";}else{echo "value='Web Form'"; }?> name="lead_source" placeholder="Lead Source (Google, JustDial, Marketing)" required="true">
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
                                                    <option value="<?php echo $user['id']?>" <?php if ($user['username'] == session('username')) {echo "selected";}?>><?php echo $user['username']?></option>
                                                <?php
                                                    }
                                                ?>    
                                            </select>
                                        </div>
                                    </div>
                                    <center>
                                        <button class="btn btn-primary" type="submit" id="submit1" name="submit" <?php if(isset($customer_details[0]['customer_name'])){echo "value='check'";}else{echo "value='submit'";}?>>Submit</button>
                                        <button class="btn btn-default" type="reset" id="reset2" name="reset">Clear</button>
                                    </center>
                                    <hr/>
                                    <center><b>Additional Information</b></center>
                                    <hr/>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="address_line_1">Line 1</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="address_line_1"<?php if(isset($customer_details[0]['address_line_1'])){ echo"value='".$customer_details[0]['address_line_1']."'";}?> name="address_line_1" placeholder="Line 1">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="address_line_2">Line 2</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="address_line_2"<?php if(isset($customer_details[0]['address_line_2'])){ echo"value='".$customer_details[0]['address_line_2']."'";}?> name="address_line_2" placeholder="Line 2">
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="landmark">Landmark</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="landmark" <?php if(isset($customer_details[0]['landmark'])){ echo"value='".$customer_details[0]['landmark']."'";}?> name="landmark" placeholder="Landmark">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="area">Area</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="area" <?php if(isset($customer_details[0]['area'])){ echo"value='".$customer_details[0]['area']."'";}?> name="area" placeholder="Area">
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="city">City</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="city"<?php if(isset($customer_details[0]['city'])){ echo"value='".$customer_details[0]['city']."'";}else{echo "value='Mumbai'";}?> name="city" placeholder="City">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="pincode">Pin Code</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="pincode"<?php if(isset($customer_details[0]['pincode'])){ echo"value='".$customer_details[0]['pincode']."'";}?> name="pincode" maxlength="6" placeholder="Pincode">
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="state">State</label>
                                        </div>
                                        <div class="col-md-8">
                                            <select class="selectpicker form-control" title="State" name="state" data-live-search="true">
                                                <?php 
                                                    foreach ($states as $state) 
                                                    {
                                                ?>
                                                    <option value="<?php echo $state['name']?>"<?php if($state['name'] == 'Maharashtra'){ echo "selected";}?>><?php echo $state['name']?></option>
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
                                            <select class="selectpicker form-control" title="Country" name="country" data-live-search="true">
                                                <?php 
                                                    foreach ($countries as $country) 
                                                    {
                                                ?>
                                                    <option value="<?php echo $country['name']?>"<?php if($country['name'] == 'India'){ echo "selected";}?>><?php echo $country['name']?></option>
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
                                            <input type="email" class="form-control" id="email_id" <?php if(isset($customer_details[0]['email_id'])){ echo"value='".$customer_details[0]['email_id']."'";}?> name="email" placeholder="Email">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="eqipments">Mobile Number(Secondary)</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="number" class="form-control" id="secondary_contact_no" <?php if(isset($customer_details[0]['secondary_contact_no'])){ echo"value='".$customer_details[0]['secondary_contact_no']."'";}?> name="secondary_contact_no" max="9999999999" placeholder="Mobile Number (Secondary)">
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="doctor_name">Doctor Name</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" <?php if(isset($customer_details[0]['doctor_name'])){ echo"value='".$customer_details[0]['doctor_name']."'";}?> name="doctor_name" placeholder="Doctor Name">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="hospital_name">Hospital Name</label>
                                        </div>
                                        <div class="col-md-8">
                                                <input type="text" class="form-control" <?php if(isset($customer_details[0]['hospital_name'])){ echo"value='".$customer_details[0]['hospital_name']."'";}?> name="hospital_name" placeholder="Hospital Name">
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <div class="col-md-4">                                            
                                            <label for="therapeutic_requirement">Therapeutic Requirement</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" <?php if(isset($customer_details[0]['therapeutic_requirement'])){ echo"value='".$customer_details[0]['therapeutic_requirement']."'";}?> name="therapeutic_requirement" placeholder="Therapeutic Requirement">
                                        </div>                                        
                                    </div>
                                    <center>
                                        <button class="btn btn-primary" type="submit" id="submit2" name="submit" <?php if(isset($customer_details[0]['customer_name'])){echo "value='check'";}else{echo "value='submit'";}?>>Submit</button>
                                        <button class="btn btn-default" type="reset" id="reset3" name="reset">Clear</button>
                                    </center>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endsection
            </div>

            <!-- End of Main Content -->
        
        <!-- End of Content Wrapper -->

    
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">                	
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="login.html">Logout</a>
                </div>
            </div>
        </div>
    </div>	   
    @section('script')
    
    <script>
    //SELECT PICKER operation handler ........                                                        
        $('select').selectpicker();
        //----------------Real time search custoomer details for autofill text in create lead-------------------//
            $("#primary_contact_no").on("input", function (e) {
            var primary_contact_no = $("#primary_contact_no").val();
            var dataString = (primary_contact_no);

                $.ajax({
                type: "GET",
                url: "<?php echo url('/');?>/findCustomer/"+dataString,
                //data: dataString,
                cache: false,
                success: function(data)
                {
                    //alert(data);
                    //alert(data[0].customer_name);
                     var obj = jQuery.parseJSON(data);
                    var i=0;
                     //alert(obj.customer_id);
                     $('#cust_id').val(obj.customer_id); 
                    $('#cust_name').val(obj.customer_name);
                    if(obj.customer_name!=null)
                    {
                        $('#cust_name').attr('readonly',true);
                        document.getElementById("primary_contact").classList.remove('col-md-7');
                        document.getElementById("buttons").classList.remove('col-md-1');
                        document.getElementById("primary_contact").classList.add('col-md-4');
                        document.getElementById("buttons").classList.add('col-md-4');
                        //$('prev_leads').attr('href',"<?php echo url('/');?>/view_cust_lead/"+obj.customer_id+"");
                        document.getElementById("prev_leads").href="<?php echo url('/');?>/view_cust_lead/"+obj.customer_id;
                        $('#submit1').val('check');
                        $('#submit2').val('check');
                        $('#prev_leads').show();
                    }
                    else
                    {
                        $('#cust_name').attr('readonly',false);
                        document.getElementById("primary_contact").classList.remove('col-md-4');
                        document.getElementById("buttons").classList.remove('col-md-4');
                        document.getElementById("primary_contact").classList.add('col-md-7');
                        document.getElementById("buttons").classList.add('col-md-1');
                        $('#submit1').val('submit');
                        $('#submit2').val('submit');
                        $('#prev_leads').hide();
                    }
                    $('#location').val(obj.location);
                    $('#location').selectpicker('val',obj.location);
                    $('select[name=selValue]').val(1);
                    // $('#address_line_1').val(obj.address_line_1);
                    // $('#address_line_2').val(obj.address_line_2);
                    // $('#area').val(obj.area);
                    // $('#landmark').val(obj.landmark);
                    // $('#city').val(obj.city);
                    // $('#pincode').val(obj.pincode);
                    // $('#state').selectpicker('val',obj.state);
                    // $('#country').selectpicker('val',obj.country);
                    // $('#landmark').val(obj.landmark);
                    // $('#secondary_contact_no').val(obj.secondary_contact_no);
                    // $('#email_id').val(obj.email_id);
                    $('#refered_by').val(obj.refered_by);
                }
                });
            }) 
            $('#reset1').on('click', function(){
                $('#location').selectpicker('val',false);
                $('#state').selectpicker('val',false);
                $('#country').selectpicker('val',false);
                $('#equipments').selectpicker('val',false);
                $('#lead_owner').selectpicker('val',false);
                $('#cust_name').attr('readonly',false);
                document.getElementById("primary_contact").classList.remove('col-md-4');
                document.getElementById("buttons").classList.remove('col-md-4');
                document.getElementById("primary_contact").classList.add('col-md-7');
                document.getElementById("buttons").classList.add('col-md-1');
                $('#prev_leads').hide();
            });
            $('#reset2').on('click', function(){
                $('#location').selectpicker('val',false);
                $('#state').selectpicker('val',false);
                $('#country').selectpicker('val',false);
                $('#equipments').selectpicker('val',false);
                $('#lead_owner').selectpicker('val',false);
                $('#cust_name').attr('readonly',false);
                document.getElementById("primary_contact").classList.remove('col-md-4');
                document.getElementById("buttons").classList.remove('col-md-4');
                document.getElementById("primary_contact").classList.add('col-md-7');
                document.getElementById("buttons").classList.add('col-md-1');
                $('#prev_leads').hide();
            });
            $('#reset3').on('click', function(){
                $('#location').selectpicker('val',false);
                $('#state').selectpicker('val',false);
                $('#country').selectpicker('val',false);
                $('#equipments').selectpicker('val',false);
                $('#lead_owner').selectpicker('val',false);
                $('#cust_name').attr('readonly',false);
                document.getElementById("primary_contact").classList.remove('col-md-4');
                document.getElementById("buttons").classList.remove('col-md-4');
                document.getElementById("primary_contact").classList.add('col-md-7');
                document.getElementById("buttons").classList.add('col-md-1');
                $('#prev_leads').hide();
            });
        
            //corporate display
            $('#corporate').click(function() 
            {
                if($(this).is(':checked'))
                {
                    $('#corporation_name').css('display','block');
                    $('#corporation_name_label').css('display','block');
                    $('#gst_no_label').css('display','block');
                    $('#gst_no').css('display','block');
                }
            });
            $('#individual').click(function() 
            {
                if($(this).is(':checked'))
                {
                    $('#corporation_name').css('display','none');
                    $('#corporation_name_label').css('display','none');
                    $('#gst_no_label').css('display','none');
                    $('#gst_no').css('display','none');
                }
            });

	</script>
    @endsection

</body>

</html>