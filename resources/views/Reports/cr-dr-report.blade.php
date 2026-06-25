@extends('header_and_sidebar')

@section('styles')

@endsection
@section('content')
    <div class="card my-3">
        <div class="card-header border-primary" id="report-filter">
            <div class="row">
                <div class="col text-primary" data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                    <strong>Credit/Debit Report</strong>
                </div>
                <div class="col-auto">
                    <a data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                        <i class="fa fa-chevron-down pull-right"></i>&emsp;<b>Filter</b>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body border-primary collapse" id="filter-collapse" aria-labelledby="headingTwo" data-parent="#report-filter">
            <form action="{{route('cr-dr-report')}}" method="GET">
                <div class="row form-group">
                    <div class="col-md-4">
                        <label for="orderid">OrderId</label>
                        <input type="text" name="orderid" id="orderid" value="{{request()->get('orderid')}}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <label for="customername">Customer Name</label>
                        <input type="text" name="customername" id="customername" value="{{request()->get('customername')}}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <label for="patientname">Patient Name</label>
                        <input type="text" name="patientname" id="patientname" value="{{request()->get('patientname')}}" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-4">
                        <label for="contactno">Contact No</label>
                        <input type="text" name="contactno" id="contactno" value="{{request()->get('contactno')}}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <label for="orderstartdate">Date Range</label>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="date" name="orderstartdate" id="orderstartdate" value="{{request()->get('orderstartdate')}}" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-6">
                                <input type="date" name="orderenddate" id="orderenddate" value="{{request()->get('orderenddate')}}" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex">
                        <button type="submit" name="submit" id="search" class="btn btn-outline-primary"><i class="fas fa-search"></i> Search</button>
                        <a href="{{route('cr-dr-report')}}" class="btn btn-outline-secondary d-flex align-items-center ml-2"><i class="fas fa-filter"></i> Clear</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <span>Note : R-Rent/Rate&emsp;D-Deposit&emsp;T-Transport&emsp;Cr-Credit&emsp;Dr-Debit</span>
    <div class="table table-responsive jim-table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <td>Order Id</td>
                    <td>Order Date</td>
                    <td>Customer Name</td>
                    <td>Patient Name</td>
                    <td>Contact</td>
                    <td>Credit Amount</td>
                    <td>Debit Amount</td>
                </tr>
            </thead>
            <tbody>
                @forelse($details as $key=>$record)
                    <tr scope="row" data-toggle="collapse" data-target="#collapseTable{{$key}}" class="data-toggle" data-id="{{$key}}">
                        <td>{{$record[0]->order_id}}</td>
                        <td>{{$record[0]->DelDate}}</td>
                        <td>{{$record[0]->shipping_first_name}}</td>
                        <td>{{$record[0]->patient_name}}</td>
                        <td>{{$record[0]->mobileno}}</td>
                        <td>@if(isset($record->groupBy('crdrtype')['Cr'])){{$record->groupBy('crdrtype')['Cr']->pluck('amount')->sum()}}@else{{0}}@endif</td>
                        <td>@if(isset($record->groupBy('crdrtype')['Dr'])){{$record->groupBy('crdrtype')['Dr']->pluck('amount')->sum()}}@else{{0}}@endif</td>
                        <tr class="collapse" id="collapseTable{{$key}}">
                            <td colspan="7">
                                <div class="card flex-column align-start border-primary">
                                    <div class="list-group list-group-flush">
                                        @foreach($record->groupBy('order_details_id') as $k=>$product)
                                            <div class="list-group-item list-group-item-action flex-column align-items-start">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h5 class="mb-1">
                                                        {{$product[0]->product_name}}
                                                    </h5>
                                                    <div class="text-nowrap" role="group">
                                                        {{$product[0]->unique_id}}
                                                    </div>
                                                </div>
                                                <hr>
                                                @foreach($product->groupBy('crdrtype') as $index=>$notes)
                                                    <div class="credit-report">
                                                        <h5 class="text-center">{{$index}} Notes</h5>
                                                        <div class="table table-responsive jim-table-responsive">
                                                            <table class="table">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Sr.No.</th>
                                                                        <th>Field</th>
                                                                        <th>Og Amount</th>
                                                                        <th>{{$index}} Amount</th>
                                                                        <th>DateTime</th>
                                                                        <th>Note Created</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($notes as $kindex=>$value)
                                                                        <tr>
                                                                            <td data-label="Sr.no">{{$kindex + 1}}</td>
                                                                            <td data-label="Field">{{$value->intype}}</td>
                                                                            <td data-label="Og Amount">
                                                                                @if($value->intype == 'R')
                                                                                    {{$value->product_rent}}
                                                                                @elseif($value->intype == 'D')
                                                                                    {{$value->product_deposite}}
                                                                                @else
                                                                                    {{$value->transport}}
                                                                                @endif
                                                                            </td>
                                                                            <td data-label="{{$index}} Amount">{{$value->amount}}</td>
                                                                            <td data-label="DateTime">{{$value->createdat}}</td>
                                                                            <td data-label="DateTime">{{$value->createdby}}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                @if($product[0]->remark != null)
                                                    <div>
                                                        <span>Product Remark : {{$product[0]->remark}}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                        @if($product[0]->comment != null)
                                            <div>
                                                <span>Order Remark : {{$product[0]->comment}}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tr>
                @empty

                @endforelse
            </tbody>
        </table>
        {{$details->withPath(url()->full())->links('Custom.Pagination.pagination')}}
    </div>
@endsection
@section('script')

@endsection