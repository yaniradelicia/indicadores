<?php

namespace App\Http\Controllers;
use DB;

use Illuminate\Http\Request;

class RegistroFirmasController extends Controller
{
    public function index()
    {
        /*$sql="
            SELECT car_id_FK, car_nom
            FROM pago_cliente_2 as p
            INNER JOIN cartera as c on p.car_id_FK=c.car_id
            WHERE DATE_FORMAT(pag_cli_fec,'%Y-%m')=DATE_FORMAT('$mes','%Y-%m')
            GROUP BY car_id_FK
            order by car_nom asc
        ";
        $cartera=DB::select(DB::raw($sql));*/
        //return view('vista_pagos.registro_firmas',compact('cartera'));
        return view('vista_pagos.registro_firmas');
    }
    
    public function cargarCarteras($mes)
    {
        $sql="
            SELECT car_id_FK, car_nom
            FROM pago_cliente_2 as p
            INNER JOIN cartera as c on p.car_id_FK=c.car_id
            WHERE DATE_FORMAT(pag_cli_fec,'%Y-%m')='$mes'
            GROUP BY car_id_FK
            order by car_nom asc
        ";
        $cartera=DB::select(DB::raw($sql));
        return $cartera;
    }

    public function guardarAutomatico($car,$mes)
    {
        $sqlCli="
            SELECT 
            pag_cli_id, 
            car_id_FK as car, 
            pag_cli_cod as cli_cod,
            '' as tarjeta,
            pag_cli_mon,
            pag_cli_fec,
            '' as estado,
            '' as agregado,
            '' as producto,
            pago_gestor as encargado,
            '1' as cantidad 
            FROM indicadores.pago_cliente as p1
            INNER JOIN pago_cliente_2 as p2 
            on (p1.pago_cli_cod=p2.pag_cli_cod and p1.pago_cli_fec=p2.pag_cli_fec and p1.pago_cli_mon=p2.pag_cli_mon)
            WHERE car_id=$car and car_id_FK=$car
            AND DATE_FORMAT(pago_cli_fec,'%Y-%m')='$mes'
            AND DATE_FORMAT(pag_cli_fec,'%Y-%m')='$mes'
        ";
        $pagosClientes=DB::select(DB::raw($sqlCli));
        $array = array();
        foreach($pagosClientes as $p){
            //$array[] = $p->pago_cli_cod;
            $array[] = $p->pag_cli_id;
        }
        $cantidad_cli=count($array);
        $cadena=implode(',',$array);

        if($cantidad_cli>0){
            $cadena=$cadena;
        }else{
            $cadena=0;
        }

        $sqlInsertar="
            insert into indicadores.pago_cliente (
            car_id,
            pago_cli_cod,
            pago_cli_pro,
            pago_cli_mon,
            pago_cli_fec,
            pago_cli_est,
            pago_add,
            pago_grup_pro,
            pago_gestor
            )
            
            SELECT car, cli_cod,'' as tarjeta,pag_cli_mon,pag_cli_fec,'' as estado,'' as agregado,'' as producto,encargado
            FROM
            (
            Select pag_cli_id,t.car_id_FK as car,cli_cod,pag_cli_mon,count(DISTINCT emp_firma) as cantidad,pag_cli_fec,emp_firma,emp_nom,encargado
            from 
            (	
                    Select pag_cli_id,p.car_id_FK,cli_cod,pag_cli_mon,ges_cli_det,ges_cli_fec,pag_cli_fec,
                    right(ges_cli_det,3) as firma
                    from cliente c
                    Inner join gestion_cliente g on c.cli_id=g.cli_id_FK
                    INNER JOIN pago_cliente_2 as p on c.cli_cod=p.pag_cli_cod
                    WHERE DATE_FORMAT(ges_cli_fec,'%Y-%m')='$mes'
                    and DATE_FORMAT(pag_cli_fec,'%Y-%m')='$mes'
                    and DATE(ges_cli_fec)<=pag_cli_fec
                    and pag_cli_id not in ($cadena)
                    AND c.car_id_FK=$car and p.car_id_FK=$car
                    and cli_est=0 and cli_pas=0
                    and res_id_FK not in (2,38,41,45,44, 25,19,27,12,26,13,4,11,12,20,14,15,16,23,24,29,31,32)
                    Group by pag_cli_id,firma
            ) t
            left join sub_empleado s on 
            t.firma like CONCAT('%', s.emp_firma)
            WHERE s.emp_firma not in ('') and emp_est=0
            Group by pag_cli_id,pag_cli_fec,pag_cli_mon,firma
            ) w
            Group by pag_cli_id,pag_cli_fec,pag_cli_mon
            HAVING COUNT(cantidad)=1
        ";
        $insertar=DB::insert(DB::raw($sqlInsertar));
        return $insertar;

    }

    public function cargaClientes($car,$mes)
    {
        $sqlCli="
            SELECT 
            pag_cli_id, 
            car_id_FK as car, 
            pag_cli_cod as cli_cod,
            '' as tarjeta,
            pag_cli_mon,
            pag_cli_fec,
            '' as estado,
            '' as agregado,
            '' as producto,
            pago_gestor as encargado,
            '1' as cantidad,
            DATE_FORMAT(pag_cli_fec,'%Y%m') as fec
            FROM indicadores.pago_cliente as p1
            INNER JOIN pago_cliente_2 as p2 
            on (p1.pago_cli_cod=p2.pag_cli_cod and p1.pago_cli_fec=p2.pag_cli_fec and p1.pago_cli_mon=p2.pag_cli_mon)
            WHERE car_id=$car and car_id_FK=$car
            AND DATE_FORMAT(pago_cli_fec,'%Y-%m')='$mes'
            AND DATE_FORMAT(pag_cli_fec,'%Y-%m')='$mes'
            Group by pag_cli_id
        ";
        $pagosClientes=DB::select(DB::raw($sqlCli));
        $array = array();
        foreach($pagosClientes as $p){
            //$array[] = $p->pago_cli_cod;
            $array[] = $p->pag_cli_id;
        }
        $cantidad_cli=count($array);
        $cadena=implode(',',$array);

        if($cantidad_cli>0){
            $cadena=$cadena;
        }else{
            $cadena=0;
        }

        /*$sqlInsertar="
            insert into indicadores.pago_cliente (
            car_id,
            pago_cli_cod,
            pago_cli_pro,
            pago_cli_mon,
            pago_cli_fec,
            pago_cli_est,
            pago_add,
            pago_grup_pro,
            pago_gestor
            )
            
            SELECT car, cli_cod,'' as tarjeta,pag_cli_mon,pag_cli_fec,'' as estado,'' as agregado,'' as producto,encargado
            FROM
            (
            Select pag_cli_id,t.car_id_FK as car,cli_cod,pag_cli_mon,count(emp_firma) as cantidad,pag_cli_fec,emp_firma,emp_nom,encargado
            from 
            (	
                    Select pag_cli_id,p.car_id_FK,cli_cod,pag_cli_mon,ges_cli_det,ges_cli_fec,pag_cli_fec,
                    right(ges_cli_det,3) as firma
                    from cliente c
                    Inner join gestion_cliente g on c.cli_id=g.cli_id_FK
                    INNER JOIN pago_cliente_2 as p on c.cli_cod=p.pag_cli_cod
                    WHERE DATE_FORMAT(ges_cli_fec,'%Y-%m')=DATE_FORMAT(now(),'%Y-%m')
                    and DATE_FORMAT(pag_cli_fec,'%Y-%m')=DATE_FORMAT(now(),'%Y-%m')
                    and DATE(ges_cli_fec)<=pag_cli_fec
                    and pag_cli_id not in ($cadena)
                    AND c.car_id_FK=$car and p.car_id_FK=$car
                    and cli_est=0 and cli_pas=0
                    and res_id_FK not in (2,38,41)
                    Group by pag_cli_id,firma
            ) t
            left join sub_empleado s on 
            t.firma like CONCAT('%', s.emp_firma)
            WHERE s.emp_firma not in ('')
            Group by pag_cli_id,pag_cli_fec,pag_cli_mon
            ) w
            where cantidad=1
        ";
        $insertar=DB::insert(DB::raw($sqlInsertar));*/
    
        $sql1="
            SELECT 
                pag_cli_id,
                car, 
                cli_cod,
                '' as tarjeta,
                pag_cli_mon,
                pag_cli_fec,
                '' as estado,
                '' as agregado,
                '' as producto,
                encargado,
                count(cantidad) as cantidad,
                DATE_FORMAT(pag_cli_fec,'%Y%m') as fec
            FROM
            (
            Select pag_cli_id,t.car_id_FK as car,cli_cod,pag_cli_mon,count(DISTINCT emp_firma) as cantidad,pag_cli_fec,emp_firma,emp_nom,encargado
            from 
            (	
                    Select pag_cli_id,p.car_id_FK,cli_cod,pag_cli_mon,ges_cli_det,ges_cli_fec,pag_cli_fec,
                    right(ges_cli_det,3) as firma
                    from cliente c
                    Inner join gestion_cliente g on c.cli_id=g.cli_id_FK
                    INNER JOIN pago_cliente_2 as p on c.cli_cod=p.pag_cli_cod
                    WHERE DATE_FORMAT(ges_cli_fec,'%Y-%m')='$mes'
                    and DATE_FORMAT(pag_cli_fec,'%Y-%m')='$mes'
                    and DATE(ges_cli_fec)<=pag_cli_fec
                    and pag_cli_id not in ($cadena)
                    AND c.car_id_FK=$car and p.car_id_FK=$car
                    and cli_est=0 and cli_pas=0
                    and res_id_FK not in (2,38,41,45,44, 25,19,27,12,26,13,4,11,12,20,14,15,16,23,24,29,31,32)
                    Group by pag_cli_id,firma
            ) t
            left join sub_empleado s on 
            t.firma like CONCAT('%', s.emp_firma)
            WHERE s.emp_firma not in ('') and emp_est=0
            Group by pag_cli_id,pag_cli_fec,pag_cli_mon,firma
            ) w
            Group by pag_cli_id,pag_cli_fec,pag_cli_mon
            HAVING COUNT(cantidad)>=1
            order by pag_cli_fec desc
        ";

        $query1=DB::select(DB::raw($sql1));
        $array2 = array();
        foreach($query1 as $q1){
            $array2[] = $q1->pag_cli_id;
        }
        $cantidad_cli2=count($array2);
        $cadena2=implode(',',$array2);

        if($cantidad_cli2>0){
            $cadena2=$cadena2;
        }else{
            $cadena2=0;
        }


        $sql2="
            SELECT 
                pag_cli_id, 
                car_id_FK as car, 
                pag_cli_cod as cli_cod,
                '' as tarjeta,
                pag_cli_mon,
                pag_cli_fec,
                '' as estado,
                '' as agregado,
                '' as producto,
                '' as encargado,
                '0' as cantidad,
                DATE_FORMAT(pag_cli_fec,'%Y%m') as fec
            FROM pago_cliente_2 
            WHERE car_id_FK=$car
            AND DATE_FORMAT(pag_cli_fec,'%Y-%m')='$mes'
            and pag_cli_id not in ($cadena,$cadena2)
            order by pag_cli_fec desc
        ";
        $query2=DB::select(DB::raw($sql2));

        $unido=array_merge($query2,$query1,$pagosClientes);
        return $unido;
    }

    public function cargaClienteCod($codigo,$fecha)
    {

        /*$sqlCliente="
            SELECT *
            FROM pago_cliente_2 
            WHERE car_id_FK=$cartera AND DATE_FORMAT(pag_cli_fec,'%Y-%m')=DATE_FORMAT(now(),'%Y-%m')
            AND pag_cli_cod=$codigo
        ";*/
        $sqlCliente="
            SELECT *
            FROM pago_cliente_2 
            WHERE DATE_FORMAT(pag_cli_fec,'%Y%m')='$fecha'
            AND pag_cli_id=$codigo
        ";
        $cliente=DB::select(DB::raw($sqlCliente));
        foreach($cliente as $c){
            $fec = $c->pag_cli_fec;
            $cartera=$c->car_id_FK;
            $monto=$c->pag_cli_mon;
            $cli_cod=$c->pag_cli_cod;
        }

        $sqlUsuario="
            Select t.car_id_FK,cli_cod,pag_cli_mon,pag_cli_fec,emp_firma,emp_nom,encargado
            from (
            Select p.car_id_FK,cli_cod,pag_cli_mon,ges_cli_det,ges_cli_fec,pag_cli_fec,
            right(ges_cli_det,3) as firma
            from cliente c
            Inner join gestion_cliente g on c.cli_id=g.cli_id_FK
            INNER JOIN pago_cliente_2 as p on c.cli_cod=p.pag_cli_cod
            WHERE DATE_FORMAT(ges_cli_fec,'%Y%m')='$fecha'
            and DATE_FORMAT(pag_cli_fec,'%Y%m')='$fecha'
            and DATE(ges_cli_fec)<='$fec'
            and pag_cli_id in ($codigo)
            AND c.car_id_FK=$cartera and p.car_id_FK=$cartera
            and cli_est=0 and cli_pas=0
            and res_id_FK not in (2,38,41,45,44, 25,19,27,12,26,13,4,11,12,20,14,15,16,23,24,29,31,32)
            Group by cli_cod,firma
            ) t
            left join sub_empleado s on 
            t.firma like CONCAT('%', s.emp_firma)
            WHERE s.emp_firma not in ('') and emp_est=0
            Group by cli_cod,firma
        ";

        $usuario=DB::select(DB::raw($sqlUsuario));

        $array = array();
        foreach($usuario as $u){
            $array[] = $u->emp_firma;
        }
        $cantidad_cli=count($array);
        //dd($cantidad_cli);

        if($cantidad_cli>0){
            //return $usuario;
            return response()->json(['usuario' => $usuario,'usuario2' => $usuario,'usuario3' => $usuario]);
        }else{
            $sqlCliUno="
                    SELECT pag_cli_id,car_id_FK, pag_cli_cod as cli_cod,pag_cli_mon,pag_cli_fec from pago_cliente_2
                    WHERE car_id_FK = $cartera
                    and pag_cli_id=$codigo
                ";
            if($cartera==2 || $cartera==88 || $cartera==89){
                $sqlCarUsu="
                    SELECT * from sub_empleado
                    WHERE car_id_FK in (2,88,89) and emp_est=0
                    GROUP BY encargado
                ";
            }else if($cartera==20 || $cartera==70 || $cartera==72){
                $sqlCarUsu="
                    SELECT * from sub_empleado
                    WHERE car_id_FK in (20,70,72) and emp_est=0
                    GROUP BY encargado
                ";
            }else{
                $sqlCarUsu="
                    SELECT * from sub_empleado
                    WHERE car_id_FK=$cartera and emp_est=0
                    GROUP BY encargado
                ";
            }
            $encargados=DB::select(DB::raw($sqlCarUsu));
            $clientes=DB::select(DB::raw($sqlCliUno));
            return response()->json(['clientes' => $clientes, 'encargados' => $encargados]);
            //return $encargados;
        }
        //return $usuario;
    }

    public function GuardarCliente($car,$cod,$pago,$fec,$usuario)
    {
        $insertado = DB::insert("
        insert into indicadores.pago_cliente (car_id,pago_cli_cod,pago_cli_pro,pago_cli_mon,pago_cli_fec,pago_cli_est,pago_add,pago_grup_pro,pago_gestor)
        values ($car,'$cod','',$pago,'$fec','','','',$usuario)
        ");

        return $insertado;
    }
}
