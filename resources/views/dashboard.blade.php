@extends('header_and_sidebar')
@section('title')
   Dashboard
@endsection
    @section('header')
    {{-- <link rel="stylesheet" type="text/css" href="http://www.shieldui.com/sha#e74a3b/components/latest/css/light/all.min.css" /> --}}
       <style>
           .card {
                box-shadow: 0 0 10px 0 rgba(100, 100, 100, 0.26);
            }
       </style>
    @endsection

    @section('content')
        <br>
        <h4>Dashboard</h4>
        {{-- Leads Reports --}}
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Leads</h5>
                    </div>
                    <div class="col-md-6 text-right">
                        {{-- <label for="leadFilter">Filter: </label>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="date" class="form-control" name="start_date" id="start_date">
                                <input type="date" class="form-control" name="end_date" id="end_date">
                            </div>
                            <div class="col-md-6">
                                <select class="selectpicker border border-dark rounded" name="leadFilter" id="leadFilter">
                                    <option value="Week" @if($lead_filter=='Week'){{'selected'}}@endif>Week</option>
                                    <option value="Month" @if($lead_filter=='Month'){{'selected'}}@endif>Month</option>
                                    <option value="Year" @if($lead_filter=='Year'){{'selected'}}@endif>Year</option>
                                    <option value="All" @if($lead_filter=='All'){{'selected'}}@endif>All</option>
                                </select>
                            </div>
                        </div>--}} 
                        <div class="row">
                            <div class="col-md-12">
                                <span>Filter Value: </span><br>
                                <span id="leadFilterSpan">{{$lead_filter}}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8"></div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-outline-secondary btn-block" id="leadFilterBtn" data-toggle="modal" data-target="#leadFilterModal">Filter<i class="fas fa-regular fa-filter"></i></button>
                            </div>
                        </div>
                        {{-- <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-5">
                                        
                                        <input type="date" class="form-control" name="start_date" id="start_date">
                                    </div>
                                    <div class="col-md-5">
                                        
                                        <input type="date" class="form-control" name="end_date" id="end_date">
                                    </div>
                                    <div class="col-md-2">
                                        
                                        <button type="button" class="btn btn-outline-secondary btn-block" id="leadFilterBtn"><i class="fas fa-regular fa-filter"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card bg-primary">
                            <div class="card-body text-center">
                                <a  class="stretched-link"></a>
                                <p class="card-text" style="color: #fff; font-size:20px">Total Leads</p>
                                <span class="card-text" id="total_leads" style="color: #fff; font-size:20px">{{$total_leads}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning">
                            <div class="card-body text-center">
                                <a  class="stretched-link"></a>
                                <p class="card-text" style="color: #fff; font-size:20px">In Process Leads</p>
                                <p class="card-text" id="inprocess_count" style="color: #fff; font-size:20px">{{$inprocess_count}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger">
                            <div class="card-body text-center">
                                <a  class="stretched-link"></a>
                                <p class="card-text" style="color: #fff; font-size:20px">Closed Leads</p>
                                <p class="card-text" id="closed_count" style="color: #fff; font-size:20px">{{$closed_count}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success">
                            <div class="card-body text-center">
                                <a  class="stretched-link"></a>
                                <p class="card-text" style="color: #fff; font-size:20px">Converted Leads</p>
                                <p class="card-text" id="converted_count" style="color: #fff; font-size:20px">{{$converted_count}}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body text-center">
                                <canvas id="leadsStats" style="width:100%;">
                                </canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body text-center">
                                <div id="leadsPiechart">
        
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <hr>
        {{-- Orders --}}
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Orders</h5>
                    </div>
                    <div class="col-md-6 text-right">
                        <label for="orderFilter">Filter: </label>
                        <select class="selectpicker border border-dark rounded" name="orderFilter" id="orderFilter">
                            <option value="Week" @if($order_filter=='Week'){{'selected'}}@endif>Week</option>
                            <option value="Month" @if($order_filter=='Month'){{'selected'}}@endif>Month</option>
                            <option value="Year" @if($order_filter=='Year'){{'selected'}}@endif>Year</option>
                            <option value="All" @if($order_filter=='All'){{'selected'}}@endif>All</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card bg-primary">
                            <div class="card-body text-center">
                                <a  class="stretched-link"></a>
                                <p class="card-text" style="color: #fff; font-size:20px">Total Orders</p>
                                <span class="card-text" id="total_orders" style="color: #fff; font-size:20px">{{$total_orders}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-danger">
                            <div class="card-body text-center">
                                <a  class="stretched-link"></a>
                                <p class="card-text" style="color: #fff; font-size:20px">Incomplete Orders</p>
                                <p class="card-text" id="incomplete_orders" style="color: #fff; font-size:20px">{{$incompleted_total}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success">
                            <div class="card-body text-center">
                                <a  class="stretched-link"></a>
                                <p class="card-text" style="color: #fff; font-size:20px">Completed Orders</p>
                                <p class="card-text" id="completed_orders" style="color: #fff; font-size:20px">{{$completed_total}}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body text-center">
                                <canvas id="ordersStats" style="width:100%;">
                                </canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body text-center">
                                <div id="ordersPiechart">
        
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        
        
        @if(session('role') == 'superuser')
        <div class="card card-body">
            <div class="row text-right form-group">
                <div class="col-md-8">

                </div>
                <div class="col-md-4 ">
                    <select class="select selectpicker" name="select_avg_city" id="select_avg_city">
                        <option value="All" @if(request()->get('select_avg_city') == "All"){{"selected"}}@endif>All</option>
                        <option value="Pune" @if(request()->get('select_avg_city') == "Pune"){{"selected"}}@endif>Pune</option>
                        <option value="Mumbai" @if(request()->get('select_avg_city') == "Mumbai"){{"selected"}}@endif>Mumbai</option>
                    </select>
                </div>
            </div>
            <div class="table table-responsive jim-table-responsive">
                <table class="table font-weight-bold">
                    <thead>
                        <tr>
                            <th>Avg Per day</th>
                            <th>YTD</th>
                            <th>MTD</th>
                            <th>% Change</th>
                            <th>Today</th>
                            <th>% Change</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Rental Total</td>
                            <td class="text-primary">{{round($ytd_rental_total)}}</td>
                            <td>{{round($mtd_rental_total)}}</td>
                            <td class="@if(round($ytd_rental_total)!=0)@if(round((round($mtd_rental_total)-round($ytd_rental_total))*100/round($ytd_rental_total))<=0){{'text-danger'}}@else{{'text-success'}}@endif @else{{'text-danger'}} @endif">@if($ytd_rental_total !=0){{round((round($mtd_rental_total)-round($ytd_rental_total))*100/round($ytd_rental_total))}}@else{{0}}@endif</td>
                            <td>{{round($td_rental_total)}}</td>
                            <td class="@if(round($ytd_rental_total)!=0)@if(round((round($td_rental_total)-round($ytd_rental_total))*100/round($ytd_rental_total))<=0){{'text-danger'}}@else{{'text-success'}}@endif @else{{'text-danger'}} @endif">@if($ytd_rental_total !=0){{round((round($td_rental_total)-round($ytd_rental_total))*100/round($ytd_rental_total))}}@else{{0}}@endif</td>
                        </tr>
                        <tr>
                            <td>Rental New</td>
                            <td class="text-primary">{{round($ytd_rental_total_new)}}</td>
                            <td>{{round($mtd_rental_total_new)}}</td>
                            <td class="@if(round($ytd_rental_total_new)!=0)@if(round((round($mtd_rental_total_new)-round($ytd_rental_total_new))*100/round($ytd_rental_total_new))<=0){{'text-danger'}}@else{{'text-success'}}@endif @else{{'text-danger'}} @endif">@if($ytd_rental_total_new !=0){{round((round($mtd_rental_total_new)-round($ytd_rental_total_new))*100/round($ytd_rental_total_new))}}@else{{0}}@endif</td>
                            <td>{{round($td_rental_total_new)}}</td>
                            <td class="@if(round($ytd_rental_total_new)!=0)@if(round((round($td_rental_total_new)-round($ytd_rental_total_new))*100/round($ytd_rental_total_new))<=0){{'text-danger'}}@else{{'text-success'}}@endif @else{{'text-danger'}} @endif">@if($ytd_rental_total_new !=0){{round((round($td_rental_total_new)-round($ytd_rental_total_new))*100/round($ytd_rental_total_new))}}@else{{0}}@endif</td>
                        </tr>
                        <tr>
                            <td>Rental Renew</td>
                            <td class="text-primary">{{round($ytd_rental_total_renew)}}</td>
                            <td>{{round($mtd_rental_total_renew)}}</td>
                            <td class="@if(round($ytd_rental_total_renew)!=0)@if(round((round($mtd_rental_total_renew)-round($ytd_rental_total_renew))*100/round($ytd_rental_total_renew))<=0){{'text-danger'}}@else{{'text-success'}}@endif @else{{'text-danger'}} @endif">@if($ytd_rental_total_renew !=0){{round((round($mtd_rental_total_renew)-round($ytd_rental_total_renew))*100/round($ytd_rental_total_renew))}}@else{{0}}@endif</td>
                            <td>{{round($td_rental_total_renew)}}</td>
                            <td class="@if(round($ytd_rental_total_renew)!=0)@if(round((round($td_rental_total_renew)-round($ytd_rental_total_renew))*100/round($ytd_rental_total_renew))<=0){{'text-danger'}}@else{{'text-success'}}@endif @else{{'text-danger'}} @endif">@if($ytd_rental_total_renew !=0){{round((round($td_rental_total_renew)-round($ytd_rental_total_renew))*100/round($ytd_rental_total_renew))}}@else{{0}}@endif</td>
                        </tr>
                        <tr>
                            <td>Sale Total</td>
                            <td class="text-primary">{{round($ytd_sale_total)}}</td>
                            <td>{{round($mtd_sale_total)}}</td>
                            <td class="@if(round((round($mtd_sale_total)-round($ytd_sale_total))*100/round($ytd_sale_total))<=0){{'text-danger'}}@else{{'text-success'}}@endif">{{round((round($mtd_sale_total)-round($ytd_sale_total))*100/round($ytd_sale_total))}}</td>
                            <td>{{round($td_sale_total)}}</td>
                            <td class="@if(round((round($td_sale_total)-round($ytd_sale_total))*100/round($ytd_sale_total))<=0){{'text-danger'}}@else{{'text-success'}}@endif">{{round((round($td_sale_total)-round($ytd_sale_total))*100/round($ytd_sale_total))}}</td>
                        </tr>
                        <tr>
                            <td>Transport Total</td>
                            <td class="text-primary">{{round($ytd_trans_collected)}}</td>
                            <td>{{round($mtd_trans_collected)}}</td>
                            <td class="@if(round((round($mtd_trans_collected)-round($ytd_trans_collected))*100/round($ytd_trans_collected))<=0){{'text-danger'}}@else{{'text-success'}}@endif">{{round((round($mtd_trans_collected)-round($ytd_trans_collected))*100/round($ytd_trans_collected))}}</td>
                            <td>{{round($td_trans_collected)}}</td>
                            <td class="@if(round((round($td_trans_collected)-round($ytd_trans_collected))*100/round($ytd_trans_collected))<=0){{'text-danger'}}@else{{'text-success'}}@endif">{{round((round($td_trans_collected)-round($ytd_trans_collected))*100/round($ytd_trans_collected))}}</td>
                        </tr>
                        <tr>
                            <td>Total no of equipment</td>
                            <td class="text-primary">{{round($ytd_total_no_of_equip)}}</td>
                            <td>{{round($mtd_total_no_of_equip)}}</td>
                            <td class="@if(round((round($mtd_total_no_of_equip)-round($ytd_total_no_of_equip))*100/round($ytd_total_no_of_equip))<=0){{'text-danger'}}@else{{'text-success'}}@endif">{{round((round($mtd_total_no_of_equip)-round($ytd_total_no_of_equip))*100/round($ytd_total_no_of_equip))}}</td>
                            <td>{{round($td_total_no_of_equip)}}</td>
                            <td class="@if(round((round($td_total_no_of_equip)-round($ytd_total_no_of_equip))*100/round($ytd_total_no_of_equip))<=0){{'text-danger'}}@else{{'text-success'}}@endif">{{round((round($td_total_no_of_equip)-round($ytd_total_no_of_equip))*100/round($ytd_total_no_of_equip))}}</td>
                        </tr>
                        <tr>
                            <td>Rental Customer Count</td>
                            <td class="text-primary">{{round($ytd_rental_cust_count)}}</td>
                            <td>{{round($mtd_rental_cust_count)}}</td>
                            <td class="@if(round($ytd_rental_cust_count)!=0)@if(round((round($mtd_rental_cust_count)-round($ytd_rental_cust_count))*100/round($ytd_rental_cust_count))<=0){{'text-danger'}}@else{{'text-success'}}@endif @else{{'text-danger'}}@endif">@if(round($ytd_rental_cust_count)!=0){{round((round($mtd_rental_cust_count)-round($ytd_rental_cust_count))*100/round($ytd_rental_cust_count))}}@else{{0}}@endif</td>
                            <td>{{round($td_rental_cust_count)}}</td>
                            <td class="@if(round($ytd_rental_cust_count)!=0)@if(round((round($td_rental_cust_count)-round($ytd_rental_cust_count))*100/round($ytd_rental_cust_count))<=0){{'text-danger'}}@else{{'text-success'}}@endif @else{{'text-danger'}}@endif">@if(round($ytd_rental_cust_count)!=0){{round((round($td_rental_cust_count)-round($ytd_rental_cust_count))*100/round($ytd_rental_cust_count))}}@else{{0}}@endif</td>
                        </tr>
                        <tr>
                            <td>Sale Customer Count</td>
                            <td class="text-primary">{{round($ytd_sale_cust_count)}}</td>
                            <td>{{round($mtd_sale_cust_count)}}</td>
                            <td class="@if(round($ytd_sale_cust_count)!=0)@if(round((round($mtd_sale_cust_count)-round($ytd_sale_cust_count))*100/round($ytd_sale_cust_count))<=0){{'text-danger'}}@else{{'text-success'}}@endif @else{{'text-danger'}}@endif">@if(round($ytd_sale_cust_count)!=0){{round((round($mtd_sale_cust_count)-round($ytd_sale_cust_count))/round($ytd_sale_cust_count)*100)}}@else{{0}}@endif</td>
                            <td>{{round($td_sale_cust_count)}}</td>
                            <td class="@if(round($ytd_sale_cust_count)!=0)@if(round((round($td_sale_cust_count)-round($ytd_sale_cust_count))*100/round($ytd_sale_cust_count))<=0){{'text-danger'}}@else{{'text-success'}}@endif @else{{'text-danger'}}@endif">@if(round($ytd_sale_cust_count)!=0){{round((round($td_sale_cust_count)-round($ytd_sale_cust_count))/round($ytd_sale_cust_count)*100)}}@else{{0}}@endif</td>
                        </tr>
                        <tr>
                            <td>Stop Requested</td>
                            <td class="text-primary">{{round($ytd_stop_req)}}</td>
                            <td>{{round($mtd_stop_req)}}</td>
                            <td class="@if(round($ytd_stop_req)!=0)@if(round((round($mtd_stop_req)-round($ytd_stop_req))*100/round($ytd_stop_req))<=0){{'text-danger'}}@else{{'text-success'}}@endif @else{{'text-danger'}}@endif">@if(round($ytd_stop_req)!=0){{round((round($mtd_stop_req)-round($ytd_stop_req))*100/round($ytd_stop_req))}}@else{{0}}@endif</td>
                            <td>{{round($td_stop_req)}}</td>
                            <td class="@if(round($ytd_stop_req)!=0)@if(round((round($td_stop_req)-round($ytd_stop_req))*100/round($ytd_stop_req))<=0){{'text-danger'}}@else{{'text-success'}}@endif @else{{'text-danger'}}@endif">@if(round($ytd_stop_req)!=0){{round((round($td_stop_req)-round($ytd_stop_req))*100/round($ytd_stop_req))}}@else{{0}}@endif</td>
                        </tr>
                        <tr>
                            <td>New Rent Order</td>
                            <td class="text-primary">{{round($ytd_new_rent)}}</td>
                            <td>{{round($mtd_new_rent)}}</td>
                            <td class="@if(round($ytd_new_rent)!=0)@if(round((round($mtd_new_rent)-round($ytd_new_rent))*100/round($ytd_new_rent))<=0){{'text-danger'}}@else{{'text-success'}}@endif @else{{'text-danger'}}@endif">@if(round($ytd_new_rent)!=0){{round(round(($mtd_new_rent)-round($ytd_new_rent))*100/round($ytd_new_rent))}}@else{{0}}@endif</td>
                            <td>{{round($td_new_rent)}}</td>
                            <td class="@if(round($ytd_new_rent)!=0)@if(round((round($td_new_rent)-round($ytd_new_rent))*100/round($ytd_new_rent))<=0){{'text-danger'}}@else{{'text-success'}}@endif @else{{'text-danger'}}@endif">@if(round($ytd_new_rent)!=0){{round((round($td_new_rent)-round($ytd_new_rent))*100/round($ytd_new_rent))}}@else{{0}}@endif</td>
                        </tr>
                        <tr>
                            <td>New Rent Equipment</td>
                            <td class="text-primary">{{round($ytd_new_equip)}}</td>
                            <td>{{round($mtd_new_equip)}}</td>
                            <td class="@if(round($ytd_new_equip)!=0)@if(round((round($mtd_new_equip)-round($ytd_new_equip))*100/round($ytd_new_equip))<=0){{'text-danger'}}@else{{'text-success'}}@endif @else{{'text-danger'}}@endif">@if(round($ytd_new_equip)!=0){{round(round(($mtd_new_equip)-round($ytd_new_equip))*100/round($ytd_new_equip))}}@else{{0}}@endif</td>
                            <td>{{round($td_new_equip)}}</td>
                            <td class="@if(round($ytd_new_equip)!=0)@if(round((round($td_new_equip)-round($ytd_new_equip))*100/round($ytd_new_equip))<=0){{'text-danger'}}@else{{'text-success'}}@endif @else{{'text-danger'}}@endif">@if(round($ytd_new_equip)!=0){{round((round($td_new_equip)-round($ytd_new_equip))*100/round($ytd_new_equip))}}@else{{0}}@endif</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
            {{--Vendor Equipment Reports --}}
            <h5>Vendor Equipments</h5>
            <div class="row">
                <div class="col-md-4">
                    <div class="card bg-primary">
                        <div class="card-body text-center">
                            <a  class="stretched-link"></a>
                            <p class="card-text" style="color: #fff; font-size:20px">Total Equipments</p>
                            <span class="card-text" style="color: #fff; font-size:20px">{{$vdr_total_equip}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning">
                        <div class="card-body text-center">
                            <a  class="stretched-link"></a>
                            <p class="card-text" style="color: #fff; font-size:20px">Rented Equipments</p>
                            <p class="card-text" style="color: #fff; font-size:20px">{{$vdr_rented_equip}}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success">
                        <div class="card-body text-center">
                            <a  class="stretched-link"></a>
                            <p class="card-text" style="color: #fff; font-size:20px">Available Equipments</p>
                            <p class="card-text" style="color: #fff; font-size:20px">{{$vdr_available_equip}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body text-right">
                            <div class="row">                            
                                <div class="col-md-12" style="font-size: 0.8rem;">
                                    <label for="vdrRentedEquip">Filter: </label>
                                    <select class="selectpicker border border-dark rounded" name="vdrRentedEquip" id="vdrRentedEquip">
                                        <option value="Week" @if($order_filter=='Week'){{'selected'}}@endif>Week</option>
                                        <option value="Month" @if($order_filter=='Month'){{'selected'}}@endif>Month</option>
                                        <option value="Year" @if($order_filter=='Year'){{'selected'}}@endif>Year</option>
                                        <option value="All" @if($order_filter=='All'){{'selected'}}@endif>All</option>
                                    </select>
                                </div>
                            </div>
                            <canvas id="vdrEquipmentsStats" style="width:100%;">
                            </canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <div id="vdrEquipmentsPiechart">

                            </div>
                        </div>
                    </div>
                </div>
            </div> 
            <hr>
            {{-- Q5C Equipment Reports --}}
            <h5>Q5C Equipments</h5>
            <div class="row">
                <div class="col-md-4">
                    <div class="card bg-primary">
                        <div class="card-body text-center">
                            <a  class="stretched-link"></a>
                            <p class="card-text" style="color: #fff; font-size:20px">Total Equipments</p>
                            <span class="card-text" style="color: #fff; font-size:20px">{{$total_equip}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning">
                        <div class="card-body text-center">
                            <a  class="stretched-link"></a>
                            <p class="card-text" style="color: #fff; font-size:20px">Rented Equipments</p>
                            <p class="card-text" style="color: #fff; font-size:20px">{{$rented_equip}}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success">
                        <div class="card-body text-center">
                            <a  class="stretched-link"></a>
                            <p class="card-text" style="color: #fff; font-size:20px">Available Equipments</p>
                            <p class="card-text" style="color: #fff; font-size:20px">{{$available_equip}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <canvas id="q5cEquipmentsStats" style="width:100%;">
                            </canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <div id="q5cEquipmentsPiechart">

                            </div>
                        </div>
                    </div>
                </div>
            </div> 
            <hr>
        @endif
        {{-- Vendor Reports --}}
        {{-- <h5>Vendor</h5>
        <div class="row">
            <div class="col-md-3">
                <div class="card bg-primary">
                    <div class="card-body text-center">
                        <a  class="stretched-link"></a>
                        <p class="card-text" style="color: #fff; font-size:20px">Registe#e74a3b Vendors</p>
                        <span class="card-text" style="color: #fff; font-size:20px">50</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning">
                    <div class="card-body text-center">
                        <a  class="stretched-link"></a>
                        <p class="card-text" style="color: #fff; font-size:20px">Approved Vendors</p>
                        <p class="card-text" style="color: #fff; font-size:20px">30</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger">
                    <div class="card-body text-center">
                        <a  class="stretched-link"></a>
                        <p class="card-text" style="color: #fff; font-size:20px">Awaiting Vendors</p>
                        <p class="card-text" style="color: #fff; font-size:20px">20</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success">
                    <div class="card-body text-center">
                        <a  class="stretched-link"></a>
                        <p class="card-text" style="color: #fff; font-size:20px">Rejected Vendors</p>
                        <p class="card-text" style="color: #fff; font-size:20px">20</p>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <canvas id="vendorsStats" style="width:100%;">
                        </canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <div id="vendorsPiechart">

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br> --}}
        
    <!-- Modal -->
    <div class="modal fade" id="leadFilterModal" tabindex="-1" role="dialog" aria-labelledby="leadFilterModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="leadFilterModalLabel">Lead Filter</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row justify-text-center">
                        <div class="col-md-12">
                            <center>
                                <select class="selectpicker border rounded" title="Quick Filter" name="leadFilter" id="leadFilter">
                                    <option value="Week" @if($lead_filter=='Week'){{'selected'}}@endif>Current Week</option>
                                    <option value="Month" @if($lead_filter=='Month'){{'selected'}}@endif>Current Month</option>
                                    <option value="Year" @if($lead_filter=='Year'){{'selected'}}@endif>Current Year</option>
                                    <option value="DateSearch" @if($lead_filter=='DateSearch'){{'selected'}}@endif>Custom Date Search</option>
                                    <option value="All" @if($lead_filter=='All'){{'selected'}}@endif>All</option>
                                </select>
                            </center>
                        </div>
                    </div>
                    <div class="date-search" style="display: none;">
                        {{-- <center>Or</center> --}}
                        {{-- <br> --}}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="start_date">From Date</label>
                                        <input type="date" class="form-control" name="start_date" id="start_date">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="end_date">To Date</label>
                                        <input type="date" class="form-control" name="end_date" id="end_date">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <center>And</center>
                    <div class="row">
                        <div class="col-md-12">
                            <center>
                                <label for="leadOwner">Lead Owner</label><br>
                                <select class="selectpicker border rounded" title="Lead Owner" name="leadOwner" id="leadOwner">
                                    @foreach($lead_owner as $key=>$value)
                                        <option value="{{$value->user_id}}">{{$value->lead_owner}}</option>
                                    @endforeach
                                    <option value="All" selected>All</option>
                                </select>
                            </center>
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="leadFilterSubmit">Search</button>                
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endsection
    @section('script')
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
        {{-- <script type="text/javascript" src="http://www.shieldui.com/sha#e74a3b/components/latest/js/shieldui-all.min.js"></script> --}}
        <script>
            $("#leadFilterSubmit").click(function(){
                let filter = $("#leadFilter").val();
                let start_date = $("#start_date").val();
                let end_date = $("#end_date").val();
                if(start_date == "" || filter != "DateSearch")
                {
                    start_date = 'start';
                }
                if(end_date == "" || filter != "DateSearch")
                {
                    end_date = 'end';
                }
                let lead_owner = $("#leadOwner").val();
                $.ajax({
                    type: "GET",
                    url: "{{url('/')}}/lead_count/"+filter+"/"+start_date+"/"+end_date+"/"+lead_owner+"/stateReal",
                    // cache:false,
                    success: function (details)
                    {
                        $('#leadFilterModal').modal('hide')
                        // console.log(details);
                        
                        if(filter == "DateSearch")
                        {
                            
                            $("#leadFilterSpan").text(start_date+' To '+end_date);
                        }
                        else
                        {
                            $("#leadFilterSpan").text(filter);
                        }
                        $('#total_leads').text(details.total_leads);
                        $('#inprocess_count').text(details.inprocess_count);
                        $('#closed_count').text(details.closed_count);
                        $('#converted_count').text(details.converted_count);
                        google.charts.load('current', {'packages':['corechart']});
                        google.charts.setOnLoadCallback(drawChartLeads);
                        //********** Leads ************//
                        // Load google charts
                        // google.charts.load('current', {'packages':['corechart']});
                        // google.charts.setOnLoadCallback(drawChart);

                        // Draw the chart and set the chart values
                        function drawChartLeads() {
                        var data = google.visualization.arrayToDataTable([
                        ['Task', 'Leads'],
                        ['In Process('+details.inprocess_count+')', details.inprocess_count],
                        ['Converted('+details.converted_count+')', details.converted_count],
                        ['Closed('+details.closed_count+')', details.closed_count]
                        ]);

                        // Optional; add a title and set the width and height of the chart
                        var options = {
                            'title':'Leads('+details.total_leads+')',
                            'colors': ['#f6c23e','#1cc88a','#e74a3b'],
                            is3D: true};

                        // Display the chart inside the <div> element with id="piechart"
                        var chart = new google.visualization.PieChart(document.getElementById('leadsPiechart'));
                        chart.draw(data, options);
                        // chart.draw(data);
                        }



                        // Statistics Chart or graph code....
                        var xValues = details.period_leads;
                        // alert(xValues);

                        new Chart("leadsStats", {
                        type: "line",
                        data: {
                            labels: xValues,
                            datasets: [{ 
                            label: "Closed",
                            data: details.closed_lead,
                            borderColor: "#e74a3b",
                            fill: false
                            }, { 
                            label: "Converted",
                            data: details.converted_lead,
                            borderColor: "#1cc88a",
                            fill: false
                            }, { 
                            label: "In Process",
                            data: details.in_process_lead,
                            borderColor: "yellow",
                            fill: false
                            }]
                        },
                        options: {
                            title: {
                            display: true,
                            text: 'Leads'
                            },
                            tooltips: {
                            mode: 'label',
                            },
                            hover: {
                            mode: 'nearest',
                            intersect: true
                            },
                            scales: {
                            xAxes: [{
                                display: true,
                                // ticks: {
                                // userCallback: function(label, index, labels) {
                                //     if(typeof label === "string")
                                // {
                                //     return label.substring(0,1)
                                // }
                                //     return label
                                // },
                                // },
                                scaleLabel: {
                                display: true,
                                labelString: details.xAxis
                                }
                            }],
                            yAxes: [{
                                display: true,
                                scaleLabel: {
                                display: true,
                                labelString: 'Count'
                                }
                            }]
                            }
                            // legend: {display: false}
                        }
                        // options: {
                        //     legend: {display: false}
                        // }
                        });
                    }
                });
            });
            // $('#leadFilter').change(function(){

            // });
            //********** Orders ************//
            $('#orderFilter').on('change', function(){
                var filter = this.value;
                $.ajax({
                    type: "GET",
                    url: "{{url('/')}}/order_count/"+filter,
                    // cache:false,
                    success: function (details)
                    {
                        // console.log(details);
                        // console.log(details.incomplete);
                        
                        $('#total_orders').text(details.total_orders);
                        $('#incomplete_orders').text(details.incompleted_total);
                        $('#completed_orders').text(details.completed_total);
                        google.charts.load('current', {'packages':['corechart']});
                        google.charts.setOnLoadCallback(drawChartOrders);

                        // Draw the chart and set the chart values
                        function drawChartOrders() {
                        var data = google.visualization.arrayToDataTable([
                        ['Task', 'Orders'],
                        ['Completed('+details.completed_total+')', details.completed_total],
                        ['Incomplete('+details.incompleted_total+')', details.incompleted_total]
                        ]);

                        // Optional; add a title and set the width and height of the chart
                        var options = {
                            'title':'Orders('+details.total_orders+')',
                            'colors': ['#1cc88a', '#e74a3b'],
                            'is3D': true};

                        // Display the chart inside the <div> element with id="piechart"
                        var chart = new google.visualization.PieChart(document.getElementById('ordersPiechart'));
                        chart.draw(data, options);
                        // chart.draw(data);
                        }



                        // Statistics Chart or graph code....
                        
                        var xValues = details.period_orders;
                        console.log(xValues);

                        new Chart("ordersStats", {
                        type: "line",
                        data: {
                            labels: xValues,
                            datasets: [{ 
                            label: "Incomplete",
                            data: details.incomplete,
                            borderColor: "#e74a3b",
                            fill: false
                            }, { 
                            label: "Completed",
                            data: details.completed,
                            borderColor: "#1cc88a",
                            fill: false
                            }]
                        },
                        options: {
                            title: {
                            display: true,
                            text: 'Orders'
                            },
                            tooltips: {
                            mode: 'label',
                            },
                            hover: {
                            mode: 'nearest',
                            intersect: true
                            },
                            scales: {
                            xAxes: [{
                                display: true,
                                // ticks: {
                                // userCallback: function(label, index, labels) {
                                //     if(typeof label === "string")
                                // {
                                //     return label.substring(0,1)
                                // }
                                //     return label
                                // },
                                // },
                                scaleLabel: {
                                display: true,
                                labelString: details.xAxis
                                }
                            }],
                            yAxes: [{
                                display: true,
                                scaleLabel: {
                                display: true,
                                labelString: 'Count'
                                }
                            }]
                            }
                            // legend: {display: false}
                        }
                        // options: {
                        //     legend: {display: false}
                        // }
                        });            
                    }
                });
            });
            $('#leadFilter').on('change', function(){
                var filter = this.value;
                if(filter != "DateSearch")
                {
                    // $("#leadFilterSpan").text(filter);
                    $('.date-search').hide();
                    // $('#leadFilterModal').modal('hide')
                }
                else if(filter == "DateSearch")
                {                    
                    // $("#leadFilterSpan").text(filter);
                    $('.date-search').show();
                }
            });
            $('#leadFilter1').on('change', function(){
                var filter = this.value;
                if(filter != "DateSearch")
                {
                    // $.ajax({
                    //     type: "GET",
                    //     url: "{{url('/')}}/lead_count/"+filter,
                    //     // cache:false,
                    //     success: function (details)
                    //     {
                    //         console.log(details);
                    //         $('#total_leads').text(details.total_leads);
                    //         $('#inprocess_count').text(details.inprocess_count);
                    //         $('#closed_count').text(details.closed_count);
                    //         $('#converted_count').text(details.converted_count);
                    //         google.charts.load('current', {'packages':['corechart']});
                    //         google.charts.setOnLoadCallback(drawChartLeads);
                    //         //********** Leads ************//
                    //         // Load google charts
                    //         // google.charts.load('current', {'packages':['corechart']});
                    //         // google.charts.setOnLoadCallback(drawChart);

                    //         // Draw the chart and set the chart values
                    //         function drawChartLeads() {
                    //         var data = google.visualization.arrayToDataTable([
                    //         ['Task', 'Leads'],
                    //         ['In Process('+details.inprocess_count+')', details.inprocess_count],
                    //         ['Converted('+details.converted_count+')', details.converted_count],
                    //         ['Closed('+details.closed_count+')', details.closed_count]
                    //         ]);

                    //         // Optional; add a title and set the width and height of the chart
                    //         var options = {
                    //             'title':'Leads('+details.total_leads+')',
                    //             'colors': ['#f6c23e','#1cc88a','#e74a3b'],
                    //             is3D: true};

                    //         // Display the chart inside the <div> element with id="piechart"
                    //         var chart = new google.visualization.PieChart(document.getElementById('leadsPiechart'));
                    //         chart.draw(data, options);
                    //         // chart.draw(data);
                    //         }



                    //         // Statistics Chart or graph code....
                    //         var xValues = details.period_leads;
                    //         // alert(xValues);

                    //         new Chart("leadsStats", {
                    //         type: "line",
                    //         data: {
                    //             labels: xValues,
                    //             datasets: [{ 
                    //             label: "Closed",
                    //             data: details.closed_lead,
                    //             borderColor: "#e74a3b",
                    //             fill: false
                    //             }, { 
                    //             label: "Converted",
                    //             data: details.converted_lead,
                    //             borderColor: "#1cc88a",
                    //             fill: false
                    //             }, { 
                    //             label: "In Process",
                    //             data: details.in_process_lead,
                    //             borderColor: "yellow",
                    //             fill: false
                    //             }]
                    //         },
                    //         options: {
                    //             title: {
                    //             display: true,
                    //             text: 'Leads'
                    //             },
                    //             tooltips: {
                    //             mode: 'label',
                    //             },
                    //             hover: {
                    //             mode: 'nearest',
                    //             intersect: true
                    //             },
                    //             scales: {
                    //             xAxes: [{
                    //                 display: true,
                    //                 // ticks: {
                    //                 // userCallback: function(label, index, labels) {
                    //                 //     if(typeof label === "string")
                    //                 // {
                    //                 //     return label.substring(0,1)
                    //                 // }
                    //                 //     return label
                    //                 // },
                    //                 // },
                    //                 scaleLabel: {
                    //                 display: true,
                    //                 labelString: details.xAxis
                    //                 }
                    //             }],
                    //             yAxes: [{
                    //                 display: true,
                    //                 scaleLabel: {
                    //                 display: true,
                    //                 labelString: 'Count'
                    //                 }
                    //             }]
                    //             }
                    //             // legend: {display: false}
                    //         }
                    //         // options: {
                    //         //     legend: {display: false}
                    //         // }
                    //         });
                    //     }
                    // });
                    $("#leadFilterSpan").text(filter);
                    $('.date-search').hide();
                    $('#leadFilterModal').modal('hide')
                }
                else if(filter == "DateSearch")
                {                    
                    $("#leadFilterSpan").text(filter);
                    $('.date-search').show();
                }
            });
            $('#vdrRentedEquip').on('change', function(){
                var filter = this.value;
                $.ajax({
                    type: "GET",
                    url: "{{url('/')}}/vdr_equipment_count/"+filter,
                    // cache:false,
                    success: function (details)
                    {
                        
                        // Statistics Chart or graph code....
                        var xValues = details.period_vdr_equip;
                        // alert(xValues);

                        new Chart("vdrEquipmentsStats", {
                        type: "line",
                        data: {
                            labels: xValues,
                            datasets: [{ 
                            label: "Rented",
                            data: details.rented_arr,
                            borderColor: "#1cc88a",
                            fill: false
                            }]
                        },
                        options: {
                            title: {
                            display: true,
                            text: 'Rented Products'
                            },
                            tooltips: {
                            mode: 'label',
                            },
                            hover: {
                            mode: 'nearest',
                            intersect: true
                            },
                            scales: {
                            xAxes: [{
                                display: true,
                                // ticks: {
                                // userCallback: function(label, index, labels) {
                                //     if(typeof label === "string")
                                // {
                                //     return label.substring(0,1)
                                // }
                                //     return label
                                // },
                                // },
                                scaleLabel: {
                                display: true,
                                labelString: details.xAxis
                                }
                            }],
                            yAxes: [{
                                display: true,
                                scaleLabel: {
                                display: true,
                                labelString: 'Count'
                                }
                            }]
                            }
                            // legend: {display: false}
                        }
                        // options: {
                        //     legend: {display: false}
                        // }
                        });
                    }
                });
            });
            // Load google charts
            $(document).ready(function() {
                google.charts.load('current', {'packages':['corechart']});
                google.charts.setOnLoadCallback(drawChartOrders);
                google.charts.setOnLoadCallback(drawChartLeads);
                google.charts.setOnLoadCallback(drawChartVdrEquipments);
                google.charts.setOnLoadCallback(drawChartQ5cEquipments);
                // google.charts.setOnLoadCallback(drawChartVendors);

                // Draw the chart and set the chart values
                function drawChartOrders() {
                var data = google.visualization.arrayToDataTable([
                ['Task', 'Orders'],
                ['Completed('+{{$completed_total}}+')', {{$completed_total}}],
                ['Incomplete('+{{$incompleted_total}}+')', {{$incompleted_total}}]
                ]);

                // Optional; add a title and set the width and height of the chart
                var options = {
                            'title':'Orders('+{{$total_orders}}+')',
                            'colors': ['#1cc88a', '#e74a3b'],
                            'is3D': true};

                // Display the chart inside the <div> element with id="piechart"
                var chart = new google.visualization.PieChart(document.getElementById('ordersPiechart'));
                chart.draw(data, options);
                // chart.draw(data);
            }



                // Statistics Chart or graph code....
                
                var xValues = [{{implode(",",$period_orders)}}];

                new Chart("ordersStats", {
                type: "line",
                data: {
                    labels: xValues,
                    datasets: [{ 
                    label: "Incomplete",
                    data: [{{implode(",",$incomplete)}}],
                    borderColor: "#e74a3b",
                    fill: false
                    }, { 
                    label: "Completed",
                    data: [{{implode(",",$completed)}}],
                    borderColor: "#1cc88a",
                    fill: false
                    }]
                },
                options: {
                    title: {
                    display: true,
                    text: 'Orders'
                    },
                    tooltips: {
                    mode: 'label',
                    },
                    hover: {
                    mode: 'nearest',
                    intersect: true
                    },
                    scales: {
                    xAxes: [{
                        display: true,
                        // ticks: {
                        // userCallback: function(label, index, labels) {
                        //     if(typeof label === "string")
                        // {
                        //     return label.substring(0,1)
                        // }
                        //     return label
                        // },
                        // },
                        scaleLabel: {
                        display: true,
                        labelString: '{{$xAxis}}'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                        display: true,
                        labelString: 'Count'
                        }
                    }]
                    }
                    // legend: {display: false}
                }
                });


                //********** Leads ************//
                // Load google charts
                // google.charts.load('current', {'packages':['corechart']});
                // google.charts.setOnLoadCallback(drawChart);

                // Draw the chart and set the chart values
                function drawChartLeads() {
                var data = google.visualization.arrayToDataTable([
                ['Task', 'Leads'],
                ['In Process('+{{$inprocess_count}}+')', {{$inprocess_count}}],
                ['Converted('+{{$converted_count}}+')', {{$converted_count}}],
                ['Closed('+{{$closed_count}}+')', {{$closed_count}}]
                ]);

                // Optional; add a title and set the width and height of the chart
                var options = {
                    'title':'Leads('+{{$total_leads}}+')',
                    'colors': ['#f6c23e','#1cc88a','#e74a3b'],
                    is3D: true};

                // Display the chart inside the <div> element with id="piechart"
                var chart = new google.visualization.PieChart(document.getElementById('leadsPiechart'));
                chart.draw(data, options);
                // chart.draw(data);
                }



                // Statistics Chart or graph code....
                var xValues = [{{implode(",",$period_leads)}}];

                new Chart("leadsStats", {
                type: "line",
                data: {
                    labels: xValues,
                    datasets: [{ 
                    label: "Closed",
                    data: [{{implode(",",$closed_lead)}}],
                    borderColor: "#e74a3b",
                    fill: false
                    }, { 
                    label: "Converted",
                    data: [{{implode(",",$converted_lead)}}],
                    borderColor: "#1cc88a",
                    fill: false
                    }, { 
                    label: "In Process",
                    data: [{{implode(",",$in_process_lead)}}],
                    borderColor: "yellow",
                    fill: false
                    }]
                },
                options: {
                    title: {
                    display: true,
                    text: 'Leads'
                    },
                    tooltips: {
                    mode: 'label',
                    },
                    hover: {
                    mode: 'nearest',
                    intersect: true
                    },
                    scales: {
                    xAxes: [{
                        display: true,
                        // ticks: {
                        // userCallback: function(label, index, labels) {
                        //     if(typeof label === "string")
                        // {
                        //     return label.substring(0,1)
                        // }
                        //     return label
                        // },
                        // },
                        scaleLabel: {
                        display: true,
                        labelString: '{{$xAxis}}'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                        display: true,
                        labelString: 'Count'
                        }
                    }]
                    }
                    // legend: {display: false}
                }
                // options: {
                //     legend: {display: false}
                // }
                });

//********(*(*(*(*(*(*(*(*(*(*(*(*(**((*(*(*(*) Commenting (*)(*(*(*(*(*(*(*(*(*(*(*(*(*(**((*(*(*(*(*(*//
                //********** Vendor Equipments ************//
                // Load google charts
                // google.charts.load('current', {'packages':['corechart']});
                // google.charts.setOnLoadCallback(drawChart);

                // Draw the chart and set the chart values
                function drawChartVdrEquipments() {
                    var data = google.visualization.arrayToDataTable([
                    ['Task', 'Vendor Equipments'],
                    ['Available('+{{$vdr_available_equip}}+')', {{$vdr_available_equip}}],
                    ['Rented('+{{$vdr_rented_equip}}+')', {{$vdr_rented_equip}}]
                    ]);

                    var options = {
                    'title':'Vendor Equipments('+{{$vdr_total_equip}}+')',
                    'colors': ['#1cc88a','#f6c23e'],
                    is3D: true};
                    // Optional; add a title and set the width and height of the chart
                    //var options = {'title':'My Average Day', 'width':550, 'height':400};

                    // Display the chart inside the <div> element with id="piechart"
                    var chart = new google.visualization.PieChart(document.getElementById('vdrEquipmentsPiechart'));
                    chart.draw(data, options);
                    // chart.draw(data);
                }



                // Statistics Chart or graph code....
                var xValues = [{{implode(",",$period_vdr_equip)}}];
                    // alert(xValues);

                new Chart("vdrEquipmentsStats", {
                type: "line",
                data: {
                    labels: xValues,
                    datasets: [{ 
                    label: "Rented",
                    data: [{{implode(",",$rented_arr)}}],
                    borderColor: "#1cc88a",
                    fill: false
                    }]
                },
                options: {
                    title: {
                    display: true,
                    text: 'Leads'
                    },
                    tooltips: {
                    mode: 'label',
                    },
                    hover: {
                    mode: 'nearest',
                    intersect: true
                    },
                    scales: {
                    xAxes: [{
                        display: true,
                        // ticks: {
                        // userCallback: function(label, index, labels) {
                        //     if(typeof label === "string")
                        // {
                        //     return label.substring(0,1)
                        // }
                        //     return label
                        // },
                        // },
                        scaleLabel: {
                        display: true,
                        labelString: '{{$xAxis}}'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                        display: true,
                        labelString: 'Count'
                        }
                    }]
                    }
                    // legend: {display: false}
                }
                // options: {
                //     legend: {display: false}
                // }
                });

                //********** Q5C Equipments ************//
                // Load google charts
                // google.charts.load('current', {'packages':['corechart']});
                // google.charts.setOnLoadCallback(drawChart);

                // Draw the chart and set the chart values
                function drawChartQ5cEquipments() {
                var data = google.visualization.arrayToDataTable([
                ['Task', 'Q5C Equipments'],
                ['Available('+{{$available_equip}}+')', {{$available_equip}}],
                ['Rented('+{{$rented_equip}}+')', {{$rented_equip}}]
                ]);

                var options = {
                'title':'Q5C Equipments('+{{$total_equip}}+')',
                'colors': ['#1cc88a','#f6c23e'],
                is3D: true};

                // Optional; add a title and set the width and height of the chart
                //var options = {'title':'My Average Day', 'width':550, 'height':400};

                // Display the chart inside the <div> element with id="piechart"
                var chart = new google.visualization.PieChart(document.getElementById('q5cEquipmentsPiechart'));
                chart.draw(data, options);
                // chart.draw(data);
                }



                // Statistics Chart or graph code....
                var xValues = [100,200,300,400,500,600,700,800,900,1000];

                new Chart("q5cEquipmentsStats", {
                type: "line",
                data: {
                    labels: xValues,
                    datasets: [{ 
                    data: [860,1140,1060,1060,1070,1110,1330,2210,7830,2478],
                    borderColor: "#e74a3b",
                    fill: false
                    }, { 
                    data: [1600,1700,1700,1900,2000,2700,4000,5000,6000,7000],
                    borderColor: "#1cc88a",
                    fill: false
                    }, { 
                    data: [300,700,2000,5000,6000,4000,2000,1000,200,100],
                    borderColor: "blue",
                    fill: false
                    }]
                },
                options: {
                    legend: {display: false}
                }
                });


                // //********** Vendors ************//
                // // Load google charts
                // // google.charts.load('current', {'packages':['corechart']});
                // // google.charts.setOnLoadCallback(drawChart);

                // // Draw the chart and set the chart values
                // function drawChartVendors() {
                // var data = google.visualization.arrayToDataTable([
                // ['Task', 'Hours per Day'],
                // ['Work', 8],
                // ['Eat', 2],
                // ['TV', 4],
                // ['Gym', 2],
                // ['Sleep', 8]
                // ]);

                // // Optional; add a title and set the width and height of the chart
                // //var options = {'title':'My Average Day', 'width':550, 'height':400};

                // // Display the chart inside the <div> element with id="piechart"
                // var chart = new google.visualization.PieChart(document.getElementById('vendorsPiechart'));
                // // chart.draw(data, options);
                // chart.draw(data);
                // }



                // // Statistics Chart or graph code....
                // var xValues = [100,200,300,400,500,600,700,800,900,1000];

                // new Chart("vendorsStats", {
                // type: "line",
                // data: {
                //     labels: xValues,
                //     datasets: [{ 
                //     data: [860,1140,1060,1060,1070,1110,1330,2210,7830,2478],
                //     borderColor: "#e74a3b",
                //     fill: false
                //     }, { 
                //     data: [1600,1700,1700,1900,2000,2700,4000,5000,6000,7000],
                //     borderColor: "#1cc88a",
                //     fill: false
                //     }, { 
                //     data: [300,700,2000,5000,6000,4000,2000,1000,200,100],
                //     borderColor: "blue",
                //     fill: false
                //     }]
                // },
                // options: {
                //     legend: {display: false}
                // }
                // });

                // // jQuery(function ($) {
                // //     var data1 = [12, 3, 4, 2, 12, 3, 4, 17, 22, 34, 54, 67];
                // //     var data2 = [3, 9, 12, 14, 22, 32, 45, 12, 67, 45, 55, 7];
                // //     var data3 = [23, 19, 11, 134, 242, 352, 435, 22, 637, 445, 555, 57];
                // //     var data4 = [2, 7, 10, 12, 19, 28, 40, 8, 60, 38, 49, 5];
                // //     var data5 = [23, 19, 11, 134, 242, 352, 435, 22, 637, 445, 555, 57];
                        
                // //     // $("#chart1").shieldChart({
                // //     //     exportOptions: {
                // //     //         image: false,
                // //     //         print: false
                // //     //     },
                // //     //     axisY: {
                // //     //         title: {
                // //     //             text: "Break-Down for selected quarter"
                // //     //         }
                // //     //     },
                // //     //     dataSeries: [{
                // //     //         seriesType: "bar",
                // //     //         data: data1
                // //     //     }]
                // //     // });

                // //     $("#equipmentsStatsBar").shieldChart({
                // //         exportOptions: {
                // //             image: false,
                // //             print: false
                // //         },
                // //         axisY: {
                // //             title: {
                // //                 text: "Break-Down for selected quarter"
                // //             }
                // //         },  
                // //         dataSeries: [{
                // //             seriesType: "bar",
                // //             data: data2
                // //         }, {
                // //             seriesType: "bar",
                // //             data: data3
                // //         }, {
                // //             seriesType: "bar",
                // //             data: data4
                // //         }, {
                // //             seriesType: "bar",
                // //             data: data5
                // //         }]
                // //     });
                // // });
            });
            function padTo2Digits(num) {
                return num.toString().padStart(2, '0');
            }

            function formatDate(date) {
                return [
                    padTo2Digits(date.getDate()),
                    padTo2Digits(date.getMonth() + 1),
                    date.getFullYear(),
                ].join('-');
            }
            $("#select_avg_city").change(function(){
                window.location.assign("{{url('/')}}/dashboard?select_avg_city="+this.value);
            });
        </script>
    @endsection