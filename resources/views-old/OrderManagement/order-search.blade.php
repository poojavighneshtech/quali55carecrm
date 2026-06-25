@extends('header_and_sidebar')
@section('header')

@endsection

@section('content')

    <div class="card my-3">
        <div class="card-header">
            <h5>Order Search</h5>
        </div>
        <div class="card-body">
            <form action="{{url('/')}}/order-search" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="order_id">Delivery Order Id</label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="order_id" id="order_id" value="@if(isset($order_id)){{$order_id}}@endif">
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-outline-success" name="submit" id="submit">Search</button>                            
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            @if(isset($order_details))
                <div class="table my-2 table-responsive">
                    <table class="table table-stripped">
                        <thead>
                            <th>Sr.No</th>
                            <th>Order Date</th>
                            <th>Order Type</th>
                            <th>Order Id</th>
                            <th>Customer Name</th>
                            <th>Products</th>
                        </thead>
                        <tbody>
                            @forelse($order_details as $key=>$value)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{$value->DelDate}}</td>
                                    <td>{{$value->deliverypickup}}</td>
                                    <td>{{$value->order_id}}</td>
                                    <td>{{$value->shipping_first_name}}</td>
                                    <td>{{$value->line_item_1}}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">No Records</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

@endsection

@section('script')

@endsection