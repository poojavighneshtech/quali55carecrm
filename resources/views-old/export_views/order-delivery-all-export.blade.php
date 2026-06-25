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
            <td>{{$customers}}</td>
            <td>Rent</td>
            <td>{{$productscount['rent']}}</td>
            <td>Rent</td>
            <td>{{$amountcount['rent']}}</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td>Sale</td>
            <td>{{$productscount['sale']}}</td>
            <td>Sale</td>
            <td>{{$amountcount['sale']}}</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>Deposit</td>
            <td>{{$amountcount['deposit']}}</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>Transport</td>
            <td>{{$amountcount['transport']}}</td>
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
            <th>Amount</th>
            <th>Status</th>
            <th>Lead Source</th>
            <th>
                Lead Owner
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($get_all_leads as $key => $lead)
            <tr>
                <td>{{date('d-M-Y',strtotime($lead->creation_date)).' '. date('h:i A',strtotime($lead->converted_at))}}</td>
                <td>{{$lead->customer_name}}</td>
                <td>{{$lead->patient_name}}</td>
                <td>{{$lead->primary_contact_no}}</td>
                <td>
                    {{$lead->products_name}}
                </td>
                <td>{{$lead->location}}</td>
                <td>{{$lead->city}}</td>
                <td>{{$lead->lead_value}}</td>
                <td>
                    {{$lead->lead_status}}
                </td>
                <td>{{$lead->lead_source}}</td>
                <td>{{$lead->username}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
