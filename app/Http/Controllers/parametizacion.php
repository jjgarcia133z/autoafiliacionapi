<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class parametizacion extends Controller
{
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

    public function politicas()
    {
      $planesadd = DB::table('politicas')
      ->select('*')
        ->where('activo', '=', 1)
      ->get();
        return $planesadd;
    }




}
