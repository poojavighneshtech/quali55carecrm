<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Admin-Renewal Link</title>
    @section('header')
       
    @endsection
</head>

<body id="page-top">	
<!-- Page Wrapper -->

@extends('header_and_sidebar')
    @section('content')
    {{-- <div class="row">
        <div class="col-md-12 text-center">
            <img src="{{url('/')}}/assets/images/logo_small.png" alt="">
        </div>
    </div> --}}
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-9">
                    <h5>Order Details</h5>
                </div>
                <div class="col-md-3 text-right">
                    <input type="button" class="btn btn-outline-primary btn-sm" id="btn_edit" value="Edit">
                </div>
            </div>
        </div>
        @if(session()->has('error'))
        <br>
            <div class="alert alert-danger">
                {{ session()->get('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <form action="{{url('/')}}/view_renewal_or_pickup_link/{{$link}}" method="post">
            @csrf
            <div class="table table-responsive jim-table-responsive">
                <table class="table table-hover ">
                    <thead class="thead-light"> 
                        <tr>
                            <th>Product Name</th>
                            <th>Order Id</th>
                            <th>Rent</th>
                            <th>Due Date</th>
                            <th>Due Month</th>
                            <th>Total</th>
                            <th>Response</th>
                            <th>Date</th>
                            {{-- <th>Time</th> --}}
                            <th>Payment</th>
                            @if(in_array('Y',$order_existed))
                                <th>Message</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($get_renewal_data as $key=>$renewalData)
                            <tr data-row="{{$key}}" style="@if($order_existed[$key]=='Y') background-color:rgba(255, 99, 71, 0.5); @endif" >
                                <td data-label="Product Name">
                                    {{$renewalData->product_name}}
                                </td>
                                <td data-label="Order Id">{{$renewalData->order_id}}</td>
                                <td data-label="Rent">{{$renewalData->product_rent}}</td>
                                <td data-label="Due Date">{{date('d-M-Y',strtotime($renewalData->pickup_date))}}</td>
                                <td data-label="Due Month">{{$renewalData->due_month}}</td>
                                <td data-label="Total">{{$renewalData->total_rent}}</td>
                                <td data-label="Response">
                                    {{--hidden defaults ids and all--}}
                                    <input type="hidden" name="renewal_table_id[]" value ="{{$renewalData->id}}">
                                    <input type="hidden" name="order_details_id[]" value ="{{$renewalData->order_details_id}}">
                                    <input type="hidden" name="default_product_status[{{$key}}]" value="{{$renewalData->customer_reponse}}">
                                    <input type="hidden" name="default_cust_pickup_date[{{$key}}]" value="{{$renewalData->cust_response_pickup_date}}">
                                    <input type="hidden" name="default_order_pickup_date[{{$key}}]" value="{{$renewalData->order_pickup_date}}">
                                    <input type="hidden" name="hidden_prod_date" id="hidden_prod_date{{$key}}" value="{{$renewalData->pickup_date}}">
                                    <input type="hidden" name="default_payment_mode[{{$key}}]" id="default_payment_mode{{$key}}" value="{{$renewalData->cust_response_payment}}">

                                    <div class="form-group show_form_group" id="status_form_group_show{{$key}}" style="@if(isset($renewalData->customer_reponse)) display:block @endif">
                                        @if(isset($renewalData->customer_reponse) && $renewalData->customer_reponse==0)
                                            <span class="badge badge-success">Continue</span>
                                        @elseif(isset($renewalData->customer_reponse) && $renewalData->customer_reponse==1)
                                            <span class="badge badge-danger" value="1">Pickup</span>
                                        @elseif(isset($renewalData->customer_reponse) && $renewalData->customer_reponse==2)
                                            <span class="badge badge-warning" value="2">Undecided</span>
                                        @else
                                            <span class="badge badge-primary">No response</span>
                                        @endif
                                    </div>
                                    <div class="form-group edit_form_group" id="status_form_group_edit{{$key}}" style="display: none">
                                        <div class="btn-group btn-group-toggle btn-responsive btn-sm " id="btn_product_status{{$key}}" data-id="{{$key}}" data-toggle="buttons">
                                            <label class="btn btn-outline-primary btn-sm">
                                                <input type="radio" class="btn_prod_status add_required" name="changed_product_status[{{$key}}]" id="btn_continue{{$key}}" 
                                                    data-id="{{$key}}" autocomplete="off" value="0" 
                                                    @if(isset($renewalData->customer_reponse) && $renewalData->customer_reponse==0)  checked @endif>
                                                    Continue
                                            </label>
                                            <label class="btn btn-outline-primary btn-sm">
                                                <input type="radio" class="btn_prod_status add_required" name="changed_product_status[{{$key}}]" id="btn_pickup{{$key}}"
                                                    data-id="{{$key}}" autocomplete="off" value="1"
                                                    @if(isset($renewalData->customer_reponse) && $renewalData->customer_reponse==1)  checked @endif>
                                                    Pickup
                                            </label>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="Date">
                                    <div class="form-group" id="response_date_form_group_edit{{$key}}" 
                                        style="@if($renewalData->customer_reponse==1 && isset($renewalData->cust_response_pickup_date)) display:block @else display:none @endif">
                                        <input type="date" class="form-control edit_date" name="changed_cust_pickup_date[{{$key}}]" id="date_cust_pickup_date{{$key}}"
                                            @if($renewalData->customer_reponse==1 && isset($renewalData->cust_response_pickup_date)) disabled @endif
                                            value="@if(isset($renewalData->cust_response_pickup_date)){{$renewalData->cust_response_pickup_date}}@else{{$renewalData->pickup_date}}@endif">
                                    </div>
                                </td>
                                <td data-label="Payment">
                                    <div class="form-group" id="payment_response_form_group_edit{{$key}}" 
                                        style="@if($renewalData->customer_reponse==0 && isset($renewalData->cust_response_payment))) display:block @else display:none @endif">
                                        <div class="btn-group btn-group-toggle btn-responsive btn-sm" id="payment_btn{{$key}}" data-toggle="buttons">
                                            <label class="btn btn-outline-primary btn-sm">
                                                <input type="radio" class="edit_payment" name="changed_payment_mode[{{$key}}]" id="cash_radio{{$key}}" 
                                                    autocomplete="off" value="0" @if(isset($renewalData->cust_response_payment) && $renewalData->cust_response_payment==0) checked @else disabled @endif>
                                                    Cash
                                            </label>
                                            <label class="btn btn-outline-primary btn-sm">
                                                <input type="radio"  class="edit_payment" name="changed_payment_mode[{{$key}}]" id="online_radio{{$key}}"
                                                    autocomplete="off" value="1" @if(isset($renewalData->cust_response_payment) && $renewalData->cust_response_payment==1) checked @else disabled @endif>
                                                    Online
                                            </label>
                                        </div>
                                    </div>
                                </td>
                                <input type="hidden" name="order_existed[{{$key}}]" value="{{$order_existed[$key]}}">
                                @if($order_existed[$key]=='Y')
                                    <td data-label="Message">
                                        <button type="button" class="btn btn-outline-danger btn-sm btn-rounded" 
                                            data-tooltip="tooltip" data-placement="bottom" title="Order already generated for this product">
                                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody> 
                </table>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group" id="div_submit" style="@if(!isset($get_renewal_data[0]->customer_reponse))display:none @endif">
                        <div class="row">
                            <div class="col-md-4"></div>
                            <div class="col-md-4 text-right">
                                <button type="submit" class="btn btn-outline-success" name="btn_submit" id="btn_submit" value="show_submit">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>    
                <div class="col-md-4 text-right container-fluid">
                    <a href="{{ url()->previous()}}" class="btn btn-outline-primary">Back</a>
                </div>
            </div>
            <br>
        </form>
    </div>
    @endsection
</body>
    @section('script')
        <script>
            
            $(document).ready(function() {
                $(function () {
                    $('[data-tooltip="tooltip"]').tooltip()
                });
                $('#btn_edit').on('click',function() {
                    if($(this).val()=="Edit")
                    {
                        $('.edit_form_group').css('display', 'block');
                        $('.show_form_group').css('display', 'none');
                        $('.add_required').attr('required',true);
                        $('.edit_date').removeAttr('disabled');
                        $('.edit_payment').removeAttr('disabled');
                        $('#div_submit').css('display', 'block');
                        $(this).val('Cancel');
                        $('#btn_submit').val('edit_submit')
                    }
                    else
                    {
                        location.reload();
                        // $('.edit_form_group').css('display', 'none');
                        // $('.show_form_group').css('display', 'block');
                        // $('.add_required').attr('required',false);
                        // $(this).val('Edit');
                        // $('#div_submit').css('display', 'none');
                        // $('#btn_submit').val('show_submit')
                    }
                });
            });
              $('.btn_prod_status').on('click', function(){
                    let id = $(this).data('id');
                    let hid_date = $('#hidden_prod_date'+id).val();
                    if(this.value == 0){
                        //hide 
                        $('#date_cust_pickup_date'+id).val(hid_date);
                        $('#date_cust_pickup_date'+id).removeAttr('required');
                        $('#response_date_form_group_edit'+id).css('display', 'none');
                        // $('#cust_pickup_time'+id).val('');
                        // $('#cust_pickup_time'+id).removeAttr('required');
                        // $('#cust_pickup_time'+id).css('display', 'none');
                        //display
                        $('#cash_radio'+id).attr('required',true);
                        $('#cash_radio'+id).removeAttr('disabled');
                        $('#online_radio'+id).attr('required',true);
                        $('#online_radio'+id).removeAttr('disabled');
                        $('#payment_response_form_group_edit'+id).css('display', 'block');
                        $('#cash_radio'+id).val(0);
                        $('#online_radio'+id).val(1);
                    }
                    if(this.value == 1)
                    {   
                        //hide
                        $('#cash_radio'+id).removeAttr('required');
                        $('#online_radio'+id).removeAttr('required');
                        $('#response_date_form_group_edit'+id).css('display', 'block');
                        $('#payment_response_form_group_edit'+id).css('display', 'none');
                        $('#cash_radio'+id).val('');
                        $('#cash_radio'+id).attr('checked', 'checked');
                        $('#online_radio'+id).val('');
                        //show
                        $('#date_cust_pickup_date'+id).val(hid_date);
                        $('#date_cust_pickup_date'+id).attr('required',true);
                        $('#date_cust_pickup_date'+id).css('display', 'block');
                        // $('#cust_pickup_time'+id).val('');
                        // $('#cust_pickup_time'+id).attr('required',true);
                        // $('#cust_pickup_time'+id).css('display', 'block');
                    }
                    // if(this.value==2)
                    // {
                    //     //hide
                    //     $('#date_cust_pickup_date'+id).val('');
                    //     $('#date_cust_pickup_date'+id).removeAttr('required');
                    //     $('#date_cust_pickup_date'+id).css('display', 'none');
                    //     // $('#cust_pickup_time'+id).val('');
                    //     // $('#cust_pickup_time'+id).removeAttr('required');
                    //     // $('#cust_pickup_time'+id).css('display', 'none');
                    //     $('#cash_radio'+id).removeAttr('required');
                    //     $('#online_radio'+id).removeAttr('required');
                    //     $('#payment_response_form_group_edit'+id).css('display', 'none');
                    //     $('#cash_radio'+id).val('');
                    //     $('#cash_radio'+id).attr('checked', 'checked');
                    //     $('#online_radio'+id).val('null');
                    // }
                });
        </script>
    @endsection
</html>