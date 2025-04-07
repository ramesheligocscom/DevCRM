<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            // Changed: Added comments to all columns
            $table->uuid('id')->primary()->default(Str::uuid())->comment('Primary key UUID');
            $table->json('items')->nullable()->comment('Contract line items in JSON format');
            $table->date('start_date')->comment('Effective start date of contract');
            $table->date('end_date')->comment('Expiration date of contract');
            $table->decimal('sub_total', 12, 2)->comment('Amount before taxes and discounts');
            $table->decimal('discount', 12, 2)->default(0)->comment('Discount amount applied');
            $table->decimal('tax', 12, 2)->default(0)->comment('Tax amount calculated');
            $table->string('status', 32)->comment('Current status of contract');
            
            // Changed: Removed foreign constraints, kept as nullable references
            $table->uuid('client_id')->nullable()->comment('Optional reference to client');
            $table->uuid('quotation_id')->nullable()->comment('Optional reference to quotation');
            $table->uuid('invoice_id')->nullable()->comment('Optional reference to invoice');
            
            $table->string('created_by', 32)->comment('User who created the record');
            $table->string('last_updated_by', 32)->nullable()->comment('User who last updated');
            
            // Changed: Removed is_deleted (using soft deletes)
            $table->timestamps();
            $table->softDeletes()->comment('Timestamp when record was deleted');

            // Changed: Removed foreign key constraints, kept indexes
            $table->index('status');
            $table->index('client_id');
            $table->index('quotation_id');
            $table->index('invoice_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('contracts');
    }
};
