<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Inquiry : Create Lead</title>

    <!-- Custom fonts for this template -->    
    @yield('styles')
        {{-- <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script> --}}
        <link href="<?php echo url('/');?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
        {{-- <link
            href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet"> --}}
        <!-- Custom styles for this template -->
        <link href="<?php echo url('/');?>/assets/css/sb-admin-2.min.css" rel="stylesheet">

        <!-- Custom styles for this page -->
        <link href="<?php echo url('/');?>/assets/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

        {{-- stylesheets --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.2/css/bootstrap.min.css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.min.css"/>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
        <link href="<?php echo url('/');?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
        {{-- Scripts --}}
        <script src="{{url('/')}}/assets/vendor/jquery/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="<?php echo url('/');?>/assets/js/jquery.table2excel.js"></script>
    
    
</head>

<body id="page-top">	
		<!-- Page Wrapper -->
        <div id="wrapper">
            {{-- <div class="sidenav"> --}}
                <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion sidenav1" id="accordionSidebar">
                    {{-- <a class="sidebar-brand d-flex align-items-center justify-content-center" style="background-color: white;" href="#">
                        <div class="sidebar-brand-text mx-3" ><img src="<?php echo url('/');?>/assets/images/logo_small.png"></div>
                    </a> --}}
                    <br><br><br>
                    <hr class="sidebar-divider my-0">
                    {{-- <li class="nav-item">
                        <a class="nav-link" href="{{url('/')}}/dashboard_fullfillment">
                            <a class="nav-link" href="#">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                        </a>
                    </li> --}}
                    <hr class="sidebar-divider">
                    @php
                        $role_access = json_decode(session('role_access'));
                    @endphp

                    @section('hot_leads')
                    <li class="nav-item">
                        <a class="nav-link collapsed" href="#" id="hot_leads" data-toggle="collapse" data-target="#hot_leadsCollapse"
                            aria-expanded="true" aria-controls="hot_leadsCollapse"  onClick="myFunction(this.id);">
                            <i class="fas fa-fw fa-cog"></i>
                            <span>Hot Leads Management</span>
                        </a>
                        <div id="hot_leadsCollapse" class="collapse hot_leads" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                            <div class="bg-white py-2 collapse-inner rounded">
                                <a class="collapse-item" href="<?php echo url('/');?>/hot_leads">Hot Leads</a>
                                <a class="collapse-item" href="<?php echo url('/');?>/view_in_process_leads">In Process</a>
                                <a class="collapse-item" href="<?php echo url('/');?>/view_closed_leads">Closed</a>
                            </div>
                        </div>
                    </li>
                    @if(session('role')=='superuser')
                    @show
                    @else
                    @if(in_array('20',$role_access))
                        @show
                    @else
                        @stop
                    @endif
                    @endif
                    
                    {{-- Lead--}}
                    @section('lead')
                        <li class="nav-item">
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" id="leads" data-target="#collapseLeads"
                                aria-expanded="true" aria-controls="collapseLeads"  onClick="myFunction(this.id);">
                                <i class="fas fa-fw fa-cog"></i>
                                <span>Leads</span>
                            </a>
                            <div id="collapseLeads" class="collapse leads<?php if(session('id')!=null && session('id') =='leads' ){echo "show"; }?>" data-parent="#accordionSidebar">
                                <div class="card bg-white py-2 collapse-inner rounded" id="accordion">
                                    {{-- Lead Management--}}
                                    @section('lead_mgmt')
                                        <a class="collapse-item" data-toggle="collapse" data-target="#LeadManagement" aria-controls="LeadManagement" 
                                            role="button" aria-expanded="false" aria-controls="LeadManagement" name="LeadManagement" onClick="myFunction1(this.name);">
                                            <i class="fas fa-fw fa-cog"></i>
                                            <span>Leads Management</span>
                                        </a>
                                        <div class="collapse LeadManagement @if(session('name')!=null && session('name')=='LeadManagement'){{"show"}} @endif" id="LeadManagement" aria-labelledby="LeadManagement" data-parent="#accordion">
                                            <div class=" card bg-white py-2 collapse-inner rounded">
                                                <a class="collapse-item" href="<?php echo url('/');?>/create_lead">Create New</a>
                                                <a class="collapse-item" href="<?php echo url('/');?>/viewAllLeads">View All Leads</b></a>
                                                <a class="collapse-item" href="<?php echo url('/');?>/viewInProcessLeads">In Process Leads</b></a>
                                                <a class="collapse-item" href="<?php echo url('/');?>/viewConvertedLeads">Converted Leads</b></a>
                                                <a class="collapse-item" href="<?php echo url('/');?>/viewClosedLeads">Closed Leads</b></a>
                                            </div>
                                        </div>
                                    @if(session('role')=='superuser')
                                        @show
                                    @else
                                        @if(in_array('2',$role_access))
                                            @show
                                        @else
                                            @stop
                                        @endif
                                    @endif

                                    {{-- JD Lead Management--}}
                                    @section('jd_lead_mgmt')
                                        <a class="collapse-item" data-toggle="collapse" href="#JDLeadManagement" role="button" data-target="#JDLeadManagement" aria-expanded="false" aria-controls="JDLeadManagement" name="JDLeadManagement" onClick="myFunction1(this.name);">
                                            <i class="fas fa-fw fa-cog"></i>
                                            <span>JD Lead Management</span>
                                        </a>
                                        <div class="collapse JDLeadManagement @if(session('name')!=null && session('name')=='JDLeadManagement'){{"show"}} @endif" id="JDLeadManagement" aria-labelledby="#JDLeadManagement" data-parent="#accordion">
                                            <div class=" card bg-white py-2 collapse-inner rounded">
                                                <a class="collapse-item" href="<?php echo url('/');?>/view_all_jd_leads">All Leads</a>
                                                <a class="collapse-item" href="<?php echo url('/');?>/view_all_inprocess_leads">In Progress Leads</a>
                                                {{-- <a class="collapse-item" href="<?php echo url('/');?>/view_all_converted_leads">Converted Leads</a> --}}
                                                <a class="collapse-item" href="<?php echo url('/');?>/view_all_q5c_leads">Converted Leads</a>
                                                <a class="collapse-item" href="<?php echo url('/');?>/view_all_closed_leads">Closed Leads</a>
                                            </div>
                                        </div>
                                    @if(session('role')=='superuser')
                                        @show
                                    @else
                                        @if(in_array('4',$role_access))
                                            @show
                                        @else
                                            @stop
                                        @endif
                                    @endif
                                    
                                    @section('referral_mgmt')
                                        <a  class="collapse-item" data-toggle="collapse"data-target="#ReferralManagement" aria-controls="ReferralManagement" role="button" aria-expanded="false" aria-controls="ReferralManagement" name="ReferralManagement" onClick="myFunction1(this.name);">
                                            <i class="fas fa-fw fa-cog"></i>
                                            <span>Referral Management</span>
                                        </a>
                                        <div class="collapse ReferralManagement @if(session('name')!=null && session('name')=='ReferralManagement'){{"show"}} @endif" id="ReferralManagement" aria-labelledby="ReferralManagment" data-parent="#accordion">
                                            <div class=" card bg-white py-2 collapse-inner rounded">
                                                <a class="collapse-item" href="<?php echo url('/');?>/viewAllReferrals">View All referral</a>
                                                <a class="collapse-item" href="#">Update Referral status</a>
                                            </div>
                                        </div>
                                    @if(session('role')=='superuser')
                                        @show
                                    @else
                                        @if(in_array('3',$role_access))
                                            @show
                                        @else
                                            @stop
                                        @endif
                                    @endif

                                        {{-- Nurses Data--}}
                                    @section('Nurses')
                                    <a class="collapse-item" data-toggle="collapse" href="#Nurses" role="button" data-target="#Nurses" aria-expanded="false" aria-controls="Nurses" name="Nurses" onClick="myFunction1(this.name);">
                                        <i class="fas fa-fw fa-cog"></i>
                                        <span>Referrer Management</span>
                                    </a>
                                    <div class="collapse Nurses @if(session('name')!=null && session('name')=='Nurses'){{"show"}} @endif" id="Nurses" aria-labelledby="#Nurses" data-parent="#accordion">
                                        <div class=" card bg-white py-2 collapse-inner rounded">
                                            <div class="bg-white py-2 collapse-inner rounded">
                                                <a class="collapse-item" href="{{url('/')}}/add_nurse">Add Referrer</a>
                                                <a class="collapse-item" href="{{url('/')}}/view_all_nurse">Referrer Lead</a>
                                                <a class="collapse-item" href="{{url('/')}}/view_inprogress_nurse">In-progress</a>
                                                <a class="collapse-item" href="{{url('/')}}/view_referred_nurse">Referrers</a>
                                                <a class="collapse-item" href="{{url('/')}}/view_closed_nurse">Closed Referrer</a>
                                            </div>
                                        </div>
                                    </div>
                                    @if(session('role')=='superuser')
                                        @show
                                    @else
                                        @if(in_array('16',$role_access))
                                            @show
                                        @else
                                            @stop
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </li>
                    @if(session('role')=='superuser')
                        @show
                    @else
                        @if(in_array('1',$role_access))
                            @show
                        @else
                            @stop
                        @endif
                    @endif
                    {{-- @section('Referrar')
                        <li class="nav-item">
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" id="nurses_data" data-target="#collapsenurses_data"
                                aria-expanded="true" aria-controls="collapsenurses_data"  onClick="myFunction(this.id);">
                                <i class="fas fa-fw fa-cog"></i>
                                <span>Referrer Management</span>
                            </a>
                            <div id="collapsenurses_data" class="collapse nurses_data<?php if(session('id')!=null && session('id') =='jd_lead' ){echo "show"; }?>" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                                <div class="bg-white py-2 collapse-inner rounded">
                                    <a class="collapse-item" href="{{url('/')}}/add_nurse">Add Referrer</a>
                                    <a class="collapse-item" href="{{url('/')}}/view_all_nurse">Referrer Lead</a>
                                    <a class="collapse-item" href="{{url('/')}}/view_inprogress_nurse">In-progress</a>
                                    <a class="collapse-item" href="{{url('/')}}/view_referred_nurse">Referrers</a>
                                    <a class="collapse-item" href="{{url('/')}}/view_closed_nurse">Closed Referrer</a>
                                </div>
                            </div>
                        </li>
                    @if(session('role')=='superuser')
                        @show
                    @else
                        @if(in_array('3',$role_access))
                            @show
                        @else
                            @stop
                        @endif
                    @endif --}}
                    {{-- Vendor--}}
                    @section('vendor')
                        <li class="nav-item">
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" id="vendor" data-target="#collapsevendor"
                                aria-expanded="true" aria-controls="collapsevendor"  onClick="myFunction(this.id);">
                                <i class="fas fa-fw fa-cog"></i>
                                <span>Vendor</span>
                            </a>
                            <div id="collapsevendor" class="collapse vendor<?php if(session('id')!=null && session('id') =='vendor' ){echo "show"; }?>" data-parent="#accordionSidebar">
                                <div class="card bg-white py-2 collapse-inner rounded" id="accordion1">
                                    @section('vendor_mgmt')
                                        <a class="collapse-item" data-toggle="collapse" data-target="#vendormanagement" aria-controls="vendormanagement" role="button" aria-expanded="false" aria-controls="vendormanagement" name="vendormanagement" onClick="myFunction1(this.name);">
                                            <i class="fas fa-fw fa-cog"></i>
                                            <span>Vendor Management</span>
                                        </a>
                                        <div class="collapse vendormanagement @if(session('name')!=null && session('name')=='vendormanagement'){{"show"}} @endif" id="vendormanagement" aria-labelledby="#vendormanagement" data-parent="#accordion1">
                                            <div class=" card bg-white py-2 collapse-inner rounded">
                                                <a class="collapse-item" href="<?php echo url('/');?>/pending_vendors">Pending Vendors</a>
                                                <a class="collapse-item" href="<?php echo url('/');?>/approved_vendors">Approved Vendors</a>
                                                <a class="collapse-item" href="<?php echo url('/');?>/rejected_vendors">Awaiting re-submission</a>
                                                <a class="collapse-item" href="<?php echo url('/');?>/requested_vendors">Resubmitted</a>                                  
                                            </div>
                                        </div>
                                    @if(session('role')=='superuser')
                                        @show
                                    @else
                                        @if(in_array('6',$role_access))
                                            @show
                                        @else
                                            @stop
                                        @endif
                                    @endif

                                    @section('vendor_inveentory_mgmt')
                                        <a class="collapse-item" data-toggle="collapse" data-target="#VendorInventoryManagement" aria-controls="VendorInventoryManagement" role="button" aria-expanded="false" aria-controls="VendorInventoryManagement" name="VendorInventoryManagement" onClick="myFunction1(this.name);">
                                            <i class="fas fa-fw fa-cog"></i>
                                            <span>Vendor Inventory mgmt</span>
                                        </a>
                                        <div class="collapse VendorInventoryManagement @if(session('name')!=null && session('name')=='VendorInventoryManagement'){{"show"}} @endif" id="VendorInventoryManagement" aria-labelledby="#VendorInventoryManagement" data-parent="#accordion1">
                                            <div class=" card bg-white py-2 collapse-inner rounded">
                                                <a class="collapse-item" href="{{url('/')}}/product_request">Rent approval</a>
                                                <a class="collapse-item" href="{{url('/')}}/product_approved_rent">Approved Rents</a>
                                                <a class="collapse-item" href="{{url('/')}}/product_rejected_rent">Rejected Rents</a>
                                                <a class="collapse-item" href="{{url('/')}}/product_requested_rent">Requested Rents</a>
                                                {{-- <a class="collapse-item" href="{{url('/')}}/detailed_rent_list">Rent Detailed List</a> --}}   
                                            </div>
                                        </div>
                                    @if(session('role')=='superuser')
                                        @show
                                    @else
                                        @if(in_array('7',$role_access))
                                            @show
                                        @else
                                            @stop
                                        @endif
                                        @endif
                                </div>
                            </div>
                        </li>
                    @if(session('role')=='superuser')
                        @show
                    @else
                        @if(in_array('5',$role_access))
                            @show
                        @else
                            @stop
                        @endif
                    @endif

                    {{--Product mgmt--}}
                    @section('product_mgmt')
                        <li class="nav-item">
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" id="Product" data-target="#collapseProduct"
                                aria-expanded="true" aria-controls="collapseProduct"  onClick="myFunction(this.id);">
                                <i class="fas fa-fw fa-cog"></i>
                                <span>Product Management</span>
                            </a>
                            <div id="collapseProduct" class="collapse Product<?php if(session('id')!=null && session('id') =='Product' ){echo "show"; }?>" data-parent="#accordionSidebar">
                                <div class="card bg-white py-2 collapse-inner rounded" id="accordion">
                                    @section('master_product_mgmt')
                                        <a class="collapse-item" data-toggle="collapse" href="#MasterProductManagment" data-target="#MasterProductManagment" role="button" aria-expanded="false" aria-controls="MasterProductManagment" name="MasterProductManagment" onClick="myFunction1(this.name);">
                                            <i class="fas fa-fw fa-cog"></i>
                                            <span>Master Product mgmt</span>
                                        </a>
                                        <div class="collapse MasterProductManagment @if(session('name')!=null && session('name')=='MasterProductManagment'){{"show"}} @endif" id="MasterProductManagment" aria-labelledby="MasterProductManagment" data-target="MasterProductManagment">
                                            <div class=" card bg-white py-2 collapse-inner rounded">
                                                <a class="collapse-item" href="{{url('/')}}/add_new_product">Add New Product</a>
                                                <a class="collapse-item" href="{{url('/')}}/view_master_products">View Master Products</a>                            
                                                {{-- <a class="collapse-item" href="{{url('/')}}/detailed_rent_list">Rent Detailed List</a> --}}        
                                            </div>
                                        </div>
                                    @if(session('role')=='superuser')
                                        @show
                                    @else
                                        @if(in_array('9',$role_access))
                                            @show
                                        @else
                                            @stop
                                        @endif
                                        @endif
                                    
                                </div>
                            </div>
                        </li>
                    @if(session('role')=='superuser')
                        @show
                    @else
                        @if(in_array('8',$role_access))
                            @show
                        @else
                            @stop
                        @endif
                        @endif

                    {{-- ORder Mgmt--}}
                    @section('order_mgmt')
                        <li class="nav-item">
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" id="Order" data-target="#collapseOrder"
                                aria-expanded="true" aria-controls="collapseOrder"  onClick="myFunction(this.id);">
                                <i class="fas fa-fw fa-cog"></i>
                                <span>Order</span>
                            </a>
                            <div id="collapseOrder" class="collapse Order<?php if(session('id')!=null && session('id') =='Order' ){echo "show"; }?>" data-parent="#accordionSidebar">
                                <div class="card bg-white py-2 collapse-inner rounded">
                                    <a class="collapse-item" href="<?php echo url('/');?>/order_converted_leads">Orders</a>
                                    {{-- <a class="collapse-item" href="<?php echo url('/');?>/mobileAppLeads">Mobile Generated Leads</a> --}}
                                    <a class="collapse-item" href="<?php echo url('/');?>/pendingAssignment">Pending Assignments</a>
                                    <a class="collapse-item" href="<?php echo url('/');?>/pending_for_vendor_approval">Pending For Vendor <br>Approval</a>
                                    <a class="collapse-item" href="{{url('/')}}/approved_orders">Approved Orders</a>
                                    <a class="collapse-item" href="{{url('/')}}/rejected_orders">Rejected Orders</a>
                                    {{-- @section('delivery_mgmt')
                                        <a class="collapse-item" data-toggle="collapse" href="#DeliveryManagement" role="button" aria-expanded="false" aria-controls="DeliveryManagement">
                                            <i class="fas fa-fw fa-cog"></i>
                                            <span>Deliverys Management</span>
                                        </a>
                                        <div class="collapse" id="DeliveryManagement">
                                            <div class=" card bg-white py-2 collapse-inner rounded">
                                                <a class="collapse-item" href="<?php echo url('/');?>/AddDelivery">Add New Delivery</a>
                                                <a class="collapse-item" href="<?php echo url('/');?>/confirmed_delivery">Confirm Delivery</a>
                                                <a class="collapse-item" href="<?php echo url('/');?>/ModifyDeliveryView">Modify Delivery</a>
                                                <hr class="">
                                                <a class="collapse-item" href="#">Deliver Order</a>
                                                <a class="collapse-item" href="<?php echo url('/');?>/ArchivedDeliveries">Report-Archived</a>
                                                <a class="collapse-item" href="<?php echo url('/');?>/CompletedDeliveries">Completed</b></a>
                                                <a class="collapse-item" href="<?php echo url('/');?>/AllDeliveries">All Open</b></a>
                                                <a class="collapse-item" href="#">Report-Assign Open Tasks</b></a>
                                                <a class="collapse-item" href="<?php echo url('/');?>/MonthlyDeliveryReport">Date By Report</b></a>    
                                            </div>
                                        </div>
                                    @if(session('role')=='superuser')
                                        @show
                                    @else
                                        @if(in_array('11',$role_access))
                                            @show
                                        @else
                                            @stop
                                        @endif
                                        @endif --}}
                                    
                                </div>
                            </div>
                        </li>
                    @if(session('role')=='superuser')
                        @show
                    @else
                        @if(in_array('17',$role_access))
                            @show
                        @else
                            @stop
                        @endif
                    @endif


                    @section('delivery_mgmt')
                        <li class="nav-item">
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" id="delivery" data-target="#DeliveryManagement"
                                aria-expanded="true" aria-controls="DeliveryManagement" onClick="myFunction(this.id);">
                                <i class="fas fa-fw fa-cog"></i>
                                <span>Delivery Management</span>
                            </a>
                            <div id="DeliveryManagement" class="collapse delivery" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                                <div class="bg-white py-2 collapse-inner rounded">
                                    <a class="collapse-item" href="<?php echo url('/');?>/AddDelivery">Add New Delivery</a>
                                    <a class="collapse-item" href="<?php echo url('/');?>/confirmed_delivery">Confirm Delivery</a>
                                    <a class="collapse-item" href="<?php echo url('/');?>/ModifyDeliveryView">Modify Delivery</a>
                                    <a class="collapse-item" href="<?php echo url('/');?>/pickup_request">Pickup Request</a>
                                    <a class="collapse-item" href="<?php echo url('/');?>/renew_request">Collection Request</a>
                                    {{-- <hr> --}}
                                </div>
                            </div>
                        </li> 
                    @if(session('role')=='superuser')
                        @show
                    @else
                        @if(in_array('11',$role_access))
                            @show
                        @else
                            @stop
                        @endif
                    @endif

                    

                    {{-- @section('Referral')
                        <li class="nav-item">
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" id="Referral" data-target="#collapseReferral"
                                aria-expanded="true" aria-controls="collapseReferral"  onClick="myFunction(this.id);">
                                <i class="fas fa-fw fa-cog"></i>
                                <span>Referral Management</span>
                            </a>
                            <div id="collapseReferral" class="collapse Referral<?php if(session('id')!=null && session('id') =='Referral' ){echo "show"; }?>" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                                <div class="bg-white py-2 collapse-inner rounded">
                                    <a class="collapse-item" href="<?php echo url('/');?>/viewAllReferrals">View All referral</a>
                                    <a class="collapse-item" href="#">Update Referral status</a>
                                </div>
                            </div>
                        </li>
                    @if(session('role')=='superuser')
                        @show
                    @else
                        @if(in_array('18',$role_access))
                            @show
                        @else
                            @stop
                        @endif
                    @endif --}}

                        {{-- @section('lead_mgmt') --}}
                        {{-- <li class="nav-item">
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" id="leads_new" data-target="#collapseLeads_new"
                                aria-expanded="true" aria-controls="collapseLeads_new"  onClick="myFunction(this.id);">
                                <i class="fas fa-fw fa-cog"></i>
                                <span>Leads Management</span>
                            </a>
                            <div id="collapseLeads_new" class="collapse leads_new<?php if(session('id')!=null && session('id') =='leads' ){echo "show"; }?>" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                                <div class="bg-white py-2 collapse-inner rounded">
                                    <a class="collapse-item" href="<?php echo url('/');?>/create_lead">Create New</a>
                                    <a class="collapse-item" href="<?php echo url('/');?>/viewAllLeads">View All Leads</b></a>
                                    <a class="collapse-item" href="<?php echo url('/');?>/viewInProcessLeads">In Process Leads</b></a>
                                    <a class="collapse-item" href="<?php echo url('/');?>/viewConvertedLeads">Converted Leads</b></a>
                                    <a class="collapse-item" href="<?php echo url('/');?>/viewClosedLeads">Closed Leads</b></a>
                                </div>
                            </div>
                        </li> --}}
                        {{-- <li class="nav-item">
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" id="leads" data-target="#collapseLeads"
                                aria-expanded="true" aria-controls="collapseLeads"  onClick="myFunction(this.id);">
                                <i class="fas fa-fw fa-cog"></i>
                                <span>Leads Management</span>
                            </a>
                            <div id="collapseLeads" class="collapse leads<?php if(session('id')!=null && session('id') =='leads' ){echo "show"; }?>" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                                <div class="bg-white py-2 collapse-inner rounded">
                                    <a class="collapse-item" href="<?php echo url('/');?>/create_lead">Create New</a>
                                    <a class="collapse-item" href="<?php echo url('/');?>/viewAllLeads">View All Leads</b></a>
                                    <a class="collapse-item" href="<?php echo url('/');?>/viewInProcessLeads">In Process Leads</b></a>
                                    <a class="collapse-item" href="<?php echo url('/');?>/viewConvertedLeads">Converted Leads</b></a>
                                    <a class="collapse-item" href="<?php echo url('/');?>/viewClosedLeads">Closed Leads</b></a>
                                </div>
                            </div>
                        </li> --}}
                    {{-- @if(session('role')=='superuser')
                        @show
                    @else
                        @if(in_array('1',$role_access))
                            @show
                        @else
                            @stop
                        @endif
                     @endif --}}
                     
                    {{-- @section('referral_mgmt')
                        <li class="nav-item">
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" id="referral" data-target="#collapseReferral"
                                aria-expanded="true" aria-controls="collapseReferral"  onClick="myFunction(this.id);">
                                <i class="fas fa-fw fa-cog"></i>
                                <span>Referral Management</span>
                            </a>
                            <div id="collapseReferral" class="collapse referral<?php if(session('id')!=null && session('id') =='referral' ){echo "show"; }?>" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                                <div class="bg-white py-2 collapse-inner rounded">
                                    <a class="collapse-item" href="<?php echo url('/');?>/viewAllReferrals">View All referral</a>
                                    <a class="collapse-item" href="#">Update Referral status</a>
                                </div>
                            </div>
                        </li>
                    @if(session('role')=='superuser')
                        @show
                    @else
                        @if(in_array('2',$role_access))
                            @show
                        @else
                            @stop
                        @endif
                    @endif
                    
                    @section('JD_lead')
                        <li class="nav-item">
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" id="jd_lead" data-target="#collapseJDlead"
                                aria-expanded="true" aria-controls="collapseJDlead"  onClick="myFunction(this.id);">
                                <i class="fas fa-fw fa-cog"></i>
                                <span>JD Lead Management</span>
                            </a>
                            <div id="collapseJDlead" class="collapse jd_lead<?php if(session('id')!=null && session('id') =='jd_lead' ){echo "show"; }?>" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                                <div class="bg-white py-2 collapse-inner rounded">
                                    <a class="collapse-item" href="<?php echo url('/');?>/view_all_jd_leads">All Leads</a>
                                    <a class="collapse-item" href="<?php echo url('/');?>/view_all_inprocess_leads">In-Process Leads</a>
                                    <a class="collapse-item" href="<?php echo url('/');?>/view_all_converted_leads">Converted Leads</a>
                                    <a class="collapse-item" href="<?php echo url('/');?>/view_all_q5c_leads">Q5C</a>
                                </div>
                            </div>
                        </li>
                    @if(session('role')=='superuser')
                        @show
                    @else
                        @if(in_array('3',$role_access))
                            @show
                        @else
                            @stop
                        @endif
                    @endif

                    @section('vendor_mgmt')
                        <li class="nav-item">
                            <a class="nav-link collapsed" href="#" id="vendor" data-toggle="collapse" data-target="#collapseOne"
                                aria-expanded="true" aria-controls="collapseOne"  onClick="myFunction(this.id);">
                                <i class="fas fa-fw fa-cog"></i>
                                <span>Vendor Management</span>
                            </a>
                            <div id="collapseOne" class="collapse vendor" aria-labelledby="headingOne" data-parent="#accordionSidebar">
                                <div class="bg-white py-2 collapse-inner rounded">
                                    <a class="collapse-item" href="<?php echo url('/');?>/pending_vendors">Pending Vendors</a>
                                    <a class="collapse-item" href="<?php echo url('/');?>/approved_vendors">Approved Vendors</a>
                                    <a class="collapse-item" href="<?php echo url('/');?>/rejected_vendors">Rejected Vendors</a>
                                    <a class="collapse-item" href="<?php echo url('/');?>/requested_vendors">Requested Vendors</a>                                  
                                </div>
                            </div>
                        </li>
                    @if(session('role')=='superuser')
                        @show
                    @else
                        @if(in_array('4',$role_access))
                            @show
                        @else
                            @stop
                        @endif
                    @endif

                    @section('vendor_inventory_mgmt')
                        <li class="nav-item">
                            <a class="nav-link collapsed" href="#" id="VendorInvtMgmt" data-toggle="collapse" data-target="#collapseVendorInvtMgmt"
                                aria-expanded="true" aria-controls="collapseVendorInvtMgmt"  onClick="myFunction(this.id);">
                                <i class="fas fa-fw fa-cog"></i>
                                <span>Vendor Inventory Mgmt</span   >
                            </a>
                            <div id="collapseVendorInvtMgmt" class="collapse VendorInvtMgmt" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                                <div class="bg-white py-2 collapse-inner rounded"> 
                                    
                                    <a class="collapse-item" href="{{url('/')}}/product_request">Pending Rent Requests</a>
                                    <a class="collapse-item" href="{{url('/')}}/product_approved_rent">Approved Rents</a>
                                    <a class="collapse-item" href="{{url('/')}}/product_rejected_rent">Rejected Rents</a>
                                    <a class="collapse-item" href="{{url('/')}}/product_requested_rent">Requested Rents</a>
                                    
                                                                
                                </div>
                            </div>
                        </li>
                    @if(session('role')=='superuser')
                        @show
                    @else
                        @if(in_array('5',$role_access))
                            @show
                        @else
                            @stop
                        @endif
                    @endif


                    @section('master_product_mgmt')
                        <li class="nav-item">
                            <a class="nav-link collapsed" href="#" id="master_product" data-toggle="collapse" data-target="#collapsemaster_product"
                                aria-expanded="true" aria-controls="collapsemaster_product"  onClick="myFunction(this.id);">
                                <i class="fas fa-fw fa-cog"></i>
                                <span>Master Product Mgmt</span   >
                            </a>
                            <div id="collapsemaster_product" class="collapse master_product" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                                <div class="bg-white py-2 collapse-inner rounded">
                                    <a class="collapse-item" href="{{url('/')}}/add_new_product">Add New Product</a>
                                    <a class="collapse-item" href="{{url('/')}}/view_master_products">View Master Products</a>                            
                                </div>
                            </div>
                        </li>
                    @if(session('role')=='superuser')
                        @show
                    @else
                        @if(in_array('6',$role_access))
                            @show
                        @else
                            @stop
                        @endif
                    @endif

                    @section('order_mgmt')
                        <li class="nav-item">
                            <a class="nav-link collapsed" href="#" id="orders" data-toggle="collapse" data-target="#ordersCollapse"
                                aria-expanded="true" aria-controls="ordersCollapse"  onClick="myFunction(this.id);">
                                <i class="fas fa-fw fa-cog"></i>
                                <span>Order Management</span   >
                            </a>
                            <div id="ordersCollapse" class="collapse orders" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                                <div class="bg-white py-2 collapse-inner rounded">
                                    <a class="collapse-item" href="<?php echo url('/');?>/converted_leads">Converted Leads</a>
                                    <a class="collapse-item" href="<?php echo url('/');?>/mobileAppLeads">Mobile Generated Leads</a>
                                    <a class="collapse-item" href="<?php echo url('/');?>/pending_for_vendor_approval">Pending For Vendor <br>Approval</a>
                                </div>
                            </div>
                        </li>
                    @if(session('role')=='superuser')
                        @show
                    @else
                        @if(in_array('7',$role_access))
                            @show
                        @else
                            @stop
                        @endif
                    @endif

                    @section('delivery_mgmt')
                        <li class="nav-item">
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" id="delivery" data-target="#DeliveryManagement"
                                aria-expanded="true" aria-controls="DeliveryManagement" onClick="myFunction(this.id);">
                                <i class="fas fa-fw fa-cog"></i>
                                <span>Delivery Management</span>
                            </a>
                            <div id="DeliveryManagement" class="collapse delivery" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                                <div class="bg-white py-2 collapse-inner rounded">
                                    <a class="collapse-item" href="<?php echo url('/');?>/AddDelivery">Add New Delivery</a>
                                    <a class="collapse-item" href="<?php echo url('/');?>/confirmed_delivery">Confirm Delivery</a>
                                    <hr class="">
                                    <a class="collapse-item" href="#">Deliver Order</a>
                                    <a class="collapse-item" href="<?php echo url('/');?>/ArchivedDeliveries">Report-Archived</a>
                                    <a class="collapse-item" href="<?php echo url('/');?>/CompletedDeliveries">Completed</b></a>
                                    <a class="collapse-item" href="<?php echo url('/');?>/AllDeliveries">All Open</b></a>
                                    <a class="collapse-item" href="#">Report-Assign Open Tasks</b></a>
                                    <a class="collapse-item" href="<?php echo url('/');?>/MonthlyDeliveryReport">Date By Report</b></a>
                                </div>
                            </div>
                        </li> -->
                    @if(session('role')=='superuser')
                        @show
                    @else
                        @if(in_array('8',$role_access))
                            @show
                        @else
                            @stop
                        @endif
                    @endif --}}

                    @section('renewal_pickup')
                        <li class="nav-item">
                            <a class="nav-link collapsed" href="#" id="renew_pickup" data-toggle="collapse" data-target="#renewpickupCollapse"
                                aria-expanded="true" aria-controls="renewpickupCollapse"  onClick="myFunction(this.id);">
                                <i class="fas fa-fw fa-cog"></i>
                                <span>Renewal & Pickup</span   >
                            </a>
                            <div id="renewpickupCollapse" class="collapse renew_pickup" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                                <div class="bg-white py-2 collapse-inner rounded">
                                    <a class="collapse-item" href="{{url('/')}}/renewal_pickup">Renewal And Pickup</a>
                                    <a class="collapse-item" href="{{url('/')}}/renewal_pickup_search">Search Customer Order</a>
                                </div>
                            </div>
                        </li>
                    @if(session('role')=='superuser')
                        @show
                    @else
                        @if(in_array('12',$role_access))
                            @show
                        @else
                            @stop
                        @endif
                    @endif
                    
                    @section('billing_payment')
                        <li class="nav-item">
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" id="billing_payment" data-target="#collapseBilling_payment"
                                aria-expanded="true" aria-controls="collapseBilling_payment"  onClick="myFunction(this.id);">
                                <i class="fas fa-fw fa-cog"></i>
                                <span>Billing & Payment</span>
                            </a>
                            <div id="collapseBilling_payment" class="collapse billing_payment<?php if(session('id')!=null && session('id') =='billing_payment' ){echo "show"; }?>" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                                <div class="bg-white py-2 collapse-inner rounded">
                                    <a class="collapse-item" href="{{url('/')}}/pending_online_renew">Pending Online Renew</a>
                                    {{-- <a class="collapse-item" href="{{url('/')}}/show_pedning_payments">Pending Payments</a> --}}
                                    <a class="collapse-item" href="#">Pending Payments</a>
                                </div>
                            </div>
                        </li>
                    @if(session('role')=='superuser')
                        @show
                    @else
                        @if(in_array('13',$role_access))
                            @show
                        @else
                            @stop
                        @endif
                    @endif
                    
                    @section('reports')
                        <li class="nav-item">
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" id="Reports" data-target="#collapseReports"
                                aria-expanded="true" aria-controls="collapseReports"  onClick="myFunction(this.id);">
                                <i class="fas fa-fw fa-cog"></i>
                                <span>Reports</span>
                            </a>
                            <div id="collapseReports" class="collapse Reports<?php if(session('id')!=null && session('id') =='Reports' ){echo "show"; }?>" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                                <div class="bg-white py-2 collapse-inner rounded">
                                    <a class="collapse-item" href="{{url('/')}}/leads_reports">Leads Report</a>
                                    <a class="collapse-item" href="{{url('/')}}/equipment_report">Equipment Report</a>
                                    <a class="collapse-item" href="{{url('/')}}/vendor_product_report">Vendor Product Report</a>
                                    <a class="collapse-item" href="{{url('/')}}/mis_reports">MIS Report</a>
                                    <a class="collapse-item" href="{{url('/')}}/daybyday_report">Day by Day Report</a>
                                    <a class="collapse-item" data-toggle="collapse" href="#DeliveryManagement1" data-target="#DeliveryManagement1" role="button" aria-expanded="false" aria-controls="DeliveryManagement1" name="DeliveryManagement1" onClick="myFunction1(this.name);">
                                        <i class="fas fa-fw fa-cog"></i>
                                        <span>Delivery Management</span>
                                    </a>
                                    <div class="collapse DeliveryManagement1 @if(session('name')!=null && session('name')=='DeliveryManagement1'){{"show"}} @endif" id="DeliveryManagement1" aria-labelledby="DeliveryManagement1" data-target="DeliveryManagement1">
                                        <div class=" card bg-white py-2 collapse-inner rounded">
                                            <a class="collapse-item" href="{{url('/')}}/order_feedback">Feedback</a>
                                            <a class="collapse-item" href="#">Deliver Order</a>
                                            <a class="collapse-item" href="<?php echo url('/');?>/ArchivedDeliveries">Report-Archived</a>
                                            <a class="collapse-item" href="<?php echo url('/');?>/CompletedDeliveries">Completed</b></a>
                                            <a class="collapse-item" href="<?php echo url('/');?>/AllDeliveries">All Open</b></a>
                                            <a class="collapse-item" href="#">Report-Assign Open Tasks</b></a>
                                            <a class="collapse-item" href="<?php echo url('/');?>/MonthlyDeliveryReport">Date By Report</b></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @if(session('role')=='superuser')
                        @show
                    @else
                        @if(in_array('14',$role_access))
                            @show
                        @else
                            @stop
                        @endif
                    @endif

                    @section('user_mgmt')
                        <li class="nav-item">
                            <a class="nav-link collapsed" href="#" id="users" data-toggle="collapse" data-target="#collapseThree"
                                aria-expanded="true" aria-controls="collapseThree" onClick="myFunction(this.id);">
                                <i class="fas fa-fw fa-user"></i>
                                <span>User Management</span>
                            </a>
                            <div id="collapseThree" class="collapse  users" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                                <div class="bg-white py-2 collapse-inner rounded">
                                    @if (session('role')=='superuser')
                                        <a class="collapse-item" href="<?php echo url('/');?>/add_user">Add Users</a>    
                                    @elseif(session('role')=='admin')
                                        {{""}}
                                    @endif
                                    <a class="collapse-item" href="<?php echo url('/');?>/view_all_user">View All Users</a>
                                    <a class="collapse-item" href="#">Reassigne Permissions </a>
                                                                        
                                </div>
                            </div>
                        </li>
                    @if(session('role')=='superuser')
                        @show
                    @else
                        @if(in_array('15',$role_access))
                            @show
                        @else
                            @stop
                        @endif
                    @endif
                    {{-- @section('task_mgmt')
                        <li class="nav-item">
                            <a class="nav-link collapsed" href="#" id="task" data-toggle="collapse" data-target="#collapseTask"
                                aria-expanded="true" aria-controls="collapseTask" onClick="myFunction(this.id);">
                                <i class="fas fa-fw fa-cog"></i>
                                <span>Task Management</span>
                            </a>
                            <div id="collapseTask" class="collapse  task" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                                <div class="bg-white py-2 collapse-inner rounded">

                                    <a class="collapse-item" href="<?php echo url('/');?>/add_project_task">Add Project</a>
                                    <a class="collapse-item" href="<?php echo url('/');?>/viewAllProjects">My Projects</a>
                                    <a class="collapse-item" href="<?php echo url('/');?>/add_new_task">Add Tasks</a>
                                    <a class="collapse-item" href="<?php echo url('/');?>/my_task">My Tasks</a>
                                                                        
                                </div>
                            </div>
                        </li>
                    @if(session('role')=='superuser')
                        @show
                    @else
                        @if(in_array('19',$role_access))
                            @show
                        @else
                            @stop
                        @endif
                    @endif --}}
                    
                    <hr class="sidebar-divider d-none d-md-block">
                    {{-- <div class="text-center d-none d-md-inline">
                        <button class="rounded-circle border-0" id="sidebarToggle"></button>
                    </div> --}}
                </ul>
            {{-- </div> --}}
            <div id="content-wrapper" class="d-flex flex-column">
                <div id="content">
                    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 fixed-top static-top shadow">
                        <form class="form-inline">
                            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                                <i class="fa fa-bars"></i>
                            </button>
                        </form>
                        <ul class="navbar-nav">
                            <a class="sidebar-brand d-flex align-items-center justify-content-center" style="background-color: white;" href="#">
                                <div class="sidebar-brand-text mx-3" ><img src="<?php echo url('/');?>/assets/images/logo_small.png"></div>
                            </a>
                        </ul>
                        <ul class="navbar-nav ml-auto">
                            <input type="hidden" name="count" id="count">
                            {{-- <button type="button" class="btn btn-primary btn-sm form-control" style="margin-top: 20px;">
                                Lead Notifications <span id="lead_notifications" class="badge badge-light">0</span>
                            </button> --}}
                            @if(session('role')=='superuser' || session('role')=='admin')
                                <a class="btn btn-primary btn-sm form-control" style="margin-top: 20px;" href="{{url('/')}}/pendingAssignment">
                                    Pending Assignments <span id="pendingAssignments" class="badge badge-light">0</span>
                                </a>
                            @endif
                            &emsp;
                            <button type="button" class="btn btn-primary btn-sm form-control" style="margin-top: 20px;">
                                Lead Notifications <span id="lead_notifications" class="badge badge-light">0</span>
                            </button>
                            <div class="topbar-divider d-none d-sm-block"></div>
                            <li class="nav-item dropdown no-arrow">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{session('username')}}</span>
                                    <img class="img-profile rounded-circle"
                                        src="<?php echo url('/');?>/assets/img/undraw_profile.svg">
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                    aria-labelledby="userDropdown">
                                    <a class="dropdown-item" href="<?php echo url('/');?>/view_profile">
                                        <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                        Profile
                                    </a>
                                    <a class="dropdown-item" href="#">
                                        <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                        Settings
                                    </a>
                                    <a class="dropdown-item" href="#">
                                        <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                        Activity Log
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="<?php echo url('/');?>/logout">
                                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                        Logout
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </nav>
                    <br><br><br><br>
                    <div class="main">
                        @yield('content')
                    </div>
                </div>            
            </div>        
        </div>  
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    <!-- Bootstrap core JavaScript-->
    {{-- <script src="<?php echo url('/');?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script> --}}

    <!-- Core plugin JavaScript-->
    {{-- <script src="<?php echo url('/');?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script> --}}

    <!-- Custom scripts for all pages-->
    {{-- <script src="<?php echo url('/');?>/assets/js/sb-admin-2.min.js"></script> --}}

    <!-- Page level plugins -->
    <script src="<?php echo url('/');?>/assets/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo url('/');?>/assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    {{-- <script src="<?php echo url('/');?>/assets/js/demo/datatables-demo.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script> --}}
    <script>
        $(document).ready(function() {
            $.ajax({
                url : "{{url('/')}}/pendingAssignmentsNotify",
                type : "GET",
                success : function(lead_count)
                {
                    $("#pendingAssignments").text(lead_count);
                }
            });
            $('#records').DataTable();
            $.ajax({
                    url : "{{url('/')}}/getCount",
                    type : "GET",
                    success : function(html)
                    {
                        $("#count").val(html);
                    }
            });
        } );
        function myFunction(id)
        {
            //var id = this.id;
            //alert(id);
            // {{session(['id'=>'<script>id</script>'])}}
            localStorage['id'] = ''+id;
            //alert(localStorage['id']);
        }
        if(localStorage['id'] != null) 
        {
            var id = localStorage['id'];
            $('.'+id).addClass('show');
            
        }
        function myFunction1(name)
        {
            //var name = this.name;
            //alert(name);
            // {{session(['name'=>'<script>name</script>'])}}
            localStorage['name'] = ''+name;
            //alert(localStorage['name']);
        }
        if(localStorage['name'] != null) 
        {
            var name = localStorage['name'];
            $('.'+name).addClass('show');
            
        }
        
        function charger() 
        {
            setTimeout( function()
            {
                var count = $('#count').val();
                $.ajax({
                    url : "{{url('/')}}/notifications/"+count+"",
                    type : "GET",
                    success : function(html)
                    {
                        $("#lead_notifications").text(html);
                    }
                });
                $.ajax({
                    url : "{{url('/')}}/pendingAssignmentsNotify",
                    type : "GET",
                    success : function(lead_count)
                    {
                        $("#pendingAssignments").text(lead_count);
                    }
                });

                charger();

            },5000);
        }

        charger();
	</script>
    @yield('script')
</body>

</html>