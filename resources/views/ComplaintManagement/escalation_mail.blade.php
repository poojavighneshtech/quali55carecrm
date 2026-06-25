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
    <p>Below Complaint raised on <strong>{{$complaint[0]->complaint_date}}</strong> has not been attended</p>
    <p>Please take action as soon as possible</p>

    <p><strong>Customer Name :</strong> {{$getProductDetails[0]->customer_name}}</p>
    <p><strong>Customer Contact :</strong> {{$getProductDetails[0]->primary_contact_no}}</p>
    <p><strong>Raised By :</strong> {{$complaint[0]->created_by}}</p>
   <table border="1">
       <thead>
           <tr>
                <th>Order Id</th>
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
            @foreach($getProductDetails as $key=>$product)
                <tr>
                    <td>
                        {{$product->order_id}}
                    </td>
                    <td>
                        {{$product->product_name}}
                    </td>
                    <td>
                        {{$product->vendor_name}}
                    </td>
                    <td>
                        {{$complaint[$key]->delivered_by}}
                    </td>
                    <td>
                        {{$complaint[$key]->lead_owner}}
                    </td>
                </tr>
           @endforeach
       </tbody>
   </table>
   
   <Strong>Reason : </Strong>
   <p>{{$complaint[0]->remarks}}</p>
    {{-- <p>Your product rent will remain same '&#8377; {{$product_rent}}'</p> --}}
    {{-- <p>Request you to please make payment via online link at</p>
    <p>https://quali55care.com-QuickPay or https://rzp.io/l/2eDOVwr or https://paytm.me/j-Y4Ghe</p>
    <p>or Google Pay : 9930621138 or call us on 9820930915/ 9820616550</p>
    <p>Stay home stay safe.</p> --}}
</body>
</html>

