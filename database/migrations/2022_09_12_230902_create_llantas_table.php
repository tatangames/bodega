<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLlantasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('llantas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_medida')->unsigned();
            $table->bigInteger('id_marca')->unsigned();

            $table->foreign('id_medida')->references('id')->on('medida_rin');
            $table->foreign('id_marca')->references('id')->on('marca_llanta');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('llantas');
    }
}
