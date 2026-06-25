<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Quali55care</title>
    <link href="{{url('/')}}/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
        <link
            href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
            rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.min.css"/>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
        <!-- Custom styles for this template-->
        <link href="{{url('/')}}/assets/css/sb-admin-2.min.css" rel="stylesheet">   
        <script src="{{url('/')}}/assets/vendor/jquery/jquery.min.js"></script>
        <script src="{{url('/')}}/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="{{url('/')}}/assets/vendor/jquery/jquery.cookie.js" type="text/javascript"></script>

        <!-- Core plugin JavaScript-->
        <script src="{{url('/')}}/assets/vendor/jquery-easing/jquery.easing.min.js"></script>

        <!-- Custom scripts for all pages-->
        <script src="{{url('/')}}/assets/js/sb-admin-2.min.js"></script>
        
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
        {{-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script> --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="{{url('/')}}/assets/js/jquery.table2excel.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js" ></script>
</head>
    <body>
        <div class="row">
            <div class="col-md-12 text-center">
                <img src="{{url('/')}}/assets/images/logo_small.png" alt="">
            </div>
        </div>
        <div class="container-fluid my-2">
            <div class="card">
                <div class="card-header">
                    <label for="" class="h5"><strong>Bank Details</strong></label>
                </div>
                <ul class="list-group list-group-flush accordion" id="bank_details_accordian">
                    <li class="list-group-item btn btn-link text-left click-accordian" type="button" data-toggle="collapse" data-target="#bank_details_collapse" data-radio="bank_details" aria-expanded="true" aria-controls="bank_details_collapse">
                        <input class="form-check-input ml-1" type="radio" name="radio_bank" id="radio_bank_details" value="bank_details"@if(old('submit')=='bank_details') checked @endif >
                        <label class="form-check-label ml-4" for="radio_bank_details">
                            Bank Details
                        </label>
                    </li>
                    <div id="bank_details_collapse" class="collapse @if(old('submit')=='bank_details') show @endif" aria-labelledby="headingOne" data-parent="#bank_details_accordian" >
                        <div class="container-fluid py-2">
                            <form action="{{route('cust-bank',["link_id"=>$link_id])}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-md-2 text-left ">
                                        <label for="" class="text-dark"><strong>Account Holder Name :</strong></label>
                                    </div>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="bank_account_holder_name" id="bank_account_holder_name" placeholder="Account Holder Name" value="{{old('bank_account_holder_name')}}" required>
                                    </div>
                                </div>
                                <div class="row my-2">
                                    <div class="col-md-2 text-left">
                                        <label for="" class="text-dark"><strong>Bank Name :</strong></label>
                                    </div>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="bank_name" id="bank_name" placeholder="Bank Name" value="{{old('bank_name')}}" required>
                                    </div>
                                </div>
                                <div class="row ">
                                    <div class="col-md-2 text-left">
                                        <label for="" class="text-dark"><strong>Branch Name :</strong></label>
                                    </div>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="bank_branch_name" id="bank_branch_name" placeholder="Branch Name" value="{{old('bank_branch_name')}}" required>
                                    </div>
                                </div>
                                <div class="row my-2">
                                    <div class="col-md-2 text-left">
                                        <label for="" class="text-dark"><strong>Account Number :</strong></label>
                                    </div>
                                    <div class="col-md-10">
                                        <input type="password" class="form-control" name="bank_account_number" id="bank_account_number" placeholder="Account Number" value="{{old('bank_account_number')}}"
                                            required onblur="blurPass();" onfocus="focusPass();">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2 text-left">
                                        <label for="" class="text-dark"><strong>Confirm Account No :</strong></label>
                                    </div>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="bank_confirm_account_number" id="bank_confirm_account_number" placeholder="Confirm Account Number"
                                            value="{{old('bank_confirm_account_number')}}" required
                                            onpaste="return false;" ondrop="return false;">
                                        @if($errors->has('bank_confirm_account_number'))
                                            <small><span class="form-text text-danger" id="bank_confirm_account_number_error">{{$errors->first('bank_confirm_account_number') }}</span></small>
                                        @endif
                                    </div>
                                </div>
                                <div class="row my-2">
                                    <div class="col-md-2 text-left">
                                        <label for="" class="text-dark"><strong>IFSC Code :</strong></label>
                                    </div>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control text-uppercase" name="bank_ifsc_code" id="bank_ifsc_code" placeholder="IFSC code" 
                                            value="{{old('bank_ifsc_code')}}" maxlength="11" required>
                                        <small><span class="text-muted text-lowercase" id="ifsc_text"></span></small>
                                        @if($errors->has('bank_ifsc_code'))
                                            <small><span class="form-text text-danger" id="bank_ifsc_code_error">{{$errors->first('bank_ifsc_code') }}</span></small>
                                        @endif
                                        @if($errors->has('ifsc_status'))
                                            <small><span class="form-text text-danger" id="bank_ifsc_status">{{$errors->first('ifsc_status') }}</span></small>
                                        @endif
                                        <small><span class="form-text text-danger" id="bank_ifsc_code_error_notfound" style="display: none">IFSC code not found</span></small>
            
                                        {{-- ifsc verified or not --}}
                                        <input type="hidden" name="ifsc_status" id="ifsc_status" value="@if(old('ifsc_status')=='true') true @endif">
                                    </div>
                                </div>
                               
                                
                                <div class="row my-2">
                                    <div class="col-md-2 text-left">
                                        <label for="" class="text-dark"><strong>Account Type :</strong></label>
                                    </div>
                                    <div class="col-md-10">
                                        <select class="form-control" name="bank_acount_type" id="bank_acount_type" title="Account Type" required>
                                            <option value="saving">Saving</option>
                                            <option value="current">Current</option>
                                        </select>
                                    </div>
                                </div>
            
                                <div class="row d-flex justify-content-center">
                                    <button type="submit"  class="form-control btn btn-outline-success col-4" name="submit" value="bank_details">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <li class="list-group-item btn btn-link text-left click-accordian" data-toggle="collapse" data-radio="cheque_image" 
                        data-target="#cheque_details_collapse" aria-expanded="false" aria-controls="cheque_details_collapse">
                        <input class="form-check-input ml-1" type="radio" name="radio_bank" id="radio_bank_cheque" value="cheque_image" @if(old('submit')=='cheque_details') checked @endif>
                        <label class="form-check-label ml-4" for="radio_bank_cheque">
                            Cheque Image
                        </label>
                    </li>

                    <div id="cheque_details_collapse" class="collapse @if(old('submit')=='cheque_details') show @endif" aria-labelledby="headingTwo" data-parent="#bank_details_accordian">
                        <form action="{{route('cust-bank',["link_id"=>$link_id])}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="container-fluid py-2">
                                <div class="row">
                                    <div class="input-group mb-3">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="capture_cheq_img" id="capture_cheq_img" 
                                                accept="image/*" required>
                                            <label class="custom-file-label" for="capture_cheq_img"><span id="capture_cheq_img_label">Choose File</span></label>
                                        </div>
                                    </div>
                                    @if($errors->has('capture_cheq_img'))
                                        <span class="form-text text-danger" id="capture_cheq_img_error">{{$errors->first('capture_cheq_img') }}</span>
                                    @endif
                                    {{-- <div class="text-center"> --}}
                                        <img class="rounded mx-auto d-block img-fluid img-thumbnail" src="" id="capture_cheq_img_preview" style="display: none">
                                    {{-- </div> --}}
                                </div>
    
                                <div class="row d-flex justify-content-center mt-2">
                                    <button type="submit" name="submit" class="form-control btn btn-outline-success col-4" value="cheque_details">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </ul>
            </div>
        </div>

        <script>
            $('#bank_ifsc_code').on('input',function(){
                let ifsc_code = $(this).val();
                if(ifsc_code.length ==11){
                    $.ajax({
                        type: "GET",
                        url: "https://ifsc.razorpay.com/"+ifsc_code,
                        // data: ,
                        cache:false,
                        success: function (data)
                        {
                            console.log(data);
                           
                            let branch_center = data.BRANCH+","+data.CENTRE;
                            $('#ifsc_text').text(branch_center);
                            $('#bank_ifsc_code_error').hide();
                            $('#bank_ifsc_code_error_notfound').hide();
                            $('#bank_ifsc_status').hide();
                            $('#ifsc_text').show();
                            $('#ifsc_status').val(true);
                            
                            
                        },error: function(data, textStatus,errorThrown)
                        {
                            //console.log(data,textStatus,errorThrown);
                            $('#ifsc_text').hide();
                            $('#bank_ifsc_code_error_notfound').show();
                            $('#ifsc_status').val(false);
                        }
                    });
                }
            });

            //copy paste disable
            $('#bank_confirm_account_number').on('paste', e => e.preventDefault());
            
            function blurPass(){
                document.getElementById("bank_account_number").type = "password";
            }
            function focusPass(){
                document.getElementById("bank_account_number").type = "text";
            }
        </script>
        <script type="text/javascript">
            $('.click-accordian').on('click',function(){
                let radio = $(this).data('radio');
                if(radio=="bank_details"){
                    $('#radio_bank_details').prop('checked',true);
                }
                else if(radio=="cheque_image"){
                    $('#radio_bank_cheque').prop('checked',true);
                }
            });
            $('#capture_cheq_img').on('change',function(e){
                var image = document.getElementById('capture_cheq_img_preview');
	            image.src = URL.createObjectURL(event.target.files[0]);
                $('#capture_cheq_img_preview').show();
                var fileName = event.target.files[0].name;
                $('#capture_cheq_img_label').text(fileName);
            });
            $('#btn_camera').on('click',function(e){
                $('#cheque_camera_img').trigger('click');
            })
            $('#btn_cheq_upload_file').on('click',function(e){
                $('#cheque_file').trigger('click');
            });
        </script>

    </body>
</html>