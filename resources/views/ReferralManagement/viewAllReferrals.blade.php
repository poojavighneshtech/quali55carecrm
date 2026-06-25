@extends('header_and_sidebar')
@section('title')
    Admin: All Referrals
@endsection
@section('header')
    <style>
        #scroll-card{
        height: calc(100vh - 110px);
        overflow-y: scroll;
        }
    </style>
    @section('styles')
    
    @endsection
@endsection
@section('content')                
<div class="card my-3">
    <div class="card-header border-primary" id="filter_card">
        <div class="row">
            <div class="col text-primary" data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                <strong>All Referrals</strong>
            </div>
            <div class="col-auto">
                <a data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                    <i class="fa fa-chevron-down pull-right"></i>&emsp;<b>Filter</b>
                </a>
            </div>
        </div>
    </div>
    <div class="card-body collapse" id="filter-collapse" aria-labelledby="headingTwo" data-parent="#filter_card">
        <form action="{{route('viewAllReferrals')}}" method="GET" id="referrals-form">
            @csrf
            <div class="row form-group">
                <div class="col-md-3">
                    <div class="row">
                        <div class="col-md-5">
                            <label for="filterpatientname">Patient Name</label>
                        </div>
                        <div class="col-md-7">
                            <input type="text" class="form-control form-control-sm" name="filterpatientname" id="filterpatientname" value="{{request()->get('filterpatientname')}}">
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="row">
                        <div class="col-md-5">
                            <label for="filterpatientcontact">Patient Contact</label>
                        </div>
                        <div class="col-md-7">
                            <input type="text" class="form-control form-control-sm" name="filterpatientcontact" id="filterpatientcontact" value="{{request()->get('filterpatientcontact')}}">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-2">
                                    <label for="filterfromdate">From</label>
                                </div>
                                <div class="col-md-10">
                                    <input type="date" class="form-control form-control-sm" name="filterfromdate" id="filterfromdate" value="{{request()->get('filterfromdate')}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-2">
                                    <label for="filterlastdate">To</label>
                                </div>
                                <div class="col-md-10">
                                    <input type="date" class="form-control form-control-sm" name="filterlastdate" id="filterlastdate" value="{{request()->get('filterlastdate')}}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-3">
                    <div class="row">
                        <div class="col-md-5">
                            <label for="filterlocations">Location</label>
                        </div>
                        <div class="col-md-7">
                            <select class="select selectpicker form-control form-control-sm" name="filterlocations[]" id="filterlocations" data-size="5" data-live-search="true" multiple="multiple">
                                @foreach($locations as $location)
                                    <option value="{{$location->location}}" @if(request()->get('filterlocations'))@if(in_array($location->location,request()->get('filterlocations'))){{'selected'}}@endif @endif>{{$location->location}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="row">
                        <div class="col-md-5">
                            <label for="filterreferredby">Referred By</label>
                        </div>
                        <div class="col-md-7">
                            <select class="select selectpicker form-control form-control-sm" name="filterreferredby[]" id="filterreferredby" data-size="5" data-live-search="true" multiple="multiple">
                                @foreach($referredby as $referred)
                                    <option value="{{$referred->referredBy}}" @if(request()->get('filterreferredby'))@if(in_array($referred->referredBy,request()->get('filterreferredby'))){{'selected'}}@endif @endif>{{$referred->referredBy}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-sm btn-outline-primary mr-1"><i class="fas fa-search"></i> Search</button>
                    <a href="{{route('viewAllReferrals')}}" class="btn btn-sm btn-outline-secondary ml-1"><i class="fas fa-clear"></i> Clear</a>
                </div>
            </div>
        </form>
    </div>
</div>
    <div class="table table-responsive jim-table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Sr.No.</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                    <th>Patient Name</th>
                    <th>Contact Number</th>
                    <th>Equipments</th>
                    <th>Location</th>
                    <th>Referred By</th>
                    <th>Contact Number</th>
                    <th>Comment</th>
                </tr>
            </thead>
            <tbody>
                @foreach($referrals as $referral)
                    <tr>
                        <td data-label="Sr.No.">{{$referrals->firstItem()+$loop->index}}</td>
                        <td data-label="Date">{{date('d-m-Y',strtotime($referral->insert_date))}}</td>
                        <td data-label="Status">
                            @if($referral->referralStatus == "Closed")
                                <span class="badge badge-danger">{{$referral->referralStatus}}</span>
                            @else
                                <span class="badge badge-success">{{$referral->referralStatus}}</span>
                            @endif
                        </td>
                        <td data-label="Action">
                            @if($referral->referralStatus == "Closed" OR $referral->referralStatus == "Paid")
                                <center>-</center>
                            @else
                                <a class="btn btn-outline-primary btn-sm btn-block " href="{{url('/')}}/view_details/{{$referral->id}}" role="button">Progress It</a></td>
                            @endif
                        <td data-label="Patient Name">{{$referral->referralName}}</td>
                        <td data-label="Contact Number">{{$referral->mobileNo}}</td>
                        <td data-label="Equipments">{{$referral->details}}</td>
                        <td data-label="Location">{{$referral->location}}</td>
                        <td data-label="Referred By">{{$referral->referredBy}}</td>
                        <td data-label="Contact Number">{{$referral->referredByMobileNo}}</td>
                        <td data-label="Comment">{{$referral->comment}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{$referrals->withPath(url()->full())->links('Custom.Pagination.pagination')}}
@endsection

@section('script')
<script>
</script>

@endsection