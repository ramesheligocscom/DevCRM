<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema, DB};

return new class extends Migration
{
    public function up(): void
    {
        // Enable UUID extension with error handling
        try {
            DB::statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp"');
        } catch (\Exception $e) {
            logger()->warning('UUID-OSSP extension not available: ' . $e->getMessage());
        }

        Schema::create('clients', function (Blueprint $table) {
            // Primary UUID with comment
            $table->uuid('id')
                ->primary()
                ->default(DB::raw('uuid_generate_v4()'))
                ->comment('Auto-generated UUID primary key');

            // Nullable foreign key with constraint
            $table->uuid('lead_id')
                ->nullable()
                ->comment('Reference to parent lead if exists');

            // Client information
            $table->string('name')->index();
            $table->string('contact_person');
            $table->string('contact_person_role');

            // Contact information with unique constraint
            $table->string('email')->index();
            $table->string('phone')->nullable();

            // Status fields
            $table->string('status')
                ->index()
                ->default('active')
                ->comment('Current client status');

            $table->string('assigned_user')
                ->comment('User responsible for this client');

            // Timestamps with precision
            $table->timestamp('created_at', 6)
                ->useCurrent()
                ->comment('Record creation timestamp');

            $table->timestamp('last_updated_at', 6)
                ->nullable()
                ->comment('Last modification timestamp');

            // Audit tracking
            $table->string('created_by')
                ->comment('User who created the record');

            $table->string('last_updated_by')
                ->nullable()
                ->comment('User who last modified the record');

            // Soft delete flag
            $table->boolean('is_deleted')
                ->default(false)
                ->index()
                ->comment('Soft delete flag');

            // Composite index for common queries
            $table->index(['status', 'is_deleted'], 'clients_status_deleted_index');

            // Foreign key with named constraint
            $table->foreign('lead_id', 'fk_clients_lead_id')
                ->references('id')
                ->on('leads')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });

        // Optional: Add table comment
        DB::statement("COMMENT ON TABLE clients IS 'Stores client information with UUID primary keys'");
    }

    public function down(): void
    {
        // Drop foreign key first to avoid errors
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign('fk_clients_lead_id');
        });

        Schema::dropIfExists('clients');

        // Only drop extension if you created it in this migration
        // DB::statement('DROP EXTENSION IF EXISTS "uuid-ossp"');
    }
};
