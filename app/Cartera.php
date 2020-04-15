<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Cartera extends Model
{
    public static function cartera(){
        $cartera=DB::connection('mysql')->table('cartera')->distinct()->select('cartera')->get();
        return $cartera;
    }

    public static function carteraDetalle(){
        $carterad=DB::connection('mysql')->table('cartera_detalle')->distinct()->select('cartera','car_id_fk')->get();
        return $carterad;
    }

    public static function clienteGestion(){
        /*select ges_cli_id, ges_cli_fec, car_id_fk,car_nom, cli_id_fk, cli_cod
        from cliente c
        inner join gestion_cliente g
        on c.cli_id=g.cli_id_fk
        inner join cartera car
        on c.car_id_FK=car.car_id
        where cli_cod=76937 and car_id_FK=34*/
        //creditoy_cobranzas.

        $data=DB::connection('mysql')->table('cartera_detalle');

        $cliente_gestion=DB::connection('mysql2')
        ->table('cliente as c')
        ->join('gestion_cliente as g','c.cli_id','=','g.cli_id_fk')
        ->join('cartera as car','c.car_id_FK','=','car.car_id')
        //->join(DB::raw('('.$data->toSql().') AS cd '),'c.cli_cod','=','cd.cuenta')
        ->join('indicadores.cartera_detalle as cd ','c.cli_cod','=','cd.cuenta')
        //->mergeBindings($data)
        ->select('g.ges_cli_id','g.ges_cli_fec','c.car_id_fk','car.car_nom','g.cli_id_fk','c.cli_cod',
                DB::raw('(CASE 
                        WHEN cd.cartera LIKE ("%FALABELLA%") THEN  REPLACE (cd.cartera,"BANCO FALABELLA")
                        ELSE cd.cartera
                    END) as cartera'),
                'cd.cuenta','cd.dep','cd.saldo_deuda','cd.capital','cd.monto_camp','cd.prioridad',
                'cd.entidades','cd.tramo','cd.score','cd.dep_ind')
        ->where('cartera','=','car.car_nom')
        ->where('cd.cuenta','=','c.cli_cod')
        ->get();

        /*$cliente_gestion=DB::connection('mysql')
        ->table('cartera_detalle as cd')
        ->join('creditoy_cobranzas.cliente as c','c.cli_cod','=','cd.cuenta')
        ->join('creditoy_cobranzas.gestion_cliente as g','c.cli_id','=','g.cli_id_fk')
        ->join('creditoy_cobranzas.cartera as car','c.car_id_FK','=','car.car_id')
        ->select('g.ges_cli_id','g.ges_cli_fec','c.car_id_fk','car.car_nom','g.cli_id_fk','c.cli_cod','cd.cuenta',
                'cd.dep','cd.saldo_deuda','cd.capital','cd.monto_camp','cd.prioridad','cd.entidades','cd.tramo',
                'cd.score','cd.dep_ind')
        ->get();*/

        return $cliente_gestion;
    }
}
