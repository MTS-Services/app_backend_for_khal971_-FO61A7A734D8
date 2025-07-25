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
        Schema::create('quiz_option_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quiz_option_id')->nullable()->index();
            $table->string('language' , 5)->index();
            $table->string('title', 200);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_option_translations');
    }
};
