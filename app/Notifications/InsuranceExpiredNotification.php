<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InsuranceExpiredNotification extends Notification
{
    use Queueable;
    protected $vehicle;

    public function __construct($vehicle)
    {
        $this->vehicle = $vehicle;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line("Upłynął termin ubezpieczenia pojazdu {$this->vehicle->brand} - {$this->vehicle->license_plate}.")
            ->action('Zobacz szczegóły', url('/vehicles/' . $this->vehicle->vehicle_id))
            ->line('Zadbaj o terminowy przegląd, aby uniknąć problemów.');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "Upłynął termin ubezpieczenia pojazdu {$this->vehicle->brand} - {$this->vehicle->license_plate}.",
            'vehicle_id' => $this->vehicle->vehicle_id,
        ];
    }
}
