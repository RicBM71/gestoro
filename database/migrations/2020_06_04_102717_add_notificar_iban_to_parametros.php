<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNotificarIbanToParametros extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parametros', function (Blueprint $table) {
            $table->boolean('notificar_iban')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('parametros', function (Blueprint $table) {
            $table->dropColumn('notificar_iban');
        });
    }
}
