<table>
    <thead>
        <tr>
            <th>Clicks</th>
            <th>Impr.</th>
            <th>CTR</th>
            <th>Avg-CPC</th>
            <th>Budget</th>
            <th>Cost</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>@if(isset($count)){{$count['clicks']}}@else{{0}}@endif</td>
            <td>@if(isset($count)){{$count['impr']}}@else{{0}}@endif</td>
            <td>@if(isset($count)){{$count['ctr_avg']}}@else{{0}}@endif%</td>
            <td>@if(isset($count)){{$count['avg_cpc_avg']}}@else{{0}}@endif%</td>
            <td>@if(isset($count)){{$count['budget']}}@else{{0}}@endif</td>
            <td>@if(isset($count)){{$count['cost']}}@else{{0}}@endif</td>
        </tr>
    </tbody>
</table>
<table class="table table-sm table-hover table-stripped">
    <thead class="">
        <tr class="">
            {{-- <th>SrNo.</th> --}}
            <th><small>Campaign</small></th>
            <th><small>Day</small></th>
            <th><small>Campaign state</small></th>
            <th><small>Budget</small></th>
            {{-- <th><small>Campaign type</small></th> --}}
            {{-- <th><small>Currency code</small></th> --}}
            <th><small>Clicks</small></th>
            <th><small>Impr.</small></th>
            <th><small>CTR</small></th>
            <th><small>Avg.-CPC</small></th>
            <th><small>Cost</small></th>
            <th><small>Conver sions</small></th>
            <th><small>Rates</small></th>
            <th><small>View-thr-conv.</small></th>
            <th><small>Cost/ conv.</small></th>                            
            <th><small>rate</small></th>
        </tr>
    </thead>
    <tbody>
        @foreach($google_campaign_report as $key=>$value)
            <tr>
                {{-- <td>{{$google_campaign_report->firstItem()+$loop->index}}</td> --}}
                <td><small>{{$value->campaign}}</small></td>
                {{-- <td class="text-nowrap"><small>{{date('d-M-y',strtotime($value->date))}}</small></td> --}}
                @if(request()->get('filtersummaryreport'))
                    <td class="text-nowrap"><small>{{$value->date}}</small></td>
                @else
                    <td class="text-nowrap"><small>{{date('d-M-y',strtotime($value->date))}}</small></td>
                @endif
                <td><small>{{$value->campaign_state}}</small></td>
                <td><small>{{$value->budget}}</small></td>
                {{-- <td><small>{{$value->campaign_type}}</small></td> --}}
                {{-- <td><small>{{$value->currency_code}}</small></td> --}}
                <td><small>{{$value->clicks}}</small></td>
                <td><small>{{$value->impr}}</small></td>
                <td><small>{{$value->ctr}}</small></td>
                <td><small>{{$value->avg_cpc}}</small></td>
                <td><small>{{$value->cost}}</small></td>
                <td><small>{{$value->conversions}}</small></td>
                <td><small>{{$value->total_rate}}</small></td>
                <td><small>{{$value->view_through_conv}}</small></td>
                <td>
                    <small>
                        @if($value->conversions!=null && $value->conversions!=0 && $value->cost!=0 && $value->cost!=null)
                            {{round($value->cost/$value->conversions,2)}}
                        @else
                            {{0}}
                        @endif
                    </small>
                </td>
                <td><small>{{$value->conv_rate}}</small></td>                                
            </tr>
        @endforeach
    </tbody>
</table>