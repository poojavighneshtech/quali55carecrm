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

class EquipmentReportExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $equipment_report;
    function __construct($equipment_report) {
        $this->equipment_report = $equipment_report;        
    }

    public function view(): View
    {
        $equipment_report = $this->equipment_report;
        //dd($data);
        return view('export_views.export-equipment-report',compact('equipment_report'));
    }
}