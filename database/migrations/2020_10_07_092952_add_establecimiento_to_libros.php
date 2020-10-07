<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEstablecimientoToLibros extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('libros', function (Blueprint $table) {
            $table->string('establecimiento', 50)->nullable()->after('plantilla_excel');
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
            $table->dropColumn('establecimiento');
        });
    }
}
