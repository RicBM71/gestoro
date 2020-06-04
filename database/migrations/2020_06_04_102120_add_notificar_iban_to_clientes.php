<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNotificarIbanToClientes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->boolean('notificar_iban')->default(true)->after('interes_recuperacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn('notificar_iban');
        });
    }
}
