<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Gestor;
use App\Cartera;
use Illuminate\Http\Response;

class ReporteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     /*---------------------------VISTA GESTORR------------------------------------------------------------ */
    public function reporteGestor(Request $request)
    {
        $cartera=Cartera::cartera();
        $gestor=Gestor::gestores2();

        //$gestor=DB::connection('mysql2')->table('sub_empleado')->select('emp_nom','emp_firma')->whereEmp_est('0')->get();

       /* $gest=$request->get('tipo');
        $buscar=$request->get('buscarpor');*/

        //$busgest=Gestor::buscarpor($buscar);
        //dd($cartera);

        return view('reportes.rep_gestor',compact('cartera','gestor'));
    }


    /* ----------------------------VISTA CARTERAAAAAAAAAAAA--------------------------- */

    public function reporteCartera(Request $request)
    {
        $carterad=Cartera::carteraDetalle();
        return view('reportes.rep_cartera',compact('carterad'));
    }

    public function reporteCarteraGestion(Request $request)
    {
        $carterag=Cartera::carteraDetalle();
    
        return view('reportes.rep_cartera_gestion',compact('carterag'));
    }

    public function reporteCarteraPagos(Request $request)
    {
        $carterap=Cartera::carteraDetalle();
        return view('reportes.rep_cartera_pagos',compact('carterap'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */









     /* ---------BUSCARRRRR-------------------VISTA GSTORRRRRRRRRRRRRRRR-------------------------------------- */
    public function buscarGestorCartera($car)
    {
        $results1 = DB::connection('mysql')
        ->table('cartera')
        //->distinct()
        ->select('cartera','mes','meta','recupero','efectividad')
        ->where('cartera','like','%'.$car.'%')
        ->get();
        //return Response::json($results);
        //dd($results);
        return $results1;
    }

    public function buscarGestor($firmages)
    {
        $results2 = DB::connection('mysql')
        ->table('gestor')
        //->distinct()
        ->select('cartera','modalidad','firma','mes','meta','ranking','efectividad')
        ->where('firma','like','%'.$firmages.'%')
        ->get();
        //return Response::json($results);
        //dd($results);
        return $results2;
        
    }

    /* --------BUSCARRRRR-------------------VISTA CARTERASSSSSSSSS---------------------------------------------------- */

    public function buscarCartera($car, $tip)
    {
        $results3 = DB::connection('mysql')
        ->table('cartera_detalle')
        //->distinct()
        //->select($tip,'cartera','dep','saldo_deuda','capital','monto_camp','prioridad','entidades','tramo','score','dep_ind')
        ->select(DB::raw($tip.' as tipo'),DB::raw('count(cartera) as cantidad'),
                DB::raw('sum(capital) as total_capital'),
                DB::raw('sum(saldo_deuda) as total_deuda'),
                DB::raw('sum(monto_camp) as total_importe'))
        //->where('cartera','like','%'.$car.'%')
        ->where('car_id_fk','=',$car)
        ->groupBy($tip)
        ->get();
        //return Response::json($results);
        //dd($results3);
        return $results3;
    }

    public static function buscarCarteraGestion($car, $tip, $fec_i, $fec_f){

        $gestion=DB::connection('mysql2')
        ->table('gestion_cliente as g')
        ->rightjoin('cliente as c','g.cli_id_FK','=','c.cli_id')
        ->join('cartera as car','c.car_id_FK','=','car.car_id')
        ->join('indicadores.cartera_detalle as cd','c.cli_cod','=','cd.cuenta')
        ->select(DB::raw($tip.' as tipo'),DB::raw('count(cartera) as cant_gestion'),
                DB::raw('count(distinct(cli_cod)) as cant_clientes'),DB::raw('sum(capital) as total_capital'),
                DB::raw('sum(saldo_deuda) as total_deuda'),DB::raw('sum(monto_camp) as total_importe'))
        ->where('cd.car_id_fk','=',$car)
        ->whereBetween(DB::raw('(date_format(ges_cli_fec,"%Y-%m-%d"))'),array($fec_i,$fec_f))
        ->groupBy($tip)
        ->get();
        return $gestion;

       //dd($gestion);
        /*$ges=DB::connection('mysql2')
        ->table('cliente as c')
        ->join('gestion_cliente as g','c.cli_id','=','g.cli_id_fk')
        ->join('cartera as car','c.car_id_FK','=','car.car_id')
        //->join('indicadores.cartera_detalle as cd','c.cli_cod','=','cd.cuenta')
        ->where('car_id_FK','=','34')
        ->whereBetween(DB::raw('(date_format(ges_cli_fec,"%d/%m/%Y"))'),array('07/03/2020','31/03/2020'))
        ->select('ges_cli_id',DB::raw('(date_format(ges_cli_fec,"%d/%m/%Y"))'),
        'car_id_fk','car_nom', 'cli_id_fk','cli_cod',DB::raw('count(car_nom) as cant'))
        ->groupBy(DB::raw('(date_format(ges_cli_fec,"%d/%m/%Y"))'))
        ->get();*/

        /*$cant_cli = DB::connection('mysql')
        ->table('cartera_detalle')
        ->select('tramo',DB::raw('count(cartera) as cantidad'))
        ->where('car_id_fk','=','34')
        ->groupBy('tramo');*/
        
    }

    public static function buscarCarteraGestionPDP($car, $tip, $fec_i, $fec_f){

        $gestion=DB::connection('mysql2')
        ->table('gestion_cliente as g')
        ->rightjoin('cliente as c','g.cli_id_FK','=','c.cli_id')
        ->join('cartera as car','c.car_id_FK','=','car.car_id')
        ->join('indicadores.cartera_detalle as cd','c.cli_cod','=','cd.cuenta')
        ->select(DB::raw($tip.' as tipo'),DB::raw('count(cartera) as cantidad'),
                DB::raw('count(distinct(cuenta)) as cant_clientes'),DB::raw('sum(capital) as total_capital'),
                DB::raw('sum(saldo_deuda) as total_deuda'),DB::raw('sum(monto_camp) as total_importe'))
        ->where('cd.car_id_fk','=',$car)
        ->whereIn('res_id_fk',array('1','43'))
        ->whereBetween(DB::raw('(date_format(ges_cli_fec,"%Y-%m-%d"))'),array($fec_i,$fec_f))
        ->groupBy($tip)
        ->get();
        return $gestion;
        //dd($gestion);

    }

    public static function buscarCarteraGestionCON($car, $tip, $fec_i, $fec_f){

        $gestion=DB::connection('mysql2')
        ->table('gestion_cliente as g')
        ->rightjoin('cliente as c','g.cli_id_FK','=','c.cli_id')
        ->join('cartera as car','c.car_id_FK','=','car.car_id')
        ->join('indicadores.cartera_detalle as cd','c.cli_cod','=','cd.cuenta')
        ->select(DB::raw($tip.' as tipo'),DB::raw('count(cartera) as cantidad'),
                DB::raw('count(distinct(cuenta)) as cant_clientes'),DB::raw('sum(capital) as total_capital'),
                DB::raw('sum(saldo_deuda) as total_deuda'),DB::raw('sum(monto_camp) as total_importe'))
        ->where('cd.car_id_fk','=',$car)
        ->whereIn('res_id_fk',array('2'))
        ->whereBetween(DB::raw('(date_format(ges_cli_fec,"%Y-%m-%d"))'),array($fec_i,$fec_f))
        ->groupBy($tip)
        ->get();
        return $gestion;
        //dd($gestion);

    }

    public static function buscarCarteraPagos($car, $tip, $fec_i, $fec_f){

        /*
        select p.car_id_FK,car_nom,cli_cod,pag_cli_mon,pag_cli_fec
        from pago_cliente_2 as p inner join cartera as car
        on p.car_id_fk = car.car_id 
        right join cliente c 
        on p.pag_cli_cod=c.cli_cod
        WHERE car_id=34  
*/

        $gestion=DB::connection('mysql2')
        ->table('pago_cliente_2 as p')
        ->rightjoin('cliente as c','p.pag_cli_cod','=','c.cli_cod')
        ->join('cartera as car','p.car_id_fk','=','car.car_id')
        ->join('indicadores.cartera_detalle as cd','p.pag_cli_cod','=','cd.cuenta')
        //->select('cd.car_id_fk','cartera','cuenta','pag_cli_mon','pag_cli_fec')
        //->select('tramo','pag_cli_mon',DB::raw('count(pag_cli_mon) as cantidad'))
        ->select(DB::raw($tip.' as tipo'),'pag_cli_mon',DB::raw('count(pag_cli_mon) as cantidad'),
            DB::raw('sum(pag_cli_mon) as total_pagos'),DB::raw('sum(capital) as total_capital'),
            DB::raw('sum(saldo_deuda) as total_deuda'),DB::raw('sum(monto_camp) as total_importe'))
        ->where('cd.car_id_fk','=',$car)
        //->where('cd.car_id_fk','=','34')
        ->whereBetween(DB::raw('(date_format(pag_cli_fec,"%Y-%m-%d"))'),array($fec_i,$fec_f))
        //->whereBetween(DB::raw('(date_format(pag_cli_fec,"%Y-%m-%d"))'),array('2020-01-01','2020-01-31'))
        ->groupBy($tip)
        //->groupBy('tramo')
        ->groupBy('pag_cli_mon')
        ->get();
        return $gestion;
        //dd($gestion);

    }

 





    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
