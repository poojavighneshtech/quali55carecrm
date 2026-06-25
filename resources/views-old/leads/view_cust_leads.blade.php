<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Inquiry : customer All Leads</title>
    <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script>
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
            <form action="" name="lead" method="post">
                <center><a class="btn btn-primary" href="<?php echo url('/')?>/create_lead">Create New Lead</a></center>
                <table id="records" class="table table-bordered table-responsive" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Sr. No</th>
                            <th>Date&emsp;&emsp;&emsp;</th>
                            <th>Customer Name</th>
                            <th>Mobile Number</th>
                            <th>Equipment</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Lead Owner</th>
                            <th>Action&emsp;&emsp;&emsp;</th>
                        </tr>
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
                                        //if($lead_detail['creation_date']<=$past_three_days && $lead_detail['lead_status'] =='Work In Process'){echo "background-color: #adfffb;";}elseif($lead_detail['lead_status']== 'Closed'){echo "background-color: #fdedbe;";}elseif($lead_detail['creation_date']>=$past_three_days){echo "background-color: #8fffa8;";}
                                      //  if($lead_detail['lead_status'] =='Work In Process'){echo "background-color: #8fffa8;";}elseif($lead_detail['lead_status']== 'Closed'){echo "background-color: #fdedbe;";}elseif($lead_detail['lead_status'] == 'Converted'){echo "background-color:#adfffb;";}
                                    ?>
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
                                echo "<td>".date('d-M-Y',strtotime($lead_detail['creation_date']))."</td>";
                                //echo "<td>".$lead_detail['customer_name']."</td>";
                                echo "<td><a href='".url('/')."/view_lead/".$lead_detail['customer_id']."/".$lead_detail['id']."'title='View Details'>".$lead_detail['customer_name']."</a></td>";
                               // echo "<td>".$lead_detail['primary_contact_no']."</td>";
                                echo "<td>";?>
                                    <input type="hidden" name="customer_id" id="customer_id<?php echo $count;?>" value="<?php echo $lead_detail['customer_id']; ?>">
                                    <input type="hidden" name="lead_id" id="lead_id<?php echo $count;?>" value="<?php echo $lead_detail['id']; ?>">
                                    {{-- <label for="" id="cust_no<?php echo $count;?>" name="cust_no" value="<?php echo $lead_detail['customer_id'];?>"><?php echo $lead_detail['primary_contact_no'];?></label>  --}}
                                    <a href="<?php echo url('/');?>/view_lead/<?php echo $lead_detail['customer_id']?>/<?php echo $lead_detail['id'];?>" title="View Details"><?php echo $lead_detail['primary_contact_no'];?></a>
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
                                    <td>{{$lead_detail['username']}}</td>
                                <?php
                                echo "<td><center><a class='btn btn-primary' href='".url('/')."/view_lead/".$lead_detail['customer_id']."/".$lead_detail['id']."'title='View Details'>View Details</a> </center></td>";
                                echo "</tr>";
                                $count =$count+1;

                            } 

                        ?>
                    </tbody>
                </table>
            </form>
                
        </div>
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
            $('#filter').on("change",function(){
                var filter_by = $('#filter').val();
                //alert(filter_by);
                var dataString = (filter_by);
                var url = "<?php echo url('/');?>/filterLeads/"+dataString;
                window.location.assign(url);
                // $.ajax({
                //     type: "GET",
                //     url: "<?php echo url('/');?>/filterLeads/"+dataString,
                //     cache: false,
                //     success: function(data){
                //         console.log(data);
                //         //$('#container').html(data.html);
                //     }
                // })
            });
            // $('#records tr').click("input",function() {    
            //     var count = this.dataset.count;
            //     // var cust_no = $('#cust_no'+count).text(); 
            //     // alert(cust_no);   
            //     var customer_id = $('#customer_id'+count).val(); 
            //     var lead_id = $('#lead_id'+count).val(); 
            //     var url = "<?php echo url('/'); ?>/assign_vendor_byscript/"+customer_id+"/"+lead_id;
            //     window.location.assign(url);
            // });
        });
            
    

    </script>
    @endsection
</html>