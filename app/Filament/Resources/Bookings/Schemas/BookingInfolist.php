<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BookingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Clase agendada')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Estudiante'),

                        TextEntry::make('user.email')
                            ->label('Correo'),

                        TextEntry::make('course.title')
                            ->label('Curso')
                            ->placeholder('Clase general'),

                        TextEntry::make('status')
                            ->label('Estado')
                            ->badge(),

                        TextEntry::make('scheduled_at')
                            ->label('Fecha de la clase')
                            ->dateTime('d/m/Y H:i'),

                        TextEntry::make('created_at')
                            ->label('Agendada el')
                            ->dateTime('d/m/Y H:i'),

                        TextEntry::make('calendly_event_uri')
                            ->label('Evento de Calendly'),
                    ])
                    ->columns(2),
            ]);
    }
}
