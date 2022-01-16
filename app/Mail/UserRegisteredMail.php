<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UserRegisteredMail extends Mailable
{
    // use Queueable, SerializesModels;

    public $link;

    public $username;

    protected $mailTo;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($to, $username, $link)
    {
        $this->mailTo = $to;
        $this->link = $link;
        $this->username = $username;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from("support@54gene.com", "54Gene")
            ->to($this->mailTo)
            ->subject("Welcome to 54 gene")
            ->view('mails.user_registered');
    }
}
