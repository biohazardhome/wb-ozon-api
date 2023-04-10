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
        Schema::create('wb_report_detail_by_period', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('realizationreport_id');
            $table->dateTime('date_from');
            $table->dateTime('date_to');
            $table->dateTime('create_dt');
            $table->string('suppliercontract_code')->nullable();
            $table->unsignedBigInteger('rrd_id');
            $table->unsignedInteger('gi_id');
            $table->string('subject_name')->nullable();
            $table->unsignedInteger('nm_id')->nullable();
            $table->string('brand_name')->nullable();
            $table->string('sa_name')->nullable();
            $table->string('ts_name')->nullable();
            $table->string('barcode');
            $table->string('doc_type_name');
            $table->decimal('retail_price', 8, 2);
            $table->decimal('retail_amount', 8, 2);
            $table->integer('sale_percent');
            $table->decimal('commission_percent', 8, 2);
            $table->string('office_name')->nullable();
            $table->dateTime('order_dt');
            $table->dateTime('sale_dt');
            $table->dateTime('rr_dt');
            $table->unsignedBigInteger('shk_id');
            $table->decimal('retail_price_withdisc_rub', 8, 2);
            $table->integer('delivery_amount');
            $table->integer('return_amount');
            $table->decimal('delivery_rub', 8, 2);
            $table->string('gi_box_type_name');
            $table->decimal('product_discount_for_report', 8, 2);
            $table->decimal('supplier_promo', 8, 2);
            $table->string('rid');
            $table->decimal('ppvz_spp_prc', 8, 2);
            $table->decimal('ppvz_kvw_prc_base', 8, 2);
            $table->decimal('ppvz_kvw_prc', 8, 2);
            $table->decimal('ppvz_sales_commission', 8, 2);
            $table->decimal('ppvz_for_pay', 8, 2);
            $table->decimal('ppvz_reward', 8, 2);
            $table->decimal('acquiring_fee', 8, 2);
            $table->string('acquiring_bank');
            $table->decimal('ppvz_vw', 8, 2);
            $table->decimal('ppvz_vw_nds', 8, 2);
            $table->integer('ppvz_office_id');
            $table->string('ppvz_office_name')->nullable();
            $table->integer('ppvz_supplier_id');
            $table->string('ppvz_supplier_name');
            $table->string('ppvz_inn');
            $table->string('declaration_number');
            $table->string('bonus_type_name')->nullable();
            $table->string('sticker_id');
            $table->string('site_country');
            $table->decimal('penalty', 8, 2);
            $table->decimal('additional_payment', 8, 2);
            $table->string('kiz')->nullable();
            $table->string('srid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wb_report_detail_by_period');
    }
};
