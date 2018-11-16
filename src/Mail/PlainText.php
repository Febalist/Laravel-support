<?php

namespace Febalist\Laravel\Support\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class PlainText extends Mailable
{
    use Queueable;

    public $text;

    public function __construct($subject, $text)
    {
        $this->subject = $subject;
        $this->text = is_array($text) ? implode("\n", $text) : $text;
    }

    public function build()
    {
        return $this->view(['raw' => $this->text]);
    }
}
