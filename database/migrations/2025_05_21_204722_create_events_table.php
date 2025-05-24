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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['nps', 'ces', 'csat'])->index()->comment("NPS (Net Promoter Score), CSAT (Customer Satisfaction), and CES (Customer Effort Score)");
            $table->string('name', 100);
            $table->unsignedBigInteger('channel_id');
            $table->unsignedBigInteger('bucket_id')->comment('Questions list under bucket');
            $table->enum('context', ['visitor', 'subscriber','rate'])->comment('Type feedback');
            $table->string('description', 200)->nullable();
            $table->enum('lang', ['en', 'bn'])->default("bn");
            $table->boolean('status')->default(false);
            $table->softDeletes();
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();
            $table->foreign('channel_id')->references('id')->on('channels');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
