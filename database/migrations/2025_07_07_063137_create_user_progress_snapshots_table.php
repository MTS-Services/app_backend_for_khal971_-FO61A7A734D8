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
        Schema::create('user_progress_snapshots', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_index')->default(0);
            $table->unsignedBigInteger('user_id')->index();

            $table->date('snapshot_date')->useCurrent();
            $table->string('snapshot_type', 50)->default('daily');

            $table->integer('total_subjects')->default(0);
            $table->integer('active_subjects')->default(0);
            $table->integer('completed_subjects')->default(0);

            $table->integer('total_courses')->default(0);
            $table->integer('active_courses')->default(0);
            $table->integer('completed_courses')->default(0);

            $table->integer('total_topics')->default(0);
            $table->integer('completed_topics')->default(0);

            $table->integer('total_questions_attempted')->default(0);
            $table->integer('total_questions_correct')->default(0);

            $table->decimal('overall_completion_percentage', 5, 2)->default(0.00);
            $table->decimal('overall_accuracy_percentage', 5, 2)->default(0.00);
            $table->integer('total_time_spent')->default(0);

            $table->integer('current_streak')->default(0);
            $table->integer('longest_streak')->default(0);

            $table->timestamp('created_at')->useCurrent();

            $table->unique(['user_id', 'snapshot_date', 'snapshot_type'], 'progress_snapshots_unique_idx');

            $table->index('snapshot_date', 'progress_snapshots_date_idx');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_progress_snapshots');
    }
};
