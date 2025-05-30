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
        Schema::create('client_portals', function (Blueprint $table) {
            $table->id();
            $table->text('subdomain')->unique();
            $table->boolean('is_active')->default(true);
            $table->foreignId('client_id')->constrained('clients');
            $table->timestamps();
            // $table->foreign('client_id')->references('id')->on('clients');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('client_portals');
    }
};
