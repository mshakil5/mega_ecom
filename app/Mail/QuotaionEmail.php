<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QuotaionEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $downloadLink;
    public $suject;
    public $body;

    public function __construct($order, $downloadLink, $suject, $body)
    {
        $this->order = $order;
        $this->downloadLink = $downloadLink;
        $this->suject = $suject;
        $this->body = $body;
    }

    public function build()
    {
        return $this->markdown('emails.quotation-email')
                ->subject($this->suject)
                ->with([
                'body' => $this->body,
                'downloadLink' => $this->downloadLink,
                'order' => $this->order,
                ]);
        }
}
