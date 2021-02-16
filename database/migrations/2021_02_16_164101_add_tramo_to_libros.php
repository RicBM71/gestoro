<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTramoToLibros extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('libros', function (Blueprint $table) {
            $table->decimal('tramo', 5, 2)->default(0)->after('plantilla_excel');
            $table->decimal('interes_min', 5, 2)->default(0)->after('tramo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('libros', function (Blueprint $table) {
            $table->dropColumn('tramo');
            $table->dropColumn('interes_min');
        });
    }
}
