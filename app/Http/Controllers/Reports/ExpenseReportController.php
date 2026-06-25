<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Lead\customer_detail;
use App\Models\Lead\lead;
use App\Models\Lead\leads_log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CollectionReportExport;

class ExpenseReportController extends Controller
{
    public function orderExpenseReport(Request $request)
    {
        $date = Carbon::yesterday()->toDateString();
        $delDate = date('d-m-Y',strtotime($date));
        // $getExpenses = DB::table('')
        return view('Reports.Expense-report');
    }
}
?>