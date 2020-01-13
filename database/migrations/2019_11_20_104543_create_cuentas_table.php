<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCuentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuentas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('empresa_id');
            $table->String('nombre',40);
            $table->String('iban',24)->nullable();
            $table->String('bic',11)->nullable();
            $table->string('sufijo_adeudo',20)->nullable();
            $table->string('sufijo_transf',20)->nullable();
            $table->boolean('defecto')->default(false);
            $table->boolean('activa')->default(true);
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
        Schema::dropIfExists('cuentas');
    }
}
