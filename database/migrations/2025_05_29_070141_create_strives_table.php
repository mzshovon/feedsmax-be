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
        Schema::create('strives', function (Blueprint $table) {
            $table->id();
            $table->string('reference_id', 100);
            $table->string('device_id', 300)->nullable();
            $table->foreignId('bucket_id')->constrained("buckets");
            $table->foreignId('client_id')->constrained("clients");
            $table->foreignId('channel_id')->constrained("channels");
            $table->foreignId('event_id')->constrained("events");
            $table->string('event', 250);
            $table->string('channel', 100);
            $table->string('client', 100);
            $table->string('platform', 50)->nullable();
            $table->string('tier', 100)->nullable();
            $table->string('language', 10);
            $table->json('quarantine_rule_set')->nullable();
            $table->string('device_name', 120)->nullable();
            $table->string('device_model', 200)->nullable();
            $table->string('user_app_version', 20)->nullable();
            $table->string('user_os_version', 20)->nullable();
            $table->string('network', 10)->nullable();
            $table->json('action_details')->nullable();
            $table->boolean('view');
            $table->tinyInteger('score')->nullable();
            $table->string('arpu', 100)->nullable();
            $table->enum('customer_behavior', array('visitor', 'paid'))->nullable();
            $table->string('ip')->nullable();
            $table->json('geo_location')->nullable();
            $table->timestamp('tried_at')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->json('extra')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('strives');
    }
};
