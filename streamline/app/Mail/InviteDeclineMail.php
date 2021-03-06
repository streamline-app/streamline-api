<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InviteDeclineMail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $team;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($team, $email)
    {
        $this -> team = $team;
        $this -> email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('Email.declineInvite')->with(['email' => $this->email, 'team' => $this->team]);
    }
}
