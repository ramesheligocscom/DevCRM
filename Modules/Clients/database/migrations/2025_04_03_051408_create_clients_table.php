<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema, DB};

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            // Primary UUID with comment
            $table->uuid('id')->primary()->comment('Auto-generated UUID primary key');
            // Nullable foreign key with constraint
            $table->uuid('lead_id')
                ->nullable()
                ->comment('Reference to parent lead if exists');
            // Client information
            $table->string('name')->index();
            $table->string('contact_person');
            $table->string('contact_person_role')->nullable();
            // Contact information with unique constraint
            $table->string('email')->index()->nullable();
            $table->string('phone')->nullable();
            // Status fields
            $table->string('status')
                ->index()
                ->default('active')
                ->comment('Current client status');
            $table->uuid('assigned_user')->nullable()
                ->comment('User responsible for this client');
            // Audit tracking
            $table->uuid('created_by')
                ->comment('User who created the record');
            $table->uuid('last_updated_by')
                ->nullable()
                ->comment('User who last modified the record');
            $table->softDeletes(); // Adds a `deleted_at` column
            $table->timestamps();
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
    }
};
