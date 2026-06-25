@extends('header_and_sidebar')
<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        
        <title>Raise Complaint</title>
        {{-- <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script> --}}
        <link rel="stylesheet" href="{{url('/')}}/assets/dist/toast.min.css ">
        <!-- Boostrap 4 CSS -->
   
        @section('styles')
        <style>
        </style>
        @endsection
    </head>

<body id="page-top">	
        <!-- Page Wrapper -->
        
    @section('content')
        @if(session()->has('message_delete'))
            <div class="alert alert-danger">
                {{ session()->get('message_delete') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if(session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"  style="background-color: #4a6fdc; color: white;" >
                    <center>Complaint Mamagement</center>
                </div> 
                <div class="card-body">
                    <div class="card">
                        {{-- <center><span style="margin-top:-20%">Filter</span></center> --}}
                        <div class="card-body">
                            <form action="{{url('/')}}/complaint_mgmt_filter" method="GET">
                                @csrf
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="row">
                                                    <div class="col-md-4 text-right">
                                                        <label for="customer_name"><strong>Customer Name:</strong></label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" name="filter_customer_name" id="txt_filter_complaint_name"  placeholder="Customer Name.." 
                                                            size="5" value="@if(isset($filter_arr['cust_name'])){{$filter_arr['cust_name']}}@endif" list="datalist_customers" autocomplete="off">
                                                        <datalist id="datalist_customers"></datalist>
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="row">
                                                    <div class="col-md-4 text-right">
                                                        <label for="customer_name"><strong>Complaint ID :</strong></label>
                                                    </div>
                                                    <div class="col-md-8 text-right">
                                                        <input type="text" class="form-control" name="filter_complaint_id"  id="txt_filter_complaint_id" placeholder="Complaint ID.."
                                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" value="@if(isset($filter_arr['cmp_id'])){{$filter_arr['cmp_id']}}@endif">
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="row">
                                                    <div class="col-md-4 text-right">
                                                        <label for="customer_name"><strong>Status :</strong></label>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <select class="selectpicker" name="filter_complaint_status" id="select_filter_complaint_status">
                                                            <option value="All" @if(isset($filter_arr['cmp_status']) && $filter_arr['cmp_status']=='All'){{"selected"}}@endif>All</option>
                                                            <option value="Open" @if(isset($filter_arr['cmp_status']) && $filter_arr['cmp_status']=='Open'){{"selected"}}@endif>Open</option>
                                                            <option value="Closed" @if(isset($filter_arr['cmp_status']) && $filter_arr['cmp_status']=='Closed'){{"selected"}}@endif>Closed</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <label for="date_filter"><strong>Date Filter</strong></label>
                                                            </div>
                                                            <div class="col-md-6 text-right">
                                                                {{-- <button type="button" class="btn btn-outline-danger btn-sm" id="btn_clear">Clear Filter</button> --}}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label for="">From</label>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <input type="date" name="filter_from_date" id="input_from_date" class="form-control" value="@if(isset($filter_arr['from_date'])){{$filter_arr['from_date']}}@endif">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label for="">To</label>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <input type="date" name="filter_end_date" id="input_end_date" class="form-control" value="@if(isset($filter_arr['end_date'])){{$filter_arr['end_date']}}@endif">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btn_clear">Clear Filter</button>
                                        <br>
                                        <br>
                                        <button type="submit" class="btn btn-outline-primary btn-block">Submit</button>
                                        <br>
                                        <a href="{{url('/')}}/create_complaint" class="btn btn-outline-danger btn-block"><i class="fa fa-plus" aria-hidden="true"></i> Raise Complaint</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <br>
                    {{--Complaints here customer wise--}}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                {{-- <div class="card-header">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <button type="button" class="btn btn-outline-primary btn-sm"><i class="fa fa-plus" aria-hidden="true"></i> Raise Complaint</button>
                                        </div>
                                    </div>
                                </div> --}}
                                <div class="card-body">
                                    <table class="table table-hover">
                                        <thead class="thead-light">
                                            <th>Date</th>
                                            <th>Complaint ID</th>
                                            <th>Customer Name</th>
                                            <th>Raised By</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </thead>
                                        <tbody>
                                            @foreach($get_all_complaints as $key=>$complaint)
                                                <tr>
                                                    <td>{{date('d-M-Y',strtotime($complaint->complaint_date))}}</td>
                                                    <td>{{$complaint->generated_complaint_id}}</td>
                                                    <td>{{$complaint->customer_name}}</td>
                                                    <td>{{$complaint->username}}</td>
                                                    <td>{{$complaint->status}}</td>
                                                    <td>
                                                        @if($complaint->status!='Closed')
                                                            <a type="button" class="btn btn-outline-success btn-sm" href="{{url('/')}}/complaint_details/{{$complaint->generated_complaint_id}}" 
                                                                data-toggle="tooltip" data-placement="bottom" title="Close Complaint"><i class="fas fa-check"></i></a>
                                                            {{-- <a type="button" class="btn btn-outline-primary btn-sm" href="{{url('/')}}/complaint_details/{{$complaint->generated_complaint_id}}"
                                                                data-toggle="tooltip" data-placement="bottom" title="View Complaint" ><i class="fa fa-eye" aria-hidden="true"></i></a> --}}
                                                            <button type="button" class="btn btn-outline-danger btn-sm" data-toggle="modal" data-target="#delete_cmp_modal">
                                                                <i class="far fa-trash-alt"></i>
                                                            </button>
                                                            {{--modal convert--}}
                                                            <form action="{{url('/')}}/complaint_delete" method="post">
                                                                @csrf
                                                                <div class="modal fade" id="delete_cmp_modal" tabindex="-1" role="dialog" aria-labelledby="delete_cmp_modalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                        <h5 class="modal-title" id="delete_cmp_modalLabel">Delete Complaint</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            Are you sure want to delete complaint
                                                                            <input type="hidden" name="complaint_tbl_id" id="modal_txt_complaint_tbl_id" value="{{$complaint->id}}">
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                        <button type="submit" class="btn btn-outline-danger">submit</button>
                                                                        </div>
                                                                    </div>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        @else
                                                            <a type="button" class="btn btn-outline-primary btn-sm" href="{{url('/')}}/complaint_details/{{$complaint->generated_complaint_id}}"
                                                                data-toggle="tooltip" data-placement="bottom" title="View Complaint" ><i class="fa fa-eye" aria-hidden="true"></i></a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @php
                                        $append_arr = array();
                                        if(isset($filter_arr['cust_name']))
                                        {
                                            $append_arr['filter_customer_name'] = $filter_arr['cust_name'];
                                        }
                                        if(isset($filter_arr['cmp_id']))
                                        {
                                            $append_arr['filter_complaint_id']=$filter_arr['cmp_id'];
                                        }
                                        if(isset($filter_arr['cmp_status']))
                                        {
                                            $append_arr['filter_complaint_status']=$filter_arr['cmp_status'];
                                        }
                                        if(isset($filter_arr['from_date']))
                                        {
                                            $append_arr['filter_from_date']=$filter_arr['from_date'];
                                        }
                                        if(isset($filter_arr['end_date']))
                                        {
                                            $append_arr['filter_end_date']=$filter_arr['end_date'];
                                        }
                                        
                                    @endphp
                                    {{$get_all_complaints->appends($append_arr)->links('Custom.Pagination.pagination')}}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

       
    @endsection
    @section('script')
    {{-- <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script> --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js" ></script> --}}
    <script type="text/javascript"></script>
        <script>
            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            });
            $('#btn_clear').on('click',function(){
                // $('#txt_filter_complaint_name').val('');
                // $('#txt_filter_complaint_id').val('');
                // $('#select_filter_complaint_status').selectpicker('val','All');
                // $('#input_from_date').val('');
                // $('#input_end_date').val('');
                var url="<?php echo url('/');?>/raise_complaint";
                window.location.href = url;
            });
            //populate customer name in text field
            var route = "{{ url('complaint_customers_populate') }}";
            $('#txt_filter_complaint_name').typeahead({ 
                source: function (query, process) {
                    return $.get(route, {
                        query: query
                    }, function (data) {
                        //var obj = jQuery.parseJSON(data);
                        //console.log(data);
                        return process(data);
                    });
                }
             });
            
        </script>
    @endsection
</body>
</html>