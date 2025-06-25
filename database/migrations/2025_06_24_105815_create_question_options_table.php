<?php

use App\Http\Traits\AuditColumnsTrait;
use App\Models\QuestionOption;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    use AuditColumnsTrait;
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_index')->default(0);
            $table->unsignedBigInteger('question_id')->nullable()->index();
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->longText('explanation')->nullable();
            $table->tinyInteger('status')->index()->default(QuestionOption::STATUS_ACTIVE);
            $table->timestamps();

            $this->addAuditColumns($table);

            $table->foreign('question_id')->references('id')->on('questions')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_oprions');
    }
};
