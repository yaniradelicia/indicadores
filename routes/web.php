<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/


//para autenticarse
Route::get('/', 'Login\LoginController@index')->name('login');
Route::post('login', 'Login\LoginController@login')->name('login_post');
Route::get('logout', 'Login\LoginController@logout')->name('logout');

//reportes gestor
Route::get('reporte/rep_gestor', 'ReporteController@reporteGestor')->name('reporte_gestor');
Route::get('reporte/buscarg_g/{firmages}', 'ReporteController@buscarGestor')->name('ges_buscar');
Route::get('reporte/buscarg_c/{car}', 'ReporteController@buscarGestorCartera')->name('gescar_buscar');

//reportes cartera
Route::get('reporte/rep_cartera', 'ReporteController@reporteCartera')->name('reporte_cartera');
Route::get('reporte/rep_cartera_gestion', 'ReporteController@reporteCarteraGestion')->name('reporte_cartera_g');
Route::get('reporte/rep_cartera_pagos', 'ReporteController@reporteCarteraPagos')->name('reporte_cartera_p');

Route::get('reporte/buscarc_c/{car}/{tip}', 'ReporteController@buscarCartera');
Route::get('reporte/buscarcli_g/{car}/{tip}/{fec_i}/{fec_f}', 'ReporteController@buscarCarteraGestion');
Route::get('reporte/buscarpdp_g/{car}/{tip}/{fec_i}/{fec_f}', 'ReporteController@buscarCarteraGestionPDP');
Route::get('reporte/buscarcon_g/{car}/{tip}/{fec_i}/{fec_f}', 'ReporteController@buscarCarteraGestionCON');
Route::get('reporte/buscarc_p/{car}/{tip}/{fec_i}/{fec_f}', 'ReporteController@buscarCarteraPagos');

Route::get('prueba', 'ReporteController@buscarPruebas');



/*Route::group(['middleware' => ['auth']], function(){

});*/