<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Document</title>
        <style>
            table, td, th {
            border: 1px solid black;
            }
            
            table {
            width: 50%;
            border-collapse: collapse;
            }
            td{
                text-align: center;
            }
        </style>
    </head>
    <body>
        <p>{{$orderType}} Order - <strong>@if($orderType == 'Collection' && isset($collection_order_id)){{$collection_order_id}}@else{{$order_id}}@endif</strong></p>
        <p>Customer Name - <strong>{{$customer_name}}</strong></p>
        @if($modifiedType =='Date')
            <p>{{$orderType}} Date from <strong>{{$changedDate['from']}}</strong> changed to <strong>{{$changedDate['to']}}</strong></p>
        @endif

        @if($modifiedType =='Content')
            <p>Order Date : <strong>{{$orderDate}}</strong></p>
            @if($change == 'Product-add')
                <p>New Product Added</p>
                <table border="1">
                    <thead>
                        <th>Product Name</th>
                        <th>Rent / Sale</th>
                        <th>Deopsit</th>
                        <th>Transport</th>
                        <th>Type</th>
                        <th>Vendor</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{$insert_order['product_name']}}</td>
                            <td>{{$insert_order['product_rent']}}</td>
                            <td>{{$insert_order['product_deposite']}}</td>
                            <td>{{$insert_order['transport']}}</td>
                            <td>{{$insert_order['sale_rental']}}</td>
                            <td>{{$insert_order['vendor_name']}}</td>
                        </tr>
                    </tbody>
                </table>
            @endif

            @if($change == 'Product-update')
                <p>Product Details Updated of</p>
                <table border="1">
                    <thead>
                        <th>Product Name</th>
                        <th>Rent / Sale</th>
                        <th>Deopsit</th>
                        <th>Transport</th>
                        <th>Type</th>
                        <th>Vendor</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{$productOldData[0]->product_name}}</td>
                            <td>{{$productOldData[0]->product_rent}}</td>
                            <td>{{$productOldData[0]->product_deposite}}</td>
                            <td>{{$productOldData[0]->transport}}</td>
                            <td>{{$productOldData[0]->sale_rental}}</td>
                            <td>{{$productOldData[0]->vendor_name}}</td>
                        </tr>
                    </tbody>
                </table>
                <p>Changed to</p>
                <table border="1">
                    <thead>
                        <th>Product Name</th>
                        <th>Rent / Sale</th>
                        <th>Deopsit</th>
                        <th>Transport</th>
                        <th>Type</th>
                        <th>Vendor</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{$productOldData[0]->product_name}}</td>
                            <td>{{$request->get('product_rent')}}</td>
                            <td>{{$request->get('product_deposite')}}</td>
                            <td>{{$request->get('transport')}}</td>
                            <td>{{$productOldData[0]->sale_rental}}</td>
                            <td>{{$vendor_name}}</td>
                        </tr>
                    </tbody>
                </table>
                @if(!empty($updatedDataActivityLog['key']))
                    <p>changes of product</p>
                    <table border="1">
                        <thead>
                            <th>Change</th>
                            <th>Old</th>
                            <th>New</th>
                        </thead>
                        <tbody>
                            @foreach ($updatedDataActivityLog['key'] as $key=>$data)
                                <tr>
                                    <td>{{$data}}</td>
                                    <td>{{$updatedDataActivityLog['old_value'][$key]}}</td>
                                    <td>{{$updatedDataActivityLog['new_value'][$key]}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            @endif

            @if($change == 'Product-remove')
                <p>Product Removed</p>
                <table border="1">
                    <thead>
                        <th>Product Name</th>
                        <th>Rent / Sale</th>
                        <th>Deopsit</th>
                        <th>Transport</th>
                        <th>Type</th>
                        <th>Vendor</th>
                    </thead>
                    <tbody>
                        @foreach ($order_details as $key=>$product)
                            <tr>
                                <td>{{$product->product_name}}</td>
                                <td>{{$product->product_rent}}</td>
                                <td>{{$product->product_deposite}}</td>
                                <td>{{$product->transport}}</td>
                                <td>{{$product->sale_rental}}</td>
                                <td>{{$product->vendor_name}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @if($change == 'address-changed')
                <p>Address Changed</p>
                <p>
                    <strong>Old Address : </strong> {{$old_address}}
                </p>
                <p>
                    <strong>New Address : </strong> {{$new_address}}
                </p>
            @endif
        @endif
        <p>Order Modified By - {{$modifiedBy}}</p>
    </body>
</html>
