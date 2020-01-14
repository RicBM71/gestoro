<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCajasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cajas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('empresa_id');
            $table->date('fecha');
            $table->String('dh',1)->default('D');
            $table->String('nombre')->nullable();
            $table->decimal('importe', 10, 2)->default(0);
            $table->String('manual',1)->default('S');
            $table->unsignedInteger('deposito_id')->nullable()->index();
            $table->unsignedInteger('cobro_id')->nullable()->index();
            $table->unsignedInteger('apunte_id')->nullable()->index();
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
        Schema::dropIfExists('cajas');
    }
}
