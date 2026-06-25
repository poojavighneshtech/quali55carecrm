@extends('header_and_sidebar')
@section('title')
    Dashboard
@endsection
@section('header')
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            
            <div class="col-xl-3 col-md-12 mb-4" >                
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
            <div class="col-xl-3 col-md-12 mb-4" >                
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
    
@endsection