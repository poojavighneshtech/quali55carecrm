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
                img {
                    max-width: 100%;
                    max-height: 50%;
                    padding-top:10px;
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
                        <center>Customer Complaint</center>
                    </div> 
                    <form action="{{url('/')}}/close_complaint" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="row" id="customer_div" style="display: block">
                                <div class="col-md-12">
                                    <div id="accordion" class="accordion_div">
                                        <div class="card">
                                            <div class="card-header" id="headingOne" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                Customer Details
                                            </div>
                                            
                                            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-sm-4">
                                                                    <p><strong>Customer Name :</strong></p>
                                                                </div>
                                                                <div class="col-md-8 text-left">
                                                                    <p id="p_customer_name">{{$get_complaint_detail[0]['customer_name']}}</p>
                                                                    <input type="hidden" name="customer_id" id="customer_id">
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-sm-4">
                                                                    <p><strong>Contact no :</strong></p>
                                                                </div>
                                                                <div class="col-md-8 text-left">
                                                                    <p id="p_customer_primary_no">{{$get_complaint_detail[0]['primary_contact_no']}}</p>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-sm-4">
                                                                    <p><strong>Email :</strong></p>
                                                                </div>
                                                                <div class="col-md-8 text-left">
                                                                    <p id="p_customer_email">{{$get_complaint_detail[0]['email_id']}}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-sm-3">
                                                                    <p><strong>Location :</strong></p>
                                                                </div>
                                                                <div class="col-sm-9 text-left">
                                                                    <p id="p_customer_location">{{$get_complaint_detail[0]['location']}}</p>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-sm-3">
                                                                    <p><strong>Address :</strong></p>
                                                                </div>
                                                                <div class="col-md-9 text-left">
                                                                    <p id="p_customer_address">{{$get_complaint_detail[0]['address_line_1']}},{{$get_complaint_detail[0]['address_line_2']}},{{$get_complaint_detail[0]['area']}},{{$get_complaint_detail[0]['city']}},{{$get_complaint_detail[0]['landmark']}}-{{$get_complaint_detail[0]['pincode']}}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="card">
                                            <div class="card-header" id="headingTwo" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                <span id="card_header_text">Customer Complaints</span>
                                            </div>
                                            <div id="collapseTwo" class="collapse show" aria-labelledby="headingTwo" data-parent="#accordion">
                                                <div class="card-body">
                                                    <div class="row" id="product_div" style="display: block">
                                                        <div class="container">
                                                            <div class="table" id="div_table">
                                                                <table class="table table-striped table-bordered table-responsive" id="records">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>SrNo.</th>
                                                                            <th>Complaint Date</th>
                                                                            <th>Complaint ID</th>
                                                                            <th>Product Name</th>
                                                                            <th>Vendor Name</th>
                                                                            <th>Delivered By</th>
                                                                            <th>Lead Owner</th>
                                                                            <th>Status</th>
                                                                            <th>Action</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @php
                                                                            $srno = 1;
                                                                        @endphp
                                                                        @foreach($get_complaint_detail as $key => $get_complaint)
                                                                            <tr>
                                                                                <td>{{$srno}}</td>
                                                                                <td>{{date('d-m-Y',strtotime($get_complaint['complaint_date']))}}</td>
                                                                                <td>
                                                                                    <a href="{{url('/')}}/complaint_details/{{$get_complaint['generated_complaint_id']}}">{{$get_complaint['generated_complaint_id']}}</a>
                                                                                </td>
                                                                                <td>{{$get_complaint['product_name']}}</td>
                                                                                <td>{{$get_complaint['vendor_name']}}</td>
                                                                                <td>{{$get_complaint['delivered_by']}}</td>
                                                                                <td>{{$get_complaint['lead_owner']}}</td>
                                                                                <td>{{$get_complaint['status']}}</td>
                                                                                <td>
                                                                                    <a href="{{url('/')}}/complaint_details/{{$get_complaint['generated_complaint_id']}}" class="btn btn-outline-danger">Close</a>
                                                                                </td>
                                                                                {{--hidden values--}}
                                                                                <input type="hidden" name="tbl_cmp_id[]" value="{{$get_complaint['id']}}">
                                                                            </tr>
                                                                            @php
                                                                                $srno++;
                                                                            @endphp
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                
            </div>
        </div>

       
    @endsection
</body>
@section('script')
   
@endsection
</html>