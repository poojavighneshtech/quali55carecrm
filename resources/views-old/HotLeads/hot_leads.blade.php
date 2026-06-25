@extends('header_and_sidebar')
<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
    
        <title>Hot Leads</title>
        {{-- <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script> --}}
        {{-- @section('styles')
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.2/css/bootstrap.min.css"/>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.min.css"/>
            <!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script> -->
            <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
        @endsection --}}
    </head>

<body id="page-top">	
		<!-- Page Wrapper -->
        
    
    @section('content')
    <div class="container">
        <div class="leads">
            @if(session()->has('message'))
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>
            @endif
            @if(session()->has('message_delete'))
                <div class="alert alert-danger">
                    {{ session()->get('message_delete') }}
                </div>
            @endif
            <div class="card border-primary">
                <div class="card-header text-primary">
                    <h5>Hot Leads</h5>
                </div>
                <div class="card-body">
                    <div class="table table-responsive">
                        <table class="table table-bordered text-nowrap" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Sr.No.</th>
                                    <th>Action</th>
                                    <th>Date Time</th>
                                    <th>Customer Name</th>
                                    <th>Contact Number</th>
                                    <th>Equipment</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Lead Source</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{!$sr_no = 1}}
                                @forelse($get_all_hot_leads as $key=>$lead)
                                    <tr>
                                        <td>{{$get_all_hot_leads->firstitem()+$loop->index}}</td>
                                        <td>
                                            @if($lead->hot_leads_status == "Pending")
                                                <a class="btn btn-primary btn-sm btn-block" href="{{url('/')}}/in_process_hot_leads/{{$lead->hot_lead_id}}/{{session('user_id')}}" role="button">Progress it</a>
                                            @endif
                                        </td>
                                        <td>{{date("d-m-Y h:i:sa", strtotime($lead->hot_leads_created_at))}}</td>
                                        <td>{{$lead->customer_name}}</td>
                                        <td>{{$lead->hot_leads_contact_no}}</td>
                                        <td>{{$lead->hot_leads_equipment}}</td>
                                        <td>{{$lead->city}}</td>
                                        <td>{{$lead->hot_leads_status}}</td>
                                        <td>{{$lead->hot_leads_source}}</td>
                                    </tr>
                                    {{!$sr_no = $sr_no + 1}}
                                @empty
                                    <tr>
                                        <td colspan="9">
                                            <h4 class="text-center">No Records Found</h4>
                                        </td>
                                    </tr>
                                @endforelse                        
                            </tbody>
                        </table>
                        {{$get_all_hot_leads->links('Custom.Pagination.pagination')}}
                    </div>
                </div>
            </div>
            </form>
                
        </div>
        <div class="modal" id="myModal">
            <div class="modal-dialog">
              <div class="modal-content">
              
                <!-- Modal Header -->
                <form>
                <div class="modal-header">
                  <h4 class="modal-title">Reason for closing Lead</h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                
                <!-- Modal body -->
                <div class="modal-body">
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
                
                <!-- Modal footer -->
                <div class="modal-footer">
                    <a class="btn btn-secondary" id="close_lead" onclick="getlink();" href="#" {{--style="pointer-events: none; color: #ccc;"--}} title="Close"><i class="fas fa-window-close"></i> Close lead</a>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                </div>
                </form>
                
              </div>
            </div>
          </div>
    </div>
    @endsection
</body>
@section('script')
    <script>
        // $(document).ready(function(){
        //     $('#records_filter').append("<label>Filter: <select class='form-control-sm' id='filter' name='filter'><option disabled selected>-----Select-----</option><option value='today'>Today</option><option value='yesterday'>Yesterday</option><<option value='past_3_days'>Past 3 Days</option><option value='week'>1 Week</option><option value='month'>Current Month</option><option value='all'>All</option></select></label>"); 
        //     if(localStorage['filtered'] != null)
        //     {
        //         $('#filter').val(localStorage['filtered']);
        //     }
        //     $('#filter').on("change",function(){
        //         var filter_by = $('#filter').val();
        //         var section = "In_Process";
        //         localStorage['filtered'] = filter_by;
        //         //alert(filter_by);
        //         var dataString = (filter_by);
        //         var url = "<?php echo url('/');?>/filterHotLeads/"+dataString;
        //         window.location.assign(url);
        //     })
        // });

    </script>
    @endsection
</html>