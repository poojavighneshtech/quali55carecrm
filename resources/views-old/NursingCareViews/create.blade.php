@extends('header_and_sidebar')

@section('styles')
    <style>
        .container {
            max-width: 600px;
            margin: 0 auto;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
        }
    </style>
@endsection

@section('content')
    @if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
    @endif
    @if(session()->has('error'))
    <div class="alert alert-danger">
        {{ session()->get('error') }}
    </div>
    @endif
    <div class="my-2">
        <div class="card" id="filter_card">
            <div class="card-header border-primary" id="filter_card">
                <div class="row">
                    <div class="col text-primary"aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                        <strong>Nursing Care</strong>
                    </div>
                </div>
            </div>
            <div class="card-body border-primary">
                <h5 class="text-center">
                    Add Lead
                </h5>
                <div class="container" id="main-div">
                    <form action="{{route('nursing-care-store')}}" clas="form" method="post">
                        @csrf
                        <div class="card card-body">
                            <h5>Basic Details</h5>
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <label for="lead_date">Date <span class="req text-danger">*</span></label>
                                    <input type="date" name="lead_date" id="lead_date" class="form-control form-control-sm @error('lead_date') is-invalid @enderror" value="{{date('Y-m-d')}}">
                                    @error('lead_date')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <label for="customer_name">Customer Name<span class="req text-danger">*</span></label>
                                    <input type="text" name="customer_name" id="customer_name" class="form-control form-control-sm @error('customer_name') is-invalid @enderror" value="{{old('customer_name')}}">
                                    @error('customer_name')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="patient_name">Patient Name</label>
                                    <input type="text" name="patient_name" id="patient_name" class="form-control form-control-sm @error('patient_name') is-invalid @enderror" value="{{old('patient_name')}}">
                                    @error('patient_name')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <label for="contact_no">Contact No<span class="req text-danger">*</span></label>
                                    <input type="text" name="contact_no" id="contact_no" class="form-control form-control-sm @error('contact_no') is-invalid @enderror" value="{{old('contact_no')}}">
                                    @error('contact_no')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="alt_contact_no">Alternative contact No</label>
                                    <input type="text" name="alt_contact_no" id="alt_contact_no" class="form-control form-control-sm @error('alt_contact_no') is-invalid @enderror" value="{{old('alt_contact_no')}}">
                                    @error('alt_contact_no')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="card card-body my-2">
                            <h5>Address</h5>
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <label for="address_line_1">Address line 1<span class="req text-danger">*</span></label>
                                    <input type="text" name="address_line_1" id="address_line_1" class="form-control form-control-sm @error('address_line_1') is-invalid @enderror" value="{{old('address_line_1')}}">
                                    @error('address_line_1')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="address_line_2">Address line 2</label>
                                    <input type="text" name="address_line_2" id="address_line_2" class="form-control form-control-sm @error('address_line_2') is-invalid @enderror" value="{{old('address_line_2')}}">
                                    @error('address_line_2')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <label for="landmark">Landmark</label>
                                    <input type="text" name="landmark" id="landmark" class="form-control form-control-sm @error('landmark') is-invalid @enderror" value="{{old('landmark')}}">
                                    @error('landmark')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="area">Area<span class="req text-danger">*</span></label>
                                    <input type="text" name="area" id="area" class="form-control form-control-sm @error('area') is-invalid @enderror" value="{{old('area')}}">
                                    @error('area')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <label for="city">City<span class="req text-danger">*</span></label>
                                    <input type="text" name="city" id="city" class="form-control form-control-sm @error('city') is-invalid @enderror" value="{{old('city')}}">
                                    @error('city')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="state">State<span class="req text-danger">*</span></label>
                                    <select name="state" id="state" class="select selectpicker form-control form-control-sm @error('state') is-invalid @enderror" value="{{old('state')}}" title="Select state">
                                        @foreach($states as $key=>$state)
                                            <option value="{{$state->name}}" @if(old('state') == $state->name){{"selected"}}@endif>{{$state->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('state')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <label for="pincode">Pincode</label>
                                    <input type="text" name="pincode" id="pincode" class="form-control form-control-sm @error('pincode') is-invalid @enderror" value="{{old('pincode')}}">
                                    @error('pincode')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="card card-body my-2">
                            <div class="row form-group">
                                <div class="col">
                                    <label for="patient_conditions">Patient Conditions</label>
                                    <textarea name="patient_conditions" id="patient_conditions" class="form-control form-control-sm @error('patient_conditions') is-invalid @enderror" rows="4" value="{{old('patient_conditions')}}">{{old('patient_conditions')}}</textarea>
                                    @error('patient_conditions')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="card card-body my-2">
                            <h5>Requirements</h5>
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <label for="duty_type">Duty Type</label>
                                    <select name="duty_type" id="duty_type" class="select selectpicker form-control form-control-sm @error('duty_type') is-invalid @enderror"  value="{{old('duty_type')}}">
                                        <option value="1" @if(old('duty_type') == "1"){{"selected"}}@endif>Day</option>
                                        <option value="2" @if(old('duty_type') == "2"){{"selected"}}@endif>Night</option>
                                        <option value="3" @if(old('duty_type') == "3"){{"selected"}}@endif>Full</option>
                                    </select>
                                    @error('duty_type')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="duty_hours">Duty Hours</label>
                                    <select name="duty_hours" id="duty_hours" class="select selectpicker form-control form-control-sm @error('duty_hours') is-invalid @enderror"  value="{{old('duty_hours')}}">
                                        <option value="12" @if(old('duty_hours') == "12"){{"selected"}}@endif>12</option>
                                        <option value="24" @if(old('duty_hours') == "24"){{"selected"}}@endif>24</option>
                                    </select>
                                    @error('duty_hours')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <label for="service_type">Service Type</label>
                                    <select name="service_type" id="service_type" class="select selectpicker form-control form-control-sm @error('service_type') is-invalid @enderror"  value="{{old('service_type')}}">
                                        <option value="1" @if(old('service_type') == "1"){{"selected"}}@endif>Nurse</option>
                                        <option value="2" @if(old('service_type') == "2"){{"selected"}}@endif>Care Taker</option>
                                    </select>
                                    @error('service_type')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="gender">Gender</label>
                                    <select name="gender" id="gender" class="select selectpicker form-control form-control-sm @error('gender') is-invalid @enderror"  value="{{old('gender')}}">
                                        <option value="male" @if(old('gender') == "male"){{"selected"}}@endif>Male</option>
                                        <option value="female" @if(old('gender') == "female"){{"selected"}}@endif>Female</option>
                                        <option value="anyone" @if(old('gender') == "anyone"){{"selected"}}@endif>Anyone</option>
                                    </select>
                                    @error('gender')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control form-control-sm @error('start_date') is-invalid @enderror"  value="{{old('start_date')}}">
                                    @error('start_date')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="stop_date">Stop Date</label>
                                    <input type="date" name="stop_date" id="stop_date" class="form-control form-control-sm @error('stop_date') is-invalid @enderror"  value="{{old('stop_date')}}">
                                    @error('stop_date')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <label for="charges">Charges</label>
                                    <input type="number" name="charges" id="charges" class="form-control form-control-sm @error('charges') is-invalid @enderror"  value="{{old('charges')}}">
                                    @error('charges')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="status">Status<span class="req text-danger">*</span></label>
                                    <select name="status" id="status" class="select selectpicker form-control form-control-sm @error('status') is-invalid @enderror"  value="{{old('status')}}">
                                        <option value="1" @if(old('status') == "1"){{"selected"}}@endif>Process</option>
                                        <option value="2" @if(old('status') == "2"){{"selected"}}@endif>Live</option>
                                        <option value="3" @if(old('status') == "3"){{"selected"}}@endif>Stopped</option>
                                        <option value="4" @if(old('status') == "4"){{"selected"}}@endif>Cancelled</option>
                                    </select>
                                    @error('status')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <label for="lead_source">Source</label>
                                    <input type="text" name="lead_source" id="lead_source" class="form-control form-control-sm @error('lead_source') is-invalid @enderror" value="{{old('lead_source')}}">
                                    @error('lead_source')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="referred_by">Referred By</label>
                                    <input type="text" name="referred_by" id="referred_by" class="form-control form-control-sm @error('referred_by') is-invalid @enderror" value="{{old('referred_by')}}">
                                    @error('referred_by')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col">
                                    <label for="remark">Remark</label>
                                    <textarea name="remark" id="remark" class="form-control form-control-sm @error('remark') is-invalid @enderror"  value="{{old('remark')}}">{{old('remark')}}</textarea>
                                    @error('remark')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col text-center">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        console.log("refresh");
        $(document).ready(function(){
            // $("#state").selectpicker("refresh");
        });
        $(window).on('resize load', function() {
        if ($(window).width() <= 768) { 
            $("#main-div").removeClass("container");
        }
        else {
            $("#min-div").addClass("container");
        }
        });
    </script>
@endsection