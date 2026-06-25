@extends('header_and_sidebar')

@section('styles')

@endsection

@section('content')
    <div class="content my-3">
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
        <div class="card" id="filter">
            <div class="card-header" id="filter">
                <div class="row">
                    <div class="col text-primary" data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                        <strong>Cash Report</strong>
                    </div>
                    <div class="col-auto">
                        <a data-toggle="collapse" href="#filter-collapse" aria-expanded="true" aria-controls="filter-collapse" id="heading-filter" class="d-block">
                            <i class="fa fa-chevron-down pull-right"></i>&emsp;<b>Filter</b>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body border-primary collapse" id="filter-collapse" aria-labelledby="headingTwo" data-parent="#filter">
                <form action="{{route('cash-report')}}" method="GET">
                    @csrf
                    <div class="row form-group">
                        <div class="col-md-4">
                            <input type="date" name="filterdate" id="filterdate" class="form-control form-control-sm" value="{{request()->get('filterdate')}}">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-sm btn-outline-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="header-text d-flex justify-content-center align-items-center border border-primary">
                <h4>{{date('d-M-y',strtotime($enddate))}} - 
                    @if($locking_state == 'Open')
                        <span class="badge badge-primary">{{$locking_state}}</span>
                    @else
                        <span class="badge badge-danger">{{$locking_state}}</span>
                    @endif
                </h4>
            </div>
            <div class="table table-responsive">
                <table class="table table-jim-responsive table-stripped table-bordered">
                    <thead>
                        <tr class="text-center">
                            <th colspan="2">Receipt</th>
                            <th colspan="2">Expenses</th>
                            <th colspan="2">Receipt</th>
                            <th colspan="2">Expenses</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bg-warning text-dark">
                            <td><b>Opening Balance</b></td>
                            <td><b>
                                {{$opening_closing_balance->closing_balance_ptcash}}
                                @php $balancetotal = $opening_closing_balance->closing_balance_ptcash @endphp
                                @php $paidtotal = 0 @endphp</b>
                            </td>
                            <td><b></b></td>
                            <td><b></b></td>
                            <td><b>Opening Balance</b></td>
                            <td><b>
                                {{$opening_closing_balance->closing_balance_cust_cash}}
                                @php $balancetotalcc = $opening_closing_balance->closing_balance_cust_cash @endphp
                                @php $paidtotalcc = 0 @endphp</b>
                            </td>
                            <td><b></b></td>
                            <td><b></b></td>
                        </tr>
                        @for($key =0; $key < $count; $key++)
                            <tr>
                                @if(isset($received_delboys[$key]))
                                <td>{{$received_delboys[$key]->user_name}}</td>
                                <td>
                                    {{$received_delboys[$key]->received_cash - $received_delboys[$key]->cash_received_from_customer}}
                                    @php $balancetotal = $balancetotal + ($received_delboys[$key]->received_cash - $received_delboys[$key]->cash_received_from_customer) @endphp
                                </td>
                                @else
                                <td></td>
                                <td></td>
                                @endif
                                @if(isset($paid_delboys[$key]))
                                <td>{{$paid_delboys[$key]->person}} - {{$paid_delboys[$key]->purpose}}</td>
                                <td>
                                    {{$paid_delboys[$key]->amount}}
                                    @php $paidtotal = $paidtotal + $paid_delboys[$key]->amount @endphp
                                    @if($locking_state == 'Open')
                                        <a  class="edit-paid text-primary" id="DB{{$key}}" data-id="{{$paid_delboys[$key]->id}}"><i class="fas fa-edit"></i></a>
                                    @endif
                                </td>
                                @else
                                <td></td>
                                <td></td>
                                @endif
                                @if(isset($received_customers[$key]))
                                <td>{{$received_customers[$key]->shipping_first_name.' ('.$received_customers[$key]->username.')'}}</td>
                                <td>
                                    {{$received_customers[$key]->cash_rec_from_cust}}
                                    @php $balancetotalcc = $balancetotalcc + $received_customers[$key]->cash_rec_from_cust @endphp
                                </td>
                                @else
                                <td></td>
                                <td></td>
                                @endif
                                @if(isset($paid_others[$key]))
                                <td>{{$paid_others[$key]->person}} - {{$paid_others[$key]->purpose}}</td>
                                <td>
                                    {{$paid_others[$key]->amount}}
                                    @php $paidtotalcc = $paidtotalcc + $paid_others[$key]->amount @endphp
                                    @if($locking_state == 'Open')
                                        <a class="edit-paid text-primary" id="OT{{$key}}" data-id="{{$paid_others[$key]->id}}"><i class="fas fa-edit"></i></a>
                                    @endif
                                </td>
                                @else
                                <td></td>
                                <td></td>
                                @endif
                            </tr>
                        @endfor
                        <tr class="bg-warning text-dark">
                            <td><b>Balance Total</b></td>
                            <td><b>{{$balancetotal}}</b></td>
                            <td><b>Closing Balance</b></td>
                            <td><b>{{$balancetotal - $paidtotal}}</b></td>
                            <td><b>Balance Total</b></td>
                            <td><b>{{$balancetotalcc}}</b></td>
                            <td><b>Closing Balance</b></td>
                            <td><b>{{$balancetotalcc - $paidtotalcc}}</b></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="add-record-div text-center">
                @if($locking_state == 'Open')
                    <button type="button" class="btn btn-sm btn-outline-primary add-record" id="addrecordrow" name="addrecordrow">Add</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary lock-record" id="lockrecords" name="lockrecords" data-date ="{{$enddate}}" data-opening_bal_pt_cash="{{$opening_closing_balance->closing_balance_ptcash}}" data-closing_bal_pt_cash="{{$balancetotal - $paidtotal}}" data-opening_bal_cust_cash="{{$opening_closing_balance->closing_balance_cust_cash}}" data-closing_bal_cust_cash="{{$balancetotalcc - $paidtotalcc}}">Lock Records</button>
                @else
                    <button type="button" class="btn btn-sm btn-outline-secondary unlock-record" id="unlockrecords" name="unlockrecords" data-date ="{{$enddate}}">Unlock Records</button>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="addnewrecords" tabindex="-1" role="dialog" aria-labelledby="addnewrecords" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{route('cash-report')}}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addnewrecords"><span id="headertext">Add Record</span></h5>
                        <input type="hidden" name="id" id="id">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="recorddate">Date</label>
                            </div>
                            <div class="col-md-8">
                                <input type="date" name="recorddate" id="recorddate" class="form-control form-control-sm " value="{{$enddate}}" readonly>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="recordcashmode">Cash Mode</label>
                            </div>
                            <div class="col-md-8">
                                <div class="btn-group btn-group-toggle btn-block btn-sm" data-toggle="buttons">
                                    <label class="btn btn-outline-primary btn-sm " id="labelrecordcashmodepaid">
                                        <input type="radio" class="recordcashmode modal-input" name="recordcashmode" id="recordcashmodepaid" value="Paid"> Paid
                                    </label>
                                    <label class="btn btn-outline-primary btn-sm" id="labelrecordcashmoderec">
                                        <input type="radio" class="recordcashmode modal-input" name="recordcashmode" id="recordcashmoderec" value="Received"> Received
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="recordusertype">User Type</label>
                            </div>
                            <div class="col-md-8">
                                <div class="btn-group btn-group-toggle btn-block btn-sm" data-toggle="buttons">
                                    <label class="btn btn-outline-primary btn-sm " id="labelrecordusertypeofstaff">
                                        <input type="radio" class="recordusertype modal-input" name="recordusertype" id="recordusertypeofstaff" value="Office Staff"> Office Staff
                                    </label>
                                    <label class="btn btn-outline-primary btn-sm" id="labelrecordusertypedelboy">
                                        <input type="radio" class="recordusertype modal-input" name="recordusertype" id="recordusertypedelboy" value="DelBoy"> Del Boy
                                    </label>
                                    <label class="btn btn-outline-primary btn-sm " id="labelrecordusertypecust">
                                        <input type="radio" class="recordusertype modal-input" name="recordusertype" id="recordusertypecust" value="Customer"> Customer
                                    </label>
                                    <label class="btn btn-outline-primary btn-sm" id="labelrecordusertypeoth">
                                        <input type="radio" class="recordusertype modal-input" name="recordusertype" id="recordusertypeoth" value="Other"> Other
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="recorduser">Person</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" name="recorduseroth" id="recorduseroth" class="form-control form-control-sm others modal-input">
                                <select name="recorduseros" id="recorduseros" class="select selectpicker form-control form-control-sm d-none office-staff modal-input" title="Select Office Staff" >
                                    @forelse($officestaff as $key=>$staff)
                                        <option value="{{$staff->username}}">{{$staff->username}}</option>
                                    @empty
                                        <option value="Not Found" disabled>Not Found</option>
                                    @endforelse
                                </select>
                                <select name="recorduserdb" style="display:none;" id="recorduserdb" class="select selectpicker form-control form-control-sm d-none delboy modal-input" title="Select Delboy" >
                                    @forelse($delboys as $key=>$delboy)
                                        <option value="{{$delboy->username}}">{{$delboy->username}}</option>
                                    @empty
                                        <option value="Not Found" disabled>Not Found</option>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="recordamount">Amount</label>
                            </div>
                            <div class="col-md-8">
                                <input type="number" class="form-control form-control-sm modal-input" name="recordamount" id="recordamount">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="recordpurpose">Purpose</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control form-control-sm modal-input" name="recordpurpose" id="recordpurpose" >
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="recordremark">Remark</label>
                            </div>
                            <div class="col-md-8">
                                <textarea class="form-control form-control-sm modal-input" name="recordremark" id="recordremark" cols="5" rows="5"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="btntype" id="btntype" value="addrecord">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editrecordsmodal" tabindex="-1" role="dialog" aria-labelledby="editrecordsmodal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{route('cash-report')}}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="editrecordsmodal"><span id="headertextedit">Edit Record</span></h5>
                        <input type="hidden" name="editid" id="editid">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="recorduseredit">Person</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" name="recorduseredit" id="recorduseredit" class="form-control form-control-sm others modal-input" readonly>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="recordamountedit">Amount</label>
                            </div>
                            <div class="col-md-8">
                                <input type="number" class="form-control form-control-sm modal-input" name="recordamountedit" id="recordamountedit">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="recordpurposeedit">Purpose</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control form-control-sm modal-input" name="recordpurposeedit" id="recordpurposeedit" >
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="recordremarkedit">Remark</label>
                            </div>
                            <div class="col-md-8">
                                <textarea class="form-control form-control-sm modal-input" name="recordremarkedit" id="recordremarkedit" cols="5" rows="5"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="btntype" id="btntype" value="editrecord">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('.add-record').click(function(){
            // $(".modal-input").val('');
            $("#btntype").val('addrecord');
            $("#id").val('');
            $("#headertext").text('Add Record');
            $("#addnewrecords").modal("show");
        });
        $('.recordusertype').change(function(){
            console.log($(this).val());
            if($(this).val() == 'DelBoy')
            {
                $(".delboy").removeClass("d-none");
                $(".office-staff").addClass('d-none');
                $(".others").addClass('d-none');
            }
            else if($(this).val() == 'Office Staff')
            {
                $(".delboy").addClass("d-none");
                $(".office-staff").removeClass('d-none');
                $(".others").addClass('d-none');
            }
            else{
                $(".delboy").addClass("d-none");
                $(".office-staff").addClass('d-none');
                $(".others").removeClass('d-none');
            }
        });
        $('.lock-record').click(function()
        {
            if(confirm("If you Lock Records you are unable update record untill you unlock them!"))
            {
                // console.log("Locked");
                var dataString = ({_token:"{{ csrf_token() }}",date:""+$(this).data('date'),opening_bal_pt_cash:""+$(this).data("opening_bal_pt_cash"),closing_bal_pt_cash:""+$(this).data("closing_bal_pt_cash"),opening_bal_cust_cash:""+$(this).data("opening_bal_cust_cash"),closing_bal_cust_cash:""+$(this).data("closing_bal_cust_cash"),type:"Lock"});
                $.ajax({
                    type: "GET",
                    url: "{{route('cash-report')}}",
                    data: dataString,
                    cache: false,
                    success: function (data) {
                        // console.log(data);
                        window.location.reload();
                    }
                });
            }
        });
        $('.unlock-record').click(function()
        {
            if(confirm("If you Unlock Records, Records from this date to today will be unlocked!"))
            {
                // console.log("Locked");
                var dataString = ({_token:"{{ csrf_token() }}",date:""+$(this).data('date'),type:"Open"});
                $.ajax({
                    type: "GET",
                    url: "{{route('cash-report')}}",
                    data: dataString,
                    cache: false,
                    success: function (data) {
                        window.location.reload();
                    }
                });
            }
        });
        $(".edit-paid").click(function(){
            var dataString = ({_token:"{{ csrf_token() }}",id:""+$(this).data('id'),type:"getrecord"});
            $.ajax({
                type: "GET",
                url: "{{route('cash-report')}}",
                data: dataString,
                cache: false,
                success: function (data) {
                    let record = JSON.parse(data);
                    $("#recorduseredit").val(record.person);
                    $("#recordamountedit").val(record.amount);
                    $("#recordpurposeedit").val(record.purpose);
                    $("#recordremarkedit").val(record.remark);
                    $("#editid").val(record.id);
                    $("#editrecordsmodal").modal("show");
                }
            });
        });
        // $(".edit-paid").click(function(){
        //     var dataString = ({_token:"{{ csrf_token() }}",id:""+$(this).data('id'),type:"getrecord"});
        //     $.ajax({
        //         type: "GET",
        //         url: "{{route('cash-report')}}",
        //         data: dataString,
        //         cache: false,
        //         success: function (data) {
        //             let record = JSON.parse(data);
        //             console.log(record);
        //             $("#headertext").text('Edit Record');
        //             $("#btntype").val('editrecord');
        //             $("#id").val(record.id);
        //             $("#recorddate").val(record.date);
        //             if(record.rec_paid == 'Paid')
        //             {
        //                 $("#recordcashmodepaid").prop('checked',true);
        //                 $("#labelrecordcashmodepaid").addClass('active');

        //                 $("#recordcashmoderec").prop('checked',false);
        //                 $("#labelrecordcashmoderec").removeClass('active');
        //             }
        //             else if(record.rec_paid == 'Received')
        //             {
        //                 $("#recordcashmoderec").prop('checked',true);
        //                 $("#labelrecordcashmoderec").addClass('active');

        //                 $("#recordcashmodepaid").prop('checked',false);
        //                 $("#labelrecordcashmodepaid").removeClass('active');
        //             }

        //             if(record.usertype == 'DelBoy')
        //             {
        //                 $("#recordusertypedelboy").prop('checked',true);
        //                 $("#labelrecordusertypedelboy").addClass('active');

        //                 $("#recordusertypeofstaff").prop('checked',false);
        //                 $("#labelrecordusertypeofstaff").removeClass('active');

        //                 $("#recordusertypecust").prop('checked',false);
        //                 $("#labelrecordusertypecust").removeClass('active');

        //                 $("#recordusertypeoth").prop('checked',false);
        //                 $("#labelrecordusertypeoth").removeClass('active');
                        
        //                 $(".delboy").removeClass('d-none');
        //                 $(".office-staff").addClass('d-none');
        //                 $(".others").addClass('d-none');
        //                 $(".delboy").val(record.person);
        //                 $(".delboy").selectpicker('refresh');

        //             }
        //             else if(record.usertype == 'Office Staff')
        //             {
        //                 $("#recordusertypeofstaff").prop('checked',true);
        //                 $("#labelrecordusertypeofstaff").addClass('active');

        //                 $("#recordusertypedelboy").prop('checked',false);
        //                 $("#labelrecordusertypedelboy").addClass('active');

        //                 $("#recordusertypecust").prop('checked',false);
        //                 $("#labelrecordusertypecust").removeClass('active');

        //                 $("#recordusertypeoth").prop('checked',false);
        //                 $("#labelrecordusertypeoth").removeClass('active');

        //                 $(".office-staff").removeClass('d-none');
        //                 $(".delboy").addClass('d-none');
        //                 $(".others").addClass('d-none');
        //                 $(".office-staff").val(record.person).selectpicker('refresh');
        //             }
        //             else if(record.usertype == 'Customer')
        //             {
        //                 $("#recordusertypecust").prop('checked',true);
        //                 $("#labelrecordusertypecust").addClass('active');

        //                 $("#recordusertypedelboy").prop('checked',false);
        //                 $("#labelrecordusertypedelboy").removeClass('active');

        //                 $("#recordusertypeofstaff").prop('checked',false);
        //                 $("#labelrecordusertypeofstaff").removeClass('active');

        //                 $("#recordusertypeoth").prop('checked',false);
        //                 $("#labelrecordusertypeoth").removeClass('active');

        //                 $(".others").removeClass('d-none');
        //                 $(".delboy").addClass('d-none');
        //                 $(".office-staff").addClass('d-none');
        //                 $(".others").val(record.person);
        //             }
        //             else
        //             {
        //                 $("#recordusertypeoth").prop('checked',true);
        //                 $("#labelrecordusertypeoth").addClass('active');

        //                 $("#recordusertypedelboy").prop('checked',false);
        //                 $("#labelrecordusertypedelboy").removeClass('active');

        //                 $("#recordusertypeofstaff").prop('checked',false);
        //                 $("#labelrecordusertypeofstaff").removeClass('active');

        //                 $("#recordusertypecust").prop('checked',false);
        //                 $("#labelrecordusertypecust").removeClass('active');

        //                 $(".others").removeClass('d-none');
        //                 $(".delboy").addClass('d-none');
        //                 $(".office-staff").addClass('d-none');
        //                 $(".others").val(record.person);
        //             }


                
        //             $("#recordamount").val(record.amount);
        //             $("#recordpurpose").val(record.purpose);
        //             $("#recordremark").val(record.remark);


        //             $("#addnewrecords").modal("show");
        //         }
        //     });
        // });
    </script>
@endsection