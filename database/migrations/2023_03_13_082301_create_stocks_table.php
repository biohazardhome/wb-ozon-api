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
        Schema::create('stocks', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_bin';
            
            $table->id();
            $table->dateTime('last_change_date');
            $table->string('supplier_article', 75);
            $table->string('tech_size', 30);
            $table->string('barcode', 30);
            $table->integer('quantity');
            $table->boolean('is_supply');
            $table->boolean('is_realization');
            $table->integer('quantity_full');
            $table->string('warehouse_name', 50);
            $table->unsignedInteger('nm_id');
            $table->string('subject', 50);
            $table->string('category', 50);
            $table->integer('days_on_site');
            $table->string('brand', 50);
            $table->string('sccode', 50);
            $table->decimal('price', 8, 2);
            $table->decimal('discount', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
