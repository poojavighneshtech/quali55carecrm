<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Expense Voucher</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.2/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <style>
         @media print {
            body{
                width: 21cm;
                height: 29.7cm;
                
            } 
        }
        br {
            display: block;
            margin: 10px 0;
            margin-top: 10px;
        }
        table, tr, tbody, thead, th, td {
            /* border: 1px solid rgb(31, 31, 31); */
            border-top:    1px solid  rgb(31, 31, 31);
            border-left:    1px solid  rgb(31, 31, 31);
            border-right:    1px solid  rgb(31, 31, 31);
            border-bottom:    1px solid  rgb(31, 31, 31);
            /* border-collapse: collapse; */
        }
    </style>
</head>
<body>
    
    <div class="row">
        <div class="col-md-3 text-center my-auto">
            <img src="{{url('/')}}/assets/images/logo2-1 old.png" width="65%" height="65%" alt="Stamp" srcset="">
        </div>
        <div class="col-md-6 text-center my-auto">
            <h4>Quali5Care & Consulting Private Limited</h4>
            <h6>Office No.304, Surya House, Near Vidyavihar Station, Vidyavihar(E), Mumbai - 400077</h6>
        </div>
        <div class="col-md-3 text-center my-auto align-items-center">
            <h5>{{$voucher_id}}</h5>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <span><b>Location: </b></span><span id="del_boy_name">{{$exp_details[0]->city}}</span>
        </div>
        <div class="col-md-3">
            <span><b>Name: </b></span><span id="del_boy_name">{{$exp_details[0]->username}}</span>
        </div>
        <div class="col-md-3">
            <span><b>Date: </b></span><span id="voucher_date">{{date('d-M-y',strtotime($exp_details[0]->exp_date))}}</span>
        </div>
        <div class="col-md-3">
            <span><b>Checked By: </b></span><span id="checked_by"> @if($day_expense->status == 'Verified' && $day_expense->status == 'Settled'){{$day_expense->verified_by}}@else{{"Pending"}}@endif</span>
        </div>
    </div>
    <div class="table">
        <table class="table border table-sm border-dark">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Customer Name</th>
                    <th>Location</th>
                    <th>Product</th>
                    <th>Order Type</th>
                    <th>Received</th>
                    <th>Paid</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($exp_details as $key=>$expense)
                    <tr>
                        <td>{{$key+1}}</td>
                        <td>{{$expense->shipping_first_name}}</td>
                        <td>{{$expense->location}}</td>
                        <td>{{$expense->line_item_1}}</td>
                        <td>{{$expense->deliverypickup}}</td>
                        <td class="text-right">{{number_format($expense->cash_rec_from_cust,2)}}</td>
                        <td class="text-right">{{number_format($expense->depo_returned,2)}}</td>
                        <td class="text-right">{{number_format($expense->cash_rec_from_cust - $expense->depo_returned,2)}}</td>
                    </tr>
                @endforeach
            </body>
        </table>
    </div>
    <div class="row">
        <div class="col-md-1">

        </div>
        <div class="col-md-11">
            <span><b>Travel Available: </b></span><span id="travel_available">₹{{number_format($day_expense->cash_from_office,2)}}</span>
        </div>
    </div>
    <div class="table">
        <table class="table border table-sm border-dark">
            <thead>
                <tr>
                    <th>No</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Transport Medium</th>
                    <th>Transport</th>
                    <th>Labour</th>
                    <th>Hardware Charges</th>
                    <th>Other</th>
                    <th>Floor</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($exp_details as $key=>$expense)
                    <tr>
                        <td>{{$key+1}}</td>
                        <td>{{$expense->fromlocation}}</td>
                        <td>{{$expense->tolocation}}</td>
                        <td>{{str_replace("]","",str_replace("[","",$expense->transport_medium))}}</td>
                        <td class="text-right">{{number_format($expense->transport,2)}}</td>
                        <td class="text-right">{{number_format($expense->labour,2)}}</td>
                        <td class="text-right">{{number_format($expense->hardware_expenses,2)}}</td>
                        <td>{{"-"}}</td>
                        <td>{{"-"}}</td>
                        <td>{{"-"}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="row">
        <div class="col-md-2">
            <span><b>Lunch/Dinner: </b></span><span id="travel_balance">{{$day_expense->lunch_dinner}}</span>
        </div>
        <div class="col-md-2">
            <span><b>Office Expenses: </b></span><span id="travel_balance">{{$day_expense->office_expenses}}</span>
        </div>
        <div class="col-md-2">
            <span><b>Fuel Expenses: </b></span><span id="travel_balance">{{$day_expense->fuel_expenses}}</span>
        </div>
        <div class="col-md-2">
            <span><b>Monthly Pass: </b></span><span id="travel_balance">{{$day_expense->monthly_pass}}</span>
        </div>
        <div class="col-md-3 text-right">
            <span><b>Total: </b> </span><span id="total">₹{{number_format(array_sum($exp_details->pluck('transport')->toArray()) + array_sum($exp_details->pluck('labour')->toArray()) + array_sum($exp_details->pluck('hardware_expenses')->toArray()) + $day_expense->monthly_pass + $day_expense->office_expenses + $day_expense->lunch_dinner,2)}}</span>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            
        </div>
        <div class="col-md-4 text-right">
            <span><b>Paid: </b></span><span id="paid">₹0</span>
        </div>
        <div class="col-md-4 text-right">
            <span><b>New Balance: </b> </span><span id="new_balance">₹{{number_format(((array_sum($exp_details->pluck('cash_rec_from_cust')->toArray()) + $day_expense->cash_from_office) -array_sum($exp_details->pluck('depo_returned')->toArray())) - (array_sum($exp_details->pluck('transport')->toArray()) + array_sum($exp_details->pluck('labour')->toArray()) + array_sum($exp_details->pluck('hardware_expenses')->toArray()) + $day_expense->monthly_pass + $day_expense->office_expenses + $day_expense->lunch_dinner),2)}}</span>
        </div>
    </div>





    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>