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
        Schema::create('permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('action');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->uuid('permission_type_id');
            $table->uuid('permission_category_id')->nullable();
            $table->longText('permission')->comment('this column in save value action_slug');
            # foreign
            $table->foreign('permission_type_id')->references('id')->on('permission_types')->onDelete('cascade');
            $table->foreign('permission_category_id')->references('id')->on('permission_categories')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
