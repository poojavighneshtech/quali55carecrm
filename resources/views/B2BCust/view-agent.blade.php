{{-- @extends('new-sidebar') --}}
@extends('header_and_sidebar')

@section('title')
    Add/view Agent
@endsection
@section('styles')
@endsection

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header border-primary">
            <div class="col text-primary d-block">
                <strong>Add/view Agent</strong>
            </div>
        </div>
        <div class="card-body">
            <form action="{{route('agents.update',$user[0]->id)}}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" id="name" class="form-control form-control-sm" value="{{$user[0]->name}}" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="contact_no">Contact No</label>
                        (<input type="checkbox" name="is_whats_app_1" id="is_whats_app_1" @if($user[0]->whats_app_1){{'checked'}}@endif>
                        <label for="is_whats_app_1"><small>Whatsapp No</small></label>)
                        <input type="text" name="contact_no" id="contact_no" class="form-control form-control-sm" value="{{$user[0]->contact_no}}" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="secondary_contact_no">Secondaary Contact No</label>
                        (<input type="checkbox" name="is_whats_app_2" id="is_whats_app_2" @if($user[0]->whats_app_2){{'checked'}}@endif>
                        <label for="is_whats_app_2"><small>Whatsapp No</small></label>)
                        <input type="text" name="secondary_contact_no" id="secondary_contact_no" class="form-control form-control-sm" value="{{$user[0]->secondary_contact_no}}" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="email">Primary Email</label>
                        <input type="email" name="email" id="email" class="form-control form-control-sm" value="{{$user[0]->email}}" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="email">Secondary Email</label>
                        <input type="email" name="second_email" id="second_email" class="form-control form-control-sm" value="{{$user[0]->second_email}}" required>
                    </div>
                
                    <div class="col-md-4 form-group">
                        <label for="addr_line_1">Address line 1</label>
                        <input type="text" name="addr_line_1" id="addr_line_1" class="form-control form-control-sm" value="{{$user[0]->addr_line_1}}" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="addr_line_2">Address line 2</label>
                        <input type="text" name="addr_line_2" id="addr_line_2" class="form-control form-control-sm" value="{{$user[0]->addr_line_2}}" required>
                    </div>
                
                    <div class="col-md-4 form-group">
                        <label for="landmark">Landmark</label>
                        <input type="text" name="landmark" id="landmark" class="form-control form-control-sm" value="{{$user[0]->landmark}}" required>
                    </div>
                
                    <div class="col-md-4 form-group">
                        <label for="area">Area</label>
                        <input type="text" class="form-control form-control-sm" placeholder="Select Area" list="arealist" id="area" value="{{$user[0]->area}}" name="area" required>
                        <datalist id="arealist">
                            @foreach ($cities as $city) 
                                <option value="{{$city->name}}">{{$city->name}}</option>
                            @endforeach
                        </datalist>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="city">City</label>
                        <input type="text" name="city" id="city" class="form-control form-control-sm" value="{{$user[0]->city}}" required>
                    </div>
               
                    <div class="col-md-4 form-group">
                        <label for="pincode">Pincode</label>
                        <input type="text" name="pincode" id="pincode" class="form-control form-control-sm" value="{{$user[0]->pincode}}" required>
                    </div>
                
                    <div class="col-md-4 form-group">
                        <label for="state">State</label>
                        <select class="selectpicker form-control form-control-sm" title="State" name="state" data-live-search="true" required="true">
                            @foreach ($states as $state) 
                                <option value="{{$state->name}}"@if($state->name == $user[0]->state){{"selected"}} @endif>{{$state->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="country">Country</label>
                        <select class="selectpicker form-control form-control-sm" title="Country" name="country" data-live-search="true" required="true">
                            @foreach ($countries as $country) 
                                <option value="{{$country->name}}"@if($country->name == $user[0]->country){{"selected"}} @endif>{{$country->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="">Company Type</label><br>
                        <input class="company_type" type="radio" onchange = "show_hide_gst(this.value);" @if($user[0]->company_type == 'Individual'){{"checked"}}@endif name="company_type" id="individual" value="Individual" checked>
                        <label for="individual">Individual</label>
                        <input class="company_type" type="radio" onchange = "show_hide_gst(this.value);" @if($user[0]->company_type == 'Pvt Ltd'){{"checked"}}@endif name="company_type" id="pvtltd" value="Pvt Ltd">
                        <label for="pvtltd">Pvt Ltd</label>
                        <input class="company_type" type="radio" onchange = "show_hide_gst(this.value);" @if($user[0]->company_type == 'LLP'){{"checked"}}@endif name="company_type" id="llp" value="LLP">
                        <label for="llp">LLP</label>
                        <input class="company_type" type="radio" onchange = "show_hide_gst(this.value);" @if($user[0]->company_type == 'Propritership'){{"checked"}}@endif name="company_type" id="propritorship" value="Propritership">
                        <label for="propritorship">Propritership</label>
                        <input class="company_type" type="radio" onchange = "show_hide_gst(this.value);" @if($user[0]->company_type == 'Trust'){{"checked"}}@endif name="company_type" id="trust" value="Trust">
                        <label for="trust">Trust</label>
                    </div>
                    @if(isset($user[0]->certificates))
                    <div class="col-md-4 form-group gst_no">
                        <label for="gst_no">GST No</label>
                        <input type="text" name="gst_no" id="gst_no" class="form-control form-control-sm" value="{{$user[0]->gst_no}}" required>
                    </div>
                    <div class="col-md-4 form-group certificates-view">
                        <div class="row form-group">
                            <a href="{{asset('public/storage/'.explode(',',$user[0]->certificates)[0])}}" class="btn btn-sm btn-outline-primary" target="_blank">GST Certificate</a>
                        </div>
                        <div class="row form-group">
                            <a href="{{asset('public/storage/'.explode(',',$user[0]->certificates)[1])}}" class="btn btn-sm btn-outline-primary" target="_blank">Pan Card</a>
                        </div>
                    </div>
                    @endif
                    <div class="col-md-4 form-group certificates">
                        <div class="row">
                            <label for="gst_certificate">GST Certificate</label>
                            <input type="file" name="gst_certificate" id="gst_certificate">
                            <input type="hidden" name="old_gst_certificate" value="{{explode(',',$user[0]->certificates)[0]}}">
                        </div>
                        <div class="row">
                            <label for="pan_card">Pan Card</label>
                            <input class="form-control-file" type="file"name="pan_card" id="pan_card">
                            <input type="hidden" name="old_pan_card" value="{{explode(',',$user[0]->certificates)[1]}}">
                        </div>
                    </div>
                {{-- </div>
                <div class="row"> --}}
                    <div class="col-md-4 form-group">
                        <label for="country">Profile Image</label>
                        <img class="img img-thumbnail" src="{{asset('public/storage/'.$user[0]->profile_img)}}" alt="No Image Found">
                        <input type="file" class="form-control-file" name="profile_img" id="profile_img">
                        <input type="hidden" name="old_profile_img" value="{{$user[0]->profile_img}}">
                    </div>
                </div>
                <center>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="edit" onClick="$('.form-control').prop('disabled',false);$('.company_type').prop('disabled',false);$('.certificates').show();$('.certificates-view').hide();$('#update').show();$('#edit').hide();">Edit</button>
                    <button type="submit" class="btn btn-sm btn-outline-success" style="display: none;" id="update">Update</button>
                </center>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script>
        $(".form-control").prop('disabled',true);
        $(".company_type").prop('disabled',true);
        // $(".custom-file-input").attr('disabled',true);
        $(".certificates").hide();
        $(".certificates-view").show();
        function show_hide_gst(type)
    {
        // console.log(type);
        if(type == 'Individual')
        {
            $(".gst_no").hide();
            $(".certificates").hide();
            $('#gst_certificate').prop('required',false);
            $('#pan_card').prop('required',false);
            $('#gst_no').prop('required',false);
        }
        else
        {
            $(".gst_no").show();
            $(".certificates").show();
            $('#gst_certificate').prop('required',true);
            $('#pan_card').prop('required',true);
            $('#gst_no').prop('required',true);
        }
    }
    </script>
@endsection