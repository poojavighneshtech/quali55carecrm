<table>
    <thead>
        <tr>
            <th>Customers</th>
            <th>Count</th>
            <th>Products</th>
            <th>Count</th>
            <th>Total Amount</th>
            <th>Count</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Total Customers</td>
            <td>{{$cust_or_pay_status['total_customer']}}</td>
            <td>Rent</td>
            <td>{{$cust_or_pay_status['total_rent_product']}}</td>
            <td>Rent</td>
            <td>{{$cust_or_pay_status['total_rent_amt']}}</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td>Sale</td>
            <td>{{$cust_or_pay_status['total_sale_product']}}</td>
            <td>Sale</td>
            <td>{{$cust_or_pay_status['total_sale_amt']}}</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>Deposit</td>
            <td>{{$cust_or_pay_status['total_deposit']}}</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>Transport</td>
            <td>{{$cust_or_pay_status['total_transport']}}</td>
        </tr>
    </tbody>
</table>
<table>
    <thead class="thead">
        <tr class="text-nowrap">
            <th>
                Creation Date
            </th>
            <th>
                Customer Name
            </th>
            <th>Patient Name</th>
            <th>Mobile Number</th>
            <th>Equipment</th>
            <th>
                Location
            </th>
            <th>
                City
            </th>
            <th>Status</th>
            <th>Lead Source</th>
            <th>
                Lead Owner
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($get_all_leads as $key => $lead)
            <tr class="text-wrap">
                {{-- <td>{{$get_all_leads->firstItem()+$loop->index}}</td> --}}
                <td data-label="Creation Date">{{date('d-M-Y',strtotime($lead->creation_date)).' '. date('h:i A',strtotime($lead->converted_at))}}</td>
                <td data-label="Customer Name">{{$lead->customer_name}}</td>
                <td data-label="Patient Name">{{$lead->patient_name}}</td>
                <td data-label="Mobile Number">{{$lead->primary_contact_no}}</td>
                <td data-label="Equipment">{{$all_leads_products['data'][$key]['product_name']}}</td>
                <td data-label="Location">{{$lead->location}}</td>
                <td>{{$lead->city}}</td>
                <td data-label="Status">
                    @if($lead->lead_status=='Order Generated')
                        @if($getOrderStatuses[$lead->lead_id]['current_status']=='Pending')
                            Del Boy not Assigned
                        @else
                            {{$getOrderStatuses[$lead->lead_id]['current_status']}}
                        @endif

                    @else
                        {{$lead->lead_status}}
                    @endif
                </td>
                {{-- <td>{{$lead->priority}}</td> --}}
                <td data-label="Source">{{$lead->lead_source}}</td>
                <td data-label="Lead Owner">{{$lead->username}}</td>
            </tr>
        @endforeach
    </tbody>
</table>