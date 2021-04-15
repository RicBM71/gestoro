<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddValidadoToAlbaranes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('albaranes', function (Blueprint $table) {
            $table->boolean('validado')->default(true)->after('pedido');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('albaranes', function (Blueprint $table) {
            $table->dropColumn('validado');
        });
    }
}
