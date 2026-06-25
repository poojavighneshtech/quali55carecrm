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
        
        <style>
            .morecontent span {
                display: none;
            }
            .morelink {
                display: block;
            }
        </style>
    @endsection
</head>

<body id="page-top">	
		<!-- Page Wrapper -->
    
    @extends('header_and_sidebar')
       
    @section('content')
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
            <div class="container">  
                <div class="card">
                    <div class="card-header"  style="background-color: #4a6fdc; color: white;" >
                        <center>JD Converted Leads</center>
                    </div> 
                    <div class="card-body">
                        <form class="form" method="post" action="<?php echo url('/');?>/viewConvertedLeads">
                            {{csrf_field()}}
                        </form>
                        <div class="table">
                            <table class="table table-responsive" id="records">
                                <thead>
                                    <th>Sr. No</th>
                                    <th>Date&emsp;&emsp;&emsp;</th>
                                    <th>Customer Name</th>
                                    <th>Mobile Number</th>
                                    <th>Comment</th>
                                    <th>Add Comment</th>
                                    <th>Equipment</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Lead Owner</th>
                                </thead>
                                <tbody>
                                    @php
                                        $count = 0;    
                                    @endphp
                                    @if(isset($lead_details))
                                        @foreach ($lead_details as $lead_detail)
                                            {{!$products = json_decode($lead_detail['equipment_requirement'])}}
                                            @php
                                                $count = $count+1;
                                            @endphp
                                            <tr data-count="{{$count}}">
                                                <td>
                                                    {{$count}}
                                                    <input type="hidden" name="lead_id{{$count}}" id="lead_id{{$count}}" value="{{$lead_detail['id']}}">
                                                    <input type="hidden" name="user_id" id="user_id{{$count}}" value="{{session('user_id')}}">
                                                </td>
                                                <td>
                                                    {{date('d-M-Y',strtotime($lead_detail['creation_date']))}}
                                                    {{-- <a class="btn btn-outline-success btn-sm btn-block disabled" href="{{url('/')}}/create_jd_lead/{{$lead_details['jd_leads_id']}}" role="button" aria-disabled="true">Converted</a> --}}
                                                    {{-- <a class="btn btn-danger btn-sm btn-block" href="#" role="button" data-toggle="modal" data-target="#myModal" title="Close">Close</a>  --}}
                                                    {{-- @if($lead_details['status']=="In Process")
                                                        <a class="btn btn-outline-success btn-sm btn-block disabled" href="#" role="button" aria-disabled="true">Convert</a>
                                                    @else
                                                        <a class="btn btn-primary btn-sm btn-block" href="{{url('/')}}/in_process/{{$lead_details['jd_leads_id']}}/{{session('user_id')}}" role="button">In Process</a> 
                                                        <a class="btn btn-outline-success btn-sm btn-block disabled" href="#" role="button" aria-disabled="true">Convert</a>
                                                    @endif --}}
                                                </td>
                                                <td><a href="{{url('/')}}/jd_view_lead/{{$lead_detail['customer_id']}}/{{$lead_detail['id']}}" title="View Details">{{$lead_detail['customer_name']}}</a></td>
                                                <td><a href="{{url('/')}}/jd_view_lead/{{$lead_detail['customer_id']}}/{{$lead_detail['id']}}" title="View Details">{{$lead_detail['primary_contact_no']}}</a></td>
                                                <td>
                                                    <div class="more">
                                                            {{$lead_detail['comment']}}
                                                    </div>
                                                    <a class="btn btn-sm" href="#my_modal" data-toggle="modal" data-book-id="{{$lead_detail['comment']}}">...</a>
                                                    {{-- <span class="comment">
                                                        {{$lead_details['remark']}}
                                                    </span> --}}
                                                    
                                                </td>
                                                <td>
                                                    {{-- <a class="btn btn-danger btn-block" href="#comment_modal" role="button" data-toggle="modal" data-target="#comment_modal" data-book-id="{{$lead_details['jd_leads_id']}}" title="Comment"><i class="far fa-comment-alt fa-lg"></i></a> --}}
                                                    <button class="btn btn-success" src="#comment_modal"  data-toggle="modal" data-target="#comment_modal" title="Add Comment"><i class="far fa-comment-alt fa-lg"></i></button>
                                                </td>
                                                <td>{{implode(", ",$products)}}</td>
                                                <td>{{$lead_detail['location']}}</td>
                                                <td>{{$lead_detail['lead_status']}}</td>
                                                <td>{{$lead_detail['username']}}</td>
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
        <div class="modal" id="myModal">
            <div class="modal-dialog">
              <div class="modal-content">
              
                
                <form action="">
                    <div class="modal-header">
                        <h4 class="modal-title">Reason for closing Lead</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                
                    
                    <div class="modal-body">
                        {{-- <input type="hidden" name="modal_lead_id" id="modal_lead_id" value=""> --}}
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
                        <a class="btn btn-secondary" id="close_jd_lead" onclick="getlink();" href="#" {{--style="pointer-events: none; color: #ccc;"--}} title="Close"><i class="fas fa-window-close"></i> Close lead</a>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
              </div>
            </div>
        </div>

        <div class="modal" id="comment_modal">
            <div class="modal-dialog">
              <div class="modal-content">
              
                
                <form action="">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                
                    <div class="modal-body">
                        <label for="desc">Comment</label>
                        {{-- <input class="form-control" type="textarea" rows="5" name="desc" id="desc" placeholder="Remark*"> --}}
                        <textarea class="form-control" rows="5" name="desc" id="cmt_desc"></textarea>
                    </div>
                    
                    <div class="modal-footer">
                        <a class="btn btn-primary" id="add_converted_comment" onclick="add_converted_comment();" href="#" {{--style="pointer-events: none; color: #ccc;"--}} title="comment">Submit</a>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
              </div>
            </div>
        </div>
        <div class="modal" id="my_modal">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    
                </div>
                <div class="modal-body">
                  {{-- <div name="bookId"></div> --}}
                  <pre class="blockquote" name="bookId"></pre>
                  {{-- <input type="text" name="bookId" value=""/> --}}
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
        </div>
    @endsection
</body>
@section('script')
<script>
    function getlink()
    {
        var reason = $('#reasons').val();
        var desc = $('#desc').val();
        document.getElementById('close_jd_lead').href += '/'+reason+'/'+desc;
    }

    function add_converted_comment()
    {
        //var reason = $('#reasons').val();
        var desc = $('#cmt_desc').val();
        document.getElementById('add_converted_comment').href += '/'+desc;
    }
    $('#records tr').click(function() {    
        var count = this.dataset.count;
        // var cust_no = $('#cust_no'+count).text(); 
        //alert(count);   
        var lead_id = $('#lead_id'+count).val();
        var user_id = $('#user_id'+count).val();
        //var desc = $('#desc').val();
        document.getElementById('close_jd_lead').href ="<?php echo url('/');?>/close_jd_lead/"+user_id+"/"+lead_id;
        document.getElementById('add_converted_comment').href ="<?php echo url('/');?>/add_converted_comment/"+user_id+"/"+lead_id;
        // var url = "<?php echo url('/'); ?>/assign_vendor_byscript/"+customer_id+"/"+lead_id;
        // window.location.assign(url);
    });
           $(document).ready(function() {
        // Configure/customize these variables.
        var showChar = 100;  // How many characters are shown by default
        // var ellipsestext = "...";
        ellipsestext = "";
        var moretext = "";
        var lesstext = "";
        

        $('.more').each(function() {
            var content = $(this).html();
    
            if(content.length > showChar) {
    
                var c = content.substr(0, showChar);
                var h = content.substr(showChar, content.length - showChar);
    
                 var html = c + '<span class="moreellipses">' + ellipsestext+ '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';
    
                $(this).html(html);
            }
    
        });
    
        $(".morelink").click(function(){
            if($(this).hasClass("less")) {
                $(this).removeClass("less");
                $(this).html(moretext);
            } else {
                $(this).addClass("less");
                $(this).html(lesstext);
            }
            $(this).parent().prev().toggle();
            $(this).prev().toggle();
            return false;
        });
    });
    $('#my_modal').on('show.bs.modal', function(e) {
        var bookId = $(e.relatedTarget).data('book-id');
        $(e.currentTarget).find('pre[name="bookId"]').text(bookId);
    });
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
            var url = "<?php echo url('/');?>/filterJDLeadsConverted/"+dataString;
            window.location.assign(url);
        })
    });
</script>
    @endsection
</html>