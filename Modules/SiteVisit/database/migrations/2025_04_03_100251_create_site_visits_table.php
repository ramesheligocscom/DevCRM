<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('site_visits', function (Blueprint $table) {
            // Primary key as UUID
            $table->uuid('uuid')->primary()->comment('Primary key using UUID format');

            // When the site visit is scheduled to occur
            $table->timestamp('visit_time')->comment('Scheduled date and time of the site visit');

            // ID of the user assigned to this visit (foreign key to users table)
            $table->unsignedBigInteger('visit_assignee')->comment('User ID of the assigned staff member');

            // Automatic timestamp when record is created
            $table->timestamp('created_at')->useCurrent()->comment('When this record was created');

            // Who created this record (could be user email, username, or system identifier)
            $table->string('created_by')->comment('Identifier of who created this record');

            // Current status of the visit (e.g., 'scheduled', 'completed', 'canceled')
            $table->string('status')->comment('Current status of the site visit');

            // Optional notes about the visit
            $table->text('visit_notes')->nullable()->comment('Additional notes about the visit');

            // Optional reference to a lead (if this visit is related to a sales lead)
            $table->uuid('lead_id')->nullable()->comment('Associated lead ID if applicable');

            // Optional reference to a client (if this visit is for an existing client)
            $table->uuid('client_id')->nullable()->comment('Associated client ID if applicable');

            // Soft delete flag (preferred over actual deletion for audit purposes)
            $table->boolean('is_deleted')->default(false)->comment('Flag for soft deletion');

            // Foreign key constraints
            $table->foreign('visit_assignee')
                ->references('id')
                ->on('users')
                ->comment('Links to the users table for assigned staff');

            $table->foreign('lead_id')
                ->references('id')
                ->on('leads')
                ->comment('Links to the leads table if visit is lead-related');

            $table->foreign('client_id')
                ->references('id')
                ->on('clients')
                ->comment('Links to the clients table if visit is for existing client');
        });
    }

    public function down()
    {
        Schema::dropIfExists('site_visits');
    }
};
