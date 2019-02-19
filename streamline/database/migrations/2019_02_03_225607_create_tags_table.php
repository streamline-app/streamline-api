<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->text('description');
            $table->integer('tasks_completed');
            $table->double('average_time');
            $table->double('average_accuracy');
            $table->double('task_over_to_under');
            $table->string('color', 10);
            $table->integer('userID')->unsigned();
            $table->timestamps();
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->foreign('userID')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tags');
    }
}
