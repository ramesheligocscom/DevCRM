<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(Str::uuid())->comment('Primary key UUID');
            $table->json('items')->nullable()->comment('Invoice line items in JSON format');
            $table->decimal('sub_total', 12, 2)->comment('Amount before taxes and discounts');
            $table->decimal('tax', 12, 2)->default(0)->comment('Tax amount');
            $table->decimal('discount', 12, 2)->default(0)->comment('Discount amount');
            $table->decimal('total', 12, 2)->comment('Final total amount');
            $table->string('status', 32)->comment('Current status of invoice');
            $table->uuid('client_id')->nullable()->comment('Reference to client');
            $table->uuid('contract_id')->nullable()->comment('Reference to contract');
            $table->string('created_by', 32)->comment('User who created the invoice');
            $table->string('last_updated_by', 32)->nullable()->comment('User who last updated');
            $table->timestamps();
            $table->softDeletes()->comment('Timestamp when invoice was deleted');

            // Indexes
            $table->index('status');
            $table->index('client_id');
            $table->index('contract_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};
