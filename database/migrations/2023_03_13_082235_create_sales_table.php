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
        Schema::create('sales', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_bin';

            $table->id();
            $table->string('g_number', 50);
            $table->dateTime('date');
            $table->dateTime('last_change_date');
            $table->string('supplier_article', 75);
            $table->string('tech_size', 30);
            $table->string('barcode', 30);
            $table->decimal('total_price', 8, 2);
            $table->integer('discount_percent');
            $table->boolean('is_supply');
            $table->boolean('is_realization');
            $table->decimal('promo_code_discount', 8, 2);
            $table->string('warehouse_name', 50);
            $table->string('country_name', 200);
            $table->string('oblast_okrug_name', 200);
            $table->string('region_name', 200);
            $table->unsignedInteger('income_id');
            $table->string('sale_id', 15);
            $table->unsignedBigInteger('odid');
            $table->decimal('spp', 8, 2);
            $table->decimal('for_pay', 8, 2);
            $table->decimal('finished_price', 8, 2);
            $table->decimal('price_with_disc', 8, 2);
            $table->unsignedInteger('nm_id');
            $table->string('subject', 50);
            $table->string('category', 50);
            $table->string('brand', 50);
            $table->integer('is_storno');
            $table->string('sticker');
            $table->string('srid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
