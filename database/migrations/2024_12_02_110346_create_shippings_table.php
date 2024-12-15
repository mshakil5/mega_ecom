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
        Schema::create('shippings', function (Blueprint $table) {
            $table->id();
            $table->string('shipping_id')->nullable();
            $table->string('shipping_date')->nullable();
            $table->string('shipping_name')->nullable();
            $table->json('purchase_ids')->nullable();
            $table->json('purchase_history_ids')->nullable();
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
        Schema::dropIfExists('shippings');
    }
};
