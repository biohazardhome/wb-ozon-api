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
        Schema::create('oz_requirements', function (Blueprint $table) {
            $table->id();
            $table->json('products_requiring_gtd');
            $table->json('products_requiring_country');
            $table->json('products_requiring_mandatory_mark');
            $table->json('products_requiring_rnpt');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requirements');
    }
};
