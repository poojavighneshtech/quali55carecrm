<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Consumables</title>    
    <link href="{{url('/')}}/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">    
    <link href="<?php echo url('/');?>/assets/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link href="{{url('/')}}/assets/css/sb-admin-2.min.css" rel="stylesheet">   
</head>
<body class="bg-dark">
    <div class="container">
        <div class="row my-2">
            <div class="col-md-3"></div>
            <div class="col-md-6 bg-white">
                @if(session()->has('message'))
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>
                @endif
                @if(session()->has('error'))
                    <div class="alert alert-danger">
                        {{ session()->get('error') }}
                    </div>
                @endif 
                <div class="row">
                    <div class="col text-center">
                        <img class="img-fluid rounde" src="<?php echo url('/');?>/assets/images/LogoGadm.png">
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col text-dark">
                        <form action="{{route('consumables-form-submit')}}" method="POST">
                            @csrf
                            <p class="text-justify text-dark">
                                Dear customer, <br>
                                Thank you for allowing us to serve you.<br><br>
                                We have also large varieties of Consumables and Patient support products on Quali55Care store.<br>
                                Save on your daily patient consumables and get upto <b>50%</b> discount.
                            </p>
                            <input type="hidden" name="contactno" id="contactno" value="{{$contact}}">
                            <input type="hidden" name="orderid" id="orderid" value="{{$order}}">
                            <input type="hidden" name="prevurl" id="prevurl" value="{{url()->full()}}">
                            <p class="text-center">Products</p>
                            <div class="row form-group">
                                @foreach($products as $key=>$product)
                                    <div class="col-md-6">
                                        <input type="checkbox" name="products[]" id="{{$key}}" value="{{$product}}">
                                        <label for="{{$key}}">{{$product}}</label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="row">
                                <div class="col text-center">
                                    <button type="submit" class="btn btn-outline-success btn-sm" name="form_submit" value="submit" id="form_submit">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
    <script src="{{url('/')}}/assets/vendor/jquery/jquery.min.js"></script>
    <script src="{{url('/')}}/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{url('/')}}/assets/vendor/jquery/jquery.cookie.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
</html>