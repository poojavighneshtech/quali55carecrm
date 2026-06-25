{{-- @extends('new-sidebar') --}}
@extends('header_and_sidebar')
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
            /* .modal-dialog{
                overflow-y: initial !important
            }
            .modal-body{
                height: 80vh;
                overflow-y: auto;
            } */
        </style>
    @endsection
</head>

<body id="page-top">	
        
    @section('content')
        
        @if(session()->has('message') || session()->has('message_pop') )
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                {{ session()->get('message')}} @if(session()->has('collection_url'))<small><a class="" href="{{ session()->get('collection_url')}}">See Order Here</a></small>@endif
                {{ session()->get('message_pop')}}
            </div>
        @endif
        @if(session()->has('reminder_msg'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                {{ session()->get('reminder_msg')}} 
            </div>
        @endif
        @if(session()->has('message_delete'))
            <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                {{ session()->get('message_delete') }}
            </div>
        @endif 
        @if(session()->has('error'))
            <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                {{ session()->get('error') }}
            </div>
        @endif 

        <div class="card">
            <div class="card-header border-primary" id="filter_card">
                <div class="row">
                    <div class="col text-primary" data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                        <strong>Renewal Pickup</strong>
                    </div>
                    <div class="col-auto">
                        <a data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                            <i class="fa fa-chevron-down pull-right"></i>&emsp;<b>Filter</b>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body border-primary collapse" id="filter-collapse" aria-labelledby="headingTwo" data-parent="#filter_card">
                <form action="{{route('renewalpickup-test')}}" method="get">
                    @csrf
                    <div class="row">
                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="row">
                                        <div class="col-md-auto">
                                            <Strong>Customer Search</Strong>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control form-control-sm" name="customer_search" id="customer_search" placeholder="Name/Contact no/address/patient name .." value="{{request()->get('customer_search')}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="row text-right">
                                        <div class="col-md-4">
                                            <Strong>Order Id</Strong>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control form-control-sm" name="order_id" id="order_id" placeholder="order id.." value="{{request()->get('order_id')}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <select class="form-control form-control-sm selectpicker border" name="lead_user" id="lead_user" data-size="5" title="Lead Owner">
                                                <option value="All" @if(request()->get('lead_user')=='All') selected @endif>All</option>
                                                @foreach($leadUsers as $key=>$user)
                                                    <option value="{{$user->id}}" @if(request()->get('lead_user')==$user->id) selected @endif>{{$user->username}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <select class="form-control form-control-sm" name="date_filter" id="date_filter" title="Filter">
                                               @foreach($dateFilter as $filter)
                                                    <option value="{{$filter}}" @if($dateFilterVal==$filter) selected @endif>{{$filter}}</option>
                                               @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                          
                            <div class="row my-2">
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Order Product Type :</strong>
                                        </div>
                                        <div class="col-md-6">
                                            <select class="form-control form-control-sm" name="order_product_type" id="order_product_type">
                                                <option value="All" @if(request()->get('order_product_type')=='All') selected @endif>All</option>
                                                <option value="Stop" @if(request()->get('order_product_type')=='Stop') selected @endif>Stop</option>
                                                <option value="Live" @if(request()->get('order_product_type')=='Live') selected @endif>Live</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Customer Type :</strong>
                                        </div>
                                        <div class="col-md-6">
                                            <select class="form-control form-control-sm" name="customer_type" id="customer_type">
                                                <option value="All" @if(request()->get('customer_type')=='All') selected @endif>All</option>
                                                <option value="Individual" @if(request()->get('customer_type')=='Individual') selected @endif>Individual</option>
                                                <option value="Corporate" @if(request()->get('customer_type')=='Corporate') selected @endif>Corporate</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>City :</strong>
                                        </div>
                                        <div class="col-md-6">
                                            <select class="form-control form-control-sm selectpicker" name="city_filter" id="city_filter"
                                                data-size="5" data-live-search="true">
                                                <option value="All" @if(request()->get('city_filter')=='All') selected @endif>All</option>
                                                {{-- @foreach ($cities as $city)
                                                    <option value="{{$city->citygroup}}" @if(request()->get('city_filter')==$city->citygroup) selected @endif>{{$city->citygroup}}</option>
                                                @endforeach --}}
                                                @foreach(config('app.citylist') as $key=>$value)
                                                    {{-- <option value="{{$city->city}}" @if(request()->get('city_filter')==$city->city) selected @endif>{{$city->city}}</option> --}}
                                                    <option value="{{$value}}" @if(request()->get('city_filter')==$value) selected @endif>{{$value}}</option>
                                                    {{-- <option value="Pune" @if(isset($filter_data['city'])) @if($filter_data['city'] == "Pune")selected @endif @endif>Pune</option> --}}
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="input-group mb-1">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <input type="checkbox" name="shows_only_stops" data-toggle="tooltip" data-placement="bottom" title="Show only stop orders" 
                                                @if(request()->get('shows_only_stops')=='on') checked @endif>
                                            </div>
                                        </div>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary btn-sm btn-block" data-toggle="modal" data-target=".viewStopProductModal">
                                                <span class="badge badge-primary">{{$stoppedProductsCount}}</span> Stop Orders
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <Strong>Product</Strong>
                                        </div>
                                        <div class="col-md-9">
                                            <select class="select selectpicker" name="filter_product[]" id="filter_product" multiple="multiple" data-live-search="true" title="Select Product">
                                                @foreach($products as $key=>$product)
                                                    <option value="{{$product->id}}" @if(request()->get('filter_product') !=null) @if(in_array($product->id,request()->get('filter_product'))){{"selected"}}@endif @endif>{{$product->product_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <Strong>From :</Strong>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="date" class="form-control form-control-sm" name="start_date" id="start_date"
                                                value="{{request()->get('start_date')}}">
                                        </div>
                                        <div class="col-md-2">
                                            <strong>To :</strong>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="date" class="form-control form-control-sm" name="end_date" id="end_date"
                                                value="{{request()->get('end_date')}}">
                                        </div>
                                    </div>    
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="row">
                                <a type="button" class="form-control form-control-sm btn btn-outline-secondary btn-sm" href="{{route('renewalpickup-test',['date_filter'=>'Today'])}}"  id="btn_clear">Clear</a>
                            </div>
                            <div class="row my-2">
                                <button type="submit" class="form-control form-control-sm btn btn-outline-success btn-sm" >Submit</button>
                            </div>
                            <div class="row">
                                <button type="submit" name="submit" class="form-control form-control-sm btn btn-outline-info btn-sm" value="export_excel">Export Excel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <ul class="list-group list-sm-group list-group-flush">
                <li class="list-group-item list-group-item-sm">
                    <div class="row justify-content-between">
                        <div class="col-sm-auto">
                            <div class="row">
                                <div class="col-sm-auto">
                                    Total Customers : <strong>{{$totalCustomers}}</strong>
                                </div>
                                <div class="col-sm-auto">
                                    Total Products : <strong>{{$totalProducts}}</strong>
                                </div>
                                <div class="col-sm-auto">
                                    Total Rent : <strong>{{$totalRentHeador}}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <div class="table table-responsive jim-table-responsive">
                <table class="table table-hover">
                    <thead>
                        <th>Due Date</th>
                        <th>Customer Name</th>
                        <th>Patient Name</th>
                        <th>Contact No</th>
                        <th>Address</th>
                        <th>Products</th>
                        <th>Lead Owner</th>
                        <th>Total Due</th>
                    </thead>
                    <tbody>
                        @forelse($renewPickupData as $key=>$data)
                        
                            <tr class="row-click
                                @if($data[0]->current_status=='CustStop') table-danger text-black @endif 
                                @if($data[0]->customer_type=='Corporate') bg-info text-dark @endif" data-order_details_id ={{$data->pluck('order_details_id')->toJson()}} 
                                data-customer_name="{{$data[0]->customer_name}}">
                                <td data-label="Due Date" class="text-nowrap">{{date('d-M-y',strtotime($data[0]->pickup_date))}}</td>
                                <td data-label="Customer Name">{{$data[0]->customer_name}}</td>
                                <td>{{($data[0]->patient_name?$data[0]->patient_name:'-')}}</td>
                                <td data-label="Contact No">{{$data[0]->primary_contact_no}}</td>
                                <td data-label="Address">{{$data[0]->address_line_1}}, {{$data[0]->address_line_2}}, {{$data[0]->location}},{{$data[0]->area}}, {{$data[0]->city}}, {{$data[0]->pincode}}</td>
                                <td data-label="Products">{{count($data)}}</td>
                                <td data-label="Lead Owner">{{$data[0]->username}}</td>
                                <td data-label="Total Due">{{array_sum(array_column($totalRent[$key],'total_rent'))}}</td>
                            </tr>
                        @empty
                        <tr>
                           <td class="text-center" colspan="*">No record</td> 
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                {{$renewPickupData->withPath(url()->full())->links('Custom.Pagination.pagination')}}
            </div>
        </div>

        {{-- Products Modal --}}
        <div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="productModal" aria-hidden="true">
            <div class="modal-dialog modal-xl">
              <div class="modal-content">
                  <form action="{{route('order-request')}}" method="post">
                      @csrf
                      <div class="modal-header">
                          <h5 class="modal-title"><span id="product-modal-title"></span>Products</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                      </div>
                      {{-- <div class="modal-body"> --}}
                            <div class="table table-responsive jim-table-responsive">
                                <table class="table table-hover">
                                    <thead class="thead-light">
                                        <th>Action</th>
                                        <th>Start Date</th>
                                        <th>Due Date</th>
                                        <th>Order Id</th>
                                        <th>Inventory Id</th>
                                        <th>Product Name</th>
                                        <th>Vendor Name</th>
                                        <th>Quantity</th>
                                        <th>Rent</th>
                                        <th>Deposit</th>
                                        <th>Due Month</th>
                                        <th>Total Due Rent</th>
                                    </thead>
                                    <tbody id="productsTable"></tbody>
                                </table>
                            </div>
                            <div class="alert alert-danger" id="product-alert" role="alert" style="display:none ">
                                <span id="product-alert-span"></span>
                                <button type="button" class="close" id="product-alert-close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                            </div>
                      {{-- </div> --}}
                      <div class="modal-footer">
                            <button type="submit" class="btn btn-outline-warning submit-product" name="submit" value="renew-and-pickup">Renew & Pickup</button>
                            <button type="submit" class="btn btn-outline-success submit-product" name="submit" value="renew">Renew</button>
                            <button type="submit" class="btn btn-outline-primary submit-product" name="submit" value="pickup">Pickup</button>
                            <button type="button" class="btn btn-outline-danger submit-product stop-reason" name="submit" value="stop_product">Stop Request</button>
                            <button type="button" class="btn btn-secondary " data-dismiss="modal">Close</button>
                      </div>
                  </form>
              </div>
            </div>
        </div>
        {{-- stop reason modal --}}
         <div class="modal fade" id="viewStopReasonModal" tabindex="-1" role="dialog" aria-labelledby="viewStopReasonModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered mdoal-sm" role="document">
                <div class="modal-content border-danger">
                    <form action="{{route('order-request')}}" method="post" id="order_request_form">
                        @csrf
                        <div class="modal-header modal-header-sm">
                            <h5 class="modal-title" id="viewStopReasonModalTitle">Stop Reason</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="checked_product[]" id="stop_order_id">
                            <textarea class="form-control form-control-sm" name="stop_reason" id="stop_reason" cols="30" rows="5" placeholder="Stop requested reason..." required></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary btn-sm" id="" name="submit" value="stop_product">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- view Stopped prodcuts modal --}}
        <div class="modal fade viewStopProductModal" tabindex="-1" role="dialog" id="viewStopProducts" aria-labelledby="viewStopProducts" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="table table-responsive jim-table-responsive">
                            <table class="table" id="stopProductsTable">
                                <thead>
                                    <th>Sr No</th>
                                    <th>Product Name</th>
                                    <th>Product Count</th>
                                    <th>Action</th>
                                </thead>
                                <tbody>
                                    @php                                            
                                        $srno = 1;
                                    @endphp
                                    @foreach($stoppedProducts as $key =>$productData)
                                        <tr>
                                            <td data-label="Sr No">{{$srno++}}</td>
                                            <td data-label="Product Name">{{$productData[0]->product_name}}</td>
                                            <td data-label="Count">{{count($productData)}}</td>
                                            <td data-label="Action">
                                                {{-- <a class="btn btn-outline-primary btn-sm" href="{{request()->fullUrlWithQuery(['product_id'=>$key,'btn_search'=>'stop_product_search'])}}">Search</a> --}}
                                                <a class="btn btn-outline-primary btn-sm" href="{{route('renewalpickup-test',['stopped_product_id'=>$key])}}">Search</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" tabindex="-1" role="dialog" id="corporateRenewals" aria-labelledby="corporateRenewals" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header modal-header-sm">
                        <h5 class="modal-title" id="corporateRenewalsModalTitle">Invoices</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('corporate-invoice-nos')}}" method="POST">
                            @csrf
                            <div class="table table-responsive jim-table-responsive">
                                <table class="table" id="stopProductsTable">
                                    <thead>
                                        <th>SrNo</th>
                                        <th>Rent Period</th>
                                        <th>Invoice No</th>
                                    </thead>
                                    <tbody id="tablebody">
                                        
                                    </tbody>
                                </table>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                        Submit
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endsection
</body>
@section('script')
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
    $('.row-click').on('click',function(){
        let order_details_id = JSON.stringify($(this).data('order_details_id'));
        $('#product-modal-title').text($(this).data('customer_name')+" - ");
        //get order_data
        var dataString = ({_token:"{{ csrf_token() }}",order_details_id:""+order_details_id});
        $.ajax({
            type: "GET",
            url: "{{url('/')}}/order-individual_data",
            data: dataString,
            cache:false,
            success: function (data)
            {
                //console.log(data);
                $('#productsTable').empty();
                let row="";
                Object.keys(data.orderProducts).forEach(function(key){
                    let disabeldStatus = '';
                    if(data.orderProducts[key].current_status=='Pending Renew'){
                        disabeldStatus = 'disabled=="true"';
                    };
                    row += "<tr id='trParent'>";
                        row+="<td data-label='Action'>";
                                row+="<input type='checkbox' class='checkbox-product' name='checked_product[]' value='"+data.orderProducts[key].order_details_id+"' "+disabeldStatus+">";
                        row+="</td>";
                        row+="<td class='text-nowrap' data-label='Start Date'>"+data.orderProducts[key].DelDate+"</td>";
                        row+="<td class='text-nowrap' data-label='Due Date'>"+data.orderProducts[key].pickup_date+"</td>";
                        row+="<td data-label='Order Id'>"+data.orderProducts[key].order_id+"</td>";
                        row+="<td data-label='Inventory Id'>"+data.orderProducts[key].unique_id+"</td>";
                        row+="<td data-label='Product Name'>"+data.orderProducts[key].product_name+"</td>";
                        row+="<td data-label='Vendor Name'>"+data.orderProducts[key].vendor_name+"</td>";
                        row+="<td data-label='Quantity'>"+data.orderProducts[key].product_qty+"</td>";
                        row+="<td data-label='Rent'>"+data.orderProducts[key].product_rent+"</td>";
                        row+="<td data-label='Deposit'>"+data.orderProducts[key].product_deposite+"</td>";
                        if(data.orderProducts[key].customer_type == 'Corporate'){
                            row+="<td data-label='Month Count'><a type='button' onclick='fetchdata("+data.orderProducts[key].order_details_id+");'>"+data.productMonthData[key].month_count+"</a></td>";
                        }else{
                            row+="<td data-label='Month Count'>"+data.productMonthData[key].month_count+"</td>";
                        }
                        row+="<td data-label='Total Due'>"+data.productMonthData[key].total_rent+"</td>";
                    row+="</tr>";
                });

                $('#productsTable').append(row);
                $('#productModal').modal('show');
            }
        });
    });
    function fetchdata(id){
        var dataString = ({_token:"{{ csrf_token() }}",id:""+id});
        $.ajax({
            type: "POST",
            url: "{{route('get-overdue-period')}}",
            data: dataString,
            cache:false,
            success: function (data)
            {
                // let resp = jQuery.parseJSON(data);
                resp = data;
                console.log(resp);
                $("#tablebody").empty();
                let tr = null;
                let i = 1;
                Object.keys(resp).forEach(function(key){
                    tr += "<tr>";
                        tr += "<td>"+i+"</td>";
                        tr += "<td class='text-nowrap'>"+resp[key].period+"<input type='hidden' class='form-control form-control-sm' name='alldetails[]' value='"+JSON.stringify(resp[key])+"'></td>";
                        tr += "<td><input type='text' class='form-control form-control-sm' name='update_invoice_ids[]' value='"+resp[key].invoice_no+"'></td>";
                    tr += "</tr>";
                    i++;
                });
                $("#tablebody").append(tr)
                $("#corporateRenewals").modal("show");
                console.log(data);
            },error: function(err){

            }
        });
    }
    $('.stop-reason').on('click',function(){
        let checkedProductLen = $('input[name="checked_product[]"]:checked').length;
        if(checkedProductLen>0){
            var orderIds = [];
            $('input[name="checked_product[]"]:checked').each(function(i){
                orderIds[i] = $(this).val();
            });
            $('#stop_order_id').val(orderIds);
            $('#viewStopReasonModal').modal('show');
            $('#product-alert').css('display','none');
        }else{
            $('#product-alert-span').text('Please select product');
            $('#product-alert').css('display','block');
            //$('#product-alert').show();
        }
    });
    
    $('#product-alert-close').on('click',function(){
        $('#product-alert').css('display','none');
    });
    
    // $(document).ready(function(){
    //     $('#order_request_form').submit(function() {
    //         let checkedProductLen = $('input[name="checked_product[]"]:checked').length;
    //         if(checkedProductLen>0){
    //             e.preventDefault();
    //         }else{
    //             $('#product-alert-span').text('Please select product');
    //             $('#product-alert').css('display','block');
    //         }
    //         //$('#product-alert').show();
    //         //event.preventDefault();
    //     });
    // });
    
</script>     

@endsection
</html>