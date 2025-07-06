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
        Schema::create('question_option_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('question_option_id')->index();
            $table->string('language', 5)->nullable()->index();
            $table->text('option_text');
            $table->longText('explanation')->nullable();
            $table->timestamps();

            $table->foreign('question_option_id')->references('id')->on('question_options')->cascadeOnDelete()->cascadeOnUpdate();
            // $table->unique(['question_option_id', 'language']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_option_translations');
    }
};
