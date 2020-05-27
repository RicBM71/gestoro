<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInteresRecuToCompras extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->decimal('interes_recuperacion', 5, 2)->default(0)->after('interes');
            $table->decimal('importe_recuperacion', 10, 2)->default(0)->after('importe_renovacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->dropColumn('interes_recuperacion');
            $table->dropColumn('importe_recuperacion');
        });
    }
}
