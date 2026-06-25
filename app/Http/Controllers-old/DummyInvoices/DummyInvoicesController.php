<?php

namespace App\Http\Controllers\DummyInvoices;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DummyInvoicesController extends Controller
{
    public function viewAllDummyInvoices(Request $request)
    {
        try
        {
            $invoices = DB::table('dummy_invoice')->orderBy('date','DESC')->paginate(10);
            return view('DummyInvoices.view-all-invoices',compact('invoices'));
        }
        catch(Exception $ex)
        {
            $file = fopen(public_path().'/tempLogfile'.date('Y-m-d').'.txt','a');
            fwrite($file,date('Y-m-d')."Dummy Invoice - Exception: ".$ex);
            fclose($file);
            return redirect()->back()->with('message','Something Went Wrong! Please Try Again or Contact Administrator.');
        }
    }

    public function generateDummyInvoice(Request $request)
    {
        if($request->method() == 'GET')
        {
            $equipments = DB::table('products')->where('flag','Active')->get();
            $states = DB::table('states')->get();
            return view('DummyInvoices.create-invoice',compact('equipments','states'));
        }
        else
        {
            DB::beginTransaction();
            try
            {
                $insertDummyInvoice = [
                    'invoice_no'=>$request->get('inv_no'),
                    'date'=>$request->get('inv_date'),
                    'cust_name'=>$request->get('inv_buy_cust_name'),
                    'patient_name'=>$request->get('inv_con_cust_name'),
                    'customer_type'=>$request->get('inv_cust_type'),
                    'reg_type'=>$request->get('inv_reg_type'),
                    'floor'=>$request->get('dummyfloor'),
                    'labour'=>$request->get('dummylabourcharges'),
                    'gstin'=>$request->get('inv_gstin'),
                    'created_by'=>session('username')
                ];
                $master_inv_id = DB::table('dummy_invoice')->insertGetId($insertDummyInvoice);
                $insertDummyInvoicesAddrCon = 
                [
                    'master_id'=>$master_inv_id,
                    'name'=>$request->get('inv_con_cust_name'),
                    'addr_line_1'=>$request->get('inv_con_addr_line_1'),
                    'addr_line_2'=>$request->get('inv_con_addr_line_2'),
                    'landmark'=>$request->get('inv_con_landmark'),
                    'area'=>$request->get('inv_con_area'),
                    'city'=>$request->get('inv_con_city'),
                    'pincode'=>$request->get('inv_con_pincode'),
                    'state'=>$request->get('inv_con_state'),
                    'addr_type'=>'Consignee',
                    'created_by'=>session('username')
                ];
                DB::table('dummy_invoices_addr')->insert($insertDummyInvoicesAddrCon);
                $insertDummyInvoicesAddrBuy = 
                [
                    'master_id'=>$master_inv_id,
                    'name'=>$request->get('inv_buy_cust_name'),
                    'addr_line_1'=>$request->get('inv_buy_addr_line_1'),
                    'addr_line_2'=>$request->get('inv_buy_addr_line_2'),
                    'landmark'=>$request->get('inv_buy_landmark'),
                    'area'=>$request->get('inv_buy_area'),
                    'city'=>$request->get('inv_buy_city'),
                    'pincode'=>$request->get('inv_buy_pincode'),
                    'state'=>$request->get('inv_buy_state'),
                    'addr_type'=>'Buyer',
                    'created_by'=>session('username')
                ];
                DB::table('dummy_invoices_addr')->insert($insertDummyInvoicesAddrBuy);
                foreach($request->get('inv_equip') as $key=>$prod_id)
                {
                    $insertDummyInvoicesProd = 
                    [
                        'master_id'=>$master_inv_id,
                        'prod_id'=>$prod_id,
                        'qty'=>$request->get('inv_qty')[$key],
                        'rent'=>$request->get('inv_rent')[$key],
                        'deposit'=>$request->get('inv_deposit')[$key],
                        'prod_type'=>$request->get('inv_equip_type')[$key],
                        'transport'=>$request->get('inv_transport')[$key],
                        'created_by'=>session('username')
                    ];
                    DB::table('dummy_invoices_prod')->insert($insertDummyInvoicesProd);
                }
                DB::commit();
                return redirect()->to('view-all-dummy-invoices')->with('message','Invoice Generated');
            }
            catch(Exception $ex)
            {
                DB::rollBack();
                $file = fopen(public_path().'/tempLogfile'.date('Y-m-d').'.txt','a');
                fwrite($file,date('Y-m-d')."Dummy Invoice - Exception: ".$ex);
                fclose($file);
                return redirect()->back()->with('message','Something Went Wrong! Please Try Again or Contact Administrator.');
            }
        }
    }

    public function viewDummyInvoice(Request $request)
    {
        // dd($request->get('id'));
        $invoice_type = 'Delivery';
        $invoice_details = DB::table('dummy_invoice')->where('dummy_invoice.id',$request->get('id'))->first();
        $invoice_addr = DB::table('dummy_invoice')
                            ->join('dummy_invoices_addr','dummy_invoices_addr.master_id','=','dummy_invoice.id')
                            ->where('dummy_invoice.id',$request->get('id'))
                            ->get();

        $invoice_prod = DB::table('dummy_invoice')
                            ->join('dummy_invoices_prod','dummy_invoices_prod.master_id','=','dummy_invoice.id')
                            ->join('products','dummy_invoices_prod.prod_id','=','products.id')
                            ->select('dummy_invoices_prod.*','products.gst_rent','products.product_name','products.gst_sale','products.hsn_sac_rent','products.hsn_sac_sale')
                            ->where('dummy_invoice.id',$request->get('id'))
                            ->get();
        
        // dd($invoice_addr,$invoice_prod->groupBy('prod_type'));
        $product_details = $invoice_prod->groupBy('prod_type');
        $product_details_rent = array();
        $product_details_sale = array();
        $transport_cost = 0;
        $total_rent = 0;
        $gst_percents = array();
        foreach($product_details as $key=>$value)
        {
            $temp_ids = array();
            // dd($key);
            foreach($value as $k=>$v)
            {
                if($key == 'Rental')
                {
                    if(in_array($v->prod_id,$temp_ids))
                    {
                        $index = array_search($v->prod_id,$temp_ids);
                        $product_details_rent[$index]['product_qty'] = $product_details_rent[$index]['product_qty'] + $v->qty;
                        $product_details_rent[$index]['product_rent'] = $product_details_rent[$index]['product_rent'] + $v->rent;
                        $product_details_rent[$index]['product_deposit'] = $product_details_rent[$index]['product_deposit'] + $v->deposit;
                        $total_rent = $total_rent + ($v->rent*$v->qty) + $v->deposit;
                        $transport_cost = $transport_cost + $v->transport;
                    }
                    else
                    {
                        $index = count($product_details_rent);
                        $product_details_rent[$index]['product_name'] = $v->product_name;
                        $product_details_rent[$index]['inventory_id'] = 0;
                        $product_details_rent[$index]['order_id'] = 0;
                        $product_details_rent[$index]['product_qty'] = $v->qty;
                        $product_details_rent[$index]['product_rent'] = $v->rent;
                        $product_details_rent[$index]['product_deposit'] = $v->deposit;
                        $product_details_rent[$index]['creation_date'] = $invoice_details->date;
                        $product_details_rent[$index]['pickup_date'] = date('Y-m-d',strtotime("+1 month",strtotime($invoice_details->date)));
                        $product_details_rent[$index]['sale_rental'] = $v->prod_type;
                        $product_details_rent[$index]['gst_rent'] = $v->gst_rent;
                        $product_details_rent[$index]['gst_sale'] = $v->gst_sale;
                        $product_details_rent[$index]['hsn_sac_rent'] = $v->hsn_sac_rent;
                        $product_details_rent[$index]['hsn_sac_sale'] = $v->hsn_sac_sale;
                        $transport_cost = $transport_cost + $v->transport;
                        $total_rent = $total_rent + ($v->rent*$v->qty) + $v->deposit;
                        array_push($temp_ids,$v->prod_id);
                    }
                    array_push($gst_percents,$v->gst_rent);
                }
                else
                {
                    if(in_array($v->prod_id,$temp_ids))
                    {
                        $index = array_search($v->prod_id,$temp_ids);
                        $product_details_sale[$index]['product_qty'] = $product_details_sale[$index]['product_qty'] + $v->qty;
                        $product_details_sale[$index]['product_rent'] = $product_details_sale[$index]['product_rent'] + $v->rent;
                        $product_details_sale[$index]['product_deposit'] = $product_details_sale[$index]['product_deposit'] + $v->deposit;
                        $transport_cost = $transport_cost + $v->transport;
                        $total_rent = $total_rent + ($v->rent*$v->qty) + $v->deposit;
                    }
                    else
                    {
                        $index = count($product_details_sale);
                        $product_details_sale[$index]['product_name'] = $v->product_name;
                        $product_details_sale[$index]['inventory_id'] = 0;
                        $product_details_sale[$index]['order_id'] = 0;
                        $product_details_sale[$index]['product_qty'] = $v->qty;
                        $product_details_sale[$index]['product_rent'] = $v->rent;
                        $product_details_sale[$index]['product_deposit'] = $v->deposit;
                        $product_details_sale[$index]['creation_date'] = $invoice_details->date;
                        $product_details_sale[$index]['pickup_date'] = date('Y-m-d',strtotime("+1 month",strtotime($invoice_details->date)));
                        $product_details_sale[$index]['sale_rental'] = $v->prod_type;
                        $product_details_sale[$index]['gst_rent'] = $v->gst_rent;
                        $product_details_sale[$index]['gst_sale'] = $v->gst_sale;
                        $product_details_sale[$index]['hsn_sac_rent'] = $v->hsn_sac_rent;
                        $product_details_sale[$index]['hsn_sac_sale'] = $v->hsn_sac_sale;
                        $transport_cost = $transport_cost + $v->transport;
                        $total_rent = $total_rent + ($v->rent*$v->qty) + $v->deposit;
                        array_push($temp_ids,$v->prod_id);
                    }
                    array_push($gst_percents,$v->gst_sale);
                }
            }
        }
        $product_details = array_merge($product_details_sale,$product_details_rent);
        // dd($product_details);
        $temp_hsn_code = array();
        // $temp_hsn_code_sale = array();
        $hsn_code_details = array();
        // $hsn_code_details_sale = array();
        // dd($product_details);
        $rental_exists = false;
        $sale_exists = false;
        foreach($product_details as $key=>$value)
        {
            if($value['sale_rental'] == 'Rental')
            {
                $rental_exists = true;
                $product_details[$key]['product_rate'] = ((($value['product_rent'])/($value['gst_rent']+100))*100);
                $product_details[$key]['amount'] = ((($value['product_rent'])/($value['gst_rent']+100))*100)*$value['product_qty'];
                $product_details[$key]['amount_cal'] = ((($value['product_rent'])/($value['gst_rent']+100))*100)*$value['product_qty'];
                if(in_array($value['hsn_sac_rent'],$temp_hsn_code))
                {
                    $index = array_search($value['hsn_sac_rent'],array_reverse($temp_hsn_code,true));
                    // echo $index.' - '.$hsn_code_details[$index]['gst'].' - '.$value['gst_rent'].' - '.json_encode(array_reverse($temp_hsn_code));
                    if($hsn_code_details[$index]['gst'] == $value['gst_rent'])
                    {
                        $hsn_code_details[$index]['taxable_value'] = $hsn_code_details[$index]['taxable_value'] + $product_details[$key]['amount'];
                        // $hsn_code_details[$index]['ct_rate'] = $value['gst_rent']/2;
                        $hsn_code_details[$index]['ct_amount'] = $hsn_code_details[$index]['taxable_value']*(($value['gst_rent']/2)/100);
                        $hsn_code_details[$index]['st_amount'] = $hsn_code_details[$index]['taxable_value']*(($value['gst_rent']/2)/100);
                        $hsn_code_details[$index]['i_amount'] = $hsn_code_details[$index]['taxable_value']*(($value['gst_rent'])/100);
                    }
                    else{
                        $index = count($hsn_code_details);
                        $hsn_code_details[$index]['hsn_sac'] = $value['hsn_sac_rent'];
                        $hsn_code_details[$index]['gst'] = $value['gst_rent'];
                        $hsn_code_details[$index]['taxable_value'] = $product_details[$key]['amount'];
                        $hsn_code_details[$index]['ct_rate'] = $value['gst_rent']/2;
                        $hsn_code_details[$index]['ct_amount'] = ($product_details[$key]['product_rate'])*(($value['gst_rent']/2)/100)*$value['product_qty'];    
                        $hsn_code_details[$index]['st_rate'] = $value['gst_rent']/2;
                        $hsn_code_details[$index]['st_amount'] = ($product_details[$key]['product_rate'])*(($value['gst_rent']/2)/100)*$value['product_qty'];    
                        $hsn_code_details[$index]['i_rate'] = $value['gst_rent'];
                        $hsn_code_details[$index]['i_amount'] = ($product_details[$key]['product_rate'])*(($value['gst_rent'])/100)*$value['product_qty'];    
                        array_push($temp_hsn_code,$value['hsn_sac_rent']);
                    }
                }
                else
                {
                    $index = count($hsn_code_details);
                    $hsn_code_details[$index]['hsn_sac'] = $value['hsn_sac_rent'];
                    $hsn_code_details[$index]['gst'] = $value['gst_rent'];
                    $hsn_code_details[$index]['taxable_value'] = $product_details[$key]['amount'];
                    $hsn_code_details[$index]['ct_rate'] = $value['gst_rent']/2;
                    $hsn_code_details[$index]['ct_amount'] = ($product_details[$key]['product_rate'])*(($value['gst_rent']/2)/100)*$value['product_qty'];
                    $hsn_code_details[$index]['st_rate'] = $value['gst_rent']/2;
                    $hsn_code_details[$index]['st_amount'] = ($product_details[$key]['product_rate'])*(($value['gst_rent']/2)/100)*$value['product_qty'];
                    $hsn_code_details[$index]['i_rate'] = $value['gst_rent'];
                    $hsn_code_details[$index]['i_amount'] = ($product_details[$key]['product_rate'])*(($value['gst_rent'])/100)*$value['product_qty'];
                    array_push($temp_hsn_code,$value['hsn_sac_rent']);
                }
                $product_details[$key]['product_rate'] = number_format(((($value['product_rent']/$value['product_qty'])/($value['gst_rent']+100))*100),2);
                $product_details[$key]['amount'] = number_format(((($value['product_rent'])/($value['gst_rent']+100))*100),2);
            }
            else
            {
                $sale_exists = true;
                $product_details[$key]['product_rate'] = ((($value['product_rent'])/($value['gst_sale']+100))*100);
                $product_details[$key]['amount'] = ((($value['product_rent'])/($value['gst_sale']+100))*100)*$value['product_qty'];
                $product_details[$key]['amount_cal'] = ((($value['product_rent'])/($value['gst_sale']+100))*100)*$value['product_qty'];
                if(in_array($value['hsn_sac_sale'],$temp_hsn_code))
                {
                    $index = array_search($value['hsn_sac_sale'],array_reverse($temp_hsn_code,true));
                    if($hsn_code_details[$index]['gst'] == $value['gst_sale'])
                    {
                        $hsn_code_details[$index]['taxable_value'] = $hsn_code_details[$index]['taxable_value'] + $product_details[$key]['amount'];
                        // $hsn_code_details[$index]['ct_rate'] = $value['gst_sale']/2;
                        $hsn_code_details[$index]['ct_amount'] = number_format($hsn_code_details[$index]['taxable_value']*($value['gst_sale']/100),2)*$value['product_qty'];
                        $hsn_code_details[$index]['st_amount'] = number_format($hsn_code_details[$index]['taxable_value']*($value['gst_sale']/100),2)*$value['product_qty'];
                        $hsn_code_details[$index]['i_amount'] = number_format($hsn_code_details[$index]['taxable_value']*($value['gst_sale']/100),2)*$value['product_qty'];
                    }
                    else{
                        $index = count($hsn_code_details);
                        $hsn_code_details[$index]['hsn_sac'] = $value['hsn_sac_sale'];
                        $hsn_code_details[$index]['gst'] = $value['gst_sale'];
                        $hsn_code_details[$index]['taxable_value'] = $product_details[$key]['amount'];
                        $hsn_code_details[$index]['ct_rate'] = $value['gst_sale']/2;
                        $hsn_code_details[$index]['ct_amount'] = ($product_details[$key]['product_rate'])*(($value['gst_sale']/2)/100)*$value['product_qty'];
                        $hsn_code_details[$index]['st_rate'] = $value['gst_sale']/2;
                        $hsn_code_details[$index]['st_amount'] = ($product_details[$key]['product_rate'])*(($value['gst_sale']/2)/100)*$value['product_qty'];
                        $hsn_code_details[$index]['i_rate'] = $value['gst_sale'];
                        $hsn_code_details[$index]['i_amount'] = ($product_details[$key]['product_rate'])*(($value['gst_sale'])/100)*$value['product_qty'];
                        array_push($temp_hsn_code,$value['hsn_sac_sale']);
                    }
                }
                else
                {
                    $index = count($hsn_code_details);
                    $hsn_code_details[$index]['hsn_sac'] = $value['hsn_sac_sale'];
                    $hsn_code_details[$index]['gst'] = $value['gst_sale'];
                    $hsn_code_details[$index]['taxable_value'] = $product_details[$key]['amount'];
                    $hsn_code_details[$index]['ct_rate'] = $value['gst_sale']/2;
                    $hsn_code_details[$index]['ct_amount'] = ($product_details[$key]['product_rate'])*(($value['gst_sale']/2)/100)*$value['product_qty'];
                    $hsn_code_details[$index]['st_rate'] = $value['gst_sale']/2;
                    $hsn_code_details[$index]['st_amount'] = ($product_details[$key]['product_rate'])*(($value['gst_sale']/2)/100)*$value['product_qty'];
                    $hsn_code_details[$index]['i_rate'] = $value['gst_sale'];
                    $hsn_code_details[$index]['i_amount'] = ($product_details[$key]['product_rate'])*(($value['gst_sale'])/100)*$value['product_qty'];
                    array_push($temp_hsn_code,$value['hsn_sac_sale']);
                }
                $product_details[$key]['product_rate'] = number_format(((($value['product_rent']/$value['product_qty'])/($value['gst_sale']+100))*100),2);
                $product_details[$key]['amount'] = number_format(((($value['product_rent'])/($value['gst_sale']+100))*100),2);
            }

        }

        
        foreach($product_details as $key=>$value)
        {
            if($value['sale_rental'] == 'Rental')
            {
                $product_details[$key]['ct_rate'] = $value['gst_rent']/2;
                $product_details[$key]['ct_amount'] = ($product_details[$key]['amount_cal'])*(($value['gst_rent']/2)/100);
                $product_details[$key]['st_rate'] = $value['gst_rent']/2;
                $product_details[$key]['st_amount'] = ($product_details[$key]['amount_cal'])*(($value['gst_rent']/2)/100);
                $product_details[$key]['i_rate'] = $value['gst_rent'];
                $product_details[$key]['i_amount'] = ($product_details[$key]['amount_cal'])*(($value['gst_rent'])/100);
            }
            else
            {
                $product_details[$key]['ct_rate'] = $value['gst_sale']/2;
                $product_details[$key]['ct_amount'] = ($product_details[$key]['amount_cal'])*(($value['gst_sale']/2)/100);
                $product_details[$key]['st_rate'] = $value['gst_sale']/2;
                $product_details[$key]['st_amount'] = ($product_details[$key]['amount_cal'])*(($value['gst_sale']/2)/100);
                $product_details[$key]['i_rate'] = $value['gst_sale'];
                $product_details[$key]['i_amount'] = ($product_details[$key]['amount_cal'])*(($value['gst_sale'])/100);
            }
        }

        // dd($hsn_code_details);
        // dd($product_details,max($gst_percents),$transport_cost);

        $data['office_address'] = DB::table('misc_table')->where('field','office_address')->first('value')->value;
        $data['invoice_no_format'] = DB::table('misc_table')->where('field','invoice_no_format')->first('value')->value;
        $data['invoice_no'] = $invoice_details->invoice_no;
        $data['pan_no'] = DB::table('misc_table')->where('field','pan_no')->first('value')->value;
        // $data['bank_name'] = DB::table('misc_table')->where('field','bank_name')->first('value')->value;
        // $data['account_no'] = DB::table('misc_table')->where('field','account_no')->first('value')->value;
        // $data['branch'] = DB::table('misc_table')->where('field','branch')->first('value')->value;
        // $data['ifsc_code'] = DB::table('misc_table')->where('field','ifsc_code')->first('value')->value;   
        if(in_array($invoice_addr[0]->city,['Delhi','Gurgaon'])){
            $data['bank_name'] = DB::table('misc_table')->where('field','bank_name_del')->first('value')->value;
            $data['account_no'] = DB::table('misc_table')->where('field','account_no_del')->first('value')->value;
            $data['branch'] = DB::table('misc_table')->where('field','branch_del')->first('value')->value;
            $data['ifsc_code'] = DB::table('misc_table')->where('field','ifsc_code_del')->first('value')->value;
            $data['company_name'] = DB::table('misc_table')->where('field','comp_name_del')->first('value')->value;
            $data['company_addr_1'] = DB::table('misc_table')->where('field','comp_addr_1_del')->first('value')->value;
            $data['company_addr_2'] = DB::table('misc_table')->where('field','comp_addr_2_del')->first('value')->value;
            $data['company_gst'] = DB::table('misc_table')->where('field','comp_gst_del')->first('value')->value;
            $data['company_state'] = DB::table('misc_table')->where('field','comp_state_del')->first('value')->value;
            $data['company_state_code'] = DB::table('misc_table')->where('field','comp_state_code_del')->first('value')->value;
        }else{
            $data['bank_name'] = DB::table('misc_table')->where('field','bank_name')->first('value')->value;
            $data['account_no'] = DB::table('misc_table')->where('field','account_no')->first('value')->value;
            $data['branch'] = DB::table('misc_table')->where('field','branch')->first('value')->value;
            $data['ifsc_code'] = DB::table('misc_table')->where('field','ifsc_code')->first('value')->value;
            $data['company_name'] = DB::table('misc_table')->where('field','comp_name_mum')->first('value')->value;
            $data['company_addr_1'] = DB::table('misc_table')->where('field','comp_addr_1_mum')->first('value')->value;
            $data['company_addr_2'] = DB::table('misc_table')->where('field','comp_addr_2_mum')->first('value')->value;
            $data['company_gst'] = DB::table('misc_table')->where('field','comp_gst_mum')->first('value')->value;
            $data['company_state'] = DB::table('misc_table')->where('field','comp_state_mum')->first('value')->value;
            $data['company_state_code'] = DB::table('misc_table')->where('field','comp_state_code_mum')->first('value')->value;
        }       
        // dd($rental_exists,$sale_exists);
        // 
        if($rental_exists == true && $sale_exists == true)
        {
            $data['invoice_no_format'] = DB::table('misc_table')->where('field','invoice_no_comp')->first('value')->value.'/SRG/'.DB::table('misc_table')->where('field','invoice_no_period')->first('value')->value.'/';
        }
        else if($rental_exists == true && $sale_exists == false)
        {
            $data['invoice_no_format'] = DB::table('misc_table')->where('field','invoice_no_comp')->first('value')->value.'/SR/'.DB::table('misc_table')->where('field','invoice_no_period')->first('value')->value.'/';
        }
        else if($rental_exists == false && $sale_exists == true)
        {
            $data['invoice_no_format'] = DB::table('misc_table')->where('field','invoice_no_comp')->first('value')->value.'/SG/'.DB::table('misc_table')->where('field','invoice_no_period')->first('value')->value.'/';
        }

        // dd($data['invoice_no_format']);
        $con_address = "";
        if($invoice_addr[0]->addr_line_1 != null && $invoice_addr[0]->addr_line_1 != "" )
        {
            $con_address .= $invoice_addr[0]->addr_line_1.',';
        }
        if($invoice_addr[0]->addr_line_2 != null && $invoice_addr[0]->addr_line_2 != "" )
        {
            $con_address .= $invoice_addr[0]->addr_line_2.',';
        }
        if($invoice_addr[0]->landmark != null && $invoice_addr[0]->landmark != "" )
        {
            $con_address .= $invoice_addr[0]->landmark.',';
        }
        if($invoice_addr[0]->area != null && $invoice_addr[0]->area != "" )
        {
            $con_address .= $invoice_addr[0]->area.',';
        }
        if($invoice_addr[0]->city != null && $invoice_addr[0]->city != "" )
        {
            $con_address .= $invoice_addr[0]->city;
        }
        if($invoice_addr[0]->pincode != null && $invoice_addr[0]->pincode != "" )
        {
            $con_address .= ' - '.$invoice_addr[0]->pincode;
        }
        $buy_address = "";
        if($invoice_addr[1]->addr_line_1 != null && $invoice_addr[1]->addr_line_1 != "" )
        {
            $buy_address .= $invoice_addr[1]->addr_line_1.',';
        }
        if($invoice_addr[1]->addr_line_2 != null && $invoice_addr[1]->addr_line_2 != "" )
        {
            $buy_address .= $invoice_addr[1]->addr_line_2.',';
        }
        if($invoice_addr[1]->landmark != null && $invoice_addr[1]->landmark != "" )
        {
            $buy_address .= $invoice_addr[1]->landmark.',';
        }
        if($invoice_addr[1]->area != null && $invoice_addr[1]->area != "" )
        {
            $buy_address .= $invoice_addr[1]->area.',';
        }
        if($invoice_addr[1]->city != null && $invoice_addr[1]->city != "" )
        {
            $buy_address .= $invoice_addr[1]->city;
        }
        if($invoice_addr[1]->pincode != null && $invoice_addr[1]->pincode != "" )
        {
            $buy_address .= ' - '.$invoice_addr[1]->pincode;
        }
        $data['gst_no'] = $invoice_details->gstin;
        $data['consignee_name'] = $invoice_addr[0]->name;    
        $data['consignee'] = $con_address;
        $data['consignee_state'] = $invoice_addr[0]->state;
        // $data['consignee'] = $order_data[0]->fulldetails;
        $data['buyer_name'] = $invoice_addr[1]->name;
        $data['buyer'] = $buy_address;
        $data['buyer_state'] = $invoice_addr[1]->state;
        $data['state_code'] = 27;
        
        
        // $data['buyer'] = $order_data[0]->fulldetails;
        $data['order_date'] = $invoice_details->date;
        $data['max_gst'] = max($gst_percents);
        // dd($data['max_gst']);
        $data['transport_hsn'] = DB::table('misc_table')->where('field','trans'.max($gst_percents))->first('value')->value;
        // dd($data['transport_hsn']);
        $data['total_amount'] = $this->getIndianCurrency($total_rent+$transport_cost+$invoice_details->labour);
        $data['total_amount_no'] = $total_rent+$transport_cost+$invoice_details->labour;
        $exists = false;
        $data['total_taxable_value'] = 0;
        $data['total_central_tax'] = 0;
        $data['total_state_tax'] = 0;
        $data['total_i_tax'] = 0;
        $data['total_tax_amount'] = 0;
        $data['transport_cost'] = ((($transport_cost)/($data['max_gst']+100))*100);
        $data['labour'] = +$invoice_details->labour;
        if($transport_cost != 0)
        {
        $data['transport_cal'] = array();
        $data['transport_cal']['ct_amount'] = (($data['transport_cost']*(($data['max_gst']/2)/100)));
        $data['transport_cal']['st_amount'] = (($data['transport_cost']*(($data['max_gst']/2)/100)));
        $data['transport_cal']['i_amount'] = (($data['transport_cost']*(($data['max_gst'])/100)));
        $data['transport_cal']['taxable_value'] = $data['transport_cost'];
        foreach($hsn_code_details as $k=>$v)
        {
            if($v['hsn_sac'] == $data['transport_hsn'] && $v['gst'] == $data['max_gst'])
            {
                // dd(";");
                // dd((($transport_cost*(($data['max_gst']/2)/100))));
                $exists = true;
                $hsn_code_details[$k]['ct_amount'] = $hsn_code_details[$k]['ct_amount'] + (($data['transport_cost']*(($data['max_gst']/2)/100)));
                $hsn_code_details[$k]['st_amount'] = $hsn_code_details[$k]['st_amount'] + (($data['transport_cost']*(($data['max_gst']/2)/100)));
                $hsn_code_details[$k]['i_amount'] = $hsn_code_details[$k]['i_amount'] + (($data['transport_cost']*(($data['max_gst'])/100)));
                $hsn_code_details[$k]['taxable_value'] = $hsn_code_details[$k]['taxable_value'] + $data['transport_cost'];
            }
        }
        if($exists == false)
        {
            $index = count($hsn_code_details);
            $hsn_code_details[$index]['hsn_sac'] = $data['transport_hsn'];
            $hsn_code_details[$index]['gst'] = $data['max_gst'];
            $hsn_code_details[$index]['taxable_value'] = $data['transport_cost'];
            $hsn_code_details[$index]['ct_rate'] = $data['max_gst']/2;
            $hsn_code_details[$index]['ct_amount'] = number_format(($data['transport_cost'])*(($data['max_gst']/2)/100),2);
            
            $hsn_code_details[$index]['st_rate'] = $data['max_gst']/2;
            $hsn_code_details[$index]['st_amount'] = number_format(($data['transport_cost'])*(($data['max_gst']/2)/100),2);

            $hsn_code_details[$index]['i_rate'] = $data['max_gst'];
            $hsn_code_details[$index]['i_amount'] = number_format(($data['transport_cost'])*(($data['max_gst'])/100),2);
            }
        }
        foreach($hsn_code_details as $k=>$v)
        {
            $data['total_taxable_value'] = $data['total_taxable_value'] + $v['taxable_value'];
            $data['total_central_tax'] = $data['total_central_tax'] + $v['ct_amount'];
            $data['total_state_tax'] = $data['total_state_tax'] + $v['st_amount'];
            $data['total_i_tax'] = $data['total_i_tax'] + $v['i_amount'];
            $data['total_tax_amount'] = $data['total_tax_amount'] + ($v['ct_amount'] + $v['st_amount']);
        }
        $data['total_tax_amount_word'] = $this->getIndianCurrency($data['total_tax_amount']);
        return view('generate-invoice',compact('data','product_details','hsn_code_details','invoice_type'));
        
    }
    function getIndianCurrency(float $number)
    {
        $decimal = round($number - ($no = floor($number)), 2) * 100;
        $hundred = null;
        $digits_length = strlen($no);
        $i = 0;
        $str = array();
        $words = array(0 => '', 1 => 'One', 2 => 'Two',
            3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
            7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
            10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
            13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
            16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
            19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
            40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
            70 => 'Seventy', 80 => 'Eeighty', 90 => 'Ninety');
        $digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
        while( $i < $digits_length ) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += $divider == 10 ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
            } else $str[] = null;
        }
        $Rupees = implode('', array_reverse($str));
        $paise = ($decimal > 0) ? "and" . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise only' : 'only';
        return ($Rupees ? $Rupees . '' : '') . $paise;
    }
}
