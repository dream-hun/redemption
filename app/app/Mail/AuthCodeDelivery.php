<?php

namespace App\Mail;

use App\Models\Domain;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AuthCodeDelivery extends Mailable
{
    use Queueable, SerializesModels;

    public $domain;
    public $authCode;
    public $recipientEmail;

    public function __construct(Domain $domain, string $authCode, string $recipientEmail)
    {
        $this->domain = $domain;
        $this->authCode = $authCode;
        $this->recipientEmail = $recipientEmail;
    }

    public function build()
    {
        return $this->subject('Domain Transfer Auth Code for ' . $this->domain->name)
            ->markdown('emails.auth_code_delivery')
            ->with([
                'domainName' => $this->domain->name,
                'authCode' => $this->authCode,
                'recipientEmail' => $this->recipientEmail,
            ]);
    }
}