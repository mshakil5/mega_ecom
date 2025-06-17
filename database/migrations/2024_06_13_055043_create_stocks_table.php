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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->decimal('quantity', 10, 2)->nullable();
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->tinyInteger('zip')->default(0);
            $table->double('purchase_price',10,2)->nullable();
            $table->double('ground_price_per_unit',10,2)->nullable();
            $table->double('profit_margin',10,2)->nullable();
            $table->double('selling_price',10,2)->nullable();
            $table->double('considerable_margin',10,2)->nullable();
            $table->double('considerable_price',10,2)->nullable();
            $table->string('exp_date')->nullable();
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
        Schema::dropIfExists('stocks');
    }
};
