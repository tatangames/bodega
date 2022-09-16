<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFirmasLlantasTable extends Migration
{
    /**
     * bloque de nombre para 3 firmas en reportes de llantas
     *
     * @return void
     */
    public function up()
    {
        Schema::create('firmas_llantas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_1', 100);
            $table->string('nombre_2', 100);

            $table->string('nombre_3', 100);
            $table->string('nombre_4', 100);

            $table->string('nombre_5', 100);
            $table->string('nombre_6', 100);

            $table->integer('distancia');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('firmas_llantas');
    }
}
