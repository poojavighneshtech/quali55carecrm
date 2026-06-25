
<table border="1">
    <thead>
        <tr>
            <th>Due Date</th>
            <th>Customer Name</th>
            <th>Address</th>
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
        @php
            $i =0;
            $srno=2;
        @endphp
        @foreach ($customer_products_details as $customer_products_detail) 
            <tr>
                <td>{{date('d-m-Y',strtotime($customer_products_detail['product_details'][0]['pickup_date']))}}</td>
                <td>{{$customer_products_detail['customer_name']}}</td>
                <td>{{$customer_products_detail['customer_address']}}</td>
                <td>{{$customer_products_detail['customer_contact_no']}}</td>
                <td>{{count($customer_products_detail['product_details'])}}</td>
                <td>{{$customer_products_detail['username']}}</td>
                <td>
                    @php
                        $product_rent = array_column($customer_products_detail['product_details'],'product_rent');
                        $total_rent = array_sum($product_rent);
                    @endphp
                    {{$total_rent}}
                </td>
                @php
                    $product_details = $customer_products_detail['product_details'];
                    $count = count($customer_products_detail['product_details']);
                @endphp
                @if($count==1)
                    @foreach($product_details as $pd)
                        <td>1</td>
                        <td>{{$pd['order_id']}}</td>
                        <td>{{$pd['DelDate']}}</td>
                        <td>{{date('d-m-Y',strtotime($pd['pickup_date']))}}</td>
                        <td>{{$pd['product_name']}}</td>
                        <td>{{$pd['vendor_name']}}</td>
                        <td>{{$pd['product_qty']}}</td>
                        <td>{{$pd['product_rent']}}</td>
                        <td>{{$pd['product_deposite']}}</td>
                        <td>{{$pd['month_count']}}</td>
                        <td>{{$pd['total_month_rent']}}</td>
                    @endforeach
                @endif
                @if($count>1)
                    <td>1</td>
                    <td>{{$product_details[0]['order_id']}}</td>
                    <td>{{$product_details[0]['DelDate']}}</td>
                    <td>{{date('d-m-Y',strtotime($product_details[0]['pickup_date']))}}</td>
                    <td>{{$product_details[0]['product_name']}}</td>
                    <td>{{$product_details[0]['vendor_name']}}</td>
                    <td>{{$product_details[0]['product_qty']}}</td>
                    <td>{{$product_details[0]['product_rent']}}</td>
                    <td>{{$product_details[0]['product_deposite']}}</td>
                    <td>{{$product_details[0]['month_count']}}</td>
                    <td>{{$product_details[0]['total_month_rent']}}</td>
                @endif
            </tr>
            @if($count>1)
                @for ($i=1; $i < $count; $i++)
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{$srno}}</td>
                        <td>{{$product_details[$i]['order_id']}}</td>
                        <td>{{$product_details[$i]['DelDate']}}</td>
                        <td>{{date('d-m-Y',strtotime($product_details[$i]['pickup_date']))}}</td>
                        <td>{{$product_details[$i]['product_name']}}</td>
                        <td>{{$product_details[$i]['vendor_name']}}</td>
                        <td>{{$product_details[$i]['product_qty']}}</td>
                        <td>{{$product_details[$i]['product_rent']}}</td>
                        <td>{{$product_details[$i]['product_deposite']}}</td>
                        <td>{{$product_details[$i]['month_count']}}</td>
                        <td>{{$product_details[$i]['total_month_rent']}}</td>
                    </tr>
                    @php
                        $srno++;
                    @endphp
                @endfor
            @endif
        @endforeach
    </tbody>
</table>
                            