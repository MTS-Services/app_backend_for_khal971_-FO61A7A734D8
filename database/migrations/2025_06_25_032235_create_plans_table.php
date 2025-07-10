<?php

use App\Http\Traits\AuditColumnsTrait;
use App\Models\Plan;
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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_index')->default(0)->index();
            $table->decimal('price', 15, 2);
            $table->integer('duration')->comment('in months');
            $table->string('stripe_price_id', 255)->nullable()->comment('Stripe Price ID');
            $table->string('apple_product_id', 255)->nullable()->comment('Apple App Store product ID');
            $table->string('google_product_id', 255)->nullable()->comment('Google Play product ID');

            $table->json('features')->nullable();

            $table->tinyInteger('status')->index()->default(Plan::STATUS_ACTIVE);
            $table->boolean('is_popular')->index()->default(false);


            $table->timestamps();
            $this->addAuditColumns($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};

