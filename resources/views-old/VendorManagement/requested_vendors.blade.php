@extends('header_and_sidebar')
@section('title')
    Admin: Pending Vendors 
@endsection
@section('content')
    <form action="" method="POST">
        {{ csrf_field() }}
        <div class="container-fluid">
            <!-- DataTales Example -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Requested Vendors Registration</h6>
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
                                            @if($vendor_detail['authentication_status']=='Requested')
                                               <td data-label="Vendor Name" value='{{$vendor_detail['registered_name']}}'>{{$vendor_detail['registered_name']}}</td>
                                               <td data-label="Brand Name" value='{{$vendor_detail['brand_name']}}'>{{$vendor_detail['brand_name']}}</td>
                                               <td data-label="Landmark" value='{{$vendor_detail['of_landmark']}}'>{{$vendor_detail['of_landmark']}}</td>
                                               <td data-label="Mob No" value='{{$vendor_detail['of_primary_contact_1']}}'>{{$vendor_detail['of_primary_contact_1']}}</td>
                                               <td data-label="City">{{$vendor_detail['of_city']}}</td>
                                               <td data-label="Email" value='{{$vendor_detail['of_email']}}'>{{$vendor_detail['of_email']}}</td>
                                               <td data-label="Status" value='{{$vendor_detail['authentication_status']}}'>@if($vendor_detail['authentication_status']=='Requested'){{"Re-Submitted"}}@endif</td>
                                               <td data-label="Action"><a href = '@php echo url('/'); @endphp/vendor_details/{{$vendor_detail['id']}}' name='' class='btn btn-primary'>View Details</a> </td>
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
        </div>
        <!-- /.container-fluid -->
        
    </form>
@endsection