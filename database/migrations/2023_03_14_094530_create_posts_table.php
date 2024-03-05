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
        Schema::create('oz_posts', function (Blueprint $table) {
            $table->id();
            $table->string('posting_number')->unique();
            $table->unsignedInteger('cancellation_id')->nullable()->index();
            $table->unsignedBigInteger('delivery_method_id')->length(15)->nullable()->index();
            $table->unsignedInteger('requirement_id')->nullable()->index();
            $table->unsignedInteger('analytic_id')->nullable()->index();
            $table->unsignedInteger('financial_id')->nullable()->index();
            $table->unsignedBigInteger('order_id');
            $table->string('order_number');
            $table->string('status');
            $table->string('tracking_number');
            $table->string('tpl_integration_type');
            $table->dateTime('in_process_at');
            $table->dateTime('shipment_date');
            $table->dateTime('delivering_date')->nullable();
            $table->json('customer')->nullable();
            $table->json('addressee')->nullable();
            $table->json('barcodes')->nullable();
            $table->boolean('is_express');
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
        Schema::dropIfExists('posts');
    }
};
