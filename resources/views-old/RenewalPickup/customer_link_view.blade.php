<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Customer Link View</title>
    <link href="{{url('/')}}/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
         href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
         rel="stylesheet">
     <!-- Custom styles for this page -->
     <link href="<?php echo url('/');?>/assets/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
     
     {{-- stylesheets --}}
     {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.2/css/bootstrap.min.css"/> --}}
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.min.css"/>
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
     
     <!-- Custom styles for this template-->
     <link href="{{url('/')}}/assets/css/sb-admin-2.min.css" rel="stylesheet">   
     
</head>
    <body>
        <div class="row">
            <div class="col-md-12 text-center">
                <img src="{{url('/')}}/assets/images/logo_small.png" alt="">
            </div>
        </div>
        <div class="card">
            <div class="card-header text-center" style="background-color: #337ab7; color: white;">
                <span><b>Product details</b></span>
            </div>
            <div class="card-body">
                <form action="{{url('/')}}/customer_renewal_or_pickup_link/{{$link}}" method="post">
                    @csrf
                    <div class="row">
                        @foreach($get_renewal_data as $key => $data)
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-header">
                                        <strong>{{$data->product_name}}</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-auto">
                                                Pickup Date : <strong>{{date('d-m-Y',strtotime($data->order_pickup_date))}}</strong><br>
                                            </div>
                                            <div class="col-auto">
                                                Product Rent : <strong>{{$data->product_rent}}</strong><br>
                                            </div>
                                            <div class="col-auto">
                                                Due Month : <strong>{{$data->due_month}}</strong><br>
                                            </div>
                                            <div class="col-auto">
                                                Total Rent : <strong>{{$data->total_rent}}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="renewal_table_id[]" value ="{{$data->id}}">
                                    <input type="hidden" name="order_details_id[]" value ="{{$data->order_details_id}}">
    
                                    <div class="accordion btn-group-vertical btn-group-toggle btn-group-flush" id="accordionCollapse{{$key}}">
                                        <label class="btn btn-outline-primary btn-group-item btn-sm btn_status" id="lbl_Continue{{$key}}" data-id="{{$key}}" data-name="continue">
                                            <input type="radio" class="radio_btn{{$key}}" name="product_status[{{$key}}]" data-id="{{$key}}" id="radio_btn_continue{{$key}}"
                                                autocomplete="off" value="0" required 
                                                data-toggle="collapse" data-target="#collapseContinue{{$key}}" aria-expanded="true" aria-controls="collapseContinue{{$key}}">Renew
                                        </label>
                                        <div id="collapseContinue{{$key}}" class="collapse" aria-labelledby="lbl_Continue{{$key}}" data-parent="#accordionCollapse{{$key}}">
                                            <div class="card-body">
                                                Payment Mode :  
                                                <div class="btn-group btn-group-toggle btn-responsive" id="payment_btn{{$key}}" data-toggle="buttons" >
                                                    <label class="btn btn-outline-primary">
                                                        <input type="radio" name="payment_mode[{{$key}}]" id="cash_radio{{$key}}" autocomplete="off" value="0" required>Cash
                                                    </label>
                                                    <label class="btn btn-outline-primary">
                                                        <input type="radio" name="payment_mode[{{$key}}]" id="online_radio{{$key}}" autocomplete="off" value="1" required>Online
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <label class="btn btn-outline-primary btn-group-item btn-sm btn_status" id="lbl_Pickup{{$key}}" data-id="{{$key}}" data-name="pickup">
                                            <input type="radio" class="radio_btn{{$key}}" name="product_status[{{$key}}]" data-id="{{$key}}" id="radio_btn_pickup{{$key}}"
                                                autocomplete="off" value="1" required
                                                data-toggle="collapse" data-target="#collapsePickup{{$key}}" aria-expanded="true" aria-controls="collapsePickup{{$key}}">Pickup
                                        </label>
                                        <div id="collapsePickup{{$key}}" class="collapse" aria-labelledby="lbl_Pickup{{$key}}" data-parent="#accordionCollapse{{$key}}">
                                            <div class="card-body">
                                                <div class="row">
                                                        Pickup Date: 
                                                        <input type="date" class="form-control pickup_restrict" name="cust_pickup_date[{{$key}}]" id="date_cust_pickup_date{{$key}}" 
                                                            min="{{$data->order_pickup_date}}" value="{{$data->order_pickup_date}}" data-id="{{$key}}" required>
                                                        <input type="hidden" name="hidden_prod_date" id="hidden_prod_date{{$key}}" value="{{$data->order_pickup_date}}">
                                                        <input type="hidden" name="hidden_prod_max_date" id="hidden_prod_max_date{{$key}}" value="{{date('Y-m-d',strtotime($data->order_pickup_date.'+ 2 days'))}}">
                                                    {{-- <div class="col-md-5">
                                                        PickupTime: 
                                                        <input type="time" class="form-control pickup_time" name="cust_pickup_time[{{$key}}]" id="cust_pickup_time{{$key}}" placeholder="Pickup Time" required>
                                                    </div> --}}
                                                </div>
                                                <div class="row" style="display: none" id="div_pickup_restrict_message{{$key}}">
                                                    <small class="text-danger" id="pickup_restrict_message{{$key}}" >Only 2 days grace period for pickup is allowed. If you want more time then please call customer care at <a href="tel:8792740050">8792740050</a></small>
                                                </div>
                                            </div>
                                        </div>
                                        <label class="btn btn-outline-primary btn-group-item btn-sm btn_status" id="lbl_Undecided{{$key}}" data-id="{{$key}}" data-name="undecided">
                                            <input type="radio" class="radio_btn{{$key}}" name="product_status[{{$key}}]" data-id="{{$key}}" id="radio_btn_undecided{{$key}}"
                                                autocomplete="off"
                                                data-toggle="collapse" data-target="#collapseUndecided{{$key}}" aria-expanded="true" aria-controls="collapseUndecided{{$key}}"
                                                value="2" required>Undecided
                                        </label>
                                    </div>
                                </div>
                                <br>
                            </div>
                        @endforeach
                    </div>
                    <div class="row container-fluid justify-content-center">
                        <div class="col-md-3">
                            <input type="submit" class="btn btn-outline-primary btn-block" value="Submit">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <script src="{{url('/')}}/assets/vendor/jquery/jquery.min.js"></script>
        <script src="{{url('/')}}/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="{{url('/')}}/assets/vendor/jquery/jquery.cookie.js" type="text/javascript"></script>

        <!-- Core plugin JavaScript-->
        <script src="{{url('/')}}/assets/vendor/jquery-easing/jquery.easing.min.js"></script>

        <!-- Custom scripts for all pages-->
        <script src="{{url('/')}}/assets/js/sb-admin-2.min.js"></script>

        <!-- Page level plugins -->
        <script src="{{url('/')}}/assets/vendor/chart.js/Chart.min.js"></script>

        <!-- Page level custom scripts -->
        <script src="{{url('/')}}/assets/js/demo/chart-area-demo.js"></script>
        <script src="{{url('/')}}/assets/js/demo/chart-pie-demo.js"></script>

        <!-- Page level plugins -->
        <script src="<?php echo url('/');?>/assets/vendor/datatables/jquery.dataTables.min.js"></script>
        <script src="<?php echo url('/');?>/assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>

        {{-- Scripts --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="{{url('/')}}/assets/js/jquery.table2excel.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js" ></script>
        <script>
            $(document).ready(function() {
                $('#select_equipment').select2({
                    width: 'resolve',
                    placeholder: 'Select product',
                    allowClear: true
                });
            });
            $(document).ready(function() {
                $('#select_additional_equipment').select2({
                    width: 'resolve',
                    placeholder: 'Select products',
                    allowClear: true
                });
                // $('.pickup_time').timepicker();
            });

            $('.btn_status').on('change', function(){
                id=$(this).data('id');
                name = $(this).data('name');
                let hid_date = $('#hidden_prod_date'+id).val();
                if(name=="continue"){
                    $('#lbl_Continue'+id).addClass('active');
                    $('#lbl_Pickup'+id).removeClass('active');
                    $('#lbl_Undecided'+id).removeClass('active');

                   // check radio buttons by jquery
                    $('#radio_btn_continue'+id).attr("checked", true);
                    $('#radio_btn_pickup'+id).attr("checked",false);
                    $('#radio_btn_undecided'+id).attr("checked",false);

                    $('#date_cust_pickup_date'+id).removeAttr('required');
                    $('#cust_pickup_time'+id).val('');
                    $('#cust_pickup_time'+id).removeAttr('required');
                    //display
                    $('#cash_radio'+id).val(0);
                    $('#cash_radio'+id).attr('required',true);
                    $('#online_radio'+id).val(1);
                    $('#online_radio'+id).attr('required',true);
                }
                else if(name=="pickup"){
                    $('#lbl_Pickup'+id).addClass('active');
                    $('#lbl_Continue'+id).removeClass('active');
                    $('#lbl_Undecided'+id).removeClass('active');

                    $('#radio_btn_pickup'+id).attr("checked", true);
                    $('#radio_btn_continue'+id).attr("checked",false);
                    $('#radio_btn_undecided'+id).attr("checked",false);

                    //hide
                    $('#cash_radio'+id).removeAttr('required');
                    $('#cash_radio'+id).val('');
                    $('#cash_radio'+id).attr('checked',true);
                    $('#online_radio'+id).removeAttr('required');
                    $('#online_radio'+id).val('');
                    //show
                    $('#date_cust_pickup_date'+id).val(hid_date);
                    $('#date_cust_pickup_date'+id).attr('required',true);
                    $('#cust_pickup_time'+id).val('');
                    $('#cust_pickup_time'+id).attr('required',true);
                }
                else if(name="undecided"){
                    $('#lbl_Undecided'+id).addClass('active');
                    $('#lbl_Pickup'+id).removeClass('active');
                    $('#lbl_Continue'+id).removeClass('active');

                    //collapse other tabs
                    $('#collapseContinue'+id).removeClass('show');
                    $('#collapsePickup'+id).removeClass('show');

                    $('#radio_btn_undecided'+id).attr("checked",true);
                    $('#radio_btn_pickup'+id).attr("checked",false);
                    $('#radio_btn_continue'+id).attr("checked",false);

                    //hide
                    $('#date_cust_pickup_date'+id).removeAttr('required');
                    $('#cust_pickup_time'+id).removeAttr('required');
                    $('#cash_radio'+id).removeAttr('required');
                    $('#cash_radio'+id).val('');
                    $('#cash_radio'+id).attr('checked',true);
                    $('#online_radio'+id).removeAttr('required');
                    $('#online_radio'+id).val('');
                }
            });
            
            $('.pickup_restrict').on('change',function(){
                let id = $(this).data('id');
                let selected_date =$(this).val();
                let get_min_date = $('#hidden_prod_date'+id).val();
                let get_max_date = $('#hidden_prod_max_date'+id).val();
                if(selected_date>get_max_date){
                    $('#div_pickup_restrict_message'+id).css('display', 'block');
                    $(this).val(get_min_date);
                }
                else{
                    $('#div_pickup_restrict_message'+id).css('display', 'none');
                }
            });
            
        </script>
    </body>
</html>