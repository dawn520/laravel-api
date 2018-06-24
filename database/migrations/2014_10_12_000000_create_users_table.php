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
            $table->string('username')->nullable()->comments('用户名');
            $table->string('phone')->nullable()->comments('手机号');;
            $table->string('email')->nullable()->comments('邮箱');;
            $table->string('password')->nullable()->comments('密码');
            $table->string('wx_openid');
            $table->string('session_key');

            $table->string('name')->nullable()->comments('姓名');

            $table->rememberToken();
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
