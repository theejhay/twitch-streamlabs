<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stream_tags', function (Blueprint $table) {
            $table->bigInteger('stream_id');
            $table->char('tag_id', 36);
            $table->index('stream_id');
            $table->unique(['stream_id', 'tag_id'], 'unique_records');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stream_tags');
    }
};
