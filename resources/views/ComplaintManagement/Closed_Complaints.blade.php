@extends('header_and_sidebar')
<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
    
        <title>Raise Complaint</title>
        {{-- <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script> --}}
        @section('styles')
            <style>
                .card-header {
                    cursor: pointer;
                }
            </style>
        @endsection
    </head>

<body id="page-top">	
        <!-- Page Wrapper -->
        
    @section('content')
        @if(session()->has('message_delete'))
            <div class="alert alert-danger">
                {{ session()->get('message_delete') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if(session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"  style="background-color: #4a6fdc; color: white;" >
                        <center>Closed Complaints</center>
                    </div> 
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <form action="{{url('/')}}/date_filter_complaints/closed" method="post">
                                    @csrf
                                    <div class="row">
                                        <label for="Filter"><strong>Filter :</strong></label>
                                        <div class="col-md-4">
                                            <input type="date" class="form-control" name="start_date" id="start_date" required>
                                        </div>
                                        <strong>To</strong> 
                                        <div class="col-md-4">
                                        <input type="date" class="form-control" name="end_date" id="end_date" required>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="submit" class="btn btn-outline-primary btn-sm btn-block" name="submit" id="submit" value="Submit">
                                        </div>
                                    </div>
                                </form>
                               
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-striped " id="records" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Sr No</th>
                                            <th>Closed Date</th>
                                            <th>Customer Name</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $srno = 1;
                                        @endphp
                                        @foreach($closed_complaints as $get_complaint)
                                            @for($i=0; $i <count($get_complaint['customer_ids']); $i++)
                                                <tr>
                                                    <td>{{$srno}}</td>
                                                    <td>{{date('d-m-Y',strtotime($get_complaint['closed_date']))}}</td>
                                                    <td>{{$get_complaint['customer_names'][$i]}}</td>
                                                    <td><span class="badge badge-success">Closed</span></td>
                                                    <td>
                                                        <a href="{{url('/')}}/view_closed_complaint/{{$get_complaint['customer_ids'][$i]}}/{{$get_complaint['closed_date']}}" class="btn btn-outline-primary">View</a>
                                                    </td>
                                                </tr>
                                                @php
                                                    $srno++;
                                                @endphp
                                            @endfor
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
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