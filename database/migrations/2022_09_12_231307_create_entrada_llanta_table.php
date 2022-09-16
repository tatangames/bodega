<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntradaLlantaTable extends Migration
{
    /**
     * entrada de llantas
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entrada_llanta', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_proveedor')->unsigned();
            $table->date('fecha');
            $table->string('descripcion', 800)->nullable();

            $table->string('documento', 100)->nullable();

            // 0: el repuesto es nuevo
            // 1: el repuesto ya estaba en bodega
            $table->boolean('inventario');

            $table->string('factura', 50)->nullable();
            $table->foreign('id_proveedor')->references('id')->on('proveedor');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entrada_llanta');
    }
}
