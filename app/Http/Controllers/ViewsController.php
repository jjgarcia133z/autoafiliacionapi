<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
class ViewsController extends Controller
{


    
    
    function buscarCedula(Request $request,$Cedula){

        $baseUrl = env('API_ENDPOINTTST');
        $url = $baseUrl ."/api/persona/buscarCedula.php?user=sfconsult&password=8Rh8hcRFMyGmqimA&buscarCedula=". $Cedula;
        $response = Http::get($url);
        return $response;
    }
}
