<table class="table table-stripped">
    <tbody>
        <tr>
            <th>Month</th>
            @foreach ($month_data as $key=>$value)
                <th>{{$value['month']}}</th>
            @endforeach
        </tr>
        <tr>
            <th>Total Revenue</th>
            @foreach ($month_data as $key=>$value)
                <td>{{$value['total_revenue']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>Gross Earning total</th>
            @foreach ($month_data as $key=>$value)
                <td>{{$value['gross_earning_total']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>Total Rent</th>
            @foreach ($month_data as $key=>$value)
                <td>{{$value['total_rent']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>Total Rent Collected</th>
            @foreach ($month_data as $key=>$value)
                <td>{{$value['total_rent_collected']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>Due Rent</th>
            @foreach ($month_data as $key=>$value)
                <td>{{$value['due_rent']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>% growth over last month</th>
            @foreach ($month_data as $key=>$value)
                <td>{{$value['total_growth_over_last_month']}}%</td>
            @endforeach
        </tr>
        <tr>
            <th>Total Unit Rented</th>
            @foreach ($month_data as $key=>$value)
                <td>{{$value['total_unit_rented']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>Total Customer Served(Rental)</th>
            @foreach ($month_data as $key=>$value)
            <td>{{$value['total_customer_served_rental'] + $value['total_renewed_customer']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>New Rent Collected</th>
            @foreach ($month_data as $key=>$value)
                <td>{{$value['new_rent_collected']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>Deposite Collected</th>
            @foreach ($month_data as $key=>$value)
                <td>{{$value['new_rent_deposite']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>New Unit Rented</th>
            @foreach ($month_data as $key=>$value)
                <td>{{$value['new_unit_rented']}}</td>
            @endforeach
        </tr>
        {{-- <tr>
            <th>New Customer(Rental)</th>
            @foreach ($month_data as $key=>$value)
                <td>{{$value['total_customer_served_rental']}}</td>
            @endforeach
        </tr> --}}
        <tr>
            <th>New Customer(Rental-Online)</th>
            @foreach ($month_data as $key=>$value)
                <td>{{$value['new_customer_rented_online']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>New Customer(Rental-Offline)</th>
            @foreach ($month_data as $key=>$value)
                <td>{{$value['new_customer_rented_offline']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>New Customer(Rental-Corporate)</th>
            @foreach ($month_data as $key=>$value)
                <td>{{$value['new_customer_rented_corporate']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>Customer growth over last month</th>
            @foreach ($month_data as $key=>$value)
                <td>{{$value['new_growth_over_last_month']}}%</td>
            @endforeach
        </tr>
        <tr>
            <th>Value added service</th>
            @foreach ($month_data as $key=>$value)
                <td>{{$value['value_added_service']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>Renewal rent collected</th>
            @foreach ($month_data as $key=>$value)
                <td>{{$value['renewal_rent_collected']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>Renewed Customers</th>
            @foreach ($month_data as $key=>$value)
                <td>{{$value['total_renewed_customer']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>Renewal count of equipment</th>
            @foreach ($month_data as $key=>$value)
                <td>{{$value['renewal_count_of_equipment']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>Renewal %</th>
            @foreach ($month_data as $key=>$value)
            <td>{{$value['renewal_%']}}%</td>
            @endforeach
        </tr>
        <tr>
            <th>Earning from vendor equipment</th>
            @foreach ($month_data as $key=>$value)
            <td>{{$value['vendor_equipment_renewal'] + $value['vendor_equipment_rent']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>Vendor Payment</th>
            @foreach ($month_data as $key=>$value)
            <td>{{$value['vendor_payment_no_q5c_rent']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>Net Earning from vendor equipment</th>
            @foreach ($month_data as $key=>$value)
            <td>{{$value['net_earning_from_vendor_equipment']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>% of vendor net rental Earning</th>
            @foreach ($month_data as $key=>$value)
            <td>{{$value['%_of_vendor_net_rental_earning']}}%</td>
            @endforeach
        </tr>
        <tr>
            <th>Net Rental Earning</th>
            @foreach ($month_data as $key=>$value)
            <td>{{$value['net_rental_earning']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>Gross Net Rental Earning</th>
            @foreach ($month_data as $key=>$value)
            <td>{{$value['gross_net_rental_earning']}}%</td>
            @endforeach
        </tr>
        <tr>
            <th>Rental Transportation</th>
            @foreach ($month_data as $key=>$value)
            <td>{{$value['rental_transportation']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>Transportation Expense</th>
            @foreach ($month_data as $key=>$value)
            <td>{{$value['transportation_expense']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>Total Expense</th>
            @foreach ($month_data as $key=>$value)
            <td>{{$value['total_expense']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>Google spend Marketing</th>
            @foreach ($month_data as $key=>$value)
            <td>{{$value['google_spend_marketing']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>No of clicks</th>
            @foreach ($month_data as $key=>$value)
            <td>{{$value['no_of_clicks']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>Impression</th>
            @foreach ($month_data as $key=>$value)
            <td>{{$value['impressions']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>Conversion Ratio</th>
            @foreach ($month_data as $key=>$value)
            <td>{{$value['conversion_ratio']}}%</td>
            @endforeach
        </tr>
        <tr>
            <th>New customer acquition cost</th>
            @foreach ($month_data as $key=>$value)
            <td>{{$value['new_customer_aquition_cost']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>All customer acquition cost</th>
            @foreach ($month_data as $key=>$value)
            <td>{{$value['all_customer_aquition_cost']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>Avg rental per customer new</th>
            @foreach ($month_data as $key=>$value)
            <td>{{$value['avg_rental_per_customer_new']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>Avg rental per customer total</th>
            @foreach ($month_data as $key=>$value)
            <td>{{$value['avg_rental_per_customer_rental']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>Justdial</th>
            @foreach ($month_data as $key=>$value)
            <td>{{$value['justdial']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>Offline Marketing</th>
            @foreach ($month_data as $key=>$value)
            <td>{{$value['offline_marketing']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>Sales Value</th>
            @foreach ($month_data as $key=>$value)
            <td>{{$value['sales_value']}}</td>
            @endforeach
        </tr>                    
        <tr>
            <th>Purchase Value</th>
            @foreach ($month_data as $key=>$value)
            <td>{{$value['purchase_value']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>Sales Margin%</th>
            @foreach ($month_data as $key=>$value)
            <td>{{$value['sales_margin']}}%</td>
            @endforeach
        </tr>
        <tr>
            <th>Sales Customer</th>
            @foreach ($month_data as $key=>$value)
            <td>{{$value['sales_customer']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>Over all acquition cost</th>
            @foreach ($month_data as $key=>$value)
            <td>{{$value['over_all_aquition_cost']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>Sales Transport</th>
            @foreach ($month_data as $key=>$value)
            <td>{{$value['sales_transport']}}</td>
            @endforeach
        </tr>
    </tbody>
</table>