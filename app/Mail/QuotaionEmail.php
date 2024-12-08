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

    public function __construct($order, $downloadLink)
    {
        $this->order = $order;
        $this->downloadLink = $downloadLink;
    }

    public function build()
    {
        return $this->markdown('emails.quotation-email')
                    ->subject('Quotation');
    }
}
