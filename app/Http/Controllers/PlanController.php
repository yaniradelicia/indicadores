<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cartera;
use Carbon\Carbon;
use DB;

class PlanController extends Controller
{
   
    public function index()
    {
        $cartera=Cartera::carteraDetalle();
        return view('plan.crear_plan_trabajo',compact('cartera'));
    }

    public function buscar()
    {
        $cartera=Cartera::cartera2();
        return view('plan.buscar_plan_trabajo',compact('cartera'));
    }

    public function filtro(Request $request)
    {
        $car = $request->query('car');
        $fec_i = $request->query('fec_i');
        $fec_f = $request->query('fec_f');

        $sql = "
            select * from indicadores.plan
            where id_cartera=$car
            and fecha_i between '$fec_i' and '$fec_f'
            and date_format(now(),'%Y-%m-%d') <= fecha_f
        ";
        $query=DB::select(DB::raw($sql));
        return $query;
    }
    public function mostrarDetalle(Request $request)
    {
        $id = $request->query('id');
        $sql = "
            select 
                *
            FROM indicadores.plan
            where id_plan=$id
            ";
        $query=DB::select(DB::raw($sql));
        return $query;
    }

    public function mostrarResultado(Request $request)
    {
        $id = $request->query('id');
        $car = $request->query('car');
        $sql = "
            select 
                clientes,cant_clientes,fecha_i,fecha_f
            FROM indicadores.plan
            where id_cartera=$car and id_plan=$id
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
                            creditoy_cobranzas.gestion_cliente g
                        INNER JOIN creditoy_cobranzas.cliente c ON c.cli_id = g.cli_id_FK
                        INNER JOIN indicadores.cartera_detalle ic ON c.cli_cod = ic.cuenta
                        WHERE
                        ic.car_id_FK=$car
                        AND c.car_id_fk=$car
                        and ges_cli_acc in (1,2)
                        and date_format(fecha,'%Y%m') = date_format(now(),'%Y%m')
                        AND (date_format(ges_cli_fec,'%Y-%m-%d') between '$fecha_i' and '$fecha_f')
                        AND cuenta in ($cadena)
                        GROUP BY cuenta
                    ) t
            )tt
        ";
        $cobertura=DB::select(DB::raw($sqlCobertura));*/

        /*$sqlContacto="
            select 
            if(sum(cli) is null,0,sum(cli)) as can_clientes
            from
            (SELECT         
            if(res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7,34,17,21),1,0) as cli
            FROM
            creditoy_cobranzas.cliente c
            INNER JOIN indicadores.cartera_detalle ic ON c.cli_cod = ic.cuenta
            INNER JOIN(select max(ges_cli_id) as maxid,cli_id_FK from creditoy_cobranzas.gestion_cliente 
                                where (date_format(ges_cli_fec,'%Y-%m-%d') between '$fecha_i' and '$fecha_f') GROUP BY cli_id_FK) as g
            on g.cli_id_FK=c.cli_id
            inner join creditoy_cobranzas.gestion_cliente as gg on gg.ges_cli_id=g.maxid
            WHERE
            ic.car_id_FK=$car
            AND c.car_id_fk=$car
            and date_format(fecha,'%Y%m') = date_format(now(),'%Y%m')
            AND (date_format(ges_cli_fec,'%Y-%m-%d') between '$fecha_i' and '$fecha_f')
            and res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7,34,17,21)
            AND cuenta in ($cadena)
            GROUP BY cuenta
            ) t
                
        ";
        $contacto=DB::select(DB::raw($sqlContacto));*/

        /*$sqlPdp="
            select 
            if(cli_cod,count(cli_cod),0)  as can_clientes, 
            if(monto,sum(monto),0) as monto_pdp,
            if(pdp,count(pdp),0) as can_pdp
            from
                (
                select cli_cod,sum(ges_cli_com_can) as monto,count(ges_cli_com_can) as pdp
                from creditoy_cobranzas.gestion_cliente as g
                right join creditoy_cobranzas.cliente as c on g.cli_id_FK = c.cli_id
                inner join indicadores.cartera_detalle as cd on c.cli_cod =cd.cuenta
                where cd.car_id_fk=$car and c.car_id_fk=$car
                and res_id_fk in ('1','43')
                and date_format(fecha,'%Y-%m') = date_format(now(),'%Y-%m')
                AND (date_format(ges_cli_fec,'%Y-%m-%d') between '$fecha_i' and '$fecha_f')
                and cuenta in ($cadena)
                group By cli_cod
                ) t					
        ";
        $pdp=DB::select(DB::raw($sqlPdp));*/

        /*$sqlConf="
            select 
            if(cli_cod,count(cli_cod),0)  as can_clientes, 
            if(monto,sum(monto),0) as monto_conf,
            if(conf,count(conf),0) as can_conf
            from
                (
                select cli_cod,sum(ges_cli_conf_can) as monto,count(ges_cli_conf_can) as conf
                from creditoy_cobranzas.gestion_cliente as g
                right join creditoy_cobranzas.cliente as c on g.cli_id_FK = c.cli_id
                inner join indicadores.cartera_detalle as cd on c.cli_cod =cd.cuenta
                where cd.car_id_fk=$car and c.car_id_fk=$car
                and res_id_fk in ('2')
                and date_format(fecha,'%Y-%m') = date_format(now(),'%Y-%m')
                AND (date_format(ges_cli_fec,'%Y-%m-%d') between '$fecha_i' and '$fecha_f')
                and cuenta in ($cadena)
                group By cli_cod
                ) t				
        ";
        $conf=DB::select(DB::raw($sqlConf));*/

        $sqlUsuario="
            SELECT emp_cod,emp_nom,count(cli_cod) as cantidad
            FROM
            (
            SELECT cli_cod,emp_cod,emp_nom
            FROM cliente as c
            INNER JOIN indicadores.cartera_detalle as cd on c.cli_cod =cd.cuenta
            INNER JOIN empleado as e on c.emp_tel_id_FK=e.emp_id
            where cd.car_id_fk=$car and c.car_id_fk=$car
            AND cli_est=0 and cli_pas=0
            and date_format(fecha,'%Y-%m') = date_format(now(),'%Y-%m')
            and cuenta in ($cadena)
            group By cli_cod
            ) t
            GROUP BY emp_cod
        ";
        $usuario=DB::select(DB::raw($sqlUsuario));

        /*$sqlneg="
            select 
            if(cli_cod,count(cli_cod),0)  as can_clientes, 
            if(motivo_np,count(motivo_np),0) as can_mot_np
            FROM
            (select cli_cod,count(mot_id_FK) as motivo_np
            from creditoy_cobranzas.gestion_cliente as g
            right join creditoy_cobranzas.cliente as c on g.cli_id_FK = c.cli_id
            inner join indicadores.cartera_detalle as cd on c.cli_cod =cd.cuenta
            where cd.car_id_fk=$car and c.car_id_fk=$car
            and mot_id_FK=3
            and date_format(fecha,'%Y-%m') = date_format(now(),'%Y-%m')
            AND (date_format(ges_cli_fec,'%Y-%m-%d') between '$fecha_i' and '$fecha_f')
            and cuenta in ($cadena)
            group By cli_cod
            ) tt
        ";
        $negocio=DB::select(DB::raw($sqlneg));*/

        $slqmontos="
                SELECT
                sum(pdp) as can_pdp,
                sum(monto_pdp) as monto_pdp,
                sum(conf) as can_conf,
                sum(monto_conf)as monto_conf,
                sum(mot_np) as can_mot_np,
                if(cliente,count(DISTINCT cliente),0) as can_clientes,
                sum(gestiones) as cant_gestiones,
                count(DISTINCT contacto) as can_contacto
                FROM 
                (SELECT
                if(gg.res_id_FK in (2,37,33,10,1,8,43,39,7,3,5,9,34,17,21,18,28,30,35,36,46,47,48,49)
                AND (date_format(gg.ges_cli_fec,'%Y-%m-%d') between '$fecha_i' and '$fecha_f'),cli_cod,null) as contacto,
                    if(ges_cli_tel_id_FK,1,0) as gestiones,
                    cli_cod as cliente,
                    if(g.res_id_FK in (1,43),1,0) as pdp,
                    if(g.res_id_FK in (1,43),g.ges_cli_com_can,0) as monto_pdp,
                    if(g.res_id_FK in (2),1,0) as conf,
                    if(g.res_id_FK in (2),g.ges_cli_conf_can,0) as monto_conf,
                    if(g.mot_id_FK in (3),1,0) as mot_np
                    FROM
                        gestion_cliente as g
                    inner JOIN cliente as c ON g.cli_id_FK=c.cli_id
                    inner JOIN gestion_cliente gg ON c.ges_cli_tel_id_FK=gg.ges_cli_id
                    WHERE
                    c.car_id_FK=$car
                    and cli_est=0
                    and cli_pas=0
                    AND (date_format(g.ges_cli_fec,'%Y-%m-%d') between '$fecha_i' and '$fecha_f')
                    and cli_cod in ($cadena)
                ) t
        ";
        $montos=DB::select(DB::raw($slqmontos));

        return response()->json(['usuario' => $usuario,'montos' => $montos]);
    }

    public function mostrarUsuario(Request $request)
    {

        $id = $request->query('id');
        $car = $request->query('car');
        $cod = $request->query('cod');
        $sql = "
            select 
                clientes,cant_clientes,fecha_i,fecha_f
            FROM indicadores.plan
            where id_cartera=$car and id_plan=$id
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
                            creditoy_cobranzas.gestion_cliente g
                        INNER JOIN creditoy_cobranzas.cliente c ON c.cli_id = g.cli_id_FK
                        INNER JOIN indicadores.cartera_detalle ic ON c.cli_cod = ic.cuenta
                        INNER JOIN creditoy_cobranzas.empleado as e ON c.emp_tel_id_FK=e.emp_id
                        WHERE
                        ic.car_id_FK=$car
                        AND c.car_id_fk=$car
                        and ges_cli_acc in (1,2)
                        and date_format(fecha,'%Y%m') = date_format(now(),'%Y%m')
                        AND (date_format(ges_cli_fec,'%Y-%m-%d') between '$fecha_i' and '$fecha_f')
                        AND emp_cod=$cod
                        AND cuenta in ($cadena)
                        GROUP BY cuenta
                    ) t
            )tt
        ";
        $cobertura=DB::select(DB::raw($sqlCobertura));*/

        /*$sqlContacto="
            select 
            if(sum(cli) is null,0,sum(cli)) as can_clientes
            from
            (SELECT         
            if(res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7,34,17,21),1,0) as cli
            FROM
            creditoy_cobranzas.cliente c
            INNER JOIN indicadores.cartera_detalle ic ON c.cli_cod = ic.cuenta
            INNER JOIN(select max(ges_cli_id) as maxid,cli_id_FK from creditoy_cobranzas.gestion_cliente 
                                where (date_format(ges_cli_fec,'%Y-%m-%d') between '$fecha_i' and '$fecha_f') GROUP BY cli_id_FK) as g
            on g.cli_id_FK=c.cli_id
            inner join creditoy_cobranzas.gestion_cliente as gg on gg.ges_cli_id=g.maxid
            INNER JOIN creditoy_cobranzas.empleado as e ON c.emp_tel_id_FK=e.emp_id
            WHERE
            ic.car_id_FK=$car
            AND c.car_id_fk=$car
            and date_format(fecha,'%Y%m') = date_format(now(),'%Y%m')
            AND (date_format(ges_cli_fec,'%Y-%m-%d') between '$fecha_i' and '$fecha_f')
            and res_id_FK in (38,6,22,41,2,37,33,10,1,8,43,39,7,34,17,21)
            AND emp_cod=$cod
            AND cuenta in ($cadena)
            GROUP BY cuenta
            ) t
                
        ";
        $contacto=DB::select(DB::raw($sqlContacto));*/

        /*$sqlPdp="
            select 
            if(cli_cod,count(cli_cod),0)  as can_clientes, 
            if(monto,sum(monto),0) as monto_pdp,
            if(pdp,count(pdp),0) as can_pdp
            from
                (
                select cli_cod,sum(ges_cli_com_can) as monto,count(ges_cli_com_can) as pdp
                from creditoy_cobranzas.gestion_cliente as g
                right join creditoy_cobranzas.cliente as c on g.cli_id_FK = c.cli_id
                inner join indicadores.cartera_detalle as cd on c.cli_cod =cd.cuenta
                INNER JOIN creditoy_cobranzas.empleado as e ON c.emp_tel_id_FK=e.emp_id
                where cd.car_id_fk=$car and c.car_id_fk=$car
                and res_id_fk in ('1','43')
                and date_format(fecha,'%Y-%m') = date_format(now(),'%Y-%m')
                AND (date_format(ges_cli_fec,'%Y-%m-%d') between '$fecha_i' and '$fecha_f')
                AND emp_cod=$cod
                and cuenta in ($cadena)
                group By cli_cod
                ) t					
        ";
        $pdp=DB::select(DB::raw($sqlPdp));

        $sqlConf="
            select 
            if(cli_cod,count(cli_cod),0)  as can_clientes, 
            if(monto,sum(monto),0) as monto_conf,
            if(conf,count(conf),0) as can_conf
            from
                (
                select cli_cod,sum(ges_cli_conf_can) as monto,count(ges_cli_conf_can) as conf
                from creditoy_cobranzas.gestion_cliente as g
                right join creditoy_cobranzas.cliente as c on g.cli_id_FK = c.cli_id
                inner join indicadores.cartera_detalle as cd on c.cli_cod =cd.cuenta
                INNER JOIN creditoy_cobranzas.empleado as e ON c.emp_tel_id_FK=e.emp_id
                where cd.car_id_fk=$car and c.car_id_fk=$car
                and res_id_fk in ('2')
                and date_format(fecha,'%Y-%m') = date_format(now(),'%Y-%m')
                AND (date_format(ges_cli_fec,'%Y-%m-%d') between '$fecha_i' and '$fecha_f')
                AND emp_cod=$cod
                and cuenta in ($cadena)
                group By cli_cod
                ) t				
        ";
        $conf=DB::select(DB::raw($sqlConf));
        $sqlneg="
            select 
            if(cli_cod,count(cli_cod),0)  as can_clientes, 
            if(motivo_np,count(motivo_np),0) as can_mot_np
            FROM
            (select cli_cod,count(mot_id_FK) as motivo_np
            from creditoy_cobranzas.gestion_cliente as g
            right join creditoy_cobranzas.cliente as c on g.cli_id_FK = c.cli_id
            inner join indicadores.cartera_detalle as cd on c.cli_cod =cd.cuenta
            INNER JOIN creditoy_cobranzas.empleado as e ON c.emp_tel_id_FK=e.emp_id
            where cd.car_id_fk=$car and c.car_id_fk=$car
            and mot_id_FK=3
            and date_format(fecha,'%Y-%m') = date_format(now(),'%Y-%m')
            AND (date_format(ges_cli_fec,'%Y-%m-%d') between '$fecha_i' and '$fecha_f')
            AND emp_cod=$cod
            and cuenta in ($cadena)
            group By cli_cod
            ) tt
        ";
        $negocio=DB::select(DB::raw($sqlneg));*/

        $slqmontos="
                SELECT
                sum(pdp) as can_pdp,
                sum(monto_pdp) as monto_pdp,
                sum(conf) as can_conf,
                sum(monto_conf)as monto_conf,
                sum(mot_np) as can_mot_np,
                if(cliente,count(DISTINCT cliente),0) as can_clientes,
                sum(gestiones) as cant_gestiones,
                count(DISTINCT contacto) as can_contacto
                FROM 
                (SELECT
                    if(gg.res_id_FK in (2,37,33,10,1,8,43,39,7,3,5,9,34,17,21,18,28,30,35,36,46,47,48,49)
                    AND (date_format(gg.ges_cli_fec,'%Y-%m-%d') between '$fecha_i' and '$fecha_f'),cli_cod,null) as contacto,
                    if(ges_cli_tel_id_FK,1,0) as gestiones,
                    cli_cod as cliente,
                    if(g.res_id_FK in (1,43),1,0) as pdp,
                    if(g.res_id_FK in (1,43),g.ges_cli_com_can,0) as monto_pdp,
                    if(g.res_id_FK in (2),1,0) as conf,
                    if(g.res_id_FK in (2),g.ges_cli_conf_can,0) as monto_conf,
                    if(g.mot_id_FK in (3),1,0) as mot_np
                    FROM
                        gestion_cliente as g
                    inner JOIN cliente as c ON g.cli_id_FK=c.cli_id
                    inner JOIN gestion_cliente gg ON c.ges_cli_tel_id_FK=gg.ges_cli_id
                    INNER JOIN creditoy_cobranzas.empleado as e ON c.emp_tel_id_FK=e.emp_id
                    WHERE
                    c.car_id_FK=$car
                    AND emp_cod=$cod
                    and cli_est=0
                    and cli_pas=0
                    AND (date_format(g.ges_cli_fec,'%Y-%m-%d') between '$fecha_i' and '$fecha_f')
                    and cli_cod in ($cadena)
                ) t
        ";
        $montos=DB::select(DB::raw($slqmontos));



        return response()->json(['montos' => $montos]);

    }

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
        $entidades = explode(',',$request->query('entidades'));
        $clientes = explode(',',$request->query('clientes'));
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
        $lengthEntidades = count($entidades);
        $lengthClientes = count($clientes);
        $lengthUsuarios = count($usuarios);
        //dd($usuarios!= [""]);
        //dd($lengthTramos);
        //cuenta,rango_sueldo,dep_ind,prioridad,cal_nom,emp_cod,dep,tramo,capital,saldo_deuda,monto_camp,res_id_FK
        //SELECT 	count(cuenta) as cantidad,emp_cod as usuario
        $sql = "
            SELECT
                count(cuenta) as cantidad,emp_cod as usuario, emp_nom as usuario_nom
            FROM 
            (SELECT
                cuenta,
                    cli_nom,
                    if(cli_tel_tel is null,'0',cli_tel_tel) as cli_tel_tel,
                    capital as cap,
                    saldo_deuda as sal,
                    monto_camp as importe,
                    rango_sueldo,
                    dep_ind,
                    prioridad,
                    if(cal_nom is null,'SIN CALL',cal_nom) as cal_nom,
                    if(emp_cod is null,'NO ASIGNADO',emp_cod) as emp_cod,
                    if(emp_nom is null,'NO ASIGNADO',emp_nom) as emp_nom,
                    cartera,
                    cd.car_id_fk as car,
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
                            WHEN ges_cli_fec IS NULL OR DATE_FORMAT(ges_cli_fec,'%Y%m')<DATE_FORMAT(NOW(),'%Y%m') THEN 'SG' 
                            WHEN res_id_FK IN ( 38, 6, 22, 41 ) THEN 'cfrn'
                            WHEN res_id_FK IN ( 2,37,33,10,1,8,43,39,7,3,5,9,34,17,21,18,28,30,35,36,46,47,48,49 ) THEN 'contacto'
                            WHEN res_id_FK IN ( 32 ) THEN 'nodisponible'
                            WHEN res_id_FK IN ( 19,27,12,26,13,4,11,12,20,14,15,16,23,24,29,31 ) THEN 'inubicable'
                            WHEN res_id_FK IN ( 45,44, 25 ) THEN 'nocontacto'
                            ELSE 'NO ENCONTRADO'
                    END) AS res_id_FK,
                (CASE 
                    WHEN entidades like '%1%' or entidades = 2 THEN 2
                    WHEN entidades like '%2%' or entidades = 3 THEN 3
                    WHEN entidades like '%3%' or entidades >= 4 THEN 4
                    ELSE 1
                END) AS entidad,
                if(cli_nuev_cod is null,'NO','NUEVO') as nuevo
            FROM
                indicadores.cartera_detalle cd
            INNER JOIN creditoy_cobranzas.cliente c ON c.cli_cod = cd.cuenta
            LEFT JOIN creditoy_cobranzas.cliente_telefono as tele on c.cli_id=tele.cli_id_FK and cli_tel_est=0 and cli_tel_pas=0
            left JOIN creditoy_cobranzas.empleado as e on c.emp_tel_id_FK=e.emp_id
            left JOIN creditoy_cobranzas.call_telefonica as cal on e.cal_id_FK=cal.cal_id
            LEFT JOIN creditoy_cobranzas.nuevo_cliente as cn on c.cli_cod=cn.cli_nuev_cod
            LEFT JOIN gestion_cliente g on c.ges_cli_tel_id_FK=g.ges_cli_id
            WHERE 
                    cli_est=0
                and cli_pas=0
                and c.car_id_fk=$car
                and date_format(cd.fecha,'%Y%m')=date_format(now(),'%Y%m') 
                and cd.car_id_FK=$car
            GROUP BY cuenta
            )t
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

        if($entidades!= "null" && $cont>0){
            $sql=$sql." and (";
            for($i=0;$i<$lengthEntidades;$i++){ 
                $sql = $sql." entidad = '".$entidades[$i]."'".(($i == $lengthEntidades-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }else if ($entidades!= "null" && $cont==0){
            $sql=$sql." ( ";
            for($i=0;$i<$lengthEntidades;$i++){ 
                $sql = $sql." entidad = '".$entidades[$i]."'".(($i == $lengthEntidades-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }

        if($clientes!= "null" && $cont>0){
            $sql=$sql." and (";
            for($i=0;$i<$lengthClientes;$i++){ 
                $sql = $sql." nuevo LIKE '%".$clientes[$i]."%'".(($i == $lengthClientes-1)?"":" OR");
                $cont=1;
            }
            $sql=$sql." ) ";
        }else if ($clientes!= "null" && $cont==0){
            $sql=$sql." ( ";
            for($i=0;$i<$lengthClientes;$i++){ 
                $sql = $sql." nuevo LIKE '%".$clientes[$i]."%'".(($i == $lengthClientes-1)?"":" OR");
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
        $plan = $request->query('plan');
        $speech = $request->query('speech');
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
        $entidades = explode(',',$request->query('entidades'));
        $clientes = explode(',',$request->query('clientes'));
        $usuarios = explode(',',$request->query('usuarios'));
        
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
        $lengthEntidades = count($entidades);
        $lengthClientes = count($clientes);
        $lengthUsuarios = count($usuarios);
        //dd($usuarios);
        //dd($usuarios!= [""]);
        $des="";
        $sql = "
            SELECT cuenta,cli_nom,cli_tel_tel,cap,sal,importe,rango_sueldo,dep_ind,prioridad,
                    cal_nom,emp_cod,emp_nom,cartera,car,dep,tramo,capital,saldo_deuda,monto_camp,
                    entidad,nuevo,res_id_FK,
                    (CASE 
                        WHEN res_id_FK LIKE 'nocontacto%' THEN 'NO CONTACTO'
                        WHEN res_id_FK LIKE 'inubicable%' THEN 'INUBICABLE'
            WHEN res_id_FK LIKE 'contacto%' THEN 'CONTACTO'
            WHEN res_id_FK LIKE 'nodisponible%' THEN 'NO DISPONIBLE'
            WHEN res_id_FK LIKE 'cfrn%' THEN 'CFRN'
            WHEN res_id_FK LIKE 'SG%' THEN 'SIN GESTIÓN'
                END) AS respuesta
            FROM
            (
            SELECT
            cuenta,
                cli_nom,
                if(cli_tel_tel is null,'0',cli_tel_tel) as cli_tel_tel,
                capital as cap,
                saldo_deuda as sal,
                monto_camp as importe,
                rango_sueldo,
                dep_ind,
                prioridad,
                if(cal_nom is null,'SIN CALL',cal_nom) as cal_nom,
                if(emp_cod is null,'NO ASIGNADO',emp_cod) as emp_cod,
                if(emp_nom is null,'NO ASIGNADO',emp_nom) as emp_nom,
                cartera,
                cd.car_id_fk as car,
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
                        WHEN ges_cli_fec IS NULL OR DATE_FORMAT(ges_cli_fec,'%Y%m')<DATE_FORMAT(NOW(),'%Y%m') THEN 'SG' 
                        WHEN res_id_FK IN ( 38, 6, 22, 41 ) THEN 'cfrn'
                        WHEN res_id_FK IN ( 2,37,33,10,1,8,43,39,7,3,5,9,34,17,21,18,28,30,35,36,46,47,48,49 ) THEN 'contacto'
                        WHEN res_id_FK IN ( 32 ) THEN 'nodisponible'
                        WHEN res_id_FK IN ( 19,27,12,26,13,4,11,12,20,14,15,16,23,24,29,31 ) THEN 'inubicable'
                        WHEN res_id_FK IN ( 45,44, 25 ) THEN 'nocontacto'
                        ELSE 'NO ENCONTRADO'
                END) AS res_id_FK,
            (CASE 
                WHEN entidades like '%1%' or entidades = 2 THEN 2
                WHEN entidades like '%2%' or entidades = 3 THEN 3
                WHEN entidades like '%3%' or entidades >= 4 THEN 4
                ELSE 1
            END) AS entidad,
            if(cli_nuev_cod is null,'NO','NUEVO') as nuevo
            FROM
                indicadores.cartera_detalle cd
            INNER JOIN creditoy_cobranzas.cliente c ON c.cli_cod = cd.cuenta
            LEFT JOIN creditoy_cobranzas.cliente_telefono as tele on c.cli_id=tele.cli_id_FK and cli_tel_est=0 and cli_tel_pas=0
            left JOIN creditoy_cobranzas.empleado as e on c.emp_tel_id_FK=e.emp_id
            left JOIN creditoy_cobranzas.call_telefonica as cal on e.cal_id_FK=cal.cal_id
            LEFT JOIN creditoy_cobranzas.nuevo_cliente as cn on c.cli_cod=cn.cli_nuev_cod
            LEFT JOIN gestion_cliente g on c.ges_cli_tel_id_FK=g.ges_cli_id
            WHERE 
                    cli_est=0
                and cli_pas=0
                and c.car_id_fk=$car
                and date_format(cd.fecha,'%Y%m')=date_format(now(),'%Y%m') 
                and cd.car_id_FK=$car
            GROUP BY cuenta
            ) t
            where 
        ";

        $cont=0;
        $espacio="";
        //---------------tramosssssssssssssss---------------------------------------------
        if($tramos!= "null" && $cont>0){
            $sql=$sql." and (";
            $des=$des."; Tramos: ";
            $a="";
            for($i=0;$i<$lengthTramos;$i++){ 
                $sql = $sql." tramo ='".$tramos[$i]."'".(($i == $lengthTramos-1)?"":" OR");
                if($lengthTramos==5){
                    $a = "TODOS";
                }else{
                    if($tramos[$i]=="2016"){
                        $a = $a."<=2016".(($i == $lengthTramos-1)?"":",");
                    }else{
                        $a = $a."".$tramos[$i]."".(($i == $lengthTramos-1)?"":",");
                    }
                    //$a = $a."".$tramos[$i]=='2016'?"<=2016":$tramos[$i]."".(($i == $lengthTramos-1)?"":",");
                }
                //$des = $des."".$tramos[$i]."".(($i == $lengthTramos-1)?"":",");
                $cont=1;
            }
            $des=$des.$a;
            $sql=$sql." ) ";
        }else if ($tramos!= "null" && $cont==0){
            $sql=$sql." ( ";
            $des=$des."Tramos: ";
            $a="";
            for($i=0;$i<$lengthTramos;$i++){ 
                $sql = $sql." tramo ='".$tramos[$i]."'".(($i == $lengthTramos-1)?"":" OR");
                if($lengthTramos==5){
                    $a = "TODOS";
                }else{
                    if($tramos[$i]=="2016"){
                        $a = $a."<=2016".(($i == $lengthTramos-1)?"":",");
                    }else{
                        $a = $a."".$tramos[$i]."".(($i == $lengthTramos-1)?"":",");
                    }
                }
                //$des = $des."".$tramos[$i]."".(($i == $lengthTramos-1)?"":",");
                $cont=1;
            }
            $des=$des.$a;
            $sql=$sql." ) ";
        }
        //------------------------------departamentos------------------------------------------------
        if($deps!= "null" && $cont>0){
            $sql=$sql." and (";
            $des=$des."; Departamentos: ";
            $b="";
            for($i=0;$i<$lengthDeps;$i++){ 
                $sql = $sql." dep like '%".$deps[$i]."%'".(($i == $lengthDeps-1)?"":" OR");
                if($lengthDeps==10){
                    $b = "TODOS";
                }else{
                    $b=$b."".$deps[$i]."".(($i == $lengthDeps-1)?"":",");
                }
                //$des=$des."".$deps[$i]."".(($i == $lengthDeps-1)?"":",");
                $cont=1;
            }
            $des=$des.$b;
            $sql=$sql." ) ";
        }else if ($deps!= "null" && $cont==0){
            $sql=$sql." ( ";
            $des=$des."Departamentos: ";
            $b="";
            for($i=0;$i<$lengthDeps;$i++){ 
                $sql = $sql." dep LIKE '%".$deps[$i]."%'".(($i == $lengthDeps-1)?"":" OR");
                if($lengthDeps==10){
                    $b = "TODOS";
                }else{
                    $b=$b."".$deps[$i]."".(($i == $lengthDeps-1)?"":",");
                }
                //$des=$des."".$deps[$i]."".(($i == $lengthDeps-1)?"":",");
                $cont=1;
            }
            $des=$des.$b;
            $sql=$sql." ) ";
        }
        //--------------------------------------------------------------------

        //--------------------------------prioridad-----------------------------
        if($prioridades!= "null" && $cont>0){
            $sql=$sql." and (";
            $des=$des."; Prioridad: ";
            $k="";
            for($i=0;$i<$lengthPrio;$i++){ 
                $sql = $sql." prioridad LIKE '%".$prioridades[$i]."%'".(($i == $lengthPrio-1)?"":" OR");
                if($lengthPrio==4){
                    $k = "TODOS";
                }else{
                    $k=$k."".$prioridades[$i]."".(($i == $lengthPrio-1)?"":",");
                }
                //$des=$des."".$prioridades[$i]."".(($i == $lengthPrio-1)?"":",");
                $cont=1;
            }
            $des=$des.$k;
            $sql=$sql." ) ";
        }else if ($prioridades!= "null" && $cont==0){
            $sql=$sql." ( ";
            $des=$des."Prioridad: ";
            $k="";
            for($i=0;$i<$lengthPrio;$i++){ 
                $sql = $sql." prioridad LIKE '%".$prioridades[$i]."%'".(($i == $lengthPrio-1)?"":" OR");
                if($lengthPrio==4){
                    $k = "TODOS";
                }else{
                    $k=$k."".$prioridades[$i]."".(($i == $lengthPrio-1)?"":",");
                }
                //$des=$des."".$prioridades[$i]."".(($i == $lengthPrio-1)?"":",");
                $cont=1;
            }
            $des=$des.$k;
            $sql=$sql." ) ";
        }
        //----------------------situacion----------------
        if($situaciones!= "null" && $cont>0){
            $sql=$sql." and (";
            $des=$des."; Situación Laboral: ";
            $l="";
            for($i=0;$i<$lengthSitu;$i++){ 
                $sql = $sql." dep_ind LIKE '".$situaciones[$i]."%'".(($i == $lengthSitu-1)?"":" OR");
                if($lengthSitu==5){
                    $l = "TODOS";
                }else{
                    $l=$l."".$situaciones[$i]."".(($i == $lengthSitu-1)?"":",");
                }
                //$des=$des."".$situaciones[$i]."".(($i == $lengthSitu-1)?"":",");
                $cont=1;
            }
            $des=$des.$l;
            $sql=$sql." ) ";
        }else if ($situaciones!= "null" && $cont==0){
            $sql=$sql." (";
            $des=$des."Situación Laboral: ";
            $l="";
            for($i=0;$i<$lengthSitu;$i++){ 
                $sql = $sql." dep_ind LIKE '".$situaciones[$i]."%'".(($i == $lengthSitu-1)?"":" OR");
                if($lengthSitu==5){
                    $l = "TODOS";
                }else{
                    $l=$l."".$situaciones[$i]."".(($i == $lengthSitu-1)?"":",");
                }
                //$des=$des."".$situaciones[$i]."".(($i == $lengthSitu-1)?"":",");
                $cont=1;
            }
            $des=$des.$l;
            $sql=$sql." ) ";
        }

        //--------------------call--------------
        if($calls!= "null" && $cont>0){
            $sql=$sql." and (";
            $des=$des."; Call: ";
            $m="";
            for($i=0;$i<$lengthCalls;$i++){ 
                $sql = $sql." cal_nom LIKE '%".$calls[$i]."%'".(($i == $lengthCalls-1)?"":" OR");
                if($lengthCalls==4){
                    $m = "TODOS";
                }else{
                    $m=$m."".$calls[$i]."".(($i == $lengthCalls-1)?"":",");
                }
                //$des=$des."".$calls[$i]."".(($i == $lengthCalls-1)?"":",");
                $cont=1;
            }
            $des=$des.$m;
            $sql=$sql." ) ";
        }else if ($calls!= "null" && $cont==0){
            $sql=$sql." ( ";
            $des=$des."Call: ";
            $m="";
            for($i=0;$i<$lengthCalls;$i++){ 
                $sql = $sql." cal_nom LIKE '%".$calls[$i]."%'".(($i == $lengthCalls-1)?"":" OR");
                if($lengthCalls==4){
                    $m = "TODOS";
                }else{
                    $m=$m."".$calls[$i]."".(($i == $lengthCalls-1)?"":",");
                }
                //$des=$des."".$calls[$i]."".(($i == $lengthCalls-1)?"":",");
                $cont=1;
            }
            $des=$des.$m;
            $sql=$sql." ) ";
        }

        //--------------sueldo-------------------------------------------------------------
        if($sueldos!= "null" && $cont>0){
            $sql=$sql." and (";
            $des=$des."; Rango Sueldo: ";
            $j="";
            for($i=0;$i<$lengthSueldos;$i++){ 
                $sql = $sql." rango_sueldo LIKE '".$sueldos[$i]."%'".(($i == $lengthSueldos-1)?"":" OR");
                //dd($lengthSueldos);
                if($lengthSueldos==5){
                    $j = "TODOS";
                }else{
                    if($sueldos[$i]=="A"){
                        $j =$j. "[0-500>".(($i == $lengthSueldos-1)?"":",");
                    }else if($sueldos[$i]=="B"){
                        $j = $j."[500-1000>".(($i == $lengthSueldos-1)?"":",");
                    }else if($sueldos[$i]=="C"){
                        $j = $j."[1000-3000>".(($i == $lengthSueldos-1)?"":",");
                    }else if($sueldos[$i]=="D"){
                        $j = $j."[3000+>".(($i == $lengthSueldos-1)?"":",");
                    }else{
                        $j = $j."SIN DATO".(($i == $lengthSueldos-1)?"":",");
                    }
                }
                //dd($d);
                //$des=$des."".$sueldos[$i]."".(($i == $lengthSueldos-1)?"":",");
                $cont=1;
            }
            $des=$des.$j;
            $sql=$sql." ) ";
        }else if ($sueldos!= "null" && $cont==0){
            $sql=$sql." ( ";
            $des=$des."Rango Sueldo: ";
            $j="";
            for($i=0;$i<$lengthSueldos;$i++){ 
                $sql = $sql." rango_sueldo LIKE '".$sueldos[$i]."%'".(($i == $lengthSueldos-1)?"":" OR");
                if($lengthSueldos==5){
                    $j = "TODOS";
                }else{
                    if($sueldos[$i]=="A"){
                        $j=$j."[0-500>".(($i == $lengthSueldos-1)?"":",");
                    }else if($sueldos[$i]=="B"){
                        $j=$j."[500-1000>".(($i == $lengthSueldos-1)?"":",");
                    }else if($sueldos[$i]=="C"){
                        $j=$j."[1000-3000>".(($i == $lengthSueldos-1)?"":",");
                    }else if($sueldos[$i]=="D"){
                        $j=$j."[3000+>".(($i == $lengthSueldos-1)?"":",");
                    }else{
                        $j=$j."SIN DATO".(($i == $lengthSueldos-1)?"":",");
                    }
                }
                //$des=$des."".$sueldos[$i]."".(($i == $lengthSueldos-1)?"":",");
                $cont=1;
            }
            $des=$des.$j;
            $sql=$sql." ) ";
        }

        //----------------------capital-------------------------------------------
        if($capitales!= "null" && $cont>0){
            $sql=$sql." and (";
            $des=$des."; Rango Capital: ";
            $d="";
            for($i=0;$i<$lengthCapitales;$i++){ 
                $sql = $sql." capital LIKE '%".$capitales[$i]."%'".(($i == $lengthCapitales-1)?"":" OR");
                if($lengthCapitales==4){
                    $d = "TODOS";
                }else{
                    if($capitales[$i]=="A"){
                        $d=$d."[0-500>".(($i == $lengthCapitales-1)?"":",");
                    }else if($capitales[$i]=="B"){
                        $d=$d."[500-1000>".(($i == $lengthCapitales-1)?"":",");
                    }else if($capitales[$i]=="C"){
                        $d=$d."[1000-3000>".(($i == $lengthCapitales-1)?"":",");
                    }else{
                        $d=$d."[3000+>".(($i == $lengthCapitales-1)?"":",");
                    }
                }
                //$des=$des."".$capitales[$i]."".(($i == $lengthCapitales-1)?"":",");
                $cont=1;
            }
            $des=$des.$d;
            $sql=$sql." ) ";
        }else if ($capitales!= "null" && $cont==0){
            $sql=$sql." ( ";
            $des=$des."Rango Capital: ";
            $d="";
            for($i=0;$i<$lengthCapitales;$i++){ 
                $sql = $sql." capital LIKE '%".$capitales[$i]."%'".(($i == $lengthCapitales-1)?"":" OR");
                if($lengthCapitales==4){
                    $d = "TODOS";
                }else{
                    if($capitales[$i]=="A"){
                        $d=$d."[0-500>".(($i == $lengthCapitales-1)?"":",");
                    }else if($capitales[$i]=="B"){
                        $d=$d."[500-1000>".(($i == $lengthCapitales-1)?"":",");
                    }else if($capitales[$i]=="C"){
                        $d=$d."[1000-3000>".(($i == $lengthCapitales-1)?"":",");
                    }else{
                        $d=$d."[3000+>".(($i == $lengthCapitales-1)?"":",");
                    }
                }
                //$des=$des."".$capitales[$i]."".(($i == $lengthCapitales-1)?"":",");
                $cont=1;
            }
            $des=$des.$d;
            $sql=$sql." ) ";
        }

        //-------------------------deudad-----------------------------------------
        if($deudas!= "null" && $cont>0){
            $sql=$sql." and (";
            $des=$des."; Rango Deuda: ";
            $e="";
            for($i=0;$i<$lengthDeudas;$i++){ 
                $sql = $sql." saldo_deuda LIKE '%".$deudas[$i]."%'".(($i == $lengthDeudas-1)?"":" OR");
                if($lengthDeudas==4){
                    $e = "TODOS";
                }else{
                    if($deudas[$i]=="A"){
                        $e=$e."[0-500>".(($i == $lengthDeudas-1)?"":",");
                    }else if($deudas[$i]=="B"){
                        $e=$e."[500-1000>".(($i == $lengthDeudas-1)?"":",");
                    }else if($deudas[$i]=="C"){
                        $e=$e."[1000-3000>".(($i == $lengthDeudas-1)?"":",");
                    }else{
                        $e=$e."[3000+>".(($i == $lengthDeudas-1)?"":",");
                    }
                }
                //$des=$des."".$deudas[$i]."".(($i == $lengthDeudas-1)?"":",");
                $cont=1;
            }
            $des=$des.$e;
            $sql=$sql." ) ";
        }else if ($deudas!= "null" && $cont==0){
            $sql=$sql." (";
            $des=$des."Rango Deuda: ";
            $e="";
            for($i=0;$i<$lengthDeudas;$i++){ 
                $sql = $sql." saldo_deuda LIKE '%".$deudas[$i]."%'".(($i == $lengthDeudas-1)?"":" OR");
                if($lengthDeudas==4){
                    $e = "TODOS";
                }else{
                    if($deudas[$i]=="A"){
                        $e=$e."[0-500>".(($i == $lengthDeudas-1)?"":",");
                    }else if($deudas[$i]=="B"){
                        $e=$e."[500-1000>".(($i == $lengthDeudas-1)?"":",");
                    }else if($deudas[$i]=="C"){
                        $e=$e."[1000-3000>".(($i == $lengthDeudas-1)?"":",");
                    }else{
                        $e=$e."[3000+>".(($i == $lengthDeudas-1)?"":",");
                    }
                }
                //$des=$des."".$deudas[$i]."".(($i == $lengthDeudas-1)?"":",");
                $cont=1;
            }
            $des=$des.$e;
            $sql=$sql." ) ";
        }

        //----------------------------importess-------------------------------------
        if($importes!= "null" && $cont>0){
            $sql=$sql." and (";
            $des=$des."; Rango Importe: ";
            $f="";
            for($i=0;$i<$lengthImportes;$i++){ 
                $sql = $sql." monto_camp LIKE '%".$importes[$i]."%'".(($i == $lengthImportes-1)?"":" OR");
                if($lengthImportes==4){
                    $f ="TODOS";
                }else{
                    if($importes[$i]=="A"){
                        $f=$f."[0-500>".(($i == $lengthImportes-1)?"":",");
                    }else if($importes[$i]=="B"){
                        $f=$f."[500-1000>".(($i == $lengthImportes-1)?"":",");
                    }else if($importes[$i]=="C"){
                        $f=$f."[1000-3000>".(($i == $lengthImportes-1)?"":",");
                    }else{
                        $f=$f."[3000+>".(($i == $lengthImportes-1)?"":",");
                    }
                }
                //$des=$des."".$importes[$i]."".(($i == $lengthImportes-1)?"":",");
                $cont=1;
            }
            $des=$des.$f;
            $sql=$sql." ) ";
        }else if ($importes!= "null" && $cont==0){
            $sql=$sql." ( ";
            $des=$des."Rango Importe: ";
            $f="";
            for($i=0;$i<$lengthImportes;$i++){ 
                $sql = $sql." monto_camp LIKE '%".$importes[$i]."%'".(($i == $lengthImportes-1)?"":" OR");
                if($lengthImportes==4){
                    $f = "TODOS";
                }else{
                    if($importes[$i]=="A"){
                        $f=$f."[0-500>".(($i == $lengthImportes-1)?"":",");
                    }else if($importes[$i]=="B"){
                        $f=$f."[500-1000>".(($i == $lengthImportes-1)?"":",");
                    }else if($importes[$i]=="C"){
                        $f=$f."[1000-3000>".(($i == $lengthImportes-1)?"":",");
                    }else{
                        $f=$f."[3000+>".(($i == $lengthImportes-1)?"":",");
                    }
                }
                //$des=$des."".$importes[$i]."".(($i == $lengthImportes-1)?"":",");
                $cont=1;
            }
            $des=$des.$f;
            $sql=$sql." ) ";
        }

        //------------------ubicabilidadd--------------------------------
        if($ubics!= "null" && $cont>0){
            $sql=$sql." and (";
            $des=$des."; Ubicabilidad: ";
            $g="";
            for($i=0;$i<$lengthUbics;$i++){ 
                $sql = $sql." res_id_FK LIKE '".$ubics[$i]."%'".(($i == $lengthUbics-1)?"":" OR");
                if($lengthUbics==6){
                    $g = "TODOS";
                }else{
                    if($ubics[$i]=="cfrn"){
                        $g=$g."C-F-R-N".(($i == $lengthUbics-1)?"":",");
                    }else if($ubics[$i]=="contacto"){
                        $g=$g."CONTACTO".(($i == $lengthUbics-1)?"":",");
                    }else if($ubics[$i]=="nodisponible"){
                        $g=$g."NO DISPONIBLE".(($i == $lengthUbics-1)?"":",");
                    }else if($ubics[$i]=="nocontacto"){
                        $g=$g."NO CONTACTO".(($i == $lengthUbics-1)?"":",");
                    }else if($ubics[$i]=="inublicable"){
                        $g=$g."INUBICABLE".(($i == $lengthUbics-1)?"":",");
                    }else{
                        $g=$g."SIN GESTIÓN".(($i == $lengthUbics-1)?"":",");
                    }
                }
                //$des=$des."".$ubics[$i]."".(($i == $lengthUbics-1)?"":",");
                $cont=1;
            }
            $des=$des.$g;
            $sql=$sql." ) ";
        }else if ($ubics!= "null" && $cont==0){
            $sql=$sql." ( ";
            $des=$des."Ubicabilidad: ";
            $g="";
            for($i=0;$i<$lengthUbics;$i++){ 
                $sql = $sql." res_id_FK LIKE '".$ubics[$i]."%'".(($i == $lengthUbics-1)?"":" OR");
                if($lengthUbics==6){
                    $g = "TODOS";
                }else{
                    if($ubics[$i]=="cfrn"){
                        $g=$g."C-F-R-N".(($i == $lengthUbics-1)?"":",");
                    }else if($ubics[$i]=="contacto"){
                        $g=$g."CONTACTO".(($i == $lengthUbics-1)?"":",");
                    }else if($ubics[$i]=="nodisponible"){
                        $g=$g."NO DISPONIBLE".(($i == $lengthUbics-1)?"":",");
                    }else if($ubics[$i]=="nocontacto"){
                        $g=$g."NO CONTACTO".(($i == $lengthUbics-1)?"":",");
                    }else if($ubics[$i]=="inublicable"){
                        $g=$g."INUBICABLE".(($i == $lengthUbics-1)?"":",");
                    }else{
                        $g=$g."SIN GESTIÓN".(($i == $lengthUbics-1)?"":",");
                    }
                }
                //$des=$des."".$ubics[$i]."".(($i == $lengthUbics-1)?"":",");
                $cont=1;
            }
            $des=$des.$g;
            $sql=$sql." ) ";
        }

        if($entidades!= "null" && $cont>0){
            $sql=$sql." and (";
            $des=$des."; Entidades: ";
            $p="";
            for($i=0;$i<$lengthEntidades;$i++){ 
                $sql = $sql." entidad = '".$entidades[$i]."'".(($i == $lengthEntidades-1)?"":" OR");
                if($lengthEntidades==4){
                    $p = "TODOS";
                }else{
                    $p=$p."".$entidades[$i]."".(($i == $lengthEntidades-1)?"":",");
                }
                //$des=$des."".$entidades[$i]."".(($i == $lengthEntidades-1)?"":",");
                $cont=1;
            }
            $des=$des.$p;
            $sql=$sql." ) ";
        }else if ($entidades!= "null" && $cont==0){
            $sql=$sql." ( ";
            $des=$des."Entidades: ";
            $p="";
            for($i=0;$i<$lengthEntidades;$i++){ 
                $sql = $sql." entidad = '".$entidades[$i]."'".(($i == $lengthEntidades-1)?"":" OR");
                if($lengthEntidades==4){
                    $p = "TODOS";
                }else{
                    $p=$p."".$entidades[$i]."".(($i == $lengthEntidades-1)?"":",");
                }
                //$des=$des."".$entidades[$i]."".(($i == $lengthEntidades-1)?"":",");
                $cont=1;
            }
            $des=$des.$p;
            $sql=$sql." ) ";
        }

        if($clientes!= "null" && $cont>0){
            $sql=$sql." and (";
            $des=$des."; Tipo Cliente: ";
            $h="";
            for($i=0;$i<$lengthClientes;$i++){ 
                $sql = $sql." nuevo LIKE '%".$clientes[$i]."%'".(($i == $lengthClientes-1)?"":" OR");
                if($lengthClientes==2){
                    $h = "TODOS";
                }else{
                    if($clientes[$i]=="NUEVO"){
                        $h=$h."NUEVOS".(($i == $lengthClientes-1)?"":",");
                    }else{
                        $h=$h."ANTIGUOS".(($i == $lengthClientes-1)?"":",");
                    }
                }
                //$des=$des."".$clientes[$i]."".(($i == $lengthClientes-1)?"":",");
                $cont=1;
            }
            $des=$des.$h;
            $sql=$sql." ) ";
        }else if ($clientes!= "null" && $cont==0){
            $sql=$sql." ( ";
            $des=$des."Tipo Cliente: ";
            $h="";
            for($i=0;$i<$lengthClientes;$i++){ 
                $sql = $sql." nuevo LIKE '%".$clientes[$i]."%'".(($i == $lengthClientes-1)?"":" OR");
                if($lengthClientes==2){
                    $h = "TODOS";
                }else{
                    if($clientes[$i]=="NUEVO"){
                        $h=$h."NUEVOS".(($i == $lengthClientes-1)?"":",");
                    }else{
                        $h=$h."ANTIGUOS".(($i == $lengthClientes-1)?"":",");
                    }
                }
                //$des=$des."".$clientes[$i]."".(($i == $lengthClientes-1)?"":",");
                $cont=1;
            }
            $des=$des.$h;
            $sql=$sql." ) ";
        }

        $sqlUsu="";
        if($usuarios!= [""]){
            $sql2=$sql." and (";
            //$des=$des."; Usuarios: ";
            for($i=0;$i<$lengthUsuarios;$i++){ 
                $sql2 = $sql2." emp_cod = '".$usuarios[$i]."' ".(($i == $lengthUsuarios-1)?"":" OR");
                //$des=$des."'".$usuarios[$i]."'".(($i == $lengthUsuarios-1)?"":",");
                $cont=1;
            }
            $sql2=$sql2." ) ";
        }else{
            $sql2=$sql;
        }

        //dd($des);

        $sqlUsu=$sqlUsu."
        SELECT 	count(cuenta) as cantidad,emp_cod as usuario, emp_nom as usuario_nom
        FROM
        (".$sql2.") Z
        group by emp_cod
        ";
        $queryUsu=DB::select(DB::raw($sqlUsu));
        //dd($queryUsu);
        $arrayUsu = array();
        foreach($queryUsu as $qu){
            $arrayUsu[] = $qu->usuario." (".$qu->cantidad.")";
        }
        $cadenaUsu=implode(',',$arrayUsu);
        $cadenaUsuario="Usuarios: ".$cadenaUsu;
        
        $des=$des."; ".$cadenaUsuario;
        //dd($des);
       


        $querytabla=DB::select(DB::raw($sql2));
        $query=DB::select(DB::raw($sql2));
        //dd($querytabla);
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
        insert into indicadores.plan (id_cartera,nombre_cartera,nombre_plan,clientes,cant_clientes,speech,detalle,fecha_i,fecha_f,fecha_reg)
        values ($car,'$cartera','$plan','$cadena',$cantidad_cli,'$speech','$des','$fec_i','$fec_f','$fec_reg')
        ");
       
        return response()->json (['insertado' =>$insertado,'querytabla' =>$querytabla]); 
    }

}
