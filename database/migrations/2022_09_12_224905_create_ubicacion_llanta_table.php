<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUbicacionLlantaTable extends Migration
{
    /**
     * ubicacion para llantas
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ubicacion_llanta', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ubicacion_llanta');
    }
}
