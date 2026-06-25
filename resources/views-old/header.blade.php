<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title></title>

    <!-- Custom fonts for this template -->    
    @yield('styles')
        {{-- <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script> --}}
        <link href="<?php echo url('/');?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
        {{-- <link
            href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet"> --}}
        <!-- Custom styles for this template -->
        <link href="<?php echo url('/');?>/assets/css/sb-admin-2.min.css" rel="stylesheet">

        <!-- Custom styles for this page -->
        <link href="<?php echo url('/');?>/assets/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

        {{-- stylesheets --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.2/css/bootstrap.min.css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.min.css"/>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
        <link href="<?php echo url('/');?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
        {{-- Scripts --}}
        <script src="{{url('/')}}/assets/vendor/jquery/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="<?php echo url('/');?>/assets/js/jquery.table2excel.js"></script>
            
        <style>
            .navbar2 {
                background-color: #ffffff;
                position:fixed;
                top:30;
                width:100%;
                z-index:100;
            }
        </style>

    
</head>

<body id="page-top">	
		<!-- Page Wrapper -->
        <div id="wrapper">
            {{-- <div class="sidenav"> --}}                
            {{-- </div> --}}
            <div id="content-wrapper" class="d-flex flex-column">
                <div id="content">
                    <nav class="navbar navbar-expand navbar-light bg-white topbar fixed-top static-top">
                        <form class="form-inline">
                            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                                <i class="fa fa-bars"></i>
                            </button>
                        </form>
                        <ul class="navbar-nav">
                            <a class="sidebar-brand d-flex align-items-center justify-content-center" style="background-color: white;" href="#">
                                <div class="sidebar-brand-text mx-3" ><img src="<?php echo url('/');?>/assets/images/logo_small.png"></div>
                            </a>
                        </ul>                        
                        <ul class="navbar-nav ml-auto">
                            <input type="hidden" name="count" id="count">
                            {{-- <button type="button" class="btn btn-primary btn-sm form-control" style="margin-top: 20px;">
                                Lead Notifications <span id="lead_notifications" class="badge badge-light">0</span>
                            </button> --}}
                            @if(session('role')=='superuser' || session('role')=='admin')
                                <a class="btn btn-primary btn-sm form-control" style="margin-top: 20px;" href="{{url('/')}}/pendingAssignment">
                                    Pending Assignments <span id="pendingAssignments" class="badge badge-light">0</span>
                                </a>
                            @endif
                            &emsp;
                            <button type="button" class="btn btn-primary btn-sm form-control" style="margin-top: 20px;">
                                Lead Notifications <span id="lead_notifications" class="badge badge-light">0</span>
                            </button>
                            <div class="topbar-divider d-none d-sm-block"></div>
                            <li class="nav-item dropdown no-arrow">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{session('username')}}</span>
                                    <img class="img-profile rounded-circle"
                                        src="<?php echo url('/');?>/assets/img/undraw_profile.svg">
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                    aria-labelledby="userDropdown">
                                    <a class="dropdown-item" href="<?php echo url('/');?>/view_profile">
                                        <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                        Profile
                                    </a>
                                    <a class="dropdown-item" href="#">
                                        <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                        Settings
                                    </a>
                                    <a class="dropdown-item" href="#">
                                        <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                        Activity Log
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="<?php echo url('/');?>/logout">
                                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                        Logout
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </nav>
                    
                    <br><br><br>
                    <div class="navbar2 shadow">
                        sffgsrgdbssgsefgsfgsgsg
                    </div>
                    <div class="main">                        
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    <!-- Bootstrap core JavaScript-->
    {{-- <script src="<?php echo url('/');?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script> --}}

    <!-- Core plugin JavaScript-->
    {{-- <script src="<?php echo url('/');?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script> --}}

    <!-- Custom scripts for all pages-->
    {{-- <script src="<?php echo url('/');?>/assets/js/sb-admin-2.min.js"></script> --}}

    <!-- Page level plugins -->
    <script src="<?php echo url('/');?>/assets/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo url('/');?>/assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    {{-- <script src="<?php echo url('/');?>/assets/js/demo/datatables-demo.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script> --}}
    <script>
        $(document).ready(function() {
            $.ajax({
                url : "{{url('/')}}/pendingAssignmentsNotify",
                type : "GET",
                success : function(lead_count)
                {
                    $("#pendingAssignments").text(lead_count);
                }
            });
            $('#records').DataTable();
            $.ajax({
                    url : "{{url('/')}}/getCount",
                    type : "GET",
                    success : function(html)
                    {
                        $("#count").val(html);
                    }
            });
        } );
        function myFunction(id)
        {
            //var id = this.id;
            //alert(id);
            // {{session(['id'=>'<script>id</script>'])}}
            localStorage['id'] = ''+id;
            //alert(localStorage['id']);
        }
        if(localStorage['id'] != null) 
        {
            var id = localStorage['id'];
            $('.'+id).addClass('show');
            
        }
        function myFunction1(name)
        {
            //var name = this.name;
            //alert(name);
            // {{session(['name'=>'<script>name</script>'])}}
            localStorage['name'] = ''+name;
            //alert(localStorage['name']);
        }
        if(localStorage['name'] != null) 
        {
            var name = localStorage['name'];
            $('.'+name).addClass('show');
            
        }
        
        function charger() 
        {
            setTimeout( function()
            {
                var count = $('#count').val();
                $.ajax({
                    url : "{{url('/')}}/notifications/"+count+"",
                    type : "GET",
                    success : function(html)
                    {
                        $("#lead_notifications").text(html);
                    }
                });
                $.ajax({
                    url : "{{url('/')}}/pendingAssignmentsNotify",
                    type : "GET",
                    success : function(lead_count)
                    {
                        $("#pendingAssignments").text(lead_count);
                    }
                });

                charger();

            },5000);
        }

        charger();
	</script>
    @yield('script')
</body>

</html>