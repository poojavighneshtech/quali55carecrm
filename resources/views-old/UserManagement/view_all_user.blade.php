@extends('header_and_sidebar')
@section('title')
   Admin : User List
@endsection

@section('content')
    <form action="" method="POST">
        {{ csrf_field() }}
        <div class="container-fluid">
            <!-- DataTales Example -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"> All User List</h6>
                </div>
                <div class="card-body">
                    @if(session()->has('message'))
                        <div class="alert alert-success">
                            {{ session()->get('message') }}
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-bordered " id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Sr.No.</th>
                                    @if (session('role')=='superuser')
                                        <th>Action</th>
                                    @endif
                                    <th>Username</th>
                                    <th>Email ID</th>
                                    <th>Contact Number</th>
                                    <th>Location</th>
                                    <th>City</th>
                                    <th>Role</th>
                                    {{-- <th>Password</th> --}}
                                </tr>
                            </thead>
                                    @php
                                        $count = 0;
                                    @endphp
                                    @foreach($all_user as $user)
                                        @php
                                            $count = $count+1;
                                        @endphp
                                        <tr data-count="{{$count}}">
                                            <td>{{ $count }}</td>
                                            @if (session('role')=='superuser')
                                                <td><a href="{{url('/')}}/edit_user/{{$user['id']}}" class="btn btn-primary">Edit</a></td>    
                                            @endif
                                            <td>{{ $user['username'] }}</td>
                                            <td>{{ $user['email_id_user'] }}</td>
                                            <td>{{ $user['contact_no'] }}</td>
                                            <td>{{ $user['location_user'] }}</td>
                                            <td>{{ $user['user_city'] }}</td>
                                            <td>{{ $user['role'] }}</td>
                                         {{-- <td>{{ $user['password'] }}</td> --}}
                                        </tr>
                                    @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>	                    
        </div>
        <!-- /.container-fluid -->
        
    </form>
@endsection

@section('script')
    <script>
        
    </script>   
@endsection