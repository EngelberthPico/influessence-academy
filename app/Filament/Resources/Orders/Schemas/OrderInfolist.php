<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Orden')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Cliente'),

                        TextEntry::make('user.email')
                            ->label('Email'),

                        TextEntry::make('status')
                            ->label('Estado')
                            ->badge(),

                        TextEntry::make('total_cents')
                            ->label('Total')
                            ->formatStateUsing(fn ($state) => '$'.number_format($state / 100, 2)),

                        TextEntry::make('paid_at')
                            ->label('Pagada el')
                            ->date(),

                        TextEntry::make('created_at')
                            ->label('Creada el')
                            ->date(),
                    ])
                    ->columns(2),

                Section::make('Cursos incluidos')
                    ->schema([
                        RepeatableEntry::make('orderItems')
                            ->label('')
                            ->schema([
                                TextEntry::make('course.title')
                                    ->label('Curso'),

                                TextEntry::make('price_cents')
                                    ->label('Precio pagado')
                                    ->formatStateUsing(fn ($state) => '$'.number_format($state / 100, 2)),
                            ])
                            ->columns(2),
                    ]),
            ]);
    }
}
