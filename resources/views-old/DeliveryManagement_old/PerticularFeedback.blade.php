@extends('header_and_sidebar')
@section('title')
    Admin: feedback
@endsection
@section('styles')
    <style>
       
    </style>
@endsection
@section('content')
    <form action="<?php echo url('/');?>" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="row justify-content-center" style="margin-top: 0rem;">
            <div class="col-6">
                <div class="card o-hidden border-0 shadow-lg">
                    
                    <div class="card-header bg-primary text-white">
                        <h3 class="m-0 font-weight-bold">Feedback Information</h3>
                    </div>
                    <div class="card-body">
                        @if(session()->has('message'))
                            <div class="alert alert-success">
                                {{ session()->get('message') }}
                            </div>
                        @endif
                    
                        <div class="row">
                            <div class="col-md-4 form-group text-right">
                                <label for="customer_name" class="control-label"><b>Customer Name :</b></label>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <span>{{$perticular_feedback[0]['shipping_first_name']}}</span>
                                    {{-- <input type="text" class="form-control" name="username" id="username" placeholder="Username" required="true"> --}}
                                </div>
                            </div>
                        </div>
                       
                        <div class="row">
                            <div class="col-md-4 form-group text-right">
                                <label for="product_name" class="control-label"><b>Product Name :</b></label>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <span>{{$perticular_feedback[0]['line_item_1']}}</span>
                                    {{-- <input type="number" class="form-control" name="contact_no" id="contact_no" maxlength="10" placeholder="Mobile Number" required="true"> --}}
                                </div>
                            </div> 
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 form-group text-right">
                                <label for="customer_rating" class="control-label"><b>Customer Rating :</b></label>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    @if($perticular_feedback[0]['custstarrating']=='1')
                                        <img src="https://img.icons8.com/color/50/000000/filled-star--v1.png" /> 
                                    @endif
                                    @if($perticular_feedback[0]['custstarrating']=='2')
                                        <img src="https://img.icons8.com/color/50/000000/filled-star--v1.png"/> 
                                        <img src="https://img.icons8.com/color/50/000000/filled-star--v1.png"/> 
                                    @endif
                                    @if($perticular_feedback[0]['custstarrating']=='3')
                                        <img src="https://img.icons8.com/color/50/000000/filled-star--v1.png"/> 
                                        <img src="https://img.icons8.com/color/50/000000/filled-star--v1.png"/> 
                                        <img src="https://img.icons8.com/color/50/000000/filled-star--v1.png"/> 
                                    @endif
                                    @if($perticular_feedback[0]['custstarrating']=='4')
                                        <img src="https://img.icons8.com/color/50/000000/filled-star--v1.png"/> 
                                        <img src="https://img.icons8.com/color/50/000000/filled-star--v1.png"/> 
                                        <img src="https://img.icons8.com/color/50/000000/filled-star--v1.png"/> 
                                        <img src="https://img.icons8.com/color/50/000000/filled-star--v1.png"/> 
                                    @endif
                                    @if($perticular_feedback[0]['custstarrating']=='5')
                                        <img src="https://img.icons8.com/color/50/000000/filled-star--v1.png"/> 
                                        <img src="https://img.icons8.com/color/50/000000/filled-star--v1.png"/> 
                                        <img src="https://img.icons8.com/color/50/000000/filled-star--v1.png"/> 
                                        <img src="https://img.icons8.com/color/50/000000/filled-star--v1.png"/> 
                                        <img src="https://img.icons8.com/color/50/000000/filled-star--v1.png"/> 
                                    @endif
                                  
                                </div>
                            </div> 
                        </div>

                        <div class="row">
                            <div class="col-md-4 form-group text-right">
                                <label for="cust_sign" class="control-label"><b>Customer Sign :</b></label>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <img src="http://{{$perticular_feedback[0]['cust_sign']}}" class="rounded " id="output"  alt="" width="200" />
                                </div>
                            </div> 
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group text-right">
                                <label for="product_image" class="control-label"><b>Product Image :</b></label>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <img src="http://{{$perticular_feedback[0]['product_delivered']}}" class="rounded " id="output"  alt="" width="200" height="200" />
                                </div>
                            </div> 
                        </div>

                        <div class="row">
                            <div class="col-md-4 form-group text-right">
                                <label for="product_name" class="control-label"><b>Customer Comment:</b></label>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <span>{{$perticular_feedback[0]['custcomments']}}</span>
                                    {{-- <input type="number" class="form-control" name="contact_no" id="contact_no" maxlength="10" placeholder="Mobile Number" required="true"> --}}
                                </div>
                            </div> 
                        </div>

                    </div>
                </div>   
            </div>
        </div> 
    </form>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $(':radio').change(function() {
            console.log('New star rating: ' + this.value);
        });
    });
    // function viewDetails(clicked_id)
    // {

    //         document.getElementById("thead"+clicked_id).style.visibility="visible";
    //         document.getElementById("tbody"+clicked_id).style.visibility="visible";
    //         document.getElementById(clicked_id).style.display = "none";
    //         document.getElementById("hide"+clicked_id).style.display = "block";
    // }
    // function hideDetails(clicked_id)
    // {
    //         var id = document.getElementById(clicked_id);
    //         var dataID = id.getAttribute('data-id');
    //         document.getElementById("thead"+dataID).style.visibility="hidden";
    //         document.getElementById("tbody"+dataID).style.visibility="hidden";
    //         document.getElementById(dataID).style.display = "block";
    //         document.getElementById("hide"+dataID).style.display = "none";
    // }
</script>

@endsection