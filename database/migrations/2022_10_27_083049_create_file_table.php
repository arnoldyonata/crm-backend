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
        Schema::create('file_category', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        Schema::create('file', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('filename');
            $table->string('filepath');
            $table->string('filetype');
            $table->integer('file_category_id');
            $table->foreign('file_category_id')
                ->references('id')
                ->on('file_category');
            $table->index('file_category_id');
            $table->timestamps();
        });

        Schema::create('file_relation_type', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        Schema::create('file_relation', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('relation_id');
            $table->bigInteger('file_id');
            $table->integer('file_relation_type_id');
            $table->foreign('file_id')
                ->references('id')
                ->on('file');
            $table->foreign('file_relation_type_id')
                ->references('id')
                ->on('file_relation_type');
            $table->index('relation_id');
            $table->index('file_id');
            $table->index('file_relation_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_relation');
        Schema::dropIfExists('file');
        Schema::dropIfExists('file_category');
        Schema::dropIfExists('file_relation_type');
    }
};
