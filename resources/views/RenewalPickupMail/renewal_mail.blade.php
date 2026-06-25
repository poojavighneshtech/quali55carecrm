<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h1>Hi, {{ $customer_name }}</h1>
    <p>Your rental product '{{$product_name}}' renewal is approved.</p>.
    <p>Your last rental product renewal date '{{$pickup_date}}' now extends to '{{$renewal_date}}'</p>
    <p>Your product rent will remain same '&#8377; {{$product_rent}}'</p>
</body>
</html>

