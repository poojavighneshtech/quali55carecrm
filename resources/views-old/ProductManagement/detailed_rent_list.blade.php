@extends('header_and_sidebar')
@section('title')
    Admin: Detailed Report
@endsection
@section('header')

@endsection
@section('content')
    <form action="<?php echo url('/');?>/update_product_status" method="POST">
        {{ csrf_field() }}
        <div class="">
            <!-- DataTales Example -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detailed List of Vendor Details and Products</h6>
                </div>
                <div class="card-body">
                    <center>
                        <select class="selectpicker" id="vendor" name="vendor" title="------Select Vendor------" data-live-search='true'>
                        <?php 
                            foreach ($vendor_names as $vendor_name)
                            {
                        ?>
                                <option value="{{$vendor_name['id']}}">{{$vendor_name['registered_name']}}</option>
                        <?php
                            }
                        ?>
                        </select>
                    </center>
                </div>
            </div>	                    
        </div>
        <!-- /.container-fluid -->
        
    </form>
@endsection

@section('script')
<script>
    $(document).ready(function(){
        $('#vendor').on('change',function(){
            var id = $(this).val();
            var dataString = id;
            //alert(dataString);
            $.ajax({
                type: "GET",
                url: "<?php echo url('/');?>/fetch_all_vendor_details/"+dataString,
                success: function(data)
                {
                    //alert(data);
                    var obj = jQuery.parseJSON(data);
                    console.log(obj);
                    var table = "<br><table class='table'><thead><th>Sr.No.</th><th>Product Name</th><th>Product Brand</th><th>Product Details</th><th>Warehouse_details</th><th>Contact Number</th></thead><tbody>"
                        for (var i=0; i<=obj.length; i++)
                        {
                            name = obj[0].registered_name;
                            var sr_no = i+1;
                            table +="<tr><td>"+sr_no+"</td><td>"+name+"</td></tr>"
                        }
                        table += "</tbody></table>";
                    $('.card-body').append(table);
                }
            });
        });
    });
</script>

@endsection