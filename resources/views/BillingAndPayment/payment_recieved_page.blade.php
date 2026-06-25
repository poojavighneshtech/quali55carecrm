<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Payment Recieved</title>
    {{--
    <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script> --}}
    @section('styles')

    @endsection
</head>

<body id="page-top">
    <!-- Page Wrapper -->

    @extends('header_and_sidebar')
{{-- @extends('new-sidebar') --}}


    @section('content')
    <div class="leads">
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
        <form action="{{url('/')}}/payment_recieved/{{$order_data['collection_order_id']}}" method="post" enctype="multipart/form-data">
            @csrf
            {{-- <div class="card ">
                <div class="card-header">
                    Order Details
                </div>
                <div class="card-body"> --}}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-light mb-3">
                                <div class="card-header">Customer Details</div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Customer Name :</strong></p>
                                        </div>
                                        <div class="col-md-6">
                                            {{$order_data['customer_details'][0]['customer_name']}}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-5">
                                            <p><strong>Contact No:</strong></p>
                                        </div>
                                        <div class="col-md-7">
                                            {{$order_data['customer_details'][0]['primary_contact_no']}}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p><strong>Adress :</strong></p>
                                        </div>
                                        <div class="col-md-8">
                                            {{$order_data['customer_details'][0]['address_line_1']}},
                                            {{$order_data['customer_details'][0]['address_line_2']}},
                                            {{$order_data['customer_details'][0]['area']}},
                                            {{$order_data['customer_details'][0]['landmark']}},
                                            {{$order_data['customer_details'][0]['city']}},
                                            {{$order_data['customer_details'][0]['pincode']}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">Product Details</div>
                                <div class="table table-responsive jim-table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Sr No</th>
                                                <th>Product Name</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Product Rent</th>
                                                <th>Collect Rent</th>
                                                <th>Deposit Adjusted</th>
                                                <th>Discount</th>
                                            </tr>
                                        </thead>
                                        @php
                                        $srno=1;
                                        @endphp
                                        @foreach($order_data['product_details'] as $product_detail)
                                        <tr>
                                            <td data-label="Sr No">{{$srno}}
                                                <input type="hidden" name="order_details_id[]"
                                                    id="order_details_id{{$srno-1}}"
                                                    value="{{$product_detail['order_details_id']}}"></td>
                                            <td data-label="Product Name">{{$product_detail['product_name']}}</td>
                                            <td class="text-nowrap" data-label="Start Date">
                                                {{date('d-M-y',strtotime($product_detail['start_date']))}}
                                                <input type="hidden" name="renew_date[]" id="renew_date{{$srno-1}}" value="{{$product_detail['start_date']}}">
                                            </td>
                                            <td class="text-nowrap" data-label="End Date">{{date('d-M-y',strtotime($product_detail['end_date']))}}</td>
                                            <td data-label="Product Rent">{{$product_detail['product_rent']}}</td>
                                            <td data-label="Amount">{{$product_detail['online_amount']}}</td>
                                            <td data-label="Deposit Adjust">{{$product_detail['adjusted_deposit']}}</td>
                                            <td data-label="Discount">
                                                @if($product_detail['discount_amt']!=null)
                                                    {{$product_detail['discount_amt']}}
                                                @else
                                                    0
                                                @endif
                                            </td>
                                        </tr>
                                        @php
                                        $srno++;
                                        @endphp
                                        @endforeach
                                        <tr>
                                            <td colspan="6" class="text-right" data-label="Total Rent"><strong>Total</strong>: 
                                                {{$order_data['total_rent']}}
                                                <input type="hidden" name="total_collected_amount" value="{{$order_data['total_rent']}}">
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    Payment Details
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <div class="row form-group">
                                                <div class="col-md-3">
                                                    <label for="collected_at"><strong>Collected At :</strong></label>    
                                                </div>
                                                <div class="col-md-9">
                                                    <span>
                                                        {{-- {{date('d-M-Y',strtotime($collected_at))}} --}}
                                                        <input type="date" class="form-control" name="collected_at" id="collected_at" value="{{date('Y-m-d')}}">
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="reference_id"><strong>Reference ID</strong></label>
                                                <input type="text" class="form-control" placeholder="Reference ID" name="reference_id" id="reference_id" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="payment_mode"><strong>Payment Mode</strong></label>
                                                <select class="form-control selectpicker" title="Payment Mode" name="payment_mode" id="payment_mode" required>
                                                    <option value="Gpay">Google Pay</option>
                                                    <option value="Razor Pay">Razor Pay</option>
                                                    <option value="Phone Pe">Phone Pe</option>
                                                    <option value="Paytm">Paytm</option>
                                                    <option value="Online Banking">Online Banking</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="reference_image"><strong>Reference Image</strong></label>
                                                <div class="custom-file mb-3">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" name="reference_image" id="reference_image" aria-describedby="reference_image" accept="image/*">
                                                        <label class="custom-file-label" for="reference_image">Choose file</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="comment"><strong>Comment</strong></label>
                                                <textarea class="form-control" name="comment" id="comment" cols="30" placeholder="Comment" rows="8" required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-4">
                                            <input type="submit" class="btn btn-outline-success btn-block">
                                        </div>
                                        <div class="col-md-4"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                {{-- </div>
            </div> --}}
        </form>
    </div>
    @endsection
</body>
@section('script')
<script>
document.querySelector('.custom-file-input').addEventListener('change',function(e){
  var fileName = document.getElementById("reference_image").files[0].name;
  var nextSibling = e.target.nextElementSibling
  nextSibling.innerText = fileName
});

</script>
@endsection

</html>