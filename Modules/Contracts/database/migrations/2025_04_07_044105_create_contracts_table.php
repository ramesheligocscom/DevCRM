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
            // Primary key as UUID
            $table->uuid('id')->primary()->comment('Primary key UUID');
            
            $table->string('title', 128)->nullable()->comment('The title of the contract');
            $table->longText('description')->nullable()->comment('The description of the contract');
            $table->json('items')->nullable()->comment('Contract line items in JSON format');
            $table->date('start_date')->comment('Effective start date of contract');
            $table->date('end_date')->comment('Expiration date of contract');
            $table->decimal('sub_total', 12, 2)->comment('Amount before taxes and discounts');
            $table->decimal('discount', 12, 2)->default(0)->comment('Discount amount applied');
            $table->decimal('tax', 12, 2)->default(0)->comment('Tax amount calculated');
            $table->decimal('total', 12, 2)->comment('Final total amount');
            $table->string('status', 32)->comment('Current status of contract');
            
            // Changed: Removed foreign constraints, kept as nullable references
            $table->uuid('client_id')->nullable()->comment('Optional reference to client');
            $table->uuid('quotation_id')->nullable()->comment('Optional reference to quotation');
            $table->uuid('invoice_id')->nullable()->comment('Optional reference to invoice');
            
            $table->uuid('created_by')->comment('User who created the record');
            $table->uuid('last_updated_by')->nullable()->comment('User who last updated');
            
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
