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
            
            <div class="card">
                <div class="card-header"  style="background-color: #4a6fdc; color: white;" >
                    <center>Nursing Care Leads</center>
                </div> 
                <div class="card-body">
                    <div class="row">
                        <div class="table">
                            <table class="table table-striped table-bordered " id="records">
                                <thead>
                                    <tr>
                                        <th>Sr No</th>
                                        <th>Date</th>
                                        <th>Name</th>
                                        <th>Contact Name</th>
                                        <th>Location</th>
                                        <th>Service Required</th>
                                        <th>Lead Owner</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($get_all_leads as $key=> $all_leads) 
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{$all_leads['created_date']}}</td>
                                            <td>{{$all_leads['name']}}</td>
                                            <td>{{$all_leads['contact_no']}}</td>
                                            <td>{{$all_leads['location']}}</td>
                                            <td>{{$all_leads['service_required']}}</td>
                                            <td>{{$all_leads['username']}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    @endsection
</body>
@section('script')
  
@endsection
</html>