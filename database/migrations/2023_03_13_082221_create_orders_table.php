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
        Schema::create('wb_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('odid')->unique();
            $table->string('g_number', 50);
            $table->dateTime('date');
            $table->dateTime('last_change_date')->index();
            $table->string('supplier_article', 75);
            $table->string('tech_size', 30);
            $table->string('barcode', 30);
            $table->decimal('total_price', 8, 2);
            $table->integer('discount_percent');
            $table->string('warehouse_name', 50);
            $table->string('oblast', 200);
            $table->unsignedInteger('income_id')->index();
            $table->unsignedInteger('nm_id')->index();
            $table->string('subject', 50);
            $table->string('category', 50);
            $table->string('brand', 50);
            $table->boolean('is_cancel');
            $table->dateTime('cancel_dt');
            $table->string('sticker');
            $table->string('srid');
            $table->string('order_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wb_orders');
    }
};
