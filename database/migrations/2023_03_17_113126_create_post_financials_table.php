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
        Schema::create('post_financials', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('service_id')->nullable()->index();
            $table->string('cluster_from');
            $table->string('cluster_to');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_financials');
    }
};
