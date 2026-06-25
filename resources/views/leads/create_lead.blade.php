<!DOCTYPE html>
<html lang="en">
    @extends('header_and_sidebar')
    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Inquiry : Create Lead</title>
        @section('styles')
        {{-- stylesheets --}}
        {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.2/css/bootstrap.min.css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.min.css"/>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" /> --}}
        {{-- Scripts --}}
        {{-- <script src="{{url('/')}}/assets/vendor/jquery/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyD4X-Vj_g11S7a_-16GC9BHkvFMPZCSPew"></script> --}}

        <style>
            .scrollable_card{
                overflow-y: auto;
                max-height: 400px;
            }
        </style>
        
        @endsection
    </head>
    <body id="page-top">	
            <!-- Page Wrapper -->
        @section('breadcrumb_item')
            <li class="breadcrumb-item active" aria-content="page">Create New Lead</li>
        @endsection
                @section('content')
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header text-center" style="background-color: #337ab7; color: white;">
                                <span><b>New Inquiry</b></span>
                            </div>
                            <div class="card-body"><!--style="min-height: 0; max-height: 5; overflow-y: scroll;"-->
                                <form class="form doublePost"  method="POST" action="<?php echo url('/');?>/create_lead" id="create_lead">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="previous_url" value="{{url()->previous()}}">
                                    <input type="hidden" name="customer_id" id = "cust_id"<?php if(isset($customer_details[0]['cust_id'])){ echo"value='".$customer_details[0]['cust_id']."'";}?>>
                                    <input type="hidden" name="corporate_cust_id" id = "corporate_cust_id"<?php if(isset($customer_details[0]['corporate_cust_id'])){ echo"value='".$customer_details[0]['corporate_cust_id']."'";}?>>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="creation_date">Date</label>
                                            <input class="form-control" type="date" name="creation_date" id="creation_date" value="{{date('Y-m-d')}}" required>
                                        </div>
                                        {{-- <div class="col-md-1">
                                            <label for="creation_date">Date</label>
                                        </div>
                                        <div class="col-md-3">
                                            <input class="form-control" type="date" name="creation_date" id="creation_date" value="{{date('Y-m-d')}}" required>
                                        </div> --}}
                                        
                                        <div id="primary_contact" class="col-md-4">
                                            <label for="primary_contact_no">Contact Number</label>
                                            <input type="text" class="form-control" id="primary_contact_no" <?php if(isset($customer_details[0]['primary_contact_no'])){echo"value='".$customer_details[0]['primary_contact_no']."'";}?> name="primary_contact_no" placeholder="Mobile Number (Primary)" oninput="numberOnly(this.id);" maxlength="10" required="true">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="customer_type">Customer Type:</label>
                                            <div class="btn-group btn-group-toggle btn-block btn-sm" data-toggle="buttons">
                                                <label class="btn btn-outline-success btn-sm active" id="radio_individual_lbl">
                                                    <input type="radio" name="customer_type" id="radio_individual" checked value="Individual"> Individual
                                                </label>
                                                <label class="btn btn-outline-success btn-sm" id="radio_corporate_lbl">
                                                    <input type="radio" name="customer_type" id="radio_corporate" value="Corporate"> Corporate
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div id="buttons" class="col-md-12 text-center">
                                            <button class="btn btn-primary" type="button" id="submitbtn" name="submitbtn">Submit</button>
                                            <button class="btn btn-secondary" type="reset" id="reset1" name="reset">Clear</button>
                                            <a href="#" id="prev_leads" class="btn btn-primary" style="display:none;">Prev Leads</a>
                                        </div>
                                    </div>
                                    <div class="details" style="display:none;">
                                        <hr>
                                        <div class="row form-group">
                                            <div class="col-md-5">
                                                <div class="row">
                                                    <div class="col-md-5">
                                                        <label for="cust_name">Customer Name*</label>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <input type="text" class="form-control" <?php if(isset($customer_details[0]['customer_name'])){echo"value='".$customer_details[0]['customer_name']."'";}?> id="cust_name" name="cust_name" placeholder="Customer Name" required="true">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="row">
                                                    <div class="col-md-5">
                                                        <label for="patient_name">Patient Name</label>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <input type="text" class="form-control" <?php if(isset($customer_details[0]['patient_name'])){ echo"value='".$customer_details[0]['patient_name']."'";}?> name="patient_name" id="patient_name" placeholder="Patient Name">
                                                        <input type="hidden" name="hidden_def_patient_id" id="hidden_def_patient_id">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-sm btn-outline-primary" style="display:none;" name="def_patient" id="def_patient">Auto</button>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label for="patient_age">Patient Age</label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="text" id="patient_age" class="form-control" <?php if(isset($customer_details[0]['patient_age'])){ echo"value='".$customer_details[0]['patient_age']."'";}?> name="patient_age" oninput="numberOnly(this.id);" maxlength="3">
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
                                                            <label class="btn btn-outline-secondary btn-sm active" id="radio_lbl_cust_male">
                                                                <input type="radio" name="customer_gender" id="radio_cust_male" value="Male" required> Male
                                                            </label>
                                                            <label class="btn btn-outline-secondary btn-sm" id="radio_lbl_cust_female">
                                                                <input type="radio" name="customer_gender" id="radio_cust_female" value="Female" required> Female
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
                                                            <label class="btn btn-outline-secondary btn-sm active">
                                                                <input type="radio" name="patient_gender" id="radio_patient_male" value="Male"> Male
                                                            </label>
                                                            <label class="btn btn-outline-secondary btn-sm ">
                                                                <input type="radio" name="patient_gender" id="radio_patient_female" value="Female"> Female
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
                                                        {{-- <select class="selectpicker form-control" id="location"  title="Select Location" name="location" data-live-search="true" required>
                                                            @foreach ($cities as $city) 
                                                                <option value="{{$city['name']}}">{{$city['name']}}</option>
                                                            @endforeach
                                                        </select> --}}
                                                        <input type="text" class="form-control" placeholder="Select Location" list="city" id="location" name="location" required>
                                                        <datalist id="city">
                                                            @foreach ($cities as $city) 
                                                                <option value="{{$city['name']}}">{{$city['name']}}</option>
                                                            @endforeach
                                                        </datalist>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="select_container">

                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-5">
                                                        <label for="eqipments">Equipment Required*</label>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <div class="row form-group">
                                                            <div class="products_display">
                                                                {{-- <span class="btn btn-sm btn-outline-primary">Walker Without Wheel <span onclick="removeProduct(this.id);" id="1"><i class="fas fa-window-close"></i></span></span>
                                                                <span class="btn btn-sm btn-outline-primary">Walker With Wheel  <span onclick="removeProduct(this.id);" id="2"><i class="fas fa-window-close"></i></span></span> --}}
                                                            </div>
                                                        </div>
                                                        <span class="text-danger" id="least_prod" style="display:none;">Select atleast One Product</span>
                                                        <div class="row justify-content-start form-group">
                                                            <button type="button" name="add_product_btn" id="add_product_btn" class="btn btn-sm btn-outline-warning" value="1" onclick="selectProduct();">Add Product</button>
                                                        </div>
                                                        {{-- <select class="selectpicker products form-control" id="equipments" title="Select Products From Dropdown" name="eqipments[]" multiple data-live-search="true" required>
                                                            @foreach ($products as $product) 
                                                                <option value="{{$product['id']}}">{{$product['product_name']}}</option>
                                                            @endforeach
                                                        </select> --}}
                                                        {{-- <select class="equipments form-control" name="eqipments[]" multiple="multiple" style="width: 110%" required>
                                                            @foreach ($products as $product) 
                                                                <option value="{{$product['id']}}">{{$product['product_name']}}</option>
                                                            @endforeach
                                                        </select> --}}
                                                    </div>
                                                    {{-- <div class="col-md-7 content">
                                                        <select class="equipments form-control" name="eqipments[]" multiple="multiple" style="width: 110%" required>
                                                            @foreach ($products as $product) 
                                                                <option value="{{$product['id']}}">{{$product['product_name']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div> --}}
                                                </div>
                                            </div>
                                        </div>
                                        {{--city state mandatory--}}
                                        <hr>
                                        <div class="row form-group">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="city">City</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" id="city"<?php if(isset($customer_details[0]['city'])){ echo"value='".$customer_details[0]['city']."'";}else{echo "value='Mumbai'";}?> name="city" placeholder="City" required="true">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="state">State</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <select class="selectpicker form-control" title="State" name="state" data-live-search="true" required="true">
                                                            @foreach ($states as $state) 
                                                                <option value="{{$state['name']}}"@if($state['name'] == 'Maharashtra'){{"selected"}} @endif>{{$state['name']}}</option>
                                                            @endforeach
                                                        </select>
                                                        {{-- <input type="text" class="form-control" placeholder="Select Location" list="location">
                                                        <datalist id="location">
                                                            @foreach ($cities as $city) 
                                                                <option value="{{$city['name']}}">{{$city['name']}}</option>
                                                            @endforeach
                                                        </datalist> --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row form-group">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="refered_by">Refered by</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" id="refered_by" <?php if(isset($customer_details[0]['refered_by'])){ echo"value='".$customer_details[0]['refered_by']."'";}?> name="refered_by" placeholder="Refered By">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="lead_source">Lead Source*</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        {{-- <input type="text" class="form-control" id="lead_source" <?php if(isset($customer_details[0]['lead_source'])){ echo"value='".$customer_details[0]['lead_source']."'";}else{echo "value='Google Ads'"; }?> name="lead_source" placeholder="Lead Source (Google, JustDial, Marketing)" required="true"> --}}
                                                        <select class="select selectpicker" title="Select Lead Source" name="lead_source" id="lead_source" required>
                                                            @foreach($lead_source as $key=>$value)
                                                                <option value="{{$value}}">{{$value}}</option>
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
                                                        <label for="lead_owner">Lead Owner*</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <select class="selectpicker form-control" id="lead_owner" title="Select Owner" name="lead_owner" data-live-search="true" required>
                                                            @foreach ($users as $user) 
                                                                <option value="{{$user['id']}}"@if ($user['username'] == session('username')) {{"selected"}} @endif>{{$user['username']}}</option>
                                                            @endforeach
                                                        </select>
                                                        {{-- <input type="text" class="form-control" placeholder="Select Location" list="location">
                                                        <datalist id="location">
                                                            @foreach ($cities as $city) 
                                                                <option value="{{$city['name']}}">{{$city['name']}}</option>
                                                            @endforeach
                                                        </datalist> --}}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="lead_value">Lead Value*</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="number" class="form-control" id="lead_value" name="lead_value" placeholder="Lead Value" required="true">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div id="corporate_contacts" class="row form-group" style="display:none;">
                                            <div class="col-md-3">
                                                <label for="contact_person_1_name">Corporate Contact Person 1</label>
                                                <input type="text" class="form-control" id="contact_person_1_name" <?php if(isset($customer_details[0]['contact_person_1_name'])){echo"value='".$customer_details[0]['contact_person_1_name']."'";}?> name="contact_person_1_name" placeholder="First Contact Person Name"required="true">
                                                <input type="hidden" class="form-control" id="contact_person_1_name1" <?php if(isset($customer_details[0]['contact_person_1_name'])){echo"value='".$customer_details[0]['contact_person_1_name']."'";}?> name="contact_person_1_name" disabled>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="contact_person_1_no">Contact Number</label>
                                                <input type="text" class="form-control" id="contact_person_1_no" <?php if(isset($customer_details[0]['contact_person_1_no'])){echo"value='".$customer_details[0]['contact_person_1_no']."'";}?> name="contact_person_1_no" placeholder="First Contact Person Number" oninput="numberOnly(this.id);" maxlength="10" required="true">
                                                <input type="hidden" class="form-control" id="contact_person_1_no1" <?php if(isset($customer_details[0]['contact_person_1_no'])){echo"value='".$customer_details[0]['contact_person_1_no']."'";}?> name="contact_person_1_no" placeholder="First Contact Person Number" disabled>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="contact_person_2_name">Corporate Contact Person 2</label>
                                                <input type="text" class="form-control" id="contact_person_2_name" <?php if(isset($customer_details[0]['contact_person_2_name'])){echo"value='".$customer_details[0]['contact_person_2_name']."'";}?> name="contact_person_2_name" placeholder="Second Contact Person Name">
                                                <input type="hidden" class="form-control" id="contact_person_2_name1" <?php if(isset($customer_details[0]['contact_person_2_name'])){echo"value='".$customer_details[0]['contact_person_2_name']."'";}?> name="contact_person_2_name" disabled>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="contact_person_2_no">Contact Number</label>
                                                <input type="text" class="form-control" id="contact_person_2_no" <?php if(isset($customer_details[0]['contact_person_2_no'])){echo"value='".$customer_details[0]['contact_person_2_no']."'";}?> name="contact_person_2_no" placeholder="Second Contact Person Number" oninput="numberOnly(this.id);" maxlength="10">
                                                <input type="hidden" class="form-control" id="contact_person_2_no1" <?php if(isset($customer_details[0]['contact_person_2_no'])){echo"value='".$customer_details[0]['contact_person_2_no']."'";}?> name="contact_person_2_no" placeholder="Second Contact Person Number" disabled>
                                            </div>
                                            <hr>
                                        </div>
                                        {{-- <div class="row form-group">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="customer_type">Customer Type:</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="btn-group btn-group-toggle btn-block btn-sm" data-toggle="buttons">
                                                            <label class="btn btn-outline-success btn-sm active">
                                                                <input type="radio" name="customer_type" id="radio_individual" checked value="Individual"> Individual
                                                            </label>
                                                            <label class="btn btn-outline-success btn-sm ">
                                                                <input type="radio" name="customer_type" id="radio_corporate" value="Corporate"> Corporate
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> --}}
                                        <br>
                                        <center>
                                            <button class="btn btn-primary" type="submit" id="submit1" name="submit" <?php if(isset($customer_details[0]['customer_name'])){echo "value='check'";}else{echo "value='submit'";}?>>Submit</button>
                                            <button class="btn btn-default" type="reset" id="reset2" name="reset">Clear</button>
                                        </center>

                                        
                                        <hr/>
                                        <center><b>Equipments</b></center>
                                        <hr/>
                                        <div class="row align-items-center">
                                            <!-- Billing Type -->
                                            <div class="col-md-2">
                                                <label><strong>Billing Type</strong></label>
                                                <select class="form-control" id="billing_type" name="billing_type">
                                                    <option value="RENT">RENT</option>
                                                    <option value="SALE">SALE</option>
                                                </select>
                                            </div>
                                    
                                            <!-- Period -->
                                            <div class="col-md-2">
                                                <label><strong>Period</strong></label>
                                                <select class="form-control" id="period" name="period">
                                                    <option value="1_week">1 week</option>
                                                    <option value="1_month">1 month</option>
                                                    <option value="3_month">3 month</option>
                                                    <option value="6_month">6 month</option>
                                                    <option value="1_year">1 year</option>
                                                </select>
                                            </div>
                                    
                                            <!-- Quantity -->
                                            <div class="col-md-2">
                                                <label><strong>Quantity</strong></label>
                                                <div class="input-group">
                                                    <button type="button" class="btn btn-danger" id="decreaseQty">-</button>
                                                    <input type="text" class="form-control text-center" id="quantity" name="quantity" value="1" readonly>
                                                    <button type="button" class="btn btn-success" id="increaseQty">+</button>
                                                </div>
                                            </div>
                                    
                                            <!-- Product -->
                                            <div class="col-md-4">
                                                <label><strong>Product</strong></label>
                                                <select class="selectpicker form-control" data-live-search="true" id="product" name="product" title="Search Product">
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product['id'] }}">{{ $product['product_name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                    
                                            <!-- Add Button -->
                                            <div class="col-md-2 text-center">
                                                <label><strong>&nbsp;</strong></label><br>
                                                <button type="button" class="btn btn-success w-100" id="addProduct">
                                                    <i class="fa fa-plus"></i> Add
                                                </button>
                                            </div>
                                        </div>
                                        

<!-- Optional JS for Quantity Control -->
<script>
document.getElementById('increaseQty').addEventListener('click', function() {
    let qty = document.getElementById('quantity');
    qty.value = parseInt(qty.value) + 1;
});

document.getElementById('decreaseQty').addEventListener('click', function() {
    let qty = document.getElementById('quantity');
    if (parseInt(qty.value) > 1) {
        qty.value = parseInt(qty.value) - 1;
    }
});
</script>




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
                                                        <input type="text" class="form-control" id="address_line_1"<?php if(isset($customer_details[0]['address_line_1'])){ echo"value='".$customer_details[0]['address_line_1']."'";}?> name="address_line_1" placeholder="Line 1">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="address_line_2">Line 2</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" id="address_line_2"<?php if(isset($customer_details[0]['address_line_2'])){ echo"value='".$customer_details[0]['address_line_2']."'";}?> name="address_line_2" placeholder="Line 2">
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
                                                        <input type="text" class="form-control" id="landmark" <?php if(isset($customer_details[0]['landmark'])){ echo"value='".$customer_details[0]['landmark']."'";}?> name="landmark" placeholder="Landmark">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="area">Area</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" id="area" <?php if(isset($customer_details[0]['area'])){ echo"value='".$customer_details[0]['area']."'";}?> name="area" placeholder="Area">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row form-group">
                                            {{-- <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="city">City</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" id="city"<?php if(isset($customer_details[0]['city'])){ echo"value='".$customer_details[0]['city']."'";}else{echo "value='Mumbai'";}?> name="city" placeholder="City">
                                                    </div>
                                                </div>
                                            </div> --}}
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="pincode">Pin Code</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" id="pincode"<?php if(isset($customer_details[0]['pincode'])){ echo"value='".$customer_details[0]['pincode']."'";}?> name="pincode" oninput="numberOnly(this.id);" maxlength="6" placeholder="Pincode">
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
                                                            @foreach ($countries as $country) 
                                                                <option value="{{$country['name']}}" @if($country['name'] == 'India'){{"selected"}} @endif>{{$country['name']}}</option>
                                                            @endforeach
                                                        </select>
                                                        {{-- <input type="text" class="form-control" placeholder="Select Location" list="location">
                                                        <datalist id="location">
                                                            @foreach ($cities as $city) 
                                                                <option value="{{$city['name']}}">{{$city['name']}}</option>
                                                            @endforeach
                                                        </datalist> --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            {{-- <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="state">State</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <select class="selectpicker form-control" title="State" name="state" data-live-search="true">
                                                            @foreach ($states as $state) 
                                                                <option value="{{$state['name']}}"@if($state['name'] == 'Maharashtra'){{"selected"}} @endif>{{$state['name']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div> --}}
                                            
                                        </div>
                                        <hr>
                                        <div class="row form-group">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="email">Email Id</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="email" class="form-control" id="email_id" <?php if(isset($customer_details[0]['email_id'])){ echo"value='".$customer_details[0]['email_id']."'";}?> name="email" placeholder="Email">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="eqipments">Mobile Number(Secondary)</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" id="secondary_contact_no" <?php if(isset($customer_details[0]['secondary_contact_no'])){ echo"value='".$customer_details[0]['secondary_contact_no']."'";}?> name="secondary_contact_no" placeholder="Mobile Number (Secondary)" oninput="numberOnly(this.id);" maxlength="10">
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
                                                        <input type="text" class="form-control" <?php if(isset($customer_details[0]['doctor_name'])){ echo"value='".$customer_details[0]['doctor_name']."'";}?> name="doctor_name" placeholder="Doctor Name">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="hospital_name">Hospital Name</label>
                                                    </div>
                                                    <div class="col-md-8">
                                                            <input type="text" class="form-control" <?php if(isset($customer_details[0]['hospital_name'])){ echo"value='".$customer_details[0]['hospital_name']."'";}?> name="hospital_name" placeholder="Hospital Name">
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
                                                        <input type="text" class="form-control" <?php if(isset($customer_details[0]['therapeutic_requirement'])){ echo"value='".$customer_details[0]['therapeutic_requirement']."'";}?> name="therapeutic_requirement" placeholder="Therapeutic Requirement">
                                                    </div>                                        
                                                </div>
                                            </div>
                                        </div>
                                        <center>
                                            <button class="btn btn-primary" type="submit" id="submit2" name="submit" <?php if(isset($customer_details[0]['customer_name'])){echo "value='check'";}else{echo "value='submit'";}?>>Submit</button>
                                            <button class="btn btn-default" type="reset" id="reset3" name="reset">Clear</button>
                                        </center>
                                    </div>
                                </form>
                                <br>
                            </div>
                        </div>
                    </div>
                </div>
                @endsection
                <div class="modal fade" id="add_product" tabindex="-1" role="dialog" aria-labelledby="add_productLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="add_productLabel">Add Product</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body  scrollable_card ">
                                <select class="select selectpicker selectto form-control" data-live-search="true" onchange="fetchProductDetails(this.value)" id="eqipments" name="eqipments" title="Select Product" style="width: 110%" required>
                                    @foreach ($products as $product) 
                                        <option value="{{$product['id']}}">{{$product['product_name']}}</option>
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
                            <span aria-hidden="true"></span>
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
        //SELECT PICKER operation handler ........                                                        
            //$('select').selectpicker();
            // $(document).ready(function(){
            //     $("form").submit(function(){
            //         alert("Are you sure to submit ?");
            //     });
            // });
            $(document).ready(function() {
                $('.equipments').select2({
                    theme: "classic",
                    placeholder: 'Select Products',
                    allowClear: true
                });
                $("#submit1").show();
                $("#submit2").show();            
                $("#reset2").show();
                $("#reset3").show();
            });
            // $("#submit1").on('click', function(e){
            //     $("#submit1").hide();
            // });
            // $("#submit2").on('click', function(e){
            //     $("#submit2").hide();
            // });
            // $("#submit1").on('click', function(e){
            //     $("#reset2").hide();
            // });
            // $("#submit2").on('click', function(e){
            //     $("#reset3").hide();
            // });
            //----------------Real time search custoomer details for autofill text in create lead-------------------//
            $('input[name="customer_type"]').change(function(){
                $('.details').hide();
                $('#location').selectpicker('val',false);
                $('#state').selectpicker('val',false);
                $('#country').selectpicker('val',false);
                $('#equipments').selectpicker('val',false);
                if($('input[name="customer_type"]:checked').val() == 'Corporate'){
                    $("#def_patient").show();
                }   
                else{
                    $("#def_patient").hide();
                }
                // $('#lead_owner').selectpicker('val',false);
                $('#cust_name').attr('readonly',false);
                document.getElementById("primary_contact").classList.remove('col-md-3');
                document.getElementById("buttons").classList.remove('col-md-1');
                document.getElementById("primary_contact").classList.add('col-md-1');
                document.getElementById("buttons").classList.add('col-md-3');
                $('#prev_leads').hide();
            });
            $("#submitbtn").on("click", function (e) 
            {
                $('.details').show();
                var primary_contact_no = $("#primary_contact_no").val();                
                let customer_type = $('input[name="customer_type"]:checked').val();
                let dataString = ({_token:"{{ csrf_token() }}",primary_contact_no:""+primary_contact_no,customer_type:""+customer_type});                
                $.ajax({
                    type: "GET",
                    url: "<?php echo url('/');?>/findCustomer",
                    data: dataString,
                    cache: false,
                    success: function(data)
                    {              
                        var obj = jQuery.parseJSON(data);
                        // console.log(data);
                        var i=0;
                        if(obj.cust_type == 'Corporate')
                        {
                            $("#radio_corporate_lbl").addClass('active');
                            $("#radio_corporate").attr('checked',true);
                            $("#radio_individual").removeAttr('checked');
                            $("#radio_individual_lbl").removeClass('active');
                            $("#radio_individual").attr('disabled',true);
                        }
                        if(obj.cust_type == 'Individual')
                        {
                            $("#radio_individual_lbl").addClass('active');
                            $("#radio_individual").attr('checked',true);
                            $("#radio_corporate").removeAttr('checked');
                            $("#radio_corporate_lbl").removeClass('active');
                        }
                        let customer_type1 = $('input[name="customer_type"]:checked').val();
                        if(customer_type1 == "Individual")
                        {
                            $('#cust_id').val(obj.customer_id);
                            $("#corporate_contacts").hide();
                            $("#contact_person_1_name").val(null);
                            $("#contact_person_1_no").val(null);
                            $("#contact_person_2_name").val(null);
                            $("#contact_person_2_no").val(null);
                            $("#contact_person_1_name1").val(null);
                            $("#contact_person_1_no1").val(null);
                            $("#contact_person_2_name1").val(null);
                            $("#contact_person_2_no1").val(null);
                            $("#contact_person_1_name").removeAttr('required');
                            $("#contact_person_1_no").removeAttr('required');
                        }
                        else if(customer_type1 == "Corporate")
                        {
                            $('#corporate_cust_id').val(obj.customer_id);
                            $("#corporate_contacts").show();
                            // alert(obj.contact_person_1_name);
                            if(obj.contact_person_1_name !=null && obj.contact_person_1_name !="")
                            {
                                $("#contact_person_1_name").val(obj.contact_person_1_name);
                                $("#contact_person_1_no").val(obj.contact_person_1_no);
                                $("#contact_person_1_name1").val(obj.contact_person_1_name);
                                $("#contact_person_1_no1").val(obj.contact_person_1_no);
                                $("#contact_person_1_name").attr('disabled',true);
                                $("#contact_person_1_no").attr('disabled',true);
                                $("#contact_person_1_name1").removeAttr('disabled');
                                $("#contact_person_1_no1").removeAttr('disabled');
                            }
                            else
                            {
                                $("#contact_person_1_name").removeAttr('disabled');
                                $("#contact_person_1_no").removeAttr('disabled');
                                $("#contact_person_1_name").attr('required',true);
                                $("#contact_person_1_no").attr('required',true);
                                $("#contact_person_1_name1").attr('disabled',true);
                                $("#contact_person_1_no1").attr('disabled',true);
                            }
                            if(obj.contact_person_2_name !=null && obj.contact_person_2_name !="")
                            {
                                $("#contact_person_2_name").val(obj.contact_person_2_name);
                                $("#contact_person_2_no").val(obj.contact_person_2_no);
                                $("#contact_person_2_name1").val(obj.contact_person_2_name);
                                $("#contact_person_2_no1").val(obj.contact_person_2_no);
                                $("#contact_person_2_name").attr('disabled',true);
                                $("#contact_person_2_no").attr('disabled',true);
                                $("#contact_person_2_name1").removeAttr('disabled');
                                $("#contact_person_2_no1").removeAttr('disabled');
                            }
                            else
                            {
                                $("#contact_person_2_name").removeAttr('disabled');
                                $("#contact_person_2_no").removeAttr('disabled');
                                $("#contact_person_2_name1").attr('disabled',true);
                                $("#contact_person_2_no1").attr('disabled',true);
                                // $("#contact_person_2_name").attr('required',true);
                            }
                            // $("#contact_person_1_no")attr('required',true);
                            // $("#contact_person_2_name").val(obj.contact_person_2_name);
                            // $("#contact_person_2_no").val(obj.contact_person_2_no);
                        }
                        $('#cust_name').val(obj.customer_name);
                        $('#address_line_1').val(obj.address_line_1);
                        $('#address_line_2').val(obj.address_line_2);
                        $('#area').val(obj.area);
                        $('#landmark').val(obj.landmark);
                        $('#pincode').val(obj.pincode);
                        
                        if(obj.customer_name!=null)
                        {
                            if(customer_type1 == "Individual")
                            {
                                $('#cust_name').attr('readonly',true);
                                if(obj.cust_migrate == true)
                                {
                                    $('#submit1').val('submit');
                                    $('#submit2').val('submit');
                                }
                                else
                                {
                                    $('#submit1').val('check');
                                    $('#submit2').val('check');
                                }
                            }
                            else if(customer_type1 == "Corporate")
                            {
                                $('#cust_name').attr('readonly',false);
                                $('#submit1').val('submit');
                                $('#submit2').val('submit');
                            }
                            document.getElementById("primary_contact").classList.remove('col-md-3');
                            document.getElementById("buttons").classList.remove('col-md-2');
                            document.getElementById("primary_contact").classList.add('col-md-1');
                            document.getElementById("buttons").classList.add('col-md-3');
                            document.getElementById("prev_leads").href="<?php echo url('/');?>/view_cust_lead/"+obj.customer_id;
                            $('#prev_leads').show();
                        }
                        else
                        {
                            $('#cust_name').attr('readonly',false);
                            document.getElementById("primary_contact").classList.remove('col-md-3');
                            document.getElementById("buttons").classList.remove('col-md-1');
                            document.getElementById("primary_contact").classList.add('col-md-1');
                            document.getElementById("buttons").classList.add('col-md-3');
                            $('#submit1').val('submit');
                            $('#submit2').val('submit');
                            $('#prev_leads').hide();
                        }
                        if(obj.cust_gender!=null)
                        {
                            if(obj.cust_gender=='Male'){
                                //$('#radio_cust_male').removeAttr('required');
                                $('#radio_lbl_cust_male').addClass('active');
                                $('#radio_cust_male').attr('checked',true);
                            }else{
                                //$('#radio_cust_female').removeAttr('required');
                                $('#radio_lbl_cust_female').addClass('active');
                                $('#radio_cust_female').attr('checked',true);
                            }
                        }
                        $('#location').val(obj.location);
                        $('#location').selectpicker('val',obj.location);
                        $('select[name=selValue]').val(1);
                        $('#refered_by').val(obj.refered_by);
                    }
                });
            }); 
            $('#reset1').on('click', function(){
                $('.details').hide();
                $('#location').selectpicker('val',false);
                $('#state').selectpicker('val',false);
                $('#country').selectpicker('val',false);
                $('#equipments').selectpicker('val',false);
                // $('#lead_owner').selectpicker('val',false);
                $('#cust_name').attr('readonly',false);
                document.getElementById("primary_contact").classList.remove('col-md-3');
                document.getElementById("buttons").classList.remove('col-md-1');
                document.getElementById("primary_contact").classList.add('col-md-1');
                document.getElementById("buttons").classList.add('col-md-3');
                $('#prev_leads').hide();

                //default customer type
                $("#radio_individual").attr('checked',true);
                $("#radio_individual_lbl").addClass('active');
                $("#radio_individual").removeAttr('disabled');

                $("#radio_corporate_lbl").removeClass('active');
                $("#radio_corporate").removeAttr('checked');
            });
            $('#reset2').on('click', function(){
                $('.details').hide();
                $('#location').selectpicker('val',false);
                $('#state').selectpicker('val',false);
                $('#country').selectpicker('val',false);
                $('#equipments').selectpicker('val',false);
                // $('#lead_owner').selectpicker('val',false);
                $('#cust_name').attr('readonly',false);
                document.getElementById("primary_contact").classList.remove('col-md-3');
                document.getElementById("buttons").classList.remove('col-md-1');
                document.getElementById("primary_contact").classList.add('col-md-1');
                document.getElementById("buttons").classList.add('col-md-3');
                $('#prev_leads').hide();
                
                //default customer type
                $("#radio_individual").attr('checked',true);
                $("#radio_individual_lbl").addClass('active');
                $("#radio_individual").removeAttr('disabled');

                $("#radio_corporate_lbl").removeClass('active');
                $("#radio_corporate").removeAttr('checked');
            });
            $('#reset3').on('click', function(){
                $('.details').hide();
                $('#location').selectpicker('val',false);
                $('#state').selectpicker('val',false);
                $('#country').selectpicker('val',false);
                $('#equipments').selectpicker('val',false);
                // $('#lead_owner').selectpicker('val',false);
                $('#cust_name').attr('readonly',false);
                document.getElementById("primary_contact").classList.remove('col-md-3');
                document.getElementById("buttons").classList.remove('col-md-1');
                document.getElementById("primary_contact").classList.add('col-md-1');
                document.getElementById("buttons").classList.add('col-md-3');
                $('#prev_leads').hide();

                 //default customer type
                $("#radio_individual").attr('checked',true);
                $("#radio_individual_lbl").addClass('active');
                $("#radio_individual").removeAttr('disabled');

                $("#radio_corporate_lbl").removeClass('active');
                $("#radio_corporate").removeAttr('checked');
            });

            function numberOnly(id) {
                var element = document.getElementById(id);
                element.value = element.value.replace(/[^0-9]/gi, "");
            }
            $("form").submit(function(e){
                if($("#equipment_id1").val() == undefined || $("#equipment_id1").val() == null)
                {
                    e.preventDefault();
                    $("#least_prod").show();
                }
            });
        </script>
        @endsection

    </body>

</html>
