<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInteresRecuToClientes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->decimal('interes', 5, 2)->default(0)->after('fpago_id');
            $table->decimal('interes_recuperacion', 5, 2)->default(0)->after('interes');
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
            $table->dropColumn('interes');
            $table->dropColumn('interes_recuperacion');
        });
    }
}
