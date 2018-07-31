<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id', false, true)->comment('用户ID');
            $table->string('folder_name', 255)->comment('文件名称');
            $table->text('content')->nullable(true)->comment('文件内容');
            $table->integer('header_id', false, true)->nullable(true)->comment('头文件ID');
            $table->tinyInteger('type', false,true)->comment('文件类型');


            $table->unique(['user_id', 'folder_name']); //文件名用户ID复合唯一索引

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('header_id')->references('id')->on('file');
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
        Schema::dropIfExists('file');
    }
}
