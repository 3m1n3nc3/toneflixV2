<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlaylistSpotifyLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('playlist_spotify_logs', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('playlist_id')->index('playlist_id');
            $table->string('spotify_id', 30)->nullable();
            $table->string('artwork_url')->nullable();
            $table->boolean('fetched')->default(0)->index('fetched');
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
        Schema::dropIfExists('playlist_spotify_logs');
    }
}
