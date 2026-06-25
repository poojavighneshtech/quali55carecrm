@extends('header_and_sidebar')
@section('style')
@endsection
@section('content')
    <div class="container-fluid">
        @if(session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
        @endif
        @if(session()->has('error'))
            <div class="alert alert-danger">
                {{ session()->get('error') }}
            </div>
        @endif 
        <div class="card my-5" id="filter_card">
            <div class="card-header border-primary" id="filter_card">
                <div class="row">
                    <div class="col text-primary" data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                        <strong>Google Campaign Report</strong>
                    </div>
                    <div class="col-auto">
                        <a data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                            <i class="fa fa-chevron-down pull-right"></i>&emsp;<b>Filter</b>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body border-primary collapse" id="filter-collapse" aria-labelledby="headingTwo" data-parent="#filter_card">
                <form action="{{url('/')}}/googleCampaignReport" method="GET" id="googleCampaignReport_form">
                    @csrf
                    <div class="row">
                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="row">
                                        <div class="col-md-4 text-right">
                                            <label for="campaign_name"><strong>Campaign Name:</strong></label>
                                        </div>
                                        <div class="col-md-8">
                                            <select class="selectpicker form-control " name="filtercampaigns[]" id="filtercampaigns" data-size="5" data-live-search="true" multiple="true" title="select campaign">
                                                @foreach($campaign_names as $key=>$campaign)
                                                    <option value="{{$campaign->campaign}}" @if(isset($filter_data['filtercampaigns'])) @if(in_array($campaign->campaign,$filter_data['filtercampaigns']))selected @endif @endif>{{$campaign->campaign}}</option>
                                                @endforeach
                                            </select>
                                            {{-- <input type="text" class="form-control" name="filter_campaign_name" id="txt_filter_campaign_name"  placeholder="Campaign Name.." 
                                                size="5" autocomplete="off" value="@if(isset($filter_data['filter_campaign_name'])){{$filter_data['filter_campaign_name']}}@endif">
                                            <datalist id="datalist_customers"></datalist> --}}
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-4 text-right">
                                            <label for="filter_campaign_state"><strong>Campaign State:</strong></label>
                                        </div>
                                        <div class="col-md-8 text-right">
                                            <select class="selectpicker form-control " name="filter_campaign_state[]" id="select_filter_campaign_state" multiple="true">
                                                {{-- <option value="All" @if(isset($filter_data['filter_campaign_state'])) @if(in_array("All",$filter_data['filter_campaign_state']))selected @endif @endif>All</option> --}}
                                                <option value="Enabled" @if(isset($filter_data['filter_campaign_state'])) @if(in_array("Enabled",$filter_data['filter_campaign_state']))selected @endif @endif>Enabled</option>
                                                <option value="Removed" @if(isset($filter_data['filter_campaign_state'])) @if(in_array("Removed",$filter_data['filter_campaign_state']))selected @endif @endif>Removed</option>
                                                <option value="Paused" @if(isset($filter_data['filter_campaign_state'])) @if(in_array("Paused",$filter_data['filter_campaign_state']))selected @endif @endif>Paused</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="col-md-5">
                                    <div class="row">
                                        <div class="col-md-3 text-right">
                                            From
                                        </div>
                                        <div class="col-md-9">
                                            <input type="date" name="filter_from_date" id="input_from_date" class="form-control" value="@if(isset($filter_data['filter_from_date'])){{$filter_data['filter_from_date']}}@endif">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-3 text-right">
                                            To
                                        </div>
                                        <div class="col-md-9">
                                            <input type="date" name="filter_end_date" id="input_end_date" class="form-control" value="@if(isset($filter_data['filter_end_date'])){{$filter_data['filter_end_date']}}@endif">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-12 text-right">
                                            <input type="checkbox" name="filtersummaryreport" id="filtersummaryreport" @if(request()->get('filtersummaryreport')){{"checked"}}@endif>
                                            <label class="ml-1" for="filtersummaryreport"><strong>Summary Report</strong></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <a href="{{route('googleCampaignReport')}}" class="btn btn-outline-secondary btn-sm btn-block" id="btn_clear">Clear Filter</a>
                            <br>
                            <button type="submit" class="btn btn-outline-primary btn-block" name="btn_submit" value="submit">Submit</button>
                            <br>                            
                            <button type="submit" class="btn btn-outline-success btn-block" name="btn_export" value="export">Export</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="count-div">
                <div class="row">
                    <div class="col-auto">
                        <div class="row">
                            <div class="col-auto">Clicks</div>
                            <div class="col-auto"><a  type="button" class="count" id="clicks"><span class="badge badge-primary">@if(isset($count)){{$count['clicks']}}@else{{0}}@endif</span></a></div>
                            <div class="col-auto">Impr.</div>
                            <div class="col-auto"><a  type="button" class="count" id="impr"><span class="badge badge-primary">@if(isset($count)){{$count['impr']}}@else{{0}}@endif</span></a></div>
                            <div class="col-auto">CTR</div>
                            <div class="col-auto"><a  type="button" class="count" id="ctr"><span class="badge badge-primary">@if(isset($count)){{$count['ctr_avg']}}@else{{0}}@endif%</span></a></div>
                            <div class="col-auto">Avg-CPC</div>
                            <div class="col-auto"><a  type="button" class="count" id="avg_cpc"><span class="badge badge-primary">@if(isset($count)){{$count['avg_cpc_avg']}}@else{{0}}@endif%</span></a></div>
                            <div class="col-auto">Budget</div>
                            <div class="col-auto"><a  type="button" class="count" id="budget"><span class="badge badge-primary">@if(isset($count)){{$count['budget']}}@else{{0}}@endif</span></a></div>
                            <div class="col-auto">Cost</div>
                            <div class="col-auto"><a  type="button" class="count" id="cost"><span class="badge badge-primary">@if(isset($count)){{$count['cost']}}@else{{0}}@endif</span></a></div>
                            <div class="col-auto">Conv. Count</div>
                            <div class="col-auto"><a  type="button" class="count" id="conv_count"><span class="badge badge-primary">@if(isset($count)){{$count['conv_count']}}@else{{0}}@endif</span></a></div>
                            <div class="col-auto">Conv. Rate</div>
                            <div class="col-auto"><a  type="button" class="count" id="conv_rate"><span class="badge badge-primary">@if(isset($count)){{$count['conv_rate']}}@else{{0}}@endif</span></a></div>
                        </div>
                    </div>
                    <div class="col-auto mr-auto"></div>
                </div>
            </div>
            <div class="table table-responsive">
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
                            <th><small>Conversions</small></th>
                            <th><small>Rates</small></th>
                            <th><small>View-thr-conv.</small></th>
                            <th><small>Cost/ conv.</small></th>                            
                            <th><small>rate</small></th>
                            <th><small>Call Received</small></th>
                            <th><small>-</small></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($google_campaign_report as $key=>$value)
                            <tr>
                                {{-- <td>{{$google_campaign_report->firstItem()+$loop->index}}</td> --}}
                                <td><small>{{$value->campaign}}</small></td>
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
                                <td><small>{{$value->calls_received_count}}</small></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="update_record({{$value->id}},{{$value->calls_received_count}});"><i class="fa fa-edit" aria-hidden="true"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @php
                $append_arr = array();
                if(isset($filter_data['filtercampaigns'])){
                    $append_arr['filtercampaigns'] = $filter_data['filtercampaigns'];
                }
                if(isset($filter_data['filter_campaign_state'])){
                    $append_arr['filter_campaign_state'] = $filter_data['filter_campaign_state'];
                }
                if(isset($filter_data['filter_from_date'])){
                    $append_arr['filter_from_date'] = $filter_data['filter_from_date'];
                }
                if(isset($filter_data['filter_end_date'])){
                    $append_arr['filter_end_date'] = $filter_data['filter_end_date'];
                }
                if(isset($filter_data['filtersummaryreport'])){
                    $append_arr['filtersummaryreport'] = $filter_data['filtersummaryreport'];
                }
            @endphp
            {{$google_campaign_report->appends($append_arr)->links('Custom.Pagination.pagination')}}
            
        </div>
    </div>
    <div class="modal fade" id="updateCount" tabindex="-1" role="dialog" aria-labelledby="updateCountLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{route('update-details-campaign')}}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateCountLabel">Calls Received</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <input type="hidden" name="row_id" id="row_id">
                                <label for="calls_received_count">Calls Received</label>
                                <input type="text" name="calls_received_count" id="calls_received_count" class="form-control form-control-sm" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save changes</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        function update_record(id,count){
            $("#calls_received_count").val(count);
            $("#row_id").val(id);
            $("#updateCount").modal("show");
        }
    </script>
@endsection