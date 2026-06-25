<table>
    <thead>
        <tr>
            <th>Sr.No.</th>
            <th>Date</th>
            <th>Product Name</th>
            <th>Qty</th>
            <th>City</th>
            <th>Sale/Rental</th>
            <th>Product Rent</th>
            <th>Product Deposit</th>
        </tr>
    </thead>
    <tbody>
        @foreach($details_count as $key=>$value)
            <tr>
                <td>{{$key+1}}</td>
                <td>{{$value['DelDate']}}</td>
                <td>{{$value['product_name']}}</td>
                <td>{{$value['qty']}}</td>
                <td>{{implode(',',$value['cities'])}}</td>
                <td>{{$value['sale_rental']}}</td>
                <td>{{$value['product_rent']}}</td>
                <td>{{$value['product_deposite']}}</td>
            </tr>
        @endforeach
    </tbody>
</table>