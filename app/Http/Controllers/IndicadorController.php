<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class IndicadorController extends Controller
{
    public static function listaEntidades($car){
        $sql="
            SELECT
                tag_valor as valor
            FROM
                creditoy_lotesms.tag_condicion
            WHERE 
                car_id_FK=:car
            and tag_tipo='entidades'
        ";
        $query=DB::connection('mysql2')->select(DB::raw($sql),array("car"=>$car));
        return $query;
    }

    public static function listaScore($car){
        $sql="
            SELECT
                tag_valor as valor
            FROM
                creditoy_lotesms.tag_condicion
            WHERE 
                car_id_FK=:car
            and tag_tipo='score'
        ";
        $query=DB::connection('mysql')->select(DB::raw($sql),array("car"=>$car));
        return $query;
    }

    public static function listaDep($car){
        $sql="
        SELECT DISTINCT dep as valor
        FROM indicadores.cartera_detalle
        WHERE car_id_fk=:car
        and DATE_FORMAT(fecha,'%Y-%m')=DATE_FORMAT(NOW(),'%Y-%m')
        ";
        $query=DB::connection('mysql')->select(DB::raw($sql),array("car"=>$car));
        return $query;
    }

    public static function listaTramos($car){
        $sql="
        SELECT DISTINCT tramo as valor
        FROM indicadores.cartera_detalle
        WHERE car_id_fk=:car
        and DATE_FORMAT(fecha,'%Y-%m')=DATE_FORMAT(NOW(),'%Y-%m')
        ";
        $query=DB::connection('mysql')->select(DB::raw($sql),array("car"=>$car));
        return $query;
    }

    public static function listaPrioridad($car){
        $sql="
        SELECT DISTINCT prioridad as valor
        FROM indicadores.cartera_detalle
        WHERE car_id_fk=:car
        and DATE_FORMAT(fecha,'%Y-%m')=DATE_FORMAT(NOW(),'%Y-%m')
        ";
        $query=DB::connection('mysql')->select(DB::raw($sql),array("car"=>$car));
        return $query;
    }

    public static function listaSituacion($car){
        $sql="
        SELECT DISTINCT dep_ind as valor
        FROM indicadores.cartera_detalle
        WHERE car_id_fk=:car
        and DATE_FORMAT(fecha,'%Y-%m')=DATE_FORMAT(NOW(),'%Y-%m')
        ";
        $query=DB::connection('mysql')->select(DB::raw($sql),array("car"=>$car));
        return $query;
    }

    public static function buscarCobertura($car,$asig,$estr,$valor){
        
        
        $sql=" ";
        $sqll=" ";
        $filtro=" ";
        if($asig=="0" && $asig!="nuevos"){
            $sql = $sql . " and 1=1 " ;
            $sqll=$sqll." and 1=1 ";
        }else if($asig!="0" && $asig!="nuevos"){
            $sql = $sql . " and call_pertenece like '%$asig%' " ;
            $sqll= $sqll . " and cal_nom like '%$asig%' ";
        }else if($asig="nuevos"){
            $filtro = $filtro . " INNER JOIN nuevo_cliente as n on n.cli_nuev_cod=c.cli_cod " ;
            $sql= $sql . " and nuevo like '%NUEVO%' ";
            $sqll=$sqll." and 1=1 ";
        }

        if($estr!='null' && $valor!='null'){
            if($estr=="saldo_deuda" || $estr=="capital" || $estr=="monto_camp"){
                if($valor=='1'){
                    $sql = $sql . " and $estr <500 " ;
                    $sqll= $sqll . " and $estr <500 ";
                }else if($valor=='2'){
                    $sql = $sql . " and $estr >= 500 and $estr < 1000 " ;
                    $sqll= $sqll . " and $estr >= 500 and $estr < 1000 ";
                }else if($valor=='3'){
                    $sql = $sql . " and $estr >= 1000 and $estr < 3000 " ;
                    $sqll= $sqll . " and $estr >= 1000 and $estr < 3000 ";
                }else{
                    $sql = $sql . " and $estr >= 3000 " ;
                    $sqll= $sqll . " and $estr >= 3000 ";
                }
            }else{
                $sql = $sql . " and $estr = '$valor' " ;
                $sqll= $sqll . " and $estr = '$valor' ";
            }
        }else{
            $sql = $sql . " and 1=1 " ;
            $sqll=$sqll." and 1=1 ";
        }

            $tsql1="
                select mes,total_cuentas
                FROM
                (
                select month(fecha) as mes, count(distinct(cuenta)) as total_cuentas 
                from indicadores.cartera_detalle as cd
                INNER JOIN cliente c ON cd.cuenta = c.cli_cod 
            ";
            $tsql1= $tsql1." ".$filtro." 
                left JOIN empleado as e on c.emp_tel_id_FK=e.emp_id
                left JOIN call_telefonica as cal on e.cal_id_FK=cal.cal_id
                where cd.car_id_fk=$car
                and year(fecha) = year(now())
                and month(fecha) = month(now()) 
            ";
            $tsql1= $tsql1." ".$sqll." 
                GROUP by month(fecha)
                UNION ALL
                select month(fecha) as mes, count(distinct(cuenta)) as total_cuentas 
                from indicadores.cartera_detalle as cd
                INNER JOIN cliente c ON cd.cuenta = c.cli_cod
                where cd.car_id_fk=$car
                and year(fecha) = year(now())
                and month(fecha) BETWEEN month(now()) - 2 and month(now())-1
            ";
            $tsql1= $tsql1." ".$sql." 
                GROUP by month(fecha)
                ) a
                ORDER BY mes ASC
            ";
            $totales=DB::connection('mysql2')->select(DB::raw($tsql1));

            $sql1="
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
            ";
            $sql1 = $sql1." ". $sql." 
                            GROUP BY
                                cuenta,date(ges_cli_fec)
                        ) t
                    )tt
                GROUP BY fec      
            ";
            $datos1=DB::connection('mysql2')->select(DB::raw($sql1));

            $sql2="
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
            ";
            $sql2 = $sql2." ". $sql." 
                            GROUP BY
                                cuenta,date(ges_cli_fec)
                        ) t
                    )tt
                GROUP BY fec      
            ";
            $datos2=DB::connection('mysql2')->select(DB::raw($sql2));

            $sql3="
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
            ";
            $sql3 = $sql3." ". $filtro." 
                            left JOIN empleado as e on c.emp_tel_id_FK=e.emp_id
                            left JOIN call_telefonica as cal on e.cal_id_FK=cal.cal_id
                            WHERE
                            ic.car_id_FK=$car
                            AND c.car_id_fk=$car
                            and ges_cli_acc in (1,2)
                            and year(ges_cli_fec) = year(now())
                            and month(ges_cli_fec) = month(now())
                            and MONTH(fecha) = month(now())
            ";
            $sql3 = $sql3." ". $sqll." 
                            GROUP BY
                                cuenta,date(ges_cli_fec)
                        ) t
                    )tt
                GROUP BY fec      
            ";
            $datos3=DB::connection('mysql2')->select(DB::raw($sql3));

            return response()->json(['totales' => $totales, 'datos1' => $datos1, 'datos2' => $datos2, 'datos3' => $datos3]);
        
        
    }

    public static function buscarContacto($car,$asig,$estr,$valor){
        $sql=" ";
        $sqll=" ";
        $filtro=" ";
        if($asig=="0" && $asig!="nuevos"){
            $sql = $sql . " and 1=1 " ;
            $sqll=$sqll." and 1=1 ";
        }else if($asig!="0" && $asig!="nuevos"){
            $sql = $sql . " and call_pertenece like '%$asig%' " ;
            $sqll= $sqll . " and cal_nom like '%$asig%' ";
        }else if($asig="nuevos"){
            $filtro = $filtro . " INNER JOIN nuevo_cliente as n on n.cli_nuev_cod=c.cli_cod " ;
            $sql= $sql . " and nuevo like '%NUEVO%' ";
            $sqll=$sqll." and 1=1 ";
        }
        //dd($valor);
        if($estr!='null' && $valor!='null'){
            if($estr=="saldo_deuda" || $estr=="capital" || $estr=="monto_camp"){
                if($valor=='1'){
                    $sql = $sql . " and $estr <500 " ;
                    $sqll= $sqll . " and $estr <500 ";
                }else if($valor=='2'){
                    $sql = $sql . " and $estr >= 500 and $estr < 1000 " ;
                    $sqll= $sqll . " and $estr >= 500 and $estr < 1000 ";
                }else if($valor=='3'){
                    $sql = $sql . " and $estr >= 1000 and $estr < 3000 " ;
                    $sqll= $sqll . " and $estr >= 1000 and $estr < 3000 ";
                }else{
                    $sql = $sql . " and $estr >= 3000 " ;
                    $sqll= $sqll . " and $estr >= 3000 ";
                }
            }else{
                $sql = $sql . " and $estr = '$valor' " ;
                $sqll= $sqll . " and $estr = '$valor' ";
            }
        }else{
            $sql = $sql . " and 1=1 " ;
            $sqll=$sqll." and 1=1 ";
        }

        $tsql1="
                select mes,total_cuentas
                FROM
                (
                select month(fecha) as mes, count(distinct(cuenta)) as total_cuentas
                from indicadores.cartera_detalle as cd
                INNER JOIN cliente c ON cd.cuenta = c.cli_cod
            ";
            $tsql1= $tsql1." ".$filtro." 
                left JOIN empleado as e on c.emp_tel_id_FK=e.emp_id
                left JOIN call_telefonica as cal on e.cal_id_FK=cal.cal_id
                where cd.car_id_fk=$car
                and year(fecha) = year(now())
                and month(fecha) = month(now()) 
            ";
            $tsql1= $tsql1." ".$sqll." 
                GROUP by month(fecha)
                UNION ALL
                select month(fecha) as mes, count(distinct(cuenta)) as total_cuentas 
                from indicadores.cartera_detalle as cd
                INNER JOIN cliente c ON cd.cuenta = c.cli_cod
                where cd.car_id_fk=$car
                and year(fecha) = year(now())
                and month(fecha) BETWEEN month(now()) - 2 and month(now())-1
            ";
            $tsql1= $tsql1." ".$sql." 
                GROUP by month(fecha)
                ) a
                ORDER BY mes ASC
            ";
            $totales=DB::connection('mysql2')->select(DB::raw($tsql1));

            $sql1="
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
                        left JOIN empleado as e on c.emp_tel_id_FK=e.emp_id
                        left JOIN call_telefonica as cal on e.cal_id_FK=cal.cal_id
                        WHERE
                        ic.car_id_FK=$car
                        AND c.car_id_fk=$car
                        and year(ges_cli_fec) = year(now())
                        and month(ges_cli_fec) = month(now()) - 2 
                        and MONTH(fecha) = month(now()) - 2
                        and res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7,34,17,21)
            ";

            $sql1 = $sql1." ". $sql." 
                        GROUP BY cuenta,date(ges_cli_fec)
                    ) t
                    INNER JOIN gestion_cliente ult_ges ON t.MAXGES=ult_ges.ges_cli_id
                    WHERE res_id_FK not in (19,27,12,13,26)
                )tt
                GROUP BY fec
            ";
            $datos1=DB::connection('mysql2')->select(DB::raw($sql1));

            $sql2="
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
                    left JOIN empleado as e on c.emp_tel_id_FK=e.emp_id
                    left JOIN call_telefonica as cal on e.cal_id_FK=cal.cal_id
                    WHERE
                    ic.car_id_FK=$car
                    AND c.car_id_fk=$car
                    and year(ges_cli_fec) = year(now())
                    and month(ges_cli_fec) = month(now()) - 1
                    and MONTH(fecha) = month(now()) - 1
                    and res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7,34,17,21)
            ";

            $sql2 = $sql2." ". $sql." 
                    GROUP BY cuenta,date(ges_cli_fec)
                ) t
                INNER JOIN gestion_cliente ult_ges ON t.MAXGES=ult_ges.ges_cli_id
                WHERE res_id_FK not in (19,27,12,13,26)
            )tt
            GROUP BY fec
            ";
            $datos2=DB::connection('mysql2')->select(DB::raw($sql2));

            $sql3="
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
            ";
            $sql3 = $sql3." ". $filtro."
                    left JOIN empleado as e on c.emp_tel_id_FK=e.emp_id
                    left JOIN call_telefonica as cal on e.cal_id_FK=cal.cal_id
                    WHERE
                    ic.car_id_FK=$car
                    AND c.car_id_fk=$car
                    and year(ges_cli_fec) = year(now())
                    and month(ges_cli_fec) = month(now())
                    and MONTH(fecha) = month(now())
                    and res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7,34,17,21)
            ";

            $sql3 = $sql3." ". $sqll." 
                        GROUP BY cuenta,date(ges_cli_fec)
                    ) t
                    INNER JOIN gestion_cliente ult_ges ON t.MAXGES=ult_ges.ges_cli_id
                    WHERE res_id_FK not in (19,27,12,13,26)
                )tt
                GROUP BY fec
            ";
            $datos3=DB::connection('mysql2')->select(DB::raw($sql3));

            return response()->json(['totales' => $totales, 'datos1' => $datos1, 'datos2' => $datos2, 'datos3' => $datos3]);
        
    }

    public static function buscarContactoEfectivo($car,$asig,$estr,$valor){
        $sql=" ";
        $sqll=" ";
        $filtro=" ";
        if($asig=="0" && $asig!="nuevos"){
            $sql = $sql . " and 1=1 " ;
            $sqll=$sqll." and 1=1 ";
        }else if($asig!="0" && $asig!="nuevos"){
            $sql = $sql . " and call_pertenece like '%$asig%' " ;
            $sqll= $sqll . " and cal_nom like '%$asig%' ";
        }else if($asig="nuevos"){
            $filtro = $filtro . " INNER JOIN nuevo_cliente as n on n.cli_nuev_cod=c.cli_cod " ;
            $sql= $sql . " and nuevo like '%NUEVO%' ";
            $sqll=$sqll." and 1=1 ";
        }
        //dd($valor);
        if($estr!='null' && $valor!='null'){
            if($estr=="saldo_deuda" || $estr=="capital" || $estr=="monto_camp"){
                if($valor=='1'){
                    $sql = $sql . " and $estr <500 " ;
                    $sqll= $sqll . " and $estr <500 ";
                }else if($valor=='2'){
                    $sql = $sql . " and $estr >= 500 and $estr < 1000 " ;
                    $sqll= $sqll . " and $estr >= 500 and $estr < 1000 ";
                }else if($valor=='3'){
                    $sql = $sql . " and $estr >= 1000 and $estr < 3000 " ;
                    $sqll= $sqll . " and $estr >= 1000 and $estr < 3000 ";
                }else{
                    $sql = $sql . " and $estr >= 3000 " ;
                    $sqll= $sqll . " and $estr >= 3000 ";
                }
            }else{
                $sql = $sql . " and $estr = '$valor' " ;
                $sqll= $sqll . " and $estr = '$valor' ";
            }
        }else{
            $sql = $sql . " and 1=1 " ;
            $sqll=$sqll." and 1=1 ";
        }

        
        $tsql1="
            select mes,total_cuentas
            FROM
            (
            select month(fecha) as mes, count(distinct(cuenta)) as total_cuentas
            from indicadores.cartera_detalle as cd
            INNER JOIN cliente c ON cd.cuenta = c.cli_cod
        ";
        $tsql1= $tsql1." ".$filtro." 
            left JOIN empleado as e on c.emp_tel_id_FK=e.emp_id
            left JOIN call_telefonica as cal on e.cal_id_FK=cal.cal_id
            where cd.car_id_fk=$car
            and year(fecha) = year(now())
            and month(fecha) = month(now()) 
        ";
        $tsql1= $tsql1." ".$sqll." 
            GROUP by month(fecha)
            UNION ALL
            select month(fecha) as mes, count(distinct(cuenta)) as total_cuentas 
            from indicadores.cartera_detalle as cd
            INNER JOIN cliente c ON cd.cuenta = c.cli_cod
            where cd.car_id_fk=$car
            and year(fecha) = year(now())
            and month(fecha) BETWEEN month(now()) - 2 and month(now())-1
        ";
        $tsql1= $tsql1." ".$sql." 
            GROUP by month(fecha)
            ) a
            ORDER BY mes ASC
        ";
        $totales=DB::connection('mysql2')->select(DB::raw($tsql1));

            $sql1="
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
            ";
            $sql1 = $sql1." ". $sql." 
                                GROUP BY cuenta,date(ges_cli_fec)
                            ) t
                            INNER JOIN gestion_cliente ult_ges ON t.MAXGES=ult_ges.ges_cli_id
                            WHERE res_id_FK not in (19,27,12,13,26,34,17,21)
                        )tt
                        GROUP BY fec
            ";

            $datos1=DB::connection('mysql2')->select(DB::raw($sql1));

            $sql2="
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
            ";
            $sql2 = $sql2." ". $sql." 
                                GROUP BY cuenta,date(ges_cli_fec)
                            ) t
                            INNER JOIN gestion_cliente ult_ges ON t.MAXGES=ult_ges.ges_cli_id
                            WHERE res_id_FK not in (19,27,12,13,26,34,17,21)
                        )tt
                        GROUP BY fec
            ";

            $datos2=DB::connection('mysql2')->select(DB::raw($sql2));

            $sql3="
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
            ";
            $sql3 = $sql3." ". $filtro."
                                left JOIN empleado as e on c.emp_tel_id_FK=e.emp_id
                                left JOIN call_telefonica as cal on e.cal_id_FK=cal.cal_id
                                WHERE
                                ic.car_id_FK=$car
                                AND c.car_id_fk=$car
                                and year(ges_cli_fec) = year(now())
                                and month(ges_cli_fec) = month(now()) 
                                and MONTH(fecha) = month(now()) 
                                and res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7)
            ";
            $sql3 = $sql3." ". $sqll." 
                                GROUP BY cuenta,date(ges_cli_fec)
                            ) t
                            INNER JOIN gestion_cliente ult_ges ON t.MAXGES=ult_ges.ges_cli_id
                            WHERE res_id_FK not in (19,27,12,13,26,34,17,21)
                        )tt
                        GROUP BY fec
            ";

            $datos3=DB::connection('mysql2')->select(DB::raw($sql3));

            return response()->json(['totales' => $totales, 'datos1' => $datos1, 'datos2' => $datos2, 'datos3' => $datos3]);
        
    }

    public static function buscarIntensidad($car,$asig,$estr,$valor){
        $sql=" ";
        $sqll=" ";
        $filtro=" ";
        if($asig=="0" && $asig!="nuevos"){
            $sql = $sql . " and 1=1 " ;
            $sqll=$sqll." and 1=1 ";
        }else if($asig!="0" && $asig!="nuevos"){
            $sql = $sql . " and call_pertenece like '%$asig%' " ;
            $sqll= $sqll . " and cal_nom like '%$asig%' ";
        }else if($asig="nuevos"){
            $filtro = $filtro . " INNER JOIN nuevo_cliente as n on n.cli_nuev_cod=c.cli_cod " ;
            $sql= $sql . " and nuevo like '%NUEVO%' ";
            $sqll=$sqll." and 1=1 ";
        }
        //dd($valor);
        if($estr!='null' && $valor!='null'){
            if($estr=="saldo_deuda" || $estr=="capital" || $estr=="monto_camp"){
                if($valor=='1'){
                    $sql = $sql . " and $estr <500 " ;
                    $sqll= $sqll . " and $estr <500 ";
                }else if($valor=='2'){
                    $sql = $sql . " and $estr >= 500 and $estr < 1000 " ;
                    $sqll= $sqll . " and $estr >= 500 and $estr < 1000 ";
                }else if($valor=='3'){
                    $sql = $sql . " and $estr >= 1000 and $estr < 3000 " ;
                    $sqll= $sqll . " and $estr >= 1000 and $estr < 3000 ";
                }else{
                    $sql = $sql . " and $estr >= 3000 " ;
                    $sqll= $sqll . " and $estr >= 3000 ";
                }
            }else{
                $sql = $sql . " and $estr = '$valor' " ;
                $sqll= $sqll . " and $estr = '$valor' ";
            }
        }else{
            $sql = $sql . " and 1=1 " ;
            $sqll=$sqll." and 1=1 ";
        }

        
            $tsql1="
                select mes,total_cuentas
                FROM
                (
                select month(fecha) as mes, count(distinct(cuenta)) as total_cuentas
                from indicadores.cartera_detalle as cd
                INNER JOIN cliente c ON cd.cuenta = c.cli_cod
            ";
            $tsql1= $tsql1." ".$filtro." 
                left JOIN empleado as e on c.emp_tel_id_FK=e.emp_id
                left JOIN call_telefonica as cal on e.cal_id_FK=cal.cal_id
                where cd.car_id_fk=$car
                and year(fecha) = year(now())
                and month(fecha) = month(now()) 
            ";
            $tsql1= $tsql1." ".$sqll." 
                GROUP by month(fecha)
                UNION ALL
                select month(fecha) as mes, count(distinct(cuenta)) as total_cuentas 
                from indicadores.cartera_detalle as cd
                INNER JOIN cliente c ON cd.cuenta = c.cli_cod
                where cd.car_id_fk=$car
                and year(fecha) = year(now())
                and month(fecha) BETWEEN month(now()) - 2 and month(now())-1
            ";
            $tsql1= $tsql1." ".$sql." 
                GROUP by month(fecha)
                ) a
                ORDER BY mes ASC
            ";
            $totales=DB::connection('mysql2')->select(DB::raw($tsql1));

            $sql1="
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
            ";
            $sql1 = $sql1." ". $sql." 
                GROUP BY
                    date(ges_cli_fec)
            ";
            $datos1=DB::connection('mysql2')->select(DB::raw($sql1));

            $sql2="
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
            ";
            $sql2 = $sql2." ". $sql." 
                GROUP BY
                    date(ges_cli_fec)
            ";
            $datos2=DB::connection('mysql2')->select(DB::raw($sql2));

            $sql3="
                SELECT
                date(ges_cli_fec) AS fec,
                count( ic.car_id_fk) AS can_clientes
                FROM
                    gestion_cliente g
                INNER JOIN cliente c ON c.cli_id = g.cli_id_FK
                INNER JOIN indicadores.cartera_detalle ic ON c.cli_cod = ic.cuenta
            ";
            $sql3 = $sql3." ". $filtro."
                left JOIN empleado as e on c.emp_tel_id_FK=e.emp_id
                left JOIN call_telefonica as cal on e.cal_id_FK=cal.cal_id
                WHERE
                ic.car_id_FK=$car
                and year(ges_cli_fec) = year(now())
                and month(ges_cli_fec) = month(now()) 
                and MONTH(fecha) = month(now())
            ";
            $sql3 = $sql3." ". $sqll." 
                GROUP BY
                    date(ges_cli_fec)
            ";
            $datos3=DB::connection('mysql2')->select(DB::raw($sql3));

            return response()->json(['totales' => $totales, 'datos1' => $datos1, 'datos2' => $datos2, 'datos3' => $datos3]);
        

    }

    public static function buscarIntensidadDirecta($car,$asig,$estr,$valor){
        $sql=" ";
        $sqll=" ";
        $filtro=" ";
        if($asig=="0" && $asig!="nuevos"){
            $sql = $sql . " and 1=1 " ;
            $sqll=$sqll." and 1=1 ";
        }else if($asig!="0" && $asig!="nuevos"){
            $sql = $sql . " and call_pertenece like '%$asig%' " ;
            $sqll= $sqll . " and cal_nom like '%$asig%' ";
        }else if($asig="nuevos"){
            $filtro = $filtro . " INNER JOIN nuevo_cliente as n on n.cli_nuev_cod=c.cli_cod " ;
            $sql= $sql . " and nuevo like '%NUEVO%' ";
            $sqll=$sqll." and 1=1 ";
        }
        //dd($valor);
        if($estr!='null' && $valor!='null'){
            if($estr=="saldo_deuda" || $estr=="capital" || $estr=="monto_camp"){
                if($valor=='1'){
                    $sql = $sql . " and $estr <500 " ;
                    $sqll= $sqll . " and $estr <500 ";
                }else if($valor=='2'){
                    $sql = $sql . " and $estr >= 500 and $estr < 1000 " ;
                    $sqll= $sqll . " and $estr >= 500 and $estr < 1000 ";
                }else if($valor=='3'){
                    $sql = $sql . " and $estr >= 1000 and $estr < 3000 " ;
                    $sqll= $sqll . " and $estr >= 1000 and $estr < 3000 ";
                }else{
                    $sql = $sql . " and $estr >= 3000 " ;
                    $sqll= $sqll . " and $estr >= 3000 ";
                }
            }else{
                $sql = $sql . " and $estr = '$valor' " ;
                $sqll= $sqll . " and $estr = '$valor' ";
            }
        }else{
            $sql = $sql . " and 1=1 " ;
            $sqll=$sqll." and 1=1 ";
        }

        
            $tsql1="
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
            
            ";
            $tsql1= $tsql1." ".$sql." 
                        GROUP BY cuenta,date(ges_cli_fec)
                    ) t
                    INNER JOIN gestion_cliente ult_ges ON t.MAXGES=ult_ges.ges_cli_id
                    WHERE res_id_FK not in (19,27,12,13,26,34,17,21)
                )tt
                GROUP BY fec
            ";

            $total1=DB::connection('mysql2')->select(DB::raw($tsql1));

            $sql1="
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
            ";
            $sql1 = $sql1." ". $sql." 
                GROUP BY
                    date(ges_cli_fec)
            ";

            $datos1=DB::connection('mysql2')->select(DB::raw($sql1));

            $tsql2="
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
            
            ";
            $tsql2= $tsql2." ".$sql." 
                        GROUP BY cuenta,date(ges_cli_fec)
                    ) t
                    INNER JOIN gestion_cliente ult_ges ON t.MAXGES=ult_ges.ges_cli_id
                    WHERE res_id_FK not in (19,27,12,13,26,34,17,21)
                )tt
                GROUP BY fec
            ";

            $total2=DB::connection('mysql2')->select(DB::raw($tsql2));

            $sql2="
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
            ";
            $sql2 = $sql2." ". $sql." 
                GROUP BY
                    date(ges_cli_fec)
            ";

            $datos2=DB::connection('mysql2')->select(DB::raw($sql2));

            $tsql3="
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
        ";
        $tsql3 = $tsql3." ". $filtro." 
                        left JOIN empleado as e on c.emp_tel_id_FK=e.emp_id
                        left JOIN call_telefonica as cal on e.cal_id_FK=cal.cal_id
                        WHERE
                        ic.car_id_FK=$car
                        AND c.car_id_fk=$car
                        and year(ges_cli_fec) = year(now())
                        and month(ges_cli_fec) = month(now()) 
                        and MONTH(fecha) = month(now())
                        and res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7)
            
            ";
            $tsql3= $tsql3." ".$sqll." 
                        GROUP BY cuenta,date(ges_cli_fec)
                    ) t
                    INNER JOIN gestion_cliente ult_ges ON t.MAXGES=ult_ges.ges_cli_id
                    WHERE res_id_FK not in (19,27,12,13,26,34,17,21)
                )tt
                GROUP BY fec
            ";

            $total3=DB::connection('mysql2')->select(DB::raw($tsql3));

            $sql3="
                SELECT
                date(ges_cli_fec) AS fec,
                count( ic.car_id_fk) AS can_gestiones
                FROM
                    gestion_cliente g
                INNER JOIN cliente c ON c.cli_id = g.cli_id_FK
                INNER JOIN indicadores.cartera_detalle ic ON c.cli_cod = ic.cuenta
            ";
            $sql3 = $sql3." ". $filtro." 
                left JOIN empleado as e on c.emp_tel_id_FK=e.emp_id
                left JOIN call_telefonica as cal on e.cal_id_FK=cal.cal_id
                WHERE
                ic.car_id_FK=$car
                and year(ges_cli_fec) = year(now())
                and month(ges_cli_fec) = month(now()) 
                and MONTH(fecha) = month(now()) 
                and res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7,34,17,21)
            ";
            $sql3 = $sql3." ". $sqll." 
                GROUP BY
                    date(ges_cli_fec)
            ";

            $datos3=DB::connection('mysql2')->select(DB::raw($sql3));

            return response()->json(['total1' => $total1, 'datos1' => $datos1,'total2' => $total2, 'datos2' => $datos2 ,'total3' => $total3, 'datos3' => $datos3]);
        
    }

    public static function buscarTasa($car,$asig,$estr,$valor){
        $sql=" ";
        $sqll=" ";
        $filtro=" ";
        if($asig=="0" && $asig!="nuevos"){
            $sql = $sql . " and 1=1 " ;
            $sqll=$sqll." and 1=1 ";
        }else if($asig!="0" && $asig!="nuevos"){
            $sql = $sql . " and call_pertenece like '%$asig%' " ;
            $sqll= $sqll . " and cal_nom like '%$asig%' ";
        }else if($asig="nuevos"){
            $filtro = $filtro . " INNER JOIN nuevo_cliente as n on n.cli_nuev_cod=c.cli_cod " ;
            $sql= $sql . " and nuevo like '%NUEVO%' ";
            $sqll=$sqll." and 1=1 ";
        }
        //dd($valor);
        if($estr!='null' && $valor!='null'){
            if($estr=="saldo_deuda" || $estr=="capital" || $estr=="monto_camp"){
                if($valor=='1'){
                    $sql = $sql . " and $estr <500 " ;
                    $sqll= $sqll . " and $estr <500 ";
                }else if($valor=='2'){
                    $sql = $sql . " and $estr >= 500 and $estr < 1000 " ;
                    $sqll= $sqll . " and $estr >= 500 and $estr < 1000 ";
                }else if($valor=='3'){
                    $sql = $sql . " and $estr >= 1000 and $estr < 3000 " ;
                    $sqll= $sqll . " and $estr >= 1000 and $estr < 3000 ";
                }else{
                    $sql = $sql . " and $estr >= 3000 " ;
                    $sqll= $sqll . " and $estr >= 3000 ";
                }
            }else{
                $sql = $sql . " and $estr = '$valor' " ;
                $sqll= $sqll . " and $estr = '$valor' ";
            }
        }else{
            $sql = $sql . " and 1=1 " ;
            $sqll=$sqll." and 1=1 ";
        }

            $tsql1="
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
            ";
            $tsql1= $tsql1." ".$sql." 
                        GROUP BY cuenta,date(ges_cli_fec)
                    ) t
                    INNER JOIN gestion_cliente ult_ges ON t.MAXGES=ult_ges.ges_cli_id
                    WHERE res_id_FK not in (19,27,12,13,26,34,17,21)
                )tt
                GROUP BY fec
            ";

            $total1=DB::connection('mysql2')->select(DB::raw($tsql1));

            $sql1="
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

            ";
            $sql1 = $sql1." ". $sql." 
                GROUP BY
                    date(ges_cli_fec)
            ";

            $datos1=DB::connection('mysql2')->select(DB::raw($sql1));

            $tsql2="
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
            ";
            $tsql2= $tsql2." ".$sql." 
                        GROUP BY cuenta,date(ges_cli_fec)
                    ) t
                    INNER JOIN gestion_cliente ult_ges ON t.MAXGES=ult_ges.ges_cli_id
                    WHERE res_id_FK not in (19,27,12,13,26,34,17,21)
                )tt
                GROUP BY fec
            ";

            $total2=DB::connection('mysql2')->select(DB::raw($tsql2));

            $sql2="
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

            ";
            $sql2 = $sql2." ". $sql." 
                GROUP BY
                    date(ges_cli_fec)
            ";

            $datos2=DB::connection('mysql2')->select(DB::raw($sql2));

            $tsql3="
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
            ";
            $tsql3 = $tsql3." ". $filtro." 
                        left JOIN empleado as e on c.emp_tel_id_FK=e.emp_id
                        left JOIN call_telefonica as cal on e.cal_id_FK=cal.cal_id
                        WHERE
                        ic.car_id_FK=$car
                        AND c.car_id_fk=$car
                        and year(ges_cli_fec) = year(now())
                        and month(ges_cli_fec) = month(now())
                        and MONTH(fecha) = month(now())
                        and res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7)
            ";
            $tsql3= $tsql3." ".$sqll." 
                        GROUP BY cuenta,date(ges_cli_fec)
                    ) t
                    INNER JOIN gestion_cliente ult_ges ON t.MAXGES=ult_ges.ges_cli_id
                    WHERE res_id_FK not in (19,27,12,13,26,34,17,21)
                )tt
                GROUP BY fec
            ";

            $total3=DB::connection('mysql2')->select(DB::raw($tsql3));

            $sql3="
                SELECT
                date(ges_cli_fec) AS fec,
                count( ic.car_id_fk) AS can_promesas
                FROM
                    gestion_cliente g
                INNER JOIN cliente c ON c.cli_id = g.cli_id_FK
                INNER JOIN indicadores.cartera_detalle ic ON c.cli_cod = ic.cuenta
            ";
            $sql3 = $sql3." ". $filtro." 
                left JOIN empleado as e on c.emp_tel_id_FK=e.emp_id
                left JOIN call_telefonica as cal on e.cal_id_FK=cal.cal_id
                WHERE
                ic.car_id_FK=$car
                and year(ges_cli_fec) = year(now())
                and month(ges_cli_fec) = month(now())
                and MONTH(fecha) = month(now())
                and res_id_FK in (1)

            ";
            $sql3 = $sql3." ". $sqll." 
                GROUP BY
                    date(ges_cli_fec)
            ";

            $datos3=DB::connection('mysql2')->select(DB::raw($sql3));
            return response()->json(['total1' => $total1, 'datos1' => $datos1,'total2' => $total2, 'datos2' => $datos2 ,'total3' => $total3, 'datos3' => $datos3]);
        

    }

    public static function buscarEfectividad($car,$asig,$estr,$valor){
        $sql=" ";
        $sqll=" ";
        $filtro=" ";
        if($asig=="0" && $asig!="nuevos"){
            $sql = $sql . " and 1=1 " ;
            $sqll=$sqll." and 1=1 ";
        }else if($asig!="0" && $asig!="nuevos"){
            $sql = $sql . " and call_pertenece like '%$asig%' " ;
            $sqll= $sqll . " and cal_nom like '%$asig%' ";
        }else if($asig="nuevos"){
            $filtro = $filtro . " INNER JOIN nuevo_cliente as n on n.cli_nuev_cod=c.cli_cod " ;
            $sql= $sql . " and nuevo like '%NUEVO%' ";
            $sqll=$sqll." and 1=1 ";
        }
        //dd($valor);
        if($estr!='null' && $valor!='null'){
            if($estr=="saldo_deuda" || $estr=="capital" || $estr=="monto_camp"){
                if($valor=='1'){
                    $sql = $sql . " and $estr <500 " ;
                    $sqll= $sqll . " and $estr <500 ";
                }else if($valor=='2'){
                    $sql = $sql . " and $estr >= 500 and $estr < 1000 " ;
                    $sqll= $sqll . " and $estr >= 500 and $estr < 1000 ";
                }else if($valor=='3'){
                    $sql = $sql . " and $estr >= 1000 and $estr < 3000 " ;
                    $sqll= $sqll . " and $estr >= 1000 and $estr < 3000 ";
                }else{
                    $sql = $sql . " and $estr >= 3000 " ;
                    $sqll= $sqll . " and $estr >= 3000 ";
                }
            }else{
                $sql = $sql . " and $estr = '$valor' " ;
                $sqll= $sqll . " and $estr = '$valor' ";
            }
        }else{
            $sql = $sql . " and 1=1 " ;
            $sqll=$sqll." and 1=1 ";
        }

    
            $tsql1="
                select date(pag_cli_fec) as fec,sum(pag_cli_mon) as can_clientes
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
            ";
            $tsql1= $tsql1." ".$sql." 
                GROUP BY cli_cod, date(ges_cli_com_fec)
                ) a
                group by fec
            ";
            $total1=DB::connection('mysql2')->select(DB::raw($tsql1));

            $sql1="
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
                            and res_id_fk in (1,43)
            ";
            $sql1 = $sql1." ". $sql." 
                            ) a
                            group by fec
            ";
            $datos1=DB::connection('mysql2')->select(DB::raw($sql1));

            $tsql2="
                select date(pag_cli_fec) as fec,sum(pag_cli_mon) as can_clientes
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
            ";
            $tsql2= $tsql2." ".$sql." 
                GROUP BY cli_cod, date(ges_cli_com_fec)
                ) a
                group by fec
            ";
            $total2=DB::connection('mysql2')->select(DB::raw($tsql2));

            $sql2="
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
                            and res_id_fk in (1,43)
            ";
            $sql2 = $sql2." ". $sql." 
                            ) a
                            group by fec
            ";
            $datos2=DB::connection('mysql2')->select(DB::raw($sql2));

            $tsql3="
                select date(pag_cli_fec) as fec,sum(pag_cli_mon) as can_clientes
                from
                (select cli_cod,ges_cli_fec,ges_cli_com_can,pag_cli_mon, pag_cli_fec,ges_cli_com_fec
                FROM pago_cliente_2 as p
                RIGHT JOIN cliente AS c ON c.CLI_COD = p.pag_cli_cod
                INNER JOIN gestion_cliente AS ge ON ge.cli_id_FK = c.cli_id
                INNER JOIN indicadores.cartera_detalle AS CDET ON p.pag_cli_cod = CDET.CUENTA
            ";
            $tsql3 = $tsql3." ". $filtro."
                left JOIN empleado as e on c.emp_tel_id_FK=e.emp_id
                left JOIN call_telefonica as cal on e.cal_id_FK=cal.cal_id
                WHERE CDET.car_id_fk = $car
                and year(pag_cli_fec) = year(now())
                and month(pag_cli_fec) = month(now())
                and year(ges_cli_com_fec) = year(now())
                and month(ges_cli_com_fec) = month(now())
                and year(fecha) = year(now())
                and MONTH(fecha) = month(now())
                and res_id_fk in (1,43)
            ";
            $tsql3= $tsql3." ".$sqll." 
                GROUP BY cli_cod, date(ges_cli_com_fec)
                ) a
                group by fec
            ";
            $total3=DB::connection('mysql2')->select(DB::raw($tsql3));

            $sql3="
            select date(ges_cli_com_fec) as fec,sum(ges_cli_com_can) can_gestiones
                            from
                            (select  cli_cod,ges_cli_fec,ges_cli_com_can, ges_cli_com_fec
                            from cliente AS c
                            INNER JOIN gestion_cliente AS ge ON ge.cli_id_FK = c.cli_id
                            INNER JOIN indicadores.cartera_detalle AS CDET ON c.cli_cod= CDET.CUENTA
            ";
            $sql3 = $sql3." ". $filtro."
                            left JOIN empleado as e on c.emp_tel_id_FK=e.emp_id
                            left JOIN call_telefonica as cal on e.cal_id_FK=cal.cal_id
                            WHERE CDET.car_id_fk = $car
                            and year(ges_cli_fec) = year(now())
                            and month(ges_cli_fec) = month(now())
                            and year(ges_cli_com_fec) = year(now())
                            and month(ges_cli_com_fec) = month(now())
                            and year(fecha) = year(now())
                            and MONTH(fecha) = month(now())
                            and res_id_fk in (1,43)
            ";
            $sql3 = $sql3." ". $sqll." 
                            ) a
                            group by fec
            ";
            $datos3=DB::connection('mysql2')->select(DB::raw($sql3));
            

            return response()->json(['total1' => $total1, 'datos1' => $datos1,'total2' => $total2, 'datos2' => $datos2 ,'total3' => $total3, 'datos3' => $datos3]);
        
    }
}
