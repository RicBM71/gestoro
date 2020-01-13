<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraspasosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('traspasos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('empresa_id')->index(); // solicitante
            $table->unsignedInteger('proveedora_empresa_id')->index();
            $table->date('fecha');
            $table->smallInteger('situacion_id')->default(1);
            $table->decimal('importe_solicitado', 7, 2)->default(0);
            $table->decimal('importe_entregado', 7, 2)->default(0);
            $table->unsignedInteger('solicitante_user_id')->default(0);
            $table->unsignedInteger('autorizado_user_id')->default(0);
            $table->unsignedInteger('receptor_user_id')->default(0);
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
        Schema::dropIfExists('traspasos');
    }
}
