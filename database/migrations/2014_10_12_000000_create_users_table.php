<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('social')->nullable();
            $table->integer('role_id')->default(2);
            $table->boolean('email_verify')->default(0);
            $table->boolean('banned')->default(0);
            $table->timestamp('actived_at')->nullable();
            $table->timestamp('last_login')->useCurrent();
            $table->string('reset_password_code')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->integer('created_user_id')->nullable();
            $table->integer('updated_user_id')->nullable();
            $table->rememberToken();
            $table->timestamp('delete_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
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
};
