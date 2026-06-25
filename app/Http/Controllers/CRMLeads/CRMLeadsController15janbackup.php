<?php

namespace App\Http\Controllers\CRMLeads;

use App\Http\Controllers\Controller;
use App\Models\Lead\customer_detail;
use App\Models\LinkCustDetails;
use App\Models\Lead\lead;
use App\Models\leads_log;
use App\Models\LeadsQueryLog;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use App\Models\GoogleCampaignReport;
use PDF;
use Mail;
use File;
use DateTime;
use DateTimeZone;
use DatePeriod;
use DateInterval;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DailyNewSiteOrders;
use App\Exports\ReminderOverdueMail;
use App\Exports\AllLeadsReportExport;
use App\Http\Controllers\RenewalPickup\RenewalPickupController;
//use Storage;

class CRMLeadsController extends Controller
{
   public function isLoggedIn()
   {
      $data = session('isLoggedIn');
      //print_r($data);      
      return $data;
   }
   
    // ****View CRM All Leads****
    public function viewCRMAllLeads()
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
	  
		// $get_all_hot_leads = DB::table('hot_leads')
		// 	->whereNotNull('hot_leads_contact_no')
		// 	->where(array(['hot_leads_status','=','Pending'],['hot_leads_contact_no','!=','']))
		// 	->whereOr(['hot_leads_lead_owner','=','Null'],['hot_leads_lead_owner','=',''])
		// 	->whereOr($whereClause)
		// 	->orderBy('hot_lead_id','DESC')
		// 	->paginate(10);

		$get_all_hot_leads = DB::table('crm_leads as cl')
			->leftJoin('customer_details as cd', 'cd.cust_id', '=', 'cl.customerId')
			->select(
				'cl.*',
				'cd.customer_name'
			)
			->where('cl.verify_status', '0')
			->orderBy('cl.cmsLeadsId', 'DESC')
			->paginate(10);

		$data['get_all_hot_leads'] = $get_all_hot_leads;


        return view('CRMLeads/viewCRMAllLeads',$data);
    }

	public function viewWebsiteOrder()
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
	  
		$ordersList = DB::connection('mysql_second')
			->table('tblorders as o')
			->leftJoin('tblorderdetails as od', 'od.orderId', '=', 'o.orderId')
			->leftJoin('tblcustomers as cus', 'cus.customerId', '=', 'o.customerId')
			->select(
				'o.*',
				'od.productId',
				DB::raw("CONCAT(cus.firstName, ' ', cus.lastName) as customer_name"),
				'cus.mobile','cus.city'
			)
			->orderBy('o.orderId', 'DESC')
			->get();

			// echo "<pre>";print_r($ordersList);die;

		$data['ordersList'] = $ordersList;


        return view('CRMLeads/viewWebsiteOrder',$data);
    }

    // ****View CRM All Leads****
    public function createLeads()
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

        $products = DB::select("SELECT * FROM products WHERE flag = 'Active'");
		// $products = DB::connection('mysql_second')
        //       ->select("SELECT * FROM tblproducts WHERE status = '1' AND deleted='0'");

        $data['products'] = \json_decode(\json_encode($products), true);
        
        return view('CRMLeads/createLeads',$data);
    }

	public function getProductsByType(Request $request)
	{
		$type = $request->type;

		// $products = DB::connection('mysql_second')
		// 		->select("SELECT * FROM tblproducts WHERE status = '1' AND deleted='0'");
		$products = DB::select("SELECT * FROM products WHERE flag = 'Active'");

		return response()->json($products);
	}

	public function createLeads_Save(Request $request)
	{
		// Validation (optional)
		$request->validate(
			[
				'order_status'   => 'required',
				'source'         => 'required',
				'contact_no'     => 'required',
				'customer_name'  => 'required',
				'ship_flat'      => 'required',
				'ship_landmark'  => 'required',
				'ship_pincode'   => 'required',
				'bill_flat'      => 'required',
				'bill_landmark'  => 'required',
			],
			[
				'order_status.required'  => 'Enter your Order Status',
				'source.required'        => 'Select a Source',
				'contact_no.required'    => 'Enter Contact Number',
				'customer_name.required' => 'Enter Customer Name',

				'ship_flat.required'     => 'Enter Shipping Flat/House No.',
				'ship_landmark.required' => 'Enter Shipping Landmark',
				'ship_pincode.required'  => 'Enter Shipping Pincode',

				'bill_flat.required'     => 'Enter Billing Flat/House No.',
				'bill_landmark.required' => 'Enter Billing Landmark',
			]
		);

		$customer = DB::table('customer_details')
    ->where('primary_contact_no', $request->contact_no)
    ->first();

if ($customer) {
    // Customer already exists
    $customerId = $customer->cust_id;
} else {
	$todaydate = now()->setTimezone('Asia/Kolkata')->toDateString();
    // Insert new customer
    $customerId = DB::table('customer_details')->insertGetId([
        'customer_name'        => $request->customer_name,
        'cust_gender'        => $request->gender,
        'location'        => $request->bill_city,
        'address_line_1'        => $request->bill_flat,
        'landmark'        => $request->bill_landmark,
        'pincode'        => $request->bill_pincode,
        'state'        => $request->bill_state,
        'cust_date'        => $todaydate,
        'email_id'        => $request->email,
        'primary_contact_no'   => $request->contact_no,
        'customer_type'   => 'Individual',
        'cust_source'   => 'System',
        'created_at'           => now(),
        'updated_at'           => now(),
    ]);
}

		$leadId = DB::table('crm_leads')->insert([
			// Step 1
			'order_status'   => $request->order_status,
			'customerId'    => $customerId,
			'source'         => $request->source,
			'agentName'      => $request->agentName,
			'payment_mode'   => $request->payment_mode,
			'city'           => $request->city,
			'outskirts'      => $request->has('outskirts') ? 1 : 0,
			'follow_up'      => $request->follow_up,
			'remark'         => $request->remark,

			// Step 2 - Customer
			'customer_name'  => $request->customer_name,
			'contact_no'     => $request->contact_no,
			'email'          => $request->email,

			// Patient
			'patient_name'   => $request->patient_name,
			'gender'         => $request->gender,
			'age'            => $request->age,

			// Step 3 - Shipping
			'ship_search_address'      => $request->ship_search_address,
			'ship_flat'      => $request->ship_flat,
			'ship_landmark'  => $request->ship_landmark,
			'ship_pincode'   => $request->ship_pincode,
			'ship_state'     => $request->ship_state,
			'ship_city'      => $request->ship_city,
			'ship_contact_person' => $request->ship_contact_person,
			'ship_contact_no'     => $request->ship_contact_no,
			'pickup_store'   => $request->has('pickup_store') ? 1 : 0,

			// Billing
			'bill_search_address'      => $request->bill_search_address,
			'bill_flat'      => $request->bill_flat,
			'bill_landmark'  => $request->bill_landmark,
			'bill_pincode'   => $request->bill_pincode,
			'bill_state'     => $request->bill_state,
			'bill_city'      => $request->bill_city,
			'bill_contact_person' => $request->bill_contact_person,
			'bill_contact_no'     => $request->bill_contact_no,
			'same_as_shipping'    => $request->has('same_as_shipping') ? 1 : 0,

			// Step 4 - Equipments
			// 'billing_type'     => $request->billing_type,
			// 'period'     => $request->period,
			// 'quantity'     => $request->quantity,
			// 'product'     => $request->product,

		]);

		// ⭐ Insert Equipments
		if ($request->has('items.product_id')) {

			foreach ($request->items['product_id'] as $index => $pid) {

				DB::table('crm_equipments')->insert([
					'cmsLeadsId'     => $leadId,
					'product_id'     => $request->items['product_id'][$index],
					'billing_type'   => $request->items['billing_type'][$index],
					// 'period'         => $request->items['period'][$index],
					// ⭐ If Sale → period = 0, otherwise use user value
            		'period'         => ($request->items['billing_type'][$index] == 'Sale') ? 0: $request->items['period'][$index],
					'qty'            => $request->items['qty'][$index],
					'price'          => $request->items['price'][$index],
					'deposit'        => $request->items['deposit'][$index],
					'transport'      => $request->items['transport'][$index],
					'discount'       => $request->items['discount'][$index],
					'totalAmt'       => $request->items['total'][$index],
				]);
			}
		}

		return redirect()->back()->with('success', 'Lead created successfully!');
	}

	public function searchByMobile(Request $request)
{
    $mobile = $request->mobile;
	
	$lead = DB::table('crm_leads')
        ->where('contact_no', $mobile)
        ->first();

    $custinfo = DB::table('customer_details')
        ->where('primary_contact_no', $mobile)
        ->first();

    if (!empty($custinfo) && empty($lead)) {
        // return response()->json([
        //     'success' => false,
        //     'message' => 'No record found'
        // ]);

		  return response()->json([
        'success' => true,
        'data' => [
            'customer_name' => $custinfo->customer_name,
            'contact_no' => $custinfo->primary_contact_no,
            'email' => $custinfo->email_id,
            'patient_name' => $custinfo->patient_name,
            'gender' => $custinfo->cust_gender,
            // 'age' => $lead->age,
            // 'pickup_store' => $lead->pickup_store,
            // 'ship_search_address' => $lead->ship_search_address,
            // 'ship_flat' => $lead->ship_flat,
            // 'ship_landmark' => $lead->ship_landmark,
            // 'ship_pincode' => $lead->ship_pincode,
            // 'ship_state' => $lead->ship_state,
            // 'ship_city' => $lead->ship_city,
            // 'ship_contact_person' => $lead->ship_contact_person,
            // 'ship_contact_no' => $lead->ship_contact_no,

			// 'same_as_shipping' => $lead->same_as_shipping,
			// 'bill_search_address' => $lead->bill_search_address,
            // 'bill_flat' => $lead->bill_flat,
            // 'bill_landmark' => $lead->bill_landmark,
            // 'bill_pincode' => $lead->bill_pincode,
            'bill_state' => $custinfo->state,
            'bill_city' => $custinfo->city,
        ]
    ]);

    }elseif (!empty($custinfo) && !empty($lead)) {
        // return response()->json([
        //     'success' => false,
        //     'message' => 'No record found'
        // ]);

		  return response()->json([
        'success' => true,
        'data' => [
            'customer_name' => $lead->customer_name,
            'contact_no' => $custinfo->primary_contact_no,
            'email' => $lead->email,
            'patient_name' => $lead->patient_name,
            'gender' => $lead->gender,
            'age' => $lead->age,
            'pickup_store' => $lead->pickup_store,
            'ship_search_address' => $lead->ship_search_address,
            'ship_flat' => $lead->ship_flat,
            'ship_landmark' => $lead->ship_landmark,
            'ship_pincode' => $lead->ship_pincode,
            'ship_state' => $lead->ship_state,
            'ship_city' => $lead->ship_city,
            'ship_contact_person' => $lead->ship_contact_person,
            'ship_contact_no' => $lead->ship_contact_no,

			'same_as_shipping' => $lead->same_as_shipping,
			'bill_search_address' => $lead->bill_search_address,
            'bill_flat' => $lead->bill_flat,
            'bill_landmark' => $lead->bill_landmark,
            'bill_pincode' => $lead->bill_pincode,
            'bill_state' => $lead->bill_state,
            'bill_city' => $lead->bill_city,
            'bill_contact_person' => $lead->bill_contact_person,
            'bill_contact_no' => $lead->bill_contact_no,
        ]
    ]);

    }else{
		return response()->json([
            'success' => false,
            'message' => 'No record found'
        ]);
	}

  
}

// public function verifyUpdate(Request $request)
// {

// // echo "Pooja";
//     $request->validate([
//         'lead_id' => 'required',
//         'verify_status' => 'required|in:0,1'
//     ]);

	

//     DB::table('crm_leads')
//         ->where('cmsLeadsId', $request->lead_id)
//         ->update([
//             'verify_status' => $request->verify_status
//         ]);

//     return redirect()->back()->with('success', 'Lead status updated successfully');
// }

public function verifyUpdate(Request $request)
{


    $request->validate([
        'lead_id' => 'required',
        'verify_status' => 'required|in:0,1'
    ]);

    // DB::beginTransaction();

    // try {

        DB::table('crm_leads')
            ->where('cmsLeadsId', $request->lead_id)
            ->update([
                'verify_status' => $request->verify_status
            ]);	

        $lead = DB::table('crm_leads')
            ->where('cmsLeadsId', $request->lead_id)
            ->first();
		
			// print_r($lead); die;


        

        $equipments = DB::table('crm_equipments')
            ->where('cmsLeadsId', $request->lead_id)
            ->get();

        $productIds = [];
        $quantities = [];
        $period = [];

        foreach ($equipments as $eq) {
            $productIds[] = (string) $eq->product_id;
            $quantities[] = (int) $eq->qty;
            $period[]     = (int) $eq->period;
        }

        $date = now()->setTimezone('Asia/Kolkata')->toDateString();
$lead_owner = session('user_id');
       $inserted = DB::table('leads')->insert([
            'source_id'             => $lead->source,
            'customer_id'           => $lead->customerId,
            'creation_date'         => $lead->created_at,
            'converted_at'          => $date,
            'patient_name'          => $lead->patient_name,
            'patient_gender'        => $lead->gender,
            'patient_age'           => $lead->age,
            'lead_source'           => $lead->source,
            'lead_status'           => $lead->order_status,
            'equipment_requirement' => json_encode($productIds),
            'equipment_qty'         => json_encode($quantities),
            'months'                => json_encode($period),
            'created_at'            => now(),
            'created_by'            => $lead_owner,
            'lead_owner'            => $lead_owner,
            'updated_at'            => now(),
        ]);

			if($inserted)
				{
 					return redirect()->back()->with('success', 'Lead verified & moved successfully');
				}
        // DB::commit();

       

    // } catch (\Exception $e) {
    //     DB::rollBack();
    //     return redirect()->back()->with('error', $e->getMessage());
    // }
}


   
}
