@extends('header_and_sidebar')
@section('title')
   Day By Day Report
@endsection
    @section('header')
       
    @endsection

    @section('content')
    <div class="container-fluid">
        
        <form class="form" action="" method="post" >
            {{ csrf_field() }}
            <div class="card">
                <div class="card-header" style="background-color: #337ab7; color: white;">
                    <center>
                        <b>Web Renewals</b>
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
                    <div class="row mb-2">
                        <form class="" action="{{route('WebRenewals_report')}}" method="get">
                            @csrf
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="date" class="form-control form-control-sm" name="start_date" id="" value="{{request()->get('start_date')}}">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="date" class="form-control form-control-sm" name="end_date" id="" value="{{request()->get('end_date')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-sm btn-success">Search</button>
                            </div>
                        </form>
                    </div>
                    <div class="row">                        
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <th>Sr.No.</th>
                                    <th>Date</th>
                                    <th>Customer Name</th>
                                    <th>Contact Number</th>
                                    <th>Equipments</th>
                                    <th>Sale/Rental</th>
                                    <th>Rents</th>
                                    <th>Deposites</th>
                                    <th>Transport</th>
                                    <th>Total Amount</th>
                                </thead>
                                <tbody>
                                    @php
                                        $srno = 1;
                                    @endphp
                                    @foreach($report_d as $date =>$customerOrderData)
                                        @php
                                            $i=0;
                                        @endphp
                                        @foreach ($customerOrderData as $custkey=>$custOrders)
                                            @foreach ($custOrders as $key=>$product)
                                                <tr>
                                                    @if($i===0)
                                                        <td>{{$srno++}}</td>
                                                        <td class="text-nowrap">{{$date}}</td>
                                                    @else
                                                        <td></td>
                                                        <td></td>
                                                    @endif
                                                    @if($key===0)
                                                        <td>{{$custOrders[0]->customer_name}}</td>
                                                        <td>{{$custOrders[0]->primary_contact_no}}</td>
                                                    @else
                                                        <td></td>
                                                        <td></td>
                                                    @endif
                                                    <td>{{$custOrders[$key]->product_name}}</td>
                                                    <td>{{$custOrders[$key]->sale_rental}}</td>
                                                    <td>{{$custOrders[$key]->product_rent}}</td>
                                                    <td>{{$custOrders[$key]->product_deposite}}</td>
                                                    <td>{{$custOrders[$key]->transport}}</td>
                                                    <td>{{$custOrders[$key]->product_rent+$custOrders[$key]->product_deposite+$custOrders[$key]->transport}}</td>
                                                </tr>
                                                @php
                                                    $i++;
                                                @endphp
                                            @endforeach
                                                
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                            {{$report_d->links('Custom.Pagination.pagination')}}
                            {{-- <center><button type="button" id="export" class="btn btn-primary">Export To Excel</button></center> --}}
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>   
    @endsection
    @section('script')
        <script>
            // $(document).ready(function()
            // {
            //     var table = $('#records').DataTable();
                
            //     $('#export').on('click', function(){
            //         $('<table>').append(table.$('tr').clone()).table2excel({
            //             //exclude: ".excludeThisClass",
            //             //name: "abc",
            //             filename: "DayByDay Report-{{date('d-m-Y')}}"
            //         });
            //     });
            // });
        // function monthly_report() {
        //     document.getElementById("monthly_report").style.display = "block";
        //     document.getElementById("datewise_report").style.display = "none";
        // }
        // function datewise_report() {
        //     document.getElementById("datewise_report").style.display = "block";
        //     document.getElementById("monthly_report").style.display = "none";
        // }
        </script>                                                         
    @endsection