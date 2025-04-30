<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AuthCodeDelivery extends Mailable
{
    use Queueable, SerializesModels;

    private $domain, $authCode, $recipient_email, $owner;
    public function __construct($domain, $authCode, $recipient_email, $owner)
    {
        $this->domain = $domain;
        $this->authCode = $authCode;
        $this->recipient_email = $recipient_email;
        $this->owner = $owner;
    }
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Auth Code Delivery',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.auth_code_delivery', with:['domainName'=>$this->domain, 'recipientEmail'=>$this->recipient_email, 'authCode'=>$this->authCode, 'owner' => $this->owner]
        );
    }

    
    public function attachments(): array
    {
        return [];
    }
}
