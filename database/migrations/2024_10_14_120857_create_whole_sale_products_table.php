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
        Schema::create('whole_sale_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->string('feature_image')->nullable();
            $table->string('quantity')->nullable();
            $table->longText('short_description')->nullable();
            $table->longText('long_description')->nullable();
            $table->integer('watch')->default(0);
            $table->boolean('is_featured')->default(0);
            $table->boolean('is_new_arrival')->default(0);
            $table->boolean('is_top_rated')->default(0);
            $table->boolean('is_recent')->default(0);
            $table->boolean('is_popular')->default(0);
            $table->boolean('is_trending')->default(0);
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
        Schema::dropIfExists('whole_sale_products');
    }
};
