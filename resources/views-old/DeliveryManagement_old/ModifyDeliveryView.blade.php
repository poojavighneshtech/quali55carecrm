<!DOCTYPE html>
<html lang="en">
    @extends('header_and_sidebar')
    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Delivery : Modify</title>
        @section('styles')
       
        
        @endsection
    </head>

        

<body id="page-top">	
		<!-- Page Wrapper -->
        @section('breadcrumb_item')
           
            <li class="breadcrumb-item active" aria-content="page">Modify Delivery</li>
        @endsection
            <div class="container">                
                @section('content')
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
                @if(session()->has('message_search'))
                    <div class="alert alert-danger">
                        {{ session()->get('message_search') }}
                    </div>
                @endif 
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-primary">
                            <div class="card-header text-center" style="background-color: #345bcc; color: white;">
                                <span><b>Modify Delivery</b></span>
                            </div>
                            <div class="card-body">
                                <table id="records" class="table table-bordered">
                                    <thead>
                                        <th>Sr.No.</th>
                                        <th>Order Id</th>
                                        <th>Customer Name</th>
                                        <th>Contact Number</th>
                                        <th>Equipment</th>
                                        <th>Action</th>
                                    </thead>
                                    <tbody>
                                        {{!$Srno = 1}}
                                        @foreach ($orders as $order)
                                            <tr>
                                                <td>{{$Srno}}</td>
                                                <td>{{$order['order_id']}}</td>
                                                <td>{{$order['shipping_first_name']}}</td>
                                                <td>{{$order['mobileno']}}</td>
                                                {{-- <td>{{$order['line_item_1']}}</td> --}}
                                                <td><center>---</center></td>
                                                <td>
                                                    <a class="btn btn-primary" href="{{url('/')}}/ModifyDelivery/{{$order['order_id']}}">Modify</a>
                                                </td>
                                            </tr>
                                            {{!$Srno++}}
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endsection
            </div>
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    </div>	   
    @section('script')    
    <script>

	</script>
    @endsection

    </body>
</html>