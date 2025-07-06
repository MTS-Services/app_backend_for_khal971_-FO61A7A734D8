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
        Schema::create('question_answer_translations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_index')->default(0);
            $table->unsignedBigInteger('question_answer_id');
            $table->string('language', 5)->index();
            $table->longText('answer');
            $table->integer('match_percentage');
            $table->timestamps();

            $table->foreign('question_answer_id')->references('id')->on('question_answers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->unique(['question_answer_id', 'language']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_answer_translations');
    }
};
