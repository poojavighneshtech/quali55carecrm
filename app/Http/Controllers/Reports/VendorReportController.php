<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Lead\customer_detail;
use App\Models\Lead\lead;
use App\Models\Lead\leads_log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class VendorReportController extends Controller
{
   public function isLoggedIn()
   {
      $data = session('isLoggedIn');
      //print_r($data);      
      return $data;
   }
   public function vendor_product_report1()
   {
        // $vendor_product_report = DB::select("SELECT 
        //                                         vendor_details.registered_name as registered_name,
        //                                         vendor_details.id as id,
        //                                         vendor_products.status as status
        //                                      FROM vendor_details,vendor_products 
        //                                      Where vendor_details.id = vendor_products.vendor_id 
        //                                      AND vendor_details.authentication_status = 'Approved' ");
        // $data['vendor_product_report'] = json_decode(json_encode($vendor_product_report), true);
        //print_r($data['vendor_product_report']);

        $data['vendor_product_report'] = array();
        $vendor_details = DB::select("SELECT * FROM vendor_details WHERE authentication_status='Approved' ");
        $data['vendor_details'] = json_decode(json_encode($vendor_details),true);
        //print_r($data['vendor_details']);
        $i=0;
        foreach ($data['vendor_details'] as $vendor_details)
        {
            $vendor_id = $vendor_details['id'];
            $vendor_name = $vendor_details['registered_name'];
            $vendor_product_details = DB::select("SELECT * FROM vendor_products WHERE vendor_id = $vendor_id");
            $data['vendor_product_details'] = json_decode(json_encode($vendor_product_details),true);
            $temp_count_pending = 0;
            $temp_count_rejected = 0;
            $temp_count_approved = 0;
            $total_products = count($data['vendor_product_details']);
            foreach($data['vendor_product_details'] as $vendor_product_details)
            {
                if($vendor_product_details['status'] == "Approved")
                {
                    $temp_count_approved = $temp_count_approved + 1;
                }
                elseif($vendor_product_details['status'] == "Rejected")
                {
                    $temp_count_rejected = $temp_count_rejected + 1;
                }
                elseif($vendor_product_details['status'] == "Pending")
                {
                    $temp_count_pending = $temp_count_pending + 1;
                }
            }
            $data['vendor_product_report'][$i]['registered_name'] = $vendor_name;
            $data['vendor_product_report'][$i]['pending'] = $temp_count_pending;
            $data['vendor_product_report'][$i]['rejected'] = $temp_count_rejected;
            $data['vendor_product_report'][$i]['approved'] = $temp_count_approved;
            $data['vendor_product_report'][$i]['total'] = $total_products;
            $i = $i + 1;       
        }
        return view('Reports/vendor_product_reports',$data);

   }
   public function vendor_product_report()
   {
        $data['vdr_details'] = array();
        $data['vdr_prod_details'] = array();
        $data['vdr_prod_rent_details'] = array();
        //-*-*-*-*-* Vendor Details *-*-*-*-*-//
        // $query = DB::select("SELECT vendor_details.id as vendor_id, vendor_details.registered_name as vendor_name FROM vendor_details");
        $query = DB::table('vendor_details')
                        ->select(
                            'vendor_details.id as vendor_id',
                            'vendor_details.registered_name as vendor_name',
                        )
                        ->when(session('city_based_access') == '1',function($query){
                            $query->where('vendor_details.of_city',session('user_city'));
                        })
                        ->get();
        $vdr_info = json_decode(json_encode($query),true);
        $i = 0;
        foreach($vdr_info as $sgl_vdr)
        {
            $vdr_id = $sgl_vdr['vendor_id'];
            $vdr_name = $sgl_vdr['vendor_name'];
            $query = DB::select("SELECT vendor_products.status as status FROM vendor_products WHERE vendor_id = $vdr_id");
            $vdr_prod_info = json_decode(json_encode($query),true);
            $count_pending = 0;
            $count_rejected = 0;
            $count_approved = 0;
            $total_products = count($vdr_prod_info);
            foreach($vdr_prod_info as $sgl_prod)
            {
                if($sgl_prod['status'] == "Approved")
                {
                    $count_approved = $count_approved + 1;
                }
                elseif($sgl_prod['status'] == "Rejected")
                {
                    $count_rejected = $count_rejected + 1;
                }
                elseif($sgl_prod['status'] == "Pending" || $sgl_prod['status'] == "Requested")
                {
                    $count_pending = $count_pending + 1;
                }
            }
            $query = DB::select("SELECT count(*) as rented_products FROM vendor_rented_products WHERE vendor_id = $vdr_id AND status = 'On Rent'");
            $rented_products = json_decode(json_encode($query),true);
            $query = DB::select("SELECT products.product_name as product_name, vendor_products.id as id, vendor_products.product_rent_approved as product_rent_approved, vendor_products.batch as batch, vendor_products.product_quantity as product_quantity FROM vendor_products,products WHERE vendor_products.vendor_id = $vdr_id AND vendor_products.status = 'Approved' AND vendor_products.product_id = products.id");
            $second_rowDetails = json_decode(json_encode($query),true);
            if(isset($second_rowDetails))
            {
                for($j=0; $j<count($second_rowDetails); $j++)
                {
                    $vdr_product_id = $second_rowDetails[$j]['id'];
                    $query = DB::select("SELECT 
                                            products.product_name as product_name, 
                                            vendor_products.batch as batch,                                            
                                            vendor_products.product_rent_approved as product_rent_approved,
                                            vendor_rented_products.rental_date as rented_date, 
                                            vendor_rented_products.pickup_date as pickup_date 
                                        FROM 
                                            vendor_rented_products,
                                            products,
                                            vendor_products 
                                        WHERE 
                                            vendor_rented_products.vendor_product_id = $vdr_product_id
                                            AND 
                                            vendor_products.id = vendor_rented_products.vendor_product_id 
                                            AND 
                                            vendor_products.product_id = products.id");
                    $data['product_count'] = json_decode(json_encode($query),true);
                    $second_rowDetails[$j]['rented_qty_count'] = count($data['product_count']);
                    if(isset($data['product_count'][0]))
                    {
                        $second_rowDetails[$j]['rent'] = $data['product_count'][0]['product_rent_approved'];
                    }
                    else
                    {
                        $second_rowDetails[$j]['rent'] = 0;
                    }
                    $second_rowDetails[$j]['rented_products'] = $data['product_count'];
                }
            }
            $data['vdr_details'][$i]['vendor_name'] = $vdr_name;
            $data['vdr_details'][$i]['pending'] = $count_pending;
            $data['vdr_details'][$i]['rejected'] = $count_rejected;
            $data['vdr_details'][$i]['approved'] = $count_approved;
            $data['vdr_details'][$i]['rented'] = $rented_products[0]['rented_products'];
            $data['vdr_details'][$i]['total'] = $total_products;
            $data['vdr_details'][$i]['product_details'] = $second_rowDetails;
            $i = $i + 1;
        }
    return view('Reports/vendor_product_reports',$data);
   }
    public function vendor_select()
    {
        $vendor_details = DB::select("SELECT registered_name,id FROM vendor_details WHERE authentication_status = 'Approved'");
        $data['vendor_details'] = json_decode(json_encode($vendor_details),true);
        return view('Reports/vdr_prod_rep_select',$data);
    }
    public function rented_equipment_report($vendor_id)
    {
        $data['equipment_details'] = array();
        $vendor_details = DB::select("SELECT registered_name,id FROM vendor_details WHERE authentication_status = 'Approved'");
        $data['vendor_details'] = json_decode(json_encode($vendor_details),true);

        $vdr_details = DB::select("SELECT registered_name,id FROM vendor_details WHERE authentication_status = 'Approved' AND id = $vendor_id");
        $data['vdr_detail'] = json_decode(json_encode($vdr_details),true);

            $vendor_products = DB::select("SELECT DISTINCT vendor_products.id as vdr_product_id, vendor_products.product_id as product_id, products.product_name as product_name FROM vendor_products,products WHERE vendor_products.vendor_id = $vendor_id AND vendor_products.product_id = products.id");
            $vendor_products = json_decode(json_encode($vendor_products),true);
            $temp_array = array();
            // print_r($vendor_products);
            foreach($vendor_products as $product)
            {
                if(in_array($product['product_id'],$temp_array))
                {
                    
                }
                else
                {
                    array_push($temp_array,$product['product_id']);
                    $vendor_product_id = $product['vdr_product_id'];
                    $query = DB::select("SELECT count(*) as rented_products FROM vendor_rented_products WHERE vendor_id = $vendor_id AND vendor_product_id = $vendor_product_id AND status = 'On Rent'");
                    $rented_products = json_decode(json_encode($query),true);
                    
                    $count = count($data['equipment_details']);
                    $data['equipment_details'][$count]['equipment_name'] = $product['product_name'];
                    $data['equipment_details'][$count]['rented_qty'] = $rented_products[0]['rented_products'];
                }
            }
        // print_r($data['equipment_details']);
        return view('Reports/vdr_prod_rep_select',$data);
    }
   
}
