{{-- @extends('new-sidebar') --}}
@extends('header_and_sidebar')

@section('title')
    All B2B Users
@endsection
@section('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.css" rel="stylesheet"/>
@endsection

@section('content')
    @if(session()->has('message'))
        <div class="alert alert-success alert">
            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                <span aria-hidden='true'>&times;</span>
            </button> {{ session()->get('message') }}
        </div>
    @endif
    @if(session()->has('error'))
    <div class="alert alert-danger alert">
        <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
            <span aria-hidden='true'>&times;</span>
        </button> {{ session()->get('error') }}
    </div>
@endif
    <div class="card">
        <div class="card-header border-primary">
            <div class="col text-primary d-block">
                <strong>B2B Users</strong>
            </div>
        </div>
        <div class="card-body">
            <form action="{{route('b2b-user-view-all')}}" method="get">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" name="search_customer" id="search_customer" placeholder="Seach customer name,contact no,email..." 
                            value="{{request()->get('search_customer')}}">
                    </div>
                    <div class="col-md-2">
                        <select class="form-control form-control-sm" name="search_flag" id="">
                            <option value="All" @if(request()->get('search_flag')=='All') selected @endif>All</option>
                            @foreach ($flagState as $flag)
                                <option value="{{$flag->flag}}" @if(request()->get('search_flag')==$flag->flag) selected @endif>{{$flag->flag}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-md-8">
                                <button type="submit" class="btn btn-outline-success btn-sm btn-block">Submit</button>
                            </div>
                            <div class="col-md-4">
                                <a href="{{route('b2b-user-view-all')}}" class="btn btn-outline-secondary btn-sm btn-block">Clear</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
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
                    @foreach($users as $key=>$value)
                        <tr>
                            <td data-label="Sr.No.">
                                {{$users->firstItem()+$loop->index}}
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
                                        <a href="{{route('b2b-user-view')}}/?id={{$value->id}}" title="View" class="dropdown-item btn btn-sm btn-outline-primary fas fa-eye text-primary"
                                            data-tooltip="tooltip" data-placement="bottom" title="View">
                                            <span>View</span></a>
                                        <a href="{{route('b2b-user-view')}}/?id={{$value->id}}" title="Edit" class="dropdown-item btn btn-sm btn-outline-success fas fa-edit  text-success"
                                            data-tooltip="tooltip" data-placement="bottom" title="Edit">
                                            <span>Edit</span>
                                        </a>
                                        <a href="{{route('b2b-user-delete')}}/?id={{$value->id}}" title="Delete" class="dropdown-item btn btn-sm btn-outline-danger fas fa-trash-alt text-danger"
                                            data-tooltip="tooltip" data-placement="bottom" title="Delete">
                                            <span>Delete</span>
                                        </a>
                                    </div>

                                    {{-- <a href="{{route('b2b-user-view')}}/?id={{$value->id}}" title="View" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                                    <a href="{{route('b2b-user-view')}}/?id={{$value->id}}" title="Edit" class="btn btn-sm btn-outline-success"><i class="fas fa-edit"></i></a>
                                    <a href="{{route('b2b-user-delete')}}/?id={{$value->id}}" title="Delete" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash-alt"></i></a> --}}
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
                    @endforeach
                </tbody>
            </table>
            {{$users->links('Custom.Pagination.pagination')}}
        </div>

    </div>
    {{-- Modals --}}
    <div class="modal fade show" id="passwordChangeModal" tabindex="-1" role="dialog" aria-labelledby="passwordChangeModalLabel" aria-hidden="true" >
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{route('b2b-user-password-change')}}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="passwordChangeModalLabel">Change User Password</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body container-fluid">
                        <div class="row">
                            <input type="hidden" name="user_id" id="password_change_user_id" value="{{old('user_id')}}">
                            <label for=""><strong>LoggedIn User Password</strong></label>
                            <div class="input-group">
                                <input type="password" class="form-control form-control-sm" name="loggedin_password" id="loggedin_password" placeholder="" value="{{old('loggedin_password')}}" required>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary btn-sm toggle-password" type="button"><i class="fa fa-eye" aria-hidden="true"></i></button>
                                </div>
                            </div>
                            
                            @if ($errors->has('loggedin_password'))
                                <span class="text-danger" id="loggedin_password_error_span">{{ $errors->first('loggedin_password') }}...</span>
                            @endif
                        </div>
                        <div class="row mt-2">
                            <label for=""><strong>Change User Password</strong></label>
                            <input type="text" class="form-control form-control-sm" name="change_user_password" id="change_user_password" placeholder="" value="{{old('change_user_password')}}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade show" id="activeUser" tabindex="-1" role="dialog" aria-labelledby="activeUserLabel" aria-hidden="true" >
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{route('b2b-user-active')}}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="activeUserLabel"><span id="activeUserModalTitle"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body container-fluid">
                        <input type="hidden" name="user_id" id="activeUserModal_user_id">
                        <strong>
                            Are your sure to update this user in active state ?
                        </strong>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-outline-success">Active</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('script')
    @if ($errors->has('loggedin_password'))
        <script>
            $(function() {
                $('#passwordChangeModal').modal('show');
            });
        </script>
    @endif
    <script>
        $(document).ready(function () {
            $(function () {
                $('[data-tooltip="tooltip"]').tooltip()
            });
        });

        $('.toggle-password').click(function(){
            $(this).children().toggleClass('mdi-eye-outline mdi-eye-off-outline');
            let input = $('#loggedin_password');
            input.attr('type', input.attr('type') === 'password' ? 'text' : 'password');
        });

        $('.btn_password_change').on('click',function(){
            let userId = $(this).data('user_id');
            $('#loggedin_password_error_span').css('display','none');
            $('#loggedin_password').val(null);
            $('#change_user_password').val(null);
            $('#password_change_user_id').val(userId);
            $('#passwordChangeModal').modal('show');
        });

        $('.btn_active').on('click',function(){
            let name = $(this).data('name');
            let user_id = $(this).data('user_id');
            $('#activeUserModalTitle').text(name);
            $('#activeUserModal_user_id').val(user_id)
            $('#activeUser').modal('show');
        });
    </script>
@endsection