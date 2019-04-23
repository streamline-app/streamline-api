<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TeamAssignmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teamassignments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user') -> unsigned();
            $table->integer('team') -> unsigned();
            $table->string('admin', 100);
            $table->timestamps();
        });

        Schema::table('teamassignments', function (Blueprint $table) {
            $table->foreign('user')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('team')->references('id')->on('teams')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teamassignments');
    }
}
