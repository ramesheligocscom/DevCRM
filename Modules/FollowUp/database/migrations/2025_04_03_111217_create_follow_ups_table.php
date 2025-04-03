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
        Schema::create('follow_ups', function (Blueprint $table) {
            // Primary key as UUID
            $table->uuid('id')->primary()->comment('Primary key identifier using UUID');

            // Call status tracking
            $table->enum('call_status', [
                'completed',
                'pending',
                'no_answer',
                'busy',
                'failed'
            ])->comment('Current status of the follow-up call');

            // Lead/prospect classification
            $table->string('lead_prospect', 32)->comment('Type of contact (lead/prospect)');

            // Call details
            $table->text('call_summary')->nullable()->comment('Detailed summary of the call');

            // Audit fields
            $table->string('created_by')->comment('User who created this follow-up record');
            $table->timestamp('created_at')->useCurrent()->comment('When this record was created');
            $table->timestamp('last_updated_at')->useCurrent()->comment('When this record was last updated');
            $table->string('last_updated_by')->nullable()->comment('User who last updated this record');

            // Relationship references
            $table->uuid('lead_id')->nullable()->comment('Reference to associated lead if applicable');
            $table->uuid('client_id')->nullable()->comment('Reference to associated client if applicable');

            // Soft delete flag
            $table->boolean('is_deleted')->default(false)->comment('Whether this record is marked as deleted');

            // Foreign key constraints
            $table->foreign('lead_id')
                ->references('id')
                ->on('leads')
                ->onDelete('set null')
                ->comment('Links to leads table');

            $table->foreign('client_id')
                ->references('id')
                ->on('clients')
                ->onDelete('set null')
                ->comment('Links to clients table');

            // Indexes for better performance
            $table->index('call_status');
            $table->index('lead_prospect');
            $table->index(['lead_id', 'client_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('follow_ups');
    }
};
