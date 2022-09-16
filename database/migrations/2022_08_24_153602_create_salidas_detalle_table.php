<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalidasDetalleTable extends Migration
{
    /**
     * detalle para salida de repuestos
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salidas_detalle', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_material')->unsigned();
            $table->bigInteger('id_entrada_detalle')->unsigned();
            $table->bigInteger('id_salida')->unsigned();
            $table->bigInteger('id_equipo')->unsigned();

            $table->integer('cantidad');

            $table->foreign('id_material')->references('id')->on('materiales');
            $table->foreign('id_entrada_detalle')->references('id')->on('entradas_detalle');
            $table->foreign('id_salida')->references('id')->on('salidas');
            $table->foreign('id_equipo')->references('id')->on('equipos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salidas_detalle');
    }
}
