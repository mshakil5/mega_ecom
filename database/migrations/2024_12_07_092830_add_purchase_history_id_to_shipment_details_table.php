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
        Schema::table('shipment_details', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_history_id')->nullable()->after('supplier_id');
            $table->foreign('purchase_history_id')->references('id')->on('purchase_histories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipment_details', function (Blueprint $table) {
            //
        });
    }
};
