
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Inquiry : View Lead Details</title>
	<script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script>
</head>

<body id="page-top">	
	@extends('header_and_sidebar')
       
	@section('content')
		
        <div class="card">
            <div class="card-header" style="background-color: #337ab7; color: white;">
                <center>
                    <b>Converted Inquiry Details</b>
                </center>
            </div>
            <form class="form" action="<?php echo url('/');?>/assign_vendor" method="post">
                {{ csrf_field() }}
                <div class="card-body">
                    <input type="hidden" name="customer_id" id="customer_id" value="<?php echo $lead_details[0]['customer_id'];?>"></input>
                    <input type="hidden" name="lead_id" id="lead_id" value="<?php echo $lead_details[0]['id'];?>">
                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            <center><b><h3>Status : <?php if ($lead_details[0]['lead_status'] == 'Work In Process'){echo "In Process";}else{echo $lead_details[0]['lead_status'];}?></h3></b></center>
                        </div>
                    </div> 
                    <hr> 
                    <div class="row">
                        <div class="col-auto">
                            <strong>Customer Name :</strong>  &emsp;<span>{{$lead_details[0]['customer_name']}}</span>
                        </div>
                        <div class="col-auto">
                            <strong>Mobile No:</strong> &emsp;<span>{{$lead_details[0]['primary_contact_no']}}</span>
                        </div>
                        <div class="col-auto">
                            <strong>Location :</strong>
                            &emsp;<span>{{$lead_details[0]['location']}}</span>
                        </div>
                        <div class="col-auto">
                            <strong>Gender :</strong>
                            &emsp;
                            <span>
                                @if(isset($lead_details[0]['cust_gender']))
                                    {{$lead_details[0]['cust_gender']}}
                                @else 
                                    - 
                                @endif
                            </span>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-auto">
                            <strong>Patient Name:</strong>
                            &emsp;{{$lead_details[0]['patient_name']}}</div>
                        <div class="col-auto "><strong>Patient Age:</strong> &emsp;{{$lead_details[0]['patient_age']}}</div>
                        <div class="col-auto"><strong>Gender:</strong> &emsp;@if(isset($lead_details[0]['patient_gender'])){{$lead_details[0]['patient_gender']}}@else - @endif</div>
                    </div>
                    <hr>
                    <div class="row">                        
                        {{-- DESKTOP table --}}
                        <div class="table table-responsive jim-table-responsive">
                            <strong>Equipment:</strong>
                            <?php 
                                if(isset($lead_details[0]['deposite']) AND $lead_details[0]['lead_status']=='Converted' OR $lead_details[0]['lead_status']=='Closed' OR $lead_details[0]['lead_status']=='Vendor Assigned' OR $lead_details[0]['lead_status']=='Order Generated' OR $lead_details[0]['lead_status']=='Delivery In Progress')
                                {
                            ?>
                                <table class="table table-bordered" id="equipment_table">
                                    <thead>
                                        <th>Sr. No.</th>
                                        <th>Delivery Date</th>
                                        <th>Equipment Name</th>
                                        <th>Qty</th>
                                        <th>Months</th>
                                        <th>Deposite</th>
                                        <th>Total Deposit</th>
                                        <th>Offered Rent</th>
                                        <th>Total Rent</th>
                                        <th>Transport</th>
                                        <th>Sale / Rental</th>
                                    </thead>
                                    <tbody class="table-body">
                                        <?php
                                            $count = 0;
                                            $products = json_decode($lead_details[0]['equipment_requirement']);
                                            $del_dates = json_decode($lead_details[0]['del_date']);
                                            $qtys = json_decode($lead_details[0]['equipment_qty']);
                                            $months = json_decode($lead_details[0]['months']);
                                            $deposites = json_decode($lead_details[0]['deposite']);
                                            $offered_rents = json_decode($lead_details[0]['offered_rent']);
                                            $deposites_total = json_decode($lead_details[0]['deposite_total']);
                                            $offered_rents_total = json_decode($lead_details[0]['offered_rent_total']);
                                            $transports = json_decode($lead_details[0]['transport']);
                                            $sale_rental = json_decode($lead_details[0]['sale_rental']);
                                            $total = 0;
                                            for($i=0; $i<count($products); $i++) 
                                            {
                                                $count++;
                                                $total = $total + $deposites_total[$i] + $offered_rents_total[$i] + $transports[$i];
                                        ?>
                                                <tr id="{{$count}}">
                                                    <td data-label="Sr.No.">{{$count}}</td>
                                                    <td data-label="Del Date">{{date('d-m-Y',strtotime($del_dates[$i]))}}</td>
                                                    <td data-label="Equipment Name">{{$products[$i]}}</td>
                                                    <td data-label="Qty">{{$qtys[$i]}}</td>
                                                    <td data-label="Months">{{$months[$i]}}</td>
                                                    <td data-label="Deposit">{{$deposites[$i]}}</td>
                                                    <td data-label="Total Deposit">{{$deposites_total[$i]}}</td>
                                                    <td data-label="Offered Rent">{{$offered_rents[$i]}}</td>
                                                    <td data-label="Total Rent">{{$offered_rents_total[$i]}}</td>
                                                    <td data-label="Transport">{{$transports[$i]}}</td>
                                                    <td data-label="Sale / Rental">{{$sale_rental[$i]}}</td>
                                                </tr>
                                        <?php   
                                            } 
                                        ?>
                                        <tr>
                                            <td colspan="6" class="">Total </td>
                                            <td colspan="6">{{$total}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            <?php
                                }
                                else
                                {
                            ?>
                                <div class="row form-group">
                                    <div class="col-6 col-md-4">
                                        <label for="eqipments">Equipment Required</label>
                                    </div>
                                    <div class="col-6 col-md-8">
                                        <?php $products = json_decode($lead_details[0]['equipment_requirement']); echo implode(", ",$products);?>
                                    </div>
                                </div>
                            <?php
                                }
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header text-center"> <strong>Additional Info</strong> </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6 col-md-6 ">Payment Mode :</div>
                                        <div class="col-6 col-md-6 ">{{$lead_details[0]['payment_mode']}}</div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-6 col-md-6 ">Reffered By :</div>
                                        <div class="col-6 col-md-6 ">{{$lead_details[0]['referredby']}}</div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-6 col-md-6 ">Lead Source :</div>
                                        <div class="col-6 col-md-6 ">{{$lead_details[0]['lead_source']}}</div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-6 col-md-6 ">Lead Owner :</div>
                                        <div class="col-6 col-md-6 ">{{$lead_details[0]['username']}}</div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-6 col-md-6 ">Doctor Name :</div>
                                        <div class="col-6 col-md-6 ">{{$lead_details[0]['doctor_name']}}</div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-6 col-md-6 ">Hospital Name :</div>
                                        <div class="col-6 col-md-6 ">{{$lead_details[0]['hospital_name']}}</div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-6 col-md-6 ">Therapeutic Requirement :</div>
                                        <div class="col-6 col-md-6 ">{{$lead_details[0]['therapeutic_requirement']}}</div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-6 col-md-6 ">Comment :</div>
                                        <div class="col-6 col-md-6 ">{{$lead_details[0]['comment']}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header text-center"> <strong>Address</strong> </div>
                                <div class="card-body">
                                    @if($lead_details[0]['handovermode'] == 'pickup')
                                        <div class="row">
                                            <div class="col-12 col-md-12 text-center text-danger"><b>Self Pickup From Store</b></div>
                                        </div>
                                    @endif
                                    <div class="row">
                                        <div class="col-6 col-md-6 ">Line 1 :</div>
                                        <div class="col-6 col-md-6 ">{{$lead_details[0]['address_line_1']}}</div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-6 col-md-6 ">Line 2 :</div>
                                        <div class="col-6 col-md-6 ">{{$lead_details[0]['address_line_2']}}</div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-6 col-md-6 ">Landmark :</div>
                                        <div class="col-6 col-md-6 ">{{$lead_details[0]['landmark']}}</div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-6 col-md-6 ">Area :</div>
                                        <div class="col-6 col-md-6 ">{{$lead_details[0]['area']}}</div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-6 col-md-6 ">City :</div>
                                        <div class="col-6 col-md-6 ">{{$lead_details[0]['city']}}</div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-6 col-md-6 ">Pincode :</div>
                                        <div class="col-6 col-md-6 ">{{$lead_details[0]['pincode']}}</div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-6 col-md-6 ">State :</div>
                                        <div class="col-6 col-md-6 ">{{$lead_details[0]['state']}}</div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-6 col-md-6 ">State :</div>
                                        <div class="col-6 col-md-6 ">{{$lead_details[0]['country']}}</div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-6 col-md-6 ">Email id :</div>
                                        <div class="col-6 col-md-6 ">{{$lead_details[0]['email_id']}}</div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-6 col-md-6 ">Mobile No (Secondary):</div>
                                        <div class="col-6 col-md-6 ">{{$lead_details[0]['secondary_contact_no']}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <center>    
                        <?php 
                            if($lead_details[0]['lead_status'] == 'Work In Process')
                            {
                        ?>
                            <a class="btn btn-primary" href="<?php echo url('/');?>/edit_lead/<?php echo $lead_details[0]['customer_id'].'/'.$lead_details[0]['id'];?>" title="Edit"><i class="far fa-edit"></i>Edit Lead</a>
                            <a class="btn btn-secondary" href="<?php echo url('/');?>/convert_lead/<?php echo $lead_details[0]['customer_id'].'/'.$lead_details[0]['id'];?>" title="Convert"><i class="fas fa-check-circle"></i>Convert Lead</a>
                        <?php
                            }
                            elseif($lead_details[0]['lead_status'] == 'Converted')
                            {
                        ?>
                                <a class="btn btn-primary" href="<?php echo url('/');?>/edit_lead/<?php echo $lead_details[0]['customer_id'].'/'.$lead_details[0]['id'];?>" title="Edit"><i class="far fa-edit"></i>Edit Lead</a>
                        <?php
                            }
                        ?>
                    </center>
                </div>
            </form>
            
        </div>
		
	@endsection
</body>

</html>
