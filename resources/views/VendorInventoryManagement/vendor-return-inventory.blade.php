@extends('header_and_sidebar')
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Vendor Inventory</title>
    @section('styles')
        <style>
            
            .target {
                border: solid 1px #aaa;
                min-height: 200px;
                width: 30%;
                margin-top: 1em;
                border-radius: 5px;
                cursor: pointer;
                transition: 300ms all;
                position: relative;
                }

                .contain {
                    background-size: cover;
                position: relative;
                z-index: 10;
                top: 0px;
                left: 0px;
                }

                /* .active {
                box-shadow: 0px 0px 10px 10px rgba(0,0,255,.4);
                } */


                .new:after {
                    content: "NEW feature";
                    color: white;
                    letter-spacing: 1px;
                    background: hsla(80, 90%, 40%, .9);
                    position: absolute;
                    margin: -10px 5px 0 0;
                    transform: rotate(-25deg);
                    padding: 2px 5px;
                    border-radius: 4px;
                    font-size: 10px;
                    line-height: 14px;
                    opacity: .85;
                }

        </style>
    @endsection
</head>

<body id="page-top">	
        
    @section('content')
        
        @if(session()->has('message') || session()->has('message_pop') )
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                {{ session()->get('message')}} @if(session()->has('collection_url'))<small><a class="" href="{{ session()->get('collection_url')}}">See Order Here</a></small>@endif
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

        <div class="card">
            <div class="card-header border-primary" id="filter_card">
                <div class="row">
                    <div class="col text-primary" data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                        <strong>Vendor Inventory</strong>
                        
                    </div>
                    <div class="col-auto">
                        <a data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                            <i class="fa fa-chevron-down pull-right"></i>&emsp;<b>Filter</b>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{url('/')}}/vendor-return-inventory" method="GET">
                    @csrf
                    <div class="row">
                        <div class="col-md-9">
                            <div class="row ">
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <label for="vendor_name"><strong>Vendor Name:</strong></label>
                                        </div>
                                        <div class="col-md-7">
                                            <select class="selectpicker form-control form-control-sm" name="return_vendor[]" multiple="multiple" id="filter_return_vendor" title="Select Vendor"
                                                aria-placeholder="Select Vendor" data-live-search="true" data-size="5" placeholder="Select Vendor" >
                                                @foreach ($vendors as $key => $vendor)
                                                    <option value="{{$vendor->id}}" @if(request()->get('return_vendor')!=null) @if(in_array($vendor->id,request()->get('return_vendor'))){{'selected'}} @endif @endif>{{$vendor->registered_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="product_name"><strong>Products:</strong></label>
                                        </div>
                                        <div class="col-md-8">
                                            <select class="selectpicker form-control form-control-sm" name="prod_name" id="prod_name" data-live-search = true style="width: 100%"
                                                title="Select Product">
                                                @foreach ($masterProducts as $key => $product)
                                                    <option value="{{$product->id}}" @if(request()->get('prod_name')!=null) @if($product->id == request()->get('prod_name')){{'selected'}} @endif @endif>{{$product->product_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="assigned_to"><strong>Assigned To</strong></label>
                                        </div>
                                        <div class="col-md-8">
                                            <select class="selectpicker form-control form-control-sm" name="filter_return_assigned_to" id="filter_return_assigned_to" title="Select Delboy"
                                                aria-placeholder="Select Vendor" data-live-search="true" data-size="5" placeholder="Select Delboy" value="{{request()->get('filter_return_assigned_to')}}">
                                                @foreach ($deliveryStaff as $key => $delboy)
                                                    <option value="{{$delboy->username}}" @if(request()->get('filter_return_assigned_to')!=null) @if($delboy->username == request()->get('filter_return_assigned_to')){{'selected'}} @endif @endif>{{$delboy->username}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>From</strong>
                                </div>
                                <div class="col-md-9">
                                    <input type="date" name="from_date" id="from_date" class="form-control form-control-sm" value="{{request()->get('from_date')}}">
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-md-3">
                                    <strong>To</strong>
                                </div>
                                <div class="col-md-9">
                                    <input type="date" name="end_date" id="end_date" class="form-control form-control-sm" value="{{request()->get('end_date')}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="row">
                                <div class="col-md-5">
                                    <label for="city"><strong>City:</strong></label>
                                </div>
                                <div class="col-md-7">
                                    <select class="selectpicker form-control form-control-sm" name="city" id="filter_city" title="Select City"
                                        aria-placeholder="Select Vendor" data-live-search="true" data-size="5" placeholder="Select City" value="{{request()->get('city')}}">
                                        <option value="All" @if(request()->get('city')!=null) @if('All' == request()->get('city')){{'selected'}} @endif @endif>All</option>
                                        @foreach ($cities as $key => $city)
                                            <option value="{{$city->city}}" @if(request()->get('city')!=null) @if($city->city == request()->get('city')){{'selected'}} @endif @endif>{{$city->city}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="row">
                                <div class="col-md-5">
                                    <label for="city"><strong>State:</strong></label>
                                </div>
                                <div class="col-md-7">
                                    <select class="selectpicker form-control form-control-sm" name="filter_state" id="filter_state" title="Select State"
                                        aria-placeholder="Select Vendor" data-live-search="true" data-size="5" placeholder="Select State" value="{{request()->get('filter_state')}}">
                                        <option value="All" @if(request()->get('filter_state')!=null) @if('All' == request()->get('filter_state')){{'selected'}} @endif @endif>All</option>
                                            <option value="in" @if(request()->get('filter_state')!=null) @if("in" == request()->get('filter_state')){{'selected'}} @endif @endif>In</option>
                                            <option value="out" @if(request()->get('filter_state')!=null) @if("out" == request()->get('filter_state')){{'selected'}} @endif @endif>Out</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-3"></div>
                        <div class="col-md-6 text-center">
                            {{-- @if(in_array(session('user_id'),[19,175])) --}}
                                <button type="button" class="btn btn-outline-primary btn-sm " id="create_return">Add Record</button>
                            {{-- @endif --}}
                            <button type="submit" class="btn btn-outline-primary  btn-sm" name="btn_submit" value="submit">Submit</button>
                            <a href="{{url('/')}}/vendor-return-inventory" class="btn btn-outline-secondary btn-sm">Clear Filter</a>
                            <button type="submit" class="btn btn-outline-success btn-sm" name="btn_submit" id="export_excel" value="export_excel">Export</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="table table-responsive">
                <table class="table">
                    <thead>
                        <th>Date</th>
                        <th>Inventory Pickup/Return Date</th>
                        <th>Product Name</th>
                        <th>Vendor Name</th>
                        <th>Quantity</th>
                        <th>Inventory Id</th>
                        <th>Pickup Addr</th>
                        <th>Drop Addr</th>
                        <th>State</th>
                        <th>Product Image</th>
                        <th>Assigned To</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        @foreach ($inventory as $key=>$inv)
                            <tr>
                                <td class="text-nowrap">{{date('d-M-Y',strtotime($inv->date))}}</td>
                                <td class="text-nowrap">
                                    @if($inv->inventory_pickup_date)
                                        {{date('d-M-Y',strtotime($inv->inventory_pickup_date))}}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{$inv->product_name}}</td>
                                <td>{{$inv->vendor_name}}</td>
                                <td>{{$inv->quantity}}</td>
                                <td>{{$inv->inventory_id}}</td>
                                <td>{{$inv->pickup_address}}</td>
                                <td>{{$inv->drop_address}}</td>
                                <td>
                                    <span class="badge @if($inv->state=='in') badge-warning @else badge-primary @endif">{{$inv->state}}</span>
                                </td>
                                <td class="text-center">
                                    @if(count(explode(',',$inv->product_img))>1)
                                        <a class="multi-images btn btn-sm btn-outline-primary" data-value="{{$inv->product_img}}">View Images</a>
                                    @else
                                        <img src="{{asset('storage/app/'.$inv->product_img)}}" class="rounded view-image image-fluid" data-value="{{asset('storage/app/'.$inv->product_img)}}" width="50" height="50" alt="ff">
                                    @endif
                                </td>
                                <td>
                                    {{$inv->assigned_to}}
                                </td>
                                <td class="text-nowrap">
                                    @if($inv->details_id == null || in_array(session('user_id'),[14,97,19]))
                                        <button type="button" class="btn btn-sm btn-outline-primary edit-record" data-status = "{{$inv->state}}" data-record_id = "{{$inv->id}}"><i class="fa fa-edit" aria-hidden="true"></i></button>
                                    @else
                                        <button type="button" class="btn btn-sm btn-outline-primary edit-img-record" data-status = "{{$inv->state}}" data-record_id = "{{$inv->id}}"><i class="fa fa-edit" aria-hidden="true"></i></button>
                                    @endif
                                        @if($inv->is_verified == "no")
                                            <button type="button" class="btn btn-sm btn-outline-primary verify-record" data-record_id = "{{$inv->id}}" data-href = "{{route('vendor-inventory-verify',['id'=>$inv->id])}}"><i class="fa fa-check" aria-hidden="true"></i></button>
                                        
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-record" data-record_id = "{{$inv->id}}"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                        @endif
                                    {{-- <button type="button" class="btn btn-sm btn-outline-danger delete-record"><i class="fas fa-window-close"></i></button> --}}
                                </td>
                            </tr>    
                        @endforeach
                    </tbody>
                </table>
                {{$inventory->withPath(url()->full())->links('Custom.Pagination.pagination')}}
            </div>
        </div>
        <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="create_return_modal" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <strong id="header_text">Add Record</strong>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('vendor-return-inventory-create')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="container container-fluid">
                                <div class="row">
                                    <label for=""><strong>Date :</strong></label>
                                    <input type="date" class="form-control form-control-sm" name="return_date" id="return_date" required>
                                    <input type="hidden" name="hide_record_id" id="hide_record_id">
                                </div>
                                <div class="row my-2">
                                    <label for=""><strong>Select product :</strong></label>
                                    <select class="selectpicker form-control form-control-sm border border-dark" name="return_product" id="return_product" 
                                        aria-placeholder="Select product" data-live-search="true" data-size="5" placeholder="Select product" required>
                                        <option style="display:none"></option>
                                        @foreach ($masterProducts as $key => $products)
                                            <option value="{{$products->id}}">{{$products->product_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="row my-2">
                                    <label for=""><strong>Select Vendor :</strong></label>
                                    <select class="selectpicker form-control form-control-sm border border-dark" name="return_vendor" id="return_vendor" 
                                        aria-placeholder="Select Vendor" data-live-search="true" data-size="5" placeholder="Select Vendor" required>
                                        <option style="display:none"></option>
                                        @foreach ($vendors as $key => $vendor)
                                            {{-- <option value="{{$vendor->id}}">{{$vendor->registered_name}}</option> --}}
                                            <option value="{{$vendor->id}}">{{$vendor->registered_name.' - '.$vendor->of_city}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- <div class="row">
                                    <label for=""><strong>Inventory ID : </strong></label>
                                    <input type="text" class="form-control form-control-sm" name="return_inventory_id" id="return_inventory_id" placeholder="Inventory Id." required>
                                </div> --}}
                                <div class="row mt-3">
                                    <div class="col-md-3">
                                        <label for=""><strong>Inv Pickup Date : </strong></label>
                                        <input type="date" class="form-control form-control-sm" name="return_pickup_date" id="return_pickup_date" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for=""><strong>Type : </strong></label>
                                        <div class="btn-group btn-group-toggle btn-group-sm col" data-toggle="buttons">
                                            <label class="btn btn-outline-primary in">
                                              <input type="radio" name="return_type" id="option2" value="in" required disabled> In
                                            </label>
                                            <label class="btn btn-outline-primary out">
                                              <input type="radio" name="return_type" id="option3" value="out" required> Out
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label for=""><strong>Quantity : </strong></label>
                                        <input type="number" class="form-control form-control-sm" name="return_inventory_quantity" id="return_inventory_quantity" placeholder="Quantity.." required>    
                                    </div>
                                    <div class="col-md-4">
                                        <label for=""><strong>Inventory ID : </strong></label>
                                        <input type="text" class="form-control form-control-sm" name="return_inventory_id" id="return_inventory_id" value="0" placeholder="Inventory Id." required>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <label for=""><strong>Pickup Address : </strong></label>
                                        {{-- <textarea class="form-control form-control-sm" name="return_pickup_address" id="return_pickup_address" cols="30" rows="3" required></textarea> --}}
                                        <select name="return_pickup_address" id="return_pickup_address" class="select selectpicker form-control" required>
                                            @foreach($virtual_warehouses as $key=>$warehouse)
                                                <option value="{{$warehouse->wh_name.', '.$warehouse->wh_area.', '.$warehouse->wh_city}}">{{$warehouse->wh_name.', '.$warehouse->wh_area.', '.$warehouse->wh_city}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for=""><strong>Drop Address :</strong></label>
                                        <textarea class="form-control form-control-sm" name="return_drop_address" id="return_drop_address" cols="30" rows="3" required></textarea>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <label for=""><strong>Order Assign :</strong></label>
                                    <select class="selectpicker form-control form-control-sm border border-dark" title="Select Vendor" name="return_assigned_to" id="return_assigned_to" 
                                        aria-placeholder="Select Vendor" data-live-search="true" data-size="5" placeholder="Select Delivery staff" required>
                                        @foreach ($deliveryStaff as $key => $delboy)
                                            <option value="{{$delboy->username}}">{{$delboy->username}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-10">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="return_product_image">Product Image</span>
                                            </div>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input form-control-sm" max-size="3000" name="return_product_image[]" id="return_product_image_file" multiple="multiple" aria-describedby="return_product_image" accept="image/png, image/gif, image/jpeg">
                                                <label class="custom-file-label" for="expenseReceiptImage">Choose file</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary view-product-image" id="view_product_image_btn" data-id="view_product_image">
                                            <i class="fas fa-image"></i>
                                        </button>
                                        <input type="hidden" name="hidden_receipt_image" id="hidden_receipt_image">
                                    </div>
                                </div>
                                <div class="row my-2">
                                    <div class="col text-center">
                                        <div class="span4 target"></div>
                                        <input type="hidden" name="pasted_image" id="pasted_image">
                                    </div>
                                </div>
                                <div class="row my-2">
                                    <label for=""><strong>Comment :</strong></label>
                                    <textarea class="form-control form-control-sm" name="comment" id="comment" cols="5" rows="3"></textarea>
                                </div>
                                <div class="row my-2 text-center" id="divIsVerified">
                                    <div class="col-md-12 text-center">
                                        <input type="checkbox" name="isVerified" id="isVerified" value="verified">
                                        <label for="isVerified"><strong> Is Verified</strong></label>
                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <button type="submit" name="submit" id="submit" value="create" class="btn btn-outline-success btn-sm col-md-3 btn-block">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="update_img_modal" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <strong id="img_header_text">Update Record</strong>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('vendor-return-inventory-create')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="type" value="img_update">
                            <input type="hidden" name="row_id" id="row_id">
                            <div class="container container-fluid">
                                <div class="row mt-4 " id="date_section">
                                    <div class="col-md-1">
                                        <label for="img_return_date">Date</label>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="date" name="return_pickup_date" id="img_return_date" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="row my-2">
                                    <div class="col-md-10">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="img_return_product_image">Product Image</span>
                                            </div>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input form-control-sm" max-size="3000" name="img_return_product_image[]" id="img_return_product_image_file" multiple="multiple" aria-describedby="return_product_image" accept="image/png, image/gif, image/jpeg">
                                                <label class="custom-file-label" for="expenseReceiptImage">Choose file</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary view-product-image" id="img_view_product_image_btn" data-id="img_view_product_image">
                                            <i class="fas fa-image"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="row my-2">
                                    <div class="col text-center">
                                        <div class="span4 target"></div>
                                        <input type="hidden" name="img_pasted_image" id="img_pasted_image">
                                    </div>
                                </div>
                                <div class="row my-2">
                                    <label for=""><strong>Comment :</strong></label>
                                    <textarea class="form-control form-control-sm" name="img_comment" id="img_comment" cols="5" rows="3"></textarea>
                                </div>
                                <div class="row justify-content-center">
                                    <button type="submit" name="img_submit" id="img_submit" value="create" class="btn btn-outline-success btn-sm col-md-3 btn-block">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalProductImage" tabindex="-1" role="dialog" aria-labelledby="modalProductImageTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Image</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                    <img class="img-fluid img-thumbnail" src="" alt="" id="modal_image">
              </div>
            </div>
        </div>

        <div class="modal fade" id="modalProductImageMulti" tabindex="-1" role="dialog" aria-labelledby="modalProductImageMultiTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Image</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="multi-images-div">
                    
                </div>
              </div>
            </div>
        </div>
    @endsection
</body>
@section('script')
    <script>
        $(document).ready(function(){
            $('#zoom-in').click(function () {
                $('#pic').width($('#pic').width()*1.2)
                $('#pic').height($('#pic').height()*1.2)

            })
            $('#zoom-out').click(function () {
                $('#pic').width($('#pic').width()/1.2)
                $('#pic').height($('#pic').height()/1.2)

            })
        })
        var imgState = null;
        $('#create_return').on('click',function(){
            $("#header_text").text("Add Record");
            $("#return_date").val(null);
            $("#return_product").val(null);
            $("#return_vendor").val(null);
            $("#return_inventory_id").val("0");
            $(".in").removeClass("active");
            $("#option2").removeAttr('checked');
            $(".out").addClass("active");
            $("#option3").attr('checked','true');
            $(".selectpicker").selectpicker("refresh");
            $("#submit").val("create");
            $("#divIsVerified").show();
            // $("#return_product_image_file").attr('required',true);
            $("#hide_record_id").val(null);
            $("#comment").val(null);
            $('#create_return_modal').modal('show');
            path= null;
        });
        // $('#return_product_image_file').on('change',function(){
        //     let image = document.getElementById('return_product_image_file');
        //     let imageName = image.files[0].name;
        //     var nextSibling = e.target.nextElementSibling
        //     nextSibling.innerText = imageName;
        //     imgState = null;
        //     path = URL.createObjectURL(event.target.files[0]);
        // });
        $('#return_product_image_file').on('change',function(e){
            let image = document.getElementById('return_product_image_file');
            let imageName = image.files[0].name;
            var nextSibling = e.target.nextElementSibling
            nextSibling.innerText = imageName;
            imgState = null;
            path = URL.createObjectURL(event.target.files[0]);
        });
        $('#img_return_product_image_file').on('change',function(e){
            let image = document.getElementById('img_return_product_image_file');
            let imageName = image.files[0].name;
            var nextSibling = e.target.nextElementSibling
            nextSibling.innerText = imageName;
            imgState = null;
            path = URL.createObjectURL(event.target.files[0]);
        });
        $(".view-image").click(function(){
            // console.log($(this).data("value"));
            $("#modal_image").attr("src", $(this).data("value"));
            $("#modalProductImage").modal('show');
        });

        $("#view_product_image_btn").click(function() {
            if(imgState != null){
                path = "http://"+path;
            }
            let id = $(this).data("id");
            //let path = $('#expenseReceiptImageFile').val();
            //let path = $("#hidden_receipt_image"+id).val();
            //$("#product_image_path").attr("src", path);
            $("#modal_image").attr("src", path);
            $("#modalProductImage").modal('show');
        });

        $("#img_view_product_image_btn").click(function() {
            if(imgState != null){
                path = "http://"+path;
            }
            let id = $(this).data("id");
            //let path = $('#expenseReceiptImageFile').val();
            //let path = $("#hidden_receipt_image"+id).val();
            //$("#product_image_path").attr("src", path);
            $("#modal_image").attr("src", path);
            $("#modalProductImage").modal('show');
        });

        $(".delete-record").click(function(){
            if (confirm("Aur you want to delete the record") == true) {
                let record_id = $(this).data("record_id");
                var dataString = ({_token:"{{ csrf_token() }}",record_id:""+record_id});
                $.ajax({
                    type: "POST",
                    url: "{{route('vendor-return-inventory-delete')}}",
                    data: dataString,
                    cache:false,
                    success: function (data)
                    {
                        window.location.reload();
                    }
                });
            }
        });
        $(".verify-record").click(function(){
            let record_id = $(this).data("record_id");
            let url = $(this).data("href");
            let text = "Are you sure to verify the record!";
            if(confirm(text) == true){
                // console.log(url);
                window.location.replace(url);
            }
        });

        $(".edit-record").click(function(){
            let record_id = $(this).data("record_id");
            var dataString = ({_token:"{{ csrf_token() }}",record_id:""+record_id});
            $.ajax({
                type: "POST",
                url: "{{route('vendor-return-inventory-get')}}",
                data: dataString,
                cache:false,
                success: function (data)
                {
                    console.log(data);
                    $("#header_text").text("Edit Record");
                    $("#return_date").val(data[0].date);
                    $("#return_pickup_date").val(data[0].inventory_pickup_date);
                    $("#return_product").val(data[0].equipment);
                    $("#return_vendor").val(data[0].vendor);
                    $("#return_inventory_id").val(data[0].inventory_id);
                    $("#return_inventory_quantity").val(data[0].quantity);
                    $("#return_pickup_address").val(data[0].pickup_address);
                    $("#return_drop_address").val(data[0].drop_address);
                    $("#return_assigned_to").val(data[0].assigned_to);

                    $(".selectpicker").selectpicker("refresh");
                    $(".in").removeClass("active");
                    $("#option2").removeAttr('checked');
                    $(".out").removeClass("active");
                    $("#option3").removeAttr('checked');
                    if(data[0].state == "in")
                    {
                        $(".in").addClass("active");
                        $("#option2").attr('checked',true);
                    }else if(data[0].state == "out")
                    {
                        $(".out").addClass("active");
                        $("#option3").attr('checked',true);
                    }
                    path = "{{asset('storage/app/')}}"+"/"+data[0].product_img;
                    $("#submit").val("update");
                    $("#divIsVerified").hide();
                    $("#return_product_image_file").removeAttr('required',true);
                    $("#hide_record_id").val(data[0].id);
                    $("#comment").val(data[0].comment);
                    $("#create_return_modal").modal("show");
                }
            });
        });

        $(".edit-img-record").click(function(){
            let record_id = $(this).data("record_id");
            let status = $(this).data("status");
            if(status == 'out'){
                $("#date_section").show();
            }else{
                $("#date_section").hide();
            }
            $("#row_id").val(record_id);
            $("#update_img_modal").modal("show");
        });

        function dataURLtoFile(dataurl, filename) {
            var arr = dataurl.split(','),
                mime = arr[0].match(/:(.*?);/)[1],
                bstr = atob(arr[arr.length - 1]), 
                n = bstr.length, 
                u8arr = new Uint8Array(n);
            while(n--){
                u8arr[n] = bstr.charCodeAt(n);
            }
            return new File([u8arr], filename, {type:mime});
        }

        // //Usage example:
        // var file = dataURLtoFile('data:text/plain;base64,aGVsbG8=','hello.txt');
        // console.log(file);

        $(".multi-images").click(function(){
            let images = "";
            $.each($(this).data("value").split(","), function(index,value){
                images += "<img class='img-fluid img-thumbnail' src='{{asset('storage/app/')}}/"+value+"' alt='' id='modal_image'>";
            });
            $(".multi-images-div").empty();
            $(".multi-images-div").append(images);
            $("#modalProductImageMulti").modal("show");
        });

        /*

        */
        // Created by STRd6
        // MIT License
        // jquery.paste_image_reader.js
        (function($) {
            var defaults;
            $.event.fix = (function(originalFix) {
                return function(event) {
                    event = originalFix.apply(this, arguments);
                    if (event.type.indexOf("copy") === 0 || event.type.indexOf("paste") === 0) {
                        event.clipboardData = event.originalEvent.clipboardData;
                    }
                    return event;
                };
            })($.event.fix);
            defaults = {
                callback: $.noop,
                matchType: /image.*/
            };
            return ($.fn.pasteImageReader = function(options) {
                if (typeof options === "function") {
                    options = {
                        callback: options
                    };
                }
                options = $.extend({}, defaults, options);
                return this.each(function() {
                    var $this, element;
                    element = this;
                    $this = $(this);
                    return $this.bind("paste", function(event) {
                        var clipboardData, found;
                        found = false;
                        clipboardData = event.clipboardData;
                        return Array.prototype.forEach.call(clipboardData.types, function(type, i) {
                            var file, reader;
                            if (found) {
                                return;
                            }
                            if (
                                type.match(options.matchType) ||
                                clipboardData.items[i].type.match(options.matchType)
                            ) {
                                file = clipboardData.items[i].getAsFile();
                                reader = new FileReader();
                                reader.onload = function(evt) {
                                    return options.callback.call(element, {
                                        dataURL: evt.target.result,
                                        event: evt,
                                        file: file,
                                        name: file.name
                                    });
                                };
                                reader.readAsDataURL(file);
                                setTimeout(() => {
                                var t = document.getElementById("base64");
                                var md = document.getElementById('base64MD');
                                md.value = `![image](${t.value})`;	
                                }, 1000)
                                
                                snapshoot();
                                return (found = true);
                            }
                        });
                    });
                });
            });
        })(jQuery);

        var dataURL, filename;
        $("html").pasteImageReader(function(results) {
            filename = results.filename, dataURL = results.dataURL;
            $data.text(dataURL);
            $size.val(results.file.size);
            $type.val(results.file.type);
            var img = document.createElement("img");
            img.src = dataURL;
            var w = img.width;
            var h = img.height;
            $width.val(w);
            $height.val(h);
            $("#pasted_image").val(dataURL);
            $("#img_pasted_image").val(dataURL);
            return $(".target")
                .css({
                    backgroundImage: "url(" + dataURL + ")"
                })
                .data({ width: w, height: h });
        });
        
        var $data, $size, $type, $width, $height;
        $(function() {
            $data = $(".data");
            $size = $(".size");
            $type = $(".type");
            $width = $("#width");
            $height = $("#height");
            $(".target").on("click", function() {
                var $this = $(this);
                var bi = $this.css("background-image");
                if (bi != "none") {
                    $data.text(bi.substr(4, bi.length - 6));
                }

                // $(".active").removeClass("active");
                // $this.addClass("active");

                $this.toggleClass("contain");

                $width.val($this.data("width"));
                $height.val($this.data("height"));
                if ($this.hasClass("contain")) {
                    $this.css({
                        width: $this.data("width"),
                        height: $this.data("height"),
                        "z-index": "10"
                    });
                } else {
                    $this.css({ width: "", height: "", "z-index": "" });
                }
            });
        });

        function copy(text) {
            var t = document.getElementById("base64");
            t.select();
            try {
                var successful = document.execCommand("copy");
                var msg = successful ? "successfully" : "unsuccessfully";
                alert("Base64 data coppied " + msg + " to clipboard");
            } catch (err) {
                alert("Unable to copy text");
            }
        }

        function copyMDImage() {
            var md = document.getElementById('base64MD');
            md.select();
            try {
                var successful = document.execCommand("copy");
                var msg = successful ? "successfully" : "unsuccessfully";
                alert("Markdown Base64 data coppied " + msg + " to clipboard");
            } catch (err) {
                alert("Unable to copy text");
            }
        }
    
    </script>
@endsection
</html>
