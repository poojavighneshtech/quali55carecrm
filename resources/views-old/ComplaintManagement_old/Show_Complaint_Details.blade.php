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
                .images {
                    float:left;
                    /* border-style:solid; */
                    /* border-width:3px; */
                    padding:20px;
                    margin:5px;
                    height:220px;
                    width:220px;
                    overflow:hidden;
                    /* text-align:center; */
                }

                .clearfix {
                    clear: both;
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
                            <div class="row" id="complaint_details_div">
                                <div class="col-md-12">
                                    <div id="accordion" class="accordion_div">
                                        <div class="card">
                                            <div class="card-header" id="headingOne" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                Complaint Details
                                            </div>
                                            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <p><strong>Complaint Date :</strong></p>
                                                                </div>
                                                                <div class="col-md-6 text-left">
                                                                    <p id="p_customer_name">{{$get_complaint_detail[0]['complaint_date']}}</p>
                                                                    <input type="hidden" name="customer_id" id="customer_id" value="{{$get_complaint_detail[0]['customer_id']}}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <p><strong>Complaint ID :</strong></p>
                                                                </div>
                                                                <div class="col-sm-6 text-left">
                                                                    <p id="p_customer_location">{{$get_complaint_detail[0]['generated_complaint_id']}}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <p><strong>Complaint By :</strong></p>
                                                                </div>
                                                                <div class="col-sm-6 text-left">
                                                                    <p id="p_customer_location">{{$get_complaint_detail[0]['created_by']}}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <div class="row">
                                                        <div class="card" >
                                                            <div class="card-body" >
                                                                <table id="records" class="table table-bordered table-striped" width="100%">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Sr No</th>
                                                                            <th>Complaint ID</th>
                                                                            <th>Complaint Date</th>
                                                                            <th>Product Name</th>
                                                                            <th>Vendor Name</th>
                                                                            <th>Delivered By</th>
                                                                            <th>Lead Owner</th>
                                                                            <th>Status</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @php
                                                                            $srno = 1;
                                                                        @endphp
                                                                        @foreach($get_complaint_detail as $key => $get_detail)
                                                                            <tr>
                                                                                <td>{{$srno}}</td>
                                                                                <td>{{$get_detail['generated_complaint_id']}}</td>
                                                                                <td>{{$get_detail['complaint_date']}}</td>
                                                                                <td>{{$get_detail['product_name']}}</td>
                                                                                <td>{{$get_detail['vendor_name']}}</td>
                                                                                <td>{{$get_detail['delivered_by']}}</td>
                                                                                <td>{{$get_detail['lead_owner']}}</td>
                                                                                <td>{{$get_detail['status']}}</td>
                                                                                 {{--hidden values--}}
                                                                                 <input type="hidden" name="tbl_cmp_id[]" value="{{$get_detail['id']}}">
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
                                                    <br> 
                                                    <div class="row">
                                                        <div class="container"></div>
                                                        <label for="images"><strong>Images</strong></label>
                                                        @foreach($image as $key => $image)
                                                            <img class="images img-thumbnail rounded" src="{{$image}}" alt="Photo" />
                                                        @endforeach
                                                    </div>
                                                    <br>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="remarks"><strong>Remarks</strong></label>
                                                                <textarea class="form-control" name="remarks" id="remarks" disabled>{{$get_complaint_detail[0]['remarks']}}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="solution"><strong>Solution</strong></label>
                                                            @if($get_complaint_detail[0]['status']=='Closed')
                                                                <textarea class="form-control" name="solution" id="solution" disabled>{{$get_complaint_detail[0]['solution']}}</textarea>
                                                            @else
                                                                <textarea class="form-control" name="solution" id="solution"required></textarea>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="row" style="@if($get_complaint_detail[0]['status']=='Closed'){{"display:none"}}@endif">
                                                        <div class="container">
                                                            <input type="submit" class="form-control btn btn-success" value="Submit">
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