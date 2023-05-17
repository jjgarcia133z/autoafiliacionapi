<?php

namespace App\Http\Controllers;
use Response;
use App\Models\Sms_Registro;

use App\Models\promociones;
use App\Models\afiliado;
use App\Models\mascota;
use App\Models\beneficiario;


use Illuminate\Support\Facades\View; 


use PDF;
use Dompdf\Dompdf;
use Illuminate\Support\Collection;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Jobs\CodeEmail;
use Illuminate\Support\Facades\Http;
class ViewsController extends Controller
{







    public function updateop(Request $request) {

   
       /// response()->json($data);
   $data=$request;
   
$beneSF=$request->beneficiarios;
 
$mascotaSF=$request->mascotas;
$promoSF=$request->promociones;

   $dataSF = array('cli' => $request->cli,
   'rebajoDias' => $request->rebajoDias,
   'op' => $request->op,
   

   'beneficiarios'=>$beneSF,
               'mascotas'=>$mascotaSF,
                'promociones'=>$promoSF,
    

                
);
              ///  $responseSF = $this->primeraventa(json_encode($dataSF));
  //return $dataSF;
$data=json_encode($dataSF);
        $url = "https://medismart--test.sandbox.my.salesforce.com/services/apexrest/account/CreateOPTempRecalculated";
   
       $ch = curl_init($url);
    
        //configuracion de la conexion
        $ch = $this->setConfiguration($ch, 'POST', $data);
  
        $responseSF = curl_exec($ch);
    
        //cerrar conexion
        curl_close($ch);
        $array = json_decode($responseSF, true);

        $subtotalsiniva=0;
  if( isset($array["costoPlan"])){
    $totalIva = $array["costoPlan"]* 0.13;
    $Totalivam=$totalIva+$array["costoPlan"];

///     dd($totalIva);
 $array['iva'] =$totalIva;

 $array['totaiva'] =$Totalivam;
 $array['frecuencia'] =$request->frecuenciaPago;

}

$porcentaje= $array["oportunidad"]["OPDescuento"]/100;


    $descuento= $subtotalsiniva*$porcentaje;

    $subtotacondescuento=$subtotalsiniva-$descuento;
    $ivaProrateo=$subtotacondescuento*0.13;

    $totalconiva=$subtotacondescuento+$ivaProrateo;


$Prorateoivas=($array['montoProrateo']*0.13)+($array['montoProrateo']);
    $array['subtotalsiniva']=$subtotalsiniva;

$array['prorateiva']=$Prorateoivas;


        $array['porcentaje']=$porcentaje;

        $array['descuento']=$descuento;
        $array['subtotacondescuento']=$subtotacondescuento;

        $array['ivaProrateo']=$ivaProrateo;

        $array['totalconiva']=$totalconiva;
        $array['frecuenciaPago']=$request->rebajoDias;




      /// return $responseSF;
      return Response::json(($array), 201);


       
 

/// return Response::json(["body" => json_encode($responseSF) ], 201);
        
    }
public function getcontrato($cedula){


    $day = date('d');   // day of the month, with leading zeros (e.g. "01" to "31")
    $month = date('m'); // month number, with leading zeros (e.g. "01" to "12")
    $year = date('Y');   


    $res = $this->getData($cedula);
    $res = json_decode($res);
    $res = json_decode($res, true);

    if(true){

        $afiliado = DB::table('afiliados')
        ->select('*')
          ->where('cedula', '=', $cedula)
        ->get();

  
    $Nombre=$afiliado[0]->nombrecompleto;
   // return($Nombre);

     ///   $Nombre = 'Jonathan García Alfaro';
        $Nacionalidad = $afiliado[0]->nacionalidad;
        $EstadoCivil = $afiliado[0]->estadocivil;
        $vecino=$afiliado[0]->direccion;
        $profesion=$afiliado[0]->profesion;
        $cedula =$cedula;
        $dia=$day;
        $mes=$month;
        $ano=$year;



    $html =View::make('invoice' )->withnombre($Nombre)->withnacionalidad($Nacionalidad)
 
 
    ->withestadocivil($EstadoCivil)->withvecino($vecino)
    
    
    ->withprofesion($profesion)->withcedula($cedula)->withdia($dia)
    
    ->withmes($mes)->withano($ano);
    return $html;
    }else{
    return false;
    }
}







 public function validardata($cedula,$correo,$telefono) {
  
    
        $res = $this->CustomerStatus($cedula,$correo,$telefono);
        return json_decode($res,true);
        
    }



    public function CustomerStatus($cedula,$correo,$telefono)
    {
        
        $url = "https://medismart--test.sandbox.my.salesforce.com/services/apexrest/dataValid/CustomerStatus?cedula=".$cedula."&numero=".$telefono."&correo=".$correo;

       // dd($url);
        $ch = curl_init($url);
        
        //configuracion de la conexion
        $ch = $this->setConfiguration($ch, 'GET');

        //ejecutar
        $responseSF = curl_exec($ch);
       // dd($responseSF);
        //cerrar conexion
        curl_close($ch);
        if ($responseSF === false) {
            return null;
        }
  
        return $responseSF;
    }








public function cambiostatus($data)
{


   /// https://medismart--test.sandbox.my.salesforce.com/services/apexrest/account/ReactivateSaleProcess?identification=108760987



    $url = "https://medismart--test.sandbox.my.salesforce.com/services/apexrest/account/ReactivateSaleProcess?identification=".$data;

    // dd($url);
     $ch = curl_init($url);
     
     //configuracion de la conexion
     $ch = $this->setConfiguration($ch, 'GET');

     //ejecutar
     $responseSF = curl_exec($ch);
    // dd($responseSF);
     //cerrar conexion
     curl_close($ch);
     if ($responseSF === false) {
         return null;
     }
   ///  
  /// dd($responseSF);

     return $responseSF;



}









function pagoFactura1(Request $request){
    

    $SaleForce=$request;
  ///  $responseSF = $this->accionesTarjetas(($SaleForce));
  
   /// return  $SaleForce['cli'];

///dd($SaleForce['cli']);


 if(isset($SaleForce['tarjeta']) and isset($SaleForce['cli']) and isset($SaleForce['tarjeta']['numeroTarjeta'])and isset($SaleForce['tarjeta']['tarjetaHabiente'])and isset($SaleForce['tarjeta']['tipoTarjeta']) and isset($SaleForce['tarjeta']['fechaVencimiento']))
   
{

    $cli=$SaleForce['cli'];
    $numeroTarjeta=$SaleForce['tarjeta']['numeroTarjeta'];
    $tarjetaHabiente=$SaleForce['tarjeta']['tarjetaHabiente'];
    $tipoTarjeta=$SaleForce['tarjeta']['tipoTarjeta'];
     $fechaVencimiento=$SaleForce['tarjeta']['fechaVencimiento'];
   //7 $factura = json_decode($afiliado->facturaSF);

    define('AES_256_CBC', 'aes-256-cbc');

    // Generate a 256-bit encryption key
    // This should be stored somewhere instead of recreating it each time
    $encryption_key = base64_decode("j/gwTi0igUda93H9DTwKyANnzBY7PaEzZ/7hnwCObpA=");
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));

    // Generate an initialization vector
    // This *MUST* be available for decryption as well
    //$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(AES_256_CBC));
    $dataSF=array(
        'cli' => $cli,
        'tarjeta' => array(
            "numeroTarjeta" => openssl_encrypt(str_replace(' ', '', $numeroTarjeta), 'AES-256-CBC', $encryption_key, $options=0, $iv),
            "tarjetaHabiente" =>$tarjetaHabiente,
            "tipoTarjeta" => $tipoTarjeta,
            "fechaVencimiento" => openssl_encrypt($fechaVencimiento, 'AES-256-CBC', $encryption_key, $options=0, $iv),
            "IV" =>  base64_encode($iv)
        ),
        'IdWebTransaction' => 12121,
    );
    //7return $dataSF;
   // $salesforcontroller = new SalesforceController;
    $responseSF = $this->primeraventapago1(json_encode($dataSF));
    ///$responseSF = $this->accionesTarjetas(($SaleForce));
    return $responseSF;
    if ($responseSF === false) {
        Activity_Log::create([
            'descripcion' => 'Error proceso de pago. Timeout de Salesforce',
            'session' => Session::getId(),
        ]);

        return Response::json($this->timeOut($cli, $factura->idFactura), 201);




}
}else{


  return Response::json("Faltan Datos de la tarjeta o el CLI");

}
}



    public function primeraventapago1($data)
    {
        $url = "https://medismart.my.salesforce.com/services/apexrest/primeraventapagoapi";
        ///$url="https://medismart--test.cs77.my.salesforce.com/services/apexrest/primeraventapagoapi";
        
       //$url="https://medismart--test.sandbox.my.salesforce.com/services/apexrest/primeraventapagoapi";
        $ch = curl_init($url);
        
        //configuracion de la conexion
        $ch = $this->setConfiguration1($ch, 'POST', $data);
    
        //ejecutar
        $responseSF = curl_exec($ch);
    
        //cerrar conexion
        curl_close($ch);
    
        return $responseSF;
    }
    public function checkapi1()
    {
        $apiSF= DB::table('apisf')->first();

        $hoy = date("Ymdhis");
        $expire = $hoy +1000000;
        $client_id=$apiSF->client_id;
        $client_secret=$apiSF->client_secret;
        $user= $apiSF->username;
        $pass=$apiSF->password;
        $date= $apiSF->date;

      // dd( $hoy);
        if (true) {




            $baseUrl = env('API_ENDPOINT');
          //  $url = "https://medismart--test.my.salesforce.com/services/oauth2/token?grant_type=password&client_id=".$client_id."&client_secret=".$client_secret."&username=".$user."&password=".$pass;
          
            $url = "https://medismart.my.salesforce.com/services/oauth2/token?grant_type=password&client_id=".$client_id."&client_secret=".$client_secret."&username=".$user."&password=".$pass;
           
            $response = Http::post($url);
            $json = $response;
            
            $res = json_decode($json);
    
    

       /// dd($res);

           /// $res=json_decode($response);

        
           
            DB::table('apisf')->update(['accessToken' => $res->access_token]);
            DB::table('apisf')->update(['signature' => $res->signature]);
            DB::table('apisf')->update(['date' => $expire]);


         
        }
    }

    public function setConfiguration1($ch, $method, $fields = null)
    {
        $this->checkapi1();

        $apiSF= DB::table('apisf')->first();

        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Authorization: Bearer '. $apiSF->accessToken ,
            )
        );

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        if ($fields != null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10000);

        return $ch;

        
    }











    public function contra($email, $code=null)
    {   
      
        $day = date('d');   // day of the month, with leading zeros (e.g. "01" to "31")
        $month = date('m'); // month number, with leading zeros (e.g. "01" to "12")
        $year = date('Y');   


        $data = array(
            'Nombre' => 'Jonathan García Alfaro',
            'Nacionalidad' => 'costarricense',
            'EstadoCivil' => 'Casado',
            'vecino'=>'San jose',
            'profesion'=>'Programador',
            'cedula' =>'207560693',
            'dia'=>$day,
            'mes'=>$month,
            'ano'=>$year

        );


///      return view('invoice')->with($data);



        $pdf = PDF::loadView('invoice', ['invoice' => $data] );

      ///  $pdf->loadHTML('<h1>Test</h1>');
        return $pdf->stream();

    }

public function primeraventapago($data)
{
    $url = "https://medismart.my.salesforce.com/services/apexrest/primeraventapagoapi";
    $url="https://medismart--test.cs77.my.salesforce.com/services/apexrest/primeraventapagoapi";
    
   $url="https://medismart--test.sandbox.my.salesforce.com/services/apexrest/primeraventapagoapi";
    $ch = curl_init($url);
    
    //configuracion de la conexion
    $ch = $this->setConfiguration($ch, 'POST', $data);

    //ejecutar
    $responseSF = curl_exec($ch);

    //cerrar conexion
    curl_close($ch);

    return $responseSF;
}







function pagoFactura(Request $request){
    
if(true){
    $numeroTarjeta=$request['tarjeta']['numeroTarjeta'];
    $numeroTarjeta = substr($numeroTarjeta, 0, 4);

if($numeroTarjeta=="1111"){



    return json_decode('{"code":1,"resultado":false,"mensaje":"DENEGADA"}');

}


if($numeroTarjeta=="2222"){



    return json_decode('{"code":2,"resultado":false,"mensaje":"TARJETA INVALIDA"}');

}

if($numeroTarjeta=="3333"){



    return json_decode('{"code":3,"resultado":false,"mensaje":"DENEGADA FI"}');

}

if($numeroTarjeta=="4444"){



    return json_decode('{"code":4,"resultado":true,"mensaje":"OK"}');

}
}
else{
    $SaleForce=$request;
  ///  $responseSF = $this->accionesTarjetas(($SaleForce));
  
   /// return  $SaleForce['cli'];


    $cli=$SaleForce['cli'];
    $numeroTarjeta=$SaleForce['tarjeta']['numeroTarjeta'];
    $tarjetaHabiente=$SaleForce['tarjeta']['tarjetaHabiente'];
    $tipoTarjeta=$SaleForce['tarjeta']['tipoTarjeta'];
     $fechaVencimiento=$SaleForce['tarjeta']['fechaVencimiento'];
   //7 $factura = json_decode($afiliado->facturaSF);

    define('AES_256_CBC', 'aes-256-cbc');

    // Generate a 256-bit encryption key
    // This should be stored somewhere instead of recreating it each time
    $encryption_key = base64_decode("j/gwTi0igUda93H9DTwKyANnzBY7PaEzZ/7hnwCObpA=");
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));

    // Generate an initialization vector
    // This *MUST* be available for decryption as well
    //$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(AES_256_CBC));
    $dataSF=array(
        'cli' => $cli,
        'tarjeta' => array(
            "numeroTarjeta" => openssl_encrypt(str_replace(' ', '', $numeroTarjeta), 'AES-256-CBC', $encryption_key, $options=0, $iv),
            "tarjetaHabiente" =>$tarjetaHabiente,
            "tipoTarjeta" => $tipoTarjeta,
            "fechaVencimiento" => openssl_encrypt($fechaVencimiento, 'AES-256-CBC', $encryption_key, $options=0, $iv),
            "IV" =>  base64_encode($iv)
        ),
        'IdWebTransaction' => 12121,
    );
    //7return $dataSF;
   // $salesforcontroller = new SalesforceController;
    $responseSF = $this->primeraventapago(json_encode($dataSF));
    ///$responseSF = $this->accionesTarjetas(($SaleForce));
    return $responseSF;
    if ($responseSF === false) {
        Activity_Log::create([
            'descripcion' => 'Error proceso de pago. Timeout de Salesforce',
            'session' => Session::getId(),
        ]);

        return Response::json($this->timeOut($cli, $factura->idFactura), 201);

    }


}

}







function agoFactura(Request $request){
    

    $SaleForce=$request;
  ///  $responseSF = $this->accionesTarjetas(($SaleForce));
  
   /// return  $SaleForce['cli'];


    $cli=$SaleForce['cli'];
    $numeroTarjeta=$SaleForce['tarjeta']['numeroTarjeta'];
    $tarjetaHabiente=$SaleForce['tarjeta']['tarjetaHabiente'];
    $tipoTarjeta=$SaleForce['tarjeta']['tipoTarjeta'];
     $fechaVencimiento=$SaleForce['tarjeta']['fechaVencimiento'];
   //7 $factura = json_decode($afiliado->facturaSF);

    define('AES_256_CBC', 'aes-256-cbc');

    // Generate a 256-bit encryption key
    // This should be stored somewhere instead of recreating it each time
    $encryption_key = base64_decode("j/gwTi0igUda93H9DTwKyANnzBY7PaEzZ/7hnwCObpA=");
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));

    // Generate an initialization vector
    // This *MUST* be available for decryption as well
    //$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(AES_256_CBC));
    $dataSF=array(
        'cli' => $cli,
        'tarjeta' => array(
            "numeroTarjeta" => openssl_encrypt(str_replace(' ', '', $numeroTarjeta), 'AES-256-CBC', $encryption_key, $options=0, $iv),
            "tarjetaHabiente" =>$tarjetaHabiente,
            "tipoTarjeta" => $tipoTarjeta,
            "fechaVencimiento" => openssl_encrypt($fechaVencimiento, 'AES-256-CBC', $encryption_key, $options=0, $iv),
            "IV" =>  base64_encode($iv)
        ),
        'IdWebTransaction' => 12121,
    );
    //7return $dataSF;
   // $salesforcontroller = new SalesforceController;
    $responseSF = $this->primeraventapago(json_encode($dataSF));
    ///$responseSF = $this->accionesTarjetas(($SaleForce));
    return $responseSF;
    if ($responseSF === false) {
        Activity_Log::create([
            'descripcion' => 'Error proceso de pago. Timeout de Salesforce',
            'session' => Session::getId(),
        ]);

        return Response::json($this->timeOut($cli, $factura->idFactura), 201);




}

}













   
 public function validardatos($cedula,$correo,$telefono) {
        //VALIDAR 
    
        $res = $this->getData($cedula);

        $res = json_decode($res);
        $res = json_decode($res, true);
        $telestatus=0;
        $correostatus=0;
        if ($res['existe']) {

          //  dd($res['petResults'][0]['estado']);
            if (count($res['accountResults']) > 0){
                $telefonosf=$res['accountResults'][0]['telefono'];
                $correosf=$res['accountResults'][0]['correo'];
               /// $status=$res['accountResults'][0]['estado'];


               if($telefonosf==$telefono){
                $telestatus=1;
                

               }if($correosf==$correo){


                $correostatus=1;


               }


              ///  telefono
      ///          correo
    //
                return ['telefono'=> $telestatus, 'correo'=> $correostatus];

            }
            
            /*else if (count($res['benResults']) > 0){


                $status=$res['benResults'][0]['estado'];
         
           return ['status'=> $status, 'existe'=> true, 'afiliado'=> false];
            }
           

*/



        }else{
            return ['no existe','status'=>0, 'existe'=>false];
        }
    }

    

 public function SaveAfiliado(Request $request)
    {
        //dd($request->codreferidocliente==null ? " " : $request->codreferidocliente);

        ///    $cedula = afiliado::where('cedula', '=', $request->cedula);

        /*
        $cedula = DB::table('afiliados')
        ->select('*')
          ->where('cedula', '=', $request->cedula)
          ->get();*/
        // busca el usuario con el ID especificado

        ///dd($request->direccion);
        $cedula = afiliado::where('cedula', '=', $request->cedula)->first();
        //  $tamaño = $cedula->count();
        if ($cedula) {
            ///$cedula->cedula = "207560693";



            ////////////////
            ////$afiliado = new afiliado;
            $cedula->rebajodias = $request->rebajoDias ?? "";
            $cedula->codreferidocliente = $request->codreferidocliente == null ? " " : $request->codreferidocliente;
            $cedula->frecuenciapago = $request->frecuenciaPago ?? "";
            $cedula->tipoidentificacion = $request->tipoIdentificacion ?? "";
            $cedula->profesion = $request->profesion ?? "";
            $cedula->nacionalidad = $request->nacionalidad ?? "";
            $cedula->nombre = $request->nombre ?? "";
            $cedula->apellido1 = $request->apellido1 ?? "";
            $cedula->apellido2 = $request->apellido2 ?? "";

            $cedula->telefono1 = $request->telefono1 ?? "";
            $cedula->codigotel1 = $request->codigotel1 ?? "";
            $cedula->telefono2 = $request->telefono2 ?? "";
            $cedula->codigotel2 = $request->codigotel2 ?? "";




            $cedula->nombrecompleto = $request->nombreCompleto ?? "";
            $cedula->fechanacimiento = $request->fechaNacimiento ?? "";
            $cedula->telefonocelular = $request->telefonoCelular ?? "";
            $cedula->correo = $request->correo ?? "";
            $cedula->direccion = $request->direccion ?? "";
            $cedula->provincia = $request->provincia ?? "";
            $cedula->canton = $request->canton ?? "";
            $cedula->distrito = $request->distrito ?? "";
            $cedula->estadocivil = $request->estadoCivil ?? "";
            $cedula->genero = $request->genero ?? "";
            $cedula->coberturaOncosmart = $request->coberturaOncosmart == true ? 1 : 0;

            $cedula->save();



            ////////////////////
            ///   dd(  $cedula);
        } else {

            $afiliado = new afiliado;
            $afiliado->rebajodias = $request->rebajoDias ?? "";
            $afiliado->codreferidocliente = $request->codreferidocliente == null ? " " : $request->codreferidocliente;
            $afiliado->frecuenciapago = $request->frecuenciaPago ?? "";
            $afiliado->tipoidentificacion = $request->tipoIdentificacion ?? "";
            $afiliado->cedula = $request->cedula ?? "";
            $afiliado->profesion = $request->profesion ?? "";
            $afiliado->nacionalidad = $request->nacionalidad ?? "";

            $afiliado->telefono1 = $request->telefono1 ?? "";
            $afiliado->codigotel1 = $request->codigotel1 ?? "";
            $afiliado->telefono2 = $request->telefono2 ?? "";
            $afiliado->codigotel2 = $request->codigotel2 ?? "";


            $afiliado->nombrecompleto = $request->nombreCompleto ?? "";
            $afiliado->nombre = $request->nombre ?? "";
            $afiliado->apellido1 = $request->apellido1 ?? "";
            $afiliado->apellido2 = $request->apellido2 ?? "";
            $afiliado->fechanacimiento = $request->fechaNacimiento ?? "";
            $afiliado->telefonocelular = $request->telefonoCelular ?? "";
            $afiliado->correo = $request->correo ?? "";
            $afiliado->direccion = $request->direccion ?? "";
            $afiliado->provincia = $request->provincia ?? "";
            $afiliado->canton = $request->canton ?? "";
            $afiliado->distrito = $request->distrito ?? "";
            $afiliado->estadocivil = $request->estadoCivil ?? "";
            $afiliado->genero = $request->genero ?? "";
            $afiliado->coberturaOncosmart = $request->coberturaOncosmart == true ? 1 : 0;

            $afiliado->save();
        }

        if (isset($request->beneficiarios)) {

            $array = $request->beneficiarios;

            DB::table('beneficiarios')
            ->where('cedulatitular', $request->cedula)
            ->delete();
            foreach ($array as $key => $value) {
                $cedulabn = beneficiario::where('cedula', '=', $value['cedula'])->first();
                //  $tamaño = $cedula->count();
                // dd($cedulabn);
                //    dd($cedulabn);
                if ($cedulabn) {

                    $cedulabn->cedulatitular = $request->cedula;
                    //    $bene->tipoidentificacion = $value['tipoIdentificacion'];
                    $cedulabn->tipoidentificacion = $value['tipoIdentificacion'] ?? "";
                    $cedulabn->cedula = $value['cedula'] ?? "";
                    $cedulabn->nombrecompleto = $value['nombreCompleto'] ?? "";
                    $cedulabn->nombre = $value['nombre'] ?? "";
                    $cedulabn->apellido1 = $value['apellido1'] ?? "";
                    $cedulabn->apellido2 = $value['apellido2'] ?? "";

                    $cedulabn->profesion = $value['profesion'] ?? "";
                    
                    $cedulabn->fechanacimiento = $value['fechaNacimiento'] ?? "";
                    $cedulabn->telefonocelular = $value['telefonoCelular'] ?? "";
                    $cedulabn->correo = $value['correo'] ?? "";

                    $cedulabn->parentesco = $value['parentesco'] ?? "";
                    $cedulabn->telefono1 = $value['telefono1'] ?? "";
                    $cedulabn->codigotel1 = $value['codigotel1'] ?? "";
                    $cedulabn->telefono2 = $value['telefono2'] ?? "";
                    $cedulabn->codigotel2 = $value['codigotel2'] ?? "";

                    $cedulabn->provincia = $value['provincia'] ?? "";
                    $cedulabn->canton = $value['canton'] ?? "";
                    $cedulabn->distrito = $value['distrito'] ?? "";
                    $cedulabn->estadocivil = $value['estadoCivil'] ?? "";
                    $cedulabn->genero = $value['genero'] ?? "";
                    $cedulabn->coberturaOncosmart = $value['coberturaOncosmart'] == true ? 1 : 0;
                    $cedulabn->save();

                } else {



                    ///echo "Elemento ".$key.":<br>";
                    // foreach($value as $clave => $valor) {

                    $bene = new beneficiario;
                    $bene->cedulatitular = $request->cedula;
                    //    $bene->tipoidentificacion = $value['tipoIdentificacion'];
                    $bene->tipoidentificacion = $value['tipoIdentificacion'] ?? "";
                    $bene->cedula = $value['cedula'] ?? "";
                    $bene->nombrecompleto = $value['nombreCompleto'] ?? "";
                    $bene->nombre = $value['nombre'] ?? "";
                    $bene->apellido1 = $value['apellido1'] ?? "";
                    $bene->apellido2 = $value['apellido2'] ?? "";
                    $bene->profesion = $value['profesion'] ?? "";
                    
                    $bene->fechanacimiento = $value['fechaNacimiento'] ?? "";
                    $bene->telefonocelular = $value['telefonoCelular'] ?? "";

                    $bene->parentesco = $value['parentesco'] ?? "";
                    $bene->telefono1 = $value['telefono1'] ?? "";
                    $bene->codigotel1 = $value['codigotel1'] ?? "";
                    $bene->telefono2 = $value['telefono2'] ?? "";
                    $bene->codigotel2 = $value['codigotel2'] ?? "";

                    $bene->correo = $value['correo'] ?? "";
                    $bene->provincia = $value['provincia'] ?? "";
                    $bene->canton = $value['canton'] ?? "";
                    $bene->distrito = $value['distrito'] ?? "";
                    $bene->estadocivil = $value['estadoCivil'] ?? "";
                    $bene->genero = $value['genero'] ?? "";
                    $bene->coberturaOncosmart = $value['coberturaOncosmart'] == true ? 1 : 0;
                    $bene->save();
                }



                //    }
            }







        }
        if (isset($request->mascotas)) {

            DB::table('mascotas')
            ->where('cedulatitular', $request->cedula)
            ->delete();

            $array = $request->mascotas;
            // dd($array);
            foreach ($array as $key => $value) {
                $nombremas = mascota::where('nombremascota', '=', $value['nombreMascota'])->first();
                //  $tamaño = $cedula->count();

                 
                ///  echo($nombremas);
                if ($nombremas) {

                   /// dd($nombremas);
                    $nombremas->especie = $value['especie'] ?? "";
                    $nombremas->nombremascota = $value['nombreMascota'] ?? "";
                    $nombremas->raza = $value['raza'] ?? "";
                    $nombremas->genero = $value['genero'] ?? "";
                    $nombremas->edad = $value['edad'] ?? "";
                    $nombremas->color = $value['color'] ?? "";
                    $nombremas->fechanacimiento = $value['fechaNacimiento'] ?? "";

                    $nombremas->cedulatitular = $request->cedula;




                    //$cedulabn->coberturaOncosmart = $value['coberturaOncosmart'] == true ? 1 : 0 ;
                    $nombremas->save();

                } else {



                    ///echo "Elemento ".$key.":<br>";
                    // foreach($value as $clave => $valor) {

                    $masc = new mascota;
                    $masc->especie = $value['especie'] ?? "";
                    $masc->nombremascota = $value['nombreMascota'] ?? "";
                    $masc->raza = $value['raza'] ?? "";
                    $masc->genero = $value['genero'] ?? "";
                    $masc->edad = $value['edad'] ?? "";
                    $masc->color = $value['color'] ?? "";
                    $masc->fechanacimiento = $value['fechaNacimiento'] ?? "";
                    
                    $masc->cedulatitular = $request->cedula;
                    $masc->save();


                }



                /// dd($request->beneficiarios[0]['tipoIdentificacion']);

            }
        }
        /// dd($request->promociones);
        if (isset($request->promocion)) {





            //    "idPromocion": "0002",
            //    "cantidadMeses": 1

            $array = $request->promocion;

            DB::table('promociones')
            ->where('cedulatitular', $request->cedula)
            ->delete();

          ///  dd($array);
            foreach ($array as $key => $value) {
           ///   dd($value['idPromocion']);
                $promo = promociones::where('idPromocion', '=', $value['idPromocion'])->first();
                //  $tamaño = $cedula->count();

                  
                ///  echo($nombremas);
                if ($promo) {

                    $promo->idpromocion = $value['idPromocion'] ?? "";
                    $promo->cantidadmeses = $value['cantidadMeses'] ?? "";

                    $promo->cedulatitular = $request->cedula;

                    $promo->save();

                } else {



                    ///echo "Elemento ".$key.":<br>";
                    // foreach($value as $clave => $valor) {
                    // dd($value['idPromocion']);
                    $promos = new promociones;
                    $promos->idpromocion = $value['idPromocion'] ?? "";
                    $promos->cantidadmeses = $value['cantidadMeses'] ?? "";

                    $promos->cedulatitular = $request->cedula;

                    $promos->save();

                }



                /// dd($request->beneficiarios[0]['tipoIdentificacion']);

            }



            /// dd($request->beneficiarios[0]['tipoIdentificacion']);

        }

        //dd( $request->beneficiarios->tipoIdentificacion);



        return Response::json(['status' => 1, 'guardado con existo'], 403);

    }




    public function Save1Afiliado(Request $request)
    {
        //dd($request->codreferidocliente==null ? " " : $request->codreferidocliente);

        ///    $cedula = afiliado::where('cedula', '=', $request->cedula);

        /*
        $cedula = DB::table('afiliados')
        ->select('*')
          ->where('cedula', '=', $request->cedula)
          ->get();*/
        // busca el usuario con el ID especificado

        ///dd($request->direccion);
        $cedula = afiliado::where('cedula', '=', $request->cedula)->first();
        //  $tamaño = $cedula->count();
        if ($cedula) {
            ///$cedula->cedula = "207560693";



            ////////////////
            ////$afiliado = new afiliado;
            $cedula->rebajodias = $request->rebajoDias ?? "";
            $cedula->codreferidocliente = $request->codreferidocliente == null ? " " : $request->codreferidocliente;
            $cedula->frecuenciapago = $request->frecuenciaPago ?? "";
            $cedula->tipoidentificacion = $request->tipoIdentificacion ?? "";
            $cedula->profesion = $request->profesion ?? "";
            $cedula->nacionalidad = $request->nacionalidad ?? "";

            $cedula->telefono1 = $request->telefono1 ?? "";
            $cedula->codigotel1 = $request->codigotel1 ?? "";
            $cedula->telefono2 = $request->telefono2 ?? "";
            $cedula->codigotel2 = $request->codigotel2 ?? "";




            $cedula->nombrecompleto = $request->nombreCompleto ?? "";
            $cedula->fechanacimiento = $request->fechaNacimiento ?? "";
            $cedula->telefonocelular = $request->telefonoCelular ?? "";
            $cedula->correo = $request->correo ?? "";
            $cedula->direccion = $request->direccion ?? "";
            $cedula->provincia = $request->provincia ?? "";
            $cedula->canton = $request->canton ?? "";
            $cedula->distrito = $request->distrito ?? "";
            $cedula->estadocivil = $request->estadoCivil ?? "";
            $cedula->genero = $request->genero ?? "";
            $cedula->coberturaOncosmart = $request->coberturaOncosmart == true ? 1 : 0;

            $cedula->save();



            ////////////////////
            ///   dd(  $cedula);
        } else {

            $afiliado = new afiliado;
            $afiliado->rebajodias = $request->rebajoDias ?? "";
            $afiliado->codreferidocliente = $request->codreferidocliente == null ? " " : $request->codreferidocliente;
            $afiliado->frecuenciapago = $request->frecuenciaPago ?? "";
            $afiliado->tipoidentificacion = $request->tipoIdentificacion ?? "";
            $afiliado->cedula = $request->cedula ?? "";
            $afiliado->profesion = $request->profesion ?? "";
            $afiliado->nacionalidad = $request->nacionalidad ?? "";

            $afiliado->telefono1 = $request->telefono1 ?? "";
            $afiliado->codigotel1 = $request->codigotel1 ?? "";
            $afiliado->telefono2 = $request->telefono2 ?? "";
            $afiliado->codigotel2 = $request->codigotel2 ?? "";


            $afiliado->nombrecompleto = $request->nombreCompleto ?? "";
            $afiliado->fechanacimiento = $request->fechaNacimiento ?? "";
            $afiliado->telefonocelular = $request->telefonoCelular ?? "";
            $afiliado->correo = $request->correo ?? "";
            $afiliado->direccion = $request->direccion ?? "";
            $afiliado->provincia = $request->provincia ?? "";
            $afiliado->canton = $request->canton ?? "";
            $afiliado->distrito = $request->distrito ?? "";
            $afiliado->estadocivil = $request->estadoCivil ?? "";
            $afiliado->genero = $request->genero ?? "";
            $afiliado->coberturaOncosmart = $request->coberturaOncosmart == true ? 1 : 0;

            $afiliado->save();
        }

        if (isset($request->beneficiarios)) {

            $array = $request->beneficiarios;


            foreach ($array as $key => $value) {
                $cedulabn = beneficiario::where('cedula', '=', $value['cedula'])->first();
                //  $tamaño = $cedula->count();
                // dd($cedulabn);
                //    dd($cedulabn);
                if ($cedulabn) {

                    $cedulabn->cedulatitular = $request->cedula;
                    //    $bene->tipoidentificacion = $value['tipoIdentificacion'];
                    $cedulabn->tipoidentificacion = $value['tipoIdentificacion'] ?? "";
                    $cedulabn->cedula = $value['cedula'] ?? "";
                    $cedulabn->nombrecompleto = $value['nombreCompleto'] ?? "";
                    $cedulabn->fechanacimiento = $value['fechaNacimiento'] ?? "";
                    $cedulabn->telefonocelular = $value['telefonoCelular'] ?? "";
                    $cedulabn->correo = $value['correo'] ?? "";

                    $cedulabn->parentesco = $value['parentesco'] ?? "";
                    $cedulabn->telefono1 = $value['telefono1'] ?? "";
                    $cedulabn->codigotel1 = $value['codigotel1'] ?? "";
                    $cedulabn->telefono2 = $value['telefono2'] ?? "";
                    $cedulabn->codigotel2 = $value['codigotel2'] ?? "";

                    $cedulabn->provincia = $value['provincia'] ?? "";
                    $cedulabn->canton = $value['canton'] ?? "";
                    $cedulabn->distrito = $value['distrito'] ?? "";
                    $cedulabn->estadocivil = $value['estadoCivil'] ?? "";
                    $cedulabn->genero = $value['genero'] ?? "";
                    $cedulabn->coberturaOncosmart = $value['coberturaOncosmart'] == true ? 1 : 0;
                    $cedulabn->save();

                } else {



                    ///echo "Elemento ".$key.":<br>";
                    // foreach($value as $clave => $valor) {

                    $bene = new beneficiario;
                    $bene->cedulatitular = $request->cedula;
                    //    $bene->tipoidentificacion = $value['tipoIdentificacion'];
                    $bene->tipoidentificacion = $value['tipoIdentificacion'] ?? "";
                    $bene->cedula = $value['cedula'] ?? "";
                    $bene->nombrecompleto = $value['nombreCompleto'] ?? "";
                    $bene->fechanacimiento = $value['fechaNacimiento'] ?? "";
                    $bene->telefonocelular = $value['telefonoCelular'] ?? "";

                    $bene->parentesco = $value['parentesco'] ?? "";
                    $bene->telefono1 = $value['telefono1'] ?? "";
                    $bene->codigotel1 = $value['codigotel1'] ?? "";
                    $bene->telefono2 = $value['telefono2'] ?? "";
                    $bene->codigotel2 = $value['codigotel2'] ?? "";

                    $bene->correo = $value['correo'] ?? "";
                    $bene->provincia = $value['provincia'] ?? "";
                    $bene->canton = $value['canton'] ?? "";
                    $bene->distrito = $value['distrito'] ?? "";
                    $bene->estadocivil = $value['estadoCivil'] ?? "";
                    $bene->genero = $value['genero'] ?? "";
                    $bene->coberturaOncosmart = $value['coberturaOncosmart'] == true ? 1 : 0;
                    $bene->save();
                }



                //    }
            }







        }
        if (isset($request->mascotas)) {

            $array = $request->mascotas;
            // dd($array);
            foreach ($array as $key => $value) {
                $nombremas = mascota::where('nombremascota', '=', $value['nombreMascota'])->first();
                //  $tamaño = $cedula->count();

                ///  dd($nombremas);
                ///  echo($nombremas);
                if ($nombremas) {


                    $nombremas->especie = $value['especie'] ?? "";
                    $nombremas->nombremascota = $value['nombreMascota'] ?? "";
                    $nombremas->raza = $value['raza'] ?? "";
                    $nombremas->genero = $value['genero'] ?? "";
                    $nombremas->edad = $value['edad'] ?? "";
                    $nombremas->color = $value['color'] ?? "";

                    $nombremas->cedulatitular = $request->cedula;




                    //$cedulabn->coberturaOncosmart = $value['coberturaOncosmart'] == true ? 1 : 0 ;
                    $nombremas->save();

                } else {



                    ///echo "Elemento ".$key.":<br>";
                    // foreach($value as $clave => $valor) {

                    $masc = new mascota;
                    $masc->especie = $value['especie'] ?? "";
                    $masc->nombremascota = $value['nombreMascota'] ?? "";
                    $masc->raza = $value['raza'] ?? "";
                    $masc->genero = $value['genero'] ?? "";
                    $masc->edad = $value['edad'] ?? "";
                    $masc->color = $value['color'] ?? "";
                    $masc->cedulatitular = $request->cedula;
                    $masc->save();


                }



                /// dd($request->beneficiarios[0]['tipoIdentificacion']);

            }
        }
        /// dd($request->promociones);
        if (isset($request->promociones)) {





            //    "idPromocion": "0002",
            //    "cantidadMeses": 1

            $array = $request->promociones;


            ///dd($array);
            foreach ($array as $key => $value) {
                $promo = promociones::where('idPromocion', '=', $value['idPromocion'])->first();
                //  $tamaño = $cedula->count();

                //   dd($promo);
                ///  echo($nombremas);
                if ($promo) {

                    $promo->idpromocion = $value['idPromocion'] ?? "";
                    $promo->cantidadmeses = $value['cantidadMeses'] ?? "";

                    $promo->cedulatitular = $request->cedula;

                    $promo->save();

                } else {



                    ///echo "Elemento ".$key.":<br>";
                    // foreach($value as $clave => $valor) {
                    // dd($value['idPromocion']);
                    $promos = new promociones;
                    $promos->idpromocion = $value['idPromocion'] ?? "";
                    $promos->cantidadmeses = $value['cantidadMeses'] ?? "";

                    $promos->cedulatitular = $request->cedula;

                    $promos->save();

                }



                /// dd($request->beneficiarios[0]['tipoIdentificacion']);

            }



            /// dd($request->beneficiarios[0]['tipoIdentificacion']);

        }

        //dd( $request->beneficiarios->tipoIdentificacion);



        return Response::json(['status' => 1, 'guardado con existo'], 403);

    }









    
    public function SaveAfilaaaaaiado(Request $request)
    {
        //dd($request->codreferidocliente==null ? " " : $request->codreferidocliente);

    ///    $cedula = afiliado::where('cedula', '=', $request->cedula);

        /*
        $cedula = DB::table('afiliados')
        ->select('*')
          ->where('cedula', '=', $request->cedula)
          ->get();*/
        // busca el usuario con el ID especificado
        $cedula = afiliado::where('cedula', '=', $request->cedula)->first();
      //  $tamaño = $cedula->count();
        if($cedula){
            ///$cedula->cedula = "207560693";
            


            ////////////////
            ////$afiliado = new afiliado;
            $cedula->rebajodias = $request->rebajoDias;
            $cedula->codreferidocliente = $request->codreferidocliente==null ? " " : $request->codreferidocliente;
            $cedula->frecuenciapago = $request->frecuenciaPago;
            $cedula->tipoidentificacion = $request->tipoIdentificacion;
            $cedula->profesion = $request->profesion;
            $cedula->nacionalidad = $request->nacionalidad;
            
            $cedula->telefono1 = $request->telefono1;
            $cedula->codigotel1 = $request->codigotel1;
            $cedula->telefono2 = $request->telefono2;
            $cedula->codigotel2 = $request->codigotel2;
            



            $cedula->nombrecompleto = $request->nombreCompleto;
            $cedula->fechanacimiento = $request->fechaNacimiento;
            $cedula->telefonocelular = $request->telefonoCelular;
            $cedula->correo = $request->correo;
            $cedula->direccion = $request->direccion;
            $cedula->provincia = $request->provincia;
            $cedula->canton = $request->canton;
            $cedula->distrito = $request->distrito;
            $cedula->estadocivil =$request->estadoCivil;
            $cedula->genero = $request->genero;
            $cedula->coberturaOncosmart = $request->coberturaOncosmart == true ? 1 : 0 ;

            $cedula->save();



            ////////////////////
         ///   dd(  $cedula);
        }else{

$afiliado = new afiliado;
$afiliado->rebajodias = $request->rebajoDias;
$afiliado->codreferidocliente = $request->codreferidocliente==null ? " " : $request->codreferidocliente;
$afiliado->frecuenciapago = $request->frecuenciaPago;
$afiliado->tipoidentificacion = $request->tipoIdentificacion;
$afiliado->cedula = $request->cedula;
$afiliado->profesion = $request->profesion;
$afiliado->nacionalidad = $request->nacionalidad;

$afiliado->telefono1 = $request->telefono1;
$afiliado->codigotel1 = $request->codigotel1;
$afiliado->telefono2 = $request->telefono2;
$afiliado->codigotel2 = $request->codigotel2;


$afiliado->nombrecompleto = $request->nombreCompleto;
$afiliado->fechanacimiento = $request->fechaNacimiento;
$afiliado->telefonocelular = $request->telefonoCelular;
$afiliado->correo = $request->correo;
$afiliado->direccion = $request->direccion;
$afiliado->provincia = $request->provincia;
$afiliado->canton = $request->canton;
$afiliado->distrito = $request->distrito;
$afiliado->estadocivil =$request->estadoCivil;
$afiliado->genero = $request->genero;
$afiliado->coberturaOncosmart = $request->coberturaOncosmart == true ? 1 : 0 ;

$afiliado->save();
        }

        if(isset($request->beneficiarios)){

            $array=$request->beneficiarios;


            foreach($array as $key => $value) {
                $cedulabn = beneficiario::where('cedula', '=', $value['cedula'])->first();
                //  $tamaño = $cedula->count();
               // dd($cedulabn);
           //    dd($cedulabn);
                  if($cedulabn){
                    
                    $cedulabn->cedulatitular = $request->cedula;
                    //    $bene->tipoidentificacion = $value['tipoIdentificacion'];
                        $cedulabn->tipoidentificacion = $value['tipoIdentificacion'];
                        $cedulabn->cedula =$value['cedula'];
                        $cedulabn->nombrecompleto = $value['nombreCompleto'];
                        $cedulabn->fechanacimiento = $value['fechaNacimiento'];
                        $cedulabn->telefonocelular = $value['telefonoCelular'];
                        $cedulabn->correo = $value['correo'];


                        $cedulabn->parentesco = $value['parentesco'];
                        $cedulabn->telefono1 = $value['telefono1'];
                        $cedulabn->codigotel1 = $value['codigotel1'];
                        $cedulabn->telefono2 = $value['telefono2'];
                        $cedulabn->codigotel2 = $value['codigotel2'];

                        $cedulabn->provincia = $value['provincia'];
                        $cedulabn->canton = $value['canton'];
                        $cedulabn->distrito = $value['distrito'];
                        $cedulabn->estadocivil =$value['estadoCivil'];
                        $cedulabn->genero = $value['genero'];
                        $cedulabn->coberturaOncosmart = $value['coberturaOncosmart'] == true ? 1 : 0 ;
                      $cedulabn->save();

                  }else{



                ///echo "Elemento ".$key.":<br>";
               // foreach($value as $clave => $valor) {

                $bene = new beneficiario;
                $bene->cedulatitular = $request->cedula;
            //    $bene->tipoidentificacion = $value['tipoIdentificacion'];
                $bene->tipoidentificacion = $value['tipoIdentificacion'];
                $bene->cedula =$value['cedula'];
                $bene->nombrecompleto = $value['nombreCompleto'];
                $bene->fechanacimiento = $value['fechaNacimiento'];
                $bene->telefonocelular = $value['telefonoCelular'];
                
                $bene->parentesco = $value['parentesco'];
                $bene->telefono1 = $value['telefono1'];
                $bene->codigotel1 = $value['codigotel1'];
                $bene->telefono2 = $value['telefono2'];
                $bene->codigotel2 = $value['codigotel2'];


                $bene->correo = $value['correo'];
                $bene->provincia = $value['provincia'];
                $bene->canton = $value['canton'];
                $bene->distrito = $value['distrito'];
                $bene->estadocivil =$value['estadoCivil'];
                $bene->genero = $value['genero'];
                $bene->coberturaOncosmart = $value['coberturaOncosmart'] == true ? 1 : 0 ;
                $bene->save();
               }



            //    }
             }







        }
        if(isset($request->mascotas)){

            $array=$request->mascotas;
           // dd($array);
            foreach($array as $key => $value) {
                $nombremas = mascota::where('nombremascota', '=', $value['nombreMascota'])->first();
                //  $tamaño = $cedula->count();
                
             ///  dd($nombremas);
           ///  echo($nombremas);
                  if($nombremas){
                  

                    $nombremas->especie =  $value['especie'];
                    $nombremas->nombremascota = $value['nombreMascota'];
                    $nombremas->raza = $value['raza'];
                    $nombremas->genero = $value['genero'];
                    $nombremas->edad = $value['edad'];
                    $nombremas->color = $value['color'];
                    $nombremas->cedulatitular = $request->cedula;



                  
//$cedulabn->coberturaOncosmart = $value['coberturaOncosmart'] == true ? 1 : 0 ;
                      $nombremas->save();

                  }else{



                ///echo "Elemento ".$key.":<br>";
               // foreach($value as $clave => $valor) {
           
                $masc = new mascota;
                $masc->especie =  $value['especie'];
                $masc->nombremascota = $value['nombreMascota'];
                $masc->raza = $value['raza'];
                $masc->genero = $value['genero'];
                $masc->edad = $value['edad'];
                $masc->color = $value['color'];
                $masc->cedulatitular = $request->cedula;
                $masc->save();

             
               }



           /// dd($request->beneficiarios[0]['tipoIdentificacion']);

        }
    }
   /// dd($request->promociones);
        if(isset($request->promociones)){





       //    "idPromocion": "0002",
     //    "cantidadMeses": 1

     $array=$request->promociones;
 

     ///dd($array);
      foreach($array as $key => $value) {
          $promo = promociones::where('idPromocion', '=', $value['idPromocion'])->first();
          //  $tamaño = $cedula->count();
          
      //   dd($promo);
     ///  echo($nombremas);
            if($promo){
            

              $promo->idpromocion =  $value['idPromocion'];
              $promo->cantidadmeses = $value['cantidadMeses'];
              $promo->cedulatitular = $request->cedula;

            $promo->save();

            }else{



          ///echo "Elemento ".$key.":<br>";
         // foreach($value as $clave => $valor) {
    // dd($value['idPromocion']);
          $promos = new promociones;
          $promos->idpromocion =  $value['idPromocion'];
          $promos->cantidadmeses = $value['cantidadMeses'];
          $promos->cedulatitular = $request->cedula;

          $promos->save();

         }



     /// dd($request->beneficiarios[0]['tipoIdentificacion']);

  }



           /// dd($request->beneficiarios[0]['tipoIdentificacion']);

        }
     
//dd( $request->beneficiarios->tipoIdentificacion);



   return Response::json(['status'=>1 ,'guardado con existo'], 403);

    }





    public function SaaveAfiliado(Request $request)
    {
        //dd($request->codreferidocliente==null ? " " : $request->codreferidocliente);

    ///    $cedula = afiliado::where('cedula', '=', $request->cedula);

        /*
        $cedula = DB::table('afiliados')
        ->select('*')
          ->where('cedula', '=', $request->cedula)
          ->get();*/
        // busca el usuario con el ID especificado
        $cedula = afiliado::where('cedula', '=', $request->cedula)->first();
      //  $tamaño = $cedula->count();
        if($cedula){
            ///$cedula->cedula = "207560693";
            


            ////////////////
            ////$afiliado = new afiliado;
            $cedula->rebajodias = $request->rebajoDias;
            $cedula->codreferidocliente = $request->codreferidocliente==null ? " " : $request->codreferidocliente;
            $cedula->frecuenciapago = $request->frecuenciaPago;
            $cedula->tipoidentificacion = $request->tipoIdentificacion;
           /// $cedula->cedula = $request->cedula;$afiliado->profesion = $request->profesion;

            $cedula->profesion = $request->profesion;

            $cedula->nombrecompleto = $request->nombreCompleto;
            $cedula->fechanacimiento = $request->fechaNacimiento;
            $cedula->telefonocelular = $request->telefonoCelular;
            $cedula->correo = $request->correo;
            $cedula->direccion = $request->direccion;
            $cedula->provincia = $request->provincia;
            $cedula->canton = $request->canton;
            $cedula->distrito = $request->distrito;
            $cedula->estadocivil =$request->estadoCivil;
            $cedula->genero = $request->genero;
            $cedula->coberturaOncosmart = $request->coberturaOncosmart == true ? 1 : 0 ;

            $cedula->save();



            ////////////////////
           //& dd(  $cedula);
        }else{

$afiliado = new afiliado;
$afiliado->rebajodias = $request->rebajoDias;
$afiliado->codreferidocliente = $request->codreferidocliente==null ? " " : $request->codreferidocliente;
$afiliado->frecuenciapago = $request->frecuenciaPago;
$afiliado->tipoidentificacion = $request->tipoIdentificacion;
$afiliado->cedula = $request->cedula;
$afiliado->profesion = $request->profesion;
$afiliado->nombrecompleto = $request->nombreCompleto;
$afiliado->fechanacimiento = $request->fechaNacimiento;
$afiliado->telefonocelular = $request->telefonoCelular;
$afiliado->correo = $request->correo;
$afiliado->direccion = $request->direccion;
$afiliado->provincia = $request->provincia;
$afiliado->canton = $request->canton;
$afiliado->distrito = $request->distrito;
$afiliado->estadocivil =$request->estadoCivil;
$afiliado->genero = $request->genero;
$afiliado->coberturaOncosmart = $request->coberturaOncosmart == true ? 1 : 0 ;

$afiliado->save();
        }

        if(isset($request->beneficiarios)){

            $array=$request->beneficiarios;


            foreach($array as $key => $value) {
                $cedulabn = beneficiario::where('cedula', '=', $value['cedula'])->first();
                //  $tamaño = $cedula->count();
               // dd($cedulabn);
           //    dd($cedulabn);
                  if($cedulabn){
                    
                    $cedulabn->cedulatitular = $request->cedula;
                    //    $bene->tipoidentificacion = $value['tipoIdentificacion'];
                        $cedulabn->tipoidentificacion = $value['tipoIdentificacion'];
                        $cedulabn->cedula =$value['cedula'];
                        $cedulabn->nombrecompleto = $value['nombreCompleto'];
                        $cedulabn->fechanacimiento = $value['fechaNacimiento'];
                        $cedulabn->telefonocelular = $value['telefonoCelular'];
                        $cedulabn->correo = $value['correo'];
                        $cedulabn->provincia = $value['provincia'];
                        $cedulabn->canton = $value['canton'];
                        $cedulabn->distrito = $value['distrito'];
                        $cedulabn->estadocivil =$value['estadoCivil'];
                        $cedulabn->genero = $value['genero'];
                        $cedulabn->coberturaOncosmart = $value['coberturaOncosmart'] == true ? 1 : 0 ;
                      $cedulabn->save();

                  }else{



                ///echo "Elemento ".$key.":<br>";
               // foreach($value as $clave => $valor) {

                $bene = new beneficiario;
                $bene->cedulatitular = $request->cedula;
            //    $bene->tipoidentificacion = $value['tipoIdentificacion'];
                $bene->tipoidentificacion = $value['tipoIdentificacion'];
                $bene->cedula =$value['cedula'];
                $bene->nombrecompleto = $value['nombreCompleto'];
                $bene->fechanacimiento = $value['fechaNacimiento'];
                $bene->telefonocelular = $value['telefonoCelular'];
                $bene->correo = $value['correo'];
                $bene->provincia = $value['provincia'];
                $bene->canton = $value['canton'];
                $bene->distrito = $value['distrito'];
                $bene->estadocivil =$value['estadoCivil'];
                $bene->genero = $value['genero'];
                $bene->coberturaOncosmart = $value['coberturaOncosmart'] == true ? 1 : 0 ;
                $bene->save();
               }



            //    }
             }







        }
        if(isset($request->mascotas)){

            $array=$request->mascotas;
           // dd($array);
            foreach($array as $key => $value) {
                $nombremas = mascota::where('nombremascota', '=', $value['nombreMascota'])->first();
                //  $tamaño = $cedula->count();
                
             ///  dd($nombremas);
           ///  echo($nombremas);
                  if($nombremas){
                  

                    $nombremas->especie =  $value['especie'];
                    $nombremas->nombremascota = $value['nombreMascota'];
                    $nombremas->raza = $value['raza'];
                    $nombremas->genero = $value['genero'];
                    $nombremas->edad = $value['edad'];
                    $nombremas->color = $value['color'];
                    $nombremas->cedulatitular = $request->cedula;



                  
//$cedulabn->coberturaOncosmart = $value['coberturaOncosmart'] == true ? 1 : 0 ;
                      $nombremas->save();

                  }else{



                ///echo "Elemento ".$key.":<br>";
               // foreach($value as $clave => $valor) {
           
                $masc = new mascota;
                $masc->especie =  $value['especie'];
                $masc->nombremascota = $value['nombreMascota'];
                $masc->raza = $value['raza'];
                $masc->genero = $value['genero'];
                $masc->edad = $value['edad'];
                $masc->color = $value['color'];
                $masc->cedulatitular = $request->cedula;
                $masc->save();

             
               }



           /// dd($request->beneficiarios[0]['tipoIdentificacion']);

        }
    }
   /// dd($request->promociones);
        if(isset($request->promociones)){





       //    "idPromocion": "0002",
     //    "cantidadMeses": 1

     $array=$request->promociones;
 

     ///dd($array);
      foreach($array as $key => $value) {
          $promo = promociones::where('idPromocion', '=', $value['idPromocion'])->first();
          //  $tamaño = $cedula->count();
          
      //   dd($promo);
     ///  echo($nombremas);
            if($promo){
            

              $promo->idpromocion =  $value['idPromocion'];
              $promo->cantidadmeses = $value['cantidadMeses'];
              $promo->cedulatitular = $request->cedula;

            $promo->save();

            }else{



          ///echo "Elemento ".$key.":<br>";
         // foreach($value as $clave => $valor) {
    // dd($value['idPromocion']);
          $promos = new promociones;
          $promos->idpromocion =  $value['idPromocion'];
          $promos->cantidadmeses = $value['cantidadMeses'];
          $promos->cedulatitular = $request->cedula;

          $promos->save();

         }



     /// dd($request->beneficiarios[0]['tipoIdentificacion']);

  }



           /// dd($request->beneficiarios[0]['tipoIdentificacion']);

        }
     
//dd( $request->beneficiarios->tipoIdentificacion);



   return Response::json(['status'=>1 ,'guardado con existo'], 403);

    }
    public function consultainterno( $cedula)
    {

      //  dd($cedula);
      //  $datos = array('clave' => 'valor');
    //    return response()->json($datos);



      $afiliado = DB::table('afiliados')

      ->select('*')
        ->where('cedula', '=', $cedula)
      ->get();


if($afiliado->count() == 1){


//      beneficiarios
      $beneficiarios = DB::table('beneficiarios')

      ->select('*')
        ->where('cedulatitular', '=', $cedula)
      ->get();
      $array = json_decode($beneficiarios, true);

      $namedArray = [ 'beneficiarios' => $array ];
      $beneficiarios = json_encode($namedArray);


      $mascotas = DB::table('mascotas')

      

      ->select('*')
        ->where('cedulatitular', '=', $cedula)
      ->get();

      $array = json_decode($mascotas, true);

      $namedArray = [ 'mascotas' => $array ];
      $mascotas = json_encode($namedArray);

////////////////////////////////

      $promociones = DB::table('promociones')

      ->select('*')
        ->where('cedulatitular', '=', $cedula)
      ->get();

      $array = json_decode($promociones, true);

      $namedArray = [ 'promociones' => $array ];
      $promociones = json_encode($namedArray);

 $satatus = [ 'status' => 1 ];
      $satatus = json_encode($satatus);





 $afiliado = [ 'propietario' => $afiliado[0] ];
      $afiliado = json_encode($afiliado);



      $object1 = json_decode($afiliado, true);
      $object2 = json_decode($beneficiarios, true);
      $object3 = json_decode($mascotas, true);
      $object4 = json_decode($promociones, true);
       $object5 = json_decode($satatus, true);

     




      $mergedObject = Collection::make($object1)
          ->merge($object2)
          ->merge($object3)
          ->merge($object4)
->merge($object5 );
      
       $mergedJson = json_encode($mergedObject,true);
      
     /// dd($mergedJson);






       $mergedJson = json_decode($mergedJson, true);


      //mascotas
    //  promociones


        return $mergedJson;
}else{

   return Response::json(['status'=>0 ,'no existe'], 403);
}
    }









public function link(Request $request){
//7    dd($request->cli);
    $object = array(
        "email"   => $request->email,
        "cli"     => $request->cli,
        "afiliado" => true,
    );



    $string = json_encode($object);


    $link =('https://medismart.net/control/afiliado-crear-contrasena/'.encrypt($string));

return $link;
    
}











    public function userStatus($cedula) {
        //VALIDAR 
    





        $res = $this->getData($cedula);

        $res = json_decode($res);
        $res = json_decode($res, true);



return $res;
       dd($res);
        if ($res['existe']) {

          //  dd($res['petResults'][0]['estado']);
            if (count($res['accountResults']) > 0){
                $status=$res['accountResults'][0]['estado'];
    
                return ['status'=> $status, 'existe'=> true, 'afiliado'=> true];
            }else if (count($res['benResults']) > 0){
                $status=$res['benResults'][0]['estado'];
         
           return ['status'=> $status, 'existe'=> true, 'afiliado'=> false];
            }
           





        }else{
            return ['no existe','status'=>0, 'existe'=>false];
        }
    }



    public function getData($data)
    {
        


        $url = "https://medismart--test.sandbox.my.salesforce.com/services/apexrest/sfconsultapi/getdata?search=".$data;

       // dd($url);
        $ch = curl_init($url);
        
        //configuracion de la conexion
        $ch = $this->setConfiguration($ch, 'GET');

        //ejecutar
        $responseSF = curl_exec($ch);
       // dd($responseSF);
        //cerrar conexion
        curl_close($ch);
        if ($responseSF === false) {
            return null;
        }
      ///  
     /// dd($responseSF);

        return $responseSF;
    }




    public function checkapi()
    {
        



	$apiSF= DB::table('apisf')->where('tipo', '=', 'dev')->first();
        $id=$apiSF->id;

        $hoy = date("Ymdhis");
        $expire = $hoy +1000000;
        $client_id=$apiSF->client_id;
        $client_secret=$apiSF->client_secret;
        $user= $apiSF->username;
        $pass=$apiSF->password;
        $date= $apiSF->date;

      // dd( $hoy);
        if (true) {




            $baseUrl = env('API_ENDPOINTTST');
            $url = "https://medismart--test.my.salesforce.com/services/oauth2/token?grant_type=password&client_id=".$client_id."&client_secret=".$client_secret."&username=".$user."&password=".$pass;
            $response = Http::post($url);
            $json = $response;
            
            $res = json_decode($json);
    
    

      ///  dd($res);

           /// $res=json_decode($response);

        
           
            



		  
            DB::table('apisf')->where('id', '=', $id)->update(['accessToken' => $res->access_token]);
            DB::table('apisf')->where('id', '=', $id)->update(['signature' => $res->signature]);
            DB::table('apisf')->where('id', '=', $id)->update(['date' => $expire]);



         
        }
    }

    public function setConfiguration($ch, $method, $fields = null)
    {
        $this->checkapi();

         $apiSF= DB::table('apisf')->where('tipo', '=', 'dev')->first();
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Authorization: Bearer '. $apiSF->accessToken ,
            )
        );

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        if ($fields != null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10000);

        return $ch;

        
    }

   
    
    function buscarCedula(Request $request,$Cedula){

        $baseUrl = env('API_ENDPOINTTST');
        $url = $baseUrl ."/api/persona/buscarCedula.php?user=sfconsult&password=8Rh8hcRFMyGmqimA&buscarCedula=". $Cedula;
        $response = Http::get($url);

   $json = $response;
        
        $response = json_decode($json);







        return $response;
    }




public function primeraventa($data)
{
    $url = "https://medismart--test.sandbox.my.salesforce.com/services/apexrest/primeraventacliapi";
    $ch = curl_init($url);
    
    //configuracion de la conexion
    $ch = $this->setConfiguration($ch, 'POST', $data);

    //ejecutar
    $responseSF = curl_exec($ch);

    //cerrar conexion
    curl_close($ch);
    if ($responseSF === false) {
        return null;
    }

    return $responseSF;
}


public function ahorro($Titular,$Beneficiario,$Mascota,$Oncosmart,$Oncosmartbene,$monto, $tipo)
    {   
       
       
        $plan = DB::table('planes')
      ->select('*')
        ->where('codigoSalesforce', '=', 3)
      ->get();
      $add = DB::table('planesadd')
      ->select('precio')
       ->where('estado', '=', 1)
      ->get();
//dd($add );

      $plan = json_decode($plan, true);
      $add = json_decode($add, true);


     // dd($plan[0]['montoTitular']);
      $montoTitular = $plan[0]['montoTitular'];
      $montoBeneficiario = $plan[0]['montoBeneficiario'];
      $montoMascota = $plan[0]['montoMascota'];
      $Oncosmartc=$add[0]['precio'];
    //$Titular,$Beneficiario,$Mascota,$Oncosmart,$Oncosmartbene,

      $precioadd=$add[0]['precio'];
      $costomesual=$montoTitular;
      if($Beneficiario==1){
        $costomesual=  $costomesual+$montoBeneficiario;
      }
      if($Mascota==1){
        $costomesual=  $costomesual+$montoMascota;
      }if($Oncosmart==1){
        $costomesual=  $costomesual+$Oncosmartc;
      }if($Oncosmartbene==1){
        $costomesual=  $costomesual+$Oncosmartc;
      }



$costototal=$costomesual;
      

      if($tipo=='T'){
        $costototal=$costomesual*3;
      }if($tipo=='s'){
        $costototal=$costomesual*6;
      }if($tipo=='A'){
        $costototal=$costomesual*12;

      }


              return response()->json(['ahorro' =>  ($monto-$costototal) ], 200);

       
       
       /// return "bien";



    //    $Titular,$Beneficiario,$Mascota,$Oncosmart,$Oncosmartbene,$monto, $tipo
/*
Titular
Beneficiario
Mascota
Oncosmart
Oncosmartbene
*/

    }





public function  CalculoProrrateo(Request $request){


    $mes = 4; // mes de abril
    $año = date('Y'); // año actual
    
    // Obtener la fecha actual
    $hoy = date('Y-m-d');
    
    // Calcular la fecha correspondiente al día 30 del mes deseado
    $fecha_objetivo = date("$año-$mes-".$request["frecuencia"]);
    
    $hoy = date('Y-m-d');
    
    // Calcular la diferencia en segundos entre las dos fechas
    $diferencia_segundos = strtotime($fecha_objetivo) - strtotime($hoy);
    
    // Calcular la diferencia en días
    $diferencia_dias = $diferencia_segundos / 60 / 60 / 24;
    
 
    if($request["Tipo"]=="M"){

        $monto=($request["monto"]/31);

        $total=(31+ $diferencia_dias)*$monto;
    }
    if($request["Tipo"]=="T"){

        $monto=($request["monto"]/91);
        $total=(91+ $diferencia_dias)*$monto;
    }
    if($request["Tipo"]=="S"){

        $monto=($request["monto"]/183);
        $total=(183+ $diferencia_dias)*$monto;
    }
    if($request["Tipo"]=="A"){
        $monto=($request["monto"]/365);

        $total=(365+ $diferencia_dias)*$monto;

    }
    return Response::json(['total' => $total ]);

}








public function getPromotion(Request $request = null, $promocion = '', $frecPago = ''){
       
        $promoSF = array();
        $descuento = DB::table('descuentos')
        ->select('*')
          ->where('promo', '=', $promocion )
        ->get();
        if($descuento->count() == 0){
            array_push($promoSF, array("exist"=> 0, "valid" => 1,  "msg"=> "No existe el código ingresado", "descuento"=> 10, "idPromocion"=> "ALL-DESC-002" , "cantidadMeses"=> 1));
            return $promoSF;

        }else{

          
            return $descuento;
        }
     


    }



public function get1Promotion(Request $request = null, $promocion = '', $frecPago = ''){
       
        $promoSF = array();

        if($promocion == '' && $frecPago == '' && $request != null){
            if ($request->has('promocion')){
                $promocion = $request->get('promocion');
                $frecPago = $request->get('frecPago');
            }
        }

        if(count($promoSF) == 0){
            switch (strtoupper($promocion)) {
                case "FEB20":
                    if(count($promoSF) == 0){
                        //array_push($promoSF,["exist"=> true, "valid"=>false, "descuento"=> 25,  "msg"=> "¡El código promocional ha vencido!", 'code' => '403', "idPromocion"=> "ALL-DESC-25OCT22" , "cantidadMeses"=> 1,'message' => '¡El código promocional ha vencido!'], 403);
                        array_push($promoSF, array("exist"=> true, "valid"=>true,  "msg"=> "", "descuento"=> 20, "idPromocion"=> "ALL-DESC-20FEB23" , "cantidadMeses"=> 1));
                       }   
                    break;
                case "WEB25":
                    if(count($promoSF) == 0){
                         //array_push($promoSF,["exist"=> true, "valid"=>false, "descuento"=> 25,  "msg"=> "¡El código promocional ha vencido!", 'code' => '403', "idPromocion"=> "ALL-DESC-25OCT22" , "cantidadMeses"=> 1,'message' => '¡El código promocional ha vencido!'], 403);
                         array_push($promoSF, array("exist"=> true, "valid"=>true,  "msg"=> "", "descuento"=> 25, "idPromocion"=> "ALL-DESC-WEB23" , "cantidadMeses"=> 1));
                        }   
                    break;
                case "ENE25":
                    if(count($promoSF) == 0){
                         array_push($promoSF,["exist"=> true, "valid"=>false, "descuento"=> 25,  "msg"=> "¡El código promocional ha vencido!", 'code' => '403', "idPromocion"=> "ALL-DESC-25OCT22" , "cantidadMeses"=> 1,'message' => '¡El código promocional ha vencido!'], 403);
                         //array_push($promoSF, array("exist"=> true, "valid"=>true,  "msg"=> "", "descuento"=> 25, "idPromocion"=> "ALL-DESC-25ENE23" , "cantidadMeses"=> 1));
                        }   
                    break;
                case "LIMON20":
                        if(count($promoSF) == 0){
                             //array_push($promoSF,["exist"=> true, "valid"=>false, "descuento"=> 25,  "msg"=> "¡El código promocional ha vencido!", 'code' => '403', "idPromocion"=> "ALL-DESC-25OCT22" , "cantidadMeses"=> 1,'message' => '¡El código promocional ha vencido!'], 403);
                             array_push($promoSF, array("exist"=> true, "valid"=>true,  "msg"=> "", "descuento"=> 20, "idPromocion"=> "ALL-DESC-20LIM" , "cantidadMeses"=> 1));
                            }   
                    break;
                case "DIC25":
                    if(count($promoSF) == 0){
                         array_push($promoSF,["exist"=> true, "valid"=>false, "descuento"=> 25,  "msg"=> "¡El código promocional ha vencido!", 'code' => '403', "idPromocion"=> "ALL-DESC-25OCT22" , "cantidadMeses"=> 1,'message' => '¡El código promocional ha vencido!'], 403);
                        //  array_push($promoSF, array("exist"=> true, "valid"=>true,  "msg"=> "", "descuento"=> 25, "idPromocion"=> "ALL-DESC-25DIC22" , "cantidadMeses"=> 1));
                        }   
                    break;
                case "SMART20":
                    if(count($promoSF) == 0){
                            //array_push($promoSF,["exist"=> true, "valid"=>false, "descuento"=> 25,  "msg"=> "¡El código promocional ha vencido!", 'code' => '403', "idPromocion"=> "ALL-DESC-25OCT22" , "cantidadMeses"=> 1,'message' => '¡El código promocional ha vencido!'], 403);
                            array_push($promoSF, array("exist"=> true, "valid"=>true,  "msg"=> "", "descuento"=> 20, "idPromocion"=> "ALL-DESC-20SMRT22" , "cantidadMeses"=> 1));
                    }   
                    break;
                case "NOV25":
                    if(count($promoSF) == 0){
                         //array_push($promoSF,["exist"=> true, "valid"=>false, "descuento"=> 25,  "msg"=> "¡El código promocional ha vencido!", 'code' => '403', "idPromocion"=> "ALL-DESC-25OCT22" , "cantidadMeses"=> 1,'message' => '¡El código promocional ha vencido!'], 403);
                         array_push($promoSF,["exist"=> true, "valid"=>false, "descuento"=> 25,  "msg"=> "¡El código promocional ha vencido!", 'code' => '403', "idPromocion"=> "NOV25" , "cantidadMeses"=> 1,'message' => '¡El código promocional ha vencido!'], 403);
                    }   
                    break;
                case "BLACK40":
                    if(count($promoSF) == 0){
                         array_push($promoSF,["exist"=> true, "valid"=>false, "descuento"=> 40,  "msg"=> "¡El código promocional ha vencido!", 'code' => '403', "idPromocion"=> "BLACK40" , "cantidadMeses"=> 1,'message' => '¡El código promocional ha vencido!'], 403);
                        // array_push($promoSF, array("exist"=> true, "valid"=>true,  "msg"=> "", "descuento"=> 40, "idPromocion"=> "BLACK40" , "cantidadMeses"=> 1));
                    }   
                    break;
                case "LIGA":
                        if(count($promoSF) == 0){
                             //array_push($promoSF,["exist"=> true, "valid"=>false, "descuento"=> 25,  "msg"=> "¡El código promocional ha vencido!", 'code' => '403', "idPromocion"=> "ALL-DESC-25OCT22" , "cantidadMeses"=> 1,'message' => '¡El código promocional ha vencido!'], 403);
                             array_push($promoSF, array("exist"=> true, "valid"=>true,  "msg"=> "", "descuento"=> 20, "idPromocion"=> "LIGA" , "cantidadMeses"=> 1));
                        }   
                    break;
                case "OCT25":
                    if(count($promoSF) == 0){
                         array_push($promoSF,["exist"=> true, "valid"=>false, "descuento"=> 25,  "msg"=> "¡El código promocional ha vencido!", 'code' => '403', "idPromocion"=> "ALL-DESC-25OCT22" , "cantidadMeses"=> 1,'message' => '¡El código promocional ha vencido!'], 403);
                         //array_push($promoSF, array("exist"=> true, "valid"=>true,  "msg"=> "", "descuento"=> 25, "idPromocion"=> "ALL-DESC-25OCT22" , "cantidadMeses"=> 1));
                    }   
                    break;
                case "SET25":
                    if(count($promoSF) == 0){
                         //array_push($promoSF, array("exist"=> true, "valid"=>true,  "msg"=> "", "descuento"=> 25, "idPromocion"=> "ALL-DESC-25SET22" , "cantidadMeses"=> 1));
                         array_push($promoSF,["exist"=> true, "valid"=>false, "descuento"=> 25,  "msg"=> "¡El código promocional ha vencido!", 'code' => '403', "idPromocion"=> "" , "cantidadMeses"=> 1,'message' => '¡El código promocional ha vencido!'], 403);
                    }   
                    break;
                case "SET20":
                    if(count($promoSF) == 0){
                         array_push($promoSF, array("exist"=> true, "valid"=>true,  "msg"=> "", "descuento"=> 20, "idPromocion"=> "ALL-DESC-20SET22" , "cantidadMeses"=> 1));
                    }   
                    break;
                case "AGOS25":
                    if(count($promoSF) == 0){
                        array_push($promoSF,["exist"=> true, "valid"=>false, "descuento"=> 25,  "msg"=> "¡El código promocional ha vencido!", 'code' => '403', "idPromocion"=> "ALL-DESC-25AGO22" , "cantidadMeses"=> 1,'message' => '¡El código promocional ha vencido!'], 403);
                        //array_push($promoSF, array("exist"=> true, "valid"=>true,  "msg"=> "", "descuento"=> 25, "idPromocion"=> "ALL-DESC-25AGO22" , "cantidadMeses"=> 1));
                    }
                break;
                case "JUL20":
                    if(count($promoSF) == 0){
                        array_push($promoSF, array("exist"=> true, "valid"=>true,  "msg"=> "", "descuento"=> 20, "idPromocion"=> "ALL-DESC-20JUL22" , "cantidadMeses"=> 1));
                    }
                break;
                case "JUN25":
                    if(count($promoSF) == 0){
                        array_push($promoSF,["exist"=> true, "valid"=>false, "descuento"=> 25,  "msg"=> "¡El código promocional ha vencido!", 'code' => '403', "idPromocion"=> "ALL-DESC-002" , "cantidadMeses"=> 1,'message' => '¡El código promocional ha vencido!'], 403);
                    }
                break;
                case "PROMOXK":
                    if(count($promoSF) == 0){
                        if ($frecPago == "PLAN SEMESTRAL"){
                            array_push($promoSF, array("exist"=> true, "valid"=>true,  "msg"=> "", "descuento"=> 90, "idPromocion"=> "ALL-DESC-PRXK" , "cantidadMeses"=> 1));
                        }else{
                            array_push($promoSF, ["exist"=> true, "valid" => false,  "msg"=> "¡El código promocional [PROMOXK] solo aplica para frecuencia de pago Semestral!", "descuento"=> 0, 'code' => '403', "idPromocion"=> "ALL-DESC-002" , "cantidadMeses"=> 1,'message' => '¡El código promocional [PROMOXK] solo aplica para frecuencia de pago Semestral!'], 403);
                        }
                    }
                break;
                case "PROM25":
                    if(count($promoSF) == 0){
                        if ($frecPago == "PLAN SEMESTRAL"){
                            array_push($promoSF, array("exist"=> true, "valid"=>true,  "msg"=> "", "descuento"=> 25, "idPromocion"=> "ALL-DESC-25SET22" , "cantidadMeses"=> 1));
                        }else{
                            array_push($promoSF, ["exist"=> true, "valid" => false,  "msg"=> "¡El código promocional [PROM25] solo aplica para frecuencia de pago Semestral!", "descuento"=> 0, 'code' => '403', "idPromocion"=> "ALL-DESC-25SET22" , "cantidadMeses"=> 1,'message' => '¡El código promocional [PROM25] solo aplica para frecuencia de pago Semestral!'], 403);
                        }
                    }
                break;
                case "PROM35":
                    if(count($promoSF) == 0){
                        if ($frecPago == "PLAN ANUAL"){
                            array_push($promoSF, array("exist"=> true, "valid"=>true,  "msg"=> "", "descuento"=> 35, "idPromocion"=> "ALL-DESC-35OCT22" , "cantidadMeses"=> 1));
                        }else{
                            array_push($promoSF, ["exist"=> true, "valid" => false,  "msg"=> "¡El código promocional [PROM35] solo aplica para frecuencia de pago Anual!", "descuento"=> 0, 'code' => '403', "idPromocion"=> "ALL-DESC-35OCT22" , "cantidadMeses"=> 1,'message' => '¡El código promocional [PROM35] solo aplica para frecuencia de pago Anual!'], 403);
                        }
                    }
                break;
                case "MAYO30":
                    if(count($promoSF) == 0){
                        array_push($promoSF,["exist"=> true, "valid"=>false, "descuento"=> 25,  "msg"=> "¡El código promocional ha vencido!", 'code' => '403', "idPromocion"=> "ALL-DESC-002" , "cantidadMeses"=> 1,'message' => '¡El código promocional ha vencido!'], 403);
                    }
                break;
                case "MS40":
                    if(count($promoSF) == 0){
                        array_push($promoSF, array("exist"=> true, "valid"=>true,  "msg"=> "", "descuento"=> 40, "idPromocion"=> "ALL-DESC-40ABR22" , "cantidadMeses"=> 1));
                    }
                break;
                case 'BEN1': 
                    if(count($promoSF) == 0){
                        if ($frecPago == "PLAN MENSUAL"){
                            array_push($promoSF, array("exist"=> true, "valid"=>true,  "msg"=> "Gratis un beneficiario", "descuento"=> 0, "idPromocion"=> "BEN-CORT-IND-0001" , "cantidadMeses"=> 12));
                        }else if ($frecPago == "PLAN SEMESTRAL"){
                            array_push($promoSF, array("exist"=> true, "valid"=>true,  "msg"=> "Gratis un beneficiario", "descuento"=> 0, "idPromocion"=> "BEN-CORT-IND-0003" , "cantidadMeses"=> 12));
                        }else if ($frecPago == "PLAN ANUAL"){
                            array_push($promoSF, array("exist"=> true, "valid"=>true,  "msg"=> "Gratis un beneficiario", "descuento"=> 0, "idPromocion"=> "BEN-CORT-IND-0004" , "cantidadMeses"=> 12));
                        }else{
                            array_push($promoSF,['code' => '403', "idPromocion"=> "ALL-DESC-002" , "cantidadMeses"=> 1, "exist"=> true, "valid"=>false, "descuento"=> 0,  "msg"=> "No pudimos determinar el tipo del plan, por favor vuelva a intentarlo", 'message' => 'No pudimos determinar el tipo del plan, por favor vuelva a intentarlo'], 403);
                        }
                    }
                break;
                case 'MAR25':
                    if(count($promoSF) == 0){
                        array_push($promoSF,["exist"=> true, "valid"=>false, "descuento"=> 25,  "msg"=> "¡El código promocional ha vencido!", 'code' => '403', "idPromocion"=> "ALL-DESC-002" , "cantidadMeses"=> 1,'message' => '¡El código promocional ha vencido!'], 403);
                    }
                    break;
                case 'PROMO25':
                    if(count($promoSF) == 0){
                        array_push($promoSF, array("exist"=> true, "valid"=>true,  "msg"=> "", "descuento"=> 25, "idPromocion"=> "ALL-DESC-25JUN22" , "cantidadMeses"=> 1));
                    }
                    break;
                case 'FEB25':
                    array_push($promoSF,["exist"=> true, "valid"=>false, "descuento"=> 25,  "msg"=> "¡El código promocional ha vencido!", 'code' => '403', "idPromocion"=> "ALL-DESC-002" , "cantidadMeses"=> 1,'message' => '¡El código promocional ha vencido!'], 403);
                break;
                case 'DIC20':
                    array_push($promoSF,["exist"=> true, "valid"=>false, "descuento"=> 25,  "msg"=> "¡El código promocional ha vencido!", 'code' => '403', "idPromocion"=> "ALL-DESC-002" , "cantidadMeses"=> 1,'message' => '¡El código promocional ha vencido!'], 403);
                break;
                case 'DIC30':
                    array_push($promoSF,["exist"=> true, "valid"=>false, "descuento"=> 25,  "msg"=> "¡El código promocional ha vencido!", 'code' => '403', "idPromocion"=> "ALL-DESC-002" , "cantidadMeses"=> 1,'message' => '¡El código promocional ha vencido!'], 403);
                break;
                case 'VACUNAINFLUENZA':
                    if(count($promoSF) == 0){
                        array_push($promoSF, array("exist"=> true, "valid" => true,  "msg"=> "", "descuento"=> 0, "idPromocion"=> "TIT-PROM-IND-040" , "cantidadMeses"=> 1));
                        array_push($promoSF, array("exist"=> true, "valid" => true,  "msg"=> "", "descuento"=> 0, "idPromocion"=> "ALL-DESC-002" , "cantidadMeses"=> 1));
                    }
                break;
                case 'INFLUENZA-21':
                    if ($frecPago == "PLAN SEMESTRAL"){
                        array_push($promoSF, array("exist"=> true, "valid" => true,  "msg"=> "", "descuento"=> 0, "idPromocion"=> "TIT-PROM-IND-040" , "cantidadMeses"=> 2));
                    }else{
                        array_push($promoSF, ["exist"=> true, "valid" => false,  "msg"=> "¡El código promocional [INFLUENZA-21] solo aplica para frecuencia de pago Semestral!", "descuento"=> 0, 'code' => '403', "idPromocion"=> "ALL-DESC-002" , "cantidadMeses"=> 1,'message' => '¡El código promocional [INFLUENZA-21] solo aplica para frecuencia de pago Semestral!'], 403);
                    }
                break;
                case 'JULIO30':
                    array_push($promoSF,["exist"=> true, "valid" => false,  "msg"=> "¡El código promocional ha vencido!", "descuento"=> 0 ,'code' => '403', "idPromocion"=> "ALL-DESC-002" , "cantidadMeses"=> 1,'message' => '¡El código promocional ha vencido!'], 403);
                break;
                case 'CHEQUEO':
                    if(count($promoSF) == 0){
                        array_push($promoSF, array("exist"=> true, "valid" => true,  "msg"=> "", "descuento"=> 0, "idPromocion"=> "TIT-PROM-IND-014" , "cantidadMeses"=> 1));
                        array_push($promoSF, array("exist"=> true, "valid" => true,  "msg"=> "", "descuento"=> 0, "idPromocion"=> "ALL-DESC-002" , "cantidadMeses"=> 1));
                    }
                break;
                case '30MS':
                     array_push($promoSF,["exist"=> true, "valid" => false,  "msg"=> "¡El código promocional ha vencido!", "descuento"=> 0, 'code' => '403', "idPromocion"=> "ALL-DESC-002" , "cantidadMeses"=> 1,'message' => '¡El código promocional ha vencido!'], 403);
                break;
                case '50MS':
                     array_push($promoSF,["exist"=> true, "valid" => false,  "msg"=> "¡El código promocional ha vencido!", "descuento"=> 0, 'code' => '403','message' => '¡El código promocional ha vencido!'], 403);
                break;
                case 'AHORRO25':
                    if(count($promoSF) == 0){
                        array_push($promoSF, array("exist"=> true, "valid" => true,  "msg"=> "", "descuento"=> 25, "idPromocion"=> "ALL-DESC-005" , "cantidadMeses"=> 1));
                    }
                        //array_push($promoSF, array("idPromocion"=> "ALL-DESC-025" , "cantidadMeses"=> 1));
   
                        // switch($frecPago){
                        //     case 'PLAN MENSUAL':
                        //         return Response::json(['code' => '403','message' => '¡El codigo promocional [AHORRO25] no aplica para su frecuencia de pago Mensual!'], 403);
                        //     break;
                        //     case 'PLAN SEMESTRAL':
                        //         return Response::json(['code' => '403','message' => '¡El codigo promocional [AHORRO25] no aplica para su frecuencia de pago Semestral!'], 403);
                        //     break;
                        //     case 'PLAN ANUAL':
                        //         return Response::json(['code' => '403','message' => '¡El codigo promocional ha vencido!'], 403);
                        //         //array_push($promoSF, array("idPromocion"=> "ALL-DESC-025" , "cantidadMeses"=> 1));
                        //     break;
                        //   }
                    break;
                case "SMART25":
                        if(count($promoSF) == 0){
                                //array_push($promoSF,["exist"=> true, "valid"=>false, "descuento"=> 25,  "msg"=> "¡El código promocional ha vencido!", 'code' => '403', "idPromocion"=> "ALL-DESC-25OCT22" , "cantidadMeses"=> 1,'message' => '¡El código promocional ha vencido!'], 403);
                                array_push($promoSF, array("exist"=> true, "valid"=>true,  "msg"=> "", "descuento"=> 25, "idPromocion"=> "ALL-DESC-25SMRT22" , "cantidadMeses"=> 1));
                        }   
                    break;
                default:
                    if(count($promoSF) == 0){
                      array_push($promoSF, array("exist"=> false, "valid" => true,  "msg"=> "No existe el código ingresado", "descuento"=> 10, "idPromocion"=> "ALL-DESC-002" , "cantidadMeses"=> 1));
                    }
                break;
            }
        }

        return $promoSF;
    }
function verificarCodigoEmail($idVerificacion , $codigo){
    return  Sms_Registro::where('id_sms', $idVerificacion)
            ->where('sms',$codigo )
          ///  ->where(date('Y-m-d', 'fecha_creacion'),date("Y-m-d") )
            ->get()->count();
}





function verificarCodigo($idVerificacion , $codigo){
    return  Sms_Registro::where('id_sms', $idVerificacion)
            ->where('sms',$codigo )
          ///  ->where(date('Y-m-d', 'fecha_creacion'),date("Y-m-d") )
            ->get()->count();
}


public function sendEmailcode($email, $code=null)
    {   

        $keys=array('HTTP_CLIENT_IP','HTTP_X_FORWARDED_FOR','HTTP_X_FORWARDED','HTTP_FORWARDED_FOR','HTTP_FORWARDED','REMOTE_ADDR');
        $ip = "";
        foreach($keys as $k){
            if (!empty($_SERVER[$k]) && filter_var($_SERVER[$k], FILTER_VALIDATE_IP)){
                $ip = $_SERVER[$k];
            }
        }
       
        $code = rand(100000,999999);
        // Create sms
        $sms = array(
            'telefono' =>  "email",
            'cedula'=> $email, 
            'sms'=> $code,
            'validado'=> "0" ,
            'fecha_creacion'=> date("Y-m-d H:m:s"),
            'fecha_validacion'=> null ,
            'ip'=> $ip
        );
      
        
        $smsRegistro  = Sms_Registro::create($sms);
    
        $smsRegistro->save();
    
        $lastId = $smsRegistro->id_sms;



        $data = array(
            'email' => $email,
            'title'=>'Su usuario está generado',
            'code' => $code,
            'button' => 'Creá tu usuario',
            "image" => 'ew',
            "url" => 'index'
        );
//dd($lastId);
        $this->dispatch(new CodeEmail($data));


        
    return Response::json(['codigo' => $code, 'idEmail' => $lastId  ], 201);

    }


public function reenviarSms(Request $request){




    $keys=array('HTTP_CLIENT_IP','HTTP_X_FORWARDED_FOR','HTTP_X_FORWARDED','HTTP_FORWARDED_FOR','HTTP_FORWARDED','REMOTE_ADDR');
    $ip = "";
    foreach($keys as $k){
        if (!empty($_SERVER[$k]) && filter_var($_SERVER[$k], FILTER_VALIDATE_IP)){
            $ip = $_SERVER[$k];
        }
    }

    $smsRegistro = Sms_Registro::where('id_sms', $request['idvalidacion'])->first();

   // dd($smsRegistro);

    $code = rand(100000,999999);
    // Create sms
    $sms = array(
        'telefono' =>  $smsRegistro["telefono"],
        'cedula'=> $smsRegistro["cedula"], 
        'sms'=> $code,
        'validado'=> "0" ,
        'fecha_creacion'=> date("Y-m-d H:m:s"),
        'fecha_validacion'=> null ,
        'ip'=> $ip
    );

    $smsRegistro  = Sms_Registro::create($sms);
    $smsRegistro->save();
    $lastId = $smsRegistro->id_sms;

    $response = $this->sendSmsValidation1($smsRegistro["telefono"] , "Hola este es tu código ". $code." de verificación Medismart!" );

    return Response::json(['codigo' => $code, 'idSms' => $lastId ,"body" => json_encode($response) ], 201);

}







public function  sendSmsValidation(Request $request){
    $keys=array('HTTP_CLIENT_IP','HTTP_X_FORWARDED_FOR','HTTP_X_FORWARDED','HTTP_FORWARDED_FOR','HTTP_FORWARDED','REMOTE_ADDR');
    $ip = "";
    foreach($keys as $k){
        if (!empty($_SERVER[$k]) && filter_var($_SERVER[$k], FILTER_VALIDATE_IP)){
            $ip = $_SERVER[$k];
        }
    }
   
        $code = rand(100000,999999);

    // Create sms
    $sms = array(
        'telefono' =>  $request["telefono"],
        'cedula'=> $request["cedula"], 
        'sms'=> $code,
        'validado'=> "0" ,
        'fecha_creacion'=> date("Y-m-d H:m:s"),
        'fecha_validacion'=> null ,
        'ip'=> $ip
    );
  
    
    $smsRegistro  = Sms_Registro::create($sms);

    $smsRegistro->save();

    $lastId = $smsRegistro->id_sms;
   

    $response = $this->sendSmsValidation1($smsRegistro["telefono"]  , "Hola este es tu código ". $code." de verificación Medismart!" );


    return Response::json(['codigo' => $code, 'idSms' => $lastId ,"body" => json_encode($response) ], 201);

     
}







//////////////////////////////////




    function sendSmsValidation1($telefono , $body){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://red.medismart.online/api-medi/public/refer/sms',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
            "phone":"506'.$telefono.'",
            "body":"'.$body.'"
        }',

        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Cookie: PHPSESSID=5acf729db1bc017d87a94e63c39c2574'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return json_encode($response);
       
    }









public function CrearFactura(Request $request)
    {
       


$beneSF=$request->beneficiarios;
 
$mascotaSF=$request->mascotas;
$promoSF=$request->promociones;


       $frecuenciaPago="Mensual";
       $personatipoid="extranjero";



       $dataSF = array('frecuenciaPago' => $request->frecuenciaPago,
       'rebajoDias'=>$request->rebajoDias   ,
                    'tipoIdentificacion'=> $request->tipoIdentificacion,
                    'cedula'=>$request->cedula,
                    'nombreCompleto' => $request->nombreCompleto,
                    'fechaNacimiento' =>$request->fechaNacimiento,
                    'telefonoCelular'=>$request->telefonoCelular,
                    'telefonoCasa' => $request->telefonoCasa,
                    'correo'=> $request->correo,
                    'direccion'=>$request->direccion,
                    'provincia'=>$request->provincia,
                    'canton'=>$request->canton,
                    'distrito'=>$request->distrito,
                    'estadoCivil'=>'Soltero(a)',
                    'genero'=> $request->genero,
                    'coberturaOncosmart'=>$request->coberturaOncosmart,
                   
                    'beneficiarios'=>$beneSF,
                    'mascotas'=>$mascotaSF,
                    'promociones'=>$promoSF,
        

                    
    );
                    $responseSF = $this->primeraventa(json_encode($dataSF));
              
                    $res=json_decode($responseSF, true);






         /// dd($res);
    
                $array = json_decode($responseSF, true);

			   $subtotalsiniva=0;
            ///  dd($array["oportunidad"]["OPDescuento"]);
                foreach ($array["oportunidad"]["OPLineas"] as $element) {
                   ///(///) dd($element["precio"]);
                    $subtotalsiniva=$subtotalsiniva+$element["precio"];
                }
                
             ///   dd($array["costoPlan"]);
          ///  dd($array["oportunidad"]["OPLineas"][0]["precio"]);
             //   echo $array["resultado"]; // Imprime "1"
               /// echo $array["oportunidad"]["OPTotal"]; // Imprime "23"
                //(/echo $array["oportunidad"]["OPLineas"][0]["precio"]; 

  if( isset($array["costoPlan"])){
                $totalIva = $array["costoPlan"]* 0.13;
                $Totalivam=$totalIva+$array["costoPlan"];

        ///     dd($totalIva);
             $array['iva'] =$totalIva;

             $array['totaiva'] =$Totalivam;
             $array['frecuencia'] =$request->frecuenciaPago;

 }

$porcentaje= $array["oportunidad"]["OPDescuento"]/100;

       
                $descuento= $subtotalsiniva*$porcentaje;

                $subtotacondescuento=$subtotalsiniva-$descuento;
                $ivaProrateo=$subtotacondescuento*0.13;

                $totalconiva=$subtotacondescuento+$ivaProrateo;


$Prorateoivas=($array['montoProrateo']*0.13)+($array['montoProrateo']);
                $array['subtotalsiniva']=$subtotalsiniva;

$array['prorateiva']=$Prorateoivas;


                    $array['porcentaje']=$porcentaje;

                    $array['descuento']=$descuento;
                    $array['subtotacondescuento']=$subtotacondescuento;

                    $array['ivaProrateo']=$ivaProrateo;

                    $array['totalconiva']=$totalconiva;
                    $array['frecuenciaPago']=$request->rebajoDias;

                 
                       return Response::json(['status'=>1 ,json_encode($array)], 201);
                




      
 
        // Aquí va la lógica para crear el usuario
    }

  public function C11rearFactura(Request $request)
    {
       // return "dd";

//dd($request->fechaNacimiento);
     ///  const jsonArray = JSON.parse($request);

       $frecuenciaPago="Mensual";
       $personatipoid="extranjero";



       $dataSF = array('frecuenciaPago' => $request->frecuenciaPago,
                    'tipoIdentificacion'=> $request->tipoIdentificacion,
                    'cedula'=>$request->cedula,
                    'nombreCompleto' => $request->nombreCompleto,
                    'fechaNacimiento' =>$request->fechaNacimiento,
                    'telefonoCelular'=>$request->telefonoCelular,
                    'telefonoCasa' => $request->telefonoCasa,
                    'correo'=> $request->correo,
                    'direccion'=>$request->direccion,
                    'provincia'=>$request->provincia,
                    'canton'=>$request->canton,
                    'distrito'=>$request->distrito,
                    'estadoCivil'=>'Soltero(a)',
                    'genero'=> $request->genero,
                    'coberturaOncosmart'=>$request->coberturaOncosmart,
                    //'coberturaOncosmart'= $request->coberturaOncosmart,
                    //'beneficiarios'=>$beneSF,
                    //'mascotas'=>$mascotaSF,
                    //'promociones'=>$promoSF,
                    'rebajoDias'=>$request->rebajoDias
    );
                   /// $salesforcontroller = new SalesforceController;
                    $responseSF = $this->primeraventa(json_encode($dataSF));
                    
                    $res=json_decode($responseSF, true);






           // $res = json_decode($res);


          
    
                $array = json_decode($responseSF, true);


                
             ///   dd($array["costoPlan"]);
          ///  dd($array["oportunidad"]["OPLineas"][0]["precio"]);
             //   echo $array["resultado"]; // Imprime "1"
               /// echo $array["oportunidad"]["OPTotal"]; // Imprime "23"
                //(/echo $array["oportunidad"]["OPLineas"][0]["precio"]; 


                $totalIva = $array["costoPlan"]* 0.13;
                $Totalivam=$totalIva+$array["costoPlan"];

        ///     dd($totalIva);
             $array['iva'] =$totalIva;

             $array['totaiva'] =$Totalivam;





                 
                          return Response::json(['status'=>1 ,json_encode($array)], 403);
                


   



      
 
        // Aquí va la lógica para crear el usuario
    }




   }
