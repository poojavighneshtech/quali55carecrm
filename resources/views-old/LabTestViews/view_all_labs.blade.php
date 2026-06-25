@extends('header_and_sidebar')
<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
    
        <title>All Labs</title>
        {{-- <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script> --}}
        @section('styles')
        <style>
            /* .table {
                width:100%!important;
            }     */
            .padding-0{
                padding-right:0;
                padding-left:0;
            }
        </style>
        @endsection
    </head>

<body id="page-top">	
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
            <form action="{{url('/')}}/lab_register" method="post" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-header"  style="background-color: #4a6fdc; color: white;" >
                        <center>View All Labs</center>
                    </div> 
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-striped table-bordered table-repsonsive" id="records" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Sr No</th>
                                            <th>Lab Name</th>
                                            <th>Area</th>
                                            <th>Email</th>
                                            <th>Address</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($get_labs as $key => $lab)
                                            <tr>
                                                <td>{{$key+1}}</td>
                                                <td>{{$lab['lab_name']}}</td>
                                                <td>{{$lab['area']}}</td>
                                                <td>{{$lab['lab_email']}}</td>
                                                <td>{{$lab['line_1']}} ,{{$lab['line_2']}} ,{{$lab['landmark']}} ,{{$lab['area']}} ,{{$lab['location']}}-{{$lab['pincode']}}</td>
                                                <td>
                                                    <a href="{{url('/')}}/view_lab/{{$lab['id']}}" class="btn btn-outline-primary">View</a>
                                                    <a href="{{url('/')}}/delete_lab/{{$lab['id']}}" class="btn btn-outline-danger"><i class="fas fa-trash-alt"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
</body>
@section('script')
  <script>
      $(document).ready(function() {
            $('.select2').select2({
                theme: "classic",
                //placeholder: 'Select Test',
                allowClear: true
            });
        });
        document.querySelector('.custom-file-input').addEventListener('change',function(e){
            var fileName = document.getElementById("file_aggreement").files[0].name;
            var nextSibling = e.target.nextElementSibling
            //var numFiles = $(this).get(0).files.length
            nextSibling.innerText = fileName
            // if(numFiles>1)
            // {
            //     nextSibling.innerText = numFiles+" Files...";
            // }
            // else
            // {
            //     nextSibling.innerText = fileName;
            // }
            
        });

        var max_fields = 10;
        var x=1;

        $('#btn_certificate_add').on('click', function(){
            if(x < max_fields)
            {
                var rows="<div class='row' id='div_cer"+x+"'>";
                    rows+="<div class='col-md-11'>";
                    rows+="<div class='input-group mb-3'>";
                    rows+="<div class='input-group-prepend'>";
                    rows+="<input type='text' class='input-group-input form-control ' name='other_certificate[]' id='txt_aggreement"+x+"' placeholder='certificate name..' required>";
                    rows+="</div>";
                    rows+="<div class='custom-file'>";
                    rows+="<input type='file' class='custom-file-input' name='other_certificate[]' id='file_aggreement' required>";
                    rows+="<label class='custom-file-label' for='aggreement'>Choose file</label>";
                    rows+="</div>";
                    rows+="</div>";
                    rows+="</div>";
                    rows+="<div class='col-md-1 padding-0'>";
                    rows+="<button type='button' class='btn btn-outline-danger btn-rounded' id='remove_btn' data-count="+x+" value="+x+" onclick='remove_div(this.value);'> <i class='fas fa-trash-alt'></i></button>";
                    rows+="</div>";
                    rows+="</div>";
                    x++; 
                $('#div_certificate').append(rows);  
                
            }
        });

        // var max_fields = 10;
        // var wrapper = $("#div_add_image");
        // var rm = $('#remove_div');
        // var x=1;
        // $('#add_more').on('click',function(e){
        //     e.preventDefault();
        
        //     if(x < max_fields)
        //     {
        //         x++; 
        //         //alert(x);
        //         $(wrapper).append('<div class="row" id="remove_div'+x+'"><div class="form-group col-md-3 text-center" id="image_label"><label for="complaint_img" class="form-label"><b>Add Image</b></label></div><div class="col-md-9">  <input type="file" id="shop_images" class="" name="complaint_img[]" required="true" accept="image/png, image/jpeg, image/jpg," onclick="fileType(this.id);"><input type="button" class="btn btn-danger" id="'+x+'" onclick="remove_image_fun(this.id);" name="remove_image" value="- Remove" ></div></div>');

        //     }
        
        // // });
        function remove_div(id)
        {
            $('#div_cer'+id).remove();
            x--;
        }
  </script>
@endsection
</html>