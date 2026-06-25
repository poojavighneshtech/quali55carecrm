@extends('header_and_sidebar')

@section('styles')

@endsection

@section('content')

    @if(session()->has('message'))
        <div class="alert alert-success">
            {{ session()->get('message') }}
        </div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger">
            {{ session()->get('error') }}
        </div>
    @endif

    <div class="my-2">
        <div class="card" id="filter_card">
            <div class="card-header border-primary" id="filter_card">
                <div class="row">
                    <div class="col text-primary" data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                        <strong>Vendor Billing</strong>
                    </div>
                    <div class="col-auto">
                        <a data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                            <i class="fa fa-chevron-down pull-right"></i>&emsp;<b>Filter</b>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body border-primary collapse" id="filter-collapse" aria-labelledby="headingTwo" data-parent="#filter_card">
                <form action="{{route('vendor-billing')}}" method="GET">
                    <div class="row form-group">
                        <div class="col-md-2">
                            <label for="filtervendorid">Vendor</label>
                            <select name="filtervendorid" id="filtervendorid" class="form-control form-control-sm select selectpicker" data-live-search="true" data-size="5" title="Select Vendor">
                                @forelse($vendors as $key=>$vendor)
                                    <option value="{{$vendor->id}}" @if(request()->get('filtervendorid') == $vendor->id){{"selected"}}@endif>{{$vendor->vendor_name}}</option>
                                @empty
                                    <option value="na" disabled selected>No Vendors Found</option>
                                @endforelse
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="filterinvoiceno">Vendor Invoice No.</label>
                            <input type="text" name="filterinvoiceno" id="filterinvoiceno" class="form-control form-control-sm" value="{{request()->get('filterinvoiceno')}}">
                        </div>
                        <div class="col-md-4">
                            <label for="filterinvoicedate">Vendor Invoice Date<small>  (Date Range)</small></label>
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="date" name="filterinvoicedatefrom" id="filterinvoicedatefrom" class="form-control form-control-sm" value="{{request()->get('filterinvoicedatefrom')}}">
                                </div>
                                <div class="col-md-6">
                                    <input type="date" name="filterinvoicedateto" id="filterinvoicedateto" class="form-control form-control-sm" value="{{request()->get('filterinvoicedateto')}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="filterinvoicestatus">Invoice Status</label>
                            <select name="filterinvoicestatus" id="filterinvoicestatus" class="form-control form-control-sm select selectpicker" data-live-search="true" data-size="5" title="Invoice Status">
                                <option value="verified" @if(request()->get('filterinvoicestatus') == "verified"){{"selected"}}@endif>Verified</option>
                                <option value="unverified" @if(request()->get('filterinvoicestatus') == "unverified"){{"selected"}}@endif>Unverified</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="filterpaymentstate">Payment State</label>
                            <select name="filterpaymentstate" id="filterpaymentstate" class="form-control form-control-sm select selectpicker" data-live-search="true" data-size="5" title="Payment State">
                                <option value="unpaid" @if(request()->get('filterpaymentstate') == "unpaid"){{"selected"}}@endif>Unpaid</option>
                                <option value="partialpaid" @if(request()->get('filterpaymentstate') == "partialpaid"){{"selected"}}@endif>Partial Paid</option>
                                <option value="paid" @if(request()->get('filterpaymentstate') == "paid"){{"selected"}}@endif>Paid</option>
                            </select>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col text-center">
                            <button type="button" class="btn btn-outline-primary btn-sm" name="add_invoice" id="add_invoice">Add Invoice Record</button>        
                            <button type="submit" class="btn btn-outline-success btn-sm" name="form_submit" value="search" id="add_invoice">Search</button>        
                            <a type="button" class="btn btn-outline-secondary btn-sm" name="form_reset" id="reset" href="{{route('vendor-billing')}}">Reset</a>        
                            {{-- <button type="submit" class="btn btn-outline-warning btn-sm" name="form_submit" value="export" id="export">Export</button>         --}}
                        </div>
                    </div>
                </form>
                {{-- <div class="row text-center">
                    <button type="button" class="btn btn-outline-primary btn-sm" name="add_invoice" id="add_invoice">Add Invoice Record</button>
                </div> --}}
            </div>
            <div class="table table-responsive jim-table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            {{-- <th>Invoice Date</th> --}}
                            <th>Action</th>
                            <th>Date</th>
                            <th>Invoice No</th>
                            <th>Invoice Date</th>
                            <th>Vendor Name</th>
                            <th>Mob No</th>
                            <th>Invoice Amount</th>
                            <th>Invoice Status</th>
                            <th>Invoice Image</th>
                            <th>Verified At</th>
                            <th>Verified By</th>
                            <th>Vdr. Pickup Date</th>
                            <th>Vdr. Return Date</th>
                            <th>Invoice Period</th>
                            <th>Vendor Serial No.</th>
                            <th>Inventory No.</th>
                            <th>Payment State</th>
                            <th>Payment Image</th>
                            <th>Comment</th>
                        </tr>
                    </thead>
                        
                    <tbody>
                        @foreach($vendorBilling as $key=>$value)
                            <tr class="text-nowrap">
                                <td data-label="Action">
                                    <button type="button" class="btn btn-sm btn-outline-primary" value="{{json_encode($value)}}" onclick="editrow($(this).val());"><i class="fas fa-edit"></i></button>
                                </td>
                                <td class="text-nowrap" data-label="Date">{{date('d-M-y',strtotime($value->created_at))}}</td>
                                <td data-label="Invoice No">{{$value->vendor_invoice_no}}</td>
                                <td data-label="Invoice Date">{{($value->vendor_invoice_date)?date('d-M-y',strtotime($value->vendor_invoice_date)):"-"}}</td>
                                <td data-label="Vendor Name">{{$value->registered_name}}</td>
                                <td data-label="Mob No">{{$value->of_primary_contact_1}}</td> 
                                <td data-label="Invoice Amount">{{$value->vendor_invoice_rate}}</td> 
                                <td data-label="Invoice Status">{{$value->invoice_status}}</td>
                                <td data-label="Invoice Image">
                                    @if(!empty($value->vendor_invoice_image))
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="showpaymentaaa{{$key}}" value="{{url('/').'/assets/uploads/vdr_invoice_img/'.$value->vendor_invoice_image}}" onclick="showImage($(this).val())"><i class="fas fa-image"></i></button>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td data-label="Verified At">{{$value->verified_at}}</td>
                                <td data-label="Verified By">{{$value->verified_by}}</td>
                                <td data-label="Vdr. Pickup Date">{{($value->vendor_pickup_date)?date('d-M-y',strtotime($value->vendor_pickup_date)):"-"}}</td>
                                <td data-label="Vdr. Return Date">{{($value->vendor_return_date)?date('d-M-y',strtotime($value->vendor_return_date)):"-"}}</td>
                                <td data-label="Vendor Serial No.">{{$value->vendor_serial_no}}</td> 
                                <td data-label="Invoice Period">{{($value->period)?$value->period.' '.$value->rent_unit:""}}</td>
                                <td data-label="Inventory No">{{$value->product_inventory_no}}</td>
                                <td data-label="Payment State">{{$value->payment_state}}</td>
                                <td data-label="Payment Image">
                                    @if(!empty($value->payment_image))
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="showpaymentimg{{$key}}" value="{{url('/').'/assets/uploads/vdr_pay_img/'.$value->payment_image}}" onclick="showImage($(this).val())"><i class="fas fa-image"></i></button>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td data-label="Comment">
                                    <span data-toggle="tooltip" data-placement="left" title="{{$value->comment}}"><small>{{substr($value->comment,0,20)}} ...</small></span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{$vendorBilling->withPath(url()->full())->links('Custom.Pagination.pagination')}}
        </div>
    </div>
    {{-- Add Record Modal --}}
    <div id="addInvoiceModal" class="modal modal-fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span id="invoiceTitle">Invoice</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body container-fluid">
                    <form action="{{route('vendor-billing-crud')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="updateid" id="updateid">
                        <div class="row form-group">
                            <div class="col">
                                <label for="vendorid">Vendor</label>
                                <select name="vendorid" id="vendorid" class="form-control form-control-sm select selectpicker" data-live-search="true" data-size="5" title="Select Vendor" required>
                                    @forelse($vendors as $key=>$vendor)
                                        <option value="{{$vendor->id}}">{{$vendor->vendor_name}}</option>
                                    @empty
                                        <option value="na" disabled selected>No Vendors Found</option>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-6">
                                <label for="invoicedate">Vendor Invoice Date</label>
                                <input type="date" name="invoicedate" id="invoicedate" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-6">
                                <label for="invoiceno">Vendor Invoice No</label>
                                <input type="text" name="invoiceno" id="invoiceno" class="form-control form-control-sm" required>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-6">
                                <label for="vendor_pickup_date">Vendor Pickup Date</label>
                                <input type="date" name="vendor_pickup_date" id="vendor_pickup_date" class="form-control form-control-sm calculateperiod" required>
                            </div>
                            <div class="col-md-6">
                                <label for="vendor_returned_date">Vendor Returned Date</label>
                                <input type="date" name="vendor_returned_date" id="vendor_returned_date" class="form-control form-control-sm calculateperiod">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-6">
                                <label for="vendor_rent_unit">Vendor Billing Cycle</label>
                                <select name="vendor_rent_unit" id="vendor_rent_unit" class="select selectpicker form-control form-control-sm calculateperiod">
                                    <option value="1">Day</option>
                                    <option value="2">Week</option>
                                    <option value="3">Month</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="vendor_rent_period">Period</label>
                                <input type="text" name="vendor_rent_period" id="vendor_rent_period" class="form-control form-control-sm" readonly>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-6">
                                <label for="vdrserialno">Vendor Serial No</label>
                                <input type="text" name="vdrserialno" id="vdrserialno" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-6">
                                <label for="inventoryid">Product Inventory Id</label>
                                <input type="text" name="inventoryid" id="inventoryid" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col">
                                <label for="orderid">Order Id</label>
                                <select class="orderids form-control" name="orderid[]" id="orderid" multiple="multiple" data-size="5" style="width: 100%" >
                                    {{-- <option value="na" disabled>Enter atleast 3 digits</option> --}}
                                    {{-- @forelse($orderIds as $orderid)
                                        <option value="{{$orderid}}">{{$orderid}}</option>
                                    @empty
                                    @endforelse --}}
                                </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col">
                                <label for="vdrrate">Total Vendor Amount</label>
                                <input type="text" name="vdrrate" id="vdrrate" class="form-control form-control-sm" required>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col">
                                <label for="invoiceimg">Invoice Image</label>
                                <input type="file" name="invoiceimg" id="invoiceimg" accept=".pdf,.jpeg,.jpg,.png" required>
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
                                <label for="paymentimg">Payment Image</label>
                                <input type="file" name="paymentimg" id="paymentimg" accept=".pdf,.jpeg,.jpg,.png">
                                <center>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="showpaymentimg" name="showpaymentimg" onclick="showImage($(this).val())"><i class="fas fa-image"></i></button>
                                </center>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col">
                                <span id="commentdisplay"></span>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col">
                                <label for="comment">Comment</label>
                                <textarea name="comment" id="comment" class="form-control form-control-sm" rows="4"></textarea>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col text-center">
                                <button class="btn btn-sm btn-outline-success">Submit</button>
                                <button type="reset" class="btn btn-sm btn-outline-secondary" id="reset">Reset</button>
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
                    <div id="imageModalBody" data-rotation="0" class="rotate0">
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
        var orderIds = {{$orderIds}};
        $("#add_invoice").click(function(){
            $("#updateid").val(null);
            let vendorid = $("#vendorid").val();
            $("#reset").trigger('click');
            $("#orderid").val(null);
            $("#orderid").select2({
                theme: "classic",
            placeholder: 'Select Orders',
            allowClear: true
            });
            $("#vendorid").val(vendorid);
            $("#vendorid").selectpicker("refresh");
            $("#updateunverified").attr('checked',true);
            $("#labelunverified").addClass('active');
            $("#labelverified").removeClass('active');
            $("#updateverified").removeAttr('checked');
            

            $("#updateunpaid").attr('checked',true);
            $("#labelunpaid").addClass('active');
            $("#labelpartialpaid").removeClass('active');
            $("#updatepartialpaid").removeAttr('checked');
            $("#labelpaid").removeClass('active');
            $("#updatepaid").removeAttr('checked');

            $("#showpaymentimg").hide();
            $("#showinvoiceimg").hide();
            $("#invoiceimg").attr("required","true");
            $("#vendor_rent_unit").val('3');
            $("#vendor_rent_unit").selectpicker("refresh");
            $("#addInvoiceModal").modal('show');
            $("#commentdisplay").text(null);
        });

        $(".calculateperiod").change(function(){
            if($("#vendor_pickup_date").val() && $("#vendor_returned_date").val()){
                const date1 = new Date($("#vendor_pickup_date").val());
                const date2 = new Date($("#vendor_returned_date").val());
                const diffTime = Math.abs(date2 - date1);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
                let period = 0;

                if($("#vendor_rent_unit").val() == "1"){
                    period = diffDays;
                }else if($("#vendor_rent_unit").val() == "2"){
                    period = parseInt(parseInt(diffDays) / parseInt(7));
                    if((parseInt(diffDays) - parseInt(parseInt(7)*parseInt(period))) >0){
                        period++;
                    }
                }else if($("#vendor_rent_unit").val() == "3"){
                    period = parseInt(parseInt(diffDays) / parseInt(30));
                    if((parseInt(diffDays) - parseInt(parseInt(30)*parseInt(period))) >0){
                        period++;
                    }
                }else{
                    $("#vendor_rent_period").val("other");    
                    return false;
                }
                $("#vendor_rent_period").val(period);

            }else{
                return false;
            }
        });

        // $("#vendorid").change(function(){
        //     let vendorid = $(this).val();
        //     var dataString = ({_token:"{{ csrf_token() }}",vendorid:vendorid});
        //         $.ajax({
        //             type: "POST",
        //             url:"{{route('get-orderid')}}",
        //             data:dataString,
        //             cache:false,
        //             success: function (data)
        //             {
        //                 var options = "";
        //                 $("#orderid").empty();
        //                 for(let i = 0; i<data.length; i++){
        //                     options += '<option value="'+data[i]+'">'+data[i]+'</option>';
        //                 }
        //                 $("#orderid").append(options);
        //             },
        //             error: function(er){
        //                 console.log(er);
        //             }
        //         });
        // });

        $(".orderids").select2({
            theme: "classic",
            placeholder: 'Select Orders',
            data: orderIds,
            allowClear: true,
            query: function(q) {
                var pageSize,
                    results,
                    that = this;
                pageSize = 20; // or whatever pagesize
                results = [];
                if (q.term && q.term !== '') {
                    // HEADS UP; for the _.filter function i use underscore (actually lo-dash) here
                    results = _.filter(that.data, function(e) {
                    return e.text.toUpperCase().indexOf(q.term.toUpperCase()) >= 0;
                    });
                } else if (q.term === '') {
                    results = that.data;
                }
                q.callback({
                    results: results.slice((q.page - 1) * pageSize, q.page * pageSize),
                    more: results.length >= q.page * pageSize,
                });
            },
        });
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
            let vendor_rent_unit = ['Day','Week','Month'];
            $("#inventoryid").val(inventory.product_inventory_no);
            $("#invoiceno").val(inventory.vendor_invoice_no);
            $("#vdrrate").val(inventory.vendor_invoice_rate);
            $("#vdrserialno").val(inventory.vendor_serial_no);
            $("#invoiceimg").removeAttr("required");
            $("#invoicedate").val(inventory.vendor_invoice_date);
            $("#vendor_pickup_date").val(inventory.vendor_pickup_date);
            $("#vendor_returned_date").val(inventory.vendor_return_date);
            $("#vendor_rent_period").val(inventory.period);
            console.log(vendor_rent_unit.indexOf(inventory.rent_unit));
            if(vendor_rent_unit.indexOf(inventory.rent_unit)){
                $("#vendor_rent_unit").val(vendor_rent_unit.indexOf(inventory.rent_unit)+1);
            }
            $("#vendor_rent_unit").selectpicker("refresh");
            $("#commentdisplay").text(inventory.comment);
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
            if(inventory.payment_image != null && inventory.payment_image != ""){
                $("#showpaymentimg").show();
                $("#showpaymentimg").val("{{url('/').'/assets/uploads/vdr_pay_img/'}}"+inventory.payment_image);
            }else{
                $("#showpaymentimg").hide();
            }
            if(inventory.vendor_invoice_image != null && inventory.vendor_invoice_image != ""){
                $("#showinvoiceimg").show();
                $("#showinvoiceimg").val("{{url('/').'/assets/uploads/vdr_invoice_img/'}}"+inventory.vendor_invoice_image);
            }else{
                $("#showinvoiceimg").hide();
            }
            console.log(inventory.orderid);
            
            $("#orderid").val(inventory.orderid);
            $("#orderid").select2({
                theme: "classic",
            placeholder: 'Select Orders',
            allowClear: true
            });
            $("#vendorid").val(inventory.vendor_id);
            $("#vendorid").selectpicker("refresh");
            $("#updateid").val(inventory.id);
            $("#addInvoiceModal").modal("show");
        }
        function showImage(img){
            $("#showimgtag").attr('src',img);
            $("#showimageModal").modal("show");
        }
        /*----- reduce loading time tried to impliment live search with live fetch data from database on input after 3 characters...-----*/
        $(".select2-search__field").on('input',function(){
            var selectedIds = $("#orderid").val();
            // console.log(selectedIds);
            if($.trim($(".select2-search__field").val()).length >2){
                var dataString = ({_token:"{{ csrf_token() }}",rawid:$.trim($(".select2-search__field").val()),selectedid:selectedIds});
                $.ajax({
                    type: "POST",
                    url:"{{route('get-orderid')}}",
                    data:dataString,
                    cache:false,
                    success: function (data)
                    {
                        var options = "";
                        $("#orderid").empty();
                        for(let i = 0; i<data.length; i++){
                            if($.inArray(data[i],selectedIds) !== -1){
                                options += '<option value="'+data[i]+'" selected>'+data[i]+'</option>';
                            }else{
                                options += '<option value="'+data[i]+'">'+data[i]+'</option>';
                            }
                        }
                        $("#orderid").append(options);
                        $(".select2-search__field").trigger("input"); 
                    },
                    error: function(er){
                        console.log(er);
                    }
                });
            }

        });
    </script>
@endsection