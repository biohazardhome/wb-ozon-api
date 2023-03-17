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
        Schema::create('post_financial_services', function (Blueprint $table) {
            $table->id();
            $table->integer('marketplace_service_item_fulfillment');
            $table->integer('marketplace_service_item_pickup');
            $table->integer('marketplace_service_item_dropoff_pvz');
            $table->integer('marketplace_service_item_dropoff_sc');
            $table->integer('marketplace_service_item_dropoff_ff');
            $table->integer('marketplace_service_item_direct_flow_trans');
            $table->integer('marketplace_service_item_return_flow_trans');
            $table->integer('marketplace_service_item_deliv_to_customer');
            $table->integer('marketplace_service_item_return_not_deliv_to_customer');
            $table->integer('marketplace_service_item_return_part_goods_customer');
            $table->integer('marketplace_service_item_return_after_deliv_to_customer');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_financial_services');
    }
};
