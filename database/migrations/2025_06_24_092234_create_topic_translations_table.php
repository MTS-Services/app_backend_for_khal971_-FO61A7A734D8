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
        Schema::create('topic_translations', function (Blueprint $table) {
            $table->id();
            $table ->unsignedBigInteger('topic_id');
            $table->string('language', 2)->index();  // Language code (e.g., 'en', 'fr', 'es')
            $table->string('name');
            $table->timestamps();

            $table->foreign('topic_id')->references('id')->on('topics')->nullOnDelete()->cascadeOnUpdate();
            $table->unique(['topic_id', 'language']);
            $table->index(['language', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topic_translations');
    }
};
