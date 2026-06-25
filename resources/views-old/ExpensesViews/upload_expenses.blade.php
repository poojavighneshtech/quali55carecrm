@extends('header_and_sidebar')
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        
        <title>upload Expenses</title>
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
        <div class="card shadow-sm bg-white rounded">  
            <div class="card-header"  style="background-color: #4a6fdc; color: white;" >
                <center>Upload Expenses</center>
            </div> 
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="expense_date">Expense Date</label>
                                <input type="date" class="form-control form-control-sm" name="expense_date" id="expenseDate" value="{{$yesterday}}">
                            </div>
                            <div class="col-md-4">
                                <label for="delivery_staff_name">Del Users</label>
                                <select class="form-control form-control-sm selectpicker border" name="delivery_staff_name" id="deliveryStaffName" data-live-search="true" data-size="5">
                                    @foreach ($getDelUsers as $key=>$delUser)
                                        <option value="{{$delUser->username}}">{{$delUser->username}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <br>
                                <button type="button" class="form-control btn btn-outline-primary btn-sm mt-2" name="submit" id="submit" >Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid" id="divContainer" style="display: none">
                <div class="row">
                    <div class="col-md-12">
                        <form action="{{url('/')}}/insert_expense" method="post" enctype="multipart/form-data">
                            @csrf
                            {{--hidden--}}
                            <input type="hidden" name="del_user_name" id="delUserHidden">
                            <input type="hidden" name="selected_date" id="selectedDateHidden">
                            <div class="card card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-auto">
                                                <label for="selected_expense_date"><strong>Date :</strong></label>
                                            </div>
                                            <div class="col-auto">
                                                <span id="selectedDate"></span>
                                            </div>
                                            <div class="col-auto">
                                                <label for="del_user_name"><strong>Staff Name :</strong></label>
                                            </div>
                                            <div class="col-auto">
                                                <span id="delUserNameSpan"></span>
                                            </div>
                                            <div class="col-auto">
                                                <label for="cash_carried_forward"><strong>Cash Carried Forward :</strong></label>
                                            </div>
                                            <div class="col-auto text-left">
                                                <span id="cashCarriedForward_span"></span>
                                                <input type="hidden" name="" id="cashCarriedForward_input" value="0">
                                            </div>
                                            {{-- <div class="col-auto">
                                                <label for="cash_expected"><strong>Cash Expected :</strong></label>
                                            </div>
                                            <div class="col-auto text-left">
                                                <span id="cashExpected">1200</span>
                                            </div>
                                            <div class="col-auto">
                                                <label for="deposit_returned"><strong>Deposit Returned :</strong></label>
                                            </div>
                                            <div class="col-auto text-left">
                                                <span id="depositReturned">1200</span>
                                            </div>
                                            <div class="col-auto">
                                                <label for="expected_balance_cash"><strong>Expected Balance Cash :</strong></label>
                                            </div>
                                            <div class="col-auto text-left">
                                                <span id="expectedBalanceCash">1200</span>
                                            </div> --}}
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="holiday" id="holidayCheckbox">
                                            <label class="custom-control-label" for="holidayCheckbox">Holiday</label>
                                            <input type="hidden" name="voucher_id" id="voucher_id">
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-6 text-right">
                                                <label for="cash_rec_frm_off">Cash received from office : </label>
                                            </div>
                                            <div class="col-md-6">
                                                <input type="number" class="form-control form-control-sm calculateExp" name="cash_received_from_office" id="cashRecFrmOff" value="0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-6 text-right">
                                                <label for="cash_rec_frm_off">Cash received from customer : </label>
                                            </div>
                                            <div class="col-md-6 justify-content-left">
                                                <input type="number" class="form-control form-control-sm calculateExp" name="cash_received_from_customer" id="cashRecFrmCust" value="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-6 text-right">
                                                <label for="transport">Transport : </label>
                                            </div>
                                            <div class="col-md-6">
                                                <input type="number" class="form-control form-control-sm calculateExp" name="transport" id="transport" value="0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-6 text-right">
                                                <label for="cash_rec_frm_off">Actual Deposit returned : </label>
                                            </div>
                                            <div class="col-md-6 justify-content-left">
                                                <input type="number" class="form-control form-control-sm calculateExp" name="actual_deposit_returned" id="actualDepositReturned" value="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-6 text-right">
                                                <label for="expense">Expense : </label>
                                            </div>
                                            <div class="col-md-6">
                                                <input type="number" class="form-control form-control-sm calculateExp" name="expense" id="expense" value="0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-6 text-right">
                                                <label for="labour_charge">Labour Charges : </label>
                                            </div>
                                            <div class="col-md-6 justify-content-left">
                                                <input type="number" class="form-control form-control-sm calculateExp" name="labour_charges" id="labourCharges" value="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-6 text-right">
                                                <label for="receipt_no">Receipt No : </label>
                                            </div>
                                            <div class="col-md-6 justify-content-left">
                                                <input type="text" class="form-control form-control-sm" name="receipt_no" id="receiptNo" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-6 text-right">
                                                <label for="balance_cash">Balance Cash : </label>
                                            </div>
                                            <div class="col-md-6">
                                                <span id="balanceCash_span">0</span>
                                                <input type="hidden" name="balance_cash" id="balanceCash_input" value="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row justify-content-center">
                                    <div class="col-md-4">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="expenseReceiptImage">Receipt Image</span>
                                            </div>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input form-control-sm" name="expense_image" id="expenseReceiptImageFile" aria-describedby="expenseReceiptImage" accept="image/png, image/gif, image/jpeg" required>
                                                <label class="custom-file-label" for="expenseReceiptImage">Choose file</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-1">
                                        <button type="button" class="btn btn-sm btn-outline-primary viewReceipt" id="viewReceiptImageBtn" data-id="viewReceiptImageBtn">
                                            <i class="fas fa-image"></i>
                                        </button>
                                        <input type="hidden" name="hidden_receipt_image" id="hidden_receipt_image">
                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <button type="submit" class="btn btn-outline-success">Submit</button>
                                </div>
                            </div>
                        </form>
                        <br>
                    </div>
                </div>
            </div>
            {{--modal Images--}}
            <div class="modal fade" id="viewReceiptImage" tabindex="-1" role="dialog" aria-labelledby="viewReceiptImage" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="viewReceiptImage">Payment Image</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">                    
                            <div>
                                <img class="img-fluid img" src="" id="ReceiptImagePath" alt="No Image Found" >
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @section('script')
        <script>
            $(document).ready(function () {
                let path = "";
                let imgState = null;
                $('#submit').on('click',function(){
                    let date = $('#expenseDate').val();
                    let delUser = $('#deliveryStaffName').val()
                    $('#selectedDate').text(date);
                    $('#selectedDateHidden').val(date);
                    $('#delUserHidden').val(delUser);
                    $('#delUserNameSpan').text(delUser);
                    $('#divContainer').css('display','block')
                    var dataString = ({_token:"{{ csrf_token() }}",del_user:""+delUser,date:""+date});
    
                    //check the voucher is available or not
                    $.ajax({
                        type: "POST",
                        url: "{{route('check-voucher-available')}}",
                        data: dataString,
                        cache: false,
                        success: function (data) {
                            if(data!='failed'){
                                let voucher = data[0];
                                console.log(voucher);
                                $('#cashRecFrmOff').val(voucher.cash_from_office);
                                $('#cashRecFrmCust').val(voucher.cash_received_from_customer);
                                $('#transport').val(voucher.transport);
                                $('#actualDepositReturned').val(voucher.deposite_paid);
                                $('#expense').val(voucher.expenses);
                                $('#labourCharges').val(voucher.labour);
                                $('#receiptNo').val(voucher.receipt_no);
                                $('#balanceCash_span').text(voucher.balance_cash);
                                $('#balanceCash_input').val(voucher.balance_cash);
                                //path = voucher.img_url;
                                imgState = "available";
                                path = voucher.img_url;
                                //$('#expenseReceiptImageFile').val(path);
                                $('#voucher_id').val(voucher.id);
                                $('#hidden_receipt_image').val(voucher.img_url);
                                $('#expenseReceiptImageFile').prop('required',false);
                                //$('#ReceiptImagePath').attr('src',voucher.img_url);
                            }else{
                                $('#voucher_id').val("");
                                $('#expenseReceiptImageFile').prop('required',true);
                                $('#hidden_receipt_image').val(null);
                                $('#cashRecFrmOff').val(0);
                                $('#cashRecFrmCust').val(0);
                                $('#transport').val(0);
                                $('#actualDepositReturned').val(0);
                                $('#expense').val(0);
                                $('#labourCharges').val(0);
                                $('#receiptNo').val(0);
                                $('#balanceCash_span').text(0);
                                $('#balanceCash_input').val(0);
                            }
                        }
                        
                    });
    
                    $.ajax({
                        type: "POST",
                        url: "{{url('/')}}/check_prev_bal",
                        data: dataString,
                        cache: false,
                        success: function (data) {
                            let total = 0;
                            if(data.status!='Not Found'){
                                for(let i=0; i<data.status.length;i++){
                                    total = total+parseInt(data.status[i].balance_cash);
                                }
                                $('#cashCarriedForward_span').text(total);
                                $('#cashCarriedForward_input').val(total);
                            }else{
                                $('#cashCarriedForward_span').text(total);
                                $('#cashCarriedForward_input').val(total);
                            }
                        }
                    });
                })
                
                $('.calculateExp').on('input',function(){
                    let carriedBal = $('#cashCarriedForward_input').val();
                    let cashRecFrmOff = $('#cashRecFrmOff').val();
                    let cashRecFrmCust = $('#cashRecFrmCust').val();
                    let transport = $('#transport').val();
                    let actualDepositReturned = $('#actualDepositReturned').val();
                    let expense = $('#expense').val();
                    let labourCharges = $('#labourCharges').val();
                    let total = carriedBal;
    
                    total = parseInt(carriedBal)+parseInt(cashRecFrmOff)+parseInt(cashRecFrmCust)-parseInt(transport)-parseInt(actualDepositReturned)-parseInt(expense)-parseInt(labourCharges);
                    $('#balanceCash_span').text(total);
                    $('#balanceCash_input').val(total);
                });
                
                $('#holidayCheckbox').on('click',function(){
                    if($(this).is(":checked")){
                        $('.calculateExp').attr('disabled',true);
                        $('#expenseReceiptImageFile').attr('required',false);
                    }else{
                        $('.calculateExp').attr('disabled',false);
                        $('#expenseReceiptImageFile').attr('required',true);
                    }
                });
                
                $('#expenseReceiptImageFile').on('change',function(e){
                    let image = document.getElementById('expenseReceiptImageFile');
                    let imageName = image.files[0].name;
                    var nextSibling = e.target.nextElementSibling
                    nextSibling.innerText = imageName;
                    imgState = null;
                    path = URL.createObjectURL(event.target.files[0]);
                });
                $("#viewReceiptImageBtn").click(function() {
                    if(imgState != null){
                        path = "http://"+path;
                    }
                    let id = $(this).data("id");
                    //let path = $('#expenseReceiptImageFile').val();
                    //let path = $("#hidden_receipt_image"+id).val();
                    $("#ReceiptImagePath").attr("src", path);
                    $("#viewReceiptImage").modal('show');
                });
            });
        </script>
    @endsection
</body>
</html>
