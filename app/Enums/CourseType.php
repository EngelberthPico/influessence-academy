<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CourseType: string implements HasColor, HasLabel
{
    case Recorded = 'recorded';
    case LiveProgram = 'live_program';
    case Hybrid = 'hybrid';
    case Guide = 'guide';

    public function getLabel(): string
    {
        return match ($this) {
            self::Recorded => 'Grabado',
            self::LiveProgram => 'Programa en vivo',
            self::Hybrid => 'Híbrido',
            self::Guide => 'Guía',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Recorded => 'info',
            self::LiveProgram => 'success',
            self::Hybrid => 'warning',
            self::Guide => 'danger',
        };
    }
}
