<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHcomlinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hcomlines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('operacion',1)->nullable();
            $table->string('username_his',30)->nullable();
            $table->timestamp('created_his');
            $table->unsignedInteger('comline_id')->index();
            $table->unsignedInteger('compra_id')->index();
            $table->unsignedInteger('empresa_id');
            $table->text('concepto')->nullable();
            $table->text('grabaciones')->nullable();
            $table->string('colores',50)->nullable();
            $table->unsignedInteger('clase_id');
            $table->decimal('peso_gr', 10,2)->default(0);
            $table->decimal('importe', 10, 2)->default(0);
            $table->decimal('importe_gr', 10, 2)->default(0);
            $table->decimal('importe_venta', 10, 2)->default(0);
            $table->decimal('iva', 6, 2)->default(0);
            $table->decimal('retencion', 6,2)->default(0);
            $table->smallInteger('quilates')->nullable();
            $table->date('fecha_liquidado')->nullable();
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
        Schema::dropIfExists('hcomlines');
    }
}
