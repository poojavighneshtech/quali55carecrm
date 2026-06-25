@extends('header_and_sidebar')
    @section('styles')
        {{-- <link rel="stylesheet" href="{{url('/')}}/assets/dist/toast.min.css "> --}}
        <style>
            #filter_card{
                position: relative;
            }
            #h6_filter{
                position: absolute;
                right: 50%;
                top: -0.5rem;
                /* z-index: -100; */
            }
        </style>
    @endsection
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
        <div class="alert alert-danger fade out" id="date_alert" style="display:none">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Date Range</strong> TO date should be greator than start date
        </div>
        <div class="card" id="filter_card">
            <div class="card-header border-primary" id="filter_card">
                <div class="row">
                    <div class="col text-primary" data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                        <strong>Order Details</strong>
                    </div>
                    <div class="col-auto">
                        <a data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                            <i class="fa fa-chevron-down pull-right"></i>&emsp;<b>Filter</b>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body border-primary collapse" id="filter-collapse" aria-labelledby="headingTwo" data-parent="#filter_card">
                <form action="{{url('/')}}/order_details" method="GET" id="all_order_form">
                    @csrf
                    <div class="row">
                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <label for="customer_name"><strong>Customer Name:</strong></label>
                                                </div>
                                                <div class="col-md-7">
                                                    <input type="text" class="form-control form-control-sm" name="cust_name" id="cust_name"  placeholder="Customer Name.." 
                                                            size="5" autocomplete="off" value="@if(isset($filter_data['cust_name'])){{$filter_data['cust_name']}}@endif">
                                                        <datalist id="datalist_customers"></datalist>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label for="contact_no"><strong>Contact No :</strong></label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control form-control-sm" name="cust_no"  id="cust_no" placeholder="Contact No..."
                                                        oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" 
                                                        value="@if(isset($filter_data['cust_no'])){{$filter_data['cust_no']}}@endif">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row my-2">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <label for="vendor_name"><strong>Vendor Name:</strong></label>
                                                </div>
                                                <div class="col-md-7">
                                                    <input type="text" class="form-control form-control-sm" name="vdr_name" id="vdr_name"  placeholder="Vendor Name.." 
                                                        size="5" autocomplete="off" value="@if(isset($filter_data['vdr_name'])){{$filter_data['vdr_name']}}@endif">
                                                    <datalist id="datalist_vendor_name"></datalist>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label for="product_name"><strong>Products:</strong></label>
                                                </div>
                                                <div class="col-md-8">
                                                    <select class="select2 form-control form-control-sm" name="prod_name[]" id="prod_name" data-live-search = true multiple="multiple" style="width: 100%">
                                                        @foreach ($products as $key => $product)
                                                            <option value="{{$product->id}}"@if(isset($filter_data['prod_name'])) @if(in_array($product->id,$filter_data['prod_name']))selected @endif @endif>{{$product->product_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <label for="patient_name"><strong>Patient Name:</strong></label>
                                                </div>
                                                <div class="col-md-7">
                                                    <input type="text" class="form-control form-control-sm" name="patient_name" id="patient_name"  placeholder="Patient Name.." 
                                                        value="@if(isset($filter_data['patient_name'])){{$filter_data['patient_name']}}@endif">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <label for="product_name"><strong>Order Type:</strong></label>
                                                </div>
                                                <div class="col-md-7">
                                                    <select class="selectpicker form-control form-control-sm border" name="order_type" id="order_type">
                                                        <option value="All" @if(isset($filter_data['order_type'])) @if($filter_data['order_type'] == "All")selected @endif @endif>All</option>
                                                        <option value="Delivery" @if(isset($filter_data['order_type'])) @if($filter_data['order_type'] == "Delivery")selected @endif @endif>Delivery</option>
                                                        <option value="Pick Up" @if(isset($filter_data['order_type'])) @if($filter_data['order_type'] == "Pick Up")selected @endif @endif>Pick Up</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-group my-2">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <label for="city"><strong>City:</strong></label>
                                                </div>
                                                <div class="col-md-7">
                                                    {{-- <input type="text" class="form-control form-control-sm" name="city" id="city"  placeholder="Patient Name.."  --}}
                                                    <select class="select selectpicker form-control form-control-sm" name="city" id="city">
                                                        <option value="All" @if(isset($filter_data['city'])) @if($filter_data['city'] == "All")selected @endif @endif>All</option>
                                                        {{-- <option value="Mumbai" @if(isset($filter_data['city'])) @if($filter_data['city'] == "Mumbai")selected @endif @endif>Mumbai</option>
                                                        <option value="Pune" @if(isset($filter_data['city'])) @if($filter_data['city'] == "Pune")selected @endif @endif>Pune</option> --}}
                                                        @foreach(config('app.citylist') as $key=>$value)
                                                            <option value="{{$value}}" @if(isset($filter_data['city'])) @if($filter_data['city'] == $value)selected @endif @endif>{{$value}}</option>
                                                            {{-- <option value="Pune" @if(isset($filter_data['city'])) @if($filter_data['city'] == "Pune")selected @endif @endif>Pune</option> --}}
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>From</strong>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="date" name="from_date" id="from_date" class="form-control form-control-sm" value="@if(isset($filter_data['from_date'])){{$filter_data['from_date']}}@endif">
                                        </div>
                                    </div>
                                    <div class="row my-2">
                                        <div class="col-md-3">
                                            <strong>To</strong>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="date" name="end_date" id="end_date" class="form-control form-control-sm" value="@if(isset($filter_data['end_date'])){{$filter_data['end_date']}}@endif">
                                        </div>
                                    </div>
                                    <div class="row my-2">
                                        <div class="col-md-5">
                                            <label for="product_name"><strong>Equipment Type:</strong></label>
                                        </div>
                                        <div class="col-md-7">
                                            <select class="selectpicker form-control form-control-sm border" name="equip_type" id="equip_type">
                                                <option value="All" @if(isset($filter_data['equip_type'])) @if($filter_data['equip_type'] == "All")selected @endif @endif>All</option>
                                                <option value="Live" @if(isset($filter_data['equip_type'])) @if($filter_data['equip_type'] == "Live")selected @endif @endif>Live</option>
                                                <option value="Sold" @if(isset($filter_data['equip_type'])) @if($filter_data['equip_type'] == "Sold")selected @endif @endif>Sold</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="row">
                                <div class="col-md">
                                    <a href="{{url('/')}}/order_details" class="btn btn-outline-secondary btn-sm btn-block btn">Clear Filter</a>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col">
                                    <button type="submit" class="btn btn-outline-primary btn-block btn-sm" name="btn_submit" value="submit">Submit</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <button type="submit" class="btn btn-outline-success btn-sm btn-block" name="btn_submit" value="export_excel">Export Excel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <div class="row justify-content-between">
                        <div class="col-auto">
                            <div class="row">
                                <div class="col-auto">Orders</div>
                                <div class="col-auto"><a  type="button" class="count" id="orders_count"><span class="badge badge-primary">@if(isset($count)){{$count['orders_count']}}@else{{0}}@endif</span></a></div>
                                <div class="col-auto">Vendor Rent</div>
                                <div class="col-auto"><a  type="button" class="count" id="vendor_rent_count"><span class="badge badge-primary">@if(isset($count)){{$count['vendor_rent_count']}}@else{{0}}@endif</span></a></div>
                                <div class="col-auto">Order Sale</div>
                                <div class="col-auto"><a  type="button" class="count" id="order_sale_count"><span class="badge badge-primary">@if(isset($count)){{$count['order_sale_count']}}@else{{0}}@endif</span></a></div>
                                <div class="col-auto">Order Rent</div>
                                <div class="col-auto"><a  type="button" class="count" id="order_rent_count"><span class="badge badge-primary">@if(isset($count)){{$count['order_rent_count']}}@else{{0}}@endif</span></a></div>
                                <div class="col-auto">Order Deposit</div>
                                <div class="col-auto"><a  type="button" class="count" id="order_deposite_count"><span class="badge badge-primary">@if(isset($count)){{$count['order_deposite_count']}}@else{{0}}@endif</span></a></div>
                                <div class="col-auto">Order Transport</div>
                                <div class="col-auto"><a  type="button" class="count" id="order_transport_count"><span class="badge badge-primary">@if(isset($count)){{$count['order_transport_count']}}@else{{0}}@endif</span></a></div>
                                <div class="col-auto">Equipment</div>
                                <div class="col-auto"><a  type="button" class="count" id="equipment_count"><span class="badge badge-primary">@if(isset($count)){{$count['equipment_count']}}@else{{0}}@endif</span></a></div>
                            </div>
                        </div>
                        <div class="col-auto mr-auto"></div>
                    </div>
                </li>
            </ul>
            <div class="table table-responsive jim-table-responsive table-sm">
                <table class="table table-hover" id="tbl_all_order">
                    <thead class="thead-light">
                        <th>Action</th>
                        <th>Order Date</th>
                        <th>Created On</th>
                        <th>Order Id</th>
                        <th>Product Name</th>
                        <th>Vendor Name</th>
                        <th>Customer Name</th>
                        <th>Patient Name</th>
                        <th>Customer Contact</th>
                        <th>Vendor Rent</th>
                        <th>Product Rent</th>
                        <th>Product Deposit</th>
                        <th>Transport</th>
                        <th>Product Type</th>
                        <th>City</th>
                        <th>Status</th>
                    </thead>
                    <tbody>
                        @foreach($order_details as $key => $value)
                            <tr>
                                <td class="text-nowrap">
                                    <a class="btn btn-sm btn-outline-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Action
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                        <a class="dropdown-item btn btn-outline-primary btn-sm" href="{{route('transaction_history',['order_id'=>$value->order_id])}}">Trans Hist</a>
                                        <a class="dropdown-item btn btn-outline-primary btn-sm viewImages" onClick="viewImages({{$value->order_id}});">View Images</a>
                                    </div>
                                </td>
                                <td data-label="Order Date" class="text-nowrap">{{date('d-m-Y',strtotime($value->creation_date))}}</td>
                                <td data-label="Created On" class="text-nowrap">{{date('d-M-y h:i',strtotime($value->created_at))}}</td>
                                <td data-label="Order Id">{{$value->order_id}}</td>
                                <td data-label="Equipment">{{$value->product_name}}</td>
                                <td data-label="Vendor">{{$value->vendor_name}}</td>
                                <td data-label="Customer">{{$value->customer_name}}</td>
                                <td data-label="Customer">{{$value->patient_name}}</td>
                                <td data-label="Contact No">{{$value->primary_contact_no}}</td>
                                <td data-label="Vendor Rent">{{$value->vendor_rent}}</td>
                                <td data-label="Rent">{{$value->product_rent}}</td>
                                <td data-label="Deposit">{{$value->product_deposite}}</td>
                                <td data-label="Transport">{{$value->transport}}</td>
                                <td data-label="Type">{{$value->sale_rental}}</td>
                                <td data-label="City">{{$value->city}}</td>
                                <td data-label="Status">
                                    @if($value->current_status == 'Pending Pickup' || $value->current_status == 'Picked Up' || $value->current_status == 'Picked UP' || $value->current_status == 'Pickuped')
                                        <span class="badge badge-danger">Stop</span>
                                    @elseif($value->current_status == 'Cancel')
                                        <span class="badge badge-secondary">Cancel</span>
                                    @elseif($value->sale_rental == 'Sale')
                                        <span class="badge badge-success">Sold</span>
                                    @else
                                        <span class="badge badge-success">Live @if($value->current_status == 'CustStop'){{"(Stop Req.)"}}@endif</span>
                                    @endif
                                </td>
                                {{-- <td data-label="Customer Id">{{$value->customer_id}}</td> --}}
                                {{-- <td></td> --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @php
                    $append_arr = array();
                    if(isset($filter_data['cust_name'])){
                        $append_arr['cust_name'] = $filter_data['cust_name'];
                    }
                    if(isset($filter_data['cust_no'])){
                        $append_arr['cust_no'] = $filter_data['cust_no'];
                    }
                    if(isset($filter_data['patient_name'])){
                        $append_arr['patient_name'] = $filter_data['patient_name'];
                    }
                    if(isset($filter_data['from_date'])){
                        $append_arr['from_date'] = $filter_data['from_date'];
                    }
                    if(isset($filter_data['end_date'])){
                        $append_arr['end_date'] = $filter_data['end_date'];
                    }
                    if(isset($filter_data['vdr_name'])){
                        $append_arr['vdr_name'] = $filter_data['vdr_name'];
                    }
                    if(isset($filter_data['prod_name'])){
                        $append_arr['prod_name'] = $filter_data['prod_name'];
                    }
                    if(isset($filter_data['order_type'])){
                        $append_arr['order_type'] = $filter_data['order_type'];
                    }
                    if(isset($filter_data['equip_type'])){
                        $append_arr['equip_type'] = $filter_data['equip_type'];
                    }
                    if(isset($filter_data['city'])){
                        $append_arr['city'] = $filter_data['city'];
                    }
                @endphp
                {{$order_details->appends($append_arr)->links('Custom.Pagination.pagination')}}
            </div>
        </div>
        
        <div class="modal fade" id="images" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Update Corporate Payments</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                       <div id="product-images">

                       </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="modal fade" id="viewImages1" tabindex="-1" role="dialog" aria-labelledby="viewPaymentImage" aria-hidden="true" style="text-align: center;">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewPaymentImage">Product Images</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">                    
                        <div id="product-images">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div> --}}
    @endsection
    @section('script')
        <script>
            $('.select2').select2({
                theme: "classic",
                placeholder: 'Select Products',
                allowClear: true
            });
            $("#cust_name").on("click", function(){
                var route = "{{ url('complaint_customers_populate') }}";
                $('#cust_name').typeahead({ 
                    source: function (query, process) {
                        return $.get(route, {
                            query: query
                        }, function (data) {
                            //var obj = jQuery.parseJSON(data);
                            //console.log(data);
                            return process(data);
                        });
                    }
                });
            });
            $("#vdr_name").on("click", function(){
                var route = "{{ url('complaint_vendors_populate') }}";
                $('#vdr_name').typeahead({ 
                    source: function (query, process) {
                        return $.get(route, {
                            query: query
                        }, function (data) {
                            //var obj = jQuery.parseJSON(data);
                            //console.log(data);
                            return process(data);
                        });
                    }
                });
            });
            $(".count").on("click",function(){
                let id = $(this).attr("id");
                let cust_name = $("#cust_name").val();
                let cust_no = $("#cust_no").val();
                let from_date = $("#from_date").val();
                let end_date = $("#end_date").val();
                let vdr_name = $("#vdr_name").val();
                let prod_name = $("#prod_name").val();
                let order_type = $("#order_type").val();
                

                $("#table").empty();
                // var thead = "<thead>";
                // var tbody = "<tbody>";
                // var tr = "<tr>";
                // alert(id);
                if(id == "orders_count")
                {
                    
                    console.log(id);
                }
                else if(id == "vendor_rent_count")
                {
                    let dataString = ({_token:"{{ csrf_token() }}",cust_name:""+cust_name,cust_no:""+cust_no,from_date:""+from_date,end_date:""+end_date,vdr_name:""+vdr_name,prod_name:""+prod_name,order_type:""+order_type,section:"vendor_rent_count"});
                    $.ajax({
                        type: "GET",
                        url: "{{url('/')}}/order_details_count",
                        data: dataString,
                        cache: false,
                        success: function (data)
                        {
                            // console.log(data);
                            let data_length = data.length;
                            // console.log(data_length);
                            var table = "<table id='count' class='table table-stripped'>";
                            table += "<thead>";
                            table +="<tr>";
                            table += "<th>Sr.No</th>";
                            table += "<th>Vendor Name</th>";
                            table += "<th>Live Product Qty</th>";
                            table += "<th>Live Product Rent</th>";
                            table += "<th>Picked up Product Qty</th>";
                            table += "<th>Picked up Product Rent</th>";
                            table +="</tr>";
                            table += "</thead>";
                            table += "<tbody>";
                            for(var i = 0; i < data_length; i++)
                            {
                                // console.log(i+1);
                                table += "<tr>";
                                    table += "<td>"+(i+1)+"</td>";
                                    table += "<td>"+data[i].vendor_name+"</td>";
                                    table += "<td>"+data[i].product_live_qty+"</td>";
                                    table += "<td>"+data[i].total_live_rent+"</td>";
                                    table += "<td>"+data[i].product_stop_qty+"</td>";
                                    table += "<td>"+data[i].total_stop_rent+"</td>";
                                table += "</tr>";
                            }
                            table += "</tbody>";         
                            table +="</table>";
                            // console.log(table);
                            $("#table").append(table);
                            $('.table').DataTable();
                        }
                    });
                    $('#modal_title').text('Vendor Rent Details');
                    $("#count_modal").modal("show");
                }
                else if(id == "order_rent_count")
                {
                    let dataString = ({_token:"{{ csrf_token() }}",cust_name:""+cust_name,cust_no:""+cust_no,from_date:""+from_date,end_date:""+end_date,vdr_name:""+vdr_name,prod_name:""+prod_name,order_type:""+order_type,section:"order_rent_count"});
                    $.ajax({
                        type: "GET",
                        url: "{{url('/')}}/order_details_count",
                        data: dataString,
                        cache: false,
                        success: function (data)
                        {
                            // console.log(data);
                            let data_length = data.length;
                            // console.log(data_length);
                            var table = "<table id='count' class='table table-stripped'>";
                            table += "<thead>";
                            table +="<tr>";
                            table += "<th>Sr.No</th>";
                            table += "<th>Product Name</th>";
                            table += "<th>Live Product Qty</th>";
                            table += "<th>Live Product Rent</th>";
                            table += "<th>Picked Up Product Qty</th>";
                            table += "<th>Picked Up Product Rent</th>";
                            table +="</tr>";
                            table += "</thead>";
                            table += "<tbody>";
                            for(var i = 0; i < data_length; i++)
                            {
                                // console.log(i+1);
                                table += "<tr>";
                                    table += "<td>"+(i+1)+"</td>";
                                    table += "<td>"+data[i].product_name+"</td>";
                                    table += "<td>"+data[i].product_live_qty+"</td>";
                                    table += "<td>"+data[i].total_live_rent+"</td>";
                                    table += "<td>"+data[i].product_stop_qty+"</td>";
                                    table += "<td>"+data[i].total_stop_rent+"</td>";
                                table += "</tr>";
                            }
                            table += "</tbody>";         
                            table +="</table>";
                            // console.log(table);
                            $("#table").append(table);
                            $('.table').DataTable();
                        }
                    });
                    $('#modal_title').text('Product Rent Details');
                    $("#count_modal").modal("show");
                }
                else if(id == "order_deposite_count")
                {
                    let dataString = ({_token:"{{ csrf_token() }}",cust_name:""+cust_name,cust_no:""+cust_no,from_date:""+from_date,end_date:""+end_date,vdr_name:""+vdr_name,prod_name:""+prod_name,order_type:""+order_type,section:"order_deposite_count"});
                    $.ajax({
                        type: "GET",
                        url: "{{url('/')}}/order_details_count",
                        data: dataString,
                        cache: false,
                        success: function (data)
                        {
                            // console.log(data);
                            let data_length = data.length;
                            // console.log(data_length);
                            var table = "<table id='count' class='table table-stripped'>";
                            table += "<thead>";
                            table +="<tr>";
                            table += "<th>Sr.No</th>";
                            table += "<th>Product Name</th>";
                            table += "<th>Live Product Qty</th>";
                            table += "<th>Live Product Deposit</th>";
                            table += "<th>Stop Product Qty</th>";
                            table += "<th>Stop Product Deposit</th>";
                            table +="</tr>";
                            table += "</thead>";
                            table += "<tbody>";
                            for(var i = 0; i < data_length; i++)
                            {
                                // console.log(i+1);
                                table += "<tr>";
                                    table += "<td>"+(i+1)+"</td>";
                                    table += "<td>"+data[i].product_name+"</td>";
                                    table += "<td>"+data[i].product_live_qty+"</td>";
                                    table += "<td>"+data[i].total_live_deposite+"</td>";
                                    table += "<td>"+data[i].product_stop_qty+"</td>";
                                    table += "<td>"+data[i].total_stop_deposite+"</td>";
                                table += "</tr>";
                            }
                            table += "</tbody>";         
                            table +="</table>";
                            // console.log(table);
                            $("#table").append(table);
                            $('.table').DataTable();
                        }
                    });
                    $('#modal_title').text('Product Deposit Details');
                    $("#count_modal").modal("show");
                }
                else if(id == "equipment_count")
                {
                    let dataString = ({_token:"{{ csrf_token() }}",cust_name:""+cust_name,cust_no:""+cust_no,from_date:""+from_date,end_date:""+end_date,vdr_name:""+vdr_name,prod_name:""+prod_name,order_type:""+order_type,section:"equipment_count"});
                    $.ajax({
                        type: "GET",
                        url: "{{url('/')}}/order_details_count",
                        data: dataString,
                        cache: false,
                        success: function (data)
                        {
                            // console.log(data);
                            let data_length = data.length;
                            // console.log(data_length);
                            var table = "<table id='count' class='table table-stripped'>";
                            table += "<thead>";
                            table +="<tr>";
                            table += "<th>Sr.No</th>";
                            table += "<th>Product Name</th>";
                            table += "<th>Live Product Qty</th>";
                            table += "<th>Stop Product Qty</th>";
                            // table += "<th>Product Rent</th>";
                            table +="</tr>";
                            table += "</thead>";
                            table += "<tbody>";
                            for(var i = 0; i < data_length; i++)
                            {
                                // console.log(i+1);
                                table += "<tr>";
                                    table += "<td>"+(i+1)+"</td>";
                                    table += "<td>"+data[i].product_name+"</td>";
                                    table += "<td>"+data[i].product_live_qty+"</td>";
                                    table += "<td>"+data[i].product_stop_qty+"</td>";
                                    // table += "<td>"+data[i].total_rent+"</td>";
                                table += "</tr>";
                            }
                            table += "</tbody>";         
                            table +="</table>";
                            // console.log(table);
                            $("#table").append(table);
                            $('.table').DataTable();
                        }
                    });
                    $('#modal_title').text('Product Qty Details');
                    $("#count_modal").modal("show");
                }
                else
                {
                    console.log(id);
                }
                // $("#count").DataTable().draw();
            });
            function viewImages(order_id){
                // let id = $(".viewImages").attr("id");
                // order_id = $("#"+id).data("val");
                var dataString = ({_token:"{{ csrf_token() }}",order_id:""+order_id});
                $.ajax({
                    type: "GET",
                    url: "{{url('/')}}/get-order-images/"+order_id,
                    data: dataString,
                    cache:false,
                    success: function (data)
                    {
                        let images = data;
                        $('#product-images').empty();
                        if(images!=false){
                            let divcard= '<div class="card-body">';
                                    divcard+='<div class="text-center">';
                                        Object.keys(images).forEach(function(key){
                                            if(images[key].includes("http://")){
                                                divcard+='<img src="'+images[key]+'" class="img-fluid img-thumbnail view-image" alt="Responsive image" width="250px" height="400px">';
                                            }else{
                                                divcard+='<img src="http://'+images[key]+'" class="img-fluid img-thumbnail view-image" alt="Responsive image" width="250px" height="400px">';
                                            }
                                        });
                                    divcard+='</div>';
                                divcard+= '</div>';
                            $('#product-images').append(divcard);
                        }else{
                            $('#product-images').append("<span class='text-center'>No Product Images Found</span>");
                        }
                        $("#images").modal("show");
                    }
                });
            };
        </script>
    @endsection