@extends('header_and_sidebar')

@section('style')
@endsection

@section('content')
    <div class="card">
        <div class="card-header">Lead Order Comparison Report</div>
        <div class="card-body">
            <div class="table table-reponsive jim-table-responsive">
                <table clas="table table-stripped" id="records" width = "100%">
                    <thead>
                        <tr>
                            <td>Sr. No.</td>
                            <td>Date</td>
                            <td>Type</td>
                            <td>Rent</td>
                            <td>Sale</td>
                            <td>Deposit</td>
                            <td>Transport</td>
                            <td>Total</td>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($date_report as $key=>$value)
                            <tr>
                                <td>{{$key+1}}</td>
                                <td>{{date('d-m-Y',strtotime($value['date']))}}</td>
                                <td>{{$value['type']}}</td>
                                <td>{{$value['rent']}}</td>
                                <td>{{$value['sale']}}</td>
                                <td>{{$value['deposite']}}</td>
                                <td>{{$value['transport']}}</td>
                                <td>{{$value['rent'] + $value['sale'] + $value['deposite'] + $value['transport']}}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7"><h4>No Records Found</h4></td>
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