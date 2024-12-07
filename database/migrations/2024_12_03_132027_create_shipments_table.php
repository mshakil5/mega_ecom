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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shipping_id')->nullable();
            $table->foreign('shipping_id')->references('id')->on('shippings')->onDelete('cascade');
            $table->json('purchase_ids')->nullable();
            $table->integer('total_product_quantity')->nullable();
            $table->integer('total_missing_quantity')->nullable();
            $table->decimal('total_purchase_cost',10,2)->nullable();
            $table->decimal('cnf_cost',10,2)->nullable();
            $table->decimal('import_duties_tax',10,2)->nullable();
            $table->decimal('warehouse_and_handling_cost',10,2)->nullable();
            $table->decimal('other_cost',10,2)->nullable();
            $table->decimal('total_additional_cost',10,2)->nullable();
            $table->boolean('status')->default(1);
            // 1 == Processing, 2 == On The Way, 3 == Received
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
