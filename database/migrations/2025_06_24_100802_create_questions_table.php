<?php

use App\Http\Traits\AuditColumnsTrait;
use App\Models\Question;
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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_index')->default(0)->unique();
            $table->unsignedBigInteger('topic_id')->nullable()->index();
            $table->unsignedBigInteger('question_type_id')->nullable()->index();
            $table->string('title', 500);
            $table->text('description')->nullable();
            $table->string('file')->nullable();
            $table->integer('points')->default(1);
            $table->integer('time_limit')->nullable();
            $table->text('explanation')->nullable();
            $table->text('hints')->nullable();
            $table->text('tags')->nullable();
            $table->boolean('status')->index()->default(Question::STATUS_ACTIVE)->comment(Question::STATUS_ACTIVE . '= Active' . Question::STATUS_INACTIVE . '= Inactive');
            $table->timestamps();

            $this->addAuditColumns($table);

            $table->foreign('topic_id')->references('id')->on('topics')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('question_type_id')->references('id')->on('question_types')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
