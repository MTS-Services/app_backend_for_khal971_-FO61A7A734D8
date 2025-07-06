<?php

use App\Http\Traits\AuditColumnsTrait;
use App\Models\QuizOption;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use AuditColumnsTrait;
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quiz_options', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_index')->default(0);
            $table->unsignedBigInteger('quiz_id')->nullable()->index();
            $table->boolean('is_correct')->default(QuizOption::IS_CORRECT_FALSE);

            $table->timestamps();
            $this->addAuditColumns($table);

            $table->foreign('quiz_id')->references('id')->on('quizzes')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_options');
    }
};
