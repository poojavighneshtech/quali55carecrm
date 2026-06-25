<?php

namespace App\Http\Controllers\Quote;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PDF;
//model
use App\Models\Quote\QuoteCustomer;
use App\Models\Quote\QuoteProducts;

class Quote extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $products = DB::table('products')->where('flag','Active')->get();
        $states = DB::table('states')->get();
        $orders = DB::table('quote_customer')
                    ->join('quote_products','quote_customer.id','=','quote_products.customer_id')
                    ->join('products','products.id','=','quote_products.product_id')
                    ->select('quote_customer.*','quote_products.*','quote_products.id as qp_id','products.product_name')
                    ->where('quote_products.deleted','N')
                    ->when($request->get('search_customer_name'),function($q)use($request){
                        $q->where('quote_customer.customer_name','LIKE','%'.$request->get('search_customer_name').'%');
                    })
                    ->when($request->get('search_contact_no'),function($q)use($request){
                        $q->where('quote_customer.contact_no',$request->get('search_contact_no'));
                    })
                    ->when(!empty($request->get('search_products')),function($q)use($request){
                        $q->whereIn('quote_products.product_id',$request->get('search_products'));
                    })
                    ->when(session('role')=='user',function($q){
                        $q->where('quote_customer.created_by',session('username'));
                    })
                    ->get()
                    ->groupBy('customer_id')
                    ->paginate(10);
            
        return view('Quote.index',compact('products','orders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            //get quote_id
            $glob_quote_id = DB::table('misc_table')->where('field','quote_id')->first();
            $finacial_year = DB::table('misc_table')->where('field','invoice_no_period')->first()->value;
            // $year = Carbon::now();
            // $start_year = Carbon::now();
            // $end_year = Carbon::now();
            // if($year->month <=3 && $year->day < 31){
            //     $start_year = $start_year->subYear(1);
            // }else{
            //     $end_year = $end_year->addYear(1);
            // }
            // $gen_quote_id = "Q5C/".$start_year->year."-".$end_year->year."/".$glob_quote_id;
            $gen_quote_id = "Q5C/".$finacial_year."/".$glob_quote_id->value;
            $QuoteCustomer = QuoteCustomer::create([
                    'customer_name'=>$request->get('customer_name'),
                    'contact_no'=>$request->get('contact_no'),
                    'gender'=>$request->get('gender'),
                    'address_line_1'=>$request->get('address_line_1'),
                    'address_line_2'=>$request->get('address_line_2'),
                    'area'=>$request->get('area'),
                    'landmark'=>$request->get('landmark'),
                    'city'=>$request->get('city'),
                    'pincode'=>$request->get('pincode'),
                    'quote_date'=>$request->get('quote_date'),
                    'quote_id'=>$gen_quote_id,
                    'transport_amt'=>$request->get('transport_amt'),
                    'created_by'=>session('username')]);
            $QuoteCustomer->save();
            $update_quote_id = DB::table('misc_table')->where('id',$glob_quote_id->id)->update(['value'=>$glob_quote_id->value+1]);

            $products = $request->get('product');
            if($request->has('product') && !empty($request->get('product'))){
                foreach ($products['product_id'] as $key => $value) {
                    $rent = 0;
                    $sale = 0;
                    if($products['purchase_type'][$key]=='rent'){
                        $rent = $products['rate'][$key];
                    }else{
                        $sale = $products['rate'][$key];
                    }
                    $QuoteProducts = QuoteProducts::create([
                        'customer_id'=>$QuoteCustomer->id,
                        'product_id'=>$value,
                        'purchase_type'=>$products['purchase_type'][$key],
                        'quantity'=>$products['quantity'][$key],
                        'rent'=>$rent,
                        'sale'=>$sale,
                        'rate'=>$products['rate'][$key],
                        'frequency'=>$products['period'][$key],
                        'frequency_type'=>$products['period_type'][$key],
                        'amount'=>$products['rate'][$key]*$products['quantity'][$key],
                        'created_by'=>session('username')
                    ]);
                    $QuoteProducts->save();
                }
            }
            $order =  DB::table('quote_customer')
                        ->join('quote_products','quote_customer.id','=','quote_products.customer_id')
                        ->join('products','products.id','=','quote_products.product_id')
                        ->select('quote_customer.*','quote_products.*','quote_products.id as qp_id','products.product_name','products.product_img_url')
                        ->where('quote_customer.id',$QuoteCustomer->id)
                        ->where('quote_products.deleted','N')
                        ->get();
            return view('Quote.pdf-view',compact('order'));
        } catch (\Throwable $th) {
            //throw $th;
            dd($th);
            return redirect()->back()->with('error','somthing went wrong');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order =  DB::table('quote_customer')
                        ->join('quote_products','quote_customer.id','=','quote_products.customer_id')
                        ->join('products','products.id','=','quote_products.product_id')
                        ->select('quote_customer.*','quote_products.*','quote_products.id as qp_id','products.product_name','products.product_img_url')
                        ->where('quote_customer.id',$id)
                        ->where('quote_products.deleted','N')
                        ->get();
        return view('Quote.pdf-view',compact('order'));
        // $pdf = PDF::loadView('Quote.pdf',compact('order'));
        // ob_end_clean(); // this
        // ob_start(); // and this
        // return $pdf->download();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $order =  DB::table('quote_customer')
                ->join('quote_products','quote_customer.id','=','quote_products.customer_id')
                ->join('products','products.id','=','quote_products.product_id')
                ->select('quote_customer.*','quote_products.*','quote_products.id as qp_id','products.product_name')
                ->where('quote_customer.id',$id)
                ->where('deleted','N')
                ->get();
        return $order;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $QuoteCustomer = QuoteCustomer::find($id);
            $QuoteCustomer->customer_name = $request->get('customer_name');
            $QuoteCustomer->contact_no = $request->get('contact_no');
            $QuoteCustomer->gender = $request->get('gender');
            $QuoteCustomer->address_line_1 = $request->get('address_line_1');
            $QuoteCustomer->address_line_2 = $request->get('address_line_2');
            $QuoteCustomer->area = $request->get('area');
            $QuoteCustomer->landmark = $request->get('landmark');
            $QuoteCustomer->city = $request->get('city');
            $QuoteCustomer->pincode = $request->get('pincode');
            $QuoteCustomer->quote_date = $request->get('quote_date');
            $QuoteCustomer->transport_amt = $request->get('transport_amt');
            $QuoteCustomer->updated_by = session('username');
            $QuoteCustomer->save();
            //first deleted set Y 
            DB::table('quote_products')->where('customer_id',$id)->update(['deleted'=>'Y']);
            $products = collect($request->get('product'));
            $qp_id = collect([]);
            if($products->has('qp_id')){
                $qp_id = collect($products['qp_id']);
            }
            
            if($products->has('product_id')){
                foreach ($products['product_id'] as $key => $value) {
                    $rent = 0;
                    $sale = 0;
                    if($products['purchase_type'][$key]=='rent'){
                        $rent = $products['rate'][$key];
                    }else{
                        $sale = $products['rate'][$key];
                    }
                    if($qp_id->has($key)){
                        $QuoteProducts = QuoteProducts::find($qp_id[$key]);
                        //$QuoteProducts->customer_id= $id,
                        $QuoteProducts->product_id= $value;
                        $QuoteProducts->purchase_type= $products['purchase_type'][$key];
                        $QuoteProducts->quantity= $products['quantity'][$key];
                        $QuoteProducts->rent= $rent;
                        $QuoteProducts->sale= $sale;
                        $QuoteProducts->rate= $products['rate'][$key];
                        $QuoteProducts->frequency= $products['period'][$key];
                        $QuoteProducts->frequency_type= $products['period_type'][$key];
                        $QuoteProducts->updated_by= session('username');
                        $QuoteProducts->amount= $products['rate'][$key] * $products['quantity'][$key];
                        $QuoteProducts->deleted= 'N';
                        $QuoteProducts->save();
                    }else{
                        $QuoteProducts = QuoteProducts::create([
                            'customer_id'=>$QuoteCustomer->id,
                            'product_id'=>$value,
                            'purchase_type'=>$products['purchase_type'][$key],
                            'quantity'=>$products['quantity'][$key],
                            'rent'=>$rent,
                            'sale'=>$sale,
                            'rate'=>$products['rate'][$key],
                            'frequency'=>$products['period'][$key],
                            'frequency_type'=>$products['period_type'][$key],
                            'amount'=>$products['rate'][$key] * $products['quantity'][$key],
                            'created_by'=>session('username')
                        ]);
                        $QuoteProducts->save();
                    }
                }
            }
            return redirect()->back()->with('message','Quote Updated successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error','something went wrong');
        }
        

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // $QuoteCustomer = QuoteCustomer::find($id);
        // $QuoteCustomer->status = 'cancel';
        // $QuoteCustomer->save();
        $update_customer = DB::table('quote_customer')->where('id',$id)->update(['status'=>'cancel']);
        return redirect()->back()->with('message','cancel successfully');
    }
    public function pdfDownload($id){
        $order =  DB::table('quote_customer')
                ->join('quote_products','quote_customer.id','=','quote_products.customer_id')
                ->join('products','products.id','=','quote_products.product_id')
                ->select('quote_customer.*','quote_products.*','quote_products.id as qp_id','products.product_name','products.product_img_url')
                ->where('quote_customer.id',$id)
                ->where('quote_products.deleted','N')
                ->get();
        $pdf = PDF::loadView('Quote.pdf-download',compact('order'));
        ob_end_clean(); // this
        ob_start(); // and this
        return $pdf->download('quote.pdf');
    }
}
