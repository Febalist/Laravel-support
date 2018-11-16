<?php

namespace Febalist\Laravel\Support\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class MarkdownEmail extends Mailable
{
    use Queueable;

    public function __construct($subject, $markdown)
    {
        $this->subject = $subject;
        $this->html = markdown($markdown, true)->toHtml();
    }

    public function build()
    {
        return $this;
    }
}
