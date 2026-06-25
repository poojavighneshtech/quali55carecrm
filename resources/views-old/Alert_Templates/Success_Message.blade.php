<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="{{url('/')}}/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
        <link
            href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
            rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.min.css"/>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
        <!-- Custom styles for this template-->
        <link href="{{url('/')}}/assets/css/sb-admin-2.min.css" rel="stylesheet">   
        <script src="{{url('/')}}/assets/vendor/jquery/jquery.min.js"></script>
        <script src="{{url('/')}}/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="{{url('/')}}/assets/vendor/jquery/jquery.cookie.js" type="text/javascript"></script>

        <!-- Core plugin JavaScript-->
        <script src="{{url('/')}}/assets/vendor/jquery-easing/jquery.easing.min.js"></script>

        <!-- Custom scripts for all pages-->
        <script src="{{url('/')}}/assets/js/sb-admin-2.min.js"></script>
        
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
        {{-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script> --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="{{url('/')}}/assets/js/jquery.table2excel.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js" ></script>
</head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <img src="{{url('/')}}/assets/images/logo2-1 old.png" alt="" h>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    <img src="{{url('/')}}/assets/images/success1.png" alt="">
                </div>
            </div>
            <div class="row">
                <div class="col justify-content-center text-center p-2">
                    <strong><p class="text-dark">Our support team will get in touch with you shortly. In case you need immediate assistence then please call us at <a href="tel:{{$contact_no}}">{{$contact_no}}</a> ({{$username}}).</p></strong>
                </div>
            </div>
        </div>
    </body>
</html>