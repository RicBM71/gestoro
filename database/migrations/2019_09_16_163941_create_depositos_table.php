<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepositosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('depositos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('compra_id')->index();
            $table->unsignedInteger('empresa_id');
            $table->unsignedInteger('cliente_id')->index();
            $table->date('fecha');
            $table->unsignedInteger('concepto_id')->index();
            $table->decimal('importe', 10, 2)->default(0);
            $table->smallInteger('dias')->default(0);
            $table->String('iban',24)->nullable()->default(null);
            $table->String('bic',11)->nullable()->default(null);
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
        Schema::dropIfExists('depositos');
    }
}
