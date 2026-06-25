<?php

    namespace App\Http\Controllers\OrderManagement;

    use Illuminate\Support\Facades\Validator;
    use Illuminate\Support\Facades\DB;
    //use Illuminate\Support\Collection;
    use App\Support\Collection;
    use App\Models\VendorRegister;
    use App\Models\UserRegister;
    use App\Models\DelOrders;
    use App\Models\lead;
    use App\Models\OrderDetails;
    use App\Models\VendorProducts;
    use App\Models\leads_log;
    use App\Models\VendorProductDetails;
    use App\Models\sale_vendor_products;
    use App\Models\VendorRentedProducts;
    use App\Models\Renewal;
    use App\Models\Pickup;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    use Mail;
    use App\Exports\OrdersExport;
    use Maatwebsite\Excel\Facades\Excel;
    use Carbon\Carbon;
    use Exception;

    class OrderController extends Controller
    {
        public function isLoggedIn()
        {
            $data = session('isLoggedIn');
            return $data;
        }
        public function ViewAllLeads_new(Request $request)
        {
            $btn_submit = $request->get('btn_submit');
            //$get_min_date = lead::get('creation_date')->first();
            $get_min_date = Carbon::now()->toDateString();
            $get_max_date = Carbon::now()->toDateString();
            // $date_changed = "False";
            // if(isset($btn_submit))
            // {
            //     $date_changed = "True";
            //     $get_min_date = "2021-08-01";
            //     $get_max_date = lead::max('creation_date');
            // }
            // else
            // {
            //     $get_min_date = date('Y-m-d');//lead::min('creation_date');
            //     $get_max_date = date('Y-m-d');//lead::max('creation_date');
            // }
            
            $get_lead_owners = DB::table('user')
                                ->select('id as user_id','username as lead_owner')
                                ->whereIn('role',["user","admin","superuser"])
								->get();
		    $lead_owners_arr = array_column($get_lead_owners->toArray(),'user_id');

            $whereCondition = [];
            $customer_name = $request->get('filter_customer_name');
            if(isset($customer_name)){
                $whereCondition1 = ['customer_details.customer_name','LIKE','%'.$customer_name.'%'];
                array_push($whereCondition,$whereCondition1);
            }
            $customer_contact = $request->get('filter_contact_no');
            if(isset($customer_contact)) {
                $whereCondition2 = ['customer_details.primary_contact_no','=',$customer_contact];
                array_push($whereCondition,$whereCondition2);
            }
            $lead_status = $request->get('filter_lead_status');
            $status_arr = ["Converted","Order Generated","Vendor Assigned","Delivery In Progress"];
            if(isset($lead_status) && in_array($lead_status,$status_arr)){
                $whereCondition3 = ['leads.lead_status','=',$lead_status];
                array_push($whereCondition,$whereCondition3);
            }
            $lead_owner = $request->get('filter_lead_owner');
            if(isset($lead_owner) && in_array($lead_owner,$lead_owners_arr)){
                $whereCondition7 = ['leads.lead_owner','=',$lead_owner];
                array_push($whereCondition,$whereCondition7);
            }
            if(session('role')=='user')
            {
                $whereCondition8 = ['leads.lead_owner','=',session('user_id')];
                array_push($whereCondition,$whereCondition8);
            }
            // $from_date = null;
            // $end_date = null;
            if(!empty($request->get('filter_from_date')) && !empty($request->get('filter_end_date'))){
                $get_min_date = $request->get('filter_from_date');
                $get_max_date = $request->get('filter_end_date');
                // $from_date = $get_min_date;
                // $end_date = $get_max_date;
            }
            // else{
            //     $request->validate(
            //         [
            //            'filter_from_date' => 'required',
            //            'filter_end_date' => 'required',
            //         ]
            //      );
            // }
            $sort_colmun = $request->get('sort_column');
            $sort_val = $request->get('sort_direction');
            $column = 'leads.id';
            $direction = 'DESC';
            if(isset($sort_colmun) && isset($sort_val)){
                $column = $sort_colmun;
                $direction = $sort_val;
            }
            $get_all_leads = DB::table('leads')
                                    ->join('customer_details','leads.customer_id','=','customer_details.cust_id')
                                    ->join('user','leads.lead_owner','=','user.id')
                                    ->select('leads.*','customer_details.*','leads.id as lead_id','user.*')
                                    ->whereIn('leads.lead_status',$status_arr)
                                    ->wherebetween('leads.creation_date',[$get_min_date,$get_max_date])
                                    ->where($whereCondition)
                                    ->orderBy($column,$direction)
                                    ->paginate(10);
            $all_leads_products = json_decode(json_encode($get_all_leads->toArray()),true);
            foreach($all_leads_products['data'] as $key=>$lead)
            {
                $get_product_name = DB::table('products')->select('product_name')->whereIn('id',json_decode($lead['equipment_requirement']))->get()->toArray();
                $get_product_name = implode(",",array_column(json_decode(json_encode($get_product_name),true),'product_name'));
                $all_leads_products['data'][$key]['product_name']=$get_product_name;
            }
            //get all status of cash and customer
            $all_leads_payment_status = DB::table('leads')
                    ->join('customer_details','leads.customer_id','=','customer_details.cust_id')
                    ->join('user','leads.lead_owner','=','user.id')
                    ->whereIn('leads.lead_status',$status_arr)
                    ->wherebetween('leads.creation_date',[$get_min_date,$get_max_date])
                    ->where($whereCondition)
                    ->orderBy('leads.id','DESC')
                    ->get();
            $all_leads_payment_status = json_decode(json_encode($all_leads_payment_status->toArray()),true);
            $unq_cust = array();
            $total_products = array();
            foreach($all_leads_payment_status as $key => $lead_info) 
            {
                $customer_id = $lead_info['customer_id'];
                if(in_array($customer_id,$unq_cust))
                {
                    for($i=0; $i<count($total_products); $i++)
                    {
                        if($total_products[$i]['customer_id'] == $lead_info['customer_id'])
                        {
                            $count = count($total_products[$i]['product_details']);
                            $total_products[$i]['product_details'][$count]['quantity'] = json_decode($lead_info['equipment_qty']);
                            $total_products[$i]['product_details'][$count]['offered_rent'] = json_decode($lead_info['offered_rent']);
                            $total_products[$i]['product_details'][$count]['offered_rent_total'] = json_decode($lead_info['offered_rent_total']);
                            $total_products[$i]['product_details'][$count]['deposit'] = json_decode($lead_info['deposite']);
                            $total_products[$i]['product_details'][$count]['deposit_total'] = json_decode($lead_info['deposite_total']);
                            $total_products[$i]['product_details'][$count]['sale_rental'] = json_decode($lead_info['sale_rental']);
                            $total_products[$i]['product_details'][$count]['transport'] = json_decode($lead_info['transport']);
                        }
                    }
                }
                else
                {
                    array_push($unq_cust,$customer_id);
                    $count = count($total_products);
                    $total_products[$count]['customer_id'] = $lead_info['customer_id'];
                    $total_products[$count]['product_details'][0]['quantity'] = json_decode($lead_info['equipment_qty']);
                    $total_products[$count]['product_details'][0]['offered_rent'] = json_decode($lead_info['offered_rent']);
                    $total_products[$count]['product_details'][0]['offered_rent_total'] = json_decode($lead_info['offered_rent_total']);
                    $total_products[$count]['product_details'][0]['deposit'] = json_decode($lead_info['deposite']);
                    $total_products[$count]['product_details'][0]['deposit_total'] = json_decode($lead_info['deposite_total']);
                    $total_products[$count]['product_details'][0]['transport'] = json_decode($lead_info['transport']);
                    $total_products[$count]['product_details'][0]['sale_rental'] = json_decode($lead_info['sale_rental']);
                }
            }
            //print_r($total_products);
            $product_count = 0;
            $total_rent_product = 0;
            $total_sale_product = 0;
            $total_rent_amt = 0;
            $total_sale_amt = 0;
            $total_deposit = 0;
            $total_transport = 0;
            foreach ($total_products as $total_p)
            {
                $product_details = $total_p['product_details'];
                for ($i=0; $i <count($product_details) ; $i++) { 
                    for ($j=0; $j <count($product_details[$i]['quantity']) ; $j++) { 
                        
                        if($product_details[$i]['sale_rental'][$j]=='Rental')
                        {
                            // print_r($total_p);
                            $total_rent_amt += $product_details[$i]['offered_rent_total'][$j];    
                            $total_rent_product += $product_details[$i]['quantity'][$j];    
                        }
                        elseif($product_details[$i]['sale_rental'][$j]=='Sale')
                        {
                            $total_sale_amt += $product_details[$i]['offered_rent_total'][$j];    
                            $total_sale_product += $product_details[$i]['quantity'][$j];   
                        }

                        $total_transport += $product_details[$i]['transport'][$j];
                        $total_deposit += $product_details[$i]['deposit_total'][$j];
                        $product_count += $product_details[$i]['quantity'][$j];
                    }
                }
            }
            $cust_or_pay_status['product_count'] = $product_count;
            $cust_or_pay_status['total_customer'] = count($unq_cust);
            $cust_or_pay_status['total_amount'] = $total_rent_amt+$total_sale_amt+$total_transport+$total_deposit;
            $cust_or_pay_status['total_rent_product'] = $total_rent_product;
            $cust_or_pay_status['total_sale_product'] = $total_sale_product;
            $cust_or_pay_status['total_sale_amt'] = $total_sale_amt;
            $cust_or_pay_status['total_rent_amt'] = $total_rent_amt;
            $cust_or_pay_status['total_deposit_amt'] = $total_deposit;
            $cust_or_pay_status['total_transport'] = $total_transport;
            //close
            $filter_collapse_cookie = null;
            if(isset($_COOKIE['filter_collapse_js']) && $_COOKIE['filter_collapse_js'] =='Yes')
            {
                $filter_collapse_cookie = 1;
            }
            $filter_arr = ["cust_name"=>$customer_name,
                            "cust_no"=>$customer_contact,
                            "lead_status"=>$lead_status,
                            "lead_owner"=>$lead_owner,
                            "get_min_date"=>$get_min_date,
                            "get_max_date"=>$get_max_date,
                            // "from_date" => $from_date,
                            // "end_date"=>$end_date,
                            "btn_submit"=>$btn_submit,
                            "sort_column"=>$sort_colmun,
                            "sort_val"=>$sort_val,
                            "filter_collapse_cookie"=>$filter_collapse_cookie];
            return view('OrderManagement/view_all_leads',compact('get_all_leads','all_leads_products','filter_arr','get_lead_owners','cust_or_pay_status'));
            
            //$leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress' ) AND leads.creation_date='$date' AND leads.lead_owner = $user_id ORDER BY leads.creation_date DESC");
        }
        public function viewAllLeads(Request $request)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
            return redirect()->to($url);
            }
            $date = date('Y-m-d');
            $data['start_date'] = $date;
            $data['end_date'] = $date;
            $user = DB::select("SELECT id,username FROM user WHERE role!='vendor' ");
            $data['users'] = json_decode(json_encode($user),true);
            if(session('role') == "superuser")
            {
                if(!empty($request->get('user_id')))
                {
                    $user_id=$request->get('user_id');
                    $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress' ) AND leads.creation_date='$date' AND leads.lead_owner = $user_id ORDER BY leads.creation_date DESC");
                }
                else
                {
                    $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress' ) AND leads.creation_date='$date' AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
                }
                
            }
            elseif(session('role') == "admin")
            {
                $user_city = session('user_city');
                $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated') AND customer_details.citygroup='$user_city' AND leads.creation_date='$date' AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
            }
            elseif(session('role') == "user")
            {
                $user_city = session('user_city');
                $user_id = session('user_id');
                $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress' ) AND leads.creation_date='$date' AND leads.lead_owner = $user_id AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
            }

            //$leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated') ORDER BY leads.creation_date DESC");
            //echo "SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated') ORDER BY leads.creation_date DESC";
            $data['lead_details'] = json_decode(json_encode($leads), true);
            for ($i=0; $i < count($data['lead_details']); $i++) 
            { 
                $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
                $equipement_details = array();
                for ($j=0; $j <count($product); $j++) 
                { 
                    $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
                    $product_details = json_decode(json_encode($product_details), true);
                    array_push($equipement_details,$product_details[0]['product_name']);
                }
                $equipements = json_encode($equipement_details);
                $data['lead_details'][$i]['equipment_requirement'] = $equipements;
            }

            //print_r($data['lead_details']);
            $unq_cust = array();
            $total_products = array();
            foreach($data['lead_details'] as $key => $lead_info) 
            {
                $customer_id = $lead_info['customer_id'];
                if(in_array($customer_id,$unq_cust))
                {
                    for($i=0; $i<count($total_products); $i++)
                    {
                        if($total_products[$i]['customer_id'] == $lead_info['customer_id'])
                        {
                            $count = count($total_products[$i]['product_details']);
                            $total_products[$i]['product_details'][$count]['quantity'] = json_decode($lead_info['equipment_qty']);
                            $total_products[$i]['product_details'][$count]['offered_rent'] = json_decode($lead_info['offered_rent']);
                            $total_products[$i]['product_details'][$count]['offered_rent_total'] = json_decode($lead_info['offered_rent_total']);
                            $total_products[$i]['product_details'][$count]['deposit'] = json_decode($lead_info['deposite']);
                            $total_products[$i]['product_details'][$count]['deposit_total'] = json_decode($lead_info['deposite_total']);
                            $total_products[$i]['product_details'][$count]['sale_rental'] = json_decode($lead_info['sale_rental']);
                            $total_products[$i]['product_details'][$count]['transport'] = json_decode($lead_info['transport']);
                        }
                    }
                }
                else
                {
                    array_push($unq_cust,$customer_id);
                    $count = count($total_products);
                    $total_products[$count]['customer_id'] = $lead_info['customer_id'];
                    $total_products[$count]['product_details'][0]['quantity'] = json_decode($lead_info['equipment_qty']);
                    $total_products[$count]['product_details'][0]['offered_rent'] = json_decode($lead_info['offered_rent']);
                    $total_products[$count]['product_details'][0]['offered_rent_total'] = json_decode($lead_info['offered_rent_total']);
                    $total_products[$count]['product_details'][0]['deposit'] = json_decode($lead_info['deposite']);
                    $total_products[$count]['product_details'][0]['deposit_total'] = json_decode($lead_info['deposite_total']);
                    $total_products[$count]['product_details'][0]['transport'] = json_decode($lead_info['transport']);
                    $total_products[$count]['product_details'][0]['sale_rental'] = json_decode($lead_info['sale_rental']);
                }
            }
            //print_r($total_products);
            $product_count = 0;
            $total_rent_product = 0;
            $total_sale_product = 0;
            $total_rent_amt = 0;
            $total_sale_amt = 0;
            $total_deposit = 0;
            $total_transport = 0;
            foreach ($total_products as $total_p)
            {
                $product_details = $total_p['product_details'];
                for ($i=0; $i <count($product_details) ; $i++) { 
                    for ($j=0; $j <count($product_details[$i]['quantity']) ; $j++) { 
                        
                        if($product_details[$i]['sale_rental'][$j]=='Rental')
                        {
                            $total_rent_amt += $product_details[$i]['offered_rent_total'][$j];    
                            $total_rent_product += $product_details[$i]['quantity'][$j];    
                        }
                        elseif($product_details[$i]['sale_rental'][$j]=='Sale')
                        {
                            $total_sale_amt += $product_details[$i]['offered_rent_total'][$j];    
                            $total_sale_product += $product_details[$i]['quantity'][$j];   
                        }
                        $total_transport += $product_details[$i]['transport'][$j];
                        $total_deposit += $product_details[$i]['deposit_total'][$j];
                        $product_count += $product_details[$i]['quantity'][$j];
                    }
                }
            }
            $data['product_count'] = $product_count;
            $data['total_customer'] = count($unq_cust);
            $data['total_amount'] = $total_rent_amt+$total_sale_amt+$total_transport+$total_deposit;
            $data['total_rent_product'] = $total_rent_product;
            $data['total_sale_product'] = $total_sale_product;
            $data['total_sale_amt'] = $total_sale_amt;
            $data['total_rent_amt'] = $total_rent_amt;
            $data['total_deposit'] = $total_deposit;
            $data['total_transport'] = $total_transport;
            echo "<script>localStorage['filtered']='today';</script>";
            $user = DB::select("SELECT id,username FROM user WHERE role!='vendor' ");
            $data['users'] = json_decode(json_encode($user),true);
            if(!empty($request->get('user_id')))
            {
                $data['user_id']=$request->get('user_id');
            }
            return view('OrderManagement/viewAllLeads',$data);
        }
        public function pendingAssignment()
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
            return redirect()->to($url);
            }
            $date = date('Y-m-d');
            $data['start_date'] = $date;
            $data['end_date'] = $date;
            if(session('role') == "superuser")
            {
                $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' AND leads.creation_date='$date' AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
            }
            elseif(session('role') == "admin")
            {
                $user_city = session('user_city');
                $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' AND customer_details.citygroup='$user_city' AND leads.creation_date='$date' AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
            }
            elseif(session('role') == "user")
            {
                $user_city = session('user_city');
                $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' AND customer_details.citygroup='$user_city' AND leads.creation_date='$date' AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
            }

            //$leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated') ORDER BY leads.creation_date DESC");
            //echo "SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated') ORDER BY leads.creation_date DESC";
            $data['lead_details'] = json_decode(json_encode($leads), true);
            for ($i=0; $i < count($data['lead_details']); $i++) 
            { 
                $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
                $equipement_details = array();
                for ($j=0; $j <count($product); $j++) 
                { 
                    $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
                    $product_details = json_decode(json_encode($product_details), true);
                    array_push($equipement_details,$product_details[0]['product_name']);
                }
                $equipements = json_encode($equipement_details);
                $data['lead_details'][$i]['equipment_requirement'] = $equipements;
            }
            //print_r($data['lead_details']);
            $Countlead = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' ORDER BY leads.creation_date DESC");
            $count1 = count(json_decode(json_encode($Countlead),true));
            $data['count1'] = $count1;
            echo "<script>localStorage['filtered']='today';</script>";
            return view('OrderManagement/pendingAssignment',$data);
        }
        public function order_view_lead($customer_id,$id)
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
            $leads_log_data = DB::select("SELECT * FROM leads_log WHERE log_lead_id = $id");
            $data['leads_log_data'] = json_decode(json_encode($leads_log_data), true);
            $data['log_lead_status'] = array();
            $data['log_lead_date'] = array();
            $data['log_lead_time'] = array();
            foreach($data['leads_log_data'] as $leads)
            {
                array_push($data['log_lead_status'], $leads['log_lead_status']);
                array_push($data['log_lead_date'], $leads['log_date']);
                array_push($data['log_lead_time'], $leads['log_time']);
            }
            return view('OrderManagement/view_lead',$data);
        }
        //-----filter order lead-------//
        public function filterOrderLeads($filter_by)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
             return redirect()->to($url);
            }
            $user = DB::select("SELECT id,username FROM user WHERE role!='vendor' ");
            $data['users'] = json_decode(json_encode($user),true);
            if($filter_by =='today')
            {
                $date = date('Y-m-d');
                $data['start_date'] = $date;
                $data['end_date'] = $date;
                $whereClause = "leads.creation_date = '$date'";
                echo "<script>localStorage['filtered']='today';</script>";
            }
            elseif($filter_by =='yesterday')
            {
                $prevDate = date('Y-m-d',strtotime("-1 days"));
                $data['start_date'] = $prevDate;
                $data['end_date'] = date('Y-m-d');
                $whereClause = "leads.creation_date = '$prevDate'";
                echo "<script>localStorage['filtered']='yesterday';</script>";
            }
            elseif($filter_by =='past_3_days')
            {
                $past_three_days = date('Y-m-d',strtotime("-2 days"));
                $data['start_date'] = $past_three_days;
                $data['end_date'] = date('Y-m-d');
                $whereClause = "leads.creation_date >= '$past_three_days'";
                echo "<script>localStorage['filtered']='past_3_days';</script>";
            }
            elseif($filter_by =='week')
            {
                $past_three_days = date('Y-m-d',strtotime("-7 days"));
                $data['start_date'] = $past_three_days;
                $data['end_date'] = date('Y-m-d');
                $whereClause = "leads.creation_date >= '$past_three_days'";
                echo "<script>localStorage['filtered']='week';</script>";
            }
            elseif($filter_by =='month')
            {
                $month = date('m-Y');
                $start_date_temp = '01-'.$month;
                $start_date = date('Y-m-d',strtotime($start_date_temp));
                $end_date_temp = '31-'.$month;
                $end_date = date('Y-m-d',strtotime($end_date_temp));
                $data['start_date'] = $start_date;
                $data['end_date'] = $end_date;
                $whereClause = "leads.creation_date BETWEEN '$start_date' AND '$end_date'";
            }
            elseif($filter_by == 'all')

            {   if(session('role') == "superuser")
                {
                    $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress' ) AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
                }
                elseif(session('role') == "admin")
                {
                    $user_city = session('user_city');
                    $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated') AND customer_details.citygroup='$user_city' AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
                }
                elseif(session('role') == "user")
                {
                    $user_city = session('user_city');
                    $user_id = session('user_id');
                    // $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress' ) AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
                    $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress' ) AND leads.creation_date='$date' AND leads.lead_owner = $user_id AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
                }
                
        
                    $data['lead_details'] = json_decode(json_encode($leads), true);
                    for ($i=0; $i < count($data['lead_details']); $i++) 
                    { 
                        $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
                        $equipement_details = array();
                        for ($j=0; $j <count($product); $j++) 
                        { 
                            $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
                            $product_details = json_decode(json_encode($product_details), true);
                            array_push($equipement_details,$product_details[0]['product_name']);
                        }
                        $equipements = json_encode($equipement_details);
                        $data['lead_details'][$i]['equipment_requirement'] = $equipements;
                    }
                //total status
                $unq_cust = array();
                $total_products = array();
                foreach($data['lead_details'] as $key => $lead_info) 
                {
                    $customer_id = $lead_info['customer_id'];
                    if(in_array($customer_id,$unq_cust))
                    {
                        for($i=0; $i<count($total_products); $i++)
                        {
                            if($total_products[$i]['customer_id'] == $lead_info['customer_id'])
                            {
                                $count = count($total_products[$i]['product_details']);
                                $total_products[$i]['product_details'][$count]['quantity'] = json_decode($lead_info['equipment_qty']);
                                $total_products[$i]['product_details'][$count]['offered_rent'] = json_decode($lead_info['offered_rent']);
                                $total_products[$i]['product_details'][$count]['offered_rent_total'] = json_decode($lead_info['offered_rent_total']);
                                $total_products[$i]['product_details'][$count]['deposit'] = json_decode($lead_info['deposite']);
                                $total_products[$i]['product_details'][$count]['deposit_total'] = json_decode($lead_info['deposite_total']);
                                $total_products[$i]['product_details'][$count]['sale_rental'] = json_decode($lead_info['sale_rental']);
                                $total_products[$i]['product_details'][$count]['transport'] = json_decode($lead_info['transport']);
                            }
                        }
                    }
                    else
                    {
                        array_push($unq_cust,$customer_id);
                        $count = count($total_products);
                        $total_products[$count]['customer_id'] = $lead_info['customer_id'];
                        $total_products[$count]['product_details'][0]['quantity'] = json_decode($lead_info['equipment_qty']);
                        $total_products[$count]['product_details'][0]['offered_rent'] = json_decode($lead_info['offered_rent']);
                        $total_products[$count]['product_details'][0]['offered_rent_total'] = json_decode($lead_info['offered_rent_total']);
                        $total_products[$count]['product_details'][0]['deposit'] = json_decode($lead_info['deposite']);
                        $total_products[$count]['product_details'][0]['deposit_total'] = json_decode($lead_info['deposite_total']);
                        $total_products[$count]['product_details'][0]['transport'] = json_decode($lead_info['transport']);
                        $total_products[$count]['product_details'][0]['sale_rental'] = json_decode($lead_info['sale_rental']);
                    }
                }
                //print_r($total_products);
                $product_count = 0;
                $total_rent_product = 0;
                $total_sale_product = 0;
                $total_rent_amt = 0;
                $total_sale_amt = 0;
                $total_deposit = 0;
                $total_transport = 0;
                foreach ($total_products as $total_p)
                {
                    $product_details = $total_p['product_details'];
                    for ($i=0; $i <count($product_details) ; $i++) { 
                        for ($j=0; $j <count($product_details[$i]['quantity']) ; $j++) { 
                            
                            if($product_details[$i]['sale_rental'][$j]=='Rental')
                            {
                                $total_rent_amt += $product_details[$i]['offered_rent_total'][$j];    
                                $total_rent_product += $product_details[$i]['quantity'][$j];    
                            }
                            elseif($product_details[$i]['sale_rental'][$j]=='Sale')
                            {
                                $total_sale_amt += $product_details[$i]['offered_rent_total'][$j];    
                                $total_sale_product += $product_details[$i]['quantity'][$j];   
                            }
                            $total_transport += $product_details[$i]['transport'][$j];
                            $product_count += $product_details[$i]['quantity'][$j];
                        }
                    }
                }
                $data['product_count'] = $product_count;
                $data['total_customer'] = count($unq_cust);
                $data['total_amount'] = $total_rent_amt+$total_sale_amt+$total_transport;
                $data['total_rent_product'] = $total_rent_product;
                $data['total_sale_product'] = $total_sale_product;
                $data['total_sale_amt'] = $total_sale_amt;
                $data['total_rent_amt'] = $total_rent_amt;
                $data['total_deposit'] = $total_deposit;
                $data['total_transport'] = $total_transport;
                echo "<script>localStorage['filtered']='all';</script>";
                return view('OrderManagement/viewAllLeads',$data);
            }
            

            if(session('role') == "superuser")
            {
                $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress' ) AND $whereClause AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
            }
            elseif(session('role') == "admin")
            {
                $user_city = session('user_city');
                $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated')  AND customer_details.citygroup='$user_city' AND $whereClause AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
            }
            elseif(session('role') == "user")
            {
                $user_city = session('user_city');
                // $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress' ) AND $whereClause AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
                $user_id = session('user_id');
                // $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress' ) AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
                $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress' ) AND $whereClause AND leads.lead_owner = $user_id AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
            }
            //echo "SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.lead_status = 'Work In Process' AND $whereClause AND ORDER BY leads.creation_date DESC";
                $data['lead_details'] = json_decode(json_encode($leads), true);
                for ($i=0; $i < count($data['lead_details']); $i++) 
                { 
                $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
                $equipement_details = array();
                for ($j=0; $j <count($product); $j++) 
                { 
                    $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
                    $product_details = json_decode(json_encode($product_details), true);
                    array_push($equipement_details,$product_details[0]['product_name']);
                }
                $equipements = json_encode($equipement_details);
                $data['lead_details'][$i]['equipment_requirement'] = $equipements;
                }

                //total status
                $unq_cust = array();
                $total_products = array();
                foreach($data['lead_details'] as $key => $lead_info) 
                {
                    $customer_id = $lead_info['customer_id'];
                    if(in_array($customer_id,$unq_cust))
                    {
                        for($i=0; $i<count($total_products); $i++)
                        {
                            if($total_products[$i]['customer_id'] == $lead_info['customer_id'])
                            {
                                $count = count($total_products[$i]['product_details']);
                                $total_products[$i]['product_details'][$count]['quantity'] = json_decode($lead_info['equipment_qty']);
                                $total_products[$i]['product_details'][$count]['offered_rent'] = json_decode($lead_info['offered_rent']);
                                $total_products[$i]['product_details'][$count]['offered_rent_total'] = json_decode($lead_info['offered_rent_total']);
                                $total_products[$i]['product_details'][$count]['deposit'] = json_decode($lead_info['deposite']);
                                $total_products[$i]['product_details'][$count]['deposit_total'] = json_decode($lead_info['deposite_total']);
                                $total_products[$i]['product_details'][$count]['sale_rental'] = json_decode($lead_info['sale_rental']);
                                $total_products[$i]['product_details'][$count]['transport'] = json_decode($lead_info['transport']);
                            }
                        }
                    }
                    else
                    {
                        array_push($unq_cust,$customer_id);
                        $count = count($total_products);
                        $total_products[$count]['customer_id'] = $lead_info['customer_id'];
                        $total_products[$count]['product_details'][0]['quantity'] = json_decode($lead_info['equipment_qty']);
                        $total_products[$count]['product_details'][0]['offered_rent'] = json_decode($lead_info['offered_rent']);
                        $total_products[$count]['product_details'][0]['offered_rent_total'] = json_decode($lead_info['offered_rent_total']);
                        $total_products[$count]['product_details'][0]['deposit'] = json_decode($lead_info['deposite']);
                        $total_products[$count]['product_details'][0]['deposit_total'] = json_decode($lead_info['deposite_total']);
                        $total_products[$count]['product_details'][0]['transport'] = json_decode($lead_info['transport']);
                        $total_products[$count]['product_details'][0]['sale_rental'] = json_decode($lead_info['sale_rental']);
                    }
                }
                //print_r($total_products);
                $product_count = 0;
                $total_rent_product = 0;
                $total_sale_product = 0;
                $total_rent_amt = 0;
                $total_sale_amt = 0;
                $total_deposit = 0;
                $total_transport = 0;
                foreach ($total_products as $total_p)
                {
                    $product_details = $total_p['product_details'];
                    for ($i=0; $i <count($product_details) ; $i++) { 
                        for ($j=0; $j <count($product_details[$i]['quantity']) ; $j++) { 
                            
                            if($product_details[$i]['sale_rental'][$j]=='Rental')
                            {
                                $total_rent_amt += $product_details[$i]['offered_rent_total'][$j];    
                                $total_rent_product += $product_details[$i]['quantity'][$j];    
                            }
                            elseif($product_details[$i]['sale_rental'][$j]=='Sale')
                            {
                                $total_sale_amt += $product_details[$i]['offered_rent_total'][$j];    
                                $total_sale_product += $product_details[$i]['quantity'][$j];   
                            }
                            $total_transport += $product_details[$i]['transport'][$j];
                            $product_count += $product_details[$i]['quantity'][$j];
                        }
                    }
                }
                $data['product_count'] = $product_count;
                $data['total_customer'] = count($unq_cust);
                $data['total_amount'] = $total_rent_amt+$total_sale_amt+$total_transport;
                $data['total_rent_product'] = $total_rent_product;
                $data['total_sale_product'] = $total_sale_product;
                $data['total_sale_amt'] = $total_sale_amt;
                $data['total_rent_amt'] = $total_rent_amt;
                $data['total_deposit'] = $total_deposit;
                $data['total_transport'] = $total_transport;
                return view('OrderManagement/viewAllLeads',$data);
        }
        public function filterOrderLeadsDWS()
        {
            if($_SERVER['REQUEST_METHOD']=='POST')
            {
                
                
                $start_date = date('Y-m-d',strtotime($_POST['start_date']));
                $end_date = date('Y-m-d',strtotime($_POST['end_date']));
                $data['start_date'] = $start_date;
                $data['end_date'] = $end_date;
                $whereClause = "leads.creation_date BETWEEN '$start_date' AND '$end_date'";

                if(session('role') == "superuser")
                {
                    $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress' ) AND $whereClause AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
                }
                elseif(session('role') == "admin")
                {
                    $user_city = session('user_city');
                    $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated')  AND customer_details.citygroup='$user_city' AND $whereClause AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
                }
                elseif(session('role') == "user")
                {
                    $user_city = session('user_city');
                    $user_id = session('user_id');
                    $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress' ) AND $whereClause AND leads.lead_owner = $user_id AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
                }
                //echo "SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.lead_status = 'Work In Process' AND $whereClause AND ORDER BY leads.creation_date DESC";
                    $data['lead_details'] = json_decode(json_encode($leads), true);
                    for ($i=0; $i < count($data['lead_details']); $i++) 
                    { 
                    $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
                    $equipement_details = array();
                    for ($j=0; $j <count($product); $j++) 
                    { 
                        $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
                        $product_details = json_decode(json_encode($product_details), true);
                        array_push($equipement_details,$product_details[0]['product_name']);
                    }
                    $equipements = json_encode($equipement_details);
                    $data['lead_details'][$i]['equipment_requirement'] = $equipements;
                    }
                    $unq_cust = array();
                $total_products = array();
                foreach($data['lead_details'] as $key => $lead_info) 
                {
                    $customer_id = $lead_info['customer_id'];
                    if(in_array($customer_id,$unq_cust))
                    {
                        for($i=0; $i<count($total_products); $i++)
                        {
                            if($total_products[$i]['customer_id'] == $lead_info['customer_id'])
                            {
                                $count = count($total_products[$i]['product_details']);
                                $total_products[$i]['product_details'][$count]['quantity'] = json_decode($lead_info['equipment_qty']);
                                $total_products[$i]['product_details'][$count]['offered_rent'] = json_decode($lead_info['offered_rent']);
                                $total_products[$i]['product_details'][$count]['offered_rent_total'] = json_decode($lead_info['offered_rent_total']);
                                $total_products[$i]['product_details'][$count]['deposit'] = json_decode($lead_info['deposite']);
                                $total_products[$i]['product_details'][$count]['deposit_total'] = json_decode($lead_info['deposite_total']);
                                $total_products[$i]['product_details'][$count]['sale_rental'] = json_decode($lead_info['sale_rental']);
                                $total_products[$i]['product_details'][$count]['transport'] = json_decode($lead_info['transport']);
                            }
                        }
                    }
                    else
                    {
                        array_push($unq_cust,$customer_id);
                        $count = count($total_products);
                        $total_products[$count]['customer_id'] = $lead_info['customer_id'];
                        $total_products[$count]['product_details'][0]['quantity'] = json_decode($lead_info['equipment_qty']);
                        $total_products[$count]['product_details'][0]['offered_rent'] = json_decode($lead_info['offered_rent']);
                        $total_products[$count]['product_details'][0]['offered_rent_total'] = json_decode($lead_info['offered_rent_total']);
                        $total_products[$count]['product_details'][0]['deposit'] = json_decode($lead_info['deposite']);
                        $total_products[$count]['product_details'][0]['deposit_total'] = json_decode($lead_info['deposite_total']);
                        $total_products[$count]['product_details'][0]['transport'] = json_decode($lead_info['transport']);
                        $total_products[$count]['product_details'][0]['sale_rental'] = json_decode($lead_info['sale_rental']);
                    }
                }
                //print_r($total_products);
                $product_count = 0;
                $total_rent_product = 0;
                $total_sale_product = 0;
                $total_rent_amt = 0;
                $total_sale_amt = 0;
                $total_deposit = 0;
                $total_transport = 0;
                // foreach ($total_products as $total_p)
                // {
                //     $product_details = $total_p['product_details'];
                //     for ($i=0; $i <count($product_details) ; $i++) { 
                //         for ($j=0; $j <count($product_details[$i]['quantity']) ; $j++) { 
                            
                //             if($product_details[$i]['sale_rental'][$j]=='Rental')
                //             {
                //                 $total_rent_amt += $product_details[$i]['offered_rent_total'][$j];    
                //                 $total_rent_product += $product_details[$i]['quantity'][$j];    
                //             }
                //             elseif($product_details[$i]['sale_rental'][$j]=='Sale')
                //             {
                //                 $total_sale_amt += $product_details[$i]['offered_rent_total'][$j];    
                //                 $total_sale_product += $product_details[$i]['quantity'][$j];   
                //             }
                //             $total_transport += $product_details[$i]['transport'][$j];
                //             $total_deposit += $product_details[$i]['deposit_total'][$j];
                //             $product_count += $product_details[$i]['quantity'][$j];
                //         }
                //     }
                // }
                foreach($data['lead_details'] as $leads)
                {
                    $qty = json_decode($leads['equipment_qty']);
                    $sale_rental = json_decode($leads['sale_rental']);
                    $rent_total = json_decode($leads['offered_rent_total']);
                    $deposit_total = json_decode($leads['deposite_total']);
                    $transport = json_decode($leads['transport']);
                    for ($j=0; $j <count($qty) ; $j++) 
                    {
                            
                        if($sale_rental[$j]=='Rental')
                        {
                            $total_rent_amt += $rent_total[$j];    
                            $total_rent_product += $qty[$j];    
                            $total_deposit += $deposit_total[$j];
                        }
                        elseif($sale_rental[$j]=='Sale')
                        {
                            $total_sale_amt += $rent_total[$j];    
                            $total_sale_product += $qty[$j];    
                        }
                        $total_transport += $transport[$j];
                        $product_count += $qty[$j];
                    }
                }
                $data['product_count'] = $product_count;
                $data['total_customer'] = count($unq_cust);
                $data['total_amount'] = $total_rent_amt+$total_sale_amt+$total_transport+$total_deposit;
                $data['total_rent_product'] = $total_rent_product;
                $data['total_sale_product'] = $total_sale_product;
                $data['total_sale_amt'] = $total_sale_amt;
                $data['total_rent_amt'] = $total_rent_amt;
                $data['total_deposit'] = $total_deposit;
                $data['total_transport'] = $total_transport;
                return view('OrderManagement/viewAllLeads',$data);
            }
        }
        //-----filter order lead-------//
        public function filterPendingAssignment($filter_by)
        {
            if($filter_by =='today')
            {
                $date = date('Y-m-d');
                $data['start_date'] = $date;
                $data['end_date'] = $date;
                $whereClause = "leads.creation_date = '$date'";
                echo "<script>localStorage['filtered']='today';</script>";
            }
            elseif($filter_by =='yesterday')
            {
                $prevDate = date('Y-m-d',strtotime("-1 days"));
                $data['start_date'] = $prevDate;
                $data['end_date'] = date('Y-m-d');
                $whereClause = "leads.creation_date = '$prevDate'";
                echo "<script>localStorage['filtered']='yesterday';</script>";
            }
            elseif($filter_by =='past_3_days')
            {
                $past_three_days = date('Y-m-d',strtotime("-2 days"));
                $data['start_date'] = $past_three_days;
                $data['end_date'] = date('Y-m-d');
                $whereClause = "leads.creation_date >= '$past_three_days'";
                echo "<script>localStorage['filtered']='past_3_days';</script>";
            }
            elseif($filter_by =='week')
            {
                $past_three_days = date('Y-m-d',strtotime("-7 days"));
                $data['start_date'] = $past_three_days;
                $data['end_date'] = date('Y-m-d');
                $whereClause = "leads.creation_date >= '$past_three_days'";
                echo "<script>localStorage['filtered']='week';</script>";
            }
            elseif($filter_by =='month')
            {
                $month = date('m-Y');
                $start_date_temp = '01-'.$month;
                $start_date = date('Y-m-d',strtotime($start_date_temp));
                $end_date_temp = '31-'.$month;
                $end_date = date('Y-m-d',strtotime($end_date_temp));
                $data['start_date'] = $start_date;
                $data['end_date'] = $end_date;
                $whereClause = "leads.creation_date BETWEEN '$start_date' AND '$end_date'";
            }
            elseif($filter_by == 'all')
            {   if(session('role') == "superuser")
                {
                    $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
                }
                elseif(session('role') == "admin")
                {
                    $user_city = session('user_city');
                    $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' AND customer_details.citygroup='$user_city' AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
                }
                elseif(session('role') == "user")
                {
                    $user_city = session('user_city');
                    $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' AND customer_details.citygroup='$user_city' AND leads.lead_owner = user.id ORDER BY leads.creation_date DESC");
                }
                
        
                    $data['lead_details'] = json_decode(json_encode($leads), true);
                    for ($i=0; $i < count($data['lead_details']); $i++) 
                    { 
                        $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
                        $equipement_details = array();
                        for ($j=0; $j <count($product); $j++) 
                        { 
                            $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
                            $product_details = json_decode(json_encode($product_details), true);
                            array_push($equipement_details,$product_details[0]['product_name']);
                        }
                        $equipements = json_encode($equipement_details);
                        $data['lead_details'][$i]['equipment_requirement'] = $equipements;
                    }
                
                echo "<script>localStorage['filtered']='all';</script>";
                $Countlead = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' ORDER BY leads.creation_date DESC");
                $count1 = count(json_decode(json_encode($Countlead),true));
                $data['count1'] = $count1;
                return view('OrderManagement/pendingAssignment',$data);
            }
            

            if(session('role') == "superuser")
            {
                $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' AND $whereClause ORDER BY leads.creation_date DESC");
            }
            elseif(session('role') == "admin")
            {
                $user_city = session('user_city');
                $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' AND customer_details.citygroup='$user_city' AND $whereClause ORDER BY leads.creation_date DESC");
            }
            elseif(session('role') == "user")
            {
                $user_city = session('user_city');
                $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' AND customer_details.citygroup='$user_city' AND $whereClause ORDER BY leads.creation_date DESC");
            }
            //echo "SELECT * FROM leads,customer_details WHERE leads.lead_status = 'Work In Process' AND $whereClause AND ORDER BY leads.creation_date DESC";
                $data['lead_details'] = json_decode(json_encode($leads), true);
                for ($i=0; $i < count($data['lead_details']); $i++) 
                { 
                $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
                $equipement_details = array();
                for ($j=0; $j <count($product); $j++) 
                { 
                    $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
                    $product_details = json_decode(json_encode($product_details), true);
                    array_push($equipement_details,$product_details[0]['product_name']);
                }
                $equipements = json_encode($equipement_details);
                $data['lead_details'][$i]['equipment_requirement'] = $equipements;
                }
                $Countlead = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' ORDER BY leads.creation_date DESC");
                $count1 = count(json_decode(json_encode($Countlead),true));
                $data['count1'] = $count1;
                return view('OrderManagement/pendingAssignment',$data);
        }
        public function filterPendingAssignmentDWS()
        {
            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $start_date = $_POST['start_date'];
                $end_date = $_POST['end_date'];
                $data['start_date'] = $start_date;
                $data['end_date'] = $end_date;
                $whereClause = "leads.creation_date BETWEEN '$start_date' AND '$end_date'";

                if(session('role') == "superuser")
                {
                    $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' AND $whereClause ORDER BY leads.creation_date DESC");
                }
                elseif(session('role') == "admin")
                {
                    $user_city = session('user_city');
                    $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' AND customer_details.citygroup='$user_city' AND $whereClause ORDER BY leads.creation_date DESC");
                }
                elseif(session('role') == "user")
                {
                    $user_city = session('user_city');
                    $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' AND customer_details.citygroup='$user_city' AND $whereClause ORDER BY leads.creation_date DESC");
                }
                //echo "SELECT * FROM leads,customer_details WHERE leads.lead_status = 'Work In Process' AND $whereClause AND ORDER BY leads.creation_date DESC";
                $data['lead_details'] = json_decode(json_encode($leads), true);
                for ($i=0; $i < count($data['lead_details']); $i++) 
                { 
                $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
                $equipement_details = array();
                for ($j=0; $j <count($product); $j++) 
                { 
                    $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
                    $product_details = json_decode(json_encode($product_details), true);
                    array_push($equipement_details,$product_details[0]['product_name']);
                }
                $equipements = json_encode($equipement_details);
                $data['lead_details'][$i]['equipment_requirement'] = $equipements;
                }
                $Countlead = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' ORDER BY leads.creation_date DESC");
                $count1 = count(json_decode(json_encode($Countlead),true));
                $data['count1'] = $count1;
                return view('OrderManagement/pendingAssignment',$data);
            }
        }
        public function pendingAssignmentsNotify()
        {
            $date = date('Y-m-d',strtotime('-7 days'));
            if(session('role') == "superuser")
            {
                $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' AND leads.creation_date>='$date' ORDER BY leads.creation_date DESC");
                // $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' ORDER BY leads.creation_date DESC");
            }
            elseif(session('role') == "admin")
            {
                $user_city = session('user_city');
                $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' AND customer_details.citygroup='$user_city' AND leads.creation_date>='$date' ORDER BY leads.creation_date DESC");
                // $leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' AND customer_details.citygroup='$user_city' ORDER BY leads.creation_date DESC");
            }
            $data['leads'] = json_decode(json_encode($leads), true);
            $leads = count($data['leads']);
            echo $leads;
        }
        /*Assign Vendor*/
        public function assign_vendor()
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
            if($_SERVER['REQUEST_METHOD']=='GET')
            {
                $id = $_GET['lead_id'];
                $cust_id = $_GET['customer_id'];
                DB::enableQueryLog();
                $lead_details = DB::select("SELECT * FROM leads,customer_details WHERE leads.id=$id AND customer_details.cust_id=$cust_id");
                $data['lead_details'] = json_decode(json_encode($lead_details), true);

                $product = json_decode($data['lead_details'][0]['equipment_requirement']);
                $equipment_id = $data['lead_details'][0]['equipment_requirement'];
                
                $equipement_details = array();
                $equipement_rent = array();
                for ($j=0; $j <count($product); $j++) 
                { 
                    $product_details = DB::select("SELECT product_name,product_rent FROM products WHERE id = $product[$j]");
                    $product_details = json_decode(json_encode($product_details), true);
                    array_push($equipement_details,$product_details[0]['product_name']);
                    array_push($equipement_rent,$product_details[0]['product_rent']);
                }
                $equipements = json_encode($equipement_details);
                $equipements_rent = json_encode($equipement_rent);
                $data['lead_details'][0]['equipment_requirement'] = $equipements;
                $data['lead_details'][0]['equipment_id'] = $equipment_id;
                $data['lead_details'][0]['equipments_rent'] = $equipements_rent;
                $vendor_details = DB::select("SELECT * FROM vendor_details");
                $data['vendor_details'] = json_decode(json_encode($vendor_details), true);
                // dd(DB::getQueryLog());
                return view('OrderManagement/assign_vendor_view',$data);
            }
        }
        public function assign_vendor_exp()
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
            if($_SERVER['REQUEST_METHOD']=='POST')
            {
                $lead_id = $_POST['lead_id'];
                $customer_id = $_POST['customer_id'];
                
                $lead_details = DB::select("SELECT * FROM leads,customer_details WHERE leads.id=$lead_id AND leads.customer_id = customer_details.cust_id AND customer_details.cust_id=$customer_id");
                $data['lead_details'] = json_decode(json_encode($lead_details), true);

                $product = json_decode($data['lead_details'][0]['equipment_requirement']);
                $equipment_id = $data['lead_details'][0]['equipment_requirement'];
                
                $equipement_details = array();
                $equipement_rent = array();
                for ($j=0; $j <count($product); $j++) 
                { 
                    $product_details = DB::select("SELECT product_name,product_rent FROM products WHERE id = $product[$j]");
                    $product_details = json_decode(json_encode($product_details), true);
                    array_push($equipement_details,$product_details[0]['product_name']);
                    array_push($equipement_rent,$product_details[0]['product_rent']);
                }
                $equipements = json_encode($equipement_details);
                $equipements_rent = json_encode($equipement_rent);
                $data['lead_details'][0]['equipment_requirement'] = $equipements;
                $data['lead_details'][0]['equipment_id'] = $equipment_id;
                $data['lead_details'][0]['equipments_rent'] = $equipements_rent;
                $vendor_details = DB::select("SELECT * FROM vendor_details"  );
                $data['vendor_details'] = json_decode(json_encode($vendor_details), true);
                echo json_encode($data);
                // return view('OrderManagement/assign_vendor_qty',$data);
            }
        }
        public function assign_vendor_byscript($customer_id,$lead_id)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
                $id = $lead_id;
                $cust_id = $customer_id;
                
                $lead_details = DB::select("SELECT * FROM leads,customer_details WHERE leads.id=$id AND customer_details.cust_id=$cust_id");
                $data['lead_details'] = json_decode(json_encode($lead_details), true);

                $product = json_decode($data['lead_details'][0]['equipment_requirement']);
                $equipment_id = $data['lead_details'][0]['equipment_requirement'];
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

                $vendor_details = DB::select("SELECT * FROM vendor_details"  );
                $data['vendor_details'] = json_decode(json_encode($vendor_details), true);
                return view('OrderManagement/assign_vendor',$data);      
        }
        //------Individual vendor----//
        public function individual_vendor($equipment)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
            if($_SERVER['REQUEST_METHOD']=='GET')
            {
                $vendor_products = DB::select("SELECT * FROM vendor_products,vendor_details WHERE vendor_products.product_id= '$equipment' AND vendor_details.id = vendor_products.vendor_id ");
                $data['vendor_products'] = json_decode(json_encode($vendor_products), true);   
                $data['vendor_products'][0]['sale_rental']=$sale_rental;
                
                if(isset($data['vendor_products']) && $data['vendor_products'] != NULL)
                {
                    $json = $data['vendor_products'];
                    
                    //$json= array('vendor_id' => $data['vendor_products'][0]['vendor_id'], 'registered_name'=>$data['vendor_products'][0]['registered_name'] ,'warehouse_id'=>$data['vendor_warehouse'][0]['id']);	
                }
                else
                {
                    $json= array('vendor_id' => null, 'registered_name'=>null ,'warehouse_id'=>null);	
                }            
                $jsonstring = json_encode($json);
                    echo $jsonstring;
            } 
        }
        public function select_vendor($slct_vdr_id,$equipment)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
            if($_SERVER['REQUEST_METHOD']=='GET')
            {
                $vendor_warehouse = DB::select("SELECT * FROM vendor_products,vendor_warehouse WHERE vendor_products.product_id = $equipment AND vendor_products.vendor_id AND vendor_products.warehouse_id = vendor_warehouse.id");
                $data['vendor_warehouse'] = json_decode(json_encode($vendor_warehouse),true);
                $jsonstring = json_encode($data['vendor_warehouse']);
                echo $jsonstring;
            }
            // if($_SERVER['REQUEST_METHOD']=='GET')
            // {
            //     //echo $equipment;
            //     $vendor_products = DB::select("SELECT * FROM vendor_products WHERE vendor_id = '$slct_vdr_id' and product_id ='$equipment'");
            //     $data['vendor_products'] = json_decode(json_encode($vendor_products), true);            
            //     // print_r($data['vendor_products']);
            //     if(isset($data['vendor_products']) && $data['vendor_products'] != NULL)
            //     {
            //         $warehouse_id = $data['vendor_products'][0]['warehouse_id'];                
            //         $vendor_warehouse = DB::select("SELECT * FROM vendor_warehouse WHERE id = $warehouse_id");
            //         $data['vendor_warehouse'] = json_decode(json_encode($vendor_warehouse), true);
                    
            //         $json= array('id' => $data['vendor_products'][0]['id'] , 'vendor_id' => $data['vendor_products'][0]['vendor_id'], 'product_id'=>$data['vendor_products'][0]['product_id'] , 'product_details' => $data['vendor_products'][0]['product_brand'] , 'product_price' => $data['vendor_products'][0]['product_rent_approved'], 'warehouse_details' => $data['vendor_warehouse'][0]['wh_landmark'].','.$data['vendor_warehouse'][0]['wh_city'],'warehouse_id'=>$data['vendor_warehouse'][0]['id'], 'virtual_id' => $data['vendor_products'][0]['virtual_id']);	
            //     }
            //     else
            //     {
            //         $json= array('id' => null , 'vendor_id' => null, 'product_id'=>null , 'product_details' => null , 'product_price' => null, 'warehouse_details' => null,'warehouse_id'=>null, 'virtual_id' => null);	
            //     }            
            //     $jsonstring = json_encode($json);
            //     echo $jsonstring;
            // } 
        }
        //-------------------added new select vendor controller----------------//
        public function get_vendor()
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
            
            $eq_id =  json_decode($_POST['equipments']);
            $assign = $_POST['assign'];
            $count = 0;
            $data_count = count($eq_id);
            $data_count = $data_count-1;
            $vdr = array();
            for ($i=0; $i <$data_count ; $i++) 
            { 
                $equip = $eq_id[$i];
                $vendor_id = DB::select("SELECT vendor_id FROM vendor_products WHERE product_id=$equip");
                $data['vendor'.$i] = json_decode(json_encode($vendor_id), true); 
                $count = count($data['vendor'.$i]);
                $temp_array = $data['vendor'.$i];
                for($j=0; $j<$count; $j++)
                {
                    $data['vendor_dummy'][$i][$j] = $temp_array[$j]['vendor_id'];
                }
                array_push($vdr,$data['vendor_dummy'][$i]);
            }
            $temp_array_1 =array();
            for($i=0; $i<count($vdr); $i++)
            {
                $temp_array_1= array_merge($temp_array_1,$vdr[$i]);
            }
            $result_values = array_count_values($temp_array_1);
            $result_keys = array_keys($result_values,"$data_count");
            $vdr_details = DB::select('SELECT id,registered_name FROM vendor_details WHERE id IN (' . implode(',', array_map('intval', $result_keys)) . ') ' );
            $jsonstring = json_encode($vdr_details);
            echo $jsonstring;
        }
        public function select_vendor_all()
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
            $equipments = request()->get('equipments');
            $vendor_id = request()->get('vendor_id');
            $equipments = json_decode($equipments);
            $count = count($equipments);
            $equipment_details = array();
            $equipment_price = array();
            $equipment_warehouse = array();
            $vendor_warehouse_id = array();
            for($i=0; $i<$count-1; $i++)
            {
                $product = $equipments[$i];
                $vendor_products = DB::select("SELECT * FROM vendor_products WHERE vendor_id = $vendor_id AND product_id= '$product' ");
                $data['vendor_products'] = json_decode(json_encode($vendor_products), true);
                $warehouse_id = $data['vendor_products'][0]['warehouse_id'];
                $vendor_warehouse = DB::select("SELECT * FROM vendor_warehouse WHERE id = $warehouse_id");
                $data['vendor_warehouse'] = json_decode(json_encode($vendor_warehouse), true);
                $vendor_product_id[$i] = $data['vendor_products'][0]['id'];
                $equipment_price[$i] = $data['vendor_products'][0]['product_rent_approved'];
                $equipment_details[$i] = $data['vendor_products'][0]['product_brand'];
                $equipment_warehouse[$i] = $data['vendor_warehouse'][0]['wh_landmark'].','.$data['vendor_warehouse'][0]['wh_city'];
                $vendor_warehouse_id[$i] = $data['vendor_warehouse'][0]['id'];
            }
            $data['vendor_product_details']['vendor_product_id']=$vendor_product_id;
            $data['vendor_product_details']['product_rent']=$equipment_price;
            $data['vendor_product_details']['product_details']=$equipment_details;
            $data['vendor_product_details']['warehouse_details']=$equipment_warehouse;
            $data['vendor_product_details']['warehouse_id']=$vendor_warehouse_id;
            $jsonstring = json_encode($data['vendor_product_details']);
            echo $jsonstring;
        }
        public function generate_order1(Request $request)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            if($_SERVER['REQUEST_METHOD']=='POST')
            {
                print_r($_POST);
            }
        }
        public function generate_order(Request $request)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
            $request_dump = "";
            DB::beginTransaction();
            try
            {
                if($_SERVER['REQUEST_METHOD']=='POST')
                {
                    $del_orders = new DelOrders();
                    $order_details = new OrderDetails();
                    $sale_vendor_products = new sale_vendor_products();
                    $leads_log = new leads_log();
                    //print_r($_POST);
                    if ($_POST['submit']=='submit') 
                    {
                        $total_amt=array_sum($_POST['offered_rent'])+array_Sum($_POST['deposite'])+array_Sum($_POST['transport']);
                        $customer_name = $_POST['customer_name'];
                            $customer_gender = $_POST['customer_gender'];
                        $customer_address = $_POST['customer_address'];
                        $customer_id = $_POST['customer_id'];
                        $lead_id = $_POST['lead_id'];
                        $location = $_POST['location'];
                        // $vendor_id = $_POST['vendor'];
                        $product_rent = $_POST['offered_rent'];
                        $product_rent_total = $_POST['offered_rent_total'];
                        $product_deposite = $_POST['deposite'];
                        $transport = $_POST['transport'];
                        $fulldetails = $customer_name.','.$customer_address;
                        $date = date('Y-m-d');
                        $DelDate= date('d-m-Y');
                        $temp_order_details = array();
                        $temp_array =array();
                        $vendor_product_id = $_POST['vendor_product_id'];
                        // $vendor_product_details_id = $_POST['vendor_product_details_id'];
                        $vendor_product_details_id = $_POST['vendor_product_details_id'];
                        $equipment_id = $_POST['req_eq_hidden'];
                        $equipment_qty = $_POST['eq_quantity_hidden'];
                        $vendor_id = $_POST['vendors'];
                        $warehouse_id = $_POST['warehouses'];
                        $brand_id = $_POST['brands'];
                        $batch_id = $_POST['batches'];
                        $serial_numbers = $_POST['serial_numbers'];
                        $sale_rental_hidden = $_POST['sale_rental_hidden'];
                        $created_at = date('Y-m-d H:i:s', strtotime('now'));
                        $pickup_date = date('Y-m-d',strtotime("+1 month"));
                        $username = session('username');
                        $temp_vendor_id = $vendor_id[0];
                        $order_id_array = array();
                        if($_POST['assign']=='Individual')
                        {
                            for($i=0; $i<count($vendor_id); $i++)
                            {
                                for($l=0; $l<$equipment_qty[$i]; $l++)
                                {
                                    if($l==0)
                                    {
                                        $temp_transport = $transport[$i];
                                        $temp_deposite = $product_deposite[$i];
                                    }
                                    else
                                    {
                                        $temp_deposite = 0;
                                        $temp_transport = 0;
                                    }
                                    //$count = count($temp_order_details);
                                    if(in_array($vendor_id[$i], $temp_array, TRUE))
                                    {
                                        $same_vendor_index_i = 0;
                                        $same_vendor_index_j = 0;
                                        for ($j=0; $j<count($temp_order_details); $j++)
                                        {
                                            for ($k=0; $k<count($temp_order_details[$j]); $k++)
                                            {
                                                if($temp_order_details[$j][$k]['vendor_id'] == $vendor_id[$i])
                                                {
                                                    $same_vendor_index_j = $j;
                                                    $same_vendor_index_k = $k+1;
                                                }
                                            }
                                        }
                                        // $count_first = count($temp_order_details);
                                        // $count_minus = $count_first-1;
                                        // $count = count($temp_order_details[$count_minus]);
                                        //$count = 0;
                                        $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['vendor_id'] = $vendor_id[$i];
                                        $temp_total_amt = $temp_order_details[$same_vendor_index_j][$same_vendor_index_k-1]['total_amt'];
                                        $temp_total_amt=(int)$temp_total_amt+(int)$product_rent[$i]+(int)$temp_deposite+(int)$temp_transport;
                                        // $temp_total_amt=(int)$temp_total_amt+(int)$product_rent_total[$i]+(int)$product_deposite[$i]+(int)$transport[$i];
                                        $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['total_amt'] = $temp_total_amt;
                                        $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['product_id'] = $equipment_id[$i];
                                        $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['vendor_product_id'] = $vendor_product_id[$i];
                                        $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['vendor_product_details_id'] = $vendor_product_details_id[$i];
                                        $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['brand_id'] = $brand_id[$i];
                                        if($sale_rental_hidden[$i] == "Sale")
                                        {
                                            $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['batch_id'] = "-";
                                        }
                                        else
                                        {
                                            $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['batch_id'] = $batch_id[$i];
                                        }
                                        // $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['batch_id'] = $batch_id[$i];
                                        $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['vendor_warehouse_id'] = $warehouse_id[$i];
                                        // $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['product_qty'] = $equipment_qty[$i];
                                        $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['product_qty'] = 1;
                                        $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['product_rent'] = $product_rent[$i];
                                        // $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['product_rent'] = $product_rent_total[$i];
                                        $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['product_deposite'] = $temp_deposite;
                                        $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['serial_numbers'] = $serial_numbers[$i];
                                        $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['transport'] = $temp_transport;
                                        $temp_order_details[$same_vendor_index_j][$same_vendor_index_k]['sale_rental_hidden'] = $sale_rental_hidden[$i];
                                    }
                                    else
                                    {
                                        $temp_total_amt = 0;
                                        array_push($temp_array,$vendor_id[$i]);
                                        //array_push($temp_order_details, $vendor_id[$i]);
                                        $temp_vendor_id = $vendor_id[$i];
                                        if(isset($temp_order_details[$temp_vendor_id]))
                                        {
                                            $count = count($temp_order_details[$temp_vendor_id]);
                                        }
                                        else
                                        {
                                            $count = 0;
                                        }
                                        $count_first = count($temp_order_details);
                                        $temp_order_details[$count_first][$count]['vendor_id'] = $vendor_id[$i];
                                        // $temp_total_amt=(int)$product_rent_total[$i]+(int)$product_deposite[$i]+(int)$transport[$i];
                                        $temp_total_amt=(int)$product_rent[$i]+(int)$temp_deposite+(int)$temp_transport;
                                        $temp_order_details[$count_first][$count]['total_amt'] = $temp_total_amt;
                                        $temp_order_details[$count_first][$count]['product_id'] = $equipment_id[$i];
                                        $temp_order_details[$count_first][$count]['vendor_product_id'] = $vendor_product_id[$i];
                                        $temp_order_details[$count_first][$count]['vendor_product_details_id'] = $vendor_product_details_id[$i];
                                        $temp_order_details[$count_first][$count]['brand_id'] = $brand_id[$i];
                                        if($sale_rental_hidden[$i] == "Sale")
                                        {
                                            $temp_order_details[$count_first][$count]['batch_id'] = "-";
                                        }
                                        else
                                        {
                                            $temp_order_details[$count_first][$count]['batch_id'] = $batch_id[$i];
                                        }
                                        // $temp_order_details[$count_first][$count]['batch_id'] = $batch_id[$i];
                                        $temp_order_details[$count_first][$count]['vendor_warehouse_id'] = $warehouse_id[$i];
                                        // $temp_order_details[$count_first][$count]['product_qty'] = $equipment_qty[$i];
                                        $temp_order_details[$count_first][$count]['product_qty'] = 1;
                                        $temp_order_details[$count_first][$count]['product_rent'] = $product_rent[$i];
                                        // $temp_order_details[$count_first][$count]['product_rent'] = $product_rent_total[$i];
                                        $temp_order_details[$count_first][$count]['product_deposite'] = $temp_deposite;
                                        $temp_order_details[$count_first][$count]['serial_numbers'] = $serial_numbers[$i];
                                        $temp_order_details[$count_first][$count]['transport'] = $temp_transport;
                                        $temp_order_details[$count_first][$count]['sale_rental_hidden'] = $sale_rental_hidden[$i];
                                    }
                                }
                            }
                            //print_r($temp_order_details);
                                    $leads_log_data = [
                                        'log_lead_id' => $lead_id,
                                        'log_lead_status' => 'Vendor Assigned',
                                        'log_date' => date('Y-m-d'),
                                        'log_time' => date('H:i:s'),
                                        'updated_by' => session('username')
                                    ];
                                    /**/ $leads_log->insert($leads_log_data);
                            foreach ($temp_order_details as $temp_order_detail)
                            {
                                $equipment_names = array();
                                foreach($temp_order_detail as $order_detail)
                                {
                                    $product_id = $order_detail['product_id'];
                                    $equipment_name = DB::select("SELECT product_name FROM products WHERE id = $product_id");
                                    $data['equipment_name'] = json_decode(json_encode($equipment_name), true);
                                    array_push($equipment_names,$data['equipment_name'][0]['product_name']);
                                }
                                $count = count($temp_order_detail);
                                $equip_name = implode(', ',$equipment_names);
                                $generate_order = [
                                    'status'=>'Pending',
                                    'lead_id'=>$lead_id,
                                    'vendor_id'=>$temp_order_detail[0]['vendor_id'],
                                    'deliverypickup'=>'Delivery',
                                    'DelDate'=>$DelDate,
                                    'location'=>$location,
                                    'shipping_first_name'=>$customer_name,
                                        'cust_gender'=>$customer_gender,
                                    'TotalAmt'=>$temp_order_detail[$count-1]['total_amt'],
                                    'PaymentMode'=>request()->get('payment_mode'),
                                    'mobileno'=>request()->get('mobile_no'),
                                    'DelAssignedTo'=>'Pending',
                                    'TravelMode'=>'Null',
                                    'order_approval_status'=>'Pending',
                                    'fulldetails'=>$fulldetails,
                                    'line_item_1'=>$equip_name
                                ];
                                //print_r($generate_order); 
                                /**/ $order_id = $del_orders->insertGetId($generate_order);
                                        $leads_log_data = [
                                            'log_lead_id' => $lead_id,
                                            'log_order_id' => $order_id,
                                            'log_order_lead_date' => date('Y-m-d').' '.date('H:i:s'),
                                            'log_order_type' => 'DO',
                                            'log_lead_status' => 'Order Generated',
                                            'log_date' => date('Y-m-d'),
                                            'log_time' => date('H:i:s'),
                                            'updated_by' => session('username')
                                        ];
                                        $leads_log->insert($leads_log_data);
                                        // /**/ $del_orders->insert($generate_order);
                                        // $order_id = DB::table('del_orders')->select('del_orders.order_id')->orderBy('del_orders.order_id','DESC')->first();
                                        // $order_id = $order_id->order_id;
                                        // $order_id = DB::table('del_orders')->insertGetId($generate_order);
                                        // $order_id = 0;
                                
                                for ($i=0; $i <count($temp_order_detail) ; $i++)
                                {
                                    $temp_vendor_product_id = $temp_order_detail[$i]['vendor_product_id'];
                                    $temp_vendor_product_details_id = $temp_order_detail[$i]['vendor_product_details_id'];
                                    $temp_eq_id = $temp_order_detail[$i]['product_id'];
                                    $temp_eq_qty = $temp_order_detail[$i]['product_qty'];
                                    $temp_vendor_id = $temp_order_detail[$i]['vendor_id'];
                                    $temp_warehouse_id = $temp_order_detail[$i]['vendor_warehouse_id'];
                                    $temp_product_rent = $temp_order_detail[$i]['product_rent'];
                                    $temp_product_brand = $temp_order_detail[$i]['brand_id'];
                                    $temp_product_batch = $temp_order_detail[$i]['batch_id'];
                                    $temp_product_deposite = $temp_order_detail[$i]['product_deposite'];
                                    $temp_serial_numbers = $temp_order_detail[$i]['serial_numbers'];
                                    $temp_transport = $temp_order_detail[$i]['transport'];
                                    $temp_sale_rental_hidden = $temp_order_detail[$i]['sale_rental_hidden'];
                                    $request_dump = "temp_vendor_product_id: ".$temp_vendor_product_id.
                                        "\n temp_vendor_product_details_id: ".$temp_vendor_product_details_id.
                                        "\n temp_eq_id: ".$temp_eq_id.
                                        "\n temp_eq_qty: ".$temp_eq_qty.
                                        "\n temp_vendor_id: ".$temp_vendor_id.
                                        "\n temp_warehouse_id: ".$temp_warehouse_id.
                                        "\n temp_product_rent: ".$temp_product_rent.
                                        "\n temp_product_brand: ".$temp_product_brand.
                                        "\n temp_product_batch: ".$temp_product_batch.
                                        "\n temp_product_deposite: ".$temp_product_deposite.
                                        "\n temp_serial_numbers: ".$temp_serial_numbers.
                                        "\n temp_transport: ".$temp_transport.
                                        "\n temp_sale_rental_hidden: ".$temp_sale_rental_hidden;
                                    if(in_array($order_id,$order_id_array))
                                    {
    
                                    }
                                    else
                                    {
                                        array_push($order_id_array,$order_id);
                                    }
                                    if($temp_sale_rental_hidden == "Sale")
                                    {
                                        $status = "Pending";
                                        $insertData = 
                                        [
                                            'order_id'=> $order_id,
                                            'vendor_id' => $temp_vendor_id,
                                            'product_id' => $temp_eq_id,
                                            'sale_price' => $temp_product_rent,
                                            'vendor_sale_price' => 0,
                                            'vendor_warehouse_id' => $temp_warehouse_id,
                                            'created_by' => session('username')
                                        ];
                                        $inserted = $sale_vendor_products->insert($insertData);
                                    }
                                    else
                                    {
                                        $status = "Pending";
                                    }
                                    if($temp_sale_rental_hidden == "Sale")
                                    {
                                        $temp_vendor_product_id = 0;
                                       
                                    }
                                    if($temp_sale_rental_hidden == 'Rental')
                                    {
                                        
                                        DB::enableQueryLog();
                                        $product_details = DB::select("SELECT * FROM vendor_product_details WHERE id = $temp_vendor_product_details_id");
                                        // dd(DB::getQueryLog());
                                        $product_details = json_decode(json_encode($product_details), true);
                                        // print_r($product_details);
    
                                        $temp_vendor_product_details_id = $product_details[0]['id'];
                                        $inventory_id = $product_details[0]['inventory_id'];
                                    }
                                    else
                                    {
                                        $temp_vendor_product_details_id = 0;
                                        $inventory_id = 0;
                                    }
                                    $insert_order = [
                                        'order_id'=> $order_id,
                                        'customer_id'=>$customer_id,
                                        'product_id'=>$temp_eq_id,
                                        'vendor_product_id'=>$temp_vendor_product_id,
                                        'vendor_id'=>$temp_vendor_id,
                                        'vendor_warehouse_id'=>$temp_warehouse_id,
                                        'product_brand'=>$temp_product_brand,
                                        'product_batch'=>$temp_product_batch,
                                        'product_qty'=>$temp_eq_qty,
                                        'product_rent'=>$temp_product_rent,
                                        'product_deposite'=>$temp_product_deposite,
                                        'transport'=>$temp_transport,
                                        'sale_rental' =>$temp_sale_rental_hidden,
                                        'vendor_product_details_id' => $temp_vendor_product_details_id,
                                        'unique_id'=> $inventory_id,
                                        'product_serial_nos' =>$temp_serial_numbers,
                                        'creation_date'=>$date,
                                        'pickup_date'=>$pickup_date,
                                        'status'=>$status,
                                        'created_at'=>$created_at,
                                        'created_by'=>$username,  
                                    ];
                                    $update_inventory_status = DB::update("UPDATE vendor_product_details SET availability_status = 1, current_location = 0 WHERE id = $temp_vendor_product_details_id");
                                    //print_r($insert_order);
                                    /**/ $order_details->insert($insert_order);
                                    // **********$update_qty = DB::update("UPDATE vendor_products SET product_quantity = product_quantity-$temp_eq_qty WHERE id=$temp_vendor_product_id");
                                    /**/ $update = DB::update("UPDATE leads SET lead_status = 'Vendor Assigned' WHERE id = $lead_id");
                                    // $leads_log = new leads_log();
                                    // $leads_log_data = [
                                    //     'log_lead_id' => $lead_id,
                                    //     'log_lead_status' => 'Vendor Assigned',
                                    //     'log_date' => date('Y-m-d'),
                                    //     'log_time' => date('H:i:s'),
                                    //     'updated_by' => session('username')
                                    // ];
                                    // $leads_log->insert($leads_log_data);
                                    // if($key == 1)
                                    // {
                                    //     $temp_vendor_id = null;
                                    // }
                                    $vendor_details = DB::select("SELECT * FROM vendor_details WHERE id=$temp_vendor_id");
                                    $data['vendor_details'] = json_decode(json_encode($vendor_details),true);
                                    $products = DB::select("SELECT * FROM products WHERE id=$temp_eq_id");
                                    $data['products'] = json_decode(json_encode($products),true);
                                    // session(['email' => $data['vendor_details'][0]['of_email']]);
                                    // $data = array('name'=>"'".$data['vendor_details'][0]['registered_name']."'",'product_name'=>"'".$data['products'][0]['product_name']."'");
                                    // Mail::send('vendorMail/pendingRentedProductMail', $data, function($message) 
                                    // {                
                                    //     $email_id = session('email');
                                    //     $message->to($email_id, 'Product Rental Request')->subject('Product Rental Request');
                                    //     $message->from('tempmailquali@gmail.com', 'Quali55Care');
                                    // });
                                }
                            }
                        }
                        else
                        {
                            $generate_order = [
                                'status'=>'Pending',
                                'lead_id'=>$lead_id,
                                'vendor_id'=>$vendor_id[0],
                                'deliverypickup'=>'Delivery',
                                'DelDate'=>$DelDate,
                                'shipping_first_name'=>$customer_name,
                                'TotalAmt'=>$total_amt,
                                'PaymentMode'=>request()->get('payment_mode'),
                                'mobileno'=>request()->get('mobile_no'),
                                'DelAssignedTo'=>'Pending',
                                'TravelMode'=>'Null',
                                'order_approval_status'=>'Pending',
                                'fulldetails'=>$fulldetails,
                            ]; 
                            //print_r($generate_order); 
                            $order_id = $del_orders->insertGetId($generate_order);
                            array_push($order_id_array,$order_id);
                            $update = DB::update("UPDATE leads SET lead_status = 'Vendor Assigned' WHERE id = $lead_id");
                            $leads_log = new leads_log();
                            $leads_log_data = [
                                'log_lead_id' => $lead_id,
                                'log_lead_status' => 'Vendor Assigned',
                                        'log_date' => date('Y-m-d'),
                                        'log_time' => date('H:i:s'),
                                        'updated_by' => session('username')
                                    ];
                                    $leads_log->insert($leads_log_data);
                                    $leads_log_data = [
                                        'log_lead_id' => $lead_id,
                                        'log_lead_status' => 'Order Generated',
                                        'log_order_lead_date' => date('Y-m-d').' '.date('H:i:s'),
                                        'log_order_id' => $order_id,
                                        'log_order_type' => 'DO',
                                'log_date' => date('Y-m-d'),
                                'log_time' => date('H:i:s'),
                                'updated_by' => session('username')
                            ];
                            $leads_log->insert($leads_log_data);
                            $vendor_product_id = $_POST['vendor_product_id'];
                            $equipment_id = $_POST['req_eq_hidden'];
                            $equipment_qty = $_POST['eq_quantity_hidden'];
                            $vendor_id = $_POST['vendors'];
                            $serial_numbers = $_POST['serial_numbers'];
                            $warehouse_id = $_POST['warehouses'];
                            $brand_id = $_POST['brands'];
                            $batch_id = $_POST['batches'];
                            $sale_rental_hidden = $_POST['sale_rental_hidden'];
                            //echo "asdas".$_POST['deposite'];
                            $date = date('Y-m-d H:i:m');
                            $created_at = date( 'Y-m-d H:i:s', strtotime( 'now' ) );
                            $pickup_date = date('Y-m-d',strtotime("+1 month"));
                            $username = session('username');
                            $temp_vendor_id = $vendor_id[0];
                            $temp_warehouse_id = $warehouse_id[0];
                            for ($i=0; $i <count($equipment_id) ; $i++)
                            {
                                $temp_vendor_product_id = $vendor_product_id[$i];
                                $temp_eq_id = $equipment_id[$i];
                                $temp_eq_qty = $equipment_qty[$i];
                                $temp_vendor_id = $vendor_id[0];
                                $temp_warehouse_id = $warehouse_id[0];
                                if($_POST['assign']=='Individual')
                                {
                                    $temp_vendor_id = $vendor_id[$i];
                                    $temp_warehouse_id = $warehouse_id[$i];
                                }
                                // $temp_warehouse_id = $warehouse_id[$i];
                                $temp_product_rent = $product_rent[$i];
                                $temp_product_brand = $brand_id[$i];
                                $temp_product_batch = $batch_id[$i];
                                $temp_product_deposite = $product_deposite[$i];
                                $temp_serial_numbers = $serial_numbers[$i];
                                $temp_transport = $transport[$i];
                                $temp_sale_rental_hidden = $sale_rental_hidden[$i];
                                $insert_order = [
                                    'order_id'=> $order_id,
                                    'customer_id'=>$customer_id,
                                    'product_id'=>$temp_eq_id,
                                    'vendor_product_id'=>$temp_vendor_product_id,
                                    'vendor_id'=>$temp_vendor_id,
                                    'vendor_warehouse_id'=>$temp_warehouse_id,
                                    'product_qty'=>$temp_eq_qty,
                                    'product_rent'=>$temp_product_rent,
                                    'product_brand'=>$temp_product_brand,
                                    'product_batch'=>$temp_product_batch,
                                    'product_deposite'=>$temp_product_deposite,
                                    'product_serial_nos'=>$temp_serial_numbers,
                                    'transport'=>$temp_transport,
                                    'sale_rental' => $temp_sale_rental_hidden,
                                    'creation_date'=>$date,
                                    'pickup_date'=>$pickup_date,
                                    'status'=>'Pending',
                                    'created_at'=>$created_at,
                                    'created_by'=>$username,  
                                ];
                                $order_details->insert($insert_order);
                                $vendor_details = DB::select("SELECT * FROM vendor_details WHERE id=$temp_vendor_id");
                                $data['vendor_details'] = json_decode(json_encode($vendor_details),true);
                                $products = DB::select("SELECT * FROM products WHERE id=$temp_eq_id");
                                $data['products'] = json_decode(json_encode($products),true);
                                // session(['email' => $data['vendor_details'][0]['of_email']]);
                                // $data = array('name'=>"'".$data['vendor_details'][0]['registered_name']."'",'product_name'=>"'".$data['products'][0]['product_name']."'");
                                // Mail::send('vendorMail/pendingRentedProductMail', $data, function($message) 
                                // {
                                //     $email_id = session('email');
                                //     $message->to($email_id, 'Product Rental Request')->subject('Product Rental Request');
                                //     $message->from('tempmailquali@gmail.com', 'Quali55Care');
                                // });
                            }
                        }
                        //print_r($order_id_array);
                        //-------change lead status to order_generated---//
                        $lead_status = DB::update("UPDATE leads SET lead_status='Order Generated' WHERE id=$lead_id");
                                // $leads_log_data = [
                                //     'log_lead_id' => $lead_id,
                                //     'log_lead_status' => 'Order Generated',
                                //     'log_date' => date('Y-m-d'),
                                //     'log_time' => date('H:i:s'),
                                //     'updated_by' => session('username')
                                // ];
                                // $leads_log->insert($leads_log_data);
                            //     return $order_id_array;
                            // });
                            // dd($order_id_array);
                        //session(['order_id' => $order_id_array]);   
                        $order_id = json_encode($order_id_array);
                        $order_id = base64_encode($order_id);
                        /**/ DB::commit();       
                        return redirect('/order_details/'.$order_id)->with('message','Order Assign Succesfully');     
                    }  
                    else
                    {
                    } 
                }
                
            }
            catch(Exception $ex)
            {
                DB::rollBack();
                $file = fopen(public_path().'/tempLogfile'.date('Y-m-d').'.txt','a');
                fwrite($file,date('Y-m-d')."Exception: ".$ex);
                fwrite($file,"request_data".$request_dump);
                fclose($file);
                return redirect()->back()->with('message','Something Went Wrong! Please Try Again or Contact Administrator.');
            }
        }
        public function order_details($order_id)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            $order_id = base64_decode($order_id);
            $order_id = json_decode($order_id, true);
            $temp_array = array();
            $temp_order_details = array();
            
            for($i=0; $i<count($order_id); $i++)
            {
                //$count = count($temp_order_details);
                $order_details = DB::select("SELECT order_details.*,
                                                    customer_details.*,
                                                    del_orders.*,
                                                    vendor_details.registered_name as vendor_name,
                                                    vendor_warehouse.wh_name as wh_name,
                                                    vendor_warehouse.wh_landmark as wh_landmark,
                                                    vendor_warehouse.wh_city as wh_city,
                                                    products.product_name as product_name
                                                    FROM order_details,del_orders,customer_details,products,vendor_details,vendor_warehouse
                                                    WHERE del_orders.order_id =$order_id[$i] 
                                                    AND del_orders.order_id = order_details.order_id 
                                                    AND products.id = order_details.product_id 
                                                    AND vendor_details.id = order_details.vendor_id 
                                                    AND vendor_warehouse.id = order_details.vendor_warehouse_id
                                                    AND order_details.customer_id = customer_details.cust_id");
                $data['order_details'] = json_decode(json_encode($order_details), true);    

                
                $temp_order_details[$i] = $data['order_details'];
            }
            $data['order_details'] = $temp_order_details;
            //print_r($data['order_details']);
            return view('OrderManagement/order_details',$data);
            
        }
        public function pending_for_vendor_approval()
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
             return redirect()->to($url);
            }
            $today = date('d-m-Y');
            $data['start_date'] = date('Y-m-d');
            $data['end_date'] = date('Y-m-d');
            $temp_order_details = DB::select("SELECT * FROM del_orders,order_details WHERE del_orders.order_id=order_details.order_id AND del_orders.order_approval_status='Pending' AND del_orders.status != 'Cancel' AND del_orders.DelDate='$today' ");
            $data['temp_order_details'] = json_decode(json_encode($temp_order_details),true);
            //print_r($data['temp_order_details']);
            $prev_order_id = 0;
            $count_status = 1;
            $status = null;
            $data['final_array'] = array();
            $order_details_array = array();
            foreach($data['temp_order_details'] as $order_detail)
            {
                if($order_detail['order_id'] == $prev_order_id)
                {
                        if($status=='Accepted')
                        {
                                $count_status = $count_status + 1;
                                $temp_data['DelDate'] = $order_detail['DelDate'];
                                $temp_data['order_id'] = $order_detail['order_id'];
                                $temp_data['lead_id'] = $order_detail['lead_id'];
                                $temp_data['shipping_first_name'] = $order_detail['shipping_first_name'];
                                $temp_data['mobileno'] = $order_detail['mobileno'];
                                $temp_data['status'] = $order_detail['status'];         
                                $count = count($order_details_array[$prev_order_id]);
                                $order_details_array[$prev_order_id][$count] = $temp_data;            
                                $order_details_array[$prev_order_id]['count'] = $count_status;
                        }
                        $status = $order_detail['status'];
                }
                else
                {
                    $count_status = 1;
                    if($status=='Accepted')
                    {
                        $prev_order_id = $order_detail['order_id'];
                        $temp_data['DelDate'] = $order_detail['DelDate'];
                        $temp_data['order_id'] = $order_detail['order_id'];
                        $temp_data['lead_id'] = $order_detail['lead_id'];
                        $temp_data['shipping_first_name'] = $order_detail['shipping_first_name'];
                        $temp_data['mobileno'] = $order_detail['mobileno'];
                        $temp_data['status'] = $order_detail['status'];                        
                        $order_details_array[$prev_order_id][0] = $temp_data;
                        $order_details_array[$prev_order_id]['count'] = $count_status;
                        $count_status = $count_status + 1;
                    }
                    $status = $order_detail['status'];
                }
            }
            //print_r($order_details_array);
            foreach($order_details_array as $order_detail)
            {
            $count=count($order_detail);
            //$count=$count-1;
            //echo $count;
            if($order_detail['count'] == $count)
            {
                //echo "a";
                //$final_array = $order_detail;
                $lead_id = $order_detail[0]['lead_id'];
                $order_id = $order_detail[0]['order_id'];
                DB::UPDATE("UPDATE del_orders SET order_approval_status = 'Approved' WHERE order_id = $order_id");
                //   $leads_log = new leads_log();
                //   $leads_log_data = [
                //      'log_lead_id' => $lead_id,
                //      'log_lead_status' => 'Delivery In Progress',
                //      'log_date' => date('Y-m-d'),
                //      'log_time' => date('H:i:s'),
                //      'updated_by' => session('username')
                //   ];
                //   $leads_log->insert($leads_log_data);
                //array_push($data['final_array'],$order_detail);
            }
            }
            $order_details = DB::select("SELECT DISTINCT('order_id'),del_orders.*,order_details.created_at as created_at FROM del_orders,order_details WHERE del_orders.order_id=order_details.order_id AND del_orders.order_approval_status='Pending' AND del_orders.status !='Cancel' AND del_orders.DelDate ='$today' ");
            $data['order_details'] = json_decode(json_encode($order_details),true);
            echo "<script>localStorage['filtered']='today';</script>";        
            return view('/OrderManagement/pending_for_vendor_approval',$data);
        }
        public function view_pending_order_details($order_id)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
             return redirect()->to($url);
            }
            $order_details = DB::select("SELECT 
                                            order_details.id as order_details_id,
                                            order_details.creation_date as creation_date,
                                            order_details.order_id as order_id,
                                            order_details.status as status,
                                            order_details.unique_id as unique_id,
                                            order_details.customer_id as customer_id,
                                            order_details.vendor_product_id as vendor_product_id,
                                            order_details.vendor_id as vendor_id,
                                            order_details.vendor_warehouse_id as vendor_warehouse_id,
                                            order_details.created_at as created_at,
                                            order_details.product_qty as product_qty,
                                            vendor_details.registered_name as registered_name,
                                            products.product_name as product_name,
                                            customer_details.customer_name as customer_name,
                                            vendor_warehouse.wh_name as warehouse_name,
                                            vendor_warehouse.wh_area as warehouse_area,
                                            vendor_warehouse.wh_city as warehouse_city
                                        FROM 
                                            order_details,products,vendor_details,customer_details,vendor_warehouse
                                        WHERE 
                                            order_details.order_id = $order_id 
                                            AND 
                                            order_details.vendor_id = vendor_details.id 
                                            AND 
                                            order_details.product_id = products.id 
                                            AND 
                                            order_details.customer_id = customer_details.cust_id
                                            AND
                                            order_details.vendor_warehouse_id = vendor_warehouse.id");
            $data['order_details'] = json_decode(json_encode($order_details),true);
            //print_r($data['order_details']);
            return view('/OrderManagement/view_pending_order_details',$data);
        }
        public function reassign_vendor($order_id)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
             return redirect()->to($url);
            }
            $product_details = DB::select("SELECT del_orders.lead_id as lead_id,
                                                    del_orders.DelDate as DelDate,
                                                    order_details.*,
                                                    products.product_rent as actual_product_rent,
                                                    products.product_name as product_name
                                            FROM products,order_details,del_orders WHERE order_details.id=$order_id 
                                            AND order_details.product_id=products.id 
                                            AND order_details.order_id=del_orders.order_id ");
            $data['product_details'] = json_decode(json_encode($product_details),true);
            $vendor_details = DB::select("SELECT DISTINCT vendor_details.*,order_details.id as order_details_id FROM vendor_details,order_details,vendor_products WHERE order_details.id=$order_id AND order_details.product_id = vendor_products.product_id AND vendor_products.vendor_id = vendor_details.id");
            $data['vendor_details'] = json_decode(json_encode($vendor_details), true);

            //print_r($data['product_details']);
            return view('/OrderManagement/reassign_vendor',$data);
        }
        public function reassign_vendor_post()
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
             return redirect()->to($url);
            }
            //print_r($_POST);
            $lead_id = $_POST['lead_id'];
            $order_id = $_POST['order_id'];
            $order_details_id = $_POST['order_details_id'];
            $DelDate = $_POST['del_date'];
            $vendor_id = $_POST['vendors'][0];
            $warehouse_id = $_POST['warehouses'][0];
            $product_id= $_POST['req_eq_hidden'][0];
            $vendor_product_id= $_POST['vendor_product_id'][0];
            $product_qty= $_POST['eq_quantity_hidden'][0];
            $product_rent= $_POST['offered_rent'][0];
            $product_deposite= $_POST['deposite'][0];
            $product_transport= $_POST['transport'][0];
            $brand = $_POST['brands'][0];
            $batch = $_POST['batches'][0];
            //$update_order = DB::update("UPDATE order_details SET vendor_id=$vendor_id, vendor_warehouse_id=$warehouse_id, status='Pending' WHERE id=$order_id");
            $del_orders = new DelOrders();
            $order_details = new OrderDetails();
            $update_del_orders = [
                'status'=>'Pending',
                'lead_id'=>$lead_id,
                'vendor_id'=>$vendor_id,
                'deliverypickup'=>'Delivery',
                'DelDate'=>$DelDate,
                // 'location'=>$location,
                // 'shipping_first_name'=>$customer_name,
                // 'TotalAmt'=>$temp_order_detail[$count-1]['total_amt'],
                // 'PaymentMode'=>request()->get('payment_mode'),
                // 'mobileno'=>request()->get('mobile_no'),
                'DelAssignedTo'=>'Pending',
                'TravelMode'=>'Null',
                'order_approval_status'=>'Pending',
                // 'fulldetails'=>$fulldetails,
            ];
            //print_r($generate_order); 
                $del_orders->where('order_id',$order_id)->update($update_del_orders);
                $update_order_details = [
                    'order_id'=> $order_id,
                    //'customer_id'=>$customer_id,
                    'product_id'=>$product_id,
                    'vendor_product_id'=>$vendor_product_id,
                    'vendor_id'=>$vendor_id,
                    'vendor_warehouse_id'=>$warehouse_id,
                    'product_brand'=>$brand,
                    'product_batch'=>$batch,
                    'product_qty'=>$product_qty,
                    'product_rent'=>$product_rent,
                    'product_deposite'=>$product_deposite,
                    'transport'=>$product_transport,
                    //'sale_rental' =>$temp_sale_rental_hidden,
                    //'creation_date'=>$date,
                    //'pickup_date'=>$pickup_date,
                    'status'=>'Pending',
                    //'created_at'=>$created_at,
                    //'created_by'=>$username,  
                ];
            $order_details->where('id',$order_details_id)->update($update_order_details);

            $vendor_details = DB::select("SELECT * FROM vendor_details WHERE id=$vendor_id");
            $data['vendor_details'] = json_decode(json_encode($vendor_details),true);
            
            //session(['email' => $data['vendor_details'][0]['of_email']]);
            //$data = array('name'=>"'".$data['vendor_details'][0]['registered_name']."'",'product_name'=>"'".$data['products'][0]['product_name']."'");
            // Mail::send('vendorMail/pendingRentedProductMail', $data, function($message) 
            // {                
            //     $email_id = session('email');
            //     $message->to($email_id, 'Product Rental Request')->subject('Product Rental Request');
            //     $message->from('tempmailquali@gmail.com', 'Quali55Care');
            // });
            
            return redirect('/view_pending_order_details/'.$order_id)->with('message','Product Assign Succesfully');
        }
        public function mobile_app_leads()
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
            return redirect()->to($url);
            }
            $leads = DB::select('SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = "Mobile Generated" ORDER BY leads.creation_date DESC');
            $data['lead_details'] = json_decode(json_encode($leads), true);
            for ($i=0; $i < count($data['lead_details']); $i++) 
            { 
                $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
                $equipement_details = array();
                for ($j=0; $j <count($product); $j++) 
                { 
                $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
                $product_details = json_decode(json_encode($product_details), true);
                array_push($equipement_details,$product_details[0]['product_name']);
                }
                $equipements = json_encode($equipement_details);
                $data['lead_details'][$i]['equipment_requirement'] = $equipements;
            }
            return view('OrderManagement/mobileAppLeads',$data);
        }
        public function status_change()
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
            return redirect()->to($url);
            }
            
            $order_details_id = $_POST['order_details_id'];
            $order_id = $_POST['order_id'];
            $vendor_product_id = $_POST['vendor_product_id'];
            $vendor_id = $_POST['vendor_id'];
            $vendor_warehouse_id = $_POST['vendor_warehouse_id'];
            $product_qty = $_POST['product_qty'];
            $creation_date = $_POST['creation_date'];
            $vendor_rented_products = new VendorRentedProducts();
            $OrderDetails = new OrderDetails();
            // $date = date('dm');  
            for ($i=0; $i < count($vendor_warehouse_id); $i++) 
            {
                $max_id = DB::select("SELECT max(id) as id FROM vendor_rented_products");
                $data['max_id'] = json_decode(json_encode($max_id),true);
                $max_id = $data['max_id'][0]['id']+1;
                $unique_id = dechex($vendor_id[$i]."".$date."".$vendor_warehouse_id[$i]."".$vendor_product_id[$i]."".$max_id); 
                // order_details status changing to accpted
                $temp_order_id = $order_id[$i];
                $status_order_details = DB::update("UPDATE order_details status = 'Accepted' WHERE order_id IN($temp_order_id)");
                for($j=0; $j<$product_qty[$i]; $j++)
                {
                    $insert_order =[
                        'vendor_id'=>$vendor_id[$i],
                        'vendor_product_id'=> $vendor_product_id[$i],
                        'unique_id'=>$unique_id,
                        'rental_date'=>$creation_date[$i],
                        'pickup_date'=>date('Y-m-d',strtotime("+1 month",strtotime($creation_date[$i]))),
                        'status' => 'On Rent',
                        'created_by'=>session('username')
                    ];
                   $get_insert_id = $vendor_rented_products->insertGetId($insert_order);
                   $OrderDetails->where('id',$order_details_id[$i])->update(['rented_product_id'=>$get_insert_id]);
                }
                // Del_orders status changed to Approved 
                $status_del_orders = DB::update("UPDATE del_orders SET order_approval_status = 'Approved' WHERE order_id IN($temp_order_id)");
            }
            return redirect()->back()->with('message','Approved Successfully');
        }
        public function filterPendingVendorApproval($filter_by)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
             return redirect()->to($url);
            }
            if($filter_by =='today')
            {
                $date = date('d-m-Y');
                $data['start_date'] = date('Y-m-d');
                $data['end_date'] = date('Y-m-d');
                $whereClause = "del_orders.DelDate = '$date'";
                echo "<script>localStorage['filtered']='today';</script>";
            }
            elseif($filter_by =='yesterday')
            {
                $prevDate = date('d-m-Y',strtotime("-1 days"));
                $data['start_date'] = date('Y-m-d',strtotime($prevDate));
                $data['end_date'] = date('Y-m-d');
                $whereClause = "del_orders.DelDate = '$prevDate'";
                echo "<script>localStorage['filtered']='yesterday';</script>";
            }
            elseif($filter_by =='past_3_days')
            {
                $past_three_days = date('d-m-Y',strtotime("-2 days"));
                $data['start_date'] = date('Y-m-d',strtotime($past_three_days));
                $data['end_date'] = date('Y-m-d');
                $whereClause = "del_orders.DelDate >= '$past_three_days'";
                echo "<script>localStorage['filtered']='past_3_days';</script>";
            }
            elseif($filter_by =='week')
            {
                $past_three_days = date('d-m-Y',strtotime("-7 days"));
                $data['start_date'] = date('Y-m-d',strtotime($past_three_days));
                $data['end_date'] = date('Y-m-d');
                $whereClause = "del_orders.DelDate >= '$past_three_days'";
                echo "<script>localStorage['filtered']='week';</script>";
            }
            elseif($filter_by =='month')
            {
                $month = date('m-Y');
                $start_date_temp = '01-'.$month;
                $start_date = date('d-m-Y',strtotime($start_date_temp));
                $end_date_temp = '31-'.$month;
                $end_date = date('d-m-Y',strtotime($end_date_temp));
                $data['start_date'] = date('Y-m-d',strtotime($start_date));
                $data['end_date'] = date('Y-m-d',strtotime($end_date));
                $whereClause = "del_orders.DelDate BETWEEN '$start_date' AND '$end_date'";
            }
            elseif($filter_by =='all')
            {
                $isLoggedIn = $this->isLoggedIn();
                if($isLoggedIn == 'false')
                {
                    $url = url('/');
                    return redirect()->to($url);
                }

                $temp_order_details = DB::select("SELECT * FROM del_orders,order_details WHERE del_orders.order_id=order_details.order_id AND del_orders.order_approval_status='Pending' AND del_orders.status != 'Cancel' ");
                $data['temp_order_details'] = json_decode(json_encode($temp_order_details),true);
                $prev_order_id = 0;
                $count_status = 1;
                $status = null;
                $data['final_array'] = array();
                $order_details_array = array();
                foreach($data['temp_order_details'] as $order_detail)
                {
                if($order_detail['order_id'] == $prev_order_id)
                {
                        if($status=='Accepted')
                        {
                            $count_status = $count_status + 1;
                            $temp_data['DelDate'] = $order_detail['DelDate'];
                            $temp_data['order_id'] = $order_detail['order_id'];
                            $temp_data['lead_id'] = $order_detail['lead_id'];
                            $temp_data['shipping_first_name'] = $order_detail['shipping_first_name'];
                            $temp_data['mobileno'] = $order_detail['mobileno'];
                            $temp_data['status'] = $order_detail['status'];         
                            $count = count($order_details_array[$prev_order_id]);
                            $order_details_array[$prev_order_id][$count] = $temp_data;            
                            $order_details_array[$prev_order_id]['count'] = $count_status;
                        }
                        $status = $order_detail['status'];
                }
                else
                {
                    $count_status = 1;
                    if($status=='Accepted')
                    {
                        $prev_order_id = $order_detail['order_id'];
                        $temp_data['DelDate'] = $order_detail['DelDate'];
                        $temp_data['order_id'] = $order_detail['order_id'];
                        $temp_data['lead_id'] = $order_detail['lead_id'];
                        $temp_data['shipping_first_name'] = $order_detail['shipping_first_name'];
                        $temp_data['mobileno'] = $order_detail['mobileno'];
                        $temp_data['status'] = $order_detail['status'];                        
                        $order_details_array[$prev_order_id][0] = $temp_data;
                        $order_details_array[$prev_order_id]['count'] = $count_status;
                        $count_status = $count_status + 1;
                    }
                    $status = $order_detail['status'];
                }
                }
                //print_r($order_details_array);
                foreach($order_details_array as $order_detail)
                {
                $count=count($order_detail);
                //$count=$count-1;
                //echo $count;
                if($order_detail['count'] == $count)
                {
                    //echo "a";
                    //$final_array = $order_detail;
                    $lead_id = $order_detail[0]['lead_id'];
                    $order_id = $order_detail[0]['order_id'];
                    DB::UPDATE("UPDATE del_orders SET order_approval_status = 'Approved' WHERE order_id = $order_id");
                    //   $leads_log = new leads_log();
                    //   $leads_log_data = [
                    //      'log_lead_id' => $lead_id,
                    //      'log_lead_status' => 'Delivery In Progress',
                    //      'log_date' => date('Y-m-d'),
                    //      'log_time' => date('H:i:s'),
                    //      'updated_by' => session('username')
                    //   ];
                    //   $leads_log->insert($leads_log_data);
                    //array_push($data['final_array'],$order_detail);
                }
                }
                $order_details = DB::select("SELECT * FROM del_orders WHERE  del_orders.order_approval_status='Pending'  AND del_orders.status !='Cancel'");
                //echo "SELECT * FROM del_orders,order_details WHERE del_orders.order_id=order_details.order_id AND del_orders.order_approval_status";
                $data['order_details'] = json_decode(json_encode($order_details),true);        
                return view('/OrderManagement/pending_for_vendor_approval',$data);
            }
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                    $url = url('/');
                return redirect()->to($url);
            }
            $temp_order_details = DB::select("SELECT * FROM del_orders,order_details WHERE del_orders.order_id=order_details.order_id AND del_orders.order_approval_status='Pending' AND del_orders.status != 'Cancel' AND $whereClause ");
            $data['temp_order_details'] = json_decode(json_encode($temp_order_details),true);
            $prev_order_id = 0;
            $count_status = 1;
            $status = null;
            $data['final_array'] = array();
            $order_details_array = array();
            foreach($data['temp_order_details'] as $order_detail)
            {
                if($order_detail['order_id'] == $prev_order_id)
                {
                    if($status=='Accepted')
                    {
                            $count_status = $count_status + 1;
                            $temp_data['DelDate'] = $order_detail['DelDate'];
                            $temp_data['order_id'] = $order_detail['order_id'];
                            $temp_data['lead_id'] = $order_detail['lead_id'];
                            $temp_data['shipping_first_name'] = $order_detail['shipping_first_name'];
                            $temp_data['mobileno'] = $order_detail['mobileno'];
                            $temp_data['status'] = $order_detail['status'];         
                            $count = count($order_details_array[$prev_order_id]);
                            $order_details_array[$prev_order_id][$count] = $temp_data;            
                            $order_details_array[$prev_order_id]['count'] = $count_status;
                    }
                    $status = $order_detail['status'];
                }
                else
                {
                    $count_status = 1;
                    if($status=='Accepted')
                    {
                        $prev_order_id = $order_detail['order_id'];
                        $temp_data['DelDate'] = $order_detail['DelDate'];
                        $temp_data['order_id'] = $order_detail['order_id'];
                        $temp_data['lead_id'] = $order_detail['lead_id'];
                        $temp_data['shipping_first_name'] = $order_detail['shipping_first_name'];
                        $temp_data['mobileno'] = $order_detail['mobileno'];
                        $temp_data['status'] = $order_detail['status'];                        
                        $order_details_array[$prev_order_id][0] = $temp_data;
                        $order_details_array[$prev_order_id]['count'] = $count_status;
                        $count_status = $count_status + 1;
                    }
                    $status = $order_detail['status'];
                }
            }
            //print_r($order_details_array);
            foreach($order_details_array as $order_detail)
            {
                $count=count($order_detail);
                //$count=$count-1;
                //echo $count;
                if($order_detail['count'] == $count)
                {
                    //echo "a";
                    //$final_array = $order_detail;
                    $lead_id = $order_detail[0]['lead_id'];
                    $order_id = $order_detail[0]['order_id'];
                    DB::UPDATE("UPDATE del_orders SET order_approval_status = 'Approved' WHERE order_id = $order_id ");
                //   $leads_log = new leads_log();
                //   $leads_log_data = [
                //      'log_lead_id' => $lead_id,
                //      'log_lead_status' => 'Delivery In Progress',
                //      'log_date' => date('Y-m-d'),
                //      'log_time' => date('H:i:s'),
                //      'updated_by' => session('username')
                //   ];
                //   $leads_log->insert($leads_log_data);
                    //array_push($data['final_array'],$order_detail);
                }
            }
            $order_details = DB::select("SELECT * FROM del_orders WHERE  del_orders.order_approval_status='Pending' AND del_orders.status != 'Cancel' AND $whereClause ");
            //echo "SELECT * FROM del_orders,order_details WHERE del_orders.order_id=order_details.order_id AND del_orders.order_approval_status";
            $data['order_details'] = json_decode(json_encode($order_details),true);        
            return view('/OrderManagement/pending_for_vendor_approval',$data);     
        }
        public function filterPendingVendorApprovalDWS()
        {
            $start_date = $_POST['start_date'];   
            $end_date = $_POST['end_date'];
            $data['start_date'] = $_POST['start_date'];   
            $data['end_date'] = $_POST['end_date'];
            $whereClause = "del_orders.DelDate BETWEEN '$start_date' AND '$end_date'";

            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                    $url = url('/');
                return redirect()->to($url);
            }
            $temp_order_details = DB::select("SELECT * FROM del_orders,order_details WHERE del_orders.order_id=order_details.order_id AND del_orders.order_approval_status='Pending' AND del_orders.status != 'Cancel' AND $whereClause ");
            $data['temp_order_details'] = json_decode(json_encode($temp_order_details),true);
            $prev_order_id = 0;
            $count_status = 1;
            $status = null;
            $data['final_array'] = array();
            $order_details_array = array();
            foreach($data['temp_order_details'] as $order_detail)
            {
                if($order_detail['order_id'] == $prev_order_id)
                {
                    if($status=='Accepted')
                    {
                            $count_status = $count_status + 1;
                            $temp_data['DelDate'] = $order_detail['DelDate'];
                            $temp_data['order_id'] = $order_detail['order_id'];
                            $temp_data['lead_id'] = $order_detail['lead_id'];
                            $temp_data['shipping_first_name'] = $order_detail['shipping_first_name'];
                            $temp_data['mobileno'] = $order_detail['mobileno'];
                            $temp_data['status'] = $order_detail['status'];         
                            $count = count($order_details_array[$prev_order_id]);
                            $order_details_array[$prev_order_id][$count] = $temp_data;            
                            $order_details_array[$prev_order_id]['count'] = $count_status;
                    }
                    $status = $order_detail['status'];
                }
                else
                {
                    $count_status = 1;
                    if($status=='Accepted')
                    {
                    $prev_order_id = $order_detail['order_id'];
                    $temp_data['DelDate'] = $order_detail['DelDate'];
                    $temp_data['order_id'] = $order_detail['order_id'];
                    $temp_data['lead_id'] = $order_detail['lead_id'];
                    $temp_data['shipping_first_name'] = $order_detail['shipping_first_name'];
                    $temp_data['mobileno'] = $order_detail['mobileno'];
                    $temp_data['status'] = $order_detail['status'];                        
                    $order_details_array[$prev_order_id][0] = $temp_data;
                    $order_details_array[$prev_order_id]['count'] = $count_status;
                    $count_status = $count_status + 1;
                    }
                    $status = $order_detail['status'];
                }
            }
            //print_r($order_details_array);
            foreach($order_details_array as $order_detail)
            {
                $count=count($order_detail);
                //$count=$count-1;
                //echo $count;
                if($order_detail['count'] == $count)
                {
                    //echo "a";
                    //$final_array = $order_detail;
                    $lead_id = $order_detail[0]['lead_id'];
                    $order_id = $order_detail[0]['order_id'];
                    DB::UPDATE("UPDATE del_orders SET order_approval_status = 'Approved' WHERE order_id = $order_id ");
                //   $leads_log = new leads_log();
                //   $leads_log_data = [
                //      'log_lead_id' => $lead_id,
                //      'log_lead_status' => 'Delivery In Progress',
                //      'log_date' => date('Y-m-d'),
                //      'log_time' => date('H:i:s'),
                //      'updated_by' => session('username')
                //   ];
                //   $leads_log->insert($leads_log_data);
                    //array_push($data['final_array'],$order_detail);
                }
            }
            $order_details = DB::select("SELECT * FROM del_orders WHERE  del_orders.order_approval_status='Pending'  AND del_orders.status !='Cancel' AND $whereClause ");
            //echo "SELECT * FROM del_orders,order_details WHERE del_orders.order_id=order_details.order_id AND del_orders.order_approval_status";
            $data['order_details'] = json_decode(json_encode($order_details),true);        
            return view('/OrderManagement/pending_for_vendor_approval',$data);     
        }
        public function approve_orders()
        {
            $order_checked = $_POST['order_checked'];
            $vendor_rented_products = new VendorRentedProducts();
            $OrderDetails =new OrderDetails();
            for ($i=0; $i <count($order_checked) ; $i++)
            {
                
                //$order_id = implode(",",$_POST['order_id']);
                $order_id = $order_checked[$i];
                $order_details = DB::select("SELECT * FROM order_details WHERE order_id ='$order_id' ");
                $data['order_details'] = json_decode(json_encode($order_details),true);  
                for ($j=0; $j <count($data['order_details']) ; $j++) 
                {
                    $order_details_id = $data['order_details'][$j]['id'];
                    $order_id = $data['order_details'][$j]['order_id'];
                    $vendor_id = $data['order_details'][$j]['vendor_id'];
                    $vendor_warehouse_id = $data['order_details'][$j]['vendor_warehouse_id'];
                    $vendor_product_id = $data['order_details'][$j]['vendor_product_id'];
                    $product_qty = $data['order_details'][$j]['product_qty'];
                    $creation_date = $data['order_details'][$j]['creation_date'];
                    $unique_id = $data['order_details'][$j]['unique_id'];
                    // $date = date('dm');    
                    // $max_id = DB::select("SELECT max(id) as id FROM vendor_rented_products");
                    // $data['max_id'] = json_decode(json_encode($max_id),true);
                    // $max_id = $data['max_id'][0]['id']+1;
                    // $unique_id = dechex($vendor_id."".$date."".$vendor_warehouse_id."".$vendor_product_id."".$max_id);
                    //order_details status changing to accpted
                    $status_order_details = DB::update("UPDATE order_details SET status = 'Accepted' WHERE order_id ='$order_id' AND id='$order_details_id' ");
                    for($k=0; $k<$product_qty; $k++)
                    {
                        $insert_order =[
                            'vendor_id'=>$vendor_id,
                            'vendor_product_id'=> $vendor_product_id,
                            'unique_id'=>$unique_id,
                            'rental_date'=>$creation_date,
                            'pickup_date'=>date('Y-m-d',strtotime('+1 month',strtotime($creation_date))),
                            'status' => 'On Rent',
                            'created_by'=>session('username')
                        ];
                        //print_r($insert_order);
                        $get_insert_id = $vendor_rented_products->insertGetId($insert_order);
                        $OrderDetails->where('id',$order_details_id)->update(['rented_product_id'=>$get_insert_id]);
                    }
                    //Del_orders status changed to Approved 
                    $status_del_orders = DB::update("UPDATE del_orders SET order_approval_status = 'Approved' WHERE order_id='$order_id' ");
                }
            }
            return redirect()->back()->with('message','Order Approved Successfully');

        }
        //New Logics Based on Batches
        // --Select Vendor based on equipments-- //
        public static function individual_vendor_batch($equipment,$eq_quantity)
        {
            if($_SERVER['REQUEST_METHOD']=='GET')
            {
                $vendors = DB::select("SELECT DISTINCT vendor_details.id as vendor_id, vendor_details.registered_name as vendor_name FROM vendor_details, vendor_products,vendor_product_details WHERE vendor_products.product_id = $equipment AND vendor_products.vendor_id = vendor_details.id AND vendor_products.product_quantity >= $eq_quantity AND (vendor_products.status != 'Pending' OR vendor_products.status != 'Rejected') AND (vendor_product_details.vendor_products_id = vendor_products.id AND vendor_product_details.availability_status = '0' AND vendor_product_details.current_location != 0)");
                $data['vendors'] = json_decode(json_encode($vendors), true);
                $jsonstring = json_encode($data['vendors']);
                echo $jsonstring;
            } 
        }
        //for sale
        public function individual_vendor_batch_sale()
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
             return redirect()->to($url);
            }
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
            if($_SERVER['REQUEST_METHOD']=='GET')
            {
                $vendors = DB::select("SELECT DISTINCT vendor_details.id as vendor_id, vendor_details.registered_name as vendor_name FROM vendor_details, vendor_products WHERE vendor_products.vendor_id = vendor_details.id AND (vendor_products.status != 'Pending' OR vendor_products.status != 'Rejected')");
                $data['vendors'] = json_decode(json_encode($vendors), true);
                $jsonstring = json_encode($data['vendors']);
                echo $jsonstring;
            } 
        }
        //ware for sale
        public function select_vendor_warehouses_sale($vendor_id)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
            if($_SERVER['REQUEST_METHOD']=='GET')
            {
                $warehouses = DB::select("SELECT DISTINCT vendor_warehouse.id as warehouse_id, vendor_warehouse.wh_name as wh_name, vendor_warehouse.wh_area as wh_area, vendor_warehouse.wh_city as wh_city FROM vendor_warehouse,vendor_products WHERE vendor_products.vendor_id = $vendor_id AND vendor_products.warehouse_id = vendor_warehouse.id ");
                $data['warehouses'] = json_decode(json_encode($warehouses), true);
                $jsonstring = json_encode($data['warehouses']);
                echo $jsonstring;
            }
        }
        //close sale
        public function select_vendor_warehouses($equipment,$vendor_id)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
            if($_SERVER['REQUEST_METHOD']=='GET')
            {
                // $warehouses = DB::select("SELECT DISTINCT vendor_warehouse.id as warehouse_id, vendor_warehouse.wh_name as wh_name, vendor_warehouse.wh_area as wh_area, vendor_warehouse.wh_city as wh_city FROM vendor_warehouse,vendor_products WHERE vendor_products.product_id = $equipment AND vendor_products.vendor_id = $vendor_id AND vendor_products.warehouse_id = vendor_warehouse.id ");
                // $data['warehouses'] = json_decode(json_encode($warehouses), true);
                // $jsonstring = json_encode($data['warehouses']);
                // echo $jsonstring;
                $warehouses = DB::select("SELECT DISTINCT vendor_warehouse.id as warehouse_id, vendor_warehouse.wh_name as wh_name, vendor_warehouse.wh_area as wh_area, vendor_warehouse.wh_city as wh_city,vendor_product_details.current_location as current_location FROM vendor_products,vendor_warehouse,vendor_product_details WHERE vendor_products.product_id = $equipment AND vendor_products.vendor_id = $vendor_id AND vendor_products.id = vendor_product_details.vendor_products_id AND vendor_product_details.warehouse_id = vendor_warehouse.id AND vendor_product_details.current_location IN(1,2)");
                // echo "SELECT DISTINCT vendor_warehouse.id as warehouse_id, vendor_warehouse.wh_name as wh_name, vendor_warehouse.wh_area as wh_area, vendor_warehouse.wh_city as wh_city,vendor_product_details.current_location as current_location FROM vendor_products,vendor_warehouse,vendor_product_details WHERE vendor_products.product_id = $equipment AND vendor_products.vendor_id = $vendor_id AND vendor_products.id = vendor_product_details.vendor_products_id AND vendor_product_details.warehouse_id = vendor_warehouse.id AND vendor_product_details.current_location IN(1,2)";
                $data['warehouses'] = json_decode(json_encode($warehouses), true);
                $virtual_warehouse = array();
                $vendor_warehouse = array();
                foreach($data['warehouses'] as $key=>$warehouse)
                {
                    if($warehouse['current_location']==1)
                    {
                        array_push($virtual_warehouse,$warehouse);
                    }
                    elseif($warehouse['current_location']==2)
                    {
                        array_push($vendor_warehouse,$warehouse);
                    }
                }
                $pata['warehouses'] = array();
                array_push($pata['warehouses'],$virtual_warehouse);
                array_push($pata['warehouses'],$vendor_warehouse);
                $jsonstring = json_encode($pata['warehouses']);
                echo $jsonstring;
                // return $pata;
            }
        }
        public function select_product_brand($equipment,$vendor_id,$warehouse_id)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
            if($_SERVER['REQUEST_METHOD']=='GET')
            {
                $brands = DB::select("SELECT DISTINCT product_brands.id as brand_id, product_brands.brand_name as brand_name FROM product_brands,vendor_products,vendor_product_details WHERE vendor_products.product_id = $equipment AND vendor_products.vendor_id = $vendor_id AND vendor_product_details.vendor_products_id = vendor_products.id AND vendor_product_details.warehouse_id = $warehouse_id AND product_brands.id = vendor_products.product_brand");
                $data['brands'] = json_decode(json_encode($brands), true);
                $jsonstring = json_encode($data['brands']);
                echo $jsonstring;
            }
        }
        //for sale
        public function select_product_brand_sale($equipment)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
            if($_SERVER['REQUEST_METHOD']=='GET')
            {
                $brands = DB::select("SELECT DISTINCT product_brands.id as brand_id, product_brands.brand_name as brand_name FROM product_brands WHERE product_brands.product_id = $equipment ");
                $data['brands'] = json_decode(json_encode($brands), true);
                $jsonstring = json_encode($data['brands']);
                echo $jsonstring;
            }
        }
        public function select_batch($equipment,$vendor_id,$warehouse_id,$brand_id)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
            if($_SERVER['REQUEST_METHOD']=='GET')
            {
                $brands = DB::select("SELECT DISTINCT vendor_products.id as vendor_product_id, vendor_products.batch as batch_name, vendor_products.product_rent_approved as product_rent FROM vendor_products,vendor_product_details WHERE vendor_products.product_id = $equipment AND vendor_products.vendor_id = $vendor_id AND vendor_product_details.warehouse_id = $warehouse_id AND vendor_product_details.vendor_products_id = vendor_products.id AND vendor_products.product_brand = $brand_id");
                $data['brands'] = json_decode(json_encode($brands), true);
                $jsonstring = json_encode($data['brands']);
                echo $jsonstring;
            }
        }
        public function select_inventory($product_id,$warehouse_id)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            if($_SERVER['REQUEST_METHOD']=='GET')
            {
                $inventory = DB::select("SELECT vendor_product_details.id as vendor_product_details_id, vendor_product_details.inventory_id as inventory_id FROM vendor_product_details WHERE vendor_products_id = $product_id AND warehouse_id = $warehouse_id AND availability_status=0 AND current_location != 0");
                $data['inventory'] = json_decode(json_encode($inventory), true);
                $jsonstring = json_encode($data['inventory']);
                echo $jsonstring;
            }
        }
        public function getDetails($product_id)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
            if($_SERVER['REQUEST_METHOD']=='GET')
            {
                $details = DB::select("SELECT vendor_products.product_rent_approved as product_rent, vendor_products.id as vendor_product_id, products.product_details as product_details FROM vendor_products,products WHERE vendor_products.id = $product_id AND vendor_products.product_id=products.id");
                $data['details'] = json_decode(json_encode($details), true);
                $jsonstring = json_encode($data['details']);
                echo $jsonstring;
            }
        }
        public function all_vendor_batch()
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
            if($_SERVER['REQUEST_METHOD']=='POST')
            {
                $equipment = json_decode($_POST['equipments']);
                $count = 0;
                $data_count = count($equipment);
                $data_count = $data_count-1;
                $vdr = array();
                for ($i=0; $i <$data_count ; $i++) 
                { 
                    $equip = $equipment[$i];
                    $vendor_id = DB::select("SELECT DISTINCT vendor_id FROM vendor_products WHERE product_id=$equip AND vendor_products.status != 'Pending' AND vendor_products.status != 'Rejected'");
                    $data['vendor'.$i] = json_decode(json_encode($vendor_id), true); 
                    $count = count($data['vendor'.$i]);
                    $temp_array = $data['vendor'.$i];
                    for($j=0; $j<$count; $j++)
                    {
                        $data['vendor_dummy'][$i][$j] = $temp_array[$j]['vendor_id'];
                    }
                    array_push($vdr,$data['vendor_dummy'][$i]);
                }
                $temp_array_1 =array();
                for($i=0; $i<count($vdr); $i++)
                {
                    $temp_array_1= array_merge($temp_array_1,$vdr[$i]);
                }
                $result_values = array_count_values($temp_array_1);
                $result_keys = array_keys($result_values,"$data_count");
                $vendors = DB::select('SELECT DISTINCT id as vendor_id,registered_name as vendor_name FROM vendor_details WHERE id IN (' . implode(',', array_map('intval', $result_keys)) . ')');
                $data['vendors'] = json_decode(json_encode($vendors), true);
                $jsonstring = json_encode($data['vendors']);
                echo $jsonstring;
            }
        }
        public function select_vendor_warehouses_all()
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
            if($_SERVER['REQUEST_METHOD']=='POST')
            {
                $equipment = json_decode($_POST['equipments']);
                $vendor_id = $_POST['vendor_id'];                
                $count = 0;
                $data_count = count($equipment);
                $data_count = $data_count-1;
                $vdr = array();
                for ($i=0; $i <$data_count ; $i++) 
                {
                    $data['vendor_dummy'] = array();
                    $equip = $equipment[$i];
                    $vendor_ids = DB::select("SELECT DISTINCT warehouse_id FROM vendor_products WHERE product_id = $equip AND vendor_id = $vendor_id");
                    $data['vendor'.$i] = json_decode(json_encode($vendor_ids), true); 
                    $count = count($data['vendor'.$i]);
                    $temp_array = $data['vendor'.$i];
                    for($j=0; $j<$count; $j++)
                    {
                        $data['vendor_dummy'][$i][$j] = $temp_array[$j]['warehouse_id'];
                    }
                    array_push($vdr,$data['vendor_dummy'][$i]);
                }
                $temp_array_1 =array();
                for($i=0; $i<count($vdr); $i++)
                {
                    $temp_array_1= array_merge($temp_array_1,$vdr[$i]);
                }
                $result_values = array_count_values($temp_array_1);
                $result_keys = array_keys($result_values,"$data_count");
                $vendors = DB::select('SELECT DISTINCT id as warehouse_id,wh_name as wh_name, wh_area as wh_area, wh_city as wh_city FROM vendor_warehouse WHERE id IN (' . implode(',', array_map('intval', $result_keys)) . ')');
                $data['vendors'] = json_decode(json_encode($vendors), true);
                $jsonstring = json_encode($data['vendors']);
                echo $jsonstring;
            }
        }
        public function select_product_brand_all()
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
                return redirect()->to($url);
            }
            ini_set('display_errors', 1);
            if($_SERVER['REQUEST_METHOD']=='POST')
            {
                $equipment = json_decode($_POST['equipments']);
                $vendor_id = $_POST['vendor_id'];
                $warehouse_id = $_POST['warehouse_id'];
                $data['brands']=array();
                foreach($equipment as $equip)
                {
                    $brands = DB::select("SELECT DISTINCT product_brands.id as brand_id, product_brands.brand_name as brand_name FROM product_brands,vendor_products WHERE vendor_products.product_id = $equip AND vendor_products.vendor_id = $vendor_id AND vendor_products.warehouse_id = $warehouse_id AND product_brands.id = vendor_products.product_brand");
                    $brands = json_decode(json_encode($brands), true);
                    array_push($data['brands'],$brands);
                }
                $jsonstring = json_encode($data['brands']);
                echo $jsonstring;
            }
        }
        //view all approved orders by vendor side or quali55care approved
        public function viewApprovedOrders()
        {
            return redirect('filterApprovedOrders/today');
        }
        public function filterApprovedOrders($filter_by)
        {
            if($filter_by =='today')
            {
                $date = date('d-m-Y');
                $data['start_date'] = date('Y-m-d');
                $data['end_date'] = date('Y-m-d');

                $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') = STR_TO_DATE('$date','%d-%m-%Y')";
                echo "<script>localStorage['filtered']='today';</script>";
            }
            elseif($filter_by =='yesterday')
            {
                $prevDate = date('d-m-Y',strtotime("-1 days"));
                $data['start_date'] = date('Y-m-d',strtotime("-1 days"));
                $data['end_date'] = date('Y-m-d',strtotime("-1 days"));
                $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') = STR_TO_DATE('$prevDate','%d-%m-%Y')";
                echo "<script>localStorage['filtered']='yesterday';</script>";
            }
            elseif($filter_by =='past_3_days')
            {
                $past_three_days = date('d-m-Y',strtotime("-2 days"));
                $data['start_date'] = date('Y-m-d',strtotime("-2 days"));
                $data['end_date'] = date('Y-m-d');
                $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') >= STR_TO_DATE('$past_three_days','%d-%m-%Y')";
                echo "<script>localStorage['filtered']='past_3_days';</script>";
            }
            elseif($filter_by =='week')
            {
                $past_three_days = date('d-m-Y',strtotime("-7 days"));
                $data['start_date'] = date('Y-m-d',strtotime("-7 days"));
                $data['end_date'] = date('Y-m-d');
                $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') >= STR_TO_DATE('$past_three_days','%d-%m-%Y')";
                echo "<script>localStorage['filtered']='week';</script>";
            }
            elseif($filter_by =='month')
            {
                $month = date('m-Y');
                $start_date_temp = '01-'.$month;
                $start_date = date('d-m-Y',strtotime($start_date_temp));
                $data['start_date'] = date('Y-m-d',strtotime($start_date));
                $end_date_temp = '31-'.$month;
                $end_date = date('d-m-Y',strtotime($end_date_temp));
                $data['end_date'] = date('Y-m-d',strtotime($end_date));
                $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y')";
                echo "<script>localStorage['filtered']='month';</script>";
            }
            elseif($filter_by == 'all')
            {
                echo "<script>localStorage['filtered']='all';</script>";
                $get_approved_orders = DB::select("SELECT 
                                                    DISTINCT 
                                                    customer_details.customer_name as customer_name,
                                                    customer_details.primary_contact_no as contact_no,
                                                    del_orders.order_id as order_id,
                                                    del_orders.DelDate as DelDate,
                                                    del_orders.status as del_status,
                                                    order_details.status as status
                                                FROM 
                                                    order_details,del_orders,customer_details
                                                where 
                                                    order_details.status='Accepted'
                                                AND 
                                                    del_orders.order_approval_status='Approved'
                                                AND 
                                                    del_orders.status != 'Closed'
                                                AND 
                                                    order_details.order_id=del_orders.order_id
                                                AND     
                                                    order_details.customer_id = customer_details.cust_id 
                                                ORDER BY 
                                                    del_orders.order_id DESC");
                $data['get_approved_orders'] = json_decode(json_encode($get_approved_orders), true);
                //print_r($data);
                return view('OrderManagement/ViewApprovedOrders',$data);
            }
            $get_approved_orders = DB::select("SELECT 
                                                    DISTINCT 
                                                    customer_details.customer_name as customer_name,
                                                    customer_details.primary_contact_no as contact_no,
                                                    del_orders.order_id as order_id,
                                                    del_orders.DelDate as DelDate,
                                                    del_orders.status as del_status,
                                                    order_details.status as status
                                                FROM 
                                                    order_details,del_orders,customer_details
                                                where 
                                                    order_details.status='Accepted'
                                                AND 
                                                    del_orders.order_approval_status='Approved'
                                                AND 
                                                    del_orders.status != 'Closed'
                                                AND 
                                                    order_details.order_id=del_orders.order_id
                                                AND     
                                                    order_details.customer_id = customer_details.cust_id 
                                                AND 
                                                    $whereClause
                                                ORDER BY 
                                                    del_orders.order_id DESC");
            $data['get_approved_orders'] = json_decode(json_encode($get_approved_orders), true);
            //print_r($data);
            return view('OrderManagement/ViewApprovedOrders',$data);
        }

        public function filterApprovedOrdersDWS()
        {
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];

            $data['start_date'] = $_POST['start_date'];
            $data['end_date'] = $_POST['end_date'];
            $start_date_d = date('d-m-Y',strtotime($start_date));
            $end_date_d = date('d-m-Y',strtotime($end_date));
            $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date_d','%d-%m-%Y') AND STR_TO_DATE('$end_date_d','%d-%m-%Y')";
            $get_approved_orders = DB::select("SELECT 
                                                    DISTINCT 
                                                    customer_details.customer_name as customer_name,
                                                    customer_details.primary_contact_no as contact_no,
                                                    del_orders.order_id as order_id,
                                                    del_orders.DelDate as DelDate,
                                                    del_orders.status as del_status,
                                                    order_details.status as status
                                                FROM 
                                                    order_details,del_orders,customer_details
                                                where 
                                                    order_details.status='Accepted'
                                                AND 
                                                    del_orders.order_approval_status='Approved'
                                                AND 
                                                    del_orders.status != 'Closed'
                                                AND 
                                                    order_details.order_id=del_orders.order_id
                                                AND     
                                                    order_details.customer_id = customer_details.cust_id 
                                                AND 
                                                    $whereClause
                                                ORDER BY 
                                                    del_orders.order_id DESC");
            $data['get_approved_orders'] = json_decode(json_encode($get_approved_orders), true);
            //print_r($data);
            return view('OrderManagement/ViewApprovedOrders',$data);
        }
        //View All rejected orders 
        public function viewRejectedOrders()
        {
            return redirect('filterRejectedOrders/today');
        }
        public function filterRejectedOrders($filter_by)
        {
            if($filter_by =='today')
            {
                $date = date('d-m-Y');
                $data['start_date'] = date('Y-m-d');
                $data['end_date'] = date('Y-m-d');
                $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') = STR_TO_DATE('$date','%d-%m-%Y')";
                echo "<script>localStorage['filtered']='today';</script>";
            }
            elseif($filter_by =='yesterday')
            {
                $prevDate = date('d-m-Y',strtotime("-1 days"));
                $data['start_date'] = date('Y-m-d',strtotime("-1 days"));
                $data['end_date'] = date('Y-m-d');
                $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') = STR_TO_DATE('$prevDate','%d-%m-%Y')";
                echo "<script>localStorage['filtered']='yesterday';</script>";
            }
            elseif($filter_by =='past_3_days')
            {
                $past_three_days = date('d-m-Y',strtotime("-2 days"));
                $data['start_date'] = date('Y-m-d',strtotime("-2 days"));
                $data['end_date'] = date('Y-m-d');
                $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') >= STR_TO_DATE('$past_three_days','%d-%m-%Y')";
                echo "<script>localStorage['filtered']='past_3_days';</script>";
            }
            elseif($filter_by =='week')
            {
                $past_three_days = date('d-m-Y',strtotime("-7 days"));
                $data['start_date'] = date('Y-m-d',strtotime("-7 days"));
                $data['end_date'] = date('Y-m-d');
                $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') >= STR_TO_DATE('$past_three_days','%d-%m-%Y')";
                echo "<script>localStorage['filtered']='week';</script>";
            }
            elseif($filter_by =='month')
            {
                $month = date('m-Y');
                $start_date_temp = '01-'.$month;
                $start_date = date('d-m-Y',strtotime($start_date_temp));
                $end_date_temp = '31-'.$month;
                $end_date = date('d-m-Y',strtotime($end_date_temp));
                $data['start_date'] = date('Y-m-d',strtotime($start_date_temp));
                $data['end_date'] = date('Y-m-d',strtotime($end_date_temp));
                $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y')";
                echo "<script>localStorage['filtered']='month';</script>";
            }
            elseif($filter_by == 'all')
            {
                $get_rejected_orders = DB::select("SELECT DISTINCT customer_details.customer_name as customer_name,
                customer_details.primary_contact_no as contact_no,
                del_orders.order_id as order_id,
                order_details.status as status
                FROM order_details,del_orders,customer_details 
                where order_details.status='Rejected' 
                AND del_orders.order_approval_status='Pending' 
                AND order_details.order_id=del_orders.order_id
                AND order_details.customer_id = customer_details.cust_id ");
                $data['get_rejected_orders'] = json_decode(json_encode($get_rejected_orders), true);
                //print_r($data);
                return view('OrderManagement/ViewRejectedOrders',$data);
            }
            $get_rejected_orders = DB::select("SELECT DISTINCT customer_details.customer_name as customer_name,
                customer_details.primary_contact_no as contact_no,
                del_orders.order_id as order_id,
                order_details.status as status
                FROM order_details,del_orders,customer_details 
                where order_details.status='Rejected' 
                AND del_orders.order_approval_status='Pending' 
                AND order_details.order_id=del_orders.order_id
                AND order_details.customer_id = customer_details.cust_id AND $whereClause ");
                $data['get_rejected_orders'] = json_decode(json_encode($get_rejected_orders), true);
                //print_r($data);
                return view('OrderManagement/ViewRejectedOrders',$data);
        }

        public function filterRejectedOrdersDWS()
        {
            $data['start_date'] = $_POST['start_date'];
            $data['end_date'] = $_POST['end_date'];
            $start_date = date('d-m-Y',strtotime($_POST['start_date']));
            $end_date = date('d-m-Y',strtotime($_POST['end_date']));
            $whereClause = "STR_TO_DATE(del_orders.DelDate,'%d-%m-%Y') BETWEEN STR_TO_DATE('$start_date','%d-%m-%Y') AND STR_TO_DATE('$end_date','%d-%m-%Y')";
            
            $get_rejected_orders = DB::select("SELECT DISTINCT customer_details.customer_name as customer_name,
                customer_details.primary_contact_no as contact_no,
                del_orders.order_id as order_id,
                order_details.status as status
                FROM order_details,del_orders,customer_details 
                where order_details.status='Rejected' 
                AND del_orders.order_approval_status='Pending' 
                AND order_details.order_id=del_orders.order_id
                AND order_details.customer_id = customer_details.cust_id AND $whereClause ");
                $data['get_rejected_orders'] = json_decode(json_encode($get_rejected_orders), true);
                //print_r($data);
                return view('OrderManagement/ViewRejectedOrders',$data);
        }
        //view order information on click of view details
        public function viewApprovedOrderInfo($order_id)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
             return redirect()->to($url);
            }
            $get_order_info = DB::select("SELECT products.product_name as product_name,
                                                order_details.product_qty as product_quantity,
                                                order_details.product_rent as product_rent,
                                                order_details.product_deposite as product_deposite,
                                                order_details.sale_rental as sale_rental,
                                                order_details.unique_id as unique_id,
                                                order_details.status as status,
                                                order_details.customer_id as customer_id,
                                                order_details.id as order_details_id,
                                                order_details.vendor_warehouse_id as warehouse_id,
                                                vendor_details.registered_name as vendor_name,
                                                vendor_warehouse.wh_name as warehouse_name,
                                                vendor_warehouse.wh_area as warehouse_area,
                                                vendor_warehouse.wh_city as warehouse_city,
                                                del_orders.status as delivery_status,
                                                products.id as product_id
                                        FROM order_details,del_orders,products,vendor_details,vendor_warehouse
                                        where order_details.order_id= $order_id 
                                        AND order_details.status = 'Accepted'
                                        AND del_orders.order_id = order_details.order_id 
                                        AND order_details.product_id = products.id 
                                        AND order_details.vendor_id = vendor_details.id
                                        AND order_details.vendor_warehouse_id = vendor_warehouse.id");
            $data['get_order_info'] = json_decode(json_encode($get_order_info),true);
            $customer_id = $data['get_order_info'][0]['customer_id']; 
            //get all customer information
            $get_customer_info = DB::select("SELECT * FROM customer_details WHERE cust_id ='$customer_id' ");
            $data['customer_info'] = json_decode(json_encode($get_customer_info),true);
            //print_r($data['get_order_info']);
            return view('OrderManagement/ViewApprovedOrderInfo',$data);
        }
        public function viewRejectedOrderInfo($order_id)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
             return redirect()->to($url);
            }
            $get_order_info = DB::select("SELECT products.product_name as product_name,
                                                order_details.product_qty as product_quantity,
                                                order_details.product_rent as product_rent,
                                                order_details.product_deposite as product_deposite,
                                                order_details.sale_rental as sale_rental,
                                                order_details.unique_id as unique_id,
                                                order_details.status as status,
                                                order_details.customer_id as customer_id,
                                                order_details.id as order_details_id,
                                                vendor_details.registered_name as vendor_name

                                        FROM order_details,del_orders,products,vendor_details 
                                        where order_details.order_id= $order_id 
                                        AND order_details.status = 'Rejected'
                                        AND del_orders.order_id = order_details.order_id 
                                        AND order_details.product_id = products.id 
                                        AND order_details.vendor_id = vendor_details.id");
            $data['get_order_info'] = json_decode(json_encode($get_order_info),true);
            $customer_id = $data['get_order_info'][0]['customer_id']; 
            //get all customer information
            $get_customer_info = DB::select("SELECT * FROM customer_details WHERE cust_id ='$customer_id' ");
            $data['customer_info'] = json_decode(json_encode($get_customer_info),true);
            //print_r($data['get_order_info']);
            return view('OrderManagement/ViewRejectedOrderInfo',$data);
        }
        /* Using Get Method */
        public function close_order()
        {
            // print_r($_POST);
            $order_id = $_POST['order_id'];
            $del_orders = DelOrders::where('order_id',$order_id)->get()->toArray();
            // print_r($del_orders);
            if($del_orders[0]['deliverypickup'] == 'Delivery')
            {
                $lead_id = $del_orders[0]['lead_id'];
                lead::where('id',$lead_id)->update(['lead_status'=>'Converted']);
                $order_details = OrderDetails::where('order_id',$order_id)->get()->toArray();
                foreach($order_details as $order_detail)
                {
                    $vendor_product_id = $order_detail['vendor_product_id'];
                    $vendor_product_details_id = $order_detail['vendor_product_details_id'];
                    $vendor_rented_products_id = $order_detail['rented_product_id'];
                    $qty = $order_detail['product_qty'];

                    VendorProductDetails::where('id',$vendor_product_details_id)->update(['availability_status'=>0,'current_location'=>2]);
                    VendorRentedProducts::where('id',$vendor_rented_products_id)->delete();

                    $update = DB::update("UPDATE vendor_products SET vendor_products.product_quantity = vendor_products.product_quantity+$qty WHERE id=$vendor_product_id");

                }
                OrderDetails::where('order_id',$order_id)->update(['current_status'=>'Cancel']);
                DelOrders::where('order_id',$order_id)->update(['status'=>'Cancel']);
                // OrderDetails::where('order_id',$order_id)->delete();
                // DelOrders::where('order_id',$order_id)->delete();
                // return "Deleted";
            }
            elseif($del_orders[0]['deliverypickup'] == 'Pick Up')
            {
                $pickup_details = Pickup::where('pickup_order_id',$order_id)->get()->toArray();
                foreach($pickup_details as $pickup_detail)
                {
                    $order_details_id = $pickup_detail['order_details_id'];
                    OrderDetails::where('id',$order_details_id)->update(['current_status'=>'Pending']);
                    $order_detail = OrderDetails::where('id',$order_details_id)->get()->toArray();
                    $qty = $order_detail[0]['product_qty'];
                    $inventory_id = $order_detail[0]['unique_id'];
                    $vendor_product_id = $order_detail[0]['vendor_product_id'];
                    $update = DB::update("UPDATE vendor_products SET vendor_products.product_quantity = vendor_products.product_quantity-$qty WHERE id=$vendor_product_id");
                    $vendor_product_details_id = $order_detail[0]['vendor_product_details_id'];
                    if(VendorRentedProducts::where('unique_id',$inventory_id)->where('status','On Rent')->exists())
                    {
                        $rented_prod_details = VendorRentedProducts::where('unique_id',$inventory_id)->where('status','On Rent')->get()->toArray();
                        $rented_prod_id =  $rented_prod_details[0]['id'];
                        $order_details_del = OrderDetails::where('rented_product_id',$rented_prod_id)->get()->toArray();
                        if(isset($order_details_del[0]))
                        {
                                $del_order_id = $order_details_del[0]['order_id'];
                                $response = $this->close_deliverey_fun($del_order_id);
                        }
                    }
                    VendorProductDetails::where('id',$vendor_product_details_id)->update(['availability_status'=>0,'current_location'=>0]);
                    $rented_product_id = $order_detail[0]['rented_product_id'];
                    VendorRentedProducts::where('id',$rented_product_id)->update(['status'=>'On Rent']);
                }
                // DelOrders::where('order_id',$order_id)->delete();
                // Pickup::where('pickup_order_id',$order_id)->delete();
                DelOrders::where('order_id',$order_id)->update(['status'=>'Cancel']);
                Pickup::where('pickup_order_id',$order_id)->update(['status'=>'Cancel']);
            }
            elseif($del_orders[0]['deliverypickup'] == 'Collection')
            {
                $renewal_details = Renewal::where('collection_order_id',$order_id)->get()->toArray();
                foreach($renewal_details as $renewal_detail)
                {
                    $order_details_id = $renewal_detail['order_details_id'];
                    OrderDetails::where('id',$order_details_id)->update(['current_status'=>'Pending']);
                }
                // DelOrders::where('order_id',$order_id)->delete();
                // Renewal::where('collection_order_id',$order_id)->delete();
                DelOrders::where('order_id',$order_id)->update(['status'=>'Cancel']);
                Renewal::where('collection_order_id',$order_id)->update(['status'=>'Cancel']);
            }
            
            // return $order_details;
        }
        public function close_deliverey_fun($order_id)
        {
            // $order_id = $_POST['order_id'];/
            $del_orders = DelOrders::where('order_id',$order_id)->get()->toArray();
            $lead_id = $del_orders[0]['lead_id'];
            lead::where('id',$lead_id)->update(['lead_status'=>'Converted']);
            $order_details = OrderDetails::where('order_id',$order_id)->get()->toArray();
            foreach($order_details as $order_detail)
            {
                $vendor_product_id = $order_detail['vendor_product_id'];
                $vendor_product_details_id = $order_detail['vendor_product_details_id'];
                $vendor_rented_products_id = $order_detail['rented_product_id'];
                $qty = $order_detail['product_qty'];

                VendorProductDetails::where('id',$vendor_product_details_id)->update(['availability_status'=>0,'current_location'=>2]);
                VendorRentedProducts::where('id',$vendor_rented_products_id)->delete();

                $update = DB::update("UPDATE vendor_products SET vendor_products.product_quantity = vendor_products.product_quantity+$qty WHERE id=$vendor_product_id");

            }
            OrderDetails::where('order_id',$order_id)->delete();
            DelOrders::where('order_id',$order_id)->delete();
            // return "Deleted";
        }
        public function close_delivery()
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
             return redirect()->to($url);
            }
            $order_id = $_POST['order_id']; 
            $reason = $_POST['cancellation_reason'];
            $update_data = [
                'status'=>'Closed',
                'cancellation_reason'=>$reason
            ];
            $delOrders = new DelOrders();
            $update_query = $delOrders->where('order_id',$order_id)->update($update_data);
            $lead_ids = DB::select("SELECT lead_id FROM del_orders WHERE order_id = $order_id");
            $lead_details = json_decode(json_encode($lead_ids), true);
            $update_data = [
                'lead_status'=>'Closed',
                'comment'=>$reason
            ];
            $leads = new Lead();
            $update_query = $leads->where('id',$lead_details[0]['lead_id'])->update($update_data);
            return redirect('/approved_orders')->with('message','Order Closed Succesfully');
        }
        //Delete product from order
        public function DeleteOrderProduct($order_details_id,$product_id)
        {
            $isLoggedIn = $this->isLoggedIn();
            if($isLoggedIn == 'false')
            {
                $url = url('/');
             return redirect()->to($url);
            }
            //get product name
            $del_orders = new DelOrders();
            $lead_table = new lead();
            $VendorProducts = new VendorProducts();
            $OrderDetails = new OrderDetails();
            $VendorRentedProducts = new VendorRentedProducts();
            $get_product_data = DB::select("SELECT 
                                            products.product_name as product_name,
                                            del_orders.line_item_1 as line_item_1,
                                            order_details.order_id as order_id,
                                            order_details.vendor_product_id as vendor_product_id,
                                            order_details.product_qty as order_product_quantity,
                                            order_details.rented_product_id as rented_product_id,
                                            del_orders.lead_id as lead_id,
                                            vendor_products.product_quantity as vendor_product_quantity
                                        FROM 
                                            products,del_orders,order_details,vendor_products
                                        where 
                                            products.id=$product_id 
                                        AND del_orders.order_id=order_details.order_id
                                        AND order_details.id = $order_details_id
                                        AND order_details.vendor_product_id=vendor_products.id ");
            $get_product_data = json_decode(json_encode($get_product_data), true);
            $product_name = $get_product_data[0]['product_name'];
            $del_orders_products = $get_product_data[0]['line_item_1']; //existed products in del_orders table
            $order_id = $get_product_data[0]['order_id'];
            $lead_id = $get_product_data[0]['lead_id'];
            $vendor_product_id = $get_product_data[0]['vendor_product_id'];
            $rented_product_id = $get_product_data[0]['rented_product_id'];
            $order_product_quantity = $get_product_data[0]['order_product_quantity'];
            $vendor_product_quantity = $get_product_data[0]['vendor_product_quantity'];

            //update in del_orders table 
            
            $updated_products = str_replace($product_name,"",$del_orders_products);
            $update_del_orders=[
                'line_item_1' => $updated_products
            ];
            $del_orders->where('order_id',$order_id)->update($update_del_orders);
            
            //delete from leads table
            $get_lead_data = DB::select("SELECT * FROM leads where id=$lead_id");
            $get_lead_data = json_decode(json_encode($get_lead_data), true);
            $lead_value = $get_lead_data[0]['lead_value'];
            $equipment_requirement = json_decode($get_lead_data[0]['equipment_requirement']);
            $equipment_qty = json_decode($get_lead_data[0]['equipment_qty']);
            $del_date = json_decode($get_lead_data[0]['del_date']);
            $sale_rental = json_decode($get_lead_data[0]['sale_rental']);
            $offered_rent = json_decode($get_lead_data[0]['offered_rent']);
            $offered_rent_total = json_decode($get_lead_data[0]['offered_rent_total']);
            $deposite = json_decode($get_lead_data[0]['deposite']);
            $deposite_total = json_decode($get_lead_data[0]['deposite_total']);
            $transport = json_decode($get_lead_data[0]['transport']);
            
            $get_key = null;
            $temp_equipment_requirement=$equipment_requirement;
            $temp_equipment_qty = $equipment_qty;
            $temp_del_date = $del_date;
            $temp_sale_rental = $sale_rental;
            $temp_offered_rent = $offered_rent;
            $temp_offered_rent_total = $offered_rent_total;
            $temp_deposite = $deposite;
            $temp_deposite_total = $deposite_total;
            $temp_transport = $transport;
            foreach($equipment_requirement as $key => $value)
            {
                if($product_id==$value)
                {
                    $get_key=$key;
                    unset($temp_equipment_requirement[$key]);
                }
            }
            $total_product_amt = $offered_rent[$get_key]+$deposite[$get_key]+$transport[$get_key];
            $temp_lead_value = $lead_value-$total_product_amt;
            unset($temp_equipment_qty[$key]);
            unset($temp_del_date[$key]);
            unset($temp_del_date[$key]);
            unset($temp_sale_rental[$key]);
            unset($temp_offered_rent[$key]);
            unset($temp_offered_rent_total[$key]);
            unset($temp_deposite[$key]);
            unset($temp_deposite_total[$key]);
            unset($temp_transport[$key]);
            
            $update_lead_data=[
                'equipment_requirement' => json_encode(array_values($temp_equipment_requirement)),
                'equipment_qty' => json_encode(array_values($temp_equipment_qty)),
                'del_date' => json_encode(array_values($temp_del_date)),
                'offered_rent' => json_encode(array_values($temp_offered_rent)),
                'sale_rental' => json_encode(array_values($temp_sale_rental)),
                'offered_rent_total' => json_encode(array_values($temp_offered_rent_total)),
                'deposite' => json_encode(array_values($temp_deposite)),
                'deposite_total' => json_encode(array_values($temp_deposite_total)),
                'transport' => json_encode(array_values($temp_transport)),
                'lead_value' => $temp_lead_value
            ];
            $lead_table->where('id',$lead_id)->update($update_lead_data);

            //update vendor product quantity
            $final_qty = $vendor_product_quantity+$order_product_quantity;
            $update_vendor_products = [
                'product_quantity' => $final_qty
            ];
            $VendorProducts->where('id',$vendor_product_id)->update($update_vendor_products);

            //remove rented id from rented table
            if(isset($rented_product_id))
            {
                $VendorRentedProducts->where('id',$rented_product_id)->delete();
            }

            //Delete or update order_details table row
            $OrderDetails->where('id',$order_details_id)->delete();
            
            return redirect()->back()->with('delete','deleted successfully.')
                                    ->with('product_name',$product_name);

        }
        //date filter search for order mgmt
        public function DateFilter($type)
        {
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            if($type=='orders')
            {
                $isLoggedIn = $this->isLoggedIn();
                if($isLoggedIn == 'false')
                {
                    $url = url('/');
                    return redirect()->to($url);
                }
                $date = date('Y-m-d');
                if(session('role') == "superuser")
                {
                    $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress' ) AND leads.lead_owner = user.id AND leads.creation_date BETWEEN '$start_date' AND '$end_date' ORDER BY leads.creation_date DESC");
                }
                elseif(session('role') == "admin")
                {
                    $user_city = session('user_city');
                    $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated') AND customer_details.citygroup='$user_city' AND leads.lead_owner = user.id AND leads.creation_date BETWEEN '$start_date' AND '$end_date' ORDER BY leads.creation_date DESC");
                }
                elseif(session('role') == "user")
                {
                    $user_city = session('user_city');
                    $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated') AND customer_details.citygroup='$user_city' AND leads.lead_owner = user.id AND leads.creation_date BETWEEN '$start_date' AND '$end_date' ORDER BY leads.creation_date DESC");
                }

                //$leads = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated') ORDER BY leads.creation_date DESC");
                //echo "SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated') ORDER BY leads.creation_date DESC";
                $data['lead_details'] = json_decode(json_encode($leads), true);
                for ($i=0; $i < count($data['lead_details']); $i++) 
                { 
                    $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
                    $equipement_details = array();
                    for ($j=0; $j <count($product); $j++) 
                    { 
                        $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
                        $product_details = json_decode(json_encode($product_details), true);
                        array_push($equipement_details,$product_details[0]['product_name']);
                    }
                    $equipements = json_encode($equipement_details);
                    $data['lead_details'][$i]['equipment_requirement'] = $equipements;
                }
                //print_r($data['lead_details']);
                echo "<script>localStorage['filtered']='today';</script>";
                return view('OrderManagement/viewAllLeads',$data);
            }
            elseif($type=='pending_asgmt')
            {
                $isLoggedIn = $this->isLoggedIn();
                if($isLoggedIn == 'false')
                {
                    $url = url('/');
                return redirect()->to($url);
                }
                $date = date('Y-m-d');
                if(session('role') == "superuser")
                {
                    $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted'  AND leads.lead_owner = user.id AND leads.creation_date BETWEEN '$start_date'AND '$end_date' ORDER BY leads.creation_date DESC");
                }
                elseif(session('role') == "admin")
                {
                    $user_city = session('user_city');
                    $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' AND customer_details.citygroup='$user_city'  AND leads.lead_owner = user.id AND leads.creation_date BETWEEN '$start_date'AND '$end_date'  ORDER BY leads.creation_date DESC");
                }
                elseif(session('role') == "user")
                {
                    $user_city = session('user_city');
                    $leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' AND customer_details.citygroup='$user_city' AND leads.lead_owner = user.id AND leads.creation_date BETWEEN '$start_date'AND '$end_date'  ORDER BY leads.creation_date DESC");
                }

                //$leads = DB::select("SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated') ORDER BY leads.creation_date DESC");
                //echo "SELECT leads.*,customer_details.*,user.username as lead_owner FROM leads,customer_details,user WHERE leads.customer_id = customer_details.cust_id AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated') ORDER BY leads.creation_date DESC";
                $data['lead_details'] = json_decode(json_encode($leads), true);
                for ($i=0; $i < count($data['lead_details']); $i++) 
                { 
                    $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
                    $equipement_details = array();
                    for ($j=0; $j <count($product); $j++) 
                    { 
                        $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
                        $product_details = json_decode(json_encode($product_details), true);
                        array_push($equipement_details,$product_details[0]['product_name']);
                    }
                    $equipements = json_encode($equipement_details);
                    $data['lead_details'][$i]['equipment_requirement'] = $equipements;
                }
                //print_r($data['lead_details']);
                echo "<script>localStorage['filtered']='today';</script>";
                $Countlead = DB::select("SELECT * FROM leads,customer_details WHERE leads.customer_id = customer_details.cust_id AND leads.lead_status = 'Converted' ORDER BY leads.creation_date DESC");
                $count1 = count(json_decode(json_encode($Countlead),true));
                $data['count1'] = $count1;
                return view('OrderManagement/pendingAssignment',$data);
            }
            elseif($type=='vendor_pending_approval')
            {
                $isLoggedIn = $this->isLoggedIn();
                if($isLoggedIn == 'false')
                {
                    $url = url('/');
                return redirect()->to($url);
                }
                $today = date('d-m-Y');
                $start_date = date('d-m-Y',strtotime($start_date));
                $end_date = date('d-m-Y',strtotime($end_date));
                $temp_order_details = DB::select("SELECT * FROM del_orders,order_details WHERE del_orders.order_id=order_details.order_id AND del_orders.order_approval_status='Pending' AND del_orders.status != 'Cancel' AND del_orders.DelDate BETWEEN '$start_date' AND '$end_date'");
                //echo "SELECT * FROM del_orders,order_details WHERE del_orders.order_id=order_details.order_id AND del_orders.order_approval_status='Pending' AND del_orders.DelDate BETWEEN '$start_date' AND '$end_date'"; 
                $data['temp_order_details'] = json_decode(json_encode($temp_order_details),true);
                $prev_order_id = 0;
                $count_status = 1;
                $status = null;
                $data['final_array'] = array();
                $order_details_array = array();
                foreach($data['temp_order_details'] as $order_detail)
                {
                    if($order_detail['order_id'] == $prev_order_id)
                    {
                            if($status=='Accepted')
                            {
                                    $count_status = $count_status + 1;
                                    $temp_data['DelDate'] = $order_detail['DelDate'];
                                    $temp_data['order_id'] = $order_detail['order_id'];
                                    $temp_data['lead_id'] = $order_detail['lead_id'];
                                    $temp_data['shipping_first_name'] = $order_detail['shipping_first_name'];
                                    $temp_data['mobileno'] = $order_detail['mobileno'];
                                    $temp_data['status'] = $order_detail['status'];         
                                    $count = count($order_details_array[$prev_order_id]);
                                    $order_details_array[$prev_order_id][$count] = $temp_data;            
                                    $order_details_array[$prev_order_id]['count'] = $count_status;
                            }
                            $status = $order_detail['status'];
                    }
                    else
                    {
                        $count_status = 1;
                        if($status=='Accepted')
                        {
                            $prev_order_id = $order_detail['order_id'];
                            $temp_data['DelDate'] = $order_detail['DelDate'];
                            $temp_data['order_id'] = $order_detail['order_id'];
                            $temp_data['lead_id'] = $order_detail['lead_id'];
                            $temp_data['shipping_first_name'] = $order_detail['shipping_first_name'];
                            $temp_data['mobileno'] = $order_detail['mobileno'];
                            $temp_data['status'] = $order_detail['status'];                        
                            $order_details_array[$prev_order_id][0] = $temp_data;
                            $order_details_array[$prev_order_id]['count'] = $count_status;
                            $count_status = $count_status + 1;
                        }
                        $status = $order_detail['status'];
                    }
                }
                foreach($order_details_array as $order_detail)
                {
                    $count=count($order_detail);
                    if($order_detail['count'] == $count)
                    {
                        $lead_id = $order_detail[0]['lead_id'];
                        $order_id = $order_detail[0]['order_id'];
                        DB::UPDATE("UPDATE del_orders SET order_approval_status = 'Approved' WHERE order_id = $order_id");
                    }
                }
                $order_details = DB::select("SELECT * FROM del_orders WHERE  del_orders.order_approval_status='Pending' AND del_orders.status != 'Cancel' AND del_orders.DelDate BETWEEN '$start_date' AND '$end_date'");
                $data['order_details'] = json_decode(json_encode($order_details),true);
                echo "<script>localStorage['filtered']='today';</script>";        
                return view('/OrderManagement/pending_for_vendor_approval',$data);
            }
            // elseif($type=='approved_orders')
            // {
            //     $isLoggedIn = $this->isLoggedIn();
            // if($isLoggedIn == 'false')
            // {
            //     $url = url('/');
            //  return redirect()->to($url);
            // }
            // $get_approved_orders = DB::select("SELECT DISTINCT customer_details.customer_name as customer_name,
            //                                         customer_details.primary_contact_no as contact_no,
            //                                         del_orders.order_id as order_id,
            //                                         del_orders.status as del_status,
            //                                         order_details.status as status
            //                                     FROM order_details,del_orders,customer_details
            //                                     where order_details.status='Accepted'
            //                                     AND del_orders.order_approval_status='Approved'
            //                                     AND del_orders.status != 'Closed'
            //                                     AND order_details.order_id=del_orders.order_id
            //                                     AND order_details.customer_id = customer_details.cust_id ORDER BY del_orders.order_id DESC");
            // $data['get_approved_orders'] = json_decode(json_encode($get_approved_orders), true);
            // //print_r($data);
            // return view('OrderManagement/ViewApprovedOrders',$data);
            // }
        }
        //Name FIlter apply
        public function NameFilter(Request $request)
        {
            $date = date('Y-m-d');
            $name = $_POST['name'];
            if(session('role') == "superuser")
            {
                $leads = DB::select("SELECT 
                                        leads.*,
                                        customer_details.*,
                                        user.username as lead_owner 
                                    FROM 
                                        leads,
                                        customer_details,
                                        user 
                                    WHERE 
                                        leads.customer_id = customer_details.cust_id 
                                        AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated' OR leads.lead_status = 'Order Generated' OR leads.lead_status = 'Vendor Assigned' OR leads.lead_status = 'Delivery In Progress' )
                                        AND customer_details.customer_name LIKE '%$name%'
                                        AND leads.lead_owner = user.id 
                                        ORDER BY leads.creation_date DESC");
            }
            elseif(session('role') == "admin")
            {
                $user_city = session('user_city');
                $leads = DB::select("SELECT 
                                        leads.*,
                                        customer_details.*,
                                        user.username as lead_owner 
                                    FROM 
                                        leads,
                                        customer_details,
                                        user 
                                    WHERE 
                                        leads.customer_id = customer_details.cust_id 
                                        AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated') 
                                        AND customer_details.citygroup='$user_city' 
                                        AND customer_details.customer_name LIKE '%$name%'
                                        AND leads.lead_owner = user.id 
                                        ORDER BY leads.creation_date DESC");
            }
            elseif(session('role') == "user")
            {
                $user_city = session('user_city');
                $leads = DB::select("SELECT 
                                        leads.*,
                                        customer_details.*,
                                        user.username as lead_owner 
                                    FROM 
                                        leads,
                                        customer_details,
                                        user 
                                    WHERE 
                                        leads.customer_id = customer_details.cust_id 
                                        AND (leads.lead_status = 'Converted' OR leads.lead_status = 'Mobile Generated') 
                                        AND customer_details.citygroup='$user_city' 
                                        AND customer_details.customer_name LIKE '%$name%'
                                        AND leads.lead_owner = user.id 
                                        ORDER BY leads.creation_date DESC");
            }
            $data['lead_details'] = json_decode(json_encode($leads), true);
            for ($i=0; $i < count($data['lead_details']); $i++) 
            { 
                $product = json_decode($data['lead_details'][$i]['equipment_requirement']);
                $equipement_details = array();
                for ($j=0; $j <count($product); $j++) 
                { 
                    $product_details = DB::select("SELECT product_name FROM products WHERE id = $product[$j]");
                    $product_details = json_decode(json_encode($product_details), true);
                    array_push($equipement_details,$product_details[0]['product_name']);
                }
                $equipements = json_encode($equipement_details);
                $data['lead_details'][$i]['equipment_requirement'] = $equipements;
            }
            //print_r($data['lead_details']);
            $unq_cust = array();
            $total_products = array();
            foreach($data['lead_details'] as $key => $lead_info) 
            {
                $customer_id = $lead_info['customer_id'];
                if(in_array($customer_id,$unq_cust))
                {
                    for($i=0; $i<count($total_products); $i++)
                    {
                        if($total_products[$i]['customer_id'] == $lead_info['customer_id'])
                        {
                            $count = count($total_products[$i]['product_details']);
                            $total_products[$i]['product_details'][$count]['quantity'] = json_decode($lead_info['equipment_qty']);
                            $total_products[$i]['product_details'][$count]['offered_rent'] = json_decode($lead_info['offered_rent']);
                            $total_products[$i]['product_details'][$count]['offered_rent_total'] = json_decode($lead_info['offered_rent_total']);
                            $total_products[$i]['product_details'][$count]['deposit'] = json_decode($lead_info['deposite']);
                            $total_products[$i]['product_details'][$count]['deposit_total'] = json_decode($lead_info['deposite_total']);
                            $total_products[$i]['product_details'][$count]['sale_rental'] = json_decode($lead_info['sale_rental']);
                            $total_products[$i]['product_details'][$count]['transport'] = json_decode($lead_info['transport']);
                        }
                    }
                }
                else
                {
                    array_push($unq_cust,$customer_id);
                    $count = count($total_products);
                    $total_products[$count]['customer_id'] = $lead_info['customer_id'];
                    $total_products[$count]['product_details'][0]['quantity'] = json_decode($lead_info['equipment_qty']);
                    $total_products[$count]['product_details'][0]['offered_rent'] = json_decode($lead_info['offered_rent']);
                    $total_products[$count]['product_details'][0]['offered_rent_total'] = json_decode($lead_info['offered_rent_total']);
                    $total_products[$count]['product_details'][0]['deposit'] = json_decode($lead_info['deposite']);
                    $total_products[$count]['product_details'][0]['deposit_total'] = json_decode($lead_info['deposite_total']);
                    $total_products[$count]['product_details'][0]['transport'] = json_decode($lead_info['transport']);
                    $total_products[$count]['product_details'][0]['sale_rental'] = json_decode($lead_info['sale_rental']);
                }
            }
            //print_r($total_products);
            $product_count = 0;
            $total_rent_product = 0;
            $total_sale_product = 0;
            $total_rent_amt = 0;
            $total_sale_amt = 0;
            $total_deposit = 0;
            $total_transport = 0;
            foreach ($total_products as $total_p)
            {
                $product_details = $total_p['product_details'];
                for ($i=0; $i <count($product_details) ; $i++) { 
                    for ($j=0; $j <count($product_details[$i]['quantity']) ; $j++) { 
                        
                        if($product_details[$i]['sale_rental'][$j]=='Rental')
                        {
                            $total_rent_amt += $product_details[$i]['offered_rent_total'][$j];    
                            $total_rent_product += $product_details[$i]['quantity'][$j];    
                        }
                        elseif($product_details[$i]['sale_rental'][$j]=='Sale')
                        {
                            $total_sale_amt += $product_details[$i]['offered_rent_total'][$j];    
                            $total_sale_product += $product_details[$i]['quantity'][$j];   
                        }
                        $total_transport += $product_details[$i]['transport'][$j];
                        $total_deposit += $product_details[$i]['deposit_total'][$j];
                        $product_count += $product_details[$i]['quantity'][$j];
                    }
                }
            }
            $data['product_count'] = $product_count;
            $data['total_customer'] = count($unq_cust);
            $data['total_amount'] = $total_rent_amt+$total_sale_amt+$total_transport;
            $data['total_rent_product'] = $total_rent_product;
            $data['total_sale_product'] = $total_sale_product;
            $data['total_sale_amt'] = $total_sale_amt;
            $data['total_rent_amt'] = $total_rent_amt;
            $data['total_deposit'] = $total_deposit;
            $data['total_transport'] = $total_transport;
            echo "<script>localStorage['filtered']='today';</script>";
            $data['post_name'] = $name;
            return view('OrderManagement/viewAllLeads',$data);
        }
        //view all filter
        public function ViewAllOrdersFilter(Request $request)
        {
            $match_clause = array();
            $whereCondition = [];
            //$get_min_date = DelOrders::orderBy('order_id','ASC')->first('DelDate');
            $get_min_date = '01-01-2019';
            $form_min_date = date('d-m-Y',strtotime($get_min_date));
            //$get_max_date = DelOrders::orderBy('order_id','DESC')->first('DelDate');
            $get_max_date = Carbon::now()->toDateString();
            $form_max_date = date('d-m-Y',strtotime($get_max_date));

            $get_master_products = DB::table('products')->where('flag','=','Active')->get();

            $customer_name = $request->get('filter_customer_name');
            if(isset($customer_name)){
                $whereCondition1 = ['shipping_first_name','LIKE','%'.$customer_name.'%'];
                array_push($whereCondition,$whereCondition1);
            }
            $location = $request->get('filter_location');
            if(isset($location)){
                $whereCondition1 = ['del_orders.location','LIKE','%'.$location.'%'];
                array_push($whereCondition,$whereCondition1);
            }
            $customer_contact = $request->get('filter_contact_no');
            if(isset($customer_contact)) {
                $whereCondition2 = ['del_orders.mobileno','=',$customer_contact];
                array_push($whereCondition,$whereCondition2);
            }
            $order_type = $request->get('filter_order_type');
            if(isset($order_type) && ($order_type=='Delivery' || $order_type=='Collection' || $order_type=='Pick Up')){
                $whereCondition3 = ['del_orders.deliverypickup','=',$order_type];
                array_push($whereCondition,$whereCondition3);
            }
            $order_id = $request->get('filter_order_id');
            if(isset($order_id)){
                $whereCondition4 = ['del_orders.order_id','=',$order_id];
                array_push($whereCondition,$whereCondition4);
            }
            $delivery_status = $request->get('filter_delivery_status');
            if(isset($delivery_status) && ($delivery_status=='Pending' || $delivery_status=='Assigned' || $delivery_status=='Accepted' || $delivery_status=='InProgress' || $delivery_status=='Delivered')){
                $whereCondition5 = ['del_orders.status','=',$delivery_status];
                array_push($whereCondition,$whereCondition5);
            }
           
            $from_date = $request->get('filter_from_date');
            $end_date = $request->get('filter_end_date');
            $dateArr = [];
            if(isset($from_date ) && isset($end_date)){
                $fromDate = date('d-m-Y',strtotime($from_date));
                $endDate = date('d-m-Y',strtotime($end_date));
                array_push($dateArr,$fromDate);
                array_push($dateArr,$endDate);
                // $form_min_date = date('d-m-Y',strtotime($from_date));
                // $form_max_date = date('d-m-Y',strtotime($end_date));

            }
            $sort_colmun = $request->get('sort_column');
            $sort_val = $request->get('sort_direction');
            $column = 'order_id';
            $direction = 'DESC';
            if(isset($sort_colmun) && isset($sort_val)){
                $column = $sort_colmun;
                $direction = $sort_val;
            }
            $page_no = 1;
            $page = $request->get('page');
            if(isset($page) && $page>1){
                $page_no = $page;
            }
            else{
                $page_no = 1;
            }
            $master_product = $request->get('master_product');
            
            if(isset($master_product) && $master_product!='All'){
                
                $get_del_orders = DB::table('del_orders')
                                    ->join('order_details','del_orders.order_id','=','order_details.order_id')
                                    ->select('del_orders.*','order_details.created_at')
                                    //->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$form_min_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$form_max_date','%d-%m-%Y'))")])
                                    ->when($dateArr,function($query,$dateArr){
                                        $query->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$dateArr[0]','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$dateArr[1]','%d-%m-%Y'))")]);
                                    })
                                    ->where($whereCondition)
                                    ->where('del_orders.deliverypickup','=','Delivery')
                                    ->whereIn('order_details.product_id',$master_product)
                                    ->orderBy($column,$direction)
                                    ->get();
                
                $get_pickup_orders = DB::table('del_orders')
                                    ->join('pickups','del_orders.order_id','=','pickups.pickup_order_id')
                                    ->select('del_orders.*','pickups.created_at')
                                    //->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$form_min_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$form_max_date','%d-%m-%Y'))")])
                                    ->when($dateArr,function($query,$dateArr){
                                        $query->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$dateArr[0]','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$dateArr[1]','%d-%m-%Y'))")]);
                                    })
                                    ->where($whereCondition)
                                    ->where('del_orders.deliverypickup','=','Pick Up')
                                    ->whereIn('pickups.product_id',$master_product)
                                    ->orderBy($column,$direction)
                                    ->get();
                $get_collection_orders = DB::table('del_orders')
                                    ->join('renewals','del_orders.order_id','=','renewals.order_id')
                                    ->select('del_orders.*','renewals.created_at')
                                    //->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$form_min_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$form_max_date','%d-%m-%Y'))")])
                                    ->when($dateArr,function($query,$dateArr){
                                        $query->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$dateArr[0]','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$dateArr[1]','%d-%m-%Y'))")]);
                                    })
                                    ->where($whereCondition)
                                    ->where('del_orders.deliverypickup','=','Collection')
                                    ->whereIn('renewals.product_id',$master_product)
                                    ->orderBy($column,$direction)
                                    ->get();
                                
                $get_all_orders = collect($get_del_orders->merge($get_pickup_orders)->merge($get_collection_orders))->sortByDesc('order_id');
                //print_r($get_all_orders->toArray());
                $get_all_orders = $get_all_orders->paginate(10,null,$page_no);
                $get_products = json_decode(json_encode($get_all_orders), true);
                $products_arr = array();
                foreach($get_all_orders as $key=>$order)
                {
                    $get_order_id = $order->order_id;
                    $del_status = $order->deliverypickup;
                    if($del_status=='Delivery')
                    {
                        if(OrderDetails::where('order_id',$get_order_id)->exists()){
                            $get_product_details = DB::table('order_details')
                                            ->join('products', 'order_details.product_id','=','products.id')
                                            ->join('customer_details','order_details.customer_id','customer_details.cust_id')
                                            ->select('products.product_name','customer_details.customer_type','order_details.created_at')
                                            ->where('order_details.order_id',$get_order_id)->get();
                            $product_name_imp = implode(',',$get_product_details->pluck('product_name')->toArray());
                            $products_arr[$key]['products'] = $product_name_imp;
                            $products_arr[$key]['customer_type'] = $get_product_details[0]->customer_type;
                            $products_arr[$key]['created_at'] = $get_product_details[0]->created_at;
                        }else{
                            $products_arr[$key]['products'] = null;
                            $products_arr[$key]['customer_type'] = null;
                            $products_arr[$key]['created_at'] =null;
                        }
                    }
                    elseif($del_status=='Pick Up')
                    {
                        if(Pickup::where('pickup_order_id',$get_order_id)->exists()){
                            $get_product_details = DB::table('pickups')
                                            ->join('products', 'pickups.product_id','=','products.id')
                                            ->join('order_details','pickups.order_details_id','=','order_details.id')
                                            ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                                            ->select('products.product_name','customer_details.customer_type','pickups.created_at')
                                            ->where('pickups.pickup_order_id',$get_order_id)
                                            ->get();
                            $product_name_imp = implode(',',$get_product_details->pluck('product_name')->toArray());
                            $products_arr[$key]['products'] = $product_name_imp;
                            $products_arr[$key]['customer_type'] = $get_product_details[0]->customer_type;
                            $products_arr[$key]['created_at'] = $get_product_details[0]->created_at;
                        }else{
                            $products_arr[$key]['products'] = null;
                            $products_arr[$key]['customer_type'] = null;
                            $products_arr[$key]['created_at'] = null;
                        }
                    }
                    elseif($del_status=='Collection')
                    {
                        if(Renewal::where('collection_order_id',$get_order_id)->exists()){
                            $get_product_details = DB::table('renewals')
                                            ->join('products', 'renewals.product_id','=','products.id')
                                            ->join('order_details','renewals.order_details_id','=','order_details.id')
                                            ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                                            ->select('products.product_name','customer_details.customer_type','renewals.created_at')
                                            ->where('renewals.collection_order_id',$get_order_id)
                                            ->get();
                            $product_name_imp = implode(',',$get_product_details->pluck('product_name')->toArray());
                            $products_arr[$key]['products'] = $product_name_imp;
                            $products_arr[$key]['customer_type'] = $get_product_details[0]->customer_type;
                            $products_arr[$key]['created_at'] = $get_product_details[0]->created_at;
                        }else{
                            $products_arr[$key]['products'] = null;
                            $products_arr[$key]['customer_type'] = NULL;
                            $products_arr[$key]['created_at'] = null;
                        }
                    }
                    $product_name_imp = implode(',',$get_product_details);
                    $products_arr[$key] = $product_name_imp;
                }
                
            }
            else
            {
                $get_all_orders = DB::table('del_orders')
                    //->join('order_details','del_orders.order_id','=','order_details.order_id')
                    ->select('del_orders.*')
                    //->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$form_min_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$form_max_date','%d-%m-%Y'))")])
                    ->when($dateArr,function($query,$dateArr){
                        $query->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$dateArr[0]','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$dateArr[1]','%d-%m-%Y'))")]);
                    })
                    ->where($whereCondition)
                    //->orderBy('order_id','DESC')
                    ->orderBy($column,$direction)
                    ->paginate(10);
                    //->groupBy('order_id');
                $get_products = json_decode(json_encode($get_all_orders), true);
                $products_arr = array();
                foreach($get_all_orders as $key=>$order)
                {
                    $get_order_id = $order->order_id;
                    $del_status = $order->deliverypickup;
                    if($del_status=='Delivery')
                    {
                        if(OrderDetails::where('order_id',$get_order_id)->exists())
                        {
                            $get_product_details = DB::table('order_details')
                                            ->join('products', 'order_details.product_id','=','products.id')
                                            ->join('customer_details','order_details.customer_id','customer_details.cust_id')
                                            ->select('products.product_name','customer_details.customer_type','order_details.created_at')
                                            ->where('order_id',$get_order_id)
                                            ->get();
                            $product_name_imp = implode(',',$get_product_details->pluck('product_name')->toArray());
                            $products_arr[$key]['products'] = $product_name_imp;
                            $products_arr[$key]['customer_type'] = $get_product_details[0]->customer_type;
                            $products_arr[$key]['created_at'] = $get_product_details[0]->created_at;
                        }
                        else
                        {
                            $products_arr[$key]['products'] = null;
                            $products_arr[$key]['customer_type'] = null;
                            $products_arr[$key]['created_at'] = null;
                        }
                        
                    }
                    elseif($del_status=='Pick Up')
                    {
                        if(Pickup::where('pickup_order_id',$get_order_id)->exists())
                        {
                            $get_product_details = DB::table('pickups')
                                            ->join('products', 'pickups.product_id','=','products.id')
                                            ->join('order_details','pickups.order_details_id','=','order_details.id')
                                            ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                                            ->select('products.product_name','customer_details.customer_type','pickups.created_at')
                                            ->where('pickup_order_id',$get_order_id)
                                            ->get();
                            $product_name_imp = implode(',',$get_product_details->pluck('product_name')->toArray());
                            $products_arr[$key]['products'] = $product_name_imp;
                            $products_arr[$key]['customer_type'] = $get_product_details[0]->customer_type;
                            $products_arr[$key]['created_at'] = $get_product_details[0]->created_at;
                        }
                        else{
                            $products_arr[$key]['products'] = null;
                            $products_arr[$key]['customer_type'] = null;
                            $products_arr[$key]['created_at'] = null;
                        }
                            
                    }
                    elseif($del_status=='Collection')
                    {
                        if(Renewal::where('collection_order_id',$get_order_id)->exists())
                        {
                            $get_product_details = DB::table('renewals')
                                            ->join('products', 'renewals.product_id','=','products.id')
                                            ->join('order_details','renewals.order_details_id','=','order_details.id')
                                            ->join('customer_details','order_details.customer_id','=','customer_details.cust_id')
                                            ->select('products.product_name','customer_details.customer_type','renewals.created_at')
                                            ->where('collection_order_id',$get_order_id)
                                            ->get();
                            $product_name_imp = implode(',',$get_product_details->pluck('product_name')->toArray());
                            $products_arr[$key]['products'] = $product_name_imp;
                            $products_arr[$key]['customer_type'] = $get_product_details[0]->customer_type;
                            $products_arr[$key]['created_at'] = $get_product_details[0]->created_at;
                        }else{
                            $products_arr[$key]['products'] = null;
                            $products_arr[$key]['customer_type'] = null;
                            $products_arr[$key]['created_at'] = null;
                        }
                    }
                    //dd($get_product_details);
                    // $product_name_imp = implode(',',$get_product_details->pluck('product_name')->toArray());
                    // $products_arr[$key]['products'] = $product_name_imp;
                    // $products_arr[$key]['customer_type'] = $get_product_details[0]->customer_type;
                    // $products_arr[$key]= $product_name_imp;
                    
                }
            }
            //get products of orders
            // $get_all_orders = DB::table('del_orders')
            //         //->join('order_details','del_orders.order_id','=','order_details.order_id')
            //         ->select('del_orders.*')
            //         ->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$form_min_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$form_max_date','%d-%m-%Y'))")])
            //         ->where($whereCondition)
            //         //->orderBy('order_id','DESC')
            //         ->orderBy($column,$direction)
            //         ->paginate(10);
            //         //->groupBy('order_id');
            //dd($master_product)
            
            $filter_arr = [ "cust_name"=>$customer_name,
                            "location"=>$location,
                            "cust_no"=>$customer_contact,
                            "order_type"=>$order_type,
                            "order_id"=>$order_id,
                            "master_product"=>$master_product,
                            "delivery_status"=>$delivery_status,
                            "from_date"=>$from_date,
                            "end_date"=>$end_date,
                            "sort_column"=>$sort_colmun,
                            "sort_val"=>$sort_val];
            $submit = $request->get('btn_submit');
            
            if($submit=="export_excel")
            {
               
                $get_all_orders = DB::table('del_orders')
                                    ->select('DelDate','order_id','shipping_first_name','mobileno','deliverypickup','status')
                                    ->whereBetween(DB::raw("(STR_TO_DATE(DelDate,'%d-%m-%Y'))"),[DB::raw("(STR_TO_DATE('$form_min_date','%d-%m-%Y'))"),DB::raw("(STR_TO_DATE('$form_max_date','%d-%m-%Y'))")])
                                    ->where($whereCondition)
                                    //->orderBy('order_id','DESC')
                                    ->orderBy($column,$direction)
                                    ->get()
                                    ->toArray();
                $jsonDecoded = json_decode(json_encode($get_all_orders),true);
                //print_r($jsonDecoded);
                ob_end_clean(); // this
                ob_start(); // and this
                return Excel::download(new OrdersExport($jsonDecoded), 'AllOrders.xlsx');
            }
            else
            {
                return view('OrderManagement/view_all_orders',compact('get_all_orders','products_arr','get_master_products'),compact('filter_arr'));
            }
            
        }
        function array_sort_by_column(&$array, $column, $direction = SORT_ASC) {
            $reference_array = array();
        
            foreach($array as $key => $row) {
                $reference_array[$key] = $row[$column];
            }
        
            array_multisort($reference_array, $direction, $array);
        }
        public function ViewAllOrdersExport()
        { 
            ob_end_clean(); // this
            ob_start(); // and this
            return Excel::download(new OrdersExport, 'invoices.xlsx');
        }
        public function location_populate(Request $request)
        {
            $query = $request->get('query');
            $filterResult = DB::select("SELECT DISTINCT location FROM del_orders WHERE location LIKE '%$query%' ");
            $filterResult = json_decode(json_encode($filterResult),true);
            $data = array();
            foreach($filterResult as $key => $result)
            {
                $data[] = $result['location'];
            }
            return response()->json($data);
        }
    }
?>