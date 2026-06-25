

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Forgot Password</title>

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
                                      @if(session()->has('message'))
                                        <p class="alert alert-danger">{{ Session::get('message') }}</p>
                                      @endif
                                      <h3><i class="fa fa-lock fa-4x"></i></h3>
                                      <h2 class="text-center">Forgot Password?</h2>
                                      <p>You can reset your password here.</p>
                                      <div class="panel-body">
                                        <form id="reset_password_form" role="form" action="<?php echo url('/');?>/forgot_password" autocomplete="off" class="form" method="post">
                                        {{ csrf_field() }}
                                          <div class="form-group">
                                            <div class="input-group">
                                              <span class="input-group-addon"><i class="glyphicon glyphicon-envelope color-blue"></i></span>
                                              <input id="email" name="email" placeholder="email address" class="form-control"  type="email" required="true">
                                            </div>
                                          </div>
                                          <div class="form-group">
                                            <input type="submit" name="Send_OTP" class="btn btn-lg btn-primary btn-block" value="Send OTP" >
                                          </div>
                                          <a href="<?php echo url('/')?>">Home</a>
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
    
</body>
</html>