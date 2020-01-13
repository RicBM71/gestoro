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
            $table->decimal('lim_efe', 10, 2)->default(2500);
            $table->decimal('lim_efe_nores', 10, 2)->default(15000);
            $table->string('pie_rebu')->nullable();
            $table->decimal('retencion', 6,2)->default(0);
            $table->boolean('online')->default(false);
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
