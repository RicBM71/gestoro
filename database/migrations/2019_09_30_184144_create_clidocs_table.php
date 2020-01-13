<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClidocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clidocs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('cliente_id')->index();
            $table->string('file1')->nullable();
            $table->string('file2')->nullable();
            $table->string('username',30)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clidocs');
    }
}
