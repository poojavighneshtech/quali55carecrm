@extends('header_and_sidebar')
@section('title')
    Admin: Vendor Product Requests. 
@endsection
@section('content')
    <form action="<?php echo url('/');?>/update_product_status" method="POST">
        {{ csrf_field() }}
        <div class="container-fluid">
            <!-- DataTales Example -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Vendor Product Requests</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-9">
                        </div>
                        <div class="col-md-3">
                            <div class="row">
                                <div class="col-md-4 text-right">
                                    <label for="city_filter">City Filter</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" list="city" name="city_filter" id="city_filter" class="form-control">
                                    <datalist id="city">
                                        @foreach ($main_cities as $city) 
                                            <option value="{{$city['city_name']}}">{{$city['city_name']}}</option>
                                        @endforeach
                                            <option value="Pune">Pune</option>
                                    </datalist>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive jim-table-responsive">
                       <table class="table table-striped table-hover text-center resposive">
                            <thead>
                                <tr>
                                    {{-- <th></th> --}}
                                    <th>Sr.No.</th>
                                    <th>Vendor Name</th>
                                    <th>Pending Product Request</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i =1;
                                    //  print_r($vendor_product_counts);
                                    $ids = array();
                                @endphp
                                @if($vendor_product_counts !=null)
                                    @foreach ($vendor_product_counts as $vendor_product_detail) 
                                        <tr  data-toggle="collapse" data-target="#demo{{$i}}" class="data-toggle" data-id="{{$i}}">
                                            {{-- <td><button class="btn data-toggle" data-toggle="collapse" data-target="#demo{{$i}}"><i class="fas fa-plus"></i></button></td> --}}
                                            <td data-label="Sr.No."><b>{{$i}}</b></td>
                                            <td data-label="Vendor Name"><b>{{$vendor_product_detail['vendor_name']}}</b></td>
                                            <td data-label="Pending Product Request"><b>{{$vendor_product_detail['count']}}</b></td>
                                        </tr>
                                        <tr>
                                            <td colspan="12" class="hiddenRow">
                                                <div class="collapse table-responsive jim-table-responsive" id="demo{{$i}}">
                                                    <table class="table table-bordered table-sm ">
                                                        <thead class="thead-light" style="background-color: #476dda; color:white;">
                                                            <tr>
                                                                <th>Sr.No</th>
                                                                <th>Product Name</th>
                                                                <th>Product Description</th>
                                                                <th>Warehouse City</th>
                                                                <th>Product Quantity</th>
                                                                <th>Product Rent Required</th>
                                                                <th>Action</th>
                                                                <th>Comment</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @for($j = 0; $j <count($vendor_product_detail['products']); $j++)
                                                                <tr class="table-light">
                                                                    <td data-label="Sr.No.">{{$j+1}}<input type="hidden" name="vendor_product_id{{$i.$j}}" value={{$vendor_product_detail['products'][$j]['product_id']}}></td>
                                                                    <td data-label="Product Name" class="text-left">{{$vendor_product_detail['products'][$j]['product_name']}}</td>
                                                                    <td data-label="Description">{{$vendor_product_detail['products'][$j]['product_details']}}</td>
                                                                    <td data-label="Warehouse City">{{$vendor_product_detail['products'][$j]['wh_city']}}</td>
                                                                    <td data-label="Quantity">{{$vendor_product_detail['products'][$j]['product_quantity']}}</td>
                                                                    <td data-label="Rent Required">{{$vendor_product_detail['products'][$j]['product_rent_requested']}}</td>
                                                                    <td data-label="Action"><input type="radio" name="action{{$i.$j}}" id="approved{{$i.$j}}" value="Approve"><label for="approved{{$i.$j}}">Approve</label><input type="radio" name="action{{$i.$j}}" id="rejected{{$i.$j}}" value="Reject"><label for="rejected{{$i.$j}}">Reject</label></td>
                                                                    <td data-label="Comment"><input type="text" class="form-control" name="comment{{$i.$j}}"></td>
                                                                </tr>
                                                                @php
                                                                    $ids_id = $i.$j;
                                                                    array_push($ids,$ids_id);
                                                                @endphp
                                                            @endfor
                                                           
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                        @php
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
                        {!! Form::hidden('info', json_encode($ids)) !!}
                        <center><button type="submit" class="btn btn-primary" name="submit" value="submit" style="<?php if($vendor_product_counts ==null){ echo "display:none;"; }?>">Submit</center>
                    </div>
                </div>
            </div>	                    
        <!-- /.container-fluid -->
        
    </form>
@endsection

@section('script')
<script>
    $(document).ready(function () {
        $('#dataTable').DataTable();
        $('#rowDataTable').DataTable();

        if(localStorage['filtered'] != null)
        {
            $('#city_filter').val(localStorage['filtered']);
        }
        $('#city_filter').on("change",function(){
            var filter_by = $('#city_filter').val();
            localStorage['filtered'] = filter_by;
            //alert(filter_by);
            var dataString = (filter_by);
            var url = "<?php echo url('/');?>/product_request/"+dataString;
            window.location.assign(url);
        });
    });
</script>

@endsection