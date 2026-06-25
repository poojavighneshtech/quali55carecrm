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
                        
                        <div class="col-md-12">
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
                                                        <input type="text" class="form-control" <?php echo"value='".$lead_details[0]['patient_name']."'";?> name="patient_name" id="patient_name" placeholder="Patient Name">
                                                        <input type="hidden" name="hidden_def_patient_id" id="hidden_def_patient_id">
                                                        <div class="col-md-1">
                                                            <button type="button" class="btn btn-sm btn-outline-primary" style="@if($lead_details[0]['customer_type']=='Corporate'){{"display:block;"}}@else{{"display:none;"}}@endif" name="def_patient" id="def_patient">Auto</button>
                                                        </div>
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
                                            <div class="col-md-5">
                                                <div class="row">
                                                    <div class="col-md-5">
                                                        <label for="cust_gender">Customer Gender*</label>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <div class="btn-group btn-group-toggle btn-block btn-sm" data-toggle="buttons">
                                                            <label class="btn btn-outline-secondary btn-sm  @if($lead_details[0]['cust_gender']=='Male') active @endif" id="radio_lbl_cust_male">
                                                                <input type="radio" name="customer_gender" id="radio_cust_male" value="Male"
                                                                    @if($lead_details[0]['cust_gender']=='Male') checked @else required @endif> Male
                                                            </label>
                                                            <label class="btn btn-outline-secondary btn-sm  @if($lead_details[0]['cust_gender']=='Female') active @endif" id="radio_lbl_cust_female">
                                                                <input type="radio" name="customer_gender" id="radio_cust_female" value="Female"
                                                                    @if($lead_details[0]['cust_gender']=='Female') checked @else required @endif> Female
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="row">
                                                    <div class="col-md-5">
                                                        <label for="patient_gender">Patient Gender</label>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <div class="btn-group btn-group-toggle btn-block btn-sm" data-toggle="buttons">
                                                            <label class="btn btn-outline-secondary btn-sm @if($lead_details[0]['patient_gender']=='Male') active @endif">
                                                                <input type="radio" name="patient_gender" id="radio_patient_male" value="Male"
                                                                    @if($lead_details[0]['patient_gender']=='Male') checked @endif> Male
                                                            </label>
                                                            <label class="btn btn-outline-secondary btn-sm @if($lead_details[0]['patient_gender']=='Female') active @endif">
                                                                <input type="radio" name="patient_gender" id="radio_patient_female" value="Female"
                                                                    @if($lead_details[0]['patient_gender']=='Female') checked @endif> Female
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
                                                        <div class="row form-group">
                                                            <div class="products_display">
                                                                @php
                                                                    $i = 1;
                                                                @endphp
                                                                @foreach ($products as $product) 
                                                                    @php 
                                                                        $product_name = $product['id'];
                                                                        // $products = json_decode($lead_details[0]['equipment_requirement']);
                                                                        $products_id = json_decode($lead_details[0]['equipment_id']);
                                                                    @endphp                                                                        
                                                                    @if(in_array($product_name,$products_id))
                                                                        <span class='btn btn-sm btn-outline-primary' id='span{{$i}}'>{{$product['product_name']}} <span onclick='removeProduct(this.id);' id='{{$i}}'><i class='fas fa-window-close'></i></span>
                                                                        <input type='hidden' id='equipment_id{{$i}}' name='equipment_id[]' value='{{$product_name}}'></span>
                                                                        @php 
                                                                            $i++;
                                                                        @endphp
                                                                    @endif
                                                                @endforeach
                                                                {{-- <span class="btn btn-sm btn-outline-primary">Walker Without Wheel <span onclick="removeProduct(this.id);" id="1"><i class="fas fa-window-close"></i></span></span>
                                                                <span class="btn btn-sm btn-outline-primary">Walker With Wheel  <span onclick="removeProduct(this.id);" id="2"><i class="fas fa-window-close"></i></span></span> --}}
                                                            </div>
                                                        </div>
                                                        <div class="row justify-content-start form-group">
                                                            <button type="button" name="add_product_btn" id="add_product_btn" class="btn btn-sm btn-outline-warning" value="1" onclick="selectProduct();">Add Product</button>
                                                        </div>
                                                        {{-- <select class="equipments form-control" name="eqipments[]" multiple="multiple" style="width: 100%" required>
                                                            @foreach ($products as $product) 
                                                                @php 
                                                                    $product_name = $product['id'];
                                                                    $products = json_decode($lead_details[0]['equipment_requirement']);
                                                                    $products_id = json_decode($lead_details[0]['equipment_id']);
                                                                @endphp
                                                                <option value="{{$product['id']}}" @if(in_array($product_name,$products_id)){{"selected"}} @endif>{{$product['product_name']}}</option>
                                                            @endforeach
                                                        </select> --}}
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
                                        <div id="corporate_contacts" class="row form-group" style="@if($lead_details[0]['customer_type'] == 'Individual') display:none;@else display:block @endif">
                                            <div class="col-md-3">
                                                <label for="contact_person_1_name">Corporate Contact Person 1</label>
                                                <input type="text" class="form-control" id="contact_person_1_name" <?php if(isset($lead_details[0]['contact_person_1_name'])){echo"value='".$lead_details[0]['contact_person_1_name']."' disabled";}?> name="contact_person_1_name" placeholder="First Contact Person Name"
                                                    @if($lead_details[0]['customer_type'] == 'Corporate') required="true" @endif>
                                                @if(isset($lead_details[0]['contact_person_1_name']))
                                                    <input type="hidden" class="form-control" id="contact_person_1_name" <?php if(isset($lead_details[0]['contact_person_1_name'])){echo"value='".$lead_details[0]['contact_person_1_name']."'";}?> name="contact_person_1_name">
                                                @endif
                                            </div>
                                            <div class="col-md-3">
                                                <label for="contact_person_1_no">Contact Number</label>
                                                <input type="text" class="form-control" id="contact_person_1_no" <?php if(isset($lead_details[0]['contact_person_1_no'])){echo"value='".$lead_details[0]['contact_person_1_no']."' disabled";}?> name="contact_person_1_no" placeholder="First Contact Person Number" 
                                                    oninput="numberOnly(this.id);" maxlength="10" @if($lead_details[0]['customer_type'] == 'Corporate') required="true" @endif>
                                                @if(isset($lead_details[0]['contact_person_1_no']))
                                                    <input type="hidden" class="form-control" id="contact_person_1_no" <?php if(isset($lead_details[0]['contact_person_1_no'])){echo"value='".$lead_details[0]['contact_person_1_no']."'";}?> name="contact_person_1_no">
                                                @endif
                                            </div>
                                            <div class="col-md-3">
                                                <label for="contact_person_2_name">Corporate Contact Person 2</label>
                                                <input type="text" class="form-control" id="contact_person_2_name" <?php if(isset($lead_details[0]['contact_person_2_name'])){echo"value='".$lead_details[0]['contact_person_2_name']."' disabled";}?> name="contact_person_2_name" placeholder="Second Contact Person Name">
                                                @if(isset($lead_details[0]['contact_person_2_name']))
                                                    <input type="hidden" class="form-control" id="contact_person_2_name" <?php if(isset($lead_details[0]['contact_person_2_name'])){echo"value='".$lead_details[0]['contact_person_2_name']."'";}?> name="contact_person_2_name">
                                                @endif
                                            </div>
                                            <div class="col-md-3">
                                                <label for="contact_person_2_no">Contact Number</label>
                                                <input type="text" class="form-control" id="contact_person_2_no" <?php if(isset($lead_details[0]['contact_person_2_no'])){echo"value='".$lead_details[0]['contact_person_2_no']."' disabled";}?> name="contact_person_2_no" placeholder="Second Contact Person Number" oninput="numberOnly(this.id);" maxlength="10">
                                                @if(isset($lead_details[0]['contact_person_2_no']))
                                                    <input type="hidden" class="form-control" id="contact_person_2_no" <?php if(isset($lead_details[0]['contact_person_2_no'])){echo"value='".$lead_details[0]['contact_person_2_no']."'";}?> name="contact_person_2_no">
                                                @endif
                                            </div>
                                            <hr>
                                        </div>
                                        <div class="row form-group">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="lead_source">Lead Source*</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        {{-- <input type="text" class="form-control" id="lead_source" <?php echo"value='".$lead_details[0]['lead_source']."'";?> name="lead_source" placeholder="Lead Source (Google, JustDial, Marketing)" required="true"> --}}
                                                        <select class="select selectpicker" title="Select Lead Source" name="lead_source" id="lead_source" required>
                                                            @foreach($lead_source as $key=>$value)
                                                                <option value="{{$value}}" @if($lead_details[0]['lead_source'] == $value){{"selected"}}@endif>{{$value}}</option>
                                                            @endforeach
                                                        </select>
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
                    <div class="modal fade" id="add_product" tabindex="-1" role="dialog" aria-labelledby="add_productLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="add_productLabel">Add Product</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <select class="select selectpicker selectto form-control" data-live-search="true" onchange="fetchProductDetails(this.value)" id="eqipments" name="eqipments" title="Select Product" style="width: 110%" required>
                                        @foreach ($products as $prod) 
                                            <option value="{{$prod['id']}}">{{$prod['product_name']}}</option>
                                        @endforeach
                                    </select>
                                    <div class="questionaries">
                                        <h4>Questions</h4>
                                        <div class="questions">
                                            
                                        </div>
                                    </div>
                                    <div class="related_products">
                                        <h4>Related Products</h4>
                                        <div class="products prod_card_deck">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" onclick="addProduct();">Add</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
            $("#patient_name").on("input",function(){
                if($(this).val() == 'NA' || $(this).val() == 'Na' || $(this).val() == 'na'){
                    $(this).val("Patient-"+ "{{$patient_id}}");
                    $("#hidden_def_patient_id").val("{{$patient_id}}");
                }
                else{
                    $("#hidden_def_patient_id").val("No");
                }
            });

            $("#def_patient").click(function(){
                $("#patient_name").val("Patient-"+"{{$patient_id}}");
            });

            function removeProduct(id)
            {
                $("#span"+id).remove();
                $('#add_product_btn').val(parseInt(id)-1);
            }
            function selectProduct()
            {

                $("#add_product").modal("show");
                
                let last_id = $('#add_product_btn').val();
                for(let i=1; i<last_id; i++)
                {
                    let prod_id = $('#equipment_id'+i).val();
                    $("#equipments option[value*='"+prod_id+"']").prop('disabled',true); 
                }
                // $("select option[value*='Sold Out']").prop('disabled',true);
                // $('#equipments').selectpicker('val',false);
            }
            function fetchProductDetails(id)
            {
                // console.log(id);
                $.ajax({
                    type:"GET",
                    url: "{{url('/')}}/fetchProductDetailsLead?id="+id,
                    cache:false,
                    success:function(response)
                    {
                        $("#questions").remove();
                        $(".prod_card").remove();
                        if(response[0].questionaries !=null)
                        {
                            let que_array = response[0].questionaries.split(",");
                            let questions = "<ol id='questions'>";
                            for(let i=0; i<que_array.length; i++)
                            {
                                questions += "<li>"+que_array[i]+"</li>";    
                            }
                            questions += "</ol>";
                            $(".questions").append(questions);
                        }
                        if(response['related_products'].length != 0)
                        {
                            let related_products = "";
                            for(let i=0; i<response['related_products'].length; i++)
                            {
                                if(i == 0)
                                {
                                    related_products += "<div class='card-deck'>"
                                }
                                related_products += "<div class='card prod_card'>";
                                    related_products += "<img src='"+response['related_products'][i].product_img_url+"' class='card_img card-img-top'>"
                                    related_products += "<div class='card-body'>";
                                        related_products += "<span>"+response['related_products'][i].product_name+"</span><br>";
                                        related_products += "<span>Rent: "+response['related_products'][i].product_rent+"</span><br><span>Deposit: "+response['related_products'][i].product_deposite+"</span>";
                                    related_products += "</div>";
                                related_products += "</div>";
                                if(i == 3 || i == 7 || i== 11 )
                                {                                    
                                    related_products += "</div><div class='card-deck my-2'>"
                                }
                            }
                            related_products += "</div>";
                            // console.log(related_products);
                            $(".prod_card_deck").append(related_products);
                        }
                        // console.log(response);
                    }
                });
            }
            function addProduct()
            {
                let product_id = $("#eqipments").val();
                var el = document.getElementById('eqipments');
                var text = el.options[el.selectedIndex].innerHTML;
                let id = $('#add_product_btn').val();
                let span = "<span class='btn btn-sm btn-outline-primary' id='span"+id+"'>"+text+" <span onclick='removeProduct(this.id);' id='"+id+"'><i class='fas fa-window-close'></i></span><input type='hidden' id='equipment_id"+id+"' name='equipment_id[]' value='"+product_id+"'></span>";
                // console.log(span);
                $(".products_display").append(span);
                $('#add_product_btn').val(parseInt(id)+1);
                $("#add_product").modal("hide");
            }
            $('input[name="customer_type"]').change(function(){
                let customer_type = $('input[name="customer_type"]:checked').val();
                
                if($('input[name="customer_type"]:checked').val() == 'Corporate'){
                    $("#def_patient").show();
                }   
                else{
                    $("#def_patient").hide();
                }
                
                if(customer_type == "Individual")
                {
                    $("#corporate_contacts").hide();
                }
                else if(customer_type == "Corporate")
                {
                    $("#corporate_contacts").show();
                }
            });
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