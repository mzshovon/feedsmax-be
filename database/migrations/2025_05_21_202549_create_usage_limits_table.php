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
        Schema::create('usage_limits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('package_id');
            $table->enum('limit_type', ['daily', 'monthly', 'yearly']);
            $table->integer('limit_value');
            $table->string('feature_name', 100);
            $table->foreign('package_id')->references('id')->on('packages');
            $table->unique(['package_id', 'limit_type', 'feature_name']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usage_limits');
    }
};
