<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Collection Report</title>
    {{-- <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script> --}}
    @section('styles')
        <style>
        </style>
    @endsection
</head>

<body id="page-top">	
		<!-- Page Wrapper -->
    
    {{-- @extends('header_and_sidebar') --}}
@extends('new-sidebar')

       
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

            @if(session()->has('message_search'))
                <div class="alert alert-danger">
                    {{ session()->get('message_search') }}
                </div>
            @endif 
        
            <div class="card">
                <div class="card-header"  style="background-color: #4a6fdc; color: white;" >
                    <center>Pending Payment Orders</center>
                </div>
                <div class="card-body">
                       {{print_r($collection_report)}}
                </div>
            </div>
        </div>
    @endsection
</body>
@section('script')
    @if(session()->has('pop_message'))
        <script>
            $(function() {
                $('.modal').modal('show');
            });
        </script>
    @endif
    <script>
        
    </script>
    @endsection
</html>