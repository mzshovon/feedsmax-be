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
            $table->json('retry')->nullable();
            $table->tinyInteger('status');
            $table->integer('pagination')->default(10)->comment("pagination of questions");
            $table->json('theme')->nullable();
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->foreignId('client_id')->constrained('clients');
            $table->foreignId('theme_id')->constrained('themes');
            $table->timestamps();
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
