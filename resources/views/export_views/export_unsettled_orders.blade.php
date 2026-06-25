<table class="table table-hover table-responsive table-flush ">
    <thead class="thead thead-primary text-white border-primary">
        <tr class="text-nowrap border-primary">
            {{-- <th>Sr.No.</th> --}}
            <th><b>Order Id</b></th>
            <th><b>Date</b></th>
            <th><b>Type</b></th>
            <th><b>Status</b></th>                              
            <th><b>Customer Name</b></th>
            <th><b>Mobile</b></th>
            <th><b>Mode</b></th>
            <th><b>Total Amount</b></th>
            <th><b>Cash Amount</b></th>
            <th><b>Online Amount</b></th>
            <th><b>Order State</b></th>
        </tr>
    </thead>
    <tbody>
        @php
            $settled_amount = 0;
            $not_settled_amount = 0;
            $settled_cash_amount = 0;
            $not_settled_cash_amount = 0;
            $settled_online_amount = 0;
            $not_settled_online_amount = 0;
        @endphp
        @foreach($details_count as $key=>$value)
            <tr class="text-nowrap">
                <td>{{$value->order_id}}</td>
                <td>
                    {{date('d-m-y',strtotime($value->date))}}
                </td>
                <td>
                    @if($value->order_type == 'Delivery')
                        <span class="badge badge-success">
                            Delivery
                        </span>
                    @elseif($value->order_type == 'Collection')
                        <span class="badge badge-warning">
                            Collection
                        </span>
                    @elseif($value->order_type == 'Pick Up')
                        <span class="badge badge-danger">
                            Pick Up
                        </span>
                    @endif
                </td>
                <td>
                    @if($value->status == 'Pending')
                    <span class="badge badge-danger">
                        {{"Pending"}}
                    </span>
                    @elseif($value->status == 'Accepted')
                        <span class="badge badge-secondary">
                            {{"Accepted"}}
                        </span>
                    @elseif($value->status == 'Assigned')
                        <span class="badge badge-warning">
                            {{"Assigned"}}
                        </span>
                    @elseif($value->status == 'InProgress')
                        <span class="badge badge-primary">
                            {{"InProgress"}}
                        </span>
                    @elseif($value->status == 'Cancel')
                        <span class="badge ">
                            {{"Cancel"}}
                        </span>
                    @elseif($value->status == 'Collected')
                        <span class="badge badge-success">
                            {{"Collected"}}
                        </span>
                    @elseif($value->status == 'Delivered')
                        <span class="badge badge-success">
                            {{"Delivered"}}
                        </span>
                    @elseif($value->status == 'Picked up' || $value->status == 'Picked Up')
                        <span class="badge badge-success">
                            {{"Picked Up"}}
                        </span>
                    @endif
                </td>
                <td>
                    {{$value->customer_name}}
                </td>
                <td>
                    {{$value->mobile_number}}
                </td>
                
                <td>
                    {{$value->assigned_payment_mode}}
                    <input type="hidden" name="hidden_payment_mode" id="hidden_payment_mode{{$key}}" value="{{$value->assigned_payment_mode}}">
                </td>
                <td>
                    {{$value->assigned_total_amount}}
                </td>
                <td>
                    @if($value->assigned_payment_mode == 'Cash')
                        {{$value->assigned_total_amount}}
                    @elseif(isset($value->assigned_cash) && $value->assigned_cash != '')
                    {{$value->assigned_cash}}
                    @else
                    {{"0"}}
                    @endif
                </td>
                <td>
                    @if($value->assigned_payment_mode == 'Online')
                        {{$value->assigned_total_amount}}
                    @elseif(isset($value->assigned_online) && $value->assigned_online != '')
                        {{$value->assigned_online}}
                    @else
                        {{"0"}}
                    @endif
                </td>
                <td>
                    @if($value->settlement_status == "Y")
                        @php
                            $settled_amount = $settled_amount + $value->assigned_total_amount
                        @endphp
                        @if($value->assigned_payment_mode == 'Cash')
                            {{!$settled_cash_amount = $settled_cash_amount + $value->assigned_total_amount}}
                        @elseif(isset($value->assigned_cash) && $value->assigned_cash != '')
                            {{!$settled_cash_amount = $settled_cash_amount + $value->assigned_total_amount}}
                        @endif
                        @if($value->assigned_payment_mode == 'Online')
                            {{!$settled_online_amount = $settled_online_amount + $value->assigned_total_amount}}
                        @elseif(isset($value->assigned_online) && $value->assigned_online != '')
                            {{!$settled_online_amount = $settled_online_amount + $value->assigned_total_amount}}
                        @endif
                        {{"Settled"}}
                    @elseif($value->settlement_status == "N")
                        {{"Not Settled"}}
                        @php
                            $not_settled_amount = $not_settled_amount + $value->assigned_total_amount
                        @endphp
                        @if($value->assigned_payment_mode == 'Cash')
                            {{!$not_settled_cash_amount = $not_settled_cash_amount + $value->assigned_total_amount}}
                        @elseif(isset($value->assigned_cash) && $value->assigned_cash != '')
                            {{!$not_settled_cash_amount = $not_settled_cash_amount + $value->assigned_total_amount}}
                        @endif
                        @if($value->assigned_payment_mode == 'Online')
                            {{!$not_settled_online_amount = $not_settled_online_amount + $value->assigned_total_amount}}
                        @elseif(isset($value->assigned_online) && $value->assigned_online != '')
                            {{!$not_settled_online_amount = $not_settled_online_amount + $value->assigned_total_amount}}
                        @endif
                    @endif
                </td>
            </tr>
        @endforeach
        <tr>
            <td><b>Settled Count</b></td>
            <td><b>{{$count_array[1] + $count_array[3] + $count_array[5]}}</b></td>
            <td><b>Settled Total</b></td>
            <td><b>{{$settled_amount}}</b></td>
            <td><b>Settled Cash</b></td>
            <td><b>{{$settled_cash_amount}}</b></td>
            <td><b>Settled Online</b></td>
            <td><b>{{$settled_online_amount}}</b></td>
        </tr>
        <tr>
            <td><b>Not Settled Count</b></td>
            <td><b>{{$count_array[0] + $count_array[2] + $count_array[4]}}</b></td>
            <td><b>Not Settled Total</b></td>
            <td><b>{{$not_settled_amount}}</b></td>
            <td><b>Not Settled Cash</b></td>
            <td><b>{{$not_settled_cash_amount}}</b></td>
            <td><b>Not Settled Online</b></td>
            <td><b>{{$not_settled_online_amount}}</b></td>
        </tr>
    </tbody>
</table>