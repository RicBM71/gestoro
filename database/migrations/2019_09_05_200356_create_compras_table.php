<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComprasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('empresa_id');
            $table->smallInteger('grupo_id');
            $table->Integer('ejercicio')->default(0);
            $table->Integer('albaran')->default(0);
            $table->string('serie_com', 1);
            $table->unsignedInteger('cliente_id');
            $table->smallInteger('tipo_id');
            $table->date('fecha_compra')->nullable();
            $table->date('fecha_bloqueo')->nullable();
            $table->date('fecha_renovacion')->nullable();
            $table->date('fecha_recogida')->nullable();
            $table->smallInteger('dias_custodia')->default(0);
            $table->decimal('importe', 10, 2)->default(0);
            $table->decimal('importe_renovacion', 10, 2)->default(0);
            $table->decimal('importe_acuenta', 10, 2)->default(0);
            $table->decimal('interes', 5, 2)->default(0);
            $table->unsignedInteger('fase_id');
            $table->Integer('factura')->nullable();
            $table->date('fecha_factura')->nullable();
            $table->string('serie_fac', 5)->nullable();
            $table->Integer('papeleta')->nullable();
            $table->decimal('retencion', 6,2)->default(0);
            $table->String('notas')->nullable();
            $table->string('username',30)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['empresa_id', 'cliente_id']);
            $table->unique(['empresa_id', 'ejercicio', 'serie_com', 'albaran']);


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('compras');
    }
}
