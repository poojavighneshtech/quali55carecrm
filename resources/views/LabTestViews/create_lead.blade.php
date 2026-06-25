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
                    <form action="{{url('/')}}/create_lead_lab" method="post" enctype="multipart/form-data">
                        @csrf
                        {{-- <div class="card">
                            <div class="card-header text-center">
                                    <h5>Lab Test Lead</h5>
                            </div> --}}
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
                                        <div class="col-md-2">
                                            <label for="name"><strong>Name:</strong></label>
                                        </div>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" name="patient_name" id="txt_patient_name" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="customer_price"><strong>Price :</strong></label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name="customer_price" id="txt_customer_price" 
                                                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" maxlength="10" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
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
                                                    <input type="text" class="form-control" placeholder="Location" name="location" id="txt_location" list="location_list">
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
                                                    <input type="text" class="form-control" placeholder="city" name="city" id="txt_city" list="city_list" value="Mumbai">
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
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-7">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <label for="visit_date"><strong>date:</strong></label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input type="date" class="form-control" name="visit_date" id="txt_visit_date" value="{{date('Y-m-d')}}" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <label for="visit_time"><strong>Time:</strong></label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input type="time" class="form-control" name="visit_time" id="txt_visit_time" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label for="test_name"><strong>Test Name:</strong></label>
                                                </div>
                                                <div class="col-md-9">
                                                    <select class="form-control select2" name="test_name[]" id="select_test_name" required multiple="multiple">
                                                        @foreach($get_lab_test as $lab_test)
                                                            <option value="{{$lab_test['id']}}">{{$lab_test['test_name']}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label for="lab_name"><strong>Lab Name</strong></label>
                                                </div>
                                                <div class="col-md-8">
                                                    <select class="form-control selecpticker" name="lab_name" id="select_lab_name">
                                                        @foreach($get_labs as $lab)
                                                            <option value="{{$lab['id']}}">{{$lab['lab_name']}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label for="prescription"><strong>Prescription</strong></label>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="custom-file mb-3">
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input" name="prescription_img[]" id="file_prescription" aria-describedby="prescription" accept="image/*" multiple>
                                                            <label class="custom-file-label" for="prescription">Choose file</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-nd-12">
                                                    &emsp;<label for=""><strong>Comment..</strong></label>
                                                    <div class="form-group">
                                                        <textarea class="form-control" name="comment" id="txt_comment" cols="70" placeholder="Type comment here..." rows="6"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label for="lead_source"><strong>Lead Source</strong></label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input type="text" class="form-control"  name="lead_source" id="txt_lead_source" placeholder="Googele, JustDial...etc">
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label for="reffered_by"><strong>Reffered By</strong></label>
                                                </div>
                                                <div class="col-md-9">
                                                    <input type="text" class="form-control" placeholder="Reffered By" name="reffered_by" id="txt_reffered_by" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4"></div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-outline-success btn-block" name="submit" id="btn_submit" value="lab_test_data">Submit</button>
                                </div>
                                <div class="col-md-4"></div>
                            </div>
                        {{-- </div> --}}
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