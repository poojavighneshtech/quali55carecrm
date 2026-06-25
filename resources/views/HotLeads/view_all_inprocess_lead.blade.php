<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>View In Process Leads</title>
    {{-- <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script> --}}
    @section('styles')
        {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.2/css/bootstrap.min.css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.min.css"/>
        <!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script> -->
        <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script> --}}
        <style>
           .morecontent span {
                display: none;
            }
            .morelink {
                display: block;
            }
            /* .comment {  
                height: 100px;  
                margin: 10px;  
            }  */
            
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
                        <center>View In Process Leads</center>
                    </div> 
                    <div class="card-body">
                        <form class="form" method="post" action="{{url('/')}}/view_hot_leads_in_process_leads" >
                            {{csrf_field()}}
                            <div class="row">
                                <div class="col-md-5"></div>
                                <div class="col-md-4"></div>
                                <div class="col-md-3">
                                    <select class="form-control selectpicker border border-dark border-bottom-0" name="date_filter" id="select_date_filter" title="Date Filter">
                                        <option value="{{request()->fullUrlWithQuery(['date_filter'=>'today'])}}">Today</option>
                                        <option value="{{request()->fullUrlWithQuery(['date_filter'=>'yesterday'])}}">Yesterday</option>
                                        <option value="{{request()->fullUrlWithQuery(['date_filter'=>'3_days'])}}">Past 3 Days</option>
                                        <option value="{{request()->fullUrlWithQuery(['date_filter'=>'week'])}}">Week</option>
                                        <option value="{{request()->fullUrlWithQuery(['date_filter'=>'all'])}}">All</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                        <table class="table table-bordered table-responsive text-nowrap" style="width:100%;" data-page-length='250'>
                            <thead>
                                <tr>
                                    <th>Sr.No.</th>
                                    <th>Action</th>
                                    <th>Date Time</th>
                                    <th>Comment</th>
                                    <th>Add Comment</th>
                                    <th>Contact Number</th>
                                    <th>Status</th>
                                    <th>Lead Owner</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $count = 0;    
                                @endphp
                                @forelse($get_all_hot_leads as $key=>$lead)
                                    @php
                                        $count = $count+1;
                                    @endphp
                                    <tr data-count="{{$count}}">
                                        <td>{{$get_all_hot_leads->firstItem()+$loop->index}}</td>
                                        <td>
                                            <a class="btn btn-success btn-sm" href="{{url('/')}}/qualify_hot_leads/{{$lead->hot_lead_id}}" role="button">Qualify</a>
                                            <a class="btn btn-danger btn-sm" role="button" data-toggle="modal" data-target="#ClosedModel{{$key}}" title="Close">Close</a>
                                            <div class="modal" id="ClosedModel{{$key}}">
                                                <div class="modal-dialog">
                                                  <div class="modal-content">
                                                    <form action="{{url('/')}}/close_hot_lead/{{$lead->hot_lead_id}}" method="post">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Reason for closing Lead</h4>
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <select class="form-control" id="reasons" name="reason" required>
                                                                <option disabled selected>--Select Reason*--</option>
                                                                <option>Not interested</option>
                                                                <option>Ringing</option>
                                                                <option>Not required</option>
                                                                <option>Will Confirm Later</option>
                                                                <option>Mobile Off</option>
                                                            </select>
                                                            <label for="desc">Remark</label>
                                                            <textarea class="form-control" rows="5" name="desc" id="desc"></textarea>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-secondary" id="close_hot_lead" title="Close"><i class="fas fa-window-close"></i> Close lead</button>
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                        </div>
                                                    </form>
                                                  </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{date("d-m-Y h:i:sa", strtotime($lead->hot_leads_created_at))}}</td>
                                        <td>
                                            @if(isset($lead->hot_leads_comment))
                                                {{substr($lead->hot_leads_comment,'0','10')}}<span class="btn btn-default" href="#" data-tooltip="tooltip" data-placement="bottom" title="View More" data-toggle="modal" data-target="#modal_view_more{{$key}}">...</span>
                                                {{--modal popup for View more comment--}}
                                                <div class="modal fade" id="modal_view_more{{$key}}" tabindex="-1" role="dialog" aria-labelledby="view_more_comment" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="view_more_comment">Comments</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <pre>{{$lead->hot_leads_comment}}</pre>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-success btn-sm" src="#comment_modal{{$key}}"  data-toggle="modal" data-target="#comment_modal{{$key}}" title="Add Comment"><i class="far fa-comment-alt fa-lg"></i></button>
                                            <div class="modal" id="comment_modal{{$key}}">
                                                <div class="modal-dialog">
                                                  <div class="modal-content">
                                                    <form action="{{url('/')}}/add_hot_lead_comment/{{$lead->hot_lead_id}}" method="post">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        </div>
                                                    
                                                        <div class="modal-body">
                                                            <label for="desc">Comment</label>
                                                            <textarea class="form-control" rows="5" name="desc" id="cmt_desc" required></textarea>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary"  title="comment">Submit</button>
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                        </div>
                                                    </form>
                                                  </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="hidden" name="hot_lead_id" id="hot_lead_id{{$count}}" value="{{$lead->hot_lead_id}}">
                                            {{$lead->hot_leads_contact_no}}
                                        </td>
                                        <td>{{$lead->hot_leads_status}}</td>
                                        <td>{{$lead->username}}</td>
                                    </tr>
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
                        <select class="form-control" id="reasons" required>
                            <option disabled selected>--Select Reason*--</option>
                            <option>Not interested</option>
                            <option>Ringing</option>
                            <option>Not required</option>
                            <option>Will Confirm Later</option>
                            <option>Mobile Off</option>
                        </select>
                        <label for="desc">Remark</label>
                        <textarea class="form-control" rows="5" name="desc" id="desc"></textarea>
                    </div>
                    
                    <div class="modal-footer">
                        <a class="btn btn-secondary" id="close_hot_lead" onclick="getlink();" href="#" {{--style="pointer-events: none; color: #ccc;"--}} title="Close"><i class="fas fa-window-close"></i> Close lead</a>
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
                        <a class="btn btn-primary" id="add_hot_lead_comment" onclick="add_hot_lead_comment();" href="#" {{--style="pointer-events: none; color: #ccc;"--}} title="comment">Submit</a>
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
            document.getElementById('close_hot_lead').href += '/'+reason+'/'+desc;
        }

        function add_hot_lead_comment()
        {
            //var reason = $('#reasons').val();
            var desc = $('#cmt_desc').val();
            document.getElementById('add_hot_lead_comment').href += '/'+desc;
        }
        // $('#records tr').click(function() {    
        //     var count = this.dataset.count;
        //     // var cust_no = $('#cust_no'+count).text(); 
        //     //alert(count);   
        //     // var lead_id = $('#lead_id'+count).val();
        //     // var user_id = $('#user_id'+count).val();
        //     // //var desc = $('#desc').val();
        //     // document.getElementById('close_hot_lead').href ="<?php echo url('/');?>/close_hot_lead/"+user_id+"/"+lead_id;
        //     var customer_id = $('#customer_id'+count).val();
        //     var lead_id = $('#lead_id'+count).val();
        //     document.getElementById('close_hot_lead').href ="<?php echo url('/');?>/close_hot_lead/"+customer_id+"/"+lead_id;
        //     document.getElementById('add_hot_lead_comment').href ="<?php echo url('/');?>/add_hot_lead_comment/"+user_id+"/"+lead_id;
        //     // var url = "<?php echo url('/'); ?>/assign_vendor_byscript/"+customer_id+"/"+lead_id;
        //     // window.location.assign(url);
        // });
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
                var section = "In_Process";
                localStorage['filtered'] = filter_by;
                //alert(filter_by);
                var dataString = (filter_by);
                var url = "<?php echo url('/');?>/filterHotInprocessLeads/"+dataString;
                window.location.assign(url);
            })
            $('#records tr').click("input",function() {    
                var count = this.dataset.count;
                // var cust_no = $('#cust_no'+count).text(); 
                //alert(count);   
                var hot_lead_id = $('#hot_lead_id'+count).val();
                document.getElementById('add_hot_lead_comment').href ="<?php echo url('/');?>/add_hot_lead_comment/"+hot_lead_id;
                document.getElementById('close_hot_lead').href ="<?php echo url('/');?>/close_hot_lead/"+hot_lead_id;
                // var url = "<?php echo url('/'); ?>/assign_vendor_byscript/"+customer_id+"/"+lead_id;
                // window.location.assign(url);
            });
        });
        $('#select_date_filter').on('change', function(){
            window.location.assign(this.value);
        });
    </script>
    @endsection
</html>