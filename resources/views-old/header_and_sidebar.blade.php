<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Quali55Care</title>

    <!-- Custom fonts for this template-->
    <link href="{{url('/')}}/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <!-- Custom styles for this page -->
    <link href="<?php echo url('/');?>/assets/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    
    {{-- stylesheets --}}
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.2/css/bootstrap.min.css"/> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <!-- Custom styles for this template-->
    <link href="{{url('/')}}/assets/css/sb-admin-2.min.css" rel="stylesheet">   
    <style>
        .svg1 {
        background-color: #ffffff;
        border-radius: 50%;
        animation: spin 3s ease infinite alternate;
        }

        /*Give each dot a radius of 20*/
        .shape {
        r: 20;
        }

        /*Give each dot its positioning and set the default animation and color for each */
        .shape:nth-child(1) {
        cy: 50;
        cx: 50;
        fill: #c20f00;
        animation: movein 3s ease infinite alternate;
        }
        .shape:nth-child(2) {
        cy: 50;
        cx: 150;
        fill: #ffdd22;
        animation: movein 3s ease infinite alternate;
        }
        .shape:nth-child(3) {
        cy: 150;
        cx: 50;
        fill: #2374c6;
        animation: movein 3s ease infinite alternate;
        }
        .shape:nth-child(4) {
        cy: 150;
        cx: 150;
        fill: #000000;
        animation: movein 3s ease infinite alternate;
        }

        /* Put the two interface options at the bottom of the screen */
        .control-panel {
        position: fixed;
        bottom: 5px;
        display: flex;
        align-items: center;
        }

        /* Set color and placement of labels */
        .switch-label {
        display: inline-block;
        color: #000000;
        margin: 5px;
        }

        /* Set area of switches */
        .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 25px;
        }

        /* Get rid of checkbox defaults */
        .switch input {
        opacity: 0;
        width: 0;
        height: 0;
        }

        /* Create the slider */
        .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ffdd22;
        -webkit-transition: 0.4s;
        transition: 0.4s;
        }
        .slider:before {
        position: absolute;
        content: "";
        height: 19px;
        width: 19px;
        left: 3px;
        bottom: 3px;
        background-color: #000000;
        -webkit-transition: 0.4s;
        transition: 0.4s;
        }

        /* Change color on checked */
        input:checked + .slider {
        background-color: #c20f00;
        }

        /* Set second color change */
        .movement input:checked + .slider {
        background-color: #2374c6;
        }

        /* Set the focus to same color as when checked*/
        input:focus + .slider {
        box-shadow: 0 0 3px #c20f00;
        }
        .movement input:focus + .slider {
        box-shadow: 0 0 3px #2374c6;
        }

        /* Actually move the slider when checked*/
        input:checked + .slider:before {
        -webkit-transform: translateX(25px);
        -ms-transform: translateX(25px);
        transform: translateX(25px);
        }

        /* Round the sliders */
        .slider.round {
        border-radius: 34px;
        }
        .slider.round:before {
        border-radius: 50%;
        }
        .sidebar{
            position: absolute;
            z-index: 4;
        }

        /*Spin the svg so all the dots spin*/
        @keyframes spin {
        to {
            transform: rotate(1turn);
        }
        }

        /* Move all the dots toward the center */
        @keyframes movein {
        to {
            cy: 100;
            cx: 100;
        }
        }

        /* Set a bouncy ball type movement for the dots */
        @keyframes moveup {
        to {
            cy: 20;
        }
        }
        @import url(https://fonts.googleapis.com/css?family=Roboto:300,700);
        /* CSS for smaller view spaces */
        @media screen and (max-width: 768px) {

            /* hide table headings */
            .jim-table-responsive table thead {
                display: none;
            }

            /* treat rows like divs */
            .jim-table-responsive table tr {
                display: block;
                border-top: 2px solid lightgray; /* separate row data with thicker line */
                margin-top: 5px;
            }

            /* treat columns like divs */
            .jim-table-responsive table td {
                display: block;
                text-align: right; /* text to right */
            }

            /* this part is ugly, but necessary to show label on left */
            .jim-table-responsive table td:before {
                content: attr(data-label);
                float: left; /* label to left */
                font-weight: 700;
            }
            .card_img
            {
                height: auto;
                width: auto;
            }
            .selectto .dropdown-menu{
                max-width: 300px!important;
            }
        }
        .card_img
            {
                height: 80;
                width: 200;
            }
        .sidebar.toggled 
        {
            overflow: visible;
            width: 0rem!important;
        }
        thead
        {
         background:#4e73df;   
         color: rgb(255, 255, 255);
        }
    </style>
    @yield('styles')

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            {{-- <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">SB Admin <sup>2</sup></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link nav-link-hide" href="index.html">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li> --}}

            <!-- Divider -->
            <br><br><br>
            <li class="nav-item collapsed">
                <a class="nav-link nav-link-hide" href="{{url('/')}}/dashboard" id="dashboard">
                    {{-- <i class="fas fa-fw fa-cog"></i> --}}
                    <center><span><h4>Dashboard</h4></span></center>
                </a>
            </li>
            <hr class="sidebar-divider">
            @php
                $role_access = json_decode(session('role_access'));
            @endphp
                    @section('hot_leads')
                    <li class="nav-item">
                        <a class="nav-link nav-link-hide collapsed" href="#" id="hot_leads" data-toggle="collapse" data-target="#hot_leadsCollapse"
                            aria-expanded="true" aria-controls="hot_leadsCollapse"  onClick="myFunction(this.id);">
                            <i class="fas fa-fw fa-cog"></i>
                            <span>Hot Leads Management</span>
                        </a>
                        <div id="hot_leadsCollapse" class="collapse hot_leads" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                            <div class="bg-white py-2 collapse-inner rounded">
                                <a class="collapse-item" href="<?php echo url('/');?>/hot_leads">Hot Leads</a>
                                <a class="collapse-item" href="<?php echo url('/');?>/view_hot_leads_in_process_leads">In Process</a>
                                <a class="collapse-item" href="<?php echo url('/');?>/view_closed_leads">Closed</a>
                            </div>
                        </div>
                    </li>
                    @if(session('role')=='superuser')
                    @if(in_array('20',$role_access))
                        @show
                    @else
                        @stop
                    @endif
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
                    <a class="nav-link nav-link-hide collapsed" href="#" data-toggle="collapse" id="leads" data-target="#collapseLeads"
                        aria-expanded="true" aria-controls="collapseLeads"  onClick="myFunction(this.id);">
                        <i class="fas fa-fw fa-cog"></i>
                        <span>Leads</span>
                    </a>
                    <div id="collapseLeads" class="collapse leads @if(session('id')!=null && session('id') =='leads' ){{"show"}} @endif" data-parent="#accordionSidebar">
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
                                        {{-- @if(session('role')=='superuser')
                                            <a class="collapse-item" href="{{route('assign-lead-user')}}">Assign Lead User</a>
                                        @endif --}}
                                        {{-- <a class="collapse-item" href="<?php echo url('/');?>/create_lead">Create New</a> --}}
                                        {{-- <a class="collapse-item" href="<?php echo url('/');?>/viewAllLeads">View All Leads</b></a>
                                        <a class="collapse-item" href="<?php echo url('/');?>/viewInProcessLeads">In Process Leads</b></a>
                                        <a class="collapse-item" href="<?php echo url('/');?>/viewConvertedLeads">Converted Leads</b></a>
                                        <a class="collapse-item" href="<?php echo url('/');?>/viewClosedLeads">Closed Leads</b></a> --}}
                                        <a class="collapse-item" href="<?php echo url('/');?>/view_all_leads">View All Leads</b></a>
                                        {{-- <a class="collapse-item" href="<?php echo url('/');?>/send_link_lead">Send Link</b></a> --}}
                                        <a class="collapse-item" href="{{route('quote.index')}}">Quote</b></a>
                                    </div>
                                </div>
                            @if(session('role')=='superuser')
                                @if(in_array('2',$role_access))
                                    @show
                                @else
                                    @stop
                                @endif
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
                            @if(in_array('4',$role_access))
                                @show
                            @else
                                @stop
                            @endif
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
                            @if(in_array('3',$role_access))
                                @show
                            @else
                                @stop
                            @endif
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
                                <div class="card bg-white py-2 collapse-inner rounded">
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
                            @if(in_array('16',$role_access))
                                @show
                            @else
                                @stop
                            @endif
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

        @section('Other_Services')
            <li class="nav-item">
                <a class="nav-link nav-link-hide collapsed" href="#" data-toggle="collapse" id="Other_Services" data-target="#collapseOther_Services"
                    aria-expanded="true" aria-controls="collapseOther_Services"  onClick="myFunction(this.id);">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Other Services</span>
                </a>
                <div id="collapseOther_Services" class="collapse Other_Services<?php if(session('id')!=null && session('id') =='Other_Services' ){echo "show"; }?>" data-parent="#accordionSidebar">
                    <div class="card bg-white py-2 collapse-inner rounded" id="accordion1">
                            <a class="collapse-item" data-toggle="collapse" data-target="#LabTest" aria-controls="LabTest" role="button" aria-expanded="false" aria-controls="LabTest" name="LabTest" onClick="myFunction1(this.name);">
                                <i class="fas fa-fw fa-cog"></i>
                                <span>Medical Lab Test</span>
                            </a>
                            <div class="collapse LabTest @if(session('name')!=null && session('name')=='LabTest'){{"show"}} @endif" id="LabTest" aria-labelledby="#LabTest" data-parent="#accordion1">
                                <div class=" card bg-white py-2 collapse-inner rounded">
                                    <a class="collapse-item" href="<?php echo url('/');?>/lab_register">Lab Registration</a>
                                    <a class="collapse-item" href="<?php echo url('/');?>/view_all_labs">View All Labs</a>
                                    <a class="collapse-item" href="<?php echo url('/');?>/create_lead_lab">Create Lead</a>
                                    <a class="collapse-item" href="<?php echo url('/');?>/view_all_lab_test_leads">View All Lead</a>
                                </div>
                            </div>
                            <a class="collapse-item" data-toggle="collapse" data-target="#Ambulance" aria-controls="Ambulance" role="button" aria-expanded="false" aria-controls="Ambulance" name="Ambulance" onClick="myFunction1(this.name);">
                                <i class="fas fa-fw fa-cog"></i>
                                <span>Ambulance</span>
                            </a>
                            <div class="collapse Ambulance @if(session('name')!=null && session('name')=='Ambulance'){{"show"}} @endif" id="Ambulance" aria-labelledby="#Ambulance" data-parent="#accordion1">
                                <div class=" card bg-white py-2 collapse-inner rounded">
                                    <a class="collapse-item" href="<?php echo url('/');?>/create_lead_ambulance">Create Lead</a>
                                    <a class="collapse-item" href="<?php echo url('/');?>/view_all_ambulance_leads">View All Lead</a>
                                </div>
                            </div>
                            <a class="collapse-item" data-toggle="collapse" data-target="#Nursing_Care" aria-controls="Nursing_Care" role="button" aria-expanded="false" aria-controls="Nursing_Care" name="Nursing_Care" onClick="myFunction1(this.name);">
                                <i class="fas fa-fw fa-cog"></i>
                                <span>Nursing Care</span>
                            </a>
                            <div class="collapse Nursing_Care @if(session('name')!=null && session('name')=='Nursing_Care'){{"show"}} @endif" id="Nursing_Care" aria-labelledby="#Nursing_Care" data-parent="#accordion1">
                                <div class=" card bg-white py-2 collapse-inner rounded">
                                    {{-- <a class="collapse-item" href="<?php echo url('/');?>/create_lead_nursing_care">Create Lead</a> --}}
                                    {{-- <a class="collapse-item" href="<?php echo url('/');?>/view_all_nursing_care_leads">View All Lead</a> --}}
                                    <a class="collapse-item" href="{{route('nursing-care')}}">Leads</a>
                                </div>
                            </div>
                    </div>
                </div>
            </li>
        @if(session('role')=='superuser')
        @if(in_array('40',$role_access))
            @show
        @else
            @stop
        @endif
        @else
            @if(in_array('40',$role_access))
                @show
            @else
                @stop
            @endif  
        @endif

        {{--Complaint Mgmt--}}
        @section('complaint_mgmt')
            <li class="nav-item">
                <a class="nav-link nav-link-hide collapsed" href="#" id="Complaint_Mgmt" data-toggle="collapse" data-target="#Complaint_MgmtCollapse"
                    aria-expanded="true" aria-controls="Complaint_MgmtCollapse"  onClick="myFunction(this.id);">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Complaint Mgmt</span>
                </a>
                <div id="Complaint_MgmtCollapse" class="collapse Complaint_Mgmt" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="{{url('/')}}/raise_complaint">View All Complaint</a>
                        <a class="collapse-item" href="{{url('/')}}/create_complaint">Raise Complaint</a>
                        {{-- <a class="collapse-item" href="{{url('/')}}/raise_complaint">Raise Complaint</a>
                        <a class="collapse-item" href="{{url('/')}}/open_complaints">Open Complaints</a>
                        <a class="collapse-item" href="{{url('/')}}/closed_complaints">Closed Complaints</a> --}}
                    </div>
                </div>
            </li>
        @if(session('role')=='superuser')
        @if(in_array('30',$role_access))
            @show
        @else
            @stop
        @endif
        @else
            @if(in_array('30',$role_access))
                @show
            @else
                @stop
            @endif
        @endif

            {{-- Vendor--}}
            @section('vendor')
                <li class="nav-item">
                    <a class="nav-link nav-link-hide collapsed" href="#" data-toggle="collapse" id="vendor" data-target="#collapsevendor"
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
                            @if(in_array('6',$role_access))
                                @show
                            @else
                                @stop
                            @endif
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
                                        <a class="collapse-item" href="{{url('/')}}/virtual_wh_inventory">Virtual WH Inventory</a>
                                        <a class="collapse-item" href="{{route('vendor-return-inventory')}}">Vendor Return Inventory</a>
                                        <a class="collapse-item" href="{{route('vendor-live-inventory')}}">Vendor Live Inventory</a>
                                        <a class="collapse-item" href="{{route('vendor-inventory-auto')}}">Vendor Inventory</a>
                                        <a class="collapse-item" href="{{route('vendor-billing')}}">Vendor Billing</a>
                                        {{-- <a class="collapse-item" href="{{url('/')}}/detailed_rent_list">Rent Detailed List</a> --}}   
                                    </div>
                                </div>
                            @if(session('role')=='superuser')
                            @if(in_array('7',$role_access))
                                @show
                            @else
                                @stop
                            @endif
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
            @if(in_array('5',$role_access))
                @show
            @else
                @stop
            @endif
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
                    <a class="nav-link nav-link-hide collapsed" href="#" data-toggle="collapse" id="Product" data-target="#collapseProduct"
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
                                        @if(session('role')=='superuser')
                                            <a class="collapse-item" href="{{url('/')}}/add_new_product">Add New Product</a>
                                            <a class="collapse-item" href="{{route('map-product')}}">Map Product</a>
                                        @endif
                                        <a class="collapse-item" href="{{url('/')}}/view_master_products">View Master Products</a>                            
                                        {{-- <a class="collapse-item" href="{{url('/')}}/detailed_rent_list">Rent Detailed List</a> --}}        
                                    </div>
                                </div>
                            @if(session('role')=='superuser')
                            @if(in_array('9',$role_access))
                                @show
                            @else
                                @stop
                            @endif
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
            @if(in_array('8',$role_access))
                @show
            @else
                @stop
            @endif
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
                <a class="nav-link nav-link-hide collapsed" href="#" data-toggle="collapse" id="Order" data-target="#collapseOrder"
                    aria-expanded="true" aria-controls="collapseOrder"  onClick="myFunction(this.id);">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Order</span>
                </a>
                <div id="collapseOrder" class="collapse Order<?php if(session('id')!=null && session('id') =='Order' ){echo "show"; }?>" data-parent="#accordionSidebar">
                    <div class="card bg-white py-2 collapse-inner rounded">
                        {{-- <a class="collapse-item" href="<?php echo url('/');?>/order_converted_leads">Orders_old</a> --}}
                        {{-- <a class="collapse-item" href="<?php echo url('/');?>/order_mgmt_all_leads">Converted Orders</a> --}}
                        <a class="collapse-item" href="{{route('order-delivery-all')}}">Converted Orders</a>
                        {{-- <a class="collapse-item" href="<?php echo url('/');?>/mobileAppLeads">Mobile Generated Leads</a> --}}
                        @if(session('role')=='superuser')
                        {{-- <a class="collapse-item" href="<?php echo url('/');?>/pendingAssignment">Pending Assignments</a> --}}
                        {{-- <a class="collapse-item" href="<?php echo url('/');?>/pending_for_vendor_approval">Pending For Vendor <br>Approval</a> --}}
                        {{-- <a class="collapse-item" href="{{url('/')}}/approved_orders">Approved Orders</a>
                        <a class="collapse-item" href="{{url('/')}}/rejected_orders">Rejected Orders</a> --}}
                        {{-- <a class="collapse-item" href="{{url('/')}}/viewall_order_mgmt_filter">View All Orders</a> --}}
                        <a class="collapse-item" href="{{url('/')}}/order_details">Order Details</a>
                        <a class="collapse-item" href="{{route('order-maintenance')}}">Maintenance Order</a>
			@endif
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
        @if(in_array('17',$role_access))
            @show
        @else
            @stop
        @endif
        @else
            @if(in_array('17',$role_access))
                @show
            @else
                @stop
            @endif
        @endif


        @section('delivery_mgmt')
            <li class="nav-item">
                <a class="nav-link nav-link-hide collapsed" href="#" data-toggle="collapse" id="delivery" data-target="#DeliveryManagement"
                    aria-expanded="true" aria-controls="DeliveryManagement" onClick="myFunction(this.id);">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Delivery Management</span>
                </a>
                <div id="DeliveryManagement" class="collapse delivery" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        {{-- <a class="collapse-item" href="<?php echo url('/');?>/AddDelivery">Add New Delivery</a> --}}
                        <a class="collapse-item" href="<?php echo url('/');?>/confirmed_delivery">Confirm Delivery</a>
                        <a class="collapse-item" href="<?php echo url('/');?>/ModifyDeliveryView">Modify Delivery</a>
                        <a class="collapse-item" href="<?php echo url('/');?>/pickup_request">Pickup Request</a>
                        <a class="collapse-item" href="<?php echo url('/');?>/renew_request">Collection Request</a>
                        @if(in_array(session('user_id'),config('app.it_department')))
                            <a class="collapse-item" href="{{route('rejected-orders')}}">Reassign Orders</a>
                        @endif
                        {{-- <hr> --}}
                    </div>
                </div>
            </li> 
        @if(session('role')=='superuser')
        @if(in_array('11',$role_access))
            @show
        @else
            @stop
        @endif
        @else
            @if(in_array('11',$role_access))
                @show
            @else
                @stop
            @endif
        @endif

        @section('renewal_pickup')
            <li class="nav-item">
                <a class="nav-link nav-link-hide collapsed" href="#" id="renew_pickup" data-toggle="collapse" data-target="#renewpickupCollapse"
                    aria-expanded="true" aria-controls="renewpickupCollapse"  onClick="myFunction(this.id);">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Renewal & Pickup</span   >
                </a>
                <div id="renewpickupCollapse" class="collapse renew_pickup" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="{{route('renewalpickup-test',['date_filter'=>'Today'])}}">Renewal And Pickup</a>
                        {{-- <a class="collapse-item" href="{{url('/')}}/renewal_pickup">Renewal And Pickup</a> --}}
                        <a class="collapse-item" href="{{url('/')}}/get_renewal_links">Renewal Links</a>
                        <a class="collapse-item" href="{{url('/')}}/stop_requested">Stop Request</a>
                        {{-- <a class="collapse-item" href="{{url('/')}}/renewal_pickup_search">Search Customer Order</a> --}}
                    </div>
                </div>
            </li>
        @if(session('role')=='superuser')
        @if(in_array('12',$role_access))
            @show
        @else
            @stop
        @endif
        @else
            @if(in_array('12',$role_access))
                @show
            @else
                @stop
            @endif
        @endif
                    
        @section('billing_payment')
            <li class="nav-item">
                <a class="nav-link nav-link-hide collapsed" href="#" data-toggle="collapse" id="billing_payment" data-target="#collapseBilling_payment"
                    aria-expanded="true" aria-controls="collapseBilling_payment"  onClick="myFunction(this.id);">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Billing & Payment</span>
                </a>
                <div id="collapseBilling_payment" class="collapse billing_payment<?php if(session('id')!=null && session('id') =='billing_payment' ){echo "show"; }?>" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        {{-- <a class="collapse-item" href="{{url('/')}}/pending_online_renew">Pending Online Renew</a> --}}
                        {{-- <a class="collapse-item" href="{{url('/')}}/show_pedning_payments">Pending Payments</a> --}}
                        {{-- <a class="collapse-item" href="#">Pending Payments</a> --}}
                        <a class="collapse-item" href="{{url('/')}}/pending_payments">Pending Payments</a>
                        @if(session('user_id') !='153')
                            <a class="collapse-item" href="{{url('/')}}/delivery_staff_expenses">Delivery Staff Expenses</a>
                            <a class="collapse-item" href="{{url('/')}}/upload_expenses">Upload Expenses</a>
                            <a class="collapse-item" href="{{url('/')}}/order-expenses">Order Expenses</a>
                            @if(session('user_id') == '19')
                                <a class="collapse-item" href="{{route('cash-report')}}">Cash Report</a>
                            @endif
                        @endif
                    </div>
                </div>
            </li>
        @if(session('role')=='superuser')
        @if(in_array('13',$role_access))
            @show
        @else
            @stop
        @endif
        @else
            @if(in_array('13',$role_access))
                @show
            @else
                @stop
            @endif
        @endif
                    
        @section('reports')
            <li class="nav-item">
                <a class="nav-link nav-link-hide collapsed" href="#" data-toggle="collapse" id="Reports" data-target="#collapseReports"
                    aria-expanded="true" aria-controls="collapseReports"  onClick="myFunction(this.id);">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Reports</span>
                </a>
                <div id="collapseReports" class="collapse Reports<?php if(session('id')!=null && session('id') =='Reports' ){echo "show"; }?>" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        @if(session('role') == 'superuser')
                            <a class="collapse-item" href="{{url('/')}}/googleCampaignReport">Campaign Report</a>
                            <a class="collapse-item" href="{{route('cr-dr-report')}}">Credit/Debit Note Report</a>
                            <a class="collapse-item" href="{{url('/')}}/collectionReport">Collection Report</a>
                            <a class="collapse-item" href="{{url('/')}}/daily-lead-reports">Comparion Report</a>
                            {{-- <a class="collapse-item" href="{{url('/')}}/customer_single_view_get">Customer Single View</a> --}}
                            <a class="collapse-item" href="{{url('/')}}/daybyday_report">Day by Day Report</a>
                            <a class="collapse-item" href="{{url('/')}}/equipment_report">Equipment Report</a>
                            <a class="collapse-item" href="{{url('/')}}/fy_report">Financial Year Report</a>
                            <a class="collapse-item" href="{{url('/')}}/leads_reports">Leads Report</a>
                            <a class="collapse-item" href="{{url('/')}}/mis_reports">MIS Report</a>
                            <a class="collapse-item" href="{{url('/')}}/WebRenewals_report">Web Renewals Report</a>
                            <a class="collapse-item" href="{{url('/')}}/monthly_records?headfyear=2023-2024&city=All">Monthly Records</a>
                        @endif
                        @if(session('role')=='user' || session('role')=='superuser')
                            <a class="collapse-item" href="{{url('/')}}/customer_single_view_get">Customer Single View</a>
                            <a class="collapse-item" href="{{url('/')}}/monthly_report">Monthly Report</a>
                            <a class="collapse-item" href="{{url('/')}}/deliveryReport">Order Report</a>
                            <a class="collapse-item" href="{{route('pickup-products')}}">Pickup Product Details</a>
                        @endif
                        @if(session('role') == 'superuser')
                            <a class="collapse-item" href="{{url('/')}}/timeline">Timeline</a>
                            <a class="collapse-item" href="{{url('/')}}/vendor_product_report">Vendor Product Report</a>
                            {{-- <a class="collapse-item" href="{{route('expense-report')}}">Expense Report</a> --}}
                        @endif
                        @if(session('role') == 'superuser')
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
                        @endif                        
                    </div>
                </div>
            </li>
        @if(session('role')=='superuser')
        @if(in_array('14',$role_access))
            @show
        @else
            @stop
        @endif
        @else
            @if(in_array('14',$role_access))
                @show
            @else
                @stop
            @endif
        @endif

        @section('user_mgmt')
            <li class="nav-item">
                <a class="nav-link nav-link-hide collapsed" href="#" id="users" data-toggle="collapse" data-target="#collapseThree"
                    aria-expanded="true" aria-controls="collapseThree" onClick="myFunction(this.id);">
                    <i class="fas fa-fw fa-user"></i>
                    <span>User Management</span>
                </a>
                <div id="collapseThree" class="collapse  users" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        @if (session('role')=='superuser')
                            <a class="collapse-item" href="<?php echo url('/');?>/add_user">Add Users</a>    
                            {{-- <a class="collapse-item" href="<?php echo url('/');?>/delivery-staffs">Delivery Staffs</a>     --}}
                        @elseif(session('role')=='admin')
                            {{""}}
                        @endif
                        <a class="collapse-item" href="<?php echo url('/');?>/view_all_user">View All Users</a>
                        <a class="collapse-item" href="#">Reassigne Permissions </a>
                                                            
                    </div>
                </div>
            </li>
        @if(session('role')=='superuser')
        @if(in_array('15',$role_access))
            @show
        @else
            @stop
        @endif
        @else
            @if(in_array('15',$role_access))
                @show
            @else
                @stop
            @endif
        @endif
        @section('b2bmgmt')
            <li class="nav-item">
                <a class="nav-link nav-link-hide collapsed" href="#" id="b2bcollapse" data-toggle="collapse" data-target="#b2bcollapse"
                    aria-expanded="true" aria-controls="b2bcollapse" onClick="myFunction(this.id);">
                    <i class="fas fa-fw fa-user"></i>
                    <span>B2B User</span>
                </a>
                <div id="b2bcollapse" class="collapse  b2bcollapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        {{-- <a class="collapse-item" href="{{route('b2b-user-rate')}}">User Rates</a> --}}
                        {{-- <a class="collapse-item" href="{{route('b2b-user-add')}}">Add User</a>
                        <a class="collapse-item" href="{{route('b2b-user-view-all')}}">View Users</a> --}}
                        <a class="collapse-item" href="{{route('agents.index')}}">Agents</a>
                        <a class="collapse-item" href="{{route('b2bcustomers.index')}}">B2B Customers</a>
                    </div>
                </div>
            </li>
        @if(session('role')=='superuser')
            @show
        @else
            @if(in_array('61',$role_access))
                @show
            @else
                @stop
            @endif
        @endif
        @section('dummyInvoices')
        <li class="nav-item">
            <a class="nav-link nav-link-hide collapsed" href="#" id="dummyInvoice" data-toggle="collapse" data-target="#dummyInvoice1"
                aria-expanded="true" aria-controls="dummyInvoice1" onClick="myFunction(this.id);">
                <i class="fas fa-fw fa-user"></i>
                <span>Dummy Invoices</span>
            </a>
            <div id="dummyInvoice1" class="collapse  dummyInvoice" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="{{route('generate-dummy-invoice')}}">Generate invoice</a>
                    <a class="collapse-item" href="{{route('view-all-dummy-invoices')}}">View Invoices</a>
                </div>
            </div>
        </li>
        @if(session('role')=='superuser')
            @show
        @else
            @if(in_array('60',$role_access))
                @show
            @else
                @stop
            @endif
        @endif
        <li class="nav-item">
            <a class="nav-link nav-link-hide collapsed" href="#" id="other_links" data-toggle="collapse" data-target="#other_links"
                aria-expanded="true" aria-controls="other_links" onClick="myFunction(this.id);">
                <i class="fas fa-fw fa-user"></i>
                <span>Other Links</span>
            </a>
            <div id="other_links" class="collapse  other_links" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="http://intra.quali55care.com/{{config('app.app_env')}}/vendor" target="_blank">Vendor</a>
                    <a class="collapse-item" href="http://intra.quali55care.com/{{config('app.app_env')}}/b2bcrm" target="_blank">B2buser</a>
                </div>
            </div>
        </li>

            
            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            {{-- <div class="sidebar-heading">
                Addons
            </div> --}}

            <!-- Sidebar Toggler (Sidebar) -->
            {{-- <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div> --}}

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow fixed-top">

                    <!-- Sidebar Toggle (Topbar) -->
                    {{-- <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button> --}}

                    <button id="sidebarToggleTop_show" class="btn btn-link rounded-circle mr-3"  style="display: inline-block">
                        <i class="fa fa-bars"></i>
                    </button>
                    <button id="sidebarToggleTop_cancel" class="btn btn-link rounded-circle mr-3"  style="display: none">
                        <i class="fa fa-bars"></i>
                    </button>
                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav">
                        <a class="sidebar-brand d-flex align-items-center justify-content-center" style="background-color: white;" href="{{url('/')}}/dashboard">
                            <div class="sidebar-brand-text mx-3" ><img src="<?php echo url('/');?>/assets/images/logo-sm-new.png"></div>
                        </a>
                    </ul>
                    <ul class="navbar-nav ml-auto">

                            <!-- Nav Item - User Information -->
                        <input type="hidden" name="count" id="count" >
                        @if(session('role')=='superuser' || session('role')=='admin')


                        <div class="container container-fluid d-none d-md-block mt-4">
                            <div class="btn-group">
                                <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Todays Orders (<span id="totaloOrders"></span>)
                                </button>
                                <div class="dropdown-menu" id="orders"></div>
                            </div>
                        </div>


                            <div class="container container-fluid d-none d-md-block mt-4">
                                <div class="btn-group">
                                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Order Status (<span id="totalorderct"></span>)
                                    </button>
                                    <div class="dropdown-menu" id="orderstatusct"></div>
                                </div>
                            </div>
                            {{-- <a class="btn btn-primary btn-sm form-control" style="margin-top: 20px;" href="{{url('/')}}/order_mgmt_all_leads?filter_lead_status=Converted">
                                PA <span id="pendingAssignments" class="badge badge-light">0</span>
                            </a> --}}
                            <a class="btn text-nowrap btn-primary btn-sm form-control" style="margin-top: 20px;" href="{{route('order-delivery-all',['filter_order_status'=>'Converted'])}}">
                                <span>PA</span> <span id="pendingAssignments" class="badge badge-light">0</span>
                            </a>
                        @endif
                        <div class="topbar-divider d-none d-sm-block"></div>    
                        <button type="button" class="btn btn-primary btn-sm form-control" style="margin-top: 20px;">
                            LN <span id="lead_notifications" class="badge badge-light">0</span>
                        </button>
                        
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{session('username')}}</span>
                                <img class="img-profile rounded-circle"
                                    src="{{url('/')}}/assets/img/undraw_profile.svg">
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
                                <a class="dropdown-item" href="{{url('/')}}/logout">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                    <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <br>
                    <br>
                    <br>
                    @yield('content')
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            {{-- <div class="modal fade" id="loading_screen" tabindex="-1" role="dialog" aria-labelledby="loading_screenLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" style="background-color: transparent;" role="document">
                  <div class="modal-content" style="background-color: transparent; border:rgb(255, 255, 255)">
                    <div class="modal-body trans text-center" style="background-color: transparent;">
                        <svg width="200" height="200" class="svg1" id="svg1" style="background-color: transparent;">
                            <circle id="dot1" class="shape" />
                            <circle id="dot2" class="shape" />
                            <circle id="dot3" class="shape" />
                            <circle id="dot4" class="shape" />
                        </svg>
                    </div>
                  </div>
                </div>
            </div> --}}

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="{{url('/')}}/assets/vendor/jquery/jquery.min.js"></script>
    <script src="{{url('/')}}/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{url('/')}}/assets/vendor/jquery/jquery.cookie.js" type="text/javascript"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{url('/')}}/assets/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{url('/')}}/assets/js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="{{url('/')}}/assets/vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="{{url('/')}}/assets/js/demo/chart-area-demo.js"></script>
    <script src="{{url('/')}}/assets/js/demo/chart-pie-demo.js"></script>

     <!-- Page level plugins -->
     <script src="<?php echo url('/');?>/assets/vendor/datatables/jquery.dataTables.min.js"></script>
     <script src="<?php echo url('/');?>/assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>

     {{-- Scripts --}}
     <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
     {{-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script> --}}
     <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
     <script src="{{url('/')}}/assets/js/jquery.table2excel.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js" ></script>

     <script src="{{url('/')}}/assets/dist/clipboard.min.js"></script>
    <script>
        $("#loading_screen").modal({backdrop: 'static', keyboard: false});
        $(document).ready(function() {
            $('#accordionSidebar').addClass('toggled');
            $('#sidebarToggleTop_cancel').css('display', 'block');
            $('#sidebarToggleTop_show').css('display', 'none');
            $('.collapse').removeClass('show');
            $('.nav-link-hide').hide();
            $.ajax({
                url : "{{url('/')}}/pendingAssignmentsNotify",
                type : "GET",
                success : function(lead_count)
                {
                    // console.log(lead_count);
                    $("#pendingAssignments").text(lead_count);
                }
            });
            $('#records').DataTable();
            $.ajax({
                    url : "{{url('/')}}/getCount",
                    type : "GET",
                    success : function(data)
                    {
                        $("#count").val(data);
                    }
            });
            $.ajax({
                url : "{{url('/')}}/get-order-status-count",
                type : "GET",
                success : function(data)
                {
                    let converted_ct = data.orderConvertedCount;
                    let totalorder_ct = data.totalOrderCount;
                    let order_status_count = data.ordersStateCount;
                    let row = "";
                    $('#totalorderct').text(totalorder_ct);
                    row+= '<a class="dropdown-item" href="{{route("order-delivery-all")}}">All</a>';
                    $.each(order_status_count,function(key,value){
                        let route = '{{route("order-delivery-all")}}?filter_order_status='+key;
                        row+= '<a class="dropdown-item" href="'+route+'">'+key+' &emsp;<strong>'+order_status_count[key].length+'</strong></a>';
                    })
                    let routeconv = '{{route("order-delivery-all")}}?filter_order_status=Converted';
                    row+= '<a class="dropdown-item" href="'+routeconv+'">Converted &emsp;<strong>'+converted_ct+'</strong></a>';
                    
                    $('#orderstatusct').append(row);
                }
            });
        } );

        function ordersCount() 
        {
            setTimeout( function()
            {
                getOrdersCount();
                ordersCount();

            },5000);
        }
        ordersCount();
        getOrdersCount();
        function getOrdersCount(){
            const date = new Date();

                let day = date.getDate();
                let month = date.getMonth() + 1;
                let year = date.getFullYear();
                if(month<10){
                    month = "0"+month;
                }
                // This arrangement can be altered based on how we want the date's format to appear.
                let currentDate = `${year}-${month}-${day}`;
                $.ajax({
                    url : "{{route('get-orders-count')}}",
                    type : "GET",
                    success : function(data)
                    {   
                        $('#orders').empty(); 
                        let row = "";
                        $('#totaloOrders').text(data.total);
                        $.each(data,function(key,value){
                            if(key!="total"){
                                let route = '{{route("pendingPaymentOrder")}}?filter_from_date='+currentDate+'&filter_end_date='+currentDate+'&filter_order_type='+key+'&filter_order_state=All&filter_city=All&customer_type=All&filter_delivery_status=All&filter_delivery_boy=All&filter_payment_mode=All&btn_submit=submit';
                                row+= '<a class="dropdown-item" href="'+route+'">'+key+' &emsp;<strong>'+data[key]+'</strong></a>';
                            }
                        });
                        
                        $('#orders').append(row);
                    }
                });
        }
        function charger1(){
            @if(session('user_id') == '19' || session('user_id') == '14' || session('user_id') == '97')
            setTimeout(function(){
                $.ajax({
                    url : "{{route('run-cron-job')}}",
                    type : "GET",
                    success : function(data)
                    {
                        // console.log(data)
                        charger1();
                    },
                    error: function(er){
                        // console.log(er)
                        charger1();
                    }
                });
            },60000);
            @endif
        }
        charger1();
        
        function charger() 
        {
            setTimeout( function()
            {
                var count = $('#count').val();
                if(count == "")
                {
                    count = 0;
                }
                $.ajax({
                    url : "{{url('/')}}/notifications/"+count+"",
                    type : "GET",
                    success : function(data)
                    {
                        $("#lead_notifications").text(data);
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
                let username = "{{session('username')}}";
                if(username != "")
                {
                    charger();
                }

            },5000);
        }

        charger();

        function myFunction(id)
        {
            //var id = this.id;
            //alert(id);
            // {{session(['id'=>'<script>id</script>'])}}
            localStorage['id'] = ''+id;
            //alert(localStorage['id']);
        }
        
        function myFunction1(name)
        {
            localStorage['name'] = ''+name;
        }
        
        $('#sidebarToggleTop_show').click(function(){
            $('#accordionSidebar').addClass('toggled');
            $('#sidebarToggleTop_cancel').css('display', 'block');
            $('#sidebarToggleTop_show').css('display', 'none');
            $('.collapse').removeClass('show');
            $('.nav-link-hide').hide();
            localStorage['sidebar'] = 0;
        });
        $('#sidebarToggleTop_cancel').click(function(){
            $('#accordionSidebar').removeClass('toggled');
            $('#sidebarToggleTop_show').css('display', 'block');
            $(this).css('display', 'none');
            $('.nav-link-hide').show();
            localStorage['sidebar'] = 1;

            if(localStorage['id'] != null) 
            {
                var id = localStorage['id'];
                $('.'+id).addClass('show');
            }
            if(localStorage['name'] != null) 
            {
                var name = localStorage['name'];
                $('.'+name).addClass('show');
            }
        });
       
        if(localStorage['sidebar']==0)
        {
            $('#accordionSidebar').addClass('toggled');

            // if(localStorage['id'] != null) 
            // {
            //     var id = localStorage['id'];
            //     $('.'+id).removeClass('show');
                
            // }
            // if(localStorage['name'] != null) 
            // {
            //     var name = localStorage['name'];
            //     $('.'+name).removeClass('show');
            // }
        }
        else if(localStorage['sidebar']==1)
        {
            $('#accordionSidebar').removeClass('toggled');
        }
        if($('#accordionSidebar').hasClass('toggled'))
        {
            if(localStorage['id'] != null) 
            {
                var id = localStorage['id'];
                $('.'+id).removeClass('show');
            }
            if(localStorage['name'] != null) 
            {
                var name = localStorage['name'];
                $('.'+name).removeClass('show');
            }
        }
        else{

            if(localStorage['id'] != null) 
            {
                var id = localStorage['id'];
                $('.'+id).addClass('show');
            }
            if(localStorage['name'] != null) 
            {
                var name = localStorage['name'];
                $('.'+name).addClass('show');
            }
        }
        $(window).on('load', function() {
            $("#loading_screen").modal("hide");
            setTimeout(function(){
                $("#loading_screen").modal("hide");
            }, 2000);
        });
        $(document).ready(function(){
            $(".doublePost").submit(function(){
               alert("Are you sure to submit this data ?");
            });
        });
        //Define variables
        const checkBox = document.getElementById("myCheck");
  const dot = document.getElementById("dot4");
  const body = document.getElementsByTagName("BODY")[0];
  const svg = document.getElementById("svg1");
  const label = document.getElementById("switchLabel");
  const label2 = document.getElementById("switchLabel2");

//   //If user wants dark mode
//   if (checkBox.checked == true) {
//     dot.style.fill = "#ffffff"; //Dot turns white
//     body.style.backgroundColor = "#000000"; //Background turns black
//     svg.style.backgroundColor = "#000000"; //svg background turns black
//     //The labels turn white
//     label.style.color = "#ffffff";
//     label2.style.color = "#ffffff";

//     //If they want light mode/default
//   } else {
//     dot.style.fill = "#000000"; //Dot is black
//     body.style.backgroundColor = "#ffffff"; //Background is white
//     svg.style.backgroundColor = "#ffffff"; //svg background is white
//     //Labels are white
//     label.style.color = "#000000";
//     label2.style.color = "#000000";
//   }
        
        
	</script>
    @yield('script')

    </body>

</html>