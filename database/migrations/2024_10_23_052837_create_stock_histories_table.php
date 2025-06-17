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
        Schema::create('stock_histories', function (Blueprint $table) {
            $table->id();
            $table->string('date')->nullable();
            $table->string('stockid')->nullable();
            $table->unsignedBigInteger('purchase_id')->nullable();
            $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade'); 
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade'); 
            $table->unsignedBigInteger('stock_id')->nullable();
            $table->foreign('stock_id')->references('id')->on('stocks')->onDelete('cascade'); 
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade'); 
            $table->string('quantity')->nullable();
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->tinyInteger('zip')->default(0);
            $table->string('selling_qty')->nullable();
            $table->string('available_qty')->nullable();
            $table->string('systemloss_qty')->nullable();
            $table->string('received_quantity')->nullable();
            $table->double('purchase_price',10,2)->nullable();
            $table->double('ground_price_per_unit',10,2)->nullable();
            $table->double('profit_margin',10,2)->nullable();
            $table->double('selling_price',10,2)->nullable();
            $table->double('considerable_margin',10,2)->nullable();
            $table->double('considerable_price',10,2)->nullable();
            $table->integer('sample_quantity')->default(0);
            $table->string('sl_start')->nullable();
            $table->string('sl_end')->nullable();
            $table->string('exp_date')->nullable();
            $table->string('transferred_product_quantity')->nullable();
            $table->string('missing_product_quantity')->nullable();
            $table->boolean('status')->default(1);
            $table->string('updated_by')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_histories');
    }
};
