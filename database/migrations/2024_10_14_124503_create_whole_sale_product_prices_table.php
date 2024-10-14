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
        Schema::create('whole_sale_product_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('whole_sale_product_id')->nullable();
            $table->foreign('whole_sale_product_id')->references('id')->on('whole_sale_products')->onDelete('cascade');
            $table->decimal('price', 8, 2)->nullable();
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('whole_sale_product_prices');
    }
};
