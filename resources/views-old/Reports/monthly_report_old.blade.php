{{-- @extends('header_and_sidebar') --}}
@extends('new-sidebar')

@section('title')
   Monthly Report
@endsection
    @section('header')
       
    @endsection

    @section('content')
    <div class="container-fluid">
        <br>
        <form class="form" action="" method="post" >
            {{ csrf_field() }}
            <div class="card">
                <div class="card-header" style="background-color: #337ab7; color: white;">
                    <center>
                        <b>Monthly Reports</b>
                    </center>
                </div>
                @if ($errors->any())
                    @foreach ($errors->all() as $error)
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            {{$error}}
                        </div>
                    @endforeach
                @endif
                <div class="card-body">
                    <center>
                        @php
                            $month_ar = [
                                1=>'January',
                                2=>'February',
                                3=>'March',
                                4=>'April',
                                5=>'May',
                                6=>'June',
                                7=>'July',
                                8=>'August',
                                9=>'September',
                                10=>'October',
                                11=>'November',
                                12=>'December'
                            ];
                        @endphp
                        <select class="selectpicker" title="Select Month" name="month" id="month">
                            @foreach($month_ar as $key => $month)
                                <option value="{{$key}}"
                                    @if(isset($month_count)) 
                                        @if($month_count == $key)
                                            {{"selected"}}
                                        @endif
                                    @elseif(date('m') == $month_count)
                                        {{"selected"}}
                                    @endif>
                                    {{$month}}
                                </option>
                            @endforeach
                        </select>
                    </center>
                    <table id="records" class="table table-bordered">
                        <thead>
                            <th>Sr.No</th>
                            <th>Product Name</th>
                            <th>Total Revenue</th>
                            <th>Total Count</th>
                            <th>New Revenue</th>
                            <th>New Count</th>
                            <th>Renewal Revenue</th>
                            <th>Renewal Count</th>
                        </thead>
                        <tbody>
                            @if(isset($monthReportDetails))
                                @php
                                    $sr_no = 1;
                                @endphp
                                @foreach($monthReportDetails as $details)
                                    <tr>
                                        <td>{{$sr_no}}</td>
                                        <td>{{$details['product_name']}}</td>
                                        <td>{{$details['total_amount']}}</td>
                                        <td>{{$details['total_count']}}</td>
                                        <td>{{$details['rental_amount']}}</td>
                                        <td>{{$details['rental_count']}}</td>
                                        <td>{{$details['renewal_amount']}}</td>
                                        <td>{{$details['renewal_count']}}</td>
                                    </tr>
                                    @php
                                        $sr_no ++;
                                    @endphp
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>   
    @endsection
    @section('script')
        <script>
        </script>                                                         
    @endsection