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
        Schema::create('post_financial_product_pivots', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('financial_id');
            $table->unsignedInteger('product_id');

            $table->unique(['financial_id', 'product_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_financial_product_pivots');
    }
};
