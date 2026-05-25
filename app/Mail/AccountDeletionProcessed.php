<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountDeletionProcessed extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $deletionRequest;
    public $action;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $deletionRequest, $action)
    {
        $this->user = $user;
        $this->deletionRequest = $deletionRequest;
        $this->action = $action;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = $this->action === 'approved' ? 
            'Account Deletion Request Approved' : 
            'Account Deletion Request Rejected';
            
        return $this->markdown('mail.account-deletion-processed')
            ->subject($subject)
            ->with([
                'user' => $this->user,
                'deletionRequest' => $this->deletionRequest,
                'action' => $this->action
            ]);
    }
}
