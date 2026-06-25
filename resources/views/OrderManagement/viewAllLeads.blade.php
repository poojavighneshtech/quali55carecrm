<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Inquiry : Orders</title>
    <style>
        .row_scroll {
                
                white-space:nowrap;
                /* line-break: auto; */
            }
    </style>
</head>

<body id="page-top">	
		<!-- Page Wrapper -->
    @extends('header_and_sidebar')
       
    @section('content')
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
                <center>Orders</center>
            </div> 
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        {{-- <form action="{{url('/')}}/date_filter_order_mgmt" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="date" class="form-control" name="start_date" id="start_date" required>
                                </div>
                                to
                                <div class="col-md-4">
                                    <input type="date" class="form-control" name="end_date" id="end_date" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="submit" class="btn btn-outline-primary btn-sm btn-block" name="submit" id="submit" value="Submit">
                                </div>
                            </div>    
                        </form> --}}
                        <form action="{{url('/')}}/order_mgmt_filter_post" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-2">
                                    <label for="name"><strong>Name :</strong></label>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="name" id="txt_name" placeholder="Search Name..." value="@if(isset($post_name)){{$post_name}}@endif">
                                </div>
                                <div class="col-md-4">
                                    <input type="submit" class="btn btn-outline-success btn-sm btn-block" value="Submit">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-5">
                        <div class="row">
                            <div class="col-md-4">Customers <span class="badge badge-secondary">{{$total_customer}}</span></div>
                            <div class="col-md-3">Products</div>
                            <div class="col-md-1 text-left">
                                {{-- <a  type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></a> --}}
                                <a  type="button" class="" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{$product_count}}</a>
                                <!--Menu-->
                                <div class="dropdown-menu dropdown-primary">
                                  <a class="dropdown-item" href="#">Rent &emsp;<strong>{{$total_rent_product}}</strong></a>
                                  <a class="dropdown-item" href="#">Sale &emsp;<strong>{{$total_sale_product}}</strong></a>
                                </div>
                            </div>
                            <div class="col-md-2">Amt</div>
                            <div class="col-md-1">
                                {{-- <a  type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></a> --}}
                                <a  type="button" class=""id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{$total_amount}}</a>
                                <!--Menu-->
                                <div class="dropdown-menu dropdown-primary">
                                  <a class="dropdown-item" href="#">Rent &emsp;<strong>{{$total_rent_amt}}</strong></a>
                                  <a class="dropdown-item" href="#">Sale &emsp;<strong>{{$total_sale_amt}}</strong></a>
                                  <a class="dropdown-item" href="#">Transport &emsp;<strong>{{$total_transport}}</strong></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <form action="{{url('/')}}/filterOrderLeads" method="post">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <label for="Filter"><strong>Filter :</strong></label>
                                        <div class="col-md-4">
                                            <input type="date" class="form-control" name="start_date" id="start_date" required>
                                        </div>
                                        <strong>To</strong> 
                                        <div class="col-md-4">
                                        <input type="date" class="form-control" name="end_date" id="end_date" required>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="submit" class="btn btn-outline-primary btn-sm btn-block" name="btn_date_search" value="Search">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    {{-- <div class="col-md-3 text-right">
                        <select class="form-control" name="lead_owner" id="select_lead_owners">  
                            <option value="All">all</option>
                            @foreach ($users as $key=>$user)
                                <option value="{{request()->fullUrlWithQuery(['user_id'=>$user['id']])}}" @if(isset($user_id) && $user_id==$user['id']) selected @endif>{{$user['username']}}</option>
                            @endforeach
                        </select>
                    </div> --}}
                </div>
                <div class="table">
                    <form action="" name="lead" method="post">
                        {{-- <center><a class="btn btn-primary" href="<?php echo url('/')?>/create_lead">Create New Lead</a></center> --}}
                        <table id="records" class="table table-bordered table-responsive" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>Sr.No</th>
                                    <th>Date&emsp;&emsp;&emsp;&emsp;</th>
                                    <th>Customer Name</th>
                                    <th>Mobile Number</th>
                                    <th>Equipment</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Lead Source</th>
                                    <th>Lead Owner</th>
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
                                        <tr class="rows row_scroll" data-count="{{$count}}" style="<?php if($lead_detail['creation_date']<=$past_three_days && $lead_detail['lead_status'] =='Work In Process'){echo "background-color: #adfffb;";}elseif($lead_detail['lead_status']== 'Closed'){echo "background-color: #fdedbe;";}if($lead_detail['creation_date']>=$past_three_days && $lead_detail['lead_status'] =='Work In Process'){echo "background-color: #8fffa8;";}?>">
                                        <?php
                                        echo "<td>".$sr_no."</td>";
                                        echo "<td>".date('d-M-Y',strtotime($lead_detail['creation_date']))."</td>";
                                        //echo "<td>".$lead_detail['customer_name']."</td>";
                                        echo "<td><a href='".url('/')."/order_view_lead/".$lead_detail['customer_id']."/".$lead_detail['id']."'title='View Details'>".$lead_detail['customer_name']."</a></td>";
                                        // echo "<td>".$lead_detail['primary_contact_no']."</td>";
                                        echo "<td>";?>
                                            <input type="hidden" name="customer_id" id="customer_id<?php echo $count;?>" value="<?php echo $lead_detail['customer_id']; ?>">
                                            <input type="hidden" name="lead_id" id="lead_id<?php echo $count;?>" value="<?php echo $lead_detail['id']; ?>">
                                            {{-- <label for="" id="cust_no<?php echo $count;?>" name="cust_no" value="<?php echo $lead_detail['customer_id'];?>"><?php echo $lead_detail['primary_contact_no'];?></label>  --}}
                                            <a href="<?php echo url('/');?>/order_view_lead/<?php echo $lead_detail['customer_id']?>/<?php echo $lead_detail['id'];?>" title="View Details"><?php echo $lead_detail['primary_contact_no'];?></a>
                                    <?php 
                                        echo"</td>";
                                        echo "<td>".implode(", ",$products)."</td>";
                                        echo "<td>".$lead_detail['location']."</td>";
                                        echo "<td>".$lead_detail['lead_status']."</td>";
                                    ?>
                                        <td>
                                            @if($lead_detail['priority']=='0')
                                                {{"High"}}
                                            @elseif($lead_detail['priority']=='1')
                                                {{"Normal"}}
                                            @else
                                                {{"Low"}}
                                            @endif
                                        </td>
                                        <td>{{$lead_detail['lead_source']}}</td>
                                        <td>{{$lead_detail['lead_owner']}}</td>
                                    <?php                                
                                        // if($lead_detail['lead_status']=='Converted'){
                                        //     echo "<td><a class='btn btn-sm btn-primary' href='".url('/')."/order_view_lead/".$lead_detail['customer_id']."/".$lead_detail['id']."'title='View Details'><i class='fas fa-info-circle'></i></a> <a class='btn btn-sm btn-primary' href='".url('/')."/edit_lead/".$lead_detail['customer_id']."/".$lead_detail['id']."' title='Edit'><i class='far fa-edit'></i></a>";
                                        // }
                                        // else
                                        // {
                                                ?>
                                                {{-- <td> --}}
                                                {{-- <a class="btn btn-sm btn-secondary" href="<?php echo url('/');?>/convert_lead/<?php echo $lead_detail['customer_id'].'/'.$lead_detail['id'];?>" title="Convert"><i class="fas fa-check-circle"></i></a> <a class="btn btn-sm btn-primary" href="<?php echo url('/');?>/edit_lead/<?php echo $lead_detail['customer_id'].'/'.$lead_detail['id'];?>" title="Edit"><i class="far fa-edit"></i></a> <?php if(session('role') =='admin'){?><a class="btn btn-sm btn-danger" href="<?php // echo url('/');?>/delete_lead/<?php // echo $lead_detail['id'];?>"><i class="fas fa-trash-alt"></i></a><?php } ?></td> --}}
                                        <?php
                                        // }
                                        echo "</tr>";
                                        $count =$count+1;
        
                                    } 
        
                                ?>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        <div>
    @endsection
</body>
    @section('script')
        <script>
        $(document).ready(function()
        {    
            $('#records_filter').append("<label>Filter: <select class='form-control-sm' id='filter' name='filter'><option disabled selected>-----Select-----</option><option value='today'>Today</option><option value='yesterday'>Yesterday</option><<option value='past_3_days'>Past 3 Days</option><option value='week'>1 Week</option><option value='month'>Current Month</option><option value='all'>All</option></select></label>"); 
            if(localStorage['filtered'] != null)
            {
                $('#filter').val(localStorage['filtered']);
            }
            $('#filter').on("change",function(){
                var filter_by = $('#filter').val();
                var section = "All_Leads";
                localStorage['filtered'] = filter_by;
                //alert(filter_by);
                var dataString = (filter_by);
                var url = "<?php echo url('/');?>/filterOrderLeads/"+dataString;
                window.location.assign(url);
            });
            $('#select_lead_owners').on('change',function(){
                window.location.assign(this.value);
            });
            
            // $('#records tr').click("input",function() {    
            //     var count = this.dataset.count;
            //     // var cust_no = $('#cust_no'+count).text(); 
            //     //alert(count);   
            //     var customer_id = $('#customer_id'+count).val();
            //     var lead_id = $('#lead_id'+count).val();
            //     document.getElementById('close_lead').href ="<?php echo url('/');?>/close_lead/"+customer_id+"/"+lead_id;
            //     // var url = "<?php echo url('/'); ?>/assign_vendor_byscript/"+customer_id+"/"+lead_id;
            //     // window.location.assign(url);
            // });
            // $('select').on('change', function(){
            //     var reason = $(this).val();
            //     //alert(reason);
            //     document.getElementById('close_lead').href += '/'+reason;
            // })
        });
        </script>
    @endsection
</html>