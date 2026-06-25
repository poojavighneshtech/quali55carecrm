@extends('header_and_sidebar')

@section('styles')
@endsection

@section('content')
@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif
@if(session()->has('error'))
    <div class="alert alert-danger">
        {{ session()->get('error') }}
    </div>
@endif 
<div class="my-2">
    <div class="card" id="filter_card">
        <div class="card-header border-primary" id="filter_card">
            <div class="row">
                <div class="col text-primary" data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                    <strong>Rejected Orders</strong>
                </div>
                <div class="col-auto">
                    <a data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                        <i class="fa fa-chevron-down pull-right"></i>&emsp;<b>Filter</b>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body border-primary collapse" id="filter-collapse" aria-labelledby="headingTwo" data-parent="#filter_card">
            <form action="{{route('rejected-orders')}}" method="GET" id="pending_payments_form">
                @csrf
                <div class="row form-group">
                    <div class="col-md-3">
                        <label for="filterCustomerNameNumber">Customer Name/Number</label>
                        <input type="text" class="form-control form-control-sm" name="filterCustomerNameNumber" id="filterCustomerNameNumber" value="{{request()->get('filterCustomerNameNumber')}}">
                    </div>
                    <div class="col-md-3">
                        <label for="filterOrderId">Order Id</label>
                        <input type="text" class="form-control form-control-sm" name="filterOrderId" id="filterOrderId" value="{{request()->get('filterOrderId')}}">
                    </div>
                    <div class="col-md-3">
                        <label for="filterStartDate">Start Date</label>
                        <input type="date" class="form-control form-control-sm" name="filterStartDate" id="filterStartDate" value="{{request()->get('filterStartDate')}}">
                    </div>
                    <div class="col-md-3">
                        <label for="filterEndDate">End Date</label>
                        <input type="date" class="form-control form-control-sm" name="filterEndDate" id="filterEndDate" value="{{request()->get('filterEndDate')}}">
                    </div>
                </div>
                <div class="row text-center">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-sm btn-outline-success">Search</button>
                        <a type="button" href="{{route('rejected-orders')}}" class="btn btn-sm btn-outline-secondary">Clear</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="table jim-table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Order Id</th>
                        <th>Order Date</th>
                        <th>Created On</th>
                        <th>Type/St</th>
                        <th>Assigned To</th>
                        <th>Customer Name</th>
                        <th>Patient Name</th>
                        <th>Mobile</th>
                        <th>Mode</th>
                        <th>Total Amt</th>
                        <th>Lead Own</th>
                        <th>Location</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $key => $order)
                        <tr>
                            <td data-label='Action'>
                                <button onclick="reassignOrder({{$order->order_id}});" class="btn btn-sm btn-outline-primary">Reassign</button>
                            </td>
                            <td data-label="Order Id">{{$order->order_id}}</td>
                            <td data-label="Order Date">{{date('d-m-y',strtotime($order->DelDate))}}</td>
                            <td data-label="Created On">{{date('d-M-y h:i A',strtotime($order->order_created_at))}}</td>
                            <td data-label="Type/St">
                                @if($order->deliverypickup == 'Delivery')
                                    <span class="badge badge-success">
                                        D
                                    </span>
                                @elseif($order->deliverypickup == 'Collection')
                                    <span class="badge badge-warning">
                                        C
                                    </span>
                                @elseif($order->deliverypickup == 'Pick Up')
                                    <span class="badge badge-danger">
                                        P
                                    </span>
                                @elseif(in_array($order->deliverypickup,['Install','Shifting','Repair','Replace']))
                                    <span class="badge badge-primary">
                                        {{$order->deliverypickup}}
                                    </span>
                                @endif
                                {{"/"}}
                                @if($order->status == 'Pending')
                                <span class="badge badge-danger">
                                    {{"PE"}}
                                </span>
                                @elseif($order->status == 'Accepted')
                                    <span class="badge badge-secondary">
                                        {{"AC"}}
                                    </span>
                                @elseif($order->status == 'Assigned')
                                    <span class="badge badge-warning">
                                        {{"AS"}}
                                    </span>
                                @elseif($order->status == 'InProgress')
                                    <span class="badge badge-primary">
                                        {{"IP"}}
                                    </span>
                                @elseif($order->status == 'Cancel')
                                    <span class="badge danger">
                                        {{"CA"}}
                                    </span>
                                @elseif($order->status == 'Collected')
                                    <span class="badge badge-success">
                                        {{"CO"}}
                                    </span>
                                @elseif($order->status == 'Delivered')
                                    <span class="badge badge-success">
                                        {{"DE"}}
                                    </span>
                                @elseif($order->status == 'Picked up')
                                    <span class="badge badge-success">
                                        {{"PU"}}
                                    </span>
                                @elseif($order->status == 'Completed')
                                <span class="badge badge-success">
                                    {{"COM"}}
                                </span>
                                @elseif($order->status == 'Closed')
                                <span class="badge badge-success">
                                    {{"CL"}}
                                </span>
                                @else
                                    <span class="badge badge-danger">
                                        {{"RE"}}
                                    </span>
                                @endif
                            </td>
                            <td data-label="Assigned To">{{$order->DelAssignedTo}}</td>
                            <td data-label="Customer Name">{{$order->shipping_first_name}}</td>
                            <td data-label="Patient Name">{{$order->patient_name}}</td>
                            <td data-label="Mobile">{{$order->mobileno}}</td>
                            <td data-label="Mode">{{$order->PaymentMode}}</td>
                            <td data-label="Total Amt">{{$order->TotalAmt}}</td>
                            <td data-label="Lead Owner">{{$order->username}}</td>
                            <td data-label="Location">{{$order->location}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{$orders->withPath(url()->full())->links('Custom.Pagination.pagination')}}
    </div>
</div>
<div class="modal fade" id="reassignModal" tabindex="-1" role="dialog" aria-labelledby="reassignModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary" id="reassignModalTitle">Reassign Order</h5>
                <button type="button" class="close btn-sm" data-dismiss="modal" aria-label="Close" onclick="$('.loading-spinner').show();$('.ccad-modal-content').show();">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body container-fluid crdrnotes-body">
                <div class="ccad-modal-content">
                    <form action="{{route('rejected-orders-update')}}" method="POST" class="form">
                        @csrf
                        <input type="hidden" name="updateorderid" id="updateorderid">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <label for="taskAssignedTo">Task Assigned to</label>
                                <select name="taskAssignedTo" id="taskAssignedTo" class="selectpicker form-control form-control-sm" title="Select Delboy" data-live-search="true" required>
                                    <option value="NF" selected disabled>Not Found</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label for="statusUpdate">update Status</label>
                                <select name="statusUpdate" id="statusUpdate" class="selectpicker form-control form-control-sm" title="Select Status" data-live-search="true" required>
                                    <option value="Assigned" selected>Assigned</option>
                                    <option value="Accepted">Accepted</option>
                                    <option value="InProgress">InProgress</option>
                                    <option value="Completed">Completed</option>
                                </select>
                            </div>
                            {{-- <div class="col-md-6">
                                <label for="taskHelpers">Helpers</label>
                                <select name="taskHelpers[]" id="taskHelpers" class="selectpicker form-control form-control-sm" multiple="multiple" title="Select Helpers" data-live-search="true" required>

                                </select>
                            </div> --}}
                        </div>
                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-sm btn-outline-primary" name="btnsubmit" id="btnsubmit">Reassign Order</button>
                        </div>
                    </form>
                </div>
            </div>                
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script>
        function reassignOrder(orderid){
            var dataString = ({_token:"{{ csrf_token() }}",order_id:""+orderid,request_type:"fetchOrderDetails"});
            console.log(dataString);
            $.ajax({
                type: "POST",
                url: "{{url('/')}}/getOrderDetails",
                data: dataString,
                cache:false,
                success: function (response)
                {
                    // console.log(response);
                    decodedResponse = JSON.parse(response);
                    let delboys = decodedResponse.delBoys;
                    let optionsDel = "";
                    let optionsHel = "";
                    let helpers = "";
                    // if(decodedResponse.helpers == "[No helper]"){
                    //     optionsHel += "<option value='No Helper' selected>No Helper</option>";
                    // }else{
                    //     helpers = JSON.parse(decodedResponse.helpers);
                    // }

                    // else if($.inArray("No Helper", helpers)){
                    //     optionsHel += "<option value='No Helper' selected>No Helper</option>";
                    // }else{
                    //     optionsHel += "<option value='No Helper'>No Helper</option>";
                    // }

                    for(var i = 0; i < delboys.length; i++){
                        if(decodedResponse.DelAssignedTo == delboys[i].username){
                            optionsDel += "<option value='"+delboys[i].username+"' selected>"+delboys[i].username+"</option>";
                        }else{
                            optionsDel += "<option value='"+delboys[i].username+"'>"+delboys[i].username+"</option>";
                        }
                        // if(typeof response =='object')
                        // if($.inArray(delboys[i].username, helpers) > -1){
                        //     optionsHel += "<option value='"+delboys[i].username+"' selected>"+delboys[i].username+"</option>";
                        // }else{
                        //     optionsHel += "<option value='"+delboys[i].username+"'>"+delboys[i].username+"</option>";
                        // }
                    }
                    $("#taskAssignedTo").empty();
                    $("#taskAssignedTo").append(optionsDel);
                    $("#taskAssignedTo").selectpicker("render");
                    $("#taskAssignedTo").selectpicker("refresh");

                    // $("#taskHelpers").empty();
                    // $("#taskHelpers").append(optionsHel);
                    // $("#taskHelpers").selectpicker("refresh");

                    $("#updateorderid").val(decodedResponse.order_id);

                    $("#reassignModal").modal("show");
                }
            });
        }
    </script>
@endsection