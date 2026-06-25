@extends('header_and_sidebar')
@section('title')
    Admin: Add New User
@endsection
@section('content')
    <form action="<?php echo url('/');?>/edit_user/{{$user_details[0]['id']}}" method="POST">
        {{ csrf_field() }}
        <div class="row justify-content-center" style="margin-top: 0rem;">
            <div class="col-6">
                <div class="card o-hidden border-0 shadow-lg">
                    
                    <div class="card-header bg-primary text-white">
                        <h3 class="m-0 font-weight-bold">Edit User</h3>
                    </div>
                    <div class="card-body">
                        @if(session()->has('message'))
                            <div class="alert alert-success">
                                {{session()->get('message')}}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                            </div>
                        @endif
                        @foreach ($errors->all() as $error)
                        <div class="alert alert-danger">
                            {{$error}}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endforeach
                        <div class="row">
                            <div class="col-md-4 form-group text-right">
                                <label for="username" class="control-label"><b>Username</b></label>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="username" id="username" placeholder="Username" required="true" value="{{$user_details[0]['username']}}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group text-right">
                                <label for="password" class="control-label"><b>Password</b></label>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="password" id="password" aria-describedby="password_help" placeholder="Password" required="true" value="{{$user_details[0]['password']}}">
                                    <ul>
                                        <li><small id="password_help" class="form-text text-left text-danger"> Password must have Uppercase and Lowercase letter</small></li>
                                        <li><small id="password_help" class="form-text text-left text-danger">Password must have Number and Special Character</small></li>
                                    </ul>
                                </div>
                            </div> 
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group text-right">
                                <label for="email_id_user" class="control-label"><b>Email ID</b></label>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <input type="email" class="form-control" name="email_id_user" id="email_id_user" placeholder="Email" required="true" value="{{$user_details[0]['email_id_user']}}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group text-right">
                                <label for="contact_no" class="control-label"><b>Mobile Number</b></label>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="contact_no" id="contact_no" maxlength="10" oninput="numberOnly(this.id);" placeholder="Mobile Number" required="true" value="{{$user_details[0]['contact_no']}}">
                                </div>
                            </div> 
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group text-right">
                                <label for="location_user" class="control-label"><b>Area</b></label>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="location_user" id="location_user" placeholder="Area" required="true" value="{{$user_details[0]['location_user']}}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group text-right">
                                <label for="user_city" class="control-label"><b>City</b></label>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="user_city" id="user_city" placeholder="City" required="true" value="{{$user_details[0]['user_city']}}" list="userCity" required>
                                    <datalist id="userCity">
                                        <option value="Mumbai">Mumbai</option>
                                        <option value="Pune">Pune</option>
                                    </datalist>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group text-right">
                                <label for="role" class="control-label"><b>Role</b></label>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    {{-- <input type="text" class="form-control" name="role" id="role" placeholder="Location"> --}}
                                    <select class="form-control" name="role">
                                        @if(session('role')=='superuser')
                                            <option value="superuser" @if($user_details[0]['role']=='superuser') {{"checked"}} @endif>SuperUser</option>
                                            <option value="admin" @if($user_details[0]['role']=='admin') {{"checked"}} @endif>Admin</option>
                                            <option value="user" @if($user_details[0]['role']=='user') {{"checked"}} @endif>User</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group text-right">
                                <label for="role_access" class="control-label"><b>Role Access</b></label>
                            </div>
                            <div class="col-md-8">
                                   <!-- Default switch -->
                                    @php
                                        $role_access = json_decode($user_details[0]['role_access']);
                                    @endphp

                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="role_access[]" value="20" id="hot_lead" data-toggle="collapse" href="#hot_lead" aria-expanded="false" aria-controls="hot_lead"  @if(in_array('20',$role_access)) {{"checked"}} @endif>
                                        <label class="custom-control-label" for="hot_lead">Hot Leads Management</label>
                                    </div>
                                    <div class="collapse @if(in_array('20',$role_access) || in_array('21',$role_access) || in_array('22',$role_access)) {{"show"}}@endif" id="hot_lead">
                                        <div class="card card-body">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" name="role_access[]" value="21" id="all_hot_leads" @if(in_array('21',$role_access)) {{"checked"}} @endif>
                                                <label class="custom-control-label" for="all_hot_leads">Hot Leads</label>
                                            </div>
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" name="role_access[]" value="22" id="in_process_hot_leads" @if(in_array('22',$role_access)) {{"checked"}} @endif>
                                                <label class="custom-control-label" for="in_process_hot_leads">In Process</label>
                                            </div>
                                        </div>
                                    </div>
                                    {{--lead--}}
                                    
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="role_access[]" value="1" id="lead" data-toggle="collapse" href="#lead" aria-expanded="false" aria-controls="lead" @if(in_array('1',$role_access)) {{"checked"}} @endif>
                                        <label class="custom-control-label" for="lead">Lead</label>
                                    </div>
                                    <div class="collapse @if(in_array('1',$role_access) || in_array('2',$role_access) || in_array('3',$role_access) || in_array('4',$role_access) ||in_array('16',$role_access)) {{"show"}}@endif" 
                                        id="lead">
                                        <div class="card card-body">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" name="role_access[]" value="2" id="lead_mgmt" @if(in_array('2',$role_access)) {{"checked"}} @endif>
                                                <label class="custom-control-label" for="lead_mgmt">Lead Management</label>
                                            </div>
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" name="role_access[]" value="3" id="referral_mgmt" @if(in_array('3',$role_access)) {{"checked"}} @endif>
                                                <label class="custom-control-label" for="referral_mgmt">Referral Management</label>
                                            </div>
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" name="role_access[]" value="4" id="jd_lead_mgmt" @if(in_array('4',$role_access)) {{"checked"}} @endif>
                                                <label class="custom-control-label" for="jd_lead_mgmt">JD Lead Management</label>
                                            </div>
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" name="role_access[]" value="16" id="nurses_data" @if(in_array('16',$role_access)) {{"checked"}} @endif>
                                                <label class="custom-control-label" for="nurses_data">Referrer Management</label>
                                            </div>
                                        </div>
                                    </div>
                                    {{--vendor--}}
                                    <div class="custom-control custom-switch" >
                                        <input type="checkbox" class="custom-control-input" name="role_access[]" value="5" id="vdr" data-toggle="collapse" href="#vdr" aria-expanded="false" aria-controls="vdr" @if(in_array('5',$role_access)) {{"checked"}} @endif>
                                        <label class="custom-control-label" for="vdr">Vendor</label>
                                    </div>
                                     
                                    <div class="collapse @if(in_array('5',$role_access) || in_array('6',$role_access) || in_array('7',$role_access)) {{"show"}}@endif" id="vdr">
                                        <div class="card card-body">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" name="role_access[]" value="6" id="vendor_mgmt" @if(in_array('6',$role_access)) {{"checked"}} @endif>
                                                <label class="custom-control-label" for="vendor_mgmt">Vendor Management</label>
                                            </div>
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" name="role_access[]" value="7" id="vendor_inventory_mgmt" @if(in_array('7',$role_access)) {{"checked"}} @endif>
                                                <label class="custom-control-label" for="vendor_inventory_mgmt">Vendor Inventory Management</label>
                                            </div>
                                        </div>
                                    </div>

                                    {{--Product mgmt--}}
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="role_access[]" value="8" id="product_mgmt" data-toggle="collapse" href="#product_mgmt" aria-expanded="false" aria-controls="product_mgmt" @if(in_array('8',$role_access)) {{"checked"}} @endif>
                                        <label class="custom-control-label" for="product_mgmt">Product Management</label>
                                    </div>
                                    
                                    <div class="collapse @if(in_array('8',$role_access) || in_array('9',$role_access))) {{"show"}}@endif" id="product_mgmt">
                                        <div class="card card-body">
                                            <div class="custom-control custom-switch custom-switch">
                                                <input type="checkbox" class="custom-control-input" name="role_access[]" value="9" id="master_product_mgmt" @if(in_array('9',$role_access)) {{"checked"}} @endif>
                                                <label class="custom-control-label" for="master_product_mgmt">Master Product Management</label>
                                            </div>
                                        </div>
                                    </div>
                                    {{--Order Mgmt--}}
                                    {{-- <div class="custom-control custom-switch" >
                                        <input type="checkbox" class="custom-control-input" name="role_access[]" value="10" id="order_mgmt" data-toggle="collapse" href="#order_mgmt" aria-expanded="false" aria-controls="order_mgmt">
                                        <label class="custom-control-label" for="order_mgmt">Order Management</label>
                                    </div>
                                    
                                    <div class="collapse" id="order_mgmt">
                                        <div class="card card-body">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" name="role_access[]" value="11" id="delivery_mgmt">
                                                <label class="custom-control-label" for="delivery_mgmt">Delivery Management</label>
                                            </div>
                                        </div>
                                    </div> --}}
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="role_access[]" value="17" id="order_mgmt" @if(in_array('17',$role_access)) {{"checked"}} @endif>
                                        <label class="custom-control-label" for="order_mgmt">Order</label>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="role_access[]" value="11" id="delivery_mgmt" @if(in_array('11',$role_access)) {{"checked"}} @endif>
                                        <label class="custom-control-label" for="delivery_mgmt">Delivery Management</label>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="role_access[]" value="12" id="renewal_pickup" @if(in_array('12',$role_access)) {{"checked"}} @endif>
                                        <label class="custom-control-label" for="renewal_pickup">Renewal and Pickup</label>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="role_access[]" value="13" id="billing_payments" @if(in_array('13',$role_access)) {{"checked"}} @endif>
                                        <label class="custom-control-label" for="billing_payments">Billing & Payments</label>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="role_access[]" value="14" id="reports" @if(in_array('14',$role_access)) {{"checked"}} @endif>
                                        <label class="custom-control-label" for="reports">Reports</label>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="role_access[]" value="15" id="user_mgmt" @if(in_array('15',$role_access)) {{"checked"}} @endif>
                                        <label class="custom-control-label" for="user_mgmt">User Management</label>
                                    </div>

                                   {{-- <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="role_access[]" value="1" id="lead_mgmt">
                                        <label class="custom-control-label" for="lead_mgmt">Lead Management</label>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="role_access[]" value="2" id="referral_mgmt">
                                        <label class="custom-control-label" for="referral_mgmt">Referral Management</label>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="role_access[]" value="3" id="jd_lead_mgmt">
                                        <label class="custom-control-label" for="jd_lead_mgmt">JD Lead Management</label>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="role_access[]" value="4" id="vendor_mgmt">
                                        <label class="custom-control-label" for="vendor_mgmt">Vendor Management</label>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="role_access[]" value="5" id="vendor_inventory_mgmt">
                                        <label class="custom-control-label" for="vendor_inventory_mgmt">Vendor Inventory Management</label>
                                    </div>
                                    <div class="custom-control custom-switch custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="role_access[]" value="6" id="master_product_mgmt">
                                        <label class="custom-control-label" for="master_product_mgmt">Master Product Management</label>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="role_access[]" value="7" id="order_mgmt">
                                        <label class="custom-control-label" for="order_mgmt">Order Management</label>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="role_access[]" value="8" id="delivery_mgmt">
                                        <label class="custom-control-label" for="delivery_mgmt">Delivery Management</label>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="role_access[]" value="9" id="renewal_pickup">
                                        <label class="custom-control-label" for="renewal_pickup">Renewal and Pickup</label>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="role_access[]" value="10" id="billing_payments">
                                        <label class="custom-control-label" for="billing_payments">Billing & Payments</label>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="role_access[]" value="11" id="reports">
                                        <label class="custom-control-label" for="reports">Reports</label>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="role_access[]" value="12" id="user_mgmt">
                                        <label class="custom-control-label" for="user_mgmt">User Management</label>
                                    </div> --}}
                                    
                                   
                            </div>
                        </div>

                        <hr>
                        <center>
                            <div class="col-md-8">
                                <button type="submit" name="submit" class="btn btn-primary" value="submit">Update</button>
                            </div>
                        </center>
                        
                    </div>
                </div>   
            </div>
        </div> 
    </form>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('#hot_lead').click(function() 
        {
            if($(this).is(':checked'))
            {
                $('#all_hot_leads').prop('checked',true);
                $('#in_process_hot_leads').prop('checked',true);
            }
            else
            {
                $('#lead_mgmt').prop('checked',false);
                $('#referral_mgmt').prop('checked',false);
                $('#jd_lead_mgmt').prop('checked',false);
                $('#nurses_data').prop('checked',false);
            }
        });
        $('#lead').click(function() 
        {
            if($(this).is(':checked'))
            {
                $('#lead_mgmt').prop('checked',true);
                $('#referral_mgmt').prop('checked',true);
                $('#jd_lead_mgmt').prop('checked',true);
                $('#nurses_data').prop('checked',true);
            }
            else
            {
                $('#lead_mgmt').prop('checked',false);
                $('#referral_mgmt').prop('checked',false);
                $('#jd_lead_mgmt').prop('checked',false);
                $('#nurses_data').prop('checked',false);
            }
        });
        $('#vdr').click(function() 
        {
            if($(this).is(':checked'))
            {
                $('#vendor_mgmt').prop('checked',true);
                $('#vendor_inventory_mgmt').prop('checked',true);
            }
            else
            {
                $('#vendor_mgmt').prop('checked',false);
                $('#vendor_inventory_mgmt').prop('checked',false);
            }
        });
        $('#product_mgmt').click(function() 
        {
            if($(this).is(':checked'))
            {
                $('#master_product_mgmt').prop('checked',true);
            }
            else
            {
                $('#master_product_mgmt').prop('checked',false);
            }
        });
    });
    
    function numberOnly(id) 
    {
        var element = document.getElementById(id);
        element.value = element.value.replace(/[^0-9]/gi, "");
    }
</script>

@endsection