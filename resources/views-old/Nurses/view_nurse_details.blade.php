<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title> View Details Referrer</title>
    {{-- <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script> --}}
    @section('styles')
        
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
            <form action="<?php echo url('/');?>/referral" method="POST">
                {{ csrf_field() }}
                <div class="row justify-content-center" style="margin-top: 0rem;">
                    <div class="col-6">
                        <div class="card o-hidden border-0 shadow-lg">
                            
                            <div class="card-header bg-primary text-white">
                                <h4 class="m-0 font-weight-bold">View Referrer Details</h4>
                            </div>
                            <div class="card-body">
                                @if(session()->has('message'))
                                    <div class="alert alert-success">
                                        {{ session()->get('message') }}
                                    </div>
                                @endif
                                
                                {{--hidden value--}}
                                <input type="hidden" name="nurse_id" id="nurse_id" value="{{$nurses_data[0]['id']}}">
        
                                <div class="row">
                                    <div class="col-md-4 form-group text-right">
                                        <label for="name" class="control-label"><b>Name</b></label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="name" id="name" placeholder="name" value={{$nurses_data[0]['name']}} required="true">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 form-group text-right">
                                        <label for="primary_contact" class="control-label"><b>Primary Contact</b></label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input type="number" class="form-control" name="primary_contact" id="primary_contact" value="{{$nurses_data[0]['primary_contact']}}" maxlength="10" placeholder="Mobile Number" required="true">
                                        </div>
                                    </div> 
                                </div>
                                <div class="row">
                                    <div class="col-md-4 form-group text-right">
                                        <label for="secondary_contact" class="control-label"><b>Secondary Contact</b></label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input type="number" class="form-control" name="secondary_contact" id="secondary_contact" value="{{$nurses_data[0]['secondary_contact']}}" maxlength="10" placeholder="Mobile Number" >
                                        </div>
                                    </div> 
                                </div>
                                <div class="row">
                                    <div class="col-md-4 form-group text-right">
                                        <label for="gender" class="control-label"><b>Gender</b></label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="gender[]" id="male" value="Male" @if($nurses_data[0]['gender']=='Male') {{"checked"}} @endif>
                                            <label class="form-check-label" for="male">Male</label>
                                          </div>
                                          <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="gender[]" id="female" value="Female" @if($nurses_data[0]['gender']=='Female') {{"checked"}} @endif>
                                            <label class="form-check-label" for="female">Female</label>
                                          </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 form-group text-right">
                                        <label for="profession" class="control-label"><b>Profession</b></label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input type="text" class="form-control" profession="profession" id="profession" placeholder="profession" value="{{$nurses_data[0]['profession']}}" required="true">
                                        </div>
                                    </div>
                                </div>
        
                                
                                <hr>
                                <center>
                                    <div class="col-md-8">
                                        <button type="submit" name="submit" class="btn btn-primary" value="submit">Referral it</button>
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