@extends('header_and_sidebar')

@section('styles')

@endsection

@section('content')
<div class="card my-3">
    <form action="{{route('equipment-report')}}" method="get">
        @csrf
        <div class="card-header border-primary" id="filter_card">
            <div class="row">
                <div class="col text-primary" data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                    <strong>Equipments</strong>
                </div>
                <div class="col-auto">
                    <a data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                        <i class="fa fa-chevron-down pull-right"></i>&emsp;<b>Filter</b>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body border-primary collapse" id="filter-collapse" aria-labelledby="headingTwo" data-parent="#filter_card">
            <div class="row form-group">
                <div class="col-md-2"></div>
                <div class="col-md-4">
                    <label for="start_date">Start Date</label>
                    <input type="date" class="form-control form-control-sm" name="start_date" id="start_date" value="{{request()->get('start_date')}}">
                </div>
                <div class="col-md-4">
                    <label for="end_date">End Date</label>
                    <input type="date" class="form-control form-control-sm" name="end_date" id="end_date" value="{{request()->get('end_date')}}">
                </div>
                <div class="col-md-2">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    <button class="btn btn-sm btn-outline-success" type="submit" name="btn_submit" value="Submit">Search</button>
                    <a href="{{route('equipment-report')}}" class="btn btn-sm btn-outline-secondary my-1">Clear</a>
                    <button class="btn btn-sm btn-outline-primary" type="submit" name="btn_submit" value="Export">Export</button>
                </div>
            </div>
        </div>
    </form>

    <div class="table table-responsive my-3">
        <table class="table table-stripped">
            <thead>
                <tr>
                    <th>Sr. No</th>
                    <th>Equipment Name</th>
                    <th>Total Count</th>
                    <th>Sale</th>
                    <th>Rental</th>
                    <th>Live</th>
                    <th>Stop</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($equipment_report as $key=>$equipment)
                    <tr>
                        <td>{{$equipment_report->firstItem()+$loop->index}}</td>
                        <td>{{$equipment->name}}</td>
                        <td>{{$equipment->sale + $equipment->rental}}</td>
                        <td>{{$equipment->sale}}</td>
                        <td>{{$equipment->rental}}</td>
                        <td>{{$equipment->live}}</td>
                        <td>{{$equipment->stop}}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No Records Found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{$equipment_report->appends(request()->query())->links('Custom.Pagination.pagination')}}
    </div>
</div>
@endsection

@section('script')

@endsection