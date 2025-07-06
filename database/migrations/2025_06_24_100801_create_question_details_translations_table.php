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
        Schema::create('question_details_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('question_detail_id');
            $table->string('language', 5)->index();
            $table->string('title', 500);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('question_detail_id')->references('id')->on('question_details')->cascadeOnDelete()->cascadeOnUpdate();
            $table->unique(['question_detail_id', 'language']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_details_translations');
    }
};
