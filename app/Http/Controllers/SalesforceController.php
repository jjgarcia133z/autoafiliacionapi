<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;

class SalesforceController extends Controller
{

    public function checkapi()
    {
        ///$apiSF= DB::table('apisf')->first();

        $hoy = date("Ymdhis");
        $expire = $hoy +1000000;
       $client_id="3MVG9qG3qyJ7I8u3Ow0gStJKPMXkNprZ3zS0I.mv_iQ4xrAHKDSpXQvXs4yHEo.V1ZlaBlt0i021Jfq_zJz.0";
        $client_secret="0B8CF60888E69B97A38021502DAC3ADD38775126F7FD4BF51B165CD7164A2273";
        $user= "mesquivelc@medismart.net.test";
        $pass="Migue2000";
       // $date= $apiSF->date;
        if (true) {



        $ch = ("https://medismart--test.my.salesforce.com/services/oauth2/token?grant_type=password&client_id=".$client_id."&client_secret=".$client_secret."&username=".$user."&password=".$pass);      
        $response = Http::post($ch);
        $res=json_decode($response, true);

   return $res;

 }
    }


    public function getClientInformation()
    {


        $responseSF = $this->consultarReferido('CLI195261');

dd($responseSF);
        $url = 'https://medismart--test.sandbox.my.salesforce.com/services/apexrest/consultainfoclienteapi?cli=CLI195261';

        $toke="00D8M0000004cip!AQkAQNsJy8RLLE_2BJtJpXKppzgLlhmo2eFytzD0I7m.ESlLL8CWTgICEOZ3PDT5BmsnleD2.lThblPP0ighBsQjNXswF6vp";



    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $toke,        
        'Content-Type'        => 'application/json'
    ])->get($url);

       
    return $response;



    }

    public function consultarReferido($cli)
    {

        $toke="00D8M0000004cip!AQkAQNsJy8RLLE_2BJtJpXKppzgLlhmo2eFytzD0I7m.ESlLL8CWTgICEOZ3PDT5BmsnleD2.lThblPP0ighBsQjNXswF6vp";


        $url = "https://medismart--test.sandbox.my.salesforce.com/services/apexrest/consultareferenciasapi?cliben=".$cli;
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $toke,        
            'Content-Type'        => 'application/json'
        ])->get($url);
    
           
        return $response;
    
    
    }





}
