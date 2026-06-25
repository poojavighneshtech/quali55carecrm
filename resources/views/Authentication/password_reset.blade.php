

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reset Password</title>

    <link href="<?php echo url('/');?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
        <link href="<?php echo url('/');?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">


    <!-- Custom styles for this template -->
    <link href="<?php echo url('/');?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
   
    <!-- Custom styles for this page -->
    <link href="<?php echo url('/');?>/assets/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script>

     <!-- passsword show library and icon show-->
     <link href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.css" rel="stylesheet"/>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body>
    <center>
        <div class="container" style="margin-top:10%">
            <!-- DataTales Example -->
            <div class="col-md-7 card shadow mb-4">
                <div class="card-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                  <div class="panel-body">
                                    <div class="text-center">
                                      @if(session()->has('opt_sucess'))
                                          <div class="alert alert-success">
                                              {{ session()->get('opt_sucess') }}
                                          </div>
                                      @endif
                                      <h3><i class="fa fa-lock fa-4x"></i></h3>
                                      <h2 class="text-center">Enter New Password</h2>
                                      <div class="panel-body">
                                        <form id="reset_password_form" role="form" action="<?php echo url('/');?>/password_reset" autocomplete="off" class="form" method="post">
                                          {{ csrf_field() }}

                                          <div class="input-group">
                                            <input type="password" id="password" name="password"  data-toggle="password" class="form-control"placeholder="Password.." autocomplete="new-password" minlength="6">
                                            <div class="input-group-append toggle-password">
                                                <span class="input-group-text mdi mdi-eye-outline"></span>
                                            </div>
                                          </div>
                                          <br>
                                          @foreach ($errors->all() as $error)
                                            <div class="alert alert-danger">
                                                {{$error}}
                                            </div>
                                          @endforeach
                                          
                                          </div>
                                          <div class="input-group">
                                            <input type="password" id="confirm_password" name="confirm_password"  data-toggle="password" class="form-control"placeholder="Confirm Password..." autocomplete="new-password" minlength="6">
                                            <div class="input-group-append toggle-password">
                                                  <span class="input-group-text mdi mdi-eye-outline"></span>
                                            </div>
                                          </div>
                                          <br>
                                          
                                          <div class="row">
                                            <div class="col form-group">
                                              <input type="submit" name="submit" class="btn btn-lg btn-success btn-block" value="Submit" >
                                            </div>
                                            <div class="col form-group">
                                              <a href="<?php echo url('/')?>" class="btn btn-lg btn-primary btn-block">Home</a>
                                            </div>
                                          </div>

                                          <input type="hidden" class="hide" name="token" id="token" value=""> 
                                        </form>
                        
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                        </div>
                    </div>
                      
                </div>
            </div>	                    
        </div>
    </center>

    <script>
       $('.toggle-password').click(function(){
            $(this).children().toggleClass('mdi-eye-outline mdi-eye-off-outline');
            let input = $(this).prev();
            input.attr('type', input.attr('type') === 'password' ? 'text' : 'password');
        });      
    </script>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="<?php echo url('/');?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="<?php echo url('/');?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="<?php echo url('/');?>/assets/js/sb-admin-2.min.js"></script>


    <!-- Page level plugins -->
    <script src="<?php echo url('/');?>/assets/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo url('/');?>/assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="<?php echo url('/');?>/assets/js/demo/datatables-demo.js"></script>
</body>


</html>