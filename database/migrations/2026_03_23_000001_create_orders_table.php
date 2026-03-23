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
            $table->string('order_number')->unique();
            $table->string('bill_number')->nullable();
            $table->string('md5')->nullable();
            $table->string('status')->default('pending'); // pending | paid | cancelled
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->string('currency', 8)->default('USD');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
