<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Closed Leads</title>
    {{-- <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script> --}}
    @section('styles')
        <style>
            .morecontent span {
                display: none;
            }
            .morelink {
                display: block;
            }
            .row_scroll {
                overflow-x: scroll;
                overflow-y: hidden;
                white-space:nowrap;
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
            @if(session()->has('message_search'))
            <div class="alert alert-danger">
                {{ session()->get('message_search') }}
            </div>
        @endif 
            
                <div class="card">
                    <div class="card-header"  style="background-color: #4a6fdc; color: white;" >
                        <center>Closed Leads</center>
                    </div> 
                    <div class="card-body">
                        <center>
                            <a class="btn btn-primary" href="<?php echo url('/')?>/create_lead">Create New Lead</a>
                            <button type="button" id="export" class="btn btn-primary">Export To Excel</button>
                        </center>
                        <form class="form" method="post" action="<?php echo url('/');?>/viewClosedLeads">
                            {{csrf_field()}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="start_date">From Date:</label>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="date" class="form-control" name="start_date" id="start_date" value="<?php if(isset($data['lead_details'])){echo "dasddad".$data['lead_details'][0]['start_date'];}?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 ">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="last_date">Last Date:</label>
                                    </div>
                                    <div class="col-md-6 text-left">
                                        <input type="date" class="form-control" name="last_date" id="last_date" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <center><button type = "submit" class="btn btn-primary" name="submit" id="submit">Submit</button></center>
                        <hr>
                        </form>
                        <div class="table">
                            <table id="records" class="table table-striped table-responsive" id="records">
                                <thead>
                                    <th>Sr. No</th>
                                    <th>Date & Time&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</th>
                                    <th>comment</th>
                                    <th>add comment</th>
                                    <th>Customer Name</th>
                                    <th>Mobile Number</th>
                                    <th>Equipment</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Comment</th>
                                    <th>Lead Owner</th>
                                </thead>
                                <tbody>
                                    <?php
                                        $count = 0;
                                            $past_three_days = date('Y-m-d',strtotime("-2 days"));  
                                            foreach($lead_details as $lead_detail)
                                            {
                                                $sr_no = $count+1;
                                                $products = json_decode($lead_detail['equipment_requirement']);
                                                //echo "<tr class='rows' data-count=".$count.">";
                                                ?>
                                                <tr class="rows" data-count="{{$count}}" style="
                                                    <?php 
                                                        //if($lead_detail['creation_date']<=$past_three_days && $lead_detail['lead_status'] =='Work In Process'){echo "background-color: #adfffb;";}elseif($lead_detail['lead_status']== 'Closed'){echo "background-color: #fdedbe;";}elseif($lead_detail['creation_date']>=$past_three_days && $lead_detail['lead_status'] != 'Converted'){echo "background-color: #8fffa8;";}elseif($lead_detail['lead_status'] == 'Converted'){echo "background-color:#adfffb;";}
                                                        if($lead_detail['lead_status'] =='Work In Process')
                                                        {
                                                            echo "background-color: #8fffa8;";
                                                        }
                                                        elseif($lead_detail['lead_status']== 'Closed')
                                                        {
                                                            echo "background-color: #fdedbe;";
                                                        }
                                                        elseif($lead_detail['lead_status'] == 'Converted')
                                                        {
                                                            echo "background-color:#adfffb;";
                                                        }
                                                        else
                                                        {
                                                            echo "background-color: #fdedbe;";
                                                        }
                                                    ?>
                                                    ">
                                                <?php
                                                echo "<td>".$sr_no."</td>";
                                                //echo "<td>".date('d-M-Y',strtotime($lead_detail['created_at']))."</td>";
                                                echo "<td>".date('d-M-Y h:i',strtotime($lead_detail['created_at']))."</td>";
                                                ?>
                                                <td>
                                                    <div class="more">
                                                        {{$lead_detail['comment']}}
                                                    </div>
                                                <a class="btn btn-sm" href="#my_modal" data-toggle="modal" data-book-id="{{$lead_detail['comment']}}">...</a>
                                                </td>
                                                <td>
                                                    {{-- <a class="btn btn-danger btn-block" href="#comment_modal" role="button" data-toggle="modal" data-target="#comment_modal" data-book-id="{{$lead_details['jd_leads_id']}}" title="Comment"><i class="far fa-comment-alt fa-lg"></i></a> --}}
                                                    <button class="btn btn-success" src="#comment_modal"  data-toggle="modal" data-target="#comment_modal" title="Add Comment"><i class="far fa-comment-alt fa-lg"></i></button>
                                                </td>
                                                <?php
                                                //echo "<td>".$lead_detail['customer_name']."</td>";
                                                echo "<td><a href='".url('/')."/leads_view_lead/".$lead_detail['customer_id']."/".$lead_detail['id']."'title='View Details'>".$lead_detail['customer_name']."</a></td>";
                                            // echo "<td>".$lead_detail['primary_contact_no']."</td>";
                                                echo "<td>";?>
                                                    <input type="hidden" name="customer_id" id="customer_id<?php echo $count;?>" value="<?php echo $lead_detail['customer_id']; ?>">
                                                    <input type="hidden" name="lead_id" id="lead_id<?php echo $count;?>" value="<?php echo $lead_detail['id']; ?>">
                                                    {{-- <label for="" id="cust_no<?php echo $count;?>" name="cust_no" value="<?php echo $lead_detail['customer_id'];?>"><?php echo $lead_detail['primary_contact_no'];?></label>  --}}
                                                    <a href="<?php echo url('/');?>/leads_view_lead/<?php echo $lead_detail['customer_id']?>/<?php echo $lead_detail['id'];?>" title="View Details"><?php echo $lead_detail['primary_contact_no'];?></a>
                                            <?php 
                                                echo"</td>";
                                                echo "<td>".implode(", ",$products)."</td>";
                                                echo "<td>".$lead_detail['location']."</td>";
                                                //echo "<td>".$lead_detail['lead_status']."</td>";
                                                ?>
                                                    <td>
                                                        <?php 
                                                            if($lead_detail['lead_status']=='Work In Process')
                                                            {
                                                                echo "In Process";
                                                            }
                                                            else
                                                            {
                                                                echo $lead_detail['lead_status'];
                                                            }
                                                        ?>
                                                    </td>
                                                    <td>{{$lead_detail['remark']}}</td>
                                                    <td>
                                                        {{$lead_detail['username']}}
                                                        <input type="hidden" name="user_id" id="user_id{{$count}}" value="{{session('user_id')}}">
                                                    </td>
                                                <?php
                                                echo "</tr>";
                                                $count =$count+1;
                                            } 
                                        ?>
                                </tbody>
                            </table>
                        </div>
                         {{--Modals for comment--}}
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
                                        <a class="btn btn-primary" id="add_lead_comment" onclick="add_lead_comment();" href="#" {{--style="pointer-events: none; color: #ccc;"--}} title="comment">Submit</a>
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
                        {{--close Modals for comment--}}
                    </div>
                </div>
            
        </div>
    @endsection
</body>
@section('script')
<script>
    //  $(document).ready(function(){
    //         $("#search").on('input',function(){
    //             var search = $("#search").val();
    //             if(search == 'today' || search == 'Today')
    //             {
    //                 var newRow ="<tr><td>ahshshshs</td>";
    //               $('#records tbody').append(newRow);
    //             }
    //         })
    //         $('#records_filter').append("<label>Filter: <select class='form-control-sm' id='filter' name='filter'><option disabled selected>-----Select-----</option><option value='today'>Today</option><option value='yesterday'>Yesterday</option><<option value='past_3_days'>Past 3 Days</option><option value='week'>1 Week</option><option value='month'>Current Month</option><option value='all'>All</option></select></label>"); 
    //         if(localStorage['filtered'] != null)
    //         {
    //             $('#filter').val(localStorage['filtered']);
    //         }
    //         $('#filter').on("change",function(){
    //             var filter_by = $('#filter').val();
    //             localStorage['filtered'] = filter_by;
    //             //alert(filter_by);
    //             var dataString = (filter_by);
    //             var url = "<?php echo url('/');?>/filterLeads/"+dataString;
    //             window.location.assign(url);
    //         });
    //         $('#records tr').click("input",function() {    
    //             var count = this.dataset.count;
    //             // var cust_no = $('#cust_no'+count).text(); 
    //             //alert(count);   
    //             var customer_id = $('#customer_id'+count).val();
    //             var lead_id = $('#lead_id'+count).val();
    //             document.getElementById('close_lead').href ="<?php echo url('/');?>/close_lead/"+customer_id+"/"+lead_id;
    //             // var url = "<?php echo url('/'); ?>/assign_vendor_byscript/"+customer_id+"/"+lead_id;
    //             // window.location.assign(url);
    //         });
    //         // $('select').on('change', function(){
    //         //     var reason = $(this).val();
    //         //     //alert(reason);
    //         //     document.getElementById('close_lead').href += '/'+reason;
    //         // })
    //     });
    function getlink()
    {
        var reason = $('#reasons').val();
        var desc = $('#desc').val();
        //document.getElementById('close_jd_lead').href += '/'+reason+'/'+desc;
    }

    function add_lead_comment()
    {
        //var reason = $('#reasons').val();
        var desc = $('#cmt_desc').val();
        document.getElementById('add_lead_comment').href += '/'+desc;
    }
    $('#records tr').click(function() {    
        var count = this.dataset.count;
        // var cust_no = $('#cust_no'+count).text(); 
        //alert(count);   
        var lead_id = $('#lead_id'+count).val();
        var user_id = $('#user_id'+count).val();
        //var desc = $('#desc').val();
        //document.getElementById('close_jd_lead').href ="<?php echo url('/');?>/close_jd_lead/"+user_id+"/"+lead_id;
        document.getElementById('add_lead_comment').href ="<?php echo url('/');?>/add_lead_comment/"+user_id+"/"+lead_id;
        // var url = "<?php echo url('/'); ?>/assign_vendor_byscript/"+customer_id+"/"+lead_id;
        // window.location.assign(url);
    });
           $(document).ready(function() {
            var table = $('#records').DataTable();
            
            $('#export').on('click', function(){
                $('<table>').append(table.$('tr').clone()).table2excel({
                    //exclude: ".excludeThisClass",
                    //name: "abc",
                    filename: "AllLeads-{{date('d-m-Y')}}"
                });
            }); 
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
        var table = $('#records').DataTable();    
        $('#export').on('click', function(){
            $('<table>').append(table.$('tr').clone()).table2excel({
                //exclude: ".excludeThisClass",
                //name: "abc",
                filename: "AllClosedLeads-{{date('d-m-Y')}}"
            });
        });
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
            var section = "Closed";
            localStorage['filtered'] = filter_by;
            //alert(filter_by);
            var dataString = (filter_by);
            var url = "<?php echo url('/');?>/filterClosedLeads/"+dataString;
            window.location.assign(url);
        });
        $('#records tr').click("input",function() {    
            var count = this.dataset.count;
            // var cust_no = $('#cust_no'+count).text(); 
            //alert(count);   
            var customer_id = $('#customer_id'+count).val();
            var lead_id = $('#lead_id'+count).val();
            document.getElementById('close_lead').href ="<?php echo url('/');?>/close_lead/"+customer_id+"/"+lead_id;
            // var url = "<?php echo url('/'); ?>/assign_vendor_byscript/"+customer_id+"/"+lead_id;
            // window.location.assign(url);
        });
        // $('select').on('change', function(){
        //     var reason = $(this).val();
        //     //alert(reason);
        //     document.getElementById('close_lead').href += '/'+reason;
        // })
    });
</script>
    @endsection
</html>