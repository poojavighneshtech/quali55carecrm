<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Inquiry : Create Lead</title>
    </head>

<body id="page-top">	
	<!-- Page Wrapper -->
    @extends('Base/side_nav')
        <?php if (session('role') == 'admin'){?>
            @section('Admin')
                @parent
            @endsection
            @section('side_users')
                @parent
            @endsection

        <?php } else{?>
            @section('Admin')
                @stop
        
            @section('side_users')
                @stop
        
        <?php } ?>
            <div class="container">
                @section('content')
                <div class="row">
                    <div class="col-md-2">
                    </div>
                    <div class="col-md-8 col-md-offset-2">
                        <div class="panel panel-primary">
                            <div class="panel-heading text-center">
                                <span><b><?php if(isset($_GET['ac'])){ if($_GET['ac'] == 'edit'){ echo "Modify Inquiry"; }elseif($_GET['ac'] == 'con'){echo "Convert Inquiry";}}else{echo "New Inquiry";}?></b></span>
                            </div>
                            <div class="panel-body"><!--style="min-height: 0; max-height: 5; overflow-y: scroll;"-->
                                <form class="form" method="POST" action="create_lead">
                                {{ csrf_field() }}
                                    <input type="hidden" name="customer_id" <?php if(isset($_POST['submit'])){if($_POST['submit']=='check'){echo "value ='".$row['cust_id']."'";}} if(isset($_GET['ac'])){ if($_GET['ac'] == 'edit'){ echo "value='".$row['customer_id']."'"; }elseif($_GET['ac'] == 'con'){echo "value='".$row['customer_id']."'";}}?>>
                                    <input type="hidden" name="lead_id" <?php if(isset($_GET['ac'])){ if($_GET['ac'] == 'edit'){ echo "value='".$row['id']."'"; }elseif($_GET['ac'] == 'con'){echo "value='".$row['id']."'";}}?>>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" <?php if(isset($_GET['ac'])){ if($_GET['ac'] == 'edit'){ echo "style='border-radius: 5px; border:#3292e4 1px solid;' value='".$row['customer_name']."'"; }elseif($_GET['ac'] == 'con'){echo "value='".$row['customer_name']."'";}}elseif(isset($row['customer_name'])){echo "value='".$row['customer_name']."'";}else{echo "style='border-radius: 5px; border:#FF0000 1px solid;'";}?> id="cust_name" name="cust_name" placeholder="Customer Name*" required="true">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <input type="text" class="form-control" <?php if(isset($_GET['ac'])){ if($_GET['ac'] == 'edit'){ echo "value='".$row['patient_name']."'"; }elseif($_GET['ac'] == 'con'){echo "value='".$row['patient_name']."'";}}?> name="patient_name" placeholder="Patient Name*">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <input type="number" class="form-control" <?php if(isset($_GET['ac'])){ if($_GET['ac'] == 'edit'){ echo "value='".$row['patient_age']."'"; }elseif($_GET['ac'] == 'con'){echo "value='".$row['patient_age']."'";}}?> name="patient_age" placeholder="Patient Age*">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" <?php if(isset($_GET['ac'])){ if($_GET['ac'] == 'edit'){ echo "value='".$row['address_line_1']."'"; }elseif($_GET['ac'] == 'con'){echo "value='".$row['address_line_1']."'";}}if(isset($row['address_line_1'])){echo "value='".$row['address_line_1']."'";}?> name="address_line_1" placeholder="Line 1*">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" <?php if(isset($_GET['ac'])){ if($_GET['ac'] == 'edit'){ echo "value='".$row['address_line_2']."'"; }elseif($_GET['ac'] == 'con'){echo "value='".$row['address_line_2']."'";}}if(isset($row['address_line_2'])){echo "value='".$row['address_line_2']."'";}?> name="address_line_2" placeholder="Line 2*">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" <?php if(isset($_GET['ac'])){ if($_GET['ac'] == 'edit'){ echo "value='".$row['landmark']."'"; }elseif($_GET['ac'] == 'con'){echo "value='".$row['landmark']."'";}}if(isset($row['landmark'])){echo "value='".$row['landmark']."'";}?> name="landmark" placeholder="Landmark*">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" <?php if(isset($_GET['ac'])){ if($_GET['ac'] == 'edit'){ echo "value='".$row['area']."'"; }elseif($_GET['ac'] == 'con'){echo "value='".$row['area']."'";}}if(isset($row['area'])){echo "value='".$row['area']."'";}?> name="area" placeholder="Area*">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" <?php if(isset($_GET['ac'])){ if($_GET['ac'] == 'edit'){ echo "value='".$row['city']."'"; }elseif($_GET['ac'] == 'con'){echo "value='".$row['city']."'";}}if(isset($row['city'])){echo "value='".$row['city']."'";}?> name="city" placeholder="City*">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" <?php if(isset($_GET['ac'])){ if($_GET['ac'] == 'edit' and $row['pincode']!=0){ echo "value='".$row['pincode']."'"; }elseif($_GET['ac'] == 'con' and $row['pincode']!=0){echo "value='".$row['pincode']."'";}}if(isset($row['pincode'])){echo "value='".$row['pincode']."'";}?> name="pincode" placeholder="Pincode*">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" <?php if(isset($_GET['ac'])){ if($_GET['ac'] == 'edit'){ echo "value='".$row['state']."'"; }elseif($_GET['ac'] == 'con'){echo "value='".$row['state']."'";}}if(isset($row['state'])){echo "value='".$row['state']."'";}?> name="state" placeholder="State*">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" <?php if(isset($_GET['ac'])){ if($_GET['ac'] == 'edit'){ echo "value='".$row['country']."'"; }elseif($_GET['ac'] == 'con'){echo "value='".$row['country']."'";}}if(isset($row['country'])){echo "value='".$row['country']."'";}?> name="country" placeholder="Country*">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" id="location" <?php if(isset($_GET['ac'])){ if($_GET['ac'] == 'edit'){ echo "style='border-radius: 5px; border:#3292e4 1px solid;' value='".$row['location']."'"; }elseif($_GET['ac'] == 'con'){echo "value='".$row['location']."'";}}elseif(isset($row['location'])){echo "value='".$row['location']."'";}else{ echo "style='border-radius: 5px; border:#FF0000 1px solid;'";}?> name="location" placeholder="Location*" required="true">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="email" class="form-control" <?php if(isset($_GET['ac'])){ if($_GET['ac'] == 'edit'){ echo "value='".$row['email_id']."'"; }elseif($_GET['ac'] == 'con'){echo "value='".$row['email_id']."'";}}if(isset($row['email_id'])){echo "value='".$row['email_id']."'";}?> name="email" placeholder="Email*">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" id="primary_contact_no" <?php if(isset($_GET['ac'])){ if($_GET['ac'] == 'edit'){ echo "style='border-radius: 5px; border:#3292e4 1px solid;' value='".$row['primary_contact_no']."'"; }elseif($_GET['ac'] == 'con'){echo "value='".$row['primary_contact_no']."'";}}elseif(isset($row['primary_contact_no'])){echo "value='".$row['primary_contact_no']."'";}else{echo "style='border-radius: 5px; border:#FF0000 1px solid;'";}?> name="primary_contact_no" placeholder="Mobile Number (Primary)*" required="true">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" <?php if(isset($_GET['ac'])){ if($_GET['ac'] == 'edit'){ echo "value='".$row['secondary_contact_no']."'"; }elseif($_GET['ac'] == 'con'){echo "value='".$row['secondary_contact_no']."'";}}if(isset($row['secondary_contact_no'])){echo "value='".$row['secondary_contact_no']."'";}?> name="secondary_contact_no" placeholder="Mobile Number (Secondary)*">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" <?php if(isset($_GET['ac'])){ if($_GET['ac'] == 'edit'){ echo "value='".$row['doctor_name']."'"; }elseif($_GET['ac'] == 'con'){echo "value='".$row['doctor_name']."'";}}?> name="doctor_name" placeholder="Doctor Name*">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" <?php if(isset($_GET['ac'])){ if($_GET['ac'] == 'edit'){ echo "value='".$row['hospital_name']."'"; }elseif($_GET['ac'] == 'con'){echo "value='".$row['hospital_name']."'";}}?> name="hospital_name" placeholder="Hospital Name*">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <input type="text" class="form-control" <?php if(isset($_GET['ac'])){ if($_GET['ac'] == 'edit'){ echo "value='".$row['therapeutic_requirement']."'"; }elseif($_GET['ac'] == 'con'){echo "value='".$row['therapeutic_requirement']."'";}}?> name="therapeutic_requirement" placeholder="Therapeutic Requirement*">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <input type="text" class="form-control" id="equipments" <?php if(isset($_GET['ac'])){ if($_GET['ac'] == 'edit'){ echo "style='border-radius: 5px; border:#3292e4 1px solid;' value='".$row['equipment_requirement']."'"; }elseif($_GET['ac'] == 'con'){echo "value='".$row['equipment_requirement']."'";}}else{echo "style='border-radius: 5px; border:#FF0000 1px solid;'";}?> name="equipments" placeholder="Equipment Required*" required="true"> 
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" <?php if(isset($_GET['ac'])){ if($_GET['ac'] == 'edit'){ echo "value='".$row['refered_by']."'"; }elseif($_GET['ac'] == 'con'){echo "value='".$row['refered_by']."'";}}if(isset($row['refered_by'])){echo "value='".$row['refered_by']."'";}?> name="refered_by" placeholder="Refered By*">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" id="lead_source" <?php if(isset($_GET['ac'])){ if($_GET['ac'] == 'edit'){ echo "style='border-radius: 5px; border:#3292e4 1px solid;' value='".$row['lead_source']."'"; }elseif($_GET['ac'] == 'con'){echo "value='".$row['lead_source']."'";}}else{echo "style='border-radius: 5px; border:#FF0000 1px solid;'";}?> name="lead_source" placeholder="Lead Source (Google, JustDial, Marketing)*" required="true">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- <select id="multiple" class="form-control form-control-chosen" data-placeholder="Please select..." multiple>
                                      <option></option>
                                      <option>Option One</option>
                                      <option>Option Two</option>
                                      <option>Option Three</option>
                                    </select> -->
                                    <center>
                                        <button class="btn btn-primary" type="submit" name="submit" <?php if(isset($_POST['submit'])){if($_POST['submit']=='check'){echo "value ='insert_lead'";}} if(isset($_GET['ac'])){ if($_GET['ac'] == 'edit'){ echo "value='update'"; }elseif($_GET['ac'] == 'con'){echo "value='convert'";}}else{echo "value='Submit'";}?>><?php if(isset($_GET['ac'])){ if($_GET['ac'] == 'edit'){ echo "Update"; }elseif($_GET['ac'] == 'con'){echo "Convert";}}else{echo "Submit";}?></button>
                                        <button class="btn btn-default" type="reset" name="submit">Clear</button>
                                    </center>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endsection
            </div>

            <!-- End of Main Content -->
        
        <!-- End of Content Wrapper -->

    
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">                	
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="login.html">Logout</a>
                </div>
            </div>
        </div>
    </div>	   
    @section('script')
    <script>
        
        $(document).ready(function(){
            // $('.form-control-chosen').chosen();
            $("#cust_name").on("input", function(){
                // Print entered value in a div box
                if(!$(this).val())
                {
                    $('#cust_name').attr('style', "border-radius: 5px; border:#FF0000 1px solid;");
                }
                else
                {
                    $('#cust_name').attr('style', "border-radius: 5px; border:#3292e4 1px solid;");
                }
            });
            $("#location").on("input", function(){
                // Print entered value in a div box
                if(!$(this).val())
                {
                    $('#location').attr('style', "border-radius: 5px; border:#FF0000 1px solid;");
                }
                else
                {
                    $('#location').attr('style', "border-radius: 5px; border:#3292e4 1px solid;");
                }
            });
            $("#equipments").on("input", function(){
                // Print entered value in a div box
                if(!$(this).val())
                {
                    $('#equipments').attr('style', "border-radius: 5px; border:#FF0000 1px solid;");
                }
                else
                {
                    $('#equipments').attr('style', "border-radius: 5px; border:#3292e4 1px solid;");
                }
            });
            $("#lead_source").on("input", function(){
                // Print entered value in a div box
                if(!$(this).val())
                {
                    $('#lead_source').attr('style', "border-radius: 5px; border:#FF0000 1px solid;");
                }
                else
                {
                    $('#lead_source').attr('style', "border-radius: 5px; border:#3292e4 1px solid;");
                }
            });
            $("#primary_contact_no").on("input", function(){
                // Print entered value in a div box
                if(!$(this).val())
                {
                    $('#primary_contact_no').attr('style', "border-radius: 5px; border:#FF0000 1px solid;");
                }
                else
                {
                    $('#primary_contact_no').attr('style', "border-radius: 5px; border:#3292e4 1px solid;");
                }
            });
        });
	</script>
    @endsection

</body>

</html>