<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('records', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('player_login');
            $table->string('map_uid');
            $table->integer('score', false, true);
            $table->timestamps();

            $table->foreign('player_login')->references('login')->on('players');
            $table->foreign('map_uid')->references('uid')->on('maps');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('records');
    }
}
