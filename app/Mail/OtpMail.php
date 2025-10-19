<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otp;
    public function __construct(string $otp)
    {
        $this->otp = $otp;
    }

    public function build()
    {
        return $this->subject('Kode Verifikasi RuangKita')
                    ->view('emails.otp') // create view below
                    ->with(['otp' => $this->otp]);
    }
}