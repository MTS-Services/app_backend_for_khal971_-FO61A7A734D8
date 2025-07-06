<?php

use App\Http\Traits\AuditColumnsTrait;
use App\Models\Course;
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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_index')->default(0);
            $table->unsignedBigInteger('subject_id')->nullable()->index();
            $table->tinyInteger('status')->index()->default(Course::STATUS_ACTIVE);
            $table->timestamps();

            $this->addAuditColumns($table);

            $table->foreign('subject_id')->references('id')->on('subjects')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
