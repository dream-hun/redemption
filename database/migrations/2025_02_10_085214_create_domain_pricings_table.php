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
        Schema::create('domain_pricings', function (Blueprint $table) {
            $table->id();
            $table->string('tld');
            $table->integer('register_price');
            $table->integer('transfer_price');
            $table->integer('renew_price');
            $table->integer('grace');
            $table->integer('redemption_price');
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domain_pricings');
    }
};
