<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->uuid('reference')->unique();
            $table->string('paypal_order_id')->nullable()->unique();
            $table->string('paypal_capture_id')->nullable()->unique();
            $table->decimal('amount', 12, 2);
            $table->char('currency', 3)->default('USD');
            $table->string('status', 24)->default('pending')->index();
            $table->json('order_ids');
            $table->json('response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
