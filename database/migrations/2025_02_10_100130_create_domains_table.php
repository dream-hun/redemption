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
        Schema::create('domains', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('name')->unique();
            $table->string('auth_code')->nullable();
            $table->string('registrar');
            $table->foreignId('registrant_contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignId('admin_contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignId('tech_contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->enum('status', ['active', 'pending', 'expired', 'suspended']);
            $table->timestamp('registered_at');
            $table->timestamp('expires_at');
            $table->boolean('auto_renew')->default(false);
            $table->string('dns_provider')->nullable();
            $table->json('nameservers')->nullable();
            $table->foreignId('owner_id')->constrained('users');
            $table->string('ssl_status')->nullable();
            $table->timestamp('ssl_expires_at')->nullable();
            $table->boolean('whois_privacy')->default(false);
            $table->integer('registration_period')->default(1);
            $table->timestamp('last_renewal_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
};
