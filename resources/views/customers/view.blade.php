@extends('header_and_sidebar')
    @section('content')
    <div class="container">
        <div class="card">
            <div class="card-header border-primary">
                <center>
                    <b>Customer Details</b>
                </center>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-auto">
                        <strong>Customer Name :</strong>  &emsp;<span>{{$customer->customer_name}}</span>
                    </div>
                    <div class="col-auto">
                        <strong>Mobile No:</strong> &emsp;<span>{{$customer->primary_contact_no}}</span>
                    </div>
                    <div class="col-auto">
                        <strong>Location :</strong>
                        &emsp;<span>{{$customer->location}}</span>
                    </div>
                    <div class="col-auto">
                        <strong>Gender :</strong>
                        &emsp;
                        <span>
                            @if(isset($customer->cust_gender))
                                {{$customer->cust_gender}}
                            @else 
                                - 
                            @endif
                        </span>
                    </div>
                </div>
                <hr>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header text-center"> <strong>Address</strong> </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6 col-md-6 ">Line 1 :</div>
                                    <div class="col-6 col-md-6 ">{{$customer->address_line_1}}</div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-6 col-md-6 ">Line 2 :</div>
                                    <div class="col-6 col-md-6 ">{{$customer->address_line_2}}</div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-6 col-md-6 ">Landmark :</div>
                                    <div class="col-6 col-md-6 ">{{$customer->landmark}}</div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-6 col-md-6 ">Area :</div>
                                    <div class="col-6 col-md-6 ">{{$customer->area}}</div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-6 col-md-6 ">City :</div>
                                    <div class="col-6 col-md-6 ">{{$customer->city}}</div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-6 col-md-6 ">Pincode :</div>
                                    <div class="col-6 col-md-6 ">{{$customer->pincode}}</div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-6 col-md-6 ">State :</div>
                                    <div class="col-6 col-md-6 ">{{$customer->state}}</div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-6 col-md-6 ">State :</div>
                                    <div class="col-6 col-md-6 ">{{$customer->country}}</div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-6 col-md-6 ">Email id :</div>
                                    <div class="col-6 col-md-6 ">{{$customer->email_id}}</div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-6 col-md-6 ">Mobile No (Secondary):</div>
                                    <div class="col-6 col-md-6 ">{{$customer->secondary_contact_no}}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <a class="btn btn-outline-primary btn-sm" href="{{ url()->previous() }}"><i class="fas fa-arrow-left"></i> Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
	@endsection