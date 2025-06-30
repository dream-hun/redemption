<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\TransferInvitation as ModelsTransferInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

final class TransferInvitation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var ModelsTransferInvitation
     */
    public $invitation;

    public function __construct(ModelsTransferInvitation $invitation)
    {
        $this->invitation = $invitation;
    }

    public function build()
    {
        return $this->subject('Domain Transfer Invitation for '.$this->invitation->domain->name)
            ->markdown('emails.domain_transfer_invitation')
            ->with([
                'domainName' => $this->invitation->domain->name,
                'authCode' => $this->invitation->auth_code,
                'acceptUrl' => route('domains.transfer.accept', $this->invitation->token),
                'recipientEmail' => $this->invitation->recipient_email,
            ]);
    }
}
