<?php

namespace App\Notifications;

use Edujugon\PushNotification\Channels\ApnChannel;
use Edujugon\PushNotification\Messages\PushMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TripStatusChanged extends Notification
{
    use Queueable;

    public $status;
    public $tripId;

    public function __construct(int $status, int $tripId)
    {
        $this->status = $status;
        $this->tripId = $tripId;
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
            ->title('Trip status changed')
            ->category($this->tripId)
            ->extra([
                'status' => $this->status,
                'type' => 'trip_status',
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
