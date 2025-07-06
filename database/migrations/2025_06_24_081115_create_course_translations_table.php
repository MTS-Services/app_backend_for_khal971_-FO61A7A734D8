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
        Schema::create('course_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->string('language', 2)->index();  // Language code (e.g., 'en', 'fr', 'es')
            $table->string('name')->index()->unique();
            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses')->nullOnDelete()->cascadeOnUpdate();
            $table->unique(['course_id', 'language']);
            $table->index(['language', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_translations');
    }
};
