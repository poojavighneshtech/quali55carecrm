@extends('header_and_sidebar')
@section('title')
    Admin: Approved Vendors
@endsection
@section('content')
    <form action="" method="POST">
        {{ csrf_field() }}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Approved Vendors Registration</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive jim-table-responsive">
                    <table class="table table-bordered" id="records" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Vendor Name</th>
                                <th>Brand Name</th>
                                <th>Landmark</th>
                                <th>Mob No</th>
                                <th>City</th>
                                <th>Email</th>                                            
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                            
                        <tbody>
                            @if (isset($vendor_details) and $vendor_details!='null')
                                @foreach ($vendor_details as $vendor_detail)
                                    <tr>
                                        @if($vendor_detail['authentication_status']=='Approved')
                                            <td value='{{$vendor_detail['registered_name']}}' data-label="Vendor Name">{{$vendor_detail['registered_name']}}</td>
                                            <td value='{{$vendor_detail['brand_name']}}' data-label="Brand name">{{$vendor_detail['brand_name']}}</td>
                                            <td value='{{$vendor_detail['of_landmark']}}' data-label="Landmark">{{$vendor_detail['of_landmark']}}</td>
                                            <td value='{{$vendor_detail['of_primary_contact_1']}}' data-label="Mob no">{{$vendor_detail['of_primary_contact_1']}}</td>
                                            <td data-label="City">{{$vendor_detail['of_city']}}</td>
                                            <td value='{{$vendor_detail['of_email']}}' data-label="Email">{{$vendor_detail['of_email']}}</td>
                                            <td value='{{$vendor_detail['authentication_status']}}' data-label="Status">{{$vendor_detail['authentication_status']}}</td>
                                            <td class="text-nowrap" data-label="Action">
                                                <a href = '@php echo url('/'); @endphp/vendor_details/{{$vendor_detail['id']}}' name='' class='btn btn-primary btn-sm'>View Details</a>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @else
                                <h2>No Pending Record Available</h2>                                                
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>	                    
    </form>
@endsection