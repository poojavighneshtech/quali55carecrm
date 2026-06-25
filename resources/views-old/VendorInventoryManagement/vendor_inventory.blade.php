@extends('header_and_sidebar')
@section('title')
    Vendor Inventory
@endsection
@section('content')
    <form action="" method="POST">
        {{ csrf_field() }}
        {{-- <div class="main"> --}}
            <div class="container-fluid">
                <!-- DataTales Example -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Vendor Inventory</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered " id="records" width="100%" cellspacing="0">
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
                                                @if($vendor_detail['authentication_status']=='Pending')
                                                <td value='{{$vendor_detail['registered_name']}}'>{{$vendor_detail['registered_name']}}</td>
                                                <td value='{{$vendor_detail['brand_name']}}'>{{$vendor_detail['brand_name']}}</td>
                                                <td value='{{$vendor_detail['of_landmark']}}'>{{$vendor_detail['of_landmark']}}</td>
                                                <td value='{{$vendor_detail['of_primary_contact_1']}}'>{{$vendor_detail['of_primary_contact_1']}}</td>
                                                <td>{{$vendor_detail['of_city']}}</td>
                                                <td value='{{$vendor_detail['of_email']}}'>{{$vendor_detail['of_email']}}</td>
                                                <td value='{{$vendor_detail['authentication_status']}}'>{{$vendor_detail['authentication_status']}}</td>
                                                <td><a href = '{{url('/')}}/vendor_details/{{$vendor_detail['id']}}' name='' class='btn btn-primary'>View Details</a> </td>
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
        {{-- </div> --}}
        <!-- /.container-fluid -->
        
    </form>
@endsection

@section('script')
<script>
    // $('#tbody').on('click', 'tr', function () {

    //     var data = table.row( this ).data();

    //     alert( 'You clicked on '+data[0]+'\'s row' );

    // } );
</script>

@endsection