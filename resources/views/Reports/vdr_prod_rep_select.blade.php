{{-- @extends('header_and_sidebar') --}}
@extends('new-sidebar')

@section('title')
   Renewal And Pickup
@endsection
    @section('header')
       
    @endsection

    @section('content')
    <div class="container-fluid">
        
        <form class="form" action="" method="post" >
            {{ csrf_field() }}
            <div class="card">
                <div class="card-header" style="background-color: #337ab7; color: white;">
                    <center>
                        <b>Vendor Product Reports</b>
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
                    <center>
                        <select class="selectpicker" name="select_vendor" title="Select Vendor" data-live-search="true" id="select_vendor">
                            @foreach($vendor_details as $vendor_detail)
                                <option value="{{$vendor_detail['id']}}" @if(isset($vdr_detail))@if($vdr_detail[0]['id'] == $vendor_detail['id']){{'selected'}}@endif @endif>{{$vendor_detail['registered_name']}}</option>
                            @endforeach
                        </select>
                    </center>
                    <div class="row" id="pickup_renew">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover" id="records" width="100%">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Equipment Name</th>
                                        <th>Rented Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($equipment_details))
                                        @php $sr_no = 1; @endphp
                                        @foreach($equipment_details as $equipment_detail)
                                            <tr>
                                                <td>{{$sr_no}}</td>
                                                <td>{{$equipment_detail['equipment_name']}}</td>
                                                <td>{{$equipment_detail['rented_qty']}}</td>
                                            </tr>
                                            @php $sr_no += 1; @endphp
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>   
    @endsection
    @section('script')
        <script>
            $(document).ready(function() {
                $('#select_vendor').on('change', function(){
                    var vendor_id = $(this).val();
                    var url = "{{url('/')}}/rented_equipment_report/"+vendor_id;
                    window.location.assign(url);
                });
            });
        </script>                                                         
    @endsection