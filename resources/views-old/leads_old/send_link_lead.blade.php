@extends('header_and_sidebar')
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        
        <title>Send Link</title>
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
       
        <div class="col-md-12">
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
                    <center>Send Link </center>
                </div> 
                <div class="card-body">
                    <form action="{{url('/')}}/send_link_lead" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" class="form-control form-control-sm" placeholder="Customer Name..." name="customer_name" id="inpt_customer_name" value="" required>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control form-control-sm" placeholder="Contact No..." name="primary_contact_no" id="inpt_primary_contact_no"
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" maxlength="10" required>
                                    {{-- @if (count($errors) > 0)
                                        <span class="text text-danger text-smal">Contact no must atleast 10 digits.</span>
                                    @endif --}}
                                    @if ($errors->has('primary_contact_no'))
                                        <span class="text-danger">{{ $errors->first('primary_contact_no') }}</span>
                                    @endif
                            </div>
                            <div class="col-md-3">
                                <select class="form-control form-control-sm js-example-responsive " name="product_required[]" id="select_product_required" 
                                    title="Select Products" multiple="multiple" data-allow-clear="true" style="height: 50rem" required>
                                    @foreach($get_products as $key=>$product)
                                        <option value="{{$product->id}}">{{$product->product_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="text" class="form-control form-control-sm" placeholder="Lead Source..." name="lead_source" id="inpt_lead_source" value="" required>
                                @if ($errors->has('lead_source'))
                                    <span class="text-danger">{{ $errors->first('lead_source') }}</span>
                                @endif
                            </div>
                            <div class="col-md-1">
                                <input type="hidden" name="update_link_flag" id="inpt_update_link_flag" value="0">
                                <input type="hidden" name="update_link_id" id="inpt_update_link_id" value="">
                                <button type="submit" class="btn btn-outline-success btn-sm" name="btn_gen_link" id="btn_gen_link" value="Generate_Link">Generate</button>
                            </div>
                        </div>
                        <div class="modal fade" id="lead_link_modal" tabindex="-1" role="dialog" aria-labelledby="lead_link_modalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="lead_link_modalLabel">Link Generated</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                                </div>
                                <div class="modal-body">
                                    @if(!empty(Session::get('link')))
                                        <div class="input-group">
                                            <input type="text" class="form-control user-select-all btn_copy" name="lead_link" id="inpt_lead_link" value="{{Session::get('link')}}">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-success" type="button" id="btn_copy" data-clipboard-target="#inpt_lead_link" >Copy</button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @if(!empty(Session::get('session_customer_name')) && !empty(Session::get('session_link_id')) && !empty(Session::get('session_products')))
                                    <input type="hidden" name="sCustomerName" id="sCustomerName" value="{{Session::get('session_customer_name')}}">
                                    <input type="hidden" name="sLinkId" id="sLinkId" value="{{Session::get('session_link_id')}}">
                                    <input type="hidden" name="sProducts" id="sProducts" value="{{Session::get('session_products')}}">
                                    <input type="hidden" name="sContactNo" id="sContactNo" value="{{Session::get('session_contact_no')}}">
                                    <input type="hidden" name="sLeadSource" id="sLeadSource" value="{{Session::get('session_lead_source')}}">
                                @endif
                                {{-- <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div> --}}
                              </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div>
                    
                </div>
                <div class="table table-responsive p-2">
                    <table class="table table-hover" id="link_table" width="100%">
                        <thead>
                            <th>SrNo</th>
                            <th>Customer Name</th>
                            <th>Contact No</th>
                            <th>Product Name</th>
                            <th>Link Status</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @php
                                $i=0;
                            @endphp
                            @foreach($get_all_links as $key=>$link)
                                <tr>
                                    <td>{{$i+=1}}</td>
                                    <td>{{$link->customer_name}}</td>
                                    <td>{{$link->primary_contact_no}}</td>
                                    <td>{{$products_name_arr['data'][$key]['product_name']}}</td>
                                    <td>
                                        @if($link->link_status==0)
                                            <span class="badge badge-primary">Live</span>
                                        @elseif($link->link_status==1)
                                            <span class="badge badge-success">Success</span>  
                                        @else
                                            <span class="badge badge-danger">Expired</span>
                                        @endif
                                    </td>
                                    <td>{{\Carbon\Carbon::parse($link->created_at)->diffForHumans()}}</td>
                                    <td class="text-nowrap">
                                        @if($link->link_status==0)
                                            <button type="button" class="btn btn-outline-primary btn-sm btn_copy" title="Copy Link" 
                                                data-toggle="tooltip" data-placement="bottom" id="tbl_btn_copy{{$key}}" 
                                                data-clipboard-text="{{url('/')}}/create_lead_link/{{$link->link_id}}" onClick="showHideTool({{$key}});">
                                                <i class="far fa-copy"></i>
                                            </button>
                                            {{--Hidden values--}}
                                            <input type="hidden" name="update_customer_name" id="inpt_update_customer_name{{$key}}" value="{{$link->customer_name}}">
                                            <input type="hidden" name="update_customer_contact" id="inpt_update_customer_contact{{$key}}" value="{{$link->primary_contact_no}}">
                                            <input type="hidden" name="update_products" id="inpt_update_prodcuts{{$key}}" value="{{$link->products}}">
                                            <input type="hidden" name="update_lead_source" id="inpt_lead_source{{$key}}" value="{{$link->lead_source}}">
                                            <input type="hidden" name="update_link" id="inpt_link_id{{$key}}" value="{{$link->link_id}}">
                                            <button type="button" class="btn btn-outline-primary btn-sm" title="Update Link" 
                                                data-toggle="tooltip" data-placement="bottom" onClick="updateLink({{$key}});">
                                                Update
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
        @if(!empty(Session::get('session_customer_name')) && !empty(Session::get('session_link_id')) && !empty(Session::get('session_products')))
            <script>
                let sCustomerName =$('#sCustomerName').val();
                let sLinkId =$('#sLinkId').val();
                let sProducts = $('#sProducts').val();
                let sContactNo = $('#sContactNo').val();
                let sLeadSource = $('#sLeadSource').val();

                $('#btn_session_edit_link').on('click',function(){
                    //first empty the values 
                    $('#inpt_customer_name').val(null);
                    $('#inpt_primary_contact_no').val(null);
                    $('#inpt_update_link_flag').val(null);
                    $('#select_product_required').val(null);
                    $('#inpt_update_link_id').val(null);
                    $('#btn_gen_link').val(null);
                    $('#inpt_lead_source').val(null);
                    $('#select_product_required').trigger('change');
                    //then fill the values
                    $('#inpt_customer_name').val(sCustomerName);
                    $('#inpt_primary_contact_no').val(sContactNo);
                    $('#inpt_update_link_flag').val(1);
                    $('#btn_gen_link').val('Update_Link');
                    $('#inpt_update_link_id').val(sLinkId);
                    $('#inpt_lead_source').val(sLeadSource);
                    product_arr_parse = $.parseJSON(sProducts);
                    $('#select_product_required').val(product_arr_parse);
                    $('#select_product_required').trigger('change');
                    $(".alert").alert('close')
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
                $('#link_table').DataTable({});
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
            

            //link table 
           
            
            //update link
            function updateLink(id)
            {
                let cust_name = $('#inpt_update_customer_name'+id).val();
                let cust_contact = $('#inpt_update_customer_contact'+id).val();
                let cust_products = $('#inpt_update_prodcuts'+id).val();
                let lead_source = $('#inpt_lead_source'+id).val();
                let link_id = $('#inpt_link_id'+id).val();
                let link_flag = $('#inpt_update_link_flag').val();
                //first empty fields
                $('#inpt_customer_name').val(null);
                $('#inpt_primary_contact_no').val(null);
                $('#inpt_update_link_flag').val(null);
                $('#inpt_update_link_id').val(null);
                $('#btn_gen_link').val(null);
                $('#inpt_lead_source').val(null);
                $('#select_product_required').val(null);
                $('#select_product_required').trigger('change');
                //fill value
                $('#inpt_customer_name').val(cust_name);
                $('#inpt_primary_contact_no').val(cust_contact);
                $('#inpt_update_link_flag').val(1);
                $('#inpt_update_link_id').val(link_id);
                $('#inpt_lead_source').val(lead_source);
                $('#btn_gen_link').val('Update_Link');
                product_arr_parse = $.parseJSON(cust_products);
                $('#select_product_required').val(product_arr_parse);
                $('#select_product_required').trigger('change');
            }
        </script>
        
    @endsection
</body>
</html>
