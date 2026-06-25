@extends('header_and_sidebar')
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
    @if(session()->has('error'))
        <div class="alert alert-danger">
            {{ session()->get('error') }}
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
                        <strong>Customers</strong>
                    </div>
                    <div class="col-auto">
                        <a data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                            <i class="fa fa-chevron-down pull-right"></i>&emsp;<b>Filter</b>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body border-primary collapse" id="filter-collapse" aria-labelledby="headingTwo" data-parent="#filter_card">
                <form action="{{route('customer-master.index')}}" method="GET">
                    @csrf
                    <div class="row form-group">
                        <div class="col-md-3">
                            <label for="filter_customer_name">Customer Name</label>
                            <input type="text" name="filter_customer_name" id="filter_customer_name" value="{{request()->get('filter_customer_name')}}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label for="filter_contact_no">Contact No</label>
                            <input type="number" name="filter_contact_no" id="filter_contact_no" value="{{request()->get('filter_contact_no')}}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label for="filter_customer_id">Customer Id</label>
                            <input type="number" name="filter_customer_id" id="filter_customer_id" value="{{request()->get('filter_customer_id')}}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label for="filter_customer_type">Customer Type</label>
                            <select name="filter_customer_type" id="filter_customer_type" class="select selectpicker form-control form-control-sm">
                                <option value="All" selected>All</option>
                                <option value="Individual" @if(request()->get('filter_customer_type')=="Individual"){{"selected"}}@endif>Individual</option>
                                <option value="Corporate" @if(request()->get('filter_customer_type')=="Corporate"){{"selected"}}@endif>Corporate</option>
                            </select>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-sm btn-outline-primary">Search</button>
                            <a href="{{route('customer-master.index')}}" class="btn btn-sm btn-outline-secondary">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="table table-responsive jim-table-responsive">
                <table class="table table-hover table-flush ">
                    <thead class="thead thead-primary text-white border-primary">
                        <tr class="text-nowrap border-primary">
                            <th>Created At</th>
                            <th>Customer Id</th>
                            <th>Customer Name</th>
                            <th>Contact No</th>
                            <th>Location</th>
                            <th>Customer Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $key=>$customer)
                            <tr>
                                <td data-label="Created At">{{date('d-M-y H:m A',strtotime($customer->created_at))}}</td>
                                <td data-label="Customer Id">{{$customer->cust_id}}</td>
                                <td data-label="customer Name">{{$customer->customer_name}}</td>
                                <td data-label="contact_no">{{$customer->primary_contact_no}}</td>
                                <td data-label="Location">
                                    {{$customer->location}}
                                </td>
                                <td data-label="Customer Type">{{$customer->customer_type}}</td>
                                <td data-label="Action">
                                    <a href="{{route('customer-master.show',$customer->cust_id)}}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                                    <a href="{{route('customer-master.edit',$customer->cust_id)}}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                </td>
                            </tr>
                        @empty
                            {{-- <tr><td colspan="6" class="d-flex align-item-center justify-content-center">No Records Found</td></tr> --}}
                            <tr class="text-center"><td colspan="67"><h3>No Records Found</h3></td></tr>
                        @endforelse
                    </tbody>
                </table>
                {{$customers->withPath(url()->full())->links('Custom.Pagination.pagination')}}
            </div>
        </div>
    </div>
@endsection