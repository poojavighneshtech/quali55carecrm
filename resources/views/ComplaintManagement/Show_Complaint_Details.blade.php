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
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <div class="row">
                                                                        <div class="col-sm-6 text-center">
                                                                            <p><strong>Complaint Date :</strong></p>
                                                                        </div>
                                                                        <div class="col-md-6 text-left">
                                                                            <p id="p_customer_name">{{date('d-m-Y',strtotime($get_complaint_detail[0]['complaint_date']))}}</p>
                                                                            <input type="hidden" name="customer_id" id="customer_id" value="{{$get_complaint_detail[0]['customer_id']}}">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="row">
                                                                        <div class="col-sm-7 text-center">
                                                                            <p><strong>Complaint ID :</strong></p>
                                                                        </div>
                                                                        <div class="col-sm-5 text-left">
                                                                            <p id="p_customer_location">{{$get_complaint_detail[0]['generated_complaint_id']}}</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="row">
                                                                        <div class="col-sm-7 text-center">
                                                                            <p><strong>Complaint By :</strong></p>
                                                                        </div>
                                                                        <div class="col-sm-5 text-left">
                                                                            <p id="p_customer_location">{{$get_complaint_detail[0]['created_by']}}</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="row">
                                                                        <div class="col-sm-7 text-center">
                                                                            <p><strong>Status :</strong></p>
                                                                        </div>
                                                                        <div class="col-sm-5 text-left">
                                                                            <span class="badge @if($get_complaint_detail[0]['status']=='Open'){{"badge-danger"}}@else{{"badge-success"}}@endif">
                                                                                {{$get_complaint_detail[0]['status']}}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="card" >
                                                                <div class="card-body" >
                                                                    <table class="table table-hover" width="100%">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Sr No</th>
                                                                                @if($get_complaint_detail[0]['status']=='Closed')
                                                                                    <th>Closed Date</th>
                                                                                @endif
                                                                                <th>Product Name</th>
                                                                                <th>Vendor Name</th>
                                                                                <th>Delivered By</th>
                                                                                @if($get_complaint_detail[0]['status']=='Closed' && isset($get_complaint_detail[0]['repaired_by_name']))
                                                                                    <th>Repaired By</th>
                                                                                @endif
                                                                                <th>Lead Owner</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @php
                                                                                $srno = 1;
                                                                            @endphp
                                                                            @foreach($get_complaint_detail as $key => $get_detail)
                                                                                <tr>
                                                                                    <td>{{$srno}}</td>
                                                                                    @if($get_complaint_detail[0]['status']=='Closed')
                                                                                        <td>{{date('d-m-Y',strtotime($get_detail['closed_date']))}}</td>
                                                                                    @endif
                                                                                    
                                                                                    <td>{{$get_detail['product_name']}}</td>
                                                                                    <td>{{$get_detail['vendor_name']}}</td>
                                                                                    <td>{{$get_detail['delivered_by']}}</td>
                                                                                    @if($get_complaint_detail[0]['status']=='Closed' && isset($get_complaint_detail[0]['repaired_by_name']))
                                                                                        <td>{{$get_complaint_detail[0]['repaired_by_name']}}</td>
                                                                                    @endif
                                                                                    <td>{{$get_detail['lead_owner']}}</td>
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
                                                    </div>
                                                    <br>
                                                    @if(isset($image))
                                                        @php
                                                            $count = count($image);
                                                        @endphp
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <label for="image"><strong>Images</strong></label>
                                                            </div>
                                                        </div>
                                                    @else
                                                        @php
                                                            $count = 0;
                                                        @endphp
                                                    @endif
                                                    {{--hidden count--}}
                                                    <input type="hidden" name="hid_img_count" id="hid_img_count" value="{{$count}}">
                                                    <div class="row">
                                                        @if(isset($image))
                                                            <div class="@if(isset($image) && $count==1) col-md-4 @elseif(isset($image) && $count==2) col-md-6 @endif">
                                                                <div class="row">
                                                                    <div class="form-group">
                                                                        @foreach($image as $key => $img)
                                                                            <img class="images img-thumbnail rounded" src="{{$img}}" alt="Photo" />
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="@if(isset($image) && $count==1) col-md-8 @elseif(isset($image) && $count==2) col-md-6 @endif" id="add_div" style="@if(isset($image) && $count<3) display:block @else display:none @endif">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="remarks"><strong>Remarks</strong></label>
                                                                            <textarea class="form-control" name="remarks" id="remarks" rows="6" disabled>{{$get_complaint_detail[0]['remarks']}}</textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        @if($get_complaint_detail[0]['status']=='Open')
                                                                            <div class="row">
                                                                                <div class="col-md-6">
                                                                                    <label for="solution_date"><strong>Solution Date:</strong></label>
                                                                                    <input type="date" class="form-control" name="solution_date" id="txt_solution_date" value="{{date('Y-m-d')}}">
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <label for="repaired_by"><strong>Repaired By:</strong></label>
                                                                                    <select class="form-control select selectpicker" name="repaired_by[]" id="select_repaired_by" data-live-search="true" data-size="5" multiple @if(isset($image) && $count<3) {{'required'}} @else {{''}} @endif>
                                                                                        @foreach($delusers as $del)
                                                                                            <option value="{{$del['id']}}">{{$del['username']}}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <br>
                                                                        @endif
                                                                        <div class="row">
                                                                            <div class="col">
                                                                                <label for="solution"><strong>Solution :</strong></label>
                                                                                    <textarea class="form-control" name="solution" id="solution" @if($get_complaint_detail[0]['status']=='Closed') disabled rows="6" @endif>{{$get_complaint_detail[0]['solution']}}</textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    @if($count>2 || $count==0)
                                                        <div class="row" >
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="remarks"><strong>Remarks</strong></label>
                                                                    <textarea class="form-control" name="remarks" id="remarks" rows="6" disabled>{{$get_complaint_detail[0]['remarks']}}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                @if($get_complaint_detail[0]['status']=='Open')
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <label for="solution_date"><strong>Solution Date:</strong></label>
                                                                            <input type="date" class="form-control" name="solution_date" id="txt_solution_date" value="{{date('Y-m-d')}}">
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <label for="repaired_by"><strong>Repaired By:</strong></label>
                                                                            <select class="form-control select selectpicker" name="repaired_by[]" id="select_repaired_by" data-live-search="true" data-size="5" multiple required>
                                                                                @foreach($delusers as $del)
                                                                                    <option value="{{$del['id']}}">{{$del['username']}}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <br>
                                                                @endif
                                                                <div class="row">
                                                                    <div class="col">
                                                                        <label for="solution"><strong>Solution :</strong></label>
                                                                            <textarea class="form-control" name="solution" id="solution" @if($get_complaint_detail[0]['status']=='Closed') disabled rows="6" @endif>{{$get_complaint_detail[0]['solution']}}</textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div> 
                                                    @endif
                                                    
                                                    <div class="row" style="@if($get_complaint_detail[0]['status']=='Closed'){{"display:none"}}@endif">
                                                        <br>
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