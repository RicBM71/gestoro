<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPedidoToAlbaranes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('albaranes', function (Blueprint $table) {
            $table->string('pedido', 100)->nullable()->after('notas_int');
            $table->index(['empresa_id','pedido']);
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
            $table->dropColumn('pedido');
        });
    }
}
