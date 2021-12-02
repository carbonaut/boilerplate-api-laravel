<?php

namespace App\Jobs;

use App\Models\PushNotification;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class ProcessPushNotification implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $push;

    /**
     * Create a new job for sending a push notification.
     *
     * @param PushNotification $push
     */
    public function __construct(PushNotification $push)
    {
        $this->push = $push;
    }

    /**
     * Send the push notification.
     */
    public function handle()
    {
        try {
            $message = CloudMessage::withTarget('token', $this->push->device->push_token)
                ->withData([
                    'push_notification_id' => $this->push->id,
                    'type'                 => $this->push->type,
                ])
                ->withNotification(Notification::create($this->push->title, $this->push->body));

            $messaging = app('firebase.messaging');
            $messaging->send($message);

            $this->push->status = 'sent';
            $this->push->sent_at = now();
        } catch (Exception $e) {
            Log::error($e);
            $this->push->status = 'failed';
            $this->push->failed_at = now();
        }

        $this->push->save();
    }
}
