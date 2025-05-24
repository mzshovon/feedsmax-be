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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('company_tag', 50);
            $table->string('company_name', 100);
            $table->string('contact_name', 100)->nullable();
            $table->string('email', 50)->unique();
            $table->string('phone', 13)->nullable();
            $table->text('address')->nullable();
            $table->string('client_key', 200)->comment("Client encryption key");
            $table->string('client_secret', 200)->comment("Client secret for encryption body");
            $table->boolean('status')->comment("Client status active/inactive per subscription");
            $table->softDeletes();
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
