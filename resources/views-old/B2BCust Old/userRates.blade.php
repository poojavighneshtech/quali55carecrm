{{-- @extends('header_and_sidebar') --}}
@extends('new-sidebar')

<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        
        <title>All Leads</title>
        {{-- <script src="<?php echo url('/');?>/assets/vendor/jquery/jquery.min.js"></script> --}}
        <link rel="stylesheet" href="{{url('/')}}/assets/dist/toast.min.css ">
        <!-- Boostrap 4 CSS -->
   
        @section('styles')
        <style>
            #filter_card{
                position: relative;
            }
            #h6_filter{
                position: absolute;
                right: 50%;
                top: -0.5rem;
                /* z-index: -100; */
            }
            .card-header .fa {
            transition: .3s transform ease-in-out;
            }
            .card-header .collapsed .fa {
            transform: rotate(90deg);
            }
            /* .table tbody tr td, 
            .table thead tr th { */
                white-space: nowrap;
                width: 1%;
            /* } */
        </style>
        @endsection
    </head>

<body id="page-top">	
    @section('content')
        @if(session()->has('message_delete'))
            <div class="alert alert-danger">
                {{ session()->get('message_delete') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
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
        @if(session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <div class="card" id="filter_card">
            <div class="card-header border-primary" id="filter_card">
                <div class="row">
                    <div class="col text-primary" data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                        <strong>B2B User</strong>
                    </div>
                    <div class="col-auto">
                        <a data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                            <i class="fa fa-chevron-down pull-right"></i>&emsp;<b>Filter</b>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body border-primary collapse show" id="filter-collapse" aria-labelledby="headingTwo" data-parent="#filter_card">
                <form action="{{route('b2b-user-rate')}}" method="GET" id="b2b_user_rate_form">
                    @csrf
                    <div class="row">
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-primary btn-sm btn-block" id="btn_add_product_rate">Add Product</button>
                        </div>
                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" class="form-control form-control-sm" name="search_product" id="" placeholder="Product name..."
                                        value="{{request()->get('search_product')}}">
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control form-control-sm selectpicker" name="search_b2buser" id="" title="Select User" data-live-search="true" data-size="5" data-close="true">
                                        @foreach ($getB2bUser as $key=>$user)
                                            <option value="{{$user->id}}" @if(request()->get('search_b2buser')==$user->id) selected @endif>{{$user->username}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-outline-success btn-sm btn-block">Submit</button>
                                </div>
                                <div class="col-md-2">
                                    <a class="btn btn-outline-secondary btn-sm btn-block" href="{{route('b2b-user-rate')}}">Clear</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="table table-responsive table-flush">
                <table class="table table-hover">
                    <thead class="thead thead-light">
                        <th>Username</th>
                        <th>Product Name</th>
                        <th>Rent</th>
                        <th>Sale</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        @foreach ($b2bProductRate as $key=>$product)
                        <tr>
                            <td>{{$product->username}}</td>
                            <td>{{$product->product_name}}</td>
                            <td>{{$product->rate}}</td>
                            <td>{{$product->sale_rate}}</td>
                            <td>
                                <button type="button" class="btn btn-outline-info btn-sm editProduct" 
                                    data-edit_id="{{$product->id}}" data-product_id = "{{$product->product_id}}" data-product_rate="{{$product->rate}}"
                                    data-product_sale_rate="{{$product->sale_rate}}"
                                    data-user_id="{{$product->b2b_user_id}}">Edit</button>
                                <button type="button" class="btn btn-outline-danger btn-sm removeProduct" 
                                    data-id="{{$product->id}}" data-product_name="{{$product->product_name}}" data-username="{{$product->username}}">Remove</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{$b2bProductRate->links('Custom.Pagination.pagination')}}
            </div>
        </div>
       

        {{-- modal here --}}
        <div class="modal fade" id="modal_add_product_rate" tabindex="-1" role="dialog" aria-labelledby="product_rate" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="product_rate">Add Product Rate</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{route('b2b-addproduct-rate')}}" method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="row">
                                    <select class="form-control selectpicker" name="selected_b2buser" id="" 
                                        title="Select B2B User" data-live-search="true" data-size="5"
                                            required="true">
                                        @foreach ($getB2bUser as $key=>$user)
                                            <option value="{{$user->id}}">{{$user->username}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="card" style="width:100%">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col text-primary" class="d-block">
                                                    <strong>Product</strong>
                                                </div>
                                                <div class="col-auto">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm newProduct" id="btn_add_product">Add Product</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table table-responsive">
                                            <table class="table table-hover" id="productRateTable">
                                                <thead>
                                                    <th>Product Name</th>
                                                    <th>Rent</th>
                                                    <th>Sale</th>
                                                    <th>Action</th>
                                                </thead>
                                                <tbody class="product_tbody" id="product_tbody">
                                                    <tr>
                                                        <td style="width: 40%">
                                                            <select class="form-control selectpicker border border-0" name="selected_product[id][]" id=""
                                                                title="Select Product" data-live-search="true" data-size="5" required>
                                                                @foreach ($productList as $key=>$product)
                                                                    <option value="{{$product->id}}">{{$product->product_name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control" name="selected_product[rate][]" id="" placeholder="Product Rate.." required>
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control" name="selected_product[sale_rate][]" id="" value="0" placeholder="Sale Rate..">
                                                        </td>
                                                        <td>-</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" id="submit_status" class="btn btn-outline-success">Submit</button>    
                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>

        {{-- Edit product modal --}}
        <div class="modal fade" id="modal_edit_product_rate" tabindex="-1" role="dialog" aria-labelledby="edit_product_rate" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="edit_product_rate">Edit Product Rate</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{route('b2b-editproduct-rate')}}" method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-3">
                                        <Strong><label for="selected_b2buser">Username</label></Strong>
                                        <select class="form-control selectpicker" name="edit_b2b_user" id="edit_b2b_user" 
                                            title="Select B2B User" data-live-search="true" data-size="5"
                                                required="true">
                                            @foreach ($getB2bUser as $key=>$user)
                                                <option value="{{$user->id}}">{{$user->username}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <Strong><label for="Product Name">Product</label></Strong>
                                        <select class="form-control selectpicker" name="edit_product" id="edit_product"
                                            title="Select Product" data-width="100%" data-live-search="true" data-size="5" required>
                                            @foreach ($productList as $key=>$product)
                                                <option value="{{$product->id}}">{{$product->product_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <Strong><label for="Rent">Rent</label></Strong>
                                        <input type="number" class="form-control" name="edit_rate" id="edit_rate" required>
                                    </div>
                                    <div class="col-md-3">
                                        <Strong><label for="Sale">Sale</label></Strong>
                                        <input type="number" class="form-control" name="edit_sale_rate" id="edit_sale_rate">
                                    </div>
                                    {{-- hidden --}}
                                    <input type="hidden" name="edit_id" id="edit_id">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" id="submit_status" class="btn btn-outline-success">Submit</button>    
                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>

        {{-- remover product --}}
        <div class="modal fade" id="modalRemoveProduct" tabindex="-1" role="dialog" aria-labelledby="modalRemoveProduct" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{route('b2b-removeproduct-rate')}}" method="post">
                        @csrf
                        <div class="modal-header">
                            <h5>Are you sure you wanna delete this ?</h5>
                            {{-- <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5> --}}
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="remove_id" id="removeProductRateId">
                            <strong><span id="productNameRemove"></span></strong>  from  <strong><span id="usernameRemove"></span></strong>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    @endsection
    @section('script')
        {{-- @if(request()->routeIs('view_all_leads') ? 'active' : '')
            <script>
                    $.removeCookie('filter_collapse_js');
            </script>
        @endif --}}
        <script>
            $("#btn_add_product_rate").on('click',function(){
                $('#modal_add_product_rate').modal('show');
                
            });

            $(document).ready(function() {
                $(".newProduct").on('click',function(){
                    var proudcts = @json($productList);
                       //console.log(proudcts)
                    var row="<tr>";
                            row+="<td>";
                                row+="<select class='form-control selectpicker' name='selected_product[id][]' data-live-search='true' data-size='5' title='Select Product' required>";
                                    jQuery.each(proudcts, function(index, item) {
                                        row+="<option value="+item.id+">"+item.product_name+"</option>"
                                    });
                                row+="</select>";
                            row+="</td>";
                            row+="<td>";
                                row+="<input type='number' class='form-control' name='selected_product[rate][]' placeholder='Product Rate..' required>";
                            row+="</td>";
                             row+="<td>";
                                row+="<input type='number' class='form-control' name='selected_product[sale_rate][]' placeholder='Sale Rate..' value='0'>";
                            row+="</td>";
                            row+="<td>";
                                row+="<button type='button' class='btn btn-outline-danger remProduct'>Remove</button>";
                            row+="</td>";
                        row+="</tr>";
                    $("#productRateTable tbody").append(row);
                    $('.selectpicker').selectpicker('render');
                });
                $('#productRateTable').on('click','.remProduct',function(){
                    $(this).parent().parent().remove();
                });
            });

            $(".removeProduct").on('click',function(){
                $("#productNameRemove").text($(this).data('product_name'));
                $("#usernameRemove").text($(this).data('username'));
                $("#removeProductRateId").val($(this).data('id'));
                $('#modalRemoveProduct').modal('show');
            });

            $(".editProduct").on('click',function(){
                $("#edit_b2b_user").selectpicker('val',$(this).data('user_id'));
                $("#edit_product").selectpicker('val',$(this).data('product_id'));
                $("#edit_rate").val($(this).data('product_rate'))
                $("#edit_sale_rate").val($(this).data('product_sale_rate'))
                $("#edit_id").val($(this).data('edit_id'))
                $('#modal_edit_product_rate').modal('show');
            });

            $('.table-responsive').on('show.bs.dropdown', function () {
                $('.table-responsive').css( "overflow", "inherit" );
            });

            $('.table-responsive').on('hide.bs.dropdown', function () {
                $('.table-responsive').css( "overflow", "auto" );
            });
        </script>
    @endsection
</body>
</html>
