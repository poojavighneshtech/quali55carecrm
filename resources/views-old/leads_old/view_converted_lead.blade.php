
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
	<div class="container">
		<br>
		<div class="row">
			<div class="col-md-12">
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
                                            {{-- <button  type="submit"  name="assign" id="assign" class="btn btn-primary btn-lg btn-block" disabled>Assign Vendor</button> --}}
                                            <center><b><h3>Status : <?php if ($lead_details[0]['lead_status'] == 'Work In Process'){echo "In Process";}else{echo $lead_details[0]['lead_status'];}?></h3></b></center>
                                        </div>
                                    </div>        
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="cust_name">Customer Name</label>
                                        </div>
                                        <div class="col-md-8">
                                            <span><?php echo $lead_details[0]['customer_name'];?></span>
                                        </div>
                                    </div>        
                                    
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="primary_contact_no">Mobile Number(Primary)</label>
                                        </div>
                                        <div class="col-md-8">
                                            <span><?php echo $lead_details[0]['primary_contact_no'];?></span>
                                        </div>
                                    </div>                                    
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="patient_name">Patient Name</label>
                                        </div>
                                        <div class="col-md-8">
                                            <span><?php echo $lead_details[0]['patient_name'];?></span>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="patient_age">Patient Age</label>
                                        </div>
                                        <div class="col-md-8">
                                            <span><?php echo $lead_details[0]['patient_age'];?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="location">Location</label>
                                        </div>
                                        <div class="col-md-8">
                                            <span><?php echo $lead_details[0]['location'];?></span>
                                        </div>
                                    </div>

                                    <?php 
                                        if(isset($lead_details[0]['deposite']) AND $lead_details[0]['lead_status']=='Converted' OR $lead_details[0]['lead_status']=='Closed' OR $lead_details[0]['lead_status']=='Vendor Assigned' OR $lead_details[0]['lead_status']=='Order Generated' OR $lead_details[0]['lead_status']=='Delivery In Progress')
                                        {
                                    ?>
                                            <div class="row form-group">
                                                <div class="col-md-2">
                                                    <label for="equipment">Equipment</label>
                                                </div>
                                                <div class="col-md-10">
                                                    <table class="table table-bordered table-responsive" id="equipment_table">
                                                        <thead>
                                                            <th>Sr. No.</th>
                                                            <th>Delivery Date</th>
                                                            <th>Equipment Name</th>
                                                            <th>Qty</th>
                                                            <th>Deposit</th>
                                                            <th>Total Deposit</th>
                                                            <th>Offered Rent</th>
                                                            <th>Total Rent</th>
                                                            <th>Transport</th>                                                
                                                        </thead>
                                                        <tbody class="table-body">
                                                            <?php
                                                                $count = 0;
                                                                $products = json_decode($lead_details[0]['equipment_requirement']);
                                                                $del_dates = json_decode($lead_details[0]['del_date']);
                                                                $qtys = json_decode($lead_details[0]['equipment_qty']);
                                                                $deposites = json_decode($lead_details[0]['deposite']);
                                                                $offered_rents = json_decode($lead_details[0]['offered_rent']);
                                                                $deposites_total = json_decode($lead_details[0]['deposite_total']);
                                                                $offered_rents_total = json_decode($lead_details[0]['offered_rent_total']);
                                                                $transports = json_decode($lead_details[0]['transport']);
                                                                $total = 0;
                                                                for($i=0; $i<count($products); $i++) 
                                                                {
                                                                    $count++;
                                                                    $total = $total + $deposites_total[$i] + $offered_rents_total[$i] + $transports[$i];
                                                            ?>
                                                                    <tr id="{{$count}}">
                                                                        <td>{{$count}}</td>
                                                                        <td>{{date('d-m-Y',strtotime($del_dates[$i]))}}</td>
                                                                        <td>{{$products[$i]}}</td>
                                                                        <td>{{$qtys[$i]}}</td>
                                                                        <td>{{$deposites[$i]}}</td>
                                                                        <td>{{$deposites_total[$i]}}</td>
                                                                        <td>{{$offered_rents[$i]}}</td>
                                                                        <td>{{$offered_rents_total[$i]}}</td>
                                                                        <td>{{$transports[$i]}}</td>
                                                                    </tr>
                                                            <?php   
                                                                } 
                                                            ?>
                                                            <tr>
                                                                <td colspan="6" class="text-right">Total </td>
                                                                <td colspan="6">{{$total}}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                    <?php
                                        }
                                        else
                                        {
                                    ?>
                                            <div class="row form-group">
                                                <div class="col-md-4">
                                                    <label for="eqipments">Equipment Required</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <?php $products = json_decode($lead_details[0]['equipment_requirement']); echo implode(", ",$products);?>
                                                </div>
                                            </div>
                                    <?php
                                        }
                                    ?>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="payment_mode">Payment Mode</label>
                                        </div>
                                        <div class="col-md-8">
                                            <span><?php echo $lead_details[0]['payment_mode'];?></span>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="refered_by">Refered by</label>
                                        </div>
                                        <div class="col-md-8">
                                            <span><?php echo $lead_details[0]['refered_by'];?></span>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="lead_source">Lead Source</label>
                                        </div>
                                        <div class="col-md-8">
                                            <span><?php echo $lead_details[0]['lead_source'];?></span>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="lead_source">Lead Owner</label>
                                        </div>
                                        <div class="col-md-8">
                                            <span><?php echo $lead_details[0]['username'];?></span>
                                        </div>
                                    </div>
                                    <hr/>
                                    <center><b>Additional Information</b></center>
                                    <hr/>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="address_line_1">Line 1</label>
                                        </div>
                                        <div class="col-md-8">
                                            <span><?php echo $lead_details[0]['address_line_1'];?></span>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="address_line_2">Line 2</label>
                                        </div>
                                        <div class="col-md-8">
                                            <span><?php echo $lead_details[0]['address_line_2'];?></span>
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="landmark">Landmark</label>
                                        </div>
                                        <div class="col-md-8">
                                            <span><?php echo $lead_details[0]['landmark'];?></span>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="area">Area</label>
                                        </div>
                                        <div class="col-md-8">
                                            <span><?php echo $lead_details[0]['area'];?></span>
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="city">City</label>
                                        </div>
                                        <div class="col-md-8">
                                            <span><?php echo $lead_details[0]['city'];?></span>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="pincode">Pin Code</label>
                                        </div>
                                        <div class="col-md-8">
                                            <span><?php echo $lead_details[0]['pincode'];?></span>
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="state">State</label>
                                        </div>
                                        <div class="col-md-8">
                                            <span><?php echo $lead_details[0]['state'];?></span>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="country">Country</label>
                                        </div>
                                        <div class="col-md-8">
                                           <span><?php echo $lead_details[0]['country'];?></span>
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="email">Email Id</label>
                                        </div>
                                        <div class="col-md-8">
                                            <span><?php echo $lead_details[0]['email_id'];?></span>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="eqipments">Mobile Number(Secondary)</label>
                                        </div>
                                        <div class="col-md-8">
                                            <span><?php echo $lead_details[0]['secondary_contact_no'];?></span>
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="doctor_name">Doctor Name</label>
                                        </div>
                                        <div class="col-md-8">
                                            <span><?php echo $lead_details[0]['doctor_name'];?></span>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">
                                            <label for="hospital_name">Hospital Name</label>
                                        </div>
                                        <div class="col-md-8">
                                                <span><?php echo $lead_details[0]['hospital_name'];?></span>
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <div class="col-md-4">                                            
                                            <label for="therapeutic_requirement">Therapeutic Requirement</label>
                                        </div>
                                        <div class="col-md-8">
                                            <span><?php echo $lead_details[0]['therapeutic_requirement'];?></span>
                                        </div>                                        
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-4">                                            
                                            <label for="therapeutic_requirement">Comment</label>
                                        </div>
                                        <div class="col-md-8">
                                            <span><?php echo $lead_details[0]['comment'];?></span>
                                        </div>                                        
                                    </div>
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
			</div>
		</div>
	</div>
	@endsection
</body>

</html>