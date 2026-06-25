<?php

namespace App\Http\Controllers\HotLeads;

use App\Http\Controllers\Controller;
use App\Models\customer_detail;
use App\Models\lead;
use DateTime;
use DateTimeZone;
use DatePeriod;
use DateInterval;
use PDF;
use Mail;
use File;
use App\Models\hot_leads;
use App\Models\Lead\leads_log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\User\UserController;

class hotLeadsController extends Controller
{
   public function isLoggedIn()
   {
	  $data = session('isLoggedIn');
	  //print_r($data);      
	  return $data;
   }
   // ****View Single Lead****
   public function hot_leads()
   {
	  $isLoggedIn = $this->isLoggedIn();
	  if($isLoggedIn == 'false')
	  {
		  $url = url('/');
	   return redirect()->to($url);
	  }
	  $whereClause = array();
	  if(session('role') == 'admin' OR session('role') == 'superuser')
	  {
			$lead_own = null;
	  }
	  elseif(session('role') == 'user')
	  {
		 $lead_owner = session('user_id');
		 $whereCond = ['hot_leads_lead_owner','=',$lead_owner];
		 array_push($whereClause,$whereCond);
	  }
	  
		$get_all_hot_leads = DB::table('hot_leads')
									->whereNotNull('hot_leads_contact_no')
									->where(array(['hot_leads_status','=','Pending'],['hot_leads_contact_no','!=','']))
									->whereOr(['hot_leads_lead_owner','=','Null'],['hot_leads_lead_owner','=',''])
									->whereOr($whereClause)
									//->whereNull('hot_leads_lead_owner')
									//
									->orderBy('hot_lead_id','DESC')
									->paginate(10);
	  return view('HotLeads/hot_leads',compact('get_all_hot_leads'));
   }
   public function in_process_hot_leads($hot_lead_id, $user_id)
   {
		$isLoggedIn = $this->isLoggedIn();
		if($isLoggedIn == 'false')
		{
			$url = url('/');
			return redirect()->to($url);
		}
		$update_hot_lead = DB::update("UPDATE hot_leads SET hot_leads.hot_leads_lead_owner = $user_id, hot_leads.hot_leads_status = 'In Process' WHERE hot_leads.hot_lead_id = $hot_lead_id");
		return redirect('view_hot_leads_in_process_leads');
   }
   public function create_hot_lead()
   {
	$isLoggedIn = $this->isLoggedIn();
	if($isLoggedIn == 'false')
	{
		$url = url('/');
		return redirect()->to($url);
	}
	if($_SERVER['REQUEST_METHOD']=='POST')
	{
		$customer_details = new customer_detail();
		$leads = new lead();
		$leads_log = new leads_log();
		$hot_lead_id = request()->get('hot_leads_id');
		$equipments = request()->get('equipments');         
		$equipments_requirements = json_encode($equipments);
		$cust_date = date('Y-m-d');
		$current_time = new DateTime("now", new DateTimeZone('Asia/Kolkata') );
		$current_time = $current_time->format('H:i:s');
		$converted_date = request()->get('converted_date')." ".$current_time;
		if($_POST['submit']=='convert')
		{
			$lead_id = request()->get('lead_id');
			$equipments = request()->get('equipments');
			$deposite = json_encode(request()->get('offered_deposite'));
			$offered_rent = json_encode(request()->get('offered_rent'));
			$deposite_total = json_encode(request()->get('offered_deposite_total'));
            $offered_rent_total = json_encode(request()->get('offered_rent_total'));
			$transport = json_encode(request()->get('transport'));
			$del_date = json_encode(request()->get('DelDate'));
			$priority = request()->get('priority_ratio');
			$qty = json_encode(request()->get('qty'));
			$gst_no = request()->get('gst_no');
            $lead_value = request()->get('lead_value');
			$equipments_requirements = json_encode($equipments);
			$equipment = json_decode($equipments_requirements);
            $sale_rental = json_encode(request()->get('sale_rental'));
            $same_address = request()->get('check_del_address');
            if($same_address == True){
               $is_same = "Yes";
            }
            else
            {
               $is_same = "No";
            }
               // $sale_rental = array();
               // for($i=1; $i<=count($equipment); $i++)
               // {
               //    $sale_rental_temp = request()->get('sale_rental'.$i);
               //    array_push($sale_rental,$sale_rental_temp);
               // }
               // $sale_rental = json_encode($sale_rental);
               $contact_person_1_name = null;
               $contact_person_1_no = null;
               $contact_person_2_name = null;
               $contact_person_2_no = null;
               $corp_master = null;
               
               if(request()->get('customer_type') == "Corporate")
               {
                  // $corporate_cust_id = request()->get('corporate_cust_id');
                  $contact_person_1_name = request()->get('contact_person_1_name');
                  $contact_person_1_no = request()->get('contact_person_1_no');
                  $contact_person_2_name = request()->get('contact_person_2_name');
                  $contact_person_2_no = request()->get('contact_person_2_no');
                  if(request()->get('corp_master') == 'Other')
                  {
                     $corp_master = null;
                  }
                  else
                  {
                     $corp_master = request()->get('corp_master');
                  }
               }
            $cutomer_details_updateData = 
            [
               'customer_name' => request()->get('cust_name'),
				'address_line_1' => request()->get('address_line_1'),
				'address_line_2' => request()->get('address_line_2'),
               'cust_gender' => request()->get('customer_gender'),
				'landmark' => request()->get('landmark'),
				'area' => request()->get('area'),
				'city' => request()->get('city'),
				'pincode' => request()->get('pincode'),
				'state' => request()->get('state'),
				'country' => request()->get('country'),
				'location' => request()->get('location'),
				'email_id' => request()->get('email'),
				'primary_contact_no' => request()->get('primary_contact_no'),
				'secondary_contact_no' => request()->get('secondary_contact_no'),
				'prmt_address_line_1' => request()->get('prmt_address_line_1'),
				'prmt_address_line_2' => request()->get('prmt_address_line_2'),
				'prmt_landmark' => request()->get('prmt_landmark'),
				'prmt_area' => request()->get('prmt_area'),
				'prmt_city' => request()->get('prmt_city'),
				'prmt_pincode' => request()->get('prmt_pincode'),
				'prmt_state' => request()->get('state'),
				'prmt_country' =>request()->get('country'),
				'prmt_email_id' => request()->get('prmt_email'),
               'corp_master_id' => $corp_master,
               'prmt_secondary_contact_no' => request()->get('prmt_secondary_contact_no'),
               'addr_is_same' => $is_same,
               'gst_no' => $gst_no,
               'refered_by' => request()->get('refered_by'),
               'customer_type' => request()->get('customer_type'),
               'contact_person_1_name' => $contact_person_1_name,
               'contact_person_1_no' => $contact_person_1_no,
               'contact_person_2_name' => $contact_person_2_name,
               'contact_person_2_no' => $contact_person_2_no,
				'updated_by' => session('username')
            ];
            $customer_id = $customer_details->insertGetId($cutomer_details_updateData);
            if(isset($equipments))
            {
               $equipment = json_decode($equipments_requirements);
               $sale_rental = $_POST['sale_rental'];
               $sale_rental = json_encode($sale_rental);
				//    $sale_rental = array();
				//    for($i=1; $i<=count($equipment); $i++)
				//    {
				//       $sale_rental_temp = request()->get('sale_rental'.$i);
				//       array_push($sale_rental,$sale_rental_temp);
				//    }
				//    $sale_rental = json_encode($sale_rental);
               //for converted_at date 
               $converted_at = $_POST['converted_at'];
               if($converted_at!=null)
               {
                  $converted_at = $_POST['converted_at'];
                  $creation_date1 = $_POST['converted_at'];
               }
               else
               {
                  $converted_at = date('Y-m-d');
                  $creation_date1 = date('Y-m-d');
               }
               $lead_old_data = DB::table('leads')
                  ->select(
                     'converted_at',
                     'creation_date',
                     'patient_name',
                     'patient_age',
                     'patient_gender',
                     'doctor_name',
                     'hospital_name',
                     'therapeutic_requirement',
                     'equipment_requirement',
                     'equipment_qty',
                     'months',
                     'del_date',
                     'sale_rental',
                     'deposite',
                     'offered_rent',
                     'deposite_total',
                     'offered_rent_total',
                     'transport',
                     'lead_source',
                     'lead_value',
                     'lead_status',
                     'priority',
                     'lead_owner',
                     'comment',
                     'payment_mode',
                     'updated_by')
                  ->where('id',$lead_id)
                  ->get();
               $leads_updateData = [
					'customer_id' => $customer_id,
					'creation_date' => $cust_date,
					'hot_leads_id' => request()->get('hot_leads_id'),
					'patient_name' => request()->get('patient_name'),
					'patient_age' => request()->get('patient_age'),
                  	'patient_gender' => request()->get('patient_gender'),
					'doctor_name' => request()->get('doctor_name'),
					'hospital_name' => request()->get('hospital_name'),
					'therapeutic_requirement' => request()->get('therapeutic_requirement'),
					'equipment_requirement' => $equipments_requirements,
					'equipment_qty' => $qty,
                  	'months'=>json_encode(request()->get('month')),
					'del_date' => $del_date,
					'sale_rental' => $sale_rental,
					'deposite' => $deposite,
					'deposite_total' => $deposite_total,
					'offered_rent' => $offered_rent,
					'offered_rent_total' => $offered_rent_total,
					'transport' => $transport,
					'lead_source' => request()->get('lead_source'),
					'lead_value' => request()->get('lead_value'),
					'lead_status' => 'Converted',
					'priority' => $priority,
					'lead_owner' => request()->get('lead_owner'),
					'comment' => request()->get('comment'),
					'payment_mode' => request()->get('payment_mode'),
					'created_by' => session('username')
               	];
				$lead_id = $leads->insertGetId($leads_updateData);
				// $fileName = $lead_id;
				$fileName = $lead_id.'.jpeg';
				$filePath = null;
				if(request()->file('doc_id_file') != null)
				{
					$filePath = request()->file('doc_id_file')->storeAs('public/patient_document',$fileName);
				}
				else if(request()->get('doc_id_file_hidden') != null){
					$filePath = request()->get('doc_id_file_hidden');
				}
				$update_doc = [
					'patient_document_type'=>request()->get('patient_document'),
					'patient_document_no'=>request()->get('patient_doc_id_no'),
					'patient_document_image'=>$filePath
				];
				$leads->where('id',$lead_id)->update($update_doc);
				DB::table('hot_leads')->where('hot_lead_id',request()->get('hot_leads_id'))->update(['hot_leads_status'=>'Lead Generated']);
            }
            // $activity_log = array();
            // foreach ($leads_updateData as $key => $value)
            // {
            //    if($value != $lead_old_data[0]->$key)
            //    {
            //       $insertData = [
            //          'order_type'=>'LD',
            //          'key_id'=>$lead_id,
            //          'operation'=>'Update Lead Conv.',
            //          'fields'=>$key,
            //          'old_value'=>$lead_old_data[0]->$key,
            //          'new_value'=>$value,
            //          'updated_by'=>session('username')
            //       ];
            //       ActivityLog::insert($insertData);
            //    }
            // }

            // leads_log::updateOrCreate(
            //    [
            //       'log_lead_id' => $lead_id,
            //       'log_lead_status' => 'Converted',
            //       'updated_by' => session('username')
            //    ],
            //    [
            //       'log_order_lead_date' => $converted_date,
            //       'log_date' => date('Y-m-d'),
            //       'log_time' => date('H:i:s'),
            //    ]);
            // $leads_log_data = 
            // [
            //    'log_lead_id' => $lead_id,
            //    'log_lead_status' => 'Converted',
            //    'log_date' => date('Y-m-d'),
            //    'log_time' => date('H:i:s'),
            //    'updated_by' => session('username')
            // ];
            // $leads_log->insert($leads_log_data);
            // $equipment = json_decode($equipments_requirements);
            // $equip_qty = json_decode($qty);
            // $equip_deposit = json_decode($deposit);
            // $eqip_rent = json_decode($offered_rent);
            // $transport = json_decode($transport);
            // $sale_rental = json_decode($sale_rental);
            // $cdata = array();
            // $temp_transport = 0;
            // $temp_total = 0;
            // $i = 0;
            // foreach ($equipment as $equipment_details) 
            // {
            //    $product_details = DB::select("SELECT * FROM products WHERE id = $equipement_details");
            //    $data['product_details'] = json_decode(json_encode($product_details), true);
            //    $cdata['equipment_name'.$i] = $data['product_details']['product_name'];
            //    $cdata['equipment_qty'.$i] = $equip_qty[$i];
            //    if($sale_rental[$i] == 'Rental') 
            //    {
            //       $cdata['equipment_rent'.$i] = $equip_rent[$i];
            //       $cdata['equipment_deposit'.$i] = $equip_deposit[$i];
            //       $temp_transport = $temp_transport + $transport[$i];
            //       $temp_total = $temp_total + $equip_rent[$i];
            //       $temp_total = $temp_total + $equip_deposit[$i];
            //    }
            //    if($sale_rental == 'Sale')
            //    {
            //       $cdata['equipment_sale_rate'.$i] = $equip_rent[$i];
            //       $temp_total = $temp_total + $equip_rent[$i];
            //       $temp_transport = $temp_transport + $transport[$i];
            //    } 
            //    $i = $i + 1;
            // }
            // $cdata['transport'] = $temp_transport;
            // $cdata['total'] = $temp_total;
            // generate_challan($cdata);

            //Generate Delivery Challan Information
               $customer_name = $_POST['cust_name'];
               $address = $_POST['address_line_1'].", ".$_POST['address_line_2'].", ".$_POST['landmark'].", ".$_POST['area'].", ".$_POST['city1'].", ".$_POST['pincode'];

               $get_cust_contact = DB::select("SELECT primary_contact_no from customer_details WHERE cust_id ='$customer_id' ");
               $data['get_cust_contact'] = json_decode(json_encode($get_cust_contact),true);
               $contact = $data['get_cust_contact'][0]['primary_contact_no'];
               $customer_products = $_POST['equipments'];
               $total_amt=$lead_value;                
               $total_transport=0;
               $challan_data = array();
               // print_r($_POST['offered_rent']);
               // print_r($_POST['offered_deposite']);
               // print_r($_POST['transport']);
               for ($i=0; $i <count($customer_products) ; $i++) { 
                  $product_id = $customer_products[$i];
                  $offered_rent = (int)$_POST['offered_rent_total'][$i];   
                  $offered_deposit =(int)$_POST['offered_deposite'][$i];
                  $offered_transport = (int)$_POST['transport'][$i];
                  $sale_rental = $_POST['sale_rental'][$i];
                  $product_quantity = $_POST['qty'][$i];

                  //$total_amt+=$offered_rent+$offered_deposit+$offered_transport;
                  $total_transport+=$offered_transport;

                  $get_product_name = DB::select("SELECT product_name FROM products Where id ='$product_id' ");
                  $data['get_product_name'] = json_decode(json_encode($get_product_name),true);
                  $product_name = $data['get_product_name'][0]['product_name'];

                  $challan_data['product_name'][$i] = $product_name;  
                  $challan_data['offered_rent'][$i] = $offered_rent;
                  $challan_data['offered_deposit'][$i] = $offered_deposit;
                  $challan_data['offered_transport'][$i] = $offered_transport;
                  $challan_data['sale_rental'][$i] = $sale_rental;
                  $challan_data['product_quantity'][$i] = $product_quantity;

               }
               
               $data_pdf = array(
                  'lead_id'=>$lead_id,
                  'customer_name'=>$customer_name,
                  'address' =>$address,
                  'converted_date'=>date('d-m-Y',strtotime($converted_date)),
                  'contact_no' =>$contact,
                  'total_transport'=>$total_transport,
                  'total_amt'=>$total_amt,);
                  // 'mail_data'=>"'".$mail_data."'");
                  $data_pdf['challan_data'] = $challan_data;

                  //print_r($data_message);
               $url = url('/');
               $pdf = PDF::loadView('del_challan_temp', $data_pdf);

               file_put_contents("/var/www/html/devweb/eflow/assets/uploads/challan/".$_POST['cust_name'].$lead_id.".pdf", $pdf->output()); 
               $file_path = "/assets/uploads/challan/".$_POST['cust_name'].$lead_id.".pdf";

			   //Storage::disk('local')->put(''.'/'.$_POST['cust_name'].$lead_id.".pdf", $pdf, 'public');

            //    if(!Storage::disk('public_uploads')->put($path, $file_content)) {
            // 	return false;
            // }
               
               $update_challanpath = DB::update("UPDATE leads SET delivery_challan='$file_path' WHERE id ='$lead_id' ");
            if(request()->get('hidden_def_patient_id') != "No" && request()->get('hidden_def_patient_id') != null){
               DB::table('lookup_table')->update(['patient_id'=>request()->get('hidden_def_patient_id')+1]);
            }

            //send lead msg to internal staff

            $customerName = request()->get('cust_name');
            $customerAddress = $address;
            $customerContactNO = request()->get('primary_contact_no');
            $patientName = request()->get('patient_name');
            $patientAge = request()->get('patient_age');
            $getProducts = array();
            foreach (request()->get('equipments') as $key=>$value){
                $temp_prod = DB::table('products')->where('id',$value)->get()->toArray();
               array_push($getProducts,$temp_prod[0]->product_name);
            }
            $productType = request()->get('sale_rental');
            $productSaleRent = request()->get('offered_rent_total');
            $productDeposit = request()->get('offered_deposite_total');
            $productQty = request()->get('qty');
            $productTransport = request()->get('transport');
            $paymentMode = request()->get('payment_mode');
            $totalAmt = array_sum($productSaleRent)+array_sum($productDeposit)+array_sum($productTransport);
            
            $msg ="Product Name : ";
            foreach ($getProducts as $key => $value) {
               $prdMsg ="*".$value."*, ".($productType[$key]=='Rental'?'Rent : ':'Sale : ')
                              .$productSaleRent[$key]
                              ." Deposit :"
                              .($productType[$key]=='Rental'?$productDeposit[$key]:0)
                              ." Qty :"
                              .$productQty[$key]
                              .", Transport : "
                              .$productTransport[$key]." | ";
               $msg .=" ".$prdMsg;
            }
            //$prodManagerNo = config('app.prod_manager_no');
            $business_head_id = config('app.business_head_id');
			   $business_head_number = DB::table('user')->select('contact_no')->where('id',$business_head_id)->get()->first();
			   $business_head_number = $business_head_number->contact_no;
            $leadOwnerWpno = DB::table('user')->where('id',request()->get('lead_owner'))->first();
            $wpNumbers = array($business_head_number,$leadOwnerWpno->contact_no);
            // $wpNumbers = array($leadOwnerWpno->contact_no);
            // array_push($wpNumbers,'9370738471');
            
            $delivery_date = date('d-M-y',strtotime($converted_date));
            //dd($wpNumbers,$getProducts,$msg,$customerAddress);
            if($patientName == null)
            {
               $patientName = "NA";
            }
            if($patientAge == null)
            {
               $patientAge = "NA";
            }
            $headerText = "";
            if(request()->get('flag') == "Edit")
            {
               $headerText = "(Edited): ".session('username').', Lead Owner: '.$leadOwnerWpno->username.', Lead Id: '.$lead_id;
            }
            else
            {
               $headerText = 'Lead Owner: '.$leadOwnerWpno->username.', Lead Id: '.$lead_id;
            }
            foreach ($wpNumbers as $key => $value) {
               $url = "https://lj7724mli5.execute-api.ap-south-1.amazonaws.com/prod/sendwhatsappmsg";
               $curl = curl_init();
               curl_setopt($curl, CURLOPT_URL, $url);
               curl_setopt($curl, CURLOPT_POST, true);
               curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
               
               $headers = array(
                  "Accept: application/json",
                  "Content-Type: application/json",
               );
               curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
               $data =[
                  "portno"=>"11140",
                  "namespace"=>"b9a23cb4_89ed_4fe2_b849_20775908ff5e",
                  "countrycode"=> "91",
                  "mobileno"=> $value,
                  "templatename" => "new_lead",
                  "templateparams" => [
                        ["type"=> "text","text"=> $headerText],
                        ["type"=> "text","text"=> $customerName],
                        ["type"=> "text","text"=> "$customerContactNO"],
                        ["type"=> "text","text"=> $patientName],
                        ["type"=> "text","text"=> "$patientAge"],
                        ["type"=> "text","text"=> $msg],
                        ["type"=> "text","text"=> "$totalAmt"],
                        ["type"=> "text","text"=> $paymentMode],
                        ["type"=> "text","text"=>  $customerAddress],
                        ["type"=> "text","text"=> "$delivery_date"],
                      // ["type"=> "text","text"=> "<<Delivery>>],
                  ],
              ];
            //   dd(json_encode($data));
              curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
              
               $resp = curl_exec($curl);
               curl_close($curl);
               // dd($resp);
            }

            return redirect('/view_all_leads')->with('message', 'Lead Converted Successfully');
        }
		 if($_POST['submit']=='check')
		 {
			$customer_id = request()->get('customer_id');
			$cust_date = date('Y-m-d');
			$leads_insertData = [
			   'customer_id' => $customer_id,
			   'hot_leads_id' => request()->get('hot_leads_id'),
			   'creation_date' => request()->get('creation_date'), 
			   'patient_name' => request()->get('patient_name'),
			   'patient_age' => request()->get('patient_age'),
			   'doctor_name' => request()->get('doctor_name'),
			   'hospital_name' => request()->get('hospital_name'),
			   'therapeutic_requirement' => request()->get('therapeutic_requirement'),
			   'equipment_requirement' => $equipments_requirements,
			   'lead_source' => request()->get('lead_source'),
			   'lead_status' => 'Converted',
			   'lead_owner' => request()->get('lead_owner'),
			   'created_by' => session('username')
			];
			$leads->insert($leads_insertData);
		 }
		 $hot_lead_id = request()->get('hot_leads_id');
		 $update = DB::update("update hot_leads set hot_leads_status = 'Lead Generated' where hot_lead_id = ?", [$hot_lead_id]);
		 $mobiles = request()->get('primary_contact_no');
		 $equipement_details = array();
		 for ($j=0; $j <count($equipments); $j++) 
		 { 
			$product_details = DB::select("SELECT product_name FROM products WHERE id = $equipments[$j]");
			$product_details = json_decode(json_encode($product_details), true);
			array_push($equipement_details,$product_details[0]['product_name']);
		 }
		 $equipment_count = count($equipments);
		 if($equipment_count>1)
		 {
			$count_equip = $equipment_count -1;
			$equipment_details_text = $equipement_details[0]."...";
		 }
		 else
		 {
			$equipment_details_text = $equipement_details[0];
		 }
		 $product = json_decode($equipments_requirements);
		 //print_r($product);
		 $equipement_details = array();
		 for ($j=0; $j <count($product); $j++)
		 { 
			$product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
			$product_details = json_decode(json_encode($product_details), true);
			array_push($equipement_details,$product_details[0]['product_name']);
		 }
		 //$equipements = json_encode($equipement_details);
		 $lead_owner = request()->get('lead_owner');
		 $equipments = implode(", ",$equipement_details);
		 $lead_owner_details = DB::select("SELECT * FROM user WHERE id=$lead_owner");
		 $data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
		 return redirect('/view_in_process_leads')->with('message', 'Lead Generated Successfully');
   	}
   }
   public function view_in_process_leads(Request $request)
   {
		$isLoggedIn = $this->isLoggedIn();
		if($isLoggedIn == 'false')
		{
			$url = url('/');
		return redirect()->to($url);
		}
		$today = date('Y-m-d');
		$past_three_days = date('Y-m-d',strtotime("-2 days"));
		$whereCond = [];
		$lead_owner_name['username']= null;
		if(session('role') == 'user')
		{
			$user_id = session('user_id');
			$lead_owner_name = DB::table('user')->where('id',$user_id)->get('username')->toArray();
			$lead_owner_name['username']= $lead_owner_name[0]->username;
			$whereCond1 = ['hot_leads_lead_owner','=',$user_id];
			array_push($whereCond,$whereCond1);
		}
		//dd($lead_owner_name);
		$date_filter = $request->get('date_filter');
		if(isset($date_filter))
		{
			if($date_filter=='today')
			{
				$whereCond2 = ['hot_leads_created_at','=',date('Y-m-d')];
				array_push($whereCond,$whereCond2);
			}
			elseif($date_filter=='yesterday')
			{
				$today = date('Y-m-d');
				$whereCond2 = ['hot_leads_created_at','=',date('Y-m-d',strtotime('-1 days',strtotime($today)))];
				array_push($whereCond,$whereCond2);
			}
			elseif($date_filter=='3_days')
			{
				$today = date('Y-m-d');
				$whereCond2 = ['hot_leads_created_at','>',date('Y-m-d',strtotime('-3 days',strtotime($today)))];
				array_push($whereCond,$whereCond2);
			}
			elseif($date_filter=='week')
			{
				$today = date('Y-m-d');
				$whereCond2 = ['hot_leads_created_at','>',date('Y-m-d',strtotime('-7 days',strtotime($today)))];
				array_push($whereCond,$whereCond2);
			}
		}
		$get_all_hot_leads = DB::table('hot_leads')
									->select('hot_leads.*','user.username')
									->join('user','hot_leads.hot_leads_lead_owner','=','user.id')
									->where('hot_leads.hot_leads_status','=','In Process')
									->where($whereCond)
									->orderBy('hot_leads.hot_lead_id','DESC')
									->paginate(10);
		//echo "<script>localStorage['filtered']='past_3_days';</script>";
		
		return view('HotLeads/view_all_inprocess_lead',compact('get_all_hot_leads','lead_owner_name'));
   }
   public function view_closed_leads()
   {
	  $isLoggedIn = $this->isLoggedIn();
	  if($isLoggedIn == 'false')
	  {
		  $url = url('/');
	   return redirect()->to($url);
	  }
	
		$today = date('Y-m-d');
		$past_three_days = date('Y-m-d',strtotime("-2 days"));
		$lead_owner_name['username']= null;
		$whereClause = array();
		if(session('role') == 'user')
		{
			$user_id = session('user_id');
			// $lead_owner_name = DB::table('user')->where('id',$user_id)->get('username')->toArray();
			// dd($lead_owner_name);
			$lead_owner_name['username']= session('username');
			$whereCond1 = ['hot_leads.hot_leads_status','=','Closed'];
			$whereCond2 = ['hot_leads.hot_leads_lead_owner','=',$user_id];
			array_push($whereClause,$whereCond1);
			array_push($whereClause,$whereCond2);
			//$hotleads = DB::select("SELECT * FROM hot_leads WHERE hot_leads_status = 'In Process' AND hot_leads_lead_owner = $user_id AND hot_leads_created_at >= $past_three_days ORDER BY hot_lead_id DESC");
		}
		else{
			$whereCond1 = ['hot_leads.hot_leads_status','=','Closed'];
			array_push($whereClause,$whereCond1);
		}
		
		$get_all_hot_leads = DB::table('hot_leads')
									//->where('hot_leads_status','=','Closed')
									->select('hot_leads.*','user.username')
									->join('user','hot_leads.hot_leads_lead_owner','=','user.id')
									->where($whereClause)
									->orderBy('hot_leads.hot_lead_id','DESC')
									->paginate(10);
		//echo "<script>localStorage['filtered']='past_3_days';</script>";
		
	  return view('HotLeads/view_all_closed_leads',compact('get_all_hot_leads','lead_owner_name'));
   }
   public function qualify_lead($hot_lead_id)
   {
		$patient_id = DB::table('lookup_table')->get('patient_id');
		$data['patient_id'] = $patient_id[0]->patient_id;
		$hot_lead_data = DB::select("SELECT * FROM hot_leads WHERE hot_lead_id = $hot_lead_id");
		$data['hot_leads_data'] = json_decode(json_encode($hot_lead_data),true);
		$products = DB::select("SELECT * FROM products WHERE flag = 'Active'");
		$data['products_details'] = \json_decode(\json_encode($products), true);
		//cities
		$cities = DB::select("SELECT * FROM cities");
		$data['cities'] = \json_decode(\json_encode($cities), true);
		//states
		$states = DB::select("SELECT * FROM states");
		$data['states'] = \json_decode(\json_encode($states), true);
		//countries
		$countries = DB::select("SELECT * FROM countries");
		$data['countries'] = \json_decode(\json_encode($countries), true);
		//lead_owner details
		$users = DB::select("SELECT * FROM user WHERE role!='vendor'");
		$data['users'] = \json_decode(\json_encode($users), true);
		$corp_masters = DB::select("SELECT * FROM corp_master WHERE flag = 'Active'");
		$data['corp_masters'] = json_decode(json_encode($corp_masters), true);
		$data['lead_source'] = config('app.lead_source');
		$data['patient_documents'] = config('app.patient_documents');
		return view('HotLeads/convert_lead',$data);
   }
   // ****View Single Lead****
   public function view_lead($customer_id,$id)
   {
	  $isLoggedIn = $this->isLoggedIn();
	  if($isLoggedIn == 'false')
	  {
		  $url = url('/');
	   return redirect()->to($url);
	  }
	  $lead_details = DB::select("SELECT * FROM leads,customer_details WHERE leads.id=$id AND customer_details.cust_id=$customer_id");
	  $data['lead_details'] = json_decode(json_encode($lead_details), true);
	  $user_id = $data['lead_details'][0]['lead_owner'];
	  $lead_owner_details = DB::select("SELECT * FROM user WHERE id=$user_id");
	  $data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
	  $data['lead_details'][0]['username'] =  $data['lead_owner_details'][0]['username'];
	  $data['lead_details'][0]['user_id'] =  $data['lead_owner_details'][0]['id'];
	  $product = json_decode($data['lead_details'][0]['equipment_requirement']);
		 //print_r($product);
		 $equipement_details = array();
		 $user_id = $data['lead_details'][0]['lead_owner'];
		 $lead_owner_details = DB::select("SELECT * FROM user WHERE id=$user_id");
		 $data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
		 $data['lead_details'][0]['username'] =  $data['lead_owner_details'][0]['username'];
		 $data['lead_details'][0]['user_id'] =  $data['lead_owner_details'][0]['id'];
		 for ($j=0; $j <count($product); $j++) 
		 { 
			$product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
			$product_details = json_decode(json_encode($product_details), true);
			array_push($equipement_details,$product_details[0]['product_name']);
		 }
		 $equipements = json_encode($equipement_details);
		 $data['lead_details'][0]['equipment_requirement'] = $equipements;
	  return view('HotLeads/view_lead',$data);
   }
   // Edit lead get method to display lead info
   public function edit_lead($customer_id,$id)
   {
	  $isLoggedIn = $this->isLoggedIn();
	  if($isLoggedIn == 'false')
	  {
		  $url = url('/');
	   return redirect()->to($url);
	  }
	  $products = DB::select("SELECT * FROM products flag = 'Active'");
	  $data['products'] = \json_decode(\json_encode($products), true);
	  $cities = DB::select("SELECT * FROM cities");
	  $data['cities'] = \json_decode(\json_encode($cities), true);
	  $states = DB::select("SELECT * FROM states");
	  $data['states'] = \json_decode(\json_encode($states), true);
	  $countries = DB::select("SELECT * FROM countries");
	  $data['countries'] = \json_decode(\json_encode($countries), true);
	  $users = DB::select("SELECT * FROM user WHERE role!='vendor'");
	  $data['users'] = \json_decode(\json_encode($users), true);
	  $lead_details = DB::select("SELECT * FROM leads,customer_details WHERE leads.id=$id AND customer_details.cust_id=$customer_id");
	  $data['lead_details'] = json_decode(json_encode($lead_details), true);
	  $user_id = $data['lead_details'][0]['lead_owner'];
	  $lead_owner_details = DB::select("SELECT * FROM user WHERE id=$user_id");
	  $data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
	  $data['lead_details'][0]['username'] =  $data['lead_owner_details'][0]['username'];
	  $data['lead_details'][0]['user_id'] =  $data['lead_owner_details'][0]['id'];
	  $product = json_decode($data['lead_details'][0]['equipment_requirement']);
		 $equipment_id = $data['lead_details'][0]['equipment_requirement'];
		 //print_r($product);
		 $equipement_details = array();
		 for ($j=0; $j <count($product); $j++) 
		 { 
			$product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
			$product_details = json_decode(json_encode($product_details), true);
			array_push($equipement_details,$product_details[0]['product_name']);
		 }
		 $equipements = json_encode($equipement_details);
		 $data['lead_details'][0]['equipment_requirement'] = $equipements;
		 $data['lead_details'][0]['equipment_id'] = $equipment_id;
	  return view('HotLeads/edit_lead',$data);
   }
   // to update lead details
   public function update_lead()
   {
	  $isLoggedIn = $this->isLoggedIn();
	  if($isLoggedIn == 'false')
	  {
		  $url = url('/');
	   return redirect()->to($url);
	  }
	  if($_SERVER['REQUEST_METHOD']=='POST')
	  {
		 //print_r($_POST);
		 $customer_details = new customer_detail();
		 $leads = new lead();
		 $equipments = request()->get('eqipments');
		 $equipments_requirements = json_encode($equipments);
		 //print_r($equipments_requirements);
		 if($_POST['submit']=='convert')
		 {
			$cust_date = date('Y-m-d');
			$cutomer_details_insertData = [
			   'customer_name' => request()->get('cust_name'),
			   'cust_date' => $cust_date,
			   'address_line_1' => request()->get('address_line_1'),
			   'address_line_2' => request()->get('address_line_2'),
			   'landmark' => request()->get('landmark'),
			   'area' => request()->get('area'),
			   'city' => request()->get('city'),
			   'pincode' => request()->get('pincode'),
			   'state' => request()->get('state'),
			   'country' => request()->get('country'),
			   'location' => request()->get('location'),
			   'email_id' => request()->get('email'),
			   'gst_no' =>request()->get('gst_no'),
			   'corporation_name' =>request()->get('corporation_name'),
			   'primary_contact_no' => request()->get('primary_contact_no'),
			   'secondary_contact_no' => request()->get('secondary_contact_no'),
			   'refered_by' => request()->get('refered_by'),
			   'created_by' => session('username')
			];
			$customer_id = $customer_details->insertGetId($cutomer_details_insertData);
			$leads_insertData = [
			   'customer_id' => $customer_id,
			   'creation_date' => request()->get('creation_date'), 
			   'patient_name' => request()->get('patient_name'),
			   'patient_age' => request()->get('patient_age'),
			   'doctor_name' => request()->get('doctor_name'),
			   'hospital_name' => request()->get('hospital_name'),
			   'therapeutic_requirement' => request()->get('therapeutic_requirement'),
			   'equipment_requirement' => $equipments_requirements,
			   'lead_source' => request()->get('lead_source'),
			   'lead_status' => 'Converted',
			   'lead_owner' => request()->get('lead_owner'),
			   'created_by' => session('username')
			];
			$leads->insert($leads_insertData);
		 }
		 if($_POST['submit']=='check')
		 {
			$customer_id = request()->get('customer_id');
			$cust_date = date('Y-m-d');
			$leads_insertData = [
			   'customer_id' => $customer_id,
			   'creation_date' => request()->get('creation_date'), 
			   'patient_name' => request()->get('patient_name'),
			   'patient_age' => request()->get('patient_age'),
			   'doctor_name' => request()->get('doctor_name'),
			   'hospital_name' => request()->get('hospital_name'),
			   'therapeutic_requirement' => request()->get('therapeutic_requirement'),
			   'equipment_requirement' => $equipments_requirements,
			   'lead_source' => request()->get('lead_source'),
			   'lead_status' => 'Work In Process',
			   'lead_owner' => request()->get('lead_owner'),
			   'created_by' => session('username')
			];
			$leads->insert($leads_insertData);
		 }
		 $hot_lead_id = request()->get('hot_lead_id');
		 $update = DB::update("update hot_leads set hot_leads_status = 'Lead Generated' where hot_lead_id = ?", [$hot_lead_id]);
		 $mobiles = request()->get('primary_contact_no');
		 $equipement_details = array();
		 for ($j=0; $j <count($equipments); $j++) 
		 { 
			$product_details = DB::select("SELECT product_name FROM products WHERE id = $equipments[$j]");
			$product_details = json_decode(json_encode($product_details), true);
			array_push($equipement_details,$product_details[0]['product_name']);
		 }
		 //$equipements_sms = json_encode($equipement_details);

		 $equipment_count = count($equipments);
		 if($equipment_count>1)
		 {
			$count_equip = $equipment_count -1;
			$equipment_details_text = $equipement_details[0]."...";
		 }
		 else
		 {
			$equipment_details_text = $equipement_details[0];
		 }
		 $username = session('username');
		 $contact = session('contact');
		 ////////////////////--------------Send Sms---------------//////////////////////////////
			$curl = curl_init();
			curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.msg91.com/api/v5/flow/",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			//CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"60cc49120eeed16fcd62d103\",\n  \"sender\": \"QUALCR\",\n  \"mobiles\": \"919920361040\",\n  \"name\": \"testing\",\n  \"orderno\": \"8512457845\",\n  \"equpname\": \"Standard Walker\",\n  \"date\": \"18-06-2021\",\n  \"amount\": \"550\"}",
			CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"60d05c8924235536765b61d4\",\n  \"sender\": \"QULCAR\",\n  \"mobiles\": \"91$mobiles\",\n  \"equipment\": \"$equipment_details_text\",\n  \"username\": \"$username\",\n  \"contact\": \"$contact\"}",
			CURLOPT_HTTPHEADER => array(
			   "authkey: 267641AmFwcnWjDS5e6b4757P1",
			   "content-type: application/JSON"
			),
			));

			// $response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
			//echo "cURL Error #:" . $err;
			} else {
			//echo $response;
			}
		 //----Send Message to Lead Owner about new lead-----------------------//
		 $product = json_decode($equipments_requirements);
		 //print_r($product);
		 $equipement_details = array();
		 for ($j=0; $j <count($product); $j++) 
		 { 
			$product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
			$product_details = json_decode(json_encode($product_details), true);
			array_push($equipement_details,$product_details[0]['product_name']);
		 }
		 //$equipements = json_encode($equipement_details);
		 $lead_owner = request()->get('lead_owner');
		 $equipments = implode(", ",$equipement_details);
		 $lead_owner_details = DB::select("SELECT * FROM user WHERE id=$lead_owner");
		 $data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
		 //print_r($equipments);
		 $mobile = request()->get('primary_contact_no');
		 $message = "Thank you for your inquiry at Quali55Care.com for $equipments . We assure you quick delivery and provide you with everything that you might need with the urgency and care, please contact ".$data['lead_owner_details'][0]['username']." at ".$data['lead_owner_details'][0]['contact_no']." if need any further advise/assitance";
		 //$this->sendsms($mobile,$message);
		 //echo $message;
		 //return $this->viewAllLeads();
		 return redirect('/viewAllLeads')->with('message', 'Lead Generated Successfully');
	  }
   }
   //****Convert Lead View***//
   public function convert_lead($customer_id,$id)
   {
	  $isLoggedIn = $this->isLoggedIn();
	  if($isLoggedIn == 'false')
	  {
		  $url = url('/');
	   return redirect()->to($url);
	  }
	  $products = DB::select("SELECT * FROM products flag = 'Active'");
	  $data['products'] = \json_decode(\json_encode($products), true);
	  $cities = DB::select("SELECT * FROM cities");
	  $data['cities'] = \json_decode(\json_encode($cities), true);
	  $states = DB::select("SELECT * FROM states");
	  $data['states'] = \json_decode(\json_encode($states), true);
	  $countries = DB::select("SELECT * FROM countries");
	  $data['countries'] = \json_decode(\json_encode($countries), true);
	  $users = DB::select("SELECT * FROM user WHERE role!='vendor'");
	  $data['users'] = \json_decode(\json_encode($users), true);
	  $lead_details = DB::select("SELECT * FROM leads,customer_details WHERE leads.id=$id AND customer_details.cust_id=$customer_id");
	  //$lead_details = DB::select("SELECT * FROM leads,customer_details,user WHERE leads.id=$id AND customer_details.cust_id=$customer_id AND user.id = leads.lead_owner");
	  $data['lead_details'] = json_decode(json_encode($lead_details), true);
	  $user_id = $data['lead_details'][0]['lead_owner'];
	  $lead_owner_details = DB::select("SELECT * FROM user WHERE id=$user_id");
	  $data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
	  $data['lead_details'][0]['username'] =  $data['lead_owner_details'][0]['username'];
	  $data['lead_details'][0]['user_id'] =  $data['lead_owner_details'][0]['id'];
	  $equipment_id = $data['lead_details'][0]['equipment_requirement'];
	  $product = json_decode($data['lead_details'][0]['equipment_requirement']);
		 $equipment_id = $data['lead_details'][0]['equipment_requirement'];
		 //print_r($product);
		 $equipement_details = array();
		 for ($j=0; $j <count($product); $j++) 
		 { 
			$product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
			$product_details = json_decode(json_encode($product_details), true);
			array_push($equipement_details,$product_details[0]['product_name']);
		 }
		 $equipements = json_encode($equipement_details);
		 $data['lead_details'][0]['equipment_requirement'] = $equipements;
		 $data['lead_details'][0]['equipment_id'] = $equipment_id;
	  return view('HotLeads/convert_lead',$data);
   }
   public function delete_lead($id)
   {
	  $isLoggedIn = $this->isLoggedIn();
	  if($isLoggedIn == 'false')
	  {
		  $url = url('/');
	   return redirect()->to($url);
	  }
	  $leads = new lead();
	  $leads->where('id',$id)->delete();
	  return redirect('/viewAllLeads')->with('message_delete', 'Lead Deleted Successfully');
	  //return $this->viewAllLeads();
   }
   public function close_lead($customer_id,$id)
   {
	  $isLoggedIn = $this->isLoggedIn();
	  if($isLoggedIn == 'false')
	  {
		  $url = url('/');
	   return redirect()->to($url);
	  }
	  $hot_leads = new hot_leads();
	  //$leads->where('id',$id)->delete();
	  $lead_status = [
		 'hot_leads_status' => 'Closed'
	  ];
	  $hot_leads->where('hot_lead_id',$id)->update($lead_status);
	  return redirect('/view_in_process_leads')->with('message', 'Lead Closed Successfully');
	  //return $this->viewAllLeads();
   }
   public function close_lead_with_reason(Request $request,$hot_lead_id)
   {
	  $isLoggedIn = $this->isLoggedIn();
	  if($isLoggedIn == 'false')
	  {
		  $url = url('/');
	   	return redirect()->to($url);
	  }
	  $reason = $request->get('reason');
	  $desc = $request->get('desc');
	  $hot_leads = DB::update("UPDATE hot_leads SET hot_leads_status = 'Closed',hot_leads_reason = '$reason', hot_leads_desc = '$desc' WHERE hot_lead_id = '$hot_lead_id'");
	  return redirect()->back()->with('message_delete', 'Lead Closed Successfully');
   }
   public function add_hot_lead_comment(Request $request,$hot_lead_id)
   {
	  $isLoggedIn = $this->isLoggedIn();
	  if($isLoggedIn == 'false')
	  {
		  $url = url('/');
	   return redirect()->to($url);
	  }
	  $hot_lead = new hot_leads();
	  //$leads->where('id',$id)->delete();
	  $desc = $request->get('desc');
	  $timestamp = date("d M, h:i A");
	  $comment = "[".$timestamp."]".$desc."\n";
	  $lead_status = [
		 'hot_leads_comment' => $comment
	  ];
	  $cmt_check = DB::select("SELECT hot_leads_comment FROM hot_leads WHERE hot_lead_id = '$hot_lead_id' ");
	  $data['cmt_check'] = json_decode(json_encode($cmt_check), true);
   
	  if(isset($data['cmt_check'][0]['hot_leads_comment']))
	  {
	  $cmt_update = DB::update("UPDATE hot_leads SET hot_leads_comment = CONCAT('$comment',hot_leads_comment) WHERE hot_lead_id = '$hot_lead_id' ");
	 }
	 else
	 {
	  $hot_lead->where('hot_lead_id',$hot_lead_id)->update($lead_status);
	 }
	  return redirect()->back()->with('message', 'comment add Successfully');
   }
   public function filterHotLeads($filter_by)
   {
	  if($filter_by =='today')
	  {
		 // $date = date('Y-m-d');
		 // $whereClause = "hot_leads.hot_leads_created_at = '$date'";
		 // echo "<script>localStorage['filtered']='today';</script>";
		 $date = date('Y-m-d',strtotime("+2 days"));
		 $past_three_days = date('Y-m-d',strtotime("-1 days"));
		 $whereClause = "hot_leads.hot_leads_created_at > '$past_three_days' AND hot_leads.hot_leads_created_at ='$date'";
		 echo "<script>localStorage['filtered']='yesterday';</script>";
	  }
	  elseif($filter_by =='yesterday')
	  {
		 // $prevDate = date('Y-m-d',strtotime("-2 days"));
		 // $whereClause = "hot_leads.hot_leads_created_at = '$prevDate'";
		 // echo "<script>localStorage['filtered']='yesterday';</script>";
		 // $past_three_days = date('Y-m-d',strtotime("-1 days"));
		 $date = date('Y-m-d');
		 $past_three_days = date('Y-m-d',strtotime("-1 days"));
		 $whereClause = "hot_leads.hot_leads_created_at > '$past_three_days' AND hot_leads.hot_leads_created_at <'$date'";
		 echo "<script>localStorage['filtered']='yesterday';</script>";
	  }
	  elseif($filter_by =='past_3_days')
	  {
		 $past_three_days = date('Y-m-d',strtotime("-2 days"));
		 $whereClause = "hot_leads.hot_leads_created_at >= '$past_three_days'";
		 echo "<script>localStorage['filtered']='past_3_days';</script>";
	  }
	  elseif($filter_by =='week')
	  {
		 $past_three_days = date('Y-m-d',strtotime("-6 days"));
		 $whereClause = "hot_leads.hot_leads_created_at >= '$past_three_days'";
		 echo "<script>localStorage['filtered']='week';</script>";
	  }
	  elseif($filter_by =='month')
	  {
		 $month = date('m-Y');
		 $start_date_temp = '01-'.$month;
		 $start_date = date('Y-m-d',strtotime($start_date_temp));
		 $end_date_temp = '31-'.$month;
		 $end_date = date('Y-m-d',strtotime($end_date_temp));
		 $whereClause = "hot_leads.hot_leads_created_at BETWEEN '$start_date' AND '$end_date'";
	  }
	  elseif($filter_by =='all')
	  {
		 $isLoggedIn = $this->isLoggedIn();
		 if($isLoggedIn == 'false')
		 {
			 $url = url('/');
		  return redirect()->to($url);
		 }
		 $data['user_id'] = session('user_id');
		 if(session('role') == 'admin' || session('role') == 'superuser')
		 {
			$hotleads = DB::select("SELECT * FROM hot_leads where hot_leads_status='Pending' ORDER BY hot_leads.hot_leads_created_at DESC");
		 }
		 elseif(session('role') == 'user')
		 {
			$session_user_id = session('user_id');
			$hotleads = DB::select("SELECT * FROM hot_leads where hot_leads_status='Pending' AND (hot_leads_lead_owner IS NULL OR hot_leads_lead_owner = '' OR hot_leads_lead_owner = $lead_owner) ORDER BY hot_leads.hot_leads_created_at DESC");
		 }
		 $data['hotleads'] = json_decode(json_encode($hotleads), true);
		 echo "<script>localStorage['filtered']='all';</script>";
		 return view('HotLeads/hot_leads',$data);
	  }
	  $isLoggedIn = $this->isLoggedIn();
	  if($isLoggedIn == 'false')
	  {
			$url = url('/');
		 return redirect()->to($url);
	  }
	  $data['user_id'] = session('user_id');
	  if(session('role') == 'admin' || session('role') == 'superuser')
	  {
		 $hotleads = DB::select("SELECT * FROM hot_leads where hot_leads_status='Pending' AND $whereClause ORDER BY hot_leads.hot_leads_created_at DESC");
	  }
	  elseif(session('role') == 'user')
	  {
		 $session_user_id = session('user_id');
		 $hotleads = DB::select("SELECT * FROM hot_leads where hot_leads_status='Pending' AND $whereClause AND (hot_leads_lead_owner IS NULL OR hot_leads_lead_owner = '' OR hot_leads_lead_owner = $lead_owner) ORDER BY hot_leads.hot_leads_created_at DESC");
	  }
	  $data['hotleads'] = json_decode(json_encode($hotleads), true);
	  return view('HotLeads/hot_leads',$data);
   }
   public function filterHotInprocessLeads($filter_by)
   {
	  if($filter_by =='today')
	  {
		 // $date = date('Y-m-d');
		 // $whereClause = "hot_leads.hot_leads_created_at = '$date'";
		 // echo "<script>localStorage['filtered']='today';</script>";
		 $date = date('Y-m-d',strtotime("+2 days"));
		 $past_three_days = date('Y-m-d',strtotime("-1 days"));
		 $whereClause = "hot_leads.hot_leads_created_at > '$past_three_days' AND hot_leads.hot_leads_created_at ='$date'";
		 echo "<script>localStorage['filtered']='today';</script>";
	  }
	  elseif($filter_by =='yesterday')
	  {
		 // $prevDate = date('Y-m-d',strtotime("-2 days"));
		 // $whereClause = "hot_leads.hot_leads_created_at = '$prevDate'";
		 // echo "<script>localStorage['filtered']='yesterday';</script>";
		 // $past_three_days = date('Y-m-d',strtotime("-1 days"));
		 $date = date('Y-m-d');
		 $past_three_days = date('Y-m-d',strtotime("-1 days"));
		 $whereClause = "hot_leads.hot_leads_created_at > '$past_three_days' AND hot_leads.hot_leads_created_at <'$date'";
		 echo "<script>localStorage['filtered']='yesterday';</script>";
	  }
	  elseif($filter_by =='past_3_days')
	  {
		 $past_three_days = date('Y-m-d',strtotime("-2 days"));
		 $whereClause = "hot_leads.hot_leads_created_at >= '$past_three_days'";
		 echo "<script>localStorage['filtered']='past_3_days';</script>";
	  }
	  elseif($filter_by =='week')
	  {
		 $past_three_days = date('Y-m-d',strtotime("-6 days"));
		 $whereClause = "hot_leads.hot_leads_created_at >= '$past_three_days'";
		 echo "<script>localStorage['filtered']='week';</script>";
	  }
	  elseif($filter_by =='month')
	  {
		 $month = date('m-Y');
		 $start_date_temp = '01-'.$month;
		 $start_date = date('Y-m-d',strtotime($start_date_temp));
		 $end_date_temp = '31-'.$month;
		 $end_date = date('Y-m-d',strtotime($end_date_temp));
		 $whereClause = "hot_leads.hot_leads_created_at BETWEEN '$start_date' AND '$end_date'";
	  }
	  elseif($filter_by =='all')
	  {
		 $isLoggedIn = $this->isLoggedIn();
		 if($isLoggedIn == 'false')
		 {
			 $url = url('/');
		  return redirect()->to($url);
		 }
		 $data['user_id'] = session('user_id');
		 if(session('role') == 'admin' || session('role') == 'superuser')
		 {
			$hotleads = DB::select("SELECT * FROM hot_leads where hot_leads_status='In Process'ORDER BY hot_leads.hot_leads_created_at DESC");
		 }
		 elseif(session('role') == 'user')
		 {
			$session_user_id = session('user_id');
			$hotleads = DB::select("SELECT * FROM hot_leads where hot_leads_status='In Process' AND hot_leads_lead_owner = $session_user_id ORDER BY hot_leads.hot_leads_created_at DESC");
		 }
		 $data['hotleads'] = json_decode(json_encode($hotleads), true);
		 echo "<script>localStorage['filtered']='all';</script>";
		 for ($i=0; $i < count($data['hotleads']); $i++) 
		 { 
			$user_id = $data['hotleads'][$i]['hot_leads_lead_owner'];
			$lead_owner_details = DB::select("SELECT * FROM user WHERE id=$user_id");
			$data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
			$data['hotleads'][$i]['username'] =  $data['lead_owner_details'][0]['username'];
			$data['hotleads'][$i]['user_id'] =  $data['lead_owner_details'][0]['id'];         
		 }
		 return view('HotLeads/view_all_inprocess_lead',$data);
	  }
	  $isLoggedIn = $this->isLoggedIn();
	  if($isLoggedIn == 'false')
	  {
			$url = url('/');
		 return redirect()->to($url);
	  }
	  $data['user_id'] = session('user_id');
	  if(session('role') == 'admin' || session('role') == 'superuser')
	  {
		 $hotleads = DB::select("SELECT * FROM hot_leads where hot_leads_status='In Process' AND $whereClause ORDER BY hot_leads.hot_leads_created_at DESC");
	  }
	  elseif(session('role') == 'user')
	  {
		 $session_user_id = session('user_id');
		 $hotleads = DB::select("SELECT * FROM hot_leads where hot_leads_status='In Process' AND $whereClause AND hot_leads_lead_owner = $session_user_id ORDER BY hot_leads.hot_leads_created_at DESC");
	  }
	  $data['hotleads'] = json_decode(json_encode($hotleads), true);
	  for ($i=0; $i < count($data['hotleads']); $i++) 
	  { 
		 $user_id = $data['hotleads'][$i]['hot_leads_lead_owner'];
		 $lead_owner_details = DB::select("SELECT * FROM user WHERE id=$user_id");
		 $data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
		 $data['hotleads'][$i]['username'] =  $data['lead_owner_details'][0]['username'];
		 $data['hotleads'][$i]['user_id'] =  $data['lead_owner_details'][0]['id'];         
	  }
	  return view('HotLeads/view_all_inprocess_lead',$data);
   }
   public function filterHotClosedLeads($filter_by)
   {
	  if($filter_by =='today')
	  {
		 // $date = date('Y-m-d');
		 // $whereClause = "hot_leads.hot_leads_created_at = '$date'";
		 // echo "<script>localStorage['filtered']='today';</script>";
		 $date = date('Y-m-d',strtotime("+2 days"));
		 $past_three_days = date('Y-m-d',strtotime("-1 days"));
		 $whereClause = "hot_leads.hot_leads_created_at > '$past_three_days' AND hot_leads.hot_leads_created_at ='$date'";
		 echo "<script>localStorage['filtered']='today';</script>";
	  }
	  elseif($filter_by =='yesterday')
	  {
		 // $prevDate = date('Y-m-d',strtotime("-2 days"));
		 // $whereClause = "hot_leads.hot_leads_created_at = '$prevDate'";
		 // echo "<script>localStorage['filtered']='yesterday';</script>";
		 // $past_three_days = date('Y-m-d',strtotime("-1 days"));
		 $date = date('Y-m-d');
		 $past_three_days = date('Y-m-d',strtotime("-1 days"));
		 $whereClause = "hot_leads.hot_leads_created_at > '$past_three_days' AND hot_leads.hot_leads_created_at <'$date'";
		 echo "<script>localStorage['filtered']='yesterday';</script>";
	  }
	  elseif($filter_by =='past_3_days')
	  {
		 $past_three_days = date('Y-m-d',strtotime("-2 days"));
		 $whereClause = "hot_leads.hot_leads_created_at >= '$past_three_days'";
		 echo "<script>localStorage['filtered']='past_3_days';</script>";
	  }
	  elseif($filter_by =='week')
	  {
		 $past_three_days = date('Y-m-d',strtotime("-6 days"));
		 $whereClause = "hot_leads.hot_leads_created_at >= '$past_three_days'";
		 echo "<script>localStorage['filtered']='week';</script>";
	  }
	  elseif($filter_by =='month')
	  {
		 $month = date('m-Y');
		 $start_date_temp = '01-'.$month;
		 $start_date = date('Y-m-d',strtotime($start_date_temp));
		 $end_date_temp = '31-'.$month;
		 $end_date = date('Y-m-d',strtotime($end_date_temp));
		 $whereClause = "hot_leads.hot_leads_created_at BETWEEN '$start_date' AND '$end_date'";
	  }
	  elseif($filter_by =='all')
	  {
		 $isLoggedIn = $this->isLoggedIn();
		 if($isLoggedIn == 'false')
		 {
			 $url = url('/');
		  return redirect()->to($url);
		 }
		 $data['user_id'] = session('user_id');
		 if(session('role') == 'admin' || session('role') == 'superuser')
		 {
			$hotleads = DB::select("SELECT * FROM hot_leads where hot_leads_status = 'Closed' ORDER BY hot_leads.hot_leads_created_at DESC");
		 }
		 elseif(session('role') == 'user')
		 {
			$session_user_id = session('user_id');
			$hotleads = DB::select("SELECT * FROM hot_leads where hot_leads_status = 'Closed' AND hot_leads_lead_owner = $session_user_id ORDER BY hot_leads.hot_leads_created_at DESC");
		 }
		 $data['hotleads'] = json_decode(json_encode($hotleads), true);
		 echo "<script>localStorage['filtered']='all';</script>";
		 for ($i=0; $i < count($data['hotleads']); $i++) 
		 { 
			$user_id = $data['hotleads'][$i]['hot_leads_lead_owner'];
			$lead_owner_details = DB::select("SELECT * FROM user WHERE id=$user_id");
			$data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
			$data['hotleads'][$i]['username'] =  $data['lead_owner_details'][0]['username'];
			$data['hotleads'][$i]['user_id'] =  $data['lead_owner_details'][0]['id'];         
		 }
		 return view('HotLeads/view_all_closed_leads',$data);
	  }
	  $isLoggedIn = $this->isLoggedIn();
	  if($isLoggedIn == 'false')
	  {
			$url = url('/');
		 return redirect()->to($url);
	  }
	  $data['user_id'] = session('user_id');
	  if(session('role') == 'admin' || session('role') == 'superuser')
	  {
		 $hotleads = DB::select("SELECT * FROM hot_leads where hot_leads_status = 'Closed' AND $whereClause ORDER BY hot_leads.hot_leads_created_at DESC");
	  }
	  elseif(session('role') == 'user')
	  {
		 $session_user_id = session('user_id');
		 $hotleads = DB::select("SELECT * FROM hot_leads where hot_leads_status = 'Closed' AND $whereClause AND hot_leads_lead_owner = $session_user_id ORDER BY hot_leads.hot_leads_created_at DESC");
	  }
	  $data['hotleads'] = json_decode(json_encode($hotleads), true);
	  for ($i=0; $i < count($data['hotleads']); $i++) 
	  { 
		 $user_id = $data['hotleads'][$i]['hot_leads_lead_owner'];
		 $lead_owner_details = DB::select("SELECT * FROM user WHERE id=$user_id");
		 $data['lead_owner_details'] = json_decode(json_encode($lead_owner_details), true);
		 $data['hotleads'][$i]['username'] =  $data['lead_owner_details'][0]['username'];
		 $data['hotleads'][$i]['user_id'] =  $data['lead_owner_details'][0]['id'];         
	  }
	  return view('HotLeads/view_all_closed_leads',$data);
   }
}
