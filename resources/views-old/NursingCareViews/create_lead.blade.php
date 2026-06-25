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
                    <form action="{{url('/')}}/create_lead_nursing_care" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="contact_no"><strong>Contact No :</strong></label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="contact_no" id="txt_contact_no" 
                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" maxlength="10" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="name"><strong>Name :</strong></label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="patient_name" id="txt_patient_name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="price"><strong>Price</strong></label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="price" id="txt_price"
                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header text-center">
                                        <strong>Address</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="line_1"><strong>Line 1 :</strong></label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" placeholder="Line 1.." name="line_1" id="txt_line_1">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="line_1"><strong>Line 2 :</strong></label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" placeholder="Line 2.." name="line_2" id="txt_line_2">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="line_1"><strong>Landmark :</strong></label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" placeholder="Landmark" name="landmark" id="txt_landmark">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="line_1"><strong>Area :</strong></label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" placeholder="Area" name="area" id="txt_area">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="line_1"><strong>Location</strong></label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" placeholder="Location" name="location" id="txt_location" list="location_list" value="Mumbai">
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
                                                <label for="line_1"><strong>Pincode</strong></label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" placeholder="Pincode" name="pincode" id="txt_pincode" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" maxlength="6">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="line_1"><strong>Email ID</strong></label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="email" class="form-control" placeholder="Email id.." name="email" id="txt_email">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-6">
                                                    <label for="state"><strong>State :</strong></label>
                                                <div class="col-md-12">
                                                    <select class="from-control selectpicker" name="state" id="select_state" data-size="10" data-live-search="true">
                                                        @foreach($get_states as $state)
                                                            <option value="{{$state['name']}}" @if($state['name'] =='Maharashtra'){{"selected"}} @endif>{{$state['name']}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                    <label for="country"><strong>Country :</strong></label>
                                                <div class="col-md-12">
                                                    <select class="from-control selectpicker" name="country" id="select_country" data-size="10" data-live-search="true">
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
                                <div class="row">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label for="date"><strong>Date</strong></label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input type="date" class="form-control" placeholder="date" name="date" id="txt_date" required>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col">
                                                    <div class="form-group">
                                                        <label for="service_rquirement"><strong>Service Requirements :</strong></label>
                                                        <textarea class="form-control" name="service_rquirement" id="txt_area_service_rquirement" cols="70" placeholder="Type service rquirement here..." rows="6" required></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="nurses_type"><strong>Nurses Type :</strong></label>
                                                    <div class="col-md-12">
                                                        <select class="from-control selectpicker" name="nurses_type" id="select_nurses_type" data-size="10">
                                                            <option value="Male">Male</option>
                                                            <option value="Female">Female</option>
                                                            <option value="Brother">Brother</option>
                                                            <option value="Sister">Sister</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                        <label for="duties_hr"><strong>Duties Hr :</strong></label>
                                                    <div class="col-md-12">
                                                        <select class="from-control selectpicker" name="dutie_hour" id="select_dutie_hour" data-size="10">
                                                            <option value="12">12hr</option>
                                                            <option value="24">24hr</option>
                                                            
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col">
                                                    <div class="form-group">
                                                        <label for="therapeutic"><strong>Therapeutic Requirement :</strong></label>
                                                        <textarea class="form-control" name="therapeutic_rqrmt" id="txt_area_therapeutic" cols="70" placeholder="Type Therapeutic rquirement here..." rows="2" required></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label for="lead_source"><strong>Lead Source</strong></label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input type="text" class="form-control" name="lead_source" id="txt_lead_source">
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label for="reffered_by"><strong>Reffered By</strong></label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input type="text" class="form-control" name="reffered_by" id="txt_reffered_by">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-outline-success btn-block" name="submit" id="btn_submit" value="nursing_data">Submit</button>
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