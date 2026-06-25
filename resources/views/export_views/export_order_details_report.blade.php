<table class="table table-hover" id="tbl_all_order">
    <thead class="thead-light">
        <tr>
            {{-- <th>Sr. No</th> --}}
            <th>Order Date</th>
            <th>Created On</th>
            <th>Order MM/YY</th>
            <th>Order Id</th>
            <th>Product Name</th>
            <th>Vendor Name</th>
            <th>Customer Name</th>
            <th>Patient Name</th>
            <th>Customer Contact</th>
            <th>Address</th>
            <th>Vendor Rent</th>
            <th>Product Rent</th>
            <th>Product Deposit</th>
            <th>Transport</th>
            <th>Product Type</th>
            <th>Status</th>
            <th>Stop Date</th>
            <th>Days</th>
            <th>RenewalMonth</th>
            <th>City</th>
            <th>customer_id</th>
            <th>Source</th>
            <th>Lead Type</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order_details as $key => $value)
            <tr>
                {{-- <td>{{$order_details->firstItem()+$loop->index}}</td> --}}
                <td class="text-nowrap">{{date('d-m-Y',strtotime($value->creation_date))}}</td>
                <td class="text-nowrap">{{date('d-M-y h:i',strtotime($value->created_at))}}</td>
                <td class="text-nowrap">{{date('m-Y',strtotime($value->creation_date))}}</td>
                <td>{{$value->order_id}}</td>
                <td>{{$value->product_name}}</td>
                <td>{{$value->vendor_name}}</td>
                <td>{{$value->customer_name}}</td>
                <td>{{$value->patient_name}}</td>
                <td>{{$value->primary_contact_no}}</td>
                <td>{{$value->fulldetails}}</td>
                <td>{{$value->vendor_rent}}</td>
                <td>{{$value->product_rent}}</td>
                <td>{{$value->product_deposite}}</td>
                <td>{{$value->transport}}</td>
                <td>{{$value->sale_rental}}</td>
                <td>
                    @if($value->current_status == 'Pending Pickup' || $value->current_status == 'Picked Up' || $value->current_status == 'Picked UP' || $value->current_status == 'Pickuped')
                        <span class="badge badge-danger">Stop</span>
                    @elseif($value->sale_rental == 'Sale')
                        <span class="badge badge-success">Sold</span>
                    @else
                        <span class="badge badge-success">Live @if($value->current_status == 'CustStop'){{"(Stop Req.)"}}@endif</span>
                    @endif
                </td>
                <td>
                    @if($value->current_status == 'Pending Pickup' || $value->current_status == 'Picked Up' || $value->current_status == 'Picked UP' || $value->current_status == 'Pickuped')
                        {{date('d-m-Y',strtotime($value->pickup_date_stopped))}}
                    @else
                        -
                    @endif
                </td>
                <td>
                    {{-- @if($value->current_status == 'Pending Pickup' || $value->current_status == 'Picked Up' || $value->current_status == 'Picked UP' || $value->current_status == 'Pickuped') --}}
                        {{$value->days_count}}
                    {{-- @else --}}
                        {{-- - --}}
                    {{-- @endif --}}
                </td>
                <td>
                    {{$value->months_count}}
                </td>             
                <td>{{$value->city}}</td>
                <td>{{$value->customer_id}}</td>
                <td>{{$value->lead_source}}</td>
                <td>{{$value->leadtype}}</td>
            </tr>
        @endforeach
        <tr></tr>
        <tr></tr>
        <tr>
            <td>Order Count</td>
            <td>@if(isset($count)){{$count['orders_count']}}@else{{0}}@endif</td>
        </tr>
        <tr>
            <td>Vendor Rent</td>
            <td>@if(isset($count)){{$count['vendor_rent_count']}}@else{{0}}@endif</td>
        </tr>
        <tr>
            <td>Order Sale</td>
            <td>@if(isset($count)){{$count['order_sale_count']}}@else{{0}}@endif</td>
        </tr>
        <tr>
            <td>Order Rent</td>
            <td>@if(isset($count)){{$count['order_rent_count']}}@else{{0}}@endif</td>
        </tr>
        <tr>
            <td>Order Deposit</td>
            <td>@if(isset($count)){{$count['order_deposite_count']}}@else{{0}}@endif</td>
        </tr>
        <tr>
            <td>Order Transport</td>
            <td>@if(isset($count)){{$count['order_transport_count']}}@else{{0}}@endif</td>
        </tr>
        <tr>
            <td>Equipment</td>
            <td>@if(isset($count)){{$count['equipment_count']}}@else{{0}}@endif</td>
        </tr>
    </tbody>
</table>