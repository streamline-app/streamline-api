<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table){
            //Doc ID
            $table->increments('id');
            //path to document (with name generate by Laravel)
            $table->string('path', 200);
            //given name of file
            $table->string('name', 100);
            //Team ID
            $table->integer('team_id')->unsigned();
            //laravel timestamps
            $table->timestamps();
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
            Schema::dropIfExists('documents');
    }
}
