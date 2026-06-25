@extends('header_and_sidebar')
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
                        <b>Equipment Reports</b>
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
                    <div class="row" id="pickup_renew">
                        <div class="table-responsive">
                            <table class="table table-striped " id="records" cellspacing="0">
                                <thead>
                                    <th>Sr. No</th>
                                    <th>Equipment Name</th>
                                    <th>Equipment Total Count</th>
                                    <th>Equipment Sale</th>
                                    <th>Equipment Rental</th>

                                </thead>
                                <tbody>
                                    {{!$i=1}}
                                    @foreach($product_count as $PD_Count)
                                        <tr>
                                            <td>{{$i}}</td>
                                            <td>{{$PD_Count['prod_id']}}</td>
                                            <td align="center">{{$PD_Count['count']}}</td>
                                            <td align="center">{{$PD_Count['sale_count']}}</td>
                                            <td align="center">{{$PD_Count['rental_count']}}</td>
                                        </tr>
                                        {{!$i=$i+1}}
                                    @endforeach
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
        $(document).ready(function(){
            $("#search").on('input',function(){
                var search = $("#search").val();
                if(search == 'today' || search == 'Today')
                {
                    var newRow ="<tr><td>ahshshshs</td>";
                    $('#records tbody').append(newRow);
                }
            })
            $('#records_filter').append("<label>Filter: <select class='form-control-sm' id='filter' name='filter'><option disabled selected>-----Select-----</option><option value='today'>Today</option><option value='yesterday'>Yesterday</option><<option value='past_3_days'>Past 3 Days</option><option value='week'>1 Week</option><option value='month'>Current Month</option><option value='all'>All</option></select></label>"); 
            if(localStorage['filtered'] != null)
            {
                $('#filter').val(localStorage['filtered']);
            }
            $('#filter').on("change",function(){
                var filter_by = $('#filter').val();
                var section = "In_Process";
                localStorage['filtered'] = filter_by;
                //alert(filter_by);
                var dataString = (filter_by);
                var url = "<?php echo url('/');?>/filterEquipmentReport/"+dataString;
                window.location.assign(url);
            })
        });
        </script>                                                         
    @endsection