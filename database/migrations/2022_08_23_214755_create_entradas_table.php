<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntradasTable extends Migration
{
    /**
     * entradas de repuesto
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entradas', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->string('descripcion', 800)->nullable();

            $table->string('documento', 100)->nullable();

            // 0: el repuesto es nuevo
            // 1: el repuesto ya estaba en bodega
            $table->boolean('inventario');

            $table->string('factura', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entradas');
    }
}
