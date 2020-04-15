<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CrearTablaGestor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gestor', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('cartera',100);
            $table->string('mes',10);
            $table->integer('dia');
            $table->integer('estado');
            $table->string('modalidad',10);
            $table->string('firma',10);
            $table->integer('usuario');
            $table->double('meta', 9, 2);
            $table->double('ranking', 9, 2);
            $table->integer('efectividad');
            $table->timestamps();
        });
    }
    //CARTERA	MES	DIA	ESTADO	MODALIDAD	FIRMA	USUARIO	META	RANKING	EFECTIVIDAD

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gestor');
    }
}
