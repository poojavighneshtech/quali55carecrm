<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Customer Products</title>
    @section('header')
       
    @endsection
</head>

<body id="page-top">	
<!-- Page Wrapper -->

@extends('header_and_sidebar')
   
    
    @section('content')
    <div class="container">
        <br>
        @if(session()->has('message'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                {{ session()->get('message') }}
            </div>
        @endif
        <form class="form" method="post" action="{{url('/')}}/renewal_pickup_product">    
            {{ csrf_field() }}
            <div class="card">
                <div class="card-header" style="background-color: #337ab7; color: white;">
                    <center>
                        <b>Customer Products</b>
                    </center>
                </div>
                @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="alert alert-danger">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        {{$error}}
                    </div>
                @endforeach
                @endif
                <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
                                <label><b>Customer Name :</b></label>
                            </div>  
                            <div class="col-md-10 text-left">
                                <label>{{$products[0]['customer_name']}}</label>
                                <input type="hidden" name="customer_name" id="customer_name" value="{{$products[0]['customer_name']}}">
                            </div>  
                        </div>
                        <hr>
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table table-bordered " id="dataTable" cellspacing="0">
                                    <thead>
                                        <tr class="text-center">
                                            <th class="">
                                                <input type="checkbox" class="form-check-input" name="checkall" id="checkall" value="">
                                                <label for="checkall">Check</label>
                                            </th>
                                            <th>Sr.No</th>
                                            <th>Product_name</th>
                                            <th>Start Date</th>
                                            <th>Pickup Date</th>
                                            <th>city</th>
                                            <th>Rent</th>
                                            <th>Deposit</th>
                                            {{-- <th>Action</th> --}}
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @php
                                            $sn = 1;
                                            $count = 0;
                                        @endphp
                                        @foreach($products as $pd)
                                            <tr class="" data-count="{{$count}}">
                                                <td>
                                                    <div class="form-check text-center">
                                                        {{-- <input type="checkbox" class="form-check-input" name="check" id="check{{$count}}" value="{{$count}}" @if($pd['pickup_date']==date('Y-m-d'))checked @endif> --}}
                                                        <input type="checkbox" class="form-check-input" name="check[]" id="check{{$count}}" value="{{$count}}">
                                                        <input type="hidden" name="customer_id" id="customer_id" value="{{$customer_id}}">
                                                        <input type="hidden" name="customer_name" id="customer_name" value="{{$pd['customer_name']}}">
                                                    </div>
                                                </td>
                                                <td class="text-center">{{$sn}}</td>
                                                <td>
                                                    {{$pd['product_name']}}

                                                    <input type="hidden" name="product_name[]" id="product_name{{$count}}" value="{{$pd['product_name']}}">
                                                    {{-- id --}}
                                                    <input type="hidden" name="order_details_id[]" id="order_details_id{{$count}}" value="{{$pd['order_details_id']}}">
                                                    {{-- order id --}}
                                                    <input type="hidden" name="order_id[]" id="order_id{{$count}}" value="{{$pd['order_id']}}">
                                                    {{-- product id --}}
                                                    <input type="hidden" name="product_id[]" id="product_id{{$count}}" value="{{$pd['product_id']}}">
                                                </td>
                                                <td>
                                                    {{date('d-m-Y',strtotime($pd['creation_date']))}}
                                                </td>
                                                <td>
                                                    {{date('d-m-Y',strtotime($pd['pickup_date']))}}
                                                    <input type="hidden" name="pickup_date[]" id="pickup_date{{$count}}" value="{{$pd['pickup_date']}}">
                                                </td>
                                                <td>
                                                    {{$pd['city']}}
                                                </td>
                                                <td>
                                                    {{$pd['product_rent']}}
                                                    <input type="hidden" name="product_rent[]" id="product_rent{{$count}}" value="{{$pd['product_rent']}}">
                                                </td>
                                                <td>
                                                    {{$pd['product_deposite']}}
                                                    <input type="hidden" name="product_deposite[]" id="product_deposite{{$count}}" value="{{$pd['product_deposite']}}">
                                                </td>
                                                {{-- <td>
                                                    <select class="selectpicker Individual form-control" title="Select option" id="select{{$count}}" data-width="fit"  required="true">
                                                        <option value="pickup">Pickup</option>
                                                        <option value="renewal">Renewal</option>
                                                    </select>
                                                </td> --}}
                                            </tr>
                                            @php
                                                $sn++;
                                                $count = $count + 1;
                                            @endphp
                                        @endforeach
                                    </tbody>
                                </table>
                                <input type="hidden" name="row_ct" id="row_ct" value="{{$count}}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <center>
                                    <button type="submit" class="btn btn-primary" id="renewal" name="renewal_pickup_btn" value="Renew">Renew</button>
                                    <button type="submit" class="btn btn-primary" id="pickup" name="renewal_pickup_btn" value="Pickup">Pickup</button>
                                    <button type="button" class="btn btn-outline-warning" id="send_reminder" name="renewal_pickup_btn" value="Send_Reminder">Send Reminder</button>
                                    {{-- <input type="submit" class="btn btn-primary" id="renewal" name="renewal" value="Renewal">    
                                    <input type="submit" class="btn btn-primary" id="pickup" name="pickup" value="Pickup">    
                                    <input type="submit" class="btn btn-outline-warning" id="send_reminder" name="send_reminder" value="Send Reminder">     --}}
                                </center>
                            </div>
                        </div>
                </div>

                  <!-- Modal: modalAbandonedCart-->
                <div class="modal fade" id="pop_msg_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                {{-- <h5 class="modal-title" id="exampleModalLabel">Reminder Sent Successfully</h5> --}}
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>reminder send to <b><span id="pop_cust_name"></span></b></p>
                                <p>reminder send for product renew or pickup</p>
                                <div id="div_pop_table"></div>
                                {{-- <table class="table table-bordered" id="records">
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Pickup Date</th>
                                            <th>Product Rent</th>
                                        </tr>
                                    </thead>
                                    <tbody> --}}
                                    {{-- @if(session()->has('pop_data'))
                                        {{!$items = session()->get('pop_data')}}
                                        
                                        @for($i=0; $i < count($items['product_name']); $i++)
                                            <tr>
                                                <td>
                                                    {{$items['product_name'][$i]}}
                                                </td>
                                                <td>
                                                    {{$items['pickup_date'][$i]}}
                                                </td>
                                                <td>
                                                    {{$items['product_rent'][$i]}}
                                                </td>
                                            </tr>
                                        @endfor                                  
                                    @endif --}}
                                    {{-- </tbody>
                                </table> --}}
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="Submit" class="btn btn-outline-primary" id="send_reminder" name="renewal_pickup_btn" value="Send_Reminder">Send Reminder</button>
                                
                                {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal: modalAbandonedCart-->                        

            </div>
        </form>
    </div>   
    @endsection
</body>
    @section('script')
    {{-- @if(session()->has('message'))
        <script>
            $(function() {
                $('#pop_msg_modal').modal('show');
            });
        </script>
    @endif --}}
        <script>
            $(document).ready(function(){
                //---check and uncheck all checkbox
                // $('#checkall').click(function() 
                // {
                //     if($(this).is(':checked'))
                //     {
                //         var count = $('#row_ct').val();
                //         for(i=0;i<count;i++)
                //         {
                //             $('#check'+i).prop('checked',true);

                //         }
                //     }
                //     else
                //     {
                //         var count = $('#row_ct').val();
                //         for(i=0;i<count;i++)
                //         {
                //             $('#check'+i).prop('checked',false);

                //         }
                //     }
                // });

                // var count = $('#row_ct').val();
                // for(i=0;i<count;i++)
                // {
                  
                //     $('#check'+i).on("click",function()
                //     {
                //         $('#checkall').prop('checked',false);
                //     });

                // }

                
                $('#send_reminder').click(function(){
                    var product_name = [];
                    var pickup_date = [];
                    var product_rent = [];
                    $.each($('input[name="check[]"]:checked'),function(){  
                        var check_val = $(this).val();
                        //favorite.push($(this).val());
                        product_name.push($('#product_name'+check_val).val());
                        pickup_date.push($('#pickup_date'+check_val).val());
                        product_rent.push($('#product_rent'+check_val).val());
                    });
                        var get_cust_name = $('#customer_name').val();
                        $('#pop_cust_name').text(get_cust_name);
                        $('#div_pop_table').empty();
                        $('#div_pop_table').append('<table id="pop_table" class="table table-bordered"><thead><tr><th>Product Name</th> <th>Pickup Date</th> <th>Product Rent</th></tr><thead><tbody></tbody></table>');
                        for(var i = 0; i < product_name.length; i++)
                        {

                            $('#pop_table tbody').append('<tr><td>'+product_name[i]+'</td> <td>'+pickup_date[i]+'</td> <td>'+product_rent[i]+'</td></tr>');
                            
                        }
                            $('#pop_msg_modal').modal('show');

                    // var data_product_name = JSON.stringify(product_name);
                    // var data_pickup_date = JSON.stringify(pickup_date);
                    // var data_product_rent = JSON.stringify(product_rent);
                    // var dataString = ({_token:"{{ csrf_token() }}",product_name:""+data_product_name,pickup_date:""+data_pickup_date,product_rent:""+data_product_rent});
                    // $.ajax({
                    //     type: "POST",
                    //     url: "<?php echo url('/');?>/ajax_send_reminder",
                    //     data: dataString,
                    //     cache: false,
                    //     success: function(data)
                    //     {
                    //         var reminder_data = jQuery.parseJSON(data);
                    //         console.log(reminder_data);
                    //         //var vendorsLength = vendors.length;
                    //         // // var temp_var = i-2;
                    //         // $("#vendors"+0)
                    //         // .find("option")
                    //         // .remove()
                    //         // .end();
                    //         // for(var j = 0; j < vendorsLength; j++)
                    //         // {
                    //         //     $("#vendors"+0).append("<option value='"+vendors[j].vendor_id+"'>"+vendors[j].vendor_name+"</option>");
                    //         // }
                    //         // $('#vendors'+0).selectpicker('refresh');
                    //     }
                    // });
                    //alert("My favourite sports are: " + favorite.join(", "));
                    
                });
               
            });
        </script>                                                         
    @endsection
</html>