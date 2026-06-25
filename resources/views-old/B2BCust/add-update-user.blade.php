@extends('header_and_sidebar')

@section('title')
    Add B2B User
@endsection
@section('styles')
@endsection

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header border-primary">
            <div class="col text-primary d-block">
                <strong>Add B2B User</strong>
            </div>
        </div>
        <div class="card-body">
            <form action="{{route('b2bcustomers.store')}}" method="POST"  enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" id="name" class="form-control form-control-sm" value="{{old('name')}}" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="contact_no">Primary Contact No</label>
                        (<input type="checkbox" name="is_whats_app_1" id="is_whats_app_1">
                        <label for="is_whats_app_1"><small>Whatsapp No</small></label>)
                        <input type="text" name="contact_no" id="contact_no" class="form-control form-control-sm" maxlength="10" value="{{old('contact_no')}}" required>
                        @if ($errors->has('contact_no'))
                            <span class="text-danger">{{ $errors->first('contact_no') }}...</span>
                        @endif
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="secondary_contact_no">Secondary Contact No</label>
                        (<input type="checkbox" name="is_whats_app_2" id="is_whats_app_2">
                        <label for="is_whats_app_2"><small>Whatsapp No</small></label>)
                        <input type="text" name="secondary_contact_no" id="secondary_contact_no" class="form-control form-control-sm" maxlength="10" value="{{old('contact_no')}}" required>
                        @if ($errors->has('secondary_contact_no'))
                            <span class="text-danger">{{ $errors->first('secondary_contact_no') }}...</span>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="email">Primary Email</label>
                        <input type="email" name="email" id="email" class="form-control form-control-sm" required value="{{old('email')}}">
                        @if ($errors->has('email'))
                            <span class="text-danger">{{ $errors->first('email') }}...</span>
                        @endif
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="email">Secondary Email</label>
                        <input type="email" name="second_email" id="second_email" class="form-control form-control-sm" required value="{{old('second_email')}}">
                        @if ($errors->has('second_email'))
                            <span class="text-danger">{{ $errors->first('second_email') }}...</span>
                        @endif
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="addr_line_1">Address line 1</label>
                        <input type="text" name="addr_line_1" id="addr_line_1" class="form-control form-control-sm" required value="{{old('addr_line_1')}}">
                        @if ($errors->has('addr_line_1'))
                            <span class="text-danger">{{ $errors->first('addr_line_1') }}...</span>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="addr_line_2">Address line 2</label>
                        <input type="text" name="addr_line_2" id="addr_line_2" class="form-control form-control-sm" required value="{{old('addr_line_2')}}">
                        @if ($errors->has('addr_line_2'))
                            <span class="text-danger">{{ $errors->first('addr_line_2') }}...</span>
                        @endif
                    </div>
                
                    <div class="col-md-4 form-group">
                        <label for="landmark">Landmark</label>
                        <input type="text" name="landmark" id="landmark" class="form-control form-control-sm" required value="{{old('landmark')}}">
                        @if ($errors->has('landmark'))
                            <span class="text-danger">{{ $errors->first('landmark') }}...</span>
                        @endif
                    </div>
                
                    <div class="col-md-4 form-group">
                        <label for="area">Area</label>
                        <input type="text" class="form-control form-control-sm" placeholder="Select Area" list="arealist" id="area" name="area" required value="{{old('area')}}">
                        @if ($errors->has('area'))
                            <span class="text-danger">{{ $errors->first('area') }}...</span>
                        @endif
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
                        <input type="text" name="city" id="city" class="form-control form-control-sm" required value="{{old('city')}}">
                        @if ($errors->has('city'))
                            <span class="text-danger">{{ $errors->first('city') }}...</span>
                        @endif
                    </div>
                
                    <div class="col-md-4 form-group">
                        <label for="pincode">Pincode</label>
                        <input type="text" name="pincode" id="pincode" class="form-control form-control-sm" required value="{{old('pincode')}}">
                        @if ($errors->has('pincode'))
                            <span class="text-danger">{{ $errors->first('pincode') }}...</span>
                        @endif
                    </div>
                
                    <div class="col-md-4 form-group">
                        <label for="state">State</label>
                        <select class="selectpicker form-control form-control-sm" title="State" name="state" data-live-search="true" required="true">
                            @foreach ($states as $state) 
                                <option value="{{$state->name}}"@if($state->name == 'Maharashtra'){{"selected"}} @endif>{{$state->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="country">Country</label>
                        <select class="selectpicker form-control form-control-sm" title="Country" name="country" data-live-search="true" required="true">
                            @foreach ($countries as $country) 
                                <option value="{{$country->name}}"@if($country->name == 'India'){{"selected"}} @endif>{{$country->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="">Company Type</label><br>
                        <input class="company_type" type="radio" onchange = "show_hide_gst(this.value);" name="company_type" id="individual" value="Individual" checked>
                        <label for="individual">Individual</label>
                        <input class="company_type" type="radio" onchange = "show_hide_gst(this.value);" name="company_type" id="pvtltd" value="Pvt Ltd">
                        <label for="pvtltd">Pvt Ltd</label>
                        <input class="company_type" type="radio" onchange = "show_hide_gst(this.value);" name="company_type" id="llp" value="LLP">
                        <label for="llp">LLP</label>
                        <input class="company_type" type="radio" onchange = "show_hide_gst(this.value);" name="company_type" id="propritorship" value="Propritership">
                        <label for="propritorship">Propritership</label>
                        <input class="company_type" type="radio" onchange = "show_hide_gst(this.value);" name="company_type" id="trust" value="Trust">
                        <label for="trust">Trust</label>
                    </div>
                    <div class="col-md-4 form-group gst_no">
                        <label for="gst_no">GST No</label>
                        <input type="text" name="gst_no" id="gst_no" class="form-control form-control-sm" value="{{old('gst_no')}}">
                    </div>
                    <div class="col-md-4 form-group gst_no">
                        <div class="row">
                            <label for="gst_certificate">GST Certificate</label>
                            <input type="file" name="gst_certificate" id="gst_certificate">                                                                    
                        </div>
                        <div class="row">
                            <label for="pan_card">Pan Card</label>
                            <input class="form-control-file" type="file"name="pan_card" id="pan_card">
                        </div>
                    </div>
                {{-- </div>
                <div class="row"> --}}
                    <div class="col-md-4 form-group">
                        <div class="row">
                            <label for="profile_img">Profile Image</label>
                            <input type="file" class="form-control-file" name="profile_img" id="profile_img">
                        </div>
                    </div>
                </div>
                <center><button type="submit" class="btn btn-outline-success btn-sm">Add</button></center>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(".gst_no").hide();
    function show_hide_gst(type)
    {
        // console.log(type);
        if(type == 'Individual')
        {
            $(".gst_no").hide();
            $('#gst_certificate').prop('required',false);
            $('#pan_card').prop('required',false);
            $('#gst_no').prop('required',false);
        }
        else
        {
            $(".gst_no").show();
            $('#gst_certificate').prop('required',true);
            $('#pan_card').prop('required',true);
            $('#gst_no').prop('required',true);
        }
    }
</script>
@endsection