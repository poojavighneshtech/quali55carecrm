<table class="table table-stripped">
    <thead>
        <tr>
            <th>Sr No</th>
            <th>Date</th>
            <th>Customer Name</th>
            <th>Contact Number</th>
            <th>Equipment</th>
            <th>Amount</th>                                
            <th>State</th>
            <th>Lead Owner</th>
        </tr>
    </thead>
    <tbody>
        @php
            $sr_no = 1;
        @endphp
        @foreach($count_array as $key => $value)
            <tr>
                <td>{{$sr_no}}</td>
                <td>
                    {{date('d-M-Y',strtotime($value->date))}}
                </td>
                <td>{{$value->customer_name}}</td>
                <td>{{$value->contact_no}}</td>
                <td>{{$value->product_name}}</td>
                <td>
                    @if(isset($value->product_rent_amount))
                        {{$value->product_rent_amount}}
                    @elseif(isset($value->payment_mode))
                        @if($value->payment_mode == "Cash")
                            {{$value->cash_amount}}
                        @elseif($value->payment_mode == "Online")
                            {{$value->online_amount}}
                        @endif
                    @endif
                </td>
                <td>
                    @if(isset($value->product_rent_amount))
                        <span class="badge badge-danger">Overdue</span>
                    @elseif(isset($value->payment_mode))
                        <span class="badge badge-success">Renewed</span>
                    @endif
                </td>
                <td>{{$value->lead_owner}}</td>
            </tr>
            @php
                $sr_no = $sr_no + 1;
            @endphp
        @endforeach
        <tr>
            <td>Total Revenue</td>
            <td>{{$total_renewed_count_total + $total_overdue_count_total}}</td>
            <td>Collected Revenue</td>
            <td>{{$total_renewed_count_total}}</td>
            <td>Overdue Revenue</td>
            <td>{{$total_overdue_count_total}}</td>
        </tr>
    </tbody>
</table>