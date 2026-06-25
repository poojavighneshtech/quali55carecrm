@extends('header_and_sidebar')
<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
    
        <title>Create Lead</title>
        {{-- <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script> --}}
        @section('styles')
        <style>
            .select2 {
                width:100%!important;
            }    
        </style>
        @endsection
    </head>

<body id="page-top">	
    @section('content')
        <div class="leads">
            @if(session()->has('message'))
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>
            @endif
            @if(session()->has('message_delete'))
                <div class="alert alert-danger">
                    {{ session()->get('message_delete') }}
                </div>
            @endif
            
            <div class="card">
                <div class="card-header"  style="background-color: #4a6fdc; color: white;" >
                    <center>Create Lead</center>
                </div> 
                <div class="card-body">
                    <form action="{{url('/')}}/create_lead_ambulance" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="contact_no"><strong>Contact No :</strong></label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="contact_no" id="txt_contact_no" list
                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" maxlength="10" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="name"><strong>Customer Name :</strong></label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="customer_name" id="txt_patient_name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="price"><strong>Patient Name :</strong></label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="patient_name" id="patient_name">
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="col-md-4">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="price"><strong>Price :</strong></label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="price" id="txt_price" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" maxlength="10" required>
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="date"><strong>Date :</strong></label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="date" class="form-control" name="date" id="input_date" value="{{date('Y-m-d')}}"required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-md-5">
                                        <label for="waiting_time"><strong>Waiting Time :</strong></label>
                                    </div>
                                    <div class="col-md-7">
                                        <input type="time" class="form-control" name="waiting_time" id="input_waiting_time" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="ambulance_type"><strong>Ambulance Type:</strong></label>
                                    </div>
                                    <div class="col-md-8">
                                        <select class="form-control selecpticker" name="ambulance_type" id="select_ambulance_type" required>
                                            <option value="Cardic">Cardic</option>
                                            <option value="Non Cardic">Non Cardic</option>
                                            <option value="Covid 2019">Covid 2019</option>
                                            <option value="Ac">Ac</option>
                                            <option value="Non-Ac">Non-Ac</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-auto">
                                        <label for=""><strong>Customer Type </strong></label>
                                    </div>
                                    <div class="col-auto">
                                        <select  class="form-control" name="customer_type" id="customer_type" title="Customer Type">
                                            <option value="Corporate">Corporate</option>
                                            <option value="Individual">Individual</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="price"><strong>Price :</strong></label>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="price" id="txt_price" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" maxlength="10" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header text-center">
                                        <strong>Pickup Address</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="line_1"><strong>Line 1 :</strong></label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" placeholder="Line 1.." name="pickup_line_1" id="txt_line_1">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="line_1"><strong>Line 2 :</strong></label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" placeholder="Line 2.." name="pickup_line_2" id="txt_line_2">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="line_1"><strong>Landmark :</strong></label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" placeholder="Landmark" name="pickup_landmark" id="txt_landmark">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="line_1"><strong>Area :</strong></label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" placeholder="Area" name="pickup_area" id="txt_area">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="line_1"><strong>Location</strong></label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" placeholder="Location" name="pickup_location" id="txt_location" list="location_list">
                                                <datalist id="location_list">
                                                    @foreach($get_cities as $city)
                                                        <option value="{{$city['name']}}">{{$city['name']}}</option>
                                                    @endforeach
                                                </datalist>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="city"><strong>City</strong></label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" placeholder="city" name="pickup_city" id="txt_city" list="city_list" value="Mumbai">
                                                <datalist id="city_list">
                                                    <option value="Mumbai">Mumbai</option>
                                                    <option value="Pune">Pune</option>
                                                </datalist>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="line_1"><strong>Pincode</strong></label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" placeholder="Pincode" name="pickup_pincode" id="txt_pincode" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" maxlength="6">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="line_1"><strong>Email ID</strong></label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="email" class="form-control" placeholder="Email id.." name="pickup_email" id="txt_email">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-6">
                                                    <label for="state"><strong>State :</strong></label>
                                                <div class="col-md-12">
                                                    <select class="from-control selectpicker" name="pickup_state" id="select_state" data-size="10" data-live-search="true">
                                                        @foreach($get_states as $state)
                                                            <option value="{{$state['name']}}" @if($state['name'] =='Maharashtra'){{"selected"}} @endif>{{$state['name']}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                    <label for="country"><strong>Country :</strong></label>
                                                <div class="col-md-12">
                                                    <select class="from-control selectpicker" name="pickup_country" id="select_country" data-size="10" data-live-search="true">
                                                        @foreach($get_countries as $country)
                                                            <option value="{{$country['name']}}" @if($country['name'] =='India'){{"selected"}} @endif >{{$country['name']}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header text-center">
                                        <strong>Drop Address</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="line_1"><strong>Line 1 :</strong></label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" placeholder="Line 1.." name="drop_line_1" id="txt_line_1">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="line_1"><strong>Line 2 :</strong></label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" placeholder="Line 2.." name="drop_line_2" id="txt_line_2">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="line_1"><strong>Landmark :</strong></label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" placeholder="Landmark" name="drop_landmark" id="txt_landmark">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="line_1"><strong>Area :</strong></label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" placeholder="Area" name="drop_area" id="txt_area">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="line_1"><strong>Location</strong></label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" placeholder="Location" name="drop_location" id="txt_location" list="location_list">
                                                <datalist id="location_list">
                                                    @foreach($get_cities as $city)
                                                        <option value="{{$city['name']}}">{{$city['name']}}</option>
                                                    @endforeach
                                                </datalist>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="city"><strong>City</strong></label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" placeholder="city" name="drop_city" id="txt_city" list="city_list" value="Mumbai">
                                                <datalist id="city_list">
                                                   <option value="Mumbai">Mumbai</option>
                                                   <option value="Pune">Pune</option>
                                                </datalist>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="line_1"><strong>Pincode</strong></label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" placeholder="Pincode" name="drop_pincode" id="txt_pincode" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" maxlength="6">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="line_1"><strong>Email ID</strong></label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="email" class="form-control" placeholder="Email id.." name="drop_email" id="txt_email">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-6">
                                                    <label for="state"><strong>State :</strong></label>
                                                <div class="col-md-12">
                                                    <select class="from-control selectpicker" name="drop_state" id="select_state" data-size="10" data-live-search="true">
                                                        @foreach($get_states as $state)
                                                            <option value="{{$state['name']}}" @if($state['name'] =='Maharashtra'){{"selected"}} @endif>{{$state['name']}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                    <label for="country"><strong>Country :</strong></label>
                                                <div class="col-md-12">
                                                    <select class="from-control selectpicker" name="drop_country" id="select_country" data-size="10" data-live-search="true">
                                                        @foreach($get_countries as $country)
                                                            <option value="{{$country['name']}}" @if($country['name'] =='India'){{"selected"}} @endif >{{$country['name']}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="lead_source"><Strong>Lead Source</Strong></label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" placeholder="Google,JustDial...etc" name="lead_source" id="txt_lead_source">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="reffered_by"><Strong>Reffered By</Strong></label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" placeholder="Reffered By" name="reffered_by" id="txt_reffered_by">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-outline-success btn-block" name="submit" id="btn_submit" value="ambulance_data">Submit</button>
                            </div>
                            <div class="col-md-4"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection
</body>
@section('script')
    <script type="text/javascript">
        $('#file_prescription').on('change', function(e){
            console.log($(this).val());
        });
        $(document).ready(function() {
            $('#select_test_name').select2({
                theme: "classic",
                placeholder: 'Select Test',
                allowClear: true
            });
        });
        document.querySelector('.custom-file-input').addEventListener('change',function(e){
            var fileName = document.getElementById("file_prescription").files[0].name;
            var nextSibling = e.target.nextElementSibling
            var numFiles = $(this).get(0).files.length
            //nextSibling.innerText = fileName
            if(numFiles>1)
            {
                nextSibling.innerText = numFiles+" Files...";
            }
            else
            {
                nextSibling.innerText = fileName;
            }
            
        });
    </script>
@endsection
</html>