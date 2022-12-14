<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntradasDetalleTable extends Migration
{
    /**
     * detalle de entrada para repuestos
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entradas_detalle', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_material')->unsigned();
            $table->bigInteger('id_entrada')->unsigned();
            $table->bigInteger('id_equipo')->unsigned();
            $table->bigInteger('id_ubicacion')->unsigned();

            $table->integer('cantidad');

            $table->decimal('precio', 10, 2);

            $table->foreign('id_material')->references('id')->on('materiales');
            $table->foreign('id_entrada')->references('id')->on('entradas');
            $table->foreign('id_equipo')->references('id')->on('equipos');
            $table->foreign('id_ubicacion')->references('id')->on('ubicacion_repuesto');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entradas_detalle');
    }
}
