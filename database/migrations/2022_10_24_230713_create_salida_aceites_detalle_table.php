<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalidaAceitesDetalleTable extends Migration
{
    /**
     * DESCRIBIR COMO SE UTILIZO ESE ACEITE CUANDO SE ENTREGO AL USUARIO 'PAPI'
     *
     * @return void
     */
    public function up(){

        Schema::create('salida_aceites_detalle', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_salida_aceites')->unsigned();
            $table->bigInteger('id_material_aceite')->unsigned();
            $table->bigInteger('id_entrada_aceite_deta')->unsigned();

            $table->integer('cantidad');

            // 0: en uso
            // 1: finalizado

            $table->boolean('uso');
            // fecha finalizo uso
            $table->date('fecha_finalizo')->nullable();

            $table->string('descripcion', 300)->nullable();

            $table->foreign('id_salida_aceites')->references('id')->on('salida_aceites');
            $table->foreign('id_material_aceite')->references('id')->on('materiales_aceites');
            $table->foreign('id_entrada_aceite_deta')->references('id')->on('entrada_aceites_detalle');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salida_aceites_detalle');
    }
}
