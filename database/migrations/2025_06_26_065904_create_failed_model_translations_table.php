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
        Schema::create('failed_model_translations', function (Blueprint $table) {
            $table->id();
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->string('language', 2)->index();  // Language code (e.g., 'en', 'fr', 'es')
            $table->json('fields');
            $table->timestamp('failed_at');
            $table->timestamps();

            $table->unique(['model_type', 'model_id', 'language']);
            $table->index(['language', 'model_type', 'model_id']);
            $table->index(['language', 'model_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('failed_model_translations');
    }
};
