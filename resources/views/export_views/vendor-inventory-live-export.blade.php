<table class="table table-hover" id="tbl_all_order">
    <thead class="thead-light">
        <tr>
            <th>Order Date</th>
            <th>Customer Name</th>
            <th>Customer Contact</th>
            <th>Product Name</th>
            <th>Vendor Name</th>
            <th>Product Rent</th>
            <th>Vendor Rent</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order_details as $key => $value)
            <tr>
                <td data-label="Order Date" class="text-nowrap">{{date('d-m-Y',strtotime($value->creation_date))}}</td>
                <td data-label="Customer">{{$value->customer_name}}</td>
                <td data-label="Contact">{{$value->primary_contact_no}}</td>
                <td data-label="Equipment">{{$value->product_name}}</td>
                <td data-label="Vendor">{{$value->vendor_name}}</td>
                <td data-label="Rent">{{$value->product_rent}}</td>
                <td data-label="Vendor Rent">
                    {{$value->vendor_rent}}
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