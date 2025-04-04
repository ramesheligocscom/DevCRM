<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            // Primary key as UUID
            $table->uuid('id')->primary()->default(Str::uuid());

            // Lead information
            $table->string('name', 128)->comment('The name of the lead/company');
            $table->string('contact_person', 64)->comment('Primary contact person name');
            $table->string('contact_person_role', 64)->nullable()->comment('Role of the contact person');
            $table->string('email', 128)->nullable()->comment('Email address of the lead');
            $table->string('phone', 16)->nullable()->comment('Phone number of the lead');
            $table->text('address')->nullable()->comment('Physical address of the lead');

            // Lead tracking
            $table->string('status', 32)->default('new')->comment('Current status of the lead (new, contacted, qualified, etc.)');
            $table->string('source', 64)->nullable()->comment('How the lead was acquired');
            $table->string('assigned_user', 32)->nullable()->comment('User assigned to handle this lead');
            $table->text('note')->nullable()->comment('Additional notes about the lead');

            // Visit information
            $table->string('visit_assignee', 32)->nullable()->comment('User assigned for site visit');
            $table->timestampTz('visit_time')->nullable()->comment('Scheduled time for site visit');

            // Timestamps
            $table->string('created_by', 32)->nullable()->comment('User who created the lead');
            $table->string('last_updated_by', 32)->nullable()->comment('User who last updated the lead');

            // Relationship references (as UUIDs)
            $table->uuid('client_id')->nullable()->comment('Reference to clients table if converted');
            $table->uuid('quotation_id')->nullable()->comment('Reference to quotations table');
            $table->uuid('contract_id')->nullable()->comment('Reference to contracts table');
            $table->uuid('invoice_id')->nullable()->comment('Reference to invoices table');

            // Indexes
            $table->index('status');
            $table->index('assigned_user');
            $table->index('client_id');

            $table->softDeletes(); // Adds a `deleted_at` column
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()

    {

        Schema::table('leads', function (Blueprint $table) {
            $table->dropSoftDeletes(); // Drop `deleted_at` column
        });


        Schema::dropIfExists('leads');
    }
};
