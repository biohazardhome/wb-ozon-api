<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('post_financial_products', function (Blueprint $table) {
            $table->id();
            $table->integer('commission_amount');
            $table->integer('commission_percent');
            $table->integer('payout');
            $table->integer('product_id')->unique();
            $table->decimal('old_price', 8, 2);
            $table->decimal('price', 8, 2);
            $table->decimal('total_discount_value', 8, 2);
            $table->decimal('total_discount_percent', 8, 2);
            $table->json('actions');
            $table->string('picking')->nullable();
            $table->integer('quantity');
            $table->string('client_price');
            $table->string('currency_code');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_financial_products');
    }
};
