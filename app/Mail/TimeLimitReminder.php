<?php

namespace App\Mail;

use App\Models\DailySaleTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TimeLimitReminder extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
   public $tickets; // multiple tickets

    public function __construct($tickets)
    {
        // Accept Collection or single ticket
        $this->tickets = is_iterable($tickets) ? $tickets : collect([$tickets]);
    }

    public function build()
    {
        return $this->subject('Ticket Time Limit Reminder')
                    ->view('emails.time_limit_reminder')
                    ->with(['tickets' => $this->tickets]);
    }
}
