<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username',30)->unique();
            $table->string('name');
            $table->string('lastname')->nullable();
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string('avatar')->nullable();
            $table->string('huella',3)->nullable();
            $table->timestamp('blocked_at')->nullable();
            $table->boolean('blocked')->default(false);
            //$table->unsignedInteger('empresa_id')->default(1);
            $table->timestamp('login_at')->nullable();
            // $table->smallInteger('expira')->default(0);
            // $table->date('fecha_expira')->nullable();
            $table->string('username_umod',30)->nullable();
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
        Schema::dropIfExists('users');
    }
}
