<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>

        <style type="text/css">
            .page_break {page-break-before: always;}
        </style>
    </head>
    <body>
        <table style="width:100%">
            <tr>
                <td>
                    <b>Quali55care and Consulting Pvt Limited</b>
                    <br>
                    Shop No. 6, Surya House, Vidyavihar
                    <br>
                    Station Road, Vidyavihar(E) 400077
                    <br>
                    Contact No. 9820930915 / 9820616550
                </td>
                <td style="text-align:right">
                    <img src="{{url('/')}}/assets/images/q5c_logo.png" alt="">
                </td>
            </tr>
        </table>
        <table border="1" width="100%">
            <tr>
                <td colspan="4">
                    <center><h4>DELIVERY CHALLAN</h4></center>
                </td>
            </tr>
            <tr>
                <td>
                    <b>To :</b>
                </td>
                <td>{{$customer_name}}</td>
                <td>
                    <b>Order Number :</b>
                </td>
                {{-- <td>{{$order_number}}</td> --}}
                <td>{{$lead_id}}</td>
            </tr>
            <tr>
                <td rowspan="2">
                    <b>Address :</b>
                </td>
                <td rowspan="2">{{$address}}</td>
                <td>
                    <b>Date Sent :</b>
                </td>
                <td>{{$converted_date}}</td>
            </tr>
            {{-- <tr>
                <td>
                    <b>Challan No :</b>
                </td>
                <td></td>
            </tr> --}}
            <tr>
                <td>
                    <b>Contact No :</b>
                </td>
                <td colspan="">{{$contact_no}}</td>
            </tr>
        </table>

        <table style="width:100% background-color: #ffffff; filter: alpha(opacity=40); opacity: 0.95;border:1px black solid;">
            <tr>
                <td>
                    <a href="https://quali55care.com/product/dreamstation-auto-cpap-philips-on-rent/?filter_product-type=rent&query_type_product-type=or">
                        <img src="{{url('/')}}/assets/images/DREAMSTATION-AUTO-CPAP-PHILLIPS.jpg" alt="">
                    </a>
                </td>
                <td>
                    <a href="https://quali55care.com/product/icu-5-function-electric-hospital-bed-for-rent/?filter_product-type=rent&query_type_product-type=or">
                        <img src="{{url('/')}}/assets/images/ICU-5-Function-Electric-Hospital-Bed.jpg" alt="">
                    </a>
                </td>
                <td>
                    <a href="https://quali55care.com/product/o2-concentrator-5ltr-philips-for-rent/?filter_product-type=rent&query_type_product-type=or">
                        <img src="{{url('/')}}/assets/images/O2-Concentrator-5Ltr-philisps.jpg" alt="">
                    </a>
                </td>
                <td>
                    <a href="https://quali55care.com/product/standard-wheelchair-for-rent/?filter_product-type=rent&query_type_product-type=or">
                        <img src="{{url('/')}}/assets/images/standardwheelchair.jpg" alt="">
                    </a>
                </td>
                <td>
                    <a href="https://quali55care.com/product/suction-machine-yuwell-7e-d-portable-phlegm-on-rent/?filter_product-type=rent&query_type_product-type=or">
                        <img src="{{url('/')}}/assets/images/SUCTION-MACHINE-YUWELL-7E-D-PORTABLE-PHLEGM-1.jpg" alt="">
                    </a>
                </td>
            </tr>
        </table>

        <table style="width:100%" border="1">
            <tr>
                <td colspan="4">
                    <center><h4>Summary</h4></center>
                </td>
            </tr>
            <tr>
                <td>
                    <center><b>Sr.No.</b></h4></center>
                </td>
                <td>
                    <center><b>Particulars</b></center>
                </td>
                <td>
                    <center><b>Quantity</b></center>
                </td>
                <td>
                    <center><b>Amount</b></center>
                </td>
            </tr>
            @php($sr_no = 0);
            @for($i=0; $i < count($challan_data['product_name']); $i++)
                @php($sr_no++);
                <tr>
                    <td><center>{{$sr_no}}</center></td>
                    <td>{{$challan_data['product_name'][$i]}}</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td>- @if($challan_data['sale_rental'][$i]=="Rental"){{"Rent"}}@else{{$challan_data['sale_rental'][$i]}}@endif</td>
                    <td><center>{{$challan_data['product_quantity'][$i]}}</center></td>
                    <td style="text-align:right">{{$challan_data['offered_rent'][$i]}}</td>

                </tr>
                @if($challan_data['sale_rental'][$i]=="Rental")
                    <tr>
                        <td></td>
                        <td>- Deposit</td>
                        <td><center>-</center></td>
                        <td style="text-align:right">{{$challan_data['offered_deposit'][$i]}}</td>
                    </tr>
                @endif
            @endfor 
            <tr>
                <td></td>
                <td>- Transport</td>
                <td><center>-</center></td>
                <td style="text-align:right">{{$total_transport}}</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td><center><b>Total</b></center></td>
                <td style="text-align:right"><b>{{$total_amt}}</b></td>
            </tr>
            <tr>
                <td colspan="4">Goods Received / picked up</td>
            </tr>
        </table>
        <table style="width:100%" border="1">
            <tr>
                <td>Name : {{$customer_name}}</td>
                <td>Signature :</td>
                {{-- <td><i>Dummy Sign</i></td> --}}
                <td>Date :</td>
                <td>{{$converted_date}}</td>
            </tr>
        </table>
        <table style="margin-top: -60">
            <tr>
                <td>
                        <span style="margin-top: 20">
                            T&C : Rental are monthly only( not on Daily Basis). Renewals are monthly only. Full payment at the time of delivery of before delivery online payment.<br>No refund of rent or adjustment with deposit or exchange, incase of demage, customer has to bear the charges, incase of replacement or return or cancellation of order, customer has to bear transport cost as mentioned. please visit https://Quali55care.com/terms-conditions for full rental and sales terms. <br>(If Renter fails to make any instalment pay
                        </span>
                </td>
                <td>
                    <img style="margin-top: 20" src="{{url('/')}}/assets/images/stamp.png" alt="">
                    <span>
                        Email : info@quali55care.com
                        <br>
                        www.quali55care.com
                    </span>
                </td>
            </tr>
        </table>
        {{-- <img style="float:right" src="{{url('/')}}/assets/images/stamp.png" alt="">
        <table style="width:100%">
            <tr>
                <td>Email : info@quali55care.com</td>
                <td style="text-align:right">www.quali55care.com</td>
            </tr>
        </table> --}}
        <p>This is a Computer Generated Challan</p>
    </body>
</html>