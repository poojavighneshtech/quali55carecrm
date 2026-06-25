@extends('header_and_sidebar')
@section('title')
    Task : Add Task
@endsection
@section('content')
    <form action="<?php echo url('/');?>/add_new_task" method="POST" enctype="multipart/form-data">
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
                            <h6 class="m-0 font-weight-bold text-primary">Add New Task</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="" class="control-label">Name</label>
                                        <input type="text" class="form-control form-control-sm" name="task_name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Status</label>
                                        <select name="task_status" id="status" class="custom-select custom-select-sm">
                                            <option value="Pending">Pending</option>
                                            <option value="On-hold">On-Hold</option>
                                            <option value="Done">Done</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="" class="control-label">Project</label>
                                        <select class="form-control form-control-sm select2" name="project_id">
                                            @foreach($project_details as $project_detail)
                                                <option value="{{$project_detail['id']}}">{{$project_detail['project_name']}}</option>
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
                            <center><button class="btn btn-primary" type="submit" name="submit" value="submit">Add New Task</button></center>
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