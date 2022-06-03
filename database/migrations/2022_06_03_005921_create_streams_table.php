<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('streams', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('user_login');
            $table->string('user_name');
            $table->integer('game_id')->nullable();
            $table->string('game_name');
            $table->string('type');
            $table->string('title');
            $table->integer('viewer_count');
            $table->dateTime('started_at');
            $table->char('language', 5);
            $table->string('thumbnail_url');
            $table->boolean('is_mature');
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
        Schema::dropIfExists('streams');
    }
};
