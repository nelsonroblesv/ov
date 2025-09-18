<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VisitaRealizadaUsuario extends Notification
{
    use Queueable;

    public function __construct(public $visita) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $cobro = $this->visita->cobros;

        return [
            'title' => 'Visita registrada con éxito',
            'message' => 'Se registró tu visita al cliente ' . $this->visita->customer->name .
                         ($cobro ? " e incluyó un cobro de $" . number_format($cobro->monto, 2) : ''),
            'visita_id' => $this->visita->id,
        ];
    }
}
