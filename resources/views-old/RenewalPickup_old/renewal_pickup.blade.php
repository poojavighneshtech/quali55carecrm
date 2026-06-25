<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Renewal Pickup</title>
    {{-- <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script> --}}
    @section('styles')
        
        <style>
            /* .morecontent span {
                display: none;
            }
            .morelink {
                display: block;
            } */
            #records tbody td{
                padding: 3px;
            }
        </style>
    @endsection
</head>

<body id="page-top">	
		<!-- Page Wrapper -->
    
    @extends('header_and_sidebar')
        
    @section('content')
        <div class="leads">
            <div class="container">  
                @if(session()->has('message') || session()->has('message_pop') )
                    <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        {{ session()->get('message')}} <small><a class="" href="{{ session()->get('collection_url')}}">See Order Here</a></small>
                        {{ session()->get('message_pop')}}
                    </div>
                @endif
                @if(session()->has('reminder_msg'))
                    <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        {{ session()->get('reminder_msg')}} 
                    </div>
                @endif
                @if(session()->has('message_delete'))
                    <div class="alert alert-danger">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        {{ session()->get('message_delete') }}
                    </div>
                @endif 
                @if(session()->has('error'))
                    <div class="alert alert-danger">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        {{ session()->get('error') }}
                    </div>
                @endif 
                {{-- <form action="{{url('/')}}/renewal_pickup_product" method="post"> --}}
                {{-- {{csrf_field()}} --}}
                    <div class="card">
                        <div class="card-header"  style="background-color: #4a6fdc; color: white;" >
                            <center>Renewal Pickup</center>
                        </div> 
                        <div class="card-body">
                        
                            <div class="row ">
                                <div class="col-md-1">
                                    <label><b>Filter</b></label>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group" required="true">
                                        <input type="radio" class="form-group" id="today" name="date_filter" data-id="today" value="today"><label for="today">Today</label>
                                        &emsp;
                                        <input type="radio" class="form-group" id="tomorrow" name="date_filter" data-id="tomorrow" value="tomorrow" > <label for="tomorrow">Tomorrow</label>
                                        &emsp;
                                        <input type="radio" class="form-group" id="overdue" name="date_filter" data-id="overdue" value="overdue"> <label for="overdue">Overdue</label>
                                        &emsp;
                                        <input type="radio" class="form-group" id="3_days" name="date_filter" data-id="3_days" value="3_days"> <label for="3_days">Next 3 Days</label>
                                        &emsp;
                                        <input type="radio" class="form-group" id="all" name="date_filter" data-id="all" value="all"> <label for="all">All</label>
                                    </div>
                                </div>
                            </div>   

                           
                            <!-- Search form -->
                            <select class="form-control selectpicker border rounded" name="search_customer" id="search_customer" data-live-search="true">
                                <option value="" disabled selected >Search customer here</option>
                                @foreach ($customer_products_details as $customer_products_detail) 
                                    <option value="{{$customer_products_detail['customer_id']}}">{{$customer_products_detail['customer_name']}}</option>
                                @endforeach
                            </select>
                            <form action="{{url('/')}}/renewal_pickup_product" method="post">
                                @csrf

                                <table class="table table-bordered table-striped table-hover" id="records" width="100%">
                                    <thead>
                                        <tr>
                                            <th scope="col">Sr.No.</th>
                                            <th scope="col">Due Date&emsp;</th>
                                            <th scope="col">Customer Name</th>
                                            <th scope="col">Address</th>
                                            <th scope="col">Products</th>
                                            <th scope="col">Comment</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $i =0;
                                            $srno=1;
                                        @endphp
                                        @if($customer_products_details !=null)
                                            @foreach ($customer_products_details as $customer_products_detail) 
                                                <tr scope="row" data-toggle="collapse" data-target="#demo{{$i}}" class="data-toggle" data-id="{{$i}}" >
                                                    <td>{{$srno}}</td>
                                                    <td>{{date('d-m-Y',strtotime($customer_products_detail['product_details'][0]['pickup_date']))}}</td>
                                                    <td>{{$customer_products_detail['customer_name']}}</td>
                                                    <td>{{$customer_products_detail['customer_address']}}</td>
                                                    <td>{{count($customer_products_detail['product_details'])}}</td>
                                                    <td>
                                                        {{substr($customer_products_detail['customer_log'],0,100)}}
                                                        <a class="btn btn-sm" href="" data-toggle="popover" title="Customer Log" data-content="{{$customer_products_detail['customer_log']}}"
                                                            data-book-id="{{$customer_products_detail['customer_log']}}">...</a>
                                                    </td>
                                                </tr>
                                                <tr data-id="{{$i}}" scope="row">
                                                    <td colspan="12" class="hiddenRow">
                                                        <div class="collapse" id="demo{{$i}}">
                                                            
                                                            {{-- hidden values --}}
                                                            <input type="hidden" name="cust_id" value="{{$customer_products_detail['customer_id']}}">
                                                            <input type="hidden" name="customer_id" id="customer_id{{$i}}" value="{{$customer_products_detail['customer_id']}}">
                                                            <input type="hidden" name="customer_name" id="customer_name{{$i}}" value="{{$customer_products_detail['customer_name']}}">
                                                            <input type="hidden" name="r_count" id="r_count{{$i}}" value="{{count($customer_products_detail['product_details'])}}">

                                                            <table class="table table-bordered table-sm " id="InTable{{$i}}" width="100%">
                                                                <thead class="thead-light" style="background-color: #476dda; color:white;">
                                                                    <tr>
                                                                        <th class="nosort">
                                                                            &emsp;&emsp;<input type="checkbox" class="form-check-input" name="check_all[]" id="check_all{{$i}}" value="{{$i}}" data-r_count="{{count($customer_products_detail['product_details'])}}">
                                                                            Sr.No
                                                                        </th>
                                                                        <th>Due Date</th>
                                                                        <th>Order ID</th>
                                                                        <th>Product Name</th>
                                                                        <th>Rent</th>
                                                                        <th>Deposit</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    {{!$in_sr=1}}
                                                                    @for($j = 0; $j <count($customer_products_detail['product_details']); $j++)
                                                                            {{!$p_date = date('Y-m-d',strtotime($customer_products_detail['product_details'][$j]['pickup_date']))}}
                                                                            {{!$today = date('Y-m-d')}}
                                                                        <tr class=" @if($p_date<$today){{"table-danger text-black"}} @else{{"table-light"}}@endif " data-in_row_id ="{{$j}}">
                                                                            <td>
                                                                                &emsp;&emsp;<input type="checkbox" class="form-check-input" name="check[{{$i}}][]" id="single_check{{$i.$j}}" value="{{$j}}">
                                                                                {{-- <input type="hidden" name="check[{{$i}}][{{$j}}]['table_id']" id="single_check{{$i}}"value="{{$i}}"> --}}
                                                                                {{$in_sr}} 
                                                                            </td>
                                                                            <td>
                                                                                {{date('d-m-Y',strtotime($customer_products_detail['product_details'][$j]['pickup_date']))}}
                                                                            </td>
                                                                            <td>{{$customer_products_detail['product_details'][$j]['order_id']}}</td>
                                                                            <td>{{$customer_products_detail['product_details'][$j]['product_name']}}</td>
                                                                            <td>{{$customer_products_detail['product_details'][$j]['product_rent']}}</td>
                                                                            <td>{{$customer_products_detail['product_details'][$j]['product_deposite']}}</td>
                                                                            
                                                                            {{-- hidden values --}}
                                                                            <input type="hidden" name="order_id[{{$i}}][]" id="order_id{{$j}}" value="{{$customer_products_detail['product_details'][$j]['order_id']}}">
                                                                            <input type="hidden" name="order_details_id[{{$i}}][]" value="{{$customer_products_detail['product_details'][$j]['order_details_id']}}">
                                                                            <input type="hidden" name="product_id[{{$i}}][]"  value="{{$customer_products_detail['product_details'][$j]['product_id']}}">
                                                                            <input type="hidden" name="product_name[{{$i}}][]" id="product_name{{$j}}" value="{{$customer_products_detail['product_details'][$j]['product_name']}}">
                                                                            <input type="hidden" name="pickup_date[{{$i}}][]" id="pickup_date{{$j}}" value="{{$customer_products_detail['product_details'][$j]['pickup_date']}}">
                                                                            <input type="hidden" name="product_rent[{{$i}}][]" id="product_rent{{$j}}"  value="{{$customer_products_detail['product_details'][$j]['product_rent']}}">
                                                                            <input type="hidden" name="product_deposite[{{$i}}][]" value="{{$customer_products_detail['product_details'][$j]['product_deposite']}}">
                                                                        </tr>
                                                                        {{!$in_sr++}}
                                                                    @endfor
                                                                
                                                                </tbody>
                                                            </table>
                                                            <input type="hidden" name="in_row_ct" id="in_row_ct" value="{{$j}}">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <center>
                                                                        <button type="submit" class="btn btn-primary" id="renewal_btn{{$i}}" name="renewal_pickup_btn" value="Renew" disabled>Renew</button>
                                                                        <button type="submit" class="btn btn-primary" id="pickup_btn{{$i}}" name="renewal_pickup_btn" value="Pickup" disabled>Pickup</button>
                                                                        <button type="button" class="btn btn-outline-warning" id="send_reminder{{$i}}" name="renewal_pickup_btn" value="Send_Reminder" disabled>Send Reminder</button>
                                                                        {{-- <input type="submit" class="btn btn-primary" id="renewal" name="renewal" value="Renewal">    
                                                                        <input type="submit" class="btn btn-primary" id="pickup" name="pickup" value="Pickup">    
                                                                        <input type="submit" class="btn btn-outline-warning" id="send_reminder" name="send_reminder" value="Send Reminder">--}}
                                                                    </center>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                    </td>
                                                </tr>
                                                @php
                                                    $srno++;
                                                    $i++;
                                                @endphp
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="12"><center><h3>No records found</h3></center></td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </form>
                            <input type="hidden" name="out_row_ct" id="out_row_ct" value="{{$i}}">        
                        </div>

                        

                        <!-- Modal: modalAbandonedCart-->
                        <div class="modal fade" id="pop_msg_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <form action="{{url('/')}}/send_reminder" method="post">
                                        @csrf
                                        <div class="modal-header bg-success text-white">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            {{-- <p>reminder send to <b><span id="pop_cust_name"></span></b></p> --}}
                                            <input type="hidden" name="pop_cust_id" id="pop_cust_id" value="">
                                            <input type="hidden" name="pop_cust_name" id="pop_cust_name" value="">
                                            <p>Send Reminder to Customer</p>
                                            <p>reminder send for product renew or pickup</p>
                                            <div id="div_pop_table"></div>
                                            <input type="hidden" name="hid" value="df">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="Submit" class="btn btn-outline-primary" id="send_reminder" name="renewal_pickup_btn" value="Send_Reminder">Send Reminder</button>
                                            {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Modal: modalAbandonedCart-->
                    </div>
                {{-- </form> --}}
            </div>
        </div>
    @endsection
</body>
@section('script')
    {{-- @if(session()->has('message_pop') || session()->has('pop_per_cust_name'))
        <script>
            $(function() {
                $('#pop_msg_modal').modal('show');
            });
        </script>
    @endif --}}
    
<script>
    document.querySelector('input[name=date_filter][id=today]').checked = true;
//  $(document).ready(function(){ $('#records').DataTable(); });
    $('document').ready(function() {
        var count = $('#out_row_ct').val();
        for(i=0;i<count;i++)
        {
            $('#InTable'+i).dataTable({
                "bPaginate": true,
                "bJQueryUI": true, 
                "bLengthChange": true,
                "bFilter": true,
                "bSort": false,
                "bInfo": true,
                "bAutoWidth": true,
                "bProcessing": true,
                "iDisplayLength": 25,
                });
                //$('#InTable'+i).DataTable();
        }
        
        if(localStorage['filtered']=="today")
        {
            $('#today').attr('checked','checked');
        }
        if(localStorage['filtered']=="all")
        {
            $('#all').attr('checked','checked');
        }
        if(localStorage['filtered']=="tomorrow")
        {
            $('#tomorrow').attr('checked','checked');
        }
        if(localStorage['filtered']=="overdue")
        {
            $('#overdue').attr('checked','checked');
        }
        if(localStorage['filtered']=="3_days")
        {
            $('#3_days').attr('checked','checked');
        }

    });

    $('#records tr').click(function() {    
        var count = this.dataset.count;
           
        var user_id = $('#user_id'+count).val(); 
        var customer_id = $('#customer_id'+count).val();
        //document.getElementById('add_customer_comment').href ="<?php echo url('/');?>/add_customer_comment/"+user_id+"/"+customer_id;
        // window.location.assign(url);
        var in_count = this.dataset.id;
        var r_count = $('#r_count'+in_count).val();
        $('#check_all'+in_count).click(function() 
        {   
            //var inRowCount = $('#InTable'+in_count+' tr').length;
            //alert(inRowCount);
            var r_count = this.dataset.r_count;
            if($(this).is(':checked'))
            {
                for(j=0;j<r_count;j++)
                {
                    $('#single_check'+in_count+j).prop('checked',true);
                }
                $('#renewal_btn'+in_count).attr('disabled',false);
                $('#pickup_btn'+in_count).attr('disabled',false);
                $('#send_reminder'+in_count).attr('disabled',false);
            }
            else
            {
                for(j=0;j<r_count;j++)
                {
                    $('#single_check'+in_count+j).prop('checked',false);
                }
                $('#renewal_btn'+in_count).attr('disabled',true);
                $('#pickup_btn'+in_count).attr('disabled',true);
                $('#send_reminder'+in_count).attr('disabled',true);
            }
        });
        
        for(j=0;j<r_count;j++)
        {
            
            $('#single_check'+in_count+j).click(function(){
                if($(this).is(':checked'))
                {
                    $('#renewal_btn'+in_count).attr('disabled',false);
                    $('#pickup_btn'+in_count).attr('disabled',false);
                    $('#send_reminder'+in_count).attr('disabled',false);
                }
                else
                {
                    $('#renewal_btn'+in_count).attr('disabled',true);
                    $('#pickup_btn'+in_count).attr('disabled',true);
                    $('#send_reminder'+in_count).attr('disabled',true);
                }
            });
        }
        $('#send_reminder'+in_count).click(function(){
            var product_name = [];
            var pickup_date = [];
            var product_rent = [];
            for(j=0;j<r_count;j++)
            {
                if($('#single_check'+in_count+j).is(':checked'))
                {
                    var check_val = $('#single_check'+in_count+j).val();
                    product_name.push($('#product_name'+check_val).val());
                    pickup_date.push($('#pickup_date'+check_val).val());
                    product_rent.push($('#product_rent'+check_val).val());
                }
            }
            
            var get_cust_name = $('#customer_name'+in_count).val();
            var get_cust_id = $('#customer_id'+in_count).val();
            $('#pop_cust_name').val(get_cust_name);
            $('#pop_cust_id').val(get_cust_id);
            $('#div_pop_table').empty();
            $('#div_pop_table').append('<table id="pop_table" class="table table-bordered"><thead><tr><th>Product Name</th> <th>Pickup Date</th> <th>Product Rent</th></tr><thead><tbody></tbody></table>');
            for(var i = 0; i < product_name.length; i++)
            {

                $('#pop_table tbody').append('<tr><td>'+product_name[i]+'<input type="hidden" name="product_name[]" id="product_name" value="'+product_name[i]+'"> </td> <td>'+pickup_date[i]+'<input type="hidden" name="pickup_date[]" id="pickup_date" value="'+pickup_date[i]+'"> </td> <td>'+product_rent[i]+' <input type="hidden" name="product_rent[]" id="product_rent" value="'+product_rent[i]+'"> </td></tr>');
                
            }
                $('#pop_msg_modal').modal('show');
        });

        var r_count = $('#r_count'+in_count).val();
       
    });

    // $('#my_modal').on('show.bs.modal', function(e) {
    //     var bookId = $(e.relatedTarget).data('book-id');
    //     $(e.currentTarget).find('pre[name="bookId"]').text(bookId);
    // });

    $('input[type=radio][name=date_filter]').change('click',function() {                
        var filter_val = $(this).val();
        var filtered_id = $(this).attr('data-id');
        localStorage['filtered'] = filtered_id;
        // //alert(filter_by);
        var dataString = (filter_val);
        var url = "<?php echo url('/');?>/renewal_pickup/"+dataString+"/"+"All";
        window.location.assign(url);
        // $.ajax({
        //     type: "GET",
        //     url: "<?php echo url('/');?>/filterLeads/"+dataString,
        //     cache: false,
        //     success: function(data){
        //         console.log(data);
        //         //$('#container').html(data.html);
        //     }
        // })
    });

   

    $("#search_customer").on("change",function()
    {
        var cust_id = $(this).val();
        var filter_val = localStorage['filtered'];
        var url = "<?php echo url('/');?>/renewal_pickup/"+filter_val+"/"+cust_id;
        window.location.assign(url);

    });

    $(document).ready(function() {
        $('[data-toggle="popover"]').popover({
            placement: 'left',
            trigger: 'hover'
        });
    });

    
</script>     

@endsection
</html>