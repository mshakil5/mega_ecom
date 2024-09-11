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
        Schema::table('system_loses', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->nullable()->after('product_id');
            $table->foreign('order_id')->references('id')->on('orders')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_loses', function (Blueprint $table) {
            //
        });
    }
};
