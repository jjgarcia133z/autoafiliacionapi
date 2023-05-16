<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ViewsController;
use App\Http\Controllers\ProductController;

use App\Http\Controllers\UbicacionController;
use App\Http\Controllers\parametizacion;

use App\Http\Controllers\PdfController;

use App\Http\Controllers\politicasController;

use App\Http\Controllers\PlanesaddController;
use App\Http\Controllers\SalesforceController;
/*PlanesaddController
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/validardata/{Cedula}/{Correo}/{Telefono}', [ViewsController::class, 'validardata'])->name('validardata');

Route::get('/getcontrato/{Cedula}', [ViewsController::class, 'getcontrato'])->name('getcontrato');

Route::get('/sms', [parametizacion::class, 'sms'])->name('sms');


Route::post('/updateop', [ViewsController::class, 'updateop'])->name('updateop');





Route::post('/link', [ViewsController::class, 'link'])->name('link');



Route::GET('cambiostatus/{cedula}', [ViewsController::class, 'cambiostatus'])->name('cambiostatus.cambiostatus');
Route::resource('planadd', PlanesaddController::class);




Route::post('pagoFactura', [ViewsController::class, 'pagoFactura'])->name('pagoFactura.pagoFactura');


Route::get('/contra', [ViewsController::class,'contra'])->name('contra.contra');



Route::GET('sendSmsValidation', [ViewsController::class, 'sendSmsValidation'])->name('sendSmsValidation.sendSmsValidation');

Route::GET('CalculoProrrateo', [ViewsController::class, 'CalculoProrrateo'])->name('CalculoProrrateo.CalculoProrrateo');

Route::GET('validardatos/{cedula}/{correo}/{telefono}', [ViewsController::class, 'validardatos'])->name('validardatos.validardatos');



Route::post('/SaveAfiliado', [ViewsController::class, 'SaveAfiliado']);

Route::post('pagoFactura1', [ViewsController::class, 'pagoFactura1'])->name('pagoFactura1.pagoFactura1');

Route::get('/consultainterno/{cedula}', [ViewsController::class,'consultainterno'])->name('consultainterno.consultainterno');





Route::get('/afiliacion/reenviar-sms', [ViewsController::class,'reenviarSms'])->name('reenviar.sms');
Route::get('/afiliacion/verificarCodigoEmail/{idVerificacion}/{codigo}', [ViewsController::class,'verificarCodigoEmail'])->name('verificarCodigoEmail.email');



Route::get('/ahorro/{Titular}/{Beneficiario}/{Mascota}/{Oncosmart}/{Oncosmartbene}/{monto}/{tipo}', [ViewsController::class,'ahorro'])->name('ahorro.ahorro');




Route::get('/afiliacion/consultar-promocion/{promocion}/{frecPago}', [ViewsController::class, 'getPromotion']);

Route::post('/CrearFactura', [ViewsController::class, 'CrearFactura']);


Route::get('/afiliacion/verificarCodigo/{idVerificacion}/{codigo}', 
[ViewsController::class,'verificarCodigo'])->name('verificarCodigo.sms'); 
Route::get('/afiliacion/sendEmailcode/{email}/{code}', 
[ViewsController::class,'sendEmailcode'])->name('sendEmailcode.email');


Route::GET('userStatus/{cedula}', [ViewsController::class, 'userStatus'])->name('userStatus.userStatus');


Route::get('/tipoAnimal', [parametizacion::class, 'tipoAnimal']);



Route::get('/planes', [parametizacion::class, 'Planes']);


Route::get('/planesadd', [parametizacion::class, 'Planesadd']);

Route::get('/politicas', [parametizacion::class, 'politicas']);
Route::get('/genero', [parametizacion::class, 'genero']);

Route::get('/parentesco', [parametizacion::class, 'parentesco']);


Route::get('/getProvincias', [UbicacionController::class, 'getProvincias']);
Route::get('ubicaciones/cantones/{distelec}',[UbicacionController::class,'getCantones'])->name('api.cantones');
Route::get('ubicaciones/distritos/{distelec}',[UbicacionController::class,'getDistritos'])->name('api.distritos');



Route::get('/tipoidentificacion', [parametizacion::class, 'tipoidentificacion']);
Route::resource('politica', politicasController::class);






Route::get('/datospersonal/{Cedula?}', [ViewsController::class, 'buscarCedula'])->name('datospersonal');




Route::post('/pdf-to-html', [PdfController::class, 'pdfToHtml'])->name('pdf-to-html');




Route::resource('productos', ProductController::class);

//Route::resource('planesadd', PlanesaddController::class);



