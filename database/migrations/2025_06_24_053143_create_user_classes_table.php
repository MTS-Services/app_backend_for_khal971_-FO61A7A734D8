<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Http\Traits\AuditColumnsTrait;

return new class extends Migration {

    use AuditColumnsTrait;
    public function up(): void
    {
        Schema::create('user_classes', function (Blueprint $table) {
            $table->id();
            $table->integer('order_index')->default(0);
            $table->string('name')->unique();
            $table->string('status', 10)->index()->default('active')->comment('active, inactive');

            $table->timestamps();
            $this->addAuditColumns($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_classes');
    }
};
