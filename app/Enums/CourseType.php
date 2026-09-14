<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CourseType: string implements HasColor, HasLabel
{
    case Recorded = 'recorded';
    case LiveProgram = 'live_program';
    case Hybrid = 'hybrid';

    public function getLabel(): string
    {
        return match ($this) {
            self::Recorded => 'Grabado',
            self::LiveProgram => 'Programa en vivo',
            self::Hybrid => 'Híbrido',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Recorded => 'info',
            self::LiveProgram => 'success',
            self::Hybrid => 'warning',
        };
    }
}
