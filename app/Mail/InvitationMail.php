<?php

namespace App\Mail;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The invitation instance.
     *
     * @var \App\Models\Invitation
     */
    public $invitation;

    /**
     * Create a new message instance.
     */
    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * Get the message envelope.
     */
    public function getEnvelope(): Envelope
    {
        return new Envelope(
            subject: 'Invitation a rejoindre une colocation ' . $this->invitation->colocation->nom,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $userExists = User::where('email', $this->invitation->email)->exists();

        if ($userExists) {
            $url = route('invitations.show', ['token' => $this->invitation->token]);
        } else {
            $url = route('register', ['invite' => $this->invitation->token]);
        }

        return new Content(
            markdown: 'emails.invitation',
            with : [
                'url' => $url,
            ]
        );
    }
}
