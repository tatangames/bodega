<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalidaAceitesTable extends Migration
{
    /**
     * SALIDA DE ACEITE Y LUBRICANTES A USUARIO 'PAPI' A SU BODEGA
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salida_aceites', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->string('descripcion', 800)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salida_aceites');
    }
}
