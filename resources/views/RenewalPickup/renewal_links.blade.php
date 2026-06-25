@extends('header_and_sidebar')
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        
        <title>Renewal Link</title>
        <link rel="stylesheet" href="{{url('/')}}/assets/dist/toast.min.css ">
        <script src="{{url('/')}}/assets/dist/clipboard.min.js"></script>
        <!-- Boostrap 4 CSS -->
        @section('styles')
            <style>
            </style>
        @endsection
    </head>

<body id="page-top">	
    @section('content')
       
        @if(session()->has('message_delete'))
            <div class="alert alert-danger">
                {{ session()->get('message_delete') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if(session()->has('CustExist'))
            <div class="alert alert-warning">
                <strong>{{ session()->get('CustExist') }}</strong>..&emsp;
                    <button type="button" class="btn btn-outline-primary btn-sm" id="btn_session_edit_link">Edit Link</button>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if(session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <div class="card">  
            <div class="card-header"  style="background-color: #4a6fdc; color: white;" >
                <center>Renewal Links </center>
            </div> 
            <div class="card-body">
                <form action="" action="{{url('/')}}/get_renewal_links" method="get">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" class="form-control form-control-sm" placeholder="Customer Name" name="cust_name" id="cust_name" value="@if(isset($filter['cust_name'])){{$filter['cust_name']}}@endif">
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control form-control-sm" placeholder="Customer Name" name="start_date" id="start_date" value="@if(isset($filter['start_date'])){{$filter['start_date']}}@endif">
                        </div>
                        <div class="col-auto">
                            To
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control form-control-sm" placeholder="Customer Name" name="end_date" id="end_date" value="@if(isset($filter['end_date'])){{$filter['end_date']}}@endif">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-outline-secondary btn-sm btn-block" id="btn_clear">Clear</button>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-outline-success btn-sm btn-block" >Submit</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="table table-responsive jim-table-responsive">
                <table class="table table-hover" id="link_table" width="100%">
                    <thead class="thead-light">
                        <th>SrNo</th>
                        <th>Customer Name</th>
                        <th>Contact No</th>
                        <th>Status</th>
                        <th>created at</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        @php
                            $i=0;
                        @endphp
                        @foreach($get_renewal_link as $key=>$link)
                            <tr>
                                <td data-label="Sr No">{{$i+=1}}</td>
                                <td data-label="Customer Name">{{$link->customer_name}}</td>
                                <td data-label="Contact No">{{$link->primary_contact_no}}</td>
                                <td data-label="Status">
                                    @if($link->link_status==0 && $link->admin_r_link_status==0)
                                        <span class="badge badge-primary">Live</span>
                                    @elseif($link->link_status==1 && $link->admin_r_link_status==0)
                                        <span class="badge badge-warning">Attended</span>
                                    @elseif($link->link_status==0 && $link->admin_r_link_status==1)
                                        <span class="badge badge-success">handled</span>
                                    @elseif($link->link_status==1 && $link->admin_r_link_status==1)
                                        <span class="badge badge-warning">Attended</span> <span class="badge badge-success">handled</span>
                                    @else
                                        <span class="badge badge-danger">Expired</span>
                                    @endif
                                </td>
                                <td data-label="Created At">{{\Carbon\Carbon::parse($link->created_at)->diffForHumans()}}</td>
                                <td class="text-nowrap" data-label="Action">
                                    @if($link->link_status!=2)
                                        <button type="button" class="btn btn-outline-primary btn-sm btn_copy" title="Copy Link" 
                                            data-toggle="tooltip" data-placement="bottom" id="tbl_btn_copy{{$key}}" 
                                            data-clipboard-text="{{url('/')}}/customer_renewal_or_pickup_link/{{$link->link_id}}" onClick="showHideTool({{$key}});">
                                            <i class="far fa-copy"></i>
                                        </button>
                                        <a href="{{url('/')}}/view_renewal_or_pickup_link/{{$link->link_id}}" class="btn btn-outline-primary btn-sm">View Link</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @php
                    $append_arr = array();
                    if(isset($filter['cust_name'])){
                        $append_arr['cust_name'] = $filter['cust_name'];
                    }
                    if(isset($filter['start_date'])){
                        $append_arr['start_date'] = $filter['start_date'];
                    }
                    if(isset($filter['end_date'])){
                        $append_arr['end_date'] = $filter['end_date'];
                    }
                @endphp
                {{$get_renewal_link->appends($append_arr)->links('Custom.Pagination.pagination')}}
            </div>
        </div>
    @endsection
    @section('script')
        @if(!empty(Session::get('link')))
            <script>
                $(function() {
                    $('#lead_link_modal').modal('show');
                });
                $(document).ready(function() {
                    const input = document.getElementById('inpt_lead_link');
                    input.focus();
                    input.setSelectionRange(6,11);
                });
            </script>
        @endif
        <script>
             $(document).ready(function() {
               var select2 = $('#select_product_required').select2({
                                    placeholder: 'Select Products',
                                    allowClear: true,
                                });
               
                //select2.data('select2').$selection.css('height', '38px');
               
                $(function () {
                    $('[data-toggle="tooltip"]').tooltip()
                });
                //$('#link_table').DataTable({});
            });
            $('#btn_copy').on('click', function(){
                var clipboard = new ClipboardJS('#btn_copy');
                clipboard.on('success', function(e) {
                    // console.info('Action:', e.action);
                    // console.info('Text:', e.text);
                    // console.info('Trigger:', e.trigger);
                    
                    setTooltip('Copied!');
                    hideTooltip();
                    //e.clearSelection();
                });
                $('#btn_copy').tooltip({
                    trigger: 'click',
                    placement: 'bottom'
                });
                function setTooltip(message) {
                    $('#btn_copy').tooltip('hide')
                    .attr('data-original-title', message)
                    .tooltip('show');
                }
                function hideTooltip() {
                    setTimeout(function() {
                        $('#btn_copy').tooltip('hide');
                    }, 1000);
                }
                clipboard.on('error', function(e) {
                    // console.error('Action:', e.action);
                    // console.error('Trigger:', e.trigger);
                    setTooltip('Failed!');
                    hideTooltip();      
                });
            });
            function showHideTool(id)
            {
                var clipboard = new ClipboardJS('#tbl_btn_copy'+id);
                clipboard.on('success', function(e) {
                    // console.info('Action:', e.action);
                    // console.info('Text:', e.text);
                    // console.info('Trigger:', e.trigger);
                    
                    setTooltip('Copied!');
                    hideTooltip();
                    //e.clearSelection();
                });
                $('#tbl_btn_copy'+id).tooltip({
                    trigger: 'click',
                    placement: 'bottom'
                });
                function setTooltip(message) {
                    $('#tbl_btn_copy'+id).tooltip('hide')
                    .attr('data-original-title', message)
                    .tooltip('show');
                }
                function hideTooltip() {
                    setTimeout(function() {
                        $('#tbl_btn_copy'+id).tooltip('hide');
                    }, 1000);
                }
                clipboard.on('error', function(e) {
                    // console.error('Action:', e.action);
                    // console.error('Trigger:', e.trigger);
                    setTooltip('Failed!');
                    hideTooltip();      
                });
            }
            $('#btn_clear').on('click', function(){
                window.location.href = "{{url('/')}}"+"/get_renewal_links";
                // $('#cust_name').val('');
                // $('#start_date').val('');
                // $('#end_date').val('');
            });
        </script>
        
    @endsection
</body>
</html>
