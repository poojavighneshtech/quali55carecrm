<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Edit Jd Lead</title>
    {{-- <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script> --}}
    @section('styles')
        {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.2/css/bootstrap.min.css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.min.css"/>
        <!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script> -->
        <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script> --}}
        <style>
            .morecontent span {
                display: none;
            }
            .morelink {
                display: block;
            }
        </style>
    @endsection
</head>

<body id="page-top">	
		<!-- Page Wrapper -->
    
    @extends('header_and_sidebar')
       
            @section('content')
            <form action="<?php echo url('/');?>/progress_jd_lead" method="POST">
                {{ csrf_field() }}
                <div class="row justify-content-center" style="margin-top: 0rem;">
                    <div class="col-6">
                        <div class="card o-hidden border-0 shadow-lg">
                            
                            <div class="card-header bg-primary text-white">
                                <h4 class="m-0 font-weight-bold">Jd Lead Details</h4>
                            </div>
                            <div class="card-body">
                                @if(session()->has('message'))
                                    <div class="alert alert-success">
                                        {{ session()->get('message') }}
                                    </div>
                                @endif
                                
                                {{--hidden value--}}
                                <input type="hidden" name="jd_lead_id" id="nurse_id" value="">
        
                                <div class="row">
                                    <div class="col-md-4 form-group text-right">
                                        <label for="name" class="control-label"><b>Name</b></label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="name" id="name" placeholder="name" value="{{$jd_lead_details[0]['name']}}" required="true">
                                            <input type="hidden" name="jd_lead_id" id="jd_lead_id" value="{{$jd_lead_details[0]['jd_leads_id']}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 form-group text-right">
                                        <label for="mobile_no" class="control-label"><b>Mobile No</b></label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input type="number" class="form-control" name="mobile_no" id="mobile_no" value="{{$jd_lead_details[0]['mobile']}}" maxlength="10" placeholder="Mobile Number" required="true">
                                        </div>
                                    </div> 
                                </div>
                                <div class="row">
                                    <div class="col-md-4 form-group text-right">
                                        <label for="phone" class="control-label"><b>Phone</b></label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input type="number" class="form-control" name="phone" id="phone" value="{{$jd_lead_details[0]['phone']}}" maxlength="10" placeholder="Mobile Number" >
                                        </div>
                                    </div> 
                                </div>
                                <div class="row">
                                    <div class="col-md-4 form-group text-right">
                                        <label for="pincode" class="control-label"><b>pincode</b></label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input type="number" class="form-control" name="pincode" id="pincode" value="{{$jd_lead_details[0]['pincode']}}" maxlength="10" placeholder="Pincode" >
                                        </div>
                                    </div> 
                                </div>
                                <div class="row">
                                    <div class="col-md-4 form-group text-right">
                                        <label for="city" class="control-label"><b>City</b></label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="city" id="city" value="{{$jd_lead_details[0]['city']}}" maxlength="10" placeholder="Mobile Number" >
                                        </div>
                                    </div> 
                                </div>
                                <div class="row">
                                    <div class="col-md-4 form-group text-right">
                                        <label for="area" class="control-label"><b>Area</b></label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="area" id="area" value="{{$jd_lead_details[0]['area']}}" maxlength="10" placeholder="Mobile Number" >
                                        </div>
                                    </div> 
                                </div>
                                <div class="row">
                                    <div class="col-md-4 form-group text-right">
                                        <label for="email" class="control-label"><b>Email</b></label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input type="Emaiil" class="form-control" name="email" id="email" value="{{$jd_lead_details[0]['email']}}"  placeholder="Email" >
                                        </div>
                                    </div> 
                                </div>
                                
                                <hr>
                                <center>
                                    <div class="col-md-8">
                                        <button type="submit" name="submit" class="btn btn-primary" value="submit">Progress it</button>
                                        {{-- <button type="reset" name="reset" class="btn btn-default" value="reset">Clear</button> --}}
                                    </div>
                                </center>
                                
                            </div>
                        </div>   
                    </div>
                </div> 
            </form>
        @endsection
</body>
@section('script')

@endsection
</html>