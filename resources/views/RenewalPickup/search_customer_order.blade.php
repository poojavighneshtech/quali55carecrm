@extends('header_and_sidebar')
@section('title')
   Renewal And Pickup
@endsection
    @section('header')
       
    @endsection

    @section('content')
    <div class="container-fluid">
        {{-- @if(session()->has('message'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                {{ session()->get('message') }}
            </div>
        @endif --}}
        <form class="form" action="{{url('/')}}/renewal_pickup_search" method="post" >
            {{ csrf_field() }}
            <div class="card">
                <div class="card-header" style="background-color: #337ab7; color: white;">
                    <center>
                        <b>Search Customer Order</b>
                    </center>
                </div>
                @if ($errors->any())
                    @foreach ($errors->all() as $error)
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            {{$error}}
                        </div>
                    @endforeach
                @endif
                <div class="card-body">
                    <div class="row"  id="pickup_renewal_search">
                        <div class="col-md-12 text-center">
                            <div class="row">
                                <div class="input-group col-md-5">
                                    <input class="form-control" placeholder="Order ID /Customer Name" name="search_input" id="search_input" value="@if(isset($search_input)){{$search_input}}@endif" type="text">
                                    <div class="input-group-btn">
                                        &emsp;
                                        <button class="btn btn-primary" type="submit" name="search_btn" id="search_btn" value="search_btn"><i class="fas fa-search"></i> Search</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <br>
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered " id="dataTable" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Sr.No</th>
                                            <th>Customer Name</th>
                                            <th>No of Products</th>
                                            <th>Total Amount</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
    
                                    <tbody>
                                        @php
                                            $sn=1;
                                        @endphp
                                        @if(isset($product_count))
                                            @foreach($product_count as $PC)
                                                <tr class="text-center">
                                                    <td class="text-center">{{$sn}}</td>
                                                    <td>{{$PC['customer_name']}}</td>
                                                    <td class="text-center">{{$PC['count']}}</td>
                                                    <td class="text-right">&#8377; {{$PC['total_amount']}}</td>
                                                    <td><a href="{{url('/')}}/customer_products/{{$PC['cust_id']}}" class="btn btn-primary">View Products</a></td>
                                                </tr>
                                                @php
                                                    $sn++;
                                                @endphp
                                            @endforeach   
                                        @endif
                                    </tbody>
                                </table>
                            </div>    
                        </div>                      
                    </div>
                </div>
            </div>
        </form>
    </div>   
    @endsection
    @section('script')
        <script>
        </script>                                                         
    @endsection