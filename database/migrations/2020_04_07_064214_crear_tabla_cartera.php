<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CrearTablaCartera extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cartera', function (Blueprint $table) {
            $table->bigIncrements('id');
            //$table->integer('car_id_fk');
            $table->string('cartera',100);
            $table->string('mes',10);
            $table->double('meta', 9, 2);
            $table->double('recupero', 9, 2);
            $table->integer('efectividad');
            $table->timestamps();
        });
    }
//CARTERA	MES	META	RECUPERO	EFECTIVIDAD

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cartera');
    }
}
