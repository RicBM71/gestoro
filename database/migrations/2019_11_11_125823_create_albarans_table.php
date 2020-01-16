<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlbaransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('albaranes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('empresa_id');
            $table->smallInteger('tipo_id');
            $table->Integer('albaran')->default(0);
            $table->string('serie_albaran',3);
            $table->unsignedInteger('fase_id');
            $table->unsignedInteger('cliente_id');
            $table->unsignedInteger('fpago_id')->nullable();
            $table->unsignedInteger('cuenta_id')->nullable();
            $table->date('fecha_albaran')->nullable();
            $table->date('fecha_factura')->nullable();
            $table->Integer('factura')->nullable();
            $table->string('serie_factura',3)->nullable();
            $table->smallInteger('tipo_factura')->default(0);
            $table->String('clitxt',50)->nullable();
            $table->date('fecha_notificacion')->nullable();
            $table->boolean('online')->default(false);
            $table->boolean('iva_no_residente')->default(false);
            $table->unsignedInteger('albaran_abonado_id')->nullable()->index();
            $table->unsignedInteger('motivo_id')->nullable();
            $table->string('factura_txt',30)->nullable();
            $table->unsignedInteger('procedencia_empresa_id')->nullable();
            $table->unsignedInteger('taller_id')->nullable();
            $table->boolean('facturar')->default(true);
            $table->boolean('express')->default(false);
            $table->string('username',30)->nullable();
            $table->text('notas_ext')->nullable();
            $table->text('notas_int')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['empresa_id','fecha_albaran', 'serie_albaran', 'albaran'], 'index_serie_alb');
            $table->index(['empresa_id','fecha_factura', 'serie_factura', 'factura'], 'index_serie_fac');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('albaranes');
    }
}
