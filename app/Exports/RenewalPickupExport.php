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

class RenewalPickupExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $export_val;
    protected $start_date;
    protected $end_date;
    protected $text_val;

    function __construct($export_val,$start_date,$end_date,$text_val) {
            $this->export_val = $export_val;
            $this->end_date = $end_date;
            $this->start_date = $start_date;
            $this->text_val = $text_val;
    }
    // public function headings():array{
    //     return[
    //         'Due Date',
    //         'Customer Name',
    //         'Address',
    //         'Contact Number',
    //         'Products',
    //         'Lead Owner',
    //         'Total Amount',
    //         'Sr. No.',
    //         'Order ID',	
    //         'Product Name',	
    //         'Vendor Name',
    //         'Quantity',
    //         'Rent',
    //         'Deposite',
    //         'Due Months',
    //         'Total Due Rent'
    //     ];
    // } 
    // public function collection()
    // {
    
    // }

    public function view(): View
    {
        $export_val = $this->export_val;
        if($export_val=="Today")
        {
            $date = date('Y-m-d');
            $whereClause = "AND order_details.pickup_date ='$date' ";
        }
        if($export_val=="Tomorrow")
        {
            $date = date('Y-m-d',strtotime('+1 days'));
            $whereClause = "AND order_details.pickup_date ='$date' ";
        }
        if($export_val=="Overdue")
        {
            $date = date('Y-m-d');
            $whereClause = "AND order_details.pickup_date <'$date' ";
        }
        if($export_val=="3_days")
        {
            $date = date('Y-m-d',strtotime("+1 days"));
            $end_date = date('Y-m-d',strtotime("+3 days"));
            $whereClause = "AND order_details.pickup_date BETWEEN '$date' AND '$end_date' ";
        }
        if($export_val == "All")
        {
            $get_start_date = DB::table('order_details')->first('pickup_date');
            $start_date = $get_start_date->pickup_date;
            $end_date = date('Y-m-d');
            $whereClause = "AND order_details.pickup_date BETWEEN '$start_date' AND '$end_date'";
        }
        if($export_val == "Datewise")
        {
            $start_date = $this->start_date;
            $end_date = $this->end_date;
            $whereClause = "AND order_details.pickup_date BETWEEN '$start_date' AND '$end_date' ";
        }
        if($export_val == "Textwise")
        {
            $text_val = $this->text_val;
            $whereClause = "AND (customer_details.customer_name LIKE '%$text_val%'
            OR customer_details.primary_contact_no LIKE '%$text_val%') ";
        }
        if($export_val == "PatientSearch")
        {
            $text_val = $this->text_val;
            $whereClause = "AND leads.patient_name LIKE '%$text_val%' ";
        }
        if($export_val == "AddressSearch")
        {
            $text_val = $this->text_val;
            $whereClause = "AND (customer_details.address_line_1 LIKE '%$text_val%' OR customer_details.address_line_2 LIKE '%$text_val%' OR customer_details.location LIKE '%$text_val%' OR customer_details.landmark LIKE '%$text_val%' OR customer_details.area LIKE '%$text_val%')";
        }
        
        $renewal_pickup_info = DB::select("SELECT 
                                                    order_details.id as order_details_id,
                                                    order_details.*,
                                                    customer_details.*,
                                                    products.product_name as product_name,
                                                    del_orders.order_id as order_id,
                                                    del_orders.DelDate as DelDate,
                                                    user.username as username,
                                                    vendor_details.registered_name as vendor_name
                                                FROM order_details,customer_details,del_orders,products,leads,user,vendor_details
                                                where order_details.order_id=del_orders.order_id
                                                    AND order_details.customer_id = customer_details.cust_id
                                                    AND order_details.product_id=products.id
                                                    AND del_orders.lead_id = leads.id
                                                    AND del_orders.status != 'Cancel'
                                                    AND del_orders.status != 'Rejected'
                                                    AND leads.lead_owner = user.id
                                                    AND order_details.vendor_id=vendor_details.id
                                                    AND order_details.sale_rental='Rental'
                                                    $whereClause
                                                    AND (order_details.current_status='Pending'
                                                        OR order_details.current_status='Pending Renew'
                                                        OR order_details.current_status='Renewed Online' 
                                                        OR order_details.current_status='Renewed')
                                                    ORDER BY order_details.pickup_date ASC");
            $data['renewal_pickup_info'] = json_decode(json_encode($renewal_pickup_info),true);
            // print_r($data['renewal_pickup_info']);
            $cust_id_array = array();
            $customer_products_details = array();
            foreach($data['renewal_pickup_info'] as $renewal_pickup_info)
            {
                $prod_name = $renewal_pickup_info['product_name'];
                if(in_array($renewal_pickup_info['customer_id'],$cust_id_array))
                {

                    //print_r($customer_products_details);
                    for($i=0; $i<count($customer_products_details); $i++)
                    {
                        // echo "<br>customer_id".$customer_products_details[$i]['customer_id'];
                        // echo "<br>cust_id".$renewal_pickup_info['customer_id'];
                        if($customer_products_details[$i]['customer_id'] == $renewal_pickup_info['customer_id'])
                        {
                            $count = count($customer_products_details[$i]['product_details']);
                            //print_r($customer_products_details[$i]['product_details']);
                            //echo $count;
                            $prod_name = $renewal_pickup_info['product_name'];
                            //monthly rent 
                            $temp_product_rent = $renewal_pickup_info['product_rent'];
                            $temp_today = date('Y-m-d');
                            $temp_pickup_date = $renewal_pickup_info['pickup_date'];
                            // $temp_y1 = date('Y',strtotime($temp_today));
                            // $temp_y2 = date('Y',strtotime($temp_pickup_date));
                            // $temp_m1 = date('m',strtotime($temp_today));
                            // $temp_m2 = date('m',strtotime($temp_pickup_date));
                            // $month_count = abs((($temp_y2-$temp_y1)*12)+($temp_m2-$temp_m1));
                            // if($month_count==0){
                            //     $month_count =1;
                            // }

                            //new month count
                            $d1 = new DateTime(date('Y-m-d H:i:s',));
                            $d2 = new DateTime($temp_pickup_date);
                            $interval = $d1->diff($d2);
                            $diffInSeconds = $interval->s; //45
                            $diffInMinutes = $interval->i; //23
                            $diffInHours   = $interval->h; //8
                            $diffInDays    = $interval->d; //21
                            $diffInMonths  = $interval->m; //4
                            $diffInYears   = $interval->y; //1
                            $month_count = $diffInMonths;
                            if($diffInDays>0){
                                $month_count =$month_count+1;
                            }
                            if($month_count==0)
                            {
                                $month_count = 1;
                            }
                            $total_month_rent = $month_count*$temp_product_rent;

                            $customer_products_details[$i]['product_details'][$count]['product_name'] = $prod_name;
                            $customer_products_details[$i]['product_details'][$count]['vendor_name'] = $renewal_pickup_info['vendor_name'];
                            $customer_products_details[$i]['product_details'][$count]['pickup_date'] = $renewal_pickup_info['pickup_date'];
                            $customer_products_details[$i]['product_details'][$count]['order_details_id'] = $renewal_pickup_info['order_details_id'];
                            $customer_products_details[$i]['product_details'][$count]['vendor_id'] = $renewal_pickup_info['vendor_id'];
                            $customer_products_details[$i]['product_details'][$count]['vendor_product_id'] = $renewal_pickup_info['vendor_product_id'];
                            $customer_products_details[$i]['product_details'][$count]['product_qty'] = $renewal_pickup_info['product_qty'];
                            $customer_products_details[$i]['product_details'][$count]['order_id'] = $renewal_pickup_info['order_id'];
                            $customer_products_details[$i]['product_details'][$count]['product_rent'] = $renewal_pickup_info['product_rent'];
                            $customer_products_details[$i]['product_details'][$count]['product_deposite'] = $renewal_pickup_info['product_deposite'];
                            $customer_products_details[$i]['product_details'][$count]['transport'] = $renewal_pickup_info['transport'];
                            $customer_products_details[$i]['product_details'][$count]['product_id'] = $renewal_pickup_info['product_id'];
                            $customer_products_details[$i]['product_details'][$count]['order_id'] = $renewal_pickup_info['order_id'];
                            $customer_products_details[$i]['product_details'][$count]['DelDate'] = $renewal_pickup_info['DelDate'];
                            $customer_products_details[$i]['product_details'][$count]['current_status'] = $renewal_pickup_info['current_status'];
                            $customer_products_details[$i]['product_details'][$count]['month_count'] = $month_count;
                            $customer_products_details[$i]['product_details'][$count]['total_month_rent'] = $total_month_rent;
                            // $customer_products_detail['product_details'][$count] = $renewal_pickup_info['product_name'];
                            //product quantity wise show products
                            $temp_product_quantity = $renewal_pickup_info['product_qty'];
                            $quantity_product = array();
                            if($temp_product_quantity>1)
                            {
                                $temp_product_deposite = $renewal_pickup_info['product_deposite'];
                                $divided_product_rent = $temp_product_rent/$temp_product_quantity;
                                $divided_product_deposite = $temp_product_deposite/$temp_product_quantity;
                                
                                for ($j=0; $j <$temp_product_quantity; $j++) 
                                { 
                                    $quantity_product[$j]['product_name'] = $prod_name;
                                    $quantity_product[$j]['pickup_date'] = $renewal_pickup_info['pickup_date'];
                                    $quantity_product[$j]['order_details_id'] = $renewal_pickup_info['order_details_id'];
                                    $quantity_product[$j]['vendor_id'] = $renewal_pickup_info['vendor_id'];
                                    $quantity_product[$j]['vendor_product_id'] = $renewal_pickup_info['vendor_product_id'];
                                    $quantity_product[$j]['product_qty'] = 1;
                                    $quantity_product[$j]['order_id'] = $renewal_pickup_info['order_id'];
                                    $quantity_product[$j]['product_rent'] = $divided_product_rent;
                                    $quantity_product[$j]['product_deposite'] = $divided_product_deposite;
                                }
                            }
                            $customer_products_details[$i]['product_details'][$count]['quantity_wise_products'] = $quantity_product;
                        }
                    }
                }
                else
                {
                    array_push($cust_id_array,$renewal_pickup_info['customer_id']);
                    $count = count($customer_products_details);

                    //$customer_address = $renewal_pickup_info['address_line_1'].','.$renewal_pickup_info['address_line_2'].','.$renewal_pickup_info['area'].','.$renewal_pickup_info['landmark'].','.$renewal_pickup_info['location'].','.$renewal_pickup_info['city'].','.$renewal_pickup_info['pincode'];
                    $customer_address = $renewal_pickup_info['address_line_1'].', '.$renewal_pickup_info['address_line_2'].', '.$renewal_pickup_info['area'].', '.$renewal_pickup_info['landmark'].', '.$renewal_pickup_info['location'].', '.$renewal_pickup_info['city'].', '.$renewal_pickup_info['pincode'];
                    //monthly rent 
                    $temp_product_rent = $renewal_pickup_info['product_rent'];
                    $temp_today = date('Y-m-d');
                    $temp_pickup_date = $renewal_pickup_info['pickup_date'];
                    // $temp_y1 = date('Y',strtotime($temp_today));
                    // $temp_y2 = date('Y',strtotime($temp_pickup_date));
                    // $temp_m1 = date('m',strtotime($temp_today));
                    // $temp_m2 = date('m',strtotime($temp_pickup_date));
                    // $month_count = abs((($temp_y2-$temp_y1)*12)+($temp_m2-$temp_m1));
                    // if($month_count==0){
                    //     $month_count =1;
                    // }

                    //new month count
                    $d1 = new DateTime(date('Y-m-d H:i:s',));
                    $d2 = new DateTime($temp_pickup_date);
                    $interval = $d1->diff($d2);
                    $diffInSeconds = $interval->s; //45
                    $diffInMinutes = $interval->i; //23
                    $diffInHours   = $interval->h; //8
                    $diffInDays    = $interval->d; //21
                    $diffInMonths  = $interval->m; //4
                    $diffInYears   = $interval->y; //1
                    $month_count = $diffInMonths;
                    if($diffInDays>0){
                        $month_count =$month_count+1;
                    }
                    if($month_count==0)
                    {
                        $month_count = 1;
                    }
                    $total_month_rent = $month_count*$temp_product_rent;
                    
                    $customer_products_details[$count]['customer_id'] = $renewal_pickup_info['customer_id'];
                    $customer_products_details[$count]['customer_name'] = $renewal_pickup_info['customer_name'];
                    $customer_products_details[$count]['username'] = $renewal_pickup_info['username'];
                    $customer_products_details[$count]['customer_contact_no'] = $renewal_pickup_info['primary_contact_no'];
                    $customer_products_details[$count]['customer_log'] = $renewal_pickup_info['comment'];
                    $customer_products_details[$count]['customer_address'] = $customer_address;
                    $customer_products_details[$count]['product_details'][0]['vendor_name'] = $renewal_pickup_info['vendor_name'];
                    $customer_products_details[$count]['product_details'][0]['product_name'] = $renewal_pickup_info['product_name'];
                    $customer_products_details[$count]['product_details'][0]['pickup_date'] = $renewal_pickup_info['pickup_date'];
                    $customer_products_details[$count]['product_details'][0]['order_details_id'] = $renewal_pickup_info['order_details_id'];
                    $customer_products_details[$count]['product_details'][0]['vendor_id'] = $renewal_pickup_info['vendor_id'];
                    $customer_products_details[$count]['product_details'][0]['vendor_product_id'] = $renewal_pickup_info['vendor_product_id'];
                    $customer_products_details[$count]['product_details'][0]['product_qty'] = $renewal_pickup_info['product_qty'];
                    $customer_products_details[$count]['product_details'][0]['product_rent'] = $renewal_pickup_info['product_rent'];
                    $customer_products_details[$count]['product_details'][0]['product_deposite'] = $renewal_pickup_info['product_deposite'];
                    $customer_products_details[$count]['product_details'][0]['transport'] = $renewal_pickup_info['transport'];
                    $customer_products_details[$count]['product_details'][0]['product_id'] = $renewal_pickup_info['product_id'];
                    $customer_products_details[$count]['product_details'][0]['order_id'] = $renewal_pickup_info['order_id'];
                    $customer_products_details[$count]['product_details'][0]['DelDate'] = $renewal_pickup_info['DelDate'];
                    $customer_products_details[$count]['product_details'][0]['current_status'] = $renewal_pickup_info['current_status'];
                    $customer_products_details[$count]['product_details'][0]['month_count'] = $month_count;
                    $customer_products_details[$count]['product_details'][0]['total_month_rent'] = $total_month_rent;

                    //product quantity wise show products
                    $temp_product_quantity = $renewal_pickup_info['product_qty'];
                    $quantity_product = array();
                    if($temp_product_quantity>1)
                    {
                        $temp_product_deposite = $renewal_pickup_info['product_deposite'];
                        $divided_product_rent = $temp_product_rent/$temp_product_quantity;
                        $divided_product_deposite = $temp_product_deposite/$temp_product_quantity;
                        
                        for ($j=0; $j <$temp_product_quantity; $j++) 
                        { 
                            $quantity_product[$j]['product_name'] = $renewal_pickup_info['product_name'];
                            $quantity_product[$j]['pickup_date'] = $renewal_pickup_info['pickup_date'];
                            $quantity_product[$j]['order_details_id'] = $renewal_pickup_info['order_details_id'];
                            $quantity_product[$j]['vendor_id'] = $renewal_pickup_info['vendor_id'];
                            $quantity_product[$j]['vendor_product_id'] = $renewal_pickup_info['vendor_product_id'];
                            $quantity_product[$j]['product_qty'] = 1;
                            $quantity_product[$j]['order_id'] = $renewal_pickup_info['order_id'];
                            $quantity_product[$j]['product_rent'] = $divided_product_rent;
                            $quantity_product[$j]['product_deposite'] = $divided_product_deposite;
                        }
                    }
                    $customer_products_details[$count]['product_details'][0]['quantity_wise_products'] = $quantity_product;
                }
            }
            $data['customer_products_details'] = $customer_products_details;
            //get total all of need
            $total_equipment = 0;
            $total_due_amount = 0;
            
            for ($i=0; $i<count($customer_products_details); $i++) { 
                $total_equipment+=count($customer_products_details[$i]['product_details']);
                for ($j=0; $j<count($customer_products_details[$i]['product_details']); $j++)
                {
                    $total_due_amount += $customer_products_details[$i]['product_details'][$j]['total_month_rent'];
                }
            }
            $data['total_customer']=count($cust_id_array);
            $data['total_equipment']=$total_equipment;
            $data['total_due_amount']=$total_due_amount;

        return view('export_views.renewal_pickup_excel',$data);
    }
}