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
        Schema::create('usage_tracking', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('subscription_id');
            $table->string('feature_name', 100);
            $table->date('usage_date');
            $table->integer('usage_count')->default(1);
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('subscription_id')->references('id')->on('subscriptions');
            $table->index(['client_id', 'usage_date', 'feature_name'], 'idx_usage_date_feature');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usage_tracking');
    }
};
