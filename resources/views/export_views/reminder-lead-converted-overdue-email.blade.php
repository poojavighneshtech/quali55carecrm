<table border="1">
    <thead>
        <tr>
            <th>Date</th>
            <th>Customer Name</th>
            <th>Contact Number</th>
            <th>Patient Name</th>
            <th>Lead Owner</th>
            <th>Sr No</th>
            <th>Product</th>
            @if($dataType=='converted_lead')
                <th>Sale/Rental</th>
                <th>Quantity</th>
                <th>Rent</th>
                <th>Deposit</th>
                <th>Transport</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $leadKey=>$leadData) 
            <tr>
                <td>
                    @if($dataType=='converted_lead')
                        {{date('d-m-Y',strtotime($leadData->converted_at))}}
                    @else
                        {{date('d-m-Y',strtotime($leadData->creation_date))}}
                    @endif
                </td>
                <td>{{$leadData->customer_name}}</td>
                <td>{{$leadData->primary_contact_no}}</td>
                <td>{{($leadData->patient_name!=null)?$leadData->patient_name:'-'}}</td>
                <td>{{$leadData->created_by}}</td>
                @php
                    $type = json_decode($leadData->sale_rental);
                    $quantity = json_decode($leadData->equipment_qty);
                    $rent = json_decode($leadData->offered_rent_total);
                    $deposit = json_decode($leadData->deposite_total);
                    $transport = json_decode($leadData->transport);
                    $i = 1;
                @endphp
              
                @foreach($leadData->product_name as $key1 =>$pname)
                    @if($key1==0)
                        <td>{{$i}}</td>
                        <td>{{$leadData->product_name[0]}}</td>
                        @if($dataType=='converted_lead')
                            <td>{{$type[0]}}</td>
                            <td>{{$quantity[0]}}</td>
                            <td>{{$rent[0]}}</td>
                            <td>{{$deposit[0]}}</td>
                            <td>{{$transport[0]}}</td>
                        @endif
                    @endif
                @endforeach
            </tr> 
            @foreach($leadData->product_name as $key2 =>$pname)
                @if($key2>0)
                    <tr>
                        <td colspan="5"></td>
                        <td>{{++$i}}</td>
                        <td>{{$pname}}</td>
                        @if($dataType=='converted_lead')
                            <td>{{$type[$key2]}}</td>
                            <td>{{$quantity[$key2]}}</td>
                            <td>{{$rent[$key2]}}</td>
                            <td>{{$deposit[$key2]}}</td>
                            <td>{{$transport[$key2]}}</td>
                        @endif
                    </tr>
                @endif
            @endforeach
        @endforeach
    </tbody>
</table>
                            