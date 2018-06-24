<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->integer('uid')->index()->comment('用户id');
            $table->string('nickName', 100)->nullable()->comment('昵称');
            $table->string('country', 20)->nullable()->comment('国家');
            $table->string('province', 20)->nullable()->comment('省');
            $table->string('city', 20)->nullable()->comment('市');
            $table->string('language', 20)->nullable()->comment('语言');
            $table->string('avatarUrl', 250)->nullable()->comment('头像');
            $table->tinyInteger('gender')->nullable()->comment('性别');


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
        Schema::dropIfExists('profile');
    }
}
