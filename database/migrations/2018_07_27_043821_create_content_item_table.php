<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_item', function (Blueprint $table) {
            $table->increments('id');
            $table->json('content')->comment('项内容');
            $table->integer('file_id', false, true)->comment('文件ID');

            $table->foreign('file_id')->references('id')->on('file');
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
        Schema::dropIfExists('content_item');
    }
}
