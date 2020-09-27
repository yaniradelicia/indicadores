<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Cartera extends Model
{
    public static function cartera2(){
        $cartera=DB::connection('mysql2')->table('cartera')->select('car_id','car_nom')
        ->where('car_est','=','0')
        ->where('car_pas','=','0')
        ->whereNotIn('car_id', array(23,57,86,74,73,92,93))
        ->get();
        return $cartera;
    }

    public static function cartera(){
        $cartera=DB::connection('mysql')->table('cartera')->distinct()->select('cartera')->get();
        return $cartera;
    }

    public static function carteraDetalle(){
        $carterad=DB::connection('mysql')->table('cartera_detalle')->distinct()->select('cartera','car_id_fk')->get();
        return $carterad;
    }

}
