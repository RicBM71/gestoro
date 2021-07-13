<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParametrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parametros', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('lim_efe', 10, 2)->default(1000);
            $table->decimal('lim_efe_nores', 10, 2)->default(10000);
            $table->string('pie_rebu1')->nullable();
            $table->decimal('retencion', 6,2)->default(0);
            $table->boolean('online')->default(false);
            $table->boolean('aislar_empresas')->default(false);
            $table->string('img1',100)->nullable();
            $table->string('img2',100)->nullable();
            $table->string('username',50)->nullable();
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
        Schema::dropIfExists('parametros');
    }
}
