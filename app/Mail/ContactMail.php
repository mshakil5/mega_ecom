<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contactName;
    public $contactEmail;
    public $contactPhone;
    public $contactSubject;
    public $contactMessage;

    public function __construct(Contact $contact)
    {
        $this->contactName = $contact->name;
        $this->contactEmail = $contact->email;
        $this->contactPhone = $contact->phone;
        $this->contactSubject = $contact->subject ?? 'New Contact Query';
        $this->contactMessage = $contact->message;
    }

    public function build()
    {
        return $this->subject('New Contact Message: ' . $this->contactSubject)
                    ->view('emails.contact');
    }
}