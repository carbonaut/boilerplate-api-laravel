<?php

namespace App\Jobs;

use App\Models\Email;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessEmail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $email;

    /**
     * Create a new job for sending an email.
     *
     * @param Email $email
     */
    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    /**
     * Execute the job for sending an email.
     */
    public function handle()
    {
        try {
            // Set the email id into the mailable for the tracking
            $mailable = clone $this->email->mailable;
            $mailable->setEmailId($this->email->id);

            // Send the email
            Mail::to($this->email->to)->send($mailable);

            // Update the email with the sent informations
            $this->email->status = 'sent';
            $this->email->sent_at = Carbon::now();
            $this->email->mailable = null;
            $this->email->save();
        } catch (Exception $exception) {
            Log::error($exception, ['email_id', $this->email->id]);

            // Update the email with the fail informations
            $this->email->status = 'failed';
            $this->email->failed_at = Carbon::now();
            $this->email->save();
        }
    }
}
