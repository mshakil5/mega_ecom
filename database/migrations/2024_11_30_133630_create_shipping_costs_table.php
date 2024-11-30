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
        Schema::create('shipping_costs', function (Blueprint $table) {
            $table->id();
            $table->json('purchase_ids');
            $table->decimal('direct_cost',10,2)->nullable();
            $table->decimal('cnf_cost',10,2)->nullable();
            $table->decimal('cost_a',10,2)->nullable();
            $table->decimal('cost_b',10,2)->nullable();
            $table->decimal('other_cost',10,2)->nullable();
            $table->decimal('additional_cost',10,2)->nullable();
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
        Schema::dropIfExists('shipping_costs');
    }
};
