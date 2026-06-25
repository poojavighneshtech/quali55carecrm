@extends('header_and_sidebar')
@section('title')
    Task : Add Project
@endsection
@section('content')
    <form action="<?php echo url('/');?>/add_project_task" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="container-fluid">
            <!-- DataTales Example -->
            <div class="row">
                <div class="col-md-1">
                </div>
                <div class="col-md-10">
                    @if(session()->has('message'))
                        <div class="alert alert-success">
                            {{ session()->get('message') }}
                        </div>
                    @endif
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Add New Project</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="" class="control-label">Name</label>
                                        <input type="text" class="form-control form-control-sm" name="project_name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Status</label>
                                        <select name="project_status" id="status" class="custom-select custom-select-sm">
                                            <option value="Pending">Pending</option>
                                            <option value="On-Hold">On-Hold</option>
                                            <option value="Done">Done</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                <div class="form-group">
                                <label for="" class="control-label">Start Date</label>
                                <input type="date" class="form-control form-control-sm" autocomplete="off" name="start_date">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="" class="control-label">End Date</label>
                                <input type="date" class="form-control form-control-sm" autocomplete="off" name="end_date">
                                </div>
                            </div>
                            </div>
                            <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="" class="control-label">Project Manager</label>
                                    <select class="form-control form-control-sm select2" name="project_manager">
                                        @foreach($project_managers as $project_manager)
                                            <option value="{{$project_manager['id']}}">{{$project_manager['username']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>                        
                            <input type="hidden" name="manager_id">                        
                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="" class="control-label">Project Team Members</label>
                                <select class="form-control form-control-sm" name="project_team_members[]">
                                    @foreach($team_members as $team_member)
                                        <option value="{{$team_member['id']}}">{{$team_member['username']}}</option>
                                    @endforeach
                                </select>
                                </div>
                            </div>
                            </div>
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <label for="" class="control-label">Description</label>
                                        <textarea name="description" id="" cols="20" rows="4" class="summernote form-control">
                                        </textarea>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <center><button class="btn btn-primary" type="submit" name="submit" value="submit">Add New Product</button></center>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
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