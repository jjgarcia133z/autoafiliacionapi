<?php

namespace App\Http\Controllers;
use App\Models\Planesadd;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PlanesaddController extends Controller
{
   
        public function index()
        {
    
     
         $planesadd = DB::table('planesadd')
         ->select('*')
    ///    ->where('estado', '=', 1)
         ->get();
         


         
      ////   dd($planesadd);

            return view('planesadd.index',compact('planesadd'));
               
        }
      
        /**
         * Show the form for creating a new resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function create()
        {
            
    
    
            return view('planesadd.create');
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
            /*
            <td>{{ $planesad->fecha_creacion }}</td>
            <td>{{ $planesad->creado }}</td>
            <td>{{ $planesad->fecha_modificacion }}</td>
            <td>{{ $planesad->modificado }}</td>
            <td>{{ $planesad->empresa }}</td>
            */

            $hoy = date("Ymdhis");
            $creado="jgarcia";
            $empresa="Hospital Metropolitalo";

            $estado = DB::table('planesadd')
            ->select('*')
            ->where('estado', '=', 1)
            ->count();
  if($request->estado==1){
  if($estado>=1){


            DB::table('planesadd')->insert([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'precio' => $request->precio,
                'estado' => 0,
                'fecha_creacion'=> $hoy,
               'creado'=> $creado,
                //'fecha_modificacion'=> $request->Estado,
                //'modificado'=> $request->Estado,
                'empresa'=> $empresa,
            ]);
        }
          else{
            DB::table('planesadd')->insert([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'precio' => $request->precio,
                'estado' => $request->estado,
                'fecha_creacion'=> $hoy,
               'creado'=> $creado,
                //'fecha_modificacion'=> $request->Estado,
                //'modificado'=> $request->Estado,
                'empresa'=> $empresa,
            ]);


          }
      
        }else{
            DB::table('planesadd')->insert([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'precio' => $request->precio,
                'estado' => $request->estado,
                'fecha_creacion'=> $hoy,
               'creado'=> $creado,
                //'fecha_modificacion'=> $request->Estado,
                //'modificado'=> $request->Estado,
                'empresa'=> $empresa,
            ]);





        }
        return redirect()->route('planadd.index')
        ->with('success','plan created successfully.');
    
 
           
          
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
        public function edit( $id)
        {
           
                $planadd = DB::table('planesadd')
                ->select('*')
                ->where('id', '=', $id)
                ->first();
            return view('planesadd.edit',compact('planadd'));
        }
      
        /**
         * Update the specified resource in storage.
         *
         * @param  \Illuminate\Http\Request  $request
         * @param  \App\Models\Product  $product
         * @return \Illuminate\Http\Response
         */
        public function update(Request $request,$id)
        {
            ///dd($request);
            $hoy = date("Ymdhis");
            $modificado="jgarcia";
          ///  $empresa="Hospital Metropolitalo";
          $estado = DB::table('planesadd')
          ->select('*')
          ->where('estado', '=', 1)
          ->count();
   ///   dd($request);
          if($request->estado==1){

if($estado>=1){



            DB::table('planesadd')->where('id', '=', $id)->update(
    
                ['nombre' => $request->name,
                'descripcion' => $request->descripcion,
                'precio' => $request->precio,
                'fecha_modificacion'=> $hoy,
               'modificado'=> $modificado,
                ///'empresa'=> $empresa,
                'estado' => 0
                ]
            );



        }else{


            DB::table('planesadd')->where('id', '=', $id)->update(
    
                ['nombre' => $request->name,
                'descripcion' => $request->descripcion,
                'precio' => $request->precio,
                'fecha_modificacion'=> $hoy,
               'modificado'=> $modificado,
             //    'empresa'=> $empresa,
                'estado' => 1
                ]
            );

        }


    }else{

        DB::table('planesadd')->where('id', '=', $id)->update(
    
            ['nombre' => $request->name,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'fecha_modificacion'=> $hoy,
           'modificado'=> $modificado,
            ///'empresa'=> $empresa,
            'estado' => 0
            ]
        );



    }


            
         $planesadd = DB::table('planesadd')
         ->select('*')
    ///    ->where('estado', '=', 1)
         ->get();
         


         
      ////   dd($planesadd);

            return view('planesadd.index',compact('planesadd'));
          ///  return redirect()->route('planesadd.index')
             ///               ->with('success','Product updated successfully');
        }
        /**
         * Remove the specified resource from storage.
         *
         * @param  \App\Models\Product  $product
         * @return \Illuminate\Http\Response
         */
        public function destroy($product)
        {
            DB::table('planesadd')->where('id', $product)->delete();
            return redirect()->route('planadd.index')
                            ->with('success','Plan  Eliminado Correcto');
        }
    
    
    
}

