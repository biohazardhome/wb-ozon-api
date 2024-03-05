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
        Schema::create('oz_products', function (Blueprint $table) {
            $table->id();
            $table->decimal('price', 8, 2);
            $table->string('offer_id');
            $table->string('name');
            $table->unsignedInteger('sku')->unique();
            $table->unsignedInteger('quantity');
            $table->json('mandatory_mark');
            $table->string('currency_code');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
