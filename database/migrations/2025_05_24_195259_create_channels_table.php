<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('channels', function (Blueprint $table) {
            $table->id();
            $table->string('tag', 50)->comment('Used in trigger api URI');
            $table->string('name', 50)->nullable();
            $table->unsignedBigInteger('channel_id');
            $table->unsignedBigInteger('theme_id');
            $table->json('retry')->nullable();
            $table->tinyInteger('status');
            $table->tinyInteger('pagination')->default(10)->comment("pagination of questions");
            $table->json('theme')->nullable();
            $table->softDeletes();
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();
            $table->foreign('channel_id')->references('id')->on('channels');
            $table->foreign('theme_id')->references('id')->on('themes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channels');
    }
};
