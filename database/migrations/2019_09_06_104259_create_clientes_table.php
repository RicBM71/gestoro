<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('empresa_id')->default(1);
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
            $table->string('tipodoc', 1);
            $table->string('dni', 50);
            $table->date('fecha_nacimiento')->nullable();
            $table->date('fecha_baja')->nullable();
            $table->string('nacpro', 50)->nullable();
            $table->string('nacpais', 50)->nullable();
            $table->date('fecha_dni')->nullable();
            $table->String('notas')->nullable();
            $table->String('bloqueado','1')->default('N');
            $table->boolean('iva_no_residente')->default(false);
            $table->boolean('facturar')->default(true);
            $table->boolean('vip')->default(false);
            $table->boolean('listar_347')->default(false);
            $table->boolean('asociado')->default(false);
            $table->String('iban',24)->nullable()->default(null);
            $table->String('bic',11)->nullable()->default(null);
            $table->unsignedInteger('fpago_id')->default(1);
            $table->string('username',30)->nullable();
            $table->timestamps();

            $table->index(['nombre', 'apellidos']);
            $table->unique(['empresa_id', 'dni']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clientes');
    }
}
