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
    <p>We recieved your complaint for the product..{{Collect($mail_data['product_name'])->implode(',')}}</p>
    <p>We are looking into this get back to you as soon as possible.</p>
    <p>If any queries contact on this number (<a href="tel:{{$raised_contact_no}}">{{$raised_contact_no}}</a>) {{$raised_by}}</p>
</body>
</html>

