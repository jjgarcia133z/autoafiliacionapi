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
      //->where('default', '=', 1)
      ->count();
        //$PlanesSalesforces = PlanesSalesforce::all();
       if($plan>4){
       
        $PlanesSalesforces = DB::table('planes_salesforces')
        ->select('id', 'codigo','nombre','montoTitular','montoAdicional')
        ->get();


    return view('productos.create',compact('PlanesSalesforces','plan'));
       }else{
        $productos = Planes::all();
     
        return view('productos.index',compact('productos'));

       }

    }
  
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    
        $hoy = date("Ymdhis");
        $creado="jgarcia";
        //$request->fechacreado=$hoy;

        $request->merge(['fechacreado' => $hoy]);
        $request->merge(['creado' => $creado]);

        $request->merge(['frecuencia' => 3]);
        
    ///dd($request);

    if($request->ahorro==1){

        $ahorro1 = DB::table('planes')
        ->select('ahorro')
        ->where('ahorro', '=', 1)
        ->count();
      
      ///  dd($plan+"1");
    if($ahorro1==1){
    
    
    
    $ahorro=0;
    
    
    
    
    
    }else{
    
        $ahorro=1;
       
    }
    
    }
      ////////////////////////
      $default = DB::table('planes')
      ->select('default')
      ->where('default', '=', 1)
      ->count();
      

      if($request->default==1){
   
      
        if($default>=1){

            $request->default=0;

           
           //// Planes::create($request->all());
            DB::table('planes')->insert([
                'tipo' => $request->tipo,
                'montoTitular' => $request->montoTitular,
                'montoBeneficiario' => $request->montoBeneficiario,
                'montoMascota' => $request->montoMascota,
                'frecuencia' => $request->frecuencia,
                'estado' => $request->estado,
                'default'=>0,
                'ahorro'=>$ahorro,
                'fechacreado'=> $request->fechacreado,
               'creado'=>$request->creado,
       
            ]);
        }else{

            DB::table('planes')->insert([
                'tipo' => $request->tipo,
                'montoTitular' => $request->montoTitular,
                'montoBeneficiario' => $request->montoBeneficiario,
                'montoMascota' => $request->montoMascota,
                'frecuencia' => $request->frecuencia,
                'estado' => $request->estado,
                'ahorro'=>$ahorro,
                'default'=>1,
                'fechacreado'=> $request->fechacreado,
               'creado'=>$request->creado,
       
            ]);



        }
   
   

    }else{
        DB::table('planes')->insert([
            'tipo' => $request->tipo,
            'montoTitular' => $request->montoTitular,
            'montoBeneficiario' => $request->montoBeneficiario,
            'montoMascota' => $request->montoMascota,
            'frecuencia' => $request->frecuencia,
            'estado' => $request->estado,
            'ahorro'=>$ahorro,
            'default'=>$request->default,
            'fechacreado'=> $request->fechacreado,
           'creado'=>$request->creado,
   
        ]);



    }
    return redirect()->route('productos.index')
    ->with('success','Product created successfully.');


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
    public function edit($product)
    {
   /// return ($product);
    //7///$product = Planes::find($product)->get();

    $products = DB::table('planes')
    ->select('*')
      ->where('id', '=', $product)
    ->first();

   

        return view('productos.edit',compact('products'));
    }
  
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

$defaul=0;
$ahorro=0;
$hoy = date("Ymdhis");
$modificado="jgarcia";
if($request->ahorro==1){

    $ahorro1 = DB::table('planes')
    ->select('ahorro')
    ->where('ahorro', '=', 1)
    ->count();
  
  ///  dd($plan+"1");
if($ahorro1==1){



$ahorro=0;





}else{

    $ahorro=1;
   
}

}

    if($request->default==1){

        $plan = DB::table('planes')
        ->select('default')
        ->where('default', '=', 1)
        ->count();
      
      ///  dd($plan+"1");
if($plan==1){



    $defaul=0;





    }else{

        $defaul=1;
       
    }

}




    DB::table('planes')->where('id', '=', $id)->update(
    
        ['tipo' => $request->tipo,
        'montoTitular' => $request->montoTitular,
        'montoBeneficiario' => $request->montoBeneficiario,
        'estado' => $request->estado,
        'default' => $defaul,
        'fechamodifica'=>$hoy,
        'montoMascota'=> $request->montoMascota,
        'ahorro'=> $ahorro,
        'modificado'=>$modificado
        ]
    );

 //  $product->update($request->all());
      
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

      //  dd();
        $product = Planes::find($product);
 
$product->delete();
       
        return redirect()->route('productos.index')
                        ->with('success','Plan  Eliminado Correcto');
    }
}


