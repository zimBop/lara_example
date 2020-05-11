<?php

namespace App\Notifications;

use App\Constants\TripStatuses;
use Edujugon\PushNotification\Channels\ApnChannel;
use Edujugon\PushNotification\Messages\PushMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TripOrderAccepted extends Notification
{
    use Queueable;

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
            ->title('Trip Accepted')
            ->extra([
                'status' => TripStatuses::DRIVER_IS_ON_THE_WAY,
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
