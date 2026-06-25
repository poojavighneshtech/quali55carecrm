<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Inquiry : Create Lead</title>
        <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script>
    </head>

<body id="page-top">	
		<!-- Page Wrapper -->
        @extends('header_and_sidebar')
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
                                <span><b>Check Customer (If already Registered)</b></span>
                            </div>
                            <div class="panel-body"><!--style="min-height: 0; max-height: 5; overflow-y: scroll;"-->
                                <form class="form" method="POST" action="check_customer">
                                {{ csrf_field() }}
                                    <div class="row">
                                    <div class="col-md-3">
                                    </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" id="primary_contact_no" style="border-radius: 5px; border:#FF0000 1px solid;" name="primary_contact_no" placeholder="Mobile Number (Primary)*" required="true">
                                            </div>
                                        </div>
                                    </div>
                                    <center>
                                        <button class="btn btn-primary" type="submit" name="submit" value="check">Check</button>
                                        <button class="btn btn-default" type="reset" name="submit">Clear</button>
                                    </center>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endsection
            </div>
    @section('script')
    <script>
        
        $(document).ready(function(){
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