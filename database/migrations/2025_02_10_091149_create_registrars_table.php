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
        Schema::create('registrars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('name');
            $table->string('organization')->nullable();
            $table->string('email');
            $table->string('phone');
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('postal_code')->default(250)->nullable();
            $table->foreignId('country_id')->constrained();
            $table->string('voice')->nullable();
            $table->string('fax')->nullable();
            $table->boolean('server_updated')->nullable()->default(0);
            $table->boolean('server_deleted')->nullable()->default(0);
            $table->boolean('server_created')->nullable()->default(0);
            $table->timestamp('server_created_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrars');
    }
};
