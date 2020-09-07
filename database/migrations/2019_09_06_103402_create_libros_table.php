<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLibrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('libros', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 40);
            $table->unsignedInteger('empresa_id');
            $table->unsignedInteger('grupo_id');
            $table->Integer('ejercicio')->default(0);
            $table->Integer('ult_compra')->default(0);
            $table->Integer('ult_factura')->default(0);
            $table->string('serie_com', 1)->nullable();
            $table->string('serie_fac', 5)->nullable();
            $table->smallInteger('dias_custodia')->default(30);
            $table->smallInteger('dias_cortesia')->default(10);
            $table->string('semdia_bloqueo',3)->default("3/1");
            $table->decimal('interes', 5, 2)->default(0);
            $table->string('codigo_pol',20)->nullable()->default(null);
            $table->string('nombre_csv',30)->nullable()->default(null);
            $table->boolean('cerrado')->default(false);
            $table->boolean('grabaciones')->default(false);
            $table->boolean('peso_frm')->default(false);
            $table->boolean('recompras')->default(true);
            $table->string('username',30)->nullable();
            $table->timestamps();

            $table->unique(['empresa_id','grupo_id', 'ejercicio']);
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('libros');
    }
}
