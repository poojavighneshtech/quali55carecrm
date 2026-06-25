@extends('header_and_sidebar')
@section('title')
    Task: All Projects
@endsection
@section('content')
    <form action="" method="POST">
        {{ csrf_field() }}
        <div class="container-fluid">
            @if(session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
            @endif
            <!-- DataTales Example -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">All Projects</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Sr.No.</th>
                                    <th>Project Name</th>
                                    <th>Project Status</th>
                                    <th>Duration</th>
                                    <th>Action</th>                                    
                                </tr>
                            </thead>
                                
                            <tbody>
                                {{!$sr_no = 1}}
                                @foreach($project_details as $project_detail)
                                    <tr>
                                        <td>{{$sr_no}}</td>
                                        <td>{{$project_detail['project_name']}}</td>
                                        <td>{{$project_detail['project_status']}}</td>
                                        <td>{{date('d-m-Y',strtotime($project_detail['start_date']))}} - {{date('d-m-Y',strtotime($project_detail['end_date']))}}</td>
                                        <td><a class="btn btn-primary" href="{{url('/')}}/edit_project/{{$project_detail['id']}}">Edit</a> <a class="btn btn-danger" href="{{url('/')}}/delete_project/{{$project_detail['id']}}">Delete</a></td>
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