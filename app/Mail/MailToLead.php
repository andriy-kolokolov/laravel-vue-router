<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MailToLead extends Mailable
{

    

    use Queueable, SerializesModels;

    public $lead;

    public function __construct($_lead)
    {
        $this->lead = $_lead;
    }

    
    public function envelope()
    {
        return new Envelope(
            replyTo: $this->lead->email,
            subject: 'Richiesta di informazioni ricevuta' . $this->lead->name,
        );
    }

    
    public function content()
    {
        return new Content(
            view: 'emails.mail-to-lead',
        );
    }

    
    public function attachments()
    {
        return [];
    }
}