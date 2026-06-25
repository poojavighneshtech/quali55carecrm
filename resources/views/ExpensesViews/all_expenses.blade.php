@extends('header_and_sidebar')
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        
        <title>All Expenses</title>
        <script src="{{url('/')}}/assets/dist/clipboard.min.js"></script>
        <!-- Boostrap 4 CSS -->
        @section('styles')
            <style>
                /* .rotated {
                    transform: rotate(90deg);
                    -ms-transform: rotate(90deg);
                    -moz-transform: rotate(90deg);
                    -webkit-transform: rotate(90deg);
                    -o-transform: rotate(90deg);
                }
                .img{transition: all 0.5s ease;} */
                .img-fluid {
                    max-width: auto;
                    height: auto;
                }
                .modal .modal-dialog{
                    width: fit-content;
                    max-width: 1335px;
                }

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

               
                /* .sf-icon{
                    width: 30px;
                    height: 30px;
                    margin: 10px;
                    opacity: 0.5;
                    display: block;
                }
                .sf-icon:hover{
                    opacity: 0.75;
                }
                .zoom-out{
                    background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23000' viewBox='0 0 512 512'%3E%3Cpath d='M304 192v32c0 6.6-5.4 12-12 12H124c-6.6 0-12-5.4-12-12v-32c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12zm201 284.7L476.7 505c-9.4 9.4-24.6 9.4-33.9 0L343 405.3c-4.5-4.5-7-10.6-7-17V372c-35.3 27.6-79.7 44-128 44C93.1 416 0 322.9 0 208S93.1 0 208 0s208 93.1 208 208c0 48.3-16.4 92.7-44 128h16.3c6.4 0 12.5 2.5 17 7l99.7 99.7c9.3 9.4 9.3 24.6 0 34zM344 208c0-75.2-60.8-136-136-136S72 132.8 72 208s60.8 136 136 136 136-60.8 136-136z'/%3E%3C/svg%3E");
                }
                .zoom-in{
                    background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23000' viewBox='0 0 512 512'%3E%3Cpath d='M304 192v32c0 6.6-5.4 12-12 12h-56v56c0 6.6-5.4 12-12 12h-32c-6.6 0-12-5.4-12-12v-56h-56c-6.6 0-12-5.4-12-12v-32c0-6.6 5.4-12 12-12h56v-56c0-6.6 5.4-12 12-12h32c6.6 0 12 5.4 12 12v56h56c6.6 0 12 5.4 12 12zm201 284.7L476.7 505c-9.4 9.4-24.6 9.4-33.9 0L343 405.3c-4.5-4.5-7-10.6-7-17V372c-35.3 27.6-79.7 44-128 44C93.1 416 0 322.9 0 208S93.1 0 208 0s208 93.1 208 208c0 48.3-16.4 92.7-44 128h16.3c6.4 0 12.5 2.5 17 7l99.7 99.7c9.3 9.4 9.3 24.6 0 34zM344 208c0-75.2-60.8-136-136-136S72 132.8 72 208s60.8 136 136 136 136-60.8 136-136z'/%3E%3C/svg%3E");
                }
                .rotate-left{
                    background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23000' viewBox='0 0 512 512'%3E%3Cpath d='M212.333 224.333H12c-6.627 0-12-5.373-12-12V12C0 5.373 5.373 0 12 0h48c6.627 0 12 5.373 12 12v78.112C117.773 39.279 184.26 7.47 258.175 8.007c136.906.994 246.448 111.623 246.157 248.532C504.041 393.258 393.12 504 256.333 504c-64.089 0-122.496-24.313-166.51-64.215-5.099-4.622-5.334-12.554-.467-17.42l33.967-33.967c4.474-4.474 11.662-4.717 16.401-.525C170.76 415.336 211.58 432 256.333 432c97.268 0 176-78.716 176-176 0-97.267-78.716-176-176-176-58.496 0-110.28 28.476-142.274 72.333h98.274c6.627 0 12 5.373 12 12v48c0 6.627-5.373 12-12 12z'/%3E%3C/svg%3E");
                }
                .rotate-right{
                    background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23000' viewBox='0 0 512 512'%3E%3Cpath d='M500.333 0h-47.411c-6.853 0-12.314 5.729-11.986 12.574l3.966 82.759C399.416 41.899 331.672 8 256.001 8 119.34 8 7.899 119.526 8 256.187 8.101 393.068 119.096 504 256 504c63.926 0 122.202-24.187 166.178-63.908 5.113-4.618 5.354-12.561.482-17.433l-33.971-33.971c-4.466-4.466-11.64-4.717-16.38-.543C341.308 415.448 300.606 432 256 432c-97.267 0-176-78.716-176-176 0-97.267 78.716-176 176-176 60.892 0 114.506 30.858 146.099 77.8l-101.525-4.865c-6.845-.328-12.574 5.133-12.574 11.986v47.411c0 6.627 5.373 12 12 12h200.333c6.627 0 12-5.373 12-12V12c0-6.627-5.373-12-12-12z'/%3E%3C/svg%3E");
                } */

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
                <center>All Expenses</center>
            </div> 
            <div class="container-fluid">
                <form action="{{url('/')}}/delivery_staff_expenses" method="get">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <label for="status">Status</label>
                            <select class="form-control form-control-sm selectpicker border" name="status" id="status" title="Status">
                                <option value="All" @if(request()->get('status')=='All') selected @else selected @endif>All</option>
                                <option value="Pending" @if(request()->get('status')=='Pending') selected @endif>Not Verified</option>
                                <option value="Verified" @if(request()->get('status')=='Verified') selected @endif>Not Settled</option>
                                <option value="Settled" @if(request()->get('status')=='Settled') selected @endif>Settled</option>
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="del_username">Delivery Staff</label>
                            {{-- <input type="text" class="form-control form-control-sm" name="del_username" id="del_username" placeholder="username.." 
                                value="@if(isset($filter['del_username'])){{$filter['del_username']}}@endif">--}}
                                <select class="form-control form-control-sm selectpicker border" name="selected_user" 
                                    id="selected_user" title="Select User" data-live-search="true" data-size="5">
                                    <option value="All" selected>All</option>
                                    @foreach ($getDelUsers as $key=>$deluser)
                                        <option value="{{$deluser->user_name}}" @if(request()->get('selected_user')==$deluser->user_name)selected @endif>{{$deluser->user_name}}</option>    
                                    @endforeach
                                </select>
                        </div>
                        <div class="col-md-3">
                            <label for="start_date">Start Date</label>
                            <input type="date" class="form-control form-control-sm" name="start_date" id="start_date" placeholder="Start Date"
                                value="@if(isset($filter['start_date'])){{$filter['start_date']}}@endif">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date">End Date</label>
                            <input type="date" class="form-control form-control-sm" name="end_date" id="end_date" placeholder="End Date"
                                value="@if(isset($filter['end_date'])){{$filter['end_date']}}@endif">
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="row mr-2 pt-2">
                            <div class="col-auto">
                                <button type="submit" class="btn btn-outline-success btn-sm btn-block">Submit</button>
                            </div>
                        </div>
                        <div class="row py-2">
                            <div class="col-auto">
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btn_clear">Clear</button>
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-outline-primary btn-sm" id="btn_add_cash">New Expense</button>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-outline-info btn-sm" name="submit" value="exportExcel">Export</button>
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-outline-dark btn-sm xml-export-btn"  value="exportExcel">Export XML</button>
                            </div>
                        </div>
                    </div>
                </form><br>
                <form action="{{url('/')}}/delivery_staff_expenses" method="get">
                    <input type="hidden" name="bulk_settle" value="bulk-settle">
                    <center>Bulk Settle Expenses.</center>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <select class="form-control form-control-sm selectpicker border" name="del_boy" 
                                id="del_boy" title="Select User" data-live-search="true" data-size="5" required>
                                <option value="All">All</option>
                                @foreach ($get_delivery_staff as $key=>$deluser)
                                    <option value="{{$deluser->username}}">{{$deluser->username}}</option>    
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <div class="row">
                                <label class="col-md-3" for="start_date_bulk">From Date</label>
                                <input type="date" class="form-control form-control-sm col-md-8" name="start_date_bulk" id="start_date_bulk" placeholder="Start Date"
                                value="{{date('Y-m-d',strtotime('-1 days'))}}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-outline-success btn-sm btn-block">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <div class="row justify-content-between">
                        <div class="col-auto">
                            <div class="row">
                                <div class="col-sm-auto">
                                    Total Labour : <strong>{{$totalLabour}}</strong>
                                </div>
                                <div class="col-sm-auto">
                                    Total Transport : <strong>{{$totalTransport}}</strong>
                                </div>
                                <div class="col-sm-auto">
                                    Total Expense : <strong>{{$totalExpense}}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <div class="table table-responsive jim-table-responsive">
                <table class="table table-hover">
                    <thead class="thead thead-light">
                        <th>Xml Check</th>
                        <th>Del Staff</th>
                        <th>Exp Date</th>
                        <th>Previous Balance</th>
                        <th>Off Cash</th>
                        <th></th>
                        <th>Labour</th>
                        <th>Exp</th>
                        <th>Lunch/Dinner</th>
                        <th>Monthly Pass</th>
                        <th>Trans</th>
                        <th>Depo Paid</th>
                        <th>Cust Received cash</th>
                        <th>Office Expenses</th>
                        <th>Fuel Expenses</th>
                        <th>Received Cash</th>
                        <th>Bal</th>
                        <th>Rec No</th>
                        <th>Rec Image</th>
                        <th>Stat/Act</th>
                        <th>Narr</th>
                        <th>Verified By</th>
                        <th>Verified At</th>
                        <th>Settled By</th>
                        <th>Settled At</th>
                        <th>Created At</th>
                        <th>Xml</th>
                        <th>Xml Generated At</th>
                    </thead>
                    <tbody>
                        @foreach($get_all_expenses as $key => $all_expense)
                            <tr  >
                                <td>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="xmlexpcheck{{$key}}" name="xmlcheckedids[]" value="{{$all_expense->id}}" @if($all_expense->status!='Settled') disabled @endif>
                                        <label class="custom-control-label" for="xmlexpcheck{{$key}}"></label>
                                    </div>
                                </td>
                                <td data-label="Del Staff">
                                    {{$all_expense->user_name}}
                                    <input type="hidden" name="username" id="username{{$key}}" value="{{$all_expense->user_name}}">
                                </td>
                                <td class="text-nowrap" data-label="Exp Date">
                                    {{$all_expense->exp_date}}
                                    <input type="hidden" name="expense_date" id="expenseDate{{$key}}" value="{{$all_expense->exp_date}}">
                                </td>
                                <td class="text-nowrap" data-label="Previous Cash">
                                    <div class="row  justify-content-end">
                                        <input type="text" class="form-control form-control-sm previous_cash" name="previous_cash" id="previous_cash{{$key}}" data-id="{{$key}}" style="width: 4rem" value="{{$all_expense->previous_balance}}" disabled>
                                    </div>
                                </td>
                                <td class="text-nowrap" data-label="Off Cash">
                                    <div class="row  justify-content-end">
                                        <input type="text" class="form-control form-control-sm cash_frm_off" name="cash_frm_off" id="cash_frm_off{{$key}}" data-id="{{$key}}" style="width: 4rem" value="{{$all_expense->cash_from_office}}" disabled>
                                    </div>
                                </td>
                                <td data-label="">
                                    @if($all_expense->status!='Settled')
                                        {{-- <button type="button" class="btn btn-outline-primary btn-sm btn_edit_cash"  
                                                    id="btn_edit_cash{{$key}}" data-id="{{$key}}" value="edit">
                                            <i class="fas fa-edit" id="add_btn_icon{{$key}}"></i>
                                        </button> --}}

                                        <button type="button" class="btn btn-outline-primary btn-sm btn_edit_expense"  
                                            id="btn_edit_expense{{$key}}" data-id="{{$key}}" value="edit">
                                            <i class="fas fa-edit" id="edit_expense_btn_icon{{$key}}"></i>
                                        </button>
                                    @endif
                                </td>
                                <td data-label="Labour">{{$all_expense->labour}}</td>
                                <td data-label="Expense">{{$all_expense->expenses}}</td>
                                <td data-label="Lunch Dinner">{{$all_expense->lunch_dinner}}</td>
                                <td data-label="Monthly Pass">{{$all_expense->monthly_pass}}</td>
                                <td data-label="Transport">{{$all_expense->transport}}</td>
                                <td data-label="Deposit Paisd">{{$all_expense->deposite_paid}}</td>
                                <td data-label="Cust Recieved Cash">{{$all_expense->cash_received_from_customer}}</td>
                                <td data-label="Office Expenses">{{$all_expense->office_expenses}}</td>
                                <td data-label="Fuel Expenses">{{$all_expense->fuel_expenses}}</td>
                                <td data-label="Recieved Cash"><span>{{($all_expense->received_cash)?$all_expense->received_cash:0}} </span></td>
                                <td class="text-dark bg-success" data-label="Balance Cash">
                                    {{$all_expense->balance_cash}}
                                    <input type="hidden" name="balance_cash{{$key}}" id="balance_cash{{$key}}" value="{{$all_expense->balance_cash}}">
                                    <input type="hidden" name="tbl_id{{$key}}" id="tbl_id{{$key}}" value="{{$all_expense->id}}">
                                </td>
                                <td data-label="Reciept No">{{$all_expense->receipt_no}}</td>
                                <td class="text-nowrap" data-label="Image">
                                    <button class="btn btn-sm btn-outline-primary viewReceipt" id="viewReceiptImage{{$key}}" data-id={{$key}}>
                                        <i class="fas fa-image"></i>
                                    </button>
                                    <input type="hidden" name="hidden_receipt_image" id="hidden_receipt_image{{$key}}" value="{{$all_expense->img_url}}">
                                    {{-- <button class="btn btn-sm btn-outline-primary generateReceipt" id="generateReceipt{{$key}}" data-id={{$key}}>
                                        <i class="fas fa-plus"></i>
                                    </button> --}}
                                    @if(date('Y-m-d',strtotime($all_expense->exp_date))>date('Y-m-d',strtotime('2023-01-02')))
                                        <a href="{{route('generate-expense')}}?exp_date={{$all_expense->exp_date}}&user={{$all_expense->user_name}}" target="_blank" class="btn btn-sm btn-outline-primary generateReceipt" id="generateReceipt{{$key}}" data-id={{$key}}>
                                            <i class="fas fa-plus"></i>
                                        </a>
                                    @endif
                                </td>
                                <td class="text-nowrap" data-label="Status">
                                    @if($all_expense->status=='Pending')
                                        {{-- <span class="badge badge-primary">Pending</span> --}}
                                        <button type="button" class="btn btn-outline-dark btn-sm recalculate" data-id="{{$key}}" data-tooltip="Calculate" title="Cal. Balance">
                                            <i class="fa fa-calculator" aria-hidden="true"></i>
                                        </button>
                                        @if(!in_array(session('user_id'),config('app.accounts_id_array')))
                                            <button type="button" class="btn btn-outline-primary btn-sm btn_verify" data-id="{{$key}}" data-expense_id = {{$all_expense->id}}>Verify</button>
                                        @endif
                                    @elseif($all_expense->status=='Settled')
                                        <span class="badge badge-success">Settled</span>
                                    @elseif($all_expense->status=='Verified')
                                        <button type="button" class="btn btn-outline-dark btn-sm recalculate" data-id="{{$key}}">
                                            <i class="fa fa-calculator" aria-hidden="true"></i>
                                        </button>
                                        @if(!in_array(session('user_id'),config('app.accounts_id_array')))
                                            <button type="button" class="btn btn-outline-danger btn-sm btn_unverify" data-id="{{$key}}">Unverify</button>
                                        @endif
                                        <button type="button" class="btn btn-outline-warning btn-sm btn_settle" data-id="{{$key}}">Settle</button>
                                    @endif
                                    <button type="button" class="btn btn-outline-warning btn-sm btn_details" id="{{$all_expense->id}}" data-user_name="{{$all_expense->user_name}}" data-exp_date="{{$all_expense->exp_date}}" onclick="expOrder('{{$all_expense->user_name}}','{{$all_expense->exp_date}}')">Details</button>
                                </td>
                                <td class="text-nowrap" data-label="Narr"> 
                                    <button type="button" class="btn btn-outline-secondary btn-sm btn_narration rounded-circle" data-id="{{$key}}"><i class="fa fa-comments" aria-hidden="true"></i></button>
                                    @if(isset($all_expense->comment))
                                        <input type="hidden" name="hid_comments" id="hid_comments{{$key}}" value="{{$all_expense->comment}}">
                                        <small>{{substr($all_expense->comment,'0','10')}}<span class="btn btn-default btn_view_comments" href="#" data-tooltip="tooltip" data-placement="bottom" title="View More" data-id="{{$key}}">...</span></small>
                                    @endif
                                </td>
                                <td data-label="verified By">{{$all_expense->verified_by}}</td>
                                <td class="text-nowrap" data-label="Verified At"> 
                                    @if($all_expense->status=='Verified' || $all_expense->status=='Settled')
                                        {{date('d-M-Y',strtotime(\Carbon\Carbon::parse($all_expense->verified_at)->toDateString()))}} 
                                        {{\Carbon\Carbon::parse($all_expense->verified_at)->format('g:i A')}}
                                    @endif
                                </td>
                                <td data-label="Settled By">{{$all_expense->settled_by}}</td>
                                <td class="text-nowrap" data-label="Settled At">
                                    @if($all_expense->status=='Settled')
                                        {{date('d-M-Y',strtotime(\Carbon\Carbon::parse($all_expense->settled_at)->toDateString()))}} 
                                        {{\Carbon\Carbon::parse($all_expense->settled_at)->format('g:i A')}}    
                                    @endif
                                </td>
                                <td class="text-nowrap" data-label="Create At">
                                    {{date('d-M-Y',strtotime(\Carbon\Carbon::parse($all_expense->created_at)->toDateString()))}} 
                                    {{\Carbon\Carbon::parse($all_expense->created_at)->format('g:i A')}}    
                                </td>
                                <td>
                                    <span class="badge badge-pill {{($all_expense->xml=='Y' ? 'badge-success' : 'badge-danger')}}">{{$all_expense->xml}}</span>
                                </td>
                                <td class="text-nowrap" data-label="Xml Generated At">
                                    @if($all_expense->xml_generated_at && $all_expense->xml=='Y')
                                        {{\Carbon\Carbon::parse($all_expense->xml_generated_at)->format('d-M-Y g:i A')}}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @php
                    $append_arr = array();
                    if(isset($filter['start_date'])){
                        $append_arr['start_date'] = $filter['start_date'];
                    }
                    if(isset($filter['end_date'])){
                        $append_arr['end_date'] = $filter['end_date'];
                    }
                @endphp
                {{$get_all_expenses->appends(request()->query())->links('Custom.Pagination.pagination')}}
            </div>
            {{-- <div class="row justify-content-center mb-2">
                <button class="btn btn-small btn-primary xml-export-btn">Export XML</button>
            </div> --}}
        </div>
        {{--All modals--}}
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
                        <div id="imageModalBody" data-rotation="0" class="rotate0">
                            <img class="img-fluid img rotate-right" src="" id="ReceiptImagePath" alt="No Image Found">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        {{--settle balance cash--}}
        <div class="modal fade" id="viewBalanceSettle" tabindex="-1" role="dialog" aria-labelledby="viewBalanceSettle" aria-hidden="true">
            <div class="modal-dialog modal modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewBalanceSettle">Settle Balance</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{url('/')}}/settle_expense" method="post">
                        @csrf
                        <div class="modal-body">                    
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="actual_bal">Remaining Balance</label>
                                    <input type="number" class="form-control" name="actual_bal" id="actual_bal" val="0" readonly>
                                    <input type="hidden" class="form-control" name="total_bal" id="total_bal" val="0">
                                    <input type="hidden" class="form-control" name="id" id="id">
                                    <input type="hidden" class="form-control" name="isin_negative" id="isin_negative" value="N">
                                    <br>
                                    <input type="number" class="form-control" name="received_bal" id="received_bal" placeholder="Balance Received..." value="0" required>
                                    <br>
                                    <textarea name="narration" class="form-control" id="narration" cols="30" rows="5" placeholder="Narration.."></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-outline-primary btn-sm">Submit</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal"><i class="fa fa-window-close" aria-hidden="true"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        {{--only narration modal--}}
        <div class="modal fade" id="viewNarrationModal" tabindex="-1" role="dialog" aria-labelledby="viewNarrationModal" aria-hidden="true">
            <div class="modal-dialog modal modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewNarrationModal">Narration</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{url('/')}}/narration_updt" method="post">
                        @csrf
                        <div class="modal-body">                    
                            <div class="row">
                                <div class="col-md-12">
                                    <input type="hidden" class="form-control" name="narr_dexp_id" id="narr_dexp_id">
                                    <textarea name="narration" class="form-control" id="narration" cols="30" rows="5" placeholder="Narration.." required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-outline-primary btn-sm">Submit</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal"><i class="fa fa-window-close" aria-hidden="true"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{--Add cash modal--}}
        <div class="modal fade" id="viewAddCashModal" tabindex="-1" role="dialog" aria-labelledby="viewAddCashModal" aria-hidden="true">
            <div class="modal-dialog modal modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewAddCashModal">Add Cash</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{url('/')}}/add_cash" method="post">
                        @csrf
                        <div class="modal-body">                    
                           <div class="container-fluid">
                               <div class="row">
                                   <label for="add_cash_date"><strong>Date</strong></label>
                                   <input type="date" class="form-control" name="add_cash_date" id="add_cash_date" value="{{date('Y-m-d')}}">
                               </div>
                               <div class="row">
                                    <label for="add_del_boy"><strong>Delivery Staff</strong></label>
                                    <select class="form-control selectpicker border" name="add_del_boy" id="add_del_boy" data-size="5" data-live-search="true" data-width="100%">
                                        @foreach ($get_delivery_staff as $key=>$delboys)
                                            <option value="{{$delboys->username}}">{{$delboys->username}}</option>
                                        @endforeach
                                    </select>
                               </div>
                               <div class="row">
                                    <label for="add_cash_amount"><strong>Amount</strong></label>
                                    <input type="number" class="form-control" name="add_cash_amount" id="add_cash_amount">
                               </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-outline-primary btn-sm">Submit</button>
                            <button type="button" class="btn btn-outline-danger btn-sm" data-dismiss="modal"><i class="fa fa-window-close" aria-hidden="true"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- show comments in modal --}}
        <div class="modal fade" id="viewAllCommentsModal" tabindex="-1" role="dialog" aria-labelledby="viewAllCommentsModal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewAllCommentsModal">Comments</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <pre id="mod_view_comments"></pre>
                    </div>
                </div>
            </div>
        </div>

        {{-- edit Details of expense --}}
        <div class="modal fade" id="viewEditExpenseModal" tabindex="-1" role="dialog" aria-labelledby="viewEditExpenseModal" aria-hidden="true">
            <div class="modal-lg modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewEditExpenseModal">Edit Expense</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{url('/')}}/update_expense" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">                    
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="row">
                                        <div class="col-auto">
                                            <label for="selected_expense_date"><strong>Date :</strong></label>
                                            {{-- hidden values --}}
                                            <input type="hidden" name="expense_id" id="expenseId">
                                        </div>
                                        <div class="col-auto">
                                            <span id="selectedDateSpan"></span>
                                            <input type="hidden" name="expense_date" id="selectedDateHidden_expmodal">
                                        </div>
                                        <div class="col-auto">
                                            <label for="del_user_name"><strong>Staff Name :</strong></label>
                                            <input type="hidden" name="del_user_name" id="delUserNameInput_expmodal">
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
                                    </div>
                                </div>
                                <div class="col-md-2 text-right">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="holiday" id="holidayCheckbox">
                                        <label class="custom-control-label" for="holidayCheckbox">Holiday</label>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <label for="cash_rec_frm_off">Cash received from office : </label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="number" class="form-control form-control-sm calculateExp" name="cash_received_from_office" id="cashRecFrmOff_expmodal" value="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <label for="cash_rec_frm_off">Cash received from customer : </label>
                                        </div>
                                        <div class="col-md-6 justify-content-left">
                                            <input type="number" class="form-control form-control-sm calculateExp" name="cash_received_from_customer" id="cashRecFrmCust_expmodal" value="0" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <label for="transport">Transport : </label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="number" class="form-control form-control-sm calculateExp" name="transport" id="transport_expmodal" value="0" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <label for="cash_rec_frm_off">Actual Deposit returned : </label>
                                        </div>
                                        <div class="col-md-6 justify-content-left">
                                            <input type="number" class="form-control form-control-sm calculateExp" name="actual_deposit_returned" id="actualDepositReturned_expmodal" value="0" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <label for="expense">Expense : </label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="number" class="form-control form-control-sm calculateExp" name="expense" id="expense_expmodal" value="0" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <label for="labour_charge">Labour Charges : </label>
                                        </div>
                                        <div class="col-md-6 justify-content-left">
                                            <input type="number" class="form-control form-control-sm calculateExp" name="labour_charges" id="labourCharges_expmodal" value="0" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <label for="lunch_dinner">Lunch/Dinner : </label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="number" class="form-control form-control-sm calculateExp" name="lunch_dinner" id="lunch_dinner_expmodal" value="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <label for="monthly_pass">Monthly Pass : </label>
                                        </div>
                                        <div class="col-md-6 justify-content-left">
                                            <input type="number" class="form-control form-control-sm calculateExp" name="monthly_pass" id="monthly_pass_expmodal" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <label for="fuel_expense">Fuel Expenses : </label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="number" class="form-control form-control-sm calculateExp" name="fuel_expenses" id="fuel_expenses_expmodal" value="0">
                                        </div>
                                    </div>
                                </div>
                            {{-- </div>
                            <div class="row form-group"> --}}
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <label for="office_expense">Office Expenses : </label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="number" class="form-control form-control-sm calculateExp" name="office_expenses" id="office_expenses_expmodal" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- <br>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <label for="receipt_no">Receipt No : </label>
                                        </div>
                                        <div class="col-md-6 justify-content-left">
                                            <input type="text" class="form-control form-control-sm" name="receipt_no" id="receiptNo_expmodal" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <label for="balance_cash">Balance Cash : </label>
                                        </div>
                                        <div class="col-md-6">
                                            <span id="balanceCash_span_expmodal">0</span>
                                            <input type="hidden" name="balance_cash" id="balanceCash_input_expmodal" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
                            <br>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <label for="receipt_no">Receipt No : </label>
                                        </div>
                                        <div class="col-md-6 justify-content-left">
                                            <input type="text" class="form-control form-control-sm" name="receipt_no" id="receiptNo_expmodal" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <label for="balance_cash">Balance Cash : </label>
                                        </div>
                                        <div class="col-md-6">
                                            <span id="balanceCash_span_expmodal">0</span>
                                            <input type="hidden" name="balance_cash" id="balanceCash_input_expmodal" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row justify-content-center">
                                <div class="col-md-8">
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
                                    {{-- <button type="button" class="btn btn-sm btn-outline-primary" id="viewReceiptImageBtn" role="button" data-toggle="popover" data-trigger="focus" title="Dismissible popover" data-content="">
                                        <i class="fas fa-image"></i>
                                    </button> --}}
                                    {{-- <a tabindex="0" class="btn btn-lg btn-danger" role="button" id="viewReceiptImageBtn" data-toggle="popover" data-trigger="focus" title="Dismissible popover" data-content="hdhfhg"><i class="fas fa-image"></i></a> --}}
                                    <input type="hidden" name="hidden_receipt_image" id="hidden_receipt_image">
                                </div>
                                
                            </div>
                            {{-- <div class="row justify-content-center">
                                <button type="submit" class="btn btn-outline-success">Submit</button>
                            </div> --}}
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-outline-primary btn-sm">Submit</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal"><i class="fa fa-window-close" aria-hidden="true"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- unverify modal comment --}}
        <div class="modal fade" id="viewUnverifyCommentModal" tabindex="-1" role="dialog" aria-labelledby="viewUnverifyCommentModal" aria-hidden="true">
            <div class="modal-dialog modal modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewUnverifyCommentModal">Narration</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{url('/')}}/unverify_exp" method="post">
                        @csrf
                        <div class="modal-body">                    
                            <div class="row">
                                <div class="col-md-12">
                                    <input type="hidden" class="form-control" name="unverify_exp_id" id="unverify_exp_id">
                                    <textarea name="unverify_comment" class="form-control" id="unverify_comment" cols="30" rows="5" placeholder="Comment.." required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-outline-primary btn-sm">Submit</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal"><i class="fa fa-window-close" aria-hidden="true"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="orderExpensesModal" tabindex="-1" role="dialog" aria-labelledby="orderExpensesModal" aria-hidden="true">
            <div class="modal-dialog modal modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="orderExpensesModal">Order Expenses</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>                    
                    <div class="modal-body orderExpenses">                    
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="orderTransModeModal" tabindex="-1" role="dialog" aria-labelledby="orderTransModeModal" aria-hidden="true">
            <div class="modal-dialog modal modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="orderTransModeModal">Order Trans Mode</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>                    
                    <div class="modal-body transmode">                    
                        <input type="hidden" name="transmode_order_id" id="transmode_order_id">
                        <select class="select selectpicker border" title="select transport mode" name="transmode[]" id="transmode" multiple="true" data-live-search="true" >
                            <option value="Petrol Charges">Petrol-Charges</option>
                            <option value="Boat">Boat</option>
                            <option value="Bike">Bike</option>
                            <option value="Metro">Metro</option>
                            <option value="OLA">OLA</option>
                            <option value="Rickshaw">Rickshaw</option>
                            <option value="Taxi">Taxi</option>
                            <option value="Tempo">Tempo</option>
                            <option value="Train">Train</option>
                            <option value="Uber">Uber</option>
                            <option value="Others">Others</option>
                        </select>
                        <button class="btn btn-outline-success btn_submit_transmode">Submit</button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    @endsection
    @section('script')
        <script>
            $(".viewReceipt").click(function() {
                let id=$(this).data("id");
                let path = $("#hidden_receipt_image"+id).val();
                if(path.includes("http://")){
                    $("#ReceiptImagePath").attr("src",path);
                }else{
                    $("#ReceiptImagePath").attr("src","http://"+path);
                }
                $("#viewReceiptImage").modal('show');
            });
            $('#btn_clear').on('click',function(){
                window.location.href = "{{url('/')}}"+"/delivery_staff_expenses";
            });
            $('.btn_settle').on('click',function(){
                let data_id=$(this).data("id");
                let bal_cash = $('#balance_cash'+data_id).val();
                let id = $('#tbl_id'+data_id).val();
                $('#actual_bal').val(bal_cash);
                $('#total_bal').val(bal_cash);
                $('#id').val(id);
                $("#viewBalanceSettle").modal('show');
            });
            $('.btn_verify').click(function(){
                let expense_id = $(this).data('expense_id');
                $.ajax({
                    type:"GET",
                    url:"{{url('/')}}/verify_expense/"+expense_id,
                    cache:false,
                    success:function(data){
                        location.reload();
                    }
                });
            });
            $('#received_bal').on('input',function(){
                let act_val = $('#total_bal').val();
                let act_val_abs = Math.abs(act_val);
                let rec_val = $(this).val();
                let total = act_val_abs - rec_val;
                if(act_val<0){
                    total = -(total);
                    $('#isin_negative').val('Y');
                }
                $('#actual_bal').val(total);
            });

            $('.btn_narration').on('click',function(){
                let data_id=$(this).data("id");
                let get_tbl_id = $('#tbl_id'+data_id).val();
                let id = $('#narr_dexp_id').val(get_tbl_id);
                $("#viewNarrationModal").modal('show');
            })
            $(document).ready(function(){
                $('#btn_add_cash').on('click',function(){
                    $('#add_del_boy').selectpicker('refresh');
                    $("#viewAddCashModal").modal('show');
                });
            });
            
            // $('.btn_edit_cash').on('click',function(){
            //     let data_id = $(this).data('id');
            //     let data_id = $(this).data('id');
            //     let id = $('#tbl_id'+data_id).val();
            //     let edited_cash = $('#cash_frm_off'+data_id).val();
            //     if($(this).val()=='edit'){
            //         $('#add_btn_icon'+data_id).removeClass('fas fa-edit');
            //         $('#add_btn_icon'+data_id).addClass('fa fa-check');
            //         $('#cash_frm_off'+data_id).removeAttr('disabled');
            //         $(this).val('update');
            //     }else{
            //         $(this).val('edit');
            //         $('#add_btn_icon'+data_id).addClass('fas fa-edit');
            //         $('#add_btn_icon'+data_id).removeClass('fa fa-check');
            //         $('#cash_frm_off'+data_id).attr('disabled',true);
            //         var dataString = ({_token:"{{ csrf_token() }}",id:""+id,edited_cash:""+edited_cash});
            //         $.ajax({
            //             type: "POST",
            //             url: "{{url('/')}}/update_cash",
            //             data: dataString,
            //             cache: false,
            //             success: function (data) {
            //                 //console.log(data);
            //                 location.reload();
            //             }
            //         });
            //     }
            // });

            $('.btn_edit_expense').on('click',function(){
                let data_id = $(this).data('id');
                let username = $('#username'+data_id).val();
                let expenseDate = $('#expenseDate'+data_id).val();
                var dataString = ({_token:"{{ csrf_token() }}",del_user:""+username,date:""+expenseDate});
                let total = 0;
                $.ajax({
                    type: "POST",
                    url: "{{url('/')}}/check_prev_bal",
                    data: dataString,
                    cache: false,
                    success: function (data) {
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
                $('#selectedDateSpan').text(expenseDate);
                $('#selectedDateHidden_expmodal').val(expenseDate);
                $('#delUserNameSpan').text(username);
                $('#delUserNameInput_expmodal').val(username);
                $('#cashCarriedForward_span').text(total);

                //get expense details
                let cash_rec_frm_off = 0;
                let cash_rec_frm_cust = 0;
                let transport = 0;
                let actual_depo_return = 0;
                let expense = 0;
                let labour_charge = 0;
                let receipt_no = null;
                let balance_cash = 0;
                var dataString = ({_token:"{{ csrf_token() }}",username:""+username,expense_date:""+expenseDate});
                $.ajax({
                    type: "POST",
                    url: "{{url('/')}}/get_expense_data",
                    data: dataString,
                    cache: false,
                    success: function (data) {
                        $('#expenseId').val(data.id);
                        $('#cashRecFrmOff_expmodal').val(data.cash_from_office);
                        $('#cashRecFrmCust_expmodal').val(data.cash_received_from_customer);
                        $('#actualDepositReturned_expmodal').val(data.deposite_paid);
                        $('#expense_expmodal').val(data.expenses);
                        $('#labourCharges_expmodal').val(data.labour);
                        $('#lunch_dinner_expmodal').val(data.lunch_dinner);
                        $('#monthly_pass_expmodal').val(data.monthly_pass);
                        $('#office_expenses_expmodal').val(data.office_expenses);
                        $('#fuel_expenses_expmodal').val(data.fuel_expenses);
                        $('#receiptNo_expmodal').val(data.receipt_no);
                        $('#balanceCash_span_expmodal').text(data.balance_cash);
                        $('#balanceCash_input_expmodal').val(data.balance_cash);
                        $('#transport_expmodal').val(data.transport);
                    }
                });
                $("#viewEditExpenseModal").modal('show');
            });

            $('.calculateExp').on('input',function(){
                let carriedBal = $('#cashCarriedForward_input').val();
                let cashRecFrmOff = $('#cashRecFrmOff_expmodal').val();
                let cashRecFrmCust = $('#cashRecFrmCust_expmodal').val();
                let transport = $('#transport_expmodal').val();
                let actualDepositReturned = $('#actualDepositReturned_expmodal').val();
                let expense = $('#expense_expmodal').val();
                let labourCharges = $('#labourCharges_expmodal').val();
                let office_expenses = $('#office_expenses_expmodal').val();
                let fuel_expenses = $('#fuel_expenses_expmodal').val();
                let lunchDinner = $('#lunch_dinner_expmodal').val();
                let monthlyPass = $('#monthly_pass_expmodal').val();
                let total = carriedBal;

                total = parseInt(carriedBal)+parseInt(cashRecFrmOff)+parseInt(cashRecFrmCust)-parseInt(transport)-parseInt(actualDepositReturned)-parseInt(expense)-parseInt(labourCharges)-parseInt(office_expenses)-parseInt(fuel_expenses)-parseInt(lunchDinner)-parseInt(monthlyPass);
                $('#balanceCash_span_expmodal').text(total);
                $('#balanceCash_input_expmodal').val(total);
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

            let path = "";
            $('#expenseReceiptImageFile').on('change',function(e){
                let image = document.getElementById('expenseReceiptImageFile');
	                path = URL.createObjectURL(event.target.files[0]);
                //$("#ReceiptImagePath").attr("src", path);
                $('#viewImage').attr('src',path);
            });
            $('[data-toggle="popover"]').popover({
                placement : 'top',
                trigger : 'click',
                html : true,
                content : '<div class="media"><img src="'+path+'" class="mr-3" alt="Sample Image">'
            });
            // $("#viewReceiptImageBtn").click(function() {
            //     let id = $(this).data("id");
            //     //let path = $('#expenseReceiptImageFile').val();
            //     //let path = $("#hidden_receipt_image"+id).val();
            //     $("#ReceiptImagePath").attr("src", path);
            //     $("#viewReceiptImage").modal('show');
            // });


            $('.btn_view_comments').on('click',function(){
                let data_id=$(this).data("id");
                let comments = $('#hid_comments'+data_id).val();
                $('#mod_view_comments').text(comments);
                $("#viewAllCommentsModal").modal('show');
            });
            $('.btn_unverify').on('click',function(){
                let data_id = $(this).data('id');
                let username = $('#username'+data_id).val();
                let expenseDate = $('#expenseDate'+data_id).val();
                let id = $('#tbl_id'+data_id).val();
                console.log(id);
                $('#unverify_exp_id').val(id);
                $("#viewUnverifyCommentModal").modal('show');
            });

            $('.recalculate').on('click',function(){
                let data_id = $(this).data('id');
                let id = $('#tbl_id'+data_id).val();
                let userName = $('#username'+data_id).val();
                let expenseDate = $('#expenseDate'+data_id).val();
                var dataString = ({_token:"{{ csrf_token() }}",id:""+id,username:""+userName,exp_date:""+expenseDate});
                $.ajax({
                    type: "POST",
                    url: "{{url('/')}}/recalculate_exp",
                    data: dataString,
                    cache: false,
                    success: function (data) {
                        //console.log(data);
                        location.reload();
                    }
                });
                
            });

            //rotate image
            $(function(){
                var zoomStep = 50;
                var rotateStep = 90;
                function rotate(step){
                    var container = $('#imageModalBody');
                    var rotation = parseInt(container.attr('data-rotation'));
                    var newRotation = rotation + step;

                    newRotation = newRotation < 0 ? (newRotation + 360) % 360 : newRotation % 360;
                    container.removeClass('rotate' + rotation);
                    container.addClass('rotate' + newRotation);
                    container.attr('data-rotation', newRotation);
                    setImageContainerDimensions();
                }
                function setImageContainerDimensions(){
                    var container = $('#imageModalBody')
                    var image = container.find('.img-fluid');
                    rotation = parseInt(container.attr('data-rotation'));
                    if (rotation % 180 == 90) {
                        container.width(image.height() + 17).height(image.width());
                    }
                    else {
                        container.width(image.width() + 17).height(image.height());
                    }
                }
            
                $('.rotate-right').on('click', function(){
                        rotate(rotateStep);
                });
                
            });
            // $('.btn_details').on('click', function () {
            //     // alert("username"+this.dataset.user_name+" - exp_date"+this.dataset.exp_date);
            //     var dataString = ({_token:"{{ csrf_token() }}",id:""+id,username:""+this.dataset.user_name,exp_date:""+this.dataset.exp_date});
            //     $.ajax({
            //         type: "POST",
            //         url: "{{url('/')}}/get-order-exp",
            //         data: dataString,
            //         cache: false,
            //         success: function (data) {
            //             console.log(data.length);
            //             $("#orderExpensesModal").modal("show");
            //             $(".orderExpenses").empty();
            //             if(data.length !=0)
            //             {
            //                 let table = "<div class='table table-responsive jim-table-responsive'><table class='table' id='table_order_exp'>";
            //                     table += "<thead>";
            //                         table += "<tr>";
            //                             table += "<th>Order Type</th>";
            //                             table += "<th>Order Id</th>";
            //                             table += "<th>Customer Name</th>";
            //                             table += "<th>Contact No</th>";
            //                             table += "<th>Cash Rec. from cust.</th>";
            //                             table += "<th>Deposit Refund</th>";
            //                             table += "<th>Transport</th>";
            //                             table += "<th>Expenses</th>";
            //                             table += "<th>Labour Charges</th>";
            //                             table += "<th>Mode of TR</th>";
            //                         table += "</tr>";
            //                     table += "</thead>";
            //                     table += "<tbody>";
            //                         for(let i=0; i<data.length; i++){
            //                             table += "<tr>";
            //                                 table += "<td>"+data[0].deliverypickup+"</td>";
            //                                 table += "<td>"+data[i].order_id+"</td>";
            //                                 table += "<td>"+data[i].shipping_first_name+"</td>";
            //                                 table += "<td>"+data[i].mobileno+"</td>";
            //                                 if(data[i].del_cash_rec_from_cust != null){

            //                                     table += "<td>"+data[i].del_cash_rec_from_cust+"</td>";
            //                                 }
            //                                 else{
            //                                     table += "<td>0</td>";
            //                                 }
            //                                 if(data[i].del_depo_returned != null){
            //                                     table += "<td>"+data[i].del_depo_returned+"</td>";
            //                                 }
            //                                 else{
            //                                     table += "<td>0</td>";
            //                                 }
            //                                 if(data[i].del_transport != null){
            //                                     table += "<td>"+data[i].del_transport+"</td>";
            //                                 }
            //                                 else{
            //                                     table += "<td>0</td>";
            //                                 }
            //                                 if(data[i].del_expenses != null){
            //                                     table += "<td>"+data[i].del_expenses+"</td>";
            //                                 }
            //                                 else{
            //                                     table += "<td>0</td>";
            //                                 }
            //                                 if(data[i].del_labour_charges != null){
            //                                     table += "<td>"+data[i].del_labour_charges+"</td>";
            //                                 }
            //                                 else{
            //                                     table += "<td>0</td>";
            //                                 }
            //                                 if(data[i].del_transport_mode != null){
            //                                     // table += "<td>"+data[i].del_transport_mode+"</td>";
            //                                     let array_data = JSON.parse(data[i].del_transport_mode);
            //                                     array_data.join(',');
            //                                     table += "<td>"+array_data+"<button class='btn btn-outline-primary btn-sm btn_edit_trans_mode' data-order_id='"+data[i].order_id+"' id='edit_trans_mode"+i+"' data-toggle='tooltip' data-placement='bottom' title='Edit Transport Mode' data-trans='"+data[i].del_transport_mode+"' onclick='edit_trans_mode(this.dataset.order_id,this.dataset.trans);'><i class='fa fa-edit' aria-hidden='true'></i></button></td>";
            //                                 }
            //                                 else{
            //                                     table += "<td>-<button class='btn btn-outline-primary btn_edit_trans_mode btn-sm' data-order_id='"+data[i].order_id+"' id='edit_trans_mode"+i+"' data-toggle='tooltip' data-placement='bottom' title='Edit Transport Mode'  onclick='edit_trans_mode(this.dataset.order_id);'><i class='fa fa-edit' aria-hidden='true'></i></button></td>";
            //                                 }   
            //                             table += "</tr>";
            //                         }
            //                     table += "</body>";
            //                 table += "</table>";
            //                 $(".orderExpenses").append(table);
            //                 $("#table_order_exp").dataTable();
            //             }
            //             else{
            //                 let data = "<h4>No Orders Found</h4>";
            //                 $(".orderExpenses").append(data);
            //             }
                        
            //         }
            //     });
            // });


            // $('.btn_details').on('click', function () {
            //     // alert("username"+this.dataset.user_name+" - exp_date"+this.dataset.exp_date);
            //     var dataString = ({_token:"{{ csrf_token() }}",id:""+id,username:""+this.dataset.user_name,exp_date:""+this.dataset.exp_date});
            //     $.ajax({
            //         type: "POST",
            //         url: "{{url('/')}}/get-order-exp",
            //         data: dataString,
            //         cache: false,
            //         success: function (data) {
            //             console.log(data);
            //             $("#orderExpensesModal").modal("show");
            //             $(".orderExpenses").empty();
            //             let table = "<div class='table table-responsive jim-table-responsive'><table class='table' id='table_order_exp'>";
            //                     table += "<thead>";
            //                         table += "<tr>";
            //                             table += "<th>Order Type</th>";
            //                             table += "<th>Order Id</th>";
            //                             table += "<th>Customer Name</th>";
            //                             table += "<th>Patient Name</th>";
            //                             table += "<th>Location</th>";
            //                             table += "<th>Contact No</th>";
            //                             table += "<th>Cash Rec. from cust.</th>";
            //                             table += "<th>Deposit Refund</th>";
            //                             table += "<th>Transport</th>";
            //                             table += "<th>Expenses</th>";
            //                             table += "<th>Labour Charges</th>";
            //                             table += "<th>Mode of TR</th>";
            //                         table += "</tr>";
            //                     table += "</thead>";
            //                     table += "<tbody>";
            //                         for(let i=0; i<data.length; i++){
            //                             table += "<tr>";
            //                                 table += "<td>"+data[0].deliverypickup+"</td>";
            //                                 table += "<td>"+data[i].order_id+"</td>";
            //                                 table += "<td>"+data[i].shipping_first_name+"</td>";
            //                                 table += "<td>"+data[i].patient_name+"</td>";
            //                                 table += "<td>"+data[i].location+"</td>";
            //                                 table += "<td>"+data[i].mobileno+"</td>";
            //                                 if(data[i].cash_rec_from_cust != null){

            //                                     table += "<td>"+data[i].cash_rec_from_cust+"</td>";
            //                                 }
            //                                 else{
            //                                     table += "<td>0</td>";
            //                                 }
            //                                 if(data[i].depo_returned != null){
            //                                     table += "<td>"+data[i].depo_returned+"</td>";
            //                                 }
            //                                 else{
            //                                     table += "<td>0</td>";
            //                                 }
            //                                 if(data[i].transport != null){
            //                                     table += "<td>"+data[i].transport+"</td>";
            //                                 }
            //                                 else{
            //                                     table += "<td>0</td>";
            //                                 }
            //                                 if(data[i].hardware_expenses != null){
            //                                     table += "<td>"+data[i].hardware_expenses+"</td>";
            //                                 }
            //                                 else{
            //                                     table += "<td>0</td>";
            //                                 }
            //                                 if(data[i].labour != null){
            //                                     table += "<td>"+data[i].labour+"</td>";
            //                                 }
            //                                 else{
            //                                     table += "<td>0</td>";
            //                                 }
            //                                 if(data[i].del_transport_mode != null){
            //                                     // table += "<td>"+data[i].del_transport_mode+"</td>";
            //                                     let array_data = JSON.parse(data[i].del_transport_mode);
            //                                     array_data.join(',');
            //                                     table += "<td>"+array_data+"<button class='btn btn-outline-primary btn-sm btn_edit_trans_mode' data-order_id='"+data[i].order_id+"' id='edit_trans_mode"+i+"' data-toggle='tooltip' data-placement='bottom' title='Edit Transport Mode' data-trans='"+data[i].del_transport_mode+"' onclick='edit_trans_mode(this.dataset.order_id,this.dataset.trans);'><i class='fa fa-edit' aria-hidden='true'></i></button></td>";
            //                                 }
            //                                 else{
            //                                     table += "<td>-<button class='btn btn-outline-primary btn_edit_trans_mode btn-sm' data-order_id='"+data[i].order_id+"' id='edit_trans_mode"+i+"' data-toggle='tooltip' data-placement='bottom' title='Edit Transport Mode'  onclick='edit_trans_mode(this.dataset.order_id);'><i class='fa fa-edit' aria-hidden='true'></i></button></td>";
            //                                 }                                            
            //                             table += "</tr>";
            //                         }
            //                     table += "</body>";
            //                 table += "</table>";
            //             $(".orderExpenses").append(table);
            //             $("#table_order_exp").dataTable();
            //         }
            //     });
            // });
            
            function edit_trans_mode(order_id,trans){
                console.log(trans);
                if(trans != undefined){
                    $("#transmode").val(JSON.parse(trans));
                }
                else{
                    $("#transmode").val(null);
                }
                $("#orderTransModeModal").modal("show");
                $("#transmode_order_id").val(order_id);
                
                $("#transmode").selectpicker("refresh");
            }
            var userNameG = null;
            var expDateG = null;
            function expOrder(userName,expDate){
                expDateG = expDate;
                userNameG = userName;
                console.log(expDate,userName);
                var dataString = ({_token:"{{ csrf_token() }}",id:""+id,username:""+userName,exp_date:""+expDate});
                $.ajax({
                    type: "POST",
                    url: "{{url('/')}}/get-order-exp",
                    data: dataString,
                    cache: false,
                    success: function (data) {
                        console.log(data);
                        $("#orderExpensesModal").modal("show");
                        $(".orderExpenses").empty();
                        let table = "<div class='table table-responsive jim-table-responsive'><table class='table' id='table_order_exp'>";
                                table += "<thead>";
                                    table += "<tr>";
                                        table += "<th>Order Type</th>";
                                        table += "<th>Order Id</th>";
                                        table += "<th>Customer Name</th>";
                                        table += "<th>Patient Name</th>";
                                        table += "<th>Location</th>";
                                        table += "<th>Contact No</th>";
                                        table += "<th>Cash Rec. from cust.</th>";
                                        table += "<th>Deposit Refund</th>";
                                        table += "<th>Transport</th>";
                                        table += "<th>Expenses</th>";
                                        table += "<th>Labour Charges</th>";
                                        table += "<th>Mode of TR</th>";
                                    table += "</tr>";
                                table += "</thead>";
                                table += "<tbody>";
                                    for(let i=0; i<data.length; i++){
                                        table += "<tr>";
                                            table += "<td>"+data[0].deliverypickup+"</td>";
                                            table += "<td>"+data[i].order_id+"</td>";
                                            table += "<td>"+data[i].shipping_first_name+"</td>";
                                            table += "<td>"+data[i].patient_name+"</td>";
                                            table += "<td>"+data[i].location+"</td>";
                                            table += "<td>"+data[i].mobileno+"</td>";
                                            if(data[i].cash_rec_from_cust != null){

                                                table += "<td>"+data[i].cash_rec_from_cust+"</td>";
                                            }
                                            else{
                                                table += "<td>0</td>";
                                            }
                                            if(data[i].depo_returned != null){
                                                table += "<td>"+data[i].depo_returned+"</td>";
                                            }
                                            else{
                                                table += "<td>0</td>";
                                            }
                                            if(data[i].transport != null){
                                                table += "<td>"+data[i].transport+"</td>";
                                            }
                                            else{
                                                table += "<td>0</td>";
                                            }
                                            if(data[i].hardware_expenses != null){
                                                table += "<td>"+data[i].hardware_expenses+"</td>";
                                            }
                                            else{
                                                table += "<td>0</td>";
                                            }
                                            if(data[i].labour != null){
                                                table += "<td>"+data[i].labour+"</td>";
                                            }
                                            else{
                                                table += "<td>0</td>";
                                            }
                                            if(data[i].transport_medium != null){
                                                // table += "<td>"+data[i].transport_medium+"</td>";
                                                let array_data = JSON.parse(data[i].transport_medium);
                                                array_data.join(',');
                                                table += "<td>"+array_data+"<button class='btn btn-outline-primary btn-sm btn_edit_trans_mode' data-order_id='"+data[i].id+"' id='edit_trans_mode"+i+"' data-toggle='tooltip' data-placement='bottom' title='Edit Transport Mode' data-trans='"+data[i].transport_medium+"' onclick='edit_trans_mode(this.dataset.order_id,this.dataset.trans);'><i class='fa fa-edit' aria-hidden='true'></i></button></td>";
                                            }
                                            else{
                                                table += "<td>-<button class='btn btn-outline-primary btn_edit_trans_mode btn-sm' data-order_id='"+data[i].id+"' id='edit_trans_mode"+i+"' data-toggle='tooltip' data-placement='bottom' title='Edit Transport Mode'  onclick='edit_trans_mode(this.dataset.order_id);'><i class='fa fa-edit' aria-hidden='true'></i></button></td>";
                                            }                                            
                                        table += "</tr>";
                                    }
                                table += "</body>";
                            table += "</table>";
                        $(".orderExpenses").append(table);
                        $("#table_order_exp").dataTable();
                    }
                });
            }

            $(".btn_submit_transmode").click(function(){
                // console.log($("#transmode").val());
                if($("#transmode").val() == null || $("#transmode").val().length == 0)
                {
                    // console.log("empty");
                    $(".border").removeClass('border-success');
                    $(".border").addClass('border-danger');
                }
                else{
                    $(".border").removeClass('border-danger');
                    $(".border").addClass('border-success');
                    let order_id = $("#transmode_order_id").val();
                    let transmodes = $("#transmode").val();
                    var dataString = ({_token:"{{ csrf_token() }}",order_id:""+order_id,transmodes:""+transmodes});
                    $.ajax({
                    type: "POST",
                    url: "{{url('/')}}/update-trans-mode",
                    data: dataString,
                    cache: false,
                    success: function (data) {
                        // console.log(data);
                        // location.reload();
                        //$(".modal").modal('hide');
                        $('#orderTransModeModal').modal('hide');
                        expOrder(userNameG,expDateG);
                    }
                });
                }
            });
            $('.xml-export-btn').on('click',function(){
                let xmlcheckedids = [];
                //console.log($('input[name="xmlcheckedids[]"]:checked').val());
                $('input[name="xmlcheckedids[]"]:checked').each(function(){
                    xmlcheckedids.push($(this).val());
                });
                console.log(JSON.stringify(xmlcheckedids));
                if(xmlcheckedids.length > 0){
                    var dataString = ({_token:"{{ csrf_token() }}",expids:""+JSON.stringify(xmlcheckedids)});
                    $.ajax({
                        type: "POST",
                        url: "{{url('/')}}/expense-xml-export",
                        data: dataString,
                        cache: false,
                        success: function (res) {
                            // var xmlDocument = $.parseXML(res);
                            let today =new Date;
                            var xmlDocument = res;
                            xmlDocument = new Blob([xmlDocument]);
                            const link = document.createElement('a');
                            link.setAttribute('href', URL.createObjectURL(xmlDocument));
                            link.setAttribute('download', today.getDate()+'-'+(today.getMonth()+1)+'-'+today.getFullYear()+'_voucher.xml'); // Need to modify filename ...
                            link.click();
                        }
                    });
                }else{
                    alert('please select expense');
                }
                
            });

           
        </script>
    @endsection
</body>
</html>
