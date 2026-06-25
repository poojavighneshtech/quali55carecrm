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
                    <div class="row">
                        <div class="col-md-3"></div>
                        <div class="col-md-6">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="lead_type" id="lab_test_lead" value="lab_test_lead" checked>
                                <label class="form-check-label" for="lab_test_lead">Lab Test Lead</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="lead_type" id="ambulance_lead" value="ambulance_lead">
                                <label class="form-check-label" for="ambulance_lead">Ambulance Lead</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="lead_type" id="nursing_care" value="nursing_care">
                                <label class="form-check-label" for="nursing_care">Nursing Care</label>
                            </div>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                    <br>
                    <div class="row" id="div_lab_test_lead" style="display: block">
                        <div class="col-md-12">
                            <form action="{{url('/')}}/create_other_leads" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="card">
                                    <div class="card-header text-center">
                                            <h5>Lab Test Lead</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
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
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label for="name"><strong>Name :</strong></label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" name="patient_name" id="txt_patient_name" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label for="visit_date"><strong>date:</strong></label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <input type="date" class="form-control" name="visit_date" id="txt_visit_date" value="{{date('Y-m-d')}}" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label for="visit_time"><strong>Time:</strong></label>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <input type="time" class="form-control" name="visit_time" id="txt_visit_time" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label for="test_name"><strong>Test Name:</strong></label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <select class="form-control select2" name="test_name" id="select_test_name" required>
                                                            @foreach($get_lab_test as $lab_test)
                                                                <option value="{{$lab_test['id']}}">{{$lab_test['test_name']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="lab_name"><strong>Lab Name</strong></label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <select class="form-control selecpticker" name="lab_name" id="select_lab_name">
                                                            <option value="UDC">UDC</option>
                                                            <option value="Lal Path Lab">Lal Path Lab</option>
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
                                            </div>
                                            <div class="col-md-6">
                                                {{-- <label for="address_of _bld_clc"><strong>Address For Blood Collection :</strong></label> --}}
                                                <div class="form-group">
                                                    <textarea class="form-control" name="blood_collection_address" id="txt_area_bld_address" cols="30" placeholder="Address for blood collection..." rows="4" required></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="location"><strong>Location :</strong></label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" name="location" id="txt_location" list="cities" required>
                                                        <datalist id="cities">
                                                            @foreach($get_cities as $city)
                                                                <option value="{{$city['name']}}">{{$city['name']}}</option>
                                                            @endforeach
                                                        </datalist>
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="email_id"><strong>Email ID :</strong></label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="email" class="form-control" name="email_id" id="txt_email_id" required>
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="customer_price"><strong>Price :</strong></label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" name="customer_price" id="txt_customer_price" 
                                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" maxlength="10" required>
                                                    </div>
                                                </div>
                                                <br>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <textarea class="form-control" name="comment" id="txt_comment" cols="30" placeholder="Type comment here..." rows="6"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4">
                                                <button type="submit" class="btn btn-outline-success btn-block" name="submit" id="btn_submit" value="lab_test_data">Submit</button>
                                            </div>
                                            <div class="col-md-4"></div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="row" id="div_ambulance_lead" style="display: none">
                        <div class="col-md-12">
                            <form action="{{url('/')}}/create_other_leads" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="card">
                                    <div class="card-header text-center">
                                        <label for=""><h5>Ambulance Lead</h5></label>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label for="contact_no"><strong>Contact No :</strong></label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" name="contact_no" id="txt_contact_no" list
                                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" maxlength="10" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label for="name"><strong>Name :</strong></label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" name="patient_name" id="txt_patient_name" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="pickup_location"><strong>Pickup Location :</strong></label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" name="pickup_location" id="txt_pickup_location" list="cities" required>
                                                        <datalist id="cities">
                                                            @foreach($get_cities as $city)
                                                                <option value="{{$city['name']}}">{{$city['name']}}</option>
                                                            @endforeach
                                                        </datalist>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="drop_location"><strong>Drop Location :</strong></label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" name="drop_location" id="txt_drop_location" list="cities" required>
                                                        <datalist id="cities">
                                                            @foreach($get_cities as $city)
                                                                <option value="{{$city['name']}}">{{$city['name']}}</option>
                                                            @endforeach
                                                        </datalist>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="location"><strong>Patient Location:</strong></label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" name="location" id="txt_location" list="cities" required>
                                                        <datalist id="cities">
                                                            @foreach($get_cities as $city)
                                                                <option value="{{$city['name']}}">{{$city['name']}}</option>
                                                            @endforeach
                                                        </datalist>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="ambulance_type"><strong>Ambulance Type:</strong></label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <select class="form-control selecpticker" name="ambulance_type" id="select_ambulance_type" required>
                                                            <option value="Cardic">Cardic</option>
                                                            <option value="Non Cardic">Non Cardic</option>
                                                            <option value="Covid 2019">Covid 2019</option>
                                                        </select>
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
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="row" id="div_nursing_care" style="display: none">
                        <div class="col-md-12">
                            <form action="{{url('/')}}/create_other_leads" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="card">
                                    <div class="card-header text-center">
                                        <label for=""><h5>Nursing Care</h5></label>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label for="contact_no"><strong>Contact No :</strong></label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" name="contact_no" id="txt_contact_no" 
                                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" maxlength="10" required>
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label for="name"><strong>Name :</strong></label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" name="patient_name" id="txt_patient_name" required>
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label for="email_id"><strong>Email ID :</strong></label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <input type="email" class="form-control" name="email_id" id="txt_email_id">
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label for="location"><strong>Location :</strong></label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" name="location" id="txt_location" list="cities" required>
                                                        <datalist id="cities">
                                                            @foreach($get_cities as $city)
                                                                <option value="{{$city['name']}}">{{$city['name']}}</option>
                                                            @endforeach
                                                        </datalist>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="form-group">
                                                        <label for="service_rquirement"><strong>Service Requirements :</strong></label>
                                                        <textarea class="form-control" name="service_rquirement" id="txt_area_service_rquirement" cols="70" placeholder="Type service rquirement here..." rows="2" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group">
                                                        <label for="address"><strong>Address :</strong></label>
                                                        <textarea class="form-control" name="address" id="txt_area_address" cols="70" placeholder="Type Address here..." rows="3" required></textarea>
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
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
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
        $("input[name='lead_type']").on("change", function(){
            let rad_val = this.value;
            if(rad_val=='lab_test_lead')
            {
                $('#div_lab_test_lead').css('display','block');
                $('#div_ambulance_lead').css('display','none');
                $('#div_nursing_care').css('display','none');
            }
            else if(rad_val=='nursing_care')
            {
                $('#div_lab_test_lead').css('display','none');
                $('#div_ambulance_lead').css('display','none');
                $('#div_nursing_care').css('display','block');
            }
            else
            {
                $('#div_lab_test_lead').css('display','none');
                $('#div_ambulance_lead').css('display','block');
                $('#div_nursing_care').css('display','none');
            }
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