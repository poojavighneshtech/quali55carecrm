@extends('header_and_sidebar')

@section('styles')

@endsection

@section('content')
    @if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
    @endif
    @if(session()->has('info'))
    <div class="alert alert-primary">
        {{ session()->get('info') }}
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
                        <strong>Pickup Products</strong>
                    </div>
                    <div class="col-auto">
                        <a data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                            <i class="fa fa-chevron-down pull-right"></i>&emsp;<b>Filter</b>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body border-primary collapse" id="filter-collapse" aria-labelledby="headingTwo" data-parent="#filter_card">
                <form action="{{route('pickup-products')}}" method="get">
                    <div class="row form-group">
                        <div class="col-md-2">
                            <label for="filter_customer_name">Customer Name</label>
                            <input type="text" name="filter_customer_name" id="filter_customer_name" class="form-control form-control-sm" value="{{request()->get('filter_customer_name')}}">
                        </div>
                        <div class="col-md-2">
                            <label for="filter_contact_no">Contact No.</label>
                            <input type="text" name="filter_contact_no" id="filter_contact_no" class="form-control form-control-sm" value="{{request()->get('filter_contact_no')}}">
                        </div>
                        <div class="col-md-2">
                            <label for="filter_start_date">From Date</label>
                            <input type="date" name="filter_start_date" id="filter_start_date" class="form-control form-control-sm" value="{{request()->get('filter_start_date')}}">
                        </div>
                        <div class="col-md-2">
                            <label for="filter_stop_date">To Date</label>
                            <input type="date" name="filter_stop_date" id="filter_stop_date" class="form-control form-control-sm" value="{{request()->get('filter_stop_date')}}">
                        </div>                        
                        <div class="col-md-2">
                            <label for="filter_order_id">Order Id</label>
                            <input type="number" name="filter_order_id" id="filter_order_id" class="form-control form-control-sm" value="{{request()->get('filter_order_id')}}">
                        </div>
                        <div class="col-md-2">
                            <label for="filter_master_products">Products</label>
                            <select name="filter_master_products[]" id="filter_master_products" class="select selectpicker form-control form-control-sm" title="Select Product" multiple="multiple" data-live-search="true">
                                @foreach($master_products as $key=>$product)
                                    <option value="{{$product->id}}"@if(request()->get('filter_master_products'))@if(in_array($product->id,request()->get('filter_master_products'))){{"selected"}}@endif @endif>{{$product->product_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>                  
                    <div class="row form-group">
                        <div class="col text-center">
                            <button type="submit" class="btn btn-sm btn-outline-success">
                                Search
                            </button>
                            <a href="{{route('pickup-products')}}" class="btn btn-sm btn-outline-secondary">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="row align-content-center">
                <div class="col-auto">Products</div>
                <div class="col-auto">
                    <a  type="button" class="" id="dropdownMenu1"><span class="badge badge-primary">{{$picked_up_products->total()}}</span></a>
                </div>
                <div class="col-auto">Total Rent</div>
                <div class="col-auto">
                    <a  type="button" class="" id="dropdownMenu2"><span class="badge badge-primary">{{$total_rent}}</span></a>
                </div>
                <div class="col-auto">Total Deposit</div>
                <div class="col-auto">
                    <a  type="button" class="" id="dropdownMenu3"><span class="badge badge-primary">{{$total_deposit}}</span></a>
                </div>
                <div class="col-auto">Avg. Kept Period</div>
                <div class="col-auto">
                    <a  type="button" class="" id="dropdownMenu3"><span class="badge badge-primary">{{$avg_kept_period}}</span></a>
                </div>
            </div>
            <div class="table table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Order Id</th>
                            <th>Kept Period</th>
                            <th>Name</th>
                            <th>Contact No</th>
                            <th>Location</th>
                            <th>Product</th>
                            <th>Rent</th>
                            <th>Deposit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($picked_up_products as $key=>$product)
                            <tr>
                                <td data-label="Date">{{date('d-M-y',strtotime($product->DelDate))}}</td>
                                <td data-label="Order Id">{{$product->order_id}}</td>
                                <td data-label="Kept Period">
                                    @php
                                        $days = Carbon\Carbon::parse($product->creation_date)->diffInDays(date('Y-m-d',strtotime($product->DelDate)));  
                                        $years = ($days / 365) ; // days / 365 days
                                        $years = floor($years); // Remove all decimals

                                        $month = ($days % 365) / 30.5; // I choose 30.5 for Month (30,31) ;)
                                        $month = floor($month); // Remove all decimals

                                        $days = ($days % 365) % 30.5; // the rest of days
                                        if($years!=0){
                                            echo $years.' years - '.$month.' month - '.$days.' days';
                                        }else{
                                            // echo $product->creation_date.'<br>'.
                                            echo $month.' month - '.$days.' days';
                                        }
                                    @endphp
                                </td>
                                <td data-label="Name">{{$product->shipping_first_name}}</td>
                                <td data-label="Contact No">{{$product->mobileno}}</td>
                                <td data-label="Location">{{$product->location}}</td>
                                <td data-label="Product">{{$product->product_name}}</td>
                                <td data-label="Rent">{{App\Http\Controllers\RenewalPickup\RenewalPickupController::fetchCrDrData($product->order_details_id,'R')}}</td>
                                <td data-label="Deposit">{{App\Http\Controllers\RenewalPickup\RenewalPickupController::fetchCrDrData($product->order_details_id,'D')}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{$picked_up_products->withPath(url()->full())->links('Custom.Pagination.pagination')}}
        </div>
    </div>
    <div class="modal fade" id="updateStatus" tabindex="-1" role="dialog" aria-labelledby="updateStatusLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="#" method="POST" id="statusUpdateForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateStatusLabel">Update Status</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <label for="status_updated">Update Status</label>
                                <select name="status_updated" id="status_updated" class="select selectpicker form-control form-control-sm" title="Select Status">
                                    <option value="1">Process</option>
                                    <option value="2">Live</option>
                                    <option value="3">Stopped</option>
                                    <option value="4">Cancelled</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label for="comment_updated">Remark</label>
                                <textarea class="form-control form-control-sm" name="comment_updated" id="comment_updated" rows="5"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save changes</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="viewCommentModel" tabindex="-1" role="dialog" aria-labelledby="viewCommentModelLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewCommentModelLabel">Comment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <span id="view_comment_span"></span>
                            
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save changes</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')

@endsection