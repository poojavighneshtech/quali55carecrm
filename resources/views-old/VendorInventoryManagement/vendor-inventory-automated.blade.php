@extends('header_and_sidebar')
@section('styles')
<style>
    #imageModalBody {
        /* margin:auto;
        width: fit-content;
        max-height: 825px; */
        overflow: auto;
    }

    #imageModalBody, #imageModalBody .img-fluid{
        /* max-width: 1300px;
        min-width: 250px;
        min-height: 250px; */
        max-width: 100%;
        min-width: 100%;
        min-height: 100%;
    }
    #imageModalBody .img-fluid {
        transform-origin: top left;
        -webkit-transform-origin: top left;
        -ms-transform-origin: top left;
    }
    #imageModalBody.rotate90 .img-fluid {
        transform: rotate(90deg) translateY(-100%);
        -webkit-transform: rotate(90deg) translateY(-100%);
        -ms-transform: rotate(90deg) translateY(-100%);
    }
    #imageModalBody.rotate180 .img-fluid {
        transform: rotate(180deg) translate(-100%, -100%);
        -webkit-transform: rotate(180deg) translate(-100%, -100%);
        -ms-transform: rotate(180deg) translateX(-100%, -100%);
    }
    #imageModalBody.rotate270 .img-fluid {
        transform: rotate(270deg) translateX(-100%);
        -webkit-transform: rotate(270deg) translateX(-100%);
        -ms-transform: rotate(270deg) translateX(-100%);
    }
</style>
@endsection
@section('content')

    @if(session()->has('error'))
    <div class="alert alert-danger">
        {{ session()->get('error') }}
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

    <div class="card my-3" id="filter_card">
        <div class="card-header"  id="filter_card">
            <div class="row">
                <div class="col text-primary" data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                    <strong>Vendor Inventory</strong>
                </div>
                <div class="col-auto">
                    <a data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                        <i class="fa fa-chevron-down pull-right"></i>&emsp;<b>Filter</b>
                    </a>
                </div>
            </div>
        </div> 
        <div class="card-body collapse" id="filter-collapse" aria-labelledby="headingTwo" data-parent="#filter_card">
            <form action="{{route('vendor-inventory-auto')}}" method="GET">
                @csrf
                <div class="row form-group">
                    <div class="col-md-3">
                        <label for="filtervdrname">Vendor Name</label>
                        {{-- <input type="text" name="filtervdrname" id="filtervdrname" class="form-control form-control-sm" value="{{request()->get('filtervdrname')}}"> --}}
                        <input type="text" class="form-control form-control-sm" name="filtervdrname" id="filtervdrname"  placeholder="Vendor Name.." 
                                                            size="5" autocomplete="off" value="{{request()->get('filtervdrname')}}">
                                                        <datalist id="datalist_vendor_name"></datalist>
                    </div>
                    <div class="col-md-3">
                        <label for="filterproducts">Products</label>
                        <select name="filterproducts[]" id="filterproducts" class="select selectpicker form-control form-control-sm" multiple="multiple" data-live-search="true" data-size="5">
                            @foreach($products as $product)
                                <option value="{{$product->id}}" @if(!empty(request()->get('filterproducts')))@if(in_array($product->id,request()->get('filterproducts'))){{"selected"}}@endif @endif>{{$product->product_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filterfromdate">From Date</label>
                        <input type="date" name="filterfromdate" id="filterfromdate" class="form-control form-control-sm" value="{{request()->get('filterfromdate')}}">
                    </div>
                    <div class="col-md-3">
                        <label for="filtertodate">To Date</label>
                        <input type="date" name="filtertodate" id="filtertodate" class="form-control form-control-sm" value="{{request()->get('filtertodate')}}">
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-3">
                        <label for="filterproductstatus">Product Status</label>
                        <select name="filterproductstatus" id="filterproductstatus" title="Select Product Status" class="select selectpicker form-control form-control-sm" data-size="5">
                            <option value="all"@if(request()->get('filterproductstatus') == 'all'){{'selected'}}@endif>All</option>
                            <option value="live" @if(request()->get('filterproductstatus') == 'live'){{'selected'}}@endif>Live</option>
                            <option value="stop" @if(request()->get('filterproductstatus') == 'stop'){{'selected'}}@endif>Stop</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filtervdrinvoiceno">Vendor Invoice No</label>
                        <input type="text" name="filtervdrinvoiceno" id="filtervdrinvoiceno" class="form-control form-control-sm" value="{{request()->get('filtervdrinvoiceno')}}">
                    </div>
                    <div class="col-md-3">
                        <label for="filterinvoicestatus">Invoice Status</label>
                        <select name="filterinvoicestatus" id="filterinvoicestatus" title="Select Invoice Status" class="select selectpicker form-control form-control-sm">
                            <option value="all" @if(request()->get('filterinvoicestatus') == 'all'){{'selected'}}@endif>All</option>
                            <option value="pending" @if(request()->get('filterinvoicestatus') == 'pending'){{'selected'}}@endif>Pending</option>
                            <option value="unverified" @if(request()->get('filterinvoicestatus') == 'unverified'){{'selected'}}@endif>Unverified</option>
                            <option value="verified" @if(request()->get('filterinvoicestatus') == 'verified'){{'selected'}}@endif>Verified</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filterpaymentstatus">Payment Status</label>
                        <select name="filterpaymentstatus" id="filterpaymentstatus" title="Select Payment Status" class="select selectpicker form-control form-control-sm">
                            <option value="all" @if(request()->get('filterpaymentstatus') == 'all'){{'selected'}}@endif>All</option>
                            <option value="unpaid" @if(request()->get('filterpaymentstatus') == 'unpaid'){{'selected'}}@endif>Pending</option>
                            <option value="partialpaid" @if(request()->get('filterpaymentstatus') == 'partialpaid'){{'selected'}}@endif>Partial Paid</option>
                            <option value="paid" @if(request()->get('filterpaymentstatus') == 'paid'){{'selected'}}@endif>Paid</option>
                        </select>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col text-center">
                        <button class="btn btn-sm btn-outline-success" name="submit" value="search" >Search</button>
                        <a class="btn btn-sm btn-outline-secondary" href="{{route('vendor-inventory-auto')}}">Clear</a>
                        {{-- <button class="btn btn-sm btn-outline-primary" name="submit" value="export" >Export</button> --}}
                    </div>
                </div>
            </form>
        </div>
        <div class="table table-responsive jim-table-responsive">
            <table class="table">
                <thead>
                    <tr class="text-nowrap">
                        <th>Action</th>
                        <th>Date</th>
                        <th>Vendor Name</th>
                        <th>Product</th>
                        <th>Product Rent</th>
                        <th>Vendor Rate</th>
                        {{-- <th>Warehouse</th> --}}
                        <th>Inventory No</th>
                        <th>Product Type</th>
                        <th>Order Type</th>
                        <th>Status</th>
                        <th>Stop Date</th>
                        <th>Invoice No</th>
                        <th>Vendor Serial No</th>
                        <th>Invoice Status</th>
                        <th>Invoice Image</th>
                        <th>Verified At</th>
                        <th>Verified By</th>
                        <th>Payment State</th>
                        <th>Payment Image</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventories as $key=>$inventory)
                        <tr class="text-nowrap">
                            <td data-label="Action"><button type="button" class="btn btn-sm btn-outline-primary" value="{{json_encode($inventory)}}" onclick="editrow($(this).val());"><i class="fas fa-edit"></i></button></td>
                            <td data-label="Date">{{$inventory->DelDate}}</td>
                            <td data-label="Vendor Name">{{$inventory->registered_name}}</td>
                            <td data-label="Product">{{$inventory->product_name}}</td>
                            <td data-label="Product Rent">{{$inventory->prod_rent}}</td>
                            <td data-label="Vendor Rate">{{$inventory->vdr_rent}}</td>
                            {{-- <td data-label="Warehouse">{{$inventory->wh_name.', '.$inventory->wh_area.', '.$inventory->wh_city}}</td> --}}
                            <td data-label="Inventory No">{{$inventory->unique_id}}</td>
                            <td data-label="Product Type">{{$inventory->sale_rental}}</td>
                            <td data-label="Order Type">
                                @if($inventory->deliverypickup == 'Delivery')
                                    {{"D"}}
                                    {{-- {{date("d-M-Y",strtotime($inventory->start_date))." - ".date("d-M-Y",strtotime($inventory->end_date))}} --}}
                                @elseif($inventory->deliverypickup == 'Collection')
                                    {{"C"}}
                                    {{date("d-M-Y",strtotime($inventory->start_date))." - ".date("d-M-Y",strtotime($inventory->end_date))}}
                                @else
                                    {{"P"}}
                                @endif
                            </td>
                            <td data-label="Status">
                                {{-- {{$inventory->current_status}} --}}
                                @if($inventory->current_status == 'Picked Up' || $inventory->current_status == 'Picked UP')
                                    <span class="badge badge-danger">{{"Stop"}}</span>
                                @else
                                    <span class="badge badge-success">{{"Live"}}</span>
                                @endif
                            </td>
                            <td data-label="Stop Date">{{$inventory->stop_date}}</td>
                            <td data-label="Invoice No">{{$inventory->invoice_no}}</td>
                            <td data-label="Vendor Serial No">{{$inventory->vdr_serial_no}}</td>
                            <td data-label="Invoice Status">{{$inventory->invoice_status}}</td>
                            <td data-label="Invoice Image">
                                @if(!empty($inventory->vendor_invoice_img))
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="showpaymentaaa{{$key}}" value="{{url('/').'/assets/uploads/vdr_invoice_img/'.$inventory->vendor_invoice_img}}" onclick="showImage($(this).val())"><i class="fas fa-image"></i></button>
                                @else
                                    -
                                @endif
                            </td>
                            <td data-label="Verified At">{{$inventory->verified_at}}</td>
                            <td data-label="Verified By">{{$inventory->verified_by}}</td>
                            <td data-label="Payment State">{{$inventory->payment_state}}</td>
                            <td data-label="Payment Image">
                                @if(!empty($inventory->payment_img))
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="showpaymentimg{{$key}}" value="{{url('/').'/assets/uploads/vdr_pay_img/'.$inventory->payment_img}}" onclick="showImage($(this).val())"><i class="fas fa-image"></i></button>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
        {{$inventories->withPath(url()->full())->links('Custom.Pagination.pagination')}}
    </div>
    <div id="updateinventorymodal" class="modal modal-fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span id="modal_title">Update Details</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body container-fluid">
                    <form action="{{route('vendor-inventory-auto')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="updateid" id="updateid">
                        <input type="hidden" name="updatetype" id="updatetype">
                        <div class="row form-group">
                            <div class="col">
                                <label for="updateinventoryid">Inventory Id</label>
                                <input type="text" name="updateinventoryid" id="updateinventoryid" class="form-control form-control-sm">
                            </div>
                        </div>
			            <div class="row form-group">
                            <div class="col">
                                <label for="updatevdrserialno">Vendor Serial No</label>
                                <input type="text" name="updatevdrserialno" id="updatevdrserialno" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col">
                                <label for="updateinvoiceno">Vendor Invoice No</label>
                                <input type="text" name="updateinvoiceno" id="updateinvoiceno" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col">
                                <label for="updatevdrrate">Vendor Rate</label>
                                <input type="text" name="updatevdrrate" id="updatevdrrate" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col">
                                <label for="updateinvoiceimg">Invoice Image</label>
                                <input type="file" name="updateinvoiceimg" id="updateinvoiceimg">
                                <center>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="showinvoiceimg" name="showinvoiceimg" onclick="showImage($(this).val())"><i class="fas fa-image"></i></button>
                                </center>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col">
                                <label for="updateinvoicestatus">Invoice Status</label>
                                <div class="btn-group btn-group-toggle" width="100%" data-toggle="buttons">
                                    <label class="btn btn-primary verified" id="labelverified">
                                    <input type="radio" name="invoice_status" id="updateverified" value="1"> Verified
                                    </label>
                                    <label class="btn btn-primary unverified" id="labelunverified">
                                    <input type="radio" name="invoice_status" id="updateunverified" value="2"> Unverified
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col">
                                <label for="updatepaymentstate">Payment State</label>
                                <div class="btn-group btn-group-toggle" width="100%" data-toggle="buttons">
                                    <label class="btn btn-primary verified" id="labelpaid">
                                    <input type="radio" name="payment_state" id="updatepaid" value="3"> Paid
                                    </label>
                                    <label class="btn btn-primary unverified" id="labelpartialpaid">
                                    <input type="radio" name="payment_state" id="updatepartialpaid" value="2"> Partial Paid
                                    </label>
                                    <label class="btn btn-primary unverified" id="labelunpaid">
                                        <input type="radio" name="payment_state" id="updateunpaid" value="1"> Pending
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col">
                                <label for="updatepaymentimg">Payment Image</label>
                                <input type="file" name="updatepaymentimg" id="updatepaymentimg">
                                <center>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="showpaymentimg" name="showpaymentimg" onclick="showImage($(this).val())"><i class="fas fa-image"></i></button>
                                </center>
                            </div>
                        </div>
                        
                        <div class="row form-group">
                            <div class="col text-center">
                                <button class="btn btn-sm btn-outline-success">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div id="showimageModal" class="modal modal-fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span id="modal_title">Payment Image</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body container-fluid">
                    {{-- <img src="" id="showimgtag" alt="No Image Found"> --}}
                    <div id="imageModalBody" data-rotation="0" class="rotate0">
                        {{-- <img class="img-fluid img rotate-right" src="" id="showimgtag" alt="No Image Found"> --}}
                        <iframe src="" id="showimgtag"  frameborder="0" width="100%" height="400px"></iframe>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $("#filtervdrname").on("click", function(){
            var route = "{{ url('complaint_vendors_populate') }}";
            $('#filtervdrname').typeahead({ 
                source: function (query, process) {
                    return $.get(route, {
                        query: query
                    }, function (data) {
                        //var obj = jQuery.parseJSON(data);
                        //console.log(data);
                        return process(data);
                    });
                }
            });
        });
        function editrow(inv){
            let inventory = JSON.parse(inv);
            console.log(inventory);
            $("#updateinventoryid").val(inventory.unique_id);
            $("#updateinvoiceno").val(inventory.invoice_no);
            $("#updatevdrrate").val(inventory.vdr_rent);
            $("#updatevdrserialno").val(inventory.vdr_serial_no);
            if(inventory.invoice_status != null && inventory.invoice_status != "")
            {
                if(inventory.invoice_status == 'verified'){
                    $("#updateverified").attr('checked',true);
                    $("#labelverified").addClass('active');
                    $("#labelunverified").removeClass('active');
                    $("#updateunverified").removeAttr('checked');
                }
                else{
                    $("#updateunverified").attr('checked',true);
                    $("#labelunverified").addClass('active');
                    $("#labelverified").removeClass('active');
                    $("#updateverified").removeAttr('checked');
                }
            }
            else{
                $("#updateunverified").attr('checked',true);
                $("#labelunverified").addClass('active');
                $("#labelverified").removeClass('active');
                $("#updateverified").removeAttr('checked');
            }
            if(inventory.payment_state != null && inventory.payment_state != "")
            {
                if(inventory.payment_state == 'paid'){
                    $("#updatepaid").attr('checked',true);
                    $("#labelpaid").addClass('active');
                    $("#labelpartialpaid").removeClass('active');
                    $("#updatepartialpaid").removeAttr('checked');
                    $("#labelunpaid").removeClass('active');
                    $("#updateunpaid").removeAttr('checked');
                }
                else if(inventory.payment_state == 'partialpaid'){
                    $("#labelpartialpaid").addClass('active');
                    $("#updatepartialpaid").attr('checked',true);
                    $("#updatepaid").removeAttr('checked',true);
                    $("#labelpaid").removeClass('active');
                    $("#labelunpaid").removeClass('active');
                    $("#updateunpaid").removeAttr('checked');
                }
                else{
                    $("#updateunpaid").attr('checked',true);
                    $("#labelunpaid").addClass('active');
                    $("#labelpartialpaid").removeClass('active');
                    $("#updatepartialpaid").removeAttr('checked');
                    $("#labelpaid").removeClass('active');
                    $("#updatepaid").removeAttr('checked');
                }
            }
            else{
                $("#updateunpaid").attr('checked',true);
                $("#labelunpaid").addClass('active');
                $("#labelpartialpaid").removeClass('active');
                $("#updatepartialpaid").removeAttr('checked');
                $("#labelpaid").removeClass('active');
                $("#updatepaid").removeAttr('checked');
            }
            if(inventory.payment_img != null && inventory.payment_img != ""){
                $("#showpaymentimg").show();
                $("#showpaymentimg").val("{{url('/').'/assets/uploads/vdr_pay_img/'}}"+inventory.payment_img);
            }else{
                $("#showpaymentimg").hide();
            }
            if(inventory.vendor_invoice_img != null && inventory.vendor_invoice_img != ""){
                $("#showinvoiceimg").show();
                $("#showinvoiceimg").val("{{url('/').'/assets/uploads/vdr_invoice_img/'}}"+inventory.vendor_invoice_img);
            }else{
                $("#showinvoiceimg").hide();
            }
            $("#updateid").val(inventory.id);
            $("#updatetype").val(inventory.deliverypickup);
            // $("#updateinvoicestatus").val(inventory.unique_id);
            // $("#updatepaymentstate").val(inventory.unique_id);
            // $("#updatepaymentimg").val(inventory.unique_id);
            $("#updateinventorymodal").modal("show");
        }
        function showImage(img){
            $("#showimgtag").attr('src',img);
            $("#showimageModal").modal("show");
        }
    </script>
@endsection