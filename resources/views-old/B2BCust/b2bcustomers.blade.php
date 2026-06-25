@extends('header_and_sidebar')

@section('styles')
    <title>B2B Customers</title>
@endsection

@section('content')
    <div class="card my-3">
        <div class="card-header border-primary" id="agentsfilter">
            <div class="row">
                <div class="col text-primary" data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                    <strong>B2B Customers</strong>
                </div>
                <div class="col-auto">
                    <a data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                        <i class="fa fa-chevron-down pull-right"></i>&emsp;<b>Filter</b>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body border-primary collapse" id="filter-collapse" aria-labelledby="headingTwo" data-parent="#agentsfilter">
            <a id="addnew" name="addnew" value="Add New" class="btn btn-sm btn-outline-primary" href="{{route('b2bcustomers.create')}}"><i class="fas fa-plus"></i> New</a>
        </div>
    </div>
    <div class="table table-responsive jim-table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Sr.No.</th>
                    <th>Name</th>
                    <th>Contact No</th>
                    <th>Email</th>
                    <th>Active/In-Active</th>
                    <th>Request</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($b2bcustomers as $key=>$value)
                    <tr>
                        <td data-label="Sr.No.">
                            {{$b2bcustomers->firstItem()+$loop->index}}
                        </td>
                        <td data-label="Name">
                            {{$value->name}}
                        </td>
                        <td data-label="Contact No">
                            {{$value->contact_no}}
                        </td>
                        <td data-label="Email">
                            {{$value->email}}
                        </td>
                        <td>
                            <span class="badge {{($value->flag=='Active'?'badge-success':'badge-danger')}}">{{$value->flag}}</span>
                        </td>
                        <td>
                            @if($value->forgot_pass_req==1)
                                <span class="badge badge-warning text-dark">Password Change</span>
                            @endif
                        </td>
                        <td data-label="Action" >
                            @if($value->flag=='Active')
                                <button class="btn btn-outline btn-sm" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-ellipsis-v " aria-hidden="true"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton" width="100%">
                                    <a href="{{route('b2bcustomers.show',$value->id)}}" title="View" class="dropdown-item btn btn-sm btn-outline-primary fas fa-eye text-primary"
                                        data-tooltip="tooltip" data-placement="bottom" title="View">
                                        <span>View</span></a>
                                    <a href="{{route('b2bcustomers.edit',$value->id)}}" title="Edit" class="dropdown-item btn btn-sm btn-outline-success fas fa-edit  text-success"
                                        data-tooltip="tooltip" data-placement="bottom" title="Edit">
                                        <span>Edit</span>
                                    </a>
                                    <a href="{{route('b2bcustomers.destroy',$value->id)}}" title="Delete" class="dropdown-item btn btn-sm btn-outline-danger fas fa-trash-alt text-danger"
                                        data-tooltip="tooltip" data-placement="bottom" title="Delete">
                                        <span>Delete</span>
                                    </a>
                                </div>
                                @if($value->forgot_pass_req==1)
                                    <button class="btn btn-outline-secondary btn-sm btn_password_change" id="btn_password_change" data-user_id="{{$value->id}}"
                                        data-tooltip="tooltip" data-placement="bottom" title="Change Password">
                                        <i class="fa fa-key" aria-hidden="true"></i>
                                    </button>
                                @endif
                            @elseif($value->flag=='Inactive')
                                <button class="btn btn-outline-info btn-sm btn_active" id="btn_active" data-user_id="{{$value->id}}"
                                    data-name="{{$value->name}}" data-tooltip="tooltip" data-placement="bottom" title="Activate User">
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No B2B Customers Found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{$b2bcustomers->links('Custom.Pagination.pagination')}}
    </div>
@endsection

@section('script')

@endsection