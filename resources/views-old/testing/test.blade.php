<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Renewal Pickup</title>
    {{-- <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script> --}}
    @section('styles')
        
        <style>
            /* .morecontent span {
                display: none;
            }
            .morelink {
                display: block;
            } */
            #records tbody td{
                padding: 0.08rem;
            }
        </style>
    @endsection
</head>

<body id="page-top">	
		<!-- Page Wrapper -->
    @extends('header_and_sidebar')
        
    @section('content')
        <form action="{{url('/')}}/testing_esc" method="post">
            @csrf
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="customer_name" id="inpt_cutomer_name" value="{{$cust[0]->customer_name}}">
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    @endsection
</body>
@section('script')
    
@endsection
</html>