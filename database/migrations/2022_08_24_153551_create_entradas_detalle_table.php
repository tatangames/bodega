<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntradasDetalleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entradas_detalle', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_material')->unsigned();
            $table->bigInteger('id_entrada')->unsigned();

            $table->integer('cantidad');

            $table->foreign('id_material')->references('id')->on('materiales');
            $table->foreign('id_entrada')->references('id')->on('entradas');
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
