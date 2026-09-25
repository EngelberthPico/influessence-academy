<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum BookingStatus: string implements HasColor, HasLabel
{
    case Scheduled = 'scheduled';
    case Completed = 'completed';
    case Canceled = 'canceled';

    public function getLabel(): string
    {
        return match ($this) {
            self::Scheduled => 'Programada',
            self::Completed => 'Completada',
            self::Canceled => 'Cancelada',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Scheduled => 'info',
            self::Completed => 'success',
            self::Canceled => 'danger',
        };
    }
}
