<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Closed Referrer</title>
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
                        <center>Closed Referrer</center>
                    </div> 
                    <div class="card-body">
                        <form class="form" method="post" action="<?php echo url('/');?>/viewConvertedLeads">
                            {{csrf_field()}}
                        </form>
                        <div class="table">
                            <table class="table table-responsive" id="records">
                                <thead>
                                    <tr>
                                        <th>Sr.No</th>
                                        {{-- <th>Action</th> --}}
                                        <th>Comment</th>
                                        {{-- <th>Add Comment</th> --}}
                                        <th>Name</th>
                                        <th>Mobile Number</th>                                        
                                        <th>Profession</th>
                                        <th>Status</th>
                                        <th>Referrer Owner</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $count = 0;    
                                    @endphp
                                    @foreach ($nurses_data as $nurse_data)
                                        @php
                                            $count = $count+1;
                                        @endphp
                                        <tr data-count="{{$count}}">
                                            <td>{{$count}}</td>
                                            
                                            <td>
                                                <div class="more">
                                                    {{$nurse_data['comment']}}
                                                </div>
                                                <a class="btn btn-sm" href="#my_modal" data-toggle="modal" data-book-id="{{$nurse_data['comment']}}">...</a>
                                            </td>
                                            
                                            <td>{{$nurse_data['name']}}</td>
                                            <td>{{$nurse_data['primary_contact']}}</td>
                                            <td>{{$nurse_data['profession']}}</td>
                                            <td>{{$nurse_data['status']}}</td>
                                            <td>{{$nurse_data['username']}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                
                            </table>
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
                                <a class="btn btn-primary" id="add_nurse_comment" onclick="add_nurse_comment();" href="#" {{--style="pointer-events: none; color: #ccc;"--}} title="comment">Submit</a>
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

        function add_nurse_comment()
        {
            //var reason = $('#reasons').val();
            var desc = $('#cmt_desc').val();
            document.getElementById('add_nurse_comment').href += '/'+desc;
        }
        $('#records tr').click(function() {    
            var count = this.dataset.count;
            // var cust_no = $('#cust_no'+count).text(); 
            //alert(count);   
            var nurse_id = $('#nurse_id'+count).val();
            var user_id = $('#user_id'+count).val();
            //var desc = $('#desc').val();
            document.getElementById('add_nurse_comment').href ="<?php echo url('/');?>/add_nurse_comment/"+user_id+"/"+nurse_id;
            // var url = "<?php echo url('/'); ?>/assign_vendor_byscript/"+customer_id+"/"+nurse_id;
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
</script>
@endsection
</html>