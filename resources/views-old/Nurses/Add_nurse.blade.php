<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Add Referrer</title>
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
            <form action="<?php echo url('/');?>/add_nurse" method="POST">
                {{ csrf_field() }}
                <div class="row justify-content-center" style="margin-top: 0rem;">
                    <div class="col-6">
                        <div class="card o-hidden border-0 shadow-lg">
                            
                            <div class="card-header bg-primary text-white">
                                <h4 class="m-0 font-weight-bold">Add Referrer Details</h4>
                            </div>
                            <div class="card-body">
                                @if(session()->has('message'))
                                    <div class="alert alert-success">
                                        {{ session()->get('message') }}
                                    </div>
                                @endif
                                
                                {{--hidden value--}}
                                <input type="hidden" name="nurse_id" id="nurse_id" value="">
        
                                <div class="row">
                                    <div class="col-md-4 form-group text-right">
                                        <label for="name" class="control-label"><b>Name</b></label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="nurse_name" id="nurse_name" placeholder="Name" value="" required="true">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 form-group text-right">
                                        <label for="primary_contact" class="control-label"><b>Primary Contact</b></label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input type="number" class="form-control" name="primary_contact" id="primary_contact" value="" maxlength="10" placeholder="Mobile Number" required="true">
                                        </div>
                                    </div> 
                                </div>
                                <div class="row">
                                    <div class="col-md-4 form-group text-right">
                                        <label for="secondary_contact" class="control-label"><b>Secondary Contact</b></label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input type="number" class="form-control" name="secondary_contact" id="secondary_contact" value="" maxlength="10" placeholder="Mobile Number" >
                                        </div>
                                    </div> 
                                </div>
                                <div class="row">
                                    <div class="col-md-4 form-group text-right">
                                        <label for="gender" class="control-label"><b>Gender</b></label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="gender" id="male" value="Male" >
                                            <label class="form-check-label" for="male">Male</label>
                                          </div>
                                          <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="gender" id="female" value="Female">
                                            <label class="form-check-label" for="female">Female</label>
                                          </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4 form-group text-right">
                                        <label for="city" class="control-label"><b>City</b></label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input type="text" class="form-control" city="city" id="city" placeholder="city" list="city_list" required>
                                            <datalist id="city_list">
                                                @foreach($cities as $city)
                                                    <option value="{{$city['name']}}">{{$city['name']}}</option>
                                                @endforeach
                                            </datalist>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 form-group text-right">
                                        <label for="profession" class="control-label"><b>Profession</b></label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="profession" id="profession" placeholder="profession" required>
                                        </div>
                                    </div>  
                                </div>
        
                                
                                <hr>
                                <center>
                                    <div class="col-md-8">
                                        <button type="submit" name="submit" class="btn btn-primary" value="submit">Submit</button>
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