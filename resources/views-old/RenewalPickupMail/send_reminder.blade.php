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
    <p>Dear <b>{{ $customer_name }}</b></p>
    <p>Your medical equipment rent payment is due.</p>
    <table border="1">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Pickup Date</th>
                <th>Product Rent</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{$product_name}}</td>
                <td>{{date('d-m-Y',strtotime($pickup_date))}}</td>
                <td>{{$product_rent}}</td>
                <td>{{$product_rent}}</td>
            </tr>
        </tbody>
    </table>

    <p>Request you to please make payment via online link at</p>
    <p>https://quali55care.com-QuickPay or https://rzp.io/l/2eDOVwr or https://paytm.me/j-Y4Ghe</p>
    <p>or Google Pay : 9930621138 or call us on 9820930915/ 9820616550</p>
    <p>Stay home stay safe.</p>
</body>
</html>

