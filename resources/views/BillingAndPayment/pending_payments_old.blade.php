@extends('header_and_sidebar_test')
@section('styles')
    <style>
        /* .dropdown-menu{
            transform: translate3d(5px, 35px, 0px)!important;
        } */
    </style>
    <style>
        body {
            background-color: #eee
        }
    
        .mt-70 {
            margin-top: 70px
        }
    
        .mb-70 {
            margin-bottom: 70px
        }
    
        .card {
            box-shadow: 0 0.46875rem 2.1875rem rgba(4, 9, 20, 0.03), 0 0.9375rem 1.40625rem rgba(4, 9, 20, 0.03), 0 0.25rem 0.53125rem rgba(4, 9, 20, 0.05), 0 0.125rem 0.1875rem rgba(4, 9, 20, 0.03);
            border-width: 0;
            transition: all .2s
        }
    
        .card {
            position: relative;
            display: flex;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
            background-color: #fff;
            background-clip: border-box;
            border: 1px solid rgba(26, 54, 126, 0.125);
            border-radius: .25rem
        }
    
        /* .card-body {
            flex: 1 1 auto;
            padding: 1.25rem
        } */
    
        .vertical-timeline {
            width: 100%;
            position: relative;
            padding: 1.5rem 0 1rem
        }
    
        .vertical-timeline::before {
            content: '';
            position: absolute;
            top: 0;
            left: 67px;
            height: 100%;
            width: 4px;
            background: #e9ecef;
            border-radius: .25rem
        }
    
        .vertical-timeline-element {
            position: relative;
            margin: 0 0 1rem
        }
    
        .vertical-timeline--animate .vertical-timeline-element-icon.bounce-in {
            visibility: visible;
            animation: cd-bounce-1 .8s
        }
    
        .vertical-timeline-element-icon {
            position: absolute;
            top: 0;
            left: 60px
        }
    
        .vertical-timeline-element-icon .badge-dot-xl {
            box-shadow: 0 0 0 5px #fff
        }
    
        .badge-dot-xl {
            width: 18px;
            height: 18px;
            position: relative
        }
    
        .badge:empty {
            display: none
        }
    
        .badge-dot-xl::before {
            content: '';
            width: 10px;
            height: 10px;
            border-radius: .25rem;
            position: absolute;
            left: 50%;
            top: 50%;
            margin: -5px 0 0 -5px;
            background: #fff
        }
    
        .vertical-timeline-element-content {
            position: relative;
            margin-left: 90px;
            font-size: .8rem
        }
    
        .vertical-timeline-element-content .timeline-title {
            font-size: .8rem;
            text-transform: uppercase;
            margin: 0 0 .5rem;
            padding: 2px 0 0;
            font-weight: bold
        }
    
        .vertical-timeline-element-content .vertical-timeline-element-date {
            display: block;
            position: absolute;
            left: -90px;
            top: 0;
            padding-right: 10px;
            text-align: right;
            color: #adb5bd;
            font-size: .7619rem;
            white-space: nowrap
        }
        /* .vertical-timeline-element-content .vertical-timeline-element-dateonly {
            display: block;
            position: absolute;
            left: -90px;
            top: 20px;
            padding-right: 10px;
            text-align: right;
            color: #adb5bd;
            font-size: .7619rem;
            white-space: nowrap
        } */
    
        .vertical-timeline-element-content:after {
            content: "";
            display: table;
            clear: both
        }
        .overlay-card {
                pointer-events: none;
                opacity: 0.4;
            }
        
    </style>
    <style>
    </style>
@endsection
@section('content')
    @if(session()->has('message'))
        <div class="alert alert-success">
            {{ session()->get('message') }}
        </div>
    @endif
    @if(session()->has('message_delete'))
        <div class="alert alert-danger">
            {{ session()->get('message_delete') }}
        </div>
    @endif 
    @if(session()->has('message_search'))
        <div class="alert alert-danger">
        {{ session()->get('message_search') }}
        </div>
    @endif 
<div class="my-2">
    <div class="card" id="filter_card">
        <div class="card-header border-primary" id="filter_card">
            <div class="row">
                <div class="col text-primary" data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                    <strong>Pending Payments</strong>
                </div>
                <div class="col-auto">
                    <a data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                        <i class="fa fa-chevron-down pull-right"></i>&emsp;<b>Filter</b>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body border-primary collapse" id="filter-collapse" aria-labelledby="headingTwo" data-parent="#filter_card">
            <form action="{{url('/')}}/pending_payments" method="GET" id="pending_payments_form">
                @csrf
                <div class="row">
                    <div class="col-md-10">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="customer_name">Customer Name:</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control form-control-sm" name="filter_customer_name" id="txt_filter_customer_name"  placeholder="Customer Name.." 
                                                size="5" autocomplete="off" value="@if(isset($filter_arr['cust_name'])){{$filter_arr['cust_name']}}@endif">
                                        <datalist id="datalist_customers"></datalist>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label for="From">From:</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="date" name="filter_from_date" id="input_from_date" class="form-control form-control-sm" value="@if(isset($filter_arr['from_date'])){{$filter_arr['from_date']}}@endif">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label for="From">To:</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="date" name="filter_end_date" id="input_end_date" class="form-control form-control-sm" value="@if(isset($filter_arr['end_date'])){{$filter_arr['end_date']}}@endif">
                                            </div>
                                            <div class="col-md-2">
                                                <input type="checkbox" name="onorderdate" id="onorderdate" title="Range Between Orderdate" @if(request()->get('onorderdate'))@if(request()->get('onorderdate') == 'on'){{'checked'}}@endif @endif>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row my-2">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="customer_name">Contact:</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control form-control-sm" name="filter_contact_no"  id="txt_filter_contact_no" placeholder="Contact No..."
                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" 
                                            value="@if(isset($filter_arr['cust_no'])){{$filter_arr['cust_no']}}@endif">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label for="">Type:</label>
                                            </div>
                                            <div class="col-md-10">
                                                <select class="form-control form-control-sm border" name="filter_order_type" id="select_filter_order_type" title="Order Type">
                                                    <option value="All" @if(isset($filter_arr['order_type']) && $filter_arr['order_type']=='All'){{"selected"}}@endif>All</option>
                                                    <option value="Delivery" @if(isset($filter_arr['order_type']) && $filter_arr['order_type']=='Delivery'){{"selected"}}@endif>Delivery</option>
                                                    <option value="Collection" @if(isset($filter_arr['order_type']) && $filter_arr['order_type']=='Collection'){{"selected"}}@endif>Collection</option>
                                                    <option value="Pick Up" @if(isset($filter_arr['order_type']) && $filter_arr['order_type']=='Pick Up'){{"selected"}}@endif>Pick Up</option>
                                                    <option value="Repair" @if(isset($filter_arr['order_type']) && $filter_arr['order_type']=='Repair'){{"selected"}}@endif>Repair</option>
                                                    <option value="Install" @if(isset($filter_arr['order_type']) && $filter_arr['order_type']=='Install'){{"selected"}}@endif>Install</option>
                                                    <option value="Shifting" @if(isset($filter_arr['order_type']) && $filter_arr['order_type']=='Shifting'){{"selected"}}@endif>Shifting</option>
                                                    <option value="Replacement" @if(isset($filter_arr['order_type']) && $filter_arr['order_type']=='Replacement'){{"selected"}}@endif>Replacement</option>
                                                    <option value="Live" @if(isset($filter_arr['order_type']) && $filter_arr['order_type']=='Live'){{"selected"}}@endif>Live</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label for="From">State:</label>
                                            </div>
                                            <div class="col-md-10">
                                                <select class="selectpicker form-control form-control-sm border" name="filter_order_state" title="Select Order State" id="select_filter_settled_status">
                                                    <option value="All" @if(isset($filter_arr['order_state']) && $filter_arr['order_state']=='All'){{"selected"}}@endif>All</option>
                                                    <option value="Y" @if(isset($filter_arr['order_state']) && $filter_arr['order_state']=='Y'){{"selected"}}@endif>Settled</option>
                                                    <option value="N" @if(isset($filter_arr['order_state']) && $filter_arr['order_state']=='N'){{"selected"}}@endif>Not Settled</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="patient_name">Patient Name :</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control form-control-sm" name="filter_patient_name" id="filter_patient_name" placeholder="patient name..."
                                            value="@if(isset($filter_arr['patient_name'])){{$filter_arr['patient_name']}}@endif">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="patient_name">City :</label>
                                    </div>
                                    <div class="col-md-9">
                                        <select class="form-control form-control-sm selectpicker" name="filter_city" id="filter_city" 
                                        data-size="4" data-live-search="true">
                                            <option value="All" @if(request()->get('filter_city')=='All')selected @endif>All</option>
                                            {{-- <option value="Mumbai" @if(request()->get('filter_city')=='Mumbai') selected @endif>Mumbai</option>
                                            <option value="Pune" @if(request()->get('filter_city')=='Pune') selected @endif>Pune</option> --}}
                                            @foreach(config('app.citylist') as $key=>$value)
                                                {{-- <option value="{{$city->city}}" @if(request()->get('city_filter')==$city->city) selected @endif>{{$city->city}}</option> --}}
                                                <option value="{{$value}}" @if(request()->get('filter_city')==$value) selected @endif>{{$value}}</option>
                                                {{-- <option value="Pune" @if(isset($filter_data['city'])) @if($filter_data['city'] == "Pune")selected @endif @endif>Pune</option> --}}
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="patient_name">CustType</label>
                                    </div>
                                    <div class="col-md-8">
                                        <select class="form-control form-control-sm selectpicker" name="customer_type" id="customer_type" 
                                            data-size="5" data-live-search="true">
                                            <option value="All" @if(request()->get('customer_type')=='All')selected @endif>All</option>
                                            <option value="Individual" @if(request()->get('customer_type')=='Individual')selected @endif>Individual</option>
                                            <option value="Corporate" @if(request()->get('customer_type')=='Corporate')selected @endif>Corporate</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-2">
                                <input type="text" name="filter_order_id" id="input_order_id" class="form-control form-control-sm" placeholder="Order Id.."
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" 
                                    value="@if(isset($filter_arr['order_id'])){{$filter_arr['order_id']}}@endif"> 
                            </div>
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="Del Status">DStatus</label>
                                    </div>
                                    <div class="col-md-9">
                                        <select class="form-control form-control-sm border" name="filter_delivery_status" id="select_filter_delivery_status">
                                            <option value="All" @if(isset($filter_arr['delivery_status']) && $filter_arr['delivery_status']=='All'){{"selected"}}@endif>All</option>
                                            <option value="Cancel" @if(isset($filter_arr['delivery_status']) && $filter_arr['delivery_status']=='Cancel')selected @endif>Cancel - CA</option>
                                            <option value="Pending" @if(isset($filter_arr['delivery_status']) && $filter_arr['delivery_status']=='Pending')selected @endif>Pending - PE</option>
                                            <option value="Assigned" @if(isset($filter_arr['delivery_status']) && $filter_arr['delivery_status']=='Assigned')selected @endif>Assigned - AS</option>
                                            <option value="Accepted" @if(isset($filter_arr['delivery_status']) && $filter_arr['delivery_status']=='Accepted')selected @endif>Accepted - AC</option>
                                            <option value="Rejected" @if(isset($filter_arr['delivery_status']) && $filter_arr['delivery_status']=='Accepted')selected @endif>Rejected - RE</option>
                                            <option value="InProgress" @if(isset($filter_arr['delivery_status']) && $filter_arr['delivery_status']=='InProgress')selected @endif>In Progress - IP</option>
                                            <option value="Delivered" @if(isset($filter_arr['delivery_status']) && $filter_arr['delivery_status']=='Delivered')selected @endif>Delivered - DE</option>
                                            <option value="Collected" @if(isset($filter_arr['delivery_status']) && $filter_arr['delivery_status']=='Collected')selected @endif>Collected - CO</option>
                                            <option value="Picked Up" @if(isset($filter_arr['delivery_status']) && $filter_arr['delivery_status']=='Picked Up')selected @endif>Picked Up - PU</option>
                                            <option value="Closed" @if(isset($filter_arr['delivery_status']) && $filter_arr['delivery_status']=='Closed')selected @endif>Closed - CL</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="Dboys">DStaff</label>
                                    </div>
                                    <div class="col-md-9">
                                        <select class="selectpicker form-control form-control-sm border" name="filter_delivery_boy" title="Select Delivery Boy" id="select_filter_delivery_boy" 
                                            data-size="5">
                                            <option value="All" @if($filter_arr['delivery_boy']=="All"){{"selected"}}@endif>All</option>
                                            @foreach($all_delivery_boys as $key=>$value)
                                                <option value="{{$value->DelAssignedTo}}" @if($filter_arr['delivery_boy']==$value->DelAssignedTo){{"selected"}}@endif>{{$value->DelAssignedTo}}</option>
                                            @endforeach
                                            <option value="NAN">{{$filter_arr['delivery_boy']}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 pt-2">
                                <select class="selectpicker form-control form-control-sm border" name="filter_lead_owner" id="select_filter_lead_owner" title="Lead owner only for delivery" data-size="3" data-live-search="true">
                                    <option value="All" @if($filter_arr['lead_owner']=="All") selected @endif>All</option>
                                    @foreach ($get_lead_owner as $key=>$lead_owner)
                                        <option value="{{$lead_owner->id}}" @if(isset($filter_arr['lead_owner']) && $filter_arr['lead_owner']==$lead_owner->id) selected @endif>{{$lead_owner->username}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 pt-2">
                                <select class="selectpicker form-control form-control-sm border" name="filter_payment_mode" id="select_filter_payment_mode" title="Payment Mode" data-size="3" data-live-search="true">
                                    <option value="All" @if(request()->get('filter_payment_mode'))@if(request()->get('filter_payment_mode') == 'All')selected @endif @else selected @endif>All</option>
                                    <option value="Online" @if(request()->get('filter_payment_mode'))@if(request()->get('filter_payment_mode') == 'Online')selected @endif @endif>Online</option>
                                    <option value="Cash" @if(request()->get('filter_payment_mode'))@if(request()->get('filter_payment_mode') == 'Cash')selected @endif @endif>Cash/COD</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 my-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm btn-block" id="btn_clear">Clear Filter</button>
                        <button type="submit" class="btn btn-outline-primary btn-block btn-sm" name="btn_submit" value="submit">Submit</button>
                        <button type="submit" class="btn btn-outline-success btn-block btn-sm" name="btn_submit" value="export_excel">Excel Export</button>
                        <button type="button" class="btn btn-outline-primary btn-block btn-sm" name="update" id="update" data-toggle="modal" data-target="#updateCorpPayments">Update Corporate Payments</button>
                    </div>
                </div>
                <div class="row from-group">
                    <div class="col-auto">
                        <label for="leadsource">Lead Source</label>
                        <select name="filterleadsource[]" id="filterleadsource" class="select selectpicker leadsource" title="Select Source" data-live-search="true" multiple="true" data-size="5">
                            @foreach($leadsource as $key=>$source)
                                <option value="{{$source}}" @if(request()->get('filterleadsource'))@if(in_array($source,request()->get('filterleadsource')))selected @endif @endif>{{$source}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <label for="products">Products</label>
                        <select name="filterproducts[]" id="filterproducts" class="select selectpicker products" title="Select Product" data-live-search="true" multiple="true" data-size="5">
                            @foreach($products as $key=>$product)
                                <option value="{{$product->id}}" @if(request()->get('filterproducts'))@if(in_array($product->id,request()->get('filterproducts')))selected @endif @endif>{{$product->product_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <label for="categories">Product Category</label>
                        <select name="filtercategories[]" id="filtercategories" class="select selectpicker categories" title="Select Category" data-live-search="true" multiple="true" data-size="5">
                            @foreach($categories as $key=>$product)
                                <option value="{{$product->product_category}}" @if(request()->get('filtercategories'))@if(in_array($product->product_category,request()->get('filtercategories')))selected @endif @endif>{{$product->product_category}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <div class="row">
            <div class="col-auto">Settled</div>
            <div class="col-auto">
                <a  type="button" class="" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="badge badge-primary">{{number_format($count_array[1]+$count_array[3]+$count_array[5]+$count_array[7])}}</span></a>
                <!--Menu-->
                <div class="dropdown-menu dropdown-primary">
                    <a class="dropdown-item" href="#">Delivery &emsp;<strong>{{number_format($count_array[1])}}</strong></a>
                    <a class="dropdown-item" href="#">Collection &emsp;<strong>{{number_format($count_array[3])}}</strong></a>
                    <a class="dropdown-item" href="#">Pick Up &emsp;<strong>{{number_format($count_array[5])}}</strong></a>
                    <a class="dropdown-item" href="#">Maitenance Order &emsp;<strong>{{number_format($count_array[7])}}</strong></a>
                </div>
            </div>
            <div class="col-auto">Not Settled</div>
            <div class="col-auto">
                <a  type="button" class="" id="dropdownMenu3" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="badge badge-primary">{{number_format($count_array[0]+$count_array[2]+$count_array[4] + $count_array[6])}}</span></a>
                <!--Menu-->
                <div class="dropdown-menu dropdown-primary">
                    <a class="dropdown-item" href="#">Delivery &emsp;<strong>{{number_format($count_array[0])}}</strong></a>
                    <a class="dropdown-item" href="#">Collection &emsp;<strong>{{number_format($count_array[2])}}</strong></a>
                    <a class="dropdown-item" href="#">Pick Up &emsp;<strong>{{number_format($count_array[4])}}</strong></a>
                    <a class="dropdown-item" href="#">Maitenance Order &emsp;<strong>{{number_format($count_array[6])}}</strong></a>
                </div>
            </div>
            <div class="col-auto">Online Amount</div>
            <div class="col-auto">
                <a  type="button" class="" id="dropdownMenu3" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="badge badge-primary">{{number_format($amount_array[0]+$amount_array[2]+$amount_array[4])}}</span></a>
                <!--Menu-->
                <div class="dropdown-menu dropdown-primary">
                    <a class="dropdown-item" href="#">Delivery &emsp;<strong>{{number_format($amount_array[0])}}</strong></a>
                    <a class="dropdown-item" href="#">Collection &emsp;<strong>{{number_format($amount_array[2])}}</strong></a>
                    <a class="dropdown-item" href="#">Pick Up &emsp;<strong>{{number_format($amount_array[4])}}</strong></a>
                </div>
            </div>
            <div class="col-auto">Cash Amount</div>
            <div class="col-auto">
                <a  type="button" class="" id="dropdownMenu3" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="badge badge-primary">{{number_format($amount_array[1]+$amount_array[3]+$amount_array[5])}}</span></a>
                <!--Menu-->
                <div class="dropdown-menu dropdown-primary">
                    <a class="dropdown-item" href="#">Delivery &emsp;<strong>{{number_format($amount_array[1])}}</strong></a>
                    <a class="dropdown-item" href="#">Collection &emsp;<strong>{{number_format($amount_array[3])}}</strong></a>
                    <a class="dropdown-item" href="#">Pick Up &emsp;<strong>{{number_format($amount_array[5])}}</strong></a>
                </div>
            </div>
            <div class="col-auto">Total Amount</div>
            <div class="col-auto">
                <a  type="button" class="" id="dropdownMenu3" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="badge badge-primary">{{number_format($amount_array[1]+$amount_array[3]+$amount_array[5] + $amount_array[0]+$amount_array[2]+$amount_array[4])}}</span></a>
                <!--Menu-->
                <div class="dropdown-menu dropdown-primary">
                    <a class="dropdown-item" href="#">Delivery &emsp;<strong>{{number_format($amount_array[0] + $amount_array[1])}}</strong></a>
                    <a class="dropdown-item" href="#">Collection &emsp;<strong>{{number_format($amount_array[2] + $amount_array[3])}}</strong></a>
                    <a class="dropdown-item" href="#">Pick Up &emsp;<strong>{{number_format($amount_array[4] + $amount_array[5])}}</strong></a>
                </div>
            </div>
            <div class="col-auto">Taxable Amount</div>
            <div class="col-auto">
                <a  type="button" class="" id="dropdownMenu3" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="badge badge-primary">{{number_format($amount_array[8]+$amount_array[9])}}</span></a>
                <!--Menu-->
                <div class="dropdown-menu dropdown-primary">
                    <a class="dropdown-item" href="#">Delivery &emsp;<strong>{{number_format($amount_array[8])}}</strong></a>
                    <a class="dropdown-item" href="#">Collection &emsp;<strong>{{number_format($amount_array[9])}}</strong></a>
                </div>
            </div>
            <div class="col-auto">Count</div>
            <div class="col-auto">
                <a  type="button" class="" id="dropdownMenu3" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="badge badge-primary">{{number_format($prodcountrent+$prodcountrenewal)}}</span></a>
                <!--Menu-->
                <div class="dropdown-menu dropdown-primary">
                    <a class="dropdown-item" href="#">Delivery &emsp;<strong>{{number_format($prodcountrent)}}</strong></a>
                    <a class="dropdown-item" href="#">Collection &emsp;<strong>{{number_format($prodcountrenewal)}}</strong></a>
                </div>
            </div>
        </div>
        <div class="table table-responsive jim-table-responsive">
            <table class="table table-hover table-responsive table-flush ">
                <thead class="thead thead-primary text-white border-primary">
                    <tr class="text-nowrap border-primary">
                        <th>Action&emsp;</th>
                        <th>Order Id</th>
                        <th>Order Date</th>
                        <th>Created On</th>
                        <th>Type/St</th>
                        <th>Customer Name</th>
                        <th>Patient Name</th>
                        <th>Mobile</th>
                        <th>Mode</th>
                        <th>Total Amt</th>
                        <th>Labour</th>
                        <th>Pay Img/Ref Id</th>
                        {{-- <th>Reference No</th> --}}
                        <th>Rec Img</th>
                        <th>Lead Own</th>
                        <th>Location</th>
                        <th>Assigned To</th>
                        {{-- <th>Oth Tra Exp</th> --}}
                        <th>Expenses</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($non_settled_orders as $key=>$value)
                        <tr class="text-nowrap @if($value->adjustedIn == 1 || $value->adjustedFrom == 1){{'table-warning'}}@endif">
                            <td data-label="Action">
                                <a class="btn btn-sm btn-outline-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Action
                                </a>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                    <a type="button" class="dropdown-item text-primary" data-id="{{$value->order_id}}" data-order_type ="{{$value->order_type}}" data-order_id="{{$value->order_id}}" 
                                        onclick="activityLog('{{$value->order_id}}','{{$value->order_type}}');"
                                        data-toggle="modal" data-target="#activityLogModal">
                                        <i class="fas fa-list"></i>Activity Log
                                    </a>
                                    @if($value->ccadflag != 'CCAD')
                                        <a type="button" class="dropdown-item btn_timeline text-primary" data-id="{{$value->order_id}}" data-order_type ="{{$value->order_type}}" data-order_id="{{$value->order_id}}" 
                                            data-toggle="modal" data-target="#timelineModal">
                                            <i class="fas fa-history mr-2"></i>Timeline
                                        </a>
                                    @endif
                                    <a type="button" class="dropdown-item  text-primary viewOrder" id="viewOrder{{$key}}" data-order_type="{{$value->order_type}}" data-order_id="{{$value->order_id}}" data-id={{$key}}> <i class="fa fa-eye mr-2" aria-hidden="true"></i>View Order</a>
                                    @if($value->status != 'Cancel')
                                        @php
                                            $invoice_visibility = config('app.invoice_visibility');
                                        @endphp
                                        @if(in_array(session('user_id'),$invoice_visibility))
                                            @if($value->invoice_no != null && $value->invoice_no != "" && $value->order_type == 'Delivery') 
                                                <a type="button" class="dropdown-item  text-primary viewInvoice" target = "_blank" href="{{route('generate-invoice')}}?lead_id={{$value->lead_id}}" id="viewInvoice{{$key}}" data-order_type="{{$value->order_type}}" data-order_id="{{$value->order_id}}" data-id={{$key}} data-order_status="{{$value->status}}">
                                                    <i class="fa fa-eye mr-2" aria-hidden="true"></i>View invoice
                                                </a>
                                            @endif 
                                            @if($value->invoice_no != null && $value->invoice_no != "" && $value->order_type == 'Collection') 
                                                <a type="button" class="dropdown-item  text-primary viewInvoice" target = "_blank" href="{{route('generate-invoice-renew')}}?order_id={{$value->order_id}}" id="viewInvoice{{$key}}" data-order_type="{{$value->order_type}}" data-order_id="{{$value->order_id}}" data-id={{$key}} data-order_status="{{$value->status}}">
                                                    <i class="fa fa-eye mr-2" aria-hidden="true"></i>View invoice
                                                </a>
                                            @endif
                                        @endif
                                        @if(session('role') == 'superuser')
                                            @if($value->settlement_status == 'N')
                                                @if($value->order_type == 'Collection' && $value->status != 'Collected' && $value->assigned_payment_mode == 'Online')
                                                    <a href="{{url('/')}}/payment_recieved/{{$value->order_id}}" class="dropdown-item  text-primary"><i class="fa fa-check mr-2" aria-hidden="true"></i>Payment Recieved</a>
                                                @endif
                                                <a type="button" class="dropdown-item  text-primary updateOrder" id="updateOrder{{$key}}" data-ref_id = "{{$value->reference_id}}" data-order_type="{{$value->order_type}}" data-invoice_no="{{$value->invoice_no}}" data-order_id="{{$value->order_id}}" data-id={{$key}} data-floor_no = {{$value->floor_no}} data-labour_charges = {{$value->labour_charges}}>
                                                    <i class="fa fa-edit mr-2" aria-hidden="true"></i>Update Payment
                                                </a>
                                                <a type="button" class="dropdown-item  text-primary settleOrder" id="settleOrder{{$key}}" data-order_type="{{$value->order_type}}" data-order_id="{{$value->order_id}}" data-id={{$key}} data-order_status="{{$value->status}}">
                                                    <i class="fa fa-check mr-2" aria-hidden="true"></i>Settle
                                                </a>
                                                @if(!in_array(session('user_id'),config('app.accounts_id_array')))
                                                    @if($value->order_type=='Delivery' && $value->status!= 'Cust Rejected' && $value->status!= 'Closed')
                                                        <a type="button" class="dropdown-item text-primary" 
                                                            href="{{url('/')}}/editOrder/{{$value->order_id}}/{{$value->order_type}}"><i class="fa fa-edit mr-2" aria-hidden="true"></i>Update Order
                                                        </a>
                                                    @elseif($value->order_type == 'Collection' && $value->status != 'Collected' && $value->status!= 'Cust Rejected' && $value->status!= 'Closed')
                                                        @if($value->ccadflag != 'CCAD')
                                                            <a type="button" class="dropdown-item text-primary" href="{{route('edit-renewal')}}?order_id={{$value->order_id}}" ><i class="fa fa-edit mr-2" aria-hidden="true"></i>Update Order</a>
                                                        @endif
                                                    @endif
                                                    @if($value->ccadflag != 'CCAD')
                                                        <a type="button" class="dropdown-item text-primary upload_image" data-id="{{$value->order_id}}" title="Upload Image"><i class="fa fa-upload mr-2" aria-hidden="true"></i>Upload Image
                                                        </a>
                                                        <a id="addLaboour{{$key}}" type="button" class="dropdown-item text-primary addLabour" 
                                                            data-order_id={{$value->order_id}} data-floor_no={{$value->floor_no}} data-labour_charges = {{$value->labour_charges}}>
                                                            <i class="fa fa-users mr-2" aria-hidden="true"></i>Labour
                                                        </a>
                                                    @endif
                                                     @if($value->order_type != 'Delivery')
                                                        <a type="button" class="dropdown-item text-danger close_order" data-id="{{$value->order_id}}" data-order_type ="{{$value->order_type}}" ><i class="fas fa-window-close mr-2"></i>Close Order</a>
                                                    @elseif($value->order_type == 'Delivery')
                                                        <a type="button" class="dropdown-item text-danger close_order" data-id="{{$value->order_id}}" data-order_type ="{{$value->order_type}}" ><i class="fas fa-window-close mr-2"></i>Close Order</a>
                                                    @else
                                                        <a type="button" class="dropdown-item text-danger close_order" data-id="{{$value->order_id}}" data-order_type ="{{$value->order_type}}" ><i class="fas fa-window-close mr-2"></i>Close Order</a>
                                                    @endif
                                                @endif
                                            @else
                                                <a type="button" class="dropdown-item  text-danger unsettle-order" href="{{route('unsettle-order')}}?order_id={{$value->order_id}}" id="unsettle-order{{$key}}">
                                                    <i class="fa fa-window-close mr-2" aria-hidden="true"></i>Unsettle Order
                                                </a>
                                            @endif
                                            @if($value->order_type == "Collection" && $value->status == 'Collected')
                                                <a type="button" class="dropdown-item  text-primary update-collection" href="#" onclick="editCollection({{$value->order_id}});">
                                                    <i class="fa fa-edit mr-2" aria-hidden="true"></i>Update Order
                                                </a>
                                            @endif
                                        @elseif(session('role') == 'user')
                                            @if($value->settlement_status == 'N')
                                                @if($value->order_type == 'Collection' && $value->status != 'Collected' && $value->assigned_payment_mode == 'Online')
                                                    <a href="{{url('/')}}/payment_recieved/{{$value->order_id}}" class="dropdown-item  text-primary"><i class="fa fa-check mr-2" aria-hidden="true"></i>Payment Recieved</a>
                                                @endif
                                                <a class="dropdown-item text-primary updateOrder" id="updateOrder{{$key}}" data-ref_id = "{{$value->reference_id}}"  data-order_type="{{$value->order_type}}" data-order_id="{{$value->order_id}}" data-id={{$key}} data-floor_no = {{$value->floor_no}} data-labour_charges = {{$value->labour_charges}} >Update Order</a>
                                            @endif
                                        @endif
                                    @else                                        
                                        <a type="button" class="dropdown-item  text-primary updateOrder" id="updateOrder{{$key}}" data-ref_id = "{{$value->reference_id}}"  data-order_type="{{$value->order_type}}" data-invoice_no="{{$value->invoice_no}}" data-order_id="{{$value->order_id}}" data-id={{$key}} data-floor_no = {{$value->floor_no}} data-labour_charges = {{$value->labour_charges}}>
                                            <i class="fa fa-edit mr-2" aria-hidden="true"></i>Update Payment
                                        </a>
                                        <a type="button" class="dropdown-item  text-primary settleOrder" id="settleOrder{{$key}}" data-order_type="{{$value->order_type}}" data-order_id="{{$value->order_id}}" data-id={{$key}} data-order_status="{{$value->status}}">
                                            <i class="fa fa-check mr-2" aria-hidden="true"></i>Settle
                                        </a>
                                    @endif
                                    @if($value->order_type == 'Pick Up')
                                        <a class="dropdown-item bank_details text-primary" id="bank_details{{$key}}" data-id={{$key}}><i class="fa fa-university mr-2" aria-hidden="true"></i> Bank Details</a>
                                        @if($value->settlement_status == 'N')
                                            <a type="button" class="dropdown-item updatePickup text-primary" id="updatePickup{{$key}}" data-id="{{$key}}" data-order_type="{{$value->order_type}}" data-order_id="{{$value->order_id}}"><i class="fa fa-edit mr-2" aria-hidden="true"></i> Update Pickup</a>
                                        @endif
                                    @endif
                                    @if($value->order_type == 'Delivery' && $value->status == 'Cancel' && $value->status!= 'Cust Rejected' && $value->status!= 'Closed')
                                        <a type="button" class="dropdown-item text-primary updateClosedOrder" id="update_closed_order{{$key}}" data-order_id="{{$value->order_id}}"><i class="fa fa-edit mr-2" aria-hidden="true"></i> Update Order</a>
                                    @endif
                                        <a type="button" class="dropdown-item text-primary add-comment" id="addcomment{{$key}}" data-order_id="{{$value->order_id}}" data-comment="{{$value->comment}}"><i class="fa fa-edit mr-2" aria-hidden="true"></i> Add Comment</a>
                                    @if($value->order_type == 'Delivery')
                                        <a type="button" class="dropdown-item  text-primary" href="{{route('transaction_history',['order_id'=>$value->order_id])}}" id="ordertransacthist{{$key}}"> 
                                            <i class="fa fa-credit-card mr-2" aria-hidden="true"></i>Transaction History
                                        </a>
                                    @endif
                                    @if($value->ccadflag != 'CCAD')
                                        <a type="button" class="dropdown-item  text-primary adjust-deposit" id="adjustdeposit{{$key}}" data-order_type="{{$value->order_type}}" data-order_id="{{$value->order_id}}" data-id={{$key}}> <i class="fas fa-exchange-alt mr-2" aria-hidden="true"></i>Adjustment</a>

                                        <a type="button" class="dropdown-item  text-primary replacement-sale-order" onclick="fetchProducts({{$value->lead_id}})" id="replacesaleorder{{$key}}" data-order_type="{{$value->order_type}}" data-order_id="{{$value->order_id}}" data-id="{{$key}}"> <i class="fas fa-rupee-sign mr-2"></i>Replace/Sale Order</a>
                                    @endif
                                        <a type="button" class="dropdown-item  text-primary other-expense" id="otherexpense{{$key}}" data-order_type="{{$value->order_type}}" data-order_id="{{$value->order_id}}" data-id={{$key}}> <i class="fas fa fa-taxi mr-2" aria-hidden="true"></i>Other Travel Expense</a>
                                        @if($value->order_type == 'Delivery' && $value->status=='Delivered')
                                            <a type="button" class="dropdown-item  text-primary cash-collection-request" id="ccrequest{{$key}}" data-leadid = "{{$value->lead_id}}" data-orderid = "{{$value->order_id}}"> 
                                                <i class="fas fa fa-taxi mr-2" aria-hidden="true"></i>Cash Collection Request
                                            </a>
                                        @endif
                                        @if($value->ccadflag == 'CCAD')
                                            <a type="button" class="dropdown-item  text-primary cash-collection-request-update" id="ccrequestupdate{{$key}}" data-leadid = "{{$value->lead_id}}" data-orderid = "{{$value->order_id}}"> 
                                                <i class="fas fa fa-taxi mr-2" aria-hidden="true"></i>Update Order
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td data-label="Order Id">
                                @if($value->settlement_status == 'Y')<span class="badge badge-success">S</span>@endif{{$value->order_id}}
                                @if($value->assigned_to == 'Customer' && $value->order_type == 'Delivery')<br><span class="badge badge-danger"><small>Cust Self Pickup</small></span>@endif
                                @if($value->assigned_to == 'Customer' && $value->order_type == 'Pick Up')<br><span class="badge badge-danger"><small>Cust Self Drop</small></span>@endif
                                @if($value->ccadflag == 'CCAD')<br><span class="badge badge-info"><small>Against Delivery - {{$value->ccad_delivery_order_id}}</small></span>@endif
                                @if(Collect($value)->has('ccad_collection_order_id')) <br><span class="badge badge-primary"> <small>Collection Order - {{$value->ccad_collection_order_id}}</small> </span>@endif
                                @if($value->flag == 'Replacement')<br><span class="badge badge-primary"> <small>Replacement</small></span>@endif
                            </td>
                            <td data-label="Order Date">
                                {{date('d-m-y',strtotime($value->date))}}
                            </td>
                            <td data-label="Created At">
                                @if($value->order_type == 'Delivery')
                                    {{date('d-M-y h:i A',strtotime($value->order_created_at))}}
                                @elseif($value->order_type == 'Collection')
                                    {{date('d-M-y h:i A',strtotime($value->order_created_at))}}
                                @elseif($value->order_type == 'Pick Up')
                                    {{date('d-M-y h:i A',strtotime($value->order_created_at))}}
                                @elseif(in_array($value->order_type,['Install','Shifting','Repair','Replace']))
                                    {{date('d-M-y h:i A',strtotime($value->order_created_at))}}
                                @endif
                                
                            </td>
                            <td data-label="Type/Status">
                                @if($value->order_type == 'Delivery')
                                    <span class="badge badge-success">
                                        D
                                    </span>
                                @elseif($value->order_type == 'Collection')
                                    <span class="badge badge-warning">
                                        C
                                    </span>
                                @elseif($value->order_type == 'Pick Up')
                                    <span class="badge badge-danger">
                                        P
                                    </span>
                                @elseif(in_array($value->order_type,['Install','Shifting','Repair','Replace']))
                                    <span class="badge badge-primary">
                                        {{$value->order_type}}
                                    </span>
                                @endif
                                {{"/"}}
                                @if($value->status == 'Pending')
                                <span class="badge badge-danger">
                                    {{"PE"}}
                                </span>
                                @elseif($value->status == 'Accepted')
                                    <span class="badge badge-secondary">
                                        {{"AC"}}
                                    </span>
                                @elseif($value->status == 'Assigned')
                                    <span class="badge badge-warning">
                                        {{"AS"}}
                                    </span>
                                @elseif($value->status == 'InProgress')
                                    <span class="badge badge-primary">
                                        {{"IP"}}
                                    </span>
                                @elseif($value->status == 'Cancel')
                                    <span class="badge danger">
                                        {{"CA"}}
                                    </span>
                                @elseif($value->status == 'Collected')
                                    <span class="badge badge-success">
                                        {{"CO"}}
                                    </span>
                                @elseif($value->status == 'Delivered')
                                    <span class="badge badge-success">
                                        {{"DE"}}
                                    </span>
                                @elseif($value->status == 'Picked up')
                                    <span class="badge badge-success">
                                        {{"PU"}}
                                    </span>
                                @elseif($value->status == 'Completed')
                                <span class="badge badge-success">
                                    {{"COM"}}
                                </span>
                                @elseif($value->status == 'Closed')
                                <span class="badge badge-success">
                                    {{"CL"}}
                                </span>
                                @else
                                    <span class="badge ">
                                        {{"CR"}}
                                    </span>
                                @endif

                            </td>
                            <td data-label="Customer Name">
                                {{$value->customer_name}}
                                @if($value->customer_type == 'Corporate' && $value->order_type != 'Pick Up')
                                    <br><small>Invoice No: {{$value->invoice_no}}</small>
                                @endif
                            </td>
                            <td data-label="Patient Name">
                                {{$value->patient_name}}
                            </td>
                            <td data-label="Mobile Number">
                                {{$value->mobile_number}}
                            </td>
                            
                            <td data-label="Payment Mode">
                                {{$value->assigned_payment_mode}}@if(!empty($value->lead_reference_id))<br><span class="badge badge-success"><small>Paid Web Online</small></span>@endif
                                <input type="hidden" name="hidden_payment_mode" id="hidden_payment_mode{{$key}}" value="{{$value->assigned_payment_mode}}">
                                <input type="hidden" name="hidden_order_expense" id="hidden_order_expense{{$key}}" value="{{$value->order_expense}}">
                                <input type="hidden" name="hidden_vendor_charges" id="hidden_vendor_charges{{$key}}" value="{{$value->vendor_charges}}">
                            </td>
                           <!-- <td data-label="Amount">
                                @if($value->modified)
                                    <button class="btn btn-sm btn-primary btn-crdrdata" id="btn-crdrdata{{$key}}" value="{{$value->order_id}}">M</button>
                                @endif
                                <b>T: </b>{{$value->assigned_total_amount}}
                                    <input type="hidden" name="assigned_total_amount" id="assigned_total_amount{{$key}}" value="{{$value->assigned_total_amount}}">
                                @if($value->assigned_payment_mode == 'Cash')
                                    <b>C: </b>{{$value->assigned_total_amount}}
                                @elseif(isset($value->assigned_cash) && $value->assigned_cash != '')
                                <b>C: </b>{{$value->assigned_cash}}
                                @else
                                <b>C: </b>{{"0"}}
                                @endif
                                @if($value->assigned_payment_mode == 'Online')
                                    <b>O: </b>{{$value->assigned_total_amount}}
                                @elseif(isset($value->assigned_online) && $value->assigned_online != '')
                                    <b>O: </b>{{$value->assigned_online}}
                                @else
                                    <b>O: </b>{{"0"}}
                                @endif
                                @if($value->adjustedIn == 1 || $value->adjustedFrom == 1)
                                    <button type="button" class="btn btn-sm btn-adjusted-view" data-order_id="{{$value->order_id}}" data-order_type="{{$value->order_type}}" data-state="@if($value->adjustedIn == 1){{'in'}}@elseif($value->adjustedFrom == 1){{'out'}}@endif"><i class="fa fa-eye" aria-hidden="true"></i></button>
                                @endif
                                @if($value->comment)
                                    <br>
                                    <span data-toggle="tooltip" data-placement="left" title="{{$value->comment}}"><small>{{substr($value->comment,0,20)}} ...</small></span>
                                @else
                                    -
                                @endif
                            </td>-->
                            <td data-label="Amount">
                                @if($value->modified)
                                    <button class="btn btn-sm btn-primary btn-crdrdata" id="btn-crdrdata{{$key}}" value="{{$value->order_id}}">M</button>
                                @endif
                                {{$value->assigned_total_amount}}
                                    <input type="hidden" name="assigned_total_amount" id="assigned_total_amount{{$key}}" value="{{$value->assigned_total_amount}}">

                                @if($value->adjustedIn == 1 || $value->adjustedFrom == 1)
                                    <button type="button" class="btn btn-sm btn-adjusted-view" data-order_id="{{$value->order_id}}" data-order_type="{{$value->order_type}}" data-state="@if($value->adjustedIn == 1){{'in'}}@elseif($value->adjustedFrom == 1){{'out'}}@endif"><i class="fa fa-eye" aria-hidden="true"></i></button>
                                @endif
                                @if($value->comment)
                                    <br>
                                    <span data-toggle="tooltip" data-placement="left" title="{{$value->comment}}"><small>{{substr($value->comment,0,20)}} ...</small></span>
                                @endif
                            </td>
                            <td data-label="Labour">{{$value->labour_charges}}</td>
                            <td data-label="Payment Image">
                                @if($value->payment_image != null || $value->payment_image != "")
                                    <center>
                                        <button class="btn btn-sm btn-outline-primary viewPayment" id="viewPaymentImage{{$key}}" data-id={{$key}}>
                                            <i class="fas fa-image"></i>
                                        </button>
                                    </center>
                                    <input type="hidden" name="hidden_payment_image" id="hidden_payment_image{{$key}}" value="{{url('/').'/assets/uploads/payment_images/'.$value->payment_image}}">
                                @else
                                    <center>
                                        <input type="hidden" name="hidden_payment_image" id="hidden_payment_image{{$key}}" value="-">
                                        <span>-</span>
                                    </center>
                                @endif
                                @if(isset($value->reference_id) && strlen($value->reference_id)>15)
                                    <span id="refidtrunc" data-toggle="tooltip" data-placement="right" title="{{$value->reference_id}}">{{Str::limit($value->reference_id,15) }}...</span>
                                @else
                                    {{$value->reference_id}}
                                @endif
                                <input type="hidden" name="hidden_reference_id" id="hidden_reference_id{{$key}}" value="{{$value->reference_id}}">
                            </td>
                            {{-- <td data-label="Ref Id">
                                @if(isset($value->reference_id) && strlen($value->reference_id)>15)
                                    <span id="refidtrunc" data-toggle="tooltip" data-placement="right" title="{{$value->reference_id}}">{{Str::limit($value->reference_id,15) }}...</span>
                                @else
                                    {{$value->reference_id}}
                                @endif
                                
                                <input type="hidden" name="hidden_reference_id" id="hidden_reference_id{{$key}}" value="{{$value->reference_id}}">
                            </td> --}}
                            <td data-label="Receipt Image">
                                @if($value->receipt_image != null || $value->receipt_image != "")
                                    <center>
                                        <button class="btn btn-sm btn-outline-primary viewReceipt" id="viewReceiptImage{{$key}}" data-id={{$key}}>
                                            <i class="fas fa-image"></i>
                                        </button>
                                    </center>
                                    <input type="hidden" name="hidden_receipt_image" id="hidden_receipt_image{{$key}}" value="{{$value->receipt_image}}" data-uploaded_at="{{$value->uploaded_at}}">
                                @else
                                    <center>
                                        <span>-</span>
                                        <input type="hidden" name="hidden_receipt_image" id="hidden_receipt_image{{$key}}" value="-">
                                    </center>
                                @endif
                            </td>
                            
                            <td data-label="Lead Owner">{{$value->username}}</td>
                            <td data-label="Location">
                                {{$value->location}}
                            </td>
                            <td data-label="Assigned To">
                                {{$value->assigned_to}}
                            </td>
                            {{-- <td>
                                <span>{{$value->expense_amt}} - {{$value->expense_type}}</span>
                            </td> --}}
                            <td data-label="Expenses">
                                {{$value->expenses + $value->expense_amt}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @php
            $append_arr = array();
            if(isset($filter_arr['cust_name'])){
                $append_arr['filter_customer_name'] = $filter_arr['cust_name'];
            }
            if(isset($filter_arr['delivery_boy'])){
                $append_arr['filter_delivery_boy'] = $filter_arr['delivery_boy'];
            }
            if(isset($filter_arr['cust_no'])){
                $append_arr['filter_contact_no'] = $filter_arr['cust_no'];
            }
            if(isset($filter_arr['patient_name'])){
                $append_arr['filter_patient_name'] = $filter_arr['patient_name'];
            }
            if(isset($filter_arr['city'])){
                $append_arr['filter_city'] = $filter_arr['city'];
            }
            if(isset($filter_arr['from_date'])){
                $append_arr['filter_from_date'] = $filter_arr['from_date'];
            }
            if(isset($filter_arr['end_date'])){
                $append_arr['filter_end_date'] = $filter_arr['end_date'];
            }
            if(isset($filter_arr['order_id'])){
                $append_arr['filter_order_id'] = $filter_arr['order_id'];
            }
            if(isset($filter_arr['delivery_status'])){
                $append_arr['filter_delivery_status'] = $filter_arr['delivery_status'];
            }
            if(isset($filter_arr['order_type'])){
                $append_arr['filter_order_type'] = $filter_arr['order_type'];
            }
            if(isset($filter_arr['order_state'])){
                $append_arr['filter_order_state'] = $filter_arr['order_state'];
            }
            if(isset($filter_arr['lead_owner'])){
                $append_arr['filter_lead_owner'] = $filter_arr['lead_owner'];
            }
            if(isset($filter_arr['customer_type'])){
                $append_arr['customer_type'] = $filter_arr['customer_type'];
            }
            if(isset($filter_arr['filter_payment_mode'])){
                $append_arr['filter_payment_mode'] = $filter_arr['filter_payment_mode'];
            }
            if(isset($filter_arr['filterleadsource'])){
                $append_arr['filterleadsource'] = $filter_arr['filterleadsource'];
            }
            if(isset($filter_arr['filterproducts'])){
                $append_arr['filterproducts'] = $filter_arr['filterproducts'];
            }
            if(isset($filter_arr['filtercategories'])){
                $append_arr['filtercategories'] = $filter_arr['filtercategories'];
            }
            if(isset($filter_arr['onorderdate'])){
                $append_arr['onorderdate'] = $filter_arr['onorderdate'];
            }
        @endphp
        {{$non_settled_orders->appends($append_arr)->links('Custom.Pagination.pagination')}}
    </div>
   
    {{-- View Payment Image --}}
    <div class="modal fade" id="viewPaymentImage" tabindex="-1" role="dialog" aria-labelledby="viewPaymentImage" aria-hidden="true" style="text-align: center;">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewPaymentImage">Payment Image</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">                    
                    <div class="col-auto">
                        <img src="{{url('/')}}/assets/images/logo2-1 old.png" id="PaymentImagePath" height="60%" width="60%" alt="No Image Found">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- View Receipt Image --}}
    <div class="modal fade" id="viewReceiptImage" tabindex="-1" role="dialog" aria-labelledby="viewReceiptImage" aria-hidden="true" style="text-align: center;">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewReceiptImage">Receipt Image</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <center>
                        <img src="{{url('/')}}/assets/images/logo2-1 old.png" id="ReceiptImagePath" height="60%" width="60%" alt="No Image Found">
                    </center>
                    <center>
                        Uploaded At : <span id="uploaded_at_span"><b></b></span>
                    </center>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- View Order Details --}}
    <div class="modal fade " id="viewOrderDetails" tabindex="-1" role="dialog" aria-labelledby="viewOrderDetails" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewOrderDetails"><span id="order_type_title"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="container-fluid mt-2">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-4 text-right">Customer Name :</div>
                                <div class="col-md-8 text-left"><h5><span id="vieworder_customername"></span></h5></div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 text-right">Mobile No :</div>
                                <div class="col-md-8 text-left"><span id="vieworder_mobileno">1234567890</span></div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 text-right">Email Id :</div>
                                <div class="col-md-8 text-left"><span id="vieworder_email">1234567890</span></div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 text-right">Location :</div>
                                <div class="col-md-8 text-left"><span id="vieworder_location">1234567890</span></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Address</h5>
                                    <address id="vieworder_address"></address>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 text-right">Del Assigned To :</div>
                                <div class="col-md-8 text-left"><span id="vieworder_del_assigned_to">1234567890</span></div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 text-right">Helpers :</div>
                                <div class="col-md-8 text-left"><span id="vieworder_helpers">1234567890</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-body" id="order_details_modal_body"> <!-- remove class="modal-body" -->
                </div>
                <div id="accordion">
                    <div class="card">
                        <div class="card-header" id="headingOne"  data-toggle="collapse" data-target="#product-images" aria-expanded="true" aria-controls="product-images">
                            <h5 class="mb-0">
                                <span class="btn btn-outline-primary">Show Images</span>
                            </h5>
                        </div>
                        <div id="product-images" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade " id="updateCollectionOrder" tabindex="-1" role="dialog" aria-labelledby="updateCollectionOrder" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateCollectionOrder"><span id="order_type_title">Update Collection (Credit-Debit)</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="container-fluid mt-2">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-4 text-right">Customer Name :</div>
                                <div class="col-md-8 text-left"><h5><span id="updateco_customername"></span></h5></div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 text-right">Mobile No :</div>
                                <div class="col-md-8 text-left"><span id="updateco_mobileno">1234567890</span></div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 text-right">Email Id :</div>
                                <div class="col-md-8 text-left"><span id="updateco_email">1234567890</span></div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 text-right">Location :</div>
                                <div class="col-md-8 text-left"><span id="updateco_location">1234567890</span></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Address</h5>
                                    <address id="updateco_address"></address>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 text-right">Del Assigned To :</div>
                                <div class="col-md-8 text-left"><span id="updateco_del_assigned_to">1234567890</span></div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 text-right">Helpers :</div>
                                <div class="col-md-8 text-left"><span id="updateco_helpers">1234567890</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <form action="{{route('update-collection')}}" method="POST">
                    @csrf
                    <input type="hidden" name="order_id" id="updateco_orderid">
                    <div class="modal-body" id="update_collection_modal_body">
                    </div>
                    <div class="row form-group">
                        <div class="col text-center">
                            <button class="btn btn-sm btn-outline-success">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

        {{-- Update Order --}}
        <div class="modal fade" id="updateOrder" tabindex="-1" role="dialog" aria-labelledby="updateOrder" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateOrder">update Order/Payment</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                        <div class="modal-body">
                            <form class="form" method="post" action="{{url('/')}}/getOrderDetails" id="form1" enctype='multipart/form-data'>
                                @csrf
                                <div class="form-group invoice-field row">
                                    <div class="col-md-3">
                                        <input type="hidden" name="hidden_order_id_inv" id="hidden_order_id_inv">
                                        <input type="hidden" name="request_type_inv" id="request_type_inv" value="update-invoice-id">
                                        <label for="update_ref_id">Invoice Id</label>
                                    </div>
                                    <div class="col-md-7">
                                        <input type="text" class="form-control form-control-sm" name="invoice_id" id="invoice_id"required>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-sm btn-outline-success" id="update_invoice">Update</button>
                                    </div>
                                </div>
                            </form>
                            <form class="form" method="post" action="{{url('/')}}/getOrderDetails" id="form2" enctype='multipart/form-data'>
                                @csrf
                            <input type="hidden" name="hidden_order_id" id="hidden_order_id">
                            <input type="hidden" name="request_type" id="request_type" value="update-pay-details">
                                <input type="hidden" name="hidden_invoice_id" id="hidden_invoice_id">
                            
                            <label for="update_payment_mode">Payment Mode:</label>
                            <input class="form-radio" type="radio" name="payment_mode" id="Online" value="Online"> <label for="Online">Online</label>
                            <input class="form-radio" type="radio" name="payment_mode" id="Cash" value="Cash"> <label for="Cash">Cash</label>
                            <input class="form-radio" type="radio" name="payment_mode" id="Both" value="Both"> <label for="Both">Both</label>
                            <br>
                            <div id="update_ref_id_div">
                                <label for="update_ref_id">Receipt/Reference Id.</label>
                                <input type="text" class="form-control" name="update_ref_id" id="update_ref_id"required>
                            </div>
                            <br>
                                <div id="payment_file_div">
                                    <label for="payment_file">Payment Image..</label><input class="images" type="file" name="payment_file" id="payment_file">
                                </div>
                            <br>
                                <div id="receipt_file_div">
                                    <label for="receipt_file">Receipt Image..</label><input class="images" type="file" name="receipt_file" id="receipt_file">
                                </div>
                                <label for="comment">Comment</label>
                                <textarea name="comment" class="form-control" id="comment" cols="5" rows="3"></textarea>
                            {{-- <input type="hidden" name="hidden_order_id" id="hidden_order_id"> --}}
                            <div class="card" id="">
                                <div class="card-header"> 
                                    <strong>Expenses</strong>
                                </div>
                                <div class="card-body">
                                    <div id="labourChargesCard">
                                        <div class="row">
                                            <div class="col-md-5 text-right">
                                                <label for="">Floor No:</label>
                                            </div>
                                            <div class="col-md-7">
                                                <input type="number" class="form-control form-control-sm" name="floor_no" id="floor_no">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-5 text-right">
                                                <label for="">Labour Charges:</label>
                                            </div>
                                            <div class="col-md-7">
                                                <input type="number" class="form-control form-control-sm" name="labour_charges" id="labour_charges">
                                            </div>
                                        </div>
                                    </div>
                                    @if(in_array(session('user_id'),[14,97,19]))
                                        <div class="row">
                                            <div class="col-md-5 text-right">
                                                <label for="">Order Expense:</label>
                                            </div>
                                            <div class="col-md-7">
                                                <input type="number" class="form-control form-control-sm" name="order_expense_pay" id="order_expense_pay" value="0" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-5 text-right">
                                                <label for="">Vendor Charges:</label>
                                            </div>
                                            <div class="col-md-7">
                                                <input type="number" class="form-control form-control-sm" name="vendor_charges_pay" id="vendor_charges_pay" value="0" required>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>  
                                <br>
                                <center><button type="submit" id="update_order" class="btn btn-outline-success">update</button></center>
                                @if(in_array(session('user_id'),config('app.accounts_id_array')) || session('user_id') == '19')
                                <br>
                                    <span class="text-center">To update payement details,invoice no and direct settle order click below button</span>
                                    <center><button type="submit" class="btn btn-sm btn-outline-success" name="submit" value="submit-settle">Update & Settle</button></center>
                                @endif
                            </form>  
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    {{-- Settle Order --}}
    <div class="modal fade" id="settleOrder" tabindex="-1" role="dialog" aria-labelledby="settleOrder" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="settleOrder">Settle Order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form" method="post" action="{{url('/')}}/getOrderDetails" enctype='multipart/form-data'>
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="hidden_order_id_set" id="hidden_order_id_set">
                        <input type="hidden" name="request_type" id="request_type" value="update-status">
                        <label for="update_order_comment">Comment</label>
                        <textarea class="form-control" name="update_order_comment" id="update_order_comment" cols="30" rows="10" required></textarea>
                        {{-- <br>
                        <label for="payment_file">Payment Image..</label><input class="images" type="file" name="payment_file" id="payment_file">
                        <br>
                        <label for="receipt_file">Receipt Image..</label><input class="images" type="file" name="receipt_file" id="receipt_file"> --}}
                        {{-- <input type="hidden" name="hidden_order_id" id="hidden_order_id"> --}}
                    </div>
                    <span id="display_error_text" style="color:red; display:none;"></span>
                    <br>
                    <div class="modal-footer">
                        <button type="submit" id="settle_order" class="btn btn-outline-success">Settle</button>    
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Bank details modal --}}
    <div class="modal fade" id="bankDetailsModal" tabindex="-1" role="dialog" aria-labelledby="bankDetailsModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bankDetailsModal">Bank Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <strong><span>Bank Name : </span></strong>
                        </div>
                        <div class="col-md-8">
                            <span id="span_bank_name"></span>
                        </div>
                    </div>
                    <div class="row my-2">
                        <div class="col-md-4">
                            <strong><span>Branch Name : </span></strong>
                        </div>
                        <div class="col-md-8">
                            <span id="span_branch_name"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <strong><span>Account No : </span></strong>
                        </div>
                        <div class="col-md-8">
                            <span id="span_account_no"></span>
                        </div>
                    </div>
                    <div class="row my-2">
                        <div class="col-md-4">
                            <strong><span >IFSC Code : </span></strong>
                        </div>
                        <div class="col-md-8">
                            <span id="span_ifsc_code" class="text-uppercase"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <strong><span>Account Type : </span></strong>
                        </div>
                        <div class="col-md-8">
                            <span id="span_account_type"></span>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>

    <div class="modal fade" id="adjustmentDetailsModal" tabindex="-1" role="dialog" aria-labelledby="adjustmentDetailsModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="adjustmentDetailsModal">Adjustment Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body adjustments">
                </div>
            </div>
        </div>
    </div>

    {{-- upload image --}}
    <div class="modal fade" id="modal_upload_image" tabindex="-1" role="dialog" aria-labelledby="upload_image" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="upload_image">Upload Image for <span id="span_order_id">0000000</span></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form" method="post" action="{{url('/')}}/delivery_upload_image" enctype='multipart/form-data'>
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="hidden_order_id" id="hidden_order_id_upimg">
                        <label for="image_file">Upload Image..</label><input type="file" name="image_file" id="image_file">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="btn_upload_image" title="Upload Image">Submit</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- closed order --}}
    <div class="modal fade" id="orderClosedModal" tabindex="-1" role="dialog" aria-labelledby="orderClosedModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderClosedModal">Close Order - <strong>"<span id="close_orderid_span"></span>"</strong></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                {{-- <div class="container"> --}}
                   
                {{-- </div> --}}
                <form action="{{route('order-close')}}" method="post">
                    @csrf
                    <div class="modal-body container-fluid">
                        <div class="table table-responsive" id="closedOrderProductTableDiv">
                            <table class="table">
                                <thead>
                                    <th>Product name</th>
                                    <th>Sale/Rent</th>
                                    <th>Rent/price</th>
                                    <th>Deposit</th>
                                    <th>Transport</th>
                                </thead>
                                <tbody id="closedOrderProductTable"></tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="">Transportation Charged? &emsp;</label>
                                <input type="radio" name="transport_taken_not" id="yes" value="Yes" required><label for="yes">Yes</label> &emsp;
                                <input type="radio" name="transport_taken_not" id="no" value="No" required><label for="no">No</label>
                            </div>
                        </div>
                        <div class="row">
                            <input type="hidden" name="order_id" id="order_close_orde_id">
                            <select class="form-control form-control-sm selectpicker border border-dark order-close-reason" name="close_reason" id="close_reason" 
                                title="Select Reason"  required>
                                @foreach ($orderClosedReason as $key=>$reason)
                                    <option value="{{$key}}">{{$reason}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row mt-2">
                            <textarea class="form-control form-control-sm " name="close_remark" id="close_remark" placeholder="Remark..." cols="30" rows="5" ></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-outline-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- add labour --}}
    <div class="modal fade" id="addLabourModal" tabindex="-1" role="dialog" aria-labelledby="addLabourModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <strong>Add Labour Charges</strong>
                </div>
                <form class="form" method="post" action="{{route('addLabourCharges')}}" enctype='multipart/form-data'>
                    @csrf
                    {{-- hidden --}}
                    <input type="hidden" name="labour_order_id" id="labour_order_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 text-right">
                                <label for="">Floor No:</label>
                            </div>
                            <div class="col-md-8">
                                <input type="number" class="form-control form-control-sm" name="floor_no" id="floor_no">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 text-right">
                                <label for="">Labour Charges:</label>
                            </div>
                            <div class="col-md-8">
                                <input type="number" class="form-control form-control-sm" name="labour_charges" id="labour_charges">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-outline-success btn-sm" id="btn_upload_image" title="Upload Image">Submit</button>
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Activity Log -Modal- --}}

    <div class="modal" id="activityLogModal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-lg " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    -
                </div>
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Activity Log</h5>
                                <div class="table table-responsive jim-table-responsive" id="activity_logs_div">
                                    <table class="table table-dark" id="activity_logs_table">
                                        <thead>
                                            <tr>
                                                <th>Updated On</th>
                                                <th class="text-nowrap">Updated By</th>
                                                <th>Updated Details</th>
                                            </tr>
                                        </thead>
                                        <tbody id="activity_logs_tbody">
                                            <tr>
                                                <td colspan="3" class="text-center">
                                                    <span id="initial_text">Loading...</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- time line modal --}}
    <div class="modal fade" id="timelineModal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="row d-flex justify-content-center">
                <div class="main-card card">
                    <div class="card-body">
                        <h5 class="card-title">Order Timeline</h5>
                        <div class="vertical-timeline vertical-timeline--animate vertical-timeline--one-column" id="append_div">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Update Closed Order --}}
    <div class="modal fade" id="orderClosedUpdateModal" tabindex="-1" role="dialog" aria-labelledby="orderClosedUpdateModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderClosedUpdateModal">Update Close Order - <strong>"<span id="update_closed_order_id_span"></span>"</strong></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('getOrderDetails')}}" method="post">
                    @csrf
                    <div class="modal-body container-fluid">
                        <div class="row form-group">
                            <div class="col-md-12">
                                <input type="hidden" name="update_closed_order_order_id" id="update_closed_order_order_id" >
                                <input type="hidden" name="request_type" value="update-closed-order-transport" id="update_closed_order_request_type">
                                <label for="transport_update">Transport</label>
                                <input type="text" name="transport_update_cost" id="transport_update_cost" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-outline-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addCommentModel" tabindex="-1" role="dialog" aria-labelledby="addCommentModel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCommentModel">Add Comment - <strong>"<span id="addcomment_orderid"></span>"</strong></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('add-comment')}}" method="post">
                    @csrf
                    <input type="hidden" name="addcomment_hiddenorderid" id="addcomment_hiddenorderid">
                    <div class="modal-body container-fluid">
                        <div class="row form-group">
                            <div class="col-md-12">
                                <span>Comment: </span>
                                <span id="old_comment" name="old_comment"></span>
                            </div>
                            <div class="col-md-12">
                                <textarea name="updatedcomment" class="form-control" id="updatedcomment" cols="5" rows="5"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-outline-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalImage" tabindex="-1" role="dialog" aria-labelledby="modalImageTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
                <img class="img-fluid img-thumbnail" src="" alt="" id="modal_image" height="300" width="300">
          </div>
        </div>
    </div>

    <div class="modal fade" id="adjustmentModal" tabindex="-1" role="dialog" aria-labelledby="adjustmentModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <form action="{{route('adjustment-details')}}" method="POST">
                    @csrf
                    <input type="hidden" name="request_type" id="request_type" value="adjust-deposit-details">
                    <input type="hidden" name="source" id="source" value="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="adjustmentModalTitle">Adjust Deposit/Rent</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body container-fluid">
                        <div class="order-product-details">
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <label for="inorderproduct">Adjust In Product</label>
                                    <select class="select ad-product selectpicker form-control form-control-sm" title="Select Product" data-live-search="true" data-size="5" name="inorderproduct" id="inorderproduct">

                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="fromorderproduct">Adjust From Product</label>
                                    <select class="select ad-product selectpicker form-control form-control-sm" title="Select Product" data-live-search="true" data-size="5" name="fromorderproduct" id="fromorderproduct">

                                    </select>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-12 text-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary" name="getadjustmentdetails" id="getadjustmentdetails">Get Details</button>
                                </div>
                            </div>
                            <div class="ad-details">

                            </div>
                            <div class="error-texts">

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-outline-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Adjustment Modal --}}
    <div class="modal fade" id="adjustmentDamageModal" tabindex="-1" role="dialog" aria-labelledby="adjustmentDamageModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <form action="{{route('adjustment-details')}}" method="POST">
                    @csrf
                    <input type="hidden" name="request_type" id="request_type" value="adjust-againts-damage">
                    <input type="hidden" name="source_dm" id="source_dm" value="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="adjustmentDamageModalTitle">Damage Adjustment</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body container-fluid">
                        <div class="order-product-details">
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <label for="inorderproductdm">Adjust In Product</label>
                                    <select class="select ad-product selectpicker form-control form-control-sm" title="Select Product" data-live-search="true" data-size="5" name="inorderproductdm" id="inorderproductdm" required>

                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="available_deposit_dm">Available total deposit</label>
                                    <input type="number" class="form-control form-control-sm" name="available_deposit_dm" id="available_deposit_dm" value="0" disabled>
                                </div>
                                <div class="col-md-3">
                                    <label for="adjusted_deposit_dm">Adjust in Damage</label>
                                    <input type="number" class="form-control form-control-sm" name="adjusted_deposit_dm" id="adjusted_deposit_dm" value="0" required>
                                </div>
                            </div>
                            <div class="error-texts-dm">

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-outline-primary" name="dmsubmit" id="dmsubmit" value="0">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="hidden-routes">
        <input type="hidden" id="adjustment-details" value="{{route('adjustment-details')}}">
    </div>
     {{-- other expense modal --}}
     <div class="modal fade" id="other-expense-modal" tabindex="-1" role="dialog" aria-labelledby="other-expense-modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{route('order-other-expense')}}" method="POST">
                    @csrf
                    {{-- <input type="hidden" name="request_type" id="request_type" value="adjust-deposit-details">
                    <input type="hidden" name="source" id="source" value=""> --}}
                    <input type="hidden" name="order_id" id="other_exp_order_id" value="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="other-expense-modalTitle">Other Traveling Expense</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body container-fluid">
                        <div class="row">
                            <label for="amount">Amount :</label>
                            <input type="number" class="form-control" name="expense_amt" id="expense_amt" required>
                        </div>
                        <div class="row" id="expense_type_select_div">
                            <label for="amount">Type :</label>
                            <select name="expense_type" id="expense_type_select" class="selectpicker form-control" title="select type" required>
                                <option value="Ola">Ola</option>
                                <option value="Uber">Uber</option>
                                <option value="Porter">Porter</option>
                                <option value="Rapido">Rapido</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="row d-none" id="expense_type_input_div">
                            <label for="">Other : </label>
                            <input type="text" class="form-control" name="other_exp_type" id="other_exp_type_input">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-outline-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="crdr-data-modal" tabindex="-1" role="dialog" aria-labelledby="crdr-data-modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <input type="hidden" name="order_id" id="other_exp_order_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="crdr-data-modalTitle">Credit Debit Notes</h5>
                    <button type="button" class="close btn-sm" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body container-fluid crdrnotes-body">
                    <span class="text-center">Loading!</span>
                </div>
                <div class="remarks text-center">
                    <span>Note : RE-Rent/Rate(Collection)&emsp;R-Rent/Rate&emsp;D-Deposit&emsp;T-Transport&emsp;DM-Damage&emsp;Cr-Credit&emsp;Dr-Debit</span>
                </div>
                <div class="crdrimg text-center">
                    <div class="accordion" id="accordionExample">
                        <div class="card">
                            <div class="card-header" id="headingOneaa">
                                <h2 class="mb-0">
                                    <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        Show Payment Image
                                    </button>
                                </h2>
                            </div>
                            <div id="collapseOne" class="collapse show" aria-labelledby="headingOneaa" data-parent="#accordionExample">
                                <div class="card-body">
                                    <form action="{{route('upload-crdr-note-img')}}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="hidden_order_id_crdr_img" id="hidden_order_id_crdr_img">
                                        <input type="file" name="uploadimagecrdrnote" id="uploadimagecrdrnote" class="form-control form-control-sm">
                                        <button type="submit" class="btn btn-sm btn-outline-primary my-2"><i class="fas fa-upload"></i> Upload</button>
                                    </form>
                                    <img src="" class="img img-fluid" name="viewimagecrdrnote" id="viewimagecrdrnote" alt="No Image Found">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-sm btn-outline-primary">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="ccrequestModal" tabindex="-1" role="dialog" aria-labelledby="ccrequestModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-primary" id="ccrequestModalTitle">Cash Collection against Delivery</h5>
                    <button type="button" class="close btn-sm" data-dismiss="modal" aria-label="Close" onclick="$('.loading-spinner').show();$('.ccad-modal-content').show();">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body container-fluid crdrnotes-body">
                    <div class="loading-spinner" style="display: none;">
                        <div class="d-flex justify-content-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <span class="text-primary"><h4>Loading...</h4></span>
                        </div>
                    </div>
                    <div class="ccad-modal-content">
                        <form action="{{route('ccad.store')}}" method="POST" class="form ccad-form">
                            @csrf
                            <input type="hidden" name="_method" id="_method" value="PUT">
                            <input type="hidden" name="ccadorderid" id="ccadorderid">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="ccaddate">Date</label>
                                    <input type="date" name="ccaddate" id="ccaddate" class="form-control form-control-sm" value="{{date('Y-m-d')}}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="ccaddelassigned">Task Assigned to</label>
                                    <select name="ccaddelassigned" id="ccaddelassigned" class="selectpicker form-control form-control-sm" title="Select Delboy" data-live-search="true" required>

                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="ccadorderamount">Order Amount</label>
                                    <input type="number" name="ccadorderamount" id="ccadorderamount" class="form-control form-control-sm" readonly disabled required>
                                </div>
                                <div class="col-md-6">
                                    <label for="ccadamounttocollect">Collect Amount</label>
                                    <input type="number" name="ccadamounttocollect" id="ccadamounttocollect" class="form-control form-control-sm" required>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="submit" class="btn btn-sm btn-outline-primary" name="btnsubmit" id="btnsubmit">Generate Cash Collection</button>
                            </div>
                        </form>
                    </div>
                </div>                
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" onclick="$('.loading-spinner').show();$('.ccad-modal-content').show();" data-dismiss="modal">Close</button>
                    {{-- <button type="submit" class="btn btn-sm btn-outline-primary">Submit</button> --}}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="createReplacement" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Search Customer / Patient</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                    <div class="accordian" id="replacementAccordian">
                    <div class="card d-none product-details-card">
                        <div class="card-header" id="headingProductDetails">
                            <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseProductDetails" aria-expanded="false" aria-controls="collapseProductDetails">
                                Product Details
                            </button>
                            </h2>
                        </div>
                        <div id="collapseProductDetails" class="collapse" aria-labelledby="headingProductDetails" data-parent="#replacementAccordian">
                            <div class="card-body">
                                <form action="{{route('replace-order-create')}}" method="POST">
                                    @csrf
                                    <div class="product-details">
                                        <div class="table table-responsive jim-table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>Delivery Date</th>
                                                        <th>Product Name</th>
                                                        <th>Vendor</th>
                                                        <th>Warehouse</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="product-table-records">
                
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="button-div d-flex justify-content-center">
                                                <button type="submit" class="btn btn-sm btn-outline-success" name="generateorder" value="replacement" id="generatereplacement">Generate Replacement</button>
                                                <button type="submit" class="btn btn-sm btn-outline-success ml-2" name="generateorder" value="sale" id="generatePurchase">Generate Sale Order</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
    <div class="modal fade" id="updateCorpPayments" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update Corporate Payments</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('corporate-renewal')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row form-group">
                            <div class="col-md-3">
                                <input type="text" name="corppay_invoice_no" id="corppay_invoice_no" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-sm btn-outline-success" id="corpay_search">Search</button>
                            </div>
                        </div>
                        <div id="corpay-product-details" style="display:none;">
                            <div class="corpay-heading text-center">
                                <h5>Customer Details</h5>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-4">
                                    <span>Customer Name:</span><span id="corpay_customer_name"></span>
                                </div>
                                <div class="col-md-4">
                                    <span>Contact No:</span><span id="corpay_contact_no"></span>
                                </div>
                                <div class="col-md-4">
                                    <span>Patient Name:</span><span id="corpay_patient_name"></span>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-12">
                                    <span>Address:</span><span id="corpay_address"></span>
                                </div>
                            </div>
                            <div class="corpay-heading text-center">
                                <h5>Product Details</h5>
                            </div>
                            <div class="table jim-table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Sr.No</th>
                                            <th>Product Name</th>
                                            <th>Period</th>
                                            <th>Rent</th>
                                        </tr>
                                    </thead>
                                    <tbody id="corpay-table">

                                    </tbody>
                                </table>
                            </div>
                            <div class="row form-group justify-content-center">
                                <div class="col-md-3">
                                    <label for="corpay_date">Date</label>
                                    <input type="date" name="corpay_date" id="corpay_date" class="form-control form-control-sm" value="{{date('Y-m-d')}}" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="corpay_reference_id">Reference Id</label>
                                    <input type="text" name="corpay_reference_id" id="corpay_reference_id" class="form-control form-control-sm" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="corpay_payment_img">Payment Image</label>
                                    <input type="file" name="corpay_payment_img" id="corpay_payment_img" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-sm btn-outline-success">Complete Payment</button>
                            </div>
                        </div>
                        <div id="corpay-error-text">

                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="updatePickupModal" tabindex="-1" aria-labelledby="updatePickupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updatePickupModalLabel">Update Pickup</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('update-pickup')}}" method="POST">
                    @csrf
                    <div class="container-fluid mt-2">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-4 text-right">Customer Name :</div>
                                    <div class="col-md-8 text-left"><h5><span id="updatepickup_customername"></span></h5></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 text-right">Mobile No :</div>
                                    <div class="col-md-8 text-left"><span id="updatepickup_mobileno">1234567890</span></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 text-right">Email Id :</div>
                                    <div class="col-md-8 text-left"><span id="updatepickup_email">1234567890</span></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 text-right">Location :</div>
                                    <div class="col-md-8 text-left"><span id="updatepickup_location">1234567890</span></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5>Address</h5>
                                        <address id="updatepickup_address"></address>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 text-right">Del Assigned To :</div>
                                    <div class="col-md-8 text-left"><span id="updatepickup_del_assigned_to">1234567890</span></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 text-right">Helpers :</div>
                                    <div class="col-md-8 text-left"><span id="updatepickup_helpers">1234567890</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div id="pickupmodalbody">

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-sm btn-outline-success">Update</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script>
        // Activity Log -script-
        function activityLog(order_id,order_type){
            if(order_id && order_type){
                let dataString = ({_token:"{{ csrf_token() }}",order_id:""+order_id,order_type:""+order_type});
                $.ajax({
                    type: "GET",
                    url: "{{route('get-activity-log')}}",
                    data: dataString,
                    cache:false,
                    success: function(data){
                        // console.log(data);
                        if(data.length>0){
                            console.log(data);
                            let tbody="";
                            for(var i = 0; i<data.length; i++){
                                if(data[i].old_value != data[i].new_value){
                                    let string = data[i].fields+" changed from "+data[i].old_value+" to "+data[i].new_value;
                                    tbody += '<tr>';
                                        tbody += '<td>'+data[i].updated_at+'</td>';
                                        tbody += '<td>'+data[i].updated_by+'</td>';
                                        tbody += '<td>'+string+'</td>';
                                    tbody += '</tr>';
                                }else{
                                    continue;
                                }
                            }
                            $("#activity_logs_tbody").empty();
                            $("#activity_logs_tbody").append(tbody);
                            $("#activity_logs_table").dataTable();
                        }else{
                            let tbody="";
                            tbody += '<tr>';
                                tbody += '<td colspan="3" class="text-center">';
                                tbody += '<span id="initial_text">Loading...</span>';
                                tbody += '</td>';
                            tbody += '</tr>';
                            $("#activity_logs_tbody").empty();
                            $("#activity_logs_tbody").append(tbody);
                            $("#initial_text").text("No Records Found");
                        }
                    },
                    error: function(er){
                        console.log(er);
                    }
                });
            }else{
                return false;
            }
        }
        $(".updatePickup").click(function(){
            let id = $(this).data("id");
            let order_type = $(this).data("order_type");
            let order_id = $(this).data("order_id");
            if(order_type == "Pick Up")
            {
                $("#pickupmodalbody").empty();
                var dataString = ({_token:"{{ csrf_token() }}",order_id:""+order_id,request_type:"Pick Up"});
                console.log(dataString);
                $.ajax({
                    type: "POST",
                    url: "{{url('/')}}/getOrderDetails",
                    data: dataString,
                    cache:false,
                    success: function (data)
                    {
                        console.log(data);
                        if(data.length != 0)
                        {
                            let details_count = data.length;
                            let table = '<div class="table table-responsive jim-table-responsive">';
                                table += '<input type="hidden" name="update_pickup_order_id" value="'+order_id+'">';
                                table += '<table class="table" id="update_pickup_table">';
                                    table += '<thead>';
                                        table += '<th>Sr. No</th>';
                                        table += '<th>Date</th>';
                                        table += '<th>Product Name</th>';
                                        table += '<th>Vendor Name</th>';
                                        table += '<th>Warehouse Name</th>';
                                        // table += '<th>Customer Name</th>';
                                        // table += '<th>Customer Contact</th>';
                                        // table += '<th>Vendor Rent</th>';
                                        table += '<th>Product Deposit</th>';
                                        table += '<th>Transport</th>';
                                        table += '<th>Transport taken</th>';
                                        // table += '<th>Status</th>';
                                    table += '</head>';
                                    table += '<tbody>';
                                    for (let i = 0; i < details_count; i++)
                                    {
                                        let sr_no = i+1;
                                        table += '<tr class="text-wrap">';
                                            table +='<td data-label="Sr.No.">'+sr_no+'</td>';
                                            table +='<td data-label="Date">'+data[i].date+'</td>';
                                            // table +='<td data-label="Product Name">'+data[i].product_name+'</td>';
                                            if(data[i].sale_rental == 'Rental'){
                                                table +='<td data-label="Product Name">'+data[i].product_name+'<br>('+data[i].unique_id+')</td>';
                                            }else{
                                                table +='<td data-label="Product Name">'+data[i].product_name+'</td>';
                                            }
                                            table +='<td data-label="Vendor Name">'+data[i].vendor_name+'</td>';
                                            table +='<td data-label="Warehouse">'+data[i].warehouse_name+', '+data[0].warehouse_area+', '+data[0].warehouse_city+'</td>';
                                            // table +='<td>'+data[i].customer_name+'</td>';
                                            // table +='<td>'+data[i].primary_contact_no+'</td>';
                                            // table +='<td>'+data[i].vendor_rent+'</td>';
                                            table +='<td data-label="Deposit">'+data[i].product_deposite+'</td>';
                                            if(data[i].transport==null){
                                                table +='<td data-label="Transport">0</td>';
                                            }else{
                                                table +='<td data-label="Transport">'+data[i].transport+'</td>';
                                            }
                                            table +='<td data-label="Transport"><input type="number" class="form-control form-control-sm" name="transportTaken[]" value="'+data[i].transport+'"><input type="hidden" class="form-control form-control-sm" name="pickupIds[]" value="'+data[0].pickup_id+'"></td>';
                                            // table +='<td>'+data[i].start_date+' - '+data[i].end_date+'</td>';
                                        table += '</tr>';
                                    }
                                    table += '</tbody>';
                                table += '</table>';
                            table += '</div>';
                            if(data[0].paid_total_amount == '' || data[0].paid_total_amount == null)
                            {
                                data[0].paid_total_amount = 0;
                            }
                            if(data[0].received_total_amount==null || data[0].received_total_amount== '' ){
                                data[0].received_total_amount= 0;
                            }
                            table +='<div class="container container-fluid">';
                                table += '<div class="row"><div class="col-md-12 text-right"><span>Total Amount : </span><span>'+data[0].total_amount+'</span></div></div>';
                                table += '<div class="row"><div class="col-md-12 text-right"><span>Received Amount : </span><span>'+data[0].paid_total_amount+'</span></div></div>';
                                table += '<div class="row"><div class="col-md-12 text-right"><span>Remaining : </span><span>'+(data[0].total_amount - data[0].paid_total_amount)+'</span></div></div>';
                            table += '</div>';
                            // location.reload();
                            $('#updatepickup_customername').text(data[0].shipping_first_name);
                            $('#updatepickup_mobileno').text(data[0].mobileno);
                            $('#updatepickup_email').text(data[0].email_id);
                            $('#updatepickup_location').text(data[0].location);
                            $('#updatepickup_address').text(data[0].address_line_1+','+data[0].address_line_2+','+data[0].area+','+data[0].landmark+','+data[0].city+','+data[0].pincode);
                            $("#updatepickup_del_assigned_to").text(data[0].DelAssignedTo);
                            $("#updatepickup_helpers").text(data[0].helpers);
                            $("#pickupmodalbody").append(table);
                            // $("#order_details_table").dataTable();
                        }
                        else
                        {
                            let heading = '<h5 class="text-center">Something Went Wrong!</h5>';
                            $("#pickupmodalbody").append(heading);
                        }
                    }
                });
                $("#updatePickupModal").modal('show');
            }
        });
    </script>
    <script>
        $("#corpay_search").click(function(){
            $("#corpay-error-text").empty();
            $('#corpay-product-details').hide();
            if($("#corppay_invoice_no").val() == null || $("#corppay_invoice_no").val() == ""){
                $("#corpay-error-text").append("<span class='text-danger'>Invoice no can't be empty</span>");
            }else{
                let dataString = {_token:config.postval.token,invoice_no:$("#corppay_invoice_no").val(),request_type:"get-invoice-products"};
                $.ajax({
                    type: "POST",
                    url : "{{route('corporate-renewal')}}",
                    data: dataString,
                    cache:false,
                    success: function (response)
                    {
                        console.log(response);
                        if(response.status =='success')
                        {
                            // $('#corpay-product-details').append("Success");
                            $("#corpay_customer_name").text(response.data[0].shipping_first_name);
                            $("#corpay_patient_name").text(response.data[0].patient_name);
                            $("#corpay_contact_no").text(response.data[0].mobileno);
                            $("#corpay_address").text(response.data[0].fulldetails);
                            let tr = null;
                            Object.keys(response.data).forEach(function(key){
                                tr += "<tr>";
                                    tr += "<td data-label='Sr.No'>"+(parseInt(key)+parseInt(1))+"</td>";
                                    tr += "<td data-label='Product Name'>"+response.data[key].product_name+"</td>";
                                    tr += "<td data-label='Period'>"+response.data[key].start_date+" to "+response.data[key].end_date+"</td>";
                                    tr += "<td data-label='Rent'>"+response.data[key].amount+"</td>";
                                tr += "</tr>";
                            });
                            $("#corpay-table").empty();
                            $("#corpay-table").append(tr);
                            $('#corpay-product-details').show();
                        }
                        else if(response.status == 'error')
                        {
                            let div = "<div class='text-center text-danger'>";
                                    div += "<span>"+response.description+"</span>"
                                div += "</div>";
                            $('#corpay-error-text').append(div);
                        }
                        else
                        {
                            let div = "<div class='text-center text-danger'>";
                                    div += "<span>Someting went wrong, Try Again!</span>"
                                div += "</div>";
                            $('#corpay-error-text').append(div);
                        }
                    },
                    error: function(e){
                        let div = "<div class='text-center text-danger'>";
                                div += "<span>Someting went wrong, Try Again!</span>"
                            div += "</div>";
                        $('#corpay-error-text').append(div);
                    }
                });
            }
        });
    </script>
    <script>
        $("#invoice_id").change(function(){
            $("#hidden_invoice_id").val($("#invoice_id").val());
        });
    </script>
    <script>
        $('.adjust-deposit').click(function(){
            // $('.order-product-details').empty();
            $('.error-texts').empty();
            $(".ad-details").empty();
            if($(this).data('order_type') != 'Pick Up'){
                let dataString = {_token:config.postval.token,orderid:$(this).data("order_id"),ordertype:$(this).data('order_type'),request_type:"order-product-details"};
                $("#source").val($(this).data('order_type'));
                $.ajax({
                    type: "GET",
                    // url: $("#adjustment-details").val(),
                    url : "{{route('adjustment-details')}}",
                    data: dataString,
                    cache:false,
                    success: function (response)
                    {
                        console.log(response);
                        if(response.status =='success')
                        {
                            setData(response.data,response.availableproducts,'in');
                        }
                        else if(response.status == 'error')
                        {
                            let div = "<div class='text-center text-danger'>";
                                    div += "<span>"+response.description+"</span>"
                                div += "</div>";
                            $('.error-texts').append(div);
                        }
                        else
                        {
                            let div = "<div class='text-center text-danger'>";
                                    div += "<span>Someting went wrong, Try Again!</span>"
                                div += "</div>";
                            $('.error-texts').append(div);
                        }
                    },
                    error: function(e){
                        let div = "<div class='text-center text-danger'>";
                                div += "<span>Someting went wrong, Try Again!</span>"
                            div += "</div>";
                        $('.error-texts').append(div);
                    }
                });
                $("#adjustmentModal").modal("show");
            }
            else{
                let dataString = {_token:config.postval.token,orderid:$(this).data("order_id"),ordertype:$(this).data('order_type'),request_type:"order-product-details"};
                $("#source_dm").val($(this).data('order_type'));
                $("#dmsubmit").val($(this).data("order_id"));
                $.ajax({
                    type: "GET",
                    // url: $("#adjustment-details").val(),
                    url : "{{route('adjustment-details')}}",
                    data: dataString,
                    cache:false,
                    success: function (response)
                    {
                        console.log(response);
                        if(response.status =='success')
                        {
                            setData(response.data,response.availableproducts,'in');
                            $("#available_deposit_dm").val(response.availabledeposit);
                        }
                        else if(response.status == 'error')
                        {
                            let div = "<div class='text-center text-danger'>";
                                    div += "<span>"+response.description+"</span>"
                                div += "</div>";
                            $('.error-texts-dm').append(div);
                        }
                        else
                        {
                            let div = "<div class='text-center text-danger'>";
                                    div += "<span>Someting went wrong, Try Again!</span>"
                                div += "</div>";
                            $('.error-texts-dm').append(div);
                        }
                    },
                    error: function(e){
                        let div = "<div class='text-center text-danger'>";
                                div += "<span>Someting went wrong, Try Again!</span>"
                            div += "</div>";
                        $('.error-texts-dm').append(div);
                    }
                });
                $("#adjustmentDamageModal").modal("show");
            }
        });

        function setData(data,availableproducts,type)
        {
            if(type == 'in' && $("#source").val() == 'Repair')
            {
                $("#inorderproduct")
                    .find("option")
                    .remove()
                    .end();
                    data.forEach(function(element){
                        $("#inorderproduct").append("<option value='"+element.maintenance_id+"'>"+element.product_name+" - <small>"+element.unique_id+"</small></option>");
                    });
                $('#inorderproduct').selectpicker('refresh');
                $("#fromorderproduct")
                    .find("option")
                    .remove()
                    .end();
                    availableproducts.forEach(function(element){
                        $("#fromorderproduct").append("<option value='"+element.id+"'>"+element.product_name+" - <small>"+element.unique_id+"</small></option>");
                    });
                $('#fromorderproduct').selectpicker('refresh');
            }
            if(type == 'in' && $("#source").val() == 'Delivery')
            {
                $("#inorderproduct")
                    .find("option")
                    .remove()
                    .end();
                    data.forEach(function(element){
                        $("#inorderproduct").append("<option value='"+element.id+"'>"+element.product_name+" - <small>"+element.unique_id+"</small></option>");
                    });
                $('#inorderproduct').selectpicker('refresh');
                $("#fromorderproduct")
                    .find("option")
                    .remove()
                    .end();
                    availableproducts.forEach(function(element){
                        $("#fromorderproduct").append("<option value='"+element.id+"'>"+element.product_name+" - <small>"+element.unique_id+"</small></option>");
                    });
                $('#fromorderproduct').selectpicker('refresh');
            }
            else if(type == 'in' && $("#source").val() == 'Collection')
            {
                $("#inorderproduct")
                    .find("option")
                    .remove()
                    .end();
                    data.forEach(function(element){
                        $("#inorderproduct").append("<option value='"+element.renewalid+"'>"+element.product_name+" : <small>"+element.start_date+" - "+element.end_date+"</small></option>");
                    });
                $('#inorderproduct').selectpicker('refresh');
                $("#fromorderproduct")
                    .find("option")
                    .remove()
                    .end();
                    availableproducts.forEach(function(element){
                        $("#fromorderproduct").append("<option value='"+element.id+"'>"+element.product_name+" - <small>"+element.unique_id+"</small></option>");
                    });
                $('#fromorderproduct').selectpicker('refresh');
            }
            else if(type == 'in' && $("#source_dm").val() == 'Pick Up')
            {
                $("#inorderproductdm")
                    .find("option")
                    .remove()
                    .end();
                    data.forEach(function(element){
                        $("#inorderproductdm").append("<option value='"+element.id+"'>"+element.product_name+" - <small>"+element.unique_id+"</small></option>");
                    });
                $('#inorderproductdm').selectpicker('refresh');
            }
        }
        $(".ad-product").change(function(){
            let id = $(this).attr("id");
            if(id == 'fromorderproduct')
            {                
                document.querySelectorAll("#inorderproduct option").forEach(opt => {
                    if (opt.value == $(this).val()) {
                        opt.disabled = true;
                    }
                    else{
                        opt.disabled = false;
                    }
                });
            }
            else
            {
                document.querySelectorAll("#fromorderproduct option").forEach(opt => {
                    if (opt.value == $(this).val()) {
                        opt.disabled = true;
                    }
                    else{
                        opt.disabled = false;
                    }
                });
            }
        });
        $("#getadjustmentdetails").click(function(){
            $(".ad-details").empty();
            $(".error-texts").empty();
            if($("#fromorderproduct").val() && $("#inorderproduct").val())
            {
                let dataString = {fromorderproduct:$("#fromorderproduct").val(),inorderproduct:$("#inorderproduct").val(),ordertype:$("#source").val(),request_type:"adj-details"};
                console.log(dataString);
                $.ajax({
                    type: "GET",
                    // url: $("#adjustment-details").val(),
                    url : "{{route('adjustment-details')}}",
                    data: dataString,
                    cache:false,
                    success: function (response)
                    {
                        console.log(response);
                        if(response.status =='success')
                        {
                            let rawdata = '<div class="card"><div class="card-header">Adjust In</div> <div class="card-body"><div class="row form-group">';
                                    rawdata += '<div class="col-md-12"><span><h4>'+response.inorderproduct.product_name+' - '+response.inorderproduct.unique_id+'</h4></span></div>';
                                rawdata += '</div>';
                                rawdata += '<div class="row form-group">';
                                    rawdata += '<div class="col-md-12"><input type="radio" name="inadjust_rent_depo" id="inorderproductrent" value="rent"><label for="inorderproductrent" class="mr-2">Rent</label>';
                                    rawdata += '<input type="radio" name="inadjust_rent_depo" id="inorderproductdeposit" value="deposit" checked><label for="inorderproductdeposit">Deposit</label><input type="radio" name="inadjust_rent_depo" id="inorderproducttransport" value="transport"><label for="inorderproducttransport">Transport</label>';
                                rawdata += '</div></div>';
                                rawdata += '<div class="row form-group">';
                                    rawdata += '<div class="col-md-3"><label for="inorderproductactrent">Actual Rent</label><input type="text" class="form-control form-control-sm" id="inorderproductactrent" value="'+response.inorderproduct.product_rent+'" disabled></div>';
                                    rawdata += '<div class="col-md-1 text-center"><span><br>-</span></div>';
                                    rawdata += '<div class="col-md-3"><label for="inorderproductadjrent">Adjusted Rent</label><input type="text" class="form-control form-control-sm" id="inorderproductadjrent" value="'+response.inorderproduct.adjusted_rent+'" disabled></div>';
                                    rawdata += '<div class="col-md-1 text-center"><span><br>=</span></div>';
                                    rawdata += '<div class="col-md-4"><label for="inorderproductremrent">Remaining Rent</label><input type="text" class="form-control form-control-sm" id="inorderproductremrent" value="'+parseInt(response.inorderproduct.product_rent - response.inorderproduct.adjusted_rent)+'" disabled></div>';
                                rawdata += '</div>';
                                rawdata += '<div class="row form-group">';
                                    rawdata += '<div class="col-md-3"><label for="inorderproductactdepo">Actual Deposit</label><input type="text" class="form-control form-control-sm" id="inorderproductactdepo" value="'+response.inorderproduct.product_deposite+'" disabled></div>';
                                    rawdata += '<div class="col-md-1 text-center"><span><br>-</span></div>';
                                    rawdata += '<div class="col-md-3"><label for="inorderproductadjdepo">Adjusted Deposit</label><input type="text" class="form-control form-control-sm" id="inorderproductadjdepo" value="'+response.inorderproduct.adjusted_deposit+'" disabled></div>';
                                    rawdata += '<div class="col-md-1 text-center"><span><br>=</span></div>';
                                    rawdata += '<div class="col-md-4"><label for="inorderproductremdepo">Remaining Deposit</label><input type="text" class="form-control form-control-sm" id="inorderproductremdepo" value="'+parseInt(response.inorderproduct.product_deposite - response.inorderproduct.adjusted_deposit)+'" disabled></div>';
                                rawdata += '</div>';
                                rawdata += '<div class="row form-group">';
                                    rawdata += '<div class="col-md-3"><label for="inorderproductacttransport">Actual Transport</label><input type="text" class="form-control form-control-sm" id="inorderproductacttransport" value="'+response.inorderproduct.transport+'" disabled></div>';
                                    rawdata += '<div class="col-md-1 text-center"><span><br>-</span></div>';
                                    rawdata += '<div class="col-md-3"><label for="inorderproductadjtransport">Adjusted Transport</label><input type="text" class="form-control form-control-sm" id="inorderproductadjtransport" value="'+response.inorderproduct.adjusted_transport+'" disabled></div>';
                                    rawdata += '<div class="col-md-1 text-center"><span><br>=</span></div>';
                                    rawdata += '<div class="col-md-4"><label for="inorderproductremtransport">Remaining Transport</label><input type="text" class="form-control form-control-sm" id="inorderproductremtransport" value="'+parseInt(response.inorderproduct.transport - response.inorderproduct.adjusted_transport)+'" disabled></div>';
                                rawdata += '</div></div></div>';
                                rawdata += '<div class="card mt-3"><div class="card-header">Adjust From</div> <div class="card-body"><div class="row form-group">';
                                    rawdata += '<div class="col-md-12"><span><h4>'+response.fromorderproduct.product_name+' - '+response.fromorderproduct.unique_id+'</h4></span></div>';
                                rawdata += '</div>';
                                rawdata += '<div class="row form-group">';
                                    rawdata += '<div class="col-md-12"><input type="radio" name="fromadjust_rent_depo" id="fromorderproductrent" value="rent"><label for="fromorderproductrent" class="mr-2">Rent</label>';
                                    rawdata += '<input type="radio" name="fromadjust_rent_depo" id="fromorderproductdeposit" value="deposit" checked><label for="fromorderproductdeposit">Deposit</label>';
                                    rawdata += '<input type="radio" name="fromadjust_rent_depo" id="fromorderproducttransport" value="transport"><label for="fromorderproducttransport">Transport</label>';
                                rawdata += '</div></div>';
                                rawdata += '<div class="row form-group">';
                                    rawdata += '<div class="col-md-3"><label for="fromorderproductactrent">Actual Rent</label><input type="text" class="form-control form-control-sm" id="fromorderproductactrent" value="'+response.fromorderproduct.product_rent+'" disabled></div>';
                                    rawdata += '<div class="col-md-1 text-center"><span><br>-</span></div>';
                                    rawdata += '<div class="col-md-3"><label for="fromorderproductadjrent">Adjusted Rent</label><input type="text" class="form-control form-control-sm" id="fromorderproductadjrent" value="'+response.fromorderproduct.adjusted_rent+'" disabled></div>';
                                    rawdata += '<div class="col-md-1 text-center"><span><br>=</span></div>';
                                    rawdata += '<div class="col-md-4"><label for="fromorderproductremrent">Remaining Rent</label><input type="text" class="form-control form-control-sm" id="fromorderproductremrent" value="'+parseInt(response.fromorderproduct.product_rent - response.fromorderproduct.adjusted_rent)+'" disabled></div>';
                                rawdata += '</div>';
                                rawdata += '<div class="row form-group">';
                                    rawdata += '<div class="col-md-3"><label for="fromorderproductactdepo">Actual Deposit</label><input type="text" class="form-control form-control-sm" id="fromorderproductactdepo" value="'+response.fromorderproduct.product_deposite+'" disabled></div>';
                                    rawdata += '<div class="col-md-1 text-center"><span><br>-</span></div>';
                                    rawdata += '<div class="col-md-3"><label for="fromorderproductadjdepo">Adjusted Deposit</label><input type="text" class="form-control form-control-sm" id="fromorderproductadjdepo" value="'+response.fromorderproduct.adjusted_deposit+'" disabled></div>';
                                    rawdata += '<div class="col-md-1 text-center"><span><br>=</span></div>';
                                    rawdata += '<div class="col-md-4"><label for="fromorderproductremdepo">Remaining Deposit</label><input type="text" class="form-control form-control-sm" id="fromorderproductremdepo" value="'+parseInt(response.fromorderproduct.product_deposite - response.fromorderproduct.adjusted_deposit)+'" disabled></div>';
                                rawdata += '</div>';
                                rawdata += '<div class="row form-group">';
                                    rawdata += '<div class="col-md-3"><label for="fromorderproductacttransport">Actual Transport</label><input type="text" class="form-control form-control-sm" id="fromorderproductacttransport" value="'+response.fromorderproduct.transport+'" disabled></div>';
                                    rawdata += '<div class="col-md-1 text-center"><span><br>-</span></div>';
                                    rawdata += '<div class="col-md-3"><label for="fromorderproductadjtransport">Adjusted Transport</label><input type="text" class="form-control form-control-sm" id="fromorderproductadjtransport" value="'+response.fromorderproduct.adjusted_transport+'" disabled></div>';
                                    rawdata += '<div class="col-md-1 text-center"><span><br>=</span></div>';
                                    rawdata += '<div class="col-md-4"><label for="fromorderproductremtransport">Remaining Transport</label><input type="text" class="form-control form-control-sm" id="fromorderproductremtransport" value="'+parseInt(response.fromorderproduct.transport - response.fromorderproduct.adjusted_transport)+'" disabled></div>';
                                rawdata += '</div></div></div>';
                                rawdata += '<div class="row form-group">';
                                    rawdata += '<div class="col-md-4"></div><div class="col-md-4"><label for="adjusteddeposit">Adjust Deposit/Rent</label><input type="number" class="adjusteddeposit form-control form-control-sm" value="0" id="adjusteddeposit" name="adjusteddeposit" onInput="calculateDeposit($(this).val());"></div>'
                                rawdata += '</div></div>';
                            $(".ad-details").append(rawdata);
                        }
                        else if(response.status == 'error')
                        {
                            let div = "<div class='text-center text-danger'>";
                                    div += "<span>"+response.description+"</span>"
                                div += "</div>";
                            $('.error-texts').append(div);
                        }
                        else
                        {
                            let div = "<div class='text-center text-danger'>";
                                    div += "<span>Someting went wrong, Try Again!</span>"
                                div += "</div>";
                            $('.error-texts').append(div);
                        }
                    },
                    error: function(e){
                        let div = "<div class='text-center text-danger'>";
                                div += "<span>Someting went wrong, Try Again!</span>"
                            div += "</div>";
                        $('.error-texts').append(div);
                    }
                });
            }
            else
            {
                let div = "<div class='text-center text-danger'>";
                            div += "<span>Select Products!</span>"
                        div += "</div>";
                    $('.error-texts').append(div);
                    setTimeout(function(){
                        $('.error-texts').empty();
                    }, 2000);
            }
        });
        function calculateDeposit($adjusteddepo)
        {
            if($("input[name='inadjust_rent_depo']:checked").val() == 'rent' && $("input[name='fromadjust_rent_depo']:checked").val() == 'rent' ){
                console.log("Rent:Rent");
                if(parseInt($("#inorderproductremrent").val())<parseInt($adjusteddepo)){
                    let div = "<div class='text-center text-danger'>";
                                div += "<span>Amount should not be greater than amount to be adjusted!</span>"
                            div += "</div>";
                        $('.error-texts').append(div);
                        if(parseInt($("#inorderproductremrent").val()) > $("#fromorderproductremrent").val()){
                            let div = "<div class='text-center text-danger'>";
                                    div += "<span>Amount should not be greater than amount from adjusted!</span>"
                                div += "</div>";
                            $('.error-texts').append(div);
                            $("#adjusteddeposit").val(0);
                            setTimeout(function(){
                                $('.error-texts').empty();
                            }, 2000);
                        }
                        else
                        {
                            $("#adjusteddeposit").val($("#inorderproductremrent").val());
                        }
                        setTimeout(function(){
                            $('.error-texts').empty();
                        }, 2000);
                }
                else if(parseInt($("#fromorderproductremrent").val())<parseInt($adjusteddepo)){
                    let div = "<div class='text-center text-danger'>";
                                div += "<span>Amount should not be greater than amount from adjusted!</span>"
                            div += "</div>";
                        $('.error-texts').append(div);
                        $("#adjusteddeposit").val(0);
                        setTimeout(function(){
                            $('.error-texts').empty();
                        }, 2000);
                }
                else{
                    return true;
                }
            }
            else if($("input[name='inadjust_rent_depo']:checked").val() == 'rent' && $("input[name='fromadjust_rent_depo']:checked").val() == 'deposit' ){
                console.log("Rent:Deposit");
                if(parseInt($("#inorderproductremrent").val())<parseInt($adjusteddepo)){
                    let div = "<div class='text-center text-danger'>";
                                div += "<span>Amount should not be greater than amount to be adjusted!</span>"
                            div += "</div>";
                        $('.error-texts').append(div);
                        if(parseInt($("#inorderproductremrent").val()) > $("#fromorderproductremdepo").val()){
                            let div = "<div class='text-center text-danger'>";
                                    div += "<span>Amount should not be greater than amount from adjusted!</span>"
                                div += "</div>";
                            $('.error-texts').append(div);
                            $("#adjusteddeposit").val(0);
                            setTimeout(function(){
                                $('.error-texts').empty();
                            }, 2000);
                        }
                        else
                        {
                            $("#adjusteddeposit").val($("#inorderproductremrent").val());
                        }
                        setTimeout(function(){
                            $('.error-texts').empty();
                        }, 2000);
                }
                else if(parseInt($("#fromorderproductremdepo").val())<parseInt($adjusteddepo)){
                    let div = "<div class='text-center text-danger'>";
                                div += "<span>Amount should not be greater than amount from adjusted!</span>"
                            div += "</div>";
                        $('.error-texts').append(div);
                        $("#adjusteddeposit").val(0);
                        setTimeout(function(){
                            $('.error-texts').empty();
                        }, 2000);
                }
                else{
                    return true;
                }
            }
            else if($("input[name='inadjust_rent_depo']:checked").val() == 'deposit' && $("input[name='fromadjust_rent_depo']:checked").val() == 'rent' ){
                console.log("Deposit:Rent");
                if(parseInt($("#inorderproductremdepo").val())<parseInt($adjusteddepo)){
                    let div = "<div class='text-center text-danger'>";
                                div += "<span>Amount should not be greater than amount to be adjusted!</span>"
                            div += "</div>";
                        $('.error-texts').append(div);
                        if(parseInt($("#inorderproductremdepo").val()) > $("#fromorderproductremrent").val()){
                            let div = "<div class='text-center text-danger'>";
                                    div += "<span>Amount should not be greater than amount from adjusted!</span>"
                                div += "</div>";
                            $('.error-texts').append(div);
                            $("#adjusteddeposit").val(0);
                            setTimeout(function(){
                                $('.error-texts').empty();
                            }, 2000);
                        }
                        else
                        {
                            $("#adjusteddeposit").val($("#inorderproductremdepo").val());
                        }
                        setTimeout(function(){
                            $('.error-texts').empty();
                        }, 2000);
                }
                else if(parseInt($("#fromorderproductremrent").val())<parseInt($adjusteddepo)){
                    let div = "<div class='text-center text-danger'>";
                                div += "<span>Amount should not be greater than amount from adjusted!</span>"
                            div += "</div>";
                        $('.error-texts').append(div);
                        $("#adjusteddeposit").val(0);
                        setTimeout(function(){
                            $('.error-texts').empty();
                        }, 2000);
                }
                else{
                    return true;
                }
            }
            else if($("input[name='inadjust_rent_depo']:checked").val() == 'deposit' && $("input[name='fromadjust_rent_depo']:checked").val() == 'deposit' ){
                console.log("Deposit:Deposit");
                if(parseInt($("#inorderproductremdepo").val())<parseInt($adjusteddepo)){
                    let div = "<div class='text-center text-danger'>";
                                div += "<span>Amount should not be greater than amount to be adjusted!</span>"
                            div += "</div>";
                        $('.error-texts').append(div);
                        if(parseInt($("#inorderproductremdepo").val()) > $("#fromorderproductremdepo").val()){
                            let div = "<div class='text-center text-danger'>";
                                    div += "<span>Amount should not be greater than amount from adjusted!</span>"
                                div += "</div>";
                            $('.error-texts').append(div);
                            $("#adjusteddeposit").val(0);
                            setTimeout(function(){
                                $('.error-texts').empty();
                            }, 2000);
                        }
                        else
                        {
                            $("#adjusteddeposit").val($("#inorderproductremdepo").val());
                        }
                        setTimeout(function(){
                            $('.error-texts').empty();
                        }, 2000);
                }
                else if(parseInt($("#fromorderproductremdepo").val())<parseInt($adjusteddepo)){
                    let div = "<div class='text-center text-danger'>";
                                div += "<span>Amount should not be greater than amount from adjusted!</span>"
                            div += "</div>";
                        $('.error-texts').append(div);
                        $("#adjusteddeposit").val(0);
                        setTimeout(function(){
                            $('.error-texts').empty();
                        }, 2000);
                }
                else{
                    return true;
                }
            }else{
                return false;
            }
        }
    </script>
    <script>
        var config = {
            routes: {
                adjustmentdetails: "{{ route('adjustment-details') }}",
            },
            postval: {
                token: "{{ csrf_token() }}",
            }
        };
    </script>
    <script>

        $(".btn-adjusted-view").click(function(){
            $(".adjustments").empty();
            // alert($(this).data('order_id'));
            
            var dataString = ({_token:"{{ csrf_token() }}",order_id:""+$(this).data('order_id'),order_type:""+$(this).data('order_type'),adjust_state:""+$(this).data('state'),request_type:"adjusted-data"});
            $.ajax({
                type: "POST",
                url: "{{url('/')}}/getOrderDetails",
                data: dataString,
                cache:false,
                success: function (data)
                {
                    
                    console.log(data);
                    var raw_data = "";
                    for(let i=0; i<data.length; i++)
                    {
                        if(data[i].adjustment.length >0)
                        {
                            raw_data += '<div class="card card-body my-2">';
                                raw_data += '<h4>'+data[i].product_name+' ('+data[i].unique_id+')</h4>';
                                raw_data += '<div class="table table-responsive jim-table-responsive">';
                                    raw_data += '<table class="table">';
                                        raw_data += '<thead>';
                                            raw_data += '<tr>';
                                                raw_data += '<th>Order Id</th>';
                                                raw_data += '<th>Order Date</th>';
                                                raw_data += '<th>Product Name</th>';
                                                raw_data += '<th>Adj Amt</th>';
                                                raw_data += '<th>From</th>';
                                                raw_data += '<th>In</th>';
                                                raw_data += '<th>-</th>';
                                            raw_data += '</tr>';
                                        raw_data += '</thead>';
                                        raw_data += '<tbody>';
                                            for(let j=0; j<data[i].adjustment.length; j++)
                                            {
                                                raw_data += '<tr>';
                                                    raw_data += '<td>'+data[i].adjustment[j].order_id+'</td>';
                                                    raw_data += '<td>'+data[i].adjustment[j].creation_date+'</td>';
                                                    raw_data += '<td>'+data[i].adjustment[j].product_name+'</td>';
                                                    raw_data += '<td>'+data[i].adjustment[j].adjusted_amount+'</td>';
                                                    if(data[i].adjustment[j].fromtype == 'R')
                                                        raw_data += '<td>Rent</td>';
                                                    if(data[i].adjustment[j].fromtype == 'D')
                                                        raw_data += '<td>Depo.</td>';
                                                    if(data[i].adjustment[j].fromtype == 'T')
                                                        raw_data += '<td>Trans.</td>';
                                                    if(data[i].adjustment[j].intype == 'R')
                                                        raw_data += '<td>Rent</td>';
                                                    if(data[i].adjustment[j].intype == 'D')
                                                        raw_data += '<td>Depo.</td>';
                                                    if(data[i].adjustment[j].intype == 'T')
                                                        raw_data += '<td>Trans.</td>';
                                                    // raw_data += '<td>'+data[i].adjustment[j].intype+'</td>';
                                                    raw_data += "<td><a href='{{url('/')}}/reverse-adjustment/"+data[i].adjustment[j].adjustment_id+"' class='btn btn-sm text-danger'><i class='fa fa-window-close'></i></a></td>";
                                                raw_data += '</tr>';
                                            }
                                        raw_data += '</tbody>';
                                    raw_data += '</table>';
                                raw_data += '</div>';
                            raw_data += '</div>';
                        }
                    }
                    $(".adjustments").append(raw_data);
                    $("#adjustmentDetailsModal").modal("show");
                }
            });
        });

        $("#btn_clear").click(function(){
            window.open("{{url('/')}}/pending_payments",'_self');
        });
        $(".viewPayment").click(function() {
            let id = $(this).data("id");
            let path = $("#hidden_payment_image"+id).val();
            $("#PaymentImagePath").attr("src",path);
            $("#viewPaymentImage").modal('show');
        });
        $(".viewReceipt").click(function() {
            let id=$(this).data("id");
            let path = $("#hidden_receipt_image"+id).val();
            if(path.includes("http://")){
                $("#ReceiptImagePath").attr("src",path);
            }else{
                $("#ReceiptImagePath").attr("src","http://"+path);
            }
            // $("#ReceiptImagePath").attr("src","http://"+path);
            $("#uploaded_at_span").text($("#hidden_receipt_image"+id).data("uploaded_at"));
            $("#viewReceiptImage").modal('show');
        });
        $(".viewOrder").click(function() {
            let id = $(this).data("id");
            let order_type = $(this).data("order_type");
            let order_id = $(this).data("order_id");
            let maintenanceOrderType = ['Repair','Install','Shifting','Replace'];
            if(order_type == "Delivery")
            {
                $("#order_details_modal_body").empty();
                var dataString = ({_token:"{{ csrf_token() }}",order_id:""+order_id,request_type:"Delivery"});
                $.ajax({
                    type: "POST",
                    url: "{{url('/')}}/getOrderDetails",
                    data: dataString,
                    cache:false,
                    success: function (data)
                    {
                        $("#order_type_title").text("Delivery Order");
                        // console.log(data);
                        let details_count = data.length;
                        let table = '<div class="table table-responsive jim-table-responsive">';
                            table += '<table class="table" id="order_details_table">';
                                table += '<thead>';
                                    table += '<th>Sr. No</th>';
                                    table += '<th>Date</th>';
                                    table += '<th>Product Name</th>';
                                    table += '<th>Vendor Name</th>';
                                    table += '<th>Warehouse Name</th>';
                                    // table += '<th>Customer Name</th>';
                                    // table += '<th>Customer Contact</th>';
                                    // table += '<th>Vendor Rent</th>';
                                    table += '<th>Offered Rent</th>';
                                    table += '<th>Current Rent</th>';
                                    table += '<th>B.Period</th>';
                                    table += '<th>Total Rent</th>';
                                    table += '<th>Offered Deposit</th>';
                                    table += '<th>Current Deposit</th>';
                                    table += '<th>Adjusted Amount</th>';
                                    table += '<th>Offered Transport</th>';
                                    table += '<th>Current Transport</th>';
                                    // table += '<th>Status</th>';
                                table += '</head>';
                                table += '<tbody>';
                                for (let i = 0; i < details_count; i++)
                                {
                                    let sr_no = i+1;
                                    if(data[i].current_status == 'Cancel')
                                    {
                                        table += '<tr class="text-wrap overlay-card">';
                                        table +='<td data-label="Sr.No.">'+sr_no+'</td>';
                                        table +='<td data-label="Date">'+data[i].creation_date+'<span class="text-danger font-weight-bold">Removed</span></td>';
                                    }
                                    else
                                    {
                                        table += '<tr class="text-wrap">';
                                        table +='<td data-label="Sr.No.">'+sr_no+'</td>';
                                        table +='<td class="text-nowrap" data-label="Date">'+data[i].creation_date+'</td>';
                                    }
                                    if(data[i].sale_rental == 'Rental'){
                                        table +='<td data-label="Product Name">'+data[i].product_name+'<br>('+data[i].unique_id+')</td>';
                                    }else{
                                        table +='<td data-label="Product Name">'+data[i].product_name+'</td>';
                                    }
                                        table +='<td data-label="Vendor Name">'+data[i].vendor_name+'</td>';
                                        table +='<td data-label="Warehouse">'+data[i].warehouse_name+', '+data[0].warehouse_area+', '+data[0].warehouse_city+'</td>';
                                        // table +='<td>'+data[i].customer_name+'</td>';
                                        // table +='<td>'+data[i].primary_contact_no+'</td>';
                                        // table +='<td>'+data[i].vendor_rent+'</td>';
                                        table +='<td data-label="Offered Rent">'+data[i].offered_rent+'</td>';
                                        table +='<td data-label="Current Rent">'+data[i].product_rent+'</td>';
                                        table +='<td data-label="B.Period">'+data[i].billing_period+" "+data[i].billing_unit+'</td>';
                                        table +='<td data-label="Total Rent">'+(data[i].product_rent * data[i].months)+'</td>';
                                        table +='<td data-label="Rent">'+data[i].offered_deposite+'</td>';
                                        table +='<td data-label="Deposit">'+data[i].product_deposite+'</td>';
                                        table +='<td data-label="Adjusted Deposit">'+data[i].adjusted_deposit+'</td>';
                                        table +='<td data-label="Rent">'+data[i].offered_transport+'</td>';
                                        table +='<td data-label="Transport">'+data[i].transport+'</td>';
                                    table += '</tr>';
                                }
                                table += '</tbody>';
                            table += '</table>';
                        table += '</div>';
                        if(data[0].received_total_amount==null || data[0].received_total_amount== '' ){
                            data[0].received_total_amount= 0;
                        }
                        table +='<div class="container container-fluid">';
                            table += '<div class="row"><div class="col-md-12 text-right"><span>Total Amount : </span><span>'+data[0].assigned_total_amount+'</span></div></div>';
                            table += '<div class="row"><div class="col-md-12 text-right"><span>Received Amount : </span><span>'+data[0].received_total_amount+'</span></div></div>';
                            table += '<div class="row"><div class="col-md-12 text-right"><span>Remaining : </span><span>'+(data[0].assigned_total_amount - data[0].received_total_amount)+'</span></div></div>';
                        table += '</div>';
                        // location.reload();
                        $('#vieworder_customername').text(data[0].shipping_first_name);
                        $('#vieworder_mobileno').text(data[0].mobileno);
                        $('#vieworder_email').text(data[0].email_id);
                        $('#vieworder_location').text(data[0].location);
                        $('#vieworder_address').text(data[0].address_line_1+','+data[0].address_line_2+','+data[0].area+','+data[0].landmark+','+data[0].city+','+data[0].pincode);
                        $("#vieworder_del_assigned_to").text(data[0].DelAssignedTo);
                        $("#vieworder_helpers").text(data[0].helpers.replace(']','').replace('[','').replace('"','').replace('"',''));
                        $("#order_details_modal_body").append(table);
                        //$("#order_details_table").dataTable();
                    }
                });
                $("#viewOrderDetails").modal('show');
            }
            else if(order_type == "Collection")
            {
                $("#order_details_modal_body").empty();
                var dataString = ({_token:"{{ csrf_token() }}",order_id:""+order_id,request_type:"Collection"});
                $.ajax({
                    type: "POST",
                    url: "{{url('/')}}/getOrderDetails",
                    data: dataString,
                    cache:false,
                    success: function (data)
                    {
                        console.log(data);
                        if(data!=false){
                        $("#order_type_title").text("Collection Order");
                        // console.log(data);
                        let details_count = data.length;
                        let table = '<div class="table table-responsive jim-table-responsive">';
                            table += '<table class="table" id="order_details_table">';
                                table += '<thead>';
                                    table += '<th>Sr. No</th>';
                                    table += '<th>Date</th>';
                                    table += '<th>Product Name</th>';
                                    table += '<th>Vendor Name</th>';
                                    table += '<th>Warehouse Name</th>';
                                    // table += '<th>Customer Name</th>';
                                    // table += '<th>Customer Contact</th>';
                                    // table += '<th>Vendor Rent</th>';
                                    table += '<th>Product Rent</th>';
                                    table += '<th>Adjusted Deposit</th>';
                                    table += '<th>Discount</th>';
                                    table += '<th>Period</th>';
                                    // table += '<th>Status</th>';
                                table += '</head>';
                                table += '<tbody>';
                                for (let i = 0; i < details_count; i++)
                                {
                                    let sr_no = i+1;
                                    table += '<tr class="text-wrap">';
                                        table +='<td data-label="Sr.No.">'+sr_no+'</td>';
                                        table +='<td data-label="Date">'+data[i].date+'</td>';
                                        // table +='<td data-label="Product Name">'+data[i].product_name+'</td>';
                                        if(data[i].sale_rental == 'Rental'){
                                            table +='<td data-label="Product Name">'+data[i].product_name+'<br>('+data[i].unique_id+')</td>';
                                        }else{
                                            table +='<td data-label="Product Name">'+data[i].product_name+'</td>';
                                        }
                                        table +='<td data-label="Vendor Name">'+data[i].vendor_name+'</td>';
                                        table +='<td data-label="Warehouse">'+data[i].warehouse_name+', '+data[0].warehouse_area+', '+data[0].warehouse_city+'</td>';
                                        // table +='<td>'+data[i].customer_name+'</td>';
                                        // table +='<td>'+data[i].primary_contact_no+'</td>';
                                        // table +='<td>'+data[i].vendor_rent+'</td>';
                                        table +='<td data-label="Rent">'+data[i].product_rent+'</td>';
                                        table +='<td data-label="Product Rent">'+data[i].adjusted_deposit+'</td>';
                                        table +='<td data-label="Product Rent">'+data[i].discount_amt+'</td>';
                                        table +='<td data-label="Period">'+data[i].start_date+' - '+data[i].end_date+'</td>';
                                    table += '</tr>';
                                }
                                table += '</tbody>';
                            table += '</table>';
                        table += '</div>';
                        if(data[0].received_total_amount == '' || data[0].received_total_amount == null)
                        {
                            data[0].received_total_amount = 0;
                        }
                        if(data[0].received_total_amount==null || data[0].received_total_amount== '' ){
                            data[0].received_total_amount= 0;
                        }
                        table +='<div class="container container-fluid">';
                            table += '<div class="row"><div class="col-md-12 text-right"><span>Total Amount : </span><span>'+data[0].total_amount+'</span></div></div>';
                            table += '<div class="row"><div class="col-md-12 text-right"><span>Received Amount : </span><span>'+  data[0].received_total_amount+'</span></div></div>';
                            table += '<div class="row"><div class="col-md-12 text-right"><span>Remaining : </span><span>'+(data[0].total_amount - data[0].received_total_amount)+'</span></div></div>';
                        table += '</div>';
                        // location.reload();
                        $('#vieworder_customername').text(data[0].shipping_first_name);
                        $('#vieworder_mobileno').text(data[0].mobileno);
                        $('#vieworder_email').text(data[0].email_id);
                        $('#vieworder_location').text(data[0].location);
                        $('#vieworder_address').text(data[0].address_line_1+','+data[0].address_line_2+','+data[0].area+','+data[0].landmark+','+data[0].city+','+data[0].pincode);
                        $("#vieworder_del_assigned_to").text(data[0].DelAssignedTo);
                        $("#vieworder_helpers").text(data[0].helpers);
                        $("#order_details_modal_body").append(table);
                        //$("#order_details_table").dataTable();
                    }
                        else{
                            $("#order_details_modal_body").append("<div class='d-flex justify-content-center'><span>Cash Collection Generated Against Delivery Order</span></div>");
                        }
                    }
                });
                $("#viewOrderDetails").modal('show');
            }
            else if(order_type == "Pick Up")
            {
                $("#order_type_title").text("Pick Up Order");
                $("#order_details_modal_body").empty();
                var dataString = ({_token:"{{ csrf_token() }}",order_id:""+order_id,request_type:"Pick Up"});
                console.log(dataString);
                $.ajax({
                    type: "POST",
                    url: "{{url('/')}}/getOrderDetails",
                    data: dataString,
                    cache:false,
                    success: function (data)
                    {
                        // console.log(data.length);
                        if(data.length != 0)
                        {
                            let details_count = data.length;
                            let table = '<div class="table table-responsive jim-table-responsive">';
                                table += '<table class="table" id="order_details_table">';
                                    table += '<thead>';
                                        table += '<th>Sr. No</th>';
                                        table += '<th>Date</th>';
                                        table += '<th>Product Name</th>';
                                        table += '<th>Vendor Name</th>';
                                        table += '<th>Drop Location</th>';
                                        table += '<th>Drop Address</th>';
                                        // table += '<th>Customer Name</th>';
                                        // table += '<th>Customer Contact</th>';
                                        // table += '<th>Vendor Rent</th>';
                                        table += '<th>Product Deposit</th>';
                                        table += '<th>Transport</th>';
                                        // table += '<th>Status</th>';
                                    table += '</head>';
                                    table += '<tbody>';
                                    for (let i = 0; i < details_count; i++)
                                    {
                                        let sr_no = i+1;
                                        table += '<tr class="text-wrap">';
                                            table +='<td data-label="Sr.No.">'+sr_no+'</td>';
                                            table +='<td data-label="Date">'+data[i].date+'</td>';
                                            // table +='<td data-label="Product Name">'+data[i].product_name+'</td>';
                                            if(data[i].sale_rental == 'Rental'){
                                                table +='<td data-label="Product Name">'+data[i].product_name+'<br>('+data[i].unique_id+')</td>';
                                            }else{
                                                table +='<td data-label="Product Name">'+data[i].product_name+'</td>';
                                            }
                                            table +='<td data-label="Vendor Name">'+data[i].vendor_name+'</td>';
                                            table +='<td data-label="Drop Location">'+data[i].drop_location+'</td>';
                                            table +='<td data-label="Drop Address">'+data[i].warehouse_name+', '+data[0].warehouse_area+', '+data[0].warehouse_city+'</td>';
                                            // table +='<td>'+data[i].customer_name+'</td>';
                                            // table +='<td>'+data[i].primary_contact_no+'</td>';
                                            // table +='<td>'+data[i].vendor_rent+'</td>';
                                            table +='<td data-label="Deposit">'+data[i].product_deposite+'</td>';
                                            if(data[i].transport==null){
                                                table +='<td data-label="Transport">0</td>';
                                            }else{
                                                table +='<td data-label="Transport">'+data[i].transport+'</td>';
                                            }
                                            // table +='<td>'+data[i].start_date+' - '+data[i].end_date+'</td>';
                                        table += '</tr>';
                                    }
                                    table += '</tbody>';
                                table += '</table>';
                            table += '</div>';
                            if(data[0].paid_total_amount == '' || data[0].paid_total_amount == null)
                            {
                                data[0].paid_total_amount = 0;
                            }
                            if(data[0].received_total_amount==null || data[0].received_total_amount== '' ){
                                data[0].received_total_amount= 0;
                            }
                            table +='<div class="container container-fluid">';
                                table += '<div class="row"><div class="col-md-12 text-right"><span>Total Amount : </span><span>'+data[0].total_amount+'</span></div></div>';
                                table += '<div class="row"><div class="col-md-12 text-right"><span>Received Amount : </span><span>'+data[0].paid_total_amount+'</span></div></div>';
                                table += '<div class="row"><div class="col-md-12 text-right"><span>Remaining : </span><span>'+(data[0].total_amount - data[0].paid_total_amount)+'</span></div></div>';
                            table += '</div>';
                            // location.reload();
                            $('#vieworder_customername').text(data[0].shipping_first_name);
                            $('#vieworder_mobileno').text(data[0].mobileno);
                            $('#vieworder_email').text(data[0].email_id);
                            $('#vieworder_location').text(data[0].location);
                            $('#vieworder_address').text(data[0].address_line_1+','+data[0].address_line_2+','+data[0].area+','+data[0].landmark+','+data[0].city+','+data[0].pincode);
                            $("#vieworder_del_assigned_to").text(data[0].DelAssignedTo);
                            $("#vieworder_helpers").text(data[0].helpers);
                            $("#order_details_modal_body").append(table);
                            // $("#order_details_table").dataTable();
                        }
                        else
                        {
                            let heading = '<h5 class="text-center">Something Went Wrong!</h5>';
                            $("#order_details_modal_body").append(heading);
                        }
                    }
                });
                $("#viewOrderDetails").modal('show');
            }
            else if($.inArray(order_type,maintenanceOrderType) !== -1){
                
                $("#order_type_title").text(order_type+" Order");
                $("#order_details_modal_body").empty();
                var dataString = ({_token:"{{ csrf_token() }}",order_id:""+order_id,request_type:""+order_type});
                $.ajax({
                    type: "POST",
                    url: "{{url('/')}}/getOrderDetails",
                    data: dataString,
                    cache:false,
                    success: function (data)
                    {
                        // console.log(data);
                        if(data.length != 0)
                        {
                            let details_count = data.length;
                            let table = '<div class="table table-responsive jim-table-responsive">';
                                table += '<table class="table" id="order_details_table">';
                                    table += '<thead>';
                                        table += '<th>Sr. No</th>';
                                        table += '<th>Date</th>';
                                        table += '<th>Product Name</th>';
                                        table += '<th>Vendor Name</th>';
                                        // table += '<th>Warehouse Name</th>';
                                        // table += '<th>Customer Name</th>';
                                        // table += '<th>Customer Contact</th>';
                                        // table += '<th>Vendor Rent</th>';
                                        // table += '<th>Product Deposit</th>';
                                        // table += '<th>Transport</th>';
                                        // table += '<th>Period</th>';
                                        // table += '<th>Status</th>';
                                    table += '</head>';
                                    table += '<tbody>';
                                    for (let i = 0; i < details_count; i++)
                                    {
                                        let sr_no = i+1;
                                        table += '<tr class="text-wrap">';
                                            table +='<td data-label="srno">'+sr_no+'</td>';
                                            table +='<td data-label="date">'+data[i].date+'</td>';
                                            table +='<td data-label="Product Name" class="text-wrap">'+data[i].product_name+'</td>';
                                            table +='<td data-label="Vendor Name" class="text-wrap">'+data[i].vendor_name+'</td>';
                                            // table +='<td data-label="Warehouse" class="text-wrap">'+data[i].warehouse_name+', '+data[0].warehouse_area+', '+data[0].warehouse_city+'</td>';
                                            // table +='<td data-label="">'+data[i].customer_name+'</td>';
                                            // table +='<td data-label="">'+data[i].primary_contact_no+'</td>';
                                            // table +='<td data-label="">'+data[i].vendor_rent+'</td>';
                                            // table +='<td data-label="Deposit">'+data[i].product_deposite+'</td>';
                                            // table +='<td data-label="Transport">'+data[i].transport+'</td>';
                                            // table +='<td data-label="">'+data[i].start_date+' - '+data[i].end_date+'</td>';
                                        table += '</tr>';
                                    }
                                    table += '</tbody>';
                                table += '</table>';
                            table += '</div>';
                            // if(data[0].paid_total_amount == '' || data[0].paid_total_amount == null)
                            // {
                            //     data[0].paid_total_amount = 0;
                            // }
                            table += '<div class="row"><div class="col-md-12 text-right"><span>Total Amount : </span><span>'+data[0].total_amount+'</span></div></div>';
                            // table += '<div class="row"><div class="col-md-12 text-right"><span>Received Amount : </span><span>'+data[0].paid_total_amount+'</span></div></div>';
                            // table += '<div class="row"><div class="col-md-12 text-right"><span>Remaining : </span><span>'+(data[0].total_amount - data[0].paid_total_amount)+'</span></div></div>';
                            // location.reload();
                            $('#vieworder_customername').text(data[0].shipping_first_name);
                            $('#vieworder_mobileno').text(data[0].mobileno);
                            $('#vieworder_email').text(data[0].email_id);
                            $('#vieworder_location').text(data[0].location);
                            $('#vieworder_address').text(data[0].address_line_1+','+data[0].address_line_2+','+data[0].area+','+data[0].landmark+','+data[0].city+','+data[0].pincode);
                            $("#vieworder_del_assigned_to").text(data[0].DelAssignedTo);
                            $("#vieworder_helpers").text(data[0].helpers);
                            $("#order_details_modal_body").append(table);
                            // $("#order_details_table").dataTable();
                        }
                        else
                        {
                            let heading = '<h5 class="text-center">Something Went Wrong!</h5>';
                            $("#order_details_modal_body").append(heading);
                        }
                    }
                });
                $("#viewOrderDetails").modal('show');
            }
            else
            {
                console.log('Something went wrong');
            }
            //get order images 
            var dataString = ({_token:"{{ csrf_token() }}",order_id:""+order_id});
            $.ajax({
                type: "GET",
                url: "{{url('/')}}/get-order-images/"+order_id,
                data: dataString,
                cache:false,
                success: function (data)
                {
                    let images = data;
                    $('#product-images').empty();
                    if(images!=false){
                        let divcard= '<div class="card-body">';
                                divcard+='<div class="text-center">';
                                    Object.keys(images).forEach(function(key){
                                        if(images[key].includes("http://")){
                                            divcard+='<img src="'+images[key]+'" class="img-fluid img-thumbnail view-image" alt="Responsive image" width="250px" height="400px">';
                                        }else{
                                            divcard+='<img src="http://'+images[key]+'" class="img-fluid img-thumbnail view-image" alt="Responsive image" width="250px" height="400px">';
                                        }
                                    });
                                divcard+='</div>';
                            divcard+= '</div>';
                        $('#product-images').append(divcard);
                    }else{
                        $('#product-images').append("<span class='text-center'>No Product Images Found</span>");
                    }
                }
            });

        });
        $(".settleOrder").click(function() {
            let order_id = $(this).data("order_id");
            let key = $(this).data("id");
            let comment = $("#update_order_comment").val("");
            let payment_mode = $("#hidden_payment_mode"+key).val();
            let payment_image = $("#hidden_payment_image"+key).val();
            let reference_id = $("#hidden_reference_id"+key).val();
            let receipt_image = $("#hidden_receipt_image"+key).val();
            let assigned_total_amount = $("#assigned_total_amount"+key).val();
            if($(this).data('order_status')!='Cancel'){
                if(payment_mode == 'Cash' && receipt_image == '-')
                {
                    // $("#receipt_file").attr('required', 'true');
                    // $("#display_error_text").text("*Payment Mode is Cash and receipt image is required order can not settled.");
                    // $("#display_error_text").show();
                    // $("#settle_order").attr('disabled',true);
                }
                else if(payment_mode == 'Online' && (payment_image == '-' && (reference_id == '' || reference_id == null) && assigned_total_amount!=0))
                {
                    $("#display_error_text").text("*Payment Mode is Online and payment image or reference_id is required order can not settled.");
                    $("#display_error_text").show();
                    $("#settle_order").attr('disabled',true);
                    // $("#payment_file").attr('required', 'true');
                }
                else if(payment_mode == 'Both')
                {
                    if((payment_image == '-'&& (reference_id == '' || reference_id == null)) && receipt_image == '-')
                    {
                        // $("#display_error_text").text("*Payment Mode is Both receipt_image,payment image or reference_id is required order can not settled.");
                        // $("#display_error_text").show();
                        // $("#settle_order").attr('disabled',true);
                        // $("#receipt_file").attr('required', 'true');
                        // $("#payment_file").attr('required', 'true');
                    }
                    else if(payment_image == '-' && (reference_id == '' || reference_id == null))
                    {
                        $("#display_error_text").text("*Payment Mode is Both receipt_image,payment image or reference_id is required order can not settled.");
                        $("#display_error_text").show();
                        $("#settle_order").attr('disabled',true);
                        // $("#payment_file").attr('required', 'true');
                    }
                    else if(receipt_image == '-')
                    {
                        // $("#display_error_text").text("*Payment Mode is Both receipt_image,payment image or reference_id is required order can not settled.");
                        // $("#display_error_text").show();
                        // $("#settle_order").attr('disabled',true);
                        // $("#receipt_file").attr('required', 'true');
                    }
                }
                else
                {
                    // $("#display_error_text").text("*Payment Mode is Cash and receipt image is required order can not settled.");
                    $("#display_error_text").hide();
                    $("#settle_order").removeAttr('disabled');
                }
                $("#hidden_order_id_set").val(order_id);
                
                $("#settleOrder").modal('show');
            }else{
                $("#hidden_order_id_set").val(order_id);
                $("#display_error_text").hide();
                $("#settle_order").removeAttr('disabled');
                $("#settleOrder").modal('show');
            }
        });

        $("#payment_file").click(function () {
            this.value = null;
        });
        
        $("#payment_file").change(function () {
            // console.log(this.value);
            $("#update_ref_id").removeAttr('required');
        });

        $("#update_ref_id").keyup(function(){
            let ref_id = $(this).val();
            // console.log(ref_id);
            if(ref_id.length >= 1)
            {
                $("#payment_file").removeAttr('required');
            }
            else
            {
                $("#payment_file").attr('required','true');
            }
        });

        $(".updateOrder").click(function()
        {
            let order_id = $(this).data("order_id");
            let key = $(this).data("id");
            let comment = $("#update_ref_id").val("");
            let order_type = $(this).data("order_type");
            let invoice_no = $(this).data("invoice_no");
            let reference_id = $(this).data("ref_id");
            $("#Cash").attr("checked",false);
            $("#Online").attr("checked",false);
            $("#Both").attr("checked",false);

            $(".images").removeAttr("required");
            let payment_mode = $("#hidden_payment_mode"+key).val();
            // alert(payment_mode);
            let payment_image = $("#hidden_payment_image"+key).val();
            let receipt_image = $("#hidden_receipt_image"+key).val();
            $("#order_expense_pay").val($("#hidden_order_expense"+key).val());
            $("#vendor_charges_pay").val($("#hidden_vendor_charges"+key).val());
            $("#update_ref_id").val(reference_id);
            if(payment_mode == "Cash")
            {
                $("#Cash").attr("checked",true);
                $("#Online").attr("checked",false);
                $("#Both").attr("checked",false);
                $("#update_ref_id").removeAttr('required');
                $("#receipt_file").removeAttr('required');
                $("#payment_file").removeAttr('required');
                $("#update_ref_id_div").hide();
                $("#payment_file_div").hide();
                $("#receipt_file_div").show();
            }
            else if(payment_mode == "Online")
            {
                $("#Cash").attr("checked",false);
                $("#Online").attr("checked",true);
                $("#Both").attr("checked",false);
                $("#update_ref_id").attr('required','true');
                $("#receipt_file").removeAttr('required');
                $("#payment_file").attr('required','true');
                if(reference_id != null || reference_id != ""){
                    $("#payment_file").removeAttr('required');
                }
                $("#update_ref_id_div").show();
                $("#payment_file_div").show();
                $("#receipt_file_div").hide();
            }
            else if(payment_mode == "Both")
            {
                $("#Cash").attr("checked",false);
                $("#Online").attr("checked",false);
                $("#Both").attr("checked",true);
                $("#update_ref_id").attr('required','true');
                $("#receipt_file").attr('required','true');
                $("#payment_file").attr('required','true');
                if(reference_id != null || reference_id != ""){
                    $("#payment_file").removeAttr('required');
                }
                $("#receipt_file_div").show();
                $("#payment_file_div").show();
                $("#update_ref_id_div").show();
            }

            $("input[name='payment_mode']").change(function()
            {
                let selected_payment_mode = $("input[name='payment_mode']:checked").val();
                $("#Cash").attr("checked",false);
                $("#Online").attr("checked",false);
                $("#Both").attr("checked",false);
                $("#update_ref_id").removeAttr('required');
                $("#receipt_file").removeAttr('required');
                $("#payment_file").removeAttr('required');
                if(selected_payment_mode == "Cash")
                {
                    $("#Cash").attr("checked",true);
                    $("#Online").attr("checked",false);
                    $("#Both").attr("checked",false);
                    $("#update_ref_id_div").hide ();
                    $("#payment_file_div").hide();
                    $("#receipt_file_div").show();
                }
                else if(selected_payment_mode == "Online")
                {
                    $("#Cash").attr("checked",false);
                    $("#Online").attr("checked",true);
                    $("#Both").attr("checked",false);
                    $("#update_ref_id").attr('required','true');
                    $("#payment_file").attr('required','true');
                    $("#update_ref_id_div").show();
                    $("#payment_file_div").show();
                    $("#receipt_file_div").hide();
                }
                else if(selected_payment_mode == "Both")
                {
                    $("#Cash").attr("checked",false);
                    $("#Online").attr("checked",false);
                    $("#Both").attr("checked",true);
                    $("#update_ref_id").attr('required','true');
                    $("#payment_file").attr('required','true');
                    $("#update_ref_id_div").show();
                    $("#receipt_file_div").show();
                    $("#payment_file_div").show();
                    // $("#receipt_file").attr('required', 'true');
                }
            });

            if(payment_mode == 'Cash' && receipt_image == '-')
            {
                // $("#receipt_file").attr('required', 'true');                
            }
            else if(payment_mode == 'Online' && payment_image == '-')
            {
                $("#update_ref_id").attr('required','true');
                $("#payment_file").attr('required','true');
                if(reference_id != null || reference_id != ""){
                    $("#payment_file").removeAttr('required');
                }
            }
            else if(payment_mode == 'Both')
            {
                if(payment_image == '-' && receipt_image == '-')
                {
                    // $("#receipt_file").attr('required', 'true');
                    $("#update_ref_id").attr('required','true');
                    $("#payment_file").attr('required','true');
                }
                else if(payment_image == '-')
                {
                    $("#update_ref_id").attr('required','true');
                    $("#payment_file").attr('required','true');
                }
                else if(receipt_image == '-')
                {
                    // $("#receipt_file").attr('required', 'true');
                }
            }
            $("#hidden_order_id").val(order_id);
            $("#hidden_order_id_inv").val(order_id);

            if(order_type=="Collection"){
                $('#labourChargesCard').css('display','none');
            }else{
                $('#labourChargesCard').css('display','block');
            }
            if(invoice_no != null && invoice_no != "") 
            {
                $(".invoice-field").show();
                $("#invoice_id").val(invoice_no);
                $("#hidden_invoice_id").val(invoice_no);
            }
            else{
                $(".invoice-field").hide();
                $("#hidden_invoice_id").val(null);
            }

            $('#floor_no').val($(this).data('floor_no'));
            $('#labour_charges').val($(this).data('labour_charges'));
            $("#updateOrder").modal('show');
        });
        // $("#settle_order").click(function(){
        //     let order_id = $("#hidden_order_id").val();
        //     let comment = $("#update_order_comment").val();
        //     if(comment.length <= 0)
        //     {
        //         $('#update_order_comment').attr('style','border:dashed 2px red;');
        //     }
        //     else
        //     {
        //         var dataString = ({_token:"{{ csrf_token() }}",order_id:""+order_id,comment:""+comment,request_type:"update-status"});
        //         $.ajax({
        //             type:"POST",
        //             url:"{{url('/')}}/getOrderDetails",
        //             data:dataString,
        //             cache:false,
        //             success:function(data){
        //                 console.log(data);
        //                 location.reload();
        //             }
        //         });
        //     }
        // });
        $('.bank_details').on('click',function(){
            let id = $(this).data('id');
            let bank_details_data = @json($bankCustomerDetails);
            $('#span_bank_name').text(bank_details_data[id].bank_name);
            $('#span_branch_name').text(bank_details_data[id].branch_name);
            $('#span_account_no').text(bank_details_data[id].account_number);
            $('#span_ifsc_code').text(bank_details_data[id].ifsc_code);
            $('#span_account_type').text(bank_details_data[id].account_type);
            $('#bankDetailsModal').modal('show');
        });
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
    <script>
         $(".upload_image").on('click', function(){
            let order_id = $(this).data("id");
            $("#hidden_order_id_upimg").val(order_id);
            $("#span_order_id").text(order_id);
            $('#modal_upload_image').modal('show');
        });
        $(".close_order").click(function(){
            var order_id = $(this).data("id");
            $('#order_close_orde_id').val(order_id);
            $('#close_orderid_span').text(order_id);
            var dataString = ({_token:"{{ csrf_token() }}",order_id:""+order_id});
            $.ajax({
                type: "POST",
                url: "{{route('order-data')}}",
                data: dataString,
                cache:false,
                success: function (data)
                {
                    let orderData = data;
                    $('#closedOrderProductTable').empty();
                    let row="";
                    Object.keys(orderData).forEach(function(key){
                        // let disabeldStatus = '';
                        // if(data.orderProducts[key].current_status=='Pending Renew'){
                        //     disabeldStatus = 'disabled=="true"';
                        // };
                        row += "<tr id='trParent'>";
                            row+="<td class='text-nowrap' data-label='product name'>"+orderData[key].product_name+" <input type='hidden' name='order_details_id[]' value="+orderData[key].id+"></td>";
                            row+="<td class='text-nowrap' data-label='sale rental'>"+orderData[key].sale_rental+"</td>";
                            row+="<td class='text-nowrap' data-label='Rent'><input type='number' class='form-control form-control-sm' name='product_rent[]' value='0' required></td>";
                            row+="<td data-label='Deposit'><input type='number' class='form-control form-control-sm' name='product_deposit[]' value='0' required></td>";
                            row+="<td data-label='Transport'> <input type='number' class='form-control form-control-sm prodTransport' name='product_transport[]' value="+orderData[key].transport+" required></td>";
                        row+="</tr>";
                    });
                    $('#closedOrderProductTable').append(row);
                    //location.reload();
                }
            });
            if($(this).data('order_type')=='Delivery'){
                $('#closedOrderProductTableDiv').show();
            }else{
                $('#closedOrderProductTableDiv').hide();
            }
            $('#orderClosedModal').modal('show');
        });
        $('.addLabour').on('click',function(){
            $('#labour_order_id').val($(this).data('order_id'));
            $('#floor_no').val($(this).data('floor_no'));
            $('#labour_charges').val($(this).data('labour_charges'));
            $('#addLabourModal').modal('show');
        })

        $('.btn_timeline').on('click',function(){
            let order_id = $(this).data('order_id');
            var dataString = ({_token:"{{ csrf_token() }}"});
            $.ajax({
                type: "POST",
                url: "{{url('/')}}/order_timeline/"+order_id,
                data: dataString,
                cache:false,
                success: function (data)
                {
                    let timeline = JSON.parse(data);
                    // console.log(timeline);
                    let row = "";
                    $('#append_div').empty();
                    $.each(timeline,function(key,value){
                        row+='<div class="vertical-timeline-item vertical-timeline-element">';
                            row+='<div> <span class="vertical-timeline-element-icon bounce-in"> <i class="badge badge-dot badge-dot-xl badge-primary"> </i> </span>';
                                row+='<div class="vertical-timeline-element-content bounce-in">';
                                    row+='<h4 class="timeline-title text-success">'+value['status']+' <span class="text-dark">('+dateFormat(value['datetime'])+')</span></h4>';
                                    if(value['type']=='DO' && value['status']=='Order Generated'){
                                        row+='<strong class="timeline-title text-success">/ Vendor Assigned </span></strong>';
                                    }
                                    row+='<p>-By '+value['user']+'</p>';
                                    row+='<span class="vertical-timeline-element-date text-dark">'+timeFormat(value['datetime'])+'</span>';
                                row+='</div>';
                            row+='</div>';
                        row+='</div>';
                    });
                    
                    $('#append_div').append(row);
                }
            });
        });

        function timeFormat(input){
            var d = new Date(Date.parse(input.replace(/-/g, "/")));
            var time = d.toLocaleTimeString().toUpperCase().replace(/([\d]+:[\d]+):[\d]+(\s\w+)/g, "$1$2");
            return (time);  
        }
        function dateFormat(input){
            var d = new Date(Date.parse(input.replace(/-/g, "/")));
            var month = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            var date = d.getDate() + " " + month[d.getMonth()] + ", " + d.getFullYear();
            return (date);  
        }

        $('.table-responsive').on('show.bs.dropdown', function () {
            $('.table-responsive').css( "overflow", "inherit" );
        });

        $('.table-responsive').on('hide.bs.dropdown', function () {
            $('.table-responsive').css( "overflow", "auto" );
        })
        $("input[name='transport_taken_not']").change(function(){
            if($("input[name='transport_taken_not']:checked").val() == 'Yes')
            {

            }
            else if($("input[name='transport_taken_not']:checked").val() == 'No')
            {
                console.log("No");
                $(".prodTransport").val("0");
            }
        });
        $(".updateClosedOrder").click(function(){
            var dataString = ({_token:"{{ csrf_token() }}",order_id:""+$(this).data('order_id'),request_type:"get-closed-order-details"});
            let order_id = $(this).data('order_id');
            $.ajax({
                type: "POST",
                url: "{{url('/')}}/getOrderDetails",
                data: dataString,
                cache:false,
                success: function (data)
                {
                    $("#update_closed_order_id_span").text(order_id);
                    $("#update_closed_order_order_id").val(order_id);
                    $("#transport_update_cost").val(data);
                    $("#orderClosedUpdateModal").modal("show");
                }
            });
        });
        $(".add-comment").click(function(){
            let order_id = $(this).data('order_id');
            $("#addcomment_orderid").text(order_id);
            $("#addcomment_hiddenorderid").val(order_id);
            $("#old_comment").text($(this).data('comment'));
            $("#addCommentModel").modal("show");
        });

        $('.other-expense').on('click',function(){
            let order_id = $(this).data('order_id');
            $('#other_exp_order_id').val(order_id);
            var dataString = ({_token:"{{ csrf_token() }}",order_id:""+order_id});
            $.ajax({
                type: "GET",
                url: "{{route('order-other-expense')}}",
                data: dataString,
                cache:false,
                success: function (data)
                {
                    $('#expense_type_input_div').removeClass('d-block');
                    $('#expense_type_input_div').addClass('d-none');
                    $('#other_exp_type_input').prop('required',false);

                    let type = ['Ola','Porter','Rapido','Uber','Other'];

                    if(data[0].expense_type!=null){
                        let exp_type = data[0].expense_type;
                        let index = type.findIndex(type=> type == data[0].expense_type);
                        if(index==-1){
                            exp_type = 'Other';
                            $('#other_exp_type_input').val(data[0].expense_type);

                            $('#expense_type_input_div').removeClass('d-none');
                            $('#expense_type_input_div').addClass('d-block');
                            $('#other_exp_type_input').prop('required',true);
                        }
                        $('#expense_amt').val(data[0].expense_amt);
                        $('#expense_type_select').val(exp_type);
                    }else{
                        $('#expense_amt').val(null);
                        $('#expense_type_select').val(null);
                    }
                    $('#expense_type_select').selectpicker('refresh');
                }
            });
            $('#other-expense-modal').modal('show');
        });

        $('#expense_type_select').on('change',function(){
            let expense_type = $(this).val();
            if(expense_type=='Other'){
                $('#expense_type_input_div').removeClass('d-none');
                $('#expense_type_input_div').addClass('d-block');
                $('#other_exp_type_input').prop('required',true);
            }else{
                $('#expense_type_input_div').removeClass('d-block');
                $('#expense_type_input_div').addClass('d-none');
                $('#other_exp_type_input').prop('required',false);
            }
            
        });
    </script>
    <script>
        $(".btn-crdrdata").click(function(){
            var dataString = ({_token:"{{ csrf_token() }}",order_id:""+$(this).val()});
            $.ajax({
                type: "POST",
                url: "{{route('crdr-data')}}",
                data: dataString,
                cache:false,
                success: function (data)
                {
                    // console.log(data);
                    let divdata = '<div class="card flex-column align-start">';
                        divdata += '<div class="list-group list-group-flush">';
                            let details_id = '';
                            $.each(data,function(key,value){
                                details_id = key;
                                divdata += '<div class="list-group-item list-group-item-action flex-column align-items-start">';
                                    divdata += '<div class="d-flex w-100 justify-content-between">';
                                        divdata += '<h5 class="mb-1">';
                                            divdata += value['product_name']+' - '+value['status'];
                                        divdata += '</h5>';
                                        divdata += '<div class="text-nowrap" role="group">';
                                            divdata += value['unique_id'];
                                        divdata += '</div>';
                                    divdata += '</div>';
                                    divdata += '<hr>';
                                    $.each(value['data'],function(index,notes){
                                        divdata += '<div class="credit-report">';
                                            divdata += '<h5 class="text-center">'+index+' Notes</h5>';
                                            divdata += '<div class="table table-responsive jim-table-responsive">';
                                                divdata += '<table class="table">';
                                                    divdata += '<thead>';
                                                        divdata += '<tr>';
                                                            divdata += '<th>Sr.No.</th>';
                                                            divdata += '<th>Field</th>';
                                                            divdata += '<th>Amount</th>';
                                                            divdata += '<th>DateTime</th>';
                                                            divdata += '<th>Created By</th>';
                                                        divdata += '</tr>';
                                                    divdata += '</thead>';
                                                    divdata += '<tbody>';
                                                        $.each(notes,function(ind,records){
                                                            divdata += '<tr>';
                                                                divdata += '<td data-label="Sr.no">'+(ind+1)+'</td>';
                                                                divdata += '<td data-label="Field">'+records.intype+'</td>';
                                                                divdata += '<td data-label="Amount">'+records.amount+'</td>';
                                                                divdata += '<td data-label="DateTime">'+records.createdat+'</td>';
                                                                divdata += '<td data-label="DateTime">'+records.createdby+'</td>';
                                                            divdata += '</tr>';
                                                        });
                                                    divdata += '</tbody>';
                                                divdata += '</table>';
                                            divdata += '</div>';
                                        divdata += '</div>';
                                    });
                                    if(value['remark'] != null){
                                        divdata += '<div>';
                                            divdata += '<span>Product Remark : '+value['remark']+'</span>';
                                        divdata += '</div>';
                                    }
                                divdata += '</div>';
                            });
                            if(data[details_id]['comment'] != null && data[details_id]['comment'] != ''){
                                divdata += '<div>';
                                    divdata += '<span>Order Remark : '+data[details_id]['comment']+'</span>';
                                divdata += '</div>';
                            }
                            $("#hidden_order_id_crdr_img").val(data[details_id]['order_id']);
                            $("#viewimagecrdrnote").attr("src","{{url('/')}}/assets/uploads/crdr_images/"+data[details_id]['cr_dr_img']);
                        divdata += '</div>';
                    divdata += '</div>';
                    $(".crdrnotes-body").empty().append(divdata);
                    $("#crdr-data-modal").modal("show");
                },
                error: function (err)
                {
                    console,log(err);
                    alert ("Something Went Wrong Try Again!");
                }
            });
        });

        // $('.order-xml').on('click',function(){
        //     let lead_id = $(this).data('lead_id');
        //     var dataString = ({_token:"{{ csrf_token() }}",lead_id:""+lead_id});
        //     $.ajax({
        //         type: "POST",
        //         url: "{{url('/')}}/order-xml-export",
        //         data: dataString,
        //         cache: false,
        //         success: function (res) {
        //             console.log(res);
                    
        //             // var xmlDocument = $.parseXML(res);

        //             // let today =new Date;
        //             // var xmlDocument = res;
        //             // xmlDocument = new Blob([xmlDocument]);
        //             // const link = document.createElement('a');
        //             // link.setAttribute('href', URL.createObjectURL(xmlDocument));
        //             // link.setAttribute('download', today.getDate()+'-'+(today.getMonth()+1)+'-'+today.getFullYear()+'_voucher.xml'); // Need to modify filename ...
        //             // link.click();
        </script>
    <script>
        $(".cash-collection-request").click(function(){
            $("#btnsubmit").text('Generate Cash Collection');
            $("#ccrequestModal").modal("show");
            $(".loading-spinner").show();
            $(".ccad-modal-content").hide();
            $("#_method").hide();
            $("#_method").val('POST');
            // $(".ccad-form").attr('method',"POST");
            let leadid = $(this).data("leadid");
            dataString = ({leadid:leadid});
            $.ajax({
                type:"GET",
                url:"{{route('ccad.create')}}",
                cache:false,
                data:dataString,
                success:function(res){
                    // console.log(res);
                    $(".ccad-form").attr('action',"{{route('ccad.store')}}");
                    $('#ccaddelassigned').find("option")
                                        .remove()
                                        .end();
                    $.each(res.delboys,function(key,value){
                        $("#ccaddelassigned").append("<option value='"+value.username+"'>"+value.username+"</option>");
                    });
                    $("#ccaddelassigned").selectpicker('refresh');
                    $("#ccadorderamount").val(res.totalamt);
                    $("#ccadamounttocollect").val(res.totalamt);
                    $("#btnsubmit").val(res.custdetails.lead_id);
                    $("#ccadorderid").val(res.custdetails.order_id);
                    $(".loading-spinner").hide();
                    $(".ccad-modal-content").show();
                },
                error:function(err){

                }
            });
        });
        $(".cash-collection-request-update").click(function(){
            $("#btnsubmit").text('Update Cash Collection');
            $("#ccrequestModal").modal("show");
            $(".loading-spinner").show();
            $(".ccad-modal-content").hide();
            let orderid = $(this).data("orderid");
            let leadid = $(this).data("leadid");
            $("#_method").show();
            //$(".ccad-form").attr('method',"PUT");
            // $("#_method").val('PUT');
            dataString = ({orderid:orderid,leadid:leadid});
            $.ajax({
                type:"GET",
                // url:"{{route('ccad.edit',"+orderid+")}}",
                url:"{{url('/')}}/ccad/"+orderid+"/edit",
                cache:false,
                data:dataString,
                success:function(res){
                    // console.log(res);
                    $(".ccad-form").attr('action',"{{url('/')}}/ccad/"+orderid);
                    $('#ccaddelassigned').find("option")
                                        .remove()
                                        .end();
                    $.each(res.delboys,function(key,value){
                        $("#ccaddelassigned").append("<option value='"+value.username+"'>"+value.username+"</option>");
                    });
                    // console.log("DelAssignedTo : "+res.custdetails.DelAssignedTo);
                    $("#ccaddelassigned").selectpicker('refresh');
                    $("#ccaddelassigned").selectpicker('val',res.custdetails.DelAssignedTo);
                    $("#ccaddelassigned").selectpicker('refresh');
                    $("#ccaddate").val(res.custdetails.DelDate);
                    $("#ccadamounttocollect").val(res.custdetails.TotalAmt);
                    // $("#ccaddelassigned").selectpicker('render');
                    $("#ccadorderamount").val(res.totalamt);
                    $("#btnsubmit").val(res.custdetails.lead_id);
                    $("#ccadorderid").val(res.custdetails.order_id);
                    $(".loading-spinner").hide();
                    $(".ccad-modal-content").show();
                },
                error:function(err){
                    console.log(err);
                }
            });
        });

        $('.table-responsive').on('show.bs.dropdown', function () {
            $('.table-responsive').css( "overflow", "inherit" );
        });

        $('.table-responsive').on('hide.bs.dropdown', function () {
            $('.table-responsive').css( "overflow", "auto" );
        })
    </script>
    <script>
        $("#search-customer").click(function(){
            if($("#searchcustomertxt").val().length == 0){
                $("#searchcustomertxt").addClass('glowing-border');
            }
            else{
                $("#searchcustomertxt").removeClass('glowing-border');
                let dataString = ({searchcustomertxt:$("#searchcustomertxt").val(),reqtype:"getcustomers"});
                $("#customer-table-records").empty();
                $(".customer-details-card").addClass('d-none');
                $.ajax({
                    type:"GET",
                    url:"{{route('replace-order.create')}}",
                    cache:false,
                    data:dataString,
                    success:function(res){
                        console.log(res);
                        var tr = null;
                        $.each(res,function(key,value){
                            tr += '<tr>';
                                tr += '<td data-label="Order Date">'+value[0].DelDate+'</td>';
                                tr += '<td data-label="Customer Name">'+value[0].shipping_first_name+'</td>';
                                tr += '<td data-label="Patient Name">'+value[0].patient_name+'</td>';
                                tr += '<td data-label="Address">'+value[0].fulldetails+'</td>';
                                // tr += '<td><a href="'+"{{route('replace-order-create')}}?id="+key+'" class="btn btn-sm btn-outline-primary">View</a></td>';
                                tr += '<td><button type="button" class="btn btn-sm btn-outline-primary viewproducts" onclick="fetchProducts('+key+')" id="'+key+'">View</button></td>';
                            tr += '</tr>';
                        });
                        $("#customer-table-records").append(tr);
                        $("#customer-details-table").dataTable('refresh');
                        $(".customer-details-card").removeClass('d-none');
                    },
                    error:function(err){
                        console.log(err);
                        $(".customer-details-card").addClass('d-none');
                    }
                });
            }
        });
        function fetchProducts(id){
            // console.log("Products");
            $("#createReplacement").modal("show");
            $("#product-table-records").empty();
            let dataString = ({leadid:id,reqtype:"productdetails"});
            $.ajax({
                type:"GET",
                url:"{{route('replace-order.create')}}",
                data:dataString,
                cache:false,
                success:function(res){
                    console.log(res);
                    var tr = null;
                    $.each(res,function(key,value){
                        tr += '<tr>';
                            tr += '<td><input type="checkbox" name="checkedproducts[]" id="'+key+'" value="'+value.id+'"></td>';
                            tr += '<td data-label="Delivery Date">'+value.creation_date+'</td>';
                            tr += '<td data-label="Product Name">'+value.product_name+'</td>';
                            tr += '<td data-label="Patient Name">'+value.registered_name+'</td>';
                            tr += '<td data-label="Address">'+value.wh_name+', '+value.wh_area+', '+value.wh_city+'</td>';
                        tr += '</tr>';
                    });
                    $("#product-table-records").append(tr);
                    $("#product-details-table").dataTable('refresh');
                    $(".product-details-card").removeClass('d-none');
                },
                error:function(err){
                    console.log(err);
                }
            });
        }
        function editCollection($order_id){
            $("#update_collection_modal_body").empty();
            $.ajax({
                type:"GET",
                url:"{{route('edit-collection')}}?order_id="+$order_id,
                cache:false,
                success:function(res){
                    let data = res;
                    let details_count = data.length;
                    let table = '<div class="table table-responsive jim-table-responsive">';
                        table += '<table class="table" id="order_details_table">';
                            table += '<thead>';
                                table += '<th>Sr. No</th>';
                                table += '<th>Date</th>';
                                table += '<th>Product Name</th>';
                                table += '<th>Vendor Name</th>';
                                table += '<th>Warehouse Name</th>';
                                table += '<th>Rent</th>';
                                table += '<th>Updated Rent</th>';
                                table += '<th>Adjusted Deposit</th>';
                                table += '<th>Discount</th>';
                                table += '<th>Period</th>';
                            table += '</head>';
                            table += '<tbody>';
                            for (let i = 0; i < details_count; i++)
                            {
                                let sr_no = i+1;
                                table += '<tr class="text-wrap">';
                                    table +='<td data-label="Sr.No.">'+sr_no+'</td>';
                                    table +='<td data-label="Date">'+data[i].date+'</td>';
                                    if(data[i].sale_rental == 'Rental'){
                                        table +='<td data-label="Product Name">'+data[i].product_name+'<br>('+data[i].unique_id+')</td>';
                                    }else{
                                        table +='<td data-label="Product Name">'+data[i].product_name+'</td>';
                                    }
                                    table +='<td data-label="Vendor Name">'+data[i].vendor_name+'</td>';
                                    table +='<td data-label="Warehouse">'+data[i].warehouse_name+', '+data[0].warehouse_area+', '+data[0].warehouse_city+'</td>';
                                    table +='<td data-label="Rent">'+data[i].product_rent+'</td>';
                                    table +='<td data-label="Updated Rent"><input type="hidden" class="form-control form-control-sm" name="renewal_id[]" id="renewal_id'+i+'"value="'+data[i].renewal_id+'"><input type="hidden" name="actual_rent[]" id="actual_rent'+i+'"value="'+data[i].product_rent+'"><input type="number" class="form-control form-control-sm" name="updated_rent[]" id="updated_rent'+i+'"value="'+data[i].product_rent+'" required></td>';
                                    table +='<td data-label="Deposit">'+data[i].adjusted_deposit+'</td>';
                                    table +='<td data-label="Discount">'+data[i].discount_amt+'</td>';
                                    table +='<td data-label="Period">'+data[i].start_date+' - '+data[i].end_date+'</td>';
                                table += '</tr>';
                            }
                            table += '</tbody>';
                        table += '</table>';
                    table += '</div>';
                    $('#updateco_orderid').val(data[0].order_id);
                    $('#updateco_customername').text(data[0].shipping_first_name);
                    $('#updateco_mobileno').text(data[0].mobileno);
                    $('#updateco_email').text(data[0].email_id);
                    $('#updateco_location').text(data[0].location);
                    $('#updateco_address').text(data[0].fulldetails);
                    $("#updateco_del_assigned_to").text(data[0].DelAssignedTo);
                    $("#updateco_helpers").text(data[0].helpers);
                    $("#update_collection_modal_body").append(table);
                    $("#updateCollectionOrder").modal("show");
                },
                error:function(er){

                }
            });
        }
    </script>
@endsection
