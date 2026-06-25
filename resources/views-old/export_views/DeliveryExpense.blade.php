    <table class="table table-hover">
        <thead >
            <tr>
                <th>Delivery Staff</th>
                <th>Expense Date</th>
                <th>Office Cash</th>
                <th>Labour</th>
                <th>Expense</th>
                <th>Transport</th>
                <th>Deposit Paid</th>
                <th>Cust Received cash</th>
                <th>Received Cash</th>
                <th>Balance</th>
                <th>Receipt No</th>
                <th>Status</th>
                <th>Verified By</th>
                <th>Verified At</th>
                <th>Settled By</th>
                <th>Settled At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($get_all_expenses as $key => $all_expense)
                <tr>
                    <td>{{$all_expense->user_name}}</td>
                    <td>{{$all_expense->exp_date}}</td>
                    <td>{{$all_expense->cash_from_office}}</td>
                    <td>{{$all_expense->labour}}</td>
                    <td>{{$all_expense->expenses}}</td>
                    <td>{{$all_expense->transport}}</td>
                    <td>{{$all_expense->deposite_paid}}</td>
                    <td>{{$all_expense->cash_received_from_customer}}</td>
                    <td>{{$all_expense->received_cash}}</td>
                    <td>
                        {{$all_expense->balance_cash}}
                    </td>
                    <td>{{$all_expense->receipt_no}}</td>
                    <td>{{$all_expense->status}}</td>
                    <td>{{$all_expense->verified_by}}</td>
                    <td>
                        @if($all_expense->status=='Verified' || $all_expense->status=='Settled')
                            {{date('d-M-Y',strtotime(\Carbon\Carbon::parse($all_expense->verified_at)->toDateString()))}} 
                            {{\Carbon\Carbon::parse($all_expense->verified_at)->format('g:i A')}}
                        @endif
                    </td>
                    <td>{{$all_expense->settled_by}}</td>
                    <td>
                        @if($all_expense->status=='Settled')
                            {{date('d-M-Y',strtotime(\Carbon\Carbon::parse($all_expense->settled_at)->toDateString()))}} 
                            {{\Carbon\Carbon::parse($all_expense->settled_at)->format('g:i A')}}    
                        @endif
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3">Total</td>
                <td>{{$totalLabour}}</td>
                <td>{{$totalExpense}}</td>
                <td>{{$totalTransport}}</td>
            </tr>
        </tbody>
    </table>