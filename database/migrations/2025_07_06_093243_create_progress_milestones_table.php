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
        Schema::create('progress_milestones', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_index')->default(0)->index();
            $table->decimal('threshold_value', 10, 2);
            $table->integer('points_reward')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->string('badge_icon', 255)->nullable();

            $table->timestamps();
            $this->addAuditColumns($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_milestones');
    }
};
