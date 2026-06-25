@extends('header_and_sidebar')
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>CRM Leads</title>
    {{-- <script src="<?php echo url('/'); ?>/assets/vendor/jquery/jquery.min.js"></script> --}}
    {{-- @section('styles')
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.2/css/bootstrap.min.css"/>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.min.css"/>
            <!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script> -->
            <script src="<?php echo url('/'); ?>/assets/vendor/jquery/jquery.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
        @endsection --}}
</head>

<body id="page-top">
    <!-- Page Wrapper -->


    @section('content')
    <div class="container">
        <div class="leads">
            @if(session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
            @endif
            @if(session()->has('message_delete'))
            <div class="alert alert-danger">
                {{ session()->get('message_delete') }}
            </div>
            @endif
            <div class="card border-primary">
                <div class="card-header text-primary">
                    <h5>View Website Order</h5>
                </div>
                <div class="card-body">
                    <div class="table table-responsive">


                        <table class="table table-bordered text-nowrap" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Date Time</th>
                                    <th>customer_name</th>
                                    <th>Mobile No</th>
                                    <th>City</th>
                                    <th>Equipment</th>
                                    <th>QTY</th>
                                    <th>Amount</th>
                                    <th>Action</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                {{!$sr_no = 1}}
                                @forelse($ordersList as $key=>$lead)
                                <tr>
                                    <td>{{date("d-m-Y h:i:sa", strtotime($lead->orderDate))}}</td>
                                    <td>{{$lead->customer_name}}</td>
                                    <td>{{$lead->mobile}}</td>
                                    <td>{{$lead->city}}</td>
                                    <td style="white-space:normal; word-break:break-word;">{{$lead->productName}}</td>
                                    <td>{{$lead->quantity}}</td>
                                    <td>{{$lead->totalAmount}}</td>
                                    <td>
                                        <!-- <button class="btn btn-success btn-sm verifyBtn" src="#verify_status"  data-toggle="modal" data-target="#verify_status" title="Verify" data-id="{{ $lead->orderId }}">Converted</button> -->
                                        <button class="btn btn-success btn-sm convertBtn" data-id="{{ $lead->orderId }}">Converted</button>
                                    </td>

                                    

                                    
                                </tr>
                               
                                {{!$sr_no = $sr_no + 1}}
                                @empty
                                <tr>
                                    <td colspan="9">
                                        <h4 class="text-center">No Records Found</h4>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            </form>

        </div>
        <div class="modal" id="verify_status">
            <div class="modal-dialog">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <form method="POST" action="{{ route('leadverify') }}">
                    @csrf

                        <div class="modal-header">
                            <h4 class="modal-title">Verify Status</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>

                        <!-- Modal body -->
                        <input type="hidden" name="lead_id" id="lead_id" class="form-control mb-2" readonly>
                        <div class="modal-body">
                            <select class="form-control" name="verify_status" required>
                                <option disabled selected>--Verify Status*--</option>
                                <option value="0">Pending</option>
                                <option value="1">Verify</option>
                            </select>
                        </div>

                        <!-- Modal footer -->
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-secondary" href="#" title="Close">Sumbit</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        
    </div>
    @endsection
</body>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
$(document).on('click', '.verifyBtn', function () {
    let leadId = $(this).data('id');
    $('#lead_id').val(leadId);
});

$(document).on('click','.convertBtn',function(){

    var orderId = $(this).data('id');

    Swal.fire({
        title: 'Are you sure?',
        text: "Do you want to convert this order?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33'
    }).then((result) => {

        if (result.isConfirmed) {

            // yaha ajax ya redirect kar sakte ho
            window.location.href = "/convert-order/"+orderId;

        }

    });

});
</script>

</html>