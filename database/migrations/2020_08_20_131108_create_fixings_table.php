<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFixingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fixings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('clase_id');
            $table->date('fecha');
            $table->decimal('importe', 10, 2)->default(0);
            $table->string('username', 30)->nullable();
            $table->timestamps();
            
            $table->unique(['clase_id', 'fecha']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fixings');
    }
}
