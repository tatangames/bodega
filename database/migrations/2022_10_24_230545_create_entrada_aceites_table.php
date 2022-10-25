<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntradaAceitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entrada_aceites', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_empresa')->unsigned();

            $table->date('fecha');
            $table->string('descripcion', 800)->nullable();

            $table->foreign('id_empresa')->references('id')->on('empresa_licitacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entrada_aceites');
    }
}
