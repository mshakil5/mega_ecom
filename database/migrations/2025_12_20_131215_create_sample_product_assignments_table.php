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
        Schema::create('sample_product_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sample_product_id');
            $table->unsignedBigInteger('wholesaler_id');
            $table->integer('quantity');
            $table->date('assignment_date');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('sample_product_id')
                ->references('id')
                ->on('sample_products')
                ->onDelete('cascade');
            
            $table->foreign('wholesaler_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            // Indexes
            $table->index('sample_product_id');
            $table->index('wholesaler_id');
            $table->index('assignment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sample_product_assignments');
    }
};
