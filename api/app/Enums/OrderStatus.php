<?php

namespace App\Enums;

enum OrderStatus: string
{
    case IN_ATTESA = 'in_attesa';
    case IN_LAVORAZIONE = 'in_lavorazione';
    case COMPLETATO = 'completato';
    case ANNULLATO = 'annullato';

    public function label(): string
    {
        return match($this) {
            self::IN_ATTESA => 'In Attesa',
            self::IN_LAVORAZIONE => 'In Lavorazione',
            self::COMPLETATO => 'Completato',
            self::ANNULLATO => 'Annullato',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::IN_ATTESA => 'gray',
            self::IN_LAVORAZIONE => 'blue',
            self::COMPLETATO => 'green',
            self::ANNULLATO => 'red',
        };
    }
}
