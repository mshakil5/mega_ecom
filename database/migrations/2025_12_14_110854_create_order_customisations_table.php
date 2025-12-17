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
        Schema::create('order_customisations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_details_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('size_id')->nullable();
            $table->unsignedBigInteger('color_id')->nullable();
            
            // Customization details
            $table->string('customization_type')->default('text'); // text, image
            $table->string('method')->nullable(); // embroidery, printing, etc.
            $table->string('position')->nullable(); // left_chest, back, etc.
            $table->integer('z_index')->nullable();
            $table->string('layer_id')->nullable(); // For identifying layers
            
            // Customization data (stored as JSON)
            $table->json('data')->nullable(); // Contains all customization data
            
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('order_details_id')->references('id')->on('order_details')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            $table->foreign('size_id')->references('id')->on('sizes')->onDelete('set null');
            $table->foreign('color_id')->references('id')->on('colors')->onDelete('set null');
            
            // Indexes
            $table->index('order_details_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_customisations');
    }
};
