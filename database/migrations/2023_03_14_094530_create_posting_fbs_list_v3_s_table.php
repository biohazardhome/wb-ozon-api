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
        Schema::create('posting_fbs_list_v3_s', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_bin';
            
            $table->id();
            $table->string('posting_number')->unique();
            $table->unsignedBigInteger('order_id');
            $table->string('order_number');
            $table->string('status');
            $table->json('delivery_method');
            $table->string('tracking_number');
            $table->string('tpl_integration_type');
            $table->dateTime('in_process_at');
            $table->dateTime('shipment_date');
            $table->dateTime('delivering_date')->nullable();
            $table->json('cancellation');
            $table->string('customer')->nullable();
            $table->json('products');
            $table->string('addressee')->nullable();
            $table->string('barcodes')->nullable();
            $table->dateTime('analytics_data')->nullable();
            $table->dateTime('financial_data')->nullable();
            $table->boolean('is_express');
            $table->json('requirements');
            $table->string('parent_posting_number');
            $table->json('available_actions');
            $table->integer('multi_box_qty');
            $table->boolean('is_multibox');
            $table->string('substatus');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posting_fbs_list_v3_s');
    }
};
