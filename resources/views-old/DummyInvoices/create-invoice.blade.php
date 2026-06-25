@extends('header_and_sidebar')

@section('style')

@endsection

@section('content')

    @if(session()->has('message'))
        <div class="alert alert-success">
            {{ session()->get('message') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger">
            {{ session()->get('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <div class="card border-primary my-3">
        <div class="card-header text-white bg-primary">
            <h5>Generate Invoice</h5>
        </div>
        <div class="card-body">
            <form action="{{route('generate-dummy-invoice')}}" method="POST">
                @csrf
                <div class="row form-group">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="inv_date">Date</label>
                            </div>
                            <div class="col-md-8">
                                <input type="date" class="form-control form-control-sm" name="inv_date" id="inv_date" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="inv_no">Invoice No</label>
                            </div>
                            <div class="col-md-8">
                                <input type="number" class="form-control form-control-sm" name="inv_no" id="inv_no" placeholder="Last Series" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-6">
                        <div class="card border-primary">
                            <div class="card-header text-white bg-primary">
                                {{-- <h6>Consignee Details</h6> --}}
                                <strong>Consignee Details</strong>
                            </div>
                            <div class="card-body text-right">
                                <div class="row form-group">
                                    <div class="col-md-4">
                                        <label for="inv_con_cust_name">Name of Consignee</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control form-control-sm" name="inv_con_cust_name" id="inv_con_cust_name" required>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-4">
                                        <label for="inv_con_addr_line_1">Line 1</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control form-control-sm" name="inv_con_addr_line_1" id="inv_con_addr_line_1" placeholder="Flat No., Floor, Building Name" required>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-4">
                                        <label for="inv_con_addr_line_2">Line 2</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control form-control-sm" name="inv_con_addr_line_2" id="inv_con_addr_line_2" placeholder="Road, Local Area">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-4">
                                        <label for="inv_con_landmark">Landmark</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control form-control-sm" name="inv_con_landmark" id="inv_con_landmark" placeholder="Landmark eg. opp. xyz bldg" required>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-4">
                                        <label for="inv_con_area">Area</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control form-control-sm" name="inv_con_area" id="inv_con_area" placeholder="Area" required>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-4">
                                        <label for="inv_con_city">City</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control form-control-sm" name="inv_con_city" id="inv_con_city" placeholder="City" required>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-4">
                                        <label for="inv_con_pincode">Pincode</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="number" class="form-control form-control-sm" name="inv_con_pincode" id="inv_con_pincode" placeholder="Pincode" required>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-4">
                                        <label for="inv_con_state">State</label>
                                    </div>
                                    <div class="col-md-8">
                                        <select class="selectpicker form-control form-control-sm" data-size="5" data-live-search="true" title="Select State" name="inv_con_state" id="inv_con_state" required>
                                            @foreach($states as $key=>$state)
                                                <option value="{{$state->name}}">{{$state->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-primary">
                            <div class="card-header text-white bg-primary">
                                {{-- <h5>Buyer Details</h5> --}}
                                <strong>Buyer Details 
                                </strong>
                                <small>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="sam_as_con" value="same">
                                        <label class="form-check-label" for="sam_as_con">Same as Consignee Details</label>
                                    </div>
                                </small>
                            </div>
                            <div class="card-body text-right">
                                <div class="row form-group">
                                    <div class="col-md-4">
                                        <label for="inv_buy_cust_name">Name of Buyer</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control form-control-sm" name="inv_buy_cust_name" id="inv_buy_cust_name" required>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-4">
                                        <label for="inv_buy_addr_line_1">Line 1</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control form-control-sm" name="inv_buy_addr_line_1" id="inv_buy_addr_line_1" placeholder="Flat No., Floor, Building Name" required>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-4">
                                        <label for="inv_buy_addr_line_2">Line 2</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control form-control-sm" name="inv_buy_addr_line_2" id="inv_buy_addr_line_2" placeholder="Road, Local Area">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-4">
                                        <label for="inv_buy_landmark">Landmark</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control form-control-sm" name="inv_buy_landmark" id="inv_buy_landmark" placeholder="Landmark eg. opp. xyz bldg" required>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-4">
                                        <label for="inv_buy_area">Area</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control form-control-sm" name="inv_buy_area" id="inv_buy_area" placeholder="Area" required>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-4">
                                        <label for="inv_buy_city">City</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control form-control-sm" name="inv_buy_city" id="inv_buy_city" placeholder="City" required>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-4">
                                        <label for="inv_buy_pincode">Pincode</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="number" class="form-control form-control-sm" name="inv_buy_pincode" id="inv_buy_pincode" placeholder="Pincode" required>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-4">
                                        <label for="inv_buy_state">State</label>
                                    </div>
                                    <div class="col-md-8">
                                        <select class="selectpicker form-control form-control-sm" data-size="5" data-live-search="true" title="Select State" name="inv_buy_state" id="inv_buy_state" required>
                                            @foreach($states as $key=>$state)
                                                <option value="{{$state->name}}">{{$state->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-12">
                        <div class="card border-primary">
                            <div class="card-header text-white bg-primary">
                                <strong>Corporate Details</strong>
                            </div>
                            <div class="card-body">
                                <div class="row form-group">
                                    <div class="col-md-4">
                                        <div class="btn-group btn-group-toggle btn-block btn-sm" data-toggle="buttons">
                                            <label for="inv_cust_type_ind" class="btn btn-outline-primary btn-sm">
                                                <input type="radio" name="inv_cust_type" id="inv_cust_type_ind" value="Individual" required> Individual
                                            </label>
                                            <label for="inv_cust_type_corp" class="btn btn-outline-primary btn-sm">
                                                <input type="radio" name="inv_cust_type" id="inv_cust_type_corp" value="Corporate" required> Corporate
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="btn-group btn-group-toggle btn-block btn-sm" data-toggle="buttons">
                                            <label for="inv_reg_type_reg" class="btn btn-outline-primary btn-sm">
                                                <input type="radio" name="inv_reg_type" id="inv_reg_type_reg" value="Registered" required> Registered
                                            </label>
                                            <label for="inv_reg_type_nreg" class="btn btn-outline-primary btn-sm">
                                                <input type="radio" name="inv_reg_type" id="inv_reg_type_nreg" value="Non Registered" required> Non Registered
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        {{-- <input type="text" name="inv_gstin" id="inv_gstin" class="form-control form-control-sm"> --}}
                                        <div class="input-group mb-3 gstin_div" style="display:none">
                                            <div class="input-group-prepend">
                                              <span class="input-group-text" id="inv_gstin_span">GSTIN</span>
                                            </div>
                                            <input type="text" class="form-control" placeholder="GSTIN" name="inv_gstin" id="inv_gstin"  aria-label="GSTIN" aria-describedby="inv_gstin_span">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-12">
                        <div class="card border-primary">
                            <div class="card-header text-white bg-primary">
                                {{-- <h5>Equipments</h5> --}}
                                <strong>Equipments</strong>
                            </div>
                            <div class="card-body">
                                <div class="table table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                {{-- <th>Sr.no.</th> --}}
                                                <th>Equipment</th>
                                                <th class="text-nowrap">Type</th>
                                                <th>Qty</th>
                                                <th>Rate</th>
                                                <th>Deposit</th>
                                                <th>Transport</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="equip_tbody">
                                            <tr id="1">
                                                {{-- <td>1</td> --}}
                                                <td>
                                                    <select class="select selectpicker border form-control form-control-sm" data-size="5" data-width="fit" data-live-search="true" title="Select Equip" name="inv_equip[]" id="inv_equip_1" required>
                                                        @foreach($equipments as $key=>$equipment)
                                                            <option value="{{$equipment->id}}">{{$equipment->product_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="text-nowrap">
                                                    <div class="btn-group btn-group-toggle btn-block btn-sm" data-toggle="buttons">
                                                        <label for="inv_equip_type_sale_1" class="btn btn-outline-primary btn-sm">
                                                            <input type="radio" name="inv_equip_type[]1" id="inv_equip_type_sale_1" value="Sale" required> Sale
                                                        </label>
                                                        <label for="inv_equip_type_rental_1" class="btn btn-outline-primary btn-sm">
                                                            <input type="radio" name="inv_equip_type[]1" id="inv_equip_type_rental_1" value="Rental" required> Rental
                                                        </label>
                                                    </div>
                                                    {{-- <input type="radio" name="inv_equip_type[]1" id="inv_equip_type_sale_1" value="Sale" required>
                                                    <label for="inv_equip_type_sale_1" >Sale</label>
                                                    &emsp;
                                                    <input type="radio" name="inv_equip_type[]1" id="inv_equip_type_rental_1" value="Rental" required>
                                                    <label for="inv_equip_type_rental_1" >Rental</label> --}}
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm" name="inv_qty[]" id="inv_qty_1" value="1" required>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm" name="inv_rent[]" id="inv_rent_1" value="0" required>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm" name="inv_deposit[]" id="inv_deposit_1" value="0" required>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm" name="inv_transport[]" id="inv_transport_1" value="0" required>
                                                </td>
                                                <td>
                                                    -
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-12 text-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary" name="add_product" id="add_product" value="2">Add Equipment</button>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-4 text-center">
                                        <label for="dummylabourcharges">Floor</label>
                                        <input type="number" name="dummyfloor" id="dummyfloor" class="form-control form-control-sm">
                                        <label for="dummylabourcharges">Labour Charges</label>
                                        <input type="number" name="dummylabourcharges" id="dummylabourcharges" class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-sm btn-outline-success" name="submit" id="submit" value="Submit">Generate Invoice</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('.table-responsive').on('show.bs.dropdown', function () {
            $('.table-responsive').css( "overflow", "inherit" );
        });

        $('.table-responsive').on('hide.bs.dropdown', function () {
            $('.table-responsive').css( "overflow", "auto" );
        });

        $("#add_product").click(function(){
            let id = $(this).val();
            let row = '<tr id="'+id+'">';
                // row += '<td>'+id+'</td>';
                row += '<td>';
                    row += '<select class="select selectpicker border form-control form-control-sm" data-size="5" data-width="fit" data-live-search="true" title="Select Equip" name="inv_equip[]" id="inv_equip_'+id+'" required>';
                        row += '@foreach($equipments as $key=>$equipment)';
                            row += '<option value="{{$equipment->id}}">{{$equipment->product_name}}</option>';
                        row += '@endforeach';
                    row += '</select>';
                row += '</td>';
                row += '<td>';
                    row += '<div class="btn-group btn-group-toggle btn-block btn-sm" data-toggle="buttons">';
                        row += '<label for="inv_equip_type_sale_'+id+'" class="btn btn-outline-primary btn-sm">';
                            row += '<input type="radio" name="inv_equip_type[]'+id+'" id="inv_equip_type_sale_'+id+'" value="Sale" required> Sale';
                        row += '</label>';
                        row += '<label for="inv_equip_type_rental_'+id+'" class="btn btn-outline-primary btn-sm">';
                            row += '<input type="radio" name="inv_equip_type[]'+id+'" id="inv_equip_type_rental_'+id+'" value="Rental" required> Rental';
                        row += '</label>';
                    row += '</div>';
                    // row += '<input type="radio" name="inv_equip_type[]2" id="inv_equip_type_sale_'+id+'" value="Sale" required>';
                    // row += '<label for="inv_equip_type_sale_'+id+'" >Sale</label>';
                    // row += '&emsp;'
                    // row += '<input type="radio" name="inv_equip_type[]2" id="inv_equip_type_rental_'+id+'" value="Rental" required>';
                    // row += '<label for="inv_equip_type_rental_'+id+'" >Rental</label>';
                row += '</td>';
                row += '<td>';
                    row += '<input type="number" class="form-control form-control-sm" name="inv_qty[]" id="inv_qty_'+id+'" value="1" required>';
                row += '</td>';
                row += '<td>';
                    row += '<input type="number" class="form-control form-control-sm" name="inv_rent[]" id="inv_rent_'+id+'" value="0" required>';
                row += '</td>';
                row += '<td>';
                    row += '<input type="number" class="form-control form-control-sm" name="inv_deposit[]" id="inv_deposit_'+id+'" value="0" required>';
                row += '</td>';
                row += '<td>';
                    row += '<input type="number" class="form-control form-control-sm" name="inv_transport[]" id="inv_transport_'+id+'" value="0" required>';
                row += '</td>';
                row += '<td>';
                    row += '<button type="button" class="btn btn-sm btn-outline-danger remove_equip" id="inv_remove_equip_'+id+'" onclick="remove_equip(this.id)" data-id="'+id+'"><i class="fas fa-trash-alt"></i></button>';
                row += '</td>';
            row += '</tr>';
            $("#equip_tbody").append(row);
            $("#inv_equip_"+id).selectpicker('refresh');
            $(this).val(parseInt(id) + parseInt(1));
        });
        function remove_equip(id)
        {
            $("#"+$("#"+id).data('id')).remove();
        }
        $("#sam_as_con").change(function(){
            if($("#sam_as_con").is(':checked'))
            {
                $("#inv_buy_cust_name").val($("#inv_con_cust_name").val());
                $("#inv_buy_addr_line_1").val($("#inv_con_addr_line_1").val());
                $("#inv_buy_addr_line_2").val($("#inv_con_addr_line_2").val());
                $("#inv_buy_landmark").val($("#inv_con_landmark").val());
                $("#inv_buy_area").val($("#inv_con_area").val());
                $("#inv_buy_city").val($("#inv_con_city").val());
                $("#inv_buy_pincode").val($("#inv_con_pincode").val());
                $("#inv_buy_state").val($("#inv_con_state").val());
                $("#inv_buy_state").selectpicker('refresh');
            }
            else
            {
                $("#inv_buy_cust_name").val(null);
                $("#inv_buy_addr_line_1").val(null);
                $("#inv_buy_addr_line_2").val(null);
                $("#inv_buy_landmark").val(null);
                $("#inv_buy_area").val(null);
                $("#inv_buy_city").val(null);
                $("#inv_buy_pincode").val(null);
                $("#inv_buy_state").val(null);
                $("#inv_buy_state").selectpicker('refresh');
            }
        });

        $("input[name='inv_reg_type']").change(function() {
            console.log($("input[name='inv_reg_type']:checked").val());
            if($("input[name='inv_reg_type']:checked").val() == 'Registered')
            {
                $(".gstin_div").show();
                $("#inv_gstin").attr('required',true);
            }
            else
            {
                $(".gstin_div").hide();
                $("#inv_gstin").attr('required',false);
            }
        });
    </script>
@endsection