<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountDeletionRequestConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $deletionRequest;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $deletionRequest)
    {
        $this->user = $user;
        $this->deletionRequest = $deletionRequest;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.account-deletion-request-confirmation')
            ->subject('Account Deletion Request Received')
            ->with([
                'user' => $this->user,
                'deletionRequest' => $this->deletionRequest
            ]);
    }
}
