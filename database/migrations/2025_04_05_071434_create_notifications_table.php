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
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title')->nullable();
            $table->string('module_type')->nullable();
            $table->text('message')->nullable();
            $table->uuid('user_id')->nullable();
            $table->string('module_id')->nullable();
            $table->json('show_ids')->nullable(); # Show notification User
            $table->json('event_ids')->nullable(); # andy module multiple ids
            $table->foreign('user_id')->references('uuid')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('notification_user', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('notification_id')->nullable();
            $table->boolean('is_read')->default(false);
            $table->uuid('user_id')->nullable();
            $table->foreign('notification_id')->references('id')->on('notifications')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('uuid')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('notification_user');
    }
};
