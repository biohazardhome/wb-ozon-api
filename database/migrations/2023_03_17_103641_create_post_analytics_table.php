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
        Schema::create('oz_post_analytics', function (Blueprint $table) {
            $table->id();
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->string('delivery_type')->nullable();
            $table->boolean('is_premium')->nullable();
            $table->string('payment_type_group_name');
            $table->unsignedBigInteger('warehouse_id')->length(15);
            $table->string('warehouse');
            $table->unsignedInteger('tpl_provider_id');
            $table->string('tpl_provider');
            $table->dateTime('delivery_date_begin')->nullable();
            $table->dateTime('delivery_date_end')->nullable();
            $table->boolean('is_legal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_analytics');
    }
};
