@extends('header_and_sidebar')
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
                        <button type="button" id="export" class="btn btn-primary">Export To Excel</button><br>
                    </center>
                        <form action="{{url('/')}}/monthly_report" method="post">
                            <div class="row my-2">
                                <div class="col-md-3">
                                        <label for="">Start Date</label>
                                    <input type="date" class="form-control form-control" name="start_date" id="" value="@if(isset($start_date)){{$start_date}}@endif">
                                </div>
                                <div class="col-md-3">
                                    <label for="">End Date</label>
                                    <input type="date" class="form-control" name="end_date" id="" value="@if(isset($end_date)){{$end_date}}@endif">
                                </div>
                                <div class="col-md-3">
                                    <label for="">Action</label>
                                    <br>
                                    <button type="submit" name="searchByMonth" id="searchByMonth" class="btn btn-primary">Search</button>
                                    <a href="{{url('/')}}/monthly_report" class="btn btn-outline-secondary">clear</a>
                                </div>
                            </div>
                        </form>
                    <div class="table table-responsive">
                        <table data-page-length='250' class="table table-bordered" >
                            <thead class="text-center">
                                <tr>
                                    <th rowspan="3">Sr.No</th>
                                    <th rowspan="3">Product Name</th>
                                    <th colspan="3">Total Revenue</th>
                                    <th colspan="3">Total Count</th>
                                    <th colspan="3">Rental Revenue</th>
                                    <th colspan="3">Rental Count</th>
                                    <th colspan="3">Sale Revenue</th>
                                    <th colspan="3">Sale Count</th>
                                    <th colspan="3">Renewal Revenue</th>
                                    <th colspan="3">Renewal Count</th>
                                    <th colspan="3">Overdue Revenue</th>
                                    <th colspan="3">Overdue Count</th>
                                    <th rowspan="3">Category</th>
                                </tr>
                                <tr>
                                    <th rowspan="2">Corporate</th>
                                    <th colspan="2">Individual</th>
                                    <th rowspan="2">Corporate</th>
                                    <th colspan="2">Individual</th>
                                    <th rowspan="2">Corporate</th>
                                    <th colspan="2">Individual</th>
                                    <th rowspan="2">Corporate</th>
                                    <th colspan="2">Individual</th>
                                    <th rowspan="2">Corporate</th>
                                    <th colspan="2">Individual</th>
                                    <th rowspan="2">Corporate</th>
                                    <th colspan="2">Individual</th>
                                    <th rowspan="2">Corporate</th>
                                    <th colspan="2">Individual</th>
                                    <th rowspan="2">Corporate</th>
                                    <th colspan="2">Individual</th>
                                    <th rowspan="2">Corporate</th>
                                    <th colspan="2">Individual</th>
                                    <th rowspan="2">Corporate</th>
                                    <th colspan="2">Individual</th>
                                </tr>
                                <tr>
                                    <th>Online</th>
                                    <th>Offline</th>
                                    <th>Online</th>
                                    <th>Offline</th>
                                    <th>Online</th>
                                    <th>Offline</th>
                                    <th>Online</th>
                                    <th>Offline</th>
                                    <th>Online</th>
                                    <th>Offline</th>
                                    <th>Online</th>
                                    <th>Offline</th>
                                    <th>Online</th>
                                    <th>Offline</th>
                                    <th>Online</th>
                                    <th>Offline</th>
                                    <th>Online</th>
                                    <th>Offline</th>
                                    <th>Online</th>
                                    <th>Offline</th>
                                </tr>
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
                                            <td>{{number_format($details['total_amount_corp'],2)}}</td>
                                            <td>{{number_format($details['total_amount_indon'],2)}}</td>
                                            <td>{{number_format($details['total_amount_indoff'],2)}}</td>
                                            <td>{{number_format($details['total_count_corp'])}}</td>
                                            <td>{{number_format($details['total_count_indon'])}}</td>
                                            <td>{{number_format($details['total_count_indoff'])}}</td>
                                            
                                            <td>{{number_format($details['rental_amount_corp'],2)}}</td>
                                            <td>{{number_format($details['rental_amount_indon'],2)}}</td>
                                            <td>{{number_format($details['rental_amount_indoff'],2)}}</td>
                                            <td>{{number_format($details['rental_count_corp'])}}</td>
                                            <td>{{number_format($details['rental_count_indon'])}}</td>
                                            <td>{{number_format($details['rental_count_indoff'])}}</td>

                                            <td>{{number_format($details['sale_amount_corp'],2)}}</td>
                                            <td>{{number_format($details['sale_amount_indon'],2)}}</td>
                                            <td>{{number_format($details['sale_amount_indoff'],2)}}</td>
                                            <td>{{number_format($details['sale_count_corp'])}}</td>
                                            <td>{{number_format($details['sale_count_indon'])}}</td>
                                            <td>{{number_format($details['sale_count_indoff'])}}</td>

                                            <td>{{number_format($details['renewal_amount_corp'],2)}}</td>
                                            <td>{{number_format($details['renewal_amount_indon'],2)}}</td>
                                            <td>{{number_format($details['renewal_amount_indoff'],2)}}</td>
                                            <td>{{number_format($details['renewal_count_corp'])}}</td>
                                            <td>{{number_format($details['renewal_count_indon'])}}</td>
                                            <td>{{number_format($details['renewal_count_indoff'])}}</td>

                                            <td>{{number_format($details['overdue_amount_corp'],2)}}</td>
                                            <td>{{number_format($details['overdue_amount_indon'],2)}}</td>
                                            <td>{{number_format($details['overdue_amount_indoff'],2)}}</td>
                                            <td>{{number_format($details['overdue_count_corp'])}}</td>
                                            <td>{{number_format($details['overdue_count_indon'])}}</td>
                                            <td>{{number_format($details['overdue_count_indoff'])}}</td>
                                            <td>
                                                @if($details['category'] == 'CO')
                                                    Consumables
                                                @elseif($details['category'] == 'MO')
                                                    Mobility
                                                @elseif($details['category'] == 'RC')
                                                    Respiratory
                                                @else

                                                @endif
                                            </td>
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
            </div>
        </form>
    </div>   
    @endsection
    @section('script')
        <script>
            $("#records").dataTable();
            $(document).ready(function()
            {
                // var table = $('#records').DataTable();
                
                $('#export').on('click', function(){
                    $('<table>').append($('tr').clone()).table2excel({
                        //exclude: ".excludeThisClass",
                        //name: "abc",
                        filename: "Monthly Records {{$start_date}}-{{$end_date}}"
                    });
                }); 
            });
        </script>                                                         
    @endsection