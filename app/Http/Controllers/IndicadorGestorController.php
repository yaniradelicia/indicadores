<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class IndicadorGestorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sql="
            select car_id_FK,car_nom as cartera
            from sub_empleado as e
            inner join cartera as c
            on e.car_id_FK=c.car_id
            where emp_est=0 and car_est=0 and car_pas=0
            group by car_id_FK
            order by car_nom asc
        ";
        $cartera=DB::select(DB::raw($sql));
        return view('gestores.indicador_gestor',compact('cartera'));
    }

    public function cargarGestoresTotal()
    {
        $sql="
            select car_id_FK as id,car_nom, emp_nom,emp_firma, emp_id
            from sub_empleado as e
            inner join cartera as c
            on e.car_id_FK=c.car_id
            where
                emp_est=0 and car_est=0 and car_pas=0
            order by emp_nom asc
        ";
        $gestores=DB::select(DB::raw($sql));
        return $gestores;
    }

    public function cargarGestores($car)
    {
        $sql="
            select car_id_FK as id,car_nom, emp_nom,emp_firma, emp_id
            from sub_empleado as e
            inner join cartera as c
            on e.car_id_FK=c.car_id
            where car_id_FK=$car
            and emp_est=0 and car_est=0 and car_pas=0
            order by emp_nom asc
        ";
        $gestores=DB::select(DB::raw($sql));
        return $gestores;
    }

    public static function buscarGestorGestiones($car,$firma,$tip,$fec_i, $fec_f){
        
        if($car==0){
            $sqlCar="
                select car_id_FK from sub_empleado
                WHERE emp_firma like '%$firma'
            ";
            $cartera=DB::select(DB::raw($sqlCar));
            foreach($cartera as $c){
                $car_id = $c->car_id_FK;
            }
            $car=$car_id;
        }else{
            $car=$car;
        }
        //dd($car);
        
        $sql="
            select 
                t.$tip as tipo,
                count(cli_cod) as cant_clientes,
                sum(cant_gestion) as cant_gestion,
                sum(cde.capital) as total_capital,
                sum(cde.saldo_deuda) as total_deuda,
                sum(cde.monto_camp) as total_importe,
                format(sum(cant_gestion)/count(cli_cod),2) as intensidad
            from
            (
            select 
                cli_cod, 
                if(tramo<=2016,2016,tramo) as tramo,
                ges_cli_det as detalle,
                date_format( ges_cli_fec, '%Y-%m-%d') as fecha_gestion,
                count(cartera) as cant_gestion,
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
                dep_ind,prioridad,dep,entidades,score,rango_sueldo
                from gestion_cliente as g
                inner join cliente as c on g.cli_id_FK=c.cli_id
                inner join indicadores.cartera_detalle as cd on c.cli_cod=cd.cuenta
                where cd.car_id_fk=$car
                and (date_format(ges_cli_fec,'%Y-%m-%d')) between '$fec_i' and '$fec_f'
                and (date_format(fecha,'%Y-%m'))=(date_format('$fec_i','%Y-%m'))
                and ges_cli_det like '%$firma'
                group by cli_cod
            ) t
            inner join indicadores.cartera_detalle as cde on t.cli_cod=cde.cuenta
            where 
                cde.car_id_fk=$car
                and (date_format(fecha_gestion,'%Y-%m-%d')) between '$fec_i' and '$fec_f'
                and (date_format(fecha,'%Y-%m'))=(date_format('$fec_i','%Y-%m'))
            group by tipo
        ";
        $gestiones=DB::select(DB::raw($sql));
        return $gestiones;
    }

    public static function buscarGestorPDPS($car,$firma,$tip,$fec_i, $fec_f){
        if($car==0){
            $sqlCar="
                select car_id_FK from sub_empleado
                WHERE emp_firma like '%$firma'
            ";
            $cartera=DB::select(DB::raw($sqlCar));
            foreach($cartera as $c){
                $car_id = $c->car_id_FK;
            }
            $car=$car_id;
        }else{
            $car=$car;
        }

        $sql="
            select 
                t.$tip as tipo,
                count(cli_cod) as cant_clientes,
				sum(monto_pdp) as monto,
				sum(can_pdp) as cantidad,
                sum(cde.capital) as total_capital,
                sum(cde.saldo_deuda) as total_deuda,
                sum(cde.monto_camp) as total_importe
            from
                (
                select 
                    cli_cod, 
                    if(tramo<=2016,2016,tramo) as tramo,
                    ges_cli_det as detalle,
                    sum(ges_cli_com_can) as monto_pdp,
                    count(ges_cli_com_can) as can_pdp,
                    date_format( ges_cli_fec, '%Y-%m-%d') as fecha_gestion,
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
                    dep_ind,prioridad,dep,entidades,score,rango_sueldo
                    from gestion_cliente as g
                    inner join cliente as c on g.cli_id_FK=c.cli_id
                    inner join indicadores.cartera_detalle as cd on c.cli_cod=cd.cuenta
                    where cd.car_id_fk=$car
                    and (date_format(ges_cli_fec,'%Y-%m-%d')) between '$fec_i' and '$fec_f'
                    and (date_format(fecha,'%Y-%m'))=(date_format('$fec_i','%Y-%m'))
                    and res_id_fk in (1,43)
                    and ges_cli_det like '%$firma'
                    group by cli_cod
                ) t
            inner join indicadores.cartera_detalle as cde on t.cli_cod=cde.cuenta
            where 
                cde.car_id_fk=$car
                and (date_format(fecha_gestion,'%Y-%m-%d')) between '$fec_i' and '$fec_f'
                and (date_format(fecha,'%Y-%m'))=(date_format('$fec_i','%Y-%m'))
            group by tipo
        ";
        $pdps=DB::select(DB::raw($sql));
        return $pdps;
    }

    public static function buscarGestorCONF($car,$firma,$tip,$fec_i, $fec_f){
        if($car==0){
            $sqlCar="
                select car_id_FK from sub_empleado
                WHERE emp_firma like '%$firma'
            ";
            $cartera=DB::select(DB::raw($sqlCar));
            foreach($cartera as $c){
                $car_id = $c->car_id_FK;
            }
            $car=$car_id;
        }else{
            $car=$car;
        }

        $sql="
            select 
                t.$tip as tipo,
                count(cli_cod) as cant_clientes,
				sum(monto_conf) as monto,
				sum(can_conf) as cantidad,
                sum(cde.capital) as total_capital,
                sum(cde.saldo_deuda) as total_deuda,
                sum(cde.monto_camp) as total_importe
            from
                (
                select 
                    cli_cod, 
                    if(tramo<=2016,2016,tramo) as tramo,
                    ges_cli_det as detalle,
                    sum(ges_cli_conf_can) as monto_conf,
                    count(ges_cli_conf_can) as can_conf,
                    date_format( ges_cli_fec, '%Y-%m-%d') as fecha_gestion,
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
                    dep_ind,prioridad,dep,entidades,score,rango_sueldo
                    from gestion_cliente as g
                    inner join cliente as c on g.cli_id_FK=c.cli_id
                    inner join indicadores.cartera_detalle as cd on c.cli_cod=cd.cuenta
                    where cd.car_id_fk=$car
                    and (date_format(ges_cli_fec,'%Y-%m-%d')) between '$fec_i' and '$fec_f'
                    and (date_format(fecha,'%Y-%m'))=(date_format('$fec_i','%Y-%m'))
                    and res_id_fk in (2)
                    and ges_cli_det like '%$firma'
                    group by cli_cod
                ) t
            inner join indicadores.cartera_detalle as cde on t.cli_cod=cde.cuenta
            where 
                cde.car_id_fk=$car
                and (date_format(fecha_gestion,'%Y-%m-%d')) between '$fec_i' and '$fec_f'
                and (date_format(fecha,'%Y-%m'))=(date_format('$fec_i','%Y-%m'))
            group by tipo
        ";
        $conf=DB::select(DB::raw($sql));
        return $conf;
    }

    public static function buscarGestorUbic($car,$firma,$fec_i, $fec_f){
        /*$sql="
            SELECT
            ubic as tipo,
            count(cli_cod) as cant_clientes,
            sum(cant_gestion) as cant_gestion,
            sum(cde.capital) as total_capital,
            sum(cde.saldo_deuda) as total_deuda,
            sum(cde.monto_camp) as total_importe,
            format(sum(cant_gestion)/count(cli_cod),2) as intensidad
            FROM
                (
                    SELECT
                    cli_cod,
                    if(tramo<=2016,2016,tramo) as tramo,
                    ges_cli_det as detalle,
                    date_format( ges_cli_fec, '%Y-%m-%d' ) as ult_gestion,
                    count(cli_cod) as cant_gestion,
                    (CASE 
                        WHEN res_id_FK IN ( 38, 6, 22, 41 ) THEN 'CRFN'
                        WHEN res_id_FK IN ( 2,37,33,10,1,8,43,39,7,3,5,9,34,17,21,18,28,30,35,36,46,47,48,49 ) THEN 'CONTACTO'
                        WHEN res_id_FK IN ( 32 ) THEN 'NO DISPONIBLE'
                        WHEN res_id_FK IN ( 19,27,12,26,13,4,11,12,20,14,15,16,23,24,29,31 ) THEN 'ILOCALIZADO'
                        WHEN res_id_FK IN ( 45,44, 25 ) THEN 'NO CONTACTO'
                        ELSE 'NO ENCONTRADO'
                    END) AS ubic
                    FROM cliente as c
                    INNER JOIN indicadores.cartera_detalle as cd on c.cli_cod=cd.cuenta
                    INNER JOIN (select max(ges_cli_id) as maxid,cli_id_FK 
                                from gestion_cliente 
                                where date(ges_cli_fec) between '$fec_i' and '$fec_f' 
                                GROUP BY cli_id_FK) as g
                    on g.cli_id_FK=c.cli_id
                    left JOIN gestion_cliente as gg on gg.ges_cli_id=g.maxid
                    WHERE cd.car_id_fk=$car
                    and (date_format(ges_cli_fec,'%Y-%m-%d')) between '$fec_i' and '$fec_f'
                    and (date_format(fecha,'%Y-%m'))=(date_format('$fec_i','%Y-%m'))
                    and ges_cli_det like '%$firma'
                    group by cli_cod
                ) t
            inner join indicadores.cartera_detalle as cde on t.cli_cod=cde.cuenta
            where 
                cde.car_id_fk=$car
                and (date_format(ult_gestion,'%Y-%m-%d')) between '$fec_i' and '$fec_f'
                and (date_format(fecha,'%Y-%m'))=(date_format('$fec_i','%Y-%m'))
            group by ubic
        ";*/
        if($car==0){
            $sqlCar="
                select car_id_FK from sub_empleado
                WHERE emp_firma like '%$firma'
            ";
            $cartera=DB::select(DB::raw($sqlCar));
            foreach($cartera as $c){
                $car_id = $c->car_id_FK;
            }
            $car=$car_id;
        }else{
            $car=$car;
        }

        $sql="
            select 
            count(cli_cod) as cant_clientes,
            sum(cant_gestion) as cant_gestion,
            sum(cde.capital) as total_capital,
            sum(cde.saldo_deuda) as total_deuda,
            sum(cde.monto_camp) as total_importe,
            (CASE 
                WHEN gg.res_id_FK IN ( 38, 6, 22, 41 ) THEN 'CRFN'
                WHEN gg.res_id_FK IN ( 2,37,33,10,1,8,43,39,7,3,5,9,34,17,21,18,28,30,35,36,46,47,48,49 ) THEN 'CONTACTO'
                WHEN gg.res_id_FK IN ( 32 ) THEN 'NO DISPONIBLE'
                WHEN gg.res_id_FK IN ( 19,27,12,26,13,4,11,12,20,14,15,16,23,24,29,31 ) THEN 'ILOCALIZADO'
                WHEN gg.res_id_FK IN ( 45,44, 25 ) THEN 'NO CONTACTO'
                ELSE 'NO ENCONTRADO'
            END) AS tipo,
            format(sum(cant_gestion)/count(cli_cod),2) as intensidad
            from
                (select 
                    cli_cod,cli_id,
                    ges_cli_det as detalle,
                    count(cartera) as cant_gestion
                from gestion_cliente as g
                inner join cliente as c on g.cli_id_FK=c.cli_id
                inner join indicadores.cartera_detalle as cd on c.cli_cod=cd.cuenta
                where cd.car_id_fk=$car
                    and (date_format(ges_cli_fec,'%Y-%m-%d')) between '$fec_i' and '$fec_f'
                    and (date_format(fecha,'%Y-%m'))=(date_format('$fec_i','%Y-%m'))
                    and ges_cli_det like '%$firma'
                group by cli_cod
                ) a
            inner join 
                (select max(ges_cli_id) as maxid,cli_id_FK,ges_cli_det
                    from gestion_cliente 
                    where date(ges_cli_fec) between '$fec_i' and '$fec_f'
                    and ges_cli_det like '%$firma'
                    GROUP BY cli_id_FK) as g
            on g.cli_id_FK=a.cli_id
            INNER JOIN gestion_cliente as gg on gg.ges_cli_id=g.maxid
            inner join indicadores.cartera_detalle as cde on a.cli_cod=cde.cuenta
            where 
                cde.car_id_fk=$car
                and (date_format(fecha,'%Y-%m'))=(date_format('$fec_i','%Y-%m'))
                and gg.ges_cli_det like '%$firma' and g.ges_cli_det like '%$firma'
            group by tipo
        ";

        $ubic=DB::select(DB::raw($sql));
        return $ubic;
    }

    public static function buscarGestorUbicPDPS($car,$firma,$fec_i, $fec_f){
        if($car==0){
            $sqlCar="
                select car_id_FK from sub_empleado
                WHERE emp_firma like '%$firma'
            ";
            $cartera=DB::select(DB::raw($sqlCar));
            foreach($cartera as $c){
                $car_id = $c->car_id_FK;
            }
            $car=$car_id;
        }else{
            $car=$car;
        }

        $sql="
            select 
            count(cli_cod) as cant_clientes,
            sum(monto_pdp) as monto,
            sum(can_pdp) as cantidad,
            sum(cde.capital) as total_capital,
            sum(cde.saldo_deuda) as total_deuda,
            sum(cde.monto_camp) as total_importe,
            (CASE 
                WHEN gg.res_id_FK IN ( 38, 6, 22, 41 ) THEN 'CRFN'
                WHEN gg.res_id_FK IN ( 2,37,33,10,1,8,43,39,7,3,5,9,34,17,21,18,28,30,35,36,46,47,48,49 ) THEN 'CONTACTO'
                WHEN gg.res_id_FK IN ( 32 ) THEN 'NO DISPONIBLE'
                WHEN gg.res_id_FK IN ( 19,27,12,26,13,4,11,12,20,14,15,16,23,24,29,31 ) THEN 'ILOCALIZADO'
                WHEN gg.res_id_FK IN ( 45,44, 25 ) THEN 'NO CONTACTO'
                ELSE 'NO ENCONTRADO'
            END) AS tipo
            from
                (select 
                    cli_cod,cli_id,
                    ges_cli_det as detalle,
                    count(cartera) as cant_gestion,
                    sum(ges_cli_com_can) as monto_pdp,
                    count(ges_cli_com_can) as can_pdp
                from gestion_cliente as g
                inner join cliente as c on g.cli_id_FK=c.cli_id
                inner join indicadores.cartera_detalle as cd on c.cli_cod=cd.cuenta
                where cd.car_id_fk=$car
                    and (date_format(ges_cli_fec,'%Y-%m-%d')) between '$fec_i' and '$fec_f'
                    and (date_format(fecha,'%Y-%m'))=(date_format('$fec_i','%Y-%m'))
                    and res_id_fk in (1,43)
                    and ges_cli_det like '%$firma'
                group by cli_cod
                ) a
            inner join 
                (select max(ges_cli_id) as maxid,cli_id_FK,ges_cli_det
                    from gestion_cliente 
                    where date(ges_cli_fec) between '$fec_i' and '$fec_f'
                    and ges_cli_det like '%$firma'
                    GROUP BY cli_id_FK) as g
            on g.cli_id_FK=a.cli_id
            INNER JOIN gestion_cliente as gg on gg.ges_cli_id=g.maxid
            inner join indicadores.cartera_detalle as cde on a.cli_cod=cde.cuenta
            where 
                cde.car_id_fk=$car
                and (date_format(fecha,'%Y-%m'))=(date_format('$fec_i','%Y-%m'))
                and gg.ges_cli_det like '%$firma'
            group by tipo
        ";
        $ubic=DB::select(DB::raw($sql));
        return $ubic;
    }

    public static function buscarGestorUbicCONF($car,$firma,$fec_i, $fec_f){
        if($car==0){
            $sqlCar="
                select car_id_FK from sub_empleado
                WHERE emp_firma like '%$firma'
            ";
            $cartera=DB::select(DB::raw($sqlCar));
            foreach($cartera as $c){
                $car_id = $c->car_id_FK;
            }
            $car=$car_id;
        }else{
            $car=$car;
        }
        
        $sql="
            select 
            count(cli_cod) as cant_clientes,
            sum(monto_conf) as monto,
            sum(can_conf) as cantidad,
            sum(cde.capital) as total_capital,
            sum(cde.saldo_deuda) as total_deuda,
            sum(cde.monto_camp) as total_importe,
            (CASE 
                WHEN gg.res_id_FK IN ( 38, 6, 22, 41 ) THEN 'CRFN'
                WHEN gg.res_id_FK IN ( 2,37,33,10,1,8,43,39,7,3,5,9,34,17,21,18,28,30,35,36,46,47,48,49 ) THEN 'CONTACTO'
                WHEN gg.res_id_FK IN ( 32 ) THEN 'NO DISPONIBLE'
                WHEN gg.res_id_FK IN ( 19,27,12,26,13,4,11,12,20,14,15,16,23,24,29,31 ) THEN 'ILOCALIZADO'
                WHEN gg.res_id_FK IN ( 45,44, 25 ) THEN 'NO CONTACTO'
                ELSE 'NO ENCONTRADO'
            END) AS tipo
            from
                (select 
                    cli_cod,cli_id,
                    ges_cli_det as detalle,
                    count(cartera) as cant_gestion,
                    sum(ges_cli_conf_can) as monto_conf,
		            count(ges_cli_conf_can) as can_conf
                from gestion_cliente as g
                inner join cliente as c on g.cli_id_FK=c.cli_id
                inner join indicadores.cartera_detalle as cd on c.cli_cod=cd.cuenta
                where cd.car_id_fk=$car
                    and (date_format(ges_cli_fec,'%Y-%m-%d')) between '$fec_i' and '$fec_f'
                    and (date_format(fecha,'%Y-%m'))=(date_format('$fec_i','%Y-%m'))
                    and res_id_fk in (2)
                    and ges_cli_det like '%$firma'
                group by cli_cod
                ) a
            inner join 
                (select max(ges_cli_id) as maxid,cli_id_FK,ges_cli_det
                    from gestion_cliente 
                    where date(ges_cli_fec) between '$fec_i' and '$fec_f'
                    and ges_cli_det like '%$firma'
                    GROUP BY cli_id_FK) as g
            on g.cli_id_FK=a.cli_id
            INNER JOIN gestion_cliente as gg on gg.ges_cli_id=g.maxid
            inner join indicadores.cartera_detalle as cde on a.cli_cod=cde.cuenta
            where 
                cde.car_id_fk=$car
                and (date_format(fecha,'%Y-%m'))=(date_format('$fec_i','%Y-%m'))
                and gg.ges_cli_det like '%$firma'
            group by tipo
        ";
        $ubic=DB::select(DB::raw($sql));
        return $ubic;
    }
    
}
