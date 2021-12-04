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
            $table->string('player_id');
            $table->string('map_id');
            $table->boolean('auto_upload_replay');
            $table->boolean('banned');
            $table->integer('score', false, true);
            $table->timestamps();

            $table->foreign('player_id')->references('id')->on('players');
            $table->foreign('map_id')->references('id')->on('maps');
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
