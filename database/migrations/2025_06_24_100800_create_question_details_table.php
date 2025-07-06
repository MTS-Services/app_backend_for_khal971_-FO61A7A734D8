<?php

use App\Http\Traits\AuditColumnsTrait;
use App\Models\QuestionDetails;
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
        Schema::create('question_details', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('order_index')->default(0);
                $table->unsignedBigInteger('topic_id')->nullable()->index();
                $table->string('file')->nullable();
                $table->tinyInteger('status')->index()->default(QuestionDetails::STATUS_ACTIVE);
                $table->timestamps();

                $this->addAuditColumns($table);

                $table->foreign('topic_id')->references('id')->on('topics')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_details');
    }
};
