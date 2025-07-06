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
        Schema::create('progress_milestone_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('progress_milestone_id');
            $table->string('language', 5)->index();
            $table->string('content_type', 50)->index();
            $table->string('milestone_type', 50)->index();
            $table->text('requirement_description');
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->text('celebration_message')->nullable();
            $table->string('badge_name', 100)->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_milestone_translations');
    }
};
