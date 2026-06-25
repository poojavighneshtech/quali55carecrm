
<ENVELOPE>
    <HEADER>
     <TALLYREQUEST>Import Data</TALLYREQUEST>
    </HEADER>
    <BODY>
        <IMPORTDATA>
            <REQUESTDESC>
                <REPORTNAME>Vouchers</REPORTNAME>
            </REQUESTDESC>
            <REQUESTDATA>
                <TALLYMESSAGE xmlns:UDF="TallyUDF">
                        @foreach ($getexpenses as $date =>$data)
                            @foreach ($data as $key=>$expense)
                                @if($expense->transport+($expense->monthly_pass!=null ? $expense->monthly_pass : 0) != 0 
                                    || $expense->labour !=0
                                    || ($expense->lunch_dinner!=null ? $expense->lunch_dinner : 0) != 0
                                    || ($expense->office_expenses !=null ? $expense->office_expenses : 0 ) != 0)
                                    
                                    <VOUCHER VCHTYPE="Payment" ACTION="Create" OBJVIEW="Accounting Voucher View">
                                        <DATE>{{date('Ymd',strtotime($date))}}</DATE>
                                        <NARRATION>Being amt paid to {{$expense->user_name}} - Voucher No.: {{$expense->receipt_no}}</NARRATION>
                                        <PARTYLEDGERNAME>Petty Cash</PARTYLEDGERNAME>
                                        <VOUCHERTYPENAME>Payment</VOUCHERTYPENAME>
                                        <PERSISTEDVIEW>Accounting Voucher View</PERSISTEDVIEW>
                                        <EFFECTIVEDATE>{{date('Ymd',strtotime($date))}}</EFFECTIVEDATE>
                                            
                                        @if($expense->transport!=null || $expense->transport!=0 || $expense->monthly_pass!=null || $expense->monthly_pass!=0 || $expense->fuel_expenses!=null || $expense->fuel_expenses!=0)
                                            <ALLLEDGERENTRIES.LIST>
                                                <LEDGERNAME>Transport Charges - Pur</LEDGERNAME>
                                                <ISDEEMEDPOSITIVE>Yes</ISDEEMEDPOSITIVE>
                                                <ISLASTDEEMEDPOSITIVE>Yes</ISLASTDEEMEDPOSITIVE>
                                                <AMOUNT>-{{number_format($expense->transport+($expense->monthly_pass!=null ? $expense->monthly_pass : 0)+$expense->fuel_expenses,2,'.','')}}</AMOUNT>
                                            </ALLLEDGERENTRIES.LIST>
                                        @endif
                                            
                                        @if($expense->labour!=0 || $expense->labour!=null)
                                            <ALLLEDGERENTRIES.LIST>
                                                <LEDGERNAME>Outside Labour Charges</LEDGERNAME>
                                                <ISDEEMEDPOSITIVE>Yes</ISDEEMEDPOSITIVE>
                                                <ISLASTDEEMEDPOSITIVE>Yes</ISLASTDEEMEDPOSITIVE>
                                                <AMOUNT>-{{number_format($expense->labour,2,'.','')}}</AMOUNT>
                                            </ALLLEDGERENTRIES.LIST>
                                        @endif
                                        
                                        @if($expense->lunch_dinner!=0 || $expense->lunch_dinner!=null)
                                            <ALLLEDGERENTRIES.LIST>
                                                <LEDGERNAME>Staff Walfare Exp</LEDGERNAME>
                                                <ISDEEMEDPOSITIVE>Yes</ISDEEMEDPOSITIVE>
                                                <ISLASTDEEMEDPOSITIVE>Yes</ISLASTDEEMEDPOSITIVE>
                                                <AMOUNT>-{{number_format(($expense->lunch_dinner!=null ? $expense->lunch_dinner : 0),2,'.','')}}</AMOUNT>
                                            </ALLLEDGERENTRIES.LIST>
                                        @endif
                                            
                                        @if($expense->office_expenses!=0 || $expense->office_expenses!=null || $expense->expenses !=0 || $expense->office_expenses!=null )
                                            <ALLLEDGERENTRIES.LIST>
                                                <LEDGERNAME>Office Exp-Non Gst</LEDGERNAME>
                                                <ISDEEMEDPOSITIVE>Yes</ISDEEMEDPOSITIVE>
                                                <ISLASTDEEMEDPOSITIVE>Yes</ISLASTDEEMEDPOSITIVE>
                                                <AMOUNT>-{{number_format(($expense->office_expenses !=null ? $expense->office_expenses : 0 )+($expense->expenses !=null ? $expense->expenses : 0 ),2,'.','')}}</AMOUNT>
                                            </ALLLEDGERENTRIES.LIST>
                                        @endif
                                
                                        <ALLLEDGERENTRIES.LIST>
                                            <LEDGERNAME>Petty Cash</LEDGERNAME>
                                            <ISDEEMEDPOSITIVE>No</ISDEEMEDPOSITIVE>
                                            <ISPARTYLEDGER>Yes</ISPARTYLEDGER>
                                            <ISLASTDEEMEDPOSITIVE>No</ISLASTDEEMEDPOSITIVE>
                                            <AMOUNT>{{number_format($expense->transport+($expense->monthly_pass!=null ? $expense->monthly_pass : 0)+$expense->labour+($expense->lunch_dinner!=null ? $expense->lunch_dinner : 0)+($expense->office_expenses !=null ? $expense->office_expenses : 0 )+($expense->expenses !=null ? $expense->expenses : 0 )+($expense->fuel_expenses !=null ? $expense->fuel_expenses : 0 ),2,'.','')}}</AMOUNT>      
                                        </ALLLEDGERENTRIES.LIST>
                                
                                    </VOUCHER>
                                @endif
                            @endforeach
                        @endforeach
                </TALLYMESSAGE>
            </REQUESTDATA>
        </IMPORTDATA>
    </BODY>
</ENVELOPE> 