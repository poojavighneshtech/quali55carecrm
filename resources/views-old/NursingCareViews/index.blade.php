@extends('header_and_sidebar')

@section('styles')

@endsection

@section('content')
    @if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
    @endif
    @if(session()->has('info'))
    <div class="alert alert-primary">
        {{ session()->get('info') }}
    </div>
    @endif
    @if(session()->has('error'))
    <div class="alert alert-danger">
        {{ session()->get('error') }}
    </div>
    @endif
    <div class="my-2">
        <div class="card" id="filter_card">
            <div class="card-header border-primary" id="filter_card">
                <div class="row">
                    <div class="col text-primary" data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                        <strong>Nursing Care</strong>
                    </div>
                    <div class="col-auto">
                        <a data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                            <i class="fa fa-chevron-down pull-right"></i>&emsp;<b>Filter</b>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body border-primary collapse" id="filter-collapse" aria-labelledby="headingTwo" data-parent="#filter_card">
                <form action="{{route('nursing-care')}}" method="get">
                    <div class="row form-group">
                        <div class="col-md-2">
                            <label for="filter_customer_name">Customer Name</label>
                            <input type="text" name="filter_customer_name" id="filter_customer_name" class="form-control form-control-sm" value="{{request()->get('filter_customer_name')}}">
                        </div>
                        <div class="col-md-2">
                            <label for="filter_contact_no">Contact No.</label>
                            <input type="text" name="filter_contact_no" id="filter_contact_no" class="form-control form-control-sm" value="{{request()->get('filter_contact_no')}}">
                        </div>
                        <div class="col-md-2">
                            <label for="filter_start_date">From Date</label>
                            <input type="date" name="filter_start_date" id="filter_start_date" class="form-control form-control-sm" value="{{request()->get('filter_start_date')}}">
                        </div>
                        <div class="col-md-2">
                            <label for="filter_stop_date">To Date</label>
                            <input type="date" name="filter_stop_date" id="filter_stop_date" class="form-control form-control-sm" value="{{request()->get('filter_stop_date')}}">
                        </div>
                        <div class="col-md-2">
                            <label for="filter_service">Service</label>
                            {{-- <input type="text" name="filter_service" id="filter_service" class="form-control form-control-sm"> --}}
                            <select name="filter_service" id="filter_service" class="select selectpicker form-control form-control-sm" title="Select Service" value="{{request()->get('filter_service')}}">
                                <option value="Nurse" @if(request()->get('filter_service') == "Nurse"){{"selected"}}@endif>Nurse</option>
                                <option value="Care Taker" @if(request()->get('filter_service') == "Care Taker"){{"selected"}}@endif>Care Taker</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="filter_status">Status</label>
                            {{-- <input type="text" name="filter_status" id="filter_status" class="form-control form-control-sm"> --}}
                            <select name="filter_status" id="filter_status" class="select selectpicker form-control form-control-sm" title="Select Status" value="{{request()->get('filter_status')}}">
                                <option value="process"@if(request()->get('filter_status') == "process"){{"selected"}}@endif>Process</option>
                                <option value="live"@if(request()->get('filter_status') == "live"){{"selected"}}@endif>Live</option>
                                <option value="stopped"@if(request()->get('filter_status') == "stopped"){{"selected"}}@endif>Stopped</option>
                                <option value="cancelled"@if(request()->get('filter_status') == "cancelled"){{"selected"}}@endif>Cancelled</option>
                            </select>
                        </div>
                    </div>
                    @if(session('role') != 'user')
                        <div class="row form-group">
                            <div class="col-md-2">
                                <label for="filter_lead_owner">Lead Owner</label>
                                <select name="filter_lead_owner" id="filter_lead_owner" class="select selectpicker form-control form-control-sm" title="Select Lead owner" value="{{request()->get('filter_lead_owner')}}">
                                    @foreach($users as $key=>$user)
                                        <option value="{{$user->id}}"@if(request()->get('filter_lead_owner') == $user->id){{"selected"}}@endif>{{$user->username}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif
                    <div class="row form-group">
                        <div class="col text-center">
                            <a href="{{route('nursing-care-create')}}" class="btn btn-sm btn-outline-primary">
                                Create Lead
                            </a>
                            <button type="submit" class="btn btn-sm btn-outline-success">
                                Search
                            </button>
                            <a href="{{route('nursing-care')}}" class="btn btn-sm btn-outline-secondary">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="table table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Contact No</th>
                            <th>Location</th>
                            <th>Service Required</th>
                            <th>Lead Owner</th>
                            <th>Status</th>
                            <th>Remark</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leads as $key=>$lead)
                            <tr class="@if($lead->status=='Process')
                                table-warning
                            @elseif($lead->status!='Live' && $lead->status!='Process')
                                table-danger
                            @elseif($lead->status=='Live')
                                table-success
                            @else
                                table-primary
                            @endif text-dark">
                                <td data-label="Date">{{date('d-M-y',strtotime($lead->lead_date))}}</td>
                                <td data-label="Name">{{$lead->customer_name}}</td>
                                <td data-label="Contact No">{{$lead->contact_no}}</td>
                                <td data-label="Location">{{$lead->area}}</td>
                                <td data-label="Service Required">{{$lead->service_type}}</td>
                                <td data-label="Lead Owner">{{$lead->username}}</td>
                                <td data-label="Status">{{$lead->status}}</td>
                                <td data-label="Remark">
                                    {{-- {{substr($lead->comment,0,20)}} --}}
                                    <span data-toggle="tooltip" data-placement="left" title="{{$lead->remark}}" id="remark{{$key}}" data-remark="{{$lead->remark}}" onclick="viewComment('remark{{$key}}')"><small>{{substr($lead->remark,0,20)}} ...</small></span>
                                </td>
                                <td data-label="Action" class="text-nowrap">
                                    <button id="actionButton_list{{$key}}" type="button" class="btn btn-outline-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-tools"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="actionButton_list{{$key}}">
                                    <a href="{{route('nursing-care-view',$lead->id)}}" class="btn btn-sm btn-outline-primary dropdown-item"><i class="fas fa-eye"></i> View</a>
                                    @if($lead->status != 'Cancelled')
                                        <a href="{{route('nursing-care-edit',$lead->id)}}" class="btn btn-sm btn-outline-secondary dropdown-item"><i class="fas fa-edit"></i> Edit</a>
                                        <a class="btn btn-sm btn-outline-secondary dropdown-item" onclick="updateStatus('{{$lead->id}}','{{$lead->status}}');"><i class="fas fa-edit"></i> Update Status</a>
                                        {{-- <a href="{{route('nursing-care-cancel',$lead->id)}}" class="btn btn-sm btn-outline-danger dropdown-item"><i class="fas fa-trash"></i> Cancel</a> --}}
                                    @endif
                                    
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{$leads->withPath(url()->full())->links('Custom.Pagination.pagination')}}
        </div>
    </div>
    <div class="modal fade" id="updateStatus" tabindex="-1" role="dialog" aria-labelledby="updateStatusLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="#" method="POST" id="statusUpdateForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateStatusLabel">Update Status</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <label for="status_updated">Update Status</label>
                                <select name="status_updated" id="status_updated" class="select selectpicker form-control form-control-sm" title="Select Status">
                                    <option value="1">Process</option>
                                    <option value="2">Live</option>
                                    <option value="3">Stopped</option>
                                    <option value="4">Cancelled</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label for="comment_updated">Remark</label>
                                <textarea class="form-control form-control-sm" name="comment_updated" id="comment_updated" rows="5"></textarea>
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
    <div class="modal fade" id="viewCommentModel" tabindex="-1" role="dialog" aria-labelledby="viewCommentModelLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewCommentModelLabel">Comment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <span id="view_comment_span"></span>
                            
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save changes</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function updateStatus(id,current_status){
            
            let action = "{{url('/')}}/nursing-care-status-update/"+id;
            $("#statusUpdateForm").attr('action', action);
            let status_array = ['Process','Live','Stopped','Cancelled'];
            $("#status_updated").val(parseInt($.inArray(current_status,status_array) + 1));
            $("#status_updated").selectpicker("refresh");
            $("#updateStatus").modal("show");
        }

        function viewComment(id){
            let remark = $("#"+id).data('remark');
            $("#view_comment_span").text(remark);
            $("#viewCommentModel").modal("show");
        }
    </script>
@endsection