<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContadorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contadores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('empresa_id');
            $table->unsignedInteger('tipo_id');
            $table->Integer('ejercicio')->default(0);
            $table->Integer('ult_albaran')->default(0);
            $table->Integer('ult_factura')->default(0);
            $table->Integer('ult_factura_auto')->default(0);
            $table->Integer('ult_factura_abono')->default(0);
            $table->string('serie_albaran',3);
            $table->string('serie_factura',3)->nullable();
            $table->string('serie_factura_auto',3)->nullable();
            $table->string('serie_factura_abono',3)->nullable();
            $table->boolean('cerrado')->default(false);
            $table->string('username',30)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contadores');
    }
}
