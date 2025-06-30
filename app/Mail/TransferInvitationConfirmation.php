<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\TransferInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

final class TransferInvitationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var TransferInvitation
     */
    public $invitation;

    public function __construct(TransferInvitation $invitation)
    {
        $this->invitation = $invitation;
    }

    public function build()
    {
        return $this->subject('Transfer Invitation Sent for '.$this->invitation->domain->name)
            ->markdown('emails.transfer_invitation_confirmation')
            ->with([
                'domainName' => $this->invitation->domain->name,
                'authCode' => $this->invitation->auth_code,
                'recipientEmail' => $this->invitation->recipient_email,
            ]);
    }
}
