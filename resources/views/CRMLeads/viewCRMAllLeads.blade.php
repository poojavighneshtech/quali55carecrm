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
    {{-- <script src="<?php echo url(
        "/"
    ); ?>/assets/vendor/jquery/jquery.min.js"></script> --}}
    {{-- @section('styles')
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.2/css/bootstrap.min.css"/>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.min.css"/>
            <!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script> -->
            <script src="<?php echo url(
                "/"
            ); ?>/assets/vendor/jquery/jquery.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
        @endsection --}}
</head>

<body id="page-top">
    <!-- Page Wrapper -->


    @section('content')
    <div class="container">





<!-- Lead Tabs -->
{{-- <ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link active" href="#">
            All <span class="badge badge-primary">17</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">
            New <span class="badge badge-success">5</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">
            In Process <span class="badge badge-info">3</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">
            Hot Leads <span class="badge badge-danger">9</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">Follow up</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">Call Alerts</a>
    </li>
</ul>

<!-- Hot Leads Table -->
<div class="card border-primary">
    <div class="card-header text-primary d-flex justify-content-between">
        <h5 class="mb-0">Hot Leads</h5>
        <button class="btn btn-sm btn-outline-primary">Refresh</button>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered text-nowrap">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date Time</th>
                        <th>Customer Name</th>
                        <th>Mobile</th>
                        <th>City</th>
                        <th>Status</th>
                        <th>Lead Source</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>02-02-2026 02:45 PM</td>
                        <td>Prashant Wable</td>
                        <td>7350137700</td>
                        <td>Mumbai</td>
                        <td><span class="badge badge-warning">Order Generated</span></td>
                        <td>Web Order</td>
                        <td>
                            <button class="btn btn-success btn-sm">Verify</button>
                        </td>
                    </tr>

                    <tr>
                        <td>2</td>
                        <td>02-02-2026 02:39 PM</td>
                        <td>D</td>
                        <td>8657454596</td>
                        <td>Pune</td>
                        <td><span class="badge badge-success">Converted</span></td>
                        <td>Web Popup</td>
                        <td>
                            <button class="btn btn-success btn-sm">Verify</button>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
</div> --}}



<!-- Tabs -->
<ul class="nav nav-tabs mb-3" id="leadTabs">
    <li class="nav-item">
        <a class="nav-link active" data-target="all">All <span class="badge badge-primary">{{ $all_leads_count }}</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-target="new">New <span class="badge badge-success">{{ $all_new_leads_count }}</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-target="process">In Process <span class="badge badge-info">{{ $all_inProcess_leads_count }}</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-target="hot">Hot Leads <span class="badge badge-danger">{{ $all_hot_leads_count }}</span></a>
    </li>
</ul>

<!-- ================= ALL LEADS ================= -->
<div class="lead-table" id="all">
   <div class="card border-primary">
      <div class="card-header">
         <h5>All Leads</h5>
      </div>
      <div class="card-body">
         <table class="table table-bordered">
            <thead>
               <tr>
                  <th>#</th>
                  <th>Creation Date</th>
                  <th>Customer Name</th>
                  <th>Patient Name</th>
                  <th>Mobile Number</th>
                  <th>Equipment</th>
                  <th>Location</th>
                  <th>City</th>
                  <th>Status</th>
                  <th>Lead Source</th>
                  <th>Lead Owner</th>
                  <th>Action</th>
               </tr>
            </thead>
            <tbody>
                @forelse($get_all_leads_list as $key => $lead)

                    <tr>
                        <td>{{ $key + 1 }}</td>

                        <td>
                            {{ isset($lead->created_at) ? date('d-M-Y',strtotime($lead->created_at)).' '. date('h:i A',strtotime($lead->created_at)) : '-' }}
                        </td>

                        <td>{{ $lead->customer_name ?? '-' }}</td>

                        <td>{{ $lead->patientName ?? '-' }}</td>

                        <td>{{ $lead->contact_no ?? '-' }}</td>

                        <td>{{ $lead->product_names ?? '-' }}</td>

                        <td>{{ $lead->location ?? '-' }}</td>

                        <td>{{ $lead->city ?? '-' }}</td>

                        <td>
                            {{ $lead->order_status ?? '-' }}
                        </td>

                        <td>{{ $lead->lead_source ?? '-' }}</td>

                        <td>{{ $lead->username ?? '-' }}</td>

                        <td>
                            <button class="btn btn-success btn-sm verifyBtn" src="#verify_status"  data-toggle="modal" data-target="#verify_status" title="Verify" data-id="{{ $lead->cmsLeadsId }}">Verify</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center">No Hot Leads Found</td>
                    </tr>
                @endforelse
            </tbody>
         </table>
      </div>
   </div>
</div>

<!-- ================= NEW LEADS ================= -->
<div class="lead-table d-none" id="new">
   <div class="card border-success">
      <div class="card-header">
         <h5>New Leads</h5>
      </div>
      <div class="card-body">
         <table class="table table-bordered">
            <thead>
               <tr>
                  <th>#</th>
                  <th>Creation Date</th>
                  <th>Customer Name</th>
                  <th>Patient Name</th>
                  <th>Mobile Number</th>
                  <th>Equipment</th>
                  <th>Location</th>
                  <th>City</th>
                  <th>Status</th>
                  <th>Lead Source</th>
                  <th>Lead Owner</th>
                  <th>Action</th>
               </tr>
            </thead>
            <tbody>
                @forelse($get_all_new_leads_list as $key => $lead)

                    <tr>
                        <td>{{ $key + 1 }}</td>

                        <td>
                            {{ isset($lead->created_at) ? date('d-M-Y',strtotime($lead->creation_date)).' '. date('h:i A',strtotime($lead->converted_at)) : '-' }}
                        </td>

                        <td>{{ $lead->customer_name ?? '-' }}</td>

                        <td>{{ $lead->patientName ?? '-' }}</td>

                        <td>{{ $lead->contact_no ?? '-' }}</td>

                        <td>{{ $lead->product_names ?? '-' }}</td>

                        <td>{{ $lead->location ?? '-' }}</td>

                        <td>{{ $lead->city ?? '-' }}</td>

                        <td>
                            {{ $lead->order_status ?? '-' }}
                        </td>

                        <td>{{ $lead->lead_source ?? '-' }}</td>

                        <td>{{ $lead->username ?? '-' }}</td>
                        <td>
                            <button class="btn btn-success btn-sm verifyBtn" src="#verify_status"  data-toggle="modal" data-target="#verify_status" title="Verify" data-id="{{ $lead->cmsLeadsId }}">Verify</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center">No Hot Leads Found</td>
                    </tr>
                @endforelse
            </tbody>
         </table>
      </div>
   </div>
</div>

<!-- ================= IN PRICCESS ================= -->
<div class="lead-table d-none" id="process">
   <div class="card border-danger">
      <div class="card-header">
         <h5>In Process Leads</h5>
      </div>
      <div class="card-body">
         <table class="table table-bordered">
            <thead>
               <tr>
                  <th>#</th>
                  <th>Creation Date</th>
                  <th>Customer Name</th>
                  <th>Patient Name</th>
                  <th>Mobile Number</th>
                  <th>Equipment</th>
                  <th>Location</th>
                  <th>City</th>
                  <th>Status</th>
                  <th>Lead Source</th>
                  <th>Lead Owner</th>
                  <th>Action</th>
               </tr>
            </thead>
            <tbody>
                @forelse($get_all_inProcess_leads_list as $key => $lead)

                    <tr>
                        <td>{{ $key + 1 }}</td>

                        <td>
                            {{ isset($lead->created_at) ? date('d-M-Y',strtotime($lead->creation_date)).' '. date('h:i A',strtotime($lead->converted_at)) : '-' }}
                        </td>

                        <td>{{ $lead->customer_name ?? '-' }}</td>

                        <td>{{ $lead->patientName ?? '-' }}</td>

                        <td>{{ $lead->contact_no ?? '-' }}</td>

                        <td>{{ $lead->product_names ?? '-' }}</td>

                        <td>{{ $lead->location ?? '-' }}</td>

                        <td>{{ $lead->city ?? '-' }}</td>

                        <td>
                            {{ $lead->order_status ?? '-' }}
                        </td>

                        <td>{{ $lead->lead_source ?? '-' }}</td>

                        <td>{{ $lead->username ?? '-' }}</td>
                        <td>
                            <button class="btn btn-success btn-sm verifyBtn" src="#verify_status"  data-toggle="modal" data-target="#verify_status" title="Verify" data-id="{{ $lead->cmsLeadsId }}">Verify</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center">No Hot Leads Found</td>
                    </tr>
                @endforelse
            </tbody>
         </table>
      </div>
   </div>
</div>


<!-- ================= HOT LEADS ================= -->
<div class="lead-table d-none" id="hot">
   <div class="card border-danger">
      <div class="card-header">
         <h5>Hot Leads</h5>
      </div>
      <div class="card-body">
         <table class="table table-bordered">
            <thead>
               <tr>
                  <th>#</th>
                  <th>Creation Date</th>
                  <th>Customer Name</th>
                  <th>Patient Name</th>
                  <th>Mobile Number</th>
                  <th>Equipment</th>
                  <th>Location</th>
                  <th>City</th>
                  <th>Status</th>
                  <th>Lead Source</th>
                  <th>Lead Owner</th>
                  <th>Action</th>
               </tr>
            </thead>
            <tbody>
                @forelse($get_all_hot_leads_list as $key => $lead)

                    <tr>
                        <td>{{ $key + 1 }}</td>

                        <td>
                            {{ isset($lead->created_at) ? date('d-M-Y',strtotime($lead->creation_date)).' '. date('h:i A',strtotime($lead->converted_at)) : '-' }}
                        </td>

                        <td>{{ $lead->customer_name ?? '-' }}</td>

                        <td>{{ $lead->patientName ?? '-' }}</td>

                        <td>{{ $lead->contact_no ?? '-' }}</td>

                        <td>{{ $lead->product_names ?? '-' }}</td>

                        <td>{{ $lead->location ?? '-' }}</td>

                        <td>{{ $lead->city ?? '-' }}</td>

                        <td>
                            {{ $lead->order_status ?? '-' }}
                        </td>

                        <td>{{ $lead->lead_source ?? '-' }}</td>

                        <td>{{ $lead->username ?? '-' }}</td>
                        <td>
                            <button class="btn btn-success btn-sm verifyBtn" src="#verify_status"  data-toggle="modal" data-target="#verify_status" title="Verify" data-id="{{ $lead->cmsLeadsId }}">Verify</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center">No Hot Leads Found</td>
                    </tr>
                @endforelse
            </tbody>
         </table>
      </div>
   </div>
</div>

<!-- JS -->
<script>
document.querySelectorAll('#leadTabs .nav-link').forEach(tab => {
    tab.addEventListener('click', function () {

        // remove active tab
        document.querySelectorAll('#leadTabs .nav-link')
            .forEach(t => t.classList.remove('active'));

        this.classList.add('active');

        // hide all tables
        document.querySelectorAll('.lead-table')
            .forEach(table => table.classList.add('d-none'));

        // show selected table
        document.getElementById(this.dataset.target)
            .classList.remove('d-none');
    });
});
</script>



        <!-- <div class="leads">
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
                    <h5>Hot Leads</h5>
                </div>
                <div class="card-body">
                    <div class="table table-responsive">


                        <table class="table table-bordered text-nowrap" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Sr.No.</th>
                                    <th>Date Time</th>
                                    <th>Customer Name</th>
                                    <th>Mobile Number</th>
                                    <th>City</th>
                                    <th>Status</th>
                                    <th>Lead Source</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{!$sr_no = 1}}
                                @forelse($get_all_hot_leads as $key=>$lead)
                                <tr>
                                    <td>{{$get_all_hot_leads->firstitem()+$loop->index}}</td>
                                    <td>{{date("d-m-Y h:i:sa", strtotime($lead->created_at))}}</td>
                                    <td>{{$lead->customer_name}}</td>
                                    <td>{{$lead->contact_no}}</td>
                                    <td>{{$lead->city}}</td>
                                    <td>
                                        @php
                                        $statusText = [
                                        0 => 'Order Generated',
                                        1 => 'Converted'
                                        ];
                                        @endphp

                                        {{ $statusText[$lead->verify_status] ?? '-' }}
                                    </td>
                                    <td>{{$lead->source}}</td>
                                    <td>
                                        <button class="btn btn-success btn-sm verifyBtn" src="#verify_status"  data-toggle="modal" data-target="#verify_status" title="Verify" data-id="{{ $lead->cmsLeadsId }}">Verify</button>
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
                        {{$get_all_hot_leads->links('Custom.Pagination.pagination')}}
                    </div>
                </div>
            </div>
            </form>

        </div> -->
        {{-- <div class="modal" id="verify_status">
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
        </div> --}}

        <!-- Verify Modal -->
        <div class="modal fade" id="verify_status" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                
                <div class="modal-header">
                    <h5 class="modal-title">Verify Lead Status</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form id="verifyForm">
                    @csrf
                    <div class="modal-body">
                    
                    {{-- <input type="hidden" name="lead_id" id="lead_id"> --}}
                    <input type="hidden" name="cmsLeadsId" id="cmsLeadsId">

                    <div class="mb-3">
                        <label class="form-label">Select Status</label>
                        <select name="order_status" class="form-control" required>
                            <option value="">Select Status</option>
                            <option value="new">New</option>
                            <option value="Work In Process">In Process</option>
                            <option value="hotLead">Hot Lead</option>
                            <option value="Converted">Convert</option>
                        </select>
                    </div>

                    </div>

                    <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Submit</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </form>

                </div>
            </div>
        </div>
        
    </div>
    @endsection
</body>

</html>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
$(document).on('click', '.verifyBtn', function () {
    let leadId = $(this).data('id');
    $('#cmsLeadsId').val(leadId);
});
</script>
<script>
$(document).on('submit','#verifyForm',function(e){

    e.preventDefault();

    $.ajax({
        url: "{{ route('verifyStatus') }}",
        type: "POST",
        data: $(this).serialize(),

        success:function(response){

            if(response.success){

                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message,
                    confirmButtonColor: '#3085d6'
                }).then((result) => {
                    if(result.isConfirmed){
                        $('#verify_status').modal('hide');
                        location.reload();
                    }
                });

            }else{

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });

            }

        }
    });

});
</script>
<script>
    $(document).on('click', '#leadTabs .nav-link', function () {

        var tab = $(this).data('target');

        if(tab == 'new'){
            $('select[name="order_status"]').val('new');
        }
        else if(tab == 'process'){
            $('select[name="order_status"]').val('Work In Process');
        }
        else if(tab == 'hot'){
            $('select[name="order_status"]').val('hotLead');
        }
        else{
            $('select[name="order_status"]').val('');
        }

    });

</script>