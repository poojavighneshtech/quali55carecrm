<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Order Feedback</title>
    {{-- <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script> --}}
    @section('styles')
       <style>
           /* [IMAGE] */
            .zoomD {
            width: 200px;
            height: 150px;
            cursor: pointer;
            }
            .zoomDsign {
            width: 200px;
            height: 100px;
            cursor: pointer;
            }

            /* [LIGHTBOX BACKGROUND] */
            #lb-back {
            position: fixed;
            top: 11vh;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            /* background: rgba(255,255,255); */
            z-index: 999;
            visibility: hidden;
            opacity: 0;
            transition: all ease 0.4s;
            }
            #lb-back.show {
            visibility: visible;
            opacity: 1;
            }

            /* [LIGHTBOX IMAGE] */
            #lb-img {
            position: relative;
            top: 50%;
            transform: translateY(-50%);
            text-align: center;
            }
            #lb-img img {
            /* You might want to play around with 
                width, height, max-width, max-height
                to fit portrait / landscape pictures properly. */
            width: 600px;
            height: 650px;
            
            /* ALTERNATE EXAMPLE
            width: 100%;
            max-width: 1200px;
            height: auto;
            margin: 0 auto; */
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

                <div class="card">
                    <div class="card-header"  style="background-color: #4a6fdc; color: white;" >
                        <center>Order's Feedback</center>
                    </div> 
                    <div class="card-body">
                        <div class="table">
                            <table class="table table-bordered" id="records">
                                <thead>
                                    <th>Sr.No</th>
                                    <th>Order ID</th>
                                    <th>Customer Name</th>
                                    <th>Product Name</th>
                                    <th>Sign</th>
                                    <th>Product Image</th>
                                    <th>Comment</th>
                                </thead>
                                <tbody>
                                    <?php $srno=1;?>
                                    @foreach($feedback_info as $FI)
                                        <tr>
                                            <td class="text-center">{{$srno}}</td>
                                            <td>{{$FI['order_id']}}</td>
                                            <td>{{$FI['shipping_first_name']}}</td>
                                            <td>{{$FI['line_item_1']}}</td>
                                            <td><img src="http://{{$FI['cust_sign']}}" class="rounded " id=""  alt="" width="200" /></td>
                                            <td><img src="http://{{$FI['product_delivered']}}" class="rounded zoomD" id=""  alt="" width="200" height="200"/></td>
                                            <td>{{$FI['custcomments']}}</td>
                                            {{-- <td><a href="{{url('/')}}/perticular_feedback/{{$FI['order_id']}}" class="btn btn-outline-primary btn-rounded">View Feedback</a></td> --}}
                                        </tr>
                                        <?php $srno++;?>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div id="lb-back">
                    <div id="lb-img"></div>
                </div>
            </div>
        </div>
    @endsection
</body>
@section('script')
    <script>
        var zoomImg = function () {
        // (A) CREATE EVIL IMAGE CLONE
            var clone = this.cloneNode();
            clone.classList.remove("zoomD");

            // (B) PUT EVIL CLONE INTO LIGHTBOX
            var lb = document.getElementById("lb-img");
            lb.innerHTML = "";
            lb.appendChild(clone);

            // (C) SHOW LIGHTBOX
            lb = document.getElementById("lb-back");
            lb.classList.add("show");
            };

            window.addEventListener("load", function(){
            // (D) ATTACH ON CLICK EVENTS TO ALL .ZOOMD IMAGES
            var images = document.getElementsByClassName("zoomD");
            if (images.length>0) {
                for (var img of images) {
                img.addEventListener("click", zoomImg);
                }
            }

            // (E) CLICK EVENT TO HIDE THE LIGHTBOX
            document.getElementById("lb-back").addEventListener("click", function(){
                this.classList.remove("show");
            })
        });
        $(document).ready(function()
        {    
            $('#records_filter').append("<label>Filter: <select class='form-control-sm' id='filter' name='filter'><option disabled selected>-----Select-----</option><option value='today'>Today</option><option value='yesterday'>Yesterday</option><option value='past_3_days'>Past 3 Days</option><option value='week'>1 Week</option><option value='month'>Current Month</option><option value='all'>All</option></select></label>"); 
            if(localStorage['filtered'] != null)
            {
                $('#filter').val(localStorage['filtered']);
            }
            $('#filter').on("change",function(){
                var filter_by = $('#filter').val();
                var section = "All_Leads";
                localStorage['filtered'] = filter_by;
                //alert(filter_by);
                var dataString = (filter_by);
                var url = "<?php echo url('/');?>/filterFeedback/"+dataString;
                window.location.assign(url);
            });
        });
    </script>
    @endsection
</html>