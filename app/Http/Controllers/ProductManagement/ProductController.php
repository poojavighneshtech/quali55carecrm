<?php

namespace App\Http\Controllers\ProductManagement;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\VendorRegister;
use App\Models\UserRegister;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Mail;

class ProductController extends Controller
{
    public function isLoggedIn()
    {
        $data = session('isLoggedIn');
        return $data;
    }
    public function add_new_product()
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
            return redirect()->to($url);
        }
        if($_SERVER['REQUEST_METHOD'] == 'GET')
        {
            return view('ProductManagement/add_new_product');
        }
        else
        {
            $product_name = $_POST['product_name'];
            $product_details = $_POST['product_details'];
            $product_type = $_POST['product_type'];
            $product_deposite = $_POST['product_deposite'];
            $product_rent = $_POST['product_rent'];
            $min_rent_percentage = $_POST['min_rent_percentage'];
            DB::insert("INSERT INTO products (product_name,product_details,product_type,product_deposite,product_rent,min_rent_percentage) values('$product_name','$product_details','$product_type',$product_deposite,$product_rent,$min_rent_percentage)");
            return redirect('/add_new_product')->with('message','New Product Successfully added');
        }
    }
    //View Master Products....
    public function view_master_products()
    {
        $product_details = DB::select("SELECT * FROM products");
        $data['product_details'] = json_decode(json_encode($product_details), true);
        return view('ProductManagement/master_products',$data);
    }
    //Pending Products------------
    public function product_request()
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
            return redirect()->to($url);
        }

        $main_cities = DB::select("SELECT * FROM main_cities");
        $data['main_cities'] = \json_decode(\json_encode($main_cities), true);
        
        // $vendor_details = DB::select("SELECT vendor_details.registered_name as registered_name, vendor_products.id as id,vendor_products.product_quantity as product_quantity, vendor_products.product_id as product_id, vendor_products.product_brand as product_details, vendor_products.product_rent_requested as product_rent_requested, vendor_products.warehouse_id as warehouse_id, products.product_name as product_name, vendor_warehouse.wh_city as wh_city, vendor_warehouse.wh_name as wh_name FROM vendor_products,vendor_details,products,vendor_warehouse WHERE vendor_products.vendor_id = vendor_details.id AND vendor_products.status = 'Pending' AND vendor_products.product_id = products.id And vendor_products.warehouse_id = vendor_warehouse.id");
        $vendor_details = DB::table('vendor_details')
                                ->join('vendor_products','vendor_products.vendor_id','=','vendor_details.id')
                                ->join('products','products.id','=','vendor_products.product_id')
                                ->join('vendor_warehouse','vendor_warehouse.id','=','vendor_products.warehouse_id')
                                ->select(
                                    'vendor_details.registered_name as registered_name',
                                    'vendor_products.id as id',
                                    'vendor_products.product_quantity as product_quantity',
                                    'vendor_products.product_id as product_id',
                                    'vendor_products.product_brand as product_details',
                                    'vendor_products.product_rent_requested as product_rent_requested',
                                    'vendor_products.warehouse_id as warehouse_id',
                                    'products.product_name as product_name',
                                    'vendor_warehouse.wh_city as wh_city',
                                    'vendor_warehouse.wh_name as wh_name'
                                )
                                ->where('vendor_products.status','Pending')
                                ->when(session('city_based_access') == '1',function($query){
                                    $query->where('vendor_warehouse.wh_city',session('user_city'));
                                })
                                ->get();
        $data['vendor_details'] = json_decode(json_encode($vendor_details), true);
        $data['vendor_product_details'] = array();
        $data['vendor_product_counts'] = array();
        for ($i=0; $i <count($data['vendor_details']); $i++) 
        {
            $temp = array();
            $products = array();
            $vendor_name = $data['vendor_details'][$i]['registered_name'];
            if(in_array($vendor_name, array_column($data['vendor_product_counts'], 'vendor_name')))
            {
                $vendor_count_key = array_search($vendor_name, array_column($data['vendor_product_counts'], 'vendor_name'));
                $data['vendor_product_counts'][$vendor_count_key]['count'] = $data['vendor_product_counts'][$vendor_count_key]['count']+1;
                //$product_count_key = array_search($vendor_name, array_column($data['vendor_product_counts'][$vendor_count_key]['products'], 'product_name'));
                //$product_count_key = array_search($vendor_name, array_column($data['vendor_product_counts'][$vendor_count_key],'products'));
                $product_count_key = count($data['vendor_product_counts'][$vendor_count_key]['products']);
                $product_count_key = $product_count_key;
                //echo $product_count_key;
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_id'] = $data['vendor_details'][$i]['id'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_name'] = $data['vendor_details'][$i]['product_name'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_details'] = $data['vendor_details'][$i]['product_details'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_rent_requested'] = $data['vendor_details'][$i]['product_rent_requested'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_quantity'] = $data['vendor_details'][$i]['product_quantity'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['warehouse_id'] = $data['vendor_details'][$i]['warehouse_id'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['wh_city'] = $data['vendor_details'][$i]['wh_city'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['wh_name'] = $data['vendor_details'][$i]['wh_name'];
            }
            else
            {
                $temp['vendor_name'] = $vendor_name;
                $temp['count'] = 1;
                $products[0]['product_id'] = $data['vendor_details'][$i]['id'];
                $products[0]['product_name'] = $data['vendor_details'][$i]['product_name'];
                $products[0]['product_details'] = $data['vendor_details'][$i]['product_details'];
                $products[0]['product_rent_requested'] = $data['vendor_details'][$i]['product_rent_requested'];
                $products[0]['product_quantity'] = $data['vendor_details'][$i]['product_quantity'];
                $products[0]['warehouse_id'] = $data['vendor_details'][$i]['warehouse_id'];
                $products[0]['wh_city'] = $data['vendor_details'][$i]['wh_city'];
                $products[0]['wh_name'] = $data['vendor_details'][$i]['wh_name'];
                $temp['products'] = $products;
                array_push($data['vendor_product_counts'],$temp);
            }            
        }     
        //print_r($data['vendor_product_counts']);
        echo "<script>localStorage['filtered']='Mumbai';</script>";
        return view('ProductManagement/product_request',$data);
    }
    public function product_request_citywise($city)
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
            return redirect()->to($url);
        }

        $main_cities = DB::select("SELECT * FROM main_cities");
        $data['main_cities'] = \json_decode(\json_encode($main_cities), true);

        $vendor_details = DB::select("SELECT vendor_details.registered_name as registered_name, vendor_products.id as id,vendor_products.product_quantity as product_quantity, vendor_products.product_id as product_id, vendor_products.product_brand as product_details, vendor_products.product_rent_requested as product_rent_requested, vendor_products.warehouse_id as warehouse_id, products.product_name as product_name, vendor_warehouse.wh_city as wh_city, vendor_warehouse.wh_name as wh_name FROM vendor_products,vendor_details,products,vendor_warehouse WHERE vendor_products.vendor_id = vendor_details.id AND vendor_products.status = 'Pending' AND vendor_warehouse.wh_city = '$city' AND vendor_products.product_id = products.id And vendor_products.warehouse_id = vendor_warehouse.id");
        
        $vendor_details = DB::table('vendor_details')
                                ->join('vendor_products','vendor_products.vendor_id','=','vendor_details.id')
                                ->join('products','products.id','=','vendor_products.product_id')
                                ->join('vendor_warehouse','vendor_warehouse.id','=','vendor_products.warehouse_id')
                                ->select(
                                    'vendor_details.registered_name as registered_name',
                                    'vendor_products.id as id',
                                    'vendor_products.product_quantity as product_quantity',
                                    'vendor_products.product_id as product_id',
                                    'vendor_products.product_brand as product_details',
                                    'vendor_products.product_rent_requested as product_rent_requested',
                                    'vendor_products.warehouse_id as warehouse_id',
                                    'products.product_name as product_name',
                                    'vendor_warehouse.wh_city as wh_city',
                                    'vendor_warehouse.wh_name as wh_name'
                                )
                                ->where('vendor_products.status','Pending')
                                ->when(session('city_based_access') == '1',function($query){
                                    $query->where('vendor_warehouse.wh_city',session('user_city'));
                                })
                                ->where('vendor_warehouse.wh_city',$city)
                                ->get();
        $data['vendor_details'] = json_decode(json_encode($vendor_details), true);
        $data['vendor_product_details'] = array();
        $data['vendor_product_counts'] = array();
        for ($i=0; $i <count($data['vendor_details']); $i++) 
        {
            $temp = array();
            $products = array();
            $vendor_name = $data['vendor_details'][$i]['registered_name'];
            if(in_array($vendor_name, array_column($data['vendor_product_counts'], 'vendor_name')))
            {
                $vendor_count_key = array_search($vendor_name, array_column($data['vendor_product_counts'], 'vendor_name'));
                $data['vendor_product_counts'][$vendor_count_key]['count'] = $data['vendor_product_counts'][$vendor_count_key]['count']+1;
                //$product_count_key = array_search($vendor_name, array_column($data['vendor_product_counts'][$vendor_count_key]['products'], 'product_name'));
                //$product_count_key = array_search($vendor_name, array_column($data['vendor_product_counts'][$vendor_count_key],'products'));
                $product_count_key = count($data['vendor_product_counts'][$vendor_count_key]['products']);
                $product_count_key = $product_count_key;
                //echo $product_count_key;
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_id'] = $data['vendor_details'][$i]['id'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_name'] = $data['vendor_details'][$i]['product_name'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_details'] = $data['vendor_details'][$i]['product_details'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_rent_requested'] = $data['vendor_details'][$i]['product_rent_requested'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_quantity'] = $data['vendor_details'][$i]['product_quantity'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['warehouse_id'] = $data['vendor_details'][$i]['warehouse_id'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['wh_city'] = $data['vendor_details'][$i]['wh_city'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['wh_name'] = $data['vendor_details'][$i]['wh_name'];
            }
            else
            {
                $temp['vendor_name'] = $vendor_name;
                $temp['count'] = 1;
                $products[0]['product_id'] = $data['vendor_details'][$i]['id'];
                $products[0]['product_name'] = $data['vendor_details'][$i]['product_name'];
                $products[0]['product_details'] = $data['vendor_details'][$i]['product_details'];
                $products[0]['product_rent_requested'] = $data['vendor_details'][$i]['product_rent_requested'];
                $products[0]['product_quantity'] = $data['vendor_details'][$i]['product_quantity'];
                $products[0]['warehouse_id'] = $data['vendor_details'][$i]['warehouse_id'];
                $products[0]['wh_city'] = $data['vendor_details'][$i]['wh_city'];
                $products[0]['wh_name'] = $data['vendor_details'][$i]['wh_name'];
                $temp['products'] = $products;
                array_push($data['vendor_product_counts'],$temp);
            }            
        }     
        //print_r($data['vendor_product_counts']);
        return view('ProductManagement/product_request',$data);
    }
    //Approved Products----------------
    public function product_approved_rent()
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
            return redirect()->to($url);
        }
        
        $main_cities = DB::select("SELECT * FROM main_cities");
        $data['main_cities'] = \json_decode(\json_encode($main_cities), true);

        // $vendor_details = DB::select("SELECT vendor_details.registered_name as registered_name, vendor_products.id as id,vendor_products.product_quantity as product_quantity, vendor_products.product_id as product_id, vendor_products.product_brand as product_details, vendor_products.product_rent_approved as product_rent_approved, products.product_name as product_name, vendor_products.warehouse_id as warehouse_id, vendor_warehouse.wh_city as wh_city, vendor_warehouse.wh_name as wh_name FROM vendor_products,vendor_details,products,vendor_warehouse WHERE vendor_products.vendor_id = vendor_details.id AND vendor_products.status = 'Approved' AND vendor_products.product_id = products.id AND vendor_products.warehouse_id = vendor_warehouse.id");
        $vendor_details = DB::table('vendor_details')
                                ->join('vendor_products','vendor_products.vendor_id','=','vendor_details.id')
                                ->join('products','products.id','=','vendor_products.product_id')
                                ->join('vendor_warehouse','vendor_warehouse.id','=','vendor_products.warehouse_id')
                                ->select(
                                    'vendor_details.registered_name as registered_name',
                                    'vendor_products.id as id',
                                    'vendor_products.product_quantity as product_quantity',
                                    'vendor_products.product_id as product_id',
                                    'vendor_products.product_brand as product_details',
                                    'vendor_products.product_rent_approved as product_rent_approved',
                                    'products.product_name as product_name',
                                    'vendor_products.warehouse_id as warehouse_id',
                                    'vendor_warehouse.wh_city as wh_city',
                                    'vendor_warehouse.wh_name as wh_name'
                                )
                                ->where('vendor_products.status','Approved')
                                // ->where('vendor_warehouse.wh_city',$city)
                                ->when(session('city_based_access') == '1',function($query){
                                    $query->where('vendor_warehouse.wh_city',session('user_city'));
                                })
                                ->get();
        $data['vendor_details'] = json_decode(json_encode($vendor_details), true);        
        $data['vendor_product_details'] = array();
        $data['vendor_product_counts'] = array();
        for ($i=0; $i <count($data['vendor_details']); $i++) 
        {
            $temp = array();
            $products = array();
            $vendor_name = $data['vendor_details'][$i]['registered_name'];
            if(in_array($vendor_name, array_column($data['vendor_product_counts'], 'vendor_name')))
            {
                $vendor_count_key = array_search($vendor_name, array_column($data['vendor_product_counts'], 'vendor_name'));
                $data['vendor_product_counts'][$vendor_count_key]['count'] = $data['vendor_product_counts'][$vendor_count_key]['count']+1;
                // $product_count_key = array_search($vendor_name, array_column($data['vendor_product_counts'][$vendor_count_key]['products'], 'product_name'));
                // $product_count_key = $product_count_key+1;
                $product_count_key = count($data['vendor_product_counts'][$vendor_count_key]['products']);
                $product_count_key = $product_count_key;
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_id'] = $data['vendor_details'][$i]['id'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_name'] = $data['vendor_details'][$i]['product_name'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_details'] = $data['vendor_details'][$i]['product_details'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_rent_approved'] = $data['vendor_details'][$i]['product_rent_approved'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_quantity'] = $data['vendor_details'][$i]['product_quantity'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['warehouse_id'] = $data['vendor_details'][$i]['warehouse_id'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['wh_city'] = $data['vendor_details'][$i]['wh_city'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['wh_name'] = $data['vendor_details'][$i]['wh_name'];
            }
            else
            {
                $temp['vendor_name'] = $vendor_name;
                $temp['count'] = 1;
                $products[0]['product_id'] = $data['vendor_details'][$i]['id'];
                $products[0]['product_name'] = $data['vendor_details'][$i]['product_name'];
                $products[0]['product_details'] = $data['vendor_details'][$i]['product_details'];
                $products[0]['product_rent_approved'] = $data['vendor_details'][$i]['product_rent_approved'];
                $products[0]['product_quantity'] = $data['vendor_details'][$i]['product_quantity'];
                $products[0]['warehouse_id'] = $data['vendor_details'][$i]['warehouse_id'];
                $products[0]['wh_city'] = $data['vendor_details'][$i]['wh_city'];
                $products[0]['wh_name'] = $data['vendor_details'][$i]['wh_name'];
                $temp['products'] = $products;
                array_push($data['vendor_product_counts'],$temp);                
            }            
        }
        echo "<script>localStorage['filtered']='Mumbai';</script>";
        return view('ProductManagement/product_approved_rent',$data);
    }
    public function product_approved_rent_citywise($city)
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
            return redirect()->to($url);
        }

        $main_cities = DB::select("SELECT * FROM main_cities");
        $data['main_cities'] = \json_decode(\json_encode($main_cities), true);

        // $vendor_details = DB::select("SELECT vendor_details.registered_name as registered_name, vendor_products.id as id,vendor_products.product_quantity as product_quantity, vendor_products.product_id as product_id, vendor_products.product_brand as product_details, vendor_products.product_rent_approved as product_rent_approved, products.product_name as product_name, vendor_products.warehouse_id as warehouse_id, vendor_warehouse.wh_city as wh_city, vendor_warehouse.wh_name as wh_name FROM vendor_products,vendor_details,products,vendor_warehouse WHERE vendor_products.vendor_id = vendor_details.id AND vendor_products.status = 'Approved' AND vendor_warehouse.wh_city = '$city' AND vendor_products.product_id = products.id AND vendor_products.warehouse_id = vendor_warehouse.id");
        $vendor_details = DB::table('vendor_details')
                                ->join('vendor_products','vendor_products.vendor_id','=','vendor_details.id')
                                ->join('products','products.id','=','vendor_products.product_id')
                                ->join('vendor_warehouse','vendor_warehouse.id','=','vendor_products.warehouse_id')
                                ->select(
                                    'vendor_details.registered_name as registered_name',
                                    'vendor_products.id as id',
                                    'vendor_products.product_quantity as product_quantity',
                                    'vendor_products.product_id as product_id',
                                    'vendor_products.product_brand as product_details',
                                    'vendor_products.product_rent_approved as product_rent_approved',
                                    'products.product_name as product_name',
                                    'vendor_products.warehouse_id as warehouse_id',
                                    'vendor_warehouse.wh_city as wh_city',
                                    'vendor_warehouse.wh_name as wh_name'
                                )
                                ->where('vendor_products.status','Approved')
                                ->where('vendor_warehouse.wh_city',$city)
                                ->when(session('city_based_access') == '1',function($query){
                                    $query->where('vendor_warehouse.wh_city',session('user_city'));
                                })
                                ->get();
        $data['vendor_details'] = json_decode(json_encode($vendor_details), true);        
        $data['vendor_product_details'] = array();
        $data['vendor_product_counts'] = array();
        for ($i=0; $i <count($data['vendor_details']); $i++) 
        {
            $temp = array();
            $products = array();
            $vendor_name = $data['vendor_details'][$i]['registered_name'];
            if(in_array($vendor_name, array_column($data['vendor_product_counts'], 'vendor_name')))
            {
                $vendor_count_key = array_search($vendor_name, array_column($data['vendor_product_counts'], 'vendor_name'));
                $data['vendor_product_counts'][$vendor_count_key]['count'] = $data['vendor_product_counts'][$vendor_count_key]['count']+1;
                // $product_count_key = array_search($vendor_name, array_column($data['vendor_product_counts'][$vendor_count_key]['products'], 'product_name'));
                // $product_count_key = $product_count_key+1;
                $product_count_key = count($data['vendor_product_counts'][$vendor_count_key]['products']);
                $product_count_key = $product_count_key;
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_id'] = $data['vendor_details'][$i]['id'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_name'] = $data['vendor_details'][$i]['product_name'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_details'] = $data['vendor_details'][$i]['product_details'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_rent_approved'] = $data['vendor_details'][$i]['product_rent_approved'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_quantity'] = $data['vendor_details'][$i]['product_quantity'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['warehouse_id'] = $data['vendor_details'][$i]['warehouse_id'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['wh_city'] = $data['vendor_details'][$i]['wh_city'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['wh_name'] = $data['vendor_details'][$i]['wh_name'];
            }
            else
            {
                $temp['vendor_name'] = $vendor_name;
                $temp['count'] = 1;
                $products[0]['product_id'] = $data['vendor_details'][$i]['id'];
                $products[0]['product_name'] = $data['vendor_details'][$i]['product_name'];
                $products[0]['product_details'] = $data['vendor_details'][$i]['product_details'];
                $products[0]['product_rent_approved'] = $data['vendor_details'][$i]['product_rent_approved'];
                $products[0]['product_quantity'] = $data['vendor_details'][$i]['product_quantity'];
                $products[0]['warehouse_id'] = $data['vendor_details'][$i]['warehouse_id'];
                $products[0]['wh_city'] = $data['vendor_details'][$i]['wh_city'];
                $products[0]['wh_name'] = $data['vendor_details'][$i]['wh_name'];
                $temp['products'] = $products;
                array_push($data['vendor_product_counts'],$temp);                
            }            
        }
        return view('ProductManagement/product_approved_rent',$data);
    }
    //Rejected Products----------------
    public function product_rejected_rent()
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
            return redirect()->to($url);
        }

        $main_cities = DB::select("SELECT * FROM main_cities");
        $data['main_cities'] = \json_decode(\json_encode($main_cities), true);

        //$vendor_details = DB::select("SELECT vendor_details.registered_name as registered_name, vendor_products.id as id,vendor_products.product_quantity as product_quantity, vendor_products.product_id as product_id, vendor_products.product_brand as product_details, vendor_products.product_rent_requested as product_rent_rejected, products.product_name as product_name FROM vendor_products,vendor_details,products WHERE vendor_products.vendor_id = vendor_details.id AND vendor_products.status = 'Rejected' AND vendor_products.product_id = products.id");
        // $vendor_details = DB::select("SELECT vendor_details.registered_name as registered_name, vendor_products.id as id,vendor_products.product_quantity as product_quantity, vendor_products.product_id as product_id, vendor_products.product_brand as product_details, vendor_products.product_rent_requested as product_rent_rejected, products.product_name as product_name, vendor_products.warehouse_id as warehouse_id, vendor_warehouse.wh_city as wh_city, vendor_warehouse.wh_name as wh_name FROM vendor_products,vendor_details,products,vendor_warehouse WHERE vendor_products.vendor_id = vendor_details.id AND vendor_products.status = 'Rejected' AND vendor_warehouse.wh_city = 'Mumbai' AND vendor_products.product_id = products.id AND vendor_products.warehouse_id = vendor_warehouse.id");
        $vendor_details = DB::table('vendor_details')
                                ->join('vendor_products','vendor_products.vendor_id','=','vendor_details.id')
                                ->join('products','products.id','=','vendor_products.product_id')
                                ->join('vendor_warehouse','vendor_warehouse.id','=','vendor_products.warehouse_id')
                                ->select(
                                    'vendor_details.registered_name as registered_name',
                                    'vendor_products.id as id',
                                    'vendor_products.product_quantity as product_quantity',
                                    'vendor_products.product_id as product_id',
                                    'vendor_products.product_brand as product_details',
                                    'vendor_products.product_rent_requested as product_rent_rejected',
                                    'products.product_name as product_name',
                                    'vendor_products.warehouse_id as warehouse_id',
                                    'vendor_warehouse.wh_city as wh_city',
                                    'vendor_warehouse.wh_name as wh_name'
                                )
                                ->where('vendor_products.status','Rejected')
                                // ->where('vendor_warehouse.wh_city',$city)
                                ->when(session('city_based_access') == '1',function($query){
                                    $query->where('vendor_warehouse.wh_city',session('user_city'));
                                })
                                ->get();
        $data['vendor_details'] = json_decode(json_encode($vendor_details), true);        
        $data['vendor_product_details'] = array();
        $data['vendor_product_counts'] = array();
        for ($i=0; $i <count($data['vendor_details']); $i++) 
        {
            $temp = array();
            $products = array();
            $vendor_name = $data['vendor_details'][$i]['registered_name'];
            if(in_array($vendor_name, array_column($data['vendor_product_counts'], 'vendor_name')))
            {
                $vendor_count_key = array_search($vendor_name, array_column($data['vendor_product_counts'], 'vendor_name'));
                $data['vendor_product_counts'][$vendor_count_key]['count'] = $data['vendor_product_counts'][$vendor_count_key]['count']+1;
                // $product_count_key = array_search($vendor_name, array_column($data['vendor_product_counts'][$vendor_count_key]['products'], 'product_name'));
                // $product_count_key = $product_count_key+1;
                $product_count_key = count($data['vendor_product_counts'][$vendor_count_key]['products']);
                $product_count_key = $product_count_key;
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_id'] = $data['vendor_details'][$i]['id'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_name'] = $data['vendor_details'][$i]['product_name'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_details'] = $data['vendor_details'][$i]['product_details'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_rent_rejected'] = $data['vendor_details'][$i]['product_rent_rejected'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_quantity'] = $data['vendor_details'][$i]['product_quantity'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['warehouse_id'] = $data['vendor_details'][$i]['warehouse_id'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['wh_city'] = $data['vendor_details'][$i]['wh_city'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['wh_name'] = $data['vendor_details'][$i]['wh_name'];
            }
            else
            {
                $temp['vendor_name'] = $vendor_name;
                $temp['count'] = 1;
                $products[0]['product_id'] = $data['vendor_details'][$i]['id'];
                $products[0]['product_name'] = $data['vendor_details'][$i]['product_name'];
                $products[0]['product_details'] = $data['vendor_details'][$i]['product_details'];
                $products[0]['product_rent_rejected'] = $data['vendor_details'][$i]['product_rent_rejected'];
                $products[0]['product_quantity'] = $data['vendor_details'][$i]['product_quantity'];
                $products[0]['warehouse_id'] = $data['vendor_details'][$i]['warehouse_id'];
                $products[0]['wh_city'] = $data['vendor_details'][$i]['wh_city'];
                $products[0]['wh_name'] = $data['vendor_details'][$i]['wh_name'];
                $temp['products'] = $products;
                array_push($data['vendor_product_counts'],$temp);                
            }            
        }
        //print_r($data['vendor_product_counts']);
        echo "<script>localStorage['filtered']='Mumbai';</script>";   
        return view('ProductManagement/product_rejected_rent',$data);
    }
    public function product_rejected_rent_citywise($city)
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
            return redirect()->to($url);
        }

        $main_cities = DB::select("SELECT * FROM main_cities");
        $data['main_cities'] = \json_decode(\json_encode($main_cities), true);

        //$vendor_details = DB::select("SELECT vendor_details.registered_name as registered_name, vendor_products.id as id,vendor_products.product_quantity as product_quantity, vendor_products.product_id as product_id, vendor_products.product_brand as product_details, vendor_products.product_rent_requested as product_rent_rejected, products.product_name as product_name FROM vendor_products,vendor_details,products WHERE vendor_products.vendor_id = vendor_details.id AND vendor_products.status = 'Rejected' AND vendor_products.product_id = products.id");
        // $vendor_details = DB::select("SELECT vendor_details.registered_name as registered_name, vendor_products.id as id,vendor_products.product_quantity as product_quantity, vendor_products.product_id as product_id, vendor_products.product_brand as product_details, vendor_products.product_rent_requested as product_rent_rejected, products.product_name as product_name, vendor_products.warehouse_id as warehouse_id, vendor_warehouse.wh_city as wh_city, vendor_warehouse.wh_name as wh_name FROM vendor_products,vendor_details,products,vendor_warehouse WHERE vendor_products.vendor_id = vendor_details.id AND vendor_products.status = 'Rejected' AND vendor_warehouse.wh_city = '$city' AND vendor_products.product_id = products.id AND vendor_products.warehouse_id = vendor_warehouse.id");
        $vendor_details = DB::table('vendor_details')
                                ->join('vendor_products','vendor_products.vendor_id','=','vendor_details.id')
                                ->join('products','products.id','=','vendor_products.product_id')
                                ->join('vendor_warehouse','vendor_warehouse.id','=','vendor_products.warehouse_id')
                                ->select(
                                    'vendor_details.registered_name as registered_name',
                                    'vendor_products.id as id',
                                    'vendor_products.product_quantity as product_quantity',
                                    'vendor_products.product_id as product_id',
                                    'vendor_products.product_brand as product_details',
                                    'vendor_products.product_rent_requested as product_rent_rejected',
                                    'products.product_name as product_name',
                                    'vendor_products.warehouse_id as warehouse_id',
                                    'vendor_warehouse.wh_city as wh_city',
                                    'vendor_warehouse.wh_name as wh_name'
                                )
                                ->where('vendor_products.status','Rejected')
                                ->where('vendor_warehouse.wh_city',$city)
                                ->when(session('city_based_access') == '1',function($query){
                                    $query->where('vendor_warehouse.wh_city',session('user_city'));
                                })
                                ->get();
        $data['vendor_details'] = json_decode(json_encode($vendor_details), true);        
        $data['vendor_product_details'] = array();
        $data['vendor_product_counts'] = array();
        for ($i=0; $i <count($data['vendor_details']); $i++) 
        {
            $temp = array();
            $products = array();
            $vendor_name = $data['vendor_details'][$i]['registered_name'];
            if(in_array($vendor_name, array_column($data['vendor_product_counts'], 'vendor_name')))
            {
                $vendor_count_key = array_search($vendor_name, array_column($data['vendor_product_counts'], 'vendor_name'));
                $data['vendor_product_counts'][$vendor_count_key]['count'] = $data['vendor_product_counts'][$vendor_count_key]['count']+1;
                // $product_count_key = array_search($vendor_name, array_column($data['vendor_product_counts'][$vendor_count_key]['products'], 'product_name'));
                // $product_count_key = $product_count_key+1;
                $product_count_key = count($data['vendor_product_counts'][$vendor_count_key]['products']);
                $product_count_key = $product_count_key;
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_id'] = $data['vendor_details'][$i]['id'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_name'] = $data['vendor_details'][$i]['product_name'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_details'] = $data['vendor_details'][$i]['product_details'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_rent_rejected'] = $data['vendor_details'][$i]['product_rent_rejected'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_quantity'] = $data['vendor_details'][$i]['product_quantity'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['warehouse_id'] = $data['vendor_details'][$i]['warehouse_id'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['wh_city'] = $data['vendor_details'][$i]['wh_city'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['wh_name'] = $data['vendor_details'][$i]['wh_name'];
            }
            else
            {
                $temp['vendor_name'] = $vendor_name;
                $temp['count'] = 1;
                $products[0]['product_id'] = $data['vendor_details'][$i]['id'];
                $products[0]['product_name'] = $data['vendor_details'][$i]['product_name'];
                $products[0]['product_details'] = $data['vendor_details'][$i]['product_details'];
                $products[0]['product_rent_rejected'] = $data['vendor_details'][$i]['product_rent_rejected'];
                $products[0]['product_quantity'] = $data['vendor_details'][$i]['product_quantity'];
                $products[0]['warehouse_id'] = $data['vendor_details'][$i]['warehouse_id'];
                $products[0]['wh_city'] = $data['vendor_details'][$i]['wh_city'];
                $products[0]['wh_name'] = $data['vendor_details'][$i]['wh_name'];
                $temp['products'] = $products;
                array_push($data['vendor_product_counts'],$temp);                
            }            
        }
        //print_r($data['vendor_product_counts']);   
        return view('ProductManagement/product_rejected_rent',$data);
    }
    //Rejected Products----------------
    public function product_requested_rent()
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
            return redirect()->to($url);
        }

        $main_cities = DB::select("SELECT * FROM main_cities");
        $data['main_cities'] = \json_decode(\json_encode($main_cities), true);

        //$vendor_details = DB::select("SELECT vendor_details.registered_name as registered_name, vendor_products.id as id,vendor_products.product_quantity as product_quantity, vendor_products.product_id as product_id, vendor_products.product_brand as product_details, vendor_products.product_rent_requested as product_rent_requested, products.product_name as product_name FROM vendor_products,vendor_details,products WHERE vendor_products.vendor_id = vendor_details.id AND vendor_products.status = 'Requested' AND vendor_products.product_id = products.id");
        // $vendor_details = DB::select("SELECT vendor_details.registered_name as registered_name, vendor_products.id as id,vendor_products.product_quantity as product_quantity, vendor_products.product_id as product_id, vendor_products.product_brand as product_details, vendor_products.product_rent_requested as product_rent_requested, products.product_name as product_name, vendor_products.warehouse_id as warehouse_id, vendor_warehouse.wh_city as wh_city, vendor_warehouse.wh_name as wh_name FROM vendor_products,vendor_details,products,vendor_warehouse WHERE vendor_products.vendor_id = vendor_details.id AND vendor_products.status = 'Requested' AND vendor_warehouse.wh_city = 'Mumbai' AND vendor_products.product_id = products.id AND vendor_products.warehouse_id = vendor_warehouse.id");
        $vendor_details = DB::table('vendor_details')
                                ->join('vendor_products','vendor_products.vendor_id','=','vendor_details.id')
                                ->join('products','products.id','=','vendor_products.product_id')
                                ->join('vendor_warehouse','vendor_warehouse.id','=','vendor_products.warehouse_id')
                                ->select(
                                    'vendor_details.registered_name as registered_name',
                                    'vendor_products.id as id',
                                    'vendor_products.product_quantity as product_quantity',
                                    'vendor_products.product_id as product_id',
                                    'vendor_products.product_brand as product_details',
                                    'vendor_products.product_rent_requested as product_rent_requested',
                                    'products.product_name as product_name',
                                    'vendor_products.warehouse_id as warehouse_id',
                                    'vendor_warehouse.wh_city as wh_city',
                                    'vendor_warehouse.wh_name as wh_name'
                                )
                                ->where('vendor_products.status','Requested')
                                // ->where('vendor_warehouse.wh_city',$city)
                                ->when(session('city_based_access') == '1',function($query){
                                    $query->where('vendor_warehouse.wh_city',session('user_city'));
                                })
                                ->get();
        $data['vendor_details'] = json_decode(json_encode($vendor_details), true);        
        $data['vendor_product_details'] = array();
        $data['vendor_product_counts'] = array();
        for ($i=0; $i <count($data['vendor_details']); $i++) 
        {
            $temp = array();
            $products = array();
            $vendor_name = $data['vendor_details'][$i]['registered_name'];
            if(in_array($vendor_name, array_column($data['vendor_product_counts'], 'vendor_name')))
            {
                $vendor_count_key = array_search($vendor_name, array_column($data['vendor_product_counts'], 'vendor_name'));
                $data['vendor_product_counts'][$vendor_count_key]['count'] = $data['vendor_product_counts'][$vendor_count_key]['count']+1;
                // $product_count_key = array_search($vendor_name, array_column($data['vendor_product_counts'][$vendor_count_key]['products'], 'product_name'));
                // $product_count_key = $product_count_key+1;
                $product_count_key = count($data['vendor_product_counts'][$vendor_count_key]['products']);
                $product_count_key = $product_count_key;
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_id'] = $data['vendor_details'][$i]['id'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_name'] = $data['vendor_details'][$i]['product_name'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_details'] = $data['vendor_details'][$i]['product_details'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_rent_requested'] = $data['vendor_details'][$i]['product_rent_requested'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_quantity'] = $data['vendor_details'][$i]['product_quantity'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['warehouse_id'] = $data['vendor_details'][$i]['warehouse_id'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['wh_city'] = $data['vendor_details'][$i]['wh_city'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['wh_name'] = $data['vendor_details'][$i]['wh_name'];
            }
            else
            {
                $temp['vendor_name'] = $vendor_name;
                $temp['count'] = 1;
                $products[0]['product_id'] = $data['vendor_details'][$i]['id'];
                $products[0]['product_name'] = $data['vendor_details'][$i]['product_name'];
                $products[0]['product_details'] = $data['vendor_details'][$i]['product_details'];
                $products[0]['product_rent_requested'] = $data['vendor_details'][$i]['product_rent_requested'];
                $products[0]['product_quantity'] = $data['vendor_details'][$i]['product_quantity'];
                $products[0]['warehouse_id'] = $data['vendor_details'][$i]['warehouse_id'];
                $products[0]['wh_city'] = $data['vendor_details'][$i]['wh_city'];
                $products[0]['wh_name'] = $data['vendor_details'][$i]['wh_name'];
                $temp['products'] = $products;
                array_push($data['vendor_product_counts'],$temp);
            }            
        }        
        echo "<script>localStorage['filtered']='Mumbai';</script>";
        return view('ProductManagement/product_requested_rent',$data);
    }
    public function product_requested_rent_citywise($city)
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
            return redirect()->to($url);
        }

        $main_cities = DB::select("SELECT * FROM main_cities");
        $data['main_cities'] = \json_decode(\json_encode($main_cities), true);

        //$vendor_details = DB::select("SELECT vendor_details.registered_name as registered_name, vendor_products.id as id,vendor_products.product_quantity as product_quantity, vendor_products.product_id as product_id, vendor_products.product_brand as product_details, vendor_products.product_rent_requested as product_rent_requested, products.product_name as product_name FROM vendor_products,vendor_details,products WHERE vendor_products.vendor_id = vendor_details.id AND vendor_products.status = 'Requested' AND vendor_products.product_id = products.id");
        // $vendor_details = DB::select("SELECT vendor_details.registered_name as registered_name, vendor_products.id as id,vendor_products.product_quantity as product_quantity, vendor_products.product_id as product_id, vendor_products.product_brand as product_details, vendor_products.product_rent_requested as product_rent_requested, products.product_name as product_name, vendor_products.warehouse_id as warehouse_id, vendor_warehouse.wh_city as wh_city, vendor_warehouse.wh_name as wh_name FROM vendor_products,vendor_details,products,vendor_warehouse WHERE vendor_products.vendor_id = vendor_details.id AND vendor_products.status = 'Requested' AND vendor_warehouse.wh_city = '$city' AND vendor_products.product_id = products.id AND vendor_products.warehouse_id = vendor_warehouse.id");
        $vendor_details = DB::table('vendor_details')
                                ->join('vendor_products','vendor_products.vendor_id','=','vendor_details.id')
                                ->join('products','products.id','=','vendor_products.product_id')
                                ->join('vendor_warehouse','vendor_warehouse.id','=','vendor_products.warehouse_id')
                                ->select(
                                    'vendor_details.registered_name as registered_name',
                                    'vendor_products.id as id',
                                    'vendor_products.product_quantity as product_quantity',
                                    'vendor_products.product_id as product_id',
                                    'vendor_products.product_brand as product_details',
                                    'vendor_products.product_rent_requested as product_rent_requested',
                                    'products.product_name as product_name',
                                    'vendor_products.warehouse_id as warehouse_id',
                                    'vendor_warehouse.wh_city as wh_city',
                                    'vendor_warehouse.wh_name as wh_name'
                                )
                                ->where('vendor_products.status','Requested')
                                ->where('vendor_warehouse.wh_city',$city)
                                ->when(session('city_based_access') == '1',function($query){
                                    $query->where('vendor_warehouse.wh_city',session('user_city'));
                                })
                                ->get();
        $data['vendor_details'] = json_decode(json_encode($vendor_details), true);        
        $data['vendor_product_details'] = array();
        $data['vendor_product_counts'] = array();
        for ($i=0; $i <count($data['vendor_details']); $i++) 
        {
            $temp = array();
            $products = array();
            $vendor_name = $data['vendor_details'][$i]['registered_name'];
            if(in_array($vendor_name, array_column($data['vendor_product_counts'], 'vendor_name')))
            {
                $vendor_count_key = array_search($vendor_name, array_column($data['vendor_product_counts'], 'vendor_name'));
                $data['vendor_product_counts'][$vendor_count_key]['count'] = $data['vendor_product_counts'][$vendor_count_key]['count']+1;
                // $product_count_key = array_search($vendor_name, array_column($data['vendor_product_counts'][$vendor_count_key]['products'], 'product_name'));
                // $product_count_key = $product_count_key+1;
                $product_count_key = count($data['vendor_product_counts'][$vendor_count_key]['products']);
                $product_count_key = $product_count_key;
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_id'] = $data['vendor_details'][$i]['id'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_name'] = $data['vendor_details'][$i]['product_name'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_details'] = $data['vendor_details'][$i]['product_details'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_rent_requested'] = $data['vendor_details'][$i]['product_rent_requested'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['product_quantity'] = $data['vendor_details'][$i]['product_quantity'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['warehouse_id'] = $data['vendor_details'][$i]['warehouse_id'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['wh_city'] = $data['vendor_details'][$i]['wh_city'];
                $data['vendor_product_counts'][$vendor_count_key]['products'][$product_count_key]['wh_name'] = $data['vendor_details'][$i]['wh_name'];
            }
            else
            {
                $temp['vendor_name'] = $vendor_name;
                $temp['count'] = 1;
                $products[0]['product_id'] = $data['vendor_details'][$i]['id'];
                $products[0]['product_name'] = $data['vendor_details'][$i]['product_name'];
                $products[0]['product_details'] = $data['vendor_details'][$i]['product_details'];
                $products[0]['product_rent_requested'] = $data['vendor_details'][$i]['product_rent_requested'];
                $products[0]['product_quantity'] = $data['vendor_details'][$i]['product_quantity'];
                $products[0]['warehouse_id'] = $data['vendor_details'][$i]['warehouse_id'];
                $products[0]['wh_city'] = $data['vendor_details'][$i]['wh_city'];
                $products[0]['wh_name'] = $data['vendor_details'][$i]['wh_name'];
                $temp['products'] = $products;
                array_push($data['vendor_product_counts'],$temp);
            }            
        }        
        return view('ProductManagement/product_requested_rent',$data);
    }
    public function update_product_status()
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
            return redirect()->to($url);
        }
       if($_SERVER['REQUEST_METHOD']=='POST')
       {
            if($_POST['submit']=='submit')
            {
                $ids = json_decode(request()->get('info'));
                // print_r($_POST);
                foreach ($ids as $id)
                {
                    $action = request()->get('action'.$id);
                    $vendor_product_id = request()->get('vendor_product_id'.$id);
                    if((request()->get('comment'.$id))!==null)
                    {
                            $comment = request()->get('comment'.$id);
                    }
                    else
                    {
                        $comment = 'null';
                    }
                    if($action == 'Approve')
                    {
                        DB::update("UPDATE vendor_products SET status='Approved', product_rent_approved=vendor_products.product_rent_requested, comment='$comment' WHERE id = $vendor_product_id");
                    }
                    // else
                    elseif($action == 'Reject')
                    {
                        DB::update("UPDATE vendor_products SET status='Rejected', comment='$comment' WHERE id = $vendor_product_id");
                    }
                }
                return $this->product_request();
            }
            else
            {
                $ids = json_decode(request()->get('info'));
                foreach ($ids as $id)
                {
                    $action = request()->get('action'.$id);
                    $vendor_product_id = request()->get('vendor_product_id'.$id);
                    if((request()->get('comment'.$id))!==null)
                    {
                            $comment = request()->get('comment'.$id);
                    }
                    else
                    {
                        $comment = 'null';
                    }
                    if($action == 'Approve')
                    {
                        DB::update("UPDATE vendor_products SET status='Approved', product_rent_approved=vendor_products.product_rent_requested, comment='$comment' WHERE id = $vendor_product_id");
                    }
                    else
                    {
                        DB::update("UPDATE vendor_products SET status='Rejected', comment='$comment' WHERE id = $vendor_product_id");
                    }
                }
                return $this->product_approved_rent();
            }
        }
    }
    public function detailed_rent_list()
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
            return redirect()->to($url);
        }
       $vendor_names = DB::select("SELECT * FROM vendor_details");
       $data['vendor_names'] = json_decode(json_encode($vendor_names),true);
       return view('ProductManagement/detailed_rent_list',$data);
    }
}
?>