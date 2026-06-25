@extends('header_and_sidebar')



@section('styles')
    <style>
        /* th{
            background-color: #4e73df;
            color: white;
        } */

        /* .card_scroll{
            width: 400px;
            height: 370px;
            margin: 30px auto;
        } */
        .scrollable_card{
            overflow-y: auto;
            max-height: 600px;
        }

    </style>
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
    <div class="card my-3 card_scroll">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8  justify-content-start">
                    <h4>
                        Monthly Report 
                        <select class="select selectpicker" name="headfyear" id="headfyear">
                            {{-- <option value="{{url('/')}}/monthly_records?headfyear=2018-2019&city={{$month_data[0]['city']}}" @if(isset($month_data[0]) && $month_data[0]['headfyear'] == '2018-2019'){{'selected'}}@endif>2018-2019</option> --}}
                            <option value="{{url('/')}}/monthly_records?headfyear=2019-2020&city={{$month_data[0]['city']}}" @if(isset($month_data[0]) && $month_data[0]['headfyear'] == '2019-2020'){{'selected'}}@endif>2019-2020</option>
                            <option value="{{url('/')}}/monthly_records?headfyear=2020-2021&city={{$month_data[0]['city']}}" @if(isset($month_data[0]) && $month_data[0]['headfyear'] == '2020-2021'){{'selected'}}@endif>2020-2021</option>
                            <option value="{{url('/')}}/monthly_records?headfyear=2021-2022&city={{$month_data[0]['city']}}" @if(isset($month_data[0]) && $month_data[0]['headfyear'] == '2021-2022'){{'selected'}}@endif>2021-2022</option>
                            <option value="{{url('/')}}/monthly_records?headfyear=2022-2023&city={{$month_data[0]['city']}}" @if(isset($month_data[0]) && $month_data[0]['headfyear'] == '2022-2023'){{'selected'}}@endif>2022-2023</option>
                            <option value="{{url('/')}}/monthly_records?headfyear=2023-2024&city={{$month_data[0]['city']}}" @if(isset($month_data[0]) && $month_data[0]['headfyear'] == '2023-2024'){{'selected'}}@endif >2023-2024</option>
                        </select>
                        <select class="select selectpicker" name="headcity" id="headcity">
                            <option value="{{url('/')}}/monthly_records?city=All&headfyear={{$month_data[0]['headfyear']}}" @if(isset($month_data[0]) && $month_data[0]['city'] == 'All'){{'selected'}}@endif>All</option>
                            {{-- <option value="{{url('/')}}/monthly_records?city=Mumbai&headfyear={{$month_data[0]['headfyear']}}" @if(isset($month_data[0]) && $month_data[0]['city'] == 'Mumbai'){{'selected'}}@endif>Mumbai</option>
                            <option value="{{url('/')}}/monthly_records?city=Pune&headfyear={{$month_data[0]['headfyear']}}" @if(isset($month[0]) && $month_data[0]['city'] == 'Pune'){{'selected'}}@endif>Pune</option> --}}
                            @foreach(config('app.citylist') as $key=>$value)
                                <option value="{{url('/')}}/monthly_records?city={{$value}}&headfyear={{$month_data[0]['headfyear']}}" @if(isset($month[0]) && $month_data[0]['city'] == $value){{'selected'}}@endif>{{$value}}</option>
                                {{-- <option value="{{url('/')}}/monthly_records?city=Pune&headfyear={{$month_data[0]['headfyear']}}" @if(isset($month[0]) && $month_data[0]['city'] == 'Pune'){{'selected'}}@endif>Pune</option> --}}
                            @endforeach
                        </select>
                    </h4>
                </div>
                <div class="col-md-4  justify-content-end">
                    <button type="button" class="btn btn-outline-primary btn-sm" id="btn_add_record">Add Record</button>    
                    <a class="btn btn-outline-success btn-sm" 
                            href="{{url('/').'/monthly_records?headfyear='.$month_data[0]['headfyear'].'&city='.$month_data[0]['city'].'&btn_submit=export_excel'}}">Export</a>
                </div>
            </div>
            
        </div>
        <div class="card-body">
            <div class="scrollable_card">
                <table class="table table-stripped">
                    <tbody class="text-right">
                        <tr>
                            <th class="text-left">Month</th>
                            @foreach ($month_data as $key=>$value)
                                <th>{{$value['month']}}</th>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Total Revenue</th>
                            @foreach ($month_data as $key=>$value)
                                <td>{{number_format($value['total_revenue'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Gross Earning total</th>
                            @foreach ($month_data as $key=>$value)
                                <td>{{number_format($value['gross_earning_total'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Total Rent</th>
                            @foreach ($month_data as $key=>$value)
                                <td>{{number_format($value['total_rent'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Total Rent Collected</th>
                            @foreach ($month_data as $key=>$value)
                                <td>{{number_format($value['total_rent_collected'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Due Rent</th>
                            @foreach ($month_data as $key=>$value)
                                <td>{{number_format($value['due_rent'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">% growth over last month</th>
                            @foreach ($month_data as $key=>$value)
                                <td>{{$value['total_growth_over_last_month']}}%</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Total Unit Rented</th>
                            @foreach ($month_data as $key=>$value)
                                <td>{{number_format($value['total_unit_rented'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Total Customer Served(Rental)</th>
                            @foreach ($month_data as $key=>$value)
                            <td>{{number_format($value['total_customer_served_rental'] + $value['total_renewed_customer'])}}</td>
                            @endforeach
                        </tr>
                        {{-- <tr>
                            <th>New class="text-left" Rent Collected</th>
                            @foreach ($month_data as $key=>$value)
                                <td>{{$value['new_rent_collected']}}</td>
                            @endforeach
                        </tr> --}}
                        <tr>
                            <th class="text-left">New Rent Collected(Online)</th>
                            @foreach ($month_data as $key=>$value)
                                <td>{{number_format($value['new_rent_collected_online'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">New Rent Collected(Offline)</th>
                            @foreach ($month_data as $key=>$value)
                                <td>{{number_format($value['new_rent_collected_offline'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">New Rent Collected(Corporate)</th>
                            @foreach ($month_data as $key=>$value)
                                <td>{{number_format($value['new_rent_collected_corporate'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Deposite Collected</th>
                            @foreach ($month_data as $key=>$value)
                                <td>{{number_format($value['new_rent_deposite'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">New Unit Rented</th>
                            @foreach ($month_data as $key=>$value)
                                <td>{{number_format($value['new_unit_rented'])}}</td>
                            @endforeach
                        </tr>
                        {{-- <tr>
                            <th>New class="text-left" Customer(Rental)</th>
                            @foreach ($month_data as $key=>$value)
                                <td>{{$value['total_customer_served_rental']}}</td>
                            @endforeach
                        </tr> --}}
                        <tr>
                            <th class="text-left">New Customer(Rental-Online)</th>
                            @foreach ($month_data as $key=>$value)
                                <td>{{number_format($value['new_customer_rented_online'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">New Customer(Rental-Offline)</th>
                            @foreach ($month_data as $key=>$value)
                                <td>{{number_format($value['new_customer_rented_offline'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">New Customer(Rental-Corporate)</th>
                            @foreach ($month_data as $key=>$value)
                                <td>{{number_format($value['new_customer_rented_corporate'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Customer growth over last month</th>
                            @foreach ($month_data as $key=>$value)
                                <td>{{$value['new_growth_over_last_month']}}%</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Value added service</th>
                            @foreach ($month_data as $key=>$value)
                                <td>{{number_format($value['value_added_service'])}}</td>
                            @endforeach
                        </tr>
                        {{-- <tr>
                            <th class="text-left">Renewal rent collected</th>
                            @foreach ($month_data as $key=>$value)
                                <td>{{number_format($value['renewal_rent_collected'])}}</td>
                            @endforeach
                        </tr> --}}
                        <tr>
                            <th class="text-left">Renewal rent collected(Online)</th>
                            @foreach ($month_data as $key=>$value)
                                <td>{{number_format($value['renewal_rent_collected_online'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Renewal rent collected(Offline)</th>
                            @foreach ($month_data as $key=>$value)
                                <td>{{number_format($value['renewal_rent_collected_offline'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Renewal rent collected(Corporate)</th>
                            @foreach ($month_data as $key=>$value)
                                <td>{{number_format($value['renewal_rent_collected_corporate'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Renewed Customers</th>
                            @foreach ($month_data as $key=>$value)
                                <td>{{number_format($value['total_renewed_customer'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Renewal count of equipment</th>
                            @foreach ($month_data as $key=>$value)
                                <td>{{number_format($value['renewal_count_of_equipment'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Renewal %</th>
                            @foreach ($month_data as $key=>$value)
                            <td>{{$value['renewal_%']}}%</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Earning from vendor equipment</th>
                            @foreach ($month_data as $key=>$value)
                            <td>{{number_format($value['vendor_equipment_renewal'] + $value['vendor_equipment_rent'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Vendor Payment</th>
                            @foreach ($month_data as $key=>$value)
                            <td>{{number_format($value['vendor_payment_no_q5c_rent'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Net Earning from vendor equipment</th>
                            @foreach ($month_data as $key=>$value)
                            <td>{{$value['net_earning_from_vendor_equipment']}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">% of vendor net rental Earning</th>
                            @foreach ($month_data as $key=>$value)
                            <td>{{$value['%_of_vendor_net_rental_earning']}}%</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Net Rental Earning</th>
                            @foreach ($month_data as $key=>$value)
                            <td>{{number_format($value['net_rental_earning'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Gross Net Rental Earning</th>
                            @foreach ($month_data as $key=>$value)
                            <td>{{$value['gross_net_rental_earning']}}%</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Rental Transportation</th>
                            @foreach ($month_data as $key=>$value)
                            <td>{{number_format($value['rental_transportation'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Transportation Expense</th>
                            @foreach ($month_data as $key=>$value)
                            <td>{{number_format($value['transportation_expense'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Total Expense</th>
                            @foreach ($month_data as $key=>$value)
                            <td>{{number_format($value['total_expense'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Google spend Marketing</th>
                            @foreach ($month_data as $key=>$value)
                            <td>{{number_format($value['google_spend_marketing'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">No of clicks</th>
                            @foreach ($month_data as $key=>$value)
                            <td>{{number_format($value['no_of_clicks'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Impression</th>
                            @foreach ($month_data as $key=>$value)
                            <td>{{number_format($value['impressions'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Conversion Ratio</th>
                            @foreach ($month_data as $key=>$value)
                            <td>{{$value['conversion_ratio']}}%</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">New customer acquition cost</th>
                            @foreach ($month_data as $key=>$value)
                            <td>{{number_format($value['new_customer_aquition_cost'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">All customer acquition cost</th>
                            @foreach ($month_data as $key=>$value)
                            <td>{{number_format($value['all_customer_aquition_cost'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Avg rental per customer new</th>
                            @foreach ($month_data as $key=>$value)
                            <td>{{number_format($value['avg_rental_per_customer_new'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Avg rental per customer total</th>
                            @foreach ($month_data as $key=>$value)
                            <td>{{number_format($value['avg_rental_per_customer_rental'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Justdial</th>
                            @foreach ($month_data as $key=>$value)
                            <td>{{number_format($value['justdial'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Offline Marketing</th>
                            @foreach ($month_data as $key=>$value)
                            <td>{{number_format($value['offline_marketing'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Sales Value</th>
                            @foreach ($month_data as $key=>$value)
                            <td>{{number_format($value['sales_value'])}}</td>
                            @endforeach
                        </tr>                    
                        <tr>
                            <th class="text-left">Purchase Value</th>
                            @foreach ($month_data as $key=>$value)
                            <td>{{number_format($value['purchase_value'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Sales Margin%</th>
                            @foreach ($month_data as $key=>$value)
                            <td>{{$value['sales_margin']}}%</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Sales Customer</th>
                            @foreach ($month_data as $key=>$value)
                            <td>{{number_format($value['sales_customer'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Over all acquition cost</th>
                            @foreach ($month_data as $key=>$value)
                            <td>{{number_format($value['over_all_aquition_cost'])}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="text-left">Sales Transport</th>
                            @foreach ($month_data as $key=>$value)
                            <td>{{number_format($value['sales_transport'])}}</td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- modal here add record --}}
    <div class="modal fade" id="modal_add_monthly_record" tabindex="-1" role="dialog" aria-labelledby="add_recorrd" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="add_recorrd">Add Record</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('report-addmonthly-record')}}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="month_year"><Strong>Month Year</Strong></label>
                                    <input type="month" class="form-control" name="month_year" id="month_year" max="{{date('Y-m')}}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="record_city"><Strong>City</Strong></label>

                                    <select class="form-control" name="record_city" id="record_city">
                                        <option value="Mumbai">Mumbai</option>
                                        <option value="Pune">Pune</option>
                                    </select>
                                    
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-md-6">
                                    <label for="justdial"><Strong>Justdial</Strong></label>
                                    <input type="number" class="form-control" name="justdial" id="justdial" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="offline_marketing"><Strong>Offline Marketing</Strong></label>
                                    <input type="number" class="form-control" name="offline_marketing" id="offline_marketing" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="transport_expense"><Strong>Transport Expense</Strong></label>
                                    <input type="number" class="form-control" name="transport_expense" id="transport_expense" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="purchase_value"><Strong>Purchase Value</Strong></label>
                                    <input type="number" class="form-control" name="purchase_value" id="purchase_value" required>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-md-6">
                                    <label for="value_added_service"><Strong>Value Added Service</Strong></label>
                                    <input type="number" class="form-control" name="value_added_service" id="value_added_service" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="total_expense"><Strong>Total Expense</Strong></label>
                                    <input type="number" class="form-control" name="total_expense" id="total_expense" required>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="submit_status" class="btn btn-outline-success">Submit</button>    
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
                
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $("#btn_add_record").on('click',function(){
            $('#modal_add_monthly_record').modal('show');
        });
        $('#headcity').on('change',function(){
            window.location.assign(this.value);
        });
        $('#headfyear').on('change',function(){
            window.location.assign(this.value);
        });
    </script>
@endsection