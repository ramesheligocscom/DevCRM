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
        Schema::create('product_services', function (Blueprint $table) {
             // Primary key
             $table->uuid('id')->primary()->comment('Primary key UUID');

             // Product/Service information
             $table->string('name')->comment('Name of the product or service');
             $table->decimal('price', 10, 2)->nullable()->comment('Price of the product or service');
             $table->json('attributes')->nullable()->comment('Additional attributes in JSON format');
             
             // Timestamps and user tracking
             $table->uuid('created_by')->nullable()->comment('User who created the record');
             $table->uuid('last_updated_by')->nullable()->comment('User who last updated the record');
             
             // Indexes
             $table->index('name');
             $table->index('price');
             
             $table->softDeletes();
             $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_services');
    }
};
