<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCobrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cobros', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('albaran_id')->index();
            $table->unsignedInteger('empresa_id');
            $table->unsignedInteger('cliente_id')->index();
            $table->date('fecha');
            $table->unsignedInteger('fpago_id')->index();
            $table->decimal('importe', 10, 2)->default(0);
            $table->String('notas')->nullable();
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
        Schema::dropIfExists('cobros');
    }
}
