<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subject_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subject_id');
            $table->string('language', 2)->index();  // Language code (e.g., 'en', 'fr', 'es')
            $table->string('name');
            $table->timestamps();

            $table->foreign('subject_id')->references('id')->on('subjects')->nullOnDelete()->cascadeOnUpdate();
            $table->unique(['subject_id', 'language']);
            $table->index(['language', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_translations');
    }
};
