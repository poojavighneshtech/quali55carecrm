<?php use \App\Http\Controllers\Authentication\AuthController; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quali5Care: LOGIN</title>

     <!-- Custom fonts for this template -->
     <link href="<?php echo url('/');?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="<?php echo url('/');?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
   
    <!-- Custom styles for this page -->
    <link href="<?php echo url('/');?>/assets/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

    <!-- passsword show library and icon show-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    


</head>
<body style="background-image: url({{url('/')}}/assets/images/LoginBackground1.png); background-position: center;">
    <div class="container">
        <form class="form" method="POST" action="<?php echo url('/')?>/validate_login" enctype="multipart/form-data" >
            {{ csrf_field() }}
            <div class="row justify-content-center" style="margin-top: 8rem;">
                <div class="col-md-6">
                    <div class="card o-hidden border-0 shadow-lg">
                    </div>
                    <a class="sidebar-brand d-flex align-items-center justify-content-center" style="background-color: white; margin-right:" href="#"> 
                        <div class="sidebar-brand-text mx-3" ><img class="img-fluid rounded" src="<?php echo url('/');?>/assets/images/LogoGadm.png"></div>
                    </a>
                    <br>

                    @if(session()->has('message'))
                        <div class="alert alert-success alert">
                            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                <span aria-hidden='true'>&times;</span>
                            </button> {{ session()->get('message') }}
                        </div>
                    @endif
                    @if(session()->has('error_login'))
                        <div class="alert alert-danger">
                            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                <span aria-hidden='true'>&times;</span>
                            </button> {{ session()->get('error_login') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        @foreach ($errors->all() as $error)
                            <div class="alert alert-danger">
                                <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                    <span aria-hidden='true'>&times;</span>
                                </button> {{$error}}
                            </div>
                        @endforeach
                    @endif
                    <div class="form-group">
                        <input type="text"  class="form-control" id="username" name="username" placeholder="Username" value="{{old('username')}}" required>
                        <br>
                        <div class="input-group">
                            <input type="password"  data-toggle="password" class="form-control" name="password" id="password" placeholder="Password" autocomplete="new-password" required>
                            <div class="input-group-append toggle-password">
                                 <span class="input-group-text mdi mdi-eye-outline"></span>
                            </div>
                         </div>
                    </div>
                    <div class="row">
                        <div class="col-md-7">
                            <div class="form-group">
                                @php
                                    $ip_address = AuthController::get_client_ip();
                                    $ip_address = str_replace(".", "", $ip_address);
                                    $ip_address_captcha_high = config('app.ip_address_captcha_high');
                                    $ip_address_captcha_high = str_replace(".", "", $ip_address_captcha_high);
                                    // echo "High".$ip_address_captcha_high;
                                    $ip_address_captcha_low = config('app.ip_address_captcha_low');
                                    $ip_address_captcha_low = str_replace(".", "", $ip_address_captcha_low);
                                @endphp 
                              <input type="text" class="form-control" name="captcha" id="captcha" aria-describedby="helpId" placeholder="Enter Captcha" @if(!$ip_address <= $ip_address_captcha_high && !$ip_address >= $ip_address_captcha_low){{"required"}}@endif>
                            </div>
                        </div>
                        <div class="col-md-5 form-group">
                            <div class="captcha input-group">
                                <span>{!! captcha_img('mini') !!}</span>
                                &emsp;
                                <div class="input-group-append">
                                    <a class="btn btn-secondary" id="reload" name="reload"><i class="fas fa-sync"></i></a>
                                </div>
                             </div>
                        </div>
                        
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary btn-block" value="Login">Login</button> 
                    <hr>
                </div>
            </div> 
        </form>                                
    </div>
    <script>
        
        $('.toggle-password').click(function(){
            $(this).children().toggleClass('mdi-eye-outline mdi-eye-off-outline');
            let input = $(this).prev();
            input.attr('type', input.attr('type') === 'password' ? 'text' : 'password');
        });

        $('#reload').click(function () {
            $.ajax({
                type: 'GET',
                url: '<?php echo url('/');?>/reload_captcha',
                success: function (data) {
                   // alert(data);
                    $(".captcha span").html(data.captcha);
                }
            });
        });
    </script>

    <!-- Bootstrap core JavaScript-->
    <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script>
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