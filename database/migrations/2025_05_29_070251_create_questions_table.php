<?php

use App\Enums\FieldType;
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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('question_en', 1000);
            $table->string('question_another_lang', 1000);
            $table->enum('field_type', array_column(FieldType::cases(), 'value'));
            $table->json('options')->nullable();
            $table->string('score_range')->nullable();
            $table->integer('ref_id')->nullable();
            $table->integer('parent_id')->nullable();
            $table->integer('order');
            $table->boolean('status');
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
        Schema::dropIfExists('questions');
    }
};
