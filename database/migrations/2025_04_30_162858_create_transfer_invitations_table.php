<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateTransferInvitationsTable extends Migration
{
    public function up()
    {
        Schema::create('transfer_invitations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('domain_id');
            $table->unsignedBigInteger('sender_id');
            $table->string('recipient_email');
            $table->string('auth_code');
            $table->string('token')->unique();
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            $table->unsignedBigInteger('accepted_by_id')->nullable();
            $table->timestamps();

            $table->foreign('domain_id')->references('id')->on('domains')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('accepted_by_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transfer_invitations');
    }
}
