<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntradaLlantaDetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entrada_llanta_deta', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_llanta')->unsigned();
            $table->bigInteger('id_entrada_llanta')->unsigned();
            $table->bigInteger('id_ubicacion')->unsigned();

            $table->integer('cantidad');

            $table->decimal('precio', 10, 2);

            $table->foreign('id_llanta')->references('id')->on('llantas');
            $table->foreign('id_entrada_llanta')->references('id')->on('entrada_llanta');
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
        Schema::dropIfExists('entrada_llanta_deta');
    }
}
