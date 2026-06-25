@extends('header_and_sidebar')

@section('style')

@endsection

@section('content')
<div class="container-fluid">
    @if(session()->has('message'))
        <div class="alert alert-success">
            {{ session()->get('message') }}
        </div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger">
            {{ session()->get('error') }}
        </div>
    @endif 
    <div class="card my-3">
        <div class="card-header">
            <h4>Upload Google Campain Report</h4>
        </div>
        <div class="card-body text-center">
            <form action="{{url('/')}}/upload-records" method="POST" enctype="multipart/form-data">
                @csrf
                <label for="upload_report">Upload Report(.csv)</label>
                <input type="file" name="upload_report" id="upload_report">
                <br>
                <button class="btn btn-outline-success" type="submit">Upload</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')

@endsection