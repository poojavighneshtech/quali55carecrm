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
                <form action="{{route('customer-master.update',$customer->cust_id)}}" method="post">
                    @method('PUT')
                    @csrf
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
                                        <div class="col-6 col-md-6 ">
                                            <input type="text" class="form-control form-control-sm" name="address_line_1" id="address_line_1" value="{{$customer->address_line_1}}">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-6 col-md-6 ">Line 2 :</div>
                                        <div class="col-6 col-md-6 ">
                                            <input type="text" class="form-control form-control-sm" name="address_line_2" id="address_line_2" value="{{$customer->address_line_2}}">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-6 col-md-6 ">Landmark :</div>
                                        <div class="col-6 col-md-6 ">
                                            <input type="text" class="form-control form-control-sm" name="landmark" id="landmark" value="{{$customer->landmark}}">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-6 col-md-6 ">Area :</div>
                                        <div class="col-6 col-md-6 ">
                                            <input type="text" class="form-control form-control-sm" name="area" id="area" value="{{$customer->area}}">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-6 col-md-6 ">City :</div>
                                        <div class="col-6 col-md-6 ">
                                            <input type="text" class="form-control form-control-sm" name="city" id="city" value="{{$customer->city}}">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-6 col-md-6 ">Pincode :</div>
                                        <div class="col-6 col-md-6 ">
                                            <input type="text" class="form-control form-control-sm" name="pincode" id="pincode" value="{{$customer->pincode}}">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-6 col-md-6 ">State :</div>
                                        <div class="col-6 col-md-6 ">
                                            <input type="text" class="form-control form-control-sm" name="state" id="state" value="{{$customer->state}}">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-6 col-md-6 ">State :</div>
                                        <div class="col-6 col-md-6 ">
                                            <input type="text" class="form-control form-control-sm" name="country" id="country" value="{{$customer->country}}">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-6 col-md-6 ">Email id :</div>
                                        <div class="col-6 col-md-6 ">
                                            <input type="text" class="form-control form-control-sm" name="email_id" id="email_id" value="{{$customer->email_id}}">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-6 col-md-6 ">Mobile No (Secondary):</div>
                                        <div class="col-6 col-md-6 ">
                                            <input type="text" class="form-control form-control-sm" name="secondary_contact_no" id="secondary_contact_no" value="{{$customer->secondary_contact_no}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-sm btn-outline-success">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
	@endsection