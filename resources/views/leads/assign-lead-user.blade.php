 @extends('header_and_sidebar')

 @section('styles')

 @endsection

 @section('content')
        @if(session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
    <div class="card my-3">
        <div class="card-header">
            Pending Lead Owner Leads
        </div>
        <div class="card-body">
            <div class="table table-responsive">
                <table class="table table-stripped">
                    <thead>
                        <tr>
                            <th>Sr.No.</th>
                            <th>Customer Name</th>
                            <th>Patient Name</th>
                            <th>Mobile No</th>
                            <th>Equipment</th>
                            <th>Location</th>
                            <th>Lead Source</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leads as $key=>$lead)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$lead->customer_name}}</td>
                            <td>{{$lead->patient_name}}</td>
                            <td>{{$lead->primary_contact_no}}</td>
                            <td>{{"-"}}</td>
                            <td>{{$lead->location.'-'.$lead->city}}</td>
                            <td>{{$lead->lead_source}}</td>
                            <td><button class="btn btn-sm btn-outline-primary assign" data-id="{{$lead->id}}" title="Assign"><i class="fas fa-arrow-right"></i></button></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No Leads Pending</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="assignLeadOwner" tabindex="-1" aria-labelledby="assignLeadOwnerLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('assign-lead-user')}}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="assignLeadOwnerLabel">Assign Lead Owner</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="lead_id" id="lead_id">
                        <label for="lead_owner">Select Lead Owner</label>
                        <select name="lead_owner" id="lead_owner" title="Select Lead Owner" class="select selectpicker form-control" data-live-search="true" data-size="5" required>
                            @forelse($lead_owners as $key=>$user)
                                <option value="{{$user->id}}">{{$user->username}}</option>
                            @empty
                                <option value="No" disabled>No Users Found</option>
                            @endforelse
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
 @endsection

 @section('script')
    <script>
        $(".assign").click(function(){
            $("#lead_id").val($(this).data("id"));
            $("#assignLeadOwner").modal('show');
        });
    </script>
 @endsection