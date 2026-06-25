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
                    View Lead
                </h5>
                <div class="container" id="main-div">
                    <div class="card card-body">
                        <h5>Basic Details</h5>
                        <div class="row form-group">
                            <div class="col-md-6">
                                <label for="lead_date">Date: </label>
                                <label for="lead_date">{{$lead->lead_date}}</label>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-6">
                                <label for="customer_name">Customer Name: </label>
                                <label for="">{{$lead->customer_name}}</label>
                            </div>
                            <div class="col-md-6">
                                <label for="patient_name">Patient Name: </label>
                                <label for="">{{$lead->patient_name}}</label>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-6">
                                <label for="contact_no">Contact No: </label>
                                <label for="">{{$lead->contact_no}}</label>
                            </div>
                            <div class="col-md-6">
                                <label for="alt_contact_no">Alternative contact No:</label>
                                <label for="">{{$lead->alt_contact_no}}</label>
                            </div>
                        </div>
                    </div>
                    <div class="card card-body my-2">
                        <h5>Address</h5>
                        <div class="row form-group">
                            <div class="col-md-6">
                                <label for="address_line_1">Address line 1:</label>
                                <label for="">{{$lead->address_line_1}}</label>
                            </div>
                            <div class="col-md-6">
                                <label for="address_line_2">Address line 2:</label>
                                <label for="">{{$lead->address_line_2}}</label>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-6">
                                <label for="landmark">Landmark:</label>
                                <label for="">{{$lead->landmark}}</label>
                            </div>
                            <div class="col-md-6">
                                <label for="area">Area:</label>
                                <label for="">{{$lead->area}}</label>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-6">
                                <label for="city">City:</label>
                                <label for="">{{$lead->city}}</label>
                            </div>
                            <div class="col-md-6">
                                <label for="state">State:</label>
                                <label for="">{{$lead->state}}</label>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-6">
                                <label for="pincode">Pincode:</label>
                                <label for="">{{$lead->pincode}}</label>
                            </div>
                        </div>
                    </div>
                    <div class="card card-body my-2">
                        <div class="row form-group">
                            <div class="col">
                                <label for="patient_conditions">Patient Conditions:</label>
                                <label for="">{{$lead->patient_conditions}}</label>
                            </div>
                        </div>
                    </div>
                    <div class="card card-body my-2">
                        <h5>Requirements</h5>
                        <div class="row form-group">
                            <div class="col-md-6">
                                <label for="duty_type">Duty Type:</label>
                                <label for="">{{$lead->duty_type}}</label>
                                
                            </div>
                            <div class="col-md-6">
                                <label for="duty_hours">Duty Hours:</label>
                                <label for="">{{$lead->duty_hours}}</label>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-6">
                                <label for="service_type">Service Type:</label>
                                <label for="">{{$lead->service_type}}</label>
                            </div>
                            <div class="col-md-6">
                                <label for="gender">Gender:</label>
                                <label for="">{{$lead->gender}}</label>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-6">
                                <label for="start_date">Start Date:</label>
                                <label for="">{{date('d-M-y',strtotime($lead->start_date))}}</label>
                            </div>
                            <div class="col-md-6">
                                <label for="stop_date">Stop Date:</label>
                                <label for="">{{($lead->stop_date)?date('d-M-y',strtotime($lead->stop_date)):"-"}}</label>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-6">
                                <label for="charges">Charges:</label>
                                <label for="">{{$lead->charges}}</label>
                            </div>
                            <div class="col-md-6">
                                <label for="status">Status:</label>
                                <label for="">{{$lead->status}}</label>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-6">
                                <label for="lead_source">Source:</label>
                                <label for="">{{$lead->lead_source}}</label>
                            </div>
                            <div class="col-md-6">
                                <label for="referred_by">Referred By:</label>
                                <label for="">{{$lead->referred_by}}</label>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col">
                                <label for="remark">Remark:</label>
                                <label for="">{{$lead->remark}}</label>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col text-center">
                                <a type="button" href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary">Back</a>
                            </div>
                        </div>
                    </div>
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