<table border="1">
    <thead>
        <tr>
            <th>Due Date</th>
            <th>Customer Name</th>
            <th>Patient Name</th>
            <th>Address</th>
            <th>Location</th>
            <th>City</th>
            <th>Contact Number</th>
            <th>Products</th>
            <th>Lead Owner</th>
            <th>Total Amount</th>
            <th>Sr. No</th>
            <th>Order ID</th>
            <th>Start Date</th>
            <th>Due Date</th>
            <th>Product Name</th>
            <th>Vendor Name</th>
            <th>Quantity</th>
            <th>Rent</th>
            <th>Deposit</th>
            <th>Due Months</th>
            <th>Total Due Rent</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($orderData as $key=>$data) 
            <tr>
                <td>{{date('d-m-Y',strtotime($data[0]->pickup_date))}}</td>
                <td>{{$data[0]->customer_name}}</td>
                <td>{{$data[0]->patient_name}}</td>
                <td>{{$data[0]->address_line_1}},{{$data[0]->address_line_2}},{{$data[0]->area}},{{$data[0]->location}},{{$data[0]->city}},{{$data[0]->pincode}}</td>
                <td>{{$data[0]->location}}</td>
                <td>{{$data[0]->city}}</td>
                <td>{{$data[0]->primary_contact_no}}</td>
                <td>{{$data->count()}}</td>
                <td>{{$data[0]->username}}</td>
                <td>{{$data->pluck('product_rent')->sum()}}</td>
                @foreach ($data as $key1=>$product)
                    @if($key1==0)
                        <td>1</td>
                        <td>{{$product->order_id}}</td>
                        <td>{{$product->DelDate}}</td>
                        <td>{{$product->pickup_date}}</td>
                        <td>{{$product->product_name}}</td>
                        <td>{{$product->vendor_name}}</td>
                        <td>{{$product->product_qty}}</td>
                        <td>{{$product->product_rent}}</td>
                        <td>{{$product->product_deposite}}</td>
                        <td>
                            @if($product->current_status == 'CustStop')
                                Stop
                            @else
                                Live
                            @endif
                        </td>
                        <td>{{$orderMonthData[$key][$key1]['month_count']}}</td>
                        <td>{{$orderMonthData[$key][$key1]['total_rent']}}</td>
                    @endif
                @endforeach
            </tr>
            @php
                $srno=1;
            @endphp
            @foreach ($data as $key1=>$product)
                @if($key1>0)
                    <tr>
                        <td>{{date('d-m-Y',strtotime($data[0]->pickup_date))}}</td>
                        <td>{{$data[0]->customer_name}}</td>
                        <td>{{$data[0]->patient_name}}</td>
                        <td>{{$data[0]->address_line_1}},{{$data[0]->address_line_2}},{{$data[0]->area}},{{$data[0]->location}},{{$data[0]->city}},{{$data[0]->pincode}}</td>
                        <td>{{$data[0]->location}}</td>
                        <td>{{$data[0]->city}}</td>
                        <td>{{$data[0]->primary_contact_no}}</td>
                        <td>{{$data->count()}}</td>
                        <td>{{$data[0]->username}}</td>
                        <td>0</td>
                        <td>{{$srno}}</td>
                        <td>{{$product->order_id}}</td>
                        <td>{{$product->DelDate}}</td>
                        <td>{{$product->pickup_date}}</td>
                        <td>{{$product->product_name}}</td>
                        <td>{{$product->vendor_name}}</td>
                        <td>{{$product->product_qty}}</td>
                        <td>{{$product->product_rent}}</td>
                        <td>{{$product->product_deposite}}</td>
                        <td>
                            @if($product->current_status == 'CustStop')
                                Stop
                            @else
                                Live
                            @endif
                        </td>
                        <td>{{$orderMonthData[$key][$key1]['month_count']}}</td>
                        <td>{{$orderMonthData[$key][$key1]['total_rent']}}</td>
                    </tr>
                @endif
                @php
                    $srno++;    
                @endphp
            @endforeach
        @endforeach
    </tbody>
</table>
                            