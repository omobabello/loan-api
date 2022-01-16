<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WalletChanged extends Mailable
{
    // use Queueable, SerializesModels;

    protected $mailTo;

    public $amount;
    public $subjectMatter;
    public $username;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($to, $amount, $username)
    {
        $this->amount = $amount;
        $this->mailTo = $to;
        $this->subjectMatter = $amount > 0 ? "Wallet Credited" : "Wallet Debited";
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
            ->subject($this->subjectMatter)
            ->view('mails.wallet_changed');
    }
}
