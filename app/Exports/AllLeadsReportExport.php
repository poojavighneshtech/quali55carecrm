<?php

namespace App\Exports;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use App\Models\Renewal;
use App\Models\UserLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromView;
use DateTime;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AllLeadsReportExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $get_all_leads;
    protected $json_decode_all_leads;
    function __construct($get_all_leads,$json_decode_all_leads) {
        $this->get_all_leads = $get_all_leads;
        $this->json_decode_all_leads = $json_decode_all_leads;
    }

    public function view(): View
    {
        $get_all_leads = $this->get_all_leads;
        $json_decode_all_leads = $this->json_decode_all_leads;
        //dd($data);
        return view('export_views.export-leads',compact('get_all_leads','json_decode_all_leads'));
    }
}