<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cartera;
use Carbon\Carbon;
use DB;

class CampanaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cartera=Cartera::carteraDetalle();
        return view('campanas.crear_campana',compact('cartera'));
    }

    public function cargar(Request $request)
    {
        $car = $request->query('car');
        $id = $request->query('id');
        $sql = "
            select 
            nombre_camp,id_campana,id_cartera,clientes,cant_clientes,fecha_i,fecha_f
            FROM indicadores.campana
            where id_campana=$id
            ";
        $query=DB::select(DB::raw($sql));
        return $query;
    }

    public function actualizar(Request $request)
    {
        $valor_id = $request->query('valor_id');
        $valor_fi = $request->query('valor_fi');
        $valor_ff = $request->query('valor_ff');
        $sql = DB::update("
            update indicadores.campana set fecha_i='$valor_fi' , fecha_f='$valor_ff' where id_campana=$valor_id
            ");
        //$query=DB::select(DB::raw($sql));
        //return $query;
        
       
        return response()->json ($sql); 
    }

    public function buscar()
    {
        $cartera=Cartera::carteraDetalle();
        return view('campanas.buscar_campana',compact('cartera'));
    }

    public function filtro(Request $request)
    {
        $car = $request->query('car');
        $fec_i = $request->query('fec_i');
        $fec_f = $request->query('fec_f');
        $sql = "
            select 
                nombre_camp,id_campana,id_cartera,clientes,cant_clientes
            FROM indicadores.campana
            where id_cartera=$car and date_format(fecha_i,'%Y-%m-%d') between '$fec_i' and '$fec_f'
            ";
        $query=DB::select(DB::raw($sql));
        return $query;
    }

    public function mostrar(Request $request)
    {
        $id = $request->query('id');
        $car = $request->query('car');
        $sql = "
            select 
                clientes,cant_clientes,fecha_i,fecha_f
            FROM indicadores.campana
            where id_cartera=$car and id_campana=$id
            ";
        $query=DB::select(DB::raw($sql));
        foreach($query as $q){
            $cadena=$q->clientes;
            $cantidad=$q->cant_clientes;
            $fecha_i=$q->fecha_i;
            $fecha_f=$q->fecha_f;
        }

        /*$sqlCobertura="
        SELECT
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
								AND cuenta in ($cadena)
                                GROUP BY
                                    cuenta
                            ) t
                        )tt
        ";*/
        
        $sqlCobertura="
            SELECT 
                if(cant_gestiones is null,0,cant_gestiones) as cant_gestiones,
                if(can_clientes is null,0,can_clientes) as can_clientes
            FROM
            (SELECT
                SUM(gestiones) as cant_gestiones,
                SUM(cliente) as can_clientes
                FROM
                    (
                        SELECT
                            cli_id,
                            date(ges_cli_fec) AS fec,
                            count( cuenta) AS gestiones,
                            1 as cliente
                        FROM
                            gestion_cliente g
                        INNER JOIN cliente c ON c.cli_id = g.cli_id_FK
                        INNER JOIN indicadores.cartera_detalle ic ON c.cli_cod = ic.cuenta
                        WHERE
                        ic.car_id_FK=$car
                        AND c.car_id_fk=$car
                        and ges_cli_acc in (1,2)
                        and date_format(fecha,'%Y%m') = date_format(now(),'%Y%m')
                        AND (ges_cli_fec between '$fecha_i' and '$fecha_f')
                        AND cuenta in ($cadena)
                        GROUP BY cuenta
                    ) t
            )tt
        ";

        $cobertura=DB::select(DB::raw($sqlCobertura));
        //return $query2;

        $sqlPDP="
            select if(cli_cod,count(cli_cod),0)  as can_clientes, if(monto,sum(monto),0) as monto_pdp
            from
            (
                select cli_cod, sum(ges_cli_com_can) as monto
                from gestion_cliente as g
                right join cliente as c on g.cli_id_FK = c.cli_id
                inner join indicadores.cartera_detalle as cd on c.cli_cod =cd.cuenta
                where cd.car_id_fk=$car
                and res_id_fk in ('1','43')
                and date_format(fecha,'%Y-%m') = date_format(now(),'%Y-%m')
                AND (ges_cli_fec between '$fecha_i' and '$fecha_f')
                and cuenta in ($cadena)
                group By cli_cod
            ) t
        ";
        $pdp=DB::select(DB::raw($sqlPDP));

        $sqlGestiones="
            select count(cuenta) as can_clientes, gestiones as llamadas
            from
                (
                SELECT
                cuenta,
                date(ges_cli_fec) AS fec,
                (case
                    when count(cuenta) >=3 then '3'
                    else count(cuenta)
                end) AS gestiones
                FROM
                gestion_cliente g
                INNER JOIN cliente c ON c.cli_id = g.cli_id_FK
                INNER JOIN indicadores.cartera_detalle ic ON c.cli_cod = ic.cuenta
                WHERE
                ic.car_id_FK=$car
                AND c.car_id_fk=$car
                and ges_cli_acc in (1,2)
                and date_format(fecha,'%Y-%m') = date_format(now(),'%Y-%m')
                AND (ges_cli_fec between '$fecha_i' and '$fecha_f')
                AND cuenta in ($cadena)
                GROUP BY cuenta
                ) t
            group by gestiones
            order by llamadas asc
        ";
        $gestiones=DB::select(DB::raw($sqlGestiones));

        $sqlPagos = "
            select if(cuenta,count(cuenta),0)  as can_clientes, if(monto,sum(monto),0) as monto_pago
            from
            (
            select cuenta,sum(pag_cli_mon) as monto
            from pago_cliente_2 as p inner join indicadores.cartera_detalle as cd
            on p.pag_cli_cod=cd.cuenta
            WHERE cd.car_id_fk=$car
            and date_format(fecha,'%Y-%m') =date_format(now(),'%Y-%m')
            and date_format(pag_cli_fec,'%Y-%m')=date_format(now(),'%Y-%m')
            and cuenta in ($cadena)
            group By cuenta
            ) t
        ";
        $pagos=DB::select(DB::raw($sqlPagos));

        $sqlUbic="
            SELECT res_id_FK as ubic,count(cuenta) as cant_clientes
            from	
                                    (
                                    SELECT
                        cuenta,
                        (CASE 
                            WHEN res_id_FK IN ( 38, 6, 22, 41 ) THEN 'C-F-R-N'
                            WHEN res_id_FK IN ( 2, 37, 18, 10, 8, 39, 7 ) THEN 'Contacto Otros'
                            WHEN res_id_FK IN (  1, 43 ) THEN 'Contacto PDP'
                            WHEN res_id_FK IN (  33 ) THEN 'Contacto Positivo'
                            WHEN res_id_FK IN ( 34, 17, 21 ) THEN 'No Disponible'
                            WHEN res_id_FK IN ( 19, 27, 12, 26, 13 ) THEN 'Ilocalizado'
                            WHEN res_id_FK IN ( 4,45,44, 25,32 ) THEN 'No Contacto'
                            ELSE 'NO ENCONTRADO'
                        END) AS res_id_FK
                        FROM
                        creditoy_cobranzas.cliente AS c
                        INNER JOIN indicadores.cartera_detalle AS cd ON c.cli_cod = cd.cuenta 
                        INNER JOIN(select max(ges_cli_id) as maxid,cli_id_FK from creditoy_cobranzas.gestion_cliente where (ges_cli_fec between '$fecha_i' and '$fecha_f') GROUP BY cli_id_FK) as g
                        on g.cli_id_FK=c.cli_id
                        inner join creditoy_cobranzas.gestion_cliente as gg on gg.ges_cli_id=g.maxid
                        left JOIN creditoy_cobranzas.empleado as e on c.emp_tel_id_FK=e.emp_id
                        left JOIN creditoy_cobranzas.call_telefonica as cal on e.cal_id_FK=cal.cal_id
                        WHERE c.car_id_FK =$car
                        AND cli_est =0 and cli_pas=0
                        and date_format(fecha,'%Y-%m') = date_format(now(),'%Y-%m')
                        AND (ges_cli_fec between '$fecha_i' and '$fecha_f')
                        AND cuenta in ($cadena)
                        GROUP BY cuenta
                            ) t
            group by ubic
        ";
        $ubicabilidad=DB::select(DB::raw($sqlUbic));
        return response()->json(['cobertura' => $cobertura,'pdp' => $pdp, 'gestiones' => $gestiones, 'pagos' => $pagos, 'ubicabilidad' => $ubicabilidad]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ver(Request $request)
    {
        $car = $request->query('car');
        $tramos = explode(',',$request->query('tramos'));
        $deps = explode(',',$request->query('deps'));
        $prioridades = explode(',',$request->query('prioridades'));
        $situaciones = explode(',',$request->query('situaciones'));
        $calls = explode(',',$request->query('calls'));
        $sueldos = explode(',',$request->query('sueldos'));
        $capitales = explode(',',$request->query('capitales'));
        $deudas = explode(',',$request->query('deudas'));
        $importes = explode(',',$request->query('importes'));
        $ubics = explode(',',$request->query('ubics'));
        $usuarios = explode(',',$request->query('usuarios'));
        //return $tramos;
        // $lengthTramos = sizeof($tramos); 
        $lengthTramos = count($tramos); 
        $lengthDeps = count($deps);
        $lengthPrio = count($prioridades);
        $lengthSitu = count($situaciones);
        $lengthCalls = count($calls);
        $lengthSueldos = count($sueldos);
        $lengthCapitales = count($capitales);
        $lengthDeudas = count($deudas);
        $lengthImportes = count($importes);
        $lengthUbics = count($ubics);
        $lengthUsuarios = count($usuarios);
        //dd($usuarios);
        //dd($lengthTramos);
        //cuenta,rango_sueldo,dep_ind,prioridad,cal_nom,emp_cod,dep,tramo,capital,saldo_deuda,monto_camp,res_id_FK
        //SELECT 	count(cuenta) as cantidad,emp_cod as usuario
        $sql = "
        SELECT 	count(cuenta) as cantidad,emp_cod as usuario, emp_nom as usuario_nom
                FROM
                (
                SELECT 	cuenta,cli_nom,cli_tel_tel,rango_sueldo,dep_ind,prioridad,cal_nom,emp_cod,emp_nom,cartera,car,dep,tramo,capital,cap,saldo_deuda,sal,monto_camp,importe,res_id_FK
                from
                (SELECT
                    cuenta,cli_nom,if(cli_tel_tel is null,'0',cli_tel_tel) as cli_tel_tel,
                                    capital as cap,saldo_deuda as sal,monto_camp as importe,rango_sueldo,dep_ind,prioridad,
                                    if(cal_nom is null,'SIN CALL',cal_nom) as cal_nom,
                                    if(emp_cod is null,'NO ASIGNADO',emp_cod) as emp_cod,if(emp_nom is null,'NO ASIGNADO',emp_nom) as emp_nom,
                                    cartera,cd.car_id_fk as car,
                            (CASE 
                                WHEN dep LIKE '%TACNA%' or dep LIKE '%JUNIN%' or dep LIKE '%HUANUCO%' or dep LIKE '%UCAYALI%' or
                                            dep LIKE '%LORETO%' or dep LIKE '%CUSCO%' or dep LIKE '%OTROS%' or dep LIKE '%MOQUEGUA%' OR
                                            dep LIKE '%HUANCAVELICA%' or dep LIKE '%SAN MARTIN%' or dep LIKE '%PUNO%' or dep LIKE '%TUMBES%' or 
                                            dep LIKE '%AYACUCHO%' or dep LIKE '%PASCO%' THEN 'OTROS'
                                ELSE dep
                            END) AS dep,
                        if(tramo <=2016,2016,tramo) AS tramo,
                        (CASE 
                            WHEN capital <500 THEN 'A'
                            WHEN capital >= 500 and capital < 1000 THEN 'B'
                            WHEN capital >= 1000 and capital < 3000 THEN 'C'
                            WHEN capital >= 3000 THEN 'D'
                        END) AS capital,
                        (CASE 
                            WHEN saldo_deuda <500 THEN 'A'
                            WHEN saldo_deuda >= 500 and saldo_deuda < 1000 THEN 'B'
                            WHEN saldo_deuda >= 1000 and saldo_deuda < 3000 THEN 'C'
                            WHEN saldo_deuda >= 3000 THEN 'D'
                        END) AS saldo_deuda,
                        (CASE 
                            WHEN monto_camp <500 THEN 'A'
                            WHEN monto_camp >= 500 and monto_camp < 1000 THEN 'B'
                            WHEN monto_camp >= 1000 and monto_camp < 3000 THEN 'C'
                            WHEN monto_camp >= 3000 THEN 'D'
                        END) AS monto_camp,
                        (CASE 
                        WHEN res_id_FK IN ( 38, 6, 22, 41 ) THEN 'cfrn'
                        WHEN res_id_FK IN ( 2, 37, 33, 18, 10, 1, 8, 43, 39, 7 ) THEN 'contacto'
                        WHEN res_id_FK IN ( 34, 17, 21 ) THEN 'nodisponible'
                        WHEN res_id_FK IN ( 19, 27, 12, 26, 13 ) THEN 'inubicable'
                        WHEN res_id_FK IN ( 4,45,44, 25,32 ) THEN 'nocontacto'
                        ELSE 'NO ENCONTRADO'
                    END) AS res_id_FK
                    FROM
                    creditoy_cobranzas.cliente AS c
                                    INNER JOIN creditoy_cobranzas.cliente_telefono as tele
                                    on c.cli_id=tele.cli_id_FK
                    INNER JOIN indicadores.cartera_detalle AS cd ON c.cli_cod = cd.cuenta 
                    INNER JOIN(select max(ges_cli_id) as maxid,cli_id_FK from creditoy_cobranzas.gestion_cliente where date_format( ges_cli_fec, '%Y-%m' ) = date_format( now(), '%Y-%m' ) GROUP BY cli_id_FK) as g
                    on g.cli_id_FK=c.cli_id
                    inner join creditoy_cobranzas.gestion_cliente as gg on gg.ges_cli_id=g.maxid
                            left JOIN creditoy_cobranzas.empleado as e on c.emp_tel_id_FK=e.emp_id
                            left JOIN creditoy_cobranzas.call_telefonica as cal on e.cal_id_FK=cal.cal_id
                    WHERE c.car_id_FK =$car
                                    and cli_tel_est=0 and cli_tel_pas=0
                    AND cli_est =0 and cli_pas=0
                    AND date_format( ges_cli_fec, '%Y-%m' ) = date_format( now(), '%Y-%m' )
                    AND date_format( fecha, '%Y-%m' ) = date_format( now(), '%Y-%m' )
                            GROUP BY	
                    cli_cod
                
                UNION ALL 
                
                SELECT cuenta,cli_nom,if(cli_tel_tel is null,'0',cli_tel_tel) as cli_tel_tel,
                                    capital as cap,saldo_deuda as sal,monto_camp as importe,rango_sueldo,dep_ind,prioridad,
                                    if(cal_nom is null,'SIN CALL',cal_nom) as cal_nom,
                                    if(emp_cod is null,'NO ASIGNADO',emp_cod) as emp_cod,if(emp_nom is null,'NO ASIGNADO',emp_nom) as emp_nom,
                                    cartera,cd.car_id_fk as car,
                            (CASE 
                        WHEN dep LIKE '%TACNA%' or dep LIKE '%JUNIN%' or dep LIKE '%HUANUCO%' or dep LIKE '%UCAYALI%' or
                                    dep LIKE '%LORETO%' or dep LIKE '%CUSCO%' or dep LIKE '%OTROS%' or dep LIKE '%MOQUEGUA%' OR
                                    dep LIKE '%HUANCAVELICA%' or dep LIKE '%SAN MARTIN%' or dep LIKE '%PUNO%' or dep LIKE '%TUMBES%' or 
                                    dep LIKE '%AYACUCHO%' or dep LIKE '%PASCO%' THEN 'OTROS'
                        ELSE dep
                    END) AS dep,
                    if(tramo <=2016,2016,tramo) AS tramo,
                    (CASE 
                        WHEN capital <500 THEN 'A'
                        WHEN capital >= 500 and capital < 1000 THEN 'B'
                        WHEN capital >= 1000 and capital < 3000 THEN 'C'
                        WHEN capital >= 3000 THEN 'D'
                    END) AS capital,
                    (CASE 
                        WHEN saldo_deuda <500 THEN 'A'
                        WHEN saldo_deuda >= 500 and saldo_deuda < 1000 THEN 'B'
                        WHEN saldo_deuda >= 1000 and saldo_deuda < 3000 THEN 'C'
                        WHEN saldo_deuda >= 3000 THEN 'D'
                    END) AS saldo_deuda,
                    (CASE 
                        WHEN monto_camp <500 THEN 'A'
                        WHEN monto_camp >= 500 and monto_camp < 1000 THEN 'B'
                        WHEN monto_camp >= 1000 and monto_camp < 3000 THEN 'C'
                        WHEN monto_camp >= 3000 THEN 'D'
                    END) AS monto_camp,
                            'singestion' as res_id_FK
                FROM
                        (SELECT 
                            cli_cod,emp_tel_id_FK,cli_nom, cli_tel_tel
                        FROM
                            creditoy_cobranzas.cliente as clie
                                        INNER JOIN creditoy_cobranzas.cliente_telefono as tele
                                        on clie.cli_id=tele.cli_id_FK
                        WHERE
                            car_id_FK =$car
                                        and cli_tel_est=0 and cli_tel_pas=0
                        AND cli_est =0 and cli_pas=0
                        and (SELECT count(*) FROM creditoy_cobranzas.gestion_cliente WHERE cli_id_FK=cli_id and DATE_FORMAT(ges_cli_fec,'%Y-%m')=date_format( now(), '%Y-%m' ))=0
                        GROUP BY	
                        cli_cod) a
                INNER JOIN indicadores.cartera_detalle AS cd ON a.cli_cod = cd.cuenta
                left JOIN creditoy_cobranzas.empleado as e on a.emp_tel_id_FK=e.emp_id
                left JOIN creditoy_cobranzas.call_telefonica as cal on e.cal_id_FK=cal.cal_id
                WHERE cd.car_id_fk =$car
                AND date_format( fecha, '%Y-%m' ) =date_format( now(), '%Y-%m' )
                GROUP BY cuenta
            ) x
            ) W
        where ";
        $cont=0;
        if($tramos!= "null" && $cont>0){
            $sql=$sql." and (";
            for($i=0;$i<$lengthTramos;$i++){ 
                $sql = $sql." tramo ='".$tramos[$i]."'".(($i == $lengthTramos-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }else if ($tramos!= "null" && $cont==0){
            $sql=$sql." ( ";
            for($i=0;$i<$lengthTramos;$i++){ 
                $sql = $sql." tramo ='".$tramos[$i]."'".(($i == $lengthTramos-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }
        if($deps!= "null" && $cont>0){
            $sql=$sql." and (";
            for($i=0;$i<$lengthDeps;$i++){ 
                $sql = $sql." dep like '%".$deps[$i]."%'".(($i == $lengthDeps-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }else if ($deps!= "null" && $cont==0){
            $sql=$sql." ( ";
            for($i=0;$i<$lengthDeps;$i++){ 
                $sql = $sql." dep LIKE '%".$deps[$i]."%'".(($i == $lengthDeps-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }

        if($prioridades!= "null" && $cont>0){
            $sql=$sql." and (";
            for($i=0;$i<$lengthPrio;$i++){ 
                $sql = $sql." prioridad LIKE '%".$prioridades[$i]."%'".(($i == $lengthPrio-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }else if ($prioridades!= "null" && $cont==0){
            $sql=$sql." ( ";
            for($i=0;$i<$lengthPrio;$i++){ 
                $sql = $sql." prioridad LIKE '%".$prioridades[$i]."%'".(($i == $lengthPrio-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }

        if($situaciones!= "null" && $cont>0){
            $sql=$sql." and (";
            for($i=0;$i<$lengthSitu;$i++){ 
                $sql = $sql." dep_ind LIKE '".$situaciones[$i]."%'".(($i == $lengthSitu-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }else if ($situaciones!= "null" && $cont==0){
            $sql=$sql." (";
            for($i=0;$i<$lengthPrio;$i++){ 
                $sql = $sql." dep_ind LIKE '".$situaciones[$i]."%'".(($i == $lengthSitu-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }

        if($calls!= "null" && $cont>0){
            $sql=$sql." and (";
            for($i=0;$i<$lengthCalls;$i++){ 
                $sql = $sql." cal_nom LIKE '%".$calls[$i]."%'".(($i == $lengthCalls-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }else if ($calls!= "null" && $cont==0){
            $sql=$sql." ( ";
            for($i=0;$i<$lengthCalls;$i++){ 
                $sql = $sql." cal_nom LIKE '%".$calls[$i]."%'".(($i == $lengthCalls-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }

        if($sueldos!= "null" && $cont>0){
            $sql=$sql." and (";
            for($i=0;$i<$lengthSueldos;$i++){ 
                $sql = $sql." rango_sueldo LIKE '".$sueldos[$i]."%'".(($i == $lengthSueldos-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }else if ($sueldos!= "null" && $cont==0){
            $sql=$sql." ( ";
            for($i=0;$i<$lengthSueldos;$i++){ 
                $sql = $sql." rango_sueldo LIKE '".$sueldos[$i]."%'".(($i == $lengthSueldos-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }

        if($capitales!= "null" && $cont>0){
            $sql=$sql." and (";
            for($i=0;$i<$lengthCapitales;$i++){ 
                $sql = $sql." capital LIKE '%".$capitales[$i]."%'".(($i == $lengthCapitales-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }else if ($capitales!= "null" && $cont==0){
            $sql=$sql." ( ";
            for($i=0;$i<$lengthCapitales;$i++){ 
                $sql = $sql." capital LIKE '%".$capitales[$i]."%'".(($i == $lengthCapitales-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }

        if($deudas!= "null" && $cont>0){
            $sql=$sql." and (";
            for($i=0;$i<$lengthDeudas;$i++){ 
                $sql = $sql." saldo_deuda LIKE '%".$deudas[$i]."%'".(($i == $lengthDeudas-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }else if ($deudas!= "null" && $cont==0){
            $sql=$sql." (";
            for($i=0;$i<$lengthDeudas;$i++){ 
                $sql = $sql." saldo_deuda LIKE '%".$deudas[$i]."%'".(($i == $lengthDeudas-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }

        if($importes!= "null" && $cont>0){
            $sql=$sql." and (";
            for($i=0;$i<$lengthImportes;$i++){ 
                $sql = $sql." monto_camp LIKE '%".$importes[$i]."%'".(($i == $lengthImportes-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }else if ($importes!= "null" && $cont==0){
            $sql=$sql." ( ";
            for($i=0;$i<$lengthImportes;$i++){ 
                $sql = $sql." monto_camp LIKE '%".$importes[$i]."%'".(($i == $lengthImportes-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }

        if($ubics!= "null" && $cont>0){
            $sql=$sql." and (";
            for($i=0;$i<$lengthUbics;$i++){ 
                $sql = $sql." res_id_FK LIKE '".$ubics[$i]."%'".(($i == $lengthUbics-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }else if ($ubics!= "null" && $cont==0){
            $sql=$sql." ( ";
            for($i=0;$i<$lengthUbics;$i++){ 
                $sql = $sql." res_id_FK LIKE '".$ubics[$i]."%'".(($i == $lengthUbics-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }

        if($usuarios!= [""]){
            $sql=$sql." and (";
            for($i=0;$i<$lengthUsuarios;$i++){ 
                $sql = $sql." emp_cod = '".$usuarios[$i]."' ".(($i == $lengthUsuarios-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }


        $sql=$sql." group by emp_cod ";
        $query=DB::select(DB::raw($sql));
        return $query;
    }

    public function insertar(Request $request)
    {
        $car = $request->query('car');
        $camp = $request->query('camp');
        $fec_i = $request->query('fec_i');
        $fec_f = $request->query('fec_f');
        $tramos = explode(',',$request->query('tramos'));
        $deps = explode(',',$request->query('deps'));
        $prioridades = explode(',',$request->query('prioridades'));
        $situaciones = explode(',',$request->query('situaciones'));
        $calls = explode(',',$request->query('calls'));
        $sueldos = explode(',',$request->query('sueldos'));
        $capitales = explode(',',$request->query('capitales'));
        $deudas = explode(',',$request->query('deudas'));
        $importes = explode(',',$request->query('importes'));
        $ubics = explode(',',$request->query('ubics'));
        $usuarios = explode(',',$request->query('usuarios'));
        //return $tramos;
       // dd($camp);
       // $lengthTramos = sizeof($tramos); 
        $lengthTramos = count($tramos); 
        $lengthDeps = count($deps);
        $lengthPrio = count($prioridades);
        $lengthSitu = count($situaciones);
        $lengthCalls = count($calls);
        $lengthSueldos = count($sueldos);
        $lengthCapitales = count($capitales);
        $lengthDeudas = count($deudas);
        $lengthImportes = count($importes);
        $lengthUbics = count($ubics);
        $lengthUsuarios = count($usuarios);
        //dd($usuarios);
        //dd($lengthTramos);
        //cuenta,rango_sueldo,dep_ind,prioridad,cal_nom,emp_cod,dep,tramo,capital,saldo_deuda,monto_camp,res_id_FK
        $sql = "
                SELECT 	cuenta,cli_nom,capital,cap,saldo_deuda,sal,monto_camp,importe,emp_cod,emp_nom,cli_tel_tel,res_id_FK,
                        (CASE 
                        WHEN res_id_FK LIKE 'nocontacto%' THEN 'NO CONTACTO'
                        WHEN res_id_FK LIKE 'inubicable%' THEN 'INUBICABLE'
                        WHEN res_id_FK LIKE 'contacto%' THEN 'CONTACTO'
                        WHEN res_id_FK LIKE 'nodisponible%' THEN 'NO DISPONIBLE'
                        WHEN res_id_FK LIKE 'cfrn%' THEN 'CFRN'
                        WHEN res_id_FK LIKE 'singestion%' THEN 'SIN GESTIÓN'
                    END) AS respuesta,
                    cal_nom,cartera,car
                FROM
                (
                SELECT 	cuenta,cli_nom,cli_tel_tel,rango_sueldo,dep_ind,prioridad,cal_nom,emp_cod,emp_nom,cartera,car,dep,tramo,capital,cap,saldo_deuda,sal,monto_camp,importe,res_id_FK
                from
                (SELECT
                    cuenta,cli_nom,if(cli_tel_tel is null,'0',cli_tel_tel) as cli_tel_tel,
                                    capital as cap,saldo_deuda as sal,monto_camp as importe,rango_sueldo,dep_ind,prioridad,
                                    if(cal_nom is null,'SIN CALL',cal_nom) as cal_nom,
                                    if(emp_cod is null,'NO ASIGNADO',emp_cod) as emp_cod,if(emp_nom is null,'NO ASIGNADO',emp_nom) as emp_nom,
                                    cartera,cd.car_id_fk as car,
                            (CASE 
                                WHEN dep LIKE '%TACNA%' or dep LIKE '%JUNIN%' or dep LIKE '%HUANUCO%' or dep LIKE '%UCAYALI%' or
                                            dep LIKE '%LORETO%' or dep LIKE '%CUSCO%' or dep LIKE '%OTROS%' or dep LIKE '%MOQUEGUA%' OR
                                            dep LIKE '%HUANCAVELICA%' or dep LIKE '%SAN MARTIN%' or dep LIKE '%PUNO%' or dep LIKE '%TUMBES%' or 
                                            dep LIKE '%AYACUCHO%' or dep LIKE '%PASCO%' THEN 'OTROS'
                                ELSE dep
                            END) AS dep,
                        if(tramo <=2016,2016,tramo) AS tramo,
                        (CASE 
                            WHEN capital <500 THEN 'A'
                            WHEN capital >= 500 and capital < 1000 THEN 'B'
                            WHEN capital >= 1000 and capital < 3000 THEN 'C'
                            WHEN capital >= 3000 THEN 'D'
                        END) AS capital,
                        (CASE 
                            WHEN saldo_deuda <500 THEN 'A'
                            WHEN saldo_deuda >= 500 and saldo_deuda < 1000 THEN 'B'
                            WHEN saldo_deuda >= 1000 and saldo_deuda < 3000 THEN 'C'
                            WHEN saldo_deuda >= 3000 THEN 'D'
                        END) AS saldo_deuda,
                        (CASE 
                            WHEN monto_camp <500 THEN 'A'
                            WHEN monto_camp >= 500 and monto_camp < 1000 THEN 'B'
                            WHEN monto_camp >= 1000 and monto_camp < 3000 THEN 'C'
                            WHEN monto_camp >= 3000 THEN 'D'
                        END) AS monto_camp,
                        (CASE 
                        WHEN res_id_FK IN ( 38, 6, 22, 41 ) THEN 'cfrn'
                        WHEN res_id_FK IN ( 2, 37, 33, 18, 10, 1, 8, 43, 39, 7 ) THEN 'contacto'
                        WHEN res_id_FK IN ( 34, 17, 21 ) THEN 'nodisponible'
                        WHEN res_id_FK IN ( 19, 27, 12, 26, 13 ) THEN 'inubicable'
                        WHEN res_id_FK IN ( 4,45,44, 25,32 ) THEN 'nocontacto'
                        ELSE 'NO ENCONTRADO'
                    END) AS res_id_FK
                    FROM
                    creditoy_cobranzas.cliente AS c
                                    INNER JOIN creditoy_cobranzas.cliente_telefono as tele
                                    on c.cli_id=tele.cli_id_FK
                    INNER JOIN indicadores.cartera_detalle AS cd ON c.cli_cod = cd.cuenta 
                    INNER JOIN(select max(ges_cli_id) as maxid,cli_id_FK from creditoy_cobranzas.gestion_cliente where date_format( ges_cli_fec, '%Y-%m' ) = date_format( now(), '%Y-%m' ) GROUP BY cli_id_FK) as g
                    on g.cli_id_FK=c.cli_id
                    inner join creditoy_cobranzas.gestion_cliente as gg on gg.ges_cli_id=g.maxid
                            left JOIN creditoy_cobranzas.empleado as e on c.emp_tel_id_FK=e.emp_id
                            left JOIN creditoy_cobranzas.call_telefonica as cal on e.cal_id_FK=cal.cal_id
                    WHERE c.car_id_FK =$car
                                    and cli_tel_est=0 and cli_tel_pas=0
                    AND cli_est =0 and cli_pas=0
                    AND date_format( ges_cli_fec, '%Y-%m' ) = date_format( now(), '%Y-%m' )
                    AND date_format( fecha, '%Y-%m' ) = date_format( now(), '%Y-%m' )
                            GROUP BY	
                    cli_cod
                
                UNION ALL 
                
                SELECT cuenta,cli_nom,if(cli_tel_tel is null,'0',cli_tel_tel) as cli_tel_tel,
                                    capital as cap,saldo_deuda as sal,monto_camp as importe,rango_sueldo,dep_ind,prioridad,
                                    if(cal_nom is null,'SIN CALL',cal_nom) as cal_nom,
                                    if(emp_cod is null,'NO ASIGNADO',emp_cod) as emp_cod,if(emp_nom is null,'NO ASIGNADO',emp_nom) as emp_nom,
                                    cartera,cd.car_id_fk as car,
                            (CASE 
                        WHEN dep LIKE '%TACNA%' or dep LIKE '%JUNIN%' or dep LIKE '%HUANUCO%' or dep LIKE '%UCAYALI%' or
                                    dep LIKE '%LORETO%' or dep LIKE '%CUSCO%' or dep LIKE '%OTROS%' or dep LIKE '%MOQUEGUA%' OR
                                    dep LIKE '%HUANCAVELICA%' or dep LIKE '%SAN MARTIN%' or dep LIKE '%PUNO%' or dep LIKE '%TUMBES%' or 
                                    dep LIKE '%AYACUCHO%' or dep LIKE '%PASCO%' THEN 'OTROS'
                        ELSE dep
                    END) AS dep,
                    if(tramo <=2016,2016,tramo) AS tramo,
                    (CASE 
                        WHEN capital <500 THEN 'A'
                        WHEN capital >= 500 and capital < 1000 THEN 'B'
                        WHEN capital >= 1000 and capital < 3000 THEN 'C'
                        WHEN capital >= 3000 THEN 'D'
                    END) AS capital,
                    (CASE 
                        WHEN saldo_deuda <500 THEN 'A'
                        WHEN saldo_deuda >= 500 and saldo_deuda < 1000 THEN 'B'
                        WHEN saldo_deuda >= 1000 and saldo_deuda < 3000 THEN 'C'
                        WHEN saldo_deuda >= 3000 THEN 'D'
                    END) AS saldo_deuda,
                    (CASE 
                        WHEN monto_camp <500 THEN 'A'
                        WHEN monto_camp >= 500 and monto_camp < 1000 THEN 'B'
                        WHEN monto_camp >= 1000 and monto_camp < 3000 THEN 'C'
                        WHEN monto_camp >= 3000 THEN 'D'
                    END) AS monto_camp,
                            'singestion' as res_id_FK
                FROM
                        (SELECT 
                            cli_cod,emp_tel_id_FK,cli_nom, cli_tel_tel
                        FROM
                            creditoy_cobranzas.cliente as clie
                                        INNER JOIN creditoy_cobranzas.cliente_telefono as tele
                                        on clie.cli_id=tele.cli_id_FK
                        WHERE
                            car_id_FK =$car
                                        and cli_tel_est=0 and cli_tel_pas=0
                        AND cli_est =0 and cli_pas=0
                        and (SELECT count(*) FROM creditoy_cobranzas.gestion_cliente WHERE cli_id_FK=cli_id and DATE_FORMAT(ges_cli_fec,'%Y-%m')=date_format( now(), '%Y-%m' ))=0
                        GROUP BY	
                        cli_cod) a
                INNER JOIN indicadores.cartera_detalle AS cd ON a.cli_cod = cd.cuenta
                left JOIN creditoy_cobranzas.empleado as e on a.emp_tel_id_FK=e.emp_id
                left JOIN creditoy_cobranzas.call_telefonica as cal on e.cal_id_FK=cal.cal_id
                WHERE cd.car_id_fk =$car
                AND date_format( fecha, '%Y-%m' ) =date_format( now(), '%Y-%m' )
                GROUP BY cuenta
            ) x
            ) W
        where ";
        $cont=0;
        if($tramos!= "null" && $cont>0){
            $sql=$sql." and (";
            for($i=0;$i<$lengthTramos;$i++){ 
                $sql = $sql." tramo =".$tramos[$i]." ".(($i == $lengthTramos-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }else if ($tramos!= "null" && $cont==0){
            $sql=$sql." ( ";
            for($i=0;$i<$lengthTramos;$i++){ 
                $sql = $sql." tramo =".$tramos[$i]." ".(($i == $lengthTramos-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }
        if($deps!= "null" && $cont>0){
            $sql=$sql." and (";
            for($i=0;$i<$lengthDeps;$i++){ 
                $sql = $sql." dep like '%".$deps[$i]."%'".(($i == $lengthDeps-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }else if ($deps!= "null" && $cont==0){
            $sql=$sql." ( ";
            for($i=0;$i<$lengthDeps;$i++){ 
                $sql = $sql." dep LIKE '%".$deps[$i]."%'".(($i == $lengthDeps-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }

        if($prioridades!= "null" && $cont>0){
            $sql=$sql." and (";
            for($i=0;$i<$lengthPrio;$i++){ 
                $sql = $sql." prioridad LIKE '%".$prioridades[$i]."%'".(($i == $lengthPrio-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }else if ($prioridades!= "null" && $cont==0){
            $sql=$sql." ( ";
            for($i=0;$i<$lengthPrio;$i++){ 
                $sql = $sql." prioridad LIKE '%".$prioridades[$i]."%'".(($i == $lengthPrio-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }

        if($situaciones!= "null" && $cont>0){
            $sql=$sql." and (";
            for($i=0;$i<$lengthSitu;$i++){ 
                $sql = $sql." dep_ind LIKE '".$situaciones[$i]."%'".(($i == $lengthSitu-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }else if ($situaciones!= "null" && $cont==0){
            $sql=$sql." (";
            for($i=0;$i<$lengthPrio;$i++){ 
                $sql = $sql." dep_ind LIKE '".$situaciones[$i]."%'".(($i == $lengthSitu-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }

        if($calls!= "null" && $cont>0){
            $sql=$sql." and (";
            for($i=0;$i<$lengthCalls;$i++){ 
                $sql = $sql." cal_nom LIKE '%".$calls[$i]."%'".(($i == $lengthCalls-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }else if ($calls!= "null" && $cont==0){
            $sql=$sql." ( ";
            for($i=0;$i<$lengthCalls;$i++){ 
                $sql = $sql." cal_nom LIKE '%".$calls[$i]."%'".(($i == $lengthCalls-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }

        if($sueldos!= "null" && $cont>0){
            $sql=$sql." and (";
            for($i=0;$i<$lengthSueldos;$i++){ 
                $sql = $sql." rango_sueldo LIKE '".$sueldos[$i]."%'".(($i == $lengthSueldos-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }else if ($sueldos!= "null" && $cont==0){
            $sql=$sql." ( ";
            for($i=0;$i<$lengthSueldos;$i++){ 
                $sql = $sql." rango_sueldo LIKE '".$sueldos[$i]."%'".(($i == $lengthSueldos-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }

        if($capitales!= "null" && $cont>0){
            $sql=$sql." and (";
            for($i=0;$i<$lengthCapitales;$i++){ 
                $sql = $sql." capital LIKE '%".$capitales[$i]."%'".(($i == $lengthCapitales-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }else if ($capitales!= "null" && $cont==0){
            $sql=$sql." ( ";
            for($i=0;$i<$lengthCapitales;$i++){ 
                $sql = $sql." capital LIKE '%".$capitales[$i]."%'".(($i == $lengthCapitales-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }

        if($deudas!= "null" && $cont>0){
            $sql=$sql." and (";
            for($i=0;$i<$lengthDeudas;$i++){ 
                $sql = $sql." saldo_deuda LIKE '%".$deudas[$i]."%'".(($i == $lengthDeudas-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }else if ($deudas!= "null" && $cont==0){
            $sql=$sql." (";
            for($i=0;$i<$lengthDeudas;$i++){ 
                $sql = $sql." saldo_deuda LIKE '%".$deudas[$i]."%'".(($i == $lengthDeudas-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }

        if($importes!= "null" && $cont>0){
            $sql=$sql." and (";
            for($i=0;$i<$lengthImportes;$i++){ 
                $sql = $sql." monto_camp LIKE '%".$importes[$i]."%'".(($i == $lengthImportes-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }else if ($importes!= "null" && $cont==0){
            $sql=$sql." ( ";
            for($i=0;$i<$lengthImportes;$i++){ 
                $sql = $sql." monto_camp LIKE '%".$importes[$i]."%'".(($i == $lengthImportes-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }

        if($ubics!= "null" && $cont>0){
            $sql=$sql." and (";
            for($i=0;$i<$lengthUbics;$i++){ 
                $sql = $sql." res_id_FK LIKE '".$ubics[$i]."%'".(($i == $lengthUbics-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }else if ($ubics!= "null" && $cont==0){
            $sql=$sql." ( ";
            for($i=0;$i<$lengthUbics;$i++){ 
                $sql = $sql." res_id_FK LIKE '".$ubics[$i]."%'".(($i == $lengthUbics-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }

        if($usuarios!= [""]){
            $sql2=$sql." and (";
            for($i=0;$i<$lengthUsuarios;$i++){ 
                $sql2 = $sql2." emp_cod ='".$usuarios[$i]."' ".(($i == $lengthUsuarios-1)?"":" OR");
                $cont=1;
            }
            $sql2=$sql2." ) ";
        }else{
            $sql2=$sql;
        }
        $querytabla=DB::select(DB::raw($sql2));
        $query=DB::select(DB::raw($sql2));
        $array = array();
        $variable="";
        foreach($query as $q){
            $array[] = $q->cuenta;
            $car_nom=$q->cartera;
        }
        $cantidad_cli=count($array);
        $cadena=implode(',',$array);
        $cartera=$car_nom;
        $fec_reg=Carbon::now();

        $insertado = DB::insert("
        insert into indicadores.campana (id_cartera,nombre_cartera,nombre_camp,clientes,cant_clientes,fecha_i,fecha_f,fecha_reg)
        values ($car,'$cartera','$camp','$cadena',$cantidad_cli,'$fec_i','$fec_f','$fec_reg')
        ");
       
        return response()->json (['insertado' =>$insertado,'querytabla' =>$querytabla]); 
       
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
