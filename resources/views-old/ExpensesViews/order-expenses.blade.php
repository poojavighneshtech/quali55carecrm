@extends('header_and_sidebar')
@section('header')

@endsection

@section('content')

<div class="card" id="filter_card">
    <div class="card-header border-primary" id="filter_card">
        <div class="row">
            <div class="col text-primary" data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                <strong>Order Expenses</strong>
            </div>
            <div class="col-auto">
                <a data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                    <i class="fa fa-chevron-down pull-right"></i>&emsp;<b>Filter</b>
                </a>
            </div>
        </div>
    </div>
    <div class="card-body border-primary collapse" id="filter-collapse" aria-labelledby="headingTwo" data-parent="#filter_card">
        <form action="{{url('/')}}/order-expenses" method="GET">
            {{-- @csrf --}}
            <div class="row form-group">
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="filter_customer_name">Customer Name</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control form-control-sm" value="{{request()->get('filter_customer_name')}}" name="filter_customer_name" id="filter_customer_name">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="filter_start_date">Start Date</label>
                        </div>
                        <div class="col-md-8">
                            <input type="date" class="form-control form-control-sm" value="{{request()->get('filter_start_date')}}" name="filter_start_date" id="filter_start_date">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="filter_end_date">End Date</label>
                        </div>
                        <div class="col-md-8">
                            <input type="date" class="form-control form-control-sm" value="{{request()->get('filter_end_date')}}" name="filter_end_date" id="filter_end_date">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="filter_order_id">Order Id</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control form-control-sm" value="{{request()->get('filter_order_id')}}" name="filter_order_id" id="filter_order_id">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="filter_del_boy">Delivery Boy</label>
                        </div>
                        <div class="col-md-8">
                            <select class="select selectpicker form-control form-control-sm" title="Select Delivery Boy" data-live-search="true" name="filter_del_boy" id="filter_del_boy">
                                    <option value="All"@if(request()->get('filter_del_boy') == "All"){{'selected'}}@endif>All</option>
                                @foreach($del_boys as $key=>$value)
                                    <option value="{{$value->username}}"@if(request()->get('filter_del_boy') == $value->username){{'selected'}}@endif>{{$value->username}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="filter_order_type">Order Type</label>
                        </div>
                        <div class="col-md-8">
                            <select class="select selectpicker form-control form-control-sm" title="Select Order Type" data-live-search="true" name="filter_order_type" id="filter_order_type">
                                <option value="All" @if(request()->get('filter_order_type')=='All'){{'selected'}}@endif>All</option>
                                <option value="Delivery" @if(request()->get('filter_order_type')=='Delivery'){{'selected'}}@endif>Delivery</option>
                                <option value="Pick Up" @if(request()->get('filter_order_type')=='Pick Up'){{'selected'}}@endif>Pick Up</option>
                                <option value="Collection" @if(request()->get('filter_order_type')=='Collection'){{'selected'}}@endif>Collection</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-3">

                </div>
                <div class="col-md-6 text-center">
                    <button class="btn btn-sm btn-outline-success" name="btn_submit" value="submit" id="submit">Submit</button>
                    {{-- <button class="btn btn-sm btn-outline-secondary" name="btn_submit" value="reset" id="reset">Reset</button> --}}
                    <a href="{{url('/')}}/order-expenses" class="btn btn-sm btn-outline-secondary" name="btn_submit" value="reset" id="reset">Reset</a>
                    <button class="btn btn-sm btn-outline-primary" name="btn_submit" value="export" id="export" disabled>Export</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="card card-body my-2">
    <div class="table table-responsive jim-table-responsive">
        <table class="table">
            <thead>
                <tr class="text-nowrap">
                    <th>Order Date</th>
                    <th>Order Id</th>
                    <th>Order Type</th>
                    <th>Del Boy</th>
                    <th>Customer Name</th>
                    <th>Products</th>
                    <th>Cash rec from cust</th>
                    <th>Deposit Returned</th>
                    <th>Labour</th>
                    <th>Transport</th>
                    <th>Expenses</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($order_expenses as $key=>$value)
                    <tr>
                        <td class="text-nowrap" data-label="Order Date">{{$value->DelDate}}</td>
                        <td data-label="Order Id">{{$value->order_id}}</td>
                        <td data-label="Order Type">{{$value->deliverypickup}}</td>
                        <td data-label="Del Boy">{{$value->username}}</td>
                        <td data-label="Customer Name">{{$value->shipping_first_name}}</td>
                        <td data-label="Products">{{$value->line_item_1}}</td>
                        <td data-label="Cash rec from cust">{{$value->cash_rec_from_cust}}</td>
                        <td data-label="Deposit Returned">{{$value->depo_returned}}</td>
                        <td data-label="Labour">{{$value->labour}}</td>
                        <td data-label="Transport">{{$value->transport}}</td>
                        <td data-label="Expenses">{{$value->hardware_expenses}}</td>
                        <td data-label="Action">
                            @if($value->exp_status!='Verified')
                                <button type="button" class="btn btn-outline-primary btn-sm btn_edit_expense"  
                                    id="btn_edit_expense{{$key}}" data-id="{{$value->id}}" value="edit">
                                    <i class="fas fa-edit" id="edit_expense_btn_icon{{$key}}"></i>
                                </button>
                            @else
                                <span class='badge badge-success'>Verified</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center" colspan="12"><h4>No Records Found</h4></td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{$order_expenses->withPath(url()->full())->links('Custom.Pagination.pagination')}}
</div>

<div class="modal fade" id="editExp" tabindex="-1" role="dialog" aria-labelledby="editExpTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{url('/')}}/order-expenses" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="editExpLongTitle">Edit Order Expense</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <label for="cash_rec_frm_off">Cash rec from cust : </label>
                                        </div>
                                        <div class="col-md-6 justify-content-left">
                                            <input type="number" class="form-control form-control-sm calculateExp" name="cash_received_from_customer" id="cashRecFrmCust_expmodal" value="0">
                                            <input type="hidden" name="order_exp_id" id="order_exp_id">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <label for="cash_rec_frm_off">Deposit returned : </label>
                                        </div>
                                        <div class="col-md-6 justify-content-left">
                                            <input type="number" class="form-control form-control-sm calculateExp" name="actual_deposit_returned" id="actualDepositReturned_expmodal" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <label for="transport">Transport : </label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="number" class="form-control form-control-sm calculateExp" name="transport" id="transport_expmodal" value="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <label for="expense">Expense : </label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="number" class="form-control form-control-sm calculateExp" name="expense" id="expense_expmodal" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6 ">
                                            <label for="labour_charge">Labour Charges : </label>
                                        </div>
                                        <div class="col-md-6 justify-content-left">
                                            <input type="number" class="form-control form-control-sm calculateExp" name="labour_charges" id="labourCharges_expmodal" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>                        
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-outline-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
    <script>
        $(".btn_edit_expense").click(function(){
            let exp_id = $(this).data("id");
            var dataString = ({_token:"{{ csrf_token() }}",exp_id:""+exp_id});
            $.ajax({
                type:"POST",
                data:dataString,
                url:"{{url('/')}}/get-order-expense",
                cache:false,
                success:function(data){
                    console.log(data);
                    $('#cashRecFrmCust_expmodal').val(data[0].cash_rec_from_cust);
                    $('#actualDepositReturned_expmodal').val(data[0].depo_returned);
                    $('#expense_expmodal').val(data[0].hardware_expenses);
                    $('#labourCharges_expmodal').val(data[0].labour);
                    $('#transport_expmodal').val(data[0].transport);
                    $('#order_exp_id').val(data[0].id);
                    $("#editExp").modal("show");
                }
            });
        });
    </script>
@endsection