<table id="mis_records" class="table table-responsive" style="width:100%">
    <thead>
        <tr class = "row_scroll">
            <th>Sr No</th>
            <th>City</th>
            <th>Date</th>
            <th>Patient Name</th>
            <th>Contact No</th>
            <th>Equipment Taken</th>
            <th>Qty</th>
            <th>Start Date</th>
            <th>Renewal Date</th>
            <th>Stop Date</th>
            <th>Status</th>
            <th>Rent per unit</th>
            <th>Deposit Taken</th>
            <th>Deposit Return</th>
            <th>Deposit Outstanding</th>
            <th>Transport</th>
            <th>Total</th>
            <th>Paid</th>
            <th>Outstanding</th>
            <th>Outstanding last year</th>
            <th>Payment Received</th>
            <th>Net Outstanding</th>
            <th>How many months</th>
            <th>Apr'{{date('Y')}}</th>
            <th>May'{{date('Y')}}</th>
            <th>Jun'{{date('Y')}}</th>
            <th>July'{{date('Y')}}</th>
            <th>Aug'{{date('Y')}}</th>
            <th>Sep'{{date('Y')}}</th>
            <th>Oct'{{date('Y')}}</th>
            <th>Nov'{{date('Y')}}</th>
            <th>Dec'{{date('Y')}}</th>
            <th>Jan'{{date('Y')}}</th>
            <th>Feb'{{date('Y')}}</th>
            <th>Mar'{{date('Y')}}</th>
            <th>No of Month</th>
            <th>Rental</th>
            <th>Rental Collected</th>
            <th>Vendor</th>
            <th>Net Rental</th>
            <th>Net Rental Outstanding</th>
            <th>Owner</th>
            <th>Address</th>
            <th>Location</th>
            <th>Lead Source</th>
            <th>Vendor Name</th>
        </tr>
    </thead>
    <tbody>
        @php $sr_no = 1; @endphp
        @foreach($mis_report_details as $key=>$mis_report_detail)
        @if(isset($mis_report_detail->payment_mode))
        <tr class = "row_scroll">
            <td>{{$mis_report_details->firstItem()+$loop->index}}</td>
            {{-- <td></td> --}}
            <td>{{$mis_report_detail->city}}</td>
            <td>{{date('d-m-Y',strtotime($mis_report_detail->date))}}</td>
            <td>{{$mis_report_detail->customer_name}}</td>
            <td>{{$mis_report_detail->contact_number}}</td>
            <td>{{$mis_report_detail->product_name}}</td>
            <td>{{$mis_report_detail->product_qty}}</td>
            <td>{{date('d-m-Y',strtotime($mis_report_detail->start_date))}}</td>
            <td>{{date('d-m-Y',strtotime($mis_report_detail->renewal_date))}}</td>
            <td>{{date('d-m-Y',strtotime($mis_report_detail->stop_date))}}</td>
            <td>{{$mis_report_detail->status}}</td>
            <td>{{$mis_report_detail->rent_per_unit}}</td>
            <td>{{$mis_report_detail->deposit_taken}}</td>
            <td>{{$pickup_data[$key]['deposite_return']}}</td>
            <td>{{$mis_report_detail->deposit_taken}}</td>
            <td>{{$mis_report_detail->transport}}</td>
            <td>{{$mis_report_detail->total}}</td>
            <td>{{$mis_report_detail->paid}}</td>
            <td>{{$mis_report_detail->outstanding}}</td>
            <td>{{$mis_report_detail->outstanding_last_year}}</td>
            <td>{{$mis_report_detail->payment_received}}</td>
            <td>{{$mis_report_detail->net_outstanding}}</td>
            <td>{{$mis_report_detail->how_many_months}}</td>
            <td>{{$mis_report_detail->apr}}</td>
            <td>{{$mis_report_detail->may}}</td>
            <td>{{$mis_report_detail->jun}}</td>
            <td>{{$mis_report_detail->july}}</td>
            <td>{{$mis_report_detail->aug}}</td>
            <td>{{$mis_report_detail->sep}}</td>
            <td>{{$mis_report_detail->oct}}</td>
            <td>{{$mis_report_detail->nov}}</td>
            <td>{{$mis_report_detail->dece}}</td>
            <td>{{$mis_report_detail->jan}}</td>
            <td>{{$mis_report_detail->feb}}</td>
            <td>{{$mis_report_detail->march}}</td>
            <td>{{$mis_report_detail->no_of_month}}</td>
            <td>{{$mis_report_detail->rental}}</td>
            <td>{{$mis_report_detail->rental_collected}}</td>
            <td>{{$mis_report_detail->vendor_rent}}</td>
            <td>{{$mis_report_detail->net_rental}}</td>
            <td>{{$mis_report_detail->net_rental_outstanding}}</td>
            {{-- <td>-0</td> --}}
            <td>{{$mis_report_detail->lead_owner}}</td>
            <td>{{$mis_report_detail->address}}</td>
            <td>{{$mis_report_detail->location}}</td>
            <td>{{$mis_report_detail->lead_source}}</td>
            <td>{{$mis_report_detail->vendor_name}}</td>
        </tr>
    @else
        <tr class = "row_scroll">
            <td>{{$mis_report_details->firstItem()+$loop->index}}</td>
            {{-- <td></td> --}}
            <td>{{$mis_report_detail->city}}</td>
            <td>{{date('d-m-Y',strtotime($mis_report_detail->date))}}</td>
            <td>{{$mis_report_detail->customer_name}}</td>
            <td>{{$mis_report_detail->contact_number}}</td>
            <td>{{$mis_report_detail->product_name}}</td>
            <td>{{$mis_report_detail->product_qty}}</td>
            <td>{{date('d-m-Y',strtotime($mis_report_detail->start_date))}}</td>
            <td>{{date('d-m-Y',strtotime($mis_report_detail->renewal_date))}}</td>
            <td>{{$pickup_data[$key]['stop_date']}}</td>
            <td>{{$pickup_data[$key]['status']}}</td>
            <td>{{$mis_report_detail->rent_per_unit}}</td>
            <td>{{$mis_report_detail->deposit_taken}}</td>
            <td>{{$pickup_data[$key]['deposite_return']}}</td>
            <td>{{$mis_report_detail->deposit_taken}}</td>
            <td>{{$mis_report_detail->transport}}</td>
            <td>{{$mis_report_detail->rent_per_unit+$mis_report_detail->deposit_taken+$mis_report_detail->transport}}</td>
            <td>{{$mis_report_detail->rent_per_unit+$mis_report_detail->deposit_taken+$mis_report_detail->transport}}</td>
            <td>NA</td>
            <td>NA</td>
            <td>NA</td>
            <td>NA</td>
            <td>NA</td>
            <td>@if(isset($renewal_data[$key]['04'])){{"1"}}@else{{"0"}}@endif</td>
            <td>@if(isset($renewal_data[$key]['05'])){{"1"}}@else{{"0"}}@endif</td>
            <td>@if(isset($renewal_data[$key]['06'])){{"1"}}@else{{"0"}}@endif</td>
            <td>@if(isset($renewal_data[$key]['07'])){{"1"}}@else{{"0"}}@endif</td>
            <td>@if(isset($renewal_data[$key]['08'])){{"1"}}@else{{"0"}}@endif</td>
            <td>@if(isset($renewal_data[$key]['09'])){{"1"}}@else{{"0"}}@endif</td>
            <td>@if(isset($renewal_data[$key]['10'])){{"1"}}@else{{"0"}}@endif</td>
            <td>@if(isset($renewal_data[$key]['11'])){{"1"}}@else{{"0"}}@endif</td>
            <td>@if(isset($renewal_data[$key]['12'])){{"1"}}@else{{"0"}}@endif</td>
            <td>@if(isset($renewal_data[$key]['01'])){{"1"}}@else{{"0"}}@endif</td>
            <td>@if(isset($renewal_data[$key]['02'])){{"1"}}@else{{"0"}}@endif</td>
            <td>@if(isset($renewal_data[$key]['03'])){{"1"}}@else{{"0"}}@endif</td>
            <td>{{count($renewal_data[$key])}}</td>
            <td>
                @php
                    $date1 = date('Y-m-d',strtotime($mis_report_detail->start_date));
                    $date2 = date('Y-m-d');

                    $ts1 = strtotime($date1);
                    $ts2 = strtotime($date2);

                    $year1 = date('Y', $ts1);
                    $year2 = date('Y', $ts2);

                    $month1 = date('m', $ts1);
                    $month2 = date('m', $ts2);

                    $diff = (($year2 - $year1) * 12) + ($month2 - $month1);
                    echo $diff * $mis_report_detail->rent_per_unit;
                @endphp
            </td>
            <td>{{($mis_report_detail->rent_per_unit * count($renewal_data[$key]))}}</td>
            <td>{{($diff * $mis_report_detail->vendor_rent)}}</td>
            <td>{{(count($renewal_data[$key]) * $mis_report_detail->rent_per_unit) - (count($renewal_data[$key]) * $mis_report_detail->vendor_rent)}}</td>
            <td>{{($diff * $mis_report_detail->rent_per_unit) - ($diff * $mis_report_detail->vendor_rent)}}</td>
            {{-- <td>-0</td> --}}
            <td>{{$mis_report_detail->owner}}</td>
            <td>{{$mis_report_detail->address_line_1.', '.$mis_report_detail->address_line_2.', '.$mis_report_detail->area.', '.$mis_report_detail->landmark.', '.$mis_report_detail->city.'- '.$mis_report_detail->pincode}}</td>
            <td>{{$mis_report_detail->location}}</td>
            <td>{{$mis_report_detail->source}}</td>
            <td>{{$mis_report_detail->vendor_name}}</td>
        </tr>
    @endif
            @php $sr_no = $sr_no++; @endphp
        @endforeach
    </tbody>
</table>