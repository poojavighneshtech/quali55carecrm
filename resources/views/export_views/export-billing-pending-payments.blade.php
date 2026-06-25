<table>
    <thead>
        <tr>
            <th>Order Id</th>
            <th>Order Date</th>
            <th>Created On</th>
            <th>Type</th>
            <th>Status</th>
            <th>Customer Name</th>
            <th>Patient Name</th>
            <th>Mobile</th>
            <th>Address</th>
            <th>Products</th>
            <th>Mode</th>
            @if(session('user_id') == '97')
                <th>Total Amt</th>
                @if(session('user_id') == '97')                
                    <th>Total Rent</th>
                    <th>Total Sale</th>
                    <th>Total Transport</th>
                    <th>Total Deposite</th>
                    <th>Total V. Cost</th>
                @endif
            @endif
            <th>Labour</th>
            <th>Reference No</th>
            <th>Rec Total Amt</th>
            <th>Lead Own</th>
            <th>Location</th>
            <th>Assigned To</th>
            <th>Comment</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key=>$value)
            <tr>
                <td data-label="Order Id">{{$value->order_id}}</td>
                <td data-label="Order Date">
                    {{date('d-m-y',strtotime($value->date))}}
                </td>
                <td>
                    @if($value->order_type == 'Delivery')
                        {{date('d-M-y h:i A',strtotime($value->order_created_at))}}
                    @elseif($value->order_type == 'Collection')
                        {{date('d-M-y h:i A',strtotime($value->order_created_at))}}
                    @elseif($value->order_type == 'Pick Up')
                        {{date('d-M-y h:i A',strtotime($value->order_created_at))}}
                    @elseif(in_array($value->order_type,['Install','Shifting','Repair','Replace']))
                        {{date('d-M-y h:i A',strtotime($value->order_created_at))}}
                    @endif
                </td>
                <td>
                    {{$value->order_type}}
                </td>
                <td>
                    {{$value->status}}
                </td>
                <td data-label="Customer Name">
                    {{$value->customer_name}}
                </td>
                <td data-label="Patient Name">
                    {{$value->patient_name}}
                </td>
                <td data-label="Mobile No">
                    {{$value->mobile_number}}
                </td>
                {{-- @if(session('user_id') == '22')
                    <td data-label="Total">
                        T: {{$value->assigned_total_amount}}
                        @if($value->assigned_payment_mode == 'Cash')
                            C: {{$value->assigned_total_amount}}
                        @elseif(isset($value->assigned_cash) && $value->assigned_cash != '')
                        C: {{$value->assigned_cash}}
                        @else
                        C: {{"0"}}
                        @endif
                        @if($value->assigned_payment_mode == 'Online')
                            O: {{$value->assigned_total_amount}}
                        @elseif(isset($value->assigned_online) && $value->assigned_online != '')
                            O: {{$value->assigned_online}}
                        @else
                            O: {{"0"}}
                        @endif
                    </td>
                @endif --}}
                <td>
                    {{$value->address}}
                </td>
                <td>
                    {{$value->order_products}}
                </td>
                <td data-label="Mode">
                    {{$value->assigned_payment_mode}}
                </td>
                @if(session('user_id') == '97')
                    <td data-label="Total">
                        {{$value->assigned_total_amount}}
                        {{-- {{$value->assigned_total_amount}}
                        T: {{$value->assigned_total_amount}}
                        @if($value->assigned_payment_mode == 'Cash')
                            C: {{$value->assigned_total_amount}}
                        @elseif(isset($value->assigned_cash) && $value->assigned_cash != '')
                        C: {{$value->assigned_cash}}
                        @else
                        C: {{"0"}}
                        @endif
                        @if($value->assigned_payment_mode == 'Online')
                            O: {{$value->assigned_total_amount}}
                        @elseif(isset($value->assigned_online) && $value->assigned_online != '')
                            O: {{$value->assigned_online}}
                        @else
                            O: {{"0"}}
                        @endif --}}
                    </td>
                    @if(session('user_id') == '97')                    
                        <td data-label="Total Rent">
                            {{$value->order_products_rent}}
                        </td>                    
                        <td data-label="Total Sale">
                            {{$value->order_products_sale}}
                        </td>
                        <td data-label="Total Transport">
                            {{$value->order_products_transport}}
                        </td>    
                        <td data-label="Total Deposite">
                            {{$value->order_products_deposite}}
                        </td>
                        <td data-label="Total Vendor Cost">
                            {{$value->order_vendor_cost}}
                        </td>
                    @endif                                     
                @endif
                <td data-label="Labour">{{$value->labour_charges}}</td>
                <td data-label="Reference No">
                    {{($value->reference_id)?$value->reference_id:"-"}}
                </td>
                <td data-label="Rec Total">
                    @if($value->order_type == 'Collection' && $value->assigned_payment_mode == 'Online')
                        T: {{$value->assigned_total_amount}}
                    @elseif(isset($value->received_total_amount) && $value->received_total_amount != '')
                        T: {{$value->received_total_amount}}
                    @else
                        T: {{"-"}}
                    @endif
                    @if($value->order_type == 'Collection' && $value->assigned_payment_mode == 'Cash')
                        @if($value->assigned_payment_mode == 'Cash')
                            C: {{$value->assigned_total_amount}}
                        @elseif(isset($value->assigned_cash) && $value->assigned_cash != '')
                            C: {{$value->assigned_cash}}
                        @else
                            C: {{"0"}}
                        @endif
                    @elseif(isset($value->received_cash) && $value->received_cash != '')
                        C: {{$value->received_cash}}
                    @else
                    C: -
                    @endif
                    @if($value->order_type == 'Collection' && $value->assigned_payment_mode == 'Online')
                        @if($value->assigned_payment_mode == 'Online')
                            O: {{$value->assigned_total_amount}}
                        @elseif(isset($value->assigned_online) && $value->assigned_online != '')
                            O: {{$value->assigned_online}}
                        @else
                            O: {{"0"}}
                        @endif
                    @elseif(isset($value->received_online) && ($value->received_online !='' || $value->received_online != null))
                        O: {{$value->received_online}}
                    @else
                        O: {{"-"}}
                    @endif
                </td>
                <td data-label="Lead Own">{{($value->username)?$value->username:"-"}}</td>
                <td data-label="Location">
                    {{$value->location}}
                </td>
                <td data-label="Assigned To">
                    {{$value->assigned_to}}
                </td>
                <td data-label="Comment">
                    {{$value->comment}}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>