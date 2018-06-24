<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->integer('uid')->index()->comment('用户id');
            $table->char('phone', 11)->index()->comment('手机号');
            $table->string('code', 6)->comment('验证码');
            $table->tinyInteger('used')->comment('是否已经使用');
            $table->tinyInteger('type')->comment('类型：1：注册（填写资料）');
            $table->bigInteger('ip')->comment('ip地址');
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
        Schema::dropIfExists('sms');
    }
}
