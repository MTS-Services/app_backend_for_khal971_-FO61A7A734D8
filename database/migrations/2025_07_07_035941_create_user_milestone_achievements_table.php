<?php

use App\Http\Traits\AuditColumnsTrait;
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
        Schema::create('user_milestone_achievements', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_index')->default(0);
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('milestone_id')->nullable()->index();
            $table->unsignedBigInteger('progress_id')->nullable()->index();
            $table->decimal('achieved_value', 10, 2); 
            $table->date('achieved_at')->useCurrent();
            $table->boolean('is_notified')->default(false);
            $table->date('notified_sended_at')->useCurrent();

            $table->timestamps();
            $this->addAuditColumns($table);

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('milestone_id')->references('id')->on('progress_milestones')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('progress_id')->references('id')->on('user_progress')->nullOnDelete()->cascadeOnUpdate();});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_milestone_achievements');
    }
};
