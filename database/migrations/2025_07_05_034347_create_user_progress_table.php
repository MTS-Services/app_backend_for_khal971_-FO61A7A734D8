<?php

use App\Http\Traits\AuditColumnsTrait;
use App\Models\UserProgress;
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
        Schema::create('user_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index()->nullable();
            $table->string('content_type', 50);
            $table->bigInteger('content_id');

            $table->integer('total_items')->default(0);
            $table->integer('completed_items')->default(0);
            $table->integer('correct_items')->default(0);

            $table->decimal('completion_percentage', 5, 2)->index()->default(0.00);
            $table->decimal('accuracy_percentage', 5, 2)->default(0.00);

            $table->integer('total_time_spent')->default(0);
            $table->integer('average_time_per_item')->default(0);

            $table->tinyInteger('status')->index()->default(UserProgress::STATUS_NOT_STARTED);

            $table->date('first_accessed_at')->nullable();
            $table->date('last_accessed_at')->index()->nullable();
            $table->date('completed_at')->nullable();

            $table->integer('current_streak')->default(0);
            $table->integer('best_streak')->default(0);
            $table->date('last_activity_date')->nullable();

            $table->timestamps();
            $this->addAuditColumns($table);
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete()->cascadeOnDelete();

            $table->index(['content_type', 'content_id']);
            $table->unique(['user_id', 'content_type', 'content_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_progress');
    }
};
