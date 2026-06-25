<?php

namespace App\Http\Controllers\B2BController;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Models\ActivityLog;
use App\Models\B2BProdRate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use PDF;
use Mail;
use Session;
use DateTime;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Response;
//use other controler
use App\Http\Controllers\Leads\LeadController;


class B2BController extends Controller
{
    public function userRates(Request $request)
    {
        $getB2bUser = DB::table('user')
                            ->where('role','=','b2buser')
                            ->get();
        $productList = DB::table('products')
                            ->where('flag','=','Active')
                            ->get();
        $b2bProductRate = DB::table('b2b_prod_rates')
                            ->select('b2b_prod_rates.*','products.product_name','user.username')
                            ->join('products','b2b_prod_rates.product_id','=','products.id')
                            ->join('user','b2b_prod_rates.b2b_user_id','=','user.id')
                            ->when($request->get('search_product'),function($query) use($request){
                                $query->where('products.product_name','LIKE','%'.$request->get('search_product').'%');
                            })
                            ->when($request->get('search_b2buser'),function($query) use($request){
                                $query->where('b2b_prod_rates.b2b_user_id','=',$request->get('search_b2buser'));
                            })
                            ->orderBy('id','DESC')
                            ->paginate(10);
        return view('B2BCust.userRates',compact('getB2bUser','productList','b2bProductRate'));
    }

    public function addProductRate(Request $request){
        DB::beginTransaction();
        try
        {
            
            $selectedProduct = $request->get('selected_product');
            $insertData = [];
            for ($i=0; $i <count($selectedProduct['id']) ; $i++) { 
                $sale_rate = 0;
                if($selectedProduct['sale_rate'][$i]!=null){
                    $sale_rate = $selectedProduct['sale_rate'][$i];
                }
                $insertData[] = [
                    'product_id'=>$selectedProduct['id'][$i],
                    'b2b_user_id'=>$request->get('selected_b2buser'),
                    'rate'=>$selectedProduct['rate'][$i],
                    'sale_rate'=>$sale_rate,
                    'created_by'=>session('user_id'),
                    'updated_by'=>session('user_id'),
                ];
            }
            B2BProdRate::insert($insertData);
            DB::commit();
            return redirect()->to('b2b-user-rate')->with('message','B2B Use product rate added successfully');
        }
        catch (Exception $ex) 
        {
            DB::rollBack();
            $file = fopen(public_path().'/tempLogfile'.date('Y-m-d').'.txt','a');
            fwrite($file,date('Y-m-d')."Exception: ".$ex);
            fwrite($file,"request_data".$request_dump);
            fclose($file);
            return redirect()->back()->with('error','Something Went Wrong! Please Try Again or Contact Administrator.');
        }
    }

    public function editProductRate(Request $request)
    {
        $request->validate(
        [
            'edit_id' => 'required|numeric',
        ],
        [
            'edit_id.required' => 'Something Went Wrong?',
            'edit_id.numeric' => 'Something Went Wrong?',
        ]);

        $id = $request->get('edit_id');
        $updateData = [
            'product_id'=>$request->get('edit_product'),
            'b2b_user_id'=>$request->get('edit_b2b_user'),
            'rate'=>$request->get('edit_rate'),
            'sale_rate'=>$request->get('edit_sale_rate'),
            'updated_by'=>session('user_id'),
        ];
        B2BProdRate::where('id',$id)->update($updateData);
        return redirect()->to('b2b-user-rate')->with('message','Product Updated successfully');
    }

    public function reomveProductRate(Request $request)
    {
        $request->validate(
            [
               'remove_id' => 'required|numeric',
            ],
            [
               'remove_id.required' => 'Something Went Wrong?',
               'remove_id.numeric' => 'Something Went Wrong?',
            ]);

        $id = $request->get('remove_id');
        B2BProdRate::where('id',$id)->delete();
        return redirect()->to('b2b-user-rate')->with('message','Product deleted successfully');
    }

}
?>