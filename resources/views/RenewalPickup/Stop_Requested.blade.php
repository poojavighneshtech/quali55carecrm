@extends('header_and_sidebar')
<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        
        <title>Stop Requestes</title>
        {{-- <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script> --}}
        <link rel="stylesheet" href="{{url('/')}}/assets/dist/toast.min.css ">
        <!-- Boostrap 4 CSS -->
   
        @section('styles')
        <style>
         
        </style>
        @endsection
    </head>

<body id="page-top">	
    @section('content')
        @if(session()->has('message_delete'))
            <div class="alert alert-danger">
                {{ session()->get('message_delete') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        {{-- @if(session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif --}}
        @if(session()->has('message') || session()->has('message_pop') )
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                {{ session()->get('message')}} @if(session()->has('collection_url'))<small><a class="" href="{{ session()->get('collection_url')}}">See Order Here</a></small>@endif
                {{ session()->get('message_pop')}}
            </div>
        @endif
        <div class="card">
            <div class="card-header">
                <strong>Stop Requested Products</strong>
            </div>
            <div class="card-body">
                <form action="{{url('/')}}/stop_requested" method="get">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Customer :</strong>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control form-control-sm" name="text_search" id="text_search" placeholder="Customer Name / Address / pateint name..."
                                        value="@if($filterArr['textSearch']!=null){{$filterArr['textSearch']}}@endif">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-2">
                                    <strong>City : </strong>
                                </div>
                                <div class="col-md-9">
                                    <select class="form-control form-control-sm selectpicker border" name="filter_city" id="filter_city"
                                    data-size="5" data-live-search="true">
                                        <option value="All" @if(request()->get('filter_city')=='All') selected @endif>All</option>
                                        {{-- @foreach ($cities as $key=>$city)
                                            <option value="{{$city->citygroup}}" @if(request()->get('filter_city')==$city->citygroup) selected @endif>{{$city->citygroup}}</option>
                                        @endforeach --}}
                                        @foreach(config('app.citylist') as $key=>$value)
                                            {{-- <option value="{{$city->city}}" @if(request()->get('city_filter')==$city->city) selected @endif>{{$city->city}}</option> --}}
                                            <option value="{{$value}}" @if(request()->get('filter_city')==$value) selected @endif>{{$value}}</option>
                                            {{-- <option value="Pune" @if(isset($filter_data['city'])) @if($filter_data['city'] == "Pune")selected @endif @endif>Pune</option> --}}
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="cust_name_label"><strong>From:</strong></span>
                                        </div>
                                        <input type="date" class="form-control form-control-sm" name="start_date" id="start_date" value="{{$filterArr['startDate']}}" title="Start Date">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="cust_name_label"><strong>To:</strong></span>
                                        </div>
                                        <input type="date" class="form-control form-control-sm" name="end_date" id="end_date" value="{{$filterArr['endDate']}}" title="End Date">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <button type="button" id="stopProductsDisplay" class="btn btn-primary btn-sm" value="Y">
                                Total Product <span class="badge badge-light">{{$productCount}}</span>
                                <span class="sr-only">unread messages</span>
                            </button>
                        </div>
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Product :</strong>
                                </div>
                                <div class="col-md-9">
                                    <select class="form-control selectpicker form-control-sm" name="selected_product" id="selected_product" data-live-search="true" data-size="5">
                                        <option value="All" @if($filterArr['selectedProduct']=="All" && $filterArr['selectedProduct']==null) selected @endif>All</option>
                                        @foreach ($allProducts as $key=>$product)
                                            <option value="{{$product->id}}" @if($filterArr['selectedProduct']==$product->id) selected @endif>{{$product->product_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center mt-2">
                        <button type="submit" name="submit" class="btn btn-outline-success btn-sm" value="submit">Submit</button>
                        &emsp;
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btn_clear">Clear</button>
                    </div>
                    {{-- <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" class="form-control form-control-sm" name="text_search" id="text_search" placeholder="Customer Name / Address / pateint name..."
                                        value="@if($filterArr['textSearch']!=null){{$filterArr['textSearch']}}@endif">
                                </div>
                                <div class="col-md-4">
                                    <input type="date" class="form-control form-control-sm" name="start_date" id="start_date" value="{{$filterArr['startDate']}}" title="Start Date">
                                </div>
                                <div class="col-auto text-center">
                                    <strong>To</strong>
                                </div>
                                <div class="col-md-3">
                                    <input type="date" class="form-control form-control-sm" name="end_date" id="end_date" value="{{$filterArr['endDate']}}" title="End Date">
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="row">
                                        <div class="col-auto">
                                            <button type="button" id="stopProductsDisplay" class="btn btn-primary btn-sm" value="Y">
                                                Total Product <span class="badge badge-light">{{$productCount}}</span>
                                                <span class="sr-only">unread messages</span>
                                            </button>
                                        </div>
                                        <div class="col-md-7">
                                            <select class="form-control selectpicker form-control-sm" name="selected_product" id="selected_product" data-live-search="true" data-size="5">
                                                <option value="All" @if($filterArr['selectedProduct']=="All" && $filterArr['selectedProduct']==null) selected @endif>All</option>
                                                @foreach ($allProducts as $key=>$product)
                                                    <option value="{{$product->id}}" @if($filterArr['selectedProduct']==$product->id) selected @endif>{{$product->product_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-7 d-flex flex-row">
                                    <button type="submit" name="submit" class="btn btn-outline-success btn-sm" value="submit">Submit</button>
                                        &emsp;<button type="button" class="btn btn-outline-secondary btn-sm" id="btn_clear">Clear</button>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                </form>
            </div>
            <div class="table table-responsive jim-table-responsive">
                <table class="table table-hover">
                    <thead>
                        <th>Stop Date</th>
                        <th>Pickup Date</th>
                        <th>Customer Name</th>
                        <th>Patient Name</th>
                        <th>Contact  No</th>
                        <th>Address</th>
                        <th>Products</th>
                        <th>Lead Owner</th>
                        {{-- <th>Total Due rent</th> --}}
                    </thead>
                    <tbody>
                        @foreach ($get_data as $key=>$requestedData)
                            <tr scope="row" data-toggle="collapse" data-target="#collapseTable{{$key}}" class="data-toggle" data-id="{{$key}}" >
                                <td class="text-nowrap" data-label="Stop Date">{{date('d-M-y',strtotime($requestedData[0]->stop_requested_date))}}</td>    
                                <td class="text-nowrap" data-label="Pickup Date">{{date('d-M-y',strtotime($requestedData[0]->pickup_date))}}</td>    
                                <td data-label="Customer Name">
                                    {{$requestedData[0]->customer_name}}
                                    <input type="hidden" name="customer_id" id="customer_id{{$key}}" value="{{$requestedData[0]->cust_id}}">
                                    <input type="hidden" name="order_count" id="order_count{{$key}}" value="{{count($requestedData)}}">
                                    <input type="hidden" name="order_arr" id="order_arr{{$key}}" value="{{$requestedData}}">
                                </td>
                                <td>{{$requestedData[0]->patient_name}}</td>
                                <td data-label="Contact No">{{$requestedData[0]->primary_contact_no}}</td>
                                <td data-label="Address">{{$requestedData[0]->address_line_1}},{{$requestedData[0]->address_line_2}},{{$requestedData[0]->area}},{{$requestedData[0]->location}},{{$requestedData[0]->pincode}}</td>
                                <td data-label="Products">{{count($requestedData)}}</td>
                                <td data-label="Lead Owner">{{$requestedData[0]->username}}</td>
                                {{-- <td>{{$requestedData->sum('product_rent')}}</td> --}}
                                <tr class="collapse" id="collapseTable{{$key}}">
                                    <td colspan="12">
                                        <form action="{{url('/')}}/stop_product_pickup" method="post">
                                            @csrf
                                            <div class="table table-responsive jim-table-responsive" >
                                                <table class="table" style="width:auto;height:auto;overflow-x:auto ">
                                                    <thead class="thead thead-light">
                                                        <th>Sr No</th>
                                                        <th>Start date</th>
                                                        <th>Due date</th>
                                                        <th>Order Id</th>
                                                        <th>Inventory Id</th>
                                                        <th>Product Name</th>
                                                        <th>Vendor Name</th>
                                                        <th>Quantity</th>
                                                        <th>Rent</th>
                                                        <th>Deposit</th>
                                                        <th>Due Period</th>
                                                        <th>Total Due Rent</th>
                                                        <th>reason</th>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $i = 1;   
                                                            $total_sum = [];                                                     
                                                        @endphp
                                                        @foreach ($requestedData as $pKey=>$product)
                                                            <tr>
                                                                <td class="text-nowrap" data-label="Sr no">
                                                                    &emsp;<input type="checkbox" name="checkedProduct[{{$pKey}}]" class="form-check-input" id="checkedProduct{{$key.$pKey}}"
                                                                        value="{{$product->order_details_id}}">
                                                                    {{$i++}}
                                                                </td>
                                                                <td class="text-nowrap" data-label="Start Date">{{date('d-M-y',strtotime($product->DelDate))}}</td>
                                                                <td class="text-nowrap" data-label="Due Date">{{date('d-M-y',strtotime($product->pickup_date))}}</td>
                                                                <td data-label="Order id">
                                                                    {{$product->order_id}}
                                                                    <input type="hidden" name="order_id[]" id="order_id{{$key.$pKey}}" value="{{$product->order_id}}">
                                                                    <input type="hidden" name="order_details_id[]" id="order_details_id{{$key.$pKey}}" value="{{$product->order_details_id}}">
                                                                    <input type="hidden" name="product_id[]" id="product_id{{$key.$pKey}}" value="{{$product->product_id}}">
                                                                    <input type="hidden" name="product_name[]" id="product_name{{$key.$pKey}}" value="{{$product->product_name}}">
                                                                    <input type="hidden" name="pickup_date[]" id="pickup_date{{$key.$pKey}}" value="{{$product->pickup_date}}">
                                                                    <input type="hidden" name="product_rent[]" id="product_rent{{$key.$pKey}}" value="{{$product->product_rent}}">
                                                                    <input type="hidden" name="product_deposite[]" id="product_rent{{$key.$pKey}}" value="{{$product->product_deposite}}">
                                                                </td>
                                                                <td data-label="Inventory Id">{{$product->unique_id}}</td>
                                                                <td data-label="Product Name">{{$product->product_name}}</td>
                                                                <td data-label="Vendor Name">{{$product->vendor_name}}</td>
                                                                <td data-label="Quantity">{{$product->product_qty}}</td>
                                                                <td data-label="Rent">{{$product->product_rent}}</td>
                                                                <td data-label="Deposit">{{$product->product_deposite}}</td>
                                                                <td data-label="Due Months">
                                                                    @php
                                                                        $today = \Carbon\Carbon::now()->toDateString();
                                                                        // $pickupDate = \Carbon\Carbon::parse($product->pickup_date)->toDateString();
                                                                        // $diff = \Carbon\Carbon::parse($today)->diffInMonths($pickupDate);
                                                                        // if($diff>0){
                                                                        //     $dueMonth = $diff;
                                                                        // }elseif($diff==0){
                                                                        //     $dueMonth = 1;
                                                                        // }
                                                                        $dueMonth = 0;
                                                                        if($product->billing_unit == 'Days'){
                                                                            if(date('Y-m-d') > $product->pickup_date){
                                                                                $dueMonth = \Carbon\Carbon::parse($product->pickup_date)->diffInDays($today);
                                                                            }else{
                                                                                $dueMonth = 1;
                                                                            }
                                                                        }
                                                                        elseif($product->billing_unit == 'Week'){
                                                                            if(date('Y-m-d') > $product->pickup_date){
                                                                                $days = \Carbon\Carbon::parse($product->pickup_date)->diffInDays($today);
                                                                                $dueMonth = (int)($days / 7);
                                                                                if(($days - (7*$dueMonth)) >0){
                                                                                    $dueMonth++;
                                                                                }
                                                                            }else{
                                                                                $dueMonth = 1;
                                                                            }
                                                                        }else if($product->billing_unit == "Half Month"){
                                                                            if(date('Y-m-d') > $product->pickup_date){
                                                                                $days = \Carbon\Carbon::parse($product->pickup_date)->diffInDays($today);
                                                                                $dueMonth = (int)($days / 14);
                                                                                if(($days - (14*$dueMonth)) >0){
                                                                                    $dueMonth++;
                                                                                }
                                                                            }else{
                                                                                $dueMonth = 1;
                                                                            }
                                                                        }else{
                                                                            $dueMonth = \Carbon\Carbon::parse($product->pickup_date)->diffInMonths($today);
                                                                            // return $dueMonth;
                                                                            $currentRenewDate = \Carbon\Carbon::parse($product->pickup_date)->addMonths($dueMonth);
                                                                            if(\Carbon\Carbon::parse($currentRenewDate)->diffInDays($today)>0){
                                                                                $dueMonth++;
                                                                            }
                                                                            // if(Carbon::parse($product->pickup_date)->diffInDays($today)==0)
                                                                            else
                                                                            {
                                                                                $dueMonth = 1;
                                                                            }
                                                                        }

                                                                        $tRent = $product->product_rent*$dueMonth;
                                                                        array_push($total_sum,$tRent);
                                                                        @endphp
                                                                    {{$dueMonth." ".$product->billing_unit}}
                                                                    <input type="hidden" name="dueMonths[{{$pKey}}]" value="{{$dueMonth}}"> 
                                                                    <input type="hidden" name="due_month_count[]" id="due_month_count{{$key.$pKey}}" value="{{$dueMonth}}">   
                                                                    
                                                                </td>
                                                                <td data-label="Total Due Rent">
                                                                    {{$product->product_rent*$dueMonth}}
                                                                    <input type="hidden" name="totalProductRent[{{$pKey}}]" value="{{$product->product_rent*$dueMonth}}">    
                                                                    <input type="hidden" name="total_due_month_rent[]" id="total_due_month_rent{{$key.$pKey}}" value="{{$product->product_rent*$dueMonth}}">   
                                                                </td>
                                                                <td data-label="Reason">{{$product->stop_requested_reason}}</td>
                                                            </tr>    
                                                            @endforeach
                                                        <tr>
                                                            <td  colspan="12" class=" text-right" data-label="Total">
                                                                <Strong>Total : </Strong>
                                                                {{array_sum($total_sum)}}
                                                                &emsp;<input type="hidden" name="totalRent[{{$pKey}}]" value="{{array_sum($total_sum)}}">
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="row justify-content-center">
                                                <button type="submit" class="btn btn-outline-primary" name="btn_submit" value="pickup">Pickup Product</button>
                                                &emsp;
                                                <button type="submit" class="btn btn-outline-primary" name="btn_submit" value="renew" data-id={{$key}}>Renew Product</button>
                                                &emsp;
                                                <button type="submit" class="btn btn-outline-danger" name="btn_submit" value="remove" data-id={{$key}}>Remove</button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            </tr>                            
                        @endforeach
                    </tbody>
                </table>
                @php
                    $append_arr = array();
                    if(isset($filterArr['textSearch'])){
                        $append_arr['text_search'] = $filterArr['textSearch'];
                    }
                    if(isset($filterArr['startDate'])){
                        $append_arr['start_date'] = $filterArr['startDate'];
                    }
                    if(isset($filterArr['endDate'])){
                        $append_arr['end_date'] = $filterArr['endDate'];
                    }
                @endphp
                {{$get_data->appends($append_arr)->links('Custom.Pagination.pagination')}}
            </div>
        </div>
        {{--view stop Products cash--}}
        <div class="modal fade" id="viewStopProducts" tabindex="-1" role="dialog" aria-labelledby="viewStopProducts" aria-hidden="true">
            <div class="modal-dialog modal modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewStopProducts">All Products</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
              
                    <div class="modal-body">  
                        <div class="table table-responsive">
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
                                    @foreach($getProductwiseData as $key =>$productData)
                                        <tr>
                                            <td>{{$srno++}}</td>
                                            <td>{{$productData[0]->product_name}}</td>
                                            <td>{{count($productData)}}</td>
                                            <td>
                                                {{-- {{request()->fullUrlWithQuery(['sort_column'=>'user.username','sort_direction'=>'ASC'])}}" --}}
                                                {{-- <form action="{{url('/')}}/stop_requested" method="get">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-primary btn-sm">Search</button>
                                                </form> --}}
                                                <a class="btn btn-outline-primary btn-sm" href="{{request()->fullUrlWithQuery(['selected_product'=>$key])}}">Search</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{-- <div class="modal-footer">
                        <button type="submit" class="btn btn-outline-primary btn-sm">Submit</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal"><i class="fa fa-window-close" aria-hidden="true"></i></button>
                    </div> --}}
                </div>
            </div>
        </div>
       
    @endsection
    @section('script')
        @if(request()->routeIs('view_all_leads') ? 'active' : '')
            <script>
                $.removeCookie('filter_collapse_js');
            </script>
        @endif
        {{-- @if($productModal=='show')
            <script>
                $("#viewStopProducts").modal('show');
            </script>
        @endif --}}
        <script>
            $('#stopProductsDisplay').on('click',function(){
                $("#viewStopProducts").modal('show');
            });
            $('#stopProductsTable').DataTable();
            $('#start_date').on('change',function(){
                $('#end_date').attr('required',true);
            });
            $('#end_date').on('change',function(){
                $('#start_date').attr('required',true);
            });
            $('#btn_clear').on('click',function(){
                    //window.location.href = "{{url('/')}stop_requested";
                    var url="<?php echo url('/');?>/stop_requested";
                    window.location.href = url;
            });
            $('.btn_renew').on('click',function(){
                let data_id = $(this).data('id');
                let order_count = $('#order_count'+data_id).val();
                let cust_id = $('#customer_id'+data_id).val();
                let customer_id = [[]];
                    //customer_id[0].push($('#customer_id'+data_id).val());
                let customer_name = $('#customer_name'+data_id).val();
                let order_id = [[]];
                let order_details_id = [[]];
                let product_id = [[]];
                let product_name = [[]];
                let pickup_date = [[]];
                let product_rent = [[]];
                let product_deposite = [[]];
                let due_month_count = [[]];
                let total_due_month_rent = [[]];
                let check = [[]];
                let renewal_pickup_btn = "Renew";
                for (let i = 0; i < order_count; i++) {
                    if($('#checkedProduct'+data_id+i).is(":checked")){
                        order_id[0].push($('#order_id'+data_id+i).val());
                        order_details_id[0].push($('#order_details_d'+data_id+i).val());
                        product_id[0].push($('#product_id'+data_id+i).val());
                        product_name[0].push($('#product_name'+data_id+i).val());
                        pickup_date[0].push($('#pickup_date'+data_id+i).val());
                        product_rent[0].push($('#product_rent'+data_id+i).val());
                        product_deposite[0].push($('#product_deposite'+data_id+i).val());
                        due_month_count[0].push($('#due_month_count'+data_id+i).val());
                        total_due_month_rent[0].push($('#total_due_month_rent'+data_id+i).val());
                        customer_id[0].push($('#customer_id'+data_id).val());   
                        check[0].push(i);
                    }
                }
                //alert($('#order_arr'+data_id).val());
                //let order_details_id = $('#tbl_id'+data_id).val();
                var dataString = ({_token:"{{ csrf_token() }}",customer_id:""+JSON.stringify(customer_id),
                                    order_id:""+JSON.stringify(order_id),
                                    // order_details_id:""+JSON.stringify(order_details_id),
                                    // product_id:""+JSON.stringify(product_id),
                                    // product_name:""+JSON.stringify(product_name),
                                    // customer_name:""+customer_name,
                                    // pickup_date:""+JSON.stringify(pickup_date),
                                    // product_rent:""+JSON.stringify(product_rent),
                                    // product_deposite:""+JSON.stringify(product_deposite),
                                    // due_month_count:""+JSON.stringify(due_month_count),
                                    // total_due_month_rent:""+JSON.stringify(total_due_month_rent),
                                    // check:""+JSON.stringify(check),
                                    renewal_pickup_btn:""+renewal_pickup_btn});
                $.ajax({
                    type: "POST",
                    url: "{{url('/')}}/renewal_pickup_product",
                    data: dataString,
                    cache: false,
                    success: function (data) {
                        //data.redirect;
                        console.log(data);
                        //location.reload();
                    }
                });
            });
        </script>
     
    @endsection
</body>
</html>
