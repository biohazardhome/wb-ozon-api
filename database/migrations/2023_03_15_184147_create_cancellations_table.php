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
        Schema::create('cancellations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('cancel_reason_id')->unique();
            $table->string('cancel_reason');
            $table->string('cancellation_type');
            $table->boolean('cancelled_after_ship');
            $table->boolean('affect_cancellation_rating');
            $table->string('cancellation_initiator');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cancellations');
    }
};
