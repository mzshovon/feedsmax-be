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
        Schema::create('quarantine_policies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40);
            $table->string('call_object_notation', 250);
            $table->unsignedInteger('order')->nullable();
            $table->json('args')->nullable();
            $table->json('update_params')->nullable();
            $table->longText('definition')->nullable();
            $table->foreignId('event_id')->constrained("events");
            $table->boolean('status');
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quarantine_policies');
    }
};
