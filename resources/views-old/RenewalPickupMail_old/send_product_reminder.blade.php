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
    <p>Dear {{ $customer_name }}</p>
    <p>Your medical equipment rent payment is due.</p>
   <table border="1">
       <thead>
           <tr>
               <th>Product Name</th>
               <th>Pickup Date</th>
               <th>Product Rent</th>
           </tr>
       </thead>
       <tbody>
            @php
                //$mail_data = json_decode($mail_data);   
                //print_r($mail_data['product_name']);
            @endphp
            @for($i=0; $i < count($mail_data['product_name']); $i++)
                <tr>
                    <td>
                        {{$mail_data['product_name'][$i]}}
                    </td>
                    <td>
                        {{date('d-m-Y',strtotime($mail_data['pickup_date'][$i]))}}
                    </td>
                    <td>
                        &#8377; {{$mail_data['product_rent'][$i]}}
                    </td>
                </tr>
           @endfor
           <tr>
               <td colspan="2">Total Amount</td>
               <td>&#8377; {{$total_rent}}</td>
           </tr>
         
       </tbody>
   </table>
    {{-- <p>Your product rent will remain same '&#8377; {{$product_rent}}'</p> --}}
    <p>Request you to please make payment via online link at</p>
    <p>https://quali55care.com-QuickPay or https://rzp.io/l/2eDOVwr or https://paytm.me/j-Y4Ghe</p>
    <p>or Google Pay : 9930621138 or call us on 9820930915/ 9820616550</p>
    <p>Stay home stay safe.</p>
</body>
</html>

