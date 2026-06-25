@extends('header_and_sidebar')
@section('title')
    Admin: User Profile
@endsection
@section('content')
    {{-- <form action="<?php //echo url('/');?>/add_user" method="POST">
        {{ csrf_field() }} --}}
        <div class="row justify-content-center" style="margin-top: 0rem;">
            <div class="col-6">
                <div class="card o-hidden border-0 shadow-lg">
                    
                    <div class="card-header bg-primary text-white">
                        <h3 class="m-0 font-weight-bold">Profile Details</h3>
                    </div>
                    <div class="card-body">
                        @if(session()->has('message'))
                            <div class="alert alert-success">
                                {{ session()->get('message') }}
                            </div>
                        @endif
                    
                        <div class="row">
                            <div class="col-md-4 form-group text-right">
                                <label for="username" class="control-label"><b>Username</b></label>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <span>{{$user_details[0]['username']}}</span>
                                    {{-- <input type="text" class="form-control" name="username" id="username" placeholder="Username" required="true"> --}}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group text-right">
                                <label for="password" class="control-label"><b>Password</b></label>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <span>{{$user_details[0]['password']}}</span>
                                    {{-- <input type="text" class="form-control" name="password" id="password" placeholder="Password" required="true"> --}}
                                </div>
                            </div> 
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group text-right">
                                <label for="email_id_user" class="control-label"><b>Email ID</b></label>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <span>{{$user_details[0]['email_id_user']}}</span>
                                    {{-- <input type="email" class="form-control" name="email_id_user" id="email_id_user" placeholder="Email" required="true"> --}}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group text-right">
                                <label for="contact_no" class="control-label"><b>Mobile Number</b></label>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <span>{{$user_details[0]['contact_no']}}</span>
                                    {{-- <input type="number" class="form-control" name="contact_no" id="contact_no" maxlength="10" placeholder="Mobile Number" required="true"> --}}
                                </div>
                            </div> 
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group text-right">
                                <label for="location_user" class="control-label"><b>Location</b></label>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <span>{{$user_details[0]['location_user']}}</span>
                                    {{-- <input type="text" class="form-control" name="location_user" id="location_user" placeholder="Location" required="true"> --}}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group text-right">
                                <label for="role" class="control-label"><b>Role</b></label>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <span>{{$user_details[0]['role']}}</span>
                                    {{-- <input type="text" class="form-control" name="role" id="role" placeholder="Location"> --}}
                                    {{-- <select class="form-control" name="role">
                                        <option value="admin">Admin</option>
                                        <option value="user">User</option>
                                    </select> --}}
                                </div>
                            </div>
                        </div>
                        {{-- <hr> --}}
                        <center>
                            {{-- <div class="col-md-8">
                                <button type="submit" name="submit" class="btn btn-primary" value="submit">Add User</button>
                                <button type="reset" name="reset" class="btn btn-default" value="reset">Clear</button>
                            </div> --}}
                        </center>
                        
                    </div>
                </div>   
            </div>
        </div> 
    {{-- </form> --}}
@endsection

@section('script')
<script>
    // function viewDetails(clicked_id)
    // {

    //         document.getElementById("thead"+clicked_id).style.visibility="visible";
    //         document.getElementById("tbody"+clicked_id).style.visibility="visible";
    //         document.getElementById(clicked_id).style.display = "none";
    //         document.getElementById("hide"+clicked_id).style.display = "block";
    // }
    // function hideDetails(clicked_id)
    // {
    //         var id = document.getElementById(clicked_id);
    //         var dataID = id.getAttribute('data-id');
    //         document.getElementById("thead"+dataID).style.visibility="hidden";
    //         document.getElementById("tbody"+dataID).style.visibility="hidden";
    //         document.getElementById(dataID).style.display = "block";
    //         document.getElementById("hide"+dataID).style.display = "none";
    // }
</script>

@endsection