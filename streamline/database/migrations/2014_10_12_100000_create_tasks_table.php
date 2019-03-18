<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            // Unique ID of Task
            $table->increments('id');
            // Unique ID of owner
            $table->integer('ownerId')->unsigned();
            // Title of Task
            $table->string('title', 100);
            // Body of Task
            $table->text('body')->nullable();
            // Current Total Time Worked on Task
            $table->bigInteger('workedDuration');
            // User inputed expected duration of task in minutes
            $table->integer('estimatedMin');
            // User inputed expected duration of task in hours
            $table->integer('estimatedHour');
            // Expected Total Time the Task Should Take
            $table->bigInteger('expDuration');
            // Updated_At at and Created_At
            $table->timestamps();
            // Task Last Turned Active Time
            $table->timestamp('lastWorkedAt')->nullable();
            // Whether a Task is Active (Worked On) or Inactive
            $table->boolean('isFinished')->default(false);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->foreign('ownerId')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
