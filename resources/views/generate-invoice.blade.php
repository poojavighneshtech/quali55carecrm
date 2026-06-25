<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Tax Invoice</title>
    {{-- <link href="{{url('/')}}/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css"> --}}
    {{-- <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet"> --}}
    <!-- Custom styles for this page -->
    {{-- <link href="{{url('/')}}/assets/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet"> --}}
    
    {{-- stylesheets --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.2/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    
    <!-- Custom styles for this template-->
    {{-- <link href="{{url('/')}}/assets/css/sb-admin-2.min.css" rel="stylesheet">    --}}

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <style>
        @media print {
            body{
                width: 21cm;
                height: 29.7cm;
                
            } 
        }
        br {
            display: block;
            margin: 10px 0;
            margin-top: 10px;
        }
        table, tr, tbody, thead, th, td {
            /* border: 1px solid rgb(31, 31, 31); */
            border-top:    1px solid  rgb(31, 31, 31);
            border-left:    1px solid  rgb(31, 31, 31);
            border-right:    1px solid  rgb(31, 31, 31);
            border-bottom:    1px solid  rgb(31, 31, 31);
            /* border-collapse: collapse; */
        }
    </style>
</head>
<body>
        <center><h4>Tax Invoice</h4></center>
        {{-- <div class="table table-sm table-responsive"> --}}
            <table class="table table-sm main-table">
                <tbody>
                    <tr>
                        {{-- <td rowspan="3" colspan="3">
                            <b>Quali5care and Consulting Private Limited</b><br>
                            Office No.304 Surya House, 7th Road Rajawadi CTS Bo. 464/A<br>
                            Vidyavihar E - Mumbai, 400077<br>
                            GSTIN/UIN: 27AAACQ5737D1ZI<br>
                            State Name: Maharashtra, Code: 27<br>
                            E-Mail: care@quali55care.com
                        </td> --}}
                        <td rowspan="3" colspan="3">
                            <b>{{$data['company_name']}}</b><br>
                            {{$data['company_addr_1']}}<br>
                            {{$data['company_addr_2']}}<br>
                            GSTIN/UIN: {{$data['company_gst']}}<br>
                            State Name: {{$data['company_state']}}, Code: {{$data['company_state_code']}}<br>
                            E-Mail: care@quali55care.com
                        </td>
                        <td>
                            <span>Invoice No.</span><br>
                            <span id="invoice_no"><b>{{$data['invoice_no_format'].($data['invoice_no'])}}</b></span>
                        </td>
                        <td>
                            <span>Dated</span><br>
                            <span id="dated"><b>{{$data['order_date']}}</b></span>
                        </td>
                    </tr>
                    <tr class="text-nowrap">
                        <td>
                            <span>Delivery Note</span>
                            <span id="delivery_node"></span>
                        </td>
                        <td>
                            <span>Mode/Terms of Payment</span>
                            <span id="mode_terms_of_payment"></span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span>Supplier's Ref.</span>
                            <span id="suppliers_ref"></span>
                        </td>
                        <td>
                            <span>Other Reference(s)</span>
                            <span id="other_references"></span>
                        </td>
                    </tr>
                    <tr>
                        <td rowspan="3" colspan="3">
                            <span>Consignee</span><br>
                            <span><b>{{$data['consignee_name']}}</b></span><br>
                            <span>{{$data['consignee']}}</span><br>
                            <span>State Name: {{$data['consignee_state']}}</span>
                        </td>
                        <td>
                            <span>Buyer's Order No.</span>
                            <span id="buyers_order_no"></span>
                        </td>
                        <td>
                            <span>Dated</span>
                            <span id="buyer_dated"></span>
                        </td>
                    </tr>
                    <tr class="text-nowrap">
                        <td>
                            <span>Despatch Document No.</span>
                            <span id="despatch_document_no"></span>
                        </td>
                        <td>
                            <span>Delivery Note Date</span>
                            <span id="delivery_note_date"></span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span>Despatch through</span>
                            <span id="despatch_through"></span>
                        </td>
                        <td>
                            <span>Destination</span>
                            <span id="destination"></span>
                        </td>
                    </tr>
                    <tr>
                        <td rowspan="3" colspan="3">
                            <span>Buyer (if other than consignee)</span><br>
                            <span><b>{{$data['buyer_name']}}</b></span><br>
                            <span>{{$data['buyer']}}</span><br>
                            @if($data['gst_no'])
                                <span>GSTIN/UIN: {{$data['gst_no']}}</span><br>
                            @endif
                            <span>State Name: {{$data['buyer_state']}}</span>
                        </td>
                        <td rowspan="3" colspan="2">
                            <span>Terms of Delivery</span>
                            <span id="terms_of_delivery"></span>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="table table-sm">
                    @php
                        $qty = 0;   
                        $total_amt = 0; 
                        $bodyindex = 0;
                    @endphp
                    
                <tbody id="body{{$bodyindex}}" class="tablebody">
                    <tr class="text-center">
                        <td rowspan="2">Sr.<br>No</td>
                        <td rowspan="2">Description of Good and Services</td>
                        <td rowspan="2">HSN/SAC</td>
                        <td rowspan="2">Qty</td>
                        {{-- <td rowspan="2">MRP</td> --}}
                        <td rowspan="2">Rate</td>
                        <td rowspan="2">per</td>
                        <td rowspan="2">Disc. %</td>
                        <td rowspan="2">Taxable Amount</td>
                        @if($data['state_code'] == 27)
                            <td colspan="2">CGST</td>
                            <td colspan="2">SGST</td>
                        @else
                            <td colspan="2">IGST</td>
                        @endif
                        <td rowspan="2">Amount</td>
                    </tr>
                    <tr>
                        @if($data['state_code'] == 27)
                            <td>Rate</td>
                            <td>Amount</td>
                            <td>Rate</td>
                            <td>Amount</td>
                        @else
                            <td>Rate</td>
                            <td>Amount</td>
                        @endif
                    </tr>
                    @foreach($product_details as $key=>$value)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>
                                @if($value['sale_rental'] == 'Rental')
                                    
                                    <span><b><i>{{$value['product_name']}}</i></b></span><span><small>@if($value['inventory_id'] != 0) -({{$value['inventory_id']}})@endif</small></span><br>
                                    @if($invoice_type == 'Delivery')
                                        <span style="text-align:left;">
                                            {{-- Rental Charges @ {{$value['gst_rent']}}% --}}
                                            @if($value['product_deposit']!=0)
                                                <b>Deposit</b>
                                            @endif
                                            @if($value['order_id'] != 0)
                                                <span style="float:right;">
                                                    O.No: {{$value['order_id']}}
                                                </span>
                                            @endif
                                        </span><br>
                                    @else
                                        <span style="text-align:left;">
                                            {{-- Renewal Charges @ {{$value['gst_rent']}}% --}}
                                            <span style="float:right;">
                                                O.No: {{$value['order_id']}}
                                            </span>
                                        </span><br>
                                    @endif
                                    <span>
                                        @if($invoice_type == 'Delivery')
                                            <small>{{date('d-m-Y',strtotime($value['creation_date']))}} to {{date('d-m-Y',strtotime($value['pickup_date']))}}</small>
                                        @else
                                            <small>{{date('d-m-Y',strtotime($value['start_date']))}} to {{date('d-m-Y',strtotime($value['end_date']))}}</small>
                                        @endif
                                    </span><br>
                                    
                                @elseif($value['sale_rental'] == 'Sale')
                                    <span style="text-align:left;">
                                        <b><i>{{$value['product_name']}}</i></b>
                                        @if($value['order_id'] != 0)
                                            <span style="float:right;">
                                                O.No: {{$value['order_id']}}
                                            </span>
                                        @endif
                                    </span><br>
                                    {{-- <b><i>{{$value['product_name']}}</i></b><br> --}}
                                    {{-- <span>{{$value['product_name']}}</span><br> --}}
                                @else

                                @endif
                            </td>
                            <td>
                                @if($value['sale_rental'] == 'Rental')
                                    {{$value['hsn_sac_rent']}}
                                @else
                                    {{$value['hsn_sac_sale']}}
                                @endif
                            </td>
                            <td>
                                @if($invoice_type == 'Delivery')
                                    <b>{{$value['product_qty']}} @php $qty = $qty + $value['product_qty']; @endphp</b>
                                @else
                                    <b>{{$value['product_qty']}}</b>
                                @endif
                            </td>
                            {{-- <td>
                                @if($invoice_type == 'Delivery')
                                    <b>{{$value['product_rent']/$value['product_qty']}}</b>
                                @else
                                    <b>{{$value['product_rent']}}</b>
                                @endif
                            </td> --}}
                            <td class="text-right">
                                @if($invoice_type == 'Delivery')
                                    @if($value['sale_rental'] == 'Rental')
                                        {{$value['product_rate']}}
                                    @else
                                        {{-- {{($value['product_rent']/$value['product_qty']) - (($value['product_rent']/$value['product_qty'])*100/$value['gst_rent']+100)}} --}}
                                        {{$value['product_rate']}}
                                    @endif
                                @else
                                    {{$value['product_rate']}}
                                @endif
                            </td>
                            <td>
                                @if($invoice_type == 'Delivery')
                                    @if(isset($value['unit']))
                                        num<br>{{$value['unit']}}
                                    @endif
                                @else
                                    @if(isset($value['unit']))
                                        num<br>{{$value['unit']}}
                                    @endif
                                @endif
                            </td>
                            <td></td>
                            <td class="text-right">{{number_format($value['amount_cal'],2)}}</td>
                            @if($data['state_code'] == 27)
                                <td class="text-right">{{number_format($value['ct_rate'],2)}}%</td>
                                <td class="text-right">{{number_format($value['ct_amount'],2)}}</td>
                                <td class="text-right">{{number_format($value['st_rate'],2)}}%</td>
                                <td class="text-right">{{number_format($value['st_amount'],2)}}</td>
                            @else
                                <td class="text-right">{{number_format($value['i_rate'],2)}}%</td>
                                <td class="text-right">{{number_format($value['i_amount'],2)}}</td>
                            @endif
                            <td class="text-right">
                                @if($value['sale_rental'] == 'Rental')
                                    @php
                                        $total_amt = $total_amt +  $value['amount_cal'] + $value['ct_amount'] + $value['st_amount'];
                                    @endphp
                                    {{number_format($value['amount_cal'] + $value['ct_amount'] + $value['st_amount'],2)}}<br><br>
                                    @if($value['product_deposit']!=0)
                                        @php
                                            $total_amt = $total_amt + $value['product_deposit'];
                                        @endphp
                                        {{number_format($value['product_deposit'],2)}}<br>
                                    @endif

                                @else
                                    @php
                                        $total_amt = $total_amt +  $value['amount_cal'] + $value['ct_amount'] + $value['st_amount'];
                                        // $total_amt = $total_amt +  floatval(preg_replace('/[^\d.]/', '', number_format(((($value['product_rent'])/($value['gst_sale']+100))*100),2)));
                                    @endphp
                                    {{-- {{number_format(((($value['product_rent'])/($value['gst_sale']+100))*100),2)}} --}}
                                    {{number_format($value['amount_cal'] + $value['ct_amount'] + $value['st_amount'],2)}}<br><br>
                                @endif
                            </td>
                        </tr>
                        @if($key !=0 && $key % 2 == 0)
                        @php $bodyindex++; @endphp
                            <tr>
                                <td colspan="@if($data['state_code']==27){{'14'}}@else{{'12'}}@endif" class="continued" id="{{$bodyindex}}">
                                    <span style="float:right;">
                                        Continued ...
                                    </span>
                                </td>
                            </tr>
                            </tbody>
                            </table>
                            <span><center>This is Computer Generated Invoice</center></span>
                            <div class="" id="between{{$bodyindex}}"></div>
                            <center><h4>Tax Invoice(Page {{$key/2 + 1}})</h4></center>
                            <table class="table table-sm">
                                <tbody id="body{{$bodyindex}}" class="tablebody">
                                    <tr>
                                        {{-- <td rowspan="3" colspan="3">
                                            <b>Quali5care and Consulting Private Limited</b><br>
                                            508-509, 5th floor Surya House, 7th Road Rajawadi CTS Bo. 464/A<br>
                                            Vidyavihar E - Mumbai, 400077<br>
                                            GSTIN/UIN: 27AAACQ5737D1ZI<br>
                                            State Name: Maharashtra, Code: 27<br>
                                            E-Mail: care@quali55care.com
                                        </td> --}}
                                        <td rowspan="3" colspan="3">
                                            <b>{{$data['company_name']}}</b><br>
                                            {{$data['company_addr_1']}}<br>
                                            {{$data['company_addr_2']}}<br>
                                            GSTIN/UIN: {{$data['company_gst']}}<br>
                                            State Name: {{$data['company_state']}}, Code: {{$data['company_state_code']}}<br>
                                            E-Mail: care@quali55care.com
                                        </td>
                                        <td>
                                            <span>Invoice No.</span><br>
                                            <span id="invoice_no"><b>{{$data['invoice_no_format'].($data['invoice_no']+1)}}</b></span>
                                        </td>
                                        <td>
                                            <span>Dated</span><br>
                                            <span id="dated"><b>{{$data['order_date']}}</b></span>
                                        </td>
                                    </tr>
                                    <tr class="text-nowrap">
                                        <td>
                                            <span>Delivery Note</span>
                                            <span id="delivery_node"></span>
                                        </td>
                                        <td>
                                            <span>Mode/Terms of Payment</span>
                                            <span id="mode_terms_of_payment"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span>Supplier's Ref.</span>
                                            <span id="suppliers_ref"></span>
                                        </td>
                                        <td>
                                            <span>Other Reference(s)</span>
                                            <span id="other_references"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td rowspan="3" colspan="3">
                                            <span>Consignee</span><br>
                                            <span><b>{{$data['consignee_name']}}</b></span><br>
                                            <span>{{$data['consignee']}}</span>
                                        </td>
                                        <td>
                                            <span>Buyer's Order No.</span>
                                            <span id="buyers_order_no"></span>
                                        </td>
                                        <td>
                                            <span>Dated</span>
                                            <span id="buyer_dated"></span>
                                        </td>
                                    </tr>
                                    <tr class="text-nowrap">
                                        <td>
                                            <span>Despatch Document No.</span>
                                            <span id="despatch_document_no"></span>
                                        </td>
                                        <td>
                                            <span>Delivery Note Date</span>
                                            <span id="delivery_note_date"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span>Despatch through</span>
                                            <span id="despatch_through"></span>
                                        </td>
                                        <td>
                                            <span>Destination</span>
                                            <span id="destination"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td rowspan="3" colspan="3">
                                            <span>Buyer (if other than consignee)</span><br>
                                            <span><b>{{$data['buyer_name']}}</b></span><br>
                                            <span>{{$data['buyer']}}</span><br>
                                            @if($data['gst_no'])
                                                <span>GSTIN/UIN: {{$data['gst_no']}}</span><br>
                                            @endif
                                            <span>State Name: {{$data['buyer_state']}}</span>
                                        </td>
                                        <td rowspan="3" colspan="2">
                                            <span>Terms of Delivery</span>
                                            <span id="terms_of_delivery"></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table table-sm">
                                <tbody>
                                    <tr class="text-center">
                                        <td rowspan="2">Sr.<br>No</td>
                                        <td rowspan="2">Description of Good and Services</td>
                                        <td rowspan="2">HSN/SAC</td>
                                        <td rowspan="2">Qty</td>
                                        {{-- <td rowspan="2">MRP</td> --}}
                                        <td rowspan="2">Rate</td>
                                        <td rowspan="2">per</td>
                                        <td rowspan="2">Disc. %</td>
                                        <td rowspan="2">Taxable Amount</td>
                                        {{-- <td colspan="2">CGST</td>
                                        <td colspan="2">SGST</td> --}}
                                        @if($data['state_code'] == 27)
                                            <td colspan="2">CGST</td>
                                            <td colspan="2">SGST</td>
                                        @else
                                            <td colspan="2">IGST</td>
                                        @endif
                                        <td rowspan="2">Amount</td>
                                    </tr>
                                    <tr>
                                        <td>Rate</td>
                                        <td>Amount</td>
                                        <td>Rate</td>
                                        <td>Amount</td>
                                    </tr>
                        @endif
                    @endforeach
                    @if($invoice_type == 'Delivery' && $data['transport_cost'] != 0)
                        <tr>
                            <td></td>
                            <td>
                                @if($invoice_type == 'Delivery')
                                    <span><b>Transport Charges</b></span>
                                @endif
                            </td>
                            <td>
                                @if($invoice_type == 'Delivery')
                                    {{$data['transport_hsn']}}
                                @endif
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>                            
                            <td class="text-right">{{number_format($data['transport_cal']['taxable_value'],2)}}</td>
                            @if($data['state_code'] == 27)
                                <td class="text-right">{{number_format($data['max_gst']/2,2)}}%</td>
                                <td class="text-right">{{number_format($data['transport_cal']['ct_amount'],2)}}</td>
                                <td class="text-right">{{number_format($data['max_gst']/2,2)}}%</td>
                                <td class="text-right">{{number_format($data['transport_cal']['st_amount'],2)}}</td>
                            @else
                                <td class="text-right">{{number_format($data['max_gst'],2)}}%</td>
                                <td class="text-right">{{number_format($data['transport_cal']['i_amount'],2)}}</td>
                            @endif
                            <td class="text-right">
                                {{number_format($data['transport_cal']['taxable_value'] + $data['transport_cal']['ct_amount'] + $data['transport_cal']['st_amount'],2)}}
                                @php
                                    $total_amt = $total_amt + $data['transport_cal']['taxable_value'] + $data['transport_cal']['ct_amount'] + $data['transport_cal']['st_amount'];
                                @endphp
                                {{-- {{(($data['transport_cost'])/($data['max_gst']100))}} --}}
                            </td>
                        </tr>       
                    @endif
                    @if($invoice_type == 'Delivery' && $data['labour'] != 0)
                        <tr>
                            <td></td>
                            <td>
                                @if($invoice_type == 'Delivery')
                                    <span><b>Labour Charges</b></span>
                                @endif
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>                            
                            <td class="text-right"></td>
                            @if($data['state_code'] == 27)
                                <td class="text-right"></td>
                                <td class="text-right"></td>
                                <td class="text-right"></td>
                                <td class="text-right"></td>
                            @else
                                <td class="text-right"></td>
                                <td class="text-right"></td>
                            @endif
                            <td class="text-right">
                                {{number_format($data['labour'],2)}}
                                @php
                                    $total_amt = $total_amt + $data['labour'];
                                @endphp
                                {{-- {{(($data['transport_cost'])/($data['max_gst']100))}} --}}
                            </td>
                        </tr>
                    @endif
                    {{-- <tr>
                        <td></td>
                        <td>
                           <span style="float:right;"><b>Output - CGST</b></span>
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right">
                            @php
                                $total_amt = $total_amt +  floatval(preg_replace('/[^\d.]/', '', number_format($data['total_central_tax'],2)));
                            @endphp
                            {{number_format($data['total_central_tax'],2)}}</td>
                    </tr> --}}
                    {{-- <tr>
                        <td></td>
                        <td>
                           <span style="float:right;"><b>Output - SGST</b></span>
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right">
                            @php
                                $total_amt = $total_amt +  floatval(preg_replace('/[^\d.]/', '', number_format($data['total_state_tax'],2)));
                            @endphp
                            {{number_format($data['total_state_tax'],2)}}</td>
                    </tr> --}}
                    <tr>
                        <td></td>
                        <td>
                           <span style="float:right;"><b>Round Off</b></span>
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        @if($data['state_code'] == 27)
                            <td></td>
                            <td></td>
                        @endif
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>                        
                        <td class='text-right'>{{number_format($data['total_amount_no'] - $total_amt,2)}}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                           <span style="float:right;"><b>Total</b></span>
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        @if($data['state_code'] == 27)
                            <td></td>
                            <td></td>
                        @endif
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class='text-right'><b>{{number_format($data['total_amount_no'],2)}}</b></td>
                    </tr>
                    <tr>
                        <td colspan="13">
                            Amount Chargeable (in words)<br>
                            <b>INR {{ucfirst($data['total_amount'])}}</b>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" rowspan="2" class="text-center"><span>HSN/SAC</span></td>
                        
                        @if($data['state_code'] == 27)
                            <td colspan="1" rowspan="2" class="text-center"><span>Taxable Value</span></td>
                            <td colspan="4" class="text-center"><span>Central Tax</span></td>
                            <td colspan="4" class="text-center"><span>State Tax</span></td>
                            <td colspan="2" rowspan="2" class="text-center"><span>Total Tax Amount</span></td>
                        @else
                            <td colspan="2" rowspan="2" class="text-center"><span>Taxable Value</span></td>
                            <td colspan="4" class="text-center"><span>Integrated Tax</span></td>
                            <td colspan="3" rowspan="2" class="text-center"><span>Total Tax Amount</span></td>
                        @endif
                    </tr>
                    <tr>
                        <td colspan="2" class="text-center">Rate</td>
                        <td colspan="2" class="text-center">Amount</td>
                        @if($data['state_code'] == 27)
                            <td colspan="2" class="text-center">Rate</td>
                            <td colspan="2" class="text-center">Amount</td>
                        @endif
                    </tr>
                    @foreach($hsn_code_details as $k=>$v)
                        <tr>
                            <td colspan="2">{{$v['hsn_sac']}}</td>
                            
                            @if($data['state_code'] == 27)
                                <td colspan="1" class="text-right">{{ number_format($v['taxable_value'],2)}}</td>
                                <td colspan="2" class="text-right">{{ number_format($v['ct_rate'],2)}}%</td>
                                <td colspan="2" class="text-right">{{ number_format($v['ct_amount'],2)}}</td>
                                <td colspan="2" class="text-right">{{ number_format($v['st_rate'],2)}}%</td>
                                <td colspan="2" class="text-right">{{ number_format($v['st_amount'],2)}}</td>
                                <td colspan="2" class="text-right">{{ number_format($v['ct_amount'] + $v['st_amount'],2)}}</td>
                            @else
                                <td colspan="2" class="text-right">{{ number_format($v['taxable_value'],2)}}</td>
                                <td colspan="2" class="text-right">{{ number_format($v['i_rate'],2)}}%</td>
                                <td colspan="2" class="text-right">{{ number_format($v['i_amount'],2)}}</td>
                                <td colspan="3" class="text-right">{{ number_format($v['ct_amount'] + $v['st_amount'],2)}}</td>
                            @endif
                            
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2"><span style="float:right;">Total</span></td>
                        @if($data['state_code'] == 27)
                            <td colspan="1" class="text-right">{{ number_format($data['total_taxable_value'],2)}}</td>
                            <td colspan="2"></td>
                            <td colspan="2" class="text-right">{{ number_format($data['total_central_tax'],2)}}</td>
                            <td colspan="2"></td>
                            <td colspan="2" class="text-right">{{ number_format($data['total_state_tax'],2)}}</td>
                            <td colspan="2" class="text-right">{{ number_format($data['total_tax_amount'],2)}}</td>
                        @else
                            <td colspan="2" class="text-right">{{ number_format($data['total_taxable_value'],2)}}</td>
                            <td colspan="2"></td>
                            <td colspan="2" class="text-right">{{ number_format($data['total_i_tax'],2)}}</td>
                            <td colspan="3" class="text-right">{{ number_format($data['total_tax_amount'],2)}}</td>
                        @endif
                    </tr>
                    <tr>
                        <td colspan="13"><small>Tax Amount (in words):&emsp;&emsp;&emsp;</small><b>INR {{$data['total_tax_amount_word']}}</b></td>
                    </tr>
                    <tr>
                        <td colspan="6" rowspan="3">
                            <span>Company's PAN: {{$data['pan_no']}}</span><br>
                            <span><small>Declaration</small></span><br>
                            <div style="text-align: justify; text-justify: inter-word;">T&C : Rental are monthly only ( not on Daily Basis)
                                Renewals are monthly only. full payment at the time
                                of delivery of before delivery online payment. No
                                refund of rent or adjustment with deposit or exchange. 
                                incase of demage, customer has to bear the charges.
                                incase of replacement or return or cancellation of
                                order, customer has to bear transport cost as
                                mentioned. please visit <a href="https://quali55care.com/tabs/termscondition" target="_blank">https://quali55care.com/tabs/termscondition</a> for full rental and sales terms. (if 
                                Renter fails to make any instalment pay
                            </div>
                        </td>
                        <td colspan="8" rowspan="3">
                            <span>Company's Bank Details</span><br>
                            <span>Bank Name          : {{$data['bank_name']}}</span><br>
                            <span>A/c No             : {{$data['account_no']}}</span><br>
                            <span>Branch & IFSC Code : {{$data['branch'].'& '.$data['ifsc_code']}}</span><br>
                            <span>for Quali5care and Consulting Pvivate Limited</span><br>
                            <br>
                            <div class="row">
                                <div class="col-md-6">
                                    <img src="{{url('/')}}/assets/images/Stamp.png" width="65%" height="65%" alt="Stamp" srcset="">
                                </div>
                                {{-- <div class="col-md-4">
                                    
                                </div> --}}
                                <div class="col-md-6">
                                    <img src="{{url('/')}}/assets/images/Sign.png" width="65%" height="65%" alt="Sign" srcset="">        
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <span><center>This is Computer Generated Invoice</center></span>
        {{-- </div> --}}

{{-- <!-- Bootstrap core JavaScript-->
<script src="{{url('/')}}/assets/vendor/jquery/jquery.min.js"></script>
<script src="{{url('/')}}/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="{{url('/')}}/assets/vendor/jquery/jquery.cookie.js" type="text/javascript"></script>

<!-- Core plugin JavaScript-->
<script src="{{url('/')}}/assets/vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="{{url('/')}}/assets/js/sb-admin-2.min.js"></script>

<!-- Page level plugins -->
<script src="{{url('/')}}/assets/vendor/chart.js/Chart.min.js"></script>

<!-- Page level custom scripts -->
<script src="{{url('/')}}/assets/js/demo/chart-area-demo.js"></script>
<script src="{{url('/')}}/assets/js/demo/chart-pie-demo.js"></script>

<!-- Page level plugins -->
<script src="<?php echo url('/');?>/assets/vendor/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo url('/');?>/assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>

{{-- Scripts --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{url('/')}}/assets/js/jquery.table2excel.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js" ></script>
<script src="{{url('/')}}/assets/dist/clipboard.min.js"></script> --}}

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

<script>
    $(document).ready(function(){
        // console.log("a");
        if($(".continued")[0])
        {
            // console.log("a");
            $.each($(".continued"),function(index, value){
                // console.log(value.id);
                let height_inner = $("#body"+value.id).innerHeight();
                let height_main = $(".main-table").innerHeight();
                console.log(parseInt(height_inner + height_main));
                let remaining = parseInt(935 - (height_inner + height_main));
                // console.log(remaining);
                let rounded = Math.round(remaining/8);
                for(let i=0; i<rounded; i++)
                {
                    $("#"+value.id).append("<br>");
                }
                height_inner = $("#body"+value.id).innerHeight();
                height_main = $(".main-table").innerHeight();
                // console.log(parseInt(height_inner + height_main));
                remaining = parseInt(935 - (height_inner + height_main));
                // $("#between"+value.id).css("margin-top",remaining);
                // rounded = Math.round(remaining/14);
                // console.log(rounded);
                // for(let i=0; i<rounded; i++)
                // {
                //     $("#between"+value.id).append("<br>");
                // }
            });
        }

        window.print();
    });
</script>

</body>
</html>