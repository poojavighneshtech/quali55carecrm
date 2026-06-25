@extends('header_and_sidebar')

@section('styles')
@endsection

@section('content')
    <div class="container my-3">
        <div class="card">
            <div class="card-header">
                Financial Year Reports
            </div>
            <div class="card-body">
                <div class="table table-responsive my-3">
                    <table class="table table-stripped">
                        <tbody>
                            <tr>
                                <th>Financial Year</th>
                                @foreach($fy_record as $key=>$value)
                                    <td><b>{{$value->fy}}</b></td>
                                @endforeach
                            </tr>
                            <tr>
                                <th>Total</th>
                                @foreach($fy_record as $key=>$value)
                                    @if(isset($value->total))
                                        <td><b>{{$value->total}}</b></td>
                                    @else
                                        <td><b>{{$value->total_rental+$value->transport+$value->sale+$value->sale_transport}}</b></td>
                                    @endif
                                @endforeach
                            </tr>
                            <tr>
                                <th>Deposit Collected</th>
                                @foreach($fy_record as $key=>$value)
                                    <td>{{$value->total_depo_collected}}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <th>Last Year Deposit</th>
                                @foreach($fy_record as $key=>$value)
                                    <td>{{$value->last_year_depo}}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <th>Deposit Returned</th>
                                @foreach($fy_record as $key=>$value)
                                    <td>{{$value->depo_returned}}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <th>Total Rental</th>
                                @foreach($fy_record as $key=>$value)
                                    <td>{{$value->total_rental}}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <th>Vendor Payment</th>
                                @foreach($fy_record as $key=>$value)
                                    <td>{{$value->vdr_payment}}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <th>Transport</th>
                                @foreach($fy_record as $key=>$value)
                                    <td>{{$value->transport}}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <th>Sale</th>
                                @foreach($fy_record as $key=>$value)
                                    <td>{{$value->sale}}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <th>Sale Transport</th>
                                @foreach($fy_record as $key=>$value)
                                    <td>{{$value->sale_transport}}</td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- <div class="card card-body my-3" id="bar_chart" style="width: 100%; height: 500px;">

        </div> --}}
    </div>
@endsection

@section('script')

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script>
        google.charts.load('current', {'packages':['bar']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
            ['Fields', '2020-2021', '2021-2022', '2022-2023'],
            ['Deposit Collected',{{implode(',',$Deposit_Collected)}}],
            ['Last Year Deposit', {{implode(',',$Last_Year_Deposit)}}],
            ['Deposit Returned', {{implode(',',$Deposit_Returned)}}],
            ['Total Rental', {{implode(',',$Total_Rental)}}],
            ['Vendor Payment', {{implode(',',$Vendor_Payment)}}],
            ['Transport', {{implode(',',$Transport)}}],
            ['Sale', {{implode(',',$Sale)}}],
            ['Sale Transport', {{implode(',',$Sale_Transport)}}]
            ]);

            var options = {
            chart: {
                title: 'Financial Year Report',
                subtitle: '2020-2021, 2021-2022, 2022-2023',
            },
            bars: 'vertical' // Required for Material Bar Charts.
            };

            var chart = new google.charts.Bar(document.getElementById('bar_chart'));

            chart.draw(data, google.charts.Bar.convertOptions(options));
        }
        $('#fy_year_filter').on('change',function(){
            window.location.assign(this.value);
        });
    </script>
    

@endsection