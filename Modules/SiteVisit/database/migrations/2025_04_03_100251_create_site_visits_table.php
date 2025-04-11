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
            $table->uuid('id')->primary()->comment('Primary key using UUID format');
            // When the site visit is scheduled to occur
            $table->timestamp('visit_time')->comment('Scheduled date and time of the site visit');

            // ID of the user assigned to this visit (foreign key to users table)
            $table->uuid('visit_assignee')->comment('User ID of the assigned staff member');
            // Who created this record (could be user email, username, or system identifier)
            $table->uuid('created_by')->comment('Identifier of who created this record');
            // Current status of the visit (e.g., 'scheduled', 'completed', 'canceled')
            $table->string('status')->comment('Current status of the site visit');
            // Optional notes about the visit
            $table->text('visit_notes')->nullable()->comment('Additional notes about the visit');
            // Optional reference to a lead (if this visit is related to a sales lead)
            $table->uuid('last_updated_by')->nullable()->comment('User who last updated this record');
            $table->uuid('lead_id')->nullable()->comment('Associated lead ID if applicable');
            // Optional reference to a client (if this visit is for an existing client)
            $table->uuid('client_id')->nullable()->comment('Associated client ID if applicable');
            $table->timestamps();
            $table->softDeletes()->comment('Timestamp when record was deleted');

            // Indexes for better performance
            $table->index(['lead_id', 'client_id' ,'visit_assignee']);
        });
    }

    public function down()
    {

        Schema::table('site_visits', function (Blueprint $table) {
            $table->dropSoftDeletes(); // Drop `deleted_at` column
        });



        Schema::dropIfExists('site_visits');
    }
};
