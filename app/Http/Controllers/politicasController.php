<?php

namespace App\Http\Controllers;
use App\Models\Politicas;
use App\Models\Planes;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class politicasController extends Controller
{
    public function index()
    {
        $politicas = Politicas::all();
     
        return view('politica.index',compact('politicas'));
           
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
      $politi = DB::table('politicas')
      ->select('activo')
      ->where('activo', '=', 1)
      ->count();
        //$PlanesSalesforces = PlanesSalesforce::all();
       // dd($PlanesSalesforces);
       
$PlanesSalesforces = DB::table('planes_salesforces')
            ->select('id', 'codigo','nombre','montoTitular','montoAdicional')
            ->get();
//(/)        RENAME TABLE planes_salesforce TO planes_salesforces;


return view('politica.create',compact('PlanesSalesforces','politi'));
       

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
      
      
   
      

if(true){
    Politicas::create($request->all());
    return redirect()->route('politica.index')
    ->with('success','Politicas created successfully.');
}else{


 

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
    public function edit(Politicas $politica)
    {
      $politi = DB::table('politicas')
      ->select('activo')
      ->where('activo', '=', 1)
      ->where('id', '=', $politica->id)
      ->count();
      if($politi ==0){
        $politi = DB::table('politicas')
        ->select('activo')
        ->where('activo', '=', 1)
        ->count();
        if( $politi ==1){
            $politi =0;

        }else{
            $politi =1;

        }
     


      }

     
      
      
        return view('politica.edit',compact('politica','politi'));
    }
  
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Politicas $Politicas)
    {
          //  dd($request->id);
        ///
        //$activo
        if($request->activo=="on"){
            $activo=1;
        }else{

            $activo=0;
        }
        //$request->validate([
         //   'name' => 'required',
       //     'detail' => 'required',
     //   ]);
     Politicas::where('id', $request->id)->update([
        'url' => $request->url,
        'version' => $request->version,
        'activo' => $activo
    ]);
      
       // $Politicas->update($request->all());
      
      return redirect()->route('politica.index')
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
