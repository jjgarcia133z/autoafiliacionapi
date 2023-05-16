<?php

namespace App\Http\Controllers;
use Response;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class UbicacionController extends Controller
{
    public function getUbicacion($distelec)
    {
        $distritos = DB::table('distritos')
            ->where('distritos.CODIGODISTRITO_C', $distelec)
            ->get();
        $arrayUbicacion = json_decode($distritos)[0];
        echo(json_decode($distritos)[0]->NAME);




        
        $response	=Response::json($distritos, 200, [], JSON_NUMERIC_CHECK);
        return $response;
    }

    public function getProvincias()
    {
        $provincias = DB::table('provincia')->get();
        $response	= Response::json($provincias, 200, [], JSON_NUMERIC_CHECK);
        return $response;
    }

    

    public function getCantones($distelec)
    {
        $cantones = DB::table('canton')
        ->where('idProvincia', $distelec)
            ->get();
        $response	=	Response::json($cantones, 200, [], JSON_NUMERIC_CHECK);
        return $response;
    }

    public function getDistritos($distelec)
    {
        $original_string = $distelec;
        $distelec = str_pad($original_string, 8, '0', STR_PAD_LEFT);
       


        $distritos = DB::table('distrito')
            ->where('idCanton', $distelec)
            ->get();
        $response	=	Response::json($distritos, 200, [], JSON_NUMERIC_CHECK);
        return $response;
    }

    public function getProvinciaById($code)
    {
        $provincia = Provincias::where('CODIGOPROVINCIA', $code)->first();
        return $provincia;
    }

    public function getCantonById($code)
    {
        $provincia = Cantones::where('CODIGOCANTON_C', $code)->first();
        return $provincia;
    }

    public function getDistritoById($code)
    {
        $provincia = Distritos::where('CODIGODISTRITO_C', $code)->first();
        return $provincia;
    }
}

