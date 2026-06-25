@extends('header_and_sidebar')
<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
    
        <title>All Leads</title>
        {{-- <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script> --}}
        @section('styles')
            
        @endsection
    </head>

<body id="page-top">	
		<!-- Page Wrapper -->
        
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
            
                <div class="card">
                    <div class="card-header"  style="background-color: #4a6fdc; color: white;" >
                        <center>All Leads</center>
                    </div> 
                    <div class="card-body">
                            <center>
                                <a class="btn btn-primary" href="<?php echo url('/')?>/create_lead">Create New Lead</a>
                                <button type="button" id="export" class="btn btn-primary">Export To Excel</button>
                            </center>
                            <br id="break_datewise" style="@if(isset($start_date)){{'display: inline;'}}@else{{'display: none;'}}@endif">
                                <div class="row" id="datewise_search" style="@if(isset($start_date)){{'display: inline;'}}@else{{'display: none;'}}@endif">                                
                                    <div class="col-md-6">
                                        <form action="{{url('/')}}/leads_datewise_search" method="post">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <input type="date" class="form-control" name="start_date" id="start_date" value="@if(isset($start_date)){{$start_date}}@else{{date('Y-m-d')}}@endif" required>
                                                </div>
                                                <div class="col-md-1">
                                                    <strong>To</strong>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="date" class="form-control" name="end_date" id="end_date" value="@if(isset($end_date)){{$end_date}}@else{{date('Y-m-d')}}@endif" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <button type="submit" class="form-control btn btn-outline-primary" id="btn_date_search_customer" name="btn_search" value="date_search">Search</button>
                                                </div>
                                            </div>    
                                        </form>
                                    </div>
                                </div>                            
                            <table id="records" class="table table-bordered table-responsive" data-page-length='250' style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>Sr. No</th>
                                        <th>Date & Time&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</th>
                                        <th>Customer Name&emsp;&emsp;</th>
                                        <th>Mobile Number</th>
                                        <th>Equipment&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Lead Source</th>
                                        <th>Lead Owner</th>
                                        <th>Action&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(isset($lead_details[0]['equipment_requirement'])){?>
                                        <tr class="row_scroll" style="display: none">
                                            <td>1</td>
                                            <td>Date & Time</td>
                                            <td>Customer Name</td>
                                            <td>Mobile Number</td>
                                            <td>Equipment</td>
                                            <td>Location</td>
                                            <td>Status</td>
                                            <td>Lead Source</td>
                                            <td>Lead Owner</td>
                                            <td>-</td>
                                        </tr>
                                        <?php } ?>
                                    <?php
                                    $count = 0;
                                        $past_three_days = date('Y-m-d',strtotime("-2 days"));  
                                        foreach($lead_details as $lead_detail)
                                        {
                                            $sr_no = $count+1;
                                            $products = json_decode($lead_detail['equipment_requirement']);
                                            //echo "<tr class='rows' data-count=".$count.">";
                                            ?>
                                            <tr class="rows row_scroll" data-count="{{$count}}" style="
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
                                                    elseif($lead_detail['lead_status'] == 'Converted' || $lead_detail['lead_status'] == 'Order Generated')
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
                                            echo "<td>".date('d-M-Y',strtotime($lead_detail['creation_date']))." ".date('H:i',strtotime($lead_detail['created_at']))."</td>";
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
                                                <td>{{$lead_detail['lead_source']}}</td>
                                                <td>{{$lead_detail['username']}}</td>
                                            <?php
                                            if($lead_detail['lead_status']=='Converted')
                                            {
                                                //echo "<td><a class='btn btn-sm btn-primary' href='".url('/')."/leads_view_lead/".$lead_detail['customer_id']."/".$lead_detail['id']."'title='View Details'><i class='fas fa-info-circle'></i></a></td>";
                                                echo "<td><a class='btn btn-sm btn-primary' href='".url('/')."/leads_view_lead/".$lead_detail['customer_id']."/".$lead_detail['id']."'title='View Details'><i class='fas fa-info-circle'></i></a> <a class='btn btn-sm btn-primary' href='".url('/')."/edit_lead/".$lead_detail['customer_id']."/".$lead_detail['id']."' title='Edit'><i class='far fa-edit'></i></a></td>";
                                            }
                                            elseif ($lead_detail['lead_status']=='Closed' OR $lead_detail['lead_status']=='Vendor Assigned' OR $lead_detail['lead_status']=='Order Generated' OR $lead_detail['lead_status']=='Delivery In Progress' OR $lead_detail['lead_status'] != 'Work In Process') 
                                            {
                                                echo "<td><a class='btn btn-sm btn-primary' href='".url('/')."/leads_view_lead/".$lead_detail['customer_id']."/".$lead_detail['id']."'title='View Details'><i class='fas fa-info-circle'></i></a></td>";
                                            }
                                            else
                                            {?>
                                                <td>
                                                    
                                                        <a class="btn btn-sm btn-secondary" href="<?php echo url('/');?>/convert_lead/<?php echo $lead_detail['customer_id'].'/'.$lead_detail['id'];?>" title="Convert"><i class="fas fa-check-circle"></i></a>
                                                        <a class="btn btn-sm btn-primary" href="<?php echo url('/');?>/edit_lead/<?php echo $lead_detail['customer_id'].'/'.$lead_detail['id'];?>" title="Edit"><i class="far fa-edit"></i></a>
                                                        <?php if(session('role') =='admin'){?> 
                                                            <a class="btn btn-sm btn-danger" href="<?php echo url('/');?>/delete_lead/<?php echo $lead_detail['id'];?>"><i class="fas fa-trash-alt"></i></a>
                                                        <?php } ?> 
                                                        {{-- <a class="btn btn-sm btn-secondary" href="<?php echo url('/');?>/close_lead/<?php echo $lead_detail['customer_id'].'/'.$lead_detail['id'];?>" title="Close"><i class="fas fa-window-close"></i></a> --}}
                                                        <button class="btn btn-sm btn-secondary" type="button" data-toggle="modal" data-target="#myModal" title="Close"><i class="fas fa-window-close"></i></button>
                                                    
                                                </td>
                                            <?php
                                            }
                                            echo "</tr>";
                                            $count =$count+1;

                                        } 

                                    ?>
                                </tbody>
                            </table>
                    </div>
                </div>
            
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
    @endsection
</body>
@section('script')
    <script>
        function getlink()
        {
            var reason = $('#reasons').val();
            var desc = $('#desc').val();
            document.getElementById('close_lead').href += '/'+reason+'/'+desc;

        }
        $(document).ready(function()
        {
            var table = $('#records').DataTable();
            
            $('#export').on('click', function(){
                $('<table>').append(table.$('tr').clone()).table2excel({
                    //exclude: ".excludeThisClass",
                    //name: "abc",
                    filename: "AllLeads-{{date('d-m-Y')}}"
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
            $('#records_filter').append("<label>Filter: <select class='form-control-sm' id='filter' name='filter'><option disabled selected>-----Select-----</option><option value='today'>Today</option><option value='yesterday'>Yesterday</option><<option value='past_3_days'>Past 3 Days</option><option value='week'>1 Week</option><option value='month'>Current Month</option><option value='all'>All</option><option value='datewise'>Datewise</option></select></label>"); 
            if(localStorage['filtered'] != null)
            {
                $('#filter').val(localStorage['filtered']);
            }
            $('#filter').on("change",function(){
                var filter_by = $('#filter').val();
                var section = "All_Leads";
                localStorage['filtered'] = filter_by;
                //alert(filter_by);
                if(filter_by != 'datewise')
                {
                    $('#datewise_search').hide();
                    $('#break_datewise').hide();
                    var dataString = (filter_by);
                    var url = "<?php echo url('/');?>/filterNormalLeads/"+dataString;
                    window.location.assign(url);
                }
                else if(filter_by == 'datewise')
                {
                    $('#break_datewise').show();
                    $('#datewise_search').show();
                }
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