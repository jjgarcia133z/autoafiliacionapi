<?php
  
namespace App\Http\Controllers;
  
use App\Models\Product;
use App\Models\Planes;
use App\Models\PlanesSalesforce;
use Illuminate\Support\Facades\DB;
 
use Illuminate\Http\Request;
  
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $productos = Planes::all();
     
        return view('productos.index',compact('productos'));
           
    }
  
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
      $plan = DB::table('planes')
      ->select('default')
      ->where('default', '=', 1)
      ->count();
        //$PlanesSalesforces = PlanesSalesforce::all();
       // dd($PlanesSalesforces);
       
$PlanesSalesforces = DB::table('planes_salesforces')
            ->select('id', 'codigo','nombre','montoTitular','montoAdicional')
            ->get();


        return view('productos.create',compact('PlanesSalesforces','plan'));
    }
  
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      //  $request->validate([
           // 'name' => 'required',
          //  'detail' => 'required',
        //]);
      


      
      $plan = DB::table('planes')
      ->select('default')
      ->where('default', '=', 1)
      ->count();
      

if($plan==0){
    Planes::create($request->all());
    return redirect()->route('productos.index')
    ->with('success','Product created successfully.');
}else{


    $PlanesSalesforces = DB::table('planes_salesforces')
            ->select('id', 'codigo','nombre','montoTitular','montoAdicional')
            ->get();

$message="err0r";

    return view('productos.create',compact('PlanesSalesforces','message'));



}

       
      
    }
  
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */

  
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //dd($product);
        return view('productos.edit',compact('product'));
    }
  
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'detail' => 'required',
        ]);
      
        $product->update($request->all());
      
        return redirect()->route('productos.index')
                        ->with('success','Product updated successfully');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($product)
    {
        $product = Planes::find($product);
 
$product->delete();
       
        return redirect()->route('productos.index')
                        ->with('success','Plan  Eliminado Correcto');
    }
}

