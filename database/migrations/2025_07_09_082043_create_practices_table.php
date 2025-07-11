<?php

use App\Models\Practice;
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
        Schema::create('practices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('practiceable_id')->index();
            $table->string('practiceable_type')->index();
            $table->integer('total_attempts')->default(0)->index();
            $table->integer('correct_attempts')->default(0)->index();
            $table->integer('wrong_attempts')->default(0)->index();
            $table->integer('progress')->default(0)->index();
            $table->tinyInteger('status')->index()->default(Practice::STATUS_NOT_STARTED);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->unique(['user_id', 'practiceable_id', 'practiceable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('practices');
    }
};
