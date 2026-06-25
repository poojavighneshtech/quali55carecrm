
@extends('header_and_sidebar')
    
    @section('content')
        <div class="card my-2" id="filter_card">
            <div class="card-header border-primary" id="filter_card">
                <div class="row">
                    <div class="col text-primary" id="heading-filter" class="d-block">
                        <strong>Lead Management</strong>
                    </div>
                </div>
            </div>
            <div class="card-body border-primary">
                <div class="row">
                    <div class="col-md-9">
                        <label for="">Total Equipment Leads</label>
                        <label for="">{{number_format($totalEquipCount)}}</label>&emsp;
                        <label for="">Total Nursing Leads</label>
                        <label for="">{{number_format($totalNurseCount)}}</label>
                    </div>
                    <div class="col-md-3">
                        <select class="select form-control" onchange="location='{{route('lead_reports')}}'+'?filter='+this.value">
                            <option value='all'selected name="all">All</option>
                            <option value='today' @if(request()->get('filter') == 'today'){{"selected"}}@endif name="today">Today</option>
                            <option value='yesterday' @if(request()->get('filter') == 'yesterday'){{"selected"}}@endif name="yesterday">Yesterday</option>
                            <option value='past_3_days' @if(request()->get('filter') == 'past_3_days'){{"selected"}}@endif name="past_3_days">Past 3 Days</option>
                            <option value='week' @if(request()->get('filter') == 'week'){{"selected"}}@endif name="week">1 Week</option>
                            <option value='month' @if(request()->get('filter') == 'month'){{"selected"}}@endif name="month">Current Month</option>
                        </select>
                    </div>
                </div>
                <br>
                <div class="table table-responsive jim-table-responsive">
                    <table class="display table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th rowspan="2" class="text-center">Sr. No</th>
                                <th rowspan="2" class="text-center">User / Lead Owner</th>
                                <th colspan="4" class="text-center">Equipment Leads</th>
                                <th colspan="4" class="text-center">Nursing Leads</th>
                            </tr>
                            <tr>
                                <th>Total Leads</th>
                                <th>In Process Leads</th>
                                <th>Converted Leads</th>
                                <th>Closed Leads</th>
                                <th>Total Leads</th>
                                <th>In Process Leads</th>
                                <th>Converted Leads</th>
                                <th>Closed Leads</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($details as $key=>$detail)
                            <tr>
                                <td data-label="#">{{$key+1}}</td>
                                <td data-label="User">{{$detail['user']}}</td>
                                <td data-label="Equip.Total Leads">{{number_format($detail['total_leads'])}}</td>
                                <td data-label="Equip.In Process">{{number_format($detail['process'])}}</td>
                                <td data-label="Equip.Converted">{{number_format($detail['converted'])}}</td>
                                <td data-label="Equip.Closed">{{number_format($detail['closed'])}}</td>
                                <td data-label="Nurse.Total Leads">{{number_format($detail['nur_total'])}}</td>
                                <td data-label="Nurse.In Process">{{number_format($detail['nur_process'])}}</td>
                                <td data-label="Nurse.Converted">{{number_format($detail['nur_converted'])}}</td>
                                <td data-label="Nurse.Closed">{{number_format($detail['nur_closed'])}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endsection
</body>

    @section('script')
    <script>
        $(document).ready(function() {
            // $('#records_filter').append("<label>Filter: <select class='form-control-sm' id='filter' name='filter'><option disabled selected>-----Select-----</option><option value='today'>Today</option><option value='yesterday'>Yesterday</option><<option value='past_3_days'>Past 3 Days</option><option value='week'>1 Week</option><option value='month'>Current Month</option><option value='all'>All</option></select></label>"); 
            if(localStorage['filteredReport'] != null)
            {
                $('#filter').val(localStorage['filteredReport']);
            }
            $('table.display').DataTable();
        } );        
    </script>                                                       

    @endsection
    
</html>