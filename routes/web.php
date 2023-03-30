<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ViewsController;
use App\Http\Controllers\ProductController;

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

Route::get('/planes', [parametizacion::class, 'Planes']);

Route::get('/planesadd', [parametizacion::class, 'Planesadd']);

Route::get('/politicas', [parametizacion::class, 'politicas']);



Route::resource('politica', politicasController::class);






Route::get('/datospersonal/{Cedula?}', [ViewsController::class, 'buscarCedula'])->name('datospersonal');




Route::post('/pdf-to-html', [PdfController::class, 'pdfToHtml'])->name('pdf-to-html');




Route::resource('productos', ProductController::class);

//Route::resource('planesadd', PlanesaddController::class);



