@extends('header_and_sidebar')
    @section('styles')
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
                <div class="card-header"  id="filter_card">
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
                <div class="card-body collapse" id="filter-collapse" aria-labelledby="headingTwo" data-parent="#filter_card">
                    <form action="{{url('/')}}/vendor-live-inventory" method="GET" id="all_order_form">
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
                                                                size="5" autocomplete="off" value="{{request()->get('cust_name')}}">
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
                                                            value="{{request()->get('cust_no')}}">
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
                                                            size="5" autocomplete="off" value="{{request()->get('vdr_name')}}">
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
                                                                <option value="{{$product->id}}" @if(request()->get('prod_name')!=null) @if(in_array($product->id,request()->get('prod_name'))){{'selected'}} @endif @endif>{{$product->product_name}}</option>
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
                                                <input type="date" name="from_date" id="from_date" class="form-control form-control-sm" value="{{request()->get('form_date')}}">
                                            </div>
                                        </div>
                                        <div class="row my-2">
                                            <div class="col-md-3">
                                                <strong>To</strong>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="date" name="end_date" id="end_date" class="form-control form-control-sm" value="{{request()->get('end_date')}}">
                                            </div>
                                        </div>
                                        <div class="row my-2">
                                            <div class="col-md-3">
                                                <strong>Vendor Rent:</strong>
                                            </div>
                                            <div class="col-md-9">
                                                <select class="form-control form-control-sm border" name="vendor_rent_flag" title="Select Vendor Rent" id="vendor_rent_flag">
                                                    <option value="All" @if(request()->get('vendor_rent_flag')=='All'){{"selected"}}@endif>All</option>
                                                    <option value="1" @if(request()->get('vendor_rent_flag')=='1'){{"selected"}}@endif>Not Empty</option>
                                                    <option value="0" @if(request()->get('vendor_rent_flag')=='0'){{"selected"}}@endif>Empty</option>
                                                </select>
                                            </div>
                                        </div>                                        
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-md-2">
                                <div class="row">   
                                    <div class="col-md">
                                        <a href="{{url('/')}}/vendor-live-inventory" class="btn btn-outline-secondary btn-sm btn-block btn">Clear Filter</a>
                                    </div>
                                </div>
                                <div class="row my-2">
                                    <div class="col">
                                        <button type="submit" class="btn btn-outline-primary btn-block btn-sm" name="btn_submit" value="submit">Submit</button>
                                    </div>
                                </div>
                                <div class="row my-2">
                                    <div class="col">
                                        <button type="submit" class="btn btn-outline-success btn-block btn-sm" name="btn_submit" value="export">Export</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <form id="form_submit_rent" action="{{route('vendor-live-inventory')}}" method="POST">
                    @csrf
                    <div class="table table-responsive jim-table-responsive table-sm">
                        <table class="table table-hover" id="tbl_all_order">
                            <thead class="thead-light">
                                <th>Order Date</th>
                                <th>order ID</th>
                                <th>Customer Name</th>
                                <th>Customer Contact</th>
                                <th>Product Name</th>
                                <th>Vendor Name</th>
                                <th>Product Rent</th>
                                <th>Vendor Rent</th>
                                <th>Inventory Id</th>
                                <th>Status</th>
                            </thead>
                            <tbody>
                                @foreach($order_details as $key => $value)
                                    <tr>
                                        <td data-label="Order Date" class="text-nowrap">{{date('d-m-Y',strtotime($value->creation_date))}}</td>
                                        <td>{{$value->order_id}}</td>
                                        <td data-label="Customer">{{$value->customer_name}}</td>
                                        <td data-label="Contact">{{$value->primary_contact_no}}</td>
                                        <td data-label="Equipment">{{$value->product_name}}</td>
                                        <td data-label="Vendor">{{$value->vendor_name}}</td>
                                        <td data-label="Rent">{{$value->product_rent}}</td>
                                        <td data-label="Vendor Rent">
                                            <input type="hidden" name="order_details_id[]" id="order_details_id{{$key}}" value="{{$value->id}}">
                                            <input type="number" name="vendor_rent[]" id="vendor_rent{{$key}}" class="form-control form-control-sm" value="{{$value->vendor_rent}}">
                                        </td>
                                        <td data-label="Inventory Id">
                                            {{-- {{$value->unique_id}} --}}
                                            {{-- <input type="hidden" name="order_details_id[]" id="order_details_id{{$key}}" value="{{$value->id}}"> --}}
                                            <input type="text" name="inventory_id[]" id="inventory_id{{$key}}" class="form-control form-control-sm" value="{{$value->unique_id}}">
                                        </td>
                                        <td data-label="Status">
                                            @if($value->current_status == 'Pending Pickup' || $value->current_status == 'Picked Up' || $value->current_status == 'Picked UP' || $value->current_status == 'Pickuped')
                                                <span class="badge badge-danger">Stop</span>
                                            @elseif($value->sale_rental == 'Sale')
                                                <span class="badge badge-success">Sold</span>
                                            @else
                                                <span class="badge badge-success">Live @if($value->current_status == 'CustStop'){{"(Stop Req.)"}}@endif</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <center>
                            <button type="submit" class="btn btn-sm btn-outline-success" name="submit_rent" id="submit_rent" value="submit_rent">Submit</button>
                        </center>
                        {{$order_details->withPath(url()->full())->links('Custom.Pagination.pagination')}}
                    </div>
                </form>
            </div>
    
        <div id="count_modal" class="modal modal-fade" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><span id="modal_title">Modal</span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="table" style="overflow:scroll; height:400px;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
       
    @endsection
    @section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.0/js/bootstrap-datepicker.min.js"></script>
    {{-- alert on screeen popup Script--}}
    <script src="{{url('/')}}/assets/dist/toast.min.js"></script>
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
            </script>
    @endsection
</body>
</html>
