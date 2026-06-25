<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Inquiry : Create Lead</title>
    {{-- <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script> --}}
    @section('styles')
        
    @endsection
</head>

<body id="page-top">	
		<!-- Page Wrapper -->
    
    @extends('header_and_sidebar')
        
    @section('content')
        <div class="leads">
           
            @if(session()->has('message_delete'))
                <div class="alert alert-danger">
                    {{ session()->get('message_delete') }}
                </div>
            @endif 
            <div class="container">  
                 @if(session()->has('message'))
                    <div class="alert alert-success">
                        {{ session()->get('message') }}
                    </div>
                @endif
                <div class="card">
                    <div class="card-header"  style="background-color: #4a6fdc; color: white;" >
                        <center>JD Leads</center>
                    </div> 
                    <div class="card-body">
                        <form class="form" method="post" >
                            {{csrf_field()}}
                        </form>
                        <div class="table">
                            <table class="table table-responsive" id="records">
                                <thead>
                                    <tr>
                                        <th>Sr.No</th>
                                        <th>Action </th>
                                        <th>Date / Time &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</th>
                                        <th>Customer Name</th>
                                        <th>Mobile Number</th>                                        
                                        <th>Category</th>
                                        <th>Area</th>
                                        <th>City</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $count = 0;    
                                    @endphp
                                    @if(isset($jd_lead_details))
                                        @foreach ($jd_lead_details as $lead_details)
                                            @php
                                                $count = $count+1;
                                            @endphp
                                            <tr data-count="{{$count}}"> 
                                                <td>
                                                    {{$count}}
                                                    <input type="hidden" name="lead_id{{$count}}" id="lead_id{{$count}}" value="{{$lead_details['jd_leads_id']}}">
                                                    <input type="hidden" name="user_id" id="user_id{{$count}}" value="{{session('user_id')}}">
                                                </td>
                                                <td>
                                                    <a class="btn btn-primary btn-sm btn-block" href="{{url('/')}}/in_process/{{$lead_details['jd_leads_id']}}/{{session('user_id')}}" role="button">Progress it</a> 
                                                    {{-- <a class="btn btn-danger btn-sm btn-block" href="#" role="button" data-toggle="modal" data-target="#myModal" title="Close">Close</a>  --}}
                                                    {{-- @if($lead_details['status']=="In Process")
                                                        <a class="btn btn-outline-success btn-sm btn-block disabled" href="#" role="button" aria-disabled="true">Convert</a>
                                                    @else
                                                        <a class="btn btn-primary btn-sm btn-block" href="{{url('/')}}/in_process/{{$lead_details['jd_leads_id']}}/{{session('user_id')}}" role="button">In Process</a> 
                                                        <a class="btn btn-outline-success btn-sm btn-block disabled" href="#" role="button" aria-disabled="true">Convert</a>
                                                    @endif --}}
                                                </td>
                                                <td>{{$lead_details['date']}} {{$lead_details['time']}}</td>
                                                <td>{{$lead_details['name']}}</td>
                                                <td>{{$lead_details['mobile']}}</td>
                                                <td>{{$lead_details['category']}}</td>
                                                <td>{{$lead_details['area']}}</td>
                                                <td>{{$lead_details['city']}}</td>
                                                <td>{{$lead_details['email']}}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--<div class="modal" id="myModal">
            <div class="modal-dialog">
              <div class="modal-content">
              
                
                <form action="">
                    <div class="modal-header">
                        <h4 class="modal-title">Reason for closing Lead</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                
                    
                    <div class="modal-body">
                        <input type="hidden" name="modal_lead_id" id="modal_lead_id" value="">
                        <select class="form-control" id="reasons" required>
                        <option disabled selected>--Select Reason*--</option>
                        <option>Not interested</option>
                        <option>Ringing</option>
                        <option>Not required</option>
                        <option>Will Confirm Later</option>
                        {{-- <option>Converted</option> --}}
                        <option>Mobile Off</option>
                    </select>
                    <label for="desc">Remark</label>
                    {{-- <input class="form-control" type="textarea" rows="5" name="desc" id="desc" placeholder="Remark*"> --}}
                    <textarea class="form-control" rows="5" name="desc" id="desc"></textarea>
                    </div>
                
                    
                    <div class="modal-footer">
                        <a class="btn btn-secondary" id="close_lead" onclick="getlink();" href="#" {{--style="pointer-events: none; color: #ccc;"--}} title="Close"><i class="fas fa-window-close"></i> Close lead</a>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
              </div>
            </div>
        </div> -->
    @endsection
</body>
@section('script')
    <script>
        $(document).ready(function(){
            $("#search").on('input',function(){
                var search = $("#search").val();
                if(search == 'today' || search == 'Today')
                {
                    var newRow ="<tr><td>ahshshshs</td>";
                    $('#records tbody').append(newRow);
                }
            })
            $('#records_filter').append("<label>Filter: <select class='form-control-sm' id='filter' name='filter'><option disabled selected>-----Select-----</option><option value='today'>Today</option><option value='yesterday'>Yesterday</option><<option value='past_3_days'>Past 3 Days</option><option value='week'>1 Week</option><option value='month'>Current Month</option><option value='all'>All</option></select></label>"); 
            if(localStorage['filtered'] != null)
            {
                $('#filter').val(localStorage['filtered']);
            }
            $('#filter').on("change",function(){
                var filter_by = $('#filter').val();
                localStorage['filtered'] = filter_by;
                //alert(filter_by);
                var dataString = (filter_by);
                var url = "<?php echo url('/');?>/filterJDLeadsViewAll/"+dataString;
                window.location.assign(url);
            })
        });
    </script>
@endsection
</html>