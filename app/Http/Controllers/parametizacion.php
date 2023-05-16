<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class parametizacion extends Controller
{


   public function sms()
    {        
      $plan = DB::table('sms_registro')
      ->select('sms')
      ->orderBy('id_sms', 'desc')
      ->first();
        return $plan;
    }


    public function Planes()
    {        
      $plan = DB::table('planes')
      ->select('*')
        ->where('estado', '=', 1)
      ->get();
        return $plan;
    }
    public function Planesadd()
    {               
      $planesadd = DB::table('planesadd')
      ->select('*')
        ->where('estado', '=', 1)
      ->get();
        return $planesadd;
    }

 public function tipoAnimal()
    {
      $tipoAnimal = DB::table('tipoAnimal')
      ->select('*')
        ->where('activo', '=', 1)
      ->get();
        return $tipoAnimal;
    }




    public function politicas()
    {
      $planesadd = DB::table('politicas')
      ->select('*')
        ->where('activo', '=', 1)
      ->get();
        return $planesadd;
    }
  public function genero()
    {
      $planesadd = DB::table('genero')
      ->select('*')
        ->where('activo', '=', 1)
      ->get();
        return $planesadd;
    }
    public function parentesco()
    {
      $planesadd = DB::table('parentesco')
      ->select('*')
        ->where('activo', '=', 1)
      ->get();
        return $planesadd;
}
 public function tipoidentificacion()
    {
      $planesadd = DB::table('tipoidentificacion')
      ->select('*')
        ->where('activo', '=', 1)
      ->get();
        return $planesadd;
        }




}
