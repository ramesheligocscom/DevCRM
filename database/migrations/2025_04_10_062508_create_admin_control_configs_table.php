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
        // $table->enum('status_for', ['Lead', 'Client', 'Site Visit', 'Quotation', 'Contract', 'Invoice', 'Scheduling'])->nullable(); // Adjust enum values as needed
        Schema::create('admin_control_configs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('invoice_footer_text')->nullable();
            $table->text('contract_footer_text')->nullable();
            $table->string('status_for')->nullable();
            $table->string('status_text')->nullable();
            $table->string('status_color', 7)->nullable(); // Expecting hex code like #FF0000
            $table->integer('position')->default(1); // this column to work to way one way to status active inactive and scend position wise filter 0 or -1 active other wise active and 09 t greater to sort order
            $table->boolean('is_predefined')->default(1)->comment('0-> Not (edit,Delete), 1-> (Delete,edit) Item');
            $table->timestamps();
        });
    }

    /**
     * 
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_control_configs');
    }
};
