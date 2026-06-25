@extends('header_and_sidebar')

@section('style')

@endsection

@section('content')
    <div class="container-fluid my-4">
        <div class="card" id="filter_card">
            <div class="card-header border-primary" id="filter_card">
                <div class="row">
                    <div class="col text-primary" data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                        <strong>Virtual Warehouse Inventory</strong>
                    </div>
                    <div class="col-auto">
                        <a data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                            <i class="fa fa-chevron-down pull-right"></i>&emsp;<b>Filter</b>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body border-primary collapse" id="filter-collapse" aria-labelledby="headingTwo" data-parent="#filter_card">
                <form action="{{url('/')}}/virtual_wh_inventory" method="GET" id="all_leads_form">
                    @csrf
                    <div class="row">
                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="row">
                                        <div class="col-md-4 text-right">
                                            <label for="vendor_name"><strong>Vendor Name:</strong></label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name="filter_vendor_name" id="txt_filter_vendor_name"  placeholder="Vendor_Name.." 
                                                size="5" autocomplete="off" value="@if(isset($filter_arr['vendor_name'])){{$filter_arr['vendor_name']}}@endif">
                                                <datalist id="datalist_vendor_name"></datalist>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-4 text-right">
                                            <label for="products"><strong>Products :</strong></label>
                                        </div>
                                        <div class="col-md-8 text-right">
                                            <select class="select form-control selectpicker border" name="products[]" multiple="true" id="select_products" title="Products">
                                                @foreach($products as $key=>$value)
                                                    <option value="{{$value->product_id}}" @if(isset($filter_arr['product_id']) && in_array($value->product_id,$filter_arr['product_id'])){{"selected"}}@endif>{{$value->product_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="row">
                                        <div class="col-md-3 text-right">
                                            From
                                        </div>
                                        <div class="col-md-9">
                                            <input type="date" name="filter_from_date" id="input_from_date" class="form-control" value="@if(isset($filter_arr['from_date'])){{$filter_arr['from_date']}}@endif">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-3 text-right">
                                            To
                                        </div>
                                        <div class="col-md-9">
                                            <input type="date" name="filter_end_date" id="input_end_date" class="form-control" value="@if(isset($filter_arr['end_date'])){{$filter_arr['end_date']}}@endif">
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-4 text-right">
                                            <label for="status"><strong>Status :</strong></label>
                                        </div>
                                        <div class="col-md-8 text-right">
                                            <select class="select form-control selectpicker border" name="filter_status" id="select_filter_status" title="Status">
                                                <option value="All" selected>All</option>
                                                <option value="0" @if(isset($filter_arr['status']) && $filter_arr['status']=='0'){{"selected"}}@endif>In</option>
                                                <option value="1" @if(isset($filter_arr['status']) && $filter_arr['status']=='1'){{"selected"}}@endif>Out Process</option>
                                                <option value="3" @if(isset($filter_arr['status']) && $filter_arr['status']=='3'){{"selected"}}@endif>Out</option>                                                
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-4 text-right">
                                            <label for="del_boys"><strong>Delivery Boy :</strong></label>
                                        </div>
                                        <div class="col-md-8 text-right">
                                            <select class="select form-control selectpicker border" name="del_boys" id="select_del_boys" title="Delivery Boys">
                                                @foreach($del_boys as $key=>$value)
                                                    <option value="{{$value->user_id}}" @if(isset($filter_arr['del_boys']) && $filter_arr['del_boys']==$value->user_id){{"selected"}}@endif>{{$value->username}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="row">
                                <div class="col">
                                    {{-- <button type="button" class="btn btn-outline-secondary btn-sm" id="btn_clear">Clear Filter</button> --}}
                                    <a href="{{url('/')}}/virtual_wh_inventory" class="btn btn-outline-secondary btn-sm">Clear Filter</a>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col">
                                    <button type="submit" class="btn btn-outline-success btn-block">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="table table-responsive my-3 jim-table-responsive">
                <table class="table table-stripped">
                    <thead>
                        <tr>
                            <th>Sr.No</th>
                            <th>Date</th>
                            <th>Product Name</th>
                            <th>Vendor Name</th>
                            <th>Virtual Warehouse</th>
                            <th>Status</th>
                            <th>Delivery Boy</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $key=>$value)
                            <tr>
                                <td data-label="Sr.No.">{{$records->firstItem()+$loop->index}}</td>
                                <td data-label="Date">{{$value->in_time}}</td>
                                <td data-label="Product Name">{{$value->product_name}}</td>
                                <td data-label="Vendor Name">{{$value->vendor_name}}</td>
                                <td data-label="Virtual Warehouse">{{$value->wh_name.', '.$value->wh_area.', '.$value->wh_city}}</td>
                                <td data-label="Status">
                                    @if($value->status == '0')
                                        <span class="badge badge-danger">In</span>
                                    @elseif($value->status == '1')
                                        <span class="badge badge-warning">Out Process</span>
                                    @elseif($value->status == '3')
                                        <span class="badge badge-success">Out</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td data-label="Delivery boy">{{$value->del_boy}}</td>
                                <td data-label="Action">
                                    @if($value->status == '0')
                                        <button type="button" id="out_process{{$key}}" class="btn btn-outline-warning btn-block out-process" data-id = "{{$value->id}}" data-vendor_id = "{{$value->vdr_id}}">Out Process</button>
                                    @elseif($value->status == '1')
                                    <button type="button" id="out{{$key}}" class="btn btn-outline-success btn-block out" data-id = "{{$value->id}}">Out</button>
                                    @elseif($value->status == '3')
                                        <span class="badge badge-primary">At Vendor</span>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @php
                    $append_arr = array();
                    if(isset($filter_arr['filter_vendor_name'])){
                        $append_arr['filter_vendor_name'] = $filter_arr['filter_vendor_name'];
                    }
                    if(isset($filter_arr['products'])){
                        $append_arr['products'] = $filter_arr['products'];
                    }
                    if(isset($filter_arr['filter_from_date'])){
                        $append_arr['filter_from_date'] = $filter_arr['filter_from_date'];
                    }
                    if(isset($filter_arr['filter_end_date'])){
                        $append_arr['filter_end_date'] = $filter_arr['filter_end_date'];
                    }
                    if(isset($filter_arr['del_boys'])){
                        $append_arr['del_boys'] = $filter_arr['del_boys'];
                    }
                    if(isset($filter_arr['status'])){
                        $append_arr['status'] = $filter_arr['status'];
                    }
                    print_r($append_arr);
                @endphp
                {{$records->appends($append_arr)->links('Custom.Pagination.pagination')}}
            </div>
            <div class="modal fade" id="out_processModal" tabindex="-1" aria-labelledby="out_processLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{url('/')}}/update-vir-state" method="POST" class="form">
                            @csrf
                            <div class="modal-header">
                            <h5 class="modal-title" id="out_processLabel">Out Process</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="hidden_out_process_id" id="hidden_out_process_id">
                                <input type="hidden" name="request_type" id="request_type" value="update-state">
                                <div class="row form-group">
                                    <div class="col-md-4 text-right">
                                        <label for="Warehouse"><strong>Warehouse :</strong></label>
                                    </div>
                                    <div class="col-md-8 text-right">
                                        <select class="select form-control selectpicker border" name="warehouse" id="select_warehouse" title="Select Vendor Warehouse" required>
                                            <option value="Not Found" disabled>No Warehouse Found</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-4 text-right">
                                        <label for="del_boy_select"><strong>Delivery Boy :</strong></label>
                                    </div>
                                    <div class="col-md-8 text-right">
                                        <select class="select form-control selectpicker border" name="del_boy_select" id="del_boy_select" title="Delivery Boys" required>
                                            @foreach($del_boys as $key=>$value)
                                                <option value="{{$value->user_id}}">{{$value->username}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row form-group">
                                    <div class="col-md-4 text-right">
                                        <label for="out_time"><strong>Date :</strong></label>
                                    </div>
                                    <div class="col-md-8 text-right">
                                        <input type="datetime-local" class="form-control" name="out_time" id="out_time" required>
                                    </div>
                                </div>    
                                <div class="row form-group">
                                    <div class="col-md-12 text-center">
                                        <input type="checkbox" name="email_check" id="email_check" checked> <label for="email_check">Send Email</label>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Update State</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
        
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var route = "{{ url('complaint_vendors_populate') }}";
        $('#txt_filter_vendor_name').typeahead({ 
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
        $(".out-process").click(function(){
            let id = $(this).data("id");
            $("#hidden_out_process_id").val(id);
            let vendor_id = $(this).data("vendor_id");
            var dataString = ({_token:"{{ csrf_token() }}",vendor_id:""+vendor_id});
            $.ajax({
                type:"GET",
                url:"{{url('/')}}/getVendorWarehouse",
                data:dataString,
                cache:false,
                success:function(data){
                    console.log(data);
                    var warehouseLength = data.length;
                    console.log(warehouseLength);
                    $("#select_warehouse")
                        .find("option")
                        .remove()
                        .end();
                    for(var j = 0; j < warehouseLength; j++)
                    {
                        $("#select_warehouse").append("<option value='"+data[j].id+"'>"+data[j].wh_name+", "+data[j].wh_area+", "+data[j].wh_city+"</option>");
                    }
                    $('#select_warehouse').selectpicker('refresh');
                }
            });
            $("#out_processModal").modal("show");
        });

        $(".out").click(function(){
            let id = $(this).data("id");
            var dataString = ({_token:"{{ csrf_token() }}",id:""+id,request_type:"update-state-out"});
            if (confirm("Are you sure you want to update the status.") == true)
            {
                $.ajax({
                    type:"POST",
                    url:"{{url('/')}}/update-vir-state",
                    data:dataString,
                    cache:false,
                    success:function(data){
                        location.reload();
                    }
                });
            }
        });
    </script>
@endsection