<?php

namespace App\Http\Controllers\MasterProductManagement;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\VendorRegister;
use App\Models\UserRegister;
use App\Models\MasterProduct;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Mail;

class MasterProductController extends Controller
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
            return view('MasterProductManagement/add_new_product');
        }
        else
        {
            $master_product = new MasterProduct();
            $order = MasterProduct::whereRaw('id = (select max(`id`) from products)')->get();
            //echo $order;
            $maxid_details = json_decode(json_encode($order),true);
            //print_r($maxid_details);
            // ----------------image add-----------//
            $id= $maxid_details[0]['id'];
            $id = $id+1;
            $name = $_POST['product_name'];
            $targetDir = "assets/images/product_images/";
            $fileName = basename($_FILES['product_image']['name']);
            $targetFilePath = $targetDir . $fileName;
            $fileType =strtolower(pathinfo($targetFilePath,PATHINFO_EXTENSION));
            $new_file_name = $targetDir ."".$id.".".$fileType;
            //echo $new_file_name;
            move_uploaded_file($_FILES["product_image"]["tmp_name"], $new_file_name);    
            $product_image_path = "http://intra.quali55care.com/devweb/fullfillment_presales/assets/images/product_images/".$id.".".$fileType;
            //$insert_cheque_image = DB::update("UPDATE vendor_details SET cheque_image='$cheque_image_path' WHERE id = '$vendor_id' ");
            //--------------------------------------------------------------------------//

            $insert_product = [
                'product_name' => request()->get('product_name'),
                'product_details' => request()->get('product_details'),
                'product_qty' => request()->get('product_qty'),
                'product_deposite' => request()->get('product_deposite'),
                'product_rent' => request()->get('product_rent'),
                'product_sale_rate' => request()->get('product_sale_price'),
                'product_transport_cost' => request()->get('product_transport_cost'),
                'min_rent_percentage' => request()->get('min_rent_percentage'),
                'product_type' => request()->get('product_type'),
                'product_img_url' => $product_image_path,
            ];

            $master_product -> insert($insert_product);
            //DB::insert("INSERT INTO products (product_name,product_details,product_type,product_deposite,product_rent,min_rent_percentage) values('$product_name','$product_details','$product_type',$product_deposite,$product_rent,$min_rent_percentage)");
            return redirect('/add_new_product')->with('message','New Product Successfully added');

            
        }
    }
    //View Master Products....
    public function view_master_products()
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
            return redirect()->to($url);
        }

        $product_details = DB::select("SELECT * FROM products WHERE flag = 'Active'");
        $data['product_details'] = json_decode(json_encode($product_details), true);
        return view('MasterProductManagement/master_products',$data);
    }
    //-------------EDIT PRODUCT-------------//
    public function edit_master_product($product_id)
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
            return redirect()->to($url);
        }
        if($_SERVER['REQUEST_METHOD'] == 'GET')
        {
            $product_details = DB::select("SELECT * FROM products where id = $product_id");
            $data['product_details'] = json_decode(json_encode($product_details), true);
            return view('MasterProductManagement/edit_master_product',$data);
            //print_r($data);
        }
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $master_product = new MasterProduct();
            // ----------------image update-----------//
                $name = $_POST['product_name'];
                $targetDir = "assets/images/product_images/";
                $fileName = basename($_FILES['product_image']['name']);
                $targetFilePath = $targetDir . $fileName;
                $fileType =strtolower(pathinfo($targetFilePath,PATHINFO_EXTENSION));
                $new_file_name = $targetDir ."".$product_id.".".$fileType;
                //echo $new_file_name;
                move_uploaded_file($_FILES["product_image"]["tmp_name"], $new_file_name);    
                $product_image_path = "http://intra.quali55care.com/prodweb/eflow/assets/images/product_images/".$product_id.".".$fileType;
           
            //$insert_cheque_image = DB::update("UPDATE vendor_details SET cheque_image='$cheque_image_path' WHERE id = '$vendor_id' ");
            //--------------------------------------------------------------------------//

            $update_product = [
                'product_name' => request()->get('product_name'),
                'product_details' => request()->get('product_details'),
                'product_qty' => request()->get('product_qty'),
                'product_deposite' => request()->get('product_deposite'),
                'product_rent' => request()->get('product_rent'),
                'product_sale_rate' => request()->get('product_sale_price'),
                'product_transport_cost' => request()->get('product_transport_cost'),
                'min_rent_percentage' => request()->get('min_rent_percentage'),
                'product_type' => request()->get('product_type'),
                'product_img_url' => $product_image_path,
            ];
            //print_r($_POST);
            //print_r($_FILES);
            $master_product ->where('id',$product_id)->update($update_product);
            return redirect('/view_master_products')->with('message','New Product Successfully added');

        }
        
    }
    public function view_product_details($product_id)
    {
        $isLoggedIn = $this->isLoggedIn();
        if($isLoggedIn == 'false')
        {
            $url = url('/');
            return redirect()->to($url);
        }
        $product_details = DB::select("SELECT * FROM products where id = $product_id");
        $data['product_details'] = json_decode(json_encode($product_details), true);
        return view('MasterProductManagement/view_product_details',$data);
    }
    public function mapproduct(Request $request)
    {
        if($request->method() == 'GET')
        {
            if($request->get('state') == 'Check')
            {
                if(DB::table('productsmap')->where('webproductid',$request->get('webproductid'))->exists())
                {
                    return ['status'=>"Exists"];
                }
                else
                {
                    return ['status'=>"Not Exists"];
                }
            }
            if($request->get('request-type') == "Insert")
            {
                if(!DB::table('productsmap')->where('webproductid',$request->get('web_product_id'))->exists())
                {
                    DB::table('productsmap')->insert([
                        'webproductid'=>$request->get('web_product_id'),
                        'brandid'=>'1234',
                        'oldproductid'=>$request->get('master_product_id')
                    ]);
                    return redirect()->back()->with('message','Products Mapped Successfully!');
                }
                else
                {
                    return redirect()->back()->with('error','Web Id Already Exists');
                }
            }
            $mappedproducts = DB::table('productsmap')
                        ->join('products','productsmap.oldproductid','=','products.id')
                        ->select('products.product_name','productsmap.*')
                        ->when($request->get('filterwebproductid'),function($query)use($request){
                            $query->where('webproductid',$request->get('filterwebproductid'));
                        })->paginate(10);
            $masterproducts = DB::table('products')->select('id','product_name')->where('flag','Active')->get();
            return view('MasterProductManagement.mapproduct',compact('masterproducts','mappedproducts'));
        }
    }
}
?>