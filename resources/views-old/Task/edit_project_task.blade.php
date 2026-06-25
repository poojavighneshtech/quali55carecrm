@extends('header_and_sidebar')
@section('title')
    Task : Add Project
@endsection
@section('content')
    <form action="<?php echo url('/');?>/update_project" method="POST" enctype="multipart/form-data">
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
                            <h6 class="m-0 font-weight-bold text-primary">Edit Project</h6>
                            <input type="hidden" name="project_id" value="{{$project_details[0]['id']}}">
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="" class="control-label">Name</label>
                                        <input type="text" class="form-control form-control-sm" name="project_name" value="{{$project_details[0]['project_name']}}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Status</label>
                                        <select name="project_status" id="status" class="custom-select custom-select-sm">
                                            <option value="Pending" <?php if($project_details[0]['project_status'] == 'Pending'){echo 'selected';}?> >Pending</option>
                                            <option value="On-Hold" <?php if($project_details[0]['project_status'] == 'On-Hold'){echo 'selected';}?> >On-Hold</option>
                                            <option value="Done" <?php if($project_details[0]['project_status'] == 'Done'){echo 'selected';}?> >Done</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                <div class="form-group">
                                <label for="" class="control-label">Start Date</label>
                                <input type="date" class="form-control form-control-sm" autocomplete="off" name="start_date" value="{{$project_details[0]['start_date']}}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="" class="control-label">End Date</label>
                                <input type="date" class="form-control form-control-sm" autocomplete="off" name="end_date" value="{{$project_details[0]['end_date']}}">
                                </div>
                            </div>
                            </div>
                            <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="" class="control-label">Project Manager</label>
                                    <select class="form-control form-control-sm select2" name="project_manager">
                                        @foreach($project_managers as $project_manager)
                                            <option value="{{$project_manager['id']}}" <?php if($project_details[0]['project_manager'] == $project_manager['id']){echo 'selected';}?> >{{$project_manager['username']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>                        
                            <input type="hidden" name="manager_id">                        
                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="" class="control-label">Project Team Members</label>
                                <select class="form-control form-control-sm" name="project_team_members[]">
                                    {{!$project_team_members = json_decode($project_details[0]['project_team_members'])}}
                                    @foreach($team_members as $team_member)
                                        <option value="{{$team_member['id']}}" <?php if(in_array($team_member['id'],$project_team_members)){echo 'selected';}?> >{{$team_member['username']}}</option>
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
                                            {{$project_details[0]['description']}}
                                        </textarea>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <center><button class="btn btn-primary" type="submit" name="submit" value="submit">Update Product</button></center>
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