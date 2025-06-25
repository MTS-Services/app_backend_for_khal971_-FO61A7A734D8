<?php

use App\Http\Traits\AuditColumnsTrait;
use App\Models\PlanFeature;
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
        Schema::create('plan_features', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_index')->default(0);
            $table->unsignedBigInteger('plan_id')->nullable()->index();
            $table->string('name');
            $table->string('icon')->nullable();
            $table->string('description')->nullable();
            $table->tinyInteger('status')->index()->default(PlanFeature::STATUS_ACTIVE);
            $table->timestamps();

            $this->addAuditColumns($table);

            $table->foreign('plan_id')->references('id')->on('plans')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_features');
    }
};
