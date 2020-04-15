<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Gestor extends Model
{
    protected $connection = 'mysql'; 
    protected $table = 'gestor';
    protected $fillable=['cartera','modalidad'];

    public static function gestores2(){

        $gestor=DB::connection('mysql')
        ->table('gestor as g')
        ->join('creditoy_cobranzas.sub_empleado as s','g.firma','=','s.emp_firma')
        ->where('s.emp_est','=','0')
        ->select('s.emp_firma',DB::raw('CONCAT(s.emp_nom," - " ,s.emp_firma) AS emp'))
        ->get();
        return $gestor;
    }

    public static function gestores(){
        $gestor=DB::connection('mysql2')->table('sub_empleado')->select('emp_nom','emp_firma')->whereEmp_est('0')->get();
        return $gestor;
    }

}
