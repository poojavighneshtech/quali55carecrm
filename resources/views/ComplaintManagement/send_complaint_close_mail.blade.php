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
    <p>Complaint Closed for {{ $customer_name }}</p>
   {{-- <table border="1">
       <thead>
           <tr>
               <th>Product Name</th>
               <th>Vendor Name</th>
               <th>Delivered By</th>
               <th>Lead Owner</th>
           </tr>
       </thead>
       <tbody>
            @php
                //$mail_data = json_decode($mail_data);   
                //print_r($mail_data['product_name']);
            @endphp
            @for($i=0;$i< count($mail_data['product_name']); $i++)
                <tr>
                    <td>
                        {{$mail_data['product_name'][$i]}}
                    </td>
                    <td>
                        {{$mail_data['vendor_name'][$i]}}
                    </td>
                    <td>
                        {{$mail_data['delivered_by'][$i]}}
                    </td>
                    <td>
                        {{$mail_data['lead_owner'][$i]}}
                    </td>
                </tr>
           @endfor
       </tbody>
   </table> --}}
   <Strong>Reason : </Strong>
    &emsp;<p>{{$remarks}}</p>
    <Strong>Solution : </Strong>
    &emsp;<p>{{$solution}}</p>
    complaint closed by {{$sender_name}}
    {{-- <p>Your product rent will remain same '&#8377; {{$product_rent}}'</p> --}}
    {{-- <p>Request you to please make payment via online link at</p>
    <p>https://quali55care.com-QuickPay or https://rzp.io/l/2eDOVwr or https://paytm.me/j-Y4Ghe</p>
    <p>or Google Pay : 9930621138 or call us on 9820930915/ 9820616550</p>
    <p>Stay home stay safe.</p> --}}
</body>
</html>

