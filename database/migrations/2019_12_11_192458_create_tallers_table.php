<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTallersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('talleres', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('empresa_id');
            $table->string('razon', 70);
            $table->string('nombre', 30)->nullable();
            $table->string('apellidos', 45)->nullable();
            $table->string('direccion', 50)->nullable();
            $table->string('cpostal', 10)->nullable();
            $table->string('poblacion', 40)->nullable();
            $table->string('provincia', 30)->nullable();
            $table->string('telefono1', 15)->nullable();
            $table->string('telefono2', 15)->nullable();
            $table->string('tfmovil', 15)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('tipodoc', 1)->default('O');
            $table->string('dni', 30)->nullable();
            $table->String('notas')->nullable();
            $table->String('bloqueado','1')->default('N');
            $table->boolean('facturar')->default(true);
            $table->unsignedInteger('fpago_id')->default(1);
            $table->String('iban',24)->nullable()->default(null);
            $table->String('bic',11)->nullable()->default(null);
            $table->string('username',30)->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('talleres');
    }
}
