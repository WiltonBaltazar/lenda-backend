<?php

namespace App\Mail;

use App\Models\Plan;
use App\Models\User;
use App\Models\Subscription; // <--- Import this
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeUser extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public Plan $plan;
    public Subscription $subscription; // <--- Add property

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Plan $plan, Subscription $subscription) // <--- Update constructor
    {
        $this->user = $user;
        $this->plan = $plan;
        $this->subscription = $subscription; // <--- Assign it
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bem-vindo à Lenda +!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome',
        );
    }
}