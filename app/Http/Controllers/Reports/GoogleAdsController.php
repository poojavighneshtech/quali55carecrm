<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Lead\customer_detail;
use App\Models\Lead\lead;
use App\Models\Lead\leads_log;
use App\Models\GoogleCampaignReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CollectionReportExport;
use App\Exports\GoogleAdsCampaign;

class GoogleAdsController extends Controller
{
    public function uploadRecords(Request $request)
    {
        if($_SERVER['REQUEST_METHOD'] == 'GET')
        {
            return view('GoogleAds.upload-records');
        }
        elseif($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $filename=$_FILES["upload_report"]["tmp_name"];
			if($_FILES["upload_report"]["size"] > 0)
			{
				$file = fopen($filename, "r");
                $count = 0;
                while (($csvData = fgetcsv($file, 10000, ",")) !== FALSE)
				{
                    $count++;
                    if($count>3)
                    {
                        GoogleCampaignReport::updateOrCreate([
                            'campaign'=>$csvData[0],
                            'date'=>date('Y-m-d',strtotime($csvData[1])),
                        ],
                        [
                            'campaign_state'=>$csvData[2],
                            'campaign_type'=>$csvData[3],
                            'budget'=>$csvData[4],
                            'currency_code'=>$csvData[5],
                            'clicks'=>$csvData[6],
                            'impr'=>$csvData[7],
                            'ctr'=>$csvData[8],
                            'avg_cpc'=>$csvData[9],
                            'cost'=>$csvData[10],
                            // 'conversions'=>$csvData[11],
                            'view_through_conv'=>$csvData[12],
                            'cost_conv'=>$csvData[13],
                            'conv_rate'=>$csvData[14],
                            'city'=>$csvData[15],
                            'campaignid'=>$csvData[16],
                            'created_by'=>session('user_id')
                        ]);
                    }
                }
                return redirect('googleCampaignReport')->with('Message','Uploaded Successfully!');
            }
            else
            {
                return view('GoogleAds.upload-records')-with('error','Something went wrong!');
            }
        }
    }
    public function googleCampaignReport(Request $request)
    {
        
        // dd($request->all());
        $get_min_date = Carbon::yesterday()->toDateString();
        $get_max_date = Carbon::yesterday()->toDateString();
        $campaign_state = ['Enabled','Paused','Removed'];
        $filter_data['filter_from_date'] = $get_min_date;
        $filter_data['filter_end_date'] = $get_max_date;
        $filter_data['filtercampaigns'] = null;
        $filter_data['filtersummaryreport'] = null;
        $filter_data['filter_campaign_state'] = ['Enabled'];

        $whereCondition = [];
        $campaign = $request->get('filtercampaigns');
        if(isset($campaign)){
            // $whereCondition1 = ['google_campain_report.campaign','IN',$campaign];
            // array_push($whereCondition,$whereCondition1);
            $filter_data['filtercampaigns'] = $campaign;
        }
        if(!empty($request->get('filter_from_date')) && !empty($request->get('filter_end_date'))){
            $get_min_date = $request->get('filter_from_date');
            $get_max_date = $request->get('filter_end_date');
            $filter_data['filter_from_date'] = $get_min_date;
            $filter_data['filter_end_date'] = $get_max_date;
        }
        $campaign_names = DB::table('google_campain_report')->select('campaign')->distinct('campaign')->whereIn('google_campain_report.campaign_state',$campaign_state)->whereBetween('google_campain_report.date',[$get_min_date,$get_max_date])->get();
        $temp_campaign_state = $request->get('filter_campaign_state');
        if(isset($temp_campaign_state)){
            if(in_array("All",$temp_campaign_state))
            {
                $campaign_state = ['Enabled','Paused','Removed'];
                $campaign_names = DB::table('google_campain_report')->select('campaign')->distinct('campaign')->get();
            }
            else
            {
                $campaign_state = $temp_campaign_state;
                $campaign_names = DB::table('google_campain_report')->select('campaign')->distinct('campaign')->whereIn('google_campain_report.campaign_state',$campaign_state)->whereBetween('google_campain_report.date',[$get_min_date,$get_max_date])->get();
            }
            $filter_data['filter_campaign_state'] = $campaign_state;
            // array_push($whereCondition,$whereCondition1);
        }
        if($request->boolean('filtersummaryreport')){
            $filter_data['filtersummaryreport'] = $request->boolean('filtersummaryreport');
            $distinct_campaign_name = DB::table('google_campain_report')->select('campaign')->distinct('campaign')->where($whereCondition)
            ->whereBetween('google_campain_report.date',[$get_min_date,$get_max_date])
            ->whereIn('google_campain_report.campaign_state',$campaign_state)
            ->when($request->get('filtercampaigns'),function($query)use($request){
                $query->whereIn('google_campain_report.campaign',$request->get('filtercampaigns'));
            })
            ->when(session('city_based_access') == '1',function($query){
                $query->where('google_campain_report.city',session('user_city'));
            })->get();
            $google_campaign_report = array();
            // dd($distinct_campaign_name);
            foreach($distinct_campaign_name as $key=>$tempcampaign)
            {
                $temprecord = DB::table('google_campain_report')->where('campaign',$tempcampaign->campaign)
                ->where($whereCondition)
                ->whereBetween('google_campain_report.date',[$get_min_date,$get_max_date])
                ->whereIn('google_campain_report.campaign_state',$campaign_state)
                ->when($request->get('filtercampaigns'),function($query)use($request){
                    $query->whereIn('google_campain_report.campaign',$request->get('filtercampaigns'));
                })
                ->when(session('city_based_access') == '1',function($query){
                    $query->where('google_campain_report.city',session('user_city'));
                })
                ->get();
                $google_campaign_report[$key] = collect();
                $google_campaign_report[$key]->id = $key;
                $google_campaign_report[$key]->campaign = $tempcampaign->campaign;
                $google_campaign_report[$key]->date = date('d-M-y',strtotime($get_min_date)).' to '.date('d-M-y',strtotime($get_max_date));
                $google_campaign_report[$key]->campaign_state = "-";                
                $google_campaign_report[$key]->budget = 0;
                $google_campaign_report[$key]->clicks = $temprecord->sum('clicks');
                $google_campaign_report[$key]->impr = $temprecord->sum('impr');
                $google_campaign_report[$key]->ctr = $temprecord->avg(DB::raw("ctr=replace(ctr,'%','')"));
                $google_campaign_report[$key]->avg_cpc = $temprecord->avg(DB::raw("avg_cpc=replace(avg_cpc,'--','0')"));;
                $google_campaign_report[$key]->cost = $temprecord->sum('cost');
                $google_campaign_report[$key]->conversions = $temprecord->sum('conversions');
                $google_campaign_report[$key]->total_rate = $temprecord->sum('total_rate');
                $google_campaign_report[$key]->view_through_conv = "0";
                $google_campaign_report[$key]->cost_conv = " --";
                $google_campaign_report[$key]->conv_rate = "0.00%";
                $google_campaign_report[$key]->calls_received_count = $temprecord->sum('calls_received_count');
            }
            if($request->get('btn_export'))
            {

            }else{
                // $google_campaign_report = $google_campaign_report->paginate(10);
                $google_campaign_report = collect($google_campaign_report)->paginate(10);
            }
        }
        else{

            $google_campaign_report = DB::table('google_campain_report')
                            ->select('google_campain_report.*')
                            ->where($whereCondition)
                            ->whereBetween('google_campain_report.date',[$get_min_date,$get_max_date])
                            ->whereIn('google_campain_report.campaign_state',$campaign_state)
                            ->when($request->get('filtercampaigns'),function($query)use($request){
                                $query->whereIn('google_campain_report.campaign',$request->get('filtercampaigns'));
                            })
                            ->when(session('city_based_access') == '1',function($query){
                                $query->where('google_campain_report.city',session('user_city'));
                            })
                            ->orderBy('date','DESC')
                            ->get();
                            if($request->get('btn_export'))
                            {
    
                            }else{
    
                                $google_campaign_report = $google_campaign_report->paginate(10);
                            }
        }
        // dd($google_campaign_report);
        $google_campaign_report_count = DB::table('google_campain_report')
                        ->select('google_campain_report.*')
                        ->where($whereCondition)
                        ->whereBetween('google_campain_report.date',[$get_min_date,$get_max_date])
                        ->whereIn('google_campain_report.campaign_state',$campaign_state)
                        ->when(session('city_based_access') == '1',function($query){
                            $query->where('google_campain_report.city',session('user_city'));
                        })
                        ->get()
                        ->toArray();
        $count['clicks'] = 0;
        $count['budget']  = 0;
        $count['impr'] = 0;
        $ctr = array();
        $avg_cpc = array();
        $count['cost'] = 0;
        $count['conv_count'] = 0;
        $count['conv_rate'] = 0;

        foreach($google_campaign_report_count as $key=>$value)
        {
            $count['clicks'] = $count['clicks'] + $value->clicks;
            $count['budget'] = $count['budget'] + $value->budget;
            $count['impr'] = $count['impr'] + $value->impr;
            array_push($ctr,str_replace("%",'',$value->ctr));
            array_push($avg_cpc,$value->avg_cpc);
            $count['cost'] = $count['cost'] + $value->cost;
            $count['conv_count'] = $count['conv_count'] + $value->conversions;
            $count['conv_rate'] = $count['conv_rate'] + $value->total_rate;
        }
        $count['cost'] = number_format($count['cost'], 0);
        $count['ctr_avg'] = 0;
        $count['avg_cpc_avg'] = 0;
        $a = array_filter($ctr);
        if(count($ctr)) {
            $count['ctr_avg'] = number_format(array_sum($ctr)/count($ctr), 2);
        }
        $a = array_filter($avg_cpc);
        if(count($avg_cpc)) {
            $count['avg_cpc_avg'] = number_format(array_sum($avg_cpc)/count($avg_cpc), 2);
        }
        if($request->get('btn_export'))
        {
            ob_end_clean(); // this
            ob_start(); // and this
            return Excel::download(new GoogleAdsCampaign($google_campaign_report,$filter_data,$count,$campaign_names), 'Campaign Report.xls');
        }
        else{
            return view('GoogleAds.campaign-report',compact('google_campaign_report','filter_data','count','campaign_names'));
            
        }
    }
    public function updateDetailsCampaign(Request $request){
        if($request->has('row_id')){
            DB::table('google_campain_report')->where('id',$request->get('row_id'))->update(['calls_received_count'=>$request->get('calls_received_count')]);
            return redirect()->back()->with('message','Updated Successfully!');
        }
    }
}


?> 