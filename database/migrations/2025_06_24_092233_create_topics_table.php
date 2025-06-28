<?php

use App\Http\Traits\AuditColumnsTrait;
use App\Models\Topic;
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

        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_index')->default(0);
            $table->unsignedBigInteger('course_id')->nullable()->index();
            $table->tinyInteger('status')->index()->default(Topic::STATUS_ACTIVE);
            $table->boolean('is_premium')->default(true);
            $table->timestamps();

            $this->addAuditColumns($table);

            $table->foreign('course_id')->references('id')->on('courses')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topics');
    }
};
