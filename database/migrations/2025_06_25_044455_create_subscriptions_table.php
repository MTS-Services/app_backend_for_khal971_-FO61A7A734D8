<?php

use App\Http\Traits\AuditColumnsTrait;
use App\Models\Subscription;
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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_index')->default(0);
            $table->unsignedBigInteger('user_id')->index()->nullable();
            $table->unsignedBigInteger('plan_id')->index()->nullable();

            $table->string('name', 255);
            $table->string('stripe_id', 255)->unique()->nullable()->index();
            $table->string('stripe_status', 50)->nullable();
            $table->string('stripe_price_id', 255)->nullable();
            $table->string('stripe_product_id', 255)->nullable();

            $table->string('apple_transaction_id', 255)->unique()->nullable();
            $table->string('google_order_id', 255)->unique()->nullable();

            $table->integer('quantity')->default(1);

            $table->dateTime('starts_at')->useCurrent();
            $table->dateTime('ends_at')->nullable();
            $table->dateTime('canceled_at')->nullable();

            $table->enum('payment_method', ['stripe', 'apple', 'google']);
            $table->enum('payment_frequency', ['monthly', 'yearly']);

            $table->string('status')->index()->default(Subscription::STATUS_ACTIVE);

            $table->timestamps();
            $this->addAuditColumns($table);

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('plan_id')->references('id')->on('plans')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
