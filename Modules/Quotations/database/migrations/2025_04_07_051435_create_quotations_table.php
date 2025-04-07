<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            // Primary key as UUID
            $table->uuid('id')->primary()->comment('Primary key UUID');

            $table->string('quotation_number', 64)->unique()->comment('Unique quotation identifier');
            $table->date('valid_uptil')->comment('Date until quotation is valid');
            $table->string('quotation_type', 32)->comment('Type of quotation');
            $table->string('title', 255)->comment('Quotation title/description');
            $table->decimal('sub_total', 12, 2)->comment('Amount before discounts/taxes');
            $table->decimal('discount', 12, 2)->default(0)->comment('Discount amount');
            $table->decimal('tax', 12, 2)->default(0)->comment('Tax amount');
            $table->decimal('total', 12, 2)->comment('Final total amount');
            $table->string('status', 32)->comment('Current status of quotation');
            $table->json('items')->nullable()->comment('Quotation line items in JSON format');
            $table->text('custom_header_text')->nullable()->comment('Custom header text');
            $table->text('payment_terms')->nullable()->comment('Payment terms and conditions');
            $table->text('terms_conditions')->nullable()->comment('General terms and conditions');
            $table->uuid('lead_id')->nullable()->comment('Reference to lead if applicable');
            $table->uuid('client_id')->nullable()->comment('Reference to client if applicable');
            $table->uuid('contract_id')->nullable()->comment('Reference to contract if applicable');
            $table->uuid('created_by')->comment('User who created the quotation');
            $table->uuid('last_updated_by')->nullable()->comment('User who last updated');
            $table->timestamps();
            $table->softDeletes()->comment('Timestamp when quotation was deleted');

            // Indexes
            $table->index('quotation_number');
            $table->index('status');
            $table->index('lead_id');
            $table->index('client_id');
            $table->index('contract_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('quotations');
    }
};
