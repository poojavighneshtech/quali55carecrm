<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Inquiry : Create Lead</title>
        @section('styles')
        
        {{-- <style>
            *{outline:none !important;}
            .bootstrap-select > .dropdown-toggle[title='Select Products From Dropdown'],
            .bootstrap-select > .dropdown-toggle[title='Select Products From Dropdown']:hover,
            .bootstrap-select > .dropdown-toggle[title='Select Products From Dropdown']:focus,
            .bootstrap-select > .dropdown-toggle[title='Select Products From Dropdown']:active { color: red; }
        </style> --}}
        @endsection
    </head>

<body id="page-top">	
		<!-- Page Wrapper -->
    @extends('header_and_sidebar')
        
            <div class="container">
                @section('content')
                <div class="row">
                    <div class="col-md-2">
                    </div>
                    <div class="col-md-8 col-md-offset-2">
                        <div class="panel panel-primary">
                            <div class="panel-heading text-center">
                                <span><b>Convert Lead</b></span>
                            </div>
                            <div class="panel-body"><!--style="min-height: 0; max-height: 5; overflow-y: scroll;"-->
                                <form class="form" method="POST" action="<?php echo url('/');?>/update_hot_lead">
                                {{ csrf_field() }}
                                    <input type="hidden" name="customer_id">
                                    <input type="hidden" name="hot_lead_id" value="{{$hot_leads_data[0]['hot_lead_id']}}">
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="primary_contact_no">Mobile Number(Primary)*</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="number" class="form-control" id="primary_contact_no" name="primary_contact_no" value="{{$hot_leads_data[0]['hot_leads_contact_no']}}" placeholder="Mobile Number (Primary)*" max="9999999999" required="true">
                                        </div>
                                    </div> 
                                    <div class="row form-group">
                                        <div class="col-md-4">                                            
                                            <label for="cust_name">Customer Name*</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="cust_name" name="cust_name" placeholder="Customer Name*" required="true">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">                                            
                                            <label for="patient_name">Patient Name</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name="patient_name" placeholder="Patient Name*"required="true">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="patient_age">Patient Age</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="number" class="form-control" name="patient_age" placeholder="Patient Age*"required="true">
                                        </div>
                                    </div>                                    
                                   
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="location">Location*</label>
                                        </div>
                                        <div class="col-md-8">
                                            
                                            <input class="form-control" list="city_list" id="location" title="Select Location" name="location" required>
                                            <datalist id="city_list">
                                                @foreach ($cities as $city)
                                                    <option value="{{$city['name']}}">    
                                                @endforeach
                                            </datalist>
                                        </div>
                                    </div>      
                                    
                                    {{--gst no and corp name--}}
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
                                            <label for="corporation_name" id="corporation_name_label">Corporation Name</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control"  name="corporation_name" id="corporation_name" placeholder="Corporation Name">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="gst_no" id="gst_no_label">GST NO</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control text-uppercase" id="gst_no" name="gst_no" placeholder="GST No." maxlength="15" >
                                        </div>
                                    </div>
                                    
                                    <div class="row form-group">
                                        <div class="col-md-4">                                            
                                            <label for="eqipments">Equipment Required*</label>
                                        </div>
                                        <div class="col-md-8">
                                            <select class="selectpicker form-control" title="Select Products From Dropdown" name="eqipments[]" multiple data-live-search="true" required>
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
                                            <input type="text" class="form-control" name="refered_by" placeholder="Refered By*">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">                                        
                                            <label for="lead_source">Lead Source*</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="lead_source" name="lead_source" value="Web Form" placeholder="Lead Source (Google, JustDial, Marketing)*" required="true">
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
                                                    <option value="<?php echo $user['id']?>" <?php if($user['username']==session('username')){echo "selected";}?> ><?php echo $user['username']?></option>
                                                <?php
                                                    }
                                                ?>    
                                            </select>
                                        </div>
                                    </div>
                                    <hr/>
                                    <center><b>Additional Information</b></center>
                                    <hr/>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="address_line_1">Line 1</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name="address_line_1" placeholder="Line 1*"required="true">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="address_line_2">Line 2</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name="address_line_2" placeholder="Line 2*">
                                        </div>
                                    </div>                                    
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="landmark">Landmark</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name="landmark" placeholder="Landmark*"required="true">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="area">Area</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name="area" placeholder="Area*">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="city">City</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name="city" placeholder="City*"required="true">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="pincode">Pin Code</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name="pincode" placeholder="Pincode*" maxlength="6" required="true">
                                        </div>
                                    </div>
                                    <!-- <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" <?php //echo"value='".$lead_details[0]['state']."'";?> name="state" placeholder="State*">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" <?php //echo"value='".$lead_details[0]['country']."'";?> name="country" placeholder="Country*">
                                            </div>
                                        </div>
                                    </div> -->
                                    <div class="row form-group">
                                        <!-- <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" <?php //echo"value='".$lead_details[0]['state']."'";?> name="state" placeholder="State*">
                                            </div>
                                        </div> -->
                                        <div class="col-md-4">
                                            <label for="state">State</label>
                                        </div>
                                        <div class="col-md-8">
                                            <select class="selectpicker form-control" title="State*" name="state" data-live-search="true">
                                                <?php 
                                                    foreach ($states as $state) 
                                                    {
                                                ?>
                                                    <option value="<?php echo $state['name']?>"><?php echo $state['name']?></option>
                                                <?php
                                                    }
                                                ?>    
                                            </select>
                                        </div>
                                    </div>
                                        <!-- <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" <?php //echo"value='".$lead_details[0]['country']."'";?> name="country" placeholder="Country*">
                                            </div>
                                        </div> -->
                                    <div class="row form-group">
                                        <div class="col-md-4">                                
                                            <label for="country">Country</label>
                                        </div>
                                        <div class="col-md-8">
                                            <select class="selectpicker form-control" title="Country*" name="country" data-live-search="true">
                                                <?php 
                                                    foreach ($countries as $country) 
                                                    {
                                                ?>
                                                    <option value="<?php echo $country['name']?>"><?php echo $country['name']?></option>
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
                                            <input type="email" class="form-control" name="email" placeholder="Email*"required="true">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="eqipments">Mobile Number(Secondary)</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="number" class="form-control" name="secondary_contact_no" max="9999999999" placeholder="Mobile Number (Secondary)*"required="true">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="doctor_name">Doctor Name</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name="doctor_name" placeholder="Doctor Name">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="hospital_name">Hospital Name</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name="hospital_name" placeholder="Hospital Name">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="therapeutic_requirement">Therapeutic Requirement</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name="therapeutic_requirement" placeholder="Therapeutic Requirement">
                                        </div>
                                    </div>
                                    
                                    <center>
                                        <button class="btn btn-primary" type="submit" name="submit" value="convert">Convert</button>
                                        <button class="btn btn-default" type="reset" name="submit">Clear</button>
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
    {{-- <script>
        
        $(document).ready(function(){
            // $('.form-control-chosen').chosen();
            $("#cust_name").on("input", function(){
                // Print entered value in a div box
                if(!$(this).val())
                {
                    $('#cust_name').attr('style', "border-radius: 5px; border:#FF0000 1px solid;");
                }
                else
                {
                    $('#cust_name').attr('style', "border-radius: 5px; border:#3292e4 1px solid;");
                }
            });
            $("#location").on("input", function(){
                // Print entered value in a div box
                if(!$(this).val())
                {
                    $('#location').attr('style', "border-radius: 5px; border:#FF0000 1px solid;");
                }
                else
                {
                    $('#location').attr('style', "border-radius: 5px; border:#3292e4 1px solid;");
                }
            });
            $("#equipments").on("input", function(){
                // Print entered value in a div box
                if(!$(this).val())
                {
                    $('#equipments').attr('style', "border-radius: 5px; border:#FF0000 1px solid;");
                }
                else
                {
                    $('#equipments').attr('style', "border-radius: 5px; border:#3292e4 1px solid;");
                }
            });
            $("#lead_source").on("input", function(){
                // Print entered value in a div box
                if(!$(this).val())
                {
                    $('#lead_source').attr('style', "border-radius: 5px; border:#FF0000 1px solid;");
                }
                else
                {
                    $('#lead_source').attr('style', "border-radius: 5px; border:#3292e4 1px solid;");
                }
            });
            $("#primary_contact_no").on("input", function(){
                // Print entered value in a div box
                if(!$(this).val())
                {
                    $('#primary_contact_no').attr('style', "border-radius: 5px; border:#FF0000 1px solid;");
                }
                else
                {
                    $('#primary_contact_no').attr('style', "border-radius: 5px; border:#3292e4 1px solid;");
                }
            });
        });
	</script> --}}
    <script>
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