<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cartera;
use Carbon\Carbon;
use DB;

class EtiquetaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cartera=Cartera::carteraDetalle();
        return view('etiqueta.crear_etiqueta',compact('cartera'));
    }

    public function buscar()
    {
        $cartera=Cartera::carteraDetalle();
        return view('etiqueta.buscar_etiqueta',compact('cartera'));
    }

    public function filtro(Request $request)
    {
        $car = $request->query('car');
        $etiqueta = $request->query('etiqueta');
        $sql = "
            select 
                nombre_etiqueta, id_cartera, id_etiqueta,clientes,cant_clientes
            FROM indicadores.etiqueta
            where id_cartera=$car and nombre_etiqueta like '%$etiqueta%'
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
                clientes,cant_clientes
            FROM indicadores.etiqueta
            where id_cartera=$car and id_etiqueta=$id
            ";
        $query=DB::select(DB::raw($sql));
        foreach($query as $q){
            $cadena=$q->clientes;
            $cantidad=$q->cant_clientes;
        }

        $sqlCobertura="
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
                and date_format(ges_cli_fec,'%Y-%m')=date_format(now(),'%Y-%m')
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
                and year(ges_cli_fec) = year(now())
                and month(ges_cli_fec) = month(now())
                and MONTH(fecha) = month(now())
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
                      INNER JOIN(select max(ges_cli_id) as maxid,cli_id_FK from creditoy_cobranzas.gestion_cliente where date_format( ges_cli_fec, '%Y-%m' ) = date_format( now(), '%Y-%m' ) GROUP BY cli_id_FK) as g
                      on g.cli_id_FK=c.cli_id
                      inner join creditoy_cobranzas.gestion_cliente as gg on gg.ges_cli_id=g.maxid
                      left JOIN creditoy_cobranzas.empleado as e on c.emp_tel_id_FK=e.emp_id
                      left JOIN creditoy_cobranzas.call_telefonica as cal on e.cal_id_FK=cal.cal_id
                      WHERE c.car_id_FK =$car
                      AND cli_est =0 and cli_pas=0
                      AND date_format( ges_cli_fec, '%Y-%m' ) = date_format( now(), '%Y-%m' )
                      AND date_format( fecha, '%Y-%m' ) = date_format( now(), '%Y-%m' )
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
    public function insertar(Request $request)
    {
        $car = $request->query('car');
        $etiqueta = $request->query('etiqueta');
        $clientes = $request->query('clientes');
        $array_clientes = explode(',',$request->query('clientes'));
        $lengthClientes = count($array_clientes); 

        $sql = "
            select car_nom from cartera where car_id=$car
        ";
        $query=DB::select(DB::raw($sql));
        $array = array();
        foreach($query as $q){
            $car_nom=$q->car_nom;
        }

        $cartera=$car_nom;
        $fec_reg=Carbon::now();

        if($lengthClientes>0){
            $insertado = DB::insert("
            insert into indicadores.etiqueta (id_cartera,nombre_cartera,nombre_etiqueta,clientes,cant_clientes,fec_reg)
            values ($car,'$cartera','$etiqueta','$clientes',$lengthClientes,'$fec_reg')
            ");
        }
       
        return response()->json ($insertado); 
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
