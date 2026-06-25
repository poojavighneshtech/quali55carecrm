<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Inventory Pickup/Return Date</th>
            <th>Product Name</th>
            <th>Vendor Name</th>
            <th>Quanity</th>
            <th>Inventory Id</th>
            <th>Pickup Address</th>
            <th>Drop Address</th>
            <th>In/Out</th>
            <th>Assigned To</th>
            <th>Images</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key=>$inv)
            <tr>
                <td>{{date('d-M-Y',strtotime($inv->date))}}</td>
                <td class="text-nowrap">
                    @if($inv->inventory_pickup_date)
                        {{date('d-M-Y',strtotime($inv->inventory_pickup_date))}}
                    @else
                        -
                    @endif
                </td>
                <td>{{$inv->product_name}}</td>
                <td>{{$inv->vendor_name}}</td>
                <td>{{$inv->quantity}}</td>
                <td>{{$inv->inventory_id}}</td>
                <td>{{$inv->pickup_address}}</td>
                <td>{{$inv->drop_address}}</td>
                <td>
                    {{$inv->state}}
                </td>
                <td>
                    {{$inv->assigned_to}}
                </td>
                <td>
                    @if(count(explode(',',$inv->product_img))>1)
                        @foreach(explode(',',$inv->product_img) as $img)
                            <a href="{{asset('storage/app/'.$img)}}">View</a>
                        @endforeach                        
                    @else
                    <a href="{{asset('storage/app/'.$inv->product_img)}}">View</a>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>