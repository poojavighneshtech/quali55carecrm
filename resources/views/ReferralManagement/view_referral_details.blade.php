@extends('header_and_sidebar')
@section('title')
    Admin: Referral Details
@endsection
@section('header')
    <style>
        #scroll-card{
        height: calc(100vh - 110px);
        overflow-y: scroll;
        }
        #scroll-card1{
        height: calc(55vh - 80px);
        overflow-y: scroll;
        }
    </style>
    @section('styles')

    @endsection
@endsection
@section('content')    
        <div class="container">
            
            <div class="row" style="margin-top: 0rem;">
                <div class="col-md-12">
                    <form class="form" action="<?php echo url('/');?>/update_status" method="POST">
                        {{csrf_field()}}
                        <div class="card o-hidden border-0 shadow-lg">
                            
                            <div class="card-header bg-primary text-white">
                                <center>
                                    <h3 class="m-0 font-weight-bold">Referral Details</h3>
                                    {{-- <h5 class="m-0 font-weight-bold">Name : {{$referral_details[0]['user_name']}}</h5> --}}
                                </center>
                            </div>
                            <div class="card-body overflow-auto" id="scroll-card">
                                @if(session()->has('message'))
                                    <div class="alert alert-success">
                                        {{ session()->get('message') }}
                                    </div>
                                @endif
                                <div class="row justify-content-center" style="margin-top: 0rem;">
                                    <div class="col-6">
                                        <div class="card o-hidden border-0 shadow-lg">
                                            <div class="card-body">
                                                @if(session()->has('message'))
                                                    <div class="alert alert-success">
                                                        {{ session()->get('message') }}
                                                    </div>
                                                @endif
                                                <div class="row">                                                    
                                                    <div class="col-md-5 form-group text-right">
                                                        <label for="status" class="control-label"><b>Status</b></label>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <div class="form-group">
                                                            <span style="color:#2abf51"><b>{{$referral_details[0]['referralStatus']}}</b></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-5 form-group text-right">
                                                        <label for="patient_name" class="control-label"><b>Patient Name</b></label>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <div class="form-group">
                                                            <input type="hidden" name="ref_id" value="{{$referral_details[0]['id']}}">
                                                            <span>{{$referral_details[0]['referralName']}}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-5 form-group text-right">
                                                        <label for="contact_number" class="control-label"><b>Contact Number</b></label>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <div class="form-group">
                                                            <span>{{$referral_details[0]['mobileNo']}}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-5 form-group text-right">
                                                        <label for="equipments" class="control-label"><b>Equipments</b></label>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <div class="form-group">
                                                            <span>{{$referral_details[0]['details']}}</span>
                                                        </div>
                                                    </div> 
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-5 form-group text-right">
                                                        <label for="location" class="control-label"><b>Location</b></label>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <div class="form-group">
                                                            <span>{{$referral_details[0]['location']}}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-5 form-group text-right">
                                                        <label for="refered_by" class="control-label"><b>Referred By</b></label>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <div class="form-group">
                                                            <span>{{$referral_details[0]['referredBy']}}</span>
                                                        </div>
                                                    </div> 
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-5 form-group text-right">
                                                        <label for="referred_by_contact_no" class="control-label"><b>Contact Number</b></label>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <div class="form-group">
                                                            <span>{{$referral_details[0]['referredByMobileNo']}}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-5 form-group text-right">
                                                        <label for="referralStatus" class="control-label"><b>Update Status</b></label>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <div class="form-group">
                                                            <select class="form-control" name="referralStatus">
                                                                <option value="Recieved" <?php if($referral_details[0]['referralStatus']=="recieved"){echo "selected";}?>>Recieved</option>
                                                                <option value="Spoke to Customer" <?php if($referral_details[0]['referralStatus']=="Spoke to Customer"){echo "selected";}?>>Spoke to Customer</option>
                                                                <option value="Waiting" <?php if($referral_details[0]['referralStatus']=="Waiting"){echo "selected";}?>>Waiting</option>
                                                                <option value="Converted" <?php if($referral_details[0]['referralStatus']=="Converted"){echo "selected";}?>>Converted</option>
                                                                <option value="Paid" <?php if($referral_details[0]['referralStatus']=="Paid"){echo "selected";}?>>Paid</option>
                                                                <option value="In Progress" <?php if($referral_details[0]['referralStatus']=="In Progress"){echo "selected";}?>>In Progress</option>
                                                                <option value="Closed" <?php if($referral_details[0]['referralStatus']=="Closed"){echo "selected";}?>>Closed</option>
                                                                <option value="Not Interested" <?php if($referral_details[0]['referralStatus']=="Not Interested"){echo "selected";}?>>Not Interested</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-5 form-group text-right">
                                                        <label for="comment" class="control-label"><b>Comment</b></label>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <textarea class="form-control" name="comment">{{$referral_details[0]['comment']}}</textarea>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div>
                                                    <center>
                                                        <div class="col-md-8">
                                                            <button type="submit" name="update" id="update" class="btn btn-primary" value="update">Update</button>
                                                        </div>
                                                    </center>
                                                </div>
                                            </div>
                                        </div>   
                                    </div>
                                </div> 
                            </div>
                        </div>   
                    </form>
                </div>
            </div>
        </div>
@endsection

@section('script')
<script>
   $('#update').on('click',function(){
        $('#cash_received_from_office').attr('readonly',false);
        $('#cash_received_from_customer').attr('readonly',false);
        $('#transport').attr('readonly',false);
        $('#actual_deposite_returned').attr('readonly',false);
        $('#expense').attr('readonly',false);
        //$('#balance_cash').attr('readonly',false);
        $('#comment').attr('readonly',false);
        $(this).hide();
   })
       function calculate()
       {
            var cash_received_from_office = parseInt($('#cash_received_from_office').val());
            var cash_received_from_customer = parseInt($('#cash_received_from_customer').val());
            var transport = $('#transport').val();
            var actual_deposite_returned = $('#actual_deposite_returned').val();
            var expense = $('#expense').val();
            var total_cash = 0;
            var balance_cash = 0;
            total_cash = cash_received_from_office + cash_received_from_customer;
            balance_cash = total_cash - transport - actual_deposite_returned - expense;
            $('#balance_cash').val(balance_cash);
       }
</script>

@endsection