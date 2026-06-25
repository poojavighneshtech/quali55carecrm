@extends('header_and_sidebar')

@section('styles')
<style>
    tr:hover {
        /* background-color: rgb(140, 134, 132); */
        /* color:rgb(248, 242, 240); */
        cursor: pointer;
    }
    .scrollable_card{
        overflow-y: auto;
        max-height: 400px;
    }
    .mbody{
        overflow-y: auto;
        max-height: 450px;
    }
</style>
@endsection

@section('content')
    <div class="card my-3">
        <div class="card-header text-center">
            <h4>Transaction History</h4>
        </div>
        <div class="card-body">

            <div class="card card-body">
                <h4>Customer Details:</h4>
                <h5 class="card-title"><strong>Customer Name :</strong> {{$customer_details->customer_name}}</h5>
                <h6 class="card-subtitle mb-2"><strong>Contact No : </strong>{{$customer_details->primary_contact_no}}</h6>
                <address class="card-text">
                    <strong>Address : </strong>{{$customer_details->address_line_1.", ".$customer_details->address_line_2.", ".$customer_details->landmark.", ".$customer_details->area.", ".$customer_details->city."- ".$customer_details->pincode}}
                </address>                
            </div>
            <div class="card card-body my-1">
                <h4>Transactions:</h4>
                <table class="table">
                    <th>Type</th>
                    <th>Order Id</th>
                    <th>Order Date</th>
                    <th>Period</th>
                    <th>Total</th>
                    <th>Credit</th>
                    <th>Debit</th>
                    @php $total = 0; @endphp
                    @forelse($orders as $key=>$order)
                        <tr id="{{$order->order_id}}" data-order_type="Delivery">
                            <td>Delivery @if($order->patient_name!="" && $order->patient_name!=null){{"- ".$order->patient_name}}@endif</td>
                            <td>{{$order->order_id}}</td>
                            <td>{{date('d-m-Y',strtotime($order->DelDate))}}</td>
                            <td>{{date('d-m-Y',strtotime($order->DelDate))}} - {{Carbon\Carbon::parse(date('Y-m-d',strtotime($order->DelDate)))->addMonth()->format('d-m-Y')}}</td>                            
                            <td><i class="fas fa-plus text-success"></i> {{$order->TotalAmt - $order->drnote->pluck('amount')->sum() + $order->crnote->pluck('amount')->sum()}}
                                @php $total = $total + $order->TotalAmt;@endphp
                            </td>
                            <td>@if(Collect($order)->has('drnote')){{$order->drnote->pluck('amount')->sum()}}@endif</td>
                            <td>@if(Collect($order)->has('crnote')){{$order->crnote->pluck('amount')->sum()}}@endif</td>
                        </tr>
                        @foreach ($order->renewals as $ke=>$renewals)
                            {{-- <tr id="{{$renewals->order_id}}" data-order_type="Collection">
                                <td>&emsp;Renewal</td>
                                <td>{{$renewals->order_id}}</td>
                                <td>{{date('d-m-Y',strtotime($renewals->DelDate))}}</td>
                                <td>{{date('d-m-Y',strtotime($renewals->start_date))}} - {{date('d-m-Y',strtotime($renewals->end_date))}}</td>
                                <td><i class="fas fa-plus text-success"></i> {{$renewals->TotalAmt}}@php $total = $total + $renewals->TotalAmt;@endphp</td>
                                <td></td>
                            </tr> --}}
                            <tr id="{{$ke}}" data-order_type="Collection">
                                <td>&emsp;Renewal</td>
                                <td>{{$ke}}</td>
                                <td>{{$renewals->first()->DelDate}}</td>
                                <td>{{date('d-m-Y',strtotime($renewals->first()->start_date))}} - {{date('d-m-Y',strtotime($renewals->last()->end_date))}}</td>
                                <td><i class="fas fa-plus text-success"></i> {{$renewals->first()->TotalAmt}} @php $total = $total + $renewals->first()->TotalAmt;@endphp</td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endforeach

                        @foreach ($order->pickups as $ke=>$pickups)
                            <tr id="{{$pickups->order_id}}" data-order_type="Pickup">
                                <td>&emsp;&emsp;Pickup</td>
                                <td>{{$pickups->order_id}}</td>
                                <td>{{date('d-m-Y',strtotime($pickups->DelDate))}}</td>
                                <td>-</td>
                                <td></td>
                                <td></td>
                                <td><i class="fas fa-minus text-danger"></i> {{$pickups->TotalAmt}}@php $total = $total - $pickups->TotalAmt;@endphp</td>
                            </tr>
                        @endforeach
                        <tr>
                           <th colspan="7"></th>
                        </tr>
                    @empty
                        <h5 class="text-center">No Transactions</h5>
                    @endforelse
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Total</th>
                        <th>{{$total}}</th>
                        <th></th>
                        <th></th>
                    </tr>
                <table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="viewOrderDetails" tabindex="-1" role="dialog" aria-labelledby="viewOrderDetails" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewOrderDetails"><span id="order_type_title"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body mbody" id="order_details_modal_body">
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    $('table').on('click', 'tr', function () {
        if(this.id != "" && this.id != undefined){
            let order_type = this.dataset.order_type;
            $("#order_details_modal_body").empty();
            var dataString = ({_token:"{{ csrf_token() }}",order_id:""+this.id,request_type:this.dataset.order_type});
                $.ajax({
                    type: "POST",
                    url: "{{url('/')}}/getOrderDetails",
                    data: dataString,
                    cache:false,
                    success: function (data)
                    {
                        console.log(data);
                        console.log(order_type);
                        if(order_type == "Delivery"){
                            let details_count = data.length;
                            let table = '<div class="table table-responsive jim-table-responsive scrollable_card">';
                                table += '<table class="table" id="order_details_table">';
                                    table += '<thead>';
                                        table += '<th>Sr. No</th>';
                                        table += '<th>Date</th>';
                                        table += '<th>Product Name</th>';
                                        table += '<th>Product Rent</th>';
                                        table += '<th>Product Deposit</th>';
                                        table += '<th>Transport</th>';
                                    table += '</thead>';
                                    table += '<tbody>';
                                    for (let i = 0; i < details_count; i++)
                                    {
                                        let sr_no = i+1;
                                        table += '<tr class="text-nowrap">';
                                            table +='<td data-label="sr no">'+sr_no+'</td>';
                                            table +='<td data-label="Date">'+data[i].deldate+'</td>';
                                            table +='<td data-label="Product Name" class="text-wrap">'+data[i].product_name+'</td>';
                                            table +='<td data-label="Rent">'+data[i].product_rent+'</td>';
                                            table +='<td data-label="Deposit">'+data[i].product_deposite+'</td>';
                                            table +='<td data-label="Transport">'+data[i].transport+'</td>';
                                        table += '</tr>';
                                    }
                                    table += '</tbody>';
                                table += '</table>';
                            table += '</div>';
                            let rec_amt = 0;
                            if(data[0].received_total_amount!=null){
                                rec_amt = data[0].received_total_amount;
                            }
                            table += '<div class="row"><div class="col-md-12 text-right"><span>Total Amount : </span><span>'+data[0].assigned_total_amount+'</span></div></div>';
                            table += '<div class="row"><div class="col-md-12 text-right"><span>Received Amount : </span><span>'+rec_amt+'</span></div></div>';
                            table += '<div class="row"><div class="col-md-12 text-right"><span>Remaining : </span><span>'+(data[0].assigned_total_amount - data[0].received_total_amount)+'</span></div></div>';
                            // location.reload();
                            $("#order_type_title").text("Delivery");
                            $("#order_details_modal_body").append(table);
                            $("#viewOrderDetails").modal("show");
                            $("#order_details_table").dataTable();
                        }
                        else if(order_type == "Collection"){
                            let details_count = data.length;
                            let table = '<div class="table table-responsive jim-table-responsive scrollable_card">';
                            table += '<table class="table" id="order_details_table">';
                                table += '<thead>';
                                    table += '<th>Sr. No</th>';
                                    table += '<th>Date</th>';
                                    table += '<th>Product Name</th>';
                                    table += '<th>Product Rent</th>';
                                    table += '<th>Adjusted Deposit</th>';
                                    table += '<th>Discount</th>';
                                    table += '<th>Period</th>';
                                table += '</thead>';
                                table += '<tbody>';
                                for (let i = 0; i < details_count; i++)
                                {
                                    let sr_no = i+1;
                                    table += '<tr class="text-nowrap">';
                                        table +='<td data-label="srno">'+sr_no+'</td>';
                                        table +='<td data-label="Date">'+data[i].date+'</td>';
                                        table +='<td data-label="Product Name" class="text-wrap">'+data[i].product_name+'</td>';
                                        table +='<td data-label="Rent">'+data[i].product_rent+'</td>';
                                        table +='<td data-label="Deposit">'+data[i].adjusted_deposit+'</td>';
                                        table +='<td data-label="Discount">'+data[i].discount_amt+'</td>';
                                        table +='<td data-label="Period">'+data[i].start_date+' - '+data[i].end_date+'</td>';
                                    table += '</tr>';
                                }
                                table += '</tbody>';
                            table += '</table>';
                            table += '</div>';
                            if(data[0].received_total_amount == '' || data[0].received_total_amount == null)
                            {
                                data[0].received_total_amount = 0;
                            }
                            table += '<div class="row"><div class="col-md-12 text-right"><span>Total Amount : </span><span>'+data[0].total_amount+'</span></div></div>';
                            table += '<div class="row"><div class="col-md-12 text-right"><span>Received Amount : </span><span>'+data[0].received_total_amount+'</span></div></div>';
                            table += '<div class="row"><div class="col-md-12 text-right"><span>Remaining : </span><span>'+(data[0].total_amount - data[0].received_total_amount)+'</span></div></div>';
                            // location.reload();
                            $("#order_type_title").text("Collection");
                            $("#order_details_modal_body").append(table);
                            $("#viewOrderDetails").modal("show");
                            $("#order_details_table").dataTable();
                        }
                        else if(order_type == "Pickup"){
                            let details_count = data.length;
                            let table = '<div class="table table-responsive jim-table-responsive scrollable_card">';
                                table += '<table class="table" id="order_details_table">';
                                    table += '<thead>';
                                        table += '<th>Sr. No</th>';
                                        table += '<th>Date</th>';
                                        table += '<th>Product Name</th>';
                                        table += '<th>Product Deposit</th>';
                                    table += '</head>';
                                    table += '<tbody>';
                                    for (let i = 0; i < details_count; i++)
                                    {
                                        let sr_no = i+1;
                                        table += '<tr class="text-nowrap">';
                                            table +='<td data-label="srno">'+sr_no+'</td>';
                                            table +='<td data-label="date">'+data[i].date+'</td>';
                                            table +='<td data-label="Product Name" class="text-wrap">'+data[i].product_name+'</td>';
                                            table +='<td data-label="Deposit">'+data[i].product_deposite+'</td>';                                            
                                        table += '</tr>';
                                    }
                                    table += '</tbody>';
                                table += '</table>';
                            table += '</div>';
                            if(data[0].paid_total_amount == '' || data[0].paid_total_amount == null)
                            {
                                data[0].paid_total_amount = 0;
                            }
                            table += '<div class="row"><div class="col-md-12 text-right"><span>Total Amount : </span><span>'+data[0].total_amount+'</span></div></div>';
                            table += '<div class="row"><div class="col-md-12 text-right"><span>Received Amount : </span><span>'+data[0].paid_total_amount+'</span></div></div>';
                            table += '<div class="row"><div class="col-md-12 text-right"><span>Remaining : </span><span>'+(data[0].total_amount - data[0].paid_total_amount)+'</span></div></div>';
                            // location.reload();
                            $("#order_type_title").text("Pick Up");
                            $("#order_details_modal_body").append(table);
                            $("#viewOrderDetails").modal("show");
                            $("#order_details_table").dataTable();
                        }
                    }
                });
        }
    });
</script>
@endsection