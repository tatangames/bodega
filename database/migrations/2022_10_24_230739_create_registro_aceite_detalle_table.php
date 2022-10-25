<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistroAceiteDetalleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registro_aceite_detalle', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_salida_acei_deta')->unsigned();
            $table->bigInteger('id_equipo')->unsigned();
            $table->bigInteger('id_medida')->unsigned();

            $table->date('fecha');
            $table->time('hora')->nullable();

            $table->decimal('cantidad_salida', 10,2);

            $table->string('descripcion', 800)->nullable();

            $table->foreign('id_salida_acei_deta')->references('id')->on('salida_aceites_detalle');
            $table->foreign('id_equipo')->references('id')->on('equipos');
            $table->foreign('id_medida')->references('id')->on('unidad_medida_aceites');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registro_aceite_detalle');
    }
}
