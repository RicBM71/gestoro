<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPlantillaToLibros extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('libros', function (Blueprint $table) {
            $table->string('plantilla_excel', 30)->nullable()->after('recompras');
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
            $table->dropColumn('plantilla_excel');
        });
    }
}
