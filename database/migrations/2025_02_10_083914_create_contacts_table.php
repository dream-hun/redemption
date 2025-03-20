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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('contact_id')->unique();
            $table->string('contact_type');
            $table->string('name');
            $table->string('organization')->nullable();
            $table->string('street1');
            $table->string('street2')->nullable();
            $table->string('city');
            $table->string('province');
            $table->string('postal_code');
            $table->string('country_code');
            $table->string('voice');
            $table->string('fax_number')->nullable();
            $table->string('fax_ext')->nullable();
            $table->string('email');
            $table->string('auth_info')->nullable();
            $table->string('epp_status')->default('active');
            $table->timestamps();
            // Index for faster lookups
            $table->index('contact_type');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
