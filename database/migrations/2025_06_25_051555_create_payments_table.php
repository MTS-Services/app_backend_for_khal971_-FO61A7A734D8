<?php

use App\Http\Traits\AuditColumnsTrait;
use App\Models\Payment;
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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_index')->index()->default(0);

            $table->unsignedBigInteger('user_id')->index()->nullable();
            $table->unsignedBigInteger('subscription_id')->nullable();

            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('payment_method', ['stripe', 'apple', 'google']);
            $table->string('payment_id', 255)->nullable()->index();
            $table->tinyInteger('status')->index()->default(Payment::STATUS_PENDING);
            $table->string('receipt_url', 512)->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            $this->addAuditColumns($table);

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('subscription_id')->references('id')->on('subscriptions')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
