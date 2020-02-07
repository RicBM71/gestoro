<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecuentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recuentos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('empresa_id');
            $table->date('fecha')->required();
            $table->unsignedInteger('producto_id')->nullable();
            $table->unsignedInteger('estado_id')->nullable();
            $table->unsignedInteger('rfid_producto_id')->nullable();
            $table->unsignedInteger('destino_empresa_id')->nullable();
            $table->unsignedInteger('rfid_id')->nullable();
            $table->string('username',30)->nullable();
            $table->timestamps();

            $table->unique(['empresa_id', 'producto_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recuentos');
    }
}
