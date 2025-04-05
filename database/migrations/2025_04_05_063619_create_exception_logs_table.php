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
        Schema::create('exception_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('status')->default('Pending')->comment('Pending,Complete,Cancel');
            $table->longText('type')->nullable();
            $table->longText('title')->nullable();
            $table->longText('error')->nullable();
            $table->longText('file_name')->nullable();
            $table->integer('line_number')->nullable();
            $table->longText('comment')->nullable();
            $table->longText('full_error')->nullable();
            $table->integer('type_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exception_logs');
    }
};
