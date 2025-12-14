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
            $table->foreignId('order_details_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('customization_type', ['text', 'image']);
            $table->string('method')->nullable();
            $table->string('position')->nullable();
            $table->text('text_content')->nullable();
            $table->string('font_family')->nullable();
            $table->string('font_size')->nullable();
            $table->longText('image_url')->nullable();
            $table->integer('z_index')->nullable();
            $table->string('layer_id')->nullable();
            $table->json('data')->nullable();
            $table->timestamps();
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
