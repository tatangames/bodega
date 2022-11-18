<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntradaAceitesDetalleTable extends Migration
{
    /**
     * ENTRADA DE ACEITE Y LUBRICANTES - DETALLE
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entrada_aceites_detalle', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_entrada_aceite')->unsigned();
            $table->bigInteger('id_material_aceite')->unsigned();
            $table->bigInteger('id_ubicacion')->unsigned();

            $table->integer('cantidad');

            $table->decimal('precio', 10, 2);

            $table->foreign('id_entrada_aceite')->references('id')->on('entrada_aceites');
            $table->foreign('id_material_aceite')->references('id')->on('materiales_aceites');
            $table->foreign('id_ubicacion')->references('id')->on('ubicacion_llanta');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entrada_aceites_detalle');
    }
}
