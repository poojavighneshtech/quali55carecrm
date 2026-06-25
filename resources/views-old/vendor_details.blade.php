@extends('header_and_sidebar')
@section('title')
    Admin : Vendor Details
@endsection
@section('header')
    <style>
        .zoom {
            transition: transform .2s;
            }

            .zoom:hover {
            -ms-transform: scale(1.8); /* IE 9 */
            -webkit-transform: scale(1.8); /* Safari 3-8 */
            transform: scale(1.8); 
            }
    </style>
@endsection

@section('content')
    <form action="<?php echo url('/')?>/share_info" method="POST">
        {{ csrf_field() }}
        <div class="container-fluid">
            <!-- DataTales Example -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h3 class="m-0 font-weight-bold text-primary">Vendor Details</h3>
                </div>
                <div class="card-body">	                        	
                    
                    <div class="container">
                        <div class="row my-2">
                            <div class="col-lg-8 order-lg-2">
                                <ul class="nav nav-tabs">
                                    <li class="nav-item">
                                        <a href="" data-target="#profile" data-toggle="tab" class="nav-link active">Vendor Profile</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="" data-target="#warehouse" data-toggle="tab" class="nav-link">Warehouse Details</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="" data-target="#images" data-toggle="tab" class="nav-link">Uploaded Images</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="" data-target="#certificate" data-toggle="tab" class="nav-link">Certificates</a>
                                    </li>
                                </ul>

                                        
                                <div class="tab-content py-4">
                                    <div class="tab-pane active" id="profile">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label><b>Vendor Name</b></label>
                                            </div>
                                            <div class="col-md-6">
                                               <span>:{{$vendor_details[0]['registered_name']}}</span>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <label><b>Brand Name</b></label>
                                            </div>
                                            <div class="col-md-6">
                                               <span>:{{$vendor_details[0]['brand_name']}}</span>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <label><b>Office Address</b></label>
                                            </div>
                                            <div class="col-md-6">
                                               <span>:{{$vendor_details[0]['of_address']}}</span>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <label><b>Office Landmark</b></label>
                                            </div>
                                            <div class="col-md-6">
                                               <span>:{{$vendor_details[0]['of_landmark']}}</span>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <label><b>Office City</b></label>
                                            </div>
                                            <div class="col-md-6">
                                               <span>:{{$vendor_details[0]['of_city']}}</span>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <label><b>Office State</b></label>
                                            </div>
                                            <div class="col-md-6">
                                               <span>:{{$vendor_details[0]['of_state']}}</span>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <label><b>Office State</b></label>
                                            </div>
                                            <div class="col-md-6">
                                               <span>:{{$vendor_details[0]['of_country']}}</span>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <label><b>Office pincode</b></label>
                                            </div>
                                            <div class="col-md-6">
                                               <span>:{{$vendor_details[0]['of_pincode']}}</span>
                                            </div> 
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <label><b>Office Primary Contact</b></label>
                                            </div>
                                            <div class="col-md-6">
                                               <span>:{{$vendor_details[0]['wh_primary_contact_1']}}</span>
                                            </div>
                                        </div>	

                                        <div class="row">
                                            <div class="col-md-6">
                                                <label><b>Office Secondary Contact</b></label>
                                            </div>
                                            <div class="col-md-6">
                                               <span>:{{$vendor_details[0]['of_secondary_contact_2']}}</span>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <label><b>Office Email</b></label>
                                            </div>
                                            <div class="col-md-6">
                                               <span>:{{$vendor_details[0]['of_email']}}</span>
                                            </div>
                                        </div>	

                                    </div>

                                    <div class="tab-pane" id="warehouse">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label><b>Name</b></label>
                                            </div>
                                            <div class="col-md-6">
                                               <span>:{{$vendor_details[0]['warehouse_name']}}</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label><b>Address</b></label>
                                            </div>
                                            <div class="col-md-6">
                                               <span>:{{$vendor_details[0]['warehouse_address']}}</span>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <label><b>Landmark</b></label>
                                            </div>
                                            <div class="col-md-6">
                                               <span>:{{$vendor_details[0]['wh_landmark']}}</span>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <label><b>City</b></label>
                                            </div>
                                            <div class="col-md-6">
                                               <span>:{{$vendor_details[0]['wh_city']}}</span>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <label><b>Warehuse State</b></label>
                                            </div>
                                            <div class="col-md-6">
                                               <span>:{{$vendor_details[0]['wh_state']}}</span>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <label><b>Country</b></label>
                                            </div>
                                            <div class="col-md-6">
                                               <span>:{{$vendor_details[0]['wh_country']}}</span>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <label><b>pincode</b></label>
                                            </div>
                                            <div class="col-md-6">
                                               <span>:{{$vendor_details[0]['wh_pincode']}}</span>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <label><b>Primary Contact</b></label>
                                            </div>
                                            <div class="col-md-6">
                                               <span>:{{$vendor_details[0]['wh_primary_contact_1']}}</span>
                                            </div>
                                        </div>	

                                        <div class="row">
                                            <div class="col-md-6">
                                                <label><b>Secondary Contact</b></label>
                                            </div>
                                            <div class="col-md-6">
                                               <span>:{{$vendor_details[0]['wh_secondary_contact_2']}}</span>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <label><b>Email</b></label>
                                            </div>
                                            <div class="col-md-6">
                                               <span>:{{$vendor_details[0]['wh_email']}}</span>
                                            </div>
                                        </div>	

                                    </div>

                                    <div class="tab-pane" id="images">
                                        <div class="container">
                                            <div class="row">
                                                <h3>Shop Images</h3>
                                            </div>
                                            <div class="row">
                                                @if(isset($vendor_details[0]['shop_image']))
                                                    {{!$shop_images = json_decode($vendor_details[0]['shop_image'])}}
                                                    @foreach ($shop_images as $img)
                                                        <img class="img-fluid rounded zoom" src="{{$img}}" alt="Responsive image" width="190" />&emsp;
                                                    @endforeach
                                                @endif
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <h3>Cheque Images</h3>
                                            </div>
                                            <div class="row">
                                                @if(isset($vendor_details[0]['cheque_image']))
                                                    <img class="img-fluid rounded zoom" src="{{$vendor_details[0]['cheque_image']}}" alt="Responsive image" width="190"/>
                                                @endif                                                    
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane" id="certificate">
                                        @if($vendor_details[0]['type']=='commercial')
                                            <div class="form-group row">
                                                <label class="col-lg-5 col-form-label form-control-label"><b>GST Certificate</b></label>
                                                <div class="col-lg-7">
                                                    <a target="_blank" href="{{$vendor_details[0]['gst_certificate']}}" class="btn btn-primary" value="View Certificate">View Certificate</a>
                                                    &emsp;
                                                    @if($vendor_details[0]['authentication_status']=='Pending' || $vendor_details[0]['authentication_status']=='Rejected' || $vendor_details[0]['authentication_status']=='Requested')
                                                        <input type="checkbox" name="check_certificate[]" value="gst_certificate">
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-lg-5 col-form-label form-control-label"><b>Shop Establishment Certificate</b></label>
                                                <div class="col-lg-7">
                                                    <a target="_blank" href="{{$vendor_details[0]['shop_establishment_certificate']}}" class="btn btn-primary" value="View Certificate">View Certificate</a>
                                                    &emsp;
                                                    @if($vendor_details[0]['authentication_status']=='Pending' || $vendor_details[0]['authentication_status']=='Rejected' || $vendor_details[0]['authentication_status']=='Requested')
                                                        <input type="checkbox" name="check_certificate[]" value="shop_certificate">
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <div class="form-group row">
                                            <label class="col-lg-5 col-form-label form-control-label"><b>Vendor Pan Card</b></label>
                                            <div class="col-lg-7">
                                                <a target="_blank" href="{{$vendor_details[0]['vendor_pan_card']}}" class="btn btn-primary" value="View Certificate">View Certificate</a>
                                                &emsp;
                                                @if($vendor_details[0]['authentication_status']=='Pending' || $vendor_details[0]['authentication_status']=='Rejected' || $vendor_details[0]['authentication_status']=='Requested')
                                                    <input type="checkbox" name="check_certificate[]" value="vendor_pancard">
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-5 col-form-label form-control-label"><b>Vendor aggreement</b></label>
                                            <div class="col-lg-7">
                                                <a target="_blank" href="{{$vendor_details[0]['vendor_aggreement']}}" class="btn btn-primary" value="View Certificate">View Certificate</a>
                                                &emsp;
                                                @if($vendor_details[0]['authentication_status']=='Pending' || $vendor_details[0]['authentication_status']=='Rejected' || $vendor_details[0]['authentication_status']=='Requested')
                                                    <input type="checkbox" name="check_certificate[]" value="aggreement_certificate">
                                                @endif
                                            </div>
                                        </div>

                                        <hr class="sidebar-divider my-0">
                                        <br>
                                        @if($vendor_details[0]['authentication_status']=='Pending' || $vendor_details[0]['authentication_status']=='Rejected' || $vendor_details[0]['authentication_status']=='Requested')
                                            <div class="form-group row">
                                                <label class="col-lg-3 col-form-label form-control-label"></label>
                                                <div class="col-lg-9">
                                                    <input type="hidden" value="{{$vendor_details[0]['id']}}" name="vendor_id" id="vendor_id">
                                                    <button type="submit" name="submit" class="btn btn-primary" value="Approve">Approve</button>
                                                    <button type="button" class="btn btn-secondary" id="revise"  value="Revise">Revise</button>
                                                </div>
                                            </div> 
                                        @endif   
                                    </div>
                                </div>

                            </div>
                            <input type="hidden" name="auth_status" id="auth_status" value="{{$vendor_details[0]['authentication_status']}}">
                            <div class="col-lg-4 order-lg-2">
                                <div class="card" id="comments" style="display:none;">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="exampleFormControlTextarea1"></label>
                                            <textarea class="form-control" id="comment" name="comment" rows="12">@if($vendor_details[0]['authentication_status']=='Requested' || $vendor_details[0]['authentication_status']=='Rejected' || isset($vendor_details[0]['comment'])){{$vendor_details[0]['comment']}}@endif</textarea>
                                        </div>
                                        <div class="col">
                                            <input type="submit" name="submit" class="btn btn-primary" value="Revise">
                                            <input type="button" class="btn btn-secondary" id="cancel_comment" value="Cancel">
                                        </div>
                                    </div>
                                </div>
                            </div>    
                        </div>									      
                    </div>  
                </div>

            </div>
            
        </div>
        <!-- /.container-fluid -->
                	
    </form>
@endsection

@section('script')
<script>
    $(document).ready(function(){
        var auth_status = $('#auth_status').val();
        if (auth_status=='Requested' || auth_status=='Rejected') {
            document.getElementById('comments').style.display='block';
        }

        $('#revise').on('click',function(){
            document.getElementById('comments').style.display='block';
            //alert(auth_status);
        })
        $('#cancel_comment').on('click',function(){
            document.getElementById('comments').style.display='none';
        })

    });
</script>
@endsection
