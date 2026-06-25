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
        
        @endsection
    </head>

<body id="page-top">	
		<!-- Page Wrapper -->
        @extends('header_and_sidebar')
            <div class="container">
                @section('content')
                    <div class="row">
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-10">
                            <div class="card">
                                <div class="card-header text-center" style="background-color: #337ab7; color: white;">
                                    <span><b>Edit Lead</b></span>
                                </div>
                                <div class="card-body"><!--style="min-height: 0; max-height: 5; overflow-y: scroll;"-->
                                    <form class="form doublePost" method="POST" action="<?php echo url('/');?>/update_lead">
                                    {{ csrf_field() }}
                                        <input type="hidden" name="customer_id" <?php echo"value='".$lead_details[0]['cust_id']."'";?>>
                                        <input type="hidden" name="lead_id" <?php echo"value='".$lead_details[0]['id']."'";?>>
                                        <div class="row form-group">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-4 text-right">
                                                        <label for="creation_date">Date</label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input class="form-control" type="date" name="creation_date" id="creation_date" value="{{$lead_details[0]['creation_date']}}" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <div class="col-md-5">
                                                <div class="row">
                                                    <div class="col-md-5">                                            
                                                        <label for="cust_name">Customer Name*</label>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <input type="text" class="form-control" <?php echo"value='".$lead_details[0]['customer_name']."'";?> id="cust_name" name="cust_name" placeholder="Customer Name" required="true">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="row">
                                                    <div class="col-md-5">                                            
                                                        <label for="patient_name">Patient Name</label>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <input type="text" class="form-control" <?php echo"value='".$lead_details[0]['patient_name']."'";?> name="patient_name" placeholder="Patient Name">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label for="patient_age">Patient Age</label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="number" class="form-control" <?php echo"value='".$lead_details[0]['patient_age']."'";?> name="patient_age" placeholder="Patient Age">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row form-group">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="location">Location*</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <!-- <input type="text" class="form-control" id="location" <?php //echo "style='border-radius: 5px; border:#3292e4 1px solid;'"; echo"value='".$lead_details[0]['location']."'";?> name="location" placeholder="Location" required="true"> -->
                                                        {{-- <select class="selectpicker form-control" title="Select Location From Dropdown" name="location" data-live-search="true" required>
                                                            @foreach ($cities as $city) 
                                                                <option value="{{$city['name']}}" @if($city['name']==$lead_details[0]['location']){{"selected"}}@endif>{{$city['name']}}</option>
                                                            @endforeach
                                                        </select> --}}
                                                        <input type="text" class="form-control" placeholder="Select Location" list="city" id="location" name="location" value="@if(isset($lead_details[0]['location'])){{$lead_details[0]['location']}}@endif" required>
                                                        <datalist id="city">
                                                            @foreach ($cities as $city) 
                                                                <option value="{{$city['name']}}">{{$city['name']}}</option>
                                                            @endforeach
                                                        </datalist>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">                                            
                                                        <label for="eqipments">Equipment Required*</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        {{-- <select class="selectpicker form-control" title="Select Products From Dropdown" name="eqipments[]" multiple data-live-search="true" required>
                                                            <?php 
                                                                // foreach ($products as $product) 
                                                                // {
                                                                    // $product_name = $product['id'];
                                                                    // $products = json_decode($lead_details[0]['equipment_requirement']);
                                                                    // $products_id = json_decode($lead_details[0]['equipment_id']);
                                                            ?>
                                                                <option value="<?php //echo $product['id']?>"<?php //if(in_array($product_name,$products_id)){echo "selected";} ?>><?php// echo $product['product_name']?></option>
                                                            <?php
                                                                //}
                                                            ?>    
                                                        </select> --}}

                                                        <select class="equipments form-control" name="eqipments[]" multiple="multiple" style="width: 100%" required>
                                                            @foreach ($products as $product) 
                                                                @php 
                                                                    $product_name = $product['id'];
                                                                    $products = json_decode($lead_details[0]['equipment_requirement']);
                                                                    $products_id = json_decode($lead_details[0]['equipment_id']);
                                                                @endphp
                                                                <option value="{{$product['id']}}" @if(in_array($product_name,$products_id)){{"selected"}} @endif>{{$product['product_name']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row form-group">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">                                            
                                                        <label for="primary_contact_no">Mobile Number(Primary)*</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" id="primary_contact_no" <?php echo"value='".$lead_details[0]['primary_contact_no']."'";?> name="primary_contact_no" placeholder="Mobile Number (Primary)" oninput="numberOnly(this.id);" maxlength="10" required="true">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">                                            
                                                        <label for="refered_by">Refered by</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" <?php echo"value='".$lead_details[0]['refered_by']."'";?> name="refered_by" placeholder="Refered By">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row form-group">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="customer_type">Customer Type: </label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="btn-group btn-group-toggle btn-block btn-sm" data-toggle="buttons">
                                                            <label class="btn btn-outline-success btn-sm @if($lead_details[0]['customer_type']=='Individual'){{"active"}}@endif">
                                                                <input type="radio" name="customer_type" id="radio_individual" @if($lead_details[0]['customer_type']=='Individual'){{"checked"}}@endif value="Individual"> Individual
                                                            </label>
                                                            <label class="btn btn-outline-success btn-sm @if($lead_details[0]['customer_type']=='Corporate'){{"active"}}@endif">
                                                                <input type="radio" name="customer_type" id="radio_corporate" @if($lead_details[0]['customer_type']=='Corporate'){{"checked"}}@endif value="Corporate"> Corporate
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row form-group">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="lead_source">Lead Source*</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" id="lead_source" <?php echo"value='".$lead_details[0]['lead_source']."'";?> name="lead_source" placeholder="Lead Source (Google, JustDial, Marketing)" required="true">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="lead_owner">Lead Owner*</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <select class="selectpicker form-control" id="lead_owner" title="Select Owner" name="lead_owner" data-live-search="true" required>
                                                            <?php 
                                                                foreach ($users as $user) 
                                                                {
                                                            ?>
                                                                <option value="<?php echo $user['id']?>" <?php if($user['username']==$lead_details[0]['username']){echo "selected";}?> ><?php echo $user['username']?></option>
                                                            <?php
                                                                }
                                                            ?>    
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="lead_value">Lead Value*</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" id="lead_value" <?php echo"value='".$lead_details[0]['lead_value']."'";?> name="lead_value" placeholder="Lead Value" required="true">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr/>
                                        <center><b>Additional Information</b></center>
                                        <hr/>
                                        <div class="row form-group">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="address_line_1">Line 1</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" <?php echo"value='".$lead_details[0]['address_line_1']."'";?> name="address_line_1" placeholder="Line 1">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="address_line_2">Line 2</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" <?php echo"value='".$lead_details[0]['address_line_2']."'";?> name="address_line_2" placeholder="Line 2">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>                                    
                                        <hr>
                                        <div class="row form-group">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="landmark">Landmark</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" <?php echo"value='".$lead_details[0]['landmark']."'";?> name="landmark" placeholder="Landmark">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="area">Area</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" <?php echo"value='".$lead_details[0]['area']."'";?> name="area" placeholder="Area">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row form-group">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="city">City</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control"<?php echo"value='".$lead_details[0]['city']."'";?> name="city" placeholder="City">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="pincode">Pin Code</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" <?php echo"value='".$lead_details[0]['pincode']."'";?> name="pincode" maxlength="6" placeholder="Pincode">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <!-- <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" <?php //echo"value='".$lead_details[0]['state']."'";?> name="state" placeholder="State">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" <?php //echo"value='".$lead_details[0]['country']."'";?> name="country" placeholder="Country">
                                                </div>
                                            </div>
                                        </div> -->
                                        <div class="row form-group">
                                            <div class="col-md-6">
                                                <div class="row">
                                            <!-- <div class="col-md-6">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" <?php //echo"value='".$lead_details[0]['state']."'";?> name="state" placeholder="State">
                                                </div>
                                            </div> -->
                                                    <div class="col-md-4">                                            
                                                        <label for="state">State</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <select class="selectpicker form-control" title="State" name="state" data-live-search="true">
                                                            <?php 
                                                                foreach ($states as $state) 
                                                                {
                                                            ?>
                                                                <option value="<?php echo $state['name']?>" <?php if($state['name'] == $lead_details[0]['state']){echo "selected";} ?>><?php echo $state['name']?></option>
                                                            <?php
                                                                }
                                                            ?>    
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="country">Country</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <select class="selectpicker form-control" title="Country" name="country" data-live-search="true">
                                                            <?php 
                                                                foreach ($countries as $country) 
                                                                {
                                                            ?>
                                                                <option value="<?php echo $country['name']?>" <?php if($country['name'] == $lead_details[0]['country']){echo "selected";} ?>><?php echo $country['name']?></option>
                                                            <?php
                                                                }
                                                            ?>    
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row form-group">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="email">Email Id</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="email" class="form-control" <?php echo"value='".$lead_details[0]['email_id']."'";?> name="email" placeholder="Email">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="eqipments">Mobile Number(Secondary)</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" <?php echo"value='".$lead_details[0]['secondary_contact_no']."'";?> name="secondary_contact_no"  oninput="numberOnly(this.id);" maxlength="10" placeholder="Mobile Number (Secondary)">                                            
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row form-group">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="doctor_name">Doctor Name</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" <?php echo"value='".$lead_details[0]['doctor_name']."'";?> name="doctor_name" placeholder="Doctor Name">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="hospital_name">Hospital Name</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" <?php echo"value='".$lead_details[0]['hospital_name']."'";?> name="hospital_name" placeholder="Hospital Name">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>  
                                        <hr>                                 
                                        <div class="row form-group">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="therapeutic_requirement">Therapeutic Requirement</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" <?php echo"value='".$lead_details[0]['therapeutic_requirement']."'";?> name="therapeutic_requirement" placeholder="Therapeutic Requirement">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" id="equipments" <?php //echo "style='border-radius: 5px; border:#3292e4 1px solid;'"; echo"value='".$lead_details[0]['equipment_requirement']."'";?> name="equipments" placeholder="Equipment Required" required="true"> 
                                                </div>
                                            </div>
                                        </div> -->

                                        
                                        <!-- <select id="multiple" class="form-control form-control-chosen" data-placeholder="Please select..." multiple>
                                        <option></option>
                                        <option>Option One</option>
                                        <option>Option Two</option>
                                        <option>Option Three</option>
                                        </select> -->
                                        <hr>
                                        <center>
                                            <button class="btn btn-primary" type="submit" name="submit" value="update">Update</button>
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
        <script>
            //$('select').selectpicker();
            $(document).ready(function() {
                $('.equipments').select2({
                    placeholder: 'Select Products',
                    allowClear: true
                });
            });
            function numberOnly(id) 
            {
                var element = document.getElementById(id);
                element.value = element.value.replace(/[^0-9]/gi, "");
            }
        </script>
    @endsection

</body>

</html>