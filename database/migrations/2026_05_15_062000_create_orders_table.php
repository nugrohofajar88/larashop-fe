<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_address_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shipping_service_id')->constrained()->cascadeOnDelete();
            $table->string('status', 30)->default('pending_payment')->index();
            $table->unsignedInteger('items_total')->default(0);
            $table->unsignedInteger('shipping_total')->default(0);
            $table->unsignedInteger('unique_code')->default(0);
            $table->unsignedInteger('grand_total')->default(0);
            $table->timestamp('ordered_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
