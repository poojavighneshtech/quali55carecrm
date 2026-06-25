

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Enter OTP</title>

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
                                      {{-- <div class="alert alert-success" role="alert">
                                        OTP has been sent on your Email Address...
                                      </div> --}}
                                      @if(session()->has('message'))
                                          <div class="alert alert-success">
                                              {{ session()->get('message') }}
                                          </div>
                                      @endif
                                      @if(session()->has('resend_message'))
                                          <div class="alert alert-success">
                                              {{ session()->get('resend_message') }}
                                          </div>
                                      @endif
                                      @if(session()->has('otp_wrong'))
                                          <div class="alert alert-danger">
                                              {{ session()->get('otp_wrong') }}
                                          </div>
                                      @endif
                                      <h3><i class="fa fa-lock fa-4x"></i></h3>
                                      <h2 class="text-center">Enter OTP</h2>
                                      <div class="panel-body">
                                        <form id="reset_password_form" role="form" action="<?php echo url('/');?>/submit_otp" autocomplete="off" class="form" method="post">
                                        {{ csrf_field() }}
                                          <div class="form-group">
                                            <div class="input-group">
                                              <input id="Entered_OTP" name="Entered_OTP" placeholder="Enter OTP..." class="form-control"  type="text" required="true">
                                            </div>
                                          </div>
                                          @foreach ($errors->all() as $error)
                                            <div class="alert alert-danger">
                                                {{$error}}
                                            </div>
                                          @endforeach
                                          <div class="row">
                                            <div class="col form-group">
                                              <a href="@php echo url('/');@endphp/resend_otp">Resend OTP</a>
                                            </div>
                                           
                                          </div>
                                          <div class="row">
                                            <div class="col form-group">
                                              <input type="submit" name="Submit_OTP" class="btn btn-lg btn-success btn-block" value="Submit OTP" >
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
    
</body>
@section('script')
  <script>
    $(".alert").fadeTo(10000, 5000).slideUp(500, function(){
        $(".alert").slideUp(10000);
    });
  </script>
@endsection
</html>