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
            // TODO: Incorporate Actual User ID
            $table->unsignedInteger('ownerId');
            // Title of Task
            $table->string('title', 100);
            // Body of Task
            $table->text('body');
            // Current Total Time Worked on Task
            $table->bigInteger('workedDuration');
            // Expected Total Time the Task Should Take
            $table->bigInteger('expDuration');
            // Task Creation Time
            $table->timestamp('created_at');
            // Task Last Modified Time
            $table->timestamp('updated_at');
            // Task Last Turned Active Time
            $table->timestamp('lastWorkedAt')->nullable();
            // Whether a Task is Active (Worked On) or Inactive
            $table->boolean('active')->default(false);
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
