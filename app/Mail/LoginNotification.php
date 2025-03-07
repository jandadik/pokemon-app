<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\LoginHistory;

class LoginNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Uživatel, kterému patří účet.
     *
     * @var \App\Models\User
     */
    public $user;

    /**
     * Záznam o přihlášení.
     *
     * @var \App\Models\LoginHistory
     */
    public $loginRecord;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, LoginHistory $loginRecord)
    {
        $this->user = $user;
        $this->loginRecord = $loginRecord;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Upozornění na nové přihlášení - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.login-notification',
            with: [
                'user' => $this->user,
                'loginRecord' => $this->loginRecord,
                'isSuspicious' => $this->loginRecord->is_suspicious,
                'url' => route('user.profile', ['tab' => 'security']),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
