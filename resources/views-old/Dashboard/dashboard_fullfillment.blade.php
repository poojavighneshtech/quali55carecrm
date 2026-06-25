@extends('header_and_sidebar')
@section('title')
    Dashboard
@endsection
@section('header')
@endsection
@section('content')
    <div class="container-fluid">
        @php
            $role_access = json_decode(session('role_access'));
        @endphp
        <div class="row">

            <div class="col-xl-3 col-md-12 mb-4"
                @if(session('role')=='admin' || session('role')=='superuser')
                    style="display: block";
                @else
                    @if(in_array('1',$role_access))
                        style="display: block";
                    @else
                        style="display: none";
                    @endif
                @endif
                >                
                <a class="card-block stretched-link text-decoration-none" data-toggle="modal" data-target="#LeadManagement" href="#">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xl font-weight-bold text-warning text-uppercase mb-1">
                                        <center>
                                            <h3>
                                                <i class="fas fa-users"></i>
                                            </h3>                                        
                                            <h4>    
                                                <span><b>Lead Management</b></span>
                                            </h4>
                                        </center>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div> 
            <div class="col-xl-3 col-md-12 mb-4"
                 
                @if(session('role')=='admin' || session('role')=='superuser')
                    style="display: block";
                @else
                    @if(in_array('2',$role_access))
                        style="display: block";
                    @else
                        style="display: none";
                    @endif
                @endif
                >                
                <a class="card-block stretched-link text-decoration-none" data-toggle="modal" data-target="#ReferralManagement" href="#">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xl font-weight-bold text-success text-uppercase mb-1">
                                        <center>
                                            <h3>
                                                <i class="fas fa-users"></i>
                                            </h3>                                        
                                            <h4>    
                                                <span><b>Referral Management</b></span>
                                            </h4>
                                        </center>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-6 mb-4"
                @if(session('role')=='admin' || session('role')=='superuser')
                    style="display: block";
                @else
                    @if(in_array('3',$role_access))
                        style="display: block";
                    @else
                        style="display: none";
                    @endif
                @endif
            >
                <a class="card-block stretched-link text-decoration-none" data-toggle="modal" data-target="#JDLeadManagement" href="#">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xl font-weight-bold text-danger text-uppercase mb-1">
                                        <center>
                                            <h3>
                                                <i class="fas fa-user-cog"></i>
                                            </h3>                                        
                                            <h4>    
                                                <span><b>JD Lead Management</b></span>
                                            </h4>
                                        </center>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div> 

            <div class="col-xl-3 col-md-6 mb-4" 
                 
                @if(session('role')=='admin' || session('role')=='superuser')
                    style="display: block";
                @else
                    @if(in_array('4',$role_access))
                        style="display: block";
                    @else
                        style="display: none";
                    @endif
                @endif
                
            >
                <a class="card-block stretched-link text-decoration-none" data-toggle="modal" data-target="#VendorManagement" href="#">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xl font-weight-bold text-success text-uppercase mb-1">
                                        <center>
                                            <h3>
                                                <i class="fas fa-user-cog"></i>
                                            </h3>                                        
                                            <h4>    
                                                <span><b>Vendor Management</b></span>
                                            </h4>
                                        </center>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div> 
        </div>

        <div class="row">

            <div class="col-xl-3 col-md-6 mb-4" 
                 
                @if(session('role')=='admin' || session('role')=='superuser')
                    style="display: block";
                @else
                    @if(in_array('5',$role_access))
                        style="display: block";
                    @else
                        style="display: none";
                    @endif
                @endif
                
            >
                <a class="card-block stretched-link text-decoration-none" data-toggle="modal" data-target="#VendorInventoryMgmt" href="#">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xl font-weight-bold text-info text-uppercase mb-1">
                                        <center>
                                            <h3>
                                                <i class="fab fa-product-hunt"></i>
                                            </h3>                                        
                                            <h4>    
                                                <span><b>Vendor Inventory Management</b></span>
                                            </h4>
                                        </center>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-6 mb-4" 
                @if(session('role')=='admin' || session('role')=='superuser')
                    style="display: block";
                @else
                    @if(in_array('6',$role_access))
                        style="display: block";
                    @else
                        style="display: none";
                    @endif
                @endif
                >
                <a class="card-block stretched-link text-decoration-none" data-toggle="modal" data-target="#MasterProductMgmt" href="#">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xl font-weight-bold text-danger text-uppercase mb-1">
                                        <center>
                                            <h3>
                                                <i class="fas fa-user-cog"></i>
                                            </h3>                                        
                                            <h4>    
                                                <span><b>Master Product Management</b></span>
                                            </h4>
                                        </center>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div> 
            <div class="col-xl-3 col-md-6 mb-4" 
                 
                @if(session('role')=='admin' || session('role')=='superuser')
                    style="display: block";
                @else
                    @if(in_array('7',$role_access))
                        style="display: block";
                    @else
                        style="display: none";
                    @endif
                @endif
                
            >
                <a class="card-block stretched-link text-decoration-none" data-toggle="modal" data-target="#OrderManagement" href="#">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xl font-weight-bold text-warning text-uppercase mb-1">
                                        <center>
                                            <h3>
                                                <i class="fas fa-tasks"></i>
                                            </h3>                                        
                                            <h4>    
                                                <span><b>Order Management</b></span>
                                            </h4>
                                        </center>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div> 
            <div class="col-xl-3 col-md-6 mb-4" 
                 
                @if(session('role')=='admin' || session('role')=='superuser')
                    style="display: block";
                @else
                    @if(in_array('8',$role_access))
                        style="display: block";
                    @else
                        style="display: none";
                    @endif
                @endif
                
            >
                <a class="card-block stretched-link text-decoration-none" data-toggle="modal" data-target="#Delivery1Management" href="#">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xl font-weight-bold text-success text-uppercase mb-1">
                                        <center>
                                            <h3>
                                                <i class="fas fa-tasks"></i>
                                            </h3>                                        
                                            <h4>    
                                                <span><b>Delivery Management</b></span>
                                            </h4>
                                        </center>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4" 
                 
                @if(session('role')=='admin' || session('role')=='superuser')
                    style="display: block";
                @else
                    @if(in_array('9',$role_access))
                        style="display: block";
                    @else
                        style="display: none";
                    @endif
                @endif
                
            >
                <a class="card-block stretched-link text-decoration-none" data-toggle="modal" data-target="#renewal_pickup" href="#">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xl font-weight-bold text-success text-uppercase mb-1">
                                        <center>
                                            <h3>
                                                <i class="fas fa-tasks"></i>
                                            </h3>                                        
                                            <h4>    
                                                <span><b>Renewal And Pickup</b></span>
                                            </h4>
                                        </center>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>  

            <div class="col-xl-3 col-md-6 mb-4"
                @if(session('role')=='admin' || session('role')=='superuser')
                    style="display: block";
                @else
                    @if(in_array('10',$role_access))
                        style="display: block";
                    @else
                        style="display: none";
                    @endif
                @endif
            >
                <a class="card-block stretched-link text-decoration-none" data-toggle="modal" data-target="#BillingPayments" href="#">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xl font-weight-bold text-primary text-uppercase mb-1">
                                        <center>
                                            <h3>
                                                <i class="fas fa-tasks"></i>
                                            </h3>                                        
                                            <h4>    
                                                <span><b>Billing & Payments</b></span>
                                            </h4>
                                        </center>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>  

            <div class="col-xl-3 col-md-6 mb-4"
                @if(session('role')=='admin' || session('role')=='superuser')
                    style="display: block";
                @else
                    @if(in_array('11',$role_access))
                        style="display: block";
                    @else
                        style="display: none";
                    @endif
                @endif
            >
                <a class="card-block stretched-link text-decoration-none" data-toggle="modal" data-target="#reports" href="#">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xl font-weight-bold text-danger text-uppercase mb-1">
                                        <center>
                                            <h3>
                                                <i class="fas fa-tasks"></i>
                                            </h3>                                        
                                            <h4>    
                                                <span><b>Reports</b></span>
                                            </h4>
                                        </center>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-12 mb-4" 
                 
                @if(session('role')=='admin' || session('role')=='superuser')
                    style="display: block";
                @else
                    @if(in_array('12',$role_access))
                        style="display: block";
                    @else
                        style="display: none";
                    @endif
                @endif
                
            >                
                <a class="card-block stretched-link text-decoration-none" data-toggle="modal" data-target="#UserManagement" href="#">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xl font-weight-bold text-info text-uppercase mb-1">
                                        <center>
                                            <h3>
                                                <i class="fas fa-users"></i>
                                            </h3>                                        
                                            <h4>    
                                                <span><b>User Management</b></span>
                                            </h4>
                                        </center>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div> 
             
        </div>
    </div>

    <div class="modal fade" id="MasterProductMgmt">
        <div class="modal-dialog modal-sm modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-body">                
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xl font-weight-bold text-danger text-uppercase mb-1">
                            <center>
                                <h3>
                                    <i class="fas fa-user-cog"></i>
                                </h3>                                        
                                <h4>    
                                    <span><b>Master Product Management</b></span>
                                </h4>
                            </center>
                        </div>
                    </div>
                </div> 
                <a class="btn btn-danger form-control" href="{{url('/')}}/add_new_product">Add New Product</a><br><br>
                <a class="btn btn-danger form-control" href="{{url('/')}}/view_master_products">View Master Products</a><br><br>
            </div>
          </div>
        </div>
    </div>

    <div class="modal fade" id="VendorManagement">
        <div class="modal-dialog modal-sm modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-body">                
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xl font-weight-bold text-success text-uppercase mb-1">
                            <center>
                                <h3>
                                    <i class="fas fa-user-cog"></i>
                                </h3>                                        
                                <h4>    
                                    <span><b>Vendor Management</b></span>
                                </h4>
                            </center>
                        </div>
                    </div>
                </div> 
                <a class="btn btn-success form-control" href="{{url('/')}}/pending_vendors">Pending Vendors</a><br><br>
                <a class="btn btn-success form-control" href="{{url('/')}}/approved_vendors">Approved Vendors</a><br><br>
                <a class="btn btn-success form-control" href="{{url('/')}}/rejected_vendors">Rejected Vendors</a><br><br>
                <a class="btn btn-success form-control" href="{{url('/')}}/requested_vendors">Requested Vendors</a><br><br>                  
            </div>
          </div>
        </div>
    </div>
    <div class="modal fade" id="VendorInventoryMgmt">
        <div class="modal-dialog modal-sm modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-body">                
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xl font-weight-bold text-info text-uppercase mb-1">
                            <center>
                                <h3>
                                    <i class="fab fa-product-hunt"></i>
                                </h3>                                        
                                <h4>    
                                    <span><b>Vendor Inventory Management</b></span>
                                </h4>
                            </center>
                        </div>
                    </div>
                </div>
                <center>
                   
                    <a class="btn btn-info form-control" href="{{url('/')}}/product_request">Pending Rent Requests</a><br><br>
                    <a class="btn btn-info form-control" href="{{url('/')}}/product_approved_rent">Approved Rents</a><br><br>
                    <a class="btn btn-info form-control" href="{{url('/')}}/product_rejected_rent">Rejected Rents</a><br><br>
                    <a class="btn btn-info form-control" href="{{url('/')}}/product_requested_rent">Requested Rents</a><br><br>
                    {{-- <a class="btn btn-info form-control" href="{{url('/')}}/detailed_rent_list">Rent Detailed List</a><br><br> --}}
                </center>
            </div>
          </div>
        </div>
    </div>
    <div class="modal fade" id="OrderManagement">
        <div class="modal-dialog modal-sm modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-body">                
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xl font-weight-bold text-warning text-uppercase mb-1">
                            <center>
                                <h3>
                                    <i class="fas fa-tasks"></i>
                                </h3>                                        
                                <h4>    
                                    <span><b>Order Management</b></span>
                                </h4>
                            </center>
                        </div>
                    </div>
                </div>
                <center>
                    <a class="btn btn-warning form-control" href="{{url('/')}}/converted_leads">Converted Leads</a><br><br>
                    <a class="btn btn-warning form-control" href="{{url('/')}}/pending_for_vendor_approval">Pending For Vendor Approval</a><br><br>
                </center>
            </div>
          </div>
        </div>
    </div>
    <div class="modal fade" id="Delivery1Management">
        <div class="modal-dialog modal-sm modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-body">                
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xl font-weight-bold text-success text-uppercase mb-1">
                            <center>
                                <h3>
                                    <i class="fas fa-tasks"></i>
                                </h3>                                        
                                <h4>    
                                    <span><b>Delivery Management</b></span>
                                </h4>
                            </center>
                        </div>
                    </div>
                </div>
                <center>
                    <a class="btn btn-success form-control" href="{{url('/')}}/AddDelivery">Add New Delivery</a><br><br>
                    <a class="btn btn-success form-control" href="{{url('/')}}/confirmed_delivery">Confirmed Delivery</a><br><br>
                    <a class="btn btn-success form-control" href="#">Deliver Order</a><br><br>
                    <a class="btn btn-success form-control" href="{{url('/')}}/ArchivedDeliveries">Report-Archived</a><br><br>
                    <a class="btn btn-success form-control" href="{{url('/')}}/CompletedDeliveries">Completed</b></a><br><br>
                    <a class="btn btn-success form-control" href="{{url('/')}}/AllDeliveries">All Open</b></a><br><br>
                    <a class="btn btn-success form-control" href="#">Report-Assign Open Tasks</b></a><br><br>
                    <a class="btn btn-success form-control" href="{{url('/')}}/MonthlyDeliveryReport">Date By Report</b></a><br><br>
                </center>
            </div>
          </div>
        </div>
    </div>
    <div class="modal fade" id="UserManagement">
        <div class="modal-dialog modal-sm modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-body">                
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xl font-weight-bold text-info text-uppercase mb-1">
                            <center>
                                <h3>
                                    <i class="fas fa-users"></i>
                                </h3>                                        
                                <h4>    
                                    <span><b>User Management</b></span>
                                </h4>
                            </center>
                        </div>
                    </div>
                </div>
                <center>
                    <a class="btn btn-info form-control" href="{{url('/')}}/add_user">Add Users</a><br><br>
                    <a class="btn btn-info form-control" href="{{url('/')}}/view_all_user">View All Users</a><br><br>
                    <a class="btn btn-info form-control" href="#">Reassigne Permissions </a>
                </center>
            </div>
          </div>
        </div>
    </div>
    <div class="modal fade" id="LeadManagement">
        <div class="modal-dialog modal-sm modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-body">                
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xl font-weight-bold text-warning text-uppercase mb-1">
                            <center>
                                <h3>
                                    <i class="fas fa-users"></i>
                                </h3>                                        
                                <h4>    
                                    <span><b>Lead Management</b></span>
                                </h4>
                            </center>
                        </div>
                    </div>
                </div>
                <center>
                    <a class="btn btn-warning form-control" href="<?php echo url('/');?>/create_lead">Create New</a><br><br>
                    {{-- <a class="collapse-item" href="<?php echo url('/');?>/check_customer">Check Customer</b></a> --}}
                    <a class="btn btn-warning form-control" href="<?php echo url('/');?>/viewAllLeads">View All Leads</b></a><br><br>
                    <a class="btn btn-warning form-control" href="<?php echo url('/');?>/viewInProcessLeads">In Process Leads</b></a><br><br>
                    <a class="btn btn-warning form-control" href="<?php echo url('/');?>/viewConvertedLeads">Converted Leads</b></a><br><br>
                    <a class="btn btn-warning form-control" href="<?php echo url('/');?>/viewClosedLeads">Closed Leads</b></a><br><br>
                </center>
            </div>
          </div>
        </div>
    </div>
    <div class="modal fade" id="ReferralManagement">
        <div class="modal-dialog modal-sm modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-body">                
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xl font-weight-bold text-success text-uppercase mb-1">
                            <center>
                                <h3>
                                    <i class="fas fa-users"></i>
                                </h3>                                        
                                <h4>    
                                    <span><b>Referral Management</b></span>
                                </h4>
                            </center>
                        </div>
                    </div>
                </div>
                <center>
                    <a class="btn btn-success form-control" href="<?php echo url('/');?>/viewAllReferrals">View All referral</a><br><br>
                    <a class="btn btn-success form-control" href="#">Update Referral status</a><br><br>
                </center>
            </div>
          </div>
        </div>
    </div>

    <div class="modal fade" id="renewal_pickup">
        <div class="modal-dialog modal-sm modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-body">                
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xl font-weight-bold text-success text-uppercase mb-1">
                            <center>
                                <h3>
                                    <i class="fas fa-users"></i>
                                </h3>                                        
                                <h4>    
                                    <span><b>renewal and pickup</b></span>
                                </h4>
                            </center>
                        </div>
                    </div>
                </div>
                <center>
                    <a class="btn btn-success form-control" href="{{url('/')}}/renewal_pickup/{{date('Y-m-d')}}">Renewal And Pickup</a><br><br>
                    <a class="btn btn-success form-control" href="{{url('/')}}/renewal_pickup_search">Search Customer Order</a><br><br>
                </center>
            </div>
          </div>
        </div>
    </div>
    
@endsection