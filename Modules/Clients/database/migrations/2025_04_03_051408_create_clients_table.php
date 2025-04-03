<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('lead_id')->nullable();
            $table->string('name');
            $table->string('contact_person');
            $table->string('contact_person_role');
            $table->string('email');
            $table->string('phone');
            $table->string('status');
            $table->string('assigned_user');
            $table->string('created_by');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('last_updated_at')->nullable();
            $table->string('last_updated_by')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
