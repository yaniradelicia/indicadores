<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class GestionController extends Controller
{

    public function index()
    {
        return view('gestion.rep_consolidado');
    }

    public function repConsolidado()
    {
        $sqlGestion="
            select
            id,cartera,
            capital,
            deuda,
            importe,
            clientes_cartera_total,
            format((cli_ges_cob_cart/clientes_cartera_total)*100,2) as cobertura_total,
            format((cli_ges_cob_cont/clientes_contacto_cob)*100,2) as cobertura_contacto,
            if((llam_int_cart/clientes_llam_cartera),format((llam_int_cart/clientes_llam_cartera),1),0) as intensidad_total,
            if((llam_int_cont/clientes_llam_contacto)>0,format((llam_int_cont/clientes_llam_contacto),1),0) as intensidad_contacto,
            if((cant_contactos/clientes_cartera_total)>0,format((cant_contactos/clientes_cartera_total)*100,2),0) as contactos,
            format((cant_no_contactos/clientes_cartera_total)*100,2) as no_contactos,
            format((cant_sin_gestion/clientes_cartera_total)*100,2) as sin_gestion
            from
            (select 
            car_id as id,
            cartera,
            sum(deuda_capital) as capital,
            sum(deuda_moroso_soles_total) as deuda,
            sum(importe_cancelacion_total) as importe,
            count(*) clientes_cartera_total,
            sum(cli_cont) clientes_contacto_cob,
            sum(cli_cob_cont) cli_ges_cob_cont,
            sum(cli_cob_cart) cli_ges_cob_cart,
            sum(cli_llam_cart) clientes_llam_cartera,
            sum(llam_cart) llam_int_cart,
            sum(cli_llam_cont) clientes_llam_contacto,
            sum(llam_cont) llam_int_cont,
            sum(contacto) as cant_contactos,
            sum(no_contacto) as cant_no_contactos,
            sum(sin_gestion) as cant_sin_gestion
            
            from
            (
            SELECT
                car_id,
                cartera,
                deuda_capital,
                deuda_moroso_soles_total,
                importe_cancelacion_total,
                IF(ubicabilidad_call in ('CONTACTO','NO DISPONIBLE'),1,0) AS cli_cont,
                if(ubicabilidad_call in ('CONTACTO','NO DISPONIBLE') AND gestion_periodo LIKE '%SI%',1,0) AS cli_cob_cont,
                IF(gestion_periodo LIKE '%SI%',1,0) cli_cob_cart,
                IF(llamadas_en_el_mes>=1,1,0) AS cli_llam_cart,
                IF(llamadas_en_el_mes>=1,llamadas_en_el_mes,0) AS llam_cart,
                IF(llamadas_en_el_mes>=1 and ubicabilidad_call in ('CONTACTO','NO DISPONIBLE'),1,0) AS cli_llam_cont,
                IF(llamadas_en_el_mes>=1 and ubicabilidad_call in ('CONTACTO','NO DISPONIBLE'),llamadas_en_el_mes,0) AS llam_cont,
                if(ubicabilidad_call in ('CONTACTO','NO DISPONIBLE'),1,0) as contacto,
                if(ubicabilidad_call in ('NO CONTACTO'),1,0) as no_contacto,
                if(ubicabilidad_call not in ('CONTACTO','NO CONTACTO','NO DISPONIBLE'),1,0) as sin_gestion
            FROM 
                temp_deuda_detallada_total as t
            inner join cartera as c on t.cartera=c.car_nom
            where car_id not in (73,92,93)
            ) a
            group by cartera
            ORDER BY cartera ASC
            ) t
        ";
        $gestiones=DB::connection('mysql2')->select(DB::raw($sqlGestion));

        $sqlPdp="
            SELECT 
            car_id_FK as id,
            car_nom as cartera,
            (cumplido+vigente+caidos) as total_monto,
            cumplido,
            vigente,
            caidos
            from
            (
            SELECT
            car_id_FK,car_nom,
            sum(if(com_cli_est<>0,com_cli_can,0)) as cumplido,
            sum(if( com_cli_est=0 and DATEDIFF(DATE_FORMAT(now(),'%Y-%m-%d'),DATE_FORMAT(com_cli_fec_pag,'%Y-%m-%d')) <= 0,com_cli_can,0)) as vigente,
            sum(if( com_cli_est=0 and com_cli_pas=0 and DATEDIFF(DATE_FORMAT(now(),'%Y-%m-%d'),DATE_FORMAT(com_cli_fec_pag,'%Y-%m-%d')) > 0,com_cli_can,0)) as caidos
            FROM compromiso_cliente as com
            INNER JOIN cliente as cli
            on com.cli_id_FK=cli.cli_id
            INNER JOIN cartera as cart
            on cli.car_id_FK=cart.car_id
            WHERE cli_est=0 and cli_pas=0
            AND date_format(com_cli_fec_pag,'%Y%m')=date_format(NOW(),'%Y%m')
            AND car_id not in (73,92,93)
            GROUP BY car_id_FK
            ) as t
            ORDER BY car_nom ASC
        ";
        $pdps=DB::connection('mysql2')->select(DB::raw($sqlPdp));
        return response()->json(['gestiones' => $gestiones, 'pdps' => $pdps]);
    }

    public function repConsolidadoFecha()
    {
        //fecha_registro=DATE_FORMAT(date_add(NOW(), INTERVAL -1 DAY),'%Y-%m-%d')
        $sql="
            select *
            from indicadores.reporte_gestion
            where fecha_registro = (
                SELECT MAX(fecha_registro)
                FROM indicadores.reporte_gestion
            )
        ";
        $query=DB::connection('mysql2')->select(DB::raw($sql));

        return $query;
    }

}
