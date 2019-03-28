<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InvitationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sender') -> unsigned();
            $table->integer('recipient') -> unsigned();
            $table->integer('team') -> unsigned();
            $table->string('senderEmail', 100);
            $table->string('recipientEmail', 100);
            $table->string('message', 100);
            $table->timestamps();
        });

        Schema::table('invitations', function (Blueprint $table) {
            $table->foreign('sender')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('recipient')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('invitations');
    }
}
