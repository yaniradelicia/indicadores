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


    public function fechaModificacion(){
        /*SELECT UPDATE_TIME
        FROM   information_schema.tables
        WHERE  TABLE_SCHEMA = 'indicadores' and UPDATE_TIME IS NOT NULL
        ORDER BY UPDATE_TIME DESC*/

        $dia = DB::connection('mysql')
        ->table('information_schema.tables')
        ->select('UPDATE_TIME')
        ->where('TABLE_SCHEMA','=','indicadores')
        ->whereNotNull('UPDATE_TIME')
        ->orderBy('UPDATE_TIME')
        ->get();
        //dd($fecha);

        return view('themeAdmin.header',compact('dia'));

    }


     /*---------------------------VISTA GESTORR-COMPARTIVO------------------------------------------------------------ */
    public function reporteGestor(Request $request)
    {
        //$cartera=Cartera::cartera();
        $gestor=Gestor::gestores2();

        //$gestor=DB::connection('mysql2')->table('sub_empleado')->select('emp_nom','emp_firma')->whereEmp_est('0')->get();

       /* $gest=$request->get('tipo');
        $buscar=$request->get('buscarpor');*/

        //$busgest=Gestor::buscarpor($buscar);
        //dd($cartera);

        return view('reportes.rep_gestor',compact('gestor'));
    }

    public function reporteGestorCartera(Request $request)
    {
        $cartera=Cartera::cartera2();
        //$gestor=Gestor::gestores2();

        //$gestor=DB::connection('mysql2')->table('sub_empleado')->select('emp_nom','emp_firma')->whereEmp_est('0')->get();

       /* $gest=$request->get('tipo');
        $buscar=$request->get('buscarpor');*/

        //$busgest=Gestor::buscarpor($buscar);
        //dd($cartera);

        return view('reportes.rep_gestor_c',compact('cartera'));
    }


    /* ----------------------------VISTA ESTRUCTURA--CARTERA--------------------------- */

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

    public function reporteCarteraIndicador(Request $request)
    {
        $carterai=Cartera::carteraDetalle();
        return view('reportes.rep_cartera_indicador',compact('carterai'));
    }
    public function reporteCarteraTiming(Request $request)
    {
        $carterai=Cartera::carteraDetalle();
        return view('reportes.rep_cartera_timing',compact('carterai'));
    }

    public function reporteCarteraRecupero(Request $request)
    {
        $carterai=Cartera::carteraDetalle();
        return view('reportes.rep_cartera_recupero',compact('carterai'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */









    /* ---------BUSCARRRRR-------------------VISTA GSTORRRRRRRRRRRRRRRR-------------------------------------- */
    public function verPagos(){
        $carteras=DB::select(DB::raw("
            select car_id,car_nom from creditoy_cobranzas.cartera
            where car_est=0 and car_pas=0 and car_id not in (23,57,86,74,73,91)
        "));

        $carterasPagos=DB::select(DB::raw("
            select car_id,car_nom, 'Sin Pagos a la Fecha' as fecha from creditoy_cobranzas.cartera
            where car_est=0 and car_pas=0 and car_id not in (23,57,86,74,73,84,85,62,63,81,80,9,91)
        "));

        $carterasCon=DB::select(DB::raw("
            select car_id,car_nom from creditoy_cobranzas.cartera
            where car_est=0 and car_pas=0 and car_id in (84,85,62,63,81,80,9)
        "));

        $pagos=DB::select(DB::raw("
            select car_id_FK as car_id, max(pag_cli_fec) as fecha 
            FROM pago_cliente_2
            where DATE_FORMAT(pag_cli_fec,'%Y-%m')=DATE_FORMAT(now(),'%Y-%m')
            GROUP BY car_id_FK
        "));

        return response()->json(['carteras' => $carteras, 'carterasPagos' => $carterasPagos, 'carterasCon' => $carterasCon, 'pagos' => $pagos]);
    }

    public function verCarteras(){
        $carteras=DB::select(DB::raw("
            select car_id,car_nom, 'sincargar' as estado   from creditoy_cobranzas.cartera
            where car_est=0 and car_pas=0 and car_id not in (23,57,86,74,73,91)
        "));

        $carterasCargadas=DB::select(DB::raw("
            select car_id_fk as car_id, 'cargadas' as estado from indicadores.cartera_detalle
            where DATE_FORMAT(fecha,'%Y-%m')=DATE_FORMAT(now(),'%Y-%m')
            group by car_id_fk
        "));

        

        return response()->json(['carteras' => $carteras,'carterasCargadas'=> $carterasCargadas]);
    }

    public function buscarGestorCarteraPago($car){
        $metas=DB::select(DB::raw("
            select month(fecha) as mes, meta,mes_nombre from indicadores.cartera
            where cartera_id_fk=$car
            and meta>0
            and year(fecha) = year(now())
            and month(fecha) BETWEEN month(now()) - 3 and month(now())
        "));

        $pagos=DB::select(DB::raw("
        select month(pag_cli_fec) m, (case
            WHEN month(pag_cli_fec)=1  THEN 'Enero'
            WHEN month(pag_cli_fec)=2 THEN  'Febrero'
            WHEN month(pag_cli_fec)=3 THEN 'Marzo' 
            WHEN month(pag_cli_fec)=4 THEN 'Abril' 
            WHEN month(pag_cli_fec)=5 THEN 'Mayo'
            WHEN month(pag_cli_fec)=6 THEN 'Junio'
            WHEN month(pag_cli_fec)=7 THEN 'Julio'
            WHEN month(pag_cli_fec)=8 THEN 'Agosto'
            WHEN month(pag_cli_fec)=9 THEN 'Septiembre'
            WHEN month(pag_cli_fec)=10 THEN 'Octubre'
            WHEN month(pag_cli_fec)=11 THEN 'Noviembre'
            WHEN month(pag_cli_fec)=12 THEN 'Diciembre'
            END) as mes,
            meta, 
            sum(pag_cli_mon) as recupero
            from pago_cliente_2 as p
            INNER JOIN indicadores.cartera as c
            on p.car_id_FK=c.cartera_id_fk
            where car_id_FK=$car
            and year(pag_cli_fec) = year(now())
            and day(pag_cli_fec) < day(now())
            and month(pag_cli_fec) BETWEEN month(now()) - 3 and month(now())
            and date_format(fecha,'%Y-%m')=date_format(pag_cli_fec,'%Y-%m')
            GROUP by month(pag_cli_fec),mes,meta
            order by month(pag_cli_fec)
        "));
        return response()->json(['metas' => $metas, 'pagos' => $pagos]);
        //dd($results1);
        //return $results1;
    }

    public function buscarGestorCarteraCON($car){
        $metas=DB::select(DB::raw("
            select month(fecha) as mes, meta,mes_nombre from indicadores.cartera
            where cartera_id_fk=$car
            and meta>0
            and year(fecha) = year(now())
            and month(fecha) BETWEEN month(now()) - 3 and month(now())
        "));
        $pagos=DB::select(DB::raw("
            select month(ges_cli_conf_fec) m, (case
                WHEN month(ges_cli_conf_fec)=1  THEN 'Enero'
                WHEN month(ges_cli_conf_fec)=2 THEN  'Febrero'
                WHEN month(ges_cli_conf_fec)=3 THEN 'Marzo' 
                WHEN month(ges_cli_conf_fec)=4 THEN 'Abril' 
                WHEN month(ges_cli_conf_fec)=5 THEN 'Mayo'
                WHEN month(ges_cli_conf_fec)=6 THEN 'Junio'
                WHEN month(ges_cli_conf_fec)=7 THEN 'Julio'
                WHEN month(ges_cli_conf_fec)=8 THEN 'Agosto'
                WHEN month(ges_cli_conf_fec)=9 THEN 'Septiembre'
                WHEN month(ges_cli_conf_fec)=10 THEN 'Octubre'
                WHEN month(ges_cli_conf_fec)=11 THEN 'Noviembre'
                WHEN month(ges_cli_conf_fec)=12 THEN 'Diciembre'
                END) as mes,
                meta, 
                sum(ges_cli_conf_can) as recupero
                from gestion_cliente as g
                INNER JOIN cliente as c
                on g.cli_id_FK=c.cli_id
                INNER JOIN indicadores.cartera as car
                on c.car_id_FK=car.cartera_id_fk
                where car_id_FK=$car
                and res_id_fk=2
                and year(ges_cli_conf_fec) = year(now())
                and day(ges_cli_conf_fec) < day(now())
                and month(ges_cli_conf_fec) BETWEEN month(now()) - 3 and month(now())
                and date_format(fecha,'%Y-%m')=date_format(ges_cli_conf_fec,'%Y-%m')
                GROUP by month(ges_cli_conf_fec),mes,meta
                order by month(ges_cli_conf_fec)
        "));
        //dd($results1);
        //return $results1;
        return response()->json(['metas' => $metas, 'pagos' => $pagos]);
    }

    public function buscarGestorCarteraPagoCierre($car){
        $metas=DB::select(DB::raw("
            select month(fecha) as mes, meta,mes_nombre from indicadores.cartera
            where cartera_id_fk=$car
            and meta>0
            and year(fecha) = year(now())
            and month(fecha) BETWEEN month(now()) - 3 and month(now())-1
        "));

        $pagos=DB::select(DB::raw("
        select month(pag_cli_fec) m, (case
            WHEN month(pag_cli_fec)=1  THEN 'Enero'
            WHEN month(pag_cli_fec)=2 THEN  'Febrero'
            WHEN month(pag_cli_fec)=3 THEN 'Marzo' 
            WHEN month(pag_cli_fec)=4 THEN 'Abril' 
            WHEN month(pag_cli_fec)=5 THEN 'Mayo'
            WHEN month(pag_cli_fec)=6 THEN 'Junio'
            WHEN month(pag_cli_fec)=7 THEN 'Julio'
            WHEN month(pag_cli_fec)=8 THEN 'Agosto'
            WHEN month(pag_cli_fec)=9 THEN 'Septiembre'
            WHEN month(pag_cli_fec)=10 THEN 'Octubre'
            WHEN month(pag_cli_fec)=11 THEN 'Noviembre'
            WHEN month(pag_cli_fec)=12 THEN 'Diciembre'
            END) as mes,
            meta, 
            sum(pag_cli_mon) as recupero
            from pago_cliente_2 as p
            INNER JOIN indicadores.cartera as c
            on p.car_id_FK=c.cartera_id_fk
            where car_id_FK=$car
            and year(pag_cli_fec) = year(now())
            and month(pag_cli_fec) BETWEEN month(now()) - 3 and month(now())-1
            and date_format(fecha,'%Y-%m')=date_format(pag_cli_fec,'%Y-%m')
            GROUP by month(pag_cli_fec),mes,meta
            order by month(pag_cli_fec)
        "));
        return response()->json(['metas' => $metas, 'pagos' => $pagos]);
        //dd($results1);
        //return $results1;
    }

    public function buscarGestorCarteraCONCierre($car){
        $metas=DB::select(DB::raw("
            select month(fecha) as mes, meta,mes_nombre from indicadores.cartera
            where cartera_id_fk=$car
            and meta>0
            and year(fecha) = year(now())
            and month(fecha) BETWEEN month(now()) - 3 and month(now())-1
        "));
        $pagos=DB::select(DB::raw("
            select month(ges_cli_conf_fec) m, (case
                WHEN month(ges_cli_conf_fec)=1  THEN 'Enero'
                WHEN month(ges_cli_conf_fec)=2 THEN  'Febrero'
                WHEN month(ges_cli_conf_fec)=3 THEN 'Marzo' 
                WHEN month(ges_cli_conf_fec)=4 THEN 'Abril' 
                WHEN month(ges_cli_conf_fec)=5 THEN 'Mayo'
                WHEN month(ges_cli_conf_fec)=6 THEN 'Junio'
                WHEN month(ges_cli_conf_fec)=7 THEN 'Julio'
                WHEN month(ges_cli_conf_fec)=8 THEN 'Agosto'
                WHEN month(ges_cli_conf_fec)=9 THEN 'Septiembre'
                WHEN month(ges_cli_conf_fec)=10 THEN 'Octubre'
                WHEN month(ges_cli_conf_fec)=11 THEN 'Noviembre'
                WHEN month(ges_cli_conf_fec)=12 THEN 'Diciembre'
                END) as mes,
                meta, 
                sum(ges_cli_conf_can) as recupero
                from gestion_cliente as g
                INNER JOIN cliente as c
                on g.cli_id_FK=c.cli_id
                INNER JOIN indicadores.cartera as car
                on c.car_id_FK=car.cartera_id_fk
                where car_id_FK=$car
                and res_id_fk=2
                and year(ges_cli_conf_fec) = year(now())
                and month(ges_cli_conf_fec) BETWEEN month(now()) - 3 and month(now())-1
                and date_format(fecha,'%Y-%m')=date_format(ges_cli_conf_fec,'%Y-%m')
                GROUP by month(ges_cli_conf_fec),mes,meta
                order by month(ges_cli_conf_fec)
        "));
        //dd($results1);
        //return $results1;
        return response()->json(['metas' => $metas, 'pagos' => $pagos]);
    }
    
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
    public function buscarCarteraTimingP($car)
    {
        $totales=DB::select(DB::raw("
            SELECT date(fecha) as fec,meta 
            from indicadores.cartera
            WHERE DATE_FORMAT(fecha,'%Y-%m')=DATE_FORMAT(now(),'%Y-%m')
            and cartera_id_fk=$car
        "));
        $datos=DB::select(DB::raw("
            SELECT date(pag_cli_fec) as fec, SUM(pag_cli_mon) as pagos 
            from creditoy_cobranzas.pago_cliente_2
            WHERE car_id_FK=$car
            AND DATE_FORMAT(pag_cli_fec,'%Y-%m')=DATE_FORMAT(now(),'%Y-%m')
            group by date(pag_cli_fec)
        "));
        $porcentajes=DB::select(DB::raw("
            SELECT fecha_t as fec,porcentaje_ideal as porce from indicadores.timing
            WHERE	DATE_FORMAT(fecha_t,'%Y-%m')=DATE_FORMAT(now(),'%Y-%m')
        "));
        return response()->json(['totales' => $totales, 'datos' => $datos, 'porcentajes' => $porcentajes]);
    }

    public function buscarCarteraTimingC($car)
    {
        $totales=DB::select(DB::raw("
            SELECT date(fecha) as fec,meta 
            from indicadores.cartera
            WHERE DATE_FORMAT(fecha,'%Y-%m')=DATE_FORMAT(now(),'%Y-%m')
            and cartera_id_fk=$car
        "));
        $datos=DB::select(DB::raw("
            SELECT date(ges_cli_conf_fec) as fec, SUM(ges_cli_conf_can) as pagos 
            from creditoy_cobranzas.gestion_cliente as g
            INNER JOIN cliente as c on g.cli_id_FK=c.cli_id
            WHERE car_id_FK=$car
            AND DATE_FORMAT(ges_cli_conf_fec,'%Y-%m')=DATE_FORMAT(now(),'%Y-%m')
            group by date(ges_cli_conf_fec)
        "));
        $porcentajes=DB::select(DB::raw("
            SELECT fecha_t as fec,porcentaje_ideal as porce from indicadores.timing
            WHERE	DATE_FORMAT(fecha_t,'%Y-%m')=DATE_FORMAT(now(),'%Y-%m')
        "));
        return response()->json(['totales' => $totales, 'datos' => $datos, 'porcentajes' => $porcentajes]);
    }

    public function buscarCarteraRecuperoP($car, $fec_i, $fec_f)
    {
        $fi=date('Y-m', strtotime($fec_i));
        $datos=DB::select(DB::raw("
                SELECT  meta,  recupero, if((recupero/meta)>0,(recupero/meta)*100,0) as alcance
                FROM
                (select meta,
                            (select 
                            if(sum(pag_cli_mon)>0,sum(pag_cli_mon),0) as recu
                            from pago_cliente_2 as p
                            INNER JOIN indicadores.cartera as c
                            on p.car_id_FK=c.cartera_id_fk
                            INNER JOIN indicadores.cartera_detalle as cde
                            on p.pag_cli_cod=cde.cuenta
                            where cde.car_id_fk=$car and p.car_id_FK=$car and cartera_id_fk=$car
                            and date(pag_cli_fec) BETWEEN '$fec_i' and '$fec_f'
                            and date_format(c.fecha,'%Y-%m')= '$fi'
                            and date_format(cde.fecha,'%Y-%m')= '$fi'
                            and date_format(pag_cli_fec,'%Y-%m')= '$fi'
                            ) as recupero
                from indicadores.cartera 
                WHERE cartera_id_fk=$car AND date_format(fecha,'%Y-%m')= '$fi')b
        "));
        return $datos;
    }

    public function buscarCarteraRecuperoC($car, $fec_i, $fec_f)
    {
        $fi=date('Y-m', strtotime($fec_i));
        $datos=DB::select(DB::raw("
                select meta,  recupero, if((recupero/meta)>0,(recupero/meta)*100,0) as alcance
                from
                (select meta,
                            (select  
                            if(sum(ges_cli_conf_can)>0,sum(ges_cli_conf_can),0) as recu
                            from gestion_cliente as g
                            INNER JOIN cliente as c
                            on g.cli_id_FK=c.cli_id
                            INNER JOIN indicadores.cartera as cd
                            on c.car_id_FK=cd.cartera_id_fk
                            INNER JOIN indicadores.cartera_detalle as cde
                            on c.cli_cod=cde.cuenta
                            where cde.car_id_fk=$car and c.car_id_FK=$car and cartera_id_fk=$car
                            and date(ges_cli_conf_fec) BETWEEN '$fec_i' and '$fec_f'
                            and date_format(cd.fecha,'%Y-%m')= '$fi'
                            and date_format(cde.fecha,'%Y-%m')= '$fi'
                            and date_format(ges_cli_conf_fec,'%Y-%m')= '$fi'
                            and res_id_fk=2
                            ) as recupero
                from indicadores.cartera 
                WHERE cartera_id_fk=$car AND date_format(fecha,'%Y-%m')= '$fi'
                ) a
        "));
        return $datos;
    }


    public function buscarCartera($car, $tip,$mes)
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
        ->where(DB::raw('(date_format(fecha,"%Y-%m"))'),'=',date("Y-m", strtotime($mes)))
        //->where(DB::raw('(date_format(fecha,"%Y-%m"))'),'=','2020-04')
        ->groupBy($tip)
        ->get();
        //return Response::json($results);
        //dd($results3);
        return $results3;
    }
    public function buscarCarteraContactos($car,$ubi, $tip,$mes)
    {
        /*$sq=DB::connection('mysql2')
        ->table('gestion_cliente as g')
        ->rightjoin('cliente as c','g.cli_id_FK','=','c.cli_id')
        ->join('indicadores.cartera_detalle as cd','c.cli_cod','=','cd.cuenta')
        ->select(DB::raw('cli_cod as clientes'),'tramo','dep','score','entidades','dep_ind','prioridad',
                DB::raw('max( date_format( ges_cli_fec, "%Y-%m-%d" ) ) as ult_gestion'),
                DB::raw('(CASE 
                        WHEN res_id_FK IN ( 38, 6, 22, 41 ) THEN "cfrn"
                        WHEN res_id_FK IN ( 2, 37, 33, 18, 10, 1, 8, 43, 39, 7 ) THEN "contacto"
                        WHEN res_id_FK IN ( 34, 17, 21 ) THEN "nodisponible"
                        WHEN res_id_FK IN ( 19, 27, 12, 26, 13 ) THEN "inubicable"
                        WHEN res_id_FK IN ( 4,45,44, 25,32 ) THEN "nocontacto"
                        ELSE "NO ENCONTRADO"
                    END) AS ubic'),'capital','saldo_deuda','monto_camp')
        ->where('cd.car_id_fk','=',$car)
        ->where(DB::raw('(date_format(ges_cli_fec,"%Y-%m"))'),'=',date("Y-m", strtotime($mes)))
        ->where(DB::raw('(date_format(fecha,"%Y-%m"))'),'=',date("Y-m", strtotime($mes)))
        ->groupBy('cli_cod');*/

        /*$sq2=DB::connection('mysql2')
        ->table(DB::raw(' ( ' . $sq->toSql() . ' ) as t '))
        ->mergeBindings( $sq )
        ->select('ubic',DB::raw($tip.' as tipo'),
                DB::raw('COUNT(clientes) as cantidad'),
                DB::raw('sum(t.capital) as total_capital'),
                DB::raw('sum(t.saldo_deuda) as total_deuda'),DB::raw('sum(t.monto_camp) as total_importe'))
        ->where('ubic','=',$ubi)
        ->groupBy($tip)
        ->get();*/
        //dd($sq2);
        $sq=DB::select(DB::raw("
                        select $tip as tipo, COUNT(clientes) as cantidad,
                        sum(capital) as total_capital,
                        sum(saldo_deuda) as total_deuda,
                        sum(monto_camp) as total_importe
                        from
                        (SELECT
                                    cli_cod as clientes,tramo,dep,score,entidades,dep_ind,prioridad,
                                    max( date_format( ges_cli_fec, '%Y-%m-%d' ) ) ult_gestion,
                                    (CASE 
                                        WHEN res_id_FK IN ( 38, 6, 22, 41 ) THEN 'cfrn'
                                        WHEN res_id_FK IN ( 2,37,33,10,1,8,43,39,7,3,5,9,34,17,21,18,28,30,35,36,46,47,48,49 ) THEN 'contacto'
                                        WHEN res_id_FK IN ( 32 ) THEN 'nodisponible'
                                        WHEN res_id_FK IN ( 19,27,12,26,13,4,11,12,20,14,15,16,23,24,29,31 ) THEN 'inubicable'
                                        WHEN res_id_FK IN ( 45,44, 25 ) THEN 'nocontacto'
                                        ELSE 'NO ENCONTRADO'
                                    END) AS ubic,
                                    capital,saldo_deuda,monto_camp
                                FROM
                                    cliente AS c
                                    INNER JOIN indicadores.cartera_detalle AS cd ON c.cli_cod = cd.cuenta 
                                    INNER JOIN(select max(ges_cli_id) as maxid,cli_id_FK from gestion_cliente where date_format( ges_cli_fec, '%Y-%m' ) = '$mes' GROUP BY cli_id_FK) as g
                                    on g.cli_id_FK=c.cli_id
                                    inner join gestion_cliente as gg on gg.ges_cli_id=g.maxid
                                WHERE
                                    c.car_id_FK =$car
                                    AND cli_est =0 and cli_pas=0
                                    AND date_format( ges_cli_fec, '%Y-%m' ) = '$mes'
                                    AND date_format( fecha, '%Y-%m' ) = '$mes'		
                                GROUP BY	
                                    cli_cod
                        ) a
                        WHERE ubic='$ubi'
                        group by tipo
        "));

        return $sq;
    }
    public function buscarCarteraClientesSinGestion($car,$tip,$mes)
    {
        /*$sq=DB::connection('mysql2')
        ->table('gestion_cliente')
        ->select(DB::raw('count(*)'))
        ->where('cli_id_FK','=','cli_id')
        ->where(DB::raw('(date_format(ges_cli_fec,"%Y-%m"))'),'=',date("Y-m", strtotime($mes)));

        $sq1=DB::connection('mysql2')
        ->table('cliente')
        ->select('cli_cod')
        ->where('car_id_FK','=',$car)
        ->where('cli_est','=',0)
        ->where('cli_pas','=',0)
        ->where(DB::raw(' ( ' . $sq->toSql() . ' )'),'=',0)
        ->mergeBindings( $sq )
        ->groupBy('cli_cod');

        $sq2=DB::connection('mysql2')
        ->table(DB::raw(' ( ' . $sq1->toSql() . ' ) as a'))
        ->mergeBindings( $sq1 )
        ->join('indicadores.cartera_detalle as cd','a.cli_cod','=','cd.cuenta')
        ->select(DB::raw('count(cuenta) as clientes'),'tramo','dep','score','entidades','dep_ind','prioridad',
                'capital','saldo_deuda','monto_camp')
        ->where('cd.car_id_fk','=',$car)
        ->where(DB::raw('(date_format(fecha,"%Y-%m"))'),'=',date("Y-m", strtotime($mes)))
        ->groupBy('cuenta');

        $sq3=DB::connection('mysql2')
        ->table(DB::raw(' ( ' . $sq2->toSql() . ' ) as t '))
        ->mergeBindings( $sq2 )
        ->select(DB::raw($tip.' as tipo'),
                DB::raw('count(clientes) as cantidad'),
                DB::raw('sum(t.capital) as total_capital'),
                DB::raw('sum(t.saldo_deuda) as total_deuda'),DB::raw('sum(t.monto_camp) as total_importe'))
        ->groupBy($tip)
        ->get();
        dd($sq3);*/
        //return $sq3;
        $sq=DB::select(DB::raw("
                    select $tip as tipo, count(clientes) as cantidad,
                    sum(capital) as total_capital,
                    sum(saldo_deuda) as total_deuda,
                    sum(monto_camp) as total_importe
                    from
                    (SELECT	count(cuenta) clientes,tramo,dep,score,entidades,dep_ind,prioridad,
                                capital,saldo_deuda,monto_camp
                    FROM
                        (SELECT 
                                cli_cod
                            FROM
                                cliente
                            WHERE
                                car_id_FK =$car
                                AND cli_est =0 and cli_pas=0
                                and (SELECT count(*) FROM gestion_cliente WHERE cli_id_FK=cli_id and DATE_FORMAT(ges_cli_fec,'%Y-%m')='$mes')=0
                                GROUP BY	
                                cli_cod) a
                    INNER JOIN indicadores.cartera_detalle AS cd ON a.cli_cod = cd.cuenta 
                    WHERE cd.car_id_fk =$car
                    AND date_format( fecha, '%Y-%m' ) ='$mes'
                    GROUP BY cuenta) b
                    group by tipo
        "));
        //dd($sq);
        return $sq;
    }

    public static function buscarCarteraGestion($car, $tip, $fec_i, $fec_f){

        $sq=DB::connection('mysql2')
        ->table('gestion_cliente as g')
        ->rightjoin('cliente as c','g.cli_id_FK','=','c.cli_id')
        ->join('indicadores.cartera_detalle as cd','c.cli_cod','=','cd.cuenta')
        ->select('cli_cod',DB::raw($tip.' as tipo'),DB::raw('date_format( ges_cli_fec, "%Y-%m-%d") as fecha_gestion'),
                DB::raw('count(cartera) as cant_gestion'),
                'capital','saldo_deuda','monto_camp')
        ->where('cd.car_id_fk','=',$car)
        ->whereBetween(DB::raw('(date_format(ges_cli_fec,"%Y-%m-%d"))'),array($fec_i,$fec_f))
        ->where(DB::raw('(date_format(fecha,"%Y-%m"))'),'=',date("Y-m", strtotime($fec_i)))
        ->groupBy('cli_cod');

        $sq2=DB::connection('mysql2')
        ->table(DB::raw(' ( ' . $sq->toSql() . ' ) as t '))
        ->mergeBindings( $sq )
        ->join('indicadores.cartera_detalle as cde','t.cli_cod','=','cde.cuenta')
        ->select(DB::raw('tipo'),
                DB::raw('count(cli_cod) as cant_clientes'),
                DB::raw('count(cant_gestion) as cant_gestion'),
                DB::raw('sum(t.capital) as total_capital'),
                DB::raw('sum(t.saldo_deuda) as total_deuda'),DB::raw('sum(t.monto_camp) as total_importe'))
        ->where('cde.car_id_fk','=',$car)
        ->whereBetween(DB::raw('(date_format(fecha_gestion,"%Y-%m-%d"))'),array($fec_i,$fec_f))
        ->where(DB::raw('(date_format(fecha,"%Y-%m"))'),'=',date("Y-m", strtotime($fec_i)))
        ->groupBy('tipo')
        ->get();
        return $sq2;

        /*$gestion=DB::connection('mysql2')
        ->table('gestion_cliente as g')
        ->join('cliente as c','g.cli_id_FK','=','c.cli_id')
        //->join('cartera as car','c.car_id_FK','=','car.car_id')
        ->join('indicadores.cartera_detalle as cd','c.cli_cod','=','cd.cuenta')
        ->select(DB::raw($tip.' as tipo'),DB::raw('count(cartera) as cant_gestion'),
                DB::raw('count(distinct(cli_cod)) as cant_clientes'),DB::raw('sum(distinct(capital)) as total_capital'),
                DB::raw('sum(distinct(saldo_deuda)) as total_deuda'),DB::raw('sum(distinct(monto_camp)) as total_importe'))
        ->where('cd.car_id_fk','=',$car)
        ->whereBetween(DB::raw('(date_format(ges_cli_fec,"%Y-%m-%d"))'),array($fec_i,$fec_f))
        ->where(DB::raw('(date_format(fecha,"%Y-%m"))'),'=',date("Y-m", strtotime($fec_i)))
        //->where(DB::raw('(date_format(fecha,"%Y-%m"))'),'=','2020-04')
        ->groupBy($tip)
        ->get();
        return $gestion;*/

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
        $sq=DB::connection('mysql2')
        ->table('gestion_cliente as g')
        ->rightjoin('cliente as c','g.cli_id_FK','=','c.cli_id')
        ->join('indicadores.cartera_detalle as cd','c.cli_cod','=','cd.cuenta')
        ->select('cli_cod',DB::raw($tip.' as tipo'),'ges_cli_com_can',
                DB::raw('date_format( ges_cli_fec, "%Y-%m-%d") as fecha_gestion'),
                DB::raw('count(cartera) as cantidad'),
                'capital','saldo_deuda','monto_camp')
        ->where('cd.car_id_fk','=',$car)
        ->whereIn('res_id_fk',array('1','43'))
        ->whereBetween(DB::raw('(date_format(ges_cli_fec,"%Y-%m-%d"))'),array($fec_i,$fec_f))
        ->where(DB::raw('(date_format(fecha,"%Y-%m"))'),'=',date("Y-m", strtotime($fec_i)))
        ->groupBy('cli_cod');

        $sq2=DB::connection('mysql2')
        ->table(DB::raw(' ( ' . $sq->toSql() . ' ) as t '))
        ->mergeBindings( $sq )
        ->join('indicadores.cartera_detalle as cde','t.cli_cod','=','cde.cuenta')
        ->select(DB::raw('tipo'),
                DB::raw('sum(t.ges_cli_com_can) as monto'),
                DB::raw('count(cli_cod) as cant_clientes'),
                DB::raw('count(cantidad) as cantidad'),
                DB::raw('sum(t.capital) as total_capital'),
                DB::raw('sum(t.saldo_deuda) as total_deuda'),DB::raw('sum(t.monto_camp) as total_importe'))
        ->where('cde.car_id_fk','=',$car)
        ->whereBetween(DB::raw('(date_format(fecha_gestion,"%Y-%m-%d"))'),array($fec_i,$fec_f))
        ->where(DB::raw('(date_format(fecha,"%Y-%m"))'),'=',date("Y-m", strtotime($fec_i)))
        ->groupBy('tipo')
        ->get();
        return $sq2;

        /*$gestion=DB::connection('mysql2')
        ->table('gestion_cliente as g')
        ->rightjoin('cliente as c','g.cli_id_FK','=','c.cli_id')
        ->join('cartera as car','c.car_id_FK','=','car.car_id')
        ->join('indicadores.cartera_detalle as cd','c.cli_cod','=','cd.cuenta')
        ->select(DB::raw($tip.' as tipo'),DB::raw('count(cartera) as cantidad'),
                DB::raw('sum(ges_cli_com_can) as monto'),
                DB::raw('count(distinct(cuenta)) as cant_clientes'),DB::raw('sum(capital) as total_capital'),
                DB::raw('sum(saldo_deuda) as total_deuda'),DB::raw('sum(monto_camp) as total_importe'))
        ->where('cd.car_id_fk','=',$car)
        ->whereIn('res_id_fk',array('1','43'))
        ->whereBetween(DB::raw('(date_format(ges_cli_fec,"%Y-%m-%d"))'),array($fec_i,$fec_f))
        //->where(DB::raw('(date_format(fecha,"%Y-%m"))'),'=','2020-04')
        ->where(DB::raw('(date_format(fecha,"%Y-%m"))'),'=',date("Y-m", strtotime($fec_i)))
        ->groupBy($tip)
        ->get();
        return $gestion;*/
        //dd($gestion);

    }

    public static function buscarCarteraGestionCON($car, $tip, $fec_i, $fec_f){

        $sq=DB::connection('mysql2')
        ->table('gestion_cliente as g')
        ->rightjoin('cliente as c','g.cli_id_FK','=','c.cli_id')
        ->join('indicadores.cartera_detalle as cd','c.cli_cod','=','cd.cuenta')
        ->select('cli_cod',DB::raw($tip.' as tipo'),'ges_cli_conf_can',
                DB::raw('date_format( ges_cli_conf_fec, "%Y-%m-%d") as fecha_gestion'),
                DB::raw('count(ges_cli_conf_can) as cantidad'),
                'capital','saldo_deuda','monto_camp')
        ->where('cd.car_id_fk','=',$car)
        ->whereIn('res_id_fk',array('2'))
        ->whereBetween(DB::raw('(date_format(ges_cli_conf_fec,"%Y-%m-%d"))'),array($fec_i,$fec_f))
        ->where(DB::raw('(date_format(fecha,"%Y-%m"))'),'=',date("Y-m", strtotime($fec_i)))
        ->groupBy('cli_cod');
        //->groupBy('ges_cli_conf_can');

        $sq2=DB::connection('mysql2')
        ->table(DB::raw(' ( ' . $sq->toSql() . ' ) as t '))
        ->mergeBindings( $sq )
        ->join('indicadores.cartera_detalle as cde','t.cli_cod','=','cde.cuenta')
        ->select(DB::raw('tipo'),
                DB::raw('sum(t.ges_cli_conf_can) as monto'),
                DB::raw('count(cli_cod) as cant_clientes'),
                DB::raw('count(cantidad) as cantidad'),
                DB::raw('sum(t.capital) as total_capital'),
                DB::raw('sum(t.saldo_deuda) as total_deuda'),DB::raw('sum(t.monto_camp) as total_importe'))
        ->where('cde.car_id_fk','=',$car)
        ->whereBetween(DB::raw('(date_format(fecha_gestion,"%Y-%m-%d"))'),array($fec_i,$fec_f))
        ->where(DB::raw('(date_format(fecha,"%Y-%m"))'),'=',date("Y-m", strtotime($fec_i)))
        ->groupBy('tipo')
        ->get();
        return $sq2;

        /*$gestion=DB::connection('mysql2')
        ->table('gestion_cliente as g')
        ->rightjoin('cliente as c','g.cli_id_FK','=','c.cli_id')
        ->join('cartera as car','c.car_id_FK','=','car.car_id')
        ->join('indicadores.cartera_detalle as cd','c.cli_cod','=','cd.cuenta')
        ->select(DB::raw($tip.' as tipo'),DB::raw('count(ges_cli_conf_can) as cantidad'),
                DB::raw('sum(ges_cli_conf_can) as monto'),
                DB::raw('count(distinct(cuenta)) as cant_clientes'),DB::raw('sum(capital) as total_capital'),
                DB::raw('sum(saldo_deuda) as total_deuda'),DB::raw('sum(monto_camp) as total_importe'))
        ->where('cd.car_id_fk','=',$car)
        ->whereIn('res_id_fk',array('2'))
        ->whereBetween(DB::raw('(date_format(ges_cli_conf_fec,"%Y-%m-%d"))'),array($fec_i,$fec_f))
        ->where(DB::raw('(date_format(fecha,"%Y-%m"))'),'=',date("Y-m", strtotime($fec_i))) 
        ->groupBy($tip)
        //->groupBy('ges_cli_conf_can')
        ->get();
        return $gestion;*/
        //dd($gestion);

    }

    /*-----------------------ubicabilidad----------------------------------------*/
    /*-----------------------------------------------------------------------------*/
    public function buscarCarteraUbic($car,$fec_i,$fec_f){
        /*$sq=DB::connection('mysql2')
        ->table('gestion_cliente as g')
        ->rightjoin('cliente as c','g.cli_id_FK','=','c.cli_id')
        ->join('indicadores.cartera_detalle as cd','c.cli_cod','=','cd.cuenta')
        ->select('cli_cod',DB::raw('max( date_format( ges_cli_fec, "%Y-%m-%d" ) ) as ult_gestion'),
                DB::raw('count(cli_cod) as cant_gestion'),
                DB::raw('(CASE 
                    WHEN res_id_FK IN ( 38, 6, 22, 41 ) THEN "CRFN"
                    WHEN res_id_FK IN ( 2, 37, 33, 10, 1, 8, 43, 39, 7, 3, 5, 9, 34, 17, 21, 18, 28, 30, 35, 36, 46, 47, 48, 49 ) THEN "CONTACTO"
                    WHEN res_id_FK IN ( 19, 27, 12, 26, 13, 4, 11, 12, 20, 14, 15, 16, 23, 24, 29, 31 ) THEN "ILOCALIZADO"
                    WHEN res_id_FK IN ( 45, 25, 44 ) THEN "NO CONTACTO"
                    WHEN res_id_FK IN ( 32 ) THEN "NO DISPONIBLE"
                    ELSE "NO ENCONTRADO"
                    END) AS ubic'),
                DB::raw('count(distinct(cuenta)) as cant_clientes'),'capital','saldo_deuda','monto_camp')
        ->where('cd.car_id_fk','=',$car)
        ->whereBetween(DB::raw('(date_format(ges_cli_fec,"%Y-%m-%d"))'),array($fec_i,$fec_f))
        ->where(DB::raw('(date_format(fecha,"%Y-%m"))'),'=',date("Y-m", strtotime($fec_i)))
        ->groupBy('cli_cod');*/
        //->groupBy('ubic');
        //->get();
        /**WHEN res_id_FK IN ( 38, 6, 22, 41 ) THEN 'cfrn'
            WHEN res_id_FK IN ( 2,37,33,10,1,8,43,39,7,3,5,9,34,17,21,18,28,30,35,36,46,47,48,49 ) THEN 'contacto'
            WHEN res_id_FK IN ( 32 ) THEN 'nodisponible'
            WHEN res_id_FK IN ( 19,27,12,26,13,4,11,12,20,14,15,16,23,24,29,31 ) THEN 'inubicable'
            WHEN res_id_FK IN ( 45,44, 25 ) THEN 'nocontacto'
            ELSE 'NO ENCONTRADO' */
        $sq=DB::connection('mysql2')
        ->table('cliente as c')
        ->join('indicadores.cartera_detalle as cd','c.cli_cod','=','cd.cuenta')
        ->join(DB::raw('(select max(ges_cli_id) as maxid,cli_id_FK from gestion_cliente where date(ges_cli_fec) between "'.$fec_i .'" and "'. $fec_f.'" GROUP BY cli_id_FK) as g'),'g.cli_id_FK','=','c.cli_id')
        ->join('gestion_cliente as gg','gg.ges_cli_id','=','g.maxid')
        ->select('cli_cod',DB::raw(' date_format( ges_cli_fec, "%Y-%m-%d" ) as ult_gestion'),
                DB::raw('count(cli_cod) as cant_gestion'),
                DB::raw('(CASE 
                        WHEN res_id_FK IN ( 38, 6, 22, 41 ) THEN "CRFN"
                        WHEN res_id_FK IN ( 2,37,33,10,1,8,43,39,7,3,5,9,34,17,21,18,28,30,35,36,46,47,48,49 ) THEN "CONTACTO"
                        WHEN res_id_FK IN ( 32 ) THEN "NO DISPONIBLE"
                        WHEN res_id_FK IN ( 19,27,12,26,13,4,11,12,20,14,15,16,23,24,29,31 ) THEN "ILOCALIZADO"
                        WHEN res_id_FK IN ( 45,44, 25 ) THEN "NO CONTACTO"
                        ELSE "NO ENCONTRADO"
                    END) AS ubic'),
                DB::raw('count(distinct(cuenta)) as cant_clientes'),'capital','saldo_deuda','monto_camp')
        ->where('cd.car_id_fk','=',$car)
        ->whereBetween(DB::raw('(date_format(ges_cli_fec,"%Y-%m-%d"))'),array($fec_i,$fec_f))
        ->where(DB::raw('(date_format(fecha,"%Y-%m"))'),'=',date("Y-m", strtotime($fec_i)))
        ->groupBy('cli_cod');

        $sq2=DB::connection('mysql2')
        ->table(DB::raw(' ( ' . $sq->toSql() . ' ) as t '))
        ->mergeBindings( $sq )
        ->join('indicadores.cartera_detalle as cde','t.cli_cod','=','cde.cuenta')
        ->select(DB::raw('ubic as tipo'),
                DB::raw('count(ubic) as cant_clientes'),
                DB::raw('count(cant_gestion) as cant_gestion'),
                DB::raw('sum(t.capital) as total_capital'),
                DB::raw('sum(t.saldo_deuda) as total_deuda'),DB::raw('sum(t.monto_camp) as total_importe'))
        ->where('cde.car_id_fk','=',$car)
        ->whereBetween(DB::raw('(date_format(ult_gestion,"%Y-%m-%d"))'),array($fec_i,$fec_f))
        ->where(DB::raw('(date_format(fecha,"%Y-%m"))'),'=',date("Y-m", strtotime($fec_i)))
        ->groupBy('ubic')
        ->get();
        return $sq2;
        //dd($sq2);
    }

    public function buscarCarteraUbicPDP($car,$fec_i,$fec_f){
        /**WHEN res_id_FK IN ( 38, 6, 22, 41 ) THEN 'cfrn'
            WHEN res_id_FK IN ( 2,37,33,10,1,8,43,39,7,3,5,9,34,17,21,18,28,30,35,36,46,47,48,49 ) THEN 'contacto'
            WHEN res_id_FK IN ( 32 ) THEN 'nodisponible'
            WHEN res_id_FK IN ( 19,27,12,26,13,4,11,12,20,14,15,16,23,24,29,31 ) THEN 'inubicable'
            WHEN res_id_FK IN ( 45,44, 25 ) THEN 'nocontacto'
            ELSE 'NO ENCONTRADO' */
        $sq=DB::connection('mysql2')
        ->table('gestion_cliente as g')
        ->rightjoin('cliente as c','g.cli_id_FK','=','c.cli_id')
        ->join('indicadores.cartera_detalle as cd','c.cli_cod','=','cd.cuenta')
        ->select('cli_cod','ges_cli_com_can',DB::raw('max( date_format( ges_cli_fec, "%Y-%m-%d" ) ) as ult_gestion'),
                DB::raw('count(cli_cod) as cant_gestion'),
                DB::raw('(CASE 
                        WHEN res_id_FK IN ( 38, 6, 22, 41 ) THEN "CRFN"
                        WHEN res_id_FK IN ( 2,37,33,10,1,8,43,39,7,3,5,9,34,17,21,18,28,30,35,36,46,47,48,49 ) THEN "CONTACTO"
                        WHEN res_id_FK IN ( 32) THEN "NO DISPONIBLE"
                        WHEN res_id_FK IN ( 19,27,12,26,13,4,11,12,20,14,15,16,23,24,29,31 ) THEN "ILOCALIZADO"
                        WHEN res_id_FK IN ( 45,44, 25 ) THEN "NO CONTACTO"
                        ELSE "NO ENCONTRADO"
                    END) AS ubic'),
                'capital','saldo_deuda','monto_camp')
        ->where('cd.car_id_fk','=',$car)
        ->whereIn('res_id_fk',array('1','43'))
        ->whereBetween(DB::raw('(date_format(ges_cli_fec,"%Y-%m-%d"))'),array($fec_i,$fec_f))
        ->where(DB::raw('(date_format(fecha,"%Y-%m"))'),'=',date("Y-m", strtotime($fec_i)))
        ->groupBy('cli_cod');
        //->groupBy('ubic');
        //->get();

        $sq2=DB::connection('mysql2')
        ->table(DB::raw(' ( ' . $sq->toSql() . ' ) as t '))
        ->mergeBindings( $sq )
        ->join('indicadores.cartera_detalle as cde','t.cli_cod','=','cde.cuenta')
        ->select(DB::raw('ubic as tipo'),
                DB::raw('count(ges_cli_com_can) as cantidad'),
                DB::raw('count(distinct(cli_cod)) as cant_clientes'),
                DB::raw('sum(ges_cli_com_can) as monto'),
                DB::raw('sum(t.capital) as total_capital'),
                DB::raw('sum(t.saldo_deuda) as total_deuda'),DB::raw('sum(t.monto_camp) as total_importe'))
        ->where('cde.car_id_fk','=',$car)
        ->whereBetween(DB::raw('(date_format(ult_gestion,"%Y-%m-%d"))'),array($fec_i,$fec_f))
        ->where(DB::raw('(date_format(fecha,"%Y-%m"))'),'=',date("Y-m", strtotime($fec_i)))
        ->groupBy('ubic')
        ->get();
        return $sq2;
        //dd($sq2);
    }

    public function buscarCarteraUbicCON($car,$fec_i,$fec_f){
        /**WHEN res_id_FK IN ( 38, 6, 22, 41 ) THEN 'cfrn'
            WHEN res_id_FK IN ( 2,37,33,10,1,8,43,39,7,3,5,9,34,17,21,18,28,30,35,36,46,47,48,49 ) THEN 'contacto'
            WHEN res_id_FK IN ( 32 ) THEN 'nodisponible'
            WHEN res_id_FK IN ( 19,27,12,26,13,4,11,12,20,14,15,16,23,24,29,31 ) THEN 'inubicable'
            WHEN res_id_FK IN ( 45,44, 25 ) THEN 'nocontacto'
            ELSE 'NO ENCONTRADO' */
        $sq=DB::connection('mysql2')
        ->table('gestion_cliente as g')
        ->rightjoin('cliente as c','g.cli_id_FK','=','c.cli_id')
        ->join('indicadores.cartera_detalle as cd','c.cli_cod','=','cd.cuenta')
        ->select('cli_cod','ges_cli_conf_can',DB::raw('max( date_format( ges_cli_fec, "%Y-%m-%d" ) ) as ult_gestion'),
                DB::raw('count(cli_cod) as cant_gestion'),
                DB::raw('(CASE 
                        WHEN res_id_FK IN ( 38, 6, 22, 41 ) THEN "CRFN"
                        WHEN res_id_FK IN ( 2,37,33,10,1,8,43,39,7,3,5,9,34,17,21,18,28,30,35,36,46,47,48,49 ) THEN "CONTACTO"
                        WHEN res_id_FK IN ( 32 ) THEN "NO DISPONIBLE"
                        WHEN res_id_FK IN ( 19,27,12,26,13,4,11,12,20,14,15,16,23,24,29,31 ) THEN "ILOCALIZADO"
                        WHEN res_id_FK IN ( 45,44, 25 ) THEN "NO CONTACTO"
                        ELSE "NO ENCONTRADO"
                    END) AS ubic'),
                'capital','saldo_deuda','monto_camp')
        ->where('cd.car_id_fk','=',$car)
        ->whereIn('res_id_fk',array('2'))
        ->whereBetween(DB::raw('(date_format(ges_cli_fec,"%Y-%m-%d"))'),array($fec_i,$fec_f))
        ->where(DB::raw('(date_format(fecha,"%Y-%m"))'),'=',date("Y-m", strtotime($fec_i)))
        ->groupBy('cli_cod');
        //->groupBy('ubic');
        //->get();

        $sq2=DB::connection('mysql2')
        ->table(DB::raw(' ( ' . $sq->toSql() . ' ) as t '))
        ->mergeBindings( $sq )
        ->join('indicadores.cartera_detalle as cde','t.cli_cod','=','cde.cuenta')
        ->select(DB::raw('ubic as tipo'),
                DB::raw('count(ges_cli_conf_can) as cantidad'),
                DB::raw('count(distinct(cli_cod)) as cant_clientes'),
                DB::raw('sum(ges_cli_conf_can) as monto'),
                DB::raw('sum(t.capital) as total_capital'),
                DB::raw('sum(t.saldo_deuda) as total_deuda'),DB::raw('sum(t.monto_camp) as total_importe'))
        ->where('cde.car_id_fk','=',$car)
        ->whereBetween(DB::raw('(date_format(ult_gestion,"%Y-%m-%d"))'),array($fec_i,$fec_f))
        ->where(DB::raw('(date_format(fecha,"%Y-%m"))'),'=',date("Y-m", strtotime($fec_i)))
        ->groupBy('ubic')
        ->get();
        return $sq2;
        //dd($sq2);
    }

    public function buscarCarteraUbicPagos($car,$fec_i,$fec_f){
        $sql="
            select * 
            FROM
            (
            select 
                ubic as tipo,
                count(cartera) as clientes,
                sum(capital) as capital,
                sum(saldo_deuda) as deuda,
                sum(monto_camp) as importe,
                sum(cliente_pago) as clientes_pagos,
                sum(capital_pago) as capital_pagos,
                sum(importe_pago) as importe_pagos,
                sum(monto_pagos) as monto_pagos,
                (sum(cliente_pago)/count(cartera))*100 as cobertura,
                (sum(monto_pagos)/sum(monto_camp))*100 as recupero
            FROM
            (
                    SELECT
                    (CASE 
                        WHEN res_id_FK IN ( 38, 6, 22, 41 ) THEN 'CRFN'
                        WHEN res_id_FK IN ( 2, 37, 33, 10, 1, 8, 43, 39, 7, 3, 5, 9, 34, 17, 21, 18, 28, 30, 35, 36, 46, 47, 48, 49 ) THEN 'CONTACTO'
                        WHEN res_id_FK IN ( 19, 27, 12, 26, 13, 4, 11, 12, 20, 14, 15, 16, 23, 24, 29, 31 ) THEN 'ILOCALIZADO'
                        WHEN res_id_FK IN ( 45, 25, 44 ) THEN 'NO CONTACTO'
                        WHEN res_id_FK IN ( 32 ) THEN 'NO DISPONIBLE'
                        ELSE 'SIN GESTIÓN'
                    end) as ubic,
                    cartera,
                    if(capital is null,0,capital) as capital,
                    if(saldo_deuda is null,0,saldo_deuda) as saldo_deuda,
                    if(monto_camp is null,0,monto_camp) as monto_camp,
                    if(cliente_pago is null,0,cliente_pago) as cliente_pago,
                    capital_pago,
                    importe_pago,
                    monto_pagos
                    FROM
                    (
                    SELECT 
                    cartera,
                    capital,
                    saldo_deuda,
                    monto_camp,
                    count(pag_cli_cod) as cant_pagos,
                    sum(pag_cli_mon) as monto_pagos,
                    if(pag_cli_cod is null,0,capital) as capital_pago,
                    if(pag_cli_cod is null,0,monto_camp) as importe_pago,
                    if(pag_cli_cod is null,0,1) as cliente_pago,
                    (select max(ges_cli_id)
                        from cliente c 
                        inner join gestion_cliente g on c.cli_id=g.cli_id_FK
                        where c.cli_cod=i.cuenta
                        and c.car_id_FK=i.car_id_FK
                        and (date(ges_cli_fec) between '$fec_i' and '$fec_f')
                    ) as maxid
                    FROM indicadores.cartera_detalle i
                    LEFT join pago_cliente_2 p on p.pag_cli_cod=i.cuenta and p.car_id_FK=$car and date(pag_cli_fec) between date('$fec_i') and date('$fec_f')
                    WHERE 
                    i.car_id_fk=$car
                    and date_format(fecha,'%Y%m') in (date_format('$fec_i','%Y%m'),date_format('$fec_f','%Y%m'))
                    group by cuenta
                    ) t
                    LEFT JOIN gestion_cliente gg ON t.maxid=gg.ges_cli_id
            ) tt
            group by ubic
            Order by clientes desc
            ) ttt
            where clientes_pagos>0
        ";
        $query=DB::select(DB::raw($sql));
        return $query;
    }

    /*-----------------------cierre ubicabilidad----------------------------------------*/
    /*-----------------------------------------------------------------------------*/

    public static function buscarCarteraPagos($car, $tip, $fec_i, $fec_f){

        /*
        select p.car_id_FK,car_nom,cli_cod,pag_cli_mon,pag_cli_fec
        from pago_cliente_2 as p inner join cartera as car
        on p.car_id_fk = car.car_id 
        right join cliente c 
        on p.pag_cli_cod=c.cli_cod
        WHERE car_id=34  
*/
        $sq=DB::connection('mysql2')
        ->table('pago_cliente_2 as p')
        ->join('indicadores.cartera_detalle as cd','p.pag_cli_cod','=','cd.cuenta')
        ->select('pag_cli_cod',DB::raw('pag_cli_mon'),
                DB::raw('pag_cli_mon as cantidad_pagos'),
                DB::raw('date_format( pag_cli_fec, "%Y-%m-%d") as fecha_pago'),
                DB::raw('(CASE 
                WHEN capital <500 THEN "A: [0-500>"
                WHEN capital >= 500 and capital < 1000 THEN "B: [500-1000>"
                WHEN capital >= 1000 and capital < 3000 THEN "C: [1000-3000>"
                WHEN capital >= 3000 THEN "D: [3000-+>"
                END) AS capital'),
                DB::raw('(CASE 
                WHEN saldo_deuda <500 THEN "A: [0-500>"
                WHEN saldo_deuda >= 500 and saldo_deuda < 1000 THEN "B: [500-1000>"
                WHEN saldo_deuda >= 1000 and saldo_deuda < 3000 THEN "C: [1000-3000>"
                WHEN saldo_deuda >= 3000 THEN "D: [3000-+>"
                END) AS saldo_deuda'),
                DB::raw('(CASE 
                WHEN monto_camp <500 THEN "A: [0-500>"
                WHEN monto_camp >= 500 and monto_camp < 1000 THEN "B: [500-1000>"
                WHEN monto_camp >= 1000 and monto_camp < 3000 THEN "C: [1000-3000>"
                WHEN monto_camp >= 3000 THEN "D: [3000-+>"
                END) AS monto_camp'),
                //'capital as capital_t','saldo_deuda as saldo_deuda_t','monto_camp as monto_camp_t'
                'tramo','dep_ind','prioridad','dep','entidades','score','rango_sueldo'
                )
        ->where('cd.car_id_fk','=',$car)
        ->whereBetween(DB::raw('(date_format(pag_cli_fec,"%Y-%m-%d"))'),array($fec_i,$fec_f))
        ->where(DB::raw('(date_format(fecha,"%Y-%m"))'),'=',date("Y-m", strtotime($fec_i)));
        

        $sq2=DB::connection('mysql2')
        ->table(DB::raw(' ( ' . $sq->toSql() . ' ) as t '))
        ->mergeBindings( $sq )
        ->join('indicadores.cartera_detalle as cde','t.pag_cli_cod','=','cde.cuenta')
        ->select(DB::raw('TRIM(t.'.$tip.') as tipo'),
                DB::raw('sum(t.pag_cli_mon) as total_pagos'),
                DB::raw('count(distinct(pag_cli_cod)) as cantidad'),
                DB::raw('count(cantidad_pagos) as cant_pagos'),
                DB::raw('sum(distinct(cde.capital)) as total_capital'),
                DB::raw('sum(distinct(cde.saldo_deuda)) as total_deuda'),
                DB::raw('sum(distinct(cde.monto_camp)) as total_importe'))
        ->where('cde.car_id_fk','=',$car)
        ->whereBetween(DB::raw('(date_format(fecha_pago,"%Y-%m-%d"))'),array($fec_i,$fec_f))
        ->where(DB::raw('(date_format(fecha,"%Y-%m"))'),'=',date("Y-m", strtotime($fec_i)))
        ->groupBy('tipo')
        ->get();
        //return $sq2;

        /*$sq3="
            SELECT
            $tip as tipo,
            count(cuenta) as clientes_estructura_total,
            sum(capital) as capital_estructura_total,
            SUM(monto_camp) as importe_estructura_total
            from indicadores.cartera_detalle
            WHERE date_format(fecha,'%Y-%m')=date_format('$fec_i','%Y-%m')
             and car_id_fk=$car
            GROUP BY $tip
        ";*/
        $sq3="
            SELECT
            TRIM($tip) as tipo,
            count(cuenta) as clientes_estructura_total,
            SUM(capital_t) as capital_estructura_total,
            SUM(monto_camp_t) as importe_estructura_total
            FROM
            (SELECT 
                cuenta,
                (CASE 
                WHEN capital <500 THEN 'A: [0-500>'
                WHEN capital >= 500 and capital < 1000 THEN 'B: [500-1000>'
                WHEN capital >= 1000 and capital < 3000 THEN 'C: [1000-3000>'
                WHEN capital >= 3000 THEN 'D: [3000-+>'
                END) AS capital,
                (CASE 
                WHEN saldo_deuda <500 THEN 'A: [0-500>'
                WHEN saldo_deuda >= 500 and saldo_deuda < 1000 THEN 'B: [500-1000>'
                WHEN saldo_deuda >= 1000 and saldo_deuda < 3000 THEN 'C: [1000-3000>'
                WHEN saldo_deuda >= 3000 THEN 'D: [3000-+>'
                END) AS saldo_deuda,
                (CASE 
                WHEN monto_camp <500 THEN 'A: [0-500>'
                WHEN monto_camp >= 500 and monto_camp < 1000 THEN 'B: [500-1000>'
                WHEN monto_camp >= 1000 and monto_camp < 3000 THEN 'C: [1000-3000>'
                WHEN monto_camp >= 3000 THEN 'D: [3000-+>'
                END) AS monto_camp,
                capital as capital_t,saldo_deuda as saldo_deuda_t,monto_camp as monto_camp_t,
                tramo,dep_ind,prioridad,dep,entidades,score,rango_sueldo
            from indicadores.cartera_detalle
            WHERE date_format(fecha,'%Y-%m')=date_format('$fec_i','%Y-%m') and car_id_fk=$car
            ) t
            GROUP BY $tip
        ";
        $totales=DB::select(DB::raw($sq3));
        return response()->json(['datos' => $sq2,'totales' => $totales]);

        //dd($sq2);

        /*$gestion=DB::connection('mysql2')
        ->table('pago_cliente_2 as p')
        ->join('indicadores.cartera_detalle as cd','p.pag_cli_cod','=','cd.cuenta')
        ->select(DB::raw($tip.' as tipo'),DB::raw('count(distinct(pag_cli_cod)) as cantidad'),
            DB::raw('sum(pag_cli_mon) as total_pagos'),DB::raw('sum(capital) as total_capital'),
            DB::raw('sum(saldo_deuda) as total_deuda'),DB::raw('sum(monto_camp) as total_importe'))
        ->where('cd.car_id_fk','=',$car)
        ->whereBetween(DB::raw('(date_format(pag_cli_fec,"%Y-%m-%d"))'),array($fec_i,$fec_f))
        ->where(DB::raw('(date_format(fecha,"%Y-%m"))'),'=',date("Y-m", strtotime($fec_i)))
        ->groupBy($tip)
        ->get();
        return $gestion;*/
        //dd($gestion);

    }

 








    /* --indicadores operativos-- */

    public static function buscarTotales(){
        $total = DB::connection('mysql')
        ->table('cartera_detalle')
        ->select(DB::raw('count(distinct(cuenta)) as cuentas_total'))
        ->where('car_id_fk','=','34')
        ->groupBy('car_id_fk')
        ->get();

        //return $total;
        dd($total);
    }
    

    public static function buscarCobertura($car){

        //(select count(distinct(cuenta)) from indicadores.cartera_detalle where car_id_fk=$car group by car_id_fk) as total_cuentas

        $totales=DB::select(DB::raw("
        select month(fecha) as mes, count(distinct(cuenta)) as total_cuentas from indicadores.cartera_detalle 
        where car_id_fk=$car
        and year(fecha) = year(now())
        and month(fecha) BETWEEN month(now()) - 2 and month(now())
        GROUP by  month(fecha)"));

        /*$datos=DB::select(DB::raw("
                    SELECT fec,SUM(gestiones) as cant_gestiones,SUM(cliente) as can_clientes
                    FROM
                        (SELECT cli_id,fec,gestiones,car_id_fk,
                            IF (
                                (SELECT count(*)
                                    FROM
                                        gestion_cliente
                                    WHERE                                 		
                                        month(ges_cli_fec) = month(fec)
                                        AND date(ges_cli_fec)<fec - INTERVAL 1 day                                  	
                                        AND cli_id_FK=cli_id
                                        AND ges_cli_acc in (1,2)
                                ) = 0,
                                1,
                                0
                            ) AS cliente
                        FROM
                            (SELECT distinct cli_id,date(ges_cli_fec) AS fec,count(cuenta) AS gestiones,ic.car_id_fk
                            FROM
                                gestion_cliente g
                            INNER JOIN cliente c ON c.cli_id = g.cli_id_FK
                            INNER JOIN indicadores.cartera_detalle ic ON c.cli_cod = ic.cuenta
                            WHERE
                                    year(ges_cli_fec) = year(now())
                                    and month(ges_cli_fec) BETWEEN month(now()) - 2 and month(now()) 
                                    AND ic.car_id_fk=$car
                                    AND c.car_id_FK=$car
                                    AND ges_cli_acc in (1,2)
                            GROUP BY cli_id,cuenta,date(ges_cli_fec),ic.car_id_fk
                            ) t
                        )tt
                    GROUP BY fec
            "));*/

        //dd($res);
        //return $res;

        $datos1=DB::select(DB::raw("
                    SELECT
                        fec,
                        SUM(gestiones) as cant_gestiones,
                        SUM(cliente) as can_clientes
                    FROM
                        (SELECT
                            cli_id,
                            fec,
                            gestiones,
                            IF (
                                (
                                    SELECT
                                        count(*)
                                    FROM
                                        gestion_cliente
                                    WHERE
                                    year(ges_cli_fec) = year(now())
                                    and month(ges_cli_fec) = month(now()) - 2
                                    AND date(ges_cli_fec)<fec
                                    AND cli_id_FK=cli_id
                                ) = 0,
                                1,
                                0
                            ) AS cliente
                        FROM
                            (
                                SELECT
                                    cli_id,
                                    date(ges_cli_fec) AS fec,
                                    count( cuenta) AS gestiones
                                FROM
                                    gestion_cliente g
                                INNER JOIN cliente c ON c.cli_id = g.cli_id_FK
                                INNER JOIN indicadores.cartera_detalle ic ON c.cli_cod = ic.cuenta
                                WHERE
                                ic.car_id_FK=$car
                                AND c.car_id_fk=$car
                                and ges_cli_acc in (1,2)
                                and year(ges_cli_fec) = year(now())
                            and month(ges_cli_fec) = month(now()) - 2 
                                and MONTH(fecha) = month(now()) - 2 
                                GROUP BY
                                    cuenta,date(ges_cli_fec)
                            ) t
                        )tt
                    GROUP BY fec      
        "));
        $datos2=DB::select(DB::raw("
                    SELECT
                        fec,
                        SUM(gestiones) as cant_gestiones,
                        SUM(cliente) as can_clientes
                    FROM
                        (SELECT
                            cli_id,
                            fec,
                            gestiones,
                            IF (
                                (
                                    SELECT
                                        count(*)
                                    FROM
                                        gestion_cliente
                                    WHERE
                                    year(ges_cli_fec) = year(now())
                                    and month(ges_cli_fec) = month(now()) - 1
                                    AND date(ges_cli_fec)<fec
                                    AND cli_id_FK=cli_id
                                ) = 0,
                                1,
                                0
                            ) AS cliente
                        FROM
                            (
                                SELECT
                                    cli_id,
                                    date(ges_cli_fec) AS fec,
                                    count( cuenta) AS gestiones
                                FROM
                                    gestion_cliente g
                                INNER JOIN cliente c ON c.cli_id = g.cli_id_FK
                                INNER JOIN indicadores.cartera_detalle ic ON c.cli_cod = ic.cuenta
                                WHERE
                                ic.car_id_FK=$car
                                AND c.car_id_fk=$car
                                and ges_cli_acc in (1,2)
                                and year(ges_cli_fec) = year(now())
                            and month(ges_cli_fec) = month(now()) - 1 
                                and MONTH(fecha) = month(now()) - 1 
                                GROUP BY
                                    cuenta,date(ges_cli_fec)
                            ) t
                        )tt
                    GROUP BY fec
        "));
        $datos3=DB::select(DB::raw("
                    SELECT
                        fec,
                        SUM(gestiones) as cant_gestiones,
                        SUM(cliente) as can_clientes
                    FROM
                        (SELECT
                            cli_id,
                            fec,
                            gestiones,
                            IF (
                                (
                                    SELECT
                                        count(*)
                                    FROM
                                        gestion_cliente
                                    WHERE
                                    year(ges_cli_fec) = year(now())
                                    and month(ges_cli_fec) = month(now())
                                    AND date(ges_cli_fec)<fec
                                    AND cli_id_FK=cli_id
                                ) = 0,
                                1,
                                0
                            ) AS cliente
                        FROM
                            (
                                SELECT
                                    cli_id,
                                    date(ges_cli_fec) AS fec,
                                    count( cuenta) AS gestiones
                                FROM
                                    gestion_cliente g
                                INNER JOIN cliente c ON c.cli_id = g.cli_id_FK
                                INNER JOIN indicadores.cartera_detalle ic ON c.cli_cod = ic.cuenta
                                WHERE
                                ic.car_id_FK=$car
                                AND c.car_id_fk=$car
                                and ges_cli_acc in (1,2)
                                and year(ges_cli_fec) = year(now())
                                and month(ges_cli_fec) = month(now()) 
                                and MONTH(fecha) = month(now())
                                GROUP BY
                                    cuenta,date(ges_cli_fec)
                            ) t
                        )tt
                    GROUP BY fec
        "));

        return response()->json(['totales' => $totales, 'datos1' => $datos1, 'datos2' => $datos2 , 'datos3' => $datos3]);

    }

    public static function buscarContacto($car){

        /*(select count(distinct(cuenta)),fecha from indicadores.cartera_detalle 
        where car_id_fk=34
        and year(fecha) = year(now())
        and month(fecha) BETWEEN month(now()) - 2 and month(now())
        GROUP by  month(fecha)) as total_cuentas*/
        $totales=DB::select(DB::raw("
        select month(fecha) as mes, count(distinct(cuenta)) as total_cuentas from indicadores.cartera_detalle 
        where car_id_fk=$car
        and year(fecha) = year(now())
        and month(fecha) BETWEEN month(now()) - 2 and month(now())
        GROUP by  month(fecha)"));
        //$query1=$sub->get()->toArray();


        /*$datos=DB::select(DB::raw("
                SELECT date(ges_cli_fec) AS fec, COUNT(cartera) AS cant_gestiones,
                COUNT(DISTINCT(CLI_COD)) AS can_clientes
                FROM gestion_cliente AS GES
                RIGHT JOIN cliente AS CLI ON CLI.CLI_ID = GES.CLI_ID_FK
                INNER JOIN cartera AS CAR ON CAR.CAR_ID = CLI.CAR_ID_FK
                INNER JOIN indicadores.cartera_detalle AS CDET ON CLI.CLI_COD = CDET.CUENTA
                WHERE CDET.car_id_fk = $car
                and year(ges_cli_fec) = year(now())
                and month(ges_cli_fec) BETWEEN month(now()) - 2 and month(now())
                and res_id_fk in (18,5,17,21)
                and year(fecha) = year(now())
                and month(fecha) BETWEEN month(now()) - 2 and month(now())
                GROUP by DATE(ges_cli_fec)
            "));*/
        $datos1=DB::select(DB::raw("
        
                        SELECT
                        fec,
                        SUM(cliente) as can_clientes
                    FROM
                    (SELECT
                        cli_id,
                        fec,
                        IF (
                            (
                                SELECT
                                        count(*)
                                    FROM
                                        gestion_cliente g
                                    WHERE
                                    year(g.ges_cli_fec) = year(now())
                                    and month(g.ges_cli_fec) = month(now()) - 2
                                    AND date(g.ges_cli_fec)<fec
                                    AND g.cli_id_FK=cli_id
                                    and g.res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7,34,17,21)
                            ) = 0,
                            1,
                            0
                        ) AS cliente
                    FROM
                        (
                            SELECT 
                                cli_id,
                                cuenta,
                                date(ges_cli_fec) AS fec,
                                (SELECT
                                        max(ges_cli_id) AS maxid
                                    FROM
                                        gestion_cliente
                                    WHERE
                                        date(ges_cli_fec)<=DATE(g.ges_cli_fec)
                                    and cli_id_FK=cli_id
                                ) AS MAXGES,
                                sum(if(res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7,34,17,21),1,0)) as cli
                            FROM
                                gestion_cliente g
                            INNER JOIN cliente c ON c.cli_id = g.cli_id_FK
                            INNER JOIN indicadores.cartera_detalle ic ON c.cli_cod = ic.cuenta
                            WHERE
                            ic.car_id_FK=$car
                            AND c.car_id_fk=$car
                            and year(ges_cli_fec) = year(now())
                            and month(ges_cli_fec) = month(now()) - 2 
                            and MONTH(fecha) = month(now()) - 2
                            and res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7,34,17,21)
                            GROUP BY cuenta,date(ges_cli_fec)
                        ) t
                        INNER JOIN gestion_cliente ult_ges ON t.MAXGES=ult_ges.ges_cli_id
                        WHERE res_id_FK not in (19,27,12,13,26)
                    )tt
                    GROUP BY fec
        "));

        $datos2=DB::select(DB::raw("
        
                        SELECT
                        fec,
                        SUM(cliente) as can_clientes
                    FROM
                    (SELECT
                        cli_id,
                        fec,
                        IF (
                            (
                                SELECT
                                        count(*)
                                    FROM
                                        gestion_cliente g
                                    WHERE
                                    year(g.ges_cli_fec) = year(now())
                                    and month(g.ges_cli_fec) = month(now()) - 1
                                    AND date(g.ges_cli_fec)<fec
                                    AND g.cli_id_FK=cli_id
                                    and g.res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7,34,17,21)
                            ) = 0,
                            1,
                            0
                        ) AS cliente
                    FROM
                        (
                            SELECT 
                                cli_id,
                                cuenta,
                                date(ges_cli_fec) AS fec,
                                (SELECT
                                        max(ges_cli_id) AS maxid
                                    FROM
                                        gestion_cliente
                                    WHERE
                                        date(ges_cli_fec)<=DATE(g.ges_cli_fec)
                                    and cli_id_FK=cli_id
                                ) AS MAXGES,
                                sum(if(res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7,34,17,21),1,0)) as cli
                            FROM
                                gestion_cliente g
                            INNER JOIN cliente c ON c.cli_id = g.cli_id_FK
                            INNER JOIN indicadores.cartera_detalle ic ON c.cli_cod = ic.cuenta
                            WHERE
                            ic.car_id_FK=$car
                            AND c.car_id_fk=$car
                            and year(ges_cli_fec) = year(now())
                            and month(ges_cli_fec) = month(now()) - 1
                            and MONTH(fecha) = month(now()) - 1
                            and res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7,34,17,21)
                            GROUP BY cuenta,date(ges_cli_fec)
                        ) t
                        INNER JOIN gestion_cliente ult_ges ON t.MAXGES=ult_ges.ges_cli_id
                        WHERE res_id_FK not in (19,27,12,13,26)
                    )tt
                    GROUP BY fec
        "));

        $datos3=DB::select(DB::raw("
        
                        SELECT
                        fec,
                        SUM(cliente) as can_clientes
                    FROM
                    (SELECT
                        cli_id,
                        fec,
                        IF (
                            (
                                SELECT
                                        count(*)
                                    FROM
                                        gestion_cliente g
                                    WHERE
                                    year(g.ges_cli_fec) = year(now())
                                    and month(g.ges_cli_fec) = month(now())
                                    AND date(g.ges_cli_fec)<fec
                                    AND g.cli_id_FK=cli_id
                                    and g.res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7,34,17,21)
                            ) = 0,
                            1,
                            0
                        ) AS cliente
                    FROM
                        (
                            SELECT 
                                cli_id,
                                cuenta,
                                date(ges_cli_fec) AS fec,
                                (SELECT
                                        max(ges_cli_id) AS maxid
                                    FROM
                                        gestion_cliente
                                    WHERE
                                        date(ges_cli_fec)<=DATE(g.ges_cli_fec)
                                    and cli_id_FK=cli_id
                                ) AS MAXGES,
                                sum(if(res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7,34,17,21),1,0)) as cli
                            FROM
                                gestion_cliente g
                            INNER JOIN cliente c ON c.cli_id = g.cli_id_FK
                            INNER JOIN indicadores.cartera_detalle ic ON c.cli_cod = ic.cuenta
                            WHERE
                            ic.car_id_FK=$car
                            AND c.car_id_fk=$car
                            and year(ges_cli_fec) = year(now())
                            and month(ges_cli_fec) = month(now())
                            and MONTH(fecha) = month(now())
                            and res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7,34,17,21)
                            GROUP BY cuenta,date(ges_cli_fec)
                        ) t
                        INNER JOIN gestion_cliente ult_ges ON t.MAXGES=ult_ges.ges_cli_id
                        WHERE res_id_FK not in (19,27,12,13,26)
                    )tt
                    GROUP BY fec
        "));
        

        // return response()->json(['totales' => $totales, 'datos' => $datos]);
        return response()->json(['totales' => $totales, 'datos1' => $datos1, 'datos2' => $datos2 , 'datos3' => $datos3]);
    }

    public static function buscarContactoEfectivo($car){

        /*(select count(distinct(cuenta)) from indicadores.cartera_detalle 
                where car_id_fk=$car
                and year(fecha) = year(now())
                and month(fecha) BETWEEN month(now()) - 2 and month(now())) as total_cuentas*/

        $totales=DB::select(DB::raw("
        select month(fecha) as mes, count(distinct(cuenta)) as total_cuentas from indicadores.cartera_detalle 
        where car_id_fk=$car
        and year(fecha) = year(now())
        and month(fecha) BETWEEN month(now()) - 2 and month(now())
        GROUP by  month(fecha)"));

        /*$datos=DB::select(DB::raw("
                SELECT date(ges_cli_fec) AS fec, COUNT(cartera) AS cant_gestiones,
                COUNT(DISTINCT(CLI_COD)) AS can_clientes
                FROM gestion_cliente AS GES
                RIGHT JOIN cliente AS CLI ON CLI.CLI_ID = GES.CLI_ID_FK
                INNER JOIN cartera AS CAR ON CAR.CAR_ID = CLI.CAR_ID_FK
                INNER JOIN indicadores.cartera_detalle AS CDET ON CLI.CLI_COD = CDET.CUENTA
                WHERE CDET.car_id_fk = $car
                and year(ges_cli_fec) = year(now())
                and month(ges_cli_fec) BETWEEN month(now()) - 2 and month(now())
                and res_id_fk in (38,6,22,41,2,37,18,10,1,8,43,39,7)
                and year(fecha) = year(now())
                and month(fecha) BETWEEN month(now()) - 2 and month(now())
                GROUP by DATE(ges_cli_fec)
            "));*/

        $datos1=DB::select(DB::raw("
        
                        SELECT
                        fec,
                        SUM(cliente) as can_clientes
                    FROM
                    (SELECT
                        cli_id,
                        fec,
                        IF (
                            (
                                SELECT
                                        count(*)
                                    FROM
                                        gestion_cliente g
                                    WHERE
                                    year(g.ges_cli_fec) = year(now())
                                    and month(g.ges_cli_fec) = month(now()) - 2
                                    AND date(g.ges_cli_fec)<fec
                                    AND g.cli_id_FK=cli_id
                                    and g.res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7)
                            ) = 0,
                            1,
                            0
                        ) AS cliente
                    FROM
                        (
                            SELECT 
                                cli_id,
                                cuenta,
                                date(ges_cli_fec) AS fec,
                                (SELECT
                                        max(ges_cli_id) AS maxid
                                    FROM
                                        gestion_cliente
                                    WHERE
                                        date(ges_cli_fec)<=DATE(g.ges_cli_fec)
                                    and cli_id_FK=cli_id
                                ) AS MAXGES,
                                sum(if(res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7),1,0)) as cli
                            FROM
                                gestion_cliente g
                            INNER JOIN cliente c ON c.cli_id = g.cli_id_FK
                            INNER JOIN indicadores.cartera_detalle ic ON c.cli_cod = ic.cuenta
                            WHERE
                            ic.car_id_FK=$car
                            AND c.car_id_fk=$car
                            and year(ges_cli_fec) = year(now())
                            and month(ges_cli_fec) = month(now()) - 2
                            and MONTH(fecha) = month(now()) - 2
                            and res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7)
                            GROUP BY cuenta,date(ges_cli_fec)
                        ) t
                        INNER JOIN gestion_cliente ult_ges ON t.MAXGES=ult_ges.ges_cli_id
                        WHERE res_id_FK not in (19,27,12,13,26,34,17,21)
                    )tt
                    GROUP BY fec
        "));

        $datos2=DB::select(DB::raw("
        
                        SELECT
                        fec,
                        SUM(cliente) as can_clientes
                    FROM
                    (SELECT
                        cli_id,
                        fec,
                        IF (
                            (
                                SELECT
                                        count(*)
                                    FROM
                                        gestion_cliente g
                                    WHERE
                                    year(g.ges_cli_fec) = year(now())
                                    and month(g.ges_cli_fec) = month(now()) - 1
                                    AND date(g.ges_cli_fec)<fec
                                    AND g.cli_id_FK=cli_id
                                    and g.res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7)
                            ) = 0,
                            1,
                            0
                        ) AS cliente
                    FROM
                        (
                            SELECT 
                                cli_id,
                                cuenta,
                                date(ges_cli_fec) AS fec,
                                (SELECT
                                        max(ges_cli_id) AS maxid
                                    FROM
                                        gestion_cliente
                                    WHERE
                                        date(ges_cli_fec)<=DATE(g.ges_cli_fec)
                                    and cli_id_FK=cli_id
                                ) AS MAXGES,
                                sum(if(res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7),1,0)) as cli
                            FROM
                                gestion_cliente g
                            INNER JOIN cliente c ON c.cli_id = g.cli_id_FK
                            INNER JOIN indicadores.cartera_detalle ic ON c.cli_cod = ic.cuenta
                            WHERE
                            ic.car_id_FK=$car
                            AND c.car_id_fk=$car
                            and year(ges_cli_fec) = year(now())
                            and month(ges_cli_fec) = month(now()) - 1
                            and MONTH(fecha) = month(now()) - 1
                            and res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7)
                            GROUP BY cuenta,date(ges_cli_fec)
                        ) t
                        INNER JOIN gestion_cliente ult_ges ON t.MAXGES=ult_ges.ges_cli_id
                        WHERE res_id_FK not in (19,27,12,13,26,34,17,21)
                    )tt
                    GROUP BY fec
        "));

        $datos3=DB::select(DB::raw("
        
                        SELECT
                        fec,
                        SUM(cliente) as can_clientes
                    FROM
                    (SELECT
                        cli_id,
                        fec,
                        IF (
                            (
                                SELECT
                                        count(*)
                                    FROM
                                        gestion_cliente g
                                    WHERE
                                    year(g.ges_cli_fec) = year(now())
                                    and month(g.ges_cli_fec) = month(now())
                                    AND date(g.ges_cli_fec)<fec
                                    AND g.cli_id_FK=cli_id
                                    and g.res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7)
                            ) = 0,
                            1,
                            0
                        ) AS cliente
                    FROM
                        (
                            SELECT 
                                cli_id,
                                cuenta,
                                date(ges_cli_fec) AS fec,
                                (SELECT
                                        max(ges_cli_id) AS maxid
                                    FROM
                                        gestion_cliente
                                    WHERE
                                        date(ges_cli_fec)<=DATE(g.ges_cli_fec)
                                    and cli_id_FK=cli_id
                                ) AS MAXGES,
                                sum(if(res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7),1,0)) as cli
                            FROM
                                gestion_cliente g
                            INNER JOIN cliente c ON c.cli_id = g.cli_id_FK
                            INNER JOIN indicadores.cartera_detalle ic ON c.cli_cod = ic.cuenta
                            WHERE
                            ic.car_id_FK=$car
                            AND c.car_id_fk=$car
                            and year(ges_cli_fec) = year(now())
                            and month(ges_cli_fec) = month(now()) 
                            and MONTH(fecha) = month(now())
                            and res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7)
                            GROUP BY cuenta,date(ges_cli_fec)
                        ) t
                        INNER JOIN gestion_cliente ult_ges ON t.MAXGES=ult_ges.ges_cli_id
                        WHERE res_id_FK not in (19,27,12,13,26,34,17,21)
                    )tt
                    GROUP BY fec
        "));

        
        //dd($res);
        //return $res;
        //return response()->json(['totales' => $totales, 'datos' => $datos]);
        return response()->json(['totales' => $totales, 'datos1' => $datos1, 'datos2' => $datos2 , 'datos3' => $datos3]);
    }

    public static function buscarIntensidad($car){
        //can_clientes es cantidad de gestiones

        $totales=DB::select(DB::raw("
        select month(fecha) as mes, count(distinct(cuenta)) as total_cuentas from indicadores.cartera_detalle 
        where car_id_fk=$car
        and year(fecha) = year(now())
        and month(fecha) BETWEEN month(now()) - 2 and month(now())
        GROUP by  month(fecha)"));

        /*$datos=DB::select(DB::raw("
                SELECT date(ges_cli_fec) AS fec, COUNT(cartera) AS can_clientes,
                COUNT(DISTINCT(CLI_COD)) AS cant_clientes
                FROM gestion_cliente AS GES
                RIGHT JOIN cliente AS CLI ON CLI.CLI_ID = GES.CLI_ID_FK
                INNER JOIN cartera AS CAR ON CAR.CAR_ID = CLI.CAR_ID_FK
                INNER JOIN indicadores.cartera_detalle AS CDET ON CLI.CLI_COD = CDET.CUENTA
                WHERE CDET.car_id_fk = $car
                and year(ges_cli_fec) = year(now())
                and month(ges_cli_fec) BETWEEN month(now()) - 2 and month(now())
                and year(fecha) = year(now())
                and month(fecha) BETWEEN month(now()) - 2 and month(now())
                GROUP by DATE(ges_cli_fec)
            "));*/
        $datos1=DB::select(DB::raw("
                            SELECT
                                date(ges_cli_fec) AS fec,
                                count( ic.car_id_fk) AS can_clientes
                            FROM
                                gestion_cliente g
                            INNER JOIN cliente c ON c.cli_id = g.cli_id_FK
                            INNER JOIN indicadores.cartera_detalle ic ON c.cli_cod = ic.cuenta
                            WHERE
                            ic.car_id_FK=$car
                            and year(ges_cli_fec) = year(now())
                            and month(ges_cli_fec) = month(now()) - 2 
                            and MONTH(fecha) = month(now()) - 2 
                            GROUP BY
                                date(ges_cli_fec)
        "));
        $datos2=DB::select(DB::raw("
                            SELECT
                                date(ges_cli_fec) AS fec,
                                count( ic.car_id_fk) AS can_clientes
                            FROM
                                gestion_cliente g
                            INNER JOIN cliente c ON c.cli_id = g.cli_id_FK
                            INNER JOIN indicadores.cartera_detalle ic ON c.cli_cod = ic.cuenta
                            WHERE
                            ic.car_id_FK=$car
                            and year(ges_cli_fec) = year(now())
                            and month(ges_cli_fec) = month(now()) - 1
                            and MONTH(fecha) = month(now()) - 1 
                            GROUP BY
                                date(ges_cli_fec)
        "));
        $datos3=DB::select(DB::raw("
                            SELECT
                                date(ges_cli_fec) AS fec,
                                count( ic.car_id_fk) AS can_clientes
                            FROM
                                gestion_cliente g
                            INNER JOIN cliente c ON c.cli_id = g.cli_id_FK
                            INNER JOIN indicadores.cartera_detalle ic ON c.cli_cod = ic.cuenta
                            WHERE
                            ic.car_id_FK=$car
                            and year(ges_cli_fec) = year(now())
                            and month(ges_cli_fec) = month(now())
                            and MONTH(fecha) = month(now())
                            GROUP BY
                                date(ges_cli_fec)
        "));
        //dd($res);
        //return $res;
        //return response()->json(['totales' => $totales, 'datos' => $datos]);
        return response()->json(['totales' => $totales, 'datos1' => $datos1, 'datos2' => $datos2 , 'datos3' => $datos3]);
    }

    public static function buscarIntensidadDirecta($car){
        //can_clientes es cantidad de gestiones

        /*$totales=DB::select(DB::raw("
        select month(fecha) as mes, count(distinct(cuenta)) as total_cuentas from indicadores.cartera_detalle 
        where car_id_fk=$car
        and year(fecha) = year(now())
        and month(fecha) BETWEEN month(now()) - 2 and month(now())
        GROUP by  month(fecha)"));

        $datos=DB::select(DB::raw("
                SELECT date(ges_cli_fec) AS fec, COUNT(cartera) AS can_clientes,
                COUNT(DISTINCT(CLI_COD)) AS cant_clientes
                FROM gestion_cliente AS GES
                RIGHT JOIN cliente AS CLI ON CLI.CLI_ID = GES.CLI_ID_FK
                INNER JOIN cartera AS CAR ON CAR.CAR_ID = CLI.CAR_ID_FK
                INNER JOIN indicadores.cartera_detalle AS CDET ON CLI.CLI_COD = CDET.CUENTA
                WHERE CDET.car_id_fk = $car
                and year(ges_cli_fec) = year(now())
                and month(ges_cli_fec) BETWEEN month(now()) - 2 and month(now())
                and res_id_fk in (38,6,22,41,2,37,18,10,1,8,43,39,7,34,17,21,19,27,12,26,13,45,25,32)
                and year(fecha) = year(now())
                and month(fecha) BETWEEN month(now()) - 2 and month(now())
                GROUP by DATE(ges_cli_fec)
            "));*/
            $total1=DB::select(DB::raw("
                            SELECT
                            fec,
                            SUM(cliente) as can_clientes
                        FROM
                        (SELECT
                            cli_id,
                            fec,
                            IF (
                                (
                                    SELECT
                                            count(*)
                                        FROM
                                            gestion_cliente g
                                        WHERE
                                        year(g.ges_cli_fec) = year(now())
                                        and month(g.ges_cli_fec) = month(now()) - 2
                                        AND date(g.ges_cli_fec)<fec
                                        AND g.cli_id_FK=cli_id
                                        and g.res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7)
                                ) = 0,
                                1,
                                0
                            ) AS cliente
                        FROM
                            (
                                SELECT 
                                    cli_id,
                                    cuenta,
                                    date(ges_cli_fec) AS fec,
                                    (SELECT
                                            max(ges_cli_id) AS maxid
                                        FROM
                                            gestion_cliente
                                        WHERE
                                            date(ges_cli_fec)<=DATE(g.ges_cli_fec)
                                        and cli_id_FK=cli_id
                                    ) AS MAXGES,
                                    sum(if(res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7),1,0)) as cli
                                FROM
                                    gestion_cliente g
                                INNER JOIN cliente c ON c.cli_id = g.cli_id_FK
                                INNER JOIN indicadores.cartera_detalle ic ON c.cli_cod = ic.cuenta
                                WHERE
                                ic.car_id_FK=$car
                                AND c.car_id_fk=$car
                                and year(ges_cli_fec) = year(now())
                                and month(ges_cli_fec) = month(now()) - 2
                                and MONTH(fecha) = month(now()) - 2
                                and res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7)
                                GROUP BY cuenta,date(ges_cli_fec)
                            ) t
                            INNER JOIN gestion_cliente ult_ges ON t.MAXGES=ult_ges.ges_cli_id
                            WHERE res_id_FK not in (19,27,12,13,26,34,17,21)
                        )tt
                        GROUP BY fec
            "));

            $datos1=DB::select(DB::raw("
                            SELECT
                            date(ges_cli_fec) AS fec,
                            count( ic.car_id_fk) AS can_gestiones
                            FROM
                                gestion_cliente g
                            INNER JOIN cliente c ON c.cli_id = g.cli_id_FK
                            INNER JOIN indicadores.cartera_detalle ic ON c.cli_cod = ic.cuenta
                            WHERE
                            ic.car_id_FK=$car
                            and year(ges_cli_fec) = year(now())
                            and month(ges_cli_fec) = month(now()) -2
                            and MONTH(fecha) = month(now()) -2
                            and res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7,34,17,21)
                            GROUP BY
                                date(ges_cli_fec)
            "));

            $total2=DB::select(DB::raw("
                            SELECT
                            fec,
                            SUM(cliente) as can_clientes
                        FROM
                        (SELECT
                            cli_id,
                            fec,
                            IF (
                                (
                                    SELECT
                                            count(*)
                                        FROM
                                            gestion_cliente g
                                        WHERE
                                        year(g.ges_cli_fec) = year(now())
                                        and month(g.ges_cli_fec) = month(now()) - 1
                                        AND date(g.ges_cli_fec)<fec
                                        AND g.cli_id_FK=cli_id
                                        and g.res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7)
                                ) = 0,
                                1,
                                0
                            ) AS cliente
                        FROM
                            (
                                SELECT 
                                    cli_id,
                                    cuenta,
                                    date(ges_cli_fec) AS fec,
                                    (SELECT
                                            max(ges_cli_id) AS maxid
                                        FROM
                                            gestion_cliente
                                        WHERE
                                            date(ges_cli_fec)<=DATE(g.ges_cli_fec)
                                        and cli_id_FK=cli_id
                                    ) AS MAXGES,
                                    sum(if(res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7),1,0)) as cli
                                FROM
                                    gestion_cliente g
                                INNER JOIN cliente c ON c.cli_id = g.cli_id_FK
                                INNER JOIN indicadores.cartera_detalle ic ON c.cli_cod = ic.cuenta
                                WHERE
                                ic.car_id_FK=$car
                                AND c.car_id_fk=$car
                                and year(ges_cli_fec) = year(now())
                                and month(ges_cli_fec) = month(now()) - 1
                                and MONTH(fecha) = month(now()) - 1
                                and res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7)
                                GROUP BY cuenta,date(ges_cli_fec)
                            ) t
                            INNER JOIN gestion_cliente ult_ges ON t.MAXGES=ult_ges.ges_cli_id
                            WHERE res_id_FK not in (19,27,12,13,26,34,17,21)
                        )tt
                        GROUP BY fec
            "));

            $datos2=DB::select(DB::raw("
                            SELECT
                            date(ges_cli_fec) AS fec,
                            count( ic.car_id_fk) AS can_gestiones
                            FROM
                                gestion_cliente g
                            INNER JOIN cliente c ON c.cli_id = g.cli_id_FK
                            INNER JOIN indicadores.cartera_detalle ic ON c.cli_cod = ic.cuenta
                            WHERE
                            ic.car_id_FK=$car
                            and year(ges_cli_fec) = year(now())
                            and month(ges_cli_fec) = month(now()) -1
                            and MONTH(fecha) = month(now()) -1
                            and res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7,34,17,21)
                            GROUP BY
                                date(ges_cli_fec)
            "));

            $total3=DB::select(DB::raw("
                            SELECT
                            fec,
                            SUM(cliente) as can_clientes
                        FROM
                        (SELECT
                            cli_id,
                            fec,
                            IF (
                                (
                                    SELECT
                                            count(*)
                                        FROM
                                            gestion_cliente g
                                        WHERE
                                        year(g.ges_cli_fec) = year(now())
                                        and month(g.ges_cli_fec) = month(now())
                                        AND date(g.ges_cli_fec)<fec
                                        AND g.cli_id_FK=cli_id
                                        and g.res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7)
                                ) = 0,
                                1,
                                0
                            ) AS cliente
                        FROM
                            (
                                SELECT 
                                    cli_id,
                                    cuenta,
                                    date(ges_cli_fec) AS fec,
                                    (SELECT
                                            max(ges_cli_id) AS maxid
                                        FROM
                                            gestion_cliente
                                        WHERE
                                            date(ges_cli_fec)<=DATE(g.ges_cli_fec)
                                        and cli_id_FK=cli_id
                                    ) AS MAXGES,
                                    sum(if(res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7),1,0)) as cli
                                FROM
                                    gestion_cliente g
                                INNER JOIN cliente c ON c.cli_id = g.cli_id_FK
                                INNER JOIN indicadores.cartera_detalle ic ON c.cli_cod = ic.cuenta
                                WHERE
                                ic.car_id_FK=$car
                                AND c.car_id_fk=$car
                                and year(ges_cli_fec) = year(now())
                                and month(ges_cli_fec) = month(now())
                                and MONTH(fecha) = month(now())
                                and res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7)
                                GROUP BY cuenta,date(ges_cli_fec)
                            ) t
                            INNER JOIN gestion_cliente ult_ges ON t.MAXGES=ult_ges.ges_cli_id
                            WHERE res_id_FK not in (19,27,12,13,26,34,17,21)
                        )tt
                        GROUP BY fec
            "));

            $datos3=DB::select(DB::raw("
                            SELECT
                            date(ges_cli_fec) AS fec,
                            count( ic.car_id_fk) AS can_gestiones
                            FROM
                                gestion_cliente g
                            INNER JOIN cliente c ON c.cli_id = g.cli_id_FK
                            INNER JOIN indicadores.cartera_detalle ic ON c.cli_cod = ic.cuenta
                            WHERE
                            ic.car_id_FK=$car
                            and year(ges_cli_fec) = year(now())
                            and month(ges_cli_fec) = month(now())
                            and MONTH(fecha) = month(now())
                            and res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7,34,17,21)
                            GROUP BY
                                date(ges_cli_fec)
            "));

            
        
        //dd($res);
        //return $res;
        //return response()->json(['totales' => $totales, 'datos' => $datos]);
        return response()->json(['total1' => $total1, 'datos1' => $datos1,'total2' => $total2, 'datos2' => $datos2 ,'total3' => $total3, 'datos3' => $datos3]);
    }

    public static function buscarTasa($car){
        //can_clientes es cantidad de gestiones promesas de pago
        /*(select count(distinct(cuenta)) from gestion_cliente AS g
                RIGHT JOIN cliente AS c ON c.CLI_ID = g.CLI_ID_FK
                INNER JOIN cartera AS ca ON ca.CAR_ID = c.CAR_ID_FK
                INNER JOIN indicadores.cartera_detalle AS cd ON c.CLI_COD = cd.CUENTA 
                where cd.car_id_fk=$car
                and res_id_fk in (38,6,22,41,2,37,18,10,1,8,43,39,7)
                and year(fecha) = year(now())
                and month(fecha) BETWEEN month(now()) - 2 and month(now())) as total_cuentas */

        /*$totales=DB::select(DB::raw("
            select month(fecha) as mes, count(distinct(cuenta)) as total_cuentas from gestion_cliente AS g
            RIGHT JOIN cliente AS c ON c.CLI_ID = g.CLI_ID_FK
            INNER JOIN cartera AS ca ON ca.CAR_ID = c.CAR_ID_FK
            INNER JOIN indicadores.cartera_detalle AS cd ON c.CLI_COD = cd.CUENTA 
            where cd.car_id_fk=$car
            and res_id_fk in (38,6,22,41,2,37,18,10,1,8,43,39,7)
            and year(fecha) = year(now())
            and month(fecha) BETWEEN month(now()) - 2 and month(now())
            GROUP by month(fecha)
        "));

        $datos=DB::select(DB::raw("
                SELECT date(ges_cli_fec) AS fec, COUNT(cartera) AS can_clientes,
                COUNT(DISTINCT(CLI_COD)) AS cant_clientes
                FROM gestion_cliente AS GES
                RIGHT JOIN cliente AS CLI ON CLI.CLI_ID = GES.CLI_ID_FK
                INNER JOIN cartera AS CAR ON CAR.CAR_ID = CLI.CAR_ID_FK
                INNER JOIN indicadores.cartera_detalle AS CDET ON CLI.CLI_COD = CDET.CUENTA
                WHERE CDET.car_id_fk = $car
                and year(ges_cli_fec) = year(now())
                and month(ges_cli_fec) BETWEEN month(now()) - 2 and month(now())
                and res_id_fk in (1,43)
                and year(fecha) = year(now())
                and month(fecha) BETWEEN month(now()) - 2 and month(now())
                GROUP by DATE(ges_cli_fec)
            "));*/

            $total1=DB::select(DB::raw("
                            SELECT
                            fec,
                            SUM(cliente) as can_clientes
                        FROM
                        (SELECT
                            cli_id,
                            fec,
                            IF (
                                (
                                    SELECT
                                            count(*)
                                        FROM
                                            gestion_cliente g
                                        WHERE
                                        year(g.ges_cli_fec) = year(now())
                                        and month(g.ges_cli_fec) = month(now()) - 2
                                        AND date(g.ges_cli_fec)<fec
                                        AND g.cli_id_FK=cli_id
                                        and g.res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7)
                                ) = 0,
                                1,
                                0
                            ) AS cliente
                        FROM
                            (
                                SELECT 
                                    cli_id,
                                    cuenta,
                                    date(ges_cli_fec) AS fec,
                                    (SELECT
                                            max(ges_cli_id) AS maxid
                                        FROM
                                            gestion_cliente
                                        WHERE
                                            date(ges_cli_fec)<=DATE(g.ges_cli_fec)
                                        and cli_id_FK=cli_id
                                    ) AS MAXGES,
                                    sum(if(res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7),1,0)) as cli
                                FROM
                                    gestion_cliente g
                                INNER JOIN cliente c ON c.cli_id = g.cli_id_FK
                                INNER JOIN indicadores.cartera_detalle ic ON c.cli_cod = ic.cuenta
                                WHERE
                                ic.car_id_FK=$car
                                AND c.car_id_fk=$car
                                and year(ges_cli_fec) = year(now())
                                and month(ges_cli_fec) = month(now()) - 2
                                and MONTH(fecha) = month(now()) - 2
                                and res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7)
                                GROUP BY cuenta,date(ges_cli_fec)
                            ) t
                            INNER JOIN gestion_cliente ult_ges ON t.MAXGES=ult_ges.ges_cli_id
                            WHERE res_id_FK not in (19,27,12,13,26,34,17,21)
                        )tt
                        GROUP BY fec
            "));

            $datos1=DB::select(DB::raw("
                        SELECT
                        date(ges_cli_fec) AS fec,
                        count( ic.car_id_fk) AS can_promesas
                        FROM
                            gestion_cliente g
                        INNER JOIN cliente c ON c.cli_id = g.cli_id_FK
                        INNER JOIN indicadores.cartera_detalle ic ON c.cli_cod = ic.cuenta
                        WHERE
                        ic.car_id_FK=$car
                        and year(ges_cli_fec) = year(now())
                        and month(ges_cli_fec) = month(now())-2
                        and MONTH(fecha) = month(now())-2
                        and res_id_FK in (1)
                        GROUP BY
                            date(ges_cli_fec)
            "));

            $total2=DB::select(DB::raw("
                            SELECT
                            fec,
                            SUM(cliente) as can_clientes
                        FROM
                        (SELECT
                            cli_id,
                            fec,
                            IF (
                                (
                                    SELECT
                                            count(*)
                                        FROM
                                            gestion_cliente g
                                        WHERE
                                        year(g.ges_cli_fec) = year(now())
                                        and month(g.ges_cli_fec) = month(now()) - 1
                                        AND date(g.ges_cli_fec)<fec
                                        AND g.cli_id_FK=cli_id
                                        and g.res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7)
                                ) = 0,
                                1,
                                0
                            ) AS cliente
                        FROM
                            (
                                SELECT 
                                    cli_id,
                                    cuenta,
                                    date(ges_cli_fec) AS fec,
                                    (SELECT
                                            max(ges_cli_id) AS maxid
                                        FROM
                                            gestion_cliente
                                        WHERE
                                            date(ges_cli_fec)<=DATE(g.ges_cli_fec)
                                        and cli_id_FK=cli_id
                                    ) AS MAXGES,
                                    sum(if(res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7),1,0)) as cli
                                FROM
                                    gestion_cliente g
                                INNER JOIN cliente c ON c.cli_id = g.cli_id_FK
                                INNER JOIN indicadores.cartera_detalle ic ON c.cli_cod = ic.cuenta
                                WHERE
                                ic.car_id_FK=$car
                                AND c.car_id_fk=$car
                                and year(ges_cli_fec) = year(now())
                                and month(ges_cli_fec) = month(now()) - 1
                                and MONTH(fecha) = month(now()) - 1
                                and res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7)
                                GROUP BY cuenta,date(ges_cli_fec)
                            ) t
                            INNER JOIN gestion_cliente ult_ges ON t.MAXGES=ult_ges.ges_cli_id
                            WHERE res_id_FK not in (19,27,12,13,26,34,17,21)
                        )tt
                        GROUP BY fec
            "));

            $datos2=DB::select(DB::raw("
                        SELECT
                        date(ges_cli_fec) AS fec,
                        count( ic.car_id_fk) AS can_promesas
                        FROM
                            gestion_cliente g
                        INNER JOIN cliente c ON c.cli_id = g.cli_id_FK
                        INNER JOIN indicadores.cartera_detalle ic ON c.cli_cod = ic.cuenta
                        WHERE
                        ic.car_id_FK=$car
                        and year(ges_cli_fec) = year(now())
                        and month(ges_cli_fec) = month(now())-1
                        and MONTH(fecha) = month(now())-1
                        and res_id_FK in (1)
                        GROUP BY
                            date(ges_cli_fec)
            "));

            $total3=DB::select(DB::raw("
                            SELECT
                            fec,
                            SUM(cliente) as can_clientes
                        FROM
                        (SELECT
                            cli_id,
                            fec,
                            IF (
                                (
                                    SELECT
                                            count(*)
                                        FROM
                                            gestion_cliente g
                                        WHERE
                                        year(g.ges_cli_fec) = year(now())
                                        and month(g.ges_cli_fec) = month(now())
                                        AND date(g.ges_cli_fec)<fec
                                        AND g.cli_id_FK=cli_id
                                        and g.res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7)
                                ) = 0,
                                1,
                                0
                            ) AS cliente
                        FROM
                            (
                                SELECT 
                                    cli_id,
                                    cuenta,
                                    date(ges_cli_fec) AS fec,
                                    (SELECT
                                            max(ges_cli_id) AS maxid
                                        FROM
                                            gestion_cliente
                                        WHERE
                                            date(ges_cli_fec)<=DATE(g.ges_cli_fec)
                                        and cli_id_FK=cli_id
                                    ) AS MAXGES,
                                    sum(if(res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7),1,0)) as cli
                                FROM
                                    gestion_cliente g
                                INNER JOIN cliente c ON c.cli_id = g.cli_id_FK
                                INNER JOIN indicadores.cartera_detalle ic ON c.cli_cod = ic.cuenta
                                WHERE
                                ic.car_id_FK=$car
                                AND c.car_id_fk=$car
                                and year(ges_cli_fec) = year(now())
                                and month(ges_cli_fec) = month(now())
                                and MONTH(fecha) = month(now())
                                and res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7)
                                GROUP BY cuenta,date(ges_cli_fec)
                            ) t
                            INNER JOIN gestion_cliente ult_ges ON t.MAXGES=ult_ges.ges_cli_id
                            WHERE res_id_FK not in (19,27,12,13,26,34,17,21)
                        )tt
                        GROUP BY fec
            "));

            $datos3=DB::select(DB::raw("
                        SELECT
                        date(ges_cli_fec) AS fec,
                        count( ic.car_id_fk) AS can_promesas
                        FROM
                            gestion_cliente g
                        INNER JOIN cliente c ON c.cli_id = g.cli_id_FK
                        INNER JOIN indicadores.cartera_detalle ic ON c.cli_cod = ic.cuenta
                        WHERE
                        ic.car_id_FK=$car
                        and year(ges_cli_fec) = year(now())
                        and month(ges_cli_fec) = month(now())
                        and MONTH(fecha) = month(now())
                        and res_id_FK in (1)
                        GROUP BY
                            date(ges_cli_fec)
            "));
        
        //dd($res);
        //return $res;
        //return response()->json(['totales' => $totales, 'datos' => $datos]);
        return response()->json(['total1' => $total1, 'datos1' => $datos1,'total2' => $total2, 'datos2' => $datos2 ,'total3' => $total3, 'datos3' => $datos3]);
    }

    public static function buscarEfectividad($car){
        //can_clientes es cantidad de gestiones promesas de pago
        /* (select sum(pag_cli_mon) from pago_cliente_2 as pa
                RIGHT JOIN cliente AS c ON c.CLI_COD = pa.pag_cli_cod
                INNER JOIN cartera AS ca ON ca.CAR_ID = pa.CAR_ID_FK
                INNER JOIN gestion_cliente AS g ON g.cli_id_FK = c.cli_id
                INNER JOIN indicadores.cartera_detalle AS cd ON pa.pag_cli_cod = cd.CUENTA 
                where cd.car_id_fk=$car
                and res_id_fk in (1)
                and year(fecha) = year(now())
                and month(fecha) BETWEEN month(now()) - 2 and month(now())) as total_cuentas*/
        
        /*$totales=DB::select(DB::raw("
            select month(fecha) as mes, sum(pag_cli_mon) as total_cuentas from pago_cliente_2 as pa
            RIGHT JOIN cliente AS c ON c.CLI_COD = pa.pag_cli_cod
            INNER JOIN cartera AS ca ON ca.CAR_ID = pa.CAR_ID_FK
            INNER JOIN gestion_cliente AS g ON g.cli_id_FK = c.cli_id
            INNER JOIN indicadores.cartera_detalle AS cd ON pa.pag_cli_cod = cd.CUENTA 
            where cd.car_id_fk=$car
            and res_id_fk in (1)
            and year(fecha) = year(now())
            and month(fecha) BETWEEN month(now()) - 2 and month(now())
            GROUP by month(fecha)
        "));
        
        $datos=DB::select(DB::raw("
                SELECT date(pag_cli_fec) AS fec, sum(pag_cli_mon) AS can_clientes,
                COUNT(DISTINCT(CLI_COD)) AS cant_clientes
                FROM pago_cliente_2 as p
                RIGHT JOIN cliente AS CLI ON CLI.CLI_COD = p.pag_cli_cod
                INNER JOIN gestion_cliente AS ge ON ge.cli_id_FK = CLI.cli_id
                INNER JOIN cartera AS CAR ON CAR.CAR_ID = p.CAR_ID_FK
                INNER JOIN indicadores.cartera_detalle AS CDET ON p.pag_cli_cod = CDET.CUENTA
                WHERE CDET.car_id_fk = $car
                and year(pag_cli_fec) = year(now())
                and month(pag_cli_fec) BETWEEN month(now()) - 2 and month(now())
                and res_id_fk in (1)
                and year(fecha) = year(now())
                and month(fecha) BETWEEN month(now()) - 2 and month(now())
                GROUP by DATE(pag_cli_fec)
            "));*/

            $total1=DB::select(DB::raw("
                        select date(ges_cli_com_fec) as fec,sum(ges_cli_com_can) as can_clientes
                        from
                        (select cli_cod,ges_cli_fec,ges_cli_com_can,pag_cli_mon, pag_cli_fec,ges_cli_com_fec
                        FROM pago_cliente_2 as p
                        RIGHT JOIN cliente AS CLI ON CLI.CLI_COD = p.pag_cli_cod
                        INNER JOIN gestion_cliente AS ge ON ge.cli_id_FK = CLI.cli_id
                        INNER JOIN indicadores.cartera_detalle AS CDET ON p.pag_cli_cod = CDET.CUENTA
                        WHERE CDET.car_id_fk = $car
                        and year(pag_cli_fec) = year(now())
                        and month(pag_cli_fec) = month(now())-2
                        and year(ges_cli_com_fec) = year(now())
                        and month(ges_cli_com_fec) = month(now())-2
                        and year(fecha) = year(now())
                        and MONTH(fecha) = month(now())-2
                        and res_id_fk in (1,43)
                        GROUP BY cli_cod, date(ges_cli_com_fec)
                        ) a
                        group by fec
            "));

            $datos1=DB::select(DB::raw("
                        select date(ges_cli_com_fec) as fec,sum(ges_cli_com_can) can_gestiones
                        from
                        (select  cli_cod,ges_cli_fec,ges_cli_com_can, ges_cli_com_fec
                        from cliente AS CLI
                        INNER JOIN gestion_cliente AS ge ON ge.cli_id_FK = CLI.cli_id
                        INNER JOIN indicadores.cartera_detalle AS CDET ON CLI.cli_cod= CDET.CUENTA
                        WHERE CDET.car_id_fk = $car
                        and year(ges_cli_fec) = year(now())
                        and month(ges_cli_fec) = month(now())-2
                        and year(ges_cli_com_fec) = year(now())
                        and month(ges_cli_com_fec) = month(now())-2
                        and year(fecha) = year(now())
                        and MONTH(fecha) = month(now())-2
                        and res_id_fk in (1,43)) a
                        group by fec
            "));

            $total2=DB::select(DB::raw("
                        select date(ges_cli_com_fec) as fec,sum(ges_cli_com_can) as can_clientes
                        from
                        (select cli_cod,ges_cli_fec,ges_cli_com_can,pag_cli_mon, pag_cli_fec,ges_cli_com_fec
                        FROM pago_cliente_2 as p
                        RIGHT JOIN cliente AS CLI ON CLI.CLI_COD = p.pag_cli_cod
                        INNER JOIN gestion_cliente AS ge ON ge.cli_id_FK = CLI.cli_id
                        INNER JOIN indicadores.cartera_detalle AS CDET ON p.pag_cli_cod = CDET.CUENTA
                        WHERE CDET.car_id_fk = $car
                        and year(pag_cli_fec) = year(now())
                        and month(pag_cli_fec) = month(now())-1
                        and year(ges_cli_com_fec) = year(now())
                        and month(ges_cli_com_fec) = month(now())-1
                        and year(fecha) = year(now())
                        and MONTH(fecha) = month(now())-1
                        and res_id_fk in (1,43)
                        GROUP BY cli_cod, date(ges_cli_com_fec)
                        ) a
                        group by fec
            "));

            $datos2=DB::select(DB::raw("
                        select date(ges_cli_com_fec) as fec,sum(ges_cli_com_can) can_gestiones
                        from
                        (select  cli_cod,ges_cli_fec,ges_cli_com_can, ges_cli_com_fec
                        from cliente AS CLI
                        INNER JOIN gestion_cliente AS ge ON ge.cli_id_FK = CLI.cli_id
                        INNER JOIN indicadores.cartera_detalle AS CDET ON CLI.cli_cod= CDET.CUENTA
                        WHERE CDET.car_id_fk = $car
                        and year(ges_cli_fec) = year(now())
                        and month(ges_cli_fec) = month(now())-1
                        and year(ges_cli_com_fec) = year(now())
                        and month(ges_cli_com_fec) = month(now())-1
                        and year(fecha) = year(now())
                        and MONTH(fecha) = month(now())-1
                        and res_id_fk in (1,43)) a
                        group by fec
            "));
            $total3=DB::select(DB::raw("
                        select date(ges_cli_com_fec) as fec,sum(ges_cli_com_can) as can_clientes
                        from
                        (select cli_cod,ges_cli_fec,ges_cli_com_can,pag_cli_mon, pag_cli_fec,ges_cli_com_fec
                        FROM pago_cliente_2 as p
                        RIGHT JOIN cliente AS CLI ON CLI.CLI_COD = p.pag_cli_cod
                        INNER JOIN gestion_cliente AS ge ON ge.cli_id_FK = CLI.cli_id
                        INNER JOIN indicadores.cartera_detalle AS CDET ON p.pag_cli_cod = CDET.CUENTA
                        WHERE CDET.car_id_fk = $car
                        and year(pag_cli_fec) = year(now())
                        and month(pag_cli_fec) = month(now())
                        and year(ges_cli_com_fec) = year(now())
                        and month(ges_cli_com_fec) = month(now())
                        and year(fecha) = year(now())
                        and MONTH(fecha) = month(now())
                        and res_id_fk in (1,43)
                        GROUP BY cli_cod, date(ges_cli_com_fec)
                        ) a
                        group by fec
            "));

            $datos3=DB::select(DB::raw("
                        select date(ges_cli_com_fec) as fec,sum(ges_cli_com_can) can_gestiones
                        from
                        (select  cli_cod,ges_cli_fec,ges_cli_com_can, ges_cli_com_fec
                        from cliente AS CLI
                        INNER JOIN gestion_cliente AS ge ON ge.cli_id_FK = CLI.cli_id
                        INNER JOIN indicadores.cartera_detalle AS CDET ON CLI.cli_cod= CDET.CUENTA
                        WHERE CDET.car_id_fk = $car
                        and year(ges_cli_fec) = year(now())
                        and month(ges_cli_fec) = month(now())
                        and year(ges_cli_com_fec) = year(now())
                        and month(ges_cli_com_fec) = month(now())
                        and year(fecha) = year(now())
                        and MONTH(fecha) = month(now())
                        and res_id_fk in (1,43)) a
                        group by fec
            "));

            return response()->json(['total1' => $total1, 'datos1' => $datos1,'total2' => $total2, 'datos2' => $datos2 ,'total3' => $total3, 'datos3' => $datos3]);
        //dd($res);
        //return $res;
        //return response()->json(['totales' => $totales, 'datos' => $datos]);
    }

































































    public static function buscarContacto2(){

        $totales=DB::select(DB::raw("
        select month(fecha) as mes, count(distinct(cuenta)) as total_cuentas from indicadores.cartera_detalle 
        where car_id_fk=34
        and year(fecha) = year(now())
        and month(fecha) BETWEEN month(now()) - 2 and month(now())
        GROUP by  month(fecha)"));
        //$query1=$sub->get()->toArray();


        $datos=DB::select(DB::raw("
                SELECT date(ges_cli_fec) AS fec, COUNT(cartera) AS cant_gestiones,
                COUNT(DISTINCT(CLI_COD)) AS can_clientes
                FROM gestion_cliente AS GES
                RIGHT JOIN cliente AS CLI ON CLI.CLI_ID = GES.CLI_ID_FK
                INNER JOIN cartera AS CAR ON CAR.CAR_ID = CLI.CAR_ID_FK
                INNER JOIN indicadores.cartera_detalle AS CDET ON CLI.CLI_COD = CDET.CUENTA
                WHERE CDET.car_id_fk = 34
                and year(ges_cli_fec) = year(now())
                and month(ges_cli_fec) BETWEEN month(now()) - 2 and month(now())
                and res_id_fk in (18,5,17,21)
                and year(fecha) = year(now())
                and month(fecha) BETWEEN month(now()) - 2 and month(now())
                GROUP by DATE(ges_cli_fec)
            "));
            //$query2=$res->get()->toArray();
        
            //$da=array_collapse()
        //$datos = array_collapse([$sub,$res]);
        //dd($datos);
        //return $datos;
        return response()->json(['totales' => $totales, 'datos' => $datos]);
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
