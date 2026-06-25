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
            <form action="{{url('/')}}/update_lab_register" method="post" enctype="multipart/form-data">
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
                                        <input type="text" class="form-control" placeholder="Lab Name.." name="lab_name" id="txt_lab_name" value="{{$get_lab[0]['lab_name']}}" required disabled>
                                        <input type="hidden" name="old_lab_name" value="{{$get_lab[0]['lab_name']}}">
                                        <input type="hidden" name="lab_id" value="{{$get_lab[0]['id']}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-outline-primary btn-block" id="btn_edit_lab">Edit Details</button>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="button" class="btn btn-outline-danger btn-block" id="btn_delete_lab">Delete</button>
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
                                                <input type="text" class="form-control" placeholder="Line 1.." name="line_1" id="txt_line_1" value="{{$get_lab[0]['line_1']}}" disabled>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="line_2"><strong>Line 2 :</strong></label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" placeholder="Line 2.." name="line_2" id="txt_line_2" value="{{$get_lab[0]['line_1']}}" disabled>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="lanmark"><strong>Landmark :</strong></label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" placeholder="Landmark" name="landmark" id="txt_landmark" value="{{$get_lab[0]['landmark']}}" disabled>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="area"><strong>Area :</strong></label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" placeholder="Area" name="area" id="txt_area" value="{{$get_lab[0]['area']}}" disabled>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="location"><strong>Location</strong></label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" placeholder="Location" name="location" id="txt_location" list="location_list" value="{{$get_lab[0]['location']}}" disabled>
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
                                                <label for="pincode"><strong>Pincode</strong></label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" placeholder="Pincode" name="pincode" id="txt_pincode" 
                                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" 
                                                    maxlength="6"
                                                    value="{{$get_lab[0]['pincode']}}" disabled>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="line_1"><strong>Email ID</strong></label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="email" class="form-control" placeholder="Email id.." name="email" id="txt_email" value="{{$get_lab[0]['lab_email']}}" disabled>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-6">
                                                    <label for="state"><strong>State :</strong></label>
                                                <div class="col-md-12">
                                                    <select class="from-control selectpicker" name="state" id="select_state" data-size="10" data-live-search="true" >
                                                        @foreach($get_states as $state)
                                                            <option value="{{$state['name']}}" @if($state['name']==$get_lab[0]['state']){{"selected"}} @endif>{{$state['name']}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                    <label for="country"><strong>Country :</strong></label>
                                                <div class="col-md-12">
                                                    <select class="from-control selectpicker" name="country" id="select_country" data-size="10" data-live-search="true">
                                                        @foreach($get_countries as $country)
                                                            <option value="{{$country['name']}}" @if($country['name']==$get_lab[0]['country']){{"selected"}} @endif >{{$country['name']}}</option>
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
                                            <div class="col-md-9 text-right" id="btn_add_certificate" style="display:none">
                                                <button type="button" class="btn btn-outline-primary btn-sm btn-rounded" id="btn_certificate_add">Add</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body" id="div_certificate">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="input-group">
                                                    @php
                                                        $aggreement = json_decode($get_lab[0]['aggreement'])
                                                    @endphp
                                                    <div class="input-group-prepend">
                                                        <input type="text" class="input-group-text form-control" name="aggreement" id="txt_aggreement" value="Aggreement" disabled>
                                                    </div>
                                                    <div class="custom-file div_choose_file" style="display:none" id="div_choose_file">
                                                        <input type="file" class="custom-file-input" name="aggreement" id="file_aggreement">
                                                        <label class="custom-file-label" for="aggreement">Choose file</label>
                                                    </div>
                                                    <div class="input-group-append">
                                                        <a class="btn btn-outline-primary" type="button" href="{{$aggreement}}" target="_blank">View</a>
                                                        <input type="hidden" name="old_aggreement_path" value="{{$aggreement}}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @if($get_lab[0]['other_certificates']!=null)
                                            @php
                                                $other_certificate = json_decode($get_lab[0]['other_certificates'],true);
                                            @endphp
                                            @for ($i = 0; $i <count($other_certificate); $i++)
                                                <input type="hidden" name="old_certificates_name[]" value="{{$other_certificate[$i]['name']}}">
                                                <input type="hidden" name="old_certificates_status[]" id="txt_del_status{{$i}}" value="No">
                                                <input type="hidden" name="old_certificates_path[]" id="txt_del_status{{$i}}" value="{{$other_certificate[$i]['path']}}">
                                                <br id="br{{$i}}">
                                                <div class="row" id="div_other_cer{{$i}}">
                                                    <div class="col-md-11">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <input type="text" class="input-group-text form-control txt_other_certificate" name="old_other_certificate_name[{{$i}}][]" id="txt_other_certificate{{$i}}" value="{{$other_certificate[$i]['name']}}" disabled>
                                                            </div>
                                                            <div class="custom-file div_choose_file" style="display:none" id="div_choose_file">
                                                                <input type="file" class="custom-file-input" name="old_other_certificate_file[{{$i}}][]" id="file_other_certificate{{$i}}" data-count="{{$i}}"  value="{{$other_certificate[$i]['path']}}" onchange="file_change_div(this.id);">
                                                                <label class="custom-file-label" for="other_certificate">Choose file</label>
                                                            </div>
                                                            <div class="input-group-append">
                                                                @php
                                                                    $aggreement = json_decode($get_lab[0]['aggreement'])
                                                                @endphp
                                                                <a class="btn btn-outline-primary" type="button" href="{{$other_certificate[$i]['path']}}" target="_blank">View</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1 padding-0 div_remove_file" style="display:none">
                                                        <button type='button' class='btn btn-outline-danger btn-rounded' id='remove_btn' value="{{$i}}" onclick='other_remove_div(this.value);'><i class='fas fa-trash-alt'></i></button>
                                                    </div>
                                                </div> 
                                            @endfor
                                        @else
                                            <input type="hidden" name="old_certificates_name[]" value="">
                                            <input type="hidden" name="old_certificates_status[]" id="" value="">
                                            <input type="hidden" name="old_certificates_path[]" id="" value="">
                                        @endif
                                    </div>
                                </div>
                                <br>
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <strong>Contact Persons</strong>
                                            </div>
                                            <div class="col-md-8 text-right" id="div_btn_add_contact_prsn" style="display: none">
                                                <button type="button" class="btn btn-outline-primary btn-sm btn-rounded" id="btn_add_contact_prsn" ><i class="fa fa-plus" aria-hidden="true"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body" id="div_contact_prsn">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <div class="col-md-4">
                                                            <span>Person 1</span>
                                                        </div>
                                                        <div class="col-md-8"></div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-2">
                                                                <label for="person1_name"><strong>Name</strong></label>
                                                            </div>
                                                            <div class="col-md-10">
                                                                <input type="text" class="form-control" name="person1_name" id="txt_person1_name" placeholder="Person Name.." value="{{$get_lab[0]['person1_name']}}" required disabled>
                                                            </div>
                                                        </div>
                                                        <br>
                                                        <div class="row">
                                                            <div class="col-md-5">
                                                                    <label for="person1_contact"><strong>Contact :</strong></label>
                                                                <div class="col-md-12">
                                                                    <input type="text" class="form-control" name="person1_contact" id="txt_person1_contact" 
                                                                        oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" 
                                                                        maxlength="10" 
                                                                        value="{{$get_lab[0]['person1_contact']}}"
                                                                        required disabled>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-7">
                                                                    <label for="person1_email"><strong>Email ID :</strong></label>
                                                                <div class="col-md-12">
                                                                    <input type="email" class="form-control" name="person1_email" id="txt_person1_email" value="{{$get_lab[0]['person1_email']}}" disabled>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @if(isset($get_lab[0]['other_contact_persons']))
                                            @php
                                                $other_contact_persons = json_decode($get_lab[0]['other_contact_persons'],true);
                                                $x = 2;
                                            @endphp
                                            <input type="hidden" name="other_prsn_count" id="other_prsn_count" value="{{count($other_contact_persons['name'])}}">
                                            @for($i=0; $i<count($other_contact_persons['name']); $i++)
                                                <br id="cnt_br{{$i}}">
                                                <div class="row" id="div_row_cnt_prsn{{$i}}">
                                                    <div class="col-md-12">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <span>Person {{$x}}</span>
                                                                    </div>
                                                                    <div class="col-md-8 text-right div_btn_remove_persons" style="display:none">
                                                                        <button type="button" class="btn btn-outline-danger btn-rounded" id="btn_remove_div_cnt_prsn{{$i}}" value="{{$i}}" onclick="remove_div_cnt_prsn(this.value);" > <i class="fas fa-trash-alt"></i></button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-md-2">
                                                                        <label for="txt_other_contact_persons_name"><strong>Name</strong></label>
                                                                    </div>
                                                                    <div class="col-md-10">
                                                                        <input type="text" class="form-control class_other_contact_persons_name" name="other_contact_persons[name][]" id="txt_other_contact_persons_name" placeholder="Person Name.." value="{{$other_contact_persons['name'][$i]}}" required disabled>
                                                                    </div>
                                                                </div>
                                                                <br>
                                                                <div class="row">
                                                                    <div class="col-md-5">
                                                                            <label for="txt_other_contact_persons_contact"><strong>Contact :</strong></label>
                                                                        <div class="col-md-12">
                                                                            <input type="text" class="form-control class_other_contact_persons_contact" name="other_contact_persons[contact][]" id="txt_other_contact_persons_contact" 
                                                                                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" 
                                                                                maxlength="10" 
                                                                                value="{{$other_contact_persons['contact'][$i]}}"
                                                                                required disabled>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-7">
                                                                        <label for="txt_other_contact_persons_email"><strong>Email ID :</strong></label>
                                                                        <div class="col-md-12">
                                                                            <input type="email" class="form-control class_other_contact_persons_email" name="other_contact_persons[email][]" id="txt_other_contact_persons_email" value="{{$other_contact_persons['email'][$i]}}" disabled>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @php
                                                    $x++;
                                                @endphp
                                            @endfor
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <input type="submit" class="btn btn-outline-success btn-block" id="btn_update" value="Update" disabled>
                            </div>
                            <div class="col-md-4"></div>
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
        // document.querySelector('.custom-file-input').addEventListener('change',function(e){
        //     var fileName = document.getElementById("file_aggreement").files[0].name;
        //     var nextSibling = e.target.nextElementSibling
        //     //var numFiles = $(this).get(0).files.length
        //     nextSibling.innerText = fileName
        //     // if(numFiles>1)
        //     // {
        //     //     nextSibling.innerText = numFiles+" Files...";
        //     // }
        //     // else
        //     // {
        //     //     nextSibling.innerText = fileName;
        //     // }
            
        // });

        var max_fields = 10;
        var x=1;

        $('#btn_certificate_add').on('click', function(){
            if(x < max_fields)
            {
                // if(x==1)
                // {
                //     var rows = "<br id='br"+x+"'>"    
                // }
                var rows = "<br id='br"+x+"'>"    
                    rows+="<div class='row' id='div_cer"+x+"'>";
                    rows+="<div class='col-md-11'>";
                    rows+="<div class='input-group mb-3'>";
                    rows+="<div class='input-group-prepend'>";
                    rows+="<input type='text' class='input-group-input form-control ' name='other_certificate_name[]' id='txt_aggreement"+x+"' placeholder='certificate name..' required>";
                    rows+="</div>";
                    rows+="<div class='custom-file'>";
                    rows+="<input type='file' class='custom-file-input' name='other_certificate_file[]' id='file_aggreement' required>";
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
            $('#br'+id).remove();
            x--;
        }
        $('#btn_edit_lab').on('click', function(){
            $('#txt_lab_name').prop('disabled', false);
            $('#txt_line_1').prop('disabled', false);
            $('#txt_line_2').prop('disabled', false);
            $('#txt_landmark').prop('disabled', false);
            $('#txt_area').prop('disabled', false);
            $('#txt_location').prop('disabled', false);
            $('#txt_pincode').prop('disabled', false);
            $('#txt_email').prop('disabled', false);
            $('#txt_lab_name').prop('disabled', false);
            $('.txt_other_certificate').prop('disabled', false);
            $('.div_choose_file').css('display','block');
            $('.div_remove_file').css('display','block');
            $('.div_btn_remove_persons').css('display','block');
            $('#div_btn_add_contact_prsn').css('display','block');
            $('#btn_add_certificate').css('display','block');
            $('.class_other_contact_persons_name').prop('disabled', false);
            $('.class_other_contact_persons_contact').prop('disabled', false);
            $('.class_other_contact_persons_email').prop('disabled', false);
            $('#div_person2').css('display','block');
            $('#txt_person1_name').prop('disabled', false);
            $('#txt_person1_contact').prop('disabled', false);
            $('#txt_person1_email').prop('disabled', false);
            $('#txt_person2_name').prop('disabled', false);
            $('#txt_person2_contact').prop('disabled', false);
            $('#txt_person2_email').prop('disabled', false);
            $('#btn_update').prop('disabled', false);
            
        });


        function other_remove_div(id)
        {
            $('#txt_del_status'+id).val('Deleted');
            $('#div_other_cer'+id).remove();
            $('#br'+id).remove();
        }
        function file_change_div(id)
        {
           var count = $('#'+id).data('count');
           $('#txt_del_status'+count).val('Updated');
        }

        //contact prsn add
        var max_fields = 10;
        var y=$('#other_prsn_count').val();
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
                                            rows+='<input type="text" class="form-control" name="other_contact_persons[name][]" id=" id="txt_person'+y+'_name" placeholder="Person Name.." required>';
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
            $('#div_row_cnt_prsn'+id).remove();
            $('#cnt_br'+id).remove();
            y--;
        }

  </script>
@endsection
</html>