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
        Schema::create('host_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('host_id')->constrained('hostings');
            $table->foreignId('feature_id')->constrained('features');
            $table->integer('quantity')->default(1)->nullable();
            $table->string('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('host_features');
    }
};
