<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable as IlluminateMailable;
use Illuminate\Queue\SerializesModels;

class Mailable extends IlluminateMailable {
    use Queueable;
    use SerializesModels;

    /**
     * The email id.
     *
     * @var string
     */
    public $email_id;

    /**
     * Set the email id for the mailable.
     *
     * @param string $email_id
     */
    public function setEmailId(string $email_id) {
        $this->email_id = $email_id;
    }
}
