<?php

use App\Http\Traits\AuditColumnsTrait;
use App\Models\UserItemProgresss;
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
        Schema::create('user_item_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index()->nullable();
            $table->unsignedBigInteger('parent_progress_id')->index()->nullable();

            $table->string('item_type', 50)->index();
            $table->bigInteger('item_id')->index();
            $table->integer('item_order')->index()->default(0);

            $table->tinyInteger('status')->index()->default(UserItemProgresss::STATUS_NOT_STARTED);
            $table->integer('attempts')->default(0);
            $table->integer('correct_attempts')->default(0);

            $table->integer('time_spent')->default(0);
            $table->date('first_accessed_at')->nullable();
            $table->date('last_accessed_at')->nullable();
            $table->date('completed_at')->nullable();

            $table->decimal('score', 5, 2)->nullable();
            $table->boolean('is_bookmarked')->default(false);
            $table->boolean('is_flagged')->default(false);
            $table->text('notes')->nullable();

            $table->timestamps();
            $this->addAuditColumns($table);

            // Foreign Keys
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('parent_progress_id')->references('id')->on('user_item_progress')->nullOnDelete()->cascadeOnUpdate();;

            // Indexes
            // $table->unique(['user_id', 'item_type', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_item_progress');
    }
};
