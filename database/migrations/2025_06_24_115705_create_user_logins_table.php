<?php

use App\Models\UserLogin;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_logins', function (Blueprint $table) {
            $table->id();
            $table->integer('order_index')->default(0);
            $table->unsignedBigInteger('user_id');

            $table->string('ip');
            $table->string('country');
            $table->string('city');
            $table->string('region');
            $table->decimal('lat', 10, 7);
            $table->decimal('lon', 10, 7);

            $table->string('device');
            $table->string('browser');
            $table->string('platform');
            $table->longText('device_id')->nullable();
            $table->text('user_agent')->nullable();

            $table->tinyInteger('status')->index()->default(UserLogin::STATUS_ACTIVE);
            $table->dateTime('last_login_at')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_logins');
    }
};
