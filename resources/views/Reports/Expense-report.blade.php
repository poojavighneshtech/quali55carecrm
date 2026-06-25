@extends('header_and_sidebar')
@section('title')
   Monthly Report
@endsection
@section('header')
    
@endsection

@section('content')
    <div class="card">
        <div class="card-body container-fluid">
            <form action="{{route('expense-report')}}" method="get">
                @csrf
                <div class="row">
                    <div class="col-md-10">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="date" class="form-control form-control-sm" name="start_date" id="">
                            </div>
                            <div class="col-md-6">
                                <input type="date" class="form-control form-control-sm" name="end_date" id="">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="form-control form-control-sm btn btn-outline-success">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $("#records").dataTable();
        $(document).ready(function()
        {
           
        });
    </script>                                                         
@endsection