<?php

use App\Http\Traits\AuditColumnsTrait;
use App\Models\QuestionType;
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
        Schema::create('question_types', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_index')->default(0);
            $table->string('name')->unique();
            $table->longText('description')->nullable();
            $table->tinyInteger('status')->index()->default(QuestionType::STATUS_ACTIVE);
            $table->boolean('is_premium')->default(true);
            $table->timestamps();

            $this->addAuditColumns($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_types');
    }
};
