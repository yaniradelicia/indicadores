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
Route::get('/', 'LoginController@index')->name('login');
Route::post('login', 'LoginController@login')->name('login_post');
Route::get('logout', 'LoginController@logout')->name('logout');


Route::group(['middleware' => ['auth']], function(){
    //fecha de modificacion
    //Route::get('reporte/header', 'ReporteController@fechaModificacion')->name('fecha');

    //ver pagos
    Route::get('reporte/ver_pagos', 'ReporteController@verPagos');

    //ver carteras
    Route::get('reporte/ver_carteras', 'ReporteController@verCarteras');

    //reportes gestor-compartivo
    Route::get('reporte/rep_gestor', 'ReporteController@reporteGestor')->name('reporte_gestor');
    Route::get('reporte/rep_gestor_c', 'ReporteController@reporteGestorCartera')->name('reporte_gestor_c');
    Route::get('reporte/buscarg_g/{firmages}', 'ReporteController@buscarGestor')->name('ges_buscar');
    Route::get('reporte/buscarg_c/{car}', 'ReporteController@buscarGestorCartera')->name('gescar_buscar');
    Route::get('reporte/buscarg_cp/{car}', 'ReporteController@buscarGestorCarteraPago');
    Route::get('reporte/buscarg_cco/{car}', 'ReporteController@buscarGestorCarteraCON');
    Route::get('reporte/buscarg_cpcierre/{car}', 'ReporteController@buscarGestorCarteraPagoCierre');
    Route::get('reporte/buscarg_ccocierre/{car}', 'ReporteController@buscarGestorCarteraCONCierre');

    //reportes cartera-estructura
    Route::get('reporte/rep_cartera', 'ReporteController@reporteCartera')->name('reporte_cartera');
    Route::get('reporte/rep_cartera_gestion', 'ReporteController@reporteCarteraGestion')->name('reporte_cartera_g');
    Route::get('reporte/rep_cartera_pagos', 'ReporteController@reporteCarteraPagos')->name('reporte_cartera_p');
    Route::get('reporte/rep_cartera_indicador', 'ReporteController@reporteCarteraIndicador')->name('reporte_cartera_i');
    Route::get('reporte/rep_cartera_timing', 'ReporteController@reporteCarteraTiming')->name('reporte_cartera_t');
    Route::get('reporte/rep_cartera_recupero', 'ReporteController@reporteCarteraRecupero')->name('reporte_cartera_rec');

    Route::get('reporte/buscarc_tp/{car}', 'ReporteController@buscarCarteraTimingP');
    Route::get('reporte/buscarc_tc/{car}', 'ReporteController@buscarCarteraTimingC');
    Route::get('reporte/buscarc_recp/{car}/{fec_i}/{fec_f}', 'ReporteController@buscarCarteraRecuperoP');
    Route::get('reporte/buscarc_recc/{car}/{fec_i}/{fec_f}', 'ReporteController@buscarCarteraRecuperoC');
    Route::get('reporte/buscarc_c/{car}/{tip}/{mes}/', 'ReporteController@buscarCartera');
    Route::get('reporte/buscarc_ubcon/{car}/{ubi}/{tip}/{mes}', 'ReporteController@buscarCarteraContactos');
    Route::get('reporte/buscarc_ubsing/{car}/{tip}/{mes}', 'ReporteController@buscarCarteraClientesSinGestion');
    Route::get('reporte/buscarcli_g/{car}/{tip}/{fec_i}/{fec_f}', 'ReporteController@buscarCarteraGestion');
    Route::get('reporte/buscarpdp_g/{car}/{tip}/{fec_i}/{fec_f}', 'ReporteController@buscarCarteraGestionPDP');
    Route::get('reporte/buscarcon_g/{car}/{tip}/{fec_i}/{fec_f}', 'ReporteController@buscarCarteraGestionCON');
    Route::get('reporte/buscarc_p/{car}/{tip}/{fec_i}/{fec_f}', 'ReporteController@buscarCarteraPagos');

    //--ubicabilidad
    Route::get('reporte/buscarc_ubic/{car}/{fec_i}/{fec_f}', 'ReporteController@buscarCarteraUbic');
    Route::get('reporte/buscarc_ubic_pdp/{car}/{fec_i}/{fec_f}', 'ReporteController@buscarCarteraUbicPDP');
    Route::get('reporte/buscarc_ubic_con/{car}/{fec_i}/{fec_f}', 'ReporteController@buscarCarteraUbicCON');
    Route::get('reporte/buscarc_ubic_pag/{car}/{fec_i}/{fec_f}', 'ReporteController@buscarCarteraUbicPagos');

    //Route::get('reporte/buscari', 'ReporteController@buscarIndicadores');
    //indicadores operativos
    Route::get('reporte/lista_entidades/{car}', 'IndicadorController@listaEntidades');
    Route::get('reporte/lista_score/{car}', 'IndicadorController@listaScore');
    Route::get('reporte/lista_dep/{car}', 'IndicadorController@listaDep');
    Route::get('reporte/lista_tramos/{car}', 'IndicadorController@listaTramos');
    Route::get('reporte/lista_prioridad/{car}', 'IndicadorController@listaPrioridad');
    Route::get('reporte/lista_situacion/{car}', 'IndicadorController@listaSituacion');
    Route::get('reporte/buscari_co/{car}/{asig}/{estr}/{valor}', 'IndicadorController@buscarCobertura');
    Route::get('reporte/buscari_con/{car}/{asig}/{estr}/{valor}', 'IndicadorController@buscarContacto');
    Route::get('reporte/buscari_conef/{car}/{asig}/{estr}/{valor}', 'IndicadorController@buscarContactoEfectivo');
    Route::get('reporte/buscari_in/{car}/{asig}/{estr}/{valor}', 'IndicadorController@buscarIntensidad');
    Route::get('reporte/buscari_di/{car}/{asig}/{estr}/{valor}', 'IndicadorController@buscarIntensidadDirecta');
    Route::get('reporte/buscari_tc/{car}/{asig}/{estr}/{valor}', 'IndicadorController@buscarTasa');
    Route::get('reporte/buscari_ep/{car}/{asig}/{estr}/{valor}', 'IndicadorController@buscarEfectividad');
    //Route::get('reporte/buscart', 'ReporteController@buscarTotales');

    //vistas campañassss
    Route::get('campana/crear_campana', 'CampanaController@index')->name('crear_campana');
    Route::get('campana/buscar_campana', 'CampanaController@buscar')->name('buscar_campana');
    Route::get('/campana/ver', 'CampanaController@ver');
    Route::get('/campana/insertar', 'CampanaController@insertar');
    Route::get('/campana/buscar', 'CampanaController@filtro');
    Route::get('/campana/mostrar', 'CampanaController@mostrar');
    Route::get('/campana/cargar', 'CampanaController@cargar');
    Route::get('/campana/actualizar', 'CampanaController@actualizar');

    //vistas etiqueta
    Route::get('etiqueta/crear_etiqueta', 'EtiquetaController@index')->name('crear_etiqueta');
    Route::get('etiqueta/buscar_etiqueta', 'EtiquetaController@buscar')->name('buscar_etiqueta');
    Route::get('/etiqueta/insertar', 'EtiquetaController@insertar');
    Route::get('/etiqueta/buscar', 'EtiquetaController@filtro');
    Route::get('/etiqueta/mostrar', 'EtiquetaController@mostrar');

    //vistas consolidado
    Route::get('reporte/consolidado', 'GestionController@index')->name('reporte_consolidado');
    Route::get('reporte/mostrar_consolidado', 'GestionController@repConsolidado');
    Route::get('reporte/mostrar_consolidado_fecha/{fecha}', 'GestionController@repConsolidadoFecha');

    //vistas Plan de Trabajo
    Route::get('plan/crear_plan', 'PlanController@index')->name('crear_plan');
    Route::get('plan/buscar_plan', 'PlanController@buscar')->name('buscar_plan');
    Route::get('/plan/ver', 'PlanController@ver');
    Route::get('/plan/insertar', 'PlanController@insertar');
    Route::get('/plan/filtro', 'PlanController@filtro');
    Route::get('/plan/mostrar_detalle', 'PlanController@mostrarDetalle');
    Route::get('/plan/mostrar_resultado', 'PlanController@mostrarResultado');
    Route::get('/plan/mostrar_usuario', 'PlanController@mostrarUsuario');

    //vistas indicador gestor
    Route::get('gestor/buscar_indicador', 'IndicadorGestorController@index')->name('indicador_gestor');
    Route::get('gestor/carga_gestores/{car}', 'IndicadorGestorController@cargarGestores');
    Route::get('gestor/carga_gestores_total', 'IndicadorGestorController@cargarGestoresTotal');
    Route::get('gestor/buscargestor_g/{car}/{firma}/{tip}/{fec_i}/{fec_f}', 'IndicadorGestorController@buscarGestorGestiones');
    Route::get('gestor/buscargestor_pdp/{car}/{firma}/{tip}/{fec_i}/{fec_f}', 'IndicadorGestorController@buscarGestorPDPS');
    Route::get('gestor/buscargestor_conf/{car}/{firma}/{tip}/{fec_i}/{fec_f}', 'IndicadorGestorController@buscarGestorCONF');
    Route::get('gestor/buscargestor_pagos/{car}/{firma}/{tip}/{fec_i}/{fec_f}', 'IndicadorGestorController@buscarGestorPagos');
    Route::get('gestor/buscargestor_ubic/{car}/{firma}/{fec_i}/{fec_f}', 'IndicadorGestorController@buscarGestorUbic');
    Route::get('gestor/buscargestor_ubic_pdp/{car}/{firma}/{fec_i}/{fec_f}', 'IndicadorGestorController@buscarGestorUbicPDPS');
    Route::get('gestor/buscargestor_ubic_conf/{car}/{firma}/{fec_i}/{fec_f}', 'IndicadorGestorController@buscarGestorUbicCONF');
    Route::get('gestor/buscargestor_ubic_pagos/{car}/{firma}/{fec_i}/{fec_f}', 'IndicadorGestorController@buscarGestorUbicPagos');


    //asignacion de firmas
    Route::get('asignacion/firmas', 'RegistroFirmasController@index');
    Route::get('/asignacion/carga_carteras/{mes}', 'RegistroFirmasController@cargarCarteras');
    Route::get('/asignacion/actualizado_automatico/{car}/{mes}', 'RegistroFirmasController@guardarAutomatico');
    Route::get('/asignacion/carga_clientes_pagos/{car}/{mes}', 'RegistroFirmasController@cargaClientes');
    Route::get('/asignacion/carga_cliente/{codigo}/{fecha}', 'RegistroFirmasController@cargaClienteCod');
    Route::get('/asignacion/guardar_cliente/{car}/{cod}/{pago}/{fec}/{usuario}', 'RegistroFirmasController@GuardarCliente');
});


Route::get('reporte/buscarc_ubsing/{car}/{tip}/{mes}', 'ReporteController@buscarCarteraClientesSinGestion');
Route::get('reporte/b', 'ReporteController@buscarContacto2');
Route::get('reporte/buscart', 'ReporteController@buscarTotales');
