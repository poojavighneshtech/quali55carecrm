@extends('header_and_sidebar')
<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        
        <title>Raise Complaint</title>
        {{-- <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script> --}}
        <link rel="stylesheet" href="{{url('/')}}/assets/dist/toast.min.css ">
        @section('styles')
        
            <style>
                .card-header {
                    cursor: pointer;
                }
                img {
                    max-width: 100%;
                    max-height: 50%;
                    padding-top:10px;
                }
            </style>
        @endsection
    </head>

<body id="page-top">	
        <!-- Page Wrapper -->
        
    @section('content')
        @if(session()->has('message_delete'))
            <div class="alert alert-danger">
                {{ session()->get('message_delete') }}
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
        <div class="alert alert-danger fade" id="error_pop">
            <span id="error_text"></span> Not found
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"  style="background-color: #4a6fdc; color: white;" >
                        <center>Raise Complaint</center>
                    </div> 
                    <form action="{{url('/')}}/raise_complaint" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2">
                                    <label for="customer" class="">Search Customer</label> 
                                </div>
                                <div class="col-md-5">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="radio_search_customer" id="by_customer_val" value="customer_name_no" checked>
                                        <label class="form-check-label" for="by_customer_val">Customner name / Contact no</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="radio_search_customer" id="by_complaint_id" value="complaint_id">
                                        <label class="form-check-label" for="by_complaint_id">Complaint ID</label>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="form-row row" id="">
                                <div class="col-md-5">
                                    <input type="text" class="form-control" placeholder="Search Customer.." name="text_search_customer" id="text_search_customer">
                                </div>
                                <div class="col-md-2">
                                    {{-- <input type="button" class="form-control btn btn-outline-primary" name="btn_search_customer" id="btn_search_customer" value="text_customer"> --}}
                                    <button type="button" class="form-control btn btn-outline-primary" name="btn_search_customer" id="btn_search_customer" value="btn_customer">Search</button>
                                    {{-- <button type ="button" class="toast-btn btn btn-primary">Toast</button> --}}
                                </div>
                                <div class="col-md-5" id="div_select_complaint_ids" style="display: none">
                                    <select class="form-control selectpicker" name="complaint_ids" id="select_complaint_ids" title="Complaint ids">
                                        <option value="no found" disabled>No Complaint</option>
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class="row" id="customer_div" style="display: none">
                                <div class="col-md-12">
                                    <div id="accordion" class="accordion_div">
                                        <div class="card">
                                            <div class="card-header" id="headingOne" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                Customer Details
                                            </div>
                                            
                                            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-sm-4">
                                                                    <p><strong>Customer Name :</strong></p>
                                                                </div>
                                                                <div class="col-md-8 text-left">
                                                                    <p id="p_customer_name"></p>
                                                                    <input type="hidden" name="customer_id" id="customer_id">
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-sm-4">
                                                                    <p><strong>Contact no :</strong></p>
                                                                </div>
                                                                <div class="col-md-8 text-left">
                                                                    <p id="p_customer_primary_no"></p>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-sm-4">
                                                                    <p><strong>Email :</strong></p>
                                                                </div>
                                                                <div class="col-md-8 text-left">
                                                                    <p id="p_customer_email"></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-sm-3">
                                                                    <p><strong>Location :</strong></p>
                                                                </div>
                                                                <div class="col-sm-9 text-left">
                                                                    <p id="p_customer_location"></p>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-sm-3">
                                                                    <p><strong>Address :</strong></p>
                                                                </div>
                                                                <div class="col-md-9 text-left">
                                                                    <p id="p_customer_address"></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="card">
                                            <div class="card-header" id="headingTwo" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                <span id="card_header_text">Customer Products</span>
                                            </div>
                                            <div id="collapseTwo" class="collapse show" aria-labelledby="headingTwo" data-parent="#accordion">
                                                <div class="card-body">
                                                    <div class="row" id="div_selectpicker">
                                                        <div class="col-sm-2 mb-2">
                                                            <p><strong>Products :</strong></p>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <select class="form-control" id="customer_products" name="customer_products[]" multiple="multiple" style="width:100%" required>
                                                                {{-- @foreach ($products as $product) 
                                                                    <option value="{{$product['id']}}">{{$product['product_name']}}</option>
                                                                @endforeach --}}
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            {{-- <button type="button" id="submit_products" class="btn btn-primary mb-2">Submit</button> --}}
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <div class="row" id="product_div" style="display: none">
                                                        <div class="col-md-12">
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    Product Details
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        <div class="table" id="div_table">
                                                                        </div>
                                                                    </div>
                                                                    <br>
                                                                    <div class="row">
                                                                        <textarea class="form-control" name="remarks" id="remarks" cols="20" rows="5" required></textarea>
                                                                    </div>
                                                                    <br>
                                                                    {{-- <div class="row">
                                                                        <div class="custom-file" id="div_complaint_img_file">
                                                                            <input type="file" class="custom-file-input" name="complaint_img[0][]" id="complaint_multi_img0" accept="image/*" multiple>
                                                                            <label class="custom-file-label" for="complaint_multi_img">Choose file</label>
                                                                        </div>
                                                                        
                                                                    </div> --}}
                                                                    <div class="container" id="div_add_image">
                                                                        <div class="row">
                                                                            <div class="form-group col-md-3 text-center" id="image_label">
                                                                                <label for="complaint_img_id" class="form-label"><b>Add Image</b></label>
                                                                            </div>
                                                                            <div class="col-md-9" >  
                                                                                <div>
                                                                                    <input type="file" id="complaint_img_id" class="" name="complaint_img[]" accept="image/png, image/jpeg, image/jpg," >
                                                                                    <input type="button" class="btn btn-primary add_more" id="add_more" name="add_more" value="+ Add More" >
                                                                                </div>                                    
                                                                            </div>   
                                                                        </div>
                                                                    </div>  
                                                                    <br>
                                                                    <div class="row" id="div_submit">
                                                                        <div class="col-md-3"></div>
                                                                        <div class="col-md-6 text-center">
                                                                            <input type="submit" class="form-control btn btn-success" value="Submit">
                                                                        </div>
                                                                        <div class="col-md-3"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                
            </div>
        </div>

       
    @endsection
    @section('script')

    {{-- <script>
        const TYPES = ['info', 'warning', 'success', 'error'],
            TITLES = {
                'info': 'Notice!',
                'success': 'Awesome!',
                'warning': 'Watch Out!',
                'error': 'Doh!'
            },
            CONTENT = {
                'info': 'Hello, world! This is a toast message.',
                'success': 'The action has been completed.',
                'warning': 'It\'s all about to go wrong',
                'error': 'It all went wrong.'
            },
            POSITION = ['top-right', 'top-left', 'top-center', 'bottom-right', 'bottom-left', 'bottom-center'];

        $.toastDefaults.position = 'top-right';
        $.toastDefaults.dismissible = true;
        $.toastDefaults.stackable = true;
        $.toastDefaults.pauseDelayOnHover = true;

        $('.snack').click(function () {
            var type = TYPES[Math.floor(Math.random() * TYPES.length)],
                content = CONTENT[type];

            $.snack(type, content);
        });

        $('.toast-btn').click(function () {
            var rng = Math.floor(Math.random() * 2) + 1,
                type = TYPES[Math.floor(Math.random() * TYPES.length)],
                title = TITLES[type],
                content = CONTENT[type];

            if (rng === 1) {
                $.toast({
                    type: type,
                    title: title,
                    subtitle: '11 mins ago',
                    content: content,
                    delay: 5000
                });
            } else {
                $.toast({
                    type: type,
                    title: title,
                    subtitle: '11 mins ago',
                    content: content,
                    delay: 5000,
                    img: {
                        src: 'https://via.placeholder.com/20',
                        alt: 'Image'
                    }
                });
            }
        });
    </script> --}}


    <script>

        function numberOnly(id) {
            var element = document.getElementById(id);
            element.value = element.value.replace(/[^0-9]/gi, "");
        }
        $('input[type=radio][name=radio_search_customer]').on('change', function(){
            let radio_val = $(this).val();
            if(radio_val=='complaint_id')
            {
                $('#text_search_customer').attr("placeholder", "Complaint ID..");
                $('#btn_search_customer').val('btn_complaint_id');
            }
            else if(radio_val=='customer_name_no')
            {
                $('#text_search_customer').attr("placeholder", "Search Customer..");
                $('#btn_search_customer').val('btn_customer');
            }
        });
        $('#btn_search_customer').on('click',function(){
            let btn_search=$(this).val();
            let text_search_val = $('#text_search_customer').val();
            $('#error_pop').removeClass('show'); 
            $('#error_pop').addClass('fade');
            if(btn_search=='btn_customer')
            {
                $.ajax({
                    type: "GET",
                    url: "<?php echo url('/');?>/get_complaint/"+btn_search+"/"+text_search_val,
                    //data: dataString,
                    cache: false,
                    //dataType: 'json',
                    contentType: 'application/json; charset=utf-8',
                    statusCode: {
                        404: function(responseObject, textStatus, jqXHR) {
                            // No content found (404)
                            // This code will be executed if the server returns a 404 response
                        },
                        503: function(responseObject, textStatus, errorThrown) {
                            // Service Unavailable (503)
                            // This code will be executed if the server returns a 503 response
                        }           
                    },
                    success: function(data)
                    {
                        $('#customer_div').css('display', 'block');
                        $('#div_select_complaint_ids').css('display', 'block');
                        var obj = jQuery.parseJSON(data);
                        
                        console.log(obj);
                        $('#customer_id').val(obj[0][0].cust_id);
                        $('#p_customer_name').text(obj[0][0].customer_name);
                        $('#p_customer_primary_no').text(obj[0][0].primary_contact_no);
                        $('#p_customer_location').text(obj[0][0].location);
                        $('#p_customer_email').text(obj[0][0].prmt_email_id);
                        var address_1 = obj[0][0].address_line_1;
                        var address_2 = obj[0][0].address_line_2;
                        var area = obj[0][0].area;
                        var city = obj[0][0].city;
                        var landmark = obj[0][0].landmark;
                        var pincode = obj[0][0].pincode;
                        $('#p_customer_address').text(address_1+" "+address_2+" "+area+" "+city+" "+landmark+" "+pincode);
                        $('#customer_products').find('option').remove();
                        for(var i=0;i<obj[0].length;i++){
                            var newOption = new Option(obj[0][i].product_name, obj[0][i].product_id+"."+obj[0][i].id, false, false);
                            $('#customer_products').append(newOption);
                        }
                        $('#select_complaint_ids').find('option').remove().end();
                        for(var i=0;i<obj[1].length;i++){
                            $('#select_complaint_ids').append("<option value='"+obj[1][i].generated_complaint_id+"'>"+obj[1][i].generated_complaint_id+" ("+obj[1][i].complaint_date+")</option>");
                        }
                        $('#select_complaint_ids').selectpicker("refresh");
                        //console$('#customer_div').css('display', 'block');
                        $('#div_selectpicker').css('display', 'block');
                        $('#product_div').css('display', 'none');
                        $('#div_submit').css('display','block');
                        $('#remarks').val(null);
                        $('#remarks').prop('disabled',false);
                    },
                    error: function(xhr, status, error){
                        var errorMessage = xhr.status + ': ' + xhr.statusText;
                        alert("Customer Not Found");
                        $('#error_text').text("Customer");
                        $('#error_pop').removeClass('fade'); 
                        $('#error_pop').addClass('show');
                        // $.toast({
                        //     type: 'error',
                        //     title: 'Customer Not Found',
                        //     subtitle: '11 mins ago',
                        //     content: 'Something Wennt Wrong',
                        //     delay: 5000
                        // });
                    }
                    
                });
            }
            else if(btn_search=='btn_complaint_id')
            {
                $.ajax({
                    type: "GET",
                    url: "<?php echo url('/');?>/get_complaint/"+btn_search+"/"+text_search_val,
                    //data: dataString,
                    cache: false,
                    //dataType: 'json',
                    contentType: 'application/json; charset=utf-8',
                    statusCode: {
                        404: function(responseObject, textStatus, jqXHR) {
                            // No content found (404)
                            // This code will be executed if the server returns a 404 response
                        },
                        503: function(responseObject, textStatus, errorThrown) {
                            // Service Unavailable (503)
                            // This code will be executed if the server returns a 503 response
                        }           
                    },
                    success: function(data)
                    {
                        //alert(data);
                        console.log(data);
                        var obj = jQuery.parseJSON(data);
                        $('#customer_div').css('display', 'block');
                        $('#div_selectpicker').css('display', 'none');
                        $('#product_div').css('display', 'block');
                        $('#card_header_text').text("Complaint Details");
                        $('#div_add_image').css('display', 'none');

                        $('#customer_id').val(obj[0].cust_id);
                        $('#p_customer_name').text(obj[0].customer_name);
                        $('#p_customer_primary_no').text(obj[0].primary_contact_no);
                        $('#p_customer_location').text(obj[0].location);
                        $('#p_customer_email').text(obj[0].prmt_email_id);
                        var address_1 = obj[0].address_line_1;
                        var address_2 = obj[0].address_line_2;
                        var area = obj[0].area;
                        var city = obj[0].city;
                        var landmark = obj[0].landmark;
                        var pincode = obj[0].pincode;
                        $('#p_customer_address').text(address_1+" "+address_2+" "+area+" "+city+" "+landmark+" "+pincode);
                        $('#customer_products').find('option').remove();

                        $('#remarks').val(obj[0].remarks);
                        $('#remarks').prop('disabled',true);

                        var srno = 1;
                        var rows = "<table class='table table-striped table-bordered table-responsive' id='records'>";
                            rows += "<thead class='thead'><tr><th>Sr No</th><th>Complaint Date</th><th>Product Name</th><th>Delivery Date</th><th>Vendor Name</th><th>Delivered By</th><th>Lead Owner</th><th>Complaint By</th><th>Status</th></tr></thead>";
                            rows += "<tbody class='tbody'>";
                        for(var i=0; i<obj.length; i++){
                            rows += "<tr class='rows'>";
                            rows += "<td> <span >"+srno+"</span> <input type='hidden' name='order_details_id[]' value='"+obj[i].id+"'> <input type='hidden' name='lead_id[]' value='"+obj[i].lead_id+"'> </td>";
                            rows += "<td> <span name='complaint_date[]'>"+obj[i].complaint_date+"</span> <input type='hidden' name='complaint_date[]' value='"+obj[i].complaint_date+"'> </td>";
                            rows += "<td> <span name='product_name[]'>"+obj[i].product_name+"</span> <input type='hidden' name='product_id[]' value='"+obj[i].product_id+"'> </td>";
                            rows += "<td> <span name='Del_Date[]'>"+obj[i].DelDate+"</span> </td>";
                            rows += "<td> <span name='vendor_name[]'>"+obj[i].vendor_name+"</span> <input type='hidden' name='vendor_id[]' value='"+obj[i].vendor_id+"'> </td>";
                            rows += "<td> <span name='DelAssignedTo[]'>"+obj[i].delivered_by+"</span> <input type='hidden' name='DelAssignedTo[]' value='"+obj[i].delivered_by+"'> </td>";
                            rows += "<td> <span name='username[]'>"+obj[i].lead_owner+"</span> <input type='hidden' name='username[]' value='"+obj[i].lead_owner+"'> </td>";
                            rows += "<td> <span name='created_by[]'>"+obj[i].created_by+"</span> <input type='hidden' name='created_by[]' value='"+obj[i].created_by+"'> </td>";
                            rows += "<td> <span name='status[]'>"+obj[i].status+"</span> <input type='hidden' name='status[]' value='"+obj[i].status+"'> </td>";
                            //rows += "<td> <textarea class='form-control' name='remarks[]' id='remarks' rows='3' required></textarea></td>";
                            rows += "</tr>";
                            srno++;
                        }
                        rows += "</tbody>";
                        rows += "</table>";
                        $('#records').DataTable().destroy();
                        $('#div_table').find('table').remove();
                        // $('#records tbody').remove();
                        $('#div_table').append(rows);
                        $('#records').DataTable().draw();
                        $('#div_submit').css('display','none');
                    },
                    error: function(xhr, status, error){
                        
                        var errorMessage = xhr.status + ': ' + xhr.statusText;
                        alert("Complaint Not Found");
                        // $.toast({
                        //     type: 'error',
                        //     title: 'Customer Not Found',
                        //     subtitle: '11 mins ago',
                        //     content: 'Something Wennt Wrong',
                        //     delay: 5000
                        // });
                    }
                });
            }
        });

		$(document).ready(function() {
            $('#customer_products').select2({
                theme: "classic",
                placeholder: 'Select Products',
                allowClear: true
            });
        });

        $('#customer_products').on('change', function(){
            var products = JSON.stringify($(this).val());
            $('#product_div').css('display', 'block');
			var dataString = ({_token:"{{ csrf_token() }}",product:""+products});
			$.ajax({
				type: "POST",
				url: "<?php echo url('/');?>/get_product_details",
				data: dataString,
				cache: false,
				//dataType: 'json',
				//contentType: 'application/json; charset=utf-8',
				success: function(data)
				{
					//console.log(data);
					var obj = jQuery.parseJSON(data);
                    var srno = 1;
                    var rows = "<table class='table table-striped table-bordered' id='records' width='100%'>";
                        rows += "<thead class='thead'><tr><th>Sr No</th><th>Product Name</th><th>Delivery Date</th><th>Vendor Name</th><th>Delivered By</th><th>Lead Owner</th></tr></thead>";
                        rows += "<tbody class='tbody'>";
                    for(var i=0; i<obj.length; i++){
                        rows += "<tr class='rows'>";
                        rows += "<td> <span >"+srno+"</span> <input type='hidden' name='order_details_id[]' value='"+obj[i].id+"'> <input type='hidden' name='lead_id[]' value='"+obj[i].lead_id+"'> </td>";
                        rows += "<td> <span name='product_name[]'>"+obj[i].product_name+"</span> <input type='hidden' name='product_id[]' value='"+obj[i].product_id+"'> </td>";
                        rows += "<td> <span name='Del_Date[]'>"+obj[i].DelDate+"</span> </td>";
                        rows += "<td> <span name='vendor_name[]'>"+obj[i].vendor_name+"</span> <input type='hidden' name='vendor_id[]' value='"+obj[i].vendor_id+"'> </td>";
                        rows += "<td> <span name='DelAssignedTo[]'>"+obj[i].DelAssignedTo+"</span> <input type='hidden' name='DelAssignedTo[]' value='"+obj[i].DelAssignedTo+"'> </td>";
                        rows += "<td> <span name='username[]'>"+obj[i].username+"</span> <input type='hidden' name='username[]' value='"+obj[i].username+"'> </td>";
                        //rows += "<td> <textarea class='form-control' name='remarks[]' id='remarks' rows='3' required></textarea></td>";
                        rows += "</tr>";
                        srno++;
                    }
                    rows += "</tbody>";
                    rows += "</table>";
                    $('#records').DataTable().destroy();
                    $('#div_table').find('table').remove();
                    // $('#records tbody').remove();
                    $('#div_table').append(rows);
                    $('#records').DataTable().draw();
                    
				},
                error: function(xhr, status, error){
                    var errorMessage = xhr.status + ': ' + xhr.statusText
                    alert("not found");
                    // $.toast({
                    //     type: 'error',
                    //     title: 'Customer Not Found',
                    //     subtitle: '11 mins ago',
                    //     content: 'Something Wennt Wrong',
                    //     delay: 5000
                    // });
                }
            });
        });

        //Complaint Images show
        $(function() {
            // Multiple images preview in browser
            var imagesPreview = function(input, placeToInsertImagePreview) {

                if (input.files) {
                    var filesAmount = input.files.length;

                    for (i = 0; i < filesAmount; i++) {
                        var reader = new FileReader();

                        reader.onload = function(event) {
                            $($.parseHTML('<img>')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                        }

                        reader.readAsDataURL(input.files[i]);
                    }
                }

            };
            
        });
        var max_fields = 10;
        var wrapper = $("#div_add_image");
        var rm = $('#remove_div');
        var x=1;
        $('#add_more').on('click',function(e){
            e.preventDefault();
        
            if(x < max_fields)
            {
                x++; 
                //alert(x);
                $(wrapper).append('<div class="row" id="remove_div'+x+'"><div class="form-group col-md-3 text-center" id="image_label"><label for="complaint_img" class="form-label"><b>Add Image</b></label></div><div class="col-md-9">  <input type="file" id="shop_images" class="" name="complaint_img[]" required="true" accept="image/png, image/jpeg, image/jpg," onclick="fileType(this.id);"><input type="button" class="btn btn-danger" id="'+x+'" onclick="remove_image_fun(this.id);" name="remove_image" value="- Remove" ></div></div>');

            }
        
        });
        function remove_image_fun(id)
        {
            $('#remove_div'+id).remove();x--;
        }

        //select complaints ids show complaint
        $('#select_complaint_ids').selectpicker('render');
        $('#select_complaint_ids').selectpicker('refresh');
        $('#select_complaint_ids').on('change', function(){
            let btn_search="btn_complaint_id";
            let text_search_val = $(this).val();
            $.ajax({
                type: "GET",
                url: "<?php echo url('/');?>/get_complaint/"+btn_search+"/"+text_search_val,
                //data: dataString,
                cache: false,
                //dataType: 'json',
                contentType: 'application/json; charset=utf-8',
                success: function(data)
                {
                    //location.reload(true);
                    //console.log(data);
                    var obj = jQuery.parseJSON(data);
                    $('#customer_div').css('display', 'block');
                    $('#div_selectpicker').css('display', 'none');
                    $('#product_div').css('display', 'block');
                    $('#card_header_text').text("Complaint Details");
                    $('#div_add_image').css('display', 'none');

                    $('#customer_id').val(obj[0].cust_id);
                    $('#p_customer_name').text(obj[0].customer_name);
                    $('#p_customer_primary_no').text(obj[0].primary_contact_no);
                    $('#p_customer_location').text(obj[0].location);
                    $('#p_customer_email').text(obj[0].prmt_email_id);
                    var address_1 = obj[0].address_line_1;
                    var address_2 = obj[0].address_line_2;
                    var area = obj[0].area;
                    var city = obj[0].city;
                    var landmark = obj[0].landmark;
                    var pincode = obj[0].pincode;
                    $('#p_customer_address').text(address_1+" "+address_2+" "+area+" "+city+" "+landmark+" "+pincode);
                    $('#customer_products').find('option').remove();

                    $('#remarks').val(obj[0].remarks);
                    $('#remarks').prop('disabled',true);

                    var srno = 1;
                    var rows = "<table class='table table-striped table-bordered table-responsive' id='records'>";
                        rows += "<thead class='thead'><tr><th>Sr No</th><th>Complaint Date</th><th>Product Name</th><th>Delivery Date</th><th>Vendor Name</th><th>Delivered By</th><th>Lead Owner</th><th>Complaint By</th><th>Status</th></tr></thead>";
                        rows += "<tbody class='tbody'>";
                    for(var i=0; i<obj.length; i++){
                        rows += "<tr class='rows'>";
                        rows += "<td> <span >"+srno+"</span> <input type='hidden' name='order_details_id[]' value='"+obj[i].id+"'> <input type='hidden' name='lead_id[]' value='"+obj[i].lead_id+"'> </td>";
                        rows += "<td> <span name='complaint_date[]'>"+obj[i].complaint_date+"</span> <input type='hidden' name='complaint_date[]' value='"+obj[i].complaint_date+"'> </td>";
                        rows += "<td> <span name='product_name[]'>"+obj[i].product_name+"</span> <input type='hidden' name='product_id[]' value='"+obj[i].product_id+"'> </td>";
                        rows += "<td> <span name='Del_Date[]'>"+obj[i].DelDate+"</span> </td>";
                        rows += "<td> <span name='vendor_name[]'>"+obj[i].vendor_name+"</span> <input type='hidden' name='vendor_id[]' value='"+obj[i].vendor_id+"'> </td>";
                        rows += "<td> <span name='DelAssignedTo[]'>"+obj[i].delivered_by+"</span> <input type='hidden' name='DelAssignedTo[]' value='"+obj[i].delivered_by+"'> </td>";
                        rows += "<td> <span name='username[]'>"+obj[i].lead_owner+"</span> <input type='hidden' name='username[]' value='"+obj[i].lead_owner+"'> </td>";
                        rows += "<td> <span name='created_by[]'>"+obj[i].created_by+"</span> <input type='hidden' name='created_by[]' value='"+obj[i].created_by+"'> </td>";
                        rows += "<td> <span name='status[]'>"+obj[i].status+"</span> <input type='hidden' name='status[]' value='"+obj[i].status+"'> </td>";
                        //rows += "<td> <textarea class='form-control' name='remarks[]' id='remarks' rows='3' required></textarea></td>";
                        rows += "</tr>";
                        srno++;
                    }
                    rows += "</tbody>";
                    rows += "</table>";
                    $('#records').DataTable().destroy();
                    $('#div_table').find('table').remove();
                    // $('#records tbody').remove();
                    $('#div_table').append(rows);
                    $('#records').DataTable().draw();
                    $('#div_submit').css('display','none');
                },
                error: function(xhr, status, error){
                    var errorMessage = xhr.status + ': ' + xhr.statusText
                    // $.toast({
                    //     type: 'error',
                    //    // title: 'Customer Not Found',
                    //     subtitle: '11 mins ago',
                    //     content: 'Something Wennt Wrong',
                    //     delay: 5000
                    // });
                }

            });
        });

    </script>  
@endsection
</body>
</html>