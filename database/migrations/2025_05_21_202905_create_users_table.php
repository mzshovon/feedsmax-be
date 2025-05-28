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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50);
            $table->string('email', 100);
            $table->unique('username');
            $table->unique('email');
            $table->string('password_hash', 255);
            $table->enum('role', 
            [
                'admin', 
                'manager', 
                'user', 
                'root', 
                'sales', 
                'marketing', 
                'accounts'
                ])->default('user');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
