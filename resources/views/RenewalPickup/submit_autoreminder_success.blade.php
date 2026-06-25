<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Quali55care</title>
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
        <div class="row">
            <div class="col-md-12 text-center">
                <img src="{{url('/')}}/assets/images/logo_small.png" alt="">
            </div>
        </div>
        <div class="container-fluid">
            <strong><p>Your request has been submitted successfully</p></strong>
            <div class="row">
                @foreach($product_status as $key=>$status)
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-auto">
                                        <b>Product Name : </b>{{$product_name_arr[$key]}}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-auto">
                                        <b>Product Rent : </b>{{$product_rent_arr[$key]}}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-auto">
                                        <b>Status : </b>
                                        @if($status==0)
                                            <span>Renew</span>
                                        @elseif($status==1)
                                            <span>Pickup</span>
                                        @else
                                            <span>Undecided</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-auto">
                                        <b>Due Date : </b>{{$order_pickup_date[$key]}}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-auto">
                                        <b>Next Renewal Date : </b>
                                        @if($status==0)
                                            {{$order_renewal_date[$key]}}
                                        @else
                                            <span>-</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-auto">
                                        <b>Pickup Date : </b>
                                        @if($status==1)
                                            {{$cust_pickup_date[$key]}}
                                        @else
                                            <span>-</span>
                                        @endif
                                    </div>
                                </div>
                                {{-- <div class="row">
                                    <div class="col-auto">
                                        <b>Pickup Time : </b>
                                        @if($status==1)
                                            {{$cust_pickup_time[$key]}}
                                        @else
                                            <span>-</span>
                                        @endif
                                    </div>
                                </div> --}}
                                <div class="row">
                                    <div class="col-auto">
                                        <b>Payment : </b>
                                        @if($status==0)
                                            @if($cust_payment_mode[$key]==0)
                                                <span>Cash</span>
                                            @elseif($cust_payment_mode[$key]==1)
                                                <span>Online</span>
                                            @else
                                                <span>-</span>
                                            @endif
                                        @else
                                            <span>-</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                    </div>
                @endforeach
            </div>
            @if(in_array(0,$product_status))
                <strong><p class="text-dark">You have decided to renew above some of the equipments and make the payment using below mode</p></strong>
                <strong><p class="text-dark">Online : {{array_sum($product_online_amount)}} <br>
                Cash : {{array_sum($product_cash_amount)}}</p> </strong>
                <strong><p class="text-dark">You can make online payment via <a href="https://rzp.io/l/2eDOVwr">https://rzp.io/l/2eDOVwr</a> or Google pay <a href="https://bit.ly/3b5q776">https://bit.ly/3b5q776</a><br>
                Please send us the screen shot of the payment along with your name and phone number or call us on <a href="tel:9820930915">9820930915</a> / <a href="tel:9167133150">9167133150</a></p></strong>
            @endif
            <strong><p class="text-dark">Our support team will get in touch with you shortly. In case you need immediate assistence then please call us at <a href="tel:{{$contact_no}}">{{$contact_no}}</a> / @if(isset($contact_no2))<a href="tel:{{$contact_no2}}">{{$contact_no2}}</a>@endif <span>({{$username}})</span> or checkout our website <a href="https://quali55care.com">Quali55care.com</a></p></strong>
        </div>
    </body>
</html>