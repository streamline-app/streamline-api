<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 100);
            $table->text('body');
            $table->integer('estimatedDurationHour');
            $table->integer('estimatedDurationMin');
            $table->integer('actualDurationHour');
            $table->integer('actualDurationMin');
            $table->timestamp('lastWorked');
            $table->unsignedInteger('creator_id');
            $table->boolean('active');
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
        Schema::dropIfExists('task');
    }
}
