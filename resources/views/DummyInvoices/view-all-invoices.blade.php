@extends('header_and_sidebar')

@section('style')

@endsection

@section('content')

    @if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="card border-primary">
        <div class="card-header text-white bg-primary">
            <strong>View All Invoices</strong>
        </div>
        <div class="card-body">
            <div class="table table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Sr.No.</th>
                            <th>Date</th>
                            <th>Customer Name</th>
                            <th>Patient Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $key=>$invoice)
                            <tr>
                                <td>{{$invoices->firstItem()+$loop->index}}</td>
                                <td>{{date('d-M-y',strtotime($invoice->date))}}</td>
                                <td>{{$invoice->cust_name}}</td>
                                <td>{{$invoice->patient_name}}</td>
                                <td>
                                    <a href="{{route('view-dummy-invoice')}}?id={{$invoice->id}}" class="btn btn-sm btn-outline-primary" data-toggle="View" tooltip="View"><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class='text-center'>No Records Found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('script')

@endsection