@extends('header_and_sidebar')
@section('title')
   Renewal And Pickup
@endsection
    @section('header')
       
    @endsection

    @section('content')
    <div class="container-fluid">
        
        <form class="form" action="" method="post" >
            {{ csrf_field() }}
            <div class="card">
                <div class="card-header" style="background-color: #337ab7; color: white;">
                    <center>
                        <b>Vendor Product Reports</b>
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
                    <div class="row" id="pickup_renew">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover" id="records" width="100%">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Vendor Name</th>
                                        <th>Approved Products</th>
                                        <th>Rejected Products</th>
                                        <th>Awaiting Products</th>
                                        <th>Rented Products</th>
                                        <th>Total Products</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @for($i = 0; $i < count($vdr_details); $i++)
                                        <tr data-toggle="collapse" data-target="#demo1{{$i}}" class="data-toggle" data-id="{{$i}}" >
                                            <td>{{$sr = $i+1}}</td>
                                            <td>{{$vdr_details[$i]['vendor_name']}}</td>
                                            <td>{{$vdr_details[$i]['approved']}}</td>
                                            <td>{{$vdr_details[$i]['rejected']}}</td>
                                            <td>{{$vdr_details[$i]['pending']}}</td>
                                            <td>{{$vdr_details[$i]['rented']}}</td>
                                            <td>{{$vdr_details[$i]['total']}}</td>
                                        </tr>
                                        <tr data-id="{{$i}}">
                                            <td colspan="12" class="hiddenRow">
                                                <div class="collapse" id="demo1{{$i}}">
                                                    <table class="table table-bordered table-sm " id="InTable{{$i}}" width="100%">
                                                        <thead class="thead-light" style="background-color: #476dda; color:white;">
                                                            <tr>
                                                                <th>Sr No</th>
                                                                <th>Product Name</th>
                                                                <th>Batch</th>
                                                                <th>Rented Qty</th>
                                                                <th>Free Qty</th>
                                                                <th>Total Rent</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @for($j = 0; $j < count($vdr_details[$i]['product_details']); $j++)                                                           
                                                                <tr data-toggle="collapse" data-target="#demo2{{$j}}" class="data-toggle" data-id="{{$j}}" data-in_row_id ="{{$j}}">
                                                                    <td>{{$j+1}}</td>
                                                                    <td>{{$vdr_details[$i]['product_details'][$j]['product_name']}}</td>
                                                                    <td>{{$vdr_details[$i]['product_details'][$j]['batch']}}</td>
                                                                    <td>{{$vdr_details[$i]['product_details'][$j]['rented_qty_count']}}</td>
                                                                    <td>{{$vdr_details[$i]['product_details'][$j]['product_quantity'] - $vdr_details[$i]['product_details'][$j]['rented_qty_count']}}</td>
                                                                    <td>{{$vdr_details[$i]['product_details'][$j]['rented_qty_count']*$vdr_details[$i]['product_details'][$j]['rent']}}</td>
                                                                    {{-- <td>3210</td> --}}
                                                                </tr>
                                                                <tr data-id="{{$j}}">
                                                                    <td colspan="12" class="hiddenRow">
                                                                        <div class="collapse" id="demo2{{$j}}">
                                                                            <table class="table table-bordered table-sm " id="InTable{{$j}}" width="100%">
                                                                                <thead class="thead-light" style="background-color: #476dda; color:white;">
                                                                                    <tr>
                                                                                        <th>Sr No</th>
                                                                                        <th>Product Name</th>
                                                                                        <th>Batch</th>
                                                                                        <th>Rent</th>
                                                                                        <th>From Date</th>
                                                                                        <th>To Date</th>
                                                                                        {{-- <th>Customer Name</th> --}}
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @for($k = 0; $k < count($vdr_details[$i]['product_details'][$j]['rented_products']); $k++)
                                                                                        <tr data-in_row_id ="2">
                                                                                            <td>{{$sr_no_row = $k+1}}</td>
                                                                                            <td>{{$vdr_details[$i]['product_details'][$j]['rented_products'][$k]['product_name']}}</td>
                                                                                            <td>{{$vdr_details[$i]['product_details'][$j]['rented_products'][$k]['batch']}}</td>
                                                                                            <td>{{$vdr_details[$i]['product_details'][$j]['rented_products'][$k]['product_rent_approved']}}</td>
                                                                                            <td>{{$vdr_details[$i]['product_details'][$j]['rented_products'][$k]['rented_date']}}</td>
                                                                                            <td>{{$vdr_details[$i]['product_details'][$j]['rented_products'][$k]['pickup_date']}}</td>
                                                                                            {{-- <td>Test Customer 1</td> --}}
                                                                                        </tr>
                                                                                    @endfor
                                                                                </tbody>
                                                                            </table>
                                                                            <input type="hidden" name="in_row_ct" id="in_row_ct" value="1">                                                                                                                    
                                                                        </div>
                                                                    </td>
                                                                </tr>   
                                                            @endfor                                                                                                                                    
                                                        </tbody>
                                                    </table>
                                                    <input type="hidden" name="in_row_ct" id="in_row_ct" value="1">                                                                                                                    
                                                </div>
                                            </td>
                                        </tr>
                                    @endfor                                                                             
                                </tbody>
                            </table>
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