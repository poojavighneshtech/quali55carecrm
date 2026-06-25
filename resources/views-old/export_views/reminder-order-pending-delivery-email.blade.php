<table border="1">
    <thead>
        <tr>
            <th>Order Id</th>
            <th>Order Date</th>
            <th>Customer Name</th>
            <th>Products</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $key=>$orderData) 
            <tr>
                <td>{{$orderData->order_id}}</td>
                <td>{{$orderData->DelDate}}</td>
                <td>{{$orderData->shipping_first_name}}</td>
                <td>{{$orderData->line_item_1}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
                            