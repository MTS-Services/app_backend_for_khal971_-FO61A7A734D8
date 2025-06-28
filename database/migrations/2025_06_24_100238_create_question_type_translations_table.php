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
        Schema::create('question_type_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('question_type_id')->index();
            $table->string('language', 2)->index();
            $table->string('name')->index();
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->foreign('question_type_id')->references('id')->on('question_types')->cascadeOnDelete()->cascadeOnUpdate();
            $table->unique(['question_type_id', 'language']);
            $table->index(['language', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_type_translations');
    }
};
