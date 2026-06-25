@extends('header_and_sidebar')
<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
    
        <title>All Lab Test Leads</title>
        {{-- <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script> --}}
        @section('styles')
        <style>
            .select2 {
                width:100%!important;
            }    
            .padding-0{
                padding-right:0;
                padding-left:0;
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
            <form action="{{url('/')}}/lab_register" method="post" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-header"  style="background-color: #4a6fdc; color: white;" >
                        <center>Lab Registration</center>
                    </div> 
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="lab_name"><Strong>Lab name :</Strong></label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" placeholder="Lab Name.." name="lab_name" id="txt_lab_name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6"></div>
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
                                                <label for="line_1"><strong>City</strong></label>
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
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>Certificate</strong>
                                            </div>
                                            <div class="col-md-9 text-right">
                                                <button type="button" class="btn btn-outline-primary btn-sm btn-rounded" id="btn_certificate_add"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body" id="div_certificate">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                      <input type="text" class="input-group-text form-control " name="aggreement" id="txt_aggreement" value="Aggreement" disabled>
                                                    </div>
                                                    <div class="custom-file">
                                                      <input type="file" class="custom-file-input" name="aggreement" id="file_aggreement" required>
                                                      <label class="custom-file-label" for="aggreement">Choose file</label>
                                                    </div>
                                                  </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                                <br>
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <strong>Contact Persons</strong>
                                            </div>
                                            <div class="col-md-8 text-right">
                                                <button type="button" class="btn btn-outline-primary btn-sm btn-rounded" id="btn_add_contact_prsn"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body" id="div_contact_prsn">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <span>Person 1</span>
                                                            </div>
                                                            {{-- <div class="col-md-8 text-right">
                                                                <button type="button" class="btn btn-outline-danger btn-rounded" id="remove_btn" value="1" onclick="remove_div(this.value);"> <i class="fas fa-trash-alt"></i></button>
                                                            </div> --}}
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-2">
                                                                <label for="person1_name"><strong>Name</strong></label>
                                                            </div>
                                                            <div class="col-md-10">
                                                                <input type="text" class="form-control" name="person1_name" id="txt_person1_name" placeholder="Person Name.." required>
                                                            </div>
                                                        </div>
                                                        <br>
                                                        <div class="row">
                                                            <div class="col-md-5">
                                                                    <label for="person1_contact"><strong>Contact :</strong></label>
                                                                <div class="col-md-12">
                                                                    <input type="text" class="form-control" name="person1_contact" id="txt_person1_contact" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" maxlength="10" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-7">
                                                                    <label for="person1_email"><strong>Email ID :</strong></label>
                                                                <div class="col-md-12">
                                                                    <input type="email" class="form-control" name="person1_email" id="txt_person1_email" >
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
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
                                <input type="submit" class="btn btn-outline-success btn-block" value="Submit">
                            </div>
                            <div class="col-md-4">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
</body>
@section('script')
  <script>
      $(document).ready(function() {
            $('.select2').select2({
                theme: "classic",
                //placeholder: 'Select Test',
                allowClear: true
            });
        });
        document.querySelector('.custom-file-input').addEventListener('change',function(e){
            var fileName = document.getElementById("file_aggreement").files[0].name;
            var nextSibling = e.target.nextElementSibling
            //var numFiles = $(this).get(0).files.length
            nextSibling.innerText = fileName
            // if(numFiles>1)
            // {
            //     nextSibling.innerText = numFiles+" Files...";
            // }
            // else
            // {
            //     nextSibling.innerText = fileName;
            // }
            
        });

        //certificate add 
        var max_fields = 10;
        var x=1;
        $('#btn_certificate_add').on('click', function(){
            if(x < max_fields)
            {
                var rows="<div class='row' id='div_cer"+x+"'>";
                    rows+="<div class='col-md-11'>";
                    rows+="<div class='input-group mb-3'>";
                    rows+="<div class='input-group-prepend'>";
                    rows+="<input type='text' class='input-group-input form-control ' name='other_certificate[]' id='txt_aggreement"+x+"' placeholder='certificate name..' required>";
                    rows+="</div>";
                    rows+="<div class='custom-file'>";
                    rows+="<input type='file' class='custom-file-input' name='other_certificate[]' id='file_aggreement"+x+"' required>";
                    rows+="<label class='custom-file-label' for='aggreement'>Choose file</label>";
                    rows+="</div>";
                    rows+="</div>";
                    rows+="</div>";
                    rows+="<div class='col-md-1 padding-0'>";
                    rows+="<button type='button' class='btn btn-outline-danger btn-rounded' id='remove_btn' data-count="+x+" value="+x+" onclick='remove_div(this.value);'> <i class='fas fa-trash-alt'></i></button>";
                    rows+="</div>";
                    rows+="</div>";
                    x++; 
                $('#div_certificate').append(rows);  
            }
        });
        function remove_div(id)
        {
            $('#div_cer'+id).remove();
            x--;
        }

        //contact prsn add
        var max_fields = 10;
        var y=2;
        $('#btn_add_contact_prsn').on('click', function(){
            if(y < max_fields)
            {
                var rows='<br id="cnt_br'+y+'">';
                    rows+='<div class="row" id="div_row__cnt_prsn'+y+'">';
                        rows+='<div class="col-md-12">';
                            rows+='<div class="card">';
                                rows+='<div class="card-header">';
                                    rows+='<div class="row">';
                                        rows+='<div class="col-md-4">';
                                            rows+='<span>Person '+y+'</span>';
                                        rows+='</div>';
                                        rows+='<div class="col-md-8 text-right">';
                                            rows+='<button type="button" class="btn btn-outline-danger btn-rounded" data-count="'+y+'" id="btn_remove_div_cnt_prsn" value="'+y+'" onclick="remove_div_cnt_prsn(this.value);"> <i class="fas fa-trash-alt"></i></button>';
                                        rows+='</div>';
                                    rows+='</div>';
                                rows+='</div>';
                                rows+='<div class="card-body">';
                                    rows+='<div class="row">';
                                        rows+='<div class="col-md-2">';
                                            rows+='<label for="person'+y+'_name"><strong>Name</strong></label>';
                                        rows+='</div>';
                                        rows+='<div class="col-md-10">';
                                            rows+='<input type="text" class="form-control" name="other_contact_persons[name][]" id="txt_person'+y+'_name" placeholder="Person Name.." required>';
                                        rows+='</div>';
                                    rows+='</div>';
                                    rows+='<br>';
                                    rows+='<div class="row">';
                                        rows+='<div class="col-md-5">';
                                                rows+='<label for="person'+y+'_contact"><strong>Contact :</strong></label>';
                                            rows+='<div class="col-md-12">';
                                                rows+="<input type='text' class='form-control' name='other_contact_persons[contact][]' id='txt_person'"+y+"'_contact' oninput='this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');' maxlength='10' required>";
                                            rows+='</div>';
                                        rows+='</div>';
                                        rows+='<div class="col-md-7">';
                                                rows+='<label for="person'+y+'_email"><strong>Email ID :</strong></label>';
                                            rows+='<div class="col-md-12">';
                                                rows+='<input type="text" class="form-control" name="other_contact_persons[email][]" id="txt_person'+y+'_email">';
                                            rows+='</div>';
                                        rows+='</div>';
                                    rows+='</div>';
                                rows+='</div>';
                            rows+='</div>';
                        rows+='</div>';
                    rows+='</div>';
                $('#div_contact_prsn').append(rows);  
                y++;
            }
        });
        function remove_div_cnt_prsn(id)
        {
            $('#div_row__cnt_prsn'+id).remove();
            $('#cnt_br'+id).remove();
            y--;
        }
  </script>
@endsection
</html>