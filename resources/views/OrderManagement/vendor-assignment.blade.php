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
    @if(session()->has('message_search'))
        <div class="alert alert-danger">
        {{ session()->get('message_search') }}
        </div>
    @endif 
    <div class="mt-2">
        <div class="card" id="filter_card">
            <div class="card-header border-primary" id="filter_card">
                <div class="row">
                    <div class="col text-primary" id="heading-filter" class="d-block">
                        <strong>Assign Vendor</strong>
                    </div>
                </div>
            </div>
            <div class="card-body border-primary" data-parent="#filter_card">
                <form action="{{route('order-generate')}}" method="POST">
                    @csrf
                    <h3>Lead Details</h3>
                    <input type="hidden" name="leadid" value="{{$details->id}}">
                    <hr>
                    <div class="row form-group">
                        <div class="col-md-4 col-12">
                            Customer Name: {{$details->customer_name}}
                        </div>
                        <div class="col-md-4 col-12">
                            Mobile No: {{$details->primary_contact_no}}
                        </div>
                        <div class="col-md-4 col-12">
                            Gender: {{$details->cust_gender}}
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-4 col-12">
                            Patient Name: {{$details->patient_name}}
                        </div>
                        <div class="col-md-4 col-12">
                            Age: {{$details->patient_age}}
                        </div>
                        <div class="col-md-4 col-12">
                            Gender: {{$details->patient_gender}}
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-1 col-12">
                            Address:
                        </div>
                        <div class="col-md-11 col-12">
                            <span>{{$details->address_line_1.', '.$details->address_line_2.', '.$details->landmark.', '.$details->area.', '.$details->city.', '.$details->pincode.', '.$details->state.', '.$details->country}}</span>
                        </div>
                    </div>
                    <hr>
                    <h4>Vendor Selection</h4>
                    <hr>
                    @foreach(json_decode($details->equipment_requirement) as $key=>$value)
                        {{-- Card  Here --}}
                        <div class="card form-group">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-auto">
                                        <span><b>Selection Type:</b> </span>
                                        <input type="radio" class="selectionType" name="details[{{$key}}][selectionType]" id="stAll{{$key}}" value="All" checked @if(json_decode($details->equipment_qty)[$key] == 1) readonly @endif data-id="{{$key}}" data-qty="{{json_decode($details->equipment_qty)[$key]}}"><label for="stAll{{$key}}">All</label>&emsp;
                                        <input type="radio" class="selectionType" name="details[{{$key}}][selectionType]" id="stIndividual{{$key}}" value="Individual" @if(json_decode($details->equipment_qty)[$key] == 1) readonly @endif data-id="{{$key}}" data-qty="{{json_decode($details->equipment_qty)[$key]}}"><label for="stIndividual{{$key}}" >Individual</label>
                                    </div>
                                    <div class="col-auto">
                                        <span><b>Billing Type:</b> </span>
                                        <span>{{json_decode($details->sale_rental)[$key]}}</span>
                                    </div>
                                    <div class="col-auto">
                                        <span><b>Equipment:</b> </span>
                                        <input type="hidden" name="details[{{$key}}][billingtype]"value="{{json_decode($details->sale_rental)[$key]}}">
                                        <input type="hidden" name="details[{{$key}}][productid]" id="productid{{$key}}" value="{{$value}}">
                                        <span>{{json_decode($details->equipment_names)[$key]}}</span>
                                    </div>
                                    <div class="col-auto">
                                        <span><b>Qty:</b> </span>
                                        <span>{{json_decode($details->equipment_qty)[$key]}}</span>
                                    </div>
                                    <div class="col-auto">
                                        <span><b>Rent/Sale:</b> </span>
                                        <span>{{json_decode($details->offered_rent_total)[$key]}}</span>
                                    </div>
                                    <div class="col-auto">
                                        <span><b>Deposit:</b> </span>
                                        <span>{{json_decode($details->deposite_total)[$key]}}</span>
                                    </div>
                                    <div class="col-auto">
                                        <span><b>Transport:</b> </span>
                                        <span>{{json_decode($details->transport)[$key]}}</span>
                                    </div>
                                </div>
                                <div class="inventoriesRecords" id="inventories-records{{$key}}" data-id="{{$key}}" data-product_id="{{$value}}">
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <select class="select selectpicker form-control" data-dropup-auto="false" data-size="5" width="fit" title="Select Vendor" name="details[{{$key}}][inventory][0][vendor]" onchange="fetchDetails(`war`,$(this).val(),0,{{$key}},{{json_decode($details->equipment_qty)[$key]}},`all`);" id="vendors0{{$key}}" data-live-search="true" width="100%" required="true">
                                                <option value="" disabled>No Vendors Found</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="hidden" name="details[{{$key}}][inventory][0][warehousetype]" id="group_name0{{$key}}" value="">
                                            <select class="select selectpicker form-control warehouse-select" data-dropup-auto="false" data-size="5" width="fit" title="Select Warehouse" name="details[{{$key}}][inventory][0][warehouse]" onchange="fetchDetails(`bra`,{{$value}},0,{{$key}},{{json_decode($details->equipment_qty)[$key]}},`all`);" id="warehouses0{{$key}}" data-id="0{{$key}}" data-live-search="true" width="100%" required="true">
                                                <option value="" disabled>No Warehouses Found</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <select class="select selectpicker form-control" data-dropup-auto="false" data-size="5" width="fit" title="Select Brand" name="details[{{$key}}][inventory][0][brand]" id="brands0{{$key}}" onchange="fetchDetails(`addBra`,{{$value}},0,{{$key}},{{json_decode($details->equipment_qty)[$key]}},`all`);" id="vendors0{{$key}}" data-live-search="true" width="100%" required="true">
                                                <option value="" disabled>No Brands Found</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="row form-group text-center">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-sm btn-outline-success" name="btntype" id="btnSubmit" value="submit">
                                Submit
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="addbrand" class="modal modal-fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span id="modal_title">Add Warehouse / Batch</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body container-fluid">
                    <div class="row form-group">
                        <div class="col">
                            <input type="hidden" id="hiddenProductId" name="hiddenProductId" value="">
                            <input type="hidden" id ="hiddenPositionId" name="hiddenPositionId" value="">
                            <input type="hidden" id ="hiddenIndex" name="hiddenIndex" value="">
                            <label for="awb_add_brand">Brand</label>
                            <input type="text" name="awb_add_brand" id="awb_add_brand" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col text-center">
                            <button type="submit" name="submitformbrand" id="submitformbrand" class="btn btn-sm btn-outline-success">Add</button>
                        </div>
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
        $(".warehouse-select").change(function(){
            var id = $(this).data("id");
            console.log(id);
            var s = $(this).find(":selected").closest("optgroup");
            if($(s).attr("label") == 'Virtual Warehouse')
            {
                $("#group_name"+id).val('Virtual Warehouse');
            }
            else if($(s).attr("label") == 'Vendor Warehouse')
            {
                $("#group_name"+id).val('Vendor Warehouse');
            }
            else if($(s).attr("label") == 'Customer Location')
            {
                $("#group_name"+id).val('Customer Location');
            }
            $("#group_name"+id).val($(s).attr("label"));            
        });

        $(document).ready(function(){
            let len = {{count(json_decode($details->equipment_requirement))}};
            for(let i =0; i<len; i++){
                fetchDetails('vdr',1,0,i,1,'all');
            }
        })
        $("#submitformbrand").click(function(){
                if($("#awb_add_brand").val() == null || $("#awb_add_brand").val() == ""){
                    $("#awb_add_brand").attr('style','border:dashed 2px red;');
                }
                else{
                    let dataString = ({brandname:""+$("#awb_add_brand").val(),productid:""+$("#hiddenProductId").val(),requesttype:"addnewbrand"});
                    let prodid = $("#hiddenProductId").val();
                    let posiid = $("#hiddenPositionId").val();
                    let index = $("#hiddenIndex").val();
                    $.ajax({
                        type: "GET",
                        url: "{{route('add-warehouse-brand')}}",
                        data: dataString,
                        cache:false,
                        success: function (data)
                        {
                            console.log(data);
                            let res = jQuery.parseJSON(data);
                            console.log(res["id"]);
                            fetchBrand(`bra`,prodid,posiid,index,`all`);
                            setTimeout( function()
                            {
                                $("#brands"+posiid+index).selectpicker("refresh");
                                $("#brands"+posiid+index).val(res["id"]);
                                $("#brands"+posiid+index).selectpicker("refresh");
                                
                            },1000);
                            $("#addbrand").modal("hide");
                        }
                    })
                }
            });
        $(".selectionType").click(function(){
            let id = $(this).data("id");
            let qty = $(this).data("qty");
            let selectionType = 'ind';
            let productid = $("#productid"+id).val();
            // console.log($('input[name="details['+id+'][selectionType]"]').val());
            $('input[name="details['+id+'][selectionType]"]').change(function(){
                let div = "";
                if($('#stAll'+id).is(':checked')){
                    let selectionType = 'all';
                    div += '<div class="row form-group">';
                        div += '<div class="col-md-4">';
                            div += '<select class="select selectpicker selectpicker'+id+' form-control" data-dropup-auto="false" data-size="5" width="fit" title="Select Vendor" name="details['+id+'][inventory][0][vendor]" id="vendors'+0+id+'" onchange="fetchDetails(`war`,$(this).val(),0,'+id+','+qty+',`'+selectionType+'`);" data-live-search="true" width="100%" required="true">';
                                div += '<option value="" disabled>No Vendors Found</option>';
                                // div += fetchDetails('vdr',$("#productid"+id));
                            div += '</select>';
                        div += '</div>';
                        div += '<div class="col-md-4">';
                            div += '<input type="hidden" name="details['+id+'][inventory][0][warehousetype]" id="group_name0'+id+'" value="">';
                            div += '<select class="select selectpicker selectpicker'+id+' warehouse'+id+' form-control warehouse-select" data-dropup-auto="false" data-id="0'+id+'" data-size="5" width="fit" title="Select Warehouse" name="details['+id+'][inventory][0][warehouse]" id="warehouses'+0+id+'" onchange="fetchDetails(`bra`,'+productid+',0,'+id+','+qty+',`'+selectionType+'`);" data-live-search="true" width="100%" required="true">';
                                div += '<option value="" disabled>No Warehouses Found</option>';
                            div += '</select>';
                        div += '</div>';
                        div += '<div class="col-md-4">';
                            div += '<select class="select selectpicker selectpicker'+id+' form-control" data-dropup-auto="false" data-size="5" width="fit" title="Select Brand" name="details['+id+'][inventory][0][brand]" id="brands'+0+id+'" onchange="fetchDetails(`addBra`,'+productid+',0,'+id+','+qty+',`'+selectionType+'`);" data-live-search="true" width="100%" required="true">';
                                div += '<option value="" disabled>No Brands Found</option>';
                            div += '</select>';
                        div += "</div>";
                    div += '</div>';
                }else{
                    let selectionType = 'ind';
                    for(var i = 0; i<qty; i++){
                        div += '<div class="row form-group">';
                            div += '<div class="col-md-4">';
                                div += '<select class="select selectpicker selectpicker'+id+' form-control" data-dropup-auto="false" data-size="5" width="fit" title="Select Vendor" name="details['+id+'][inventory]['+i+'][vendor]" id="vendors'+i+id+'" onchange="fetchDetails(`war`,$(this).val(),'+i+','+id+','+qty+',`'+selectionType+'`);" data-live-search="true" width="100%" required="true">';
                                    div += '<option value="" disabled>No Vendors Found</option>';
                                div += '</select>';
                            div += '</div>';
                            div += '<div class="col-md-4">';
                                div += '<input type="hidden" name="details['+id+'][inventory]['+i+'][warehousetype]" id="group_name'+i+id+'" value="">';
                                div += '<select class="select selectpicker selectpicker'+id+' warehouse'+id+' form-control warehouse-select" data-dropup-auto="false" data-size="5" width="fit" title="Select Warehouse" name="details['+id+'][inventory]['+i+'][warehouse]" id="warehouses'+i+id+'" data-id="'+i+id+'" onchange="fetchDetails(`bra`,'+productid+','+i+','+id+','+qty+',`'+selectionType+'`);" data-live-search="true" width="100%" required="true">';
                                    div += '<option value="" disabled>No Warehouses Found</option>';
                                div += '</select>';
                            div += '</div>';
                            div += '<div class="col-md-4">';
                                div += '<select class="select selectpicker selectpicker'+id+' form-control" data-dropup-auto="false" data-size="5" width="fit" title="Select Brand" name="details['+id+'][inventory]['+i+'][brand]" id="brands'+i+id+'" onchange="fetchDetails(`addBra`,'+productid+','+i+','+id+','+qty+',`'+selectionType+'`);" data-live-search="true" width="100%" required="true">';
                                    div += '<option value="" disabled>No Brands Found</option>';
                                div += '</select>';
                            div += "</div>";
                        div += '</div>';
                    }
                }
                $("#inventories-records"+id).empty();                
                $("#inventories-records"+id).append(div);
                $(".selectpicker"+id).selectpicker("refresh");
                fetchDetails('vdr',$("#productid"+id).val(),0,id,qty,selectionType);
            });
        });
        function fetchDetails(type,prodid,posi,id,qty,selectionType){
            console.log(type,prodid,id,qty,selectionType);
            let dataString = ({_token:"{{ csrf_token() }}",type:""+type,fieldid:""+prodid});
            $.ajax({
                type:"POST",
                url:"{{route('fetch-inventory-details')}}",
                data:dataString,
                cache:false,
                success:function(resp){
                    var option = "";
                    if(type == 'vdr')
                    {
                        if(resp.length != 0){
                            for(let i = 0; i< resp.length; i++){
                                option += "<option value='"+resp[i].id+"'>"+resp[i].registered_name+"</option>";
                            }
                        }else{
                            option += "<option value='NF' disabled>No Vendor Found</option>";
                        }                   
                        if(selectionType == 'all'){
                            $("#vendors"+0+id).empty();
                            $("#vendors"+0+id).append(option);
                            $("#vendors"+0+id).selectpicker("refresh");
                        }else{
                            for(let i = 0; i < qty; i++){
                                $("#vendors"+i+id).empty();
                                $("#vendors"+i+id).append(option);
                                $("#vendors"+i+id).selectpicker("refresh");
                            }
                        }
                    }
                    if(type == 'war')
                    {
                        let virtual_warehouse = resp['virtual_warehouse'];
                        let vendor_warehouse = resp['vendor_warehouse'];
                        if(vendor_warehouse.length != 0){
                            option += '<optgroup label="Virtual Warehouse">';
                            for(let i = 0; i< virtual_warehouse.length; i++){
                                option += "<option value='"+virtual_warehouse[i].id+"'>"+virtual_warehouse[i].wh_name+', '+virtual_warehouse[i].wh_area+', '+virtual_warehouse[i].wh_city+"</option>";
                            }
                            option += '</optgroup>';
                        }else{
                            option += '<optgroup label="Virtual Warehouse">';
                                option += "<option value='NF' disabled>No Warehouse Found</option>";
                            option += '</optgroup>';
                        }
                        if(vendor_warehouse.length != 0){
                            option += '<optgroup label="Vendor Warehouse">';
                            for(let i = 0; i< vendor_warehouse.length; i++){
                                option += "<option value='"+vendor_warehouse[i].id+"'>"+vendor_warehouse[i].wh_name+', '+vendor_warehouse[i].wh_area+', '+vendor_warehouse[i].wh_city+"</option>";
                            }
                            option += '</optgroup>';
                        }else{
                            option += '<optgroup label="Vendor Warehouse">';
                                option += "<option value='NF' disabled>No Warehouse Found</option>";
                            option += '</optgroup>';
                        }                   
                        $("#warehouses"+posi+id).empty();
                        $("#warehouses"+posi+id).append(option);
                        $("#warehouses"+posi+id).selectpicker("refresh");
                    }
                    if(type == 'bra')
                    {
                        
                        var s = $("#warehouses"+posi+id).find(":selected").closest("optgroup");
                        if($(s).attr("label") == 'Virtual Warehouse')
                        {
                            $("#group_name"+posi+id).val('Virtual Warehouse');
                        }
                        else if($(s).attr("label") == 'Vendor Warehouse')
                        {
                            $("#group_name"+posi+id).val('Vendor Warehouse');
                        }
                        else if($(s).attr("label") == 'Customer Location')
                        {
                            $("#group_name"+posi+id).val('Customer Location');
                        }
                        $("#group_name"+posi+id).val($(s).attr("label"));


                        option += "<option value='Add'>Add New</option>";
                        if(resp.length != 0){
                            for(let i = 0; i< resp.length; i++){
                                option += "<option value='"+resp[i].id+"'>"+resp[i].brand_name+"</option>";
                            }
                        }else{
                            option += "<option value='NF' disabled>No Brand Found</option>";
                        }                   
                        $("#brands"+posi+id).empty();
                        $("#brands"+posi+id).append(option);
                        $("#brands"+posi+id).selectpicker("refresh");
                    }
                    if(type == 'addBra'){
                        if($("#brands"+posi+id).val() == 'Add'){
                            $("#hiddenProductId").val(prodid);
                            $("#hiddenPositionId").val(posi);
                            $("#hiddenIndex").val(id);
                            $("#addbrand").modal("show");
                        }
                    }
                }
            });
            return true;
        }

        function fetchBrand(type,prodid,posi,id){
            let dataString = ({_token:"{{ csrf_token() }}",type:""+type,fieldid:""+prodid});
            $.ajax({
                type:"POST",
                url:"{{route('fetch-inventory-details')}}",
                data:dataString,
                cache:false,
                success:function(resp){
                    var option = "";
                    if(type == 'bra')
                    {
                        option += "<option value='Add'>Add New</option>";
                        if(resp.length != 0){
                            for(let i = 0; i< resp.length; i++){
                                option += "<option value='"+resp[i].id+"'>"+resp[i].brand_name+"</option>";
                            }
                        }else{
                            option += "<option value='NF' disabled>No Brand Found</option>";
                        }                   
                        $("#brands"+posi+id).empty();
                        $("#brands"+posi+id).append(option);
                        $("#brands"+posi+id).selectpicker("refresh");
                    }
                    if(type == 'addBra'){
                        $("#hiddenProductId").val(prodid);
                        $("#hiddenPositionId").val(posi);
                        $("#hiddenIndex").val(id);
                        $("#addbrand").modal("show");
                    }
                }
            });
            return true;
        }
    </script>
@endsection