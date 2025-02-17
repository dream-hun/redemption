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
        Schema::table('domains', function (Blueprint $table) {
            $table->foreignId('registrant_contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignId('admin_contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignId('tech_contact_id')->nullable()->constrained('contacts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->dropForeign(['registrant_contact_id']);
            $table->dropForeign(['admin_contact_id']);
            $table->dropForeign(['tech_contact_id']);
            $table->dropColumn(['registrant_contact_id', 'admin_contact_id', 'tech_contact_id']);
        });
    }
};
