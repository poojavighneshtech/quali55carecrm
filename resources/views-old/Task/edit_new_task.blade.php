@extends('header_and_sidebar')
@section('title')
    Task : Add Task
@endsection
@section('content')
    <form action="<?php echo url('/');?>/update_task" method="POST" enctype="multipart/form-data">
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
                            <input type="hidden" name="task_id" id="task_id" value="{{$task_detail[0]['task_id']}}">
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="" class="control-label">Name</label>
                                        <input type="text" class="form-control form-control-sm" name="task_name" value="{{$task_detail[0]['task_name']}}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Status</label>
                                        <select name="task_status" id="status" class="custom-select custom-select-sm">
                                            <option value="Pending" <?php if($task_detail[0]['status'] == 'Pending'){echo "selected";}?> >Pending</option>
                                            <option value="On-Hold" <?php if($task_detail[0]['status'] == 'On-Hold'){echo "selected";}?> >On-Hold</option>
                                            <option value="Done" <?php if($task_detail[0]['status'] == 'Done'){echo "selected";}?> >Done</option>
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
                                                <option value="{{$project_detail['id']}}" <?php if($task_detail[0]['status'] == $project_detail['id']){echo "selected";}?>>{{$project_detail['project_name']}}</option>
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
                                            {{$task_detail[0]['description']}}
                                        </textarea>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <center><button class="btn btn-primary" type="submit" name="submit" value="submit">Update Task</button></center>
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