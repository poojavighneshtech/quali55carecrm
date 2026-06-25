<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="{{url('/')}}/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
        <link
            href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
            rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.min.css"/>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
        <!-- Custom styles for this template-->
        <link href="{{url('/')}}/assets/css/sb-admin-2.min.css" rel="stylesheet">   
        <script src="{{url('/')}}/assets/vendor/jquery/jquery.min.js"></script>
        <script src="{{url('/')}}/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="{{url('/')}}/assets/vendor/jquery/jquery.cookie.js" type="text/javascript"></script>

        <!-- Core plugin JavaScript-->
        <script src="{{url('/')}}/assets/vendor/jquery-easing/jquery.easing.min.js"></script>

        <!-- Custom scripts for all pages-->
        <script src="{{url('/')}}/assets/js/sb-admin-2.min.js"></script>
        
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
        {{-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script> --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="{{url('/')}}/assets/js/jquery.table2excel.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js" ></script>
</head>
    <body>
        <div class="row">
            <div class="col-md-12 text-center">
                <img src="{{url('/')}}/assets/images/logo_small.png" alt="">
            </div>
        </div>
        <div class="card">
            <div class="card-header text-center" style="background-color: #337ab7; color: white;">
                <span><b>Customer Details</b></span>
            </div>
            <div class="card-body">
                <form class="form" method="POST" action="{{url('/')}}/create_lead_link/{{$link_id}}" id="create_lead">
                    @csrf
                    <div class="row form-group">
                        @if(session()->has('message'))
                            <div class="alert alert-success">
                                {{ session()->get('message') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                    </div>
                    <div class="row form-group">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="customer_name">Customer Name*</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" placeholder="Customer Name..." id="inpt_customer_name" name="customer_name" value="{{old('customer_name',$customer_name)}}" 
                                        @if(isset($customer_details)) readonly @endif required>
                                        <input type="hidden" name="link_created_by" value="{{$link_created_by}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="cust_name">Customer Contact*</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="customer_no" value="{{old('customer_no',$customer_no)}}" readonly>
                                    @if ($errors->has('customer_no'))
                                        <span class="text-danger">{{ $errors->first('customer_no') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="patient_name">Patient Name</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="patient_name" placeholder="Patient Name" value="{{old('patient_name')}}">
                                    
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="patient_age">Patient Age</label>
                                </div>
                                <div class="col-md-4">
                                    <input type="number" id="patient_age" class="form-control" name="patient_age" min="1" value="{{old('patient_age')}}">
                                        @if ($errors->has('patient_age'))
                                            <span class="text-danger">{{ $errors->first('patient_age') }}</span>
                                        @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="address_line_1">Address Line 1</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="address_line_1" name="address_line_1" placeholder="Line 1"
                                        value="@if(isset($customer_details)){{old('address_line_1',$customer_details[0]->address_line_1)}}@else{{old('address_line_1')}}@endif" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="address_line_2">Address Line 2</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="address_line_2" name="address_line_2" placeholder="Line 2"
                                        value="@if(isset($customer_details)){{old('address_line_2',$customer_details[0]->address_line_2)}}@else{{old('address_line_2')}}@endif">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="landmark">Landmark</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="landmark"name="landmark" placeholder="Landmark"
                                        value="@if(isset($customer_details)){{old('landmark',$customer_details[0]->landmark)}}@else{{old('landmark')}}@endif" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="area">Area</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="area" name="area" placeholder="Area" 
                                        value="@if(isset($customer_details)){{old('area',$customer_details[0]->area)}}@else{{old('area')}}@endif" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="location">Location*</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" placeholder="Select Location" list="city" id="location" name="location" 
                                        value="@if(isset($customer_details)){{old('location',$customer_details[0]->location)}}@else{{old('location')}}@endif" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="pincode">Pincode*</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" placeholder="Pincode" id="pincode" name="pincode" 
                                        value="@if(isset($customer_details)){{old('pincode',$customer_details[0]->pincode)}}@else{{old('pincode')}}@endif" required>
                                        @if ($errors->has('pincode'))
                                            <span class="text-danger">{{ $errors->first('pincode') }}</span>
                                        @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="city">City</label>
                                </div>
                                <div class="col-md-9">
                                    <select class="form-control selectpicker" id="city" name="city" placeholder="" >
                                        <option value="Mumbai" @if(isset($customer_details) && $customer_details[0]->city=='Mumbai') selected @endif>Mumbai</option>
                                        <option value="Pune" @if(isset($customer_details) && $customer_details[0]->city=='Pune') selected @endif>Pune</option>
                                        <option value="Navi Mumbai" @if(isset($customer_details) && $customer_details[0]->city=='Navi Mumbai') selected @endif>Navi Mumbai</option>
                                        <option value="Thane" @if(isset($customer_details) && $customer_details[0]->city=='Thane') selected @endif>Thane</option>
                                        <option value="Mira Bhaindar" @if(isset($customer_details) && $customer_details[0]->city=='Mira Bhaindar') selected @endif>Mira Bhaindar</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="state">State</label>
                                </div>
                                <div class="col-md-9">
                                    <select class="selectpicker form-control" title="State" name="state" data-live-search="true" required="true">
                                        @foreach ($states as $state) 
                                            <option value="{{$state->name}}"@if($state->name == 'Maharashtra') selected @endif>{{$state->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="eqipments">Alternate No</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="secondary_contact_no"  name="secondary_contact_no" 
                                        placeholder="Mobile Number (Secondary)"  maxlength="10" value="{{old('secondary_contact_no')}}">
                                    @if ($errors->has('secondary_contact_no'))
                                        <span class="text-danger">{{ $errors->first('secondary_contact_no') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="email">Email Id</label>
                                </div>
                                <div class="col-md-9">
                                    <input class="form-control" type="email" name="email_id" id="inpt_email_id" placeholder="Email Id" value="{{old('email_id')}}">
                                    @if ($errors->has('email_id'))
                                        <span class="text-danger">{{ $errors->first('email_id') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                       
                    </div>
                    <div class="row form-group">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="eqipments">Product Required</label>
                                </div>
                                <div class="col-md-9">
                                    <span><strong>{{$default_products}}</strong></span>
                                    {{-- <select class="form-control js-example-responsive" name="default_equipments[]" id="select_equipment" multiple="multiple"  tyle="width: 100%" disabled required>
                                        @foreach ($required_products as $key=>$product) 
                                            <option value="{{$product}}" selected >{{$products[$product]->product_name}}</option>
                                        @endforeach
                                    </select> --}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="eqipments">Additional Products</label>
                                </div>
                                <div class="col-md-9">
                                    <select class="form-control js-example-responsive" name="additional_equipments[]" id="select_additional_equipment" multiple="multiple"  tyle="width: 100%">
                                        @foreach ($products as $product) 
                                            <option value="{{$product->id}}">{{$product->product_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="refered_by">Referred by</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="refered_by" name="refered_by" placeholder="Referred By" value="{{old('refered_by')}}">
                                </div>
                            </div>
                        </div> 
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="doctor_name">Doctor Name</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="doctor_name" placeholder="Doctor Name" value="{{old('doctor_name')}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="hospital_name">Hospital Name</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="hospital_name" placeholder="Hospital Name" value="{{old('hospital_name')}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <p>Quali5care Contact Person :{{$username}}(<a href="tel:{{$lead_own_contact}}">{{$lead_own_contact}}</a>)</p>
                        </div>
                    </div>
                    <div class="row form-group justify-content-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="terms_and_condition" id="check_terms_and_condition" required>
                            <label class="form-check-label" for="check_terms_and_condition">
                                I Agree <a href="{{url('/')}}/assets/terms_condition/terms and condition.pdf" target="_blank" >Terms & Coditions</a> 
                            </label> 
                            <br>
                            @if ($errors->has('terms_and_condition'))
                            <span class="text-danger">{{ $errors->first('terms_and_condition') }}</span>
                        @endif
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-4"></div>
                        <div class="col-md-4 text-center">
                            <button type="submit" class="btn btn-outline-success btn-block" name="lead_submit">Submit</button>
                        </div>
                        <div class="col-md-4"></div>
                    </div>
                </form>
            </div>
        </div>
        <script>
            $(document).ready(function() {
                $('#select_equipment').select2({
                    width: 'resolve',
                    placeholder: 'Select product',
                    allowClear: true
                });
            });
            $(document).ready(function() {
                $('#select_additional_equipment').select2({
                    width: 'resolve',
                    placeholder: 'Select products',
                    allowClear: true
                });
            });
        </script>

        
    </body>
</html>