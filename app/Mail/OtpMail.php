<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otp;
    public string $type;

    public function __construct(string $otp, string $type)
    {
        $this->otp = $otp;
        $this->type = $type;
    }

    public function build(): self
    {
        return $this
            ->subject('Your RecipeHub verification code')
            ->view('emails.otp');
    }
}

