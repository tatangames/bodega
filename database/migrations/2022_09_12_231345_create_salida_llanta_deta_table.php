<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalidaLlantaDetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salida_llanta_deta', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_llanta')->unsigned();
            $table->bigInteger('id_l_entrada_detalle')->unsigned();
            $table->bigInteger('id_salida_llanta')->unsigned();
            $table->bigInteger('id_equipo')->unsigned();

            $table->integer('cantidad');

            $table->foreign('id_llanta')->references('id')->on('llantas');
            $table->foreign('id_l_entrada_detalle')->references('id')->on('entrada_llanta_deta');
            $table->foreign('id_salida_llanta')->references('id')->on('salida_llanta');
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
        Schema::dropIfExists('salida_llanta_deta');
    }
}
