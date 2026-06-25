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
                    Edit Lead
                </h5>
                <div class="container" id="main-div">
                    <form action="{{route('nursing-care-update',$lead->id)}}" clas="form" method="post">
                        @csrf
                        <div class="card card-body">
                            <h5>Basic Details</h5>
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <label for="lead_date">Date <span class="req text-danger">*</span></label>
                                    <input type="date" name="lead_date" id="lead_date" class="form-control form-control-sm @error('lead_date') is-invalid @enderror" value="{{(old('lead_date'))?old('lead_date'):$lead->lead_date}}">
                                    @error('lead_date')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <label for="customer_name">Customer Name<span class="req text-danger">*</span></label>
                                    <input type="text" name="customer_name" id="customer_name" class="form-control form-control-sm @error('customer_name') is-invalid @enderror" value="{{(old('customer_name'))?old('customer_name'):$lead->customer_name}}">
                                    @error('customer_name')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="patient_name">Patient Name</label>
                                    <input type="text" name="patient_name" id="patient_name" class="form-control form-control-sm @error('patient_name') is-invalid @enderror" value="{{(old('patient_name'))?old('patient_name'):$lead->patient_name}}">
                                    @error('patient_name')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <label for="contact_no">Contact No<span class="req text-danger">*</span></label>
                                    <input type="text" name="contact_no" id="contact_no" class="form-control form-control-sm @error('contact_no') is-invalid @enderror" value="{{(old('contact_no'))?old('contact_no'):$lead->contact_no}}">
                                    @error('contact_no')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="alt_contact_no">Alternative contact No</label>
                                    <input type="text" name="alt_contact_no" id="alt_contact_no" class="form-control form-control-sm @error('alt_contact_no') is-invalid @enderror" value="{{(old('alt_contact_no'))?old('alt_contact_no'):$lead->alt_contact_no}}">
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
                                    <input type="text" name="address_line_1" id="address_line_1" class="form-control form-control-sm @error('address_line_1') is-invalid @enderror" value="{{(old('address_line_1'))?old('address_line_1'):$lead->address_line_1}}">
                                    @error('address_line_1')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="address_line_2">Address line 2</label>
                                    <input type="text" name="address_line_2" id="address_line_2" class="form-control form-control-sm @error('address_line_2') is-invalid @enderror" value="{{(old('address_line_2'))?old('address_line_2'):$lead->address_line_2}}">
                                    @error('address_line_2')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <label for="landmark">Landmark</label>
                                    <input type="text" name="landmark" id="landmark" class="form-control form-control-sm @error('landmark') is-invalid @enderror" value="{{(old('landmark'))?old('landmark'):$lead->landmark}}">
                                    @error('landmark')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="area">Area<span class="req text-danger">*</span></label>
                                    <input type="text" name="area" id="area" class="form-control form-control-sm @error('area') is-invalid @enderror" value="{{(old('area'))?old('area'):$lead->area}}">
                                    @error('area')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <label for="city">City<span class="req text-danger">*</span></label>
                                    <input type="text" name="city" id="city" class="form-control form-control-sm @error('city') is-invalid @enderror" value="{{(old('city'))?old('city'):$lead->city}}">
                                    @error('city')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="state">State<span class="req text-danger">*</span></label>
                                    <select name="state" id="state" class="select selectpicker form-control form-control-sm @error('state') is-invalid @enderror" data-live-search="true" title="Select state">
                                        @foreach($states as $key=>$state)
                                            <option value="{{$state->name}}"@if(old('state') == $state->name){{"selected"}}@elseif($state->name == $lead->state){{"selected"}}@endif>{{$state->name}}</option>
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
                                    <input type="text" name="pincode" id="pincode" class="form-control form-control-sm @error('pincode') is-invalid @enderror" value="{{(old('pincode'))?old('pincode'):$lead->pincode}}">
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
                                    <textarea name="patient_conditions" id="patient_conditions" class="form-control form-control-sm @error('patient_conditions') is-invalid @enderror" rows="4" value="{{$lead->patient_conditions}}">{{(old('patient_conditions'))?old('patient_conditions'):$lead->patient_conditions}}</textarea>
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
                                    <select name="duty_type" id="duty_type" class="select selectpicker form-control form-control-sm @error('duty_type') is-invalid @enderror">
                                        <option value="1" @if(old('duty_type') == "1"){{"selected"}}@elseif($lead->duty_type == 'Day'){{"selected"}}@endif>Day</option>
                                        <option value="2" @if(old('duty_type') == "2"){{"selected"}}@elseif($lead->duty_type == 'Night'){{"selected"}}@endif>Night</option>
                                        <option value="3" @if(old('duty_type') == "3"){{"selected"}}@elseif($lead->duty_type == 'Full'){{"selected"}}@endif>Full</option>
                                    </select>
                                    @error('duty_type')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="duty_hours">Duty Hours</label>
                                    <select name="duty_hours" id="duty_hours" class="select selectpicker form-control form-control-sm @error('duty_hours') is-invalid @enderror">
                                        <option value="12" @if(old('duty_hours') == "12"){{"selected"}}@elseif($lead->duty_hours == '12'){{"selected"}}@endif>12</option>
                                        <option value="24" @if(old('duty_hours') == "24"){{"selected"}}@elseif($lead->duty_hours == '24'){{"selected"}}@endif>24</option>
                                    </select>
                                    @error('duty_hours')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <label for="service_type">Service Type</label>
                                    <select name="service_type" id="service_type" class="select selectpicker form-control form-control-sm @error('service_type') is-invalid @enderror" >
                                        <option value="1" @if(old('sservice_type') == "1"){{"selected"}}@elseif($lead->service_type == 'Nurse'){{"selected"}}@endif>Nurse</option>
                                        <option value="2" @if(old('sservice_type') == "2"){{"selected"}}@elseif($lead->service_type == 'Care Taker'){{"selected"}}@endif>Care Taker</option>
                                    </select>
                                    @error('service_type')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="gender">Gender</label>
                                    <select name="gender" id="gender" class="select selectpicker form-control form-control-sm @error('gender') is-invalid @enderror">
                                        <option value="male" @if(old('gender') == "male"){{"selected"}}@elseif($lead->gender == 'male'){{"selected"}}@endif>Male</option>
                                        <option value="female" @if(old('gender') == "female"){{"selected"}}@elseif($lead->gender == 'female'){{"selected"}}@endif>Female</option>
                                        <option value="anyone" @if(old('gender') == "anyone"){{"selected"}}@elseif($lead->gender == 'anyone'){{"selected"}}@endif>Anyone</option>
                                    </select>
                                    @error('gender')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control form-control-sm @error('start_date') is-invalid @enderror"  value="{{(old('start_date'))?old('start_date'):$lead->start_date}}">
                                    @error('start_date')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="stop_date">Stop Date</label>
                                    <input type="date" name="stop_date" id="stop_date" class="form-control form-control-sm @error('stop_date') is-invalid @enderror"  value="{{(old('stop_date'))?old('stop_date'):$lead->stop_date}}">
                                    @error('stop_date')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <label for="charges">Charges</label>
                                    <input type="number" name="charges" id="charges" class="form-control form-control-sm @error('charges') is-invalid @enderror"  value="{{(old('charges'))?old('charges'):$lead->charges}}">
                                    @error('charges')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="status">Status<span class="req text-danger">*</span></label>
                                    <select name="status" id="status" class="select selectpicker form-control form-control-sm @error('status') is-invalid @enderror">
                                        <option value="1" @if(old('status') == "1"){{"selected"}}@elseif($lead->status == 'Process'){{"selected"}}@endif>Process</option>
                                        <option value="2" @if(old('status') == "2"){{"selected"}}@elseif($lead->status == 'Live'){{"selected"}}@endif>Live</option>
                                        <option value="3" @if(old('status') == "3"){{"selected"}}@elseif($lead->status == 'Stopped'){{"selected"}}@endif>Stopped</option>
                                        <option value="4" @if(old('status') == "4"){{"selected"}}@elseif($lead->status == 'Cancelled'){{"selected"}}@endif>Cancelled</option>
                                    </select>
                                    @error('status')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <label for="lead_source">Source</label>
                                    <input type="text" name="lead_source" id="lead_source" class="form-control form-control-sm @error('lead_source') is-invalid @enderror" value="{{(old('lead_source'))?old('lead_source'):$lead->lead_source}}">
                                    @error('lead_source')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="referred_by">Referred By</label>
                                    <input type="text" name="referred_by" id="referred_by" class="form-control form-control-sm @error('referred_by') is-invalid @enderror" value="{{(old('referred_by'))?old('referred_by'):$lead->referred_by}}">
                                    @error('referred_by')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col">
                                    <label for="remark">Remark</label>
                                    <textarea name="remark" id="remark" class="form-control form-control-sm @error('remark') is-invalid @enderror"  value="{{(old('remark'))?old('remark'):$lead->remark}}">{{(old('remark'))?old('remark'):$lead->remark}}</textarea>
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