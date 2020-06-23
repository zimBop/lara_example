<?php

namespace App\Notifications;

use Edujugon\PushNotification\Channels\ApnChannel;
use Edujugon\PushNotification\Messages\PushMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TripOrderUpdated extends Notification
{
    use Queueable;

    public $tripOrderId;

    public function __construct(int $tripOrderId)
    {
        $this->tripOrderId = $tripOrderId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [ApnChannel::class];
    }

    public function toApn($notifiable)
    {
        return (new PushMessage())
            ->title('Trip request updated')
            ->category($this->tripOrderId)
            ->extra([
                'request_id' => $this->tripOrderId,
                'type' => 'request_updated',
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
