<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedidaRinTable extends Migration
{
    /**
     * medidas de rin para llantas
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medida_rin', function (Blueprint $table) {
            $table->id();
            $table->string('medida', 100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medida_rin');
    }
}
