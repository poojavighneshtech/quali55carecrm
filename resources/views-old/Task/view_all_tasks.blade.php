@extends('header_and_sidebar')
@section('title')
    Task: All Projects
@endsection
@section('content')
    <form action="" method="POST">
        {{ csrf_field() }}
        <div class="container-fluid">
            <!-- DataTales Example -->
            @if(session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
            @endif
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">All Tasks</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Sr.No.</th>
                                    <th>Task Name</th>
                                    <th>Task Status</th>
                                    <th>Project Name</th>
                                    <th>Action</th>                                    
                                </tr>
                            </thead>
                                
                            <tbody>
                                {{!$sr_no = 1}}
                                @foreach($project_details as $project_detail)
                                    <tr>
                                        <td>{{$sr_no}}</td>
                                        <td>{{$project_detail['task_name']}}</td>
                                        <td>{{$project_detail['status']}}</td>
                                        <td>{{$project_detail['project_name']}}</td>
                                        <td><a class="btn btn-primary" href="{{url('/')}}/edit_task/{{$project_detail['task_id']}}">Edit</a> <a class="btn btn-danger" href="{{url('/')}}/delete_task/{{$project_detail['task_id']}}">Delete</a></td>
                                    </tr>
                                    {{!$sr_no = $sr_no + 1}}
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