<table>
    <thead>
        <tr>
            <th>Total</th>
            <th>Settled</th>
            <th>Not Settled</th>
            <th>Not Verified</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{count($settled)+count($verified)+count($not_verified)}}</td>
            <td>{{count($settled)}}</td>
            <td>{{count($verified)}}</td>
            <td>{{count($not_verified)}}</td>
        </tr>
    </tbody>
</table>
<hr>
@if(count($settled)!=0)
    <center>Settled</center>
    @foreach($settled as $key=>$value)
        {{$value->user_name}}<br>
    @endforeach
    <hr>
@endif
@if(count($verified)!=0)
    <center>Verified</center>
    @foreach($verified as $key=>$value)
        {{$value->user_name}}<br>
    @endforeach
    <hr>
@endif
@if(count($not_verified)!=0)
    <center>Not Verified</center>
    @foreach($not_verified as $key=>$value)
        {{$value->user_name}}<br>
    @endforeach
    <hr>
@endif